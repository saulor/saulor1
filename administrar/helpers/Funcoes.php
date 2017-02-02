<?php

class Funcoes {

	public static function getEstadosRegioes ($conexao, $dao) {

		try {
			$regioes = $dao->findAll($conexao, "regioes");
			$regioesArray = array();
			foreach($regioes as $regiao) {

				$estadosArray = array();
				$estados = $dao->findAll($conexao, "estados", array(
						"where" => array(
							"regiao" => (int) $regiao["id"]
						)
					)
				);

				foreach($estados as $estado) {

					// armazena o id
					$estadosArray[$estado["sigla"]]["id"] = $estado["id"];

					// verifica em quais cursos o estado possui cidades cadastradas
					$cursos = $dao->findAll($conexao, "cursos", array(
							"dados" => array(
								"distinct cursos.id"
							),
							"join" => array(
								"cursos_estados" => "cursos.id = cursos_estados.curso",
								"cursos_estados_cidades" => "cursos_estados.id = cursos_estados_cidades.cursoEstado"
							),
							"where" => array(
								"cursos_estados.estado" => (int) $estado["id"]
							)
						)
					);

					$ids = array();
					foreach ($cursos as $curso) {
						$ids[] = $curso["id"];
					}

					// armazena os ids dos cursos
					$estadosArray[$estado["sigla"]]["cursos"] = $ids;

					// verifica em quais cidades o estado possui cidades cadastradas
					$cidades = $dao->findAll($conexao, "cursos_estados_cidades", array(
							"dados" => array(
								"distinct cursos_estados_cidades.cidade"
							),
							"join" => array(
								"cursos_estados" => "cursos_estados_cidades.cursoEstado = cursos_estados.id",
								"cursos" => "cursos.id = cursos_estados.curso"
							),
							"where" => array(
								"cursos_estados.estado" => (int) $estado["id"]
							)
						)
					);

					$ids = array();
					foreach ($cidades as $cidade) {
						$ids[] = $cidade["cidade"];
					}

					// armazena os nomes das cidades
					$estadosArray[$estado["sigla"]]["cidades"] = $ids;
				}

				$regioesArray[$regiao["nome"]] = $estadosArray;
			}
			return $regioesArray;
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public static function jaExiste ($conexao, $dao, $dados, $tabela, $campo) {
		try {
			$paramsQuery = array(
				"where" => array(
					$campo => $dados[$campo]
				),
				"whereNot" => array(
					"id" => $dados["id"]
				)
			);
			return count($dao->findAll($conexao, $tabela, $paramsQuery)) > 0;
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public static function cast ($destination, $sourceObject) {
		if (is_string($destination)) {
			$destination = new $destination();
		}
		$sourceReflection = new ReflectionObject ($sourceObject);
		$destinationReflection = new ReflectionObject ($destination);
		$sourceProperties = $sourceReflection->getProperties();
		foreach ($sourceProperties as $sourceProperty) {
			//$sourceProperty->setAcessible(true);
			$name = $sourceProperty->getName();
			$value = $sourceProperty->getValue($sourceObject);
			if ($destinationReflection->hasProperty($name)) {
				$propDest = $destinationReflection->getProperty($name);
				//$propDest->setAcessible(true);
				$propDest->setValue($destination, $value);
			}
			else {
				$destination->$name = $value;
			}
		}
		return $destination;
	}

	public static function arrayToObject ($dados) {
      if (is_array($dados)) {
        return (object) array_map(array('Funcoes', 'arrayToObject'), $dados);
      }
      else {
        return $dados;
      }
  }

  public static function objectToArray ($obj) {
    if (is_object($obj)) {
      $obj = get_object_vars($obj);
    }
    if (is_array($obj)) {
      return array_map(array('Funcoes', 'objectToArray'), $obj);
    }
    else {
      return $obj;
    }
  }

	/*
	*	Função que calcula a diferença entre duas datas. Data inicial deve ser maior que a
	*	data final.
	*	@param 	$from 	string 	Data inicial no formato dd/mm/yyyy
	*	@param 	$to 		string 	Data final no formato dd/mm/yyyy
	*	@return 				integer Retorna a diferença entre as duas datas
	*/
	public static function diferencaDatas ($from, $to) {

		if (empty($to)) {
			return -1;
		}

		if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $from)) {
			list($dia, $mes, $ano) = explode("/", $from);
			$from = $ano . "-" . $mes . "-" . $dia;
		}

		if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $to)) {
			list($dia, $mes, $ano) = explode("/", $to);
			$to = $ano . "-" . $mes . "-" . $dia;
		}

		$dFrom = new \DateTime($from);
		$dTo = new \DateTime($to);

		if ($dFrom > $dTo) {
			return -1;
		}
		else {
			$diff = $dTo->diff($dFrom);
			return $diff->days;
		}
	}

	public static function lowerCase ($word) {
		if (self::isUTF8($word)) {
			$word = self::decodificaDado($word);
			return mb_strtolower($word, "UTF-8");
		}
		return $word;	
	}

  public static function getValidationErrors ($validated) {
    $messages = array();
    if (is_array($validated[0])) {
      foreach($validated as $v) {
        switch($v['rule']) {
          case 'validate_required':
            $messages[] = '<p>O campo ' . ucwords($v['field']) . ' deve ser preenchido</p>';
          break;
          case 'validate_valid_email':
            $messages[] = '<p>O e-mail ' . $v['value'] . ' não representa um endereço de e-mail válido</p>';
          break;
          case 'validate_integer':
            $messages[] = '<p>O número ' . $v['value'] . ' não representa um número válido</p>';
          break;
          case 'validate_date':
            $messages[] = '<p>A data ' . $v['value'] . ' não representa uma data válida</p>';
          break;
          case 'validate_cep':
            $messages[] = '<p>O cep '. $v['value'] . ' não representa um cep válido</p>';
          break;
        }
      }
    }
    else {
      foreach($validated as $v) {
        $messages[] = '<p>' . $v . '</p>';
      }
    }
    return implode($messages);
  }

  /**
  * Codifica uma data do formato dd/mm/yyyy para o formato yyyy-mm-dd
  * @param string $data Data no formato dd/mm/yyyy
  * @return string Data convertida
  */
  public static function encodeDate ($date) {
    if (empty($date)) {
      return NULL;
    }
    return implode('-', array_reverse(explode("/", $date)));
  }

  /**
  * Decodifica uma data do formato yyyy-mm-dd para o formato dd/mm/YYYY
  * @param  string $data Data no formato yyyy-mm-dd
  * @return string Data desconvertida
  */
  public static function decodeDate ($date) {
    if (empty($date)) {
      return "";
    }
    list($ano, $mes, $dia) = explode("-", $date);
    return $dia . "/" . $mes . "/" . $ano;
  }

  /**
  * Codifica uma data do formato dd/mm/yyyy para o formato yyyy-mm-dd
  * @param  string $data Data no formato dd/mm/yyyy
  * @return string Data convertida
  */
  public static function encodeDatetime ($date) {
    if (empty($date)) {
      return NULL;
    }
    list($data, $hora) = explode(" ", $date);
    list($dia, $mes, $ano) = explode("/", $data);
    return $ano . "/" . $mes . "/" . $dia . " " . substr($hora, 0, 5);
  }

  /**
  * Decodifica uma data do formato yyyy-mm-dd hh:ii:ss para o formato dd/mm/yyyy hh:ii
  * @param  string $data Data no formato yyyy-mm-dd
  * @return string Data desconvertida
  */
  public static function decodeDatetime ($date) {
    if (empty($date)) {
      return "";
    }
    list($data, $hora) = explode(" ", $date);
    list($ano, $mes, $dia) = explode("-", $data);
    return $dia . "/" . $mes . "/" . $ano . " às " . substr($hora, 0, 5);
  }

  /**
  * Retorna a data a partir do timestamp no formato padrão especificado Y-m-d.
  * @param  integer $timestamp Data em formato timestamp
  * @return string Formato de retorno desejado
  */
  public static function dateFromTimestamp ($timestamp, $format = 'Y-m-d') {
    if ($timestamp == 0) {
      return NULL;
    }
    return date($format, $timestamp);
  }

  /**
  * Retorna data e hora a partir do timestamp no formato padrão especificado Y-m-d H:i:s.
  * @param  integer $timestamp Data em formato timestamp
  * @return string Formato de retorno desejado
  */
  public static function datetimeFromTimestamp ($timestamp, $format = 'Y-m-d H:i') {
    if ($timestamp == 0) {
      return NULL;
    }
    return date($format, $timestamp);
  }

  public static function getDataExtenso ($timestamp) {
      $daysOfWeek = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
      $months = array('January','February','March','April','May','June','July','August','September','October','November','December');
      $diasDaSemana = array('Domingo', 'Segunda-Feira', 'Terça-Feira', 'Quarta-Feira', 'Quinta-Feira', 'Sexta-Feira', 'Sábado');
      $meses = array('Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
      $data = strftime('%A, %d de %B de %Y', $timestamp);
      $data = str_replace($daysOfWeek, $diasDaSemana, $data);
      return str_replace($months, $meses, $data);
  }

  public static function codificaDado ($dado) {
  	return htmlentities($dado, ENT_NOQUOTES, "utf-8");
  }

  public static function decodificaDado ($dado) {
  	return html_entity_decode($dado, ENT_NOQUOTES, "utf-8");
  }

  public static function decodificaDados ($dados) {
    foreach ($dados as $key => $value) {
      $dados[$key] = html_entity_decode(stripslashes($value), ENT_NOQUOTES, "utf-8");
    }
    return $dados;
  }

  public static function verificaArquivo($arquivo, $mimes = array()) {
  	if (empty($mimes)) {
  		throw new Exception("os tipos de arquivos permitidos não foram informados");
  	}
    if (in_array($arquivo["type"], $mimes)) {
        return true;
   	}
    return false;
  }

  public static function enviouArquivo ($arquivo, $nome = "imagem") {
    if (isset($arquivo)) {
      if($arquivo[$nome]["error"] != 4) {
        return true;
      }
    }
    return false;
  }

	public static function excluiArquivo ($diretorio) {
	    if (is_file($diretorio)) {
	        if(@unlink($diretorio)) {
				return true;
			}
			return false;
	    }
	}

  public static function excluiDiretorio ($diretorio){
      $diretorio = cleanPath($diretorio);
      if(is_dir($diretorio)){
          excluiConteudo($diretorio);
          @rmdir($diretorio);
      }
  }

  private static function cleanPath ($word){
      return str_replace('amp;', '', $word);
  }

  public static function excluiConteudo ($diretorio){
      if(!self::estaVazia($diretorio)){
          if($dir = opendir($diretorio)){
              while(false !== $arq = readdir($dir)) {
                  if ($arq != '.' && $arq != '..') {
                      if( is_dir($diretorio . DS . $arq) ) {
                          if(self::estaVazia($diretorio . DS . $arq)) {
                              @rmdir($diretorio . DS . $arq);
                          }
                          else {
                              self::excluiConteudo($diretorio . DS . $arq);
                              rmdir($diretorio . DS . $arq);
                          }
                      }
                      else{
                          unlink($diretorio . DS . $arq);
                      }
                  }
              }
              closedir($dir);
          }
      }
  }

  public static function estaVazia ($pasta){
      if(file_exists($pasta . DS . 'Thumbs.db')) {
        @unlink($pasta . DS . 'Thumbs.db');
      }
      $files = scandir($pasta);
      if(count($files) > 2) {
        return false;
      }
      else {
        return true;
      }
  }

  public static function criaDiretorio ($diretorio) {
      if (is_dir($diretorio)) {
          return true;
      }
      if (@mkdir($diretorio,0777)) {
          return true;
      }
      return false;
  }

  public static function verificaTipo ($arquivo, $permitidos) {
      $permitidos = explode("|", $permitidos);
      if (in_array($arquivo["type"], $permitidos)) {
          return true;
      }
      return false;
  }

  public static function salvaArquivo ($arquivo, $diretorio) {
      if(@move_uploaded_file($arquivo['tmp_name'],$diretorio.'/'.$arquivo['name'])) {
          return true;
      }
      return false;
  }

  public static function getExtensao ($arquivo) {
      return pathinfo($arquivo["name"], PATHINFO_EXTENSION);
  }

  public static function enviarArquivo ($arquivo, $diretorio = NULL, $nome = "imagem", 
    $mimes = array()) {

  	// verifica se o diretório foi informado
  	if (empty($diretorio)) {
  		throw new Exception("É necessário informar o diretório da imagem");
  	}

  	// cria o diretório do arquivo
		if (!self::criaDiretorio ($diretorio)) {
			throw new Exception("Erro ao tentar criar diretório para thumbnail");
		}

		// muda nome do arquivo
		$arquivo[$nome]["name"] =  date("dmYHis") . '.' . self::getExtensao($arquivo[$nome]);
    while(is_file($diretorio . DS . $arquivo[$nome]['name'])) {
      $arquivo[$nome]["name"] =  (date("dmYHis") + 1) . '.' . self::getExtensao($arquivo[$nome]);
    }
		
		// verifica se o tipo da thumbnail enviada é um tipo permitido
		if (!empty($mimes) && !self::verificaTipo ($arquivo[$nome], array("image/jpeg"))) {
			throw new Exception("Apenas imagens no formato JPEG podem ser enviadas");
		}

		// envia o arquivo
		if (!self::salvaArquivo ($arquivo[$nome], $diretorio)) {
			throw new Exception("Erro ao tentar enviar imagem");
		}

		return $arquivo[$nome]["name"];

  }

  public static function removeAcentos ($string) {
	    	
	    $temAcento = false;

	    if (preg_match('/[\x80-\xff]/', $string)) {
	    	$temAcento = true;
	    }

	    if ($temAcento && self::isUTF8($string)) {
	    	$chars = array(
            // Decompositions for Latin-1 Supplement
            chr(194).chr(170) => 'a', chr(194).chr(186) => 'o',
            chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
            chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
            chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
            chr(195).chr(134) => 'AE',chr(195).chr(135) => 'C',
            chr(195).chr(136) => 'E', chr(195).chr(137) => 'E',
            chr(195).chr(138) => 'E', chr(195).chr(139) => 'E',
            chr(195).chr(140) => 'I', chr(195).chr(141) => 'I',
            chr(195).chr(142) => 'I', chr(195).chr(143) => 'I',
            chr(195).chr(144) => 'D', chr(195).chr(145) => 'N',
            chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
            chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
            chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
            chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
            chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
            chr(195).chr(158) => 'TH',chr(195).chr(159) => 's',
            chr(195).chr(160) => 'a', chr(195).chr(161) => 'a',
            chr(195).chr(162) => 'a', chr(195).chr(163) => 'a',
            chr(195).chr(164) => 'a', chr(195).chr(165) => 'a',
            chr(195).chr(166) => 'ae',chr(195).chr(167) => 'c',
            chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
            chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
            chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
            chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
            chr(195).chr(176) => 'd', chr(195).chr(177) => 'n',
            chr(195).chr(178) => 'o', chr(195).chr(179) => 'o',
            chr(195).chr(180) => 'o', chr(195).chr(181) => 'o',
            chr(195).chr(182) => 'o', chr(195).chr(184) => 'o',
            chr(195).chr(185) => 'u', chr(195).chr(186) => 'u',
            chr(195).chr(187) => 'u', chr(195).chr(188) => 'u',
            chr(195).chr(189) => 'y', chr(195).chr(190) => 'th',
            chr(195).chr(191) => 'y', chr(195).chr(152) => 'O',
            // Decompositions for Latin Extended-A
            chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
            chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
            chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
            chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
            chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
            chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
            chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
            chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
            chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
            chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
            chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
            chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
            chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
            chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
            chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
            chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
            chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
            chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
            chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
            chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
            chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
            chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
            chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
            chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
            chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
            chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
            chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
            chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
            chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
            chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
            chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
            chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
            chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
            chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
            chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
            chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
            chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
            chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
            chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
            chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
            chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
            chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
            chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
            chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
            chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
            chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
            chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
            chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
            chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
            chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
            chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
            chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
            chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
            chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
            chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
            chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
            chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
            chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
            chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
            chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
            chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
            chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
            chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
            chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
            // Decompositions for Latin Extended-B
            chr(200).chr(152) => 'S', chr(200).chr(153) => 's',
            chr(200).chr(154) => 'T', chr(200).chr(155) => 't',
            // Euro Sign
            chr(226).chr(130).chr(172) => 'E',
            // GBP (Pound) Sign
            chr(194).chr(163) => '',
            // Vowels with diacritic (Vietnamese)
            // unmarked
            chr(198).chr(160) => 'O', chr(198).chr(161) => 'o',
            chr(198).chr(175) => 'U', chr(198).chr(176) => 'u',
            // grave accent
            chr(225).chr(186).chr(166) => 'A', chr(225).chr(186).chr(167) => 'a',
            chr(225).chr(186).chr(176) => 'A', chr(225).chr(186).chr(177) => 'a',
            chr(225).chr(187).chr(128) => 'E', chr(225).chr(187).chr(129) => 'e',
            chr(225).chr(187).chr(146) => 'O', chr(225).chr(187).chr(147) => 'o',
            chr(225).chr(187).chr(156) => 'O', chr(225).chr(187).chr(157) => 'o',
            chr(225).chr(187).chr(170) => 'U', chr(225).chr(187).chr(171) => 'u',
            chr(225).chr(187).chr(178) => 'Y', chr(225).chr(187).chr(179) => 'y',
            // hook
            chr(225).chr(186).chr(162) => 'A', chr(225).chr(186).chr(163) => 'a',
            chr(225).chr(186).chr(168) => 'A', chr(225).chr(186).chr(169) => 'a',
            chr(225).chr(186).chr(178) => 'A', chr(225).chr(186).chr(179) => 'a',
            chr(225).chr(186).chr(186) => 'E', chr(225).chr(186).chr(187) => 'e',
            chr(225).chr(187).chr(130) => 'E', chr(225).chr(187).chr(131) => 'e',
            chr(225).chr(187).chr(136) => 'I', chr(225).chr(187).chr(137) => 'i',
            chr(225).chr(187).chr(142) => 'O', chr(225).chr(187).chr(143) => 'o',
            chr(225).chr(187).chr(148) => 'O', chr(225).chr(187).chr(149) => 'o',
            chr(225).chr(187).chr(158) => 'O', chr(225).chr(187).chr(159) => 'o',
            chr(225).chr(187).chr(166) => 'U', chr(225).chr(187).chr(167) => 'u',
            chr(225).chr(187).chr(172) => 'U', chr(225).chr(187).chr(173) => 'u',
            chr(225).chr(187).chr(182) => 'Y', chr(225).chr(187).chr(183) => 'y',
            // tilde
            chr(225).chr(186).chr(170) => 'A', chr(225).chr(186).chr(171) => 'a',
            chr(225).chr(186).chr(180) => 'A', chr(225).chr(186).chr(181) => 'a',
            chr(225).chr(186).chr(188) => 'E', chr(225).chr(186).chr(189) => 'e',
            chr(225).chr(187).chr(132) => 'E', chr(225).chr(187).chr(133) => 'e',
            chr(225).chr(187).chr(150) => 'O', chr(225).chr(187).chr(151) => 'o',
            chr(225).chr(187).chr(160) => 'O', chr(225).chr(187).chr(161) => 'o',
            chr(225).chr(187).chr(174) => 'U', chr(225).chr(187).chr(175) => 'u',
            chr(225).chr(187).chr(184) => 'Y', chr(225).chr(187).chr(185) => 'y',
            // acute accent
            chr(225).chr(186).chr(164) => 'A', chr(225).chr(186).chr(165) => 'a',
            chr(225).chr(186).chr(174) => 'A', chr(225).chr(186).chr(175) => 'a',
            chr(225).chr(186).chr(190) => 'E', chr(225).chr(186).chr(191) => 'e',
            chr(225).chr(187).chr(144) => 'O', chr(225).chr(187).chr(145) => 'o',
            chr(225).chr(187).chr(154) => 'O', chr(225).chr(187).chr(155) => 'o',
            chr(225).chr(187).chr(168) => 'U', chr(225).chr(187).chr(169) => 'u',
            // dot below
            chr(225).chr(186).chr(160) => 'A', chr(225).chr(186).chr(161) => 'a',
            chr(225).chr(186).chr(172) => 'A', chr(225).chr(186).chr(173) => 'a',
            chr(225).chr(186).chr(182) => 'A', chr(225).chr(186).chr(183) => 'a',
            chr(225).chr(186).chr(184) => 'E', chr(225).chr(186).chr(185) => 'e',
            chr(225).chr(187).chr(134) => 'E', chr(225).chr(187).chr(135) => 'e',
            chr(225).chr(187).chr(138) => 'I', chr(225).chr(187).chr(139) => 'i',
            chr(225).chr(187).chr(140) => 'O', chr(225).chr(187).chr(141) => 'o',
            chr(225).chr(187).chr(152) => 'O', chr(225).chr(187).chr(153) => 'o',
            chr(225).chr(187).chr(162) => 'O', chr(225).chr(187).chr(163) => 'o',
            chr(225).chr(187).chr(164) => 'U', chr(225).chr(187).chr(165) => 'u',
            chr(225).chr(187).chr(176) => 'U', chr(225).chr(187).chr(177) => 'u',
            chr(225).chr(187).chr(180) => 'Y', chr(225).chr(187).chr(181) => 'y',
            // Vowels with diacritic (Chinese, Hanyu Pinyin)
            chr(201).chr(145) => 'a',
            // macron
            chr(199).chr(149) => 'U', chr(199).chr(150) => 'u',
            // acute accent
            chr(199).chr(151) => 'U', chr(199).chr(152) => 'u',
            // caron
            chr(199).chr(141) => 'A', chr(199).chr(142) => 'a',
            chr(199).chr(143) => 'I', chr(199).chr(144) => 'i',
            chr(199).chr(145) => 'O', chr(199).chr(146) => 'o',
            chr(199).chr(147) => 'U', chr(199).chr(148) => 'u',
            chr(199).chr(153) => 'U', chr(199).chr(154) => 'u',
            // grave accent
            chr(199).chr(155) => 'U', chr(199).chr(156) => 'u',
        );

	    	$string = strtr($string, $chars);

	    } else {
            // Assume ISO-8859-1 if not UTF-8
            $chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
                .chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
                .chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
                .chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
                .chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
                .chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
                .chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
                .chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
                .chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
                .chr(252).chr(253).chr(255);

            $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

            $string = strtr($string, $chars['in'], $chars['out']);
            $double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
            $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
			$string = str_replace($double_chars['in'], $double_chars['out'], $string);
        }

	    return strtolower($string);
	}

	 /** 
	 * 	Checks to see if a string is utf8 encoded.
	 *
	 * 	NOTE: This function checks for 5-Byte sequences, UTF8
	 *       has Bytes Sequences with a maximum length of 4.
	 *
	 * 	@author bmorel at ssi dot fr (modified)
	 * 	@since 1.2.1
	 *
	 * 	@param string $str The string to be checked
	 * 	@return bool True if $str fits a UTF-8 model, false otherwise.
	 */
	public static function isUTF8 ($str) {
        self::mbstring_binary_safe_encoding();
        $length = strlen($str);
        self::reset_mbstring_encoding();
        for ($i=0; $i < $length; $i++) {
            $c = ord($str[$i]);
            if ($c < 0x80) $n = 0; # 0bbbbbbb
            elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
            elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
            elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
            elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
            elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
            else return false; # Does not match any model
            for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
        		if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
             	   return false;
            }
        }
        return true;
	}

	private static function mbstring_binary_safe_encoding ($reset = false) {
	    static $encodings = array();
	    static $overloaded = null;
	 
	    if (is_null($overloaded))
	        $overloaded = function_exists( 'mb_internal_encoding' ) && ( ini_get( 'mbstring.func_overload' ) & 2 );
	 
	    if (false === $overloaded)
	        return;
	 
	    if (!$reset) {
	        $encoding = mb_internal_encoding();
	        array_push($encodings, $encoding);
	        mb_internal_encoding('ISO-8859-1');
	    }
	 
	    if ($reset && $encodings) {
	        $encoding = array_pop($encodings);
	        mb_internal_encoding($encoding);
	    }
	}

	public static function reset_mbstring_encoding () {
	    self::mbstring_binary_safe_encoding(true);
	}

  public static function criaSlug ($string) {
    // remove os acentos
  	$string = self::removeAcentos (trim($string));
    //echo $string;die;
    // remove qualquer coisa que não seja letra, número ou espaço em branco
    $string = preg_replace('/[^\sa-z0-9$\/]/i', '', $string);
    //echo $string;die;
    // substitui ' - ' por '-'
    $string = preg_replace('/\s-\s/', '-', $string);
    //echo $string;die;
    // substitui os espaços em branco por -
    //echo preg_replace('/[^A-Za-z0-9-$]+/', '-', $string);;die;
  	return preg_replace('/[^A-Za-z0-9-$]+/', '-', $string);
  }

  public static function xmlToArray ($fname) {
    $sxi = new SimpleXmlIterator($fname, null, true);
    return self::sxiToArray($sxi);
  }

  public static function sxiToArray ($sxi, $pai = NULL) {
    $a = array();
    for($sxi->rewind(); $sxi->valid(); $sxi->next()) {

      $nome = trim($sxi->current()->attributes()->nome);

      $pai = !empty($sxi->current()->attributes()) ? trim($sxi->current()->attributes()) : 
        trim($sxi->current()->getName());

      if($sxi->hasChildren()){
        $a[$pai] = self::sxiToArray($sxi->current(), $pai);
      }
      else {
        $a[$sxi->current()->getName()] = strval($sxi->current());
      }
    }
    return $a;
  }

  public static function getMetaTags ($pagina, $xmlDir) {
    $catArray = self::xmlToArray($xmlDir);
    foreach ($catArray as $key => $value) {
      if ($key == $pagina) {
        return $value;
      }
    }    
  }

  /*  */

  public static function validaPost ($obrigatorios, $campos) {
    $camposVazios = array();
    foreach($campos as $key => $value) {
      if(array_key_exists($key, $obrigatorios)) {
        if (is_array($campos[$key])) {
          // imagem
          if (array_key_exists('tmp_name', $campos[$key]) && $campos[$key]['error'] == 4) {
            $camposVazios[] = $obrigatorios[$key]["nome"];
          }
        }
        else if (empty($campos[$key]) && !is_numeric($campos[$key])) {
          $camposVazios[] = $obrigatorios[$key]["nome"];
        }
      } 
    }
    if (count($camposVazios) > 0) {
        $mensagem = sprintf("O(s) campo(s) %s deve(m) ser preenchido(s).", implode(", ", $camposVazios));
        throw new Exception($mensagem);
    }
  }

  public static function decodeDate2 ($datetime, $format = 'd/m/Y à\s H:i') {
    return date($format, strtotime($datetime));
  }

  public static function encodeDate2 ($datetime, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($datetime));
  }

  public static function is_utf8($str) {
      return (bool) preg_match('//u', $str);
  }

  /**
  * Função compacta uma palavra com mais de 40 caracteres
  * @param $word string Palavra que deve ser compactada
  * @param $tamanho int Quantidade de caracteres que a palavra deve ter após a compactação
  */
  public static function compactaTexto ($word, $tamanho = 48) {
    $wordAux = strip_tags($word);
    $tamanhoAux = $tamanho;
      if (strlen($word) > $tamanho) {
          do {
             $word = substr($wordAux, 0, (($tamanhoAux++)-3)) . '...';
          } while (!self::is_utf8($word));
      }
      return $word;
  }

  public static function soData ($text) {
    if (preg_match('/\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:?[\d{2}]?/', $text)) {
      return explode(' ', $text)[0];
    }
    else if (preg_match('/\d{4}\/\d{2}\/\d{2} \d{2}:\d{2}:?[\d{2}]?/', $text)) {
      return date('d/m/Y', strtotime($text));
    }
  }

  public static function getFromTimestamp ($timestamp, $format = 'd/m/Y') {
    return date($format, $timestamp);
  }

  public static function converteDecimal($dado) {
    $valor = $dado;
    $v = "";
    if (strpos($valor, ",") !== false) {
      $partes = explode(".", $valor);
      foreach ($partes as $parte) {
        $v .= $parte;
      }
      $valor = $v;
    }
    return (float) str_replace(",", ".", $valor);
  }

  public static function moneyFormat ($valor) {
    if (empty($valor)) {
      return "0,00";
    }
    return number_format($valor, 2, ",", ".");
  }

  public static function getImagens($diretorio){
      $extensions = 'jpg|JPG|png|PNG|gif|GIF|bmp|BMP|jpeg|JPEG|ico|ICO';
      $files = array();
      if(is_dir($diretorio)){
          if ($dir = opendir($diretorio)) {
              while(false !== ($arq = readdir($dir))) {
                  if ((!is_dir($diretorio . DS . $arq) ) && (preg_match("/$extensions/",$arq))) {
                      $files[] = $arq;
                  }
              }
          }
      }
      return $files;
  }

  // site functions

  public static function cleanString ($word) {
      return strip_tags ($word);
  }

  public static function mesNoticia ($timestamp) {
      setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
      return substr(strtoupper(strftime('%B', $timestamp)),0,3);
  }

  public static function getMeta ($view = 'nao-encontrado') {
        $file = APPDIR . Url::relativeTemplatePath() . 'metatags.xml';
        if (is_file($file)) {
            $catArray = self::xmlToArray($file);
            if (!array_key_exists($view, $catArray)) {
                $item = $catArray['nao-encontrado'];
            }
            else {
                $item = $catArray[$view];
            }
        }
        return self::buildMeta($item);
    }

    private static function buildMeta ($meta) {
        $r = array();
        while (count($meta) > 0) {
            $defer = array();
            foreach ($meta as $k => $v) {   
                if (is_array($v)) {       
                    foreach ($v as $kk => $vv) {
                        $defer["$k.$kk"] = $vv;
                    }
                }  
                else {
                    $r[$k] = $v;
                }    
            }   
            $meta = $defer;
        }
        return $r;
    }

    public static function setMensagem ($tipo, $mensagem) {
      $m = array('tipo' => $tipo, 'mensagem' => $mensagem);
      Session::set('mensagem', $m);
    }

    public static function getMensagem ($id = 'mensagem') {
      $msg = Session::get($id);
      Session::destroy($id);
      if ($msg) {
        return '<div class="alert alert-' . $msg['tipo'] . ' alert-dismissable">
              ' . $msg['mensagem'] . '
            </div>';
      }
      return NULL;
    }

}

?>
