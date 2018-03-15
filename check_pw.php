<?php
	//password_hash("rasmuslerdorf", PASSWORD_DEFAULT)
	include('funktionen.php');
	session_start();
	/******************
		variablen einholen
	******************/
	$mysqli=connect();
	$user=$_POST["user"];
	$password=$_POST["password"];
	/******************
		validate userdata
	******************/
	$user=		addslashes(strip_tags($user));
	$password=	addslashes(strip_tags($password));
	echo'loading';
	/******************
		CHECK ip flood
	******************/
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
		$result=sql("select * from msg.login where name='$user'",$mysqli);//binary= case sensitive password
		$rows_returned 	= $result->num_rows;
		if($rows_returned>0){
			$row = $result->fetch_object();
			$DBPW= $row	  ->password;
			$user_id=$row ->user_id;
			/******************
				verify password
			******************/
			if ($user !== false && password_verify($password, $DBPW)) {
				$_SESSION['userid'] = $user_id;
				$_SESSION['ip']=$ip;
				sql("insert into msg.logbuch (`user`,`ip_address`, `time`, `type`) values($user_id, '$ip', sysdate(),'log in')",$mysqli);
				
				if(isset($_GET['fwd'])){
					weiterleiten('/'.addslashes(strip_tags($_GET['fwd'])));
				}else{
					weiterleiten('main.php');
				}
				
			} else {
				echo "User oder Passwort war ungültig<br>";
				weiterleiten('index.php?failed=j');
			}
		}else{
			echo "User oder Passwort war ungültig<br>";
			weiterleiten('index.php?failed=j');
		}
	}
?>