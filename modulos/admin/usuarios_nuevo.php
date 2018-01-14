<?php

require_once ("../../config.php");
require_once("../permisos/permisos.class.php");
echo $html_header;
?>
<SCRIPT>
function long_firma(firma1,firma2,firma3)
{
 if(firma1.length>30)
 {alert('La longitud del primer campo de firma no puede superar los 30 caracteres.');
  return false;
 } 	
 if(firma2.length>30)
 {alert('La longitud del segundo campo de firma no puede superar los 30 caracteres.');
  return false;
 } 	
 if(firma3.length>30)
 {alert('La longitud del tercer campo de firma no puede superar los 30 caracteres.');
  return false;
 } 
 return true;	
}
</SCRIPT>

<?
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
$firma1=$_POST["firma1"];
$firma2=$_POST["firma2"];
$firma3=$_POST["firma3"];
$ubicacion=$_POST["ubicacion"];

if ($cmd=="Guardar") {	
	$db->StartTrans();
	$sql="select login from usuarios where login='$login'";
	$rs = sql($sql,"$sql ") or fin_pagina();
	if ($rs->RecordCount()!=0 or !$login or strstr($login," "))
		error ("El usuario a sido mal ingresado.");
	elseif (!$password)
		error ("Debe ingresar una contraseña.");
	elseif (!$nombre)
		error ("Debe ingresar el nombre.");
	elseif (!$apellido)
		error ("Debe ingresar el apellido.");
	elseif (!strstr($mail,"@") && $mail)
		error ("El mail a sido mal ingresado.");
	else {
		  
		  $sql_sec="SELECT nextval('sistema.usuarios_id_usuario_seq') as id_usuario";
		  $res_id=sql($sql_sec,"$sql_sec") or fin_pagina();
		  $id_usuario=$res_id->fields['id_usuario'];		  
		  $sql = "INSERT INTO usuarios ";
		  $sql .= "(id_usuario,login,passwd,nombre,apellido,direccion,telefono,celular,mail,comentarios,firma1,firma2,firma3,pcia_ubicacion,id_lugar_pedido_comida)";
		  $sql .= "VALUES ($id_usuario,'$login','".md5($password)."','$nombre','$apellido','$direccion','$telefono','$celular','$mail','$comentario','$firma1','$firma2','$firma3',$ubicacion,$ubicacion)";
		 //$nuevo_usuario=$login;
		  if ($db->Execute($sql)) {
			  include "../../lib/gacl_api.class.php";
			  $gacl_api = new gacl_api();
			  /*if (!$gacl_api->add_object('usuarios',$nombre ." ". $apellido,$login,1,0,'aco')){
				   $sql="DELETE usuarios WHERE login='$login'";
				   $db->Execute($sql);
				   echo "ere";
			  }*/
			  if (0){}
			  else {
				   $sql = "INSERT INTO phpss_account ";
				   $sql .= "(username,password,active)";
				   $sql .= "VALUES ('$login','".md5("$password")."','true')";				   
				   if ($db->Execute($sql)) {
					  //le agrego permiso item accesible a todos
			          $sql_item="Select id_grupo from permisos.grupos where uname='Items accesibles a todos'";
			          $res_item=sql($sql_item,"ERROR: $sql_item") or fin_pagina();
			          $id_grupo=$res_item->fields['id_grupo'];
			  
			          if ($id_grupo) {
			            $sql_insert="insert into permisos.grupos_usuarios 
			                         (id_grupo,id_usuario) values ($id_grupo,$id_usuario)";
			            sql($sql_insert,"$sql_insert") or fin_pagina();
			           
			            //guardo los permisos en la tabla permisos.permisos_sesion
			            actualizar_permisos_bd($id_usuario);
			           }
				   	
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
	$db->CompleteTrans();
}
?>
<table width=100% cellpadding=0 cellspacing=6 bgcolor='<? echo $bgcolor2; ?>'>
<tr>
<td>
<center><p style='font-size: 18;'><font color=#0000cc>Registro de Usuario</font></p></center>
<br>
<center>Llene este formulario para generar un nuevo usuario.<br>
Asegúrese de que los datos esten bien escritos, y que no haya coincidencia<br>
en el login del usuario.</br></br>
<b>NOTA</b> Tenga en cuenta que los campos marcados con <font color=red>*</font> son indispensables.
<br><br>
<form action='usuarios_nuevo.php' method='POST' name=frm id=frm>
<table width=325 align=center border="1" cellpadding="2" cellspacing="0" style="border-collapse: collapse; " bordercolor="#9A9A9A"  bgcolor="<?=$bgcolor3?>">
 <tr>
  <td align=center bgcolor="<?=$bgcolor1?>">
   <p class=menutitulo style='margin-bottom: 0;'><font color="White"><b>Formulario de Registro</b></font></p>
  </td>
 </tr>
 <tr>
  <td>
   <table width=100% border=0  bgcolor="<?=$bgcolor3?>">
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
	  <font color=red>*</font> Contraseña:
	 </td>
	 <td>
	  <input type='password' name=password value='<? echo $password;?>' size=20>
	 </td>
	</tr>
   </table>
  </td>
 </tr>
 <tr>
  <td align=center bgcolor="<?=$bgcolor1?>">
   <p class=menutitulo style='margin-bottom: 0;'><font color="White"><b>Datos Personales</b></font></p>
  </td>
 </tr>
 <tr>
  <td>
   <table width=100% border=0  bgcolor="<?=$bgcolor3?>">
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
	  Dirección:
	 </td>
	 <td>
	  <input type='text' name=direccion value='<? echo $direccion;?>' size=20>
	 </td>
	</tr>
	<tr>
	 <td>
	  Teléfono:
	 </td>
	 <td>
	  <input type='text' name=telefono value='<? echo $telefono;?>' size=20>
	 </td>
	</tr>
	<tr>
	 <td>
	  Móvil:
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
	 <td>
	  Sucursal:
	 </td>
	 
	 <td>
	  
	   <select name="ubicacion">
	   	<option value=1>Nacer</option>>
	   </select>
	   
	 </td>
	</tr>
	</table>
	<br>
 	<table width="100%" align="center">
	  <tr>
	  <td align="center" width="100%" bgcolor="<?=$bgcolor1?>"><font color="white"><b>Firma</b></font></td>
      </tr>
      <tr>
       <td align="center">
	    <input type="text" name="firma1" value="<? echo $rs->fields["firma1"];?>" size=45><br>
	    <input type="text" name="firma2" value="<? echo $rs->fields["firma2"];?>" size=45><br>
	    <input type="text" name="firma3" value="<? echo $rs->fields["firma3"];?>" size=45>
	   </td>
	  </tr>
	 </table> 
	<br>
   <table width=100% align="left" border=0>
	<tr>
	 <td valign=top bgcolor="<?=$bgcolor1?>" align="center">
	  <font color="white"><b>Comentario</b></font>
	 </td>
	 </tr>
	 <tr>
	 <td align="center" bgcolor="<?=$bgcolor3?>">
	  <textarea name=comentario rows=5 cols=45><? echo $comentario; ?></textarea>
	 </td>
	</tr>
	<tr>
	 <td colspan=2 align=center>
	  <input type='submit' name='cmd' value='Guardar' onclick="return long_firma(document.all.firma1.value,document.all.firma2.value,document.all.firma3.value)">
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
