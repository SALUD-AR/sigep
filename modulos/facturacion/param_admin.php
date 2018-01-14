<?php
require_once("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
variables_form_busqueda("param_admin");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

$orden = array(
        "default" => "2",
        "1" => "id_nomenclador",
        "2" => "descripcion",
        "3" => "codigo",
        "4" => "grupo",
        "5" => "subgrupo",
        "6" => "precio",
       );
$filtro = array(
		"descripcion" => "Descripcion",
       );

$sql_tmp="select * from facturacion.nomenclador where id_nomenclador_detalle=$id_nomenclador_detalle order by grupo,codigo,descripcion";
$where_tmp= "";

echo $html_header;
?>
<form name=form1 action="param_admin.php" method=POST>
<input type="hidden" name="id_nomenclador_detalle" value="<?=$id_nomenclador_detalle?>">
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?//list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    <!--</->&nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>-->
	  </td>
     </tr>
</table>

<?$result = sql($sql_tmp) or die;?>

<table border=0 width=80% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=11 align=left id=ma>
     <table width=100%>
      <tr id=ma>
		  <?$total_muletos=$result->recordcount()?>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>    
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
    <td align=right id=mo><!--<a id=mo href='<?=encode_link("param_admin.php",array("sort"=>"1","up"=>$up))?>'>-->ID</a></td>      	
    <td align=right id=mo><!--<a id=mo href='<?=encode_link("param_admin.php",array("sort"=>"2","up"=>$up))?>'>-->Descripcion</a></td>
    <td align=right id=mo><!--<a id=mo href='<?=encode_link("param_admin.php",array("sort"=>"3","up"=>$up))?>'>-->Codigo</a></td>
    <td align=right id=mo><!--<a id=mo href='<?=encode_link("param_admin.php",array("sort"=>"4","up"=>$up))?>'>-->Grupo</a></td>
    <td align=right id=mo><!--<a id=mo href='<?=encode_link("param_admin.php",array("sort"=>"5","up"=>$up))?>'>-->Subgrupo</a></td>
    <td align=right id=mo><!--<a id=mo href='<?=encode_link("param_admin.php",array("sort"=>"6","up"=>$up))?>'>-->Precio</a></td>
  </tr>
 <?
   while (!$result->EOF) {
   		$ref = encode_link("param_admin_fin.php",array("id_nomenclador"=>$result->fields['id_nomenclador']));
    	$onclick_elegir="location.href='$ref'";
   	?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['id_nomenclador']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['descripcion']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['codigo']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['grupo']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['subgrupo']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['precio']?></td>     
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
