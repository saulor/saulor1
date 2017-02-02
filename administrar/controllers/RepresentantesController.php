<?php

class RepresentantesController extends Controller {
	
	private $modulo = 'representantes';

	public function RepresentantesController() {
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
			$this->checaPermissao('profissionais', 'index');
			$conexao = $this->conexao->getConexao();
			$model = new Representante($conexao);
			$breadcrumbs = array();
			$breadcrumbs[] = array('Representantes' => '');
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
			)
		);
        $view->showContents();
	}
	
	public function cadastrarAction() {
		try {
			$this->checaPermissao('profissionais', 'cadastrar');
			$conexao = $this->conexao->getConexao();
			
			$representanteModel = new Representante($conexao);

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Representantes' => '?modulo=' . $this->modulo
			);
			
			if (isset($_GET["id"])) {
				$model = $representanteModel->getObjetoOrFail(getVariavel('id'));
				$acao = "editar";
				$breadcrumbs[] = array(
					"Editar" => "" 
				);
			}
			else {
				$model = $representanteModel;
				$acao = "cadastrar";
				$breadcrumbs[] = array(
					"Novo" => "" 
				);
			}
			
			if (count($_POST) > 0) {
				
				$redirecionar = NULL;
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
					$this->log->adicionar ('cadastrou', 'representante', $model->nome, 
						'Usuário cadastrou representante.');					
					$redirecionar = sprintf('?modulo=%s&acao=%s&id=%d', $this->modulo, 
						'cadastrar', $model->id);
				}
				else {
					$acao = 'atualizado';
					$this->log->adicionar ('atualizou', 'representante', $model->nome, 
						'Usuário atualizou representante.');
					$redirecionar = sprintf('?modulo=%s', $this->modulo);
				}

				setMensagem('info', sprintf('Representante %s [%s]', $acao, $model->nome));
				
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
			setMensagem("error", 'Representante não encontrado');
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
				"objeto" => $model
			)
		);
        $view->showContents();
	}
	
	public function excluirAction() {
		try {
			$this->checaPermissao('profissionais', 'excluir');
			$conexao = $this->conexao->getConexao();
			$model = new Representante($conexao);
			$objeto = $model->getObjetoOrFail(getVariavel('id'));
			$affectedRows = $objeto->excluir();
			if ($affectedRows) {
				$diretorio = DIR_UPLOADS . DS . 'trabalhe' . DS . 'representantes';
				$diretorio .= DS . $objeto->id;
				Funcoes::excluiDiretorio($diretorio);
				$this->log->adicionar ('excluiu', 'representante', $objeto->nome, 
					'Usuário excluiu representante.');
				$conexao->commit();					
				setMensagem('info', 'Representante excluído [' . $objeto->nome . ']');
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
			setMensagem("error", 'Representante não encontrado');
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

	public function excluircAction() {
		try {
			$this->checaPermissao('profissionais', 'excluir');
			$conexao = $this->conexao->getConexao();
			$model = new Representante($conexao);
			$representante = $model->getObjetoOrFail(getVariavel('id'));
			
			$diretorio = DIR_UPLOADS . DS . "trabalhe";
			$diretorio .= DS . "representantes" . DS;
			$diretorio .= $representante->id . DS;
			$diretorio .= base64_decode($representante->curriculoComercial);
			
			if (!existeArquivo($diretorio)) {
				throw new Exception("Currículo não encontrado");
			}

			if (excluiArquivo($diretorio)) {
				$this->log->adicionar ("excluiu", "currículo", $representante->nome, 
					"Usuário excluiu currículo do Profissional Representante.");	
				$representante->curriculoComercial = NULL;
				$representante->mime = NULL;
				$representante->extensao = NULL;
				$representante->salvar();	
				$conexao->commit();	
				setMensagem("info", "Currículo excluído");
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
		
		$conexao->disconnect();
		Application::redirect($_SERVER['HTTP_REFERER']);
		exit;
	}

	public function downloadAction () {
		try {
			$this->checaPermissao('profissionais', 'cadastrar');
			$conexao = $this->conexao->getConexao();
			$model = new Representante($conexao);
			$representante = $model->getObjetoOrFail(getVariavel('id'));
		
			$diretorio = DIR_UPLOADS . DS . "trabalhe" . DS;
			$diretorio .= "representantes" . DS . $representante->id;
			$diretorio .= DS . base64_decode($representante->curriculoComercial);
			
			if (!existeArquivo($diretorio)) {
				throw new Exception("Currículo não encontrado");
			}
			
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . base64_decode($representante->curriculoComercial) . '"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($diretorio));
			//ob_clean();
			flush();
			readfile($diretorio);
			
			// adiciona nos logs 
			$this->log->adicionar ("download", "currículo", "Profissional Representante", 
				"Usuário fez download de currículo do Profissional Representante " . $representante->nome . ".");
			// comita transação
			$conexao->commit();
		}
		catch (PermissaoException $e) {
			$this->conexao->getConexao()->disconnect();
			setMensagem("error", $e->getMessage());
			Application::redirect("index.php");
			exit;
		}
		catch (ModelNotFoundException $e) {
			setMensagem("error", 'Representante não encontrado');
		}
		catch (Exception $e) {
			setMensagem("error", $e->getMessage());
		}	

		$conexao->disconnect();
		Application::redirect($_SERVER['HTTP_REFERER']);
		exit;
	}

	public function acoesAction() {
		try {
			$this->checaPermissao('profissionais', "opcoes");
			$conexao = $this->conexao->getConexao();
			$processados = 0;
			$naoProcessados = 0;
			
			if (empty($_POST["acoes"])) {
				throw new Exception("É necessário escolher uma ação");
			}
			
			$representanteModel = new Representante($conexao);
			$objetos = isset($_POST["objetos"]) ? $_POST["objetos"] : array();
			
			// retira o elemento -1, caso exista
			if (count($objetos) > 0 && $objetos[0] == -1) {
				array_shift($objetos);
			}
			
			foreach ($objetos as $id) {

				$model = $representanteModel->getObjeto($id);

				switch ($_POST['acoes']) {
				
					case 'excluir' :
						$opcao = "excluído(s)";	
						if ($model->excluir()) {
							$processados += 1;
							$this->log->adicionar ('excluiu', 'representante', $model->nome, 
								"Usuário excluiu representante através do recurso de aplicar ações em massa.");
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

	public function emailAction() {
		try {
			$this->checaPermissao('profissionais', 'email');
			$conexao = $this->conexao->getConexao();
			$model = new Representante($conexao);
			$representante = $model->getObjetoOrFail(getVariavel('id'));
			
			$breadcrumbs[] = array(
				"Representantes" => "?modulo=" . $this->modulo,
				$representante->nome => "?modulo=" . $this->modulo . "&acao=cadastrar&id=" . $representante->id,
				"E-mail" => ""
			);

			if (count($_POST) > 0) {
				
				$dados = $_POST;
				
				$obrigatorios = array(
					"resposta" => array(
						"nome" => "Resposta"
					)
				);
				
				Funcoes::validaPost($obrigatorios, $dados);
				
				// if (enviarEmailProfissional(array(
				// 		"cargo" => "Representante",
				// 		"nome" => $representante->nome,
				// 		"email" => $representante->email,
				// 		"texto" => $dados["resposta"],
				// 		"timestamp" => $representante->timestamp
				// 	), true
				// )) {
				// 	$this->log->adicionar ("enviou", "e-mail", $representante->email, 
				// 		"Usuário enviou e-mail para o Profissional Representante " . $representante->nome . ".");
				// 	setMensagem("info", "E-mail enviado com sucesso para " . $representante->email . "<br />Uma cópia foi enviada para contato@iefap.com.br");
					
				// }
				// else {
				// 	$this->log->adicionar ("não enviou", "e-mail", $representante->email, 
				// 		"Sistema não foi capaz de enviar e-mail para o Profissional Representante " . $representante->nome . ".");
				// 	setMensagem("error", "Erro ao tentar enviar e-mail para " . $representante->email . ". Por favor, tente novamente!");
				// }
				
				$conexao->commit();
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
			setMensagem("error", 'Representante não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->disconnect();
			setMensagem("error", $e->getMessage());
		}
		
		$conexao->disconnect();
		$view = new View2($_GET['modulo'], "extendido", "email.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs), 
				"objeto" => $representante,
				"breadcrumbs" => $breadcrumbs
				
			)
		);
        $view->showContents();
	}

	public function ajaxAction() {
		try {
			$conexao = $this->conexao->getConexao();
			$model = new Representante($conexao);

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