<?php

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

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

$query="select id_factura,cuie,nombre,fecha_factura,periodo_actual,monto_prefactura,fecha from (
select id_factura from (select facturacion.factura.cuie,
  facturacion.factura.fecha_carga,
  facturacion.factura.fecha_factura,
  facturacion.factura.periodo,
  facturacion.factura.periodo_actual,
  facturacion.factura.estado,
  facturacion.factura.observaciones,
  facturacion.factura.id_factura,
  facturacion.factura.online,
  facturacion.factura.nro_exp_ext,
  facturacion.factura.fecha_exp_ext,
  facturacion.factura.periodo_contable,
  facturacion.factura.monto_prefactura,
  nacer.efe_conv.nombre from facturacion.factura left join nacer.efe_conv using (cuie) where facturacion.factura.estado = 'C' and
facturacion.factura.estado_exp = 0 and periodo_actual like '%$cmd%' except 
select facturacion.factura.cuie,
  facturacion.factura.fecha_carga,
  facturacion.factura.fecha_factura,
  facturacion.factura.periodo,
  facturacion.factura.periodo_actual,
  facturacion.factura.estado,
  facturacion.factura.observaciones,
  facturacion.factura.id_factura,
  facturacion.factura.online,
  facturacion.factura.nro_exp_ext,
  facturacion.factura.fecha_exp_ext,
  facturacion.factura.periodo_contable,
  facturacion.factura.monto_prefactura,
  nacer.efe_conv.nombre from expediente.expediente left join nacer.efe_conv using (id_efe_conv) left join facturacion.factura using (id_factura)
  ) as facturas_cerradas except (select numero_factura as id_factura from contabilidad.ingreso where numero_factura<>0) )as facturas_pagadas left join facturacion.factura using (id_factura)
  left join nacer.efe_conv using (cuie) left join (select * from facturacion.log_factura order by fecha DESC ) as tabla_log using (id_factura) where descripcion like '%Cierra la Factura%'
order by periodo_actual ASC";

$result=$db->Execute($query) or die($db->ErrorMsg());

excel_header("informe_facturacion_nuevo_excel.xls");

?>
<form name=form1 method=post action="informe_facturacion_nuevo_excel.php">
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
  	<td align=right id=mo>Nro Factura</a></td>      	
    <td align=right id=mo>CUIE</a></td>      	
    <td align=right id=mo>Efector</a></td>
    <td align=right id=mo>Periodo de Realizacion de la Prestacion</a></td>  
    <td align=right id=mo>Cierre de Factura (log)</a></td>  
	<td align=right id=mo>Monto Factura</td>
	<td align=right id=mo>Cantidad de Notificacion</td>
	<td align=right id=mo>Dias de Mora segun periodo prest.</td>
	<td align=right id=mo>Dias entre cierre y prestaciones</td>
    </tr>
  <?
   $fecha_hoy=date("Y-m-d");
   $fecha_hoy2=fecha($fecha_hoy);
   
   while (!$result->EOF) {
   	$dias_mora=restaFechas (Fecha($result->fields['fecha']),$fecha_hoy2);
   	
   	$dias_mora_period=restaFechas (Fecha(string_fecha($result->fields['periodo_actual'])),$fecha_hoy2);
	$diff_dias=restaFechas(Fecha(string_fecha($result->fields['periodo_actual'])),Fecha($result->fields['fecha']));
   	
   	$plazo=string_fecha($result->fields['periodo_actual']);
    $plazo_30=date("Y-m-d", strtotime ("$plazo +30 days"));
	$plazo_150=date("Y-m-d", strtotime ("$plazo +150 days"));
    //$plazo_240=date("Y-m-d", strtotime ("$plazo +240 days"));
    if ($fecha_hoy<=$plazo_30) $tr=atrib_tr();
	if ($fecha_hoy>$plazo_30 && $fecha_hoy<=$plazo_150) $tr=atrib_tr1();
	if ($fecha_hoy>$plazo_150) $tr=atrib_tr2();
	
   if ($dias_mora_period>=210){
			$color='#FF0000'; 
			$title='FACTURA CON MAS DE 210 PARA PAGO';
		}
		else {
			$color=''; 
			$title='';
		}
	?>	
    <tr <?=$tr?>>   
      <?$id_factura=$result->fields['id_factura'];
	 
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
		$total=$total->fields['total']+$total1->fields['total1'];?>
	 
	 <td bgcolor="<?=$color?>" title="<?=$title?>"><?=$result->fields['id_factura']?></td>
	 <td title="<?=$title?>"><?=$result->fields['cuie']?></td>
     <td ><?=$result->fields['nombre']?></td>
     <td align="center"><?=$result->fields['periodo_actual']?></td>	
     <td align="center"><?=fecha($result->fields['fecha'])?></td> 
     <td align="center"><?=number_format($total,2,',','.');?></td>
	 <?php $consulta="select id_factura, count (id_factura) as cantidad from facturacion.notificacion where id_factura='$id_factura' group by (id_factura)";
	 						$res_consulta=sql ($consulta,"Error al Ejecutar la Consulta")  or fin_pagina();
	 						$cantidad=$res_consulta->fields['cantidad'];
	 						if (!($cantidad)) $cantidad=0;?>
	 <td align="center"><?=$cantidad;?></td>
	  <td align="center" bgcolor="<?=$color?>" title="<?=$title?>"><?=$dias_mora_period;?></td>
	  <td align="center"><?=$diff_dias;?></td>
             
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>
