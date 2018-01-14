<?php

require_once ("../../config.php");

$sql=$parametros["sql"];
$result=sql($sql) or fin_pagina();

excel_header("resumen_operador_hora.xls");

?>
<form name=form1 method=post action="resumen_operador_hora_excel.php">
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
  <td align=right id=mo>Fecha de Carga</td>
    <td align=right id=mo>Hora</td>
    <td align=right id=mo>Usuario</td>
    <td align=right id=mo>Cantidad</td>
  </tr>
  <?   
  while (!$result->EOF) {
    ?>
    <tr>     
	 <td><?=$result->fields['fecha_cargax']?></td>
		 <td><?=$result->fields['hora']?></td>
		 <td><?=$result->fields['nom_ape']?></td>
		 <td><?=$result->fields['cant']?></td>
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>