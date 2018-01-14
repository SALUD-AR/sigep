<?

require_once("../../config.php");

$query="select id_usuario,login,nombre,apellido from sistema.usuarios join permisos.phpss_account on usuarios.login=phpss_account.username
		where phpss_account.active='true'
		order by usuarios.nombre,usuarios.apellido";
$users=sql($query,"<br>Error al traer los datos de los usuarios para actualizar permisos<br>") or fin_pagina();


if($_POST["actualizar_permisos"]=="Actualizar Permisos")
{
	$db->StartTrans();

	$users->Move(0);
	while (!$users->EOF)
	{
		$id_usuario=$users->fields["id_usuario"];
		$login_usuario=$users->fields["login"];
		if($_POST[$login_usuario]==1)
		{
			recargar_permisos($id_usuario,$login_usuario);
		}//de if($_POST[$login_usuario]==1)

	 	$users->MoveNext();
	}//de while(!$users->EOF)

	$db->CompleteTrans();

	echo "<center><H4>Se actualizaron con éxito los permisos para los usuarios seleccionados</h4></center>";
}//de if($_POST["actualizar_permisos"]=="Actualizar Permisos")


echo $html_header;
?>

<script>
function seleccionar_suma(elegir)
{
	var valor,check;
     if(elegir.checked==true)
     {
         valor=true;
     }
     else
     {
         valor=false;
     }

     <?
     $users->Move(0);
     while (!$users->EOF)
     {?>
      	check=eval("document.all.<?=$users->fields["login"]?>");
      	check.checked=valor;
      <?
      	$users->MoveNext();
     }//de while(!$users->EOF)
     ?>

}//de function seleccionar_suma(elegir,check)
</script>

<form name="form1" method="POST" action="actualizar_permisos.php">
<div style="overflow:auto;width:100%;height:90%;position:relative">
<table align="center" width="70%" class="bordes">
 <tr id="mo">
  <td colspan="3">
   <b>Selección de Usuarios para Actualizar Permisos</b>
  </td>
 </tr>
 <tr id="ma">
  <td>
   <input type="checkbox" name="seleccionar_todos" value="1" onclick="seleccionar_suma(this)">
  </td>
  <td>
   Usuario
  </td>
  <td>
   Login
  </td>
 </tr>
<?
//generamos el listado de usuarios a los cuales se le actualizaran los permisos
$users->Move(0);
while (!$users->EOF)
{?>
	<tr <?=atrib_tr()?>>
		<td width="1%">
			<input type="checkbox" name="<?=$users->fields["login"]?>" value="1">
		</td>
		<td width="85%" align="left" onclick="document.all.<?=$users->fields["login"]?>.checked=!document.all.<?=$users->fields["login"]?>.checked">
			<b><?=$users->fields["nombre"]." ".$users->fields["apellido"]?></b>
		</td>
		<td width="15%" onclick="document.all.<?=$users->fields["login"]?>.checked=!document.all.<?=$users->fields["login"]?>.checked">
			<b><?=$users->fields["login"]?></b>
		</td>
	</tr>

 	<?
 	$users->MoveNext();
}//de while(!$users->EOF)
?>
</table>
</div>
<div align="center">
	<input type="submit" name="actualizar_permisos" value="Actualizar Permisos">
</div>
</form>
<?fin_pagina();?>