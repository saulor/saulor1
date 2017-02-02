<?php
/*
* Inspector class
*/

class Inspector {	
	
	protected $_class; // armazena referência a classe que vai ser inspecionada
	protected $_meta = array(
		"class" => array(),
		"properties" => array(),
		"methods" => array()
	);
	protected $_properties = array();
	protected $_methods = array();
	
	/*
	 * Construtor da classe Inspector
	*/
	public function __construct ($class) {
		$this->_class = $class;
	}
	
	/*
	 * Método que retorna o nome da classe.
	 * @return String
	*/
	protected function _getClassName () {
		$reflection = new ReflectionClass($this->_class);
		return $reflection->getName();
	}
	
	/*
	 * Método que retorna os comentários da classe.
	 * @return String
	 * 
	*/
	protected function _getClassComments () {
		$reflection = new ReflectionClass($this->_class);
		return $reflection->getDocComment();
	}
	
	/*
	 * Método protegido, interno à classe Inspector, que recupera as propriedades da classe através da 
	 * API Reflection.
	 * @return Retorna um array de objetos ReflectionProperties
	*/
	protected function _getClassProperties () {
		$reflection = new ReflectionClass($this->_class);
		return $reflection->getProperties();
	}
	
	/*
	 * Método público da classe Inspector semelhante ao método anterior _getClassProperties .
	 * @return Retorna um array de String com os nomes das propriedades
	*/
	public function getClassProperties() {
		$_properties = $this->_getClassProperties();
		$p = array();
		foreach ($_properties as $property) {
			$p[] = $property->getName();
		}
		return $p;
	}

	/*
	 * Método protegido, interno à classe Inspector, que retorna os métodos definidos na classe através da 
	 * API Reflection
	 * @return Retorna um array de objetos ReflectionMethod
	*/
	protected function _getClassMethods () {
		$reflection = new ReflectionClass($this->_class);
		return $reflection->getMethods();
	}
	
	/*
	 * Método público da classe Inspector semelhante ao método anterior _getClassMethods .
	 * @return Retorna um array de String com os nomes dos métodos
	*/	
	public function getClassMethods () {
		$methods = $this->_getClassMethods();
		$m = array();
		foreach ($methods as $method) {
			$m[] = $method->getName();
		}
		return $m;
	}
	
	/*
	 * Método protegido, interno à classe Inspector, que retorna os comentários de uma propriedade
	 * @return 
	*/
	protected function _getPropertyComment ($property) {
		$reflection = new ReflectionProperty($this->_class, $property);
		return $reflection->getDocComment();
	}

	/*
	 * Retorna os comentários de um método
	*/
	protected function _getMethodComment ($method) {
		$reflection = new ReflectionMethod($this->_class, $method);
		return $reflection->getDocComment();
	}
	
	/*
	 * Método que retorna os metadados de uma determinada classe 
	 * Os metadados são todas as marcações que começam com @
	*/
	public function getClassMeta () {
		if (!isset($_meta["class"])) {
			$comment = $this->_getClassComments();
			if (!empty($comment)) {
				$_meta["class"] = $this->_parse($comment);
			}
			else {
				$_meta["class"] = null;
			}
		}
		return $_meta["class"];
	}
	
	/*
	 * Método que retorna os metadados de uma determinada propriedade 
	 * Os metadados são todas as marcações que começam com @
	*/	
	public function getPropertyMeta ($property) {
		if (!isset($this->_meta["properties"][$property])) {
			$comment = $this->_getPropertyComment($property);
			if (!empty($comment)) {
				$this->_meta["properties"][$property] = $this->_parse($comment);
			}
			else {
				$this->_meta["properties"][$property] = null;
			}
		}
		return $this->_meta["properties"][$property];
	}
	
	/*
	 * Método que retorna os metadados de um método
	 * Os metadados são todas as marcações que começam com @
	*/
	public function getMethodMeta ($method) {
		if (!isset($this->_meta["actions"][$method])) {
			$comment = $this->_getMethodComment($method);
			if (!empty($comment)) {
				$this->_meta["methods"][$method] = $this->_parse($comment);
			}
			else {
				$this->_meta["methods"][$method] = null;
			}
		}
		return $this->_meta["methods"][$method];
	}	
	
	/*
	* Função recebe o comentário definido na propriedade da classe
	*/	
	protected function _parse ($comment) {
		$_meta = array();
		$pattern = "(@[a-zA-Z]+\s*[a-zA-Z0-9, ()_]*)";
		// procura o padrão @readwrite ou @read ou @write
		$matches = StringMethods::match($comment, $pattern);
		
		if ($matches != null) {
			foreach ($matches as $match) {
				$parts = ArrayMethods::clean(
					ArrayMethods::trim(
						StringMethods::split($match, "[\s]", 2)
					)
				);
				$_meta[$parts[0]] = true;
				if (sizeof($parts) > 1) {
					$_meta[$parts[0]] = ArrayMethods::clean(
						ArrayMethods::trim(
							StringMethods::split($parts[1], ",")
						)
					);
				}
			}
		}
		return $_meta;
	}
	
}

?>