
<?php
require_once("../../config.php");

if (isset($_POST["btn-guardar"])) {
  $_id_turno  = $_POST["id_turno"];
  $_id_cie10  = $_POST["id_cie10"];
  $_id_cepsap = $_POST["id_cepsap"];
  $_evolucion = $_POST["evolucion"];
  $query_insert = "INSERT INTO nacer.diagnosticos (id_turno, ";
  if (!empty($_id_cie10)) {
    $query_insert .= "id_cie10, ";
  }
  if (!empty($_id_cepsap)) {
    $query_insert .= "id_cepsap, ";
  }
  $query_insert .= "evolucion) VALUES (".$db->Quote($_POST["id_turno"]).", ";
  if (!empty($_id_cie10)) {
    $query_insert .= $db->Quote($_POST["id_cie10"]).", ";
  }
  if (!empty($_id_cepsap)) {
    $query_insert .= $db->Quote($_POST["id_cepsap"]).", ";
  }
  $query_insert .= $db->Quote($_POST["evolucion"]).") RETURNING id_diagnostico";

  $query_update = "UPDATE nacer.agendas_eventos 
                    SET estado = 'completo' 
                    WHERE id = ".$db->Quote($_POST["id_turno"]);
    
  $res = sql(array($query_insert, $query_update), "al guardar los datos del Turno") or fin_pagina();

  header("Location: $html_root/modulos/turnos/sala_espera.php");
}

echo $html_header;

$id_cie10 = "";
$codigo_cie10 = "";
if (!empty($parametros["nuevo_id_cie10"]) && intval($parametros["nuevo_id_cie10"]) > 0) {
  $id_cie10 = intval($parametros["nuevo_id_cie10"]);
  $_ses_nomenclador["id_cie10"] = $id_cie10;
  phpss_svars_set("_ses_nomenclador", $_ses_nomenclador);
}
elseif (!empty($_ses_nomenclador["id_cie10"])) {
  $id_cie10 = $_ses_nomenclador["id_cie10"];
}
// echo "id=$id_cie10";
if (!empty($id_cie10)) {
  $query = "SELECT 
              *
            FROM
              nacer.cie10
            WHERE
              id10 = $id_cie10";
  $res_cie10 = sql($query, "al obtener los datos del Nomenclador") or fin_pagina();
  $codigo_cie10 = $res_paciente->fields["dec10"];
}

$id_turno = intval($_GET["id_turno"]);

if ($id_turno <= 0) {
  echo "<h1>Falta ingresar el ID!</h1>";
  fin_pagina();
}

$query = "SELECT 
            nacer.agendas_eventos.id,
            nacer.agendas_eventos.titulo,
            nacer.agendas_eventos.inicio,
            nacer.agendas_eventos.fin,
            nacer.agendas_eventos.url,
            nacer.agendas_eventos.id_agenda,
            nacer.agendas_eventos.id_paciente,
            nacer.agendas_eventos.estado,
            uad.beneficiarios.apellido_benef AS paciente_apellido,
            uad.beneficiarios.nombre_benef AS paciente_nombre,
            nacer.agendas_eventos.id_efector,
            nacer.efe_conv.nombre AS efector_nombre,
            nacer.especialidades.id_especialidad,
            nacer.especialidades.nom_titulo AS especialidad_nombre,
            nacer.medicos.id_medico,
            nacer.medicos.apellido AS medico_apellido,
            nacer.medicos.nombre AS medico_nombre,
            nacer.obras_sociales.id_obra_social,
            nacer.obras_sociales.nom_obra_social AS obra_social_nombre
          FROM
            sistema.usu_efec
            LEFT OUTER JOIN nacer.efe_conv ON (sistema.usu_efec.cuie = nacer.efe_conv.cuie)
            RIGHT OUTER JOIN nacer.agendas_eventos ON (sistema.usu_efec.id_usuefect = nacer.agendas_eventos.id_efector)
            LEFT OUTER JOIN uad.beneficiarios ON (nacer.agendas_eventos.id_paciente = uad.beneficiarios.id_beneficiarios)
            LEFT OUTER JOIN nacer.agendas ON (nacer.agendas_eventos.id_agenda = nacer.agendas.id)
            LEFT OUTER JOIN nacer.especialidades_medicos ON (nacer.agendas.id_especialidad_medico = nacer.especialidades_medicos.id)
            LEFT OUTER JOIN nacer.medicos ON (nacer.especialidades_medicos.id_medico = nacer.medicos.id_medico)
            LEFT OUTER JOIN nacer.especialidades ON (nacer.especialidades.id_especialidad = nacer.especialidades_medicos.id_especialidad)
            LEFT OUTER JOIN nacer.obras_sociales ON (nacer.agendas_eventos.id_obra_social = nacer.obras_sociales.id_obra_social)
          WHERE
            nacer.agendas_eventos.estado = 'presente' AND
            nacer.agendas_eventos.id = $id_turno
          ";

$res_turno = sql($query, "al obtener los datos del Turno") or die();
if ($res_turno->recordCount() == 1) {
  $paciente     = $res_turno->fields["paciente_apellido"].' '.$res_turno->fields["paciente_nombre"];
  $efector      = $res_turno->fields["efector_nombre"];
  $especialidad = $res_turno->fields["especialidad_nombre"];
  $medico       = $res_turno->fields["medico_apellido"].' '.$res_turno->fields["medico_nombre"];
  $obra_social  = (empty($res_turno->fields["obra_social_nombre"]) ? "Sin Obra Social" : $res_turno->fields["obra_social_nombre"]);
} else {
  echo "<h1>No se encontró el turno ingresado!</h1>";
  fin_pagina();
}
?>
<br/>
<form action="cargar_diagnostico.php" id="form1" method="POST">
<input type="hidden" name="id_turno" id="id_turno" value="<?php echo $id_turno; ?>" />
<div class="container">
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <h2><?php echo $medico; ?></h2>
  </div>
</div>
<br/>
<div class="row">
  <div class="col-md-5 col-md-offset-1">
    <label>Efector</label>
    <input type="text" name="efector" id="efector" class="form-control" disabled="" value="<?php echo $efector; ?>" />
  </div>
  <div class="col-md-5">
    <label>Especialidad</label>
    <input type="text" name="especialidad" id="especialidad" class="form-control" disabled="" value="<?php echo $especialidad; ?>" />
  </div>
</div>
<br/>
<div class="row">
  <div class="col-md-5 col-md-offset-1">
    <label>Paciente</label>
    <input type="text" name="paciente" id="paciente" class="form-control" disabled="" value="<?php echo $paciente; ?>" />
  </div>
  <div class="col-md-5">
    <label>Obra Social</label>
    <input type="text" name="obra_social" id="obra_social" class="form-control" disabled="" value="<?php echo $obra_social; ?>" />
  </div>
</div>
<br/>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <label>Diagn&oacute;stico con nomenclador CIE10</label>
    <input type="hidden" name="id_cie10" id="id_cie10" value="<?php echo $id_cie10; ?>" />
    <!-- <div class="input-group"> -->
      <input type="text" name="codigo_cie10" class="form-control" id="codigo_cie10" placeholder="Ingrese el c&oacute;digo o descripci&oacute;n de CIE10" value="<?php echo $codigo_cie10; ?>">
<!--       <span class="input-group-btn">
        <button class="btn btn-default" type="button" id="seleccionar_codigo" title="Seleccionar c&oacute;digo">
          &nbsp;<span class="glyphicon glyphicon-search" aria-hidden="true"> </span>&nbsp;Seleccionar
        </button>
      </span>
    </div>
 -->  </div>
</div>
<br/>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <label>Diagn&oacute;stico con nomenclador CEPSAP</label>
    <input type="hidden" name="id_cepsap" id="id_cepsap" value="<?php echo $id_cepsap; ?>" />
      <input type="text" name="codigo_cepsap" class="form-control" id="codigo_cepsap" placeholder="Ingrese el c&oacute;digo o descripci&oacute;n de CEPSAP" value="<?php echo $codigo_cepsap; ?>">
  </div>
</div>
<br/>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <label>Evoluci&oacute;n</label>
    <textarea name="evolucion" id="evolucion" class="form-control" rows="5"></textarea>
  </div>
</div>
<br/>
<div class="row">
  <div class="col-md-3 col-md-offset-3 text-center">
    <input name="btn-guardar" type="submit" class="btn btn-primary" type="button" id="seleccionar_paciente" title="Guardar la datos y volver a la Sala de Espera" value="Guardar" />
  </div>
  <div class="col-md-3 text-center">
    <button id="btn-volver" class="btn btn-default" type="button" title="Volver a la Sala de Espera">Volver</button>
  </div>
</div>
</div>
</form>
<br/>
<script type="text/javascript">
  $(document).ready(function() {
    $( "#codigo_cie10" ).autocomplete({
      source: '<?php echo encode_link("sala_espera_datos.php", array("accion" => "cie10_autocomplete")); ?>',
      minLength: 2,
      select: function( event, ui ) {
        if (ui.item) {
          $('#id_cie10').val(ui.item.id);
        }
        else {
          $('#codigo_cie10').val("");
        }
      }
    });

    $( "#codigo_cepsap" ).autocomplete({
      source: '<?php echo encode_link("sala_espera_datos.php", array("accion" => "cepsap_autocomplete")); ?>',
      minLength: 2,
      select: function( event, ui ) {
        if (ui.item) {
          $('#id_cepsap').val(ui.item.id);
        }
        else {
          $('#codigo_cepsap').val("");
        }
      }
    });

    $("#btn-volver").on('click', function(event) {
      event.preventDefault();
      document.location = 'sala_espera.php';
    });

    $("#form1").submit(function(event) {
      if ($("#id_cie10").val() == '' && $("#id_cepsap").val() == '') {
        event.preventDefault();
        BootstrapDialog.alert("Falta seleccionar el c&oacute;digo del diagn&oacute;stico, complete alguno de los campos de nomenclador (CIE10 o CEPSAP)");
      }
    });

    // $('#seleccionar_codigo').on('click', function() {
    //   document.location = '<?php echo encode_link("nomenclador.php", array("pagina"=>"$html_root/modulos/turnos/cargar_diagnostico.php?id_turno=$id_turno")); ?>';
    // });

  });
</script>
<?php fin_pagina(); ?>