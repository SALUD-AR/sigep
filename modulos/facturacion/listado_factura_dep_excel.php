<?php
/*
$Author: gaby $
$Revision: 1.0 $
$Date: 2010/10/20 15:22:40 $
*/
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

$sql="SELECT			Sum(facturacion.prestacion.cantidad) AS cantidad,
			facturacion.nomenclador.codigo,
			facturacion.nomenclador.descripcion,
			facturacion.nomenclador.catas,
			facturacion.nomenclador.priori,	
			facturacion.prestacion.precio_prestacion,
			facturacion.prestacion.precio_prestacion*sum(facturacion.prestacion.cantidad) AS precio_total
			FROM
			
				(SELECT * from facturacion.factura
				WHERE id_factura in 
					(SELECT DISTINCT
						factura.id_factura
					FROM facturacion.factura 
					INNER JOIN expediente.expediente ON facturacion.factura.id_factura = expediente.expediente.id_factura
					INNER JOIN expediente.transaccion ON expediente.transaccion.id_expediente = expediente.expediente.id_expediente
					WHERE
						(expediente.transaccion.id_area = 1 AND expediente.transaccion.estado = 'D') AND
						(expediente.transaccion.num_tranf is null) AND
						(expediente.transaccion.fecha_mov BETWEEN '$fecha_desde' AND '$fecha_hasta') 
					))as cons1
			INNER JOIN facturacion.comprobante USING (id_factura)
			INNER JOIN facturacion.prestacion USING (id_comprobante)
			INNER JOIN facturacion.nomenclador USING (id_nomenclador)
			GROUP BY
			facturacion.nomenclador.id_nomenclador,
			facturacion.nomenclador.codigo,
			facturacion.nomenclador.descripcion,
			facturacion.nomenclador.catas,
			facturacion.nomenclador.priori,	
			facturacion.prestacion.precio_prestacion
			ORDER BY codigo DESC";

$sql_fact="SELECT 			
			factura.* 
			FROM facturacion.factura
				WHERE id_factura in 
					(SELECT DISTINCT
						factura.id_factura
					FROM facturacion.factura 
					INNER JOIN expediente.expediente ON facturacion.factura.id_factura = expediente.expediente.id_factura
					INNER JOIN expediente.transaccion ON expediente.transaccion.id_expediente = expediente.expediente.id_expediente
					WHERE
						(expediente.transaccion.id_area = 1 AND expediente.transaccion.estado = 'D') AND
						(expediente.transaccion.num_tranf is null) AND
						(expediente.transaccion.fecha_mov BETWEEN '$fecha_desde' AND '$fecha_hasta') 
					)";

$result=sql($sql) or fin_pagina();

excel_header("declaracion jurada.xls");

?>
<form name=form1 method=post action="listado_factura_dep_excel.php">
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right >Codigo</td>      	
    <td align=right >Descripcion</td>
	<td align=right >Catastrofica</td>
    <td align=right >Priorizada</td>
    <td align=right >Cantidad</td>
    <td align=right id=mo>P Unitario</td>
    <td align=right id=mo>P Total</td>    
  </tr>
  <?   
  while (!$result->EOF) {
	if ($result->fields['catas']=='1'){
			$color_catas='#FA58AC'; 
			$title_catas='Prestacion Catastrofica';
	   }else{
			$color_catas=''; 
			$title_catas='Prestacion Normal';
	}
	if ($result->fields['priori']=='1'){
			$color_priori='#00FF00'; 
			$title_priori='Prestacion Priorizada';
	   }else{
			$color_priori=''; 
			$title_priori='Prestacion Normal';
	}?>  
    <tr>     
     <td bgcolor="<?=$color_catas?>" title="<?=$title_catas?>"><?=$result->fields['codigo']?></td>
     <td bgcolor="<?=$color_priori?>" title="<?=$title_priori?>"><?=$result->fields['descripcion']?></td>	 
     <td><?=$result->fields['catas']?></td>
     <td><?=$result->fields['priori']?></td>
     <td><?=$result->fields['cantidad']?></td>
     <td><?=number_format($result->fields['precio_prestacion'],2,',','.')?></td>     
     <td><?=number_format($result->fields['precio_total'],2,',','.')?></td>             
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 <br>
 <?$result = sql($sql_fact) or die;?>
 <table border=0 width=70% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=80%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$result->recordCount()?> Filtro: <?=$_POST['periodo'];?></td>       
      </tr>
    </table>
   </td>
  </tr>
    <td align=right id=mo>Num Factura</td>
    <td align=right id=mo>CUIE</td>
    <td align=right id=mo>Fecha Carga</td>
    <td align=right id=mo>Fecha Factura</td>
  </tr>
 <? $t=0;
 	$s=0;
   while (!$result->EOF) {?>  	
  
    <tr <?=atrib_tr()?> >
	 <?$id_factura=$result->fields['id_factura'];
	 $ref = encode_link("./factura_admin.php",array("id_factura"=>$id_factura));?> 	  
     <td><a href="<?=$ref?>" target="_blank"><?=$result->fields['id_factura']?></a></td>     
     <td><?=$result->fields['cuie']?></td>     
     <td><?=fecha($result->fields['fecha_carga'])?></td>     
     <td><?=fecha($result->fields['fecha_factura'])?></td>       
    </tr>
	<?$result->MoveNext();
   }?>   
</table>
 </form>
