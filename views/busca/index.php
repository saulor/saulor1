<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/banner-default.jpg" />
  </div>
</section>
<!-- Banner -->

<!-- Busca -->
<section id="busca">
  <div class="wrap-section">
    <div class="container">
      
      <div class="row no-gutter">
        <div class="col-lg-12">
          <div class="navegacao-wrap hidden-xs">
            <ol class="breadcrumb">
              <li><a href="<?php echo SITEURL; ?>">Início</a></li>
              <li class="active">Resultados da busca</li>
            </ol>
          </div>
        </div>
      </div> 

      <div class="row">
          <div class="col-lg-12">
           <div class="heading internas" id="topo">
              <h1>Resultados da busca</h1>
            </div>
          </div>
      </div>

      <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
          
          <?php if (!empty($_GET["search"])) { ?>
            <h2 class="h4">Você pesquisou por: <?php echo $_GET["search"]; ?></h2>
          <?php } ?>

          <?php echo $data["pageLinks"]; ?>

          <?php foreach ($data["resultados"] as $key => $resultado) { ?>
            <div class="resultado">
              <h3>
                <u>
                  <a href="<?php echo $resultado['url']; ?>"><?php echo $resultado['titulo']; ?></a>
                </u>
              </h3>
              <div>
                <p><?php echo strip_tags($resultado['descricao']); ?></p>
                <a href="<?php echo $resultado['url']; ?>"><small><?php echo $resultado['url']; ?></small></a>
              </div>
            </div>
          <?php } ?>

          <?php if(count($data['resultados']) > 15) { ?>
          <a href="#topo">
            <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span> topo
          </a>
          <?php } ?>

          <?php echo $data["pageLinks"]; ?>

        </div>
        <div class="col-lg-4 col-md-4 visible-lg visible-md">
          <div class="matriz estilo2">
            <div class="logo-matriz">
              <img src="<?php echo Url::templatePath(); ?>images/logo.png" alt="IEFAP" title="Logo IEFAP" />
            </div>
            <address>
              <p>
                <small>(44)</small>&nbsp;3123.6000 | 9985.7199<br />
                <small>Av. Adv. Horácio Raccanelo Filho, 5415 - Sala 01<br />Maringá - PR</small>
              </p>
            </address>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Busca -->