<?php
require_once('config.php');

//This is a function workaround for str_getcsv, which is included with latest PHP.
function string_getcsv($input, $delimiter=',', $enclosure='"', $escape=null, $eol=null) { 
  $temp=fopen("php://memory", "rw"); 
  fwrite($temp, $input); 
  fseek($temp, 0); 
  $r = array(); 
  while (($data = fgetcsv($temp, 4096, $delimiter, $enclosure)) !== false) { 
    $r[] = $data; 
  } 
  fclose($temp); 
  return $r; 
} 

function upload_csv($csvfile){

		ini_set('auto_detect_line_endings',TRUE); 


		$rawcsvdata = file_get_contents($csvfile);
		$precsvdata = array_slice(string_getcsv($rawcsvdata,',','\r'), "1");

		$jsondata = json_encode($precsvdata);

		

		if (!mysql_query("INSERT INTO repoapp (`json`) VALUES ('$jsondata')"))
		  {
		  	return 'Error: ' . mysql_error();
		  }
			return TRUE;


}

function show_repo(){

	$mysql = mysql_query("SELECT * FROM `repoapp` ORDER BY `index` DESC") or die(mysql_error());
	while($row = mysql_fetch_array($mysql)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
		extract($row);

		$jsonarray = json_decode($json);

		$thecount = count($jsonarray);
		
		echo '<li>'.$date.' ('.$thecount.' links) - <a href="view.php?id='.$index.'"><span class="label success">View</span></a> <a href="delete.php?rowid='.$index.'" onclick="javascript:return confirm(\'Are you sure you want to delete this entry ?\')"><span class="label important">Delete</span></a></li>';

	}


}

function total_link_count(){

	$i=0;

	$mysql = mysql_query("SELECT * FROM `repoapp` ORDER BY `index` DESC") or die(mysql_error());
	while($row = mysql_fetch_array($mysql)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
		extract($row);

		$jsonarray = json_decode($json);

		$thecount = count($jsonarray);

		$i = $i+$thecount;

	}
	
	echo "$i";

}

function delete_row($rowid){

		if (!mysql_query("DELETE FROM `repoapp` WHERE `index` = $rowid"))
		  {
		  	return 'Error: ' . mysql_error();
		  }
			return TRUE;


}

?>