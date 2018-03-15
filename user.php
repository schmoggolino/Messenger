<?php
	session_start();
	include('funktionen.php');
	check_session();
	$user_id=$_SESSION['userid'];
	//echo $user_id;
	$mysqli=connect();
	$result=sql("select name from msg.users where user_id=$user_id",$mysqli);
	$row = $result->fetch_object();	//all chats of this person
	/******************
		get data about the message inside
	******************/
	$as=$row 		->name;

?>
<!DOCTYPE html>
<html lang="en">
<!--------
	BOOTSTRAP TEMPLATE 
--------->
  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
	
	<link rel="icon" href="site_media/chat.ico">
    <title>Messenger</title>

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
            <li class="nav-item">
              <a class="nav-link" href="main.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="about.php">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php">Logout</a>
            </li>
			<li class="nav-item active">
              <a class="nav-link" href="user.php?u=<?php echo $user_id; ?>"><?php echo $as; ?>
				<span class="sr-only">(current)</span>
			  </a>
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
      <h1 class="mt-5"><?php echo $as; ?></h1>
	  <p>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
<div id="content">

<?php
	$p_user_id=addslashes(strip_tags($_GET["u"]));
	if($_SESSION["userid"]!=$p_user_id){
		weiterleiten('main.php');
	}
	if(isset($_GET["request"])&&addslashes(strip_tags($_GET["request"]))=='ch_pw'){
		$password=addslashes(strip_tags($_POST["old_password"]));
		$new_pw=addslashes(strip_tags($_POST["password"]));
		$new_pw2=addslashes(strip_tags($_POST["password2"]));
		if($new_pw==$new_pw2) {
			$ip		= get_client_ip();
			sql("insert into msg.login_attempts values('$ip',sysdate())",$mysqli);
			$result=sql("select * from msg.login_attempts where ip_adress='$ip' and c_date>sysdate()-INTERVAL 1 MINUTE",$mysqli);
			$rows_returned 	= $result->num_rows;
			if($rows_returned>10){
				sql("insert into msg.blocklist values('$ip','ip_flood detected')",$mysqli);
			}
			
			$result=sql("select * from msg.blocklist where ip='$ip'",$mysqli);
			$rows_returned 	= $result->num_rows;
			if($rows_returned==0){
				$result=sql("select * from msg.login where user_id=$user_id",$mysqli);//binary= case sensitive password
				$rows_returned 	= $result->num_rows;
				if($rows_returned>0){
					$row = $result->fetch_object();
					$DBPW= $row	  ->password;
					$user_id=$row ->user_id;
					/******************
						verify password
					******************/
					if ($as !== false && password_verify($password, $DBPW)) {
						$password=password_hash($new_pw, PASSWORD_DEFAULT);
						sql("update msg.login set password='$password' where user_id=$user_id",$mysqli);
						sql("insert into msg.logbuch (`user`,`ip_address`, `time`, `type`) values($user_id, '$ip', sysdate(),'password changed')",$mysqli);
		
						$mysqli->commit();
						
						
					} else {
						echo '<span class="badge badge-danger">Passwort war ungültig</span><br>';
					}
				}//rows returned>0
				else{
					echo '<span class="badge badge-danger">Passwort war ungültig</span><br>';
				}
			}//rows returned==0
		}else{
			echo '<span class="badge badge-danger">passwörter stimmen nicht überein</span> <br>';
		}
	}elseif(isset($_GET["request"])&&addslashes(strip_tags($_GET["request"]))=='ch_un'){
		$name=addslashes(strip_tags($_POST["new_name"]));
		$mysqli=connect();
		sql("update msg.users set name='$name' where user_id=$user_id",$mysqli);
		sql("insert into msg.logbuch (`user`,`ip_address`, `time`, `type`) values($user_id, '$ip', sysdate(),'username changed')",$mysqli);
		
		weiterleiten("user.php?u=$user_id;");
	}
	
	//isset($_GET["request"])&&addslashes(strip_tags($_GET["u"]))='ch_pw'
	

?>
<form action="user.php?u=<?php echo $p_user_id; ?>&request=ch_un" class="form-inline" method="post">
	<div class="form-group mx-sm-3">
		<input type="text" 	name="new_name" class="form-control" id="inputPassword2"placeholder="New username">
	</div>
		<input type ="submit" 					class="btn btn-secondary" 					value      ="Change screenname">
</form>

<form action="user.php?u=<?php echo $p_user_id; ?>&request=ch_pw" class="form-inline" method="post">
	<div class="form-group mx-sm-3">
		<input type="password" 	name="old_password" class="form-control" id="inputPassword2"placeholder="Old Password">
	</div>
	<div class="form-group">
		<input type="password" 	name="password"class="form-control" id="inputPassword2" 	placeholder="New Password">
	</div>
	<div class="form-group">
		<input type="password" 	name="password2"class="form-control" id="inputPassword2" 	placeholder="Repeat New Password">
	</div>
		<input type ="submit" 					class="btn btn-secondary" 					value      ="Change Password">
</form>

<?php
if($user_id==1){
		echo'<h2>You are considered an Admin</h2>';
		$server	= $_SERVER['SERVER_ADDR'];
		$ip		= get_client_ip();
		echo "server=".$server."<br>";
		echo "client=".$ip;
		if ($ip==$server){
			$localIP = getHostByName(getHostName());
			echo" your device hosts this site locally as $localIP";
		}else{
			echo "you are a guest";
		}
		echo '<form action="testing.php" method="post">
				<input class="btn btn-secondary" type ="submit" value="set testing (is ';
		if(isset($_SESSION["testing"])){
			echo $_SESSION["testing"];
		}else{
			echo"0";
		}
		echo ")\"></form><p>";
		echo "<button class=\"btn btn-secondary\" onclick=\"location.href='logbuch.php'\" >logbuch</button>";
	}

?>