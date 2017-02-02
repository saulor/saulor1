<?php

class Mysql extends Connector {

	protected $_service;

	/**
	* @readwrite
	*/
	protected $_host;

	/**
	* @readwrite
	*/
	protected $_username;

	/**
	* @readwrite
	*/
	protected $_password;

	/**
	* @readwrite
	*/
	protected $_schema;

	/**
	* @readwrite
	*/
	protected $_port = "3306";

	/**
	* @readwrite
	*/
	protected $_charset = "utf8";

	/**
	* @readwrite
	*/
	protected $_engine = "InnoDB";

	/**
	* @readwrite
	*/
	protected $_isConnected = false;

	/*
	* Método que verifica se há conexão com o banco de dados
	*/
	protected function _isValidService () {
		$isEmpty = empty($this->_service);
		$isInstance = $this->_service instanceof MySQLi;
		if ($this->_isConnected && $isInstance && !$isEmpty) {
			return true;
		}
		return false;
	}

	/*
	* Método que salva a transação atual
	*/
	public function commit () {
		$this->_service->commit();
	}

	/*
	* Método que retorna o banco de dados ao estado anterior
	*/
	public function rollback () {
		$this->_service->rollback();
	}

	/*
	* Método que retorna um objeto Query
	*/
	public function query () {
		return new Query(array(
			"connector" => $this
		));
	}

	// escapes the provided value to make it safe for queries
	public function escape ($value) {
		if (!$this->_isValidService()) {
			throw new Exception("Not connected to a valid service");
		}
		return $this->_service->real_escape_string($value);
	}

	/*
	* Método que realiza a conexão ao banco de dados
	*/
	public function connect () {
		if (!$this->_isValidService()) {
			$this->_service = @new MySQLi(
				$this->_host,
				$this->_username,
				$this->_password,
				$this->_schema,
				$this->_port
			);

			if ($this->_service->connect_error) {
				die('<!DOCTYPE HTML><html><head><meta http-equiv="Content-Type" content="text/html;
					charset=utf-8" /><title>Erro</title></head><body><div style="margin-left:auto;
					margin-right:auto; color:#990000; font-size:large;"><p>Erro: Impossível conectar ao banco de
					dados</p></div></body></html>');
			}
			else {
				$this->_isConnected = true;
				$this->_service->autocommit(false);
			}
		}
		return $this;
	}

	/*
	* Método que desconecta do banco de dados
	*/
	public function disconnect () {
		if ($this->_isValidService()) {
			$this->_isConnected = false;
			$this->_service->close();
		}
		return $this;
	}

	/*
	* Método que executa uma Query SQL
	*/
	public function execute ($sql) {
		if (!$this->_isValidService()) {
			throw new Exception("Not connected to a valid service");
		}
		return $this->_service->query($sql);
	}


	/*
	* Método que exclui as constraints de chave estrangeira
	*/
	public function prepare ($model) {
		$columns = $model->getColumns();
		$table = $model->getTable();
		//print_r($columns);die;
		foreach ($columns as $column) {
			if ($column["foreign"]) {
				$result = $this->execute("ALTER TABLE {$table} DROP FOREIGN KEY {$table}_ibfk_1;");
			}
		}
		return $this;
	}

	/*
	* Método que realiza a sincronização do banco de dados, através da criação das tabelas
	*/
	public function sync ($model) {

		$lines = array();
		$indices = array();
		$columns = $model->getColumns();
		$table = $model->getTable();
		$template = "CREATE TABLE %s (%s,\n%s\n) ENGINE=%s DEFAULT CHARSET=%s;";

		foreach ($columns as $column) {
			$raw = $column["raw"];
			$name = $column["name"];
			$type = $column["type"];
			$length = $column["length"];

			if ($column["primary"]) {
				$indices[] = "PRIMARY KEY ({$name})";
			}

			if ($column["index"]) {
				$indices[] = "KEY {$name} ({$name})";
			}

			switch ($type) {
				case "autonumber" :	{
					$lines[] = "{$name} int NOT NULL AUTO_INCREMENT";
					break;
				}
				case "text" : {
					if ($length !== null && $length <= 255) {
						$lines[] = "{$name} varchar({$length})";
					}
					else {
						$lines[] = "{$name} text";
					}
					break;
				}
				case "longtext" : {
					$lines[] = "{$name} longtext DEFAULT NULL";
					break;
				}
				case "tinytext" : {
					$lines[] = "{$name} tinytext DEFAULT NULL";
					break;
				}
				case "integer" : {
					//$lines[] = "'{$name}' int(11) DEFAULT NULL";
					$lines[] = "{$name} int DEFAULT 0";
					break;
				}
				case "decimal" : {
					//$lines[] = "'{$name}' float DEFAULT NULL";
					//$lines[] = "{$name} float";
					$lines[] = "{$name} decimal({$length},2)";
					break;
				}
				case "boolean" : {
					//$lines[] = "'{$name}' tinyint(4) DEFAULT NULL";
					$lines[] = "{$name} tinyint";
					break;
				}
				case "datetime" : {
					//$lines[] = "'{$name}' datetime DEFAULT NULL";
					$lines[] = "{$name} datetime";
					break;
				}
				case "timestamp" : {
					//$lines[] = "'{$name}' datetime DEFAULT NULL";
					$lines[] = "{$name} timestamp";
					break;
				}
				case "date" : {
					//$lines[] = "'{$name}' datetime DEFAULT NULL";
					$lines[] = "{$name} date";
					break;
				}
			}
		}

		$result = $this->execute("DROP TABLE IF EXISTS {$table};");

		if ($result === false) {
			$error = $this->last_error;
			throw new Exception ("There was an error in the query: {$error}");
		}

		$sql = sprintf(
			$template,
			$table,
			join(",\n", $lines),
			join(",\n", $indices),
			$this->_engine,
			$this->_charset
		);

		$result = $this->execute($sql);

		if ($result === false) {
			//$error = $this->last_error;
			//echo $sql;
			//throw new Exception ("There was an error in the query: {$error}");
			throw new Exception ("There was an error in the query");
		}

		//echo $sql."<br /><br />";

		return $this;

	}

	/*
	* Método que cria as constraints de chave estrangeira
	*/
	public function syncConstraint ($model) {
		$foreign = array();
		$columns = $model->getColumns();
		$table = $model->getTable();
		$templateForeign = "ALTER TABLE %s ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s ON DELETE %s ON UPDATE %s;";

		foreach ($columns as $column) {
			if ($column["foreign"]) {
				$foreign = array(
					"table" => $table,
					"column" => $column["name"],
					"references" => $column["references"],
					"delete" => $column["delete"],
					"update" => $column["update"],
				);
			}
		}

		//print_r($foreign);

		if (count($foreign) > 0) {
			$sqlForeign = sprintf(
				$templateForeign,
				$table,
				$table . "_ibfk_1",
				$foreign["column"],
				$foreign["references"],
				$foreign["delete"],
				$foreign["update"]

			);

			//echo $sqlForeign."<br /><br />";

			$result = $this->execute($sqlForeign);
			if ($result === false) {
				$error = $this->last_error;
				throw new Exception ("There was an error in the query: {$error}");
			}
		}

	}

	public function getLastInsertId() {
		if (!$this->_isValidService()) {
			throw new Exception ("Not connected to a valid service");
		}
		return $this->_service->insert_id;
	}

	// returns the number of rows affected
	// by the last SQL query executed
	public function getAffectedRows() {
		if (!$this->_isValidService()) {
			throw new Exception ("Not connected to a valid service");
		}
		return $this->_service->affected_rows;
	}

	// returns the last error of occur
	public function getLastError() {
		if (!$this->_isValidService()) {
			throw new Exception ("Not connected to a valid service");
		}
		return $this->_service->error;
		//return $this->_service->errno;
	}

}

?>
