<?php
session_start();
include("global.php");
include("config.php");
if( checklogin() == true ) {
	$user = $_SESSION['discord_user'];
	$pterodactyl_panelinfo = $conn->query("SELECT * FROM users WHERE discord_id='" . mysqli_real_escape_string($conn, $user->id) . "'")->fetch_assoc();
	$pterodactyl_username = $pterodactyl_panelinfo['pterodactyl_username'];
	$pterodactyl_password = $pterodactyl_panelinfo['pterodactyl_password'];
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

    <title>LimitedNodes | Pricing</title>
  </head>
  <body>
	  
	  <center>
	
			<div class="jumbotron">
			  <h1 class="display-4">LimitedNodes</h1>
				<hr class="my-4">
			  <p class="lead">Free Minecraft Server Hosting</p>
				<?php
				if( checklogin() !== true ) {
					echo '
				<a class="btn btn-primary btn-lg" style="background-color: #7289da;outline: none;box-shadow:none;" href="login" role="button"><i class="fab fa-discord"></i> Login with Discord</a>
				';
				} else {
					echo '<hr class="my-4">Welcome ' . $user->username . '#' . $user->discriminator . '!<br /><a class="btn btn-primary btn-lg" href="logout" role="button"><i class="fas fa-sign-out-alt"></i> Logout</a><br /><br />';
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
				}
				?>
			</div>
			
	</center>
		  
	  <div class="container">
			<?php
			  if( checklogin() == true ) {
				  echo '
				  <a href="/" class="btn btn-primary" role="button"><i class="fas fa-home"></i> Home</a>
				  ';
			  }
			?>
		  <br /><br />
	  <h3>Monthly Plans</h3>
      <div class="card-deck mb-3 text-center">
	  
        <div class="card mb-4 box-shadow">
          <div class="card-header">
            <h4 class="my-0 font-weight-normal">Tier #1</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">£2 <small class="text-muted">/ mo</small></h1>
            <ul class="list-unstyled mt-3 mb-4">
              <li>5GB RAM Balance</li>
              <li>Unlimited Servers</li>
              <li>15gb DISK</li>
			  <li>4 CPU Cores per Server.</li>
            </ul>
            <a class="btn btn-lg btn-block btn-primary" href="buy?level=5">Purchase</a>
          </div>
        </div>
		
		
        <div class="card mb-4 box-shadow">
          <div class="card-header">
            <h4 class="my-0 font-weight-normal">Tier #2</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">£5 <small class="text-muted">/ mo</small></h1>
            <ul class="list-unstyled mt-3 mb-4">
              <li>10 GB RAM Balance</li>
              <li>Unlimited Servers</li>
              <li>25GB DISK</li>
			  <li>4 CPU Cores per Server</li>
            </ul>
            <a class="btn btn-lg btn-block btn-primary" href="buy?level=6">Purchase</a>
          </div>
        </div>
		
		
        <div class="card mb-4 box-shadow">
          <div class="card-header">
            <h4 class="my-0 font-weight-normal">Tier #3</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">£7 <small class="text-muted">/ mo</small></h1>
            <ul class="list-unstyled mt-3 mb-4">
              <li>20GB RAM Balance</li>
              <li>Unlimited Servers</li>
              <li>35GB DISK</li>
			  <li>4 CPU Cores per Server</li>
            </ul>
            <a class="btn btn-lg btn-block btn-primary" href="buy?level=7">Purchase</a>
          </div>
        </div>
		
		<div class="card mb-4 box-shadow">
          <div class="card-header">
            <h4 class="my-0 font-weight-normal">Tier #4</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">£10 <small class="text-muted">/ mo</small></h1>
            <ul class="list-unstyled mt-3 mb-4">
              <li>30GB RAM Balance</li>
              <li>Unlimited Servers</li>
              <li>45GB DISK</li>
			  <li>4 CPU cores per server</li>
            </ul>
            <a class="btn btn-lg btn-block btn-primary" href="buy?level=8">Purchase</a>
          </div>
        </div>
        
        <div class="card mb-4 box-shadow">
          <div class="card-header">
            <h4 class="my-0 font-weight-normal">Tier #5</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">£25 <small class="text-muted">/ mo</small></h1>
            <ul class="list-unstyled mt-3 mb-4">
              <li>60GB RAM Balance</li>
              <li>Unlimited Servers</li>
              <li>50GB DISK</li>
			  <li>6 CPU cores per server</li>
            </ul>
            <a class="btn btn-lg btn-block btn-primary" href="buy?level=9">Purchase</a>
          </div>
        </div>
		
		
      </div>
	 
      </div>

      <?php include("templates/footer.php"); ?>
	  
    </div>
	  
	  <br /><br />

    <!-- Optional JavaScript -->
    <!-- first Popper.js, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>