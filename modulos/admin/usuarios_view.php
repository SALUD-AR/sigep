<?

require_once("../../config.php");

echo $html_header;

variables_form_busqueda("usr_view");

if ($_POST["edit_aceptar"]) {
	$sql_array = array();
	$edit_id = $_POST["edit_id"];
	$edit_login = $_POST["edit_login"];
	$edit_activo = $_POST["edit_activo"];
	$edit_acceso_remoto = ($_POST["edit_acceso_remoto"]==0 or $_POST["edit_acceso_remoto"]==1)?$_POST["edit_acceso_remoto"]:0;
	$edit_nombre = $_POST["edit_nombre"];
	$edit_apellido = $_POST["edit_apellido"];
	$edit_direccion = $_POST["edit_direccion"];
	$edit_telefono = $_POST["edit_telefono"];
	$edit_celular = $_POST["edit_celular"];
	$edit_mail = $_POST["edit_mail"];
	$edit_comentarios = $_POST["edit_comentarios"];
	$sql = "UPDATE phpss_account SET active='$edit_activo' ";
	if ($_POST['new_pass'] != "") {
		$sql .= ", password='".md5($_POST['new_pass'])."' ";
	}
	$sql .= "WHERE username='$edit_login'";
	$sql_array[] = $sql;
	$sql = "UPDATE usuarios SET ";
	$sql .= "nombre='$edit_nombre',";
	$sql .= "apellido='$edit_apellido',";
	$sql .= "direccion='$edit_direccion',";
	$sql .= "telefono='$edit_telefono',";
	$sql .= "celular='$edit_celular',";
	$sql .= "mail='$edit_mail',";
	$sql .= "comentarios='$edit_comentarios', ";
	$sql .= "acceso_remoto=$edit_acceso_remoto ";
	if ($_POST['new_pass'] != "") {
		$sql .= ", passwd='".md5($_POST['new_pass'])."' ";
	}
	$sql .= "WHERE id_usuario=$edit_id";
	$sql_array[] = $sql;
	sql($sql_array) or reportar_error($sql_array,__FILE__,__LINE__);
	detalle_usuario($edit_id);
}
elseif ($_POST["editar"]) {
	$id_usuario = $_POST["id_usuario"];
	$sql = "SELECT usuarios.*,phpss_account.active ";
	$sql .= "FROM usuarios LEFT JOIN phpss_account ";
	$sql .= "ON usuarios.login = phpss_account.username ";
	$sql .= "WHERE id_usuario=$id_usuario";
	$result = sql($sql) or die;
	$fila = $result->FetchRow();
	cargar_calendario();
	$link_volver = encode_link("usuarios_view.php",array("cmd" => "detalle","id_usuario" => $id_usuario));

	echo "<br>
	<form method=post action=usuarios_view.php>
	<input type=hidden name=edit_id value=$id_usuario>
	<input type=hidden name=edit_login value=".$fila['login'].">
	<table align=center cellpadding=5 cellspacing=0 bgcolor=$bgcolor2>
		<tr><td colspan=2 id=mo>Datos del usuario</td></tr>
		<tr><td align=right><b>Activo:</b></td>
		<td align=left>
		<select name=edit_activo>
		<option value='true'".(($fila['active'] == "true") ? " selected>" : ">").$sino['true']."
		<option value='false'".(($fila['active'] == "false") ? " selected>" : ">").$sino['false']."
		</select>
		</td>
		</tr>
		<tr><td align=right><b>Acceso Remoto:</b></td>
		<td align=left>
		<select name=edit_acceso_remoto>
		<option value='1'".(($fila['acceso_remoto'] == "1") ? " selected>" : ">").$sino['1']."
		<option value='0'".(($fila['acceso_remoto'] == "0") ? " selected>" : ">").$sino['0']."
		</select>
		</td>
		</tr>
		<tr><td align=right><b>Login:</b></td>
		<td align=left>".$fila['login']."</td>
		</tr>
		<tr><td align=right><b>Nueva contraseña:</b></td>
		<td align=left><input type=password size=30 name=new_pass value=''></td>
		</tr>
		<tr><td align=right><b>Nombre:</b></td>
		<td align=left><input type=text size=30 name=edit_nombre value='".$fila['nombre']."'></td>
		</tr>
		<tr><td align=right><b>Apellido:</b></td>
		<td align=left><input type=text size=30 name=edit_apellido value='".$fila['apellido']."'></td>
		</tr>
		<tr><td align=right><b>Direción:</b></td>
		<td align=left><input type=text size=30 name=edit_direccion value='".$fila['direccion']."'></td>
		</tr>
		<tr><td align=right><b>Teléfono:</b></td>
		<td align=left><input type=text size=30 name=edit_telefono value='".$fila['telefono']."'></td>
		</tr>
		<tr><td align=right><b>Celular:</b></td>
		<td align=left><input type=text size=30 name=edit_celular value='".$fila['celular']."'></td>
		</tr>
		<tr><td align=right><b>E-Mail:</b></td>
		<td align=left><input type=text size=30 name=edit_mail value='".$fila['mail']."'></td>
		</tr>
		<tr><td align=right><b>Fecha de alta:</b></td>
		<td align=left>".Fecha($fila['fecha_alta'])."</td>
		</tr>
		<tr><td align=right><b>Comentarios:</b></td>
		<td>&nbsp;</td></tr>
		<td align=center colspan=2>
		<textarea name=edit_comentarios cols=45 rows=5>".$fila['comentarios']."</textarea>
		</td>
		</tr>
		<tr><td colspan=2 align=center>
		<input type=submit style='width: 70;' name=edit_aceptar value='Aceptar'>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type=button style='width: 70;' name=volver value='Cancelar' onClick=\"javascript:document.location='$link_volver';\">
		</td>
		</tr>
		</table>
		</form>";
	exit;
}
elseif ($parametros["cmd"] == "detalle") {
	$id_usuario = $parametros["id_usuario"];
	detalle_usuario($id_usuario);
}
else {
	listado_usuarios();
}
function detalle_usuario($id_usuario) {
	global $bgcolor1,$bgcolor2,$bgcolor4,$sino,$permisos;
	$sql = "SELECT usuarios.*,phpss_account.active ";
	$sql .= "FROM usuarios LEFT JOIN phpss_account ";
	$sql .= "ON usuarios.login = phpss_account.username ";
	$sql .= "WHERE id_usuario=$id_usuario";
	$result = sql($sql) or die;
	$fila = $result->FetchRow();
	echo "<br>
	<table align=center cellpadding=5 cellspacing=0 bgcolor=$bgcolor2>
		<tr><td colspan=2 id=mo>Datos del usuario</td></tr>
		<tr><td align=right><b>Activo:</b></td>
		<td align=left>".$sino[$fila['active']]."</td>
		</tr>
		<tr><td align=right><b>Acceso Remoto:</b></td>
		<td align=left>".$sino[$fila['acceso_remoto']]."</td>
		</tr>
		<tr><td align=right><b>Login:</b></td>
		<td align=left>".$fila['login']."</td>
		</tr>
		<tr><td align=right><b>Nombre:</b></td>
		<td align=left>".$fila['nombre']." ".$fila['apellido']."</td>
		</tr>
		<tr><td align=right><b>Direción:</b></td>
		<td align=left>".$fila['direccion']."</td>
		</tr>
		<tr><td align=right><b>Teléfono:</b></td>
		<td align=left>".$fila['telefono']."</td>
		</tr>
		<tr><td align=right><b>Celular:</b></td>
		<td align=left>".$fila['celular']."</td>
		</tr>
		<tr><td align=right><b>E-Mail:</b></td>
		<td align=left>".$fila['mail']."</td>
		</tr>
	    <tr>
	    <td align='center' colspan='2'>
	      <hr>
	      <b>Firma</b></td>
	    </tr>
	    <tr>
	    <td align='center' colspan='2'>
	    ".$fila['firma1']."<br>
	    ".$fila['firma2']."<br>
	    ".$fila['firma3']."<br>
	    <hr>
	    </td>
	    </tr>
		<tr><td align=right><b>Fecha de alta:</b></td>
		<td align=left>".Fecha($fila['fecha_alta'])."</td>
		</tr>
		<tr><td align=right><b>Comentarios:</b></td>
		<td>&nbsp;</td></tr>
		<tr><td align=left colspan=2>".$fila['comentarios']."</td>
		</tr>";
	echo "<tr><td colspan=2 align=center>";
	echo "<form method=post action=usuarios_view.php>";
	
	if (permisos_check("sistema","usuarios_edit")) {
		echo "<input type=hidden name=id_usuario value=$id_usuario>";
		echo "<input type=submit style='width: 70;' name=editar value='Modificar'>&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	echo "<input type=button style='width: 70;' value='Volver' onClick=\"document.location='".$_SERVER["PHP_SELF"]."';\">";
	echo "</form></td>";
	echo "</tr>";
	echo "</table>";
	exit;
}

function listado_usuarios() {
	global $bgcolor3,$up,$itemspp,$sino;
	$orden = array(
		"default" => "1",
		"1" => "usuarios.login",
		"2" => "usuarios.nombre",
		"3" => "usuarios.comentarios",
		"4" => "phpss_account.active",
		"5" => "usuarios.acceso_remoto"
	);
	
	$filtro = array(
		"usuarios.login" => "Login",
		"usuarios.nombre" => "Nombre",
		"usuarios.apellido" => "Apellido",
		"usuarios.comentarios" => "Comentarios"
	);
	$itemspp = 20;
	
	$fecha_hoy = date("Y-m-d",mktime());
	echo "<br><center><font size=3><b>Administración de Usuarios</b></font><br></center>\n";
	echo "<form action='usuarios_view.php' method='post'>";
	echo "<table cellspacing=2 cellpadding=5 border=0 bgcolor=$bgcolor3 width=100% align=center>\n";
	echo "<tr><td align=center>\n";
	$sql_tmp = "SELECT usuarios.id_usuario,usuarios.login,";
	$sql_tmp .= "usuarios.nombre,usuarios.apellido,";
	$sql_tmp .= "usuarios.comentarios,usuarios.acceso_remoto, phpss_account.active ";
	$sql_tmp .= "FROM usuarios LEFT JOIN phpss_account ";
	$sql_tmp .= "ON usuarios.login = phpss_account.username";
	list($sql,$total_usr,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");
	echo "&nbsp;&nbsp;<input type=submit name=buscar value='Buscar'>\n";
	echo "</td></tr></table><br>\n";
	echo "</form>\n";
	$result = sql($sql) or die;
	echo "<table border=0 width=95% cellspacing=2 cellpadding=3 bgcolor=$bgcolor3 align=center>";
	echo "<tr><td colspan=5 align=left id=ma>\n";
	echo "<table width=100%><tr id=ma>\n";
	echo "<td width=30% align=left><b>Total:</b> $total_usr usuarios.</td>\n";
	echo "<td width=70% align=right>$link_pagina\n</td>\n";
	echo "</tr></table>\n";
	echo "</td></tr><tr>\n";
	echo "<td align=right id=mo width=15%><a id=mo href='".encode_link("usuarios_view.php",array("sort"=>"1","up"=>$up))."'>Login</a></td>\n";
	echo "<td align=right id=mo width=20%><a id=mo href='".encode_link("usuarios_view.php",array("sort"=>"2","up"=>$up))."'>Nombre</td>\n";
	echo "<td align=right id=mo width=55%><a id=mo href='".encode_link("usuarios_view.php",array("sort"=>"3","up"=>$up))."'>Comentarios</td>\n";
	echo "<td align=right id=mo width=5%><a id=mo href='".encode_link("usuarios_view.php",array("sort"=>"4","up"=>$up))."'>Activo</td>\n";
	echo "<td align=right id=mo width=5%><a id=mo href='".encode_link("usuarios_view.php",array("sort"=>"5","up"=>$up))."'>Acceso Remoto</td>\n";
	echo "</tr>\n";
	while (!$result->EOF) {
		$ref = encode_link("usuarios_view.php",array("cmd"=>"detalle","id_usuario"=>$result->fields["id_usuario"]));
		tr_tag($ref);
		echo "<td align=left><b>".$result->fields["login"]."</b></td>\n";
		echo "<td align=left>".$result->fields["nombre"]." ".$result->fields["apellido"]."</td>\n";
		echo "<td align=left>&nbsp;".html_out($result->fields["comentarios"])."</td>\n";
		echo "<td align=center>&nbsp;".$sino[$result->fields["active"]]."</td>\n";
		echo "<td align=center>&nbsp;".$sino[$result->fields["acceso_remoto"]]."</td>\n";
		echo "</tr>\n";
		$result->MoveNext();
	}
	echo "</table><br>\n";
}
fin_pagina();
?>