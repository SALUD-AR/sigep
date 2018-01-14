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
    $this->Image('logo_ministerio.jpg',15,37,35);
    $this->Image('logo_sumar.jpg',63,30,35);
      
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

$fecha_carga=date("d-m-Y");
$sql_fact="select * from (
select * from cardiopatia.factura where id_factura='$id_factura' ) as tabla1
left join cardiopatia.efector using (id_efector)";
$res_sql=sql($sql_fact,"no se pudo ejecutar la consulta de factura") or die();
$orden_pago=$res_sql->fields['orden_pago'];
$efector=$res_sql->fields['nombre'];
$direccion=$res_sql->fields['domicilio'];
$cuit=$res_sql->fields['cuit'];
$cheque_nombre=$res_sql->fields['cheque_a_nombre_de'];
$cbu=$res_sql->fields['cbu'];
$num_cuenta=$res_sql->fields['numero_cuenta'];
$tipo_pago=$res_sql->fields['tipo_pago'];
$expediente=$res_sql->fields['expediente_interno'];
$comprobante=$res_sql->fields['comprobante'];
$total_bruto=number_format($res_sql->fields['total'],2,'.','');

$sql_comp="SELECT cardiopatia.comprobantes.*,
	cardiopatia.prestaciones.id_prestacion,
	cardiopatia.prestaciones.codigo,
	cardiopatia.prestaciones.patologia,
	cardiopatia.prestaciones.cirugia,
	cardiopatia.prestaciones.id_nomenclador,
	cardiopatia.prestaciones.valor as valor_practica
	from cardiopatia.comprobantes 
	inner join cardiopatia.prestaciones on comprobantes.id_prestacion=prestaciones.id_prestacion
	where comprobantes.id_factura='$id_factura'";
$res_comp=sql ($sql_comp,"no se pudo ejecutar la consulta de comprobantes") or die();

$sql_benef="SELECT cardiopatia.beneficiario.*
	from cardiopatia.beneficiario
	inner join cardiopatia.factura on beneficiario.id_beneficiario=factura.id_beneficiario
	where factura.id_factura=$id_factura";
$res_benefic=sql($sql_benef,"No se pudo ejecutar la consulta de beneficiario");

$dni=$res_benefic->fields['dni'];
$nombre_benef=$res_benefic->fields['apellido']." ".$res_benefic->fields['nombre'];
$fecha_nac=$res_benefic->fields['fechanacimiento'];


$pdf=new PDF('P','mm','Legal');
$pdf->AliasNbPages();
$pdf->AddPage();
//cuadrado mayor
$pdf->Line(10,10,210,10);
$pdf->Line(10,10,10,340);
$pdf->Line(10,340,210,340);
$pdf->Line(210,10,210,340);
//lineas horizontales de arriba hacia abajo
$pdf->Line(10,75,210,75);
$pdf->Line(10,90,210,90);
$pdf->Line(10,105,210,105);
$pdf->Line(10,115,210,115);////
$pdf->Line(10,175,210,175);
$pdf->Line(10,200,210,200);
$pdf->Line(10,215,210,215);
$pdf->Line(10,230,210,230);/////
$pdf->Line(10,235,210,235);
$pdf->Line(10,240,210,240);
$pdf->Line(10,265,210,265);
$pdf->Line(10,280,210,280);
$pdf->Line(10,295,210,295);
$pdf->Line(85,305,210,305);

//lineas verticales
$pdf->Line(100,10,100,75);
$pdf->Line(10,10,10,340);

$pdf->Line(30,105,30,175);
$pdf->Line(55,105,55,175);
$pdf->Line(165,105,165,175);
$pdf->Line(180,105,180,175);
$pdf->Line(195,105,195,175);

$pdf->Line(35,235,35,265);
$pdf->Line(55,235,55,265);
//$pdf->Line(30,235,30,265);
$pdf->Line(123,235,123,265);

$pdf->Line(105,280,105,295);

$pdf->Line(85,295,85,340);
$pdf->Line(115,295,115,340);
$pdf->Line(145,295,145,340);

$pdf->SetFont('Arial','B',10);
$pdf->Text(110,16,"Fecha:");
$pdf->SetFont('Arial','',10);
$pdf->Text(130,16,"$fecha_carga");

$pdf->SetFont('Arial','B',10);
$pdf->Text(110,25,"Orden de Pago:");
$pdf->SetFont('Arial','',10);
$pdf->Text(140,25,"$orden_pago");
$pdf->SetFont('Arial','B',10);
$pdf->Text(110,34,"Efector:");
$pdf->SetFont('Arial','',8);
$pdf->Text(130,34,"$efector");
$pdf->SetFont('Arial','B',10);
$pdf->Text(110,43,"Domicilio:");
$pdf->SetFont('Arial','',8);
$pdf->Text(130,43,"$direccion");
$pdf->SetFont('Arial','B',10);
$pdf->Text(110,52,"C.U.I.T.:");
$pdf->SetFont('Arial','',10);
$pdf->Text(140,52,"$cuit");
if ($tipo_pago=="Cheque"){
$pdf->SetFont('Arial','B',10);
$pdf->Text(110,61,"Cheque a Nombre de:");
$pdf->SetFont('Arial','',10);
$pdf->Text(150,61,"$cheque_nombre");}
else {
$pdf->SetFont('Arial','B',10);
$pdf->Text(110,61,"C.B.U.:");
$pdf->SetFont('Arial','',10);
$pdf->Text(135,61,"$cbu");
$pdf->SetFont('Arial','B',10);
$pdf->Text(110,70,"Nº de Cuenta:");
$pdf->SetFont('Arial','',10);
$pdf->Text(135,70,"$num_cuenta");
};
$pdf->SetFont('Arial','B',10);
$pdf->Text(20,82,"Expediente:");
$pdf->SetFont('Arial','',12);
$pdf->Text(50,82,"$expediente");

$pdf->SetFont('Arial','',8);
$pdf->Text(110,78,"Nombre Beneficiario:");
$pdf->Text(110,83,"Documento:");
$pdf->Text(110,88,"Fecha de Nacimiento:");
$pdf->SetFont('Arial','B',8);
$pdf->Text(140,78,"$nombre_benef");
$pdf->Text(140,83,"$dni");
$fecha_nac=fecha($fecha_nac);
$pdf->Text(140,88,"$fecha_nac");


$pdf->SetFont('Arial','B',12);
$pdf->Text(80,98,"COMPROBANTES LIQUIDACION:");

$pdf->SetFont('Arial','B',8);
$pdf->Text(17,108,"Tipo");
$pdf->SetFont('Arial','',8);
$pdf->Text(14,122,"Factura");
$pdf->SetFont('Arial','B',8);
$pdf->Text(11,112,"Comprobante");
$pdf->SetFont('Arial','B',8);
$pdf->Text(38,108,"Numero");
$pdf->SetFont('Arial','B',8);
$pdf->Text(32,112,"de Comprobante");
$pdf->SetFont('Arial','',8);
$pdf->Text(38,122,"$comprobante");
$pdf->SetFont('Arial','B',8);
$pdf->Text(110,110,"Concepto");
$pdf->SetFont('Arial','B',8);
$pdf->Text(166,110,"Cantidad");
$pdf->SetFont('Arial','B',8);
$pdf->Text(182,110,"Importe");
$pdf->Text(195,110,"Diferencia");

$y_com=122;
$total_diff=0;
$total_bruto=0;
while (!$res_comp->EOF){
	$id_prestacion=$res_comp->fields['id_prestacion'];

	if ($id_prestacion>=22 and $id_prestacion<=27) $valor=number_format($res_comp->fields['valor_practica'],2,'.','');
		else $valor=number_format($res_comp->fields['valor'],2,'.','');
	
	
	if ($id_prestacion==21) $codigo="-----------";
		else $codigo=$res_comp->fields['codigo'];
	
	$cantidad=$res_comp->fields['cantidad'];
	$total=$valor*$cantidad;
	$pdf->SetFont('Arial','',8);
    //$pdf->Text(172,$y_com,"$cantidad");
    //$valor=$res_comp->fields['neto'];
    $valor_con_formato=number_format($valor,2,'.','');
    $neto=$valor*$cantidad;
    $neto=number_format($neto,2,'.','');
    $valor_real=$res_comp->fields['valor'];
    $valor_real=$valor_real*$cantidad;
    /*switch ($codigo){
    	case 1: $diff=0;$total_bruto=$total_bruto+$neto;;break;
    	case 2: $diff=$valor_real-$neto;break;
    	case 3: $diff=$neto-$valor_real;$total_bruto=$total_bruto+$neto;;break;
    	default: $diff=$valor_real-$neto;$total_bruto=$total_bruto+$neto;;break;
    	}*/
    $diff=0;
    $diff=number_format($diff,2,'.','');
    $total_diff=$total_diff+$diff;
    $total_bruto=$total_bruto+$neto;
    
	//$pdf->Text(181,$y_com,"$ $neto");
	
	
	$valor_real=number_format($valor_real,2,'.','');
	
	switch ($id_prestacion){
		case 21:{$pdf->SetFont('Arial','',6);
    			$pdf->Text(60,$y_com,"$codigo");
    			$id_modulo=$res_comp->fields['id_modulo'];
				$pdf->Text(72,$y_com,"$id_modulo....($ $valor_con_formato)");
				break;}
		
		default: {$pdf->Text(60,$y_com,"$codigo");
					$pdf->SetFont('Arial','',6);
    				$patologia=$res_comp->fields['patologia'];
    				$cirugia=$res_comp->fields['cirugia'];
    				$modulo=$res_comp->fields['id_modulo'];
    				$practica=$patologia.".//.".$cirugia.".//.".$modulo;
    				//$practica=str_replace("Prácticas de Alta Complejidad","P.A.C.",$practica);
    				if (strlen($practica)<36 ){
					$pdf->Text(72,$y_com,"$practica....($ $valor_con_formato)");}
					else {$parte_practica=explode (".//.",$practica);
	      			$pdf->Text(72,$y_com,"$parte_practica[0]");;
	      			$y_com=$y_com+5;	 
	      			$pdf->Text(72,$y_com,"$parte_practica[1]");
	      			$y_com=$y_com+5;	 
	      			$pdf->Text(72,$y_com,"$parte_practica[2]....($ $valor_con_formato)");}
    				break;}
			}
		  	
    $pdf->Text(172,$y_com,"$cantidad");
	$pdf->Text(181,$y_com,"$ $neto");
	$pdf->Text(196,$y_com,"$ $diff");
	$pdf->SetFont('Arial','',8);
	$y_com=$y_com+5;
    $res_comp->MoveNext();
};


$pdf->SetFont('Arial','B',10);
$pdf->Text(100,185,"IMPORTE BRUTO:");
$pdf->SetFont('Arial','',14);
$total_bruto=number_format($total_bruto,2,'.','');
$pdf->Text(178,185,"$ $total_bruto");
$pdf->SetFont('Arial','B',12);
$pdf->Text(100,210,"DEDUCCIONES:");
$total_diff=number_format($total_diff,2,'.','');
$pdf->Text(178,210,"$ $total_diff");
$pdf->Text(100,225,"NETO A PAGAR:");
$pdf->Text(50,234,"EPCSS (Equipo Provincial de Compras de Servicios de Salud)");
$pdf->SetFont('Arial','',14);
$total_pagar=$total_bruto+$total_diff;
$total_pagar=number_format($total_pagar,2,'.','');
$pdf->Text(178,225,"$ $total_pagar");

$pdf->SetFont('Arial','B',8);
$pdf->Text(11,238,"Confecciono");
$pdf->Text(40,238,"Reviso");
$pdf->Text(72,238,"Responsable Admin.");
$pdf->Text(150,238,"Coordinador del EPCSS");

$pdf->SetFont('Arial','B',10);
$pdf->Text(45,274,"UA (Unidad de Administracion) del FAS (Fondo de Aseguramiento Solidario)");

$pdf->SetFont('Arial','B',12);
$pdf->Text(15,287,"Cheque Nº:");
$pdf->Text(115,287,"Banco:");

$pdf->SetFont('Arial','B',10);
$pdf->Text(90,301,"Confecciono");
$pdf->Text(123,301,"Reviso");
$pdf->Text(158,301,"Gerencia Financiera");
$pdf->Text(15,315,"FECHA DE PAGO");

$pdf->Output();
?>