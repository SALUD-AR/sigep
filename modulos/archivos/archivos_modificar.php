<?


require_once("../../config.php");

if (isset($_POST['id_directorios_archivos']))
	$id_directorios_archivos=$_POST['id_directorios_archivos'];
else
	$id_directorios_archivos='1';
	
echo $html_header;
$cmd=$_POST["cmd"];
$id=$parametros["id"] or $id=$_POST["id"];
if ($cmd=="Eliminar") {
    $sql="select nombre from subir_archivos where id=$id";
    $rs=sql($sql,'Error') or fin_pagina();
    if (!unlink(UPLOADS_DIR."/archivos/".$rs->fields["nombre"]))
         $error="No se encontro el archivo";
    $sql="delete from subir_archivos where id=$id";
    sql($sql,'No se puede Eliminar') or fin_pagina();
    if ($error)
        error($error);
    else
        echo "<script>window.location='archivos_lista.php';</script>\n";
}
if ($cmd=="subir") {
    $acceso=$_POST["acceso"];
    $acc=$_POST["acc"];
    $comentario=$_POST["comentario"];
    if ($acceso=="Algunos") {
        $acceso="|";
        reset($acc);
        while (list($key,$cont)=each($acc))
               $acceso.="$cont|";
    }
    if ($acceso=="yo")
        $acceso="";
    if ($id_directorios_archivos=='todos')
         $error.="'Todos' NO es un Directorio, NO puede Seleccionarlo para Modificar un Archivo.<br>";
    if (!$error) {
         $sql="UPDATE subir_archivos set
         			id_directorios_archivos='$id_directorios_archivos',
              comentario = '$comentario',
              acceso='$acceso' where id=$id";
         sql($sql,'no se puede actualizar el archivo') or fin_pagina();
    }
    if ($error) {
        Error($error);
    }
    else {
          aviso("Las modificaciones fueron realizadas con exito.");
    }
}
$sql="Select * from subir_archivos where id=$id";
$rs=sql($sql,'No se puede traer el archivo') or fin_pagina();
$fila=$rs->fetchrow();
?>
<br>

<link rel="STYLESHEET" type="text/css" href="<?=$html_root?>/lib/dhtmlXTree.css">
<script  src="<?=$html_root?>/lib/dhtmlXCommon.js"></script>
<script  src="<?=$html_root?>/lib/dhtmlXTree.js"></script>	
	
<form action='archivos_modificar.php' method=post>
<input type=hidden name=cmd value=subir>
<input type=hidden name=id value='<? echo $id; ?>'>
<input type=hidden name=id_directorios_archivos value='<?=$id_directorios_archivos?>'>

<table align=center border=1 width="50%" cellspacing=0 cellpadding=3 bgcolor=<? echo $bgcolor2; ?>>

<tr>
   <td colspan=3 id="mo">
      Modificar archivos
   </td>
</tr>

<tr>	
	<td colspan="3">
	<table class='bordes'>
		<tr>
	 		<td id="mo" class="bordes">
	 			Seleccionar Directorio
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
	 			<div id="treeboxbox_tree" style="width:485;height:150" class="bordes"></div> 
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
   <td>
      Nombre del archivo y localización:
   </td>
   <td colspan=2>
      <? echo $fila["nombre"]; ?>
   </td>
</tr>
<tr>
   <td width=100>
      Acceso:<br>
(selección múltiple con la tecla 'Ctrl')
   </td>
   <td>
      <?
      if (!$fila["acceso"]) echo "<input type=radio name=acceso value=yo checked>Yo&nbsp;";
      else echo "<input type=radio name=acceso value=yo>Yo&nbsp;";
      if ($fila["acceso"]=="Todos") echo "<input type=radio name=acceso value=Todos checked>Todos";
      else echo "<input type=radio name=acceso value=Todos>Todos";
      ?>
   </td>
   <td rowspan=2>
      <?
      if ($fila["acceso"] and $fila["acceso"]!="Todos") echo "<input type=radio name=acceso value=Algunos checked>Algunos<br>";
      else echo "<input type=radio name=acceso value=Algunos>Algunos<br>";
      echo "<select name=acc[] size=6 multiple>";
			$sql="select username from phpss_account where active='true'";
			$rs=sql($sql,'no se puede traer las cuentas de usuario') or fin_pagina();
			while ($filas=$rs->fetchrow()) {
      	echo "<option value='".$filas["username"]."'";
      	if (strstr($fila["acceso"],$filas["username"])) echo "selected";
       	echo ">".$filas["username"]."</option>\n";
			}?>
   </td>
</tr>
<tr>
   <td colspan=2>
   Comentario:<br>
   <textarea name=comentario rows=4 cols=50><? echo $fila["comentario"]; ?></textarea>
   </td>
</tr>
<tr>
   <td align=center colspan=3 bgcolor="<?=$bgcolor_out?>">
   		<input type=submit name=enviar value=Enviar>
   		<input type=submit name=cmd value=Eliminar>
      <input type="button" name=volver value=Volver onClick='window.location="archivos_lista.php"'>      
   </td>
</tr>
</table>
</form>
<?fin_pagina();?>