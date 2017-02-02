<?php

class Regiao extends Model {
	
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
    * @length 100
    */
    protected $_nome;

    protected $_table = 'regioes';
	
}


?>