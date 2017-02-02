<?php

class Log extends Model {
    
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
    */
    protected $_onde;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_acao;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_oque;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_qual;
    
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
    * @type text
    */
    protected $_descricao;
    
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
    protected $_timestamp;

    protected $_table = 'logs';

    const LOG_ONDE_SITE = 1;
    const LOG_ONDE_PAINEL = 2;

    public static function getOnde ($onde) {
        switch ($onde) {
            case Log::LOG_ONDE_PAINEL :
                return "Painel de Administracão";
            break;
            
            case Log::LOG_ONDE_SITE :
                return "Site";
            break;
        }
    }

    public function adicionar ($acao, $oQue, $qual, $descricao = null) {
        try {
            $timestamp = time();
            $this->_conexao->query()
                ->from("logs")
                ->save(array(
                    "acao" => Funcoes::codificaDado($acao),
                    "oque" => Funcoes::codificaDado($oQue),
                    "quem" => Funcoes::codificaDado($_SESSION[PREFIX . "loginNome"]),
                    "qual" => Funcoes::codificaDado($qual),
                    "descricao" => Funcoes::codificaDado($descricao),
                    "timestamp" => $timestamp,
                    "data" => date('Y-m-d H:i:s', $timestamp)
                )
            );
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function site ($quem, $acao, $oQue, $qual, $descricao = null) {
        try {
            $timestamp = time();
            $this->_conexao->query()
                ->from("logs")
                ->save(array(
                    "onde" => (int) Log::LOG_ONDE_SITE,
                    "quem" => Funcoes::codificaDado($quem),
                    "acao" => Funcoes::codificaDado($acao),
                    "oque" => Funcoes::codificaDado($oQue),
                    "qual" => Funcoes::codificaDado($qual),
                    "descricao" => Funcoes::codificaDado($descricao),
                    "timestamp" => $timestamp,
                    "data" => date('Y-m-d H:i:s', $timestamp)
                )
            );
        }
        catch (Exception $e) {
            throw $e;
        }
    }
    
    
}

?>