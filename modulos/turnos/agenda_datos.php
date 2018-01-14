<?php
require_once("../../config.php");
require_once("agenda_funciones.php");

$extras = array(
            "id_efector"      => "",
            "id_especialidad" => "",
            "id_paciente"     => "",
            "id_obra_social"  => ""
          );
variables_form_busqueda("agenda_turnos", $extras);
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(E_ALL);

if (isset($parametros["accion"])) {
  switch ($parametros["accion"]) {
    case 'cargar_especialidades':
      $ret = '<option value=""></option>';
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
        while (!$res_especialidades->EOF) {
          $ret .= '<option value="' . $res_especialidades->fields['id_especialidad'] . '">';
          $ret .= $res_especialidades->fields['nom_titulo'] . '</option>';
          $res_especialidades->MoveNext();
        }
        $_ses_agenda_turnos["id_especialidad"] = "";
        phpss_svars_set("_ses_agenda_turnos", $_ses_agenda_turnos);
      }
      else {
        $_ses_agenda_turnos["id_efector"] = "";
        $_ses_agenda_turnos["id_especialidad"] = "";
        phpss_svars_set("_ses_agenda_turnos", $_ses_agenda_turnos);
      }
      echo utf8_encode($ret);
      break;
    
    case 'cargar_medicos':
      $id_especialidad = intval($_POST["id_especialidad"]);
      if ($id_especialidad > 0) {
        $_ses_agenda_turnos["id_especialidad"] = $id_especialidad;
        cargar_medicos($id_especialidad);
      }
      else {
        $_ses_agenda_turnos["id_especialidad"] = "";
      }
      phpss_svars_set("_ses_agenda_turnos", $_ses_agenda_turnos);
      break;

    case 'agenda_events':
      $res_json = array();
      $start = $_POST["start"];
      $end = $_POST["end"];
      $agendas_ids = $_POST["agendas_ids"];
      // $id_efector = $_POST["id_efector"];
      // $id_especialidad = $_POST["id_especialidad"];
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
                  nacer.agendas_eventos.estado IN ('activo', 'presente') AND 
                  nacer.agendas_eventos.inicio >= '{$start}' AND 
                  nacer.agendas_eventos.fin <= '{$end}' AND
                  nacer.agendas_eventos.id_agenda IN ({$agendas_ids})";
      $res_eventos = sql($query) or die($query);
      while (!$res_eventos->EOF) {
        $res_json[] = array(
          "id"            => $res_eventos->fields["id"],
          "start"         => $res_eventos->fields["inicio"],
          "end"           => $res_eventos->fields["fin"],
          "title"         => utf8_encode($res_eventos->fields["paciente_apellido"].' '.$res_eventos->fields["paciente_nombre"]),
          "url"           => $res_eventos->fields["url"],
          "id_agenda"     => $res_eventos->fields["id_agenda"],
          "estado"        => $res_eventos->fields["estado"],
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
      $id_obra_social = $_POST["id_obra_social"];
      $id_efector = $_POST["id_efector"];
      $inicio = $_POST["inicio"];
      $fin = $_POST["fin"];
      $query="INSERT INTO nacer.agendas_eventos
                (titulo, inicio, fin, url, id_agenda, id_paciente, id_obra_social, id_efector, estado)
              VALUES
                (
                '',
                ".$db->Quote($inicio).", 
                ".$db->Quote($fin).",
                '',
                ".$db->Quote($id_agenda).",
                ".$db->Quote($id_paciente).",
                ".(empty($id_obra_social) ? "NULL" : $db->Quote($id_obra_social)).",
                ".$db->Quote($id_efector).",
                'activo'
                )
              RETURNING id
          ";
        
      $res_insert = sql($query) or die($query);
      
      $id_evento = $res_insert->fields['id'];

      echo (($id_evento > 0) ? $id_evento : 0);
      break;
    case 'cancelar_turno':
      $id_turno = $_POST["id_turno"];
      $query="UPDATE nacer.agendas_eventos SET estado = 'cancelado' WHERE id = ".$db->Quote($id_turno);
        
      $res_update = sql($query) or die(-1);
      echo ($db->Affected_Rows() == 1) ? 1 : 0;
      break;
    case 'turno_presente':
      $id_turno = $_POST["id_turno"];
      $query="UPDATE nacer.agendas_eventos SET estado = 'presente' WHERE id = ".$db->Quote($id_turno);
        
      $res_update = sql($query) or die(-1);
      echo ($db->Affected_Rows() == 1) ? 1 : 0;
      break;
    case 'turno_activo':
      $id_turno = $_POST["id_turno"];
      $query="UPDATE nacer.agendas_eventos SET estado = 'activo' WHERE id = ".$db->Quote($id_turno);
        
      $res_update = sql($query) or die(-1);
      echo ($db->Affected_Rows() == 1) ? 1 : 0;
      break;
    case 'datos_paciente':
      $dni = intval($_POST["dni"]);
      $res_json = array();
      if ($dni > 0) {
        $query = "SELECT *
                  FROM 
                    uad.beneficiarios
                  WHERE
                    numero_doc = '$dni'";
        $res_paciente = sql($query) or die();
        if ($res_paciente->recordCount()==1) {
          $_ses_agenda_turnos["id_paciente"] = $res_paciente->fields["id_beneficiarios"];
          phpss_svars_set("_ses_agenda_turnos", $_ses_agenda_turnos);
        }
        while (!$res_paciente->EOF) {
          $res_json[] = array(
            "id"                => $res_paciente->fields["id_beneficiarios"],
            "apellido"          => utf8_encode($res_paciente->fields["apellido_benef"]),
            "nombre"            => utf8_encode($res_paciente->fields["nombre_benef"]),
            "documento"         => $res_paciente->fields["tipo_documento"].' '.$res_paciente->fields["numero_doc"],
            "sexo"              => ((trim($res_paciente->fields["sexo"]) == 'M') ? 'Masculino' : 'Femenino'),
            "fechanac"          => Fecha($res_paciente->fields["fecha_nacimiento_benef"]),
            "clavebeneficiario" => $res_paciente->fields["clave_beneficiario"],
            "cuieefector"       => $res_paciente->fields["cuie_ea"],
            "cuielugaratencion" => $res_paciente->fields["cuie_ah"],
            "activo"            => htmlentities($sino[trim($res_paciente->fields["activo"])])
          );
          $res_paciente->MoveNext();
        }
      }
      echo json_encode($res_json);
      break;

    case 'datos_obra_social':
      $id_paciente = intval($_POST["id_paciente"]);
      // $res_json = array();
      $ret = '<option value="">Sin Obra Social</option>';
      if ($id_paciente > 0) {
        $query = "SELECT 
                    nacer.obras_sociales.id_obra_social,
                    nacer.obras_sociales.nom_obra_social
                  FROM
                    nacer.obras_sociales
                  RIGHT OUTER JOIN nacer.obras_sociales_pacientes ON (nacer.obras_sociales.id_obra_social = nacer.obras_sociales_pacientes.id_obra_social)
                  WHERE
                    nacer.obras_sociales_pacientes.id_paciente = $id_paciente
                  ORDER BY nacer.obras_sociales_pacientes.fecha_ultimo_uso DESC";
        $res_obra_social = sql($query) or die();

        while (!$res_obra_social->EOF) {
          $selected = "";
          if (!empty($_ses_agenda_turnos["id_obra_social"]) && $_ses_agenda_turnos["id_obra_social"] == $res_obra_social->fields['id_obra_social']) {
            $selected = " selected";
          }

          $ret .= '<option value="' . $res_obra_social->fields['id_obra_social'] . '"';
          $ret .= $selected . '>' . $res_obra_social->fields['nom_obra_social'] . '</option>';
          $res_obra_social->MoveNext();
        }
      }
      echo utf8_encode($ret);
      break;

    case 'guardar_obra_social':
      $id_obra_social = $_POST["id_obra_social"];
      $_ses_agenda_turnos["id_obra_social"] = $id_obra_social;
      phpss_svars_set("_ses_agenda_turnos", $_ses_agenda_turnos);
      break;

    default:
      # code...
      break;
  }
}
?>