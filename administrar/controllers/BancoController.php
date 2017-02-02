<?php

class BancoController extends Controller {

	private $modulo = 'banco';
	
	public function BancoController() {
		try {
			parent::__construct();
		}
		catch (Exception $e) {
			$this->conexao->getConexao()->disconnect();
			setMensagem("error", $e->getMessage());
			Application::redirect("index.php");
			exit;
		}
	}

	public function indexAction() {
		try {
			$this->checaPermissao($this->modulo, 'index');
			$conexao = $this->conexao->getConexao();
			$model = new Informacao($conexao);
			$breadcrumbs = array();
			$breadcrumbs[] = array('Banco de Dados' => '');
			$quantidadePorPagina = isset($_GET['exibir']) ? (int) $_GET['exibir'] : QUANTIDADE_POR_PAGINA;
			$quantidadePorPagina = ($quantidadePorPagina <= 0) ? QUANTIDADE_POR_PAGINA : $quantidadePorPagina;
			$pagina = (isset($_GET['p'])) ? $_GET['p'] : 1;
			$pagina = ($pagina <= 0) ? 1 : $pagina;
			$limit = ($pagina == 1) ? $quantidadePorPagina : $quantidadePorPagina * ($pagina - 1);
			$offset = ($pagina == 1) ? 0 : $quantidadePorPagina;
			$order = array(
				'data' => 'desc'
			);
			if (isset($_GET['order'])) {
				$order = array(
					$_GET['order'] => 'asc'
				);
			}

			$quantidade = 0;
			$objetos = array();
			$pageLinks = NULL;

			$quantidade = $model->count();
			$objetos = $model->getObjetos(array(
					'limit' => $limit, 
					'offset' => $offset,
					'order' => $order
				)
			); 

			$pageQs[] = 'modulo=' . $this->modulo;
			if (isset($_GET['exibir'])) {
				$pageQs[] = 'exibir=' . $_GET['exibir'];
			}
			if (isset($_GET['order'])) {
				$pageQs[] = 'order=' . $_GET['order'];
			}
			$pages = new Paginator($quantidadePorPagina, 'p');
        	$pages->setTotal($quantidade);
        	$pageLinks = $pages->pageLinks('?' . implode('&', $pageQs) . '&');
			
			if (count($objetos) == 0 && $pagina > 1) {
				$redirecionar = montaRedirect($_SERVER['QUERY_STRING'], array('p'));
				Application::redirect($redirecionar . '&p=' . ($pagina-1));
			}
			
		}
		catch (PermissaoException $e) {
			$this->conexao->getConexao()->disconnect();
			setMensagem('error', $e->getMessage());
			Application::redirect('index.php');
			exit;
		}
		catch (Exception $e) {
			setMensagem('error', $e->getMessage());
		}

		$conexao->disconnect();
		$view = new View2($this->modulo, 'extendido', 'index.phtml');
		$view->setParams(array(
				'title' => getTitulo($breadcrumbs),
				'objetos' => $objetos,
				'quantidade' => $quantidade,
				'quantidadePorPagina' => $quantidadePorPagina,
				'pagina' => $pagina,
				'breadcrumbs' => $breadcrumbs,
				'paginacao' => $pageLinks,
				'columns' => $model->getColumns()
			)
		);
        $view->showContents();
	}

	public function cadastrarAction() {
		try {
			$this->checaPermissao($this->modulo, 'cadastrar');
			$conexao = $this->conexao->getConexao();
			
			$informacaoModel = new Informacao($conexao);
			$cursoModel = new Curso($conexao);
			$cursos = $cursoModel->getObjetos(array(
					'order' => array('nome' => 'asc')
				)
			);

			if (isset($_GET["id"])) {
				$model = $informacaoModel->getObjetoOrFail(getVariavel('id'));
				$acao = "editar";
				$breadcrumbs[] = array(
					"Banco de Dados" => "?modulo=" . $this->modulo,
					"Editar" => "" 
				);
			}
			else {
				$model = $informacaoModel;
				$acao = "cadastrar";
				$breadcrumbs[] = array(
					"Banco de Dados" => "?modulo=" . $this->modulo,
					"Nova" => "" 
				);
			}
			
			if (count($_POST) > 0) {

				$dados = $_POST;
				$model->setDados($dados);
				
				$obrigatorios = array(
					"nome" => array(
						"nome" => "Nome"
					)
				);
				
				Funcoes::validaPost($obrigatorios, $dados);
				$model->salvar();

				if ($acao == 'cadastrar') {	
					$acao = 'cadastrado';
					$this->log->adicionar ('cadastrou', 'informação', $model->nome, 
						'Usuário cadastrou informação no Banco de Dados.');					
					$redirecionar = sprintf('?modulo=%s&acao=%s&id=%d', $this->modulo, 
						'cadastrar', $model->id);
				}
				else {
					$acao = 'atualizado';
					$this->log->adicionar ('atualizou', 'informação', $model->nome, 
						'Usuário atualizou informação no Banco de Dados.');
					$redirecionar = sprintf('?modulo=%s', $this->modulo);
				}

				setMensagem('info', sprintf('Registro %s [%s]', $acao, $model->nome));
				
				$conexao->commit();
				$conexao->disconnect();	
				Application::redirect($redirecionar);
				exit;
			}
		}
		catch (PermissaoException $e) {
			$this->conexao->getConexao()->disconnect();
			setMensagem("error", $e->getMessage());
			Application::redirect("index.php");
			exit;
		}
		catch (ModelNotFoundException $e) {
			$conexao->disconnect();
			setMensagem("error", 'Registro não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
	
		$conexao->disconnect();					
		$view = new View2($this->modulo, "extendido", "novo.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"objeto" => $model,
				'cursos' => $cursos
			)
		);
        $view->showContents();
	}
	
	public function excluirAction() {
		try {
			$this->checaPermissao($this->modulo, 'excluir');
			$conexao = $this->conexao->getConexao();
			$model = new Informacao($conexao);
			$ligar = $model->getObjetoOrFail(getVariavel('id'));
			$affectedRows = $ligar->excluir();
			if ($affectedRows) {
				$this->log->adicionar ('excluiu', 'informação', $model->nome, 
					'Usuário excluiu informação do Banco de Dados.');
				$conexao->commit();					
				setMensagem('info', 'Registro excluído [' . $model->nome . ']');
			}
		}
		catch (PermissaoException $e) {
			$this->conexao->getConexao()->disconnect();
			setMensagem('error', $e->getMessage());
			Application::redirect('index.php');
			exit;
		}
		catch (ModelNotFoundException $e) {
			$conexao->disconnect();
			setMensagem("error", 'Registro não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem('error', $e->getMessage());
		}
		$conexao->disconnect();
		Application::redirect($_SERVER['HTTP_REFERER']);
		exit;
	}
	
	public function acoesAction() {
		try {
			$this->checaPermissao($this->modulo, "opcoes");
			$conexao = $this->conexao->getConexao();
			$processados = 0;
			$naoProcessados = 0;
			
			if (empty($_POST["acoes"])) {
				throw new Exception("É necessário escolher uma ação");
			}
			
			$model = new Informacao($conexao);
			$objetos = isset($_POST["objetos"]) ? $_POST["objetos"] : array();
			
			// retira o elemento -1, caso exista
			if (count($objetos) > 0 && $objetos[0] == -1) {
				array_shift($objetos);
			}
			
			foreach ($objetos as $id) {

				$model->getObjeto($id);

				switch ($_POST['acoes']) {
				
					case 'excluir' :
						$opcao = "excluído(s)";	
						$affectedRows = $model->excluir();
						if ($affectedRows) {
							$processados += 1;
							$this->log->adicionar ('excluiu', 'informação', $model->nome, 
								"Usuário excluiu informação do Banco de Dados através do recurso de aplicar ações em massa.");
						}
						else {
							$naoProcessados+=1;
						}
					break;
				}
			}
			
		}
		catch (PermissaoException $e) {
			$this->conexao->getConexao()->disconnect();
			setMensagem("error", $e->getMessage());
			Application::redirect("index.php");
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
		
		if ($processados > 0) {
			$conexao->commit();
			setMensagem("info", $processados . " " . $opcao);
		}
		
		if ($naoProcessados > 0) {
			setMensagem("error", $naoProcessados . " não pode(m) ser " . $opcao);
		}
		
		$conexao->disconnect();
		Application::redirect($_SERVER['HTTP_REFERER']);
		exit;
	}

	public function planilhaAction() {
		try {
			//$this->checaPermissao($this->modulo, 'planilha');
			$conexao = $this->conexao->getConexao();
			$model = new Informacao($conexao);
			$colunas = isset($_POST["colunas"]) ? $_POST["colunas"] : array();
			$order = isset($_GET['order']) ? array($_GET['order'] => 'asc') : array('data' => 'desc');
			
			if (count($colunas) == 0) {
				throw new Exception('É necessário escolher pelo menos uma coluna');
			}
			
			if ($colunas[0] == (int) -1) {
				array_shift($colunas);
			}	

			if (isset($_POST["qs"])) {
				foreach ($_POST["qs"] as $key => $value) {
					$where[$key] = $value;
				}
				$informacoes = $model->getObjetos(array(
						'filtro' => $colunas,
						'order' => $order,
						'where' => $where
					)
				);
			}
			else {
				$informacoes = $model->getObjetos(array(
						'filtro' => $colunas,
						'order' => array(
							'nome' => 'asc'
						)
					)
				);
			}
			
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("IEFAP");
			$objPHPExcel->getProperties()->setLastModifiedBy("IEFAP");
			$objPHPExcel->getProperties()->setTitle("Ligar IEFAP");
			$objPHPExcel->getProperties()->setSubject("Ligar IEFAP");
			$objPHPExcel->getProperties()->setDescription("IEFAP - Ligar");
			$objPHPExcel->setActiveSheetIndex(0);
			
			$numeroLinha = 1;
			$numeroColuna = 0;
			
			foreach ($colunas as $coluna) {
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, $numeroLinha, 
						$coluna, false);
			}
			
			$numeroLinha = 2;
			$numeroColuna = 0;
			
			foreach ($informacoes as $key => $obj) {

				if (array_key_exists("data", $informacoes[$key])) {
					$obj->data = desconverteData(substr($obj->data,0,10));
				}
				
				foreach ($obj as $campo => $valor) {					
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, $numeroLinha, 
						$valor, false);
				}
				
				$numeroLinha++;
				$numeroColuna = 0;
			}
			
			$nomeArquivo = "Banco-IEFAP.xlsx";
			$path = DIR_ROOT . DS . "temp";
			
			if (is_writable($path)) {
				$path .=  DS . $nomeArquivo;
				$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
				$objWriter->save($path);
				$this->log->adicionar ("gerou", "planilha", "EXCEL", 
					"Usuário gerou planilha em EXCEL dos registros do Banco de Dados.");
				$conexao->commit();
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="' . basename($path) . '"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: '.filesize($path));
				flush();
				readfile($path);
				unlink($path);
			}
			else {
				throw new Exception ("Erro ao tentar criar arquivo " . $nomeArquivo);
			}
		}
		catch (PermissaoException $e) {
			$this->conexao->getConexao()->disconnect();
			setMensagem("error", $e->getMessage());
			Application::redirect("index.php");
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			$conexao->disconnect();
			setMensagem("error", $e->getMessage());
			Application::redirect("?modulo=banco");
			exit;
		}

		$conexao->disconnect();
	}

    public function ajaxAction() {
		try {
			$conexao = $this->conexao->getConexao();
			$model = new Informacao($conexao);

			$dados = isset($_POST['dados']) ? $_POST['dados'] : array();
			$qs = isset($_POST['qs']) ? $_POST['qs'] : array();

			$modulo = $dados['modulo'];
			$quantidadePorPagina = isset($dados['exibir']) ? (int) $dados['exibir'] : QUANTIDADE_POR_PAGINA;
			$quantidadePorPagina = ($quantidadePorPagina <= 0) ? QUANTIDADE_POR_PAGINA : $quantidadePorPagina;
			$pagina = (isset($_GET['p'])) ? $_GET['p'] : 1;
			$pagina = ($pagina <= 0) ? 1 : $pagina;
			$limit = ($pagina == 1) ? $quantidadePorPagina : $quantidadePorPagina * ($pagina - 1);
			$offset = ($pagina == 1) ? 0 : $quantidadePorPagina;
			list($order,$sort) = explode(';', $dados['order']);

			foreach ($qs as $key => $value) {
				$where[$key] = $value;
			}

			$quantidade = $model->count(array(
					'where' => $where
				)
			);

			$params['objetos'] = $model->getObjetos(array(
					'limit' => $limit,
					'offset' => $offset,
					'order' => array(
						$order => $sort
					),
					'where' => $where
				)
			);

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

			$pages = new Paginator($quantidadePorPagina, 'p');
        	$pages->setTotal($quantidade);
        	$pageLinks = $pages->pageLinks('?modulo=' . $modulo . '&');

			$conexao->disconnect();

			ob_start();
			include DIR_ROOT . '/administrar/views/' . $modulo . '/rows.phtml';
			$contents = ob_get_contents();
	        ob_end_clean();

	        echo json_encode(array(
	        		'inicio' => $iniCount,
	        		'fim' => $fim,
	        		'quantidade' => $quantidade,
		        	'paginacao' => $pageLinks,
		        	'trs' => $contents
		        )
	        );
		}
		catch (Exception $e) {
			$conexao->disconnect();
			echo json_encode(array());
		}
	}

	public function carregarAction() {
		try {
			$this->checaPermissao($this->modulo, 'carregar');
			$conexao = $this->conexao->getConexao();
			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Banco de Dados" => "?modulo=banco",
				"Carregar Arquivo" => ""
			);
		}
		catch (PermissaoException $e) {
			$this->conexao->getConexao()->disconnect();
			setMensagem("error", $e->getMessage());
			Application::redirect("index.php");
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
		
		$conexao->disconnect();					
		$view = new View2($_GET["modulo"], "extendido", "carregar.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs), 
				"breadcrumbs" => $breadcrumbs
			)
		);
		$view->showContents();
	}
	
	public function processaAction() {
		try {
			$this->checaPermissao($this->modulo, 'processa');
			$conexao = $this->conexao->getConexao();
			
			if (!enviouArquivo($_FILES["arquivo"])) { 
				throw new Exception("É necessário escolher um arquivo");
			}
			
			$path = DIR_ROOT . DS . "temp";
			
			if (!verificaTipo ($_FILES["arquivo"], "application/vnd.ms-excel|application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")) {
				throw new Exception("Apenas planilhas EXCEL podem ser enviadas");
			}
			
			if (!salvaArquivo ($_FILES["arquivo"], $path)) {
				throw new Exception("Erro ao tentar enviar arquivo");
			}
				
			$arquivoExcel = PHPExcel_IOFactory::identify($path . DS . $_FILES['arquivo']['name']);
			$objReader = PHPExcel_IOFactory::createReader($arquivoExcel);
			$objPHPExcel = $objReader->load($path . DS . $_FILES['arquivo']['name']);
			
			$objWorksheet = $objPHPExcel->getSheet(0);
			$highestRow = $objWorksheet->getHighestRow();
			$highestColumn = $objWorksheet->getHighestColumn();  
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);  
			
			$cadastrados = 0;
			
			$padrao = array(
				"nome",
				"endereco",
				"cidade",
				"uf",
				"cep",
				"cidadeCurso",
				"email",
				"telefone1",
				"telefone2",
				"empresa",
				"profissao",
				"cursosInteresse",
				"comoConheceu"
			);
			
			for ($row = 1; $row <= 1; ++$row) {
				$linha = array();
				for ($col = 0; $col <= $highestColumnIndex-1; ++$col) {
					$value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
					if ($value != $padrao[$col])
						throw new Exception("Impossível cadastrar porque a planilha não respeita o padrão esperado. A primeira linha da 
						planilha deve conter: ".implode(", ", $padrao) . ". E a organização dos dados deve respeitar essa sequência.");
				}
			}
			
			for ($row = 2; $row <= $highestRow; ++$row) {
				$linha = array();
				for ($col = 0; $col <= $highestColumnIndex; ++$col) {
					$value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
					$linha[] = $value;
				}
				$this->bancoDAO->registrar($conexao, $linha, time());
				$cadastrados++;
			}
			
			$redirect .= "&cadastrados=".$cadastrados;
			
			$conexao->commit();
			
			unlink($path . DS . $_FILES["arquivo"]["name"]);
		}
		catch (PermissaoException $e) {
			$this->conexao->getConexao()->disconnect();
			setMensagem("error", $e->getMessage());
			Application::redirect("index.php");
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
		Application::redirect($_SERVER['HTTP_REFERER']);
		exit;
	}
	
}

?>