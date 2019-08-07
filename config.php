<?php
$servername = "localhost";
$username = "";
$password = "";
$dbname = "";
$conn = new mysqli($servername, $username, $password, $dbname);

// Site domain
$site_domain = "limitednodes.host";

// Pterodactyl API settings
$ptero_domain = "";
$ptero_key = "";

// Payment settings
$paypal['email'] = "";

// Discord server settings
$discord['autojoin_role'] = "C582892108046663690"; //role ID
$discord['autojoin_guildid'] = "579239670639099915"; //server ID
$discord['bot_token'] = "";

// Discord OAUTH2 Settings
$discord_oauth2['client_id'] = "";
$discord_oauth2['client_secret'] = "";
$discord_oauth2['redirect_uri'] = "http://domain.host/login";
?>