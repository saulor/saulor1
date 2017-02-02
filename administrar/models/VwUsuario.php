<?php

class VwUsuario extends Model {
	
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
    * @type text
    * @length 100
    */
    protected $_login;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 32
    */
    protected $_senha;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_email;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_filtrar;
    
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
    protected $_status;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_idPermissao;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_nomePermissao;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_codigoPermissao;

    protected $_table = 'vw_usuarios';
	
}

?>