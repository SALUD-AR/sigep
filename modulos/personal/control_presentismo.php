<?php
//Autor: Gabriel Gaudina
/*
	$Author: mari $
	$Revision: 1.18 $
	$Date: 2006/06/06 17:06:12 $
*/

	require "../../config.php";
	require_once "gutils.php";
	variables_form_busqueda("stats0");

	echo $html_header;
	$orden = array(
		"default" => "1",
		"1" => "apellido",
		"2" => "nombre"
		//"3" => "ubicacion"
//		"4" => "domicilio",
//		"5" => "tel_particular"
	);

	$filtro = array(
		"u.apellido" => "Apellido",
		"u.nombre" => "Nombre"
		//"ubicacion" => "Ubicaci&oacute;n"
	);
	$itemspp = 50;

	if (!$cmd) $cmd="hoy";

	echo "<form action='control_presentismo.php' method='post'>";
	if($parametros['accion']!=""){
		Aviso($parametros['accion']);
	} 
	if (($_POST["stat_bsas"])||($_POST["stat_sl"])||($parametros["ubicacion"])){
		if ($parametros["ubicacion"]) $ubicacion=$parametros["ubicacion"];
		else 
			if ($_POST["stat_bsas"]) $ubicacion=$_POST["stat_bsas"];
			else $ubicacion=$_POST["stat_sl"];
	}
	
	if ($_POST["ocultar"]){
		$chk=PostvartoArray("ocultar_");
        if ($chk)   { 
		 $list=implode(",",$chk);
         $sql="update sistema.usuarios set visible=0 where id_usuario in ($list)";
		 sql($sql,"$sql") or fin_pagina();
		}
		/*for ($i=0; $i<$_POST["cantidad"]; $i++)	
			if (($_POST["ch_ocultar_$i"])&&($_POST["usuario_$i"])) sql("update sistema.usuarios set visible=0 where id_usuario=".$_POST["usuario_$i"], "c45") or fin_pagina();*/
	}

	$mes_resumen=$parametros["mes_resumen"] or $mes_resumen=$_POST["mes_resumen"] or $mes_resumen=date("m");
	$agno_resumen=$parametros["agno_resumen"] or $agno_resumen=$_POST["agno_resumen"] or $agno_resumen=date("Y");
	
	if (($_POST["stat_bsas"])||($_POST["stat_sl"])||($_POST["actualizar"])||($parametros["dia_seleccionado"])){
		$mes_resumen=$_POST["mes_resumen"] or $mes_resumen=$parametros["mes_resumen"] or $mes_resumen=date("m");
		$agno_resumen=$_POST["agno_resumen"] or $agno_resumen=$parametros["agno_resumen"] or $agno_resumen=date("Y");
		$dia_seleccionado=$parametros["dia_seleccionado"] or $dia_seleccionado=date("d");
	}
	?>
<script>
var img_ext='<?=$img_ext='../../imagenes/right2.gif' ?>';//imagen extendido
var img_cont='<?=$img_cont='../../imagenes/left2.gif' ?>';//imagen contraido
function muestra_tabla(obj_tabla, nro){
	oimg=eval("document.all.imagen_"+nro);//objeto tipo IMG
 	if (obj_tabla.style.display=='none'){
 		obj_tabla.style.display='inline';
    oimg.show=0;
    oimg.src=img_cont;
    if (nro==1) oimg.title='Ocultar columnas';
		else oimg.title='Imagen no identificada';
	}else{
		obj_tabla.style.display='none';
    oimg.show=1;
		oimg.src=img_ext;
		if (nro==1) oimg.title='Mostrar columnas';
		else oimg.title='Imagen no identificada';
  } 
}
</script>	
	<table cellspacing=2 cellpadding=5 border=0 bgcolor='<? echo $bgcolor3; ?>' width=95% align=center>
		<tr>
			<td align=center>
				<table cellspacing=2 cellpadding=5 border=0 bgcolor="<? echo $bgcolor3 ?>" width=70% align="center">
					<tr>
						<td align=center>
							<?
								//definicion de parámetros de listado
								$sql_tmp="select id_usuario, id_legajo, u.nombre, u.apellido, u.login, d.nombre as ubicacion 
								          from sistema.usuarios u 
								          left join permisos.phpss_account p on (u.login=p.username)
								          left join personal.legajos l using (id_usuario)
									      left join personal.distrito d on(pcia_ubicacion=id_distrito)"; 
								$where_tmp="(visible=1 and active='true')";
								if ($ubicacion){
									$where_tmp.="and (d.nombre ilike '%".$ubicacion."%') ";
								}else{
									if (($_POST["sel_ubicacion"])&&($_POST["sel_ubicacion"]!="Todas")){
										$where_tmp.="and (d.nombre ilike '%".$_POST["sel_ubicacion"]."%')";
									}elseif (!$_POST["sel_ubicacion"]){
										$where_tmp.="and(d.nombre ilike '%San Luis%')";
									}else{
										$where_tmp.="and(1=1)";
									}
								}
								$itemspp=100;
								list($sql, $total_leg, $link, $up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");
								$listado_usuarios = sql($sql) or fin_pagina();
								while (!$listado_usuarios->EOF){
									$lista_nombres[]="<b>".$listado_usuarios->fields["apellido"]."</b>, ".$listado_usuarios->fields["nombre"]. " (<i>".$listado_usuarios->fields["login"]."</i>)";
									if ($listado_usuarios->fields["id_usuario"]) $lista_id[]=$listado_usuarios->fields["id_usuario"];
									else $lista_id[]="-1";
									if ($listado_usuarios->fields["id_legajo"]) $lista_id2[]=$listado_usuarios->fields["id_legajo"];
									else $lista_id2[]="-1";
									$listado_usuarios->moveNext();
								}
							
							if (!$ubicacion) $ubic=$_POST['sel_ubicacion'] or $ubic=$parametros['ubicacion'] ;
				                else $ubic=$ubicacion;
				                
				                ?>
							&nbsp;&nbsp;
							<b>Ubicaci&oacute;n:<b>&nbsp;
							<select id="sel_ubicacion" name="sel_ubicacion">
							  <option value='San Luis' <?if ($ubic=='San Luis') echo 'selected'?>>San Luis </option>
		                      <option value='Todas' <?if ($ubic=='Todas') echo 'selected'?> >Todas </option>
							</select>
							<input type=submit name=buscar value='Buscar'>
				            <?
				           
				            $link_reporte=encode_link('reporte_asistencia_mes.php',array("mes_resumen"=>$mes_resumen,"agno_resumen"=>$ano_resumen,"ubicacion"=>$ubic))
				            ?>
							<input type='button' name="control" value="Control de Asistencia" onclick="window.open('<?=$link_reporte?>','','')"> 
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table border=0 width=95% cellspacing=2 cellpadding=3 bgcolor='<? echo $bgcolor3; ?>' align=center>
		<tr>			
			<input type="submit" name="stat_sl" value="San Luis"></input>
		</tr>
<?
	if (($_POST["stat_bsas"])||($_POST["stat_sl"])||($_POST["actualizar"])||($parametros["dia_seleccionado"])){
?>
		<table border=1 width="95%" cellspacing=2 cellpadding=3 bgcolor='<? echo $bgcolor3 ?>' align=center>
		<tr>
			<td>
				<table border=0 width="100%" cellspacing=0 cellpadding=3 bgcolor='<? echo $bgcolor3 ?>' align=center>
					<tr>
						<td align="center">Resumen de asistencia del mes de&nbsp;
							<?
								g_draw_select("mes_resumen", $mes_resumen);
								echo "de&nbsp;";
								g_draw_range_select("agno_resumen", $agno_resumen, 1995, date("Y"));
							?>
							<input type="submit" name="actualizar" value="Actualizar datos">
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<table border="1" width="95%" cellpadding="1" bgcolor="White" align="center" cellspacing="1">
				<tr>
					<?
						//colores de la estadística de asistencia
						$falto="#FFCCCC";
						$asistio="#CCFFCC";
						$domingo="#000000";
						$no_salio="#EE00EE";
						$mostrando="#FFFFFF";//dia_seleccionado
						$hoy_asistencia="#99EE99";//día actual
						$fuera_de_hora="#AAAAAA";//fuera de horario de trabajo
						$dentro_de_hora="#FFFFFF";//en horario de trabajo
						//consulta para obtener los registros de un mes y agno particular
						$asist = "select *
							from (
								select fecha, hora_entra, hora_sale, asistencia.id_usuario, asistencia.id_legajo, 
									case when usuarios.nombre is null then legajos.nombre else usuarios.nombre end as nombre, 
									case when usuarios.apellido is null then legajos.apellido else usuarios.apellido end as apellido, 
									distrito.nombre as provincia 
								from personal.asistencia
									left join sistema.usuarios on(usuarios.id_usuario=asistencia.id_usuario and usuarios.visible=1) 
									left join personal.legajos using (id_legajo) 
									left join personal.distrito on (pcia_ubicacion=id_distrito)
								)as tmp0 where provincia ilike '%".$ubicacion."%'and fecha ilike '".$agno_resumen."-";
						if (strlen($mes_resumen)==1) $asist.="0".$mes_resumen."-%'";
						else $asist.=$mes_resumen."-%'";
						//echo "<bR> asist ".$asist;
						$result = sql($asist) or fin_pagina();
						$iii=1;
						//cuántos días tiene el mes y agno pedidos
						$asistencia[]=date("t", mktime(0, 0, 0, intval($mes_resumen), 1, intval($agno_resumen)));
						//"11" si vino el día $iii, sino "00"
						while ($iii<=$asistencia[0]){
							if ($iii==intval(substr($result->fields["fecha"], 8, 2))){
								$asistencia[]="11";
								$result->MoveNext();
							}else $asistencia[]="00";
							$iii++;
						}
						//////////////////////////////////////////////////
						$colorDia;
						for ($i=1; $i<count($asistencia); $i++){
							//nombre corto del día
							$ddd=date_spa("D", $agno_resumen."-".$mes_resumen."-".$i);
							//is $ddd es domingo marcarlo ("22") para no contar falta ese día
							if ($ddd=="Dom") $asistencia[$i]="22";
							if ($i==$dia_seleccionado) $asistencia[$i]="44";
							if (($mes_resumen==date("m"))&&($agno_resumen==date("Y"))&&($i==intval(date("d")))) 
								echo "<td bgcolor='$hoy_asistencia'><font color='#000000'>".$ddd."</td>";
							else echo "<td id=mo>".$ddd."</td>";
						}
						//fin fila de días
					?>
				</tr>
				<tr>
					<?
						//comienzo fila de nro de días (colores)
						for ($i=1; $i<count($asistencia); $i++){
							if ($asistencia[$i]=="11") $colorDia=$asistio;//verde
							elseif ($asistencia[$i]=="22") $colorDia=$domingo;//negro
							elseif ($asistencia[$i]=="00") $colorDia=$falto;//rojo
							elseif ($asistencia[$i]=="44") $colorDia=$mostrando;//verde fuerte día seleccionado
							echo "<td align='center' bgcolor='$colorDia' style='cursor:hand;' onclick=\"document.location.href='".encode_link("control_presentismo.php",array("ubicacion"=>$ubicacion, "dia_seleccionado"=>$i, "mes_resumen"=>$mes_resumen, "agno_resumen"=>$agno_resumen))."'\"><font color=".contraste($colorDia, '#0000FF', '#FFFFFF')."><b>$i</b><font></td>";
						}
					?>
				</tr>
			</table>
		</tr>
		<tr>
			<?
				if ($dia_seleccionado!=""){//se eligió un día particular
					//48=24 hs con fracciones de 30'
					$hora_de_inicio_de_act=g_timeToSec("00:00:00");//desde cuándo tener en cuenta
					$hora_de_fin_de_act=g_timeToSec("24:00:00");//hasta q hora
					$periodo_de_conteo=g_timeToSec("00:30:00");//fracción a considerar
					$incremento_etiquetas=g_timeToSec("01:00:00");//intervalo de etiquetas blancas (horas)
					$columna_pivot=14;
					$total_columnas=floor(($hora_de_fin_de_act - $hora_de_inicio_de_act) / $periodo_de_conteo);
					//mostrado de fila de horario
				
					for ($counter=0; $counter<count($lista_id); $counter++){
						$asist = "select *
							from (
								select fecha, hora_entra, hora_sale, asistencia.id_usuario, asistencia.id_legajo, 
									case when usuarios.nombre is null then legajos.nombre else usuarios.nombre end as nombre, 
									case when usuarios.apellido is null then legajos.apellido else usuarios.apellido end as apellido, 
									distrito.nombre as provincia 
								from personal.asistencia
									left join sistema.usuarios on(usuarios.id_usuario=asistencia.id_usuario and usuarios.visible=1) 
									left join personal.legajos using (id_legajo) 
									left join personal.distrito on (pcia_ubicacion=id_distrito)
								)as tmp0
							where provincia ilike '%".$ubicacion."%' and (id_usuario=".$lista_id[$counter]." or id_legajo=".$lista_id2[$counter].") and fecha ilike '".$agno_resumen."-";
						//si mes = X y no a 0X o 1X
						if (strlen($mes_resumen)==1) $asist.="0".$mes_resumen."-";
						else $asist.=$mes_resumen."-";
						//si dia = X y no a 0X o 1X o 2X o 3X
						if (strlen($dia_seleccionado)==1) $asist.="0".$dia_seleccionado."'";
						else $asist.=$dia_seleccionado."'";
						//consulta
						$result = sql($asist) or fin_pagina();
						//marca con "11" los intervalos horarios en los que el empleado se encontraba trabajando
						//copiado info
						while (!$result->EOF){
							$horas[$counter][]=$result->fields["hora_entra"]."-".$result->fields["hora_sale"];
							$result->moveNext();
						}
						for ($ii=0; $ii<$total_columnas; $ii++) $registros[$counter][]="00";
						for ($ii=0, $jj=$hora_de_inicio_de_act; $ii<$total_columnas; $jj+=$periodo_de_conteo, $ii++){
							if ($ii==0) $jj+=g_timeToSec("00:15:00");
							for ($kk=0; $kk<count($horas[$counter]); $kk++){
								if (($jj> g_timeToSec(substr($horas[$counter][$kk], 0, 8)))&&($jj< g_timeToSec(substr($horas[$counter][$kk], 9, 8)))) $registros[$counter][$ii]="11";
								elseif (($jj>= g_timeToSec(substr($horas[$counter][$kk], 0, 8)))&&(substr($horas[$counter][$kk], 9, 8)=="")) $registros[$counter][$ii]="33";
							}
						}
					}
					?>
				<table cellspacing="0" width="50%" cellpadding="0" bgcolor="White" align="center" id="tabla_apellidos" cellspacing="1">
					<tr bgcolor="<?=$bgcolor3?>">
						<td colspan="3" align="left"><?=$total_leg?> usuarios</td>
						<td align="right"><?=$link?></td>
					</tr>
					<tr>
						<td nowrap>
							<table border="1" width="10%" cellpadding="0" bgcolor="White" align="center" id="tabla_apellidos" cellspacing="1">
								<tr>
									<td>
										Apellido
									</td>
								</tr>
						<?
						for ($counter=0; $counter<count($lista_id); $counter++){
							$ref = encode_link("asistencia_stats.php",array("dia_seleccionado"=>$dia_seleccionado, "id_usuario"=>$lista_id[$counter], "id_legajo"=>$lista_id2[$counter], "pagina"=>"asistencia_stats"));
							tr_tag($ref);
							//$apel=substr($lista_nombres[$counter], 0, strpos($lista_nombres[$counter], ","));
							//$apel.="<font color=$bgcolor2>.</font>";
							//echo "<td align=center bgcolor=$bgcolor2 nowrap>".$apel."</td></tr>";
							echo "<td id='mo' nowrap>".$lista_nombres[$counter]."</td></tr>";
						}
?>
							</table>
						</td>
						<td style="cursor:hand;" bgcolor='<?=$bgcolor3?>' onclick="muestra_tabla(document.all.tabla_expandible,1);">
							<table border="0" width="10%" cellpadding="0" align="center" cellspacing="0">
								<tr>
									<td>
										<img id="imagen_1" src="<?=$img_ext?>" border=0 title="Mostrar columnas" align="left">
									</td>
								</tr>
							</table>
						</td>
						<td>
							<table border="1" width="10%" cellpadding="0" bgcolor=<?=$bgcolor3?> align="center" id="tabla_expandible" style="display:none" cellspacing="1">
								<tr>
<?
					for ($i=$hora_de_inicio_de_act; $i<g_timeToSec(($columna_pivot/2).":00:00"); $i+=$incremento_etiquetas){
						if (($hr_entra)&&($hr_sale)&&(($i<g_timeToSec($hr_entra))||($i>=g_timeToSec($hr_sale)))) $hora_habil=$fuera_de_hora;
						else $hora_habil=$dentro_de_hora;
						echo "<td width='10%' colspan= ".floor($incremento_etiquetas/$periodo_de_conteo)." bgcolor='$hora_habil' align='center'><span>".substr(g_secToTime($i), 0, 5)."</span></td>\n";
					}
					echo "</tr>";
					for ($counter=0; $counter<count($lista_id); $counter++){
						//etiqueta verde si "estaba", roja si "no estaba"
						for ($ii=0; $ii<$columna_pivot; $ii++){
							if ($registros[$counter][$ii]=="11") echo "<td width='15' height='15' align='center' bgcolor='$asistio'><font color='$asistio'>.</font></td>";
							elseif ($registros[$counter][$ii]=="33") echo "<td width='15' height='15' align='center' bgcolor='$no_salio'><font color='$no_salio'>.</font></td>";
							else echo "<td width='15' height='15' align='center' bgcolor='$falto'><font color='$falto'>.</font></td>";
						}
						echo "</tr>";
					}
					echo "</table>";//fin fila colores
?>
					</td>
					<td>
							<table border="1" width="10%" cellpadding="0" bgcolor="White" align="center" id="tabla_fija" cellspacing="1">
								<tr>
<?
					for ($i=g_timeToSec(($columna_pivot/2).":00:00"); $i<$hora_de_fin_de_act; $i+=$incremento_etiquetas){
						if (($hr_entra)&&($hr_sale)&&(($i<g_timeToSec($hr_entra))||($i>=g_timeToSec($hr_sale)))) $hora_habil=$fuera_de_hora;
						else $hora_habil=$dentro_de_hora;
						echo "<td id='".substr(g_secToTime($i), 0, 5)."' colspan= ".floor($incremento_etiquetas/$periodo_de_conteo)." bgcolor='$hora_habil' align='center'><span>".substr(g_secToTime($i), 0, 5)."</span></td>\n";
					}
					echo "</tr>";
					for ($counter=0; $counter<count($lista_id); $counter++){
						//etiqueta verde si "estaba", roja si "no estaba"
						for ($ii=$columna_pivot; $ii<count($registros[$counter]); $ii++){
							if ($registros[$counter][$ii]=="11") $color_td=$asistio;
							elseif ($registros[$counter][$ii]=="33") $color_td=$no_salio;
							else $color_td=$falto;
							
							echo "<td width='15' height='15' align='center' bgcolor='$color_td'><font color='$color_td'>&nbsp;</font></td>";
							
							
						}
						echo "</tr>";
					}
					echo "</table>";//fin fila colores
				}
			?>
				</td>
			</tr>
		</table>
				<table border=1 width="80%" cellspacing=0 cellpadding=0 bgcolor='#FFFFFF' align=center>
					<tr>
						<td colspan="11" align="left">&nbsp;Referencia de colores:</td>
					</tr>
					<tr>
						<td width="15" height="15" align='center' bgcolor='<? echo $no_salio; ?>'><font color='<? echo $no_salio; ?>'>.</td>
						<td width="23%" align="left" >&nbsp;No ha registrado la salida</td>
						<td></td>
						<td width="15" height="15" align='center' bgcolor='<? echo $falto; ?>'><font color='<? echo $falto; ?>'>.</td>
						<td width="23%" align="left" >&nbsp;Ausente</td>
						<td></td>
						<td width="15" height="15" align='center' bgcolor='<? echo $asistio; ?>'><font color='<? echo $asistio; ?>'>.</td>
						<td width="23%" align="left" >&nbsp;Presente</td>
						<td></td>
						<td width="15" height="15" align='center' bgcolor='<? echo $domingo; ?>'><font color='<? echo $domingo; ?>'>.</td>
						<td width="23%" align="left" >&nbsp;Domingo</td>
					</tr>
					<tr>
						<td width="15" height="15" align='center' bgcolor='<? echo $mostrando; ?>'><font color='<? echo $mostrando; ?>'>.</td>
						<td align="left" >&nbsp;D&iacute;a seleccionado</td>
						<td></td>
						<td width="15" height="15" align='center' bgcolor='<? echo $hoy_asistencia; ?>'><font color='<? echo $hoy_asistencia; ?>'>.</td>
						<td align="left" >&nbsp;D&iacute;a corriente</td>
						<td></td>
						<td width="15" height="15" align='center' bgcolor='<? echo $fuera_de_hora; ?>'><font color='<? echo $fuera_de_hora; ?>'>.</td>
						<td align="left" >&nbsp;Fuera de horario de trabajo</td>
						<td></td>
						<td width="15" height="15" align='center' bgcolor='<? echo $dentro_de_hora; ?>'><font color='<? echo $dentro_de_hora; ?>'>.</td>
						<td align="left" >&nbsp;Horario de trabajo</td>
					</tr>
				</table>
		</tr>
	</table>
<?
	}else {
?>
	<table  class="bordessininferior" width="95%" align="center" cellpadding="3" cellspacing='0'>
		<tr>
			<td width=30% id="ma" align=left colspan="2"><b>Total:</b> <? echo $total_leg." "; ?>legajo/s.
			</td>
		</tr>
		<?/*<tr>
			<td align=right id=mo>
				<a id=mo href='"<?echo encode_link("listado_legajos.php",array("sort"=>"1","up"=>$up)); ?>"'>Apellido, </a>
				<a id=mo href='"<? echo encode_link("listado_legajos.php",array("sort"=>"2","up"=>$up)); ?>"'>Nombre (login)</a>
			</td>
			<td width="1%" id="mo">Ocultar</td>
		</tr>*/?>
		</table>
		<table width='95%' class="bordessinsuperior" cellspacing='2' align="center">
		<tr id=mo>
		   <td>Apellido, Nombre (login)</td>
		   <td width="2%">Ocultar</td>
		</tr>
	<?   

			for ($i=0; $i<count($lista_nombres); $i++){
				$ref = encode_link("asistencia_stats.php",array("id_usuario"=>$lista_id[$i], "id_legajo"=>$lista_id2[$i],"pagina"=>"asistencia_stats"));
				if (condition) {
					
				}
				?>
					<tr <?=atrib_tr();?>>
					<a href="<?=$ref?>">
						<td align=left ><?=$lista_nombres[$i]?></td>
					</a>
					<td align="center" >
						<input type='checkbox' class="estilos_check" value='<?=$lista_id[$i]?>' name='ocultar_<?=$lista_id[$i]?>'>
					</td>
					<?/*<td align="center" >
						<input type="checkbox" class="estilos_check" name="ch_ocultar_<?=$i?>">
						<input type="hidden" name="usuario_<?=$i?>" value="<?=$lista_id[$i]?>">
					</td>*/?>
				</tr>
				<?
			}
			
			 if ($ubic=="") $ubic_personal="San Luis";
			 else $ubic_personal=$ubic;	
			    		
			$link_ocultos=encode_link("mostrar_ocultos.php",array("ubicacion"=>$ubic_personal));
			?>
				<tr>
					<td colspan="2" align="center">
						<input type="hidden" name="cantidad" value="<?=$i?>">
						<input type="submit" name="ocultar" value="Ocultar seleccionados">
						<input type="button" name="ver_ocultos" value="Agregar ocultos" onclick="window.open('<?=$link_ocultos?>')">
					</td>
				</tr>
			</table>
			<?
	}
			echo "</form>";
fin_pagina();
?>