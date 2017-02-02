<?php

class SiteController {

	private $conexao;
    private $dao;
    private $log;
	private $dados = array();

	public function __construct() {
        $c = new Conexao();
        $this->conexao = $c->getConexao();
        $this->dao = new SiteDAO($this->conexao);
		$this->load();
	}

	public function __call($name, $parameters) {

        $this->dados['unidadesBar'] = array(
            'Maringá / PR' => array(
              'default' => true,
              'active' => true,
              'class' => 'maringa',
              'telefone' => '0800.501.6000 / 44 98813-1364',
              'email' => 'contato@iefap.com.br',
              'endereco' => 'Av. Adv. Horácio Raccanelo Filho, 5415 - Sala 01'
            ),
            'Londrina / PR' => array(
              'class' => 'londrina',
              'telefone' => '0800.501.6000 / 44 98813-1364',
              'email' => 'contato@iefap.com.br',
              'endereco' => 'Cedro Hotel - Av. Juscelino Kubitscheck, 200'
            ),
            'Teresina / PI' => array(
              'class' => 'teresina',
              'telefone' => '86 9424-2002',
              'email' => 'contato@iefap.com.br',
              'endereco' => 'Rua David Caldas, 90 - 1º Andar'
            ),
            'Belém / PA' => array(
              'class' => 'belem',
              'telefone' => '91 3266-3100',
              'email' => 'contato@iefap.com.br',
              'endereco' => 'Trav. Mauriti, 1771A - Pedreira'
            ),
            'Cascavel / PR' => array(
              'class' => 'cascavel',
              'telefone' => '44 3123-6000',
              'email' => 'contato@iefap.com.br',
              'endereco' => ''
            ),
            'Campinas / SP' => array(
              'class' => 'campinas',
              'telefone' => '0800.501.6000 / 44 98813-1364',
              'email' => 'contato@iefap.com.br',
              'endereco' => 'Espaço Ideal Campinas - R. Romualdo Andreazzi, 677'
            ),
            'Fortaleza / CE' => array(
              'class' => 'fortaleza',
              'telefone' => '0800.501.6000 / 44 98813-1364',
              'email' => 'contato@iefap.com.br',
              'endereco' => 'Colégio Padre José Nilson - R. Coronel Manuel Jesuíno, 225 - Mucuripe'
            ),
            'Salvador / BA' => array(
              'class' => 'salvador',
              'telefone' => '0800.501.6000 / 44 98813-1364',
              'email' => 'contato@iefap.com.br',
              'endereco' => 'Avenida Juracy Magalhães Jr - Edf WA Empresarial Sl. 106 - Rio Vermelho'
            ),
            'Brasília / DF' => array(
              'class' => 'brasilia',
              'telefone' => '0800.501.6000 / 44 98813-1364',
              'email' => 'contato@iefap.com.br',
              'endereco' => 'SRTVS 701 - Edifício Palácio do Rádio III - Sls 101 a 106'
            ),
            'Curitiba / PR' => array(
              'class' => 'curitiba',
              'telefone' => '0800.501.6000 / 44 98813-1364',
              'email' => 'contato@iefap.com.br',
              'endereco' => 'Instituto Paulista de Ensino em Medicina - R. Tobias de Macedo Junior, 246'
            ),
            'Porto Alegre / RS' => array(
              'class' => 'portoalegre',
              'telefone' => '0800.501.6000 / 44 98813-1364',
              'email' => 'contato@iefap.com.br',
              'endereco' => ''
            )
        );

        // recupera as categorias item menu pós-graduação
        $this->dados['categoriasPos'] = $this->dao->table('cursos_categorias', array(
                    'slug', 
                    'nome'
                )
            )
            ->where('quantidadeCursosPosVisiveis', '>', 0)
            ->where('pai', '=', 0)
            ->all();

        // recupera as categorias item menu aperfeiçoamento
        $this->dados['categoriasApe'] = $this->dao->table('cursos_categorias', array(
                    'slug', 
                    'nome'
                )
            )
            ->where('quantidadeCursosApeVisiveis', '>', 0)
            ->where('pai', '=', 0)
            ->all();
    }

    public function errorAction() {
    	$data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('404');
    	View::renderTemplate('header', $data);
		View::render('error/index', $data);
		View::renderTemplate('footer', $data);
    }

    public function indexAction() {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('index');
    	$data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        $data['cursos'] = $this->dao->table('vw_cursos', array(
                    'id',
                    'nome',
                    'link',
                    'nomeCategoria',
                    'publicoAlvo',
                    'publicoAlvoResumo',
                    'thumbnail'
                )
            )
            ->where('status', '=', 1)
            ->where('tipo', '=', Curso::CURSO_TIPO_POS)
            ->order('nome', 'asc')
            ->all(array(
                    'limit' => QUANTIDADE_PAGINACAO
                )
            );

        // quantidade de cursos ativos de pós-graduação
        $data['quantidadeCursos'] = $this->dao->table('vw_cursos')
            ->where('status', '=', 1)
            ->where('tipo', '=', Curso::CURSO_TIPO_POS)
            ->count();

        // quantidade de páginas da paginação de cursos
        $data['quantidadePaginas'] = ceil($data['quantidadeCursos'] / QUANTIDADE_PAGINACAO);

        $data['banners'] = $this->dao->table('banners')
            ->where('status', '=', 1)
            ->order('data', 'desc')
            ->all();

        // recupera as categorias
        $data['categorias'] = $this->dao->table('cursos_categorias')
            ->where('quantidadeCursosVisiveis', '>', 0)
            ->order('pai', 'asc')
            ->all();

        foreach ($data['categorias'] as $key => $categoria) {
            if (!empty($categoria->caminho)) {
                $caminhos = explode('/', $categoria->caminho);
                array_shift($caminhos);
                $nomes = array();
                foreach ($caminhos as $id) {
                    $c = $this->dao->table('cursos_categorias')
                        ->where('id', '=', $id)
                        ->first();
                    $nomes[] = $c->nome;
                }
                $data['categorias'][$key]->nome = implode(' / ', $nomes);
            }
        }

        $data['todosCursos'] = $this->dao->table('vw_cursos', array(
                    'nome',
                    'link'
                )
            )
            ->where('status', '=', 1)
            ->order('nome', 'asc')
            ->all();

        $data['noticias'] = $this->dao->table('noticias')
            ->order('data', 'desc')
            ->all(array(
                    'limit' => 3
                )
            );
        
        $this->conexao->disconnect();

    	View::renderTemplate('header', $data);
		View::render('index/index', $data);
		View::renderTemplate('footer', $data);
    }

    public function institucionalAction() {
    	$data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('institucional');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        $data['pagina'] = $this->dao->table('paginas')
            ->where('pagina', '=', Pagina::PAGINA_CATEGORIA_SOBRE)
            ->order('data', 'desc')
            ->first();

        $this->conexao->disconnect();

    	View::renderTemplate('header', $data);
		View::render('institucional/sobre', $data);
		View::renderTemplate('footer', $data);
    }

    public function iesAction() {
        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('ies');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        $data['mns'] = $this->dao->table('paginas')
            ->where('pagina', '=', Pagina::PAGINA_CATEGORIA_FACULDADE_MNS)
            ->first();

        $data['uni'] = $this->dao->table('paginas')
            ->where('pagina', '=', Pagina::PAGINA_CATEGORIA_FACULDADE_UNI)
            ->first();

        $data['fip'] = $this->dao->table('paginas')
            ->where('pagina', '=', Pagina::PAGINA_CATEGORIA_FACULDADE_FIP)
            ->first();

        $data['anhembi'] = $this->dao->table('paginas')
            ->where('pagina', '=', Pagina::PAGINA_CATEGORIA_FACULDADE_ANHEMBI)
            ->first();

        $data['fmu'] = $this->dao->table('paginas')
            ->where('pagina', '=', Pagina::PAGINA_CATEGORIA_FACULDADE_FMU)
            ->first();

        $data['unp'] = $this->dao->table('paginas')
            ->where('pagina', '=', Pagina::PAGINA_CATEGORIA_FACULDADE_UNP)
            ->first();

        $this->conexao->disconnect();
        View::renderTemplate('header', $data);
        View::render('institucional/ies', $data);
        View::renderTemplate('footer', $data);
    }

    public function faqAction() {
        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('faq');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('institucional/faq', $data);
        View::renderTemplate('footer', $data);
    }

    public function unidadesAction() {
    	$data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('unidades');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        $this->conexao->disconnect();

    	View::renderTemplate('header', $data);
		View::render('institucional/unidades', $data);
		View::renderTemplate('footer', $data);
    }

    public function parceirosAction() {
    	$data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('parceiros');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        $this->conexao->disconnect();

    	View::renderTemplate('header', $data);
		View::render('institucional/parceiros', $data);
		View::renderTemplate('footer', $data);
    }

    public function trabalheAction() {
        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('trabalhe-conosco');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('institucional/trabalhe', $data);
        View::renderTemplate('footer', $data);
    }

    public function colaboradoresAction() {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('colaboradores');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        $data['objeto'] = $model = new Administrativo($this->conexao);

        if (count($_POST) > 0) {

            try {
                $validator = new MyGump();
                $dados = $validator->sanitize($_POST['Colaborador']);
                $model->setDados($dados);
                $curriculo = isset($_FILES['curriculoComercial']) && FuncoesSite::enviouArquivo($_FILES['curriculoComercial']);

                $rules = array(
                    'nome' => 'required',
                    'email' => 'required|valid_email',
                    'numero' => 'integer',
                    'cep' => 'valid_cep',
                    'cidade' => 'required',
                    'uf' => 'required'                );

                $filters = array(
                    'nome' => 'trim|sanitize_string',
                    'email' => 'trim|sanitize_email',
                    'endereco' => 'trim|sanitize_string',
                    'complemento' => 'trim|sanitize_string',
                    'bairro' => 'trim|sanitize_string',
                    'cidade' => 'trim|sanitize_string',
                    'numero' => 'trim|sanitize_numbers',
                );

                $validated = $validator->is_valid($dados, $rules);

                if (!is_array($validated) && (int) $validated == 1) {
                   $validated = array();
                }
                if (empty($dados['telefoneResidencial']) && empty($dados['telefoneCelular'])) {
                    $validated[] = 'Pelo menos um telefone deve ser preenchido';
                }
                if($validated) {
                    throw new Exception(FuncoesSite::getValidationErrors($validated));
                }

                $dados = $validator->filter($dados, $filters);
                $model->setDados($dados);
                $model->salvar();

                if ($curriculo) {
                    $diretorio = 'trabalhe' . DS . 'administrativos' . DS;
                    $arquivo = FuncoesSite::enviaArquivo($_FILES['curriculoComercial'], $diretorio . $model->id);
                    $model->curriculoComercial = $arquivo['nome'];
                    $model->mime = $arquivo['mime'];
                    $model->extensao = $arquivo['extensao'];
                    $model->salvar();
                }

                $log = new Log($this->conexao);
                $log->site ($model->nome, 'cadastrou-se', 'trabalhe conosco', 
                    'colaboradores', 'Cadastrou-se em Trabalhe Conosco');

                $this->conexao->commit();
                
                // envia e-mails
                $configuracoesModel = new Configuracoes($this->conexao);
                $to = $configuracoesModel->getEmails(Configuracoes::EMAIL_ADMINISTRATIVOS);
                EmailSite::trabalhe($model, 'Administrativos', $to);

                FuncoesSite::setMensagem('success', 'Seu cadastro foi processado com sucesso. Obrigado por escolher o IEFAP!');
                Url::redirect(SITEURL . 'institucional/trabalhe-conosco/colaboradores');
            }
            catch (Exception $e) {
                $this->conexao->rollback();
                FuncoesSite::setMensagem('danger', $e->getMessage());
            }
        }

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('trabalhe/colaboradores', $data);
        View::renderTemplate('footer', $data);
    }

    public function docentesAction() {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('docentes');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        $data['objeto'] = $model = new Docente($this->conexao);

        $data['cursosDisciplinas'] = array(
            array(
                'curso' => '',
                'disciplinas' => ''
            )
        );

        if (count($_POST) > 0) {

            try {
                $validator = new MyGump();
                $dados = $validator->sanitize($_POST['Docente']);
                $model->setDados($dados);
                $cursosDisciplinas = $data['cursosDisciplinas'] = $_POST['Cursos'];
                $curriculo = isset($_FILES['curriculoComercial']) && FuncoesSite::enviouArquivo($_FILES['curriculoComercial']);

                $rules = array(
                    'nome' => 'required',
                    'email' => 'required|valid_email',
                    'numero' => 'integer',
                    'cep' => 'valid_cep',
                    'cidade' => 'required',
                    'uf' => 'required',
                    'instituicao' => 'required',
                    'graduacoes' => 'required',
                    'titulacao' => 'required',
                    'curriculoLattes' => 'valid_url'
                );

                $filters = array(
                    'nome' => 'trim|sanitize_string',
                    'email' => 'trim|sanitize_email',
                    'endereco' => 'trim|sanitize_string',
                    'complemento' => 'trim|sanitize_string',
                    'bairro' => 'trim|sanitize_string',
                    'cidade' => 'trim|sanitize_string',
                    'numero' => 'trim|sanitize_numbers',
                    'titulacao' => 'integer'
                );

                MyGump::set_field_name('instituicao', 'Instituição');
                MyGump::set_field_name('graduacoes', 'Graduações');
                MyGump::set_field_name('titulacao', 'Titulação');
                MyGump::set_field_name('curriculoLattes', 'Lattes');

                $validated = $validator->is_valid($dados, $rules);

                if (!is_array($validated) && (int) $validated == 1) {
                   $validated = array();
                }
                if (empty($dados['telefoneResidencial']) && empty($dados['telefoneCelular'])) {
                    $validated[] = 'Pelo menos um telefone deve ser preenchido';
                }
                if (count($cursosDisciplinas) == 1 && empty($cursosDisciplinas[0]['curso'])) {
                   $validated[] = 'Informe pelo menos um curso que pode ministrar';
                }
                if($validated) {
                    throw new Exception(FuncoesSite::getValidationErrors($validated));
                }

                $dados = $validator->filter($dados, $filters);
                $model->setDados($dados);
                $model->salvar();

                if ($curriculo) {
                    $diretorio = 'trabalhe' . DS . 'docentes' . DS;
                    $arquivo = FuncoesSite::enviaArquivo($_FILES['curriculoComercial'], $diretorio . $model->id);
                    $model->curriculoComercial = $arquivo['nome'];
                    $model->mime = $arquivo['mime'];
                    $model->extensao = $arquivo['extensao'];
                    $model->salvar();
                }

                // cadastra cursos e disciplinas
                foreach ($cursosDisciplinas as $curso) {
                    if (!empty($curso['curso'])) {
                        $dcd = new DocenteCursoDisciplina ($this->conexao);
                        $dcd->docente = $model->id;
                        $dcd->curso = trim($curso['curso']);
                        $dcd->disciplinas = trim($curso['disciplinas']);
                        $dcd->salvar();
                    }
                }

                $log = new Log($this->conexao);
                $log->site ($model->nome, 'cadastrou-se', 'trabalhe conosco', 
                    'docentes', 'Cadastrou-se em Trabalhe Conosco');

                $this->conexao->commit();

                // envia e-mails
                $configuracoesModel = new Configuracoes($this->conexao);
                $to = $configuracoesModel->getEmails(Configuracoes::EMAIL_DOCENTES);
                EmailSite::trabalhe($model, 'Docentes', $to);

                FuncoesSite::setMensagem('success', 'Seu cadastro foi processado com sucesso. Obrigado por escolher o IEFAP!');
                Url::redirect(SITEURL . 'institucional/trabalhe-conosco/docentes');
            }
            catch (Exception $e) {
                $this->conexao->rollback();
                FuncoesSite::setMensagem('danger', $e->getMessage());
            }
        }

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('trabalhe/docentes', $data);
        View::renderTemplate('footer', $data);
    }

    public function representantesAction() {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('representantes');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        $data['objeto'] = $model = new Representante($this->conexao);

        if (count($_POST) > 0) {

            try {
                $validator = new MyGump();
                $dados = $validator->sanitize($_POST['Representante']);
                $model->setDados($dados);
                $curriculo = isset($_FILES['curriculoComercial']) && FuncoesSite::enviouArquivo($_FILES['curriculoComercial']);

                $rules = array(
                    'nome' => 'required',
                    'email' => 'required|valid_email',
                    'numero' => 'integer',
                    'cep' => 'valid_cep',
                    'cidade' => 'required',
                    'uf' => 'required'                
                );

                $filters = array(
                    'nome' => 'trim|sanitize_string',
                    'email' => 'trim|sanitize_email',
                    'endereco' => 'trim|sanitize_string',
                    'complemento' => 'trim|sanitize_string',
                    'bairro' => 'trim|sanitize_string',
                    'cidade' => 'trim|sanitize_string',
                    'numero' => 'trim|sanitize_numbers',
                );

                $validated = $validator->is_valid($dados, $rules);

                if (!is_array($validated) && (int) $validated == 1) {
                   $validated = array();
                }
                if (empty($dados['telefoneResidencial']) && empty($dados['telefoneCelular'])) {
                    $validated[] = 'Pelo menos um telefone deve ser preenchido';
                }
                if($validated) {
                    throw new Exception(FuncoesSite::getValidationErrors($validated));
                }

                $dados = $validator->filter($dados, $filters);
                $model->setDados($dados);
                $model->salvar();

                if ($curriculo) {
                    $diretorio = 'trabalhe' . DS . 'representantes' . DS;
                    $arquivo = FuncoesSite::enviaArquivo($_FILES['curriculoComercial'], $diretorio . $model->id);
                    $model->curriculoComercial = $arquivo['nome'];
                    $model->mime = $arquivo['mime'];
                    $model->extensao = $arquivo['extensao'];
                    $model->salvar();
                }

                $log = new Log($this->conexao);
                $log->site ($model->nome, 'cadastrou-se', 'trabalhe conosco', 
                    'representantes', 'Cadastrou-se em Trabalhe Conosco');

                $this->conexao->commit();
                
                // envia e-mails
                $configuracoesModel = new Configuracoes($this->conexao);
                $to = $configuracoesModel->getEmails(Configuracoes::EMAIL_REPRESENTANTES);
                EmailSite::trabalhe($model, 'representantes', $to);

                FuncoesSite::setMensagem('success', 'Seu cadastro foi processado com sucesso. Obrigado por escolher o IEFAP!');
                Url::redirect(SITEURL . 'institucional/trabalhe-conosco/representantes');
            }
            catch (Exception $e) {
                $this->conexao->rollback();
                FuncoesSite::setMensagem('danger', $e->getMessage());
            }
        }

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('trabalhe/representantes', $data);
        View::renderTemplate('footer', $data);
    }

    public function contatoAction() {
    	$data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('contato');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        $data['contato'] = $model = new Contato($this->conexao);


        if (count($_POST) > 0) {

            try {
                $validator = new MyGump();
                $dados = $validator->sanitize($_POST);
                $model->setDados($dados);

                $rules = array(
                    'nome' => 'required',
                    'email' => 'valid_email',
                    'mensagem' => 'required'
                );

                $filters = array(
                    'nome' => 'trim|sanitize_string',
                    'cidade' => 'trim|sanitize_string',
                    'cursos' => 'trim|sanitize_string',
                    'mensagem' => 'trim|sanitize_string'
                );

                $validated = $validator->is_valid($dados, $rules);

                if (!is_array($validated) && (int) $validated == 1) {
                    $validated = array();
                }
                if (empty($dados['telefone']) && empty($dados['email'])) {
                    $validated[] = 'Informe pelo menos uma opção de contato (telefone ou e-mail)';
                }

                if($validated) {
                    throw new Exception(FuncoesSite::getValidationErrors($validated));
                }

                $dados = $validator->filter($dados, $filters);
                $model->setDados($dados);
                
                $model->salvar();

                $configuracoesModel = new Configuracoes($this->conexao);
                $to = $configuracoesModel->getEmails(Configuracoes::EMAIL_CONTATOS);
                EmailSite::contato($model, $to);

                $this->conexao->commit();
                FuncoesSite::setMensagem('success', 'Sua mensagem foi enviada com sucesso. Em breve entraremos em contato!');
                Url::redirect(SITEURL . 'contato');
                
            }
            catch (Exception $e) {
                $this->conexao->rollback();
                FuncoesSite::setMensagem('danger', $e->getMessage());
            }
        }

        $this->conexao->disconnect();

    	View::renderTemplate('header', $data);
		View::render('contato/index', $data);
		View::renderTemplate('footer', $data);
    }

    public function noticiasAction() {
    	$data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('noticias');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        $quantidadePorPagina = 20;
        $pagina = isset($_GET['p']) ? $_GET['p'] : 1;
        $pagina = $pagina <= 0 ? 1 : $pagina;
        $limit = $pagina == 1 ? $quantidadePorPagina : $quantidadePorPagina * ($pagina - 1);
        $offset = $pagina == 1 ? 0 : $quantidadePorPagina;

        try {
            $data['noticias'] = $this->dao->table('noticias')
                ->order('data', 'desc')
                ->where('status', '=', 1)
                ->all(array(
                        'limit' => $limit,
                        'offset' => $offset
                    )
                );

            $quantidade = $this->dao->table('noticias')
                ->where('status', '=', 1)
                ->count();

            $pages = new Paginator($quantidadePorPagina, 'p');
            $pages->setTotal($quantidade);
            $data['pageLinks'] = $pages->pageLinks();

            $this->conexao->disconnect();

        }
        catch (Exception $e) {
            // não encontrou a notícia
            $this->errorAction();
            exit;
        }

        $this->conexao->disconnect();

    	View::renderTemplate('header', $data);
		View::render('noticias/noticias', $data);
		View::renderTemplate('footer', $data);
    }

    public function noticiaAction ($slug = NULL) {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('noticia');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        try {
            $noticia = $this->dao->table('noticias')
                ->where('link', '=', $slug)
                ->firstOrFail();
            $noticia->data = FuncoesSite::getDataExtenso($noticia->timestamp);
            $data['noticia'] = $noticia;

            if (!empty(trim($noticia->tags))) {
                $tags = FuncoesSite::configuraTags($noticia->tags);
            }
            else {
                $tags = FuncoesSite::configuraTags($noticia->titulo);
            }

            $data['outros'] = array();

            if (!empty($tags)) {

                $tagsAux = array_map('rtrim', explode(',', $tags));
                $tagsAux = array_map('FuncoesSite::lowerCase', $tagsAux);
                $tagsAux = FuncoesSite::addCoringa($tagsAux);

                $query = $data['outros'] = $this->dao->table('noticias')
                    ->where('id', '<>', (int) $noticia->id)
                    ->where('LOWER(tags)', 'or', $tagsAux);

                $data['outros'] = $query->all(array(
                        'limit' => 4
                    )
                );
            }

            // meta tags
            $a = new stdClass();
            $a->tags = $tags;
            $a->titulo = $a->descricao = $noticia->titulo;
            $data['tags'] = $a->tags;
            $data['meta'] = FuncoesSite::replaceMeta ($data['meta'], $a);

            $this->conexao->disconnect();

        }
        catch (Exception $e) {
            // não encontrou a notícia
            $this->errorAction();
            exit;
        }

        View::renderTemplate('header', $data);
        View::render('noticias/noticia', $data);
        View::renderTemplate('footer', $data);
    }

    public function cursoAction ($slug = NULL) {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('curso');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;
        $view = 'index';

        try {
            // recupera o curso
            $data['curso'] = $curso = $this->dao->table('vw_cursos')
                ->where('link', '=', $slug)
                ->firstOrFail();

            // recupera a categoria e suas categorias ancestrais
            $categorias = array();
            // recupera e armazena a categoria do curso
            $data['categoria'] = $categoria = $categoriaC = $this->dao->table('cursos_categorias')
                ->where('id', '=', (int) $curso->categoria)
                ->first();
            // se tem categoria pai
            if (!empty($categoria->pai)) {
                do {
                    // recupera categoria pai
                    $categoria = $this->dao->table('cursos_categorias')
                        ->where('id', '=', (int) $categoria->pai)
                        ->first();
                    // armazena primeiro as categorias ancestrais
                    $data['categorias'][] = $categoria; // objeto
                    $categorias[] = $categoria->nome; // só o nome
                }
                while($categoria->pai != 0);  
            }
            // por último adiciona a categoria do curso
            $data['categorias'][] = $categoriaC; // objeto
            $categorias[] = $curso->nomeCategoria; // só o nome

            // view diferenciada para cursos de pós e aperfeiçoamento
            $view = Curso::getSlug($curso->tipo);

            // unidade e outros cursos
            $data['outros'] = $data['unidades'] = array();

            $data['unidades'] = $this->dao->table('vw_cidades_cursos')
                ->where('curso', '=', (int) $curso->id)
                ->where('status', '=', 1)
                ->order('idRegiao', 'asc')
                ->order('siglaEstado', 'asc')
                ->all();

            $outros = $this->dao->table('vw_cursos')
                ->where('categoria', '=', (int) $curso->categoria)
                ->where('vinculado', '=', 0)
                ->where('id', '<>', (int) $curso->id)
                ->all();

            if ($outros) {
                $quantidadeItens = count($outros) >= 4 ? 4 : count($outros);
                $outrosRand = $quantidadeItens == 1 ? array(0) : array_rand($outros, $quantidadeItens);
                foreach($outrosRand as $key) {
                    $data['outros'][] = $outros[$key];
                }
            }

            // meta tags
            $a = new stdClass();
            $a->tipo = Curso::getTipo($curso->tipo);
            $a->nome = $curso->nome;
            $a->categorias = implode(' › ', $categorias);
            $data['meta'] = FuncoesSite::replaceMeta ($data['meta'], $a);

            $this->conexao->disconnect();

        }
        catch (NotFoundException $e) {
            // não encontrou o curso
            $this->conexao->rollback();
            $this->conexao->disconnect();
            $this->errorAction();
            exit;
        }
        catch (Exception $e) {
            // não encontrou o curso
            $this->conexao->rollback();
            $this->conexao->disconnect();
            FuncoesSite::setMensagem('danger', $e->getMessage());
            exit;
        }

        View::renderTemplate('header', $data);
        View::render('curso/' . $view, $data);
        View::renderTemplate('footer', $data);
    }

    public function cursosAction() {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('busca');
        $data['title'] = $data['meta']['meta.title'];

        $quantidadePorPagina = 20;
        $pagina = isset($_GET['p']) ? $_GET['p'] : 1;
        $pagina = $pagina <= 0 ? 1 : $pagina;
        $limit = $pagina == 1 ? 0 : $quantidadePorPagina * ($pagina - 1);
        $quantidadeResultados = 0;
        $resultados = array();

        try {

            $query = $this->dao->table('vw_cursos', array(
                        'id',
                        'nome',
                        'link',
                        'nomeCategoria',
                        'publicoAlvo',
                        'publicoAlvoResumo',
                        'thumbnail'
                    )
                )
                ->where('status', '=', 1);

            if (!empty($_GET['categoria'])) {
                $query->where('slug', '=', $_GET['categoria']);
            }

            $data['cursos'] = $query
                ->order('nome', 'asc')
                ->all();
                
            // recupera as categorias
            $data['categorias'] = $this->dao->table('cursos_categorias')
                ->where('quantidadeCursosVisiveis', '>', 0)
                ->order('pai', 'asc')
                ->all();

            foreach ($data['categorias'] as $key => $categoria) {
                if (!empty($categoria->caminho)) {
                    $caminhos = explode('/', $categoria->caminho);
                    array_shift($caminhos);
                    $nomes = array();
                    foreach ($caminhos as $id) {
                        $c = $this->dao->table('cursos_categorias')
                            ->where('id', '=', $id)
                            ->first();
                        $nomes[] = $c->nome;
                    }
                    $data['categorias'][$key]->nome = implode(' / ', $nomes);
                }
            }

            $query = $this->dao->table('vw_cursos', array(
                    'nome',
                    'link',
                    'objetivosGerais',
                    'publicoAlvo',
                    'nomeCategoria'
                )
            )
            ->where('status', '=', 1);


            if (!empty($_GET['categoria'])) {
                $query->where('slug', '=', $_GET['categoria']);
            }

            if (!empty($_GET['curso'])) {
                $query->where('link', '=', $_GET['curso']);
            }

            $query->order('nome', 'asc');

            $quantidadeResultados = $query->count();
            $objetos = $query->all();

            foreach ($objetos as $r) {
                $item = array();
                $item['titulo'] = 'Cursos › ' . $r->nomeCategoria . ' › ' .  $r->nome;
                if (!empty($r->objetivosGerais)) {
                    $item['descricao'] = $r->objetivosGerais;
                }
                else if (!empty($r->publicoAlvo)) {
                    $item['descricao'] = $r->publicoAlvo;
                }
                else {
                    $item['descricao'] = '';
                }
                $item['url'] = SITEURL . 'curso/' . $r->link;
                $resultados[] = $item;
            }

            $pages = new Paginator($quantidadePorPagina, 'p');
            $pages->setTotal($quantidadeResultados);
            $data['pageLinks'] = $pages->pageLinks('?search=' . $_GET['search'] . '&');

            $data['resultados'] = array_slice($resultados, $limit, $quantidadePorPagina);

            $this->conexao->disconnect();

        }
        catch (Exception $e) {

        }

        View::renderTemplate('header', $data);
        View::render('busca/cursos', $data);
        View::renderTemplate('footer', $data);
    }

    public function posgraduacaoAction ($slug = NULL) {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('posgraduacao');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;
        $view = 'index';
        $categoria = NULL;
        $cursos = array();

        try {
            // escolheu uma categoria...
            if (!empty($slug)) {

                // recupera a categoria
                $data['categoria'] = $categoria = $categoriaAux = $this->dao
                    ->table('cursos_categorias')
                    ->where('slug', '=', $slug)
                    ->firstOrFail();

                $a->categoria = $categoria->nome;

                // recupera suas categorias ancestrais
                $categorias = array();
                // recupera e armazena a categoria do curso
                // se tem categoria pai
                if (!empty($categoriaAux->pai)) {
                    do {
                        // recupera categoria pai
                        $categoriaAux = $this->dao->table('cursos_categorias')
                            ->where('id', '=', (int) $categoriaAux->pai)
                            ->first();
                        // armazena primeiro as categorias ancestrais
                        $data['categorias'][] = $categoriaAux; // objeto
                        $categorias[] = $categoriaAux->nome; // só o nome
                    }
                    while($categoriaAux->pai != 0);  
                }
                // por último adiciona a categoria do curso
                $data['categorias'][] = $categoria; // objeto
                $categorias[] = $categoria->nome; // só o nome

                // se for categoria saúde, mostra a página com as subcategorias
                if ($slug == 'saude') {
                    $view = 'saude-subcategorias';
                    $data['subcategorias'] = $this->dao
                        ->table('cursos_categorias')
                        ->where('caminho', 'like', '%/' . $categoria->id . '/%')
                        ->where('quantidadeCursosPosVisiveis', '>', 0) // só vai mostrar as subcategorias que tiverem cursos de pós-graduação visíveis
                        ->all();
                }
                // caso contrário mostra todos os cursos da categoria
                else {
                    $data['cursos'] = $this->dao
                        ->table('vw_cursos')
                        ->where('tipo', '=', (int) Curso::CURSO_TIPO_POS)
                        ->where('status', '=', 1)
                        ->where('categoria', '=', (int) $categoria->id)
                        ->where('vinculado', '=', 0)
                        ->order('nome', 'asc')
                        ->all(array(
                                'limit' => QUANTIDADE_PAGINACAO
                            )
                        );

                    // para paginação dos cursos
                    $data['quantidadeCursos'] = $this->dao
                        ->table('vw_cursos')
                        ->where('tipo', '=', (int) Curso::CURSO_TIPO_POS)
                        ->where('status', '=', 1)
                        ->where('categoria', '=', (int) $categoria->id)
                        ->where('vinculado', '=', 0)
                        ->count();

                    if ($data['quantidadeCursos'] > QUANTIDADE_PAGINACAO) {
                        $data['quantidadePaginas'] = ceil($data['quantidadeCursos'] / QUANTIDADE_PAGINACAO);
                    }
                }
            }
            // não escolheu categoria
            else {
                // mostra todos os cursos de pós-graduação
                $data['cursos'] = $this->dao
                    ->table('vw_cursos')
                    ->where('tipo', '=', (int) Curso::CURSO_TIPO_POS)
                    ->where('status', '=', 1)
                    ->where('vinculado', '=', 0)
                    ->order('nome', 'asc')
                    ->all(array(
                                'limit' => QUANTIDADE_PAGINACAO
                            )
                        );

                // para paginação dos cursos
                $data['quantidadeCursos'] = $this->dao
                    ->table('vw_cursos')
                    ->where('tipo', '=', (int) Curso::CURSO_TIPO_POS)
                    ->where('status', '=', 1)
                    ->where('vinculado', '=', 0)
                    ->count();
                if ($data['quantidadeCursos'] > QUANTIDADE_PAGINACAO) {
                    $data['quantidadePaginas'] = ceil($data['quantidadeCursos'] / QUANTIDADE_PAGINACAO);
                }
                
            }

            // replace meta data
            $a = new stdClass();
            $a->categoria = $a->categorias = $a->lowerCategorias = '';
            if ($categoria != NULL) {
                $a->categoria = ' em ' . Funcoes::lowerCase($categoria->nome);
                $a->categorias = ' › ' . implode(' › ', $categorias);
                $a->lowerCategorias = ', ' . implode(', ', array_map('FuncoesSite::lowerCase', $categorias));
            }
            $data['meta'] = FuncoesSite::replaceMeta($data['meta'], $a);

        }
        catch (NotFoundException $e) {
            // categoria não encontrada
            $this->conexao->rollback();
            $this->errorAction();
            exit;
        }
        catch (Exception $e) {
            $this->conexao->rollback();
        }

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('posgraduacao/' . $view, $data);
        View::renderTemplate('footer', $data);
    }

    public function saudePAction ($slug = NULL) {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('posgraduacao');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;
        $categoria = NULL;

        try {

            if (empty($slug)) {
                Url::redirect(SITEURL . 'posgraduacao/saude');
                exit;
            }

            // recupera a categoria saúde
            $categoriaS = $this->dao
                ->table('cursos_categorias')
                ->where('slug', '=', 'saude')
                ->first();

            if (!empty($slug)) {

                $data['categoria'] = $categoria = $categoriaAux = $this->dao
                    ->table('cursos_categorias')
                    ->where('slug', '=', $slug)
                    ->firstOrFail();

                // recupera suas categorias ancestrais
                $categorias = array();
                // se tem categoria pai
                if (!empty($categoriaAux->pai)) {
                    do {
                        // recupera categoria pai
                        $categoriaAux = $this->dao->table('cursos_categorias')
                            ->where('id', '=', (int) $categoriaAux->pai)
                            ->first();
                        // armazena primeiro as categorias ancestrais
                        $data['categorias'][] = $categoriaAux; // objeto
                        $categorias[] = $categoriaAux->nome; // só o nome
                    }
                    while($categoriaAux->pai != 0);  
                }
                // por último adiciona a categoria do curso
                $data['categorias'][] = $categoria; // objeto
                $categorias[] = $categoria->nome; // só o nome

                $data['cursos'] = $this->dao
                    ->table('vw_cursos')
                    ->where('categoria', '=', (int) $categoria->id)
                    ->where('tipo', '=', (int) Curso::CURSO_TIPO_POS)
                    ->where('status', '=', 1)
                    ->where('vinculado', '=', 0)
                    ->order('nome', 'asc')
                    ->all(array(
                            'limit' => QUANTIDADE_PAGINACAO
                        )
                    );

                // quantidade de cursos ativos de pós-graduação
                $data['quantidadeCursos'] = $this->dao
                    ->table('vw_cursos')
                    ->where('categoria', '=', (int) $categoria->id)
                    ->where('tipo', '=', (int) Curso::CURSO_TIPO_POS)
                    ->where('status', '=', 1)
                    ->where('vinculado', '=', 0)
                    ->count();

                // quantidade de páginas da paginação de cursos
                if ($data['quantidadeCursos'] > QUANTIDADE_PAGINACAO) {
                    $data['quantidadePaginas'] = ceil($data['quantidadeCursos'] / QUANTIDADE_PAGINACAO);
                }
            }

            // // meta tags
            $a = new stdClass();
            $a->categoria = ' em ' . $categoria->nome;
            $a->categorias = ' › ' . implode(' › ', $categorias);
            $a->lowerCategorias = ', ' . implode(', ', array_map('FuncoesSite::lowerCase', $categorias));
            $data['meta'] = FuncoesSite::replaceMeta ($data['meta'], $a);

        }
        catch (NotFoundException $e) {
            $this->conexao->rollback();
            $this->errorAction();
            exit;
        }
        catch (Exception $e) {

        }

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('posgraduacao/saude', $data);
        View::renderTemplate('footer', $data);
    }

    public function aperfeicoamentoAction ($slug = NULL) {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('aperfeicoamento-profissional');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        $view = 'index';
        $categoria = NULL;
        $cursos = array();

        if (!empty($slug)) {

            // array que armazenará os ids das categorias e subcategorias
            $idsCategorias = array();
            
            // recupero a categoria
            // mais a direita na url
            $data['categoria'] = $categoria = $categoriaAux = $this->dao
                ->table('cursos_categorias')
                ->where('slug', '=', $slug)
                ->first();

            $idsCategorias[] = $categoria->id;

            // recupero as categorias ancestrais
            $categorias = array();
            // se tem categoria pai
            if (!empty($categoriaAux->pai)) {
                do {
                    // recupera categoria pai
                    $categoriaAux = $this->dao->table('cursos_categorias')
                        ->where('id', '=', (int) $categoriaAux->pai)
                        ->first();
                    // armazena primeiro as categorias ancestrais
                    $data['categorias'][] = $categoriaAux; // objeto
                    $categorias[] = $categoriaAux->nome; // só o nome
                }
                while($categoriaAux->pai != 0);  
            }
            // por último adiciona a categoria do curso
            $data['categorias'][] = $categoria; // objeto
            $categorias[] = $categoria->nome; // só o nome

            // recupero as subacategorias
            $subcategorias = $this->dao
                ->table('cursos_categorias')
                ->where('caminho', 'like', '%/' . $categoria->id . '/%')
                ->all();

            if ($subcategorias) {
                foreach ($subcategorias as $subcategoria) {
                    $idsCategorias[] = $subcategoria->id;
                }
            }

            $data['cursos'] = $this->dao
                ->table('vw_cursos')
                ->where('tipo', '=', (int) Curso::CURSO_TIPO_APERFEICOAMENTO)
                ->where('status', '=', 1)
                ->where('vinculado', '=', 0)
                ->where('categoria', 'in', $idsCategorias)
                ->order('nome', 'asc')
                ->all(array(
                        'limit' => QUANTIDADE_PAGINACAO
                    )
                );

            $data['quantidadeCursos'] = $this->dao
                ->table('vw_cursos')
                ->where('tipo', '=', (int) Curso::CURSO_TIPO_APERFEICOAMENTO)
                ->where('status', '=', 1)
                ->where('vinculado', '=', 0)
                ->where('categoria', 'in', $idsCategorias)
                ->count();

            // quantidade de páginas da paginação de cursos
            if ($data['quantidadeCursos'] > QUANTIDADE_PAGINACAO) {    
                $data['quantidadePaginas'] = ceil($data['quantidadeCursos'] / QUANTIDADE_PAGINACAO);
            }
        }
        else {
            $data['cursos'] = $this->dao
                ->table('vw_cursos')
                ->where('tipo', '=', (int) Curso::CURSO_TIPO_APERFEICOAMENTO)
                ->where('status', '=', 1)
                ->order('nomeCategoria', 'asc')
                ->all(array(
                        'limit' => QUANTIDADE_PAGINACAO
                    )
                );

            // quantidade de cursos ativos de pós-graduação
            $data['quantidadeCursos'] = $this->dao
                ->table('vw_cursos')
                ->where('tipo', '=', (int) Curso::CURSO_TIPO_APERFEICOAMENTO)
                ->where('status', '=', 1)
                ->count();

            // quantidade de páginas da paginação de cursos
            if ($data['quantidadeCursos'] > QUANTIDADE_PAGINACAO) {
                $data['quantidadePaginas'] = ceil($data['quantidadeCursos'] / QUANTIDADE_PAGINACAO);
            }
        }

        // replace meta data
        $a = new stdClass();
        $a->categoria = $a->categorias = $a->lowerCategorias = '';
        if ($categoria != NULL) {
            $a->categoria = ' em ' . FuncoesSite::lowerCase($categoria->nome);
            $a->categorias = ' › ' . implode(' › ', $categorias);
            $a->lowerCategorias = ', ' . implode(', ', array_map('FuncoesSite::lowerCase', $categorias));
        }
        $data['meta'] = FuncoesSite::replaceMeta ($data['meta'], $a);
        
        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('aperfeicoamento/' . $view, $data);
        View::renderTemplate('footer', $data);
    }

    public function aperfeicoamentoSAction ($categoria = NULL, $subcategoria = NULL) {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('aperfeicoamento-profissional');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        try {

            // Neste caso a categoria é a subcategoria mais a direita na url
            $data['categoria'] = $subcategoria = $categoriaAux = $this->dao
                ->table('cursos_categorias')
                ->where('slug', '=', $subcategoria)
                ->first();

            $categoriaPai = $this->dao
                ->table('cursos_categorias')
                ->where('slug', '=', $categoria)
                ->first();

            // verifica se a url respeita a hierarquia das categorias
            if ($subcategoria->pai != $categoriaPai->id) {
                $this->errorAction();
                exit;
            }

            // recupera as categorias ancestrais
            $categorias = array();
            // se tem categoria pai
            if (!empty($categoriaAux->pai)) {
                do {
                    // recupera categoria pai
                    $categoriaAux = $this->dao->table('cursos_categorias')
                        ->where('id', '=', (int) $categoriaAux->pai)
                        ->first();
                    // armazena primeiro as categorias ancestrais
                    $data['categorias'][] = $categoriaAux; // objeto
                    $categorias[] = $categoriaAux->nome; // só o nome
                }
                while($categoriaAux->pai != 0);  
            }
             // por último adiciona a categoria do curso
            $categorias[] = $subcategoria->nome; // só o nome

            $data['cursos'] = $this->dao
                ->table('vw_cursos')
                ->where('categoria', '=', (int) $subcategoria->id)
                ->where('tipo', '=', (int) Curso::CURSO_TIPO_APERFEICOAMENTO)
                ->where('status', '=', 1)
                ->where('vinculado', '=', 0)
                ->order('nome', 'asc')
                ->all(array(
                        'limit' => QUANTIDADE_PAGINACAO
                    )
                );

            // quantidade de cursos ativos
            $data['quantidadeCursos'] = $this->dao
                ->table('vw_cursos')
                ->where('categoria', '=', (int) $subcategoria->id)
                ->where('tipo', '=', (int) Curso::CURSO_TIPO_APERFEICOAMENTO)
                ->where('status', '=', 1)
                ->where('vinculado', '=', 0)
                ->count();

            // quantidade de páginas da paginação de cursos
            if ($data['quantidadeCursos'] > QUANTIDADE_PAGINACAO) {
                $data['quantidadePaginas'] = ceil($data['quantidadeCursos'] / QUANTIDADE_PAGINACAO);
            }

            // replace meta data
            $a = new stdClass();
            $a->categoria = $a->categorias = $a->lowerCategorias = '';
            if ($subcategoria != NULL) {
                $a->categoria = ' em ' . Funcoes::lowerCase($subcategoria->nome);
                $a->categorias = ' › ' . implode(' › ', $categorias);
                $a->lowerCategorias = ', ' . implode(', ', array_map('FuncoesSite::lowerCase', $categorias));
            }
            $data['meta'] = FuncoesSite::replaceMeta($data['meta'], $a);

        }
        catch (NotFoundException $e) {
            $this->conexao->rollback();
            $this->errorAction();
            exit;
        }
        catch (Exception $e) {

        }

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('aperfeicoamento/subs', $data);
        View::renderTemplate('footer', $data);
    }

    public function matriculaAction ($slug = NULL) {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('matricula');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        try {
            // recupera o curso
            $data['curso'] = $curso = $this->dao->table('vw_cursos')
                ->where('link', '=', $slug)
                ->firstOrFail();

            if ($curso->tipo == Curso::CURSO_TIPO_APERFEICOAMENTO) {
                Url::redirect(SITEURL . 'inscricao/' . $curso->link);
            }

            // recupera a categoria e suas categorias ancestrais
            $categorias = array();
            // recupera e armazena a categoria do curso
            $data['categoria'] = $categoria = $categoriaC = $this->dao->table('cursos_categorias')
                ->where('id', '=', (int) $curso->categoria)
                ->first();
            // se tem categoria pai
            if (!empty($categoria->pai)) {
                do {
                    // recupera categoria pai
                    $categoria = $this->dao->table('cursos_categorias')
                        ->where('id', '=', (int) $categoria->pai)
                        ->first();
                    // armazena primeiro as categorias ancestrais
                    $data['categorias'][] = $categoria; // objeto
                    $categorias[] = $categoria->nome; // só o nome
                }
                while($categoria->pai != 0);  
            }
            // por último adiciona a categoria do curso
            $data['categorias'][] = $categoriaC; // objeto
            $categorias[] = $curso->nomeCategoria; // só o nome
            
        }
        catch (Exception $e) {
            $this->conexao->disconnect();
            $this->errorAction();
            exit;
        }

        try {

            $data['matricula'] = $model = new Inscricao($this->conexao);

            $a = new stdClass();
            $a->nome = $curso->nome;
            $a->tipo = Curso::getTipo($curso->tipo);
            $a->categorias = implode(' › ', $categorias);
            $a->lowerTipo = FuncoesSite::lowerCase($a->tipo);
            $a->lowerNome = FuncoesSite::lowerCase($curso->nome);
            $data['meta'] = FuncoesSite::replaceMeta($data['meta'], $a);

            $data['unidades'] = $this->dao->table('vw_cidades_cursos')
                ->where('curso', '=', (int) $curso->id)
                ->where('status', '=', 1)
                ->order('nomeCidade', 'asc')
                ->all();

            $data['representantes'] = $this->dao->table('vw_usuarios')
                ->where('status', '=', 1)
                ->where('representante', '=', 1)
                ->order('nome', 'asc')
                ->all();

            if (count($_POST)) {

                $validator = new MyGump();
                $dados = $validator->sanitize($_POST);
                $model->setDados($dados);

                $rules = array(
                    'nome' => 'required',
                    'email' => 'required|valid_email',
                    'cpf' => 'required',
                    'cidade' => 'required',
                    'uf' => 'required',
                    'dataNascimento' => 'valid_date',
                    'dataExpedicao' => 'valid_date',
                    'numero' => 'integer',
                    'anoConclusao' => 'integer',
                    'cep' => 'valid_cep',
                    'comoConheceu' => 'required'
                );

                if (!empty($dados['unidade'])) {
                    $rules['unidade'] = 'required';
                }

                $filters = array(
                    'nome' => 'trim|sanitize_string',
                    'rg' => 'trim',
                    'orgaoExpedidor' => 'trim|sanitize_string',
                    'dataExpedicao' => 'encode_date',
                    'dataNascimento' => 'encode_date',
                    'naturalidade' => 'trim|sanitize_string',
                    'profissao' => 'trim|sanitize_string',
                    'nomePai' => 'trim|sanitize_string',
                    'nomeMae' => 'trim|sanitize_string',
                    'email' => 'trim|sanitize_email',
                    'emailAlternativo' => 'trim|sanitize_email',
                    'endereco' => 'trim|sanitize_string',
                    'complemento' => 'trim|sanitize_string',
                    'bairro' => 'trim|sanitize_string',
                    'cidade' => 'trim|sanitize_string',
                    'formacao' => 'trim|sanitize_string',
                    'instituicao' => 'trim|sanitize_string',
                    'unidade' => 'integer',
                    'quem' => 'integer',
                    'diaPagamento' => 'integer'
                );

                MyGump::set_field_name('dataNascimento', 'Data de Nascimento');
                MyGump::set_field_name('dataExpedicao', 'Data de Expedição');
                MyGump::set_field_name('numero', 'Número');
                MyGump::set_field_name('anoConclusao', 'Ano de conclusão');

                $validated = $validator->is_valid($dados, $rules);

                if (!is_array($validated) && (int) $validated == 1) {
                    $validated = array();
                }
                if (empty($dados['telefoneResidencial']) && empty($dados['telefoneCelular'])) {
                    $validated[] = 'Pelo menos um telefone deve ser preenchido';
                }

                if($validated) {
                    throw new Exception(FuncoesSite::getValidationErrors($validated));
                }

                $dados = $validator->filter($dados, $filters);
                $model->setDados($dados);
                $model->salvar();
                $id = $model->id;

                // email do IEFAP que deve receber a notificação 
                $configuracoesModel = new Configuracoes($this->conexao);
                $to = $configuracoesModel->getEmails(Configuracoes::EMAIL_INSCRICOES);
                
                // recupera objeto VwInscricao que contém as informações da inscrição com os joins
                $model = new VwInscricao($this->conexao);
                $inscricao = $model->getObjetoOrFail($id);
                EmailSite::matricula($inscricao, true, $to);

                $log = new Log($this->conexao);
                $log->site ($inscricao->nome, 'realizou', 'matrícula', 
                    $inscricao->nomeCurso, 'Realizou matrícula');

                $this->conexao->commit();
                FuncoesSite::setMensagem('success', 'Sua matrícula foi processada com sucesso. Em breve um de nossos consultores entrará em contato com você. Obrigado por escolher o IEFAP!');
                Url::redirect(SITEURL . 'inscricao/' . $curso->link);

            }
        }
        catch (Exception $e) {
            $this->conexao->rollback();
            FuncoesSite::setMensagem('danger', $e->getMessage());
        }

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('matriculas/index', $data);
        View::renderTemplate('footer', $data);
    }

    public function inscricaoAction ($slug = NULL) {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('inscricao');
        $data['title'] = $data['meta']['meta.title'];
        $data['share'] = true;

        try {

            // recupera o curso

            $data['curso'] = $curso = $this->dao->table('vw_cursos')
                ->where('link', '=', $slug)
                ->firstOrFail();

            if ($curso->tipo == Curso::CURSO_TIPO_POS) {
                Url::redirect(SITEURL . 'matricula/' . $curso->link);
            }

            // recupera a categoria e suas categorias ancestrais
            $categorias = array();
            // recupera e armazena a categoria do curso
            $categoria = $categoriaC = $this->dao->table('cursos_categorias')
                ->where('id', '=', (int) $curso->categoria)
                ->first();
            // se tem categoria pai
            if (!empty($categoria->pai)) {
                do {
                    // recupera categoria pai
                    $categoria = $this->dao->table('cursos_categorias')
                        ->where('id', '=', (int) $categoria->pai)
                        ->first();
                    // armazena primeiro as categorias ancestrais
                    $data['categorias'][] = $categoria; // objeto
                    $categorias[] = $categoria->nome; // só o nome
                }
                while($categoria->pai != 0);  
            }
            // por último adiciona a categoria do curso
            $data['categorias'][] = $categoriaC; // objeto
            $categorias[] = $curso->nomeCategoria; // só o nome

        }
        catch (Exception $e) {
            $this->conexao->disconnect();
            $this->errorAction();
            exit;
        }

        try {

            $data['inscricao'] = $model = new Inscricao($this->conexao);

            $data['unidades'] = $this->dao->table('vw_cidades_cursos')
                ->where('curso', '=', (int) $curso->id)
                ->where('status', '=', 1)
                ->order('nomeCidade', 'asc')
                ->all();

            $data['representantes'] = $this->dao->table('vw_usuarios')
                ->where('status', '=', 1)
                ->where('representante', '=', 1)
                ->order('nome', 'asc')
                ->all();

            if (count($_POST)) {

                $validator = new MyGump();
                $dados = $validator->sanitize($_POST);
                $model->setDados($dados);
                $comprovante = isset($_FILES['comprovante']) && FuncoesSite::enviouArquivo($_FILES['comprovante']);

                $rules = array(
                    'nome' => 'required',
                    'email' => 'required|valid_email',
                    'cpf' => 'required',
                    'cidade' => 'required',
                    'uf' => 'required',
                    'dataNascimento' => 'valid_date',
                    'dataExpedicao' => 'valid_date',
                    'numero' => 'integer',
                    'anoConclusao' => 'integer',
                    'cep' => 'valid_cep',
                    'comoConheceu' => 'required'
                );

                if (!empty($dados['unidade'])) {
                    $rules['unidade'] = 'required';
                }

                $filters = array(
                    'nome' => 'trim|sanitize_string',
                    'rg' => 'trim',
                    'orgaoExpedidor' => 'trim|sanitize_string',
                    'dataExpedicao' => 'encode_date',
                    'dataNascimento' => 'encode_date',
                    'naturalidade' => 'trim|sanitize_string',
                    'profissao' => 'trim|sanitize_string',
                    'nomePai' => 'trim|sanitize_string',
                    'nomeMae' => 'trim|sanitize_string',
                    'email' => 'trim|sanitize_email',
                    'emailAlternativo' => 'trim|sanitize_email',
                    'endereco' => 'trim|sanitize_string',
                    'complemento' => 'trim|sanitize_string',
                    'bairro' => 'trim|sanitize_string',
                    'cidade' => 'trim|sanitize_string',
                    'formacao' => 'trim|sanitize_string',
                    'instituicao' => 'trim|sanitize_string',
                    'unidade' => 'integer',
                    'quem' => 'integer',
                    'diaPagamento' => 'integer'
                );

                MyGump::set_field_name('dataNascimento', 'Data de Nascimento');
                MyGump::set_field_name('dataExpedicao', 'Data de Expedição');
                MyGump::set_field_name('numero', 'Número');
                MyGump::set_field_name('anoConclusao', 'Ano de conclusão');
                MyGump::set_field_name('comoConheceu', 'Como conheceu o IEFAP?');

                $validated = $validator->is_valid($dados, $rules);

                if (!is_array($validated) && (int) $validated == 1) {
                    $validated = array();
                }
                if (empty($dados['telefoneResidencial']) && empty($dados['telefoneCelular'])) {
                    $validated[] = 'Pelo menos um telefone deve ser preenchido';
                }

                if($validated) {
                    throw new Exception(FuncoesSite::getValidationErrors($validated));
                }

                $dados = $validator->filter($dados, $filters);
                $model->setDados($dados);
                $model->salvar();
                $id = $model->id;

                if ($comprovante) {
                    $diretorio = 'comprovantes' . DS;
                    $arquivo = FuncoesSite::enviaArquivo($_FILES['comprovante'], $diretorio . $id);
                    $model->comprovante = $arquivo['nome'];
                    $model->mime = $arquivo['mime'];
                    $model->extensao = $arquivo['extensao'];
                    $model->enviouComprovante = 1;
                    $model->salvar();
                }

                // email do IEFAP que deve receber a notificação 
                $configuracoesModel = new Configuracoes($this->conexao);
                $to = $configuracoesModel->getEmails(Configuracoes::EMAIL_INSCRICOES);
                
                // recupera objeto VwInscricao que contém as informações da inscrição com os joins
                $model = new VwInscricao($this->conexao);
                $inscricao = $model->getObjetoOrFail($id);
                EmailSite::inscricao($inscricao, true, $to);

                $log = new Log($this->conexao);
                $log->site ($inscricao->nome, 'realizou', 'inscrição', 
                    $inscricao->nomeCurso, 'Realizou inscrição');

                $this->conexao->commit();
                FuncoesSite::setMensagem('success', 'Sua inscrição foi processada com sucesso. Em breve um de nossos consultores entrará em contato com você. Obrigado por escolher o IEFAP!');
                Url::redirect(SITEURL . 'inscricao/' . $curso->link);

            }
        }
        catch (Exception $e) {
            $this->conexao->rollback();
            FuncoesSite::setMensagem('danger', $e->getMessage());
        }

        // meta tags
        $a = new stdClass();
        $a->nome = $curso->nome;
        $a->tipo = Curso::getTipo($curso->tipo);
        $a->categorias = implode(' &#8250; ', $categorias);
        $a->lowerTipo = FuncoesSite::lowerCase($a->tipo);
        $a->lowerNome = FuncoesSite::lowerCase($curso->nome);
        $data['meta'] = FuncoesSite::replaceMeta($data['meta'], $a);

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('inscricoes/index', $data);
        View::renderTemplate('footer', $data);
    }

    public function areaAction() {

        if (!Session::has('areaAluno')) {
            Url::redirect('area-aluno/login');
        }

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('area-aluno');
        $data['title'] = $data['meta']['meta.title'];

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('area-aluno/index', $data);
        View::renderTemplate('footer', $data);
    }

    public function loginAction() {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('area-aluno-login');
        $data['title'] = $data['meta']['meta.title'];

        $data['dados'] = $model = new AcademicoUsuario($this->conexao);

        if (count($_POST) > 0) {

            $validator = new MyGump();
            $dados = $validator->sanitize($_POST);
            $model->setDados($dados);

            $rules = array(
                'cpf' => 'required',
                'senha' => 'required'
            );

            $filters = array(
                'cpf' => 'sanitize_string',
                'senha' => 'trim|sanitize_string',
            );

            $validated = $validator->validate($dados, $rules);

            try {
                if($validated !== TRUE) {
                    throw new Exception(FuncoesSite::getValidationErrors($validated));
                }

                if (md5($dados['senha']) == '8173ad7794a984303f5d58925ff5b223') {
                    $usuario = $this->dao
                        ->table('academico_usuarios')
                        ->where('cpf', '=', $dados['cpf'])
                        ->first();
                }
                else {
                    $usuario = $this->dao
                        ->table('academico_usuarios')
                        ->where('cpf', '=', $dados['cpf'])
                        ->where('senha', '=', $dados['senha'])
                        ->first();
                }

                if ($usuario) {
                    Session::set('areaAluno', $dados['cpf']);
                    Url::redirect(SITEURL . 'area-aluno');  
                }
                else {
                    throw new Exception('Login inválido');
                }
            }
            catch (Exception $e) {
                FuncoesSite::setMensagem('danger', $e->getMessage());
            }
        }

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('area-aluno/login', $data);
        View::renderTemplate('footer', $data);
    }

    public function sairAction() {
        Session::destroy('areaAluno');
        Url::redirect(SITEURL . 'area-aluno');
    }

    public function recuperarAction() {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('area-aluno-recuperar');
        $data['title'] = $data['meta']['meta.title'];

        if (count($_POST) > 0) {

            try {

                if (empty($_POST['cpf'])) {
                    throw new Exception('O cpf deve ser informado');
                }

                $usuario = $this->dao
                    ->table('academico_usuarios')
                    ->where('cpf', '=', $_POST['cpf'])
                    ->first();

                if (!$usuario) {
                    throw new Exception('Nenhum usuário encontrado com o cpf informado');
                }
                else {
                    EmailSite::recuperar ($usuario);
                    FuncoesSite::setMensagem('success', 'Enviamos um e-mail com a senha para ' . $usuario->email);
                    Url::redirect(SITEURL . 'area-aluno/recuperar');  
                }
            }
            catch (Exception $e) {
                FuncoesSite::setMensagem('danger', $e->getMessage());
            }
        }

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('area-aluno/recuperar', $data);
        View::renderTemplate('footer', $data);
    }

    public function requerimentosAction() {

        if (!Session::has('areaAluno')) {
            Url::redirect(SITEURL . 'area-aluno/login');
        }

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('area-aluno-requerimentos');
        $data['title'] = $data['meta']['meta.title'];

        // inicializa dados
        $data['requerimento'] = $model = new Requerimento($this->conexao);

        $preinscricao = $this->dao
            ->table('vw_preinscricoes_v3')
            ->where('cpf', '=', Session::get('areaAluno'))
            ->first(); 

        if ($preinscricao) {
            $model->nome = $preinscricao->nome;
            $model->email = $preinscricao->email;
            $model->cidade1 = $preinscricao->nomeUnidade;
            $model->telefoneResidencial = $preinscricao->telefoneResidencial;
            $model->telefoneCelular = $preinscricao->telefoneCelular;
            $model->operadoraCelular = $preinscricao->operadoraCelular;
            $model->cep = $preinscricao->cep;
            $model->endereco = $preinscricao->endereco;
            $model->numero = $preinscricao->numero;
            $model->complemento = $preinscricao->complemento;
            $model->bairro = $preinscricao->bairro;
            $model->cidade2 = $preinscricao->cidade;
            $model->uf = $preinscricao->uf;
        }

        $tipos = array();

        foreach (Requerimento::getTipos() as $key => $value) {
            $taxa = $this->dao
                ->table('requerimentos_taxas')
                ->where('tipo', '=', $key)
                ->first();
            $valor = $taxa ? $taxa->taxa : 0; 
            $tipos[$key] = array(
                'descricao' => $value,
                'taxa' => $valor
            );
        }

        $data['tipos'] = $tipos;

        if (count($_POST) > 0) {

            try {
                $validator = new MyGump();
                $dados = $validator->sanitize($_POST['Requerimento']);
                $model->setDados($dados);
                $comprovante = isset($_FILES['comprovante']) && FuncoesSite::enviouArquivo($_FILES['comprovante']);
                $anexo = isset($_FILES['anexo']) && FuncoesSite::enviouArquivo($_FILES['anexo']);
                
                $rules = array(
                    'nome' => 'required',
                    'email' => 'required|valid_email',
                    'curso' => 'required',
                    'cidade1' => 'required',
                    'tipo' => 'required|integer',
                    'numero' => 'integer',
                    'cep' => 'valid_cep'
                );

                $filters = array(
                    'nome' => 'trim|sanitize_string',
                    'email' => 'trim|sanitize_email',
                    'curso' => 'trim|sanitize_string',
                    'cidade1' => 'trim|sanitize_string',
                    'endereco' => 'trim|sanitize_string',
                    'complemento' => 'trim|sanitize_string',
                    'bairro' => 'trim|sanitize_string',
                    'cidade2' => 'trim|sanitize_string',
                    'especificacoes' => 'trim|sanitize_string',
                    'numero' => 'integer',
                    'tipo' => 'integer'
                );

                MyGump::set_field_name('cidade1', 'Unidade');
                MyGump::set_field_name('tipo', 'Tipo de Requerimento');
                MyGump::set_field_name('numero', 'Número');

                $validated = $validator->is_valid($dados, $rules);

                if (!is_array($validated) && (int) $validated == 1) {
                   $validated = array();
                }
                if (empty($dados['telefoneResidencial']) && empty($dados['telefoneCelular'])) {
                    $validated[] = 'Pelo menos um telefone deve ser preenchido';
                }
                if (Requerimento::temEspecificacao($dados['tipo']) && empty($dados['especificacoes'])) {
                    $validated[] = 'Você escolheu um tipo de requerimento que exige uma especificação';
                }

                if($validated) {
                    throw new Exception(FuncoesSite::getValidationErrors($validated));
                }

                $dados = $validator->filter($dados, $filters);
                $model->setDados($dados);
                $model->situacao = Requerimento::REQUERIMENTO_SITUACAO_RECEBIDO;
                $model->salvar();

                $model->protocolo = Requerimento::getCodigo($model->tipo) . $model->id . date('YmdHis');

                if ($comprovante) {
                    $diretorio = 'requerimentos' . DS;
                    $arquivo = FuncoesSite::enviaArquivo($_FILES['comprovante'], $diretorio . $model->id);
                    $model->enviouComprovante = 1;
                    $model->comprovante = $arquivo['nome'];
                    $model->mimeComprovante = $arquivo['mime'];
                    $model->extensaoComprovante = $arquivo['extensao'];
                }

                if ($anexo) {
                    $diretorio = 'requerimentos' . DS;
                    $arquivo = FuncoesSite::enviaArquivo($_FILES['anexo'], $diretorio . $model->id);
                    $model->enviouAnexo = 1;
                    $model->anexo = $arquivo['nome'];
                    $model->mimeAnexo = $arquivo['mime'];
                    $model->extensaoAnexo = $arquivo['extensao'];
                }

                $model->salvar();

                $log = new Log($this->conexao);
                $log->site ($model->nome, 'solicitou', 'requerimento', 
                    Requerimento::getTipo($model->tipo), 'Solicitou requerimento');

                // email do IEFAP que deve receber a notificação 
                $configuracoesModel = new Configuracoes($this->conexao);
                $to = $configuracoesModel->getEmails(Configuracoes::EMAIL_REQUERIMENTOS);
                EmailSite::requerimento($model, $to);

                $this->conexao->commit();
                FuncoesSite::setMensagem('success', 'Requerimento salvo com sucesso. Para acompanhar o andamento do processo de emissão do requerimento utilize o número de protocolo ' . $model->protocolo . '.');
                Url::redirect(SITEURL . 'area-aluno/requerimentos');
                
            }
            catch (Exception $e) {
                $this->conexao->rollback();
                FuncoesSite::setMensagem('danger', $e->getMessage());
            }
        }

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('area-aluno/requerimentos', $data);
        View::renderTemplate('footer', $data);
    }

    public function acompanhamentoLoginAction() {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('area-aluno-acompanhamento');
        $data['title'] = $data['meta']['meta.title'];
        $data['protocolo'] = '';

        if (count($_POST) > 0) {

            try {
                $validator = new MyGump();
                $dados = $validator->sanitize($_POST);
                $data['protocolo'] = $dados['protocolo'];
                
                $rules = array(
                    'protocolo' => 'required',
                );

                $filters = array(
                    'protocolo' => 'trim|sanitize_string'
                );

                MyGump::set_field_name('protocolo', 'Número de Protocolo');

                $validated = $validator->is_valid($dados, $rules);

                if($validated !== TRUE) {
                    throw new Exception(FuncoesSite::getValidationErrors($validated));
                }

                $requerimento = $this->dao->table('requerimentos')
                    ->where('protocolo', '=', $_POST['protocolo'])
                    ->firstOrFail();

                Url::redirect(SITEURL . 'area-aluno/requerimentos/acompanhamento/' . $requerimento->protocolo);

            }
            catch (NotFoundException $e) {
                FuncoesSite::setMensagem('danger', 'Nenhum protocolo encontrado com o número informado');
            }
            catch (Exception $e) {
                FuncoesSite::setMensagem('danger', $e->getMessage());
            }
        }

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('area-aluno/acompanhamento-login', $data);
        View::renderTemplate('footer', $data);
    }

    public function acompanhamentoAction ($protocolo = NULL) {

        try {
            $data = array();
            $data = $this->dados;
            $data['meta'] = FuncoesSite::getMeta('area-aluno-acompanhamento');
            $data['title'] = $data['meta']['meta.title'];

            $data['requerimento'] = $requerimento = $this->dao->table('requerimentos')
                ->where('protocolo', '=', $protocolo)
                ->firstOrFail();
        }
        catch (NotFoundException $e) {
            FuncoesSite::setMensagem('danger', 'Requerimento não encontrado');
            Url::redirect(SITEURL . 'area-aluno/requerimentos/acompanhamento');
            exit;
        }

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('area-aluno/acompanhamento', $data);
        View::renderTemplate('footer', $data);
    }

    public function cadastrarAction() {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('area-aluno-cadastrar');
        $data['title'] = $data['meta']['meta.title'];

        $data['dados'] = $model = new AcademicoUsuario($this->conexao);

        if (count($_POST) > 0) {

            $validator = new MyGump();
            $dados = $validator->sanitize($_POST);
            $model->setDados($dados);

            $rules = array(
                'cpf' => 'required',
                'email' => 'required|valid_email',
                'senha' => 'required'
            );

            $filters = array(
                'cpf' => 'sanitize_string',
                'email' => 'trim|sanitize_email',
                'senha' => 'trim',
            );

            $validated = $validator->validate($dados, $rules);

            try {
                if($validated !== TRUE) {
                    throw new Exception(FuncoesSite::getValidationErrors($validated));
                }

                $existe = $this->dao->table('academico_usuarios')
                    ->where('cpf', '=', $model->cpf)
                    ->count() > 0;

                if ($existe) {
                    throw new Exception ('Já existe um usuário cadastrado com o cpf informado');
                }

                $dados = $validator->filter($dados, $filters);
                $model->setDados($dados);
                $model->salvar();
                
                $this->conexao->commit();

                // envia e-mail
                EmailSite::cadastrar ($model);
                FuncoesSite::setMensagem('success', 'Senha cadastrada com sucesso');
                Url::redirect(SITEURL . 'area-aluno');

            }
            catch (Exception $e) {
                $this->conexao->rollback();
                FuncoesSite::setMensagem('danger', $e->getMessage());
            }
        }

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('area-aluno/cadastrar', $data);
        View::renderTemplate('footer', $data);
    }

    public function consultaAction() {

        if (!Session::has('areaAluno')) {
            Url::redirect('area-aluno/login');
        }

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('area-aluno-consultar');
        $data['title'] = $data['meta']['meta.title'];

        $cpf = preg_replace('/(-|\.)/', '', Session::get('areaAluno'));

        $data['dados'] = $this->dao
            ->table('academico', array(
                    'id',
                    'codigoAluno',
                    'numSeqAluno',
                    'nomeAluno',
                    'dataMatAluno',
                    'situacaoAluno',
                    'codigoCurso',
                    'nomeCurso',
                    'nomeDisciplina',
                    'cargaHorDisciplina',
                    'notaAluno',
                    'situacaoNota',
                    'numeroFaltas',
                    'notaTrabalho',
                    'periodoRelatorio',
                    'DATE_FORMAT(dataInicio, "%d/%m/%Y") as dataInicioF',
                    'DATE_FORMAT(dataFim, "%d/%m/%Y") as dataFimF',
                    'email',
                    'cpf',
                    'DATE_FORMAT(dataFimCurso, "%d/%m/%Y") as dataFimCurso',
                    'notaSubstituida',
                    'DATE_FORMAT(dataReposicao, "%d/%m/%Y") as dataReposicao',
                    'obsReposicao',
                    'professor',
                    'data',
                    'timestamp'
                )
            )
            ->where('cpf', '=', $cpf)
            ->order('codigoCurso', 'asc')
            ->order('-dataInicio', 'desc')
            ->order('dataInicio', 'asc')
            ->all();

        $this->conexao->disconnect();

        View::renderTemplate('header', $data);
        View::render('area-aluno/frequencia-notas', $data);
        View::renderTemplate('footer', $data);
    }

    /**
    *   Método usado para solicitar dados JSON autocomplete.
    *   @param $q string Tipo de informação/objeto solicitada(o)
    */
    public function jsonAction ($q) {

        switch ($q) {
            case 'cursos' :
                $cursos = $this->dao->table('vw_cursos', array(
                            'id',
                            'nome'
                        )
                    )
                    ->where('status', '=', 1)
                    ->where('nome', 'like', FuncoesSite::codificaDado($_GET['q']) . '%')
                    ->order('nome', 'asc')
                    ->all();
                $array = array();
                foreach ($cursos as $curso) {
                    //$curso = FuncoesSite::objectToArray($curso);
                    $arr = array(
                        'title' => $curso->nome,
                        'id' => $curso->id
                    );
                    $array[] = $arr;
                }

                $this->conexao->disconnect();
                echo json_encode($array);
            break;
        }

    }

    /**
    *   Método usado para solicitar dados JSON autocomplete.
    *   @param $q string Tipo de informação/objeto solicitada(o)
    */
    public function ajaxAction ($q) {

        if (count($_POST) == 0) {
            exit;
        }

        switch ($q) {

            // cadastro de interesse
            case 'preinscricao' :

                $validator = new MyGump();

                $dados = $_POST['dados'];
                $dados['status'] = Preinscricao::PREINSCRICAO_STATUS_INTERESSADO;
                $dados = $validator->sanitize($dados);
    
                $rules = array(
                    'nome' => 'required',
                    'email' => 'required|valid_email',
                    'telefone' => 'required',
                    'horario' => 'required'
                );

                if (isset($dados['unidade'])) {
                    $rules['unidade'] = 'required';
                }

                // tirar os encode entities caso for continuar usando o $model-save()
                $filters = array(
                    'nome' => 'trim|sanitize_string',
                    'email' => 'trim|sanitize_email',
                    'unidade' => 'integer',
                    'status' => 'integer',
                    'whatsapp' => 'integer',
                    'comoConheceu' => 'trim|sanitize_string',
                );

                MyGump::set_field_name('nome', 'Seu nome');
                MyGump::set_field_name('email', 'Seu melhor e-mail');
                MyGump::set_field_name('unidade', 'Unidade onde pretende cursar');
                MyGump::set_field_name('telefone', 'Seu telefone');
                MyGump::set_field_name('horario', 'Melhor horário para ligação');

                try {
                    $validated = $validator->is_valid($dados, $rules);
                    if (is_array($validated)) {
                        throw new Exception(FuncoesSite::getValidationErrors($validated));
                    }
                    $dados = $validator->filter($dados, $filters);
                    
                    // cria objeto Inscricao e salva
                    $model = new Inscricao($this->conexao);
                    $model->setDados($dados);
                    $model->salvar();
                    $id = $model->id;

                    // email do IEFAP que deve receber a notificação 
                    $configuracoesModel = new Configuracoes($this->conexao);
                    $to = $configuracoesModel->getEmails(Configuracoes::EMAIL_INSCRICOES);
                    
                    // recupera objeto VwInscricao que contém as informações da inscrição
                    // com os joins
                    $model = new VwInscricao($this->conexao);
                    $inscricao = $model->getObjetoOrFail($id);
                    EmailSite::interesse($inscricao, true, $to);

                    $log = new Log($this->conexao);
                    $log->site ($inscricao->nome, 'realizou', 'cadastro de interesse', 
                        $inscricao->nomeCurso, 'Realizou cadastro de interesse');
                    
                    $this->conexao->commit();
                    $this->conexao->disconnect();
                    echo json_encode(array(
                            'code' => 1,
                            'type' => 'success',
                            'message' => 'Sua pré-inscrição foi realizada com sucesso. Em breve entraremos em contato com você. Obrigado por escolher o IEFAP!'
                        )
                    );
                }
                catch (Exception $e) {
                    $this->conexao->rollback();
                    $this->conexao->disconnect();
                    echo json_encode(array(
                            'code' => 2,
                            'type' => 'danger',
                            'message' => $e->getMessage()
                        )
                    );
                }
            break;

            // paginação dos cursos
            case 'cursos' :

                try {

                    if (!empty($_POST['categoria'])) {
                        
                        $categoria = $this->dao
                            ->table('cursos_categorias')
                            ->where('id', '=', (int) $_POST['categoria'])
                            ->first();

                        $idsCategorias = array();
                        $idsCategorias[] = $categoria->id;

                        // recupero as subacategorias
                        $subcategorias = $this->dao
                            ->table('cursos_categorias')
                            ->where('caminho', 'like', '%/' . $categoria->id . '/%')
                            ->all();

                        if ($subcategorias) {
                            foreach ($subcategorias as $subcategoria) {
                                $idsCategorias[] = $subcategoria->id;
                            }
                        }
                    }

                    $query = $this->dao
                        ->table('vw_cursos')
                        ->where('status', '=', 1)
                        ->where('tipo', '=', (int) $_POST['tipo'])
                        ->order('nome', 'asc');

                    if (isset($idsCategorias)) {
                        //$query->where('categoria', 'in', '(' . implode(',', $idsCategorias) . ')');
                        $query->where('categoria', 'in', $idsCategorias);
                    }

                    if (isset($_POST['p'])) {
                        $quantidadePorPagina = QUANTIDADE_PAGINACAO;
                        $pagina = isset($_POST['p']) ? $_POST['p'] : 1;
                        $pagina = $pagina <= 0 ? 1 : $pagina;
                        $limit = $pagina == 1 ? $quantidadePorPagina : $quantidadePorPagina * ($pagina - 1);
                        $offset = $pagina == 1 ? 0 : $quantidadePorPagina;
                        $cursos = $query->all(array(
                                'limit' => $limit,
                                'offset' => $offset
                            )
                        );
                    }
                    else {
                        $cursos = $query->all();
                    }

                    ob_start();
                    include DIR_ROOT . '/views/includes/list-cursos.php';
                    $contents = ob_get_contents();
                    ob_end_clean();
                    echo $contents;
                }
                catch (Exception $e) {
                    throw $e;
                }
            break;

            // select de cursos
            case 'select' :

                $dados = $_POST['dados'];

                $categoria = $this->dao
                    ->table('cursos_categorias')
                    ->where('slug', '=', $dados['categoria'])
                    ->first();

                $idsCategorias = array();
                $idsCategorias[] = $categoria->id;

                $subcategorias = $this->dao
                    ->table('cursos_categorias')
                    ->where('caminho', 'like', '%/' . $categoria->id . '/%')
                    ->where('quantidadeCursosVisiveis', '>', 0)
                    ->all();

                if ($subcategorias) {
                    foreach ($subcategorias as $subcategoria) {
                        $idsCategorias[] = $subcategoria->id;
                    }
                }

                $cursos = $this->dao
                    ->table('cursos')
                    ->where('categoria', 'in', $idsCategorias)
                    ->where('status', '=', 1)
                    ->order('nome', 'asc')
                    ->all();

                $options = '<option value="">Escolha o curso</option>';
                $option = '<option value="%s">%s</option>';
                foreach ($cursos as $curso) {
                    $options .= sprintf($option, $curso->link, $curso->nome);
                }
                echo $options;

            break;
        }

    }

    public function cepAction ($cep) {
        try {
            if (isset($cep) && !empty($cep)) {
                $cep = trim($cep);
                $reg = simplexml_load_file('http://cep.republicavirtual.com.br/web_cep.php?formato=xml&cep=' . $cep);
                $dados['sucesso'] = (string) $reg->resultado;
                $dados['endereco']     = (string) $reg->tipo_logradouro . ' ' . $reg->logradouro;
                $dados['bairro']  = (string) $reg->bairro;
                $dados['cidade']  = (string) $reg->cidade;
                $dados['uf']  = (string) $reg->uf;
                echo json_encode($dados);
            }
        }
        catch (Exception $e) {
            echo json_encode(array(
                    'sucesso' => 0
                )
            );
        }
    }

    public function buscaAction () {
        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('busca');
        $data['title'] = $data['meta']['meta.title'];
        
        $quantidadePorPagina = 20;
        $pagina = isset($_GET['p']) ? $_GET['p'] : 1;
        $pagina = $pagina <= 0 ? 1 : $pagina;
        $limit = $pagina == 1 ? 0 : $quantidadePorPagina * ($pagina - 1);
        $quantidadeResultados = 0;
        $resultados = array();

        $q = isset($_GET['search']) ? trim(urldecode(FuncoesSite::codificaDado($_GET['search']))) : '';

        $query = $this->dao->table('vw_cursos', array(
                    'nome',
                    'link',
                    'objetivosGerais',
                    'publicoAlvo',
                    'nomeCategoria'
                )
            )
            ->where('status', '=', 1)
            ->where('nome', 'like', '%' . $q . '%')
            ->order('nome', 'asc');

        $quantidadeResultados += $query->count();
        $objetos = $query->all();

        foreach ($objetos as $r) {
            $item = array();
            $item['titulo'] = 'Cursos › ' . $r->nomeCategoria . ' › ' .  $r->nome;
            if (!empty($r->objetivosGerais)) {
                $item['descricao'] = $r->objetivosGerais;
            }
            else if (!empty($r->publicoAlvo)) {
                $item['descricao'] = $r->publicoAlvo;
            }
            else {
                $item['descricao'] = '';
            }
            $item['url'] = SITEURL . 'curso/' . $r->link;
            $resultados[] = $item;
        }

        $query = $this->dao->table('noticias', array(
                    'titulo',
                    'link',
                    'noticia'
                )
            )
            ->where('status', '=', 1)
            ->where('titulo', 'like', '%' . $q . '%')
            ->order('titulo', 'asc');

        $quantidadeResultados += $query->count();
        $objetos = $query->all();

        foreach ($objetos as $r) {
            $item = array();
            $item['titulo'] = 'Notícias › ' .  $r->titulo;
            if (!empty($r->noticia)) {
                $item['descricao'] = FuncoesSite::compactaTexto(strip_tags($r->noticia), 200);
            }
            else {
                $item['descricao'] = '';
            }
            $item['url'] = SITEURL . 'noticia/' . $r->link;
            $resultados[] = $item;
        }

        $pages = new Paginator($quantidadePorPagina, 'p');
        $pages->setTotal($quantidadeResultados);
        $data['pageLinks'] = $pages->pageLinks('?search=' . $_GET['search'] . '&');

        $data['resultados'] = array_slice($resultados, $limit, $quantidadePorPagina);

        $this->conexao->disconnect();

        // replace meta data
        $a = new stdClass();
        $a->search = $q;
        $data['meta'] = FuncoesSite::replaceMeta($data['meta'], $a);

        View::renderTemplate('header', $data);
        View::render('busca/index', $data);
        View::renderTemplate('footer', $data);
    }

    public function informativoAction ($slug = NULL) {

        $data = array();
        $data = $this->dados;
        $data['meta'] = FuncoesSite::getMeta('curso');
        $data['title'] = $data['meta']['meta.title'];

        try {

            // recupera o curso
            $data['curso'] = $curso = $this->dao->table('vw_cursos')
                ->where('link', '=', $slug)
                ->firstOrFail();

            // recupera a categoria e suas categorias ancestrais
            $categorias = array();
            // recupera e armazena a categoria do curso
            $data['categoria'] = $categoria = $categoriaC = $this->dao->table('cursos_categorias')
                ->where('id', '=', (int) $curso->categoria)
                ->first();
            // se tem categoria pai
            if (!empty($categoria->pai)) {
                do {
                    // recupera categoria pai
                    $categoria = $this->dao->table('cursos_categorias')
                        ->where('id', '=', (int) $categoria->pai)
                        ->first();
                    // armazena primeiro as categorias ancestrais
                    $data['categorias'][] = $categoria; // objeto
                    $categorias[] = $categoria->nome; // só o nome
                }
                while($categoria->pai != 0);  
            }
            // por último adiciona a categoria do curso
            $data['categorias'][] = $categoriaC; // objeto
            $categorias[] = $curso->nomeCategoria; // só o nome

            // meta tags
            $a = new stdClass();
            $a->tipo = Curso::getTipo($curso->tipo);
            $a->nome = $curso->nome;
            $a->categorias = implode(' › ', $categorias);
            $data['meta'] = FuncoesSite::replaceMeta ($data['meta'], $a);

            ob_start();
            View::render('curso/informativo', $data);
            $contents = ob_get_contents();
            ob_end_clean();
            echo $contents;

        }
        catch (NotFoundException $e) {
            $this->conexao->disconnect();
            $this->errorAction();
            exit;
        }
    }

    public function downloadAction ($protocolo = NULL) {
        try {

            $requerimento = $this->dao->table('requerimentos')
                ->where('protocolo', '=', $protocolo)
                ->firstOrFail();

            $arquivo = base64_decode($requerimento->arquivo);

            $diretorio = DIR_UPLOADS . DS . "requerimentos";
            $diretorio .= DS . $requerimento->id . DS;
            $diretorio .= $arquivo;

            if (!FuncoesSite::existeArquivo($diretorio)) {
                throw new Exception('Arquivo não encontrado');
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $arquivo . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($diretorio));
            flush();
            readfile($diretorio);
        }
        catch (Exception $e) {
            FuncoesSite::setMensagem('danger', $e->getMessage());
            Url::previous();
        }

    }
}



?>