<?php

class CursoCategoria extends Model {
    
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
    * @length 255
    */
    protected $_slug;
    
    /**
    * @column
    * @readwrite
    * @type longtext
    */
    protected $_descricao;
	
	/**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_pai;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_visivel;
    
    /**
    * @column
    * @readwrite
     * @type text
    * @length 255
    */
    protected $_profundidade;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_caminho;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_banner;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_imagem;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_dataCadastro;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_dataAtualizacao;

    protected $_table = "cursos_categorias";

    /**
    *   Monta um array de categorias onde o nome de cada categoria é concatenado com
    *   o nome das categorias pai.
    */
    public function listaPais () {
        try {
            $categorias = parent::getObjetos(array(
                    'order' => array(
                        'pai' => 'asc'
                    )
                )
            );
            foreach ($categorias as $key => $categoria) {
                if (!empty($categoria->caminho)) {
                    $caminhos = explode('/', $categoria->caminho);
                    array_shift($caminhos);
                    $nomes = array();
                    foreach ($caminhos as $id) {
                        $c = parent::getObjeto($id);
                        $nomes[] = $c->nome;
                    }
                    $categorias[$key]->nome = implode(' / ', $nomes);
                }
            }
            return $categorias;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    /**
     *  Monta a tabela com a lista das categorias e subcategorias
    */
    public static function listaCategorias ($categorias) {

        $branch = self::buildTree($categorias);

        function buildResult($branch, $subcategorias = false) {
            $result = '';
            foreach ($branch as $categoria) {
                $result .= '<tr><td>&nbsp;</td>';
                $result .= '<td';
                $result .= '>';
                if ($categoria["pai"] != 0) {
                    $result .= '<div style="width:13px;height:10px;border-bottom:1px solid #ccc;';
                    $result .= 'border-left:1px solid #ccc;float:left;margin-right:4px;';
                    $result .= 'margin-left:' . ($categoria["profundidade"] * 10) . 'px;';
                    $result .= '">&nbsp;</div>';
                }
                $result .= '<a href="?modulo=categorias&acao=cadastrar&id=' . $categoria["id"] . '">';
                $result .= $categoria["nome"] . '</a></td>';

                $result .= '<td align="center">';
                $result .= $categoria["quantidadeCursos"];
                $result .= '</td>';

                $result .= '<td align="center">';
                $result .= $categoria["quantidadeCursosPos"];
                $result .= '</td>';

                $result .= '<td align="center">';
                $result .= $categoria["quantidadeCursosApe"];
                $result .= '</td>';

                $result .= '<td align="center">';
                $result .= Funcoes::decodeDate(Funcoes::dateFromTimestamp($categoria["dataCadastro"]));
                $result .= '</td>';

                $result .= '<td class="icons">';
                /*$result .= '<div class="icon">';
                $result .= '<a href="?modulo=categorias&acao=cadastrar&id=' . $categoria["id"] . '">';
                $result .= '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
                $result .= '</div>';
                $result .= '<div class="icon">';
                $result .= '<a ';
                $result .= 'onclick="excluir(\'categorias\', ';
                $result .= '{\'acao\' : \'excluir\', \'id\' : \'';
                $result .= $categoria["id"] . '\'});return false;"';
                $result .= 'href="?modulo=categorias&acao=excluir&id=' . $categoria["id"];
                $result .= '">';
                $result .= '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>';
                $result .= '</div>';
                $result .= '<div class="icon">';
                $result .= '<a ';
                //$result .= 'title="';
                //$result .= $categoria["visivel"] == 1 ? 'Desabilitar' : 'Habilitar';
                //$result .= ' categoria ' . $categoria["nome"] . '"';
                $result .= 'data-visivel="' . $categoria["visivel"] . '" ';
                $result .= 'id="' . $categoria["id"] . '"';
                $result .= ' class="visible';
                if ($subcategorias) {
                    $result .= ' subcategoria' . substr($categoria["caminho"],1,1);
                }
                $result .= '" href="">';
                $result .= '<span class="glyphicon glyphicon-eye-';
                $result .= $categoria["visivel"] == 1 ? 'open' : 'close';
                $result .= '" aria-hidden="true"></span></a>';
                $result .= '</div>';*/
                $result .= '</td>';
                
                if (array_key_exists("subcategorias", $categoria)) {
                    $result .= buildResult($categoria["subcategorias"], true);
                }
            }
            $result .= '</tr>';
            return $result;
        }

        $result = buildResult($branch);

        return $result;
    }

    /**
    *   Monta um array que representa a árvore de categorias e subcategorias.
    *   @param $elements Array de categorias
    *   @param $pai Id do elemento pai
    */
    public static function buildTree (array $elements, $pai = 0) {
        $branch = array();
        foreach ($elements as $element) {
            if ($element["pai"] == $pai) {
                $children = self::buildTree($elements, $element["id"]);
                if ($children) {
                    $element["subcategorias"] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    public static function criaCaminho ($conexao, $dao, $categoria) {
        try {
            if (empty($categoria["pai"])) {
                return '/' . $categoria["id"];
            }
            else {
                $c = $dao->findByPk($conexao, "cursos_categorias", $categoria["pai"]);
                return self::criaCaminho($conexao, $dao, $c) . '/' . $categoria["id"];
            }
        }
        catch (Exception $e) {
            throw $e;
        }
    }  

    /**
    *   Atualiza uma categoria e suas subcategorias, se existirem.
    *   @param $conexao Conexão com o banco de dados.
    *   @param id Id da categoria.
    */
    public static function atualizarSubcategorias ($conexao, $dao, $oldCaminho, $oldPai, $newPai) {
        try {

            $subcategorias = $conexao->query()
                ->from("cursos_categorias")
                ->where("caminho like ?", '%' . $oldCaminho . '%')
                ->all();

            $pattern = '/' . $oldPai . '/';

            foreach ($subcategorias as $s) {
                $caminho = preg_replace($pattern, $newPai, $s["caminho"]);
                $conexao->query()
                    ->from("cursos_categorias")
                    ->where("id = ?", (int) $s["id"])
                    ->save(array(
                        "caminho" => $caminho,
                    )
                );
            }
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public static function atualizaQuantidades ($conexao) {
        try {

            $categorias = $conexao->query()
                ->from("cursos_categorias")
                ->all();

            $categorias = CursoCategoria::buildTree($categorias);

            $c = array();

            foreach ($categorias as $categoria) {

                $c[$categoria["id"]]["quantidadeCursos"] = (int) $conexao->query()
                    ->from("cursos")
                    ->where("categoria = ?", (int) $categoria["id"])
                    ->count();

                $c[$categoria["id"]]["quantidadeCursosVisiveis"] = (int) $conexao->query()
                    ->from("cursos")
                    ->where("categoria = ?", (int) $categoria["id"])
                    ->where("status = 1")
                    ->count();

                $c[$categoria["id"]]["quantidadeCursosPos"] = (int) $conexao->query()
                    ->from("cursos")
                    ->where("categoria = ?", (int) $categoria["id"])
                    ->where("tipo = ?", Curso::CURSO_TIPO_POS)
                    ->count();

                $c[$categoria["id"]]["quantidadeCursosPosVisiveis"] = (int) $conexao->query()
                    ->from("cursos")
                    ->where("categoria = ?", (int) $categoria["id"])
                    ->where("tipo = ?", Curso::CURSO_TIPO_POS)
                    ->where("status = 1")
                    ->count();

                $c[$categoria["id"]]["quantidadeCursosApe"] = (int) $conexao->query()
                    ->from("cursos")
                    ->where("categoria = ?", (int) $categoria["id"])
                    ->where("tipo = ?", Curso::CURSO_TIPO_APERFEICOAMENTO)
                    ->count();

                $c[$categoria["id"]]["quantidadeCursosApeVisiveis"] = (int) $conexao->query()
                    ->from("cursos")
                    ->where("categoria = ?", (int) $categoria["id"])
                    ->where("tipo = ?", Curso::CURSO_TIPO_APERFEICOAMENTO)
                    ->where("status = 1")
                    ->count();

                if (isset($categoria["subcategorias"])) {
                    foreach ($categoria["subcategorias"] as $subcategoria) {
                        
                        $c[$categoria["id"]]["quantidadeCursos"] += (int) $conexao->query()
                            ->from("cursos")
                            ->where("categoria = ?", (int) $subcategoria["id"])
                            ->count();

                        $c[$subcategoria["id"]]["quantidadeCursos"] = (int) $conexao->query()
                            ->from("cursos")
                            ->where("categoria = ?", (int) $subcategoria["id"])
                            ->count();

                        $c[$categoria["id"]]["quantidadeCursosVisiveis"] += (int) $conexao->query()
                            ->from("cursos")
                            ->where("categoria = ?", (int) $subcategoria["id"])
                            ->where("status = 1")
                            ->count();

                        $c[$subcategoria["id"]]["quantidadeCursosVisiveis"] = (int) $conexao->query()
                            ->from("cursos")
                            ->where("categoria = ?", (int) $subcategoria["id"])
                            ->where("status = 1")
                            ->count();

                        $c[$categoria["id"]]["quantidadeCursosPos"] += (int) $conexao->query()
                            ->from("cursos")
                            ->where("categoria = ?", (int) $subcategoria["id"])
                            ->where("tipo = ?", (int) Curso::CURSO_TIPO_POS)
                            ->count();

                        $c[$subcategoria["id"]]["quantidadeCursosPos"] = (int) $conexao->query()
                            ->from("cursos")
                            ->where("categoria = ?", (int) $subcategoria["id"])
                            ->where("tipo = ?", (int) Curso::CURSO_TIPO_POS)
                            ->count();

                        $c[$categoria["id"]]["quantidadeCursosPosVisiveis"] += (int) $conexao->query()
                            ->from("cursos")
                            ->where("categoria = ?", (int) $subcategoria["id"])
                            ->where("tipo = ?", (int) Curso::CURSO_TIPO_POS)
                            ->where("status = 1")
                            ->count();

                        $c[$subcategoria["id"]]["quantidadeCursosPosVisiveis"] = (int) $conexao->query()
                            ->from("cursos")
                            ->where("categoria = ?", (int) $subcategoria["id"])
                            ->where("tipo = ?", (int) Curso::CURSO_TIPO_POS)
                            ->where("status = 1")
                            ->count();

                        $c[$categoria["id"]]["quantidadeCursosApe"] += (int) $conexao->query()
                            ->from("cursos")
                            ->where("categoria = ?", (int) $subcategoria["id"])
                            ->where("tipo = ?", (int) Curso::CURSO_TIPO_APERFEICOAMENTO)
                            ->count();

                        $c[$subcategoria["id"]]["quantidadeCursosApe"] = (int) $conexao->query()
                            ->from("cursos")
                            ->where("categoria = ?", (int) $subcategoria["id"])
                            ->where("tipo = ?", (int) Curso::CURSO_TIPO_APERFEICOAMENTO)
                            ->count();

                        $c[$categoria["id"]]["quantidadeCursosApeVisiveis"] += (int) $conexao->query()
                            ->from("cursos")
                            ->where("categoria = ?", (int) $subcategoria["id"])
                            ->where("tipo = ?", (int) Curso::CURSO_TIPO_APERFEICOAMENTO)
                            ->where("status = 1")
                            ->count();

                        $c[$subcategoria["id"]]["quantidadeCursosApeVisiveis"] = (int) $conexao->query()
                            ->from("cursos")
                            ->where("categoria = ?", (int) $subcategoria["id"])
                            ->where("tipo = ?", (int) Curso::CURSO_TIPO_APERFEICOAMENTO)
                            ->where("status = 1")
                            ->count();
                    } 
                }
            }

            foreach ($c as $id => $quantidades) {
                $conexao->query()
                    ->from("cursos_categorias")
                    ->where("id = ?", (int) $id)
                    ->save($quantidades);
            }

        }
        catch (Exception $e) {
            throw $e;
        }
    }


}

?>