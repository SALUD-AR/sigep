<?php
require_once("../../config.php");
require_once("agenda_funciones.php");

echo $html_header;

$extras = array(
            "id_efector"  		=> "",
            "id_especialidad"	=> "",
            "id_paciente" 		=> "",
            "id_obra_social"	=> ""
          );
variables_form_busqueda("agenda_turnos", $extras);

$id_usuario = $_ses_user['id']; // el usuario logueado actualmente

$id_paciente = "";
if (!empty($parametros["nuevo_id_paciente"]) && intval($parametros["nuevo_id_paciente"]) > 0) {
	$id_paciente = intval($parametros["nuevo_id_paciente"]);
	$_ses_agenda_turnos["id_paciente"] = $id_paciente;
	phpss_svars_set("_ses_agenda_turnos", $_ses_agenda_turnos);
}
elseif (!empty($_ses_agenda_turnos["id_paciente"])) {
	$id_paciente = $_ses_agenda_turnos["id_paciente"];
}
if (!empty($id_paciente)) {
	$query = "SELECT 
						  *
						FROM
							uad.beneficiarios
						WHERE
						  id_beneficiarios = $id_paciente";
	$res_paciente = sql($query, "al obtener los datos del Paciente") or fin_pagina();
	$paciente_nombre = "";
	if ($res_paciente->recordCount()==1) {
		$paciente_nombre = $res_paciente->fields["apellido_benef"] . " " . $res_paciente->fields["nombre_benef"];
	}
}



$id_obra_social = "";
if (!empty($id_paciente) && !empty($parametros["nuevo_id_obra_social"]) && intval($parametros["nuevo_id_obra_social"]) > 0) {
	$id_obra_social = intval($parametros["nuevo_id_obra_social"]);
	sql(array(
					"DELETE FROM nacer.obras_sociales_pacientes WHERE id_paciente = $id_paciente AND id_obra_social = $id_obra_social",
					"INSERT INTO nacer.obras_sociales_pacientes (id_paciente, id_obra_social) VALUES ($id_paciente, $id_obra_social)"
					),
			"al actualizar los datos de la Obra Social"
	) or fin_pagina();
	$_ses_agenda_turnos["id_obra_social"] = $id_obra_social;
	phpss_svars_set("_ses_agenda_turnos", $_ses_agenda_turnos);
}
elseif (!empty($_ses_agenda_turnos["id_obra_social"])) {
	$id_obra_social = $_ses_agenda_turnos["id_obra_social"];
}

if (!empty($id_paciente)) {
	$query = "SELECT 
						  nacer.obras_sociales.id_obra_social,
						  nacer.obras_sociales.nom_obra_social
						FROM
							nacer.obras_sociales
					  RIGHT OUTER JOIN nacer.obras_sociales_pacientes ON (nacer.obras_sociales.id_obra_social = nacer.obras_sociales_pacientes.id_obra_social)
						WHERE
						  nacer.obras_sociales_pacientes.id_paciente = $id_paciente
						ORDER BY nacer.obras_sociales_pacientes.fecha_ultimo_uso DESC
						";
	$res_obra_social = sql($query, "al obtener los datos de la Obra Social") or fin_pagina();
}
if (!empty($_ses_agenda_turnos["id_efector"])) {
	$id_efector = $_ses_agenda_turnos["id_efector"];
}
if (!empty($_ses_agenda_turnos["id_especialidad"])) {
	$id_especialidad = $_ses_agenda_turnos["id_especialidad"];
}

$query = "SELECT 
			  nacer.efe_conv.id_efe_conv,
			  nacer.efe_conv.nombre,
			  sistema.usu_efec.cuie
			FROM
			  sistema.usu_efec
			  INNER JOIN nacer.efe_conv ON (sistema.usu_efec.cuie = nacer.efe_conv.cuie)
			WHERE
			  sistema.usu_efec.id_usuario = $id_usuario
			ORDER BY nacer.efe_conv.nombre";

$res_efectores = sql($query, "al obtener los datos de los Efectores") or fin_pagina();

if ($id_efector) {
	$query = "SELECT 
				  nacer.especialidades.id_especialidad,
				  nacer.especialidades.nom_titulo
				FROM
				  nacer.especialidades_efectores
				  LEFT OUTER JOIN nacer.especialidades ON (nacer.especialidades_efectores.id_especialidad = nacer.especialidades.id_especialidad)
				WHERE
				  nacer.especialidades_efectores.id_efector = $id_efector
				ORDER BY
				  nacer.especialidades.nom_titulo";
	
	$res_especialidades = sql($query, "al obtener los datos de las Especialidades") or fin_pagina();
}

if ($id_especialidad) {
	$query = "SELECT 
				  nacer.medicos.id_medico,
				  nacer.medicos.apellido AS apellido_medico,
				  nacer.medicos.nombre AS nombre_medico
				FROM
				  nacer.especialidades
				  LEFT OUTER JOIN nacer.especialidades_medicos ON (nacer.especialidades.id_especialidad = nacer.especialidades_medicos.id_especialidad)
				  LEFT OUTER JOIN nacer.medicos ON (nacer.especialidades_medicos.id_medico = nacer.medicos.id_medico)
				WHERE
				  nacer.especialidades.id_especialidad = $id_especialidad
				ORDER BY
				  apellido_medico,
				  nombre_medico";
	$res_medicos = sql($query, "al obtener los datos de los M&eacute;dicos") or fin_pagina();
}

?>
<br/>
<form action="agenda_turnos.php" id="form1" method="POST">
<div class="container">
<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<label>Paciente</label>
		<input type="hidden" name="id_paciente" id="id_paciente" value="<?php echo $id_paciente; ?>" />
		<div class="input-group">
			<?php $paciente_display = ($id_paciente) ? 'table-cell' : 'none'; ?>
			<span class="input-group-btn paciente-datos" style="display: <?php echo $paciente_display; ?>;">
				<button class="btn btn-default" type="button" title="Informaci&oacute;n del paciente" data-toggle="modal" data-target="#info-paciente">
					&nbsp;<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>&nbsp;
				</button>
			</span>
			<input type="text" name="nombre_paciente" class="form-control" id="nombre_paciente" placeholder="Ingrese el DNI o haga clic en el bot&oacute;n Seleccionar" value="<?php echo $paciente_nombre; ?>">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" id="seleccionar_paciente" title="Seleccionar paciente">
					&nbsp;<span class="glyphicon glyphicon-search" aria-hidden="true"> </span>&nbsp;Seleccionar
				</button>
			</span>
		</div>
	</div>
</div>
<br/>
<?php $paciente_display = ($id_paciente) ? 'block' : 'none'; ?>
<div class="row paciente-datos" style="display: <?php echo $paciente_display; ?>;">
	<div class="col-md-10 col-md-offset-1">
		<label>Obra Social</label>
		<div class="input-group">
			<select name="id_obra_social" class="form-control" id="id_obra_social">
				<option value="">Sin Obra Social</option>
				<?php
					if ($res_obra_social) {
						while (!$res_obra_social->EOF) {
							$selected = "";
							if (!empty($id_obra_social) && $id_obra_social == $res_obra_social->fields['id_obra_social']) {
								$selected = " selected";
							}
							echo '<option value="', $res_obra_social->fields['id_obra_social'], '"';
							echo $selected, '>', $res_obra_social->fields['nom_obra_social'], '</option>';
							$res_obra_social->MoveNext();
						}
					}
				?>
			</select>
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" id="seleccionar_obra_social" title="Seleccionar Obra Social">
					&nbsp;<span class="glyphicon glyphicon-search" aria-hidden="true"> </span>&nbsp;Seleccionar
				</button>
			</span>
		</div>
	</div>
</div>
<br/>
<div class="row">
	<div class="col-md-5 col-md-offset-1">
		<label for="id_efector">Efector</label>
		<select class="form-control" name="id_efector" id="id_efector">
			<option value=""></option>
			<?php
			$res_efectores->MoveFirst();
			while (!$res_efectores->EOF) {
				$selected = "";
				if (!empty($id_efector) && $id_efector == $res_efectores->fields['id_efe_conv']) {
					$selected = " selected";
				}
				echo '<option value="', $res_efectores->fields['id_efe_conv'], '"';
				echo $selected, '>', $res_efectores->fields['nombre'], '</option>';
				$res_efectores->MoveNext();
			}
			?>
		</select>
	</div>
	<div class="col-md-5">
		<label for="especialidad-nombre">Especialidad</label>
		<select class="form-control" name="id_especialidad" id="id_especialidad">
			<?php 
			if ($id_efector) {
    			echo '<option value=""></option>';
				$res_especialidades->MoveFirst();
				while (!$res_especialidades->EOF) {
					$selected = "";
					if (!empty($id_especialidad) && $id_especialidad == $res_especialidades->fields['id_especialidad']) {
						$selected = " selected";
					}
					echo '<option value="', $res_especialidades->fields['id_especialidad'], '"';
					echo $selected, '>', $res_especialidades->fields['nom_titulo'], '</option>';
					$res_especialidades->MoveNext();
				}
			} else { 
				echo '<option value="">Seleccione un Efector...</option>';
			}
			?>
		</select>
	</div>
</div>
<br/>
<br/>
<div class="row">
	<div class="col-md-3" id="columna-turnos">
		<?php 
		if ($id_especialidad) {
			cargar_medicos($id_especialidad);
		}
		?>
	</div>
	<div class="col-md-9">
		<div id='calendar'></div>
	</div>
</div>
</div>
</form>
<!-- info paciente -->
<div class="modal fade" id="info-paciente" tabindex="-1" role="dialog" aria-labelledby="info-paciente-label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="info-paciente-label">Informaci&oacute;n del paciente</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-5 text-right"><b>Apellido</b></div>
					<div class="col-md-7" id="paciente_apellido">
						<?php if (!empty($paciente_nombre)) { echo $res_paciente->fields["apellido_benef"]; } ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5 text-right"><b>Nombre</b></div>
					<div class="col-md-7" id="paciente_nombre">
						<?php if (!empty($paciente_nombre)) { echo $res_paciente->fields["nombre_benef"]; } ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5 text-right"><b>Documento</b></div>
					<div class="col-md-7" id="paciente_documento">
						<?php if (!empty($paciente_nombre)) { echo $res_paciente->fields["tipo_documento"], ' ', $res_paciente->fields["numero_doc"]; } ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5 text-right"><b>Sexo</b></div>
					<div class="col-md-7" id="paciente_sexo">
						<?php if (!empty($paciente_nombre)) { echo (trim($res_paciente->fields["sexo"]) == 'M') ? 'Masculino' : 'Femenino'; } ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5 text-right"><b>Fecha Nacimiento</b></div>
					<div class="col-md-7" id="paciente_fechanac">
						<?php if (!empty($paciente_nombre)) { echo Fecha($res_paciente->fields["fecha_nacimiento_benef"]); } ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5 text-right"><b>Clave Beneficiario</b></div>
					<div class="col-md-7" id="paciente_clavebeneficiario">
						<?php if (!empty($paciente_nombre)) { echo $res_paciente->fields["clave_beneficiario"]; } ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5 text-right"><b>CUIE Efector Asignado</b></div>
					<div class="col-md-7" id="paciente_cuieefector">
						<?php if (!empty($paciente_nombre)) { echo $res_paciente->fields["cuie_ea"]; } ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5 text-right"><b>CUIE Lugar de atenci&oacute;n habitual</b></div>
					<div class="col-md-7" id="paciente_cuielugaratencion">
						<?php if (!empty($paciente_nombre)) { echo $res_paciente->fields["cuie_ah"]; } ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5 text-right"><b>Activo</b></div>
					<div class="col-md-7" id="paciente_activo">
						<?php if (!empty($paciente_nombre)) { echo $sino[trim($res_paciente->fields["activo"])]; } ?>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<!-- Detalle del turno -->
<div id="detalle_turno" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
          		<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
              <h4 id="detalle_titulo" class="modal-title"></h4>
            </div>
            <div id="detalle_datos" class="modal-body"></div>
            <div class="modal-footer">
            	<input type="hidden" name="detalle_id_turno" id="detalle_id_turno" value="" />
              <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-cancelar">Cancelar turno</button>
              <button type="button" class="btn btn-success" data-dismiss="modal" id="btn-estado">Presente</button>
              <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	var agenda_dias = '';
	var agenda_horas = '';
	var agenda_dias_sem = [ "Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab", "Dom" ];

	var filtro_eventos = function(start, end, timezone, callback) {
		var events = [];
		var test_date, loop, pos;
		for (loop = start.unix();
		loop <= end.unix();
		loop = loop + (24 * 60 * 60)) {
			test_date = moment.unix(loop);
			
			pos = agenda_dias_sem[test_date.isoWeekday()];
			if ($.inArray(pos, agenda_dias) != -1) {
				events.push({
					title: '',
					start: test_date.format('YYYY-MM-DD ') + agenda_horas[0],
					end: test_date.format('YYYY-MM-DD ') + agenda_horas[1],
					rendering: 'background'
				});
			}
		}
		// console.log(events);
		callback( events );
	}
	function validar_fecha_evento(fecha, dias) {
		pos = agenda_dias_sem[fecha.isoWeekday()];
		if ($.inArray(pos, dias) != -1) {
			return true;
		}
		return false;
	}
	var events_url = '<?php echo encode_link("agenda_datos.php", array("accion" => "agenda_events")); ?>';

	var get_event_obj = function(start, end, timezone, callback) {
    $.ajax({
      url: events_url,
      type: 'POST',
      data: {
      	start: start.format("YYYY-MM-DD"),
      	end: end.format("YYYY-MM-DD"),
      	agendas_ids: $('#agendas_ids').val()
      },
      dataType: 'json',
      success: function(datos) {
          var events = [];
          $.each( datos, function( key, val ) {
            events.push({
              id: val.id,
              title: val.title,
              start: val.start,
              end: val.end,
              color: get_event_color(val.id_agenda),
              id_agenda: val.id_agenda,
              estado: val.estado,
              efector: val.efector,
              especialidad: val.especialidad,
              medico: val.medico,
              obra_social: val.obra_social
            });
				  });
          callback(events);
      }
	  });
  }

	function inicializar_eventos_externos() {
		$('#columna-turnos .external-events div.external-event').each(function() {
			// create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
			// it doesn't need to have a start or end
			var eventObject = {
				title: $.trim($(this).text()), // use the element's text as the event title
				start: $(this).data('start'),
				end: $(this).data('end'),
				// duration: $(this).data('duration'),
				// shareable: $(this).data('shareable'),
				id: $(this).data('id')
			};
			  
			// store the Event Object in the DOM element so we can get to it later
			$(this).data('eventObject', eventObject);

			// make the event draggable using jQuery UI
			$(this).draggable({
				cursor: "move",
				zIndex: 999,
				revert: true,      // will cause the event to go back to its
				revertDuration: 300,  //  original position after the drag
				start: function() {
					agenda_dias = $(this).prevAll('.agenda-dias').text().split(', ');
					// console.log(agenda_dias);
					agenda_horas = $(this).prevAll('.agenda-horas').text().split(' - ');
					$('#calendar').fullCalendar( 'addEventSource', { events: filtro_eventos } );
					// console.log(agenda_horas);
					// console.log($(this).prevAll('.agenda-dias').text());
				},
				stop: function () {
					$('#calendar').fullCalendar( 'removeEventSource', { events: filtro_eventos } );
				}
			});
		});
		$('#calendar').fullCalendar('removeEventSource', { events: get_event_obj });
		$('#calendar').fullCalendar('addEventSource', { events: get_event_obj });
	}
	function cargar_medicos(id_especialidad) {
		$.ajax({
			url: '<?php echo encode_link("agenda_datos.php", array("accion" => "cargar_medicos")); ?>',
			type: 'POST',
			data: 'id_especialidad='+id_especialidad,
			success: function(datos) {
				$('#columna-turnos').html(datos);
				inicializar_eventos_externos();
			},
			error: function (xhr, ajaxOptions, thrownError) {
				console.log(xhr.status + " "+ thrownError);
			}
		});
	}
	function cargar_paciente() {
		var dni_paciente = $("#nombre_paciente").val();
		$.post('<?php echo encode_link("agenda_datos.php", array("accion" => "datos_paciente")); ?>',
			{ dni:  dni_paciente },
			function(data, textStatus, jqXHR) {
				if (data.length <= 0) {
					BootstrapDialog.alert('No se encontr&oacute; un paciente con el n&uacute;mero de dni "'+dni_paciente+'"!');
					$("#nombre_paciente").val("");
					$("#id_paciente").val("");
					$("#id_obra_social").val("");
					$(".paciente-datos").hide();
				}
				else if (data.length == 1) {
					$("#id_paciente").val(data[0].id);
					$("#nombre_paciente").val(data[0].apellido+' '+data[0].nombre);
					$("#paciente_apellido").html(data[0].apellido);
					$("#paciente_nombre").html(data[0].nombre);
					$("#paciente_documento").html(data[0].documento);
					$("#paciente_sexo").html(data[0].sexo);
					$("#paciente_fechanac").html(data[0].fechanac);
					$("#paciente_clavebeneficiario").html(data[0].clavebeneficiario);
					$("#paciente_cuieefector").html(data[0].cuieefector);
					$("#paciente_cuielugaratencion").html(data[0].cuielugaratencion);
					$("#paciente_activo").html(data[0].activo);
					$(".paciente-datos").show();
					cargar_obra_social();
				}
				else {
					BootstrapDialog.alert('Se encontr&oacute; m&aacute;s de un paciente con el n&uacute;mero de dni "'+dni_paciente+'"!<br/>Utilice el bot&oacute;n de Seleccionar paciente para elegirlo');
					$("#nombre_paciente").val("");
					$("#id_paciente").val("");
					$("#id_obra_social").val("");
					$(".paciente-datos").hide();
					// $.each(data, function(k, v) {
					// 	console.log(k+": "+v.id+" "+v.apellido+" "+v.nombre);
					// });
				}
			},
			"json"
		).fail(function(jqXHR, textStatus, errorThrown) {
			BootstrapDialog.alert('Ocurrio un error al obtener los datos del paciente!');
		});
	}
	function cargar_obra_social() {
		var id_paciente = $("#id_paciente").val();
		$.post('<?php echo encode_link("agenda_datos.php", array("accion" => "datos_obra_social")); ?>',
			{ id_paciente:  id_paciente },
			function(opciones) {
				$('#id_obra_social').html(opciones);
			}
		).fail(function() {
			BootstrapDialog.alert('Ocurrio un error al obtener los datos del paciente!');
		});
	}
	// function check_overlap(event){
 //  	var eventos = $('#calendar').fullCalendar('clientEvents');
 //  	for(i in eventos){
	// 		// console.log(eventos[i]._id+' != '+event.id);
 //    	if(eventos[i]._id != event.id && eventos[i].rendering != 'background') {
	// 			// console.log(eventos[i].start.format()+' >= '+event.end.format()+' || \n'+eventos[i].end.format()+' <= '+event.start.format());
 //      	if(!(eventos[i].start.unix() >= event.end.unix() || eventos[i].end.unix() <= event.start.unix())) {
 //          return true;
 //        }
 //      }
 //    }
 //    return false;
	// }
	function check_duplicado(event){
  	var eventos = $('#calendar').fullCalendar('clientEvents');
  	for(i in eventos){
			// console.log(eventos[i].title+' == '+event.title);
    	if(eventos[i].title == event.title && eventos[i].rendering != 'background') {
      	if(!(eventos[i].start.unix() >= event.end.unix() || eventos[i].end.unix() <= event.start.unix())) {
          return true;
        }
      }
    }
    return false;
	}
	function get_event_color(id_agenda) {
		var color = $('#agenda-'+id_agenda+' .external-event:first').css('background-color');
		return (color) ? color : '#3a87ad';
	}

	$(document).ready(function() {
		<?php 
		if ($id_especialidad) {
			echo 'inicializar_eventos_externos();';
		}
		?>
		// permitir solo el ingreso de numeros en los campos con la clase "numeric"
		$('input.numeric').keyup(function() {     
  			this.value = this.value.replace(/[^0-9]/g,'');
		});
		$("#form1").submit(function(event) {
			if (isDNI($("#nombre_paciente").val())) {
				cargar_paciente();
			}
			event.preventDefault();
		});
		$('#seleccionar_paciente').on('click', function() {
			if ($("#nombre_paciente").val() == "" || !isDNI($("#nombre_paciente").val())) {
				document.location = '<?php echo encode_link("pacientes.php", array("pagina"=>"$html_root/modulos/turnos/agenda_turnos.php")); ?>';
			}
			else if (isDNI($("#nombre_paciente").val())) {
					cargar_paciente();
			}
		});
		$('#seleccionar_obra_social').on('click', function() {
				document.location = '<?php echo encode_link("obras_sociales.php", array("pagina"=>"$html_root/modulos/turnos/agenda_turnos.php")); ?>';
		});
		$('#btn-cancelar').on('click', function() {
			$.ajax({
				url: '<?php echo encode_link("agenda_datos.php", array("accion" => "cancelar_turno")); ?>',
				type: 'POST',
				data: 'id_turno='+$('#detalle_id_turno').val(),
				success: function(resultado) {
					if (resultado == 1) {
						BootstrapDialog.alert("El turno fu&eacute; cancelado correctamente");
						$('#calendar').fullCalendar( 'refetchEvents' );
					}
					else {
						BootstrapDialog.alert("El turno no se pudo cancelar");
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(xhr.status + " "+ thrownError);
				}
			});
		});
		$('#btn-estado').on('click', function() {
			$.ajax({
				url: '<?php echo encode_link("agenda_datos.php", array("accion" => "turno_presente")); ?>',
				type: 'POST',
				data: 'id_turno='+$('#detalle_id_turno').val(),
				success: function(resultado) {
					if (resultado == 1) {
						// BootstrapDialog.alert("El turno fu&eacute; cancelado correctamente");
						$('#calendar').fullCalendar( 'refetchEvents' );
					}
					// else {
						// BootstrapDialog.alert("El turno fu&eacute; cancelado correctamente");
					// }
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(xhr.status + " "+ thrownError);
				}
			});
		});
		$('#id_efector').change(function(e) {
			var selectvalue = $(this).val();

			$('#id_especialidad').html('<option value="">Cargando...</option>');
			$('#columna-turnos').html('');
			$('#calendar').fullCalendar('removeEventSource', { events: get_event_obj });

			if (selectvalue == '') {
				$('#id_especialidad').html('<option value="">Seleccione un Efector...</option>');
			} 

			$.ajax({
				url: '<?php echo encode_link("agenda_datos.php", array("accion" => "cargar_especialidades")); ?>',
				type: 'POST',
				data: 'id_efector='+selectvalue,
				success: function(opciones) {
					if (selectvalue != '') {
						$('#id_especialidad').html(opciones);
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(xhr.status + " "+ thrownError);
				}
			});
		});

		$('#id_especialidad').change(function(e) {
			$('#columna-turnos').html('');
			var selectvalue = $(this).val();
			cargar_medicos(selectvalue);
		});

		$('#id_obra_social').change(function(e) {
			$.post('<?php echo encode_link("agenda_datos.php", array("accion" => "guardar_obra_social")); ?>',
				{ id_obra_social:  $(this).val() },
				function() {
					// $('#id_obra_social').html(opciones);
				}
			).fail(function() {
				BootstrapDialog.alert('Ocurrio un error al obtener los datos del paciente!');
			});
		});

		/* initialize the calendar */
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today month,agendaWeek,agendaDay',
				center: 'title',
				right: ''
			},
			lang: 'es',
			allDaySlot: false,
			eventSources: [
			{
				events: get_event_obj
			}
		  ],
			titleRangeSeparator: ' al ',
			views: {
				week: {
					titleFormat: "LL"
				}
			},
			lazyFetching: false,
			axisFormat: 'HH:mm',
			timeFormat: 'HH:mm',
			slotDuration: '00:15:00',
			scrollTime: '07:00:00',
    	// maxTime: '20:00:00',
			defaultView: 'agendaWeek',
			displayEventEnd: false,
  		editable: false,
			aspectRatio: 1.8,
			eventDurationEditable: false,
			// eventOverlap: false,
			// slotEventOverlap: false,
  		droppable: true, // this allows things to be dropped onto the calendar !!!
  		eventRender: function( event, element, view ) {
  			if(view.name == 'agendaDay') {
  				element.find('.fc-time').append(' | ' + event.especialidad);
  			} else {
	  			if (event.estado == 'presente') {
	  				element.find('.fc-time').addClass('turno-presente');
  				}
  			}
  		},
  		eventDragStart: function( event, jsEvent, ui, view ) {
				agenda_dias = event.dias;
				agenda_horas = event.horas;
				$('#calendar').fullCalendar( 'addEventSource', { events: filtro_eventos } );
  		},
			eventClick: function(event, jsEvent, view) {
        $('#detalle_titulo').html('Detalle del turno');
        // var msg = 'Hora: '+event.start.format('HH:mm');
        //alert(event.id_agenda);ç
        // console.log(event);
				var msg = '<div class="row"><div class="col-md-3 text-right"><b>Fecha:</b></div><div class="col-md-9">'+event.start.format('DD/MM/YYYY')+'</div></div>';
				msg += '<div class="row"><div class="col-md-3 text-right"><b>Hora:</b></div><div class="col-md-9">'+event.start.format('HH:mm')+'</div></div>';
				msg += '<div class="row"><div class="col-md-3 text-right"><b>Efector:</b></div><div class="col-md-9">'+event.efector+'</div></div>';
				msg += '<div class="row"><div class="col-md-3 text-right"><b>Especialidad:</b></div><div class="col-md-9">'+event.especialidad+'</div></div>';
				// msg += '<div class="row"><div class="col-md-3 text-right"><b>M&eacute;dico:</b></div><div class="col-md-9">'+$('#agenda-'+event.id_agenda).closest('.panel').find('.panel-heading h4 a').text()+'</div></div>';
				msg += '<div class="row"><div class="col-md-3 text-right"><b>M&eacute;dico:</b></div><div class="col-md-9">'+event.medico+'</div></div>';
				msg += '<div class="row"><div class="col-md-3 text-right"><b>Paciente:</b></div><div class="col-md-9">'+event.title+'</div></div>';
				msg += '<div class="row"><div class="col-md-3 text-right"><b>Obra Social:</b></div><div class="col-md-9">'+event.obra_social+'</div></div>';
        $('#detalle_id_turno').val(event.id);
        $('#detalle_datos').html(msg);
        // $('#eventUrl').attr('href',event.url);
        $('#detalle_turno').modal();
      },
      eventDrop: function(event, delta, revertFunc, jsEvent, ui, view) {
				$('#calendar').fullCalendar( 'removeEventSource', { events: filtro_eventos } );
				if (!validar_fecha_evento(event.start, event.dias)) {
					revertFunc();
				}
				event.start.stripTime();
				event.start.time(event.start_orig.format('HH:mm'));
				event.end.stripTime();
				event.end.time(event.end_orig.format('HH:mm'));
				// if ((delta % (60*60*24)) != 0) {
				// 	revertFunc();
				// } 
			},
			drop: function(date, jsEvent, ui) { // this function is called when something is dropped
				if (!validar_fecha_evento(date, agenda_dias)) {
					$.notify("El turno no se puede asignar en el día de la semana seleccionado", "error");
					return false;
				}

				if ($('#id_paciente').val() == '') {
					BootstrapDialog.alert("Falta seleccionar el paciente!");
					return false;
				}

				// retrieve the dropped element's stored Event Object
				var originalEventObject = $(this).data('eventObject');
				
				// we need to copy it, so that multiple events don't have a reference to the same object
				var copiedEventObject = $.extend({}, originalEventObject);
				//This object needs to be extended to allow for data-shareable component that will allow date and time information this is not the same as .data look up this online
				
				// assign it the date that was reported
				//copiedEventObject.start = date;
				var sdate = $.fullCalendar.moment(date.format());  // Create a clone of the dropped date.
				sdate.stripTime();        // The time should already be stripped but lets do a sanity check.
				sdate.time(originalEventObject.start);   // Set a default start time.
				copiedEventObject.start = sdate;
				copiedEventObject.start_orig = sdate;
				//alert(sdate);

				var edate = $.fullCalendar.moment(date.format());  // Create a clone.
				edate.stripTime();        // Sanity check.
				edate.time(originalEventObject.end);   // Set a default end time.
				copiedEventObject.end = edate;

				copiedEventObject.title = $('#nombre_paciente').val();

				// if (check_overlap(copiedEventObject)) {
				// 	return false;
				// }

				if (check_duplicado(copiedEventObject)) {
					$.notify("Turno duplicado", "error");
					return false;
				}

				edate.subtract(1, 'second'); // prevent overlapping
				// var rand_id = Math.floor((Math.random() * 100) + 1);
				// copiedEventObject.id = copiedEventObject.id + '-' + rand_id;
				copiedEventObject.end = edate;
				copiedEventObject.end_orig = edate;
				copiedEventObject.allDay = false;

				copiedEventObject.dias = agenda_dias;
				copiedEventObject.horas = agenda_horas;

				var agenda_id = copiedEventObject.id.split("-"); 

				copiedEventObject.color = get_event_color(agenda_id[0]);

				copiedEventObject.efector = $('#id_efector option:selected').text();
				copiedEventObject.especialidad = $('#id_especialidad option:selected').text();
				copiedEventObject.obra_social = $('#id_obra_social option:selected').text();
				copiedEventObject.medico = $('#agenda-'+agenda_id[0]).closest('.panel').find('.panel-heading h4 a').text();

				// console.log(copiedEventObject);

				var msg = '<h4>Se va a crear el turno con los siguientes datos:</h4>';
				msg += '<div class="row"><div class="col-md-3 text-right"><b>Fecha:</b></div><div class="col-md-9">'+copiedEventObject.start.format('DD/MM/YYYY')+'</div></div>';
				msg += '<div class="row"><div class="col-md-3 text-right"><b>Hora:</b></div><div class="col-md-9">'+copiedEventObject.start.format('HH:mm')+'</div></div>';
				msg += '<div class="row"><div class="col-md-3 text-right"><b>Paciente:</b></div><div class="col-md-9">'+$('#nombre_paciente').val()+'</div></div>';
				msg += '<div class="row"><div class="col-md-3 text-right"><b>Obra Social:</b></div><div class="col-md-9">'+$('#id_obra_social option:selected').text()+'</div></div>';
				msg += '<div class="row"><div class="col-md-3 text-right"><b>Efector:</b></div><div class="col-md-9">'+$('#id_efector option:selected').text()+'</div></div>';
				msg += '<div class="row"><div class="col-md-3 text-right"><b>Especialidad:</b></div><div class="col-md-9">'+$('#id_especialidad option:selected').text()+'</div></div>';

				BootstrapDialog.confirm(msg, function(result){
					if(result) {
						$.ajax({
							url: '<?php echo encode_link("agenda_datos.php", array("accion" => "agenda_add")); ?>',
							type: 'POST',
							data: {
								id_agenda: agenda_id[0],
								id_paciente: $('#id_paciente').val(),
								id_obra_social: $('#id_obra_social').val(),
								id_efector: $('#id_efector').val(),
								inicio: sdate.format(),
								fin: edate.format()
							},
							async: false,
							cache: false,
							timeout: 60000,
							error: function(){
								BootstrapDialog.alert('Ocurrio un error al agregar el turno!');
								return false;
							},
							success: function(resultado){ 
								if (resultado > 0){
									copiedEventObject.id = resultado;
									$('#calendar').fullCalendar('renderEvent', copiedEventObject, false);
								} else {
									BootstrapDialog.alert('No se pudieron guardar los datos del turno!');
									return false;
								}
							}
						});
						
					} else {
						return false;
					}
				});
  		}
		});
  	});
</script>
<?php
fin_pagina(); 
?>