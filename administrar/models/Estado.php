<?php

class Estado extends Model {

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
    * @length 2
    */
    protected $_sigla;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 100
    */
    protected $_nome;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    private $_regiao;

   protected $_table = 'estados';

}


?>