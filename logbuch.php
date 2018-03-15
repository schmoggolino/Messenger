<link rel="stylesheet" href="stylesheet.css" >
<table style="width:100%;">
<tr>
<th>eintrag_id</th>
<th>user</th>
<th>ip_address</th>
<th>time</th>
<th>type</th>
</tr>
<?php
	include("funktionen.php");
	check_session();
	$user_id=$_SESSION["userid"];
	
	if ($user_id==1){
		$mysqli=connect();
		$result=sql("select * from msg.logbuch order by eintrag_id desc",$mysqli);
		while($row = $result->fetch_object()){
			echo "<tr>";
			echo'<td>'.$row->eintrag_id.'</td>';
			echo'<td>'.$row->user.'</td>';
			echo'<td>'.$row->ip_address.'</td>';
			echo'<td>'.$row->time.'</td>';
			echo'<td>'.$row->type.'</td>';
			
			echo "</tr>";
		}
		
		
		
	}


?>
</table>