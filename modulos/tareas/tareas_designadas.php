<?php

require_once("../../config.php");
include "funcion.php";
echo $html_header;
//Fin encabesado
// Varialbes
$up = $_POST["up"] or $up = $parametros["up"];
$sort = $_POST["sort"] or $sort = $parametros["sort"] or $sort = "";
// llenar arreglo de usuarios
$sql="Select login,nombre,apellido From usuarios";
$rs=$db->execute($sql) or die($db->errormsg());
while ($fila=$rs->fetchrow()) {
       $usuarios[$fila["login"]]=$fila["nombre"]." ".$fila["apellido"];
}
// Fin variables
// Contenido
echo "<form action=tareas_designadas.php method=post>\n";
echo "<table align=center cellpadding=5 cellspacing=0 border=1 bordercolor='$bgcolor3'>";
echo "<input type=hidden name=sort value='$sort'>\n";
echo "<input type=hidden name=up value='$up'>\n";
//echo "<tr><td align=center><h2>Tareas por hacer</h2></td></tr>\n";
echo "<tr><td>\n";
// Formulario de busqueda
// Variables necesarias
$page = $parametros["page"] or $page = 0;                                //pagina actual
$filter = $_POST["filter"] or $filter = $parametros["filter"];           //campo por el que se esta filtrando
$keyword = $_POST["keyword"] or $keyword = $parametros["keyword"];       //palabra clave
// Fin variables necesarias
if ($up=="") $up = "0";   // 1 ASC 0 DESC
$orden = Array (
"default" => "6",
"1" => "fecha",
"2" => "asignado",
"3" => "descripcion",
"4" => "estado",
"5" => "privilegio",
"6" => "anuncio",
"7" => "solucion");
$filtro = Array (
"fecha" => "Eviada el",
"asignado" => "Asignada a",
"descripcion" => "Descripci�n",
"estado" => "Estado",
"privilegio" => "Prioridad",
"anuncio" => "Fecha de anuncio",
"solucion" => "Soluci�n");
// Base datos
$sql_tmp="select tareas.id_tarea,tareas.fecha,tareas.descripcion,
          tareas.estado,tareas.vencido,tareas.privilegio,tareas.anuncio,tareas.solucion,
          usuarios.nombre,usuarios.apellido from tareas
          inner join usuarios on tareas.asignado=usuarios.login";
$where_tmp="(creadopor='".$_ses_user['login']."' and (asignado <> '".$_ses_user['login']."')) and activo=1";
list($sql,$total,$link_pagina,$up2) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");
echo "&nbsp;&nbsp;&nbsp;<input type=submit name='form_busqueda' value='   Buscar   '>";
echo "</td></tr>\n";
echo "</table></form>\n";
$result = $db->execute($sql) or die($db->ErrorMsg());
echo "<table border=1 width=100% cellspacing=0 cellpadding=1 bordercolor='white' align=center>";
echo "<td colspan=8 align=center>";
echo "<table id=mo border=0 width=100%><tr>";
echo "<td align=center width=70%>Tareas: $total</td>";
echo "<td align=center width=30%>$link_pagina&nbsp;</td>";
echo "</tr></table>";
echo "</td></tr>";
echo "<tr id=ma>";
echo "<td align=center><a id=ma href='".encode_link("tareas_designadas.php",Array('sort'=>1,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))."'>Enviada</a></td>";
echo "<td align=center><a id=ma href='".encode_link("tareas_designadas.php",Array('sort'=>2,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))."'>Asignadas a</a></td>";
echo "<td align=center><a id=ma href='".encode_link("tareas_designadas.php",Array('sort'=>3,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))."'>Descripci�n</a></td>";
echo "<td align=center><a id=ma href='".encode_link("tareas_designadas.php",Array('sort'=>4,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))."'>Estado</a></td>";
echo "<td align=center><a id=ma href='".encode_link("tareas_designadas.php",Array('sort'=>5,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))."'>Prioridad</a></td>";
echo "<td align=center><a id=ma href='".encode_link("tareas_designadas.php",Array('sort'=>6,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))."'>Cumplir el/Finalizada</a></td>";
echo "<td align=center><a id=ma href='".encode_link("tareas_designadas.php",Array('sort'=>7,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))."'>Soluci�n</a></td>";
echo "<td align=center>Eliminar</td>\n";
echo "</tr>\n";
while ($fila = $result->fetchrow()) {
    $ref = encode_link("tareas_modificar.php",Array("modi" => "tareas_designadas","id" => $fila["id_tarea"]));
    tr_tag($ref,"title='Haga click aqui para ver o modificar los datos de la tarea'");
    if (date("Y-m-d H:i:s") > $fila["vencido"] and $fila["vencido"] and $estados[$fila["estado"]]!="Finalizada") $col="bgcolor=#FF8080";
    $hora=substr($fila["fecha"],11,8);
    echo "<td align=center $col>&nbsp;".Fecha($fila["fecha"])." $hora</td>\n";
    echo "<td align=center $col>&nbsp;".$fila["nombre"]." ".$fila["apellido"]."</td>\n";
    echo "<td align=center $col>&nbsp;".html_out($fila["descripcion"])."</td>\n";
    echo "<td align=center $col>&nbsp;".$estados[$fila["estado"]]."</td>\n";
    echo "<td align=center $col>&nbsp;".$prioridades[$fila["privilegio"]]."</td>\n";
    $hora=substr($fila["vencido"],11,8);
    echo "<td align=center $col>&nbsp;".Fecha($fila["vencido"])." $hora</td>\n";
    echo "<td align=center $col>&nbsp;".html_out($fila["solucion"])."</td>\n";
    echo "<td align=center $col>&nbsp;";
    echo "<a href='".encode_link("tareas_modificar.php",array("id" => $fila["id_tarea"],"cmd"=>"Eliminar","modi"=>"tareas_designadas"))."'><img src='../../imagenes/close1.gif' border=0 alt='Has click para Modificar la tarea'></a>";
    echo "</td>\n";
    echo "</tr>\n";
}
echo "</table>\n";
?>