<?php
session_start();
include("global.php");
include("config.php");
if( checklogin() == true ) {
	$user = $_SESSION['discord_user'];
} else {
	notloggedin("You must be logged in.");
}

if( !isset($_GET['id']) || empty($_GET['id']) ) {
	header("Location: /");
	die();
}

// Check if user have permissions for the server ID, and if the server exists
$checkperms = $conn->query("SELECT * FROM servers WHERE owner_id='" . mysqli_real_escape_string($conn, $user->id) . "' AND pterodactyl_serverid=" . mysqli_real_escape_string($conn, $_GET['id']));
if( $checkperms->num_rows == 0 ) {
	ShowError("You don't have permissions to delete this server or this server doesn't exists.");
}

// Delete the server now
$ch = curl_init("https://" . $ptero_domain . "/api/application/servers/" . $_GET['id'] . "/force");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	"Authorization: Bearer " . $ptero_key,
	"Content-Type: application/json",
	"Accept: Application/vnd.pterodactyl.v1+json"
));
curl_exec($ch);
curl_close($ch);

// Delete server from database
$conn->query("DELETE FROM servers WHERE pterodactyl_serverid=" . mysqli_real_escape_string($conn, $_GET['id']));

// Redirect user to homepage with success message
ShowSuccess("Deleted server!");
?>