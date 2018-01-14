<?php
//Autor: Gabriel Gaudina
/*
	$Author: mari $
	$Revision: 1.16 $
	$Date: 2006/06/06 17:06:49 $
*/	

	require("../../config.php");
	require_once("gutils.php");
	echo $html_header;
	//inicialización de variables y estructuras
	cargar_calendario();
	$id_legajo=$_ses_stats["id_legajo"] or $id_legajo=$parametros["id_legajo"];
	$id_usuario=$_ses_stats["id_usuario"] or $id_usuario=$parametros['id_usuario'];
	$var_id=array(
		"dia_seleccionado"=>"",
		"sel_periodo"=>"",
		"sel_tperiodo"=>"",
		"sel_tperiodo2"=>"",
		"fecha_desde"=>"",
		"fecha_hasta"=>"",
		"hora_desde"=>"",
		"hora_hasta"=>"",
		"hora_desde2"=>"",
		"hora_hasta2"=>"",
		"mes_resumen"=>"",
		"agno_resumen"=>"",
		"empleado"=>"",
		"hr_entra"=>"",
		"hr_sale"=>"",
		//"id_legajo"=>$_ses_stats["id_legajo"],
		"id_legajo"=>$id_legajo,
		//"id_usuario"=>$_ses_stats["id_usuario"]
		"id_usuario"=>$id_usuario
	);
	variables_form_busqueda("stats", $var_id);

	$flag0=false;
	if ((!$id_legajo) || (!$id_usuario)){		
		if (!$id_legajo) $id_legajo=$parametros["id_legajo"] or $id_legajo=$_POST["id_legajo"];
		if (!$id_usuario) $id_usuario=$parametros["id_usuario"] or $id_usuario=$_POST["id_usuario"];
		$flag0=true;
	}
	
	if ((!$empleado)&&($id_legajo!=-1)){
		$consulta="select nombre, apellido, hr_entra, hr_sale from personal.legajos where id_legajo=".$id_legajo;
		$result=sql($consulta, "No se puede conseguir el nombre de la persona") or fin_pagina();
		$empleado=$result->fields["apellido"].", ".$result->fields["nombre"]."</b></br>";
		$_ses_stats["empleado"]=$empleado;
		$flag0=true;
	}else{// ((!$empleado)&&($parametros["id_usuario"])){
		$consulta="select u.nombre, u.apellido, l.hr_entra, l.hr_sale from usuarios u left join personal.legajos l using (id_usuario) where id_usuario=".$id_usuario;
		$result=sql($consulta, "No se puede conseguir el nombre de la persona usando el id_usuario") or fin_pagina();
		$empleado=$result->fields["apellido"].", ".$result->fields["nombre"]."</b></br>";
		$_ses_stats["empleado"]=$empleado;
		$flag0=true;
	}	
	
	if ((!$hr_entra)||(!$hr_sale)){
		$result=sql("select u.nombre, u.apellido, hr_entra, hr_sale from usuarios u left join legajos using(id_usuario) where ".(($id_legajo)?"id_legajo=".$id_legajo:"false").(($id_usuario)?" or id_usuario=".$id_usuario:" or false ")) or fin_pagina();
		$hr_entra=$result->fields["hr_entra"];
		$hr_sale=$result->fields["hr_sale"];
		$_ses_stats["hr_entra"]=$hr_entra;
		$_ses_stats["hr_sale"]=$hr_sale;
		$flag0=true;
	}
	
	if (($_POST["form_busqueda"])&&($_POST["sel_periodo"]=="")&&($sel_periodo!="")){
		$sel_periodo="-1";
		$fecha_desde="";
		$fecha_hasta="";
		$_ses_stats["sel_periodo"]=$sel_periodo;
		$_ses_stats["fecha_desde"]=$fecha_desde;
		$_ses_stats["fecha_hasta"]=$fecha_hasta;
		$flag0=true;
	}
	if (($_POST["form_busqueda"])&&($_POST["sel_tperiodo"]=="")&&($sel_tperiodo!="")){
		$sel_tperiodo="-1";
		$hora_desde="";
		$hora_hasta="";
		$_ses_stats["sel_tperiodo"]=$sel_tperiodo;
		$_ses_stats["hora_desde"]=$hora_desde;
		$_ses_stats["hora_hasta"]=$hora_hasta;
		$flag0=true;
	}
	if (($_POST["form_busqueda"])&&($_POST["sel_tperiodo2"]=="")&&($sel_tperiodo2!="")){
		$sel_tperiodo2="-1";
		$hora_desde2="";
		$hora_hasta2="";
		$_ses_stats["sel_tperiodo2"]=$sel_tperiodo2;
		$_ses_stats["hora_desde2"]=$hora_desde2;
		$_ses_stats["hora_hasta2"]=$hora_hasta2;
		$flag0=true;
	}
	if (!$mes_resumen){
		$mes_resumen=date("m");
		$_ses_stats["mes_resumen"]=$mes_resumen;
		$flag0=true;
	}
	if (!$agno_resumen){
		$agno_resumen=date("Y");
		$_ses_stats["agno_resumen"]=$agno_resumen;
		$flag0=true;
	}
	if ($cmd == ""){
		$cmd="historial";
		$_ses_stats["cmd"]=$cmd;
		$flag0=true;
	}
	if (strlen($mes_resumen)==1){
		$mes_resumen="0".$mes_resumen;
		$flag0=true;
	}
	if ($flag0) phpss_svars_set("_ses_stats", $_ses_stats);

	$orden = array(
		"default_up"=>"0",
		"default" => "1",
		"1" => "fecha",
		"2" => "hora de entrada",
		"3" => "hora de salida",
	);
	$filtro = array(
		"fecha" => "Fecha",
		"hora_entra" => "Hora de entrada",
		"hora_sale" => "Hora de salida",
	);
	$datos_barra = array(
		array(
			"descripcion" => "Historial",
			"cmd"         => "historial",
			"extra" => array(
				"id_legajo" => $id_legajo,
				"id_usuario" => $id_usuario
			)
		),
		array(
			"descripcion" => "Hoy",
			"cmd"         => "hoy",
			"extra" => array(
				"id_legajo" => $id_legajo,
				"id_usuario" => $id_usuario
			)
		),
		array(
			"descripcion" => "Semana",
			"cmd"         => "semana",
			"extra" => array(
				"id_legajo" => $id_legajo,
				"id_usuario" => $id_usuario
			)
		),
		array(
			"descripcion"	=> "Mes",
			"cmd" => "mes",
			"extra" => array(
				"id_legajo" => $id_legajo,
				"id_usuario" => $id_usuario
			)
		)
	);

	///////////////////////////////////////////////////////////////////////
	if ($_POST["guardarEdicion"]){
		$renglones=$_POST["renglones"];
		for ($i=0; $i<$renglones; $i++){
			if ($_POST["ch_renglon_".$i]){
				$consulta="update personal.asistencia set 
					hora_entra=".(($_POST["hora_entrada_".$i])?"'".$_POST["hora_entrada_".$i].":".$_POST["minuto_entrada_".$i]."'":'null').", 
					hora_sale=".(($_POST["hora_salida_".$i])?"'".$_POST["hora_salida_".$i].":".$_POST["minuto_salida_".$i]."'":'null')." 
					where id_asistencia=".$_POST["id_asistencia_".$i];
				sql($consulta, "c172($i)") or fin_pagina();
			}
		}
	}
	///////////////////////////////////////////////////////////////////////
	if ((!(($sel_periodo)||($sel_tperiodo)||($sel_tperiodo2)))&&(!$keyword)){
		if ($cmd=="hoy") $fecha_lim=date("Y-m-d");
		elseif ($cmd=="semana"){
			if (date("d")<=7){
				$mes=date("m")-1;
				$dia=30-(date("d")%7);
			}else{
				$mes=date("m");
				$dia=date("d")-7;
			}
			$fecha_lim=date("Y")."-".$mes."-".$dia;
		}elseif ($cmd=="mes") $fecha_lim=date("Y")."-".date("m")."-01";
	}
	//armado de consulta de búsqueda
	//consulta entre horarios de entrada
	if (($hora_desde)and(!strchr($hora_desde, ":"))) $hora_desde.=":00";
	if (($hora_hasta)and(!strchr($hora_hasta, ":"))) $hora_hasta.=":00";
	//consulta entre horarios de salida
	if (($hora_desde2)and(!strchr($hora_desde2, ":"))) $hora_desde2.=":00";
	if (($hora_hasta2)and(!strchr($hora_hasta2, ":"))) $hora_hasta2.=":00";
	//consulta con el "edit" de búsqueda convirtiendo la fecha a formato db
	if (strstr($keyword, "/")=="/") $keyword=str_replace("/", "", $keyword);
	elseif (strstr($keyword, "/")){
		$pos1= stripos($keyword, "/");
		$keyword1=substr($keyword, 0, $pos1);
		$pos2=stripos($keyword, "/", $pos1+1);
		$keyword2=substr($keyword, $pos1+1, $pos2);
		$pos3=stripos($keyword, "/", $pos2+1);
		$keyword3=substr($keyword, $pos3+1);
		$keyword="";
		if ($keyword3) $keyword.=$keyword3;
		if ($keyword2) $keyword.="-".$keyword2;
		if ($keyword1) $keyword.="-".$keyword1;
	}
	//consulta
	$sql_tmp = "SELECT * FROM asistencia ";
	$where_tmp="(".(($id_legajo)?"id_legajo=".$id_legajo:"false").(($id_usuario)?" or id_usuario=".$id_usuario:" or false ").")";
	//agregado de condiciones
	if ($fecha_lim)	$where_tmp.=" and fecha>=' ".$fecha_lim."'";
	//"buscar registros entre"
	if (($sel_periodo)and($fecha_desde)and($fecha_hasta))
		$where_tmp1=" fecha>= '".Fecha_db($fecha_desde)."' and fecha<= '".Fecha_db($fecha_hasta)."'";
	elseif (($sel_periodo)and($fecha_desde)and(!$fecha_hasta)){
		$fecha_hasta=date("d/m/Y");
		$where_tmp1=" fecha>= '".Fecha_db($fecha_desde)."' and fecha<= '".Fecha_db($fecha_hasta)."'";
	}elseif (!$sel_periodo){
		$fecha_desde="";
		$fecha_hasta="";
	}
	//"buscar ingresos"
	if (($sel_tperiodo)and($hora_desde)and($hora_hasta))
		$where_tmp2=" hora_entra>= '".$hora_desde."' and hora_entra<= '".$hora_hasta."'";
	else{
		$hora_desde="";
		$hora_hasta="";
	}
	//"buscar egresos"
	if (($sel_tperiodo2)and($hora_desde2)and($hora_hasta2))
		$where_tmp3=" hora_sale>= '".$hora_desde2."' and hora_sale<= '".$hora_hasta2."'";
	else{
		$hora_desde2="";
		$hora_hasta2="";
	}
	//buscar por un día en particular
	//concatenado de where(s)
	//if ($where_tmp0) $where_tmp.=" and ".$where_tmp0;
	if ($where_tmp1) $where_tmp.=" and ".$where_tmp1;
	if ($where_tmp2) $where_tmp.=" and ".$where_tmp2;
	if ($where_tmp3) $where_tmp.=" and ".$where_tmp3;
	
	
	//BARRA DE BÚSQUEDA
	if($parametros['accion']!=""){ Aviso($parametros['accion']);}
	?>
<script>
function control_dato(){
	var error=0;
	var i=0;
	
	for (; (i<document.all.renglones.value)&&(error==0); i++){
		ch=eval("document.all.ch_renglon_"+i);
		if (ch.checked){
			he=eval("document.all.hora_entrada_"+i);
			me=eval("document.all.minuto_entrada_"+i);
			hs=eval("document.all.hora_salida_"+i);
			ms=eval("document.all.minuto_salida_"+i);
	
			if (he.value=="") error=4;
			else{
				if ((he.value<0)||(he.value>24)) error=1;
				if (me.value=="") me.value="00";
				else if ((me.value<0)||(me.value>59)) error=2;
			}
			if (hs.value==""){
				if (ms.value!="")	error=3;
			}else{
				/*if ((hs.value < he.value)||
					((hs.value == he.value)&&
						(ms.value <= me.value))) error=5;*/
				if ((hs.value < 0)||(hs.value > 24)) error=1;
				if (ms.value == '') ms.value="00";
				else if ((ms.value < 0)||(ms.value > 59)) error=2;
			}
		}
		switch (error){
			case 1: mensaje="La hora debe ser un número entero entre 0 y 24"; break;
			case 2: mensaje="Los minutos debe ser un número entero entre 0 y 59"; break;
			case 3: mensaje="El campo de la hora de salida no debe estar vacío si ha ingresado minutos de salida"; break;
			case 4: mensaje="El campo de la hora de entrada no debe estar vacío"; break
		//	case 5: mensaje="La hora de salida no puede ser anterior ni igual a la hora de entrada"; break;
		}
	}
	
	if (error==0) return true;
	else{
		alert("Error (renglón "+i+"): "+mensaje);
		return false;
	}
}
function alProximoInput(elmnt, content, next){
  if (content.length==elmnt.maxLength){
	  if (typeof(next)!="undefined"){
		  next.focus();
		}else document.all.guardar.focus();	
	}
}
</script>
<form action='asistencia_stats.php' method='post' name='form1'>
	<input type="hidden" name="id_legajo" value=<?=$id_legajo?>>
	<input type="hidden" name="id_usuario" value=<?=$id_usuario?>>
		<? 
		//tabla que muestra el nombre de empleado ?>
		
		
	<table border=1 width=95% cellspacing=2 cellpadding=3 bgcolor='<?=$bgcolor3?>' align=center>
		<tr>
			<td align=center id=mo>
				Estadística de asistencia de <?=$empleado?>
			</td>
		</tr>
	</table>
	<? //comienzo formulario (tabla) de búsqueda ?>
	<table cellspacing=2 cellpadding=5 border=0 bgcolor=<?=$bgcolor3?> width=95% align=center>
		<tr>
			<td>
				<? generar_barra_nav($datos_barra);?>  
			</td>
		</tr>
		<?
		$link_reporte=encode_link('reporte_asistencia.php',array("id_usuario"=>$id_usuario));	
		?>
		<tr>
			<td align=center>
				<? list($sql,$total_reg, $link_pagina, $up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");
				?>
				
				<input type="checkbox" name="buscarAvanzado" <?=(($_POST["buscarAvanzado"])?" checked ":"")?> onclick="if (this.checked) document.getElementById('buscarAv').style.display='inline'; else document.getElementById('buscarAv').style.display='none';">
				Avanzado&nbsp;<input type=submit name=buscar value='Buscar'>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input type='button' name="reporte" value="Reporte de Asistencia" onclick="window.open('<?=$link_reporte?>','','')">
			</td>
		</tr>
		<tr>
			<td>
				<table class="bordes" bgcolor="<?=$bgcolor3?>" width=70% align="center" id="buscarAv" style="display:<?=(($_POST["buscarAvanzado"])?"'inline'":"'none'")?>">
					<tr>
						<td>
							<? 	
								if ($sel_periodo=="1") echo '<input type="checkbox" name="sel_periodo" value="1" checked>';
								else echo '<input type="checkbox" name="sel_periodo" value="1">'; 
							?>
							&nbsp;
							<b>Buscar registros entre</b>
						</td>
						<td>
							<b>el día:&nbsp;</b>
							<?
								if ($fecha_desde!="") echo "<input type=text name='fecha_desde' readonly size=11 value='".$fecha_desde."'>&nbsp;";
								else echo "<input type=text name='fecha_desde' size=11 readonly>&nbsp;";
								echo link_calendario('fecha_desde');
							?>
						</td>
						<td>
							<b>y el día:&nbsp;</b>
							<?
								if ($fecha_hasta!="") echo "<input type=text name='fecha_hasta' size=11 readonly value='".$fecha_hasta."'>&nbsp;";
								else echo "<input type=text name='fecha_hasta' size=11 readonly>&nbsp;";
								echo link_calendario('fecha_hasta');
							?>
						</td>
					</tr>
					<tr>
						<td>
							<?
								if ($sel_tperiodo=="1") echo '<input type="checkbox" name="sel_tperiodo" value="1" checked>';
								else echo '<input type="checkbox" name="sel_tperiodo" value="1">'; 
							?>
							&nbsp;
							<b>Buscar ingresos</b>
						</td>
						<td>
							<b>entre la hora:&nbsp;</b>
							<?
								if ($hora_desde!="") echo "<input type=text name='hora_desde' size=11 value='".$hora_desde."'>";
								else echo "<input type=text name='hora_desde' size=11>";
							?>
						</td>
						<td>
							<b>y la hora:&nbsp;</b>
							<?
								if ($hora_hasta!="") echo "<input type=text name='hora_hasta' size=11 value='".$hora_hasta."'>";
								else echo "<input type=text size=11 name='hora_hasta'>";
							?>
						</td>
					</tr>
					<tr>
						<td>
							<? 
								if ($sel_tperiodo2=="1") echo '<input type="checkbox" name="sel_tperiodo2" value="1" checked>&nbsp;';
								else echo '<input type="checkbox" name="sel_tperiodo2" value="1">&nbsp;'; 
							?>
							<b>Buscar egresos</b>
						</td>
						<td>
							<b>entre la hora:&nbsp;</b>
							<?
								if ($hora_desde2!="") echo "<input type=text name='hora_desde2' size=11 value='".$hora_desde2."'>";
								else echo "<input type=text size=11 name='hora_desde2'>";
							?>
						</td>
						<td>
							<b>y la hora:&nbsp;</b>
							<?
								if ($hora_hasta2!="") echo "<input type=text name='hora_hasta2' size=11 value='".$hora_hasta2."'>";
								else echo "<input type=text size=11 name='hora_hasta2'>";
							?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<? 
		//fin formulario (tabla) de búsqueda 
		//comienzo tabla de resultados y estadísticas
	?>
	<table border=1 width="95%" cellspacing=2 cellpadding=3 bgcolor='<?=$bgcolor3?>' align=center>
		<tr>
			<td>
				<table border=0 width="100%" cellspacing=0 cellpadding=3 bgcolor='<?=$bgcolor3?>' align=center>
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
			<table border="1" width="95%" cellpadding="1" bgcolor="White" align="center">
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
						$asist = "select DISTINCT fecha from asistencia where (".(($id_legajo)?"id_legajo=".$id_legajo:"false").(($id_usuario)?" or id_usuario=".$id_usuario:" or false ").") and cast (fecha as text) ilike '%".$agno_resumen."-";
						if (strlen($mes_resumen)==1) $asist.="0".$mes_resumen."-%'";
						else $asist.=$mes_resumen."-%'";
						
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
							echo "<td align='center' bgcolor='$colorDia' style='cursor:hand;' onclick=\"document.location.href='".encode_link("asistencia_stats.php",array("dia_seleccionado"=>$i, "id_usuario"=>$id_usuario, "id_legajo"=>$id_legajo, "mes_resumen"=>$mes_resumen, "agno_resumen"=>$agno_resumen))."'\"><font color=".contraste($colorDia, '#0000FF', '#FFFFFF')."><b>$i</b><font></td>";
						}
					?>
				</tr>
			</table>
		</tr>
		<tr>
			<?
				if ($dia_seleccionado!=""){//se eligió un día particular
			?>
			<table border="1" width="95%" cellpadding="0" bgcolor="White" align="center">
			<tr>
				<?
							$asist = "select * from asistencia where (".(($id_legajo)?"id_legajo=".$id_legajo:"false").(($id_usuario)?" or id_usuario=".$id_usuario:" or false ").") and fecha='".$agno_resumen."-";
							//si mes = X y no a 0X o 1X
							if (strlen($mes_resumen)==1) $asist.="0".$mes_resumen."-";
							else $asist.=$mes_resumen."-";
							//si dia = X y no a 0X o 1X o 2X o 3X
							if (strlen($dia_seleccionado)==1) $asist.="0".$dia_seleccionado."'";
							else $asist.=$dia_seleccionado."'";
							//consulta
							$result = sql($asist) or fin_pagina();
							//copiado info
							while (!$result->EOF){
								$horas[]=$result->fields["hora_entra"]."-".$result->fields["hora_sale"];
								$result->moveNext();
							}
							//inicialización arreglo de horas (in)asistidas
							//48=24 hs con fracciones de 30'
							$hora_de_inicio_de_act=g_timeToSec("00:00:00");//desde cuándo tener en cuenta
							$hora_de_fin_de_act=g_timeToSec("24:00:00");//hasta q hora
							$periodo_de_conteo=g_timeToSec("00:30:00");//fracción a considerar
							$incremento_etiquetas=g_timeToSec("01:00:00");//intervalo de etiquetas blancas (horas)
							$total_columnas=floor(($hora_de_fin_de_act - $hora_de_inicio_de_act) / $periodo_de_conteo);
							//mostrado de fila de horario
							//ej: 
							//$hora_de_inicio_de_act=g_timeToSec("09:00:00") =32400
							//$hora_de_fin_de_act=g_timeToSec("20:00:00") =72000
							//$periodo_de_conteo=g_timeToSec("00:30:00") =1800
							//$incremento= $periodo_de_conteo*(g_timeToSec("01:00:00")/$periodo_de_conteo) = 1800 * (3600 / 1800) = 1800 * 2 = 3600 = 1 hr
							//for ($i=32400; $i<72000; $i+=3600)
							for($i=$hora_de_inicio_de_act; $i<$hora_de_fin_de_act; $i+=$incremento_etiquetas){
								//etiquetas blancas de una hora de intervalo
								for ($ii=0; $ii<floor($incremento_etiquetas/$periodo_de_conteo); $ii++) $registros[]="00";
								if (($hr_entra)&&($hr_sale)&&(($i<g_timeToSec($hr_entra))||($i>=g_timeToSec($hr_sale)))) $hora_habil=$fuera_de_hora;
								else $hora_habil=$dentro_de_hora;
								echo "<td colspan= ".floor($incremento_etiquetas/$periodo_de_conteo)." bgcolor='$hora_habil' align='center'>".substr(g_secToTime($i), 0, 5)."</td>";
							}
							echo "</tr><tr>";
							//marca con "11" los intervalos horarios en los que el empleado se encontraba trabajando
							for ($ii=0, $jj=$hora_de_inicio_de_act; $ii<$total_columnas; $jj+=$periodo_de_conteo, $ii++){
								if ($ii==0) $jj+=g_timeToSec("00:15:00");
								for ($kk=0; $kk<count($horas); $kk++){
									if (($jj> g_timeToSec(substr($horas[$kk], 0, 8)))&&($jj< g_timeToSec(substr($horas[$kk], 9, 8)))) $registros[$ii]="11";
									elseif (($jj> g_timeToSec(substr($horas[$kk], 0, 8)))&&(substr($horas[$kk], 9, 8)=="")) $registros[$ii]="33";
								}
							}
							//etiqueta verde si "estaba", roja si "no estaba"
							for ($ii=0; $ii<count($registros); $ii++){
								if ($registros[$ii]=="11") echo "<td align='center' bgcolor='$asistio'><font color='$asistio'>.</td>";
								elseif ($registros[$ii]=="33") echo "<td align='center' bgcolor='$no_salio'><font color='$no_salio'>.</td>";
								else echo "<td align='center' bgcolor='$falto'><font color='$falto'>.</td>";
							}
							echo "</tr></table>";//fin fila colores
						}
					?>
		</tr>
		<tr>
				<br>
				<table border=1 width="80%" cellspacing=0 cellpadding=0 bgcolor='#FFFFFF' align=center>
					<!-- 
						$falto="#FFCCCC";
						$asistio="#CCFFCC";
						$domingo="#000000";
						$no_salio="#EE00EE";
						$mostrando="#FFFFFF";//dia_seleccionado
						$hoy_asistencia="#99EE99";//día actual
						$fuera_de_hora="#AAAAAA";//fuera de horario de trabajo
						$dentro_de_hora="#FFFFFF";//en horario de trabajo 
					-->
					<tr>
						<td colspan="11" align="left">&nbsp;Referencia de colores:</td>
					</tr>
					<tr>
						<td width="15" height="15" align='center' bgcolor='<?=$no_salio?>'><font color='<?=$no_salio?>'>.</td>
						<td width="23%" align="left" >&nbsp;No ha registrado la salida</td>
						<td></td>
						<td width="15" height="15" align='center' bgcolor='<?=$falto?>'><font color='<?=$falto?>'>.</td>
						<td width="23%" align="left" >&nbsp;Ausente</td>
						<td></td>
						<td width="15" height="15" align='center' bgcolor='<?=$asistio?>'><font color='<?=$asistio?>'>.</td>
						<td width="23%" align="left" >&nbsp;Presente</td>
						<td></td>
						<td width="15" height="15" align='center' bgcolor='<?=$domingo?>'><font color='<?=$domingo?>'>.</td>
						<td width="23%" align="left" >&nbsp;Domingo</td>
					</tr>
					<tr>
						<td width="15" height="15" align='center' bgcolor='<?=$mostrando?>'><font color='<?=$mostrando?>'>.</td>
						<td align="left" >&nbsp;D&iacute;a seleccionado</td>
						<td></td>
						<td width="15" height="15" align='center' bgcolor='<?=$hoy_asistencia?>'><font color='<?=$hoy_asistencia?>'>.</td>
						<td align="left" >&nbsp;D&iacute;a corriente</td>
						<td></td>
						<td width="15" height="15" align='center' bgcolor='<?=$fuera_de_hora?>'><font color='<?=$fuera_de_hora?>'>.</td>
						<td align="left" >&nbsp;Fuera de horario de trabajo</td>
						<td></td>
						<td width="15" height="15" align='center' bgcolor='<?=$dentro_de_hora?>'><font color='<?=$dentro_de_hora?>'>.</td>
						<td align="left" >&nbsp;Horario de trabajo</td>
					</tr>
				</table>
		</tr>
	</table>
	<br>
	<center>
		<input type="submit" name="guardarEdicion" value="Guardar cambios" onclick="return control_dato()">
	
		<input type="button" name="nuevo" value="Nuevo ingreso" onclick="window.open('<?= encode_link('editar_hora.php',array("id_usuario"=>$id_usuario, "id_legajo"=>$id_legajo, "user"=>$_ses_user["name"], "fecha" => date("Y-m-d")))?>','','toolbar=0,location=0,directories=0,status=0, menubar=0,scrollbars=1, height=300, width=400');">
	
	</center>
	<br>
		<?//mostrado de la tabla de asistencia
			$total_horas=0;//contador de horas trabajadas según el listado
			$result = sql($sql) or fin_pagina();
		?>
	<table border=0 width=95% cellspacing=1 cellpadding=3 bgcolor='<?=$bgcolor3?>' align=center>
		<tr id=ma>
			<td width=70% align=left colspan="4"><b>Total:</b> <?=$total_reg?> referencias.</td>
			<td align=right><?=$link_pagina?></td>
		</tr>
		<tr>
			<td align=right id=mo>Fecha</td>
			<td align=right id=mo>Hora de entrada</td>
			<td align=right id=mo>Hora de salida</td>
			<td align=right id=mo>Horas trabajadas</td>
			<td id="mo" width="5%">&nbsp;</td>
		</tr>
		<?
			$i=0;
			while (!$result->EOF) {//mostrado de filas de asistencia según selección
				//$hora_sale_anterior=$hora_sale_actual;
				$hora_entra=$result->fields["hora_entra"];
				$hora_sale=$result->fields["hora_sale"];
				?>
		<tr <?=atrib_tr()?>><input type="hidden" name="id_asistencia_<?=$i?>" value="<?=$result->fields["id_asistencia"]?>">
			<td align=left width="20%"><b><?=Fecha($result->fields["fecha"])?></td></b>
			<td align="right">
				<input type="text" maxlength="2" size="2" name="hora_entrada_<?=$i?>" 
					value="<?=substr($hora_entra, 0, 2)?>" 
					onfocus="this.select();"
					onkeypress="return filtrar_teclas(event,'0123456789'); "
					onkeyup="alProximoInput(this, this.value, document.all.minuto_entrada_<?=$i?>)">
				</input>
				&nbsp;:&nbsp;
				<input type="text" maxlength="2" size="2" name="minuto_entrada_<?=$i?>" 
					value="<?=substr($hora_entra, 3, 2)?>" 
					onfocus="this.select();"
					onkeypress="return filtrar_teclas(event,'0123456789'); "
					onkeyup="alProximoInput(this, this.value, document.all.hora_salida_<?=$i?>)">
				</input>
				&nbsp; hrs.
			</td>
			<td align="right">
				<input type="text" maxlength="2" size="2" name="hora_salida_<?=$i?>" 
					value="<?=substr($hora_sale, 0, 2)?>" 
					onfocus="this.select();"
					onkeypress="return filtrar_teclas(event,'0123456789'); "
					onkeyup="alProximoInput(this, this.value, document.all.minuto_salida_<?=$i?>)">
				</input>
				&nbsp;:&nbsp;
				<input type="text" maxlength="2" size="2" name="minuto_salida_<?=$i?>" 
					value="<?=substr($hora_sale, 3, 2)?>" 
					onfocus="this.select();"
					onkeypress="return filtrar_teclas(event,'0123456789'); "
					<? if ($i < ($itemspp -1)) { ?>
					onkeyup="alProximoInput(this, this.value, document.all.hora_entrada_<?=($i+1)?>)">
					<?}?>
				</input>
				&nbsp; hrs.
			</td>
				<?
				if ($result->fields["hora_sale"]){
					$difH= g_difHoras($result->fields["hora_sale"], $result->fields["hora_entra"]);
				//	echo "<br> H SALE ".$result->fields["hora_sale"]." Hora Entra".$result->fields["hora_entra"]."DIF ".$difH;
					$total_horas+= g_timeToSec($difH);
					echo "<td align=left>".$difH."</td>\n";
				}else echo "<td align=left>Aún no se ha registrado la salida</td>\n";
				?>
			<td align="center"><input type="checkbox" name="ch_renglon_<?=$i?>"></td>
		</tr>
				<?
				$i++;
				$result->MoveNext();
			}
		?>
		<tr>
			<table border=0 width=95% cellspacing=2 cellpadding=1 bgcolor='<?=$bgcolor3 ?>' align=center>
				<tr>
					<td align=center id=mo>
						Total de horas de trabajo registradas en el listado: <?echo g_secToTime($total_horas);?>
					</td>
				</tr>
			</table>
		</tr>
	</table>
	<input type="hidden" name="renglones" value="<?=$i?>">
</form>
<?
	fin_pagina();
?>
