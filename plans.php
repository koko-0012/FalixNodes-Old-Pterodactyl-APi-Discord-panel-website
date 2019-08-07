<?php
//session_start(); - commented! use it on the files you want.
//include("config.php"); - commented! use it on the files you want.
//include("global.php"); - commented! use it on the files you want.

if( checklogin() == true ) {
	$user = $_SESSION['discord_user'];
	$GET_USER_LEVEL = $conn->query("SELECT * FROM users WHERE discord_id='" . mysqli_real_escape_string($conn, $user->id) . "'")->fetch_assoc()['level'];
	$level_data = $conn->query("SELECT * FROM levels WHERE level=" . $GET_USER_LEVEL)->fetch_assoc();
	$rambalance = $level_data['ram_balance'];
	$maxservers = $level_data['max_servers'];
	$maxdisk = $level_data['max_disk'];
	$max_cores = $level_data['max_cores'];
	$user_extra_ram = $conn->query("SELECT * FROM users WHERE discord_id='" . mysqli_real_escape_string($conn, $user->id) . "'")->fetch_assoc()['extra_ram'];
	$user_extra_servers = $conn->query("SELECT * FROM users WHERE discord_id='" . mysqli_real_escape_string($conn, $user->id) . "'")->fetch_assoc()['extra_servers'];
} else {
	$GET_USER_LEVEL = "none";
}
?>