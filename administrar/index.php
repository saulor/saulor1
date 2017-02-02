<?php
require_once 'config.php';
require_once 'funcoes.php';
require_once ('core/ClassesLoader.php');
ClassesLoader::Register();
Session::init();

try {
	if (isset($_SESSION[PREFIX . "loginId"])) {
		if (isset($_SESSION[PREFIX . "tempoLogado"]) && time() - $_SESSION[PREFIX . "tempoLogado"] > (60*60)) {
			header("Location: sair.php?foi=1");
		}
		$_SESSION[PREFIX . "tempoLogado"] = time();	
	}
	$application = new Application();
	$application->dispatch();
}
catch (Exception $e) {
	echo $e->getMessage();
	Funcoes::setMensagem("error", $e->getMessage());
	//header("Location: ?modulo=erro");
}
?>