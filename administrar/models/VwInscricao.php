<?php

class VwInscricao extends Model {

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
    protected $_idCidade;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_idCurso;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_nomeCurso;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_linkCurso;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_tipoCurso;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_certificadoraCurso;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_idCidadeCurso;

    /**
    * @column
    * @readwrite
    * @type decimal
    * @length 14
    */
    protected $_valorCurso;

    /**
    * @column
    * @readwrite
    * @type decimal
    * @length 14
    */
    protected $_valorDesconto;
    
    /**
    * @column
    * @readwrite
    * @type decimal
    * @length 14
    */
    protected $_valorInscricao;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_quantidadeParcelas;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_quantidadeModulos;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_cargaHoraria;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_duracao;

    /**
    * @column
    * @readwrite
    * @type date
    */
    protected $_previsaoInicio;
	
	/**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_statusUnidade;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_nomeUnidade;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 2
    */
    protected $_siglaEstado;

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
    protected $_unidadeCertificadora;

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
    * @type text
    * @length 14
    */
    protected $_cpf;

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
    * @length 30
    */
    protected $_cep;

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
    protected $_naturalidade;

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
    protected $_responsavelNome;
    
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
    * @type text
    * @length 20
    */
    protected $_telefone;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_whatsapp;

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
    protected $_status;

    /**
    * @column
    * @readwrite
    * @type text
    */
    protected $_observacoes;
    
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
    * @type integer
    */
    protected $_visualizada;

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
    * @length 10
    */
    protected $_dataExpedicaoF;

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
    * @length 10
    */
    protected $_dataNascimentoF;

    /**
    * @column
    * @readwrite
    * @type datetime
    */
    protected $_data;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 10
    */
    protected $_dataF;

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
    protected $_idSituacao;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_tipoSituacao;

    /**
    * @column
    * @readwrite
	* @type text
    * @length 10
    */
    protected $_dataRetorno;

    /**
    * @column
    * @readwrite
	* @type text
    * @length 255
    */
    protected $_horarioRetorno;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_motivo;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_obsSituacao;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_cidadeSituacao;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 2
    */
    protected $_estadoSituacao;

    /**
    * @column
    * @readwrite
    * @type date
    */
    protected $_dataCSituacao;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_timestampDataCSituacao;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_nomeUsuarioSituacao;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_idUsuarioSituacao;

    protected $_table = 'vw_preinscricoes_v3';

}

?>