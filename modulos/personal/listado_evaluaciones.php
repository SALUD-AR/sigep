<?php
/*
$Author: fernando $
$Revision: 1.5 $
$Date: 2005/03/09 13:48:36 $
*/

require "../../config.php";

echo $html_header;

variables_form_busqueda("eval");

if ($cmd == "") {
	$cmd="pendiente";
	$_ses_eval["cmd"] = $cmd;
	phpss_svars_set("_ses_eval", $_ses_eval);
}
if ($_POST["nuevo_periodo"]) {
	echo "<form action='listado_evaluaciones.php' method='post'>";
	echo "<br><br><table align=center cellspacing=2 cellpadding=5 border=1 bgcolor=$bgcolor3>";
	echo "<tr><td align=left id=mo>Nuevo Período</td></tr>";
	echo "<tr><td align=center><b>Nombre del Período:</b>&nbsp;";
	echo "<input type=text name=periodo></td>";
	echo "</tr><tr>";
	echo "<td align=center colspan=2>";
	echo "<input type=hidden name=agregar_periodo value=1>";
	echo "<input style='width:160;' type=submit name=agregar value='Agregar'>&nbsp;&nbsp;&nbsp;";
	echo "<input style='width:160;' type='button' name='volver' value='Volver al listado' onClick=\"document.location='listado_evaluaciones.php';\"></td>";
	echo "</tr></table>";
	fin_pagina();
}
if ($_POST["agregar_periodo"]) {
	$nombre_periodo = $_POST["periodo"] or Error("Debe ingresar el nombre del período");
	if (!$error) {
		$sql = "SELECT id_evaluacion FROM evaluaciones WHERE periodo='$nombre_periodo'";
		$result = sql($sql) or fin_pagina();
		if ($result->Recordcount() > 0) {
			Error("Ya existe un período con el nombre \"$nombre_periodo\"");
		}
		else {
			$sql = "SELECT id_legajo FROM legajos";
			$result = sql($sql) or fin_pagina();
			$sql_array = array();
			while (!$result->EOF) {
				$sql_array[] = "INSERT INTO evaluaciones (periodo,id_legajo) VALUES ('$nombre_periodo',".$result->fields["id_legajo"].")";
				$result->MoveNext();
			}
			sql($sql_array) or fin_pagina();
			Aviso("El período \"$nombre_periodo\" se cargo correctamente");
		}
	}
}
$datos_barra = array(
					array(
						"descripcion"	=> "En proceso",
						"cmd"			=> "pendiente"
						),
					array(
						"descripcion"	=> "Historial",
						"cmd"			=> "historial"
						)
);

generar_barra_nav($datos_barra);

	$orden = array(
		"default" => "1",
		"1" => "periodo",
		"2" => "legajos.apellido",
		"3" => "legajos.nombre",
		"4" => "calificacion",
		"5" => "usuarios.apellido",
		"6" => "usuarios.nombre"
	);
	
	$filtro = array(
		"periodo" => "Periodo",
		"legajos.nombre" => "Nombre",
        "legajos.apellido"=>"Apellido",
		"calificacion" => "Calificación",
		"usuarios.nombre" => "Evaluador"
	);
	$itemspp = 20;
	
//	$fecha_hoy = date("Y-m-d",mktime());
//	echo "<br><center><font size=3><b>Listado de evaluaciones</b></font><br></center>\n";
	echo "<form action='listado_evaluaciones.php' method='post'>";
	echo "<table cellspacing=2 cellpadding=5 border=0 bgcolor=$bgcolor3 width=100% align=center>\n";
	echo "<tr><td align=center>\n";
	$sql_tmp = "SELECT periodo,legajos.apellido AS leg_apellido,legajos.nombre AS leg_nombre,";
	$sql_tmp .= "calificacion,usuarios.apellido AS usr_apellido,usuarios.nombre AS usr_nombre,";
	$sql_tmp .= "id_evaluacion ";
	$sql_tmp .= "FROM evaluaciones ";
	$sql_tmp .= "LEFT JOIN legajos USING (id_legajo) ";
	$sql_tmp .= "LEFT JOIN evaluadores USING (id_evaluador) ";
	$sql_tmp .= "LEFT JOIN usuarios ON evaluadores.id_usuario=usuarios.id_usuario ";

	$where_tmp .= "estado='".strtoupper($cmd)."' ";
    $where_tmp.= " and legajos.id_legajo in
                    (select id_legajo
                              from personal.legajos
                              left join sistema.usuarios using (id_usuario)
                              JOIN permisos.phpss_account ON (usuarios.login=phpss_account.username  and phpss_account.active='true')
                  )";
	list($sql,$total_leg,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");
	echo "&nbsp;&nbsp;<input type=submit name=buscar value='Buscar'>\n";
	echo "</td></tr></table><br>\n";
	$result = sql($sql) or die;
	echo "<table border=0 width=95% cellspacing=2 cellpadding=3 bgcolor=$bgcolor3 align=center>";
	echo "<tr><td colspan=4 align=left id=ma>\n";
	echo "<table width=100%><tr id=ma>\n";
	echo "<td width=30% align=left><b>Total:</b> $total_leg evaluaciones.</td>\n";
	echo "<td width=70% align=right>$link_pagina\n</td>\n";
	echo "</tr></table>\n";
	echo "</td></tr><tr>\n";
	echo "<td align=right id=mo><a id=mo href='".encode_link("listado_evaluaciones.php",array("sort"=>"1","up"=>$up))."'>Período</td>\n";
	echo "<td align=right id=mo>Evaluado (<a id=mo href='".encode_link("listado_evaluaciones.php",array("sort"=>"2","up"=>$up))."'>Apellido, </a>";
	echo "<a id=mo href='".encode_link("listado_evaluaciones.php",array("sort"=>"3","up"=>$up))."'>Nombre</a>)</td>";
	echo "<td align=right id=mo><a id=mo href='".encode_link("listado_evaluaciones.php",array("sort"=>"4","up"=>$up))."'>Calificación</td>\n";
	echo "<td align=right id=mo>Evaluador (<a id=mo href='".encode_link("listado_evaluaciones.php",array("sort"=>"5","up"=>$up))."'>Apellido, </a>";
	echo "<a id=mo href='".encode_link("listado_evaluaciones.php",array("sort"=>"6","up"=>$up))."'>Nombre</a>)</td>";
	echo "</tr>\n";
	while (!$result->EOF) {
		if ($result->fields["calificacion"]) {
			$calificacion = formato_money($result->fields["calificacion"]);
		}
		else {
			$calificacion = "No evaluado";
		}
		if ($result->fields["usr_apellido"] and $result->fields["usr_nombre"]) {
			$evaluador = $result->fields["usr_apellido"].", ".$result->fields["usr_nombre"];
		}
		else {
			$evaluador = "No asignado";
		}
		$ref = encode_link("modificar_evaluacion.php",array("cmd"=>"modificar","id_evaluacion"=>$result->fields["id_evaluacion"]));
		tr_tag($ref);
		echo "<td align=center width=20%>&nbsp;".$result->fields["periodo"]."</td>\n";
		echo "<td align=left width=30%><b>".$result->fields["leg_apellido"].", ".$result->fields["leg_nombre"]."</td>\n";
		echo "<td align=center width=20%>".$calificacion."</td>\n";
		echo "<td align=left width=30%><b>$evaluador</td>\n";
		echo "</tr>\n";
		$result->MoveNext();
	}
	echo "</table><br>\n";
	echo "<center><input type=submit name=nuevo_periodo value='Cargar un Nuevo Período'></center>";
	echo "</form>\n";

fin_pagina();
?>