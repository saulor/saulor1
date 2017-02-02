<!doctype html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <title>Área do Aluno - Recuperar senha</title>
  <meta name="description" content="Contato IEFAP">
  <meta name="author" content="IEFAP">
</head>

<body>
	<div style="color:#595959;font-family:Helvetica Neue,arial,verdana,sans-serif;line-height:150%;padding:0;font-size:14px;">
		
		<p>Sua senha de acesso a Área do Aluno é <?php echo $objeto->senha; ?></p>
		<p>Clique <a href="<?php echo SITEURL; ?>area-aluno">aqui</a> 
			para acessar a área do aluno.</p>

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