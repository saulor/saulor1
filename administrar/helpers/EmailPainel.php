<?php

class EmailPainel {

	public static function requerimento ($objeto, $toDefault = 'contato@iefap.com.br') {

		// headers
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'To: %s' . "\r\n";
		$headers .= 'From: %s' . "\r\n";
		$headers .= 'Bcc: sauloroliveira@gmail.com' . "\r\n";

		$from = 'contato@iefap.com.br';

		ob_start();
        include DIR_ROOT . '/views/emails/area-aluno/requerimentos/atualizacao.php';
        $html = ob_get_contents();
        ob_end_clean();
        
		$to = $toDefault;
        $assunto = 'IEFAP - Seu requerimento foi atualizado';
		$headers1 = sprintf($headers, $to, $from);
		mail($to, $assunto, $html, $headers1);
	}

}


?>