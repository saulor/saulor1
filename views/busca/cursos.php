<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/banner-default.jpg" />
  </div>
</section>
<!-- Banner -->

<!-- Busca -->
<section id="busca">
  <div class="wrap-section">
    <div class="container">
      
      <div class="row no-gutter">
        <div class="col-lg-12">
          <div class="navegacao-wrap hidden-xs">
            <ol class="breadcrumb">
              <li><a href="<?php echo SITEURL; ?>">Início</a></li>
              <li class="active">Resultados da busca</li>
            </ol>
          </div>
        </div>
      </div> 

      <div class="row">
          <div class="col-lg-12">
           <div class="heading internas" id="topo">
              <h1>Resultados da busca</h1>
            </div>
          </div>
      </div>

      <br />

      <style>
        select {
            max-width: 246px;
        }
      </style>

      <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
          <form class="form-inline">
            <div class="form-group">
              <select name="estado" class="form-control load-cursos">
                  <option value="" selected>Selecione seu Estado</option>
                  <option value="AC">AC</option>
                  <option value="AL">AL</option>
                  <option value="AP">AP</option>
                  <option value="AM">AM</option>
                  <option value="BA">BA</option>
                  <option value="CE">CE</option>
                  <option value="DF">DF</option>
                  <option value="ES">ES</option>
                  <option value="GO">GO</option>
                  <option value="MA">MA</option>
                  <option value="MT">MT</option>
                  <option value="MS">MS</option>
                  <option value="MG">MG</option>
                  <option value="PA">PA</option>
                  <option value="PB">PB</option>
                  <option value="PR">PR</option>
                  <option value="PE">PE</option>
                  <option value="PI">PI</option>
                  <option value="RJ">RJ</option>
                  <option value="RN">RN</option>
                  <option value="RS">RS</option>
                  <option value="RO">RO</option>
                  <option value="RR">RR</option>
                  <option value="SC">SC</option>
                  <option value="SP">SP</option>
                  <option value="SE">SE</option>
                  <option value="TO">TO</option>
                </select>
            </div>
            <div class="form-group">
              <select name="categoria" class="form-control load-cursos">
                <option value="" selected>Escolha a Categoria</option>
                <?php foreach($data['categorias'] as $c) { ?>
                  <option<?php if (isset($_GET['categoria']) && $_GET['categoria'] == $c->slug) echo ' selected'; ?> value="<?php echo $c->slug; ?>"><?php echo $c->nome; ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group">
              <select name="curso" id="select-cursos" class="form-control">
                <option value="" selected>Escolha o Curso</option>
                <?php foreach ($data['cursos'] as $curso) { ?>
                <option<?php if (isset($_GET['curso']) && $_GET['curso'] == $curso->link) echo ' selected'; ?> value="<?php echo $curso->link; ?>"><?php echo $curso->nome; ?></option>
                <?php } ?>
              </select>
            </div>
            <input type="submit" class="btn btn-orange" value="Buscar"/>
          </form>

          <br />

          <?php echo $data["pageLinks"]; ?>

          <?php foreach ($data["resultados"] as $key => $resultado) { ?>
            <div class="resultado">
              <h3>
                <u>
                  <a href="<?php echo $resultado['url']; ?>"><?php echo $resultado['titulo']; ?></a>
                </u>
              </h3>
              <div>
                <p><?php echo strip_tags($resultado['descricao']); ?></p>
                <a href="<?php echo $resultado['url']; ?>"><small><?php echo $resultado['url']; ?></small></a>
              </div>
            </div>
          <?php } ?>

          <?php if(count($data['resultados']) > 15) { ?>
          <a href="#topo">
            <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span> topo
          </a>
          <?php } ?>

          <?php echo $data["pageLinks"]; ?>

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
<!-- Busca -->