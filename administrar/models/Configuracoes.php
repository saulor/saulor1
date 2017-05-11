<?php

class Configuracoes extends Model {
    
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
    protected $_chave;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_valor;

    protected $_table = 'configuracoes';
   
	const EMAIL_INSCRICOES = 1;
	const EMAIL_CONTATOS = 2;
	const EMAIL_DOCENTES = 3;
	const EMAIL_REPRESENTANTES = 4;
	const EMAIL_ADMINISTRATIVOS = 5;
	const EMAIL_PARCEIROS = 6;
	const EMAIL_REQUERIMENTOS = 7;
    const MANUTENCAO = 8;
	
	public static function getChaves() {
		return array(
			Configuracoes::EMAIL_INSCRICOES,
			Configuracoes::EMAIL_CONTATOS,
			Configuracoes::EMAIL_DOCENTES,
			Configuracoes::EMAIL_REPRESENTANTES,
			Configuracoes::EMAIL_ADMINISTRATIVOS,
			Configuracoes::EMAIL_PARCEIROS,
			Configuracoes::EMAIL_REQUERIMENTOS,
            Configuracoes::MANUTENCAO
		);
	}
    
    public function getEmails ($chave) {
        try {
            $objeto = parent::getOrFail(array(
                    'where' => array(
                        'chave' => $chave
                    )
                )
            );
            return $objeto->valor;
        }
        catch (Exception $e) {
            throw $e;
        }
    }
}

?>