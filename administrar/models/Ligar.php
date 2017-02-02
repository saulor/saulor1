<?php

class Ligar extends Model {

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
    * @length 255
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
    protected $_assunto;

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
    * @length 255
    * @planilha
    */
    protected $_operadora;

    /**
    * @column
    * @readwrite
	* @type text
    * @length 255
    * @planilha
    */
    protected $_telefone;

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
    * @length 255
    * @planilha
    */
    protected $_horario;

	/**
    * @column
    * @readwrite
	* @type longtext
    * @planilha
    */
    protected $_observacoes;

	/**
    * @column
    * @readwrite
    * @type integer
    * @planilha
    */
    protected $_situacao;

    /**
    * @column
    * @readwrite
    * @type integer
    * @planilha
    */
    protected $_whatsapp;

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

    /**
    * @column
    * @readwrite
    * @type datetime
    * @planilha
    */
    protected $_data;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_timestamp;

    protected $_table = 'ligar';

    const LIGAR_SITUACAO_JA_LIGAMOS = 1;
    const LIGAR_SITUACAO_NAO_ATENDEU = 2;
    const LIGAR_SITUACAO_FALAMOS_NO_WHATSAPP = 3;
    const LIGAR_SITUACAO_ENVIAMOS_EMAIL = 4;

    public function __construct ($conexao) {
        parent::__construct($conexao);
    }

    public static function getSituacao ($situacao) {
        switch ($situacao) {
            case self::LIGAR_SITUACAO_JA_LIGAMOS :
                return "Já ligamos";
            break;
            
            case self::LIGAR_SITUACAO_NAO_ATENDEU :
                return "Não atendeu";
            break;

            case self::LIGAR_SITUACAO_FALAMOS_NO_WHATSAPP :
                return "Falamos no Whatsapp";
            break;

            case self::LIGAR_SITUACAO_ENVIAMOS_EMAIL :
                return "Enviamos e-mail";
            break;
        }

    }

    public static function getSituacoes() {
        return array(
            self::LIGAR_SITUACAO_JA_LIGAMOS => "Já ligamos",
            self::LIGAR_SITUACAO_NAO_ATENDEU => "Não atendeu",
            self::LIGAR_SITUACAO_FALAMOS_NO_WHATSAPP => "Falamos no Whatsapp",
            self::LIGAR_SITUACAO_ENVIAMOS_EMAIL => "Enviamos e-mail"
        );
    }

}

?>
