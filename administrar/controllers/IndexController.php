<?php

class IndexController extends Controller {

	public function IndexController() {
		parent::__construct();
	}

    public function indexAction() {
    	
    	if ($_SESSION[PREFIX . "loginCodigo"] == 33) {
    		Application::redirect("index.php?modulo=index&acao=configuracoes");
    		exit;
    	}
    	
    	$breadcrumbs = array();
		$view = new View2('index', "extendido", "index.phtml");
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs),
			)
		);
		$view->showContents();
    }
    
    public function configuracoesAction() {
    	
    	try {
        	$conexao = $this->conexao->getConexao();
        	
        	$breadcrumbs = array();
        	
        	$chaves = Configuracoes::getChaves();
        	
        	$notificacoes = array();
        	
        	foreach ($chaves as $chave) {
        		
                $notificacao = $this->dao->find($conexao, "configuracoes", array(
        				"where" => array(
        					"chave" => $chave
        				)
        			)
        		);
        		
        		if (!$notificacao) {
        			$notificacao = array(
        				"id" => 0,
        				"chave" => $chave,
        				"valor" => NULL
        			);
        		}
        		
        		$notificacoes[$chave] = $notificacao;
        	}
        	
        	if (count($_POST)) {
        		
        		$redirecionar = NULL;
        		$notificacoes = $dados = $_POST["configuracoes"];
        		
        		$invalidos = $emailsArr = array();
        		
        		foreach ($dados as $key => $configuracao) {
        			$emails = explode(",", $configuracao["valor"]);
        			foreach ($emails as $email) {
                        if (preg_match('/[01]/', $email)) {
                            continue;
                        }
        				if (!validaEmail(trim($email))) {
        					$invalidos[] = $email;
        				}
        			}
        		}
        		
        		if (count($invalidos) > 0) {
        			throw new Exception('O(s) seguinte(s) e-mail(s) é(são) inválido(s): ' . implode(", ", $invalidos));
        		}
        		
        		foreach ($dados as $key => $configuracao) {
        			$emailsArr = array();
        			$emails = explode(",", $configuracao["valor"]);
        			foreach ($emails as $email) {
        				$emailsArr[] = trim($email);
        			}
        			$configuracao["valor"] = implode(",", $emailsArr);
        			$this->dao->salva($conexao, "configuracoes", array(
        					"id" => $configuracao["id"],
        					"chave" => $configuracao["chave"],
        					"valor" => $configuracao["valor"]
        				)
        			);
        		}
        		
        		$conexao->commit();
        		$conexao->disconnect();
        		setMensagem("info", "Configurações atualizadas");
        		Application::redirect("index.php");
        		exit;
        	}
        }
        catch (Exception $e) {
        	$conexao->rollback();
        	setMensagem("error", $e->getMessage());
        	if (!empty($redirecionar)) {
        		Application::redirect("index.php");
        		exit;
        	}
        }
    	
    	$view = new View2('index', "extendido", "configuracoes.phtml");
    	$view->setParams(array(
    			"title" => getTitulo($breadcrumbs),
    			"configuracoes" => $notificacoes
    		)
    	);
    	$view->showContents();
    }
    
    public function alterarAction() {
    	
    	try {

    		$conexao = $this->conexao->getConexao();
    		$breadcrumbs = array();
    	
            $usuarioModel = new Usuario($conexao);

            $usuario = $usuarioModel->getObjetoOrFail(getVariavel('id'));
    		
    		// usuário só poderá alterar os dados dele mesmo
    		if ($usuario->id != $_SESSION[PREFIX . "loginId"]) {
    			throw new Exception("Você não tem permissão para atualizar dados de outros usuários");
    		}
    		
    		$breadcrumbs[] = array(
    			"Alterar dados" => ""
    		);
    		// armazena a senha atual em uma variável
    		$senhaAtual = $usuario->senha;
    		$novaSenha = $usuario->senha;				
    		
    		// se submeteu dados
    		if (count($_POST) > 0) {
    			
    			$dados = $_POST;
                $usuario->setDados($dados);
    			
    			// todos os dados são obrigatórios
    			$obrigatorios = array(
    				"nome" => array(
    					"nome" => "Nome"
    				),
    				"login" => array(
    					"nome" => "Login"
    				),
    				"email" => array(
    					"nome" => "E-mail"
    				)	
    			);
    			
    			// se o usuário informar a nova senha, deverá informar a senha atual
    			if (!empty($dados["novaSenha"])) {
    				$obrigatorios["senhaAtual"] = array(
						"nome" => "Senha atual"
					);
    			}
    			 
    			Funcoes::validaPost($obrigatorios, $dados);
    			
    			// recebe e codifica a senha atual
    			$dados["senhaAtual"] = !empty($dados["senhaAtual"]) ? md5(trim($dados["senhaAtual"])) : $senhaAtual;
    			if ($dados["senhaAtual"] != $senhaAtual) {
    				throw new Exception("Senha atual não confere");
    			}
    			$usuario->senha = $dados["novaSenha"] = !empty($dados["novaSenha"]) ? md5(trim($dados["novaSenha"])) : $novaSenha;
                $usuario->salvar();
    				
    			// adiciona nos logs
    			$this->log->adicionar ("alterou", "dados", $usuario->nome, 
                    "Usuário atualizou seus dados.");
    			
                $_SESSION[PREFIX . 'loginNome'] = $usuario->nome;
    			$conexao->commit();
    			$conexao->disconnect();		
    			setMensagem("info", "Dados atualizados");
                Application::redirect('?modulo=index&acao=alterar&id=' . $usuario->id);
                exit;
    		}
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
    	$view = new View2($_GET["modulo"], "extendido", "alterar.phtml");
    	$view->setParams(array(
    			"title" => getTitulo($breadcrumbs), 
    			"breadcrumbs" => $breadcrumbs,
    			"usuario" => $usuario
    		)
    	);
        $view->showContents();
    }
    
    public function novaAction() {

		try {
			
			$conexao = $this->conexao->getConexao();
			$breadcrumbs[] = array(
				"Nova Pré-inscrição" => ""
			);
			
			$cursoModel = new Curso($conexao);

            $cursos = $cursoModel->getQuery()
                ->order('nome', 'asc')
                ->all();
			
			if (count($_POST) > 0) {
				$obrigatorios = array(
					"curso" => array(
						"tipo" => "select", 
						"nome" => "Curso"
					)	
				);				
				$mensagem = validaPost($obrigatorios, $_POST);
				if (!empty($mensagem)) {
					throw new Exception($mensagem);
				}
				$conexao->disconnect();
				Application::redirect("?modulo=index&acao=preinscricao&curso=" . $_POST["curso"]);
				exit;
			}			

		}
		catch (Exception $e) {
			setMensagem("error", $e->getMessage());
		}
		
		$conexao->disconnect();					
		$view = new View2('preinscricoes', "extendido", 'nova.phtml');
		$view->setParams(array(
				"title" => getTitulo($breadcrumbs), 
				"cursos" => $cursos,
				"breadcrumbs" => $breadcrumbs
			)
		);
		$view->showContents();
    }
    
    public function preinscricaoAction() {
    	try {
    		$conexao = $this->conexao->getConexao();

            $cursoModel = new Curso($conexao);
            $inscricaoModel = new Preinscricao($conexao);
            $usuarioModel = new Usuario($conexao);
            $historicoModel = new HistoricoPreinscricao($conexao);

            $curso = $cursoModel->findOrFail(getVariavel('curso'));
            $cursos = $cursoModel->getQuery()
                ->order('nome', 'asc')
                ->all();
            $usuarios = $usuarioModel->getQuery()
                ->order('nome', 'asc')
                ->all();

            $dados = inicializaDados($inscricaoModel);
    		$dados["quem"] = $_SESSION[PREFIX . "loginId"];
    		$dados["status"] = Preinscricao::PREINSCRICAO_STATUS_INTERESSADO;

    		$breadcrumbs[] = array(
    			"Nova Pré-inscrição" => "?modulo=index&acao=nova",
    			$curso["nome"] => "",
    		);
    		
    		if (count($_POST) > 0) {
    		
    			$redirecionar = NULL;
    			$dadosIn = $dados = $_POST;
    			$dadosIn["formaPagamento"] = isset($dadosIn["formaPagamento"]) ? $dadosIn["formaPagamento"] : 0;
    			$dadosIn["unidade"] = isset($dadosIn["unidade"]) ? $dadosIn["unidade"] : 0; 
                $dadosIn["whatsapp"] = isset($dadosIn["whatsapp"]) ? $dadosIn["whatsapp"] : 0; 
    			$validacao = $this->_valida($dadosIn);
    			$dados = $validacao["dados"];
    			
    			if (!empty($validacao["mensagem"])) {
    				throw new Exception ($validacao["mensagem"]);
    			}
    			
    			$dadosIn = $this->dao->salva($conexao, "preinscricoes", $dados);
    			// adciciona nos logs
    			$this->log->adicionar ("realizou", "pré-inscrição", $dadosIn["nome"], 
                    sprintf("A inscrição foi realizada por %s através do Painel de Administração, no curso %s", $_SESSION[PREFIX . "loginNome"], $curso["nome"]));
                // adiciona no histórico
                $historicoModel->adicionar($dadosIn["id"], 'Realizou inscrição do aluno através do Painel de Administração.');
				
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

                $unidade = NULL;
				// recupera a unidade escolhida
                if ($dadosIn["cidadeCurso"] != 0) {
                    foreach ($curso['unidades'] as $key => $u) {
                        if ($u['idCidade'] == $dadosIn['cidadeCurso']) {
                            $unidade = $curso['unidades'][$key];
                            break;
                        }
                    }
                } 

				// // envia e-mail ao IEFAP e ao aluno
    //             $email = NULL;
    //             switch ($curso["tipo"]) {
    //                 case Curso::CURSO_TIPO_POS :
    //                     $email = getEmailMatricula(array(
    //                             "curso" => $curso, 
    //                             "preinscricao" => $dadosIn, 
    //                             "unidade" => $unidade
    //                         )
    //                     );
    //                 break;
    //                 case Curso::CURSO_TIPO_POS :
    //                     $email = getEmailInscricao(array(
    //                             "curso" => $curso, 
    //                             "preinscricao" => $dadosIn, 
    //                             "unidade" => $unidade
    //                         )
    //                     );
    //                 break;
    //             }
				
    //             if (!empty($email)) {
    //                 $this->_enviaEmails($conexao, $email, $dadosIn, $curso, $unidade, false);
    //             }

				$conexao->commit();
				setMensagem("info", "Inscrição realizada com sucesso");
				Application::redirect("?modulo=index&acao=preinscricao&curso=" . $curso["id"]);
				exit;
    			
    		}
    	}
    	catch (Exception $e) {
    		$conexao->rollback();
    		setMensagem("error", $e->getMessage());
    	}
    	
    	$conexao->disconnect();					
    	$view = new View2("preinscricoes", "extendido", 'cadastrar.phtml');
    	$view->setParams(array(
    			"title" => getTitulo($breadcrumbs),
    			"curso" => $curso,
    			"usuarios" => $usuarios,
    			"preinscricao" => $dados,
    			"breadcrumbs" => $breadcrumbs
    		)
    	);
    	$view->showContents();
    }
    
    private function _valida($dados) {
    	try {

            $obrigatorios = array(
                "nome" => array(
                    "tipo" => "input",
                    "nome" => "Nome"
                ),
                "unidade" => array(
                    "tipo" => "input",
                    "nome" => "Unidade"
                )
            ); 

    		$mensagens = array();
    		$mensagem = validaPost($obrigatorios, $dados);
    		
    		if (!empty($mensagem)) {
    			$mensagens[] = $mensagem;
    		}
    		
    		if (empty($dados["telefoneResidencial"]) && empty($dados["telefoneCelular"]) && empty($dados["telefone"])) {
    			$mensagens[] = "É necessário informar pelo menos um telefone para contato.";
    		}

    		if (count($mensagens) > 0) {
    			return array(
    				"dados" => $dados,
    				"mensagem" => implode("<br />", $mensagens)
    			);
    		}
    		
    		$dados["timestamp"] = time();
    		$dados["data"] = date('d/m/Y H:i:s', $dados["timestamp"]);
    		
    		return array(
    			"dados" => $dados,
    		);
    	}
    	catch (Exception $e) {
    		throw $e;
    	}
    }
    
    private function _enviaEmails($conexao, $email, $dados, $curso, $unidade, $enviouComprovante) {
    	try {

            switch ($curso["tipo"]) {

                case Curso::CURSO_TIPO_POS :
                    $assunto = "Matrícula efetuada com sucesso";
                    $tipoInscricao = 2;
                    $q = "matrícula";
                break;
                
                case Curso::CURSO_TIPO_APERFEICOAMENTO :
                    $assunto = "Inscrição efetuada com sucesso";
                    $tipoInscricao = 1;
                    $q = "inscrição";
                break;
            }

    		// $time = time();
    		// if(enviarEmail($dados["email"], $assunto, $email, true)) {
    		// 	$this->dao->salva($conexao, "historico", array(
    		// 			"id" => 0,
    		// 			"preinscricao" => $dados["id"],
    		// 			"descricao" => "Sistema enviou e-mail ao aluno(a) informando que sua " . $q . " realizada por " . $_SESSION[PREFIX . "loginNome"] . " foi processada com sucesso.",
    		// 			"quem" => "Sistema",
    		// 			"timestamp" => $time,
    		// 			"data" => date('d/m/Y H:i:s', $time)
    		// 		)
    		// 	);
    		// 	$this->logDAO->adicionar ($conexao, "enviou", "e-mail", 
      //               "Sistema", $dados["nome"] . " [" . $curso["nome"] . "]", "Sistema enviou e-mail ao aluno(a) informando que sua " . $q . " realizada por " . $_SESSION[PREFIX . "loginNome"] . " foi processada com sucesso.");
    		// }
    		// else {
    		// 	$this->dao->salva($conexao, "historico", array(
    		// 			"id" => 0,
    		// 			"preinscricao" => $dados["id"],
    		// 			"descricao" => "Sistema não foi capaz de enviar e-mail ao aluno(a) informando que sua " . $q . " realizada por " . $_SESSION[PREFIX . "loginNome"] . " foi processada com sucesso",
    		// 			"quem" => "Sistema",
    		// 			"timestamp" => $time,
    		// 			"data" => date('d/m/Y H:i:s', $time)
    		// 		)
    		// 	);
    		// 	$this->logDAO->adicionar ($conexao, "não enviou", "e-mail", "Sistema", $dados["nome"] . " [" . $curso["nome"] . "]", "Sistema não foi capaz de enviar e-mail ao aluno(a) informando que sua " . $q . " realizada por " . $_SESSION[PREFIX . "loginNome"] . " foi processada com sucesso.");
    		// }
    		
    		$to = $this->_getEmail($conexao, $this->dao, Configuracoes::EMAIL_PREINSCRICOES);
    		
    		enviarNotificacaoInscricao($to["to"], array(
				"preinscricao" => $dados,
				"curso" => $curso,
				"unidade" => $unidade,
				"timestamp" => $dados["timestamp"]
			), $tipoInscricao, true);
    		
    	}
    	catch (Exception $e) {
    		throw $e;
    	}
    }
    
    private function _getEmail($conexao, $dao, $chave) {
    	$to = $this->dao->find($conexao, "configuracoes", array(
    			"where" => array(
    				"chave" => $chave
    			)
    		)
    	);
    	if (count($to) == 0) {
    		return "contato@iefap.com.br";
    	}
    	else {
    		$emailsArr = array();
    		$emails = explode(",", $to["valor"]);
    		if (count($emails) > 1) {
    			$ultimoEmail = array_pop($emails);
    		}
    		$emailsIn = implode(", ", $emails);
    		if (isset($ultimoEmail)) {
    			$emailsIn .= " e " . $ultimoEmail;
    		}
    		return array(
    			"to" => $to["valor"],
    			"emails" => $emailsIn
    		);
    	}
    }
    
    private function _enviaComprovante ($conexao, $dados, $comprovante) {
    	try {
    		
    		$tiposPermitidos = "application/msword|application/pdf|application/vnd.openxmlformats-officedocument.wordprocessingml.document";
    			
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
	
}
?>