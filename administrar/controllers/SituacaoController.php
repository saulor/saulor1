<?php

class SituacaoController extends Controller {

	public function SituacaoController() {
		parent::__construct();
	}

	private function getObrigatorios ($tipo, $motivo) {

		$rules = array(
            'tipo' => 'required|integer'
        );

        $r = array();

		switch ($tipo) {

			case Situacao::SITUACAO_INSCRICAO_TIPO_CONTATO :
			case Situacao::SITUACAO_INSCRICAO_TIPO_EM_NEGOCIACAO :
				if ($motivo != 'Número inexistente') {
					$r = array(
		                'motivo' => 'required',
		                'data' => 'required',
		                'horario' => 'required'
		            );
				}
			break;

			case Situacao::SITUACAO_INSCRICAO_TIPO_GRADUANDO :
				$r = array(
	                'data' => 'required',
	                'horario' => 'required'
	            );
			break;

			case Situacao::SITUACAO_INSCRICAO_TIPO_CIDADE_CANDIDATA :
				$r = array(
	                'estado' => 'required',
	                'cidade' => 'required'
	            );
			break;

			case Situacao::SITUACAO_INSCRICAO_TIPO_FINANCEIRO :
			case Situacao::SITUACAO_INSCRICAO_TIPO_OUTROS :
			case Situacao::SITUACAO_INSCRICAO_TIPO_PERDIDO :
				$r = array(
	                'motivo' => 'required'
	            );
			break;
			
		}

		if ($r) {
			$rules = array_merge($rules, $r);
		}

		return $rules;
	}

	public function salvaAction() {

		try {
			$conexao = $this->conexao->getConexao();
			$time = time();
			$validator = new MyGump();

			// dados do objeto situacao
			$objeto = $_POST['objeto'];
			// dados adicionais
			$dados = $_POST['dados'];
			
			$tipoAntigo = '';
			$tipoSituacao = Situacao::getTipo($objeto['tipo']);

			// id = 0  cadastrando situação
			// id != 0 editanddo situação
			$id = (int) array_shift($objeto);

			// dependendo da situacão os campos obrigatórios são diferentes
			// alguns exigem motivo outros não
			$rules = $this->getObrigatorios($objeto['tipo'], $objeto['motivo']);

            $filters = array(
            	'tipo' => 'integer',
            	'usuario' => 'integer',
            	'inscricao' => 'integer',
                'motivo' => 'trim|sanitize_string|encode_entities',
                'cidade' => 'trim|sanitize_string|encode_entities',
                'observacoes' => 'trim|sanitize_string|encode_entities',
                'data' => 'trim|sanitize_string|encode_date',
                'horario' => 'trim|sanitize_string|encode_entities'
            );

            MyGump::set_field_name('data', 'Data');
            MyGump::set_field_name('horario', 'Horário');

            $validated = $validator->is_valid($objeto, $rules);
            if(is_array($validated)) {
                throw new Exception(Funcoes::getValidationErrors($validated));
            }

            $objeto = $validator->filter($objeto, $filters);
            if (isset($objeto['data'])) {
            	$objeto['timestampData'] = strtotime($objeto['data']);	
            }

            $query = $conexao->query()->from("situacao");

			if ($id == 0) {
				$acao = 'cadastrou';
				$objeto['timestampDataC'] = $time;
				$objeto['dataC'] = date('Y-m-d H:i:s', $objeto['timestampDataC']);
			}
			else {
				$acao = 'atualizou';
				$query->where("id = ?", $id);
				// recupero a situação que deve ser editada
				$situacao = $conexao->query()->from("situacao")
					->where("id = ?", $id)
					->first();
				// armazeno o tipo antigo
				$tipoAntigo = Situacao::getTipo($situacao['tipo']);
			}

			// salva situação
			$newId = $query->save($objeto);

			if ($newId > 1) {
				$id = $newId;
			}

			// atualiza situação da inscrição
			$conexao->query()->from("preinscricoes")
				->where('id = ?', (int) $objeto['inscricao'])
				->save(array(
						'situacao' => (int) $id
					)
				);

			$log = new Log($conexao);
			$descricao = 'Usuário %s situação na inscrição de %s no curso %s';
			if (!empty($tipoAntigo)) {
				$descricao .= '. ' . sprintf('Mudou de %s para %s', $tipoAntigo, $tipoSituacao) . '.';
			}
			$log->adicionar ($acao, "situação", Situacao::getTipo($objeto['tipo']), 
				sprintf($descricao, $acao, $dados['nome'], $dados["nomeCurso"]));

			$historico = new HistoricoPreinscricao($conexao);
			if (empty($tipoAntigo)) {
				$descricao = sprintf('Definiu situação %s', $tipoSituacao);
			}
			else {
				$descricao = sprintf('Mudou situação de %s para %s', $tipoAntigo, $tipoSituacao);
			}
			$historico->adicionar ($objeto['inscricao'], $descricao);

			if ($id == 0) {
				$id = (int) $newId;	
			}

			$situacao = $conexao->query()->from('vw_situacao')
				->where('id = ?', (int) $id)
				->first();

			$resumo = Situacao::getResumo($situacao);

			$conexao->commit();

			echo json_encode(array(
					'id' => $id,
					'type' => 'success',
					'message' => 'Salvo com sucesso',
					'curso' => $situacao['idCurso'],
					'tipo' => $objeto['tipo'],
					'tipoText' => $tipoSituacao,
					'motivo' => $objeto['motivo'],
					'resumo' => Situacao::getResumo($situacao)
				)
			);
		}
		catch (Exception $e) {
			$conexao->rollback();
			echo json_encode(array(
					'type' => 'danger',
					'message' => $e->getMessage()
				)
			);
		}
	}

	public function findAction() {

		try {
			$conexao = $this->conexao->getConexao();
			$situacao = $conexao->query()->from("vw_situacao")
				->where("id = ?", (int) $_POST['id'])
				->first();

			if (!$situacao) {
				throw new Exception('Situação não encontrada');
			}

			$s = array_map("decodificaDado", $situacao);
			$s['data'] = desconverteData($s['data']);
			echo json_encode(array(
					'id' => $s['id'],
					'tipo' => $s['tipo'],
					'motivo' => $s['motivo'],
					'data' => $s['data'],
					'horario' => $s['horario'],
					'usuario' => $s['idUsuario'],
					'observacoes' => $s['observacoes'],
					'cidade' => $s['cidade'],
					'estado' => $s['estado']
				)
			);
		}
		catch (Exception $e) {
			$conexao->rollback();
			$conexao->disconnect();
			echo json_encode(array(
					'type' => 'danger',
					'message' => $e->getMessage()
				)
			);
		}
	}

	public function tiposAction() {
		$options = '';
		$tipos = Situacao::getTipos();
		foreach ($tipos as $key => $value) {
			$options .= sprintf('<option value="%d">%s</option>', $key, $value);
		}
		echo $options;
	}

	public function tabsAction () {
		try {
			$conexao = $this->conexao->getConexao();
			$situacaoModel = new Situacao($conexao);
			echo $situacaoModel->getTabs((int) $_POST['curso']);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public function deleteAction() {
		try {
			$conexao = $this->conexao->getConexao();
			$time = time();

			// recupera situacao
			$query = $conexao->query()->from("situacao")
				->where("id = ?", (int) $_POST['id']); 

			$q1 = clone $query;

			$situacao = $q1->first();
			if (!$situacao) {
				throw new Exception('Nota não encontrada');
			}

			$q2 = clone $query;
			$q2->delete();

			$conexao->query()->from("preinscricoes")
				->where("id = ?", (int) $situacao['inscricao'])
				->save(array(
						'situacao' => 0
					)
				);

			// $tipoSituacao = Situacao::getTipo($objeto['tipo']);

			// 

			// // exclui situacao
			// $conexao->query()->from("situacao")
			// 	->where("id = ?", (int) $situacao['id'])
			// 	->save(array(
			// 			'deleted' => 1,
			// 			'deletedBy' => $_SESSION[PREFIX . "loginId"],
			// 			'deletedTime' => $time,
			// 			'deletedDate' => date('Y-m-d H:i:s')
			// 		)
			// 	);
			// 	//->delete();

			// $historicoDescricao = 'Nota %s excluída';
			// $logDescricao = 'Usuário %s situação %s na inscrição de %s inscrita no curso %s';

			// // adiciona no histórico
			// $time = time();
			// $conexao->query()
			// 	->from('historico')
			// 	->save(array(
			// 		'preinscricao' => (int) $situacao['inscricao'],
			// 		'descricao' => codificaDado(sprintf($historicoDescricao, $tipoSituacao)),
			// 		'quem' => codificaDado($_SESSION[PREFIX . "loginNome"]),
			// 		'data' => date('Y-m-d H:i:s', $time),
			// 		'timestamp' => $time
			// 	)
			// );

			// // adiciona nos logs
			// $conexao->query()
			// 	->from("logs")
			// 	->save(array(
			// 		'acao' => 'excluiu',
			// 		'oque' => 'situação',
			// 		'qual' => codificaDado($tipoSituacao),
			// 		'quem' => codificaDado($_SESSION[PREFIX . "loginNome"]),
			// 		'descricao' => codificaDado(sprintf($logDescricao, 'excluiu', 
			// 			$tipoSituacao, $dados['nome'], $dados['curso'])),
			// 		'data' => date('Y-m-d H:i:s', $time),
			// 		'timestamp' => $time
			// 	)
			// );

			$conexao->commit();
			echo json_encode(array(
					'type' => 'danger',
					'message' => 'Excluído com sucesso'
				)
			);
		}
		catch (Exception $e) {
			$conexao->rollback();
			echo json_encode(array(
					'type' => 'danger',
					'message' => $e->getMessage()
				)
			);
		}
	}

	public function historicoAction() {
		try {
			$conexao = $this->conexao->getConexao();

			$breadcrumbs = array();
			$quantidade = 0;
			$quantidadePorPagina = isset($_GET["exibir"]) ? (int) $_GET["exibir"] : QUANTIDADE_POR_PAGINA;
			$quantidadePorPagina = ($quantidadePorPagina <= 0) ? QUANTIDADE_POR_PAGINA : $quantidadePorPagina;
			$pagina = isset($_GET["p"]) ? $_GET["p"] : 1;
			$pagina = $pagina <= 0 ? 1 : $pagina;
			$limit = $pagina == 1 ? $quantidadePorPagina : $quantidadePorPagina * ($pagina - 1);
			$offset = $pagina == 1 ? 0 : $quantidadePorPagina;

			$situacao = new Situacao($conexao);
			$curso = new Curso($conexao);
			$inscricaoObj = new Preinscricao($conexao);

			$objetos = $situacao->findAll(getVariavel('inscricao'));
			$curso = $curso->findOrFail(getVariavel('curso'));
			$inscricao = $inscricaoObj->findOrFail(getVariavel('inscricao'));
			$quantidade = count($objetos);

			$breadcrumbs[] = array(
				'Inscrições' => '?modulo=preinscricoes',
				$curso['nome'] => '?modulo=preinscricoes&acao=visualizar&curso=' . $curso['id'],
				$inscricao['nome'] => '?modulo=preinscricoes&acao=cadastrar&id=' . $inscricao['id'] . '&tab1=' . $inscricao['status'] . '&curso=' . $curso['id'],
				'Histórico de situações' => ''
			);

		}
		catch (Exception $e) {
			setMensagem('error', $e->getMessage());
		}

		$view = new View2($_GET["modulo"], "extendido", "historico.phtml");
		$view->setParams(array(
				"breadcrumbs" => $breadcrumbs,
				"title" => getTitulo($breadcrumbs),
				"objetos" => $objetos,
				"quantidade" => $quantidade,
				"quantidadePorPagina" => $quantidadePorPagina,
				"pagina" => $pagina
			)
		);
        $view->showContents();
	}

	public function excluirAction() {
		try {
			$conexao = $this->conexao->getConexao();
			$situacao = new Situacao($conexao);
			$objeto = $situacao->findOrFail(getVariavel('id'));
			$affectedRows = $situacao->delete($objeto);

			if ($affectedRows > 0) {
				$descricao = "Usuário excluiu situacão '%s' da inscrição de %s do curso %s.";
				$log = new Log($conexao);
				$log->adicionar ("excluiu", "situação", 
					$_SESSION[PREFIX . "loginNome"], Situacao::getTipo($objeto['tipo']), 
					sprintf($descricao, Situacao::getTipo($objeto['tipo']), $objeto['nome'], 
					$objeto["nomeCurso"]));
				$conexao->commit();
				setMensagem("info", "Situação excluída [" . Situacao::getTipo($objeto["tipo"]) . "]");
			}

		}
		catch (Exception $e) {
			$conexao->rollback();
			setMensagem('error', $e->getMessage());
		}

		$redirecionar = montaRedirect($_SERVER["QUERY_STRING"], array("id", "acao"), array(
				"acao" => "historico"
			)
		);

		Application::redirect($redirecionar);
		exit;
	}

}


?>