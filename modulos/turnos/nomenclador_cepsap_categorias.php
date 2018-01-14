<?php
require_once("../../config.php");

if (isset($_POST['categoria']) && !empty($_POST['categoria'])) {
  $categoria = trim($_POST['categoria']);
  if (!empty($categoria)) {
    $_ses_nomenclador_cepsap["categoria"] = $categoria;
      phpss_svars_set("_ses_nomenclador_cepsap", $_ses_nomenclador_cepsap);
  }
}

?>
<script type="text/javascript">
$(document).ready(function() {
  $('#categoria').change(function() {
        this.form.submit();
    });
});
</script>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <div class="panel panel-default">
      <div class="panel-heading">
        <form method="POST" action="nomenclador_cepsap.php">
          <?php if (!empty($pagina)) { ?>
            <input type="hidden" name="pagina" value="<?php echo $pagina; ?>" />
          <?php } ?>
          <div class="row">
            <div class="col-md-1"><b>Categor&iacute;a:</b></div>
            <div class="col-md-11">
              <select id="categoria" name="categoria" class="col-md-12">
                <option></option>
                <?php
                $query_cat = "SELECT * FROM cepsap_categorias ORDER BY descripcion";
                $res_cat = sql($query_cat, "Error al traer los datos de las categorias") or fin_pagina();
                $cat_encontrado = false;

                while (!$res_cat->EOF) {
                  $selected = "";
                  if ($res_cat->fields['id'] == $categoria) {
                    $selected = " selected";
                    $cat_encontrado = true;
                  }
                  echo '<option value="', $res_cat->fields['id'], '"', $selected, '>', $res_cat->fields['descripcion'], '</option>';
                  $res_cat->MoveNext();
                }
                if (!$cat_encontrado) {
                  $categoria = "";
                }
                ?>
              </select>
            </div>
          </div>
        </form>
      </div>
        <?php 
        if (!empty($categoria)) {
          $sql_tmp = "SELECT 
                        nacer.cepsap_items.id,
                        nacer.cepsap_items.codigo,
                        nacer.cepsap_items.descripcion,
                        nacer.cepsap_items.incluye,
                        nacer.cepsap_items.excluye,
                        nacer.cepsap_items.id_categoria,
                        nacer.cepsap_categorias.descripcion AS categoria_descripcion,
                        nacer.cepsap_categorias.incluye AS categoria_incluye,
                        nacer.cepsap_categorias.excluye AS categoria_excluye
                      FROM
                        nacer.cepsap_items
                        LEFT OUTER JOIN nacer.cepsap_categorias ON (nacer.cepsap_items.id_categoria = nacer.cepsap_categorias.id)
                      ";
          $where_tmp="nacer.cepsap_items.id_categoria = ${categoria}";
          $mostrar_form_busqueda = false;
          list($sql,$total_nomenclador_cepsap,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,false);
          $res = sql($sql, "Error al traer los datos") or fin_pagina();

          echo '<table class="table table-condensed table-hover">';
          echo '<thead>';
          if ($res->recordCount() > 0) {
            echo '<tr><td colspan="2"><b>';
            if (!empty($res->fields['categoria_incluye'])) {
              echo 'Incluye: ', $res->fields['categoria_incluye'], '<br/>';
            }
            if (!empty($res->fields['categoria_excluye'])) {
              echo 'Excluye: ', $res->fields['categoria_excluye'], '<br/>';
            }
            echo '</b></td></tr>';
          }
          echo '<tr>
                  <th width="10%">
                    <a href="'.encode_link("nomenclador_cepsap.php",array("sort"=>"1","up"=>$up)).'">
                      C&oacute;digo '.icono_sort(1).'
                    </a>
                  </th>
                  <th width="90%">
                    <a href="'.encode_link("nomenclador_cepsap.php",array("sort"=>"2","up"=>$up)).'">
                      Descripci&oacute;n '.icono_sort(2).'
                    </a>
                  </th>
                </tr>
                </thead>';
          echo '<tbody>';

          if ($res->recordCount() > 0) {
            while (!$res->EOF) {
              $link_fila = "";
              if (!empty($pagina)) { // si no se debe retornar a alguna pagina en especial se va a la pagina de detalles
                $link_fila = ' onclick="location.href=\''.encode_link($pagina, array("nuevo_id_cepsap" => $result->fields['id'])).'\';"';
              } 
              echo '<tr>';
              echo '<td', $link_fila, '>', $res->fields['codigo'], '</td>';
              echo '<td', $link_fila, '>', $res->fields['descripcion'];
              if (!empty($res->fields['incluye'])) {
                echo '<br/>Incluye: ', $res->fields['incluye'];
              }
              if (!empty($res->fields['excluye'])) {
                echo '<br/>Excluye: ', $res->fields['excluye'];
              }
              echo '</td>';
              echo '<tr>';
              $res->MoveNext();
            }
          }
          else {
            echo '<td colspan="2" align="center" class="danger"><strong>No hay datos</strong></td>';
          }
          echo '</tbody>';
          echo '</table>';
        }
        ?>
    </div>
    </div>
    <div class="col-md-2"></div>
</div>