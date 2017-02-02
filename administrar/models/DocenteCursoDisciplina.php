<?php

class DocenteCursoDisciplina extends Model {
    
    /**
    * @column
    * @readwrite
    * @primary
    * @type autonumber
    */
    protected $_id;
    
    /**
    * @index
    * @column
    * @readwrite
    * @type integer
    * @foreign
    * @references docentes (id)
    * @delete cascade
    * @update cascade
    */
    protected $_docente;

    /**
    * @column
    * @readwrite
    * @type text
    */
    protected $_curso;
    
    /**
    * @column
    * @readwrite
    * @type text
    */
    protected $_disciplinas;

    protected $_table = 'docentes_cursos_disciplinas';

}

?>