<?php

class Usuario extends Model {
	
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
    protected $_permissao;
    
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
    protected $_representante;

    protected $_table = 'usuarios';

    /**
    *   Retorna as unidades as quais o usuário tem acesso.
    */
    public function getUnidades () {
        try {
            $model = new UsuarioUnidade($this->_conexao);
            $objetos = $model->getObjetos(array(
                    'where' => array(
                        'usuario' => $this->id
                    )
                )
            );

            $array = array();
            foreach ($objetos as $obj) {
                $array[] = $obj->unidade;
            }

            return $array;

        }
        catch (Exception $e) {
            throw $e;
        }
    }

    /**
    *   Retorna os cursos os quais o usuário tem acesso.
    */
    public function getCursos () {
        try {
            $model = new UsuarioCurso($this->_conexao);
            $objetos = $model->getObjetos(array(
                    'where' => array(
                        'usuario' => $this->id
                    )
                )
            );

            $array = array();
            foreach ($objetos as $obj) {
                $array[] = $obj->curso;
            }

            return $array;

        }
        catch (Exception $e) {
            throw $e;
        }
    }
	
}

?>