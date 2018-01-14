<?php

require_once("../../config.php");

variables_form_busqueda("listado_beneficiarios_fact");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

if ($cmd == "")  $cmd="activos";

$orden = array(
        "default" => "1",
        "1" => "afiapellido",
        "2" => "afinombre",
        "3" => "afidni",
        "4" => "afitipocategoria",
        "5" => "nombreefector",
        "6" => "activo",
        "7" => "clavebeneficiario",
        "8" => "activo",
        "9" => "fechainscripcion",
        "10" => "fechacarga"
       );
$filtro = array(
		"afidni" => "DNI",
        "afiapellido" => "Apellido",
        "afinombre" => "Nombre",              
       );
$datos_barra = array(
     array(
        "descripcion"=> "Activos",
        "cmd"        => "activos"
     ),
     array(
        "descripcion"=> "Inactivos",
        "cmd"        => "inactivos"
     ),
     array(
        "descripcion"=> "Todos",
        "cmd"        => "todos"
     )
);

generar_barra_nav($datos_barra);

$sql_tmp="select id_smiafiliados,afiapellido,afinombre,afidni,nombre,motivobaja,mensajebaja,afifechanac,clavebeneficiario,fechainscripcion
	 from nacer.smiafiliados
	 left join nacer.efe_conv on (cuieefectorasignado=cuie)";


if ($cmd=="activos")
    $where_tmp=" (smiafiliados.activo='S')";
    

if ($cmd=="inactivos")
    $where_tmp=" (smiafiliados.activo='N')";

echo $html_header;
?>
<form name=form1 action="listado_beneficiarios_fact.php" method=POST>
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
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios_fact.php",array("sort"=>"1","up"=>$up))?>' >Apellido</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios_fact.php",array("sort"=>"2","up"=>$up))?>'>Nombre</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios_fact.php",array("sort"=>"3","up"=>$up))?>'>DNI</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios_fact.php",array("sort"=>"5","up"=>$up))?>'>Nombre Efector</a></td>    
    <?if (($cmd=="todos")||($cmd=="inactivos")){?>
    	<td align=right id=mo>Cod Baja</td>
    	<td align=right id=mo>Mensaje Baja</td>      	
    <?}?>  
    <td align=right id=mo>F NAC</td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios_fact.php",array("sort"=>"7","up"=>$up))?>'>Clave Beneficiario</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios_fact.php",array("sort"=>"9","up"=>$up))?>'>F Ins</a></td>
    <?if ($cmd=="inactivos"){?>    	
    	<td align=right id=mo title="Facturar Excepciones" >Fact Excep</td>  
    <?}?>  
  </tr>
 <?
   while (!$result->EOF) {
   	if ($cmd=='activos'){
   		$ref = encode_link("comprobante_admin.php",array("id_smiafiliados"=>$result->fields['id_smiafiliados'],"clavebeneficiario"=>$result->fields['clavebeneficiario'],"pagina_listado"=>"listado_beneficiario_fact","estado"=>$result->fields['activo']));
    	$onclick_elegir="location.href='$ref'";
   	}?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['afiapellido']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['afinombre']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['afidni']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>      
     <?if (($cmd=="todos")||($cmd=="inactivos")){?>    
     	<td><?=$result->fields['motivobaja']?></td> 
     	<td><?=$result->fields['mensajebaja']?></td> 
     <?}?>    
      <td onclick="<?=$onclick_elegir?>"><?=fecha($result->fields['afifechanac'])?></td>
      <td onclick="<?=$onclick_elegir?>"><?=$result->fields['clavebeneficiario']?></td> 
      <td onclick="<?=$onclick_elegir?>"><?=fecha($result->fields['fechainscripcion'])?></td>  
      <?$m_b=$result->fields['motivobaja'];
      	if ($cmd=="inactivos"){
      	if ($m_b=='22'||$m_b=='31'||$m_b=='41'){
      		if (permisos_check('inicio','factura_exepciones')){	
      			$ref1 = encode_link("comprobante_admin.php",array("id_smiafiliados"=>$result->fields['id_smiafiliados'],"pagina"=>"listado_beneficiario_fact","flag_inactivo"=>"S"));
    			$onclick_elegir1="location.href='$ref1'";
    			$onclick_elegir1="alert ('ESTA OPCION ES USADA SOLO SI LLEGA UN COMPROBANTE QUE SE PUEDE FACTURAR DEBIDO A QUE FUE REALIZADO ANTES QUE EL BENEFICIARIO SE DIERA DE BAJA.');".$onclick_elegir1;
      		}
      		else $onclick_elegir1="alert ('Debe Tener Permisos Especiales para poder Facturar.')";
    		?>
      		<td title="Factura Excepciones (Embarazadas - Puerperas con ciclo cumplido y Niños con 6 años Cumplidos)" onclick="<?=$onclick_elegir1?>" align="center"><img src='../../imagenes/luz1.gif' style='cursor:hand;'></td>
		    <?}
		else{?>
		   <td title='No hay motivo para facturar este item' align="center" onclick="alert ('No hay motivo para TENER QUE FACTURAR este item - Si encuentra un motivo avise a Facturacion.')"><img src='../../imagenes/salir.gif' style='cursor:hand;'></td>
		<?}
      	}?>     	
      
      
     </tr>    
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
