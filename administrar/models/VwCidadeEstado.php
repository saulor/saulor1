<?php

class VwCidadeEstado extends Model {
	
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
    protected $_nome;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_estado;
    
    /**
    * @column
    * @readwrite
    * @type tinytext
    */
    protected $_emails;

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

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_idEstado;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 2
    */
    protected $_siglaEstado;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_nomeEstado;

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
    protected $_quantidadeCursos;

    /**
    * @column
    * @readwrite
    * @type longtext
    */
    private $_cursos;

    protected $_table = 'vw_cidades_estados';
	
}

?>