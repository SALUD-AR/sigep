<?php

require_once ("../../config.php");

$sql=$parametros["sql"];
$result=sql($sql) or fin_pagina();

excel_header("resumen_prestaciones.xls");

?>
<form name=form1 method=post action="resumen_prestaciones_excel.php">
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right >Codigo</td>      	
    <td align=right >Descripcion</td>
    <td align=right >Cantidad</td>
    <td align=right id=mo>P Unitario</td>
    <td align=right id=mo>P Total</td>    
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td><?=$result->fields['codigo']?></td>
     <td><?=$result->fields['descripcion']?></td>
     <td><?=$result->fields['cantidad']?></td>
     <td><?=number_format($result->fields['precio_prestacion'],2,',','.')?></td>     
     <td><?=number_format($result->fields['precio_total'],2,',','.')?></td>             
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>