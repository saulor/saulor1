<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $data['meta']['meta.title']; ?></title>
    <!-- CSS -->
<?php
View::css([
    '//cdnjs.cloudflare.com/ajax/libs/normalize/4.1.1/normalize.min.css',
    '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css',
     Url::templatePath() . 'css/style.css',
]);
?>
    <style>
      body,
      header h1,
      header h2,
      .heading h1,
      .breadcrumb  {
        font-family: Tahoma, Geneva, sans-serif !important;
      }
    </style>
    <!-- CSS -->
<body>

  <br />
  <?php 
    $file = DIR_UPLOADS . DS . 'categorias';
    $path = Url::templatePath() . 'images/banners/banner-default.jpg';
    foreach (array_reverse($data['categorias']) as $categoria) {
      $bannerCategoria = $file . DS . $categoria->id . DS . $categoria->banner;
      if (is_file($bannerCategoria)) {
        $file = $bannerCategoria;
        $path = Url::resourcePath() . DS . 'uploads' . DS . 'categorias' . DS . 
          $categoria->id . DS . $categoria->banner;
        break;
      }
    }
  ?>

  <!-- Banner -->
  <section id="banner" class="hidden-xs">
    <div id="banner-wrap">
      <img alt="" title="" data-description="" src="<?php echo $path; ?>" />
    </div>
  </section>
  <!-- Banner -->

  <!-- Curso -->
  <section id="curso">
    <div class="wrap-section">
      <div class="container">
        <div class="row">
          <div class="navegacao-wrap">
            <div class="col-lg-12">
              <ol class="breadcrumb">
                <li><a href="<?php echo SITEURL; ?>">Início</a></li>
                <li><a href="<?php echo SITEURL;?>posgraduacao"><?php echo Curso::getTipo($data['curso']->tipo); ?></a></li>
                <?php 
                $url = SITEURL . 'posgraduacao';
                foreach ($data['categorias'] as $categoria) { 
                  $url .= '/' . $categoria->slug;
                  echo '<li class="hidden-xs"><a href="' . $url . '">' . $categoria->nome . '</a></li>';
                } 
                ?>
                <li class="active"><?php echo $data['curso']->nome; ?></li>
              </ol>
            </div>
          </div>
        </div>

        <br class="hidden-xs" /><br class="hidden-sm" />

        <div class="row">
          <article id="curso-<?php echo $data['curso']->id; ?>" class="interna">
            <div class="col-lg-4 col-md-4 col-sm-4 hidden-xs">
              <?php 
                $file = DIR_UPLOADS . DS . 'cursos' . DS . $data['curso']->id . DS . $data['curso']->thumbnail;
                if (is_file($file)) {
                  $path = Url::resourcePath() . 'uploads' . DS . 'cursos' . DS . $data['curso']->id . DS . $data['curso']->thumbnail;
                } else { 
                  // imagem default
                  $path = Url::resourcePath() . 'uploads' . DS . 'cursos' . DS . $data['curso']->id . DS . $data['curso']->thumbnail;
                } 
                echo sprintf('<img class="img-responsive" src="%s" />', $path);
              ?>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
              <header>
                  <hgroup>
                    <h2><?php echo $data['curso']->nomeCategoria; ?></h2>
                    <span class="bottomline h2"></span>
                    <h1><strong><?php echo $data['curso']->nome; ?></strong></h1>
                    <span class="bottomline h1"></span>
                  </hgroup>
              </header>
              <div class="clearfix">&nbsp;</div>
              <?php echo htmlspecialchars_decode($data['curso']->descricao); ?>
            </div>
          </artice>
        </div>
      </div>
    </div>
  </section>
  <!-- Curso -->

  <!-- Curso informações -->
  <section id="curso-informacoes">
    <div class="wrap-section curso-informacoes-wrap grafismo">
      <div class="container">
        <div class="row">
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

            <?php if (!empty($data['curso']->objetivosGerais)) { ?>
            <div class="curso-informacao">
              <div class="heading heading4">
                <h1 class="text-uppercase"><strong>Objetivos</strong></h1>
                <span class="bottomline"></span>
              </div>
              <?php echo htmlspecialchars_decode($data['curso']->objetivosGerais); ?>
            </div>
            <?php } ?>
            
            <?php if (!empty($data['curso']->publicoAlvo)) { ?>
            <div class="curso-informacao">
              <div class="heading heading4">
                <h1 class="text-uppercase"><strong>Público Alvo</strong></h1>
                <span class="bottomline"></span>
              </div>
              <?php echo htmlspecialchars_decode($data['curso']->publicoAlvo); ?>
            </div>
            <?php } ?>

          </div>
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

            <?php if (!empty($data['curso']->disciplinas)) { ?>
            <div class="curso-informacao">
              <div class="heading heading4">
                <h1 class="text-uppercase"><strong>Disciplinas</strong></h1>
                <span class="bottomline"></span>
              </div>
              <?php echo htmlspecialchars_decode($data['curso']->disciplinas); ?>
            </div>
            <?php } ?>

            <?php if (!empty($data['curso']->professores)) { ?>
            <div class="curso-informacao">
              <div class="heading heading4">
                <h1 class="text-uppercase"><strong>Professores</strong></h1>
                <span class="bottomline"></span>
              </div>
              <?php echo htmlspecialchars_decode($data['curso']->professores); ?>
             </div>
             <?php } ?>

          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Curso informações -->

  <!-- Footer -->
  <footer class="footer" style="margin-top:0;">
    <div class="marca-wrap">
      <img class="center-block" src="<?php echo Url::templatePath(); ?>/images/marca-footer.png" />
    </div>
    <div class="social-wrap text-center">
      <ul class="list-inline">
        <li class="facebook">
          <a target="_blank" href="https://www.facebook.com/pages/IEFAP-P%C3%B3s-Gradua%C3%A7%C3%A3o/291095547586274?fref=ts">Facebook</a>
        </li>
        <li target="_blank" class="youtube">
          <a href="https://www.youtube.com/channel/UCQ8HmB-8FFTGFTx2ai4BQWA">Youtube</a>
        </li>
        <li target="_blank" class="twitter">
          <a href="https://twitter.com/iefap">Twitter</a>
        </li>
        <li target="_blank" class="linkedin">
          <a href="https://www.linkedin.com/in/iefap-p%C3%B3s-gradua%C3%A7%C3%A3o-b1619aa8">Linkedin</a>
        </li>
        <li target="_blank" class="instagram">
          <a href="http://www.instagram.com/iefap.pos">Instagram</a>
        </li>
      </ul>
    </div>
    <p class="copyright text-center clearfix">Todos os direitos reservados ao Instituto de Ensino, Formação e Aperfeiçoamento LTDA.</p>
  </footer>
  <!-- Footer -->

</body>

</html>