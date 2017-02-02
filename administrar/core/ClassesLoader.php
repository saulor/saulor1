<?php

class ClassesLoader
{
	public static function Register() {
		return spl_autoload_register(array('ClassesLoader', 'Load'));
	}

	public static function Load($_class) {

		$path = DIR_ROOT . DS . "administrar" . DS;
		
		if(file_exists($path . "lib" . DS . $_class . ".php")) {
			require($path . "lib" . DS . $_class . ".php");
		}
		else if(file_exists($path . "models" . DS . $_class.".php")) {
			require($path . "models" . DS . $_class.".php");
		}
		else if(file_exists($path . "core" . DS . $_class.".php")) {
			require($path . "core" . DS . $_class.".php");
		}
		else if(file_exists($path . "helpers" . DS . $_class.".php")) {
			require($path . "helpers" . DS . $_class.".php");
		}
		else if(file_exists($path . "language" . DS . $_class.".php")) {
			require($path . "language" . DS . $_class.".php");
		}
	}
}
?>