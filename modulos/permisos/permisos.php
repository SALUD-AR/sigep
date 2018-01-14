<?
require_once("./permisos.class.php");

if ($_POST['bguardar']) {
    require("./permisos_proc.php");
    $msg = "<b>Los permisos han sido guardados</b>";
}
echo $html_header;
echo "<link rel='STYLESHEET' type='text/css' href='$html_root/lib/dhtmlXMenu_xp.css'>\n";
echo "<link rel='STYLESHEET' type='text/css' href='$html_root/lib/dhtmlXMenu.css'>\n";
$arbol = new HTMLArbolPermisos();
$arbol->insertScripts();
$arbol->onclickhandler = "fnOnClick";
$arbol->url = "./permisos_xml.php?get=all";
$arbol->loadOnInit = true;
$arbol->bguardar->set_event("onclick", "return GuardarDatos();");

//echo "<script  src='$html_root/lib/dhtmlXMenuBar.js'></script>";
//echo "<script  src='$html_root/lib/dhtmlXMenuBar_cp.js'></script>";
$q = "select max(id_permiso) as id_permiso from permisos ";
$r = sql($q) or fin_pagina();
?>
<form name="form_guardar" id="form_guardar" method="post" action="<?= $_SERVER['SCRIPT_NAME'] ?>">
    <input type="hidden" name="tree_checked" id="tree_checked" value="">
    <input type="hidden" name="changes" id="changes" value="">
    <? if ($msg) echo "<center>$msg<center>"; ?>
    <table id="tabla_contenido" width="100%" height="94%" align="center" border="0" cellpadding="2" cellspacing="0">
        <tr>
            <td height="90%" width="50%" rowspan="2">
                <? $arbol->toBrowser(); ?>			
            </td>
            <td height="90%" align="center" valign="top" rowspan="2">
                <table width="100%" height="100%">
                    <tr>
                        <td >
                            <div style="height:100%;background-color:#f5f5f5;border :1px solid Silver;overflow:auto;">
                                <table width="100%" height="100%">
                                    <tr>
                                        <td align="left">
                                            <table>
                                                <tr><td id="td_1" onclick="radio.setCheckedIndex(0)"><input type="radio" id="itemType" name="itemType" value="1" />&nbsp;<img class="standartTreeImage" src="../../imagenes/tree/folderClosed.gif" />&nbsp;Modulo (Submenú)</td></tr>
                                                <tr><td id="td_2" onclick="radio.setCheckedIndex(1)"><input type="radio" id="itemType" name="itemType" value="2" />&nbsp;<img class="standartTreeImage" src="../../imagenes/tree/iexplore.jpg" />&nbsp;PaginaMenu</td></tr>
                                                <tr><td id="td_3" onclick="radio.setCheckedIndex(2)"><input type="radio" id="itemType" name="itemType" value="3" checked />&nbsp;<img class="standartTreeImage" src="../../imagenes/tree/leaf.gif" />&nbsp;PaginaFueraMenu</td></tr>
                                                <tr><td id="td_4" onclick="radio.setCheckedIndex(3)"><input type="radio" id="itemType" name="itemType" value="4" />&nbsp;<img class="standartTreeImage" src="../../imagenes/tree/item.gif" />&nbsp;Permiso</td></tr>
                                            </table>
                                        </td>
                                        <td align="center">
                                            <table>
                                                <input type="hidden" name="itemId" />
                                                <input type="hidden" name="op"><!-- operacion que se debe hacer-->
                                                <tr><td align="right">Nombre del Item <span id="span_id"></span>:</td><td><input type="text" name="itemName" id="itemName" onkeydown="if (window.event.keyCode==13) desc.focus();" /><br></td></tr>
                                                <tr><td align="right">Descripción:</td><td><input type="text" name="itemDesc" id="itemDesc" onkeydown="if (window.event.keyCode==13) if (path.disabled) bagregar.click(); else path.focus()" /><br></td></tr>
                                                <tr id="tr_path" ><td align="right" title="dentro del directorio modulos">Carpeta contenedora:</td><td><input type="text" name="itemPath" id="itemPath" onkeydown="if (window.event.keyCode==13) document.all.bagregar.click();"  title="carpeta dentro del directorio modulos" /><br></td></tr>
                                                <tr id="tr_parentID" ><td align="right" title="Id del padre en el arbol">ID del padre:</td><td><input type="text" name="itemParentId" id="itemParentId" onkeydown="if (window.event.keyCode==13) document.all.bmodificar.click();"  /><br></td></tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" align="center">
                                            <input type="button" name="bmodificar" id="bmodificar" onclick="fnModificar()" value="Modificar" style="width:100px"/>
                                            <input type="button" name="bagregar" id="bagregar" onClick="AgregarItem();" value="Agregar Item" style="width:100px"/>
                                            <input type="button" name="bborrar" id="bborrar" onClick="if (confirm('Se borraran tambien los permisos hijos\n\n ¿Seguro que desea eliminar?')) BorrarItem();" value="Borrar" style="width:100px"/>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td height="65%" style="max-height: 350px" align="center">
                            <b>Buscar:</b> <select name="select_usr_grp"  onchange="if (this.value==-1) document.all.bbuscar2.disabled=1; else document.all.bbuscar2.disabled=0">
                                <option value="-1">Seleccione...</option>
                                <option value="usuarios">Usuarios</option>
                                <option value="grupos">Grupos</option>
                            </select>
                            <b> con el permiso seleccionado </b><input type="button" name="bbuscar2" disabled value="Buscar" onclick="fnBuscar2()"/>
                            <div align="left" id='searchRes' style="height:80%;max-height:200px;background-color:#f5f5f5;border :1px solid Silver;overflow:auto;" />
                            </div>
                            <?
                            $w1 = new JsWindow("./permisos-usr.php");
                            $w1->setParam("usr_id", "selected", true);
                            $w1->toolBar = false;
                            $w1->menuBar = false;
                            $w1->titleBar = false;
                            $w1->center = true;
                            $w1->locationBar = false;
                            $w1->width = 900;
                            $w1->height = 600;

                            $w2 = new JsWindow("./permisos-grp.php");
                            $w2->toolBar = false;
                            $w2->menuBar = false;
                            $w2->titleBar = false;
                            $w2->center = true;
                            $w2->locationBar = false;
                            $w2->setParam("grp_id", "selected", true);
                            $w2->width = 900;
                            $w2->height = 600;
                            ?>				
                            <br>
                            <input type="button" value="Ver todos los permisos" onclick="fnVerPermisos_click()" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <input type="hidden" name="last_id" value="<?= $r->fields['id_permiso'] ?>" />	
</form>
<?php echo $footer; ?>
<script>
    function fnVerPermisos_click()
    {
        var selected;
        if((selected=treeSearch.getSelectedItemId()) =="")
        {
            alert('Debe seleccionar un usuario o un grupo');
            return;
        }
	
        if (document.all.select_usr_grp.value=='usuarios') 
<? $w1->toBrowser(); ?> 
            else 
<? $w2->toBrowser(); ?>
            }
     
        //para evitar q se envie el formulario al presionar enter en un campo de texto u otro lugar q no sea un boton de submit
        document.onkeypress=function(){if (window.event.keyCode==13) return false;};
        var radio=document.forms[0].itemType;
        var id = document.getElementsByName('itemId');
        //var id = document.getElementById('itemId');
        var name = document.getElementsByName('itemName');
        //var name = document.getElementById('itemName');
        var desc = document.getElementsByName('itemDesc');
        //var desc = document.getElementById('itemDesc');
        var type = document.getElementById('itemType');
        var path = document.getElementsByName('itemPath');
        //var path = document.getElementById('itemPath');
        var parentId= document.getElementsByName('itemParentId');
        //var parentId= document.getElementById('itemParentId');
        var bagregar=document.getElementById('bagregar');
        var bmodificar=document.getElementById('bmodificar');
        var bborrar=document.getElementById('bborrar');
        var last_id=document.getElementById('last_id');
        var changes = document.getElementById('changes');
        //var log = document.getElementById('logs');
        var select_usr_grp = document.getElementById('select_usr_grp');

        radio.getCheckedValue=function(){for (var i=0; i < this.length ; i++) if(this[i].checked) return this[i].value };
        radio.setCheckedValue=function(value)
        {
            for (var i=0; i < this.length ; i++) 
                if(this[i].value==value)
            { 
                if (i==0 || i==3)
                {
                    document.getElementById('tr_path').disabled=1;
                    path.disabled=1;
                }
                else
                {
                    document.getElementById('tr_path').disabled=0;
                    path.disabled=0;
                }
                this[i].checked=true;
            }
        };
        radio.setCheckedIndex=function(index)
        {
            if (index==0 || index==3)
            {
                document.getElementById('tr_path').disabled=1;
                path.disabled=1;
            }
            else
            {
                document.getElementById('tr_path').disabled=0;
                path.disabled=0;
            }
	
            this[index].checked=true;
            name.focus();
        }

        name.setValue=function(value)
        {
            this.value=value;
            //	for (var i in hframe)		alert(i);
        }
        desc.setValue=function(value)
        {
            this.value=value;
        }

        changes.add=function (opCode,itemId,itemParentId,itemType,itemName,itemDesc,dir)
        {
            var sep=',';//separador de valores para un nodo
            var aNodo=null;//el nodo con sus valores en un arreglo
            var oldParent;//se usa para recuperar el idParent del nodo antes de actualizarlo
            if (typeof raices[itemId]!='undefined')
            {
                //opCode,ID,type,name,desc,parentID,dir
                //	0			1		2		3			4			5			 6
                var aNodo=raices[itemId].split(sep);
            }
	
            switch (opCode)
            {
                case 'add':
                    //opCode,ID,type,name,desc,parentID,dir
                    raices[itemId]=opCode+sep+itemId+sep+itemType+sep+itemName+sep+itemDesc+sep+itemParentId+sep+dir;
                    break;
                case 'del':
                    //si el nodo era temporal (o sea q no existe en la BD)
                    if(aNodo && aNodo[0]=='add')
                        raices[itemId]=null;
                    else
                        raices[itemId]=opCode+sep+itemId+sep+itemType+sep+itemName+sep+itemDesc+sep+itemParentId+sep+dir;
                    break;
                case 'upd':
                    //si el nodo se agrego sin guardar en DB
                    if (aNodo)
                    {
                        if(aNodo[0]=='add')
                            opCode='add';//dejo el codigo para agregar a la BD
					
                        oldParent=aNodo[5];
                    }
                    else
                        oldParent=tree.getParentId(itemId);
                    //si cambio de lugar
                    if (oldParent!=itemParentId)
                    {
                        //Mover en el arbol
                        tree.doCut();
                        tree.doPaste(itemParentId);
                    }
                    raices[itemId]=opCode+sep+itemId+sep+itemType+sep+itemName+sep+itemDesc+sep+itemParentId+sep+dir;
                    break;
            }
            //	log.innerHTML +='op='+opCode+'#id='+itemId+'#parent='+itemParentId+'#type='+itemType+'#name='+itemName+'#desc='+itemDesc+'#dir='+dir+"<BR>";
        }


        function AgregarItem() 
        {
            var padre = (parentId.value=="")?tree.getSelectedItemId():parentId.value;
            if (!$("input[name='itemName']").val()) return;
            if (!$("input[id='itemDesc']").val()) return;
            if (!padre) padre='0';
		
            if (!chk_itemName($("input[name='itemName']").val()))
            {
                alert("Ya existe un item con ese nombre '"+$("input[name='itemName']").val()+"'");
                return 1;//no se pudo agregar el item
            }
            var parentType=parseInt(tree.getUserData(padre,"nodeType"));
            switch(parentType)
            {
                case 4://permiso 
                    alert('Ud. no puede agregar permisos hijos, al permiso seleccionado');
                    return 2;
                case 2://pagina de menu
                case 3://pagina fuera del menu
                    //Solo puede agregar permiso tipo comun
                    //radio.setCheckedValue(4);
                    break;
            }
            var nodeType=parseInt(radio.getCheckedValue());
            //var id=parseInt(last_id.value)+1;
            var id=parseInt($("input[name='last_id']").val())+1;
            //NOTA:
            //lOS PERMISOS TIPO PAGINA VAN DENTRO DE LAS CARPETAS
            switch(nodeType)
            {
                //carpeta contenedora
                case 1:
                    tree.insertNewItem(padre,id,$("input[id='itemDesc']").val(),0,"folderOpen.gif","folderOpen.gif","folderClosed.gif");
                    break;
                //pagina de menu
            case 2:
                tree.insertNewItem(padre,id,$("input[id='itemDesc']").val(),0,"iexplore.jpg","iexplore.jpg","iexplore.jpg");
                break;
            //item tipo boton, tabla o algo dentro de una pagina
        //CHEQUEAR SI ES DE TIPO 4, DEBE IR DENTRO DE UN PERMISO TIPO PAGINA(DE MENU O FUERA DE MENU)
    case 4:
        tree.insertNewItem(padre,id,$("input[id='itemDesc']").val(),0,"item.gif","item.gif","item.gif");
        //				tree.refreshItem(padre);
        break;
    //pagina fuera del menu
case 3:
default:
    tree.insertNewItem(padre,id,$("input[id='itemDesc']").val(),0,"leaf.gif","leaf.gif","leaf.gif");
    type=3;
    break;
		
}
//Para agregar un item en el mismo nivel que el actual
//		insertNewNext();
tree.setUserData(id,"name",$("input[name='itemName']").val());//nombre unico del item
tree.setUserData(id,"nodeType",nodeType);//tipo de permiso
tree.setUserData(id,"dir",$("input[id='itemPath']").val());//directorio (dentro de modulos) donde se ubica, en caso de ser una pagina
//last_id.value=id;	
$("input[name='last_id']").val(id);


changes.add("add",id,padre,nodeType,$("input[name='itemName']").val(),$("input[id='itemDesc']").val(),
    $("input[id='itemPath']").val());
$("input[name='itemName']").val('');
$("input[id='itemDesc']").val('');
$("input[id='itemPath']").val('');
$("input[id='itemParentId']").val('');
document.all.itemName.focus();
return 0;//TODO se salio bien
}

function BorrarItem() {
var id = tree.getSelectedItemId();
if (!id) return;

//alert("item id="+id+" parentId="+tree.getParentId(id))
changes.add("del",id,tree.getParentId(id));
tree.deleteItem(id);

}
function fnModificar()
{
var bmodificar=document.getElementById('bmodificar');
if (bmodificar.value=="Modificar")
{        
bmodificar.value="Guardar";
id.value=tree.getSelectedItemId();
$("span[id='span_id']").html("(id="+id.value+")");
//span_id.innerText="(id="+id.value+")";
$("input[id='itemParentId']").val(tree.getParentId(id.value));
//parentId.value=tree.getParentId(id.value);
radio.setCheckedValue(tree.getUserData(id.value,"nodeType"));
$("input[name='itemName']").val(tree.getUserData(id.value,"name"));
//name.setValue(tree.getUserData(id.value,"name"));
//desc.value=tree.getSelectedItemText();
$("input[id='itemDesc']").val(tree.getSelectedItemText());
//desc.setValue(tree.getSelectedItemText());
$("input[id='itemPath']").val(typeof tree.getUserData(id.value,"dir")=='undefined'?'':tree.getUserData(id.value,"dir"));
        
//path.value=typeof tree.getUserData(id.value,"dir")=='undefined'?'':tree.getUserData(id.value,"dir");
bagregar.disabled=true;
bborrar.disabled=true;
}
else
{
var nodeType=radio.getCheckedValue();
tree.setUserData(id.value,"nodeType",nodeType);
//tree.setUserData(id.value,"name",name.value);
tree.setUserData(id.value,"name",$("input[name='itemName']").val());
        
tree.setUserData(id.value,"dir",$("input[id='itemPath']").val());
//tree.setUserData(id.value,"dir",path.value);
tree.setItemText(id.value,$("input[id='itemDesc']").val()); 
//tree.setItemText(id.value,desc.value);
        
switch(nodeType)
{
    case '1':
    case 1: tree.setItemImage2(id.value,'folderClosed.gif','folderOpen.gif','folderClosed.gif');
        break;
    case '2':
    case 2: tree.setItemImage2(id.value,'iexplore.jpg','iexplore.jpg','iexplore.jpg');
        break;
    case '3':
    case 3: tree.setItemImage2(id.value,'leaf.gif','leaf.gif','leaf.gif');
        break;
    case '4':
    case 4: tree.setItemImage2(id.value,'item.gif','item.gif','item.gif');
        break;
}
changes.add("upd",id.value,$("input[id='itemParentId']").val(),nodeType,$("input[name='itemName']").val(),
$("input[id='itemDesc']").val(),$("input[id='itemPath']").val());
//changes.add("upd",id.value,parentId.value,nodeType,name.value,desc.value,path.value);
fnClear();
}
}
function GuardarDatos() {
//document.getElementById('tree_checked').value = tree.getAllChecked();
changes.value=raices.toString();
alert(changes.value);
if(changes.value=="")
{
alert("no hay cambios para guardar");
return false;
}
}
function fnClear()	
{
id.value="";
//span_id.innerText="";
radio.setCheckedValue(3);
// name.setValue("");
$("input[name='itemName']").val("");
name.value="";
$("input[id='itemDesc']").val("");
desc.setValue("");
$("input[id='itemParentId']").val("");
parentId.value="";
$("input[id='itemPath']").val("");
path.value="";
    
//document.forms[0].bmodificar.value="Modificar";
document.getElementById("bmodificar").value="Modificar";
bagregar.disabled=false;
bborrar.disabled=false;
}
function fnOnClick_divpermisos()
{
tree.selectItem(0);
//		span_id.innerText=parseInt(last_id.value)+1;
span_id.innerText="";
}
function fnDisableRow(rowId,boolval)
{
document.getElementById(rowId).disabled=boolval?true:false;
}
	
/*init menu
                aMenu=new dhtmlXContextMenuObject('120',0,"Demo menu");
                aMenu.menu.setGfxPath("../../imagenes/menu/");		
                aMenu.menu.loadXML("_menu.xml");				
                aMenu.setContextMenuHandler(onMenuClick);
function onMenuClick(id){
        alert("Menu item "+id+" was clicked");
}
*/
treeSearch=new dhtmlXTreeObject("searchRes","100%","100%",0);
treeSearch.setImagePath("<?= $html_root; ?>/imagenes/tree/");
treeSearch.enableTreeImages(0);
treeSearch.enableTreeLines("disable");	
treeSearch.enableCheckBoxes(false);
treeSearch.enableThreeStateCheckboxes(false);

//	treeSearch.loadXML('<?= $html_root ?>/modulos/permisos/lista_usr_xml.php');

function myDragHandler(idFrom,idTo){
//if we return false then drag&drop be aborted
return true;
}


function mover(item) {
var id_item=menu.getItem(item);
menu.removeItem(item);
menu.addFirstLevel(menu.getPanel("0"),id_item);
}
/**
* checkea que no se repita el nombre de un item nuevo antes de ingresarlo en el arbol
*/
function chk_itemName(newItemName)
{
var itemId=0;
		
var strItems=tree.getAllSubItems(0);
//alert(strItems);
var items=strItems.split(',');
for (var i in items)
{
if (tree.getUserData(items[i],"name")==newItemName)
    return false;//No se puede agregar con ese nombre
}
return true;
}
	
	
/*		*/
function fix_size() {
//dependiendo del largo del formulario, seteamos el largo del div del formulario
var largo=parseInt((parent.document.getElementById('frame2'))?parent.document.getElementById('frame2').clientHeight:document.body.clientHeight)-20;
//document.getElementById('tabla_contenido').style.height=largo+"px";
}
	
//variable usada para mantener los ids de los arboles que se van agregando
var raices=new Array();
raices.toString=function()
{
var str="";
var sep="";
for (var i=0; i < this.length; i++)
if (this[i]!=null)
{
//str+=sep+this[i].toString();
str+=sep+this[i];
sep="#";
}
//		if (str!='')	return str.substr(1);
return str
}
function fnBuscar2()
{
treeSearch.deleteChildItems("0");
var id=tree.getSelectedItemId();
if (id=="" || id==0)
{
alert("Debe Seleccionar un permiso");
return;
}
//treeSearch.loadXML('./permisos_xml.php?get='+select_usr_grp.value+'&id='+id);
treeSearch.loadXML('./permisos_xml.php?get='+$("select[name='select_usr_grp']").val()+'&id='+id);
    
}
</script>
<html>