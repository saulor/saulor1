<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/inscricao-sucesso.png" />
  </div>
</section>
<!-- Banner -->

<!-- Inscrições  -->
<section id="inscricoes">
  <div class="wrap-section">
    <div class="container">

      <div class="row no-gutter">
        <div class="col-lg-12">
          <div class="navegacao-wrap hidden-xs">
            <ol class="breadcrumb">
              <li><a href="<?php echo SITEURL; ?>">Início</a></li>
              <li><a href="<?php echo SITEURL;?>aperfeicoamento-profissional">Aperfeiçoamento Profissional</a></li>
              <?php 
              $url = SITEURL . 'aperfeicoamento-profissional';
              foreach ($data['categorias'] as $categoria) { 
                $url .= '/' . $categoria->slug;
                echo '<li><a href="' . $url . '">' . $categoria->nome . '</a></li>';
              } 
              ?>
              <li><a href="<?php echo SITEURL; ?>curso/<?php echo $data['curso']->link; ?>"><?php echo $data['curso']->nome; ?></a></li>
              <li><a href="<?php echo SITEURL; ?>inscricao/<?php echo $data['curso']->link; ?>">Inscrição</a></li>
              <li class="active">Inscrição realizada com sucesso</li>
            </ol>
          </div>
        </div>
      </div> 

      <div class="row">
          <div class="col-lg-12">
           <div class="heading internas">
              <h1>Inscrições</h1>
              <h2><?php echo $data['curso']->nome; ?></h2>
              <span class="bottomline"></span>
            </div>
          </div>
      </div>

      <br />

      <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
          <p>Sua inscrição foi realizada com sucesso!</p><p>Em breve um de nossos consultores entrará em contato com você!</p>
          <p>Obrigado por escolher o IEFAP!</p>
        </div>
        <div class="col-lg-4 col-md-4 visible-lg visible-md">
          <div class="matriz estilo2">
            <div class="logo-matriz">
              <img src="<?php echo Url::templatePath(); ?>images/logo.png" 
                alt="IEFAP" title="Logo IEFAP" />
            </div>
            <address>
              <p>
                <small>(44)</small>&nbsp;3123.6000 | 9985.7199<br />
                <small>Av. Adv. Horácio Raccanelo Filho, 5415 - Sala 01<br />Maringá - PR</small>
            </address>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
<!-- Inscrições -->

<?php if ($data['report']) { ?>

<script>
  function report() {
    var element = document.createElement('script');
    element.src = '<?php echo Url::templatePath() ?>js/adwords.js';
    document.body.appendChild(element);
    var interval = setInterval(function(){
        if (typeof goog_report_conversion === 'function'){
            clearInterval(interval);
            goog_report_conversion('<?php echo SITEURL; ?>curso/<?php echo $data["curso"]->link; ?>');
        }
    }, 100);
  }
  if (window.addEventListener) {
    window.addEventListener('load', report, false);
  }
  else if (window.attachEvent) {
    window.attachEvent('onload', report);
  }
  else {
    window.onload = report;
  }
</script>

<?php } ?>