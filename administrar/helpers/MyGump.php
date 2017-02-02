<?php

class MyGump extends Gump {

  private $lang;

  public function __construct () {
    $this->lang = require APPDIR . 'administrar/language/pt_br/Gump.php';
  }

  protected function filter_encode_entities ($value, $params = NULL) {
      return htmlentities($value, ENT_NOQUOTES, 'utf-8');
  }

  protected function filter_decode_entities ($value, $params = NULL) {
      return html_entity_decode($value, ENT_NOQUOTES, 'utf-8');
  }

  protected function filter_encode_date ($value, $params = NULL) {
      return Funcoes::encodeDate($value);
  }

  // protected function filter_encode_datetime ($value, $params = NULL) {
  //     return \Helpers\Date::encodeDatetime($value);
  // }

  protected function filter_integer ($value, $params = NULL) {
      return (int) $value;
  }

  protected function filter_md5 ($value, $params = NULL) {
      return md5($value);
  }

  protected function filter_decimal ($value, $params = NULL) {
  	$v = "";
  	if (strpos($value, ",") !== false) {
  		$partes = explode(".", $value);
  		foreach ($partes as $parte) {
  			$v .= $parte;
  		}
  		$value = $v;
  	}
  	return (float) str_replace(",", ".", $value);
  }

  protected function validate_valid_cep ($field, $input, $param = NULL) {
      if(!isset($input[$field]) || empty($input[$field]))
      {
          return;
      }

      // Theory: 1 number, 1 or more spaces, 1 or more words
      $isCep = preg_match('/[0-9]{5}-[0-9]{3}/', $input[$field]);

      if(!$isCep) {
          return array(
              'field' => $field,
              'value' => $input[$field],
              'rule'  => __FUNCTION__,
              'param' => $param
          );
      }
  }

  /**
   * Determine if the provided input is a valid date (ISO 8601).
   *
   * Usage: '<index>' => 'date'
   *
   * @param string $field
   * @param string $input date ('Y-m-d') or datetime ('Y-m-d H:i:s')
   * @param null   $param
   *
   * @return mixed
   */
  protected function validate_valid_date($field, $input, $param = null)
  {
      if (!isset($input[$field]) || empty($input[$field])) {
          return;
      }

      // convert date to format yyyy-mm-dd
      list($dia, $mes, $ano) = explode('/', $input[$field]);
      $dataIn = $ano . '-' . $mes . '-' . $dia;

      $cdate1 = date('Y-m-d', strtotime($dataIn));
      $cdate2 = date('Y-m-d H:i:s', strtotime($dataIn));
      
      if ($cdate1 != $dataIn && $cdate2 != $dataIn) {
          return array(
              'field' => $field,
              'value' => $input[$field],
              'rule' => __FUNCTION__,
              'param' => $param,
          );
      }
  }

  /**
   * Shorthand method for inline validation
   *
   * @param array $data The data to be validated
   * @param array $validators The Gump validators
   * @return mixed True(boolean) or the array of error messages
   */
  public static function is_valid(array $data, array $validators)
  {
      $gump = new MyGump();

      $gump->validation_rules($validators);

      if($gump->run($data) === false) {
          return $gump->get_readable_errors(false);
      } else {
          return true;
      }
  }

  public function get_readable_errors($convert_to_string = false, $field_class="field", $error_class="error-message") {
    
    if(empty($this->errors)) {
        return ($convert_to_string)? null : array();
    }

    $resp = array();

    foreach($this->errors as $e) {

        $field = ucwords(str_replace(array('_','-'), chr(32), $e['field']));
        $param = $e['param'];

        // Let's fetch explicit field names if they exist
        if(array_key_exists($e['field'], self::$fields)) {
            $field = self::$fields[$e['field']];
        }

        switch($e['rule']) {

            case 'validate_max_len' :
            case 'validate_min_len' :
            case 'validate_exact_len' :
            case 'validate_min_numeric' :
            case 'validate_max_numeric' :
              $find = array(':field', ':param');
              $replace = array($field, $param);
              $resp[] = str_replace($find, $replace, $this->lang[$e['rule']]);
            break;

            case 'validate_contains' :
              $find = array(':field', ':values');
              $replace = array($field, implode(', ', $param));
              $resp[] = str_replace($find, $replace, $this->lang[$e['rule']]);
            break;

            default :
              $resp[] = str_replace(':field', $field, $this->lang[$e['rule']]);
            break;

            
        }
    }

    if(!$convert_to_string) {
        return $resp;
    } else {
        $buffer = '';
        foreach($resp as $s) {
            $buffer .= "<span class=\"$error_class\">$s</span>";
        }
        return $buffer;
    }
  }
}

?>
