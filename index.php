<!DOCTYPE html>
<html lang="en">
<!--------
	BOOTSTRAP TEMPLATE 
--------->
  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="JAR">
	
	<link rel="icon" href="site_media/chat.ico">
    <title>Messenger-Login</title>
	
    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
 
  </head>

  <body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <div class="container">
        <a class="navbar-brand" href="#">
          <img src="http://placehold.it/300x60?text=Logo" width="150" height="30" alt="">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
              <a class="nav-link" href=index.php">Home
                <span class="sr-only">(current)</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="about.php">About</a>
            </li>
            
          </ul>
        </div>
      </div>
    </nav>
	
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/popper/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--------
	BOOTSTRAP TEMPLATE 
--------->
    <!-- Page Content -->
    <div class="container">
	<p>
      <h1 class="mt-5">Instant messenger</h1>
<?php
	session_start();
	if (isset($_GET["failed"])){
		echo '<span class="badge badge-danger">Wrong Username or Password</span><p>';
	}
	if (isset($_GET["c"])){
		echo '<span class="badge badge-danger">Your identity could not be verified, please login again. try turning off your proxy or vpn if you are using any</span><p>';
	}
	
	
	

?>

<form class="form-inline"action="check_pw.php <?php if(isset($_GET['fwd']))
			{echo"?fwd=".$_GET['fwd']."php";} ?>" method="post">
  <div class="form-group">
    <input 	type="text" 	name="user" 	class="form-control"  placeholder="Username">
  </div>
  <div class="form-group mx-sm-3">
    <label for="inputPassword2" class="sr-only">Password</label>
    <input 	type="password" name="password" class="form-control" id="inputPassword2" placeholder="Password">
  </div>
  <button 	type="submit" 					class="btn btn-secondary">Confirm identity</button>
</form>



<p>
	<strong>
		No Account yet?
	</strong>
</p>
	Register now
<p>

<form action="create_user.php" class="form-inline" method="post">
	<div class="form-group">
		<input type ="Text" 	name="user" 	class="form-control" 						placeholder="Username">
	</div>
	<div class="form-group mx-sm-3">
		<input type="password" 	name="password" class="form-control" id="inputPassword2" 	placeholder="Password">
	</div>
	<div class="form-group">
		<input type="password" 	name="password2"class="form-control" id="inputPassword2" 	placeholder="Password">
	</div>
	<div class="form-group mx-sm-3">
		<input type ="email" 	name="email" 	class="form-control" 						placeholder="E-mail">
	</div>
		<input type ="submit" 					class="btn btn-secondary" 					value      ="Register">
</form>
<p> <strong> forgot your password?</strong><p>
<form class="form-inline" action="not_developed.php" method="post">
	<div class="form-group">
		<input type="text" name="name" class="form-control"  placeholder="Username">
	</div>
	<div class="form-group mx-sm-3">
	<button type="submit" class="btn btn-secondary">Send me an E-mail</button>
	</div>
</form>

</div>
