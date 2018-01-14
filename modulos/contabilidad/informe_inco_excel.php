<?php

require_once ("../../config.php");

$id_factura=$parametros["id_factura"];
$query="SELECT 
		factura.id_factura,
		factura.cuie as cuie_f,
		factura.periodo as periodo_f,
		factura.monto_prefactura as monto_prefactura_f,
		factura.nro_exp as nro_exp_f,
		ingreso.cuie as cuie_d,
		ingreso.monto_prefactura as monto_prefactura_d,
		ingreso.monto_factura as monto_factura_d
FROM
  facturacion.factura
JOIN contabilidad.ingreso on  factura.id_factura = ingreso.numero_factura
  order by id_factura DESC";

$result=$db->Execute($query) or die($db->ErrorMsg());

excel_header("informe_inco_excel.xls");

?>
<form name=form1 method=post action="informe_inco_excel.php">
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
 
 <tr bgcolor="Aqua">
  	<td colspan=10 align="center">
    	<b>Facturación </b>
   </td>
   
  
  </tr>
  
  <tr bgcolor=#C0C0FF>
  	<td align=right id=mo>Nro Factura</td>      	
    <td align=right id=mo>CUIE</td>      	
    <td align=right id=mo>Efector</td>
    <td align=right id=mo>Periodo Factura</td>        
    <td align=right id=mo>Prefactura</td>    
    <td align=right id=mo>Debito</td>
    <td align=right id=mo>Credito</td>    
    <td align=right id=mo>Total a Pagar</td> 
    <td align=right id=mo>Factura</td>      
    <td align=right id=mo>Nro.Exp</td>         
         
  </tr>
  <?   
  while (!$result->EOF) {
   	$id_factura=$result->fields['id_factura'];?>  
    <tr <?=atrib_tr()?>>   
      
     <td ><?=$result->fields['id_factura']?></td> 
         
     <td ><?=$result->fields['cuie_f']?></td>
     
     <? $cuie_f=$result->fields['cuie_f'];
        $query=" SELECT nombreefector FROM facturacion.smiefectores where cuie='$cuie_f'";
		$result_efector=$db->Execute($query) or die($db->ErrorMsg());?>
     <td ><?=$result_efector->fields['nombreefector']?></td>
     
     <td ><?=$result->fields['periodo_f']?></td>
               
     <td ><?=number_format($result->fields['monto_prefactura_f'],2,',','.');?></td>      
       
     <? $query=" SELECT sum(cantidad*monto) as total FROM
  			facturacion.debito  			
  			where id_factura='$id_factura'";
		$result_t_debitado=$db->Execute($query) or die($db->ErrorMsg());?>
	<td align="center">
		<?=number_format($result_t_debitado->fields['total'],2,',','.')?>
    </td>

    <?$query=" SELECT sum(cantidad*monto) as total FROM
  		facturacion.credito  			  
  		where id_factura='$id_factura'";
		$result_t_acreditado=$db->Execute($query) or die($db->ErrorMsg());?>
	<td align="center">
		<?=number_format($result_t_acreditado->fields['total'],2,',','.')?>
 	</td>

 	<td align="center">
 		<?$total_a_pagar=($result->fields['monto_prefactura']+$result_t_acreditado->fields['total'])-$result_t_debitado->fields['total'];?>
        <b><?=number_format($total_a_pagar,2,',','.')?></b>
    </td>
        
    <?	$query_t="SELECT sum 
			(facturacion.prestacion.cantidad*facturacion.prestacion.precio_prestacion) as total
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
			  INNER JOIN facturacion.nomenclador ON (facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador)
			  INNER JOIN nacer.smiafiliados ON (facturacion.comprobante.id_smiafiliados = nacer.smiafiliados.id_smiafiliados)
			  INNER JOIN facturacion.smiefectores ON (facturacion.comprobante.cuie = facturacion.smiefectores.cuie)
			  where factura.id_factura=$id_factura";
		$total=sql($query_t,"NO puedo calcular el total");
		$total=$total->fields['total'];?>
	<td align="center">
    	<font color="Blue"><?=number_format($total,2,',','.');?></font>
    </td>   
       
    <td ><?=$result->fields['nro_exp_f']?></td>    
              
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>