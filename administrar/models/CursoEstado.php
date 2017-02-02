<?php
/*
	Classe que mapeia em quais estados um curso está disponível
*/
class CursoEstado extends Model {
    
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
    private $_curso;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    private $_estado;

    protected $_table = 'cursos_estados';

    
}

?>