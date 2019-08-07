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

if( !isset($_POST['discord_id']) || empty($_POST['discord_id']) ) {
	ShowError("The Discord ID field is required.");
}

if( isset($_POST['ChangeExtraRam']) ) {
	$conn->query("UPDATE users SET extra_ram=" . $_POST['new_extra_ram'] . " WHERE discord_id='" . $_POST['discord_id'] . "'");
	ShowSuccess("Done action!");
}

if( isset($_POST['ChangeExtraServers']) ) {
	$conn->query("UPDATE users SET extra_servers=" . $_POST['new_extra_servers'] . " WHERE discord_id='" . $_POST['discord_id'] . "'");
	ShowSuccess("Done action!");
}

if( isset($_POST['ChangeLevelId']) ) {
	$conn->query("UPDATE users SET level=" . $_POST['new_level'] . " WHERE discord_id='" . $_POST['discord_id'] . "'");
	ShowSuccess("Done action!");
}
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	  
	  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
	  
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <title>FalixNodes | Free Minecraft Server Hosting</title>
  </head>
  <body>
	  
	  <center>
	
			<div class="jumbotron">
			  <h1 class="display-4">FalixNodes</h1>
				<hr class="my-4">
			  <p class="lead">Staffs Panel</p>
				<?php
				echo '<hr class="my-4">Welcome ' . $user['username'] . '!<br /><a class="btn btn-primary btn-lg" href="logout" role="button"><i class="fas fa-sign-out-alt"></i> Logout</a><br /><br />';
					if( isset($_GET['success']) ) {
						echo '
						<div class="alert alert-success" role="alert">
						  <strong>Success!</strong> ' . base64_decode($_GET['success']) . '
						</div>
						';
					}
					if( isset($_GET['error']) ) {
						echo '
						<div class="alert alert-danger" role="alert">
						  <strong>Error!</strong> ' . base64_decode($_GET['error']) . '
						</div>
						';
					}
				?>
			</div>
		  
	  <div class="container">
		  <a href="index" class="btn btn-primary" role="button">Home</a>&nbsp;
		  <a href="searchUsers" class="btn btn-primary" role="button">Search Users</a>
		  <br /><br />
		  <?php
		  $userSearch = $conn->query("SELECT * FROM users WHERE discord_id='" . mysqli_real_escape_string($conn, $_POST['discord_id']) . "'")->fetch_assoc();
		  echo '
		  <strong>Discord ID:</strong> ' . $userSearch['discord_id'] . '<br />
		  <strong>Discord Username:</strong> ' . DiscordIdToUsername($discord['autojoin_guildid'], $userSearch['discord_id'], $discord['bot_token']) . '<br />
		  <strong>Plan Level ID:</strong> ' . $userSearch['level'] . '<br />
		  <strong>Extra RAM (in MB):</strong> ' . $userSearch['extra_ram'] . '<br />
		  <strong>Extra servers:</strong> ' . $userSearch['extra_servers'] . '<br />
		  <strong>Pterodactyl User ID:</strong> ' . $userSearch['pterodactyl_userid'] . '<br />
		  <strong>Pterodactyl Username:</strong> ' . $userSearch['pterodactyl_username'] . '<br />
		  <strong>Plan Expiry Date (day/month/year):</strong> ' . date('d/m/Y', $userSearch['plan_expiry']) . ' - <em>If 00/00/0000 or 01/01/1970 then it means Never.</em>
		  ';
		  ?>
		  
		  <hr>
		  
		  <h3>Change extra RAM</h3>
		  <form action="" method="post">
		  <input type="hidden" name="discord_id" value="<?php echo $_POST['discord_id']; ?>">
		  New extra RAM (in MB): <input type="number" name="new_extra_ram" class="form-control" required><br />
		  <input type="submit" name="ChangeExtraRam" class="btn btn-primary" value="Change!">
		  </form>
		  
		  <hr>
		  
		  <h3>Change extra servers</h3>
		  <form action="" method="post">
		  <input type="hidden" name="discord_id" value="<?php echo $_POST['discord_id']; ?>">
		  New extra servers: <input type="number" name="new_extra_servers" class="form-control" required><br />
		  <input type="submit" name="ChangeExtraServers" class="btn btn-primary" value="Change!">
		  </form>
		  
		  <hr>
		  
		  <h3>Change plan level ID</h3>
		  <form action="" method="post">
		  <input type="hidden" name="discord_id" value="<?php echo $_POST['discord_id']; ?>">
		  New level ID: <input type="number" name="new_level" class="form-control" required><br />
		  <input type="submit" name="ChangeLevelId" class="btn btn-primary" value="Change!">
		  </form>
		  
		  <br /><br />
	  </div>
		  
	  </center>

    <!-- Optional JavaScript -->
    <!-- first Popper.js, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>