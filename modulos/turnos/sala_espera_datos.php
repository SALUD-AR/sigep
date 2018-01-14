<?php

/* CAMBIOS:

*** cambiar tabla a uad.beneficiarios
*** agregar obra social a una lista (no reemplazar) y mostrar como select
*** busqueda de nomenclador con ajax
permitir sobreturnos
*** cambio de estado a presente (iconos y color de fondo)
*** agregar nuevo paciente
*** guardar obra social junto con el turno

*/

require_once("../../config.php");
require_once("agenda_funciones.php");

$extras = array(
            "id_efector"      => "",
            "id_especialidad" => ""
          );
variables_form_busqueda("sala_espera", $extras);
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(E_ALL);

if (isset($parametros["accion"])) {
  switch ($parametros["accion"]) {
    case 'cargar_especialidades':
      $ret = '<option value="">Todas</option>';
      $id_efector = intval($_POST["id_efector"]);

      if ($id_efector > 0) {
        $query = "SELECT 
                nacer.especialidades.id_especialidad,
                nacer.especialidades.nom_titulo
              FROM
                nacer.especialidades_efectores
                LEFT OUTER JOIN nacer.especialidades ON (nacer.especialidades_efectores.id_especialidad = nacer.especialidades.id_especialidad)
              WHERE
                nacer.especialidades_efectores.id_efector = $id_efector
              ORDER BY
                nacer.especialidades.nom_titulo";
        
        $res_especialidades = sql($query) or die();
        $color_index = 0;
        while (!$res_especialidades->EOF) {
          $ret .= '<option value="' . $res_especialidades->fields['id_especialidad'] . '" data-color="' . $agenda_colores[$color_index] . '">';
          $ret .= $res_especialidades->fields['nom_titulo'] . '</option>';
          $res_especialidades->MoveNext();
          $color_index++;
          if ($color_index == count($agenda_colores)) {
            $color_index = 0;
          }
        }
        $_ses_sala_espera["id_especialidad"] = "";
        phpss_svars_set("_ses_sala_espera", $_ses_sala_espera);
      }
      else {
        $_ses_sala_espera["id_efector"] = "";
        $_ses_sala_espera["id_especialidad"] = "";
        phpss_svars_set("_ses_sala_espera", $_ses_sala_espera);
      }
      echo utf8_encode($ret);
      break;
    
    case 'cargar_medicos':
      $id_especialidad = intval($_POST["id_especialidad"]);
      if ($id_especialidad > 0) {
        cargar_medicos($id_especialidad);
      }
      else {
        $_ses_sala_espera["id_especialidad"] = "";
        phpss_svars_set("_ses_sala_espera", $_ses_sala_espera);
      }
      break;

    case 'cie10_autocomplete':
      $buscar = $_GET["term"];
      $res_json = array();
      $query = "SELECT 
                  *
                FROM
                  nacer.cie10
                WHERE
                  id10 ILIKE ".$db->Quote($buscar)." OR
                  dec10 ILIKE ".$db->Quote("%".$buscar."%")."
                  ";
      // echo $query;
      $res_cie10 = sql($query, "al obtener los datos del nomenclador") or die();
      while (!$res_cie10->EOF) {
        $res_json[] = array(
          "id"            => $res_cie10->fields["id10"],
          "value"         => utf8_encode($res_cie10->fields["id10"]." ".$res_cie10->fields["dec10"]),
          "label"         => utf8_encode($res_cie10->fields["id10"]." ".$res_cie10->fields["dec10"])
        );
        $res_cie10->MoveNext();
      }
      echo json_encode($res_json);
      break;

    case 'cepsap_autocomplete':
      $buscar = $_GET["term"];
      $res_json = array();
      $query = "SELECT 
                  *
                FROM
                  nacer.cepsap_items
                WHERE
                  codigo ILIKE ".$db->Quote($buscar)." OR
                  descripcion ILIKE ".$db->Quote("%".$buscar."%")."
                  ";
      // echo $query;
      $res_cepsap = sql($query, "al obtener los datos del nomenclador") or die();
      while (!$res_cepsap->EOF) {
        $res_json[] = array(
          "id"            => $res_cepsap->fields["id"],
          "value"         => utf8_encode($res_cepsap->fields["codigo"]." ".$res_cepsap->fields["descripcion"]),
          "label"         => utf8_encode($res_cepsap->fields["codigo"]." ".$res_cepsap->fields["descripcion"])
        );
        $res_cepsap->MoveNext();
      }
      echo json_encode($res_json);
      break;

    case 'agenda_events':
      $res_json = array();
      $start = $_POST["start"];
      $end = $_POST["end"];
      // $agendas_ids = $_POST["agendas_ids"];
      $id_efector = $_POST["id_efector"];
      $id_especialidad = $_POST["id_especialidad"];
      $modo_sala_espera = $_POST["modo_sala_espera"];
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
                  LEFT OUTER JOIN nacer.obras_sociales ON (nacer.agendas_eventos.id_obra_social = nacer.obras_sociales.id_obra_social)
                  LEFT OUTER JOIN nacer.especialidades ON (nacer.especialidades.id_especialidad = nacer.especialidades_medicos.id_especialidad)
                WHERE
                  nacer.agendas_eventos.estado IN ('presente', 'completo') AND 
                  nacer.agendas_eventos.inicio >= '{$start}' AND 
                  nacer.agendas_eventos.fin <= '{$end}'";

      if (!empty($id_efector)) {
        $query .= " AND nacer.agendas_eventos.id_efector = {$id_efector}";
      }
      if (!empty($id_especialidad)) {
        $query .= " AND nacer.especialidades.id_especialidad = {$id_especialidad}";
      }
      $res_eventos = sql($query) or die($query);
      while (!$res_eventos->EOF) {
        $res_json[] = array(
          "id"            => $res_eventos->fields["id"],
          "start"         => $res_eventos->fields["inicio"],
          "end"           => $res_eventos->fields["fin"],
          "title"         => utf8_encode($res_eventos->fields["paciente_apellido"].' '.$res_eventos->fields["paciente_nombre"]),
          "url"           => $res_eventos->fields["url"],
          "estado"        => $res_eventos->fields["estado"],
          "id_agenda"     => $res_eventos->fields["id_agenda"],
          "efector"       => utf8_encode($res_eventos->fields["efector_nombre"]),
          "especialidad"  => utf8_encode($res_eventos->fields["especialidad_nombre"]),
          "medico"        => utf8_encode($res_eventos->fields["medico_apellido"].' '.$res_eventos->fields["medico_nombre"]),
          "obra_social"   => (empty($res_eventos->fields["obra_social_nombre"]) ? "Sin Obra Social" : utf8_encode($res_eventos->fields["obra_social_nombre"]))
        );
        $res_eventos->MoveNext();
      }
      echo json_encode($res_json);
      break;

    case 'agenda_add':
      $id_agenda = $_POST["id_agenda"];
      $id_paciente = $_POST["id_paciente"];
      $id_efector = $_POST["id_efector"];
      $inicio = $_POST["inicio"];
      $fin = $_POST["fin"];
      $query="INSERT INTO nacer.agendas_eventos
                (titulo, inicio, fin, url, id_agenda, id_paciente, id_efector, estado)
              VALUES
                (
                '',
                ".$db->Quote($inicio).", 
                ".$db->Quote($fin).",
                '',
                ".$db->Quote($id_agenda).",
                ".$db->Quote($id_paciente).",
                ".$db->Quote($id_efector).",
                'activo'
                )
              RETURNING id
          ";
        
      $res_insert = sql($query) or die();
      
      $id_evento = $res_insert->fields['id'];

      echo (($id_evento > 0) ? $id_evento : 0);
      break;
    case 'cancelar_turno':
      $id_turno = $_POST["id_turno"];
      $query="UPDATE nacer.agendas_eventos SET estado = 'cancelado' WHERE id = ".$db->Quote($id_turno);
        
      $res_update = sql($query) or die(-1);
      echo ($db->Affected_Rows() == 1) ? 1 : 0;
      break;
    case 'datos_paciente':
      $dni = intval($_POST["dni"]);
      $res_json = array();
      if ($dni > 0) {
        $query = "SELECT *
              FROM 
                nacer.smiafiliados
              WHERE
                afidni = '$dni'";
        $res_paciente = sql($query) or die();
        // TODO: guardar los parametros del form_busqueda si hay mas de un resultado
        while (!$res_paciente->EOF) {
          $res_json[] = array(
            "id"                => $res_paciente->fields["id_smiafiliados"],
            "apellido"          => utf8_encode($res_paciente->fields["afiapellido"]),
            "nombre"            => utf8_encode($res_paciente->fields["afinombre"]),
            "documento"         => $res_paciente->fields["afitipodoc"].' '.$res_paciente->fields["afidni"],
            "sexo"              => ((trim($res_paciente->fields["afisexo"]) == 'M') ? 'Masculino' : 'Femenino'),
            "fechanac"          => Fecha($res_paciente->fields["afifechanac"]),
            "clavebeneficiario" => $res_paciente->fields["clavebeneficiario"],
            "cuieefector"       => $res_paciente->fields["cuieefectorasignado"],
            "cuielugaratencion" => $res_paciente->fields["cuielugaratencionhabitual"],
            "activo"            => htmlentities($sino[trim($res_paciente->fields["activo"])])
          );
          $res_paciente->MoveNext();
        }
      }
      echo json_encode($res_json);
      break;

    default:
      # code...
      break;
  }
}
?>