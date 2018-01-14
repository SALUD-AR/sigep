<?php
/*
Autor: Gabriel
MODIFICADO POR:
$Author: mari $
$Revision: 1.9 $
$Date: 2006/06/06 17:32:39 $
*/

require_once("../../config.php");
$hora_entra=$parametros["hora_entra"] or $hora_entra=$_POST["hora_entra"];
$hora_sale=$parametros["hora_sale"] or $hora_sale=$_POST["hora_sale"];
$fecha=$parametros["fecha"] or $fecha=Fecha_db($_POST["fecha"]);
$id_asistencia=$parametros["id_asistencia"] or $id_asistencia=$_POST["id_asistencia"];
$id_legajo=(($parametros["id_legajo"]!="-1")?$parametros["id_legajo"]:"") or $id_legajo=$_POST["id_legajo"];
$id_usuario=(($parametros["id_usuario"]!="-1")?$parametros["id_usuario"]:"") or $id_usuario=$_POST["id_usuario"];

if ($_POST["guardar"]){
	$hora_in="'".$_POST["hora_entrada"].":".$_POST["minuto_entrada"]."'";
	$hora_out="'".$_POST['hora_salida'].":".$_POST["minuto_salida"]."'";
	
	if ($hora_out=="':'") $hora_out='null';
	
	$sql="select id_asistencia from personal.asistencia 
			where fecha='$fecha' and id_usuario=$id_usuario";
		
	$result=sql($sql) or fin_pagina();
	
	if ($result->recordCount()>0){ 
		?>
			<script> 
				alert("Error ya hay una entrada para la fecha seleccionada")
			</script>
		<?
	}else{
		$sql="insert into asistencia(id_legajo, id_usuario, fecha, hora_entra, hora_sale)values
			(".(($id_legajo)?$id_legajo:"null").", ".(($id_usuario)?$id_usuario:"null").", '$fecha', $hora_in, $hora_out)";
		sql($sql) or fin_pagina();
		?>
			<script>
				window.opener.location.reload();
				window.close();
			</script>
		<?
	}
}
cargar_calendario();
echo $html_header;
?>
<script>
function control_dato(){
	var error=0;
	
	if (document.all.hora_entrada.value=="") error=4;
	else{
		if ((document.all.hora_entrada.value<0)||(document.all.hora_entrada.value>24)) error=1;
		if (document.all.minuto_entrada.value=="") document.all.minuto_entrada="00";
		else if ((document.all.minuto_entrada.value<0)||(document.all.minuto_entrada.value>59)) error=2;
	}
	if (document.all.hora_salida.value==""){
		if (document.all.minuto_salida.value!="")	error=3;
	}else{
		/*if ((document.all.hora_salida.value<document.all.hora_entrada.value)||
			((document.all.hora_salida.value==document.all.hora_entrada.value)&&
				(document.all.minuto_salida.value<=document.all.minuto_entrada.value))) error=5;*/
		if ((document.all.hora_salida.value<0)||(document.all.hora_salida.value>24)) error=1;
		if (document.all.minuto_salida.value=='') document.all.minuto_salida.value="00";
		else if ((document.all.minuto_salida.value<0)||(document.all.minuto_salida.value>59)) error=2;
	}
	switch (error){
		case 1: alert("La hora debe ser un número entero entre 0 y 24"); break;
		case 2: alert("Los minutos debe ser un número entero entre 0 y 59"); break;
		case 3: alert("El campo de la hora de salida no debe estar vacío si ha ingresado minutos de salida"); break;
		case 4: alert("El campo de la hora de entrada no debe estar vacío"); break
		//case 5: alert("La hora de salida no puede ser anterior ni igual a la hora de entrada"); break;
	}
	if (error==0){
		document.all.hora_entra.value=document.all.hora_entrada.value+":"+document.all.minuto_entrada.value;
		document.all.hora_sale.value=document.all.hora_salida.value+":"+document.all.minuto_salida.value;
		return true;
	}else return false;
}
function alProximoInput(elmnt, content, next){
  if (content.length==elmnt.maxLength){
	  if (typeof(next)!="undefined"){
		  next.focus();
		}
	  else  document.all.guardar.focus();	
	}
}
</script>
<form method="POST" action="editar_hora.php" name="editar_hora">
<input type="hidden" name="id_asistencia" value="<?=$id_asistencia?>">
<input type="hidden" name="hora_entra" value="<?=$hora_entra?>">
<input type="hidden" name="hora_sale" value="<?=$hora_sale?>">
<input type="hidden" name="id_legajo" value="<?=$id_legajo?>">
<input type="hidden" name="id_usuario" value="<?=$id_usuario?>">

<table align="center" cellpadding="0" cellspacing="1" width="90%" border="1">
	<tr>
		<td id="mo"><font size="2">Fecha</font></td>
		<td align="right" bgcolor="<?=$bgcolor_out?>">
			<input type="text" name="fecha" value="<?=Fecha($fecha)?>"><?=link_calendario('fecha');?>
		</td>
	</tr>
	<tr>
		<td id="mo">Hora de entrada</td>
		<td align="right" bgcolor="#FF0000">
			<input type="text" maxlength="2" size="2" name="hora_entrada" 
				value="<?=substr($hora_entra, 0, 2)?>" 
				onfocus="this.select();"
				onkeypress="return filtrar_teclas(event,'0123456789'); "
				onkeyup="alProximoInput(this, this.value, document.all.minuto_entrada)">
			</input>
			&nbsp;:&nbsp;
			<input type="text" maxlength="2" size="2" name="minuto_entrada" 
				value="<?=substr($hora_entra, 3, 2)?>" 
				onfocus="this.select();"
				onkeypress="return filtrar_teclas(event,'0123456789'); "
				onkeyup="alProximoInput(this, this.value, document.all.hora_salida)">
			</input>
			&nbsp; hrs.
		</td>
	</tr>
	<tr>
		<td id="mo">Hora de salida</td>
		<td align="right" bgcolor="#FF0000">
			<input type="text" maxlength="2" size="2" name="hora_salida" 
				value="<?=substr($hora_sale, 0, 2)?>" 
				onfocus="this.select();"
				onkeypress="return filtrar_teclas(event,'0123456789'); "
				onkeyup="alProximoInput(this, this.value, document.all.minuto_salida)">
			</input>
			&nbsp;:&nbsp;
			<input type="text" maxlength="2" size="2" name="minuto_salida" 
				value="<?=substr($hora_sale, 3, 2)?>" 
				onfocus="this.select();"
				onkeypress="return filtrar_teclas(event,'0123456789'); "
				onkeyup="alProximoInput(this, this.value, document.all.hora_entrada)">
			</input>
			&nbsp; hrs.
		</td>
	</tr>

	<tr bgcolor="<?=$bgcolor_out?>">
		<td align=center colspan="4">
			<input name="guardar" type="submit" value="Guardar cambios" onclick="return control_dato()" >&nbsp;
			<input name="boton" type="button" value="Cerrar" onclick="window.close();">
    </td>
	</tr>
</table>
</form>
</body>
</html>
<? 
fin_pagina();
?>