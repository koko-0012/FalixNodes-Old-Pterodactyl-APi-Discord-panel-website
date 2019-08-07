<?php
session_start();
include("global.php");
include("../config.php");
if( checklogin() == true ) {
	$user = $conn->query("SELECT * FROM staffs WHERE id=" . mysqli_real_escape_string($conn, $_SESSION['user']))->fetch_assoc();
} else {
	header("Location: login");
	die();
}

if( !isset($_GET['id']) || empty($_GET['id']) ) {
	header("Location: /");
	die();
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