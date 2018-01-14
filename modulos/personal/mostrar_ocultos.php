<? 
/*
Autor: Mariela
$Author: mari $
$Revision: 1.1 $
$Date: 2006/06/06 17:07:27 $
*/

require_once("../../config.php");


variables_form_busqueda("ocultos",array("ubicacion"=>""));
echo $html_header;

$ubicacion=$parametros['ubicacion'] or $ubicacion=$_POST['ubicacion'];;

$itemspp=50;
$order=0;

$orden= array
(
	"default" => "1",
	"default_up"=>"$order",
	"1" => "u.apellido",
	"2" => "u.nombre",
	"3" => "u.login"
);

$filtro= array
(
	"u.apellido"=>"apellido",
	"u.nombre"=>"nombre",
	"u.login"=>"login"
);

if ($_POST['mostrar']) {
 $chk=PostvartoArray("usuario_");
  if ($chk) {
  $list=implode(",",$chk);
  $sql="update sistema.usuarios set visible=1 where id_usuario in ($list)";
  sql($sql,"$sql") or fin_pagina();
 
  $ref=encode_link("control_presentismo.php",array("ubicacion"=>$ubicacion));
  ?>
  <script>
    window.opener.location.href='<?=$ref?>';
    window.close();
  </script>
  <?
 }
 else Error("Seleccione al menos un usuario");
}

?>
<form name='form1' action="mostrar_ocultos.php" method="post">
<div align="center"><font size="2+" color="Blue">Agregar Usuarios al listado de Control Presentismo</font></div>
<?
//selecciono los usuarios que tienen el campo visible=0
//o sea que no se muestran el el listado de control presentismo
$sql="select id_usuario, u.nombre, u.apellido, u.login, d.nombre as ubicacion
      from sistema.usuarios u 
      left join permisos.phpss_account p on (u.login=p.username) 
      left join personal.legajos l using (id_usuario) 
      left join licitaciones.distrito d on(pcia_ubicacion=id_distrito) "; 
$where_tmp ="visible=0 and active='true'";
if ($ubicacion !='Todas')  $where_tmp.=" and d.nombre ilike '%$ubicacion%'";
?>
<table align=center cellpadding=5 cellspacing=0>
   <tr>
     <td>
    <? list($sql,$total,$link, $up) = form_busqueda($sql,$orden,$filtro,$link_tmp,$where_tmp,"buscar");
       $res=sql($sql,"$sql") or fin_pagina();    
      ?>
      <b>Ubicación:</b> <select name="ubicacion">
        <option value='Buenos Aires' <?if ($ubicacion=='Buenos Aires') echo 'selected'?>>Buenos Aires </option>
		<option value='San Luis' <?if ($ubicacion=='San Luis') echo 'selected'?>>San Luis </option>
		<option value='Todas' <?if ($ubicacion=='Todas') echo 'selected'?> >Todas </option>
  </select>
     <input type=submit name='form_busqueda' value='Buscar'>
     </td>
     <td width="25%" align="right"><input type="button" name="cerrar" value="Cerrar" onclick="window.close();"></td>
  </tr>
</table>
   <br>   
<table class="bordessininferior" width="95%" align="center" cellpadding="3" cellspacing='0'>
   <tr id=ma>
      <td align=left> <b>Total Usuarios:</b>  <?=$total?></td>
      <td align="right"><?=$link;?></td>
   </tr>
</table>
<table width='95%' class="bordessinsuperior" cellspacing='2' align="center">   
   <tr id=mo>
     <td><a href='<?=encode_link('mostrar_ocultos.php',array("sort"=>"1","up"=>$up,"ubicacion"=>$ubicacion))?>'>Apellido</a></td>
     <td><a href='<?=encode_link('mostrar_ocultos.php',array("sort"=>"2","up"=>$up,"ubicacion"=>$ubicacion))?>'>Nombre</a></td>
     <td><a href='<?=encode_link('mostrar_ocultos.php',array("sort"=>"3","up"=>$up,"ubicacion"=>$ubicacion))?>'>Login</a></td>
     <td> &nbsp;</td> 
   <? while(!$res->EOF) {
        $id_usuario=$res->fields['id_usuario'];
   ?>
   <tr <?=$atrib_tr;?>>
      <td align="center"><?=$res->fields['apellido']?></td>   
      <td align="center"><?=$res->fields['nombre']?></td>   
      <td align="center"><?=$res->fields['login']?></td>   
     <td width="3%"> <input type='checkbox' class="estilos_check" value='<?=$id_usuario?>' name='usuario_<?=$id_usuario?>'> </td>
   </tr>
   <? $res->MoveNext();
   }?>
   
   <tr align="center">
     <td colspan="4"> <input type='submit' value='Agregar Seleccionados' name='mostrar'></td>
   </tr>
</table>



</form>