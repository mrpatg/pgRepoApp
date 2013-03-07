<?php
require_once('config.php');

if($_GET['flag'] == 'health'){

	echo json_encode('OK');

}else{

	$mysql = mysql_query("SELECT * FROM `repoapp` ORDER BY `index` DESC") or die(mysql_error());
	while($row = mysql_fetch_array($mysql)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
	extract($row);
	
	//echo $json;

	$jsonarray = json_decode($json);

	shuffle($jsonarray);


	if(isset($_GET['limit'])){ $limit = $_GET['limit']; }

	$jsonarray = array_slice($jsonarray, 0, $limit);

	$json = json_encode($jsonarray);

	echo $json;
		

	}


}
?>