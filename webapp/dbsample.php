<?

   $mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $starttime = $mtime;


	$link = mysql_connect('localhost', '', '');
	if (!$link) {
	    die('Not connected : ' . mysql_error());
	}
	if (! mysql_select_db('') ) {
	    die ('Can\'t use : ' . mysql_error());
	}

	$edit = mysql_query("SELECT * FROM `repoapp` WHERE `index` = '1'") or die(mysql_error());
	while($erow = mysql_fetch_array($edit)){ 
	foreach($erow AS $key => $value) { $erow[$key] = stripslashes($value); } 
	extract($erow);
	
	//echo $json;

	$jsonarray = json_decode($json);

	shuffle($jsonarray);


	if(isset($_GET['limit'])){ $limit = $_GET['limit']; }

	$jsonarray = array_slice($jsonarray, 0, $limit);

	$json = json_encode($jsonarray);

	echo $json;
		

	}

   $mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $endtime = $mtime; 
   $totaltime = ($endtime - $starttime); 
   //echo "<p>This page was created in ".$totaltime." seconds";

?>