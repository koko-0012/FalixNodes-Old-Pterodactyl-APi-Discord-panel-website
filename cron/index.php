<?php
$base = dirname(dirname(__FILE__));
include($base . "/config.php");
include($base . "/global.php");

// cron_section:CheckUsersExpiry
$todays_date = new DateTime(date("Y-m-d")); // Y-m-d
$todays_date = strtotime($todays_date->format('Y-m-d'));
while(($row = $conn->query("SELECT * FROM users WHERE plan_expiry <= " . $todays_date . " AND plan_expiry != 0")->fetch_assoc()) != NULL) {
	$discord_id = $row['discord_id'];
	$conn->query("UPDATE users SET level=0 WHERE discord_id='" . mysqli_real_escape_string($conn, $discord_id) . "'");
	$conn->query("UPDATE users SET plan_expiry=0 WHERE discord_id='" . mysqli_real_escape_string($conn, $discord_id) . "'");
}
// cron_section:end()

// cron_section:Logger
file_put_contents($base . "/cron/log.txt", "Last execution: " . date('Y/m/d H:i:s'));
// cron_section:end()
?>