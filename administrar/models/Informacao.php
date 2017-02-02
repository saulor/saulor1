<?php

class Informacao extends Model {

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
    * @length 14
    * @planilha
    */
	protected $_cpf;
	
	/**
    * @column
    * @readwrite
    * @type text
    * @length 255
    * @planilha
    */
	protected $_endereco;
	
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
	protected $_uf;
	
	/**
    * @column
    * @readwrite
    * @type text
    * @length 30
    * @planilha
    */
	protected $_cep;
	
	/**
    * @column
    * @readwrite
    * @type text
    * @length 255
    * @planilha
    */
	protected $_cidadeCurso;
	
	/**
    * @column
    * @readwrite
    * @type text
    * @length 15
    * @planilha
    */
	protected $_telefoneResidencial;
	
	/**
    * @column
    * @readwrite
    * @type text
    * @length 15
    * @planilha
    */
	protected $_telefoneCelular;
	
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
	protected $_profissao;
	
	/**
    * @column
    * @readwrite
    * @type text
    * @length 255
    * @planilha
    */
	protected $_cursosInteresse;
	
	/**
    * @column
    * @readwrite
    * @type text
    * @length 255
    * @planilha
    */
	protected $_comoConheceu;
	
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
	
	/**
    * @column
    * @readwrite
    * @type text
    * @planilha
    */
	protected $_observacoes;

    protected $_table = 'informacoes';

    public function __construct ($conexao) {
        parent::__construct($conexao);
    }
	
}

?>