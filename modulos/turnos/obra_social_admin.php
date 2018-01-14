<?
require_once ("../../config.php");

echo $html_header;

$id_obra_social = $parametros['id_obra_social'];

$accion = "";
$accion_tipo = "success"; // success=verde info=azul warning=amarillo danger=rojo

if ($_POST['guardar_editar']=='Guardar'){
	$nom_obra_social = trim($_POST['nom_obra_social']);
	$sigla 		   	 = trim($_POST['sigla']);
	$cuit 		   	 = trim($_POST['cuit']);
	$telefono 	   	 = trim($_POST['telefono']);
	$tel_fax 	   	 = trim($_POST['tel_fax']);
	$provincia 	   	 = trim($_POST['provincia']);
	$localidad 	   	 = trim($_POST['localidad']);
	$direccion 	   	 = trim($_POST['direccion']);
	$codigo_postal 	 = trim($_POST['codigo_postal']);

	$error = "";

	if (empty($id_obra_social)) {
		$error .= "Falta el ID de la Obra Social<br/>";
	}

	if (empty($nom_obra_social)) {
		$error .= "Falta ingresar el nombre de la Obra Social<br/>";
	}

	if (empty($sigla)) {
		$error .= "Falta ingresar la sigla de la Obra Social<br/>";
	}

	if (empty($error)) { // No hubo errores
		$db->StartTrans();
		$query="UPDATE nacer.obras_sociales SET 
					nom_obra_social=".$db->Quote($nom_obra_social).",
					sigla=".$db->Quote($sigla).",
					cuit=".$db->Quote($cuit).",
					telefono=".$db->Quote($telefono).",
					tel_fax=".$db->Quote($tel_fax).",
					provincia=".$db->Quote($provincia).",
					localidad=".$db->Quote($localidad).",
					direccion=".$db->Quote($direccion).",
					cp=".$db->Quote($codigo_postal)."
				WHERE id_obra_social=$id_obra_social";	

		sql($query, "Error al insertar/actualizar la Obra Social") or fin_pagina();
	 	 
		$db->CompleteTrans();    
		$accion = "Los datos se actualizaron correctamente";
	}
	else {
		$accion = "Debe resolver los siguientes errores antes de continuar:<br/>$error";
		$accion_tipo = "danger";
	}
}

if ($_POST['guardar']=='Guardar'){
	$nom_obra_social = trim($_POST['nom_obra_social']);
	$sigla 		   	 = trim($_POST['sigla']);
	$cuit 		   	 = trim($_POST['cuit']);
	$telefono 	   	 = trim($_POST['telefono']);
	$tel_fax 	   	 = trim($_POST['tel_fax']);
	$provincia 	   	 = trim($_POST['provincia']);
	$localidad 	   	 = trim($_POST['localidad']);
	$direccion 	   	 = trim($_POST['direccion']);
	$codigo_postal 	 = trim($_POST['codigo_postal']);

	$error = "";

	if (empty($nom_obra_social)) {
		$error .= "Falta ingresar el nombre de la Obra Social<br/>";
	}

	if (empty($sigla)) {
		$error .= "Falta ingresar la sigla de la Obra Social<br/>";
	}

	if (empty($error)) { // No hubo errores
		$verificar_nombre="SELECT id_obra_social FROM nacer.obras_sociales WHERE nom_obra_social=".$db->Quote($nom_obra_social)." OR sigla=".$db->Quote($sigla)."";
		$res_verificar = sql($verificar_nombre, "Error al realizar la verificacion de los datos") or fin_pagina();
	
		if ($res_verificar->recordCount()==0) {
			$query="INSERT INTO nacer.obras_sociales
			   			(nom_obra_social, sigla, cuit, telefono, tel_fax, provincia, localidad, direccion, cp)
			   		VALUES
			   			(
			   			".$db->Quote($nom_obra_social).", 
			   			".$db->Quote($sigla).", 
			   			".$db->Quote($cuit).", 
			   			".$db->Quote($telefono).", 
			   			".$db->Quote($tel_fax).", 
			   			".$db->Quote($provincia).", 
			   			".$db->Quote($localidad).", 
			   			".$db->Quote($direccion).", 
			   			".$db->Quote($codigo_postal)."
			   			)
					RETURNING id_obra_social
					";
				
			$res_insert = sql($query, "Error al insertar/actualizar la Obra Social") or fin_pagina();
			
			$id_obra_social = $res_insert->fields['id_obra_social'];

			$accion="Los datos se han guardado correctamente ($id_obra_social)";
		} else {
			$accion = "Ya existe una Obra Social con ese nombre o sigla";
			$accion_tipo = "danger";
		}
	}
	else {
		$accion = "Debe resolver los siguientes errores antes de continuar:<br/>$error";
		$accion_tipo = "danger";
	}
}

if ($_POST['borrar']=='Borrar') {
	if (!empty($id_obra_social)) {
		$verificar_id="SELECT id_obra_social FROM nacer.obras_sociales WHERE id_obra_social=".$id_obra_social;
		$res_verificar = sql($verificar_id, "Error al realizar la verificacion de los datos") or fin_pagina();
		if ($res_verificar->recordCount() > 0) {
			$query="DELETE FROM nacer.obras_sociales WHERE id_obra_social=".$id_obra_social;
			sql($query, "Error al eliminar la Obra Social") or fin_pagina();
			$accion="Los datos se han borrado correctamente";
		}
		else {
			$accion = "No se encontr&oacute; la Obra Social para borrar con el ID=$id_obra_social";
			$accion_tipo = "danger";
		}
		$id_obra_social = "";
	}
	else {
		$accion = "Falta el ID de la Obra Social a borrar";
		$accion_tipo = "danger";
	}
}

if ($id_obra_social) {
	$query = "SELECT * FROM nacer.obras_sociales  WHERE id_obra_social=$id_obra_social";
	$res = sql($query, "Error al traer los datos de la Obra Social") or fin_pagina();
	$nom_obra_social = $res->fields['nom_obra_social'];
	$sigla 		   	 = $res->fields['sigla'];
	$cuit 		   	 = $res->fields['cuit'];
	$telefono 	   	 = $res->fields['telefono'];
	$tel_fax 	   	 = $res->fields['tel_fax'];
	$provincia 	   	 = $res->fields['provincia'];
	$localidad 	   	 = $res->fields['localidad'];
	$direccion 	   	 = $res->fields['direccion'];
	$codigo_postal 	 = $res->fields['cp'];
}

$query_provincias = "SELECT nombre FROM uad.provincias ORDER BY nombre ASC";
$res_provincias = sql($query_provincias, "Error al traer los datos de las provincias") or fin_pagina();

?>
<script type="text/javascript">
	function control_nuevos() {
		if(!$("input[name=nom_obra_social]").val()){
			alert('Debe ingresar el nombre');
			return false;
		} 
		if(!$("input[name=sigla]").val()){
			alert('Debe ingresar la sigla');
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
</script>

<?php $link = encode_link("obra_social_admin.php",array("id_obra_social"=>$id_obra_social)); ?>
<form name='form1' action='<?php echo $link; ?>' method='POST'>

<?php if (!empty($accion)) { ?>
<div class="container alert alert-<?php echo $accion_tipo; ?> alert-dismissible" role="alert" style="width:40%;">
	<button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
    <p><strong><?php echo $accion; ?></strong></p>
</div>
<?php } ?>

<table width="85%" cellspacing="0" border="1" bordercolor="#E0E0E0" align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
	<tr id="mo">
    	<td>
	    	<?php if (!$id_obra_social) { ?>  
	    		<font size="+1"><b>Nuevo Dato</b></font>   
	    	<?php } else { ?>
	        	<font size="+1"><b><?php echo $nom_obra_social; ?></b></font>   
	        <?php } ?>
	    </td>
 	</tr>
	<tr>
		<td>
			<table width="90%" align="center" class="bordes">
			    <tr>
			    	<td id="mo">
			       		<b>Descripci&oacute;n de la Obra Social</b>
			      	</td>
			    </tr>
			    <tr>
			       	<td>
			        	<table width="100%">
			         		<tr>	           
			           			<td align="center" colspan="2">
			            			<b>N&uacute;mero del Dato: <font size="+1" color="Red"> <?php echo ($id_obra_social)? $id_obra_social : "Nuevo Dato"?></font></b>
			           			</td>
			         		</tr>
			        		<tr>
			         			<td align="right" width="40%"><b>Nombre:</b></td>
			            		<td align="left" width="60%">
			              			<input type="text" size="40" class="editable" value="<?php echo $nom_obra_social; ?>" name="nom_obra_social" <? if ($id_obra_social) echo "disabled"?>>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>Sigla:</b></td>
			            		<td align="left">
			              			<input type="text" size="40" class="editable" value="<?php echo $sigla; ?>" name="sigla" <? if ($id_obra_social) echo "disabled"?>>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>CUIT:</b></td>
			            		<td align="left">
			              			<input type="text" size="40" class="editable" value="<?php echo $cuit; ?>" name="cuit" <? if ($id_obra_social) echo "disabled"?>>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>Tel&eacute;fono:</b></td>
			            		<td align="left">
			              			<input type="text" size="40" class="editable" value="<?php echo $telefono; ?>" name="telefono" <? if ($id_obra_social) echo "disabled"?>>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>Fax:</b></td>
			            		<td align="left">
			              			<input type="text" size="40" class="editable" value="<?php echo $tel_fax; ?>" name="tel_fax" <? if ($id_obra_social) echo "disabled"?>>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>Provincia:</b></td>
			            		<td align="left">
			              			<select class="editable" name="provincia" <? if ($id_obra_social) echo "disabled"?>>
			              				<option></option>
			              				<?php
			              				while (!$res_provincias->EOF) {
			              					$selected = "";
											if ($res_provincias->fields['nombre'] == $provincia) {
												$selected = " selected";
											}
											echo "<option".$selected.">".$res_provincias->fields['nombre']."</option>";
											$res_provincias->MoveNext();
										} 
										?>
			              			</select>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>Localidad:</b></td>
			            		<td align="left">
			              			<input type="text" size="40" class="editable" value="<?php echo $localidad; ?>" name="localidad" <? if ($id_obra_social) echo "disabled"?>>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>Direcci&oacute;n:</b></td>
			            		<td align="left">
			              			<input type="text" size="40" class="editable" value="<?php echo $direccion; ?>" name="direccion" <? if ($id_obra_social) echo "disabled"?>>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>Código Postal:</b></td>
			            		<td align="left">
			              			<input type="text" size="40" class="editable" value="<?php echo $codigo_postal; ?>" name="codigo_postal" <? if ($id_obra_social) echo "disabled"?>>
			            		</td>
			         		</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center">
						<br/>
						<?php if ($id_obra_social) { ?>
							<input class="btn btn-default btn-xs" type="button" name="editar" value="Editar" onclick="editar_campos()" title="Edita Campos" style="width=130px"> &nbsp;&nbsp;
						    <input class="btn btn-default btn-xs" type="submit" name="guardar_editar" value="Guardar" title="Guardar" disabled style="width=130px" onclick="return control_nuevos()">&nbsp;&nbsp;
						    <input class="btn btn-default btn-xs" type="button" name="cancelar_editar" value="Cancelar" title="Cancela Edici&oacute;n" disabled style="width=130px" onclick="document.location.reload()">&nbsp;&nbsp;	      
						    <input class="btn btn-default btn-xs" type="submit" name="borrar" value="Borrar" style="width=130px" onclick="return confirm('Esta seguro que desea eliminar esta Obra Social?')" >
						<?php } else { ?>
					    	<input class="btn btn-default btn-xs" type="submit" name="guardar" value="Guardar" title="Guardar" style="width=130px" onclick="return control_nuevos()">
				 		<?php }	?>
					</td>	
				</tr>
			 	<tr>
			 		<td align="center" class="bordes">
			 			<br/>
			     		<input class="btn btn-default btn-xs" type="button" name="volver" value="Volver" onclick="document.location='obras_sociales.php'"title="Volver al Listado" style="width=150px">
			     	</td>
			  	</tr>
			</table>
		</td>
	</tr>
</table>
</form>
 
<?php fin_pagina(); ?>