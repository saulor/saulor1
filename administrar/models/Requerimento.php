<?php

class Requerimento extends Model {

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
    protected $_email;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_curso;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_cidade1;

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
    * @type integer
    */
    protected $_tipo;

    /**
    * @column
    * @readwrite
    * @type longtext
    */
    protected $_especificacoes;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_endereco;

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
    protected $_cidade2;

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
    * @length 255
    */
    protected $_protocolo;

    /**
    * @column
    * @readwrite
    * @type longtext
    */
    protected $_observacoes;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_situacao;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_previsao;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_pendencias;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_arquivo;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
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
    protected $_enviouAnexo;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_anexo;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_mimeAnexo;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 5
    */
    protected $_extensaoAnexo;

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
    * @length 255
    */
    protected $_mimeComprovante;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 5
    */
    protected $_extensaoComprovante;

    /**
    * @column
    * @readwrite
    * @type longtext
    */
    protected $_procedimentosInternos;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_unidadeCertificadora;

    /**
    * @column
    * @readwrite
    * @type datetime
    */
    protected $_data; // data de cadastro do requerimento (criação)

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_timestamp;

    /**
    * @column
    * @readwrite
    * @type date
    */
    protected $_dataVencimento;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_dataVencimentoTimestamp;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_pago;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_isento;

    /**
    * @column
    * @readwrite
    * @type date
    */
    protected $_dataPagamento;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_dataPagamentoTimestamp;

    /**
    * @column
    * @readwrite
    * @type date
    */
    protected $_dataFinalizacao;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_dataFinalizacaoTimestamp;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_finalizadoPor;

    protected $_table = 'requerimentos';

	const REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_SIMPLES = 1;
	const REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_CARGA_HORARIA_CURSADA = 2;
	const REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_INICIO_TERMINO_CURSO = 3;
	const REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_LOCAL_REALIZACAO_CURSO = 4;
	const REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_ELABORACAO_TCC = 5;
	const REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_HORARIO_FUNCIONAMENTO = 6;
	const REQUERIMENTO_TIPO_DECLARACAO_PRATICA_ESTAGIO = 7;
	const REQUERIMENTO_TIPO_DECLARACAO_REGULARIDADE_FINANCEIRA = 8;
	const REQUERIMENTO_TIPO_DECLARACAO_FREQUENCIA = 9;
	const REQUERIMENTO_TIPO_HISTORICO_PARCIAL = 10;
	const REQUERIMENTO_TIPO_EMENTAS = 11;
	const REQUERIMENTO_TIPO_ANALISE_DISPENSA_DISCIPLINA = 12;
	const REQUERIMENTO_TIPO_PROVA_SUBSTITUTIVA = 13;
	const REQUERIMENTO_TIPO_REPOSICAO_NOTA_CARGA_HORARIA_PRESENCIAL = 14;
	const REQUERIMENTO_TIPO_REPOSICAO_NOTA = 15;
	const REQUERIMENTO_TIPO_ANALISE_MUDANCA_TURMA = 16;
	const REQUERIMENTO_TIPO_CONFECCAO_KIT_ENTREGA_TCC = 17;
	const REQUERIMENTO_TIPO_BOLETO = 18;
	const REQUERIMENTO_TIPO_CERTIDAO_CONCLUSAO = 19;
	const REQUERIMENTO_TIPO_1_VIA_CERTIFICADO_CONCLUSAO = 20;
	const REQUERIMENTO_TIPO_2_VIA_CERTIFICADO_CONCLUSAO = 21;
	const REQUERIMENTO_TIPO_CANCELAMENTO_MATRICULA = 22;
	const REQUERIMENTO_TIPO_OUTROS = 23;
    const REQUERIMENTO_TIPO_PRORROGACAO_PRAZO_ARTIGO = 24;
    const REQUERIMENTO_TIPO_ENTREGA_POSTERIOR_PRAZO_LIMITE = 25;

	const REQUERIMENTO_SITUACAO_RECEBIDO = 1;
	const REQUERIMENTO_SITUACAO_EM_ANALISE = 2;
	const REQUERIMENTO_SITUACAO_DEFERIDO_PROCESSO_EMISSAO = 3;
	const REQUERIMENTO_SITUACAO_REGULARIZAR_PENDENCIA = 4;
	const REQUERIMENTO_SITUACAO_INDEFERIDO = 5;
	const REQUERIMENTO_SITUACAO_FINALIZADO = 6;

	const REQUERIMENTO_PENDENCIA_FICHA_INSCRICAO = 1;
	const REQUERIMENTO_PENDENCIA_ACORDO_FINANCEIRO = 2;
	const REQUERIMENTO_PENDENCIA_CONTRATO = 3;
	const REQUERIMENTO_PENDENCIA_DIPLOMA_GRADUACAO = 4;
	const REQUERIMENTO_PENDENCIA_CERTIDAO_CONCLUSAO = 5;
	const REQUERIMENTO_PENDENCIA_COMPROVANTE_RESIDENCIA = 6;
	const REQUERIMENTO_PENDENCIA_EMENTARIO_HISTORICO = 7;
	const REQUERIMENTO_PENDENCIA_CERTIDAO_NASCIMENTO = 8;
	const REQUERIMENTO_PENDENCIA_FOTO_3X4 = 9;
	const REQUERIMENTO_PENDENCIA_CURRICULO = 10;
	const REQUERIMENTO_PENDENCIA_HISTORICO_ESCOLAR = 11;
	const REQUERIMENTO_PENDENCIA_REPOSICAO_AULA_PRESENCIAL = 12;
	const REQUERIMENTO_PENDENCIA_TRABALHO_REPOSICAO = 13;
	const REQUERIMENTO_PENDENCIA_REPOSICAO_CARGA_HORARIA = 14;
	const REQUERIMENTO_PENDENCIA_PROVA_SUBSTITUTIVA = 15;
	const REQUERIMENTO_PENDENCIA_RG = 16;
	const REQUERIMENTO_PENDENCIA_CPF = 17;
	const REQUERIMENTO_PENDENCIA_OUTROS = 18;
	const REQUERIMENTO_PENDENCIA_PAGAMENTO = 19;

	public static function getPendencia($tipo) {
		switch ($tipo) {
			case Requerimento::REQUERIMENTO_PENDENCIA_FICHA_INSCRICAO :
				return "Ficha de Inscrição";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_ACORDO_FINANCEIRO :
				return "Acordo Financeiro";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_CONTRATO :
				return "Contrato";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_DIPLOMA_GRADUACAO :
				return "Diploma de Graduação (autenticado)";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_CERTIDAO_CONCLUSAO :
				return "Certificado de Conclusão";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_COMPROVANTE_RESIDENCIA :
				return "Comprovante de Residência";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_EMENTARIO_HISTORICO :
				return "Ementário e/ou Histórico";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_CERTIDAO_NASCIMENTO :
				return "Certidão de Nascimento";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_FOTO_3X4 :
				return "Foto 3X4";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_CURRICULO :
				return "Currículo";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_HISTORICO_ESCOLAR :
				return "Histórico Escolar";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_REPOSICAO_AULA_PRESENCIAL :
				return "Reposição de Aula Presencial";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_TRABALHO_REPOSICAO :
				return "Trabalho de Reposição";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_REPOSICAO_CARGA_HORARIA :
				return "Reposição de Carga Horária";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_PROVA_SUBSTITUTIVA :
				return "Prova Substitutiva";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_RG :
				return "Financeiro";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_CPF :
				return "CPF";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_PAGAMENTO :
				return "Pagamento da Taxa";
			break;

			case Requerimento::REQUERIMENTO_PENDENCIA_OUTROS :
				return "Outros";
			break;

            case Requerimento::REQUERIMENTO_TIPO_PRORROGACAO_PRAZO_ARTIGO :
                return "Prorrogação do prazo de entrega do artigo científico";
            break;

            case Requerimento::REQUERIMENTO_TIPO_ENTREGA_POSTERIOR_PRAZO_LIMITE :
                return "Entrega posterior ao prazo limite de envio do artigo científico";
            break;

		}
	}

	public static function getSituacoes() {
		return array(
			Requerimento::REQUERIMENTO_SITUACAO_RECEBIDO => "Recebido",
			Requerimento::REQUERIMENTO_SITUACAO_EM_ANALISE => "Em análise",
			Requerimento::REQUERIMENTO_SITUACAO_DEFERIDO_PROCESSO_EMISSAO => "Deferido e em processo de emissão",
			Requerimento::REQUERIMENTO_SITUACAO_REGULARIZAR_PENDENCIA => "Regularizar pendência",
			Requerimento::REQUERIMENTO_SITUACAO_INDEFERIDO => "Indeferido",
			Requerimento::REQUERIMENTO_SITUACAO_FINALIZADO => "Finalizado",
		);
	}

	public static function getSituacao($situacao) {
		switch ($situacao) {
			case Requerimento::REQUERIMENTO_SITUACAO_RECEBIDO :
				return "Recebido";
			break;
			case Requerimento::REQUERIMENTO_SITUACAO_EM_ANALISE :
				return "Em Análise";
			break;
			case Requerimento::REQUERIMENTO_SITUACAO_DEFERIDO_PROCESSO_EMISSAO :
				return "Deferido e em Processo de Emissão";
			break;
			case Requerimento::REQUERIMENTO_SITUACAO_REGULARIZAR_PENDENCIA :
				return "Regularizar Pendência";
			break;
			case Requerimento::REQUERIMENTO_SITUACAO_INDEFERIDO :
				return "Indeferido";
			break;
			case Requerimento::REQUERIMENTO_SITUACAO_FINALIZADO :
				return "Finalizado";
			break;
		}
	}

	public static function getTipos() {
		return array (
			Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_SIMPLES => "Declaração de matrícula - simples",
			Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_CARGA_HORARIA_CURSADA => "Declaração de matrícula - carga horária cursada",
			Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_INICIO_TERMINO_CURSO => "Declaração de matrícula - início e término do curso",
			Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_LOCAL_REALIZACAO_CURSO => "Declaração de matrícula - local de realização do curso",
			Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_ELABORACAO_TCC => "Declaração de matrícula - elaboração de TCC",
			Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_HORARIO_FUNCIONAMENTO => "Declaração de matrícula - horário de funcionamento",
			Requerimento::REQUERIMENTO_TIPO_DECLARACAO_PRATICA_ESTAGIO => "Declaração para prática/estágio (especificar)",
			Requerimento::REQUERIMENTO_TIPO_DECLARACAO_REGULARIDADE_FINANCEIRA => "Declaração de regularidade financeira (especificar)",
			Requerimento::REQUERIMENTO_TIPO_DECLARACAO_FREQUENCIA => "Declaração de frequência",
			Requerimento::REQUERIMENTO_TIPO_HISTORICO_PARCIAL => "Histórico parcial",
			Requerimento::REQUERIMENTO_TIPO_EMENTAS => "Ementas (especificar disciplinas)",
			Requerimento::REQUERIMENTO_TIPO_ANALISE_DISPENSA_DISCIPLINA => "Análise de dispensa de disciplina (especificar)",
			Requerimento::REQUERIMENTO_TIPO_PROVA_SUBSTITUTIVA => "Prova substitutiva (especificar disciplina)",
			Requerimento::REQUERIMENTO_TIPO_REPOSICAO_NOTA_CARGA_HORARIA_PRESENCIAL => "Reposição de nota e carga horária presencial (especificar disciplina)",
			Requerimento::REQUERIMENTO_TIPO_REPOSICAO_NOTA => "Reposição de nota - falta e trabalho (até 25% de faltas) - (especificar disciplina)",
			Requerimento::REQUERIMENTO_TIPO_ANALISE_MUDANCA_TURMA => "Análise de mudança de turma",
			Requerimento::REQUERIMENTO_TIPO_CONFECCAO_KIT_ENTREGA_TCC => "Confecção de kit para entrega de TCC",
			Requerimento::REQUERIMENTO_TIPO_BOLETO => "Boleto (2ª via)",
			Requerimento::REQUERIMENTO_TIPO_CERTIDAO_CONCLUSAO => "Certidão de conclusão",
			Requerimento::REQUERIMENTO_TIPO_1_VIA_CERTIFICADO_CONCLUSAO => "1ª via - certificado de conclusão/histórico final",
			Requerimento::REQUERIMENTO_TIPO_2_VIA_CERTIFICADO_CONCLUSAO => "2ª via - certificado de conclusão/histórico final",
			Requerimento::REQUERIMENTO_TIPO_CANCELAMENTO_MATRICULA => "Cancelamento de Matrícula (desistência - justificar)",
            Requerimento::REQUERIMENTO_TIPO_PRORROGACAO_PRAZO_ARTIGO => "Prorrogação do prazo de entrega do artigo científico",
            Requerimento::REQUERIMENTO_TIPO_ENTREGA_POSTERIOR_PRAZO_LIMITE => "Entrega posterior ao prazo limite de envio do artigo científico",
			Requerimento::REQUERIMENTO_TIPO_OUTROS => "Outros (especificar)"
		);
	}

	public static function getTipo ($tipo, $raw = false) {
		$retorno = "";
		switch ($tipo) {
			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_SIMPLES :
				$retorno = "Declaração de matrícula - simples";
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_CARGA_HORARIA_CURSADA :
				$retorno = "Declaração de matrícula - carga horária cursada";
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_INICIO_TERMINO_CURSO :
				$retorno = "Declaração de matrícula - início e término do curso";
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_LOCAL_REALIZACAO_CURSO :
				$retorno = "Declaração de matrícula - local de realização do curso";
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_ELABORACAO_TCC :
				$retorno = "Declaração de matrícula - elaboração de TCC";
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_HORARIO_FUNCIONAMENTO :
				$retorno = "Declaração de matrícula - horário de funcionamento";
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_PRATICA_ESTAGIO :
				$retorno = "Declaração para prática/estágio (especificar)";
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_REGULARIDADE_FINANCEIRA :
				$retorno = "Declaração de regularidade financeira (especificar)";
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_FREQUENCIA :
				$retorno = "Declaração de frequência";
			break;

			case Requerimento::REQUERIMENTO_TIPO_HISTORICO_PARCIAL :
				$retorno = "Histórico parcial";
			break;

			case Requerimento::REQUERIMENTO_TIPO_EMENTAS :
				$retorno = "Ementas (especificar disciplinas)";
			break;

			case Requerimento::REQUERIMENTO_TIPO_ANALISE_DISPENSA_DISCIPLINA :
				$retorno = "Análise de dispensa de disciplina (especificar)";
			break;

			case Requerimento::REQUERIMENTO_TIPO_PROVA_SUBSTITUTIVA :
				$retorno = "Prova substitutiva (especificar disciplina)";
			break;

			case Requerimento::REQUERIMENTO_TIPO_REPOSICAO_NOTA_CARGA_HORARIA_PRESENCIAL :
				$retorno = "Reposição de nota e carga horária presencial (especificar disciplina)";
			break;

			case Requerimento::REQUERIMENTO_TIPO_REPOSICAO_NOTA :
				$retorno = "Reposição de nota (especificar disciplina)";
			break;

			case Requerimento::REQUERIMENTO_TIPO_ANALISE_MUDANCA_TURMA :
				$retorno = "Análise de mudança de turma";
			break;

			case Requerimento::REQUERIMENTO_TIPO_CONFECCAO_KIT_ENTREGA_TCC :
				$retorno = "Confecção de kit para entrega de TCC";
			break;

			case Requerimento::REQUERIMENTO_TIPO_BOLETO :
				$retorno = "Boleto (2ª via)";
			break;

			case Requerimento::REQUERIMENTO_TIPO_CERTIDAO_CONCLUSAO :
				$retorno = "Certidão de conclusão";
			break;

			case Requerimento::REQUERIMENTO_TIPO_1_VIA_CERTIFICADO_CONCLUSAO :
				$retorno = "1ª via - certificado de conclusão/histórico final";
			break;

			case Requerimento::REQUERIMENTO_TIPO_2_VIA_CERTIFICADO_CONCLUSAO :
				$retorno = "2ª via - certificado de conclusão/histórico final";
			break;

			case Requerimento::REQUERIMENTO_TIPO_CANCELAMENTO_MATRICULA :
				$retorno = "Cancelamento de Matrícula (desistência - justificar)";
			break;

			case Requerimento::REQUERIMENTO_TIPO_OUTROS :
				$retorno = "Outros (especificar)";
			break;

            case Requerimento::REQUERIMENTO_TIPO_PRORROGACAO_PRAZO_ARTIGO :
                $retorno = "Prorrogação do prazo de entrega do artigo científico";
            break;

            case Requerimento::REQUERIMENTO_TIPO_ENTREGA_POSTERIOR_PRAZO_LIMITE :
                $retorno = "Entrega posterior ao prazo limite de envio do artigo científico";
            break;

			default :
				$retorno = "";
			break;
		}

		if ($raw) {
			if (strpos($retorno, "(") !== false) {
				$retorno = substr($retorno, 0, strpos($retorno, "(") - 1);
			}
		}

		return $retorno;
	}

	public static function getCodigo ($tipo) {
		switch ($tipo) {
			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_SIMPLES :
				return "DECMS";
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_CARGA_HORARIA_CURSADA :
				return "DECMC";
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_INICIO_TERMINO_CURSO :
				return "DECMI";
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_LOCAL_REALIZACAO_CURSO :
				return "DECML";
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_ELABORACAO_TCC :
				return "DECME";
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_HORARIO_FUNCIONAMENTO :
				return "DECMH";
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_PRATICA_ESTAGIO :
				return "DECPE";
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_REGULARIDADE_FINANCEIRA :
				return "DECRF";
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_FREQUENCIA :
				return "DECFR";
			break;

			case Requerimento::REQUERIMENTO_TIPO_HISTORICO_PARCIAL :
				return "DECHI";
			break;

			case Requerimento::REQUERIMENTO_TIPO_EMENTAS :
				return "DECEM";
			break;

			case Requerimento::REQUERIMENTO_TIPO_ANALISE_DISPENSA_DISCIPLINA :
				return "DECAD";
			break;

			case Requerimento::REQUERIMENTO_TIPO_PROVA_SUBSTITUTIVA :
				return "PROSU";
			break;

			case Requerimento::REQUERIMENTO_TIPO_REPOSICAO_NOTA_CARGA_HORARIA_PRESENCIAL :
				return "REPNC";
			break;

			case Requerimento::REQUERIMENTO_TIPO_REPOSICAO_NOTA :
				return "REPNO";
			break;

			case Requerimento::REQUERIMENTO_TIPO_ANALISE_MUDANCA_TURMA :
				return "ANATU";
			break;

			case Requerimento::REQUERIMENTO_TIPO_CONFECCAO_KIT_ENTREGA_TCC :
				return "CONKE";
			break;

			case Requerimento::REQUERIMENTO_TIPO_BOLETO :
				return "BOLET";
			break;

			case Requerimento::REQUERIMENTO_TIPO_CERTIDAO_CONCLUSAO :
				return "CERCO";
			break;

			case Requerimento::REQUERIMENTO_TIPO_1_VIA_CERTIFICADO_CONCLUSAO :
				return "CERC1";
			break;

			case Requerimento::REQUERIMENTO_TIPO_2_VIA_CERTIFICADO_CONCLUSAO :
				return "CERC2";
			break;

			case Requerimento::REQUERIMENTO_TIPO_CANCELAMENTO_MATRICULA :
				return "CANMA";
			break;

			case Requerimento::REQUERIMENTO_TIPO_OUTROS :
				return "OUTRO";
			break;

            case Requerimento::REQUERIMENTO_TIPO_PRORROGACAO_PRAZO_ARTIGO :
                $retorno = "PROPRA";
            break;

            case Requerimento::REQUERIMENTO_TIPO_ENTREGA_POSTERIOR_PRAZO_LIMITE :
                $retorno = "ENTPPL";
            break;

			default :
				return "";
			break;
		}
	}

	public static function temEspecificacao ($tipo) {
		switch ($tipo) {
			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_SIMPLES :
				return false;
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_CARGA_HORARIA_CURSADA :
				return false;
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_INICIO_TERMINO_CURSO :
				return false;
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_LOCAL_REALIZACAO_CURSO :
				return false;
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_ELABORACAO_TCC :
				return false;
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_HORARIO_FUNCIONAMENTO :
				return false;
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_PRATICA_ESTAGIO :
				return true;
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_REGULARIDADE_FINANCEIRA :
				return true;
			break;

			case Requerimento::REQUERIMENTO_TIPO_DECLARACAO_FREQUENCIA :
				return false;
			break;

			case Requerimento::REQUERIMENTO_TIPO_HISTORICO_PARCIAL :
				return false;
			break;

			case Requerimento::REQUERIMENTO_TIPO_EMENTAS :
				return true;
			break;

			case Requerimento::REQUERIMENTO_TIPO_ANALISE_DISPENSA_DISCIPLINA :
				return true;
			break;

			case Requerimento::REQUERIMENTO_TIPO_PROVA_SUBSTITUTIVA :
				return true;
			break;

			case Requerimento::REQUERIMENTO_TIPO_REPOSICAO_NOTA_CARGA_HORARIA_PRESENCIAL :
				return true;
			break;

			case Requerimento::REQUERIMENTO_TIPO_REPOSICAO_NOTA :
				return true;
			break;

			case Requerimento::REQUERIMENTO_TIPO_ANALISE_MUDANCA_TURMA :
				return false;
			break;

			case Requerimento::REQUERIMENTO_TIPO_CONFECCAO_KIT_ENTREGA_TCC :
				return false;
			break;

			case Requerimento::REQUERIMENTO_TIPO_BOLETO :
				return false;
			break;

			case Requerimento::REQUERIMENTO_TIPO_CERTIDAO_CONCLUSAO :
				return false;
			break;

			case Requerimento::REQUERIMENTO_TIPO_1_VIA_CERTIFICADO_CONCLUSAO :
				return false;
			break;

			case Requerimento::REQUERIMENTO_TIPO_2_VIA_CERTIFICADO_CONCLUSAO :
				return false;
			break;

			case Requerimento::REQUERIMENTO_TIPO_CANCELAMENTO_MATRICULA :
				return true;
			break;

			case Requerimento::REQUERIMENTO_TIPO_OUTROS :
				return false;
			break;

			default :
				return false;
			break;
		}
	}

	public static function getConstanteTipo ($tipo) {

  	 $tipo = soLetras(mb_strtolower($tipo, 'UTF-8'));

		$pattern = '/^' . $tipo . '/';

		$tipos = array();

		if (preg_match($pattern, "declaracao de matricula simples", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_SIMPLES;
		}

		if (preg_match($pattern, "declaracao de matricula carga horaria cursada", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_CARGA_HORARIA_CURSADA;
		}

		if (preg_match($pattern, "declaracao de matricula inicio e termino do curso", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_INICIO_TERMINO_CURSO;
		}

		if (preg_match($pattern, "declaracao de matricula local de realizacao do curso", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_LOCAL_REALIZACAO_CURSO;
		}

		if (preg_match($pattern, "declaracao de matricula elaboracao de tcc", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_ELABORACAO_TCC;
		}

		if (preg_match($pattern, "declaracao de matricula horario de funcionamento", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_DECLARACAO_MATRICULA_HORARIO_FUNCIONAMENTO;
		}

		if (preg_match($pattern, "declaracao para prática estagio especificar", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_DECLARACAO_PRATICA_ESTAGIO;
		}

		if (preg_match($pattern, "declaracao de regularidade financeira especificar", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_DECLARACAO_REGULARIDADE_FINANCEIRA;
		}

		if (preg_match($pattern, "declaracao de frequencia", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_DECLARACAO_FREQUENCIA;
		}

		if (preg_match($pattern, "historico parcial", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_HISTORICO_PARCIAL;
		}

		if (preg_match($pattern, "ementas especificar disciplinas", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_EMENTAS;
		}

		if (preg_match($pattern, "analise de dispensa de disciplina especificar", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_ANALISE_DISPENSA_DISCIPLINA;
		}

		if (preg_match($pattern, "prova substitutiva especificar disciplina", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_PROVA_SUBSTITUTIVA;
		}

		if (preg_match($pattern, "reposicao de nota e carga horaria presencial especificar disciplina", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_REPOSICAO_NOTA_CARGA_HORARIA_PRESENCIAL;
		}

		if (preg_match($pattern, "reposicao de nota especificar disciplina", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_REPOSICAO_NOTA;
		}

		if (preg_match($pattern, "analise de mudanca de turma", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_ANALISE_MUDANCA_TURMA;
		}

		if (preg_match($pattern, "confeccao de kit para entrega de TCC", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_CONFECCAO_KIT_ENTREGA_TCC;
		}

		if (preg_match($pattern, "boleto 2ª via", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_BOLETO;
		}

		if (preg_match($pattern, "certidao de conclusao", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_CERTIDAO_CONCLUSAO;
		}

		if (preg_match($pattern, "1ª via certificado de conclusao historico final", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_1_VIA_CERTIFICADO_CONCLUSAO;
		}

		if (preg_match($pattern, "2ª via certificado de conclusao historico final", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_2_VIA_CERTIFICADO_CONCLUSAO;
		}

		if (preg_match($pattern, "cancelamento de matricula desistencia justificar", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_CANCELAMENTO_MATRICULA;
		}

		if (preg_match($pattern, "outros especificar", $matches, PREG_OFFSET_CAPTURE)) {
			$tipos[] = Requerimento::REQUERIMENTO_TIPO_OUTROS;
		}

        if (preg_match($pattern, "prorrogacao do prazo de entrega do artigo cientifico", $matches, PREG_OFFSET_CAPTURE)) {
            $tipos[] = Requerimento::REQUERIMENTO_TIPO_PRORROGACAO_PRAZO_ARTIGO;
        }

        if (preg_match($pattern, "entrega posterior ao prazo limite de envio do artigo cientifico", $matches, PREG_OFFSET_CAPTURE)) {
            $tipos[] = Requerimento::REQUERIMENTO_TIPO_ENTREGA_POSTERIOR_PRAZO_LIMITE;
        }

		return $tipos;

    }

    public static function getConstanteSituacao ($situacao) {

        $situacao = soLetras(mb_strtolower($situacao, 'UTF-8'));

        $pattern = '/^' . $situacao . '/';

        $tipos = array();

        if (preg_match($pattern, "recebido", $matches, PREG_OFFSET_CAPTURE)) {
            $situacao = Requerimento::REQUERIMENTO_SITUACAO_RECEBIDO;
        }
        else if (preg_match($pattern, "em analise", $matches, PREG_OFFSET_CAPTURE)) {
            $situacao = Requerimento::REQUERIMENTO_SITUACAO_EM_ANALISE;
        }
        else if (preg_match($pattern, "deferido e em processo de emissao", $matches, PREG_OFFSET_CAPTURE)) {
            $situacao = Requerimento::REQUERIMENTO_SITUACAO_DEFERIDO_PROCESSO_EMISSAO;
        }
        else if (preg_match($pattern, "regularizar pendencia", $matches, PREG_OFFSET_CAPTURE)) {
            $situacao = Requerimento::REQUERIMENTO_SITUACAO_REGULARIZAR_PENDENCIA;
        }
        else if (preg_match($pattern, "indeferido", $matches, PREG_OFFSET_CAPTURE)) {
            $situacao = Requerimento::REQUERIMENTO_SITUACAO_INDEFERIDO;
        }
        else if (preg_match($pattern, "finalizado", $matches, PREG_OFFSET_CAPTURE)) {
            $situacao = Requerimento::REQUERIMENTO_SITUACAO_FINALIZADO;
        }

        return $situacao;

    }

    public static function filterTabs ($tab, $query) {
        switch ($tab) {

            case 0 :
                $query
                    ->where("situacao = ?", (int) Requerimento::REQUERIMENTO_SITUACAO_RECEBIDO)
                    ->order('dataVencimento', 'asc');
            break;

            case 1 :
                $query
                    ->where("(dataVencimento IS NULL or DATE(dataVencimento) >= ?)", date('Y-m-d'))
                    ->where('situacao NOT IN (?,?,?)', 
                        (int) Requerimento::REQUERIMENTO_SITUACAO_RECEBIDO,
                        (int) Requerimento::REQUERIMENTO_SITUACAO_INDEFERIDO,
                        (int) Requerimento::REQUERIMENTO_SITUACAO_FINALIZADO)
                    ->order('dataVencimento', 'asc');
            break;

            case 2 :
                $query
                    ->where("situacao = ?", (int) Requerimento::REQUERIMENTO_SITUACAO_FINALIZADO)
                    ->order('data', 'DESC');
            break;

            case 3 :
                $query
                    ->where("situacao = ?", (int) Requerimento::REQUERIMENTO_SITUACAO_INDEFERIDO)
                    ->order('data', 'DESC');
            break;

            case 4 :
                $query
                    ->where("DATE(dataVencimento) < ?", date('Y-m-d'))
                    ->where('situacao NOT IN (?,?,?)', 
                        (int) Requerimento::REQUERIMENTO_SITUACAO_RECEBIDO,
                        (int) Requerimento::REQUERIMENTO_SITUACAO_INDEFERIDO,
                        (int) Requerimento::REQUERIMENTO_SITUACAO_FINALIZADO)
                    ->order('dataVencimento', 'desc');
            break;
            
        }
    }

    public static function getTabs ($conexao) {
        
        $tabs = array(
            0 => 'Recebidos',
            1 => 'Em andamento',
            2 => 'Finalizados',
            3 => 'Indeferidos',
            4 => 'Vencidos'
        );

        foreach ($tabs as $constante => $nome) {

            $query = $conexao->query()
                ->from('requerimentos');

            // apply wheres
            self::filterTabs($constante, $query);

            $quantidade = $query->count();

            $statuses[$constante] = array(
                'valor' => $nome,
                'quantidade' => $quantidade
            );

        }
        return $statuses;
    }

}

?>
