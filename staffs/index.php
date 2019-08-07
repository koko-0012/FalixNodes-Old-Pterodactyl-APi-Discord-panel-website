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

    <title>LimitedNodes | Free Minecraft Server Hosting</title>
	  
	  <script>
		  $(window).on('load', function(){
			$("#createServerBox").load("create");
		  });
	  </script>
  </head>
  <body>
	  
	  <center>
	
			<div class="jumbotron">
			  <h1 class="display-4">LimitedNodes</h1>
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
		  <a href="#" class="btn btn-primary" role="button" data-toggle="modal" data-target="#createserver"><i class="fas fa-plus"></i> Create a Server</a>&nbsp;
		  <a href="searchUsers" class="btn btn-primary" role="button">Search Users</a>
		  <a href="searchServers" class="btn btn-primary" role="button">Search Servers</a>
		  <br /><br />
		  <h3>Last 50 servers</h3>
		  <?php
		  $results = mysqli_query($conn, "SELECT * FROM servers ORDER BY id DESC LIMIT 50");
				echo "<table class=\"table table-striped\">";
			  echo "
				  <thead>
				  <tr>
					<th>Name</th>
					<th>RAM</th>
					<th>Disk</th>
					<th>CPU Cores</th>
					<th>Owner</th>
					<th>Server ID</th>
					<th>Actions</th>
				  </tr>
				</thead>
			  ";
			  if( $results->num_rows !== 0 ) {
				 while($rowitem = mysqli_fetch_array($results)) {
					 $ch = curl_init("https://" . $ptero_domain . "/api/application/servers/" . $rowitem['pterodactyl_serverid']);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						"Authorization: Bearer " . $ptero_key,
						"Content-Type: application/json",
						"Accept: Application/vnd.pterodactyl.v1+json"
					));
					$result = curl_exec($ch);
					curl_close($ch);
					 $result = json_decode($result, true);
					 $serverRAM = $result['attributes']['limits']['memory'] . " MB";
					 $serverDisk = $result['attributes']['limits']['disk'] . " MB";
					 $serverCores = $result['attributes']['limits']['cpu'] / 100;
					 $serverName = $result['attributes']['name'];
					 $serverIdentifier = $result['attributes']['identifier'];
					echo "<tr>";
					echo "<td>" . $serverName . "</td>";
					echo "<td>" . $serverRAM . "</td>";
					echo "<td>" . $serverDisk . "</td>";
					echo "<td>" . $serverCores . "</td>";
					echo "<td>" . DiscordIdToUsername($discord['autojoin_guildid'], $rowitem['owner_id'], $discord['bot_token']) . "</td>";
					echo "<td>" . $serverIdentifier . "</td>";
					echo "<td>" . '<a href="deleteserver?id=' . $rowitem['pterodactyl_serverid'] . '" class="btn btn-danger" role="button">Delete</a> &nbsp; <button type="button" class="btn btn-info" data-toggle="modal" data-target="#changeram" onclick="document.getElementById(\'changeram_serverid\').value=\'' . $rowitem['pterodactyl_serverid'] . '\'">Change RAM</button>' . "</td>";
					echo "</tr>";
				}
			  } else {
				  echo "There's no servers to show.";
			  }
				echo "</table>"; //end table tag
		  ?>
	  </div>
		  
	  </center>
	  
	<!-- modal:createserver -->
	<div id="createserver" class="modal fade" role="dialog">
	  <div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<h4 class="modal-title">Create Server</h4>
		  </div>
		  <div class="modal-body" id="createServerBox">
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>

	  </div>
	</div>
	
	<!-- modal:changeram -->
	<div id="changeram" class="modal fade" role="dialog">
	  <div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<h4 class="modal-title">Change Server RAM</h4>
		  </div>
		  <div class="modal-body">
				<form action="changeram" method="get">
				<input type="hidden" name="id" id="changeram_serverid" class="form-control">
				New server RAM (in MB): <input type="number" name="newram" class="form-control" min="0" value="128"><br />
				<input type="submit" value="Change RAM" class="btn btn-success">
				</form>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>

	  </div>
	</div>

    <!-- Optional JavaScript -->
    <!-- first Popper.js, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>