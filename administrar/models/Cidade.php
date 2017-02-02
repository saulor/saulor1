<?php

class Cidade extends Model {
    
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

    protected $_table = "cidades";
      
}

?>