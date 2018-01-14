<?php
/*
Author: sebastian lohaiza

modificada por
$Author: seba $
$Revision: 1.30 $
$Date: 2009/11/01 18:25:40 $
*/
require_once("../../config.php");



//$sql="SELECT * FROM expediente.expediente left join nacer.efe_conv using (id_efe_conv) where control=3";
$sql="select * from (
select * from (select id_expediente from expediente.expediente where control=4) as tabla1 left join (select id_expediente,num_tranf
 from expediente.transaccion where num_tranf is not null) as tabla2 using (id_expediente)) as tabla3 left join expediente.expediente
 using (id_expediente) left join nacer.efe_conv using (id_efe_conv)";
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
    <td align=right id=mo>Monto Prefactura</td>   
    <td align=right id=mo>Monto Autorizado para Pago</td>  
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
     <? $id_factura=$result->fields['id_factura'];
        $sql_temp= "select monto_prefactura from facturacion.factura where id_factura='$id_factura'";
        $result_temp = sql ($sql_temp) or die;
        $monto=$result_temp->fields['monto_prefactura'] ?>
     <td ><?=number_format($monto,2,',','.')?></td>  
     <td ><?=number_format($result->fields['monto'],2,',','.')?></td>  
           
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>



