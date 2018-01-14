<?php
/*
Author: ferni 

modificada por
$Author: ferni $
$Revision: 1.30 $
$Date: 2006/07/20 15:22:40 $
*/
require_once("../../config.php");

variables_form_busqueda("efec_nom_listado");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

if ($cmd == "")  $cmd="activos";

$orden = array(
        "default" => "1",
        "1" => "cuie",
        "2" => "nombreefector",        
       );
$filtro = array(
		"cuie" => "CUIE",
        "nombreefector" => "Nombre Efector",        
       );

$sql_tmp="select * from facturacion.smiefectores";

echo $html_header;
?>
<form name=form1 action="efec_nom_listado.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>

<table border=0 width=80% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=11 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>    
       <td width=30% align=left><b>EFECTOR / NOMENCLADOR</b></td>   
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("efec_nom_listado.php",array("sort"=>"1","up"=>$up))?>' >CUIE</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("efec_nom_listado.php",array("sort"=>"2","up"=>$up))?>'>Nombre Efector</a></td>
  </tr>
 <?
   while (!$result->EOF) {
   	if ($cmd=='activos'){
   		$ref = encode_link("efec_nom_admin.php",array("cuie"=>$result->fields['cuie'],"nombreefector"=>$result->fields['nombreefector']));
    	$onclick_elegir="location.href='$ref'";
   	}?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['cuie']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombreefector']?></td>     
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>