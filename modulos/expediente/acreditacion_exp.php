<?php
/*
Author: sebastian lohaiza

modificada por
$Author: seba $
$Revision: 1.30 $
$Date: 2009/11/01 18:25:40 $
*/
require_once("../../config.php");



variables_form_busqueda("listado_fact");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);


$orden = array(
        "default" => "1",
        "default_up" => "0",
        "1" => "id_expediente",
        "2" => "monto",
        "3" => "id_efe_conv"
       );
$filtro = array(
		"to_char(id_factura,'999999')" => "Nro. Factura",
        "cuie" => "CUIE",
        "nro_exp" => "nro_exp",
		"nombre" => "Nomb.Efector"               
       );

//$sql_tmp="SELECT * FROM expediente.expediente left join nacer.efe_conv using (id_efe_conv)";
$sql_tmp="select * from (
select * from (select id_expediente from expediente.expediente where control=4) as tabla1 left join (select id_expediente,num_tranf
 from expediente.transaccion where num_tranf is not null) as tabla2 using (id_expediente)) as tabla3 left join expediente.expediente
 using (id_expediente) left join nacer.efe_conv using (id_efe_conv)";
 $where_tmp="";

//$sql_tmp="SELECT * FROM expediente.transaccion left join nacer.efe_conv using (id_efe_conv) left join expediente.expediente using (id_expediente)";


echo $html_header;
?>
<form name=form1 action="acreditacion_exp.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>  
	     <? $link=encode_link("expediente_acred_excel.php",array());?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">  
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>

<table align='center' border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='95%' cellspacing=0 cellpadding=0>
     <tr>
      <td align='center' border=2 colspan=10 bordercolor='#FFFFFF'><b>Colores de Referencia para los Expedientes</b></td>
     <tr>
     <td width=30% bordercolor='#FFFFFF'>
      <table border=2 bordercolor='#FFFFFF' bgcolor='#BCDECF' cellspacing=0 cellpadding=0 width=100%>
       	<tr>
        <td>
        <table style="float:left" border=2 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=60%>
        <td colspan=10 bordercolor='#FFFFFF'><b>Colores de Referencia Para la Medicion de Tiempo de Plazo de Pago:</b></td>
       
		<tr>
		<td width=30 bgcolor='#CFE8DD' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Expediente con fecha para pago mayor de 30 dias</td>
        </tr>
        <tr>
        
		<td width=30 bgcolor='#F3F781' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Expediente con fecha para pago menor de 29 dias</td>
        </tr>
        <tr>
       
		<td width=30 bgcolor='#F78181' bordercolor='#000000' height=30>&nbsp;</td>
		<td bordercolor='#FFFFFF'>Expediente con fecha para pago menor de 10 dias</td>
        </tr>
       </table>
       </td>
       <td>
       <table style="float:right" border=2 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
       <td colspan=10 bordercolor='#FFFFFF'><b>Colores de Referencia para Estado Interno:</b></td>
       <tr>
       		
		<td width=30 bgcolor='#81BEF7' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Expedientes Aceptados </td>
       
       
		<td width=30 bgcolor='#CFE8DD' bordercolor='#000000' height=30>&nbsp;</td>
		<td bordercolor='#FFFFFF'>Expedientes en Verificacion</td>
		
       </tr>
       <tr>
      	
        <td width=30 bgcolor='#F5D0A9' bordercolor='#000000' height=30>&nbsp;</td>
		<td bordercolor='#FFFFFF'>Expedientes Derivados</td>
       
        <td width=30 bgcolor='F5A1F1' bordercolor='#000000' height=30>&nbsp;</td>
		<td bordercolor='#FFFFFF'>Expediente en condicion de "Rechazo"</td>
		
       </tr>
       <tr>
       
        <td width=30 bgcolor='D7D5FA' bordercolor='#000000' height=30>&nbsp;</td>
		<td bordercolor='#FFFFFF'>Expediente Pago</td>
       	
		<td width=30 bgcolor='FC5D4F' bordercolor='#000000' height=30>&nbsp;</td>
		<td bordercolor='#FFFFFF'>Expediente en condicion de "Error"</td>
       
       </tr>
       
       </table>
       </td>
       </tr>       
</table>
	  
<table border=1 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
<tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("acreditacion_exp.php",array("sort"=>"1","up"=>$up))?>'>Numero de Expediente</a></td>
     <td align=right id=mo><a id=mo href='<?=encode_link("acreditacion_exp.phpp",array("sort"=>"6","up"=>$up))?>'>Nombre del Efector</a></td>
     <td align=right id=mo><a id=mo href='<?=encode_link("acreditacion_exp.php",array("sort"=>"6","up"=>$up))?>'>CUIE</a></td> 
     <td align=center id=mo><a id=mo href='<?=encode_link("acreditacion_exp.php",array("sort"=>"6","up"=>$up))?>'>Numero de Factura</a></td>     	
    <td align=right id=mo><a id=mo href='<?=encode_link("acreditacion_exp.php",array("sort"=>"2","up"=>$up))?>'>Fecha de Ingreso</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("acreditacion_exp.php",array("sort"=>"2","up"=>$up))?>'>Plazo para Pago</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("acreditacion_exp.php",array("sort"=>"2","up"=>$up))?>'>Periodo</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("acreditacion_exp.php",array("sort"=>"2","up"=>$up))?>'>Monto</a></td>
     <td align=right id=mo><a id=mo href='<?=encode_link("acreditacion_exp.php",array("sort"=>"2","up"=>$up))?>'>debito</a></td>
      <td align=right id=mo><a id=mo href='<?=encode_link("acreditacion_exp.php",array("sort"=>"2","up"=>$up))?>'>credito</a></td>
       <td align=right id=mo><a id=mo href='<?=encode_link("acreditacion_exp.php",array("sort"=>"2","up"=>$up))?>'>total</a></td>
       <td align=right id=mo><a id=mo href='<?=encode_link("acreditacion_exp.php",array("sort"=>"2","up"=>$up))?>'>Nro.Exped.Externo</a></td>
     
   
  </tr>


<?
 
$fecha_hoy=date("Y-m-d");
while (!$result->EOF) {
   	
   	$ref = encode_link("mod_acred_exp.php",array("nro_exp"=>$result->fields['nro_exp'],"id_expediente_fact"=>$result->fields['id_factura'],"id_expediente"=>$result->fields['id_expediente'],"nombre_efector"=>$result->fields['nombre'],"fecha_ing"=>$result->fields['fecha_ing'],"monto"=>$result->fields['monto']));
    $onclick_elegir="location.href='$ref'";
   	
    $plazo=$result->fields['plazo_para_pago'];
    $plazo_30=date("Y-m-d", strtotime ("$plazo -30 days"));
    $plazo_40=date("Y-m-d", strtotime ("$plazo -10 days"));
    if ($fecha_hoy<=$plazo_30) $tr=atrib_tr();
    if ($fecha_hoy>$plazo_30 && $fecha_hoy<=$plazo_40) $tr=atrib_tr1();
    if  ($fecha_hoy>$plazo_40) $tr=atrib_tr2();  
   	
    $id_expediente=$result->fields['id_expediente'];
    $sql_montos="SELECT * FROM expediente.transaccion WHERE id_expediente=$id_expediente";
    $result_monto = sql($sql_montos) or die;
    ?>
  
    <tr <?=$tr?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nro_exp']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>
     <td onclick="<?=$onclick_elegir?>" align="center" ><?=$result->fields['cuie']?></td> 
     <?$nro_factura=$result->fields['id_factura']?>
     <td onclick="<?=$onclick_elegir?>" align="center" ><?=$nro_factura?></td>
     <td onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['fecha_ing']?></td>
     <td onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['plazo_para_pago']?></td>
     <td onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['periodo']?></td>
     
     <? $consulta_prefact="select monto_prefactura from facturacion.factura where id_factura='$nro_factura'";
        $result_prefact=sql($consulta_prefact) or die;
         ?>
     <td onclick="<?=$onclick_elegir?>">$<?=number_format($result_prefact->fields['monto_prefactura'],2,',','.')?></td>
     <?$sql_debito="SELECT * FROM facturacion.debito WHERE id_factura='$nro_factura'";
		$result_debito = sql($sql_debito) or die;
		$debito=0;
		while (!$result_debito -> EOF) {
		$debito=$debito+($result_debito->fields['monto'] * $result_debito->fields['cantidad']) ;
		$result_debito->MoveNext();
		};
		if (!$debito) $debito=0 ?>
     
     <td onclick="<?=$onclick_elegir?>">$<?=number_format($debito,2,',','.')?></td>
     
     <?$sql_credito="SELECT * FROM facturacion.credito WHERE id_factura='$nro_factura'";
		$result_credito = sql($sql_credito) or die;
		$credito=0;
		while (!$result_credito -> EOF) {
		$credito=$credito+($result_credito->fields['monto'] * $result_credito->fields['cantidad']) ;
		$result_credito->MoveNext();
		};
		if (!$credito) $credito=0 ?>
     
     <td onclick="<?=$onclick_elegir?>">$<?=number_format($credito,2,',','.')?></td>
     <? $monto_pago=$result_prefact->fields['monto_prefactura']+$credito-$debito;?>
     <td onclick="<?=$onclick_elegir?>">$<?=number_format($monto_pago,2,',','.')?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['num_tranf']?></td>
         
            
    </tr>    
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>