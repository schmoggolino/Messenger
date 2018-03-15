<?php
	session_start();
	include('funktionen.php');
	check_session();
	$chat_id	=addslashes(strip_tags($_GET['c']));
	$userid		=addslashes(strip_tags($_SESSION['userid']));
	new_message($userid, $chat_id, addslashes(strip_tags($_POST["b"])), '');
	



?>