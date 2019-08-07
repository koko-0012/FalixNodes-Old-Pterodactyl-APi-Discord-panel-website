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

// Get all current user's servers and loop over them
$userServers = $conn->query("SELECT * FROM servers WHERE owner_id='" . mysqli_real_escape_string($conn, $user->id) . "'");
if( $userServers->num_rows == 0 ) {
	ShowError("You don't have any servers.");
} else {
	while($row = $userServers->fetch_assoc()) {
		// Get some server information, those are needed to update RAM
		$ch = curl_init("https://" . $ptero_domain . "/api/application/servers/" . $row['pterodactyl_serverid']);
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
		$ch = curl_init("https://" . $ptero_domain . "/api/application/servers/" . $row['pterodactyl_serverid'] . "/build");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
			"allocation" => $ServerAllocation,
			"memory" => 5,
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
}

// Redirect user to homepage with success message
ShowSuccess("Changed RAM of all your servers to 5 MB.");
?>