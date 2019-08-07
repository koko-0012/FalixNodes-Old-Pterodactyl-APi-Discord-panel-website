<?php
session_start();
include("global.php");
include("config.php");
include("plans.php");
if( checklogin() == true ) {
	$user = $_SESSION['discord_user'];
} else {
	notloggedin("You must be logged in.");
}

if( empty($_POST) ) {
	header("Location: /");
	die();
}

$NewBalanceWillBe = 0;
foreach( $_POST as $stuff => $val ) {
	$NewBalanceWillBe = $NewBalanceWillBe + intval($val);
}
if( $NewBalanceWillBe > ($rambalance + $user_extra_ram) ) {
	ShowError("Sorry, your RAM balance will be " . $NewBalanceWillBe . " MB after this change, but your maximum RAM balance is " . ($rambalance + $user_extra_ram) . " MB");
}

foreach( $_POST as $stuff => $val ) {
	if(is_numeric($val) && $val > 0 && $val == round($val, 0)){
		//pass
	} else {
		ShowError("RAM must be a number.");
	}
	
	if( $val < 128 ) {
		ShowError("The minimum memory for each server is 128 MB.");
	}
	
	// Check if user have permissions for the server ID, and if the server exists
	$checkperms = $conn->query("SELECT * FROM servers WHERE owner_id='" . mysqli_real_escape_string($conn, $user->id) . "' AND pterodactyl_serverid=" . mysqli_real_escape_string($conn, $stuff));
	if( $checkperms->num_rows == 0 ) {
		ShowError("You don't have permissions to control one of these servers or these server doesn't exists.");
	}
	
	// Get some server information, those are needed to update RAM
	$ch = curl_init("https://" . $ptero_domain . "/api/application/servers/" . $stuff);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Authorization: Bearer " . $ptero_key,
		"Content-Type: application/json",
		"Accept: Application/vnd.pterodactyl.v1+json"
	));
	$result = curl_exec($ch);
	curl_close($ch);
	$ServerAllocation = json_decode($result, true)['attributes']['allocation'];
	$x_ServerDisk = json_decode($result, true)['attributes']['limits']['disk'];
	$x_ServerCpu = json_decode($result, true)['attributes']['limits']['cpu'];
	$x_ServerIO = json_decode($result, true)['attributes']['limits']['io'];
	$x_ServerDbs = json_decode($result, true)['attributes']['feature_limits']['databases'];
	$x_ServerAllocations = json_decode($result, true)['attributes']['feature_limits']['allocations'];

	// Update server RAM now
	$ch = curl_init("https://" . $ptero_domain . "/api/application/servers/" . $stuff . "/build");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
		"allocation" => $ServerAllocation,
		"memory" => intval($val),
		"swap" => 0,
		"disk" => $x_ServerDisk,
		"io" => $x_ServerIO,
		"cpu" => $x_ServerCpu,
		"feature_limits" => array(
			"databases" => $x_ServerDbs,
			"allocations" => $x_ServerAllocations
		)
	)));
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Authorization: Bearer " . $ptero_key,
		"Content-Type: application/json",
		"Accept: Application/vnd.pterodactyl.v1+json"
	));
	$result = curl_exec($ch);
	curl_close($ch);
}

ShowSuccess("Changed RAM!");
?>