<?php
	include('funktionen.php');
	$mysqli=connect();


	$q = addslashes(strip_tags($_REQUEST["q"]));
	$result=sql("select name from msg.users where name like '%$q%' or email like '%$q%'",$mysqli);
	$i=0;
	$a[]=null;
	while($row = $result->fetch_object()){
		$a[]=$row->name;
	}
	$hint = "";

	// lookup all hints from array if $q is different from ""
	if ($q !== "") {
		$q = strtolower($q);
		$len=strlen($q);
		foreach($a as $name) {
			if (stristr($q, substr($name, 0, $len))) {
				if ($hint === "") {
					$hint = $name;
				} else {
					$hint .= ", $name";
				}
			}
		}
	}

	// Output "no suggestion" if no hint was found or output correct values
	echo $hint === "" ? "" : "Do you mean '".$hint."'?";
?> 