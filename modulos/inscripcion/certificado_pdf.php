<?php

define('FPDF_FONTPATH','font/');

require_once("../../config.php");
include_once("certif_clasepdf.php"); 

//generacion de pdf
$pdf=new orden_compra();
if ($parametros['id_beneficiarios']) $id_beneficiarios=$parametros['id_beneficiarios'];

$db->StartTrans();         
    
   $q="select nextval('uad.transaccion_certificado_id_transaccion_certificado_seq') as id_planilla";
    $id_planilla=sql($q) or fin_pagina();
    $id_transaccion_certificado=$id_planilla->fields['id_planilla'];
   
    $usuario=$_ses_user['name'];
    $fecha_trans=date("Y-m-d H:i:s");
    $ipmaquina=$_SERVER['REMOTE_ADDR'];  
    $query="insert into uad.transaccion_certificado
             (id_transaccion_certificado,id_beneficiarios,usuario,fecha,ipmaquina)
             values
             ('$id_transaccion_certificado','$id_beneficiarios','$usuario','$fecha_trans','$ipmaquina')";

    sql($query, "Error al insertar transaccion") or fin_pagina();    
	 
    $db->CompleteTrans();
    
    

$query="SELECT   
  uad.beneficiarios.clave_beneficiario,
  uad.beneficiarios.apellido_benef,
  uad.beneficiarios.nombre_benef,
  uad.beneficiarios.numero_doc,
  uad.beneficiarios.fecha_nacimiento_benef,
  uad.beneficiarios.activo,
  uad.beneficiarios.fecha_inscripcion,
  nacer.efe_conv.nombre
FROM
  uad.beneficiarios
  INNER JOIN nacer.efe_conv ON (uad.beneficiarios.cuie_ea = nacer.efe_conv.cuie)
  where id_beneficiarios='$id_beneficiarios'";

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
$pdf->clavebeneficiario($f_res->fields['clave_beneficiario']);
$pdf->afiapellido($f_res->fields['apellido_benef']);
$pdf->afinombre($f_res->fields['nombre_benef']);
$pdf->afidni($f_res->fields['numero_doc']);
$pdf->nombre($f_res->fields['nombre']);
$pdf->afifechanac(Fecha($f_res->fields['fecha_nacimiento_benef']));
$pdf->fechainscripcion(Fecha($f_res->fields['fecha_inscripcion']));
$pdf->activo($id_transaccion_certificado);
$pdf->guardar_servidor("Certificado_$id_smiafiliados.pdf");
?>