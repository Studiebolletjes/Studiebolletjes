<?php
	include "/sessions.php";
	include "/fileManager.php";
	include "/users.php";
	
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
	
	//check if main variable are given
	if (!isset($_POST['action'])){
		error(40);
	}
	$users = new Users();
	
	switch($_POST['action']){
		case "create":
			//validate given parameters
			if (isset($_POST['username'], $_POST['password'], $_POST['pass_repeat'], $_POST['email'], $_POST['rights'])){
				if ($_POST['password'] !== $_POST['pass_repeat']){
					error(103);
				}
				
				$_POST['rights'] = intval($_POST['rights']);
				if ($_POST['rights'] != 1 && $_POST['rights'] != 2){
					error(10);
				}
				
				//remove illigal characters
				$_POST['username'] = upload::escape($_POST['username']);
				
				if ($_POST['email'] == ''){
					$returnArray["error"] = $users->addUser($_POST['username'], $_POST['password'], $account->getEmail(), $_POST['rights'], $account->getGroup());	
					if ($returnArray["error"] !== TRUE){
						echo json_encode($returnArray);
						exit;
					}
					
					$returnArray["error"] = $users->activateUser($_POST['username']);
					if ($returnArray["error"] !== TRUE){
						echo json_encode($returnArray);
						exit;
					}
					
					$returnArray["succeed"] = TRUE;
					$returnArray["error"] = NULL;
				} else {
					$returnArray["error"] = $users->addUser($_POST['username'], $_POST['password'], $_POST['email'], $_POST['rights'], $account->getGroup());
					if ($returnArray["error"] !== TRUE){
						echo json_encode($returnArray);
						exit;
					}
					
					$returnArray["error"] = $users->createActivation($_POST['username'], $_POST['email']);
					if ($returnArray["error"] !== TRUE){
						echo json_encode($returnArray);
						exit;
					}
					
					$returnArray["succeed"] = TRUE;
					$returnArray["error"] = NULL;
				}
			} else{
				error(40);
			}
			break;
		case "getTeachers":
			$returnArray["error"] = $users->getTeachers($account->getGroup());
			$returnArray["succeed"] = gettype($returnArray["error"]) == "array";
			break;
		case "getStudents":
			$returnArray["error"] = $users->getStudents($account->getGroup());
			$returnArray["succeed"] = gettype($returnArray["error"]) == "array";
			break;
		case "remove":
			if (isset($_POST['username'])){
				$_POST['username']=upload::escape($_POST['username']);
				if ($account->getUsername() == $_POST['username']){
					error(104);
				}
				$returnArray["error"] = $users->removeUser($_POST['username'], $account->getGroup());

				if ($returnArray["error"] !== TRUE){
					echo json_encode($returnArray);
					exit;
				}
				
				$returnArray["succeed"] = TRUE;
				$returnArray["error"] = NULL;
			} else{
				error(40);
			}
			break;
		default:
			error(8);
			break;
	}
	
	echo json_encode($returnArray);
	exit;
?>