<?php

require_once ("../../config.php");

$sql=$parametros["sql"];
$result=sql($sql) or fin_pagina();

excel_header("resumen_operador.xls");

?>
<form name=form1 method=post action="listado_nominal_excel.php">
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
  	<td align=right id=mo>Fecha de Carga</td>
    <td align=right id=mo>Apellido</td>
    <td align=right id=mo>Nombre</td>
    <td align=right id=mo>Calle y Nº</td>
    <td align=right id=mo>Localidad</td>
    <td align=right id=mo>Municipio</td>
	<td align=right id=mo>Barrio</td>
	<td align=right id=mo>Efector</td>
	<td align=right id=mo>Promotor</td>
	<td align=right id=mo>Score Riesgo</td>
  </tr>
  <?   
  while (!$result->EOF) { ?>
    <tr>     
	 <td><?=fecha($result->fields['fechaempadronamiento'])?></td>
     <td><?=$result->fields['ape']?></td>
     <td><?=$result->fields['nom']?></td>
     <td><?=$result->fields['calle'].' '.$result->fields['num_calle']?></td>
     <td><?=$result->fields['localidad']?></td>
     <td><?=$result->fields['municipio']?></td>
	 <td><?=$result->fields['barrio']?></td>
	  <td><?=$result->fields['nombreefector']?></td>
	  <td><?=$result->fields['promotor']?></td>
	  <td><?=$result->fields['score_riesgo']?></td>
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>