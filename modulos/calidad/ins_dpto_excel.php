<?php

require_once ("../../config.php");


$sql="SELECT 
  *
FROM
  nacer.dpto";
   
$result=sql($sql) or fin_pagina();

excel_header("Cuadro de Mando.xls");

?>
<form name=form1 method=post action="efectores_unif_excel.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total: </b><?=$result->RecordCount();?> 
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id=mo>NOMBRE</td>    
    <td align=right id=mo>Inscriptos ACTIVOS</td>    
    <td align=right id=mo>Inscriptos INACTIVOS</td>    
    <td align=right id=mo>Total Inscriptos</td>  
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td><?=$result->fields['nombre']?></td>
     
     <?$codigo=$result->fields['codigo'];
     $sql = "SELECT count (smiafiliados.id_smiafiliados)as r1 
			from nacer.smiafiliados 
			left join nacer.efe_conv ON (nacer.efe_conv.cuie = nacer.smiafiliados.cuieefectorasignado)
     		WHERE departamento='$codigo' and activo='S'";
     $r1=sql($sql,"error R1");     
     ?>
     <td><?=$r1->fields['r1']?></td>
     
     <?
     $sql = "SELECT count (smiafiliados.id_smiafiliados)as r1 
			from nacer.smiafiliados 
			left join nacer.efe_conv ON (nacer.efe_conv.cuie = nacer.smiafiliados.cuieefectorasignado)
     		WHERE departamento='$codigo' and activo='N'";
     $r2=sql($sql,"error R1");     
     ?>
     <td><?=$r2->fields['r1']?></td>
     
     <td><?=$r1->fields['r1']+$r2->fields['r1']?></td>     
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>