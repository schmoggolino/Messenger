<?php
	
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
			<li class="nav-item">
              <a class="nav-link" href="user.php?u=<?php echo $user_id; ?>"><?php echo $as; ?>
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
      <h1 class="mt-5">New Chat with</h1>
	  <p>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
<div id="content">



<?php
	if(isset($_GET["new"])){//erstelle neuen chat, dann an diesen chat weiterleiten
		$with=addslashes(strip_tags($_GET['with']));
		$name=addslashes(strip_tags($_GET['name']));
		$chat_id=new_chat($user_id, $with);
		echo $chat_id;
		weiterleiten("chat.php?chat_id=$chat_id&name=$name");
	}
	$dest='new_chat.php';
	$result=sql("select * from msg.contacts where user_id=$user_id",$mysqli);
	while($row = $result->fetch_object()){
		$name= $row->name;
		$with= $row->contact;
		echo"<input type='button' class='btn btn-secondary' style='margin-bottom:0.5em;' onclick=\"location.href='$dest?new=1&with=$with&name=$name';\" value='$name' /><p>";//auf dieselbe seite ernetu verweisen mit new=ja und gruppenteilnehmern
	}
	$rows_returned 	= $result->num_rows;
	if($rows_returned==0){
		echo"	you dont have any Contacts. please add people before starting to chat.
				<p>
				".'
				<input class="btn btn-secondary" type ="submit" style="margin-bottom:0.5em;" value="okay :("</input>
				
				';
				
	}
?>
<button style="margin-bottom:0.5em;" class="btn btn-secondary" onclick="window.location.href='user_search.php'">Add new Contacts</button>