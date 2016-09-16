<?php
	include("sessions.php");
	include("database.php");
	$account = new account();
	
	//when user logged in
	if ($account->getLoggedIn()){
		
		//logout
		if (isset($_POST['logout']) && $_POST['logout'] == TRUE ){
			$account->logout();
			echo("OK:2");
			exit();
		}
		
		echo("E8");
		exit();
	}
	
	//when user try to log in
	if (isset($_POST['username'], $_POST['password'])){
		echo($account->login($_POST['username'], $_POST['password']));
		exit();
	}
	echo("E7");
?>