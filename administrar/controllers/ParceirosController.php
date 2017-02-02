<?php

class ParceirosController extends Controller {
	
	private $modulo = 'parceiros';

	public function ParceirosController() {
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
			$model = new Parceiro($conexao);
			$breadcrumbs = array();
			$breadcrumbs[] = array('Parceiros' => '');
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
	
	public function cadastrarpfAction() {
		try {
			$this->checaPermissao('profissionais', 'cadastrar');
			$conexao = $this->conexao->getConexao();
			
			$parceiroModel = new Parceiro($conexao);

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Parceiros' => '?modulo=' . $this->modulo
			);
			
			if (isset($_GET["id"])) {
				$model = $parceiroModel->getObjetoOrFail(getVariavel('id'));
				$acao = "editar";
				$breadcrumbs[] = array(
					"Editar PF" => "" 
				);
			}
			else {
				$model = $parceiroModel;
				$acao = "cadastrar";
				$breadcrumbs[] = array(
					"Novo PF" => "" 
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
					$acao = 'cadastrada';
					$this->log->adicionar ('cadastrou', 'parceiro', $model->nome, 
						'Usuário cadastrou parceiro.');					
					$redirecionar = sprintf('?modulo=%s&acao=%s&id=%d', $this->modulo, 
						'cadastrar', $model->id);
				}
				else {
					$acao = 'atualizada';
					$this->log->adicionar ('atualizou', 'parceiro', $model->nome, 
						'Usuário atualizou parceiro.');
					$redirecionar = sprintf('?modulo=%s', $this->modulo);
				}

				setMensagem('info', sprintf('Parceiro %s [%s]', $acao, $model->nome));
				
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
			setMensagem("error", 'Parceiro não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
	
		$conexao->disconnect();					
		$view = new View2($this->modulo, "extendido", "novopf.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"objeto" => $model
			)
		);
        $view->showContents();
	}

	public function cadastrarpjAction() {
		try {
			$this->checaPermissao('profissionais', 'cadastrar');
			$conexao = $this->conexao->getConexao();
			
			$parceiroModel = new Parceiro($conexao);

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Parceiros' => '?modulo=' . $this->modulo
			);
			
			if (isset($_GET["id"])) {
				$model = $parceiroModel->getObjetoOrFail(getVariavel('id'));
				$acao = "editar";
				$breadcrumbs[] = array(
					"Editar PJ" => "" 
				);
			}
			else {
				$model = $parceiroModel;
				$acao = "cadastrar";
				$breadcrumbs[] = array(
					"Novo PJ" => "" 
				);
			}
			
			if (count($_POST) > 0) {
				
				$redirecionar = NULL;
				$dados = $_POST;
				$model->setDados($dados);
				
				$obrigatorios = array(
					"nomeFantasia" => array(
						"nome" => "Nome Fantasia"
					)
				);
				
				Funcoes::validaPost($obrigatorios, $dados);
				$model->salvar();

				if ($acao == 'cadastrar') {	
					$acao = 'cadastrada';
					$this->log->adicionar ('cadastrou', 'parceiro', $model->nomeFantasia, 
						'Usuário cadastrou parceiro.');					
					$redirecionar = sprintf('?modulo=%s&acao=%s&id=%d', $this->modulo, 
						'cadastrar', $model->id);
				}
				else {
					$acao = 'atualizada';
					$this->log->adicionar ('atualizou', 'parceiro', $model->nomeFantasia, 
						'Usuário atualizou parceiro.');
					$redirecionar = sprintf('?modulo=%s', $this->modulo);
				}

				setMensagem('info', sprintf('Parceiro %s [%s]', $acao, $model->nomeFantasia));
				
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
			setMensagem("error", 'Parceiro não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
	
		$conexao->disconnect();					
		$view = new View2($this->modulo, "extendido", "novopj.phtml");
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
			$model = new Administrativo($conexao);
			$objeto = $model->getObjetoOrFail(getVariavel('id'));
			$affectedRows = $objeto->excluir();
			if ($affectedRows) {
				$this->log->adicionar ('excluiu', 'parceiro', $objeto->nome, 
					'Usuário excluiu parceiro.');
				$conexao->commit();					
				setMensagem('info', 'Parceiro excluído [' . $objeto->nome . ']');
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
			setMensagem("error", 'Parceiro não encontrado');
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
			$this->checaPermissao('profissionais', "opcoes");
			$conexao = $this->conexao->getConexao();
			$processados = 0;
			$naoProcessados = 0;
			
			if (empty($_POST["acoes"])) {
				throw new Exception("É necessário escolher uma ação");
			}
			
			$parceiroModel = new Parceiro($conexao);
			$objetos = isset($_POST["objetos"]) ? $_POST["objetos"] : array();
			
			// retira o elemento -1, caso exista
			if (count($objetos) > 0 && $objetos[0] == -1) {
				array_shift($objetos);
			}
			
			foreach ($objetos as $id) {

				$model = $parceiroModel->getObjeto($id);

				switch ($_POST['acoes']) {
				
					case 'excluir' :
						$opcao = "excluído(s)";	
						if ($model->excluir()) {
							$processados += 1;
							$this->log->adicionar ('excluiu', 'parceiro', $model->nome, 
								"Usuário excluiu parceiro através do recurso de aplicar ações em massa.");
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
			$model = new Parceiro($conexao);
			$parceiro = $model->getObjetoOrFail(getVariavel('id'));
			
			$breadcrumbs[] = array(
				"Parceiros" => "?modulo=" . $this->modulo,
				$parceiro->nome => "?modulo=" . $this->modulo . "&acao=cadastrar&id=" . $parceiro->id,
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
				// 		"cargo" => "Parceiro",
				// 		"nome" => $parceiro->nome,
				// 		"email" => $parceiro->email,
				// 		"texto" => $dados["resposta"],
				// 		"timestamp" => $parceiro->timestamp
				// 	), true
				// )) {
				// 	$this->log->adicionar ("enviou", "e-mail", $parceiro->email, 
				// 		"Usuário enviou e-mail para o Profissional Parceiro " . $parceiro->nome . ".");
				// 	setMensagem("info", "E-mail enviado com sucesso para " . $parceiro->email . "<br />Uma cópia foi enviada para contato@iefap.com.br");
					
				// }
				// else {
				// 	$this->log->adicionar ("não enviou", "e-mail", $parceiro->email, 
				// 		"Sistema não foi capaz de enviar e-mail para o Profissional Parceiro " . $parceiro->nome . ".");
				// 	setMensagem("error", "Erro ao tentar enviar e-mail para " . $parceiro->email . ". Por favor, tente novamente!");
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
			setMensagem("error", 'Parceiro não encontrado');
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
				"objeto" => $parceiro,
				"breadcrumbs" => $breadcrumbs
				
			)
		);
        $view->showContents();
	}

	public function ajaxAction() {
		try {
			$conexao = $this->conexao->getConexao();
			$model = new Parceiro($conexao);

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