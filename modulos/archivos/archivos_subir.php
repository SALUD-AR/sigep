<?

require_once("../../config.php");

//PARAMETROS de entrada
$onclick["cancelar"]=$parametros["onclickcancelar"] or $onclick["cancelar"]="window.close()";
$onclick["aceptar"]=$parametros["onclickaceptar"];
$filecount=$_POST['select_filecount'] or $filecount=1;
$max_file_count=$parametros['max_file_count'] or $max_file_count=10;
//archivo que requiere para procesar el formulario
require_once($parametros['proc_file']);
//-------------------------------------------------------------------

//Si se producen errores se deben dejar en una variable $error_vector
//El informe de procesamiento se debe dejar en una variable $ok_vector
//Las variables se imprimen automaticamente

//-------------------------------------------------------------------
echo $html_header; ?>
<script src="../../lib/funciones.js"></script>
<form name='form_archivos' action='<?= encode_link($_SERVER['SCRIPT_NAME'],$parametros) ?>' method=POST enctype='multipart/form-data'>
<input type="hidden" name="MAX_FILE_SIZE" value="0"><!-- NO MAX -->
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
  <td style="border:#E0E0E0;" colspan=2 align=center id=mo><font size=3><b>Subir archivos</b></td>
</tr><tr>
<td align=right colspan=2><b>Cantidad de archivos:</b>
<select name=select_filecount onchange='document.forms[0].submit()'>
<? for ($i=1; $i<=$max_file_count ; $i++)
   {
?>
	<option <?= ($filecount==$i)?"selected":""; ?> ><?=$i ?></option>
<? } ?>	
</select>
</td>
</tr>
<? 
 $i=1;
 while ($filecount--)
 {
?>
<tr>
  <td align=right>Archivo <?= $i ?>: </td>
  <td><input type=file name='archivo[<?=($i++-1) ?>]' size=30></td>
</tr>
<?
 }
?>
<tr>
<td align=center colspan=2>
<input type=submit name='baceptar' value='Aceptar' onclick="document.all.div_aceptar.style.display = 'block';<?=$onclick["aceptar"]?>">&nbsp;&nbsp;&nbsp;
<input type=button name='bcancelar' value='Cancelar' onclick="<?=$onclick["cancelar"]?>">
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