<?php
/*
Author: gaby 

modificada por
$Author: gaby $
$Revision: 1.0 $
$Date: 2006/07/20 15:22:40 $
*/
require_once("../../config.php");

variables_form_busqueda("pais_listado");

$orden = array(
        "default" => "1",
        "1" => "nombre"
       );
$filtro = array(
		"nombre" => "Pais"  
       );
$sql_tmp="select * from uad.pais";

echo $html_header;
?>
<form name=form1 action="pais_listado.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_pais,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;<input type='button' name="nueva_pais" value='Nuevo pais' onclick="document.location='pais_admin.php'">
	  </td>
     </tr>
</table>

<?$result = sql($sql,"No se ejecuto en la consulta principal") or die;?>

<table border=0 width=50% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=12 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_pais?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  <tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("pais_listado.php",array("sort"=>"1","up"=>$up))?>' >Pais</a></td>      	
     </tr>
  <?
   while (!$result->EOF) {
   		$ref = encode_link("pais_admin.php",array("id_pais"=>$result->fields['id_pais'],"pagina"=>"pais_listado"));
    	$onclick_elegir="location.href='$ref'";
   	?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>
    </tr>    
	<?$result->MoveNext();
    }?>
  	
</table>
</form>
</body>
</html>

<?echo fin_pagina();// aca termino ?>