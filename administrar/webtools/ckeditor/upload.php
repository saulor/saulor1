<?php

	require ('../../config.php');
	require_once ('../../core/ClassesLoader.php');
	ClassesLoader::Register();

	$diretorio = DIR_UPLOADS . DS . 'editor';
	
	try {

		if ($_FILES['upload']["error"] == 4) {
			throw new Exception('Nenhuma imagem enviada');
		}

		if(!move_uploaded_file($_FILES['upload']['tmp_name'] , $diretorio . DS . $_FILES['upload']['name'])) {
          throw new Exception ('Imagem não enviada');
      	}

		$result = array(
			'uploaded' => 1,
		    'fileName' => $_FILES['upload']['name'],
		    'url' => Url::resourcePath() . 'uploads' . DS . 'editor' . DS . $_FILES['upload']['name']
		);

	}
	catch (Exception $e) {
		$result = array(
			'uploaded' => 0,
			'error' => array(
		        'message' => $e->getMessage()
		    )
		);
	}
	
	echo json_encode($result);

?>