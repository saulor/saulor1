<!doctype html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <title>IEFAP › Área do Aluno › Requerimentos</title>
  <meta name="description" content="Contato IEFAP">
  <meta name="author" content="IEFAP">
</head>

<body>
	<div style="color:#595959;font-family:Helvetica Neue,arial,verdana,sans-serif;line-height:150%;padding:0;font-size:14px;">
		
		<?php
			$url = SITEURL . 'area-aluno/requerimentos/acompanhamento';
		?>

		<p>Olá <?php echo $objeto['nome'] ?>, estamos enviando este e-mail para avisar que o requerimento 
			que você solicitou no dia <?php echo date('d/m/Y', $objeto['timestamp']); ?> foi atualizado. 
			Acompanhe a situação do requerimento 
			<a target="_blank" href="<?php echo $url; ?>">aqui</a>. 
			O número de protocolo da solicitação é <?php echo $objeto['protocolo']; ?>.<p>

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