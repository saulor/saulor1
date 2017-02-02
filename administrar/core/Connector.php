<?php

class Connector extends Base {

	public function initialize()	{
		return $this;
	}
	
	protected function _getExceptionForImplementation($method)	{
		//return new Exception\Implementation("{$method} method not implemented");
		return new Exception ("{$method} method not implemented");
	}
	
}

?>