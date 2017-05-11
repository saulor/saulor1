<!doctype html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <title>IEFAP › Área do Aluno › Requerimentos</title>
  <meta name="description" content="Contato IEFAP">
  <meta name="author" content="IEFAP">
</head>

<body>
	<div style="background-color:rgb(240,240,240);width:700px;">
		<div style="color:#595959;font-family:Helvetica Neue,arial,verdana,sans-serif;line-height:150%;padding:5px 30px;font-size:14px;background-color:rgb(256,256,256);width:85%;margin:0 auto;">
			<p>Você solicitou um requerimento através do site em <?php echo date('d/m/Y', $objeto->timestamp); ?>.</p>
			<p>O número de protocolo da sua solicitação é <?php echo $objeto->protocolo; ?>. Utilize 
				este número para acompanhar o andamento do processo de emissão do seu requerimento. 
				O acompanhamento pode ser realizado no link 
				<a target="_blank" href="<?php echo SITEURL; ?>area-aluno/requerimentos/acompanhamento">
				<?php echo SITEURL; ?>area-aluno/requerimentos/acompanhamento</a>.
				<br /><br />Lembramos que o prazo para início da análise do requerimento é de 48 horas e que 
				seu deferimento está condicionado à situação acadêmica regular, bem como 
				documentação completa, pagamento das respectivas taxas e análise do departamento 
				responsável.<br /><br />Em caso de dúvidas, favor entrar em contato através do e-mail 
				secretaria@iefap.com.br.<br /><br />Atenciosamente,<br />Secretaria Acadêmica IEFAP.<p>
			<a style="border:0;text-decoration:none;" href="<?php echo SITEURL; ?>">
				<p style="margin-bottom:0;padding-bottom:0;">
					<img src="<?php echo Url::templatePath() ?>images/logo.png" 
						width="80px" border="0" alt="">
				</p>
				<p style="margin-top:0;padding-top:0;">
					<span><?php echo SITETITLE; ?> <br />
					<small style="color:#999"><?php echo SITEURL; ?></span></small>
				</p>
			</a>
		</div>
	</div>
</body>
</html>