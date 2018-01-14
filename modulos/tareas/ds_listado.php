<?
/*lista tareas
 tareas pendientes: fecha inicio y fecha_fin null
 tareas en curso: fecha fin null
 tareas terminadas: fecha_pendiente, fecha inicio y fecha_fin notnull
*/
require_once("../../config.php");
echo $html_header;
if(Fechaok($_POST['keyword']))
{
 $_POST['keyword']=Fecha_db($_POST['keyword']);		
}

variables_form_busqueda("div_soft");

if ($cmd == "") {
	$cmd="encurso";
	phpss_svars_set("_ses_div_soft_cmd", $cmd);
}


if ($_ses_user['login'] == 'corapi' || $_ses_user['login'] == 'juanmanuel' || $_ses_user['login'] == 'mariela' || $_ses_user['login'] == 'marcos') {
//muestra la tarea de todos los usuarios
	$where="";
}
else { //,muestra la tarea del usuario logeado
    $where=" and (login='".$_ses_user['login']."' or id_prog isnull) "; //muestra tarea del usuario logeado
}


?>
<form name="form1" method="post" action="ds_listado.php">
<?
//link para nueva tarea
$link1=encode_link("ds_nuevatarea.php",array("id_tarea"=>-1));
echo "<table align=center cellpadding=5 cellspacing=0 >";
echo "<tr>";
echo "<td> <input type='button' name='NuevaTarea' value='Nueva Tarea' onClick=\"document.location='".$link1."'\" " ;  echo "> &nbsp;&nbsp; </td>";
echo "<td>\n";


$itemspp=50;

// Fin variables necesarias
if ($up=="") $up = "0";   // 1 ASC 0 DESC


$orden = Array (
"default" => "1",
"1" => "id_tarea",
"2" => "desc_tarea",
"3" => "nombre",
"4" => "Apellido Prog",
"5" => "Fecha"
);

$filtro = Array (
"id_tarea" => "ID tarea",
"desc_tarea" => "Descripción",
"nombre" => "Nombre Prog",
"apellido" => "Apellido Prog",
"fecha_inicio" => "Fecha"
);



$sql_tmp="select * from tareas_divisionsoft.tareas_ds 
left join tareas_divisionsoft.programadores using (id_prog)";

list($sql,$total,$link_pagina,$up2) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");

$sql_prog="select * from programadores order by apellido";
$res_prog=sql($sql_prog) or fin_pagina();
echo "&nbsp;&nbsp;&nbsp;<input type=submit name='buscar' value='Buscar'>";
echo "</td>"; 
echo "</tr>\n";
echo "</table>\n";
$res_query = sql($sql) or fin_pagina();


?>
<table width=100%>
	 <tr id=ma>
	 <td align="left"><b><? echo "Total:</b> $total TAREAS.</td>\n";?><? echo "\n";?></td>
      <td width=70% align=right><? echo $link_pagina ?></td> <? echo"\n";?>
	 </tr>
	</table> <? echo "\n";?>
<table border=0 width=100% cellspacing=2 cellpadding=3 >
 
  <tr>
      <td  align="center" id=mo><a id=mo href='<? echo encode_link("ds_listado.php",Array('sort'=>3,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))?>'><b>PROGRAMADORES</b></a></td>
      <td  align="center" id=mo><a id=mo href='<? echo encode_link("ds_listado.php",Array('sort'=>3,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))?>'><b>FECHA</b></a></td>
      <td align="center" id=mo><a id=mo href='<? echo encode_link("ds_listado.php",Array('sort'=>2,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))?>'><b>DESCRIPCION</b></a></td>
      <td  align="center" id=mo><a id=mo href='<? echo encode_link("ds_listado.php",Array('sort'=>1,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))?>'><b>ID TAREA</b></a></td>
      
      
      
   </tr>
   
 <? while (!$res_query->EOF) {
  $ref = encode_link("ds_nuevatarea.php",Array ("id_tarea" => $res_query->fields['id_tarea']));
  tr_tag($ref,"title='Haga click aqui modificar los datos de la tarea'");
  ?>
    <td align="center"><? echo $res_query->fields['apellido']." ".$res_query->fields['nombre']?></td>
    <td align="center"><? echo Fecha($res_query->fields['fecha_inicio']);?></td>
	<td ><? echo ereg_replace("\n","<br>",$res_query->fields['desc_tarea']) ?></td>
    <td align="center"><? echo $res_query->fields['id_tarea'] ?></td>
    
  </tr>
  <? 		
   $res_query->MoveNext();
   } ?>
   
</table>

</form>