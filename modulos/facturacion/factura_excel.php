<?php

require_once ("../../config.php");

$id_factura=$parametros["id_factura"];

$query1="SELECT 
  facturacion.factura.id_factura,
  nacer.smiafiliados.afiapellido,
  nacer.smiafiliados.afinombre,
  nacer.smiafiliados.afidni,
  nomenclador.prestaciones_n_op.tema,
  nomenclador.prestaciones_n_op.patologia,
  nomenclador.prestaciones_n_op.precio,
  nomenclador.prestaciones_n_op.codigo,
  nomenclador.prestaciones_n_op.fecha_comprobante,
  nomenclador.prestaciones_n_op.id_comprobante,
  facturacion.smiefectores.nombreefector,
  facturacion.smiefectores.cuie,
  facturacion.factura.periodo,
  facturacion.factura.periodo_actual,
  facturacion.factura.observaciones,
  facturacion.factura.fecha_carga,
  facturacion.factura.fecha_factura
FROM
  facturacion.factura
  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
  INNER JOIN nomenclador.prestaciones_n_op using (id_comprobante)
  INNER JOIN nacer.smiafiliados ON (facturacion.comprobante.id_smiafiliados = nacer.smiafiliados.id_smiafiliados)
  INNER JOIN facturacion.smiefectores ON (facturacion.comprobante.cuie = facturacion.smiefectores.cuie)
  where factura.id_factura=$id_factura";
$f_res1=$db->Execute($query1) or die($db->ErrorMsg());

$query2="select categoria from nomenclador.grupo_prestacion where codigo='".$f_res1->fields['tema']."';";
$tema=sql($query2) or fin_pagina();
$query3="select descripcion from nomenclador.patologias where codigo='".$f_res1->fields['patologia']."';";
$patologia=sql($query3) or fin_pagina();


$query="SELECT 
  facturacion.factura.id_factura,
  nacer.smiafiliados.afiapellido,
  nacer.smiafiliados.afinombre,
  nacer.smiafiliados.afidni,
  facturacion.prestacion.cantidad,
  facturacion.prestacion.precio_prestacion,
  facturacion.nomenclador.descripcion,
  facturacion.nomenclador.codigo,
  facturacion.smiefectores.nombreefector,
  facturacion.smiefectores.cuie,
  facturacion.factura.periodo,
  facturacion.factura.observaciones,
  facturacion.factura.fecha_carga,
  facturacion.factura.fecha_factura,
  facturacion.comprobante.id_comprobante,
  facturacion.comprobante.fecha_comprobante,
  patologias.codigo as cod_diag,
  patologias.descripcion as desc_diag
FROM
  facturacion.factura
  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
  INNER JOIN facturacion.nomenclador ON (facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador)
  INNER JOIN nacer.smiafiliados ON (facturacion.comprobante.id_smiafiliados = nacer.smiafiliados.id_smiafiliados)
  INNER JOIN facturacion.smiefectores ON (facturacion.comprobante.cuie = facturacion.smiefectores.cuie)
  LEFT JOIN nomenclador.patologias ON (prestacion.diagnostico=patologias.codigo)											
  where factura.id_factura=$id_factura
  order by codigo";

$result=$db->Execute($query) or die($db->ErrorMsg());

excel_header("factura.xls");

?>
<form name=form1 method=post action="factura_excel.php">
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
  	<td align=right >DNI</td>
    <td align=right >Apellido</td>      	
    <td align=right >Nombre</td>    
    <td align=right >Nro Comprobante</td>
    <td align=right >Fecha Prestación</td>
    <td align=right >Codigo</td>
    <td align=right >Descripcion</td>
    <td align=right >Cantidad</td>
    <td align=right >Precio Prestacion</td>       
    <td align=right >CUIE</td>
    <td align=right >Nombre Efector</td>    
    <td align=right >Periodo</td>   
    <td align=right >Fecha Factura</td>
  </tr>
  <?   
  while (!$f_res1->EOF) {?>  
    <tr>     
     <td><?=$f_res1->fields['afidni']?></td>  
     <td><?=$f_res1->fields['afiapellido']?></td>
     <td><?=$f_res1->fields['afinombre']?></td>        
     <td><?=$f_res1->fields['id_comprobante']?></td> 
     <td><?=Fecha($f_res1->fields['fecha_comprobante'])?></td>
     <td><?=$f_res1->fields['codigo']?></td> 
     <td><?=$tema->fields['categoria']." ".$patologia->fields['descripcion']?></td>           
     <td>1</td>     
     <td><?=number_format($f_res1->fields['precio'],2,',','.')?></td>            
     <td><?=$f_res1->fields['cuie']?></td>
     <td><?=$f_res1->fields['nombreefector']?></td>           
     <td><?=$f_res1->fields['periodo']?></td>           
     <td><?=Fecha($f_res1->fields['fecha_factura'])?></td>      
    </tr>
	<?$f_res1->MoveNext();
    }?>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td><?=$result->fields['afidni']?></td>  
     <td><?=$result->fields['afiapellido']?></td>
     <td><?=$result->fields['afinombre']?></td>        
     <td><?=$result->fields['id_comprobante']?></td> 
     <td><?=Fecha($result->fields['fecha_comprobante'])?></td>
     <td><?=$result->fields['codigo']?></td> 
     <td><?=$result->fields['descripcion']. ' | Diagnostico: '.$result->fields["cod_diag"].'-'.$result->fields["desc_diag"]?></td>           
     <td><?=$result->fields['cantidad']?></td>     
     <td><?=number_format($result->fields['precio_prestacion'],2,',','.')?></td>            
     <td><?=$result->fields['cuie']?></td>
     <td><?=$result->fields['nombreefector']?></td>           
     <td><?=$result->fields['periodo']?></td>           
     <td><?=Fecha($result->fields['fecha_factura'])?></td>      
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>
