<?php
session_start();
include("global.php");
include("config.php");
include("plans.php");
if( checklogin() == false ) {
	notloggedin("You must be logged in.");
}
$user = $_SESSION['discord_user'];
$getPanelIDofUser = $conn->query("SELECT * FROM users WHERE discord_id='" . mysqli_real_escape_string($conn, $user->id) . "'")->fetch_assoc()['pterodactyl_userid'];
if( isset($_POST['submit']) ) {
	if( empty($_POST['server_name']) || empty($_POST['server_ram']) || empty($_POST['location']) || empty($_POST['servertype']) || empty($_POST['cpu']) || !isset($_POST['server_name']) || !isset($_POST['server_ram']) || !isset($_POST['location']) || !isset($_POST['servertype']) || !isset($_POST['cpu']) ) {
		ShowError("All the fields are required.");
	}
	if(is_numeric($_POST['server_ram']) && $_POST['server_ram'] > 0 && $_POST['server_ram'] == round($_POST['server_ram'], 0)){
		//pass
	} else {
		ShowError("RAM must be a number.");
	}
	if(is_numeric($_POST['cpu']) && $_POST['cpu'] > 0 && $_POST['cpu'] == round($_POST['cpu'], 0)){
		//pass
	} else {
		ShowError("CPU must be a number.");
	}
	
	if( $_POST['server_ram'] < 128 ) {
		ShowError("The minimum memory is 128 MB.");
	}
	
	//Check if user reached max servers or max CPU cores
	$currentUserServersAmount = $conn->query("SELECT * FROM servers WHERE owner_id='" . mysqli_real_escape_string($conn, $user->id) . "'")->num_rows;
	if( $currentUserServersAmount >= ($maxservers + $user_extra_servers) ) {
		ShowError("Sorry, you have reached your maximum servers amount. (" . $maxservers . " servers is your maximum amount)");
	}
	if( $_POST['cpu'] > $max_cores ) {
		ShowError("Sorry, you have reached your maximum CPU cores. (" . $max_cores . " is your maximum CPU cores)");
	}
	
	//Check if user exceeded his max RAM balance
	$currentUsedBalance = 0 + $_POST['server_ram'];
	$userServers = $conn->query("SELECT * FROM servers WHERE owner_id='" . mysqli_real_escape_string($conn, $user->id) . "'");
	if($userServers->num_rows > 0) {
		while($row = $userServers->fetch_assoc()) {
			$ch = curl_init("https://" . $ptero_domain . "/api/application/servers/" . $row['pterodactyl_serverid']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Authorization: Bearer " . $ptero_key,
				"Content-Type: application/json",
				"Accept: Application/vnd.pterodactyl.v1+json"
			));
			$result = curl_exec($ch);
			curl_close($ch);
			$currentUsedBalance = $currentUsedBalance + json_decode($result, true)['attributes']['limits']['memory'];
		}
	}
	if( $currentUsedBalance > ($rambalance + $user_extra_ram) ) {
		ShowError("Sorry, you have reached your max RAM balance. (" . ($rambalance + $user_extra_ram) . " MB is your balance)");
	}
	
	// Location checker
	if( $GET_USER_LEVEL == 0 ) {
		// this user is free
		$allowed_locations = array(5, 6, 9);
	} else if ( $GET_USER_LEVEL == 122 ) {
		// this user is staff
		$allowed_locations = array(4, 5, 6, 7, 1, 9);
	} else {
	    // this user is donator
	    $allowed_locations = array(4, 5, 6, 7, 9);
	}
	if( !in_array($_POST['location'], $allowed_locations) ) {
		ShowError("You don't have permissions to create a server in this location.");
	}
	
	// get some info for the specificied egg .. those are needed to create server
	$ch = curl_init("https://" . $ptero_domain . "/api/application/nests/1/eggs/" . intval($_POST['servertype']));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Authorization: Bearer " . $ptero_key,
		"Content-Type: application/json",
		"Accept: Application/vnd.pterodactyl.v1+json"
	));
	$result = curl_exec($ch);
	curl_close($ch);
	$result_jdecoded = json_decode($result, true);
	$docker_image = $result_jdecoded['attributes']['docker_image'];
	$startup_info = $result_jdecoded['attributes']['startup'];
	
	$ch = curl_init("https://" . $ptero_domain . "/api/application/servers");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Authorization: Bearer " . $ptero_key,
		"Content-Type: application/json",
		"Accept: Application/vnd.pterodactyl.v1+json"
	));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
		"name" => $_POST['server_name'],
		"user" => intval($getPanelIDofUser),
		"nest" => 1,
		"egg" => intval($_POST['servertype']),
		"docker_image" => $docker_image,
		"startup" => $startup_info,
		"limits" => array(
			"memory" => $_POST['server_ram'],
			"swap" => 0,
			"disk" => 5000,
			"io" => 500,
			"cpu" => intval($_POST['cpu']) * 100
		),
		"feature_limits" => array(
			"databases" => 0,
			"allocations" => 0
		),
		"environment" => array(
			"DL_VERSION" => "latest",
			"SERVER_JARFILE" => "server.jar",
			"BUILD_NUMBER" => "latest",
			"BUNGEE_VERSION" => "latest",
			"WATERFALL_VERSION" => "latest",
			"NUKKIT_VERSION" => "latest",
			"PMMP_VERSION" => "latest",
			"BEDROCK_VERSION" => "latest",
			"STARTUP_CMD" => "bash",
			"LD_LIBRARY_PATH" => "."
		),
		"deploy" => array(
			"locations" => [intval($_POST['location'])],
			"dedicated_ip" => false,
			"port_range" => []
		),
		"start_on_completion" => false
	)));
    $result = curl_exec($ch);
    curl_close($ch);
    $serverinfo = json_decode($result, true);
	
	if( $serverinfo['object'] !== "server" ) {
		ShowError("An error has occured while creating your server. If this error still exists for long time, please contact support.");
	}
	
	//add server to database
	$conn->query("INSERT INTO servers (pterodactyl_serverid, owner_id) VALUES (" . $serverinfo['attributes']['id'] . ", '" . mysqli_real_escape_string($conn, $user->id) . "')");
	
	//redirect to homepage with success message
	ShowSuccess("Created server!");
}


// Get user's RAM balance
$currentUsedBalance = 0;
$userServers = $conn->query("SELECT * FROM servers WHERE owner_id='" . mysqli_real_escape_string($conn, $user->id) . "'");
if($userServers->num_rows > 0) {
	while($row = $userServers->fetch_assoc()) {
		$ch = curl_init("https://" . $ptero_domain . "/api/application/servers/" . $row['pterodactyl_serverid']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Authorization: Bearer " . $ptero_key,
			"Content-Type: application/json",
			"Accept: Application/vnd.pterodactyl.v1+json"
		));
		$result = curl_exec($ch);
		curl_close($ch);
		$currentUsedBalance = $currentUsedBalance + json_decode($result, true)['attributes']['limits']['memory'];
	}
}
?>
<strong>Your used RAM balance is:</strong> <?php echo $currentUsedBalance; ?> MB / <?php echo ($rambalance + $user_extra_ram); ?> MB
<form action="create.php" method="post">
	Server Name: <input type="text" name="server_name" class="form-control" required><br />
	Server RAM (in MB): <input type="number" name="server_ram" class="form-control" min="128" value="128" required><br />
	CPU Cores: <input <input type="number" name="cpu" class="form-control" min="1" value="1" required><br />
	Location:
		<input type="radio" name="location" value="4"> Donator 1 - Donator
		<input type="radio" name="location" value="7"> Donator 2 - Shitty Donator
		<input type="radio" name="location" value="5"> Free 1 - Free
		<input type="radio" name="location" value="6"> Free 2 - Free
		<input type="radio" name="location" value="9"> Free 3 - Free
		<input type="radio" name="location" value="1"> Staff 1 - Staff ONLY!
	<br /><br />
	Server Type:
		<div id="ServerType">
			<div class="card" style="width: 25rem;">
			  <div class="card-body">
				<h5 class="card-title">Minecraft: Java Edition</h5>
					<input type="radio" name="servertype" value="1"> BungeeCord
					<input type="radio" name="servertype" value="3"> Spigot
			  </div>
			</div>
							<br />
			<div class="card" style="width: 25rem;">
			  <div class="card-body">
				<h5 class="card-title">Discord: Discord.js</h5>
					<input type="radio" name="servertype" value="16"> Discord Bot Hosting
			  </div>
			</div>
		</div>
	<br />
	<input type="submit" name="submit" value="Create!" class="btn btn-success">
</form>