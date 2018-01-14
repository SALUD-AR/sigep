<?php

require_once ("../../config.php");

$id_factura=$parametros["id_factura"];
$query="
SELECT 
  *
FROM
  facturacion.debito
  left join facturacion.nomenclador using (id_nomenclador)  
  left join facturacion.motivo_d using (id_motivo_d)  
  where id_factura='$id_factura' 
  order by codigo";

$result=$db->Execute($query) or die($db->ErrorMsg());

$query="
SELECT 
  *
FROM
  facturacion.factura
  left join facturacion.smiefectores using (cuie) 
  where id_factura='$id_factura' ";

$result1=$db->Execute($query) or die($db->ErrorMsg());

echo $html_header

?>

<form name=form1 method=post action="debito_excel.php">

<table width="100%" align=center cellspacing="0" cellpadding="5" class="bordes"> 
 
 <tr>
 	<td>
 		<img src="../../imagenes/membrete_debito_credito.JPG">
 	</td>
 </tr>
 
 <tr id="sub_tabla">
 	<td>
 		<font face="arial" size="3"><b>INFORME DE PRESTACIONES</b></font>
 	</td>
 </tr>
 <tr bgcolor="#f6bebe">
 	<td>
 		<font face="arial" size="2"><b>NOTA DE DEBITOS/CREDITO</b></font>
 	</td>
 </tr>
 <tr bgcolor="#f6bebe">
 	<td>
 		<font face="arial" size="2"><b>ATTE</b></font>
 	</td>
 </tr>  
 
 <tr>
 	<td>
 		<font face="arial" size="2"><b>Por medio de la Presente le informo estado de saldo del siguiente Efector</b></font>
 	</td>
 </tr>
 
 <tr>
 	<td>
 		<font face="arial" size="2">Efector: <b><?=$result1->fields['nombreefector']?></b></font>
 	</td>
 </tr>
 <tr>
 	<td>
 		<font face="arial" size="2">Cuie: <b><?=$result1->fields['cuie']?></b></font>
 	</td>
 </tr>
 
 <tr>
 	<td>
 		<font face="arial" size="2">Numero de Factura: <b><?=$result1->fields['id_factura']?></b></font>
 	</td>
 </tr>
 
 <tr>
 	<td>
 		<font face="arial" size="2">Mes Facturado: <b><?=$result1->fields['mes_fact_d_c']?></b></font>
 	</td>
 </tr>
 
 <tr>
 	<td bgcolor="#f6bebe">
 		<font face="arial" size="2">Importe Total Facturado: <b><?=number_format($result1->fields['monto_prefactura'],2,',','.')?></b></font>
 	</td>
 </tr>
 
 <tr>
 	<td>
 		<?$query=" SELECT sum(cantidad*monto) as total FROM
  			facturacion.debito  			
  			where id_factura='$id_factura'";
			$result_t_debitado=$db->Execute($query) or die($db->ErrorMsg());?>

 		<font face="arial" size="2">Total Debitado: <b><?=number_format($result_t_debitado->fields['total'],2,',','.')?></b></font>
 	</td>
 </tr>
 
 <tr>
 	<td>
 		<?$query=" SELECT sum(cantidad*monto) as total FROM
  			facturacion.credito  			  
  			where id_factura='$id_factura'";
			$result_t_acreditado=$db->Execute($query) or die($db->ErrorMsg());?>

 		<font face="arial" size="2">Total Acreditado: <b><?=number_format($result_t_acreditado->fields['total'],2,',','.')?></b></font>
 	</td>
 </tr>
 
 <tr>
 	<td bgcolor="Gray">
 		<?
		$total_a_pagar=($result1->fields['monto_prefactura']+$result_t_acreditado->fields['total'])-$result_t_debitado->fields['total'];
		?>
 		<font face="arial" size="2">Total a Pagar: <b><?=number_format($total_a_pagar,2,',','.')?></b></font>
 	</td>
 </tr>  
 </table>
 
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor="#d3d3cd">
    <td align="center" colspan="5">DEBITO</td> 
   </tr>
  <tr bgcolor=#C0C0FF>
    <td align=right >Codigo</td>      	
    <td align=right >Cantidad</td>
    <td align=right >Valor Unit</td>
    <td align=right >Valor Total</td>
    <td align=right >Motivo</td>    
    <td align=right >Dni</td>    
    <td align=right >Apellido</td>    
    <td align=right >Nombre</td>    
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td><?=$result->fields['codigo']?></td>
     <td><?=number_format($result->fields['cantidad'],0,',','.')?></td>
     <td><?=number_format($result->fields['monto'],2,',','.')?></td> 
     <td><?=number_format($result->fields['cantidad']*$result->fields['monto'],2,',','.')?></td>    
     <td><?=$result->fields['descripcion']?></td>    
     <td><?=$result->fields['documento_deb']?></td>    
     <td><?=$result->fields['apellido_deb']?></td>    
     <td><?=$result->fields['nombre_deb']?></td>    
     
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 
 <?$query="
SELECT 
  *
FROM
  facturacion.credito
  left join facturacion.nomenclador using (id_nomenclador)  
  left join facturacion.motivo_d using (id_motivo_d)  
  where id_factura='$id_factura' 
  order by codigo";

$result=$db->Execute($query) or die($db->ErrorMsg());?>
<table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
 <tr bgcolor="#d3d3cd">
    <td align="center" colspan="5">CREDITO</td> 
 </tr> 
 <tr bgcolor=#C0C0FF>
    <td align=right >Codigo</td>      	
    <td align=right >Cantidad</td>
    <td align=right >Valor Unit</td>
    <td align=right >Valor Total</td>
    <td align=right >Motivo</td>    
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td><?=$result->fields['codigo']?></td>
     <td><?=number_format($result->fields['cantidad'],0,',','.')?></td>
     <td><?=number_format($result->fields['monto'],2,',','.')?></td> 
     <td><?=number_format($result->fields['cantidad']*$result->fields['monto'],2,',','.')?></td>    
     <td><?=$result->fields['descripcion']?></td>    
     
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>