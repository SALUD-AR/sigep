<?php
	require_once("../../config.php");
	
	switch ($_POST['bot']){
		case "Cancelar": { 
			header('location: ./mensajes.php');
			break;
			}
		case "Enviar mensaje": {
			require "../mensajes/guardar_mens.php";
			break;
			}
		default: { 
?>

<?php echo $html_header?>

<script type="text/javascript">
		$(document).ready(function () {
			$('.date-input').datetimepicker({
				language: 'es',
				format: 'DD/MM/YYYY',
				pickTime: false
		    });
		});
		
		function comprueba() {
			if(document.form.venc.value=='') {
				alert("Debe seleccionar fecha de vencimiento.");
				return false;
			}
			if(document.form.para.value=='?') {
				alert("Debe seleccionar usuario.");
				return false;
			}
			if(document.form.nota.value=='') {
				alert("El mansaje está en blanco.");
				return false;
			}
			return true;
		}
</script>
<div class="container">
	<form name="form" action="nuevo_mens.php" method="post">
		<legend>Nuevo Mensaje</legend>
		<input type="hidden" name="tipo_m" value="1">
		
		<div class="row">
			<div class="col-xs-4">
				<label>Para:</label>
				<select class="form-control" name="para">
					<option value='?'>Seleccione</option>
					<?php
						$ssql1="select login, nombre, apellido from usuarios where nombre!='root' order by apellido;";
						db_tipo_res('a');
						$result1=$db->Execute($ssql1) or die($db->ErrorMsg());
						while(!$result1->EOF){
					?>
							<option value='<?=$result1->fields['login']?>'>
					<?php 
							echo $result1->fields['apellido']. ' '.$result1->fields['nombre'];
					?>
							</option>
					<?php 
						$result1->MoveNext();
						}//while
						?>
					<option value='Todos'>Todos</option>
				</select>
			</div>
			<div class="col-xs-3">
				<label>Fecha&nbsp;de&nbsp;Vencimiento:</label>
				<input class="form-control date-input" name="venc" type=text >
				<input type="hidden" name="hora" value="00:00">
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="col-xs-12">
				<label>Mensaje:</label>
				<textarea class="form-control" name="nota"></textarea>
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="col-xs-3"></div>
			<div class="col-xs-3">
				<input class="btn btn-primary form-control" type="submit" name="bot" value="Enviar mensaje" onClick="return comprueba();">
			</div>
			<div class="col-xs-3">
				<input class="btn btn-default form-control" type="submit" name="bot" value="Cancelar">
			</div>
			<div class="col-xs-3"></div>
		</div>
	</form>
</div>

<?php echo $html_footer; ?>

<?php
 }//default
} //fin switch
?>
