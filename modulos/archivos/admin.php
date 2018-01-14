<?php

require_once("../../config.php");

if ($_POST['Sincronizar_Directorios']=='Sincronizar Directorios'){
	$sql="select a.id, a. apellido, a.id_directorios_archivos as destino, directorios_archivos.id_directorios_archivos as origen from
		(select id, creadopor,apellido ,id_directorios_archivos
		from general.subir_archivos
		join sistema.usuarios on creadopor=login) as a
		join general.directorios_archivos on a.apellido=directorios_archivos.nombre_nodo";
	$result_con=sql($sql,'no se puede ejecutar consulta de sincronizacion') or fin_pagina();
	
	while (!$result_con->EOF){
		$id=$result_con->fields['id'];
		$origen=$result_con->fields['origen'];
		$sql="update general.subir_archivos set id_directorios_archivos='$origen' where id=$id";
		sql($sql,'no se puede actualizar')or fin_pagina();
		
		$result_con->MoveNext();		
	}
	
}
echo $html_header;
?>

<link rel="STYLESHEET" type="text/css" href="<?=$html_root?>/lib/dhtmlXTree.css">
<script  src="<?=$html_root?>/lib/dhtmlXCommon.js"></script>
<script  src="<?=$html_root?>/lib/dhtmlXTree.js"></script>		

<script>
var img_ext='<?=$img_ext='../../imagenes/rigth2.gif' ?>';//imagen extendido
var img_cont='<?=$img_cont='../../imagenes/down2.gif' ?>';//imagen contraido
function muestra_tabla(obj_tabla,nro){
 oimg=eval("document.all.imagen_"+nro);//objeto tipo IMG
 if (obj_tabla.style.display=='none'){
 		obj_tabla.style.display='inline';
    oimg.show=0;
    oimg.src=img_ext;
    tree.findItem('Todos',0,1);
 }
 else{
 	obj_tabla.style.display='none';
    oimg.show=1;
		oimg.src=img_cont;
		document.frames['frame_archivos'].archivos_lista.id_directorios_archivos.value='todos';
	 	document.frames['frame_archivos'].archivos_lista.submit();		
 }
}
</script>
		
<form name='admin' action='admin.php' method='POST'>	
<table class='bordes' width=99% cellspacing=2 height="522px">
<tr>

<?if ($_ses_user['login']=='fer'){?>
<input type="submit" name="Sincronizar_Directorios" value="Sincronizar Directorios">
<?}?>

<td valign="top" class='bordes'>
	<table height="100%">
		<tr>
			<td id="mo" valign="top">
	 			<img id="imagen_1" src="<?=$img_ext?>" border=0 title="Ocultar Directorios" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.directorios,1);">
	 		</td>
		</tr>
	</table>
</td>

<td valign="top" width="25%" id="directorios">
	<table class='bordes' width=100% cellspacing=2 >
	 <tr>
	 	<td id="mo">
	 		Directorios
	 	</td>
	 </tr>
	 <tr>
	 
	 <tr>
	 		<td colspan="2">
	 			<center>
	 			<?$link=encode_link('archivos_mod_estructura.php',array())?>
	 			<input type="button" name="modificar_estructura" value="Modificar Estructura" onclick="window.location='<?=$link?>'">
	 			</center>
	 			<hr>
	 			<div id="treeboxbox_tree" style="width:240;height:450"></div> 
	 			<script> 
	 				function tonclick(id){	 							
	 					frames.frame_archivos.document.getElementById('archivos_lista').id_directorios_archivos.value=id;
	 					frames.frame_archivos.document.getElementById('archivos_lista').submit();
	 				}
					function tondblclick(id){
						//alert("Item "+tree.getItemText(id)+" was doubleclicked");
					}			
					function tondrag(id,id2){
						return true;
					}
					function tonopen(id,mode){
						return true;
					}
					function toncheck(id,state){
						//alert("Item "+tree.getItemText(id)+" was " +((state)?"checked":"unchecked"));
					}
			
					tree=new dhtmlXTreeObject("treeboxbox_tree","100%","100%",0);
					tree.setImagePath("../../imagenes/tree/");
					tree.enableCheckBoxes(0);
					tree.enableDragAndDrop(0);
					tree.setOnOpenHandler(tonopen);
					tree.setOnClickHandler(tonclick);
					tree.setOnCheckHandler(toncheck);
					tree.setOnDblClickHandler(tondblclick);
					tree.setDragHandler(tondrag);
					
					tree.loadXML("tree.xml");
												
				</script>	 			
	 		</td>
	 </tr>
	</table>
</td>
<td valign="top" width="100%" class="bordes">
		<?$archivo_frame=$html_root."/modulos/archivos/archivos_lista.php"?>
		<iframe name="frame_archivos" width="100%" height="100%" allowTransparency=true marginwidth=0 marginheight=0 frameborder=0
		id="frame_archivos" align='center' src='./archivos_lista.php'></iframe>	
</td>
</tr>
</table>
</form>
</body>
</html>
<?//fin_pagina();?>