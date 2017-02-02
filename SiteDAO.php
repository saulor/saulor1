<?php

class SiteDAO {

	private $conexao;
	private $table;
	private $columns;
	private $wheres = array();
	private $inWheres = array();
	private $orWheres = array();
	private $orders = array();

	public function __construct ($conexao) {
        $this->conexao = $conexao;
	}

	private function buildQuery ($params = array()) {
		try {
			$query = $this->conexao->query()
            	->from($this->table, $this->columns);

            if (isset($params["limit"])) {
            	$offset = isset($params["offset"]) ? $params["offset"] : 0;
            	$query->limitIn($params["limit"], $offset);
            }

            foreach ($this->orders as $key => $value) {
        		$query->order($key, $value);
        	}

        	foreach ($this->wheres as $key => $value) {
        		$query->where($key, $value);
        	}

        	foreach ($this->inWheres as $key => $value) {
        		if (is_array($value)) {
        			$numers = array_filter($value, 'is_numeric') === $value;
        			$query->where($key, array_map('intval', $value));
        		}
        		else {
        			$query->where($key, $value);
        		}	
        	}

        	foreach ($this->orWheres as $key => $value) {
        		if (is_array($value)) {
        			foreach ($value as $v) {
        				$query->orWhere($key, $v);
        			}
        		}
        	}

            return $query;
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	private function decodeEntities ($dado) {
		return html_entity_decode($dado, ENT_NOQUOTES, "utf-8");
	}

	public function table ($table, $columns = array('*')) {
		// nova consulta
		$this->wheres = $this->orWheres = $this->orders = array();
		$this->table = $table;
		$this->columns = $columns;
		return $this;
	}

	public function where ($column, $operator, $value) {
		if (strtolower($operator) == 'in') {
			$this->inWheres[$column . " " . $operator . " ?"] = $value;
		}
		if (strtolower($operator) == 'or') {
			$this->orWheres[$column . " = ?"] = $value;
		}
		else {
			$this->wheres[$column . " " . $operator . " ?"] = $value;
		}
		return $this; 
	}

	public function order ($order, $direction = "asc") {
        if (empty($order)) {
            throw new Exception("Invalid argument");
        }
        if (!array_key_exists($order, $this->orders)) {
        	$this->orders[$order] = $direction;
        }
	    return $this;
	}

	public function all ($params = array()) {
		try {
			$query = $this->buildQuery($params);
            $objetos = $query->all();
			foreach ($objetos as $key => $objeto) {
				$objetos[$key] = Funcoes::arrayToObject(array_map('self::decodeEntities', $objeto));
			};
			return $objetos;
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public function first ($params = array()) {
		try {
			$query = $this->buildQuery($params);
			$objeto = array_map('self::decodeEntities', $query->first());
			return Funcoes::arrayToObject($objeto);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public function firstOrFail ($params = array()) {
		try {
			$query = $this->buildQuery($params);
			$objeto = array_map('self::decodeEntities', $query->first());
			if (!$objeto) {
				throw new NotFoundException();
			}
			return Funcoes::arrayToObject($objeto);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public function count ($params = array()) {
		try {
			$query = $this->buildQuery($params);
			return $query->count();
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public function save ($dados) {
		try {
			// retira o id do array de dados
			$id = array_shift($dados);
			// armazena os dados sem codificação na variável $dadosRetorno
			$dadosRaw = $dados;
			// codifica os dados (htmlentities, converte datas, etc)
			// inicia processamento da query
			$query = $this->conexao->query()->from($this->table);
			// se o id for diferente de zero significa que o procedimento é uma atualização
			if ($id != 0) {
				$query->where("id = ?", (int) $id);
			}
			// salva
			$newId = $query->save($dados);
			// se for um cadastro atualiza com o novo id que acabou de ser gerado
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

}


?>