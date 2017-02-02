<?php

class AcademicoUsuario extends Model {

	/**
	* @column
	* @readwrite
	* @primary
	* @type autonumber
	*/
	protected $_id;

	/**
	* @column
	* @readwrite
	* @type text
	* @length 14
	*/
	protected $_cpf;

	/**
	* @column
	* @readwrite
	* @type text
	* @length 255
	*/
	protected $_email;

	/**
	* @column
	* @readwrite
	* @type text
	* @length 32
	*/
	protected $_senha;

	/**
	* @column
	* @readwrite
	* @type datetime
	*/
	protected $_data;

	/**
	* @column
	* @readwrite
	* @type integer
	*/
	protected $_timestamp;

	protected $_table = 'academico_usuarios';

}

?>
