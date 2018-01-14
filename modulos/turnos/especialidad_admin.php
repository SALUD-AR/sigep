<?
require_once ("../../config.php");

echo $html_header;

$id_especialidad = $parametros['id_especialidad'];

$mensaje = "";
$mensaje_tipo = "success"; // success=verde info=azul warning=amarillo danger=rojo

// agregar un nuevo medico a la especialidad
if (isset($parametros["nuevo_id_medico"]) && 
	!empty($parametros["nuevo_id_medico"]) &&
	is_numeric($parametros["nuevo_id_medico"]) &&
	is_numeric($id_especialidad)
	) {
	$query_add = "SELECT id FROM especialidades_medicos 
					WHERE id_medico = {$parametros["nuevo_id_medico"]}
					AND id_especialidad = {$id_especialidad}";
	$res_add = sql($query_add, "Error al verificar la existencia del M&eacute;dico") or fin_pagina();
	if ($res_add->recordCount()==0) {
		$query_add = "INSERT INTO especialidades_medicos (id_especialidad, id_medico)
						VALUES ({$id_especialidad}, {$parametros["nuevo_id_medico"]})";
		$res_add = sql($query_add, "Error al asignar el M&eacute;dico") or fin_pagina();
		if ($res_add && $db->Affected_Rows() == 1) {
			$mensaje = "El M&eacute;dico seleccionado se ha asignado correctamente!";
		}
		else {
			$mensaje = "Error al asignar el M&eacute;dico seleccionado!";
			$mensaje_tipo = "danger";
		}
	}
	else {
		$mensaje = "El M&eacute;dico seleccionado ya se encuentra asignado a esta Especialidad!";
		$mensaje_tipo = "warning";
	}
}

// eliminar el medico seleccionado
if (isset($parametros["eliminar_medico"]) && 
	!empty($parametros["eliminar_medico"]) &&
	is_numeric($parametros["eliminar_medico"])
	) {
	$query_del = "DELETE FROM especialidades_medicos WHERE especialidades_medicos.id = {$parametros["eliminar_medico"]}";
	$res_del = sql($query_del, "Error al eliminar el M&eacute;dico") or fin_pagina();
	if ($res_del && $db->Affected_Rows() == 1) {
		$mensaje = "El M&eacute;dico se ha eliminado correctamente!";
	}
	else {
		$mensaje = "Error al eliminar el M&eacute;dico!";
		$mensaje_tipo = "danger";
	}
}

if ($_POST['guardar_editar']=='Guardar'){
	$nom_titulo 	= strtoupper(trim($_POST['nom_titulo']));
	$especialidad 	= trim($_POST['especialidad']);

	$error = "";

	if (empty($id_especialidad)) {
		$error .= "Falta el ID de la Especialidad<br/>";
	}

	if (empty($nom_titulo)) {
		$error .= "Falta ingresar el t&iacute;tulo de la Especialidad<br/>";
	}

	if (empty($especialidad) || ($especialidad != "t" && $especialidad != "f")) {
		$error .= "Falta seleccionar si es una especialidad o no<br/>";
	}

	if (empty($error)) { // No hubo errores
		$db->StartTrans();
		$query="UPDATE nacer.especialidades SET 
					nom_titulo=".$db->Quote($nom_titulo).",
					especialidad=".$db->Quote($especialidad)."
				WHERE id_especialidad=$id_especialidad";	

		sql($query, "Error al insertar/actualizar la Especialidad") or fin_pagina();
	 	 
		$db->CompleteTrans();
		$mensaje = "Los datos se actualizaron correctamente";
	}
	else {
		$mensaje = "Debe resolver los siguientes errores antes de continuar:<br/>$error";
		$mensaje_tipo = "danger";
	}
}

if ($_POST['guardar']=='Guardar'){
	$nom_titulo 	= strtoupper(trim($_POST['nom_titulo']));
	$especialidad 	= trim($_POST['especialidad']);

	$error = "";

	if (empty($nom_titulo)) {
		$error .= "Falta ingresar el nombre de la Especialidad<br/>";
	}

	if (empty($especialidad) || ($especialidad != "t" && $especialidad != "f")) {
		$error .= "Falta seleccionar si es una especialidad o no<br/>";
	}

	if (empty($error)) { // No hubo errores
		$verificar_nombre="SELECT id_especialidad FROM nacer.especialidades WHERE nom_titulo=".$db->Quote($nom_titulo);
		$res_verificar = sql($verificar_nombre, "Error al realizar la verificacion de los datos") or fin_pagina();
	
		if ($res_verificar->recordCount()==0) {
			$query="INSERT INTO nacer.especialidades
			   			(nom_titulo, especialidad)
			   		VALUES
			   			(
			   			".$db->Quote($nom_titulo).", 
			   			".$db->Quote($especialidad)."
			   			)
					RETURNING id_especialidad
					";
				
			$res_insert = sql($query, "Error al insertar/actualizar la Especialidad") or fin_pagina();
			
			$id_especialidad = $res_insert->fields['id_especialidad'];

			$mensaje="Los datos se han guardado correctamente";
		} else {
			$mensaje = "Ya existe una Especialidad con ese nombre";
			$mensaje_tipo = "danger";
		}
	}
	else {
		$mensaje = "Debe resolver los siguientes errores antes de continuar:<br/>$error";
		$mensaje_tipo = "danger";
	}
}

if ($_POST['borrar']=='Borrar') {
	if (!empty($id_especialidad)) {
		$verificar_id="SELECT id_especialidad FROM nacer.especialidades WHERE id_especialidad=".$id_especialidad;
		$res_verificar = sql($verificar_id, "Error al realizar la verificacion de los datos") or fin_pagina();
		if ($res_verificar->recordCount() > 0) {
			$query="DELETE FROM nacer.especialidades WHERE id_especialidad=".$id_especialidad;
			sql($query, "Error al eliminar la Especialidad") or fin_pagina();
			$mensaje="Los datos se han borrado correctamente";
		}
		else {
			$mensaje = "No se encontr&oacute; la Especialidad para borrar con el ID=$id_especialidad";
			$mensaje_tipo = "danger";
		}
		$id_especialidad = "";
	}
	else {
		$mensaje = "Falta el ID de la Especialidad a borrar";
		$mensaje_tipo = "danger";
	}
}

if ($id_especialidad) {
	$query = "SELECT * FROM nacer.especialidades  WHERE id_especialidad=$id_especialidad";
	$res = sql($query, "Error al traer los datos de la Especialidad") or fin_pagina();
	$nom_titulo 		= $res->fields['nom_titulo'];
	$especialidad 	= $res->fields['especialidad'];

	$query_med = "SELECT 
				    especialidades_medicos.id,
				    medicos.apellido,
				    medicos.nombre
				  FROM
				    especialidades_medicos
				    LEFT OUTER JOIN medicos ON (especialidades_medicos.id_medico = medicos.id_medico)
				  WHERE
				    especialidades_medicos.id_especialidad = {$id_especialidad}
				  ORDER BY medicos.apellido, medicos.nombre";
	$res_med = sql($query_med, "Error al traer los datos de los M&eacute;dicos") or fin_pagina();
}

?>
<script type="text/javascript">
	function control_nuevos() {
		if(!$("input[name=nom_titulo]").val()){
			alert('Debe ingresar el t&iacute;tulo');
			return false;
		} 
		if(!$("select[name=especialidad]").val()){
			alert('Debe seleccionar si es una especialidad o no');
			return false;
		} 
		return true;
	}

	function editar_campos() {	
		$("form .editable").prop('disabled', false);
		$("input[name=editar]").prop('disabled', true);
		$("input[name=guardar_editar]").prop('disabled', false);
		$("input[name=cancelar_editar]").prop('disabled', false);
	}
	$(document).ready(function() {
		$('#confirm-delete').on('show.bs.modal', function(e) {
			$(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
        })
	});	
</script>

<?php $link = encode_link("especialidad_admin.php",array("id_especialidad"=>$id_especialidad)); ?>
<form name='form1' action='<?php echo $link; ?>' method='POST'>

<?php if (!empty($mensaje)) { ?>
<div class="container alert alert-<?php echo $mensaje_tipo; ?> alert-dismissible" role="alert" style="width:40%;">
	<button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
    <p><strong><?php echo $mensaje; ?></strong></p>
</div>
<?php } ?>

<table width="85%" cellspacing="0" border="1" bordercolor="#E0E0E0" align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
	<tr id="mo">
    	<td>
	    	<?php if (!$id_especialidad) { ?>  
	    		<font size="+1"><b>Nuevo Dato</b></font>   
	    	<?php } else { ?>
	        	<font size="+1"><b><?php echo $nom_titulo; ?></b></font>   
	        <?php } ?>
	    </td>
 	</tr>
	<tr>
		<td>
			<table width="90%" align="center" class="bordes">
			    <tr>
			    	<td id="mo">
			       		<b>Descripci&oacute;n de la Especialidad</b>
			      	</td>
			    </tr>
			    <tr>
			       	<td>
			        	<table width="100%">
			         		<tr>	           
			           			<td align="center" colspan="2">
			            			<b>N&uacute;mero del Dato: <font size="+1" color="Red"> <?php echo ($id_especialidad)? $id_especialidad : "Nuevo Dato"?></font></b>
			           			</td>
			         		</tr>
			        		<tr>
			         			<td align="right" width="40%"><b>T&iacute;tulo:</b></td>
			            		<td align="left" width="60%">
			              			<input type="text" size="40" class="editable" value="<?php echo $nom_titulo; ?>" name="nom_titulo" <? if ($id_especialidad) echo "disabled"?>>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>Especialidad:</b></td>
			            		<td align="left">
			              			<select class="editable" name="especialidad" <? if ($id_especialidad) echo "disabled"?>>
			              				<option></option>
			              				<option value="t" <? if ($especialidad == "t") echo "selected"?>>Sí</option>
			              				<option value="f" <? if ($especialidad == "f") echo "selected"?>>No</option>
			              			</select>
			            		</td>
			         		</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center">
						<br/>
						<?php if ($id_especialidad) { ?>
							<input class="btn btn-default btn-xs" type="button" name="editar" value="Editar" onclick="editar_campos()" title="Edita Campos" style="width=130px"> &nbsp;&nbsp;
						    <input class="btn btn-default btn-xs" type="submit" name="guardar_editar" value="Guardar" title="Guardar" disabled style="width=130px" onclick="return control_nuevos()">&nbsp;&nbsp;
						    <input class="btn btn-default btn-xs" type="button" name="cancelar_editar" value="Cancelar" title="Cancela Edici&oacute;n" disabled style="width=130px" onclick="document.location.reload()">&nbsp;&nbsp;	      
						    <input class="btn btn-default btn-xs" type="submit" name="borrar" value="Borrar" style="width=130px" onclick="return confirm('Esta seguro que desea eliminar esta Especialidad?')" >
						<?php } else { ?>
					    	<input class="btn btn-default btn-xs" type="submit" name="guardar" value="Guardar" title="Guardar" style="width=130px" onclick="return control_nuevos()">
				 		<?php }	?>
					</td>	
				</tr>
				<?php if ($id_especialidad) { ?>
				<tr>
					<td>
						<br/>
						<div class="row">
						<div class="col-md-2"></div>
						<div class="col-md-8">
						<div class="panel panel-default" id="panel_medicos">
							<div class="panel-heading">
								<b><a data-toggle="collapse" data-target="#medicos" href="#panel_medicos" class="collapsed">
									M&eacute;dicos
								</a></b>
							</div>
							<div id="medicos" class="panel-collapse collapse">
								<div class="panel-body text-center">
									<table class="table table-condensed table-hover">
										<thead>
											<tr>
												<th width="45%" class="small">Apellido</th>
												<th width="45%" class="small">Nombre</th>
												<th width="10%" class="small">Acciones</th>
											</tr>
										</thead>
										<tbody>
											<?php
											if ($res_med->recordCount() > 0) {
							      				while (!$res_med->EOF) {
							      					$link_borrar = encode_link("especialidad_admin.php", array("eliminar_medico" => $res_med->fields['id'], "id_especialidad" => $id_especialidad));
													echo '<tr>';
													echo '<td>', $res_med->fields['apellido'], '</td>';
													echo '<td>', $res_med->fields['nombre'], '</td>';
													echo '<td class="text-center"><a data-href="',$link_borrar,'" data-toggle="modal" data-target="#confirm-delete" href="#"><span class="glyphicon glyphicon-minus-sign text-danger" aria-hidden="true" title="Eliminar m&eacute;dico"></span></a></td>';
													echo '<tr>';
													$res_med->MoveNext();
												}
											}
											else {
												echo '<td colspan="3" align="center" class="danger"><strong>No hay datos</strong></td>';
											}
											?>
										</tbody>
									</table>
									<?php 
									$link_agregar = encode_link("medicos.php", array("pagina" => "especialidad_admin", "id_especialidad" => $id_especialidad));
									?>
									<a href="<?php echo $link_agregar; ?>" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-plus-sign text-success" aria-hidden="true"></span> Agregar M&eacute;dico</a>
								</div>
							</div>
						</div>
						</div>
						<div class="col-md-2"></div>
					</td>
				</tr>
				<?php }	?>
			 	<tr>
			 		<td align="center" class="bordes">
			 			<br/>
			     		<input class="btn btn-default btn-xs" type="button" name="volver" value="Volver" onclick="document.location='especialidades.php'"title="Volver al Listado" style="width=150px">
			     	</td>
			  	</tr>
			</table>
		</td>
	</tr>
</table>
</form>
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Confirmar la eliminaci&oacute;n</h4>
            </div>
            <div class="modal-body">
                <p>&iquest;Est&aacute; seguro que desea eliminar el m&eacute;dico seleccionado?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
                <a href="#" class="btn btn-danger btn-sm danger">Aceptar</a>
            </div>
        </div>
    </div>
</div>
<?php fin_pagina(); ?>