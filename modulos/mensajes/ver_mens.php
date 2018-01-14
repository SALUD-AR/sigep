<?php
	$id_mensaje=$_GET['id_mensaje'];
	require_once("../../config.php");

	switch ($_POST['Submit']){
		case "Volver a la Bandeja de Entrada": { 
			if ($donde==1)  header('location: ./mensajeria.php');
			if ($donde==0)  header('location: ./mensajes.php');
			exit;
			break;
		}
		default:{
			echo $html_header;
?>
<div class="container">
<form name="form" method="post" action="ver_mens.php">
	<div class="row">
		<div class="col-md-12">
			<?php
				db_tipo_res('a');
				
				$actualizar_mensaje="UPDATE mensajes set recibido='t' where id_mensaje=".$id_mensaje;
				$db->Execute($actualizar_mensaje) or die($db->ErrorMsg());
				
				$ssql_busca="select * from mensajes where id_mensaje=".$id_mensaje;
				$result=$db->Execute($ssql_busca) or die($db->ErrorMsg());
			?>
			
			<input type="hidden" name="id_m" value="<? echo $id_mensaje;?>">
			<input type="hidden" name="tipo_m" value=0>
			<input type="hidden" name="tipo2" value='MRU'>
			
			<legend>Mensaje #<small><? echo $result->fields['id_mensaje']; ?></small></legend>
			
			<table class="table table-condensed table-hover">
				<tbody>
					<tr>
						<td width="120">Enviado:</td>
						<td>
							<?php 
								$fecha=substr($result->fields['fecha_entrega'],0,10); 
								substr($result->fields['fecha_entrega'],10,16);
								list($a,$m,$d) = explode("-",$fecha);
								echo $d.'/'.$m.'/'.$a.substr($result->fields['fecha_entrega'],10,16);
							?>
						</td>
					</tr>
					<tr>
						<td>Recibido:</td>
						<td>
							<?php 
								if($result->fields['fecha_recibo']!=''){
								$fecha=substr($result->fields['fecha_recibo'],0,10); 
								list($a,$m,$d) = explode("-",$fecha);
								echo $d.'/'.$m.'/'.$a.substr($result->fields['fecha_recibo'],10,16);
								}
								else echo'-'; 
							?>
						</td>
					</tr>
					<tr>
						<td>Vencimiento:</td>
						<td>
							<?php
								$fecha=substr($result->fields['fecha_vencimiento'],0,10); 
								substr($result->fields['fecha_vencimiento'],10,16);
								list($a,$m,$d) = explode("-",$fecha);
								echo $d.'/'.$m.'/'.$a.substr($result->fields['fecha_vencimiento'],10,16);
							?>
						</td>
					</tr>
					<tr>
						<td>De:</td>
						<td>
							<?php
								//obtengo el nombre del usuario dado el login 
								$sql="select nombre from usuarios where login='".$result->fields['usuario_origen']."';";
								$nombre_o=$db->Execute($sql) or die($db->ErrorMsg());
								echo $nombre_o->fields['nombre'];
							?>
						</td>
					</tr>
					<tr>
						<td>Para:</td>
						<td>			
							<? 
								$sql="select nombre from usuarios where login='".$result->fields['usuario_destino']."';";
								$nombre_d=$db->Execute($sql) or die($db->ErrorMsg());
								echo $nombre_d->fields['nombre']; 
							?>
						</td>
					</tr>
					<tr>
						<td>Mensaje:</td>
						<td>
							<?php 
								echo $result->fields['comentario']; 
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="form-actions">
				<input class="btn btn-primary" type="Submit" name="Submit" value="Volver a la Bandeja de Entrada">
			</div>
		</div>
	</div>
</form>
</div>
</body>
</html>

<? 
		}
	}
?>