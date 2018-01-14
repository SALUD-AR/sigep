<?php
/*
Author: sebastian lohaiza

modificada por
$Author: seba $
$Revision: 1.30 $
$Date: 2009/11/01 18:25:40 $
*/
require_once("../../config.php");



$sql="SELECT * FROM expediente.expediente left join nacer.efe_conv using (id_efe_conv) where control=2";

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
    <td align=right id=mo>Debito</td>
    <td align=right id=mo>Credito</td>
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
        $sql_tp="select monto_prefactura from facturacion.factura where id_factura='$id_factura'";
        $result_tp=sql($sql_tp) or die;
        $monto_prefactura=$result_tp->fields['monto_prefactura'] ?>    
  
   <td ><?=number_format($monto_prefactura,2,',','.')?></td> 
  
  
  <? $id_factura= $result->fields['id_factura'];
          $sql_debito="SELECT * FROM facturacion.debito WHERE id_factura='$id_factura'";
					$result_debito = sql($sql_debito) or die;
					$debito=0;
					while (!$result_debito -> EOF) {
					$debito=$debito+($result_debito->fields['monto'] * $result_debito->fields['cantidad']) ;
					$result_debito->MoveNext();
					};
					if (!$debito) $debito=0;?>
     <td ><?=number_format($debito,2,',','.')?></td>  
     
     <?$sql_credito="SELECT * FROM facturacion.credito WHERE id_factura='$id_factura'";
					$result_credito = sql($sql_credito) or die;
					$credito=0;
					while (!$result_credito -> EOF) {
					$credito=$credito+($result_credito->fields['monto'] * $result_credito->fields['cantidad']);
					$result_credito->MoveNext();
					};
					if (!$credito) $credito=0;?>  
	<td ><?=number_format($credito,2,',','.')?></td>
	<?$monto_pago=$monto_prefactura-$debito+$credito ?>	
	<td ><?=number_format($monto_pago,2,',','.')?></td>			
           
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>



