<?php
	include('funktionen.php');
	$user=		addslashes(strip_tags($_POST["user"]));
	$password=	addslashes(strip_tags($_POST["password"]));
	$password2=	addslashes(strip_tags($_POST["password2"]));
	$email=		addslashes(strip_tags($_POST["email"]));
	$ip=get_client_ip();
	if($password!==$password2){
		not_created('passwords dont match');
	}
	elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		not_created("invalid email $email ");
	} else{
		$user_id=create_user($user, $password, $email);
		if($user_id==false){
			not_created('something else');
			//user wurde nicht erstellt
		}else{
			echo"user created $user_id";
			sql("insert into msg.logbuch (`user`,`ip_address`, `time`, `type`) values($user_id, '$ip', sysdate(),'account created')",$mysqli);
			$_SESSION["userid"]=$user_id;
			weiterleiten("main.php");
		}
	}
	
	function not_created($reason){
		if($reason=='passwords dont match'){
			$var=0;
		}elseif($reason=='passwords dont match'){
			$var=0;
		}
		$ip=get_client_ip();
		$mysqli=connect();
		sql("insert into msg.logbuch (`user`,`ip_address`, `time`, `type`) values('', '$ip', sysdate(),'account not created($reason)')",$mysqli);
		echo $reason;
	}
?>
