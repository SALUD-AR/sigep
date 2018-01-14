<?php
require_once("../../config.php");

if (isset($_POST['capitulo']) && !empty($_POST['capitulo'])) {
 	$capitulo = trim($_POST['capitulo']);
 	if (!empty($capitulo)) {
 		$_ses_nomenclador["capitulo"] = $capitulo;
  		phpss_svars_set("_ses_nomenclador", $_ses_nomenclador);
 	}
}

if (isset($_POST['grupo']) && !empty($_POST['grupo'])) {
 	$grupo = trim($_POST['grupo']);
 	if (!empty($grupo)) {
 		$_ses_nomenclador["grupo"] = $grupo;
  		phpss_svars_set("_ses_nomenclador", $_ses_nomenclador);
 	}
}

if (isset($_POST['categoria']) && !empty($_POST['categoria'])) {
 	$categoria = trim($_POST['categoria']);
 	if (!empty($categoria)) {
 		$_ses_nomenclador["categoria"] = $categoria;
  		phpss_svars_set("_ses_nomenclador", $_ses_nomenclador);
 	}
}

?>
<script type="text/javascript">
$(document).ready(function() {
	$('#capitulo, #grupo, #categoria').change(function() {
        this.form.submit();
    });
});
</script>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
    	<div class="panel panel-default">
			<div class="panel-heading">
				<form method="POST" action="nomenclador.php">
					<?php if (!empty($pagina)) { ?>
					  <input type="hidden" name="pagina" value="<?php echo $pagina; ?>" />
					<?php } ?>
					<div class="row">
						<div class="col-md-1"><b>Cap&iacute;tulo:</b></div>
						<div class="col-md-11">
							<select id="capitulo" name="capitulo" class="col-md-12">
								<option></option>
		          				<?php
		          				$query_cap = "SELECT id10, dec10, grp10 FROM cie10 WHERE grp10 ~ E'^\\\\d+$' AND id10 not LIKE '|%' ORDER BY grp10::integer";
								$res_cap = sql($query_cap, "Error al traer los datos de los cap&iacute;tulos") or fin_pagina();
								$cap_encontrado = false;

		          				while (!$res_cap->EOF) {
		          					$selected = "";
									if ($res_cap->fields['grp10'] == $capitulo) {
										$selected = " selected";
										$cap_encontrado = true;
									}
									echo '<option value="', $res_cap->fields['grp10'], '"', $selected, '>', $res_cap->fields['id10'], " ", $res_cap->fields['dec10'], '</option>';
									$res_cap->MoveNext();
								}
								if (!$cap_encontrado) {
									$capitulo = $grupo = $categoria = "";
								}
								?>
							</select>
						</div>
					</div>
					<?php if (!empty($capitulo)) { ?>
					<div class="row">
						<div class="col-md-1"><b>Grupo:</b></div>
						<div class="col-md-11">
							<select id="grupo" name="grupo" class="col-md-12">
								<option></option>
		          				<?php
		          				$query_grp = "SELECT id10, dec10, grp10 FROM cie10 WHERE grp10='{$capitulo}' AND id10 LIKE '|%' ORDER BY dec10";
								$res_grp = sql($query_grp, "Error al traer los datos de los grupos") or fin_pagina();
								$grp_encontrado = false;

		          				while (!$res_grp->EOF) {
		          					$selected = "";
									if ($res_grp->fields['id10'] == $grupo) {
										$selected = " selected";
										$grp_encontrado = true;
									}
									echo '<option value="', $res_grp->fields['id10'], '"', $selected, '>', substr($res_grp->fields['id10'], 1), " ", $res_grp->fields['dec10'], '</option>';
									$res_grp->MoveNext();
								}
								if (!$grp_encontrado) {
									$grupo = $categoria = "";
								}
								?>
							</select>
						</div>
					</div>
					<?php } ?>
					<?php if (!empty($grupo)) { ?>
					<div class="row">
						<div class="col-md-1"><b>Categor&iacute;a:</b></div>
						<div class="col-md-11">
							<select id="categoria" name="categoria" class="col-md-12">
								<option></option>
		          				<?php
		          				$query_cat = "SELECT id10, dec10, grp10 FROM cie10 WHERE grp10='{$grupo}' ORDER BY id10";
								$res_cat = sql($query_cat, "Error al traer los datos de las categorias") or fin_pagina();
								$cat_encontrado = false;

		          				while (!$res_cat->EOF) {
		          					$selected = "";
									if ($res_cat->fields['id10'] == $categoria) {
										$selected = " selected";
										$cat_encontrado = true;
									}
									echo '<option value="', $res_cat->fields['id10'], '"', $selected, '>', $res_cat->fields['id10'], " ", $res_cat->fields['dec10'], '</option>';
									$res_cat->MoveNext();
								}
								if (!$cat_encontrado) {
									$categoria = "";
								}
								?>
							</select>
						</div>
					</div>
					<?php } ?>
				</form>
			</div>
	    	<?php 
	    	if (!empty($categoria)) {
  				$sql_tmp = "SELECT id10, dec10, grp10 FROM cie10 WHERE id10 LIKE '{$categoria}%' AND grp10 IS NULL";
  				$mostrar_form_busqueda = false;
  				list($sql,$total_nomenclador,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,false);
				$res = sql($sql, "Error al traer los datos") or fin_pagina();

				echo '<table class="table table-condensed table-hover">';
				echo '<thead>
						<tr>
							<th width="10%">
								<a href="'.encode_link("nomenclador.php",array("sort"=>"1","up"=>$up)).'">
									C&oacute;digo '.icono_sort(1).'
								</a>
							</th>
							<th width="90%">
								<a href="'.encode_link("nomenclador.php",array("sort"=>"2","up"=>$up)).'">
									Descripci&oacute;n '.icono_sort(2).'
								</a>
							</th>
						</tr>
					  </thead>';
				echo '<tbody>';

				if ($res->recordCount() > 0) {
      				while (!$res->EOF) {
						echo '<tr>';
						echo '<td>', $res->fields['id10'], '</td>';
						echo '<td>', $res->fields['dec10'], '</td>';
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