<?php

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

$sql_tmp="select *
from nacer.smiafiliados 
where cuieefectorasignado='$cuie' and activo='S' and ceb='$ceb' and grupopoblacional='$grupo_poblacional'
order by afidni";

$result=sql($sql_tmp) or fin_pagina();

echo $html_header;
?>
<form name=form1 method=post action="detalle_ceb.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total beneficiarios: </b><?=$result->RecordCount();?> 
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id=mo>Apellido</td>      	
    <td align=right id=mo>Nombre</td>
    <td align=right id=mo>DNI</td>
    <td align=right id=mo>Clave Beneficiario</td>
    <td align=right id=mo>F Ins</td>
    <td align=right id=mo>Calle</td>
    <td align=right id=mo>Numero</td>
    <td align=right id=mo>Manzana</td>
    <td align=right id=mo>Fecha Ult Pres</td>
    <td align=right id=mo>Codigo Ult Pres</td>
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td><?=$result->fields['afiapellido']?></td>
     <td><?=$result->fields['afinombre']?></td>
     <td><?=$result->fields['afidni']?></td>     
     <td><?=$result->fields['clavebeneficiario']?></td>  
     <td><?=fecha($result->fields['fechainscripcion'])?></td> 
     <td><?=$result->fields['afiDomCalle']?></td>  
     <td><?=$result->fields['afiDomNro']?></td>  
     <td><?=$result->fields['afiDomManzana']?></td>  
     <td><?=fecha($result->fields['FechaUtimaPrestacion'])?></td> 
     <td><?=$result->fields['CodigoPrestacion']?></td>  
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>
