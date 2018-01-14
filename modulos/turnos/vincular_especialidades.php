<?php
require_once ("../../config.php");

echo $html_header;

$id_efector = intval($parametros['id_efector']);

$mensaje = "";
$mensaje_tipo = "success"; // success=verde info=azul warning=amarillo danger=rojo

// agregar una nueva especialidad al efector
if (isset($parametros["nuevo_id_especialidad"]) && 
  !empty($parametros["nuevo_id_especialidad"]) &&
  is_numeric($parametros["nuevo_id_especialidad"]) &&
  intval($parametros["nuevo_id_especialidad"]) > 0 &&
  isset($parametros["id_parent"]) && 
  !empty($parametros["id_parent"]) &&
  is_numeric($parametros["id_parent"]) &&
  intval($parametros["id_parent"]) > 0
  ) {
  $id_efector = $parametros["id_parent"];
  $query_add = "SELECT id FROM especialidades_efectores 
          WHERE id_especialidad = {$parametros["nuevo_id_especialidad"]}
          AND id_efector = {$id_efector}";
  $res_add = sql($query_add, "Error al verificar la existencia de la Especialidad") or fin_pagina();
  if ($res_add->recordCount()==0) {
    $query_add = "INSERT INTO especialidades_efectores (id_especialidad, id_efector)
            VALUES ({$parametros["nuevo_id_especialidad"]}, {$id_efector})";
    $res_add = sql($query_add, "Error al asignar la nueva Especialidad") or fin_pagina();
    if ($res_add && $db->Affected_Rows() == 1) {
      $mensaje = "La Especialidad seleccionada se ha asignado correctamente!";
    }
    else {
      $mensaje = "Error al asignar la Especialidad seleccionada!";
      $mensaje_tipo = "danger";
    }
  }
  else {
    $mensaje = "La Especialidad seleccionada ya se encuentra asignada a este Efector!";
    $mensaje_tipo = "warning";
  }
}

if (empty($id_efector) || $id_efector <= 0) {
  Error("Falta el ID del efector!");
  fin_pagina();
}

// eliminar la especialidad seleccionada
if (isset($parametros["eliminar_especialidad"]) && 
  !empty($parametros["eliminar_especialidad"]) &&
  is_numeric($parametros["eliminar_especialidad"])
  ) {
  $query_del = "DELETE FROM especialidades_efectores WHERE especialidades_efectores.id = {$parametros["eliminar_especialidad"]}";
  $res_del = sql($query_del, "Error al eliminar la Especialidad") or fin_pagina();
  if ($res_del && $db->Affected_Rows() == 1) {
    $mensaje = "La Especialidad se ha eliminado correctamente!";
  }
  else {
    $mensaje = "Error al eliminar la Especialidad!";
    $mensaje_tipo = "danger";
  }
}

$itemspp = 20;

$orden = array(
            "default" => "1",
            "1" => "especialidad_nombre",
            "2" => "especialidad"
          );
$filtro = array(
            "especialidad_nombre" => "T&iacute;tulo"
          );

$query_efe = "SELECT 
                nacer.efe_conv.nombre AS efector_nombre
              FROM
                nacer.efe_conv
              WHERE
                nacer.efe_conv.id_efe_conv = {$id_efector}
              ORDER BY nacer.efe_conv.nombre";
$res_efe = sql($query_efe, "al traer los datos del Efector") or fin_pagina();
$efector_nombre = $res_efe->fields['efector_nombre'];

$query_esp = "SELECT 
                nacer.especialidades_efectores.id,
                nacer.especialidades_efectores.id_efector,
                nacer.especialidades_efectores.id_especialidad,
                nacer.especialidades.nom_titulo AS especialidad_nombre,
                nacer.especialidades.especialidad,
                nacer.efe_conv.cuie AS efector_cuie,
                nacer.efe_conv.nombre AS efector_nombre,
                nacer.efe_conv.cuidad AS efector_ciudad
              FROM
                nacer.efe_conv
                RIGHT OUTER JOIN nacer.especialidades_efectores ON (nacer.efe_conv.id_efe_conv = nacer.especialidades_efectores.id_efector)
                LEFT OUTER JOIN nacer.especialidades ON (nacer.especialidades_efectores.id_especialidad = nacer.especialidades.id_especialidad)
              WHERE
                nacer.especialidades_efectores.id_efector = {$id_efector}
              ";
$res_esp = sql($query_esp, "al traer los datos de las Especialidades") or fin_pagina();
?>
<br/>
<?php if (!empty($mensaje)) { ?>
<div class="container alert alert-<?php echo $mensaje_tipo; ?> alert-dismissible" role="alert" style="width:40%;">
  <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
    <p><strong><?php echo $mensaje; ?></strong></p>
</div>
<?php } ?>

<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <div class="panel panel-default">
      <div class="panel-heading">
        <div class="row">
          <div class="col-md-12 text-center">
            <h3>Efector: <?php echo $efector_nombre; ?></h3>
          </div>
        </div>
      </div>
      <table class="table table-condensed table-hover">
        <thead>
          <tr>
            <th colspan="3">
              <div class="row">
                <div class="col-sm-12 text-left">
                  <h4>Especialidades asignadas al Efector:</h4>
                </div>
              </div>
            </th>
          </tr>
          <tr>
            <th width="70%">
              <a href="<?php echo encode_link("vincular_especialidades.php",array("sort"=>"1","up"=>$up)); ?>">
                T&iacute;tulo <?php echo icono_sort(1); ?>
              </a>
            </th>
            <th width="15%">
              <a href="<?php echo encode_link("vincular_especialidades.php",array("sort"=>"2","up"=>$up)); ?>">
                Especialidad <?php echo icono_sort(2); ?>
              </a>
            </th>
            <th width="15%">
              <a href="<?php echo encode_link("vincular_especialidades.php",array("sort"=>"2","up"=>$up)); ?>">
                Acciones
              </a>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php
            if ($res_esp->recordCount() > 0) {
              while (!$res_esp->EOF) {
                $link_borrar = encode_link("vincular_especialidades.php", array("eliminar_especialidad" => $res_esp->fields['id'], "id_efector" => $res_esp->fields['id_efector']));
                echo '<tr>';
                echo '<td>', $res_esp->fields['especialidad_nombre'], '</td>';
                echo '<td>', $sino[$res_esp->fields['especialidad']], '</td>';
                echo '<td>';
                echo '<a data-href="',$link_borrar,'" style="font-size: 14px;" data-toggle="modal" data-target="#confirm-delete" href="#"><span class="glyphicon glyphicon-minus-sign text-danger" aria-hidden="true" title="Eliminar Especialidad"></span></a></td>';
                echo '<tr>';
                $res_esp->MoveNext();
              }
            }
            else {
              echo '<td colspan="3" align="center" class="danger"><strong>No hay datos</strong></td>';
            }
          ?>
        </tbody>
      </table>
      <br/>
      <div class="row">
        <div class="col-md-2 col-md-offset-4">
          <a href="efectores.php" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-circle-arrow-left text-info" aria-hidden="true"></span> Volver al listado</a>
        </div>
        <div class="col-md-2">
          <?php 
          $link_agregar = encode_link("especialidades.php", array("pagina" => "vincular_especialidades.php", "id_parent" => $id_efector));
          echo $id_efector;
          ?>
          <a href="<?php echo $link_agregar; ?>" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-plus-sign text-success" aria-hidden="true"></span> Agregar Especialidad</a>
        </div>
      </div>
      <br/>
    </div>
  </div>
</div>
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="confirmDeleteLabel">Confirmar la eliminaci&oacute;n</h4>
            </div>
            <div class="modal-body">
                <p>&iquest;Est&aacute; seguro que desea eliminar el elemento seleccionado?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
                <a href="#" class="btn btn-danger btn-sm danger">Aceptar</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $('#confirm-delete').on('show.bs.modal', function(e) {
      $(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
    });
  });
</script>
<?php fin_pagina(); ?>