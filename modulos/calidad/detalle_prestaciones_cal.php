<?php

require_once("../../config.php");

$id_smiafiliados=$parametros['id_smiafiliados'] or $id_smiafiliados=$_POST['id_smiafiliados'];

variables_form_busqueda("detalle_prestaciones");
$orden = array(
        "default" => "4",
        "1" => "cuie",
        "2" => "nombreefector",
        "3" => "id_comprobante",
        "4" => "fecha_comprobante",
        "5" => "codigo",
        "6" => "descripcion",
        "7" => "precio_prestacion",
        "8" => "cantidad"
       );
$filtro = array(
		"Nombre Efector" => "nombreefector",
        "Codigo" => "codigo",                
       );
$sql_tmp="SELECT 
  facturacion.smiefectores.cuie,
  facturacion.smiefectores.nombreefector,
  facturacion.comprobante.id_comprobante,
  facturacion.comprobante.fecha_comprobante,
  facturacion.nomenclador.codigo,
  facturacion.nomenclador.descripcion,
  facturacion.prestacion.precio_prestacion,
  facturacion.prestacion.cantidad
FROM
  facturacion.comprobante
  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
  INNER JOIN facturacion.nomenclador ON (facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador)
  INNER JOIN facturacion.smiefectores ON (facturacion.comprobante.cuie = facturacion.smiefectores.cuie)
  INNER JOIN facturacion.factura ON (facturacion.comprobante.id_factura = facturacion.factura.id_factura)
WHERE
  (facturacion.factura.estado = 'C') and  facturacion.comprobante.id_smiafiliados='$id_smiafiliados'";

echo $html_header;
?>
<form name=form1 action="detalle_prestaciones.php" method=POST>
<input type="hidden" name="id_smiafiliados" value="<?=$id_smiafiliados?>">
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>	    
	    &nbsp;&nbsp;&nbsp;&nbsp;<input type=button name="volver" value="Volver" onclick="document.location='listado_beneficiarios_cal.php'"title="Volver al Listado" style="width=150px">
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>        
    <td align=right id=mo><a id=mo href='<?=encode_link("detalle_prestaciones.php",array("sort"=>"1","up"=>$up,"cuie"=>$cuie))?>' >CUIE</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("detalle_prestaciones.php",array("sort"=>"2","up"=>$up,"cuie"=>$cuie))?>' >Efector</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("detalle_prestaciones.php",array("sort"=>"3","up"=>$up,"cuie"=>$cuie))?>'>Nro.Comp.</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("detalle_prestaciones.php",array("sort"=>"4","up"=>$up,"cuie"=>$cuie))?>'>Fecha Comp.</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("detalle_prestaciones.php",array("sort"=>"5","up"=>$up,"cuie"=>$cuie))?>'>Codigo</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("detalle_prestaciones.php",array("sort"=>"6","up"=>$up,"cuie"=>$cuie))?>'>Descripcion</a></td>       
    <td align=right id=mo><a id=mo href='<?=encode_link("detalle_prestaciones.php",array("sort"=>"7","up"=>$up,"cuie"=>$cuie))?>'>Precio</a></td>    
    <td align=right id=mo><a id=mo href='<?=encode_link("detalle_prestaciones.php",array("sort"=>"8","up"=>$up,"cuie"=>$cuie))?>'>Cantidad</a></td>
  </tr>
 <?
   while (!$result->EOF) {	?>
  
    <tr <?=atrib_tr()?>> 
     <td ><?=$result->fields['cuie']?></td>
     <td ><?=$result->fields['nombreefector']?></td>
     <td ><?=$result->fields['id_comprobante']?></td>
     <td ><?=fecha($result->fields['fecha_comprobante'])?></td>     
     <td ><?=$result->fields['codigo']?></td> 
     <td ><?=$result->fields['descripcion']?></td>             
     <td ><?=number_format($result->fields['precio_prestacion'],2,',','.')?></td>       
     <td ><?=$result->fields['cantidad']?></td>       
      
    </tr>       
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>