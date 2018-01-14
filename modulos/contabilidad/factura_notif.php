<?
/*
Author: seba
Date: 2011/10/19 16:24:00 $
*/

require_once ("../../config.php");

function string_fecha($periodo){
	
	$long= strlen ($periodo);
	$anio= substr ($periodo,0,4);
	$anio_int = (int)$anio;
	
	$mes= substr ($periodo,5,$long);
	$mes_int=(int)$mes;
	$dia="01";
	$fecha="$anio_int-$mes-$dia";
	return $fecha;

}


$id_factura=$parametros["id_factura"];

$sql= "SELECT * from facturacion.log_factura left join facturacion.factura 
using (id_factura) left join nacer.efe_conv using (cuie) where id_factura='$id_factura'"; 


$res_factura=sql($sql,"no se puede ejecutar");
$res_factura->movefirst();

$query_t="SELECT sum 
			(facturacion.prestacion.cantidad*facturacion.prestacion.precio_prestacion) as total
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
			  where factura.id_factura=$id_factura";
		$total=sql($query_t,"NO puedo calcular el total");
		$query_t1="SELECT sum 
			(nomenclador.prestaciones_n_op.precio) as total1
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN nomenclador.prestaciones_n_op using (id_comprobante)
			  where factura.id_factura=$id_factura";
		$total1=sql($query_t1,"NO puedo calcular el total");
		$total=$total->fields['total']+$total1->fields['total1'];
  

$nombre_efector=$res_factura->fields['nombre'];
$nro_factura=$res_factura->fields['id_factura'];
//$monto_prefactura=number_format($res_factura->fields['monto_prefactura'],2,',','.');
$fecha_cierre=fecha($res_factura->fields['fecha']);
$id_factura=$res_factura->fields['id_factura'];
$cuie=$res_factura->fields['cuie'];
$referente=$res_factura->fields['referente'];
$fecha_hoy=date("Y-m-d");
$fecha_hoy2=Fecha($fecha_hoy);
$dias_mora=restaFechas ($fecha_cierre,$fecha_hoy2);
$dias_mora_period=restaFechas (Fecha(string_fecha($res_factura->fields['periodo_actual'])),$fecha_hoy2);
$dias_mora_period=$dias_mora_period-29;

$query_t="SELECT sum 
			(facturacion.prestacion.cantidad*facturacion.prestacion.precio_prestacion) as total
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
			  where factura.id_factura=$id_factura";
		$total=sql($query_t,"NO puedo calcular el total");
		$query_t1="SELECT sum 
			(nomenclador.prestaciones_n_op.precio) as total1
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN nomenclador.prestaciones_n_op using (id_comprobante)
			  where factura.id_factura=$id_factura";
		$total1=sql($query_t1,"NO puedo calcular el total");
		$total=$total->fields['total']+$total1->fields['total1'];
		$monto_total=number_format($total,2,',','.');

date_default_timezone_set('Europe/Madrid');
setlocale(LC_TIME, 'spanish');
$dia_hoy=strftime("%A %d de %B de %Y");
	
$ret .= "<table width='65%'  bgcolor='$color1' align='center' style='border: 2px solid #000000; font-size=14px;'>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='center'>\n";
$ret .= "<b>FORMULARIO I</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1' align='right'>\n";
$ret .= "<td align='rigth'>\n";
$ret .= "<b>Plan Nacer, $dia_hoy</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1' align='left'>\n";
$ret .= "<td align='left'>\n";
$ret .= "<b>Efector: $nombre_efector. CUIE: $cuie. Número de Factura: $id_factura</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1' align='left'>\n";
$ret .= "<td align='left'>\n";
$ret .= "<b>Referente: $referente.</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<b>Por medio de la presente le notifico que la factura $id_factura fue cerrada el dia $fecha_cierre cuyo monto es de $monto_total tiene $dias_mora_period
dias de mora desde la realizacion de las prestaciones.
Se ruega presentar la misma en la mayor brevedad posible. </b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "</table><br>\n";


$ret .= "<table width=95% align=center style='font-size=10px'>\n";
$ret .= "<tr>\n";
$ret .= "<td align=center>\n";
$ret .= "<b> NOTIFICACIONES\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "</table>\n";	
$ret .= "<table width='65%'  bgcolor='$color1' align='center' style='border: 2px solid #000000; font-size=14px;'>\n";
$ret .= "<tr bgcolor='$color1' align='left'>\n";
$ret .= "<td align='rigth'>\n";
$ret .= "<b>Queda Notificado Equipo de Plan Nacer a través del mail oficial.</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1' align='left'>\n";
$ret .= "<td align='left'>\n";
$ret .= "<b>Queda Notificado el Efector: $nombre_efector. CUIE: $cuie. A través de los mail declarados.</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$sql= "select * from nacer.mail_efe_conv where cuie='$cuie'";
$res_mail=sql($sql,"no se puede ejecutar");
$res_mail->movefirst();
$arr_mail="";
while (!$res_mail->EOF) { 
	$para=$res_mail->fields['mail'];
	$arr_mail="$para , $arr_mail";
	$ret .= "<tr bgcolor='$color1' align='left'>\n";
	$ret .= "<td align='left'>\n";
	$ret .= "<b>Mail: $para.</b>\n";
	$ret .= "</td>\n";
	$ret .= "</tr>\n";
	$res_mail->movenext();
}
$ret .= "</table>\n";

$usuario=$_ses_user['name'];
$q="select nextval('facturacion.id_notif_seq') as id_notif";
$id_notif=sql($q) or fin_pagina();
$id_notif=$id_notif->fields['id_notif'];
$via_com="Mail";
$comentario="";
$str_sql="INSERT into facturacion.notificacion (id_notif,id_factura,usuario,fecha_notif,
mail_efe,via_comunicacion,comentario) values ('$id_notif',
												   '$id_factura',
												   '$usuario',
												   '$fecha_hoy',
												   '$arr_mail',
												   '$via_com',
												   '$comentario')";
$intro_reg=sql($str_sql,"Error al insertar el Expediente") or fin_pagina();
$accion="SE NOTIFICO VIA MAIL AL EFECTOR";

echo $html_header;
echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";
echo $ret;
$res_mail->movefirst();
	while (!$res_mail->EOF) { 
	$para=$res_mail->fields['mail'];
	enviar_mail_html('plan.nacersl@gmail.com','Notificacion de Facturacion',$ret,0,0,0);
	enviar_mail_html($para,'Notificacion de Facturacion',$ret,0,0,0);
	$res_mail->movenext();
	}
	enviar_mail_html('caropellegrini@hotmail.com','Notificacion de Facturacion',$ret,0,0,0);
	//enviar_mail_html('magianello@hotmail.com','Notificacion de Facturacion',$ret,0,0,0);
	enviar_mail_html('operaciones.plan.nacer.sl@gmail.com','Notificacion de Facturacion',$ret,0,0,0);
	enviar_mail_html('celem_g_20@hotmail.com','Notificacion de Facturacion',$ret,0,0,0);
		
	/*$ref = encode_link("notificacion_excel.php",array("cuie"=>$cuie,"id_factura"=>$id_factura,"saldo_real"=>$saldo_real));?>
	<script>
	window.open('<?=$ref?>')*/
	
<?=fin_pagina();// aca termino ?>
