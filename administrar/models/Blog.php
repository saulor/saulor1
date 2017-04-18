<?php

class Blog extends Model {
	
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
    protected $_conteudo;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_unidade;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_slugUnidade;
    
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

    protected $_table = 'blog';

    public static function getUnidades() {
        return array(
            "Maringá",
            "Londrina",
            "Teresina",
            "Belém",
            "Cascavel",
            "Campinas",
            "Fortaleza",
            "Salvador",
            "Brasília",
            "Curitiba",
            "Porto Alegre"
        );
    }

    public static function getUnidade ($unidade) { 
        foreach (self::getUnidades() as $key => $value) {
            if (is_int($unidade)) {
                if ($key == $unidade) {
                    return $value;
                }
            }
            else if (is_string($unidade)) {
                if ($unidade == Funcoes::criaSlug($value)) {
                    return $value;
                }
            }
        }
        return NULL;
    }
}

?>