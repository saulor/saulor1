<li class="dropdown">
  <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle">Institucional <span class="caret"></span></a>
  <ul class="dropdown-menu">
    <li><a href="<?php echo SITEURL; ?>institucional">Sobre o IEFAP</a></li>
    <li><a href="<?php echo SITEURL; ?>unidades">Unidades</a></li>
    <li><a href="<?php echo SITEURL; ?>parceiros">Parceiros</a></li>
    <li><a href="<?php echo SITEURL; ?>trabalhe-conosco">Trabalhe Conosco</a></li>
  </ul>
</li>
<li class="dropdown">
  <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle">Pós-Graduação <span class="caret"></span></a>
  <ul class="dropdown-menu">
    <?php foreach($data['categoriasPos'] as $c) { ?>
    <li><a href="<?php echo SITEURL; ?>posgraduacao/<?php echo $c->slug; ?>"><?php echo $c->nome; ?></a></li>
    <?php } ?>
  </ul>
</li>
<li class="dropdown">
  <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle">Aperfeiçoamento Profissional <span class="caret"></span></a>
  <ul class="dropdown-menu">
    <?php foreach($data['categoriasApe'] as $c) { ?>
    <li><a href="<?php echo SITEURL; ?>aperfeicoamento-profissional/<?php echo $c->slug; ?>"><?php echo $c->nome; ?></a></li>
    <?php } ?>
  </ul>
</li>
<li><a href="<?php echo SITEURL; ?>ead">EAD</a></li>
<li><a href="<?php echo SITEURL; ?>contato">Contato</a></li>
<li class="hidden-xs"><a href="<?php echo SITEURL; ?>area-aluno">Área do Aluno</a></li>
<li><a href="<?php echo SITEURL; ?>noticias" role="button">Notícias</a></li>
<li class="dropdown search hidden-sm hidden-xs">
  <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
  <ul class="dropdown-menu">
    <li>
      <form method="get" action="<?php echo SITEURL;?>busca" class="form-inline">
          <div class="form-group">
            <label class="sr-only" for="Buscar">Buscar</label>
            <input type="text" name="search" id="search" placeholder="Buscar" class="form-control" />
            <button type="submit" class="btn btn-orange">Buscar</button>
          </div>
      </form>
    </li>
  </ul>
</li>