<?php

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

$fecha_desde_db= fecha_db($fecha_desde);
$fecha_hasta_db= fecha_db($fecha_hasta);

if ($cuie=='todos')
	$sql="SELECT *, efe_conv.nombre as nombreefector, nino_new.nombre as nombrepers
				FROM trazadoras.nino_new
				left join nacer.efe_conv using (CUIE)
				where fecha_control >= '$fecha_desde_db' and fecha_control <= '$fecha_hasta_db'";
else
$sql="SELECT *, efe_conv.nombre as nombreefector, nino_new.nombre as nombrepers
				FROM trazadoras.nino_new
				left join nacer.efe_conv using (CUIE)
				where fecha_control >= '$fecha_desde_db' and fecha_control <= '$fecha_hasta_db' and cuie = '$cuie'";

$result=sql($sql) or fin_pagina();

excel_header("nino_listado_excel.xls");
//echo $html_header;

?>
<form name=form1 method=post action="nino_listado_excel.php">
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
    <td align=right id=mo>id</td>      	
    <td align=right id=mo>cuie</td>      	
    <td align=right id=mo>nombreefector</td>      	
    <td align=right id=mo>tipo_doc</td>
    <td align=right id=mo>num_doc</td>
    <td align=right id=mo>apellido</td>        
    <td align=right id=mo>nombre</td> 
    <td align=right id=mo>fecha_nac</td> 
    <td align=right id=mo>fecha_control</td> 
    <td align=right id=mo>peso</td> 
    <td align=right id=mo>talla</td> 
    <td align=right id=mo>perim_cefalico</td> 
    <td align=right id=mo>percen_peso_edad</td> 
    <td align=right id=mo>percen_talla_edad</td> 
    <td align=right id=mo>percen_perim_cefali_edad</td> 
    <td align=right id=mo>percen_peso_talla</td> 
    <td align=right id=mo>triple_viral</td> 
    <td align=right id=mo>nino_edad</td> 
    <td align=right id=mo>observaciones</td>    
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td ><?=$result->fields['id_nino_new']?></td>
     <td ><?=$result->fields['cuie']?></td>
     <td ><?=$result->fields['nombreefector']?></td>
     <td ><?=$result->fields['tipo_doc']?></td>
     <td ><?=number_format($result->fields['num_doc'],0,'','')?></td>     
     <td ><?=$result->fields['apellido']?></td>      
     <td ><?=$result->fields['nombrepers']?></td>      
     <td ><?=fecha($result->fields['fecha_nac'])?></td>      
     <td ><?=fecha($result->fields['fecha_control'])?></td>      
     <td ><?=number_format($result->fields['peso'],3,',','.')?></td>      
     <td ><?=number_format($result->fields['talla'],3,',','.')?></td>      
     <td ><?=number_format($result->fields['perim_cefalico'],2,',','.')?></td>      
     <td ><?
     		if ($result->fields['percen_peso_edad']=='A') echo "-10";
     		if ($result->fields['percen_peso_edad']=='B') echo "10 al 90";
     		if ($result->fields['percen_peso_edad']=='C') echo "+90";
     		if ($result->fields['percen_peso_edad']=='') echo "Sin Dato";     
     ?></td>      
     <td ><?
     		if ($result->fields['percen_talla_edad']=='A') echo "-3";
     		if ($result->fields['percen_talla_edad']=='B') echo "3 al 97";
     		if ($result->fields['percen_talla_edad']=='C') echo "+97";
     		if ($result->fields['percen_talla_edad']=='') echo "Sin Dato";
     ?></td>      
     <td ><?
     		if ($result->fields['percen_perim_cefali_edad']=='A') echo "-(-2DS)";
     		if ($result->fields['percen_perim_cefali_edad']=='B') echo "-2DS +2DS";
     		if ($result->fields['percen_perim_cefali_edad']=='C') echo "+(+2DS)";
     		if ($result->fields['percen_perim_cefali_edad']=='') echo "Sin Dato";
     ?></td>      
     <td ><?
     		if ($result->fields['percen_peso_talla']=='A') echo "(-10)";
     		if ($result->fields['percen_peso_talla']=='B') echo "-10 al +10";
     		if ($result->fields['percen_peso_talla']=='C') echo "(+10)";
     		if ($result->fields['percen_peso_talla']=='') echo "Sin Dato";
     ?></td>
     <td ><?=fecha($result->fields['triple_viral'])?></td> 
     <td ><?=number_format($result->fields['nino_edad'],0,'','')?></td>        
     <td ><?=$result->fields['observaciones']?></td>  
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>
