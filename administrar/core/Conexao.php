<?php

class Conexao {	
	
	protected $conexao;
	
	public function Conexao () {
		
		$configuracoesDb = new Configuration(array(
				"type" => "ini"
			)
		);
		$configuracoesDb = $configuracoesDb->initialize()->parse('db');

		$databaseFactory = new Database();
		$databaseFactory->setType('mysql');
		$databaseFactory->setOptions(array(
			"host" => $configuracoesDb->database->host,
			"username" => $configuracoesDb->database->username,
			"password" => $configuracoesDb->database->password,
			"schema" => $configuracoesDb->database->schema
		));
		$this->conexao = $databaseFactory->initialize();
		$this->conexao = $this->conexao->connect();
	}
	
	public function getConexao() {
		return $this->conexao;
	}
	
}

?>