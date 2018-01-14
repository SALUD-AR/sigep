<?php
require_once("../../config.php");

$extras = array(
            "pagina" => "" // pagina a la que debe retornar despues de seleccionar el paciente
          );

variables_form_busqueda("listado_pacientes", $extras);

// si no viene una pagina por parametro o por post, borro los valores anteriores de la sesion
if (empty($parametros['pagina']) && empty($_POST['pagina']) &&
    !empty($_ses_listado_pacientes["pagina"])
  ) {
  $pagina = "";
  $_ses_listado_pacientes["pagina"] = "";
  phpss_svars_set("_ses_listado_pacientes", $_ses_listado_pacientes);
}


$orden = array(
        "default" => "1",
        "1" => "clave_beneficiario",
        "2" => "apellido_benef",
        "3" => "nombre_benef",
        "4" => "numero_doc",
        "5" => "fecha_nacimiento_benef",
        "6" => "nombre",
        "7" => "cuie"
       );
$filtro = array(
        "apellido_benef"     => "Apellido",
        "nombre_benef"       => "Nombre",
		    "numero_doc"         => "DNI",
        "nombre"             => "Nombre Efector",
        "cuie"               => "CUIE",
        "clave_beneficiario" => "Clave Beneficiario"
       );

$sql_tmp="SELECT 
      uad.beneficiarios.id_beneficiarios, 
      uad.beneficiarios.clave_beneficiario, 
      uad.beneficiarios.apellido_benef, 
      uad.beneficiarios.nombre_benef, 
      uad.beneficiarios.fecha_nacimiento_benef, 
      uad.beneficiarios.fecha_inscripcion, 
      uad.beneficiarios.estado_envio, 
      uad.beneficiarios.numero_doc, 
      nacer.efe_conv.nombre 
      FROM 
      uad.beneficiarios
      LEFT JOIN nacer.efe_conv ON uad.beneficiarios.cuie_ea=efe_conv.cuie";

echo $html_header;

$mensaje_tipo = "warning";
if (!empty($pagina)) {
  $mensaje = "Usted est&aacute; seleccionando un Paciente para ser usado en otra p&aacute;gina, haga click en la fila deseada para seleccionarlo y volver a la p&aacute;gina anterior";
}

?>
<?php if (!empty($mensaje)) { ?>
<div class="container alert alert-<?php echo $mensaje_tipo; ?> alert-dismissible" role="alert" style="width:40%;">
  <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
    <p><strong><?php echo $mensaje; ?></strong></p>
</div>
<?php } ?>

<div class="container">
<form name="form1" action="pacientes.php" method="POST">
<?php if (!empty($pagina)) { ?>
  <input type="hidden" name="pagina" value="<?php echo $pagina; ?>" />
<?php } ?>

<br/>
<div class="row">
  <div class="col-md-8 col-md-offset-2 text-center">
    <?php 
      $link_tmp = array("pagina" => $pagina);
      list($sql,$total_pacientes,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");
      $result = sql($sql) or fin_pagina();
      if (!empty($pagina)) {
        $link_nuevo_paciente = encode_link("$html_root/modulos/inscripcion/ins_admin_old.php",array("pagina_viene_1"=>encode_link("$html_root/modulos/turnos/pacientes.php", array("pagina" => $pagina))));
      } else {
        $link_nuevo_paciente = encode_link("$html_root/modulos/inscripcion/ins_admin_old.php",array("pagina_viene_1"=>"$html_root/modulos/turnos/pacientes.php"));
      }

    ?>
     &nbsp;&nbsp;<input type="submit" name="buscar" value='Buscar'>
     &nbsp;&nbsp;<input type='button' name="nuevo" value='Nuevo Paciente' onclick="document.location='<?php echo $link_nuevo_paciente; ?>';">
  </div>
</div>
<br/>

<table border="0" width="100%" cellspacing="2" cellpadding="2" bgcolor='<?=$bgcolor3?>' align="center">
  <tr>
  	<td colspan="7" align="left" id="ma">
     <table width="100%">
      <tr id="ma">
       <td width="30%" align="left"><b>Total:</b> <?=$total_pacientes?></td>       
       <td width="40%" align="right"><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  <tr>
    <td class="mo"><a href='<?php echo encode_link("pacientes.php",array_merge(array("sort"=>"1","up"=>$up), $link_tmp)); ?>'>Clave Beneficiario</a></td>
    <td class="mo"><a href='<?php echo encode_link("pacientes.php",array_merge(array("sort"=>"2","up"=>$up), $link_tmp)); ?>'>Apellido</a></td>      	
    <td class="mo"><a href='<?php echo encode_link("pacientes.php",array_merge(array("sort"=>"3","up"=>$up), $link_tmp)); ?>'>Nombre</a></td>
    <td class="mo"><a href='<?php echo encode_link("pacientes.php",array_merge(array("sort"=>"4","up"=>$up), $link_tmp)); ?>'>DNI</a></td>
    <td class="mo"><a href='<?php echo encode_link("pacientes.php",array_merge(array("sort"=>"5","up"=>$up), $link_tmp)); ?>'>Fecha Nacimiento</a></td>
    <td class="mo"><a href='<?php echo encode_link("pacientes.php",array_merge(array("sort"=>"6","up"=>$up), $link_tmp)); ?>'>Nombre Efector</a></td>
    <td class="mo"><a href='<?php echo encode_link("pacientes.php",array_merge(array("sort"=>"7","up"=>$up), $link_tmp)); ?>'>CUIE</a></td>
  </tr>

 <?php
  while (!$result->EOF) {
    $link_fila = "";
    if (!empty($pagina)) { // si no se debe retornar a alguna pagina en especial se va a la pagina de detalles
      $link_fila = encode_link($pagina, array("nuevo_id_paciente" => $result->fields['id_beneficiarios']));
    } 
    if (empty($link_fila)) {
      $link_fila = encode_link("$html_root/modulos/inscripcion/ins_admin_old.php",array("id_planilla"=>$result->fields['id_beneficiarios'],"pagina_viene_1"=>"$html_root/modulos/turnos/pacientes.php"));
    }
    ?>
    <tr <?=atrib_tr()?>>     
     <td onclick="location.href='<?php echo $link_fila; ?>';"><?=$result->fields['clave_beneficiario']?></td>
     <td onclick="location.href='<?php echo $link_fila; ?>';"><?=$result->fields['apellido_benef']?></td>
     <td onclick="location.href='<?php echo $link_fila; ?>';"><?=$result->fields['nombre_benef']?></td>
     <td onclick="location.href='<?php echo $link_fila; ?>';"><?=$result->fields['numero_doc']?></td>
     <td onclick="location.href='<?php echo $link_fila; ?>';"><?=Fecha($result->fields['fecha_nacimiento_benef'])?></td>     
     <td onclick="location.href='<?php echo $link_fila; ?>';"><?=$result->fields['nombre']?></td> 
     <td onclick="location.href='<?php echo $link_fila; ?>';"><?=$result->fields['cuie']?></td> 
    </tr>
	  <?php 
    $result->MoveNext();
  }
  ?>
    
</table>
</form>
</div>
<?php fin_pagina(); ?>