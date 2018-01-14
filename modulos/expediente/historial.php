<?php
/*
Author: sebastian lohaiza

modificada por
$Author: seba $
$Revision: 1.30 $
$Date: 2009/11/01 18:25:40 $
*/
require_once("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);



variables_form_busqueda("listado_exp_hist");
cargar_calendario();

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);


$orden = array(
        "default" => "1",
        "default_up" => "0",
        "1" => "id_expediente",
        "2" => "id_factura",
        "3" => "id_efe_conv",
        "4" => "fecha_ing"
        
       );
$filtro = array(
        "to_char(id_factura,'999999')" => "Nro. Factura",
		"cuie" => "CUIE",
        "nro_exp" => "nro_exp",
		"nombre" => "Nomb.Efector"               
       );

$sql_tmp="Select * 
			from expediente.expediente
			left join nacer.efe_conv using (id_efe_conv)";


$fecha_desde=fecha_db($_POST['fecha_desde']);
$fecha_hasta=fecha_db($_POST['fecha_hasta']);
if ($fecha_desde!="" && $fecha_hasta!=""){
$selec_fecha=$_POST['selec_fecha'];

if ($selec_fecha=='expediente.fecha_ing'){ $criterio_filtro="Fecha Ingreso al Sistema de Expediente";
	   $where_tmp="fecha_ing between '$fecha_desde' and '$fecha_hasta' and estado<>'E' and estado<>'R'"; 	
	}
if ($selec_fecha=='expediente.plazo_para_pago'){ $criterio_filtro="Fecha de Pago de Acuerdo a Expediente->plazo_para_pago";
		$where_tmp="plazo_para_pago between '$fecha_desde' and '$fecha_hasta' and estado<>'E' and estado<>'R'";
		}
}
 

echo $html_header;
?>
<form name=form1 action="historial.php" method=POST>

<table cellspacing=2 cellpadding=2 border=1 width=100% align=center>
     <tr>
      <td align=center>
		&nbsp;&nbsp;&nbsp;
        <?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp; 
	      
	  </td>
     </tr>
</table>
<table cellspacing=2 cellpadding=2 border=1 width=100% align=center>
     <tr>
      <td align=center>
		<b>			
		Desde: <input type=text id=fecha_desde name=fecha_desde value='<?=fecha($fecha_desde)?>' size=15 readonly>
		<?=link_calendario("fecha_desde");?>
		
		Hasta: <input type=text id=fecha_hasta name=fecha_hasta value='<?=fecha($fecha_hasta)?>' size=15 readonly>
		<?=link_calendario("fecha_hasta");?> 
		
		&nbsp;&nbsp;&nbsp;
		<select name='selec_fecha'>
		  <option value="expediente.fecha_ing" <?if ($selec_fecha=='expediente.fecha_ing')echo 'selected';?>>Fecha Ingreso Sistema Expediente</option>
		  <option value="expediente.plazo_para_pago" <?if ($selec_fecha=='expediente.plazo_para_pago')echo 'selected';?>>Fecha de plazo para Pago</option>	  
		</select>    
	    
	    &nbsp;&nbsp;&nbsp;
	    <input type=submit name="buscar" value='Buscar'> 
	    <? 
	     $link=encode_link("expediente_excel.php",array("sql"=>$sql));?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')"> 
	    </b>
	        
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
       
		<?$ref_1 = encode_link("historial_1.php",array());
		$onclick_elegir="location.href='$ref_1'";?>
		<tr>
		<td onclick="<?=$onclick_elegir?>" style='cursor:hand;' width=30 bgcolor='#CFE8DD' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Expediente con fecha para pago mayor de 30 dias</td>
        </tr>
        <tr>
        <?$ref_4 = encode_link("historial_4.php",array());
		$onclick_elegir="location.href='$ref_4'";?>
		
		<td onclick="<?=$onclick_elegir?>" style='cursor:hand;' width=30 bgcolor='#F3F781' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Expediente con fecha para pago menor de 29 dias</td>
        </tr>
        <tr>
        <?$ref_2= encode_link("historial_2.php",array());
		$onclick_elegir="location.href='$ref_2'";?>
		
		<td onclick="<?=$onclick_elegir?>" style='cursor:hand;' width=30 bgcolor='#F78181' bordercolor='#000000' height=30>&nbsp;</td>
		<td bordercolor='#FFFFFF'>Expediente con fecha para pago menor de 10 dias</td>
        </tr>
       </table>
       </td>
       <td>
       <table style="float:right" border=2 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
       <td colspan=10 bordercolor='#FFFFFF'><b>Colores de Referencia para Estado Interno:</b></td>
       <tr>
       <?$ref_aceptados = encode_link("historial_aceptados.php",array());
		$onclick_elegir="location.href='$ref_aceptados'";?>
		
		<td onclick="<?=$onclick_elegir?>" style='cursor:hand;' width=30 bgcolor='#81BEF7' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Expedientes Aceptados </td>
       
       <?$ref_6 = encode_link("historial_6.php",array());
		$onclick_elegir="location.href='$ref_6'";?>
		
		<td onclick="<?=$onclick_elegir?>" style='cursor:hand;' width=30 bgcolor='#CFE8DD' bordercolor='#000000' height=30>&nbsp;</td>
		<td bordercolor='#FFFFFF'>Expedientes en Verificacion</td>
		
       </tr>
       <tr>
       <?$ref_derivados = encode_link("historial_derivados.php",array());
		$onclick_elegir="location.href='$ref_derivados'";?>
		
        <td onclick="<?=$onclick_elegir?>" style='cursor:hand;' width=30 bgcolor='#F5D0A9' bordercolor='#000000' height=30>&nbsp;</td>
		<td bordercolor='#FFFFFF'>Expedientes Derivados</td>
       
       <?$ref_3 = encode_link("historial_3.php",array());
		$onclick_elegir="location.href='$ref_3'";?>
		
		<td onclick="<?=$onclick_elegir?>" style='cursor:hand;' width=30 bgcolor='F5A1F1' bordercolor='#000000' height=30>&nbsp;</td>
		<td bordercolor='#FFFFFF'>Expediente en condicion de "Rechazo"</td>
		
       </tr>
       <tr>
       <?$ref_5 = encode_link("historial_5.php",array());
		$onclick_elegir="location.href='$ref_5'";?>
		
        <td onclick="<?=$onclick_elegir?>" style='cursor:hand;' width=30 bgcolor='D7D5FA' bordercolor='#000000' height=30>&nbsp;</td>
		<td bordercolor='#FFFFFF'>Expediente Pago</td>
       <?$ref_6 = encode_link("historial_6.php",array());
		$onclick_elegir="location.href='$ref_6'";?>
		
		<td onclick="<?=$onclick_elegir?>" style='cursor:hand;' width=30 bgcolor='FC5D4F' bordercolor='#000000' height=30>&nbsp;</td>
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
    <td align=right id=mo><a id=mo href='<?=encode_link("historial.php",array("sort"=>"1","up"=>$up))?>'>Numero de Expediente</a></td>
     <td align=right id=mo><a id=mo href='<?=encode_link("historial.php",array("sort"=>"3","up"=>$up))?>'>Nombre del Efector</a></td> 
     <td align=right id=mo><a id=mo href='<?=encode_link("historial.php",array("sort"=>"3","up"=>$up))?>'>CUIE</a></td>   
     <td align=right id=mo><a id=mo href='<?=encode_link("historial.php",array("sort"=>"2","up"=>$up))?>'>Nro.Factura</a></td>	 
    <td align=right id=mo><a id=mo href='<?=encode_link("historial.php",array("sort"=>"4","up"=>$up))?>'>Fecha de Ingreso</a></td> 
    <td align=right id=mo><a id=mo href='<?=encode_link("historial.php",array("sort"=>"3","up"=>$up))?>'>Fecha de Plazo</a></td>
    <td align=right id=mo><a id=mo>Periodo</a></td> 
    <td align=right id=mo><a id=mo>Monto Prefactura</a></td>
    <td align=right id=mo><a id=mo>Monto a Pagar</a></td>
   
   
     
   
  </tr>


<?
    $fecha_hoy=date("Y-m-d");
	while (!$result->EOF) {
   	
   	$ref = encode_link("historial_det.php",array("id_expediente"=>$result->fields['id_expediente']));
    $onclick_elegir="location.href='$ref'";
    $id_expediente=$result->fields['id_expediente'];
     
    
    $plazo=$result->fields['plazo_para_pago'];
    $plazo_30=date("Y-m-d", strtotime ("$plazo -30 days"));
    $plazo_40=date("Y-m-d", strtotime ("$plazo -10 days"));
    switch ($result->fields['estado']){
    	case 'V':$tr=atrib_tr();break;
    	case 'A':$tr=atrib_tr6();break;
    	case 'C':$tr=atrib_tr3();break;
    	case 'R':$tr=atrib_tr4();break;
    	case 'E':$tr=atrib_tr5();break;
    	case 'D':$tr=atrib_tr7();break;
      } 
   	?>
  
    <tr>     
      <td <?=$tr?> onclick="<?=$onclick_elegir?>"><?=$result->fields['nro_exp']?></td>
      
   	<?switch ($result->fields['estado']){
    	case 'C':$tr=atrib_tr3();break;
    	case 'R':$tr=atrib_tr4();break;
    	case 'E':$tr=atrib_tr5();break;
    	default:{if ($fecha_hoy<$plazo_30) $tr=atrib_tr();
    			  elseif ($fecha_hoy>$plazo_30 && $fecha_hoy<=$plazo_40) $tr=atrib_tr1();
    			  elseif ($fecha_hoy>$plazo_40) $tr=atrib_tr2();break;
    	}
      } 
   	   ?>
    			  
      <td <?=$tr?> onclick="<?=$onclick_elegir?>" ><?=$result->fields['nombre']?></td>
      <td <?=$tr?> onclick="<?=$onclick_elegir?>"><?=$result->fields['cuie']?></td> 
	  <?$nro_factura=$result->fields['id_factura']?>
	  <td <?=$tr?> onclick="<?=$onclick_elegir?>" align="center"><?=$nro_factura?></td> 
     <td <?=$tr?> onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['fecha_ing']?></td>
     <td <?=$tr?> onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['plazo_para_pago']?></td>  
     <td <?=$tr?> onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['periodo']?></td>   
     <? $consulta_prefact="select monto_prefactura from facturacion.factura where id_factura='$nro_factura'";
        $result_prefact=sql($consulta_prefact) or die;
         ?>
     <td <?=$tr?> onclick="<?=$onclick_elegir?>">$<?=number_format($result_prefact->fields['monto_prefactura'],2,',','.')?></td>
     <td <?=$tr?> onclick="<?=$onclick_elegir?>">$<?=number_format($result->fields['monto'],2,',','.')?></td>
     
    
    </tr>    
    </tr>
	<?$result->MoveNext();
    }?>
    
	
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>