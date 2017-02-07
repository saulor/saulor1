<?php
	
	require_once '../config.php';
	//require_once '../funcoes.php';
	require_once ('../core/ClassesLoader.php');
	ClassesLoader::Register();

	try {
		$conexao = new Conexao();
		$model = new vwInscricao($conexao->getConexao());
		$inscricao = $model->getObjetoOrFail(105);
		$htmls = EmailSite::inscricao($inscricao, false);

		echo $htmls['notificacao'];
	}
	catch (Exception $e) {
		echo $e->getMessage();
	}

	$conexao->getConexao()->disconnect();

?>