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
              <li class="active">Cadastrar senha</li>
            </ol>
          </div>
        </div>
      </div> 

      <div class="row">
          <div class="col-lg-12">
           <div class="heading internas">
              <h1>Área do Aluno</h1>
              <h2>Cadastrar senha</h2>
              <span class="bottomline"></span>
            </div>
          </div>
      </div>

      <br />

      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <p>Informe seu CPF, e-mail e cadastre sua senha de acesso à Área do Aluno.</p>
          <br />

          <?php
            echo Funcoes::getMensagem();
          ?>

          <form class="form-horizontal" method="post">
            <input type="hidden" name="id" value="0" />
            <div class="form-group">
              <label for="CPF" class="col-sm-2 control-label">CPF:</label>
              <div class="col-sm-9">
                <input type="text" class="form-control cpf" id="cpf" name="cpf" 
                  placeholder="Seu cpf" 
                  value="<?php echo $data['dados']->cpf; ?>" />
              </div>
            </div>
            <div class="form-group">
              <label for="CPF" class="col-sm-2 control-label">E-mail:</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="email" name="email" 
                  placeholder="Seu melhor e-mail" 
                 value="<?php echo $data['dados']->email; ?>" />
              </div>
            </div>
            <div class="form-group">
              <label for="CPF" class="col-sm-2 control-label">Senha:</label>
              <div class="col-sm-9">
                <input type="password" class="form-control" id="senha" name="senha" 
                  placeholder="Sua senha" 
                  value="" />
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-orange">Cadastrar</button>
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