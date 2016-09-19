<?php
	include "/php/database.php";
	include "/php/sessions.php";
	
	//validate user
	$account = new account();
	if ($account->getLoggedIn() !== TRUE){
		header("location:login.php?redirect=users.php");
	}
	
	//only administrator or teacher allowed
	if ($account->getRights() != 0 && $account->getRights() != 1){
		header("location:list.php");
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
    <meta name="author" content="David de Prez" />

	<script src="js/jquery-1.10.2.js"></script>
	<script src="/js/MessageBox.js"></script>
	<script src="/js/users.js"></script>
	
    <link href="css/jquery-ui-1.10.4.custom.css" rel="stylesheet" />
    <link href="/css/default.css" rel="stylesheet"/>
    <link href="<?php  echo($account->getStyleSheetPath()); ?>" rel="stylesheet"/>
	<link rel="icon" href="/images/logo.ico" />
    <title>Nieuwe gebruiker</title>
</head>
<body>
	<form action="/addUser.php" method="post">
		<!--Credentials-->
		<fieldset>
			<legend>Inlog gegevens</legend>
			<div class="sort">
				<label>Gebruikersnaam: </label>
				<input type="text" size="40" maxlength="20" name="txtUsername" id="txtUsername" value=""/>
			</div>
			<div class="sort">
				<label>Wachtwoord: </label>
				<input type="password" name="txtPassword" id="txtPassword" size="40" maxlength="31" value="" />
			</div>
			<div class="sort">
				<label>Herhaal wachtwoord: </label>
				<input type="password" size="40" maxlength="31" name="txtPassRepeat" id="txtPassRepeat" value="" />
			</div>
		</fieldset>
		
		<!--User info-->
		<fieldset>
			<legend>Gebruikers gegevens</legend>
			<div class="sort">
				<label>Email: </label>
				<input type="email" name="txtEmail" id="txtEmail" size="40" maxlength="39" maxlength="" name=""/>
			</div>
			<div class="sort">
				<label>Rechten: </label>
				<select name="slcRights" id=slcRights>
					<option value="1">Leraar</option>
					<option value="2" selected="selected">Leerling</option>
				</select>
			</div>
		</fieldset>
		
		<!--Buttons-->
		<input value="Opslaan" type="button" name="bntSave" id="bntSave" onclick="javascript:createUser()" />
		<input value="Annuleren" type="button" name="bntCancel" id="bntCancel" onclick="javascript:gotoUrl('/users.php')" />
	</form>
</body>
</html>