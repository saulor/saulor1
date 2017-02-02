<?php

	// search for Cross-origin resource sharing (CORS)
	// This fixes common cross-domain errors
	header("Access-Control-Allow-Origin: *");
	
	// buscas de logs
	
	if (count($_POST) > 0) { 
	
		require_once("../../config.php");
		require_once ('../../core/ClassesLoader.php');
		ClassesLoader::Register();
		Session::init();
		require_once '../../funcoes.php';
		$conexao = new Conexao();

		$modulo = $_POST['dados']['modulo'];
		$order = isset($_POST['dados']['order']) ? $_POST['dados']['order'] : NULL;
		$exibir = $quantidadePorPagina = (isset($_POST['dados']['exibir']) && !empty($_POST['dados']['exibir']))  ? (int) $_POST['dados']['exibir'] : QUANTIDADE_POR_PAGINA;
		$pagina = isset($_GET['p']) ? $_GET['p'] : 1;
		$pagina = $pagina <= 0 ? 1 : $pagina;
		$limit = $pagina == 1 ? $quantidadePorPagina : $quantidadePorPagina * ($pagina - 1);
		$offset = $pagina == 1 ? 0 : $quantidadePorPagina;

		list($dia, $mes, $ano) = empty($_POST['dados']['dataInicial']) ? explode("/", date('d/m/Y')) : explode("/", $_POST['dados']['dataInicial']);
		$timestampInicial = mktime(0, 0, 0, $mes, $dia, $ano);
		$dataInicial = $ano . "-" . $mes . "-" . $dia;
		$dataInicialHref = $dia . "/" . $mes . "/" . $ano;
		
		list($dia, $mes, $ano) = empty($_POST['dados']['dataFinal']) ? explode("/", date('d/m/Y')) : explode("/", $_POST['dados']['dataFinal']);
		$timestampFinal = mktime(0, 0, 0, $mes, $dia, $ano);
		$dataFinal = $ano . "-" . $mes . "-" . $dia;
		$dataFinalHref = $dia . "/" . $mes . "/" . $ano;

		$model = new Preinscricao($conexao->getConexao());

		//$query = $conexao->getConexao()->query()->from("vw_preinscricoes_v3");

		$query = $model->getQuery();

		if (!empty($_POST['dados']['responsavel'])) {
			$query->where("p.responsavel = ?", (int) $_POST['dados']['responsavel']);
			$imprimirExcelHref[] = 'responsavel=' . (int) $_POST['dados']['responsavel'];
		}

		if (!empty($_POST['dados']['nome'])) {
			$query->where("p.nome like ?", "%" . codificaDado($_POST['dados']['nome']) . "%");
			$imprimirExcelHref[] = 'nome=' . codificaDado($_POST['dados']['nome']);
		}

		if (!empty($_POST['dados']['status'])) {
			$query->where("p.status in (" . $_POST['dados']['status'] . ")");
			$imprimirExcelHref[] = 'status=' . $_POST['dados']['status'];
		}

		if (!empty($_POST['dados']['situacoes'])) {
			// filtra apenas os que tem data de retorno definida
			if ((int) $_POST['dados']['situacoes'] == -1) {
				$query->where('p.dataRetorno IS NOT NULL');
			}
			else {
				$query->where("p.tipoSituacao in (" . $_POST['dados']['situacoes'] . ")");
				$query->order("p.nome", "asc");
			}
			$imprimirExcelHref[] = 'situacao=' . $_POST['dados']['situacoes'];
		}

		if (!empty($_POST['dados']['curso'])) {
			$query->where("p.idCurso = ?", (int) $_POST['dados']['curso']);
			$imprimirExcelHref[] = 'idCurso=' . $_POST['dados']['curso'];
		}

		if (!empty($_POST['dados']['observacoes'])) {
			$query->where("p.observacoes like ?", "%" . codificaDado($_POST['dados']['observacoes']) . "%");
			$imprimirExcelHref[] = 'observacoes=' . codificaDado($_POST['dados']['observacoes']);
		}
		
		if (!empty($_POST['dados']['estado'])) {
			$query->where("p.siglaEstado = ?", $_POST['dados']['estado']);
			$imprimirExcelHref[] = 'siglaEstado=' . $_POST['dados']['estado'];
		}

		if (!empty($_POST['dados']['unidade'])) {
			$query->where("p.idCidade = ?", (int) $_POST['dados']['unidade']);
			$imprimirExcelHref[] = 'idCidade=' . $_POST['dados']['unidade'];
		}
		
		if ($timestampFinal > $timestampInicial) {
			$query->where("p.data BETWEEN ? and ?", $dataInicial, $dataFinal);
			$query->order("p.data", "asc");
			$imprimirExcelHref[] = 'dataInicial=' . $dataInicialHref;
			$imprimirExcelHref[] = 'dataFinal=' . $dataFinalHref;
		}

		// retornos com data definida
		if ($_POST['dados']['situacoes'] == -1) { 
			$query1 = clone $query;
			$query2 = clone $query;

			$query1->where('dataRetorno = ?', date('Y-m-d'));
			$query2
				->where('dataRetorno <> ?', date('Y-m-d'))
				->where('dataRetorno IS NOT NULL')
				->order('dataRetorno', 'asc');

			$quantidade = $limit = $quantidadePorPagina = $query1->count() + $query2->count();
			$objetos = array_merge($query1->limit($limit)->all(), $query2->limit($limit)->all());
		}
		else {
			$quantidade = $query->count();
			$query->limitIn($limit, $offset);
			if (empty($order)) {
				$query->order("p.data", "desc");
			}
			else {
				$query->order('p.' . $order, "asc");
			}
			$objetos = $query->all();
		}
				
		foreach ($objetos as $key => $obj) {

			$objetos[$key]['labels'] = array();

			if ($obj['idSituacao'] != 0) {
                
                $query = $conexao->getConexao()->query()
                	->from('vw_situacao')
                    ->where('inscricao = ?', (int) $obj['id'])
                    ->where('id = ?', (int) $obj['idSituacao']);

                // filtra apenas os que tem data de retorno definida
               	if (!empty($_POST['dados']['situacoes'])) {
					if ((int) $_POST['dados']['situacoes'] == -1) {
						$query->where('data IS NOT NULL');
					}
               	}

                $situacao = $query->first();
                $objetos[$key]['resumo'] = Situacao::getResumo($situacao);
                // se tiver uma data de retorno definida
                if ($situacao['data']) {
                    if (Funcoes::diferencaDatas(date('Y-m-d'), $situacao['data']) == 0) {
                        $label = 'hoje';
                    }
                    else {
                        $label = Funcoes::decodeDate($situacao['data']);
                    }
                    $l = array(
                        'objeto' => $situacao,
                        'class' => 'nota',
                        'label' => 'Retornar ' . $label,
                        'text' => 'Melhor horário  ' . $situacao['horario']
                    );
                    $objetos[$key]['labels'][] = $l;
                }
            }
		}

		$pages = new Paginator($quantidadePorPagina, 'p');
        $pages->setTotal($quantidade);
        $pageLinks = $pages->pageLinks('?modulo=preinscricoes&acao=filtro&' . implode('&', $imprimirExcelHref) . '&');
		
		$params['objetos'] = $objetos;
		
		if ($quantidade == 0) {
			$iniCount = $inicio = 0;
		}
		else {
			$iniCount = $inicio = ($quantidadePorPagina * $pagina) - ($quantidadePorPagina - 1);
		}
		
		if ($inicio + ($quantidadePorPagina - 1) > $quantidade) {
			$fim = $quantidade;
		}
		else {
			$fim = $inicio + ($quantidadePorPagina - 1);
		}

		$conexao->getConexao()->disconnect();

		ob_start();
		include DIR_ROOT . '/administrar/views/preinscricoes/rows-filtro.phtml';
		$rows = ob_get_contents();
        ob_end_clean();
		
		echo json_encode(array(
				"listExcelHref" => implode("&", $imprimirExcelHref),
				"quantidade" => $quantidade,
				"inicio" => $inicio,
				"fim" => $fim,
				"modulo" => $modulo,
				"result" => $rows,
				"paginacao" => $pageLinks
			)
		);
	}
	
?>