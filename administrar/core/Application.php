<?php

class Application {

    protected $controller;
    protected $action;
  
    private function loadRoute() {
	
		$modulo = isset($_GET['modulo']) ? $_GET['modulo'] : 'Index';
        $acao = isset($_GET['acao']) ? $_GET['acao'] : 'index';
		
		if (!isset($_SESSION[PREFIX . "loginId"]) && $modulo != "login") {
			header("Location: ?modulo=login");
			exit;
		}
	
        $this->controller = $modulo;
        $this->action = $acao;
		
    }
  
    public function dispatch() {
	
        $this->loadRoute();

        $controllerArquivo = 'controllers/' . ucwords($this->controller) . 'Controller.php';
      
        if(file_exists($controllerArquivo)) {
            require_once $controllerArquivo;
       	}
        else {
            throw new Exception('Arquivo ' . $controllerArquivo . ' nao encontrado');
       	}
  
        $controllerClasse = $this->controller . 'Controller';
        
        if(class_exists($controllerClasse)) {
            $controllerObj = new $controllerClasse($this->controller, $this->action);
       	}
        else {
            throw new Exception("Classe '$controllerClasse' nao existe no arquivo '$controllerArquivo'");
       	}
  	
        $metodo = $this->action . 'Action';
		
        if(method_exists($controllerObj,$metodo)) {
            $controllerObj->$metodo();
        }
        else {
        	throw new Exception("Metodo '$metodo' nao existe na classe '$controllerClasse'");
       	}
    }
  
    public static function redirect($uri) {
        header("Location: $uri");
    }
}
?>