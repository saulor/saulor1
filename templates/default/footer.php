
</div>


  <!-- Footer -->
  <footer class="footer">
    <div class="marca-wrap">
      <img class="center-block" src="<?php echo Url::templatePath(); ?>/images/marca-footer.png" />
    </div>
    <div class="social-wrap text-center">
      <ul class="list-inline">
        <li class="facebook">
          <a target="_blank" href="https://www.facebook.com/pages/IEFAP-P%C3%B3s-Gradua%C3%A7%C3%A3o/291095547586274?fref=ts">Facebook</a>
        </li>
        <li target="_blank" class="youtube">
          <a href="https://www.youtube.com/channel/UCQ8HmB-8FFTGFTx2ai4BQWA">Youtube</a>
        </li>
        <li target="_blank" class="twitter">
          <a href="https://twitter.com/iefap">Twitter</a>
        </li>
        <li target="_blank" class="linkedin">
          <a href="https://www.linkedin.com/in/iefap-p%C3%B3s-gradua%C3%A7%C3%A3o-b1619aa8">Linkedin</a>
        </li>
        <li target="_blank" class="instagram">
          <a href="http://www.instagram.com/iefap.pos">Instagram</a>
        </li>
      </ul>
    </div>
    <div class="menu-wrap text-center clearfix">
      <nav class="center-block">
        <ul class="list-inline">
            <li><a href="<?php echo SITEURL; ?>">Início</a></li>
            <li><a href="<?php echo SITEURL; ?>institucional">Institucional</a></li>
            <li><a href="<?php echo SITEURL; ?>posgraduacao">Pós-Graduação</a></li>
            <li><a href="<?php echo SITEURL; ?>aperfeicoamento-profissional">Aperfeiçoamento Profissional</a></li>
            <li class="hidden-xs"><a target="_blank" href="http://www.eadlaureate.com.br">EAD</a></li>
            <li><a href="<?php echo SITEURL; ?>contato">Contato</a></li>
            <li class="hidden-xs"><a href="<?php echo SITEURL; ?>noticias">Notícias</a></li>
            <li class="hidden-xs"><a href="<?php echo SITEURL; ?>area-aluno">Área do Aluno</a></li>                   
        </ul>
      </nav>
    </div>
    <p class="hidden-xs copyright text-center clearfix">Todos os direitos reservados ao Instituto de Ensino, Formação e Aperfeiçoamento LTDA.</p>
    <p class="visible-xs copyright text-center clearfix" style="margin-bottom:10px;">Todos os direitos reservados ao IEFAP</p>
  </footer>
  <!-- Footer -->

</body>

<?php
View::js(array(
    Url::templatePath() . 'js/main.js',
    Url::templatePath() . 'js/funcoes.js'
));
?>
    <script>

      // paginação dos cursos
      $('.navegacao-wrap').on('click', '.btn-page:not(.disabled)', function(ev){
        ev.preventDefault();
        var container = $('#preview-cursos-container');
        var btn = $(this);
        var paginacao = $('.cursos-paginacao');
        $(paginacao).find('.rolling').removeClass('hidden');
        var p = parseInt($(paginacao).attr('data-pagina'));
        var tipo = parseInt($(paginacao).attr('data-tipo'));
        var categoria = typeof($(paginacao).attr('data-categoria')) !== 'undefined' ? $(paginacao).attr('data-categoria') : 0;
        var inicio = 1;
        var fim = parseInt($(paginacao).attr('data-fim'));
        p = $(btn).hasClass('next') ? p+1 : p-1;
        $(paginacao).attr('data-pagina', p);

        if (p > inicio) {
          $('.btn-page.prev').removeClass('disabled');
        }
        if (p == inicio) {
          $('.btn-page.prev').addClass('disabled');
        }
        if (p < fim) {
          $('.btn-page.next').removeClass('disabled');
        }
        if (p == fim) {
          $('.btn-page.next').addClass('disabled');
        }

        $.ajax({
          type: 'POST',
          url: '<?php echo SITEURL; ?>ajax/cursos',
          data: { tipo: tipo, categoria: categoria, p: p },
          timeout: 2000,
          success: function(data){
            $(container).children().fadeTo('slow', 0.4, function() {
              $(container).children().remove();
              $(container).html(data);
              $(btn).attr('data-pagina', parseInt(p)+1);
              $('.cursos-btn-next').attr('data-pagina', parseInt(p)+1);
              $(paginacao).find('.rolling').addClass('hidden');
            });
          },
          error: function(xhr, type){

          }
        });
      });

      // paginação all
      $('.navegacao-wrap').on('click', '.btn-all:not(.disabled)', function(ev){
        ev.preventDefault();
        var container = $('#preview-cursos-container');
        var btn = $(this);
        var paginacao = $('.cursos-paginacao');
        var tipo = parseInt($(paginacao).attr('data-tipo'));
        var categoria = typeof($(paginacao).attr('data-categoria')) !== 'undefined' ? $(paginacao).attr('data-categoria') : 0;
        $(paginacao).find('.rolling').removeClass('hidden');
        $('.btn-page.prev').addClass('disabled');
        $('.btn-page.next').addClass('disabled');

        $.ajax({
          type: 'POST',
          url: '<?php echo SITEURL; ?>ajax/cursos',
          data: { tipo: tipo, categoria : categoria},
          timeout: 2000,
          success: function(data){
            $(container).children().fadeTo('slow', 0.4, function() {
              $(container).children().remove();
              $(container).html(data);
              $(btn).addClass('disabled');
              $(paginacao).find('.rolling').addClass('hidden');
            });
          },
          error: function(xhr, type){

          }
        });
      });

      $('.cep').on('blur', function(){
        var parent = $(this).parent();
        var cep = $(this).val().replace(/-/g, '');
        $('#cep-message').remove();
        $('.autocomplete-cep').attr('disabled', 'disabled');
        $('.autocomplete-cep').val('');
        var loader = $('<img/>', {
            src: '<?php echo Url::templatePath(); ?>images/ajax-loader.gif'
        }).appendTo(parent);
        $.ajax({
          url: '<?php echo SITEURL; ?>cep/' + cep,
          dataType: 'json',
          success: function(json){
            
            $(loader).remove();
            $('.autocomplete-cep').removeAttr('disabled');

            if (parseInt(json.sucesso) == 1) {
              $('#endereco').val(json.endereco);
              $('#bairro').val(json.bairro);
              $('#cidade').val(json.cidade);
              $('#uf').val(json.uf);
            }
            else {
              $('<span/>', {
                id: 'cep-message',
                  text: 'CEP não encontrado!',
                  class: 'small text-danger',
              }).appendTo(parent);
            }
          }
        });
      });

      $('.load-cursos').on('change', function(){

        var dados = {};

        $('.load-cursos').each(function(){
          dados[$(this).attr('name')] = $(this).val();
        });

        $('#select-cursos').find('option').remove();
        $('#select-cursos').append('<option value="">Buscando cursos...</option>');

        $.ajax({
          type: 'POST',
          url: '<?php echo SITEURL; ?>ajax/select',
          data: { dados },
          timeout: 2000,
          success: function(data){
            $('#select-cursos').find('option').remove();
            $('#select-cursos').append(data);
          },
          error: function(xhr, type){

          }
        })
      });

      function downloadJSAtOnload() {
        var element = document.createElement('script');
        element.src = '<?php echo Url::templatePath() ?>js/defer.js';
        //document.body.appendChild(element);
      }
      if (window.addEventListener) {
        window.addEventListener('load', downloadJSAtOnload, false);
      }
      else if (window.attachEvent) {
        window.attachEvent('onload', downloadJSAtOnload);
      }
      else {
        window.onload = downloadJSAtOnload;
      }
    </script>

</html>
