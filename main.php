<?php
	include('funktionen.php');
	check_session();
	$user_id=$_SESSION['userid'];
	//echo $user_id;
	$mysqli=connect();
	$result=sql("select name from msg.users where user_id=$user_id",$mysqli);
	$row   = $result->fetch_object();	//all chats of this person
	/******************
		get data about the message inside
	******************/
	$as    =$row    ->name;

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
    <title>Messenger</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
  </head>

  <body>

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
            <li class="nav-item active">
              <a class="nav-link" href="#">Home
                <span class="sr-only">(current)</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="about.php">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php">Logout</a>
            </li>
			<li class="nav-item">
              <a class="nav-link" href="user.php?u=<?php echo $user_id; ?>"><?php echo $as; ?></a>
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
      <h1 class="mt-5">Main	</h1>
	  <p>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
<div id="content">
<?php
	/******************
		delete empty chats
	******************/
	$ip=get_client_ip();
	//sql("delete ch.* from msg.chats ch  where not exists(select 1 from msg.messages ms where ms.chat_id=ch.chat_id);",$mysqli);
	//sql("insert into msg.logbuch (`user`,`ip_address`, `time`, `type`) values($user_id, '$ip', sysdate(),'deleting empty chat')",$mysqli);
		
	/******************
		display all chats
	******************/
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
	while($row = $result->fetch_object()){	//all chats of this person
		/******************
			get data about the message inside
		******************/
		$chat_id=$row 	->chat_id;
		$name=$row 		->name;
		$anz_ungelesene=$row->anz_ungelesene;
		$status2=$row 	->status2;
		$datum  =$row 	->datum;
		$text   =$row 	->text;
		$stext=substr($text, 0, 100);
		if($stext!=$text){//wenn der text abgeschnitten wurde kommt ein ... dahinter
			$stext.="...";
		}
		if($status2=='seen'){
			$ungelesen='';
		}else{
			$ungelesen="($anz_ungelesene new)";
		}
		echo "<div class='alert alert-success'><button style=\" width: 17%;\" class=\"btn btn-secondary\" onclick=\"location.href='chat.php?chat_id=$chat_id&name=$name'\">$name $ungelesen</button>$datum | $stext<p></div>";
		//tests how to display elseway
		//echo"<a href='chat.php?chat_id=$chat_id&name=$name' class='alert alert-success' style='width:100%' >$name $ungelesen</a>";
	}
	$rows_returned 	= $result->num_rows;
	if ($rows_returned==0){
		echo'<span style="margin-top:0.5em;">Start a chat by clicking on the button in the bottom left corner</span>';
	}
?>
</div>

<script type="text/javascript" src="jquery.js"></script>
<script charset="UTF-8">
	var container_content="";
	var highest_message=0;
	var msg_id='';
	(function($)
	{
		$(document).ready(function()
		{
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
				
				$.get("main_loader.php?u=<?php echo$user_id;?>", function(data){
					//container_content=data;
					if(data!=null && data !=""){
						$container.html(data);
					}
					
					
					
					
				},"text");
			}, 5000);
		});
			
		
		
	})(jQuery);
</SCRIPT>
	
	
<div style="position:absolute; float:right; bottom:0;" class="btn-group dropup">
  <button  style="background: url(site_media/drpdwn.jpg)" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    kjnn
  </button>
  <div class="dropdown-menu">
	<a href="new_chat.php" 		class="dropdown-item">new chat</a>
	<!--<a href="not_developed.php" class="dropdown-item">create group chat</a>-->
	<a href="contacts.php" class="dropdown-item">Contacts</a>
  </div>
</div>	
</div>
