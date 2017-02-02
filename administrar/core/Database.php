<?php

class Database extends Base {

	/**
	* @readwrite
	*/
	protected $_type;

	/**
	* @readwrite
	*/
	protected $_options;

	protected function _getExceptionForImplementation ($method) {
		//return new Exception\Implementation("{$method} method not implemented");
		return new Implementation("{$method} method not implemented");
	}
	
	/*
	* Returns a database connector subclass
	* Connectors are the classes that do the actual interfacing  with the database engine
	*/
	public function initialize () {
		
		if (!$this->_type) {
			//throw new Exception\Argument("Invalid type");
			throw new Exception ("Invalid type");
		}	
		
		switch ($this->_type) {
			case "mysql" : {
				return new Mysql($this->_options);
				break;
			}
			
			default : {
				//throw new Exception\Argument("Invalid type");
				throw new Exception ("Invalid type");
				break;
			}
		}
	}
	
}

?>