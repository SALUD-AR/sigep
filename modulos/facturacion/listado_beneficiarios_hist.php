<?php

require_once("../../config.php");

variables_form_busqueda("listado_beneficiarios_hist");

$orden = array(
        "default" => "1",
        "1" => "afiapellido",
        "2" => "afinombre",
        "3" => "afidni"
       );
$filtro = array(
		"afidni" => "DNI",
        "afinombre" => "Nombre",               
       );
$sql_tmp="select id_smiafiliados,clavebeneficiario,afiapellido,afinombre,afidni,activo
  from nacer.smiafiliados";

echo $html_header;
?>
<form name=form1 action="listado_beneficiarios_hist.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	  </td>
     </tr>
</table>

<?if ($_POST['buscar']) $result = sql($sql) or die;?>

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
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios_hist.php",array("sort"=>"1","up"=>$up))?>'>Apellido</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios_hist.php",array("sort"=>"2","up"=>$up))?>'>Nombre</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios_hist.php",array("sort"=>"3","up"=>$up))?>'>DNI</a></td>
    <td align=right id=mo>Clave Beneficiario</td>
  </tr>
 <?if ($_POST['buscar']){
   while (!$result->EOF) {
   	$ref = encode_link("comprobante_admin.php",array("id_smiafiliados"=>$result->fields['id_smiafiliados'],"clavebeneficiario"=>$result->fields['clavebeneficiario'],"pagina_listado"=>"listado_beneficiarios_hist.php","estado"=>"S"));
    $onclick_elegir="location.href='$ref'";?>
   	  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['afiapellido']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['afinombre']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['afidni']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['clavebeneficiario']?></td>     
    </tr>
	<?$result->MoveNext();
    }}?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>