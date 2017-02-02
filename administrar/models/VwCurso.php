<?php

class VwCurso extends Curso {
	
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
    * @length 255
    */
    protected $_banner;
    
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
    * @length 50
    */
    protected $_nomeBreve;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_categoria;
    
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
    protected $_area;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_imagem;

    /**
    * @column
    * @readwrite
    * @type text
    * @editor
    */
    protected $_publicoAlvo;

    /**
    * @column
    * @readwrite
    * @type text
    * @editor
    */
    protected $_publicoAlvoResumo;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @editor
    */
    protected $_descricao;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @editor
    */
    protected $_objetivosGerais;

    /**
    * @column
    * @readwrite
    * @type text
    * @editor
    */
    protected $_objetivosEspecificos;
    
    /**
    * @column
    * @readwrite
    * @type text
    */
    protected $_disciplinas;
    
    /**
    * @type array
    */
    protected $_estados;
    
    /**
    * @column
    * @readwrite
    * @type text
    */
    protected $_facilitadoresProfessores;
   
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_status;
    
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
    protected $_visualizacoes;
    
    /**
    * @column
    * @readwrite
    * @type text
    */
    protected $_localHorarios;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_vinculado;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_thumbnail;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_thumbStatus;

    /**
    * @column
    * @readwrite
    * @type autonumber
    */
    protected $_idCategoria;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_nomeCategoria;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_slugCategoria;
    
    /**
    * @column
    * @readwrite
    * @type longtext
    */
    protected $_descricaoCategoria;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_pai;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_visivel;
    
    /**
    * @column
    * @readwrite
     * @type text
    * @length 255
    */
    protected $_profundidade;

    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_caminho;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_imagemCategoria;

    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_dataCadastro;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_dataAtualizacao;

    protected $_table = 'vw_cursos';
	
}

?>