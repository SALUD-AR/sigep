<?php
require_once("../../config.php");

$sql_tmp = "SELECT * FROM nacer.cie10";
$itemspp = 20;
?>
<form method="POST" action="nomenclador.php">
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
						list($sql,$total_nomenclador,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
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
									<h6><b>Total:</b> <?php echo $total_nomenclador; ?></h6>
								</div>
								<div class="col-sm-4 text-right">
									<h6><?php echo $link_pagina; ?></h6>
								</div>
							</div>
						</th>
					</tr>
					<tr>
						<th width="10%">
							<a href="<?php echo encode_link("nomenclador.php",array("sort"=>"1","up"=>$up)); ?>">
								C&oacute;digo <?php echo icono_sort(1); ?>
							</a>
						</th>
						<th width="90%">
							<a href="<?php echo encode_link("nomenclador.php",array("sort"=>"2","up"=>$up)); ?>">
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
						      $link_fila = ' onclick="location.href=\''.encode_link($pagina, array("nuevo_id_cuie10" => $result->fields['id10'])).'\';"';
						    } 
		      			$id = $result->fields['id10'];
		      			if ($id[0] == '|') {
		      				$id = substr($id, 1);
		      			}
								echo '<tr>';
								echo '<td'.$link_fila.'>', $id, '</td>';
								echo '<td'.$link_fila.'>', $result->fields['dec10'], '</td>';
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
