<?

require_once("../../config.php");

echo $html_header;
//$db->debug = true;
variables_form_busqueda("usr_con");

if ($cmd == "") {
	$cmd="conectados";
	$_ses_usr_con["cmd"] = $cmd;
	phpss_svars_set("_ses_usr_con", $_ses_usr_con);
}

$datos_barra = array(
					array(
						"descripcion"	=> "Usuarios Conectados",
						"cmd"			=> "conectados"
						),
					array(
						"descripcion"	=> "Historial de conexiones",
						"cmd"			=> "historial"
						)
				 );
generar_barra_nav($datos_barra);

if ($_POST["desconectar_usuario"]) {
	if (is_array($_POST["borrar_sesion"])) {
		$where = "id = ";
		$where .= join(" OR id = ",$_POST["borrar_sesion"]);
	}
	elseif ($_POST["borrar_sesion"]) {
		$where = "id = ".$_POST["borrar_sesion"];
	}
	else {
		Error("Debe seleccionar algún usuario");
	}
	$sql = "UPDATE phpss_session SET active='false' WHERE $where";
	sql($sql) or die;
}

if ($cmd == "conectados") {
	conectados();
}
elseif ($cmd == "historial") {
	historial();
}
function conectados() {
	global $session_timeout,$bgcolor2,$bgcolor3,$up,$itemspp,$cmd;
	$orden = array(
		"default_up" => "1",
		"default" => "2",
		"1" => "usuarios.login",
		"2" => "usuarios.nombre",
		"3" => "phpss_ip.ip",
		"4" => "phpss_session.created",
		"5" => "phpss_session.lastrequest"
	);
	$filtro = array(
		"usuarios.login" => "Login",
		"usuarios.nombre" => "Nombre",
		"phpss_ip.ip" => "IP"
	);
	$itemspp = 20;
	$d = date("d");
	$m = date("m");
	$y = date("Y");
	$h = date("H");
	$i = date("i");
	$s = date("s");
	$fecha_actual = mktime($h,$i,$s,$m,$d,$y);
	$fecha_limite = date("Y-m-d H:i:s",($fecha_actual - ($session_timeout * 60)));
	echo "<form action='usuarios_conectados.php' method='post'>";
	echo "<table cellspacing=2 cellpadding=5 border=0 width=100% align=center>\n";
	echo "<tr><td align=center>\n";
	$sql_tmp = "SELECT phpss_session.id,ip,created,lastrequest,login,
		textcat(textcat(usuarios.nombre,' '),usuarios.apellido) as usuario
		FROM permisos.phpss_session
		LEFT JOIN permisos.phpss_account ON phpss_account.id = phpss_session.accountfid
		LEFT JOIN permisos.phpss_ip ON phpss_ip.id = phpss_session.ipfid
		LEFT JOIN sistema.usuarios ON usuarios.login = phpss_account.username";
	$where_tmp = "(phpss_session.active='true' AND lastrequest > '$fecha_limite')";
	list($sql,$total_usr,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");
	echo "&nbsp;&nbsp;<input type=submit name=form_busqueda value='Buscar'>\n";
	echo "</td></tr></table>\n";
	echo "<div align=center>";
	// Boton Actualizar
	echo "<input type=submit style='width=100;' name=actualizar value='Actualizar' title='Actualizar los datos'>&nbsp;&nbsp;&nbsp;";
	// Boton Desconectar
	echo "<input type=submit style='width=100;' name=desconectar_usuario value='Desconectar' title='Desconectar usuarios seleccionados' onClick=\"return confirm('ADVERTENCIA: Se van a finalizar las sesiones de los usuarios seleccionados.');\">";
	echo "</div><br>";
	$result = sql($sql) or die;
	echo "<table border=0 width=95% cellspacing=2 cellpadding=3 bgcolor=$bgcolor2 align=center>";
	echo "<tr><td colspan=5 align=left id=ma>\n";
	echo "<table width=100%><tr id=ma>\n";
	echo "<td width=30% align=left><b>Total:</b> $total_usr conexiones.</td>\n";
	echo "<td width=70% align=right>$link_pagina\n</td>\n";
	echo "</tr></table>\n";
	echo "</td></tr><tr>\n";
	echo "<td align=right id=mo><a id=mo href='".encode_link("usuarios_conectados.php",array("sort"=>"1","up"=>$up))."'>Login</a></td>\n";
	echo "<td align=right width='35%' id=mo><a id=mo href='".encode_link("usuarios_conectados.php",array("sort"=>"2","up"=>$up))."'>Nombre</td>\n";
	echo "<td align=right id=mo><a id=mo href='".encode_link("usuarios_conectados.php",array("sort"=>"3","up"=>$up))."'>IP</td>\n";
	echo "<td align=right id=mo><a id=mo href='".encode_link("usuarios_conectados.php",array("sort"=>"4","up"=>$up))."'>Inicio</td>\n";
	echo "<td align=right id=mo><a id=mo href='".encode_link("usuarios_conectados.php",array("sort"=>"5","up"=>$up))."'>Inactivo</td>\n";
	echo "</tr>\n";
	while (!$result->EOF) {
		$fecha_last = mktime(date("H",strtotime($result->fields["lastrequest"])),
						 date("i",strtotime($result->fields["lastrequest"])),
						 date("s",strtotime($result->fields["lastrequest"])),
						 date("m",strtotime($result->fields["lastrequest"])),
						 date("d",strtotime($result->fields["lastrequest"])),
						 date("Y",strtotime($result->fields["lastrequest"]))
						);
		$diff = $fecha_actual - $fecha_last;
		if ($diff < 0) $diff = 0;
		$timestamp = mktime(0,0,0,$m,$d,$y) + $diff;
		$inactivo = date("H:i:s", $timestamp);
//		$ref = encode_link("usuarios_conectados.php",array("cmd"=>"detalle","id_usuario"=>$result->fields["id_usuario"]));
//		tr_tag($ref);
		echo "<tr>";
		echo "<td align=left>";
		echo "<input type=checkbox name=borrar_sesion[] value='".$result->fields["id"]."'>&nbsp;";
		echo "<b>".$result->fields["login"]."</b></td>\n";
		echo "<td align=left>".$result->fields["usuario"]."</td>\n";
		echo "<td align=left>".$result->fields["ip"]."</td>\n";
		echo "<td align=center>".Fecha($result->fields["created"])." ".Hora($result->fields["created"])."</td>\n";
		echo "<td align=center>".$inactivo."</td>\n";
		echo "</tr>\n";
		$result->MoveNext();
	}
	echo "</table><br>\n";
	echo "</form>\n";
}

function historial() {
	global $session_timeout,$bgcolor2,$bgcolor3,$up,$itemspp,$cmd;
	$orden = array(
		"default_up" => "0",
		"default" => "4",
		"1" => "usuarios.login",
		"2" => "usuarios.nombre",
		"3" => "phpss_ip.ip",
		"4" => "phpss_session.created",
		"5" => "phpss_session.lastrequest"
	);
	$filtro = array(
		"usuarios.login" => "Login",
		"usuarios.nombre" => "Nombre",
		"phpss_ip.ip" => "IP"
	);
	$itemspp = 20;
	$d = date("d");
	$m = date("m");
	$y = date("Y");
	$h = date("H");
	$i = date("i");
	$s = date("s");
	$fecha_actual = mktime($h,$i,$s,$m,$d,$y);
	$fecha_limite = date("Y-m-d H:i:s",($fecha_actual - ($session_timeout * 60)));
	echo "<form action='usuarios_conectados.php' method='post'>";
	echo "<table cellspacing=2 cellpadding=5 border=0 width=100% align=center>\n";
	echo "<tr><td align=center>\n";
	$sql_tmp = "SELECT phpss_session.id,ip,created,lastrequest,login,
		textcat(textcat(usuarios.nombre,' '),usuarios.apellido) as usuario
		FROM permisos.phpss_session
		LEFT JOIN permisos.phpss_account ON phpss_account.id = phpss_session.accountfid
		LEFT JOIN permisos.phpss_ip ON phpss_ip.id = phpss_session.ipfid
		LEFT JOIN sistema.usuarios ON usuarios.login = phpss_account.username";
	$where_tmp = "(phpss_session.active='false' OR lastrequest <= '$fecha_limite')";
	list($sql,$total_usr,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");
	echo "&nbsp;&nbsp;<input type=submit name=form_busqueda value='Buscar'>\n";
	echo "</td></tr></table>\n";
	echo "<div align=center>";
	// Boton Actualizar
	echo "<input type=submit style='width=100;' name=actualizar value='Actualizar' title='Actualizar los datos'>&nbsp;&nbsp;&nbsp;";
	echo "</div><br>";
	$result = sql($sql) or die;
	echo "<table border=0 width=95% cellspacing=2 cellpadding=3 bgcolor=$bgcolor2 align=center>";
	echo "<tr><td colspan=5 align=left id=ma>\n";
	echo "<table width=100%><tr id=ma>\n";
	echo "<td width=30% align=left><b>Total:</b> $total_usr conexiones.</td>\n";
	echo "<td width=70% align=right>$link_pagina\n</td>\n";
	echo "</tr></table>\n";
	echo "</td></tr><tr>\n";
	echo "<td align=right id=mo><a id=mo href='".encode_link("usuarios_conectados.php",array("sort"=>"1","up"=>$up))."'>Login</a></td>\n";
	echo "<td align=right width='35%' id=mo><a id=mo href='".encode_link("usuarios_conectados.php",array("sort"=>"2","up"=>$up))."'>Nombre</td>\n";
	echo "<td align=right id=mo><a id=mo href='".encode_link("usuarios_conectados.php",array("sort"=>"3","up"=>$up))."'>IP</td>\n";
	echo "<td align=right id=mo><a id=mo href='".encode_link("usuarios_conectados.php",array("sort"=>"4","up"=>$up))."'>Inicio</td>\n";
	echo "<td align=right id=mo><a id=mo href='".encode_link("usuarios_conectados.php",array("sort"=>"5","up"=>$up))."'>Finalización</td>\n";
	echo "</tr>\n";
	while (!$result->EOF) {
/*		$fecha_last = mktime(date("H",strtotime($result->fields["lastrequest"])),
						 date("i",strtotime($result->fields["lastrequest"])),
						 date("s",strtotime($result->fields["lastrequest"])),
						 date("m",strtotime($result->fields["lastrequest"])),
						 date("d",strtotime($result->fields["lastrequest"])),
						 date("Y",strtotime($result->fields["lastrequest"]))
						);
		$diff = $fecha_actual - $fecha_last;
		if ($diff < 0) $diff = 0;
		$timestamp = mktime(0,0,0,$m,$d,$y) + $diff;
		$inactivo = date("H:i:s", $timestamp);*/
//		$ref = encode_link("usuarios_conectados.php",array("cmd"=>"detalle","id_usuario"=>$result->fields["id_usuario"]));
//		tr_tag($ref);
		echo "<tr>";
		echo "<td align=left>";
//		echo "<input type=checkbox name=borrar_sesion[] value='".$result->fields["id"]."'>&nbsp;";
		echo "<b>".$result->fields["login"]."</b></td>\n";
		echo "<td align=left>".$result->fields["usuario"]."</td>\n";
		echo "<td align=left>".$result->fields["ip"]."</td>\n";
		echo "<td align=center>".Fecha($result->fields["created"])." ".Hora($result->fields["created"])."</td>\n";
		echo "<td align=center>".Fecha($result->fields["lastrequest"])." ".Hora($result->fields["lastrequest"])."</td>\n";
		echo "</tr>\n";
		$result->MoveNext();
	}
	echo "</table><br>\n";
	echo "</form>\n";
}
fin_pagina();
?>