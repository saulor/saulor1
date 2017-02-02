<?php
/*
	Classe que mapeia em quais estados um curso está disponível
*/
class CursoEstadoCidade extends Model {
    
    /**
    * @column
    * @readwrite
    * @primary
    * @type autonumber
    */
    private $_id;
    
    /**
    * @column
    * @readwrite
    * @type integer
    * @index
    * @foreign
    * @references cursos_estados (id)
    * @delete cascade
    * @update cascade
    */
    private $_cursoEstado;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    private $_cidade;
    
    /**
    * @column
    * @readwrite
    * @type decimal
    * @length 14
    */
    private $_valorDesconto;
    
    /**
    * @column
    * @readwrite
    * @type decimal
    * @length 14
    */
    private $_valorInscricao;
    
    /**
    * @column
    * @readwrite
    * @type decimal
    * @length 14
    */
    private $_valorCurso;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    private $_quantidadeParcelas;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    private $_quantidadeModulos;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @type integer
    */
    private $_cargaHoraria;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    private $_duracao;
    
    /**
    * @column
    * @readwrite
    * @type date
    */
    private $_previsaoInicio;
    
    /**
    * @column
    * @readwrite
    * @type text
    */
    private $_informacoesAulas;
    
    /**
    * @column
    * @readwrite
    * @type text
    */
    private $_cronograma;
    
    /* cursos de aperfeiçoamento */
    
    /**
    * @column
    * @readwrite
    * @type text
    */
    private $_palestrantes;
    
    /**
    * @column
    * @readwrite
    * @type text
    */
    private $_dataDuracaoLocal;
    
    /**
    * @column
    * @readwrite
    * @type text
    */
    private $_investimento;
    
    /**
    * @column
    * @readwrite
    * @type text
    */
    private $_pagamento;
	
	/**
    * @column
    * @readwrite
    * @type integer
    */
    private $_status;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    private $_emails;

    protected $_conexao;

    public function __construct($conexao) {
        $this->_conexao = $conexao;
    }

    public function getQuery ($table = 'cursos_estados_cidades') {
        try {
            return $this->_conexao->query()->from($table);
        }
        catch (Exception $e) {
            throw $e;
        }
    }
      
}

?>