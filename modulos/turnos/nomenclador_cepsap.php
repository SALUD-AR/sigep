<?php
require_once("../../config.php");

echo $html_header;

$extras = array(
            "pagina"    => "",
            "categoria" => ""
          );
variables_form_busqueda("nomenclador_cepsap", $extras);
// print_r($parametros);

// si no viene una pagina por parametro o por post, borro los valores anteriores de la sesion
if (empty($parametros['pagina']) && empty($_POST['pagina']) &&
    !empty($_ses_nomenclador_cepsap["pagina"])
  ) {
  $pagina = "";
  $_ses_nomenclador_cepsap["pagina"] = "";
  phpss_svars_set("_ses_nomenclador_cepsap", $_ses_nomenclador_cepsap);
}

$mensaje_tipo = "warning";
if (!empty($pagina)) {
  $mensaje = "Usted est&aacute; seleccionando un C&oacute;digo de CEPSAP para ser usado en otra p&aacute;gina, haga click en la fila deseada para seleccionarlo y volver a la p&aacute;gina anterior";
  echo '<div class="container alert alert-'.$mensaje_tipo.' alert-dismissible" role="alert" style="width:40%;">
          <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            <p><strong>'.$mensaje.'</strong></p>
        </div>';
}

if ($cmd == ""){
  $cmd = "categorias";
  $_ses_nomenclador_cepsap["cmd"] = $cmd;
  phpss_svars_set("_ses_nomenclador_cepsap", $_ses_nomenclador_cepsap);
}

$datos_barra = array(
  array(
    "descripcion" => "Por Categor&iacute;as", 
    "cmd"         => "categorias", 
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
            "1" => "codigo",
            "2" => "descripcion"
          );
$filtro = array(
            "descripcion" => "Descripci&oacute;n"
          );

if (($cmd == "categorias" || $cmd == "texto") && file_exists("nomenclador_cepsap_{$cmd}.php")) {
    include_once("nomenclador_cepsap_{$cmd}.php");
}

fin_pagina(); 
?>