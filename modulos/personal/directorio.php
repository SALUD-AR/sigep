<?php
/*
AUTOR: Gabriel
MODIFICADO POR:
$Author: gabriel $
$Revision: 1.2 $
$Date: 2005/11/28 14:10:05 $
*/
	require("../../config.php");
	cargar_calendario();
	variables_form_busqueda("directorio", array());
	if ($cmd == ""){
		$cmd="actuales";
		$_ses_directorio["cmd"]=$cmd;
  	phpss_svars_set("_ses_directorio", $_ses_directorio);
	}
	//////////////////////////////////////////////////////////////////////////////
	$datos_barra = array(
		array("descripcion"=> "Actuales", "cmd"=> "actuales"),
		array("descripcion"=> "Historial", "cmd"=> "historial")
	);
	$orden = array(
		"default_up"=>"0",
		"default" => "1",
		"1" => "directorio.apellido",
		"2" => "directorio.nombre",
		"3" => "directorio.login",
		"4" => "directorio.dir_mail",
		"5" => "directorio.direccion",
		"6" => "directorio.dir_msn",
		"7" => "directorio.dir_icq",
		"8" => "directorio.dir_mic",
		"9" => "directorio.dni",
		"10" => "directorio.tel_celular",
		"11" => "directorio.tel_particular",
		"12" => "directorio.tel_trabajo",
		"13" => "directorio.tel_interno",
		"14" => "base_trabajo.nombre"
	);
	$filtro = array(        
  	"directorio.apellido" => "Apellido",
		"directorio.nombre" => "Nombre",
		"directorio.login" => "Login",
		"directorio.dir_mail" => "E-mail",
		"directorio.direccion" => "Dirección",
		"directorio.dir_msn" => "Dir. correo MSN",
		"directorio.dir_icq" => "Dir. correo ICQ",
		"directorio.dir_mic" => "Dir. correo MIC",
		"directorio.dni" => "DNI",
		"directorio.tel_celular" => "Tel. Celular",
		"directorio.tel_particular" => "Tel. Particular",
		"directorio.tel_trabajo" => "Tel. Trabajo",
		"directorio.tel_interno" =>"Interno"
	);     
	$sql_tmp="select directorio.*, base_trabajo.nombre as base
		from personal.directorio join personal.base_trabajo on (planta=id_base_trabajo)";
	if ($cmd=="actuales"){
		$where_tmp.="(directorio_activo='s')";
	}else{//historial
		$where_tmp.="(directorio_activo='n')";
	}
	
	if($_POST['keyword'] || $keyword) $contar="buscar";
	//////////////////////////////////////////////////////////////////////////////
	echo($html_header);
?>
	<form name="form_directorio" method="POST" action="directorio.php">
	<table cellspacing=2 cellpadding=2 border=0 bgcolor=<? echo $bgcolor3 ?> width=95% align=center>
		<tr>
			<td>
				<? generar_barra_nav($datos_barra);?>  
			</td>
		</tr>
		<tr>
			<td align=center>
				<? list($sql, $total_leg, $link_pagina, $up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar"); ?>
				<input type=submit name=buscar value='Buscar'>
			</td>
		</tr>
	</table>
	<table align="center" width="90%" cellpadding="1" cellspacing="0" border="1">
		<tr>
			<td colspan="6">
				<table width=100%>
					<tr id=ma>
						<td width=30% align=left><b>Total:</b> <?=$total_leg?> registros.</td>
						<td width=70% align=right><?=$link_pagina?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr id=mo>
 			<td nowrap><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"1","up"=>$up))?>'>Apellido, Nombre</a></b></td>
 			<td nowrap><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"3","up"=>$up))?>'>Login</a></b></td>
 			<td nowrap><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"13","up"=>$up))?>'>Interno</a></b></td>
 			<td nowrap><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"10","up"=>$up))?>'>Celular</a></b></td>
 			<td nowrap><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"4","up"=>$up))?>'>E-mail</a></b></td>
 			<td nowrap><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"14","up"=>$up))?>'>Planta</a></b></td>
		</tr>
		<?
			$result=sql($sql) or fin_pagina();
			while ($fila=$result->fetchRow()){
				$ref = encode_link("directorio_detalle.php",array_merge(array("modo"=>"modif", "pagina"=>"directorio_detalle.php"), $fila));
				tr_tag($ref, "title='".$fila["direccion"]."'");
		?>
			<td><?echo $fila["apellido"].", ".$fila["nombre"]?>&nbsp;</td>
			<td><?=$fila["login"]?>&nbsp;</td>
			<td><?=$fila["tel_interno"]?>&nbsp;</td>
			<td><?=$fila["tel_celular"]?>&nbsp;</td>
			<td><?=$fila["dir_mail"]?>&nbsp;</td>
			<td><?=$fila["base"]?>&nbsp;</td>
		</tr>
		<?
			}
?>
	</table>
	<center>
		<input type="button" name="nuevo" value="Nuevo registro" onclick="document.location.href='<?=encode_link("directorio_detalle.php",array("modo"=>"nuevo", "pagina"=>"directorio_detalle.php"))?>'">
	</center>
</form>
<?
fin_pagina();
?>
?>