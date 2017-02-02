<?php

class Query extends Base {

	/**
	* @readwrite
	*/
	protected $_connector;

	/**
	* @read
	*/
	protected $_from;

	/**
	* @read
	*/
	protected $_fields;

	/**
	* @read
	*/
	protected $_limit;

	/**
	* @read
	*/
	protected $_offset;

	/**
	* @read
	*/
	protected $_order;
	
	/**
	* @read
	*/
	protected $_group;

	/**
	* @read
	*/
	protected $_direction;

	/**
	* @read
	*/
	protected $_join = array();

	/**
	* @read
	*/
	protected $_where = array();
	
	/**
	* @read
	*/
	protected $_orders = array();
	
	protected function _quote ($value) {
	
		if (is_string($value)) {
			$escaped = $this->_connector->escape($value);
			return "'{$escaped}'";
		}
		
		if (is_array($value)) {
			$buffer = array();
			foreach ($value as $i) {
				array_push($buffer, $this->_quote($i));
			}
			$buffer = join(", ", $buffer);
			return "({$buffer})";
		}
		
		if (is_null($value)) {
			return "NULL";
		}
		
		if (is_bool($value)) {
			return (int) $value;
		}
		
		return $this->_connector->escape($value);
        
	}	

	public function from ($from, $fields = array("*")) {
        if (empty($from)) {
            throw new Exception ("Invalid argument");
        }
        $this->_from = $from;
        if ($fields) {
            $this->_fields[$from] = $fields;
        }
        return $this;
	}
	
	public function join($join, $on, $fields = array()) {
        if (empty($join)) {
            throw new Exception ("Invalid argument");
        }
        if (empty($on)) {
            throw new Exception ("Invalid argument");
        }
        $this->_fields += array($join => $fields);
        $this->_join[] = "JOIN {$join} ON {$on}";
        return $this;
	}
	
	public function leftJoin($join, $on, $fields = array()) {
	    if (empty($join)) {
	        throw new Exception ("Invalid argument");
	    }
	    if (empty($on)) {
	        throw new Exception ("Invalid argument");
	    }
	    $this->_fields += array($join => $fields);
	    $this->_join[] = "LEFT JOIN {$join} ON {$on}";
	    return $this;
	}
	
	public function rightJoin($join, $on, $fields = array()) {
	    if (empty($join)) {
	        throw new Exception ("Invalid argument");
	    }
	    if (empty($on)) {
	        throw new Exception ("Invalid argument");
	    }
	    $this->_fields += array($join => $fields);
	    $this->_join[] = "RIGHT JOIN {$join} ON {$on}";
	    return $this;
	}
	
	public function limit($limit, $page = 1) {
        //if (empty($limit)) {
        //	throw new Exception("Invalid argument");
        //}
        $this->_limit = $limit;
        //$this->_offset = $limit * ($page - 1);
        $this->_offset = $limit * ($page - 1);
        //echo 'limit '.$this->_limit.'<br>';
        //echo 'offset '.$this->_offset.'<br>';
        return $this;
	}
        
    public function limitIn($limit, $offset) {
        $this->_limit = $limit;
        //$this->_offset = ($limit == $offset) ? 0 : $offset;
        $this->_offset = $offset;
        return $this;
	}
	
	public function order($order, $direction = "asc") {
        if (empty($order)) {
            throw new Exception("Invalid argument");
        }
        $this->_order = $order;
        $this->_direction = $direction;
        if (!in_array($this->_order . " " . $this->_direction, $this->_orders)) {
        	$this->_orders[] = $this->_order . " " . $this->_direction;
        }
	    return $this;
	}
	
	public function group($group) {
	    $arguments = func_get_args();
	    if (sizeof($arguments) < 1) {
	        throw new Exception("Invalid argument");
	    }
	    $arguments[0] = preg_replace("#\?#", "%s", $arguments[0]);
	    foreach (array_slice($arguments, 1, null, true) as $i => $parameter) {
	        $arguments[$i] = $this->_quote($arguments[$i]);
	    }
	    $this->_group[] = call_user_func_array("sprintf", $arguments);
	    return $this;
	}	

	public function where() {
        $arguments = func_get_args();
        //print_r($arguments);echo '<br />';
        if (sizeof($arguments) < 1) {
            throw new Exception("Invalid argument");
        }
        $arguments[0] = preg_replace("#\?#", "%s", $arguments[0]);
        foreach (array_slice($arguments, 1, null, true) as $i => $parameter) {
            $arguments[$i] = $this->_quote($arguments[$i]);
        }
        $this->_where[] = call_user_func_array("sprintf", $arguments);
		return $this;
	}

    public function orWhere() {
        $arguments = func_get_args();
        if (sizeof($arguments) < 1) {
            throw new Exception("Invalid argument");
        }

        if (preg_match('/^\%[\w\s]+\%$/i', Funcoes::removeAcentos($arguments[1]))) {
            $arguments[0] = preg_replace("#=#", "LIKE", $arguments[0]);
        }

        $arguments[0] = preg_replace("#\?#", "%s", $arguments[0]);
        
        foreach (array_slice($arguments, 1, null, true) as $i => $parameter) {
            $arguments[$i] = $this->_quote($arguments[$i]);
        }
        $this->_orWhere[] = call_user_func_array("sprintf", $arguments);
        return $this;
    }
	
	protected function _buildSelect () {
        $fields = array();
        $where = $order = $limit = $join = $group = "";
        $template = "SELECT %s FROM %s %s %s %s %s %s";
        
        foreach ($this->_fields as $table => $_fields) {
            foreach ($_fields as $field => $alias) {
                if (is_string($field)) {
                    $fields[] = "{$field} AS {$alias}";
                }
                else {
                    $fields[] = $alias;
                }
            }
        }

        $fields = join(", ", $fields);
        $_join = $this->_join;
        
        if (!empty($_join)) {
            $join = join(" ", $_join);
        }
        
        $_where = $this->_where;
        
        if (!empty($_where)) {
            $joined = join(" AND ", $_where);
            $where = "WHERE {$joined}";
        }
        
        $_orWhere = $this->_orWhere;

        if (!empty($_orWhere)) {
            $joined = join(" OR ", $_orWhere);
            if ($where) {
                $where .= " AND ({$joined})";
            }
            else {
                $where = " WHERE ({$joined})";
            }
        }
        
        $_order = $this->_orders;
        
        if (!empty($_order)) {
            $joined = join(", ", $_order);
            $order = "ORDER BY {$joined}";
        }
        
        $_group = $this->_group;
        
        if (!empty($_group)) {
        	$joined = join(", ", $_group);
            $group = "GROUP BY {$joined}";
        }
        
        $_limit = $this->_limit;
        
        if (!empty($_limit)) {
            $_offset = $this->_offset;
            if ($_offset) {
                $limit = "LIMIT {$_limit}, {$_offset}";
            }
            else {
                $limit = "LIMIT {$_limit}";
            }
        }
       	//echo htmlspecialchars(sprintf($template, $fields, $this->_from, $join, $where, $group, $order, $limit)).'<br />';
        return sprintf($template, $fields, $this->_from, $join, $where, $group, $order, $limit);
		
	}
	
	public function all () {
        $sql = $this->_buildSelect();
        //echo htmlspecialchars($sql) . '<br />';
        $result = $this->_connector->execute($sql);
        if ($result === false) {
            $error = $this->_connector->getLastError();
            //throw new Exception ("There was an error with your SQL query: {$error}");
           	//echo htmlspecialchars($sql);die;
            throw new Exception("Erro ao tentar executar comando [1]");
        }
        $rows = array();
        for ($i=0; $i<$result->num_rows; $i++) {
            $rows[] = $result->fetch_array(MYSQLI_ASSOC);
        }
        return $rows;
	}
	
	public function save ($data) {
        $isInsert = sizeof($this->_where) == 0;
        if ($isInsert) {
            $sql = $this->_buildInsert($data);
        }
        else {
            $sql = $this->_buildUpdate($data);
        }
        //echo $sql . '<br />';die;
        $result = $this->_connector->execute($sql);
        if ($result === false) {
            //throw new Exception ("There was an error with your SQL query: {$sql}");
			//echo htmlspecialchars($sql);die;
            throw new Exception("Erro ao tentar executar comando [2]");
        }
        if ($isInsert) {
            return $this->_connector->getLastInsertId();
        }
        else {
            return $this->_connector->getAffectedRows();
        }
	}
	
	protected function _buildInsert ($data) {
        $fields = array();
        $values = array();
        //$template = "INSERT INTO '%s' ('%s') VALUES (%s)";
        $template = "INSERT INTO %s (%s) VALUES (%s)";
        foreach ($data as $field => $value) {
            $fields[] = $field;
            $values[] = $this->_quote($value);
        }
        //$fields = join("', '", $fields);
        $fields = join(", ", $fields);
        $values = join(", ", $values);
        return sprintf($template, $this->_from, $fields, $values);
	}
	
	protected function _buildUpdate ($data) {
        $parts = array();
        $where = $limit = "";
        $template = "UPDATE %s SET %s %s %s";
        foreach ($data as $field => $value) {
            $parts[] = "{$field} = ".$this->_quote($value);
        }
        $parts = join(", ", $parts);
        $_where = $this->_where;
        if (!empty($_where)) {
            $joined = join(" AND ", $_where);
            $where = "WHERE {$joined}";
        }
        $_limit = $this->_limit;
        if (!empty($_limit)) {
            $_offset = $this->offset;
            $limit = "LIMIT {$_limit} {$_offset}";
        }
        return sprintf($template, $this->_from, $parts, $where, $limit);
	}
	
	public function delete() {
        $sql = $this->_buildDelete();
        //echo $sql . '<br />';die;
        $result = $this->_connector->execute($sql);
        if ($result === false) {
            $error = $this->_connector->getLastError();
            //echo $sql;die;
            //throw new DeleteForeignException ("There was an error with your SQL query: {$error}");
            // código 1451
            throw new Exception ("Registro n&atilde;o pode ser exclu&iacute;do: {$error}");
            //throw new Exception("Erro ao tentar executar comando [3]");
        }
        return $this->_connector->getAffectedRows();
	}

	protected function _buildDelete() {
        $where = $limit = "";
        $template = "DELETE FROM %s %s %s";
        $_where = $this->_where;
        if (!empty($_where)) {
            $joined = join(" AND ", $_where);
            $where = "WHERE {$joined}";
        }
        $_limit = $this->_limit;
        if (!empty($_limit)){
            $_offset = $this->_offset;
            $limit = "LIMIT {$_limit} {$_offset}";
        }
        return sprintf($template, $this->_from, $where, $limit);
	}	
	
	public function first() {
        $limit = $this->_limit;
        $offset = $this->_offset;
        $this->limit(1);
        $all = $this->all();
        //$first = ArrayMethods::first($all);
        if (count($all) > 0) {
            $first = $all[0];
            if ($limit) {
                $this->_limit = $limit;
            }
            if ($offset) {
                $this->_offset = $offset;
            }
            return $first;
        }
        return NULL;
	}

	public function count() {
        $limit = $this->_limit;
        $offset = $this->_offset;
        $fields = $this->_fields;
        $this->_fields = array($this->_from => array("COUNT(1)" => "rows"));
        $this->limit(1);
        $row = $this->first();
        $this->_fields = $fields;
        if ($fields) {
            $this->_fields = $fields;
        }
        if ($limit) {
            $this->_limit = $limit;
        }
        if ($offset) {
            $this->_offset = $offset;
        }
        return $row["rows"];
	}	
	
}

?>