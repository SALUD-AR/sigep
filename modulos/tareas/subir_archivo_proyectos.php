<?

require_once("../../config.php");
$nro_proyecto=$parametros['nro_proyecto'] or $nro_proyecto=$_POST['nro_proyecto'];
$cantidad_archivos=$_POST['cant_archivos'] or $cantidad_archivos=1;

if ($_POST['volver']=="Volver")
{$link_padre=encode_link("cargar_proyectos.php",array("id"=>$nro_proyecto));
   echo "window.opener.location.href='$link_padre';";
}	

if ($_POST['guardar']=="Guardar")
{$db->StartTrans();
 $control=1;
 $msg=" ";
 while ($control<=$cantidad_archivos)
      {
		$size=$_FILES["file_".$control]["size"];
		$type=$_FILES["file_".$control]["type"];		
		$name=nombre_archivo($_FILES["file_".$control]["name"]);
		$temp=$_FILES["file_".$control]["tmp_name"];
		//$max_file_size=5120000;
		$path = UPLOADS_DIR."/tareas/archivos_subidos_proyectos";
		$extensiones = array("doc","pdf","zip");
		//function algo($uno,$dos){};
		$func = " ";		
		$ret = FileUpload($temp,$size,$name,$type,$max_file_size,$path,$func,$extensiones,"","",1,0);		
		switch ($ret["error"]) {
			case 0: {$cant_aviso=sizeof($_POST['avisar_'.$control]);
			         $inicio=0;
			         $arreglo=$_POST['avisar_'.$control];			         
			          $db->StartTrans();
			         while ($inicio<$cant_aviso)
			               {$sql="select mail,nombre,apellido,id_usuario from usuarios where id_usuario=".$arreglo[$inicio];
			                $resultado_sql=sql($sql) or fin_pagina();
			                while (!$resultado_sql->EOF)
			                      {$mensaje=$resultado_sql->fields['apellido'].", ".$resultado_sql->fields['nombre']." se le informa que ".$_ses_user['name']." ha subido un archivo al Proyecto Nro: $nro_proyecto";
			                       //echo $mensaje;
			                       enviar_mail($resultado_sql->fields['mail'],"Subieron Archivos en Proyectos",$mensaje,' ',' ',' ',0);
			                       $resultado_sql->MoveNext();
			                      }
			                $inicio++;      
			               }
				     $FileDateUp = date("Y-m-d H:i:s", mktime());
				     $user=$_ses_user['name'];								     
                      $sql = "select nextval('archivos_subidos_proyectos_id_archivo_subido_seq') as id";
                      $id_rec = sql($sql) or fin_pagina();
					  $sql = "insert into archivos_subidos_proyectos (id_archivo_subido,comentario,usuario,fecha,nombre_archivo_comp,nombre_archivo,id_proyecto,filetype,filesize,filesize_comp) values ";
					  $sql .="(".$id_rec->fields['id'].",'".$_POST["comentario_".$control]."','$user','$FileDateUp','".$ret["filenamecomp"]."','$name',$nro_proyecto,'$type',$size,".$ret["filesizecomp"].")";					 
					  $resultado_sql=sql($sql) or fin_pagina();
				     $db->CompleteTrans();
				     $msg.="<font size='2' color='red'>El Archivo ".$name." se subio Correctamente.</font><br>";
				} 
			 break;
			case 1: {
				$msg.="<font color='red'>Error: No especificó el archivo Nº $control o el mismo supera el tamaño máximo soportado.</font><br>";
			} break;
			case 2: {
				$msg.="<font color='red'>Error: Falla inesperada subiendo el archivo $name.</font><br>";
			} break;
			case 3: {
				$msg.="<font color='red'>Error: El sistema no acepta archivos vacios, fallo en Archivo Nº $control.</font><br>";
			} break;
			case 5: {
				$msg.="<font color='red'>Error: El sistema no acepta archivos que superen los ".($max_file_size/1024)."Kb de información, fallo en Archivo Nº $control.</font><br>";
			} break;
			case 6: {
				$msg.="<font color='red'>Error: El archivo $name ya existe en el Sistema y no esta permitido sobreescribir el mismo.</font><br>";
			} break;
			case 8: {
				$msg.="<font color='red'>Error: Fallo el proceso de compresión, pero el archivo $name fue guardado sin comprimir.</font><br>";
			} break;
		}
		$db->CompleteTrans();
	$control++;	
      }	
		//echo $msg;
	$cantidad_archivos=1;		
	}	

echo $html_header;	

$sql_usuarios="select * from usuarios order by nombre";
$consulta_sql_usuarios=sql($sql_usuarios) or fin_pagina();

?>

<form name='form1' action="subir_archivo_proyectos.php" method="POST" enctype="multipart/form-data">
<table align="center">
 <tr>
  <td align="center"><font size="2" color="Red"><b>Recuerde que el tamaño TOTAL de los archivos no puede superar los&nbsp;</font><font size="3" color="Red">5 MB.</font></b></td>
 </tr>
</table>
<input type='hidden' name='MAX_FILE_SIZE' value='<? echo $max_file_size; ?>'>

<table align="center">
 <tr><td><b><?=$msg?></b></td></tr>
</table>

<table align="center" width="70%" class="bordes">
 <tr id=mo><td><font size="2"><b>Subir Archivos</b></font></td></tr>
 <tr>
     <td align="right" colspan="2"><b>Seleccione cantidad de Archivos a Subir: &nbsp;</b>
      <select name="cant_archivos" onchange="document.all.form1.submit()">
       <option value=1><b>1</b></option>
       <option value=2 <?if ($cantidad_archivos==2) echo "selected"?>><b>2</b></option>
       <option value=3 <?if ($cantidad_archivos==3) echo "selected"?>><b>3</b></option>
       <option value=4 <?if ($cantidad_archivos==4) echo "selected"?>><b>4</b></option>
       <option value=5 <?if ($cantidad_archivos==5) echo "selected"?>><b>5</b></option>
       <option value=6 <?if ($cantidad_archivos==6) echo "selected"?>><b>6</b></option>
       <option value=7 <?if ($cantidad_archivos==7) echo "selected"?>><b>7</b></option>
       <option value=8 <?if ($cantidad_archivos==8) echo "selected"?>><b>8</b></option>
       <option value=9 <?if ($cantidad_archivos==9) echo "selected"?>><b>9</b></option>
       <option value=10 <?if ($cantidad_archivos==10) echo "selected"?>><b>10</b></option>
      </select>  
     </td>
    </tr>
 <?
  $cantidad=1;
  while ($cantidad<=$cantidad_archivos)
  { 
 ?>
 <tr>
  <td>
  
   <table width="100%" align="center" id=ma  border="1" cellspacing="1">    
    <tr >
     <td align="left" colspan="2" >
      <font color="Black"><b>Archivo:&nbsp;<?=$cantidad?></b></font>
     </td>
    </tr>
    <tr >
     <td align="left" colspan="2">
      <font color="Black"><b>Nombre Archivo:&nbsp;</b></font><INPUT type="file" name="file_<?=$cantidad?>">
     </td>
    </tr>  
    <tr>
     <td align="left" width="15%">
      <font color="Black"><b>Avisar a:&nbsp;</b></font>      
     </td>
     <td>
     
      <select name="avisar_<?=$cantidad?>[]" size="5" multiple>
      
      <?$consulta_sql_usuarios->MoveFirst();
        while (!$consulta_sql_usuarios->EOF)
        {
      ?>
        <option value="<?=$consulta_sql_usuarios->fields['id_usuario']?>"><?=$consulta_sql_usuarios->fields['nombre']?>&nbsp;<?=$consulta_sql_usuarios->fields['apellido']?></option> 
      <?
        $consulta_sql_usuarios->MoveNext();
        } 
      ?>  
      </select> 
     </td>
    </tr>
    <tr>
     <td align="left" width="15%">
      <font color="Black"><b>Comentarios:&nbsp;</b></font>      
     </td>
     <td>
      <textarea name="comentario_<?=$cantidad?>" rows="5" cols="35" ></textarea> 
     </td>
    </tr>
   </table>
  </td>  
 </tr>
 <?
  $cantidad++;
  }
 ?>
</table>

<input name="nro_proyecto" type="hidden" value="<?=$nro_proyecto?>">


<table align="center">
 <tr>
  <td><input name="guardar" type="submit" value="Guardar"></td>
  <td><input name="volver" type="button" value="Volver" onclick="location.href='<?= encode_link("cargar_proyectos.php",array("id_"=>$nro_proyecto)) ?>';window.opener.location.reload(); window.close();"></td>
 </tr>
</table>

</form>