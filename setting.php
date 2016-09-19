<?php
	include("php/database.php");
	include("php/sessions.php");
	
	//check is logged in
	$account = new account();
	if (!$account->getLoggedIn()){
		header("location:/login.php?redirect=setting.php");
		exit();
	}
	else{
		
		//check if it is postback or not
		if (isset($_POST['password'], $_POST['password_repeat'])){
			if ($_POST['password'] == ""){
				header("location:list.php");
				exit();
			}
			
			
			//check password same then repeated password
			if ($_POST['password'] !== $_POST['password_repeat']){
				echo ("Wachtwoorden komen niet overeen");
				exit();
			}
			
			//connect to database
			$database = new Database();
			$connection = $database->connect();
			
			//check if connected
			if ($connection === FALSE){
				//error handler for invalid connection
				echo("Er is een fout opgetreden (E1)!");
				exit();
			}
			
			//make querry
			$querry = $connection->prepare("UPDATE `users` SET password=AES_ENCRYPT(?, ?) WHERE id=?");
			if (!$querry->bind_param("sss", $_POST['password'], $database->getEncryptKey(), $account->getId()))
			{
				//error handling for binding error
				$connection->close();
				echo("Er is een fout opgetreden (E2)!");
				exit();
			}
			
			//get result
			if (!$querry->execute()){
				//error handling if execute failed
				$connection->close();
				echo("Er is een fout opgetreden (E3)!");
				exit();
			}
			
			//close connection
			$querry->close();
			$connection->close();
			
			//redirect back to this site
			header("location:list.php");
		}else{
			//get data to fill everything
			//connect to Database
			$database = new Database();
			$connection = $database->connect();
			
			//check if connected
			if ($connection === FALSE){
				//error handler for invalid connection
				echo("Er is een fout opgetreden (E1)!");
				exit();
			}
			
			//make querry
			$querry = $connection->prepare("SELECT `username`, `style` FROM `users` WHERE `id`=?");
			$id = $account->getId();
			$querry->bind_param('i', $id);
			
			//run querry
			if (!$querry->execute()){
				//error handling if execute failed
				$connection->close();
				echo("Er is een fout opgetreden (E3)!");
				exit();
			}
			
			//format result
			$result = $querry->get_result();
			$row = $result->fetch_assoc();
			
			//closing connection
			$querry->close();
			$connection->close();
			?>
			
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="author" content="David de Prez" />

    <link href="css/jquery-ui-1.10.4.custom.css" rel="stylesheet" />
    <link href="/css/default.css" rel="stylesheet"/>
    <link href="<?php  echo($account->getStyleSheetPath()); ?>" rel="stylesheet"/>
	<link rel="icon" href="/images/logo.ico" />
    <title>Instellingen</title>
</head>
<body>
    <noscript>
        Deze website heeft javascript nodig om te kunnen functioneren.
    </noscript>
	
	<form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<!--Credentials-->
		<fieldset>
			<legend>Inlog gegevens</legend>
			<div class="sort">
				<label>Gebruikersnaam: </label>
				<input type="text" readonly="readonly" size="43" name="username" value="<?php echo $row['username'] == '' ? 'username' : $row['username']; ?>"/>
			</div>
			<div class="sort">
				<label>Wachtwoord: </label>
				<input type="password" name="password" size="43" maxlength="31" value="" placeholder="Oude wachtwoord behouden" />
			</div>
			<div class="sort">
				<label>Herhaal wachtwoord: </label>
				<input type="password" size="43" maxlength="31" name="password_repeat" value="" />
			</div>
		</fieldset>
		
		<!--buttons-->
		<em>Lege en niet gewijzigde velden worden niet opgeslagen</em>
		<br />
		<input type="submit" value="Opslaan" />
	</form>
</body>
</html>
			
			<?php
		}
	}
?>