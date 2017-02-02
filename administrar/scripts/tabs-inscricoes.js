function loadInscricoes (dados) {
  return $.ajax({
    type: "POST",
    url: '?modulo=preinscricoes&acao=inscricoes',
    data: { dados }
  });
}

function tabsSituacoes(curso, callback) {
  $.ajax({
    type: "POST",
    url: '?modulo=situacao&acao=tabs',
    data: { curso: curso }
  }).done(function(response) {
    $('ul#situacoes li').remove();
    var html = $.parseHTML(response);
    $('ul#situacoes').append(html);
    if (typeof callback === 'function') {
        callback();
    }
  });
}

function valida (form) {
  var validated = true;
  var data = $(form).serializeArray();
  $('.required-field').remove();
  for (var input in data) {
    var element = $(form).find("[name='" + data[input]['name'] + "']");
    var required = typeof($(element).attr('required')) !== 'undefined';
    var isField = $(element).hasClass('field');
    if (required && data[input]['value'] == '') {
      validated = false;
      var span = $('<span/>', {
        class : 'required-field destaque small',
        text : 'Campo obrigatório'
      });
      $(element).after(span);
    }
  }
  return validated;
}

function getDados (form) {
  var data = $(form).serializeArray();
  var objeto = {};
  var dados = {};
  for (var input in data) {
    var element = $(form).find("[name='" + data[input]['name'] + "']");
    var required = typeof($(element).attr('required')) !== 'undefined';
    var isField = $(element).hasClass('field');
    if (isField) {
      objeto[data[input]['name']] = data[input]['value'];
    }
    else {
      dados[data[input]['name']] = data[input]['value'];
    }
  }
  return {objeto : objeto, dados : dados};
}

function mostraMensagem (response) {
  if (response.type) {
    $('div#user-message').children().remove();
    $('<div/>', {
      class : 'alert alert-' + response.type, 
    }).append(response.message).appendTo('div#user-message');
    $('div#user-message').removeClass('hidden');
    if (response.type == 'danger') {
      return;
    }
  }
}

var functions = {
  situacaoAtual: function(response) {
    $('#situacao-atual').find('span').html(response.tipoText + ' - ' + response.motivo);
    $('#situacao-atual').find('a').removeClass('hidden');
  },
  atualizaSituacoes: function(response) {
    if ($('ul#situacoes').length) {
      // atualiza tabs de situações com as quantidades certas
      tabsSituacoes(response.curso, function() {
        // clica na tab pra mudar pra ela
        $('ul#situacoes li#tab-situacao' + response.tipo).trigger('click');
      });
    }
  },
  atualizaSituacao: function(response) {
    var form = $('#modal-container form');
    var tr = $(form).attr('data-tr');
    var span = $('<span/>', {
      text : response.tipoText
    });
    var div = $('<div/>', {
      class : 'tip right',
    });
    var html = $.parseHTML(response.resumo);
    $(div).append(html);
    $('tr#' + tr).css('border', '1px solid #d8d7d7');
    $('tr#' + tr).find('td.situacao-resumo').children().remove();
    $('tr#' + tr).find('td.situacao-resumo').append(span);
    $('tr#' + tr).find('td.situacao-resumo').append(div);
  }
};

$(document).ready(function(){

  $('.primary').on('keyup', '.filtro', function(ev){
    ev.preventDefault();
    var tr = $(this).parents('tr');
    var curso = $(tr).attr('data-curso');
    var status = $(tr).attr('data-status');
    var order = $(tr).attr('data-order');
    var div = $(tr).attr('data-div');
    var situacao = $(tr).attr('data-situacao');
    var dados = {status, curso, order, situacao};
    var loader = $(this).parents('table').find('.ajax-loader')
    $(loader).css('display', 'inline');
    var qs = {};
    $(tr).find('input:text').each(function(){
      qs[$(this).attr('name')] = $(this).val();
    });
    $.ajax({
      type: "POST",
      url: '?modulo=preinscricoes&acao=inscricoes',
      data: { dados, qs }
    }).done(function(response) {
      var html = $.parseHTML(response);
      var quantidade = $(html).filter('tr:not(.none):not(.links)').length;
      $('div#' + div + ' strong#quantidade').html(quantidade);
      $('div#' + div + ' tbody tr:not(:first-child)').children().remove();
      $('div#' + div + ' tbody tr#buscaAjax').after(response);
      $(loader).css('display', 'none');
    });
  });

  $('.primary').on('click', '.ordenar', function(ev){
    ev.preventDefault();
    
    var order = $(this).attr('data-order');
    var curso = $(this).attr('data-curso');
    var status = $(this).attr('data-status');
    var situacao = $(this).attr('data-situacao');
    var div = $(this).attr('data-div');
    $(this).parents('table').find('tr#buscaAjax').attr('data-order', order);

    var dados = {status, curso, order};
    var loader = $(this).parents('table').find('.ajax-loader');
    $(loader).css('display', 'inline');
    if (situacao !== 'undefined') {
     dados.situacao = situacao;
    }
    loadInscricoes(dados).success(function(response){
      var html = $.parseHTML(response);
      var quantidade = $(html).filter('tr:not(.none):not(.links)').length;
      $('div#' + div + ' strong#quantidade').html(quantidade);
      $('div#' + div + ' tbody tr:not(:first-child)').children().remove();
      $('div#' + div + ' tbody tr#buscaAjax').after(response);
      $(loader).css('display', 'none');
    });
  });

  // fechar modal
  $('#modal-container').on('click', '.modal-fechar', function(ev){
    ev.preventDefault();
    $('#modal-container').children().remove();
    $('#modal-container').addClass('hidden');
    $("#modal-mask").hide();
  });

  // cada tipo de situacão tem uma view específica
  // carrega a view da situação
  $('#modal-container').on('change', 'select#tipo', function(ev){
    ev.preventDefault();
    var date = new Date();
    var situacao = $(this).val();
    $(this).parents('form').find('.btn').removeAttr('disabled');
    if (situacao != '') {
      $('#modal-container .ajax-loader').css('display', 'inline');
      $('#modal-container #situacao-html').children().remove();
      var view = 'situacao' + $(this).val() + '.html';
      var url = 'views/situacao/' + view + '?time=' +  date.getTime();
      $.get(url, function(data) {
        var html = $.parseHTML(data);
        var success = $(html).find('#motivo').length > 0;
        if (success) {
          $('#modal-container #situacao-html').append(html);
        }
        else {
          mostraMensagem({
            'type' : 'danger',
            'message' : 'Error: ' + view + ' not found'
          });
        }
      }).done(function(){
        $('#modal-container .ajax-loader').css('display', 'none');
        $('#modal-container .datepicker').datepicker();
      });
    }
  });

  // click em Adicionar situacao
  $('.primary').on('click', '.add-situacao', function(ev){

    ev.preventDefault();

    // elemento clicado
    var elemento = $(this);
    var idSituacao = parseInt($(elemento).attr('data-id'));
    var tipoSituacao = parseInt($(elemento).attr('data-tipo'));

    // mostra a máscara
    var maskHeight = $(document).height();
    var maskWidth = $(window).width();
    $('#modal-mask').css({
      'width' : maskWidth, 
      'height' : maskHeight
    });

    $('#modal-mask').fadeTo('slow', 0.8, function() {
      // ao carregar a máscara recupera o html padrão das situações (index.phtml)
      var date = new Date();
      var url = 'views/situacao/index.html?time=' +  date.getTime();
      $.get(url).done(function(data){
        var html = $.parseHTML(data);
        $('#modal-container').append(html);
        $('#modal-container').removeClass('hidden');
        $('#modal-container .ajax-loader').css('display', 'inline');
        
        var form = $('#modal-container').find('form');
        $(form).attr('data-status', $(elemento).attr('data-status'));
        $(form).attr('data-curso', $(elemento).attr('data-curso'));
        $(form).attr('data-tr', $(elemento).attr('data-tr'));
        $(form).attr('data-after', $(elemento).attr('data-after'));
        // seta alguns elementos no form
        $(form).find('.data-in').each(function(){
          $(this).val($(elemento).attr('data-' + $(this).attr('name')));
        });
        
        // inicializa o select de tipos de situacões
        $.ajax({
          type: "POST",
          cache: false,
          url: '?modulo=situacao&acao=tipos&time=' + date.getTime(),
        }).done(function(response) {
          $(form).find('select#tipo').append($.parseHTML(response));
          $('#modal-container .ajax-loader').css('display', 'none');
          if (tipoSituacao != 0) {
           // carrega a view da situação
           $(form).find('select#tipo').val(tipoSituacao).trigger('change');
           // editando uma situação
           if (idSituacao != 0) {
              $('#modal-container .ajax-loader').css('display', 'inline');
              $.ajax({
                type: "POST",
                dataType: 'json',
                cache: false,
                url: '?modulo=situacao&acao=find&time=' + date.getTime(),
                data: {id : idSituacao}
              }).done(function(response) {
                mostraMensagem(response);
                $.each(response, function (index, item) {
                  $(form).find('#' + index).val(item);
                }); 
                $('#modal-container .ajax-loader').css('display', 'none');  
              });
            }
          }
        });
      });
    });
  });

  // Salvar Situação
  $('#modal-container').on('click', '.btn', function(ev){

    ev.preventDefault();

    // referência ao form
    var form = $(this).parents('form');
    var afterFunction = $(form).attr('data-after');
    
    // validação
    if (!valida(form)) {
      return;
    }

    var formData = getDados(form);
    var dados = formData.dados;
    var objeto = formData.objeto;

    $('span.required-field').remove();
    mostraMensagem({'type' : 'info', 'message' : 'Processando...'});

    var botao = $(this);
    $(botao).attr('disabled', 'disabled');

    $.ajax({
      type: "POST",
      dataType : "json",
      url: '?modulo=situacao&acao=salva',
      data: { objeto, dados }
    }).done(function(response) {
      $(botao).removeAttr('disabled');
      // se o retorno for uma mensagem ...
      mostraMensagem(response);
      if (response.type == 'success') {
        $(form).find('input[name="id"]').val(response.id);
        functions[afterFunction](response);
      }
    });
  });

  $.ajaxSetup({
      error : function(jqXHR, textStatus, errorThrown) {
        mostraMensagem({
          'type' : 'danger',
          'message' : 'Error: ' + textStatus + ': ' + errorThrown
        });
      },
      fail : function(jqXHR, textStatus, errorThrown) {
        mostraMensagem({
          'type' : 'danger',
          'message' : 'Error: ' + textStatus + ': ' + errorThrown
        });
      },
      statusCode : {
          404: function() {
              mostraMensagem({
                'type' : 'danger',
                'message' : 'Not found'
              });
          }
      }
  });

});