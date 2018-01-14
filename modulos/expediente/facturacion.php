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
        "1" => "id_factura",
        "2" => "monto_prrefactura",
        "3" => "periodo",
        "4" => "estado",
        "5" => "fecha_factura",
        "6" => "nro_exp"
       );
$filtro = array(
		"cuie" => "cuie"
                        
       );

$sql_tmp="SELECT * FROM facturacion.factura where cuie='$cuie'";


echo $html_header;
?>
<form name=form1 action="facturacion.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>  
	     <? $link=encode_link("facturacion_excel.php",array());?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">  
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>

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
  
>

<tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"1","up"=>$up))?>'>Id_factura</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"6","up"=>$up))?>'>Cuie</a></td> 
    <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"6","up"=>$up))?>'>Periodo</a></td>     	
    <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"2","up"=>$up))?>'>Monto Prefactura</a></td> 
    <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"3","up"=>$up))?>'>Numero Expediente</a></td> 
    <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"2","up"=>$up))?>'>Numero Exp. Externo</a></td>     	
    <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"2","up"=>$up))?>'>Periodo Contable</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"4","up"=>$up))?>'>Periodo Actual</a></td>
     
   
  </tr>


<?
   while (!$result->EOF and $result->fields['control']==0) {
   	
   	$ref = encode_link("mod_exp.php",array("nro_exp"=>$result->fields['nro_exp'],"id_expediente"=>$result->fields['id_expediente'],"nombre_efector"=>$result->fields['nombre'],"fecha_ing"=>$result->fields['fecha_ing'],"fecha_fin"=>$result->fields['plazo_para_pago'],"monto"=>$result->fields['monto']));
    $onclick_elegir="location.href='$ref'";
   	?>
  
    <tr <?=atrib_tr()?>>     
      <td onclick="<?=$onclick_elegir?>"><?=$result->fields['id_factura']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['cuie']?></td>
      <td onclick="<?=$onclick_elegir?>"><?=$result->fields['periodo']?></td> 
     <td onclick="<?=$onclick_elegir?>" align="center" >$<?=$result->fields['monto_prefactura']?></td>
     <td  onclick="<?=$onclick_elegir?>"align="center" > <?=$result->fields['nro_exp']?></td>     
     <td  onclick="<?=$onclick_elegir?>"align="center" > <?=$result->fields['nro_exp_ext']?></td>
    <td><?=$result->fields['periodo_contable']?></td>
     <td><?=$result->fields['periodo_actual']?></td>
         
            
    </tr>    
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>