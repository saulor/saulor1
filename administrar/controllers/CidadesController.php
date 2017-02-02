<?php

class CidadesController extends Controller {
	
	private $modulo = 'cidades';

	public function CidadesController() {
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
			//$this->checaPermissao('cidades', 'estados');
			$conexao = $this->conexao->getConexao();
			
			$vwEstadoModel = new VwEstado($conexao);
			$regioesModel = new Regiao($conexao);

			// todos os estados
			$estados = $vwEstadoModel->getObjetos(array(
					'order' => array(
						'nomeRegiao' => 'asc' 
					)
				)
			);
			// todas as regiões
			$regioes = $regioesModel->getObjetos();

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Cidades' => ''
			);
			
			if (count($_POST) > 0) {
				
				
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
			setMensagem("error", 'Cidade não encontrada');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
	
		$conexao->disconnect();					
		$view = new View2($this->modulo, "extendido", "estados.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				'objetos' => $estados,
				'regioes' => $regioes
			)
		);
        $view->showContents();
	}

	public function visualizarAction() {
		try {
			//$this->checaPermissao('cidades', 'cidades');
			$conexao = $this->conexao->getConexao();
			
			$estadosModel = new Estado($conexao);
			$cidadesModel = new VwCidadeEstado($conexao);

			$estado = $estadosModel->getObjetoOrFail(getVariavel('estado'));
			$cidades = $cidadesModel->getObjetos(array(
					'where' => array(
						'idEstado' => $estado->id
					),
					'order' => array(
						'nome' => 'asc'
					)
				)
			);

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Cidades' => '?modulo=' . $this->modulo,
 				$estado->nome => '',
			);
			
			if (count($_POST) > 0) {
				
				
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
			setMensagem("error", 'Cidade não encontrada');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
	
		$conexao->disconnect();					
		$view = new View2($this->modulo, "extendido", "cidades.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"estado" => $estado,
				'objetos' => $cidades
			)
		);
        $view->showContents();
	}
	
	public function cadastrarAction() {
		try {
			//$this->checaPermissao('cidades', 'cadastrar');
			$conexao = $this->conexao->getConexao();
			
			$vwEstadoModel = new VwEstado($conexao);
			$cidadeModel = new Cidade($conexao);

			$estado = $vwEstadoModel->getObjetoOrFail(getVariavel('estado'));

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Cidades' => '?modulo=' . $this->modulo,
				$estado->nome => '?modulo=' . $this->modulo . '&acao=visualizar&estado=' . $estado->id
			);
			
			if (isset($_GET["id"])) {
				$model = $cidadeModel->getObjetoOrFail(getVariavel('id'));
				$acao = "editar";
				$breadcrumbs[] = array(
					"Editar" => "" 
				);
			}
			else {
				$model = $cidadeModel;
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
						"nome" => "Nome"
					)
				);
				
				Funcoes::validaPost($obrigatorios, $dados);
				$model->salvar();

				$redirecionar = sprintf('?modulo=%s&acao=%s&id=%d&estado=%d', $this->modulo,
						'cadastrar', $model->id, $estado->id);

				if ($acao == 'cadastrar') {	
					$acao = 'cadastrada';
					$this->log->adicionar ('cadastrou', 'cidade', $model->nome, 
						'Usuário cadastrou cidade.');
				}
				else {
					$acao = 'atualizada';
					$this->log->adicionar ('atualizou', 'cidade', $model->nome, 
						'Usuário atualizou cidade.');
				}

				setMensagem('info', sprintf('Cidade %s [%s]', $acao, $model->nome));
				
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
			setMensagem("error", 'Cidade não encontrada');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
	
		$conexao->disconnect();					
		$view = new View2($this->modulo, "extendido", "cadastrar.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				"cidade" => $model,
				"estado" => $estado
			)
		);
        $view->showContents();
	}

	public function excluirAction() {
		try {
			// exclui cidades
			//$this->checaPermissao('cidades', 'excluir');
			$conexao = $this->conexao->getConexao();
			$model = new Cidade($conexao);
			$objeto = $model->getObjetoOrFail(getVariavel('id'));
			$affectedRows = $objeto->excluir();
			if ($affectedRows) {
				$this->log->adicionar ('exclui', 'cidade', $objeto->nome, 
					sprintf('Usuário exclui cidade %s.', $objeto->nome));
				$conexao->commit();					
				setMensagem('info', 'Cidade excluída [' . $objeto->nome . ']');
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
			setMensagem("error", 'Cidade não encontrada');
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

	public function desfazerAction() {
		try {
			// exclui cidades
			//$this->checaPermissao('cidades', 'excluir');
			$conexao = $this->conexao->getConexao();
			$model = new VwCidadeCurso($conexao);
			$objeto = $model->getObjetoOrFail(getVariavel('id'));
			$model->setTable('cidades_cursos');
			$affectedRows = $objeto->excluir();
			if ($affectedRows) {
				$this->log->adicionar ('desfez', 'associação', $objeto->nomeCidade, 
					sprintf('Usuário desfez associação da cidade %s com o curso %s.', 
						$objeto->nomeCidade, $objeto->nomeCurso));
				$conexao->commit();					
				setMensagem('info', 'Associação desfeita [' . $objeto->nomeCidade . ' e ' . $objeto->nomeCurso . ']');
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

	public function associarAction() {
		try {
			//$this->checaPermissao('cidades', 'associar');
			$conexao = $this->conexao->getConexao();
			
			$vwEstadoModel = new VwEstado($conexao);
			$cidadeModel = new Cidade($conexao);
			$cursoModel = new Curso($conexao);
			$cidadeCursoModel = new VwCidadeCurso($conexao);

			$cursos = $cursoModel->getObjetos(array(
					'order' => array(
						'nome' => 'asc'
					)
				)
			);
			$cidade = $cidadeModel->getObjetoOrFail(getVariavel('cidade'));
			$estado = $vwEstadoModel->getObjetoOrFail(getVariavel('estado'));

			$cursosAssociados = $cidadeCursoModel->getObjetos(array(
					'order' => array(
						'nomeCurso' => 'asc'
					),
					'where' => array(
						'idCidade' => $cidade->id
					)
				)
			);

			if (isset($_GET["id"])) {
				$model = $cidadeCursoModel->getObjetoOrFail(getVariavel('id'));
				$acao = "editar";
				$breadcrumbs[] = array(
					"Editar" => "" 
				);
			}
			else {
				$model = $cidadeCursoModel;
				$acao = "cadastrar";
				$breadcrumbs[] = array(
					"Nova" => "" 
				);
			}

			$breadcrumbs = array();
			$breadcrumbs[] = array(
				'Cidades' => '?modulo=' . $this->modulo,
				$estado->nome => '?modulo=' . $this->modulo . '&acao=visualizar&estado=' . $estado->id,
				$cidade->nome => '?modulo=' . $this->modulo . '&acao=cadastrar&id=' . $cidade->id . '&estado=' . $estado->id,
				'Associar cursos' => ''
			);
			
			if (count($_POST) > 0) {
				
				$dados = $_POST;
				
				$obrigatorios = array(
					"curso" => array(
						"nome" => "Curso"
					),
					'valorDesconto' => array(
						'nome' => 'Valor do desconto'
					),
					'valorCurso' => array(
						'nome' => 'Valor do curso'
					),
					'quantidadeParcelas' => array(
						'nome' => 'Parcelas'
					)
				);
				
				Funcoes::validaPost($obrigatorios, $dados);
				$model = new CidadeCurso($conexao);
				$model->setDados($dados);

				$mensagens = array();

				// valida a quantidade de parcelas
				if (!preg_match("/^([0-9]+)+(,[0-9]+)*$/", $dados["quantidadeParcelas"])) {
					$mensagens[] = 'Formato inválido para a quantidade de parcelas';
				}

				if (!preg_match("/^([1-9]{1}[\d]{0,2}(\.[\d]{3})*(\,[\d]{0,2})?|[1-9]{1}[\d]{0,}(\,[\d]{0,2})?|0(\,[\d]{0,2})?|(\,[\d]{1,2})?)$/", 
					$dados["valorInscricao"])) {
					$mensagens[] = 'Formato inválido para valor de inscrição';
				}

				if (!preg_match("/^([1-9]{1}[\d]{0,2}(\.[\d]{3})*(\,[\d]{0,2})?|[1-9]{1}[\d]{0,}(\,[\d]{0,2})?|0(\,[\d]{0,2})?|(\,[\d]{1,2})?)$/", 
					$dados["valorDesconto"])) {
					$mensagens[] = 'Formato inválido para valor de desconto';
				}

				if (!preg_match("/^([1-9]{1}[\d]{0,2}(\.[\d]{3})*(\,[\d]{0,2})?|[1-9]{1}[\d]{0,}(\,[\d]{0,2})?|0(\,[\d]{0,2})?|(\,[\d]{1,2})?)$/", 
					$dados["valorCurso"])) {
					$mensagens[] .= 'Formato inválido para valor de curso';
				}

				if (count($mensagens) > 0) {
					throw new Exception(implode("<br />", $mensagens));
				}
				
				$curso = $cursoModel->getObjetoOrFail($dados['curso']);
				$model->salvar();

				$redirecionar = sprintf('?modulo=%s&acao=%s&cidade=%d&estado=%d&id=%d', $this->modulo,
						'associar', $cidade->id, $estado->id, $model->id);

				if ($acao == 'cadastrar') {	
					$acao = 'realizada com sucesso';
					$this->log->adicionar ('associou', 'curso', $curso->nome, 
						sprintf('Usuário associou curso %s a cidade %s', 
							$curso->nome, $cidade->nome));
				}
				else {
					$acao = 'atualizada com sucesso';
					$this->log->adicionar ('atualizou associação', 'curso', $curso->nome, 
						sprintf('Usuário atualizou associação do curso %s na cidade %s', 
							$curso->nome, $cidade->nome));
				}

				setMensagem('info', sprintf('Associação %s [%s]', $acao, $curso->nome));
				
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
			setMensagem("error", 'Cidade não encontrada');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem("error", $e->getMessage());
		}
	
		$conexao->disconnect();					
		$view = new View2($this->modulo, "extendido", "associar.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
				"breadcrumbs" => $breadcrumbs,
				'estado' => $estado,
				'cidade' => $cidade,
				'cursos' => $cursos,
				'cursosAssociados' => $cursosAssociados,
				'objeto' => $model
			)
		);
        $view->showContents();
	}

	public function acoesAction() {
		try {
			$this->checaPermissao('cidades', "opcoes");
			$conexao = $this->conexao->getConexao();
			$processados = 0;
			$naoProcessados = 0;
			
			if (empty($_POST["acoes"])) {
				throw new Exception("É necessário escolher uma ação");
			}
			
			$cidadeModel = new Cidade($conexao);
			$objetos = isset($_POST["objetos"]) ? $_POST["objetos"] : array();
			
			// retira o elemento -1, caso exista
			if (count($objetos) > 0 && $objetos[0] == -1) {
				array_shift($objetos);
			}
			
			foreach ($objetos as $id) {

				$model = $cidadeModel->getObjeto($id);

				switch ($_POST['acoes']) {

					case 'ativar' :
						$opcao = "ativado(s)";	
						$model->status = 1;
						if ($model->salvar()) {
							$processados += 1;
							$this->log->adicionar ('ativou', 'cidade', $model->nome, 
								"Usuário ativou cidade através do recidade de aplicar ações em massa.");
						}
					break;

					case 'desativar' :
						$opcao = "desativado(s)";	
						$model->status = 0;
						if ($model->salvar()) {
							$processados += 1;
							$this->log->adicionar ('desativou', 'cidade', $model->nome, 
								"Usuário desativou cidade através do recidade de aplicar ações em massa.");
						}
					break;
				
					case 'excluir' :
						$opcao = "excluído(s)";	
						if ($model->excluir()) {
							$processados += 1;
							$this->log->adicionar ('excluiu', 'cidade', $model->nome, 
								"Usuário excluiu cidade através do recidade de aplicar ações em massa.");
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

	public function ajax1Action() {
		try {
			$conexao = $this->conexao->getConexao();
			$model = new VwEstado($conexao);

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
			include DIR_ROOT . '/administrar/views/' . $modulo . '/rows-estados.phtml';
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