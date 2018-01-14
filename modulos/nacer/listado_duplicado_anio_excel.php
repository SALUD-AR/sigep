<?php

require_once ("../../config.php");

$sql="SELECT *, current_date-afifechanac as dias_pasados FROM nacer.smiafiliados
WHERE aficlasedoc <> 'P' and ( current_date-afifechanac) > 365 and activo = 'S'
order by afidni";

$result=sql($sql) or fin_pagina();

excel_header("beneficiarios.xls");

?>
<form name=form1 method=post action="listado_duplicado_anio_excel.php">
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
    <td align=right >Doc Madre</td>      	
    <td align=right >Nombre Madre</td>
    <td align=right >Fecha Nac Niño</td>
    <td align=right >Apellido</td>
    <td align=right >Nombre</td>    
    <td align=right >DNI</td>    
    <td align=right >F Ins</td>    
    <td align=right >Dias Pasados</td>    
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td><?=$result->fields['manrodocumento']?></td>
     <td><?=$result->fields['maapellido'].", ".$result->fields['manombre']?></td> 
     <td><?=fecha($result->fields['afifechanac'])?></td>
     <td><?=$result->fields['afiapellido']?></td>     
     <td><?=$result->fields['afinombre']?></td>     
     <td><?=$result->fields['afidni']?></td> 
     <td><?=fecha($result->fields['fechainscripcion'])?></td> 
     <td><?=$result->fields['dias_pasados']?></td> 
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>