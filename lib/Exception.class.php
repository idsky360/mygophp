<?php
class Exception extends Exception{

	public function __construct($message,$code=500,$previous=null){
		parent::__construct($message,$code);
	}
}
