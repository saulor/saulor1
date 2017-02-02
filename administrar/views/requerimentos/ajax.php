<?php

	// search for Cross-origin resource sharing (CORS)
	// This fixes common cross-domain errors
	header("Access-Control-Allow-Origin: *");

	if (count($_POST) > 0) {
		require_once("../../config.php");
		$conexao = new Conexao();
		$modulo = $_POST['dados']['modulo'];
		$order = isset($_POST['dados']['order']) ? $_POST['dados']['order'] : NULL;
		$exibir = $quantidadePorPagina = (isset($_POST["dados"]["exibir"]) && !empty($_POST["dados"]["exibir"])) ?
			(int) $_POST["dados"]["exibir"] : QUANTIDADE_POR_PAGINA;
		$pagina = isset($_POST["dados"]["pagina"]) ? $_POST["dados"]["pagina"] : 1;
		$pagina = $pagina <= 0 ? 1 : $pagina;
		$limit = $pagina == 1 ? $quantidadePorPagina : $quantidadePorPagina * ($pagina - 1);
		$offset = $pagina == 1 ? 0 : $quantidadePorPagina;

		$query = $conexao->getConexao()->query()
			->from("requerimentos");

		if (!empty($_POST["dados"]["nome"])) {
			$query->where("nome like ?", "%" . codificaDado($_POST["dados"]["nome"]) . "%");
			$query->order("nome", "asc");
		}

		if (!empty($_POST["dados"]["curso"])) {
			$query->where("curso like ?", "%" . codificaDado($_POST["dados"]["curso"]) . "%");
			$query->order("curso", "asc");
		}

		if (!empty($_POST["dados"]["protocolo"])) {
			$query->where("protocolo like ?", "%" . $_POST["dados"]["protocolo"] . "%");
			$query->order("nome", "asc");
		}

		if (!empty($_POST["dados"]["email"])) {
			$query->where("email like ?", $_POST["dados"]["email"] . "%");
			$query->order("email", "asc");
		}

		if (!empty($_POST["dados"]["cidade1"])) {
			$query->where("cidade1 like ?", "%" . codificaDado($_POST["dados"]["cidade1"]) . "%");
			$query->order("cidade1", "asc");
		}

		if (!empty($_POST["dados"]["tipo"])) {
			$tipos = Requerimento::getConstanteTipo($_POST["dados"]["tipo"]);
			$query->where("tipo in (" . implode(",", $tipos) . ")");
			$query->order("nome", "asc");
		}

		if (!empty($_POST["dados"]["situacao"])) {
			$situacao = Requerimento::getConstanteSituacao($_POST["dados"]["situacao"]);
			$query->where("situacao = ?", (int) $situacao);
			$query->order("nome", "asc");
		}

		if (!empty($_POST["dados"]["procedimentosInternos"])) {
			$situacao = Requerimento::getConstanteSituacao($_POST["dados"]["situacao"]);
			$query->where("procedimentosInternos like ?", "%" . codificaDado($_POST["dados"]["procedimentosInternos"]) . "%");
			$query->order("nome", "asc");
		}

		if (!empty($_POST["dados"]["unidadeCertificadora"])) {
			$certificadora = Curso::getConstanteCertificadora($_POST["dados"]["unidadeCertificadora"]);
			$query->where("unidadeCertificadora = ?", (int) $certificadora);
			$query->order("nome", "asc");
		}

		if (preg_match("/^\d{2}\\/\d{2}\/\d{4}$/", $_POST["dados"]["data"])) {
			list($dia, $mes, $ano) = explode("/", $_POST["dados"]["data"]);
			$data = $ano . "-" . $mes . "-" . $dia;
			if ($data) {
				$query->where("DATE(data) = ?", $data);
			}
		}

		if (preg_match("/^\d{2}\\/\d{2}\/\d{4}$/", $_POST["dados"]["dataVencimento"])) {
			list($dia, $mes, $ano) = explode("/", $_POST["dados"]["dataVencimento"]);
			$data = $ano . "-" . $mes . "-" . $dia;
			if ($data) {
				$query->where("DATE(dataVencimento) = ?", $data);
			}
		}

		if (!empty($_POST["dados"]["tabs"])) {
			switch ($_POST["dados"]["tabs"]) {
				case '#tabs-1':
					$query->where("situacao = ?", (int) Requerimento::REQUERIMENTO_SITUACAO_RECEBIDO);
				break;
				case '#tabs-2':
					$query->where("situacao NOT IN (?,?,?)", (int) Requerimento::REQUERIMENTO_SITUACAO_RECEBIDO,
						(int) Requerimento::REQUERIMENTO_SITUACAO_FINALIZADO,
						(int) Requerimento::REQUERIMENTO_SITUACAO_INDEFERIDO);
				break;
				case '#tabs-3':
					$query->where("situacao = ?", (int) Requerimento::REQUERIMENTO_SITUACAO_FINALIZADO);
				break;
				case '#tabs-4':
					$query->where("situacao = ?", (int) Requerimento::REQUERIMENTO_SITUACAO_INDEFERIDO);
				break;
			}
			$query->order("nome", "asc");
		}

		$query->order("data", "desc");

		$quantidade = $query->count();

		$query->limitIn($limit, $offset);

		$params["objetos"] = $query->all();

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

		$acoesMassa = array();

		include('list.phtml');

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
