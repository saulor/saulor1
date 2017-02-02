<?php

class Permissao extends Model {

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
    protected $_codigo;
	
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

    protected $_table = 'permissoes';

    /**
    *   Retorna as unidades as quais o usuário tem acesso.
    */
    public function getAcoes () {
        try {
            $model = new PermissaoAcao($this->_conexao);
            $objetos = $model->getObjetos(array(
                    'where' => array(
                        'permissao' => $this->id,
                        'liberado' => 1
                    )
                )
            );

            $array1 = $array2 = array();
            foreach ($objetos as $acao) {

                $array1[] = $acao->modulo . ':' . $acao->acao;

                if (!array_key_exists($acao->modulo, $array2)) {
                    $array2[$acao->modulo] = array();
                }

                if (!in_array($acao->acao, $array2[$acao->modulo])) {
                    $array2[$acao->modulo][] = $acao->acao;
                }
            }

            return array(
                'formato1' => $array1,
                'formato2' => $array2
            );

        }
        catch (Exception $e) {
            throw $e;
        }

    }

    public function excluir () {
        try {
            if (in_array($this->codigo, array(2,6,32,33))) {
               throw new Exception('Permissão padrão do sistema não pode ser excluída');
            }
            return parent::excluir();
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    
	
}

?>