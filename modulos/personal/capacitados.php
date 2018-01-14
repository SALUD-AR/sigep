<?php
/*
AUTOR: Gabriel
MODIFICADO POR:
$Author: gabriel $
$Revision: 1.15 $
$Date: 2005/11/07 19:30:33 $
*/
	require_once("../../config.php");
	require_once("gutils.php");
	//////////////////////////////////////////////////////////////////////////////
	/*$var_id=array(
		"sel_periodo"=>"",
		"fecha_d"=>"",
		"fecha_h"=>""
	);*/
	//variables_form_busqueda("capacitados_calif");
	/*$flag0=false;
	if ($_POST["sel_periodo"]){
		$sel_periodo=1;
		$fecha_h=$_POST["fecha_h"];
		$fecha_d=$_POST["fecha_d"];
		if ($fecha_d=="") $sel_periodo=false;
		$flag0=true;
	}else{
		$sel_periodo=false;
		$flag0=true;
	}
	if (($_POST["form_busqueda"])&&(!$_POST["sel_periodo"])&&($sel_periodo)){
		$sel_periodo="-1";
		$fecha_d="";
		$fecha_h="";
		$_ses_stats["sel_periodo"]=$sel_periodo;
		$_ses_stats["fecha_d"]=$fecha_d;
		$_ses_stats["fecha_h"]=$fecha_h;
		$flag0=true;
	}
	if ($flag0) phpss_svars_set("_ses_capacitados_calif", $_ses_capacitados_calif);
	*/
	variables_form_busqueda("capacitados_calif", array());
	if ($cmd==""){
		$cmd="pendientes";
		$_ses_capacitados_calif["cmd"]=$cmd;
  	phpss_svars_set("_ses_capacitados_calif", $_ses_capacitados_calif);
	}
	$datos_barra = array(
		array("descripcion"=> "Pendientes", "cmd"=> "pendientes"),
		array("descripcion"=> "Historial", "cmd"=> "historial")
	);
	$orden = array(
		"default_up"=>"1",
		"default" => "1",
		"1" => "apellido",
		"2" => "tema",
		"3" => "calificacion",
		"4" => "d.nombre",
		"5" => "l.nombre"
	);
	
	$filtro = array(
		"l.nombre" =>"Nombre",
		"apellido" => "Apellido",
  	"tema" => "Tema",
		"d.nombre" => "Lugar de dictado",
	);
	//////////////////////////////////////////////////////////////////////////////
	$sql_tmp="select c.id_capacitacion, c.tema, c.dictado, c.comentarios, c.dictado_desde, c.dictado_hasta, d.nombre as locacion,
			l.apellido, l.nombre, cc.calificacion, l.id_legajo 
		from personal.capacitaciones c join personal.capacitados cc using (id_capacitacion) join personal.legajos l using (id_legajo) join personal.base_trabajo d on (id_base_trabajo=ubicacion) ";
	
	if ($cmd=="historial") $where_tmp=" not (calificacion is null) ";
	else{
		$where_tmp=" (calificacion is null and dictado!=0)and(dictado_hasta<='".date("Y-m-d", strtotime("-1 month"))."')";
	}
	$sel_periodo=$_POST["sel_periodo"];
	if ($sel_periodo){
		$fecha_d=$_POST["fecha_d"];
		$fecha_h=$_POST["fecha_h"];
	}
	//////////////////////////////////////////////////////////////////////////////
	echo $html_header; 
	cargar_calendario();
	if($parametros['accion']!=""){ Aviso($parametros['accion']);}
	//////////////////////////////////////////////////////////////////////////////
	if ($_POST["guardar"]){
		if (($_POST["id_calif"]!="")&&($_POST["notas"]!="")){
			$ids=explode(",", $_POST["id_calif"]);
			$notas=explode(",", $_POST["notas"]);
			for ($i=0; $i<count($ids); $i++){
				$last_=strpos($ids[$i], "_", 6);
				$sql_up="update capacitados set calificacion=".$notas[$i].", calificador='".$_ses_user["login"]."', fecha_calificacion='".date("Y-m-d")."'
					where id_capacitacion=".substr($ids[$i], 5, $last_-5)." and id_legajo=".substr($ids[$i], $last_+1, strlen($ids[$i]));
				sql($sql_up, "No se puede asignar calificación") or fin_pagina();
			}
		}
	}
	if (($sel_periodo)&&(($fecha_d)&&($fecha_d!=""))&&(($fecha_h)&&($fecha_h!="")))
		$where_tmp1=" (dictado_desde>= '".Fecha_db($fecha_d)."' and dictado_desde<= '".Fecha_db($fecha_h)."')";
	elseif (($sel_periodo)&&(($fecha_d)&&($fecha_d!=""))&&((!$fecha_h)||($fecha_h==""))){
		$fecha_h=date("d/m/Y");
		$where_tmp1=" (dictado_desde>= '".Fecha_db($fecha_d)."' and dictado_desde<= '".Fecha_db($fecha_h)."')";
	}elseif (!$sel_periodo){
		$fecha_d="";
		$fecha_h="";
	}
	if (($where_tmp1)&&($where_tmp)) $where_tmp.=" and ".$where_tmp1;
	elseif (!$where_tmp) $where_tmp=$where_tmp1;
?>
<script>
function control_datos(datos){
	var califs=new Array();
	var ids=new Array();
	
	for (i=0, j=0; i<datos.length; i++){
		id=eval("document.all."+datos[i]+".value");
		if ((id!="")&&((id<=0)||(id>10))){	
			alert("Error: La calificación debe ser un número entero entre 1 y 10");
			return false;
		}
		if (id!=''){
			califs[j]=id;
			ids[j++]=datos[i];
		}
	}
	document.all.id_calif.value=ids;
	document.all.notas.value=califs;
	return true;
}
</script>
<form name="form_capacitados" method="POST" action="capacitados.php">
	<input type="hidden" name="id_calif" value="" id="id_calif">
	<input type="hidden" name="notas" value="" id="notas">
	<table border=0 bgcolor=<?=$bgcolor3?> width="95%" align=center>
		<tr>
			<td>
				<?=generar_barra_nav($datos_barra)?>
			</td>
		</tr>
		<tr>
			<td align=center nowrap>
				<? list($sql,$total_cap,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar"); ?>
				<input type="checkbox" name="sel_periodo" value="1" <?=(($_POST["sel_periodo"])?" checked ":"")?> onclick="document.all.tabla_busqueda_avanzada.style.display=((this.checked)?'inline':'none');">
				&nbsp;Avanzado&nbsp;&nbsp;&nbsp;
				<input type=submit name=buscar value='Buscar'>
			</td>
		</tr>
		<tr>
			<td>
				<table border=0 bgcolor="<?=$bgcolor3?>" width="70%" align="center" id="tabla_busqueda_avanzada" style="display:none">
					<tr>
						<td>
							<b>Buscar cursadas entre el día:&nbsp;</b>
							<input type=text name='fecha_d' readonly size=11 value='<?=(($_POST["sel_periodo"])?$_POST["fecha_d"]:"")?>'>&nbsp;
							<?=link_calendario('fecha_d')?>
						</td>
						<td>
							<b>y el día:&nbsp;</b>
							<input type=text name='fecha_h' size=11 readonly value='<?=(($_POST["sel_periodo"])?$_POST["fecha_h"]:"")?>'>&nbsp
							<?=link_calendario('fecha_h')?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<script>
		if (document.all.sel_periodo.checked) document.all.tabla_busqueda_avanzada.style.display='inline';
	</script>
	<table align="center" width="95%" cellpadding="1" cellspacing="0" border="1">
		<tr>
			<td colspan="4">
				<table width=100%>
					<tr id=ma>
						<td width=30% align=left><b>Total:</b> <?=$total_cap?> registros.</td>
						<td width=70% align=right><?=$link_pagina?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr id=mo>
 			<td width="40%" nowrap><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"1","up"=>$up))?>'>Apellido, </a><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"5","up"=>$up))?>'>Nombre</a></b></td>
 			<td width="40%"5%" nowrap><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"2","up"=>$up))?>'>Tema</b></td>
 			<td width="5%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"3","up"=>$up))?>'>Calificaci&oacute;n</b></td>
 			<td width="15%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"4","up"=>$up))?>'>Ubicaci&oacute;n (curso)</b></td>
		</tr>
		<?
			$result=sql($sql) or fin_pagina();
			$i=0;
			$ecalificacion[0]="";
			while (!$result->EOF){
				$nombre=$result->fields["apellido"].", ".$result->fields["nombre"];
				$tema=$result->fields["tema"];
				$calificacion=$result->fields["calificacion"];
				$id_capacitacion=$result->fields["id_capacitacion"];
				$locacion=$result->fields["locacion"];
				$dictado=$result->fields["dictado"];
				if (($calificacion=="")&&($result->fields["dictado"]!=0)){
					$ecalificacion[$i]="\"nota_".$id_capacitacion."_".$result->fields["id_legajo"]."\"";
					$calificacion="<input align='center' type='text' maxlength='2' size='2' name=".$ecalificacion[$i]." id=".$ecalificacion[$i++]."
						value='' onkeypress=\"return filtrar_teclas(event,'0123456789');\"></input>";
					$color_celda_calificacion=$bgcolor3;
				}elseif ($calificacion==""){
					$calificacion="&nbsp;";
					$color_celda_calificacion=$bgcolor3;
				}else{
					$calificacion=$result->fields["calificacion"];
					if ($calificacion>=4) $color_celda_calificacion="#1eaa19";
					else $color_celda_calificacion="#fb471e";
					$calificacion="";
				}
				$color_celda=$bgcolor3;
				echo "<tr><td bgcolor=$color_celda>$nombre</td><td bgcolor=$color_celda>$tema</td><td align='center' bgcolor=$color_celda_calificacion>$calificacion</td><td bgcolor=$color_celda>$locacion </td></tr>";
				$result->moveNext();
			}
			$val=implode(",", $ecalificacion);
?>
	<tr>
		<td colspan="4" align="center" bgcolor="<?=$bgcolor3?>">
			<input type="submit" name="guardar" value="Guardar calificaciones" onclick='return control_datos(new Array(<?=$val?>));'>
		</td>
	</tr>
	</table>
	<table align="center" width="60%" cellpadding="1" cellspacing="0" border="1" bgcolor="White">
		<tr>
			<td width="10" height="10" bgcolor="#fb471e">
			</td>
			<td>
				Se debe recapacitar sobre este tema.
			</td>
		</tr>
		<tr>
			<td width="10" height="10" bgcolor="#1eaa19">
			</td>
			<td>
				Demuestra haber adquirido los conocimientos de la capacitaci&oacute;n.
			</td>
		</tr>
	</table>
</form>
<?
fin_pagina("false");
?>
<script>
	function msg(mensaje){
		alert(mensaje);
		return true;
	}
</script>