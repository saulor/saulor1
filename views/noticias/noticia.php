<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/noticias.png" />
  </div>
</section>
<!-- Banner -->

<!-- Notícia -->
<section id="noticia">
  <div class="wrap-section">
    <div class="container">
      <div class="row">
        <div class="navegacao-wrap hidden-xs">
          <div class="col-lg-12">
            <ol class="breadcrumb">
              <li><a href="<?php echo SITEURL; ?>">Início</a></li>
              <li><a href="<?php echo SITEURL;?>noticias">Notícias</a></li>
              <li class="active"><?php echo $data['noticia']->titulo; ?></li>
            </ol>
          </div>
        </div>
      </div>
      <br /><br />
      <div class="row">
        <article id="noticia-<?php echo $data['noticia']->id; ?>" class="noticia interna">
          <?php $col2 = 12; if (false) { $col1 = 4; $col2 = 8; ?>
          <div class="col-lg-<?php echo $col1; ?> col-md-<?php echo $col1; ?> col-sm-<?php echo $col1; ?> hidden-xs">
            <img class="img-responsive" src="<?php echo Url::templatePath(); ?>images/cursos/curso-chamada.jpg" />
          </div>
          <?php } ?>
          <div class="col-lg-<?php echo $col2; ?> col-md-<?php echo $col2; ?> col-sm-<?php echo $col2; ?> col-xs-<?php echo $col2; ?>">
            <header>
                <hgroup>
                  <h1><?php echo $data['noticia']->titulo; ?></h1>
                  <span class="bottomline h1"></span><br />
                  <small>
                    <time datetime="<?php echo date('Y-m-d', $data['noticia']->timestamp); ?>" pubdate><?php echo $data['noticia']->data; ?></time>
                  </small>
                </hgroup>
            </header>
            <div class="clearfix">&nbsp;</div>

            <?php if (!empty($data['noticia']->imagem)) { ?>
            <div class="col-xs-3 pull-left">
              <img class="img-responsive" 
                src="<?php echo SITEURL2 . 'uploads/noticias/' . $data['noticia']->id . '/' . $data['noticia']->imagem; ?>" />
            </div>
            <?php } ?>

            <?php echo htmlspecialchars_decode($data['noticia']->noticia); ?>

            <?php if (!empty($data["noticia"]->tags)) { ?>
            <br />
            <div id="marcadores" class="clearfix">
              <ul class="semantic-list">
                <li><h4>Marcadores:</h4></li>
                <?php
                  $marcadores = explode(",", $data["noticia"]->tags);
                  $tags = array();
                  foreach ($marcadores as $marcador) {
                    $marcador = trim($marcador);
                    if (!empty($marcador)) {
                      $tags[] = '<li><a href="'.SITEURL.'busca?search='.$marcador.'">'.$marcador.'</a></li>';
                    }
                  }
                  echo implode("", $tags);
                ?>
              </ul>
            </div>
            <br />
          <?php } ?>

          <div id="social" class="clearfix">
            <h4>
              <img src="<?php echo Url::templatePath(); ?>images/logo-mini.png" 
                alt="" /> Compartilhe!
            </h4>
            <br />
            <div id="facebook-like">
                  <div class="fb-like"
                    data-href="<?php echo SITEURL . 'noticia/' . $data["noticia"]->link; ?>"
                    data-layout="standard"
                    data-action="like"
                    data-show-faces="false"
                    data-share="false">
                  </div>
                </div>
            <div id="facebook-share">
              <div class="fb-share-button"
                data-href="<?php echo SITEURL . 'noticia/' . $data["noticia"]->link; ?>"
                data-layout="button">
              </div>
            </div>
            <div id="twitter">
                <a href="https://twitter.com/share" class="twitter-share-button"
                  data-url="<?php echo SITEURL . 'noticia/' . $data["noticia"]->link; ?>" data-lang="pt"
                  data-hashtags="<?php echo $data["tags"]; ?>">Tweetar</a>
            </div>
            <!--div id="email">
                <a href="" class="btnStyle" rel="modal" data-id="modalEnviar"
                  title="Envie esta notícia por e-mail">
                  <i class="icon16 white letter"></i>
                  Enviar por e-mail</a>
            </div-->
          </div>

          </div>
        </artice>
      </div>
    </div>
  </div>
</section>
<!-- Notícia  -->

<?php 
  if ($data['outros']) {
?>
<section id="leia-tambem">
  <div class="wrap-section">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="heading heading2">
            <h1 class="text-uppercase">Leia também</h1>
            <span class="bottomline"></span>
          </div>
        </div>
      </div>

      <div class="row">
        <?php foreach ($data['outros'] as $key => $noticia) { ?>
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
    </div>
  </div>
</section>
<?php
  }
?>