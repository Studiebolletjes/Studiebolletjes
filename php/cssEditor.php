<?php
class CssEditor {
	const FILTERPATTERN = '/[^\w&#\.\{\}\:\;\-]/';
	private $cssData;
	private $fileHandler;
	private $selector;
	private $property;
	private $value;
	
	public function __construct($fileName){
		$this->cssData = array();
		if (file_exists($fileName)){
			$this->loadFile(fopen($fileName, 'r'));
		}
	}
	
	private function escape($value){
		return preg_replace(self::FILTERPATTERN, "", $value);
	}
	
	private function loadFile($fileHandler){
		if ($fileHandler == FALSE){
			return FALSE;
		}
		
		$this->fileHandler = $fileHandler;
		while (!feof($this->fileHandler)){
			$this->selector = "";
			$this->readSelector();
		}
		
		fclose($this->fileHandler);
	}
	
	private function readCharacter($pattern){
		if ($pattern == FALSE){
			$pattern = self::FILTERPATTERN;
		}
		
		if (!feof($this->fileHandler)){
			return preg_replace($pattern, "", fgetc($this->fileHandler));
		} else {
			return FALSE;
		}
	}
	
	private function readSelector() {
		if (strlen($this->selector) > 0){
			$character = $this->readCharacter('/[^\w&#\.\{\- ]/');
		} else {
			$character = $this->readCharacter(NULL);
		}
		
		if ($character === FALSE){
			return;
		}
		elseif ($character == "{"){
			$this->selector = rtrim($this->selector);
			$this->cssData[$this->selector] = array();
			$this->property = "";
			$this->readProperty();
		} else {
			$this->selector .= $character;
			$this->readSelector();
		}
	}
	
	private function readProperty(){
		$character = $this->readCharacter(NULL);
		if ($character === FALSE){
			return;
		}
		elseif ($character == "}"){
			$this->selector = "";
			$this->property = "";
			$this->value = "";
			return;
		}
		elseif ($character != ":"){
			$this->property .= $character;
			$this->readProperty();
		} else {
			//$this->property = $this->escape($this->property);
			$this->cssData[$this->selector][$this->property] = "";
			$this->value = "";			
			$this->readValue();
			$this->readProperty();
		}
	}
	
	private function readValue(){
		$character = $this->readCharacter('/[^\w&#\.\;\-\% ]/');
		if ($character === FALSE){
			return;
		}
		elseif ($character != ";"){
			$this->value .= $character;
			$this->readValue();
		} else {
			//$this->value = $this->escape($this->value);
			$this->cssData[$this->selector][$this->property] = ltrim($this->value);
			$this->property = "";
			$this->value = "";
		}
	}
	
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

	public function getCssData(){
		return $this->cssData;
	}

	public function setProperty($selector, $property, $value){
		$selector = preg_replace('/[^\w&#\.\{\- ]/', "", $selector);
		$property = $this->escape($property);
		$value = preg_replace('/[^\w&#\.\;\-\% ]/', "", $value);
		
		if (!isset($this->cssData[$selector])){
				$this->cssData[$selector] = array();
		}
		if (!isset($this->cssData[$selector][$property])){
				$this->cssData[$selector][$property] = array();
		}
		
		$this->cssData[$selector][$property] = $value;
	}

	public function save($filename){
		if (file_exists($filename)){
			$this->fileHandler = fopen($filename, "w");
			fwrite($this->fileHandler, $this->toString());
			fclose($this->fileHandler);
		}
	}
}
?>