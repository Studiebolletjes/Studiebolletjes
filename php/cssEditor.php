<?php
class CssEditor {
	const FILTERPATTERN = '/[^\w&#\.\*\{\}\:\;\-]/';
	private $cssData;
	private $fileHandler;
	private $selector;
	private $property;
	private $value;
	
	/**
	* Open file and prepaire
	* 
	* @param String $fileName The path and the name to the file
	* 
	* @return
	*/
	public function __construct($fileName){
		$this->cssData = array();
		
		
		if (file_exists($fileName)){
			//read default file
			$this->loadFile(fopen("../css/default.css", 'r'));
			
			//read given file
			$this->loadFile(fopen($fileName, 'r'));
		}
	}
	
	/**
	* Replace all illegally  characters
	* @param String $value string where all legally characters are accepted
	* 
	* @return string where all illegally characters are removed from
	*/
	private function escape($value){
		return preg_replace(self::FILTERPATTERN, "", $value);
	}
	
	/**
	* Read a given file as a css file and put all data into a variable
	* @param undefined $fileHandler a handler of a file
	* 
	* @return false if not a handler;
	*/
	private function loadFile($fileHandler){
		//check if it is a handler
		if ($fileHandler == FALSE){
			return FALSE;
		}
		
		//read file until end
		$this->fileHandler = $fileHandler;
		while (!feof($this->fileHandler)){
			$this->selector = "";
			$this->readSelector();
		}
		
		//close file
		fclose($this->fileHandler);
	}
	
	/**
	* Read a single character from handler
	* 
	* @param undefined $pattern A pattern with all characters that are not allowed. Is none is given, is use the standard pattern
	* 
	* @return a single charater is succeed, false if end of file
	*/
	private function readCharacter($pattern){
		//load standard pattern if none is given
		if ($pattern == FALSE){
			$pattern = self::FILTERPATTERN;
		}
		
		//read a character if not end of file, return emtpy string if character is illegal
		if (!feof($this->fileHandler)){
			return preg_replace($pattern, "", fgetc($this->fileHandler));
		} else {
			return FALSE;
		}
	}
	
	/**
	* Read a selector from the CSS file
	* 
	* @return
	*/
	private function readSelector() {
		//different pattern if first character of selector is readed
		if (strlen($this->selector) > 0){
			//read first character, with while accepting all whitespaces, &, #, ., {, -
			$character = $this->readCharacter('/[^\w&#\.\*\{\- ]/');
		} else {
			//read without pattern
			$character = $this->readCharacter(NULL);
		}
		
		//if no character is readed, stop
		if ($character === FALSE){
			return;
		}
		elseif ($character == "{"){
			//end of selecter, read property
			$this->selector = rtrim($this->selector);	//remove whitespaces of end of string
			if (!isset($this->cssData[$this->selector])){
				$this->cssData[$this->selector] = array();	
			}
			$this->property = "";
			$this->readProperty();
		} else {
			//read next character of selector
			$this->selector .= $character;
			$this->readSelector();
		}
	}
	
	/**
	* Read property  of the current selector
	* 
	* @return
	*/
	private function readProperty(){
		$character = $this->readCharacter(NULL);
		if ($character === FALSE){
			return;
		}
		elseif ($character == "}"){
			//end of block, reset all and stop
			$this->selector = "";
			$this->property = "";
			$this->value = "";
			return;
		}
		elseif ($character != ":"){
			//read next character of property
			$this->property .= $character;
			$this->readProperty();
		} else {
			//$this->property = $this->escape($this->property);
			
			//end of property, read value
			$this->cssData[$this->selector][$this->property] = "";
			$this->value = "";			
			$this->readValue();
			
			//read next property (until end of block)
			$this->readProperty();
		}
	}
	
	/**
	* read the value of ther current property
	* 
	* @return
	*/
	private function readValue(){
		//read character with whitespace, &, #, ., ;, -, %
		$character = $this->readCharacter('/[^\w&#\(\)\/\.\;\-\% ]/');
		if ($character === FALSE){
			return;
		}
		elseif ($character != ";"){
			//read next character of value
			$this->value .= $character;
			$this->readValue();
		} else {
			//$this->value = $this->escape($this->value);
			
			//property and value readed, stop
			$this->cssData[$this->selector][$this->property] = ltrim($this->value);
			$this->property = "";
			$this->value = "";
		}
	}
	
	/**
	* Convert data to a valid css file
	* 
	* @return String a valid css string that can be save in a file
	*/
	public function toString(){
		$stringValue = "";
		foreach ($this->cssData as $selector => $data){
			$stringValue .= $selector . " {\n";
			
			foreach ($data as $property => $value){
				$stringValue .= "\t" . $property;
				$stringValue .= ": " . $value . ";\n";
			}
			
			$stringValue .= "}\n";
		}
		
		return $stringValue;
	}

	/**
	* Get the array with all readed data
	* 
	* @return Array data
	*/
	public function getCssData(){
		return $this->cssData;
	}

	/**
	* Set or add a selector with a property and a value 
	* @param String $selector	Name of the selector to added or edit
	* @param String $property	Name of the property to add or edit in the selector
	* @param String $value		The new value of the property
	* 
	* @return
	*/
	public function setProperty($selector, $property, $value){
		//remove all illegal character
		$selector = preg_replace('/[^\w&#\.\*\{\- ]/', "", $selector);
		$property = $this->escape($property);
		$value = preg_replace('/[^\w&#\(\)\/\.\;\-\% ]/', "", $value);
		
		//add new items if not exists
		if (!isset($this->cssData[$selector])){
				$this->cssData[$selector] = array();
		}
		if (!isset($this->cssData[$selector][$property])){
				$this->cssData[$selector][$property] = array();
		}
		
		//edit value
		$this->cssData[$selector][$property] = $value;
	}

	/**
	* Save all the data as valid css to a file. The file must be a existing file
	* @param String $filename Name and path of a existing file
	* 
	* @return
	*/
	public function save($filename){
		//save file if exists
		if (file_exists($filename)){
			$this->fileHandler = fopen($filename, "w");
			fwrite($this->fileHandler, $this->toString());
			fclose($this->fileHandler);
		}
	}
}
?>