<?php

class Pagina extends Model {
    
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
    protected $_pagina;
    
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
    * @editor
    */
    protected $_conteudo;
	
	/**
    * @column
    * @type integer
    */
    protected $_visualizacoes;
	
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

    protected $_table = 'paginas';

    const PAGINA_CATEGORIA_SOBRE = 1;
    const PAGINA_CATEGORIA_POSICIONAMENTO = 2;
    const PAGINA_CATEGORIA_FACULDADE_MNS = 3;
    const PAGINA_CATEGORIA_FACULDADE_UNI = 4;
    const PAGINA_CATEGORIA_FACULDADE_FIP = 5;
    const PAGINA_CATEGORIA_FACULDADE_ANHEMBI = 6;
    const PAGINA_CATEGORIA_FACULDADE_FMU = 7;
    const PAGINA_CATEGORIA_FACULDADE_UNP = 8;

    public function __construct ($conexao) {
        parent::__construct($conexao);
    }
    
    public static function getConstantes() {
    	return array(
    		1 => "Sobre o IEFAP",
    		2 => "Posicionamento Estratégico",
    		3 => "Faculdade Maurício de Nassau",
            4 => "Uningá",
            5 => "Faculdades Integradas de Patos (FIP)",
            6 => "Universidade Anhembi Morumbi",
            7 => "FMU - Faculdades Metropolitanas Unidas",
            8 => "UNP - Universidade Potiguar"
    	);
    }
    
}

?>