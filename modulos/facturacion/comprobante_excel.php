<?php

require_once ("../../config.php");

$fecha_desde=$parametros["fecha_desde"];
$fecha_hasta=$parametros["fecha_hasta"];

$query="SELECT 
  facturacion.comprobante.id_factura,
  facturacion.factura.periodo,
  facturacion.comprobante.id_comprobante,
  facturacion.comprobante.fecha_comprobante,
  facturacion.comprobante.cuie,
  nacer.efe_conv.nombre,
  nacer.smiafiliados.afiapellido,
  nacer.smiafiliados.afinombre,
  nacer.smiafiliados.afidni,
  facturacion.nomenclador.descripcion,
  facturacion.prestacion.precio_prestacion,
  facturacion.prestacion.cantidad,
  facturacion.prestacion.diagnostico,
  facturacion.anexo.prueba,
  CASE WHEN facturacion.nomenclador_detalle.modo_facturacion='1' THEN facturacion.nomenclador.codigo
  WHEN facturacion.nomenclador_detalle.modo_facturacion<>'1' THEN facturacion.nomenclador.grupo||facturacion.nomenclador.codigo||facturacion.prestacion.diagnostico
  END as codigo_prestacion
FROM
	facturacion.prestacion
	left JOIN facturacion.comprobante ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
	left JOIN nacer.efe_conv ON (facturacion.comprobante.cuie = nacer.efe_conv.cuie)				  
	left JOIN facturacion.nomenclador ON (facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador)
	left JOIN facturacion.nomenclador_detalle ON (facturacion.nomenclador.id_nomenclador_detalle=facturacion.nomenclador_detalle.id_nomenclador_detalle)
	left JOIN nacer.smiafiliados ON (facturacion.comprobante.id_smiafiliados = nacer.smiafiliados.id_smiafiliados)
	left JOIN facturacion.factura ON (comprobante.id_factura = factura.id_factura)
	left join facturacion.anexo on (prestacion.id_anexo = anexo.id_anexo)  
where (comprobante.fecha_comprobante between '$fecha_desde' and '$fecha_hasta') and comprobante.id_factura is not null
  order by id_factura DESC";

$result=$db->Execute($query) or die($db->ErrorMsg());

excel_header("muestra.xls");

?>
<form name=form1 method=post action="comprobante_excel.php">
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
  	<td align=right >Num Factura</td>
    <td align=right >Periodo</td>      	
    <td align=right >Num Comprobante</td>    
    <td align=right >Fecha Prestación</td>
    <td align=right >CUIE</td>
    <td align=right >Efector</td>
    <td align=right >Apellido</td>
    <td align=right >Nombre</td>
    <td align=right >DNI</td>       
    <td align=right >Codigo</td>
    <td align=right >Descripcion</td>    
    <td align=right >Precio</td>   
    <td align=right >Cantidad</td>
    <td align=right >Prueba</td>
   </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td><?=$result->fields['id_factura']?></td>  
     <td><?=$result->fields['periodo']?></td>
     <td><?=$result->fields['id_comprobante']?></td> 
     <td><?=Fecha($result->fields['fecha_comprobante'])?></td>
     <td><?=$result->fields['cuie']?></td> 
     <td><?=$result->fields['nombre']?></td>           
     <td><?=$result->fields['afiapellido']?></td>           
     <td><?=$result->fields['afinombre']?></td>           
     <td><?=$result->fields['afidni']?></td>           
     <td><?=$result->fields['codigo_prestacion']?></td>     
     <td><?=$result->fields['descripcion']?></td>     
     <td><?=number_format($result->fields['precio_prestacion'],2,',','.')?></td>            
     <td><?=$result->fields['cantidad']?></td>
        
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>
