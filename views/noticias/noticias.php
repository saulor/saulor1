<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/noticias.png" />
  </div>
</section>
<!-- Banner -->

<!-- Notícias -->
<section id="noticias">
  <div class="wrap-section">
    <div class="container">
      
      <div class="row no-gutter">
        <div class="col-lg-12">
          <div class="navegacao-wrap hidden-xs">
            <ol class="breadcrumb">
              <li><a href="<?php echo SITEURL; ?>">Início</a></li>
              <li class="active">Notícias</li>
            </ol>
          </div>
        </div>
      </div> 

      <div class="row">
          <div class="col-lg-12">
           <div class="heading internas">
              <h1>Notícias</h1>
            </div>
          </div>
      </div>

      <?php echo $data['pageLinks']; ?>

      <div class="row">
        <?php foreach ($data['noticias'] as $key => $noticia) { ?>
          <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
            <article class="preview noticia" id="noticia-<?php echo $noticia->id; ?>">
              <time class="" datetime="<?php echo date('d/m/Y', $noticia->timestamp); ?>" pubdate><?php echo date('d/m/Y', $noticia->timestamp); ?></time>
              <h1 class="h4">
                <a href="<?php echo WWW_ROOT; ?>/noticia/<?php echo $noticia->link; ?>"<?php if(strlen($noticia->titulo) > 50) echo ' title="' . $noticia->titulo . '"'; ?>>
                <?php
                    echo $titulo = Funcoes::compactaTexto($noticia->titulo, 47);
                ?>
                </a>
              </h1>
              <p class="visible-lg visible-md">
                <?php
                    echo Funcoes::compactaTexto($noticia->noticia, 77);
                ?>
              </p>
              <p class="visible-sm">
                <?php
                    echo Funcoes::compactaTexto($noticia->noticia, 67);
                ?>
              </p>
              <p class="visible-xs">
                <?php
                    echo Funcoes::compactaTexto($noticia->noticia, 57);
                ?>
              </p>
              <a href="<?php echo WWW_ROOT; ?>/noticia/<?php echo $noticia->link; ?>">saiba mais</a>
            </article>
          </div>
        <?php 
            if (($key+1)%2==0) {
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

      <?php echo $data['pageLinks']; ?>
    </div>
  </div>
</section>
<!-- Notícias -->