<?php

require_once("config.php");
//error_reporting(8);
include "modulos/tareas/funcion.php";
echo $html_header;
// Variables
$id=$_GET["id"] or $id=$_POST["id"];
$cmd=$_POST["cmd"];
// Funciones
function actualiza () {
    $sql="select id_tarea,anuncio from tareas where
      (asignado ilike '%|".$_ses_user["login"]."|%' or asignado='Todos') and anuncio is not NULL and activo=1 and estado != '3' order by anuncio ASC,privilegio DESC";
    $rs=sql($sql) or die;
    echo "<script>parent.tareas=new Array;\n";
    while ($fila=$rs->fetchrow())
		echo "parent.tareas['".$fila["id_tarea"]."']='".$fila["anuncio"]."';\n";
	echo "</script>\n";
}
// Comandos
if ($id) {
if ($cmd=="Aceptar") {
    $tipo=$_POST["tipo"];
    $numero=$_POST["numero"];
    if ($tipo=="dia")
        $fecha="'".date("Y/m/d H:i:s",mktime(date("H"),date("i"),date("s"),date("m"),date("d")+$numero,date("Y")))."'";
    if ($tipo=="hora")
        $fecha="'".date("Y/m/d H:i:s",mktime(date("H")+$numero,date("i"),date("s"),date("m"),date("d"),date("Y")))."'";
    if ($tipo=="minuto")
        $fecha="'".date("Y/m/d H:i:s",mktime(date("H"),date("i")+$numero,date("s"),date("m"),date("d"),date("Y")))."'";
    $sql="UPDATE tareas SET anuncio=$fecha where id_tarea=$id";
    $db->execute($sql)or die($db->errormsg());
    actualiza();
    echo "<script>parent.document.all.aviso.style.visibility='hidden';</script>";
}
if ($cmd=="Realizada") {
    $fecha=date("Y/m/d H:i:s");
    $sql="UPDATE tareas SET estado='3', vencido='$fecha' where id_tarea=$id";
    $db->execute($sql)or die($db->errormsg());
    actualiza();
    echo "<script>parent.document.all.aviso.style.visibility='hidden';</script>";
}
// sql
$sql="Select fecha,vencido,creadopor,descripcion,estado,privilegio,solucion from tareas where id_tarea=$id";
$rs=$db->execute($sql) or die($db->errormsg());
?>
<script language='javascript' src='/lib/popcalendar.js'></script>
<form action="aviso.php" method=post>
<input type='hidden' name=id value='<? echo $id; ?>'>
<table align=center border=1 cellspacing=0 cellpadding=3 bgcolor="#D5D5D5" width=100% height=100%>
<tr>
   <td align=center colspan=2>
      <h1>Tiene una tarea pendiente</h1>
   </td>
</tr>
<tr>
   <td style="border: none;">
      Fecha y hora de inicio:
<?
$h=substr($rs->fields["fecha"],11,8);
echo fecha($rs->fields["fecha"])." $h\n";
?>
   </td>
   <td align=right style="border: none;">
      Fecha y hora de vencimiento:
<?
$h=substr($rs->fields["vencido"],11,8);
echo fecha($rs->fields["vencido"])." $h\n";
?>
   </td>
</tr>
<tr>
   <td width=50%>
      Prioridad: <b><? echo $prioridades[$rs->fields["privilegio"]]; ?></b>
   </td>
   <td width=50%>
      Estado: <b><? echo $estados[$rs->fields["estado"]]; ?></b>
   </td>
</tr>
<tr>
   <td width=50%>
      Enviada por: <b><? echo $rs->fields["creadopor"]; ?></b>
   </td>
   <td valign=top width=50% rowspan=3>
      Descripcion: <br>
      <div style="background-color: white;height: 90%;border: solid ;border-width: 1;overflow-y: auto; width: 100%;"><? echo html_out($rs->fields["descripcion"]); ?></div>
   </td>
<tr>
   <td>
      Recordar en: <input type=text name=numero value=15 size=5>
      <select name=tipo>
      <option value=minuto selected>Minutos</option>
      <option value=hora>Horas</option>
      <option value=dia>Dias</option>
      </select>
   </td>
</tr>
</tr>
<tr>
   <td align=center>
      <input type=submit name=cmd value="Aceptar">
      <input type=button value="Ver detalles" OnClick="parent.location.href='<? echo encode_link("index.php",array("menu" => "tareas_porhacer", "extra" => Array("modi" => "tareas_porhacer","cmd" => "modificar","id" => $id))); ?>';">
      <input type=submit name=cmd value="Realizada">
   </td>
</tr>
</table>
</form>
<?
}
?>
</body>
</html>