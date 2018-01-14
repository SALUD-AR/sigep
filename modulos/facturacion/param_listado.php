<?php
require_once("../../config.php");

variables_form_busqueda("param_listado");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

$orden = array(
        "default" => "1",
        "1" => "id_nomenclador_detalle",
        "2" => "descripcion",
        "3" => "modo_facturacion",
        "4" => "fecha_desde",
        "5" => "fecha_hasta",
       );
$filtro = array(
		"descripcion" => "Descripcion",
       );

$sql_tmp="select * from facturacion.nomenclador_detalle";

echo $html_header;
?>
<form name=form1 action="param_listado.php" method=POST>
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
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("param_listado.php",array("sort"=>"1","up"=>$up))?>'>ID</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("param_listado.php",array("sort"=>"2","up"=>$up))?>'>Descripcion</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("param_listado.php",array("sort"=>"3","up"=>$up))?>'>Modo</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("param_listado.php",array("sort"=>"4","up"=>$up))?>'>Fecha Desde</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("param_listado.php",array("sort"=>"5","up"=>$up))?>'>Fecha Hasta</a></td>
  </tr>
 <?
   while (!$result->EOF) {
   		$ref = encode_link("param_admin.php",array("id_nomenclador_detalle"=>$result->fields['id_nomenclador_detalle'],"modo_facturacion"=>$result->fields['modo_facturacion']));
    	$onclick_elegir="location.href='$ref'";
   	?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['id_nomenclador_detalle']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['descripcion']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['modo_facturacion']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['fecha_desde']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['fecha_hasta']?></td>     
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
