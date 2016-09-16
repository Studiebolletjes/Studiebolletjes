<?php
	//include and declare variables
	include("/sessions.php");
	include("/fileManager.php");
	$returnArray = [
		"succeed" => FALSE,
		"error" => "E101",
	];
	
	//log in
	$account = new account();
	if ($account->getLoggedIn()){
		
		//check user rights
		if ($account->getRights() != 0 && $account->getRights() != 1){
			$returnArray['error'] = "E10";
			
			//return data
			echo(json_encode($returnArray));
			exit();
		}
		
		
		//look which action to do
		if (isset($_POST['action'])){
			if ($_POST['action'] == "delete"){ //delete subject
				//check parameters
				if (!isset($_POST['subject_name']) || $_POST['subject_name'] == ""){
					$returnArray['error'] = "E40";
					
					//return data
					echo(json_encode($returnArray));
					exit();
				}
				
				//escape characters
				$_POST['subject_name'] = upload::escape($_POST['subject_name']);
				
				//make path
				$path = sprintf(
					upload::PERMDIRECTORY, 
					$account->getGroup(), $_POST['subject_name']
				);
				
				//delete directory
				$error = fileManager::removeDirectory($path);
				if ($error !== TRUE){
					$returnArray['error'] = $error;
					
					//return data
					echo(json_encode($returnArray));
					exit();
				}
				
				//remove the questions
				$returnArray['error'] =  fileManager::removeSubjects($account->getGroup(), $_POST['subject_name']);
				if ($returnArray['error'] === TRUE){
					$returnArray['succeed'] = TRUE;
					$returnArray['error'] = NULL;
				}
				
				echo(json_encode($returnArray));
				exit();
			}
			elseif ($_POST['action'] == "delete_row"){ //delete row
				if (!isset($_POST['id'])){
					$returnArray['error'] = "E40";
					
					//return data
					echo(json_encode($returnArray));
					exit();
				}
				
				$returnArray['error'] = fileManager::removeRow($account->getGroup(), $_POST['id']);
				
				if ($returnArray['error'] === TRUE){
					$returnArray['succeed'] = TRUE;
					$returnArray['error'] = NULL;
				}
				
				echo(json_encode($returnArray));
				exit();
			}
			elseif ($_POST['action'] == "upload_row"){ //upload data for row (question, etc)
				//check parameters
				if (!isset($_POST['subject'], $_POST['question'], $_POST['m1'], $_POST['m2'], $_POST['m3'], $_POST['m4']) || $_POST['subject'] == ""){
					$returnArray['error'] = "E40";
					echo(json_encode($returnArray));
					exit();
				}
				
				//save row to Database
				$upload = new upload();
				$returnArray['error'] = $upload->addQuestion($account->getGroup(), $_POST['subject'], $_POST['question'], 
					$_POST['m1'], $_POST['m2'], $_POST['m3'], $_POST['m4']);
				
				if ($returnArray['error'] === TRUE){
					$returnArray['succeed'] = TRUE;
					$returnArray['error'] = NULL;
				}
				
				echo(json_encode($returnArray));
				exit();
			}
			elseif ($_POST['action'] == "update_row"){
				//TODO: update row and look if new file has been uploaded or not -> update if necessary
				//TODO: check parameters
				if (!isset($_POST['id'], $_POST['question'], $_POST['m1'], $_POST['m2'], $_POST['m3'],
					$_POST['m4'])){
					$returnArray['error'] = "E40";
					echo(json_encode($returnArray));
				}
				
				//change row
				$upload = new upload();
				$returnArray['error'] = $upload->editQuestion(intval($_POST['id']), $account->getGroup(), $_POST['question'], $_POST['m1'], 
					$_POST['m2'], $_POST['m3'], $_POST['m4']);
				
				//return result (succeed or error)
				if ($returnArray['error'] === TRUE){
					$returnArray['succeed'] = TRUE;
					$returnArray['error'] = NULL;
				}
				
				echo(json_encode($returnArray));
				exit();
			}
			elseif ($_POST['action'] == "abort"){
				//remove upload
				$upload = new upload();
				if ($upload->remove()){
					//set return values
					$returnArray = [
						"succeed" => TRUE,
						"error" => "",
					];
				}
				
				//return return values
				echo(json_encode($returnArray));
				exit();
			}
			else{
				$returnArray['error'] = "E8";
				
				//return data
				echo(json_encode($returnArray));
				exit();
			}
		}
		if (isset($_GET['action']) && $_GET['action'] == "upload_file"){ //upload file
			//save chunk
			$upload = new upload();
			$error = $upload->saveChunk();
			
			//check errors
			if ($error !== TRUE){
				$returnArray['error'] = $error;
				echo(json_encode($returnArray));
				exit();
			}
			
			//check last chuck
			if (isset($_GET['ext'], $_GET['subject'])){				
				//check subject empty
				if ($_GET['subject'] == ""){
					//error handling
					$returnArray['error'] = "E40";
					
					//return data
					echo(json_encode($returnArray));
					exit();
				}
				//replace special chars back
				$_GET['subject'] = str_replace('*2', '=', $_GET['subject']);
				$_GET['subject'] = str_replace('*1', '&', $_GET['subject']);
				$_GET['subject'] = str_replace('*0', '*', $_GET['subject']);
				
				//check if file need to be updated or is new file...
				if (isset($_GET['id'])){
					//remove old file and replace with new and update to database
					$returnArray['error'] = $upload->replaceFile($_GET['ext'], $account->getGroup(), intval($_GET['id']));
				} else{
					//finish file upload
					$returnArray['error'] = $upload->finish($_GET['ext'], $account->getGroup(), $_GET['subject']);	
				}
				
				if ($returnArray['error'] !== TRUE){
					echo(json_encode($returnArray));
					exit();
				}
			}
			
			$returnArray['succeed'] = TRUE;
			$returnArray['error'] = NULL;
			
			echo(json_encode($returnArray));
			exit();
		}
		else{
			$returnArray['error'] = "E40";
			
			//return data
			echo(json_encode($returnArray));
			exit();
		}
	} else {
		$returnArray['error'] = "E9";
	}
	
	//return data
	echo(json_encode($returnArray));
	exit();
?>