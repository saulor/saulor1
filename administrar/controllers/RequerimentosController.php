<?php

class RequerimentosController extends Controller {

	protected $info = array(
		"tabela" => "requerimentos",
		"modulo" => "requerimentos",
		"labelSing" => "Requerimento",
		"labelPlur" => "Requerimentos"
	);

	public function RequerimentosController() {
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
			$this->checaPermissao('preinscricoes', 'visualizar');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("id", "acao", "curso"));
			$breadcrumbs = array();
			$breadcrumbs[] = array("Requerimentos" => "");
			$tabs = Requerimento::getTabs($conexao);
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
		$view = new View2($_GET["modulo"], "extendido", "index.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"tabs" => $tabs
			)
		);
        $view->showContents();

	}

	public function visualizarAction() {

		try {
			$this->checaPermissao('requerimentos', 'visualizar');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("id", "acao"));
			$dados = $this->dao->findByPk($conexao, "requerimentos", getVariavel("id"));
			$arquivo = $dados["arquivo"];
			$situacao = $dados["situacao"];
			$timestamp = $dados["timestamp"];

			$cursos = $this->dao->findAll($conexao, "cursos", array(
					"order" => array(
						"nome" => "asc"
					)
				)
			);

			$usuarios = $this->dao->findAll($conexao, "usuarios", array(
					"order" => array(
						"nome" => "asc"
					)
				)
			);

			$cursosDisponiveis = array();
			foreach ($cursos as $curso) {
				$cursosDisponiveis[] = '\'' . $curso["nome"] . '\'';
			}

			$tiposIn = array();
			$tipos = Requerimento::getTipos();

			foreach ($tipos as $key => $value) {
				$taxa = $this->dao->find($conexao, "requerimentos_taxas", array(
						"where" => array(
							"tipo" => $key
						)
					)
				);
				if ($taxa) {
					$tiposIn[$key] = array(
						"descricao" => $value,
						"taxa" => $taxa["taxa"]
					);
				}
				else {
					$tiposIn[$key] = array(
						"descricao" => $value
					);
				}
			}

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Requerimentos" => "?modulo=requerimentos&tab=" . $_GET['tab'],
				$dados["nome"] => ""
			);

			if (count($_POST) > 0) {

				$this->checaPermissao('requerimentos', 'cadastrar');

				$obrigatorios = array(
					"nome" => array(
						"tipo" => "input",
						"nome" => "Nome"
					),
					"email" => array(
						"tipo" => "input",
						"nome" => "E-mail"
					),
					"curso" => array(
						"tipo" => "input",
						"nome" => "Curso"
					),
					"cidade1" => array(
						"tipo" => "input",
						"nome" => "Cidade"
					),
					"tipo" => array(
						"tipo" => "radio",
						"nome" => "Tipo de Requerimento"
					)
				);

				$dadosIn = $_POST;
				$redirecionar = NULL;

				// verifica se a solicitação já foi finalizada e se estão tentando reabrí-la
				if ($situacao == Requerimento::REQUERIMENTO_SITUACAO_FINALIZADO &&
					$dadosIn["situacao"] != $situacao) {
					throw new Exception("Não é permitido mudar a situação deste requerimento porque ele
						já foi finalizado!");
				}

				// data de finalização do requerimento
				// depois que é registrada pela primeira vez não deve mudar
				if (isset($dadosIn["dataFinalizacao"])) {
					$dadosIn["dataFinalizacaoTimestamp"] = dataToTimestamp($dadosIn["dataFinalizacao"]);
				}
				else if ($dadosIn["situacao"] == Requerimento::REQUERIMENTO_SITUACAO_FINALIZADO) {
					$dadosIn["dataFinalizacao"] = date("d/m/Y");
					$dadosIn["dataFinalizacaoTimestamp"] = dataToTimestamp($dadosIn["dataFinalizacao"]);
					$dadosIn["finalizadoPor"] = $_SESSION[PREFIX . "loginNome"];
				}

				$dadosIn["timestamp"] = $timestamp;
				$dadosIn["dataVencimentoTimestamp"] = dataToTimestamp($dadosIn["dataVencimento"]);
				$dadosIn["dataPagamentoTimestamp"] = dataToTimestamp($dadosIn["dataPagamento"]);
				$dadosIn["pago"] = isset($dadosIn["pago"]) ? $dadosIn["pago"] : -1;
				$dadosIn["isento"] = isset($dadosIn["isento"]) ? $dadosIn["isento"] : -1;
				$dadosIn["pendencias"] = isset($dadosIn["pendencias"]) ? implode(",", $dadosIn["pendencias"]) : NULL;
				$dadosIn["tipo"] = isset($dadosIn["tipo"]) ? $dadosIn["tipo"] : 0;

				if (Requerimento::temEspecificacao($dadosIn["tipo"]) && empty($dadosIn["especificacoes"])) {
					setMensagem("error", "Você escolheu uma opção de Requerimento que exige uma especificação 
						detalhada.");
					$obrigatorios["especificacoes"] = array(
						"tipo" => "textarea",
						"nome" => "Especificações"
					);
				}

				$mensagem = validaPost($obrigatorios, $dadosIn);
				if (!empty($mensagem)) {
					throw new Exception($mensagem);
				}

				if (empty($dadosIn["telefoneResidencial"]) && empty($dadosIn["telefoneCelular"])) {
					throw new Exception ("É necessário informar pelo menos um telefone para contato.");
				}

				// salva
				$dadosIn = $this->dao->salva($conexao, "requerimentos", $dadosIn);

				// se mudou a situação do requerimento
				// envia um e-mail informando ao aluno
				if ($situacao != $dadosIn["situacao"]) {
					EmailPainel::requerimento($dadosIn, $dados['email']);
				}

				if (isset($_FILES["arquivo"]) && enviouArquivo($_FILES["arquivo"])) {

					$tiposPermitidos = "application/pdf";

					// armazena o diretório de envio em uma variável
					$diretorio = DIR_UPLOADS . DS . "requerimentos";
					$diretorio .= DS . $dadosIn["id"];

					// verifica se o tipo do arquivo enviado é um tipo permitido
					if (!verificaTipo ($_FILES["arquivo"], $tiposPermitidos)) {
						throw new Exception("Tipo de arquivo não permitido");
					}

					// exclui o curriculo antigo
					if (!empty($arquivo) && 
						existeArquivo($diretorio . DS . base64_decode($arquivo))) {
						excluiArquivo($diretorio . DS . base64_decode($arquivo));
					}

					// cria o diretório
					if (!criaDiretorio ($diretorio)) {
						throw new Exception("Erro ao tentar criar diretório para arquivo");
					}

					// envia o currículo
					if (!salvaArquivo ($_FILES["arquivo"], $diretorio)) {
						throw new Exception("Erro ao tentar enviar arquivo");
					}

					$dadosIn["arquivo"] = base64_encode($_FILES["arquivo"]["name"]);
					$dadosIn["mime"] = $_FILES["arquivo"]["type"];
					$dadosIn["extensao"] = pathinfo($_FILES["arquivo"]["name"], PATHINFO_EXTENSION);
					// salva
					$this->dao->salva($conexao, "requerimentos", $dadosIn);
				}

				// fazer envio do comprovante
				$enviouComprovante = $enviouAnexo = false;
				if (isset($_FILES["comprovante"]) && enviouArquivo($_FILES["comprovante"])) {
					$enviouComprovante = true;
					$this->_enviaComprovante($conexao, $dadosIn, $_FILES["comprovante"]);
					$dadosIn["enviouComprovante"] = 1;
					$dadosIn["comprovante"] = base64_encode($_FILES["comprovante"]["name"]);
					$dadosIn["mimeComprovante"] = $_FILES["comprovante"]["type"];
					$dadosIn["extensaoComprovante"] = pathinfo($_FILES["comprovante"]["name"], PATHINFO_EXTENSION);
					$this->dao->salva($conexao, "requerimentos", $dadosIn);
				}

				if (isset($_FILES["anexo"]) && enviouArquivo($_FILES["anexo"])) {
					$enviouAnexo = true;
					$diretorio = DIR_UPLOADS . DS . "requerimentos" . DS . 
						"comprovantes" . DS . $dadosIn["id"];
					$this->_enviaComprovante($conexao, $dadosIn, $_FILES["anexo"], $diretorio);
					$dadosIn["enviouAnexo"] = 1;
					$dadosIn["anexo"] = base64_encode($_FILES["anexo"]["name"]);
					$dadosIn["mimeAnexo"] = $_FILES["anexo"]["type"];
					$dadosIn["extensaoAnexo"] = pathinfo($_FILES["anexo"]["name"], PATHINFO_EXTENSION);
					$this->dao->salva($conexao, "requerimentos", $dadosIn);
				}

				$this->log->adicionar ("atualizou", "requerimento", $dadosIn["nome"], 
					"Usuário atualizou o requerimento efetuado no site por " . $dadosIn["nome"] . ".");
				$conexao->commit();
				setMensagem("info", 'Requerimento atualizado');
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
			$conexao->rollback();
			//$redirecionar = NULL;
			if (isset($timestamp)) {
				$dados["timestamp"] = $timestamp;
			}
			setMensagem("error", $e->getMessage());
			if ($redirecionar != NULL) {
				Application::redirect($redirecionar);
				exit;
			}
		}

		$conexao->disconnect();
		$view = new View2($_GET["modulo"], "extendido", 'visualizar.phtml');
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"requerimento" => $dados,
				"breadcrumbs" => $breadcrumbs,
				"usuarios" => $usuarios,
				"cursosDisponiveis" => implode(",", $cursosDisponiveis),
				"tipos" => $tiposIn
			)
		);
		$view->showContents();
	}

	public function taxasAction() {

		try {
			$this->checaPermissao('requerimentos', 'cadastrar');
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("id", "acao"));
			$breadcrumbs = array();
			$breadcrumbs[] = array(
				"Requerimentos" => "?modulo=requerimentos",
				"Gerenciar taxas" => ""
			);

			$taxas = array();
			$tipos = Requerimento::getTipos();
			array_pop($tipos);

			foreach ($tipos as $tipo => $descricao) {
				$taxas[$tipo] = array(
					'id' => 0,
					'taxa' => 0
				);
			}

			$taxasAux = $this->dao->findAll($conexao, "requerimentos_taxas");

			if (count($taxasAux) > 0) {
				foreach ($taxasAux as $taxaAux) {
					$taxas[$taxaAux["tipo"]] = array(
						'id' => $taxaAux["id"],
						'taxa' => $taxaAux["taxa"]
					);
				}
			}

			if (count($_POST) > 0) {

				$redirecionar = NULL;
				$validos = $invalidos = array();

				foreach ($_POST['taxas'] as $tipo => $taxa) {
					$taxaIn = array(
						'id' => $taxa["id"],
						'tipo' => $tipo,
						'taxa' => $taxa["taxa"]
					);
					$this->dao->salva($conexao, "requerimentos_taxas", $taxaIn);
				}

				$conexao->commit();
				$conexao->disconnect();
				setMensagem("info", "Taxas atualizada(s)");
				Application::redirect("?modulo=requerimentos&acao=taxas");
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
		$view = new View2($_GET["modulo"], "extendido", 'taxas.phtml');
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"taxas" => $taxas,
				"tipos" => $tipos,
				"breadcrumbs" => $breadcrumbs,
			)
		);
		$view->showContents();
	}

	public function removerAction() {

		try {
			$conexao = $this->conexao->getConexao();
			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("acao"), array("acao" => "visualizar"));
			$requerimento = $this->dao->findByPk($conexao, "requerimentos", getVariavel("id"));
			$diretorio = DIR_UPLOADS . DS . "requerimentos" . DS;
			$diretorio .= $requerimento["id"] . DS . base64_decode($requerimento["arquivo"]);
			if (excluiArquivo($diretorio)) {
				$conexao->query()
					->from("requerimentos")
					->where("id = ?", (int) $requerimento["id"])
					->save(array(
							"arquivo" => NULL,
							"mime" => NULL,
							"extensao" => NULL
						)
					);
				$this->log->adicionar ("excluiu", "anexo do requerimento", $requerimento["nome"], 
					"Usuário excluiu o anexo do requerimento efetuado no site por " . $requerimento["nome"] . " [" . Requerimento::getTipo($requerimento["tipo"]) . "].");
				$conexao->commit();
				setMensagem("info", "Arquivo excluído [" . base64_decode($requerimento["arquivo"]) . "]");
			}
			else {
				setMensagem("error", "Arquivo não excluído [" . base64_decode($requerimento["arquivo"]) . "]");
			}
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}

		$conexao->disconnect();
		Application::redirect($redirecionar);
		exit;

	}

	public function excluirAction() {

		try {
			$this->checaPermissao('requerimentos', 'excluir');

			$conexao = $this->conexao->getConexao();

			$dados = $this->dao->findByPk($conexao, "requerimentos", getVariavel("id"));

			$affectedRows = $this->dao->exclui($conexao, "requerimentos", array(
					"where" => array(
						"id" => (int) $dados["id"]
					)
				)
			);

			if ($affectedRows) {
				$diretorio = DIR_UPLOADS . DS . "requerimentos" . DS . $dados["id"];
				excluiDiretorio($diretorio);
				$this->log->adicionar ("excluiu", "requerimento", $dados["nome"], 
					"Usuário excluiu solicitação de requerimento efetuado através do site por " . $dados["nome"] . " [" . Requerimento::getTipo($dados["tipo"]) . "].");
				$conexao->commit();
				setMensagem("info", "Requerimento excluído [" . $dados["nome"] . "]");
			}
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}

		$conexao->disconnect();
		Application::redirect($_SERVER['HTTP_REFERER']);
		exit;

	}

	// exclui o comprovante
	public function excluircAction() {

		try {
			$this->checaPermissao('preinscricoes', 'cadastrar');
			$conexao = $this->conexao->getConexao();

			$dados = $this->dao->findByPk ($conexao, "requerimentos", getVariavel("id"));

			$diretorio = DIR_UPLOADS . DS . "requerimentos";
			$diretorio .= DS . $dados["id"] . DS . base64_decode($dados["comprovante"]);

			if (excluiArquivo($diretorio)) {
				$this->log->adicionar ("excluiu", "comprovante", $dados["nome"], 
					"Usuário excluiu comprovante de pagamento da taxa correspondente ao requerimento solicitado através do site.");
				$dadosIn["id"] = $dados["id"];
				$dadosIn["enviouComprovante"] = 0;
				$dadosIn["comprovante"] = NULL;
				$dadosIn["mimeComprovante"] = NULL;
				$dadosIn["extensaoComprovante"] = NULL;
				$this->dao->salva($conexao, "requerimentos", $dadosIn);
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

		Application::redirect($_SERVER['HTTP_REFERER']);
		exit;

	}

	// exclui o anexo
	public function excluiraAction() {

		try {
			$this->checaPermissao('preinscricoes', 'cadastrar');
			$conexao = $this->conexao->getConexao();

			$dados = $this->dao->findByPk ($conexao, "requerimentos", getVariavel("id"));

			$diretorio = DIR_UPLOADS . DS . "requerimentos";
			$diretorio .= DS . $dados["id"] . DS . base64_decode($dados["anexo"]);

			if (excluiArquivo($diretorio)) {
				$this->log->adicionar ("excluiu", "anexo", $dados["nome"], 
					"Usuário excluiu anexo correspondente ao requerimento solicitado através do site.");
				$dadosIn["id"] = $dados["id"];
				$dadosIn["enviouAnexo"] = 0;
				$dadosIn["anexo"] = NULL;
				$dadosIn["mimeAnexo"] = NULL;
				$dadosIn["extensaoAnexo"] = NULL;
				$this->dao->salva($conexao, "requerimentos", $dadosIn);
				$conexao->commit();
				setMensagem("info", "Anexo excluído");
			}
			else {
				setMensagem("error", "Erro ao tentar excluir anexo");
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

		Application::redirect($_SERVER['HTTP_REFERER']);
		exit;

	}

	public function downloadAction () {

		try {

			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("acao"), array("acao" => "visualizar"));
			$conexao = $this->conexao->getConexao();

			$dados = $this->dao->findByPk($conexao, "requerimentos", getVariavel("id"));

			$diretorio = DIR_UPLOADS . DS . "requerimentos";
			$diretorio .= DS . $dados["id"] . DS;
			$diretorio .= base64_decode($dados[$_GET['q']]);

			if (!existeArquivo($diretorio)) {
				throw new Exception('Arquivo não encontrado');
			}

			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . base64_decode($dados[$_GET['q']]) . '"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($diretorio));
			//ob_clean();
			flush();
			readfile($diretorio);

			// adiciona nos logs
			$this->log->adicionar ("realizou", "download", $_GET['q'], "Usuário fez download do " . $_GET['q'] . " de pagamento da taxa referente ao requerimento " . Requerimento::getTipo($dados["tipo"]) . " solicitado pelo aluno(a) " . $dados["nome"] . ".");

			// comita transação
			$conexao->commit();
			Application::redirect($redirecionar);
			exit;
		}
		catch (Exception $e) {
			setMensagem("error", $e->getMessage());
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
	}

	public function formularioAction () {

		try {
			//$this->checaPermissao('preinscricoes', 'formulario');
			$conexao = $this->conexao->getConexao();

			$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("acao", "id"));

			$dados = $this->dao->findByPk($conexao, "requerimentos", getVariavel("id"));

			$formulario = new FormularioRequerimento ("P", "mm", "A4");
			$formulario->setTitle(utf8_decode("Formulário de Pré-inscrição"));
			$formulario->AddPage();

			$formulario->setTextColor(0,0,0);
			$formulario->setY(38);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',11);
			$formulario->Cell(0,0,utf8_decode('FORMULÁRIO DE REQUERIMENTO'));

			$formulario->setY($formulario->getY() + 10);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',9);

			$formulario->Cell(0,0, utf8_decode("REQUERIMENTO"));
			$formulario->SetFont('Helvetica','',9);
			$formulario->setY($formulario->getY() + 8);
			$formulario->setX(31);
			$formulario->Cell(100,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY() - 2);
			$formulario->setX(30);
			$formulario->Cell(0,0, utf8_decode(Requerimento::getTipo($dados["tipo"])));

			$formulario->setY($formulario->getY() + 8);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',9);
			$formulario->Cell(0,0, utf8_decode("CURSO"));
			$formulario->SetFont('Helvetica','',9);
			$formulario->setY($formulario->getY() + 7);
			$formulario->setX(31);
			$formulario->Cell(100,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY() - 2);
			$formulario->setX(30);
			$formulario->Cell(0,0, utf8_decode($dados["curso"]));

			$especificacoes = empty($dados["especificacoes"]) ? 'Nada consta' : $dados["especificacoes"];

			$formulario->setY($formulario->getY() + 8);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',9);
			$formulario->Cell(0,0, utf8_decode("ESPECIFICAÇÕES"));
			$formulario->SetFont('Helvetica','',9);
			$formulario->setY($formulario->getY() + 4);
			$formulario->setX(30);
			$formulario->MultiCell(0,5, utf8_decode($especificacoes));

			$formulario->setY($formulario->getY() + 8);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',9);
			$formulario->Cell(0,0,utf8_decode('IDENTIFICAÇÃO DO ALUNO'));

			$formulario->setY($formulario->getY() + 6);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('NOME'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(31);
			$formulario->Cell(100,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(30);
			$formulario->Cell(0,0,utf8_decode($dados["nome"]));


			$formulario->setY($formulario->getY() - 4);
			$formulario->setX(133);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('E-MAIL'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY() + 6);
			$formulario->setX(133);
			$formulario->Cell(60,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY() - 2);
			$formulario->setX(133);
			$formulario->Cell(0,0,utf8_decode($dados["email"]));

			$formulario->setY($formulario->getY() + 6);
			$formulario->setX(30);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('CIDADE'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY() + 6);
			$formulario->setX(30);
			$formulario->Cell(100,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY() - 2);
			$formulario->setX(30);
			$formulario->Cell(0,0,utf8_decode($dados["cidade1"]));

			$celular = '';
			if (!empty($dados["operadoraCelular"])) {
				$celular .= '(' . $dados["operadoraCelular"] . ') ';
			}

			$celular .= $dados["telefoneCelular"];

			$formulario->setY($formulario->getY() - 4);
			$formulario->setX(133);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('TEL. CELULAR'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(133);
			$formulario->Cell(29,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(133);
			$formulario->Cell(0,0,utf8_decode($celular));

			$formulario->setY($formulario->getY()-4);
			$formulario->setX(164);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('TEL. RESIDENCIAL'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(164);
			$formulario->Cell(29,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(164);
			$formulario->Cell(0,0,utf8_decode($dados["telefoneResidencial"]));

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
			$formulario->Cell(0,0,utf8_decode($dados["endereco"] . " "  . $dados["numero"] . " " . $dados["complemento"]));

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
			$formulario->Cell(0,0,utf8_decode($dados["bairro"]));

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
			$formulario->Cell(0,0,utf8_decode($dados["cidade2"]));

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
			$formulario->Cell(0,0,utf8_decode($dados["uf"]));

			$formulario->setY($formulario->getY()-4);
			$formulario->setX(36);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('CEP'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(37);
			$formulario->Cell(19,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(36);
			$formulario->Cell(0,0,utf8_decode($dados["cep"]));

			$formulario->setY($formulario->getY()-4);
			$formulario->setX(57);
			$formulario->SetFont('Helvetica','B',8);
			$formulario->Cell(0,0,utf8_decode('DATA'));
			$formulario->SetFont('Helvetica','',8);
			$formulario->setY($formulario->getY()+6);
			$formulario->setX(58);
			$formulario->Cell(28,0.1,'',0,0,0,true,'');
			$formulario->setY($formulario->getY()-2);
			$formulario->setX(57);
			$formulario->Cell(0,0,utf8_decode($dados["data"]));

			$nomeArquivo = "Formulario-Requerimento-" . implode("-", explode(" ",trim($dados["nome"]))) . ".pdf";
			$path = DIR_ROOT . DS . "temp";

			if (is_writable($path)) {
				$path .=  DS . $nomeArquivo;
				//$formulario->Output();exit;
				$formulario->Output($path, "F");
				$this->log->adicionar ("gerou", "formulário", "requerimento", 
					"Usuário gerou formulário de requerimento do aluno " . $dados["nome"] . ".");
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
		Application::redirect($redirecionar);
		exit;

	}

	private function _enviaComprovante ($conexao, $dados, $comprovante) {
		try {

			$tiposPermitidos = "application/msword|application/pdf|image/jpeg";

			// armazena o diretório de envio em uma variável
			$diretorio = DIR_UPLOADS . DS . "requerimentos";
			$diretorio .= DS . "comprovantes";
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

	/**
	*	Retorna html com a view da listagem de inscrições. 
	*	As inscrições com status interessado terão uma view específica com sub-abas.
	*/
	public function viewAction() {
		$modulo ='requerimentos';
		ob_start();
		require_once ('views/requerimentos/list.phtml');
		//require_once ('views/includes/listagem/tabs/rows.phtml');
		require_once ('views/includes/listagem/tabs/listagem.phtml');
		$contents = ob_get_contents();
        ob_end_clean();
        echo $contents;
	}

	public function requerimentosAction() {
		try {
			header("Access-Control-Allow-Origin: *");
			$conexao = $this->conexao->getConexao();
			$modulo = 'requerimentos';
			$dados = isset($_POST['dados']) ? $_POST['dados'] : array();
			$order = isset($_POST['order']) ? $_POST['order'] : 'data';
			$qs = isset($_POST['qs']) ? $_POST['qs'] : array();
			$retorno = isset($_POST['retorno']) ? $_POST['retorno'] : 'tabela';

			$situacao = $situacaoConstante = (int) $_POST['situacao'];
			
			$query1 = $conexao->query()->from("requerimentos");
			Requerimento::filterTabs($situacao, $query1);

			foreach ($qs as $key => $value) {
				if (!empty($value)) {
					if(preg_match('/[a-z\s-]/i', $value)) {
						$query1->where($key . ' LIKE ?', '%' . $value . '%');
					}
					else if(preg_match('/\d{2}\/\d{2}\/\d{4}/', $value)) {
						$query1->where($key . ' = ?', converteData($value));
					}
					else if(preg_match('/\d/', $value)) {
						$query1->where($key . ' = ?', (int) $value);
					}
					else {
						$query1->where($key . ' = ?', $value);
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
			
			$quantidade = $query1->count();

			$objetos = $query2->all();

			foreach ($objetos as $key => $obj) {
				// infos e labels
				$objetos[$key]["infos"] = $objetos[$key]["labels"] = array();
				$objetos[$key]["infos"][] = Requerimento::getSituacao($obj['situacao']);

				$vencimento = Funcoes::diferencaDatas(date('Y-m-d'), $obj['dataVencimento']);

				if($vencimento > 0) {
					$objetos[$key]["labels"][] = array(
						'class' => 'warning',
						'label' => 'Vencimento em '. $vencimento .' dias',
						'text' => 'Vencimento em '. $vencimento .' dias',
					);
				} 

				$vencido = Funcoes::diferencaDatas($obj['dataVencimento'], date('Y-m-d'));
				if($vencido == 0) {
					$objetos[$key]["labels"][] = array(
						'class' => 'warning',
						'label' => 'Vencimento hoje',
						'text' => 'Vencimento hoje'
					);
				}
				else if($vencido > 0) {
					$objetos[$key]["labels"][] = array(
						'class' => 'warning',
						'label' => 'Venceu a '. $vencido .' dias',
						'text' => 'Venceu a '. $vencido .' dias',
					);
				} 
			}

			$params["objetos"] = $objetos;

			$iniCount = 1;
			$fim = $quantidade;

			ob_start();
			require_once ('views/requerimentos/list.phtml');
			require_once ('views/includes/listagem/tabs/rows.phtml');
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
