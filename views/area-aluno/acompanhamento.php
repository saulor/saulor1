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

      <style>
        label {
          background-color: #f1f1f1;
          padding: 6px;
          display: block;
        }
      </style>

      <?php
        function getInfo ($obj, $var) {
          echo !empty($obj->$var) ? $obj->$var : 'Não informado';
        }
      ?>

      <br />

      <div class="row">
        <div class="col-lg-9">

          <?php
            echo Funcoes::getMensagem();
          ?>

          <div class="row">
            <div class="col-lg-12">
              <p>Sua solicitação foi registrada no dia <strong><?php echo date('d/m/Y', $data['requerimento']->timestamp); ?></strong>
                sob número de protocolo <strong><?php echo $data['requerimento']->protocolo; ?></strong>.<br />Clique <a href="<?php echo SITEURL; ?>area-aluno/requerimentos/acompanhamento">aqui</a>
                para consultar outro requerimento.</p>
            </div>
          </div>

          <br />

          <fieldset>
            <legend class="text-uppercase"><strong>dados do requerimento</strong></legend>

            <div class="row">
              <div class="col-lg-12">
                <label class="control-label"><strong>Tipo:</strong></label>
                <p><?php echo Requerimento::getTipo($data['requerimento']->tipo); ?></p>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-12">
                <label class="control-label"><strong>Especificações:</strong></label>
                <p><?php echo getInfo($data['requerimento'], 'especificacoes'); ?></p>
              </div>
            </div>

          </fieldset>

          <br />

          <fieldset>
            <legend class="text-uppercase"><strong>dados do requerente</strong></legend>

            <div class="row">
              <div class="col-lg-6">
                <label><strong>Nome:</strong></label>
                <p><?php echo getInfo($data['requerimento'], 'nome'); ?></p>
              </div>

              <div class="col-lg-6">
                <label><strong>E-mail:</strong></label>
                <p><?php echo getInfo($data['requerimento'], 'email'); ?></p>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-12">
                <label><strong>Curso:</strong></label>
                  <p><?php echo getInfo($data['requerimento'], 'curso'); ?></p>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-6">
                <label class="control-label"><strong>Cidade:</strong></label>
                <p><?php echo getInfo($data['requerimento'], 'cidade1'); ?></p>
              </div>

              <div class="col-lg-2">
                <label class="control-label"><strong>Tel. Resid.:</strong></label>
                <p><small><?php echo getInfo($data['requerimento'], 'telefoneResidencial'); ?></small></p>
              </div>

              <div class="col-lg-2">
                <label class="control-label"><strong>Op. Cel.:</strong></label>
                <p><?php echo getInfo($data['requerimento'], 'operadoraCelular'); ?></p>
              </div>

              <div class="col-lg-2">
                <label class="control-label"><strong>Tel. Cel.:</strong></label>
                <p><small><?php echo getInfo($data['requerimento'], 'telefoneCelular'); ?></small></p>
              </div>
            </div>
          </fieldset>

          <br />

          <fieldset>
            <legend class="text-uppercase"><strong>endereço para entrega</strong></legend>

              <div class="row">
                <div class="col-lg-2">
                  <label><strong>CEP:</strong></label>
                  <p><?php echo getInfo($data['requerimento'], 'cep'); ?></p>
                </div>

                <div class="col-lg-7">
                  <label><strong>Endereço:</strong></label>
                  <p><?php echo getInfo($data['requerimento'], 'endereco'); ?></p>
                </div>

                <div class="col-lg-3">
                  <label><strong>Complemento:</strong></label>
                  <p><?php echo getInfo($data['requerimento'], 'complemento'); ?></p>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-5">
                  <label><strong>Bairro:</strong></label>
                  <p><?php echo getInfo($data['requerimento'], 'bairro'); ?></p>
                </div>

                <div class="col-lg-4">
                  <label><strong>Cidade:</strong></label>
                  <p><?php echo getInfo($data['requerimento'], 'cidade2'); ?></p>
                </div>

                <div class="col-lg-3">
                  <label><strong>UF:</strong></label>
                  <p><?php echo getInfo($data['requerimento'], 'uf'); ?></p>
                </div>
              </div>
            </fieldset>

            <br />
          
          <fieldset>
            <legend class="text-uppercase"><strong>situação do requerimento</strong></legend>

            <div class="row">
              <div class="col-lg-5">
                <label><strong>Situação atual:</strong></label>
                <p><?php 
                  echo Requerimento::getSituacao($data['requerimento']->situacao); 
                  if ($data['requerimento']->situacao == Requerimento::REQUERIMENTO_SITUACAO_FINALIZADO) {
                    if (!empty($data['requerimento']->dataFinalizacaoTimestamp)) {
                      echo ' em ' . date('d/m/Y', $data['requerimento']->dataFinalizacaoTimestamp);
                    }
                    $diretorio = DIR_UPLOADS . DS . "requerimentos";
                    $diretorio .= DS . $data['requerimento']->id;
                    $diretorio .= DS . base64_decode($data['requerimento']->arquivo);
                    if (is_file($diretorio)) {
                      echo ' <a href="' . SITEURL . 'download/requerimentos/' . $data['requerimento']->protocolo . '">Baixar o arquivo</a>';
                    }
                  }
                ?></p>
              </div>

              <?php if ($data['requerimento']->situacao == Requerimento::REQUERIMENTO_SITUACAO_DEFERIDO_PROCESSO_EMISSAO) { ?>
              <div class="col-lg-7">
                <label><strong>Previsão para emissão:</strong></label>
                <p><?php 
                  echo !empty($data['requerimento']->previsao) ? $data['requerimento']->previsao : 'Não informado'; 
                ?></p>
              </div>
              <?php } ?>

            </div>

            <div class="row">
              <?php if ($data['requerimento']->situacao == Requerimento::REQUERIMENTO_SITUACAO_REGULARIZAR_PENDENCIA) { ?>
              <div class="col-lg-12">
                <label><strong>Pendência(s):</strong></label>
                <p><?php
                  $pendencias = explode(",", $data['requerimento']->pendencias);
                  $pendenciasNomes = array();
                  foreach ($pendencias as $p) {
                    $pendenciasNomes[] = Requerimento::getPendencia($p);
                  }
                  echo implode(", ", $pendenciasNomes);
                ?></p>
              </div>
              <?php } ?>
            </div>

            <?php if (!empty($data['requerimento']->observacoes)) { ?>
            <div class="row">
              <div class="col-lg-12">
                <label><strong>Observações:</strong></label>
                <p><?php echo $data['requerimento']->observacoes; ?></p>
              </div>
            </div>
            <?php } ?>

          </fieldset>

        </div>
        <div class="col-lg-3 col-md-3 visible-lg visible-md">
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