<?

require_once("../../config.php");
$estado=-1;

$_ses_global_manuales_distrito=="plan_nacer";
$id_distrito=1;

if($parametros['download'])
{
 $query="select nombre,tipo,tamaño from archivo_manual where id_archivo_manual=".$parametros['download'];
 $result=sql($query) or fin_pagina();
 $path=UPLOADS_DIR."/Calidad/Manuales";
 $FileNameFull="$path/".$result->fields['nombre'];
 if (file_exists($FileNameFull))
 {
	Mostrar_Header($result->fields['nombre'],$result->fields['tipo'],$result->fields['tamaño']);
	readfile($FileNameFull);
 }
 else
 {
	Mostrar_Error("Se produjo un error al intentar abrir el archivo");
 }
}

if($_POST['historial']=="Pasar a Historial")
{
 $db->StartTrans();
 $fecha_hoy=date('Y-m-d H:i:s',mktime());
 $query="update manual set historial=1 where id_manual=".$parametros['id'];
 if(sql($query) or fin_pagina())
 {  $query="insert into log_manual(id_manual,fecha,usuario,tipo) values(".$parametros['id'].",'$fecha_hoy','".$_ses_user['name']."','pase a historial')";
    if(sql($query) or fin_pagina())
 	 $msg="<b><center>El manual con ID ".$parametros['id']." se pasó al listado de historial</center></b>";
    else
     $msg="<b><center>El manual con ID ".$parametros['id']." no se logró pasar al listado de historial</center></b>";
 }
 else
    $msg="<b><center>El manual con ID ".$parametros['id']." no se logró pasar al listado de historial</center></b>";
 $db->CompleteTrans();
 $link=encode_link("listado_manual.php",array("msg"=>$msg,"distrito"=>$_ses_global_manuales_distrito));
 echo "<script>window.document.location='$link'</script>";
 //header("Location:'$link'");
}

if($_POST['revisado']=="Revisado")
{$db->StartTrans();
 $fecha_hoy=date('Y-m-d H:i:s',mktime());
 $query="update manual set estado=1 where id_manual=".$parametros['id'];
 if(sql($query) or fin_pagina())
 {  $query="insert into log_manual(id_manual,fecha,usuario,tipo) values(".$parametros['id'].",'$fecha_hoy','".$_ses_user['name']."','revisión')";
    if(sql($query) or fin_pagina())
 	 $msg="El manual con ID ".$parametros['id']." se actualizó";
    else
     $msg="El manual con ID ".$parametros['id']." no se logró actualizar";
 }
 $db->CompleteTrans();

 $link=encode_link("listado_manual.php",array("msg"=>$msg,"distrito"=>$_ses_global_manuales_distrito));
 echo "<script>window.document.location='$link'</script>";
}

if($_POST['validado']=="Validado")
 {
     $db->StartTrans();
     $fecha_hoy=date('Y-m-d H:i:s',mktime());
     $query="update manual set estado=2 where id_manual=".$parametros['id'];
     if(sql($query) or fin_pagina())
     {  $query="insert into log_manual(id_manual,fecha,usuario,tipo) values(".$parametros['id'].",'$fecha_hoy','".$_ses_user['name']."','validación')";
        if(sql($query) or fin_pagina())
     	 $msg="El manual con ID ".$parametros['id']." se actualizó";
        else
         $msg="El manual con ID ".$parametros['id']." no se logró actualizar";
     }
     $db->CompleteTrans();
     $link=encode_link("listado_manual.php",array("msg"=>$msg,"distrito"=>$_ses_global_manuales_distrito));
     echo "<script>window.document.location='$link'</script>";
 }

if($_POST['boton']=="Guardar1")
{
print_r($_POST);

print($_POST[clas_manual]);
}

if($_POST['boton']=="Guardar")
{
     //clas_manual.value;
	 $extensiones = array("doc","obd","xls","zip");
     $fecha_hoy=date('Y-m-d H:i:s',mktime());
     $db->StartTrans();
     if($parametros['id'])//si esta el id, actualizamos
     {
      $id_manual=$parametros['id'];
      $query="update manual set titulo='".$_POST['titulo']."',descripcion='".$_POST['descripcion']."',id_clasificacion='".$_POST['clas_manual']."' where id_manual=$id_manual";
      if(sql($query) or fin_pagina())
      {
       $query="insert into log_manual(id_manual,fecha,usuario,tipo) values($id_manual,'$fecha_hoy','".$_ses_user['name']."','actualización del manual')";
       if(sql($query) or fin_pagina())
        $bien=1;
       else
        $bien=0;
      }
      else
       $bien=0;
     }
     else//si no esta el id se inserta
     {
      $query="insert into manual (titulo,descripcion,estado,historial,id_distrito,id_clasificacion)
               values('".$_POST['titulo']."','".$_POST['descripcion']."',0,0,$id_distrito,'".$_POST['clas_manual']."')";
      if(sql($query) or fin_pagina())
      {
       $query="select max(id_manual) as maxid from manual";
       $maxid=sql($query) or fin_pagina();
       $id_manual =$maxid->fields['maxid'];

       $query="insert into log_manual(id_manual,fecha,usuario,tipo) values($id_manual,'$fecha_hoy','".$_ses_user['name']."','creación del manual')";
       if(sql($query) or fin_pagina())
        $bien=1;
       else
        $bien=0;
      }
     }
      if($_FILES['archivo']["name"]!="" && $bien)
      {
       $size=$_FILES["archivo"]["size"];
       $type=$_FILES["archivo"]["type"];
       $name=$_FILES["archivo"]["name"];
       $temp=$_FILES["archivo"]["tmp_name"];

       $path=UPLOADS_DIR."/Calidad/Manuales";
       $FileSize="";
       $FileType="";
       $ret = FileUpload($temp,$size,$name,$type,$max_file_size,$path,"",$extensiones,"",1,0,0);
       if($ret["error"]==0)
       {//si ya hay un archivo cargado para el manual, entonces actualizamos la
        //entrada, sino agregamos una nueva entrada en artchivo_manual
       	$query="select id_manual from archivo_manual where id_manual = $id_manual";
        $res_nbre_arch=sql($query) or fin_pagina();
        if($res_nbre_arch->RecordCount()==0)
        {
        $query="insert into archivo_manual(id_manual,nombre,tipo,tamaño)
                 values($id_manual,'$name','$type',$size)";
         if(sql($query) or fin_pagina())
          $bien=1;
         else
          $bien=0;
         $tipo="archivo subido";
        }
        else
        {
          $query="update archivo_manual set nombre='$name',tipo='$type',tamaño=$size where id_manual=$id_manual";
          if(sql($query) or fin_pagina())
           $bien=1;
          else
           $bien=0;
          $tipo="modificación de archivo";
        }
        if($bien)
        {//luego ponemos Agregamos la entrada en el log
         $query="insert into log_manual(id_manual,fecha,usuario,tipo) values($id_manual,'$fecha_hoy','".$_ses_user['name']."','$tipo')";
         sql($query) or fin_pagina();
        }
       }//de if($ret==0)
      }
     $db->CompleteTrans();
     if($bien)
      $msg="El manual con ID $id_manual se insertó/actualizó con éxito";
     else
      $msg="El manual con ID $id_manual no se pudo insertar/actualizar";
     $link=encode_link("listado_manual.php",array("msg"=>$msg,"distrito"=>$_ses_global_manuales_distrito));

     echo "<script>window.document.location='$link'</script>";
}


$disabled_historial="disabled";
$disabled_revisado="disabled";
$disabled_validado="disabled";
$id_manual=$parametros['id'];

if($id_manual){
     //traemos los datos del manual seleccionado
     $query="select manual.*,archivo_manual.nombre,archivo_manual.id_archivo_manual,fecha,usuario,log_manual.tipo,id_clasificacion from manual join log_manual using (id_manual) left join archivo_manual using (id_manual) where id_manual=$id_manual order by log_manual.fecha";
     $resultado=sql($query) or fin_pagina();

     $titulo=$resultado->fields['titulo'];
     $c_manual=$resultado->fields['id_clasificacion'];
     $descripcion=$resultado->fields['descripcion'];
     $estado=$resultado->fields['estado'];
     $nombre_archivo=$resultado->fields['nombre'];
     $id_archivo=$resultado->fields['id_archivo_manual'];
     if($resultado->fields['estado']==0)//esta pendiente
         {
          $disabled_historial="disabled";
          $disabled_revisado="";
          $disabled_validado="disabled";
         }
     elseif($resultado->fields['estado']==1)//esta revisado
             {
              $disabled_historial="disabled";
              $disabled_revisado="disabled";
              $disabled_validado="";
             }
             elseif($resultado->fields['estado']==2 && $resultado->fields['historial']==0)//esta validado
             {
              $disabled_historial="";
              $disabled_revisado="disabled";
              $disabled_validado="disabled";
             }
             elseif($resultado->fields['historial']==1)//está en el hisotrial
             {
              $disabled_historial="disabled";
              $disabled_revisado="disabled";
              $disabled_validado="disabled";
             }

     if(!permisos_check("inicio","permiso_manual"))
      $disabled_historial="disabled";

     if(!permisos_check("inicio","permiso_manual"))
      $disabled_revisado="disabled";

     if(permisos_check("inicio","permiso_manual"))
        $disabled_validado="";
        else
        $disabled_validado=" disabled";

}

echo $html_header;
?>
<script>
function control_datos()
{
 
 if(document.all.titulo.value==""){
 	alert('Debe ingresar un título para el manual');
  	return false;
 }		
 
 if(document.all.descripcion.value==""){
 	alert('Debe ingresar una descripción para el manual');
 	return false;
 }

 
 if(document.all.clas_manual.value==-1){
 	alert('Debe ingresar una Clasificación para el manual');
  	return false;
 }

 return true;
}
</script>

<br>
<?

//if($id_manual) --> para ver el log
//if(false) --> para ocultar los log
if($id_manual)
{
 ?>
 <div style='position:relative; width:100%; height:15%; overflow:auto;'>
 <table width="90%"  border="0" align="center">
 <?
 while(!$resultado->EOF)
 {?><tr id=ma>
     <td align='left'>
      Fecha de <?=$resultado->fields['tipo']?>: <?$fecha_aux=split(" ",$resultado->fields['fecha']);echo fecha($fecha_aux[0])." ".$fecha_aux[1]?>
  	</td>
  	<td align='right'>
  	 Usuario: <?=$resultado->fields['usuario']?>
  	</td>
    </tr>
  <?
  $resultado->MoveNext();
 }
 ?>
 </table></div>
<?
}
?>
<br>
<?
$link=encode_link("detalle_manual.php", array("id" =>$id_manual,"distrito"=>$_ses_global_manuales_distrito));
?>
<form name="form1" action="<?=$link?>" method="POST" enctype='multipart/form-data'>
<input type="hidden" name="id" value="<?=$id?>">
<table width="90%"  border="1" align="center">
<tr>
   <td id=mo colspan="2">
    Información del Manual
   </td>
  </tr> 

 <tr>
 	<td>
 		<b>Título</b> 
 	</td>
 	<td>
  		<input type="text" name="titulo" value="<?=$titulo?>" style="width=95%">
 	</td>
 </tr>
 
  <tr>
  	<td> <strong>Clasificación del Manual</strong></td>
    <td style="width=95%">
    	<?
    	//traemos los tipos de entidad posibles
    	$query="select id_clasificacion,descripcion from clasificacion_manual order by descripcion ASC";
        $clas_manual=sql($query) or fin_pagina();
        ?>
      <select name="clas_manual">
      	<option value=-1 selected>Seleccione la Clasificación del Manual</option>
        <?
        while(!$clas_manual->EOF){
        if 	($clas_manual->fields['id_clasificacion'] == $c_manual)
        	$selected="selected";
        else 
        	$selected="";
        ?>
        <option value="<?=$clas_manual->fields['id_clasificacion']?>" <?=$selected?>><?=$clas_manual->fields['descripcion']?></option>
        <?
        $clas_manual->MoveNext();
        }
        ?>
       </select>
     </td>
  </tr>
  
  
  <tr>
  <td>
   <b>Estado</b>
  </td>
  <td>
   <?switch($estado)
    {case 0:echo "Pendiente";break;
     case 1:echo "Revisado";break;
     case 2:echo "Aprobado";break;
     default:echo "Nuevo";break;
    }
   ?>
  </td>
 </tr>
 <tr>
 <td>
  <b>Descripción</b>
 </td>
 <td>
  <textarea name="descripcion" cols="88" rows="8"><?=$descripcion?></textarea>
 </td>
</tr>
<tr>
 <td><br>
    <b>Archivo a subir</b>
 </td>
 <td><br>
	 <input type=file name='archivo' style="width=95%">
 </td>
</tr>
</table>
<br>
<?
$link=encode_link("detalle_manual.php",array("download"=>$id_archivo,"distrito"=>$_ses_global_manuales_distrito));
if($nombre_archivo)
{
?>
<b>Archivo Subido:</b> <a href='<?=$link?>'><?=$nombre_archivo?></a>
<?
}
?>
<div align="center">
<?
$link=encode_link("listado_manual.php",array("distrito"=>$_ses_global_manuales_distrito));
?>
      <input type=submit name='historial' <?=$disabled_historial?> value='Pasar a Historial' title="Pasa el manual al listado de historial">&nbsp;
      <input type=submit name='boton' value='Guardar' onclick="return control_datos()">&nbsp;&nbsp;&nbsp;
      <input type=button name='files_cancel' value='Volver' onclick="document.location='<?=$link?>'" title="Volver al listado">&nbsp;
      <input type="submit" name="revisado" <?=$disabled_revisado?> value="Revisado" title="Indica que el manual ha sido revisado">&nbsp; 
      <input type="submit" name="validado" value="Validado" <?=$disabled_validado?> title="Indica que el manual ha sido validado">&nbsp;
</div>
</form>
</body>
</html>
<?
echo fin_pagina();
?>