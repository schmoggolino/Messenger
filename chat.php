<?php
	session_start();
	include('funktionen.php');
	check_session();
	$user_id=$_SESSION['userid'];
	//echo $user_id;
	$mysqli=connect();
	$result=sql("select name from msg.users where user_id=$user_id",$mysqli);
	$row = $result->fetch_object();	//all chats of this person
	/******************
		get data about the message inside
	******************/
	$as=$row 		->name;

?>
<!DOCTYPE html>
<html lang="en">
<!--------
	BOOTSTRAP TEMPLATE 
--------->
  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
	
	<link rel="icon" href="site_media/chat.ico">
    <title><?php echo addslashes(strip_tags($_GET["name"]))?></title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
  </head>

  <body onload="scroll();">

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <div class="container">
        <a class="navbar-brand" href="#">
          <img src="http://placehold.it/300x60?text=Logo" width="150" height="30" alt="">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link" href="main.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="about.php">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php">Logout</a>
            </li>
			<li class="nav-item ">
              <a class="nav-link" href="user.php?u=<?php echo $user_id; ?>"><?php echo $as; ?>
				
			  </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/popper/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--------
	BOOTSTRAP TEMPLATE 
--------->
    <!-- Page Content -->
    <div class="container">
      <h1 class="mt-5">Chat with <?php echo addslashes(strip_tags($_GET["name"])) ?></h1><?php
	$go;
	/******************
		get variables
	******************/
	$userid=$_SESSION['userid'];
	$chat_id=$_GET['chat_id'];
	/******************
		dispose userdata
	******************/
	$userid =addslashes(strip_tags($userid));
	$chat_id=addslashes(strip_tags($chat_id));
	/******************
		Check user-rights
	******************/
	$mysqli=connect();
	$result=sql("select 1 from msg.chats where user_id=$userid and chat_id=$chat_id",$mysqli);
	$rows_returned 	= $result->num_rows;
	if($rows_returned==0){//er ist nicht teil des chats, darf ihn also nicht öffnen
		weiterleiten('main.php');
	}
	/*****************
		load chat
	******************/
	$result=sql("select * 	
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
				",$mysqli);
	$rows_returned 	= $result->num_rows;
?>
	
<div id="result">
<?php
	$message_id=0;//falls er keine fi ndet ist die msg_id=0;
	while($row = $result->fetch_object()){	//alle nachrichten im chat
		/******************
			get data about the message
		******************/
		$message_id=$row 	->message_id;
		$status=$row 		->status;
		$text=$row 			->text;
		$from=$row			->from_user;
		$sent=$row			->sent;
		/*****************
			processing & visualizing
		******************/
		if($status!=='seen'){//ungesehene nachrichten müssen hervorgehoben werden!
			sql("update msg.message_status set status='seen' , pinged=true where message_id=$message_id and user_id=$userid",$mysqli);
			
		}
		if($from==$user_id){
			$result2=sql("select * from msg.message_status where message_id=$message_id and user_id!=$user_id",$mysqli);
			$row2 = $result2->fetch_object();
			$status=$row2   ->status;
		}
		show_msg($text,$from,$status,$sent) ;
	}
	$param="chat_id=$chat_id&nm=1";
	
	function show_msg($text,$from,$status,$sent){
		if($from==$_SESSION['userid']){
		 echo'<div style="width: 200px; padding-top: 0; padding-bottom: 0; " class="alert alert-success">
			<span style="margin-bottom: 0" class="text-left">
				'.$text.'
			</span>
			<p style="margin-top:0;margin-bottom:0;" class="text-right text-top font-italic">
				<small>sent '.$sent." ".$status.'</small>
			</p>
		</div>';	
			
		}else{//nachricht von wem anders
			echo'<div  class="alert alert-warning" style="width: 200px; padding-top: 0; padding-bottom: 0; ">
			<span style="margin-bottom: 0" class="text-left">
				'.$text.'
			</span>
			<p style="margin-top:0;margin-bottom:0;" class="text-right text-top font-italic">
				<small>sent '.$sent.'</small>
			</p>
		</div><p>';
		
		}
		
	}
?>


<script type="text/javascript" src="jquery.js"></script>
<script charset="UTF-8">
	function scroll(){
		window.scrollTo(0,document.body.scrollHeight);
	}
	
	var container_content="";
	var highest_message=0;
	var msg_id='';
	(function($)
	{
		
			$(document).ready(function()
			{
				$("#message").keypress(function (e) {
					if(e.which==13){
						send_msg();
					}
				});
				$.ajaxSetup(
				{
					cache: false,
					beforeSend: function() {
						//$('#content').hide();
						$('#loading').show();
					},
					complete: function() {
						//$('#loading').hide();
						$('#content').show();
					},
					success: function() {
						//$('#loading').hide();
						$('#content').show();
					}
				});
				var $container = $("#content");
				var refreshId = setInterval(function()
				{
					
					$.get("demo_sse.php?c=<?php echo$chat_id;?>&m=<?php echo $message_id; ?>&h_m="+highest_message, function(data){
						
						if(data.search("<msg>">(-1))){
							var dauer_load="";
							var messages = data.split('<msg>');
							for (var i = 0; i < messages.length-1; i++) {
								messages[i] = messages[i].split('<attribute>');
								var message_id=messages[i][0].trim();
								var status    =messages[i][1].trim();
								var text      =messages[i][2].trim();
								var cfrom     =messages[i][3].trim();
								var type      =messages[i][4].trim();
								var sent      =messages[i][5].trim();
								if(status=="seen"){
									highest_message=message_id;
									if(cfrom!=<?php echo $user_id; ?>){
										container_content=container_content+foreign_msg(text,sent);
									}else{
										container_content=container_content+my_msg(text,sent,status);
									}
								}else{
									if(cfrom!=<?php echo $user_id; ?>){
										dauer_load=dauer_load+foreign_msg(text,sent);
									}else{
										dauer_load=dauer_load+my_msg(text,sent,status);
									}
								}
									
									
									
								
								
							}
						}
						/*if(container_content.length()>2000){
							//you should consider refreshing the site
							//force refresh?
						}*/
						$container.html(container_content+dauer_load);
					},"text");
				}, 5000);
			});
			
		
		
	})(jQuery);
	

	


	function send_msg()
	{
		// wert aus dem input feld mit dem namen "message" auslesen
		var message = $('input[name=message]').val();
		if (message!=""){
			// phpscript.php aufrufen über POST aufrufen
			// entspricht: phpscript.php?a=callDoAnything&b=[Inhalt von message]
			$.post("send_msg.php?c=<?php echo $chat_id; ?>",
			{
				a: 'callDoAnything',
				b: message
			},
			function (data) {
				$('input[name=message]').val('')
			});
		}
		
	}
	function my_msg(text,time,status){
		return "<div style='width: 200px; padding-top: 0; padding-bottom: 0; align: left' class='alert alert-success'><span style='margin-bottom: 0' class='text-left'>"+
		text+
		"</span><p style='margin-top: 0; margin-bottom: 0'class='text-right text-top font-italic'><small>sent "+time+" "+status+"</small></p></div><p>";
		
	}
	function foreign_msg(text,time){
		return "<div style='width: 200px; padding-top: 0; padding-bottom: 0; align: left' class='alert alert-warning'><span style='margin-bottom: 0' class='text-left'>"+
		text+
		"</span><p style='margin-top: 0; margin-bottom: 0'class='text-right text-top font-italic'><small>sent "+time+"</small></p></div><p>";
	}
</script>
<div id="content"></div>
</div>	 	
	<input type ="Text" name="message" id="message"  placeholder="new Message" >
	<input type="button" id="snd" class="btn btn-secondary" value="Send" onclick="send_msg()" />
</div>
</body>