<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/aperfeicoamento-profissional.png" />
  </div>
</section>
<!-- Banner -->

<!-- Aperfeicoamento -->
<section id="aperfeicoamento">
  <div class="wrap-section">
    <div class="container">
      <div class="row">
        <div class="navegacao-wrap hidden-xs">
          <div class="col-lg-12">
            <ol class="breadcrumb">
              <li><a href="<?php echo SITEURL; ?>">Início</a></li>
              <li><?php if(!empty($data['categoria'])) { ?><a href="<?php echo SITEURL; ?>aperfeicoamento-profissional"><?php } ?>Aperfeiçoamento Profissional<?php if(!empty($data['categoria'])) { ?></a><?php } ?></li>
              <?php if(!empty($data['categoria'])) { ?><li class="active"><?php echo $data['categoria']->nome; ?></li><?php } ?></ol>
          </div>
        </div>
      </div>

      <br />

      <div class="row">

        <?php if ($data['quantidadeCursos'] > 12) { ?>

        <!-- Navegação dos cursos -->
        <div class="navegacao-wrap">
          <div data-pagina="1" data-fim="<?php echo $data['quantidadePaginas']; ?>" 
            data-tipo="<?php echo Curso::CURSO_TIPO_APERFEICOAMENTO; ?>"
            data-categoria="<?php echo $data['categoria']->id; ?>"
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

        <?php } ?>

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
<!-- Aperfeicoamento  -->