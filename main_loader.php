<?php
	session_start();
	$user_id	=addslashes(strip_tags($_GET['u']));
	$userid		=addslashes(strip_tags($_SESSION['userid']));
	if($user_id!=$userid){
		$return="your identity couldnt be verified.";
	}
	elseif (!isset($_SESSION['userid'])){
		$return="your identity couldnt be verified.";
	}else{
		if(!isset($_SESSION["testing"])){
			$_SESSION["testing"]=0;
		}
		include("funktionen.php");
		$testing=$_SESSION["testing"] ?: 0;
		$mysqli=connect();
		$_SESSION["testing"]=0;
		$result=sql("select msg.message_id,
						msg.from_user,
						msg.sent as 'datum',
					   (select count(*) --  anzahl ungesehene nachrichten
							from msg.messages ms2 
							join msg.message_status mst2 on (mst2.message_id=ms2.message_id) 
							where status!= 'seen'
							and from_user!=$user_id
							and ms2.chat_id=ch.chat_id
							and mst2.user_id=$user_id
							and ch.user_id=$user_id
					   )as 'anz_ungelesene',
						case 
							when from_user=$user_id then 
								'seen' 
							else 
								mst.status
						end 'status2',
						 msg.text,
						 msg.type,
						 mst.status,
						 mst.pinged,
						 ch.*
				from msg.chats ch
				join msg.messages msg on(message_id=(select max(message_id) 
														from msg.messages msg2 
															where msg2.chat_id=ch.chat_id
													)
										) 
				join msg.message_status mst on(mst.message_id=msg.message_id 
												and mst.user_id=$user_id
											  )
					where  ch.user_id=$user_id


	",$mysqli);
		$return="";
		while($row = $result->fetch_object()){	//alle chats
			/******************
				get data about the message inside
			******************/
			$chat_id=$row 	->chat_id;
			$name=$row 		->name;
			$anz_ungelesene=$row->anz_ungelesene;
			$status2=$row 	->status2;
			$datum  =$row 	->datum;
			$text   =$row->text;
			$stext=substr($text, 0, 100);
			if($stext!=$text){//wenn der text abgeschnitten wurde kommt ein ... dahinter
				$stext.="...";
			}
			if($status2=='seen'){
				$ungelesen='';
			}else{
				$ungelesen="($anz_ungelesene ungelesene)";
			}
			$return.= "<div class='alert alert-success'><button style=\" width: 17%;\" class=\"btn btn-secondary\" onclick=\"location.href='chat.php?chat_id=$chat_id&name=$name'\">$name $ungelesen</button>$datum | $stext<p></div>";
		}
	}
	if($return!=""){
		echo $return;
	}
	


?>