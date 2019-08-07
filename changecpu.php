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

if( !isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['newcpu']) || empty($_GET['newcpu']) ) {
	header("Location: /");
	die();
}

if(is_numeric($_GET['newcpu']) && $_GET['newcpu'] > 0 && $_GET['newcpu'] == round($_GET['newcpu'], 0)){
	//pass
} else {
	ShowError("CPU must be a number.");
}

// Check if user have permissions for the server ID, and if the server exists
$checkperms = $conn->query("SELECT * FROM servers WHERE owner_id='" . mysqli_real_escape_string($conn, $user->id) . "' AND pterodactyl_serverid=" . mysqli_real_escape_string($conn, $_GET['id']));
if( $checkperms->num_rows == 0 ) {
	ShowError("You don't have permissions to control this server or this server doesn't exists.");
}

//Check if user exceeded his CPU cores per server
if( intval($_GET['newcpu']) > $max_cores ) {
	ShowError("Sorry, your max CPU cores per server is " . $max_cores);
}

// Get some server information, those are needed to update CPU
$ch = curl_init("https://" . $ptero_domain . "/api/application/servers/" . $_GET['id']);
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
$x_ServerMemory = json_decode($result, true)['attributes']['limits']['memory'];
$x_ServerIO = json_decode($result, true)['attributes']['limits']['io'];
$x_ServerDbs = json_decode($result, true)['attributes']['feature_limits']['databases'];
$x_ServerAllocations = json_decode($result, true)['attributes']['feature_limits']['allocations'];

// Update server CPU now
$ch = curl_init("https://" . $ptero_domain . "/api/application/servers/" . $_GET['id'] . "/build");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
	"allocation" => $ServerAllocation,
	"memory" => $x_ServerMemory,
	"swap" => 0,
	"disk" => $x_ServerDisk,
	"io" => $x_ServerIO,
	"cpu" => intval($_GET['newcpu']) * 100,
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

// Redirect user to homepage with success message
ShowSuccess("Changed CPU cores!");
?>