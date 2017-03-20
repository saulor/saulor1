(function(){

  // faq

  $('.panel-heading').on("click", function() {
    $('.panel-group .in').removeClass('in');
    $(this).parent().find('.collapse').addClass('in');
  });

  // unidades bar
  $('section#unidades select').on('change', function(e){
    e.preventDefault();
    // esconde a unidade atual
    $('section#unidades .unidade:not(.hidden)').addClass('hidden');
    // mostra a nova unidade
    $('section#unidades #unidade-' + $(this).val()).removeClass('hidden');
    // check na unidad escolhida
    $('section#unidades #unidade-' + $(this).val() + ' select').val($(this).val());
    $('#menu-wrap').removeClass();
    $('#menu-wrap').addClass($(this).val());
  });

  // menu mobile
  $("#menu-trigger").on("click", function(){
    $("ul#menu-mobile").slideToggle();
  });

  // slider images loading ... 
  var loaded = 0;
  var quantidadeBanners = $('#banner-wrap img').length;
  $('#banner-wrap img').one('load', function() {
      loaded++;
      if(loaded == quantidadeBanners){
        $('#loader-container').addClass('hidden');
        $('#banner-wrap').removeClass('hidden');
        var slider = $(".pgwSlider").pgwSlider({
          displayControls : true,
          displayList : false,
          transitionEffect : 'fading',
          adaptativeHeight : true,
          intervalDuration: 5000
        });
      }
  }).each(function() {
    if(this.complete) { 
      $(this).load();
    }
  });

  // menu dropdown
  $("ul#menu").on("click", "li.dropdown:not(.open)", function(e){
    e.preventDefault();
    $("ul#menu li").each(function(){
      if($(this).is(".open")) {
        $(this).removeClass("open");
        $(this).find("ul.dropdown-menu").css("display", "none");
      }
    });
    $(this).addClass("open");
    $(this).find("ul.dropdown-menu").css("display", "block");
  });

  // masks
  var telefoneMask = function (val) {
    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
  },
  telefoneOptions = {
    onKeyPress: function(val, e, field, options) {
      field.mask(telefoneMask.apply({}, arguments), options);
    },
    placeholder:"(__) _____-____"
  };

  $(".cpf").mask("000.000.000-00");
  $(".data").mask("00/00/0000");
  $(".cep").mask("00000-000");
  $(".cpfPh").mask("000.000.000-00", {placeholder: "___.___.___-__"});
  $(".telefone").mask(telefoneMask, telefoneOptions);

  // anchors
  $('a[href*="#"]:not([href="#"])').click(function(e) {
    e.preventDefault();
    smoothScroll($(window), $($(e.currentTarget).attr('href')).offset().top, 500);
  });

  /*
  // verificar esse erro
  $(".dropdown").on("click", function(e){
    e.preventDefault();
    // $("li.dropdown").not(this).each(function(){
    //   $(this).removeClass("open");
    // });
    if ($(this).hasClass("open")) {
      //$(this).removeClass("open");
      slider.startSlide();
    }
    else {
      $(this).addClass("open");
      slider.stopSlide();
    }
  });

  $(".pm-links").on("mouseenter", "li.dropdown", function() {
    var li = $(this);
    setTimeout(function(){
      $(li).addClass("open");
      $(li).find("ul.dropdown-menu").css("display", "block");
    },300);
  });

  // $("ul.dropdown-menu").on("mouseleave", function() {
  //    $(this).parent().removeClass("open");
  //    $(this).css("display", "none");
  // });

*/

  $("li.popup").on("mouseenter", function() {
    var li = $(this);
    $(li).find("div.login").css("display", "block");
  });

  $("li.popup").on("mouseleave", function() {
    var li = $(this);
    $(li).find("div.login").css("display", "none");
  });

  $(".pm-links").on("mouseenter", "li.dropdown", function() {
    var li = $(this);
    $(li).addClass("open");
    $(li).find("ul.dropdown-menu").css("display", "block");
  });

  $(".pm-links").on("mouseleave", "li.dropdown", function() {
    var li = $(this);
    $(li).removeClass("open");
    $(li).find("ul.dropdown-menu").css("display", "none");
  });

  // inicializa mecanismo de visualização das avaliações dos alunos
  $(".control-button button").click(function(){
    var divAtual = $('div#avaliacoes-fotos-wrap div.active');
    var divAtualId = parseInt($(divAtual).attr('id').split('-')[1]);
    var newDivId = $(this).attr('id') == 'prev' ? divAtualId - 1 : divAtualId + 1; 
    var newDiv = $('#avaliacao-' + newDivId);
    if (newDiv.length) {
      $(divAtual).toggleClass(function(){
        $('.avaliacao-info').addClass('hidden');
        var classe1 = $(this).attr('class');
        classe2 = classe1.replace(/3/g, 2);
        $(this).attr('class', classe2);
        $(newDiv).attr('class', classe1);
        $(divAtual).removeClass('active');
        $('#avaliacao-info-' + newDivId).removeClass('hidden');
        var prev = $('#avaliacao-' + (newDivId - 1));
        !prev.length ? $('div#avaliacoes-wrap button#prev').attr('disabled', 'disabled') : $('div#avaliacoes-wrap button#prev').removeAttr('disabled');
        var next = $('#avaliacao-' + (newDivId + 1));
        !next.length ? $('div#avaliacoes-wrap button#next').attr('disabled', 'disabled') : $('div#avaliacoes-wrap button#next').removeAttr('disabled');
      });
    }
  });
})(); 