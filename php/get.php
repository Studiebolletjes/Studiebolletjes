<?php
	//import and declare variables
	include("/sessions.php");
	include("/fileManager.php");
	
	$returnValue = ["succeed" => FALSE];
	//$_POST = $_GET;
	
	//check logged in
	$account = new account();
	if ($account->getLoggedIn()){
		//look which action it need to do
		if (isset($_POST['action'])){
			//define variables	
			$filemanage = new fileManager();
			
			switch($_POST['action']){
				case "new_game":	//start new game
					if (isset($_POST['subject'])){
						$filemanage->newGame($_POST['subject']);
						$returnValue["succeed"] = TRUE;
						break;
					}
						
					$returnValue["error"] = "E40";
					break;
				
				case "crossword":
					//maybe later...
					$returnValue["error"] = "E31";
					break;
					
				case "answer":	//check answer
					if (isset($_POST["id"], $_POST['answer'])){
						$result = NULL;
						
						//look if it administrator on other group or normal user
						if (isset($_POST['group']) && $account->getRights() == 0){
							$result = $filemanage->getAnswer($_POST["id"], $_POST['answer'], $_POST['group']);
						}else{
							$result = $filemanage->getAnswer($_POST["id"], $_POST['answer'], $account->getGroup());
						}
						
						//check if an error occurs
						if (gettype($result) == "string"){
							//Error handler
							$returnValue["error"] = $result;
							break;
						}
						
						//pack data
						$returnValue["data"] = $result;
						$returnValue["succeed"] = TRUE;
						break;
					}
					$returnValue["error"] = "E40";
					break;
					
				case "question": //give random question
					$result = NULL;
					
					//look if it administrator on other group or normal user
					if (isset($_POST['group']) && $account->getGroup() == 0){
						$result = $filemanage->getQuestion($_POST['group']);
					}else{
						$result = $filemanage->getQuestion($account->getGroup());
					}
					
					//check if an error occurs
					if (gettype($result) == "string"){
						//Error handler
						$returnValue["error"] = $result;
						break;
					}
					
					//pack data
					$returnValue["data"] = $result;
					$returnValue["succeed"] = TRUE;
					break;
					
				case "score":
					//set and pack the data
					$returnValue["data"] = [
						"score" => $filemanage->getScore(),
						"wrongWords" => $filemanage->CountWrongWords(),
					];
					$returnValue["succeed"] = TRUE;
					
					break;
				
				default:
					//unknown command error handler
					$returnValue["error"] = "E8";
					break;
			}
			
			//return encoded array
			echo(json_encode($returnValue));
			exit();
		}
		
		//missing parameters error handler
		$returnValue["error"] = "E40";
		echo(json_encode($returnValue));
		exit();
	}
	
	//not loggedin error handler
	$returnValue["error"] = "E9";
	echo(json_encode($returnValue));
?>