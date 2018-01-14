<?php

require_once ("../../config.php");

$sql="SELECT 
  trazadoras.vacunas.apellido,
  trazadoras.vacunas.nombre,
  trazadoras.vacunas.dni,
  trazadoras.vacunas.sexo,
  trazadoras.vacunas.fecha_nac,
  trazadoras.vacunas.domicilio,
  trazadoras.dosis_apli.nombre as nombre_1,
  trazadoras.vac_apli.nombre as nombre_2,
  trazadoras.vacunas.fecha_vac,
  trazadoras.vacunas.nom_resp,
  nacer.efe_conv.nombre as nombre_3,
  trazadoras.vacunas.comentario
FROM
  nacer.efe_conv
  INNER JOIN trazadoras.vacunas ON (nacer.efe_conv.cuie = trazadoras.vacunas.cuie)
  INNER JOIN trazadoras.dosis_apli ON (trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli)
  INNER JOIN trazadoras.vac_apli ON (trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli)";


$result=sql($sql) or fin_pagina();

excel_header("vacuna_excel.xls");

?>
<form name=form1 method=post action="vac_listado_excel.php">
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
    <td align=right id=mo>Apellido</td>      	
    <td align=right id=mo>Nombre</td>      	
    <td align=right id=mo>Documento</td>
    <td align=right id=mo>Sexo</td>
    <td align=right id=mo>Fecha Nacimiento</td>        
    <td align=right id=mo>Domicilio</td> 
    <td align=right id=mo>Vacuna</td> 
    <td align=right id=mo>Dosis</td> 
    <td align=right id=mo>Fecha Vacunacion</td> 
    <td align=right id=mo>Responsable</td> 
    <td align=right id=mo>Efector</td>     
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td ><?=$result->fields['apellido']?></td>
     <td ><?=$result->fields['nombre']?></td>
     <td ><?=$result->fields['dni']?></td>
     <td ><?=$result->fields['sexo']?></td>
     <td ><?=fecha($result->fields['fecha_nac'])?></td>
     <td ><?=$result->fields['domicilio']?></td>
     <td ><?=$result->fields['nombre_2']?></td>
     <td ><?=$result->fields['nombre_1']?></td>
     <td ><?=fecha($result->fields['fecha_vac'])?></td>         
     <td ><?=$result->fields['nom_resp']?></td>    
     <td ><?=$result->fields['nombre_3']?></td>    
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>