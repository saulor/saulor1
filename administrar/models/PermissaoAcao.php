<?php

class PermissaoAcao extends Model {
    
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
    * @index
    * @foreign
    * @references permissoes (id)
    * @delete cascade
    * @update cascade
    */
    private $_permissao;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    private $_modulo;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    private $_acao;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    private $_liberado;

    protected $_table = 'permissoes_acao';

    /**
    *   Retorna as ações de determinada permissão
    *   @param $permissao Objeto permissão
    */
    public function getAcoes ($obj) {
        try {

            if (!is_object($obj)) {
                throw new Exception("Objeto permissão esperado");
            }

            $acoes = parent::getObjetos(array(
                    'where' => array(
                        'permissao' => $obj->id,
                        'liberado' => 1
                    )
                )
            );

            $array1 = $array2 = array();
            foreach ($acoes as $acao) {

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

}

?>