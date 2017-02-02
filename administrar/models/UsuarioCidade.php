<?php
/*
	Classe que mapeia as cidades que um usuário do sistema pode visualizar
*/

class UsuarioCidade extends Model {
    
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
    private $_cidade;

    protected $_table = 'usuarios_cidades';
      
}

?>