<?php

class EmailSite {

	public static function trabalhe ($objeto, $tipo, $toDefault = 'contato@iefap.com.br') {

		// headers
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'To: %s' . "\r\n";
		$headers .= 'From: %s' . "\r\n";
		$headers .= 'Bcc: sauloroliveira@gmail.com' . "\r\n";

		$from = 'contato@iefap.com.br';

		ob_start();
        include DIR_ROOT . '/views/emails/trabalhe-conosco/email.php';
        $html = ob_get_contents();
        ob_end_clean();

        // Envia o primeiro e-mail
        // E-mail que vai pra pessoa que fez o contato
		$to = $objeto->email;
        $assunto = 'IEFAP - Trabalhe Conosco';
		$headers1 = sprintf($headers, $to, $from);
		mail($to, $assunto, $html, $headers1);

		ob_start();
        include DIR_ROOT . '/views/emails/trabalhe-conosco/notificacao.php';
        $html = ob_get_contents();
        ob_end_clean();

        // Envia o segundo e-mail
        // E-mail que vai pro IEFAP como notificação de nova mensagem
		$to = $toDefault;
        $assunto = 'Novo cadastro de profissional';
		$headers1 = sprintf($headers, $to, $from);
		mail($to, $assunto, $html, $headers1);
	}

	public static function interesse ($objeto, $send = true, $toDefault = 'contato@iefap.com.br') {

		$htmls = array();

		// headers
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'To: %s' . "\r\n";
		$headers .= 'From: %s' . "\r\n";
		$headers .= 'Bcc: sauloroliveira@gmail.com' . "\r\n";

		$from = 'contato@iefap.com.br';

		// não tirar isso daqui!
		$tipo = 'inscrição';
		if ($objeto->tipoCurso == Curso::CURSO_TIPO_POS) {
			$tipo = 'matrícula';
		}

        ob_start();
        include DIR_ROOT . '/views/emails/inscricoes/interesse/email.php';
        $html = ob_get_contents();
        ob_end_clean();

        if (!$send) {
        	$htmls['email'] = $html;
        }

        // Envia o primeiro e-mail
        // E-mail que vai pra pessoa que fez o contato
        if ($send) {
        	$to = $objeto->email;
	        $assunto = 'IEFAP - Cadastro de interesse realizado';
			$headers1 = sprintf($headers, $to, $from);
			mail($to, $assunto, $html, $headers1);
        }

		ob_start();
        include DIR_ROOT . '/views/emails/inscricoes/interesse/notificacao.php';
        $html = ob_get_contents();
        ob_end_clean();

        if (!$send) {
        	$htmls['notificacao'] = $html;
        }

        // Envia o segundo e-mail
        // E-mail que vai pro IEFAP como notificação de nova mensagem
        if ($send) {
        	$to = $toDefault;
	        $assunto = $objeto->nomeCurso . ' - Cadastro de interesse realizado através do site';
			$headers1 = sprintf($headers, $to, $from);
			mail($to, $assunto, $html, $headers1);
        }

        if (!$send) {
        	return $htmls;
        }
	}

	public static function inscricao ($objeto, $send = true, $toDefault = 'contato@iefap.com.br') {
		
		$htmls = array();

		// headers
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'To: %s' . "\r\n";
		$headers .= 'From: %s' . "\r\n";
		$headers .= 'Bcc: sauloroliveira@gmail.com' . "\r\n";

		$from = 'contato@iefap.com.br';

		$tipo = 'inscrição';
		if ($objeto->tipoCurso == Curso::CURSO_TIPO_POS) {
			$tipo = 'matrícula';
		}

		ob_start();
        include DIR_ROOT . '/views/emails/inscricoes/inscricao/email.php';
        $html = ob_get_contents();
        ob_end_clean();

        if (!$send) {
        	$htmls['email'] = $html;
        }

        // Envia o primeiro e-mail
        // E-mail que vai pra pessoa que fez o contato
        if ($send) {
			$to = $objeto->email;
	        $assunto = 'IEFAP - Inscricão realizada';
			$headers1 = sprintf($headers, $to, $from);
			mail($to, $assunto, $html, $headers1);
		}

		ob_start();
        include DIR_ROOT . '/views/emails/inscricoes/inscricao/notificacao.php';
        $html = ob_get_contents();
        ob_end_clean();

        if (!$send) {
        	$htmls['notificacao'] = $html;
        }

        // Envia o segundo e-mail
        // E-mail que vai pro IEFAP como notificação de nova mensagem
        if ($send) {
			$to = $toDefault;
	        $assunto = $objeto->nomeCurso . ' - Inscrição realizada através do site';
			$headers1 = sprintf($headers, $to, $from);
			mail($to, $assunto, $html, $headers1);
		}

		if (!$send) {
        	return $htmls;
        }
	}

	public static function matricula ($objeto, $send = true, $toDefault = 'contato@iefap.com.br') {
		
		// headers
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'To: %s' . "\r\n";
		$headers .= 'From: %s' . "\r\n";
		$headers .= 'Bcc: sauloroliveira@gmail.com' . "\r\n";

		$from = 'contato@iefap.com.br';

		$tipo = 'inscrição';
		if ($objeto->tipoCurso == Curso::CURSO_TIPO_POS) {
			$tipo = 'matrícula';
		}

		ob_start();
        include DIR_ROOT . '/views/emails/inscricoes/matricula/email.php';
        $html = ob_get_contents();
        ob_end_clean();

        if (!$send) {
        	$htmls['email'] = $html;
        }

        // Envia o primeiro e-mail
        // E-mail que vai pra pessoa que fez o contato
        if ($send) {
			$to = $objeto->email;
	        $assunto = 'IEFAP - Matrícula realizada';
			$headers1 = sprintf($headers, $to, $from);
			mail($to, $assunto, $html, $headers1);
		}

		ob_start();
        include DIR_ROOT . '/views/emails/inscricoes/matricula/notificacao.php';
        $html = ob_get_contents();
        ob_end_clean();

        if (!$send) {
        	$htmls['notificacao'] = $html;
        }

        // Envia o segundo e-mail
        // E-mail que vai pro IEFAP como notificação de nova mensagem
        if ($send) {
			$to = $toDefault;
	        $assunto = $objeto->nomeCurso . ' - Matrícula realizada através do site';
			$headers1 = sprintf($headers, $to, $from);
			mail($to, $assunto, $html, $headers1);
		}

		if (!$send) {
        	return $htmls;
        }
	}

	/**
	*	Função que envia notificações quando alguém envia uma mensagem através do formulário
	*	do site. Um e-mail é enviado a pessoa que enviou a mensagem e outro é enviado ao IEFAP
	*	para notificar que uma nova mensagem foi enviada.
	*	@param $objeto Objeto contato
	*	@param $destinoIefap Endereço(s) de e-mail(s) do IEFAP que devem receber a notificação
	*						 da nova mensagem 
	*/
	public static function contato ($objeto, $toDefault = 'contato@iefap.com.br') {

		// headers
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'To: %s' . "\r\n";
		$headers .= 'From: %s' . "\r\n";
		$headers .= 'Bcc: sauloroliveira@gmail.com' . "\r\n";

		$from = 'contato@iefap.com.br';

		ob_start();
        include DIR_ROOT . '/views/emails/contato/email.php';
        $html = ob_get_contents();
        ob_end_clean();

        // Envia o primeiro e-mail
        // E-mail que vai pra pessoa que fez o contato
		$to = $objeto->email;
        $assunto = 'IEFAP - Contato realizado através do site';
		$headers1 = sprintf($headers, $to, $from);
		mail($to, $assunto, $html, $headers1);

		ob_start();
        include DIR_ROOT . '/views/emails/contato/notificacao.php';
        $html = ob_get_contents();
        ob_end_clean();

        // Envia o segundo e-mail
        // E-mail que vai pro IEFAP como notificação de nova mensagem
		$to = $toDefault;
        $assunto = 'Novo contato realizado através do site';
		$headers1 = sprintf($headers, $to, $from);
		mail($to, $assunto, $html, $headers1);
	}

	/**
	*	Função que envia e-mail com a senha de acesso a área do aluno.
	*	@param $objeto Usuário da área do aluno
	*/
	public static function recuperar ($objeto) {

		// headers
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'To: %s' . "\r\n";
		$headers .= 'From: %s' . "\r\n";
		$headers .= 'Bcc: sauloroliveira@gmail.com' . "\r\n";

		$from = 'contato@iefap.com.br';

		ob_start();
        include DIR_ROOT . '/views/emails/area-aluno/recupera.php';
        $html = ob_get_contents();
        ob_end_clean();

		$to = $objeto->email;
        $assunto = 'IEFAP - Área do aluno';
		$headers1 = sprintf($headers, $to, $from);
		mail($to, $assunto, $html, $headers1);
	}

	/**
	*	Função que envia e-mail de notificação ao usuário que cadastra a senha para ter acesso
	*	a área do aluno.
	*	@param $objeto Usuário da área do aluno
	*/
	public static function cadastrar ($objeto) {

		// headers
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'To: %s' . "\r\n";
		$headers .= 'From: %s' . "\r\n";
		$headers .= 'Bcc: sauloroliveira@gmail.com' . "\r\n";

		$from = 'contato@iefap.com.br';

		ob_start();
        include DIR_ROOT . '/views/emails/area-aluno/cadastra.php';
        $html = ob_get_contents();
        ob_end_clean();

		$to = $objeto->email;
        $assunto = 'IEFAP - Área do aluno';
		$headers1 = sprintf($headers, $to, $from);
		mail($to, $assunto, $html, $headers1);
	}

	public static function requerimento ($objeto, $toDefault = 'contato@iefap.com.br') {

		// headers
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'To: %s' . "\r\n";
		$headers .= 'From: %s' . "\r\n";
		$from = 'contato@iefap.com.br';
		$headers .= 'Bcc: sauloroliveira@gmail.com' . "\r\n";

        ob_start();
        include DIR_ROOT . '/views/emails/area-aluno/requerimentos/email.php';
        $html = ob_get_contents();
        ob_end_clean();

        // Envia o primeiro e-mail
        // E-mail que vai pra pessoa que fez o contato
		$to = $objeto->email;
        $assunto = 'IEFAP - Requerimento solicitado';
		$headers1 = sprintf($headers, $to, $from);
		mail($to, $assunto, $html, $headers1);

		ob_start();
        include DIR_ROOT . '/views/emails/area-aluno/requerimentos/notificacao.php';
        $html = ob_get_contents();
        ob_end_clean();

        // Envia o segundo e-mail
        // E-mail que vai pro IEFAP como notificação de nova mensagem
		$to = $toDefault;
        $assunto = 'Requerimento solicitado através do site';
		$headers1 = sprintf($headers, $to, $from);
		mail($to, $assunto, $html, $headers1);
	}
	
}


?>