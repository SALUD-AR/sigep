<?php
/*
$Author: nazabal $
$Revision: 1.1 $
$Date: 2004/02/28 21:21:18 $
*/

require "../../config.php";

echo $html_header;

if ($_POST["eval_agregar"]) {
	$error = 0;
	$id_usuario = $_POST["eval_usuario"] or Error("Debe seleccionar el usuario para agregar");
	if (!$error) {
		$sql = "INSERT INTO evaluadores (id_usuario) VALUES ($id_usuario)";
		$result = sql($sql) or fin_pagina();
		Aviso("El evaluador se agrego correctamente");
	}
}
if ($_POST["eval_borrar"]) {
	$error = 0;
	$seleccionados = $_POST["eval_seleccionados"] or Error("No se seleccionó ningún usuario para eliminar");
	if (!$error) {
		$sql_array = array();
		foreach ($seleccionados as $id_evaluador) {
			$sql_array[] = "DELETE FROM evaluadores WHERE id_evaluador=$id_evaluador";
		}
		$result = sql($sql_array) or fin_pagina();
		Aviso("Los evaluadores seleccionados se eliminaron correctamente");
	}
}

echo "<br><form action='".$_SERVER["PHP_SELF"]."' method=POST>";
echo "<table align=center>";
echo "<tr><td align=right><b>Usuario del sistema:</b></td>";
echo "<td align=left><select name=eval_usuario>";
$sql = "SELECT id_usuario,nombre,apellido,id_evaluador ";
$sql .= "FROM usuarios ";
$sql .= "LEFT JOIN evaluadores USING (id_usuario) ";
$sql .= "ORDER BY apellido,nombre";
$result = sql($sql) or fin_pagina();
$usuario = array();
$evaluadores = array();
while (!$result->EOF) {
	if ($result->fields["id_evaluador"] == "") {
		$usuarios[$result->fields["id_usuario"]] = $result->fields["apellido"]." ".$result->fields["nombre"];
	}
	else {
		$evaluadores[$result->fields["id_evaluador"]] = $result->fields["apellido"]." ".$result->fields["nombre"];
	}
	$result->MoveNext();
}
foreach ($usuarios as $id_usuario => $nombre) {
	echo "<option value='$id_usuario'>$nombre";
}
echo "</select><input type=submit name=eval_agregar value='Agregar como evaluador'></td></tr>";
echo "</table><br>";
if (count($evaluadores) > 0) {
	echo "<table border=1 width=80% cellspacing=0 cellpadding=3 bgcolor=$bgcolor2 align=center>";
	echo "<tr><td id=mo style='border:$bgcolor3'>Evaluadores</td></tr>";
//	echo "<tr><td id=mo>Nombre</td></tr>";
	foreach ($evaluadores as $id_evaluador => $nombre) {
		echo "<tr>";
		echo "<td align=left>";
		echo "<input type=checkbox name=eval_seleccionados[] value='$id_evaluador'>";
		echo "&nbsp;<b>$nombre</b></td>";
		echo "</tr>\n";
	}
	echo "<tr><td align=center style='border:$bgcolor3'><br><input type=submit name=eval_borrar value='Eliminar seleccionados'><br><br></td></tr>";
	echo "</table><br>\n";
}
echo "</form>\n";

fin_pagina();
?>