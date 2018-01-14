<?

require_once "../../config.php";
require_once(LIB_DIR."/class.gacz.php");
require_once("./permisos.class.php");
if($_POST['bguardar'])
{
	require("permisos-grp_proc.php");
	$msg="<b>Los permisos han sido guardados</b>";
}

echo $html_header;
echo "<link rel='STYLESHEET' type='text/css' href='$html_root/lib/dhtmlXTree.css'>\n";
echo "<link rel='STYLESHEET' type='text/css' href='$html_root/lib/dhtmlXMenu_xp.css'>\n";
echo "<link rel='STYLESHEET' type='text/css' href='$html_root/lib/dhtmlXMenu.css'>\n";

$arbol=new HTMLArbolPermisos();
$arbol->insertScripts();
$arbol->checkboxes=true;
$arbol->bguardar->set_event("onclick","bguardar_click()");

$usuario = $_GET["usuario"] or $usuario = "";
$win=new JsWindow("lista_usr.php");
$win->locationBar=false;
$win->linkBar=false;
$win->menuBar=false;
$win->toolBar=false;

$q ="select id_grupo,uname||' ('||id_grupo||')' as uname from grupos order by uname";
$r=sql($q) or fin_pagina();
$oSelectGrp=new HtmlOptionList("select_grupos",10,"width:100%; height:100%");
$oSelectGrp->id="select_grupos";
$oSelectGrp->add_event("onchange","changeGroup()");
$oSelectGrp->optionsFromResulset($r,array("text"=>"uname","value"=>"id_grupo"));
$oSelectGrp->setSelected($_GET['grp_id']);//si se paso el id_grupo por GET
if ($oSelectGrp->selectedIndex!=-1)
{
	$arbol->url="./permisos_xml.php?get=grp1&grp_id=".$oSelectGrp->options[$oSelectGrp->selectedIndex]->value;
	$arbol->loadOnInit=true;
}

?>
	<form id="form_guardar" method="post" action="<? echo $_SERVER["PHP_SELF"]; ?>">
	<input type="hidden" name="tree_checked" id="tree_checked" value="">
	<input type="hidden" name="users_checked" id="users_checked" value="">
	<? if ($msg) echo "<center>$msg<center>"; ?>	
	<table id="tabla_contenido" width="100%" height="94%" align="center" border="0" cellpadding="2" cellspacing="0">
	<tr>
			<td align="center" width="40%">			
				<b>Grupos (<span id="groupCount"><?=$oSelectGrp->length ?></span>)</b><br/>
				<div id="grupos" style="height:40%;max-height: 200px; border :1px solid Silver;overflow:auto;">
				<?=$oSelectGrp->toBrowser();?>
				</div>
				<input type="button" name="btn_nuevo_grp" value="Nuevo Grupo" onclick="fnAddGroup();"/>
				<input type="button" name="btn_eliminar_grp" value="Eliminar Grupo" onclick="fnRemoveGroup()"/>
				
				<br/><b>Usuarios que pertenecen al Grupo</b><br/>
				<div id="usuarios" align="left" style="height:48%;max-height: 200px;width:100%;background-color:#f5f5f5;border :1px solid Silver;overflow:auto;" />
			</td>
			<td align="center" style="max-height: 90%" height="90%" width="49%">
				<b>Permisos de Grupo</b><br/>
				<? $arbol->toBrowser(); ?>
			</td>
		</tr>
	</table>
	</form>
	<script>
/****************************************** AJAX Begin ******************************************************/
var groupCount=document.getElementById("groupCount");//<span>
var oSelectGrupos=document.getElementById("select_grupos");//<select>

var req2;//para buscar los usuarios/grupos con el permiso
function doRequest2(url) {
   if (window.XMLHttpRequest) {
       req2 = new XMLHttpRequest();
   } else if (window.ActiveXObject) {
       req2 = new ActiveXObject("Microsoft.XMLHTTP");
   }
   req2.open("GET", url, false);//llamada sincronica, para esperar la respuesta del server
   req2.onreadystatechange = onLoad2;
   req2.send(null);   
}
//Inserta un nuevo grupo y recupera su ID
function onLoad2() {
    // only if req shows "loaded"
    if (req2.readyState == 4) {
        // only if "OK"
        if (req2.status == 200) {
            // ...processing statements go here...
            groupId=req2.responseText;//si !=0 todo ok, sino fallo
        } else {
            alert("There was a problem retrieving the XML data:\n" +req2.statusText);
        }
    }
}

var req3;
function doRequest3(url) {
   if (window.XMLHttpRequest) {
       req3 = new XMLHttpRequest();
   } else if (window.ActiveXObject) {
       req3 = new ActiveXObject("Microsoft.XMLHTTP");
   }
   req3.open("GET", url, true);
   req3.onreadystatechange = onLoad3;
   req3.send(null);   
}
//recupera un arreglo de ids de permisos para el grupo 
function onLoad3() {
    // only if req shows "loaded"
    if (req3.readyState == 4) {
        // only if "OK"
        if (req3.status == 200) {
            // ...processing statements go here...
            setCheckedIds(eval(req3.responseText));
        } else {
            alert("There was a problem retrieving the XML data:\n" +req3.statusText);
        }
    }
}
var req4;
var deletedGroup=0;
function doRequest4(url) {
   if (window.XMLHttpRequest) {
       req4 = new XMLHttpRequest();
   } else if (window.ActiveXObject) {
       req4 = new ActiveXObject("Microsoft.XMLHTTP");
   }
   req4.open("GET", url, false);//llamada sincronica
   req4.onreadystatechange = onLoad4;
   req4.send(null);   
}
//Elimina un grupo de la BD y recupera el codigo de retorno
function onLoad4() {
    // only if req shows "loaded"
    if (req4.readyState == 4) {
        // only if "OK"
        if (req4.status == 200) {
            // ...processing statements go here...
            deletedGroup=req4.responseText;
        } else {
            alert("There was a problem retrieving the data:\n" +req4.statusText);
        }
    }
}
/****************************************** AJAX End ********************************************************/
var first_grequest=true;
var aPermisosGrupo;
function changeGroup() 
{
	if (!first_grequest)
	{
		doRequest3('<?=$html_root?>/modulos/permisos/permisos_xml.php?get=grp&grp_id='+oSelectGrupos.value,onLoad3);
	}
	else
	{
		//hago el primer requerimiento
		//first_grequest=false;
		tree.deleteChildItems("0");
		tree.loadXML('./permisos_xml.php?get=grp1&grp_id='+oSelectGrupos.value);
	}
//	document.forms[0].btn_ver_permisos.disabled=1;
	treeUsers.deleteChildItems("0");
	treeUsers.loadXML('./lista_usr_xml.php?grp_id='+oSelectGrupos.value);
}

/**
 * Array aIds contiene los ids q se deben checkear
*/
function setCheckedIds(aIds)
{
		unchekAll(tree);
		for(var i=0; i < aIds.length;i++)
			tree.setCheck(aIds[i],1);
}
function unchekAll(tree)
{
	aIds2=tree.getAllChecked().split(",");
	for(var i=0; i < aIds2.length;i++)
		tree.setCheck(aIds2[i],0);		
}
var groupId;
function fnAddGroup()
{
	if (groupName=prompt('Ingrese el Nombre del nuevo grupo','')) 
	{
		groupId=0;
		doRequest2('<?=$html_root?>/modulos/permisos/permisos-grp_proc.php?cmd=ins_grp&grp_name='+groupName);
		//Si se pudo insertar en DB
		if (groupId!=0 && groupId!=-1)
		{
			oSelectGrupos.length++;
			oSelectGrupos.options[oSelectGrupos.length-1].text=groupName+" ("+groupId+")";
			oSelectGrupos.options[oSelectGrupos.length-1].value=groupId;
			groupCount.innerText=parseInt(groupCount.innerText)+1;
		}
		else if(groupId==-1)
		{
			alert('El nombre de grupo "'+groupName+'" ya existe');
		}
		else
			alert('El grupo no se pudo crear');
	}
}
function fnRemoveGroup()
{
	if (oSelectGrupos.selectedIndex >= 0)
	{
		var msg="Si elimina un Grupo los usuarios asociados al mismo perderán sus permisos\n\n";
			 msg+="¿Seguro que desea Eliminar el grupo '"+oSelectGrupos.options[oSelectGrupos.selectedIndex].text+"'?";
		if (confirm(msg))
		{
			doRequest4('<?=$html_root?>/modulos/permisos/permisos-grp_proc.php?cmd=del_grp&grp_id='+oSelectGrupos.value);
			if (deletedGroup==1)
			{
				deletedGroup=0;
				oSelectGrupos.options[oSelectGrupos.selectedIndex]=null;	
				unchekAll(tree);
				unchekAll(treeUsers);
				groupCount.innerText=parseInt(groupCount.innerText)-1;
				tree.deleteChildItems("0");
				treeUsers.deleteChildItems("0");
			}
			else
				alert('No se pudo eliminar el grupo');
		}
	}
}
function fnRemoveUser()
{
	var oSelect=document.getElementById("select_usuarios");
	if (oSelect.selectedIndex >= 0  && oSelectGrupos.selectedIndex >=0)
	{
		var msg="Se eliminará el usuario "+oSelect.options[oSelect.selectedIndex].text;
			 msg+=" del grupo "+oSelectGrupos.options[oSelectGrupos.selectedIndex].text;
			 msg+="\n\n¿Seguro que desea eliminar?";
		if (confirm(msg))
			oSelect.options[oSelect.selectedIndex]=null;	
	}
}
function bguardar_click()
{
	document.getElementById('tree_checked').value = tree.getAllChecked();
	document.getElementById('users_checked').value = treeUsers.getAllChecked();
}
		
			var treeUsers=new dhtmlXTreeObject("usuarios","100%","100%",0);
			treeUsers.setImagePath("<?= $html_root?>/imagenes/tree/");
			treeUsers.enableTreeImages(0);
			treeUsers.enableTreeLines("disable");
			treeUsers.enableCheckBoxes(true);
			treeUsers.enableThreeStateCheckboxes(false);
<? 
			if ($oSelectGrp->selectedIndex!=-1)
			 echo "treeUsers.loadXML('./lista_usr_xml.php?grp_id=".$oSelectGrp->options[$oSelectGrp->selectedIndex]->value."');\n";			
?>
			
			function fix_size() {
				//dependiendo del largo del formulario, seteamos el largo del div del formulario
				var largo=parseInt((parent.document.getElementById('frame2'))?parent.document.getElementById('frame2').clientHeight:document.body.clientHeight)-20;
				//document.getElementById('tabla_contenido').style.height=largo+"px";
			}
	</script>
</body>
</html>
