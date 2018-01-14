<?php
require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE); 

function ultimoDia($mes,$ano){ 
    $ultimo_dia=28; 
    $ultimo_dia=date("t", mktime(0, 0, 0, intval($mes), 1, intval($ano)));
    return $ultimo_dia; 
} 

$fecha_desde=ereg_replace('/','-',$periodo_desde."/01");
$fecha_hasta=ereg_replace('/','-',$periodo_hasta."/".ultimoDia(substr($periodo_hasta,5,2),substr($periodo_hasta,0,4)));

$query1="SELECT 
  nacer.smiafiliados.afiapellido as apellido,
  nacer.smiafiliados.afinombre as nombre,
  nacer.smiafiliados.afidni as dni,
  nacer.smiafiliados.afisexo as sexo,
  nacer.smiafiliados.afifechanac as fecha_nacimiento,
  facturacion.comprobante.fecha_comprobante as fecha_comprobante,
  facturacion.factura.id_factura as nro_factura,
  nacer.efe_conv.cuie as cuie,
  nacer.efe_conv.nombre as efector,
  nomenclador.prestaciones_n_op.prestacion as cod_pres,
  a.categoria as nom_pres,
  nomenclador.prestaciones_n_op.tema as cod_obj_pres,
  b.categoria as obj_pres,
  nomenclador.prestaciones_n_op.patologia as cod_pat,
  nomenclador.patologias.descripcion as patologia,
  nomenclador.prestaciones_n_op.profesional as cod_prof,
  c.categoria as prof,
  nomenclador.prestaciones_n_op.edad as edad,
  nomenclador.prestaciones_n_op.anio as anio,
  nomenclador.prestaciones_n_op.mes as mes,
  nomenclador.prestaciones_n_op.dias as dias,
  nomenclador.prestaciones_n_op.precio as precio,
  nomenclador.prestaciones_n_op.codigo as codigo
FROM
  facturacion.comprobante
  LEFT JOIN nacer.smiafiliados ON (facturacion.comprobante.id_smiafiliados = nacer.smiafiliados.id_smiafiliados)
  LEFT JOIN nomenclador.prestaciones_n_op ON (facturacion.comprobante.id_comprobante = nomenclador.prestaciones_n_op.id_comprobante)
  LEFT JOIN nomenclador.grupo_prestacion a ON (nomenclador.prestaciones_n_op.prestacion = a.codigo)
  LEFT JOIN nomenclador.grupo_prestacion b ON (nomenclador.prestaciones_n_op.tema = b.codigo)
  LEFT JOIN nomenclador.grupo_prestacion c ON (nomenclador.prestaciones_n_op.profesional = c.codigo)
  LEFT JOIN nomenclador.patologias ON (nomenclador.prestaciones_n_op.patologia = nomenclador.patologias.codigo)
  LEFT JOIN nacer.efe_conv ON (comprobante.cuie = nacer.efe_conv.cuie)
  LEFT JOIN facturacion.factura ON (facturacion.comprobante.id_factura=facturacion.factura.id_factura)
 WHERE
  ( nomenclador.prestaciones_n_op.patologia='$patologia') and (nacer.efe_conv.cuie='$cuie')
   and ('$fecha_desde' <= facturacion.comprobante.fecha_comprobante and facturacion.comprobante.fecha_comprobante <= '$fecha_hasta')
   ORDER BY comprobante.fecha_comprobante";
$f_res1=sql($query1,"No se puede ejecutar");

excel_header("reporte_n_op_1_excel.xls");
//echo $html_header;
?>
<form name=form1 method=post action="reporte_n_op_1_excel.xls">
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr>
   <td colspan="17" align="center" bgcolor=#D0A9F5>
   	<font size="+1"><b>LISTADO NOMINALIZADO COMPLETO DE PRESTACIONES A PACIENTES</b></font>
   </td>
  </tr>
  <tr>
   <td colspan="17" align="center" bgcolor=#D0A9F5>
   	<font size="-1"><b>(Las prestaciones que tiene Numero de Factura son a BENEFICIARIOS y estan PAGADAS, las demas son prestaciones a pacientes NO Activos o NO Empadronados)</b></font>
   </td>
  </tr>
  <tr bgcolor=#C0C0FF>
    <td align=center >Apellido</td>      	
    <td align=center >Nombre</td>    
    <td align=center >DNI</td>
    <td align=center >Sexo</td>
    <td align=center >Fecha de Nacimiento</td>
    <td align=center >Fecha de Prestacion</td>
    <td align=center >Num de Factura</td>
    <td align=center >CUIE</td>       
    <td align=center >Efector</td>       
<!--    <td align=center >Cod Prestacion</td>-->
    <td align=center >Prestacion</td>    
<!--    <td align=center >Cod Obj Pres</td>   -->
    <td align=center >Objeto de Prestacion</td>
<!--    <td align=center >Cod Patologia</td>-->
    <td align=center >Patologia</td>
<!--    <td align=center >Cod Profesional</td>-->
<!--    <td align=center >Profesional</td>-->
    <td align=center >Años al Momento de Prestacion</td>
    <td align=center >Meses al Momento de Prestacion</td>
    <td align=center >Diaas al Momento de Prestacion</td>
    <td align=center >Precio</td>
    <td align=center >Codigo</td>
  </tr>
  <?   
  while (!$f_res1->EOF) {?>  
    <tr>    
     <td align=center><?=$f_res1->fields['apellido']?></td>  
     <td align=center><?=$f_res1->fields['nombre']?></td>
     <td align=center><?=$f_res1->fields['dni']?></td>        
     <td align=center><?=$f_res1->fields['sexo']?></td> 
     <td align=center><?=Fecha($f_res1->fields['fecha_nacimiento'])?></td>
     <td align=center><?=Fecha($f_res1->fields['fecha_comprobante'])?></td>
     <td align=center><?=$f_res1->fields['nro_factura']?></td> 
     <td align=center><?=$f_res1->fields['cuie']?></td>
     <td align=center><?=$f_res1->fields['efector']?></td>
<!-- <td align=center><?//=$f_res1->fields['cod_pres']?></td>-->
     <td align=center><?=$f_res1->fields['nom_pres']?></td>
<!-- <td align=center><?//=$f_res1->fields['cod_obj_pres']?></td>-->
     <td align=center><?=$f_res1->fields['obj_pres']?></td>
<!-- <td align=center><?//=$f_res1->fields['cod_pat']?></td>-->
     <td align=center><?=$f_res1->fields['patologia']?></td>
<!-- <td align=center><?//=$f_res1->fields['cod_prof']?></td>
     <td align=center><?//=$f_res1->fields['prof']?></td>-->
     <td align=center><?=number_format($f_res1->fields['anio'],0,',','.')?></td>            
     <td align=center><?=number_format($f_res1->fields['mes'],0,',','.')?></td>            
     <td align=center><?=number_format($f_res1->fields['dias'],0,',','.')?></td>            
     <td align=center><?="$ ".number_format($f_res1->fields['precio'],2,',','.')?></td>            
     <td align=center><?=$f_res1->fields['codigo']?></td>
    </tr>
	<?$f_res1->MoveNext();
    }?>  
 </table> 
 </form>
 <?=fin_pagina();?>