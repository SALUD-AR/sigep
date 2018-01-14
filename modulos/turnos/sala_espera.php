<?php
require_once("../../config.php");
require_once("agenda_funciones.php");

echo $html_header;

$extras = array(
            "id_efector"      => "",
            "id_especialidad" => ""
          );
variables_form_busqueda("sala_espera", $extras);

$id_usuario = $_ses_user['id']; // el usuario logueado actualmente

if (!empty($_ses_sala_espera["id_efector"])) {
  $id_efector = $_ses_sala_espera["id_efector"];
}
if (!empty($_ses_sala_espera["id_especialidad"])) {
  $id_especialidad = $_ses_sala_espera["id_especialidad"];
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

?>
<br/>
<form action="sala_espera.php" id="form1" method="POST">
<div class="container">
<br/>
<div class="row">
  <div class="col-md-5 col-md-offset-1">
    <label for="id_efector">Efector</label>
    <select class="form-control" name="id_efector" id="id_efector">
      <option value="">Todos</option>
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
          echo '<option value="">Todas</option>';
        $res_especialidades->MoveFirst();
        $color_index = 0;
        while (!$res_especialidades->EOF) {
          $selected = "";
          if (!empty($id_especialidad) && $id_especialidad == $res_especialidades->fields['id_especialidad']) {
            $selected = " selected";
          }
          echo '<option value="', $res_especialidades->fields['id_especialidad'], '" ';
          echo 'data-color="', $agenda_colores[$color_index], '"';
          echo $selected, '>', $res_especialidades->fields['nom_titulo'], '</option>';
          $color_index++;
          if ($color_index == count($agenda_colores)) {
            $color_index = 0;
          }
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
<div class="panel panel-primary">
  <div class="panel-heading"><h3 class="panel-title">Referecia de Especialidades</h3></div>
  <div class="panel-body" id="panel-referencia">
  </div>
</div>
<br/>
<div class="row">
  <div class="col-md-12">
    <div id='calendar'></div>
  </div>
</div>
</div>
</form>
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
              <button type="button" class="btn btn-success" id="btn-diagnosticar" data-dismiss="modal" onclick="$('#frame2').attr('src', '<?php echo $html_root; ?>/modulos/turnos/cargar_diagnostico.php?id_turno='+$('#detalle_id_turno').val());">Diagnosticar</button>
              <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
  // window.parent.renderModal('#detalle_turno', $('#detalle_turno').html());
  var events_url = '<?php echo encode_link("sala_espera_datos.php", array("accion" => "agenda_events")); ?>';

  var get_event_color = function(especialidad) {
    var option = $('#id_especialidad option').filter(function () { return $(this).html() == especialidad; });
    if (option) {
      return option.data('color');
    }
    return '#3a87ad';
  }
  var get_event_obj = function(start, end, timezone, callback) {
    $.ajax({
      url: events_url,
      type: 'POST',
      data: {
        start: start.format("YYYY-MM-DD"),
        end: end.format("YYYY-MM-DD"),
        id_efector: $('#id_efector').val(),
        id_especialidad: $('#id_especialidad').val()
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
              color: get_event_color(val.especialidad),
              id_agenda: val.id_agenda,
              efector: val.efector,
              especialidad: val.especialidad,
              medico: val.medico,
              obra_social: val.obra_social,
              estado: val.estado
            });
          });
          // console.log(events);
          callback(events);
      }
    });
  }

  function check_overlap(event){
    var array = $('#calendar').fullCalendar('clientEvents');
    for(i in array){
      if(array[i]._id != event._id && array[i].rendering != 'background') {
        if(!(array[i].start.unix() >= event.end.unix() || array[i].end.unix() <= event.start.unix())) {
          // alert(array[i].start.format()+' >= '+event.end.format()+' || \n'+array[i].end.format()+' <= '+event.start.format());
          return true;
        }
      }
    }
    return false;
  }

  function cargar_referencias() {
    $('#panel-referencia').html('');
    var opciones = $('#id_especialidad > option');
    if (opciones.length > 1) {
      $('#panel-referencia').parent().show();
      opciones.each(function() {
        if ($(this).val() != '') {
          $('#panel-referencia').append('<div class="col-md-4">\
                                          <div class="referencia-color" style="background-color: '+$(this).data('color')+'"></div>\
                                          <div class="referencia-texto">&nbsp;'+$(this).text()+'</div>\
                                        </div>');
        }
      });
    }
    else {
      $('#panel-referencia').parent().hide();
    }
  }

  $(document).ready(function() {
    cargar_referencias();
    // permitir solo el ingreso de numeros en los campos con la clase "numeric"
    $('input.numeric').keyup(function() {     
        this.value = this.value.replace(/[^0-9]/g,'');
    });
    $('#btn-diagnostico').on('click', function() {
      document.location = 'cargar_diagnostico.php?id_turno='+$('#detalle_id_turno').val();
    });
    $('#id_efector').change(function(e) {
      var selectvalue = $(this).val();

      if (selectvalue == '') {
        $('#id_especialidad').html('<option value="">Seleccione un Efector...</option>');
        cargar_referencias();
        $('#calendar').fullCalendar( 'refetchEvents' );
        // resetear los valores de la sesion
        $.ajax({
          url: '<?php echo encode_link("sala_espera_datos.php", array("accion" => "cargar_especialidades")); ?>',
          type: 'POST',
          data: 'id_efector=0'
        });
      }
      else{
        $('#id_especialidad').html('<option value="">Cargando...</option>');
        $.ajax({
          url: '<?php echo encode_link("sala_espera_datos.php", array("accion" => "cargar_especialidades")); ?>',
          type: 'POST',
          data: 'id_efector='+selectvalue,
          success: function(opciones) {
            $('#id_especialidad').html(opciones);
            cargar_referencias();
            $('#calendar').fullCalendar( 'refetchEvents' );
          },
          error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.status + " "+ thrownError);
          }
        });
      }
    });

    $('#id_especialidad').change(function(e) {
      $('#calendar').fullCalendar( 'refetchEvents' );
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
      defaultView: 'agendaDay',
      displayEventEnd: false,
      editable: false,
      aspectRatio: 2.0,
      eventDurationEditable: false,
      // eventOverlap: false,
      // slotEventOverlap: false,
      droppable: true, // this allows things to be dropped onto the calendar !!!
      eventRender: function( event, element, view ) {
        if (view.name == 'agendaDay') {
          element.find('.fc-time').append(' | ' + event.efector);
          element.find('.fc-time').append(' | ' + event.especialidad);
          element.find('.fc-time').append(' | ' + event.medico);
        }
        if (event.estado == 'completo') {
          element.find('.fc-title').prepend('<span style="font-size: 14px; padding: 3px;" class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span>&nbsp;');
        }
      },
      eventDragStart: function( event, jsEvent, ui, view ) {
        agenda_dias = event.dias;
        agenda_horas = event.horas;
        $('#calendar').fullCalendar( 'addEventSource', { events: filtro_eventos } );
      },
      eventClick: function(event, jsEvent, view) {
        $('#detalle_titulo').html('Detalle del turno');
        // console.log(event);
        var msg = '<div class="row"><div class="col-md-3 text-right"><b>Fecha:</b></div><div class="col-md-9">'+event.start.format('DD/MM/YYYY')+'</div></div>';
        msg += '<div class="row"><div class="col-md-3 text-right"><b>Hora:</b></div><div class="col-md-9">'+event.start.format('HH:mm')+'</div></div>';
        msg += '<div class="row"><div class="col-md-3 text-right"><b>Efector:</b></div><div class="col-md-9">'+event.efector+'</div></div>';
        msg += '<div class="row"><div class="col-md-3 text-right"><b>Especialidad:</b></div><div class="col-md-9">'+event.especialidad+'</div></div>';
        msg += '<div class="row"><div class="col-md-3 text-right"><b>M&eacute;dico:</b></div><div class="col-md-9">'+event.medico+'</div></div>';
        msg += '<div class="row"><div class="col-md-3 text-right"><b>Paciente:</b></div><div class="col-md-9">'+event.title+'</div></div>';
        msg += '<div class="row"><div class="col-md-3 text-right"><b>Obra Social:</b></div><div class="col-md-9">'+event.obra_social+'</div></div>';
        $('#detalle_id_turno').val(event.id);
        $('#detalle_datos').html(msg);
        if (event.estado == 'completo') {
          $('#btn-diagnosticar').hide();
        } else {
          $('#btn-diagnosticar').show();
        }
        modal_top('#detalle_turno');
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
        //  revertFunc();
        // } 
      },
    });
    });
</script>
<?php
fin_pagina(); 
?>