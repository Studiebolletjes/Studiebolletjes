<?php
	include "/sessions.php";
	include "/cssEditor.php";
	include "/fileManager.php";
	
	$returnArray = [
		"succeed" => FALSE,
		"error" => "E101",
	];
	
	function error($errNumber){
		global $returnArray;
		$returnArray["error"] = "E" . $errNumber;
		echo json_encode($returnArray);
		exit;
	}
	
	//validate
	$account = new account();
	if ($account->getLoggedIn() !== TRUE){
		error(9);
	}
	
	if ($account->getRealRights() != 0 && $account->getRealRights() != 1){
		error(10);
	}
	
	$path = "../groups/g" . $account->getGroup() . "/";
	
	if (!isset($_POST['action'], $_POST['user'])){
		error(40);
	}
	
	$_POST['user'] = upload::escape($_POST['user']);
	$path = $path . $_POST['user'] . ".css";
	$cssEditor = new CssEditor($path);
	
	switch ($_POST['action']){
		case "GET":
			$returnArray["data"] =  $cssEditor->getCssData();
			$returnArray['succeed'] = TRUE;
			unset($returnArray["error"]);
			
			break;
			
		case "SET":
			if (!isset($_POST['selector'], $_POST['property'], $_POST['value'])){
				error(40);
			}
			
			$cssEditor->setProperty($_POST['selector'], $_POST['property'], $_POST['value']);
			$cssEditor->save($path);
			$returnArray['succeed'] = TRUE;
			unset($returnArray["error"]);
			
			break;

		case "CHECK":
			$returnArray["error"] = $account->setEditorMode($_POST['user'], 2);
			if ($returnArray["error"] === TRUE){
				$returnArray['succeed'] = TRUE;
				unset($returnArray["error"]);
			}
			
			break;
			
		case "STOP":
			$returnArray["error"] = $account->resetEditorMode();
			if ($returnArray["error"] === TRUE){
				$returnArray['succeed'] = TRUE;
				unset($returnArray["error"]);
			}
			break;

		default:
			error(40);
			break;
	}
	
	echo json_encode($returnArray);
	exit;
?>