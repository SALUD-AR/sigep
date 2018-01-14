<?php
/*
Author: sebastian lohaiza

modificada por
$Author: seba $
$Revision: 1.30 $
$Date: 2012/08/01 18:25:40 $
*/
require_once("../../config.php");



variables_form_busqueda("historial_cardiopatia");


$fecha_hoy=date("Y-m-d");
$plazo_30=date("Y-m-d", strtotime ("$fecha_hoy +30 days"));

$orden = array(
        "default" => "1",
        "default_up" => "0",
        "1" => "id_factura",
        "2" => "expediente",
        "3" => "comprobante",
        "4" => "id_efector",
        "5" => "nombre",
        "6" => "fecha_factura"
       );
$filtro = array(
        "to_char(id_factura,'999999')" => "Nro. Factura",
		"expediente" => "expediente",
        "comprobante" => "comprobante",
		"nombre" => "Nomb.Efector"               
       );

$sql_tmp="SELECT * FROM cardiopatia.factura";


echo $html_header;
?>
<form name=form1 action="historial_cardiopatia.php" method=POST>
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
        <td width=30 bgcolor='#D7D5FA' bordercolor='#000000' height=30>&nbsp;</td> 
        <td bordercolor='#FFFFFF'>Facturas Pagadas</td>
        
        <td width=30 bgcolor='#87F881' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Facturas No Pagas</td>
	 </tr>       
</table>
	  
<table border=1 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id="ma">
     <table width=100% >
      <tr id="ma">
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

<tr>
    <td width=5% align=right id="mo"><a id="mo" href='<?=encode_link("historial_det_card.php",array("sort"=>"1","up"=>$up))?>'>Numero de Factura</a></td>
     <td width=5% align=right id="mo"><a id="mo" href='<?=encode_link("historial_det_card.php",array("sort"=>"2","up"=>$up))?>'>Numero de Expediente</a></td>
     <td align=right id="mo"><a id="mo" href='<?=encode_link("historial_det_card.php")?>'>Orden de Pago</a></td>
     <td align=right id="mo"><a id="mo" href='<?=encode_link("historial_det_card.php",array("sort"=>"3","up"=>$up))?>'>Comprobante</a></td>
     <!--<td align=right id="mo"><a id="mo" href='<?=encode_link("historial_det_card.php",array("sort"=>"4","up"=>$up))?>'>Id Efector</a></td>-->
    <td align=right id="mo"><a id="mo" href='<?=encode_link("historial_det_card.php",array("sort"=>"5","up"=>$up))?>'>Nombre Efector</a></td> 
     <td align=right id="mo"><a id="mo" href='<?=encode_link("historial_det_card.php")?>'>Tipo de Pago</a></td>
     <td width=15% align=right id="mo"><a id="mo" href='<?=encode_link("historial_det_card.php")?>'>Cheque a nombre de</a></td>  
	 <td width=15% align=right id="mo"><a id="mo" href='<?=encode_link("historial_det_card.php")?>'>CBU</a></td> 
    <td align=right id="mo"><a id="mo" href='<?=encode_link("historial_det_card.php",array("sort"=>"6","up"=>$up))?>'>Fecha de Factura</a></td> 
    <td width=8% align=center id="mo"><a id="mo"  href='<?=encode_link("historial_det_card.php")?>'>Monto</a></td>
     
  </tr>


<?
    
	while (!$result->EOF) {
   	
   	$ref = encode_link("historial_det_card.php",array("id_factura"=>$result->fields['id_factura']));
    $onclick_elegir="location.href='$ref'";
    
    if ($result->fields['pagado']){ $tr=atrib_tr3();}
    else { 
    $tr="bgcolor=#87F881 onmouseover=\"this.style.backgroundColor = '$bgcolor_over'; this.style.color = '$text_color_over'\" onmouseout=\"this.style.backgroundColor = '$bgcolor_out_int'; this.style.color = '$text_color_out'\"; style='cursor: hand; height:35px;'";}?>
  
    <tr <?=$tr?>>     
      <td onclick="<?=$onclick_elegir?>" align="center"><?=$result->fields['id_factura']?></td>
      <td onclick="<?=$onclick_elegir?>" align="center"><?=$result->fields['expediente']?></td>
      <td onclick="<?=$onclick_elegir?>" align="center"><?=$result->fields['orden_pago']?></td> 
	  <td onclick="<?=$onclick_elegir?>"align="center"><?=$result->fields['comprobante']?></td> 
    <!-- <td onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['id_efector']?></td>-->
     <td onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['nombre']?></td>
     <td onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['tipo_pago']?></td>
     <td onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['cheque_a_nombre_de']?></td>
     <td onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['cbu']?></td>  
     <td onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['fecha_factura']?></td>   
     <td onclick="<?=$onclick_elegir?>" align="center" >$<?=$result->fields['total']?></td>
     
    </tr>    
    </tr>
	<?$result->MoveNext();
    }?>
   </td>
  </tr>
</table> 
	&nbsp;&nbsp;&nbsp;
  &nbsp;&nbsp;&nbsp;
	<tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='historial_cardiopatia.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>