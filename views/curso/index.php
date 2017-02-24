<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/banner-default.jpg" />
  </div>
</section>
<!-- Banner -->

<!-- Curso -->
<section id="curso">
  <div class="wrap-section">
    <div class="container">
      <div class="row">
        <div class="navegacao-wrap hidden-xs">
          <div class="col-lg-12">
            <ol class="breadcrumb">
              <li><a href="<?php echo SITEURL; ?>">Início</a></li>
              <li><a href="<?php echo SITEURL; echo Curso::getSlug($data['curso']->tipo);?>"><?php echo Curso::getTipo($data['curso']->tipo); ?></a></li>
              <?php 
              $url = SITEURL . 'posgraduacao';
              foreach ($data['categorias'] as $categoria) { 
                $url .= '/' . $categoria->slug;
                echo '<li><a href="' . $url . '">' . $categoria->nome . '</a></li>';
              } 
              ?>
              <li class="active"><?php echo $data['curso']->nome; ?></li>
            </ol>
          </div>
        </div>
      </div>
      
      <br class="hidden-xs" /><br class="hidden-sm" />
      
      <div class="row">
        <article id="curso-<?php echo $data['curso']->id; ?>" class="interna">
          <div class="col-lg-4 col-md-4 col-sm-4 hidden-xs">
            <?php 
              $file = DIR_UPLOADS . DS . 'cursos' . DS . $data['curso']->id . DS . $data['curso']->thumbnail;
              if (is_file($file)) {
                $path = Url::resourcePath() . 'uploads' . DS . 'cursos' . DS . $data['curso']->id . DS . $data['curso']->thumbnail;
              } else { 
                // imagem default
                $src = Url::templatePath() . 'images/cursos/thumbnail-default%d.png';
                $path = sprintf($src, mt_rand (1, 2));
              } 
              echo sprintf('<img class="img-responsive" src="%s" />', $path);
            ?>
          </div>
          <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <header>
                <hgroup>
                  <h2><?php echo $data['curso']->nomeCategoria; ?></h2>
                  <span class="bottomline h2"></span>
                  <h1><?php echo $data['curso']->nome; ?></h1>
                  <span class="bottomline h1"></span>
                </hgroup>
            </header>
            <div class="clearfix">&nbsp;</div>
            <?php echo htmlspecialchars_decode($data['curso']->descricao); ?>
            <div class="clearfix">&nbsp;</div>
            <a class="btn btn-orange text-uppercase scrollTo" href="#interesse" 
              role="button">Tenho interesse</a>
            <br /><br />
            <div class="fb-like" 
              data-href="<?php echo SITEURL . 'curso/' . $data['curso']->link; ?>"  
              data-layout="button" data-action="recommend" data-size="small" 
              data-show-faces="false" data-share="true"></div>
          </div>
        </artice>
      </div>
    </div>
  </div>
</section>
<!-- Curso -->

<!-- Curso informações -->
<section id="curso-informacoes">
  <div class="wrap-section curso-informacoes-wrap grafismo">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

          <?php if (!empty($data['curso']->objetivosGerais)) { ?>
          <div class="curso-informacao">
            <div class="heading heading4">
              <h1 class="text-uppercase">Objetivos</h1>
              <span class="bottomline"></span>
            </div>
            <?php echo htmlspecialchars_decode($data['curso']->objetivosGerais); ?>
          </div>
          <?php } ?>
          
          <?php if (!empty($data['curso']->publicoAlvo)) { ?>
          <div class="curso-informacao">
            <div class="heading heading4">
              <h1 class="text-uppercase">Público Alvo</h1>
              <span class="bottomline"></span>
            </div>
            <?php echo htmlspecialchars_decode($data['curso']->publicoAlvo); ?>
          </div>
          <?php } ?>

        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

          <?php if (!empty($data['curso']->disciplinas)) { ?>
          <div class="curso-informacao">
            <div class="heading heading4">
              <h1 class="text-uppercase">Disciplinas</h1>
              <span class="bottomline"></span>
            </div>
            <?php echo htmlspecialchars_decode($data['curso']->disciplinas); ?>
          </div>
          <?php } ?>

          <?php if (!empty($data['curso']->professores)) { ?>
          <div class="curso-informacao">
            <div class="heading heading4">
              <h1 class="text-uppercase">Professores</h1>
              <span class="bottomline"></span>
            </div>
            <?php echo htmlspecialchars_decode($data['curso']->professores); ?>
           </div>
           <?php } ?>

        </div>
      </div>
    </div>
  </div>
</section>
<!-- Curso informações -->

<section id="interesse">
  <div class="wrap-section preinscricoes-wrap">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="heading heading2">
            <h1 class="text-uppercase">Interessou? <br class="visible-xs"/>Faça sua pré-inscrição!</h1>
            <span class="bottomline"></span>
          </div>
          <p>Aproveite esta oportunidade e faça já a sua pré-inscrição preenchendo o formulário.</p>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12">
          <form id="preinscricoes-form">
            <div class="col-xs-12" id="ajax-message"></div>
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
              <div class="form-group">
                <label for="Seu nome">Seu nome</label>
                <input class="form-control property" required type="text" 
                  placeholder="Informe seu nome" id="nome" 
                  name="nome" />
              </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
              <div class="form-group">
                <label for="Seu melhor e-mail">Seu melhor e-mail</label>
                <input class="form-control property" size="60" required id="email" name="email" 
                  placeholder="Informe seu melhor e-mail" type="text" />
              </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
              <div class="form-group">
                <label for="Em que unidade pretende cursar?">Unidade onde pretende cursar</label>
                <select class="form-control property" id="unidade" name="unidade" required>
                  <option value="">Selecione</option>
                  <?php foreach ($data['unidades'] as $unidade) { ?>
                  <option value="<?php echo $unidade->idCidade; ?>"><?php echo $unidade->nomeCidade . ' (' . $unidade->siglaEstado . ')'; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
              <div class="form-group">
                <label for="Seu telefone">Seu telefone</label>
                <input class="form-control property telefone" id="telefone" name="telefone" 
                  type="text" required />
                <div class="checkbox" style="padding-top:0;">
                  <label title="Marque se este telefone for ativo para Whatsapp" >
                    <input id="whatsapp" type="checkbox" 
                      name="whatsapp" /> <small>Whatsapp?</small>
                  </label>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
              <div class="form-group">
                <label for="Seu telefone">Melhor horário para ligação</label>
                <select id="horario" name="horario" class="form-control property" 
                  title="Informe o melhor horário para que nós possamos entrar em contato com você"
                  required>
                    <option value="">Selecione</option>
                    <option value="08h às 11h">08h às 11h</option>
                    <option value="11h às 15h">11h às 15h</option>
                    <option value="15h às 19h">15h às 19h</option>
                </select>
              </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-5">
              <div class="form-group">
                <label for="Como você conheceu o IEFAP">Como você conheceu o IEFAP?</label>
                <select class="form-control property" id="comoConheceu" name="comoConheceu">
                    <option value="">Selecione</option>
                    <option value="Folder/Cartaz">Folder/Cartaz</option>
                    <option value="Out-Door">Out-Door</option>
                    <option value="Bus-Door">Bus-Door</option>
                    <option value="TV">TV</option>
                    <option value="Rádio">Rádio</option>
                    <option value="Jornal">Jornal</option>
                    <option value="Google">Google</option>
                    <option value="Twitter">Twitter</option>
                    <option value="Facebook">Facebook</option>
                    <option value="Blogs">Blogs</option>
                    <option value="Site">Site</option>
                    <option value="Representante">Representante</option>
                    <option value="Amigo/Parente/Aluno">Amigo/Parente/Aluno</option>
                    <option value="Outros">Outros</option>
                </select>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
              <div class="form-group">
                <input type="button" class="btn btn-orange" 
                  id="quero-fazer-button" value="Quero fazer minha pré-inscrição" disabled/>
              </div>
            </div>
            <input type="hidden" name="curso" id="curso" value="<?php echo $data['curso']->id; ?>"
              class="property" />
          </form>
        </div>
      </div>
    </div>  
  </div>
</section>

<section id="inscricao">
  <div class="wrap-section">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="heading heading2">
            <h1 class="text-uppercase">Este é o curso que procurava? <br class="visible-xs"/>Faça agora sua inscrição!</h1>
            <span class="bottomline"></span>
          </div>
        </div>
      </div>
      <br />
      <div class="row">
        <div class="col-lg-12">
          <p>Para efetivar sua inscrição no curso <?php echo $data['curso']->nome; ?> é só <strong><a href="<?php echo SITEURL; ?>inscricao/<?php echo $data['curso']->link; ?>">CLICAR AQUI</a></strong> ou ligar agora para <strong>0800 501 6000</strong> e falar com um de nossos consultores.</p>
        </div>
      </div>
    </div>  
  </div>
</section>

<?php 
  if ($data['outros']) {
?>
<section id="conheca-tambem">
  <div class="wrap-section">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="heading heading2">
            <h1 class="text-uppercase">Conheça também</h1>
            <span class="bottomline"></span>
          </div>
        </div>
      </div>
      <?php if ($data['outros']) { ?>
      <div class="row">
        <?php 
            $cursos = $data['outros'];
            include('views/includes/list-cursos.php'); 
          ?>
      </div>
      <?php } ?>
    </div>
  </div>
</section>
<?php
  }
?>

<script>

  function valida (form) {
    var validated = true;
    var data = $(form).serializeArray();
    $('#preinscricoes-form #ajax-message').children().remove();
    var campos = [];
    for (var input in data) {
      var element = $(form).find("[name='" + data[input]['name'] + "']");
      var required = typeof($(element).attr('required')) !== 'undefined';
      if (required && data[input]['value'] == '') {
        campos.push($(element).siblings('label').text());
      }
    }
    if (campos.length > 0) {
      var text = '';
      if (campos.length > 1) {
        text = 'Os campos ' + campos.join(', ') + ' são obrigatórios';
      }
      else {
        text = 'O campo ' + campos.join(', ') + ' é obrigatório';
      }
      $('<div/>', {
          class : 'alert alert-danger',
          text : text
      }).appendTo('#preinscricoes-form #ajax-message');
      return false;
    }
    return true;
  }

  function getDados (form) {
    var data = $(form).serializeArray();
    var dados = {};
    for (var input in data) {
      var element = $(form).find("[name='" + data[input]['name'] + "']");
      var isField = $(element).hasClass('property');
      if (isField) {
        dados[data[input]['name']] = data[input]['value'];
      }
    }
    return dados;
  }

  $('#quero-fazer-button').on('click', function(ev) {
    ev.preventDefault();
    var form = $(this).parents('form');
    var button = $(this);
    
    // validação
    if (!valida(form)) {
     return;
    }

    var dados = getDados(form);

    $('#preinscricoes-form #ajax-message').children().remove();
    $(button).attr('disabled', 'disabled');
    // Processando...
    $('<div/>', {
      class : 'alert alert-info',
        text : 'Processando aguarde...'
    }).appendTo('#preinscricoes-form #ajax-message');

    $.ajax({
      type: 'POST',
      url: '<?php echo SITEURL; ?>ajax/preinscricao',
      data: { dados },
      dataType: 'json',
      timeout: 5000,
      success: function (response) {
        $('#preinscricoes-form #ajax-message').children().remove();
        var div = $('<div/>', {
            class : 'alert alert-' + response.type,
        });
        $(div).append(response.message);
        $('#preinscricoes-form #ajax-message').append(div);
        if (parseInt(response.code) == 1) {
          $('input[type=text]', '#preinscricoes-form').val('');
          $('input[type=checkbox]', '#preinscricoes-form').prop('checked', false);
          $('select', '#preinscricoes-form').val('');
        }
        $(button).removeAttr('disabled');
        goog_report_conversion('<?php echo SITEURL; ?>curso/<?php echo $curso->link; ?>');
      },
      error: function (xhr, type) {
        $('#preinscricoes-form #ajax-message').children().remove();
        $('<div/>', {
          class : 'alert alert-danger',
          text : 'Ocorreu um erro no processamento da sua pré-inscrição. Por favor, tente novamente. Se o problema persistir entre em contato conosco pelo e-mail contato@iefap.com.br ou através do formulário de contato do site.'
        }).appendTo('#preinscricoes-form #ajax-message');
        $(button).removeAttr('disabled');
      }
    });
  });

  <?php
    // Facebook conversion code
    $campanha = APPDIR . 'templates' . DS . TEMPLATE . DS . 'assets' . DS . 'js' . DS;
    $campanha .= 'campanhas' . DS . $data['curso']->link . '.js';
    if (is_file($campanha)) {
  ?>
  function addCampanhaOnLoad() {
      var element = document.createElement('script');
      element.src = '<?php echo Url::templatePath() ?>js/campanhas/<?php echo $data["curso"] ->link; ?>.js';
      document.body.appendChild(element);
    }
    if (window.addEventListener) {
      window.addEventListener('load', addCampanhaOnLoad, false);
    }
    else if (window.attachEvent) {
      window.attachEvent('onload', addCampanhaOnLoad);
    }
    else {
      window.onload = addCampanhaOnLoad;
    }
  <?php } ?>

  // Load adwords function reports
  function addAdwords() {
    var element = document.createElement('script');
    element.src = '<?php echo Url::templatePath() ?>js/adwords.js';
    document.body.appendChild(element);
    $('#quero-fazer-button').removeAttr('disabled');
  }
  if (window.addEventListener) {
    window.addEventListener('load', addAdwords, false);
  }
  else if (window.attachEvent) {
    window.attachEvent('onload', addAdwords);
  }
  else {
    window.onload = addAdwords;
  }
</script>