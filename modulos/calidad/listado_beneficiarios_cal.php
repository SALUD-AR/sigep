<?php

require_once("../../config.php");

variables_form_busqueda("listado_beneficiarios_cal");

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
        "descripcion" => "Tipo Afiliado",
        "nombreefector"=>"Nombre Efector",
        "activo"=>"Activo",
        "clavebeneficiario"=>"Clave Beneficiario",
                
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

$sql_tmp="select * from nacer.smiafiliados
	 left join nacer.smitiposcategorias on (afitipocategoria=codcategoria)
	 left join facturacion.smiefectores on (cuieefectorasignado=cuie)";


if ($cmd=="activos")
    $where_tmp=" (smiafiliados.activo='S')";
    

if ($cmd=="inactivos")
    $where_tmp=" (smiafiliados.activo='N')";

echo $html_header;
?>
<form name=form1 action="listado_beneficiarios_cal.php" method=POST>
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
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios_cal.php",array("sort"=>"1","up"=>$up))?>' >Apellido</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios_cal.php",array("sort"=>"2","up"=>$up))?>'>Nombre</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios_cal.php",array("sort"=>"3","up"=>$up))?>'>DNI</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios_cal.php",array("sort"=>"4","up"=>$up))?>'>Tipo Beneficiario</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios_cal.php",array("sort"=>"5","up"=>$up))?>'>Nombre Efector</a></td>    
    <?if (($cmd=="todos")||($cmd=="inactivos")){?>
    	<td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios_cal.php",array("sort"=>"8","up"=>$up))?>'>Activo</a></td>
    	<td align=right id=mo>Cod Baja</td>
    	<td align=right id=mo>Mensaje Baja</td>    
    <?}?>  
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios_cal.php",array("sort"=>"7","up"=>$up))?>'>Clave Beneficiario</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios.php",array("sort"=>"9","up"=>$up))?>'>F Ins</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_beneficiarios.php",array("sort"=>"10","up"=>$up))?>'>F Carga</a></td>
    <td align=right id=mo>Pres.</td>
    <td align=right id=mo>Traz.</td>
  </tr>
 <?
   while (!$result->EOF) {?>
  
    <tr <?=atrib_tr()?>>     
     <td ><?=$result->fields['afiapellido']?></td>
     <td ><?=$result->fields['afinombre']?></td>
     <td ><?=$result->fields['afidni']?></td>     
     <td ><?=$result->fields['descripcion']?></td> 
     <td ><?=$result->fields['nombreefector']?></td>      
     <?if (($cmd=="todos")||($cmd=="inactivos")){?>    
     	<td><?=$result->fields['activo']?></td> 
     	<td><?=$result->fields['motivobaja']?></td> 
     	<td><?=$result->fields['mensajebaja']?></td> 
     <?}?>    
      <td ><?=$result->fields['clavebeneficiario']?></td> 
      <td ><?=fecha($result->fields['fechainscripcion'])?></td>  
      <td ><?=fecha($result->fields['fechacarga'])?></td>  
      <?$ref = encode_link("detalle_prestaciones_cal.php",array("id_smiafiliados"=>$result->fields['id_smiafiliados']));
      $onclick_elegir1="location.href='$ref'";
      $id_smiafiliados=$result->fields['id_smiafiliados'];
      $sql_tmp="SELECT 
  			count (facturacion.prestacion.id_prestacion) as total
  		FROM
  			facturacion.comprobante
  		INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)  
  		INNER JOIN facturacion.factura ON (facturacion.comprobante.id_factura = facturacion.factura.id_factura)
		WHERE
			(facturacion.factura.estado = 'C') and  facturacion.comprobante.id_smiafiliados='$id_smiafiliados'";
      $result_tmp=sql($sql_tmp, "Error") or fin_pagina();  
      $result_tmp=$result_tmp->fields['total']; 
      if ($result_tmp==0) $arch="alerta.gif";
      else $arch="files1.gif";    
      ?>
      <td onclick="<?=$onclick_elegir1?>"><center><img src='../../imagenes/<?=$arch?>' style='cursor:hand;' title="<?echo "Cantidad de Prestaciones: ".$result_tmp?>"> <?=$result_tmp?></center></td>  
      <?
      $afidni=$result->fields['afidni'];
      $sql_1="select * from trazadoras.embarazadas
      where num_doc='$afidni'";
      $sql_2="select * from trazadoras.nino
      where num_doc='$afidni'";
      $sql_3="select * from trazadoras.partos
      where num_doc='$afidni'";
      
      $res_1=sql($sql_1,"Error Trazadoras");
      $res_2=sql($sql_2,"Error Trazadoras");
      $res_3=sql($sql_3,"Error Trazadoras");
      $res_1=$res_1->recordCount()+$res_2->recordCount()+$res_3->recordCount();
      $ref = encode_link("detalle_trazadoras.php",array("afidni"=>$result->fields['afidni']));
      $onclick_elegir2="location.href='$ref'";
      ($res_1==0)?$image="alerta.gif":$image="files1.gif";
      ?>
      <td onclick="<?=$onclick_elegir2?>"><center><img title="Cantidad Trazadora: <?=$res_1?>" src='../../imagenes/<?=$image?>' style='cursor:hand;'> <?=$res_1?></center> </td>  
    </tr>    
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>