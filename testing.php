<?php
	session_start();
	include("funktionen.php");
	if(isset($_SESSION["testing"])&&$_SESSION["testing"]==1){
		$_SESSION["testing"]=0;
	}else{
		$_SESSION["testing"]=1;
	}
	echo $_SESSION["testing"];
	weiterleiten("index.php");
?>