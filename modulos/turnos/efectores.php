<?php
require_once("../../config.php");

$extras = array(
            "pagina"     => "", // pagina a la que debe retornar despues de seleccionar la especialidad
            "id_parent"  => ""  // id del elemento al que se debe asociar la especialidad
          );

variables_form_busqueda("listado_efectores", $extras);

// si no viene una pagina por parametro o por post, borro los valores anteriores de la sesion
if (empty($parametros['pagina']) && empty($_POST['pagina']) &&
    (!empty($_ses_listado_efectores["pagina"]) || !empty($_ses_listado_efectores["id_parent"]))
  ) {
  $pagina = $id_parent = "";
  $_ses_listado_efectores["pagina"] = "";
  $_ses_listado_efectores["id_parent"] = "";
  phpss_svars_set("_ses_listado_efectores", $_ses_listado_efectores);
}

$mensaje_tipo = "warning";

if (!empty($pagina)) {
  $mensaje = "Usted est&aacute; seleccionando un Efector para ser usado en otra p&aacute;gina, haga click en la fila deseada para seleccionarlo y volver a la p&aacute;gina anterior";
}

$orden = array(
        "default" => "2",
        "1" => "cuie",
        "2" => "nombre",
        "3" => "cuidad"
       );
$filtro = array(
        "cuie"     => "CUIE",
        "nombre"   => "Nombre",
		    "cuidad"   => "Ciudad"
       );

$sql_tmp="SELECT 
  nacer.efe_conv.id_efe_conv,
  nacer.efe_conv.cuie,
  nacer.efe_conv.nombre,
  nacer.efe_conv.cuidad AS ciudad
FROM
  nacer.efe_conv
";

echo $html_header;

$mensaje_tipo = "warning";
if (!empty($pagina)) {
  $mensaje = "Usted est&aacute; seleccionando un Efector para ser usado en otra p&aacute;gina, haga click en la fila deseada para seleccionarlo y volver a la p&aacute;gina anterior";
}

?>
<?php if (!empty($mensaje)) { ?>
<div class="container alert alert-<?php echo $mensaje_tipo; ?> alert-dismissible" role="alert" style="width:40%;">
  <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
    <p><strong><?php echo $mensaje; ?></strong></p>
</div>
<?php } ?>

<div class="container">
<form name="form1" action="efectores.php" method="POST">
<?php if (!empty($pagina)) { ?>
  <input type="hidden" name="pagina" value="<?php echo $pagina; ?>" />
  <input type="hidden" name="id_parent" value="<?php echo $id_parent; ?>" />
<?php } ?>

<br/>
<div class="row">
  <div class="col-md-8 col-md-offset-2 text-center">
    <?php 
      $link_tmp = array("pagina" => $pagina, "id_parent" => $id_parent);
      list($sql,$total_efectores,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");
      $result = sql($sql) or fin_pagina();
      // if (!empty($pagina)) {
      //   $link_nuevo_efector = encode_link("$html_root/modulos/inscripcion/ins_admin_old.php",array("pagina_viene_1"=>encode_link("$html_root/modulos/turnos/pacientes.php", array("pagina" => $pagina))));
      // } else {
      //   $link_nuevo_efector = encode_link("$html_root/modulos/inscripcion/ins_admin_old.php",array("pagina_viene_1"=>"$html_root/modulos/turnos/pacientes.php"));
      // }

    ?>
     &nbsp;&nbsp;<input type="submit" name="buscar" value='Buscar'>
     <!-- &nbsp;&nbsp;<input type='button' name="nuevo" value='Nuevo Efector' onclick="document.location='<?php echo $link_nuevo_efector; ?>';"> -->
  </div>
</div>
<br/>

<table border="0" width="100%" cellspacing="2" cellpadding="2" bgcolor='<?=$bgcolor3?>' align="center">
  <tr>
  	<td colspan="3" align="left" id="ma">
     <table width="100%">
      <tr id="ma">
       <td width="30%" align="left"><b>Total:</b> <?=$total_efectores?></td>       
       <td width="40%" align="right"><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  <tr>
    <td class="mo" width="15%"><a href='<?php echo encode_link("efectores.php",array_merge(array("sort"=>"1","up"=>$up), $link_tmp)); ?>'>CUIE <?php echo icono_sort(1); ?></a></td>
    <td class="mo" width="60%"><a href='<?php echo encode_link("efectores.php",array_merge(array("sort"=>"2","up"=>$up), $link_tmp)); ?>'>Nombre <?php echo icono_sort(2); ?></a></td>      	
    <td class="mo" width="25%"><a href='<?php echo encode_link("efectores.php",array_merge(array("sort"=>"3","up"=>$up), $link_tmp)); ?>'>Ciudad <?php echo icono_sort(3); ?></a></td>
  </tr>

 <?php
  while (!$result->EOF) {
    $link_fila = "";
    if (!empty($pagina)) { // si no se debe retornar a alguna pagina en especial se va a la pagina de detalles
      $link_fila = encode_link($pagina, array("nuevo_id_efector" => $result->fields['id_efe_conv'], "id_parent" => $id_parent));
    } 
    if (empty($link_fila)) {
      $link_fila = encode_link("$html_root/modulos/turnos/vincular_especialidades.php",array("id_efector"=>$result->fields['id_efe_conv']));
    }
    ?>
    <tr <?=atrib_tr()?>>     
     <td onclick="location.href='<?php echo $link_fila; ?>';"><?=$result->fields['cuie']?></td> 
     <td onclick="location.href='<?php echo $link_fila; ?>';"><?=$result->fields['nombre']?></td>
     <td onclick="location.href='<?php echo $link_fila; ?>';"><?=$result->fields['ciudad']?></td>
    </tr>
	  <?php 
    $result->MoveNext();
  }
  ?>
    
</table>
</form>
</div>
<?php fin_pagina(); ?>