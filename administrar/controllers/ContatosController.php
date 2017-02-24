<?php

class ContatosController extends Controller {

	private $modulo = 'contatos';

	public function ContatosController() {
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
			$model = new Contato($conexao);
			$breadcrumbs = array();
			$breadcrumbs[] = array('Contatos' => '');
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
			echo $e->getMessage();
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
				'columns' => $model->getColumns(),
				'statuses' => Contato::getStatuses()
			)
		);
        $view->showContents();
	}

	public function responderAction() {
		try {
			$this->checaPermissao($this->modulo, 'responder');
			$conexao = $this->conexao->getConexao();
			$model = new Contato($conexao);
			
			$contato = $model->getObjetoOrFail(getVariavel('id'));

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Contatos" => "?modulo=contatos",
				$contato->nome => "",
				"Responder" => ""
			);

			if (count($_POST) > 0) {

				$dados = $_POST;
				$model->setDados($dados);

				$emailsValidos = array();
				$emailsInvalidos = array();
				if (!empty($contato->cc)) {
					// validando cc's
					foreach (explode(",", $contato->cc) as $cc) {
						$cc = trim($cc);
						$emailsValidos[] = $cc;
						if (!validaEmail($cc)) {
							$emailsInvalidos[] = $cc;
						}
					}
				}

				$obrigatorios = array(
					"resposta" => array(
						"tipo" => "editor1",
						"nome" => "Resposta"
					)
				);

				Funcoes::validaPost($obrigatorios, $dados);

				if (!empty($contato->cc) && count($emailsInvalidos) > 0) {
					$redirecionar = NULL;
					throw new Exception("O(s) seguinte(s) e-mail(s) é(são) inválido(s): " . implode(', ', $emailsInvalidos));
				}

				$contato->cc = implode(',', $emailsValidos);
				$contato->resposta = str_replace('"/images/userfiles', '"http://www.iefap.com.br/images/userfiles', $contato->resposta);
				$contato->salvar();
				$conexao->commit();
				$conexao->disconnect();
				Application::redirect('?modulo=contatos&acao=enviar&id=' . $contato->id);
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
			setMensagem("error", 'Contato não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			$conexao->disconnect();
			setMensagem("error", $e->getMessage());
		}

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", 'responder.phtml');
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"contato" => $contato
			)
		);
        $view->showContents();
	}

	public function enviarAction() {
		try {
			$this->checaPermissao($this->modulo, 'enviar');
			$conexao = $this->conexao->getConexao();
			$model = new Contato($conexao);

			$contato = $model->getObjetoOrFail(getVariavel('id'));

			// se não respondeu ainda
			if (empty($contato->resposta)) {
				$redirecionar = $_SERVER['HTTP_REFERER'];
				throw new Exception('Resposta não encontrada');
			}

			if ($contato->respondido == Contato::CONTATO_STATUS_RESPONDIDO) {
				$redirecionar = $_SERVER['HTTP_REFERER'];
				throw new Exception('Este contato já foi respondido');
			}

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Contatos" => "?modulo=contatos",
				$contato->nome => "",
				"Enviar" => ""
			);

			$htmlEmail = EmailPainel::contato($contato, false);

			if (count($_POST) > 0) {

				if ($_POST["botao"] == "Editar") {
					$redirecionar = '?modulo=contatos&acao=responder&id=' . $contato->id;
					Application::redirect($redirecionar);
					exit;
				}

				if (!empty($contato->cc)) {
					$ccs = array_map('trim', explode(",", $contato->cc));
					$contato->cc = implode(', ', $ccs);
				}

				EmailPainel::contato($contato, false);
				$contato->respondido = Contato::CONTATO_STATUS_RESPONDIDO;
				$contato->respondidoPor = $_SESSION[PREFIX . "loginNome"];
				$contato->timestampResposta = time();
				$contato->dataResposta = date('d/m/Y H:i:s', $contato->timestampResposta);
				$contato->salvar();
				setMensagem("info", "Contato de [" . $contato->nome . "] respondido");
				setMensagem("info", "Resposta enviada para [" . $contato->email . "]");
				$mensagemLog = "Usuário enviou resposta ao contato realizado através do site";
				if (!empty($contato->cc)) {
					setMensagem("info", "Enviada com cópia(s) para: " . implode(", ", $ccs));
					$mensagemLog .= " com cópia(s) para " . implode(", ", $ccs);
				}
				$mensagemLog .= ".";
				$this->log->adicionar ("enviou", "resposta", $contato->nome, $mensagemLog);
			$conexao->commit();
			$conexao->disconnect();
			Application::redirect('?modulo=contatos');
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
			setMensagem("error", 'Contato não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			$conexao->disconnect();
			setMensagem("error", $e->getMessage());
		}

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", "enviar.phtml");
		$view->setParams(array(
			"title" => getTitulo($breadcrumbs),
			"breadcrumbs" => $breadcrumbs,
			"email" => $htmlEmail,
			"cc" => $contato->cc
			)
		);
		$view->showContents();
	}

	public function observacoesAction() {
		try {
			$this->checaPermissao($this->modulo, 'observacoes');
			$conexao = $this->conexao->getConexao();

			$model = new Contato($conexao);
			$contato = $model->getObjetoOrFail(getVariavel('id'));

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Contatos" => "?modulo=contatos",
				$contato->nome => "",
				"Observações" => ""
			);

			if (count($_POST) > 0) {
				$contato->observacoes = $_POST['observacoes'];
				$contato->salvar();
				$this->log->adicionar ("atualizou", "observações", "contato",
					sprintf("Usuário atualizou observações do contato realizado por %s.", 
						$contato->nome));
				$conexao->commit();
				setMensagem("info", "Observações atualizadas");
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
			setMensagem("error", 'Contato não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", "observacoes.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"contato" => $contato
			)
		);
		$view->showContents();
	}

	public function excluirAction() {
		try {
			$this->checaPermissao($this->modulo, 'excluir');
			$conexao = $this->conexao->getConexao();
			$model = new Contato($conexao);
			$contato = $model->getObjetoOrFail(getVariavel('id'));
			$affectedRows = $contato->excluir();
			if ($affectedRows) {
				$this->log->adicionar ('excluiu', 'contato', $model->nome, 
					'Usuário excluiu contato.');
				$conexao->commit();					
				setMensagem('info', 'Contato excluído [' . $model->nome . ']');
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
			setMensagem("error", 'Contato não encontrado');
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

	public function planilhaAction() {
		try {
			$this->checaPermissao($this->modulo, 'planilha');
			$conexao = $this->conexao->getConexao();
			$model = new Contato($conexao);

			$colunas = isset($_POST["colunas"]) ? $_POST["colunas"] : array();

			if (count($colunas) == 0) {
				throw new Exception ('É necessário escolher pelo menos uma coluna.');
			}

			if (count($_POST) > 0) {

				if ($colunas[0] == -1) {
					array_shift($colunas);
				}

				$filtro = $colunas;
				$filtro[] = "data";
				$filtro[] = "respondido";
				$filtro[] = "respondidoPor";
				$filtro[] = "dataResposta";

				$order = isset($_GET['order']) ? array($_GET['order'] => 'asc') : array('data' => 'desc');

				$objPHPExcel = new PHPExcel();
				$objPHPExcel->getProperties()->setCreator("IEFAP");
				$objPHPExcel->getProperties()->setLastModifiedBy("IEFAP");
				$objPHPExcel->getProperties()->setTitle("Planilha de contatos");
				$objPHPExcel->getProperties()->setSubject("Planilha de contatos");
				$objPHPExcel->getProperties()->setDescription("IEFAP - Planilha de contatos");
				$objPHPExcel->setActiveSheetIndex(0);

				// se o usuário fizer uma busca ajax não será necessário fazer uma nova consulta
				// os dados chegarão aqui em formato json
				if (isset($_POST["qs"])) {
					foreach ($_POST["qs"] as $key => $value) {
						$where[$key] = $value;
					}
					$dados = $model->getObjetos(array(
							'filtro' => $filtro,
							'order' => $order,
							'where' => $where
						)
					);
				}
				else {
					$dados = $model->getObjetos(array(
							'filtro' => $filtro,
							'order' => $order
						)
					);
				}

				$numeroLinha = 1;
				$numeroColuna = 0;

				foreach ($filtro as $f) {
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, $numeroLinha, $f, false);
				}

				$numeroLinha = 2;
				$numeroColuna = 0;

				foreach ($dados as $key => $value) {
					foreach ($value as $campo => $valor) {
						if ($campo == "respondido") {
							$valor = ($valor == 1) ? "Sim" : "Não";
						}
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, $numeroLinha, $valor, false);
					}
					$numeroLinha++;
					$numeroColuna = 0;
				}

				$nomeArquivo = "Planilha-Contatos.xlsx";
				$path = DIR_ROOT . DS . "temp";

				if (is_writable($path)) {
					$path .=  DS . $nomeArquivo;
					$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
					$objWriter->save($path);
					$this->log->adicionar ("gerou", "planilha", "EXCEL", 
						"Usuário gerou planilha de contatos em EXCEL.");
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
			Application::redirect("?modulo=contatos");
			exit;
		}
	}

	public function statusAction() {
		try {
			$this->checaPermissao($this->modulo, 'status');
			$conexao = $this->conexao->getConexao();

			$model = new Contato($conexao);
			$contato = $model->getObjetoOrFail(getVariavel('id'));

			$nome = $contato->nome;
			$statusAntigo = $contato->status;

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Contatos" => "?modulo=contatos",
				$contato->nome => "",
				"Status" => ""
			);

			if (count($_POST) > 0) {
				$dados = $_POST;
				if ($statusAntigo != $_POST["status"]) {
					$contato->status = $_POST['status'];
					$contato->salvar();
					$this->log->adicionar ("atualizou", "status", "contato",
						sprintf("Usuário atualizou status do contato realizado por %s de %s para %s.", 
							$nome, Contato::getStatus($statusAntigo), 
							Contato::getStatus($dados["status"])));
					$conexao->commit();
					setMensagem("info", "Status atualizado");
				}
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
			setMensagem("error", 'Contato não encontrado');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", "status.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"contato" => $contato
			)
		);
		$view->showContents();
	}

	public function acoesAction() {

		try {

			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("acao"));

			$conexao = $this->conexao->getConexao();

			if (count($_POST)) {

				if (empty($_POST["objetos"])) {
					throw new Exception("É necessário selecionar pelo menos um " . Funcoes::lowerCase($this->info["labelSing"]));
				}

				$ids = isset($_POST["objetos"]) ? $_POST["objetos"] : array();
				$processados = 0;

				// retira o elemento -1, caso exista
				if (count($ids) > 0 && $ids[0] == -1){
					array_shift($ids);
				}

				foreach ($ids as $id) {

					$dados = $this->dao->findByPk ($conexao, "contatos", $id);

					if (isset($_POST["acoes"])) {

						$statusAntigo = $dados["status"];

						switch ($_POST["acoes"]) {

							case "excluir" :
								//$this->checaPermissao($this->info["modulo"], 'excluir');
								$opcao = "excluído(s)";
								$affectedRows = $this->dao->excluiByPk($conexao, "contatos", $id);
								if ($affectedRows > 0) {
									$processados += 1;
									$this->logDAO->adicionar ($conexao, "excluiu", "contato", $_SESSION[PREFIX . "loginNome"],
										$dados["nome"], "Usuário excluiu contato realizado por " . $dados["nome"] . ".");
									$conexao->commit();
								}
							break;

							default :
								//$this->checaPermissao($this->info["modulo"], 'cadastrar');
								$opcao = "atualizado(s)";
								$opcaoItem = explode(",", $_POST["acoes"]);
								$atualizar = $opcaoItem[0];
								$valor = $opcaoItem[1];

								$affectedRows = $this->dao->salva($conexao, "contatos", array(
										"id" => $dados["id"],
										$atualizar => (int) $valor
									)
								);

								if ($affectedRows > 0) {
									$processados += 1;
									$this->logDAO->adicionar ($conexao, "atualizou", "status", $_SESSION[PREFIX . "loginNome"],
										"contato", "Usuário atualizou status do contato realizado por " . $dados["nome"] .
										" de " . Contato::getStatus($statusAntigo) . " para " . Contato::getStatus($valor) .
										".");
								}
							break;
						}
					}
				}

				$conexao->commit();
				$conexao->disconnect();

				if ($processados > 0) {
					setMensagem("info", $processados . " " . Funcoes::lowerCase($this->info["labelSing"]) . "(s) " . $opcao);
				}

			}

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
			setMensagem("error", $e->getMessage());
			Application::redirect($redirecionar);
			exit;
		}

	}

	public function novaAction() {
		try {
			$conexao = $this->conexao->getConexao();
			
			$cursoModel = new Curso($conexao);
			$historicoModel = new HistoricoPreinscricao($conexao);
			$contatoModel = new Contato($conexao);

			$contato = $contatoModel->getObjetoOrFail(getVariavel('id'));

            $cursos = $cursoModel->getObjetos(array(
					'order' => array('nome' => 'asc')
				)
			);

            $breadcrumbs[] = array(
				'Contatos' => '?modulo=' . $this->modulo,
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
							'nome' => $contato->nome,
							'operadoraCelular' => $contato->operadora,
							'telefone' => $contato->telefone,
							'email' => $contato->email,
							'cidade' => $contato->cidade,
							'status' => (int) $_POST['status'],
							'data' => date('Y-m-d H:i:s', $time),
							'timestamp' => $time
						)
					);

				$contato->convertido = 1;
				$contato->curso = $curso->nome;
				$contato->salvar();

				// adiciona no histórico
				$historicoModel->adicionar($id, "Inscrição criada a partir de um registro do módulo contatos");
				// adciciona nos logs
				$this->log->adicionar ("converteu", "contato", $contato->nome, 
					sprintf("Usuário converteu contato em inscrição no curso %s.", 
						$curso->nome));
				$conexao->commit();
				$conexao->disconnect();
				setMensagem('info', 'Contato convertido para inscrição com sucesso');
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
		$view = new View2($_GET["modulo"], "extendido", 'nova.phtml');
		$view->setParams(array(
				'title' => getTitulo($breadcrumbs), 
				'cursos' => $cursos,
				'contato' => $contatoModel,
				'statuses' => Preinscricao::getStatuses(),
				'breadcrumbs' => $breadcrumbs
			)
		);
		$view->showContents();
    }

    public function ajaxAction() {
		try {
			$conexao = $this->conexao->getConexao();
			$model = new Contato($conexao);

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
