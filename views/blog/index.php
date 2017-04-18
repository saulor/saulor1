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

      <?php if (!empty($data['slugUnidade'])) { ?>
      <div class="row no-gutter">
        <div class="col-lg-12">
          <div class="navegacao-wrap hidden-xs">
            <ol class="breadcrumb">
              <li><a href="<?php echo SITEURL; ?>blog">Blog</a></li>
              <li><?php echo Blog::getUnidade($data['slugUnidade']); ?></li>
            </ol>
          </div>
        </div>
      </div>
      <?php } ?>

      <div class="row">
          <div class="col-lg-12">
           <div class="heading internas" id="topo">
              <h1>Blog do IEFAP</h1>
              <span class="bottomline"></span>
            </div>
          </div>
      </div>

      <br />

      <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
          <?php if (count($data['posts']) == 0) { ?>
          <p style="font-family:Medium;">Nenhum post encontrado<?php if (!empty($data['slugUnidade'])) { echo ' para esta unidade'; } ?>!</p>
          <?php } else { ?>

          <?php foreach ($data['posts'] as $post) { ?>
          <article class="post">
            <header class="entry-header">
              <h2><?php echo $post->titulo; ?></h2>
              <span class="bottomline <?php echo $post->slugUnidade; ?>"></span>
              <div class="entry-byline">
                <span class="entry-city <?php echo $post->slugUnidade; ?>"><a href="<?php echo SITEURL; ?>blog/<?php echo $post->slugUnidade; ?>">Unidade <?php echo $post->unidade; ?></a></span>
                <time class="entry-published" datetime="<?php echo date('Y-m-d', $post->timestamp);?>T<?php echo date('H:i:s', $post->timestamp); ?>" title="<?php echo $post->dataExtenso; ?>"><?php echo $post->dataExtenso; ?></time>
              </div>
            </header>
            <div class="entry-summary">
              <?php echo $post->conteudo; ?>
            </div>
            <?php if ($post->tags) { ?>
            <div class="entry-tags">
              <?php
                $tags = array_map('trim', explode(',', $post->tags));
                foreach ($tags as $tag) {
                  echo '<i class="ico-tags"></i> <a href="#">' . $tag . '</a>';
                }
              ?>
            </div>
            <?php } ?>
            <hr />
          </article>
          <?php }} ?>

          <?php if ($data['quantidade'] >= 5) { ?>
          <a href="#topo">
            <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span> topo
          </a>
          <?php } ?>

          <?php echo $data['pageLinks']; ?>

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