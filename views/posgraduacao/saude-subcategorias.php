<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <?php 
    $file = DIR_UPLOADS . DS . 'categorias' . DS . $data['categoria']->id . DS . $data['categoria']->banner;
    if (is_file($file)) { 
    ?>
    <img alt="" title="" data-description="" src="<?php echo Url::resourcePath(); ?>uploads/categorias/<?php echo $data['categoria']->id;?>/<?php echo $data['categoria']->banner; ?>" />
    <?php } else { ?>
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/banner-default.jpg" />
    <?php } ?>
  </div>
</section>
<!-- Banner -->

<!-- Curso apresentação/chamada -->
<section id="curso-apresentacao">
  <div class="wrap-section">
    <div class="container">
      <div class="row">
        <div class="navegacao-wrap hidden-xs">
          <div class="col-lg-12">
            <ol class="breadcrumb">
              <li><a href="<?php echo SITEURL; ?>">Início</a></li>
              <li><a href="<?php echo SITEURL; ?>posgraduacao">Pós-Graduação</a></li>
              <li class="active">Saúde</li>
            </ol>
          </div>
        </div>
      </div>

      <br />

      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <div class="subcategorias-apresentacao grafismo">
            <p>Escolha sua área de atuação</p>
          </div>
        </div>
      </div>

      <br /><br />

      <div class="row">
      	<?php foreach($data['subcategorias'] as $key => $categoria) { ?>
          <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
            <div class="row">
              <article class="subcategoria">
                <div class="over-layer imagem">
                  <img class="img-responsive" 
                    src="<?php echo Url::resourcePath(); ?>/uploads/categorias/<?php echo $categoria->id; ?>/<?php echo $categoria->imagem; ?>" />
                  <div class="button-layer">
                    <a href="<?php echo SITEURL; ?>posgraduacao/saude/<?php echo $categoria->slug; ?>" 
                      class="btn btn-orange text-uppercase" role="button"><?php echo $categoria->nome; ?></a>
                  </div>
                </div>
                <h2><?php echo $categoria->nome; ?></h2>
              </article>
            </div>
          </div>
          <?php

          if ($key%1==0) {
            echo '<div class="clearfix visible-xs-block"></div>';
          } 

          if (($key+1)%3==0) {
            echo '<div class="clearfix visible-sm-block"></div>';
          } 

          if (($key+1)%4==0) {
            echo '<div class="clearfix visible-md-block"></div>';
            echo '<div class="clearfix visible-lg-block"></div>';
          } 
                } 
          ?>
      </div>

    </div>
  </div>
</section>
<!-- Curso apresentação/chamada  -->