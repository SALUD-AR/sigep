<?php
require_once("../../config.php");


$extras = array(
            "pagina" => "" // pagina a la que debe retornar despues de seleccionar el paciente
          );

variables_form_busqueda("obras_sociales", $extras);

// si no viene una pagina por parametro o por post, borro los valores anteriores de la sesion
if (empty($parametros['pagina']) && empty($_POST['pagina']) &&
    !empty($_ses_obras_sociales["pagina"])
  ) {
  $pagina = "";
  $_ses_obras_sociales["pagina"] = "";
  phpss_svars_set("_ses_obras_sociales", $_ses_obras_sociales);
}

$orden = array(
            "default" => "1",
            "1" => "nom_obra_social",
            "2" => "sigla"
          );
$filtro = array(
            "nom_obra_social" => "Nombre",
		        "sigla" => "Sigla"
          );
$sql_tmp="SELECT * FROM nacer.obras_sociales";

echo $html_header;

$mensaje_tipo = "warning";
if (!empty($pagina)) {
  $mensaje = "Usted est&aacute; seleccionando una Obra Social para ser usada en otra p&aacute;gina, haga click en la fila deseada para seleccionarla y volver a la p&aacute;gina anterior";
}

?>
<?php if (!empty($mensaje)) { ?>
<div class="container alert alert-<?php echo $mensaje_tipo; ?> alert-dismissible" role="alert" style="width:40%;">
  <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
    <p><strong><?php echo $mensaje; ?></strong></p>
</div>
<?php } ?>
<br/>
<div class="container">
<form name="form1" action="obras_sociales.php" method="POST">
<?php if (!empty($pagina)) { ?>
  <input type="hidden" name="pagina" value="<?php echo $pagina; ?>" />
<?php } ?>
<table cellspacing="2" cellpadding="2" border="0" width="100%" align="center">
  <tr>
    <td align="center">
	    <?php 
      $link_tmp = array("pagina" => $pagina);
      list($sql,$total_obras_sociales,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");
      ?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;<input type='button' name="nueva_obra_social" value='Nueva Obra Social' onclick="document.location='obra_social_admin.php'">
	  </td>
  </tr>
</table>

<br/>
<?php $result = sql($sql,"No se ejecuto en la consulta principal") or fin_pagina();?>

<table border="0" width="80%" cellspacing="2" cellpadding="2" bgcolor="<?php echo $bgcolor3; ?>" align="center">
  <tr>
  	<td colspan="2" align="left" id="ma">
      <table width="100%">
        <tr id="ma">
          <td width="30%" align="left"><b>Total:</b> <?php echo $total_obras_sociales; ?></td>       
          <td width="70%" align="right"><?php echo $link_pagina; ?></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td id="mo"><a id="mo" href="<?php echo encode_link("obras_sociales.php",array("sort"=>"1","up"=>$up))?>">Nombre</a></td>
    <td id="mo"><a id="mo" href="<?php echo encode_link("obras_sociales.php",array("sort"=>"2","up"=>$up))?>">Sigla</a></td>
  </tr>
  <?php
  while (!$result->EOF) {
    $link_fila = "";
    if (!empty($pagina)) { // si no se debe retornar a alguna pagina en especial se va a la pagina de detalles
      $link_fila = encode_link($pagina, array("nuevo_id_obra_social" => $result->fields['id_obra_social']));
    } 
    if (empty($link_fila)) {
      $link_fila = encode_link("obra_social_admin.php",array("id_obra_social"=>$result->fields['id_obra_social']));
    }
  ?>
  <tr <?php echo atrib_tr();?>>
    <td onclick="location.href='<?php echo $link_fila?>';"><?php echo $result->fields['nom_obra_social']; ?></td>
    <td onclick="location.href='<?php echo $link_fila?>';"><?php echo $result->fields['sigla']; ?></td>
  </tr>    
	<?php
    $result->MoveNext();
  }
  ?>
  	
</table>
</form>
</div>
<?php fin_pagina(); ?>