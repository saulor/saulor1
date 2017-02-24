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