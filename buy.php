<?php
session_start();
include("global.php");
include("config.php");
if( checklogin() == true ) {
	$user = $_SESSION['discord_user'];
	$pterodactyl_panelinfo = $conn->query("SELECT * FROM users WHERE discord_id='" . mysqli_real_escape_string($conn, $user->id) . "'")->fetch_assoc();
	$pterodactyl_username = $pterodactyl_panelinfo['pterodactyl_username'];
	$pterodactyl_password = $pterodactyl_panelinfo['pterodactyl_password'];
} else {
	header("Location: /");
	die();
}
include("plans.php");

if( !isset($_GET['level']) ) {
	die("ERROR: `level` is required.");
}

// Check if the specificied level exists
$checkLevel = $conn->query("SELECT * FROM levels WHERE level=" . mysqli_real_escape_string($conn, $_GET['level']))->num_rows;
if( $checkLevel == 0 ) {
	die("ERROR: invalid level.");
}

// Check if user have current level or higher
if( $GET_USER_LEVEL >= intval($_GET['level']) ) {
	ShowError("You already have this plan or a higher one.");
}

// Get level info
$level_info = $conn->query("SELECT * FROM levels WHERE level=" . mysqli_real_escape_string($conn, $_GET['level']))->fetch_assoc();


// Create payment handler
$pHandler['id'] = substr(md5(mt_rand()), 0, 7);
$pHandler['parameters'] = mysqli_real_escape_string($conn, $user->id) . ":" . mysqli_real_escape_string($conn, $_GET['level']); // discordid:level
$conn->query("INSERT INTO payment_handlers (id, parameters) VALUES ('" . $pHandler['id'] . "', '" . $pHandler['parameters'] . "')");

echo '
<noscript>
JavaScript is required.
</noscript>

<script>
window.onload = function(){
  document.forms[\'buyform\'].submit();
}
</script>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="buyform" id="buyform">
    <input type="hidden" name="cmd" value="_xclick">
	<input type="hidden" name="notify_url" value="http://' . $site_domain . '/ipn/paypal">
    <input type="hidden" name="business" value="' . $paypal['email'] . '">
    <input type="hidden" name="item_name" value="PrimeServers">
    <input type="hidden" name="amount" value="' . $level_info['price'] . '">
    <input type="hidden" name="currency_code" value="GBP">
	<input type="hidden" name="custom" value="' . $pHandler['id'] . '">
</form>
';
?>