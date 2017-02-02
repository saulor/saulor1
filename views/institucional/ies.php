<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/institucional.png" />
  </div>
</section>
<!-- Banner -->

<!-- IES  -->
<section id="ies">
  <div class="wrap-section">
    <div class="container">

      <div class="row no-gutter">
        <div class="col-lg-12">
          <div class="navegacao-wrap hidden-xs">
            <ol class="breadcrumb">
              <li><a href="<?php echo SITEURL; ?>">Início</a></li>
              <li class="active">Instituições de Ensino Superior</li>
            </ol>
          </div>
        </div>
      </div> 

      <div class="row">
      	<div class="col-lg-12">
      		<div class="heading internas">
      			<h1>Institucional</h1>
      			<h2>Instituições de Ensino Superior</h2>
      			<span class="bottomline"></span>
      		</div>
      	</div>
      </div>

      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 visible-lg visible-md">
          <br />
          <a target="_blank" title="Clique para ver o site da Universidade Anhembi Morumbi"
            href="http://portal.anhembi.br">
            <img alt="Universidade Anhembi Morumbi" class="img-rounded img-responsive" 
              src="https://upload.wikimedia.org/wikipedia/pt/archive/b/bc/20160728020401!Universidade_Anhembi_Morumbi.png" />
          </a>
        </div>
        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
          <h6 class="h4 subtitle">Universidade Anhembi Morumbi</h6>
          <?php echo $data["anhembi"]->conteudo; ?>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 visible-lg visible-md">
          <br />
          <a target="_blank" title="Clique para ver o site da FMU"
            href="http://portal.fmu.br">
            <img alt="Faculdades Metropolitanas Unidas" class="img-rounded img-responsive" 
              src="https://upload.wikimedia.org/wikipedia/commons/e/e6/Logo_fmu.jpg" />
          </a>
        </div>
        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
          <h6 class="h4 subtitle">FMU – Faculdades Metropolitanas Unidas</h6>
          <?php echo $data["fmu"]->conteudo; ?>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 visible-lg visible-md">
          <br />
          <a target="_blank" title="Clique para ver o site da FMU"
            href="http://unp.br">
            <img alt="Universidade Potiguar" class="img-rounded img-responsive" 
              src="http://www.minhapos.com.br/data/artigos/images/logomarca%20unp.jpg" />
          </a>
        </div>
        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
          <h6 class="h4 subtitle">UNP - Universidade Potiguar</h6>
          <?php echo $data["unp"]->conteudo; ?>
        </div>
      </div>

      <div class="row">
      	<div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 visible-lg visible-md">
      		<a target="_blank" title="Clique para ver o site da Faculdade Maurício de Nassau"
      			href="http://www.mauriciodenassau.edu.br">
      			<img alt="Faculdade Maurício de Nassau" class="img-rounded img-responsive" src="http://www.minhapos.com.br/data/artigos/images/logo%20uninassau.jpg" />
      		</a>
      	</div>
      	<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
      		<h6 class="h4 subtitle">Centro Universitário Maurício de Nassau - Uninassau</h6>
      		<?php echo $data["mns"]->conteudo; ?>
      	</div>
      </div>

      <div class="row">
      	<div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 visible-lg visible-md">
      		<a target="_blank" title="Clique para ver o site da Uningá"
	      		href="http://www.uninga.com.br">
      			<img alt="Uningá" class="img-rounded img-responsive" src="http://uninga.br/logo/uninga.png" />
	      	</a>
      	</div>
      	<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
      		<h6 class="h4 subtitle">Centro Universitário Ingá - Uningá</h6>
	      	<?php echo $data["uni"]->conteudo; ?>
      	</div>
	  </div>

	  <div class="row">
	  	<div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 visible-lg visible-md">
	  		<a target="_blank" title="Clique para ver o site das Faculdades Integradas de Patos" 
          href="http://www.fiponline.edu.br">
	  			<img alt="Faculdades Integradas de Patos" class="img-rounded img-responsive" 
            src="http://pos.fiponline.edu.br/images/logo.png" />
	  		</a>
	  	</div>
	  	<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
	  		<h6 class="h4 subtitle">Faculdades Integradas de Patos - FIP</h6>
	  		<?php echo $data["fip"]->conteudo; ?>
	  	</div>
	  </div>

    </div>
  </div>
</section>
<!-- IES  -->
