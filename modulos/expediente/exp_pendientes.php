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


/*$sql_tmp="select * from (
select id_factura from expediente.expediente except select numero_factura from contabilidad.ingreso where numero_factura<>0 ) as tabla left join 
expediente.expediente using (id_factura) left join nacer.efe_conv using (id_efe_conv)";*/

$sql_tmp="select expediente.expediente.*,
  nacer.efe_conv.cuie,
  nacer.efe_conv.nombre,
  facturacion.factura.observaciones
from (select id_factura from expediente.expediente except
  (select numero_factura from contabilidad.ingreso where numero_factura<>0)) as ccc
inner join expediente.expediente on expediente.id_factura=ccc.id_factura
inner join nacer.efe_conv on expediente.id_efe_conv=efe_conv.id_efe_conv
inner join facturacion.factura on ccc.id_factura=factura.id_factura";

$where_tmp="expediente.expediente.control=3";


echo $html_header;
?>
<form name=form1 action="exp_pendientes.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>  
	     <? $link=encode_link("expediente_pendientes_excel.php",array());?>
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
  	<td colspan=10 align=left id="ma">
     <table width=100%>
      <tr id="ma">
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
<tr>
   <td align=right id="mo"><a id="mo">Numero de Expediente</a></td>
   <td align=right id="mo"><a id="mo" >Nombre del Efector</a></td>
   <td align=right id="mo"><a id="mo" >CUIE</a></td> 
   <td align=center id="mo"><a id="mo" >Numero de Factura</a></td>     	
   <td align=right id="mo"><a id="mo" >Fecha de Ingreso</a></td>
   <td align=right id="mo"><a id="mo" >Plazo para Pago</a></td>      	
   <td align=right id="mo"><a id="mo" >Periodo</a></td>
   <td align=right id="mo"><a id="mo" >Monto</a></td>
   <td align=right id="mo"><a id="mo" >debito</a></td>
   <td align=right id="mo"><a id="mo" >credito</a></td>
   <td align=right id="mo"><a id="mo">total</a></td>
   <td align=right id="mo"><a id="mo">Observaciones</a></td>
   <td align=right id="mo"><a id="mo">Caratula</a></td>
     
   
  </tr>


<?
 
$fecha_hoy=date("Y-m-d");
while (!$result->EOF) {
   	
   	$ref = encode_link("mod_exp_externo.php",array("nro_exp"=>$result->fields['nro_exp'],"id_expediente_fact"=>$result->fields['id_factura'],"id_expediente"=>$result->fields['id_expediente'],"nombre_efector"=>$result->fields['nombre'],"fecha_ing"=>$result->fields['fecha_ing'],"monto"=>$result->fields['monto'],"estado_exp"=>$result->fields['estado']));
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
     <?$estado=$result->fields['estado'];
      switch ($estado){
      	case "A":$tr=atrib_tr6();break;
      	case "D":$tr=atrib_tr7();break;
      	}?>
     
     <td <?=$tr?> onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>
     <td <?=$tr?> onclick="<?=$onclick_elegir?>" align="center" ><?=$result->fields['cuie']?></td> 
     <?$nro_factura=$result->fields['id_factura']?>
     <td <?=$tr?> onclick="<?=$onclick_elegir?>" align="center" ><?=$nro_factura?></td>
     <td <?=$tr?> onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['fecha_ing']?></td>
     <td <?=$tr?> onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['plazo_para_pago']?></td>
     <td <?=$tr?> onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['periodo']?></td>
     
     <? $consulta_prefact="select monto_prefactura from facturacion.factura where id_factura='$nro_factura'";
        $result_prefact=sql($consulta_prefact) or die;
         ?>
     <td <?=$tr?> onclick="<?=$onclick_elegir?>">$<?=number_format($result_prefact->fields['monto_prefactura'],2,',','.')?></td>
     <?$sql_debito="SELECT * FROM facturacion.debito WHERE id_factura='$nro_factura'";
		$result_debito = sql($sql_debito) or die;
		$debito=0;
		while (!$result_debito -> EOF) {
		$debito=$debito+($result_debito->fields['monto'] * $result_debito->fields['cantidad']) ;
		$result_debito->MoveNext();
		};
		if (!$debito) $debito=0 ?>
     
     <td <?=$tr?> onclick="<?=$onclick_elegir?>">$<?=number_format($debito,2,',','.')?></td>
     
     <?$sql_credito="SELECT * FROM facturacion.credito WHERE id_factura='$nro_factura'";
		$result_credito = sql($sql_credito) or die;
		$credito=0;
		while (!$result_credito -> EOF) {
		$credito=$credito+($result_credito->fields['monto'] * $result_credito->fields['cantidad']) ;
		$result_credito->MoveNext();
		};
		if (!$credito) $credito=0 ?>
     
     <td <?=$tr?> onclick="<?=$onclick_elegir?>">$<?=number_format($credito,2,',','.')?></td>
     <? $monto_pago=$result_prefact->fields['monto_prefactura']+$credito-$debito;
        $sql_update="update expediente.expediente set monto='$monto_pago' where id_expediente='$id_expediente'";
        sql($sql_update) or die;   
     ?>
     <td <?=$tr?> onclick="<?=$onclick_elegir?>">$<?=number_format($monto_pago,2,',','.')?></td>
     <td <?=$tr?> onclick="<?=$onclick_elegir?>"><?=$result->fields['observaciones']?></td>
      
      <td <?=$tr?>>
     <?$link=encode_link("caratula2_pdf.php", array("nro_exp"=>$result->fields['nro_exp'],"nombre"=>$result->fields['nombre'],"fecha_ing"=>$result->fields['fecha_ing'],"periodo"=>$result->fields['periodo'],"monto"=>number_format($result_prefact->fields['monto_prefactura'],2,'.',''),"plazo_pago"=>$result->fields['plazo_para_pago'],"id_factura"=>$result->fields['id_factura'],"monto_pago"=>number_format($monto_pago,2,'.','')));
        echo "<a target='_blank' href='".$link."' title='Reimpresion de Caratula'><IMG src='$html_root/imagenes/pdf_logo.gif' aling = center height='20' width='20' border='0'></a>";?>   
      </td>      
    </tr>    
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>