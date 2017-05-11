<!doctype html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <title>Inscrições IEFAP</title>
  <meta name="description" content="Contato IEFAP">
  <meta name="author" content="IEFAP">
</head>

<body>
	<div style="background-color:rgb(240,240,240);width:700px;">
		<div style="color:#595959;font-family:Helvetica Neue,arial,verdana,sans-serif;line-height:150%;padding:5px 30px;font-size:14px;background-color:rgb(256,256,256);width:85%;margin:0 auto;">
			<p><?php echo $objeto->nome; ?> realizou uma inscrição no curso 
				<strong><?php echo $objeto->nomeCurso; ?></strong> 
				<?php if (!empty($objeto->nomeUnidade)) { ?> 
				na unidade <strong><?php echo $objeto->nomeUnidade; } ?></strong>
				em <?php echo date('d/m/Y', $objeto->timestamp); ?>.</p>
			<?php
				$dados1 = $dados2 = array();
				if (!empty($objeto->telefone)) {
					$dados['Telefone'] = $objeto->telefone;
				}
				if (!empty($objeto->telefoneResidencial)) {
					$dados['Tel. Res.'] = $objeto->telefoneResidencial;
				}
				if (!empty($objeto->operadoraCelular)) {
					$dados['Op. Cel.'] = $objeto->operadoraCelular;
				}
				if (!empty($objeto->telefoneCelular)) {
					$dados['Tel. Cel.'] = $objeto->telefoneCelular;
				}
				if (!empty($objeto->email)) {
					$dados['E-mail'] = $objeto->email;
				}
				if (!empty($objeto->cidade)) {
					$dados['Cidade'] = $objeto->cidade;
				}
				if (!empty($objeto->uf)) {
					$dados['Estado'] = $objeto->uf;
				}
				if ($dados) {
					echo '<p><strong>Dados informados:</strong><br />';
					foreach ($dados as $key => $value) {
						$dados2[] = sprintf('<strong>%s:</strong> %s', $key, $value);
					}
					echo implode('<br />', $dados2);
					echo '</p>';
				}
			?>
			<p>Para ver os detalhes acesse Painel de Administração > Inscrições > 
				<?php echo $objeto->nomeCurso; ?>.</p>
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