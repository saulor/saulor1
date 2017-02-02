<?php
/*
	Classe que mapeia os cursos que um usuário do sistema pode visualizar
*/
class UsuarioCurso extends Model {
    
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
    * @type integer
    */
    private $_curso;

    protected $_table = 'usuarios_cursos';

}

?>