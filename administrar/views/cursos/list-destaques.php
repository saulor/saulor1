<?php 
foreach($cursos as $key => $curso) { 
  $file = DIR_UPLOADS . DS . 'cursos' . DS . $curso->id . DS . $curso->thumbnail;
  if (is_file($file)) {
    $srcThumbnail = Url::resourcePath() . 'uploads/cursos/' . $curso->id . '/' . $curso->thumbnail;
  }
  else {
    $src = Url::templatePath() . 'images/cursos/thumbnail-default%d.jpg';
    $srcThumbnail = sprintf($src, mt_rand (1, 2));
  }
?>

<ul class="sortable">
  <li id="c-<?php echo $curso->id; ?>">
    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
    <article class="preview curso destaque">
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 hidden-xs">
          <img class="img-responsive" alt="Thumbnail <?php echo $curso->nome; ?>" title="" 
            src="<?php echo $srcThumbnail; ?>" />
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <header>
              <hgroup>
                <h2><?php echo $curso->nomeCategoria; ?></h2>
                <h1><?php echo $curso->nome; ?></h1>
              </hgroup>
          </header>
          <?php if (!empty($curso->publicoAlvo) || !empty($curso->publicoAlvoResumo)) { ?>
          <div class="curso-informacao">
            <span class="label">Público Alvo:</span>
            <span><?php 
              if (!empty($curso->publicoAlvoResumo)) {
                $texto = $curso->publicoAlvoResumo; 
              } 
              else {
                $texto = $curso->publicoAlvo; 
              } 
              echo FuncoesSite::compactaTexto(FuncoesSite::cleanString($texto), 100); 
            ?></span>
          </div>
          <?php } ?>
        </div>
      </div>
    </article>
  </div></li>
</ul>
<?php

if ($key%1==0) {
  echo '<div class="clearfix visible-xs-block"></div>';
} 

if (($key+1)%3==0) {
  echo '<div class="clearfix visible-sm-block"></div>';
} 

if (($key+1)%4==0) {
  echo '<div class="clearfix visible-md-block"></div>';
  echo '<div class="clearfix visible-lg-block"></div>';
} 
      } 
?> 