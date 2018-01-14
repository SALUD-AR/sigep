<?php

include("../../config.php");

$tipo_p=$_POST["tipo_producto"] or $tipo_p=$parametros["tipo"] or $tipo_p="";

echo $html_header;

variables_form_busqueda("listado_tareas");

if ($cmd == "")  $cmd="A";


$orden = array(
		"default" => "1",
		"1" => "id_tarea_soft",
		"2" => "nombre",		
		"3" => "asunto",
		"4" => "vencimiento",
		"5" => "peridiocidad"
		
	);

$filtro = array(
		"id_tarea_soft" => "Nro",
		"nombre" => "Encargado",		
		"asunto" => "Asunto",
		"vencimiento" => "Vencimiento",
		"peridiocidad" => "Periodicidad"
		
	);
	
$datos_barra = array(
     array(
        "descripcion"=> "Activas",
        "cmd"        => "A"
     ),
     array(
        "descripcion"=> "Finalizadas",
        "cmd"        => "F"
     ),
    
);

generar_barra_nav($datos_barra);

$query="select id_tarea_soft,asunto,vencimiento,aviso,peridiocidad,nombre,apellido,descripcion 
        from tareas_divisionsoft.tareas_soft join sistema.usuarios using(id_usuario)";
		

if ($_POST['borrar']) {
$db->StartTrans();
$id_tareas=PostvartoArray('elim_'); //crea un arreglo con los checkbox chequeados
$tam_arch=sizeof($id_tareas); 
if ($id_tareas){  //para ver si hay check seleccionados
 $list=implode(",",$id_tareas);

$sql="update tareas_soft set tarea_activa=2 where id_tarea_soft in ($list)";	
sql($sql,"No se pudo dar de baja los datos la tareas")or fin_pagina();

}
if ($db->Completetrans()) 
  Aviso("La baja se realizó con exito");
else Aviso ("Error al dar baja");
}

?>
<br>
<form name="form1" method="POST" action="listado_tareas.php">
<script>

var vent_cb=new Object();
vent_cb.closed=true;

function cant_chequeados() {
var cant=0;
var i,sum=0;

cant=window.document.all.cant.value;
  for (i=0;i<cant;i++) {
  	c=eval("window.document.all.elim_"+i);
  	if (typeof(c) !='undefined') {
	if (c.checked) {
  		sum++;
	}
  	}
}

if (sum > 0) return true;
else {
	 alert ('Debe seleccionar al menos una tarea a eliminar');
	 return false;
}
}

</script>
<center>
<table width="100%">
<tr>
<td align="center">
<?if (permisos_check("inicio","permiso_borrar_tareas")) {?>
<input type=submit name=borrar value='Borrar Tarea' onclick="return (cant_chequeados());">
<?}?>
<?
$link = encode_link("cargar_tarea.php",array("pagina"=>2));
echo "&nbsp;&nbsp;<input type=button name=buscar1 value='Nueva Tarea' onclick='window.open(\"$link\",\"\",\"top=50, left=170, width=800, height=600, scrollbars=1, status=1,directories=0\");'>&nbsp;&nbsp;";

if ($cmd=='A') $where="tarea_activa=1";
else if ($cmd=='F') $where="tarea_activa=0";

list($sql,$total,$link_pagina,$up) = form_busqueda($query,$orden,$filtro,$link_tmp,$where,"buscar");

$result = sql($sql,"error en busqueda") or die("$sql<br>Error en form busqueda");

echo "&nbsp;&nbsp;<input type=submit name=form_busqueda value='Buscar'>";


?>
</td>
</tr>
</table>
</CENTER>
<BR>

<?=$parametros["msg"];?>
 <input type='hidden' name="cant" value='<?=$total?>'>
<TABLE class="bordes" align="center" width="98%" cellspacing="1">
<TR id="ma">
<TD colspan="3" align="left" >Cantidad de tareas: <?=$total?></TD>
<TD colspan="3" align="right"> <?=$link_pagina?></TD>
</TR>
<TR id="mo">
<?if (permisos_check("inicio","permiso_borrar_tareas")) {?>
 <td width="1%">Borrar</td>
<?}?>
<TD width="8%"><a id=mo href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"1","up"=>$up,"tipo"=>$tipo_p))?>'>Nro</A></TD>
<TD width="8%"><a id=mo href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"2","up"=>$up,"tipo"=>$tipo_p))?>'>Encargado</A></TD>
<TD width="15%"><a id=mo href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"3","up"=>$up,"tipo"=>$tipo_p))?>'>Tarea/Asunto</A></TD>
<TD width="17%"><a id=mo href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"4","up"=>$up,"tipo"=>$tipo_p))?>'>Vencimiento</A></TD>
<TD width="17%"><a id=mo href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"5","up"=>$up,"tipo"=>$tipo_p))?>'>Periodicidad</A></TD>
</TR>
<? 
$i=0;
while(!$result->EOF){
	$fec=$result->fields["id_tarea_soft"];
	$link = encode_link("cargar_tarea.php",array("id_tarea"=>$result->fields["id_tarea_soft"],"pagina"=>1));
    $puesto_rma=$result->fields["puesto_servicio_tecnico"];
   /* if ($puesto_rma) $color="#FF8080";
                else $color="#B7C7D0";*/
     $fec=$result->fields["aviso"];
     $fecv=$result->fields["vencimiento"];
     $fecha=date("Y-m-d",mktime());
     $tp=$result->fields["peridiocidad"];
     if($tp==1)
     {
     	$tipo="SI";	
     }
     else 
     {
     	$tipo="NO";	
     }
    
     if(compara_fechas($fecha,$fecv)==1)
    {
     ?>
		<tr <?=atrib_tr();?>  title="<?=$result->fields["descripcion"]?>">
		<?if (permisos_check("inicio","permiso_borrar_tareas")) {?>
          <td align="center"><input type="checkbox" value="<?=$result->fields["id_tarea_soft"];?>" name="elim_<?=$i?>" class="estilos_check"></td>
         <?}?>
		<TD align="center" bgcolor="Red" onclick="document.location='<?=$link?>'"><?=$result->fields["id_tarea_soft"];?></TD>
		<TD align="center" bgcolor="Red" onclick="document.location='<?=$link?>'"><?=$result->fields["nombre"];?>&nbsp;<?=$result->fields["apellido"];?></TD>
		<TD align="center" bgcolor="Red" onclick="document.location='<?=$link?>'"><?=$result->fields["asunto"];?></TD>
		<TD align="center" bgcolor="Red" onclick="document.location='<?=$link?>'"><?=Fecha($result->fields["vencimiento"]);?></TD>
		<TD align="center" bgcolor="Red" onclick="document.location='<?=$link?>'"><?=$tipo;?></TD>
		</TR>
	<?
    }
	else 
	{
	?>
		<tr <?=atrib_tr();?>  title="<?=$result->fields["descripcion"]?>">
		<?if (permisos_check("inicio","permiso_borrar_tareas")) {?>
          <td align="center"><input type="checkbox" value="<?=$result->fields["id_tarea_soft"];?>" name="elim_<?=$i?>" class="estilos_check"></td>
         <?}?>
		<TD align="center" onclick="document.location='<?=$link?>'"><?=$result->fields["id_tarea_soft"];?></TD>
		<TD align="center" onclick="document.location='<?=$link?>'"><?=$result->fields["nombre"];?>&nbsp;<?=$result->fields["apellido"];?></TD>
		<TD align="center" onclick="document.location='<?=$link?>'"><?=$result->fields["asunto"];?></TD>
		<?
		if((compara_fechas($fecha,$fec)==1)||(compara_fechas($fecha,$fec)==0))
	    {
		?>
		<TD align="center" bgcolor="Red" onclick="document.location='<?=$link?>'"><?=Fecha($result->fields["vencimiento"]);?></TD>
		<?
	    }
	    else 
	    {
		?>
		<TD align="center" onclick="document.location='<?=$link?>'"><?=Fecha($result->fields["vencimiento"]);?></TD>
		<?
	    }
	    ?>
		<TD align="center" onclick="document.location='<?=$link?>'"><?=$tipo;?></TD>
		</TR>
	<?
	}
	
	$result->MoveNext();
	$i++;
	}?>
</TABLE>
<br><br>
<table align="center" bgcolor="#FFFFFF" class="bordes">
<tr>

<td><b>Vencimiento solamente en</b></td>&nbsp;
<td width="5%" bgcolor="Red">&nbsp;</td><td><b>Se cumplio la fecha de Aviso</b></td>

</tr>
<tr>
<td><b>Todo los campos en</b></td>
<td width="5%" bgcolor="Red"></td><td><b> La Tarea Ya Vencio</b></td>
</tr>
</table>

</FORM>

<?
fin_pagina();
?>
</BODY>
