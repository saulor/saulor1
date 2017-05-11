<!doctype html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <title>Matrículas IEFAP</title>
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
				<p style="margin-top:0;padding-top:0;">Você realizou uma matrícula no curso <strong><?php echo $objeto->nomeCurso; ?></strong>
					<?php if (!empty($objeto->nomeUnidade)) {
				?> 
					e escolheu a unidade <strong><?php echo $objeto->nomeUnidade; ?></strong><?php
					if (!empty($objeto->duracao) || !empty($objeto->cargaHoraria)) { 
						echo '. Este curso tem';
					} 
					if (!empty($objeto->duracao)) {
						echo ' duração de ' . $objeto->duracao;
						if (!empty($objeto->cargaHoraria)) {
							echo ' totalizando ';
						}
					} 
					if (!empty($objeto->cargaHoraria)) {
						echo ' carga horária de ' . $objeto->cargaHoraria . ' horas'; 
					}}
					?>.</p>
				<?php 
				$count = 1; 
				if (!empty($objeto->nomeUnidade)) { ?>

				<h4><?php echo $count++; ?>) Investimento</h4>
				<table width="50%">
					<thead>
						<th align="center">PARCELA</th>
						<th align="center">VALOR</th>
						<th align="center">TOTAL</th>
					</thead>
					<tbody>
					<?php
						$parcelas = explode(',', $objeto->quantidadeParcelas);
						$total = Funcoes::converteDecimal($objeto->valorCurso);
						foreach ($parcelas as $parcela) {
							$parcela = (int) trim($parcela);
							$valor = $total / $parcela; 
							$tr = '<tr><td align="center">%d</td><td align="center">R$ %s</td><td align="center">R$ %s</td></tr>';
							echo sprintf($tr, $parcela, Funcoes::moneyFormat($valor), Funcoes::moneyFormat($total));
						}
					?>
					<tbody>
				</table>
				<?php
				}
					if (!empty($objeto->certificadoraCurso) && $objeto->certificadoraCurso != Curso::CURSO_UNIDADE_CERTIFICADORA_IEFAP) {
						$info = Curso::getCertificadoraInfo($objeto->certificadoraCurso);
				?>
				<h4><?php echo $count++; ?>) Instituição de Ensino Superior</h4>
				<p><?php echo $info['nome'] ?> (<a target="_blank" href="<?php echo $info['credenciamento']; ?>">Visualizar credenciamento da instituição junto ao MEC</a>)</p>
				<?php } ?>
				<h4><?php echo $count++; ?>) Efetivação da matrícula</h4>
				<p>Sua matrícula somente será efetivada com a assinatura do Contrato de Prestação de 
					Serviços Educacionais em 3 vias, com o pagamento da Taxa de Inscrição através de 
					depósito bancário no Banco do Brasil (001) – Agência 0479-0 – Conta Corrente: 
					98863-4 (IEFAP LTDA CNPJ: 13.055.198/0001-52) e com a entrega dos documentos 
					necessários para efetivação da matrícula.</p>
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