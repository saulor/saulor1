<!-- Slider -->
<section id="slider" class="hidden-xs">

  <div id="loader-container" class="row-centered">
    <div id="loader-wrap" class="col-centered">
        <img src="<?php echo Url::templatePath(); ?>images/gears-big.gif" />
    </div>
  </div>

  <?php
  $founds = array();
  foreach ($data['banners'] as $banner) {
    $file = APPDIR . 'assets' . DS . 'banners' . DS . $banner->banner;
    if (is_file($banner)) {
      $founds[] = array(
        'link' => $banner->link,
        'descricao' => $banner->descricao,
        'src' => Url::resourcePath() . 'uploads' . DS . 'banners' . DS . $banner->banner
      );
    }
  }

  ?>

  <div id="banner-wrap" class="hidden">
    <ul class="pgwSlider">

        <?php if (!$founds) { ?>
        <li>
          <img title="Central de Matrículas IEFAP" 
            src="<?php echo Url::resourcePath(); ?>uploads/banners/default.png" />
        </li>
        <?php } else { ?>

          <?php foreach ($founds as $f) { ?>
          <li>
            <?php if (!empty($f['link'])) { ?>
            <a href="<?php echo $f['link']; ?>">
            <?php } ?>
              <img
                title="<?php echo $f['descricao']; ?>" 
                src="<?php echo $f['src']; ?>" />
            <?php if (!empty($f['link'])) { ?>
            </a>
            <?php } ?>
          </li>
          <?php } ?>

        <?php } ?>
    </ul>
  </div>

</section>
<!-- Slider -->

<!-- Destaques -->
<section id="destaques">
  <div class="container-fluid">
      <div class="row">
        <div id="destaques-abas" class="hidden-xs">
          <div class="col-lg-12">
            <div class="center-container">
              <div id="diferencial" class="absolute-center">
                <img src="<?php echo Url::templatePath(); ?>images/diferencial.png" />
              </div>
            </div>
          </div>
          <div class="col-lg-12">
            <div class="heading heading1 text-center">
              <h1 class="text-uppercase">Destaques</h1>
              <span class="bottomline"></span>
            </div>
          </div>
          <div class="col-lg-12">
            <ul>
              <li class="active"><a href="<?php echo SITEURL; ?>posgraduacao">Pós-Graduação</a></li>
              <li><a href="<?php echo SITEURL; ?>aperfeicoamento-profissional">Aperfeiçoamento Profissional</a></li>
              <li><a target="_blank" href="http://www.eadlaureate.com.br">EAD</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row">
      <!-- Destaques cursos -->
      <div class="destaques-cursos-wrap">
        <!-- Navegação dos cursos -->
        <div class="navegacao-wrap">
          <div data-pagina="1" data-fim="<?php echo $data['quantidadePaginas']; ?>" 
            data-tipo="<?php echo Curso::CURSO_TIPO_POS; ?>"
            aria-label="Navegação dos cursos" role="toolbar" 
            class="navegacao cursos-paginacao btn-toolbar pull-right">
            <div class="btn-group">
              <img class="rolling hidden" src="<?php echo Url::templatePath(); ?>images/rolling.gif" />
            </div>
            <div aria-label="First group" role="group" class="btn-group">
              <button class="disabled btn btn-default btn-page prev" type="button"><</button>
            </div>
            <div aria-label="Second group" role="group" class="btn-group">
              <button class="btn btn-default btn-page next" type="button">></button>
            </div>
            <div aria-label="Third group" role="group" class="btn-group">
              <button class="btn btn-default btn-all" type="button">Ver todos</button>
            </div>
          </div>
        </div>
        <br />
        <!-- Navegação dos cursos -->

        <!-- Preview Cursos -->
        <div id="preview-cursos-container">
          <?php 
            $cursos = $data['cursos'];
            include('views/includes/list-cursos.php'); 
          ?>
        </div>
        <!-- Preview Cursos -->

      </div>
    </div>
  </div>
</section>
<!-- Destaques -->

<!-- Notícias -->
<section>
  <div id="noticias-wrap">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="heading heading1 text-center">
              <h1 class="text-uppercase">Notícias</h1>
              <span class="bottomline"></span>
            </div>
        </div>
      </div>
      
      <!-- mais notícias -->
      <div class="row mais-noticias">
        <div class="col-lg-12">
          <a href="<?php echo SITEURL; ?>noticias">
            <button type="button" class="btn pull-right text-uppercase">Mais Notícias</button>
          </a>
        </div>
      </div>
      <!-- mais notícias -->

      <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12">

          <?php
            $temImagem = false;
            if (!empty($data['noticias'][0]->imagem)) {
              $temImagem = true;
            }
          ?>

          <?php if ($temImagem) { ?>
          <article class="noticia-preview with-image">
            <div class="col-lg-5 col-md-5 col-sm-4 hidden-xs">
              <div class="news-image hidden-xs">
                <time pubdate="" datetime="2010-12-08">
                  <span class="dia"><?php echo date('d', $data['noticias'][0]->timestamp); ?></span>
                  <span class="center-block bottomline"></span>
                  <span class="mes"><?php echo Funcoes::mesNoticia($data['noticias'][0]->timestamp); ?></span>
                </time>
                <img class="img-responsive img-rounded" 
                  src="<?php echo SITEURL2; ?>uploads/noticias/<?php echo $data['noticias'][0]->id; ?>/<?php echo $data['noticias'][0]->imagem; ?>" />
              </div>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-8 col-xs-12">
              <header class="clearfix">
                  <time class="visible-xs" pubdate="" datetime="2010-12-08">
                    <span class="dia">08</span>
                    <span class="center-block bottomline"></span>
                    <span class="mes"><?php echo Funcoes::mesNoticia($data['noticias'][0]->timestamp); ?></span>
                  </time>
                  <h2>
                    <a href="<?php echo SITEURL; ?>noticia/<?php echo $data['noticias'][0]->link;?>"><?php echo $data['noticias'][0]->titulo; ?></a>
                  </h2>
              </header>
              <div class="news-entry">
                <?php echo strip_tags(Funcoes::compactaTexto($data['noticias'][0]->noticia, 820)); ?>
              </div>
              <p class="leia-mais">
                <a class="text-uppercase" href="<?php echo SITEURL; ?>noticia/<?php echo $data['noticias'][0]->link;?>">Leia Mais</a>
              </p>
            </div>
          </article>
          <?php } else { ?>
          <article class="noticia-preview<?php if ($temImagem) echo ' with-image';?>">
            <div class="col-xs-12">
              <header class="clearfix">
                  <time pubdate="" datetime="2010-12-08">
                    <span class="dia"><?php echo date('d', $data['noticias'][0]->timestamp); ?></span>
                    <span class="center-block bottomline"></span>
                    <span class="mes"><?php echo Funcoes::mesNoticia($data['noticias'][0]->timestamp); ?></span>
                  </time>
                  <h2>
                    <a href="<?php echo SITEURL; ?>noticia/<?php echo $data['noticias'][0]->link;?>"><?php echo $data['noticias'][0]->titulo; ?></a>
                  </h2>
              </header>
              <div class="hidden-xs news-entry">
                <?php 
                    echo Funcoes::compactaTexto($data['noticias'][0]->noticia, 1600);
                ?>
              </div>
              <div class="visible-xs news-entry">
                <?php 
                    echo Funcoes::compactaTexto($data['noticias'][0]->noticia, 230);
                ?>
              </div>
              <p class="leia-mais">
                <a class="text-uppercase" href="<?php echo SITEURL; ?>noticia/<?php echo $data['noticias'][0]->link;?>">Leia Mais</a>
              </p>
            </div>
          </article>
          <?php } ?>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-6">
              <article class="noticia-preview">
                <header class="clearfix">
                    <time pubdate="" datetime="2010-12-08">
                      <span class="dia"><?php echo date('d', $data['noticias'][1]->timestamp); ?></span>
                      <span class="center-block bottomline"></span>
                      <span class="mes"><?php echo Funcoes::mesNoticia($data['noticias'][1]->timestamp); ?></span>
                    </time>
                    <h2>
                      <a href="<?php echo SITEURL; ?>noticia/<?php echo $data['noticias'][1]->link;?>"><?php echo $data['noticias'][1]->titulo; ?></a>
                    </h2>
                </header>
                <div class="news-entry">
                  <?php 
                    echo Funcoes::compactaTexto($data['noticias'][1]->noticia, 230);
                  ?>
                </div>
                <p class="leia-mais">
                  <a class="text-uppercase" href="<?php echo SITEURL; ?>noticia/<?php echo $data['noticias'][1]->link;?>">Leia Mais</a>
                </p>
              </article>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-6">
              <article class="noticia-preview">
                <header class="clearfix">
                    <time pubdate="" datetime="2010-12-08">
                      <span class="dia"><?php echo date('d', $data['noticias'][2]->timestamp); ?></span>
                      <span class="center-block bottomline"></span>
                      <span class="mes"><?php echo Funcoes::mesNoticia($data['noticias'][2]->timestamp); ?></span>
                    </time>
                    <h2>
                      <a href="<?php echo SITEURL; ?>noticia/<?php echo $data['noticias'][2]->link;?>"><?php echo $data['noticias'][2]->titulo; ?></a>
                    </h2>
                </header>
                <div class="news-entry">
                  <?php 
                    echo Funcoes::compactaTexto($data['noticias'][2]->noticia, 230);
                  ?>
                </div>
                <p class="leia-mais">
                  <a class="text-uppercase" href="<?php echo SITEURL; ?>noticia/<?php echo $data['noticias'][2]->link;?>">Leia Mais</a>
                </p>
              </article>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Notícias -->

<!-- Avaliações do Alunos -->
<section id="avaliacoes" class="hidden-xs">
  <div id="avaliacoes-wrap" class="grafismo">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <div class="heading heading1 text-center">
            <h1 class="text-uppercase">Avaliações dos Alunos</h1>
            <span class="bottomline"></span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-button">
          <button type="button" id="prev" class="btn btn-default pull-right">
              <span aria-hidden="true">&laquo;</span>
          </button>
        </div>
        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 center-block">
          <div class="row row-centered">
            <div id="avaliacoes-fotos-wrap">
              <div id="avaliacao-1" class="col-lg-2 col-md-2 col-sm-2 col-centered">
                <div class="avaliacao-foto">
                  <img class="img-circle img-responsive" 
                    src="<?php echo Url::templatePath(); ?>images/avaliacoes/avaliacao1.png">
                </div>
              </div>
              <div id="avaliacao-2" class="col-lg-2 col-md-2 col-sm-2 col-centered">
                <div class="avaliacao-foto">
                  <img class="img-circle img-responsive" 
                    src="<?php echo Url::templatePath(); ?>images/avaliacoes/avaliacao2.jpg">
                </div>
              </div>
              <div id="avaliacao-3" class="col-lg-3 col-md-3 col-sm-3 col-centered active">
                <div class="avaliacao-foto">
                  <img class="img-circle img-responsive" 
                    src="<?php echo Url::templatePath(); ?>images/avaliacoes/avaliacao3.jpg">
                </div>
              </div>
              <div id="avaliacao-4" class="col-lg-2 col-md-2 col-sm-2 col-centered">
                <div class="avaliacao-foto">
                  <img class="img-circle img-responsive" 
                    src="<?php echo Url::templatePath(); ?>images/avaliacoes/avaliacao2.png">
                </div>
              </div>
              <div id="avaliacao-5" class="col-lg-2 col-md-2 col-sm-2 col-centered">
                <div class="avaliacao-foto">
                  <img class="img-circle img-responsive" 
                    src="<?php echo Url::templatePath(); ?>images/avaliacoes/avaliacao5.jpeg">
                </div>
              </div>
            </div>
          </div>
          <div class="row row-centered">
            <div id="avaliacoes-infos-wrap">
              <div class="avaliacao-info text-center hidden" id="avaliacao-info-1">
                <p>Prof. Fernando Pratti</p>
                <p>Sendo um colaborador do IEFAP sinto orgulho dessa 
                  instituição que tanto colabora com o aprimoramento profissional de tantas pessoas, 
                  que mais qualificadas, encontram um maior resultado de seus investimentos no 
                  mercado de trabalho.</p>
              </div>
              <div class="avaliacao-info text-center hidden" id="avaliacao-info-2">
                <p>Adriana Covatti Luza Reichert</p>
                <p>Equipe sempre solícita, excelentes professores e aulas marcadas e confirmadas 
                 com antecedência. Gostei tanto da Instituição que estou inscrita para nova 
                 pós-graduação.</p>
              </div>
              <div class="avaliacao-info text-center" id="avaliacao-info-3">
                <p>Adriana Ferreira Barbato Larios</p>
                <p>Aqui você aprende com profissionais altamente capacitados, com experiência e credibilidade.
                  Ser um profissional de enfermagem em oncologia requer um conhecimento direcionado que 
                  o IEFAP conseguiu nos proporcionar.</p>
              </div>
              <div class="avaliacao-info text-center hidden" id="avaliacao-info-4">
                <p>Tatiana Botelho</p>
                <p>Trocar experiências e viver novos desafios são escolhas acertadas para o sucesso 
                  profissional! E escolher o IEFAP como parceiro nessa caminhada, com a oportunidade 
                  de aulas práticas e teóricas, com profissionais para nos orientar, tem sido muito 
                  estimulante!</p>
              </div>
              <div class="avaliacao-info text-center hidden" id="avaliacao-info-5">
                <p>Dra. Carla Paschoal</p>
                <p>Gostaria de parabenizar o curso de Reumatologia oferecido pelo IEFAP, em especial 
                  ao excelente professor Dr. Leandro Lara Prado. Muito didático e competente. Mais 
                  uma vez parabéns ao IEFAP! Sinto muito orgulho de ter estudado nesta instituição.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-button">
          <button type="button" id="next" class="btn btn-default pull-left">
            <span aria-hidden="true">&raquo;</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Avaliações Alunos -->

<!-- Principal -->
<section>
  <div id="principal-wrap" class="wrap-section">
    <div class="container">
      <div class="row">
        <!-- Sobre o IEFAP -->
        <section>
          <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="heading heading2">
              <h1 class="text-uppercase">Sobre o IEFAP</h1>
              <span class="bottomline"></span>
            </div>
            <p>O IEFAP – Instituto de Ensino, Formação e Aperfeiçoamento LTDA. é uma Instituição cujo objetivo é 
              a oferta e operacionalização administrativa de cursos de Pós-Graduação e cursos de Aperfeiçoamento 
              Profissional, nas diferentes áreas do conhecimento. Ele nasceu do sonho de algumas pessoas em 
              transformar a sociedade, através da educação. O conhecimento traz a inserção e a ascendência das 
              pessoas no meio em que estão inseridos. <a href="<?php echo SITEURL?>institucional">leia mais</a></p>
          </div>
        </section>
        <!-- Sobre o IEFAP -->
        <!-- Pré-inscrição -->
        <section>
          <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <form method="GET" role="form" action="cursos">
              <div class="heading heading2">
                <h1 class="text-uppercase">Pré-inscrição</h1>
                <span class="bottomline"></span>
                <h2 class="text-uppercase">Invista em você</h2>
              </div>
              <div class="form-group">
                <select name="estado" class="form-control load-cursos">
                  <option value="" selected>Selecione seu Estado</option>
                  <option value="AC">AC</option>
                  <option value="AL">AL</option>
                  <option value="AP">AP</option>
                  <option value="AM">AM</option>
                  <option value="BA">BA</option>
                  <option value="CE">CE</option>
                  <option value="DF">DF</option>
                  <option value="ES">ES</option>
                  <option value="GO">GO</option>
                  <option value="MA">MA</option>
                  <option value="MT">MT</option>
                  <option value="MS">MS</option>
                  <option value="MG">MG</option>
                  <option value="PA">PA</option>
                  <option value="PB">PB</option>
                  <option value="PR">PR</option>
                  <option value="PE">PE</option>
                  <option value="PI">PI</option>
                  <option value="RJ">RJ</option>
                  <option value="RN">RN</option>
                  <option value="RS">RS</option>
                  <option value="RO">RO</option>
                  <option value="RR">RR</option>
                  <option value="SC">SC</option>
                  <option value="SP">SP</option>
                  <option value="SE">SE</option>
                  <option value="TO">TO</option>
                </select>
              </div>
              <div class="form-group">
                <select name="categoria" class="form-control load-cursos">
                  <option value="" selected>Escolha a Categoria</option>
                  <?php foreach($data['categorias'] as $c) { ?>
                    <option value="<?php echo $c->slug; ?>"><?php echo $c->nome; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <select name="curso" id="select-cursos" class="form-control">
                  <option value="" selected>Escolha o Curso</option>
                  <?php foreach ($data['todosCursos'] as $curso) { ?>
                  <option value="<?php echo $curso->link; ?>"><?php echo $curso->nome; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <input type="submit" class="btn btn-orange" value="Buscar"/>
              </div>
            </form>
          </div>
        </section>
        <!-- Pré-inscrição -->
        <!-- Facebook -->
        <section>
          <div class="col-lg-4 col-md-4 visible-md visible-lg">
            <div id="facebook-widget-wrap">
              <div class="fb-like-box"
                data-href="https://www.facebook.com/pages/IEFAP-P%C3%B3s-Gradua%C3%A7%C3%A3o/291095547586274?fref=ts"
                data-width="300" data-height="300" data-colorscheme="light" data-show-faces="true" data-header="true" data-stream="false" data-show-border="true"></div>
              </div>
          </div>
        </section>
        <!-- Facebook -->
      </div>
    </div>
  </div>
</section>
<!-- Principal -->

<section>
  <div class="container">
    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-6 hidden-xs">
        <div class="row">
          <div class="col-xs-12">
            <div class="heading heading3">
              <h1 class="text-uppercase">Instituições de Ensino Superior</h1>
              <span class="bottomline"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-3 col-md-3 col-sm-4">
            <a title="Pós-FIP" target="_blank" href="http://www.fiponline.edu.br">
              <img class="img-responsive" src="<?php echo Url::templatePath(); ?>images/ies/fip.png" 
                title="Faculdades Integradas de Patos" 
                alt="Logomarca FIP"/>
            </a>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-4">
            <a title="Uninassau" target="_blank" href="http://www.uninassau.edu.br">
              <img class="img-responsive" 
                src="<?php echo Url::templatePath(); ?>images/ies/uninassau.jpg" 
                title="Centro Universitário Maurício de Nassau" 
                alt="Logomarca Uninassau"/>
            </a>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-4">
            <a title="Universidade Anhembi Morumbi" target="_blank" href="http://portal.anhembi.br">
              <img class="img-responsive" 
                src="<?php echo Url::templatePath(); ?>images/ies/anhembi.png" 
                title="Universidade Anhembi Morumbi" 
                alt="Logomarca Anhembi Morumbi"/>
            </a>
          </div>
          <div class="col-lg-3 col-md-3 hidden-sm">
            <a title="Faculdades Metropolitanas Unidas" target="_blank" href="http://portal.fmu.br">
              <img class="img-responsive" 
                src="<?php echo Url::templatePath(); ?>images/ies/fmu.jpg" 
                title="Faculdades Metropolitanas Unidas" 
                alt="Logomarca FMU"/>
            </a>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12">
            <p class="pull-right">
              <a title="Clique para ver mais instituições" 
                class="small" href="<?php echo SITEURL; ?>institucional/ies">Mais instituições...</a>
            </p>
            <div class="clearfix"></div>
            <p><small class="text-info">Todos os cursos são ministrados pelas instituições acima.<br />
          O IEFAP somente realiza a gestão administrativa e operacional dos cursos.</small></p>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6 hidden-xs">
        <div class="row">
          <div class="col-xs-12">
            <div class="heading heading3">
              <h1 class="text-uppercase">Links Interessantes</h1>
              <span class="bottomline"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-3 col-md-3 col-sm-4">
            <a title="Ministério da Educação" target="_blank" href="http://portal.mec.gov.br">
              <img class="img-responsive" 
                src="<?php echo Url::templatePath(); ?>images/links/mec.jpg" 
                title="Ministério da Educação" 
                alt="Logomarca Mec"/>
            </a>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-4">
            <a title="Educa Mais Brasil" target="_blank" href="http://www.educamaisbrasil.com.br">
              <img class="img-responsive" 
                src="<?php echo Url::templatePath(); ?>images/links/educa-mais.png" 
                title="Educa mais Brasil" 
                alt="Logomarca Educa mais Brasil"/>
            </a>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-4">
            <a title="Portal Periódicos Capes" target="_blank" href="http://www.periodicos.capes.gov.br">
              <img class="img-responsive" 
                src="<?php echo Url::templatePath(); ?>images/links/periodicos.png" 
                title="Portal Periódicos Capes" 
                alt="Logomarca Portal Periódicos Capes"/>
            </a>
          </div>
          <div class="col-lg-3 col-md-3 hidden-sm">
            <a title="Portal Domínio Público" target="_blank" href="http://www.dominiopublico.gov.br/">
              <img class="img-responsive" 
                src="<?php echo Url::templatePath(); ?>images/links/dominio-publico.png" 
                title="Portal Domínio Público" 
                alt="Logomarca Portal Domínio Público"/>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Parceiros -->
<section class="hidden-xs">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="heading heading3">
          <h1 class="text-uppercase">Nossos Parceiros</h1>
          <span class="bottomline"></span>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
          <img class="img-responsive" src="<?php echo Url::templatePath(); ?>images/parceiros/crfpa.jpg" 
            title="Conselho Regional de Farmácia do Pará" alt="Conselho Regional de Farmácia do Pará"/>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
          <img class="img-responsive" src="<?php echo Url::templatePath(); ?>images/parceiros/unimed-cascavel.jpg" 
            title="Unimed Cascavel" alt="Logomarca Unimed Cascavel"/>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
          <img class="img-responsive" src="<?php echo Url::templatePath(); ?>images/parceiros/solucaoead.jpg" 
            title="Solução EAD" alt="Logomarca Solução EAD"/>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
          <img class="img-responsive" src="<?php echo Url::templatePath(); ?>images/parceiros/drlima.jpg" 
            title="Hospital e Maternidade Dr. Lima" alt="Logomarca Hospital e Maternidade Dr. Lima"/>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
          <img class="img-responsive" src="<?php echo Url::templatePath(); ?>images/parceiros/lider.jpg" 
            title="Grupo Líder" alt="Logomarca Grupo Líder"/>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
          <img class="img-responsive" src="<?php echo Url::templatePath(); ?>images/parceiros/hevoise-papini.jpg"
            title="Hevoise F Papini - Nutrição e Educação" 
            alt="Logomarca Hevoise F Papini - Nutrição e Educação"/>
        </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <br />
        <a title="Clique para ver mais parceiros" 
          class="small pull-right" href="<?php echo SITEURL; ?>institucional/parceiros">Mais parceiros...</a>
      </div>
    </div>
  </div>
</section>
<!-- Parceiros -->