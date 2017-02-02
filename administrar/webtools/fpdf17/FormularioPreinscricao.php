<?php

class FormularioPreinscricao extends FPDF {
	
	private $certificadora;
	
	public function FormularioPreinscricao ($orientation='P', $unit='mm', $format='A4', $certificadora) {
		parent::FPDF($orientation, $unit, $format);
		$this->certificadora = $certificadora; 
	}

	public function Header() {
		$this->SetMargins(10,10,10);
		
		$certificadoraInfo = Curso::getCertificadoraInfo($this->certificadora);

		$nomeInstituicao = $certificadoraInfo["nome"];
		$logo = $certificadoraInfo["logo-impressao"];
		$x = $certificadoraInfo["x"];
		$y = $certificadoraInfo["y"];
		$tamanho = $certificadoraInfo["tamanho"];
		$r = $certificadoraInfo["r"];
		$g = $certificadoraInfo["g"];
		$b = $certificadoraInfo["b"];
		$nomeInstituicaoX = $certificadoraInfo["nomeInstituicaoX"];
		$nomeInstituicaoY = $certificadoraInfo["nomeInstituicaoY"];
		
		if ($certificadoraInfo["parceria"]) {
			$logo2 = 'imagens/logo-iefap-fundo-branco.gif';
			$this->Image($logo2,150,6,50);
		}
		
		$this->Image($logo,$x,$y,$tamanho);
		$this->SetFillColor($r, $g, $b); 
		$this->setY(27);
		$this->setX(10);
		$this->Cell(13,250,'',0,0,0,true,'');
		
		$this->SetY($nomeInstituicaoY);
		$this->SetX($nomeInstituicaoX);
		$this->SetFont('Helvetica','B',12);
		$this->SetTextColor($r, $g, $b);
		$this->MultiCell(0,5,utf8_decode($nomeInstituicao));
		
	}
	
	public function Footer() {
		$this->SetY(-15);
		$this->SetFont('Arial','',8);
		$this->SetTextColor(128);
	}

}


?>