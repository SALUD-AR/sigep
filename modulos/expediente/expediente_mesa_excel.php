<?php
/*
Author: sebastian lohaiza

modificada por
$Author: seba $
$Revision: 1.30 $
$Date: 2009/11/01 18:25:40 $
*/
require_once("../../config.php");



$sql="select expediente.expediente.id_expediente,
  expediente.expediente.id_efe_conv,
  expediente.expediente.nro_exp,
  expediente.expediente.fecha_ing,
  expediente.expediente.monto,
  expediente.expediente.plazo_para_pago,
  expediente.expediente.comentario1,
  expediente.expediente.id_factura,
  expediente.expediente.periodo,
  expediente.expediente.estado,
  nacer.efe_conv.nombre,
  nacer.efe_conv.cuie 
  from expediente.expediente
  inner join nacer.efe_conv on expediente.expediente.id_efe_conv=nacer.efe_conv.id_efe_conv
  where expediente.control=0 and (expediente.estado<>'D' and expediente.estado<>'E')";;

$result=sql($sql) or fin_pagina();

excel_header("listado de expedientes.xls");

?>
<form name=form1 method=post action="listado_factura_cont_excel.php">
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
   	<td align=right id=mo>Numero de Expediente</td>      	
    <td align=right id=mo>Nombre del Efector</td>      	
    <td align=right id=mo>CUIE</td>
    <td align=right id=mo>Nro.Factura</td>
    <td align=right id=mo>Fecha de Ingreso</td>
    <td align=right id=mo>Fecha de Plazo</td> 
    <td align=right id=mo>Periodo</td>  
    <td align=right id=mo>Monto</td>     
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
   <td ><?=$result->fields['nro_exp']?></td>
     <td ><?=$result->fields['nombre']?></td>
     <td ><?=$result->fields['cuie']?></td>
     <td ><?=$result->fields['id_factura']?></td>
     <td ><?=fecha($result->fields['fecha_ing'])?></td> 
     <td ><?=$result->fields['plazo_para_pago']?></td>   
     <td ><?=$result->fields['periodo']?></td>
     <td ><?=number_format($result->fields['monto'],2,',','.')?></td>    
      </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>



