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
		"cuie" => "Cuie",
        "nro_exp" => "Numero Expediente"                
       );

//$sql_tmp="SELECT * FROM expediente.expediente left join nacer.efe_conv using (id_efe_conv)";
$sql_tmp="SELECT * FROM expediente.expediente left join expediente.transaccion using (id_expediente)left join nacer.efe_conv using (id_efe_conv) where expediente.expediente.control=1";


echo $html_header;
?>
<form name=form1 action="modificacion.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>  
	     <? $link=encode_link("expediente_excel.php",array());?>
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
    <td align=right id=mo><a id=mo href='<?=encode_link("modificacion.php",array("sort"=>"1","up"=>$up))?>'>Numero de Expediente</a></td>
     <td align=right id=mo><a id=mo href='<?=encode_link("modificacion.php",array("sort"=>"6","up"=>$up))?>'>Nombre del Efector</a></td> 
     <td align=right id=mo><a id=mo href='<?=encode_link("modificacion.php",array("sort"=>"6","up"=>$up))?>'>CUIE</a></td>     	
    <td align=right id=mo><a id=mo href='<?=encode_link("modificacion.php",array("sort"=>"2","up"=>$up))?>'>Fecha de Ingreso</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("modificacion.php",array("sort"=>"2","up"=>$up))?>'>Plazo para Pago</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("modificacion.php",array("sort"=>"2","up"=>$up))?>'>Monto</a></td>
     <td align=right id=mo><a id=mo href='<?=encode_link("modificacion.php",array("sort"=>"2","up"=>$up))?>'>debito</a></td>
      <td align=right id=mo><a id=mo href='<?=encode_link("modificacion.php",array("sort"=>"2","up"=>$up))?>'>credito</a></td>
       <td align=right id=mo><a id=mo href='<?=encode_link("modificacion.php",array("sort"=>"2","up"=>$up))?>'>total</a></td>
     
   
  </tr>


<?
 

while (!$result->EOF ) {
   	
   	$ref = encode_link("mod_exp2.php",array("nro_exp"=>$result->fields['nro_exp'],"id_expediente"=>$result->fields['id_expediente'],"nombre_efector"=>$result->fields['nombre'],"fecha_ing"=>$result->fields['fecha_ing'],"fecha_fin"=>$result->fields['plazo_para_pago'],"debito"=>$result->fields['debito'],"credito"=>$result->fields['credito'],"total"=>$result->fields['total_pagar'],"id_transac"=>$result->fields['id_transac'],"monto"=>$result->fields['monto']));
    $onclick_elegir="location.href='$ref'";
   	?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nro_exp']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['cuie']?></td> 
     <td onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['fecha_ing']?></td>
     <td onclick="<?=$onclick_elegir?>" align="center" > <?=$result->fields['plazo_para_pago']?></td>
     <td onclick="<?=$onclick_elegir?>">$<?=$result->fields['monto']?></td>
     <td onclick="<?=$onclick_elegir?>">$<?=$result->fields['debito']?></td>
     <td onclick="<?=$onclick_elegir?>">$<?=$result->fields['credito']?></td>
     <td onclick="<?=$onclick_elegir?>">$<?=$result->fields['total_pagar']?></td>
         
            
    </tr>    
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>