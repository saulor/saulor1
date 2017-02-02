<?php

class PermissoesController extends Controller {
	
	private $modulo = 'permissoes';

	public function PermissoesController() {
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
			
			// só administradores master
			if ($_SESSION[PREFIX . "loginCodigo"] != 33) {
				throw new PermissaoException('Você não tem permissão para visualizar logs');
			}

			$conexao = $this->conexao->getConexao();
			$model = new Permissao($conexao);
			$breadcrumbs = array();
			$breadcrumbs[] = array('Permissões' => '');
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
			
			// só administradores master
			if ($_SESSION[PREFIX . "loginCodigo"] != 33) {
				throw new PermissaoException('Você não tem permissão para visualizar logs');
			}

			$conexao = $this->conexao->getConexao();
			
			$permissaoModel = new Permissao($conexao);

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Permissões' => '?modulo=' . $this->modulo
			);
			
			if (isset($_GET["id"])) {
				$model = $permissaoModel->getObjetoOrFail(getVariavel('id'));
				$acao = "editar";
				$breadcrumbs[] = array(
					"Editar" => "" 
				);
			}
			else {
				$model = $permissaoModel;
				$acao = "cadastrar";
				$breadcrumbs[] = array(
					"Nova" => "" 
				);
			}
			
			if (count($_POST) > 0) {
				
				$redirecionar = NULL;
				$dados = $_POST;
				$model->setDados($dados);
				
				$obrigatorios = array(
					"nome" => array(
						"nome" => "Permissão"
					)
				);
				
				Funcoes::validaPost($obrigatorios, $dados);
				if ($model->existe('nome')) {
					throw new Exception('Já existe uma permissão com esse nome');	
				}
				$model->salvar();

				if ($acao == 'cadastrar') {	
					$acao = 'cadastrada';
					$this->log->adicionar ('cadastrou', 'permissão', $model->nome, 
						'Usuário cadastrou permissão.');					
					$redirecionar = sprintf('?modulo=%s&acao=%s&id=%d', $this->modulo, 
						'cadastrar', $model->id);
				}
				else {
					$acao = 'atualizada';
					$this->log->adicionar ('atualizou', 'permissão', $model->nome, 
						'Usuário atualizou permissão.');
					$redirecionar = sprintf('?modulo=%s', $this->modulo);
				}

				setMensagem('info', sprintf('Permissão %s [%s]', $acao, $model->nome));
				
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
			setMensagem("error", 'Permissão não encontrada');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
	
		$conexao->disconnect();					
		$view = new View2($this->modulo, "extendido", "nova.phtml");
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
			
			// só administradores master
			if ($_SESSION[PREFIX . "loginCodigo"] != 33) {
				throw new PermissaoException('Você não tem permissão para visualizar logs');
			}

			$conexao = $this->conexao->getConexao();
			$model = new Permissao($conexao);
			$permissao = $model->getObjetoOrFail(getVariavel('id'));
			$affectedRows = $permissao->excluir();
			if ($affectedRows) {
				$this->log->adicionar ('excluiu', 'permissão', $model->nome, 
					'Usuário excluiu permissão.');
				$conexao->commit();					
				setMensagem('info', 'Permissão excluída [' . $model->nome . ']');
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
			setMensagem("error", 'Permissão não encontrada');
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
			
			// só administradores master
			if ($_SESSION[PREFIX . "loginCodigo"] != 33) {
				throw new PermissaoException('Você não tem permissão para visualizar logs');
			}
			
			$conexao = $this->conexao->getConexao();
			$processados = 0;
			$naoProcessados = 0;
			
			if (empty($_POST["acoes"])) {
				throw new Exception("É necessário escolher uma ação");
			}
			
			$permissaoModel = new Permissao($conexao);
			$objetos = isset($_POST["objetos"]) ? $_POST["objetos"] : array();
			
			// retira o elemento -1, caso exista
			if (count($objetos) > 0 && $objetos[0] == -1) {
				array_shift($objetos);
			}
			
			foreach ($objetos as $id) {

				$model = $permissaoModel->getObjeto($id);

				switch ($_POST['acoes']) {
				
					case 'excluir' :
						$opcao = "excluída(s)";	
						if ($model->excluir()) {
							$processados += 1;
							$this->log->adicionar ('excluiu', 'permissão', $model->nome, 
								"Usuário excluiu permissão através do recurso de aplicar ações em massa.");
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

	public function definirAction() {
		
		try {

			// só administradores master
			if ($_SESSION[PREFIX . "loginCodigo"] != 33) {
				throw new PermissaoException('Você não tem permissão para visualizar logs');
			}
			
			$conexao = $this->conexao->getConexao();	
			$model = new Permissao($conexao);
			$acoesModel = new PermissaoAcao($conexao);
			$permissao = $model->getObjetoOrFail(getVariavel('id'));
			$permissoesAtuais = $acoesModel->getAcoes($permissao);
			$aux1 = $permissoesAtuais['formato2'];

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Permissões' => "?modulo=" . $this->modulo,
				$permissao->nome => "?modulo=" . $this->modulo . "&acao=cadastrar&id=" . $permissao->id,
				"Definir permissões" => ""
			);	

			if (count($_POST) > 0) {

				$aux2 = array();
				$permissoesPost = isset($_POST["permissoes"]) ? $_POST["permissoes"] : array();

				$jaExiste = function($model, $idP, $modulo, $acao) {
					return $model->count(array(
							'where' => array(
								'permissao' => $idP,
								'modulo' => $modulo,
								'acao' => $acao
							)
						)
					) > 0;
				};

				$getAcao = function($model, $idP, $modulo, $acao) {
					return $model->get(array(
							'where' => array(
								'permissao' => $idP,
								'modulo' => $modulo,
								'acao' => $acao
							)
						)
					);
				};

				foreach ($permissoesPost as $permissaoPost) {

					//print_r($permissaoPost);
					
					list($modulo, $acoes) = explode(":", $permissaoPost);

					if (!array_key_exists($modulo, $aux2)) {
						$aux2[$modulo] = array();
					}

					foreach (explode(",", $acoes) as $a) {
						if (!in_array($a, $aux2[$modulo])) {
							$aux2[$modulo][] = $a;
						}
					}

					
				}

				

				// percorre as acões atuais
				foreach ($aux1 as $modulo => $acoes) {
					foreach ($acoes as $acao) {
						// se a ação atual não estiver no array submetido significa
						// que foi desmarcada
						if (!array_key_exists($modulo, $aux2) || !in_array($acao, $aux2[$modulo])) {
							$obj = $getAcao($acoesModel, $permissao->id, $modulo, $acao);
							$obj->liberado = 0;
							$obj->salvar();
						}
					}
				}

				// percorro as ações submetidas
				foreach ($aux2 as $modulo => $acoes) {
					foreach ($acoes as $acao) {
						// se a ação submetida não estiver no array das atuais significa
						// que foi marcada
						if (!array_key_exists($modulo, $aux1) || !in_array($acao, $aux1[$modulo])) {
							$obj = $getAcao($acoesModel, $permissao->id, $modulo, $acao);
							if ($obj) {
								$obj->liberado = 1;
								$obj->salvar();
							}
							else {
								$obj = new PermissaoAcao($conexao);
								$obj->permissao = $permissao->id;
								$obj->modulo = $modulo;
								$obj->acao = $acao;
								$obj->liberado = 1;
								$obj->salvar();
							}
						}
					}
				}

				$conexao->commit();
				Application::redirect('?modulo=permissoes&acao=definir&id=' . $permissao->id);
				exit;

			}
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
		
		$conexao->disconnect();					
		$view = new View2($_GET["modulo"], "extendido", "definir.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs), 
				"breadcrumbs" => $breadcrumbs,
				"permissao" => $model,
				"permissoesAtuais" => $permissoesAtuais['formato1']
			)
		);
        $view->showContents();
	}

	public function ajaxAction() {
		try {
			$conexao = $this->conexao->getConexao();
			$model = new Permissao($conexao);

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