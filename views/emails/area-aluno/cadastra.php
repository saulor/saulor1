<!doctype html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <title>Área do Aluno - Cadastrar senha</title>
  <meta name="description" content="Contato IEFAP">
  <meta name="author" content="IEFAP">
</head>

<body>
	<div style="color:#595959;font-family:Helvetica Neue,arial,verdana,sans-serif;line-height:150%;padding:0;font-size:14px;">
		
		<p>Você cadastrou sua senha para ter acesso a Área do Aluno. Para acessar 
		é necessário informar o cpf e a senha.</p>

		<p><strong>Dados cadastrados</strong></p>

		<p><strong>CPF:</strong> <?php echo $objeto->cpf; ?> <br />
			<strong>Senha:</strong> <?php echo $objeto->senha; ?><br />
			<strong>E-mail:</strong> <?php echo $objeto->email; ?></p>

		<p>Clique <a href="<?php echo SITEURL; ?>area-aluno">aqui</a> 
			para acessar a Área do Aluno.</p>

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