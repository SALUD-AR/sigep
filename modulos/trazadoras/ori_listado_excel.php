<?php

require_once ("../../config.php");

$sql="SELECT *, efe_conv.nombre as nombreefector, ori.nombre as nombrepers
		FROM trazadoras.ori
		left join nacer.efe_conv using (CUIE)";


$result=sql($sql) or fin_pagina();

excel_header("ori_listado_excel.xls");

?>
<form name=form1 method=post action="ori_listado_excel.php">
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
    <td align=right id=mo>cuie</td>      	
    <td align=right id=mo>nombreefector</td>      	
    <td align=right id=mo>tipo_doc</td>
    <td align=right id=mo>num_doc</td>
    <td align=right id=mo>apellido</td>        
    <td align=right id=mo>nombre</td> 
    <td align=right id=mo>evento</td> 
    <td align=right id=mo>fecha_evento</td>     
    <td align=right id=mo>observaciones</td>       
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td ><?=$result->fields['cuie']?></td>
     <td ><?=$result->fields['nombreefector']?></td>
     <td ><?=$result->fields['tipo_doc']?></td>
     <td ><?=number_format($result->fields['num_doc'],0,'','')?></td>     
     <td ><?=$result->fields['apellido']?></td>      
     <td ><?=$result->fields['nombrepers']?></td> 
     <td ><?=$result->fields['evento']?></td>        
     <td ><?=fecha($result->fields['fecha_evento'])?></td>          
     <td ><?=$result->fields['observaciones']?></td>     
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>