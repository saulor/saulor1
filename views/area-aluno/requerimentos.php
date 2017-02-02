<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/area-aluno.png" />
  </div>
</section>
<!-- Banner -->

<!-- Requerimentos  -->
<section id="requerimentos">
  <div class="wrap-section">
    <div class="container">

      <div class="row no-gutter">
        <div class="col-lg-12">
          <div class="navegacao-wrap hidden-xs">
            <ol class="breadcrumb">
              <li><a href="<?php echo SITEURL; ?>">Início</a></li>
              <li><a href="<?php echo SITEURL; ?>area-aluno">Área do Aluno</a></li>
              <li class="active">Requerimentos</li>
            </ol>
          </div>
        </div>
      </div> 

      <div class="row">
          <div class="col-lg-12">
           <div class="heading internas">
              <h1>Área do Aluno</h1>
              <h2>Requerimentos</h2>
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
        <div class="col-lg-12">
          <p>Utilize este formulário de requerimentos para solicitar documentos junto a direção do IEFAP.</p>
          <p>Para acompanhar o processo de atendimento da sua solicitação clique <a href="<?php echo SITEURL; ?>area-aluno/requerimentos/acompanhamento">aqui</a>.</p>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-12">

          <?php
            echo Funcoes::getMensagem();
          ?>

          <form class="form-horizontal" method="post" enctype="multipart/form-data">
            <input type="hidden" name="Requerimento[id]" value="0" />
            <fieldset>
              <legend><strong>Requerente</strong></legend>

              <div class="form-group">
                <div class="group-data">
                  <label for="Nome" class="col-lg-1 col-md-2 col-sm-2 col-xs-12 control-label">Nome</label>
                  <div class="col-lg-6 col-md-10 col-sm-10 col-xs-12">
                    <input type="text" autofocus="autofocus" class="form-control" 
                      id="nome" name="Requerimento[nome]" placeholder="Nome" 
                      value="<?php echo $data['requerimento']->nome; ?>"/>
                  </div>
                </div>
                <div class="group-data">
                  <label for="E-mail" class="col-lg-1 col-md-2 col-sm-2 col-xs-12 control-label">E-mail</label>
                  <div class="col-lg-4 col-md-10 col-sm-10 col-xs-12">
                    <input type="text" autofocus="autofocus" class="form-control" 
                      id="nome" name="Requerimento[email]" placeholder="Nome" 
                      value="<?php echo $data['requerimento']->email; ?>"/>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="group-data">
                  <label for="Nome" class="col-lg-1 col-md-2 col-sm-2 col-xs-12 control-label">Curso</label>
                  <div class="col-lg-11 col-md-10 col-sm-10 col-xs-12">
                    <input type="text" class="form-control" 
                      id="cursos" name="Requerimento[curso]" placeholder="Curso" 
                      value="<?php echo $data['requerimento']->curso; ?>"/>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="group-data">
                  <label for="Unidade" class="col-lg-1 col-md-2 col-sm-2 col-xs-12 control-label">Unidade</label>
                  <div class="col-lg-11 col-md-10 col-sm-10 col-xs-12">
                    <input type="text" class="form-control" 
                      name="Requerimento[cidade1]" placeholder="Cidade" 
                      value="<?php echo $data['requerimento']->cidade1; ?>"/>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="group-data">
                  <label for="Telefone Residencial" class="col-lg-1 col-md-2 col-sm-2 col-xs-12 control-label">Tel. Res.</label>
                  <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                    <input type="text" class="telefone form-control" 
                      name="Requerimento[telefoneResidencial]" placeholder="Tel. Residencial" 
                      value="<?php echo $data['requerimento']->telefoneResidencial; ?>"/>
                  </div>
                </div>
                <div class="clearfix visible-sm"></div>
                <div class="group-data">
                  <label for="Nome" class="col-lg-1 col-md-2 col-sm-2 col-xs-12 control-label">Op. Cel.</label>
                  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                    <select class="form-control" name="Requerimento[operadoraCelular]">
                      <option value="">--Operadora--</option>
                      <option value="Claro"<?php if($data['requerimento']->operadoraCelular == "Claro") echo ' selected'; ?>>Claro</option>
                      <option value="Tim"<?php if($data['requerimento']->operadoraCelular == "Tim") echo ' selected'; ?>>Tim</option>
                      <option value="Vivo"<?php if($data['requerimento']->operadoraCelular == "Vivo") echo ' selected'; ?>>Vivo</option>
                    </select>
                  </div>
                </div>
                <div class="clearfix visible-sm"></div>
                <div class="group-data">
                  <label for="Nome" class="col-lg-1 col-lg-offset-1 col-md-2 col-sm-2 col-xs-12 control-label">Tel. Cel.</label>
                  <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                    <input type="text" class="telefone form-control" 
                      name="Requerimento[telefoneCelular]" placeholder="Tel. Celular" 
                      value="<?php echo $data['requerimento']->telefoneCelular; ?>"/>
                  </div>
                </div>
              </div>

            </fieldset>

            <fieldset>
              <legend><strong>Endereço para envio do documento</strong></legend>
              <div class="form-group">
                <div class="group-data">
                  <label for="Cep" class="col-lg-1 col-md-2 col-sm-2 col-xs-12 control-label">Cep</label>
                  <div id="localizaCep" class="col-lg-3 col-md-4 col-xs-4">
                    <input name="Requerimento[cep]" placeholder="Cep" class="form-control cep" 
                      type="text" value="<?php echo $data['requerimento']->cep; ?>"
                      maxlength="9" />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="group-data">
                  <label for="Endereço" class="col-lg-1 col-md-2 col-sm-2 col-xs-12 control-label">Endereço</label>
                  <div class="col-lg-6 col-md-7 col-sm-7 col-xs-12">
                    <input id="endereco" placeholder="Endereço" class="form-control autocomplete-cep" 
                      name="Requerimento[endereco]" type="text" 
                      value="<?php echo $data['requerimento']->endereco; ?>" />
                  </div>
                </div>
                <div class="group-data">
                  <label for="Número" class="col-lg-1 col-md-2 col-sm-2 col-xs-12 control-label">Número</label>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                    <input name="Requerimento[numero]" placeholder="Número" 
                      class="form-control" type="text" 
                      value="<?php echo $data['requerimento']->numero; ?>" />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="group-data">
                  <label for="Complemento" class="col-lg-1 col-md-2 col-sm-2 col-xs-12 control-label">Complem.</label>
                  <div class="col-lg-6 col-md-10 col-sm-10 col-xs-12">
                    <input name="Requerimento[complemento]" placeholder="Complemento" 
                      class="form-control" type="text" 
                      value="<?php echo $data['requerimento']->complemento; ?>" />
                  </div>
                </div>
                <div class="group-data">
                  <label for="Bairro" class="col-lg-1 col-md-2 col-sm-2 col-xs-12 control-label">Bairro</label>
                  <div class="col-lg-4 col-md-10 col-sm-10 col-xs-12">
                    <input id="bairro" placeholder="Bairro" class="form-control autocomplete-cep" 
                      name="Requerimento[bairro]" type="text" 
                      value="<?php echo $data['requerimento']->bairro; ?>" />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="group-data">
                  <label for="Cidade" class="col-lg-1 col-md-2 col-sm-2 col-xs-12 control-label">Cidade</label>
                  <div class="col-lg-6 col-md-10 col-sm-10 col-xs-12">
                    <input id="cidade" placeholder="Cidade" class="form-control autocomplete-cep" 
                      name="Requerimento[cidade2]" type="text" 
                      value="<?php echo $data['requerimento']->cidade2; ?>" />
                  </div>
                </div>
                <div class="group-data">
                  <label for="UF" class="col-lg-1 col-md-2 col-sm-2 col-xs-12 control-label">UF</label>
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <select id="uf" name="Requerimento[uf]" class="form-control autocomplete-cep">
                      <option value=""<?php if ($data['requerimento']->uf == "") echo ' selected"';  ?>>--Selecione--</option>
                      <option value="AC"<?php if ($data['requerimento']->uf == "AC") echo ' selected"'; ?>>AC</option>
                      <option value="AL"<?php if ($data['requerimento']->uf == "AL") echo ' selected"'; ?>>AL</option>
                      <option value="AP"<?php if ($data['requerimento']->uf == "AP") echo ' selected"'; ?>>AP</option>
                      <option value="AM"<?php if ($data['requerimento']->uf == "AM") echo ' selected"'; ?>>AM</option>
                      <option value="BA"<?php if ($data['requerimento']->uf == "BA") echo ' selected"'; ?>>BA</option>
                      <option value="CE"<?php if ($data['requerimento']->uf == "CE") echo ' selected"'; ?>>CE</option>
                      <option value="DF"<?php if ($data['requerimento']->uf == "DF") echo ' selected"'; ?>>DF</option>
                      <option value="ES"<?php if ($data['requerimento']->uf == "ES") echo ' selected"'; ?>>ES</option>
                      <option value="GO"<?php if ($data['requerimento']->uf == "GO") echo ' selected"'; ?>>GO</option>
                      <option value="MA"<?php if ($data['requerimento']->uf == "MA") echo ' selected"'; ?>>MA</option>
                      <option value="MT"<?php if ($data['requerimento']->uf == "MT") echo ' selected"'; ?>>MT</option>
                      <option value="MS"<?php if ($data['requerimento']->uf == "MS") echo ' selected"'; ?>>MS</option>
                      <option value="MG"<?php if ($data['requerimento']->uf == "MG") echo ' selected"'; ?>>MG</option>
                      <option value="PA"<?php if ($data['requerimento']->uf == "PA") echo ' selected"'; ?>>PA</option>
                      <option value="PB"<?php if ($data['requerimento']->uf == "PB") echo ' selected"'; ?>>PB</option>
                      <option value="PR"<?php if ($data['requerimento']->uf == "PR") echo ' selected"'; ?>>PR</option>
                      <option value="PE"<?php if ($data['requerimento']->uf == "PE") echo ' selected"'; ?>>PE</option>
                      <option value="PI"<?php if ($data['requerimento']->uf == "PI") echo ' selected"'; ?>>PI</option>
                      <option value="RJ"<?php if ($data['requerimento']->uf == "RJ") echo ' selected"'; ?>>RJ</option>
                      <option value="RN"<?php if ($data['requerimento']->uf == "RN") echo ' selected"'; ?>>RN</option>
                      <option value="RS"<?php if ($data['requerimento']->uf == "RS") echo ' selected"'; ?>>RS</option>
                      <option value="RO"<?php if ($data['requerimento']->uf == "RO") echo ' selected"'; ?>>RO</option>
                      <option value="RR"<?php if ($data['requerimento']->uf == "RR") echo ' selected"'; ?>>RR</option>
                      <option value="SC"<?php if ($data['requerimento']->uf == "SC") echo ' selected"'; ?>>SC</option>
                      <option value="SP"<?php if ($data['requerimento']->uf == "SP") echo ' selected"'; ?>>SP</option>
                      <option value="SE"<?php if ($data['requerimento']->uf == "SE") echo ' selected"'; ?>>SE</option>
                      <option value="TO"<?php if ($data['requerimento']->uf == "TO") echo ' selected"'; ?>>TO</option>
                    </select>
                  </div>
                </div>
              </div>
            </fieldset>

            <fieldset>
              <legend><strong>Tipo de Requerimento</strong></legend>
              <div class="pull-left" style="width: 50%; padding-right:8px; padding-left: 10px;">
                <input type="hidden" name="Requerimento[tipo]" value="" />
                <?php
                  $metade = (int) (count($data['tipos']) / 2);
                  foreach ($data['tipos'] as $key => $value) {
                    echo '<div class="radio"><label>';
                    echo '<input type="radio" name="Requerimento[tipo]" value="' . $key . '"';
                    if ($data['requerimento']->tipo == $key) {
                      echo ' checked="checked"';
                    }
                    echo '>   ';
                    echo $value['descricao'];
                    if (!empty($value['taxa'])) {
                      echo '<br />';
                      echo '<small class="text-info">Taxa: ' . $value['taxa'] . '</small>';
                    }
                    echo '</label>';
                    echo '</div>';
                    if ($key == $metade) {
                      break;
                    }
                  }
                ?>
              </div>
              <div class="pull-left" style="width: 50%;">
                <?php
                  foreach ($data['tipos'] as $key => $value) {
                    if ($key <= $metade) {
                      continue;
                    }
                    echo '<div class="radio"><label>';
                    echo '<input type="radio" name="Requerimento[tipo]" value="' . $key . '"';
                    if ($data['requerimento']->tipo == $key) {
                      echo ' checked="checked"';
                    }
                    echo '>   ';
                    echo $value['descricao'];
                    if (!empty($value['taxa'])) {
                      echo '<br />';
                      echo '<small class="text-info">Taxa: ' . $value['taxa'] . '</small>';
                    }
                    echo '</label>';
                    echo '</div>';

                  }
                ?>
              </div>

              <div class="clearfix"></div>

              <div class="form-group">
                <label for="Especificações" class="col-sm-1">Especificações</label>
                <div class="col-sm-12">
                  <textarea rows="5" class="form-control" name="Requerimento[especificacoes]" 
                    placeholder="Especificações"><?php echo $data['requerimento']->especificacoes; ?></textarea>
                </div>
              </div>

              <div class="form-group">
                <label for="Comprovante" class="col-sm-1">Comprovante</label>
                <div class="col-sm-12">
                  <input name="comprovante" type="file" />
                  <small class="text-info">Observe o valor da taxa correspondente ao requerimento que deseja solicitar e envie o comprovante de pagamento em anexo</small>
                </div>
              </div>

              <div class="form-group">
                <label for="Anexo" class="col-sm-1">Anexo</label>
                <div class="col-sm-12">
                  <input name="anexo" type="file" />
                  <small class="text-info">Espaço para anexar outro arquivo caso seja necessário</small>
                </div>
              </div>

            </fieldset>

            <div class="form-group">
              <div class="col-sm-1">
                <button type="submit" class="btn btn-orange">Solicitar</button>
              </div>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Requerimentos -->

<script>
    $('#cursos').tinyAutocomplete({
      url: '<?php echo SITEURL; ?>json/cursos',
      onSelect: function(el, val) {
        if(val != null) {
          $(this).val(val.title);
        }
      }
    });
</script>