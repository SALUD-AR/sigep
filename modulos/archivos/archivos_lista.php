<?php

require_once("../../config.php");
$id=$parametros["id"];
$cmd=$parametros["cmd"];

if (isset($_POST['id_directorios_archivos']))
	$id_directorios_archivos=$_POST['id_directorios_archivos'];
else
	$id_directorios_archivos='todos';	

//print_r($_POST);
// Barra de consulta para enviarle al formulario
if ($cmd=="download") {
    $file=$parametros["file"];
    $size=$parametros["size"];
    Mostrar_Header($file,"application/octet-stream",$size);
    $filefull = UPLOADS_DIR ."/archivos/". $file;
    readfile($filefull);
    exit();
}
if ($cmd=="Eliminar") {
    $sql="select nombre from subir_archivos where id=$id";
    $rs=sql($sql,'no se puede seleccionar el arhivo') or fin_pagina();
    if (!unlink(UPLOADS_DIR."/archivos/".$rs->fields["nombre"]))
         $error="No se encontro el archivo";
    $sql="delete from subir_archivos where id=$id";
    sql($sql,'No se puede Eliminar el Archivo') or fin_pagina();
    if ($error)
        error($error);
    aviso("El archivo se Elimino del Sistema.");
}

variables_form_busqueda("archivos_lista");

$orden = array(
	"default_up" => "0",
	"default" => "2",
	"1" => "subir_archivos.nombre",
	"2" => "fecha",
	"3" => "comentario",
	"4" => "nbre_completo",
	"5" => "size",	
);

$filtro = array(
	"subir_archivos.nombre"         => "Archivos",
	"comentario"       => "Comentario",
	"usuarios.apellido"      => "Subido por",
	"size"           => "Tamaño"
);

$sql_temp = "SELECT subir_archivos.*,usuarios.nombre ||' '|| usuarios.apellido as nbre_completo, directorios_archivos.path as nombre_dir
				FROM general.subir_archivos 
				left join sistema.usuarios on subir_archivos.creadopor=usuarios.login
				left join general.directorios_archivos on subir_archivos.id_directorios_archivos=directorios_archivos.id_directorios_archivos";

$where_temp = "(acceso ilike '%|". $_ses_user['login'] ."|%' OR acceso='Todos' OR creadopor='". $_ses_user['login'] ."') 
								AND subir_archivos.id not in (select id from archivos_casos) 
								AND subir_archivos.comentario <> 'Archivo de Orden de Producción' ";

if ($id_directorios_archivos!='todos')
	$where_temp .="AND subir_archivos.id_directorios_archivos = '$id_directorios_archivos'";

echo $html_header;?>

<link rel="STYLESHEET" type="text/css" href="<?=$html_root?>/lib/dhtmlXTree.css">
<script  src="<?=$html_root?>/lib/dhtmlXCommon.js"></script>
<script  src="<?=$html_root?>/lib/dhtmlXTree.js"></script>		
		
<form name='archivos_lista' id='archivos_lista' action='archivos_lista.php' method='POST'>
	<input type=hidden name=id_directorios_archivos value='<?=$id_directorios_archivos?>'>

<table width=100% cellspacing=2>
<tr>

<td valign="top" width="100%">
	<table class='bordes' width=100% cellspacing=2 align="right">
		<tr>
	 		<td id="mo" colspan="9">
	 			Archivos
	 		</td>
	 	</tr>
		<tr>
			<td colspan=9 align=center>
				<br>
				<?list($sql,$total,$link_pagina,$up2) = form_busqueda($sql_temp,$orden,$filtro,$link_temp,$where_temp,"buscar");?>
				&nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
				<?$rs = sql($sql,'No se puede ejecutar la consulta') or fin_pagina();?>
			</td>				
		</td>
		</tr>	
		<?if ($id_directorios_archivos=='todos') $muestra_path='Todos';
	  else $muestra_path=$rs->fields["nombre_dir"];?>
	 	<tr>
	 		<td id="ma_mg" colspan="9" class="bordes">
	 			Directorio Actual: <font color="Black"><?=$muestra_path?></font>
	 		</td>
	 	</tr>
	 
	 <tr>
	  <td>
	   <tr>
	    <td colspan=3 id=ma align='left'>	    	
        <b>Total:</b> <?=$total?> Archivos.</td>
		<td colspan=4 style='border-left: 0;' align=right id=ma><?=$link_pagina?></td></tr>
        <tr>
         <td align=right id=mo><a id=mo href='<?=encode_link("archivos_lista.php",Array ("sort" => 1,"up" => $up2,"page" => $page_n,"keyword" => $keyword,"filter" => $filter))?>'>Archivo</a></td>
         <td align=right id=mo><a id=mo href='<?=encode_link("archivos_lista.php",Array ("sort" => 2,"up" => $up2,"page" => $page_n,"keyword" => $keyword,"filter" => $filter))?>'>Fecha</a></td>
		 <td align=right id=mo><a id=mo href='<?=encode_link("archivos_lista.php",Array ("sort" => 3,"up" => $up2,"page" => $page_n,"keyword" => $keyword,"filter" => $filter))?>'>Comentario</a></td>
		 <td align=right id=mo><a id=mo href='<?=encode_link("archivos_lista.php",Array ("sort" => 4,"up" => $up2,"page" => $page_n,"keyword" => $keyword,"filter" => $filter))?>'>Subido por</a></td>
		 <td align=right id=mo><a id=mo href='<?=encode_link("archivos_lista.php",Array ("sort" => 5,"up" => $up2,"page" => $page_n,"keyword" => $keyword,"filter" => $filter))?>'>Tamaño</td>
		 <td align=center id=mo>Funciones</td>
		 <?if ($id_directorios_archivos=='todos'){?>
		 <td align=right id=mo>Directorio</td>
		 <?}?>
 	 	</tr>
		<?while (!$rs->EOF) {?>
    	<tr style='font-size: 9pt' bgcolor=<?=$bgcolor_out?>>
    	 <td align=center>
    		<?if (is_file("../../uploads/archivos/".$rs->fields["nombre"]))?>
        		<a href='<?=encode_link("archivos_lista.php",array ("file" =>$rs->fields["nombre"],"size" => $rs->fields["size"],"cmd" => "download"))?>'>
    			<?=$rs->fields["nombre"]?></a>
    	 </td>
    	 <td align=center>&nbsp;<?=Fecha($rs->fields["fecha"])?></td>
    	 <td align=center>&nbsp;<?=$rs->fields["comentario"]?></td>
         <td align=center>&nbsp;<?=$rs->fields["nbre_completo"]?></td>
    	 <?$size=number_format($rs->fields["size"] / 1024)?>
    	 <td align=center>&nbsp;<?=$size?> Kb</td>
    	 <td align=center>
	    	 <?if ($_ses_user['login']==$rs->fields["creadopor"]) {
		        echo "<a href='".encode_link("archivos_modificar.php",array ("id" => $rs->fields["id"]))."'><img src='../../imagenes/modificar.gif' border=0 alt='Haz click para modificar el archivo'></a> ";?>
		        <a href='<?=encode_link("archivos_lista.php",array("id" => $rs->fields["id"],"cmd"=>"Eliminar"))?>' onclick="return confirm('Esta Seguro que Desea Eliminar el Archivo ?')"><img src='../../imagenes/close1.gif' border=0 alt='Haz click para eliminar el archivo'></a>
	    	 <?}
	    	 else echo "&nbsp;";
	    	 ?>
    	 </td>
    	 <?if ($id_directorios_archivos=='todos'){?>
    	 <td>
	    	 <table width="100%">
	    	  <tr>
	    	 	 <td align="left">
	    	 	 	<b>
	    	 	 	<?echo $rs->fields["nombre_dir"];?>
	    	 	 	</b>
	    	 	 </td>	    	 		
	  			</tr>
	  		 </table>
    	 </td>
    	 <?}?>
    	</tr>
    	<?$rs->MoveNext();
		}?>
	  </td>
	 </tr>
	</table>
</td>
</tr>
</table>
</form>
</body>
</html>
<?//fin_pagina();?>