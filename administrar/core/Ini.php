<?php

class Ini extends Base {
	
	// Função faz o parse das configurações do arquivo .ini para um array de maneira recursiva
	protected function _pair ($config, $key, $value) {
		if (strstr($key, ".")) {
			$parts = explode(".", $key, 2);
			if (empty($config[$parts[0]])) {
				$config[$parts[0]] = array();
			}
			$config[$parts[0]] = $this->_pair($config[$parts[0]], $parts[1], $value);
		}
		else {
			$config[$key] = $value;
		}
		return $config;
	}

	public function parse ($path) {
		
		$path = DIR_ROOT . DS . "administrar" . DS . $path;
		
		// se $path for vazio
		if (empty($path)) {
			//throw new Exception\Argument("\$path argument is not valid");
			throw new Exception ("\$path argument is not valid");
		}
		
		// verifica se o parse já foi feito
		if (!isset($this->_parsed[$path])) {
			$config = array();
			// ativa o buffer de saída
			// enquanto estiver ativo a saída é armazenada em um buffer interno
			ob_start();
			// dá um include no arquivo .ini
			include("{$path}.ini");
			//include("{$path}.ini.php");
			// obtém o conteúdo do buffer de saída
			$string = ob_get_contents();
			// descarta o conteúdo e desativa o buffer de saída
			ob_end_clean();
			// transforma o conteúdo do buffer num array associativo chave => valor
			$pairs = parse_ini_string($string);
			
			// se não tiver nada aqui provavelmente foram encontrados erros de sintaxe
			if ($pairs == false) {
				//throw new Exception\Syntax("Could not parse configuration file");
				throw new Exception ("Could not parse configuration file");
			}
			
			// percorre o array
			foreach ($pairs as $key => $value) {
				$config = $this->_pair($config, $key, $value);
			}
			
			// transforma o array num objeto
			$this->parsed[$path] = ArrayMethods::toObject($config);
			
		}

		return $this->parsed[$path];
		
	}	
	
}

?>