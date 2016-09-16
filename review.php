<?php
	include("/php/sessions.php");
	include("/php/fileManager.php");
	//include("/php/database.php"); this already include in filemanager

	//check logged in
	$account = new account();
	if (!$account->getLoggedIn()){
		header("location:/login.php");
		exit();
	}
	
	//get review data
	$filemanage = new fileManager();
	$review = $filemanage->getReview();
	
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
		exit();
	}
	/**
	* Add an row in html format
	* 
	* @param array $row Result of row from database after fetch_assoc
	*/
	function addRow($row, $answer){
		echo("<tr>");
		
		echo(sprintf("<td>%s</td>",$row['question']));
		echo(sprintf("<td>%s</td>",$answer));
		echo(sprintf("<td>%s</td>",$row['multi_1']));
		
		echo("</tr>");
	}	
	/**
	* Draw all rows in html format.
	* 
	* @param array $review Review data. Get from filemanager
	* @param int $group The group where all questions come from
	*/
	function drawRows($review, $group){
		//connecting to database
		$database = new Database();
		$connection = $database->connect();
		if ($connection === FALSE){
			alert("Er is een fout opgetreden", "E1");
		}
		
		//get id of first wrong word and the group
		$id=0;
		
		//making querry
		$querry = $connection->prepare("SELECT * FROM `g$group` WHERE id=?");
		if (!$querry->bind_param("i", $id)){
			$connection->close();
			alert("Er is een fout opgetreden", "E2");
		}
		
		foreach ($review["words"] as $key => $value){
			$id=$value['id'];
			
			//execute querry
			if (!$querry->execute()){
				$connection->close();
				alert("Er is een fout opgetreden", "E3");
			}
			
			//get result in array
			$result = $querry->get_result();
			if (!$row = $result->fetch_assoc()){
				$querry->close();
				$connection->close();
				alert("E41");
			}
			
			//adding row
			addRow($row, $value['answer']);
		}
	}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

    <meta charset="utf-8" />
    <meta name="author" content="David de Prez" />

    <script src="js/jquery-1.10.2.js"></script>
    <script src="/js/MessageBox.js"></script>

    <link href="css/jquery-ui-1.10.4.custom.css" rel="stylesheet" />
    <link href="/css/default.css" rel="stylesheet"/>
    <link href="<?php  echo($account->getStyleSheetPath()); ?>" rel="stylesheet"/>
    <link rel="icon" href="/images/logo.ico" />

    <title>Uitslag</title>
</head>

<body>
	<form>
		<fieldset>
			<div>
				<label>Lijst: </label>
				<label><?php echo($review['subject']); ?></label>
			</div>
			<div>
				<label>Aantal fout: </label>
				<label><?php echo(count($review["words"])); ?></label>
			</div>
			<div>
				<label>Score: </label>
				<label><?php echo($review['score']); ?></label>
			</div>
			<div>
				<a href="/list.php">Alle lijsten</a>
			</div>
		</fieldset>
		<fieldset>
		<?php		
			//check if there are wrong words
			if (count($review["words"]) != 0){
		?>
		<table>
			<tr>
				<th>Vraag</th>
				<th>Gegeven antwoord</th>
				<th>Goede antwoord</th>
			</tr>
			<?php
				drawRows($review, $account->getGroup());			
			?>
		</table>
		
		<?php
			} elseif ($review['subject'] != "") {
		?>
	
		<em>Goed gedaan! Je heb alles goed!</em>
	
		<?php
			}
			$filemanage->clean();
		?>
	
		</fieldset>
	</form>
</body>
</html>