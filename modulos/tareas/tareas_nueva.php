<?php

require_once("../../config.php");
include "funcion.php";
echo $html_header;
$cmd=$_POST["cmd"];
if ($cmd=="subir") {
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
    $fecha=date("Y/m/d H:i:s");
    // Control de a quien se asinara
    if ($asignado=="Algunos") {
        $asignado="";
        reset($asigna);
        while (list($key,$cont)=each($asigna))
               $asignado.="$cont|";
    }
    if ($asignado=="yo")
        $asignado=$_ses_user['login'];
    if ($asignado=="Todos") {
        $sql = "select username from phpss_account where active='true'";
        $rs=$db->execute($sql) or $error.=$db->errormsg();
        $asignado="";
        while ($fila=$rs->fetchrow())
               $asignado.=$fila["username"]."|";
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
    if (!$descripcion) $error.="Debe colocar la descripcion de la tarea.";
    if (strstr($asignado,"|")) {
        $asig=split("[|]",$asignado);
        while (list($key,$cont)=each($asig)) {
               if ($cont) {
               $sql = "Insert Into tareas
                     (fecha,creadopor,anuncio,descripcion,vencido,privilegio,asignado,observacion,estado) Values "
                     ."('$fecha','".$_ses_user['login']."',$diaanuncio,'$descripcion',$diavencido,'$privilegio','$cont','$observacion','1')";
               if (!$error) $db->execute($sql) or $error=$db->errormsg();
               else break;
               }
        }
    }
    else {
          $sql = "Insert Into tareas
            (fecha,creadopor,anuncio,descripcion,vencido,privilegio,asignado,observacion,estado) Values "
            ."('$fecha','".$_ses_user['login']."',$diaanuncio,'$descripcion',$diavencido,'$privilegio','$asignado','$observacion','1')";
          if (!$error) $db->execute($sql) or $error=$db->errormsg();
    }
    if ($error)
        Error($error);
    else
        aviso("La tarea se ha realizado con exito.");
}
?>
<br>
<script language='javascript' src='../../lib/popcalendar.js'></script>
<form action='tareas_nueva.php' method='post'>
<input type=hidden name=cmd value='subir'>
<table align=center border=1 cellspacing=0 cellpadding=3 bgcolor=<? echo $bgcolor2; ?> width=500>
<tr>
   <td colspan=3 align=center>
       <h2>Nueva tarea</h2>
   </td>
</tr>
<tr>
   <td colspan=2>
      Creada por: <? echo $_ses_user["name"]; ?>
   </td>
   <td>
      Fecha inicio: <? echo date("d/m/Y H:i:s"); ?>
   </td>
</tr>
<tr>
   <td colspan=2>
       Fecha y hora de aviso:<br>
       (Si no quiere que le avise deje estos<br> campos vacios).<br>
       Formato Fecha: dd/mm/YYYY.<br>
       Formato Hora: HH:mm.
   </td>
   <td>
       Fecha: <input name='diaanuncio' size="20">
<?
echo link_calendario("diaanuncio");
?>
       <br>
       Hora:&nbsp;&nbsp;<select name='horaanuncio'>
       <option value="00">00</option>
<?
$i=1;
while ($i<24) {
       $r=str_pad($i,2,"0",STR_PAD_LEFT);
       echo "<option value='$r'>$r</option>\n";
       $i++;
}
?></select>:<select name='minutoanuncio'>
       <option value="00">00</option>
<?
$i=1;
while ($i<60) {
       $r=str_pad($i,2,"0",STR_PAD_LEFT);
       echo "<option value='$r'>$r</option>\n";
       $i++;
}
?></select>
   </td>
</tr>
<tr>
   <td colspan=2 valign=top>
       Descripción de la tarea:
   </td>
   <td>
       <textarea rows=5 cols=40 name=descripcion></textarea>
   </td>
</tr>
<tr>
   <td colspan=2>
       Fecha y hora de vencimiento:<br>
       (Si no quiere que la tarea tenga vencimiento, deje estos<br> campos vacios).<br>
       Formato Fecha: dd/mm/YYYY.<br>
       Formato Hora: HH:mm.
   </td>
   <td>
       Fecha: <input name='diavencido' size="20">
<?
echo link_calendario("diavencido");
?>
       <br>
       Hora:&nbsp;&nbsp;<select name='horavencido'>
       <option value="00">00</option>
<?
$i=1;
while ($i<24) {
       $r=str_pad($i,2,"0",STR_PAD_LEFT);
       echo "<option value='$r'>$r</option>\n";
       $i++;
}
?></select>:<select name='minutovencido'>
       <option value="00">00</option>
<?
$i=1;
while ($i<60) {
       $r=str_pad($i,2,"0",STR_PAD_LEFT);
       echo "<option value='$r'>$r</option>\n";
       $i++;
}
?></select>
    </td>
</tr>
<tr>
   <td colspan=2 valign=top>
       Prioridad:
   </td>
   <td>
       <select name=privilegio>
<?
while (list($key,$cont)=each($prioridades)) {
       echo "<option value='$key' ";
       if ($cont=="Normal") echo "Selected";
       echo ">$cont</option>\n";
}
?>
       </select>
   </td>
</tr>
<tr>
   <td width=100>
      Asignado a:<br>
(selección múltiple con la tecla 'Ctrl')
   </td>
   <td>
      <input type=radio name=asignado value=yo checked>Yo&nbsp;
      <input type=radio name=asignado value=Todos>Todos
   </td>
   <td rowspan=2>
      <input type=radio name=asignado value=Algunos>Algunos<br>
      <select name=asigna[] size=6 multiple>
<?
$sql="select phpss_account.username,usuarios.nombre,usuarios.apellido from phpss_account inner join usuarios on phpss_account.username=usuarios.login where phpss_account.active='true'";
$rs=$db->Execute($sql) or die($db->errormsg());
while ($fila=$rs->fetchrow()) {
       echo "<option value='".$fila["username"]."'>".$fila["nombre"]."&nbsp;".$fila["apellido"]."</option>\n";
}
?>
   </td>
</tr>
<tr>
   <td colspan=2>
   Observación:<br>
   <textarea name=observacion rows=4 cols=35></textarea>
   </td>
</tr>
<tr>
   <td align=center colspan=3>
      <input type=reset name=reset value=Cancelar los Cambios>
      <input type=submit name=enviar value=Enviar>
   </td>
</tr>
</table>
</form>
<br>