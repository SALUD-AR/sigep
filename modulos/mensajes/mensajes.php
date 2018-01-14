<?php
require_once("../../config.php");
require_once("funciones.php");

switch ($_POST['boton']) {
	case "Nuevo Mensaje": {
		require_once("../mensajes/nuevo_mens.php");
		break;
	}
	case "Reenviar": {
		require_once("../mensajes/redirige.php");
		break;
	}
	case "Borrar": {
		require_once("../mensajes/borrar.php");
		break;
	}
	case "Administrar Mensajes": {
		require_once('../mensajes/mensajeria.php');
		exit;
		break;
	}
	default: { 
		$filas_encontradas=0;
		echo $html_header;
?>
<script type="text/javascript">

	function comprueba() {
		if($("input[name='radio']:checked").val() != null) {
			return true;
		} else {
			alert("Debe seleccionar un mensaje");
			return false;
		}
	}

	function borrar() {
		var valor;
		if (!comprueba())
			return false;
		else {
			valor=prompt('Dime el motivo por el cual desestimas este mensaje','');
			if ((valor==null) || (valor==""))
				return false;
			else {
				window.document.form.mensaje.value=valor;
				return true;
			}
		}
	}
	
	function controlnoc() {
		alert("Hay ordenes de compra que están por vencer.");
	}
	
	function controlvenc() {
		alert("Hay ordenes de compra vencidas.");
	}
</script>
<?php
	// echo "<link rel=stylesheet type='text/css' href='$html_root/lib/bootstrap-3.3.1/css/custom-bootstrap.css'>";
	// echo "<link rel=stylesheet type='text/css' href='$html_root/lib/bootstrap-3.3.1/css/main.css'>";
	// echo "<link rel=stylesheet type='text/css' href='$html_root/lib/jquery/jquery-ui.min.css'>";
	
	// echo "<script languaje='javascript' src='$html_root/lib/jquery/jquery-ui.min.js'></script>";
	// echo "<script languaje='javascript' src='$html_root/lib/jquery/jquery-ui-datepicker-es.js'></script>";
	//
	?>
<?php
	//este codigo es el que controla si ordenes de compra por vencer o mensajes que venecen hoy
	if(($_POST['boton1']!='Mensajeria')&&($_ses_mensajes_primera_v != 1)) {
		phpss_svars_set("_ses_mensajes_primera_v",1);
		$fecha_actual=date("Y-m-d H:m:s");
		$fecha_proxima=date("Y-m-d",mktime(0,0,0,date("m"),(date("d")+7),date("Y")));
		$sql = "select count(id_mensaje) as cant from mensajes where tipo1='LIC' and tipo2='NOC' and terminado='f' and desestimado='f' and usuario_destino='".$_ses_user['login']."' and fecha_vencimiento < '$fecha_actual'";
		$result=$db->Execute($sql) or die($db->ErrorMsg());
		$vench=$result->fields["cant"];
		$sql = "select count(id_mensaje) as cant from mensajes where tipo1='LIC' and tipo2='NOC' and terminado='f' and desestimado='f' and usuario_destino='".$_ses_user['login']."' and fecha_vencimiento <= '$fecha_proxima'";
		$result=$db->Execute($sql) or die($db->ErrorMsg());
		$noc=$result->fields["cant"];

		if($noc) {
 ?>
 
		 <script type="text/javascript">
			controlnoc();
		 </script>
 
 <?
		}//if noc

		if($vench){
?>
		<script type="text/javascript">
			controlvenc();
		</script>
 <? 
		}//vence hoy
	}//if

	// actualizo fecha de recibido y bit de recibido de los que no lo tienen
	$sql1="select id_mensaje,fecha_recibo from mensajes where recibido='f' or fecha_recibo is null and usuario_destino='".$_ses_user['login']."'";
	$result1=$db->Execute($sql1) or die($db->ErrorMsg());
	$fecha_r=date("Y-m-d H:i:s");
	
	while (!$result1->EOF) {
		if($result1->fields['fecha_recibo']=='') {
			$sql="update mensajes set fecha_recibo='".$fecha_r."' where id_mensaje=".$result1->fields['id_mensaje'];
			$result=$db->Execute($sql) or die($db->ErrorMsg());
		}
		$result1->MoveNext();      
	}
 ?>
<form name="form" method="post" action="mensajes.php">

<div class="container">
	<legend>Bandeja de Entrada</legend>
	
	<input type="hidden" name="cantr" value="<?PHP echo $cantidad; ?>">
	<input type="hidden" name="mensaje" value="">
	
	<div class="row">
		<div class="col-md-4">
			<div  class="btn-group btn-group-sm btn-group-justified" role="group">
				<div class="btn-group" role="group">
					<input id="btn-nuevo" class="btn btn-default" type="submit" name="boton" value="Nuevo Mensaje">
				</div>	
				<div class="btn-group" role="group">
					<input id="btn-reenviar" class="btn btn-default" type="submit" name="boton" value="Reenviar" onClick="return comprueba();">
				</div>	
				<div class="btn-group" role="group">
					<input id="btn-borrar" class="btn btn-default" type="submit" name="boton" value="Borrar" onClick="return borrar();">
				</div>	
			</div>
		</div>
		<div class="col-md-4">
			<!--
			<input class="btn pull-right" type="submit" name="boton" value="Administrar Mensajes" >
			-->
		</div>
	</div>
	
	<div class="row">
	
	<?php
	$est = $_GET['est'];
	
	switch($est){
		case "0": 
			$orden=" order by fecha_entrega";
			break;
		case "1": 
			$orden=" order by comentario";
			break;
		case "2": 
			$orden=" order by fecha_vencimiento";
			break;
		default:
			$orden=" order by fecha_recibo desc";
			break;
	}
	
	$sql= "select tipo1, recibido, id_mensaje, comentario, titulo, fecha_entrega, fecha_vencimiento, fecha_recibo, usuario_origen
		   from mensajes 
		   where terminado = 'f' and desestimado = 'f' and usuario_destino = '".$_ses_user['login']."'"
		   .$orden;
	
	$result=$db->Execute($sql) or die($db->ErrorMsg());
	$cantidad+=$result->RecordCount();
	
	if ($cantidad > 0) {
	?>
		<br/>
		<table class="table table-condensed table-hover">
		<thead>
			<tr>
				<th width="5%">
				</th>
				<th width="15%">
					Fecha
					<a style="text-decoration:none" href=<?php echo "mensajes.php?est=0"; ?>>
						<i class="icon-chevron-down"></i>
					</a>
				</th>
				<th width="15%">
					Enviado Por
				</th>
				<th width="45%">
					Mensaje
					<a style="text-decoration:none" href=<?php echo "mensajes.php?est=1"; ?>>
						<i class="icon-chevron-down"></i>
					</a>
				</th>
				<th width="20%">
					Vencimiento
					<a style="text-decoration:none " href=<?php echo "mensajes.php?est=2"; ?>>
						<i class="icon-chevron-down"></i>
					</a>
				</th>
			</tr>
		</thead>
		<tbody>
	<?
	$i=0;
	while(!$result->EOF) {
		$fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
		$fecha_venc = strtotime($result->fields['fecha_vencimiento']);
		$urgencia = 0; // Normal
		
		$dias_vence = $fecha_venc - $fecha_actual;
		
		switch($dias_vence){
			case ($dias_vence<=0): 
				$urgencia = 2; // Vencido
				break;
			case ($dias_vence<=259200): 
				$urgencia = 1; // Por Vencer
				break;
			default:
				$urgencia = 0; // Normal
				break;
		}
		
	?>
		<tr>
			<td align="center">
				<input type="radio" name="radio" value="<?php echo $result->fields['id_mensaje'] ?>" >
			</td>
			<td> 
				<?php 
					$fecha1=fecha(substr($result->fields['fecha_entrega'],0,10));
					$tiempo1=substr($result->fields['fecha_entrega'],10,18);
					echo $fecha1.$tiempo1;
				?>
			</td>
			<td> 
				<?php 
					echo $result->fields['usuario_origen'];
				?>
			</td>
			<td>
				<a href="ver_mens.php?id_mensaje=<? echo $result->fields['id_mensaje'];?>&donde=0">
					<?php echo $result->fields['comentario']; ?>
				</a>
			</td>
			<td>
				<?php
					$fecha=fecha(substr($result->fields['fecha_vencimiento'],0,10));
					$tiempo=substr($result->fields['fecha_vencimiento'],10,18);
					echo $fecha.$tiempo;
					
					// Marca de vencimiento
					if($urgencia==2) {
						echo "   <span class='label label-danger' style='font-size: 11px;'>Vencido</span>";
					} else {
						if ($urgencia==1) {
							echo "   <span class='label label-warning' style='font-size: 11px;''>Por Vencer</span>";
						}
					}
				?>
			</td>
		</tr>
		
		<input type="hidden" name="tipo1" value="<?php echo $result->fields['tipo1']; ?>">
		<input type="hidden" name="comentario[<?php echo $result->fields['id_mensaje']; ?>]" value="<?php echo $resultado['comentario']; ?>">
	<? 	 
	$result->MoveNext();
	}//while 
	}// end if cantidad > 0
	else
	{
	?>
		<p>Guau, tu bandeja de entrada está vacía.</p>
		<script>
			$("#btn-reenviar").attr("disabled", "disabled");
			$("#btn-borrar").attr("disabled", "disabled");
			$("#btn-nuevo").addClass("btn-primary");
		</script>
	<?
	}
	?>
	</tbody>
	</table>
	</div>

</div>
</form>
</body>
</html>

<?php
	// Switch Inicial
		//End Default
		}
	// End Switch
	}
?>