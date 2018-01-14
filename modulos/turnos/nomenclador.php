<?php
require_once("../../config.php");

echo $html_header;

$extras = array(
            "pagina"    => "",
            "capitulo"  => "",
            "grupo"     => "",
            "categoria" => ""
          );
variables_form_busqueda("nomenclador", $extras);
// print_r($parametros);

// si no viene una pagina por parametro o por post, borro los valores anteriores de la sesion
if (empty($parametros['pagina']) && empty($_POST['pagina']) &&
    !empty($_ses_nomenclador["pagina"])
  ) {
  $pagina = "";
  $_ses_nomenclador["pagina"] = "";
  phpss_svars_set("_ses_nomenclador", $_ses_nomenclador);
}

$mensaje_tipo = "warning";
if (!empty($pagina)) {
  $mensaje = "Usted est&aacute; seleccionando un C&oacute;digo de CIE10 para ser usado en otra p&aacute;gina, haga click en la fila deseada para seleccionarlo y volver a la p&aacute;gina anterior";
  echo '<div class="container alert alert-'.$mensaje_tipo.' alert-dismissible" role="alert" style="width:40%;">
          <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            <p><strong>'.$mensaje.'</strong></p>
        </div>';
}

if ($cmd == ""){
  $cmd = "capitulos";
  $_ses_nomenclador["cmd"] = $cmd;
  phpss_svars_set("_ses_nomenclador", $_ses_nomenclador);
}

$datos_barra = array(
  array(
    "descripcion" => "Por Cap&iacute;tulos", 
    "cmd"         => "capitulos", 
    "extra"       => array(
                      "pagina" => $pagina
                    )
  ),
  array(
    "descripcion" => "Por C&oacute;digo o Descripci&oacute;n", 
    "cmd"         => "texto", 
    "extra"       => array(
                      "pagina" => $pagina)
    )
);

echo "<br/>";

generar_barra_nav($datos_barra);

$orden = array(
            "default" => "1",
            "1" => "id10",
            "2" => "dec10"
          );
$filtro = array(
            "id10" => "C&oacute;digo",
            "dec10" => "Descripci&oacute;n"
          );

if (($cmd == "capitulos" || $cmd == "texto") && file_exists("nomenclador_{$cmd}.php")) {
    include_once("nomenclador_{$cmd}.php");
}

fin_pagina(); 
?>