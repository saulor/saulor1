<?php

class Docente extends Model {
    
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
    protected $_instituicao;

	/**
    * @column
    * @readwrite
    * @type text
    */   
    protected $_graduacoes;

	/**
    * @column
    * @readwrite
    * @type text
    */   
    protected $_graduacoesSenior;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_titulacao;
    
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
    protected $_outrosDocumentos;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_curriculoLattes;
    
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
    * @type integer
    */
    protected $_situacao;

    /**
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_cursos;

    /**
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_disciplinas;
    
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
    protected $_observacoes;

    protected $_table = 'docentes';
    
    const DOCENTE_TIPO_CONTA_CORRENTE = 1;
    const DOCENTE_TIPO_CONTA_POUPANCA = 2;
    const DOCENTE_TITULACAO_DOUTOR = 1;
    const DOCENTE_TITULACAO_MESTRE = 2;
    const DOCENTE_TITULACAO_ESPECIALISTA = 3;
    
    const DOCENTE_SITUACAO_CANDIDATO = 1;
    const DOCENTE_SITUACAO_CONTRATADO = 2;
    const DOCENTE_SITUACAO_CANCELADO = 3;

    public function __construct ($conexao) {
        parent::__construct($conexao);
        $this->cursos[] = new DocenteCursoDisciplina($conexao);
    }
    
    public static function getTitulacao ($titulacao) {
	    switch ($titulacao) {
	    	case Docente::DOCENTE_TITULACAO_DOUTOR :
	    		return "Doutor";
	    	break;
	    	
	    	case Docente::DOCENTE_TITULACAO_MESTRE :
	    		return "Mestre";
	    	break;
	    	
	    	case Docente::DOCENTE_TITULACAO_ESPECIALISTA :
	    		return "Especialista";
	    	break;
	    }
    }
    
    public static function getTitulacoes () {
    	return array(
    		Docente::DOCENTE_TITULACAO_ESPECIALISTA => "Especialista",
    		Docente::DOCENTE_TITULACAO_MESTRE => "Mestre",
    		Docente::DOCENTE_TITULACAO_DOUTOR => "Doutor"
    	);
    }
    
    public static function getSituacao ($situacao) {
        switch ($situacao) {
        	case Docente::DOCENTE_SITUACAO_CANDIDATO :
        		return "Candidato";
        	break;
        	
        	case Docente::DOCENTE_SITUACAO_CONTRATADO :
        		return "Contratado";
        	break;
        	
        	case Docente::DOCENTE_SITUACAO_CANCELADO :
        		return "Cancelado";
        	break;
        }
    }
    
    public static function getSituacoes () {
    	return array(
    		Docente::DOCENTE_SITUACAO_CANDIDATO => "Candidato",
    		Docente::DOCENTE_SITUACAO_CONTRATADO => "Contratado",
    		Docente::DOCENTE_SITUACAO_CANCELADO => "Cancelado"
    	);
    }

    public function getObjetoOrFail($id) {
        try {
            $objeto = parent::getObjetoOrFail($id);
            $dcd = new DocenteCursoDisciplina($this->_conexao);
            $objeto->cursos = $cursosDisciplinas = $dcd->getObjetos(array(
                    'where' => array(
                        'docente' => $objeto->id
                    )
                )
            );
            return $objeto;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function getObjetos ($params = array()) {
        try {
            $dcd = new DocenteCursoDisciplina($this->_conexao);
            $objetos = parent::getObjetos($params);
            foreach ($objetos as $key => $docente) {
                $cursosDisciplinas = $dcd->getObjetos(array(
                        'where' => array(
                            'docente' => $docente->id
                        )
                    )
                );
                
                $cursos = $disciplinas = array();
                foreach ($cursosDisciplinas as $cursoDisciplina) {
                    $cursos[] = $cursoDisciplina->curso;
                    $disciplinas[] = $cursoDisciplina->disciplinas;
                }

                $objetos[$key]->cursos = implode(', ', $cursos);
                $objetos[$key]->disciplinas = implode(', ', $disciplinas);
            }
            return $objetos;
        }
        catch (Exception $e) {
            throw $e;
        }
    }
  
}

?>