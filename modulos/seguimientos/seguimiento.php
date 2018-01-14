<?php

require_once("../../config.php");

variables_form_busqueda("seguimiento");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

if ($cmd == "")  $cmd="VERDADERO";

$orden = array(
        "default" => "1",
        "1" => "cuie",
        "2" => "efe_conv.nombre",        
        "3" => "cuidad",    
        "9" => "nombre_dpto"    
       );
       
$filtro = array(
		"cuie" => "CUIE",
        "efe_conv.nombre" => "Nombre",
        "referente" => "Referente"
       );
       

$sql_tmp="SELECT 
  efe_conv.*,zona_sani.*,dpto.nombre as nombre_dpto
FROM
  nacer.efe_conv
  left join facturacion.nomenclador_detalle using (id_nomenclador_detalle)
  left join nacer.zona_sani using (id_zona_sani)
  left join nacer.dpto on dpto.codigo=efe_conv.departamento";

$user_login1=substr($_ses_user['login'],0,6);

  	 	
echo $html_header;
?>
<form name=form1 action="seguimiento.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;
     <input type=button style="background-color:'#E3FC7E'" name="Seguimiendo Global" value="Seguimiendo Global" onclick="document.location='seguimiento_global.php'" title="Seguimiendo Gloval" style="width=200px">     
   	 </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=15 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("efectores_unif.php",array("sort"=>"1","up"=>$up))?>'>CUIE</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("efectores_unif.php",array("sort"=>"2","up"=>$up))?>'>Nombre</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("efectores_unif.php",array("sort"=>"3","up"=>$up))?>'>Cuidad</a></td>        
    <td align=right id=mo><a id=mo href='<?=encode_link("efectores_unif.php",array("sort"=>"5","up"=>$up))?>'>Referente</a></td>        
    <td align=right id=mo><a id=mo href='<?=encode_link("efectores_unif.php",array("sort"=>"6","up"=>$up))?>'>Telefono</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("efectores_unif.php",array("sort"=>"9","up"=>$up))?>'>Departamento</a></td>  
    <td align=right id=mo>Listado TODOS</td>    
    <td align=right id=mo>Listado PSP</td>        
   </tr>
 <?
   while (!$result->EOF) {
  	$ref = encode_link("efectores_detalle.php",array("id_efe_conv"=>$result->fields['id_efe_conv']));
    $onclick_elegir="location.href='$ref'";?>
    
    <tr <?=atrib_tr()?>>        
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['cuie']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['cuidad']?></td>       
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['referente']?></td>  
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['tel']?></td> 
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre_dpto']?></td>  
     <?$ref_1 = encode_link("efectores_detalle_solo.php",array("id_efe_conv"=>$result->fields['id_efe_conv']));?>
      <td align="center">  <a href="<?=$ref_1?>" title="Listado"><IMG src='<?=$html_root?>/imagenes/iso.jpg' height='20' width='20' border='0'></a></td>
    <?$ref_1 = encode_link("efectores_detalle_solo_sps.php",array("id_efe_conv"=>$result->fields['id_efe_conv']));?>
      <td align="center">  <a href="<?=$ref_1?>" title="Listado"><IMG src='<?=$html_root?>/imagenes/iso.jpg' height='20' width='20' border='0'></a></td>
    </tr>
	<?$result->MoveNext();
    }    
    ?>    
    <tr>
  	<td colspan=15 align=left id=ma>
     <table width=100%>
      
    </table>
   </td>
  </tr>
   <tr>
  	<td colspan=15 align=left id=ma>
     <table width=100%>
      	
    </table>
   </td>
  </tr>
  
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>