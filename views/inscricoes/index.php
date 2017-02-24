<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/aperfeicoamento-profissional.png" />
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
              <li class="active">Inscrição</li>
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
        <div class="col-lg-12">
          <p>Para realizar sua inscrição preencha o formulário abaixo</p>
          <p>* Campos obrigatórios</p>
        </div>
      </div>

      <div class="row">

        <?php
          echo Funcoes::getMensagem();
        ?>

        <form id="preinscricao" class="form-horizontal" method="post" enctype="multipart/form-data">

          <input type="hidden" name="id" value="0" />
          <input type="hidden" name="curso" value="<?php echo $data['curso']->id; ?>" />
          <input type="hidden" name="status" value="<?php echo Preinscricao::PREINSCRICAO_STATUS_INTERESSADO; ?>" />

          <fieldset>
            <legend><strong>Identificação</strong></legend>
            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <div class="form-group">
                    <label class="control-label" for="Nome">Nome *</label>
                    <input class="form-control" type="text" placeholder="Seu nome" id="nome" 
                      name="nome" value="<?php echo $data['inscricao']->nome; ?>" 
                      autofocus="autofocus" />
                  </div>
              </div>
            </div>
            <div class="row inline hidden-xs">
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="form-group">
                        <label for="RG">RG</label>
                        <input class="form-control" type="text" id="rg" placeholder="Seu RG" 
                          name="rg" value="<?php echo $data['inscricao']->rg; ?>" />
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="form-group">
                        <label for="Órgão Expedidor">Órgão Expedidor</label>
                        <input type="text" class="form-control" id="orgaoExpedidor" 
                          placeholder="Órgão expedidor do seu RG" name="orgaoExpedidor" 
                          value="<?php echo $data['inscricao']->orgaoExpedidor; ?>" />
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="form-group">
                        <label for="UF Expedidor">UF Expedidor</label>
                        <select class="form-control" name="ufExpedidor">
                          <option value=""<?php if ($data['inscricao']->ufExpedidor == "") echo ' selected';  ?>>--Selecione--</option>
                          <option value="AC"<?php if ($data['inscricao']->ufExpedidor == "AC") echo ' selected'; ?>>AC</option>
                          <option value="AL"<?php if ($data['inscricao']->ufExpedidor == "AL") echo ' selected'; ?>>AL</option>
                          <option value="AP"<?php if ($data['inscricao']->ufExpedidor == "AP") echo ' selected'; ?>>AP</option>
                          <option value="AM"<?php if ($data['inscricao']->ufExpedidor == "AM") echo ' selected'; ?>>AM</option>
                          <option value="BA"<?php if ($data['inscricao']->ufExpedidor == "BA") echo ' selected'; ?>>BA</option>
                          <option value="CE"<?php if ($data['inscricao']->ufExpedidor == "CE") echo ' selected'; ?>>CE</option>
                          <option value="DF"<?php if ($data['inscricao']->ufExpedidor == "DF") echo ' selected'; ?>>DF</option>
                          <option value="ES"<?php if ($data['inscricao']->ufExpedidor == "ES") echo ' selected'; ?>>ES</option>
                          <option value="GO"<?php if ($data['inscricao']->ufExpedidor == "GO") echo ' selected'; ?>>GO</option>
                          <option value="MA"<?php if ($data['inscricao']->ufExpedidor == "MA") echo ' selected'; ?>>MA</option>
                          <option value="MT"<?php if ($data['inscricao']->ufExpedidor == "MT") echo ' selected'; ?>>MT</option>
                          <option value="MS"<?php if ($data['inscricao']->ufExpedidor == "MS") echo ' selected'; ?>>MS</option>
                          <option value="MG"<?php if ($data['inscricao']->ufExpedidor == "MG") echo ' selected'; ?>>MG</option>
                          <option value="PA"<?php if ($data['inscricao']->ufExpedidor == "PA") echo ' selected'; ?>>PA</option>
                          <option value="PB"<?php if ($data['inscricao']->ufExpedidor == "PB") echo ' selected'; ?>>PB</option>
                          <option value="PR"<?php if ($data['inscricao']->ufExpedidor == "PR") echo ' selected'; ?>>PR</option>
                          <option value="PE"<?php if ($data['inscricao']->ufExpedidor == "PE") echo ' selected'; ?>>PE</option>
                          <option value="PI"<?php if ($data['inscricao']->ufExpedidor == "PI") echo ' selected'; ?>>PI</option>
                          <option value="RJ"<?php if ($data['inscricao']->ufExpedidor == "RJ") echo ' selected'; ?>>RJ</option>
                          <option value="RN"<?php if ($data['inscricao']->ufExpedidor == "RN") echo ' selected'; ?>>RN</option>
                          <option value="RS"<?php if ($data['inscricao']->ufExpedidor == "RS") echo ' selected'; ?>>RS</option>
                          <option value="RO"<?php if ($data['inscricao']->ufExpedidor == "RO") echo ' selected'; ?>>RO</option>
                          <option value="RR"<?php if ($data['inscricao']->ufExpedidor == "RR") echo ' selected'; ?>>RR</option>
                          <option value="SC"<?php if ($data['inscricao']->ufExpedidor == "SC") echo ' selected'; ?>>SC</option>
                          <option value="SP"<?php if ($data['inscricao']->ufExpedidor == "SP") echo ' selected'; ?>>SP</option>
                          <option value="SE"<?php if ($data['inscricao']->ufExpedidor == "SE") echo ' selected'; ?>>SE</option>
                          <option value="TO"<?php if ($data['inscricao']->ufExpedidor == "TO") echo ' selected'; ?>>TO</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="form-group">
                        <label for="Data de Expedição">Data de Expedição</label>
                        <input type="text" class="form-control data" id="dataExpedicao" 
                          name="dataExpedicao" value="<?php echo $data['inscricao']->dataExpedicao; ?>"
                          placeholder="Data de expedição do seu RG" />
                    </div>
                </div>
            </div>
            <div class="row inline hidden-xs">
              <div class="col-lg-2 col-md-2 col-sm-2">
                  <div class="form-group">
                      <label for="CPF">CPF *</label>
                      <input type="text" class="form-control cpf" id="cpf" name="cpf" 
                        value="<?php echo $data['inscricao']->cpf; ?>"
                        placeholder="Seu cpf" />
                  </div>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4">
                  <div class="form-group">
                      <label for="Data de Nascimento">Data de Nascimento</label>
                      <input type="text" class="form-control data" id="dataNascimento" 
                        name="dataNascimento" value="<?php echo $data['inscricao']->dataNascimento; ?>"
                        placeholder="Sua data de nascimento" />
                  </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2">
                  <div class="form-group">
                      <label for="Sexo">Sexo</label>
                      <select class="form-control" name="sexo">
                        <option value="">--Selecione--</option>
                        <option value="Masculino"<?php if($data['inscricao']->sexo == "Masculino") echo ' selected'; ?>>Masculino</option>
                        <option value="Feminino"<?php if($data['inscricao']->sexo == "Feminino") echo ' selected'; ?>>Feminino</option>
                      </select>
                  </div>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4">
                  <div class="form-group">
                      <label for="Estado Civil">Estado Civil</label>
                      <select class="form-control" name="estadoCivil">
                        <option value="">--Selecione--</option>
                        <option value="Solteiro(a)"<?php if($data['inscricao']->estadoCivil == "Solteiro(a)") echo ' selected'; ?>>Solteiro(a)</option>
                        <option value="Casado(a)"<?php if($data['inscricao']->estadoCivil == "Casado(a)") echo ' selected'; ?>>Casado(a)</option>
                        <option value="Divorciado(a)"<?php if($data['inscricao']->estadoCivil == "Divorciado(a)") echo ' selected'; ?>>Divorciado(a)</option>
                        <option value="Viúvo(a)"<?php if($data['inscricao']->estadoCivil == "Viúvo(a)") echo ' selected'; ?>>Viúvo(a)</option>
                      </select>
                  </div>
              </div>
            </div>
            <div class="row inline hidden-xs">
              <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                  <label for="Naturalidade">Naturalidade</label>
                  <input class="form-control" name="naturalidade" size="70" placeholder="Sua naturalidade" 
                    type="text" value="<?php echo $data['inscricao']->naturalidade; ?>" />
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                  <label for="Profissão">Profissão</label>
                  <input class="form-control" name="profissao" size="90" placeholder="Sua profissão" 
                    type="text" value="<?php echo $data['inscricao']->profissao; ?>" />
                </div>
              </div>
            </div>
            <div class="row inline hidden-xs">
              <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                  <label for="Nome do pai">Nome do pai</label>
                  <input class="form-control" name="nomePai" size="90" placeholder="Nome do seu pai" 
                    type="text" value="<?php echo $data['inscricao']->nomePai; ?>" />
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                  <label for="Nome da mãe">Nome da mãe</label>
                  <input class="form-control" name="nomeMae" size="90" placeholder="Nome da sua mãe" 
                    type="text" value="<?php echo $data['inscricao']->nomeMae; ?>" />
                </div>
              </div>
            </div>
            <div class="row inline">
              <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                  <label for="E-mail">E-mail *</label>
                  <input class="form-control" size="60" name="email" placeholder="Seu melhor e-mail" type="text" 
                    value="<?php echo $data['inscricao']->email; ?>" />
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 hidden-xs">
                <div class="form-group">
                  <label for="E-mail Alternativo">E-mail Alternativo</label>
                  <input class="form-control" size="60" name="emailAlternativo" 
                    placeholder="E-mail alternativo" type="text" 
                    value="<?php echo $data['inscricao']->emailAlternativo; ?>" />
                </div>
              </div>
            </div>
            <div class="row inline">
              <div class="col-lg-2 col-md-2 col-sm-2">
                <div class="form-group">
                  <label for="Telefone Residencial">Telefone Residencial *</label>
                  <input class="form-control telefone" name="telefoneResidencial" 
                    type="text" value="<?php echo $data['inscricao']->telefoneResidencial; ?>" />
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2">
                <div class="form-group">
                  <label for="Operadora Celular">Operadora Celular</label>
                  <select class="form-control" name="operadoraCelular">
                    <option value="">--Selecione--</option>
                    <option value="Oi"<?php if($data['inscricao']->operadoraCelular == "Oi") echo ' selected'; ?>>Oi</option>
                    <option value="Claro"<?php if($data['inscricao']->operadoraCelular == "Claro") echo ' selected'; ?>>Claro</option>
                    <option value="Tim"<?php if($data['inscricao']->operadoraCelular == "Tim") echo ' selected'; ?>>Tim</option>
                    <option value="Vivo"<?php if($data['inscricao']->operadoraCelular == "Vivo") echo ' selected'; ?>>Vivo</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2">
                <div class="form-group">
                  <label for="Telefone Celular">Telefone Celular *</label>
                  <input id="telefoneCelular" class="form-control telefone" name="telefoneCelular" 
                    type="text" value="<?php echo $data['inscricao']->telefoneCelular; ?>" />
                </div>
              </div>
            </div>
          </fieldset>

          <fieldset>
              <legend><strong>Endereço</strong></legend>
              <div class="row hidden-xs">
                <div class="col-lg-4 col-md-4 col-sm-4">
                  <div class="form-group">
                    <label class="block" for="Cep">Cep</label>
                    <input class="form-control cep" name="cep" type="text" 
                      value="<?php echo $data['inscricao']->cep; ?>" placeholder="Cep" />
                  </div>
                </div>
              </div>
              <div class="row inline hidden-xs">
                <div class="col-lg-10 col-md-10 col-sm-10">
                  <div class="form-group">
                    <label for="Endereço">Endereço</label>
                    <input class="form-control autocomplete-cep" size="90" name="endereco" placeholder="Endereço" 
                      id="endereco" type="text" value="<?php echo $data['inscricao']->endereco; ?>" />
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2">
                  <div class="form-group">
                    <label for="Número">Número</label>
                    <input class="form-control" name="numero" placeholder="Número" type="text" 
                      value="<?php echo $data['inscricao']->numero; ?>" />
                  </div>
                </div>
              </div>
              <div class="row inline hidden-xs">
                <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="form-group">
                    <label for="Complemento">Complemento</label>
                    <input class="form-control" size="60" name="complemento" placeholder="Complemento" 
                      type="text" value="<?php echo $data['inscricao']->complemento; ?>" />
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="form-group">
                    <label for="Bairro">Bairro</label>
                    <input class="form-control autocomplete-cep" size="70" name="bairro" id="bairro" placeholder="Bairro" 
                      type="text" value="<?php echo $data['inscricao']->bairro; ?>" />
                  </div>
                </div>
              </div>
              <div class="row inline">
                <div class="col-lg-10 col-md-10 col-sm-10">
                  <div class="form-group">
                    <label for="Cidade">Cidade *</label>
                    <input class="form-control autocomplete-cep" size="80" name="cidade" id="cidade" placeholder="Cidade" 
                      type="text" value="<?php echo $data['inscricao']->cidade; ?>" />
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2">
                  <div class="form-group">
                    <label for="UF">UF *</label>
                    <select class="form-control autocomplete-cep" name="uf" id="uf">
                      <option value=""<?php if ($data['inscricao']->uf == "") echo ' selected'; ?>>--Selecione--</option>
                      <option value="AC"<?php if ($data['inscricao']->uf == "AC") echo ' selected'; ?>>AC</option>
                      <option value="AL"<?php if ($data['inscricao']->uf == "AL") echo ' selected'; ?>>AL</option>
                      <option value="AP"<?php if ($data['inscricao']->uf == "AP") echo ' selected'; ?>>AP</option>
                      <option value="AM"<?php if ($data['inscricao']->uf == "AM") echo ' selected'; ?>>AM</option>
                      <option value="BA"<?php if ($data['inscricao']->uf == "BA") echo ' selected'; ?>>BA</option>
                      <option value="CE"<?php if ($data['inscricao']->uf == "CE") echo ' selected'; ?>>CE</option>
                      <option value="DF"<?php if ($data['inscricao']->uf == "DF") echo ' selected'; ?>>DF</option>
                      <option value="ES"<?php if ($data['inscricao']->uf == "ES") echo ' selected'; ?>>ES</option>
                      <option value="GO"<?php if ($data['inscricao']->uf == "GO") echo ' selected'; ?>>GO</option>
                      <option value="MA"<?php if ($data['inscricao']->uf == "MA") echo ' selected'; ?>>MA</option>
                      <option value="MT"<?php if ($data['inscricao']->uf == "MT") echo ' selected'; ?>>MT</option>
                      <option value="MS"<?php if ($data['inscricao']->uf == "MS") echo ' selected'; ?>>MS</option>
                      <option value="MG"<?php if ($data['inscricao']->uf == "MG") echo ' selected'; ?>>MG</option>
                      <option value="PA"<?php if ($data['inscricao']->uf == "PA") echo ' selected'; ?>>PA</option>
                      <option value="PB"<?php if ($data['inscricao']->uf == "PB") echo ' selected'; ?>>PB</option>
                      <option value="PR"<?php if ($data['inscricao']->uf == "PR") echo ' selected'; ?>>PR</option>
                      <option value="PE"<?php if ($data['inscricao']->uf == "PE") echo ' selected'; ?>>PE</option>
                      <option value="PI"<?php if ($data['inscricao']->uf == "PI") echo ' selected'; ?>>PI</option>
                      <option value="RJ"<?php if ($data['inscricao']->uf == "RJ") echo ' selected'; ?>>RJ</option>
                      <option value="RN"<?php if ($data['inscricao']->uf == "RN") echo ' selected'; ?>>RN</option>
                      <option value="RS"<?php if ($data['inscricao']->uf == "RS") echo ' selected'; ?>>RS</option>
                      <option value="RO"<?php if ($data['inscricao']->uf == "RO") echo ' selected'; ?>>RO</option>
                      <option value="RR"<?php if ($data['inscricao']->uf == "RR") echo ' selected'; ?>>RR</option>
                      <option value="SC"<?php if ($data['inscricao']->uf == "SC") echo ' selected'; ?>>SC</option>
                      <option value="SP"<?php if ($data['inscricao']->uf == "SP") echo ' selected'; ?>>SP</option>
                      <option value="SE"<?php if ($data['inscricao']->uf == "SE") echo ' selected'; ?>>SE</option>
                      <option value="TO"<?php if ($data['inscricao']->uf == "TO") echo ' selected'; ?>>TO</option>
                    </select>
                  </div>
                </div>
              </div>
          </fieldset>

          <fieldset class="hidden-xs">
              <legend><strong>Formação Acadêmica</strong></legend>
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="form-group">
                    <label for="Formação Acadêmica">Formação Acadêmica</label>
                    <input class="form-control" name="formacao" placeholder="Sua formação acadêmica" 
                      type="text" value="<?php echo $data['inscricao']->formacao; ?>" />
                  </div>
                </div>
              </div>
              <div class="row inline">
                <div class="col-lg-9 col-md-9 col-sm-9">
                  <div class="form-group">
                    <label for="Faculdade/Universidade">Faculdade/Universidade</label>
                    <input class="form-control" size="80" name="instituicao" 
                      placeholder="Faculdade/Universidade onde concluiu sua formação" type="text" 
                      value="<?php echo $data['inscricao']->instituicao; ?>" />
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3">
                  <div class="form-group">
                    <label for="Ano de conclusão">Ano de conclusão</label>
                    <input maxlength="4" class="form-control" name="anoConclusao" 
                      placeholder="Ano de conclusão da formação" type="text" 
                      value="<?php echo $data['inscricao']->anoConclusao; ?>" />
                  </div>
                </div>
              </div>
          </fieldset>

          <?php if (count($data['unidades']) > 0) { ?>

          <fieldset>
            <input type="hidden" name="unidade" value="0" />
            <legend><strong>Unidades *</strong></legend>
            <span>Escolha a unidade onde você pretende cursar.</span>
              <?php
              foreach ($data['unidades'] as $key => $unidade) {
                echo '<div class="radio">';
                echo '<label>';
                echo '<input id="unidade' . $unidade->cidade . '" ';
                echo ' type="radio" name="unidade" value="' . $unidade->cidade . '" ';
                if ($data['inscricao']->unidade == $unidade->cidade) {
                  echo 'checked="checked"';
                }
                echo '/>';
                echo $unidade->nomeCidade;
                echo ' (' . $unidade->siglaEstado . ')';
                echo '</label></div>';
              }
            ?>
          </fieldset>

          <?php } ?>

          <fieldset class="hidden-xs">
            <legend><strong>Informações de pagamento</strong></legend>
            <span>Escolha o dia de pagamento que melhor se adeque as suas possibilidades.</span><br />
            <div class="radio-inline">
              <label>
                <input type="radio" name="diaPagamento" value="01"<?php if(isset($data['inscricao']->diaPagamento) && $data['inscricao']->diaPagamento == 1) echo ' checked="checked"'; ?> />01
              </label>
            </div>
            <div class="radio-inline">
              <label>
                <input type="radio" name="diaPagamento" value="05"<?php if(isset($data['inscricao']->diaPagamento) && $data['inscricao']->diaPagamento == 5) echo ' checked="checked"'; ?> />05
              </label>
            </div>
            <div class="radio-inline">
              <label>
                <input type="radio" name="diaPagamento" value="10"<?php if(isset($data['inscricao']->diaPagamento) && $data['inscricao']->diaPagamento == 10) echo ' checked="checked"'; ?> />10
              </label>
            </div>
            <div class="radio-inline">
              <label>
                <input type="radio" name="diaPagamento" value="15"<?php if(isset($data['inscricao']->diaPagamento) && $data['inscricao']->diaPagamento == 15) echo ' checked="checked"'; ?> />15
              </label>
            </div>
            <div class="radio-inline">
              <label>
                <input type="radio" name="diaPagamento" value="20"<?php if(isset($data['inscricao']->diaPagamento) && $data['inscricao']->diaPagamento == 20) echo ' checked="checked"'; ?> />20
              </label>
            </div>
            <div class="radio-inline">
              <label>
                <input type="radio" name="diaPagamento" value="25"<?php if(isset($data['inscricao']->diaPagamento) && $data['inscricao']->diaPagamento == 25) echo ' checked="checked"'; ?> />25
              </label>
            </div>
            <div class="radio-inline">
              <label>
                <input type="radio" name="diaPagamento" value="30"<?php if(isset($data['inscricao']->diaPagamento) && $data['inscricao']->diaPagamento == 30) echo ' checked="checked"'; ?> />30
              </label>
            </div>
          </fieldset>

          <fieldset>
            <legend><strong>Comprovante de depósito</strong></legend>
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                  <label class="sr-only" for="Comprovante de depósito">Comprovante de depósito</label>
                  <input type="file" name="comprovante" />
                </div>
              </div>
            </div>
          </fieldset>

          <fieldset>
            <legend><strong>Outras informações</strong></legend>
            <div class="row inline">
              <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                  <label for="Como você conheceu o IEFAP">Como você conheceu o IEFAP? *</label>
                  <select class="form-control" id="comoConheceu" name="comoConheceu">
                      <option value="">--Selecione--</option>
                      <option value="Folder/Cartaz"<?php if($data['inscricao']->comoConheceu == "Folder/Cartaz") echo ' selected'; ?>>Folder/Cartaz</option>
                      <option value="Out-Door"<?php if($data['inscricao']->comoConheceu == "Out-Door") echo ' selected'; ?>>Out-Door</option>
                      <option value="Bus-Door"<?php if($data['inscricao']->comoConheceu == "Bus-Door") echo ' selected'; ?>>Bus-Door</option>
                      <option value="TV"<?php if($data['inscricao']->comoConheceu == "TV") echo ' selected'; ?>>TV</option>
                      <option value="Rádio"<?php if($data['inscricao']->comoConheceu == "Rádio") echo ' selected'; ?>>Rádio</option>
                      <option value="Jornal"<?php if($data['inscricao']->comoConheceu == "Jornal") echo ' selected'; ?>>Jornal</option>
                      <option value="Google"<?php if($data['inscricao']->comoConheceu == "Google") echo ' selected'; ?>>Google</option>
                      <option value="Twitter"<?php if($data['inscricao']->comoConheceu == "Twitter") echo ' selected'; ?>>Twitter</option>
                      <option value="Facebook"<?php if($data['inscricao']->comoConheceu == "Facebook") echo ' selected'; ?>>Facebook</option>
                      <option value="Blogs"<?php if($data['inscricao']->comoConheceu == "Blogs") echo ' selected'; ?>>Blogs</option>
                      <option value="Site"<?php if($data['inscricao']->comoConheceu == "Site") echo ' selected'; ?>>Site</option>
                      <option value="Representante"<?php if($data['inscricao']->comoConheceu == "Representante") echo ' selected'; ?>>Representante</option>
                      <option value="Amigo/Parente/Aluno"<?php if($data['inscricao']->comoConheceu == "Amigo/Parente/Aluno") echo ' selected'; ?>>Amigo/Parente/Aluno</option>
                      <option value="Outros"<?php if($data['inscricao']->comoConheceu == "Outros") echo ' selected'; ?>>Outros</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6"<?php if($data['inscricao']->comoConheceu != "Amigo/Parente/Aluno") echo ' style="display:none;"'; ?> 
                id="nomeIndicou">
                <div class="form-group">
                  <label for="Nome da pessoa que indicou">Nome da pessoa que indicou</label>
                  <input type="text" name="nomeIndicou"<?php if($data['inscricao']->comoConheceu != "Amigo/Parente/Aluno") echo ' style="display:none;"'; ?>
                      class="form-control"
                      placeholder="Informe o nome da pessoa que indicou"
                      value="<?php echo $data['inscricao']->nomeIndicou; ?>"/>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                  <label for="Você foi atendido por algum dos nossos Representantes?">Você foi atendido por algum dos nossos Representantes?</label>
                  <select class="form-control" name="responsavel">
                    <option value="">--Selecione o Representante--</option>
                    <?php
                      foreach ($data['representantes'] as $representante) {
                        echo '<option';
                        if ($data['matricula']->responsavel == $representante->id) {
                          echo ' selected';
                        }
                        echo ' value="' . $representante->id . '">' . $representante->nome . '</option>';
                      }
                    ?>
                  </select>
                </div>
              </div>
            </div>
          </fieldset>

          <p class="text-info">
            <strong>
              1) Responsabilizo-me pela veracidade das informações incluídas neste documento. Caso elas não correspondam a verdade, tenho conhecimento da consequente anulação da inscrição.<br />
              2) O IEFAP reserva-se o direito de não realizar o curso ou prorrogar o período de inscrição, caso o número mínimo de vagas para fechamento da turma não seja preenchido.<br />
              3) A inscrição só será efetivada após a assinatura do contrato de prestação de serviços educacionais e da confirmação do recebimento da taxa de inscrição pelo IEFAP.
              </strong>
          </p>

          <div class="form-group">
              <input type="submit" class="btn btn-orange pull-left" value="Realizar inscrição"/>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
<!-- Inscrições -->

<script>
  $('#comoConheceu').on('change', function() {
    if ($(this).val() == "Amigo/Parente/Aluno") {
      $('#nomeIndicou').css('display', 'block');
      $('#nomeIndicou input').toggle();
      $('#nomeIndicou input').focus();
    }
    else {
      $('#nomeIndicou').css('display', 'none');
      $('#nomeIndicou input').toggle();
      $('#nomeIndicou input').css('display', 'none');
    }
  });
</script>