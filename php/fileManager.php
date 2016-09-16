<?php
include ("/database.php");

class fileManager {	
	const GROUPPATH = "groups/g%d/";
	private $idDone = array();
	private $wrongWords = array();
	private $score = 0;
	private $subject = "";
	private $database;
	
	public function getScore(){
		return $this->score;
	}
	public function CountWrongWords(){
		return count($this->wrongWords);
	}
	
	/**
	* Get the values that is needed to make a review. This are the
	* words, score and the subject of the game
	* 
	* @return array Values needed for review
	*/
	public function getReview(){
		return [
			"words" => $this->wrongWords,
			"score" => $this->score,
			"subject" => $this->subject,
		];
	}

	function __construct() {
		//start session if not active
		if (session_status() == PHP_SESSION_NONE){
			session_start();
		}
		
		//load data if is initialize
		if (isset($_SESSION["IsInitialized"]) && $_SESSION["IsInitialized"] === TRUE){
			$this->idDone = $_SESSION["id_done"];
			$this->wrongWords = $_SESSION["wrong_words"];
			$this->score = $_SESSION["score"];
			$this->subject = $_SESSION["subject"];			
		}else{
			//reset data in session
			$this->save();
		}
		
		//define database for later use
		$this->database = new Database();
	}
	
	/**
	* Save variables to session
	*/
	private function save(){
		$_SESSION["id_done"] = $this->idDone;
		$_SESSION["wrong_words"] = $this->wrongWords;
		$_SESSION["score"] = $this->score;
		$_SESSION["subject"] = $this->subject;
		
		$_SESSION["IsInitialized"] = TRUE;
	}
	
	/**
	* Start new game
	* 
	* @param string $subject Subject of new game
	*/
	public function newGame($subject){
		$this->idDone = array();
		$this->wrongWords = array();
		$this->score = 0;
		$this->subject = $subject;
		$this->save();
	}
	
	/**
	* Check if the answer is good
	* Errors: E1, E2, E3, E41
	* 
	* @param int $id The id of question
	* @param string $answer The answer given by player
	* @param int $group The group that the player belongs to
	* 
	* @return mixed string If an error occurs / array The data such as
	* if answer is good
	*/
	public function getAnswer($id, $answer, $group){
		
		//connecting to database
		$connection = $this->database->connect();
		if ($connection === FALSE){
			return "E1";
		}
		
		//making querry
		$querry = $connection->prepare("SELECT * FROM `g$group` WHERE id=?");
		if (!$querry->bind_param("i", $id)){
			$connection->close();
			return "E2";
		}
		
		//execute querry
		if (!$querry->execute()){
			return "E3";
		}
		
		
		
		//get result in array
		$result = $querry->get_result();
		if (!$row = $result->fetch_assoc()){
			$querry->close();
			$connection->close();
			return "E41";
		}
		
		//close connections
		$querry->close();
		$connection->close();
		
		//remember that this question is done
		array_push($this->idDone, $id);
		
		//check answer is correct
		if (strtolower($answer) == strtolower($row['multi_1'])){
			//answer correct, increase score
			$this->score++;
			$this->save();
			
			//return data in array
			return [
				"isGood" => TRUE,
			]; 
		} 
		else {
			//save given answer and id of question
			array_push($this->wrongWords, [
				"id" => $id,
				"answer" => $answer,
			]);
			$this->save();
			
			//return correct answer and other data in array
			return [
				"isGood" => FALSE,
				"answer" => $row['multi_1'],
			]; 
		}
	}

	/**
	* Get a random question from database
	* Errors: E1, E2, E3, E41, E42
	* 
	* @param int $group The group that the player belongs to
	* 
	* @return mixed array if found random question / string if there is
	* an error
	*/
	public function getQuestion($group){
		//connect to database
		$connection = $this->database->connect();
		if ($connection === FALSE){
			return "E1";
		}
		
		//making querry
		$querry = $connection->prepare("SELECT * FROM `g$group` WHERE `subject_name` = ?");
		if (!@$querry->bind_param("s", $this->subject)){
			$connection->close();
			return "E2";
		}
		
		//execute querry
		if (!$querry->execute()){
			return "E3";
		}
		
		//get result in array
		$result = $querry->get_result();
		if (!$rows = $result->fetch_all()){
			$querry->close();
			$connection->close();
			return "E41";
		}
		
		//close connections
		$querry->close();
		$connection->close();
		
		//look if all questions are done
		if (count($this->idDone) >= count($rows)){
			return "E42";
		}
		
		while(TRUE){
			//random id
			$id = rand(0, count($rows)-1);
			
			//check id already done
			if (!in_array($rows[$id][0], $this->idDone)){				
				//save data to variables
				$data = array();
				$data["id"] = $rows[$id][0];
				$data["question"] = $rows[$id][1];
				$data["m1"] = $rows[$id][2];
				$data["m2"] = $rows[$id][3];
				$data["m3"] = $rows[$id][4];
				$data["m4"] = $rows[$id][5];
				$data["path_media"] = $rows[$id][6];
				$data["type_media"] = $rows[$id][7];
				
				return $data;
			}
		}
		
	}

	
	/**
	* Get the subjects.
	* Errprs: E31, E15
	* 
	* @param string $group The group to get all the subject from. If
	* group is set to 'all', than get all the subjects
	* 
	* @return mixed String if there is an error / array of subjects if
	* succeed
	*/
	static public function getSubjects($group){
		if ($group == "all"){ //adminitrator
			//TODO: Administrator need to see all subjects of all users
			return "E31";
		} else { //every one else
			//check if path of group exists
			$path = sprintf(self::GROUPPATH, $group);
			if (!file_exists($path)){
				return "E15";
			}
			
			//get Directory
			$directory = dir($path);
			
			$subject = array();
			while(TRUE){
				
				//read next item in directory
				$entry = $directory->read();
				if ($entry === FALSE){	//when no item available
					break;
				}
				
				//check if it is an directory
				if (is_dir($path . $entry) && $entry != "." && $entry != ".."){
					array_push($subject, $entry);
				}
			}
			
			return $subject;
		}
	}
	
	
	static public function getAllRows($subject, $group){
		//connect to database
		$dat = new Database();
		$connection = $dat->connect();
		if ($connection === FALSE){
			return "E1";
		}
		
		//making querry
		$querry = $connection->prepare("SELECT * FROM `g$group` WHERE `subject_name` = ? ORDER BY `id` ASC ");
		if (!$querry->bind_param("s", $subject)){
			$connection->close();
			return "E2";
		}
		
		//execute querry
		if (!$querry->execute()){
			return "E3";
		}
		
		//get result in array
		$result = $querry->get_result();
		$rows = array();
		$i = 1;
		while($row = $result->fetch_assoc()){
			$rows[$i] = $row;
			$i++;
		}
		
		return $rows;
	}
	
	/**
	* Remove all files is current directory.
	* Errors: E22, E16
	* 
	* @param string $path
	* 
	* @return string if error occurs, TRUE if succeed
	*/
	static public function removeDirectory($path){
		//check directory exists
		$path = "../" . $path;
		if (file_exists($path)){
			//get Directory
			$directory = dir($path);
			while(true){
				//read next entry
				$entry = $directory->read();
				if ($entry === FALSE){
					//no entry availeble
					break;
				}
				
				//check if entry is goback sign
				if ($entry != "." && $entry != ".."){
					//check entry is Directory
					if (is_dir($path . "/" . $entry)){
						return "E16";
					}
					
					if (!unlink($path . "/" . $entry)){
						return "E22";
					}
				}
			}
			
			//remove Directory
			rmdir($path);
			return TRUE;
		} else{
			return "E26";
		}
	}
	
	/**
	* Remove all question with a specific subject name from the
	* database.
	* Errors: E1, E2, E3
	* 
	* @param int $group Group where subject is saved
	* @param string $subject Name of the subject
	* 
	* @return mixed String if an error occurs, boolean if succeed
	*/
	static public function removeSubjects($group, $subject){
		//connect to database
		$database = new Database();
		$connection = $database->connect();
		if ($connection == FALSE){
			return "E1";
		}
		
		//making querry
		$querry = $connection->prepare("DELETE FROM `g$group` WHERE `subject_name`=?;");
		if (!@$querry->bind_param("s", $subject)){
			$connection->close();
			return "E2";
		}

		//execute querry
		if (!$querry->execute()){
			return "E3";
		}
		
		//close connections
		$querry->close();
		$connection->close();
		return TRUE;
	}


	/**
	* Will remove a row from the given group. 
	* Errors: E1, E2, E3, E41, E22, E24
	* 
	* @param integer $group The group where the row is a member of
	* @param integer $id The id of the row that needs to remove
	* 
	* @return mixed String if an error occurs, boolean if succeed
	*/
	static public function removeRow($group, $id){
		//connection to database
		$database = new Database();
		$connection = $database->connect();
		if ($connection === FALSE){
			$this->rs();
			return "E1";
		}
		
		//make querry
		$querry = $connection->prepare("SELECT `path_image`, `typeImage` FROM `g$group` WHERE `id` = ?");	
		if (!@$querry->bind_param("i", $id)){
			$connection->close();
			return "E2";
		}
		
		//execute querry
		if (!$querry->execute()){
			$connection->close();
			return "E3";
		}
		
		//get result
		$result = $querry->get_result();
		if (!$row = $result->fetch_assoc()){
			$querry->close();
			$connectionr->close();
			return "E41";
		}
		
		//close querry
		$querry->close();
		$querry=NULL;
		
		//check if it has media to remove
		if ($row['typeImage'] != "n"){
			$posQuestionMark = strrpos($row["path_image"], "?");
			$fileName = $row["path_image"];
			if ($posQuestionMark !== FALSE){
				$fileName = substr($row["path_image"], 0, $posQuestionMark);
			}
			
			//check if file exists
			if (!file_exists($fileName)){
				$connection->close();
				return "E24";
			}
			
			//remove file
			if (!unlink($fileName)){
				$connection->close();
				return "E22";
			}
		}
		/*
		//connect to database
		$database = new Database();
		$connection = $database->connect();
		if ($connection == FALSE){
			return "E1";
		}*/
		
		//making querry
		$querry = $connection->prepare("DELETE FROM `g$group` WHERE `id`=?;");
		if (!@$querry->bind_param("s", $id)){
			$connection->close();
			return "E2";
		}

		//execute querry
		if (!$querry->execute()){
			$connection->close();
			return "E3";
		}
		
		//close connections
		$querry->close();
		$connection->close();
		
		return TRUE;
	}

	/**
	* Ends the game
	*/
	public function clean(){
		unset(
			$_SESSION["id_done"],
			$_SESSION["wrong_words"],
			$_SESSION["score"],
			$_SESSION["subject"]
		);
		
		$_SESSION["IsInitialized"] = FALSE;
	}
}
class upload{
	const MAX_SIZE = 52428800; //50mibi
	const TMPDIRECTORY = "../tmp/";
	const PERMDIRECTORY = "groups/g%s/%s";
	const PATTERN = '/[^\w\s&#]/';
	static private $extensionList = array(
		"jpg" => "i",
		"png" => "i",
		"gif" => "i",
		"mp3" => "m1", //audio/mpeg
		"ogg" => "m2", //audio/ogg
		"wav" => "m3", //audio/wav
	);
	private $finalPath = "";
	private $fileType = "n";//standard on n (none)
	
	private $tmpName;
	
	private function getName(){
		if ($this->tmpName == NULL){
			$this->tmpName = mt_rand() . ".tmp";
			$_SESSION['uploadTMP'] = $this->tmpName;
		}
		return $this->tmpName;
	}
	
	/**
	* Reset the session variables that are use to get
	* the path to the file after it is replaced and it type. 
	*/
	private function rs(){
		$_SESSION['finalPath'] = NULL;
		$_SESSION['fileType'] = NULL;
	}
	
	/**
	* escape the string for a path. Remove all character to
	* prevent directory traversal.
	* 
	* $@param string $source The string that need to escape
	* 
	* @return string The escaped source
	*/
	static public function escape($source){		
		/*
		$source = str_replace('!', '&#33;' ,$source);
		$source = str_replace('"', '&#34;' ,$source);
		$source = str_replace('#', '&#35;' ,$source);
		$source = str_replace('$', '&#36;' ,$source);
		$source = str_replace('%', '&#37;' ,$source);
		$source = str_replace('&', '&#38;' ,$source);
		$source = str_replace("'", '&#39;' ,$source);
		$source = str_replace('(', '&#40;' ,$source);
		$source = str_replace(')', '&#41;' ,$source);
		$source = str_replace('`', '&#96;' ,$source);*/
		
		//replace all charected with nothing when they are not allowed
		$source = preg_replace(self::PATTERN, "", $source);
		
		return $source;			
	}
	
	/**
	* Save the chunk to temporarily file
	* 
	* @return mixed Boolean if succeeded, string if an error occurs
	*/
	public function __construct(){
		//start session if not active
		if (session_status() == PHP_SESSION_NONE){
			session_start();
		}
		
		if (isset($_SESSION['uploadTMP'])){
			$this->tmpName = $_SESSION['uploadTMP'];
		}
		
		if (isset($_SESSION['finalPath'], $_SESSION['fileType'])){
			$this->finalPath = $_SESSION['finalPath'];
			$this->fileType = $_SESSION['fileType'];
		}
	}
	
	/**
	* Save the piece of a file that is been uploaded to a temporarily
	* file until whole file is complete.
	* Errors: E17, E18
	* 
	* @return string if error occurs, TRUE is succeed
	*/
	public function saveChunk(){
		//check if file exceed max upload
		if (file_exists(self::TMPDIRECTORY . $this->getName())){
			if (filesize(self::TMPDIRECTORY . $this->getName()) > self::MAX_SIZE){
				
				//try to abort
				if ($this->remove()){
					return "E17";
				} else{
					//abort error handler
					return "E17, E18";
				}
			}
		}
		
		//get content from uploaded chunk
		$fileContent = file_get_contents('php://input');
		
		//save content to temporarily file
		$file = fopen(self::TMPDIRECTORY . $this->getName(), 'a');
		fwrite($file, $fileContent);
		fclose($file);
		
		return TRUE;
	}
	/**
	* Remove the temporarily file
	* 
	* @return boolean If the file is removed
	*/
	public function remove(){
		$_SESSION['uploadTMP'] = NULL;
		return unlink(self::TMPDIRECTORY . $this->tmpName);
	}
	
	/**
	* Place temporarily file to permentent place. It make the directory
	* and remove existing file if needed
	* Errors: E19, E20, E22, E23 (E21 -> no yet)
	* 
	* @param string $extension The extension of file
	* @param int $group The group number
	* @param string $subject The subject where the file must be saved
	* 
	* @return string An error, bool if succeed
	*/
	public function finish($extension, $group, $subject){	
		//validate
		if (!isset(self::$extensionList[$extension])){
			if (!$this->remove()){
				return "E19, E22";
			}
			return "E19";
		}
		$subject = self::escape($subject);
			
		//create path
		$directoryPath = sprintf(
			"../" . self::PERMDIRECTORY,
			$group,
			$subject
		);
		
		//create directory if doesn't exists
		if (!file_exists($directoryPath)){
			if (!mkdir($directoryPath)){
				return "E20";
			}
			
			//set index.php
			//...
			//copy error: E21
		}
		
		//find availeble name
		$filePath="";
		$id = 0;
		do{
			$filePath = sprintf(
				$directoryPath . "/file_%s.%s",
				$id++,
				$extension
			);
		} while (file_exists($filePath));
		
		//move temporarily file to permanent place
		if (rename(self::TMPDIRECTORY . $this->getName(), $filePath)){
			$_SESSION['uploadTMP'] = "";
			$_SESSION['finalPath'] = $filePath;
			$_SESSION['fileType'] = self::$extensionList[$extension];
			
			$this->finalPath = $filePath;
			$this->fileType = self::$extensionList[$extension];
			return TRUE;
		} else {
			return "E23";
		}
	}

	/**
	* Add row with question, answer, the choises and if availble: path
	* to file and type of file
	* Errors: E1, E2, E3, E40, E20
	* 
	* @param int $group group of user
	* @param string $subject name of subject
	* @param string $question The question
	* @param string $m1 answer of question
	* @param string $m2 choise 2
	* @param string $m3 choise 3
	* @param string $m4 choise 4
	* 
	* @return string If error occurs, boolean if succeed
	*/
	public function addQuestion($group, $subject, $question, $m1, $m2, $m3, $m4){
		//validate parameters
		$subject = self::escape($subject);
		if ($subject == "" || $m1 == "" || $m2 == "" || $m3 == "" || $m4 == ""){
			$this->rs();
			return "E40";
		}
		
		//connection to database
		$database = new Database();
		$connection = $database->connect();
		if ($connection === FALSE){
			$this->rs();
			return "E1";
		}
		
		//making querry
		$qeury = $connection->prepare("INSERT INTO g$group" . 
			"(`question`, `multi_1`, `multi_2`, `multi_3`, `multi_4`, `path_image`, `typeImage`, `subject_name`) " . 
			"VALUES(?, ?, ?, ?, ?, ?, ?, ?);");
		$uncachedPath = $this->finalPath . "?" . time();
		if (!$qeury->bind_param("ssssssss",
			$question, $m1, $m2, $m3, $m4, $uncachedPath, $this->fileType, $subject
		)){
			$connection->close();
			$this->rs();
			return "E2";
		}
		
		//execute querry
		if (!$qeury->execute()){
			$connection->close();
			$this->rs();
			return "E3";
		}
		
		//close connections
		$qeury->close();
		$connection->close();
		
		//make directory for list.php (this finds subject through directories)
		//create path
		$directoryPath = sprintf(
			"../" . self::PERMDIRECTORY,
			$group,
			$subject
		);
		
		//create directory if doesn't exists
		if (!file_exists($directoryPath)){
			if (!mkdir($directoryPath)){
				$this->rs();
				return "E20";
			}
			
			//set index.php
			//...
			//copy error: E21
		}
		
		//reset file data
		$this->rs();
		
		//return succeed
		return TRUE;
	}

	/**
	* Update the question and all choises of the row with the given id
	* Errors: E1, E2, E3, E40
	* 
	* @param integer $id Id if the row to edit
	* @param integer $group group of user
	* @param string $question The question to edit
	* @param string $m1 The anser of question to edit
	* @param string $m2 choise 2 to edit
	* @param string $m3 choise 3 to edit
	* @param string $m4 choise 4 to edit
	* 
	* @return String if error occurs, boolean if succeed
	*/
	public function editQuestion($id, $group, $question, $m1, $m2, $m3, $m4){
		
		//validate parameters
		if ($question == "" || $m1 == "" || $m2 == "" || $m3 == "" || $m4 == "" || gettype($id) != "integer"){
			return "E40";
		}
		
		//connnect to Database
		$database = new Database();
		$connection = $database->connect();
		if ($connection === FALSE){
			return "E1";
		}
		
		//making querry			
		$querry = $connection->prepare("UPDATE `g$group`" . " SET `question`=?, `multi_1`=?, `multi_2`=?, `multi_3`=?, `multi_4`=? WHERE `id`=?;");
		if (!$querry->bind_param("sssssi", $question, $m1, $m2, $m3, $m4, $id)){
			$connection->close();
			return "E2";
		}
		
		//execute querry
		if (!$querry->execute()){
			$connection->close();
			return "E3";
		}
		
		//close connection
		$querry->close();
		$connection->close();
		
		return TRUE;
	}
	
	/**
	* Replace the old file with the new uploaded file
	* Errors: E1, E2, E3, E40, E41, E19, E22, E24
	* 
	* @param string $extension The extension of the file
	* @param integer $group group where user is in
	* @param integer $id The id of question
	* 
	* @return String if an error occurs, boolean if succeed
	*/
	public function replaceFile($extension, $group, $id){
		//validate
		if (!isset(self::$extensionList[$extension])){
			if (!$this->remove()){
				return "E19, E22";
			} 
			return "E19";
		}
		
		if (gettype($id) != "integer"){
			return "E40";
		}
		
		//connection to database
		$database = new Database();
		$connection = $database->connect();
		if ($connection === FALSE){
			$this->rs();
			return "E1";
		}
		
		//make querry
		$querry = $connection->prepare("SELECT `subject_name`, `path_image`, `typeImage` FROM `g$group` WHERE `id` = ?");	
		if (!@$querry->bind_param("i", $id)){
			$connection->close();
			return "E2";
		}
		
		//execute querry
		if (!$querry->execute()){
			$connection->close();
			return "E3";
		}
		
		//get result
		$result = $querry->get_result();
		if (!$row = $result->fetch_assoc()){
			$querry->close();
			$connectionr->close();
			return "E41";
		}
		
		//close querry
		$querry->close();
		$connection->close();
		$querry=NULL;
		$connection=NULL;
		
		//check if it has media to remove
		if ($row['typeImage'] != "n"){
			$posQuestionMark = strrpos($row["path_image"], "?");
			$fileName = $row["path_image"];
			if ($posQuestionMark !== FALSE){
				$fileName = substr($row["path_image"], 0, $posQuestionMark);
			}
			
			//check if file exists
			if (!file_exists($fileName)){
				return "E24";
			}
			
			//remove file
			if (!unlink($fileName)){
				return "E22";
			}
		}
		
		//finish upload
		$err = $this->finish($extension, $group, $row['subject_name']);
		if ($err !== TRUE){
			return $err;
		}
		
		//connection to database
		$database = new Database();
		$connection = $database->connect();
		if ($connection === FALSE){
			$this->rs();
			return "E1";
		}
		
		//update database...
		//making querry
		$querry = $connection->prepare("UPDATE `g$group` SET `path_image`=?, `typeImage`=? WHERE `id`=?");
		$uncachedPath = $this->finalPath . "?" . time();
		if (!$querry->bind_param("ssi", $uncachedPath, $this->fileType, $id)){
			$connection->close();
			return "E2";
		}
		
		//running querry
		if (!$querry->execute()){
			$connection->close();
			return "E3";
		}
		
		$querry->close();
		$connection->close();
		
		return TRUE;
	}
}
?>