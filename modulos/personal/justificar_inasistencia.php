<? 
/*
Autor: 
$Author: mari $
$Revision: 1.3 $
$Date: 2006/06/06 17:09:30 $
*/

require("../../config.php");
echo $html_header;
cargar_calendario();

$id_usuario=$parametros['id_usuario'] or $id_usuario=$_POST['id_usuario'];
$nombre=$parametros['nombre'] or $nombre=$_POST['nombre'];
$fecha=$parametros["fecha"] or $fecha=$_POST["fecha"];
$tipo=$parametros["tipo"] or $tipo=$_POST["tipo"]; //1 justifica inasistencia, 2 justifica tardanza

if ($tipo==1) $just='Inasistencia';
   else $just='Tardanza';

if($_POST['guardar']) {
$fecha=fecha_db($_POST['fecha']);
$comentarios=$_POST['comentarios'];
$id_usuario=$_POST['id_usuario'];

$sql_datos="select id_inasistencia from inasistencia 
            where id_usuario=$id_usuario and tipo_justificacion=$tipo
            and fecha='$fecha'";
$res=sql($sql_datos,"$sql_datos")  or fin_pagina();

if ($res->RecordCount() > 0) {
$id_inasistencia=$res->fields['id_inasistencia'];
$sql="update inasistencia set comentario='$comentarios' 
      where id_inasistencia=$id_inasistencia";
}
else {
$sql="insert into inasistencia  (id_usuario,fecha,comentario,tipo_justificacion) 
      values ($id_usuario,'$fecha','$comentarios',$tipo)";
}
sql($sql,"$sql") or fin_pagina();
?>
<script>
    window.opener.document.all.form1.submit();
    window.close();
</script>
<?}
?>
<script>
function control_datos(tipo)
{if(document.all.fecha.value=="")
 {alert('Debe seleccionar una fecha');
  return false;
 }
 if(document.all.comentarios.value=="")
 {alert('Debe ingresar el motivo de la '+ tipo);
  return false;
 } 
 return true;
}
</script>

<form name='form1' action='justificar_inasistencia.php' method="post">
<input type='hidden' name="id_usuario" value='<?=$id_usuario?>'>
<input type='hidden' name="tipo" value='<?=$tipo?>'>
<input type='hidden' name="nombre" value='<?=$nombre?>'>
<table align="center" cellpadding="2" class="bordes">
 <tr> 
   <td id="mo" bgcolor="<?=$bgcolor3?>" align="center" colspan="2"> 
    Justificar <?=$just?> de <?=$nombre?>
   </td>
 </tr>
 <tr bgcolor=<?=$bgcolor_out?>>
    <td width="44%"> <b>Seleccionar Fecha: </b></td>
    <td align="left">
        <input name="fecha" type="text" size="10" value='<?=Fecha($fecha)?>' readonly><?=link_calendario("fecha");?> 
    </td> 
  </tr>
  <tr bgcolor=<?=$bgcolor_out?>>
     <td colspan="2">
     <b>Motivo de la <?=$just?> (<font color="Red">Obligatorio</font>):</b>
     </td>
  </tr>
    <tr bgcolor=<?=$bgcolor_out?>>
     <td colspan="2">
      <textarea name="comentarios" cols="90" rows="5"></textarea>
     </td>
    </tr> 
    <tr bgcolor=<?=$bgcolor_out?>>
     <td align="center">
       <input name="guardar" type="submit"  value="Guardar"  onclick="return control_datos('<?=$just?>')"> 
     </td>
     <td width="50%" align="center">
        <input  type="button"  value="Cerrar" onclick="window.close();"  > 
      </td>
      </tr> 
</table>  

</form>
<?
fin_pagina();
?>