<!doctype html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <title>Contato IEFAP</title>
  <meta name="description" content="Contato IEFAP">
  <meta name="author" content="IEFAP">
</head>

<body>
	<div style="width:700px;">
		<div class="header" style="border-left:1px solid #ccc;border-top:1px solid #ccc;border-right:1px solid #ccc;">
			<img style="width:100%;display:block;" src="<?php echo Url::templatePath(); ?>images/emails/header.jpg" />
		</div>
		<div style="color:#595959;font-family:Helvetica Neue,arial,verdana,sans-serif;
			line-height:150%;margin:0;padding:0;font-size:14px;border-left:1px solid #ccc;
			border-right:1px solid #ccc;">
			<div style="padding:0 20px;">
				<br />
				<p style="margin-top:0;padding-top:0;">Recebemos a confirmação do seu cadastro de interesse no curso 
					<strong><?php echo $objeto->nomeCurso; ?></strong>
					<?php if (!empty($objeto->nomeUnidade)) { ?> na unidade 
					<strong><?php echo $objeto->nomeUnidade; } ?></strong>.
				</p>
				<p>O preenchimento das informações no cadastro registra o seu interesse pelo curso, 
					não sendo considerado ainda como <?php echo $tipo; ?> definitiva. Em breve você será 
					contactado(a) por um de nossos consultores e receberá todas as informações necessárias 
					para a efetivação de sua <?php echo $tipo; ?>. Caso queira efetivar sua 
					<?php echo $tipo; ?> agora clique 
					<a target="_blank" href="<?php echo SITEURL; ?><?php echo Funcoes::removeAcentos($tipo); ?>/<?php echo $objeto->linkCurso; ?>">aqui</a> 
					ou acesse através do endereço: <?php echo SITEURL; ?><?php echo Funcoes::removeAcentos($tipo); ?>/<?php echo $objeto->linkCurso; ?>.</p>
				<p>Qualquer dúvida entre em contato com nossa central de matrículas pelo telefone 
					0800 501 6000 ou pelo e-mail contato@iefap.com.br.</p>
				<p>Obrigado por escolher o IEFAP!</p>

				<a style="border:0;text-decoration:none;" href="<?php echo SITEURL; ?>">
					<p style="margin-bottom:0;padding-bottom:0;">
						<img src="<?php echo Url::templatePath() ?>images/logo.png" 
							width="80px" border="0" alt="">
					</p>
					<p style="margin:0;padding:0;">
						<span><?php echo SITETITLE; ?> <br />
						<small style="color:#999"><?php echo SITEURL; ?></span></small>
					</p>
					<br />
				</a>
			</div>
		</div>
		<div class="footer" style="position:relative;">
			<img style="width:100%;display:block;" src="<?php echo Url::templatePath(); ?>images/emails/footer.jpg" />
			<a target="_blank" href="https://www.facebook.com/pages/IEFAP-P%C3%B3s-Gradua%C3%A7%C3%A3o/291095547586274?fref=ts">
				<div style="position:absolute;width:21px;height:20px;top:30px;left:65px;"></div>
			</a>
			<a target="_blank" href="http://www.instagram.com/iefap.pos">
				<div style="position:absolute;width:21px;height:20px;top:30px;left:106px;"></div>
			</a>
			<a target="_blank" href="https://www.youtube.com/channel/UCQ8HmB-8FFTGFTx2ai4BQWA">
				<div style="position:absolute;width:21px;height:20px;top:30px;left:148px;"></div>
			</a>
			<a target="_blank" href="https://twitter.com/iefap">
				<div style="position:absolute;width:21px;height:20px;top:30px;left:189px;"></div>
			</a>
			<a target="_blank" href="<?php echo SITEURL; ?>">
				<div style="position:absolute;width:145px;height:20px;top:34px;left:276px;"></div>
			</a>
		</div>
	</div>
</body>
</html>