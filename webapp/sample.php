<?php

   $mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $starttime = $mtime;


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

if($_GET['flag'] == 'health'){

	echo json_encode('OK');

}else{

		ini_set('auto_detect_line_endings',TRUE); 


		$rawcsvdata = file_get_contents('NeedToIndex.csv');
		$precsvdata = array_slice(string_getcsv($rawcsvdata,',','\r'), "1");

		shuffle($precsvdata);

		if(isset($_GET['num'])){ $limit = $_GET['num']; } else { $limit = null; }

		// Start counter and iterate through list of links
		$i=0;
		foreach($precsvdata AS $key=>$value){
			$url = $value[0];
			$title = $value[1];			
			$i++;
	
			if($i == $limit){ break; }

 		}

		$csvready = array_slice($precsvdata, 0, $limit);


		
		$csvjson = json_encode($csvready);

		echo $csvjson;
}

   $mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $endtime = $mtime; 
   $totaltime = ($endtime - $starttime); 
   echo "This page was created in ".$totaltime." seconds";
?>