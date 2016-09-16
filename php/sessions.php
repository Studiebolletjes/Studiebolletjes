<?php
	class account {
		private $loggedIn;
		
		public function getLoggedIn(){
			return $this->loggedIn;
		}
		public function getId(){
			if ($this->loggedIn)
				return $_SESSION['inlogId'];
		}
		public function getGroup(){
			if ($this->loggedIn)
				return $_SESSION['group'];
			else
				return -1;
		}
		public function getRights(){
			if ($this->loggedIn)
				if (!isset($_SESSION['cssEditorRights']) || $_SESSION['cssEditorRights'] == FALSE)
					return $this->getRealRights();
				else
					return $_SESSION['cssEditorRights'];
		}
		public function getUsername(){
			if ($this->loggedIn)
				if (!isset($_SESSION['cssEditorUsername']) || $_SESSION['cssEditorUsername'] == FALSE)
					return $this->getRealUsername();
				else
					return $_SESSION['cssEditorUsername'];
			else
				return "GAST";
		}
		public function getEmail(){
			if ($this->loggedIn){
				return $_SESSION['email'];
			}
		}
		public function getStyleSheetPath(){
			if ($this->loggedIn){
				return sprintf("/groups/g%d/%s.css",
					$this->getGroup(), $this->getUsername());
			} else{
				return "/css/gast.css";
			}
		}
		public function setEditorMode($username, $rights){
			if ($rights == 2){
				
				//connecting to database
				$database = new Database();
				$connection = $database->connect();
				if ($connection === FALSE){
					//error handling for connecting error
					return "E1";
				}
				
				//making querry
				$querry = $connection->prepare("SELECT `username` FROM `users` WHERE `username`=? AND `type`=? and `group`=?");
				$key = $database->getEncryptKey();
				$group = $this->getGroup();
				if (!$querry->bind_param("sis", $username, $rights, $group))
				{
					//error handling
					$connection->close();
					return "E2";
				}
				
				//execute querry
				if (!$querry->execute()){
					//error handling
					$connection->close();
					return "E3";
				}
				
				//organise received data
				$result = $querry->get_result();
				if ($result === false){
					//Error handling
					$querry->close();
					$connection->close();
					return "E4";
				}
				$row = $result->fetch_assoc();
				
				//close connections
				$querry->close();
				$connection->close();
				
				if ($row == NULL){
					//Username, right or group isn't correct
					return "E41";
				}
				
				
				$_SESSION['cssEditorUsername'] = $username;
				$_SESSION['cssEditorRights'] = $rights;
				
				return TRUE;
			}
		}
		public function resetEditorMode(){
			unset($_SESSION['cssEditorUsername']);
			unset($_SESSION['cssEditorRights']);
			return TRUE;
		}
		public function	isEditorMode(){
			return isset($_SESSION['cssEditorUsername'], $_SESSION['cssEditorRights']) && $_SESSION['cssEditorUsername'] && $_SESSION['cssEditorRights']; 
		}
		
		public function getRealUsername(){
			return $_SESSION['username'];
		}
		public function getRealRights(){
			if ($this->loggedIn)
				return $_SESSION['rights'];
		}
		
		function __construct() {
			//start session if not active
			if (session_status() == PHP_SESSION_NONE){
				session_start();
			}
			
			if (isset($_SESSION['logged_in'], $_SESSION['inlogId']) && $_SESSION['logged_in'] === TRUE ){
				$this->loggedIn = TRUE;
			} else {
				$this->loggedIn = FALSE;
			}
		}
		
		/**
		* Try to log in. If username and password correct, save login
		* 
		* @param string $usermame The user's username
		* @param string $password The user's password
		* 
		* @return string Error or OK:1
		*/
		function login($usermame, $password){
			
			//connecting to database
			$database = new Database();
			$connection = $database->connect();
			if ($connection === FALSE){
				//error handling for connecting error
				return "E1";
			}
			
			//making querry
			$querry = $connection->prepare("SELECT `id`, `username`, `type`, `group`, `activate`, `email` FROM `users` WHERE BINARY `username`=? AND `password`=AES_ENCRYPT(?, ?)");
			$key = $database->getEncryptKey();
			if (!$querry->bind_param("sss", $usermame, $password, $key))
			{
				//error handling
				$connection->close();
				return "E2";
			}
			
			//execute querry
			if (!$querry->execute()){
				//error handling
				$connection->close();
				return "E3";
			}
			
			//organise received data
			$result = $querry->get_result();
			if ($result === false){
				//Error handling
				$querry->close();
				$connection->close();
				return "E4";
			}
			$row = $result->fetch_assoc();
			
			//close connections
			$querry->close();
			$connection->close();
			
			//
			if ($row == NULL){
				//Username or password wrong
				return "E5";
			}
			
			if ($row['activate'] == 1){
				$_SESSION['logged_in'] = TRUE;
				$_SESSION['inlogId'] = $row['id'];
				$_SESSION['rights'] = $row['type'];
				$_SESSION['group'] = $row['group'];
				$_SESSION['email'] = $row['email'];
				
				$_SESSION['username'] = $usermame;
				return "OK:1";
			}
			
			return "E6";
		}
		
		/**
		* set session logged_in to false and reset variables in this class		* 
		*/
		function logout(){
			$_SESSION['logged_in'] = FALSE;
		}
	}
?>