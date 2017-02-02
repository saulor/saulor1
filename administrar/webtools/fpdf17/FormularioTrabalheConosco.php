<?php

class FormularioTrabalheConosco extends FPDF {
	
	private $curso;
	
	function Formulario ($orientation='P', $unit='mm', $format='A4') {
		parent::FPDF($orientation, $unit, $format);
		$this->curso = $curso;
	}

	function Header() {
		$this->SetMargins(10,10,10);
		$nomeInstituicao = 'IEFAP';
		$nomeInstituicao = 'Instituto de Ensino, Formação e Aperfeiçoamento LTDA.';
		$nomeInstituicao = 'Trabalhe Conosco';
		$logo = '../imagens/logo.png';
		$x = 10;
		$y = 5;
		$tamanho = 25;
		$nomeInstituicaoX = 40;
		$nomeInstituicaoY = 5;
		$this->SetFillColor(181, 181, 181); 
		$this->setY(27);
		$this->setX(10);
		$this->Cell(13,270,'',0,0,0,true,'');
		$this->Image($logo,$x,$y,$tamanho);
		$this->SetY($nomeInstituicaoY);
		$this->SetX($nomeInstituicaoX);
		$this->SetFont('Helvetica','B',12);
		$this->MultiCell(130,6,utf8_decode('IEFAP - Instituto de Ensino, Formação e Aperfeiçoamento LTDA.'));
		$this->SetY($nomeInstituicaoY + 15);
		$this->SetX($nomeInstituicaoX);
		$this->Cell(0,0, 'Trabalhe Conosco');
	}
	
	function Footer() {
		$this->SetY(-15);
		$this->SetFont('Arial','',8);
		$this->SetTextColor(128);
		$this->Cell(0,10,utf8_decode('IEFAP - Instituto de Ensino, Formação e Aperfeiçoamento LTDA.'),0,0,'C');
	}
	
	function setTitulo ($titulo) {
		$this->setTextColor(0,0,0);
		$this->setY($this->getY() + 15);
		$this->setX($this->getX() + 17);
		$this->SetFont('Helvetica','B',11);
		$this->Cell(0,0,utf8_decode($titulo));
		$this->setY($this->getY() + 5);
	}
	
	function campo ($nome, $valor) {
		$this->SetFont('Helvetica','B',9);
		$this->setY($this->getY() + 7);
		$this->setX($this->getX() + 17);
		$this->Cell(0,0,utf8_decode($nome));
		$this->setY($this->getY() + 6);
		$this->setX($this->getX() + 18);
		$this->Cell(strlen($valor)*2,0.1,'',0,0,0,true,'');
		$this->SetFont('Helvetica','',9);
		$this->setY($this->getY() - 2);
		$this->setX($this->getX() + 17);
		$this->Cell(0,0,utf8_decode($valor));
		$this->SetFont('Helvetica','B',9);
	}

}


?>