<?php

require_once("../../config.php");

variables_form_busqueda("listado_puco");

$orden = array(
        "default" => "1",
        "1" => "documento"        
       );
$filtro = array(
		"documento" => "DNI"         
       );
$sql_tmp="select documento,tipo_doc,puco.nombre,obras_sociales.nombre as nom_os
			from puco.puco
			inner join puco.obras_sociales using (cod_os)";

echo $html_header;
?>
<form name=form1 action="listado_puco.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=12 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_puco.php",array("sort"=>"1","up"=>$up))?>'>Documento</a></td>      	
    <td align=right id=mo>Tipo</td>
    <td align=right id=mo>Apellido y Nombre</td>
    <td align=right id=mo>Obra Social</td>
  </tr>
 <?
   while (!$result->EOF) {?>    	  
    <tr <?=atrib_tr()?>>     
     <td ><?=$result->fields['documento']?></td>
     <td ><?=$result->fields['tipo_doc']?></td>
     <td ><?=$result->fields['nombre']?></td>     
     <td ><?=$result->fields['nom_os']?></td>     
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>