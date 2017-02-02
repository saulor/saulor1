<?php

function ehModuloAtivo($moduloIn, $permissoes) {
	if (array_key_exists($moduloIn, $permissoes)) {
		return true;
	}
	return false;
}

function checaPermissao($modulo, $permissao, $permissoes) {
	$permissoesArr = explode(",", $permissao);
	if (array_key_exists($modulo, $permissoes)) {
		$diff = array_diff($permissoesArr, $permissoes[$modulo]);
		if (count($diff) == 0) {
			return true;
		}
	}
	return false;
}

/* creates a compressed zip file */
function create_zip($files = array(), $destination = '', $overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) {
		return false;
	}
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
			$zip->addFile($file, basename($file));
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

		//close the zip -- done!
		$zip->close();

		//check to make sure the file exists
		return file_exists($destination);
	}
	else {
		return false;
	}
}

function getVariavel ($variavel) {
	return isset($_GET[$variavel]) ? (int) $_GET[$variavel] : 0;
}

function removeAcentos ($palavra) {

	$replaces = array(
		"á,Á,â,Â,à,À,ã,Ã" => "a",
		"é,É,ê,Ê,è,È" => "e",
		"í,Í,î,Î" => "i",
		"ó,Ó,ô,Ô,ò,Ò,õ,Õ" => "o",
		"ú,Ú,û,Û" => "u",
		"ç,Ç" => "c",
	);

	foreach ($replaces as $key => $value) {
		$replaceItens = explode(",", $key);
		foreach ($replaceItens as $r) {
			$palavra = str_replace($r, $value, $palavra);
		}
	}

	return $palavra;
}

function soLetras ($palavra) {

	$replaces = array(
		"á,Á,â,Â,à,À,ã,Ã" => "a",
		"é,É,ê,Ê,è,È" => "e",
		"í,Í,î,Î" => "i",
		"ó,Ó,ô,Ô,ò,Ò,õ,Õ" => "o",
		"ú,Ú,û,Û" => "u",
		"ç,Ç" => "c",
		"-,/,\,(,),*,@,#,$,%,&,_,+,=" => ""
	);

	foreach ($replaces as $key => $value) {
		$replaceItens = explode(",", $key);
		foreach ($replaceItens as $r) {
			$palavra = str_replace($r, $value, $palavra);
		}
	}

	return $palavra;
}

function trataDados ($dados = array()) {
	foreach ($dados as $key => $value) {
		if (!is_array($value)) {
			if (!strcmp($value, strip_tags($value)) == 0) {
				$dados[$key] = $value;
			}
			else if (is_numeric($value)) {
				$dados[$key] = (int) $value;
			}
			else {
				$dados[$key] = codificaDado($value);
			}
		}
	}
	return $dados;
}

function trataDados2 ($dados = array()) {
	foreach ($dados as $key => $value) {
		if (!is_array($value)) {
			if (!strcmp($value, strip_tags($value)) == 0) {
				$dados[$key] = $value;
			}
			else if (is_numeric($value)) {
				$dados[$key] = (int) $value;
			}
			else if (ehData($value)){
				$dados[$key] = converteData($value);
			}
			else if (ehDinheiro($value)){
				$dados[$key] = formataDecimal($value);
			}
			else {
				$dados[$key] = codificaDado($value);
			}
		}
	}
	return $dados;
}

/**
*	Função que verifica se uma chave ou conjunto de chaves existe em
*	um array.
*/
function existeNoArray($chaves = array(), $array) {
	$quantidadeChaves = count($chaves);
	$count = 0;
	foreach ($chaves as $chave) {
		if(in_array($chave, $array)) {
			$count++;
		}
	}
	if ($count == $quantidadeChaves)
		return true;
	return false;
}

function encurtaUrl($url) {

	$curl = curl_init('https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyDz1fdIXfq2_z4W9_YgCn5JW13hfrvVJLs');
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(
			array(
				'longUrl' => $url
			)
		)
	);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$json = curl_exec($curl);
	curl_close($curl);
	$elements = json_decode($json, true);
	return $elements["id"];
}

function inicializaDados ($obj) {
	$dados = array();
	$inspector = new Inspector($obj);

	$first = function($array, $key) {
		if (!empty($array[$key]) && sizeof($array[$key]) == 1) {
			return $array[$key][0];
		}
		return null;
	};

	foreach ($inspector->getClassProperties() as $property) {
		$meta = $inspector->getPropertyMeta($property);
		$type = $first($meta, "@type");
		if (!empty($type)) {
			$name = preg_replace("#^_#", "", $property);
			if ($type == "autonumber") {
				$dados[$name] = 0;
			}
			else {
				$dados[$name] = "";
			}
//			if ($type == "text" || $type == "longtext") {
//				$dados[$name] = "";
//			}
//			if ($type == "integer") {
//				$dados[$name] = "";
//			}
//			if ($type == "datetime") {
//				$dados[$name] = "";
//			}
//			if ($type == "date") {
//				$dados[$name] = "";
//			}
//			if ($type == "array") {
//				$dados[$name] = array();
//			}
//			if ($type == "decimal") {
//				$dados[$name] = 0;
//			}
//			if ($type == "date") {
//				$dados[$name] = "";
//				$dados['timestamp' . ucwords($name)] = 0;
//			}
		}
    }
	return $dados;
}

function existeUrl ($sitemap = NULL, $loc) {
	$sitemap = simplexml_load_file(dirname(DIR_ROOT) . DS . "sitemap.xml");
	if ($sitemap) {
		foreach ($sitemap as $key => $url) {
			if ($url->loc == $loc)
				return true;
		}
	}
	return false;
}

function getUrl ($loc) {
	$sitemap = simplexml_load_file(dirname(DIR_ROOT) . DS . "sitemap.xml");
	if ($sitemap) {
		foreach ($sitemap as $key => $url) {
			if ($url->loc == $loc)
				return $url;
		}
	}
	return array();
}

function excluiUrl ($loc) {

	$sitemapXML = simplexml_load_file(dirname(DIR_ROOT) . DS . "sitemap.xml");
	$xmlString = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
	$xmlString .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
	$xmlString .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
	$xmlString .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 ';
	$xmlString .='http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"></urlset>';
	$sitemapAUX = simplexml_load_string($xmlString);

	if ($sitemapXML) {

		foreach ($sitemapXML as $key => $url) {

			if ($url->loc != $loc) {

				$urlAux = $sitemapAUX->addChild("url");

				if (array_key_exists('loc', $url)) {
					$urlAux->addChild("loc", $url->loc);
				}

				if (array_key_exists('lastmod', $url)) {
					$urlAux->addChild("lastmod", $url->lastmod);
				}

				if (array_key_exists('changefreq', $url)) {
					$urlAux->addChild("changefreq", $url->changefreq);
				}

				if (array_key_exists('priority', $url)) {
					$urlAux->addChild("priority", $url->priority);
				}

				if (array_key_exists('expires', $url)) {
					$urlAux->addChild("expires", $url->expires);
				}
			}
		}

		$sitemapFile = fopen(dirname(DIR_ROOT) . DS . "sitemap.xml", "w");

		if ($sitemapFile) {
			fwrite($sitemapFile, $sitemapAUX->asXML());
			fclose($sitemapFile);
		}
	}

}

function adicionaUrl ($urlIn = array()) {

	$sitemapXML = simplexml_load_file(dirname(DIR_ROOT) . DS . "sitemap.xml");

	if ($sitemapXML) {

		if (!existeUrl($sitemapXML, $urlIn["loc"])) {

			$url = $sitemapXML->addChild("url");

			if (array_key_exists('loc', $urlIn)) {
				$url->addChild("loc", $urlIn["loc"]);
			}

			if (array_key_exists('lastmod', $urlIn)) {
				$url->addChild("lastmod", $urlIn["lastmod"]);
			}

			if (array_key_exists('changefreq', $urlIn)) {
				$url->addChild("changefreq", $urlIn["changefreq"]);
			}

			if (array_key_exists('priority', $urlIn)) {
				$url->addChild("priority", $urlIn["priority"]);
			}

			if (array_key_exists('expires', $urlIn)) {
				$url->addChild("expires", $urlIn["expires"]);
			}

			$sitemapFile = fopen(dirname(DIR_ROOT) . DS . "sitemap.xml", "w");

			if ($sitemapFile) {
				fwrite($sitemapFile, $sitemapXML->asXML());
				fclose($sitemapFile);
			}
		}
	}
}

function leef ($ficheiro) {
	$texto = file($ficheiro);
	$tamleef = sizeof($texto);
	$todo = '';
	for ($n=0; $n<$tamleef; $n++) {
		$todo .= $texto[$n];
	}
	return $todo;
}

/*
	Função que cria o link das notícias
*/
function getLink ($titulo) {

	// remove aspas simples e duplas
	$titulo = preg_replace('/(\“|\”|\"|\'|‘|’)/', '', $titulo);
	$titulo = preg_replace('/-/', ' ', $titulo);

	$replaces = array(
		"á,Á,â,Â,à,À,ã,Ã" => "a",
		"é,É,ê,Ê,è,È" => "e",
		"í,Í,î,Î" => "i",
		"ó,Ó,ô,Ô,ò,Ò,õ,Õ" => "o",
		"ú,Ú,û,Û" => "u",
		"ç,Ç" => "c",
		"(,),!,?,:,;,+,=,{,},#,@,º,ª,%" => ""
	);

	foreach ($replaces as $key => $value) {
		$replaceItens = explode(",", $key);
		foreach ($replaceItens as $r) {
			$titulo = str_replace($r, $value, $titulo);
		}
		$titulo = str_replace(",", "", $titulo);
	}

	$keywords = array();
	$notKeywords = array(
		"-",
		""
	);

	// array que vai armazenar todos itens do texto
	$itens = array();
	// array que armazena os itens que são dividos pelo espaço em branco
	$elementos = explode(" ", trim($titulo));

	foreach ($elementos as $elemento) {
		if (!in_array(strtolower($elemento), $notKeywords)) {
			if (strpos($elemento, "-") !== false) {
				$elemento = implode("-", explode("-", $elemento));
			}
			// se tiver barra, quebra novamente
			if (strpos($elemento, "/") !== false) {
				foreach (explode("/", $elemento) as $e) {
					$itens[] = decodificaDado(strtolower($e));
				}
			}
			else {
				$itens[] = decodificaDado(strtolower($elemento));
			}
		}
	}

	$link = implode("-", $itens);

	return $link;
}

function configuraKeywords ($texto) {

	// remove aspas simples e duplas
	$texto = preg_replace('/(,|:|\“|\”|\"|\')/', '', $texto);

	$keywords = array();
	// não entram na composição das hashtags
	$notKeywords = array("à","a","as","ao","aos","quem","da","das","dão","na","nas","o","os","do","dos","no","nos","À","e","de","em","para","um","uma","uns","umas","com","que","-","seu","sua","seus","suas","teu","tua","teus","tuas","é","pelo","pela","pelos","pelas","está","estão");
	$replaces = array(
		"(,)" => ""
	);

	// array que vai armazenar todos itens do texto
	$itens = array();
	// array que armazena os itens que são dividos pelo espaço em branco
	$elementos = explode(" ", $texto);

	foreach ($elementos as $elemento) {

		$elemento = trim($elemento);

		// se não for nenhum item definido no array $notKeywords
		if (!in_array(strtolower($elemento), $notKeywords)) {

			//if (strpos($elemento, "-") !== false) {
			//	$elemento = implode(", ", explode("-", $elemento));
			//}

			$palavra = "";
			// se tiver barra, quebra novamente
			if (strpos($elemento, "/") !== false) {
				foreach (explode("/", $elemento) as $elementoBarra) {
					$palavra = $elementoBarra;
				}
			}
			else {
				$palavra = $elemento;
			}

			// se for string
			if (!empty($palavra) && (int) $palavra == 0 && !in_array(strtolower($palavra), $itens))
				$itens[] = strtolower($palavra);
		}
	}

	$words = implode(", ", $itens);

	foreach ($replaces as $key => $value) {
		$replaceItens = explode(",", $key);
		foreach ($replaceItens as $r) {
			$words = str_replace($r, $value, $words);
		}
	}
	//return "";
	return $words;

}

function getHashtags ($texto) {

	$keywords = array();
	$notKeywords = array(
		",",
		"a",
		"À",
		"e",
		"o",
		"no",
		"na",
		"nos",
		"com",
		"nas",
		"de",
		"do",
		"dos",
		"da",
		"das",
		"em",
		"para",
		"-"
	);
	$replaces = array(
		"(,)" => ""
	);

	// array que vai armazenar todos itens do texto
	$itens = array();
	// array que armazena os itens que são dividos pelo espaço em branco
	$elementos = explode(" ", $texto);

	foreach ($elementos as $elemento) {
		if (!in_array(strtolower($elemento), $notKeywords)) {
			if (strpos($elemento, "-") !== false) {
				//$elemento = implode("", explode("-", $elemento));
				$elemento = implode(", ", explode("-", $elemento));
			}
			// se tiver barra, quebra novamente
			if (strpos($elemento, "/") !== false) {
				foreach (explode("/", $elemento) as $elementoBarra) {
					if ((int) $elementoBarra == 0)
						$itens[] = $elementoBarra;
				}
			}
			else {
				if ((int) $elemento == 0)
					$itens[] = $elemento;
			}
		}
	}

	$hashtags = implode(", ", $itens);

	foreach ($replaces as $key => $value) {
		$replaceItens = explode(",", $key);
		foreach ($replaceItens as $r) {
			$hashtags = str_replace($r, $value, $hashtags);
		}
	}

	return $hashtags;

}

function configuraHashtags($url, $titulo, $hasgtags) {
	$hashtagsResult = array();
	$hasgtags = explode(", ", $hasgtags);
	$tamTextoTwitter = 8 + strlen($url) + strlen(html_entity_decode($titulo));
	foreach ($hasgtags as $hashTag) {
		if ($tamTextoTwitter + strlen($hashTag) + 2 < 140) {
			$hashtagsResult[] = $hashTag;
			$tamTextoTwitter = $tamTextoTwitter + strlen($hashTag) + 2;
		}
	}
	return $hashtagsResult;
}

function getTitulo ($breadcrumbs) {
	$t = array();
	foreach ($breadcrumbs as $key => $value) {
		foreach ($value as $titulo => $link) {
			$t[] = $titulo;
		}
	}
	return implode(' &rsaquo; ', $t);
}

function converteDecimal($dado) {
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

function desconverteDecimal($dado) {
	if (empty($dado)) {
		return "0,00";
	}
	return number_format($dado, 2, ",", ".");
}

function getTimestamp() {
	return time();
}

function getData($timestamp) {
	if ($timestamp == 0) {
		return "";
	}
	return date('Y-m-d H:i:s', $timestamp);
}

function getDataFormatada($timestamp) {
	if ($timestamp == 0) {
		return "";
	}
	return date('d/m/Y', $timestamp);
}

function getHoraFormatada($timestamp) {
	return date('H:i', $timestamp);
}

function getDataHoraFormatada($timestamp) {
	return date('d/m/Y', $timestamp) . " às " . date("H:i:s", $timestamp);
}

function getMetaTags($pagina) {
	$xml = simplexml_load_file("metatags.xml");
	$elementos = $xml->children();
	$metaTags = array();
	foreach ($elementos as $elemento) {
		$paginaNomes = explode(",", str_replace(" ", "", $elemento->attributes()->nome));
		if (in_array($pagina, $paginaNomes)) {
			foreach ($elemento->children() as $key => $value) {
				$metaTags[$key] = trim($value);
			}
		}
	}
	return $metaTags;
}

/*
    Função codifica, escapa e remove espaços em branco desnecessários.
	Recebe como parâmetro uma string.
*/
function codificaDado ($dado) {
    return htmlentities(trim($dado), ENT_NOQUOTES, "utf-8");
}

/*
    Função decodifica e desescapa uma string.
	Recebe como parâmetro uma string.
*/
function decodificaDado ($dado) {
    return html_entity_decode(stripslashes($dado), ENT_NOQUOTES, "utf-8");
}

/*
    Função codifica, escapa e remove espaços em branco desnecessários.
	Recebe como parâmetros um array de strings.
*/
function codificaDados ($dados) {

    $keys = array('noticia', 'descricao', 'senha', 'id');

    foreach ($dados as $key => $value) {
        if (!in_array($key, $keys)) {
            $dados[$key] = codificaDado ($value);
        }
    }

    return $dados;

}

/*
    Função decodifica e desescapa dados.
	Recebe como parâmetros um array de strings.
*/
function decodificaDados ($dados) {
    //$keys = array('id', 'senha', 'noticia', 'descricao', 'data');
	$keys = array();
    foreach ($dados as $key => $value) {
		if (!in_array($key, $keys)) {
			$dados[$key] = decodificaDado ($value);
		}
    }

    return $dados;
}

function decodificaDados2 ($dados) {
    foreach ($dados as $key => $value) {
	 	if (ehDataMysql($value)) {
	 		$dados[$key] = desconverteData ($value);
	 	}
	 	else if (ehDataTimeMysql($value)) {
	 		$dados[$key] = desconverteDataTime ($value);
	 	}
	 	else if (ehDinheiroMysql($value)) {
	 		$dados[$key] = $value;
	 	}
	 	else {
			$dados[$key] = decodificaDado ($value);
		}
    }
    return $dados;
}

function temPermissao ($permissoes = array(), $permissoesSession) {
	foreach ($permissoes as $p) {
		if (!in_array($p, $permissoesSession))
			return false;
	}
	return true;
}

/*function verificaPermissao ($permissoes = array(), $permissoesSession) {
	foreach ($permissoes as $p) {
		if (!in_array($p, $permissoesSession)) {
			throw new Exception("Você não tem permissão para realizar esta ação");
		}
	}
}*/

/*
	Função seta uma mensagem que será exibida ao usuário
*/
function setMensagem ($tipo, $texto) {
    $mensagem = array("tipo" => $tipo, "texto" => $texto);
    $_SESSION["mensagemSe"][] = $mensagem;
}

/*
	Função exibe a mensagem setada
*/
function getMensagem ($classCss = NULL) {
    $rMensagem = '';
    if (count($_SESSION["mensagemSe"]) > 0) {
        foreach ($_SESSION["mensagemSe"] as $mensagem) {
            $rMensagem .= '<div id="mensagem" class="' . $mensagem["tipo"];
			if ($classCss != NULL) {
				$rMensagem .= ' ' . $classCss;
			}
			switch ($mensagem["tipo"]) {
				case "error" : $rMensagem .= " alert alert-danger"; break;
				case "info" : $rMensagem .= " alert alert-success"; break;

			}
			$rMensagem .= '">';
            $rMensagem .= '<p>' . $mensagem["texto"] . '</p>';
            $rMensagem .= '</div>';
            $rMensagem .= '<script>toMessage();</script>';
        }
    }
    $_SESSION["mensagemSe"] = array();
    return $rMensagem;
}

/*
	Função redireciona para uma URL.
*/
function redirect ($url){
    header("Location: ".$url);
}

/*function getMenu ($queryString) {

	$menu = '<ul>';
	$xml = simplexml_load_file("menu.xml");
	$queryString = parseQueryString($queryString);

	if (!array_key_exists("acao", $queryString)) {
		$queryString["acao"] = "index";
	}

	$elementos = $xml->children();

	foreach ($elementos as $elemento) {
		if ($elemento->attributes()->nome == $queryString["modulo"]) {
			foreach ($elemento->children() as $acao) {
				$acoes = explode(",", str_replace(" ", "", $acao->attributes()->nome));
				if (in_array($queryString["acao"], $acoes)) {
					foreach ($acao->children() as $item) {
						$menu .= '<li><a href="?modulo='.$queryString["modulo"];
						if (isset($item['acao']))
							$menu .= '&acao='.$item['acao'];
						if (isset($item['parametros'])) {
							$parametros = explode(",", str_replace(" ", "", $item['parametros']));
							foreach ($parametros as $parametro) {
								if (isset($_GET[$parametro]))
									$menu .= "&".$parametro."=".$queryString[$parametro];
							}
						}
						$menu .= '">'.$item.'</a></li>';
					}
				}
			}
		}
	}

    $menu .= "</ul>";

    return $menu;
}

function parseQueryString($queryString) {

	$arrayRetorno = array();

	$elementsArray = explode("&", $queryString);
	foreach ($elementsArray as $elementArray) {
		$itensArray = explode("=", $elementArray);
		$arrayRetorno[$itensArray[0]] = $itensArray[1];
	}

	return $arrayRetorno;

}*/


/*
	Função valida os dados submetidos
*/
function validaPost ($obrigatorios, $campos) {

    $mensagem = "";
    $camposVazios = array();

    // percorre todos os campos

    foreach($campos as $key => $value) {

        // verifica se campo existe no array de obrigatórios

        if(array_key_exists($key, $obrigatorios)) {

            // se existir verifica se está vazio

            switch ($obrigatorios[$key]["tipo"]) {

            	case "array" :
            		if(count($value) > 0) {
            			if (is_string($value[0]) && trim($value[0]) == '') {
            				$camposVazios[] = $obrigatorios[$key]["nome"];
        				}
            		}
            	break;

            	case "integerNotZero" :
            		if((int) $value == 0) {
            		    $camposVazios[] = $obrigatorios[$key]["nome"];
        		   	}
            	break;

            	case "input" :
	            	if($value == "") {
	            	    $camposVazios[] = $obrigatorios[$key]["nome"];
            	   	}
            	break;

            	case "decimal" :
            		//if($value == "0,00")
            		if((double) $value == 0) {
            		    $camposVazios[] = $obrigatorios[$key]["nome"];
        		   	}
            	break;

            	case "radio" :
            		if(empty($value) || (int) $value == 0) {
            			$camposVazios[] = $obrigatorios[$key]["nome"];
            		}
            	break;

            	case "select" :
            		if($value == "") {
            		    $camposVazios[] = $obrigatorios[$key]["nome"];
        		   	}
            	break;

            	case "textarea" :
            		if(trim(strlen($value)) == 0) {
            		    $camposVazios[] = $obrigatorios[$key]["nome"];
        		   	}
            	break;

            	case "editor1" :
            		if(trim(strlen($value)) == 0) {
            		    $camposVazios[] = $obrigatorios[$key]["nome"];
        		   	}
            	break;

            	case "editor" :
            		if(strlen(strip_tags(trim($value))) == 0) {
            		    $camposVazios[] = $obrigatorios[$key]["nome"];
        		   	}
            	break;

            	case "file" :
            		if(!enviouArquivo($value)) {
            		    $camposVazios[] = $obrigatorios[$key]["nome"];
        		   	}
            	break;

            }

        }

    }

    if (count($camposVazios) > 0) {
        $mensagem = "O(s) campo(s) ";
        $mensagem .= implode(", ", $camposVazios);
        $mensagem .= " deve(m) ser preenchidos.";
    }

    return $mensagem;

}

/*
	Função valida os dados submetidos
*/
function validaPost2 ($obrigatorios, $campos) {

    $mensagem = "";
    $camposVazios = array();

    // percorre todos os campos
    foreach($campos as $key => $value) {

        // verifica se campo existe no array de obrigatórios
        if(array_key_exists($key, $obrigatorios)) {

            // se existir verifica se está vazio

            switch ($obrigatorios[$key]["tipo"]) {

            	case "array" :
            		if(count($value) > 0) {
            			if (is_string($value[0]) && trim($value[0]) == '')
            				$camposVazios[] = $key;
            		}
            	break;

            	case "integerNotZero" :
            		if((int) $value == "0") {
            		    $camposVazios[] = $key;
        		   	}
            	break;

            	case "input" :
            	case "select" :
            		if($value == "") {
            		    $camposVazios[] = $key;
        		   	}
            	break;

            	case "decimal" :
            		if($value == "0,00") {
            		    $camposVazios[] = $key;
        		   	}
            	break;

            	case "radio" :
            		if($value == 0) {
            			$camposVazios[] = $key;
            		}
            	break;

            	case "textarea" :
            	case "editor1" :
            		if(strlen(trim($value)) == 0) {
            		    $camposVazios[] = $key;
        		   	}
            	break;

            	case "editor" :
            		if(strlen(strip_tags(trim($value))) == 0) {
            		    $camposVazios[] = $key;
        		  	}
            	break;

            	case "file" :
            		if(!enviouArquivo($value)) {
            		    $camposVazios[] = $key;
            		}
            	break;

            }

        }

    }

    return $camposVazios;

}

/*
	Função valida e-mails.
*/
function validaEmail ($email) {
	//$pattern = '/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/';
	$pattern = '/^[_A-Za-z0-9-\\+]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$/';
    return preg_match($pattern, $email);
}

function ehDataMysql ($valor) {
	$pattern = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/';
	return preg_match($pattern, $valor);
}

function ehDataTimeMysql ($valor) {
	$pattern = '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/';
	return preg_match($pattern, $valor);
}

function ehData ($valor) {
	$pattern = '/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/';
	return preg_match($pattern, $valor);
}

function ehDinheiro ($valor) {
	if (is_numeric($valor)) {
		return true;
	}
	else if (strpos($valor, ",")) {
		list($parte1, $parte2) = explode(",", $valor);
		if (is_numeric($parte1) && is_numeric($parte2)) {
			return true;
		}
	}
	return false;
}

function ehDinheiroMysql ($valor) {
	if (is_numeric($valor)) {
		return true;
	}
	else if (strpos($valor, ".")) {
		list($parte1, $parte2) = explode(".", $valor);
		if (is_numeric($parte1) && is_numeric($parte2)) {
			return true;
		}
	}
	return false;
}

function is_utf8($str) {
    return (bool) preg_match('//u', $str);
}

/*
	Função compacta uma palavra com mais de 40 caracteres
*/
function compactaTexto ($word, $tamanho = 48) {
	$wordAux = $word;
	$tamanhoAux = $tamanho;
    if (strlen($word) > $tamanho) {
        do {
           $word = substr($wordAux, 0, (($tamanhoAux++)-3)) . '...';
        } while (!is_utf8($word));
   	}
    return $word;
}

/*
	Funcões do módulo de galerias
*/

function existeArquivo ($diretorio) {
	if (is_file($diretorio))
		return true;
	return false;
}

function existeDiretorio ($diretorio) {
	if (is_dir($diretorio))
		return true;
	return false;
}

function getExtensao ($arquivo) {
    return pathinfo($arquivo["name"], PATHINFO_EXTENSION);
}

/*
function getExtensaoImagem($diretorio){
	$dot = strrpos($diretorio, '.') + 1;
	return substr($diretorio, $dot);
}

function verificaImagemPorExtensao($imagem, $extensoes){
	$uploadext = explode('|',$extensoes);
	$dot = strrpos($imagem, '.') + 1;
	if(in_array(substr($imagem, $dot),$uploadext)){
		return true;
	}
	else{
		return false;
	}
}
*/

function enviouArquivo ($imagem) {
    if($imagem['error'] == 4)
        return false;
    else
        return true;
}

function criaDiretorio ($diretorio) {
    if (is_dir($diretorio)) {
        return true;
   	}
    if (@mkdir($diretorio,0777)) {
        return true;
   	}
    return false;
}

function verificaTipo ($arquivo, $permitidos) {
    $permitidos = explode("|", $permitidos);
    if (in_array($arquivo["type"], $permitidos)) {
        return true;
   	}
    return false;
}

function salvaArquivo ($arquivo, $diretorio) {
    if(@move_uploaded_file($arquivo['tmp_name'],$diretorio.'/'.$arquivo['name'])) {
        return true;
   	}
    return false;
}

function redimensionaImagem ($imagem, $diretorio, $largura = 200) {
    $imagemDir = $diretorio . DS . $imagem['name'];
    $img = imagecreatefromjpeg($imagemDir);
    $x = imagesx($img);
    $y = imagesy($img);
    $autura = ($largura * $y) / $x;
    $nova = imagecreatetruecolor($largura, $autura);
    imagecopyresampled($nova, $img, 0, 0, 0, 0, $largura, $autura, $x, $y);
    imagejpeg($nova, $diretorio. DS . 'mini-'.$imagem["name"]);
    imagedestroy($img);
    imagedestroy($nova);
    return true;
}

function cleanPath ($word){
    return str_replace('amp;', '', $word);
}

function excluiArquivo ($diretorio){
    if (is_file($diretorio)) {
        if(@unlink($diretorio)) {
			return true;
		}
		return false;
    }
}

function copiaArquivo ($source, $diretorio){

	if (!existeDiretorio(dirname($diretorio))) {
		criaDiretorio(dirname($diretorio));
	}

    if (copy($source, $diretorio)) {
		return true;
    }

    return false;
}

function excluiDiretorio ($diretorio){
    $diretorio = cleanPath($diretorio);
    if(is_dir($diretorio)){
        excluiConteudo($diretorio);
        @rmdir($diretorio);
    }
}

function excluiConteudo ($diretorio){
    if(!estaVazia($diretorio)){
        if($dir = opendir($diretorio)){
            while(false !== $arq = readdir($dir)) {
                if ($arq != '.' && $arq != '..') {
                    if( is_dir($diretorio.'/'.$arq) ) {
                        if(estaVazia($diretorio.'/'.$arq)) {
                            @rmdir($diretorio.'/'.$arq);
                        }
                        else {
                            excluiConteudo($diretorio.'/'.$arq);
                            rmdir($diretorio.'/'.$arq);
                        }
                    }
                    else{
                        unlink($diretorio.'/'.$arq);
                    }

                }
            }
            closedir($dir);
        }
    }
}

function estaVazia ($pasta){
    if(file_exists($pasta.'/Thumbs.db'))
        @unlink($pasta.'/Thumbs.db');
    $files = scandir($pasta);

    if(count($files) > 2)
        return false;
    else
        return true;
}

/*
    Retorna todas as imagens da galeria.
*/
function getArquivos ($diretorio){

    $extensions = 'jpg|JPG|png|PNG|gif|GIF|bmp|BMP|jpeg|JPEG|ico|ICO';
    $files = array();

    if(is_dir($diretorio)){
        if ($dir = opendir($diretorio)) {
            while(false !== ($arq = readdir($dir))) {
                if ((!is_dir($diretorio.'/'.$arq) ) && (preg_match("/$extensions/",$arq))) {
                    $files[] = $arq;
                }
            }
        }
    }

    return $files;
}

/*
    Retorna as imagens da galeria, exceto as imagens mini.
*/
function getImagens($diretorio){

    $extensions = 'jpg|JPG|png|PNG|gif|GIF|bmp|BMP|jpeg|JPEG|ico|ICO';
    $files = array();

    if(is_dir($diretorio)){
        if ($dir = opendir($diretorio)) {
            while(false !== ($arq = readdir($dir))) {
                //if (strpos($arq, 'mini') === false) {
                    if ((!is_dir($diretorio . DS . $arq) ) && (preg_match("/$extensions/",$arq))) {
                        $files[] = $arq;
                    }
                //}
            }
        }
    }
    return $files;
}

function imageResize($width, $height, $target){
    if ($width > $height) {
        $percentage = ($target / $width);
    }
    else {
        $percentage = ($target / $height);
    }
    $width = round($width * $percentage);
    $height = round($height * $percentage);
    return array($width, $height);
}

function renomeiaImagem ($old, $new){
	rename($old,$new);
}

function getQuantidadeImagens ($diretorio) {
	return count(glob($diretorio . "*.[jJ][pP][gG]", GLOB_NOSORT));
}

function paginacao ($quantidade, $quantidadePorPagina, $pagina, $queryString) {

  if ($quantidade == 0) {
    $numeroPaginas = 0;
 	}
  else {
    $numeroPaginas = ceil($quantidade/$quantidadePorPagina);
	}

	$numeroItens = 10;

	$pag = '<nav class="paginacao"><ol class="pagination">';

	//seta que leva para a primeira página
	if ($pagina > 1 && ($numeroPaginas > $numeroItens)) {
        $pag .= '<li title="Ir para a primeira página">';
        $pag .= '<a href="?';
        $pag .= $queryString;
        $pag .= '&p=1';
        $pag .= '">«</a></li>';
	}

	// seta que retrocede entre páginas
	if($pagina > 1) {
        $pag .= '<li title="Ir para a página anterior">';
        $pag .= '<a href="?';
        $pag .= $queryString;
        $pag .= '&p=' . ($pagina-1);
        $pag .= '">‹</a></li>';
	}

	//$pag .= $pagina;
	if ($numeroPaginas > $numeroItens) {
        $subtrai = ($numeroItens - ($numeroPaginas - $pagina + 1));
        if ($subtrai < 0)
        $subtrai = 0;
        $inicial = $pagina - $subtrai;
        $final = $inicial + $numeroItens - 1;
	}
	else {
        $inicial = 1;
        $final = $numeroPaginas;
	}

	for($i=$inicial;$i<=$final;$i++){
        $pag .= '<li';
        if ($pagina == $i) {
            $pag .= ' class="current">';
            $pag .= '<a href="#">' . $i . '</a>';
        }
        else {
            $pag .= '><a href="?';
            $pag .= $queryString;
            $pag .= '&p=' . $i;
            $pag .= '">';
            $pag .= $i;
            $pag .= '</a>';
        }
        $pag .= '</li>';
	}

	// seta que avança entre páginas
	if($pagina < $numeroPaginas) {
        $pag .= '<li title="Ir para a próxima página">';
        $pag .= '<a href="?';
        $pag .= $queryString;
        $pag .= '&p=' . ($pagina+1);
        $pag .= '">›</a></li>';
	}

	//seta que leva para a última página
	if ($pagina <= ($numeroPaginas - $numeroItens)) {
        $pag .= '<li title="Ir para última página">';
        $pag .= '<a href="?';
        $pag .= $queryString;
        $pag .= '&p=' . $numeroPaginas;
        $pag .= '">»</a></li>';
	}

	$pag .= '</ol></nav>';

	return $pag;

}

function paginacaoSite ($quantidade, $quantidadePorPagina, $pagina, $queryString) {

    if ($quantidade == 0) {
        $numeroPaginas = 0;
   	}
    else {
        $numeroPaginas = ceil($quantidade/$quantidadePorPagina);
   	}

	$numeroItens = 10;

	$pag = '<ul>';

	if ($quantidade > $quantidadePorPagina) {

		//seta que leva para a primeira página
		if ($pagina > 1 && ($numeroPaginas > $numeroItens)) {
	        $pag .= '<li title="Ir para a primeira página">';
	        $pag .= '<a href="?';
	        $pag .= $queryString;
	        $pag .= '&p=1';
	        $pag .= '">«</a></li>';
		}

		// seta que retrocede entre páginas
		if($pagina > 1) {
	        $pag .= '<li title="Ir para a página anterior">';
	        $pag .= '<a href="?';
	        $pag .= $queryString;
	        $pag .= '&p='.($pagina-1);
	        $pag .= '">‹</a></li>';
		}

		//$pag .= $pagina;
		if ($numeroPaginas > $numeroItens) {
	        $subtrai = ($numeroItens - ($numeroPaginas - $pagina + 1));
	        if ($subtrai < 0)
	        $subtrai = 0;
	        $inicial = $pagina - $subtrai;
	        $final = $inicial + $numeroItens - 1;
		}
		else {
	        $inicial = 1;
	        $final = $numeroPaginas;
		}

		for($i=$inicial;$i<=$final;$i++){
	        $pag .= '<li';
	        if ($pagina == $i) {
	            $pag .= ' class="current">';
	            $pag .= '<a href="#" onclick="return false;">'.$i.'</a>';
	        }
	        else {
	            $pag .= '><a onclick="paginacao(\'noticias\', '.$i.', this);return false;" href="';
	            $pag .= $queryString;
	            $pag .= '/'.$i;
	            $pag .= '">';
	            $pag .= $i;
	            $pag .= '</a>';
	        }
	        $pag .= '</li>';
		}

		// seta que avança entre páginas
		if($pagina < $numeroPaginas) {
	        $pag .= '<li title="Ir para a próxima página">';
	        $pag .= '<a href="?';
	        $pag .= $queryString;
	        $pag .= '&p='.($pagina+1);
	        $pag .= '">›</a></li>';
		}

		//seta que leva para a última página
		if ($pagina <= ($numeroPaginas - $numeroItens)) {
	        $pag .= '<li title="Ir para última página">';
	        $pag .= '<a href="?';
	        $pag .= $queryString;
	        $pag .= '&p='.$numeroPaginas;
	        $pag .= '">»</a></li>';
		}

	}

	$pag .= '</ul>';

	return $pag;

}

function base64url_encode($data) {
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function montaQueryString($parameters = array(), $paramsExclude = array()) {
	$resultQsArray = array();
	foreach ($parameters as $key => $value) {
		if (!empty($value) and !in_array($key, $paramsExclude)) {
			$resultQsArray[] = $key . '=' . $value;
		}
	}
	$resultQs = "?" . implode("&", $resultQsArray);
	return $resultQs;
}

/*function montaRedirect($qs, $includeItens = array()) {
	$url = '';
	$itensQS = explode("&", $qs);
	$resultQsArray = array();
	foreach ($itensQS as $itemQS) {
		// quebra o parâmetro pelo sinal =
		$item = explode("=", $itemQS);
		list($chave, $valor) = $item;
		// verifica se esse parâmetro é um dos que devem entrar na composição da query string resultante
		if (in_array($chave, $includeItens)) {
			// se for armazena  no array
			$resultQsArray[] = $chave . '=' . $valor;
		}
	}
	return "?" . implode("&", $resultQsArray);
	return urlencode($resultQs);
}

function montaRedirect2($qs, $naoIncluiItens = array(), $mudaValorItens = array()) {
	$itensQS = explode("&", $qs);
	$resultQsArray = array();
	foreach ($itensQS as $itemQS) {
		// quebra o parâmetro pelo sinal =
		$item = explode("=", $itemQS);
		list($chave, $valor) = $item;
		// verifica se esse parâmetro não deve ser incluído
		// ou se deve ser alterado
		if (!in_array($chave, $naoIncluiItens) && !array_key_exists($chave, $mudaValorItens)) {
			$resultQsArray[] = $chave . '=' . $valor;
		}
	}
	foreach ($mudaValorItens as $key => $value) {
		if (!empty($value)) {
			$resultQsArray[] = $key . '=' . $value;
		}
	}
	return "?" . implode("&", $resultQsArray);
	//return urlencode($resultQs);
}*/

function paginacao2 ($quantidade, $quantidadePorPagina, $pagina, $queryString) {

	$numeroItens = 5;
	$numeroPaginas = ceil($quantidade / $quantidadePorPagina);
	$inicio = 1;
	$fim = $numeroPaginas;

	if ($numeroPaginas > $numeroItens) {
	    $subtrai = ($numeroItens - ($numeroPaginas - $pagina + 1));
	    if ($subtrai < 0) {
	    	$subtrai = 0;
	    }
	    $inicio = $pagina - $subtrai - 1;
	    if ($inicio == 0) {
	    	$inicio = 1;
	    }
	    $fim = $inicio + $numeroItens;
	}

	$paginacao = '<ol class="pagination">';

	//seta que leva para a primeira página
	if ($pagina > 1 && ($numeroPaginas > $numeroItens)) {
	    $paginacao .= '<li title="Ir para a primeira página">';
	    $paginacao .= '<a href="';
	    $paginacao .= $queryString;
	    $paginacao .= '&pagina=1';
	    $paginacao .= '">«</a></li>';
	}

	// seta que retrocede entre páginas
	if($pagina > 1) {
	    $paginacao .= '<li title="Ir para a página anterior">';
	    $paginacao .= '<a href="';
	    $paginacao .= $queryString;
	    $paginacao .= '&pagina=' . ($pagina-1);
	    $paginacao .= '">‹</a></li>';
	}

	for($i=$inicio; $i<=$fim; $i++){
	    $paginacao .= '<li';
	    if ($pagina == $i) {
	        $paginacao .= ' class="current">';
	        $paginacao .= '<a href="#">' . $i . '</a>';
	    }
	    else {
	        $paginacao .= '><a href="';
	        $paginacao .= $queryString;
	        $paginacao .= '&pagina=' . $i;
	        $paginacao .= '">';
	        $paginacao .= $i;
	        $paginacao .= '</a>';
	    }
	    $paginacao .= '</li>';
	}

	if ($pagina <= ($numeroPaginas - $numeroItens)) {
	    $paginacao .= '<li title="Ir para última página">';
	    $paginacao .= '<a href="';
	    $paginacao .= $queryString;
	    $paginacao .= '&pagina=' . $numeroPaginas;
	    $paginacao .= '">»</a></li>';
	}



	$paginacao .= '</ol>';

	return $paginacao;

}

/*importante*/
function retiraDoArray($chaves = array(), $array) {
	$result = array();
	foreach ($array as $key => $value) {
		if(!in_array($key, $chaves)) {
			$result[$key] = $value;
		}
	}
	return $result;
}

function montaRedirect($qs, $naoIncluiItens = array(), $mudaValorItens = array()) {
	$itensQS = explode("&", $qs);
	$resultQsArray = array();
	foreach ($itensQS as $itemQS) {
		// quebra o parâmetro pelo sinal =
		$item = explode("=", $itemQS);
		list($chave, $valor) = $item;
		// verifica se esse parâmetro não deve ser incluído
		// ou se deve ser alterado
		if (!in_array($chave, $naoIncluiItens) && !array_key_exists($chave, $mudaValorItens)) {
			$resultQsArray[] = $chave . '=' . $valor;
		}
	}
	foreach ($mudaValorItens as $key => $value) {
		if (!empty($value)) {
			$resultQsArray[] = $key . '=' . $value;
		}
	}
	return "?" . implode("&", $resultQsArray);
	//return urlencode($resultQs);
}

//// Painel 2.0

function is_html($text) {
	return (bool) preg_match("/<\/?\w+((\s+\w+(\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)\/?>/", $text);
}

function converteData ($data) {
	if (preg_match('/\d{4}-\d{2}-\d{2}/', $data)) {
		return $data;
	}
	else if (empty($data)) {
		return NULL;
	}
	else {
		list($dia, $mes, $ano) = explode("/", $data);
		return $ano . "-" . $mes . "-" . $dia;
	}
}

function desconverteData ($data) {
	if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $data)) {
		return $data;
	}
	else if (empty($data) || $data == "0000-00-00") {
		return "";
	}
	else {
		list($ano, $mes, $dia) = explode("-", $data);
		return $dia . "/" . $mes . "/" . $ano;
	}
}

function converteDataTime ($data) {
	if (preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $data)) {
		return $data;
	}
	else if (empty($data)) {
		return NULL;
	}
	else {
		list($parte1, $parte2) = explode(" ", $data);
		list($dia, $mes, $ano) = explode("/", $parte1);
		list($hora, $min, $seg) = explode(":", $parte2);
		return $ano . "-" . $mes . "-" . $dia . " " . $hora . ":" . $min . ":" . $seg;
	}
}

function desconverteDataTime ($data) {
	if (preg_match('/\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}/', $data)) {
		return $data;
	}
	else if (empty($data)) {
		return "00/00/0000 00:00:00";
	}
	else {
		list($parte1, $parte2) = explode(" ", $data);
		list($ano, $mes, $dia) = explode("-", $parte1);
		list($hora, $min, $seg) = explode(":", $parte2);
		return $dia . "/" . $mes . "/" . $ano . " " . $hora . ":" . $min . ":" . $seg;
	}
}

function timestampToData($timestamp) {
	return (int) $timestamp > 0 ? date('d/m/Y', $timestamp) : "";
}

function timestampToDataTime($timestamp) {
	return (int) $timestamp > 0 ? date('d/m/Y H:i:s', $timestamp) : "";
}

function dataToTimestamp($data) {
	if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $data)) {
		$data = implode("-", explode("/", $data));
		return strtotime($data);
	}
}

?>
