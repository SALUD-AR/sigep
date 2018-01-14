<?php
//require('fpdf.php');
require('fpdf.php');
require_once("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

//$pdf=new FPDF();
class PDF extends FPDF
{
//Cabecera de página
function Header()
{
    //Logo
    $this->Image('prueba_pdf.jpg',140,5,200);
      
    $this->Ln(20);
}

//Pie de página
function Footer()
{/*
    //Posición: a 1,5 cm del final
    $this->SetY(-15);
    //Arial italic 8
    $this->SetFont('Arial','I',8);
    //Número de página
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');*/
}
}



$pdf=new PDF('L','mm','Legal');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Line(130,45,350,45);
$pdf->Line(350,45,350,200);
$pdf->Line(130,45,130,200);
$pdf->Line(130,200,350,200);
$pdf->SetFont('Arial','B',12);
$pdf->Ln(20);
$pdf->Cell(125);
$pdf->Cell(130,8,"Nº EXPEDIENTE INTERNO: (Col. B-Anexo I):");
$pdf->SetFont('Arial','BIU',14);
$pdf->Cell(0,8,"$nro_exp (Fact. $id_factura)");
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(125);
$pdf->Cell(130,8,"PRESTADOR/EFECTOR: (Col. C-Anexo I):");
$pdf->SetFont('Arial','BIU',14);
$pdf->Cell(0,8,$nombre);
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(125);
$pdf->Cell(130,8,"FECHA DE INGRESO: (Col. A-Anexo I):");
$pdf->SetFont('Arial','BIU',14);
$pdf->Cell(0,8,$fecha_ing);
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(125);
$pdf->Cell(130,8,"PERIODO FACTURADO: (Col. D- Anexo I):");
$pdf->SetFont('Arial','BIU',14);
$pdf->Cell(0,8,$periodo);
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(125);
$pdf->Cell(130,8,"MONTO CUASIFACTURA: (Col. E-Anexo I):");
$pdf->SetFont('Arial','BIU',14);
$pdf->Cell(0,8,$monto);
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(125);
$pdf->Cell(130,8,"MONTO CREDITO C/ LIQUIDACION: (Col. F-Anexo I): ..........................................................");
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(125);
$pdf->Cell(130,8,"EXPEDIENTE ADMINISTRATIVO: (Col. G-Anexo I): ..............................................................");
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(125);
$pdf->Cell(145,8,"FECHA DE NOTIFICACION AL PRESTADOR: (Col. H-Anexo I): ..........................................");
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(125);
$pdf->Cell(60,8,"VTO. de los 50 DIAS:");
$pdf->SetFont('Arial','BIU',14);	
$pdf->Cell(0,8,$plazo_pago);
$pdf->Output();
?>