<?php

require_once("../../config.php");

variables_form_busqueda("informe_inco");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

if ($cmd == "")  $cmd="A";

$orden = array(
        "default" => "1",
        "default_up" => "0",
        "1" => "id_factura",
        "2" => "factura.cuie",       
       );
$filtro = array(
		"id_factura" => "Nro. Factura",
		"factura.cuie" => "CUIE", 		
       );

$sql_tmp="SELECT 
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
JOIN contabilidad.ingreso on  factura.id_factura = ingreso.numero_factura ";


echo $html_header;
?>
<form name=form1 action="informe_inco.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;
	    <? $link=encode_link("informe_inco_excel.php",array());?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">
	  	    
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=20 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr> 
  	
  <tr>
  	<td colspan=10 align=left id=mo>
    	Facturación 
   </td>
   
  </tr>
  
  <tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("informe_inco.php",array("sort"=>"1","up"=>$up))?>'>Nro Factura</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("informe_inco.php",array("sort"=>"2","up"=>$up))?>'>CUIE</a></td>      	
    <td align=right id=mo>Efector</td>
    <td align=right id=mo>Periodo Factura</td>        
    <td align=right id=mo title="Va el Monto de la Prefactura que realizo el Efector">Prefactura</td>    
    <td align=right id=mo title="Total del Debito Cargado en el Sistema">Debito</td>
    <td align=right id=mo title="Total del Credito Cargado en el Sistema">Credito</td>    
    <td align=right id=mo title="Suma del Monto de la prefactura - Los Debitos + Los Creditos">Total a Pagar</td> 
    <td align=right id=mo title="Es el Monto por el que Imprime la Factura (Deberia coincidir con el campo anterior)">Factura</td>   
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
 		<?$total_a_pagar=($result->fields['monto_prefactura_f']+$result_t_acreditado->fields['total'])-$result_t_debitado->fields['total'];?>
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

<br>
	<table align='center' border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='80%' cellspacing=0 cellpadding=0>
     <tr>
      <td colspan=10 bordercolor='#FFFFFF'><b>Colores de Referencia para las Columnas CUIE, Monto Factura y Monto Prefactura:</b></td>      
     <tr>
     <tr>
     <td colspan=10 bordercolor='#FFFFFF'>Se listan las facturas: el listado esta dividido en dos, administrativa y facturacion. Vincula las facturas por NUMERO DE FACTURA</td>
     </tr>
     <td width=30% bordercolor='#FFFFFF'>
      <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
       <tr>
       	<td>
       	 &nbsp;
       	</td>
       </tr>
       <tr>        
        <td width=30 bgcolor='FF9999' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>No Son Iguales (Deben Coincidir Monto Prefactura de Facturacion con Monto Prefactura de Contabilidad - Monto del Campo Factura de Facturacion con Monto Factura de Contabilidad)</td>
       </tr>
       <tr>
       	<td>
       	 &nbsp;
       	</td>
       </tr>
       
      </table>
     </td>
    </table>
    
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>