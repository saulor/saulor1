<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/institucional.png" />
  </div>
</section>
<!-- Banner -->

<!-- Sobre o IEFAP  -->
<section id="sobre">
  <div class="wrap-section">
    <div class="container">

      <div class="row no-gutter">
        <div class="col-lg-12">
          <div class="navegacao-wrap hidden-xs">
            <ol class="breadcrumb">
              <li><a href="<?php echo SITEURL; ?>">Início</a></li>
              <li class="active">Sobre o IEFAP</li>
            </ol>
          </div>
        </div>
      </div> 

      <div class="row">
          <div class="col-lg-12">
           <div class="heading internas">
              <h1>Institucional</h1>
              <h2>Sobre o IEFAP</h2>
              <span class="bottomline"></span>
            </div>
          </div>
      </div>

      <div class="row">
        <div class="col-lg-12">
          <?php echo $data['pagina']->conteudo; ?>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Sobre o IEFAP  -->