<?php

include "../../config.php";
echo $html_header;

$cmd = $_POST["cmd"];
$login = $_POST["login"];
$password = $_POST["password"];
$nombre = $_POST["nombre"];
$apellido = $_POST["apellido"];
$direccion = $_POST["direccion"];
$telefono = $_POST["telefono"];
$celular = $_POST["celular"];
$mail = $_POST["mail"];
$comentario = $_POST["comentario"];

if ($cmd=="Guardar") {
	$sql="select login from usuarios where login='$login'";
	$rs = $db->Execute($sql) or die($db->ErrorMsg());
	if ($rs->RecordCount()!=0 or !$login or strstr($login," "))
		error ("El usuario a sido mal ingresado.");
	elseif (!$password)
		error ("Debe ingresar una contrase�a.");
	elseif (!$nombre)
		error ("Debe ingresar el nombre.");
	elseif (!$apellido)
		error ("Debe ingresar el apellido.");
	elseif (!strstr($mail,"@") && $mail)
		error ("El mail a sido mal ingresado.");
	else {
		  $sql = "INSERT INTO usuarios ";
		  $sql .= "(login,passwd,nombre,apellido,direccion,telefono,celular,mail,comentarios)";
		  $sql .= "VALUES ('$login','".md5($password)."','$nombre','$apellido','$direccion','$telefono','$celular','$mail','$comentario')";
		  if ($db->Execute($sql)) {
			  include "../../lib/gacl_api.class.php";
			  $gacl_api = new gacl_api();
			  if (!$gacl_api->add_object('usuarios','$nombre $apellido',$login,1,0,'aco')){
				   $sql="DELETE usuarios WHERE login='$login'";
				   $db->Execute($sql);
			  }
			  else {
				   $sql = "INSERT INTO phpss_account ";
				   $sql .= "(username,password,active)";
				   $sql .= "VALUES ('$login','".md5("$password")."','true')";
				   if ($db->Execute($sql)) {
					   Aviso("Los datos se ingresaron con exito.");
					   $login = "";
					   $password = "";
					   $nombre = "";
					   $apellido = "";
					   $direccion = "";
					   $telefono = "";
					   $celular = "";
					   $mail = "";
					   $comentario = "";
				   }
				   else {
					   $sql="DELETE FROM usuarios WHERE login='$login'";
					   $db->Execute($sql);
					   $id_obj = $gacl_api->get_object_id('usuarios',$login,'aco');
					   if ($id_obj) {
						   $gacl_api->del_object($id_obj,'aro',1);
					   }
				   }
			  }
		  }
		  else {
			  Aviso("No se pudo ingresar el nuevo usuario a la base de datos");
		  }
	}
}
?>
<table width=100% cellpadding=0 cellspacing=6 bgcolor='<? echo $bgcolor2; ?>'>
<tr>
<td>
<center><p style='font-size: 18;'><font color=#0000cc>Registro de Usuario</font></p></center>
<br>
<center>Llen� este formulario para generara un nuevo usuario.<br>
Asegurese de que los datos esten bien escritos, y que no haya coinsidencia<br>
en el login del usuario.</br></br>
<b>NOTA</b> Tenga en cuenta que los campos marcados con <font color=red>*</font> son indispensables.
<form action='usuario_nuevo.php' method='POST' name=frm id=frm>
<table width=325 align=center border="1" cellpadding="2" cellspacing="0" style="border-collapse: collapse; " bordercolor="#9A9A9A">
 <tr>
  <td align=center>
   <p class=menutitulo style='margin-bottom: 0;'>Formulario de Registro</p>
  </td>
 </tr>
 <tr>
  <td>
   <table width=100% border=0>
	<tr>
	 <td>
	  <font color=red>*</font> Usuario:
	 </td>
	 <td>
	  <input type='text' name=login value='<? echo $login;?>' size=20>
	 </td>
	</tr>
	<tr>
	 <td>
	  <font color=red>*</font> Contrase�a:
	 </td>
	 <td>
	  <input type='password' name=password value='<? echo $password;?>' size=20>
	 </td>
	</tr>
   </table>
  </td>
 </tr>
 <tr>
  <td align=center>
   <p class=menutitulo style='margin-bottom: 0;'>Datos Personales</p>
  </td>
 </tr>
 <tr>
  <td>
   <table width=100% border=0>
	<tr>
	 <td>
	  <font color=red>*</font> Nombre:
	 </td>
	 <td>
	  <input type='text' name=nombre value='<? echo $nombre;?>' size=20>
	 </td>
	</tr>
	<tr>
	 <td>
	  <font color=red>*</font> Apellido:
	 </td>
	 <td>
	  <input type='text' name=apellido value='<? echo $apellido;?>' size=20>
	 </td>
	</tr>
	<tr>
	 <td>
	  Direcci�n:
	 </td>
	 <td>
	  <input type='text' name=direccion value='<? echo $direccion;?>' size=20>
	 </td>
	</tr>
	<tr>
	 <td>
	  Tel�fono:
	 </td>
	 <td>
	  <input type='text' name=telefono value='<? echo $telefono;?>' size=20>
	 </td>
	</tr>
	<tr>
	 <td>
	  M�vil:
	 </td>
	 <td>
	  <input type='text' name=celular value='<? echo $celular;?>' size=20>
	 </td>
	</tr>
	<tr>
	 <td>
	  E-mail:
	 </td>
	 <td>
	  <input type='text' name=mail value='<? echo $mail;?>' size=20>
	 </td>
	</tr>
	<tr>
	 <td valign=top>
	  Comentario:
	 </td>
	 <td>
	  <textarea name=comentario rows=5 cols=15><? echo $comentario; ?></textarea>
	 </td>
	</tr>
	<tr>
	 <td colspan=2 align=center>
	  <input type='submit' name='cmd' value='Guardar'>
	 </td>
	</tr>
   </table>
  </td>
 </tr>
</table>
</form>
</td>
</tr>
</table>