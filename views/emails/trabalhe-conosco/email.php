<!doctype html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <title>Contato IEFAP</title>
  <meta name="description" content="Contato IEFAP">
  <meta name="author" content="IEFAP">
</head>

<body>
	<div style="color:#595959;font-family:Helvetica Neue,arial,verdana,sans-serif;line-height:150%;padding:0;font-size:14px;">
		
		<p>Prezado <?php echo $objeto->nome; ?>,<br />
		Você realizou um cadastro na área Trabalhe Conosco do nosso site em <?php echo date('d/m/Y', $objeto->timestamp); ?>. 
		Agradecemos pelo seu interesse em fazer parte da nossa equipe. Registramos seu cadastro em 
		nosso banco de dados e no momento que tivermos 
		oportunidade em sua área de atuação retornaremos o contato.
		<p>Obrigado por escolher o IEFAP!</p>

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
</body>
</html>