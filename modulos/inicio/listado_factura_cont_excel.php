<?php

require_once ("../../config.php");

function ultimoDia($mes,$ano){ 
    if ($mes=='02')$ultimo_dia=28;
	else if ($mes=='04' or $mes=='06'or $mes=='09' or $mes=='11') $ultimo_dia=30;
	else $ultimo_dia=31;
    while (checkdate($mes,$ultimo_dia + 1,$ano)){ 
       $ultimo_dia++; 
    } 
    return $ultimo_dia; 
}

$periodo_actual=$parametros['periodo'];
$anio=substr($periodo_actual,0,4);
$mes=substr($periodo_actual,5,2);	
$fecha_desde=ereg_replace('/','-',$periodo_actual).'-01';	
$fecha_hasta=ereg_replace('/','-',$periodo_actual).'-'.ultimoDia($mes,$anio);

$sql="SELECT
		facturacion.smiefectores.nombreefector,
		facturacion.factura.cuie,
		facturacion.factura.id_factura,
		facturacion.factura.fecha_factura,
		facturacion.factura.monto_prefactura,
		facturacion.factura.periodo,
		contabilidad.ingreso.comentario,
		facturacion.factura.nro_exp_ext,
		facturacion.factura.fecha_exp_ext,
		facturacion.factura.periodo_contable,
		contabilidad.ingreso.fecha_deposito,
		expediente.expediente.nro_exp,
		expediente.expediente.fecha_ing,
		contabilidad.ingreso.fecha_notificacion
		FROM
		facturacion.factura
		LEFT JOIN facturacion.smiefectores ON (facturacion.factura.cuie = facturacion.smiefectores.cuie)
		LEFT JOIN contabilidad.ingreso ON (facturacion.factura.id_factura = contabilidad.ingreso.numero_factura)
		LEFT JOIN expediente.expediente ON expediente.expediente.id_factura = facturacion.factura.id_factura
		WHERE
		((facturacion.factura.estado = 'C') AND
		(contabilidad.ingreso.fecha_deposito between '$fecha_desde' and '$fecha_hasta')) AND
		(expediente.control=5)
		ORDER BY
		facturacion.factura.nro_exp_ext ASC,
		facturacion.factura.id_factura DESC";


//(contabilidad.ingreso.fecha_deposito between '$fecha_desde' and '$fecha_hasta'))
	

$result=sql($sql) or fin_pagina();

excel_header("listado de facturas cerradas.xls");

?>
<form name=form1 method=post action="listado_factura_cont_excel.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total: </b><?=$result->RecordCount();?> 
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id=mo>Efector</td>
    <td align=right id=mo>CUIE</td>
    <td align=right id=mo>Num. Fact</td>
    <td align=right id=mo>Fecha Factura</td>
    <td align=right id=mo>Monto Prefactura</td>
    <td align=right id=mo>Corresp. al Mes</td>    
    <td align=right id=mo>Total</td>    
    <td align=right id=mo>Num. Exp. Externo</td>    
    <td align=right id=mo>Fecha. Exp. Externo</td>    
    <td align=right id=mo>Fecha Deposito</td> 
    <td align=right id=mo>Nº Exp Interno</td>    
    <td align=right id=mo>Fecha de Ingreso a la UGSP</td>  
    <td align=right id=mo>Fecha de Notificacion</td>     
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>
     <td ><?=$result->fields['nombreefector']?></td>  
     <td ><?=$result->fields['cuie']?></td>        
     <td ><?=$result->fields['id_factura']?></td>     
     <td ><?=fecha($result->fields['fecha_factura'])?></td>  
     <td ><?=number_format($result->fields['monto_prefactura'],2,',','.')?></td>  
     <td ><?=$result->fields['periodo_contable']?></td>   
 <?	$id_factura=$result->fields['id_factura'];
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
		$total=$total->fields['total'];?>
       <td align="center"><?=number_format($total,2,',','.');?></td>
       <td ><?=$result->fields['nro_exp_ext']?></td>  
       <td ><?=$result->fields['fecha_exp_ext']?></td>         
       <td ><?=fecha($result->fields['fecha_deposito'])?></td>  
       <td ><?=$result->fields['nro_exp']?></td>  
       <td ><?=fecha($result->fields['fecha_ing'])?></td>     
       <td ><?=fecha($result->fields['fecha_notificacion'])?></td>     
     	
     	
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>