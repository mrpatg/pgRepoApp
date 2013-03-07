<?php
session_start();

require_once('functions.php');


if($_SESSION['auth'] == TRUE){

	$rowid = $_GET[rowid];
	
	$deltree = delete_row($rowid);
	
	if($deltree){

		header('Location: index.php?msg=delsuccess');

	}else{

		header('Location: index.php?msg=delfail');	

	}

}else{

	header('Location: index.php');

}

?>