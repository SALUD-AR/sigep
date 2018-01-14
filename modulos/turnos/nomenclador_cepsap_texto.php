<?php
require_once("../../config.php");

$sql_tmp = "SELECT * FROM nacer.cepsap_items";
$itemspp = 20;
?>
<form method="POST" action="nomenclador_cepsap.php">
<?php if (!empty($pagina)) { ?>
  <input type="hidden" name="pagina" value="<?php echo $pagina; ?>" />
<?php } ?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <div class="panel panel-default">
      <div class="panel-heading">
        <div class="row">
          <div class="col-md-12 text-center">
            <?php 
            $link_tmp = array("pagina" => $pagina);
            list($sql,$total_nomenclador_cepsap,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
            &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
            <?php $result = sql($sql,"No se ejecuto en la consulta principal") or fin_pagina();?>
          </div>
        </div>
      </div>
      <table class="table table-condensed table-hover">
        <thead>
          <tr>
            <th colspan="2">
              <div class="row">
                <div class="col-sm-8 text-left">
                  <h6><b>Total:</b> <?php echo $total_nomenclador_cepsap; ?></h6>
                </div>
                <div class="col-sm-4 text-right">
                  <h6><?php echo $link_pagina; ?></h6>
                </div>
              </div>
            </th>
          </tr>
          <tr>
            <th width="10%">
              <a href="<?php echo encode_link("nomenclador_cepsap.php",array("sort"=>"1","up"=>$up)); ?>">
                C&oacute;digo <?php echo icono_sort(1); ?>
              </a>
            </th>
            <th width="90%">
              <a href="<?php echo encode_link("nomenclador_cepsap.php",array("sort"=>"2","up"=>$up)); ?>">
                Descripci&oacute;n <?php echo icono_sort(2); ?>
              </a>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php
            if ($result->recordCount() > 0) {
              while (!$result->EOF) {
                $link_fila = "";
                if (!empty($pagina)) { // si no se debe retornar a alguna pagina en especial se va a la pagina de detalles
                  $link_fila = ' onclick="location.href=\''.encode_link($pagina, array("nuevo_id_cepsap" => $result->fields['id'])).'\';"';
                } 
                echo '<tr>';
                echo '<td'.$link_fila.'>', $result->fields['codigo'], '</td>';
                echo '<td'.$link_fila.'>', $result->fields['descripcion'];
                if (!empty($result->fields['incluye'])) {
                  echo '<br/>Incluye: ', $result->fields['incluye'];
                }
                if (!empty($result->fields['excluye'])) {
                  echo '<br/>Excluye: ', $result->fields['excluye'];
                }
                echo '</td>';
                echo '<tr>';
                $result->MoveNext();
              }
            }
            else {
              echo '<td colspan="2" align="center" class="danger"><strong>No hay datos</strong></td>';
            }
          ?>
        </tbody>
      </table>
    </div>
    </div>
    <div class="col-md-2"></div>
</div>
</form>
