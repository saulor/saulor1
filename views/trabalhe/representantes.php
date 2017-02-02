<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/banner-posgraduacao-2.jpg" />
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
              <li><a href="<?php echo SITEURL; ?>institucional/trabalhe-conosco">Trabalhe Conosco</a></li>
              <li class="active">Representantes</li>
            </ol>
          </div>
        </div>
      </div> 

      <div class="row">
          <div class="col-lg-12">
           <div class="heading internas">
              <h1>Trabalhe Conosco</h1>
              <h2>Representantes</h2>
              <span class="bottomline"></span>
            </div>
          </div>
      </div>

      <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
          <br />
          <p>Colaboradores que queiram deixar seu cadastro registrado no IEFAP para futuras contratações.</p>
          <br />

          <?php
            echo Funcoes::getMensagem();
          ?>

          <form class="form-horizontal" method="post" enctype="multipart/form-data">
            <input type="hidden" name="Representante[id]" value="0" />
            <fieldset>
              <legend><strong>Identificação</strong></legend>

              <div class="form-group">
                <div class="group-data">
                  <label for="Nome" class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label">Nome</label>
                  <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                    <input type="text" autofocus="autofocus" class="form-control" 
                      id="nome" name="Representante[nome]" placeholder="Nome" 
                      value="<?php echo $data['objeto']->nome; ?>"/>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="group-data">
                  <label for="E-mail" class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label">E-mail</label>
                  <div class="col-lg-8 col-md-10 col-sm-10 col-xs-12">
                    <input type="text" class="form-control" 
                      id="nome" name="Representante[email]" placeholder="E-mail" 
                      value="<?php echo $data['objeto']->email; ?>"/>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="group-data">
                  <label for="Telefone Residencial" class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label">Tel. Resid.</label>
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <input type="text" class="telefone form-control" 
                      name="Representante[telefoneResidencial]" placeholder="Tel. Residencial" 
                      value="<?php echo $data['objeto']->telefoneResidencial; ?>"/>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="group-data">
                  <label for="Operadora de Celular" class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label">Op. Cel.</label>
                  <div class="col-lg-3 col-md-2 col-sm-3 col-xs-12">
                    <select class="form-control" name="Representante[operadoraCelular]">
                      <option value="">--Operadora--</option>
                      <option value="Claro"<?php if($data['objeto']->operadoraCelular == "Claro") echo ' selected="selected"'; ?>>Claro</option>
                      <option value="Tim"<?php if($data['objeto']->operadoraCelular == "Tim") echo ' selected="selected"'; ?>>Tim</option>
                      <option value="Vivo"<?php if($data['objeto']->operadoraCelular == "Vivo") echo ' selected="selected"'; ?>>Vivo</option>
                    </select>
                  </div>
                </div>
                <div class="clearfix visible-sm"></div>
                <div class="group-data">
                  <label for="Telefone Celular" class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label">Tel. Cel.</label>
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <input type="text" class="telefone form-control" 
                      name="Representante[telefoneCelular]" placeholder="Tel. Celular" 
                      value="<?php echo $data['objeto']->telefoneCelular; ?>"/>
                  </div>
                </div>
              </div>

            </fieldset>

            <fieldset>
              <legend><strong>Endereço</strong></legend>
              <div class="form-group">
                <div class="group-data">
                  <label for="Cep" class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label">Cep</label>
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-9">
                    <input name="Colaborador[cep]" placeholder="Cep" class="form-control cep" 
                      type="text" value="<?php echo $data['objeto']->cep; ?>" maxlength="9" />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="group-data">
                  <label for="Endereço" class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label">Endereço</label>
                  <div class="col-lg-7 col-md-8 col-sm-8 col-xs-12">
                    <input id="endereco" placeholder="Endereço" class="form-control autocomplete-cep" 
                      name="Representante[endereco]" type="text" 
                      value="<?php echo $data['objeto']->endereco; ?>" />
                  </div>
                </div>
                <div class="group-data">
                  <label for="Número" class="col-lg-1 col-md-2 col-sm-2 col-xs-12 control-label">Número</label>
                  <div class="col-lg-2 col-md-1 col-sm-2 col-xs-4">
                    <input id="numero" name="Representante[numero]" placeholder="Número" 
                      class="form-control" type="text" 
                      value="<?php echo $data['objeto']->numero; ?>" />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="group-data">
                  <label for="Complemento" class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label">Complem.</label>
                  <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                    <input id="complemento" name="Representante[complemento]" placeholder="Complemento" 
                      class="form-control" type="text" value="<?php echo $data['objeto']->complemento; ?>" />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="group-data">
                  <label for="Bairro" class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label">Bairro</label>
                  <div class="col-lg-6 col-md-10 col-sm-10 col-xs-12">
                    <input id="bairro" placeholder="Bairro" class="form-control autocomplete-cep" 
                      name="Representante[bairro]" type="text" value="<?php echo $data['objeto']->bairro; ?>" />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="group-data">
                  <label for="Cidade" class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label">Cidade</label>
                  <div class="col-lg-6 col-md-10 col-sm-10 col-xs-12">
                    <input id="cidade" placeholder="Cidade" class="form-control autocomplete-cep" 
                      name="Representante[cidade]" type="text" 
                      value="<?php echo $data['objeto']->cidade; ?>" />
                  </div>
                </div>
                <div class="group-data">
                  <label for="UF" class="col-lg-1 col-md-2 col-sm-2 col-xs-12 control-label">UF</label>
                  <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                    <select id="uf" name="Representante[uf]" class="form-control autocomplete-cep">
                      <option value=""<?php if ($data['objeto']->uf == "") echo ' selected';  ?>>--Selecione--</option>
                      <option value="AC"<?php if ($data['objeto']->uf == "AC") echo ' selected'; ?>>AC</option>
                      <option value="AL"<?php if ($data['objeto']->uf == "AL") echo ' selected'; ?>>AL</option>
                      <option value="AP"<?php if ($data['objeto']->uf == "AP") echo ' selected'; ?>>AP</option>
                      <option value="AM"<?php if ($data['objeto']->uf == "AM") echo ' selected'; ?>>AM</option>
                      <option value="BA"<?php if ($data['objeto']->uf == "BA") echo ' selected'; ?>>BA</option>
                      <option value="CE"<?php if ($data['objeto']->uf == "CE") echo ' selected'; ?>>CE</option>
                      <option value="DF"<?php if ($data['objeto']->uf == "DF") echo ' selected'; ?>>DF</option>
                      <option value="ES"<?php if ($data['objeto']->uf == "ES") echo ' selected'; ?>>ES</option>
                      <option value="GO"<?php if ($data['objeto']->uf == "GO") echo ' selected'; ?>>GO</option>
                      <option value="MA"<?php if ($data['objeto']->uf == "MA") echo ' selected'; ?>>MA</option>
                      <option value="MT"<?php if ($data['objeto']->uf == "MT") echo ' selected'; ?>>MT</option>
                      <option value="MS"<?php if ($data['objeto']->uf == "MS") echo ' selected'; ?>>MS</option>
                      <option value="MG"<?php if ($data['objeto']->uf == "MG") echo ' selected'; ?>>MG</option>
                      <option value="PA"<?php if ($data['objeto']->uf == "PA") echo ' selected'; ?>>PA</option>
                      <option value="PB"<?php if ($data['objeto']->uf == "PB") echo ' selected'; ?>>PB</option>
                      <option value="PR"<?php if ($data['objeto']->uf == "PR") echo ' selected'; ?>>PR</option>
                      <option value="PE"<?php if ($data['objeto']->uf == "PE") echo ' selected'; ?>>PE</option>
                      <option value="PI"<?php if ($data['objeto']->uf == "PI") echo ' selected'; ?>>PI</option>
                      <option value="RJ"<?php if ($data['objeto']->uf == "RJ") echo ' selected'; ?>>RJ</option>
                      <option value="RN"<?php if ($data['objeto']->uf == "RN") echo ' selected'; ?>>RN</option>
                      <option value="RS"<?php if ($data['objeto']->uf == "RS") echo ' selected'; ?>>RS</option>
                      <option value="RO"<?php if ($data['objeto']->uf == "RO") echo ' selected'; ?>>RO</option>
                      <option value="RR"<?php if ($data['objeto']->uf == "RR") echo ' selected'; ?>>RR</option>
                      <option value="SC"<?php if ($data['objeto']->uf == "SC") echo ' selected'; ?>>SC</option>
                      <option value="SP"<?php if ($data['objeto']->uf == "SP") echo ' selected'; ?>>SP</option>
                      <option value="SE"<?php if ($data['objeto']->uf == "SE") echo ' selected'; ?>>SE</option>
                      <option value="TO"<?php if ($data['objeto']->uf == "TO") echo ' selected'; ?>>TO</option>
                    </select>
                  </div>
                </div>
              </div>
            </fieldset>

            <fieldset class="hidden-sm hidden-xs">
              <legend><strong>Currículo</strong></legend>
              <p>Envie seu currículo</p>
              <div class="form-group">
                <label for="Currículo" class="col-lg-2 col-sm-3 control-label">Currículo</label>
                <div class="col-sm-9">
                  <input type="file" name="curriculoComercial" />
                  <small class="text-info">Tipos de arquivo permitidos: doc, docx, ou pdf.</small>
                </div>
              </div>
            </fieldset>

            <div class="form-group">
              <div class="col-sm-1">
                <button type="submit" class="btn btn-orange">Salvar</button>
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
<!-- Sobre o IEFAP  -->

<script>
  $('.cursos').tinyAutocomplete({
    url: '<?php echo SITEURL; ?>json/cursos',
    onSelect: function(el, val) {
      if(val != null) {
        $(this).val(val.title);
      }
    }
  });
</script>