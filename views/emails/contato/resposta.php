<!doctype html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <title>Resposta IEFAP</title>
  <meta name="description" content="Resposta IEFAP">
  <meta name="author" content="IEFAP">
</head>

<body>
	<div style="background-color:rgb(240,240,240);width:700px;">
		<div style="color:#595959;font-family:Helvetica Neue,arial,verdana,sans-serif;line-height:150%;padding:5px 30px;font-size:14px;background-color:rgb(256,256,256);width:85%;margin:0 auto;">	
			<p>Abaixo segue resposta à mensagem através do formulário de contato do site.</p>
			<?php echo $objeto->resposta; ?>
			<p>Em <strong><?php echo date('d/m/Y', $objeto->timestamp); ?></strong>  você escreveu:</p>
			<blockquote 
				style="font-size:1em;background: #f9f9f9;border-left: 10px solid #ccc;margin: 0 1.5em 10px;padding: 0.5em 10px;quotes: "\201C""\201D""\2018""\2019";">
				<?php echo $objeto->mensagem; ?>
			</blockquote>
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