<?php

class DAOGenerico {

	public function DAOGenerico () {
	
	}
	
	public function getEmail($conexao, $chave) {
		$to = $this->find($conexao, "configuracoes", array(
				"where" => array(
					"chave" => $chave
				)
			)
		);
		if (count($to) == 0) {
			return "contato@iefap.com.br";
		}
		else {
			$emailsArr = array();
			$emails = explode(",", $to["valor"]);
			if (count($emails) > 1) {
				$ultimoEmail = array_pop($emails);
			}
			$emailsIn = implode(", ", $emails);
			if (isset($ultimoEmail)) {
				$emailsIn .= " e " . $ultimoEmail;
			}
			return array(
				"to" => $to["valor"],
				"emails" => $emailsIn
			);
		}
		
	}
	
	public function execute ($conexao, $sql) {
		$dados = array();
		$result = $conexao->execute($sql);
		if ($result === false) {
			$error = $conexao->getLastError();
		    setMensagem("error", "There was an error with your SQL query: {$error}");
		}
		for ($i=0; $i<$result->num_rows; $i++) {
		    $dados[] = $result->fetch_array(MYSQLI_ASSOC);
		}
		return $dados;
	}
	
	public function mountQuery($conexao, $tabela, $params) {
	
		if (!array_key_exists("dados", $params)) {
			$params["dados"] = array("*");
		}
		
		$query = $conexao->query()->from($tabela, $params["dados"]);
		
		if (array_key_exists("join", $params)) {
			foreach ($params["join"] as $key => $value) {	
				$query = $query->join($key, $value);	
			}
		}
		
		if (array_key_exists("leftJoin", $params)) {
			foreach ($params["leftJoin"] as $key => $value) {	
				$query = $query->leftJoin($key, $value);	
			}
		}
		
		if (array_key_exists("rightJoin", $params)) {
			foreach ($params["rightJoin"] as $key => $value) {	
				$query = $query->rightJoin($key, $value);	
			}
		}
		
		if (array_key_exists("where", $params)) {
			foreach ($params["where"] as $key => $value) {
				if (is_numeric($value)) {
					$value = (int) $value;
				}
				$query = $query->where($key . " = ?", $value);
			}
		}
		
		if (array_key_exists("whereNot", $params)) {
			foreach ($params["whereNot"] as $key => $value) {
				if (is_numeric($value)) {
					$value = (int) $value;
				}	
				$query = $query->where($key . " <> ?", $value);	
			}
		}
			
		if (array_key_exists("whereIn", $params)) {
			foreach ($params["whereIn"] as $key => $value) {
				if (!empty($value)) {
					$query = $query->where($key . " in (" . $value .")");	
				}
			}
		}
		
		if (array_key_exists("whereNotIn", $params)) {
			foreach ($params["whereNotIn"] as $key => $value) {
				if (!empty($value)) {
					$query = $query->where($key . " not in (" . $value .")");	
				}
			}
		}
		
		if (array_key_exists("whereOr", $params)) {
			if (count($params["whereOr"]) > 0) {	
				$query = $query->where("(" . implode(" or ", $params["whereOr"]) . ")");
			}
		}
		
		if (array_key_exists("whereLike", $params)) {
			foreach ($params["whereLike"] as $key => $value) {
				if (!empty($value)) {
					$query = $query->where($key . " LIKE ?", '%'. $value . '%');
					//$sql .= " AND " . $key . " like '%" . $value . "%'";
				}
			}
		}
		
		if (array_key_exists("whereBetween", $params)) {
			foreach ($params["whereBetween"] as $key => $value) {
				$query = $query->where($key . " BETWEEN ? AND ?", $value['valorInicial'], $value['valorFinal']);
			}
		}
		
		if (array_key_exists("limit", $params)) {
			$offset = isset($params["offset"]) ? $params["offset"] : 0;
			$query = $query->limitIn($params["limit"], $offset);
		}
		
		if (array_key_exists("order", $params)) {
			foreach ($params["order"] as $key => $value) {
				$query = $query->order($key, $value);
			}
		}
		
		if (array_key_exists("group", $params)) {
			foreach ($params["group"] as $group) {
				$query = $query->group($group);
			}
		}

		return $query;
	}
	
	public function count ($conexao, $tabela, $params = array()) {
		try {
			$query = $this->mountQuery($conexao, $tabela, $params);
			return $query->count();
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public function findByPk ($conexao, $tabela, $id = 0, $params = array()) {
		try {
			$infoCampos = $this->_infoCampos($conexao, $tabela, $params);
			$query = $this->mountQuery($conexao, $tabela, $params);
			$query = $query->where($tabela . ".id = ?", (int) $id);
			$dados = $query->first();
			if (count($dados) == 0) {
				throw new Exception ("Objeto não encontrado");
			}
			return $this->_decodificaDados($conexao, $dados, $infoCampos);
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	public function find ($conexao, $tabela, $params = array()) {
		try {
			$infoCampos = $this->_infoCampos($conexao, $tabela, $params);
			$params = $this->_codificaWheres($conexao, $tabela, $params);
			//print_r($infoCampos);die;
			$query = $this->mountQuery($conexao, $tabela, $params);
			$dados = $query->first();
			if (count($dados) == 0) {
				return array();
			}
			else {
				return $this->_decodificaDados($conexao, $dados, $infoCampos);
			}
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	public function findAll ($conexao, $tabela, $params = array()) {
		try {
			$infoCampos = $this->_infoCampos($conexao, $tabela, $params);
			$params = $this->_codificaWheres($conexao, $tabela, $params);
			$query = $this->mountQuery($conexao, $tabela, $params);
			$dados = $query->all();
			return $this->_decodificaDados($conexao, $dados, $infoCampos);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public function salva ($conexao, $tabela, $dados = array()) {
		try {
			// variavel para armazenar os dados de retorno
			$retorno = array();
			// retira o id do array de dados
			$id = array_shift($dados);
			// armazena os dados sem codificação na variável $dadosRetorno
			$dadosRaw = $dados;
			// codifica os dados (htmlentities, converte datas, etc)
			$dados = $this->_codificaDados($conexao, $tabela, $dados);
			// inicia processamento da query
			$query = $conexao->query()->from($tabela);
			// se o id for diferente de zero significa que o procedimento é uma atualização
			if ($id != 0) {
				$query->where("id = ?", (int) $id);
			}
			$newId = $query->save($dados);
			if ($newId != 0 && $id == 0) {
				$id = $newId;
			}
			$retorno["id"] = $id;
			$retorno = array_merge($retorno, $dadosRaw);
			return $retorno;
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	// função criada para 
	public function salva2 ($conexao, $tabela, $dados, $params = array()) {
		try {
			$dados = $this->_codificaDados($conexao, $tabela, $dados);
			$query = $this->mountQuery($conexao, $tabela, $params);
			return $query->save($dados);
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	public function salvaTodos ($conexao, $tabela, $dados = array()) {
		try {
			foreach ($dados as $dado) {
				$id = array_shift($dado);
				$query = $conexao->query()
					->from($tabela);
				if ($id != 0) {
					$query->where("id = ?", (int) $id);
				}
				$query->save($dado);
			}
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	public function excluiByPk ($conexao, $tabela, $id) {
		try {
			return $conexao->query()
					->from($tabela)
					->where("id = ?", (int) $id)
					->delete();
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	public function exclui ($conexao, $tabela, $params = array()) {
		try {
			$query = $this->mountQuery($conexao, $tabela, $params);
			return $query->delete();
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	public function excluiTodos ($conexao, $tabela, $dados = array()) {
		try {
			$affectedRows = 0;
			foreach ($dados as $dado) {
				$affectedRows += $query = $conexao->query()
					->from($tabela)
					->where("id = ?", $dado["id"])
					->delete();
			}
			return $affectedRows;
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	private function _getInformacoesTabela($conexao, $tabela) {
		try {
			$sql = "SHOW COLUMNS FROM " . $tabela;
			$result = $conexao->execute($sql);
			$fields = array();
			for ($i=0; $i<$result->num_rows; $i++) {
				$r = $result->fetch_array(MYSQLI_ASSOC);
				$f[$r['Field']] = $r['Type'];
				$fields = $f;
			}
			return $fields;
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	private function _infoCampos ($conexao, $tabela, $params = array()) {
		try {
			$info = array();
			$nomeTabela = $tabela;
			
			if (!array_key_exists("dados", $params)) {
				foreach($this->getInformacoesTabela($conexao, $tabela) as $nomeCampo => $tipo) {
					$info[$nomeCampo] = $nomeTabela . "," . $nomeCampo;
				}
			}
			else {
				foreach ($params["dados"] as $campo) {
					$alias = $nomeCampo = $campo;
					if (preg_match("/(.*)\.(.*)/i", $campo)) {
						preg_match("/([A-Z_]*)\.([A-Z_*]*)/i", $campo, $results1);
						$nomeTabela = $results1[1];
						$alias = $nomeCampo = $results1[2];
						if (preg_match("/as (.*)/i", $campo)) {
							preg_match("/as ([A-Z_]*)/i", $campo, $results2);
							$alias = $results2[1];
						}
					}
					$info[$alias] = $nomeTabela . ',' . $nomeCampo;
				}
			}
			return $info;
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	private function _decodificaDados ($conexao, $dados, $infoCampos) {
		try {
			$infosTabela = array();
			foreach ($infoCampos as $key => $value) {
				list($tabela, $campo) = explode(",", $infoCampos[$key]);
				if (!array_key_exists($tabela, $infosTabela)) {
					$infosTabela[$tabela] = $this->getInformacoesTabela($conexao, $tabela);
				}
			}
			if (isset($dados[0]) && is_array($dados[0])) {
				foreach ($dados as $indice => $dado) {
					$dados[$indice] = $this->_applyDecodes($conexao, $dado, $infoCampos, $infosTabela);
				}
			}
			else {
				$dados = $this->_applyDecodes($conexao, $dados, $infoCampos, $infosTabela);
			}
			return $dados;
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	private function _applyDecodes($conexao, $dados, $infoCampos, $infosTabela) {
		foreach ($dados as $key => $value) {
			list($tabela, $campo) = explode(",", $infoCampos[$key]);
			if (strpos($infosTabela[$tabela][$campo], 'int') !== false) {
				$dados[$key] = (int) $value;
			}
			else if (strpos($infosTabela[$tabela][$campo], 'decimal') !== false) {
				$dados[$key] = desconverteDecimal($value);
			}
			else if ($infosTabela[$tabela][$campo] == 'text' && !is_html($value)) {
				$dados[$key] = html_entity_decode($value, ENT_NOQUOTES, 'utf-8');
			}
			else if ($infosTabela[$tabela][$campo] == 'date') {
				$dados[$key] = desconverteData($value);
			}
			else if ($infosTabela[$tabela][$campo] == 'time') {
				$dados[$key] = substr($value,0,5);
			}
			else if ($infosTabela[$tabela][$campo] == 'datetime') {
				$dados[$key] = desconverteDataTime($value);
			}
			else if ($infosTabela[$tabela][$campo] == 'varchar(255)') {
				$dados[$key] = html_entity_decode($value, ENT_NOQUOTES, 'utf-8');
			}
		}
		return $dados;
	}
	
			
	private function _codificaWheres($conexao, $tabela, $params) {
		try {

			$info = $this->getInformacoesTabela($conexao, $tabela);
			$infos[$tabela] = $info;
			
			if (array_key_exists("where", $params)) {
				foreach ($params["where"] as $key => $value) {
					
					if (preg_match("/(.*)\.(.*)/i", $key)) {
						preg_match("/([A-Z_]*)\.([A-Z]*)/i", $key, $results);
						$nomeTabela = $results[1];
						$nomeCampo = $results[2];
					}
					else {
						$nomeTabela = $tabela;
						$nomeCampo = $key;
					}

					if (!array_key_exists($nomeTabela, $infos)) {
						$info = $this->getInformacoesTabela($conexao, $nomeTabela);
						$infos[$nomeTabela] = $info;
					}
					
					if (!array_key_exists($nomeCampo, $infos[$nomeTabela])) {
						throw new Exception('Campo ' . $nomeCampo . ' não existe na tabela ' . $tabela);
					}
					
					if (strpos($infos[$nomeTabela][$nomeCampo], 'int') !== false) {
						$params["where"][$key] = (int) $value; 
					}
					else if (strpos($infos[$nomeTabela][$nomeCampo], 'decimal') !== false) {
						$params["where"][$key] = converteDecimal($value); 
					}
					else if ($infos[$nomeTabela][$nomeCampo] == 'text' && !is_html($value)) {
						$params["where"][$key] = htmlentities($value, ENT_NOQUOTES, 'utf-8');
					}
					else if ($infos[$nomeTabela][$nomeCampo] == 'date') {
						$params["where"][$key] = converteData($value);
					}
					else if ($infos[$nomeTabela][$nomeCampo] == 'datetime') {
						$params["where"][$key] = converteDataTime($value);
					}
					else if ($infos[$nomeTabela][$nomeCampo] == 'varchar(255)') {
						$params["where"][$key] = htmlentities($value, ENT_NOQUOTES, 'utf-8');
					}
				}
			}
			return $params;
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	private function _codificaDados($conexao, $tabela, $dados) {
		try {
			$info = $this->getInformacoesTabela($conexao, $tabela);
			foreach ($dados as $key => $value) {
				if (strpos($info[$key], 'int') !== false) {
					$dados[$key] = (int) $value; 
				}
				else if (strpos($info[$key], 'decimal') !== false) {
					$dados[$key] = converteDecimal($value); 
				}
				else if ($info[$key] == 'text' && !is_html($value)) {
					$dados[$key] = htmlentities($value, ENT_NOQUOTES, 'utf-8');
				}
				else if ($info[$key] == 'date') {
					$dados[$key] = converteData($value);
				}
				else if ($info[$key] == 'datetime') {
					$dados[$key] = converteDataTime($value);
				}
				else if ($info[$key] == 'varchar(255)') {
					$dados[$key] = htmlentities($value, ENT_NOQUOTES, 'utf-8');
				}
			}
			return $dados;
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	public function getInformacoesTabela($conexao, $tabela) {
		try {
			$sql = "SHOW COLUMNS FROM " . $tabela;
			//echo $sql . '<br>';
			$result = $conexao->execute($sql);
			$fields = array();
			for ($i=0; $i<$result->num_rows; $i++) {
				$r = $result->fetch_array(MYSQLI_ASSOC);
				//$f[$tabela . '.' . $r['Field']] = $r['Type'];
				$f[$r['Field']] = $r['Type'];
				$fields = $f;
			}
			return $fields;
		}
		catch (Exception $e) {
			throw $e;
		}
	}
}

?>