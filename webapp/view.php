<?php
require_once('config.php');
?>

<a href="index.php">back to app</a></br>

<?php

$id = $_GET['id'];

	$mysql = mysql_query("SELECT * FROM `repoapp` WHERE `index` = $id") or die(mysql_error());
	while($row = mysql_fetch_array($mysql)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
	extract($row);
	
	//echo $json;

	$jsonarray = json_decode($json);

	shuffle($jsonarray);


	if(isset($_GET['limit'])){ $limit = $_GET['limit']; }

	$jsonarray = array_slice($jsonarray, 0, $limit);

	$i=0;

		foreach($jsonarray AS $key=>$value){

		$i++;

		echo "[$i] $value[1] - $value[0]</br>";
	
		}

	}



?>

<a href="index.php">back to app</a></br>