<?php
//Autor: Gabriel Gaudina
/*
	$Author: mari $
	$Revision: 1.7 $
	$Date: 2006/06/06 17:08:29 $
*/	
	//require "../../config.php";

	function g_timeToSec($time){
		$horas=substr($time, 0, 2);
		$minutos=substr($time, 3, 2);
		$segundos=substr($time, 6, 2);
		
		return $segundos+($minutos*60)+($horas*3600);
	}
	
	function g_secToTime($sec){
		$segundos= $sec % 60;
		$sec=floor($sec/60);
		$minutos= $sec % 60;
		$sec=floor($sec/60);
		$horas= $sec;
		
		if (strlen($horas)==1) $horas="0".$horas;
		if (strlen($minutos)==1) $minutos="0".$minutos;
		if (strlen($segundos)==1) $segundos="0".$segundos;
		
		return $horas.":".$minutos.":".$segundos;
	}
	
	
	function dif($h1,$h2) {
	  return abs(g_timeToSec($h1)-g_timeToSec($h2));
	}
	//$time1 Hora sale
	//$time2 hora entra
	function g_difHoras($hs,$he) {	
	 if (g_timeToSec($hs) < g_timeToSec($he)) {	
		$D1= dif("23:59:59", $he) + dif("00:00:00",$hs);
		return (g_secToTime($D1+1));
		}
	 else {
	    return g_secToTime(abs(g_timeToSec($hs)-g_timeToSec($he)));
	 }	
	}
	
	
	function g_cargar_asistencia($id_subject, $mes, $año) {
		global $db;
		$sql = "select DISTINCT fecha from asistencia where id_legajo=".$id_subject." and fecha ilike '%".$año."-".$mes."-%'";
		$result = $db->Execute($sql) or fin_pagina();
		$i=1;
		while ($i<=date("t", mktime(0, 0, 0, intval($mes), 1, intval($año)))){
			if ($i==intval(substr($result->fields["fecha"], 8, 2))){
				$ret[]="11";
				$result->MoveNext();
			}else $ret[]="00";
			$i++;
		}
		return $ret;
	}
	
	function g_draw_range_select($name, $selected=1, $desde=0, $hasta=10, $size=1, $extra=""){
		echo "<select id='$name' name='$name' size='$size' $extra>";
		for ($i=$desde; $i<=$hasta; $i++)
			if ($selected!=$i) echo "<option value='$i'>$i";
			else echo "<option value='$i' selected>$i";
		echo "</select>";
	}

	function g_draw_select($name="sel", $selected=0, $datos=array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Setiembre", "Octubre", "Noviembre", "Diciembre"), $size=1, $extra=""){
		echo "<select id='$name' name='$name' size='$size' $extra>";
		for ($i=1; $i<=13; $i++)
			if ($selected!=$i) echo "<option value='$i'>".$datos[$i-1];
			else echo "<option value='$i' selected>".$datos[$i-1];
		echo "</select>";
	}
	
	function g_draw_string_select($name="sel", $selected, $datos, $size=1, $extra=""){
		echo "<select id='$name' name='$name' size='$size' $extra>";
		for ($i=0; $i<count($datos); $i++)
			if (($selected!=$datos[$i])&&($selected!=$i)) echo "<option value='$i'>".$datos[$i];
			else echo "<option value='$i' selected>".$datos[$i];
			
		echo "</select>";
	}
	
	function g_draw_value_select($name="sel", $selected, $values, $datos, $size=1, $extra=""){
		echo "<select id='$name' name='$name' size='$size' $extra>";
		for ($i=0; $i<count($datos); $i++)
			if (($selected!=$datos[$i])&&($selected!=$values[$i])) echo "<option value='$values[$i]'>".$datos[$i];
			else echo "<option value='$values[$i]' selected>".$datos[$i];
			
		echo "</select>";
	}
	
	function g_draw_mix_select($name="sel", $selected, $datos, $size=1, $extra=""){
		echo "<select id='$name' name='$name' size='$size' $extra>";
		for ($i=0; $i<count($datos); $i++)
			if (($selected!=$datos[$i]["label"])&&($selected!=$datos[$i]["key"])) 
				echo "<option value='".$datos[$i]['key']."'>".$datos[$i]["label"];
			else echo "<option value='".$datos[$i]['key']."' selected>".$datos[$i]['label'];
			
		echo "</select>";
	}
	
	//devuelve 1 si la $hor hora en segundos  esta dentro del horario de trabajo
//$hr_inicio HORA en que deberia entrar (en formato hora)
//$hr_fin HORA en la que deberia salir  (en formato hora)

function en_horario($hor,$hr_inicio,$hr_fin) {
 if ($hr_inicio <= $hr_fin) {
  if ($hor >= g_timeToSec($hr_inicio) && $hor < g_timeToSec($hr_fin) )
   return 1;
   else return 0; 
 }
 else {   
    if  ( ($hor >= g_timeToSec($hr_inicio) && $hor < g_timeToSec("23:59:00"))
	  	            ||  ($hor >= g_timeToSec("00:00:00") && $hor < g_timeToSec($hr_fin)))
	return 1; 
	else return 0; 	            
 }  
}

/*
function horas_trabaja($id_usuario) {
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
		}else {
			$hr_inicio[$id]='08:00';
		}
		 $res_horario->MoveNext();
	}
}
return $hr_inicio;
}

*/
function horas_trabaja($ubicacion=-1) {
//armo arreglla hora de inicio de trabajo de caada  usuario que este en la tabla horarios_trabajo
// si no esta guardado en la tabla horarios_trabajo el horario por defecto es de 08:00 a 18:00

$sql_horario="select id_dia,inicio_horario,id_usuario
              from personal.horarios_trabajo 
              join personal.dias_semana using(id_dia)";
if ($ubicacion !='Todas') 
   $sql_horario.=" join sistema.usuarios using (id_usuario)
                   join personal.distrito on pcia_ubicacion=id_distrito
                   where distrito.nombre ilike '%$ubicacion%'";
$sql_horario.="order by id_usuario,id_dia";

$res_horario=sql($sql_horario,"$sql_horario") or fin_pagina();

$hr_inicio=array();
$id_ant="";
while (!$res_horario->EOF) {
    $id_usuario=$res_horario->fields['id_usuario'];	
	if ($id_ant != $id_usuario ) {
	 $hr_inicio[$id_usuario]=array(); 
	}
	
	$hr_inicio[$id_usuario][$res_horario->fields['id_dia']]=substr($res_horario->fields['inicio_horario'],0,5);
	
	$id_ant=$id_usuario;
	$res_horario->Movenext();
}
return $hr_inicio;
}


//si un usuario no esta cargado en la tabla horarios_trabajo
//toma valores por defecto de lunes a viernes de 08:00 a 18:00
//sabado y domingo no trabaja
function horas_trabaja_defecto() {
  for ($i=1;$i<=5;$i++)
	$hr_inicio[$i]='08:00';
   for ($i=6;$i<=7;$i++)	
    $hr_inicio[$i]='00:00';
return $hr_inicio;	
}

//devuelve el nombre dia de la semana que le correponde d/m/a
// lun=1...dom 7
function calcula_numero_dia_semana($dia,$mes,$ano){ 
  $nrodiasemana = date('w', mktime(0,0,0,$mes,$dia,$ano));
  if ($nrodiasemana==0) $nrodiasemana=7; //si es domingo devuelve 7
  return $nrodiasemana;
} 

function calcula_nombre_mes($mes) {
switch ($mes) {
 
case 1:
case '01':return "Enero";
           break;
case 2:
case '02':return "Febrero";
           break;
case 3:
case '03':return "Marzo";
           break;
case 4: 
case '04':return "Abril";
           break;
case 5:
case '05': return "Mayo";
            break; 
case 6:
case '06': return "Junio";
           break;      
case 7: 
case '07': return "Julio";
           break;        
case 8: 
case '08':return "Agosto";
          break;   
case 9:
case '09': return "Septiembre";
         break;
case 10: return "Octubre";
         break;
case 11: return "Noviembre";
         break;        
case 12: return "Diciembre";
         break;    	

}

}
?>