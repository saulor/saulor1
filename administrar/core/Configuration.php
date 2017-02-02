<?php

class Configuration extends Base {
	
	/**
	* @readwrite
	*/
	protected $_type;
	
	protected function _getExceptionForImplementation ($method) {
		//return new Exception\Implementation("{$method} method not implemented");
		return new Exception ("{$method} method not implemented");
	}
	
	public function initialize () {
		if (!$this->_type) {
			//throw new Exception\Argument("Invalid type");
			throw new Exception ("Invalid type");
		}
		switch ($this->_type) {
			case "ini" : {
				return new Ini ();
				break;
			}
			case "language" : {
				return new Language ();
				break;
			}
			default : {
				throw new Exception ("Invalid type");
				break;
			}
		}
	}

}

?>