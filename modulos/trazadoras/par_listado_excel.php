<?php

require_once ("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

$fecha_desde_db= fecha_db($fecha_desde);
$fecha_hasta_db= fecha_db($fecha_hasta);

if ($cuie=='todos')
	$sql="SELECT * , efe_conv.nombre as nombreefector, partos.nombre as nombrepers
		FROM trazadoras.partos
		left join nacer.efe_conv using (CUIE)
				where fecha_parto >= '$fecha_desde_db' and fecha_parto <= '$fecha_hasta_db'";
else
	$sql="SELECT *, efe_conv.nombre as nombreefector, partos.nombre as nombrepers
			 FROM trazadoras.partos
			left join nacer.efe_conv using (CUIE)
				where fecha_parto >= '$fecha_desde_db' and fecha_parto <= '$fecha_hasta_db' and cuie = '$cuie'";

$result=sql($sql) or fin_pagina();

excel_header("par_listado_excel.xls");

?>
<form name=form1 method=post action="par_listado_excel.php">
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
    <td align=right id=mo>fecha_parto</td> 
    <td align=right id=mo>apgar</td> 
    <td align=right id=mo>peso</td> 
    <td align=right id=mo>vdrl</td> 
    <td align=right id=mo>antitetanica</td> 
    <td align=right id=mo>fecha_conserjeria</td> 
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
     <td ><?=fecha($result->fields['fecha_parto'])?></td>      
     <td ><?=number_format($result->fields['apgar'],0,'','')?></td>      
     <td ><?=number_format($result->fields['peso'],3,',','.')?></td> 
     <td ><?=$result->fields['vdrl']?></td>
     <td ><?=$result->fields['antitetanica']?></td>               
     <td ><?=fecha($result->fields['fecha_conserjeria'])?></td>      
     <td ><?=$result->fields['observaciones']?></td>      
           
         
     
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>