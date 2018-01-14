<?php

define('FPDF_FONTPATH','font/');
require('../../lib/fpdf.php');

class orden_compra extends FPDF
{
	var $base1;
	var $base2;
	var $cant;
	var $flag;

function asignar_base1($x) {
	 $this->base1=$x;
}

function recuperar_base1(){
	return $this->base1;
}

function asignar_base2($x) {
	 $this->base2=$x;
}

function recuperar_base2(){
	return $this->base2;
}

function Header() 
{global $periodo,$id_factura;
 	
	$this->cant=0;
	$this->flag=1;
	$this->rect(5,5,200,100);
	$this->asignar_base1(40);
	$this->line(5,$this->recuperar_base1(),205,$this->recuperar_base1());
	$this->Image('logo_nacer.png',115,10,85);
	$this->line(110,5,110,32);
	$this->line(5,32,205,32);
	$this->SetFont('Arial','B',18);
    $this->setxy(15,10);
	$this->Cell(80,10,"Constancia de Inscripción");
	$this->setxy(15,13);	
	$this->cell(80,20,"Fecha: ".date("d/m/y"));	
}	

function afiapellido($afiapellido) {
	
	$this->SetFont('Arial','',14);
	$this->setxy(28,$this->recuperar_base1()+2);
	$this->cell(70,6,$afiapellido,1,0,L);
}

function afinombre($afinombre) {
	
	$this->SetFont('Arial','',14);
	$this->setxy(123,$this->recuperar_base1()+2);
	$this->cell(80,6,$afinombre,1,0,L);
}

function afidni($afidni) {
	
	$this->SetFont('Arial','',14);
	$this->setxy(17,$this->recuperar_base1()+10);
	$this->cell(81,6,$afidni,1,0,L);
}

function nombre($nombre) {
	
	$this->SetFont('Arial','',14);
	$this->setxy(121,$this->recuperar_base1()+10);
	$this->cell(82,6,$nombre,1,0,L);
}

function fechainscripcion($fechainscripcion) {

	
	$this->SetFont('Arial','',14);
	$this->setxy(58,$this->recuperar_base1()+18);
	$this->cell(30,6,$fechainscripcion,1,0,L);
}

function afifechanac($afifechanac) {
	
	$this->SetFont('Arial','',14);
	$this->setxy(153,$this->recuperar_base1()+18);
	$this->cell(30,6,$afifechanac,1,0,L);
}

function activo($activo) {
	$this->SetFont('Arial','B',18);
	$this->setxy(5,32);
	$this->cell(5,9,"Nro. Transacción: $activo");
}

function clavebeneficiario($clavebeneficiario) {
	$this->SetFont('Arial','B',16);
	$this->setxy(107,32);
	$this->cell(5,9,"Clave Inscripto: $clavebeneficiario");
}

function dibujar_planilla($vencimiento) {
		
	$this->Open();
	$this->AliasNbPages();
	$this->AddPage();
	$this->SetFont('Arial','B',10);
	$this->SetAutoPageBreak(0);
	//Inicializo las bases:
	$this->asignar_base1(40);
	$this->asignar_base2(106);
	$this->AliasNbPages("total_pag"); 
	$this->line(5,$this->recuperar_base1(),205,$this->recuperar_base1());
	$this->SetFont('Arial','B',14);
	$this->setxy(5,$this->recuperar_base1()+2);
	$this->cell(30,6,'Apellido: ');	
	$this->setxy(100,$this->recuperar_base1()+2);
	$this->cell(30,6,'Nombre: ');
	$this->setxy(5,$this->recuperar_base1()+10);
	$this->cell(30,6,'DNI: ');
	$this->setxy(100,$this->recuperar_base1()+10);
	$this->cell(30,6,'Efector: ');
	$this->setxy(5,$this->recuperar_base1()+18);
	$this->cell(30,6,'Fecha de Inscripcion: ');
	$this->setxy(100,$this->recuperar_base1()+18);
	$this->cell(30,6,'Fecha de Nacimiento: ');
	$this->setxy(7,$this->recuperar_base1()+28);
	$this->SetFont('Arial','B',10);
	$this->cell(200,5,'Por la presente DEJO CONSTANCIA que la persona Antes Detallada esta INSCRIPTO EN EL PLAN NACER.');
	$this->setxy(7,$this->recuperar_base1()+33);
	$this->SetFont('Arial','',10);
	$this->cell(200,5,'Reune los requisitos necesarios para el proceso de validacion.');
	$this->setxy(7,$this->recuperar_base1()+40);
	$this->cell(200,5,'VALIDO PARA SER PRESENTADO ANTE AUTORIDADES DE ANSES.');
	$this->setxy(7,$this->recuperar_base1()+45);
	$this->cell(200,5,'VALIDO DENTRO DE LOS 60 DIAS DE LA FECHA DE EMISIÓN. (Vencimiento: '.$vencimiento.')');
	$this->setxy(7,$this->recuperar_base1()+52);
	$this->SetFont('Arial','B',13);
	$this->cell(20,6,'Sin otro particular, saluda atentamente:');
	
	
} //de la funcion dibujar_planilla

function guardar_servidor($string) {
 $this->output("$string",true,false);                                                   
}//fin de funcion                                    

                               
}//fin de la clase
?>