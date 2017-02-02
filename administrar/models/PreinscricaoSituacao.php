<?php

class PreinscricaoSituacao extends Model {

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
	* @index
	* @foreign
	* @references preinscricoes (id)
	* @delete cascade
	* @update cascade
	*/
	protected $_preinscricao;
	
	/**
	* @column
	* @readwrite
	* @type integer
	*/
	protected $_item;
	
	/**
	* @column
	* @readwrite
	* @type integer
	*/
	protected $_situacao;
	
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

	protected $_conexao;

	const PREINSCRICAO_ITEM_CONTRATO_PRESTACAO_SERVICOS_EDUCACIONAIS = 1;
	const PREINSCRICAO_ITEM_PAGAMENTO_TAXA_INSCRICAO = 2;
	const PREINSCRICAO_ITEM_DIPLOMA_CURSO_SUPERIOR_AUTENTICADO = 3;
	const PREINSCRICAO_ITEM_RG = 4;
	const PREINSCRICAO_ITEM_CPF = 5;
	const PREINSCRICAO_ITEM_CURRICULO = 6;
	const PREINSCRICAO_ITEM_CERTIDAO_NASCIMENTO = 7;
	const PREINSCRICAO_ITEM_CERTIDAO_CASAMENTO = 8;
	const PREINSCRICAO_ITEM_HISTORICO_ESCOLAR = 9;
	const PREINSCRICAO_ITEM_COMPROVANTE_RESIDENCIA = 10;

	public function __construct ($conexao) {
        $this->_conexao = $conexao;
    }
	
	// Quando adicionar algum outro item, lembrar de verificar a impressão de situação dos alunos
	public static function getItens() {
		return array(
			PreinscricaoSituacao::PREINSCRICAO_ITEM_CONTRATO_PRESTACAO_SERVICOS_EDUCACIONAIS => "Contrato de Prestação de Serviços Educacionais",
			PreinscricaoSituacao::PREINSCRICAO_ITEM_PAGAMENTO_TAXA_INSCRICAO => "Pagamento da Taxa de Inscrição",
			PreinscricaoSituacao::PREINSCRICAO_ITEM_DIPLOMA_CURSO_SUPERIOR_AUTENTICADO => "Diploma de Curso Superior (Autenticado)",
			PreinscricaoSituacao::PREINSCRICAO_ITEM_RG => "RG",
			PreinscricaoSituacao::PREINSCRICAO_ITEM_CPF => "CPF",
			PreinscricaoSituacao::PREINSCRICAO_ITEM_CURRICULO => "Currículo",
			PreinscricaoSituacao::PREINSCRICAO_ITEM_CERTIDAO_NASCIMENTO => "Certidão de Nascimento",
			PreinscricaoSituacao::PREINSCRICAO_ITEM_CERTIDAO_CASAMENTO => "Certidão de Casamento",
			PreinscricaoSituacao::PREINSCRICAO_ITEM_HISTORICO_ESCOLAR => "Histórico Escolar da Graduação",
			PreinscricaoSituacao::PREINSCRICAO_ITEM_COMPROVANTE_RESIDENCIA => "Comprovante de Residência"
		);
	}
	
	public static function getNomeItem ($constante) {
		$itens = self::getItens();
		return $itens[$constante];
	}

    public function getQuery ($filtro = array()) {
        try {
            if (count($filtro) == 0) {
                $filtro = array('*');
            }
            $query = $this->_conexao->query()->from('preinscricoes_situacao', $filtro);
            return $query;
        }
        catch (Exception $e) {
            throw $e;
        }
    }
	
}

?>