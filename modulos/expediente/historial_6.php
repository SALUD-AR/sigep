<?php
/*
Author: sebastian lohaiza

modificada por
$Author: seba $
$Revision: 1.30 $
$Date: 2009/11/01 18:25:40 $
*/
require_once("../../config.php");



variables_form_busqueda("listado_hist_6");

//$fecha_hoy=date("Y-m-d H:i:s");
//$fecha_hoy=fecha($fecha_hoy);
$fecha_hoy=date("Y-m-d");
$plazo_30=date("Y-m-d", strtotime ("$fecha_hoy +30 days"));

$orden = array(
        "default" => "1",
        "default_up" => "0",
        "1" => "id_expediente",
        "2" => "monto",
        "3" => "debito",
        "4" => "credito",
        "5" => "num_tranf",
        "6" => "id_efe_conv"
       );
$filtro = array(
        "to_char(id_factura,'999999')" => "Nro. Factura",
		"cuie" => "CUIE",
        "nro_exp" => "nro_exp",
		"nombre" => "Nomb.Efector"               
       );

$sql_tmp="select * from (
select * from (
select * from expediente.expediente where estado='E' ) as expediente
left join (select id_expediente,fecha_mov,estado as est_tran from expediente.transaccion) as transaccion 
using (id_expediente) where est_tran='E')as completo
left join (select id_efe_conv,nombre,cuie from nacer.efe_conv) as efector using (id_efe_conv)";

//$sql_tmp="SELECT * FROM expediente.transaccion left join nacer.efe_conv using (id_efe_conv) left join expediente.expediente using (id_expediente)";


echo $html_header;
?>
<form name=form1 action="historial_6.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>  
	     
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>
<table align='center' border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='95%' cellspacing=0 cellpadding=0>
     <tr>
      <td colspan=10 bordercolor='#FFFFFF'><b>Colores de Referencia para los Expedientes:</b></td>
     <tr>
     <td width=30% bordercolor='#FFFFFF'>
      <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
       <tr>
        <td onclick="<?=$onclick_elegir?>" style='cursor:hand;' width=30 bgcolor='FC5D4F' bordercolor='#000000' height=30>&nbsp;</td>
		<td bordercolor='#FFFFFF'>Expediente en condicion de "Error"</td>
	 </tr>       
</table>
	  
<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
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
    <td align=right id=mo><a id=mo href='<?=encode_link("historial_det.php",array("sort"=>"1","up"=>$up))?>'>Numero de Expediente</a></td>
     <td align=right id=mo><a id=mo href='<?=encode_link("historial_det.php",array("sort"=>"6","up"=>$up))?>'>Nombre del Efector</a></td> 
     <td align=right id=mo><a id=mo href='<?=encode_link("historial_det.php",array("sort"=>"6","up"=>$up))?>'>CUIE</a></td>
     <td align=right id=mo><a id=mo href='<?=encode_link("historial_det.php",array("sort"=>"6","up"=>$up))?>'>Nro.Factura</a></td>	 
    <td align=right id=mo><a id=mo href='<?=encode_link("historial_det.php",array("sort"=>"2","up"=>$up))?>'>Fecha de Ingreso</a></td> 
    <td align=right id=mo><a id=mo href='<?=encode_link("historial_det.php",array("sort"=>"3","up"=>$up))?>'>Fecha de Plazo</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("historial_det.php",array("sort"=>"6","up"=>$up))?>'>Periodo</a></td> 
    <td align=right id=mo><a id=mo href='<?=encode_link("historial_det.php",array("sort"=>"2","up"=>$up))?>'>Monto</a></td>
   
   
     
   
  </tr>


<?
    
	while (!$result->EOF) {
   	
   	$ref = encode_link("historial_det.php",array("id_expediente"=>$result->fields['id_expediente']));
    $onclick_elegir="location.href='$ref'";
    
     
    $tr=atrib_tr5();?>
  
    <tr <?=$tr?>>     
      <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nro_exp']?></td>
      <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>
      <td onclick="<?=$onclick_elegir?>"><?=$result->fields['cuie']?></td> 
	  <td onclick="<?=$onclick_elegir?>"align="center"><?=$result->fields['id_factura']?></td> 
     <td onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['fecha_ing']?></td>
     <td onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['plazo_para_pago']?></td>  
     <td onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['periodo']?></td>   
     <td onclick="<?=$onclick_elegir?>">$<?=$result->fields['monto']?></td>
     
    
    </tr>    
    </tr>
	<?$result->MoveNext();
    }?>
    
	
	<tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='historial.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>