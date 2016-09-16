<?php
	//import and define variables
	include "/php/sessions.php";
	include "/php/fileManager.php";
	include "/php/menu.php";
	
	$account = new account();
	$menu = new Menu();
	
	//check logged in
	if (!$account->getLoggedIn()){
		header("location:/login.php");
		exit();
	}
	
	/**
	* Show alert box
	* 
	* @param string $text Text that need to be showed in messagebox
	* @param string $log Text that the console write
	* 
	* @return
	*/
	function alert($text, $log){
		//create tage
		echo("<script type='text/javascript'>");
		
		//make possible to use special character without escaping them (< instead of &lt;)
		echo('//<![CDATA[' . "\n");
		
		//show alert
		echo("MessageBox.Show('$text');");
		echo("console.log('$log');");
		
		//close tags
		echo("\n//]]>");
		echo("</script>");
	}
	
	/**
	* Add label with subject and link to new game. Game will be started with javascript.
	* 
	* @param string $subject
	*
	*/
	function addSubject($subject, $account){
		//add label tag with onclick event to javascript	
		echo(sprintf(
			'<label onclick="javascript:startGame(\'%1$s\')">%1$s</label>',
			$subject
		));
		
		if ($account->getRights() != 2){
			echo(sprintf(
				'<label onclick="javascript:editSubject(\'%1$s\')">Wijzigen</label>',
				$subject
			));
			
			echo(sprintf(
				'<label onclick="javascript:deleteSubject(\'%1$s\')">Delete</label>',
				$subject
			));
		}
		
		echo('<br />');		
	}
?>

<html>
<head>
    <meta charset="utf-8" />
    <meta name="author" content="David de Prez" />
    
    <?php $menu->setHeader(); ?>
    <script src="/js/jquery-1.10.2.js"></script>
    <script src="/js/MessageBox.js"></script>
    <script src="/js/list.js"></script>
    
    <link href="/css/jquery-ui-1.10.4.custom.css" rel="stylesheet"/>
    <link href="/css/default.css" rel="stylesheet"/>
    <link href="<?php  echo($account->getStyleSheetPath()); ?>" rel="stylesheet"/>
    <link href="/images/logo.ico" rel="icon"/>
    <title>Lijsten</title>
</head>

<body>
	<?php
		$menu->drawMenu($account);
	?>
	
    <noscript>
        Deze website heeft javascript nodig om te kunnen functioneren.
    </noscript>
    
	<div id="subjects_container">
		<?php			
			$subjects = fileManager::getSubjects($account->getGroup());
			if (gettype($subjects) == "string"){
				alert("Er is een fout opgetreden.", $subjects);
			} elseif (gettype($subjects) == "array") {
				foreach ($subjects as $subject){
					addSubject($subject, $account);
				}
			} else{
				alert("Er is een fout opgetreden", "E30");
			}
		?>
	</div>
</body>
</html>