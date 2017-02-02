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
              <li class="active">Recuperar senha</li>
            </ol>
          </div>
        </div>
      </div> 

      <div class="row">
          <div class="col-lg-12">
           <div class="heading internas">
              <h1>Área do Aluno</h1>
              <h2>Recuperar senha</h2>
              <span class="bottomline"></span>
            </div>
          </div>
      </div>

      <br />

      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <p>Informe o CPF cadastrado. Você receberá um e-mail com sua senha de acesso à Área do Aluno.</p>
          <br />

          <?php
            echo Funcoes::getMensagem();
          ?>
          
          <form class="form-horizontal" method="post">
            <div class="form-group">
              <label for="CPF" class="col-sm-2 control-label">CPF:</label>
              <div class="col-sm-9">
                <input type="text" class="form-control cpf" id="cpf" name="cpf" 
                  placeholder="Seu cpf" autofocus />
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
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