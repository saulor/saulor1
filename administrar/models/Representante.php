<?php

class Representante extends Model {
	
    const REPRESENTANTE_TIPO_CONTA_CORRENTE = 1;
    const REPRESENTANTE_TIPO_CONTA_POUPANCA = 2;
    
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
    * @type date
    */
    protected $_dataNascimento;
    
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
    * @length 15
    */
    protected $_telefoneResidencial;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 10
    */
    protected $_operadoraCelular;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 15
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
    protected $_banco;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_tipoConta;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_agencia;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_conta;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_demaisInformacoes;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_curriculoComercial;
    
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
    */
    protected $_observacoes;

    protected $_table = 'representantes';

}

?>