<?php

class Noticia extends Model {
	
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
    protected $_titulo;
	
	/**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_link;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @editor
    */
    protected $_noticia;
    
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
    */
    protected $_visualizacoes;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_tags;
	
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
    * @length 255
    */
    protected $_dataExtenso;

    protected $_table = 'noticias';

}

?>