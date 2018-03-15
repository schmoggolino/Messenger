<?php
	session_start();
	include("funktionen.php");
	$user_id=$_SESSION["userid"];
	$ip=$_SESSION['ip'];
	sql("insert into msg.logbuch (`user`,`ip_address`, `time`, `type`) values($user_id, '$ip', sysdate(),'log out')",$mysqli);
	unset($_SESSION["userid"]);
	$_SESSION['ip']='hlüü';
	session_destroy();
	echo "<script language='javascript' type='text/javascript'>";
	echo "window.location.href = 'index.php';";
	echo "</script>";



?>