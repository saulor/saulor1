<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/fale-iefap.png" />
  </div>
</section>
<!-- Banner -->

<!-- Contato -->
<section id="contato">
  <div class="wrap-section">
    <div class="container">
      
      <div class="row no-gutter">
        <div class="col-lg-12">
          <div class="navegacao-wrap hidden-xs">
            <ol class="breadcrumb">
              <li><a href="<?php echo SITEURL; ?>">Início</a></li>
              <li class="active">Contato</li>
            </ol>
          </div>
        </div>
      </div> 

      <div class="row">
          <div class="col-lg-12">
           <div class="heading internas">
              <h1>Fale com o IEFAP</h1>
              <h2>Contato</h2>
              <span class="bottomline"></span>
            </div>
          </div>
      </div>

      <style>
        .form-group.required .control-label:after{
           content: "\00a0*";
        }
      </style>

      <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
          <p>Para maiores informações sobre os cursos ofertados, envie-nos sua mensagem através do formulário de contato. Nós teremos o maior prazer em respondê-lo.</p>
        </div>
      </div>

      <br />

      <div class="row">
        <div class="col-xs-8">
          <?php
            echo Funcoes::getMensagem();
          ?>
        </div>
      </div>

      <div class="row">
        <div class="col-md-3 col-md-offset-2 col-sm-3 col-sm-offset-3">
          <p class="text-info">* Campos obrigatórios</p>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
          <form class="form-horizontal" method="post">
            <input type="hidden" name="id" value="0" />
            
            <div class="form-group required">
              <label for="CPF" class="col-sm-3 control-label">Seu nome</label>
              <div class="col-sm-8">
                <input class="form-control" id="nome" name="nome" placeholder="Seu nome" 
                  value="<?php echo $data['contato']->nome; ?>">
              </div>
            </div>
            <div class="form-group required">
              <label for="CPF" class="col-sm-3 control-label">Seu telefone</label>
              <div class="col-sm-4">
                <input class="form-control telefone" id="telefone" name="telefone" 
                  placeholder="Seu telefone" value="<?php echo $data['contato']->telefone; ?>">
              </div>
            </div>
            <div class="form-group required">
              <label for="CPF" class="col-sm-3 control-label">Seu melhor e-mail</label>
              <div class="col-sm-8">
                <input class="form-control" id="email" name="email" placeholder="Seu e-mail" 
                  value="<?php echo $data['contato']->email; ?>">
              </div>
            </div>
            <div class="form-group">
              <label for="CPF" class="col-sm-3 control-label">Sua cidade</label>
              <div class="col-sm-8">
                <input class="form-control" id="cidade" name="cidade" placeholder="Sua cidade" 
                  value="<?php echo $data['contato']->cidade; ?>">
              </div>
            </div>
            <div class="form-group">
              <label for="CPF" class="col-sm-3 control-label">Seu estado</label>
              <div class="col-sm-4">
                <select class="form-control" id="estado" name="estado">
                  <option value="" <?php if ($data['contato']->estado == "") echo 'selected="selected"';  ?>>--Selecione seu estado--</option>
                  <option value="AC" <?php if ($data['contato']->estado == "AC") echo 'selected="selected"'; ?>>AC</option>
                  <option value="AL" <?php if ($data['contato']->estado == "AL") echo 'selected="selected"'; ?>>AL</option>
                  <option value="AP" <?php if ($data['contato']->estado == "AP") echo 'selected="selected"'; ?>>AP</option>
                  <option value="AM" <?php if ($data['contato']->estado == "AM") echo 'selected="selected"'; ?>>AM</option>
                  <option value="BA" <?php if ($data['contato']->estado == "BA") echo 'selected="selected"'; ?>>BA</option>
                  <option value="CE" <?php if ($data['contato']->estado == "CE") echo 'selected="selected"'; ?>>CE</option>
                  <option value="DF" <?php if ($data['contato']->estado == "DF") echo 'selected="selected"'; ?>>DF</option>
                  <option value="ES" <?php if ($data['contato']->estado == "ES") echo 'selected="selected"'; ?>>ES</option>
                  <option value="GO" <?php if ($data['contato']->estado == "GO") echo 'selected="selected"'; ?>>GO</option>
                  <option value="MA" <?php if ($data['contato']->estado == "MA") echo 'selected="selected"'; ?>>MA</option>
                  <option value="MT" <?php if ($data['contato']->estado == "MT") echo 'selected="selected"'; ?>>MT</option>
                  <option value="MS" <?php if ($data['contato']->estado == "MS") echo 'selected="selected"'; ?>>MS</option>
                  <option value="MG" <?php if ($data['contato']->estado == "MG") echo 'selected="selected"'; ?>>MG</option>
                  <option value="PA" <?php if ($data['contato']->estado == "PA") echo 'selected="selected"'; ?>>PA</option>
                  <option value="PB" <?php if ($data['contato']->estado == "PB") echo 'selected="selected"'; ?>>PB</option>
                  <option value="PR" <?php if ($data['contato']->estado == "PR") echo 'selected="selected"'; ?>>PR</option>
                  <option value="PE" <?php if ($data['contato']->estado == "PE") echo 'selected="selected"'; ?>>PE</option>
                  <option value="PI" <?php if ($data['contato']->estado == "PI") echo 'selected="selected"'; ?>>PI</option>
                  <option value="RJ" <?php if ($data['contato']->estado == "RJ") echo 'selected="selected"'; ?>>RJ</option>
                  <option value="RN" <?php if ($data['contato']->estado == "RN") echo 'selected="selected"'; ?>>RN</option>
                  <option value="RS" <?php if ($data['contato']->estado == "RS") echo 'selected="selected"'; ?>>RS</option>
                  <option value="RO" <?php if ($data['contato']->estado == "RO") echo 'selected="selected"'; ?>>RO</option>
                  <option value="RR" <?php if ($data['contato']->estado == "RR") echo 'selected="selected"'; ?>>RR</option>
                  <option value="SC" <?php if ($data['contato']->estado == "SC") echo 'selected="selected"'; ?>>SC</option>
                  <option value="SP" <?php if ($data['contato']->estado == "SP") echo 'selected="selected"'; ?>>SP</option>
                  <option value="SE" <?php if ($data['contato']->estado == "SE") echo 'selected="selected"'; ?>>SE</option>
                  <option value="TO" <?php if ($data['contato']->estado == "TO") echo 'selected="selected"'; ?>>TO</option>
                </select>
              </div>
            </div>
            <div class="form-group required">
              <label for="Assunto" class="col-sm-3 control-label">Assunto</label>
              <div class="col-sm-8">
                <select name="assunto" id="assunto" class="form-control">
                  <option value="">--Selecione--</option>
                  <option data-require="cursos" value="Informações sobre cursos"<?php if ($data['contato']->assunto == 'Informações sobre cursos') echo ' selected';?>>Informações sobre cursos</option>
                  <option value="Outros"<?php if ($data['contato']->assunto == 'Outros') echo ' selected';?>>Outros</option>
                </select>
              </div>
            </div>
            <div id="cursos" class="form-group sh required hidden">
              <label for="CPF" class="col-sm-3 control-label">Curso(s)</label>
              <div class="col-sm-8">
                <input class="form-control" name="cursos" placeholder="Informe os cursos de interesse" 
                   value="<?php echo $data['contato']->cursos; ?>"/>
                <small class="text-info">Se tiver interesse em mais de um curso informe-os separados por vírgula</small>
              </div>
            </div>
            <div class="form-group required">
              <label for="Senha" class="col-sm-3 control-label">Mensagem</label>
              <div class="col-sm-8">
                <textarea name="mensagem" rows="10" class="form-control"><?php echo $data['contato']->mensagem; ?></textarea>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-3 col-sm-10">
                <p class="text-info small">Sua privacidade será respeitada e suas informações pessoais devidamente protegidas.</p>
                <button type="submit" class="btn btn-orange">Enviar</button>
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
<!-- Contato -->

<script>
  $('#assunto').on('change', function(ev) {
    ev.preventDefault();
    $('.sh').each(function(){
      $(this).addClass('hidden');
      $(this).find('input').val('');
    });
    var e = document.getElementById($(this).attr('id'));
    var option = e.options[e.selectedIndex];
    var require = $(option).attr('data-require');
    var required = typeof(require) !== 'undefined';
    if (required) {
      $('#' + require).removeClass('hidden');
    }
  });
  $('#assunto').trigger('change');
</script>