<?php

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

$sql_tmp="select fichero.id_smiafiliados, afiapellido,fichero.fecha_control,afinombre,afidni, beneficiarios.*
			from fichero.fichero 
			left join nacer.smiafiliados using (id_smiafiliados)
			left join leche.beneficiarios using (id_beneficiarios)
			where cuie = '$cuie' and fecha_control between '2013-12-31' and '2014-12-31' and salud_rep = 'SI'";
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
       <b>Total de Inscriptos: <?=$result->RecordCount();?>  --- Total Inscriptos sin Duplicados: <?=$cuidado_sexual;?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id=mo>Id</td> 
	<td align=right id=mo>Inscripcion</td>	
    <td align=right id=mo>DNI</td>      	
    <td align=right id=mo>Apellido</td>      	
    <td align=right id=mo>Nombre</td>      	
    <td align=right id=mo>Fecha Control</td>
  </tr>
  <?   
  while (!$result->EOF) {
  
		if ($result->fields['id_smiafiliados']!=0){?>  
    <tr>  	 
     <td><?=$result->fields['id_smiafiliados']?></td>
	 <td>Inscripto SUMAR</td>
     <td><?=$result->fields['afidni']?></td>
     <td><?=$result->fields['afiapellido']?></td>
     <td><?=$result->fields['afinombre']?></td>
	 <td><?=fecha($result->fields['fecha_control'])?></td> 
    </tr>
	<?}
	else{?>
	<tr>  	 
     <td><?=$result->fields['id_beneficiarios']?></td>
     <td>Inscripto por Fichero</td>
     <td><?=$result->fields['documento']?></td>
     <td><?=$result->fields['apellido']?></td>
     <td><?=$result->fields['nombre']?></td>
	 <td><?=fecha($result->fields['fecha_control'])?></td> 
    </tr>
	<?}
	$result->MoveNext();
    }?>

 </table>
 </form>
