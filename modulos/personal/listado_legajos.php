<?php
/*
$Author: marco_canderle $
$Revision: 1.14 $
$Date: 2006/01/04 10:47:37 $
*/

require "../../config.php";
variables_form_busqueda("personal");
echo $html_header;


	$orden = array(
		"default" => "1",
		"1" => "apellido",
		"2" => "nombre",
		"3" => "domicilio",
		"4" => "tel_particular"
	);

	$filtro = array(
		"apellido" => "Apellido",
		"nombre" => "Nombre",
		"domicilio" => "Domicilio",
		"tel_particular" => "Teléfono Particular"
	);
	$itemspp = 50;

     $datos_barra = array(
				array(
					"descripcion"    => "Actuales",
					"cmd"            => "actuales"
					),
				array(
					"descripcion"    => "Historial",
					"cmd"            => "historial"
					)
				 );


     if (!$cmd) $cmd="actuales";

     if ($cmd=="actuales") $activo=1;
     if ($cmd=="historial") $activo=0;
//	$fecha_hoy = date("Y-m-d",mktime());
//	echo "<br><center><font size=3><b>Listado de legajos</b></font><br></center>\n";
	echo "<form action='listado_legajos.php' method='post'>";
    if($parametros['accion']!=""){
      Aviso($parametros['accion']);
    }
	echo "<table cellspacing=2 cellpadding=5 border=0 bgcolor=$bgcolor3 width=100% align=center>\n";

    echo "<tr><td>";
    generar_barra_nav($datos_barra);
    echo "</tr></td>";
	echo "<tr><td align=center>\n";
	$sql_tmp = "SELECT id_legajo,apellido,nombre,domicilio,tel_particular ";
	$sql_tmp .= "FROM legajos ";

	$where_tmp =" activo=$activo";

	list($sql,$total_leg,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");
	echo "&nbsp;&nbsp;<input type=submit name=buscar value='Buscar'>\n";
	echo "</td></tr></table><br>\n";
	echo "</form>\n";
	$result = sql($sql) or die;
	echo "<table border=0 width=95% cellspacing=2 cellpadding=3 bgcolor=$bgcolor3 align=center>";
	echo "<tr><td colspan=3 align=left id=ma>\n";
	echo "<table width=100%><tr id=ma>\n";
	echo "<td width=30% align=left><b>Total:</b> $total_leg legajo/s.</td>\n";
	echo "<td width=70% align=right>$link_pagina\n</td>\n";
	echo "</tr></table>\n";
	echo "</td></tr><tr>\n";
	echo "<td align=right id=mo><a id=mo href='".encode_link("listado_legajos.php",array("sort"=>"1","up"=>$up))."'>Apellido, </a>";
	echo "<a id=mo href='".encode_link("listado_legajos.php",array("sort"=>"2","up"=>$up))."'>Nombre</td>";
	echo "<td align=right id=mo><a id=mo href='".encode_link("listado_legajos.php",array("sort"=>"3","up"=>$up))."'>Domicilio</td>\n";
	echo "<td align=right id=mo><a id=mo href='".encode_link("listado_legajos.php",array("sort"=>"4","up"=>$up))."'>Teléfono Particular</td>\n";
	echo "</tr>\n";
	while (!$result->EOF) {
		$ref = encode_link("modificar_legajo.php",array("cmd"=>"modificar","id_legajo"=>$result->fields["id_legajo"],"pagina"=>"listado_legajo"));
		tr_tag($ref);
		echo "<td align=left width=30%><b>".$result->fields["apellido"].", ".$result->fields["nombre"]."</td>\n";
		echo "<td align=left width=40%>&nbsp;".$result->fields["domicilio"]."</td>\n";
		echo "<td align=center width=30%>&nbsp;".$result->fields["tel_particular"]."</td>\n";
		echo "</tr>\n";
		$result->MoveNext();
	}
	echo "</table>\n";
$sql = "SELECT id_legajo FROM legajos WHERE id_usuario=".$_ses_user["id"];
$result = sql($sql) or fin_pagina();
if ($result->RecordCount() == 0) {
	echo "<br><form action=modificar_legajo.php method=post>";
	echo "<input type=hidden name=id_usuario value='".$_ses_user["id"]."'>";
	echo "<center><input style='width:160;' type=submit name=legajo_personal value='Agregue aquí su legajo'></center>";
	echo "</form>";
}


	echo "<br><form action=modificar_legajo.php method=post>";
	echo "<center><input style='width:160;' type=submit name=nuevo_legajo value='Nuevo legajo'></center>";
	echo "</form>";

fin_pagina();
?>