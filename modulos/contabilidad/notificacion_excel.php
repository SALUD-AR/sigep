<?php

require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);


$color1="#5090C0";
  $color2="#D5D5D5";
  $ret = "";
    
$sql= "SELECT 
	ingreso.id_ingreso,
  facturacion.factura.id_factura,
  facturacion.factura.cuie,
  facturacion.factura.fecha_carga,
  facturacion.factura.fecha_factura,
  facturacion.factura.periodo,
  facturacion.factura.estado,
  facturacion.factura.observaciones,
  facturacion.factura.id_factura,
  facturacion.factura.online,
  facturacion.factura.nro_exp_ext,
  facturacion.factura.fecha_exp_ext,
  facturacion.factura.periodo_contable,
  facturacion.factura.monto_prefactura,
  efe_conv.nombre
FROM
  facturacion.factura  
left join nacer.efe_conv using (cuie)
  left join contabilidad.ingreso on factura.id_factura=ingreso.numero_factura 
  
WHERE  (factura.estado='C') and (factura.cuie='$cuie') and (facturacion.factura.nro_exp_ext is not null) and (ingreso.id_ingreso is not null) 
ORDER BY ingreso.id_ingreso DESC";
  
$res_factura=sql($sql,"no se puede ejecutar");
$res_factura->movefirst();

$nombre_efector=$res_factura->fields['nombre'];
$nro_factura=$res_factura->fields['id_factura'];
$exp_externo=$res_factura->fields['nro_exp_ext'];
$monto_prefactura=number_format($res_factura->fields['monto_prefactura'],2,',','.');
$periodo_contable=$res_factura->fields['periodo_contable'];
$id_factura=$res_factura->fields['id_factura'];

$sql= "SELECT * FROM nacer.efe_conv where cuie = '$cuie'";  
$res_efector=sql($sql,"no se puede ejecutar");
$referente=$res_efector->fields['referente'];

$query_t="SELECT sum 
			(facturacion.prestacion.cantidad*facturacion.prestacion.precio_prestacion) as total
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
			  INNER JOIN facturacion.nomenclador ON (facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador)
			  INNER JOIN nacer.smiafiliados ON (facturacion.comprobante.id_smiafiliados = nacer.smiafiliados.id_smiafiliados)
			  INNER JOIN facturacion.smiefectores ON (facturacion.comprobante.cuie = facturacion.smiefectores.cuie)
			  where factura.id_factura=$id_factura";
$total=sql($query_t,"NO puedo calcular el total");
$monto_factura=$total->fields['total'];
$monto_factura=number_format($monto_factura,2,',','.');

			$query=" SELECT sum(cantidad*monto) as total FROM
  			facturacion.debito  			
  			where id_factura='$id_factura'";
			$result_t_debitado=$db->Execute($query) or die($db->ErrorMsg());
			$debito=number_format($result_t_debitado->fields['total'],2,',','.');

			$query=" SELECT sum(cantidad*monto) as total FROM
  			facturacion.credito  			  
  			where id_factura='$id_factura'";
			$result_t_acreditado=$db->Execute($query) or die($db->ErrorMsg());
			$credito=number_format($result_t_acreditado->fields['total'],2,',','.');


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
$ret .= "<b>Por medio de la presente le notifico que se encuentra a disposición del prestador que usted representa
la suma de $ $monto_factura. Monto neto a liquidar que surge luego de haberse detectado en auditoría conceptos
erróneos en la facturación presentada. Por Consiguiente se debito la suma de $ $debito  y se acredito $ $credito.
transferida por el EPCSS en relacion a la cuasi-factura del mes de $periodo_contable, de $ $monto_prefactura. </b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<b>Asimismo informo a Usted que dicha transferencia se realizo a través del Expediente N°: $exp_externo</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "</table><br>\n";

$sql="select ingre-egre as total, ingre,egre,deve,egre_comp from
		(select sum (monto_deposito)as ingre from contabilidad.ingreso
		where cuie='$cuie') as ingreso,
		(select sum (monto_egreso)as egre from contabilidad.egreso
		where cuie='$cuie') as egreso,
		(select sum (monto_factura)as deve from contabilidad.ingreso
		where cuie='$cuie') as devengado,
		(select sum (monto_egre_comp)as egre_comp from contabilidad.egreso
		where cuie='$cuie') as egre_comp";
$res_saldo=sql($sql,"no puede calcular el saldo");
$total_depositado=number_format($res_saldo->fields['ingre'],2,',','.');
$total_egre_comp=number_format($res_saldo->fields['egre_comp'],2,',','.');

$sql_1="select sum (monto_egre_comp)as egre_incentivo
		from contabilidad.egreso
		where cuie='$cuie' and id_inciso=1";
$res_incentivo=sql($sql_1,"no puede calcular el saldo");
$total_incentivo=number_format($res_incentivo->fields['egre_incentivo'],2,',','.');

$ret .= "<table width=95% align=center style='font-size=10px'>\n";
$ret .= "<tr>\n";
$ret .= "<td align=center>\n";
$ret .= "<b>INFORMACION ANEXA\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "</table>\n";
$ret .= "<table width='65%'  bgcolor='$color1' align='center' style='border: 2px solid #000000; font-size=14px;'>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<b>Por medio de la presente le informo que: </b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<b>Su Saldo Acumulado es de: $ $total_depositado. </b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<b>Su Saldo Comprometido es de: $ $total_egre_comp. De los cuales $ $total_incentivo corresponde a incentivo.</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<b>Su Saldo Real es de: $ $saldo_real. </b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<b>Queda a su disposicion retirar en nuestras oficinas el informe de DEBITO / CREDITO en el horario de 8hs. a 18hs.</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<font color=white><b>Se ruega contestar este mail al mail Oficial del Plan Nacer, para ser tenido en cuenta como acuse de recibo.</b></font>\n";
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
while (!$res_mail->EOF) { 
	$para=$res_mail->fields['mail'];
	$ret .= "<tr bgcolor='$color1' align='left'>\n";
	$ret .= "<td align='left'>\n";
	$ret .= "<b>Mail: $para.</b>\n";
	$ret .= "</td>\n";
	$ret .= "</tr>\n";
	$res_mail->movenext();
}
$ret .= "</table>\n";

excel_header("ingreso_egreso.xls");

?>
<form name=form1 method=post action="notificacion_excel.php">
<?echo $ret;?>
 
 </form>