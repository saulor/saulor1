<?php

class LigarController extends Controller {

	private $modulo = 'ligar';
	
	public function LigarController() {
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
			$model = new Ligar($conexao);
			$breadcrumbs = array();
			$breadcrumbs[] = array('Ligar' => '');
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
			$this->checaPermissao($this->modulo, 'editar');
			$conexao = $this->conexao->getConexao();

			if (!isset($_GET['id'])) {
				$redirecionar = sprintf('?modulo=%s', $this->modulo);
				throw new Exception('Registro não encontrado');
			}
			
			$ligarModel = new Ligar($conexao);
			$cursoModel = new Curso($conexao);

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Ligar' => '?modulo=' . $this->modulo,
				'Editar' => '' 
			);

			$model = $ligarModel->getObjetoOrFail(getVariavel('id'));
			$cursos = $cursoModel->getObjetos(array(
					'order' => array('nome' => 'asc')
				)
			);
			
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

				$acao = 'atualizado';
				$this->log->adicionar ('atualizou', 'ligar', $model->nome, 
					'Usuário atualizou registro do módulo Ligar.');
				$redirecionar = sprintf('?modulo=%s', $this->modulo);
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
		$view = new View2($this->modulo, "extendido", "editar.phtml");
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
			$model = new Ligar($conexao);
			$ligar = $model->getObjetoOrFail(getVariavel('id'));
			$affectedRows = $ligar->excluir();
			if ($affectedRows) {
				$this->log->adicionar ('excluiu', 'ligar', $model->nome, 
					'Usuário excluiu registro do módulo Ligar.');
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
			
			$model = new Ligar($conexao);
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
							$this->log->adicionar ('excluiu', 'ligar', $model->nome, 
								"Usuário excluiu registro do módulo ligar através do recurso de aplicar ações em massa.");
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
			$model = new Ligar($conexao);
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
				
				if (array_key_exists("whatsapp", $informacoes[$key])) {
					$obj->whatsapp = $obj->whatsapp == 1 ? "Sim" : "Não";
				}

				if (array_key_exists("situacao", $informacoes[$key])) {
					$obj->situacao = Ligar::getSituacao($obj->situacao);
				}

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
			
			$nomeArquivo = "Ligar-IEFAP.xlsx";
			$path = DIR_ROOT . DS . "temp";
			
			if (is_writable($path)) {
				$path .=  DS . $nomeArquivo;
				$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
				$objWriter->save($path);
				$this->log->adicionar ("gerou", "planilha", "EXCEL", 
					"Usuário gerou planilha em EXCEL dos registros do módulo ligar.");
				$conexao->commit();
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="' . basename($path) . '"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: '.filesize($path));
				//ob_clean();
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
			Application::redirect("?modulo=ligar");
			exit;
		}

		$conexao->disconnect();

	}

	public function novaAction() {
		try {
			$conexao = $this->conexao->getConexao();
			
			$cursoModel = new Curso($conexao);
			$historicoModel = new HistoricoPreinscricao($conexao);
			$ligarModel = new Ligar($conexao);

			$ligar = $ligarModel->getObjetoOrFail(getVariavel('id'));

            $cursos = $cursoModel->getObjetos(array(
					'order' => array('nome' => 'asc')
				)
			);

            $breadcrumbs[] = array(
				'Ligar' => '?modulo=' . $this->modulo,
				'Converter para inscrição' => ''
			);
			
			if (count($_POST) > 0) {

				$obrigatorios = array(
					'curso' => array(
						'tipo' => 'select', 
						'nome' => 'Curso'
					)	
				);

				$mensagem = validaPost($obrigatorios, $_POST);
				if (!empty($mensagem)) {
					throw new Exception($mensagem);
				}

	            $curso = $cursoModel->getObjetoOrFail($_POST['curso']);

				$time = time();
				$id = $conexao->query()
					->from('preinscricoes')
					->save(array(
							'curso' => (int) $curso->id,
							'nome' => $ligar->nome,
							'operadoraCelular' => $ligar->operadora,
							'telefone' => $ligar->telefone,
							'email' => $ligar->email,
							'cidade' => $ligar->cidade,
							'status' => (int) $_POST['status'],
							'data' => date('Y-m-d H:i:s', $time),
							'timestamp' => $time
						)
					);

				$ligar->convertido = 1;
				$ligar->curso = $curso->nome;
				$ligar->salvar();

				// adiciona no histórico
				$historicoModel->adicionar($id, "Inscrição criada a partir de um registro do módulo ligar");
				// adciciona nos logs
				$this->log->adicionar ("converteu", "ligar", $ligar->nome, 
					sprintf("Usuário converteu registro do módulo ligar em inscrição no curso %s.", 
						$curso->nome));
				$conexao->commit();
				$conexao->disconnect();
				setMensagem('info', 'Registro convertido para inscrição com sucesso');
				Application::redirect(sprintf("?modulo=preinscricoes&acao=cadastrar&curso=%d&tab1=%d&id=%d", 
					$curso->id, $_POST["status"], $id));
				exit;
			}			

		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
		
		$conexao->disconnect();					
		$view = new View2($this->modulo, "extendido", 'nova.phtml');
		$view->setParams(array(
				'title' => getTitulo($breadcrumbs), 
				'cursos' => $cursos,
				'ligar' => $ligarModel,
				'statuses' => Preinscricao::getStatuses(),
				'breadcrumbs' => $breadcrumbs
			)
		);
		$view->showContents();
    }

    public function ajaxAction() {
		try {
			$conexao = $this->conexao->getConexao();
			$model = new Ligar($conexao);

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
	
}

?>