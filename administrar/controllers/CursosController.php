<?php

class CursosController extends Controller {
	
	private $modulo = 'cursos';

	public function CursosController() {
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
			$this->checaPermissao('cursos', 'index');

			$quantidadePorPagina = isset($_GET['exibir'])  ? (int) $_GET['exibir'] : QUANTIDADE_POR_PAGINA;
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

			$breadcrumbs = array();
			$breadcrumbs[] = array('Cursos' => '');
			$quantidade = 0;
			$objetos = $categorias = array();
			$pageLinks = NULL;

			$conexao = $this->conexao->getConexao();
			$model = new VwCurso($conexao);
			$categoriasModel = new CursoCategoria($conexao);

			$quantidade = $model->count();
			$objetos = $model->getObjetos(array(
					'limit' => $limit, 
					'offset' => $offset,
					'order' => $order
				)
			);
			$categorias = $categoriasModel->listaPais();

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
				'categorias' => $categorias,
				'quantidade' => $quantidade,
				'quantidadePorPagina' => $quantidadePorPagina,
				'pagina' => $pagina,
				'breadcrumbs' => $breadcrumbs,
				'paginacao' => $pageLinks,
			)
		);
        $view->showContents();
	}
	
	public function cadastrar1Action() {
		try {
			$this->checaPermissao('cursos', 'cadastrar');
			$conexao = $this->conexao->getConexao();
			
			$cursoModel = new Curso($conexao);
			$categoriasModel = new CursoCategoria($conexao);
			
			$categorias = $categoriasModel->listaPais();

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Cursos' => '?modulo=' . $this->modulo
			);
			
			if (isset($_GET["id"])) {
				$model = $cursoModel->getObjetoOrFail(getVariavel('id'));
				$acao = "editar";
				$breadcrumbs[] = array(
					"Editar" => "" 
				);
			}
			else {
				$model = $cursoModel;
				$acao = "cadastrar";
				$breadcrumbs[] = array(
					"Novo" => "" 
				);
			}
			
			if (count($_POST) > 0) {
				
				$redirecionar = NULL;
				$dados = $_POST;
				$dados['link'] = Funcoes::criaSlug($dados['nome']);
				$model->setDados($dados);
				
				$obrigatorios = array(
					"nome" => array(
						"nome" => "Nome"
					),
					"categoria" => array(
						"nome" => "Categoria"
					)
				);
				
				Funcoes::validaPost($obrigatorios, $dados);
				$model->salvar();
				CursoCategoria::atualizaQuantidades($conexao);

				if ($acao == 'cadastrar') {	
					$acao = 'cadastrado';
					$this->_afterCadastro($conexao, $model);
					$this->log->adicionar ('cadastrou', 'curso', $model->nome, 
						'Usuário cadastrou curso.');					
					$redirecionar = sprintf('?modulo=%s&acao=%s&id=%d', $this->modulo, 
						'cadastrar1', $model->id);
				}
				else {
					$acao = 'atualizado';
					$this->log->adicionar ('atualizou', 'curso', $model->nome, 
						'Usuário atualizou curso.');
					$redirecionar = sprintf('?modulo=%s', $this->modulo);
				}

				setMensagem('info', sprintf('Curso %s [%s]', $acao, $model->nome));
				
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
			setMensagem("error", 'Curso não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
	
		$conexao->disconnect();					
		$view = new View2($this->modulo, "extendido", "cadastrar1.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"objeto" => $model,
				'categorias' => $categorias
			)
		);
        $view->showContents();
	}

	public function cadastrar2Action() {
		try {
			$this->checaPermissao('cursos', 'cadastrar');
			$conexao = $this->conexao->getConexao();
			
			$cursoModel = new Curso($conexao);
			$categoriasModel = new CursoCategoria($conexao);
			
			$categorias = $categoriasModel->listaPais();

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Cursos' => '?modulo=' . $this->modulo
			);
			
			if (isset($_GET["id"])) {
				$model = $cursoModel->getObjetoOrFail(getVariavel('id'));
				$acao = "editar";
				$breadcrumbs[] = array(
					"Editar" => "" 
				);
			}
			else {
				$model = $cursoModel;
				$acao = "cadastrar";
				$breadcrumbs[] = array(
					"Novo" => "" 
				);
			}
			
			if (count($_POST) > 0) {
				
				$redirecionar = NULL;
				$dados = $_POST;
				$dados['link'] = Funcoes::criaSlug($dados['nome']);
				$model->setDados($dados);
				
				$obrigatorios = array(
					"nome" => array(
						"nome" => "Nome"
					),
					"categoria" => array(
						"nome" => "Categoria"
					)
				);
				
				Funcoes::validaPost($obrigatorios, $dados);
				$model->salvar();
				CursoCategoria::atualizaQuantidades($conexao);

				if ($acao == 'cadastrar') {	
					$acao = 'cadastrado';
					$this->_afterCadastro($conexao, $model);
					$this->log->adicionar ('cadastrou', 'curso', $model->nome, 
						'Usuário cadastrou curso.');					
					$redirecionar = sprintf('?modulo=%s&acao=%s&id=%d', $this->modulo, 
						'cadastrar2', $model->id);
				}
				else {
					$acao = 'atualizado';
					$this->log->adicionar ('atualizou', 'curso', $model->nome, 
						'Usuário atualizou curso.');
					$redirecionar = sprintf('?modulo=%s', $this->modulo);
				}

				setMensagem('info', sprintf('Curso %s [%s]', $acao, $model->nome));
				
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
			setMensagem("error", 'Curso não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
	
		$conexao->disconnect();					
		$view = new View2($this->modulo, "extendido", "cadastrar2.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"objeto" => $model,
				'categorias' => $categorias
			)
		);
        $view->showContents();
	}

	public function imagensAction() {
		try {
			$this->checaPermissao('cursos', 'cadastrar');
			$conexao = $this->conexao->getConexao();
			
			$cursoModel = new Curso($conexao);
			$model = $cursoModel->getObjetoOrFail(getVariavel('id'));
			$thumbnailAntigo = $model->thumbnail;
			$bannerAntigo = $model->banner;

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Cursos' => '?modulo=' . $this->modulo,
				$model->nome => '?modulo=' . $this->modulo . '&acao=cadastrar' . $model->tipo . '&id=' . $model->id,
				'Imagens' => ''
			);
			
			if (count($_POST) > 0) {
				
				$redirecionar = NULL;
				$diretorio = DIR_UPLOADS . DS . 'cursos' . DS . $model->id;

				if (Funcoes::enviouArquivo($_FILES, 'banner')) {
					$model->banner = Funcoes::enviarArquivo($_FILES, $diretorio, 'banner');
					if (!empty($bannerAntigo)) {
						Funcoes::excluiArquivo($diretorio .  DS . $bannerAntigo);
						$this->log->adicionar ('mudou', 'banner', $model->banner, 
							sprintf('Usuário mudou banner do curso %s.', $model->nome));	
					}
					else {
						$this->log->adicionar ('cadastrou', 'banner', $model->banner, 
							sprintf('Usuário cadastrou banner no curso %s.', $model->nome));
					}
				}

				if (Funcoes::enviouArquivo($_FILES, 'thumbnail')) {
					$model->thumbnail = Funcoes::enviarArquivo($_FILES, $diretorio, 'thumbnail');
					if (!empty($thumbnailAntigo)) {
						Funcoes::excluiArquivo($diretorio .  DS . $thumbnailAntigo);
						$this->log->adicionar ('mudou', 'thumbnail', $model->thumbnail, 
							sprintf('Usuário cadastrou thumbnail do curso %s.', $model->nome));	
					}
					else {
						$this->log->adicionar ('cadastrou', 'thumbnail', $model->thumbnail, 
							sprintf('Usuário mudou thumbnail no curso %s.', $model->nome));
					}
				}

				$model->salvar();
				$redirecionar = sprintf('?modulo=%s&acao=%s&id=%d', $this->modulo, 
					'imagens', $model->id);
				setMensagem('info', 'Imagens salvas');
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
			setMensagem("error", 'Curso não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
	
		$conexao->disconnect();					
		$view = new View2($this->modulo, "extendido", "imagens.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"objeto" => $model
			)
		);
        $view->showContents();
	}
	
	public function excluir1Action() {
		try {
			// exclui cursos
			$this->checaPermissao('cursos', 'excluir');
			$conexao = $this->conexao->getConexao();
			$model = new Curso($conexao);
			$preinscricaoModel = new Preinscricao($conexao);
			$curso = $model->getObjetoOrFail(getVariavel('id'));

			if ($preinscricaoModel->getQuery()
				->where('idCurso = ?', (int) $curso->id)
				->count() > 0) {
				throw new Exception ('Este curso tem inscrições e não pode ser excluído');
			}
			
			$affectedRows = $curso->excluir();
			if ($affectedRows) {
				$this->log->adicionar ('excluiu', 'curso', $model->nome, 
					'Usuário excluiu curso.');
				$conexao->commit();					
				setMensagem('info', 'Curso excluído [' . $model->nome . ']');
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
			setMensagem("error", 'Curso não encontrado');
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

	public function excluir2Action() {
		try {
			// exclui banners
			$this->checaPermissao('cursos', 'excluir');
			$conexao = $this->conexao->getConexao();
			$model = new Curso($conexao);
			$obj = $model->getObjetoOrFail(getVariavel('id'));
			
			$diretorio = DIR_UPLOADS . DS . "cursos";
			$diretorio .= DS . $obj->id . DS;
			$diretorio .= $obj->banner;
			
			if (!existeArquivo($diretorio)) {
				throw new Exception("Banner não encontrado");
			}

			if (excluiArquivo($diretorio)) {
				$this->log->adicionar ("excluiu", "banner", $obj->banner, 
					sprintf("Usuário excluiu banner do curso %s", $obj->nome));	
				$obj->banner = NULL;
				$obj->salvar();
				$conexao->commit();	
				setMensagem("info", "Banner excluído");
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

	public function excluir3Action() {
		try {
			// exclui thumbnails
			$this->checaPermissao('cursos', 'excluir');
			$conexao = $this->conexao->getConexao();
			$model = new Curso($conexao);
			$obj = $model->getObjetoOrFail(getVariavel('id'));
			
			$diretorio = DIR_UPLOADS . DS . "cursos";
			$diretorio .= DS . $obj->id . DS;
			$diretorio .= $obj->thumbnail;
			
			if (!existeArquivo($diretorio)) {
				throw new Exception("Thumbnail não encontrado");
			}

			if (excluiArquivo($diretorio)) {
				$this->log->adicionar ("excluiu", "thumbnail", $obj->thumbnail, 
					sprintf("Usuário excluiu thumbnail do curso %s", $obj->nome));	
				$obj->thumbnail = NULL;
				$obj->salvar();
				$conexao->commit();	
				setMensagem("info", "Thumbnail excluído");
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

	public function tipoAction() {
		try {
			$this->checaPermissao('cursos', 'cadastrar');
			$conexao = $this->conexao->getConexao();
			
			$cursoModel = new Curso($conexao);
			$model = $cursoModel->getObjetoOrFail(getVariavel('id'));

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Cursos' => '?modulo=' . $this->modulo,
				'Mudar tipo' => ''
			);
			
			if (count($_POST) > 0) {
				
				$redirecionar = NULL;
				
				$obrigatorios = array(
					"tipo" => array(
						"nome" => "Tipo"
					)
				);

				Funcoes::validaPost($obrigatorios, $_POST);

				$model->tipo = $_POST['tipo'];
				$model->salvar();

				$this->log->adicionar ('atualizou', 'curso', $model->nome, 
					sprintf('Usuário mudou tipo do curso %s.', $model->nome));					
				$redirecionar = sprintf('?modulo=%s&acao=%s&id=%d', $this->modulo, 
					'tipo', $model->id);

				setMensagem('info', sprintf('Curso atualizado [%s]', $model->nome));
				
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
			setMensagem("error", 'Curso não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
	
		$conexao->disconnect();					
		$view = new View2($this->modulo, "extendido", "mudar.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"objeto" => $model
			)
		);
        $view->showContents();
	}

	public function acoesAction() {
		try {
			$this->checaPermissao('cursos', "opcoes");
			$conexao = $this->conexao->getConexao();
			$processados = 0;
			$naoProcessados = 0;
			
			if (empty($_POST["acoes"])) {
				throw new Exception("É necessário escolher uma ação");
			}
			
			$cursoModel = new Curso($conexao);
			$objetos = isset($_POST["objetos"]) ? $_POST["objetos"] : array();
			
			// retira o elemento -1, caso exista
			if (count($objetos) > 0 && $objetos[0] == -1) {
				array_shift($objetos);
			}
			
			foreach ($objetos as $id) {

				$model = $cursoModel->getObjeto($id);

				switch ($_POST['acoes']) {

					case 'ativar' :
						$opcao = "ativado(s)";	
						$model->status = 1;
						if ($model->salvar()) {
							$processados += 1;
							$this->log->adicionar ('ativou', 'curso', $model->nome, 
								"Usuário ativou curso através do recurso de aplicar ações em massa.");
						}
					break;

					case 'desativar' :
						$opcao = "desativado(s)";	
						$model->status = 0;
						if ($model->salvar()) {
							$processados += 1;
							$this->log->adicionar ('desativou', 'curso', $model->nome, 
								"Usuário desativou curso através do recurso de aplicar ações em massa.");
						}
					break;
				
					case 'excluir' :
						$opcao = "excluído(s)";	
						if ($model->excluir()) {
							$processados += 1;
							$this->log->adicionar ('excluiu', 'curso', $model->nome, 
								"Usuário excluiu curso através do recurso de aplicar ações em massa.");
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
			CursoCategoria::atualizaQuantidades($conexao);
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
			$model = new VwCurso($conexao);

			$dados = isset($_POST['dados']) ? $_POST['dados'] : array();
			$qs = isset($_POST['qs']) ? $_POST['qs'] : array();

			$modulo = $dados['modulo'];
			$quantidadePorPagina = isset($dados['exibir'])  ? (int) $dados['exibir'] : QUANTIDADE_POR_PAGINA;
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

	public function informacoesDoc1Action() {

		try {
			$this->checaPermissao('cursos', 'informacoesDoc1');
			$conexao = $this->conexao->getConexao();
			
			$cursoModel = new Curso($conexao);
			$cidadeModel = new VwCidadeCurso($conexao);
			$cursos = $cursoModel->getObjetos(array(
					'order' => array(
						'nome' => 'asc'
					)
				)
			);

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Cursos" => "?modulo=cursos",
				"Informações sobre os cursos" => ""
			);

			if (count($_POST) > 0) {

				$obrigatorios = array(
					"curso" => array(
						"nome" => "Curso"
					)
				);

				Funcoes::validaPost($obrigatorios, $_POST);
				$model = $cursoModel->getObjetoOrFail($_POST['curso']);

				$nome = "Informativo-" . implode("-", explode(" ", trim($model->nome)));
				$nomeArquivo = $nome . ".doc";

				$PHPWord = new PHPWord();

				$PHPWord->addFontStyle("negrito",
					array(
						"bold" => true
					)
				);

				$PHPWord->addFontStyle("normal",
					array(
						"bold" => false
					)
				);

				$PHPWord->addParagraphStyle("pLeft",
					array(
						"align" => "left",
						"spaceBefore" => 0,
						"spaceAfter" => 0,
						"spacing" => 0
					)
				);

				$PHPWord->addParagraphStyle("pJustify",
					array(
						"align" => "both",
						"spaceBefore" => 100,
						"spaceAfter" => 100,
						"spacing" => 100
						)
					);

				$PHPWord->addParagraphStyle("pSpaced",
					array("align" => "both",
						"spaceBefore" => 50,
						"spaceAfter" => 50,
						"spacing" => 50
						)
					);

				$PHPWord->setDefaultFontName("Arial");
				$PHPWord->setDefaultFontSize(11);

				$sectionStyle = array(
					"orientation" => NULL,
					"marginLeft" => 900,
					"marginRight" => 900,
					"marginTop" => 300,
					"marginBottom" => 300
				);

				$section = $PHPWord->createSection($sectionStyle);

				// header (tabela com as logomarcas)
				$table = $section->addTable();
				$table->addRow();
				$cellStyle = array(
					"valign"=> "center"
				);

				$imageStyleIefap = array(
					"width"=>261,
					"height"=>75,
					"align"=>"left"
				);

				$imageStylePosFip = array(
					"width"=>200,
					"height"=>90,
					"align"=>"right"
				);

				$imageStyleNassau = array(
					"width"=>200,
					"height"=>90,
					"align"=>"right"
				);

				$cell = $table->addCell(5000, $cellStyle);

				$cell->addImage("imagens/logo-iefap-fundo-branco-small.gif", $imageStyleIefap);

				if ($model->unidadeCertificadora == Curso::CURSO_UNIDADE_CERTIFICADORA_FIP) {
					$cell = $table->addCell(5000, $cellStyle);
					$cell->addImage("imagens/logo-informacoes-cursos-fip.gif", $imageStylePosFip);
				}
				else if ($model->unidadeCertificadora == Curso::CURSO_UNIDADE_CERTIFICADORA_NASSAU) {
					$cell = $table->addCell(5000, $cellStyle);
					$cell->addImage("imagens/logo-informacoes-cursos-nassau.gif", $imageStyleNassau);
				}
				else if ($model->unidadeCertificadora == Curso::CURSO_UNIDADE_CERTIFICADORA_UNINGA) {
					$cell = $table->addCell(5000, $cellStyle);
					$cell->addImage("imagens/logo-informacoes-formulario-uninga.gif", $imageStyleNassau);
				}

				// nome do curso
				$textrun = $section->createTextRun();
				$textrun->addText("Curso: ", "negrito");
				$textrun->addText(utf8_decode(decodificaDado($model->nome)));

				// público alvo
				$publicoAlvo = utf8_decode(html_entity_decode(strip_tags(trim($model->publicoAlvo)), ENT_NOQUOTES, "utf-8"));
				$section->addText(utf8_decode("Público alvo: "), "negrito", "pLeft");
				$section->addText($publicoAlvo, "normal", "pJustify");

				// objetivos gerais
				$objetivosGerais = utf8_decode(html_entity_decode(strip_tags(trim($model->objetivosGerais)), ENT_NOQUOTES, "utf-8"));
				$section->addText(utf8_decode("Objetivos gerais: "), "negrito", "pLeft");
				$section->addText($objetivosGerais, "normal", "pJustify");

				// objetivos específicos
				$objetivosEspecificos = $this->_trataLista($model->objetivosEspecificos);
				$section->addText(utf8_decode("Objetivos específicos: "), "negrito", "pLeft");
				if (is_array($objetivosEspecificos)) {
					$objetivosEspecificos = implode(" ", $objetivosEspecificos);
				}
				else {
					$objetivosEspecificos = utf8_decode(html_entity_decode($objetivosEspecificos, ENT_NOQUOTES, "utf-8"));
				}
				$section->addText($objetivosEspecificos, "normal", "pJustify");

				// DISCIPLINAS

				// Unidades disponíveis (cidades)
				$unidadesDisponiveis = array();
				// retorna as cidades do curso
				$cidades = $cidadeModel->getObjetos(array(
						"where" => array(
							"curso" => $model->id
						)
					)
				);
				foreach($cidades as $cidade){
					$unidadesDisponiveis[] = utf8_decode(decodificaDado($cidade->nomeCidade));
				}

				$section->addText(utf8_decode("Unidades disponíveis: "), "negrito", "pLeft");
				$section->addText(implode(", ", $unidadesDisponiveis), "normal", "pJustify");

				// Investimento
				$section->addText(utf8_decode("Investimento: "), "negrito",
					array(
						"align" => "left",
						"spaceBefore" => 100,
						"spaceAfter" => 200,
						"spacing" => 0
					)
				);
				$table = $section->addTable();
				$table->addRow();
				$cellStyle = array("valign" => "bottom");
				$cell = $table->addCell(4000, $cellStyle);
				$cell->addText("", "negrito");
				$cell = $table->addCell(3000, $cellStyle);
				$cell->addText(utf8_decode("VALOR"), "negrito");
				$cell = $table->addCell(3000, $cellStyle);
				$cell->addText("VALOR TOTAL", "negrito");

				$cellStyle = array(
					"valign" => "center"
				);

				foreach ($cidades as $cidade) {
					$parcelas = explode(",", $cidade->quantidadeParcelas);
					foreach ($parcelas as $parcela) {
						$parcela = trim($parcela);
						// valor total do curso
						$valorTotal = Funcoes::converteDecimal($cidade->valorCurso);
						// valor do curso sem desconto
						$valorSemDesconto = $valorTotal;
						if ($valorSemDesconto != 0) {
							// quantidade de parcelas
							$table->addRow();
							$cell = $table->addCell(4000, $cellStyle);
							$cell->addText(utf8_decode(decodificaDado($cidade->nomeCidade)), "negrito");
							//$cell = $table->addCell(2000, $cellStyle);
							$cell = $table->addCell(2000, $cellStyle);

							$semDesconto = $valorSemDesconto / $parcela;
							$semDesconto = sprintf('%0.2f', $semDesconto);
							$semDesconto = number_format($semDesconto, 2, ',', '');

							$cell->addText($parcela . " x R$ " . $semDesconto, "normal");
							$cell = $table->addCell(3000, $cellStyle);
							$valorTotal = Funcoes::moneyFormat($valorTotal);
							$cell->addText("R$ " . $valorTotal, "normal");
						}
						
					}
				}

				// horários das aulas
				$horariosAulas = "";
				$section->addText(utf8_decode("Horários das aulas: "), "negrito",
					array(
						"align" => "left",
						"spaceBefore" => 300,
						"spaceAfter" => 100,
						"spacing" => 0
					)
				);
				$section->addText($horariosAulas, "normal", "pJustify");

				// documentos necessários para a efetivação da matrícula
				$section->addText(utf8_decode("Documentos necessários para a efetivação da matrícula: "), "negrito",
					array(
						"align" => "left",
						"spaceBefore" => 100,
						"spaceAfter" => 100,
						"spacing" => 0
					)
				);

				$section->addText(utf8_decode("Duas (2) cópias do Currículum Vitae"), "normal", "pSpaced");
				$section->addText(utf8_decode("Duas (2) fotos 3x4 recentes"), "normal", "pSpaced");
				$section->addText(utf8_decode("Duas (2) cópias do RG e do CPF (não pode ser CNH)"), "normal", "pSpaced");
				$section->addText(utf8_decode("Duas (2) cópias autenticadas do diploma de Graduação ou Declaração Provisória (em caso de diploma do Exterior, cópia da convalidação do mesmo no Brasil)"), "normal", "pSpaced");
				$section->addText(utf8_decode("Duas (2) cópias da Certidão de Nascimento e/ou Casamento"), "normal", "pSpaced");
				$section->addText(utf8_decode("Duas (2) cópias do Comprovante de Residência (máximo 90 dias)"), "normal", "pSpaced");
				$section->addText(utf8_decode("Duas (2) vias do Contrato de Prestação de Serviços Educacionais (será entregue e assinado na aula inaugural)"), "normal", "pSpaced");

				$section->addText(utf8_decode("Obs:"),
					array(
						"bold" => true,
						"size" => 9,
						"color" => "red"
					),
					array(
						"align" => "left",
						"spaceBefore" => 100,
						"spaceAfter" => 0,
						"spacing" => 0
					)
				);

				$section->addText(utf8_decode("A confirmação do inicio do curso depende do numero mínimo de alunos necessários para formar a turma (25 alunos). Se não atingirmos o numero mínimo de alunos a data de inicio de curso pode ser adiada ou o curso pode ser cancelado. Se o curso for cancelado, garantimos a devolução de 100% do valor da taxa de matrícula."),
					array(
						"bold" => false,
						"size" => 8,
						"color" => "red"
					),
					"pJustify"
				);

				$footer = $section->createFooter();
				$footer->addText(utf8_decode("IEFAP Instituto de Ensino, Formação e Aperfeiçoamento em Pós-Graduação | www.iefap.com.br"),
					array(
						"bold" => true,
						"size" => 8,
					),
					array(
						"align" => "right",
						"spacing" => 30
					)
				);
				$footer->addText(utf8_decode("Rua Felicíssimo de Azevedo, 53  Sala 305 | 90540-110 | Telefone 51 8124-0086 | Porto Alegre | RS."),
					array(
						"bold" => false,
						"size" => 8,
					),
					array(
						"align" => "right",
						"spacing" => 30
					)
				);
				$footer->addText(utf8_decode("Av Adv. Horácio Raccanello Filho, 5415, sala 01 | 87020-035 | Telefone 43 3123-6000 | Maringá | PR."),
					array(
						"bold" => false,
						"size" => 8,
					),
					array(
						"align" => "right",
						"spacing" => 30
					)
				);
				$footer->addText(utf8_decode("Trav. Mauriti 1771 A, entre a Marquês de Herval e Visconde de Inhaúma, Pedreira| 66.087-680 | Fone 91 3266-3100 | Belém | PA."),
					array(
						"bold" => false,
						"size" => 8,
					),
					array(
						"align" => "right",
						"spacing" => 30
					)
				);

				$objWriter = PHPWord_IOFactory::createWriter($PHPWord, "Word2007");
				$path = DIR_ROOT . DS . "temp" . DS . $nomeArquivo;
				$objWriter->save($path);
				header("Content-Description: File Transfer");
				header("Content-Type: application/octet-stream");
				header('Content-Disposition: attachment; filename="'.basename($path).'"');
				header("Content-Transfer-Encoding: binary");
				header("Expires: 0");
				header("Cache-Control: must-revalidate");
				header("Pragma: public");
				header("Content-Length: " . filesize($path));
				//ob_clean();
				flush();
				readfile($path);
				unlink($path);
				exit;

			}
		}
		catch (PermissaoException $e) {
			$this->conexao->getConexao()->disconnect();
			setMensagem("error", $e->getMessage());
			Application::redirect("index.php");
			exit;
		}
		catch (Exception $e) {
			setMensagem("error", $e->getMessage());
			if (isset($redirecionar)) {
				Application::redirect($redirecionar);
				exit;
			}
		}

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", "informativo1.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"cursos" => $cursos
			)
		);
		$view->showContents();
	}

	public function informacoesDoc2Action() {
		try {
			$this->checaPermissao('cursos', 'informacoesDoc2');
			$conexao = $this->conexao->getConexao();

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Cursos" => "?modulo=cursos",
				"Informações sobre os cursos" => ""
			);

			$cursoModel = new Curso ($conexao);
			$cidadeModel = new Cidade ($conexao);
			$cursos = $cursoModel->getObjetos(array(
					'order' => array(
						'nome' => 'asc'
					)
				)
			);

			$idCurso = $idCidade = 0;

			if (count($_POST) > 0) {

				$dados = $_POST;
				$idCurso = $dados["curso"];
				$idCidade = $dados["cidade"] = isset($dados["cidade"]) ? (int) $dados["cidade"] : NULL;

				$obrigatorios = array(
					"curso" => array(
						"nome" => "Curso"
					),
					"cidade" => array(
						"nome" => "Cidade"
					)
				);

				Funcoes::validaPost($obrigatorios, $dados);
				$curso = $cursoModel->getObjetoOrFail($dados['curso']);
				$cidade = $cidadeModel->getObjetoOrFail($dados['cidade']);

				$vwCidadeModel = new VwCidadeCurso($conexao);
				$cidadeCurso = $vwCidadeModel->getOrFail(array(
						'where' => array(
							'curso' => $curso->id,
							'cidade' => $cidade->id
						)
					)
				);

				$nome = "Informativo-" . implode("-", explode(" ",trim($curso->nome)));
				$nomeArquivo = $nome . ".doc";

				$PHPWord = new PHPWord();

				$PHPWord->addFontStyle("negrito",
					array(
						"bold" => true
					)
				);

				$PHPWord->addFontStyle("small",
					array(
						"bold" => false,
						"size" => 8,
						"color" => "red"
					)
				);

				$PHPWord->addFontStyle("normal",
					array(
						"bold" => false
					)
				);

				$PHPWord->addParagraphStyle("pLeft",
					array(
						"align" => "left",
						"spaceBefore" => 0,
						"spaceAfter" => 0,
						"spacing" => 0
					)
				);

				$PHPWord->addParagraphStyle("pJustify",
					array(
						"align" => "both",
						"spaceBefore" => 100,
						"spaceAfter" => 100,
						"spacing" => 100
						)
					);

				$PHPWord->addParagraphStyle("pSpaced",
					array("align" => "both",
						"spaceBefore" => 50,
						"spaceAfter" => 50,
						"spacing" => 50
						)
					);

				$PHPWord->setDefaultFontName("Arial");
				$PHPWord->setDefaultFontSize(11);

				$sectionStyle = array(
					"orientation" => NULL,
					"marginLeft" => 900,
					"marginRight" => 900,
					"marginTop" => 300,
					"marginBottom" => 10
				);

				$section = $PHPWord->createSection($sectionStyle);

				//$textrun = $section->createTextRun();
				$section->addText(utf8_decode($curso->nome . " - " . $cidade->nome . "/" . $cidadeCurso->siglaEstado), "negrito");
				$section->addText(utf8_decode("Prezado(a) *********, boa tarde!"));
				$section->addText(utf8_decode("Agradecemos seu contato!"));
				$section->addText(utf8_decode("Seguem as informações solicitadas sobre o Curso de Pós-Graduação Lato Sensu em " . $curso->nome . " que está com matrículas abertas na cidade de " . $cidade->nome . "/" . $cidadeCurso->siglaEstado . ":"), "normal", "pJustify");

				$section->addText(utf8_decode($curso->nome), "negrito");

				$textrun = $section->createTextRun();
				$textrun->addText(utf8_decode("Instituição de Ensino Superior: "), "negrito");
				$textrun->addText(utf8_decode(Curso::getCertificadora($curso->unidadeCertificadora)));

				// se tiver carga horária definida
				if ((int) $cidadeCurso->cargaHoraria > 0) {
					$textrun = $section->createTextRun();
					$textrun->addText(utf8_decode("Carga horária: "), "negrito");
					$textrun->addText($cidadeCurso->cargaHoraria . " horas");
				}

				$section->addText(utf8_decode("Público Alvo:"), "negrito");
				$section->addText(utf8_decode(decodificaDado(strip_tags($curso->publicoAlvo))), "normal", "pJustify");

				if (strlen(strip_tags(trim($curso->objetivosGerais))) > 0) {
					$section->addText(utf8_decode("Objetivos gerais:"), "negrito");
					$section->addText(utf8_decode(decodificaDado(strip_tags($curso->objetivosGerais))), "normal", "pJustify");
				}

				if (strlen(strip_tags(trim($curso->objetivosEspecificos))) > 0) {
					$section->addText(utf8_decode("Objetivos específicos:"), "negrito");
					$section->addText(utf8_decode(decodificaDado(strip_tags($curso->objetivosEspecificos))), "normal", "pJustify");
				}

				if (strlen(strip_tags(trim($curso->disciplinas))) > 0) {
					$section->addText(utf8_decode("Disciplinas:"), "negrito");
					$section->addText(utf8_decode(decodificaDado(strip_tags($curso->disciplinas))), "normal", "pJustify");
				}

				$PHPWord->addParagraphStyle('pInline', array(
						'align' => 'both',
						'spaceBefore' => 10,
						'spaceAfter' => 10,
						'spacing' => 10
					)
				);

				$textrun = $section->createTextRun();
				$textrun->addText(utf8_decode("Investimento: "), "negrito");
				$textrun->addText(utf8_decode("(Válido somente para ingresso até a primeira aula - Plano 1)"));

				$valorTotal = Funcoes::converteDecimal($cidadeCurso->valorCurso);
				$valorSemDesconto = $valorTotal;

				$formasPagamento = array();
				$parcelas = explode(",", $cidadeCurso->quantidadeParcelas);
				foreach ($parcelas as $parcela) {
					$parcela = (int) $parcela;
					if ($parcela != 0) {
						$semDesconto = $valorSemDesconto / $parcela;
						$semDesconto = sprintf('%0.2f', $semDesconto);
						$semDesconto = Funcoes::moneyFormat($semDesconto);
						$formasPagamento[] = $parcela . ' parcelas de R$ ' . $semDesconto;
					}
				}

				$section->addText(utf8_decode("Taxa de inscrição: R$ " . $cidadeCurso->valorInscricao), "normal", "pInline");
				$section->addText(utf8_decode("Parcelamento: " . implode(" ou ", $formasPagamento)), "normal", "pInline");

				$PHPWord->addParagraphStyle('listStyle', array(
						'spaceAfter' => 90
					)
				);

				$section->addText(utf8_decode("Descontos:"), "negrito", array(
						'spaceBefore' => 250,
						'spaceAfter' => 0,
						'spacing' => 150
					)
				);
				$section->addListItem(utf8_decode('Ex-aluno do IEFAP: 20%'), 0, null, null, 'listStyle');
				$section->addListItem(utf8_decode('Ex-aluno das Faculdades parceiras: 15%'), 0, null, null, 'listStyle');
				$section->addListItem(utf8_decode('Matrícula em dois cursos simultâneos: 15%'), 0, null, null, 'listStyle');
				$section->addListItem(utf8_decode('Quitação do Curso: Até 20%'), 0, null, null, 'listStyle');
				$section->addListItem(utf8_decode('Indicação de Alunos: 50% da parcela'), 0, null, null, 'listStyle');
				$section->addListItem(utf8_decode('Incentivo à Família: 15%'), 0, null, null, 'listStyle');
				$section->addListItem(utf8_decode('Incentivo ao Funcionário Público (Municipal, estadual ou federal: 15%)'), 0, null, null, 'listStyle');
				$section->addListItem(utf8_decode('Convênios: até 20%'), 0, null, null, 'listStyle');
				$section->addListItem(utf8_decode('Consultar condições referentes a cada desconto'), 0, null, null, array("spaceAfter" => 270));

				$section->addText(utf8_decode("Documentos necessários para a efetivação da matrícula:"), "negrito");
				$section->addListItem(utf8_decode('Duas (2) cópias do Currículo;'), 0, null, null, 'listStyle');
				$section->addListItem(utf8_decode('Duas (2) fotos 3x4 recentes'), 0, null, null, 'listStyle');
				$section->addListItem(utf8_decode('Duas (2) cópias do RG e do CPF (não pode ser CNH)'), 0, null, null, 'listStyle');
				$section->addListItem(utf8_decode('Duas (2) cópias Histórico Escolar da Graduação'), 0, null, null, 'listStyle');
				$section->addListItem(utf8_decode('Duas (2) cópias autenticadas do diploma de Graduação ou Declaração Provisória (em caso de diploma do Exterior, cópia da convalidação do mesmo no Brasil)'), 0, null, null, 'listStyle');
				$section->addListItem(utf8_decode('Duas (2) cópias da Certidão de Nascimento e/ou Casamento'), 0, null, null, 'listStyle');
				$section->addListItem(utf8_decode('Duas (2) cópias do Comprovante de Residência (máximo 90 dias)'), 0, null, null, 'listStyle');
				$section->addListItem(utf8_decode('Duas (2) vias da Ficha de Inscrição (assinadas no ato da matrícula)'), 0, null, null, 'listStyle');
				$section->addListItem(utf8_decode('Três(3) vias do Contrato de Prestação de Serviços Educacionais (assinadas no ato da matrícula)'), 0, null, null, array("spaceAfter" => 270));

				$textrun = $section->createTextRun();
				$textrun->addText(utf8_decode("Número mínimo de alunos para a abertura da turma: "), "negrito");
				$textrun->addText(utf8_decode("25 alunos *"));
				$section->addText(utf8_decode("* A confirmação de início do curso depende do número mínimo de alunos inscritos, com a assinatura do contrato e pagamento da taxa de inscrição. Não atingindo o número mínimo de alunos até a data prevista, poderá ser postergado para a uma segunda data. Permanecendo sem o mínimo de alunos para início do curso, será cancelada a turma e as taxas de inscrição serão restituídas de forma integral."), "small", "pJustify");


				$PHPWord->addFontStyle("footerFont",
					array(
						"bold" => true,
						"size" => 8
					)
				);

				$PHPWord->addParagraphStyle("footerParagraph",
					array(
						"align" => "right",
						"spaceBefore" => 50,
						"spaceAfter" => 50,
						"spacing" => 50
					)
				);

				$footer = $section->createFooter();
				$footer->addText(utf8_decode("IEFAP Instituto de Ensino, Formação e Aperfeiçoamento em Pós-Graduação | www.iefap.com.br"), "footerFont", "footerParagraph");
				$footer->addText(utf8_decode("Rua Felicíssimo de Azevedo, 53, Sala 305 | 90540-110 | Telefone 51 8124-0086 | Porto Alegre | RS"), "footerFont", "footerParagraph");
				$footer->addText(utf8_decode("Av Adv. Horácio Raccanello Filho, 5415, Sala 01 | 87020-035 | Telefone 43 3123-6000 | Maringá | PR"), "footerFont", "footerParagraph");
				$footer->addText(utf8_decode("Trav. Mauriti 1771 A, entre a Marquês de Herval e Visconde de Inhaúma, Pedreira | 66.087-680 | Fone 91 3266-3100 | Belém | PA"), "footerFont", "footerParagraph");
				//$footer->addImage("../imagens/bg-bottom-word.jpg");

				$objWriter = PHPWord_IOFactory::createWriter($PHPWord, "Word2007");
				$path = DIR_ROOT . DS . "temp" . DS . $nomeArquivo;
				$objWriter->save($path);
				header("Content-Description: File Transfer");
				header("Content-Type: application/octet-stream");
				header('Content-Disposition: attachment; filename="' . basename($path) . '"');
				header("Content-Transfer-Encoding: binary");
				header("Expires: 0");
				header("Cache-Control: must-revalidate");
				header("Pragma: public");
				header("Content-Length: " . filesize($path));
				//ob_clean();
				flush();
				readfile($path);
				unlink($path);
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
			setMensagem("error", 'Curso não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			setMensagem("error", $e->getMessage());
		}

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", "informativo2.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"cursos" => $cursos,
				"idCidade" => $idCidade,
				"idCurso" => $idCurso
			)
		);
		$view->showContents();
	}

	public function informacoesPdfAction() {

		try {
			$this->checaPermissao('cursos', 'informacoesPdf');
			$conexao = $this->conexao->getConexao();

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Cursos" => "?modulo=cursos",
				"Informações sobre os cursos" => ""
			);

			$cursoModel = new Curso($conexao);
			$cidadeModel = new VwCidadeCurso($conexao);
			$cursos = $cursoModel->getObjetos(array(
					'order' => array(
						'nome' => 'asc'
					)
				)
			);

			if (count($_POST) > 0) {

				$obrigatorios = array(
					"curso" => array(
						"nome" => "Curso"
					)
				);

				Funcoes::validaPost($obrigatorios, $_POST);
				$curso = $cursoModel->getObjetoOrFail($_POST['curso']);

				$nome = "Informacoes-" . implode("-", explode(" ", trim($curso->nome)));
				$nomeArquivo = $nome . ".pdf";

			    // create an API client instance
			    $client = new Pdfcrowd("saulor", "8b813720f93ddde06b30873886785084");
			    // convert a web page and store the generated PDF into a $pdf variable
			    $pdf = $client->convertURI('http://site.iefap.com.br/informativo/' . $curso->link);
			    // set HTTP response headers
			    header("Content-Type: application/pdf");
			    header("Cache-Control: max-age=0");
			    header("Accept-Ranges: none");
			    header("Content-Disposition: attachment; filename=\"" . $nomeArquivo . "\"");
			    // send the generated PDF
			    echo $pdf;
			}
		}
		catch (PermissaoException $e) {
			$this->conexao->getConexao()->disconnect();
			setMensagem("error", $e->getMessage());
			Application::redirect("index.php");
			exit;
		}
		catch (Exception $e) {
			setMensagem("error", $e->getMessage());
		}

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", "informativo1.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"cursos" => $cursos
			)
		);
		$view->showContents();
	}

	private function _afterCadastro ($conexao, $curso) {
		try {

			$estadosModel = new Estado($conexao);

			// cadastra os estados
			$estados = $estadosModel->getObjetos();
			foreach ($estados as $estado) {
				$obj = new CursoEstado($conexao);
				$obj->estado = $estado->id;
				$obj->curso = $curso->id;
				$obj->salvar();
			}

			// coloca o curso na lista de cursos do usuário que cadastrou o curso
			$usuarioCurso = new UsuarioCurso($conexao);
			$usuarioCurso->usuario = $_SESSION[PREFIX . 'loginId'];
			$usuarioCurso->curso = $curso->id;
			$usuarioCurso->salvar();

			$_SESSION[PREFIX . "unidades"][] = $curso->id;
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	private function _trataLista($texto) {

		$frases = array();
		$texto = trim(strip_tags(trim($texto), "<ul><li>"));

		if (strpos($texto, "<ul>") !== false) {
			$texto = trim(substr($texto, strpos($texto, "<ul>") + 4, strpos($texto, "</ul>") - 4));
			while (strpos($texto, "<li") !== false) {
				$posLi = strpos($texto, "<li");
				$posBarraLi = strpos($texto, "</li");
				$frase = trim(substr($texto, $posLi + 4, $posBarraLi - 4));
				$frase = utf8_decode(html_entity_decode($frase, ENT_NOQUOTES, "utf-8"));
				$texto = trim(substr($texto, $posBarraLi + 5, strlen($texto)));
				$frases[] = $frase;
			}
		}

		if (count($frases) > 0) {
			return $frases;
		}
		else {
			return $texto;
		}

	}
	
}

?>