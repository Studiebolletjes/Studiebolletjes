<?php
	include("/php/sessions.php");
	include("/php/menu.php");
	
	$account = new account();
	$menu = new Menu();
	
	if ($account->getLoggedIn()){
		if (isset($_GET['redirect'])){
			header("location:/" . $_GET['redirect']);
		} else{
			header("location:/list.php");
		}
	}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

    <meta charset="utf-8" />
    <meta name="author" content="David de Prez" />

	<?php $menu->setHeader(); ?>
    <script src="js/jquery-1.10.2.js"></script>
    <script src="/js/MessageBox.js"></script>
    <script src="js/login.js"></script>

    <link href="css/jquery-ui-1.10.4.custom.css" rel="stylesheet" />
    <link href="/css/default.css" rel="stylesheet"/>
    <link href="<?php echo($account->getStyleSheetPath()); ?>" rel="stylesheet"/>
    <link rel="icon" href="/images/logo.ico" />

    <title>Inloggen</title>
</head>

<body>
	<?php
		$menu->drawMenu($account);
	?>
	
    <noscript>
        Deze website heeft javascript nodig om te kunnen functioneren.
    </noscript>
    
    <form action="/login.php" method="post">
        <fieldset>
            <div>
                <label>Gebruikernaam: </label>
                <input type="text" id="username" />
            </div>
            <div>
                <label>Wachtwoord: </label>
                <input type="password" id="password" />
            </div>
            <button type="button" id="bntLogin">Inloggen</button>
        </fieldset>
    </form>
</body>
</html>