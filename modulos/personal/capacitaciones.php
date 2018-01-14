<?php
/*
AUTOR: Gabriel
MODIFICADO POR:
$Author: gabriel $
$Revision: 1.14 $
$Date: 2006/01/17 20:48:26 $
*/
	require("../../config.php");
	require_once("gutils.php");
	//////////////////////////////////////////////////////////////////////////////
	variables_form_busqueda("capacit", array());

	if ($cmd == ""){
		$cmd=date("Y");
		$_ses_capacit["cmd"]=$cmd;
  	phpss_svars_set("_ses_capacit", $_ses_capacit);
	}
	$sel_periodo=$_POST["sel_periodo"];
	if ($sel_periodo){
		$fecha_desde=$_POST["fecha_desde"];
		$fecha_hasta=$_POST["fecha_hasta"];
	}else{
		$fecha_desde="";
		$fecha_hasta="";
	}
	//////////////////////////////////////////////////////////////////////////////
	$datos_barra=array();
	for ($i=2009; $i<=date("Y")+1; $i++){
		$datos_barra[]=array("descripcion"=> $i, "cmd"=> $i);
	}
	$orden = array(
		"default_up"=>"0",
		"default" => "2",
		"1" => "tema",
		"2" => "dictado_desde",
		"3" => "d.nombre"
	);
	$filtro = array(        
  	"tema" => "Tema",
		"d.nombre" => "Lugar de dictado"
	);     
	$sql_tmp="select id_capacitacion, dictado, tema, comentarios, dictado_desde, dictado_hasta, d.nombre as locacion, dictado_por 
		from personal.capacitaciones join personal.base_trabajo d on(locacion=id_base_trabajo) ";
	$where_tmp=" (dictado_desde is null or cast (dictado_desde as text) ilike '%".$cmd."-%')";
	if (($sel_periodo)and($fecha_desde)and($fecha_hasta))
		$where_tmp1=" (dictado_desde>= '".Fecha_db($fecha_desde)."' and dictado_desde<= '".Fecha_db($fecha_hasta)."')";
	elseif (($sel_periodo)and($fecha_desde)and(!$fecha_hasta)){
		$fecha_hasta=date("d/m/Y");
		$where_tmp1=" (dictado_desde>= '".Fecha_db($fecha_desde)."' and dictado_desde<= '".Fecha_db($fecha_hasta)."')";
	}elseif (!$sel_periodo){
		$fecha_desde="";
		$fecha_hasta="";
	}
	if ($where_tmp1) $where_tmp.=" and ".$where_tmp1;
	if($_POST['keyword'] || $keyword) $contar="buscar";
	//////////////////////////////////////////////////////////////////////////////
	echo $html_header; 
	cargar_calendario();
	if($parametros['accion']!=""){ Aviso($parametros['accion']);}
	//////////////////////////////////////////////////////////////////////////////
?>
<form name="form_capacit" method="POST" action="capacitaciones.php">
	<table cellspacing=2 cellpadding=2 border=0 bgcolor=<? echo $bgcolor3 ?> width=95% align=center>
		<tr>
			<td>
				<? generar_barra_nav($datos_barra);?>  
			</td>
		</tr>
		<tr>
			<td align=center>
				<? list($sql, $total_leg, $link_pagina, $up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar"); ?>
				<input type="checkbox" name="sel_periodo" value="avanzado" <?=(($_POST["sel_periodo"])?" checked ":"")?> onclick="document.all.tabla_busqueda_avanzada.style.display=(this.checked)?'inline':'none';">&nbsp;Avanzado
				<input type=submit name=buscar value='Buscar'>
			</td>
		</tr>
		<tr>
			<td>
				<table border=0 bgcolor="<?=$bgcolor3?>" width=70% align="center" id="tabla_busqueda_avanzada" style="display:none">
					<tr>
						<td>
							<b>Entre el día:&nbsp;</b>
							<input type=text name='fecha_desde' readonly size=11 value='<?=$fecha_desde?>'>&nbsp;
							<? echo link_calendario('fecha_desde');?>
						</td>
						<td>
							<b>y el día:&nbsp;</b>
							<input type=text name='fecha_hasta' readonly size=11 value='<?=$fecha_hasta?>'>&nbsp;
							<? echo link_calendario('fecha_hasta');?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<script>
		if (document.all.sel_periodo.checked) document.all.tabla_busqueda_avanzada.style.display='inline';
	</script>
	<table align="center" width="90%" cellpadding="1" cellspacing="0" border="1">
		<tr>
			<td colspan="4">
				<table width=100%>
					<tr id=ma>
						<td width=30% align=left><b>Total:</b> <?=$total_leg?> cursos.</td>
						<td width=70% align=right><?=$link_pagina?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr id=mo>
 			<td width="60%" nowrap><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"1","up"=>$up))?>'>Tema</a></b></td>
 			<td width="25%" nowrap><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"2","up"=>$up))?>'>Per&iacute;odo de dictado</b></td>
 			<td width="10%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"3","up"=>$up))?>'>Lugar de dictado</b></td>
 			<td width="5%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"])?>'>&nbsp;</b></td>
		</tr>
		<?
			$result=sql($sql) or fin_pagina();
			while (!$result->EOF){
				$tema=$result->fields["tema"];
				$dictado_desde=Fecha($result->fields["dictado_desde"]) or $dictado_desde="?";
				$dictado_hasta=Fecha($result->fields["dictado_hasta"]) or $dictado_hasta="?";
				$id_capacitacion=$result->fields["id_capacitacion"];
				$dictado=$result->fields["dictado"];
				$comentarios=$result->fields["comentarios"];
				$locacion=$result->fields["locacion"];
				$dictado_por=$result->fields["dictado_por"];
				
				$adj="select * from archivos_capacitaciones where id_capacitacion=$id_capacitacion";
				$adjunto=sql($adj) or fin_pagina();
				
				if ($dictado!="0") $color_celda="#66FF66";
				else $color_celda=$bgcolor5;
				$ref = encode_link("capacitaciones_detalle.php",array("modo"=>"modif", "locacion"=>$locacion, "dictado"=>$dictado, "dictado_por"=>$dictado_por, "comentarios"=>$comentarios, "dictado_desde"=>$dictado_desde, "dictado_hasta"=>$dictado_hasta , "id_capacitacion"=>$id_capacitacion, "tema"=>$tema, "pagina"=>"capacitaciones_detalle.php"));
				tr_tag($ref);
				echo "<td bgcolor=$color_celda>$tema</td><td bgcolor=$color_celda>$dictado_desde a $dictado_hasta</td><td nowrap bgcolor=$color_celda>$locacion</td>";
				echo "<td width='5%' bgcolor=$color_celda>";
				if ($adjunto->recordcount()>0) echo "<img id='imagen_1' src='../../imagenes/files1.gif' border=0 title='El curso posee archivos subidos' align='left'>";
				echo "&nbsp</td></tr>";
				$result->moveNext();
			}
		?>
	<tr>
		<td colspan="4" align="center" bgcolor="<?=$bgcolor3?>">
			<input type="button" name="nuevo_curso" value="Nuevo Curso" onclick="document.location.href='<?=encode_link("capacitaciones_detalle.php",array("modo"=>"nuevo", "pagina"=>"capacitaciones_detalle.php"))?>'">
		</td>
	</tr>
	
	</table>
	<br>
	<table align="center" border="1" bordercolor='black' bgcolor="White" width="50%" cellpadding="0" cellspacing="0">
 		<tr>
 			<td bordercolor="white"><b>Colores de referencia:</b></td>
 		</tr>
 		<tr>
 			<td bordercolor='white'>
 				<table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
   				<tr>
						<td width=15 bgcolor="#66FF66" bordercolor='#000000' height=15>&nbsp;</td>
	    			<td>Curso ya dictado</td>
 					</tr>
    		</table>
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