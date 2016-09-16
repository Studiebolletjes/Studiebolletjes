<?php
	//import and define variables
	include("/php/sessions.php");
	
	$account = new account();
	
	//check logged in
	if (!$account->getLoggedIn()){
		header("location:/login.php?redirect=new.php");
		exit();
	} elseif ($account->getRights() == 2){
		header("location:/list.php");
		exit();
	}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="author" content="David de Prez" />
    <meta name="description" content="Woorden toevoegen" />

    <script src="js/jquery-1.10.2.js"></script>
    <script src="/js/MessageBox.js"></script>
    <script src="/js/upload.js"></script>
    <script src="/js/new.js"></script>

    <link href="css/jquery-ui-1.10.4.custom.css" rel="stylesheet" />
    <link href="/css/default.css" rel="stylesheet"/>
    <link href="<?php  echo($account->getStyleSheetPath()); ?>" rel="stylesheet"/>
    <link rel="icon" href="/images/logo.ico" />

    <title>Nieuwe lijst</title>
</head>

<body>
    <noscript>
        Deze website heeft javascript nodig om te kunnen functioneren.
    </noscript>
    
    <form action="/new.php" method="post">
    	<fieldset>
    		<!--Layout basic info-->
    		<legend>Basis</legend>
    		<input type="text" placeholder="Lijst naam" id="txtSubjectName" name="txtSubjectName" maxlength="15" size="16"/>
    		<input type="button" value="Opslaan" id="bntSave" name="bntSave" />
    		<input type="button" value="Annuleren" id="bntCancel" name="bntCancel"/>
    		<label id="process"></label>
    	</fieldset>
    	<fieldset>
    		<!--Layout rows-->
    		<legend>Woorden</legend>
    		<table id="rowContainer">
    			<!--Container headers-->
    			<tr>
    				<th>Id</th>
    				<th>Vraag</th>
    				<th>Bijlage</th>
    				<th>Antwoord</th>
    				<th>Meerkeuze antwoorden</th>
    			</tr>
    			
    			<!--Javascript generated rows-->
    		</table>
    		<input type="button" value="Rij toevoegen" id="bntAddRow" onclick="javascript:addRow();"/>
    	</fieldset>
    </form>
</body>
</html>