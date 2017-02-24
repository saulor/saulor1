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

	public static function contato ($objeto, $send = true) {

		// headers
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'To: %s' . "\r\n";
		$headers .= 'From: %s' . "\r\n";
		$headers .= 'Bcc: sauloroliveira@gmail.com' . "\r\n";

		if (!empty($contato->cc)) {
			$headers .= 'Cc: ' .  $contato->cc . "\r\n";
		}

		$from = 'contato@iefap.com.br';

        ob_start();
        include DIR_ROOT . '/views/emails/contato/resposta.php';
        $html = ob_get_contents();
        ob_end_clean();

        // Envia o primeiro e-mail
        // E-mail que vai pra pessoa que fez o contato
        if ($send) {
        	$to = $objeto->email;
	        $assunto = 'IEFAP - Resposta de contato realizado através do site';
			$headers1 = sprintf($headers, $to, $from);
			if (!mail($to, $assunto, $html, $headers1)) {
				throw new Exception ('Erro ao tentar enviar e-mail para ' . $objeto->email . '. Por favor tente novamente!');
			}
        }

        return $html; // if (!$send)
	}

}


?>