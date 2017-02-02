<?php

class ArrayMethods {

	public function __construct () {
	
	}
	
	public function __clone () {
	
	}
	
	public static function clean ($array) {
		return array_filter($array, function($item) {
			return !empty($item);
		});
	}
	
	public static function trim ($array) {
		return array_map(function($item) {
			return trim($item);
		}, $array);
	}
	
	// Método transforma um array num objeto
	public static function toObject($array) {
		// stdClass classe pré-definida ou genérica, criada quando se deseja fazer typecasting para objeto
		$result = new stdClass(); 
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$result->{$key} = self::toObject($value);
			}
			else {
			$result->{$key} = $value;
			}
		}
		return $result;
	}
	
	public static function flatten ($array, $return = array()) {
		foreach ($array as $key => $value) {
			if (is_array($value) || is_object($value)) {
				$return = self::flatten($value, $return);
			}
			else {
				$return[] = $value;
			}
		}
		return $return;
	}
	
}

?>