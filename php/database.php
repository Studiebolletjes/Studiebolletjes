<?php
class Database{
	//Database connection
	protected static $connection;
	const ENCRYPTKEY = "seasamstraat";
	
	public function getEncryptKey(){
		return self::ENCRYPTKEY;
	}
	
	/**
	* Connect to database
	* 
	* @return mysqli object instance on success / bool false on failure
	*/
	public function connect(){
	
		//check var exists, wich mean that the connection already been established
		if (!isset(self::$connection) || self::$connection == NULL){

			//when on own server, use an .ini file outside root.
			//code for this is:
			/*
			$config = parse_ini_file(../config.ini);
			(...)
			$config['username'] ->they way to get the variable
			(...)
			*/
			//config.ini file looks like this
			/*
			[database]
			username = root
			password = usbw
			database = project_handicap
			*/
		
			//credentials
			$server = "localhost";
			$username = /*"kse001_david"*/"root";
			$password = /*"prez331"*/"usbw";
			$database = "kse001_h4david";
			$encryptKey = 'seasamstraat';
		
			//connection to database			
			self::$connection = @new mysqli($server, $username, $password, $database);
		} else{
			if (!@self::$connection->ping()){
				self::$connection = NULL;
				return self::connect();
			}
		}
		
		//check if connection is successfull
		if (self::$connection->connect_error != ""){
			//error handling for connection failure, return error
			return FALSE;
		}
		
		return self::$connection;
	}
}
?>