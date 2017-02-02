function buscaAjax (url, dados) {
	return $.ajax({
		type: "POST",
		dataType : "json",
		url: url,
		timeout: 10000,
		data: { dados : dados }
	}).done(function() {
		$(".ajax-loader").css("display", "none");
	}).fail(function(jqXHR, textStatus, errorThrown){
		$(".ajax-loader").css("display", "none");
	});
}

function pesquisaAjax(pagina, url) {

	$(".ajax-loader").css("display", "block");

	var busca = {
		pagina : pagina,
		exibir : $("#exibir").val()
	};

	var href = [];

	var opcoesPesquisa = $('form#form-list input:hidden');
	$(opcoesPesquisa).each(function(){
		busca[$(this).attr('name')] = $(this).val();
		href.push($(this).attr('name') + "=" + $(this).val());
	});

	var opcoesPesquisa = $('form#form-list input:text');
	$(opcoesPesquisa).each(function(){
		busca[$(this).attr('name')] = $(this).val();
		href.push($(this).attr('name') + "=" + $(this).val());
	});

	opcoesPesquisa = $('form#form-list select');
	$(opcoesPesquisa).each(function(){
		busca[$(this).attr('name')] = $(this).val();
		href.push($(this).attr('name') + "=" + $(this).val());
	});

	opcoesPesquisa = $('form#form-list input:checkbox:checked');

	$(opcoesPesquisa).each(function(){
		var key = $(this).attr('name');
		if (!busca[key]) {
			busca[key] = [];
		}
		busca[key].push($(this).val());
	});

	buscaAjax(url, busca).success(function (data) {
		i = 0;
		var quantidade = data.quantidade;
		var inicio = data.inicio;
		var fim = data.fim;

		if (data.planilha) {
			$("#data").remove();
			$("<input/>", {
				"id" : "data",
				"name" : "data",
			  	"type": "hidden",
			  value: JSON.stringify(data.planilha),
			}).appendTo("form#planilha");
		}

		$("#quantidade").html(quantidade);
		$("#inicio").html(inicio);
		$("#fim").html(fim);

		$("tbody tr#buscaAjax").nextAll("tr").remove();
		$("tbody tr#buscaAjax").after(data.result);

		$(".pagination").html(paginacaoAjax(pagina, quantidade, inicio, fim, exibir));

		$(".pagination a").each(function(){
			var id = $(this).attr("id");
			var paginaArray = id.split("_");
			$(this).on("click", function(){
				pesquisaAjax (paginaArray[1], url);
				return false;
			});
		});		
	});

}

function paginacaoAjax(pagina, quantidade, inicio, fim, exibir) {

	if (exibir === undefined) {
		exibir = 10
	}

	$("#quantidade").html(quantidade);
	$("#inicio").html(inicio);
	$("#fim").html(fim);

	var quantidadePaginas = Math.ceil(quantidade / exibir);

	var html = '';

	inicio = 0;
	fim = 0;
	pagina = parseInt(pagina);

	if (pagina % exibir == 0) {
		inicio = pagina;
	}
	else {
		inicio = pagina - (pagina % exibir) + 1;
	}

	if (parseInt(pagina) % exibir == 0) {
		fim = pagina + exibir;
	}
	else {
		fim = (pagina + exibir) - (pagina % exibir);
	}

	if (fim > quantidadePaginas) {
		fim = quantidadePaginas;
	}

	if (pagina > 1) {
		html += '<li title="Ir para a primeira página">';
		html += '<a href="#" id="pagina_1">«</a>';
		html += '</li>';
	}

	if (pagina > 1 && quantidade > quantidadePaginas) {
		html += '<li title="Ir para a página anterior">';
		html += '<a href="#" id="pagina_' + (pagina - 1) + '">‹</a>';
		html += '</li>';
	}

	i = 0;

	for (i=inicio; i<=fim; i++) {
		html += '<li title="Ir para a página '+i+'" id="p'+i+'"';
		if (i==pagina) {
			html += ' class="current"';
		}
		html += '><a href="#" id="pagina_' + i + '">'+i+'</a></li>';
	}

	if (pagina < quantidadePaginas) {
		html += '<li title="Ir para a próxima página">';
		html += '<a href="#" id="pagina_' + (pagina + 1) + '">›</a>';
		html += '</li>';
	}

	if (pagina < quantidadePaginas) {
		html += '<li title="Ir para a última página">';
		html += '<a href="#" id="pagina_' + quantidadePaginas + '">»</a>';
		html += '</li>';
	}

	return html;
}

function criaElemento (tipo, atributos) {
	var elemento = document.createElement(tipo);
	for (var att in atributos) {
		elemento.setAttribute(att, atributos[att]);
	}
	return elemento;
}

function criaElementoTexto (texto) {
	return document.createTextNode(texto);
}

function maisCursos (obj) {
	var fieldset = $('fieldset#cursos');
	var quantidadeCursos = quantidade = $(fieldset).find("div.curso").length;

	var grid1 = criaElemento('div', {
		'class' : 'grid'
	});
	var col1Grid1 = criaElemento('div', {
		'class' : 'col-2-12 curso'
	});
	var contentCol1Grid1 = criaElemento('div', {
		'class' : 'content'
	});
	var labelContentCol1Grid1 = criaElemento("label", {
		"for" : "Curso " + (quantidade + 1)
	});
	labelContentCol1Grid1.appendChild(criaElementoTexto("Curso " + (quantidade + 1) + ":"));
	contentCol1Grid1.appendChild(labelContentCol1Grid1);
	col1Grid1.appendChild(contentCol1Grid1);
	grid1.appendChild(col1Grid1);

	var col2Grid1 = criaElemento('div', {
		'class' : 'col-10-12'
	});
	var contentCol2Grid1 = criaElemento('div', {
		'class' : 'content'
	});
	var input1ContentCol2Grid1 = criaElemento("input", {
		"type" : "hidden",
		"name" : "Cursos[" + quantidade + "][id]",
		"value" : "0"
	});
	var input2ContentCol2Grid1 = criaElemento("input", {
		"type" : "text",
		"name" : "Cursos[" + quantidade + "][curso]",
		"class" : "form-control autocomplete",
		"placeholder" : "Informe o curso e digite enter"
	});
	contentCol2Grid1.appendChild(input1ContentCol2Grid1);
	contentCol2Grid1.appendChild(input2ContentCol2Grid1);
	col2Grid1.appendChild(contentCol2Grid1);
	grid1.appendChild(col2Grid1);

	var grid2 = criaElemento('div', {
		'class' : 'grid'
	});
	var col1Grid2 = criaElemento('div', {
		'class' : 'col-2-12'
	});
	var contentCol1Grid2 = criaElemento('div', {
		'class' : 'content'
	});
	var labelContentCol1Grid2 = criaElemento("label", {
		"for" : "Disciplinas"
	});
	labelContentCol1Grid2.appendChild(criaElementoTexto("Disciplinas do Curso " + (quantidade+1) + ":"));
	contentCol1Grid2.appendChild(labelContentCol1Grid2);
	col1Grid2.appendChild(contentCol1Grid2);
	grid2.appendChild(col1Grid2);

	var col2Grid2 = criaElemento('div', {
		'class' : 'col-10-12'
	});
	var contentCol2Grid2 = criaElemento('div', {
		'class' : 'content'
	});
	var input2 = criaElemento("textarea", {
		"name" : "Cursos[" + quantidade + "][disciplinas]",
		"class" : "form-control",
		"placeholder" : "Informe as disciplinas do curso " + (quantidade+1)
	});
	contentCol2Grid2.appendChild(input2);
	col2Grid2.appendChild(contentCol2Grid2);
	grid2.appendChild(col2Grid2);

	$('#mais-cursos').before(grid1);
	$('#mais-cursos').before(grid2);
}

function isInt (value) {
	return !isNaN(value) && parseInt(value) == value;
}

function selecionarTodos(obj) {
	var bool = $(obj).prop("checked");
	var inputs = $(obj).closest("table").find("input");
	inputs.each(function(){
		$(this).prop("checked", bool);
	});
}

function redirect (url){
    location.href = url;
}

function marcaUnidade (obj, id) {
	var inputs = $("#unidades").find(":radio").prop("checked", false);
	$("#unidade" + id).prop("checked", true);
	$(obj).prop("checked", true);
}

function marcarDesmarcar(obj, id) {
	var bool = $(obj).prop("checked");
	var inputs = $('#' + id).find("input");
	inputs.each(function(){
		$(this).prop("checked", bool);
	});
}
