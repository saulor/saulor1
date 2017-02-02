<!doctype html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <title>Inscrições IEFAP</title>
  <meta name="description" content="Contato IEFAP">
  <meta name="author" content="IEFAP">
</head>

<body>

	<div style="width:700px;">

		<img style="width:100%;display:block;" src="<?php echo Url::templatePath(); ?>images/emails/header.jpg" />

		<div style="color:#595959;font-family:Helvetica Neue,arial,verdana,sans-serif;
			line-height:150%;padding:0 20px;font-size:14px;">

			<p>Recebemos a confirmação da sua inscrição no curso <strong><?php echo $objeto->nomeCurso; ?></strong>
				<?php if (!empty($objeto->nomeUnidade)) { ?> na unidade 
				<strong><?php echo $objeto->nomeUnidade; } ?></strong>.</p>
			<?php if ($objeto->enviouComprovante == 0) { ?>
			
			<p>Verificamos que você não enviou o comprovante de depósito referente ao pagamento do 
				valor da inscrição. Para confirmação da sua inscrição, solicitamos efetuar o pagamento e 
				enviar o comprovante para o email <strong>financeiro@iefap.com.br</strong> ou 
				<strong>contas@iefap.com.br</strong>.</p>
			<?php } ?>

			<p><strong>OBSERVAÇÃO:</strong> A inscrição só será efetivada, e a vaga garantida, após o pagamento e envio 
				do comprovante de depósito, de acordo com a condição de pagamento negociada. A 
				desistência, na forma expressa, manifestada até 07 (sete) dias úteis, antes do início 
				do curso, importará na retenção de 20% sobre o valor pago na inscrição. Há menos de 7 
				dias úteis da data de início do curso, não será devolvido o valor pago, o qual será 
				destinado a título de ressarcimento de despesas administrativas.</p>

			<p>Qualquer dúvida entre em contato com nossa central de matrículas pelo telefone 
				0800 501 6000 ou pelo e-mail contato@iefap.com.br.</p>
				
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

		<img style="width:100%;display:block;" src="<?php echo Url::templatePath(); ?>images/emails/footer.jpg" />
	</div>
</body>
</html>