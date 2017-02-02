<?php

class VwUnidade extends Model {
	
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
    * @type text
    * @length 255
    */
    protected $_email;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_quantidadeCursos;
    
    /**
    * @column
    * @readwrite
    * @type datetime
    */
    protected $_dataC;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_timestampC;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 2
    */
    protected $_siglaEstado;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_idRegiao;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_nomeRegiao;

    protected $_table = 'vw_cidades_estados';
    
    public function getUnidades () {
        try {
            $unidades = parent::getObjetos(array(
                    'order' => array(
                        'nome' => 'asc'
                    )
                )
            );

            $uniArray = array();
            foreach ($unidades as $unidade) {
                $uniArray[$unidade->nomeRegiao][] = $unidade;
            }

            return $uniArray;

        }
        catch (Exception $e) {
            throw $e;
        }

    }
	
}

?>