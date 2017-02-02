<?php

class Controller {
	
	protected $conexao;
	protected $log;
	protected $dao;
	
	public function Controller() {
		$this->conexao = new Conexao();
		$this->log = new Log($this->conexao->getConexao());
		$this->dao = new DAOGenerico();
	}
	
	public function checaPermissao($modulo, $acao) {
		if (!array_key_exists($modulo, $_SESSION[PREFIX . "permissoes"]) || !in_array($acao, $_SESSION[PREFIX . "permissoes"][$modulo])) {
			throw new PermissaoException("Você não tem permissão para realizar esta ação");
		}
	} 
	
}

?>