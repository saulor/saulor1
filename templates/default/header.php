<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $data['meta']['meta.title']; ?></title>
    <?php
      foreach ($data['meta'] as $key => $value) {
        list($type, $mData) = explode('.', $key, 2);
        switch ($type) {
          case 'meta' :
            echo '<meta name="' . $mData . '" content="' . $value . '"/>';
            echo "\n    ";
            if ($mData == 'description') {
              // funciona como uma descrição default para as meta tags de compartilhamento 
              // das redes sociais
              $shareDescription = $value; 
            }
          break;

          case 'share' :
            $shareDescription = $value;
          break;
        }
      }
      if ($data['share']) {
          // facebook open graph
          echo '<!-- facebook open graph -->';
          echo "\n    ";
          echo '<meta property="og:type" content="website"/>';
          echo "\n    ";
          echo '<meta property="og:site_name" content="' . SITEPREFIX . '"/>';
          echo "\n    ";
          echo '<meta property="og:title" content="' . $data['meta']['meta.title'] . '"/>';
          echo "\n    ";
          echo '<meta property="og:url" content="' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '">';
          echo "\n    ";
          echo '<meta property="og:image" content="' . Url::templatePath() . 'images/avatar-facebook.jpg"/>';
          echo "\n    ";
          echo '<meta property="og:image:width" content="1024"/>';
          echo "\n    ";
          echo '<meta property="og:image:height" content="1024"/>';
          echo "\n    ";
          echo '<meta property="og:description" content="' . $shareDescription . '"/>';
          echo "\n    ";       
          echo '<meta property="fb:admins" content="638997731"/>';
          echo "\n    ";
          echo '<!-- twitter cards -->';
          echo "\n    ";
          // twitter cards
          echo '<meta property="twitter:card" content="summary"/>';
          echo "\n    ";
          echo '<meta property="twitter:domain" content="' . SITEURL . '"/>';
          echo "\n    ";
          echo '<meta property="twitter:site" content="@iefap"/>';
          echo "\n    ";
          echo '<meta property="twitter:creator" content="@iefap"/>';
          echo "\n    ";
          echo '<meta property="twitter:src" content="' . Url::templatePath() . 'images/avatar-facebook.jpg"/>';
          echo "\n    ";
          echo '<meta property="twitter:width" content="1024"/>';
          echo "\n    ";
          echo '<meta property="twitter:height" content="1024"/>';
          echo "\n    ";
          echo '<meta property="twitter:title" content="' . $data['meta']['meta.title'] . '"/>';
          echo "\n    ";
          echo '<meta property="twitter:description" content="' . $shareDescription . '"/>';
          echo "\n    ";
          echo '<meta property="twitter:url" content="' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '"/>';
          echo "\n    ";
      }
    ?><link rel="shortcut icon" href="<?php echo Url::templatePath(); ?>images/favicon.png" />
    <!-- CSS -->
<?php
View::css([
    Url::templatePath() . 'css/font.css',
    '//cdnjs.cloudflare.com/ajax/libs/normalize/4.1.1/normalize.min.css',
    '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css',
    '//cdnjs.cloudflare.com/ajax/libs/hover.css/2.0.2/css/hover-min.css',
     Url::templatePath() . 'css/pgwslider-2.3.min.css',
     Url::templatePath() . 'css/style.min.css',
     Url::templatePath() . 'css/mobile.min.css',
     Url::templatePath() . 'css/tiny-autocomplete.css'
]);
?>
    <!-- JS -->
    <!--[if lt IE 9]> 
    <script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
<?php
View::js(array(
    Url::templatePath() . 'js/zepto.min.js',
    Url::templatePath() . 'js/data.js',
    Url::templatePath() . 'js/event.js',
    Url::templatePath() . 'js/fx.js',
    Url::templatePath() . 'js/fx_methods.js',
    Url::templatePath() . 'js/zepto-slide-transition.min.js',
    Url::templatePath() . 'js/pgwslider-2.3.min.js',
    Url::templatePath() . 'js/jquery.mask.js',
    Url::templatePath() . 'js/tiny-autocomplete.js'
));
?>

</head>
<body>

    <!--[if lt IE 9]> <p class="text-center">Você está usando um navegador <strong>antigo</strong>. Este site funcionará melhor em um navegador mais moderno. Por favor, atualize seu navegador.</p> <![endif]-->
    
    <!-- Unidades bar -->
    <section id="unidades">
      <div class="container-fluid">
        <div class="row">
          <?php foreach ($data['unidadesBar'] as $u) { ?>
          <div id="unidade-<?php echo $u['class']; ?>"
            class="unidades-bar-wrap unidade <?php echo $u['class']; if (!array_key_exists('default', $u)) echo ' hidden'; ?>">
            <ul>
              <?php if (!empty($u["telefone"])) { ?>
              <li>
                <span class="glyphicon glyphicon-earphone" aria-hidden="true"></span>
                <?php echo $u["telefone"]; ?>
              </li>
              <?php } ?>
              <?php if (!empty($u["endereco"])) { ?>
              <li class="hidden-xs">
                <span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>
                <?php echo $u["endereco"]; ?>
              </li>
              <?php } ?>
              <?php if (!empty($u["email"])) { ?>
              <li>
                <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
                <?php echo $u["email"]; ?>
              </li>
              <?php } ?>
              <li class="spacer hidden-md">&nbsp;</li>
              <li class="popup text-uppercase text-bold hidden-sm hidden-xs">
                  <span class="glyphicon glyphicon-log-in" aria-hidden="true"></span>
                  Sala de Aula Virtual
                  <div class="login hvr-shadow">
                    <form method="post" action="http://www.iefap.com.br/ead/login/index.php">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="Usuário">
                      </div>
                      <div class="form-group">
                        <input type="password" class="form-control" placeholder="Senha">
                      </div>
                      <button type="submit" class="pull-right text-uppercase btn btn-yellow">Acessar</button>
                    </form>
                  </div>
              </li>
              <li class="hidden-sm hidden-xs">
                <select class="form-control" id="select-unidades">
                  <option>Escolha a Unidade</option>
                  <?php foreach ($data['unidadesBar'] as $key => $u) { ?>
                  <option<?php if($u['active']) echo ' selected';?> value="<?php echo $u['class']; ?>"><?php echo $key; ?></option>
                  <?php } ?>
                </select>
              </li>
            </ul>
          </div>
          <?php } ?>
        </div>
      </div>
    </section>
    <!-- Unidades bar -->

    <!-- Header -->
    <div class="container-fluid">
      <div class="row">
        <header role="banner">
          <div class="header-wrap">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
              <div class="header-logo-wrap">
                <h1>IEFAP - Instituto de Ensino, Formação e Aperfeiçoamento LTDA.</h1>
                <a href="<?php echo SITEURL; ?>">
                  <img alt="IEFAP Logo" class="logo" src="<?php echo Url::templatePath(); ?>images/logo.png" />
                </a>
              </div>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
              <nav id="menu-wrap" class="maringa">
                <div id="menu-trigger">Menu IEFAP</div>
                <!-- menu -->
                <ul class="menu" id="menu">
                  <li class="dropdown">
                    <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle">Institucional <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <li><a href="<?php echo SITEURL; ?>institucional">Sobre o IEFAP</a></li>
                      <li><a href="<?php echo SITEURL; ?>institucional/ies">IES</a></li>
                      <li><a href="<?php echo SITEURL; ?>institucional/unidades">Unidades</a></li>
                      <li><a href="<?php echo SITEURL; ?>institucional/parceiros">Parceiros</a></li>
                      <li><a href="<?php echo SITEURL; ?>institucional/trabalhe-conosco">Trabalhe Conosco</a></li>
                    </ul>
                  </li>
                  <li class="dropdown">
                    <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle">Pós-Graduação <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <?php foreach($data['categoriasPos'] as $c) { ?>
                      <li><a href="<?php echo SITEURL; ?>posgraduacao/<?php echo $c->slug; ?>"><?php echo $c->nome; ?></a></li>
                      <?php } ?>
                    </ul>
                  </li>
                  <li class="dropdown">
                    <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle">Aperfeiçoamento Profissional <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <?php foreach($data['categoriasApe'] as $c) { ?>
                      <li><a href="<?php echo SITEURL; ?>aperfeicoamento-profissional/<?php echo $c->slug; ?>"><?php echo $c->nome; ?></a></li>
                      <?php } ?>
                    </ul>
                  </li>
                  <!--li><a href="<?php echo SITEURL; ?>ead">EAD</a></li-->
                  <li><a href="<?php echo SITEURL; ?>contato">Contato</a></li>
                  <li class="hidden-xs"><a href="<?php echo SITEURL; ?>area-aluno">Área do Aluno</a></li>
                  <li><a href="<?php echo SITEURL; ?>noticias" role="button">Notícias</a></li>
                  <li class="dropdown search hidden-sm hidden-xs">
                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                    <ul class="dropdown-menu">
                      <li>
                        <form method="get" action="<?php echo SITEURL;?>busca" class="form-inline">
                            <div class="form-group">
                              <label class="sr-only" for="Buscar">Buscar</label>
                              <input type="text" name="search" id="search" placeholder="Buscar" class="form-control" />
                              <button type="submit" class="btn btn-orange">Buscar</button>
                            </div>
                        </form>
                      </li>
                    </ul>
                  </li>
                </ul>
                <!-- menu -->
                <!-- mobile -->
                <ul class="menu" id="menu-mobile">
                  <li><a href="<?php echo SITEURL; ?>">Início</a></li>
                  <li class="dropdown">
                    <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle">Institucional <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <li><a href="<?php echo SITEURL; ?>institucional">Sobre o IEFAP</a></li>
                      <li><a href="<?php echo SITEURL; ?>unidades">Unidades</a></li>
                      <li><a href="<?php echo SITEURL; ?>parceiros">Parceiros</a></li>
                      <li><a href="<?php echo SITEURL; ?>faq">Perguntas Frequentes</a></li>
                      <li><a href="<?php echo SITEURL; ?>trabalhe-conosco">Trabalhe Conosco</a></li>
                    </ul>
                  </li>
                  <li class="dropdown">
                    <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle">Pós-Graduação <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <?php foreach($data['categoriasPos'] as $c) { ?>
                      <li><a href="<?php echo SITEURL; ?>posgraduacao/<?php echo $c->slug; ?>"><?php echo $c->nome; ?></a></li>
                      <?php } ?>
                    </ul>
                  </li>
                  <li class="dropdown">
                    <a href="<?php echo SITEURL; ?>aperfeicoamento-profissional">Aperfeiçoamento Profissional</a>
                    <ul class="dropdown-menu">
                      <?php foreach($data['categoriasApe'] as $c) { ?>
                      <li><a href="<?php echo SITEURL; ?>aperfeicoamento-profissional/<?php echo $c->slug; ?>"><?php echo $c->nome; ?></a></li>
                      <?php } ?>
                    </ul>
                  </li>
                  <!--li><a href="<?php echo SITEURL; ?>ead">EAD</a></li-->
                  <li><a href="<?php echo SITEURL; ?>contato">Contato</a></li>
                  <li><a href="<?php echo SITEURL; ?>area-aluno">Área do Aluno</a></li>
                </ul>
                <!-- mobile -->
              </nav>
            </div>
          </div>
        </header>
      </div>
    </div>
    <!-- Header -->