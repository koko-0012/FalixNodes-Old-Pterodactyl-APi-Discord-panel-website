<?php
include("../config.php");
include("../global.php");

$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
  $keyval = explode ('=', $keyval);
  if (count($keyval) == 2)
     $myPost[$keyval[0]] = urldecode($keyval[1]);
}
$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
   $get_magic_quotes_exists = true;
} 
foreach ($myPost as $key => $value) {        
   if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) { 
        $value = urlencode(stripslashes($value)); 
   } else {
        $value = urlencode($value);
   }
   $req .= "&$key=$value";
}

$ch = curl_init('https://ipnpb.paypal.com/cgi-bin/webscr');
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

if( !($res = curl_exec($ch)) ) {
    curl_close($ch);
    exit;
}
curl_close($ch);

if (strcmp ($res, "VERIFIED") == 0) {

    // check whether the payment_status is Completed
    // check that txn_id has not been previously processed
    // check that receiver_email is your Primary PayPal email
    // check that payment_amount/payment_currency are correct
    // process payment

    // assign posted variables to local variables
    $item_name = $_POST['item_name'];
    $item_number = $_POST['item_number'];
    $payment_status = $_POST['payment_status'];
    $payment_amount = $_POST['mc_gross'];
    $payment_currency = $_POST['mc_currency'];
    $txn_id = $_POST['txn_id'];
    $receiver_email = $_POST['receiver_email'];
    $payer_email = $_POST['payer_email'];
	$custom = $_POST['custom'];

	if( $payment_status !== "Completed" ) {
		die("ERROR: invalid IPN checksum.");
	}
	if( $receiver_email !== $paypal['email'] ) {
		die("ERROR: invalid IPN checksum.");
	}
	if( $payment_currency !== "GBP" ) {
		die("ERROR: invalid IPN checksum.");
	}
	
	// Check if payment handler ID exists
	if( $conn->query("SELECT * FROM payment_handlers WHERE id='" . mysqli_real_escape_string($conn, $custom) . "'")->num_rows == 0 ) {
		die("ERROR: invalid IPN checksum.");
	}
	
	$pHandler['received_parameters'] = explode(":", $conn->query("SELECT * FROM payment_handlers WHERE id='" . mysqli_real_escape_string($conn, $custom) . "'")->fetch_assoc()['parameters']);
	
	// Get payment handler parameters
	$discordid = $pHandler['received_parameters'][0];
	$needed_level = intval($pHandler['received_parameters'][1]);
    
	// Get current user level because it will be needed for some checks
	$CurrentUserLevel = $conn->query("SELECT * FROM users WHERE discord_id='" . mysqli_real_escape_string($conn, $discordid) . "'")->fetch_assoc()['level'];
	
	// Check if the needed level exists
	$checkLevel = $conn->query("SELECT * FROM levels WHERE level=" . mysqli_real_escape_string($conn, $needed_level))->num_rows;
	if( $checkLevel == 0 ) {
		die("ERROR: invalid IPN checksum.");
	}
	
	// Check if user have current level or higher
	if( $CurrentUserLevel >= $needed_level ) {
		die("ERROR: invalid IPN checksum.");
	}
	
	// Get level info
	$level_info = $conn->query("SELECT * FROM levels WHERE level=" . mysqli_real_escape_string($conn, $needed_level))->fetch_assoc();
	
	// Check if user paid full amount or not
	if( $payment_amount < $level_info['price'] ) {
		die("ERROR: invalid IPN checksum.");
	}
	
	// -------------- all checks were done! Now lets give the user his/her level.
	if( $level_info['ismonthly'] == 0 ) {
		// This plan is a lifetime plan
		$conn->query("UPDATE users SET level=" . mysqli_real_escape_string($conn, $needed_level) . " WHERE discord_id='" . mysqli_real_escape_string($conn, $discordid) . "'");
		$conn->query("UPDATE users SET plan_expiry=0 WHERE discord_id='" . mysqli_real_escape_string($conn, $discordid) . "'");
	} else {
		// This plan is a monthly plan
		$expiry_date = new DateTime(date("Y-m-d")); // Y-m-d
		$expiry_date->add(new DateInterval('P30D')); // 30 days + today's date
		$expiry_date = strtotime($expiry_date->format('Y-m-d'));
		$conn->query("UPDATE users SET level=" . mysqli_real_escape_string($conn, $needed_level) . " WHERE discord_id='" . mysqli_real_escape_string($conn, $discordid) . "'");
		$conn->query("UPDATE users SET plan_expiry=" . mysqli_real_escape_string($conn, $expiry_date) . " WHERE discord_id='" . mysqli_real_escape_string($conn, $discordid) . "'");
	}

} else if (strcmp ($res, "INVALID") == 0) {
    die("ERROR: invalid IPN response.");
}
?>