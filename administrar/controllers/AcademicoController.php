<?php

class AcademicoController extends Controller {

	private $modulo = 'academico';

	public function AcademicoController() {
		try {
			parent::__construct();
		}
		catch (Exception $e) {
			$this->conexao->getConexao()->disconnect();
			setMensagem('error', $e->getMessage());
			Application::redirect('index.php');
			exit;
		}
	}

	public function indexAction() {
		Application::redirect('?modulo=academico&acao=visualizar');
		exit;
	}

	public function visualizarAction() {
		try {
			$this->checaPermissao($this->modulo, 'visualizar');
			$conexao = $this->conexao->getConexao();
			$model = new Academico($conexao);
			$breadcrumbs = array();
			$breadcrumbs[] = array('Acadêmico' => '');
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
		$view = new View2($this->modulo, 'extendido', 'visualizar.phtml');
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

	public function areaAction() {
		try {
			//$this->checaPermissao($this->modulo, 'area');
			$conexao = $this->conexao->getConexao();
			$model = new AcademicoUsuario($conexao);
			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Acadêmico' => '?modulo=' . $this->modulo,
				'Usuários' => ''
			);
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
		$view = new View2($this->modulo, 'extendido', 'area.phtml');
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

	public function cadastrar1Action() {
		try {
			$this->checaPermissao($this->modulo, 'cadastrar');
			$conexao = $this->conexao->getConexao();
			
			$academicoModel = new Academico($conexao);

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Acadêmico' => '?modulo=' . $this->modulo
			);
			
			if (isset($_GET['id'])) {
				$model = $academicoModel->getObjetoOrFail(getVariavel('id'));
				$acao = 'editar';
				$breadcrumbs[] = array(
					'Editar' => '' 
				);
			}
			else {
				$model = $academicoModel;
				$acao = 'cadastrar';
				$breadcrumbs[] = array(
					'Nova' => '' 
				);
			}
			
			if (count($_POST) > 0) {
				
				$redirecionar = NULL;
				$dados = $_POST;
				$model->setDados($dados);

				$obrigatorios = array(
					'cpf' => array(
						'nome' => 'CPF'
					),
					'nomeAluno' => array(
						'nome' => 'Nome'
					),
					'nomeCurso' => array(
						'nome' => 'Nome do Curso'
					),
					'nomeDisciplina' => array(
						'nome' => 'Nome da Disciplina'
					),
					'notaAluno' => array(
						'nome' => 'Nota'
					),
					'dataInicio' => array(
						'nome' => 'Data Início'
					),
					'dataFim' => array(
						'nome' => 'Data Fim'
					),
					'numeroFaltas' => array(
						'nome' => 'Faltas'
					)
				);
				
				Funcoes::validaPost($obrigatorios, $dados);
				
				$model->salvar();

				if ($acao == 'cadastrar') {	
					$acao = 'cadastrada';
					$this->log->adicionar ('cadastrou', 'frequência/nota', $model->nomeAluno, 
						'Usuário cadastrou banner.');					
					$redirecionar = sprintf('?modulo=%s&acao=%s&id=%d', $this->modulo, 
						'cadastrar', $model->id);
				}
				else {
					$acao = 'atualizada';
					$this->log->adicionar ('atualizou', 'frequência/nota', $model->nomeAluno, 
						'Usuário atualizou banner.');
					$redirecionar = sprintf('?modulo=%s', $this->modulo);
				}

				setMensagem('info', sprintf('Frequência/Nota %s [%s]', $acao, $model->nomeAluno));
				
				$conexao->commit();
				$conexao->disconnect();	
				Application::redirect($redirecionar);
				exit;
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
			setMensagem('error', 'Frequência/Nota não encontrada');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem('error', $e->getMessage());
		}
	
		$conexao->disconnect();					
		$view = new View2($this->modulo, 'extendido', 'cadastrar.phtml');
		$view->setParams(array(
				'title' => getTitulo($breadcrumbs),
				'breadcrumbs' => $breadcrumbs,
				'objeto' => $model
			)
		);
        $view->showContents();
	}

	public function cadastrar2Action() {
		try {
			$this->checaPermissao($this->modulo, 'cadastrar');
			$conexao = $this->conexao->getConexao();
			
			$academicoModel = new AcademicoUsuario($conexao);

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Acadêmico' => '?modulo=' . $this->modulo,
				'Usuários' => '?modulo=' . $this->modulo . '&acao=area',
			);
			
			if (isset($_GET['id'])) {
				$model = $academicoModel->getObjetoOrFail(getVariavel('id'));
				$senha = $model->senha;
				$acao = 'editar';
				$breadcrumbs[] = array(
					'Editar' => '' 
				);
				$obrigatorios = array(
					'cpf' => array(
						'nome' => 'CPF'
					),
					'email' => array(
						'nome' => 'E-mail'
					)
				);
			}
			else {
				$model = $academicoModel;
				$acao = 'cadastrar';
				$breadcrumbs[] = array(
					'Novo' => '' 
				);
				$obrigatorios = array(
					'cpf' => array(
						'nome' => 'CPF'
					),
					'email' => array(
						'nome' => 'E-mail'
					),
					'senha' => array(
						'nome' => 'Senha'
					)
				);
			}
			
			if (count($_POST) > 0) {
				
				$redirecionar = NULL;
				$dados = $_POST;
				$model->setDados($dados);
				
				Funcoes::validaPost($obrigatorios, $dados);
				if ($model->existe('cpf')) {
					throw new Exception('Já existe um usuário com esse cpf');	
				}
				if ($model->existe('email')) {
					throw new Exception('Já existe um usuário com esse e-mail');	
				}
				if (empty($dados['senha']) && isset($senha)) {
					$model->senha = $senha;
				}
				$model->salvar();

				if ($acao == 'cadastrar') {	
					$acao = 'cadastrado';
					$this->log->adicionar ('cadastrou', 'usuário', $model->cpf, 
						'Usuário cadastrou usuário no módulo acadêmico.');					
					$redirecionar = sprintf('?modulo=%s&acao=%s&id=%d', $this->modulo, 
						'cadastrar2', $model->id);
				}
				else {
					$acao = 'atualizado';
					$this->log->adicionar ('atualizou', 'usuário', $model->cpf, 
						'Usuário atualizou usuário no módulo acadêmico.');
					$redirecionar = sprintf('?modulo=%s&acao=area', $this->modulo);
				}

				setMensagem('info', sprintf('Usuário %s [%s]', $acao, $model->cpf));
				
				$conexao->commit();
				$conexao->disconnect();	
				Application::redirect($redirecionar);
				exit;
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
			setMensagem('error', 'Frequência/Nota não encontrada');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem('error', $e->getMessage());
		}
	
		$conexao->disconnect();					
		$view = new View2($this->modulo, 'extendido', 'atualizar.phtml');
		$view->setParams(array(
				'title' => getTitulo($breadcrumbs),
				'breadcrumbs' => $breadcrumbs,
				'objeto' => $model
			)
		);
        $view->showContents();
	}

	public function excluir1Action() {
		try {
			$this->checaPermissao($this->modulo, 'excluir');
			$conexao = $this->conexao->getConexao();
			$model = new Academico($conexao);
			$obj = $model->getObjetoOrFail(getVariavel('id'));
			$affectedRows = $obj->excluir();
			if ($affectedRows) {
				$this->log->adicionar ('excluiu', 'frequência/nota', $obj->nomeAluno, 
					sprintf('Usuário excluiu frequência/nota do curso %d %s.', 
						$model->codigoCurso, $model->nomeCurso));
				$conexao->commit();					
				setMensagem('info', 'Frequência/Nota excluída [' . $obj->nomeAluno . ']');
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
			setMensagem('error', 'Frequência/Nota não encontrada');
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
			$this->checaPermissao($this->modulo, 'excluir');
			$conexao = $this->conexao->getConexao();
			$model = new AcademicoUsuario($conexao);
			$obj = $model->getObjetoOrFail(getVariavel('id'));
			$affectedRows = $obj->excluir();
			if ($affectedRows) {
				$this->log->adicionar ('excluiu', 'usuário', $obj->cpf, 
					'Usuário excluiu usuário do módulo acadêmico.');
				$conexao->commit();					
				setMensagem('info', 'Usuário excluído [' . $obj->cpf . ']');
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
			setMensagem('error', 'Usuário não encontrado');
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

	public function acoes1Action() {
		try {
			$this->checaPermissao($this->modulo, 'opcoes');
			$conexao = $this->conexao->getConexao();
			$processados = 0;
			$naoProcessados = 0;
			
			if (empty($_POST['acoes'])) {
				throw new Exception('É necessário escolher uma ação');
			}
			
			$bannerModel = new Academico($conexao);
			$objetos = isset($_POST['objetos']) ? $_POST['objetos'] : array();
			
			// retira o elemento -1, caso exista
			if (count($objetos) > 0 && $objetos[0] == -1) {
				array_shift($objetos);
			}
			
			foreach ($objetos as $id) {

				$model = $bannerModel->getObjeto($id);

				switch ($_POST['acoes']) {
				
					case 'excluir' :
						$opcao = 'excluído(s)';	
						if ($model->excluir()) {
							$processados += 1;
							$this->log->adicionar ('excluiu', 'frequência/nota', $model->nomeAluno, 
								'Usuário excluiu frequência/nota através do recurso de aplicar ações em massa.');
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
			setMensagem('error', $e->getMessage());
			Application::redirect('index.php');
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem('error', $e->getMessage());
		}
		
		if ($processados > 0) {
			$conexao->commit();
			setMensagem('info', $processados . ' ' . $opcao);
		}
		
		if ($naoProcessados > 0) {
			setMensagem('error', $naoProcessados . ' não pode(m) ser ' . $opcao);
		}
		
		$conexao->disconnect();
		Application::redirect($_SERVER['HTTP_REFERER']);
		exit;
	}

	public function acoes2Action() {
		try {
			$this->checaPermissao($this->modulo, 'opcoes');
			$conexao = $this->conexao->getConexao();
			$processados = 0;
			$naoProcessados = 0;
			
			if (empty($_POST['acoes'])) {
				throw new Exception('É necessário escolher uma ação');
			}
			
			$objModel = new AcademicoUsuario($conexao);
			$objetos = isset($_POST['objetos']) ? $_POST['objetos'] : array();
			
			// retira o elemento -1, caso exista
			if (count($objetos) > 0 && $objetos[0] == -1) {
				array_shift($objetos);
			}
			
			foreach ($objetos as $id) {

				$model = $objModel->getObjeto($id);

				switch ($_POST['acoes']) {
				
					case 'excluir' :
						$opcao = 'excluído(s)';	
						if ($model->excluir()) {
							$processados += 1;
							$this->log->adicionar ('excluiu', 'usuário', $model->cpf, 
								'Usuário excluiu usuário do módulo acadêmico através do recurso de aplicar ações em massa.');
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
			setMensagem('error', $e->getMessage());
			Application::redirect('index.php');
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem('error', $e->getMessage());
		}
		
		if ($processados > 0) {
			$conexao->commit();
			setMensagem('info', $processados . ' ' . $opcao);
		}
		
		if ($naoProcessados > 0) {
			setMensagem('error', $naoProcessados . ' não pode(m) ser ' . $opcao);
		}
		
		$conexao->disconnect();
		Application::redirect($_SERVER['HTTP_REFERER']);
		exit;
	}

	public function carregarAction() {
		try {
			$this->checaPermissao($this->modulo, 'carregar');
			// lê os arquivos texto e salva os registros no banco
			$conexao = $this->conexao->getConexao();
			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Acadêmico' => '?modulo=academico&acao=visualizar',
				'Carregar Arquivo' => ''
			);
			$cadastrados = isset($_GET['cadastrados']) ? (int) $_GET['cadastrados'] : 0;
			$atualizados = isset($_GET['atualizados']) ? (int) $_GET['atualizados'] : 0;

			if (count($_POST)) {

				$path1 = DIR_ROOT . DS . 'administrar' . DS . 'temp';
				$path1 .= DS . $_GET['file'];

				if (!existeArquivo($path1)) {
					throw new Exception('Arquivo não encontrado');
				}

				$fp = fopen($path1, 'r');

				while (!feof($fp)) {

					$linha = fgets($fp);

					if (!empty($linha)) {

						list($id,$codigoAluno,$nomeAluno,$dataMatAluno,$codigoCurso,$nomeCurso,$unidade,
							$nomeDisciplina,$professor,$titulo,$notaAluno,$situacaoNota,$numeroFaltas,
							$notaTrabalho,$dataInicio,$dataFim,$email,$cpf,$dataFimCurso,$notaSubstituida, 
							$dataReposicao,$obsReposicao) = explode(';', $linha);

						if (!empty($dataFimCurso)) {
							$dataFimCursoTmsp = strtotime($dataFimCurso);
							$dataFimCurso = date('Y-m-d', $dataFimCursoTmsp);
						}

						if (!empty($dataReposicao)) {
							$dataReposicaoTmsp = strtotime($dataReposicao);
							$dataReposicao = date('Y-m-d', $dataReposicaoTmsp);
						}

						if (!empty($dataInicio)) {
							list($dia, $mes, $ano) = explode('/', trim($dataInicio));
							if (!empty($dia)) {
								$dataInicio = $ano . '-' . $mes . '-' . $dia;
							}
						}

						if (!empty($dataFim)) {
							list($dia, $mes, $ano) = explode('/', trim($dataFim));
							if (!empty($dia)) {
								$dataFim = $ano . '-' . $mes . '-' . $dia;
							}
						}

						$query = $conexao->query()
							->from('academico');

						$dados = array(
							'codigoAluno' => $codigoAluno,
							//'numSeqAluno' => $numSeqAluno,
							'nomeAluno' => codificaDado(utf8_encode(trim($nomeAluno))),
							'dataMatAluno' => empty($dataMatAluno) ? NULL : $dataMatAluno,
							'situacaoNota' => $situacaoNota,
							'codigoCurso' => (int) $codigoCurso,
							'nomeCurso' => codificaDado(utf8_encode(trim($nomeCurso))),
							'nomeDisciplina' => codificaDado(utf8_encode(trim($nomeDisciplina))),
							//'cargaHorDisciplina' => (int) $cargaHorDisciplina,
							'notaAluno' => converteDecimal($notaAluno),
							'situacaoNota' => trim($situacaoNota),
							'numeroFaltas' => (int) $numeroFaltas,
							'notaTrabalho' => converteDecimal($notaTrabalho),
							'dataInicio' => empty($dataInicio) ? NULL : $dataInicio,
							'dataFim' => empty($dataFim) ? NULL : $dataFim,
							'cpf' => trim(preg_replace('/(-|\.)/', '', $cpf)),
							'dataFimCurso' => empty($dataFimCurso) ? NULL : $dataFimCurso,
							'notaSubstituida' => converteDecimal($notaSubstituida),
							'dataReposicao' => empty($dataReposicao) ? NULL : $dataReposicao,
							'obsReposicao' => codificaDado(utf8_encode(trim($obsReposicao))),
							'email' => $email,
							'professor' => codificaDado(utf8_encode(trim($professor))),
							'titulo' => codificaDado(utf8_encode(trim($titulo))),
							'unidade' => codificaDado(utf8_encode(trim($unidade))),
						);

						// se o registro ainda não existe
						if ($id == 0) {
							$dados['timestamp'] = time();
							$dados['data'] = date('Y-m-d H:i:s', $dados['timestamp']);
							$query->save($dados);
							$cadastrados++;
						}
						else {
							$query->where('id = ?', (int) $id);
							$affected = $query->save($dados);
							$atualizados++;
						}
					}
					
				}

				excluiArquivo($path1);
				setMensagem('info', $cadastrados . ' registro(s) cadastrado(s)');
				setMensagem('info', $atualizados . ' registro(s) atualizado(s)');
				$this->log->adicionar ('carregou', 'arquivo', $_GET['file'], 
					'Usuário carregou arquivo texto com registro de frequências e notas do sistema acadêmico.');
				$conexao->commit();
				Application::redirect('?modulo=academico&acao=visualizar');
				exit;

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
			excluiArquivo($path1);
		}

		$conexao->disconnect();
		$view = new View2($_GET['modulo'], 'extendido', 'index.phtml');
		$view->setParams(array(
				'title' => getTitulo($breadcrumbs),
				'breadcrumbs' => $breadcrumbs
			)
		);
        $view->showContents();
	}

	public function processaAction() {

		try {
			$this->checaPermissao($this->modulo, 'processa');
			// recebe o arquivo original e cria os arquivos texto
			$conexao = $this->conexao->getConexao();
			$redirecionar = '?modulo=academico&acao=carregar';

			if (!isset($_FILES['arquivo']) || !enviouArquivo($_FILES['arquivo'])) {
				throw new Exception('É necessário escolher um arquivo');
			}

			$path = DIR_ROOT . DS . 'administrar' . DS . 'temp';

			$tiposPermitidos = 'text/plain';

			// verifica o tipo de arquivo
			// apenas arquivos texto são permitidos
			if (!verificaTipo ($_FILES['arquivo'], $tiposPermitidos)) {
				throw new Exception('Tipo de arquivo não permitido');
			}

			// envia/salva o arquivo
			if (!salvaArquivo ($_FILES['arquivo'], $path)) {
				throw new Exception('Erro ao tentar enviar arquivo');
			}

			// contador da quantidade de registros que vai entrar no arquivo
			$registrosEncontrados = $registrosProcessados = 0;
			// array auxiliares para remover entradas duplicadas
			$arr1 = $arr2 = array();
			// arquivo submetido
			$dir1 = $path . DS . $_FILES['arquivo']['name'];
			// diretório para o arquivo criado para receber as informações do arquivo submetido tratadas
			$dir2 = $path . DS . '_' . $_FILES['arquivo']['name'];
			// arquivo submetido
			$fp1 = fopen($dir1, 'r');
			// arquivo que receberá as informações processadas do arquivo submetido
			$fp2 = fopen($dir2, 'w');

			// para cada linha do arquivo submetido
			while (!feof($fp1)) {

				// recupera a linha
				$linha = fgets($fp1);

				if (!empty($linha)) {

					$registrosEncontrados++;

					// explode todos os dados e limpa espaços em branco
					$informacoes = array_map('trim', explode(';', $linha, 27));

					// quantida de dados que deve existir no array explodido
					if (count($informacoes) < 27) {
						continue;
					}

					// explode os elementos atribuindo cada um a uma variável
					list($codigoAluno,,,$nomeAluno,$dataMatAluno,,$codigoCurso,,$nomeCurso,$unidade,
						$nomeDisciplina,$professor,$titulo,,$notaAluno,$situacaoNota,$numeroFaltas,
						$notaTrabalho,$dataInicio,$dataFim,$email,$cpf,$dataFimCurso,$notaSubstituida, 
						$dataReposicao,$obsReposicao) = $informacoes;

					// conjunto de dados utilizados para verificar entradas duplicadas
					$duplicadosArr = array(
						$cpf,
						$codigoCurso,
						$nomeDisciplina,
						$professor,
						$titulo,
						converteData($dataInicio),
						converteData($dataFim),
						$notaAluno
					);

					// dados que irão para o arquivo texto
					$linhaArr = array(
						$codigoAluno,
						$nomeAluno,
						$dataMatAluno,
						$codigoCurso,
						$nomeCurso,
						$unidade,
						$nomeDisciplina,
						$professor,
						$titulo,
						$notaAluno,
						$situacaoNota,
						$numeroFaltas,
						$notaTrabalho,
						$dataInicio,
						$dataFim,
						$email,
						$cpf,
						$dataFimCurso,
						$notaSubstituida,
						$dataReposicao,
						$obsReposicao
					);

					// verificar duplicados
					$l = implode(';', $duplicadosArr);

					if (!in_array($l, $arr1)) {
						// verifica se o registro é duplicado
						$registro = $this->_getRegistro($conexao, $cpf, $codigoCurso, $nomeDisciplina, 
							$professor, $titulo, $dataInicio, $dataFim);

						if (count($registro) > 0) {
							// 1 na frente da linha indica duplicado
							$linha = $registro['id'] . ';' . implode(';', $linhaArr);
						}
						else {
							// 0 na frente da linha indica não duplicado
							$linha = '0;' . implode(';', $linhaArr);
						}
					}
					else {
						$linha = '-1;' . implode(';', $linhaArr);
					}

					fwrite($fp2, $linha);
					fwrite($fp2, "\r\n");
					$registrosProcessados++;
					$arr1[] = $l;
				}				
				
			}

			fclose($fp1);
			fclose($fp2);

			unlink($path . DS . $_FILES['arquivo']['name']);
			$this->log->adicionar ('enviou', 'arquivo', $_FILES['arquivo']['name'], 
				'Usuário enviou arquivo texto com registros de frequências e notas do
				sistema acadêmico.');

			$conexao->commit();
			$conexao->disconnect();

			setMensagem('info', $registrosEncontrados . ' registro(s) encontrado(s) no arquivo ' . 
				$_FILES['arquivo']['name']);
			setMensagem('info', $registrosProcessados . ' registro(s) processados(s) no arquivo ' . 
				$_FILES['arquivo']['name']);

			$redirecionar = '?modulo=academico&acao=preview';
			$redirecionar .= '&previsualizar=' . $_POST['previsualizar'];
			$redirecionar .= '&file=' . urlencode('_' . $_FILES['arquivo']['name']);

			
		}
		catch (PermissaoException $e) {
			$this->conexao->getConexao()->disconnect();
			setMensagem('error', $e->getMessage());
			Application::redirect('index.php');
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem('error', $e->getMessage());
		}

		Application::redirect($redirecionar);
		exit;
	}

	public function previewAction() {
		try {
			// mostra o arquivo texto pro usuário
			$this->checaPermissao($this->modulo, 'preview');
			$conexao = $this->conexao->getConexao();
			$breadcrumbs = array();
			$breadcrumbs[] = array('Acadêmico' => '?modulo=academico&acao=visualizar');
			$registros = isset($_GET['registros']) ? (int) $_GET['registros'] : 0;
			$view = 'preview1.phtml';

			// lê o arquivo texto e cria um array com os dados
			// para mostrar na view
			$preview = 0;
			// flag
			$exclui = false;
			$conteudo = array();

			$path = DIR_ROOT . DS . 'administrar' . DS . 'temp' . DS;

			if (isset($_GET['file'])) {

				// view que irá mostrar uma pré-visualização antes do cadastro em si
				$view = 'preview1.phtml';
				// path para o arquivo original
				$pathArquivo = $path1 = $path . $_GET['file'];

				if (!existeArquivo($pathArquivo)) {
					$redirecionar = '?modulo=academico&acao=visualizar';
					throw new Exception('Arquivo não encontrado');
				}

				$fp = fopen($pathArquivo, 'r');
				while (!feof($fp)) {
					$linha = fgets($fp);
					if (!empty($linha)) {
						$preview++;
						$conteudo[] = $linha;
					}
					if (isset($_GET['previsualizar']) && $preview == $_GET['previsualizar']) {
						break;
					}
				}
				fclose($fp);
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
			if (!empty($redirecionar)) {
				Application::redirect($redirecionar);
				exit;
			}
		}

		$conexao->disconnect();
		$view = new View2($_GET['modulo'], 'extendido', $view);
		$view->setParams(array(
				'title' => getTitulo($breadcrumbs),
				'conteudo' => $conteudo,
				'registros' => $registros,
				'breadcrumbs' => $breadcrumbs
			)
		);
		$view->showContents();
	}

	public function lerAction() {
		try {
			$this->checaPermissao($this->modulo, 'ler');
			$conexao = $this->conexao->getConexao();
			$redirecionar = '?modulo=academico&acao=visualizar';
			$breadcrumbs = array();
			$breadcrumbs[] = array('Acadêmico' => '?modulo=academico&acao=visualizar');
			$registros = isset($_GET['registros']) ? (int) $_GET['registros'] : 0;
			$view = 'preview1.phtml';

			// lê o arquivo texto e cria um array com os dados
			// para mostrar na view
			$preview = 0;
			$conteudo = array();

			if (!isset($_GET['file'])) {
				throw new Exception('Arquivo não encontrado');
			}

			if (isset($_GET['file'])) {

				$path = DIR_ROOT . DS . 'administrar' . DS . 'temp' . DS;
				$path .= $_GET['file'];

				if (!existeArquivo($path)) {
					$redirecionar = '?modulo=academico&acao=visualizar';
					throw new Exception('Verifique as informações no sistema acadêmico envie o arquivo texto
						novamente.');
				}

				$fp = fopen($path, 'r');
				while (!feof($fp)) {
					$linha = fgets($fp);
					if (!empty($linha)) {
						$conteudo[] = $linha;
					}
				}
				fclose($fp);
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
			if (!empty($redirecionar)) {
				Application::redirect($redirecionar);
				exit;
			}
		}

		$conexao->disconnect();
		$view = new View2($_GET['modulo'], 'extendido', 'preview1.phtml');
		$view->setParams(array(
				'title' => getTitulo($breadcrumbs),
				'conteudo' => $conteudo,
				'registros' => $registros,
				'breadcrumbs' => $breadcrumbs
			)
		);
		$view->showContents();
	}

	public function imprimirAction() {

		try {

			$conexao = $this->conexao->getConexao();
			$redirecionar = '?modulo=' . $_GET['modulo'];

			$query = $conexao->query()
				->from('academico');

			foreach ($_GET as $key => $value) {
				if (!in_array($key, array('modulo', 'acao', 'data')) && !empty($value)) {
					$query->where($key . ' like ?', '%' . $value . '%');
				}
			}

			if (isset($_GET['data']) && !empty($_GET['data']) && preg_match('/^\d{2}\\/\d{2}\/\d{4}$/',
				$_GET['data'])) {
				list($dia, $mes, $ano) = explode('/', $_GET['data']);
				$data = $ano . '-' . $mes . '-' . $dia;
				if ($data) {
					$query->where('DATE(data) = ?', $data);
				}
			}

			$dados = $query->all();

			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator('IEFAP');
			$objPHPExcel->getProperties()->setLastModifiedBy('IEFAP');
			$objPHPExcel->getProperties()->setTitle('Integração Acadêmico IEFAP');
			$objPHPExcel->getProperties()->setSubject('Integração Acadêmico IEFAP');
			$objPHPExcel->getProperties()->setDescription('IEFAP - Integração Acadêmico');
			$objPHPExcel->setActiveSheetIndex(0);

			$numeroLinha = 1;
			$numeroColuna = 0;

			foreach (array(
					'Nome',
					'Curso',
					'Disciplina',
					'Nota',
					'Situação',
					'Faltas',
					'Data'
				) as $coluna) {
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, $numeroLinha, $coluna,
					false);
			}

			$numeroLinha = 2;
			$numeroColuna = 0;

			foreach ($informacoes as $key => $value) {
				if (array_key_exists('data', $informacoes[$key])) {
					$value['data'] = desconverteData(substr($value['data'],0,10));
				}
				foreach ($value as $campo => $valor) {
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($numeroColuna++, $numeroLinha, $valor,
						false);
				}
				$numeroLinha++;
				$numeroColuna = 0;
			}

			$nomeArquivo = 'Integração-Acadêmico-IEFAP.xlsx';
			$path = DIR_ROOT . DS . 'administrar' . DS . 'temp';

			if (is_writable($path)) {
				$path .=  DS . $nomeArquivo;
				$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
				$objWriter->save($path);
				$this->log->adicionar ('gerou', 'listagem', 'EXCEL',
					'Usuário gerou listagem de integração com o sistema acadêmico em EXCEL.');
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
				throw new Exception ('Erro ao tentar criar arquivo ' . $nomeArquivo);
			}

		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem('error', $e->getMessage());
		}

		$conexao->disconnect();
		if (isset($_GET['p'])) {
			$redirecionar .= '&p='.$_GET['p'];
		}
		Application::redirect($redirecionar);
		exit;
	}

	public function ajax1Action() {
		try {
			$conexao = $this->conexao->getConexao();
			$model = new Academico($conexao);

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

	public function ajax2Action() {
		try {
			$conexao = $this->conexao->getConexao();
			$model = new AcademicoUsuario($conexao);

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
			include DIR_ROOT . '/administrar/views/' . $modulo . '/rows-area.phtml';
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

	private function _getRegistro ($conexao, $cpf, $codigo, $disciplina, $professor, $titulo, 
		$dataInicio = NULL, $dataFim = NULL) {

		// trata os dados
		$cpf = preg_replace('/(-|\.)/', '', $cpf);
		$disciplina = mb_strtolower(codificaDado(utf8_encode($disciplina)));
		$professor = mb_strtolower(codificaDado(utf8_encode($professor)));
		$titulo = mb_strtolower(codificaDado(utf8_encode($titulo)));
		$dataInicio = converteData($dataInicio);
		$dataFim = converteData($dataFim);

		$query = $conexao->query()
			->from('academico')
			->where('cpf = ?', $cpf)
			->where('codigoCurso = ?', (int) $codigo)
			->where('LOWER(nomeDisciplina) = ?', $disciplina);
			//->where('LOWER(professor) = ?', $professor)
			//->where('LOWER(titulo) = ?', $titulo);

		if ($dataInicio == NULL) {
			$query->where('dataInicio IS NULL');
		}
		else {
			$query->where('dataInicio = ?', $dataInicio);
		}

		if ($dataFim == NULL) {
			$query->where('dataFim IS NULL');
		}
		else {
			$query->where('dataFim = ?', $dataFim);
		}

		$query->order('data', 'desc');
		
		return $query->first();
	}
}
?>
