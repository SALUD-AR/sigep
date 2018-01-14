<?php
require_once("../../config.php");

echo $html_header;

$extras = array(
            "pagina"          => "",
            "id_especialidad" => ""
          );

variables_form_busqueda("medicos", $extras);

// si no viene una pagina por parametro o por post, borro los valores anteriores de la sesion
if (empty($parametros['pagina']) && empty($_POST['pagina']) &&
    (!empty($_ses_medicos["pagina"]) || !empty($_ses_medicos["id_especialidad"]))
  ) {
  $pagina = $id_especialidad = "";
  $_ses_medicos["pagina"] = "";
  $_ses_medicos["id_especialidad"] = "";
  phpss_svars_set("_ses_medicos", $_ses_medicos);
}

$orden = array(
            "default" => "1",
            "1" => "apellido",
            "2" => "nombre"
          );
$filtro = array(
            "apellido" => "Apellido",
		        "nombre" => "Nombre"
          );
$sql_tmp="SELECT * FROM nacer.medicos";

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
<form name="form1" action="medicos.php" method="POST">
<?php if (!empty($pagina)) { ?>
  <input type="hidden" name="pagina" value="<?php echo $pagina; ?>" />
  <input type="hidden" name="id_especialidad" value="<?php echo $id_especialidad; ?>" />
<?php } ?>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
  <tr>
    <td align=center>
	    <?php 
      $link_tmp = array("pagina" => $pagina, "id_especialidad" => $id_especialidad);
      list($sql,$total_medicos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");
      ?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;<input type='button' name="nuevo_medico" value='Nuevo M&eacute;dico' onclick="document.location='medico_admin.php'">
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
          <td width="30%" align="left"><b>Total:</b> <?php echo $total_medicos; ?></td>       
          <td width="70%" align="right"><?php echo $link_pagina; ?></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td align="right" id="mo"><a id="mo" href="<?php echo encode_link("medicos.php",array_merge(array("sort"=>"1","up"=>$up), $link_tmp)); ?>">Apellido</a></td>
    <td align="right" id="mo"><a id="mo" href="<?php echo encode_link("medicos.php",array_merge(array("sort"=>"2","up"=>$up), $link_tmp)); ?>">Nombre</a></td>
  </tr>
  <?php
  while (!$result->EOF) {
    $link_fila = "";
    if (!empty($pagina)) { // si no se debe retornar a alguna pagina en especial se va a la pagina de edicion
      if ($pagina == "especialidad_admin") {
        $link_fila = encode_link("especialidad_admin.php", array("nuevo_id_medico" => $result->fields['id_medico'], "id_especialidad" => $id_especialidad));
      }
    } 
    if (empty($link_fila)) {
   	  $link_fila = encode_link("medico_admin.php",array("id_medico"=>$result->fields['id_medico']));
    }
  ?>
  <tr <?php echo atrib_tr();?>>
    <td onclick="location.href='<?php echo $link_fila; ?>'"><?php echo $result->fields['apellido']?></td>
    <td onclick="location.href='<?php echo $link_fila; ?>'"><?php echo $result->fields['nombre']?></td>
  </tr>    
	<?php
    $result->MoveNext();
  }
  ?>
  	
</table>
</form>
<?php fin_pagina(); ?>