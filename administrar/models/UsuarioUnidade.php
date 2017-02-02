<?php

class UsuarioUnidade extends Model {
    
    /**
    * @column
    * @readwrite
    * @primary
    * @type autonumber
    */
    private $_id;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    private $_usuario;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    private $_unidade;

    protected $_table = 'usuarios_unidades';
      
}

?>