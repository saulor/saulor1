<?php

class FPDF_Autoloader {

	public static function Register() {
		return spl_autoload_register(array('FPDF_Autoloader', 'Load'));
	}

	public static function Load($strObjectName) {
		
		if((class_exists($strObjectName)) || (strpos($strObjectName, 'PHPWord') === false)) {
			return false;
		}

		$strObjectFilePath = PHPWORD_BASE_PATH . str_replace('_', '/', $strObjectName) . '.php';
		
		if((file_exists($strObjectFilePath) === false) || (is_readable($strObjectFilePath) === false)) {
			return false;
		}
		
		require($strObjectFilePath);
	}
	
}

?>