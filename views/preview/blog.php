<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/blog.png" />
  </div>
</section>
<!-- Banner -->
<?php
View::css([
    Url::templatePath() . 'css/glyph.min.css'
]);
?><!-- Inscrições  -->
<section id="blog">
  <div class="wrap-section">
    <div class="container">

      <div class="row">
          <div class="col-lg-12">
           <div class="heading internas">
              <h1>Blog do IEFAP</h1>
              <span class="bottomline"></span>
            </div>
          </div>
      </div>

      <br />

      <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
          <article class="post">
            <header class="entry-header">
              <h2><?php echo $data['objeto']->titulo; ?></h2>
              <span class="bottomline <?php echo $data['objeto']->slugUnidade; ?>"></span>
              <div class="entry-byline">
                <span class="entry-city <?php echo $data['objeto']->slugUnidade; ?>"><a href="<?php echo SITEURL; ?>blog/<?php echo $data['objeto']->slugUnidade; ?>">Unidade <?php echo $data['objeto']->unidade; ?></a></span>
                <time class="entry-published" datetime="<?php echo date('Y-m-d', $data['objeto']->timestamp);?>T<?php echo date('H:i:s', $data['objeto']->timestamp); ?>" title="<?php echo $data['objeto']->dataExtenso; ?>"><?php echo $data['objeto']->dataExtenso; ?></time>
              </div>
            </header>
            <div class="entry-summary">
              <?php echo $data['objeto']->conteudo; ?>
            </div>
            <?php if ($data['objeto']->tags) { ?>
            <div class="entry-tags">
              <?php
                $tags = array_map('trim', explode(',', $data['objeto']->tags));
                foreach ($tags as $tag) {
                  echo '<i class="ico-tags"></i> <a href="#">' . $tag . '</a>';
                }
              ?>
            </div>
            <?php } ?>
            <hr />
          </article>
        </div>
        <div class="col-lg-4 col-md-4 hidden-xs">
          <div class="blog-unidades">
            <h2>Unidades</h2>
            <ul>
              <li><a href="<?php echo SITEURL; ?>blog/maringa">Maringá</a></li>
              <li><a href="<?php echo SITEURL; ?>blog/londrina">Londrina</a></li>
              <li><a href="<?php echo SITEURL; ?>blog/teresina">Teresina</a></li>
              <li><a href="<?php echo SITEURL; ?>blog/belem">Belém</a></li>
              <li><a href="<?php echo SITEURL; ?>blog/cascavel">Cascavel</a></li>
              <li><a href="<?php echo SITEURL; ?>blog/campinas">Campinas</a></li>
              <li><a href="<?php echo SITEURL; ?>blog/fortaleza">Fortaleza</a></li>
              <li><a href="<?php echo SITEURL; ?>blog/salvador">Salvador</a></li>
              <li><a href="<?php echo SITEURL; ?>blog/brasilia">Brasília</a></li>
              <li><a href="<?php echo SITEURL; ?>blog/curitiba">Curitiba</a></li>
              <li><a href="<?php echo SITEURL; ?>blog/porto-alegre">Porto Alegre</a></li>
            </ul>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
<!-- Inscrições -->