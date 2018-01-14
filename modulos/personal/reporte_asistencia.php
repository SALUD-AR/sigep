<? 
/*
Autor: Mariela
$Author: mari $
$Revision: 1.6 $
$Date: 2006/06/06 17:09:08 $
*/
                                                
require("../../config.php");
require_once("gutils.php");

echo $html_header;
cargar_calendario();

$id_usuario=$parametros['id_usuario'] or $id_usuario=$_POST['id_usuario'];

if ($_POST['dias_semana']) $dias_semana=descomprimir_variable($_POST['dias_semana']);
else {
  $sql="select id_dia,nombre from dias_semana order by id_dia";
  $res=sql($sql,"c20 - $sql") or fin_pagina();
  $i=0;
  while (!$res->EOF) {
    $id=$res->fields['id_dia'];
    $dias_semana[$i++]=$id;
    $res->Movenext();
	}
}

/*********************************************************************
-funcion que devuelve la cantidad de dias habilles entre dos fechas
-considerando si trabaja o no sabados y/o domingos
-El formato de las fechas debe ser d/m/Y
-si sabado es 1 indica que trabaja los sabados
-si domingo es 1 indica que trabaja los domingos
*********************************************************************/
function cantidad_dias_habiles($fecha1,$fecha2,$sabado,$domingo,$feriado=0) {
	global $dias_semana;
 	$contador=array();
 	$cant=count($dias_semana);
 	for($i=0;$i<$cant;$i++) $contador[$dias_semana[$i]]=0; 
   
 	$contador["feriados"]=0;
 	$dif_dias=0;
 	$fecha_aux=$fecha1;
 
 	while ((compara_fechas(fecha_db($fecha_aux),fecha_db($fecha2))==-1) 
 				|| (compara_fechas(fecha_db($fecha_aux),fecha_db($fecha2))==0)){ //mientras la fecha2 sea mayor que la 1
 		$fecha_split=split("/",$fecha_aux);
    $fecha_dia=date("w",mktime(0,0,0,$fecha_split[1],$fecha_split[0],$fecha_split[2]));
    if ($fecha_dia==0) $id=7;
    else $id=$fecha_dia;
  //si es dia habil, incrementamos la diferencia        
   	if (!$sabado && !$domingo) { //no trabaja sabado ni domingo
   		if((!feriado($fecha_aux) && $fecha_dia!=0 && $fecha_dia!=6) || $feriado) {
   			if (feriado($fecha_aux)) $contador["feriados"]++;
    	  $dif_dias++;
      	$contador[$id]++;
   		}
  	}elseif ($sabado && !$domingo) { // trabaja sabado y no el domingo
    	if((!feriado($fecha_aux) && $fecha_dia!=0) || $feriado) {
    		if (feriado($fecha_aux)) $contador["feriados"]++;
        $dif_dias++;
        $contador[$id]++;
      }
   	}else if ($domingo && !$sabado) {//trabaja domingo y no sabado
    	if((!feriado($fecha_aux) && $fecha_dia!=6) || $feriado) {
    		if (feriado($fecha_aux)) $contador["feriados"]++;
        $dif_dias++;
        $contador[$id]++;
     	}
   	} else {  //trabaja sabado y domingo
    	if(!feriado($fecha_aux) || $feriado) {
    		if (feriado($fecha_aux)) $contador["feriados"]++;
        $dif_dias++;
        $contador[$id]++;
     	}
   	}
	  $contador[0]=$dif_dias;//cantidad de dias habiles
  	//incrementamos en un dia la fecha
  	$fecha_aux=date("d/m/Y",mktime(12,0,0,$fecha_split[1],$fecha_split[0]+1,$fecha_split[2]));
	}
 	return $contador;
}//de la cantidad_dias_habiles


function configuracion_horario($sabado,$domingo,$datos,$res_horario,$hr_inicio,$hr_fin) {
$res_horario->MoveFirst();

?>
<table class="bordes" cellspacing="0" cellpadding="1" width="45%">
<tr id=mo>
  <td colspan='3' align='center'> Configuración de horarios </td>
</tr>
 <tr bgcolor="White" >
    <td colspan=3>
      Trabaja Sabado <input type='checkbox' name='sabado' value=1 <?if ($sabado==1) echo 'checked'?> class="estilos_check" onclick="document.all.tabla_6.style.display=((this.checked)?'inline':'none')">
    </td>
 </tr>
  <tr bgcolor="White">
    <td  colspan=3>
      Trabaja Domingo <input type='checkbox' name='domingo' value=1 <?if ($domingo==1) echo 'checked'?> class="estilos_check" onclick="document.all.tabla_7.style.display=((this.checked)?'inline':'none')">
    </td>
 </tr>
 <? while (!$res_horario->EOF) { 
 	$id=$res_horario->fields['id_dia'];
 	?>
 	<tr>
 		<td>
 			<table width="100%" id="tabla_<?=$id?>" style="display:inline">
 			 	<input type='hidden' name='id_<?=$id?>' value='<?=$id?>'>
	   		<tr id=ma>
  	   		<td width="40%"> <?=$res_horario->fields['nombre']?></td>
    	 		<td width="30%"> <? g_draw_value_select("ini_$id",$hr_inicio[$id],$datos,$datos)?></td>
     			<td width="30%"> <? g_draw_value_select("fin_$id",$hr_fin[$id],$datos,$datos)?></td>
     			<? $res_horario->Movenext(); ?>
   			</tr>
   		</table>
   </td>
 </tr>
 <?} ?>
 <tr bgcolor="White">
   <td colspan='3' align='center'>
     <input type='submit' name='guardar_configuracion' value='Guardar Configuracion'>
   </td>
 </tr>
</table>
<script>
	document.all.tabla_6.style.display=((document.all.sabado.checked)?'inline':'none');
	document.all.tabla_7.style.display=((document.all.domingo.checked)?'inline':'none');
</script>
<?}


if ($_POST['guardar_configuracion']) {
	$db->StartTrans();	
	$sql_delete="delete from horarios_trabajo where id_usuario=$id_usuario";
	$res=sql($sql_delete,"$sql_delete") or fin_pagina();
	$dias=descomprimir_variable($_POST['dias_semana']);
	$cant=count($dias);
	for($i=0;$i<$cant;$i++) {
		$id=$dias[$i];
		
		if ($_POST['sabado']) $sabado=1;
	      else $sabado=0;
	    if ($_POST['domingo']) $domingo=1;
	      else $domingo=0;  
		
	    if (($id==6 && $sabado==0) || $id==7 && $domingo==0)  { //si es sabado y no trabaja
		         $ini="00:00";
		         $fin="00:00";
	    }
		else {
			$ini=$_POST["ini_$id"];
	        $fin=$_POST["fin_$id"];
		}
	  
		$sql_insert[]="insert into horarios_trabajo (id_dia,id_usuario,trabaja_sabado,trabaja_domingo,inicio_horario,fin_horario)
             values ($id,$id_usuario,$sabado,$domingo,'$ini','$fin')";
	}
	sql($sql_insert,"$sql_insert") or fin_pagina();
	$db->CompleteTrans();
}


?>
<form name='form1' method="post" action='reporte_asistencia.php'>
<input type="hidden" name="id_usuario" value="<?=$id_usuario?>">

<?
$falto="#FFCCCC";
$asistio="#CCFFCC";
$domingo="#000000";
$no_salio="#EE00EE";
$mostrando="#FFFFFF";//dia_seleccionado
$hoy_asistencia="#99EE99";//día actual
$fuera_de_hora="#AAAAAA";//fuera de horario de trabajo
$dentro_de_hora="#FFFFFF";//en horario de trabajo
$color='white';
$color_feriado="#FBA39E";

$sql="select nombre,apellido from usuarios where id_usuario=$id_usuario";
$res=sql($sql,"$sql") or fin_pagina();
$nombre=$res->fields['apellido']." ".$res->fields['nombre'];

$fecha_desde=$_POST['fecha_desde'] or $fecha_desde=date("d/m/Y");
$fecha_hasta=$_POST['fecha_hasta'] or $fecha_hasta=date("d/m/Y");

$hora_de_inicio_de_act=g_timeToSec("00:00:00");//desde cuándo tener en cuenta
$hora_de_fin_de_act=g_timeToSec("24:00:00");//hasta q hora
$periodo_de_conteo=g_timeToSec("00:30:00");//fracción a considerar
$incremento_etiquetas=g_timeToSec("01:00:00");//intervalo de etiquetas blancas (horas)
$total_columnas=floor(($hora_de_fin_de_act - $hora_de_inicio_de_act) / $periodo_de_conteo);

//armo arreglo con horas de trabajo del usuario, si no estan guardado en la tabla horarios_trabajo
//si no esta guardado el horario por defecto es de 08:00 a 18:00
$sql_horario="select id_dia,trabaja_sabado,trabaja_domingo,inicio_horario,fin_horario,nombre
              from horarios_trabajo 
              join dias_semana using(id_dia) 
              where id_usuario=$id_usuario
              order by id_dia";
$res_horario=sql($sql_horario,"$sql_horario") or fin_pagina();

//$dias_semana=array();
$contador=array();

if ($res_horario->RecordCount()>0) {  //se ha guardado la configuracion de los horarios del usuario seleccionado
	$res_horario->MoveFirst();
	$cantidad=$res_horario->RecordCount();
	while (!$res_horario->EOF) {
 		$sabado=$res_horario->fields['trabaja_sabado'];
		$domingo=$res_horario->fields['trabaja_domingo'];
 		$dia=$res_horario->fields['nombre'];
 		$id=$res_horario->fields['id_dia'];	
 		$hr_inicio[$id]=substr($res_horario->fields['inicio_horario'],0,5);
 		$hr_fin[$id]=substr($res_horario->fields['fin_horario'],0,5);
 		$hora_a_trabajar[$id]= g_timeToSec(g_difHoras($hr_fin[$id],$hr_inicio[$id]));
 		$res_horario->MoveNext();
	}
}else {
	//si no se ha guardado configuracion se toma que trabaja de lunes a viernes de 08:00 a 18:00
	$sql="select id_dia,nombre from dias_semana order by id_dia";
	$res_horario=sql($sql,"$sql") or fin_pagina();
	$sabado=0;
	$domingo=0;
	$i=0;

	while (!$res_horario->EOF) {
		$id=$res_horario->fields['id_dia'];
     
		if ($id==7 || $id==6) { //domingo o sabado
			$hr_inicio[$id]='00:00';
			$hr_fin[$id]='00:00';
		}else {
			$hr_inicio[$id]='08:00';
			$hr_fin[$id]='18:00';
		}
		
		 $hora_a_trabajar[$id]= g_timeToSec(g_difHoras($hr_fin[$id],$hr_inicio[$id]));
		 $res_horario->MoveNext();
	}
}


$sql_datos="select id_asistencia,id_usuario,fecha,hora_entra,hora_sale 
              from asistencia where id_usuario=$id_usuario 
              and fecha between '".fecha_db($fecha_desde)."' and '".fecha_db($fecha_hasta)."'
              order by fecha asc";
$result=sql($sql_datos,"$sql_datos") or fin_pagina();

$contador=cantidad_dias_habiles($fecha_desde,$fecha_hasta,$sabado,$domingo,0);
$contador2=cantidad_dias_habiles($fecha_desde,$fecha_hasta, 1, 1, 1);

//$horas arreglo con hora inicio - hora fin de cada entrada y fecha

$i=0;
$d=intval(substr(Fecha_db($fecha_desde), 8, 2));
$m=intval(substr(Fecha_db($fecha_desde), 5, 2));
$a=intval(substr(Fecha_db($fecha_desde), 0, 4));
$dias_habiles=$inasistencias=$contador[0];

while ($i<$contador2[0]){//(!$result->EOF) {
	$fecha=date("Y-m-d", mktime(0, 0, 0, $m, $d, $a));
	
	if ($result->fields["fecha"]==$fecha) {
	  $inasistencias--;	
	  $horas[$i]['hora']=$result->fields["hora_entra"]."-".$result->fields["hora_sale"];
      $horas[$i]['fecha']=$result->fields["fecha"];
  	  $result->moveNext();
	}else{
	   $horas[$i]['hora']="00:00-00:00";
  	   $horas[$i]['fecha']=$fecha;
	}
  $i++; $d++;
}


?>
<table width="95%" align='center' cellpadding="0" bgcolor="<?=$bgcolor3?>" cellspacing="0">
  <tr>
  	<td width="35%">
    	<b>USUARIO: </b><?=$nombre?> 
   	</td>
   	<td align="center">
	  	<b> Reporte de Asistencia desde </b> <input type="text" name="fecha_desde" value='<?=$fecha_desde?>' readonly size='12'> <?=link_calendario("fecha_desde")?>
  	 	<b> hasta</b> <input type='text' name="fecha_hasta" value="<?=$fecha_hasta?>" readonly size='12'> <?=link_calendario("fecha_hasta")?>
    	<input type="submit" value='Ver' name='ver'>
    </td>
  </tr>
</table>
<table border="1" cellpadding="0" bgcolor="White" align="center" cellspacing="0">
<tr>
	<td>&nbsp; </td>
<?	
$datos=array();
	
for($i=$hora_de_inicio_de_act,$j=0; $i<$hora_de_fin_de_act; $i+=$incremento_etiquetas) {
	//etiquetas blancas de una hora de intervalo
	echo "<td colspan= ".floor($incremento_etiquetas/$periodo_de_conteo)." bgcolor='$color' align='center'>".substr(g_secToTime($i), 0, 5)."</td>";
	$datos[$j++]=substr(g_secToTime($i), 0, 5); //arreglo con las horas desde 00:00 a 23:00 para armar select 
}
  
?>
</tr>
<?	

$cant_hora=count($horas);
$horas_trabajadas=0;
$tarde=0;

for ($h=0;$h<$cant_hora;$h++) {
	$fecha=$horas[$h]['fecha'];
	$f=split('-',$fecha);
	$num=calcula_numero_dia_semana($f[2],$f[1],$f[0]);
	
	switch ($num) {
    case 1: $nombre='Lunes'; break; 
    case 2: $nombre='Martes'; break; 
    case 3: $nombre='Miércoles'; break; 
    case 4: $nombre='Jueves'; break; 
    case 5: $nombre='Viernes'; break; 
    case 6: $nombre='Sábado'; break;
    case 7: $nombre='Domingo'; break; 
  } 
	$hr_entra=substr($horas[$h]['hora'],0,8);
    $hr_sale=substr($horas[$h]['hora'],9,8);
	
   if($hr_sale)  {  
      $difH= g_difHoras($hr_sale,$hr_entra);
	  $horas_trabajadas+= g_timeToSec($difH);
  }
  $registros=array();

  //inicaliza  $registros[$ind] con 00 si esta dentro de horario de trabajo
  //inicaliza  $registros[$ind] con 22 si esta fuera de horario de trabajo
  //cada fila del arreglo corresponde a una fecha, cada columna son las hora 00:00, 00:30 .....23:00 23:30
  //si la fecha es feriado toda la fila con 44 
	for ($ind=0;$ind<$total_columnas;$ind++) {
		$hor=$ind*1800;
		if (feriado(fecha($fecha))) $registros[$ind]="44"; //feriado
		 elseif (en_horario($hor,$hr_inicio[$num],$hr_fin[$num])) $registros[$ind]="00";
		   else  $registros[$ind]="22";
	}	

	 if (feriado(fecha($fecha))) {
	   $color="bgcolor='$color_feriado'";
	   $nombre.=' - Feriado';
	 }        
	 else $color="";   
	
	echo "<tr>";
	echo "<td title='$nombre' $color>".fecha($fecha)."</td>";
	if (g_timeToSec($hr_entra)  > (g_timeToSec($hr_inicio[$num])  + g_timeToSec("00:15:00"))) $tarde++;
	//marca con "11" los intervalos horarios en los que el empleado se encontraba trabajando
	//marca con 33 en los horarios que no salio
	for ($ii=0, $jj=$hora_de_inicio_de_act; $ii<$total_columnas; $jj+=$periodo_de_conteo, $ii++){
		if ($ii==0) $jj+=g_timeToSec("00:15:00");
		if (g_timeToSec($hr_entra) <= g_timeToSec($hr_sale)) { //turno mañana o tarde
		  if (($jj > g_timeToSec($hr_entra))&&($jj< g_timeToSec($hr_sale))) 
				       $registros[$ii]="11";
		  // elseif (($jj> g_timeToSec($hr_entra)) && ($jj<g_timeToSec("24:00")) &&($hr_sale=="")) $registros[$ii]="33";
		}
		else { 
		  if ($hr_sale=="" && en_horario($jj,$hr_entra,$hr_fin[$num]))  $registros[$ii]="33";    //NO REGISTO SALIDA 
	  	   elseif (  $hr_sale!="" && ((($jj >= g_timeToSec($hr_entra)) && ($jj < g_timeToSec("23:59:00"))) 
	  	               ||  (($jj >= g_timeToSec("00:00:00")) && ($jj < g_timeToSec($hr_sale)))))  //turno noche
	  	          $registros[$ii]="11";       
		}
	}
	
  //etiqueta verde si "estaba", rosa si "no estaba"
	for ($ii=0; $ii<count($registros); $ii++){
		if ($registros[$ii]=="11") $color_td=$asistio;
		elseif ($registros[$ii]=="33") $color_td=$no_salio;
		elseif ($registros[$ii]=="22") $color_td=$fuera_de_hora;
		elseif ($registros[$ii]=="44") $color_td=$color_feriado;
		else $color_td=$falto;
		
		echo "<td width='17' height='17' align='center' bgcolor='$color_td'><font color='$color_td'>&nbsp;</td>";
		$color_td="";
	}
	echo "</tr>";
}
echo"</table>";//fin fila colores

?>
<br>
<table border=1 width="80%" cellspacing=0 cellpadding=0 bgcolor='#FFFFFF' align=center>
<tr>
  <td colspan="14" align="left">&nbsp;Referencia de colores:</td>
</tr>
<tr>
  <td align='center' bgcolor='<?=$no_salio?>'><font color='<?=$no_salio?>'>&nbsp;</font></td>
  <td align="left" >&nbsp;No ha registrado la salida</td>
  <td></td>
  <td align='center' bgcolor='<?=$falto?>'><font color='<?=$falto?>'>&nbsp;</font></td>
  <td align="left" >&nbsp;Ausente</td>
  <td></td>
  <td align='center' bgcolor='<?=$asistio?>'><font color='<?=$asistio?>'>&nbsp;</font></td>
  <td align="left" >&nbsp;Presente</td>
  <td></td>
  <td align='center' bgcolor='<?=$fuera_de_hora?>'><font color='<?=$fuera_de_hora?>'>&nbsp;</font></td>
  <td align="left" >&nbsp;Fuera de horario de trabajo</td>
  <td></td>
  <td align='center' bgcolor='<?=$color_feriado?>'><font color='<?=$color_feriado?>'>&nbsp;</font></td>
  <td align="left" >&nbsp;Feriado</td>
</tr>
</table>


<?
//meta: cantidad de horas que debería trabajar

$cant=count($dias_semana);
$meta=0;
for($j=0;$j<$cant;$j++) {
	$id=$dias_semana[$j];
	$meta+=($contador[$id] * $hora_a_trabajar[$id]);
}

?>

<input type='hidden' name='dias_semana' value='<?=comprimir_variable($dias_semana)?>'>
<br>
<table align="center" width="95%">
<tr>
<td>
 <? configuracion_horario($sabado,$domingo,$datos,$res_horario,$hr_inicio,$hr_fin);?>
</td>
<td>
 <table align="left" >
   <tr>
     <td>
      Horas Trabajadas: <?=g_secToTime($horas_trabajadas)." Hs."?>
     </td>
      <td>
      Meta: <?=g_secToTime($meta)." Hs."?>
     </td>
   </tr>
   <tr>
     <td>
      Días Hábiles: <?=(($contador[0])?$contador[0]:0)." Días"?>
     </td>
   </tr>
   <tr>
     <td>
      Inasistencias: <?=(($inasistencias)?$inasistencias:0)." Días"?>
     </td>
   </tr>
   <tr>
     <td>
      Llegadas tarde: <?=(($tarde)?$tarde:0)." Días"?>
     </td>
   </tr>
 </table>
</td>
</tr>
<tr align='center'> 
  <td colspan=2><input type='button' name="cerrar" value='Cerrar' onclick="window.close();" ></td>
</tr>
</table>
</form>
<?fin_pagina();?>