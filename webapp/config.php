<?php

$config['db']	= 'orbitrne_repoapp'; 		//	database name
$config['host']	= 'localhost';	//	database host
$config['user']	= 'orbitrne_repoapp';		//	database user
$config['pass']	= 'orbitrne_repoapp';		//	database password

$link = mysql_connect($config['host'], $config['user'], $config['pass']);
if (!$link) {
    die('Not connected : ' . mysql_error());
}
if (! mysql_select_db($config['db']) ) {
    die ('Can\'t use '.$config['host'].' : ' . mysql_error());
}


?>