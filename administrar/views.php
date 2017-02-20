<?php

require_once('config.php');
require_once ('core/ClassesLoader.php');
ClassesLoader::Register();

$conexao = new Conexao();

echo $sql = "drop view if exists vw_preinscricoes_v3;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

/**
*	View de inscrições. Reúne as informações da inscrição as informações da unidade,
*	do usuário responsável
*/ 
echo $sql = "create view vw_preinscricoes_v3 as 
select 
preinscricoes.id, 
preinscricoes.unidade as idCidade,
preinscricoes.curso as idCurso,
cursos.nome as nomeCurso,
cursos.unidadeCertificadora as certificadoraCurso,
cursos.link as linkCurso,
cursos.tipo as tipoCurso,
cidades_cursos.id as idCidadeCurso,
cidades_cursos.valorCurso,
cidades_cursos.valorDesconto,
cidades_cursos.valorInscricao,
cidades_cursos.quantidadeParcelas,
cidades_cursos.quantidadeModulos,
cidades_cursos.cargaHoraria,
cidades_cursos.duracao,
cidades_cursos.previsaoInicio,
cidades_cursos.status as statusUnidade,
cidades.nome as nomeUnidade,
estados.sigla as siglaEstado,
preinscricoes.nome,
preinscricoes.unidadeCertificadora, 
preinscricoes.sexo, 
preinscricoes.estadoCivil, 
preinscricoes.profissao, 
preinscricoes.rg, 
preinscricoes.orgaoExpedidor, 
preinscricoes.ufExpedidor, 
preinscricoes.cpf, 
preinscricoes.endereco, 
preinscricoes.numero, 
preinscricoes.complemento, 
preinscricoes.bairro, 
preinscricoes.cidade, 
preinscricoes.uf, 
preinscricoes.cep, 
preinscricoes.telefoneResidencial, 
preinscricoes.operadoraCelular, 
preinscricoes.telefoneCelular, 
preinscricoes.email, 
preinscricoes.emailAlternativo, 
preinscricoes.naturalidade, 
preinscricoes.nomePai, 
preinscricoes.nomeMae, 
preinscricoes.formacao, 
preinscricoes.instituicao, 
preinscricoes.anoConclusao, 
preinscricoes.responsavel, 
usuarios.nome as responsavelNome,
preinscricoes.formaPagamento, 
preinscricoes.banco, 
preinscricoes.diaPagamento, 
preinscricoes.comoConheceu, 
preinscricoes.nomeIndicou, 
preinscricoes.telefone, 
preinscricoes.whatsapp, 
preinscricoes.horario,
preinscricoes.enviouComprovante, 
preinscricoes.comprovante, 
preinscricoes.mime, 
preinscricoes.extensao, 
preinscricoes.status, 
preinscricoes.observacoes, 
preinscricoes.turma, 
preinscricoes.visualizada,
preinscricoes.dataExpedicao,
DATE_FORMAT(preinscricoes.dataExpedicao, '%d/%m/%Y') as dataExpedicaoF, 
preinscricoes.dataNascimento,
DATE_FORMAT(preinscricoes.dataNascimento, '%d/%m/%Y') as dataNascimentoF,
preinscricoes.data, 
DATE_FORMAT(preinscricoes.data, '%d/%m/%Y') as dataF,
preinscricoes.timestamp,
preinscricoes.situacao as idSituacao,
situacao.tipo as tipoSituacao,
situacao.iniciadoPor,
DATE_FORMAT(situacao.data, '%d/%m/%Y') as dataRetorno, 
situacao.horario as horarioRetorno,
situacao.motivo,
situacao.observacoes as obsSituacao,
situacao.cidade as cidadeSituacao,
situacao.estado as estadoSituacao,
situacao.dataC as dataCSituacao,
situacao.timestampDataC as timestampDataCSituacao,
u2.nome as nomeUsuarioSituacao,
u2.id as idUsuarioSituacao
from preinscricoes
left join cursos on preinscricoes.curso = cursos.id
left join usuarios on usuarios.id = preinscricoes.responsavel
left join cidades on cidades.id = preinscricoes.unidade
left join estados on estados.id = cidades.estado
left join cidades_cursos on cidades_cursos.curso = preinscricoes.curso 
and cidades_cursos.cidade = preinscricoes.unidade
left join situacao on preinscricoes.situacao = situacao.id
left join usuarios u2 on situacao.usuario = u2.id;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

/*

echo $sql = "drop view if exists vw_cursos_estados;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

echo $sql = "create view vw_cursos_estados 
as 
select 
cursos_estados.id as idCursoEstado, 
cursos_estados.curso as idCurso, 
cursos.nome as nomeCurso,
cursos.link as linkCurso, 
cursos_estados.estado as idEstado, 
estados.sigla, 
estados.nome as nomeEstado, 
regioes.nome as nomeRegiao 
from cursos_estados 
left join cursos on cursos_estados.curso = cursos.id 
left join estados on cursos_estados.estado = estados.id 
left join regioes on regioes.id = estados.regiao;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

*/

echo $sql = "drop view if exists vw_usuarios;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

echo $sql = "create view vw_usuarios 
as 
select 
usuarios.*,
permissoes.id as idPermissao,
permissoes.nome as nomePermissao,
permissoes.codigo as codigoPermissao
from usuarios join permissoes on permissoes.id = usuarios.permissao;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

echo $sql = "drop view if exists vw_situacao;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

echo $sql = "create view vw_situacao as 
select 
cursos.id as idCurso, 
cursos.nome as nomeCurso, 
preinscricoes.status,
preinscricoes.nome,
situacao.*,
usuarios.id as idUsuario, 
usuarios.nome as nomeUsuario 
from situacao 
join usuarios on usuarios.id = situacao.usuario 
join preinscricoes on situacao.inscricao = preinscricoes.id 
join cursos on cursos.id = preinscricoes.curso;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

echo $sql = "drop view if exists vw_docentes;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

echo $sql = "create view vw_docentes as 
SELECT 
d.*,
GROUP_CONCAT(dcd.curso) as curso,
GROUP_CONCAT(dcd.disciplinas) as disciplinas
FROM docentes d 
JOIN docentes_cursos_disciplinas dcd 
ON d.id = dcd.docente 
GROUP BY id;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

// echo $sql = "drop view if exists vw_categorias";
// $conexao->getConexao()->execute($sql);
// echo $sql = "
// create view vw_categorias as
// select
// cursos_categorias.*,
// (select count(*) 
// 	from cursos 
// 		where cursos.categoria = cursos_categorias.id
// ) as quantidadeCursos,
// (select count(*) 
// 	from cursos join cursos_categorias
// 		where cursos.tipo = 1 and cursos.categoria = cursos_categorias.id
// 			and LOCATE(cursos.categoria, cursos_categorias.caminho)
// ) as qtdePos,
// (select count(*) 
// 	from cursos 
// 		where cursos.tipo = 1 and cursos.status = 1 and cursos.categoria = cursos_categorias.id
// ) as qtdePosVisiveis,
// (select count(*) 
// 	from cursos 
// 		where cursos.tipo = 2 
// 			and cursos.categoria = cursos_categorias.id
// ) as qtdeApe,
// (select count(*) 
// 	from cursos 
// 		where cursos.tipo = 2 and cursos.status = 1 
// 			and cursos.categoria = cursos_categorias.id
// ) as qtdeApeVisiveis
// from cursos_categorias
// join cursos on cursos_categorias.id = cursos.categoria
// group by cursos_categorias.id;";
// $conexao->getConexao()->execute($sql);

// view de cursos e categorias
// com todos os cursos e as informações da sua categoria
echo $sql = "drop view if exists vw_cursos;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

echo $sql = "
create view vw_cursos as
select
cursos.*,
cursos_categorias.nome as nomeCategoria,
cursos_categorias.slug,
cursos_categorias.descricao as descricaoCategoria,
cursos_categorias.pai,
cursos_categorias.visivel,
cursos_categorias.profundidade,
cursos_categorias.caminho,
cursos_categorias.imagem as imagemCategoria,
cursos_categorias.dataCadastro,
cursos_categorias.dataAtualizacao
from cursos
join cursos_categorias on cursos.categoria = cursos_categorias.id;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

echo $sql = "drop view if exists vw_estados;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

echo $sql = "
create view vw_estados as
select
estados.*,
regioes.nome as nomeRegiao,
count(cidades.estado) as quantidadeCidades
from estados
join regioes on regioes.id = estados.regiao
left join cidades on cidades.estado = estados.id
group by estados.id;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

echo $sql = "drop view if exists vw_cidades_estados;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

echo $sql = "
create view vw_cidades_estados as
select
cidades.*,
estados.id as idEstado,
estados.sigla as siglaEstado,
estados.nome as nomeEstado,
regioes.nome as nomeRegiao,
count(cidades_cursos.cidade) as quantidadeCursos
from cidades
join estados on estados.id = cidades.estado
join regioes on regioes.id = estados.regiao
left join cidades_cursos on cidades_cursos.cidade = cidades.id
group by cidades.id;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

// os cursos são associados a cidades cadastradas
echo $sql = "drop view if exists vw_cidades_cursos;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

echo $sql = "
create view vw_cidades_cursos as
select
cidades_cursos.*,
cursos.id as idCurso,
cursos.nome as nomeCurso,
cursos.link as linkCurso,
cursos.tipo as tipoCurso,
cidades.id as idCidade,
cidades.nome as nomeCidade,
estados.id as idEstado,
estados.sigla as siglaEstado,
estados.nome as nomeEstado,
regioes.id as idRegiao,
regioes.nome as nomeRegiao
from cidades_cursos
join cursos on cidades_cursos.curso = cursos.id
join cidades on cidades_cursos.cidade = cidades.id
join estados on estados.id = cidades.estado
join regioes on regioes.id = estados.regiao;";
$conexao->getConexao()->execute($sql);

echo '<br /><br />';

?>
