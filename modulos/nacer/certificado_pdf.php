<?php

define('FPDF_FONTPATH','font/');

require_once("../../config.php");
include_once("certif_clasepdf.php"); 

//generacion de pdf
$pdf=new orden_compra();
if ($parametros['id_smiafiliados']) $id_smiafiliados=$parametros['id_smiafiliados'];

$db->StartTrans();         
    
   $q="select nextval('nacer.transaccion_certificado_id_transaccion_certificado_seq') as id_planilla";
    $id_planilla=sql($q) or fin_pagina();
    $id_transaccion_certificado=$id_planilla->fields['id_planilla'];
   
    $usuario=$_ses_user['name'];
    $fecha_trans=date("Y-m-d H:i:s");
    $ipmaquina=$_SERVER['REMOTE_ADDR'];  
    $query="insert into nacer.transaccion_certificado
             (id_transaccion_certificado,id_smiafiliados,usuario,fecha,ipmaquina)
             values
             ('$id_transaccion_certificado','$id_smiafiliados','$usuario','$fecha_trans','$ipmaquina')";

    sql($query, "Error al insertar transaccion") or fin_pagina();    
	 
    $db->CompleteTrans();
    
    

$query="SELECT   
  nacer.smiafiliados.clavebeneficiario,
  nacer.smiafiliados.afiapellido,
  nacer.smiafiliados.afinombre,
  nacer.smiafiliados.afidni,
  nacer.smiafiliados.afifechanac,
  nacer.smiafiliados.activo,
  nacer.smiafiliados.fechainscripcion,
  nacer.efe_conv.nombre
FROM
  nacer.smiafiliados
  INNER JOIN nacer.efe_conv ON (nacer.smiafiliados.cuieefectorasignado = nacer.efe_conv.cuie)
  where id_smiafiliados='$id_smiafiliados'";

$f_res=$db->Execute($query) or die($db->ErrorMsg());

function suma_fechas($fecha,$ndias){
      if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha))
      	list($dia,$mes,$año)=split("/", $fecha);
      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))
        list($dia,$mes,$año)=split("-",$fecha);
      $nueva = mktime(0,0,0, $mes,$dia,$año) + $ndias * 24 * 60 * 60;
      $nuevafecha=date("d-m-Y",$nueva);
      return ($nuevafecha);  
}

$pdf->dibujar_planilla(suma_fechas(date("d/m/y"),60));
$pdf->clavebeneficiario($f_res->fields['clavebeneficiario']);
$pdf->afiapellido($f_res->fields['afiapellido']);
$pdf->afinombre($f_res->fields['afinombre']);
$pdf->afidni($f_res->fields['afidni']);
$pdf->nombre($f_res->fields['nombre']);
$pdf->afifechanac(Fecha($f_res->fields['afifechanac']));
$pdf->fechainscripcion(Fecha($f_res->fields['fechainscripcion']));
$pdf->activo($id_transaccion_certificado);
$pdf->guardar_servidor("Certificado_$id_smiafiliados.pdf");
?>