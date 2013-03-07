<?php
session_start();

if($_POST['submitted'] == "TRUE"){

require('pg_repo_app_credentials.inc.php');

	if($_POST['username'] == $pg_repo_app_username && $_POST['password'] == $pg_repo_app_password){
	
		$_SESSION['auth'] = TRUE;
	
	}else{

		$_SESSION['autherr'] = TRUE;

	}

}

header('Location: index.php');

?>