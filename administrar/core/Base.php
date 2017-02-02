<?php

class Base {
	
	private $_inspector; // armazena referência ao objeto inspector
	
	/*
	 * Construtor da classe Base
	*/
	public function __construct ($options = array()) {
		$this->_inspector = new Inspector($this);
		// se vierem parâmetros no construtor
		if (is_array($options) || is_object($options)) {
			foreach ($options as $key => $value) {
				$key = ucfirst($key);
				$method = "set{$key}";
				$this->$method($value);
			}
		}
	}
	
	public function __call ($name, $arguments) {
		
		if (empty($this->_inspector)) {
			throw new Exception ("Call parent::__construct!");
		}
		
		// verifica se existem comandos getVariavel
		$getMatches = StringMethods::match($name, "^get([a-zA-Z0-9]+)$");
		if (sizeof($getMatches) > 0) {
			// verifica qual propriedade esta sendo manipulada
			$normalized = lcfirst($getMatches[0]);
			$property = "_{$normalized}";
			
			if (property_exists($this, $property)) {
				$meta = $this->_inspector->getPropertyMeta($property);
				// se não tiver marcação definida
				if (empty($meta["@readwrite"]) && empty($meta["@read"])){
					throw $this->_getExceptionForWriteonly($normalized);
				}
				if (isset($this->$property)) {
					return $this->$property;
				}
				return null;
			}
		}
		
		// verifica se existem comandos setVariavel
		$setMatches = StringMethods::match($name, "^set([a-zA-Z0-9]+)$");
		if (sizeof($setMatches) > 0) {
			// verifica qual propriedade esta sendo manipulada
			$normalized = lcfirst($setMatches[0]);
			$property = "_{$normalized}";
			
			if (property_exists($this, $property)) {
				$meta = $this->_inspector->getPropertyMeta($property);
				// se não tiver marcação definida
				if (empty($meta["@readwrite"]) && empty($meta["@write"])){
					throw $this->_getExceptionForReadonly($normalized);
				}
				$this->$property = $arguments[0];
				return $this;
			}
		}
		
		// se a propriedade não existir		
		throw $this->_getExceptionForImplementation($name);
		
	}
	
	protected function _getExceptionForReadonly ($property){
		return new Exception ("{$property} is read-only");
	}
	
	protected function _getExceptionForWriteonly ($property) {
		return new Exception ("{$property} is write-only");
	}
	
	protected function _getExceptionForProperty () {
		return new Exception ("Invalid property");
	}
	
	protected function _getExceptionForImplementation ($method) {
		return new Exception ("{$method} method not implemented");
	}

}

?>