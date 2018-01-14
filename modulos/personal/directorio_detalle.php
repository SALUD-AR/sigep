<?php
/*
AUTOR: Gabriel
MODIFICADO POR:
$Author: gabriel $
$Revision: 1.6 $
$Date: 2005/11/28 19:34:38 $
*/
	require("../../config.php");
	require("gutils.php");
	
	////////////////////////////////////////////////////////////////////////////////
		$rta_consulta=sql("select id_base_trabajo as key, nombre as label from personal.base_trabajo order by nombre", "c12") or fin_pagina();
		$i=0;
		while (!$rta_consulta->EOF) $sels[$i++]=$rta_consulta->fetchRow();
	////////////////////////////////////////////////////////////////////////////////

	$modo=$parametros["modo"] or $modo=$_POST["modo"];
	$id_directorio=$parametros["id_directorio"] or $id_directorio=$_POST["id_directorio"];
	$id_legajo=$parametros["id_legajo"] or $id_legajo=$_POST["id_legajo"];
	$login=$parametros["login"] or $login=$_POST["login"];
	
	$apellido=$parametros["apellido"] or $apellido=$_POST["apellido"];
	$nombre=$parametros["nombre"] or $nombre=$_POST["nombre"];
	$direccion=$parametros["direccion"] or $direccion=$_POST["direccion"];
	$dni=$parametros["dni"] or $dni=$_POST["dni"];

	$planta=$parametros["planta"] or $planta=$_POST["planta"];
	$dir_mail=$parametros["dir_mail"] or $dir_mail=$_POST["dir_mail"];
	$dir_icq=$parametros["dir_icq"] or $dir_icq=$_POST["dir_icq"];
	$dir_msn=$parametros["dir_msn"] or $dir_msn=$_POST["dir_msn"];
	$dir_mic=$parametros["dir_mic"] or $dir_mic=$_POST["dir_mic"];
	$tel_particular=$parametros["tel_particular"] or $tel_particular=$_POST["tel_particular"];
	$tel_celular=$parametros["tel_celular"] or $tel_celular=$_POST["tel_celular"];
	$tel_trabajo=$parametros["tel_trabajo"] or $tel_trabajo=$_POST["tel_trabajo"];
	$tel_interno=$parametros["tel_interno"] or $tel_interno=$_POST["tel_interno"];

	$directorio_activo=$parametros["directorio_activo"] or $directorio_activo=$_POST["directorio_activo"];
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($_POST["traerDatos"]){
		if ($login){
			$consulta="select u.nombre, u.apellido, u.direccion, u.telefono, u.celular, u.mail, u.pcia_ubicacion,	l.dni, l.id_legajo
				from sistema.usuarios u left join personal.legajos l using (id_usuario) where login='".$login."'";
			$rta_consulta=sql($consulta, "c45") or fin_pagina();
			if (($rta_consulta)&&($rta_consulta->recordCount()==1)){
				$fila=$rta_consulta->fetchRow();
				
				$nombre=$fila["nombre"];
				$apellido=$fila["apellido"];
				$direccion=$fila["direccion"];
				$tel_particular=$fila["telefono"];
				$tel_celular=$fila["celular"];
				$dir_mail=$fila["mail"];
				$planta=$fila["pcia_ubicacion"];
				$dni=$fila["dni"];
				$id_legajo=$fila["id_legajo"];
				
				$modo="modif";
				$new="";
			}else {
				$mensaje="<h3><center><b><font color='red'>No se encontraron datos.</font></b></center></h3>";
				$modo="nuevo";
				$new="algo";
			}
		}elseif ($id_legajo){
			$consulta="select u.nombre, u.apellido, u.direccion, l.dni, u.telefono, u.celular, u.mail, u.pcia_ubicacion,
					u.login, l.id_legajo
				from personal.legajos l left join sistema.usuarios u using (id_usuario)
				where id_legajo=".$_POST["id_legajo"];
			$rta_consulta=sql($consulta, "c63") or fin_pagina();
			if (($rta_consulta)&&($rta_consulta->recordCount()==1)){
				$fila=$rta_consulta->fetchRow();
				
				$nombre=$fila["nombre"];
				$apellido=$fila["apellido"];
				$direccion=$fila["direccion"];
				$dni=$fila["dni"];
				$tel_particular=$fila["telefono"];
				$tel_celular=$fila["celular"];
				$dir_mail=$fila["mail"];
				$planta=$fila["pcia_ubicacion"];
				$id_legajo=$fila["id_legajo"];
				$login=$fila["login"];
				
				$modo="modif";
				$new="";
			}else {
				$mensaje="<h3><center><b><font color='red'>No se encontraron datos.</font></b></center></h3>";
				$modo="nuevo";
				$new="algo";
			}
		}
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($_POST["historial"]){
		$directorio_activo="n";
	}
	
	if ($modo=="nuevo"){
		$rta=sql("select nextval('personal.directorio_id_directorio_seq') as id_dir");
		$id_directorio=$rta->fields["id_dir"];
		$titulo_tabla="Registro nuevo";
		$directorio_activo="s";
		$modo="modif";
		$new="disabled";
	}elseif ($modo=="modif"){
		$titulo_tabla="Modificación de los datos de ".$apellido.", ".$nombre;
	}else fin_pagina();
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($_POST["bguardar"]){
		$temporal=sql("select * from personal.directorio where id_directorio=".$id_directorio, "c85") or fin_pagina();
		if ($temporal->recordCount()==0){
			$sql="insert into personal.directorio (id_directorio, nombre, apellido, direccion, dni, planta,
				dir_mail, dir_msn, dir_icq, dir_mic, 
				tel_particular, tel_celular, tel_trabajo, tel_interno, login, id_legajo, directorio_activo)";
			$sql.="values (".$id_directorio.", '".$nombre."', '".$apellido."', '".$direccion."', '".$dni."', ".$planta.", '"
				.$dir_mail."', '".$dir_msn."', '".$dir_icq."', '".$dir_mic."', '"
				.$tel_particular."', '".$tel_celular."', '".$tel_trabajo."', '".$tel_interno."', '".$login."', "
				.(($id_legajo)?$id_legajo:"null").", '".(($directorio_activo)?$directorio_activo:"s")."')";
		}else{
			$sql="update personal.directorio set ";
			$sql.=" nombre='".$nombre."', apellido='".$apellido."', direccion='".$direccion."', dni='".$dni."', planta=".$planta
				.", dir_mail='".$dir_mail."', dir_msn='".$dir_msn."', dir_icq='".$dir_icq."', dir_mic='".$dir_mic
				."', tel_particular='".$tel_particular."', tel_celular='".$tel_celular."', tel_trabajo='".$tel_trabajo
				."', tel_interno='".$tel_interno."', login='".$login."', id_legajo=".(($id_legajo)?$id_legajo:"null")
				.", directorio_activo='".(($directorio_activo)?$directorio_activo:"s")."' ";
			$sql.=" where id_directorio=".$id_directorio;
		}
		sql($sql, "c98 - Error al agregar/actualizar registro") or fin_pagina();
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	cargar_calendario();
	echo($html_header);
	if($parametros['accion']!=""){ Aviso($parametros['accion']);}
	//////////////////////////////////////////////////////////////////////////////
	echo($mensaje);
	?>
	<form name="form1" method="POST" action="directorio_detalle.php">
		<input type="hidden" name="modo" value="<?=$modo?>">
		<input type='hidden' name='id_directorio' value='<?=$id_directorio?>'>
		<input type='hidden' name='directorio_activo' value='<?=$directorio_activo?>'>

		<table border="1" cellspacing="0" bgcolor="<?=$bgcolor2?>" width="90%" align="center">
			<th align="center" colspan="4" id="mo"><?=$titulo_tabla?></th>
			<tr>
				<td id="mo" width="20%">Apellido:</td>
				<td width="30%"><input type="text" name="apellido" id="apellido" value="<?=$apellido?>" style="width:'100%'"></td>
				<td rowspan="6" colspan="2" align="center">
					Login: <input type="text" name="login" id="login" value="<?=$login?>">&nbsp;&nbsp;
					Legajo: <input type="text" name="id_legajo" id="id_legajo" value="<?=$id_legajo?>" style="width:8%">&nbsp;&nbsp;
					<input type="submit" name="traerDatos" value="Traer datos"><br><br>
					<?
						if (file_exists(MOD_DIR."/personal/fotos/leg_$id_legajo.gif")) $foto = "fotos/leg_$id_legajo.gif";
						elseif (file_exists(MOD_DIR."/personal/fotos/leg_$id_legajo.jpg")) $foto = "fotos/leg_$id_legajo.jpg";
						else $foto = "fotos/no_disponible.jpg"; 
					?>
					<img width=120 height=120 src=<?=$foto?>><br>
				</td>
			</tr>
			<tr>
				<td id="mo">Nombre:</td>
				<td><input type="text" name="nombre" id="nombre" value="<?=$nombre?>" style="width:'100%'"></td>
			</tr>
			<tr>
				<td id="mo">DNI:</td>
				<td ><input type="text" name="dni" id="dni" value="<?=$dni?>" style="width:'100%'"></td>
			</tr>
			<tr>
				<td id="mo">Trabaja en:</td>
				<td><?=g_draw_mix_select("planta", $planta, $sels, 1, "style='width:100%'");?></td>
			</tr>
			<tr>
				<td id="mo">Tel. trabajo:</td>
				<td><input type="text" name="tel_trabajo" id="tel_trabajo" value="<?=$tel_trabajo?>" style="width:'100%'"></td>
			</tr>
			<tr>
				<td id="mo">Interno:</td>
				<td><input type="text" name="tel_interno" id="tel_interno" value="<?=$tel_interno?>" style="width:'100%'"></td>
			</tr>
			<tr>
				<td id="mo">Dirección:</td>
				<td colspan="3"><input type="text" name="direccion" id="direccion" value="<?=$direccion?>" style="width:'100%'"></td>
			</tr>
			<tr>
				<td id="mo">Tel. particular:</td>
				<td><input type="text" name="tel_particular" id="tel_particular" value="<?=$tel_particular?>" style="width:'100%'"></td>
				<td id="mo" width="20%">Celular:</td>
				<td width="30%"><input type="text" name="tel_celular" id="tel_celular" value="<?=$tel_celular?>" style="width:'100%'"></td>
			</tr>
			<tr>
				<td id="mo">E-mail:</td>
				<td><input type="text" name="dir_mail" id="dir_mail" value="<?=$dir_mail?>" style="width:'100%'"></td>
				<td id="mo">MIC:</td>
				<td><input type="text" name="dir_mic" id="dir_mic" value="<?=$dir_mic?>" style="width:'100%'"></td>
			</tr>
			<tr>
				<td id="mo">MSN:</td>
				<td><input type="text" name="dir_msn" id="dir_msn" value="<?=$dir_msn?>" style="width:'100%'"></td>
				<td id="mo">ICQ:</td>
				<td><input type="text" name="dir_icq" id="dir_icq" value="<?=$dir_icq?>" style="width:'100%'"></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" bgcolor="<?=$bgcolor2?>" width="90%" align="center">
			<tr>
				<td align="center">
					<?if(($_ses_user["login"]==$login)||(permisos_check("inicio", "boton_guardar_directorio_detalle"))){?>
					<input type="submit" name="bguardar" value="Guardar cambios">
					<?}?>
					<input type="button" name="volver" value="Volver" onclick="document.location='directorio.php'">
					<?if (($directorio_activo=="s")&&(permisos_check("inicio", "boton_pasar_historial_directorio_detalle"))){?>
						<input type="submit" name="historial" value="pasar a Historial">
					<?}?>
				</td>
			</tr>
		</table>
		<br>
	</form>
<?
	fin_pagina();
?>