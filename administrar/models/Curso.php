<?php

class Curso extends Model {
    
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
    * @editor
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
    * @editor
    */
    protected $_facilitadoresProfessores;

    /**
    * @column
    * @readwrite
    * @type text
    * @editor
    */
    protected $_localHorarios;
   
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

    protected $_table = 'cursos';
        
    const CURSO_UNIDADE_CERTIFICADORA_FIP = 1;
    const CURSO_UNIDADE_CERTIFICADORA_NASSAU = 2;
    const CURSO_UNIDADE_CERTIFICADORA_IEFAP = 3;
    const CURSO_UNIDADE_CERTIFICADORA_OUTROS = 4;
	const CURSO_UNIDADE_CERTIFICADORA_UNINGA = 5;
    const CURSO_UNIDADE_CERTIFICADORA_LAUREATE = 6;
	
	const CURSO_TIPO_POS = 1;
	const CURSO_TIPO_APERFEICOAMENTO = 2;
	const CURSO_TIPO_EAD = 3;
	
	const CURSO_AREA_BIOLOGICAS = 3;
	const CURSO_AREA_EXATAS = 2;
	const CURSO_AREA_HUMANAS = 1;

    public function getQuery ($filtro = array('*')) {
        try {
            $query = $this->_conexao->query()->from($this->_table, $filtro);

            // administradores master
            if ($_SESSION[PREFIX . 'loginCodigo'] == 33) {
                return $query;
            }

            // se na lista de cursos da sessão não existe nenhum curso significa que
            // o usuário logado não pode acessar nenhum curso
            if (count($_SESSION[PREFIX . "cursos"]) == 0) {
                $query->where('1 = 2'); // false clause
            }
            else {
                $query->where('id IN ?', array_map('intval', $_SESSION[PREFIX . "cursos"]));
            }
            
            return $query;
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function findOrFail ($id) {
        try {
            $objeto = $this->getQuery()
                ->where('id = ?', (int) $id)
                ->first();
            
            if (!$objeto) {
                throw new Exception('Curso não localizado');
            }

            //$objeto = array_map('decode', $objeto);

            $queryUnidades = $this->_conexao->query()->from('vw_cidades_cursos')
                ->where('curso = ?', (int) $objeto['id']);

            $objeto['unidades'] = $queryUnidades->all();
            
            return $objeto;
        }
        catch (Exception $e) {
            throw $e;
        }
    }
	
	public static function getTipo ($tipo) {
		switch ($tipo) {
			case Curso::CURSO_TIPO_POS :
				return "Pós-Graduação";
			break;
			case Curso::CURSO_TIPO_APERFEICOAMENTO :
				return "Aperfeiçoamento Profissional";
			break;
			case Curso::CURSO_TIPO_EAD :
				return "EAD";
			break;
			default :
				return "Não definido";
			break;
		}
	}

    public static function getSlug ($tipo) {
        switch ($tipo) {
            case Curso::CURSO_TIPO_POS :
                return "posgraduacao";
            break;
            case Curso::CURSO_TIPO_APERFEICOAMENTO :
                return "aperfeicoamento";
            break;
            default :
                return "posgraduacao";
            break;
        }
    }
	
	public static function getArea ($area) {
		switch ($area) {
			case Curso::CURSO_AREA_BIOLOGICAS :
				return "Biológicas";
			break;
			case Curso::CURSO_AREA_EXATAS :
				return "Exatas";
			break;
			case Curso::CURSO_AREA_HUMANAS :
				return "Humanas";
			break;
			default :
				return "Não definida";
			break;
		}
	}
	
	public static function getCertificadora ($unidadeCertificadora) {
		switch ($unidadeCertificadora) {
			case Curso::CURSO_UNIDADE_CERTIFICADORA_FIP :
				return "FIP";
			break;
			case Curso::CURSO_UNIDADE_CERTIFICADORA_NASSAU :
				return "Nassau";
			break;
			case Curso::CURSO_UNIDADE_CERTIFICADORA_IEFAP :
				return "IEFAP";
			break;
			case Curso::CURSO_UNIDADE_CERTIFICADORA_UNINGA :
				return "Uningá";
			break;
			case Curso::CURSO_UNIDADE_CERTIFICADORA_OUTROS :
				return "Outros";
			break;
            case Curso::CURSO_UNIDADE_CERTIFICADORA_LAUREATE :
                return "Laureate";
            break;
			default :
				return "Não definida";
			break;
		}
	}
	
	public static function getTipos () {
		return array(
			Curso::CURSO_TIPO_POS => "Pós-Graduação",
			Curso::CURSO_TIPO_APERFEICOAMENTO => "Aperfeiçoamento Profissional",
            Curso::CURSO_TIPO_EAD => "EAD"
		);
	}
	
	public static function getCertificadoras () {
		return array(
			Curso::CURSO_UNIDADE_CERTIFICADORA_IEFAP => "IEFAP",
			Curso::CURSO_UNIDADE_CERTIFICADORA_FIP => "FIP",
			Curso::CURSO_UNIDADE_CERTIFICADORA_NASSAU => "Nassau",
			Curso::CURSO_UNIDADE_CERTIFICADORA_UNINGA => "Uningá",
            Curso::CURSO_UNIDADE_CERTIFICADORA_LAUREATE => "Laureate",
			Curso::CURSO_UNIDADE_CERTIFICADORA_OUTROS => "Outros"
		);
	}
	
	public static function getCertificadoraInfo ($unidadeCertificadora) {
		switch ($unidadeCertificadora) {
			case Curso::CURSO_UNIDADE_CERTIFICADORA_FIP :
				return array(
					'nome' => 'FIP - Faculdades Integradas de Patos',
                    'credenciamento' => 'http://emec.mec.gov.br/emec/consulta-cadastro/detalhamento/d96957f455f6405d14c6542552b0f6eb/MzMwNA==',
					'logo-impressao' => 'imagens/logo-impressao-formulario-fip.gif',
					'x' => 10,
					'y' => 2,
					'tamanho' => 60,
					'r' => 58,
					'g' => 67,
					'b' => 132,
					'nomeInstituicaoX' => 60,
					'nomeInstituicaoY' => 12,
					'parceria' => false
				);
			break;
			case Curso::CURSO_UNIDADE_CERTIFICADORA_NASSAU :
				return array(
					'nome' => 'Uninassau - Centro Universitário Maurício de Nassau',
                    'credenciamento' => 'http://emec.mec.gov.br/emec/consulta-cadastro/detalhamento/d96957f455f6405d14c6542552b0f6eb/MjgzNQ==',
					'logo-impressao' => 'imagens/logo-impressao-formulario-nassau.gif',
					'x' => 10,
					'y' => 5,
					'tamanho' => 55,
					'r' => 16,
					'g' => 35,
					'b' => 74,
					'nomeInstituicaoX' => 65,
					'nomeInstituicaoY' => 15,
					'parceria' => false
				);
			break;
			case Curso::CURSO_UNIDADE_CERTIFICADORA_IEFAP :
				return array(
					'nome' => 'IEFAP - Instituto de Ensino, Formação e Aperfeiçoamento LTDA.',
					'logo-impressao' => '../templates/default/assets/images/logo.png',
					'x' => 10,
					'y' => 7,
					'tamanho' => 30,
					'r' => 16,
					'g' => 35,
					'b' => 74,
					'nomeInstituicaoX' => 45,
					'nomeInstituicaoY' => 13,
					'parceria' => false
				);
			break;
			case Curso::CURSO_UNIDADE_CERTIFICADORA_UNINGA :
				return array(
					'nome' => 'Uningá - Centro Universitário Ingá',
                    'credenciamento' => 'http://emec.mec.gov.br/emec/consulta-cadastro/detalhamento/d96957f455f6405d14c6542552b0f6eb/MTQzMA==',
					'logo-impressao' => 'imagens/logo-impressao-formulario-inga.gif',
					'x' => 8,
					'y' => 4,
					'tamanho' => 53,
					'r' => 16,
					'g' => 35,
					'b' => 74,
					'nomeInstituicaoX' => 70,
					'nomeInstituicaoY' => 12,
					'parceria' => false
				);
			break;
			default :
				return array();
			break;
		}
	}
	
	public static function getAreas () {
		return array(
			Curso::CURSO_AREA_BIOLOGICAS => "Biológicas",
			Curso::CURSO_AREA_EXATAS => "Exatas",
			Curso::CURSO_AREA_HUMANAS => "Humanas"
		);
	}
    
}

?>