<?php
	include "/php/database.php";
	include "/php/sessions.php";
	include "/php/menu.php";
	
	//validate user
	$account = new account();
	if ($account->getLoggedIn() !== TRUE){
		header("location:login.php?redirect=users.php");
	}
	
	//only administrator or teacher allowed
	if ($account->getRights() != 0 && $account->getRights() != 1){
		header("location:list.php");
	}
	
	$menu = new Menu();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
    <meta name="author" content="David de Prez" />

	<script src="js/jquery-1.10.2.js"></script>
	<?php $menu->setHeader(); ?>
	<script src="/js/MessageBox.js"></script>
	<script src="/js/users.js"></script>
	
    <link href="css/jquery-ui-1.10.4.custom.css" rel="stylesheet" />
    <link href="/css/default.css" rel="stylesheet"/>
    <link href="<?php  echo($account->getStyleSheetPath()); ?>" rel="stylesheet"/>
	<link rel="icon" href="/images/logo.ico" />
    <title>Gebruikers</title>
</head>
<body>
	<?php $menu->drawMenu($account) ?>
	<noscript>
		Deze website heeft javascript nodig om te kunnen functioneren.
	</noscript>

	<form action="/users.php" method="post">
		<fieldset>
			<input type="button" name="bntNewUser" id="bntNewUser" value="Toevoegen" onclick="javascript:gotoUrl('/addUser.php')" />
		</fieldset>	
		<fieldset>
			<!--List of all teachers, added by javascript-->
			<legend>Leraren</legend>
			<div id="divTeachers">
			</div>
		</fieldset>
		<fieldset>
			<!--List of all students, added by javascript-->
			<legend>Leerlingen</legend>
			<div id="divStudents">
			</div>
		</fieldset>
	</form>
	
	<!--Request all users when page is loading-->
	<script>getUsers("Teachers", drawTeachers); getUsers("Students", drawStudents); </script>
</body>
</html>