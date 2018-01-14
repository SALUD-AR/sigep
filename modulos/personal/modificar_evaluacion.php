<?php
/*
$Author: fernando $
$Revision: 1.8 $
$Date: 2005/03/09 14:24:44 $
*/

require "../../config.php";

echo $html_header;

$sql = "SELECT id_evaluador FROM evaluadores WHERE id_usuario=".$_ses_user["id"];
$result = sql($sql) or fin_pagina();
$es_evaluador = $result->fields["id_evaluador"] or $es_evaluador = 0;



if ($parametros["cmd"] == "modificar") {
	$id_evaluacion = $parametros["id_evaluacion"];
	form_modificar(false,$id_evaluacion);
}

elseif ($_POST["eval_guardar"]) {
	$error = 0;
	$sql_array = array();
	$eval_preg = $_POST["eval_preg"];
	$evaluador = $_POST["eval_evaluador"] or Error("Falta seleccionar el evaluador");
	$fecha = $_POST["eval_fecha"] or Error("Falta ingresar la fecha");
	$id_evaluacion = $_POST["eval_id_evaluacion"] or Error("Falta ingresar el ID");

    if ($_POST["puntos_fuertes_h"])
               $puntos_fuertes = $_POST["eval_puntos_fuertes"];
    if ($_POST["puntos_debiles_h"])
	           $puntos_debiles = $_POST["eval_puntos_debiles"];

	$historial = $_POST["eval_historial"];
	$sql = "SELECT count(*) AS cant_preg FROM evaluacion_preguntas";
	$result = sql($sql) or fin_pagina();
	if (count($eval_preg) != $result->fields["cant_preg"]) {
		Error("Faltan responder algunas preguntas");
	}
	$sql = "SELECT id_legajo FROM evaluaciones WHERE id_evaluacion=$id_evaluacion";
	$result = sql($sql) or fin_pagina();
	$id_legajo = $result->fields["id_legajo"];
	if (!$error) {

		//$sql = "UPDATE evaluaciones SET puntos_fuertes='$puntos_fuertes',puntos_debiles='$puntos_debiles'";
        $sql = "UPDATE evaluaciones SET ";
        if ($puntos_fuertes){
                   $sql.=" puntos_fuertes='$puntos_fuertes'";
                   $pf=1;
                    }
        if ($puntos_debiles){
                  if ($pf) $sql.=" ,";
                  $sql.="puntos_debiles='$puntos_debiles'";
                  $pd=1;
             }
        if ($pf || $pd) $sql.=" ,";
		if ($historial) {
            $sql .= " estado='HISTORIAL'";
		}
		else {
			$sql .= " estado='PENDIENTE'";
		}

		$suma_valores = 0;
		if ($_POST["eval_nueva"] == "1") {
			$sql .= ",fecha='$fecha'";
			foreach ($eval_preg as $id_pregunta => $valor) {
				$suma_valores += $valor;
				$sql_tmp = "INSERT INTO respuestas (id_evaluacion, id_pregunta, valor) ";
				$sql_tmp .= "VALUES ($id_evaluacion, $id_pregunta, $valor)";
				$sql_array[] = $sql_tmp;
			}
		}
		else {
			foreach ($eval_preg as $id_pregunta => $valor) {
				$suma_valores += $valor;
				$sql_tmp = "UPDATE respuestas SET valor=$valor ";
				$sql_tmp .= "WHERE id_evaluacion=$id_evaluacion AND id_pregunta=$id_pregunta";
				$sql_array[] = $sql_tmp;
			}
		}
		if ($suma_valores > 0) {
			$calificacion = sprintf("%0.2f",($suma_valores / count($eval_preg)));
		}
		else {
			$calificacion = 0;
		}
		$sql .= ",calificacion=$calificacion";
		$sql .= " WHERE id_evaluacion=$id_evaluacion";
		$sql_array[] = $sql;

		$sql = "UPDATE legajos SET id_evaluador=$evaluador WHERE id_legajo=$id_legajo";
		$sql_array[] = $sql;

		sql($sql_array) or fin_pagina();
		Aviso("Los datos se cargaron correctamente");
		echo "<br><br><form><center><input type='button' name='volver' value='Volver al listado' onClick=\"document.location='listado_evaluaciones.php';\"></form></center>";
	}
	else {
		echo "<br><br><form><center><input type='button' name='volver' value='Volver' onClick=\"document.location='".encode_link("modificar_evaluacion.php",array("cmd"=>"modificar","id_evaluacion"=>$id_evaluacion))."';\"></form></center>";
		fin_pagina();
	}
}
else {
	fin_pagina();
}

function form_modificar($nuevo = false,$id = "") {
	global $html_header,$html_root,$bgcolor2,$bgcolor3,$max_file_size,$_ses_user,$es_evaluador;
	echo "<form action='".$_SERVER["PHP_SELF"]."' method=POST enctype='multipart/form-data'>";
	$sql = "SELECT id_legajo,periodo,estado,puntos_fuertes,puntos_debiles,";
	$sql .= "legajos.apellido AS leg_apellido,legajos.nombre AS leg_nombre,";
	$sql .= "id_evaluador,fecha,calificacion,dni ";
	$sql .= "FROM evaluaciones ";
	$sql .= "LEFT JOIN legajos USING (id_legajo) ";
	$sql .= "WHERE id_evaluacion = $id";
	$result = sql($sql) or fin_pagina();
	$evaluado = $result->fields["leg_apellido"].", ".$result->fields["leg_nombre"];
	$periodo = $result->fields["periodo"];
	$dni = $result->fields["dni"];
	$id_legajo = $result->fields["id_legajo"];
	$calificacion = $result->fields["calificacion"];
	if ($result->fields["fecha"]) {
		$fecha = Fecha($result->fields["fecha"]);
		$fecha_db = $result->fields["fecha"];
	}
	else {
		$fecha = date("d/m/Y H:i:s");
		$fecha_db = date("Y-m-d H:i:s");
	}
	$estado = $result->fields["estado"];
	$puntos_fuertes = $result->fields["puntos_fuertes"];
	$puntos_debiles = $result->fields["puntos_debiles"];
	$id_evaluador = $result->fields["id_evaluador"] or $id_evaluador = $es_evaluador;
	echo "<br><table width=95% border=1 cellspacing=0 cellpadding=3 bgcolor=$bgcolor2 align=center>";
	echo "<tr><td style=\"border:$bgcolor3;\" colspan=2 align=center id=mo><font size=+1>Evaluaci�n de competencia individual</font></td></tr>";
	echo "<tr><td align=left width=50%><b>Evaluado: </b> $evaluado</td>";
	if (file_exists(MOD_DIR."/personal/fotos/leg_$id_legajo.gif")) {
		$foto = "fotos/leg_$id_legajo.gif";
	}
	elseif (file_exists(MOD_DIR."/personal/fotos/leg_$id_legajo.jpg")) {
		$foto = "fotos/leg_$id_legajo.jpg";
	}
	else { $foto = "fotos/no_disponible.jpg"; }
	echo "<td align=right rowspan=2 width=50%><img width=120 height=120 src='$foto'></td>";
	echo "</tr><tr>";
	echo "<td align=left><b>Per�odo: </b>$periodo</td>";
	echo "</tr><tr>";
	echo "<td style=\"border:$bgcolor3;\" colspan=2 align=center id=mo><font size=+1>Calificaci�n global de desempe�o</font></td>";
	echo "</tr><tr>";
	echo "<td colspan=2>";
	echo "<table border=0 width=100% cellpadding=3><tr>";
	echo "<td align=left width=50%><b>0</b> No satisface requerimientos</td>";
	echo "<td align=left width=50%><font size='-2'>Desempe�o con deficiencias</font></td>";
	echo "</tr><tr>";
	echo "<td align=left><b>50</b> Satisface requerimientos m�nimos</td>";
	echo "<td align=left><font size='-2'>Desempe�o que no siempre satisface los requerimientos del puesto o lo hace en forma marginal</font></td>";
	echo "</tr><tr>";
	echo "<td align=left><b>75</b> Satisface requerimientos</td>";
	echo "<td align=left><font size='-2'>Desempe�o que cumple con los normales del puesto</font></td>";
	echo "</tr><tr>";
	echo "<td align=left><b>100</b> Cumple y supera requerimientos</td>";
	echo "<td align=left><font size='-2'>Desempe�o claramente superior a los normales</font></td>";
	echo "<tr></table></td>";
	echo "</tr><tr>";
	echo "<td style=\"border:$bgcolor3;\" colspan=2 align=center id=mo><font size=+1>Evaluaci�n de competencia</font></td>";
	echo "</tr><tr>";
	echo "<td colspan=2>";
	$sql = "SELECT evaluacion_preguntas.id_pregunta,factor,definicion,valor,id_evaluacion ";
	$sql .= "FROM evaluacion_preguntas LEFT JOIN respuestas ";
	$sql .= "ON evaluacion_preguntas.id_pregunta=respuestas.id_pregunta AND id_evaluacion=$id ";
	$sql .= "ORDER BY evaluacion_preguntas.id_pregunta";
	$result = sql($sql) or fin_pagina();
	if ($result->RecordCount() == 0) {
		Error("No se han cargado las preguntas");
	}
	else {
		if ($result->fields["id_evaluacion"]) {
			echo "<input type=hidden name=eval_nueva value='0'>";
		}
		else {
			echo "<input type=hidden name=eval_nueva value='1'>";
		}
		echo "<table border=1 width=100% cellpadding=3 cellspacing=0><tr>";
		echo "<td align=center width=20%><b>FACTOR</b></td>";
		echo "<td align=center width=60%><b>DEFINICI�N</b></td>";
		echo "<td align=center width=5%><b>100</b></td>";
		echo "<td align=center width=5%><b>75</b></td>";
		echo "<td align=center width=5%><b>50</b></td>";
		echo "<td align=center width=5%><b>0</b></td>";
		echo "</tr>";
		while (!$result->EOF) {
			echo "<tr>";
			echo "<td align=center>".$result->fields["factor"]."</td>";
			echo "<td align=left>".$result->fields["definicion"]."</td>";
			echo "<td align=center><input type=radio value='100' name='eval_preg[".$result->fields["id_pregunta"]."]'".(($result->fields["valor"] == "100")?" checked":"")."></td>";
			echo "<td align=center><input type=radio value='75' name='eval_preg[".$result->fields["id_pregunta"]."]'".(($result->fields["valor"] == "75")?" checked":"")."></td>";
			echo "<td align=center><input type=radio value='50' name='eval_preg[".$result->fields["id_pregunta"]."]'".(($result->fields["valor"] == "50")?" checked":"")."></td>";
			echo "<td align=center><input type=radio value='0' name='eval_preg[".$result->fields["id_pregunta"]."]'".(($result->fields["valor"] == "0")?" checked":"")."></td>";
			echo "</tr>";
			$result->MoveNext();
		}
		echo "<tr><td colspan=2 align=center><b>CALIFICACI�N GLOBAL</b></td>";
		echo "<td colspan=4 align=center><b>".formato_money($calificacion)."</b></td></tr>";
		echo "</table>";
	}
	echo "</td>";
	echo "</tr>";
    echo "<tr>";
	echo "<td colspan=2>Puntos fuertes del Evaluado:<br>";

    $sql_e=" select id_usuario from evaluadores where id_evaluador=$id_evaluador";
    $result_e=sql($sql_e) or fin_pagina();
    $id_legajo_evaluador=$result_e->fields["id_usuario"];

    if ($_ses_user["login"]=="fer" ||  $_ses_user["id"]==$id_legajo_evaluador)
          {
          $disabled_pf="";
          echo "<input type=hidden name=puntos_fuertes_h value=1>";
          }
          else {
               $readonly_pf="readonly";
               $puntos_fuertes="RESTRINGIDO!!!!!!!!!!!!!!";
               $estilo=" style='color:red'";
               }

	echo "<textarea name='eval_puntos_fuertes' style='width:100%;' rows=5 $readonly_pf  $estilo>";
    echo $puntos_fuertes;
    echo "</textarea></td>";
	echo "</tr><tr>";
	echo "<td colspan=2>Puntos D�biles o a Mejorar del Evaluado:<br>";

    if ($_ses_user["login"]=="fer" ||  $_ses_user["id"]==$id_legajo_evaluador)
          {
          $disabled_pd=" ";
          echo "<input type=hidden name=puntos_debiles_h value=1>";
          }
          else {
               $readonly_pd=" readonly";
               $puntos_debiles="RESTRINGIDO!!!!!!!!!!!!!!!";
               $estilo=" style='color:red'";
               }


	echo "<textarea name='eval_puntos_debiles' style='width:100%;' rows=5 $readonly_pd  $estilo>";
    echo $puntos_debiles;
    echo "</textarea></td>";
	echo "</tr><tr>";
	echo "<td colspan=2><table border=0 width=100% cellpadding=3>";
	echo "<tr><td align=left width=33%><b>Evaluador:</b>";
	echo "<select name=eval_evaluador>";
//	echo "<option value=''>No Asignado";
	$sql = "SELECT nombre,apellido,id_evaluador ";
	$sql .= "FROM evaluadores LEFT JOIN usuarios USING (id_usuario) ";
	$sql .= "ORDER BY apellido,nombre";
	$result_eval = sql($sql) or fin_pagina();
	while (!$result_eval->EOF) {
		echo "<option value='".$result_eval->fields["id_evaluador"]."'";
		if ($id_evaluador == $result_eval->fields["id_evaluador"]) echo " selected";
		echo ">".$result_eval->fields["apellido"]." ".$result_eval->fields["nombre"];
		$result_eval->MoveNext();
	}
	echo "</select></td>";
	echo "<td align=center width=34%>";
    //echo "<b>Fecha y Hora: </b>$fecha";
    echo "</td>";
	echo "<td align=right width=33%><b>Historial: </b>";
	echo "<input type=checkbox name=eval_historial";
	if ($estado == "HISTORIAL") { echo " checked"; }
	echo "></td></tr></table></td>";
	echo "</tr><tr>";
	echo "<td style=\"border:$bgcolor3;\" align=center colspan=2><br>";
	if ($es_evaluador) {
		echo "<input type='submit' name='eval_guardar' value='Guardar'>&nbsp;&nbsp;&nbsp;";
	}
	echo "<input type='button' name='volver' value='Volver al listado' onClick=\"document.location='listado_evaluaciones.php';\">";
	echo "<input type='hidden' name=eval_fecha value='$fecha_db'>";
	echo "<input type='hidden' name=eval_id_evaluacion value='$id'>";
	echo "<br><br></td></tr>";
	echo "</table></form><br>";
}

fin_pagina();
?>