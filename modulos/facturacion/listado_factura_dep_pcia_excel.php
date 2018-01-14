<?php
/*
Author: sebastian lohaiza

modificada por
$Author: seba $
$Revision: 1.30 $
$Date: 2012/03/01 18:25:40 $
*/
require_once("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

excel_header("listado.xls");?>

<form name='form1' action='listado_factura_dep_pcia_excel.php' method='POST'>
<input type="hidden" value="<?=$fecha_desde?>" name="fecha_desde">
<input type="hidden" value="<?=$cuie?>" name="cuie">
<input type="hidden" value="<?=$fecha_hasta?>" name="fecha_hasta">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?
	$link=encode_link("listado_factura_dep_pcia_excel.php",array("cuie"=>$cuie,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">

	  </td>
       
     </tr>
     
</table>
<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<font size=+1><b>Efector: <?echo $cuie.". Desde: ".$fecha_desde." Hasta: ".$fecha_hasta?> </b></font>        
    </td>
 </tr>
 <tr><td>
      
<?
$fecha_desde_q=fecha_db($fecha_desde);
$fecha_hasta_q=fecha_db($fecha_hasta);

$sql_tmp=" SELECT
			Sum(facturacion.prestacion.cantidad) AS cantidad,
			facturacion.nomenclador.codigo,
			facturacion.nomenclador.descripcion,
			facturacion.prestacion.precio_prestacion,
			facturacion.prestacion.precio_prestacion*sum(facturacion.prestacion.cantidad) AS precio_total
			FROM
			facturacion.nomenclador
			INNER JOIN facturacion.prestacion ON (facturacion.nomenclador.id_nomenclador = facturacion.prestacion.id_nomenclador)
			INNER JOIN facturacion.comprobante ON (facturacion.prestacion.id_comprobante = facturacion.comprobante.id_comprobante)
			INNER JOIN facturacion.factura ON (facturacion.comprobante.id_factura = facturacion.factura.id_factura)
			INNER JOIN expediente.expediente ON facturacion.factura.id_factura = expediente.expediente.id_factura
			INNER JOIN expediente.transaccion ON expediente.transaccion.id_expediente = expediente.expediente.id_expediente
			WHERE
			(expediente.transaccion.id_area = 1 AND expediente.transaccion.estado = 'D') AND
			(expediente.transaccion.fecha_mov BETWEEN '$fecha_desde_q' AND '$fecha_hasta_q') AND
			(factura.cuie='$cuie')
			GROUP BY
			facturacion.nomenclador.id_nomenclador,
			facturacion.nomenclador.codigo,
			facturacion.nomenclador.descripcion,
			facturacion.prestacion.precio_prestacion
			ORDER BY codigo DESC";
$result = sql($sql_tmp) or die;
?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$result->recordCount()?> Filtro: <?=$_POST['periodo'];?></td>       
      </tr>
    </table>
   </td>
  </tr>
  
<td align=right id=mo>Codigo</td>
    <td align=right id=mo>Descripcion</td>
    <td align=right id=mo>Cantidad</td>
    <td align=right id=mo>P Unitario</td>
    <td align=right id=mo>P Total</td>
  </tr>
 <? $t=0;
 	$s=0;
   while (!$result->EOF) {?>  	
  
    <tr <?=atrib_tr()?> >     
     <td><?=$result->fields['codigo']?></td>     
     <td><?=$result->fields['descripcion']?></td>     
     <td><?=$result->fields['cantidad']?></td>     
     <td><?=number_format($result->fields['precio_prestacion'],2,',','.')?></td>     
     <td><?=number_format($result->fields['precio_total'],2,',','.')?></td>   
     <?$t=$t+$result->fields['precio_total'];
     	$s=$s+$result->fields['cantidad']
     ?>  
    </tr>
	<?$result->MoveNext();
   }?>
    <tr>    	  
     	<td>Monto Total</td> 
     	<td><b><?=number_format($t,2,',','.')?></b></td> 
    </tr>
    <tr>    	
     	<td>Total Prestaciones</td> 
     	<td><b><?=number_format($s,0,',','.')?></b></td> 
    </tr> 
    
</table>

 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>
