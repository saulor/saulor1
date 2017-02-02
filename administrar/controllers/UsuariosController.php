<?php

class UsuariosController extends Controller {
	
	private $modulo = 'usuarios';

	public function UsuariosController() {
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
				throw new PermissaoException('Você não tem permissão para gerenciar usuários');
			}

			$conexao = $this->conexao->getConexao();
			$model = new VwUsuario($conexao);
			$breadcrumbs = array();
			$breadcrumbs[] = array('Usuários' => '');
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
				throw new PermissaoException('Você não tem permissão para gerenciar usuários');
			}

			$conexao = $this->conexao->getConexao();
			
			$usuarioModel = new Usuario($conexao);
			$permissaoModel = new Permissao($conexao);

			$permissoes = $permissaoModel->getObjetos(array(
					'order' => array(
						'nome' => 'asc'
					)
				)
			);

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Usuários' => '?modulo=' . $this->modulo
			);
			
			if (isset($_GET["id"])) {
				$model = $usuarioModel->getObjetoOrFail(getVariavel('id'));
				$senha = $model->senha;
				$acao = "editar";
				$breadcrumbs[] = array(
					"Editar" => "" 
				);
			}
			else {
				$model = $usuarioModel;
				$acao = "cadastrar";
				$breadcrumbs[] = array(
					"Novo" => "" 
				);
			}
			
			if (count($_POST) > 0) {
				
				$redirecionar = NULL;
				$dados = $_POST;
				if (isset($senha) && empty($dados['senha'])) {
					$dados['senha'] = $senha;
				}
				else {
					$dados['senha'] = md5($dados['senha']);
				}
				$model->setDados($dados);
				
				$obrigatorios = array(
					"nome" => array(
						"nome" => "Nome"
					),
					"login" => array(
						"nome" => "Login"
					),
					"senha" => array(
						"nome" => "Senha"
					),
					"permissao" => array(
						"nome" => "Permissão"
					)
				);
				
				Funcoes::validaPost($obrigatorios, $dados);
				$model->salvar();

				if ($acao == 'cadastrar') {	
					$acao = 'cadastrado';
					$this->log->adicionar ('cadastrou', 'usuário', $model->nome, 
						'Usuário cadastrou usuário.');					
					$redirecionar = sprintf('?modulo=%s&acao=%s&id=%d', $this->modulo, 
						'cadastrar', $model->id);
				}
				else {
					$acao = 'atualizado';
					$this->log->adicionar ('atualizou', 'usuário', $model->nome, 
						'Usuário atualizou usuário.');
					$redirecionar = sprintf('?modulo=%s', $this->modulo);
				}

				setMensagem('info', sprintf('Usuário %s [%s]', $acao, $model->nome));
				
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
			setMensagem("error", 'Usuário não encontrado');
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
				'permissoes' => $permissoes
			)
		);
        $view->showContents();
	}
	
	public function excluirAction() {
		try {
			
			// só administradores master
			if ($_SESSION[PREFIX . "loginCodigo"] != 33) {
				throw new PermissaoException('Você não tem permissão para gerenciar usuários');
			}

			$conexao = $this->conexao->getConexao();
			$model = new Usuario($conexao);
			$preinscricaoModel = new Preinscricao($conexao);

			$usuario = $model->getObjetoOrFail(getVariavel('id'));

			if ($preinscricaoModel->getQuery()
				->where('quem = ?', (int) $usuario->id)
				->count() > 0) {
				throw new Exception ('Este usuário é responsável por inscricões e não pode ser excluído.');
			}
			
			$affectedRows = $usuario->excluir();
			if ($affectedRows) {
				$this->log->adicionar ('excluiu', 'usuário', $model->nome, 
					'Usuário excluiu usuario.');
				$conexao->commit();					
				setMensagem('info', 'Usuario excluído [' . $model->nome . ']');
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
			setMensagem("error", 'Usuario não encontrado');
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
				throw new PermissaoException('Você não tem permissão para gerenciar usuários');
			}

			$conexao = $this->conexao->getConexao();
			$processados = 0;
			$naoProcessados = 0;
			
			if (empty($_POST["acoes"])) {
				throw new Exception("É necessário escolher uma ação");
			}
			
			$noticiaModel = new Usuario($conexao);
			$objetos = isset($_POST["objetos"]) ? $_POST["objetos"] : array();
			
			// retira o elemento -1, caso exista
			if (count($objetos) > 0 && $objetos[0] == -1) {
				array_shift($objetos);
			}
			
			foreach ($objetos as $id) {

				$model = $noticiaModel->getObjeto($id);

				switch ($_POST['acoes']) {

					case 'ativar' :
						$opcao = "ativado(s)";	
						$model->status = 1;
						if ($model->salvar()) {
							$processados += 1;
							$this->log->adicionar ('ativou', 'usuário', $model->nome, 
								"Usuário ativou usuário através do recurso de aplicar ações em massa.");
						}
					break;

					case 'desativar' :
						$opcao = "desativado(s)";	
						$model->status = 0;
						if ($model->salvar()) {
							$processados += 1;
							$this->log->adicionar ('desativou', 'usuário', $model->nome, 
								"Usuário desativou usuário através do recurso de aplicar ações em massa.");
						}
					break;
				
					case 'excluir' :
						$opcao = "excluído(s)";	
						if ($model->excluir()) {
							$processados += 1;
							$this->log->adicionar ('excluiu', 'usuário', $model->nome, 
								"Usuário excluiu usuário através do recurso de aplicar ações em massa.");
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

	public function unidadesAction() {
		try {
			
			// só administradores master
			if ($_SESSION[PREFIX . "loginCodigo"] != 33) {
				throw new PermissaoException('Você não tem permissão para gerenciar usuários');
			}

			$conexao = $this->conexao->getConexao();
			$usuarioModel = new Usuario($conexao);
			$unidadesModel = new VwUnidade($conexao);

			$model = $usuarioModel->getObjetoOrFail(getVariavel('id'));
			// unidades/cidades cadastradas
			$unidades = $unidadesModel->getUnidades();
			$unidadesUsuario = $model->getUnidades();

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Usuários' => '?modulo=' . $this->modulo,
				$model->nome => '?modulo=' . $this->modulo . '&acao=cadastrar&id=' . $model->id,
				'Unidades' => ''
			);
		
			
			if (count($_POST) > 0) {

				$unidades = isset($_POST['unidades']) ? $_POST['unidades'] : array();

				$adicionados = array_diff($unidades, $unidadesUsuario);

				$excluidos = array_diff($unidadesUsuario, $unidades);

				foreach ($adicionados as $id) {
					$obj = new UsuarioUnidade($conexao);
					$obj->usuario = $model->id;
					$obj->unidade = $id;
					$obj->salvar();
				}

				foreach ($excluidos as $id) {
					$unidadesModel = new UsuarioUnidade($conexao);
					$obj = $unidadesModel->get(array(
							'where' => array(
								'usuario' => $model->id,
								'unidade' => $id
							)
						)
					);
					$obj->excluir();
				}

				$conexao->commit();
				$conexao->disconnect();	
				setMensagem('info', 'Unidades atualizadas');
				Application::redirect('?modulo=usuarios&acao=unidades&id=' . $model->id);
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
			setMensagem("error", 'Usuário não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
	
		$conexao->disconnect();					
		$view = new View2($this->modulo, "extendido", "unidades.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"objeto" => $model,
				'unidades' => $unidades,
				'unidadesUsuario' => $unidadesUsuario
			)
		);
        $view->showContents();
	}

	public function cursosAction() {
		try {
			
			// só administradores master
			if ($_SESSION[PREFIX . "loginCodigo"] != 33) {
				throw new PermissaoException('Você não tem permissão para gerenciar usuários');
			}

			$conexao = $this->conexao->getConexao();
			$usuarioModel = new Usuario($conexao);
			$cursosModel = new Curso($conexao);

			$model = $usuarioModel->getObjetoOrFail(getVariavel('id'));
			$cursos = $cursosModel->getObjetos(array(
					'order' => array(
						'nome' => 'asc'
					)
				)
			);

			// retorna os cursos liberados para o usuário
			$cursosUsuario = $model->getCursos();

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Usuários' => '?modulo=' . $this->modulo,
				$model->nome => '?modulo=' . $this->modulo . '&acao=cadastrar&id=' . $model->id,
				'Cursos' => ''
			);
		
			
			if (count($_POST) > 0) {

				$cursos = isset($_POST['cursos']) ? $_POST['cursos'] : array();

				$adicionados = array_diff($cursos, $cursosUsuario);

				$excluidos = array_diff($cursosUsuario, $cursos);

				foreach ($adicionados as $id) {
					$obj = new UsuarioCurso($conexao);
					$obj->usuario = $model->id;
					$obj->curso = $id;
					$obj->salvar();
				}

				foreach ($excluidos as $id) {
					$cursosModel = new UsuarioCurso($conexao);
					$obj = $cursosModel->get(array(
							'where' => array(
								'usuario' => $model->id,
								'curso' => $id
							)
						)
					);
					$obj->excluir();
				}

				$conexao->commit();
				$conexao->disconnect();	
				setMensagem('info', 'Cursos atualizados');
				Application::redirect('?modulo=usuarios&acao=cursos&id=' . $model->id);
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
			setMensagem("error", 'Usuário não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
	
		$conexao->disconnect();					
		$view = new View2($this->modulo, "extendido", "cursos.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"objeto" => $model,
				'cursos' => $cursos,
				'cursosUsuario' => $cursosUsuario
			)
		);
        $view->showContents();
	}

	public function acessarAction() {
		try {
			
			// só administradores master
			if ($_SESSION[PREFIX . "loginCodigo"] != 33) {
				throw new PermissaoException('Você não tem permissão para gerenciar usuários');
			}

			$conexao = $this->conexao->getConexao();
			$usuarioModel = new Usuario($conexao);
			$permissaoModel = new Permissao($conexao);
			$usuarioCursoModel = new UsuarioCurso($conexao);
			$usuarioUnidadeModel = new UsuarioUnidade($conexao);
			$permissaoAcaoModel = new PermissaoAcao($conexao);

			$usuario = $usuarioModel->getObjetoOrFail(getVariavel('id'));
			$permissao = $permissaoModel->getObjeto($usuario->permissao);
			$unidades = $usuario->getUnidades();
			$cursos = $usuario->getCursos();
			$permissoes = $permissao->getAcoes();

			unset($_SESSION[PREFIX . "loginId"]);
			unset($_SESSION[PREFIX . "loginNome"]);
			unset($_SESSION[PREFIX . "loginPermissao"]);
			unset($_SESSION[PREFIX . "loginCodigo"]);
			unset($_SESSION[PREFIX . "cursos"]);
			unset($_SESSION[PREFIX . "permissoes"]);

			$_SESSION[PREFIX . "loginId"] = $usuario->id;
			$_SESSION[PREFIX . "loginNome"] = $usuario->nome;
			$_SESSION[PREFIX . "loginPermissao"] = $usuario->permissao;
			$_SESSION[PREFIX . "loginCodigo"] = $permissao->codigoPermissao;
			$_SESSION[PREFIX . "filtrar"] = $usuario->filtrar;
			$_SESSION[PREFIX . "unidades"] = $unidades;
			$_SESSION[PREFIX . "cursos"] = $cursos;
			$_SESSION[PREFIX . "permissoes"] = $permissoes['formato2'];
			
		}
		catch (PermissaoException $e) {
			$this->conexao->getConexao()->disconnect();
			setMensagem("error", $e->getMessage());
			Application::redirect("index.php");
			exit;
		}
		catch (ModelNotFoundException $e) {
			$conexao->disconnect();
			setMensagem("error", 'Usuário não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
	
		Application::redirect($_SERVER['HTTP_REFERER']);
		exit;
	}

	public function ajaxAction() {
		try {
			$conexao = $this->conexao->getConexao();
			$model = new VwUsuario($conexao);

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