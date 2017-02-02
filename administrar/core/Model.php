<?php

class Model extends Base {

	protected $_table;
	protected $_conexao;
	protected $_columns;
	protected $_primary;
	protected $_types = array( // tipos de dados que o modelo entende
		'autonumber',
		'longtext',
		'tinytext',
		'text',
		'integer',
		'decimal',
		'boolean',
		'datetime',
		'date'
	);
	
	public function __construct ($conexao) {
        
        // cria e inicializa objeto

        $this->_conexao = $conexao;

        // init and fill object

        $hasColumn = function($array, $column) {
    		return array_key_exists($column, $array) ? true : false;
		};

    	$columns = $this->_columns = $this->getColumns();

    	foreach ($columns as $key => $column) {
    		switch ($column['type']) {
    			
    			case 'autonumber' :
    				$this->$key = 0;
    			break;
    			
    			case 'text' :
    			case 'longtext' :
                case 'tinytext' :
    			case 'integer' :
    			case 'decimal' :
    			case 'date' :
    				$this->$key = '';
    			break;

    			case 'datetime' :
    				$this->$key = date('Y-m-d H:i:s', time());
    			break;
    		}
    	}

    	// adiciona data e timestamp
    	if ($hasColumn($columns, 'data')) {
			$this->timestamp = time();
			$this->data = date('Y-m-d H:i:s', $this->timestamp);
		} 

    }

    private function objetosQuery ($params = array()) {
    	try {

    		$filtro = isset($params['filtro']) ? $params['filtro'] : array('*');

    		$query = $this->getQuery($filtro);

    		if (isset($params['limit'])) {
    			$query->limitIn($params['limit'], $params['offset']);
    		}

    		if (isset($params['order'])) {
    			$query->order(key($params['order']), $params['order'][key($params['order'])]);
    		}

    		if (isset($params['where']) && count($params['where'])) {
    			foreach ($params['where'] as $key => $value) {
                    $value = trim($value);
                    if (is_array($value)) {
                        $arg = sprintf($key . ' IN (%s)', implode(',', $value));
                        $query->where($arg);
                    }
                    else if(!empty($value) && preg_match('/[a-z\s-]/i', $value)) {
                        $query->where($key . ' LIKE ?', '%' . Funcoes::codificaDado($value) . '%');
                    }
                    else if(preg_match('/\d{2}\/\d{2}\/\d{4}/', $value)) {
                        $query->where('DATE(' . $key . ') = ?', Funcoes::encodeDate($value));
                    }
                    else if(preg_match('/\d/', $value)) {
                        $query->where($key . ' = ?', (int) $value);
                    }
                    else if (!empty($value)) {
                        $query->where($key . ' = ?', $value);
                    }
				}
    		}
    		return $query;
    	}
    	catch (Exception $e) {
    		throw $e;
    	}
    }

    public function setTable ($table) {
        $this->_table = $table;
    }

    public function getQuery ($filtro = array('*')) {
        try {
            $query = $this->_conexao->query()->from($this->_table, $filtro);
            return $query;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function existe ($campo = 'nome') {
    	try {
    		return $this->getQuery()
    			->where($campo . ' = ?', $this->$campo)
    			->where('id <> ?', (int) $this->id)
    			->count() > 0;
    	}
    	catch (Exception $e) {
    		throw $e;
    	}
    }

    public function setDados ($dados = array()) {
    	if ($dados) {
    		//$columns = $this->getColumns();
            $columns = $this->_columns;
    		// para cada dado submetido ...
    		foreach ($dados as $key => $value) {
    			// se o dado é uma propriedade do objeto ...
    			if (array_key_exists($key, $columns)) {
    				$this->$key = $value;
    			}
    		}
    	}
    }

    public function count ($params = array()) {
    	try {
    		return $this->objetosQuery($params)->count();
    	}
    	catch (Exception $e) {
    		throw $e;
    	}
    }

    public function getObjeto ($id) {
    	try {
            $objeto = $this->getQuery()
                ->where('id = ?', (int) $id)
                ->first();
            return $this->decodificaDados($objeto, true);
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function getObjetoOrFail ($id) {
    	try {
            $objeto = $this->getQuery()
                ->where('id = ?', (int) $id)
                ->first();
            if (!$objeto) {
                throw new ModelNotFoundException();
            }
            return $this->decodificaDados($objeto, true);
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function getObjetos ($params = array()) {
    	try {
			$objetos = $this->objetosQuery($params)->all();
			foreach ($objetos as $key => $value) {
				$objetos[$key] = $this->decodificaDados($value);
			}
			return $objetos;
    	}
    	catch (Exception $e) {
    		throw $e;
    	}
    }

    public function get ($params = array()) {
        try {
            $objeto = $this->objetosQuery($params)->first();
            if ($objeto) {
                return $this->decodificaDados($objeto, true);
            }
            return NULL;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function getOrFail ($params = array()) {
        try {
            $objeto = $this->objetosQuery($params)->first();
            if (!$objeto) {
                throw new ModelNotFoundException();
            }
            return $this->decodificaDados($objeto, true);
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    /**
    *	Função que decodifica os dados que vem do banco de dados. Realiza casts, conversões, 
    *	decodificação de htmlentities, dentre outros. 
    *	@param $dados array    Os dados que devem ser decodificados
    *	@param $fill  booolean Determina se o objeto deve ou não ser preenchido com os dados 
    */
    public function decodificaDados ($dados = array(), $fill = false) {

    	//$columns = $this->getColumns();
        $columns = $this->_columns;
    	$obj = $fill ? $this : new stdClass;

    	foreach ($dados as $key => $dado) {
            
            if (!array_key_exists($key, $columns)) {
                continue;
            }

    		switch ($columns[$key]['type']) {
    			
    			case 'autonumber' :
    			case 'integer' :
    				$obj->$key = (int) $dado;
    			break;
    			
    			case 'text' :
    				if ($columns[$key]['length'] || !$columns[$key]['editor']) {
    					$obj->$key = html_entity_decode($dado, ENT_NOQUOTES, 'utf-8');
    				}
    				else {
    					$obj->$key = $dado;
    				}
    			break;

    			case 'datetime' :
    				if (!empty($dado)) {
    					$obj->$key = date('d/m/Y H:i:s', strtotime($dado));
    				}
    				else {
    					$obj->$key = '';
    				}
    			break;

    			case 'date' :
                    if ($dado == '0000-00-00' || $dado == NULL) {
                        $obj->$key = '';
                    } 
                    else {
                        $obj->$key = date('d/m/Y', strtotime($dado));
                    }
    			break;

    			case 'decimal' :
    				$obj->$key = number_format($dado, 2, ",", ".");
    			break;

    			default :
    				$obj->$key = $dado;
    			break;
    		}
    	}

    	return $obj;
    }

    public function salvar () {

    	$cadastrando = true;
    	//$columns = $this->getColumns();
        $columns = $this->_columns;

    	$getPrimary = function($array) {
    		foreach ($array as $key => $field) {
    			if (array_key_exists('primary', $field)) {
    				return $field;
    			}
    		}
			return null;
		};

		$hasColumn = function($array, $column) {
    		return array_key_exists($column, $array) ? true : false;
		};

		$primary = $getPrimary($columns);
		$primaryName = $primary['name'];
		
		$query = $this->_conexao->query()->from($this->_table);
		
		if ($this->$primaryName > 0) {
			$cadastrando = false;
			$query->where($primaryName . ' = ?', (int) $this->$primaryName);
		}
		
		// retira o id
		array_shift($columns);
		
		$data = array();
		foreach ($columns as $key => $column) {

			if ($column['read'] == 1) {

				$type = $column['type'];
				$prop = $column['name'];
				$length = $column['length'];
				$editor = $column['editor']; // ckeditor

				if ($type == 'autonumber' || $type == 'integer') {
					$data[$key] = (int) $this->$prop;
				}
				else if (in_array($type, array('text', 'longtext')) && !$editor) {
                    if (empty($this->$prop)) {
                        $data[$key] = NULL;
                    }
                    else {
                        $data[$key] = Funcoes::codificaDado($this->$prop);
                    }
				}
				else if ($type == 'datetime') {
					if (preg_match ('/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}$/', $this->$prop)) {
						list($date, $hour) = explode(' ', $this->$prop);
						$this->$prop = implode('-', array_reverse(explode('/', $date))) . ' ' . $hour;
					}
					$data[$key] = $this->$prop;
				}
                else if ($type == 'decimal') {
                    $data[$key] = Funcoes::converteDecimal($this->$prop);
                }
				else if ($type == 'date') {
					if (preg_match ('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $this->$prop)) {
						$this->$prop = implode('-', array_reverse(explode('/', $this->$prop)));
					}
                    else if (empty($this->$prop)) {
                        $this->$prop = NULL;
                    }
                    $data[$key] = $this->$prop;
				}
				else {
					$data[$key] = $this->$prop;
				}
			}
		}

		$result = $query->save($data);
		
		if ($result > 1) {
			// last insert id
			$this->$primaryName = $result;
		}

		return $result;
	}

	public function excluir () {
		try {
			return $this->getQuery()
				->where('id = ?', (int) $this->id)
				->delete();
		}
		catch (Exception $e) {
			throw $e;
		}
	}
	
	public function getColumns () {

		$primaries = 0;
		$columns = array();
		// recupera o nome da classe (Model que está sendo manipulado)
		$class = get_class($this);
		// armazena os tipos de dados que o modelo entende numa variável
		$types = $this->_types;
		// cria o objeto inspector
		$inspector = new Inspector($this);
		// recupera as propriedades da classe através do objeto Inspector
		$properties = $inspector->getClassProperties();
		
		// função interna pra retornar o primeiro elemento do array
		$first = function($array, $key) {
			if (!empty($array[$key]) && sizeof($array[$key]) == 1) {
				return $array[$key][0];
			}
			return null;
		};
		
		foreach ($properties as $property) {
			$propertyMeta = $inspector->getPropertyMeta($property);
			// se tiver a marcação @column
			if (!empty($propertyMeta['@column'])) {
				$name = preg_replace('#^_#', '', $property);
				$primary = !empty($propertyMeta['@primary']);
				$foreign = !empty($propertyMeta['@foreign']);
				$references = $first($propertyMeta, '@references');
				$delete = $first($propertyMeta, '@delete');
				$update = $first($propertyMeta, '@update');
				$type = $first($propertyMeta, '@type');
				$length = $first($propertyMeta, '@length');
				$index = !empty($propertyMeta['@index']);
				$readwrite = !empty($propertyMeta['@readwrite']);
				$read = !empty($propertyMeta['@read']) || $readwrite;
				$write = !empty($propertyMeta['@write']) || $readwrite;
				$validate = !empty($propertyMeta['@validate']) ? $propertyMeta['@validate'] : false;
				$label = $first($propertyMeta, '@label');
				$planilha = !empty($propertyMeta['@planilha']);
				$editor = !empty($propertyMeta['@editor']);
				if (!in_array($type, $types)) {
					throw new Exception ($type . ' is not a valid type');
				}
				$columns[$name] = array(
					'raw' => $property,
					'name' => $name,
					'primary' => $primary,
					'foreign' => $foreign,
					'type' => $type,
					'references' => $references,
					'delete' => $delete,
					'update' => $update,
					'length' => $length,
					'index' => $index,
					'read' => $read,
					'write' => $write,
					'validate' => $validate,
					'label' => $label,
					'planilha' => $planilha,
					'editor' => $editor
				);
				if ($primary) {
					$primaries++;
				}
			}
		}
		
		if ($primaries !== 1) {
			throw new Exception ('{$class} must have exactly one @primary column');
		}	
		
		$this->_columns = $columns;
		return $this->_columns;
		
	}


	
}

?>