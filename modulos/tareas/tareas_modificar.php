<?php

require_once("../../config.php");
include "funcion.php";
echo $html_header;
// Variables
$id=$parametros["id"] or $id=$_POST["id"];
$cmd=$_POST["cmd"] or $cmd=$parametros["cmd"];
$modi=$_POST["modi"] or $modi=$parametros["modi"];
// Sql
$sql="select * from tareas where id_tarea=$id";
$rs=$db->execute($sql) or die($db->errormsg());
$fila=$rs->fetchrow();
// Comandos
if ($cmd=="Eliminar") {
    $sql="Delete From tareas where id_tarea=$id";
    $db->execute($sql)or die($db->errormsg());
    echo "<script>parent.window.location='".encode_link("../../index.php",array("menu" => $modi))."';</script>\n";
}
if ($cmd=="Finalizar") {
    $fecha=date("Y/m/d H:i:s");
    $sql="UPDATE tareas SET estado='3', vencido='$fecha' where id_tarea=$id";
    $db->execute($sql)or die($db->errormsg());
}
if ($cmd=="Modificar") {
    $diaanuncio=$_POST["diaanuncio"];
    $horaanuncio=$_POST["horaanuncio"];
    $minutoanuncio=$_POST["minutoanuncio"];
    $descripcion=$_POST["descripcion"];
    $diavencido=$_POST["diavencido"];
    $horavencido=$_POST["horavencido"];
    $minutovencido=$_POST["minutovencido"];
    $privilegio=$_POST["privilegio"];
    $asignado=$_POST["asignado"];
    $asigna=$_POST["asigna"];
    $observacion=$_POST["observacion"];
    $solucion=$_POST["solucion"];
    $fecha=date("Y/m/d H:i:s");
    // Control de a quien se asinara
    if ($asignado=="Algunos") {
        $asignado="";
        reset($asigna);
        while (list($key,$cont)=each($asigna))
               $asignado.="$cont|";
    }
    // Controla la buena insercion de la fecha
    if (FechaOk($diaanuncio))
        $diaanuncio = "'".Fecha_db($diaanuncio)." $horaanuncio:$minutoanuncio:00'";
    else
        $diaanuncio = "NULL";

    if (FechaOk($diavencido))
        $diavencido = "'".Fecha_db($diavencido)." $horavencido:$minutovencido:00'";
    else
        $diavencido = "NULL";
    // Descripcion no vacio.
    if (!$descripcion) $error.="Debe colocar la descripcion de la tarea.";
    if (strstr($asignado,"|")) {
        $asig=split("[|]",$asignado);
        while (list($key,$cont)=each($asig)) {
               if ($cont) {
               $sql = "Insert Into tareas
                     (fecha,creadopor,descripcion,observacion,solucion,estado,asignado";
                     $sql.=",privilegio,anuncio";
                     $sql.=",vencido) Values ";
               $sql.="('".$fila["fecha"]."','".$_ses_user['login']."','$descripcion','$observacion','$solucion','1','$cont'";
               if ($fila["asignado"]==$_ses_user['login'] and $estados[$fila["estado"]]!="Finalizada")
                   $sql.=",'$privilegio',$diaanuncio";
               else {
                   if ($fila["anuncio"]) $anu="'".$fila["anuncio"]."'";
                   else $anu="NULL";
                   $sql.=",'".$fila["privilegio"]."',$anu";
               }
               if ($fila["creadopor"]==$_ses_user['login'] and $estados[$fila["estado"]]!="Finalizada")
                   $sql.=",$diavencido";
               else {
                   if ($fila["vencido"]) $anu="'".$fila["vencido"]."'";
                   else $anu="NULL";
                   $sql.=",'".$fila["privilegio"]."',$anu";
               }
               $sql.=")";
               if (!$error) $db->execute($sql) or $error=$db->errormsg()." ".$sql;
               else break;
               }
        }
    }
    $sql = "UPDATE tareas SET
          descripcion='$descripcion',
          observacion='$observacion',
          solucion='$solucion'";
if ($fila["asignado"]==$_ses_user['login'] and $estados[$fila["estado"]]!="Finalizada")
    $sql .= ",anuncio=$diaanuncio,privilegio='$privilegio'";
if ($fila["creadopor"]==$_ses_user['login'] and $estados[$fila["estado"]]!="Finalizada")
    $sql .= ",vencido=$diavencido";
    $sql .= " where id_tarea=$id";
    if (!$error) $db->execute($sql) or $error=$db->errormsg()." ".$sql;
    if ($error)
        Error($error);
    else
        aviso("La tarea se ha realizado con exito.");
}
// Sql
$sql="select usuarios.nombre,usuarios.apellido,tareas.* from tareas inner join usuarios on tareas.creadopor=usuarios.login where id_tarea=$id";
$rs=$db->execute($sql) or die($db->errormsg());
$fila=$rs->fetchrow();
?>
<br>
<script language='javascript' src='../../lib/popcalendar.js'></script>
<form action='tareas_modificar.php' method='post'>
<input type=hidden name=id value='<? echo $id; ?>'>
<input type=hidden name=modi value='<? echo $modi; ?>'>
<table align=center border=1 cellspacing=0 cellpadding=3 bgcolor=<? echo $bgcolor2; ?> width=500>
<tr>
   <td colspan=3 align=center>
       <h2>Modificar Tarea</h2>
       Estado: <b><? echo $estados[$fila["estado"]]; ?></b>
   </td>
</tr>
<tr>
   <td colspan=2>
      Creada por: <? echo $fila["nombre"]." ". $fila["apellido"]; ?>
   </td>
   <td>
<?
$fecha=fecha($fila["fecha"]);
$hora=substr($fila["fecha"],11,8);
?>
      Fecha inicio: <? echo "$fecha $hora"; ?>
   </td>
</tr>
<?
if ($fila["asignado"]==$_ses_user['login'] and $estados[$fila["estado"]]!="Finalizada") {
?>
<tr>
   <td colspan=2>
       Fecha y hora de aviso:<br>
       (Si no quiere que le avise deje estos<br> campos vacios).<br>
       Formato Fecha: dd/mm/YYYY.<br>
       Formato Hora: HH:mm.
   </td>
   <td>
<?
$fecha=fecha($fila["anuncio"]);
$hora=substr($fila["anuncio"],11,8);
?>
       Fecha: <input name='diaanuncio' value='<? echo $fecha; ?>' size="20">
<?
echo link_calendario("diaanuncio");
?>
       <br>
       Hora:&nbsp;&nbsp;<select name='horaanuncio'>
<?
list($h,$m,$s)=split(":",$hora);
$i=0;
while ($i<24) {
       $r=str_pad($i,2,"0",STR_PAD_LEFT);
       echo "<option value='$r'";
       if ($r==$h) echo "selected";
       echo ">$r</option>\n";
       $i++;
}
?></select>:<select name='minutoanuncio'>
<?
$i=0;
while ($i<60) {
       $r=str_pad($i,2,"0",STR_PAD_LEFT);
       echo "<option value='$r'";
       if ($r==$m) echo "selected";
       echo ">$r</option>\n";
       $i++;
}
?></select>
   </td>
</tr>
<?
}
if ($estados[$fila["estado"]]=="Finalizada") {
?>
<tr>
   <td colspan=2>
       Fecha y hora de Terminada:<br>
       (La tarea esta finalizada).
   </td>
   <td>
<?
$fecha=fecha($fila["vencido"]);
$hora=substr($fila["vencido"],11,8);
?>
       Fecha: <input name='diaanuncio' readonly value='<? echo "$fecha $hora"; ?>' size="20">
   </td>
</tr>
<?
}
?>
<tr>
   <td colspan=2 valign=top>
       Descripción de la tarea:
   </td>
   <td>
       <textarea rows=5 cols=40 name=descripcion><? echo $fila["descripcion"] ?></textarea>
   </td>
</tr>
<?
if ($fila["creadopor"]==$_ses_user['login'] and $estados[$fila["estado"]]!="Finalizada") {
?>
<tr>
   <td colspan=2>
       Fecha y hora de vencimiento:<br>
       (Si no quiere que la tarea vensa deje estos<br> campos vacios).<br>
       Formato Fecha: dd/mm/YYYY.<br>
       Formato Hora: HH:mm.
   </td>
   <td>
<?
$fecha=fecha($fila["vencido"]);
$hora=substr($fila["vencido"],11,8);
?>
       Fecha: <input name='diavencido' value='<? echo $fecha; ?>' size="20">
<?
echo link_calendario("diavencido");
?>
       <br>
       Hora:&nbsp;&nbsp;<select name='horavencido'>
<?
list($h,$m,$s)=split(":",$hora);
$i=0;
while ($i<24) {
       $r=str_pad($i,2,"0",STR_PAD_LEFT);
       echo "<option value='$r'";
       if ($r==$h) echo "selected";
       echo ">$r</option>\n";
       $i++;
}
?></select>:<select name='minutovencido'>
<?
$i=0;
while ($i<60) {
       $r=str_pad($i,2,"0",STR_PAD_LEFT);
       echo "<option value='$r'";
       if ($r==$m) echo "selected";
       echo ">$r</option>\n";
       $i++;
}
?></select>
   </td>
</tr>
<?
}
if ($fila["asignado"]==$_ses_user['login'] and $estados[$fila["estado"]]!="Finalizada") {
?>
<tr>
   <td colspan=2 valign=top>
       Prioridad:
   </td>
   <td>
       <select name=privilegio>
<?
while (list($key,$cont)=each($prioridades)) {
       echo "<option value='$key' ";
       if ($key==$fila["privilegio"]) echo "Selected";
       echo ">$cont</option>\n";
}
?>
       </select>
   </td>
</tr>
<?
}
if ($fila["creadopor"]==$_ses_user['login'] and $estados[$fila["estado"]]!="Finalizada") {
?>
<tr>
   <td width=100>
      Asignado a:<br>
(selección múltiple con la tecla 'Ctrl')
   </td>
   <td align=center><b>
      <?
      echo $fila["asignado"];
      ?></b>
   </td>
   <td rowspan=2>
      <input type=radio name=asignado value=Algunos> Asignar a otros usuarios<br>
      <select name=asigna[] size=6 multiple>
<?
$sql="select phpss_account.username,usuarios.nombre,usuarios.apellido from phpss_account inner join usuarios on phpss_account.username=usuarios.login where phpss_account.active='true'";
$rs=$db->Execute($sql) or die($db->errormsg());
while ($fila1=$rs->fetchrow()) {
       echo "<option value='".$fila1["username"]."'>".$fila1["nombre"]."&nbsp;".$fila1["apellido"]."</option>\n";
}
?>
   </td>
</tr>
<? } ?>
<tr>
   <td colspan=2>
   Observación:<br>
   <textarea name=observacion rows=4 cols=35><? echo $fila["observacion"]; ?></textarea>
   </td>
</tr>
<tr>
   <td colspan=3>
   Solución:<br>
   <textarea name=solucion rows=4 cols=80><? echo $fila["solucion"]; ?></textarea>
   </td>
</tr>

<tr>
   <td align=center colspan=3>
      <input type=button value=Volver OnClick="window.location='<? echo $modi; ?>.php';">
      <input type=reset name=reset value=Cancelar los Cambios>
<?
if ($estados[$fila["estado"]]!="Finalizada") {
?>
<input type=submit name=cmd value=Modificar>
<?}
if ($fila["asignado"]==$_ses_user['login'] and $estados[$fila["estado"]]!="Finalizada") {
?>
      <input type=submit name=cmd value=Finalizar>
<?}
if ($fila["creadopor"]==$_ses_user['login']) {
?>
      <input type=submit name=cmd value=Eliminar>
<?
}
?>
   </td>
</tr>
</table>
</form>
<br>