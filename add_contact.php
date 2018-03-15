<?php
	include('funktionen.php');
	check_session();
	$user_id=$_SESSION["userid"];
	if(isset($_POST["contact"])){
		$mysqli=connect();
		$name=addslashes(strip_tags($_POST["contact"]));
		$result=sql("select * from msg.users where name='$name'",$mysqli);
		if($row = $result->fetch_object()){
			$contact=$row->user_id;
			new_contact($user_id,$contact,'name not given -> functionality missing');
			sql("insert into msg.logbuch (`user`,`ip_address`, `time`, `type`) values($user_id, '$ip', sysdate(),'new contact')",$mysqli);
			weiterleiten('main.php');
		}else{
			weiterleiten("user_search.php?m=u_not_found");
		}
		
	}else{
		weiterleiten('index.php');
	}


?>
