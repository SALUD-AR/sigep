<?php

require_once ("../../config.php");

$id_factura=$parametros["id_factura"];
$user=$_ses_user['login'];
 if (es_cuie($user)) {

        $query="SELECT 
          *
        FROM
          facturacion.factura LEFT JOIN (select id_factura,fecha_ing from expediente.expediente) as expediente USING (id_factura)
          LEFT JOIN facturacion.smiefectores using (cuie)
          where cuie='$user'
          order by id_factura DESC";

        $result=$db->Execute($query) or die($db->ErrorMsg());
      }
  else {
       $query="SELECT 
          *
        FROM
          facturacion.factura LEFT JOIN (select id_factura,fecha_ing from expediente.expediente) as expediente USING (id_factura)
          LEFT JOIN facturacion.smiefectores using (cuie)
          order by id_factura DESC";
         $result=$db->Execute($query) or die($db->ErrorMsg());


  }

excel_header("informe_facturacion_excel.xls");

?>
<form name=form1 method=post action="informe_facturacion_excel.php">
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
  	<td align=right id=mo>Nro Factura</td>      	
    <td align=right id=mo>Estado</td>      	
    <td align=right id=mo>CUIE</td>      	
    <td align=right id=mo>Efector</td>
    <td align=right id=mo>Periodo Proc</td>  
    <td align=right id=mo>Periodo Factura</td>      
    <td align=right id=mo>Fecha Ingreso (Exped.)</td> 
	<td align=right id=mo>Fecha Factura</td>     
    <td align=right id=mo>Prefactura</td>    
    <td align=right id=mo>Debito</td>
    <td align=right id=mo>Credito</td>    
    <td align=right id=mo>Total a Pagar</td> 
    <td align=right id=mo>Factura</td>   
    <td align=right id=mo>Fecha Control</td>    
    <td align=right id=mo>Nro.Exp</td>    
    <td align=right id=mo>usuario</td>    
  </tr>
  <?
   while (!$result->EOF) {
   	$id_factura=$result->fields['id_factura'];?>
  
    <tr <?=atrib_tr()?>>     
     <td ><?=$result->fields['id_factura']?></td>
     <td ><?=$result->fields['estado']?></td>
     <td ><?=$result->fields['cuie']?></td>
     <td ><?=$result->fields['nombreefector']?></td>
     <td ><?=$result->fields['mes_fact_d_c']?></td>
     <td ><?=$result->fields['periodo']?></td>
     <td ><?=fecha($result->fields['fecha_ing'])?></td> 
	 <td ><?=fecha($result->fields['fecha_factura'])?></td>          
     <td ><?=number_format($result->fields['monto_prefactura'],2,',','.');?></td>      
       <?
        $query=" SELECT sum(cantidad*monto) as total FROM
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
        
        <?
     	
     	$query_t="SELECT sum 
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
       <?(number_format($total_a_pagar,2,',','.')==number_format($total,2,',','.'))?$bg_color="":$bg_color="FF9999";
		?>
       <td align="center" bgcolor="<?=$bg_color?>">
       		<font color="Blue"><?=number_format($total,2,',','.');?></font>
       </td>
       
       <td ><?=fecha($result->fields['fecha_control'])?></td> 
       <td ><?=$result->fields['nro_exp']?></td> 
       <?
       $query_1="SELECT usuario FROM facturacion.factura
       				left join facturacion.log_factura using (id_factura)
       			where factura.id_factura=$id_factura and tipo ='ALTA'";
		$total1=sql($query_1,"NO puedo calcular el total");?>
       
       <td ><?=$total1->fields['usuario']?></td> 
       
       
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>