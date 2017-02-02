<?php

class CategoriasController extends Controller {

	protected $info = array(
		"tabela" => "cursos_categorias",
		"modulo" => "categorias",
		"labelSing" => "Categoria",
		"labelPlur" => "Categorias"
	);

	public function CategoriasController () {
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

	public function indexAction () {

		try {
			//$this->checaPermissao('categorias', 'index');
			$conexao = $this->conexao->getConexao();

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Cursos" => "?modulo=cursos",
				"Categorias" => ""
			);

			$order = array(
				"field" => "data",
				"ord" => "desc"
			);

			if (isset($_GET['order'])) {
				$order = array(
					"field" => $_GET['order'],
					"ord" => "asc"
				);
			}

			$categorias = CursoCategoria::listaCategorias($conexao->query()
				->from("cursos_categorias")
				->all());

		}
		catch (PermissaoException $e) {
			setMensagem("error", $e->getMessage());
			Application::redirect("index.php");
			exit;
		}
		catch (Exception $e) {
			setMensagem("error", $e->getMessage());
		}

		$view = new View2($_GET['modulo'], "extendido", "index.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"categorias" => $categorias,
				"breadcrumbs" => $breadcrumbs
			)
		);
        $view->showContents();
	}

	public function cadastrarAction() {

		try {
			//$this->checaPermissao('cursos', 'cadastrar');

			$conexao = $this->conexao->getConexao();

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Cursos" => "?modulo=cursos",
				"Categorias" => "?modulo=categorias"
			);

			$categoriasModel = new CursoCategoria ($conexao);
			$categorias = $categoriasModel->listaPais();

			if (isset($_GET['id'])) {
				$model = $categoriasModel->getObjetoOrFail(getVariavel("id"));
				$oldPai = $model->pai;
				$oldCaminho = $model->caminho;
				$imagemAntiga = $model->imagem;
				$bannerAntigo = $model->banner;
				$acao = "editar";
				$breadcrumbs[] = array(
					"Atualizar" => ""
				);
			}
			else {
				$model = new CursoCategoria ($conexao);
				$acao = "nova";
				$breadcrumbs[] = array(
					"Nova" => ""
				);
			}

			if (count($_POST) > 0) {

				$dados = $_POST;
				$dados['slug'] = Funcoes::criaSlug($dados['nome']);

				$validator = new MyGump();

		    	$rules = array(
					'nome' => 'required',
		    	);

				$filters = array(
					'id' => 'integer',
					'visivel' => 'integer',
					'pai' => 'integer',
				    'nome' => 'trim|sanitize_string',
					'descricao' => 'trim|sanitize_string'
				);

				try {

					$validated = $validator->validate($dados, $rules);

					if($validated !== TRUE) {
						throw new Exception(Funcoes::getValidationErrors($validated));
					}
					
					$dados = $validator->filter($dados, $filters);

					if ($dados['id'] == 0) {
						$dados['dataCadastro'] = time();
					}
					else {
						$dados['dataAtualizacao'] = time();
					}

					// salva
					$dados = $this->dao->salva($conexao, "cursos_categorias", $dados);

					// seta caminho, profundidade e imagem
					$dados['caminho'] = CursoCategoria::criaCaminho($conexao, $this->dao, $dados);
					$dados['profundidade'] = count(explode("/", $dados['caminho'])) - 1;
					
					if (Funcoes::enviouArquivo($_FILES, 'imagem')) {
						$diretorio = DIR_UPLOADS . DS . "categorias" . DS . $dados['id'];
						// exclui imagem antiga
						Funcoes::excluiArquivo($diretorio . DS . $imagemAntiga);
						$dados['imagem'] = Funcoes::enviarArquivo($_FILES, $diretorio);
					}

					if (Funcoes::enviouArquivo($_FILES, 'banner')) {
						$diretorio = DIR_UPLOADS . DS . "categorias" . DS . $dados['id'];
						// exclui imagem antiga
						Funcoes::excluiArquivo($diretorio . DS . $bannerAntigo);
						$dados['banner'] = Funcoes::enviarArquivo($_FILES, $diretorio, 'banner');
					}
					// atualiza
					$this->dao->salva($conexao, "cursos_categorias", $dados);

					if ($acao == "nova") {
						$this->log->adicionar ("cadastrou", "categoria", $dados['nome'], 
							"Usuário cadastrou categoria de curso");
						setMensagem("info", 'Categoria cadastrada [' . $dados['nome'] . ']');
						$redirecionar = "?modulo=categorias&acao=cadastrar";
					}
					else {
						CursoCategoria::atualizarSubcategorias($conexao, $this->dao, $oldCaminho, 
							$oldPai, $dados['pai']);
						$this->log->adicionar ("atualizou", "categoria", $dados['nome'], 
							"Usuário atualizou categoria de curso");
						setMensagem("info", 'Categoria atualizada [' . $dados['nome'] . ']');
						$redirecionar = "?modulo=categorias&acao=cadastrar&id=" . $dados['id'];
					}

					$conexao->commit();
					$conexao->disconnect();
					Application::redirect($redirecionar);
					exit;

				} catch (\Exception $e) {
					$conexao->rollback();
					setMensagem("error", $e->getMessage());
				}
				
			}
		}
		catch (PermissaoException $e) {
			$conexao->disconnect();
			setMensagem("error", $e->getMessage());
			Application::redirect("index.php");
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
			Application::redirect($redirecionar);
			exit;
		}

		//$this->conexao->getConexao()->disconnect();
		$view = new View2($_GET['modulo'], "extendido", "categorias.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"objeto" => $model,
				"categorias" => $categorias
			)
		);
		$view->showContents();

	}

	public function excluirAction () {

		try {
			//$this->checaPermissao('cursos', 'excluir');

			$conexao = $this->conexao->getConexao();
			$dados = $this->dao->findByPk($conexao, "cursos_categorias", getVariavel("id"));
			$affectedRows = $this->dao->excluiByPk($conexao, "cursos_categorias", $dados['id']);

			if ($affectedRows > 0) {
				$diretorio = DIR_UPLOADS . DS . "categorias" . DS . $dados['id'];
				excluiDiretorio($diretorio);
				$this->log->adicionar ("excluiu", $this->info['labelSing'], $dados['nome'], 
					"Usuário excluiu " . $this->info['labelSing'] . ".");
				$conexao->commit();
				setMensagem("info", $this->info['labelSing'] . " excluída [" . $dados['nome'] . "]");
			}

		}
		catch (PermissaoException $e) {
			$conexao->disconnect();
			setMensagem("error", $e->getMessage());
			Application::redirect("index.php");
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}

		$conexao->disconnect();
		Application::redirect("?modulo=categorias");
		exit;

	}

	public function removeriAction () {

		try {
			//$this->checaPermissao('cursos', 'excluir');

			$conexao = $this->conexao->getConexao();
			$dados = $this->dao->findByPk($conexao, "cursos_categorias", getVariavel("id"));
			$diretorio = DIR_UPLOADS . DS . "categorias" . DS . 
				$dados['id'] . DS . $dados['imagem'];
			if (Funcoes::excluiArquivo($diretorio)) {
				$dados['imagem'] = NULL;
				$this->dao->salva($conexao, "cursos_categorias", $dados);
				$this->log->adicionar ("excluiu", "imagem", "categoria", 
					"Usuário excluiu imagem da categoria " . $dados['nome'] . ".");
				$conexao->commit();
				setMensagem("info", "Imagem excluída");
			}

			$conexao->disconnect();
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;

		}
		catch (PermissaoException $e) {
			$conexao->disconnect();
			setMensagem("error", $e->getMessage());
			Application::redirect("index.php");
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}

		$conexao->disconnect();
		Application::redirect("?modulo=categorias");
		exit;

	}

	public function removerbAction () {

		try {
			//$this->checaPermissao('cursos', 'excluir');

			$conexao = $this->conexao->getConexao();
			$dados = $this->dao->findByPk($conexao, "cursos_categorias", getVariavel("id"));
			$diretorio = DIR_UPLOADS . DS . "categorias" . DS . 
				$dados['id'] . DS . $dados['banner'];
			if (Funcoes::excluiArquivo($diretorio)) {
				$dados['banner'] = NULL;
				$this->dao->salva($conexao, "cursos_categorias", $dados);
				$this->log->adicionar ("excluiu", "banner", "categoria", 
					"Usuário excluiu banner da categoria " . $dados['nome'] . ".");
				$conexao->commit();
				setMensagem("info", "Banner excluído");
			}

			$conexao->disconnect();
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;

		}
		catch (PermissaoException $e) {
			$conexao->disconnect();
			setMensagem("error", $e->getMessage());
			Application::redirect("index.php");
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}

		$conexao->disconnect();
		Application::redirect("?modulo=categorias");
		exit;

	}

}

?>
