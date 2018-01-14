<?php

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

$sql_tmp="select *	from uad.remediar_x_beneficiario 
							left join sistema.usuarios ON (cast(usuario_carga as integer)=id_usuario)
							where usuario_carga='$usuario_carga'
							order by fecha_carga DESC";
$result=sql($sql_tmp) or fin_pagina();

$sql_cuidado_sexual="select count(*)	from uad.remediar_x_beneficiario 
							left join sistema.usuarios ON (cast(usuario_carga as integer)=id_usuario)
							where usuario_carga='$usuario_carga'";
$res_cuidado_sexual= sql($sql_cuidado_sexual) or die;
$cuidado_sexual=$res_cuidado_sexual->fields['total'];

echo $html_header;
?>
<form name=form1 method=post action="auditoriaemp_detalle.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total: <?=$result->RecordCount();?> </b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id=mo>Nro Formulario</td>      	
    <td align=right id=mo>Fecha Empadronamiento</td>      	
    <td align=right id=mo>Fecha de Carga</td>   
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td><?=$result->fields['nroformulario']?></td>
     <td><?=fecha($result->fields['fechaempadronamiento'])?></td>
     <td><?=fecha($result->fields['fecha_carga'])?></td>
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>
