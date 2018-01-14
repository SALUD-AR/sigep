<?

require_once("../../config.php");
require_once("../../lib/funciones_arbol_xml.php");

if ($_POST['operacion']=='nuevo'){
	//asigna variables
	$id_directorios_archivos=$_POST['id_directorios_archivos'];
	$nombre_nodo=$_POST['nombre_nodo'];
	$id_nodo_padre=$_POST['id_nodo_padre'];
	$nombre_nodo_padre=$_POST['nombre_nodo_padre'];
	$path=$_POST['path'];
	//crea el objeto
	$XMLtree=new Tree("./tree.xml");
	//llama a metodo de la clase
	$XMLtree->AgregarItem($id_nodo_padre,$id_directorios_archivos,$nombre_nodo);
	//guarda en el archivo
	$XMLtree->save("./tree.xml");
	//inserto en la base de datos de directorios
	$sql="insert into general.directorios_archivos
				(id_directorios_archivos,nombre_nodo,id_nodo_padre,nombre_nodo_padre,path)values
				('$id_directorios_archivos','$nombre_nodo','$id_nodo_padre','$nombre_nodo_padre','$path')";
	sql($sql,"No se Puede insertar en la base de datos")or fin_pagina();
	echo "<br><center><font size=4 color=red><b>El Directorio se Creo Exitosamente</b></font></center><br>\n";
}

if ($_POST['operacion']=='elim'){
	//asigno el id que quiero borrar
	$id_nodo_borrar=$_POST['id_nodo_borrar'];
	//verifico que no exitan archivos relacionados
	$sql="select id from general.subir_archivos where id_directorios_archivos='$id_nodo_borrar'";
	$result_elim=sql($sql,"No se puede traer los archivos para eliminar directorio")or fin_pagina();
	if ($result_elim->RecordCount()==0){
		//crea el objeto
		$XMLtree=new Tree("./tree.xml");
		//llama a metodo de la clase
		$XMLtree->BorrarItem($id_nodo_borrar);
		//guarda en el archivo
		$XMLtree->save("./tree.xml");
		//elimino de la base de datos de directorios
		$sql="delete from general.directorios_archivos where id_directorios_archivos='$id_nodo_borrar'";
		sql($sql,"No se Puede insertar en la base de datos")or fin_pagina();
		echo "<br><center><font size=4 color=red><b>El Directorio se Elimino Exitosamente</b></font></center><br>\n";
	}
	else{
		echo "<br><center><font size=4 color=red><b>El Directorio NO se Puede Eliminar hay Archivos Asociados</b></font></center><br>\n";
	}
}

if ($_POST['operacion']=='mod'){
	//asigno el id que quiero modificar
	$id_nodo_mod=$_POST['id_nodo_mod'];
	//asigno el nombre que quiero modificar
	$nombre_mod_dir=$_POST['mod_dir'];
	//le asigno el nuevo path
	$path=$_POST['path'];
	//crea el objeto
	$XMLtree=new Tree("./tree.xml");
	//llama a metodo de la clase
	$XMLtree->ModificarItem($id_nodo_mod,$nombre_mod_dir);
	//guarda en el archivo
	$XMLtree->save("./tree.xml");
	//actualizo de la base de datos de directorios
	$sql="update general.directorios_archivos set nombre_nodo='$nombre_mod_dir', path='$path'
				where id_directorios_archivos='$id_nodo_mod'";
	sql($sql,"No se Puede actualizar en la base de datos")or fin_pagina();
	echo "<br><center><font size=4 color=red><b>El Directorio se Modifico Exitosamente</b></font></center><br>\n";
}

echo $html_header;?>

<link rel="STYLESHEET" type="text/css" href="<?=$html_root?>/lib/dhtmlXTree.css">
<script  src="<?=$html_root?>/lib/dhtmlXCommon.js"></script>
<script  src="<?=$html_root?>/lib/dhtmlXTree.js"></script>
<script  src="<?=$html_root?>/lib/dhtmlXTree_xw.js"></script>

<form name='archivos_mod_estructura' action='archivos_mod_estructura.php' method='POST'>
<br>
<input type=hidden name=id_directorios_archivos value='<?=$id_directorios_archivos?>'>
<input type=hidden name=nombre_nodo value='<?=$nombre_nodo?>'>
<input type=hidden name=id_nodo_padre value='<?=$id_nodo_padre?>'>
<input type=hidden name=nombre_nodo_padre value='<?=$nombre_nodo_padre?>'>
<input type=hidden name=path value='<?=$path?>'>
<input type=hidden name=id_nodo_borrar value='<?=$id_nodo_borrar?>'>
<input type=hidden name=id_nodo_mod value='<?=$id_nodo_mod?>'>
<input type=hidden name='operacion' value=''>
	<table class='bordes' width=70% cellspacing=2 align="center">
			<tr>
		 		<td id="mo" width="40%">
		 			Directorios
		 		</td>
		 		<td id="mo" width="60%">
		 			Operaciones
		 		</td>
		 </tr>

		 <tr>
		 		<td>
		 			<div id="treeboxbox_tree" style="width:300;height:250" class="bordes"></div>
		 			<script>
		 				function tonclick(id){
		 					//document.all.id_directorios_archivos.value=id;
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

						function nuevo_item(){
							//debe seleccionar un directorio
							if (tree.getSelectedItemId()==''){
								alert ('DEBE seleccionar un Directorio !!');
								return;
							}
							//pregunta por directorio vacio y no inserta
							if (document.getElementById('ed1').value==''){
								alert ('No Puede Insertar un Directorio VACIO !!');
								return;
							}
							//no deja insertar en todos ni en el mismo nivel que el raiz
							if ((tree.getSelectedItemId()=='todos') || (tree.getSelectedItemId()==0) ){
								alert ('No se Puede Crear Directorio en TODOS !!');
								return;
							}
							//genera una variable que va a ser el id del nodo
							var d=new Date();
							//inserta el nodo en el arbol
							tree.insertNewItem(tree.getSelectedItemId(),d.valueOf(),document.getElementById('ed1').value);
							//le doy al hidden el nuevo id
							document.all.id_directorios_archivos.value=d.valueOf();
							//le doy el hidden el nombre del nodo
							document.all.nombre_nodo.value=document.getElementById('ed1').value;
							//le doy el hidden el id del nodo padre
							document.all.id_nodo_padre.value=tree.getSelectedItemId();
							//le doy el hidden el nombre del nodo padre
							document.all.nombre_nodo_padre.value=tree.getItemText(tree.getSelectedItemId());
							//obtiene el path del nodo
							var id_nodo=tree.getSelectedItemId();
							var path="/" + document.getElementById('ed1').value;
							while(id_nodo!=0){
								path="/"+tree.getItemText(id_nodo)+path;
								id_nodo=tree.getParentId(id_nodo);
							}
							//le asigna el path del nodo al hidden
							document.all.path.value=path;
							//seteo el hidden para ver la operacion a realizar
							document.all.operacion.value='nuevo';
							//hago el submit para que grabe en la base de datos
							document.all.archivos_mod_estructura.submit();
						}

						function borrar_item(){
							//debe seleccionar un directorio
							if (tree.getSelectedItemId()==''){
								alert ('DEBE seleccionar un Directorio !!');
								return;
							}
							//no puede borrar el directorio raiz o el todos
							if ((tree.getSelectedItemId()=='todos') || (tree.getSelectedItemId()==1) ){
									alert ('No se Puede Borrar el Directorio RAIZ o TODOS');
									return;
							}
							//verifica que el directorio no tenga hijos
							if (tree.getAllSubItems(tree.getSelectedItemId())!=''){
								alert ('No se Puede Borrar HAY DIRECTORIOS HIJOS !!');
								return
							}
							if (confirm('Esta Seguro que Desea Eliminar el Directorio '+ tree.getItemText(tree.getSelectedItemId()) +' ?')){
								//le doy al hidden el nodo a borrar
								document.all.id_nodo_borrar.value=tree.getSelectedItemId();
								//seteo el hidden para ver la operacion a realizar
								document.all.operacion.value='elim';
								//hago el submit para que grabe en la base de datos
								document.all.archivos_mod_estructura.submit();
							}
						}

						function modificar_item(){
							//pregunta por directorio vacio y no inserta
							if (document.getElementById('ed2').value==''){
								alert ('Ingrese Nuevo Nombre de Directorio !!');
								return;
							}
							//debe seleccionar un directorio
							if (tree.getSelectedItemId()==''){
								alert ('DEBE seleccionar un Directorio !!');
								return;
							}
							//no puede modificar el directorio raiz o el todos
							if ((tree.getSelectedItemId()=='todos') || (tree.getSelectedItemId()==1) ){
									alert ('No se Puede Modificar el Directorio RAIZ o TODOS');
									return;
							}
							if (confirm('Esta Seguro que Desea Modificar el Directorio '+ tree.getItemText(tree.getSelectedItemId()) +' ?')){
								//obtiene el path del nodo
								var id_nodo=tree.getParentId(tree.getSelectedItemId());
								var path="/" + document.getElementById('ed2').value;
								while(id_nodo!=0){
									path="/"+tree.getItemText(id_nodo)+path;
									id_nodo=tree.getParentId(id_nodo);
								}
								//le asigna el path del nodo al hidden
								document.all.path.value=path;
								//le doy al hidden el nodo a modificar
								document.all.id_nodo_mod.value=tree.getSelectedItemId();
								//seteo el hidden para ver la operacion a realizar
								document.all.operacion.value='mod';
								//hago el submit para que grabe en la base de datos
								document.all.archivos_mod_estructura.submit();
							}

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
		 		<td valign="top" class="bordes">
		 			<table width="100%">
		 				<tr>
		 					<td id="ma" align="center" class="bordes">
		 						Nuevo Directorio
		 					</td>
		 				</tr>
		 				<tr>
		 					<td align="left">
								<input type="text" value="" id="ed1" name="ed1" style="width=200px">&nbsp;
								<input type="button" name="nuevo_directorio" value="Nuevo Directorio"
									onclick="if (confirm('Esta Seguro que Desea Agregar Directorio?'))nuevo_item();">
							</td>
						</tr>
						<tr><td><br></td></tr>
						<tr>
		 					<td id="ma" align="center" class="bordes">
		 						Modificar Directorio
		 					</td>
		 				</tr>
						<tr>
		 					<td align="left">
		 						<input type="text" value="" id="ed2" name="mod_dir" style="width=200px">&nbsp;
								<input type="button" name="modificar_directorio" value="Modificar Directorio"
									onclick="modificar_item();">
							</td>
						</tr>
						<tr><td><br></td></tr>
						<tr>
		 					<td id="ma" align="center" class="bordes">
		 						Eliminar Directorio
		 					</td>
		 				</tr>
						<tr>
		 					<td align="center">
								<input type="button" name="elimina_directorio" value="Elimina Directorio"
									onclick="borrar_item();">
							</td>
						</tr>
		 			</table>
				</td>
		 	</tr>

			<tr>
   			<td align=center colspan="2" class="bordes" bgcolor="<?=$bgcolor_out?>">
      		<input type="button" name=volver value=Volver style="width=250px"
						onClick='window.location="admin.php"'>
   			</td>
			</tr>
	</table>
</form>
</body>
</html>
<?fin_pagina();?>