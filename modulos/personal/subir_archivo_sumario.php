<?
/*
Autor: quique
Creado: miercoles 04/08/04

MODIFICADA POR
$Author: mari $
$Revision: 1.4 $
$Date: 2007/01/03 21:08:15 $
*/
$filecount=$_POST['select_filecount'] or $filecount=1;
$max_file_count=$parametros['max_file_count'] or $max_file_count=10;

require_once("../../config.php");
$id_sumario=$parametros['id_sumario'] or $id_sumario=$_POST["id_sumario"];
$id_legajo=$parametros['id_legajo'] or $id_legajo=$_POST["id_legajo"];
if ($_POST['baceptar'])
{
	$db->StartTrans();
    $acceso="Todos";
    $fecha=date("Y-m-d H:i:s");
    $fecha1=date("Y-m-d");
    $files_total=$_POST['select_filecount'];
    $id_legajo=$_POST['id_legajo'];
    $id_sumario=$_POST['id_sumario'];
    $sacar="archivo[1]";
    $filename=$_FILES["archivo"]["name"][0];
    $tamanio=$_FILES['archivo']["size"][0];
    $error_vector=array();  
    $cadena=UPLOADS_DIR."/personal/$id_legajo";
    for ($i=0; $i < $files_total ; $i++ )
    {
    	$filename=$_FILES["archivo"]["name"][$i];
    	$tamanio=$_FILES['archivo']["size"][$i];
    	if ($tamanio > $max_file_size) {
           Error("El archivo $filename es muy grande");
    	}
    	if($filename!="" && !$error)
    	{
	    if (!$error_msg) 
	    {
	    	if (subir_archivo($_FILES["archivo"]["tmp_name"][$i],"$cadena/$id_sumario/$filename",$error_msg)===true)
	    	{
	          $sql="select nextval('sumario_archivos_id_sumario_archivo_seq') as idfile ";
	         $res=sql($sql) or $db->errormsg()."<br>";
	         $idfile=$res->fields['idfile'];
	         $q="INSERT INTO personal.sumario_archivos
	              (id_sumario_archivo,id_sumario_personal,nombre_archivo,tamano,usuario_subido,fecha_subido,tipo) Values
	              ($idfile,$id_sumario,'$filename','$tamanio','".$_ses_user['login']."','$fecha','$acceso');";
	         //$q.="insert into archivos_ordprod (id_archivo,nro_orden) values ($idfile,$nro_orden);";

	         if (!sql($q))
	           $error_msg="No se pudo insertar el archivo ".$db->errormsg()."<br>$q ";	
	         else 
	           $ok_msg="El archivo '$filename' se subio con éxito";
	         }
	         
	    	
	    }
    	}
	     $error_vector[]=$error_msg;
	     $error_msg="";
	     $ok_vector[]=$ok_msg;
	     $ok_msg="";
    }
   
   
     $db->Completetrans();
}


//-------------------------------------------------------------------

//Si se producen errores se deben dejar en una variable $error_vector
//El informe de procesamiento se debe dejar en una variable $ok_vector
//Las variables se imprimen automaticamente

//-------------------------------------------------------------------
echo $html_header; ?>
<script src="../../lib/funciones.js"></script>
<script>
function control()
{
	var t=0;
	while(document.all.total.value>t)
	{
	var co=eval("document.all.archivo.value");		
	
	if(co=="")
	{
        alert ("falta ingresar los archivos");
		return false;
	}
	t++;	
	}
	return true;
	
}
</script>

<form name='form_archivos' action='subir_archivo_sumario.php' method=POST enctype='multipart/form-data'>
<input type="hidden" name="MAX_FILE_SIZE" value="0"><!-- NO MAX -->
<!-- NO MAX -->
<center><b>
<? 
  if (is_array($error_vector))
  {
	foreach ($error_vector  as $valor )
  	  echo "<font color=red>$valor</font><br>";
  }
  if (is_array($ok_vector))
  {
	foreach ($ok_vector  as $valor )
  	  echo "<font color=green>$valor</font><br>";
  }
?>
</b><br></center>
<table border=1 cellspacing=0 cellpadding=5 bgcolor=#D5D5D5 align=center>
<tr>
  <input type="hidden" name="id_sumario" value="<?=$id_sumario?>">
  <input type="hidden" name="id_legajo" value="<?=$id_legajo?>">
  <input type="hidden" name="pagina" value="1">
  <input type="hidden" name="seleccionar" value="0">
  <td style="border:#E0E0E0;" colspan=2 align=center id=mo><font size=3><b>Archivos<?=$nombre_producto?></b></td>
</tr><tr>
<td align=right colspan=2><b>Cantidad de archivos:</b>
<select name='select_filecount' onchange='document.forms[0].submit()'">
<? for ($i=1; $i<=$max_file_count ; $i++)
   {// 
?>
	<option <?= ($filecount==$i)?"selected":""; ?> ><?=$i ?></option>
<? } ?>	
</select>
</td>
</tr>
<? 
 $i=1;
 $j=0;
 while ($filecount--)
 {
?>
<tr>

  <td align=right>Archivo <?= $i ?>: </td>
  <td><b>Archivo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b><input type=file id="archivo" name='archivo[<?=$j?>]' size=30><br>
  </td>
</tr>
<?
$i++;
$j++;
 }
?>
<tr>
<td align=center colspan=2>
<input type="hidden" name="total" value="<?=$j?>">
<input type=submit name='baceptar' value='Aceptar' onclick="return control(); document.all.div_aceptar.style.display = 'block';<?=$onclick["aceptar"]?>">&nbsp;&nbsp;&nbsp;
<input type="button" name="Cerrar" value="Cerrar" onclick="window.close();window.opener.partir1();">
</table>
<br>
<br>
<div align="center" id=div_aceptar style="display:none" >
Subiendo Archivos.....<br>
Espere por favor...<br><br>
<!--<img src="../../imagenes/progreso.gif" border=0>-->
</div>
</form><br>
<?=fin_pagina(); ?>