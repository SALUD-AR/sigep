<?
/*
Creado por: Quique

Modificada por
$Author: mari $
$Revision: 
$Date: 2006/05/10 13:27:03 $
*/


require_once("../../config.php");
//funcion para mandar el arreglo por post
//print_r($parametros);
$id_sumario=$parametros['id_sumario'] or $id_sumario=$_POST["id_sumario"];
$id_legajo=$parametros['id_legajo'] or $id_legajo=$_POST["id_legajo"];
$pagina=$parametros['pagina'] or $pagina=$_POST["pagina"];
$filecount=$_POST['select_filecount'] or $filecount=1;
$max_file_count=$parametros['max_file_count'] or $max_file_count=10;

$cmd=$parametros["cmd"];

if ($cmd=="download") {
    $file=$parametros["file"];
    $size=$parametros["size"];
    $id_legajo=$parametros['id_legajo'];
    $id_sumario=$parametros['id_sumario'];
    Mostrar_Header($file,"application/octet-stream",$size);
    $filefull = UPLOADS_DIR ."/personal/".$id_legajo."/".$id_sumario."/".$file;
    readfile($filefull);
    exit();
}

echo $html_header;
cargar_calendario();

if ($_POST['baceptar'])
{
	$db->StartTrans();
    $acceso="Todos";
    $fecha=date("Y-m-d H:i:s");
    $fecha1=date("Y-m-d");
    $id_legajo=$_POST['id_legajo'];
    $pagina=$_POST['pagina'];
    $titul=$_POST['titulo_sum'];
    $desc=$_POST['desc_sum'];
    if($pagina==1)
    {
    $sql="select nextval('sumarios_personal_id_sumario_personal_seq') as idsumario ";
	$res=sql($sql) or $db->errormsg()."<br>";
	$id_sumario=$res->fields['idsumario'];
    $q1="INSERT INTO personal.sumarios_personal
	   (id_sumario_personal,id_legajo,fecha,titulo,descripcion) Values
	   ($id_sumario,$id_legajo,'$fecha1','$titul','$desc');";
    sql($q1,"NO se pudo guardar el nuevo sumario") or fin_pagina();
    $pagina=0;
    }
    else 
    {
    $fecha1=Fecha_db($_POST['fecha_sum']);
	$id_sumario=$_POST['id_sumario'];
    $q1="update personal.sumarios_personal set fecha='$fecha1',titulo='$titul',descripcion='$desc' where id_sumario_personal=$id_sumario;";
    sql($q1,"NO se pudo guardar el sumario") or fin_pagina();
    $cadena=UPLOADS_DIR."/personal/$id_legajo";
    }
    $db->Completetrans();
}
?>
<script src="../../lib/funciones.js"></script>
<script>
function control()
{
	var tit=eval("document.all.titulo_sum.value");	
	var des=eval("document.all.desc_sum.value");	
	if(tit=="")
	{
        alert ("falta ingresar el titulo");
		return false;
	}
	if(des=="")
	{
        alert ("falta ingresar la descripcion");
		return false;
	}	
	/*var t=0;
	while(document.all.total.value>t)
	{
	var co=eval("document.all.archivo.value");	
	if(co=="")
	{
        alert ("falta ingresar los archivos");
		return false;
	}
	t++;	
	}*/
	return true;
	
}
</script>
<form name='form_archivos' action='nuevo_sumario.php' method=POST enctype='multipart/form-data'>
<input type="hidden" name="MAX_FILE_SIZE" value="0"><!-- NO MAX -->
<!-- NO MAX -->
<input type="hidden" name="id_sumario" value="<?=$id_sumario?>">
<input type="hidden" name="id_legajo" value="<?=$id_legajo?>">
<?
if($pagina==1)
{	
?>
<table align="center" width="70%" border="1">
<tr>
<input type="hidden" name="pagina" value="1">
<td align="center">
<font color="Blue" size="4">Sumario/Incidentes</font>
</td></tr>
</table> 
<?
$fecha=date("d/m/Y");
?>
  <table align="center" width="70%" border="1">
	  	<tr>
	  		<td><b>Fecha: <input type=text name=fecha_sum value='<?=$fecha?>' size=10 maxlength=10></b></td>
	      </tr>
	    <tr>
	      <td><b>Titulo: </b><input type=text name=titulo_sum value='<?=$tit?>' size=70></td>
	    </tr>
	    <tr>
	      <td><b>Descripcion</b></td>
	    </tr>
	     <tr>
	      <td><textarea name="desc_sum" rows="4" cols="90"><?=$desc1?></textarea></td>
	    </tr>
</table>
<br>
<br>
<table align="center" width="100%">
<tr>
<td align="center">
<input type="hidden" name="total" value="<?=$j?>">
<input type=submit name='baceptar' value='Guardar' onclick="return control(); document.all.div_aceptar.style.display = 'block';<?=$onclick["aceptar"]?>">&nbsp;&nbsp;&nbsp;
<input type="button" name="Cerrar" value="Cerrar" onclick="window.opener.location.reload();window.close();">
</td>
</tr>
</table>
<?
}
else
{
$sel_sum="select * from personal.sumarios_personal where id_sumario_personal=$id_sumario";
$sumario=sql($sel_sum,"No se pudo recuperar los sumarios") or fin_pagina();
$sel_arc="select * from sumario_archivos where id_sumario_personal=$id_sumario";
$arch=sql($sel_arc,"No se pudo recuperar los sumarios") or fin_pagina();	
$can_arc=$arch->RecordCount();	
?>
<table align="center" width="70%" border="1">
<tr>
<td align="center">
<font color="Blue" size="4">Sumario/Incidentes</font>
</td></tr>
</table> 
  <table align="center" width="70%" border="1" bgcolor="Silver">
	  	<tr>
	  	  <td><b>Fecha:</b>
	      <input type=text name=fecha_sum value='<?=fecha($sumario->fields['fecha'])?>' size=10 maxlength=10><?=link_calendario("fecha_sum")?></td>
	  	</tr>
	    <tr>
	      <td><b>Titulo: </b><input type=text name=titulo_sum value='<?=$sumario->fields['titulo']?>' size=70></td>
	    </tr>
	    <tr>
	      <td><b>Descripcion</b></td>
	    </tr>
	     <tr>
	      <td><textarea name="desc_sum" rows="4" cols="90"><?=$sumario->fields['descripcion']?></textarea></td>
	    </tr>
	    <?
	    if($can_arc!=0)
	    {
	    ?>
	     <tr>
	      <td>
	      <table border="1" width="100%">
	      <tr>
	      <td align="center" colspan="2"><b><font color="Blue">Archivos Subidos</font></b></td>
	      </tr>
	      <tr>
	      <td width="80%" align="center"><b>Nombre Archivo</b></td>
	      <td width="10%" align="center"><b>Subido</b></td>
	      </tr>
	      <? $t=1;
	      while(!$arch->EOF)
	      {
	      ?>
	      <tr>
	  	      <td>
	        <? 
	        if (is_file(UPLOADS_DIR."/personal/".$id_legajo."/".$arch->fields['id_sumario_personal']."/".$arch->fields['nombre_archivo'])) {
				 echo "<a href='".encode_link("nuevo_sumario.php",array ("id_legajo"=>$id_legajo,"id_sumario"=>$arch->fields['id_sumario_personal'],"file" =>$arch->fields['nombre_archivo'],"size" => $arch->fields["tamano"],"cmd" => "download"))."'>";
                 echo $arch->fields['nombre_archivo']."</a>";
	        }
	        ?>
	      </td>  
	    
	      <td><b><?=Fecha($arch->fields['fecha_subido'])?></b></td>
	      </tr>
	      <?
	      $arch->MoveNext();
	      $t++;
	      }
	      ?>
	      </table>
	      </td>
	    </tr>
	    <?}?>
</table>
</b>
<br>
<table align="center" width="100%">
<tr>
<?
$link = encode_link("subir_archivo_sumario.php",array("id_sumario"=>$id_sumario,"id_legajo"=>$id_legajo));	
$link1="window.open(\"$link\",\"\",\"top=50, left=170, width=800, height=600, scrollbars=1, status=1,directories=0\")";
?>
<td align="center">
<input type="hidden" name="total" value="<?=$j?>">
<input type="hidden" name="pagina" value="0">
<input type=submit name='baceptar' value='Guardar' id="baceptar" onclick="return control(); document.all.div_aceptar.style.display = 'block';<?=$onclick["aceptar"]?>">&nbsp;&nbsp;&nbsp;
<?echo "<input type=button name=subir value='Subir Archivo' onclick='$link1;'>";?>&nbsp;&nbsp;&nbsp;
<input type="button" name="Cerrar" value="Cerrar" onclick="window.opener.location.reload();window.close();">
</td>
</tr>
</table>
<?
}
?>
<script>

function partir1()
{
 var contar =eval("document.all.pagina");
     contar.value =0;
  document.forms[0].submit();     
}
</script>