<?php

require_once ("../../config.php");

$sql="select seguimiento_remediar.clave_beneficiario, apellido_benef, nombre_benef, numero_doc,fecha_nacimiento_benef,efe_conv.nombre,seguimiento_remediar.fecha_comprobante
      from trazadoras.seguimiento_remediar
      inner join uad.beneficiarios ON (seguimiento_remediar.clave_beneficiario= beneficiarios.clave_beneficiario)
      inner join nacer.efe_conv ON (efector=cuie)";
$result=sql($sql) or fin_pagina();

//excel_header("resumen_operador.xls");
echo $html_header;

?>
<form name=form1 method=post action="listado_seguimiento_excel.php">
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td>Clave Beneficiario</td>          
    <td>Documento</td>          
    <td>Apellido</td>          
    <td>Nombre</td>          
    <td>Fecha Nac</td>          
    <td>Efector</td>          
    <td>Fecha Seg</td>    
  </tr>
  <?   
  while (!$result->EOF) { ?>
    <tr>     
	   <td  ><?=$result->fields['clave_beneficiario']?></td>
     <td  ><?=$result->fields['numero_doc']?></td>        
     <td  ><?=$result->fields['apellido_benef']?></td>     
     <td  ><?=$result->fields['nombre_benef']?></td>     
     <td  ><?=fecha($result->fields['fecha_nacimiento_benef'])?></td> 
     <td  ><?=$result->fields['nombre']?></td> 
     <td  ><?=fecha($result->fields['fecha_comprobante'])?></td> 
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>