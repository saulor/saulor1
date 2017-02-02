<?php

class Parceiro extends Model {
    
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
    * @length 255
    */
    protected $_nomeFantasia;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_razaoSocial;
    
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
    * @length 18
    */
    protected $_cnpj;
    
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
    protected $_estado;
    
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
    */
    protected $_solicitacao;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_tipo;
    
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

    protected $_table = 'parceiros';
    
    const PARCEIRO_TIPO_PF = 1;
    const PARCEIRO_TIPO_PJ = 2;
    
    public static function getTipo ($_tipo) {
    	switch ($_tipo) {
    		
    		case Parceiro::PARCEIRO_TIPO_PF :
    			return "Pessoa Física";
    		break;
    		
    		case Parceiro::PARCEIRO_TIPO_PJ :
    			return "Pessoa Jurídica";
    		break;
    		
    	}
    }

}

?>