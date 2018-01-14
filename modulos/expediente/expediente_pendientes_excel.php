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
$sql="select expediente.expediente.*,
  nacer.efe_conv.cuie,
  nacer.efe_conv.nombre,
  facturacion.factura.observaciones
from (select id_factura from expediente.expediente except
  (select numero_factura from contabilidad.ingreso where numero_factura<>0)) as ccc
inner join expediente.expediente on expediente.id_factura=ccc.id_factura
inner join nacer.efe_conv on expediente.id_efe_conv=efe_conv.id_efe_conv
inner join facturacion.factura on ccc.id_factura=factura.id_factura
where expediente.expediente.control=3";


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
    <td align=center id=mo>Observaciones</td> 
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
     <td align="center"><?=$result->fields['observaciones']?></td> 
           
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>



