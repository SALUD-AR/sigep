<?php

require_once "../../config.php";
require_once "../../lib/class.gacz.php";



$id_user=$_ses_user['id'];
$administrador=$_POST['administrador'];
$titulo=$_POST['titulo'];//titulo del proyecto
$fecha_inicio=$_POST['fecha_inicio'];//fecha de inicio del proyecto
$fecha_fin=$_POST['fecha_fin'];//fecha finalizacion del proyecto
$resumen=$_POST['resumen'];//descripcion del proyecto
$nro_proyecto=$parametros['id'] or $nro_proyecto=$_POST['nro_proyecto'] or $nro_proyecto="Nuevo";
$ordenar=$parametros['ordenar'] or 1;
$up=$parametros['up'] or "desc";
$permitido=1;


if ($nro_proyecto!="Nuevo" )
{$sql="select count(id_proyecto) from tareas_divisionsoft.proyectos
       left join tareas_divisionsoft.usuarios_proyectos using (id_proyecto)
       where ((usuarios_proyectos.id_usuario=$id_user or proyectos.id_usuario=$id_user) and id_proyecto=$nro_proyecto)";
 $resul_permitido=sql($sql) or fin_pagina();
 if ($resul_permitido->fields['count']==0) $permitido=0;
 //corapi siempre tiene permiso para todos los proyectos
 if($_ses_user["login"]=="corapi") $permitido=1;
}

if ($permitido==1)
{

if ($_POST['borrar_archivo']=="Borrar")
{
 $db->StartTrans();
 $aumento=1;
 $msg=" ";
 $cant_archivo=$_POST['cant_archivos'];
 while ($aumento<=$cant_archivo)
       {$check=$_POST['eliminar_'.$aumento];

       	if ($check)
       	   {//die($check);
       	    $archivo_comp=$_POST['nom_comp_'.$aumento];
       	    if (unlink(UPLOADS_DIR."/tareas/archivos_subidos_proyectos/$archivo_comp"))
       	       {$sql="delete from tareas_divisionsoft.archivos_subidos_proyectos where id_archivo_subido=".$_POST['id_archivo_'.$aumento];
       	        $borrado=sql($sql,"<br>Error al borrar el archivo $archivo_comp<br>") or fin_pagina();
       	        $msg.="El Archivo \"$archivo_comp\" se borro Correctamente.<br>";
       	       }
       	    else $msg.="El Archivo \"$archivo_comp\" no se pudo Borrar.<br>";
       	   }
       	$aumento++;
       }

  $db->CompleteTrans();
}//del post de borrar archivos
//////////////////////////////////////////////////////////////////////////////////////
if ($parametros["download"]) {
	$sql = "select * from archivos_subidos_proyectos where id_archivo_subido = ".$parametros["FileID"];
	$result = $db->Execute($sql) or die($db->ErrorMsg()."<br>$sql");
	if ($parametros["comp"]) {
		$FileName = $result->fields["nombre_archivo_comp"];
		$FileNameFull = UPLOADS_DIR."/tareas/archivos_subidos_proyectos/$FileName";
		$FileType="application/zip";
		$FileSize = $result->fields["filesize_comp"];
		FileDownload(1,$FileName,$FileNameFull,$FileType,$FileSize);
	} else {
		$FileName = $result->fields["nombre_archivo"];
		$FileNameFull = UPLOADS_DIR."/tareas/archivos_subidos_proyectos/$FileName";
		$FileType = $result->fields["filetype"];
		$FileSize = $result->fields["filesize"];
		FileDownload(0,$FileName,$FileNameFull,$FileType,$FileSize);
	}
}


//////////////////////////////////////////////////////////////////////////////////////

if ($_POST['guardar']=="Guardar")
   {$db->StartTrans();
   	if ($nro_proyecto=="Nuevo")
       {
        $sql="select nextval('proyectos_id_proyecto_seq') as nro_proyecto";
        $result_next_val=sql($sql) or fin_pagina;
        $nro_proyecto=$result_next_val->fields['nro_proyecto'];
        $sql="insert into tareas_divisionsoft.proyectos (id_proyecto,titulo,fecha_inicio,fecha_fin,descripcion,estado,id_usuario)
              values ($nro_proyecto,'$titulo','".fecha_db($fecha_inicio)."','".fecha_db($fecha_fin)."','$resumen',1,$id_user)";
        $result_insert=sql($sql) or fin_pagina();
        ///////////CARGO LOS USUARIOS DEL PROYECTO///////////
        $as=$_POST["usuarios_proyectos_Values"];
        if($as=="") $tam=0;
        else
            {$array=explode(",",$as);
             $tam=count($array);
            }
        for($i=0;$i<$tam;$i++)
           {
            $id_usuario=$array[$i];
            $sql="insert into usuarios_proyectos (id_usuario,id_proyecto) values(".$id_usuario.",".$nro_proyecto.")";
            $resul_insert_usuarios=sql($sql) or fin_pagina();
           }
       }
    else
        {$sql="update tareas_divisionsoft.proyectos set titulo='$titulo',fecha_inicio='".fecha_db($fecha_inicio)."',fecha_fin='".fecha_db($fecha_fin)."',descripcion='$resumen'
              where id_proyecto=$nro_proyecto";
         $result_update=sql($sql) or fin_pagina();
         /////////////////BORRO TODOS LOS USUARIOS///////////
         $sql="delete from usuarios_proyectos where id_proyecto=$nro_proyecto";
         $resul_borra_usuarios=sql($sql) or fin_pagina();
         /////////////////INSERTO LOS USUARIOS POR LAS DUDAS DE QUE HAYA PUESTO UNO NUEVO///////////
         $as=$_POST["usuarios_proyectos_Values"];
         if($as=="") $tam=0;
         else
             {$array=explode(",",$as);
              $tam=count($array);
             }
         for($i=0;$i<$tam;$i++)
            {
             $id_usuario=$array[$i];
             $sql="insert into usuarios_proyectos (id_usuario,id_proyecto) values(".$id_usuario.",".$nro_proyecto.")";
             $resul_insert_usuarios=sql($sql) or fin_pagina();
            }
         }
    if ($_POST['nuevo_comentario']!="")
       {$fecha=date("Y-m-d H:i:s");
        $usuario=$_ses_user['name'];
        $sql="insert into tareas_divisionsoft.log_proyectos (fecha,comentario,id_proyecto,usuario)
              values ('$fecha','".$_POST['nuevo_comentario']."',$nro_proyecto,'$usuario')";
        $result_insert_log=sql($sql) or fin_pagina();
       }
    $db->CompleteTrans();
    header("location:lista_proyectos.php");
   }

if ($_POST['guardar_comentario']=="Guardar Comentario")
   {$fecha=date("Y-m-d H:i:s");
    $usuario=$_ses_user['name'];
    $sql="insert into tareas_divisionsoft.log_proyectos (fecha,comentario,id_proyecto,usuario)
          values ('$fecha','".$_POST['nuevo_comentario']."',$nro_proyecto,'$usuario')";
    $result_insert_log=sql($sql) or fin_pagina();
   }

if ($nro_proyecto!="Nuevo")
   {$sql="select proyectos.*,usuarios.nombre,usuarios.apellido
   		  from proyectos join usuarios using(id_usuario) where id_proyecto=$nro_proyecto";
    $result_consulta=sql($sql) or fin_pagina();
    $administrador=$result_consulta->fields['id_administrador'];
    $titulo=$result_consulta->fields['titulo'];//titulo del proyecto
    $fecha_inicio=$result_consulta->fields['fecha_inicio'];//fecha de inicio del proyecto
    $fecha_fin=$result_consulta->fields['fecha_fin'];//fecha finalizacion del proyecto
    $resumen=$result_consulta->fields['descripcion'];//descripcion del proyecto
    $estado=$result_consulta->fields['estado'];//para saber si esta en pendiente o historial, y de acuerdo a eso poner el boton
                                               //para pasar de pendiente a historial y al reves.
   }

  if ((is_numeric($nro_proyecto))&&($_POST["pasar"])){
  	if($_POST["pasar"]=="Pasar a Historial") $val=2;
  	else $val=1;
   	sql("update tareas_divisionsoft.proyectos set estado=$val where id_proyecto=$nro_proyecto","c160") or fin_pagina();
  }

cargar_calendario();
echo $html_header;

?>

<SCRIPT language='JavaScript' src='funcion.js'></SCRIPT>
<script>
function control_datos()
{if(document.all.titulo.value=="")
 {alert('Debe poner uin titulo para el Proyecto');
  return false;
 }
 if(document.all.fecha_inicio.value=="")
 {alert('Debe especificar la Fecha de Inicio del Proyecto');
  return false;
 }
 if(document.all.fecha_fin.value=="")
 {alert('Debe especificar la Fecha de Fin del Proyecto');
  return false;
 }
/* if(document.all.administrador.value==-1)
 {alert('Debe seleccionar el Adminstrador del Proyecto');
  return false;
 }*/
 if(document.all.resumen.value=="")
 {alert('Debe colocar un Detalle del Proyecto');
  return false;
 }
 return true;
}

//////////////////Para mostrar o ocultar tablas
var img_ext='<?=$img_ext='../../imagenes/rigth2.gif' ?>';//imagen extendido
var img_cont='<?=$img_cont='../../imagenes/down2.gif' ?>';//imagen contraido
var cantidad_archivos;
cantidad_archivos=0;
function muestra_tabla(obj_tabla,nro)
{oimg=eval("document.all.imagen_"+nro);//objeto tipo IMG
 if (obj_tabla.style.display=='none')
    {obj_tabla.style.display='inline';
     oimg.show=0;
     oimg.src=img_ext;
     oimg.title='Ocultar Usuarios';
    }
 else
    {obj_tabla.style.display='none';
    oimg.show=1;
	oimg.src=img_cont;
	oimg.title='Mostrar Usuarios';
    }
}

///////////////FUNCIONES QUE PASAN VALORES ENTRE LOS SELECT
function moveOver() {
   var boxLength;// = document.form1.compatibles.length;
   var prodLength = document.all.usuarios_todos.length;
   var selectedText;  // = document.choiceForm.available.options[selectedItem].text;
   var selectedValue; // = document.form1.productos.options[selectedItem].value;
   var i;
   var isNew = true;
   //aderezos
   arrText = new Array();
   arrValue = new Array();
  var count = 0;
   for (i = 0; i < prodLength; i++) {
     if (document.all.usuarios_todos.options[i].selected) {
       arrValue[count] = document.all.usuarios_todos.options[i].value;
       arrText[count] = document.all.usuarios_todos.options[i].text;
       count++;
      }
     //count++;
   }

   //fin de aderezos
   for(j = 0; j < count; j++){
   isNew = true;
   	boxLength = document.all.usuarios_proyectos.length;
   	selectedText=arrText[j];
   	selectedValue=arrValue[j];
   if (boxLength != 0) {
      for (i = 0; i < boxLength; i++) {
       thisitem = document.all.usuarios_proyectos.options[i].text;
       if (thisitem == selectedText) {
         isNew = false;
      }
     }
   }
   if (isNew) {
   	 newoption = new Option(selectedText, selectedValue, false, false);
     document.all.usuarios_proyectos.options[boxLength] = newoption;
     //document.form1.compatibles.options[boxLength].selected=true;

   }
   document.all.usuarios_todos.selectedIndex=-1;
   }
}//Funcion moveOver

function removeMe() {
   var boxLength = document.all.usuarios_proyectos.length;
   arrSelected = new Array();
   var count = 0;
   for (i = 0; i < boxLength; i++) {
     if (document.all.usuarios_proyectos.options[i].selected) {
       arrSelected[count] = document.all.usuarios_proyectos.options[i].value;
     }
     count++;
   }
   var x;
   for (i = 0; i < boxLength; i++) {
     for (x = 0; x < arrSelected.length; x++) {
       if (document.all.usuarios_proyectos.options[i].value == arrSelected[x]) {
       	   document.all.usuarios_proyectos.options[i] = null;
       }
     }
     boxLength = document.all.usuarios_proyectos.length;
   }
}//Funcion removeMe
///////////////////////////////////////////////////////////

///armo un arreglo que luego asigno a un hidden en el que paso todos los usuarios del proyecto
function val_text()
{var a=new Array();
    var largo=document.all.usuarios_proyectos.length;
    var i=0;
    for(i;i<largo;i++)
    {a[i]=document.all.usuarios_proyectos.options[i].value;
    }
	document.all.usuarios_proyectos_Values.value=a;
}

function control_borrar()
{var control,evaluo,chequeo;
 control=1;
 chequeo=0;
 while (control<=cantidad_archivos)
       {evaluo=eval("document.all.eliminar_"+control);
        if (evaluo.checked) chequeo=1;
        control++;
       }
 if (chequeo==1)
    {if(confirm('ADVERTENCIA: Se van a eliminar los archivos seleccionados')) return true;
     else return false;
    }

 else {alert("Debe seleccionar al menos un archivo para elminar"); return false;}
}
///////////////////////////////////////////////
</script>

<form name="cargar_proyectos" action="cargar_proyectos.php" method="post">
<?
 if ($msg!=" ")
    {
 ?>
  <table align="center" width="95%">
   <tr>
    <td align="center">
     <font color="Red" size="3"><b><? echo $msg; ?></b></font>
    </td>
   </tr>
  </table>
 <?
    }
?>
<input type="hidden" name="usuarios_proyectos_Values" value="">
<br>
<table width="80%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor=<?=$bgcolor_out?> class="bordes">
 <tr id="mo">
  <td colspan="3" align="center"><font size="3"><? if ($nro_proyecto=="Nuevo") echo"<b>Proyecto &nbsp;$nro_proyecto</b>"; else echo "<b>Proyecto N°:&nbsp;$nro_proyecto</b>";?></font></td>
  <input name="nro_proyecto" type="hidden" value="<?=$nro_proyecto?>">
 </tr>
 <tr>
  <td colspan="3"><b>Titulo:</b>&nbsp;<input name="titulo" value="<?=$titulo?>" size="80"></td>
 </tr>
 <tr>
  <td align="center"><b>Fecha Inico:</b>&nbsp;<input name="fecha_inicio" type="text" id="fecha_inicio" value="<?=fecha($fecha_inicio)?>" size="10" readonly>&nbsp;<?=link_calendario("fecha_inicio");?></td>
  <td align="center"><b>Fecha Fin:</b>&nbsp;<input name="fecha_fin" type="text" id="fecha_fin" value="<?=fecha($fecha_fin)?>" size="10" readonly>&nbsp;<?=link_calendario("fecha_fin");?></td>
  <?if ($nro_proyecto=="Nuevo")
  { $sql="select nombre,apellido
          from sistema.usuarios
          where id_usuario=$id_user";
     $selec_administrador=sql($sql) or fin_pagina();
  ?>
  <td align="center"><b>Administrador:&nbsp;</b><input readonly name="administrador" value="<?=$selec_administrador->fields['apellido']?>, <?=$selec_administrador->fields['nombre']?>">
  <?}
   else {
   	     $sql="select nombre,apellido,id_usuario
   	           from tareas_divisionsoft.proyectos
   	           join sistema.usuarios using(id_usuario)
   	           where id_proyecto=$nro_proyecto";
   	     $selec_administrador=sql($sql) or fin_pagina();
   	?>
   	<td align="center"><b>Administrador:&nbsp;</b><input readonly name="administrador" value="<?=$selec_administrador->fields['apellido']?>, <?=$selec_administrador->fields['nombre']?>">
   	<?
   }
   ?>
  </td>
 </tr>
 <tr>
  <td align="right"><b>Resumen:</b></td>
  <td colspan="2" align="center"><textarea name="resumen" cols="80" rows="3"><?=$resumen?></textarea></td>
 </tr>
</table>
<br>
<?
if ($nro_proyecto=="Nuevo" || $id_user==$selec_administrador->fields['id_usuario'])
{
?>
<table width="80%" cellspacing=0 border="1" bordercolor=#E0E0E0 align="center" bgcolor=<?=$bgcolor_out?> class="bordes">
<tr id="mo">
 <td align="center" width="3%">
   <img id="imagen_4" src="<?=$img_cont?>" border=0 title="Mostrar Usuarios" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.usuarios,4);" >
  </td>
 <td colspan="2" align="center"><font size="3"><b>Usuarios Participantes</b></font></td>
</tr>
<tr>
<td colspan="3">
<table id="usuarios" cellspacing="0" width="80%" style="display:none;border:thin groove" align="center" rules="none">
<?
 $sql="select id_usuario,nombre,apellido
       from sistema.usuarios
       left join permisos.phpss_account on(usuarios.login=phpss_account.username)
       where phpss_account.active='true'
       order by apellido asc ";
 $resul_usuarios=sql($sql) or fin_pagina();
?>
<tr>
 <td align="center" width="45%">
  <select name="usuarios_todos" size="10" style="width:100%" multiple>
   <?
    while (!$resul_usuarios->EOF)
          {
   ?>
           <option value="<?=$resul_usuarios->fields['id_usuario']?>"><?=$resul_usuarios->fields['apellido']?>,&nbsp;<?=$resul_usuarios->fields['nombre']?></option>
   <?
           $resul_usuarios->MoveNext();
          }
   ?>
  </select>
 </td>
 <td align="center" width="10%">
  <table>
   <tr><td align="center"><input name="agrega" type="button" value=">>" onclick="moveOver();"></td></tr>
   <tr><td align="center"><input name="quita" type="button"  value="<<" onclick="removeMe();"></td></tr>
  </table>
 </td>
 <?
 if ($nro_proyecto!="Nuevo")
    {
     $sql="select usuarios_proyectos.id_usuario,usuarios.nombre,usuarios.apellido
           from tareas_divisionsoft.usuarios_proyectos
           join sistema.usuarios using (id_usuario) where id_proyecto=$nro_proyecto order by apellido asc";
     $resul_usuarios_proc=sql($sql) or fin_pagina();
?>
 <td align="center" width="45%">
  <select name="usuarios_proyectos" size="10" style="width:100%" multiple>
  <?
    while (!$resul_usuarios_proc->EOF)
          {
   ?>
           <option value="<?=$resul_usuarios_proc->fields['id_usuario']?>"><?=$resul_usuarios_proc->fields['apellido']?>,&nbsp;<?=$resul_usuarios_proc->fields['nombre']?></option>
   <?
           $resul_usuarios_proc->MoveNext();
          }
   ?>
  </select>
     <?}
      else{
      ?>
      <td align="center" width="45%">
      <select name="usuarios_proyectos" size="10" style="width:100%" multiple>
      </select>
      <?
      }
      ?>
 </td>
</tr>
</table>
</td>
</tr>
</table>
<br>
<?
}
?>

<table width="80%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor=<?=$bgcolor_out?> class="bordes">
 <tr id="mo">
  <td colspan="2" align="center"><font size="3">Seguimiento de Proyecto</font></td>
 </tr>
 <?if ($nro_proyecto!="Nuevo")
      {$sql="select * from tareas_divisionsoft.log_proyectos where id_proyecto=$nro_proyecto";
       $result_log_proyectos=sql($sql) or fin_pagina();
       while (!$result_log_proyectos->EOF)
             {
             ?>
             <tr>
              <td align="center" width="25%">
               <table>
                <tr><td><b>Usuario:&nbsp;<?=$result_log_proyectos->fields['usuario']?></b></td></tr>
                <tr><td><b>Fecha:&nbsp;<?=fecha($result_log_proyectos->fields['fecha'])?></b></td></tr>
               </table>
              </td>
              <td align="center" width="75%"><textarea name="comentario" cols="90" rows="5" readonly><?=$result_log_proyectos->fields['comentario']?></textarea></td>
             </tr>
            <?
             $result_log_proyectos->MoveNext();
             }
      }
 ?>
 <tr>
  <td align="center" width="25%"><b>Nuevo Comentario</b></td>
  <td align="center" width="75%"><textarea name="nuevo_comentario" cols="90" rows="5"></textarea></td>
 </tr>
<?
 if ($nro_proyecto!="Nuevo")
   {
?>
 <tr>
  <td align="center" colspan="2"><input name="guardar_comentario" type="submit" value="Guardar Comentario"></td>
 </tr>
<?
   }
?>
</table>
<br>
<?if ($nro_proyecto!="Nuevo")
{
?>
<table width="80%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor=<?=$bgcolor_out?> class="bordes">
 <tr id="mo">
  <td colspan="2" align="center"><b><font size="3">Archivos</font></b></td>
 </tr>
<?

 if ($up=="asc") $up="desc";
 else $up="asc";
 if ($ordenar==1) $orden="order by nombre_archivo $up";
 if ($ordenar==2) $orden="order by fecha $up";
 if ($ordenar==3) $orden="order by usuario $up";

 $sql="select * from tareas_divisionsoft.archivos_subidos_proyectos where id_proyecto=$nro_proyecto $orden";
 $resul_archivos=sql($sql) or fin_pagina();

   $cantidad_archivos=$resul_archivos->RecordCount();
   $resul_archivos->MoveFirst();
   if ($cantidad_archivos>0)
      {
 ?>
     <tr>
       <td colspan="2">
        <table align="center" width="100%">
         <tr id="ma">
          <td align="center" width="10%"><b>Eliminar</b></td>
          <td align="center" width="50%"><a href='<? echo encode_link("cargar_proyectos.php",array("ordenar"=>"1","up"=>$up,"pagina"=>"nuevo_proyecto","id"=>$nro_proyecto,"cmd"=>"pe"))?>'><b>Nombre Archivo</b></a></td>
          <td align="center" width="20%"><a href='<? echo encode_link("cargar_proyectos.php",array("ordenar"=>"2","up"=>$up,"pagina"=>"nuevo_proyecto","id"=>$nro_proyecto,"cmd"=>"pe"))?>'><b>Fecha de Cargado</b></a></td>
          <td align="center" width="20%"><a href='<? echo encode_link("cargar_proyectos.php",array("ordenar"=>"3","up"=>$up,"pagina"=>"nuevo_proyecto","id"=>$nro_proyecto,"cmd"=>"pe"))?>'><b>Cargado por</b></a></td>
         </tr>
         <?
           $control=0;
               while (!$resul_archivos->EOF)
               {$control++;
        ?>
              <tr>
               <td align="center"><input name="eliminar_<?=$control?>" type="checkbox"></td>
               <input name="id_archivo_<?=$control?>" type="hidden" value="<? echo $resul_archivos->fields["id_archivo_subido"]; ?>">
               <input name="nom_comp_<?=$control?>" type="hidden" value="<? echo $resul_archivos->fields["nombre_archivo_comp"]; ?>">
               <td align="center">
                <a title='<?=$resul_archivos->fields["nombre_archivo_comp"]?> [<?=number_format($resul_archivos->fields["filesize_comp"]/1024)?> Kb]' href='<?=encode_link($_SERVER["PHP_SELF"],array("FileID"=>$resul_archivos->fields["id_archivo_subido"],"download"=>1,"comp"=>1))?>'>
	            <img align=middle src=<?=$html_root?>/imagenes/zip.gif border=0></A>
	            <a title = 'Abrir archivo' href='<?=encode_link($_SERVER["PHP_SELF"],array("FileID"=>$resul_archivos->fields["id_archivo_subido"],"download"=>1,"comp"=>0))?>'><?=$resul_archivos->fields["nombre_archivo"]?>
	            <? echo $resul_archivos->fields["name"]." (".number_format(($resul_archivos->fields["filesize"]/1024),"2",".","")."Kb)"?></a>
	           </td>
	           <td align="center">
	            <b><?=fecha($resul_archivos->fields["fecha"])?></b>
	           </td>
	           <td align="center">
	            <b><?=$resul_archivos->fields["usuario"]?></b>
	           </td>
              </tr>
        <?
                $resul_archivos->MoveNext();
               }
         ?>
        <script>cantidad_archivos=<? echo $control; ?></script>
        <input name="cant_archivos" value="<? echo $control; ?>" type="hidden">
        </table>
       </td>
      </tr>

      <?
}
 ?>
 <tr>
  <td align="right">
   <?$link=encode_link("subir_archivo_proyectos.php",array("nro_proyecto"=>$nro_proyecto));
     $window=new JsWindow($link,"_blank",800,600);
     $window->center=true;
     $window->maximized=true;
   ?>
   <input type="button" name="subir_archivo" value="Agregar" onclick="<?$window->toBrowser();?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  </td>
  <td align="left">
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="borrar_archivo" type="submit" value="Borrar" onclick="return control_borrar();">
  </td>
 </tr>
<table>
<?
}
?>
<br>
<table align="center" >
 <tr>
  <td><input name="guardar" type="submit" value="Guardar" onclick="val_text(); return control_datos();"></td>
  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
  <?
   if ($nro_proyecto!="Nuevo" && $id_user==$selec_administrador->fields['id_usuario'])
      {if ($estado==1)
          {
      ?>
       <td><input type="submit" name="pasar" value="Pasar a Historial"></td>
       <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
       <?
          }
       else
          {
          ?>
           <td><input type="submit" name="pasar" value="Pasar a Pendiente"></td>
           <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <?
          }
      }
  ?>
  <?$link1=encode_link("lista_proyectos.php",array()); ?>
  <td><input name="volver" type="button" value="Volver" Onclick="location.href='<?=$link1?>'"></td>
 </tr>
</table>
</form>
<?
}//si esta permitido a ver el proyecto
else //no esta permitido a ver el proyecto
{
?>
<form name="cargar_proyectos" action="cargar_proyectos.php" method="post">
 <table align="center">
  <tr><td>&nbsp;</td></tr>
  <tr><td>&nbsp;</td></tr>
  <tr><td>&nbsp;</td></tr>
  <tr><td>&nbsp;</td></tr>
  <tr>
   <td height="100%" valign="middle">
    <b><font size="6" color="Red">Usted no tiene Permiso para ver este Proyecto</font></b>
   </td>
  </tr>
  <?$sql="select nombre,apellido
          from tareas_divisionsoft.proyectos
          join sistema.usuarios using (id_usuario)
          where id_proyecto=$nro_proyecto";
    $resul_dat=sql($sql) or fin_pagina();
  ?>
  <tr>
   <td>
    <b><font size="3">Proyecto:&nbsp;</font><font size="4" color="Red"><?=$nro_proyecto?></font></b>
   </td>
  </tr>
  <tr>
   <td>
    <b><font size="3">Administrador de Proyecto:&nbsp;</font><font color="Red" size="4"><?=$resul_dat->fields['apellido']?>,&nbsp;<?=$resul_dat->fields['nombre']?></font></b>
    </td>
  </tr>
  <tr align="center">
   <?$link1=encode_link("lista_proyectos.php",array()); ?>
    <td align="center"><input name="volver" type="button" value="Volver" Onclick="location.href='<?=$link1?>'"></td>
  </tr>
 </table>
</form>
<?
}
fin_pagina();
?>
