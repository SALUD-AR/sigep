<?php
/*
Author: gaby 
$Revision: 1.0 $
$Date: 2010/12/03 15:22:40 $
*/
require_once("../../config.php");

variables_form_busqueda("listado_validar_prest");

$orden = array(
        "default" => "1",
        "1" => "nomenclador.codigo",
        "2" => "cant_pres_lim",
        "3" => "per_pres_limite",
        "4" => "msg_error",
        "5"=> "nomenclador.descripcion",
        "6"=>"nomenclador_detalle.descripcion"
       );
$filtro = array(
		"nomenclador.codigo" => "Codigo",
    "nomenclador.descripcion"=> "Descripcion",
    "nomenclador_detalle.descripcion"=>"Descripcion Nomenclador"
           );

/*$sql_tmp="select validacion_prestacion.*, facturacion.nomenclador.*,nomenclador_detalle.descripcion as descripcion_nomen
			from facturacion.validacion_prestacion 
			left join facturacion.nomenclador using (id_nomenclador)
			left join facturacion.nomenclador_detalle using (id_nomenclador_detalle)";*/
$sql_tmp="SELECT nomenclador.*,
  nomenclador_detalle.descripcion as descripcion_nomen,
  validacion_prestacion.codigo as siglas,
  validacion_prestacion.cant_pres_lim,
  validacion_prestacion.per_pres_limite,
  validacion_prestacion.msg_error,
  validacion_prestacion.id_val_pres
  from facturacion.nomenclador
  inner join facturacion.nomenclador_detalle on nomenclador.id_nomenclador_detalle=nomenclador_detalle.id_nomenclador_detalle
  left join facturacion.validacion_prestacion on validacion_prestacion.id_nomenclador=nomenclador.id_nomenclador";

$where_tmp="nomenclador.id_nomenclador_detalle=11";

echo $html_header;
?>
<form name=form1 action="listado_validar_prest.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_validacion,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;<input type='button' name="nuevo" value='Nuevo' onclick="document.location='validar_prest_admin.php'">
	  </td>
     </tr>
</table>

<?$result = sql($sql,"No se ejecuto en la consulta principal") or die;?>

<table border=0 width=85% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=12 align=left id="ma">
     <table width=100%>
      <tr id="ma">
       <td width=30% align=left><b>Total:</b> <?=$total_validacion?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  <tr>
  <td align=right id=mo><a id=mo href='<?=encode_link("listado_validar_prest.php",array("sort"=>"1","up"=>$up))?>' >Codigo</a></td>      	
  <td align=right id=mo><a id=mo href='<?=encode_link("listado_validar_prest.php",array("sort"=>"5","up"=>$up))?>' >Descripcion</a></td>      	
	<td align=right id=mo><a id=mo href='<?=encode_link("listado_validar_prest.php",array("sort"=>"5","up"=>$up))?>' >Nomenclador</a></td>   
	<td align=right id=mo><a id=mo href='<?=encode_link("listado_validar_prest.php",array("sort"=>"2","up"=>$up))?>' >Cant. Prestaciones</a></td>   
	<td align=right id=mo><a id=mo href='<?=encode_link("listado_validar_prest.php",array("sort"=>"3","up"=>$up))?>' >Periodo Limite</a></td>   
	<td align=right id=mo><a id=mo href='<?=encode_link("listado_validar_prest.php",array("sort"=>"4","up"=>$up))?>' >Mensaje de error</a></td>              
  </tr>
  <?
   while (!$result->EOF) {
   		$ref = encode_link("validar_prest_admin.php",array(
        "id_val_pres"=>$result->fields['id_val_pres'],"id_nomenclador"=>$result->fields['id_nomenclador'],"pagina"=>"listado_validar_prest"));
   		$onclick_elegir="location.href='$ref'";
   	?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['codigo']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['descripcion']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['descripcion_nomen']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['cant_pres_lim']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['per_pres_limite']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['msg_error']?></td>
    </tr>    
	<?$result->MoveNext();
    }?>
  	
</table>
</form>
</body>
</html>

<?echo fin_pagina();// aca termino ?>
