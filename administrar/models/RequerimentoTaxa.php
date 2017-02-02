<?php

class RequerimentoTaxa extends Model {
    
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
    protected $_tipo;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_taxa;
  
}

?>