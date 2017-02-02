<?php

class BannersController extends Controller {
	
	private $modulo = 'banners';

	public function BannersController() {
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
			$model = new Banner($conexao);
			$breadcrumbs = array();
			$breadcrumbs[] = array('Banners' => '');
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
			$this->checaPermissao($this->modulo, 'cadastrar');
			$conexao = $this->conexao->getConexao();
			
			$bannerModel = new Banner($conexao);

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Banners' => '?modulo=' . $this->modulo
			);
			
			if (isset($_GET["id"])) {
				$model = $bannerModel->getObjetoOrFail(getVariavel('id'));
				$bannerAntigo = $model->banner;
				$acao = "editar";
				$breadcrumbs[] = array(
					"Editar" => "" 
				);
				$obrigatorios = array(
					'descricao' => array(
						'nome' => 'Descrição'
					)
				);
			}
			else {
				$model = $bannerModel;
				$acao = "cadastrar";
				$breadcrumbs[] = array(
					"Novo" => "" 
				);
				$obrigatorios = array(
					'descricao' => array(
						'nome' => 'Descrição'
					),
					'banner' => array(
						'nome' => 'Banner'
					)
				);	
			}
			
			if (count($_POST) > 0) {
				
				$redirecionar = NULL;
				$dados = $_POST;
				$model->setDados($dados);
				
				Funcoes::validaPost($obrigatorios, array_merge($_POST, $_FILES));
				if ($model->existe('descricao')) {
					throw new Exception('Já existe uma banner com essa descrição');	
				}

				if (Funcoes::enviouArquivo($_FILES, 'banner')) {
					$diretorio = DIR_UPLOADS . DS . 'banners';
					Funcoes::excluiArquivo($diretorio .  DS . $bannerAntigo);
					$model->banner = Funcoes::enviarArquivo($_FILES, $diretorio, 'banner');
				}
				
				$model->salvar();

				if ($acao == 'cadastrar') {	
					$acao = 'cadastrado';
					$this->log->adicionar ('cadastrou', 'banner', $model->descricao, 
						'Usuário cadastrou banner.');					
					$redirecionar = sprintf('?modulo=%s&acao=%s', $this->modulo, 
						'cadastrar');
				}
				else {
					$acao = 'atualizado';
					$this->log->adicionar ('atualizou', 'banner', $model->descricao, 
						'Usuário atualizou banner.');
					$redirecionar = sprintf('?modulo=%s', $this->modulo);
				}

				setMensagem('info', sprintf('Banner %s [%s]', $acao, $model->descricao));
				
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
			setMensagem("error", 'Banner não encontrado');
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
			$this->checaPermissao($this->modulo, 'excluir');
			$conexao = $this->conexao->getConexao();
			$model = new Banner($conexao);
			$ligar = $model->getObjetoOrFail(getVariavel('id'));
			$affectedRows = $ligar->excluir();
			if ($affectedRows) {
				$diretorio = DIR_UPLOADS . DS . "banners" . DS . $model->banner;
				excluiArquivo($diretorio);
				$this->log->adicionar ('excluiu', 'banner', $model->descricao, 
					'Usuário excluiu banner.');
				$conexao->commit();					
				setMensagem('info', 'Banner excluído [' . $model->descricao . ']');
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
			setMensagem("error", 'Banner não encontrado');
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
			
			$bannerModel = new Banner($conexao);
			$objetos = isset($_POST["objetos"]) ? $_POST["objetos"] : array();
			
			// retira o elemento -1, caso exista
			if (count($objetos) > 0 && $objetos[0] == -1) {
				array_shift($objetos);
			}
			
			foreach ($objetos as $id) {

				$model = $bannerModel->getObjeto($id);

				switch ($_POST['acoes']) {

					case 'ativar' :
						$opcao = "ativado(s)";	
						$model->status = 1;
						if ($model->salvar()) {
							$processados += 1;
							$this->log->adicionar ('ativou', 'banner', $model->descricao, 
								"Usuário ativou banner através do recurso de aplicar ações em massa.");
						}
					break;

					case 'desativar' :
						$opcao = "desativado(s)";	
						$model->status = 0;
						if ($model->salvar()) {
							$processados += 1;
							$this->log->adicionar ('desativou', 'banner', $model->descricao, 
								"Usuário desativou banner através do recurso de aplicar ações em massa.");
						}
					break;
				
					case 'excluir' :
						$opcao = "excluído(s)";	
						if ($model->excluir()) {
							$processados += 1;
							$diretorio = DIR_UPLOADS . DS . "banners" . DS . $model->banner;
							excluiArquivo($diretorio);
							$this->log->adicionar ('excluiu', 'banner', $model->descricao, 
								"Usuário excluiu banner através do recurso de aplicar ações em massa.");
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

	public function ajaxAction() {
		try {
			$conexao = $this->conexao->getConexao();
			$model = new Banner($conexao);

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