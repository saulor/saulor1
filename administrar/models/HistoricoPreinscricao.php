<?php

class HistoricoPreinscricao extends Model {
	
    /**
    * @column
    * @readwrite
    * @primary
    * @type autonumber
    */
    protected $_id;
    
    /**
    * @column
    * @readwrite
    * @type integer
    * @index
    * @foreign
    * @references preinscricoes (id)
    * @delete cascade
    * @update cascade
    */
    protected $_preinscricao;
    
	/**
    * @column
    * @readwrite
    * @type text
    */
    protected $_descricao;
	
	/**
    * @column
    * @readwrite
    * @type text
	* @length 255
    */
    protected $_quem;
    
    /**
    * @column
    * @readwrite
    * @type datetime
    */
    protected $_data;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_timestampData;

    protected $_table = 'historico';

    // public function __construct ($conexao) {
    //     $this->_conexao = $conexao;
    // }

    // public function getQuery ($filtro = array('*')) {
    //     try {
    //         $query = $this->_conexao->query()->from($this->_table, $filtro);
    //         return $query;
    //     }
    //     catch (Exception $e) {
    //         throw $e;
    //     }
    // }

    public function adicionar ($preinscricao, $descricao) {
        try {
            $timestamp = time();
            $this->_conexao->query()
                ->from("historico")
                ->save(array(
                    "preinscricao" => (int) $preinscricao,
                    "descricao" => codificaDado($descricao),
                    "quem" => codificaDado($_SESSION[PREFIX . "loginNome"]),
                    "timestamp" => $timestamp,
                    "data" => date('Y-m-d H:i:s', $timestamp)
                )
            );
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function findOrFail ($id) {
        try {
            $objeto = $this->getQuery()
                ->where('id = ?', (int) $id)
                ->first();
            
            if (!$objeto) {
                throw new Exception('Histórico não localizado');
            }

            $objeto = array_map('decode', $objeto);
            return $objeto;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

}

?>