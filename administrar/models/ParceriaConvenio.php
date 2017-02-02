<?php

class ParceriaConvenio extends Model {
    
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
    protected $_instituicaoEmpresa;
    
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
    * @length 20
    */
    protected $_telefone1;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 20
    */
    protected $_telefone2;
   
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
    protected $_contato;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_desconto;
    
    /**
    * @column
    * @readwrite
    * @type longtext
    */
    protected $_observacoes;
    
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

    protected $_table = 'parcerias_convenios';
   
}

?>