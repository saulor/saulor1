<?php

	/** Get the Router instance. */
	$router = Router::getInstance();

	// site routes
	Router::any('', 'SiteController@index');
	Router::any('institucional', 'SiteController@institucional');
	Router::any('institucional/ies', 'SiteController@ies');
	Router::any('faq', 'SiteController@faq');
	Router::any('institucional/unidades', 'SiteController@unidades');
	Router::any('institucional/faq', 'SiteController@faq');
	Router::any('institucional/parceiros', 'SiteController@parceiros');
	Router::any('institucional/trabalhe-conosco', 'SiteController@trabalhe');
	Router::any('institucional/trabalhe-conosco/docentes', 'SiteController@docentes');
	Router::any('institucional/trabalhe-conosco/colaboradores', 'SiteController@colaboradores');
	Router::any('institucional/trabalhe-conosco/representantes', 'SiteController@representantes');
	Router::any('institucional/trabalhe-conosco/parcerias', 'SiteController@parcerias');
	Router::any('contato', 'SiteController@contato');
	Router::any('noticias', 'SiteController@noticias');
	Router::any('noticia/(:any)', 'SiteController@noticia');
	Router::any('busca', 'SiteController@busca');
	Router::any('ajax/(:any)', 'SiteController@ajax');
	
	Router::any('cursos', 'SiteController@cursos');
	Router::any('curso/(:any)', 'SiteController@curso');
	Router::any('informativo/(:any)', 'SiteController@informativo');
	Router::any('matricula/(:any)', 'SiteController@matricula');
	Router::any('inscricao/(:any)', 'SiteController@inscricao');
	Router::any('aperfeicoamento-profissional', 'SiteController@aperfeicoamento');
	Router::any('posgraduacao', 'SiteController@posgraduacao');
	Router::any('posgraduacao/(:any)', 'SiteController@posgraduacao');
	Router::any('posgraduacao/saude/(:any)', 'SiteController@saudeP');
	Router::any('aperfeicoamento-profissional/(:any)', 'SiteController@aperfeicoamento');
	Router::any('aperfeicoamento-profissional/(:any)/(:any)', 'SiteController@aperfeicoamentoS');

	Router::any('area-aluno', 'SiteController@area');
	Router::any('area-aluno/login', 'SiteController@login');
	Router::any('area-aluno/sair', 'SiteController@sair');
	Router::any('area-aluno/recuperar', 'SiteController@recuperar');
	Router::any('area-aluno/requerimentos', 'SiteController@requerimentos');
	Router::any('area-aluno/requerimentos/acompanhamento', 'SiteController@acompanhamentoLogin');
	Router::any('area-aluno/requerimentos/acompanhamento/(:any)', 'SiteController@acompanhamento');
	Router::any('download/requerimentos/(:any)', 'SiteController@download');
	Router::any('area-aluno/cadastrar', 'SiteController@cadastrar');
	Router::any('area-aluno/frequencia-notas', 'SiteController@consulta');

	// download
	Router::any('downloads/(:any)/(:any)', 'SiteController@downloads');

	// ajax
	Router::any('json/(:any)', 'SiteController@json');
	Router::any('cep/(:any)', 'SiteController@cep');
	$router->dispatch();

?>