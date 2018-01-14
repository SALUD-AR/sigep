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
			  b.categoria as obj_pres,
			  count (comprobante.id_comprobante) as total_obj_pres
			FROM
			  facturacion.comprobante
			  LEFT JOIN nomenclador.prestaciones_n_op ON (facturacion.comprobante.id_comprobante = nomenclador.prestaciones_n_op.id_comprobante)
			  LEFT JOIN nomenclador.grupo_prestacion b ON (nomenclador.prestaciones_n_op.tema = b.codigo)
			  LEFT JOIN nomenclador.patologias ON (nomenclador.prestaciones_n_op.patologia = nomenclador.patologias.codigo)
			  LEFT JOIN nacer.efe_conv ON (comprobante.cuie = nacer.efe_conv.cuie)
			WHERE
			  ( nomenclador.prestaciones_n_op.patologia='$patologia') and (nacer.efe_conv.cuie='$cuie')
			   and ('$fecha_desde' <= facturacion.comprobante.fecha_comprobante and facturacion.comprobante.fecha_comprobante <= '$fecha_hasta')
			GROUP BY b.categoria,prestaciones_n_op.tema
			ORDER BY obj_pres";
$f_res1=sql($query1,"No se puede ejecutar");

$query2="SELECT 
			a.categoria as nom_pres,			  
			count (comprobante.id_comprobante) as total_pres
			FROM
				facturacion.comprobante
			LEFT JOIN nomenclador.prestaciones_n_op ON (facturacion.comprobante.id_comprobante = nomenclador.prestaciones_n_op.id_comprobante)
			LEFT JOIN nomenclador.grupo_prestacion a ON (nomenclador.prestaciones_n_op.prestacion = a.codigo)
			LEFT JOIN nomenclador.patologias ON (nomenclador.prestaciones_n_op.patologia = nomenclador.patologias.codigo)
			LEFT JOIN nacer.efe_conv ON (comprobante.cuie = nacer.efe_conv.cuie)
			WHERE
			  ( nomenclador.prestaciones_n_op.patologia='$patologia') and (nacer.efe_conv.cuie='$cuie')
			   and ('$fecha_desde' <= facturacion.comprobante.fecha_comprobante and facturacion.comprobante.fecha_comprobante <= '$fecha_hasta')
			GROUP BY a.categoria,prestaciones_n_op.prestacion
			ORDER BY nom_pres";
$f_res2=sql($query2);

excel_header("reporte_n_op_2_excel.xls");
//echo $html_header;
?>
<form name=form1 method=post action="reporte_n_op_2_excel.xls">
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr>
   <td colspan="2" align="center" bgcolor=#D0A9F5>
   	<font size="+1"><b>Agrupadas por Objeto de la Prestacion</b></font>
   </td>
  </tr>
  <tr>
   <td colspan="2" align="center" bgcolor=#D0A9F5>
   	<font size="-1"><b>(Prestaciones del Efector en el Periodo y Diagnostico Seleccionado Agrupadas por Objeto de la Prestacion)</b></font>
   </td>
  </tr>
  <tr bgcolor=#C0C0FF>
    <td align=center >Objeto de la Prestacion</td>      	
    <td align=center >Total</td>    
  </tr>
  <?   
  while (!$f_res1->EOF) {?>  
    <tr>    
     <td align=left><?=$f_res1->fields['obj_pres']?></td>  
     <td align=center><?=$f_res1->fields['total_obj_pres']?></td>
    </tr>
	<?$f_res1->MoveNext();
    }?>
    
  <tr>
   <td colspan="2" align="center" bgcolor=#D0A9F5>
   	<font size="+1"><b>Agrupadas por Prestacion</b></font>
   </td>
  </tr>
  <tr>
   <td colspan="2" align="center" bgcolor=#D0A9F5>
   	<font size="-1"><b>(Prestaciones del Efector en el Periodo y Diagnostico Seleccionado Agrupadas por Prestacion)</b></font>
   </td>
  </tr>
  <tr bgcolor=#C0C0FF>
    <td align=center >Prestacion</td>      	
    <td align=center >Total</td>    
  </tr>
  <?   
  while (!$f_res2->EOF) {?>  
    <tr>    
     <td align=left><?=$f_res2->fields['nom_pres']?></td>  
     <td align=center><?=$f_res2->fields['total_pres']?></td>
    </tr>
	<?$f_res2->MoveNext();
    }?>  
 </table> 
 </form>
 <?=fin_pagina();?>