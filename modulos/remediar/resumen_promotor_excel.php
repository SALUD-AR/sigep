<?php

require_once ("../../config.php");

$sql=$parametros["sql"];
$result=sql($sql) or fin_pagina();

excel_header("resumen_promotor.xls");

?>
<form name=form1 method=post action="resumen_promotor_excel.php">
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id=mo>Lugar</td>
    <td align=right id=mo>Promotor</td>
	<td align=right id=mo>DNI</td>
	<td align=right id=mo>Fecha Carga</td>
    <td align=right id=mo>Fichas Completas</td>
	<td align=right id=mo>Fichas Enviadas</td>
  </tr>
  <?   
  while (!$result->EOF) { ?>
    <tr>     
     <td><?=$result->fields['centro_inscriptor'].'-'.$result->fields['nombreefector']?></td>
     <td><?=$result->fields['agente']?></td>
	 <td><?=$result->fields['dni_agente']?></td>
	 <td><?=fecha($result->fields['fecha_carga'])?></td>
     <td><?=$result->fields['tcompleta']?></td>
	 <td><?=$result->fields['tenviado']?></td>
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
    
 </form>