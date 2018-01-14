<?php

$agenda_colores = array(
  "#000b42",
  "#1a1f71",
  "#0610D7",
  "#075DA0",
  "#2D7DC1",
  "#12A8CD",
  "#6ac8Ee",
  "#008080",
  "#009990",
  "#2AD4FD",
  "#7eb1b3",
  "#0BD2AA",
  "#33AD73",
  "#60ba46",
  "#008000",
  "#577315",
  "#276315",
  "#3E5A33",
  "#0B573D",
  "#114227"
);


function cargar_medicos($id_especialidad) {
  global $agenda_colores;
  $query = "SELECT 
                nacer.medicos.id_medico,
                nacer.medicos.apellido AS apellido_medico,
                nacer.medicos.nombre AS nombre_medico,
                nacer.agendas.id,
                nacer.agendas.dom,
                nacer.agendas.lun,
                nacer.agendas.mar,
                nacer.agendas.mie,
                nacer.agendas.jue,
                nacer.agendas.vie,
                nacer.agendas.sab,
                nacer.agendas.hora_inicio,
                nacer.agendas.hora_fin,
                nacer.agendas.fecha_inicio,
                nacer.agendas.fecha_fin,
                nacer.agendas.duracion
              FROM
                nacer.especialidades
                RIGHT OUTER JOIN nacer.especialidades_medicos ON (nacer.especialidades.id_especialidad = nacer.especialidades_medicos.id_especialidad)
                LEFT OUTER JOIN nacer.medicos ON (nacer.especialidades_medicos.id_medico = nacer.medicos.id_medico)
                RIGHT OUTER JOIN nacer.agendas ON (nacer.especialidades_medicos.id = nacer.agendas.id_especialidad_medico)
              WHERE
                nacer.especialidades.id_especialidad = $id_especialidad
              ORDER BY
                apellido_medico,
                nombre_medico,
                hora_inicio";
  $res_medicos = sql($query) or die();
  if ($res_medicos->recordCount() > 0) {
    echo '<div class="panel-group" id="accordion">';
    // echo '<div class="list-group-item list-group-item-info" role="alert"><h3>M&eacute;dicos y Turnos</h3></div>';
    $medicos = array();
    while (!$res_medicos->EOF) {
      $id_medico = "medico-".$res_medicos->fields["id_medico"];
      if (!isset($medicos[$id_medico])) {
        $medicos[$id_medico] = array(
          "id_medico" => $res_medicos->fields["id_medico"],
          "nombre"    => $res_medicos->fields["apellido_medico"]." ".$res_medicos->fields["nombre_medico"],
          "agendas"   => array()
        );
      }
      $medicos[$id_medico]["agendas"][] = array(
        "id"           => $res_medicos->fields["id"],
        "dom"          => $res_medicos->fields["dom"],
        "lun"          => $res_medicos->fields["lun"],
        "mar"          => $res_medicos->fields["mar"],
        "mie"          => $res_medicos->fields["mie"],
        "jue"          => $res_medicos->fields["jue"],
        "vie"          => $res_medicos->fields["vie"],
        "sab"          => $res_medicos->fields["sab"],
        "hora_inicio"  => $res_medicos->fields["hora_inicio"],
        "hora_fin"     => $res_medicos->fields["hora_fin"],
        "fecha_inicio" => $res_medicos->fields["fecha_inicio"],
        "fecha_fin"    => $res_medicos->fields["fecha_fin"],
        "duracion"     => $res_medicos->fields["duracion"]
      );

      $res_medicos->MoveNext();
    }

    $dias_arr = array('dom', 'lun', 'mar', 'mie', 'jue', 'vie', 'sab');
    $agendas = array();
    $color_index = 0;

    foreach ($medicos as $medico => $datos) {
      echo '<div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#'.$medico.'">'.$datos["nombre"].'</a>
                    </h4>
                </div>
                <div id="'.$medico.'" class="panel-collapse collapse">
                    <div class="panel-body">';
      // echo "
      //    <a href='#{$medico}' class='list-group-item list-group-item-info' data-toggle='collapse' data-target='#{$medico}' data-parent='#columna-turnos'>", $datos["nombre"], "</a>
      //    <div class='collapse' id='{$medico}'>";
      if (!empty($datos["agendas"])) {
        foreach ($datos["agendas"] as $agenda) {
          $count_id = 1;
          $id = $agenda["id"];
          $agendas[] = $id;
          $hora_inicio = strtotime($agenda["hora_inicio"]);
          $hora_fin = strtotime($agenda["hora_fin"]);
          $duracion = $agenda["duracion"];
          $dias = array();
          foreach ($dias_arr as $dia) {
            if ($agenda[$dia] == 't') {
              $dias[] = ucfirst($dia);
            }
          }
          echo "<div class='external-events' id='agenda-", $id, "'>";
          echo "<div class='row agenda-dias'>";
          echo "<div class='col-md-12'>", join(", ", $dias), "</div>";
          echo "</div>";
          echo "<div class='row agenda-horas'>";
          echo "<div class='col-md-12'>", date("H:i", $hora_inicio), " - ", date("H:i", $hora_fin), "</div>";
          echo "</div>";
          while (strtotime('+'.$duracion.' minutes', $hora_inicio) <= $hora_fin) {
            echo "<div class='external-event' style='background-color: ", $agenda_colores[$color_index], "' data-id='{$id}-", $count_id++, "' data-start='", date("H:i:s", $hora_inicio), "' data-end='", date("H:i:s", strtotime('+'.$duracion.' minutes', $hora_inicio)), "' data-duration='", date("H:i:s", mktime(0, $duracion, 0)), "'><span class='datos-agenda'>", date("H:i", $hora_inicio), "</span></div>";
            $hora_inicio = strtotime('+'.$duracion.' minutes', $hora_inicio);
          }
          echo "</div>";
          $color_index++;
          if ($color_index == count($agenda_colores)) {
            $color_index = 0;
          }
        }
        echo '
               </div>
            </div>';

      }
      echo "</div>";
    }
    echo "<input type='hidden' name='agendas_ids' id='agendas_ids' value='", join(',', $agendas), "' />";
    echo "</div>";
  }
  else {
    echo '<div class="col-md-12 list-group-item-warning">No hay M&eacute;dicos asignados a esta Especialidad</div>';
  }
}

?>