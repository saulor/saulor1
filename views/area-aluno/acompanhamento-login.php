<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/area-aluno.png" />
  </div>
</section>
<!-- Banner -->

<!-- Área do Aluno  -->
<section id="area-aluno">
  <div class="wrap-section">
    <div class="container">

      <div class="row no-gutter">
        <div class="col-lg-12">
          <div class="navegacao-wrap hidden-xs">
            <ol class="breadcrumb">
              <li><a href="<?php echo SITEURL; ?>">Início</a></li>
              <li><a href="<?php echo SITEURL; ?>area-aluno">Área do Aluno</a></li>
              <li><a href="<?php echo SITEURL; ?>area-aluno/requerimentos">Requerimentos</a></li>
              <li class="active">Acompanhamento</li>
            </ol>
          </div>
        </div>
      </div> 

      <div class="row">
          <div class="col-lg-12">
           <div class="heading internas">
              <h1>Área do Aluno</h1>
              <h2>Acompanhamento de Requerimentos</h2>
              <span class="bottomline"></span>
            </div>
          </div>
      </div>

      <div class="row">
        <div class="col-lg-12">
          <p>Clique <a href="<?php echo SITEURL; ?>area-aluno/sair">aqui</a> para sair da Área do Aluno.</p>
        </div>
      </div>

      <br />

      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <p>Para visualizar a situação dos seus requerimentos informe o número de protocolo do requerimento.</p>
          <br />
          
          <?php
            echo Funcoes::getMensagem();
          ?>

          <form class="form-horizontal" method="post">
            <div class="form-group">
              <label for="Número de Protocolo" class="col-sm-4 control-label">Número de Protocolo:</label>
              <div class="col-sm-7">
                <input class="form-control" autofocus="autofocus" id="protocolo" name="protocolo" 
                  placeholder="Número do protocolo do requerimento"
                  value="<?php echo $data['protocolo']; ?>" />
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-4 col-sm-10">
                <button type="submit" class="btn btn-orange">Acessar</button>
              </div>
            </div>
          </form>
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
<!-- Área do Aluno  -->