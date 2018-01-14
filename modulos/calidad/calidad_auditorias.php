<?php
/*
AUTOR: Gabriel
MODIFICADO POR:
$Author: ferni $
$Revision: 1.4 $
$Date: 2006/11/27 20:22:52 $
*/
	require("../../config.php");
	cargar_calendario();
	variables_form_busqueda("auditorias", array());
	if ($cmd == ""){
		$cmd="porAutorizar";
		$_ses_capacit["cmd"]=$cmd;
  	phpss_svars_set("_ses_auditorias", $_ses_auditorias);
	}
	//////////////////////////////////////////////////////////////////////////////
	if ($_POST["sel_periodo"]){
		$fecha_desde=$_POST["fecha_desde"];
		$fecha_hasta=$_POST["fecha_hasta"];
		if (($fecha_desde)&&($fecha_hasta)){
			$where_tmp=" (
				((fecha_desde>='".Fecha_db($fecha_desde)."')and(fecha_hasta<='".Fecha_db($fecha_hasta)."'))
			or
				((('".Fecha_db($fecha_desde)."'>=fecha_desde)and('".Fecha_db($fecha_desde)."'<=fecha_hasta))or(('".Fecha_db($fecha_hasta)."'<=fecha_hasta)and('".Fecha_db($fecha_hasta)."'>=fecha_desde)))
			)and";
		}elseif ($fecha_desde){
			$where_tmp=" ((fecha_desde>=".Fecha_db($fecha_desde).")or(fecha_hasta>=".Fecha_db($fecha_desde)."))and";
		}elseif ($fecha_hasta){
			$where_tmp=" ((fecha_desde<=".Fecha_db($fecha_hasta).")or(fecha_hasta<=".Fecha_db($fecha_hasta)."))and";
		}
	}
	
	$datos_barra = array(
		array("descripcion"=> "Por Autorizar", "cmd"=> "porAutorizar"),
		array("descripcion"=> "Pendientes", "cmd"=> "pendientes"),
		array("descripcion"=> "Historial", "cmd"=> "historial")
	);
	$orden = array(
		"default_up"=>"0",
		"default" => "1",
		"1" => "id_auditoria_calidad",
		"2" => "titulo",
		"3" => "tipo",
		"4" => "fecha_desde",
		"5" => "fecha_hasta"
	);
	$filtro = array(        
  	"id_auditoria_calidad" => "Nro. de auditoría",
  	"titulo" => "Título",
		"tipo" => "Tipo",
		"fecha_desde" => "Fecha de inicio",
		"fecha_hasta" => "Fecha de finalización",
		"d.nombre" => "Planta"
	);     
	$sql_tmp="select titulo, id_auditoria_calidad, tipo, fecha_desde, fecha_hasta, planta, estado_auditoria--, licitaciones.unir_texto(nombre_departamento||', ') as departamentos_afectados
		from calidad.auditorias_calidad
			left join calidad.departamentos_afectados using(id_auditoria_calidad)
			left join general.departamentos_empresa using(id_departamento_empresa)
			left join personal.base_trabajo d on (planta=id_base_trabajo)";
	if ($cmd=="porAutorizar"){
		$where_tmp.="(estado_auditoria='a')";
	}elseif ($cmd=="pendientes"){
		$where_tmp.="(estado_auditoria='p')";
	}else{//historial
		$where_tmp.="(estado_auditoria='h')";
	}
	$where_tmp.="group by titulo, id_auditoria_calidad, tipo, fecha_desde, fecha_hasta, planta, estado_auditoria";
	
	if($_POST['keyword'] || $keyword) $contar="buscar";
	//////////////////////////////////////////////////////////////////////////////
	echo($html_header);
?>
	<form name="form_auditorias" method="POST" action="calidad_auditorias.php">
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
			<td colspan="6">
				<table width=100%>
					<tr id=ma>
						<td width=30% align=left><b>Total:</b> <?=$total_leg?> auditorías.</td>
						<td width=70% align=right><?=$link_pagina?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr id=mo>
 			<!--<td width="10%" nowrap><b><a href='<?//=encode_link($_SERVER["PHP_SELF"],array("sort"=>"1","up"=>$up))?>'>Nro. Auditoría</a></b></td>-->
 			<td nowrap><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"2","up"=>$up))?>'>Título</a></b></td>
 			<td nowrap width="10%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"3","up"=>$up))?>'>Tipo</a></b></td>
 			<td width="10%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"4","up"=>$up))?>'>Desde</a></b></td>
 			<td width="10%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"5","up"=>$up))?>'>Hasta</a></b></td>
 			<td width="5%">&nbsp;</td>
		</tr>
		<?
			$result=sql($sql) or fin_pagina();
			while ($fila=$result->fetchRow()){
				$ref = encode_link("calidad_auditorias_detalle.php",array_merge(array("modo"=>"modif", "pagina"=>"calidad_auditorias_detalle.php"), $fila));
				tr_tag($ref, "title='".$fila["departamentos_afectados"]."'");
		?>
			<!--<td><?//=$fila["id_auditoria_calidad"]?></td>-->
			<td><?=$fila["titulo"]?></td>
			<td><?=$fila["tipo"]?></td>
			<td><?=Fecha($fila["fecha_desde"])?></td>
			<td><?=Fecha($fila["fecha_hasta"])?></td>
			<td>
		<?
		$adj="select * from calidad.auditorias_calidad_archivos where id_auditoria_calidad=".$fila["id_auditoria_calidad"];
		$adjunto=sql($adj, "c131") or fin_pagina();
		if ($adjunto->recordcount()>0) echo "<img id='imagen_1' src='../../imagenes/files1.gif' border=0 title='El curso posee archivos subidos' align='left'>";
		echo "&nbsp";
?>
			</td>
		</tr>
		<?
			}?>
		<tr>
			<td colspan="6" align="center" bgcolor="<?=$bgcolor3?>">
				<input type="button" name="nueva_auditoria" value="Nueva Auditoría" onclick="document.location.href='<?=encode_link("calidad_auditorias_detalle.php",array("modo"=>"nuevo", "pagina"=>"calidad_auditorias_detalle.php"))?>'">
				<input type="button" name="editar_depto" value="Editar departamentos de la empresa" onclick="document.location.href='<?=encode_link("editar_piramide.php",array("pagina"=>"calidad_auditorias_detalle.php"))?>'">
			</td>
		</tr>
	</table>
</form>
<?
fin_pagina();
?>
?>
