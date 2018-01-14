<?

require_once("../../config.php");

if (isset($_POST['id_directorios_archivos']))
	$id_directorios_archivos=$_POST['id_directorios_archivos'];
else
	$id_directorios_archivos='1';
	
echo $html_header;
$cmd=$_POST["cmd"];
if ($cmd=="subir") {
    $acceso=$_POST["acceso"];
    $acc=$_POST["acc"];
    $comentario=$_POST["comentario"];
    $tamanio=$_FILES["archivo"]["size"];
    //print_r($_FILES);
    if ($id_directorios_archivos=='todos')
         $error.="'Todos' NO es un Directorio, NO puede Seleccionarlo para Subir un Archivo.<br>";
    if (!$_FILES["archivo"])
         $error.="Debe seleccionar un archivo.<br>";
    if ($_FILES["archivo"]["error"])
         $error.="El archivo es muy grande.<br>";
    if ($acceso=="Algunos") {
        $acceso="|";
        reset($acc);
        while (list($key,$cont)=each($acc))
               $acceso.="$cont|";
    }
    if ($acceso=="yo")
        $acceso="";
    $db->begintrans();
    $fecha=date("Y/m/d");
    if (!$error) {
         $sql="INSERT INTO subir_archivos
              (nombre,comentario,creadopor,fecha,size,acceso,id_directorios_archivos) Values
              ('".$_FILES["archivo"]["name"]."','$comentario','".$_ses_user['login']."','$fecha','$tamanio','$acceso','$id_directorios_archivos')";
         $db->execute($sql) or $error.=$db->errormsg()."<br>";
         mkdirs(UPLOADS_DIR."/archivos");
         if (is_file(UPLOADS_DIR."/archivos/".$_FILES["archivo"]["name"]))
             $error="El Archivo ya existe.";
         if (!$error)
              if (!copy($_FILES["archivo"]["tmp_name"],UPLOADS_DIR."/archivos/".$_FILES["archivo"]["name"]))
                   $error.="No se pudo Subir el archivo";
    }
    if ($error) {
        $db->rollbacktrans();
        Error($error);
    }
    else {
          aviso("El archivo se subio correctamente.");
          $db->committrans();
    }
}
?>
<br>

<link rel="STYLESHEET" type="text/css" href="<?=$html_root?>/lib/dhtmlXTree.css">
<script  src="<?=$html_root?>/lib/dhtmlXCommon.js"></script>
<script  src="<?=$html_root?>/lib/dhtmlXTree.js"></script>	
	
<form name='archivos_nuevo' action='archivos_nuevo.php' method="POST" enctype='multipart/form-data'>
<input type=hidden name=cmd value=subir>
<input type=hidden name=id_directorios_archivos value='<?=$id_directorios_archivos?>'>
<table align=center border=1 cellspacing=0 cellpadding=3 width="50%" bgcolor=<? echo $bgcolor2; ?>>

<tr>	
	<td id="mo" align="center" colspan="3">	
		Subir archivos
	</td>
</tr>
<tr>
   <td colspan=3>
	  (Tamaño maximo de archivo es: <? echo sprintf("%01.2lf",get_cfg_var("upload_max_filesize")/1024/1000); ?> MB)
   </td>
</tr>
<tr>
	 <td>
	  Nombre del archivo y localización:
   </td>
   <td colspan=2>
	  <input type=file name=archivo style="width=350px">
	  
   <table class='bordes' style="width=450px">   	
		<tr>
	 		<td id="mo">
	 			Directorios
	 		</td>
	 	</tr>
	 <tr>
	 <tr>
	 		<td>
	 			<font size="1" color="Red"><b>*Recuerde Siempre Seleccionar un Directorio, si NO Selecciona se Guardara en el Raiz</b></font>
	 		</td>
	 	</tr>
	 <tr>
	 		<td>
	 			<div id="treeboxbox_tree" style="width:480;height:200" class="bordes"></div> 
	 			<script> 
	 				function tonclick(id){
	 					document.all.id_directorios_archivos.value=id;
	 					//alert (document.all.id_directorios_archivos.value);
	 					//document.all.archivos_lista.submit();	 			
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
</tr>

<tr>
   <td width=100>
	  Acceso:<br>
(selección múltiple con la tecla 'Ctrl')
   </td>
   <td>
      <input type=radio name=acceso value=yo checked>Yo&nbsp;
      <input type=radio name=acceso value=Todos>Todos
   </td>
   <td rowspan=2>
      <input type=radio name=acceso value=Algunos>Algunos<br>
      <select name=acc[] size=6 multiple>
<?
$sql="select phpss_account.username,usuarios.nombre,usuarios.apellido from phpss_account inner join usuarios on phpss_account.username=usuarios.login where phpss_account.active='true'";
$rs=$db->Execute($sql) or die($db->errormsg());
while ($fila=$rs->fetchrow()) {
       echo "<option value='".$fila["username"]."'>".$fila["nombre"]."&nbsp;".$fila["apellido"]."</option>\n";
}
?>
   </td>
</tr>
<tr>
   <td colspan=2>
   Comentario:<br>
   <textarea name=comentario rows=4 cols=50></textarea>
   </td>
</tr>
<tr>
   <td align=center colspan=3 bgcolor="<?=$bgcolor_out?>">      
      <input type=submit name=enviar value=Enviar>
      <input type="button" name=volver value=Volver onClick='window.location="admin.php"'>
   </td>
</tr>
</table>
</form>
<?fin_pagina();?>