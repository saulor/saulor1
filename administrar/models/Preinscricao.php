<?php

class Preinscricao extends Model {

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
    * @type integer
    */
    protected $_curso;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_unidade;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_unidadeCertificadora;

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
    * @length 9
    */
    protected $_sexo;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 15
    */
    protected $_estadoCivil;

	/**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_profissao;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 50
    */
    protected $_rg;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 100
    */
    protected $_orgaoExpedidor;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 2
    */
    protected $_ufExpedidor;

    /**
    * @column
    * @readwrite
    * @type date
    */
    protected $_dataExpedicao;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_naturalidade;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 14
    */
    protected $_cpf;

    /**
    * @column
    * @readwrite
    * @type date
    */
    protected $_dataNascimento;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $endereco;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 20
    */
    protected $_numero;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_complemento;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_bairro;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_cidade;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 2
    */
    protected $_uf;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 30
    */
    protected $_cep;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 20
    */
    protected $_telefone;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 20
    */
    protected $_telefoneResidencial;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 5
    */
    protected $_operadoraCelular;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 20
    */
    protected $_telefoneCelular;

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
    * @type text
    * @length 255
    */
    protected $_emailAlternativo;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_nomePai;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_nomeMae;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_formacao;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_instituicao;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 4
    */
    protected $_anoConclusao;

    /*
    * @column
    * @readwrite
    * @type integer
    * @index
    * @foreign
    * @references cursos_estados_cidades (id)
    * @delete restrict
    * @update cascade
    */
    protected $_cidadeCurso;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_formaPagamento;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_banco;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_diaPagamento;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_comoConheceu;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_nomeIndicou;

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
    * @type text
    * @length 255
    */
    protected $_turma;

    /**
    * @column
    * @readwrite
    * @type text
    */
    protected $_observacoes;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_enviouComprovante;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_comprovante;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 100
    */
    protected $_mime;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 5
    */
    protected $_extensao;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_responsavel;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_horario;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_whatsapp;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_visualizada;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_situacao;

    protected $_table = 'preinscricoes';

	const PREINSCRICAO_STATUS_INTERESSADO = 1;
	const PREINSCRICAO_STATUS_INSCRITO = 2;
	const PREINSCRICAO_STATUS_PREMATRICULADO = 3;
	const PREINSCRICAO_STATUS_CONTRATO = 4;
	const PREINSCRICAO_STATUS_CURSANDO = 5;
	const PREINSCRICAO_STATUS_DESISTENTE = 6;
	const PREINSCRICAO_STATUS_CONCLUIDO = 7;
	const PREINSCRICAO_STATUS_CANCELADO = 8;

    const PREINSCRICAO_BANCO_BRADESCO = 1;
    const PREINSCRICAO_BANCO_ITAU = 2;
    const PREINSCRICAO_BANCO_BANCO_DO_BRASIL = 3;
    const PREINSCRICAO_BANCO_CAIXA_ECONOMICA_FEDERAL = 4;

    public static function getConstanteStatus ($status) {

    	$status = mb_strtolower($status);

    	$pattern = '/^' . $status . '/';

    	if (preg_match($pattern, "interessado", $matches, PREG_OFFSET_CAPTURE))
    		return Preinscricao::PREINSCRICAO_STATUS_INTERESSADO;
    	else if (preg_match($pattern, "inscrição paga", $matches, PREG_OFFSET_CAPTURE))
    		return Preinscricao::PREINSCRICAO_STATUS_INSCRITO;
    	else if (preg_match($pattern, "inscricao paga", $matches, PREG_OFFSET_CAPTURE))
    		return Preinscricao::PREINSCRICAO_STATUS_INSCRITO;
    	else if (preg_match($pattern, "contrato assinado", $matches, PREG_OFFSET_CAPTURE))
    		return Preinscricao::PREINSCRICAO_STATUS_PREMATRICULADO;
    	else if (preg_match($pattern, "contrato assinado e taxa de inscrição paga", $matches, PREG_OFFSET_CAPTURE))
    		return Preinscricao::PREINSCRICAO_STATUS_CONTRATO;
    	else if (preg_match($pattern, "contrato assinado e taxa de inscricao paga", $matches, PREG_OFFSET_CAPTURE))
    		return Preinscricao::PREINSCRICAO_STATUS_CONTRATO;
    	else if (preg_match($pattern, "cursando", $matches, PREG_OFFSET_CAPTURE))
    		return Preinscricao::PREINSCRICAO_STATUS_CURSANDO;
    	else if (preg_match($pattern, "desistente", $matches, PREG_OFFSET_CAPTURE))
    		return Preinscricao::PREINSCRICAO_STATUS_DESISTENTE;
    	else if (preg_match($pattern, "concluído", $matches, PREG_OFFSET_CAPTURE))
    		return Preinscricao::PREINSCRICAO_STATUS_CONCLUIDO;
    	else if (preg_match($pattern, "concluido", $matches, PREG_OFFSET_CAPTURE))
    		return Preinscricao::PREINSCRICAO_STATUS_CONCLUIDO;
    	else if (preg_match($pattern, "cancelado", $matches, PREG_OFFSET_CAPTURE))
    		return Preinscricao::PREINSCRICAO_STATUS_CANCELADO;
    }

	public static function getStatus ($status) {
		switch ($status) {
			case Preinscricao::PREINSCRICAO_STATUS_INTERESSADO :
				return "Interessado";
			break;
			case Preinscricao::PREINSCRICAO_STATUS_INSCRITO :
				return "Inscrição paga";
			break;
			case Preinscricao::PREINSCRICAO_STATUS_PREMATRICULADO :
				return "Contrato assinado";
			break;
			case Preinscricao::PREINSCRICAO_STATUS_CONTRATO :
				return "Contrato assinado e taxa de inscrição paga";
			break;
			case Preinscricao::PREINSCRICAO_STATUS_CURSANDO :
				return "Cursando";
			break;
			case Preinscricao::PREINSCRICAO_STATUS_DESISTENTE :
				return "Desistente";
			break;
			case Preinscricao::PREINSCRICAO_STATUS_CONCLUIDO :
				return "Concluído";
			break;
			case Preinscricao::PREINSCRICAO_STATUS_CANCELADO :
				return "Cancelado";
			break;
			default :
				return "Não definido";
			break;
		}
	}

	public static function getBanco ($banco) {

		switch ($banco) {
			case Preinscricao::PREINSCRICAO_BANCO_BRADESCO :
				return "Bradesco";
			break;
			case Preinscricao::PREINSCRICAO_BANCO_ITAU :
				return "Itaú";
			break;
			case Preinscricao::PREINSCRICAO_BANCO_BANCO_DO_BRASIL :
				return "Banco do Brasil";
			break;
			case Preinscricao::PREINSCRICAO_BANCO_CAIXA_ECONOMICA_FEDERAL :
				return "Caixa Econômica Federal";
			break;
			default :
				return "Não definido";
			break;
		}
	}

    public static function getTabClause ($tab) {
        switch ($tab) {

            // case 0 :
            //     return array(
            //         'DATE(data) = ?' => 'return date("Y-m-d");'
            //     );
            // break;

            // case Preinscricao::PREINSCRICAO_STATUS_INTERESSADO :
            //     return array(
            //         'DATE(data) <> ?' => 'return date("Y-m-d");',
            //         'status = ?' => 'return (int) $statusConstante;'
            //     );
            // break;

            case Preinscricao::PREINSCRICAO_STATUS_INTERESSADO :
            case Preinscricao::PREINSCRICAO_STATUS_INSCRITO :
            case Preinscricao::PREINSCRICAO_STATUS_PREMATRICULADO :
            case Preinscricao::PREINSCRICAO_STATUS_CONTRATO :
            case Preinscricao::PREINSCRICAO_STATUS_CURSANDO :
            case Preinscricao::PREINSCRICAO_STATUS_DESISTENTE :
            case Preinscricao::PREINSCRICAO_STATUS_CONCLUIDO :
            case Preinscricao::PREINSCRICAO_STATUS_CANCELADO :
                return array(
                    'status = ?' => 'return (int) $statusConstante;'
                );
            break;
        }
    }

    public static function getStatuses () {
        return array(
            Preinscricao::PREINSCRICAO_STATUS_INTERESSADO => "Interessado",
            Preinscricao::PREINSCRICAO_STATUS_INSCRITO => "Inscrição paga",
            Preinscricao::PREINSCRICAO_STATUS_PREMATRICULADO => "Contrato assinado",
            Preinscricao::PREINSCRICAO_STATUS_CONTRATO => "Contrato assinado e taxa de inscrição paga",
            Preinscricao::PREINSCRICAO_STATUS_CURSANDO => "Cursando",
            Preinscricao::PREINSCRICAO_STATUS_DESISTENTE => "Desistente",
            Preinscricao::PREINSCRICAO_STATUS_CONCLUIDO => "Concluído",
            Preinscricao::PREINSCRICAO_STATUS_CANCELADO => "Cancelado"
        );
    }

    public static function getPathContrato ($curso, $unidade, $inscricao, $especial = false) {

        $pathContratos = DIR_ROOT . DS . "administrar" . DS . "contratos";

        if ($especial) {
            return $pathContratos . DS . "especial.rtf";
        }

        if ($curso["tipo"] == Curso::CURSO_TIPO_APERFEICOAMENTO) {
            return $pathContratos . DS . "aperfeicoamento.rtf";
        }

        switch ($inscricao["unidadeCertificadora"]) {
            case Curso::CURSO_UNIDADE_CERTIFICADORA_FIP :
                $path = $pathContratos . DS . "fip";
                $unidade = strtolower(removeAcentos($unidade));
                $unidade = preg_replace('/ /', '-', $unidade);
                if (existeArquivo($path . "-" . $unidade . ".rtf")) {
                    return $path . "-" . $unidade . ".rtf";
                }
                $path .= ".rtf";
                return $path;
            break;

            case Curso::CURSO_UNIDADE_CERTIFICADORA_NASSAU :
                $path = $pathContratos . DS . "nassau";
                $unidade = strtolower(removeAcentos($unidade));
                $unidade = preg_replace('/ /', '-', $unidade);
                if (existeArquivo($path . "-" . $unidade . ".rtf")) {
                    return $path . "-" . $unidade . ".rtf";
                }
                $path .= ".rtf";
                return $path;
            break;

            case Curso::CURSO_UNIDADE_CERTIFICADORA_UNINGA :
                $path = $pathContratos . DS . "uninga";
                $unidade = strtolower(removeAcentos($unidade));
                $unidade = preg_replace('/ /', '-', $unidade);
                if (existeArquivo($path . "-" . $unidade . ".rtf")) {
                    return $path . "-" . $unidade . ".rtf";
                }
                $path .= ".rtf";
                return $path;
            break;

            case Curso::CURSO_UNIDADE_CERTIFICADORA_IEFAP :
                $path = $pathContratos . DS . "iefap";
                $unidade = strtolower(removeAcentos($unidade));
                $unidade = preg_replace('/ /', '-', $unidade);
                if (existeArquivo($path . "-" . $unidade . ".rtf")) {
                    return $path . "-" . $unidade . ".rtf";
                }
                $path .= ".rtf";
                return $path;
            break;

            default : 
            break;
        }
    }

    public function getQuery ($table = 'vw_preinscricoes_v3 p', $filtro = array()) {
        try {

            if (count($filtro) == 0) {
                $filtro = array('*');
            }

            $query = $this->_conexao->query()->from($table, $filtro);

            // administradores master
            if ($_SESSION[PREFIX . 'loginCodigo'] == 33) {
                return $query;
            }
            
            if ($_SESSION[PREFIX . "filtrar"] == 1) {
                $query->where('p.responsavel = ?', (int) $_SESSION[PREFIX . "loginId"]);
            }

            // se na lista de cursos da sessão não existe nenhum curso significa que
            // o usuário logado não pode acessar nenhum curso
            if (!$_SESSION[PREFIX . "cursos"]) {
                $query->where('p.idCurso = -1'); // false clause
            }
            else {
                $query->where('p.idCurso IN ?', array_map('intval', $_SESSION[PREFIX . "cursos"]));
            }

            // se na lista de cursos da sessão não existe nenhuma cidade significa que
            // o usuário logado não pode ver inscrições de nenhuma cidade
            if (!$_SESSION[PREFIX . "unidades"]) {
                $query->where('p.idCidade = -1'); // false clause
            }
            else {
                $query->where('(p.idCidade = 0 or p.idCidade IN ?)', 
                    array_map('intval', $_SESSION[PREFIX . "unidades"]));
            }

            return $query;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    /**
    *   Retorna inscrição a partir do id.
    */
    public function findOrFail($id) {
        try {

            $objeto = $this->getQuery()
                ->where('id = ?', (int) $id)
                ->first();

            if (!$objeto) {
                throw new Exception('Inscrição não localizada');
            }
            else {
                
                $objeto = array_map('Funcoes::decodificaDado', $objeto);
                
                // recupera a situação atual da inscrições
                $objeto['situacao'] = $this->_conexao->query()->from('vw_situacao')
                    ->where('inscricao = ?', (int) $objeto['id'])
                    ->order('dataC', 'desc')
                    ->first();

            }
            return $objeto;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function delete2 ($objeto) {
        try {
            $deleted = $this->_conexao->query()->from('preinscricoes')
                ->where('id = ?', (int) $objeto['id'])
                ->delete();
            return $deleted;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    /**
    *   Retorna tabs com os status
    *   @param $curso Id do curso
    */
    public function getTabs ($curso) {
        
        $statuses = self::getStatuses();

        $query = $this->getQuery()->where('p.idCurso = ?', (int) $curso['id']);

        foreach ($statuses as $statusConstante => $statusNome) {

            $query1 = clone $query;

            foreach (self::getTabClause($statusConstante) as $chave => $valor) {
                $query1->where($chave, eval($valor));
            }

            $quantidade = $query1->count();

            $statuses[$statusConstante] = array(
                'valor' => $statusNome,
                'quantidade' => $quantidade
            );

        }
        return $statuses;
    }

    public static function buildTable ($conexao, $inscricoes) {
        try {
            foreach ($inscricoes as $key => $insc) {

                $inscricoes[$key]["labels"] = array();

                $inscricoes[$key]["infos"] = array(
                    Preinscricao::getStatus($insc['status'])
                );

                if (Funcoes::diferencaDatas(date('Y-m-d'), $insc['data']) == 0) {
                    $l = array(
                        'class' => 'warning',
                        'label' => 'Nova',
                        'text' => 'Inscrição realizada hoje'
                    );
                    $inscricoes[$key]["labels"][] = $l;
                }

                if ($insc['visualizada'] == 0) {
                    $l = array(
                        'class' => 'warning',
                        'label' => 'Não visualizada(s)',
                        'text' => 'Inscrição ainda não foi visualizada'
                    );
                    $inscricoes[$key]["labels"][] = $l;
                }

                if ($insc['idSituacao'] != 0) {

                    $situacao = $conexao->query()->from('vw_situacao')
                        ->where('id = ?', (int) $insc['idSituacao'])
                        ->first();

                    $inscricoes[$key]['resumo'] = Situacao::getResumo($situacao);

                    // se tiver uma data de retorno definida
                    if ($situacao['data']) {
                        if (Funcoes::diferencaDatas(date('Y-m-d'), $situacao['data']) == 0) {
                            $label = 'hoje';
                        }
                        else {
                            $label = Funcoes::decodeDate($situacao['data']);
                        }
                        $l = array(
                            'objeto' => $situacao,
                            'class' => 'nota',
                            'label' => 'Retornar ' . $label,
                            'text' => 'Melhor horário  ' . $situacao['horario']
                        );
                        $inscricoes[$key]["labels"][] = $l;
                    }
                }
            }
            return $inscricoes;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function indexList ($objetos) {
        try {

            foreach ($objetos as $key => $obj) {

                // infos

                $quantidadeInscricoes = $this->getQuery()
                    ->where('idCurso = ?', (int) $obj['id'])
                    ->count();

                if ($quantidadeInscricoes == 0) {
                    $label = 'Nenhuma inscrição';
                }
                else if ($quantidadeInscricoes == 1) {
                    $label = $quantidadeInscricoes . ' inscrição';
                }
                else {
                    $label = $quantidadeInscricoes . ' inscrições';
                }

                $objetos[$key]["infos"] = array(
                    $label
                );

                // labels

                $objetos[$key]["labels"] = array();

                $naoVisualizadas = $this->getQuery()
                    ->where('idCurso = ?', (int) $obj['id'])
                    ->where('visualizada = 0')
                    ->count();

                if ($naoVisualizadas > 0) {
                    $l = array(
                        'class' => 'warning',
                        'label' => $naoVisualizadas . ' Não visualizada(s)',
                        'text' => 'Inscrição ainda não foi visualizada'
                    );
                    $objetos[$key]["labels"][] = $l;
                }

                $novas = $this->getQuery()
                    ->where('idCurso = ?', (int) $obj['id'])
                    ->where('DATE(data) = ?', date('Y-m-d'))
                    ->count();

                if ($novas > 0) {
                    $l = array(
                        'class' => 'warning',
                        'label' => $novas . ' Nova(s)',
                        'text' => 'Inscrição realizada hoje'
                    );
                    $objetos[$key]["labels"][] = $l;
                }

            }
            return $objetos;
        }
        catch (Exception $e) {
            throw $e;
        }
    }	

}

?>
