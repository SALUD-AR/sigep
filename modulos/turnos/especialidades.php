<?php
require_once("../../config.php");

echo $html_header;

$extras = array(
            "pagina"     => "", // pagina a la que debe retornar despues de seleccionar la especialidad
            "id_parent"  => ""  // id del elemento al que se debe asociar la especialidad
          );

variables_form_busqueda("especialidades", $extras);

// si no viene una pagina por parametro o por post, borro los valores anteriores de la sesion
if (empty($parametros['pagina']) && empty($_POST['pagina']) &&
    (!empty($_ses_especialidades["pagina"]) || !empty($_ses_especialidades["id_parent"]))
  ) {
  $pagina = $id_parent = "";
  $_ses_especialidades["pagina"] = "";
  $_ses_especialidades["id_parent"] = "";
  phpss_svars_set("_ses_especialidades", $_ses_especialidades);
}

$orden = array(
            "default" => "1",
            "1" => "nom_titulo",
            "2" => "especialidad"
          );
$filtro = array(
            "nom_titulo" => "T&iacute;tulo"
          );
$sql_tmp="SELECT * FROM nacer.especialidades";

$mensaje_tipo = "warning";

if (!empty($pagina)) {
  $mensaje = "Usted est&aacute; seleccionando una Especialidad para ser usada en otra p&aacute;gina, haga click en la fila deseada para seleccionarla y volver a la p&aacute;gina anterior";
}

?>
<?php if (!empty($mensaje)) { ?>
<div class="container alert alert-<?php echo $mensaje_tipo; ?> alert-dismissible" role="alert" style="width:40%;">
  <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
    <p><strong><?php echo $mensaje; ?></strong></p>
</div>
<?php } ?>
<br/>
<form name="form1" action="especialidades.php" method="POST">
<?php if (!empty($pagina)) { ?>
  <input type="hidden" name="pagina" value="<?php echo $pagina; ?>" />
  <input type="hidden" name="id_parent" value="<?php echo $id_parent; ?>" />
<?php } ?>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
  <tr>
    <td align=center>
	    <?php 
      $link_tmp = array("pagina" => $pagina, "id_parent" => $id_parent);
      list($sql,$total_especialidades,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");
      ?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;<input type='button' name="nueva_especialidad" value='Nueva Especialidad' onclick="document.location='especialidad_admin.php'">
	  </td>
  </tr>
</table>

<br/>
<?php $result = sql($sql,"No se ejecuto en la consulta principal") or fin_pagina();?>

<table border="0" width="50%" cellspacing="2" cellpadding="2" bgcolor="<?php echo $bgcolor3; ?>" align="center">
  <tr>
  	<td colspan="2" align="left" id="ma">
      <table width="100%">
        <tr id="ma">
          <td width="30%" align="left"><b>Total:</b> <?php echo $total_especialidades; ?></td>       
          <td width="70%" align="right"><?php echo $link_pagina; ?></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td align="right" id="mo" width="70%"><a id="mo" href="<?php echo encode_link("especialidades.php",array_merge(array("sort"=>"1","up"=>$up), $link_tmp)); ?>">T&iacute;tulo</a></td>
    <td align="right" id="mo" width="30%"><a id="mo" href="<?php echo encode_link("especialidades.php",array_merge(array("sort"=>"2","up"=>$up), $link_tmp)); ?>">Especialidad</a></td>
  </tr>
  <?php
  while (!$result->EOF) {
    $link_fila = "";
    if (!empty($pagina)) { // si no se debe retornar a alguna pagina en especial se va a la pagina de edicion
      // if ($pagina == "medico_admin") {
        $link_fila = encode_link($pagina, array("nuevo_id_especialidad" => $result->fields['id_especialidad'], "id_parent" => $id_parent));
      // }
    } 
    if (empty($link_fila)) {
      $link_fila = encode_link("especialidad_admin.php",array("id_especialidad" => $result->fields['id_especialidad']));
    }
  ?>
    <tr <?php echo atrib_tr();?>>
      <td onclick="location.href='<?php echo $link_fila; ?>'"><?php echo $result->fields['nom_titulo']; ?></td>
      <td onclick="location.href='<?php echo $link_fila; ?>'" align="center"><?php echo $sino[$result->fields['especialidad']]; ?></td>
    </tr>
	<?php
    $result->MoveNext();
  }
  ?>
</table>
</form>
<?php fin_pagina(); ?>