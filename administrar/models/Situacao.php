<?php

class Situacao extends Model {

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
    protected $_inscricao;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_tipo;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_assunto;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_descricao;

    /**
    * @column
    * @readwrite
    * @type date
    */
    protected $_data;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_timestampData;

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
    protected $_usuario;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_observacoes;

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
    protected $_timestampDataC;

    const SITUACAO_INSCRICAO_TIPO_CONTATO = 1;
    const SITUACAO_INSCRICAO_TIPO_GRADUANDO = 2;
    const SITUACAO_INSCRICAO_TIPO_INDECISO = 3;
    const SITUACAO_INSCRICAO_TIPO_CIDADE_CANDIDATA = 4;
    const SITUACAO_INSCRICAO_TIPO_FINANCEIRO = 5;
    const SITUACAO_INSCRICAO_TIPO_OUTROS = 6;
    const SITUACAO_INSCRICAO_TIPO_PERDIDO = 7;
    const SITUACAO_INSCRICAO_TIPO_EM_NEGOCIACAO = 8;
    const SITUACAO_INSCRICAO_TIPO_TECNICO = 9;

    protected $_conexao;

    public function __construct($conexao) {
        $this->_conexao = $conexao;
    }

    public static function getTipo ($tipo) {
        switch ($tipo) {

            case self::SITUACAO_INSCRICAO_TIPO_CONTATO :
                return "Não consegui contato";
            break;

            case self::SITUACAO_INSCRICAO_TIPO_EM_NEGOCIACAO :
                return "Em negociação";
            break;

            case self::SITUACAO_INSCRICAO_TIPO_GRADUANDO :
                return "Graduando";
            break;

            case self::SITUACAO_INSCRICAO_TIPO_TECNICO :
                return "Técnico";
            break;
            
            case self::SITUACAO_INSCRICAO_TIPO_INDECISO :
                return "Indeciso";
            break;

            case self::SITUACAO_INSCRICAO_TIPO_CIDADE_CANDIDATA :
                return "Cidade candidata";
            break;

            case self::SITUACAO_INSCRICAO_TIPO_FINANCEIRO :
                return "Financeiro";
            break;

            case self::SITUACAO_INSCRICAO_TIPO_OUTROS :
                return "Outros";
            break;

            case self::SITUACAO_INSCRICAO_TIPO_PERDIDO :
                return "Perdido";
            break;

            default :
                return "Nenhum";
            break;

        }
    }

    public static function getTipos() {
        return array(
            self::SITUACAO_INSCRICAO_TIPO_CONTATO => "Não consegui contato",
            self::SITUACAO_INSCRICAO_TIPO_EM_NEGOCIACAO => "Em negociação",
            self::SITUACAO_INSCRICAO_TIPO_GRADUANDO => "Graduando",
            self::SITUACAO_INSCRICAO_TIPO_TECNICO => "Técnico",
            self::SITUACAO_INSCRICAO_TIPO_INDECISO => "Indeciso",
            self::SITUACAO_INSCRICAO_TIPO_CIDADE_CANDIDATA => "Cidade candidata",
            self::SITUACAO_INSCRICAO_TIPO_FINANCEIRO => "Financeiro",
            self::SITUACAO_INSCRICAO_TIPO_OUTROS => "Outros",
            self::SITUACAO_INSCRICAO_TIPO_PERDIDO => "Perdidos"
        );
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

    public static function getResumo ($dados, $after = 'atualizaSituacoes') {
        try {

            $resumoHtml = '<p>%s</p>';
            $itens = array();

            if (!empty($dados['motivo'])) {
                $itens[] = '<strong>Motivo:</strong> ' . $dados['motivo'];
            }

            if ($dados['tipo'] == Situacao::SITUACAO_INSCRICAO_TIPO_CIDADE_CANDIDATA) {
                $cidade = '';
                if (!empty($dados['cidade'])) {
                    $cidade .= '<strong>Cidade:</strong> ' . $dados['cidade'];
                }
                if (!empty($dados['estado'])) {
                    $cidade .= ' - ' . $dados['estado'];
                }
                $itens[] = $cidade;
            }

            if (!empty($dados['observacoes'])) {
                $itens[] = '<strong>Obs.:</strong> ' . $dados['observacoes'];
            }

            $dataHorario = '';

            if (!empty($dados['data'])) {
                $dataHorario .= '<strong>Retornar:</strong> ' . Funcoes::decodeDate($dados['data']);
            }

            if (!empty($dados['horario'])) {
                $dataHorario .= ' - ' . $dados['horario'];
            }

            if (!empty($dataHorario)) {
                $itens[] = $dataHorario;
            }
            
            $por = '<small>por ' . $dados['nomeUsuario'] . ' em ';
            $por .= Funcoes::datetimeFromTimestamp($dados['timestampDataC'], 'd/m/Y à\s H:i');
            $por .= '</small>';

            $itens[] = $por;

            $resumoHtml = sprintf($resumoHtml, implode('<br />', $itens));

            $manter = '<p>';
            $manter .= '<a href="" data-after="%s" data-tr="tr%d" data-status="%d" data-situacao="%d" ';
            $manter .= 'data-curso="%d" data-tipo="%d" data-id="%d" data-inscricao="%d" data-nome="%s" ';
            $manter .= 'data-usuario="%d" data-nomecurso="%s" class="small add-situacao">Editar</a>';
            $manter .= ' | ';
            $manter .= '<a target="_blank" href="?modulo=situacao&acao=historico&inscricao=%d&curso=%d" ';
            $manter .= 'class="small">Histórico</a>';

            $resumoHtml .= sprintf($manter, $after, $dados['inscricao'], $dados['status'], $dados['tipo'], 
                $dados['idCurso'], $dados['tipo'], $dados['id'], $dados['inscricao'], 
                $dados['nome'], $dados['idUsuario'], $dados['nomeCurso'], $dados['inscricao'], 
                $dados['idCurso'], $dados['id'], $dados['tipo']);

            return $resumoHtml;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function getTabs ($idCurso) {
        try {

            // monta as tabs de situações

            $tabs = '';

            // Aba Todos

            $query = $this->getQuery()
                ->where('status = ?', Preinscricao::PREINSCRICAO_STATUS_INTERESSADO)
                ->where('idCurso = ?', (int) $idCurso);

            $query1 = clone $query;

            $query1->where('tipoSituacao IS NULL');

            $quantidade = $query1->count();

            $tabsHtml = '<li data-situacao="0" id="tab-situacao0" role="presentation"><a href="">%s<span class="badge">%d</span></a></li>';
            $tabs .= sprintf($tabsHtml, 'Todos', $quantidade);

            foreach (Situacao::getTipos() as $key => $value) {
                $query1 = clone $query;
                $query1->where('tipoSituacao = ?', (int) $key);
                $quantidade = $query1->count();
                $tabsHtml = '<li data-situacao="%d" id="tab-situacao%d" role="presentation"><a title="%s" href="">%s<span class="badge">%d</span></a></li>';
                $tabs .= sprintf($tabsHtml, $key, $key, $value, compactaTexto($value,15), $quantidade);
            }

            $this->_conexao->disconnect();
            return $tabs;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function findOrFail ($id) {
        try {
            $objeto = $this->_conexao->query()->from('vw_situacao')
                ->where('id = ?', (int) $id)
                ->first();
            if (!$objeto) {
                throw new Exception('Situacão não encontrada');
            }
            return $objeto;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function findAll ($inscricao) {
        try {
            $objetos = $this->_conexao->query()->from('vw_situacao')
                ->where('inscricao = ?', (int) $inscricao)
                ->order('dataC', 'desc')
                ->all();
            return $objetos;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function delete ($situacao) {
        try {
            if ($this->isAtual($situacao)) {
                throw new Exception ('Situação atual não pode ser excluída');
            }
            $deleted = $this->_conexao->query()->from('situacao')
                ->where('id = ?', (int) $situacao['id'])
                ->delete();
            return $deleted;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    private function isAtual ($situacao) {
        try {
            // verifico se a situacão que estão querendo excluir é a situação atual
            $atual = $this->_conexao->query()->from('vw_situacao')
                ->where('inscricao = ?', (int) $situacao['inscricao'])
                ->order('dataC', 'desc')
                ->first();
            if ($atual['id'] == $situacao['id']) {
                return true;
            }
            return false;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

}

?>
