<?php
	//import and define variables
	include("/php/sessions.php");
	include("/php/fileManager.php");
	
	$account = new account();
	
	//check logged in
	if (!$account->getLoggedIn()){
		header("location:/login.php?redirect=edit.php?subject=" . $_GET['subject']);
		exit();
	} elseif ($account->getRights() == 2){
		header("location:/list.php");
		exit();
	}
	
	//check if subject name is passed through and if subject name has no invalid names
	if (!isset($_GET['subject']) || $_GET['subject'] != upload::escape($_GET['subject'])){
		//when not, redirect to list page
		header("location:/list.php");
		exit;
	}
	
	$rows = fileManager::getAllRows($_GET['subject'], $account->getGroup());
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="author" content="David de Prez" />
    <meta name="description" content="Woorden wijzigen" />

    <script src="js/jquery-1.10.2.js"></script>
    <script src="/js/MessageBox.js"></script>
    <script src="/js/upload.js"></script>
    <script src="/js/edit.js"></script>

    <link href="css/jquery-ui-1.10.4.custom.css" rel="stylesheet" />
    <link href="/css/default.css" rel="stylesheet"/>
    <link href="<?php  echo($account->getStyleSheetPath()); ?>" rel="stylesheet"/>
    <link rel="icon" href="/images/logo.ico" />

    <title>Wijzig lijst</title>
</head>
<body>
	<noscript>
		Deze website heeft javascript nodig of te kunnen functioneren
	</noscript>
	
	<form action="/edit.php" method="post">
		<fieldset>
			<!--Layout basic info-->
			<legend>Basis</legend>
			<input type="text" id="txtSubjectName" name="txtSubjectName" maxlength="15" size="16" disabled="true" value="<?php echo($_GET['subject']) ?>"/>
			<input type="button" value="Wijzigen" id="bntSave" name="bntSave"/>
			<input type="button" value="Annuleren" id="bntCancel" name="bntCancel"/>
			<label id="process"></label>
		</fieldset>
		<fieldset>
			<legend>Woorden</legend>
			<table id="rowContainer">
				<!--Container header-->
				<tr>
					<th>Id</th>
					<th>vraag</th>
					<th>Bijlage</th>
					<th>Antwoord</th>
					<th>Meerkeuze antwoorden</th>
				</tr>
				
				<?php
				foreach($rows as $id => $row){?>
					<tr id="row_<?php echo($id) ?>">
						<td>
							<input type="hidden" value="saved" name="state_<?php echo($id) ?>" id="state_<?php echo($id) ?>"/>
							<input type="hidden" value="<?php echo($row['id']) ?>" name="id_<?php echo($id) ?>" id="id_<?php echo($id) ?>"/>
							<label><?php echo($id); ?></label>
						</td>
						<td><input maxlength="40" type="text" name="word_<?php echo($id); ?>" id="word_<?php echo($id); ?>" value="<?php echo($row['question']); ?>" onchange="javascript:row_onchange(<?php echo($id); ?>)"></td>
						<td>
							<?php
							//check media
							if ($row['typeImage'] == "i"){
								//show image
								?>
								<img src="<?php echo($row['path_image']); ?>" class="example"/>
								<?php
							}
							?>
							<input type="file" onchange="javascript:row_onchange(<?php echo($id); ?>)" name="image_<?php echo($id) ?>" id="image_<?php echo($id) ?>"/>
						</td>
						<td><input maxlength="30" type="text" name="answer_<?php echo($id) ?>" id="answer_<?php echo($id) ?>" value="<?php echo($row['multi_1']) ?>" onchange="javascript:row_onchange(<?php echo($id); ?>)"/></td>
						<td>
							<div>
								<input maxlength="30" type="text" name="multi2_<?php echo($id) ?>" id="multi2_<?php echo($id) ?>" value="<?php echo($row['multi_2']) ?>" onchange="javascript:row_onchange(<?php echo($id); ?>)"/>
								<input maxlength="30" type="text" name="multi3_<?php echo($id) ?>" id="multi3_<?php echo($id) ?>" value="<?php echo($row['multi_3']) ?>" onchange="javascript:row_onchange(<?php echo($id); ?>)"/>
								<input maxlength="30" type="text" name="multi4_<?php echo($id) ?>" id="multi4_<?php echo($id) ?>" value="<?php echo($row['multi_4']) ?>" onchange="javascript:row_onchange(<?php echo($id); ?>)"/>
							</div>
						</td>
						<td>
							<input type="button" value="Verwijder" id="removeRow_<?php echo($id) ?>" onclick="javascript:removeRow(<?php echo($id) ?>)"/>
						</td>
					</tr>
				<?php }
				?>
			</table>
			<input type="button" value="Rij toevoegen" id="bntAddRow" onclick="javascript:addRow();"/>
		</fieldset>
	</form>
</body>
</html>
