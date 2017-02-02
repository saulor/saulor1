<?php

class Contato extends Model {

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
  * @planilha
  */
  protected $_nome;

  /**
  * @column
  * @readwrite
  * @type text
  * @length 15
  * @planilha
  */
  protected $_telefone;

  /**
  * @column
  * @readwrite
  * @type text
  * @length 100
  * @planilha
  */
  protected $_email;

  /**
  * @column
  * @readwrite
  * @type text
  * @length 255
  * @planilha
  */
  protected $_cidade;

  /**
  * @column
  * @readwrite
  * @type text
  * @length 2
  * @planilha
  */
  protected $_estado;

  /**
  * @column
  * @readwrite
  * @type text
  */
  protected $_mensagem;

  /**
  * @column
  * @readwrite
  * @type integer
  */
  protected $_respondido;

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
  * @type text
  * @editor
  */
  protected $_resposta;

  /**
  * @column
  * @readwrite
  * @type tinytext
  */
  protected $_cc;

  /**
  * @column
  * @readwrite
  * @type datetime
  */
  protected $_dataResposta;

  /**
  * @column
  * @readwrite
  * @type integer
  */
  protected $_timestampResposta;

  /**
  * @column
  * @readwrite
  * @type text
  * @length 255
  */
  protected $_respondidoPor;

  /**
  * @column
  * @readwrite
  * @type text
  * @length 255
  * @planilha
  */
  protected $_cursos;

  /**
  * @column
  * @readwrite
  * @type text
  * @planilha
  */
  protected $_observacoes;

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
  * @planilha
  */
  protected $_convertido;

  /**
  * @column
  * @readwrite
  * @type text
  * @length 255
  * @planilha
  */
  protected $_curso;

	const CONTATO_STATUS_NAO_RESPONDIDO = 0;
	const CONTATO_STATUS_RESPONDIDO = 1;
  const CONTATO_STATUS_DESISTENTE = 2;
  const CONTATO_STATUS_ENCAMINHADO = 3;
  const CONTATO_STATUS_EM_ANDAMENTO = 4;
  const CONTATO_STATUS_INTERESSADO = 5;
  const CONTATO_STATUS_MATRICULADO = 6;

  protected $_table = 'contatos';

  public function __construct ($conexao) {
      parent::__construct($conexao);
  }

  public static function getStatus ($status) {
    switch ($status) {
      case Contato::CONTATO_STATUS_DESISTENTE :
        return "Desistente";
      break;

      case Contato::CONTATO_STATUS_ENCAMINHADO :
        return "Encaminhado para unidade";
      break;

      case Contato::CONTATO_STATUS_EM_ANDAMENTO :
        return "Em andamento";
      break;

      case Contato::CONTATO_STATUS_INTERESSADO :
        return "Interessado";
      break;

      case Contato::CONTATO_STATUS_MATRICULADO :
        return "Matriculado";
      break;

      default :
        return "Nenhum";
      break;
    }
  }

  public static function getRespondido($respondido) {
    return $respondido == Contato::CONTATO_STATUS_RESPONDIDO ? "Sim" : "Não";
  }

  public static function getStatuses () {
		return array(
      -1 => "Nenhum",
			Contato::CONTATO_STATUS_DESISTENTE => "Desistente",
			Contato::CONTATO_STATUS_ENCAMINHADO => "Encaminhado para unidade",
			Contato::CONTATO_STATUS_EM_ANDAMENTO => "Em andamento",
			Contato::CONTATO_STATUS_INTERESSADO => "Interessado",
			Contato::CONTATO_STATUS_MATRICULADO => "Matriculado"
		);
	}

  public static function getConstanteStatus ($status) {

    $status = mb_strtolower($status, 'utf-8');

    $pattern = '/^' . $status . '/';

    if (preg_match($pattern, "desistente", $matches, PREG_OFFSET_CAPTURE)) {
      return Contato::CONTATO_STATUS_DESISTENTE;
    }
    else if (preg_match($pattern, "encaminhado para unidade", $matches, PREG_OFFSET_CAPTURE)) {
      return Contato::CONTATO_STATUS_ENCAMINHADO;
    }
    else if (preg_match($pattern, "em andamento", $matches, PREG_OFFSET_CAPTURE)) {
      return Contato::CONTATO_STATUS_EM_ANDAMENTO;
    }
    else if (preg_match($pattern, "interessado", $matches, PREG_OFFSET_CAPTURE)) {
      return Contato::CONTATO_STATUS_INTERESSADO;
    }
    else if (preg_match($pattern, "matriculado", $matches, PREG_OFFSET_CAPTURE)) {
      return Contato::CONTATO_STATUS_MATRICULADO;
    }
    else if (preg_match($pattern, "nenhum", $matches, PREG_OFFSET_CAPTURE)) {
      return -1;
    }
  }

  public static function getConstanteRespondido ($respondido) {

    $respondido = mb_strtolower($respondido, 'utf-8');

    $pattern = '/^' . $respondido . '/';

    if (preg_match($pattern, "sim", $matches, PREG_OFFSET_CAPTURE)) {
      return 1;
    }
    else if (preg_match($pattern, "nao", $matches, PREG_OFFSET_CAPTURE)) {
      return 0;
    }
    else if (preg_match($pattern, "não", $matches, PREG_OFFSET_CAPTURE)) {
      return 0;
    }
    else {
      return -2;
    }
  }

}

?>
