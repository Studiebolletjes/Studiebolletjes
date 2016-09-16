<?php
	//import and define variables
	include "/php/sessions.php";
	$account = new account();
	
	//check logged in
	if (!$account->getLoggedIn()){
		header("location:/login.php");
		exit();
	}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="author" content="David de Prez" />
    
    <script src="/js/jquery-1.10.2.js"></script>
    <script src="/js/MessageBox.js"></script>
    <script src="/js/media.js"></script>
    <script src="/js/game_1.js"></script>
    
    <link href="/css/jquery-ui-1.10.4.custom.css" rel="stylesheet"/>
    <link href="/css/default.css" rel="stylesheet"/>
    <link href="<?php echo($account->getStyleSheetPath()); ?>" rel="stylesheet"/>
    <link href="/images/logo.ico" rel="icon"/>
    <title>Spelen</title>
</head>

<body>
    <noscript>
        Deze website heeft javascript nodig om te kunnen functioneren.
    </noscript>
    
	<div id="score_container">
		<label>Score: </label>
		<label id="lblScore">0</label>
		<label> Aantal fout: </label>
		<label id="lblWrong">0</label>
	</div>
	
	<div id="game_container">
		<label id="lblQuestion">{vraag}</label>
		<input type="text" name="txtAnswer" id="answer"/>
		<button type="button" id="bntNext" value="next">Controleer</button>
		<label id="lblAnswer">{answer}</label>
	</div>
	<div id="mediaBox">
		<!--
		here come the media like an image, video, ...
		-->
	</div>
</body>

</html>