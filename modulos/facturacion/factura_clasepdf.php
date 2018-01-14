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
	$this->rect(5,5,200,290);
	$this->asignar_base1(40);
	$this->line(5,$this->recuperar_base1(),205,$this->recuperar_base1());
	$this->Image('logo_nacer.png',115,10,85);
	$this->line(110,5,110,32);
	$this->line(5,32,205,32);
	$this->SetFont('Arial','B',18);
    $this->setxy(15,5);
	$this->Cell(80,10,"Resumen de Prestaciones");
	$this->SetFont('Arial','B',14);
	$this->setxy(5,18);
	$this->cell(85,5,"Periodo Actual DDJJ");
	$this->setxy(60,18);
	$this->cell(83,5,"Periodo Prestación");
	/*Para licitacion*/
	$this->SetFont('Arial','B',18);
    $this->setxy(5,31);
    $this->SetFillColor(234,234,234);
    $this->cell(200,9,'Número Resumen',1,1,'L',1);
	if($this->PageNo()!=1)
	{
	 $this->setxy(5,45);
	 $this->SetFontSize(11);
	 $this->cell(0,0,"Resumen de Prestaciones Periodo: ".$periodo." - Continuación",0,0,'C');
	 $this->nro_orden_compra($periodo);
	 $this->pasa_id_licitacion($id_factura);
	} 
	$this->Ln(20);
	//$this->SetAutoPageBreak(1);
}	

function nro_orden_compra($nro) {
	
	$this->SetFont('Arial','B',14);
    $this->setxy(23,23);
	$this->cell(20,5,$nro,1);
		
}

function nro_orden_compra1($nro) {
	
	$this->SetFont('Arial','B',14);
    $this->setxy(75,23);
	$this->cell(19,5,$nro,1);
		
}

function pasa_id_licitacion($id) {
	
	$this->SetFont('Arial','B',22);
	$this->setxy(60,31);
	$this->cell(80,9,$id);
}
///////////////// GABRIEL ///////////////////
function fecha_facturacion($fecha){
	$this->SetFont('Arial','',14);
	$this->setxy(175,$this->recuperar_base1()+2);
	$this->cell(26,6,$fecha,1,0,R);
}
////////////////////////////////////////////
function fecha($fecha) {
	
	$this->SetFont('Arial','',14);
	$this->setxy(35,$this->recuperar_base1()+2);
	$this->cell(26,6,$fecha,1,0,R);
}
function fecha_carga($fecha) {
	
	$this->SetFont('Arial','',14);
	$this->setxy(140,$this->recuperar_base1()+2);
	$this->cell(26,6,$fecha,1,0,R);
}

function proveedor($nombre) {
	
	$this->SetFont('Arial','',14);
	$this->setxy(35,$this->recuperar_base1()+10);
	$this->cell(100,6,$nombre,1,0,L);
}

function vendedor($nombre) {
	
	$this->SetFont('Arial','',14);
	$this->setxy(35,$this->recuperar_base1()+18);
	$this->cell(85,6,$nombre,1,0,L);
	
	
}


function entrega($fecha) {
	
	$this->SetFont('Arial','',14);
	$this->setxy(175,$this->recuperar_base1()+50);
	$this->cell(26,6,$fecha,1,0,L);
}

function lugar_entrega($string) {
	
	$this->SetFont('Arial','',14);
	$this->setxy(5,$this->recuperar_base1()+47);
	
	//Control del tamaño del string de lugar_entrega para que
    //no se pase de largo en el pdf
    $add="";
	$largo=strlen($string);
	$cant_nl=str_count_letra("\n",$string);
	$res=ceil($largo/79);
	while($cant_nl+$res>6)
	{$add="...";
	 $string=substr($string,0,$largo-3);
	 $largo=strlen($string);
	 $cant_nl=str_count_letra("\n",$string);
	 $res=ceil($largo/79);
	} 
	$string.=$add; 
	$this->MultiCell(195,5,$string);
	
}


function producto($nom_ape,$cantidad,$unitario,$moneda) {
	
	//Control para que haga un salto de pagina, si el producto generado
	//no entra en la pagina actual.
	$largo=strlen($nom_ape);
	$cant_nl=str_count_letra("\n",$nom_ape);
	$res=ceil($largo/55);
	$nro_pixeles_posibles=($res+$cant_nl)*6;
	if(($this->recuperar_base2()+$nro_pixeles_posibles)>290)
	{$this->SetAutoPageBreak(0);
		$this->AddPage();
		$this->asignar_base2(50);
		$this->flag=0;
	 //$this->SetAutoPageBreak(1);	
	}
	$this->setxy(5,$this->recuperar_base2());
	$this->SetFont('Arial','B',9);
	$y_inicial=$this->GetY();
	
	$this->MultiCell(125,6,$nom_ape,'TRLB','L');
    $y_posterior=$this->GetY();
    
	$this->setxy(130,$this->recuperar_base2());
	$this->cell(20,6,$cantidad,'TRBL',0,'C');
	$this->setxy(150,$this->recuperar_base2());
	$this->cell(30,6,$moneda,'TBL',0,'L');
	$this->setxy(150,$this->recuperar_base2());
	$unitario_formateado = number_format($unitario, 2, ',', '.');
	$this->cell(30,6,$unitario_formateado,'TRB',0,'R');
	$this->setxy(180,$this->recuperar_base2());
	$total=$cantidad*$unitario;
	$this->cell(25,6,$moneda,'TBL',0,'L');
	$this->setxy(180,$this->recuperar_base2());
	$total_formateado = number_format($total, 2, ',', '.');
	$this->cell(25,6,$total_formateado,'TRB',0,'R');
	
	$aux=$this->recuperar_base2()+ ($y_posterior-$y_inicial);
	$this->asignar_base2($aux);
	$this->cont++;
				
}

function _final($preciototal,$moneda,$firma1,$firma2,$firma3) {
	if(($this->recuperar_base2()+20)>287)
	{$this->SetAutoPageBreak(0);
		$this->AddPage();
		$this->asignar_base2(50);
		$this->flag=0;
	}
	$this->setxy(135,$this->recuperar_base2()+5);
	$this->cell(35,6,'Total:',1);
	$this->setxy(170,$this->recuperar_base2()+5);
	$this->cell(35,6,$moneda,'TBL',0,'L');
	$this->setxy(170,$this->recuperar_base2()+5);
	$preciototal_formateado= number_format($preciototal, 2, ',', '.');
	$this->cell(35,6,$preciototal_formateado,'TRB',0,'R');
	$this->setxy(150,$this->recuperar_base2()+15);
	if(($this->recuperar_base2()+35)>287)
	{$this->SetAutoPageBreak(0);
		$this->AddPage();
		$this->asignar_base2(50);
		$this->flag=0;
	}	
	$this->setxy(6,$this->recuperar_base2()+23);
	if(($this->recuperar_base2()+35)>287)
	{$this->SetAutoPageBreak(0);
		$this->AddPage();
		$this->asignar_base2(50);
		$this->flag=0;
	}
	$this->SetFont('Arial','B',14);
	$this->cell(50,6,'Sin otro particular, saluda atentamente:');
	$this->setxy(100,$this->recuperar_base2()+28);
	if(($this->recuperar_base2()+35)>287)
	{$this->SetAutoPageBreak(0);
		$this->AddPage();
		$this->asignar_base2(50);
		$this->flag=0;
	}
	$this->SetFont('Arial','B',12);
	$this->cell(50,6,'',0,0,'C');
	$this->setxy(100,$this->recuperar_base2()+33);
	if(($this->recuperar_base2()+35)>287)
	{$this->SetAutoPageBreak(0);
		$this->AddPage();
		$this->asignar_base2(50);
		$this->flag=0;
	}
	$this->cell(50,6,$firma2,0,0,'C');
	$this->setxy(100,$this->recuperar_base2()+38);
	if(($this->recuperar_base2()+35)>287)
	{$this->SetAutoPageBreak(0);
		$this->AddPage();
		$this->asignar_base2(50);
		$this->flag=0;
	}
	$this->cell(50,6,$firma3,0,0,'C');
}
	

function dibujar_planilla() {
		
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
//	$this->setxy(5,$this->recuperar_base1()+2);
//	$this->cell(30,6,'Nº Orden de Compra ');
	$this->setxy(5,$this->recuperar_base1()+2);
	$this->cell(30,6,'Fecha');	
	$this->setxy(100,$this->recuperar_base1()+2);
	$this->cell(30,6,'Fecha de Carga');
	$this->setxy(5,$this->recuperar_base1()+10);
	$this->cell(30,6,'Efector ');
	$this->setxy(5,$this->recuperar_base1()+18);
	$this->cell(30,6,'CUIE ');
	$this->setxy(7,$this->recuperar_base1()+28);
	$this->SetFont('Arial','',12);
	$this->cell(200,5,'Por la presente cumplo  dirigirme a  Usted solicitando  la  provisión de las prestaciones que  detallo a ');
	$this->setxy(7,$this->recuperar_base1()+33);
	$this->cell(200,5,'continuación. Solicito me responda por este mismo medio la conformidad de la misma.');
	$this->setxy(5,$this->recuperar_base1()+40);
	$this->cell(35,6,'Observaciones: ');
	$this->Rect(5,$this->recuperar_base1()+40,200,20);
	$this->setxy(7,$this->recuperar_base1()+28);	
	$this->SetFont('Arial','B',11);
	$this->setxy(5,$this->recuperar_base1()+60);
	$this->cell(125,6,'DATOS','RLTB',0,'C');	
	$this->setxy(130,$this->recuperar_base1()+60);
	$this->cell(20,6,'Cantidad','RLTB',0,'C');
	$this->setxy(150,$this->recuperar_base1()+60);
	$this->cell(30,6,'Unitario','RLTB',0,'C');
	$this->setxy(180,$this->recuperar_base1()+60);
	$this->cell(25,6,'Subtotal','RLTB',0,'C');
	
} //de la funcion dibujar_planilla

function guardar_servidor($string) {
 $this->output("$string",true,false);                                                   
}//fin de funcion
                                    
function Footer()
{
 //ir a 1.2 cm del final de la hoja
    $this->SetY(-12);
    //letra italica
    $this->SetFont('Arial','I',8);
    //imprime nro de pagina y total de paginas 
    $this->Cell(0,10,'Liquidacion de Prestaciones - Página '.$this->PageNo().'/total_pag',0,0,'C');
	
}   
                               
}//fin de la clase
?>