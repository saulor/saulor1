<?php

class DocentesController extends Controller {
	
	private $modulo = 'docentes';

	public function DocentesController() {
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
			$model = new Docente($conexao);
			$breadcrumbs = array();
			$breadcrumbs[] = array('Docentes' => '');
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
			
			$docenteModel = new Docente ($conexao);
			$cursoModel = new Curso ($conexao);

			$cursos = $cursoModel->getObjetos(array(
					'order' => array('nome' => 'asc')
				)
			);
			$cursosDisponiveis = array();
			foreach ($cursos as $curso) {
				$cursosDisponiveis[] = '\'' . $curso->nome . '\'';
			}

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Docentes' => '?modulo=' . $this->modulo
			);
			
			if (isset($_GET["id"])) {
				$model = $docenteModel->getObjetoOrFail(getVariavel('id'));
				$acao = "editar";
				$breadcrumbs[] = array(
					"Editar" => "" 
				);
			}
			else {
				$model = $docenteModel;
				$acao = "cadastrar";
				$breadcrumbs[] = array(
					"Novo" => "" 
				);
			}
			
			if (count($_POST) > 0) {
				
				$redirecionar = NULL;
				$dados = $_POST['Docente'];
				$model->setDados($dados);
				
				$obrigatorios = array(
					"nome" => array(
						"nome" => "Nome"
					)
				);
				
				Funcoes::validaPost($obrigatorios, $dados);
				$model->salvar();

				foreach ($_POST['Cursos'] as $key => $curso) {
					$dcd = new DocenteCursoDisciplina ($conexao);
					$dcd->id = $curso['id'];
					$dcd->docente = $model->id;
					$dcd->curso = $curso['curso'];
					$dcd->disciplinas = $curso['disciplinas'];
					$dcd->salvar();
				}

				if ($acao == 'cadastrar') {	
					$acao = 'cadastrado';
					$this->log->adicionar ('cadastrou', 'docente', $model->nome, 
						'Usuário cadastrou docente.');					
					$redirecionar = sprintf('?modulo=%s&acao=%s&id=%d', $this->modulo, 
						'cadastrar', $model->id);
				}
				else {
					$acao = 'atualizado';
					$this->log->adicionar ('atualizou', 'docente', $model->nome, 
						'Usuário atualizou docente.');
					$redirecionar = sprintf('?modulo=%s', $this->modulo);
				}

				setMensagem('info', sprintf('Docente %s [%s]', $acao, $model->nome));
				
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
			setMensagem("error", 'Docente não encontrado');
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
				"cursosDisponiveis" => implode(",", $cursosDisponiveis)
			)
		);
        $view->showContents();
	}
	
	public function excluirAction() {
		try {
			$this->checaPermissao('profissionais', 'excluir');
			$conexao = $this->conexao->getConexao();
			$model = new Docente($conexao);
			$objeto = $model->getObjetoOrFail(getVariavel('id'));
			$affectedRows = $objeto->excluir();
			if ($affectedRows) {
				$diretorio = DIR_UPLOADS . DS . 'trabalhe' . DS . 'docentes';
				$diretorio .= DS . $objeto->id;
				Funcoes::excluiDiretorio($diretorio);
				$this->log->adicionar ('excluiu', 'docente', $objeto->nome, 
					'Usuário excluiu docente.');
				$conexao->commit();					
				setMensagem('info', 'Docente excluído [' . $objeto->nome . ']');
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
			setMensagem("error", 'Docente não encontrado');
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
			$model = new Docente($conexao);
			$docente = $model->getObjetoOrFail(getVariavel('id'));
			
			$diretorio = DIR_UPLOADS . DS . "trabalhe";
			$diretorio .= DS . "docentes" . DS;
			$diretorio .= $docente->id . DS;
			$diretorio .= base64_decode($docente->curriculoComercial);
			
			if (!existeArquivo($diretorio)) {
				throw new Exception("Currículo não encontrado");
			}

			if (excluiArquivo($diretorio)) {
				$this->log->adicionar ("excluiu", "currículo", $docente->nome, 
					"Usuário excluiu currículo do Profissional Docente.");	
				$docente->curriculoComercial = NULL;
				$docente->mime = NULL;
				$docente->extensao = NULL;
				$docente->salvar();	
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
			$model = new Docente($conexao);
			$docente = $model->getObjetoOrFail(getVariavel('id'));
		
			$diretorio = DIR_UPLOADS . DS . "trabalhe" . DS;
			$diretorio .= "docentes" . DS . $docente->id;
			$diretorio .= DS . base64_decode($docente->curriculoComercial);
			
			if (!existeArquivo($diretorio)) {
				throw new Exception("Currículo não encontrado");
			}
			
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . base64_decode($docente->curriculoComercial) . '"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($diretorio));
			//ob_clean();
			flush();
			readfile($diretorio);
			
			// adiciona nos logs 
			$this->log->adicionar ("download", "currículo", "Profissional Docente", 
				"Usuário fez download de currículo do Profissional Docente " . $docente->nome . ".");
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
			setMensagem("error", 'Docente não encontrado');
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
			
			$docenteModel = new Docente($conexao);
			$objetos = isset($_POST["objetos"]) ? $_POST["objetos"] : array();
			
			// retira o elemento -1, caso exista
			if (count($objetos) > 0 && $objetos[0] == -1) {
				array_shift($objetos);
			}
			
			foreach ($objetos as $id) {

				$model = $docenteModel->getObjeto($id);

				switch ($_POST['acoes']) {
				
					case 'excluir' :
						$opcao = "excluído(s)";	
						if ($model->excluir()) {
							$processados += 1;
							$this->log->adicionar ('excluiu', 'docente', $model->nome, 
								"Usuário excluiu docente através do recurso de aplicar ações em massa.");
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
			$model = new Docente($conexao);
			$docente = $model->getObjetoOrFail(getVariavel('id'));
			
			$breadcrumbs[] = array(
				"Docentes" => "?modulo=" . $this->modulo,
				$docente->nome => "?modulo=" . $this->modulo . "&acao=cadastrar&id=" . $docente->id,
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
				// 		"cargo" => "Docente",
				// 		"nome" => $docente->nome,
				// 		"email" => $docente->email,
				// 		"texto" => $dados["resposta"],
				// 		"timestamp" => $docente->timestamp
				// 	), true
				// )) {
				// 	$this->log->adicionar ("enviou", "e-mail", $docente->email, 
				// 		"Usuário enviou e-mail para o Profissional Docente " . $docente->nome . ".");
				// 	setMensagem("info", "E-mail enviado com sucesso para " . $docente->email . "<br />Uma cópia foi enviada para contato@iefap.com.br");
					
				// }
				// else {
				// 	$this->log->adicionar ("não enviou", "e-mail", $docente->email, 
				// 		"Sistema não foi capaz de enviar e-mail para o Profissional Docente " . $docente->nome . ".");
				// 	setMensagem("error", "Erro ao tentar enviar e-mail para " . $docente->email . ". Por favor, tente novamente!");
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
			setMensagem("error", 'Docente não encontrado');
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
				"objeto" => $docente,
				"breadcrumbs" => $breadcrumbs
				
			)
		);
        $view->showContents();
	}

	public function ajaxAction() {
		try {

			if (count($_POST) == 0) {
				return;
			}

			$conexao = $this->conexao->getConexao();
			$model = new Docente($conexao);
			$model->setTable('vw_docentes');

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
	
}

?>