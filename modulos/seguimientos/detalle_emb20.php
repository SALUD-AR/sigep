<?php

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

$sql_tmp="select *
			from trazadoras.embarazadas 
			where cuie = '$cuie' and fecha_control between '$fecha_desde' and '$fecha_hasta' and fecha_control <= fpp-140";
$result=sql($sql_tmp) or fin_pagina();

$cuidado_sexual=$res_cuidado_sexual->fields['total'];

echo $html_header;
?>
<form name=form1 method=post action="detalle_ceb.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$result->RecordCount();?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id=mo>Inscripcion</td>   	
    <td align=right id=mo>DNI</td>      	
    <td align=right id=mo>Apellido</td>      	
    <td align=right id=mo>Nombre</td>      	
    <td align=right id=mo>Fecha Control</td>
  </tr>
  <?   
  while (!$result->EOF) {?>   
    <tr>          
	 <td>Inscripto SUMAR</td>
     <td><?=$result->fields['num_doc']?></td>
     <td><?=$result->fields['apellido']?></td>
     <td><?=$result->fields['nombre']?></td>
	 <td><?=fecha($result->fields['fecha_control'])?></td> 
    </tr>	
	<?$result->MoveNext();
    }?>
 </table>
 </form>
 <?=fin_pagina();// aca termino ?>