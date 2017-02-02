<?php

class Inscricao extends Model {

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
    protected $_naturalidade;

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
    * @length 30
    */
    protected $_cep;

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

}

?>
