<? 
require_once("../../config.php");
echo $html_header;

$id_tarea=$parametros['id_tarea'] or $id_tarea=$_POST['id_tarea'];

if ($_ses_user['login'] == 'corapi' || $_ses_user['login'] == 'juanmanuel' || $_ses_user['login'] == 'mariela' || $_ses_user['login'] == 'marcos') {
//muestra todos los prog
	$ctrl=0;
}
else { // seleccionado el prog
    $query="select * from programadores where login='".$_ses_user['login']."'";
	$res_user= $db->Execute($query) or die($db->ErrorMsg().$query);
    $usuario = $res_user->fields["id_prog"];
    $nombre=$res_user->fields["nombre"];
    $apellido=$res_user->fields["apellido"];
    $ctrl=1;
}    

if ($_POST['guardar']=='Guardar') {	
 
 if ( $_POST['prog'] ==-1 ) {
 	Error ("Dede Dede seleccionar un programador  ");	
 }
 
if (!$error) {
if ($id_tarea == -1 ) { //insertar

$db->StartTrans();
//recupero el id de la tarea a insertar 
  $sql_id= "SELECT nextval('tareas_ds_id_tarea_seq') as id";
  $res_id= $db->Execute($sql_id) or die($db->ErrorMsg().$sql_id);
  $id_tarea = $res_id->fields["id"];
  $fecha=date("Y-m-d",mktime());
  $campos="id_tarea,desc_tarea,fecha_inicio";
  if ($_POST['prog'] !=-1) $campos.=",id_prog";
  $fecha_pedido=fecha_db($_POST['fecha_pedido']);
  $values="$id_tarea,'".$_POST['desc_tarea']."','".$fecha."'";
  if ($_POST['prog'] !=-1) $values.=",".$_POST['prog']."";
  
  
  $sql_insert="insert into tareas_ds ($campos) values ($values)";
  $db->Execute($sql_insert) or die($db->ErrorMsg().$sql_insert);
  $values="$id_tarea,".$_POST['prog'].",'$fecha','".$_POST['desc_tarea']."'";
  $campos="id_tarea,id_prog,fecha_log,comentario_log";
  $sql_insert="insert into log_tareas_ds ($campos) values ($values)";
  $db->Execute($sql_insert) or die($db->ErrorMsg().$sql_insert);
 
  
 

if ($db->CompleteTrans())
    $msg="LOS DATOS DE LA TAREA ID $id_tarea GUARDARON CON EXITO";
    else 
    $msg="ERROR AL GUARDAR LOS DATOS";
}
else { //modificar

 $db->StartTrans();
 
 $fecha=date("Y-m-d",mktime());
  
  
  $sql_update="update tareas_ds set desc_tarea='".$_POST['desc_tarea']."'";
  
  if ($_POST['prog']!=-1) $sql_update.=" ,id_prog=".$_POST['prog']."";
  
  $sql_update.=" where id_tarea=$id_tarea";
  $db->Execute($sql_update) or die($db->ErrorMsg().$sql_update);
   
  
  
  $values="$id_tarea,".$_POST['prog'].",'$fecha','".$_POST['desc_tarea']."'";
  $campos="id_tarea,id_prog,fecha_log,comentario_log";
  $sql_insert="insert into log_tareas_ds ($campos) values ($values)";
  $db->Execute($sql_insert) or die($db->ErrorMsg().$sql_insert);
  
  if ($db->CompleteTrans())
    $msg="LA TAREA ID  $id_tarea SE ACTUALIZO CON EXITO";
    else 
    $msg="NO SE ACTUALIZARON LOS DATOS";
   }
 }
} //fin guardar


?>
<script>
function control_datos () {

if (document.all.desc_tarea.value == "") {
 alert ("Debe ingresar un descripción para la tarea");
 return false;
}

return true;	
}
</script>

<?if ($id_tarea!=-1) {
$sql_reg="select * from tareas_historial where id_tarea=$id_tarea";	
$res_reg=sql($sql_reg) or fin_pagina();
if ($res_reg->RecordCount() > 0 ) {
?>
<!-- tabla de registro -->
<div style="overflow:auto;<? if ($res_reg->RowCount() > 3) echo 'height:50;' ?> "  >
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor=#cccccc>
<? while (!$res_reg->EOF) {?>
<tr> 
      <td>Fecha : <?=fecha($res_reg->fields['fecha_porcentaje'])?> </td>
      <td > Porcentaje de avance: <?=$res_reg->fields['porcentaje'].'%'?> </td>
</tr>
<?
$res_reg->MoveNext();
}?>
</table>
</div>
<hr>
<?}
}?>


<?$link_form=encode_link("ds_nuevatarea.php",array("modif" => $post['modif']))?>
<form action="<?=$link_form?>" method="post" name="form1">
<input type="hidden" name="id_tarea" value="<?=$id_tarea?>">
<input type='hidden' name='prog' value='<?=$usuario?>'> 
<? 
echo cargar_calendario();



$sql_prog="select * from programadores order by apellido";  
$res_prog=sql($sql_prog) or fin_pagina();
$usuario=$_ses_user['name'];

if ($id_tarea != -1) {
$sql_tareas="select * from tareas_divisionsoft.tareas_ds where id_tarea=$id_tarea";
$res_tareas=sql($sql_tareas) or fin_pagina();
}
echo "<div align='center'><font color=blue>".$msg."</font></center>";

?>
<div align="center">
<? if ($id_tarea!= -1) echo "<b>DATOS DE LA TAREA CON ID $id_tarea </b>" ; else { ?>
        <b>INGRESE DESCRIPCION DE LA TAREA</b>  <br>
<? }?>
</div>
<table  align="center" cellpadding="2" border="1">
   <tr>
    <td>Descripción de la tarea</td>
    <td align="center"><textarea name="desc_tarea" rows="6" cols="80" ><?if ($error==1) echo $_POST['desc_tarea'];elseif ($id_tarea!=-1) echo $res_tareas->fields['desc_tarea'];?></textarea></td>
  </tr>
   <tr>
    <td >Programador asignado</td>
     <td><select name="prog">
	 <option value=-1> seleccione programador</option>
     <? while (!$res_prog->EOF) { 
     	$non_prog=$res_prog->fields['nombre']. " " .$res_prog->fields['apellido'] ;
     	?>
          <option value="<?=$res_prog->fields['id_prog']?>"  
                       <?
                         if ($id_tarea != -1) {
                         	if($res_tareas->fields['id_prog']==$res_prog->fields['id_prog'])
                             echo 'selected';
                         }
                         else {
                         if ($non_prog==$usuario) echo 'selected';
                         }
                         ?> > 
          
          <?=$res_prog->fields['apellido']. " " . $res_prog->fields['nombre']?>
          </option>
      <? $res_prog->MoveNext();
      }?>
    </select></td>

    </tr>
   
    </table>
    <div  align="center"> <br>
   
<input type="submit" name="guardar" value="Guardar" onClick="return control_datos();" >
<? $link1=encode_link("ds_listado.php",array()); ?>	 
<input type="button" name="volver" value="Volver" Onclick="location.href='<?=$link1?>'">
</div>
</form>
