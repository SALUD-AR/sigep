<?
require_once ("../../config.php");

echo $html_header;

cargar_calendario();

$id_medico = $parametros['id_medico'];

$dias_arr = array('dom', 'lun', 'mar', 'mie', 'jue', 'vie', 'sab');

$mensaje = "";
$mensaje_tipo = "success"; // success=verde info=azul warning=amarillo danger=rojo

if ($_POST["agenda_accion"] == "modificar") {
	$error = "";

	$id_agenda = $_POST["agenda_id"];

	$id_especialidad = $_POST["agenda_especialidad"];
	if (empty($id_especialidad) || !is_numeric($id_especialidad)) {
		$error .= "Falta seleccionar la especialidad<br/>";
	}

	$dias_values = array();
	$dias_count = 0;
	foreach ($dias_arr as $dia) {
		if (isset($_POST['dia_'.$dia])) {
			$dias_values[$dia] = 't';
			$dias_count++;
		}
		else {
			$dias_values[$dia] = 'f';
		}
	}
	if ($dias_count == 0) {
		$error .= "Falta seleccionar al menos un d&iacute;a de la semana<br/>";
	}

	$hora_inicio = $_POST["hora_inicio"];
	$hora_fin = $_POST["hora_fin"];
	if (empty($hora_inicio)) {
		$error .= "Falta ingresar la hora de inicio<br/>";
	}
	if (empty($hora_fin)) {
		$error .= "Falta ingresar la hora de fin<br/>";
	}
	if (!empty($hora_inicio) && !empty($hora_fin)) {
		$hora_inicio_tmp = new DateTime($hora_inicio);
		$hora_fin_tmp = new DateTime($hora_fin);
		if ($hora_inicio_tmp >= $hora_fin_tmp) {
			$error .= "La hora de fin debe ser posterior a la hora de inicio<br/>";
		}
	}

	$fecha_inicio = $_POST["fecha_inicio"];
	$fecha_fin = $_POST["fecha_fin"];
	if ((!empty($fecha_inicio) && empty($fecha_fin)) ||
		(empty($fecha_inicio) && !empty($fecha_fin))
		) {
		$error .= "Los campos de fecha de inicio y fin son opcionales, pero si se ingresa el valor para uno, tambi&eacute;n debe ingresar el valor del otro.";
	}
	if (!empty($fecha_inicio) && !empty($fecha_fin)) {
		$fecha_inicio_tmp = new DateTime(Fecha_db($fecha_inicio));
		$fecha_fin_tmp = new DateTime(Fecha_db($fecha_fin));

		if ($fecha_inicio_tmp >= $fecha_fin_tmp) {
			$error .= "La fecha de fin debe ser posterior a la fecha de inicio<br/>";
		}
	}
	if (empty($error)) { // No hubo errores
		$query="UPDATE nacer.agendas SET
						id_especialidad_medico=".$db->Quote($id_especialidad).",
						dom=".$db->Quote($dias_values['dom']).",
						lun=".$db->Quote($dias_values['lun']).",
						mar=".$db->Quote($dias_values['mar']).",
						mie=".$db->Quote($dias_values['mie']).",
						jue=".$db->Quote($dias_values['jue']).",
						vie=".$db->Quote($dias_values['vie']).",
						sab=".$db->Quote($dias_values['sab']).",
						hora_inicio=".$db->Quote($hora_inicio).",
						hora_fin=".$db->Quote($hora_fin).",
						fecha_inicio=".(empty($fecha_inicio) ? "NULL" : $db->Quote(Fecha_db($fecha_inicio))).",
						fecha_fin=".(empty($fecha_fin) ? "NULL" : $db->Quote(Fecha_db($fecha_fin)))."
				WHERE id=".$db->Quote($id_agenda);	

		sql($query, "Error al actualizar los datos de la Agenda") or fin_pagina();
		$mensaje = "Los datos se actualizaron correctamente";
	}
	else {
		$mensaje = "Debe resolver los siguientes errores antes de continuar:<br/>$error";
		$mensaje_tipo = "danger";
	}
}

if ($_POST["agenda_accion"] == "agregar") {
	$error = "";

	$id_especialidad = intval($_POST["agenda_especialidad"]);
	if (empty($id_especialidad) || $id_especialidad <= 0) {
		$error .= "Falta seleccionar la especialidad<br/>";
	}

	$dias_values = array();
	$dias_count = 0;
	foreach ($dias_arr as $dia) {
		if (isset($_POST['dia_'.$dia])) {
			$dias_values[$dia] = 't';
			$dias_count++;
		}
		else {
			$dias_values[$dia] = 'f';
		}
	}
	if ($dias_count == 0) {
		$error .= "Falta seleccionar al menos un d&iacute;a de la semana<br/>";
	}

	$hora_inicio = $_POST["hora_inicio"];
	$hora_fin = $_POST["hora_fin"];
	if (empty($hora_inicio)) {
		$error .= "Falta ingresar la hora de inicio<br/>";
	}
	if (empty($hora_fin)) {
		$error .= "Falta ingresar la hora de fin<br/>";
	}
	if (!empty($hora_inicio) && !empty($hora_fin)) {
		$hora_inicio_tmp = new DateTime($hora_inicio);
		$hora_fin_tmp = new DateTime($hora_fin);
		if ($hora_inicio_tmp >= $hora_fin_tmp) {
			$error .= "La hora de fin debe ser posterior a la hora de inicio<br/>";
		}
	}

	$fecha_inicio = $_POST["fecha_inicio"];
	$fecha_fin = $_POST["fecha_fin"];
	if ((!empty($fecha_inicio) && empty($fecha_fin)) ||
		(empty($fecha_inicio) && !empty($fecha_fin))
		) {
		$error .= "Los campos de fecha de inicio y fin son opcionales, pero si se ingresa el valor para uno, tambi&eacute;n debe ingresar el valor del otro.";
	}
	if (!empty($fecha_inicio) && !empty($fecha_fin)) {
		$fecha_inicio_tmp = new DateTime(Fecha_db($fecha_inicio));
		$fecha_fin_tmp = new DateTime(Fecha_db($fecha_fin));

		if ($fecha_inicio_tmp >= $fecha_fin_tmp) {
			$error .= "La fecha de fin debe ser posterior a la fecha de inicio<br/>";
		}
	}

	$duracion = intval($_POST["duracion"]);
	if (empty($duracion) || $duracion <= 0) {
		$error .= "Debe ingresar la duraci&oacute;n del turno<br/>";
	}
	if (empty($error)) { // No hubo errores
		$query="INSERT INTO nacer.agendas (id_especialidad_medico, dom, lun, mar, mie, jue, vie, sab, hora_inicio, hora_fin, fecha_inicio, fecha_fin, duracion) 
				VALUES ({$id_especialidad}, 
						".$db->Quote($dias_values['dom']).",
						".$db->Quote($dias_values['lun']).",
						".$db->Quote($dias_values['mar']).",
						".$db->Quote($dias_values['mie']).",
						".$db->Quote($dias_values['jue']).",
						".$db->Quote($dias_values['vie']).",
						".$db->Quote($dias_values['sab']).",
						".$db->Quote($hora_inicio).",
						".$db->Quote($hora_fin).",
						".(empty($fecha_inicio) ? "NULL" : $db->Quote(Fecha_db($fecha_inicio))).",
						".(empty($fecha_fin) ? "NULL" : $db->Quote(Fecha_db($fecha_fin))).",
						{$duracion}
						)";	

		sql($query, "Error al insertar los datos de la Agenda") or fin_pagina();
		$mensaje = "Los datos se ingresaron correctamente";
		$id_especialidad = $hora_inicio = $hora_fin = $fecha_inicio = $fecha_fin = $duracion = "";
		$dias_values = array();

	}
	else {
		$mensaje = "Debe resolver los siguientes errores antes de continuar:<br/>$error";
		$mensaje_tipo = "danger";
	}
}

// agregar un nuevo efector al medico
if (isset($parametros["nuevo_id_efector"]) && 
	!empty($parametros["nuevo_id_efector"]) &&
	is_numeric($parametros["nuevo_id_efector"]) &&
	intval($parametros["nuevo_id_efector"]) > 0 &&
	isset($parametros["id_parent"]) && 
	!empty($parametros["id_parent"]) &&
	is_numeric($parametros["id_parent"]) &&
	intval($parametros["id_parent"]) > 0
	) {
	$id_medico = $parametros["id_parent"];
	$query_add = "SELECT id FROM medicos_efectores
					WHERE id_efector = {$parametros["nuevo_id_efector"]}
					AND id_medico = {$id_medico}";
	$res_add = sql($query_add, "Error al verificar la existencia del Efector") or fin_pagina();
	if ($res_add->recordCount()==0) {
		$query_add = "INSERT INTO medicos_efectores (id_efector, id_medico)
						VALUES ({$parametros["nuevo_id_efector"]}, {$id_medico})";
		$res_add = sql($query_add, "Error al asignar el nuevo Efector") or fin_pagina();
		if ($res_add && $db->Affected_Rows() == 1) {
			$mensaje = "El Efector seleccionado se ha asignado correctamente!";
		}
		else {
			$mensaje = "Error al asignar el Efector seleccionado!";
			$mensaje_tipo = "danger";
		}
	}
	else {
		$mensaje = "El Efector seleccionado ya se encuentra asignado a este M&eacute;dico!";
		$mensaje_tipo = "warning";
	}
}

// agregar una nueva especialidad al medico
if (isset($parametros["nuevo_id_especialidad"]) && 
	!empty($parametros["nuevo_id_especialidad"]) &&
	is_numeric($parametros["nuevo_id_especialidad"]) &&
	intval($parametros["nuevo_id_especialidad"]) > 0 &&
	isset($parametros["id_parent"]) && 
	!empty($parametros["id_parent"]) &&
	is_numeric($parametros["id_parent"]) &&
	intval($parametros["id_parent"]) > 0
	) {
	$id_medico = $parametros["id_parent"];
	$query_add = "SELECT id FROM especialidades_medicos 
					WHERE id_especialidad = {$parametros["nuevo_id_especialidad"]}
					AND id_medico = {$id_medico}";
	$res_add = sql($query_add, "Error al verificar la existencia de la Especialidad") or fin_pagina();
	if ($res_add->recordCount()==0) {
		$query_add = "INSERT INTO especialidades_medicos (id_especialidad, id_medico)
						VALUES ({$parametros["nuevo_id_especialidad"]}, {$id_medico})";
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
		$mensaje = "La Especialidad seleccionada ya se encuentra asignada a este M&eacute;dico!";
		$mensaje_tipo = "warning";
	}
}

// eliminar el efector seleccionado
if (isset($parametros["eliminar_efector"]) && 
	!empty($parametros["eliminar_efector"]) &&
	is_numeric($parametros["eliminar_efector"])
	) {
	$query_del = "DELETE FROM medicos_efectores WHERE id = {$parametros["eliminar_efector"]}";
	$res_del = sql($query_del, "Error al eliminar el Efector") or fin_pagina();
	if ($res_del && $db->Affected_Rows() == 1) {
		$mensaje = "El Efector se ha eliminado correctamente!";
	}
	else {
		$mensaje = "Error al eliminar el Efector!";
		$mensaje_tipo = "danger";
	}
}

// eliminar la agenda seleccionada
if (isset($parametros["eliminar_agenda"]) && 
	!empty($parametros["eliminar_agenda"]) &&
	is_numeric($parametros["eliminar_agenda"])
	) {
	$query_del = "DELETE FROM agendas WHERE id = {$parametros["eliminar_agenda"]}";
	$res_del = sql($query_del, "Error al eliminar la Agenda") or fin_pagina();
	if ($res_del && $db->Affected_Rows() == 1) {
		$mensaje = "La Agenda se ha eliminado correctamente!";
	}
	else {
		$mensaje = "Error al eliminar la Agenda!";
		$mensaje_tipo = "danger";
	}
}

// eliminar la especialidad seleccionada
if (isset($parametros["eliminar_especialidad"]) && 
	!empty($parametros["eliminar_especialidad"]) &&
	is_numeric($parametros["eliminar_especialidad"])
	) {
	$query_del = "DELETE FROM especialidades_medicos WHERE especialidades_medicos.id = {$parametros["eliminar_especialidad"]}";
	$res_del = sql($query_del, "Error al eliminar la Especialidad") or fin_pagina();
	if ($res_del && $db->Affected_Rows() == 1) {
		$mensaje = "La Especialidad se ha eliminado correctamente!";
	}
	else {
		$mensaje = "Error al eliminar la Especialidad!";
		$mensaje_tipo = "danger";
	}
}

if ($_POST['guardar_editar']=='Guardar'){
	$apellido	   	= strtoupper(trim($_POST['apellido']));
	$nombre 		= strtoupper(trim($_POST['nombre']));
	$sexo 	   	 	= trim($_POST['sexo']);
	$dni 	   	 	= trim($_POST['dni']);
	$fecha_nac 		= trim($_POST['fecha_nac']);
	$email 	   	 	= trim($_POST['email']);
	$telefono 	   	= trim($_POST['telefono']);
	$domicilio 	   	= trim($_POST['domicilio']);
	$codigo_postal 	= trim($_POST['codigo_postal']);

	$error = "";

	if (empty($id_medico)) {
		$error .= "Falta el ID del M&eacute;dico<br/>";
	}

	if (empty($nombre)) {
		$error .= "Falta ingresar el nombre del M&eacute;dico<br/>";
	}

	if (empty($apellido)) {
		$error .= "Falta ingresar el apellido del M&eacute;dico<br/>";
	}

	if (empty($sexo) || ($sexo != "MASCULINO" && $sexo != "FEMENINO")) {
		$error .= "Falta seleccionar el sexo del M&eacute;dico<br/>";
	}

	if (!empty($fecha_nac)) {
		if (FechaOk($fecha_nac)) {
			$fecha_nac = Fecha_db($fecha_nac);
		}
		else {
			$error .= "La fecha de nacimiento ingresada no es v&aacute;lida<br/>";
		}
	}

	if (empty($error)) { // No hubo errores
		$db->StartTrans();
		$query="UPDATE nacer.medicos SET 
					apellido=".$db->Quote($apellido).",
					nombre=".$db->Quote($nombre).",
					sexo=".$db->Quote($sexo).",
					dni=".$db->Quote($dni).",
					fecha_nac=".$db->Quote($fecha_nac).",
					email=".$db->Quote($email).",
					telefono=".$db->Quote($telefono).",
					domicilio=".$db->Quote($domicilio).",
					cod_postal=".$db->Quote($codigo_postal)."
				WHERE id_medico=$id_medico";	

		sql($query, "Error al insertar/actualizar los datos del M&eacute;dico") or fin_pagina();
	 	 
		$db->CompleteTrans();    
		$mensaje = "Los datos se actualizaron correctamente";
	}
	else {
		$mensaje = "Debe resolver los siguientes errores antes de continuar:<br/>$error";
		$mensaje_tipo = "danger";
	}
}

if ($_POST['guardar']=='Guardar'){
	$apellido	   	= strtoupper(trim($_POST['apellido']));
	$nombre 		= strtoupper(trim($_POST['nombre']));
	$sexo 	   	 	= trim($_POST['sexo']);
	$dni 	   	 	= trim($_POST['dni']);
	$fecha_nac 		= trim($_POST['fecha_nac']);
	$email 	   	 	= trim($_POST['email']);
	$telefono 	   	= trim($_POST['telefono']);
	$domicilio 	   	= trim($_POST['domicilio']);
	$codigo_postal 	= trim($_POST['codigo_postal']);

	$error = "";

	if (empty($nombre)) {
		$error .= "Falta ingresar el nombre del M&eacute;dico<br/>";
	}

	if (empty($apellido)) {
		$error .= "Falta ingresar la apellido del M&eacute;dico<br/>";
	}

	if (empty($sexo) || ($sexo != "MASCULINO" && $sexo != "FEMENINO")) {
		$error .= "Falta seleccionar el sexo del M&eacute;dico<br/>";
	}

	if (!empty($fecha_nac)) {
		if (FechaOk($fecha_nac)) {
			$fecha_nac = Fecha_db($fecha_nac);
		}
		else {
			$error .= "La fecha de nacimiento ingresada no es v&aacute;lida<br/>";
		}
	}

	if (empty($error)) { // No hubo errores
		$verificar_nombre="SELECT id_medico FROM nacer.medicos WHERE nombre=".$db->Quote($nombre)." AND apellido=".$db->Quote($apellido)."";
		$res_verificar = sql($verificar_nombre, "Error al realizar la verificacion de los datos") or fin_pagina();
	
		if ($res_verificar->recordCount()==0) {
			$query="INSERT INTO nacer.medicos
			   			(nombre, apellido, fecha_nac, telefono, email, sexo, dni, domicilio, cod_postal)
			   		VALUES
			   			(
			   			".$db->Quote($nombre).", 
			   			".$db->Quote($apellido).", 
			   			".$db->Quote($fecha_nac).", 
			   			".$db->Quote($telefono).", 
			   			".$db->Quote($email).", 
			   			".$db->Quote($sexo).", 
			   			".$db->Quote($dni).", 
			   			".$db->Quote($domicilio).", 
			   			".$db->Quote($codigo_postal)."
			   			)
					RETURNING id_medico
					";
				
			$res_insert = sql($query, "Error al insertar/actualizar los datos del M&eacute;dico") or fin_pagina();
			
			$id_medico = $res_insert->fields['id_medico'];

			$mensaje="Los datos se han guardado correctamente";
		} else {
			$mensaje = "Ya existe un M&eacute;dico con ese nombre y apellido";
			$mensaje_tipo = "danger";
		}
	}
	else {
		$mensaje = "Debe resolver los siguientes errores antes de continuar:<br/>$error";
		$mensaje_tipo = "danger";
	}
}

if ($_POST['borrar']=='Borrar') {
	if (!empty($id_medico)) {
		$verificar_id="SELECT id_medico FROM nacer.medicos WHERE id_medico=".$id_medico;
		$res_verificar = sql($verificar_id, "Error al realizar la verificacion de los datos") or fin_pagina();
		if ($res_verificar->recordCount() > 0) {
			$query="DELETE FROM nacer.medicos WHERE id_medico=".$id_medico;
			sql($query, "Error al eliminar los datos del M&eacute;dico") or fin_pagina();
			$mensaje="Los datos se han borrado correctamente";
		}
		else {
			$mensaje = "No se encontr&oacute; el M&eacute;dico para borrar con el ID=$id_medico";
			$mensaje_tipo = "danger";
		}
		$id_medico = "";
	}
	else {
		$mensaje = "Falta el ID del M&eacute;dico a borrar";
		$mensaje_tipo = "danger";
	}
}

if ($id_medico) {
	$query = "SELECT * FROM nacer.medicos  WHERE id_medico={$id_medico}";
	$res = sql($query, "Error al traer los datos del M&eacute;dico") or fin_pagina();
	$apellido 		= $res->fields['apellido'];
	$nombre 		= $res->fields['nombre'];
	$sexo 	   	 	= $res->fields['sexo'];
	$dni 	   	 	= $res->fields['dni'];
	$fecha_nac 		= $res->fields['fecha_nac'];
	$email 	   	 	= $res->fields['email'];
	$telefono 	   	= $res->fields['telefono'];
	$domicilio 	 	= $res->fields['domicilio'];
	$codigo_postal 	= $res->fields['cod_postal'];

	$query_efe = "SELECT 
					  nacer.medicos_efectores.id,
					  nacer.medicos.apellido AS medico_apellido,
					  nacer.medicos.nombre AS medico_nombre,
					  nacer.efe_conv.nombre AS efector_nombre,
					  nacer.efe_conv.cuidad AS efector_ciudad,
					  nacer.efe_conv.cuie AS efector_cuie
					FROM
					  nacer.medicos_efectores
					  LEFT OUTER JOIN nacer.medicos ON (nacer.medicos_efectores.id_medico = nacer.medicos.id_medico)
					  LEFT OUTER JOIN nacer.efe_conv ON (nacer.medicos_efectores.id_efector = nacer.efe_conv.id_efe_conv)
				  WHERE
				    nacer.medicos_efectores.id_medico = {$id_medico}
				  ORDER BY nacer.efe_conv.nombre";
	$res_efe = sql($query_efe, "al traer los datos de los Efectores") or fin_pagina();

	$query_esp = "SELECT 
				    especialidades_medicos.id,
				    especialidades.nom_titulo,
				    especialidades.especialidad
				  FROM
				    especialidades_medicos
				    LEFT OUTER JOIN especialidades ON (especialidades_medicos.id_especialidad = especialidades.id_especialidad)
				  WHERE
				    especialidades_medicos.id_medico = {$id_medico}
				  ORDER BY especialidades.nom_titulo";
	$res_esp = sql($query_esp, "al traer los datos de las Especialidades") or fin_pagina();

	$query_agenda = "SELECT 
					  nacer.especialidades_medicos.id,
					  nacer.especialidades.nom_titulo,
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
					  nacer.especialidades_medicos
					  LEFT OUTER JOIN nacer.especialidades ON (nacer.especialidades_medicos.id_especialidad = nacer.especialidades.id_especialidad)
					  RIGHT OUTER JOIN nacer.agendas ON (nacer.especialidades_medicos.id = nacer.agendas.id_especialidad_medico)
					WHERE
					  nacer.especialidades_medicos.id_medico = {$id_medico}
					ORDER BY
					  nacer.especialidades.nom_titulo,
					  nacer.agendas.id
					";
	$res_agenda = sql($query_agenda, "al traer los datos de las Agendas") or fin_pagina();
}

?>
<script type="text/javascript">
	function control_nuevos() {
		if(!$("input[name=nombre]").val()){
			alert('Debe ingresar el nombre');
			return false;
		} 
		if(!$("input[name=apellido]").val()){
			alert('Debe ingresar la apellido');
			return false;
		} 
		if(!$("select[name=sexo]").val()){
			alert('Debe seleccionar el sexo');
			return false;
		} 
		return true;
	}

	function editar_campos() {	
		$("form .editable").prop('disabled', false);
		$("input[name=fecha_nac]").next("img").show();
		$("input[name=editar]").prop('disabled', true);
		$("input[name=guardar_editar]").prop('disabled', false);
		$("input[name=cancelar_editar]").prop('disabled', false);
	}

	function validar_agenda() {
		var especialidad = $('#agenda-especialidad').val();
		var dias = $('input.agenda-dia:checked');
		var hora_inicio = $('#agenda-hora-inicio').val();
		var hora_fin = $('#agenda-hora-fin').val();
		var fecha_inicio = $('#agenda-fecha-inicio').val();
		var fecha_fin = $('#agenda-fecha-fin').val();
		var duracion = parseInt($('#agenda-duracion').val());
		if (especialidad == '') {
			alert('Debe seleccionar la Especialidad');
			return false;
		}
		if (dias.length <= 0) {
			alert('Debe seleccionar al menos un día de la semana');
			return false;
		}
		if (hora_inicio == '') {
			alert('Debe ingresar la hora de inicio de la agenda');
			return false;
		}
		if (hora_fin == '') {
			alert('Debe ingresar la hora de fin de la agenda');
			return false;
		}
		if (!moment(hora_fin, 'HH:mm').isAfter(moment(hora_inicio, 'HH:mm'))) {
			alert('La hora de fin debe ser posterior a la hora de inicio');
			return false;
		}
		if (fecha_inicio != '' && !moment(fecha_inicio, 'DD/MM/YYYY').isValid()) {
			alert('La fecha de inicio ingresada no es válida');
			return false;
		}
		if (fecha_fin != '' && !moment(fecha_fin, 'DD/MM/YYYY').isValid()) {
			alert('La fecha de fin ingresada no es válida');
			return false;
		}
		if ((fecha_inicio != '' && fecha_fin == '') ||
			(fecha_inicio == '' && fecha_fin != '')) {
			alert('Los campos de fecha de inicio y fin son opcionales, pero si se ingresa el valor para uno, también debe ingresar el valor del otro.');
			return false;
		}
		if ((fecha_inicio != '' && fecha_fin != '') && 
			!moment(fecha_fin, 'DD/MM/YYYY').isAfter(moment(fecha_inicio, 'DD/MM/YYYY'))) {
			alert('La fecha de fin debe ser posterior a la fecha de inicio');
			return false;
		}
		if (isNaN(duracion) || duracion <= 0) {
			alert('Debe ingresar la duración del turno');
			return false;
		}
		return true;
	}

	$(document).ready(function() {
		// permitir solo el ingreso de numeros en los campos con la clase "numeric"
		$('input.numeric').keyup(function() {     
  			this.value = this.value.replace(/[^0-9]/g,'');
		});
		// ocultar el icono del calendario
		$("input[name=fecha_nac]").next("img").hide();

		$('#confirm-delete').on('show.bs.modal', function(e) {
			$(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
		});

		$('#add-edit-agenda').on('show.bs.modal', function(e) {
			if ($(e.relatedTarget).hasClass('btn-agregar-agenda')) {
				$("#agenda-form")[0].reset();
				$('#agenda-form #agenda-accion').val('agregar');
				$('#agenda-form .agenda-titulo').text('Agregar');
			}
			else if ($(e.relatedTarget).hasClass('btn-modificar-agenda')) {
				$('#agenda-form #agenda-accion').val('modificar');
				$('#agenda-form .agenda-titulo').text('Modificar');
				var row_id = $(e.relatedTarget).closest('tr').attr('id');
				if (row_id) {
					$('#agenda-form #agenda-especialidad').val('Modificar');
					$('#agenda-form #agenda-especialidad option').filter(function () { 
						return $(this).html() == $('#'+row_id+' .agenda-especialidad').html();
					}).prop('selected', true);
					var dias_arr = $('#'+row_id+' .agenda-dias').text().split(', ');
					$.each(dias_arr, function(key, dia) {
						$('#agenda-form #dia-'+dia.toLowerCase()).prop('checked', true);
					});
					$('#agenda-form #agenda-hora-inicio').val($('#'+row_id+' .agenda-hora-inicio').text());
					$('#agenda-form #agenda-hora-fin').val($('#'+row_id+' .agenda-hora-fin').text());
					$('#agenda-form #agenda-fecha-inicio').val($('#'+row_id+' .agenda-fecha-inicio').text());
					$('#agenda-form #agenda-fecha-fin').val($('#'+row_id+' .agenda-fecha-fin').text());
					$('#agenda-form #agenda-duracion').val($('#'+row_id+' .agenda-duracion').text());

					var row_id_arr = row_id.split('-');
					$('#agenda-form #agenda-id').val(row_id_arr[1]);

				}
			}
		});

		$('#agenda-hora-inicio,#agenda-hora-fin').datetimepicker({
			language: 'es',
			format: 'HH:mm',
			pickDate: false,
			pick12HourFormat: false,
			pickSeconds: false,
			minuteStepping: 5
		});
		$('#agenda-hora-inicio,#agenda-hora-fin').on("dp.show",function (e) {
			$(this).data("DateTimePicker").setDate($(this).val());
		});
		$('#agenda-fecha-inicio,#agenda-fecha-fin').datetimepicker({
			language: 'es',
			format: 'DD/MM/YYYY',
			pickDate: true,
			pickTime: false
		});
		$("#agenda-fecha-inicio").on("dp.change",function (e) {
			$('#agenda-fecha-fin').data("DateTimePicker").setMinDate(e.date);
		});
		$("#agenda-fecha-fin").on("dp.change",function (e) {
			$('#agenda-fecha-inicio').data("DateTimePicker").setMaxDate(e.date);
		});
	});
</script>

<?php $link = encode_link("medico_admin.php",array("id_medico"=>$id_medico)); ?>
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
	    	<?php if (!$id_medico) { ?>  
	    		<font size="+1"><b>Nuevo Dato</b></font>   
	    	<?php } else { ?>
	        	<font size="+1"><b><?php echo $apellido.", ".$nombre; ?></b></font>   
	        <?php } ?>
	    </td>
 	</tr>
	<tr>
		<td>
			<table width="90%" align="center" class="bordes">
			    <tr>
			    	<td id="mo">
			       		<b>Descripci&oacute;n del M&eacute;dico</b>
			      	</td>
			    </tr>
			    <tr>
			       	<td>
			        	<table width="100%">
			         		<tr>	           
			           			<td align="center" colspan="2">
			            			<b>N&uacute;mero del Dato: <font size="+1" color="Red"> <?php echo ($id_medico)? $id_medico : "Nuevo Dato"?></font></b>
			           			</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>Apellido:</b></td>
			            		<td align="left">
			              			<input type="text" size="40" class="editable" value="<?php echo $apellido; ?>" name="apellido" <? if ($id_medico) echo "disabled"?>>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right" width="40%"><b>Nombre:</b></td>
			            		<td align="left" width="60%">
			              			<input type="text" size="40" class="editable" value="<?php echo $nombre; ?>" name="nombre" <? if ($id_medico) echo "disabled"?>>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>Sexo:</b></td>
			            		<td align="left">
			              			<select class="editable" name="sexo" <? if ($id_medico) echo "disabled"?>>
			              				<option></option>
			              				<option value="MASCULINO" <? if ($sexo == "MASCULINO") echo "selected"?>>Masculino</option>
			              				<option value="FEMENINO" <? if ($sexo == "FEMENINO") echo "selected"?>>Femenino</option>
			              			</select>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>DNI:</b></td>
			            		<td align="left">
			              			<input type="text" size="40" class="editable numeric" value="<?php echo $dni; ?>" name="dni" <? if ($id_medico) echo "disabled"?>>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>Fecha de nacimiento:</b></td>
			            		<td align="left">
			              			<input type="text" size="40" class="editable" value="<?php echo Fecha($fecha_nac); ?>" name="fecha_nac" <? if ($id_medico) echo "disabled"?> readonly>
			              			<?php echo link_calendario("fecha_nac"); ?>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>Email:</b></td>
			            		<td align="left">
			              			<input type="text" size="40" class="editable" value="<?php echo $email; ?>" name="email" <? if ($id_medico) echo "disabled"?>>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>Tel&eacute;fono:</b></td>
			            		<td align="left">
			              			<input type="text" size="40" class="editable" value="<?php echo $telefono; ?>" name="telefono" <? if ($id_medico) echo "disabled"?>>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>Direcci&oacute;n:</b></td>
			            		<td align="left">
			              			<input type="text" size="40" class="editable" value="<?php echo $domicilio; ?>" name="domicilio" <? if ($id_medico) echo "disabled"?>>
			            		</td>
			         		</tr>
			        		<tr>
			         			<td align="right"><b>C&oacute;digo Postal:</b></td>
			            		<td align="left">
			              			<input type="text" size="40" class="editable numeric" value="<?php echo $codigo_postal; ?>" name="codigo_postal" <? if ($id_medico) echo "disabled"?>>
			            		</td>
			         		</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center">
						<br/>
						<?php if ($id_medico) { ?>
							<input class="btn btn-default btn-xs" type="button" name="editar" value="Editar" onclick="editar_campos()" title="Edita Campos" style="width=130px"> &nbsp;&nbsp;
						    <input class="btn btn-default btn-xs" type="submit" name="guardar_editar" value="Guardar" title="Guardar" disabled style="width=130px" onclick="return control_nuevos()">&nbsp;&nbsp;
						    <input class="btn btn-default btn-xs" type="button" name="cancelar_editar" value="Cancelar" title="Cancela Edici&oacute;n" disabled style="width=130px" onclick="document.location.reload()">&nbsp;&nbsp;
						    <input class="btn btn-default btn-xs" type="submit" name="borrar" value="Borrar" style="width=130px" onclick="return confirm('Esta seguro que desea eliminar este M&eacute;dico?')" >
						<?php } else { ?>
					    	<input class="btn btn-default btn-xs" type="submit" name="guardar" value="Guardar" title="Guardar" style="width=130px" onclick="return control_nuevos()">
				 		<?php }	?>
					</td>	
				</tr>
				<?php if ($id_medico) { ?>
				<tr>
					<td>
						<br/>
						<div class="row">
						<div class="col-md-2"></div>
						<div class="col-md-8">
						<div class="panel panel-default" id="panel_efectores">
							<div class="panel-heading">
								<b><a data-toggle="collapse" data-target="#efectores" href="#panel_efectore" class="collapsed">
									Efectores
								</a></b>
							</div>
							<div id="efectores" class="panel-collapse collapse">
								<div class="panel-body text-center">
									<table class="table table-condensed table-hover">
										<thead>
											<tr>
												<th width="10%" class="small">CUIE</th>
												<th width="55%" class="small">Nombre</th>
												<th width="25%" class="small">Ciudad</th>
												<th width="10%" class="small">Acciones</th>
											</tr>
										</thead>
										<tbody>
											<?php
											if ($res_efe->recordCount() > 0) {
					      				while (!$res_efe->EOF) {
					      					$link_borrar = encode_link("medico_admin.php", array("eliminar_efector" => $res_efe->fields['id'], "id_medico" => $id_medico));
													echo '<tr>';
													echo '<td class="text-center">', $res_efe->fields['efector_cuie'], '</td>';
													echo '<td>', $res_efe->fields['efector_nombre'], '</td>';
													echo '<td>', $res_efe->fields['efector_ciudad'], '</td>';
													echo '<td class="text-center"><a data-href="',$link_borrar,'" data-toggle="modal" data-target="#confirm-delete" href="#"><span class="glyphicon glyphicon-minus-sign text-danger" aria-hidden="true" title="Eliminar efector"></span></a></td>';
													echo '<tr>';
													$res_efe->MoveNext();
												}
											}
											else {
												echo '<td colspan="4" align="center" class="danger"><strong>No hay datos</strong></td>';
											}
											?>
										</tbody>
									</table>
									<?php 
									$link_agregar = encode_link("$html_root/modulos/turnos/efectores.php", array("pagina" => "$html_root/modulos/turnos/medico_admin.php", "id_parent" => $id_medico));
									?>
									<a href="<?php echo $link_agregar; ?>" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-plus-sign text-success" aria-hidden="true"></span> Agregar Efector</a>
								</div>
							</div>
						</div>
						</div>
						<div class="col-md-2"></div>
						</div>

						<div class="row">
						<div class="col-md-2"></div>
						<div class="col-md-8">
						<div class="panel panel-default" id="panel_especialidades">
							<div class="panel-heading">
								<b><a data-toggle="collapse" data-target="#especialidades" href="#panel_especialidades" class="collapsed">
									Especialidades
								</a></b>
							</div>
							<div id="especialidades" class="panel-collapse collapse">
								<div class="panel-body text-center">
									<table class="table table-condensed table-hover">
										<thead>
											<tr>
												<th width="80%" class="small">T&iacute;tulo</th>
												<th width="10%" class="small">Especialidad</th>
												<th width="10%" class="small">Acciones</th>
											</tr>
										</thead>
										<tbody>
											<?php
											if ($res_esp->recordCount() > 0) {
							      				while (!$res_esp->EOF) {
							      					$link_borrar = encode_link("medico_admin.php", array("eliminar_especialidad" => $res_esp->fields['id'], "id_medico" => $id_medico));
													echo '<tr>';
													echo '<td>', $res_esp->fields['nom_titulo'], '</td>';
													echo '<td class="text-center">', $sino[$res_esp->fields['especialidad']], '</td>';
													echo '<td class="text-center"><a data-href="',$link_borrar,'" data-toggle="modal" data-target="#confirm-delete" href="#"><span class="glyphicon glyphicon-minus-sign text-danger" aria-hidden="true" title="Eliminar especialidad"></span></a></td>';
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
									<?php 
									$link_agregar = encode_link("especialidades.php", array("pagina" => "$html_root/modulos/turnos/medico_admin.php", "id_parent" => $id_medico));
									?>
									<a href="<?php echo $link_agregar; ?>" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-plus-sign text-success" aria-hidden="true"></span> Agregar Especialidad</a>
								</div>
							</div>
						</div>
						</div>
						<div class="col-md-2"></div>
						</div>

						<div class="row">
						<div class="col-md-2"></div>
						<div class="col-md-8">
						<div class="panel panel-default" id="panel_especialidades">
							<div class="panel-heading">
								<b><a data-toggle="collapse" data-target="#agendas" href="#panel_agendas" class="collapsed">
									Agendas
								</a></b>
							</div>
							<div id="agendas" class="panel-collapse collapse">
								<div class="panel-body text-center">
									<table class="table table-condensed table-hover">
										<thead>
											<tr>
												<th width="40%" class="small">Horario</th>
												<th width="50%" class="small">Especialidad</th>
												<th width="10%" class="small">Acciones</th>
											</tr>
										</thead>
										<tbody>
											<?php
											if ($res_agenda->recordCount() > 0) {
							      				while (!$res_agenda->EOF) {
							      					$link_borrar = encode_link("medico_admin.php", array("eliminar_agenda" => $res_agenda->fields['id'], "id_medico" => $id_medico));
							      					$dias = array();
							      					foreach ($dias_arr as $dia) {
							      						if ($res_agenda->fields[$dia] == 't') {
							      							$dias[] = ucfirst($dia);
							      						}
							      					}
													echo '<tr id="agenda-', $res_agenda->fields["id"], '">';
													echo '<td>';
													echo '<span class="agenda-dias">', join(', ', $dias), '</span><br/>';
													echo 'De <span class="agenda-hora-inicio">', date("H:i", strtotime($res_agenda->fields['hora_inicio'])), '</span> ';
													echo 'a <span class="agenda-hora-fin">', date("H:i", strtotime($res_agenda->fields['hora_fin'])), '</span><br/>';
													echo 'Turnos de <span class="agenda-duracion">', $res_agenda->fields['duracion'], '</span> minutos<br/>';
													if (!empty($res_agenda->fields['fecha_inicio']) && !empty($res_agenda->fields['fecha_fin'])) {
														echo 'Del <span class="agenda-fecha-inicio">', (empty($res_agenda->fields['fecha_inicio']) ? '' : date("d/m/Y", strtotime($res_agenda->fields['fecha_inicio']))), '</span> ';
														echo 'al <span class="agenda-fecha-fin">', (empty($res_agenda->fields['fecha_fin']) ? '' : date("d/m/Y", strtotime($res_agenda->fields['fecha_fin']))), '</span>';
													}
													echo '</td>';
													echo '<td><span class="agenda-especialidad">', $res_agenda->fields['nom_titulo'], '</span></td>';
													echo '<td class="text-center">';
													echo '<a class="btn-modificar-agenda" style="font-size: 14px;" data-href="',$link_borrar,'" data-toggle="modal" data-target="#add-edit-agenda" href="#"><span class="glyphicon glyphicon-pencil text-primary" aria-hidden="true" title="Modificar agenda"></span></a>&nbsp;&nbsp;&nbsp;&nbsp;';
													echo '<a data-href="',$link_borrar,'" style="font-size: 14px;" data-toggle="modal" data-target="#confirm-delete" href="#"><span class="glyphicon glyphicon-minus-sign text-danger" aria-hidden="true" title="Eliminar agenda"></span></a></td>';
													echo '<tr>';
													$res_agenda->MoveNext();
												}
											}
											else {
												echo '<td colspan="3" align="center" class="danger"><strong>No hay datos</strong></td>';
											}
											?>
										</tbody>
									</table>
									<a href="#add-edit-agenda" class="btn btn-default btn-sm btn-agregar-agenda" data-toggle="modal"><span class="glyphicon glyphicon-plus-sign text-success" aria-hidden="true"></span> Agregar Agenda</a>
								</div>
							</div>
						</div>
						</div>
						<div class="col-md-2"></div>
						</div>
					</td>
				</tr>
				<?php }	?>
			 	<tr>
			 		<td align="center" class="bordes">
			 			<br/>
			     		<input class="btn btn-default btn-xs" type="button" name="volver" value="Volver" onclick="document.location='medicos.php'"title="Volver al Listado" style="width=150px">
			     	</td>
			  	</tr>
			</table>
		</td>
	</tr>
</table>
</form>
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
<div class="modal fade" id="add-edit-agenda" tabindex="-1" role="dialog" aria-labelledby="addEditLabel" aria-hidden="true">
	<?php $link_agenda = encode_link("medico_admin.php", array("id_medico" => $id_medico)); ?>
    <form action="<?php echo $link_agenda; ?>" id="agenda-form" method="POST">
    <input type="hidden" name="agenda_accion" id="agenda-accion" value="agregar" />
    <input type="hidden" name="agenda_id" id="agenda-id" value="" />
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="addEditLabel"><span class="agenda-titulo">Agregar</span> Agenda</h4>
            </div>
            <div class="modal-body">
            	<div class="row">
            		<div class="col-md-12">
	            		<label for="agenda-especialidad">Especialidad</label>
	            		<select class="form-control" name="agenda_especialidad" id="agenda-especialidad">
							<option></option>
							<?php
							if ($res_esp->recordCount() > 0) {
								$res_esp->MoveFirst();
								while (!$res_esp->EOF) {
									$selected = "";
									if (!empty($id_especialidad) && $id_especialidad == $res_esp->fields['id']) {
										$selected = " selected";
									}
									echo '<option value="', $res_esp->fields['id'], '"', $selected, '>', $res_esp->fields['nom_titulo'], '</option>';
									$res_esp->MoveNext();
								}
							}
							?>
						</select>
					</div>
				</div>
				<hr/>
            	<div class="row">
					<div class="col-xs-1"></div>
					<?php foreach ($dias_arr as $dia) { ?>
					<div class="col-xs-1">
						<?php 
						$checked = "";
						if (!empty($dias_values) && $dias_values[$dia] == 't') {
							$checked = " checked ";
						}
						?>
						<label for="dia-<?php echo $dia; ?>"><?php echo ucfirst($dia); ?></label>
						<input type="checkbox" class="form-control agenda-dia" <?php echo $checked; ?> name="dia_<?php echo $dia; ?>" id="dia-<?php echo $dia; ?>" />
					</div>
					<?php } ?>
					<div class="col-xs-1"></div>
					<div class="col-xs-3">
						<label for="hora-inicio">Duraci&oacute;n del turno (en minutos)</label>
						<input type="text" class="form-control numeric" name="duracion" value="<?php echo $duracion; ?>" id="agenda-duracion" />
					</div>
            	</div>
				<hr/>
				<div class="row">
					<div class="col-xs-3">
						<label for="hora-inicio">Hora de Inicio</label>
						<input type="text" class="form-control" name="hora_inicio" value="<?php echo $hora_inicio; ?>" id="agenda-hora-inicio" />
					</div>
					<div class="col-xs-3">
						<label for="hora-fin">Hora de Fin</label>
						<input type="text" class="form-control" name="hora_fin" value="<?php echo $hora_fin; ?>" id="agenda-hora-fin" />
					</div>
					<div class="col-xs-3">
						<label for="fecha-inicio">Fecha de Inicio</label>
						<input type="text" class="form-control" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>" id="agenda-fecha-inicio" />
					</div>
					<div class="col-xs-3">
						<label for="fecha-fin">Fecha de Fin</label>
						<input type="text" class="form-control" name="fecha_fin" value="<?php echo $fecha_fin; ?>" id="agenda-fecha-fin" />
					</div>
				</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
                <input type="submit" class="btn btn-primary btn-sm" value="Aceptar" onclick="return validar_agenda();"></button>
            </div>
        </div>
    </div>
    </form>
</div>
<?php fin_pagina(); ?>