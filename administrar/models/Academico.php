<?php

class Academico extends Model {

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
	* @type integer
	*/
	protected $_codigoAluno;

	/**
	* @column
	* @readwrite
	* @type text
	* @length 255
	*/
	protected $_numSeqAluno;

	/**
	* @column
	* @readwrite
	* @type text
	* @length 255
	*/
	protected $_nomeAluno;

	/**
	* @column
	* @readwrite
	* @type date
	*/
	protected $_dataMatAluno;

	/**
	* @column
	* @readwrite
	* @type text
	* @length 255
	*/
	protected $_situacaoAluno;

	/**
	* @column
	* @readwrite
	* @type integer
	*/
	protected $_codigoCurso;

	/**
	* @column
	* @readwrite
	* @type text
	* @length 255
	*/
	protected $_nomeCurso;

	/**
	* @column
	* @readwrite
	* @type text
	*/
	protected $_nomeDisciplina;

	/**
	* @column
	* @readwrite
	* @type integer
	*/
	protected $_cargaHorDisciplina;

	/**
	* @column
	* @readwrite
	* @type decimal
	* @length 14
	*/
	protected $_notaAluno;

	/**
	* @column
	* @readwrite
	* @type text
	* @length 10
	*/
	protected $_situacaoNota;

	/**
	* @column
	* @readwrite
	* @type integer
	*/
	protected $_numeroFaltas;

	/**
	* @column
	* @readwrite
	* @type decimal
	* @length 14
	*/
	protected $_notaTrabalho;

	/**
	* @column
	* @readwrite
	* @type text
	* @length 255
	*/
	protected $_periodoRelatorio;

	/**
	* @column
	* @readwrite
	* @type text
	* @length 150
	*/
	protected $_email;

	/**
	* @column
	* @readwrite
	* @type text
	* @length 15
	*/
	protected $_cpf;

	/**
	* @column
	* @readwrite
	* @type date
	*/
	protected $_dataFimCurso;

	/**
	* @column
	* @readwrite
	* @type decimal
	* @length 14
	*/
	protected $_notaSubstituida;

	/**
	* @column
	* @readwrite
	* @type date
	*/
	protected $_dataReposicao;

	/**
	* @column
	* @readwrite
	* @type text
	*/
	protected $_obsReposicao;

	/**
	* @column
	* @readwrite
	* @type text
	* @length 255
	*/
	protected $_unidade;

	/**
	* @column
	* @readwrite
	* @type text
	* @length 255
	*/
	protected $_professor;

	/**
	* @column
	* @readwrite
	* @type text
	* @length 255
	*/
	protected $_titulo;

	/**
	* @column
	* @readwrite
	* @type date
	*/
	protected $_dataInicio;

	/**
	* @column
	* @readwrite
	* @type date
	*/
	protected $_dataFim;

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

	protected $_table = 'academico';

}

?>
