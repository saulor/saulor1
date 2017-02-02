<?php

class CidadeCurso extends Model {
    
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
    protected $_cidade;

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
    * @type decimal
    * @length 14
    */
    protected $_valorCurso;
    
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
    * @type date
    */
    protected $_previsaoInicio;
	
	/**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_status;

    protected $_table = 'cidades_cursos';
      
}

?>