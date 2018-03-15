
 <?php
	
	session_start();
	
	$chat_id	=addslashes(strip_tags($_GET['c']));
	$message_id	=addslashes(strip_tags($_GET['m']));
	$userid		=addslashes(strip_tags($_SESSION['userid']));
	$highest_m	=addslashes(strip_tags($_GET['h_m']));
	
	$first=true;
	$i=0;
	$testing=$_SESSION["testing"] ?: 0;
	if (!isset($_SESSION['userid'])){
		$return="your identity couldnt be verified.";
	}else{
		include('funktionen.php');
		$mysqli=connect();
		$_SESSION["testing"]=0;
		$result=sql2("select * 	
						from msg.messages ms 
						join msg.chats ch on(ch.chat_id=ms.chat_id)
						join msg.message_status mst on(mst.message_id=ms.message_id)
						 where ms.chat_id=$chat_id 
						 and exists(select 1 
									from msg.chats ch2 
									 where 	ch2.chat_id=ch.chat_id 
									  and 	ch2.user_id=$userid
									)
						  and mst.user_id=$userid
						  and ch.user_id=$userid
						  and ms.message_id>$message_id
						  and ms.message_id>$highest_m
						  "
						  
						  ,$mysqli);
		$return="";
		while($row = $result->fetch_object()){	//alle nachrichten im chat
			
			/******************
				get data about the message
			******************/
			$message_id	=utf8_encode($row->message_id);		
			$status    	=utf8_encode($row->status);
			$text		=$row->text;
			$from		=utf8_encode($row->from_user);
			$type		=utf8_encode($row->type);
			$sent		=utf8_encode($row->sent);
			/*****************
				processing & visualizing
			******************/

			
			
			if($status!=='seen'){//ungesehene nachrichten müssen hervorgehoben werden!
				sql("update msg.message_status set status='seen' , pinged=true where message_id=$message_id and user_id=$userid",$mysqli);
			}
			if($from==$userid){
				$result2=sql("select * from msg.message_status where message_id=$message_id and user_id!=$userid",$mysqli);
				$row2 = $result2->fetch_object();
				$status=$row2   ->status;
			}
			$return.="$message_id<attribute>$status<attribute>$text<attribute>$from<attribute>$type<attribute>$sent<msg>";
			
			$first=false;
			$i=$i+1;
		}
		$_SESSION["testing"]=$testing;
	}
		
		echo $return;
	function sql2($sql, $mysqli){		
		$statement 	= $mysqli->prepare($sql);
		$statement->execute();
		$result 	= $statement->get_result(); 
		return $result;
	}
?> 