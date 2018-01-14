<?php

require_once ("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

$fecha_desde_db= fecha_db($fecha_desde);
$fecha_hasta_db= fecha_db($fecha_hasta);

if ($cuie=='todos')
	$sql="SELECT *, efe_conv.nombre as nombreefector, embarazadas.nombre as nombrepers, (fpcp-fum) as dias_cap
			FROM trazadoras.embarazadas
			left join nacer.efe_conv using (CUIE)
				where fpcp >= '$fecha_desde_db' and fpcp <= '$fecha_hasta_db'";
else
	$sql="SELECT *, efe_conv.nombre as nombreefector, embarazadas.nombre as nombrepers, (fpcp-fum) as dias_cap
			FROM trazadoras.embarazadas
			left join nacer.efe_conv using (CUIE)
				where fpcp >= '$fecha_desde_db' and fpcp <= '$fecha_hasta_db' and cuie = '$cuie'";

$result=sql($sql) or fin_pagina();
excel_header("emb_listado_excel.xls");
//echo $html_header;

?>
<form name=form1 method=post action="emb_listado_excel.php">
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
    <td align=right id=mo>Efector</td>      	
    <td align=right id=mo>tipo_doc</td>
    <td align=right id=mo>num_doc</td>
    <td align=right id=mo>apellido</td>        
    <td align=right id=mo>nombre</td> 
    <td align=right id=mo>fecha_control</td> 
    <td align=right id=mo>sem_gestacion</td> 
    <td align=right id=mo>fum</td> 
    <td align=right id=mo>fpp</td> 
    <td align=right id=mo>fpcp</td> 
    <td align=right id=mo>Dias de Captacion (fpcp - FUM)</td>     
    <td align=right id=mo>Usuario</td>     
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
     <td ><?=fecha($result->fields['fecha_control'])?></td>      
     <td ><?=number_format($result->fields['sem_gestacion'],0,'','')?></td>      
     <td ><?=fecha($result->fields['fum'])?></td>      
     <td ><?=fecha($result->fields['fpp'])?></td>      
     <td ><?=fecha($result->fields['fpcp'])?></td>      
     <td ><?=$result->fields['dias_cap']?></td>      
     <td ><?=$result->fields['usuario']?></td>      
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>
