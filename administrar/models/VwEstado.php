<?php

class VwEstado extends Model {
	
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
    * @type text
    * @length 255
    */
    protected $_sigla;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_nome;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_regiao;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_nomeRegiao;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_quantidadeCidades;

    protected $_table = 'vw_estados';
	
}

?>