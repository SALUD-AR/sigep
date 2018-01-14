<?php
/*
AUTOR: Gabriel
MODIFICADO POR:
$Author: gabriel $
$Revision: 1.4 $
$Date: 2005/11/29 13:21:28 $
*/
	require("../../config.php");
	require_once("../personal/gutils.php");
	
	if ($_POST["guardar"]){
		//sql("delete from general.departamentos_empresa", "c13") or fin_pagina();
		for ($i=0; $i<$_POST["h_filas"]; $i++){
			if ($_POST["depto_".$i]){
				if ($_POST["id_depto_$i"]){
					$rta_consulta=sql("select * from general.departamentos_empresa where id_departamento_empresa=".$_POST["id_depto_$i"], "c15") or fin_pagina();
					$regs=$rta_consulta->recordCount();
				}else $regs=0;
				if ($regs==0){
					sql("insert into general.departamentos_empresa (id_departamento_empresa, nombre_departamento, mail_departamento)
						values(".($i+1).", '".$_POST["depto_".$i]."', '".$_POST["mail_".$i]."')", "c15-".$i) or fin_pagina();
				}else{
					sql("update general.departamentos_empresa set nombre_departamento='".$_POST["depto_".$i]."', 
						mail_departamento='".$_POST["mail_".$i]."' where id_departamento_empresa=".$_POST["id_depto_$i"], "c15-".$i) or fin_pagina();
				}
			}
		}
	}
	
	$consulta="select * from general.departamentos_empresa order by nombre_departamento";
	$rta_consulta=sql($consulta, "c11") or fin_pagina();
	
	echo($html_header);
?>
<form action="editar_piramide.php" method="POST" id="form1">
	<input type="hidden" name="h_filas" id="h_filas" value="<?=$_POST["h_filas"]?>">
	<table width="90%" bgcolor="<?=$bgcolor3?>" align="center">
		<tr id="mo">
			<td colspan="3">
				<h2>Departamentos de la empresa:</h2>
			</td>
		</tr>
		<tr id="mo">
			<td>Nombre</td>
			<td>E-mail de departamento (<font color="Red">separar cada mail con una coma ","</font>)</td>
			<td width="10%">&nbsp;</td>
		</tr>
<?
	$i=1;
	while ($fila=$rta_consulta->fetchRow()) {
?>
		<tr>
			<input type="hidden" name="id_depto_<?=$i?>" value="<?=$fila["id_departamento_empresa"]?>">
			<td><input type="text" name="depto_<?=$i?>" value="<?=$fila["nombre_departamento"]?>" style="width:'100%'"></td>
			<td><input type="text" name="mail_<?=$i?>" value="<?=$fila["mail_departamento"]?>" style="width:'100%'"></td>
			<td align="center"><input type="button" name="b_borrar_<?=$i?>" value="Borrar" onclick="document.all.depto_<?=$i?>.value=''"></td>
		</tr>
<?
		$i++;
	}
	for($j=0; $j<5; $j++, $i++){
?>
		<tr>
			<td><input type="text" name="depto_<?=$i?>" value="" style="width:'100%'"></td>
			<td><input type="text" name="mail_<?=$i?>" value="" style="width:'100%'"></td>
			<td align="center"><input type="button" name="b_borrar_<?=$i?>" value="Borrar" onclick="document.all.depto_<?=$i?>.value=''"></td>
		</tr>
<?
	}
?>
	</table>
	<script>document.all.h_filas.value=<?=$i?>;</script>
	<center>
		<input type="submit" name="guardar" value="Guardar cambios">
		<input type="button" name="cerrar" value="Volver" onclick="document.location.href='<?=encode_link("calidad_auditorias.php",array())?>'">
	</center>
</form>
<?
fin_pagina();