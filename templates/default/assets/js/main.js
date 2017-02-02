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
    var id = $(this).attr('id');
    // avaliação atual
    var divAtual = $(".avaliacoes-wrap .activeCol");
    if (id == "next") {
      var div = $(divAtual).next("div");
    }
    else {
      var div = $(divAtual).prev("div");
    }
    if (div.length > 0) {
      var info = "#avaliacao-" + $(div).attr("id");
      $(divAtual).find(".avaliacao-wrap").removeClass("active");
      $(divAtual).removeClass("col-lg-4 col-md-4 col-sm-4 col-xs-4 activeCol");
      $(divAtual).addClass("col-lg-2 col-md-2 col-sm-2 col-xs-2");
      $(div).removeClass("col-lg-2 col-md-2 col-sm-2 col-xs-2");
      $(div).addClass("col-lg-4 col-md-4 col-sm-4 col-xs-4 activeCol");
      $(div).find(".avaliacao-wrap").addClass("active"); 
      $(".avaliacoes-info-wrap .avaliacao-info").each(function(e){
        if ($(this).attr("id") != $(info).attr("id")) {
          $(this).addClass("hidden");
        }
        else {
          $(info).removeClass("hidden");
          $(info).fadeIn();
        }
      });
    }
  });

})(); 