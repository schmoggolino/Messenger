<?php
	/**********************************************
			removes the directory
	*********************************************/

	
	function delete_directory($dirname) {
		echo"deleting directory $dirname<br>";
         if (is_dir($dirname))
           $dir_handle = opendir($dirname);
	 if (!$dir_handle)
	      return false;
	 while($file = readdir($dir_handle)) {
			echo"cycling.. $file<br>";
	       if ($file != "." && $file != "..") {
	            if (!is_dir($dirname."/".$file))
	                 unlink($dirname."/".$file);
	            else
	                 delete_directory($dirname.'/'.$file);
	       }
	 }
	 closedir($dir_handle);
	 rmdir($dirname);
	 return true;
	}
	/**********************************************
		builds new connection if ntot there
		drops all tables and creates them
	*********************************************/
	
	
	function create_user($name, $password, $email){//incomplete
		$mysqli=connect();
		$password=password_hash($password, PASSWORD_DEFAULT);
		$result=sql("select * from msg.users where name='$email'",$mysqli);
		$rows_returned 	= $result->num_rows;
		if($rows_returned>0){
			$user_id=false;//email in use already
		}else{
			$result=sql("select * from msg.users where name='$name'",$mysqli);
			$rows_returned 	= $result->num_rows;
			if($rows_returned>0){
				$user_id=false;//name in use already
			}else{
				sql("insert into users (name, email ) values('$name',   '$email' )",$mysqli);
				$result=sql("select * from users where email='$email'",$mysqli);
				
				$rows_returned 	= $result->num_rows;
				$row = $result->fetch_object();
				$user_id= $row->user_id;
				sql("insert into login (user_id, name, password ) values($user_id,'$name', '$password')",$mysqli);
			}
			
		}
		return $user_id;
		
	}
	
	
	
	/**********************************************
		connects to database
	*********************************************/
	function connect(){
		if(!isset($mysqli)){
			$mysqli = new mysqli("127.0.0.12", "root", "", "msg");
			if ($mysqli->connect_errno) {
				die("Verbindung fehlgeschlagen: " . $mysqli->connect_error." couldnt connect to database");
			}
		}
		return $mysqli;
	}
	
	function weiterleiten($target){
		if(isset($_SESSION["testing"])&&$_SESSION["testing"]=='1'){
			echo "<a href='$target'>weiterleiten $target<a>";
		}else{
			echo "<script language='javascript' type='text/javascript'>";
			echo "window.location.href = '$target';";
			echo "</script>";
		}
		
	}
	/**********************************************
		verifies if the user still has a running session
	*********************************************/
	function check_session(){
		session_start();
		if (isset($_SESSION['userid'])&&$_SESSION['ip']==get_client_ip()){
			return true;
		}else{
			session_destroy();
			$mysqli=connect();
			$ip=get_client_ip();
			sql("insert into msg.logbuch (`user`,`ip_address`, `time`, `type`) values('', '$ip', sysdate(),'check_session failed')",$mysqli);
			weiterleiten('index.php?cs=j');
			return false;
		}
		
	}
	function sql($sql, $mysqli){
		if(isset($_SESSION["testing"])&&$_SESSION["testing"]=='1'){
			echo $sql."<br>";
		}
		
		if(!isset($mysqli)){
			$mysqli=connect();
		}
		if(isset($_SESSION["userid"])){
			$user_id=$_SESSION["userid"];
		}else{$user_id=0;}
		$ip=get_client_ip();
		if(stristr($sql, 'insert into msg.logbuch') === FALSE) {
			$sql2="insert into msg.logbuch(user, ip_address, time, type) values($user_id, '$ip', sysdate(), 'sql: ".addslashes($sql)."')";
			$statement 	= $mysqli->prepare($sql2);
			$statement->execute();
		}
		
		
		
		
		$statement 	= $mysqli->prepare($sql);
		$statement->execute();
		$result 	= $statement->get_result(); 
		return $result;
	}
	function new_chat($user_id, $with){
		$mysqli=connect();
		$sql="select * from msg.chats ch where exists(select null from msg.chats ch2 where ch2.chat_id=ch.chat_id and user_id=$user_id) and exists(select null from msg.chats ch2 where ch2.chat_id=ch.chat_id and user_id=$with)";//binary= case sensitive password
		$statement = $mysqli->prepare($sql);
		//$statement->bind_param('s', $with);
		$statement->execute();
		$result 		= $statement->get_result();
		$rows_returned 	= $result->num_rows;
		if($row = $result->fetch_object()){
			//chat already exists
			$chat_id = $row->chat_id;
			return $chat_id;
		}else{
			//create chat
			$sql="select coalesce(max(chat_id),0) as 'chat_id' from msg.chats";
			$statement 	= $mysqli->prepare($sql);
			$statement->execute();
			$result 	= $statement->get_result();
			$row = $result->fetch_object();
			$chat_id=($row ->chat_id)+1;
			
			$sql="select name from msg.contacts where user_id=$user_id and contact=$with";
			$statement 	= $mysqli->prepare($sql);
			$statement->execute();
			$result 	= $statement->get_result();
			$row = $result->fetch_object();
			$namew=$row ->name;
			
			$sql="select name from msg.contacts where user_id=$with and contact=$user_id";
			$statement 	= $mysqli->prepare($sql);
			$statement->execute();
			$result 	= $statement->get_result();
			$rows_returned 	= $result->num_rows;
			if($rows_returned==0){
				$result=sql("select name from msg.users where user_id=$user_id",$mysqli);
				$row = $result->fetch_object();
				$name=$row->name;
			}else{
				$row = $result->fetch_object();
				$name=$row->name;
			}
			
			
			sql("insert into msg.chats(chat_id, user_id, name) values($chat_id, $user_id, '$namew')",$mysqli);
			sql("insert into msg.chats(chat_id, user_id, name) values($chat_id, $with, '$name')",$mysqli);
			return $chat_id;
		}
	}
	function new_contact($user_id, $contact, $name ){
		//if name=null get name from users table
		$mysqli=connect();
		$result=sql("select * from msg.contacts where user_id=$user_id and contact=$contact",$mysqli);
		$rows_returned 	= $result->num_rows;
		if($rows_returned==0){
			sql("insert into msg.contacts(user_id,contact,name)values($user_id, $contact, (select name from msg.users where user_id=$contact))",$mysqli);
		}
		
	}
	
	function new_message($user_id, $chat_id, $text, $type){
		$mysqli=connect();
		$result=sql("select * from msg.chats where chat_id=$chat_id and user_id=$user_id",$mysqli);
		$rows_returned 	= $result->num_rows;
		if($rows_returned>0){//user und chat gibt es in der kombi
			sql("insert into msg.messages(chat_id, from_user, sent, text, type) values($chat_id, $user_id, sysdate(), '$text', '$type')", $mysqli);
			$result=sql("select max(message_id) as 'message_id' from msg.messages where chat_id=$chat_id and from_user='$user_id'",$mysqli);
			$row = $result->fetch_object();
			$message_id=$row ->message_id;
			
			$result=sql("select * from msg.chats where chat_id=$chat_id",$mysqli);
			while($row =$result->fetch_object()){//für jeden user einen message status inserten
				$participant=$row ->user_id;
				sql("insert into msg.message_status(message_id, user_id, status, pinged)values($message_id, $participant,'sent',false)",$mysqli);
			}
			$mysqli->commit();
			return $message_id;
		}
	}
	function get_client_ip() {
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;	
	}
	
	
?>