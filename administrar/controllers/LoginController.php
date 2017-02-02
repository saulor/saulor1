<?php

class LoginController extends Controller {

	public function LoginController() {
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
	
		try {
			if(count($_POST) > 0) {
			
				$conexao = $this->conexao->getConexao();
				
				if (empty($_POST['login']) && empty($_POST['senha'])) {
					throw new Exception('Login inválido!');
				}

				$usuarioModel = new Usuario($conexao);
				$permissaoModel = new Permissao($conexao);
				
				$usuario = $usuarioModel->getOrFail(array(
						'where' => array(
							'status' => 1,
							'login' => $_POST['login'],
							'senha' => md5($_POST['senha']),
						)
					)
				);

				$permissao = $permissaoModel->getObjeto($usuario->permissao);
				$unidades = $usuario->getUnidades();
				$cursos = $usuario->getCursos();
				$permissoes = $permissao->getAcoes();
				
				$_SESSION[PREFIX . 'loginId'] = $usuario->id;
				$_SESSION[PREFIX . 'loginNome'] = $usuario->nome;
				$_SESSION[PREFIX . 'loginPermissao'] = $usuario->permissao;
				$_SESSION[PREFIX . 'loginCodigo'] = $permissao->codigo;
				$_SESSION[PREFIX . 'filtrar'] = $usuario->filtrar;
				$_SESSION[PREFIX . 'unidades'] = $unidades;
				$_SESSION[PREFIX . 'cursos'] = $cursos;
				$_SESSION[PREFIX . 'permissoes'] = $permissoes['formato2'];

				$this->log->adicionar ('realizou', 'login', 'Painel de administração', 
					'Usuário realizou login no Painel de Administração.');
				
				$conexao->commit();	
				$conexao->disconnect();	
				Application::redirect('index.php');
				exit;
				
			}
		}
		catch (ModelNotFoundException $e) {
			$conexao->disconnect();
			setMensagem('error', 'Login inválido');
			Application::redirect($_SERVER['HTTP_REFERER']);
			exit;
		}
		catch (Exception $e) {
			$conexao->disconnect();	
			setMensagem('error', $e->getMessage());
		}
		
		$view = new View2($_GET['modulo'], 'extendido', 'index.phtml');
		$view->setParams(array(
				'title' => 'Login'
			)
		);
        $view->showContents();
	}
    
}
?>