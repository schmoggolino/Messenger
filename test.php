<?php
	session_start();
	include('funktionen.php');
	if($_SESSION["userid"]==1){
		rebuild_database();
	}else{
		echo"login as admin please"; 
	}
	
	function rebuild_database(){
		echo"recreating tables. lets hope this works<br>";
				$servername = "127.0.0.12";
				$username = "root";
				$password = "";
				$mysqli = new mysqli($servername, $username, $password, "msg");
				
				if ($mysqli->connect_errno) { 
					
					
					// Create connection
					$conn = new mysqli($servername, $username, $password);
					// Check connection
					if ($conn->connect_error) {
						die("Connection failed: " . $conn->connect_error);
					}
					
					// Create database
					$sql = "CREATE DATABASE msg CHARACTER SET utf8 COLLATE utf8_general_ci;";
					if ($conn->query($sql) === TRUE) {
							echo "<strong>Database created successfully</strong>";
					} else {
						echo "Error creating database: " . $conn->error;
					}
					$mysqli = new mysqli($servername, $username, $password, "msg");
				}
			
			sql("DROP TABLE if exists msg.login", $mysqli); 			//login
			sql("CREATE TABLE `msg`.`login` (
			`user_id` int not null,
			`name` VARCHAR(200) NOT NULL,
			`password` VARCHAR(200) NOT NULL,
			PRIMARY KEY (`user_id`));", $mysqli);
			echo "<strong>created tbale: login</strong><br>";
			sql("DROP TABLE if exists msg.users", $mysqli);				//users
			sql("CREATE TABLE msg.users (
					user_id int not null auto_increment PRIMARY KEY,
					name VARCHAR(200) NOT NULL,
					picture VARCHAR(200),
					last_seen date,
					email VARCHAR(200)
				
				)",$mysqli);
			echo "<strong>created table: users</strong><br>";
			sql("DROP TABLE if exists msg.session;",$mysqli);			//session
			sql("CREATE TABLE msg.session (
					session varchar(2000) not null,
					c_date date NOT NULL)",$mysqli);
			echo "<strong>created table: session</strong><br>";
			sql("DROP TABLE if exists msg.login_attempts;",$mysqli);	//login_attempts
			sql("CREATE TABLE msg.login_attempts (						
					ip_adress 	varchar(256) not null,
					c_date 		timestamp NOT NULL)",$mysqli);
			echo "<strong>created table: login_attempts</strong><br>";
			sql("DROP TABLE if exists msg.chats;",$mysqli);				//chats
			sql("CREATE TABLE msg.chats (
					chat_id 	int,
					user_id 	int,
                    admin  boolean,
                    group_chat boolean,
                    name varchar(256)
                    )",$mysqli);
			echo "<strong>created table: chats</strong><br>";
			sql("DROP TABLE if exists msg.messages;",$mysqli);			//messages
			sql("CREATE TABLE msg.messages (
					chat_id 	int ,
					message_id 	int not null AUTO_INCREMENT,
                    from_user  int,
                    sent timestamp,
                    text varchar(2000),
					type varchar(256),
                    PRIMARY KEY(message_id)
                    )",$mysqli);
			echo "<strong>created table: messages</strong><br>";
			sql("DROP TABLE if exists msg.message_status;",$mysqli);	//message_status
			sql("CREATE TABLE msg.message_status (
					message_id 	int ,
                    user_id  int,
                    status varchar(256),
                    pinged boolean
                    )",$mysqli);
			echo "<strong>created table: message_status</strong><br>";
			sql("DROP TABLE if exists msg.media;",$mysqli);				//media
			sql("CREATE TABLE msg.media (
					chat_id 	int ,
                    media_id  int not null AUTO_INCREMENT,
                    PRIMARY KEY(media_id)
                    )",$mysqli);
			echo "<strong>created table: contacts</strong><br>";
			sql("DROP TABLE if exists msg.contacts;",$mysqli);			//contacts
			sql("CREATE TABLE msg.contacts (
					user_id 	int ,
                    contact  int,
					name varchar(256)
                    )",$mysqli);
			echo "<strong>created table: media</strong><br>";
			sql("DROP TABLE if exists msg.blocklist;",$mysqli);			//blocklist
			sql("CREATE TABLE msg.blocklist (
					ip 	varchar(256) ,
                    reason  varchar(256)
                    )",$mysqli);
			echo "<strong>created table: blocklist</strong><br>";
			sql("DROP TABLE if exists msg.logbuch;",$mysqli);	
			sql("CREATE TABLE msg.logbuch (
					eintrag_id 	int not null AUTO_INCREMENT,
                    user  int,
                    ip_address varchar(100),
                    `time` timestamp,
                    `type` varchar(4000),
                    PRIMARY KEY(eintrag_id)
                    )",$mysqli);
			echo "<strong>created table: logbuch</strong><br>";		
			echo "<strong>creating admin</strong><br>";
			$us   ='admin';
			$pw   ='der admin ist das wahre alphatier';
			$email='admin@messenger.de';
			$mysqli=connect();
			create_user($us,$pw,$email);
			echo"created user ($us, $pw)<br>";
			$mysqli->commit();
			
			
	}


?>