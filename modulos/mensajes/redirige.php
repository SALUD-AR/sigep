<?php
	require_once("../../config.php");
	
	switch ($_POST['Submit']) {
		case "Cancelar":{ 
			header('location: ./mensajes.php');
			break;
			}
		case "Reenviar": {
			include_once "./guardar_mens.php";
			break;
			}
		default:{
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
			alert("El mensaje está en blanco.");
			return false;
		}
		return true;
	}
</script>
<div class="container">
	<form name="form" method="post" action="redirige.php">
		<legend>Reenviar Mensaje</legend>
		
		<?php
			$id_mensaje=$_POST['radio']; 
			$ssql_busca="select numero, nro_orden,usuario_destino,comentario,fecha_vencimiento from mensajes where id_mensaje=".$id_mensaje;
			db_tipo_res('a');
			$result=$db->Execute($ssql_busca) or die($db->ErrorMsg());
		?>
		
		<input type="hidden" name="id_m" value="<? echo $id_mensaje;?>">
		<input type="hidden" name="tipo_m" value=0>
		<input type="hidden" name="tipo2" value='MRU'>
		<input type="hidden" name="nro_ord" value="<?php echo $result->fields['nro_orden'] +1;?>" >
		
		<div class="row">
			<div class="col-xs-3">
				<label>Para:</label>
			
				<select class="form-control" name="para">
					<option value='?'></option>
					<?php
						$ssql1="select nombre from usuarios where nombre!='root';";
						db_tipo_res('a');
						$result1=$db->Execute($ssql1) or die($db->ErrorMsg());
						
						while(!$result1->EOF){
					?>
							<option> 
							<? echo $result1->fields['nombre'];?>
							</option>
					<?php 
							$result1->MoveNext();
						}// end while
					?>
					<option selected> 
						<? 
							echo $result->fields['usuario_destino']; 
						?>
					</option>
					<option>Todos</option>
				</select>
			</div>
			<div class="col-xs-3">
				<label>Fecha de Vencimieto:</label>
				<? 
					$fech=substr($result->fields['fecha_vencimiento'],0,10);
					$hora=substr($result->fields['fecha_vencimiento'],11,16);
				?>
				
				<input class="form-control date-input" name="venc" value="<?php echo fecha($fech);?>" type=text >
				<input type="hidden" name="hora" value="<? echo $hora;?>">
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="col-xs-12">
				<label>Mensaje:</label>
				<textarea class="form-control" name="nota"><?php echo $result->fields['comentario']; ?></textarea>
				<input type="hidden" name="anterior" value="<?php echo $result->fields['comentario']; ?>">
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="col-xs-3"></div>
			<div class="col-xs-3">
				<input class="btn btn-primary form-control" type="submit" name="Submit" value="Reenviar" onClick="return comprueba();">
			</div>
			<div class="col-xs-3">
				<input class="btn btn-default form-control" type="submit" name="Submit" value="Cancelar">
			</div>
			<div class="col-xs-3"></div>
		</div>
	</form>
	<?php
			}// end default
		} // end switch
	?>
	</div>
<?php echo $html_footer?>