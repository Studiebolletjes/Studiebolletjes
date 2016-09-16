<?php
class Users{
	
	public function createGroup($username, $password){
		
	}
	
	/**
	* Will add a user to the given group. Right will determine what kinds of right it have.
	* email is required, but the email of one of the teacher in the group is also possible.
	* Errors: E1, E2, E3, E21, E25
	* 
	* @param string $username Username of the new user, escape first!
	* @param string $password Password of the new user, not encrypted
	* @param string $email Email of new user or email of teacher
	* @param int $right Right of new user
	* @param string $group Group where user is added to
	* 
	* @return mixed String if an error occurs, boolean if succeed
	*/
	public function addUser($username, $password, $email, $right, $group){
		$styleFileName = "../groups/g$group/$username.css";
		if (file_exists($styleFileName)){
			return "E25";
		}
		
		
		//connect to Database
		$database = new Database();
		$connection = $database->connect();
		if ($connection === FALSE){
			return "E1";
		}
		
		//prepaire querry and bind parameters
		$encryptionKey = Database::ENCRYPTKEY;
		$querry = $connection->prepare("INSERT INTO `users` (`username`, `password`, `email`, `type`, `group`, `activate`, `style`)" .
			" VALUES(?, AES_ENCRYPT(?, ?), ?, ?, ?, 0, '');");
		if (!@$querry->bind_param("ssssis", $username, $password, $encryptionKey, $email, $right, $group)){
			return "E2";
		}
		
		//execute querry
		if (!$querry->execute()){
			$connection->close();
			return "E3";
		}
		
		//create personal style sheet from default stylesheet
		if (copy("../css/default.css", $styleFileName) === FALSE){
			return "E21";
		}
		
		return TRUE;
	}

	/**
	* Activate an account.
	* Errors: E1, E2, E3
	* 
	* @param undefined $username The username of the account that needs to be activated
	* 
	* @return mixed String if an error occurs, boolean if succeed
	*/	
	public function activateUser($username){
		//connect to Database
		$database = new Database();
		$connection = $database->connect();
		if ($connection === FALSE){
			return "E1";
		}
		
		//prepaire querry and bind parameters
		$encryptionKey = Database::ENCRYPTKEY;
		$querry = $connection->prepare("UPDATE `users` SET `activate`=1" .
			" WHERE`username`=?;");
		if (!@$querry->bind_param("s", $username)){
			return "E2";
		}
		
		//execute querry
		if (!$querry->execute()){
			$connection->close();
			return "E3";
		}
		
		return TRUE;
	}
	
	
	public function createActivation($username, $email){
		return "E31";
	}
	
	/**
	* Get a list of teachers that are in the group
	* Errors: E1, E2, E3
	* 
	* @param string $group the group to get all teachers from
	* 
	* @return mixed String if an error occurs, array with teachers if succeed
	*/
	public function getTeachers($group){
		//connection to database
		$database = new Database();
		$connection = $database->connect();
		if ($connection === FALSE){
			$this->rs();
			return "E1";
		}
		
		//make querry
		$querry = $connection->prepare("SELECT `username`, `activate` FROM `users` WHERE `group`=? AND `type`=1");	
		if (!@$querry->bind_param("s", $group)){
			$connection->close();
			return "E2";
		}
		
		//execute querry
		if (!$querry->execute()){
			$connection->close();
			return "E3";
		}
		
		//get result
		$teachers = array();
		$result = $querry->get_result();
		while ($row = $result->fetch_assoc()){
			//insert row into array
			$teachers[] = $row;
		}
		
		$querry->close();
		$connection->close();
		
		return $teachers;
	}
	
	/**
	* Get a list of students that are in the group
	* Errors: E1, E2, E3
	* 
	* @param string $group the group to get all teachers from
	* 
	* @return mixed String if an error occurs, array with teachers if succeed
	*/
	public function getStudents($group){
		//connection to database
		$database = new Database();
		$connection = $database->connect();
		if ($connection === FALSE){
			$this->rs();
			return "E1";
		}
		
		//make querry
		$querry = $connection->prepare("SELECT `username`, `activate` FROM `users` WHERE `group`=? AND `type`=2");	
		if (!@$querry->bind_param("s", $group)){
			$connection->close();
			return "E2";
		}
		
		//execute querry
		if (!$querry->execute()){
			$connection->close();
			return "E3";
		}
		
		//get result
		$students = array();
		$result = $querry->get_result();
		while ($row = $result->fetch_assoc()){
			//insert row into array
			$students[] = $row;
		}
		
		$querry->close();
		$connection->close();
		
		return $students;
	}

	/**
	* Remove a user from the group
	* Errors: E1, E2, E3, E22
	* 
	* @param string $username username of the user
	* @param string $group group where user is asign to
	* 
	* @return mixed String if an error occurs, array with teachers if succeed
	*/
	public function removeUser($username, $group){
		//connect to Database
		$database = new Database();
		$connection = $database->connect();
		if ($connection === FALSE){
			return "E1";
		}
		
		//prepaire querry and bind parameters
		$encryptionKey = Database::ENCRYPTKEY;
		$querry = $connection->prepare("DELETE FROM `users`" .
			" WHERE `username`=? AND `group`=? AND (`type`=1 OR `type`=2);");
		if (!@$querry->bind_param("ss", $username, $group)){
			return "E2";
		}
		
		//execute querry
		if (!$querry->execute()){
			$connection->close();
			return "E3";
		}
		
		$styleFileName = "../groups/g$group/$username.css";
		if (file_exists($styleFileName)){
			if (!unlink($styleFileName)){
				return "E22";
			}
		}
		
		return TRUE;
	}
}
?>