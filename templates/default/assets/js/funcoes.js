function smoothScroll(el, to, duration) {
    if (duration < 0) {
        return;
    }
    var difference = to - $(window).scrollTop();
    var perTick = difference / duration * 10;
    this.scrollToTimerCache = setTimeout(function() {
        if (!isNaN(parseInt(perTick, 10))) {
            window.scrollTo(0, $(window).scrollTop() + perTick);
            smoothScroll(el, to, duration - 10);
        }
    }.bind(this), 10);
}

function consultaCep (obj) {

    if ($(obj).val() == '') {
        return;
    }

    var cep = obj.value.replace(/-/g, '');
    $(obj).parent('div').find('span').remove();
    $('.ajax-loader').css('display', 'block');
    $('.endereco').attr('disabled', 'disabled');
    $('#endereco').val('');
    $('#bairro').val('');
    $('#cidade').val('');
    $('#uf').val('');

    $.ajax({
      type: 'GET',
      url: 'http://localhost/~saulor/iefap3/cep/' + cep,
      dataType: 'json',
      timeout: 5000,
      success: function(json){
        if (parseInt(json.sucesso) == 1) {
            $('#endereco').val(json.endereco);
            $('#bairro').val(json.bairro);
            $('#cidade').val(json.cidade);
            $('#uf').val(json.uf);
        }
        else {
            $('<span/>', {
                text: 'CEP não encontrado!',
                class: 'small text-danger',
            }).appendTo('#localizaCep');
        }
        $('.ajax-loader').css('display', 'none');
        $('.endereco').removeAttr('disabled');
        $('#endereco').focus();
      },
      error: function(xhr, type){
        $('<small/>', {
            text: 'CEP não encontrado!',
            class: 'text-danger',
        }).appendTo('#localizaCep');
        $('.ajax-loader').css('display', 'none');
        $('#endereco').removeAttr('disabled');
        $(obj).focus();
      }
    });
}

function criaElemento (tipo, atributos) {
    var elemento = document.createElement(tipo);
    for (var att in atributos) {
        elemento.setAttribute(att, atributos[att]);
    }
    return elemento;
}

function criaTexto (texto) {
    return document.createTextNode(texto);
}

function maisCursos (obj) {
    var fieldset = $('#cursos-disciplinas');
    var quantidadeCursos = $(fieldset).find('.curso-disciplinas').length;

    var cursoDisciplinas = criaElemento('div', {
        'class' : 'curso-disciplinas'
    });

    var formGroup1 = criaElemento('div', {
        'class' : 'form-group'
    });

    var formGroup2 = criaElemento('div', {
        'class' : 'form-group'
    });

    var label1 = criaElemento('label', {
        'for' : 'Curso ' + (quantidadeCursos+1),
        'class' : 'col-lg-2 col-sm-3 control-label',
    });

    $(label1).append(criaTexto('Curso ' + (quantidadeCursos+1)));

    var div1 = criaElemento('div', {
        'class' : 'col-lg-10 col-sm-9'
    });

    $(div1).append(criaElemento('input', {
        'type' : 'text',  
        'name' : 'Cursos[' + quantidadeCursos + '][curso]', 
        'class' : 'form-control cursos', 
        'placeholder' : 'Informe o curso'
    }));

    var label2 = criaElemento('label', {
        'for' : 'Disciplinas do curso ' + (quantidadeCursos+1),
        'class' : 'col-lg-2 col-sm-3 control-label',
    });

    $(label2).append(criaTexto('Disciplinas'));

    var div2 = criaElemento('div', {
        'class' : 'col-lg-10 col-sm-9'
    });

    $(div2).append(criaElemento('textarea', {
        'name' : 'Cursos[' + quantidadeCursos + '][disciplinas]', 
        'class' : 'form-control', 
        'placeholder' : 'Informe as disciplinas separadas por vírgula'
    }));

    $(formGroup1).append(label1);
    $(formGroup1).append(div1);
    $(cursoDisciplinas).append(formGroup1);
    $(formGroup2).append(label2);
    $(formGroup2).append(div2);
    $(cursoDisciplinas).append(formGroup2);
    $(obj).parents('div.form-group').before(cursoDisciplinas);
}