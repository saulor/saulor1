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
		
		<p><?php echo $objeto->nome; ?>, solicitou um novo requerimento através do site 
			em <?php echo date('d/m/Y', $objeto->timestamp); ?>. A solicitação gerou o número
			de protocolo <?php echo $objeto->protocolo; ?></p>

		<p>Para visualizar os detalhes da solicitação acesse Painel de Administração > Requerimentos > 
		Recebidos > <?php echo $objeto->nome; ?>.</p>

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