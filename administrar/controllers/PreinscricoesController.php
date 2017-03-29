<?php

class PreinscricoesController extends Controller {

	protected $info = array(
		"tabela" => "preinscricoes",
		"modulo" => "preinscricoes",
		"labelSing" => "Inscrição",
		"labelPlur" => "Inscrições"
	);

	public function PreinscricoesController() {
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
			$conexao = $this->conexao->getConexao();
			$cursoModel = new Curso($conexao);
			$model = new Preinscricao($conexao);
			$quantidadePorPagina = isset($_GET["exibir"]) ? (int) $_GET["exibir"] : QUANTIDADE_POR_PAGINA;
			$quantidadePorPagina = ($quantidadePorPagina <= 0) ? QUANTIDADE_POR_PAGINA : $quantidadePorPagina;
			$pagina = isset($_GET["p"]) ? $_GET["p"] : 1;
			$pagina = $pagina <= 0 ? 1 : $pagina;
			$limit = $pagina == 1 ? $quantidadePorPagina : $quantidadePorPagina * ($pagina - 1);
			$offset = $pagina == 1 ? 0 : $quantidadePorPagina;

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Inscrições" => ""
			);

			// recupera cursos e monta tabela
			$query = $cursoModel->getQuery();
			$quantidade = $query->count();
			$objetos = $query
				->limitIn($limit, $offset)
				->order('nome', 'asc')
				->all();
			$cursos = $model->indexList($objetos);
		}
		catch (PermissaoException $e) {
			$this->conexao->getConexao();
			setMensagem("error", $e->getMessage());
			Application::redirect("index.php");
			exit;
		}
		catch (Exception $e) {
			setMensagem("error", $e->getMessage());
		}

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", "index.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"objetos" => $cursos,
				"quantidade" => $quantidade,
				"quantidadePorPagina" => $quantidadePorPagina,
				"pagina" => $pagina,
				"breadcrumbs" => $breadcrumbs
			)
		);
        $view->showContents();
	}

	public function visualizarAction() {

		try {
			$this->checaPermissao('preinscricoes', 'visualizar');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("id", "acao", "curso"));
			
			$cursoModel = new Curso($conexao);
			$usuarioModel = new Usuario($conexao);
			$inscricaoModel = new Preinscricao($conexao);
			$vwCidadeCurso = new VwCidadeCurso($conexao);

			$curso = $cursoModel->findOrFail(getVariavel('curso'));

			if ($curso["tipo"] == 0) {
				throw new Exception("Para visualizar as inscrições, o tipo do curso '" . $curso["nome"] . "' deve estar definido");
			}

			$usuarios = $usuarioModel->getQuery()
				->order('nome', 'asc')
				->where('status = 1')
				->all();

			$unidades = $vwCidadeCurso->getObjetos(array(
					'where' => array(
						'curso' => $curso['id'],
						'status' => 1
					)
				)
			);

			$breadcrumbs[] = array(
				"Inscrições" => "?modulo=preinscricoes",
				$curso["nome"] => ""
			);

			$tabs = $inscricaoModel->getTabs($curso);
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
			if ($redirecionar != NULL) {
				Application::redirect($redirecionar);
				exit;
			}
		}

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", "visualizar.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"curso" => $curso,
				"breadcrumbs" => $breadcrumbs,
				"tabs" => $tabs,
				"usuarios" => $usuarios,
				"unidades" => $unidades
			)
		);
        $view->showContents();

	}

	public function duplicarAction() {
		try {
			$this->checaPermissao('preinscricoes', 'duplicar');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("id", "acao"), array(
					"acao" => "visualizar"
				)
			);

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);
			$historicoModel = new HistoricoPreinscricao($conexao);

			$dados = $inscricaoModel->findOrFail(getVariavel('id'));
			$curso = $cursoModel->findOrFail(getVariavel('curso'));
			$cursos = $cursoModel->getQuery()
				->order('nome', 'asc')
				->all();

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Inscrições" => "?modulo=preinscricoes",
				$curso["nome"] => sprintf("?modulo=preinscricoes&acao=visualizar&curso=%d&tab1=%d", 
					$curso["id"], $_GET['tab1']),
				$dados["nome"] => sprintf("?modulo=preinscricoes&acao=cadastrar&curso=%d&tab1=%d&id=%d", 
					$curso["id"], $_GET['tab1'], $dados['id']),
				"Duplicar inscrição" => ""
			);

			if (count($_POST) > 0) {

				$dados = $_POST;

				$obrigatorios = array(
					"curso" => array(
						"tipo" => "select",
						"nome" => "Curso"
					)
				);

				$mensagem = validaPost($obrigatorios, $dados);
				if (!empty($mensagem)) {
					$redirecionar = NULL;
					throw new Exception($mensagem);
				}

				$dados = $old = $this->dao->findByPk($conexao, "preinscricoes", (int) $dados["id"]);
				$dados["id"] = 0;
				$dados["cidadeCurso"] = 0;
				$dados["curso"] = $_POST["curso"];
				$dados["timestamp"] = time();
				$dados["data"] = date('d/m/Y H:i:s', $dados["timestamp"]);
				$dados["responsavel"] = $_SESSION[PREFIX . "loginId"];
				$dados = $this->dao->salva($conexao, "preinscricoes", $dados);
				$novoCurso = $this->dao->findByPk ($conexao, "cursos", (int) $_POST["curso"]);
				setMensagem("info", "Inscrição de " . $dados["nome"] . " duplicada para o curso " . $novoCurso["nome"]);
				
				if (!empty($dados["comprovante"])) {
					$origem = DIR_UPLOADS . DS . "comprovantes";
					$origem .= DS . $old["id"] . DS;
					$origem .= base64_decode($old["comprovante"]);
					$destino = DIR_UPLOADS . DS . "comprovantes";
					$destino .= DS . $dados["id"] . DS;
					$destino .= base64_decode($old["comprovante"]);
					if (existeArquivo($origem)) {
		                copiaArquivo($origem, $destino);
		            }
				}

				// adiciona no histórico
				$historicoModel->adicionar($dados["id"], sprintf("Inscrição duplicada de %s", 
					$curso["nome"]));
				// adciciona nos logs
				$this->log->adicionar ("duplicou", "opção de curso", $dados["nome"], 
					sprintf("Usuário duplicou inscrição de %s do curso %s para %s.", $dados["nome"], $curso["nome"], $novoCurso["nome"]));

				$conexao->commit();
				$conexao->disconnect();
				$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("curso", "id", "acao", "p", "order"), array(
						"curso" => $novoCurso["id"],
						"acao" => "visualizar"
					)
				);
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
		catch (Exception $e) {
			setMensagem("error", $e->getMessage());
			if ($redirecionar != NULL) {
				Application::redirect($redirecionar);
				exit;
			}
		}

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", "mudar.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"cursos" => $cursos,
				"preinscricao" => $dados
			)
		);
		$view->showContents();

	}

	public function mudarAction() {
		try {
			$this->checaPermissao('preinscricoes', 'mudar');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("id", "acao"), array(
					"acao" => "visualizar"
				)
			);

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);
			$historicoModel = new HistoricoPreinscricao($conexao);

			$dados = $inscricaoModel->findOrFail(getVariavel('id'));
			$curso = $cursoModel->findOrFail(getVariavel('curso'));
			$cursos = $cursoModel->getQuery()
				->order('nome', 'asc')
				->all();

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Inscrições" => "?modulo=preinscricoes",
				$curso["nome"] => sprintf("?modulo=preinscricoes&acao=visualizar&curso=%d&tab1=%d", 
					$curso["id"], $_GET['tab1']),
				$dados["nome"] => sprintf("?modulo=preinscricoes&acao=cadastrar&curso=%d&tab1=%d&id=%d", 
					$curso["id"], $_GET['tab1'], $dados['id']),
				"Mudar Curso" => ""
			);

			if (count($_POST) > 0) {

				$obrigatorios = array(
					"curso" => array(
						"tipo" => "select",
						"nome" => "Curso"
					)
				);

				$mensagem = validaPost($obrigatorios, $_POST);
				if (!empty($mensagem)) {
					$redirecionar = NULL;
					throw new Exception($mensagem);
				}

				$novoCurso = $this->dao->findByPk ($conexao, "cursos", $_POST["curso"]);
				$dadosIn = $this->dao->findByPk($conexao, "preinscricoes", $dados["id"]);
				$dadosIn["curso"] = $_POST["curso"];
				//$dadosIn["unidade"] = 0;
				$this->dao->salva($conexao, "preinscricoes", $dadosIn);

				setMensagem("info", "Opção de curso de " . $dadosIn["nome"] . " foi alterada de " . $curso["nome"] . " para " . $novoCurso["nome"]);
				//setMensagem("error", "Atenção: Inscrições que tem a opção de curso alterada devem ter sua unidade redefinida no novo curso.");

				// adiciona no histórico
				$historicoModel->adicionar($dados["id"], 
					sprintf("Opção de curso alterada de %s para %s", $curso["nome"], $novoCurso['nome']));
	            // adciciona nos logs
				$this->log->adicionar ("alterou", "opção de curso", $dados["nome"], 
					sprintf("Usuário alterou opção de curso da inscrição de %s do curso %s para %s.", $dados["nome"], $curso["nome"], $novoCurso["nome"]));

				$conexao->commit();
				$conexao->disconnect();
				$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("curso", "id", "acao", "p", "order"), array(
						"curso" => $novoCurso["id"],
						"acao" => "visualizar"
					)
				);
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
		catch (Exception $e) {
			setMensagem("error", $e->getMessage());
			if ($redirecionar != NULL) {
				Application::redirect($redirecionar);
				exit;
			}
		}

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", "mudar.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"cursos" => $cursos,
				"preinscricao" => $dados
			)
		);
		$view->showContents();

	}

	public function historicoAction() {

		try {
			$this->checaPermissao('preinscricoes', 'historico');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("id", "acao"), array(
					"acao" => "visualizar"
				)
			);

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);
			$historicoModel = new HistoricoPreinscricao($conexao);

			$dados = $inscricaoModel->findOrFail(getVariavel('id'));
			$curso = $cursoModel->findOrFail(getVariavel('curso'));

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Inscrições" => "?modulo=preinscricoes",
				$curso["nome"] => sprintf("?modulo=preinscricoes&acao=visualizar&curso=%d&tab1=%d", 
					$curso["id"], $_GET['tab1']),
				$dados["nome"] => sprintf("?modulo=preinscricoes&acao=cadastrar&curso=%d&tab1=%d&id=%d", 
					$curso["id"], $_GET['tab1'], $dados['id']),
				"Histórico" => ""
			);

			$quantidadePorPagina = isset($_GET["exibir"]) ? (int) $_GET["exibir"] : QUANTIDADE_POR_PAGINA;
			$quantidadePorPagina = ($quantidadePorPagina <= 0) ? QUANTIDADE_POR_PAGINA : $quantidadePorPagina;
			$pagina = (isset($_GET["p"])) ? $_GET["p"] : 1;
			$pagina = ($pagina <= 0) ? 1 : $pagina;
			$limit = ($pagina == 1) ? $quantidadePorPagina : $quantidadePorPagina * ($pagina - 1);
			$offset = ($pagina == 1) ? 0 : $quantidadePorPagina;

			$historico = $historicoModel->getQuery()
				->where("preinscricao = ?", (int) $dados["id"])
				->order("data", "desc")
				->limitIn($limit, $offset)
				->all();

			$quantidade = count($historico);

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
			if ($redirecionar != NULL) {
				Application::redirect($redirecionar);
				exit;
			}
		}

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", "historico.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"preinscricao" => $dados,
				"curso" => $curso,
				"objetos" => $historico,
				"quantidade" => $quantidade,
				"quantidadePorPagina" => $quantidadePorPagina,
				"pagina" => $pagina,
			)
		);
	    $view->showContents();
	}

	public function adicionarAction() {

		try {
			$this->checaPermissao('preinscricoes', 'adicionar');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("preinscricao", "acao"), array(
					"acao" => "historico",
					"id" => $_GET["preinscricao"]
				)
			);

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);
			$historicoModel = new HistoricoPreinscricao($conexao);

			$dados = $inscricaoModel->findOrFail(getVariavel('preinscricao'));
			$curso = $cursoModel->findOrFail(getVariavel('curso'));

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Inscrições" => "?modulo=preinscricoes",
				$curso["nome"] => sprintf("?modulo=preinscricoes&acao=visualizar&curso=%d&tab1=%d", 
					$curso["id"], $_GET['tab1']),
				$dados["nome"] => sprintf("?modulo=preinscricoes&acao=cadastrar&curso=%d&tab1=%d&id=%d", 
					$curso["id"], $_GET['tab1'], $dados['id']),
				"Histórico" => sprintf("?modulo=preinscricoes&acao=historico&curso=%d&tab1=%d&id=%d", 
					$curso["id"], $_GET['tab1'], $dados['id'])
			);

			if (isset($_GET["id"])) {
				$historico = $historicoModel->findOrFail(getVariavel("id"));
				$dados = array(
					"id" => $historico["id"],
					"nome" => $dados["nome"],
					"preinscricao" => $dados["id"],
					"descricao" => $historico["descricao"]
				);
				$breadcrumbs[] = array(
					"Editar Registro" => ""
				);
			}
			else {
				$dados = array(
					"id" => 0,
					"nome" => $dados["nome"],
					"preinscricao" => $dados["id"],
					"descricao" => ""
				);
				$breadcrumbs[] = array(
					"Cadastrar Registro" => ""
				);
			}

			if (count($_POST) > 0) {

				$dadosPost = $_POST;

				$obrigatorios = array(
					"descricao" => array(
						"tipo" => "textarea",
						"nome" => "Descrição"
					)
				);

				$mensagem = validaPost($obrigatorios, $dadosPost);
				if (!empty($mensagem)) {
					$redirecionar = NULL;
					throw new Exception($mensagem);
				}

				if ((int) $dadosPost['id'] == 0) {
					$dadosPost["timestamp"] = time();
					$dadosPost["data"] = getData($dadosPost["timestamp"]);
				}
				
				$dadosPost["quem"] = $_SESSION[PREFIX . "loginNome"];
				$this->dao->salva($conexao, "historico", $dadosPost);

				if ($dadosPost["id"] == 0) {
					$this->log->adicionar ("adicionou", "registro", $dados["nome"], 
						sprintf("Usuário adicionou registro no histórico do aluno do curso %s", $curso["nome"]));
					setMensagem("info", "Registro adicionado");
				}
				else {
					$this->log->adicionar ("atualizou", "registro", $dados["nome"], 
						sprintf("Usuário atualizou registro no histórico do aluno do curso %s", $curso["nome"]));
					setMensagem("info", "Registro atualizado");
				}

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
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
			if ($redirecionar != NULL) {
				Application::redirect($redirecionar);
				exit;
			}
		}

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", "adicionar.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"preinscricao" => $dados,
				"historico" => $dados,
				"curso" => $curso,
			)
		);
        $view->showContents();
	}

	public function removerAction() {

		try {
			$this->checaPermissao('preinscricoes', 'remover');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("acao", "id", "preinscricao"), array(
					"acao" => "historico",
					"id" => $_GET["preinscricao"]
				)
			);

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);
			$historicoModel = new HistoricoPreinscricao($conexao);

			$inscricao = $inscricaoModel->findOrFail(getVariavel('preinscricao'));
			$curso = $cursoModel->findOrFail(getVariavel('curso'));
			$historico = $historicoModel->findOrFail(getVariavel("id"));

			$affectedRows = $this->dao->excluiByPk($conexao, "historico", (int) $historico["id"]);

			if ($affectedRows > 0) {
				$this->log->adicionar ("excluiu", "registro", $historico["descricao"], 
					sprintf("Usuário excluiu registro do histórico do aluno %s do curso %s.", $inscricao["nome"], $curso["nome"]));
				$conexao->commit();
				setMensagem("info", "Registro excluído [" . $historico["descricao"] . "]");
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
		Application::redirect($redirecionar);
		exit;

	}

	/**
	*	Exclui o comprovante de depósito
	*/
	public function excluircAction() {

		try {
			$this->checaPermissao('preinscricoes', 'cadastrar');
			$conexao = $this->conexao->getConexao();

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);

			$dados = $inscricaoModel->findOrFail(getVariavel('id'));
			$curso = $cursoModel->findOrFail(getVariavel('curso'));

			$diretorio = DIR_UPLOADS . DS . "comprovantes";
			$diretorio .= DS . $dados["id"] . DS ;
			$diretorio .= base64_decode($dados["comprovante"]);

			if (excluiArquivo($diretorio)) {
				$this->log->adicionar ("excluiu", "comprovante", $dados["nome"], 
					sprintf("Usuário excluiu comprovante de depósito da inscrição do aluno do curso %s", $curso["nome"]));
				$dadosIn["id"] = $dados["id"];
				$dadosIn["enviouComprovante"] = 0;
				$dadosIn["comprovante"] = NULL;
				$dadosIn["mime"] = NULL;
				$dadosIn["extensao"] = NULL;
				$this->dao->salva($conexao, "preinscricoes", $dadosIn);
				$conexao->commit();
				setMensagem("info", "Comprovante excluído");
			}
			else {
				setMensagem("error", "Erro ao tentar excluir comprovante");
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
		$redirecionar = sprintf("?modulo=preinscricoes&acao=cadastrar&curso=%d&id=%d&tab1=%d", $curso["id"], $dados["id"], $dados['status']);
		Application::redirect($redirecionar);
		exit;
	}

	public function excluirAction() {

		try {
			$this->checaPermissao('preinscricoes', 'excluir');
			$conexao = $this->conexao->getConexao();
			$model = new Preinscricao($conexao);
			$dados = $model->findOrFail(getVariavel('id'));
			$affectedRows = $model->delete2($dados);
			if ($affectedRows > 0) {
				$diretorio = DIR_UPLOADS . DS . "comprovantes";
				$diretorio .= DS . $dados["id"];
				excluiDiretorio($diretorio);
				$this->log->adicionar ("excluiu", "inscrição", $dados["nome"], 
					sprintf("Usuário excluiu inscrição do curso %s", $dados["curso"]));
				$conexao->commit();
				setMensagem("info", "Pré-inscrição excluída [" . $dados["nome"] . "]");
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
		$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("id", "acao"), array(
				"acao" => "visualizar"
			)
		);
		$conexao->disconnect();
		Application::redirect($redirecionar);
		exit;

	}

	public function imprimirePdfAction() {

		try {
			$this->checaPermissao('preinscricoes', 'imprimirePdf');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("acao"));

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);

			$cursos = $cursoModel->getQuery()
				->order('nome', 'asc')
				->all();

			$header = array(
				"de" => "Estatísticas de inscrições"
			);

			$aux = 0;
			$tamanhos = array();

			$listagem = new Listagem("P", "mm", "A4", $header);
			$listagem->AddPage();

			foreach ($cursos as $curso) {

				$query = $inscricaoModel->getQuery()->where("idCurso = ?", (int) $curso["id"]);

				$query1 = clone $query;

				$quantidade = $query1->count();

				$listagem->SetFont('Helvetica','B',10);
				$listagem->Cell(30,0,utf8_decode($curso["nome"]) . " (" . $quantidade . ")",0);
				$listagem->SetFont('Helvetica','',9);
				$listagem->Ln(6);
				$texto = array();
				foreach (Preinscricao::getStatuses() as $key => $value) {
					$tam = $listagem->GetStringWidth($value) + 10;
					$query2 = clone $query;
					$quantidade = $query2->where("status = ?", (int) $key)->count();
					$texto[] = $quantidade . " " . utf8_decode($value) . "(s)";
				}
				$listagem->MultiCell(0, 5, implode(", ", $texto), 0, "L", 0);
				$listagem->Ln(6);
			}

			$nomeArquivo = "Estatisticas-Preinscricoes.pdf";
			$path = DIR_ROOT . DS . "temp";

			if (is_writable($path)) {
				$path .=  DS . $nomeArquivo;
				//$listagem->Output();exit;
				$listagem->Output($path, "F");
				$this->log->adicionar ("gerou", "estatísticas", "inscrições", 
					"Usuário gerou estatísticas de inscrições.");
				$conexao->commit();
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="' . basename($path) . '"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($path));
				ob_clean();
				flush();
				readfile($path);
				unlink($path);
				exit;
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
			if ($redirecionar != NULL) {
				Application::redirect($redirecionar);
				exit;
			}
		}
	}

	public function imprimireExcelAction() {
		try {
			$this->checaPermissao('preinscricoes', 'imprimireExcel');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("acao"));

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);

			$cursos = $cursoModel->getQuery()
				->order('nome', 'asc')
				->all();

			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("IEFAP");
			$objPHPExcel->getProperties()->setLastModifiedBy("IEFAP");
			$objPHPExcel->getProperties()->setTitle("Listagem de pré-inscrições");
			$objPHPExcel->getProperties()->setSubject("Listagem de pré-inscrições");
			$objPHPExcel->getProperties()->setDescription("IEFAP - Listagem de pré-inscrições");
			$objPHPExcel->setActiveSheetIndex(0);

			$filtro = array(
				"nome",
				"sexo",
				"estadoCivil",
				"rg",
				"orgaoExpedidor",
				"ufExpedidor",
				"cpf",
				"dataNascimento",
				"endereco",
				"numero",
				"complemento",
				"bairro",
				"cidade",
				"uf",
				"cep",
				"telefoneResidencial",
				"operadoraCelular",
				"telefoneCelular",
				"email",
				"formacao",
				"instituicao",
				"anoConclusao",
				"banco",
				"diaPagamento",
				"comoConheceu",
				"cidadeCurso",
				"turma",
				"status",
				"data"
			);

			$numeroLinha = 1;
			$numeroColuna = 1;

			foreach ($filtro as $f) {
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, $numeroLinha, $f, false);
			}

			$numeroLinha = 3;

			foreach ($cursos as $curso) {

				$numeroColuna = 0;

				$dados = $inscricaoModel->getQuery($filtro)
					->where("idCurso = ?", (int) $curso["id"])
					->all();

				foreach ($dados as $key => $value) {

					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, $numeroLinha, "Curso: " . $curso["nome"], false);

					foreach ($value as $campo => $valor) {

						if ($campo == "unidadeCertificadora") {
							$valor = Curso::getCertificadora ($valor);
						}

						if ($campo == "status") {
							$valor = Preinscricao::getStatus($valor);
						}

						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, $numeroLinha, $valor, false);
					}
					$numeroLinha++;
					$numeroColuna = 0;
				}

				$numeroLinha++;
			}

			$nomeArquivo = "Listagem-Geral-Preinscricoes.xlsx";
			$path = DIR_ROOT . DS . "temp";

			if (is_writable($path)) {
				$path .=  DS . $nomeArquivo;
				$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
				$objWriter->save($path);
				$this->log->adicionar ("gerou", "listagem geral", "EXCEL", "Usuário gerou listagem geral de pré-inscrições em EXCEL");
				$conexao->commit();
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.basename($path).'"');
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
			if ($redirecionar != NULL) {
				Application::redirect($redirecionar);
				exit;
			}
		}
	}

	public function imprimirpListagemAction() {
		try {
			$this->checaPermissao('preinscricoes', 'imprimirpListagem');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("acao"), array("acao" => "visualizar"));

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);

			$curso = $cursoModel->findOrFail(getVariavel('curso'));

			$header = array(
				"de" => "inscrições do curso " . $curso["nome"],
				"dados" => array(
					"" => 10,
					"Nome" => 50,
					"CPF" => 25,
					"Unidade" => 25,
					"Certificadora" => 21,
					"Status" => 30,
					"Data" => 20
				)
			);

			$filtro = array(
				"nome",
				"cpf",
				"unidade",
				"unidadeCertificadora",
				"status",
				"data"
			);

			$query = $inscricaoModel->getQuery($filtro)->where("idCurso = ?", (int) $curso["id"]);

			// parametros que podem vir na requisição GET
			if (array_key_exists('nome', $_GET) && !empty($_GET['nome'])) {
				$query->where("nome like ?",  codificaDado ($_GET["nome"]) . "%");
			}

			if (array_key_exists('unidade', $_GET) && !empty($_GET['unidade'])) {
				$query->where("unidade like ?",  codificaDado ($_GET["unidade"]) . "%");
			}

			if (array_key_exists('certificadora', $_GET) && !empty($_GET['certificadora'])) {
				$query->where("unidadeCertificadora = ?", Curso::getConstanteCertificadora ($_GET["certificadora"]));
			}

			if (array_key_exists('data', $_GET) && !empty($_GET['data'])) {
				if (preg_match("/^\d{2}\\/\d{2}\/\d{4}$/", $_GET["data"])) {
					list($dia, $mes, $ano) = explode("/", $_GET["data"]);
					$data = $ano . "-" . $mes . "-" . $dia;
					$query->where("DATE(data) = ?",  $data);
				}
			}

			if (array_key_exists('order', $_GET) && !empty($_GET['order'])) {
				$query->order($_GET['order'],  "asc");
			}
			else {
				$query->order("data", "desc");
			}

			$lista = $query->all();

			foreach ($lista as $key => $value) {
				$lista[$key] = decodificaDados2($value);
			}

			foreach ($lista as $key => $value) {
				$lista[$key]["status"] = Preinscricao::getStatus($lista[$key]["status"]);
				$lista[$key]["unidadeCertificadora"] = Curso::getCertificadora($lista[$key]["unidadeCertificadora"]);
			}

			$aux = 0;
			$tamanhos = array();

			$listagem = new Listagem("P", "mm", "A4", $header);
			$listagem->AddPage();

			foreach ($header["dados"] as $nome => $tamanho) {
				$listagem->Cell($tamanho,0,utf8_decode($nome),0);
				$tamanhos[] = $tamanho;
			}

			$listagem->SetFont('Helvetica','',9);
			$listagem->Ln(7);

			$x = 0;
			$y = 0;
			$y2 = 0;

			foreach ($lista as $indice => $dados) {
				// escreve a numeração
				$x = $listagem->getX();
				$y = $listagem->getY();
				$listagem->MultiCell($tamanhos[$aux], 5, ($indice+1), 0, 'L', 0);
				$listagem->setY($y);
				$listagem->setX($x + $tamanhos[$aux++]);
				foreach ($lista[$indice] as $dado) {
					$dado = utf8_decode($dado);
					$x = $listagem->getX();
					$y = $listagem->getY();
					$listagem->MultiCell($tamanhos[$aux], 5, $dado, 0, 'L', 0);
					if ($listagem->getY() > $y2) {
						$y2 = $listagem->getY();
					}
					$listagem->setY($y);
					$listagem->setX($x + $tamanhos[$aux]);
					$aux++;
				}

				$aux = 0;
				$listagem->setY($y2);

				if ($y2 + 10 > $listagem->h - 17) {
					$listagem->AddPage();
					$y2 = 0;
				}

			}

			$nomeArquivo = "Listagem-Preinscricoes-" . implode("-", explode(" ",trim($curso["nome"]))) . ".pdf";
			$path = DIR_ROOT . DS . "temp";

			if (is_writable($path)) {
				$path .=  DS . $nomeArquivo;
				//$listagem->Output();exit;
				$listagem->Output($path, "F");
				$this->log->adicionar ("gerou", "listagem", "PDF", 
					"Usuário gerou listagem em PDF de pré-inscrições do curso ".$curso["nome"] . ".");
				$conexao->commit();
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.basename($path).'"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($path));
				//ob_clean();
				flush();
				readfile($path);
				unlink($path);
				exit;
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
			if ($redirecionar != NULL) {
				Application::redirect($redirecionar);
				exit;
			}
		}
	}

	public function planilhaAction() {
		try {
			$this->checaPermissao('preinscricoes', 'planilha');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("acao"));

			$colunas = isset($_POST["colunas"]) ? $_POST["colunas"] : array();

			if (!isset($_POST["curso"])) {
				throw new Exception ('Curso não encontrado');
			}

			$redirecionar .= '&acao=visualizar&curso=' . $_POST["curso"];

			if (count($colunas) == 0) {
				throw new Exception ('É necessário escolher pelo menos uma coluna');
			}

			$inscricaoModel = new Preinscricao($conexao);
			$cursoModel = new Curso($conexao);

			if (count($_POST) > 0) {

				$curso = $cursoModel->findOrFail($_POST["curso"]);

				if ($colunas[0] == -1) {
					array_shift($colunas);
				}

				$filtro = $colunas;
				$filtro[] = "data";

				$objPHPExcel = new PHPExcel();
				$objPHPExcel->getProperties()->setCreator("IEFAP");
				$objPHPExcel->getProperties()->setLastModifiedBy("IEFAP");
				$objPHPExcel->getProperties()->setTitle("Planilha de pré-inscrições");
				$objPHPExcel->getProperties()->setSubject("Planilha de pré-inscrições");
				$objPHPExcel->getProperties()->setDescription("IEFAP - Planilha de pré-inscrições");
				$objPHPExcel->setActiveSheetIndex(0);

				$dados = $inscricaoModel->getQuery($filtro)
					->order('nome', 'asc')
					->where("idCurso = ?", (int) $curso["id"])
					->all();

				$numeroLinha = 1;
				$numeroColuna = 0;

				foreach ($filtro as $f) {
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, 
						$numeroLinha, $f, false);
				}

				$numeroLinha = 2;
				$numeroColuna = 0;

				foreach ($dados as $key => $value) {

					//$dados[$key] = decodificaDados2($value);

					foreach ($value as $campo => $valor) {

						if ($campo == "unidadeCertificadora") {
							$valor = Curso::getCertificadora ($valor);
						}

						if ($campo == "banco") {
							$valor = Preinscricao::getBanco ($valor);
						}

						if ($campo == "status") {
							$valor = Preinscricao::getStatus($valor);
						}

						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, 
							$numeroLinha, $valor, false);
					}

					$numeroLinha++;
					$numeroColuna = 0;
				}

				$nomeArquivo = "Planilha-Preinscricoes-" . implode("-", explode(" ",trim($curso["nome"]))) . ".xlsx";
				$path = DIR_ROOT . DS . "temp";

				if (is_writable($path)) {
					$path .=  DS . $nomeArquivo;
					$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
					$objWriter->save($path);
					$this->log->adicionar ("gerou", "planilha", "EXCEL", 
						"Usuário gerou planilha em EXCEL de pré-inscrições do curso " . $curso["nome"] . ".");
					$conexao->commit();
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="'.basename($path).'"');
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
			if ($redirecionar != NULL) {
				Application::redirect($redirecionar);
				exit;
			}
		}
	}

	public function imprimirSituacaoAction() {
		try {
			$this->checaPermissao('preinscricoes', 'imprimirSituacao');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("acao"), array("acao" => "visualizar"));

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);
			$situacaoModel = new PreinscricaoSituacao($conexao);

			$curso = $cursoModel->findOrFail(getVariavel("curso"));

			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("IEFAP");
			$objPHPExcel->getProperties()->setLastModifiedBy("IEFAP");
			$objPHPExcel->getProperties()->setTitle("Listagem de situação do curso " . $curso["nome"]);
			$objPHPExcel->getProperties()->setSubject("Listagem de situação do curso " . $curso["nome"]);
			$objPHPExcel->getProperties()->setDescription("IEFAP - Listagem de situação do curso " . $curso["nome"]);
			$objPHPExcel->setActiveSheetIndex(0);

			$itens = PreinscricaoSituacao::getItens();
			$cabecalho = array("Nome");

			foreach ($itens as $key => $value) {
				$cabecalho[] = $value;
			}

			$cabecalho[] = "status";
			$cabecalho[] = "data";

			$filtro = array(
				"id",
				"nome",
				"status",
				"timestamp"
			);

			$dados = $inscricaoModel->getQuery($filtro)
				->order('nome', 'asc')
				->where("idCurso = ?", (int) $curso["id"])
				->all();

			$numeroLinha = 1;
			$numeroColuna = 0;

			foreach ($cabecalho as $c) {
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, 
					$numeroLinha, $c, false);
			}

			$numeroLinha = 2;
			$numeroColuna = 0;

			foreach ($dados as $key => $value) {

				// escrevo o nome
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, 
					$numeroLinha, $value["nome"], false);

				$filtro = array(
					"item",
					"situacao"
				);

				$situacoesAluno = $situacaoModel->getQuery($filtro)
					->where('preinscricao = ?', (int) $value["id"])
					->order('item', 'asc')
					->all();

				$existeItem = function($item, $array) {
					foreach ($array as $key => $value) {
						if ($value["item"] == $item) {
							return $key;
						}
					}
					return -1;
				};

				$situacoes = array();
				for($i=1; $i<=count($itens); $i++) {
					$indice = $existeItem($i, $situacoesAluno);
					if ($indice != -1) {
						$situacoes[$i] = $situacoesAluno[$indice]["situacao"];
					}
					else {
						$situacoes[$i] = 0;
					}
				}

				foreach($situacoes as $item => $situacao) {
					if ($situacao == 1) {
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, $numeroLinha, "Sim", false);
					}
					else {
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, $numeroLinha, "Não", false);
					}
				}

				// escrevo o status
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, $numeroLinha, Preinscricao::getStatus($value["status"]), false);
				// escrevo a data
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, $numeroLinha, getDataFormatada($value["timestamp"]), false);

				$numeroLinha++;
				$numeroColuna = 0;
			}

			$nomeArquivo = "Listagem-Situacao-" . implode("-", explode(" ",trim($curso["nome"]))) . ".xlsx";
			$path = DIR_ROOT . DS . "temp";

			if (is_writable($path)) {
				$path .=  DS . $nomeArquivo;
				$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
				$objWriter->save($path);
				$this->log->adicionar ("gerou", "listagem", "Situação em EXCEL", 
					"Usuário gerou listagem de situação das pré-inscrições do curso ".$curso["nome"] . " em EXCEL.");
				$conexao->commit();
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.basename($path).'"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($path));
				//ob_clean();
				flush();
				readfile($path);
				unlink($path);
				exit;
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
			if ($redirecionar != NULL) {
				Application::redirect($redirecionar);
				exit;
			}
		}
	}

	public function imprimirHistoricoAction() {
		try {
			$this->checaPermissao('preinscricoes', 'imprimirHistorico');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("acao", "preinscricao"), array(
					"acao" => "historico",
					"id" => $_GET["preinscricao"]
				)
			);

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);
			$historicoModel = new HistoricoPreinscricao($conexao);

			$curso = $cursoModel->findOrFail(getVariavel("curso"));

			$dadosPreinscricao = $inscricaoModel->findOrFail(getVariavel('preinscricao'));

			$header = array(
				"de" => "Histórico de inscrição de " . $dadosPreinscricao["nome"],
				"dados" => array(
					"" => 7,
					"Descrição" => 110,
					"Quem registrou" => 50,
					"Data" => 20
				)
			);

			$filtro = array(
				"descricao",
				"responsavel",
				"data",
				"timestamp"
			);

			$lista = $historicoModel->getQuery()
				->order('data', 'asc')
				->where("preinscricao = ?", (int) $dadosPreinscricao["id"])
				->all();

			$aux = 0;
			$tamanhos = array();

			$listagem = new Listagem("P", "mm", "A4", $header);
			$listagem->AddPage();

			foreach ($header["dados"] as $nome => $tamanho) {
				$listagem->Cell($tamanho,0,utf8_decode($nome),0);
				$tamanhos[] = $tamanho;
			}

			$listagem->SetFont('Helvetica','',9);
			$listagem->Ln(7);
			$x = $listagem->GetX();

			foreach ($lista as $indice => $dados) {
				$topo = $listagem->GetY();
				$listagem->SetX(125);
				$listagem->MultiCell(105,5,utf8_decode(decodificaDado($dados["responsavel"])),0);
				$listagem->SetY($topo);
				$listagem->SetX(174);
				$listagem->MultiCell(105,5,utf8_decode(getDataFormatada($dados["timestamp"])),0);
				$listagem->SetY($topo);
				$listagem->SetX($x);
				$listagem->MultiCell(105,5,utf8_decode(decodificaDado($dados["descricao"])),0);
				$listagem->Ln(3);
			}

			$nomeArquivo = "Historico-";
			$nomeArquivo .= implode("-", explode(" ",trim($dadosPreinscricao["nome"])));
			$nomeArquivo .= ".pdf";

			$path = DIR_ROOT . DS . "temp";

			if (is_writable($path)) {
				$path .=  DS . $nomeArquivo;
				//$listagem->Output();exit;
				$listagem->Output($path, "F");
				$this->log->adicionar ("gerou", "listagem", "histórico", 
					"Usuário gerou listagem do histórico do aluno " . $dadosPreinscricao["nome"] . " do curso " . $curso["nome"] . ".");
				$conexao->commit();
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.basename($path).'"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($path));
				//ob_clean();
				flush();
				readfile($path);
				unlink($path);
				exit;
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
			if ($redirecionar != NULL) {
				Application::redirect($redirecionar);
				exit;
			}
		}
	}

	public function formularioAction () {

		try {
			$this->checaPermissao('preinscricoes', 'formulario');
			$conexao = $this->conexao->getConexao();

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);

			$curso = $cursoModel->findOrFail(getVariavel("curso"));
			$dadosPreinscricao = $inscricaoModel->findOrFail(getVariavel("id"));

			//$unidade = $dadosPreinscricao["idCidade"];

			$certificadora = empty($dadosPreinscricao["unidadeCertificadora"]) ? $curso["unidadeCertificadora"] : $dadosPreinscricao["unidadeCertificadora"];

			$formulario = new FormularioPreinscricao ("P", "mm", "A4", $certificadora);
			$formulario->setTitle(utf8_decode("Formulário de Pré-inscrição"));
			$formulario->AddPage();

			$formulario->setTextColor(0,0,0);
			$formulario->setY(38);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',11);
			$formulario->Cell(0,0,utf8_decode('FORMULÁRIO DE INSCRIÇÃO'));

			$formulario->setY(48);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',9);

			$label = 'CURSO';
			if ($dadosPreinscricao["unidadeCertificadora"]) {
				$label .= ' / UNIDADE CERTIFICADORA';
			}

			$valor = $curso["nome"];
			if ($dadosPreinscricao["unidadeCertificadora"]) {
				$valor .= ' / ' . Curso::getCertificadora($dadosPreinscricao["unidadeCertificadora"]);
			}

			$formulario->Cell(0,0, utf8_decode($label));
			$formulario->SetFont('Helvetica','',9);
			$formulario->setY(56);
			$formulario->setX(31);
			$formulario->Cell(100,0.1,'',0,0,0,true,'');
			$formulario->setY(54);
			$formulario->setX(30);
			$formulario->Cell(0,0, utf8_decode($valor));
			$formulario->setY(68);
			$formulario->setX(31);
			$formulario->Cell(28,0.7,'',0,0,0,true,'');
			$formulario->setY(66);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',9);
			$formulario->Cell(0,0,utf8_decode('IDENTIFICAÇÃO'));

			// faculdade/universidade
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('NOME'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(31);
			$formulario->Cell(122,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(30);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["nome"]));

			// ano conclusão
			$formulario->setY($formulario->getY()-4);
			$formulario->setX(156);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('CPF'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(157);
			$formulario->Cell(38,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(156);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["cpf"]));

			// rg
			$formulario->setY($formulario->getY() + 6);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('RG'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY() + 6);
			$formulario->setX(31);
			$formulario->Cell(40,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY() - 2);
			$formulario->setX(30);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["rg"]));

			// orgao emissor
			$formulario->setY($formulario->getY() - 4);
			$formulario->setX(72);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('ORGÃO EXPEDIDOR'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY() + 6);
			$formulario->setX(73);
			$formulario->Cell(40,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY() - 2);
			$formulario->setX(72);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["orgaoExpedidor"]));

			// uf expedidor
			$formulario->setY($formulario->getY() - 4);
			$formulario->setX(114);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('UF EXPEDIDOR'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY() + 6);
			$formulario->setX(115);
			$formulario->Cell(40,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY() - 2);
			$formulario->setX(114);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["ufExpedidor"]));

			// CPF
			$formulario->setY($formulario->getY() - 4);
			$formulario->setX(156);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('DATA DE EXPEDIÇÃO'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY() + 6);
			$formulario->setX(157);
			$formulario->Cell(36,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY() - 2);
			$formulario->setX(156);
			$formulario->Cell(0,0,$dadosPreinscricao["dataExpedicaoF"]);

			// data de nascimento
			$formulario->setY($formulario->getY() + 6);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('DATA DE NASCIMENTO'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY() + 6);
			$formulario->setX(31);
			$formulario->Cell(40,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY() - 2);
			$formulario->setX(30);
			$formulario->Cell(0,0,$dadosPreinscricao["dataNascimentoF"]);

			// sexo
			$formulario->setY($formulario->getY() - 4);
			$formulario->setX(72);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('SEXO'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY() + 6);
			$formulario->setX(73);
			$formulario->Cell(40,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY() - 2);
			$formulario->setX(72);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["sexo"]));

			// naturalidade
			$formulario->setY($formulario->getY() - 4);
			$formulario->setX(114);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('ESTADO CIVIL'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY() + 6);
			$formulario->setX(115);
			$formulario->Cell(78,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY() - 2);
			$formulario->setX(114);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["estadoCivil"]));

			// endereço
			$formulario->setY($formulario->getY() + 6);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('ENDEREÇO/Nº/COMPLEMENTO'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(31);
			$formulario->Cell(162,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(30);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["endereco"] . " " .$dadosPreinscricao["numero"] . " " . $dadosPreinscricao["complemento"]));

			// bairro
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('BAIRRO'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(31);
			$formulario->Cell(82,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(30);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["bairro"]));

			// cidade
			$formulario->setY($formulario->getY()-4);
			$formulario->setX(114);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('CIDADE'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(115);
			$formulario->Cell(78,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(114);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["cidade"]));

			// uf
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('UF'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(31);
			$formulario->Cell(4,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(30);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["uf"]));

			// cep
			$formulario->setY($formulario->getY()-4);
			$formulario->setX(36);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('CEP'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(37);
			$formulario->Cell(20,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(36);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["cep"]));

			// operadora celular
			$formulario->setY(122);
			$formulario->setX(58);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('OPERADORA CELULAR'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY(128);
			$formulario->setX(59);
			$formulario->Cell(54,0.1,'',0,0,0,true,'');
			$formulario->setY(126);
			$formulario->setX(58);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["operadoraCelular"]));

			// telefone celular
			$formulario->setY($formulario->getY()-4);
			$formulario->setX(114);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('TELEFONE CELULAR'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(115);
			$formulario->Cell(38,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(114);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["telefoneCelular"]));

			// telefone celular
			$formulario->setY($formulario->getY()-4);
			$formulario->setX(154);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('TELEFONE RESIDENCIAL'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(155);
			$formulario->Cell(38,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(154);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["telefoneResidencial"]));

			// e-mail
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('E-MAIL'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(31);
			$formulario->Cell(81,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(30);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["email"]));

			$formulario->setY($formulario->getY()-4);
			$formulario->setX(114);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('PROFISSÃO'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(115);
			$formulario->Cell(78,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(114);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["profissao"]));

			// formação
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('NATURALIDADE'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(31);
			$formulario->Cell(162,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(30);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["naturalidade"]));

			// formação
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('FILIAÇÃO'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(31);
			$formulario->Cell(162,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(30);

			$filiacao = '';
			if (!empty($dadosPreinscricao["nomePai"])) {
				$filiacao .= $dadosPreinscricao["nomePai"];
			}
			if (!empty($dadosPreinscricao["nomeMae"])) {
				$filiacao .= ' / ' . $dadosPreinscricao["nomeMae"];
			}

			$formulario->Cell(0,0,utf8_decode($filiacao));

			// formação
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('FORMAÇÃO ACADÊMICA'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(31);
			$formulario->Cell(162,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(30);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["formacao"]));

			// faculdade/universidade
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('FACULDADE/UNIVERSIDADE'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(31);
			$formulario->Cell(122,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(30);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["instituicao"]));

			// ano conclusão
			$formulario->setY($formulario->getY()-4);
			$formulario->setX(154);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('ANO CONCLUSÃO'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(155);
			$formulario->Cell(38,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(154);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["anoConclusao"]));

			// como conheceu
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('COMO CONHECEU O IEFAP'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(31);
			$formulario->Cell(81,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(30);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["comoConheceu"]));

			// nome da pessoa que indicou
			if ($dadosPreinscricao["comoConheceu"] == "Amigo/Parente" ||
				$dadosPreinscricao["comoConheceu"] == "Amigo/Parente/Aluno") {
				$formulario->setY($formulario->getY()-4);
				$formulario->setX(114);
				$formulario->SetFont('Helvetica','B',8);
				$formulario->Cell(0,0,utf8_decode('NOME DA PESSOA QUE INDICOU'));
				$formulario->SetFont('Helvetica','',8);
				$formulario->setY($formulario->getY()+6);
				$formulario->setX(115);
				$formulario->Cell(78,0.1,'',0,0,0,true,'');
				$formulario->setY($formulario->getY()-2);
				$formulario->setX(114);
				$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["nomeIndicou"]));
			}

			// responsável pela pré-inscrição
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('RESPONSÁVEL PELA PRÉ-INSCRIÇÃO'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(31);
			$formulario->Cell(81,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(30);
			$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["responsavelNome"]));

			if ($curso["tipo"] == Curso::CURSO_TIPO_POS) {
				$formulario->setY($formulario->getY()+12);
				$formulario->setX(30);
				$formulario->SetFont('Helvetica','B',9);
				$formulario->Cell(0,0,utf8_decode('UNIDADE/TURMA'));
				$formulario->SetFont('Helvetica','',8);
				$formulario->setY($formulario->getY()+2);
				$formulario->setX(31);
				$formulario->Cell(53,0.7,'',0,0,0,true,'');

				$unidadeTurma = decodificaDado($dadosPreinscricao['nomeUnidade']);
				if ($dadosPreinscricao["turma"] != "") {
					$unidadeTurma .= " " . $dadosPreinscricao["turma"];
				}

				// unidade
				$formulario->setY($formulario->getY()+6);
				$formulario->setX(30);
				$formulario->Cell(0,0,utf8_decode($unidadeTurma));

				$formulario->setY($formulario->getY()+8);
				$formulario->setX(30);
				$formulario->SetFont('Helvetica','B',9);
				$formulario->Cell(0,0,utf8_decode('INFORMAÇÕES DE PAGAMENTO'));
				$formulario->SetFont('Helvetica','',8);
				$formulario->setY($formulario->getY()+2);
				$formulario->setX(31);
				$formulario->Cell(53,0.7,'',0,0,0,true,'');

				// forma de pagamento
				$formulario->setY($formulario->getY()+7);
				$formulario->setX(30);
				$formulario->SetFont('Helvetica','B',8);
				$formulario->Cell(0,0,utf8_decode('FORMA DE PAGAMENTO'));
				$formulario->SetFont('Helvetica','',8);
				$formulario->setY($formulario->getY()+7);
				$formulario->setX(31);
				$formulario->Cell(90,0.1,'',0,0,0,true,'');
				$formulario->setY($formulario->getY()-2);
				$formulario->setX(30);
				$formulario->Cell(0,0,utf8_decode($dadosPreinscricao["formaPagamento"]));

				// banco
				$formulario->setY($formulario->getY()+7);
				$formulario->setX(30);
				$formulario->SetFont('Helvetica','B',8);
				$formulario->Cell(0,0,utf8_decode('BANCO'));
				$formulario->SetFont('Helvetica','',8);
				$formulario->setY($formulario->getY()+7);
				$formulario->setX(31);
				$formulario->Cell(70,0.1,'',0,0,0,true,'');
				$formulario->setY($formulario->getY()-2);
				$formulario->setX(30);

				if ($dadosPreinscricao["banco"] == Preinscricao::PREINSCRICAO_BANCO_BRADESCO) {
					$banco = 'Bradesco';
				}
				else if ($dadosPreinscricao["banco"] == Preinscricao::PREINSCRICAO_BANCO_ITAU) {
					$banco = 'Itaú';
				}
				else if ($dadosPreinscricao["banco"] == Preinscricao::PREINSCRICAO_BANCO_BANCO_DO_BRASIL) {
					$banco = 'Banco do Brasil';
				}
				else if ($dadosPreinscricao["banco"] == Preinscricao::PREINSCRICAO_BANCO_CAIXA_ECONOMICA_FEDERAL) {
					$banco = 'Caixa Econômica Federal';
				}
				else  {
					$banco = '';
				}

				$formulario->Cell(0,0,utf8_decode($banco));

				$diaPagamento = ((int) $dadosPreinscricao["diaPagamento"] != 0) ? $dadosPreinscricao["diaPagamento"] : "";

				// banco
				$formulario->setY($formulario->getY()+7);
				$formulario->setX(30);
				$formulario->SetFont('Helvetica','B',8);
				$formulario->Cell(0,0,utf8_decode('DIA DE PAGAMENTO'));
				$formulario->SetFont('Helvetica','',8);
				$formulario->setY($formulario->getY()+7);
				$formulario->setX(31);
				$formulario->Cell(30,0.1,'',0,0,0,true,'');
				$formulario->setY($formulario->getY()-2);
				$formulario->setX(30);
				$formulario->Cell(0,0, $diaPagamento);
			}

			$nomeArquivo = "Formulario-Preinscricao-" . implode("-", explode(" ",trim($dadosPreinscricao["nome"]))) . ".pdf";
			$path = DIR_ROOT . DS . 'administrar' . DS . "temp";

			if (is_writable($path)) {
				$path .=  DS . $nomeArquivo;
				//$formulario->Output();exit;
				$formulario->Output($path, "F");
				$this->log->adicionar ("gerou", "formulário", "inscrição", 
					"Usuário gerou formulário de inscrição do aluno " . $dadosPreinscricao["nome"] . ".");
				$conexao->commit();
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.basename($path).'"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($path));
				//ob_clean();
				flush();
				readfile($path);
				unlink($path);
				exit;
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
			setMensagem("error", $e->getMessage());
		}

		$conexao->disconnect();
		Application::redirect($_SERVER['HTTP_REFERER']);
		exit;

	}

	public function formulario2Action() {

        try {
        	// formulário de cursos de aperfeiçoamento
            $this->checaPermissao('preinscricoes', 'formulario');
            $conexao = $this->conexao->getConexao();

            $redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("acao", "id"), array(
                    "acao" => "visualizar"
                )
            );

            $cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);

			$curso = $cursoModel->findOrFail(getVariavel("curso"));
			$dados = $inscricaoModel->findOrFail(getVariavel("id"));

            if (!existeDiretorio(DIR_ROOT . DS . "temp")) {
                throw new Exception('Diretório não encontrado [' . DIR_ROOT . DS . "temp" . ']');
            }

            $copyPath = DIR_ROOT . DS . "temp" . DS . "Formulario-" . $dados["id"] . ".rtf";
            $nomeArquivo = DIR_ROOT . DS . "temp" . DS . "Formulario-Preinscricao-" . implode("-", explode(" ",trim($dados["nome"]))) . ".doc";

            $path = DIR_ROOT . DS . "contratos" . DS . "formulario-aperfeicoamento.rtf";

            if (!existeArquivo($path)) {
                throw new Exception ("Modelo de formulário não encontrado [" . basename($path) . "]");
            }

            if (!copy($path, $copyPath)) {
                throw new Exception ("Erro ao tentar copiar arquivo [" . basename($copyPath) . "]");
            }

            $texto = leef($copyPath);

            $texto = str_replace('#*NOME*#', utf8_decode($dados["nome"]), $texto);
            $texto = str_replace('#*SEXO*#', utf8_decode($dados["sexo"]), $texto);
            $texto = str_replace('#*CPF*#', $dados["cpf"], $texto);
            $texto = str_replace('#*RG*#', $dados["rg"], $texto);
            $texto = str_replace('#*ORGAO_EXPEDIDOR*#', utf8_decode($dados["orgaoExpedidor"]), $texto);
            $texto = str_replace('#*DATA2*#', $dados["dataExpedicao"], $texto);
            $texto = str_replace('#*CPF*#', $dados["cpf"], $texto);
            $texto = str_replace('#*ENDERECO*#', utf8_decode($dados["endereco"]), $texto);
            $texto = str_replace('#*NUMERO*#', $dados["numero"], $texto);
            $texto = str_replace('#*COMPLEMENTO*#', utf8_decode($dados["complemento"]), $texto);
            $texto = str_replace('#*BAIRRO*#', utf8_decode($dados["bairro"]), $texto);
            $texto = str_replace('#*TELEFONE1*#', $dados["telefoneResidencial"], $texto);
            $texto = str_replace('#*TELEFONE2*#', $dados["telefoneCelular"], $texto);
            $texto = str_replace('#*EMAIL*#', $dados["email"], $texto);
            $texto = str_replace('#*CEP*#', $dados["cep"], $texto);
            $texto = str_replace('#*CIDADE*#', utf8_decode($dados["cidade"]), $texto);
            $texto = str_replace('#*UF2*#', $dados["uf"], $texto);
            $texto = str_replace('#*E_CIVIL*#', $dados["estadoCivil"], $texto);
            $texto = str_replace('#*DATA1*#', $dados["dataNascimento"], $texto);
            $texto = str_replace('#*CURSO*#', utf8_decode(mb_strtoupper($curso["nome"], 'utf-8')), $texto);
            $texto = str_replace('#*DATA*#', date('d/m/Y'), $texto);
            $texto = str_replace('#*CIDADE_CURSO*#', utf8_decode($dados['unidade']['cidade']), $texto);
            $texto = str_replace('#*ANO_CONCLUSAO*#', utf8_decode($dados["anoConclusao"]), $texto);
            $texto = str_replace('#*FACULDADE_UNIVERSIDADE*#', utf8_decode($dados["instituicao"]), $texto);

            $novoContrato = fopen($nomeArquivo, 'w');
            fputs($novoContrato, $texto);
            fclose($novoContrato);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($nomeArquivo) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($nomeArquivo));
            //ob_clean();
            flush();
            readfile($nomeArquivo);
            unlink($nomeArquivo);
            unlink($copyPath);

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
        Application::redirect($redirecionar);
        exit;

    }

	public function contratoAction() {

		try {
			$this->checaPermissao('preinscricoes', 'contrato');
			$conexao = $this->conexao->getConexao();

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);

			$curso = $cursoModel->findOrFail(getVariavel("curso"));
			$dados = $inscricaoModel->findOrFail(getVariavel("id"));

			if (empty($dados["unidadeCertificadora"])) {
				throw new Exception("Impossível gerar contrato. A unidade certificadora não foi definida.");
			}

			if (empty($dados["formaPagamento"])) {
				throw new Exception("Impossível gerar contrato. A forma de pagamento não foi definida.");
			}

			if (empty($dados['idCidade'])) {
				throw new Exception("Impossível gerar contrato. A unidade não foi definida.");
			}

			if (empty($dados['valorCurso'])) {
				throw new Exception("Impossível gerar contrato. Valor do curso não definido na unidade [" . $dados["nomeUnidade"] . "]");
			}

			if (!existeDiretorio(DIR_ROOT . DS . "administrar" . DS . "temp")) {
				throw new Exception('Diretório não encontrado [' . DIR_ROOT . DS . "administrar" . DS . "temp" . ']');
			}

			$copyPath = DIR_ROOT . DS . "administrar" . DS . "temp" . DS . "Contrato-" . $dados["id"] . ".rtf";
			$nomeArquivo = DIR_ROOT . DS . "administrar" . DS . "temp" . DS . "Contrato-Preinscricao-" . implode("-", explode(" ",trim($dados["nome"]))) . ".doc";

			$especial = false;
			if (isset($_GET["f"])) {
				$especial = true;
			}

			$path = Preinscricao::getPathContrato($curso, $dados['nomeUnidade'], $dados, $especial);

			if (!existeArquivo($path)) {
				throw new Exception ("Modelo de contrato não encontrado para certificadora " . 
					Curso::getCertificadora($dados['unidadeCertificadora']));
			}

			if (!copy($path, $copyPath)) {
				throw new Exception ("Erro ao tentar copiar arquivo [" . basename($copyPath) . "]");
			}

			$texto = leef($copyPath);

			$enderecoCompleto = $dados["endereco"];

			if ($dados["numero"] != "") {
				$enderecoCompleto .= ", " . $dados["numero"];
			}
			if ($dados["complemento"] != "") {
				$enderecoCompleto .= ", " . $dados["complemento"];
			}
			if ($dados["bairro"] != "") {
				$enderecoCompleto .= ", " . $dados["bairro"];
			}
			if ($dados["cidade"] != "") {
				$enderecoCompleto .= " - " . $dados["cidade"];
			}
			if ($dados["uf"] != "") {
				$enderecoCompleto .= " - " . $dados["uf"];
			}
			if ($dados["cep"] != "") {
				$enderecoCompleto .= ", cep " . $dados["cep"];
			}

			$dados['dataNascimento'] = date('d/m/Y', strtotime($dados['dataNascimento']));

			$texto = str_replace('#*AREA*#', utf8_decode(Curso::getArea($curso["area"])), $texto);
			$texto = str_replace('#*NOME*#', utf8_decode($dados["nome"]), $texto);
			$texto = str_replace('#*CPF*#', $dados["cpf"], $texto);
			$texto = str_replace('#*RG*#', $dados["rg"] . " " . utf8_decode($dados["orgaoExpedidor"]), $texto);
			$texto = str_replace('#*UF1*#', $dados["ufExpedidor"], $texto);
			$texto = str_replace('#*CPF*#', $dados["cpf"], $texto);
			$texto = str_replace('#*NATURALIDADE*#', utf8_decode($dados["naturalidade"]), $texto);
			$texto = str_replace('#*ENDERECO*#', utf8_decode($dados["endereco"]), $texto);
			$texto = str_replace('#*ENDERECO_COMPLETO*#', utf8_decode($enderecoCompleto), $texto);
			$texto = str_replace('#*NUMERO*#', $dados["numero"], $texto);
			$texto = str_replace('#*COMPLEMENTO*#', utf8_decode($dados["complemento"]), $texto);
			$texto = str_replace('#*BAIRRO*#', utf8_decode($dados["bairro"]), $texto);
			$texto = str_replace('#*TELEFONE1*#', $dados["telefoneResidencial"], $texto);
			$texto = str_replace('#*TELEFONE2*#', $dados["telefoneCelular"], $texto);
			$texto = str_replace('#*EMAIL*#', $dados["email"], $texto);
			$texto = str_replace('#*CEP*#', $dados["cep"], $texto);
			$texto = str_replace('#*CIDADE*#', utf8_decode($dados["cidade"]), $texto);
			$texto = str_replace('#*UF2*#', $dados["uf"], $texto);
			$texto = str_replace('#*ESTADO_CIVIL*#', $dados["estadoCivil"], $texto);
			$texto = str_replace('#*PROFISSAO*#', utf8_decode($dados["profissao"]), $texto);
			$texto = str_replace('#*DATA_NASCIMENTO*#', $dados["dataNascimento"], $texto);
			$texto = str_replace('#*CURSO*#', utf8_decode(mb_strtoupper($curso["nome"], 'utf-8')), $texto);
			$texto = str_replace('#*DATA*#', date('d/m/Y'), $texto);
			$valorSemInsc = converteDecimal($dados['valorCurso']);
			$valorComInsc = converteDecimal($dados['valorCurso']) + converteDecimal($dados['valorInscricao']);
			$texto = str_replace('#*VALOR_CURSO*#', desconverteDecimal($valorComInsc), $texto);
			$texto = str_replace('#*VALOR*#', desconverteDecimal($valorSemInsc), $texto);
			$texto = str_replace('#*VALOR_MATRICULA*#', $dados['valorInscricao'], $texto);
			$texto = str_replace('#*DESCONTO*#', $dados['valorDesconto'], $texto);
			$texto = str_replace('#*FORMA_PAGAMENTO*#', utf8_decode('será dividido em ' . $dados["formaPagamento"]), $texto);
			$texto = str_replace('#*FORMA_PAGAMENTO2*#', utf8_decode($dados["formaPagamento"]), $texto);
			$texto = str_replace('#*CIDADE_CURSO*#', utf8_decode($dados['nomeUnidade']), $texto);
			$texto = str_replace('#*CARGA_HORARIA*#', $dados['cargaHoraria'], $texto);

			$novoContrato = fopen($nomeArquivo, 'w');
			fputs($novoContrato, $texto);
			fclose($novoContrato);

			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . basename($nomeArquivo) . '"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($nomeArquivo));
			//ob_clean();
			flush();
			readfile($nomeArquivo);
			unlink($nomeArquivo);
			unlink($copyPath);

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

	public function financeiroAction() {

        try {
            $this->checaPermissao('preinscricoes', 'financeiro');
            $conexao = $this->conexao->getConexao();

            $cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);

			$curso = $cursoModel->findOrFail(getVariavel("curso"));
			$dados = $inscricaoModel->findOrFail(getVariavel("id"));

            if (empty($dados['idCidade'])) {
                throw new Exception("Impossível gerar acordo financeiro. A unidade não foi definida.");
            }

            if (empty($dados["formaPagamento"])) {
                throw new Exception("Impossível gerar acordo financeiro. A forma de pagamento não foi definida.");
            }

            if (empty($dados["valorCurso"])) {
                throw new Exception("Impossível gerar acordo financeiro. Valor do curso não definido na cidade [" . $dados["cidadeCurso"] . "]");
            }

            //$cidadeCurso = $this->dao->findByPk ($conexao, "cursos_estados_cidades", 
            //	(int) $dados["cidadeCurso"]);

            $copyPath = DIR_ROOT . DS . "administrar" . DS . 
            	"temp" . DS . "Acordo-" . $dados["id"] . ".rtf";
            $nomeArquivo = DIR_ROOT . DS . "administrar" . DS .
            	"temp" . DS . "Acordo-Financeiro-" . implode("-", explode(" ",trim($dados["nome"]))) . ".doc";

            $path = DIR_ROOT . DS . "administrar" . DS . 
            	"contratos" . DS . "acordo-financeiro";

            if ($curso["tipo"] == Curso::CURSO_TIPO_APERFEICOAMENTO) {
                $path .= "-aperfeicoamento";
            }
            else {
            	// verifica se existe acordo financeiro definido para a cidade
            	$cidade = strtolower(removeAcentos($dados['nomeUnidade']));
            	$cidade = preg_replace('/ /', '-', $cidade);
				if (existeArquivo($path . "-" . $cidade . ".rtf")) {
					$path .= "-" . $cidade;
				}
            }

            $path .= ".rtf";

            if (!existeArquivo($path)) {
                throw new Exception ("Modelo de acordo financeiro não encontrado [" . basename($path) . "]");
            }

            if (!copy($path, $copyPath)) {
                throw new Exception ("Erro ao tentar copiar arquivo [" . basename($copyPath) . "]");
            }

            $texto = leef($copyPath);

            $texto = str_replace('#*NOME*#', utf8_decode($dados["nome"]), $texto);
            $texto = str_replace('#*CPF*#', $dados["cpf"], $texto);
            $texto = str_replace('#*CIDADE*#', utf8_decode($dados['nomeUnidade']), $texto);
            $texto = str_replace('#*ESTADO*#', $dados['uf'], $texto);
            $texto = str_replace('#*CURSO*#', utf8_decode($dados["nomeCurso"]), $texto);
            //$valorCurso = $dados['unidade']['valorCurso'] + $dados['unidade']['valorInscricao'];
            $valorCurso = $dados['valorCurso'];
            $texto = str_replace('#*VALOR_CURSO*#', $valorCurso, $texto);
            $texto = str_replace('#*VALOR*#', $valorCurso, $texto);
            $texto = str_replace('#*VALOR_MATRICULA*#', $dados['valorInscricao'], $texto);
            $texto = str_replace('#*FORMA_PAGAMENTO*#', utf8_decode($dados["formaPagamento"]), $texto);
            $texto = str_replace('#*TURMA*#', utf8_decode($dados["turma"]), $texto);
            $texto = str_replace('#*CIDADE_CURSO*#', utf8_decode($dados['nomeUnidade']), $texto);
            $texto = str_replace('#*ESTADO_CURSO*#', utf8_decode($dados['siglaEstado']), $texto);

            $novoContrato = fopen($nomeArquivo, 'w');
            fputs($novoContrato, $texto);
            fclose($novoContrato);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($nomeArquivo) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($nomeArquivo));
            //ob_clean();
            flush();
            readfile($nomeArquivo);
            unlink($nomeArquivo);
            unlink($copyPath);

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

	public function emailAction() {
		try {

		 	// só eu
		 	if ($_SESSION[PREFIX . "loginId"] != 1) {
		 		throw new PermissaoException('Você não tem permissão para realizar esta ação');
		 	}

		 	$conexao = $this->conexao->getConexao();

			$inscricaoModel = new Preinscricao($conexao);

			$dados = Funcoes::arrayToObject($inscricaoModel->findOrFail(getVariavel("id")));

	 		if ($_GET["tipo"] == 1) {
	 			$htmlEmail = EmailSite::interesse($dados, false);
	 		}
	 		else if ($_GET["tipo"] == 2) {
	 			$htmlEmail = EmailSite::inscricao($dados, false);
	 		}
	 		else {
	 			$htmlEmail = EmailSite::matricula($dados, false);
	 		}

		 	$breadcrumbs = array();
	 		$breadcrumbs[] = array(
				"Inscrições" => "?modulo=preinscricoes",
	 			$dados->nomeCurso => sprintf("?modulo=preinscricoes&acao=visualizar&curso=%d&tab1=%d", 
					$dados->idCurso, $_GET['tab1']),
				$dados->nome => sprintf("?modulo=preinscricoes&acao=cadastrar&curso=%d&tab1=%d&id=%d", 
					$dados->idCurso, $_GET['tab1'], $dados->id),
	 			"E-mail" => ""
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
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}	

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", "email.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"email" => $htmlEmail
			)
		);
		$view->showContents();
	}

	public function statusAction() {
		try {
			$this->checaPermissao('preinscricoes', 'status');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("id", "acao"), array(
					"acao" => "visualizar"
				)
			);

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);
			$historicoModel = new HistoricoPreinscricao($conexao);

			$curso = $cursoModel->findOrFail(getVariavel("curso"));
			$dados = $inscricaoModel->findOrFail(getVariavel("id"));

			$nome = $dados["nome"];
			$statusAntigo = $dados["status"];

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Inscrições" => "?modulo=preinscricoes",
				$curso["nome"] => sprintf("?modulo=preinscricoes&acao=visualizar&curso=%d&tab1=%d", 
					$curso["id"], $_GET['tab1']),
				$dados["nome"] => sprintf("?modulo=preinscricoes&acao=cadastrar&curso=%d&tab1=%d&id=%d", 
					$curso["id"], $_GET['tab1'], $dados['id']),
				"Mudar status" => ""
			);

			if (count($_POST) > 0) {
				$dados = $_POST;
				if ($statusAntigo != $_POST["status"]) {
					$this->dao->salva($conexao, "preinscricoes", $dados);
					// adiciona nos logs
					$this->log->adicionar ("atualizou", "status", "inscrição", 
						sprintf("Usuário atualizou status da inscrição de %s do curso %s para %s.", $nome, $curso["nome"], Preinscricao::getStatus($dados["status"])));
					// adiciona no histórico
					$historicoModel->adicionar($dados["id"], sprintf("O status foi atualizado de %s para %s", Preinscricao::getStatus($statusAntigo), Preinscricao::getStatus($dados["status"])));
					$conexao->commit();
					setMensagem("info", "Status atualizado [" . $nome . "]");
				}
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
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
			if ($redirecionar != NULL) {
				Application::redirect($redirecionar);
				exit;
			}
		}

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", "status.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"preinscricao" => $dados,
				"curso" => $curso
			)
		);
		$view->showContents();
	}

	public function filtroAction() {
		try {
			$this->checaPermissao('preinscricoes', 'filtro');
			$conexao = $this->conexao->getConexao();

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Inscrições" => "?modulo=preinscricoes",
				"Filtro" => ""
			);
			$quantidadePorPagina = isset($_GET["exibir"]) ? (int) $_GET["exibir"] : QUANTIDADE_POR_PAGINA;
			$quantidadePorPagina = ($quantidadePorPagina <= 0) ? QUANTIDADE_POR_PAGINA : $quantidadePorPagina;
			$pagina = isset($_GET["p"]) ? $_GET["p"] : 1;
			$pagina = $pagina <= 0 ? 1 : $pagina;
			$limit = $pagina == 1 ? $quantidadePorPagina : $quantidadePorPagina * ($pagina - 1);
			$offset = $pagina == 1 ? 0 : $quantidadePorPagina;
			$order = array(
				"data" => "desc"
			);

			if (isset($_GET['order'])) {
				$order = array(
					$_GET['order'] => 'asc'
				);
			}

			$cursoModel = new Curso($conexao);
			$unidadesModel = new Cidade($conexao);
			$usuarioModel = new Usuario($conexao);
			$cursos = $cursoModel->getQuery()
				->order('nome', 'asc')
				->all();
			$usuarios = $usuarioModel->getQuery()
				->order('nome', 'asc')
				->where('status = 1')
				->all();
			$unidades = $unidadesModel->getQuery()
				->order('nome', 'asc')
				->all();	

			$quantidade = 0;
			$objetos = array();

			$model = new Preinscricao($conexao);

			$query = $model->getQuery();
			$query1 = clone $query;
			$query2 = clone $query;

			$quantidade = $query1->count();
			$objetos = $query2
				->limitIn($limit, $offset)
				->order('data', 'desc')
				->all();

			$pages = new Paginator($quantidadePorPagina, 'p');
	        $pages->setTotal($quantidade);
	        $pageLinks = $pages->pageLinks('?modulo=preinscricoes&acao=filtro&');
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
		$view = new View2($_GET["modulo"], "extendido", "filtro.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"objetos" => $objetos,
				"quantidade" => $quantidade,
				"quantidadePorPagina" => $quantidadePorPagina,
				"pagina" => $pagina,
				"breadcrumbs" => $breadcrumbs,
				"usuarios" => $usuarios,
				"cursos" => $cursos,
				"unidades" => $unidades,
				'paginacao' => $pageLinks
			)
		);
	    $view->showContents();
	}

	public function documentacaoAction() {

		try {
			$this->checaPermissao('preinscricoes', 'situacao');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"]);

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);
			$historicoModel = new HistoricoPreinscricao($conexao);

			$curso = $cursoModel->findOrFail(getVariavel("curso"));
			$dados = $inscricaoModel->findOrFail(getVariavel("id"));

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Inscrições" => "?modulo=preinscricoes",
				$curso["nome"] => sprintf("?modulo=preinscricoes&acao=visualizar&curso=%d&tab1=%d", 
					$curso["id"], $_GET['tab1']),
				$dados["nome"] => sprintf("?modulo=preinscricoes&acao=cadastrar&curso=%d&tab1=%d&id=%d", 
					$curso["id"], $_GET['tab1'], $dados['id']),
				"Documentação" => ""
			);

			// recupero as situações da pré-inscrição
			$todasSituacoes = $this->dao->findAll($conexao, "preinscricoes_situacao", array(
					"where" => array(
						"preinscricao" => (int) $dados["id"]
					)
				)
			);

			$situacoes = array();
			foreach ($todasSituacoes as $key => $value) {
				$situacoes[$value["item"]] = $value["situacao"];
			}

			if (count($_POST) > 0) {

				$preinscricaoSituacoes = array();
				$itensDisponiveis = PreinscricaoSituacao::getItens();

				$itens = isset($_POST["itens"]) ? $_POST["itens"] : array();

				foreach ($itens as $item) {

					$p = $conexao->query()
						->from("preinscricoes_situacao")
						->where("preinscricao = ?", (int) $dados["id"])
						->where("item = ?", (int) $item)
						->first();

					if (!$p) {
						$timestamp = time();
						$conexao->query()
							->from("preinscricoes_situacao")
							->save(array(
									"preinscricao" => (int) $dados["id"],
									"item" => (int) $item,
									"situacao" => 1,
									"data" => getData($timestamp),
									"timestamp" => $timestamp
								)
							);
						$historicoModel->adicionar($dados["id"], 
							sprintf("Marquei entrega do(a) %s", PreinscricaoSituacao::getNomeItem($item)));
					}
					else if ($p["situacao"] == 0){
						$p["situacao"] = 1;
						$conexao->query()
							->from("preinscricoes_situacao")
							->where("id = ?", (int) $p["id"])
							->save(array(
									"situacao" => (int) $p["situacao"]
								)
							);
						$historicoModel->adicionar($dados["id"], 
							sprintf("Marquei entrega do(a) %s", PreinscricaoSituacao::getNomeItem($item)));
					}
				}

				foreach ($itensDisponiveis as $key => $value) {

					$p = $conexao->query()
						->from("preinscricoes_situacao")
						->where("preinscricao = ?", (int) $dados["id"])
						->where("item = ?", (int) $key)
						->first();

					if ($p && !in_array($key, $itens) && $p["situacao"] == 1) {
						$p["situacao"] = 0;
						$conexao->query()
							->from("preinscricoes_situacao")
							->where("id = ?", (int) $p["id"])
							->save(array(
									"situacao" => (int) $p["situacao"]
								)
							);
						$historicoModel->adicionar($dados["id"], 
							sprintf("Desmarquei entrega do(a) %s", PreinscricaoSituacao::getNomeItem($p['item'])));
					}
				}

				setMensagem("info", "Documentação atualizada");

				// adciciona nos logs
				$this->log->adicionar ("atualizou", "documentação", $dados["nome"], 
					sprintf("Usuário atualizou documentação da inscrição de %s no curso %s", $dados["nome"], $curso["nome"]));

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
		catch (Exception $e) {
			setMensagem("error", $e->getMessage());
			if ($redirecionar != NULL) {
				Application::redirect($redirecionar);
				exit;
			}
		}

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", "situacao.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"preinscricao" => $dados,
				"situacoes" => $situacoes
			)
		);
		$view->showContents();

	}

	// Cria uma pré-inscrição de exemplo
	public function criarAction() {
		try {

			// só eu
			if ($_SESSION[PREFIX . "loginId"] != 1) {
				throw new PermissaoException('Você não tem permissão para realizar esta ação');
			}

			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("acao"), array(
					"acao" => "visualizar"
				)
			);

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);
			$historicoModel = new HistoricoPreinscricao($conexao);

			$curso = $cursoModel->findOrFail(getVariavel("curso"));

			$timestamp = time();

			$dados = array(
				"id" => 0,
				"curso" => (int) $curso["id"],
				"nome" => "Nome exemplo",
				"rg" => "9281928",
				"orgaoExpedidor" => "SSP",
				"ufExpedidor" => "PB",
				"dataExpedicao" => "2000-01-01",
				"cpf" => "094.982.162-45",
				"dataNascimento" => "2000-01-01",
				"sexo" => "Masculino",
				"estadoCivil" => "Solteiro",
				"naturalidade" => "Naturalidade",
				"nomePai" => "Nome do pai",
				"nomeMae" => "Nome da mãe",
				"profissao" => "Profissão",
				"endereco" => "Endereço",
				"numero" => "200",
				"complemento" => "Complemento",
				"bairro" => "Bairro",
				"cidade" => "Cidade",
				"uf" => "AL",
				"cep" => "57000-348",
				"telefoneResidencial" => "(83)9209-0303",
				"operadoraCelular" => "Claro",
				"telefoneCelular" => "(83)9209-0303",
				"email" => "email@gmail.com",
				"formacao" => "Formação",
				"instituicao" => "Instituição",
				"anoConclusao" => "2000",
				"status" => 1,
				"data" => getData($timestamp),
				"timestamp" => $timestamp,
				"responsavel" => $_SESSION[PREFIX . "loginId"]
			);


			$dados = $this->dao->salva($conexao, "preinscricoes", $dados);

			$historicoModel->adicionar($dados["id"], "Inscrição criada manualmente");

			$this->log->adicionar ("criou", "inscrição", "exemplo", "Inscrição criada manualmente");

			setMensagem("info", "Inscrição exemplo criada");

			$conexao->commit();
			$conexao->disconnect();
			Application::redirect($redirecionar);
			exit;
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
			if ($redirecionar != NULL) {
				Application::redirect($redirecionar);
				exit;
			}
		}
	}

	public function listExcelAction() {
		try {
			$this->checaPermissao('preinscricoes', 'planilha');
			$conexao = $this->conexao->getConexao();

			$filtro = array(
				"idCurso",
				"nomeCurso",
				"nome",
				"responsavel",
				"responsavelNome",
				"status",
				"tipoSituacao",
				"motivo",
				"nomeUnidade",
				"siglaEstado",
				"telefone",
				"telefoneResidencial",
				"telefoneCelular",
				"observacoes",
				"data"
			);

			list($dia, $mes, $ano) = empty($_GET["dataInicial"]) ? explode("/", date('d/m/Y')) : explode("/", $_GET["dataInicial"]);
			$timestampInicial = mktime(0, 0, 0, $mes, $dia, $ano);
			$dataInicial = $ano . "-" . $mes . "-" . $dia;

			list($dia, $mes, $ano) = empty($_GET["dataFinal"]) ? explode("/", date('d/m/Y')) : explode("/", $_GET["dataFinal"]);
			$timestampFinal = mktime(0, 0, 0, $mes, $dia, $ano);
			$dataFinal = $ano . "-" . $mes . "-" . $dia;

			$inscricaoModel = new Preinscricao($conexao);
			$query = $inscricaoModel->getQuery('vw_preinscricoes_v3 p', $filtro);

			if (!empty($_GET['nome'])) {
				$query->where('p.nome LIKE ?', '%' . $_GET['nome'] . '%');
			}

			if (!empty($_GET['idCurso'])) {
				$query->where('p.idCurso = ?', (int) $_GET['idCurso']);
			}

			if (!empty($_GET['idCidade'])) {
				$query->where('p.idCidade = ?', $_GET['idCidade']);
			}

			if (!empty($_GET['siglaEstado'])) {
				$query->where('p.siglaEstado = ?', $_GET['siglaEstado']);
			}

			if (!empty($_GET['responsavel'])) {
				$query->where('p.responsavel = ?', (int) $_GET['responsavel']);
			}

			if (!empty($_GET['status'])) {
				$status = array_map('intval', explode(',', $_GET['status']));
				$query->where('p.status IN (' . implode(',', $status) . ')');
			}

			if (!empty($_GET['situacao'])) {
				if ((int) $_GET['situacao'] == -1) {
					$query1 = clone $query;
					$query2 = clone $query;

					$query1->where('p.dataRetorno = ?', date('Y-m-d'));
					$query2
						->where('p.dataRetorno <> ?', date('Y-m-d'))
						->where('p.dataRetorno IS NOT NULL')
						->order('p.dataRetorno', 'asc');

				}
				else {
					$situacao = array_map('intval', explode(',', $_GET['situacao']));
					$query->where('p.tipoSituacao IN (' . implode(',', $situacao) . ')');
				}
			}

			if ($timestampFinal > $timestampInicial) {
				$query->where('p.data BETWEEN ? and ?', $dataInicial, $dataFinal);
			}

			if (!empty($_GET['situacao']) && (int) $_GET['situacao'] == -1) {
				$objetos = array_merge($query1->all(), $query2->all());
			}
			else {
				$objetos = $query
					->order('p.data', 'desc')
					->all();
			}
			
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("IEFAP");
			$objPHPExcel->getProperties()->setLastModifiedBy("IEFAP");
			$objPHPExcel->getProperties()->setTitle("Filtro de inscrições");
			$objPHPExcel->getProperties()->setSubject("Filtro de inscrições");
			$objPHPExcel->getProperties()->setDescription("IEFAP - Filtro de inscrições");
			$objPHPExcel->setActiveSheetIndex(0);

			$dadosIn = array(
				"Curso" => "nomeCurso",
				"Nome" => "nome",
				"Responsável" => "responsavelNome",
				"Status" => "status",
				"Situação" => "tipoSituacao",
				"Motivo" => "motivo",
				"Unidade" => "nomeUnidade",
				"Sigla" => "siglaEstado",
				"Telefone" => "telefone",
				"Tel. Residencial" => "telefoneResidencial",
				"Tel. Celular" => "telefoneCelular",
				"Observações" => "observacoes",
				"Data" => "data"
			);

			$numeroLinha = 1;
			$numeroColuna = 0;

			foreach ($dadosIn as $key => $f) {
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, 
					$numeroLinha, $key, false);
			}

			$numeroLinha = 2;
			$numeroColuna = 0;

			foreach ($objetos as $indice => $dados) {
				$objetos[$indice] = array_map('decodificaDado', $dados);
				foreach ($dadosIn as $key => $dadoIn) {
					if ($dadoIn == "status") {
						$objetos[$indice][$dadoIn] = Preinscricao::getStatus($objetos[$indice][$dadoIn]);
					}
					if ($dadoIn == "tipoSituacao") {
						$objetos[$indice][$dadoIn] = Situacao::getTipo($objetos[$indice][$dadoIn]);
					}
					if ($dadoIn == "data") {
						$objetos[$indice][$dadoIn] = date('d/m/Y', strtotime($objetos[$indice][$dadoIn]));
					}
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, 
						$numeroLinha, $objetos[$indice][$dadoIn], false);
				}
				$numeroLinha++;
				$numeroColuna = 0;
			}

			$nomeArquivo = "Listagem-Filtro-Preinscricoes.xlsx";
			$path = DIR_ROOT . DS . "administrar" . DS . "temp";

			if (is_writable($path)) {
				$path .=  DS . $nomeArquivo;
				$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
				$objWriter->save($path);
				$this->log->adicionar ("gerou", "listagem", "EXCEL", 
					"Usuário gerou listagem de pré-inscrições em EXCEL através do filtro de pré-inscrições.");
				$conexao->commit();
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="' . basename($path) . '"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($path));
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
			setMensagem("error", $e->getMessage());
		}

		$conexao->disconnect();
		Application::redirect($_SERVER['HTTP_REFERER']);
		exit;
	}

	public function opcoesAction() {
		try {

			$conexao = $this->conexao->getConexao();

			$processados = 0;
			$naoProcessados = 0;
			
			if (empty($_POST["acoes"])) {
				throw new Exception("É necessário escolher uma ação");
			}

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);
			$curso = $cursoModel->findOrFail(getVariavel('curso'));
			
			$objetos = isset($_POST["objetos"]) ? $_POST["objetos"] : array();
			
			// retira o elemento -1, caso exista
			if (count($objetos) > 0 && $objetos[0] == -1) {
				array_shift($objetos);
			}
			
			switch ($_POST["acoes"]) {

				case "excluir" :

					$this->checaPermissao($this->info["modulo"], 'excluir');

					foreach ($objetos as $id) {
						$opcao = 'excluída(s)';
						$inscricao = $inscricaoModel->findOrFail($id);
						$affectedRows = $inscricaoModel->delete2($inscricao);
						if ($affectedRows > 0) {
							$processados++;
							$diretorio = DIR_UPLOADS . DS . "comprovantes" . DS . $inscricao['id'];
							excluiDiretorio($diretorio);
							$this->log->adicionar ("excluiu", "inscrição", $inscricao['nome'], 
								sprintf("Usuário excluiu inscrição do curso %s através do recurso de aplicar ações em massa", 
									$inscricao['nomeCurso']));
						}
					}

				break;

				case "moodle" :
					
					$this->checaPermissao($this->info["modulo"], 'moodle');

					foreach ($objetos as $id) {

						$inscricao = $inscricaoModel->findOrFail($id);
					
						$path = DIR_ROOT . DS . "administrar" . DS . "temp" . DS;
						$path .= 'Inscrever-' . implode("-", explode(" ",trim($curso['nome']))) . '.txt';
						$escape = "\r\n";
						$separador = ";";

						if (!file_exists($path)) {
							$fp = fopen($path, "w");
							$line = "username%spassword%sfirstname%slastname%semail%sphone1%sphone2%scity%scountry";
							$txt = sprintf($line, $separador, $separador, $separador, $separador, $separador, $separador, 
								$separador, $separador);
							fwrite($fp, $txt);
							fwrite($fp, $escape);
						}
						else {
							$fp = fopen($path, "a");
						}

						$cpf = $inscricao["cpf"];

						if (empty($cpf)) {
							throw new Exception('O cpf da inscrição de ' . $inscricao["nome"] . ' não está definido. O cpf é usado como login no moodle!');
						}

						$cpf = preg_replace('/(\.|-)/', '', $cpf);

						if (count(explode(';', $inscricao["email"])) > 1) {
							throw new Exception('O aluno ' . $inscricao["nome"] . ' deve definir apenas um e-mail.');
						}

						$nome = $inscricao["nome"];

						$nomes = explode(' ', $nome);
						$firstname = array_shift($nomes);
						$lastname = implode(' ', $nomes);

						$line = '%s;%s;%s;%s;%s;%s;%s;%s;%s';
						
						$txt = sprintf($line, $cpf, 'IEFAP12345', trim($firstname), 
							trim($lastname), trim($dados["email"]), 
							$dados["telefoneResidencial"], $dados["telefoneCelular"], 
							trim($dados["cidade"]), 'BR');

						fwrite($fp, $txt);
						fwrite($fp, $escape);
						fclose($fp);
					}

					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="' . basename($path) . '"');
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate');
					header('Pragma: public');
					header('Content-Length: ' . filesize($path));
					flush();
					readfile($path);
					unlink ($path);

				break;

				case "academico" :

					$this->checaPermissao($this->info["modulo"], 'academico');

					$path = DIR_ROOT . DS . "administrar" . DS . "temp" . DS;

					foreach ($objetos as $id) {

						$inscricao = $inscricaoModel->findOrFail($id);

						$count = function($path, $nome) {
							$i = 0;
							if ($handle = opendir($path)) {
						        while (($file = readdir($handle)) !== false){
						        	list($name, $ext) = explode(".", $file);
						        	if (!empty($name)) {
							            if (strpos($file, $name) !== false) {
							            	$i++;
							            }
						           	}
						        }
						    }
						    return $i+1;
						};

						$nomeArquivo = implode("-", explode(" ",trim($curso["nome"])));
						$nomeArray = explode(" ", $inscricao["nome"]);
						if (isset($nomeArray[0])) {
							$nomeArquivo .= '-' . $nomeArray[0];
						}
						if (isset($nomeArray[1])) {
							$nomeArquivo .= '-' . $nomeArray[1];
						}
						$nomeArquivo .= '-' . $count($path, $nomeArquivo);
						$nomeArquivo .= '.txt';

						$pathFile = $path . $nomeArquivo;

						$escape = "\r\n";
						$separador = ";";

						$fp = fopen($pathFile, "w");
						$string = "nome%ssexo%sestadoCivil%sdataNascimento%srg%sorgaoExpedidor%sufExpedidor%s";
						$string .= "dataExpedicao%scpf%sendereco%snumero%scomplemento%sbairro%scidade%suf%scep%s";
						$string .= "telefoneResidencial%stelefoneCelular%semail%semailAlternativo%s";
						$string .= "naturalidade%snomePai%snomeMae%sformacao%sinstituicao%sanoConclusao%s";
						$string .= "unidade%sturma%sstatus%sresponsavel";
						$txt = sprintf($string, $separador, $separador, $separador, $separador, $separador, 
							$separador, $separador, $separador, $separador, $separador, $separador, 
							$separador, $separador, $separador, $separador, $separador, $separador, 
							$separador, $separador, $separador, $separador, $separador, $separador, 
							$separador, $separador, $separador, $separador, $separador, $separador);
						fwrite($fp, $txt);
						$txt = $escape;
						fwrite($fp, $txt);

						$string = "%s%s%s%s%s%s%s%s%s%s%s%s%s%s";
						$string .= "%s%s%s%s%s%s%s%s%s%s%s%s%s%s%s%s%s%s";
						$string .= "%s%s%s%s%s%s%s%s";
						$string .= "%s%s%s%s%s%s%s%s%s%s%s%s";
						$string .= "%s%s%s%s%s%s%s";

						$txt = sprintf($string, $inscricao["nome"], $separador, $inscricao["sexo"],
							$separador, $inscricao["estadoCivil"], $separador, $inscricao["dataNascimento"],
							$separador, $inscricao["rg"], $separador, $inscricao["orgaoExpedidor"], 
							$separador, $inscricao["ufExpedidor"], $separador, $inscricao["dataExpedicao"],
							$separador, $inscricao["cpf"], $separador, $inscricao["endereco"], 
							$separador, $inscricao["numero"], $separador, $inscricao["complemento"], 
							$separador, $inscricao["bairro"], $separador, $inscricao["cidade"], 
							$separador, $inscricao["uf"], $separador, $inscricao["cep"],
							$separador, $inscricao["telefoneResidencial"], $separador, $inscricao["telefoneCelular"], 
							$separador, $inscricao["email"], $separador, $inscricao["emailAlternativo"], 
							$separador, $inscricao["naturalidade"], $separador, $inscricao["nomePai"], 
							$separador, $inscricao["nomeMae"], $separador, $inscricao["formacao"], 
							$separador, $inscricao["instituicao"], $separador, $inscricao["anoConclusao"], 
							$separador, $inscricao["unidade"], $separador, $inscricao["turma"], 
							$separador, $inscricao["status"], $separador, $inscricao["quemNome"],
							$separador);
						
						fwrite($fp, $txt);
						fwrite($fp, $escape);
						fclose($fp);
						$filesZip[] = $pathFile;
					}

					if (count($filesZip) > 0) {
						$pathZip = $path . 'Academico-' . implode("-", explode(" ",trim($curso["nome"]))) . '.zip';
						create_zip($filesZip, $pathZip, true);
						foreach ($filesZip as $f) {
							unlink($f);
						}
						header('Content-Description: File Transfer');
						header('Content-Type: application/octet-stream');
						header('Content-Disposition: attachment; filename="' . basename($pathZip) . '"');
						header('Content-Transfer-Encoding: binary');
						header('Expires: 0');
						header('Cache-Control: must-revalidate');
						header('Pragma: public');
						header('Content-Length: ' . filesize($pathZip));
						flush();
						readfile($pathZip);
						unlink ($pathZip);
					}

				break;

				default :

					foreach ($objetos as $id) {
						$inscricao = $inscricaoModel->findOrFail($id);
						$opcao = "atualizada(s)";
						$opcaoItem = explode(",", $_POST["acoes"]);
						$atualizar = $opcaoItem[0];
						$valor = preg_match('/\d/', $opcaoItem[1]) ? (int) $opcaoItem[1] : $opcaoItem[1];
						
						$affectedRows = $conexao->query()->from('preinscricoes')
							->where('id = ?', (int) $inscricao['id'])
							->save(array(
									$atualizar => $valor
								)
							);

						if ($affectedRows > 0) {
							$processados += 1;
							$this->log->adicionar ("atualizou", "inscrição", $inscricao['nome'], 
								sprintf("Usuário atualizou campo %s na inscrição do curso %s pelo recurso de aplicar ações em massa", 
									$atualizar, $inscricao['nomeCurso']));
						}
					}

				break;
			}
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}

		if ($processados > 0) {
			$conexao->commit();
			setMensagem("info", $processados. " inscrição(ões) " . $opcao);
		}

		$conexao->disconnect();
		Application::redirect($_SERVER['HTTP_REFERER']);
		exit;

	}

	public function downloadAction () {

		try {

			$conexao = $this->conexao->getConexao();
			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);

			$curso = $cursoModel->findOrFail(getVariavel('curso'));
			$inscricao = $inscricaoModel->findOrFail(getVariavel('id'));

			$diretorio = DIR_UPLOADS . DS . "comprovantes";
			$diretorio .= DS . $inscricao["id"];
			$diretorio .= DS . base64_decode($inscricao["comprovante"]);

			if (!existeArquivo($diretorio)) {
				throw new Exception('Comprovante não encontrado');
			}

			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . base64_decode($inscricao["comprovante"]) . '"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($diretorio));
			flush();
			readfile($diretorio);

			// adiciona nos logs
			$this->log->adicionar ("realizou", "download", "comprovante", 
				"Usuário fez download do comprovante de pagamento da taxa de inscrição do aluno(a) " . $inscricao["nome"] . ", do curso " . $curso["nome"] . ".");
			// comita transação
			$conexao->commit();
		}
		catch (Exception $e) {
			setMensagem("error", $e->getMessage());
			Application::redirect($redirecionar);
			exit;
		}
	}

	public function cadastrarAction() {

		try {
			$this->checaPermissao('preinscricoes', 'cadastrar');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("id", "acao"), array(
					"acao" => "visualizar"
				)
			);

			$cursoModel = new Curso($conexao);
			$inscricaoModel = new Preinscricao($conexao);
			$usuarioModel = new Usuario($conexao);

			$dados = $inscricaoModel->findOrFail(getVariavel('id'));
			$curso = $cursoModel->findOrFail(getVariavel('curso'));
			$cursos = $cursoModel->getQuery()
				->order('nome', 'asc')
				->all();
			$usuarios = $usuarioModel->getQuery()
				->order('nome', 'asc')
				->where('status = 1')
				->all();

			// armazena nome e status
			$nome = $dados["nome"];
			$statusAntigo = $dados["status"];

			if ($dados['visualizada'] == 0 && (int) $_SESSION[PREFIX . "loginId"] != 1) {
				$conexao->query()
					->from("preinscricoes")
					->where("id = ?", (int) $dados["id"])
					->save(array(
						'visualizada' => 1
					)
				);
				$time = time();
				$conexao->query()
					->from("historico")
					->save(array(
						'preinscricao' => (int) $dados["id"],
						'descricao' => codificaDado('Visualizou a inscrição pela primeira vez'),
						'quem' => codificaDado($_SESSION[PREFIX . "loginNome"]),
						'data' => date('Y-m-d H:i:s', $time),
						'timestamp' => $time
					)
				);
				$conexao->commit();
			}
			
			$breadcrumbs[] = array(
				"Inscrições" => "?modulo=preinscricoes",
				$curso["nome"] => sprintf("?modulo=preinscricoes&acao=visualizar&curso=%d&tab1=%d", $curso["id"], $_GET['tab1']),
				"Atualizar" => ""
			);

			if (count($_POST) > 0) {

    			$redirecionar = NULL;
    			$dadosIn = $dados = $_POST;
    			$dadosIn["formaPagamento"] = isset($dadosIn["formaPagamento"]) ? $dadosIn["formaPagamento"] : NULL;
    			$dadosIn["unidade"] = isset($dadosIn["unidade"]) ? $dadosIn["unidade"] : 0;

    			$dadosIn = $this->_valida($dadosIn);

    			$dadosIn = $this->dao->salva($conexao, "preinscricoes", $dadosIn);

    			$time = time();

    			// adiciona registro no histórico
    			$this->dao->salva($conexao, "historico", array(
						"id" => 0,
						"preinscricao" => $dadosIn["id"],
						"descricao" => "Atualizou a inscrição",
						"quem" => $_SESSION[PREFIX . "loginNome"],
						"timestamp" => $time,
						"data" => date('d/m/Y H:i:s', $time)
					)
				);

				if ($statusAntigo != $dadosIn["status"]) {
					$this->dao->salva($conexao, "historico", array(
							"id" => 0,
							"preinscricao" => $dadosIn["id"],
							"descricao" => "O status foi atualizado de " . Preinscricao::getStatus($statusAntigo) . " para " . Preinscricao::getStatus($dadosIn["status"]),
							"quem" => $_SESSION[PREFIX . "loginNome"],
							"timestamp" => $time,
							"data" => date('d/m/Y H:i:s', $time)
						)
					);
				}

				// fazer envio do comprovante
				$enviouComprovante = false;
				if (isset($_FILES["comprovante"]) && enviouArquivo($_FILES["comprovante"])) {
					$enviouComprovante = true;
					$this->_enviaComprovante($conexao, $dadosIn, $_FILES["comprovante"]);
					$dadosIn["enviouComprovante"] = 1;
					$dadosIn["comprovante"] = base64_encode($_FILES["comprovante"]["name"]);
					$dadosIn["mime"] = $_FILES["comprovante"]["type"];
					$dadosIn["extensao"] = pathinfo($_FILES["comprovante"]["name"], PATHINFO_EXTENSION);
					$this->dao->salva($conexao, "preinscricoes", $dadosIn);
				}

				$this->log->adicionar ("atualizou", "inscrição", $dadosIn["nome"], 
					"Usuário atualizou inscrição do curso " . $curso["nome"] . ".");

				$conexao->commit();
				setMensagem("info", "Pré-Inscrição atualizada");
				
				$redirecionar = montaRedirect($_SERVER["QUERY_STRING"]);
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
		catch (Exception $e) {
			setMensagem("error", $e->getMessage());
			if (!empty($redirecionar)) {
				Application::redirect($redirecionar);
				exit;
			}
		}

		$conexao->disconnect();
		$view = new View2('preinscricoes', "extendido", 'cadastrar.phtml');
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"cursos" => $cursos,
				"curso" => $curso,
				"usuarios" => $usuarios,
				"preinscricao" => $dados,
				"breadcrumbs" => $breadcrumbs
			)
		);
		$view->showContents();
    }

	private function _getHtmlEmail ($curso, $dados, $unidade = NULL) {
		try {
			if ($curso["tipo"] == Curso::CURSO_TIPO_APERFEICOAMENTO) {
				$htmlEmail = getEmail(array(
						"preinscricao" => $dados,
						"curso" => $curso
					)
				);
			}
			else {
				$htmlEmail = getEmail(array(
						"preinscricao" => $dados,
						"curso" => $curso,
						"unidade" => $unidade
					)
				);
			}
			return $htmlEmail;
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	private function _enviaComprovante ($conexao, $dados, $comprovante) {
		try {

			$tiposPermitidos = "application/msword|application/pdf|application/vnd.openxmlformats-officedocument.wordprocessingml.document|image/jpeg";

			// armazena o diretório de envio em uma variável
			$diretorio = DIR_UPLOADS . DS . "comprovantes";
			$diretorio .= DS . $dados["id"];

			// verifica se o tipo do arquivo enviado é um tipo permitido
			if (!verificaTipo ($comprovante, $tiposPermitidos))  {
				throw new Exception("Tipo de arquivo não permitido");
			}

			// cria o diretório
			if (!criaDiretorio ($diretorio)) {
				throw new Exception("Erro ao tentar fazer upload do comprovantes [iefap:1]");
			}

			// envia o currículo
			if (!salvaArquivo ($comprovante, $diretorio)) {
				throw new Exception("Erro ao tentar fazer upload do comprovantes [iefap:2]");
			}

		}
		catch (Exception $e) {
			throw $e;
		}
	}

	/*
	private function _getQuantidade($conexao, $params = array()) {
		try {

			// filtrar préinscrições que o próprio usuário realizou
			if ($_SESSION[PREFIX . "filtrar"] == 1) {
				$params["where"]["quem"] = (int) $_SESSION[PREFIX . "loginId"];
			}

			if (count($this->paramsSession["cursos"]) > 0) {
				$params["whereIn"] = array(
					"idCurso" => implode(",", $this->paramsSession["cursos"])
				);
			}

			// filtrar cidades que o usuário pode ver
			if (count($this->paramsSession["cidades"]) > 0) {
				$params["whereOr"] = array(
					"unidade IS NULL",
					"LOWER(unidade) in (" . implode(",", $this->paramsSession["cidades"]) . ")"
				);
			}

			return $this->dao->count($conexao, 'vw_preinscricoes_v3', $params);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	private function _getPreinscricoes($conexao, $params = array()) {
		try {

			// filtrar préinscrições que o próprio usuário realizou
			if ($_SESSION[PREFIX . "filtrar"] == 1) {
				$params["where"]["quem"] = (int) $_SESSION[PREFIX . "loginId"];
			}

			if (count($this->paramsSession["cursos"]) > 0) {
				$params["whereIn"] = array(
					"idCurso" => implode(",", $this->paramsSession["cursos"])
				);
			}

			if (count($this->paramsSession["cidades"]) > 0) {
				$params["whereOr"] = array(
					"unidade IS NULL",
					"LOWER(unidade) in (" . implode(",", $this->paramsSession["cidades"]) . ")"
				);
			}

			return $this->dao->findAll($conexao, 'vw_preinscricoes_v3', $params);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	private function _getPreinscricao($conexao, $id, $params = array()) {
		try {

			// filtrar préinscrições que o próprio usuário realizou
			if ($_SESSION[PREFIX . "filtrar"] == 1) {
				$params["where"]["quem"] = (int) $_SESSION[PREFIX . "loginId"];
			}

			if (count($this->paramsSession["cursos"]) > 0) {
				$params["whereIn"] = array(
					"idCurso" => implode(",", $this->paramsSession["cursos"])
				);
			}

			if (count($this->paramsSession["cidades"]) > 0) {
				$params["whereOr"] = array(
					"unidade IS NULL",
					"LOWER(unidade) in (" . implode(",", $this->paramsSession["cidades"]) . ")"
				);
			}

			// recupera a pré-inscrição
			return $this->dao->findByPk ($conexao, "vw_preinscricoes_v3", (int) $id, $params);

		}
		catch (Exception $e) {
			throw $e;
		}
	}
	*/

	private function _valida($dados) {
		try {

			$obrigatorios = array(
				"nome" => array(
					"tipo" => "input",
					"nome" => "Nome"
				)
			);

			$mensagens = array();
			$mensagem = validaPost($obrigatorios, $dados);

			if (!empty($mensagem)) {
				$mensagens[] = $mensagem;
			}

			if (count($mensagens) > 0) {
				throw new Exception (implode("<br />", $mensagens));
			}

			return $dados;
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	/**
	*	Retorna html com a view da listagem de inscrições. 
	*	As inscrições com status interessado terão uma view específica com sub-abas.
	*/
	public function viewAction() {
		$status = (int) $_POST['status'];
		$view1 = DIR_ROOT . '/administrar/views/preinscricoes/lists/';
		$view2 = DIR_ROOT . '/administrar/views/includes/listagem/tabs';
		$modulo ='preinscricoes';
		ob_start();
		if (existeDiretorio($view1 . 'status' . $status)) {
			require_once ($view1 . 'status' . $status . '/table-fields.phtml');
			require_once ($view1 . 'status' . $status . '/index.phtml');
		}
		else {
			require_once ($view1 . '/default/table-fields.phtml');
		}
		require_once ($view2 . '/listagem.phtml');
		$contents = ob_get_contents();
        ob_end_clean();
        echo $contents;
	}

	public function inscricoesAction() {
		try {
			header("Access-Control-Allow-Origin: *");
			$conexao = $this->conexao->getConexao();
			$modulo = 'preinscricoes';
			
			$dados = isset($_POST['dados']) ? $_POST['dados'] : array();
			$qs = isset($_POST['qs']) ? $_POST['qs'] : array();
			$order = isset($dados['order']) ? $dados['order'] : 'data';

			$status = $statusConstante = (int) $dados['status'];
			$curso = (int) $dados['curso'];

			$model = new Preinscricao($conexao);
			$query1 = $model->getQuery()->where('idCurso = ?', $curso);

			if (isset($dados['situacao'])) {
				$s = $dados['situacao'];
				if ($s == 0) {
					$query1->where('p.tipoSituacao IS NULL');
				}
				else {
					$query1->where('p.tipoSituacao = ?', (int) $dados['situacao']);
				}
			}

			foreach (Preinscricao::getTabClause($statusConstante) as $chave => $valor) {
                $query1->where($chave, eval($valor));
            }

			foreach ($qs as $key => $value) {
				if (!empty($value)) {
					if(preg_match('/[a-z\s-]/i', $value)) {
						$query1->where('p.' . $key . ' LIKE ?', '%' . $value . '%');
					}
					else if(preg_match('/\d{2}\/\d{2}\/\d{4}/', $value)) {
						$query1->where('p.' . $key . ' = ?', converteData($value));
					}
					else if(preg_match('/\d/', $value)) {
						$query1->where('p.' . $key . ' = ?', (int) $value);
					}
					else {
						$query1->where('p.' . $key . ' = ?', $value);
					}
				}
			}

			$query2 = clone $query1;

			if ($order == 'data') {
				$query2->order('data', 'desc');
			}
			else {
				$query2->order($order, 'asc');
			}
			
			//$quantidade = $query1->count();

			$params["objetos"] = Preinscricao::buildTable($conexao, $query2->all());
			$conexao->disconnect();

			$iniCount = 1;
			//$fim = $quantidade;

			ob_start();

			$viewsPreinscricoes = DIR_ROOT . '/administrar/views/preinscricoes/lists';
			$viewsDefault = DIR_ROOT . '/administrar/views/includes/listagem/tabs';

			if (is_dir($viewsPreinscricoes . '/status' . $status)) {
				require_once ($viewsPreinscricoes . '/status' . $status . '/table-fields.phtml');
			}
			else {
				require_once ($viewsPreinscricoes . '/default/table-fields.phtml');
			}

			require_once ($viewsDefault . '/rows.phtml');

			echo $rows;
			
			$contents = ob_get_contents();
	        ob_end_clean();
	        echo $contents;

		}
		catch (Exception $e) {

		}
	}

}

?>
