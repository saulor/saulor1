<?php

date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 0);
error_reporting(0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
error_reporting(E_ALL);
if (!isset($_SESSION['mensagemSe'])) {
	$_SESSION['mensagemSe'] = array();
}

require_once 'webtools/PHPWord_0.6.2_Beta/PHPWord.php';
require_once 'webtools/PHPExcel_1.7.9/Classes/PHPExcel.php';
require_once 'webtools/fpdf17/fpdf.php';
require_once 'webtools/pdfcrowd.php';

DEFINE ('PREFIX', 'iefap3_');
DEFINE ('SESSION_PREFIX', PREFIX);
DEFINE ('QUANTIDADE_POR_PAGINA', 10);
DEFINE ('QUANTIDADE_PAGINACAO', 12);
DEFINE ('LANGUAGE_CODE', 'pt_br');
DEFINE ('DS', '/');
DEFINE ('WWW_ROOT', 'http://localhost/~saulor/iefap');
DEFINE ('DIR_ROOT', dirname(dirname(__file__)));
DEFINE ('DIR_UPLOADS', DIR_ROOT . DS . 'assets' . DS . 'uploads');
DEFINE ('APPDIR', DIR_ROOT . DS);
DEFINE ('SITEURL', WWW_ROOT . DS);
DEFINE ('PATH_UPLOADS', '../assets/uploads');
DEFINE ('TEMPLATE', 'default');
DEFINE ('SITEPREFIX', 'IEFAP');
DEFINE ('SITETITLE', 'Instituto de Ensino, Formação e Aperfeiçoamento LTDA.');

?>