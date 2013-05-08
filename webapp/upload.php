<?php
session_start();

require_once('functions.php');


if($_SESSION['auth'] == TRUE){

	$csvfile = $_FILES['csv']['tmp_name'];
	
	$upload = upload_csv($csvfile);
	
	if($upload){

		header('Location: index.php?msg=success');

	}else{

		header('Location: index.php?msg=fail');	

	}

}else{

	header('Location: index.php');

}

?>