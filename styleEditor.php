<?php
	include "/php/sessions.php";
	include "/php/cssEditor.php";
	
	//validate user
	$account = new account();
	if ($account->getLoggedIn() !== TRUE){
		header("location:login.php?redirect=styleEditor.php");
	}
	
	//only administrator or teacher allowed
	if ($account->getRealRights() != 0 && $account->getRealRights() != 1){
		header("location:list.php");
	}
?>

<!DOCTYPE html>
<html class="fullscreen">
<head>
	<meta charset="utf-8" />
    <meta name="author" content="David de Prez" />

	<script src="js/jquery-1.10.2.js"></script>
	<script src="/js/MessageBox.js"></script>
	<script src="/js/users.js"></script>
	<script src="/js/cssEditor.js"></script>
	
    <link href="css/jquery-ui-1.10.4.custom.css" rel="stylesheet" />
    <link href="/css/default.css" rel="stylesheet"/>
    <link href="<?php  echo($account->getStyleSheetPath()); ?>" rel="stylesheet"/>
	<link rel="icon" href="/images/logo.ico" />
    <title>Style editor</title>
</head>
<body class="fullscreen">
	<noscript>
		Deze website heeft javascript nodig om te kunnen functioneren.
	</noscript>
	
	<div class="exampleScreen">
		<form action="/styleEditor.php" method="post"  class="scrollbar">
			<div>
				<!--Basic info-->
				<select id="slcUsers" name="slcUsers"></select>
				<select id="slcMode" name="slcMode">
					<option value="NORM">Basis weergave</option>
					<option value="ADV">Geavanceerd weergave</option>
				</select>
				<br />
				<input type="button" onclick="javascript:save(0)" value="Save" />
				<input type="button" onclick="javascript:closeEditor()" value="Editor sluiten"/>
			</div>
			<div id="cssFormInput">
				<!--Fields for edit-->
			</div>
		</form>
	</div>
	<!--Example-->
	<div class="exampleScreen" id="objExample"></div>
</body>
</html>