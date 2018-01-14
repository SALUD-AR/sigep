<?
/*
Author: JEM

modificada por
$Author: JEM $
$Revision: 1.00 $
$Date: 2011/07/18 14:54:30 $
*/

require_once ("../../config.php");

echo $html_header;

cargar_calendario ();

$mensaje = "";
$mensaje_tipo = "success"; // success=verde info=azul warning=amarillo danger=rojo

if ($_POST["id_usuario"] && is_numeric($_POST["id_usuario"]) && intval($_POST["id_usuario"]) > 0) {
	$id_usuario = intval($_POST["id_usuario"]);
}
elseif ($parametros["id_usuario"] && is_numeric($parametros["id_usuario"]) && intval($parametros["id_usuario"]) > 0) {
	$id_usuario = intval($parametros["id_usuario"]);
}

if ($parametros["borra_efec"] == 'borra_efec') {
	
	if ($parametros["id_usuefect"] && is_numeric($parametros["id_usuefect"]) && intval($parametros["id_usuefect"]) > 0) {
		$id_usuefect = intval($parametros["id_usuefect"]);
		$query = "delete from sistema.usu_efec where id_usuefect='$id_usuefect'";
		
		sql ( $query, "Error al eliminar el Efector" ) or fin_pagina ();
		$mensaje = "Los datos se han borrado correctamente";
	}
	else {
		$mensaje = "Falta el ID del efector a borrar!";
		$mensaje_tipo = "danger";
	}
}

if (isset($id_usuario) && !empty($id_usuario)) {
	$query = "SELECT * FROM sistema.usuarios WHERE id_usuario=$id_usuario";
	
	$res_usuario = sql ( $query, "Error al traer los datos del usuario" ) or fin_pagina ();
	$login = $res_usuario->fields['login'];
	$login = strtoupper( $login );

	if ($_POST ['guardar_efector'] == 'Guardar') {
		$db->StartTrans ();
		$cuie = $_POST["cuie"];
		if (is_array($cuie)) {
			for($i = 0; $i < count($cuie); $i ++) {
				$efector = $cuie[$i];
				
				$query = "SELECT id_usuefect FROM sistema.usu_efec WHERE cuie='{$efector}' AND id_usuario='{$id_usuario}'";
				$res = sql($query) or fin_pagina ();
				if ($res->recordCount() == 0) {
					$query = "INSERT INTO sistema.usu_efec
							   	(cuie, id_usuario)
							   	VALUES
							   	('$efector', '$id_usuario')";
					sql ( $query, "Error al insertar el Efector" ) or fin_pagina ();
				}
			
			}
			
			$mensaje = "Los datos se han guardado correctamente";
		}
		$db->CompleteTrans();
	}
}
?>
<script type="text/javascript">
	function control_nuevo_efector(){
		if($('select#cuie').val() === null){
			alert('Debe seleccionar al menos un Efector para agregar');
			return false;
		} 
	} 

	$(document).ready(function() {
		$('#confirm-delete').on('show.bs.modal', function(e) {
			$(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
		});
	});
</script>

<?php if (!empty($mensaje)) { ?>
<div class="container alert alert-<?php echo $mensaje_tipo; ?> alert-dismissible" role="alert" style="width:40%;">
	<button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
    <p><strong><?php echo $mensaje; ?></strong></p>
</div>
<?php } ?>

<form name='form1' action='usr_efectores_admin.php' method='POST'>
<input type="hidden" value="<?=$id_usuario?>" name="id_usuario">

<table width="85%" cellspacing="0" border="1" bordercolor="#E0E0E0"
	align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
	<tr id="mo">
		<td>
    		<font size=+1><b><?=($id_usuario) ? $login : "Nuevo Dato"?></b></font>
    	</td>
	</tr>
	<tr>
		<td>
		<table width=90% align="center" class="bordes">
			<tr>
				<td id=mo colspan="2"><b> Usuario</b></td>
			</tr>
			<tr>
				<td>
				<table>
					<tr>
						<td align="center" colspan="2"><b> ID Usuario: <font size="+1"
							color="Red"> <?=($id_usuario) ? $id_usuario : "Nuevo Dato"?></font>
						</b></td>
					</tr>
					</tr>
					<tr>
						<td align="right"><b>Login:</b></td>
						<td align='left'>
							<input type="text" size="40" value="<?=$login;?>"
								name="login" <?php echo ($id_usuario) ? "disabled" : "" ?>>
						</td>
					</tr>
				</table>
				</td>
			</tr>
 <?
	if ($id_usuario) {
		
		//--------------------- FORM Efector------------------------------		?>
 
 
 			<tr>
				<td>
				<table width="100%" class="bordes" align="center">
					<tr align="center" id="mo">
						<td align="center"><b>Agregar Efector</b></td>
					</tr>


					<tr>
						<td>
						<table width="100%" align="center">

							<tr>
								<td align="right"><b>Efectores:</b></td>
								<td align='left'>
									<select multiple name="cuie[]" id="cuie" size="20" 
										onkeypress="buscar_combo(this);"
										onblur="borrar_buffer();" onchange="borrar_buffer();">
										<?
										$sql = "select * from nacer.efe_conv order by nombre";
										$res_efectores = sql ( $sql ) or fin_pagina ();
										while ( ! $res_efectores->EOF ) {
											$cuiel = $res_efectores->fields ['cuie'];
											$nombre_efector = $res_efectores->fields ['nombre'];
											
											?>
												<option value='<?=$cuiel?>' <?
											if ($cuie == $cuiel)
												echo "selected"?>><?=$cuiel . " - " . $nombre_efector?></option>
											    <?
											$res_efectores->movenext();
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td align="center" colspan="5" class="bordes">
									<input type="submit" name="guardar_efector" value="Guardar"
										title="Guardar" style="" onclick="return control_nuevo_efector()" />
								</td>
							</tr>

						</table>
						</td>
					</tr>
				</table>
				</td>
			</tr>
 <? //--------------------- lista efectores------------------------------		?>
			<tr>
				<td>
					<br/>
					<div class="row">
						<div class="col-md-2"></div>
						<div class="col-md-8">
						<div class="panel panel-default" id="panel_efectores">
							<div class="panel-heading">
								<b><a data-toggle="collapse" data-target="#efectores" href="#panel_efectores" class="collapsed">
									Efectores Relacionados
								</a></b>
							</div>
							<div id="efectores" class="panel-collapse collapse">
								<div class="panel-body text-center">
									<table class="table table-condensed table-hover">
										<thead>
											<tr>
												<th width="20%" class="small">Registro</th>
												<th width="20%" class="small">CUIE</th>
												<th width="50%" class="small">Efector</th>
												<th width="10%" class="small">Acciones</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$query = "select 
														nacer.efe_conv.nombre, 
														nacer.efe_conv.cuie,
														usu_efec.id_usuefect					
														from nacer.efe_conv 
														join sistema.usu_efec on (nacer.efe_conv.cuie = sistema.usu_efec.cuie) 
												        join sistema.usuarios on (sistema.usu_efec.id_usuario = sistema.usuarios.id_usuario) 
												        where sistema.usuarios.id_usuario = '$id_usuario' order by nombre";
											
											$res_efe = sql ( $query, "Error al traer los datos de los efectores relacionados" ) or fin_pagina ();
											if ($res_efe->recordCount() > 0) {
							      				while (!$res_efe->EOF) {
							      					$link_borrar = encode_link("usr_efectores_admin.php", array("id_usuefect" => $res_efe->fields ['id_usuefect'], "cuie" => $res_efe->fields ['cuie'], "borra_efec" => "borra_efec", "id_usuario" => $id_usuario));
													echo '<tr>';
													echo '<td>', $res_efe->fields['id_usuefect'], '</td>';
													echo '<td>', $res_efe->fields['cuie'], '</td>';
													echo '<td>', $res_efe->fields['nombre'], '</td>';
													echo '<td class="text-center"><a data-href="',$link_borrar,'" data-toggle="modal" data-target="#confirm-delete" href="#"><span class="glyphicon glyphicon-minus-sign text-danger" aria-hidden="true" title="Eliminar efector"></span></a></td>';
													echo '<tr>';
													$res_efe->MoveNext();
												}
											}
											else {
												echo '<td colspan="4" align="center" class="danger"><strong>No existe ning&uacute;n Efector relacionado con este Usuario</strong></td>';
											}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						</div>
						<div class="col-md-2"></div>
					</div>
				</td>
			</tr>
		 <?php
	}
	?>
 

	
 <tr>
				<td>
				<table width=100% align="center" class="bordes">
					<tr align="center">
						<td><input type=button name="volver" value="Volver"
							onclick="document.location='usr_efectores_listado.php'"
							title="Volver al Listado" style=""></td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>
<?php // ...................... Popup de confirmacion de borrado ------------------------ ?>
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
<?php fin_pagina(); ?>
