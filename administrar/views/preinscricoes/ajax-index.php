<?php

	// search for Cross-origin resource sharing (CORS)
	// This fixes common cross-domain errors
	header("Access-Control-Allow-Origin: *");
	
	if (count($_POST) > 0) { 

		require_once('../../config.php');
		require_once('../../funcoes.php');
		require_once ('../../core/ClassesLoader.php');
		ClassesLoader::Register();
		Session::init();

		$conexao = new Conexao();
		$curso = new Curso($conexao->getConexao());
		$preinscricao = new Preinscricao($conexao->getConexao());

		$modulo = $_POST['dados']['modulo'];
		$order = isset($_POST['dados']['order']) ? $_POST['dados']['order'] : NULL;
		$exibir = $quantidadePorPagina = (isset($_POST["dados"]["exibir"]) && !empty($_POST["dados"]["exibir"]))  ? (int) $_POST["dados"]["exibir"] : QUANTIDADE_POR_PAGINA;
		$pagina = isset($_POST["dados"]["pagina"]) ? $_POST["dados"]["pagina"] : 1;
		$pagina = $pagina <= 0 ? 1 : $pagina;
		$limit = $pagina == 1 ? $quantidadePorPagina : $quantidadePorPagina * ($pagina - 1);
		$offset = $pagina == 1 ? 0 : $quantidadePorPagina;

		$query = $curso->getQuery();
		//$query1 = clone $query;
		//$query2 = clone $query;

		if (!empty($_POST["dados"]["nome"])) {
			$query->where("nome like ?", "%" . codificaDado($_POST["dados"]["nome"]) . "%");
			//$query2->where("nome like ?", "%" . codificaDado($_POST["dados"]["nome"]) . "%");
		}
		
		if (!empty($_POST["dados"]["unidadeCertificadora"])) {
			$query->where("unidadeCertificadora = ?", Curso::getConstanteCertificadora($_POST["dados"]["unidadeCertificadora"]));
			//$query2->where("unidadeCertificadora = ?", Curso::getConstanteCertificadora($_POST["dados"]["unidadeCertificadora"]));
		}
		
		if (!empty($_POST["dados"]["tipo"])) {
			$query->where("tipo = ?", Curso::getConstanteTipo($_POST["dados"]["tipo"]));
			//$query2->where("tipo = ?", Curso::getConstanteTipo($_POST["dados"]["tipo"]));
		}

		$quantidade = $query->count();

		// recupera os cursos
		$cursos = $query->limitIn($limit, $offset)
			->order("nome", "asc")
			->all();

		$query = $preinscricao->getQuery();

		// adiciona em cada curso a quantidade de pré-inscrições
		foreach ($cursos as $key => $value) {
			
			$query->where("idCurso = ?", (int) $value["id"]);

			$q2 = clone $query;
			$q3 = clone $query;

			$cursos[$key]["infos"] = $cursos[$key]["labels"] = array();
			$quantidadeInscricoes = $query->count();

			if ($quantidadeInscricoes == 0) {
                $label = 'Nenhuma inscrição';
            }
            else if ($quantidadeInscricoes == 1) {
                $label = $quantidadeInscricoes . ' inscrição';
            }
            else {
                $label = $quantidadeInscricoes . ' inscrições';
            }

			$cursos[$key]["infos"] = array(
				$label
			);

			$q2->where("DATE(p.data) = ?", date('Y-m-d'));
			$q3->where("p.visualizada = 0");

			// labels
			if ($q2->count() > 0) {
				$l = array(
					'class' => 'warning',
					'label' => $q2->count() . ' Nova(s)',
					'text' => 'Inscrição realizada hoje'
				);
				$cursos[$key]["labels"][] = $l;
			}

			if ($q3->count() > 0) {
				$l = array(
					'class' => 'warning',
					'label' => $q3->count() . ' Não visualizada(s)',
					'text' => 'Inscrição ainda não foi visualizada'
				);
				$cursos[$key]["labels"][] = $l;
			}
		}
		
		$params["objetos"] = $cursos;
		
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
		
		include('list-index.phtml');
		 
		include('../includes/rows-listagem.phtml');
			
    	echo json_encode(array(
    			"quantidade" => $quantidade,
    			"inicio" => $inicio,
    			"fim" => $fim,
    			"modulo" => $modulo,
    			"result" => $rows
    		)
    	);
			
	}
?>