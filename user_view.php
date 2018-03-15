<?php
	include('funktionen.php');
	check_session();
	$user_id=$_SESSION['userid'];
	$mysqli=connect();
	$viewing=strip_tags(addslashes($_GET["with"]));
	$result=sql("select * from msg.users where user_id=$viewing",$mysqli);
	$row = $result->fetch_object();
	$name= $row->name;
	echo"$name";
?>