<?


require_once "../../config.php";
require_once(LIB_DIR."/class.gacz.php");
require_once("./permisos.class.php");

if($_POST['bguardar'])
{
	require("permisos-usr_proc.php");
	$msg="<b>Los permisos de usuario han sido guardados</b>";
}
echo $html_header;
echo "<link rel='STYLESHEET' type='text/css' href='$html_root/lib/dhtmlXMenu_xp.css'>\n";
echo "<link rel='STYLESHEET' type='text/css' href='$html_root/lib/dhtmlXMenu.css'>\n";

$arbol=new HTMLArbolPermisos();
$arbol->insertScripts();
$arbol->checkboxes=true;
$arbol->bguardar->set_event("onclick","bguardar_click()");

$oSelect=new HtmlOptionList("select_usuarios",10,"height:100%;width:100%;");
$sql_tmp = "SELECT usuarios.id_usuario,usuarios.login,";
//$sql_tmp .= "usuarios.id_usuario||' '||usuarios.nombre ||' '|| usuarios.apellido||' ('||usuarios.login||')' as nombre,";
$sql_tmp .= "usuarios.nombre ||' '|| usuarios.apellido||' ('||usuarios.login||')' as nombre,";
$sql_tmp .= "usuarios.comentarios ";
$sql_tmp .= "FROM usuarios ";
$sql_tmp .= "join phpss_account on phpss_account.username=usuarios.login ";
$sql_tmp .= "where active='true' ";
$sql_tmp .= "order by nombre ";

$r=sql($sql_tmp) or fin_pagina();
$oSelect->optionsFromResulset($r,array("value"=>"id_usuario","text"=>"nombre"));
//$oSelect->add_event("onclick","changeUser()");
$oSelect->add_event("onclick","fnChange()");
$oSelect->setSelected($_GET['usr_id']);//si se paso el id_usuario por GET
if ($oSelect->selectedIndex!=-1)
{
	$arbol->url="./permisos_xml.php?get=usr1&usr_id=".$oSelect->options[$oSelect->selectedIndex]->value;
	$arbol->loadOnInit=true;
}

$oSelectGrupos=new HtmlOptionList("select_grupos",10,"height:100%;width:100%;");
$oSelectGrupos->add_option("Logística");
$oSelectGrupos->add_option("Proveedores");

$win=new JsWindow("lista_grp.php");
$win->locationBar=false;
$win->linkBar=false;
$win->menuBar=false;
$win->toolBar=false;

$usuario = $_GET["usuario"] or $usuario = "";
?>
	<form id="form_guardar" method="post" action="<?=$_SERVER["PHP_SELF"]; ?>">
	<input type="hidden" name="tree_checked" id="tree_checked" value="">
	<input type="hidden" name="group_checked" id="group_checked" value="">
	<? if ($msg) echo "<center>$msg<center>"; ?>
	<table id="tabla_contenido" width="100%" height="94%" align="center" border="0" cellpadding="2" cellspacing="0">
	<tr>
			<td align="center" width="40%">			
				<b>Usuarios (<?=$oSelect->length ?>)</b><br/>
				<div id="usuarios" style="height:40%;max-height: 200px;background-color:#f5f5f5;border :1px solid Silver;overflow:auto;">
				<?	$oSelect->toBrowser();?>
				</div>
				<b>Grupos a los que pertenece el usuario </b><br/>
				<div id="grupos" align="left" style="height:48%;max-height: 200px;width:100%;background-color:#f5f5f5;border :1px solid Silver;overflow:auto;" />
			</td>
			<td align="center" height="100%" style="max-height: 250px;">
				<b>Permisos de Usuario</b><br/>
<? $arbol->toBrowser();?>
			</td>
		</tr>
	</table>
	</form>
        <?php echo $footer;?>
	<script>
            
            //var oUser=document.getElementById('select_usuarios');
	//oUser.focus();
	function bguardar_click()
	{
		document.getElementById('tree_checked').value = tree.getAllChecked();
		document.getElementById('group_checked').value = treeGrupos.getAllChecked();
	}
//si el combo viene seleccionado se hace automaticamente el primer requerimiento
//var first_urequest=<? if ($oSelect->selectedIndex!=-1) echo "false"; else echo "true"; ?>;
var first_urequest=true;
function fnChange()
{    
	if (!first_urequest)
	{
		doRequest('<?=$html_root?>/modulos/permisos/permisos_xml.php?get=usr&usr_id='+
                    $("select[name='select_usuarios'] option:selected").val(),processReqChange);
                //doRequest('<?=$html_root?>/modulos/permisos/permisos_xml.php?get=usr&usr_id='+oUser.value,processReqChange);
	}
	else
	{
		//hago el primer requerimiento
		//first_urequest=false;
		tree.deleteChildItems("0");
		tree.loadXML('<?=$html_root?>/modulos/permisos/permisos_xml.php?get=usr1&usr_id='+
                    $("select[name='select_usuarios'] option:selected").val());
	}
	treeGrupos.deleteChildItems("0");
	treeGrupos.loadXML('<?=$html_root?>/modulos/permisos/lista_grp_xml.php?usr_id='+
            $("select[name='select_usuarios'] option:selected").val());
}
var oSelect=document.forms[0].select_usuarios;
var oSelectGrupos=document.getElementById("select_grupos");
function fnRemoveGroup()
{
	if (oSelect.selectedIndex >= 0 && oSelectGrupos.selectedIndex >=0)
	{
		var msg="Se eliminará el usuario "+oSelect.options[oSelect.selectedIndex].text;
			 msg+=" del grupo "+oSelectGrupos.options[oSelectGrupos.selectedIndex].text;
			 msg+="\n\n¿Seguro que desea eliminar?";
		if (confirm(msg))
			oSelectGrupos.options[oSelectGrupos.selectedIndex]=null;	
	}
}
/****************************************** AJAX Begin ******************************************************/
var req;
function doRequest(url,fn) {
   if (window.XMLHttpRequest) {
       req = new XMLHttpRequest();
   } else if (window.ActiveXObject) {
       req = new ActiveXObject("Microsoft.XMLHTTP");
   }
   req.open("GET", url, true);
   req.onreadystatechange = fn;
   req.send(null);   
}
function processReqChange() {
    // only if req shows "loaded"
    if (req.readyState == 4) {
        // only if "OK"
        if (req.status == 200) {
            // ...processing statements go here...
            setCheckedIds(eval(req.responseText));
        } else {
            alert("There was a problem retrieving the data:\n" +req.statusText);
        }
    }
}
/****************************************** AJAX End ********************************************************/

/**
* Array aIds contiene los ids q se deben checkear
*/
function setCheckedIds(aIds)
{
		aIds2=tree.getAllChecked().split(",");
		for(var i=0; i < aIds2.length;i++)
			tree.setCheck(aIds2[i],0);		
		for(var i=0; i < aIds.length;i++)
			tree.setCheck(aIds[i],1);
}
	
	function changeUser() {
				tree.deleteChildItems("0");
				tree.loadXML('<?=$html_root?>/modulos/permisos/permisos_xml.php?get=usr&usr_id='+oUser.value);
				treeGrupos.deleteChildItems("0");
				treeGrupos.loadXML('<?=$html_root?>/modulos/permisos/lista_grp_xml.php?usr_id='+oUser.value);
			}
			function fnOnClick_treeGrupos(ID)
			{
				alert(treeGrupos.getUserData(ID,"permisos"));
			}
			function fnOnCheck_treeGrupos(ID,Current)
			{
				var str;
				var permisos;
//				alert(Current);
				var msg ="Se quitaran todos los permisos del grupo para el usuario seleccionado\n\n";
						msg+="¿Desea eliminar los permisos del usuario definitivamente?\n\n";
						msg+="ACEPTAR = Eliminar permisos de grupo para este usuario\n";
						msg+="CANCELAR = Dejar permisos como permisos de usuario\n";
				if (typeof (str=treeGrupos.getUserData(ID,"permisos"))!='undefined')
				{
					permisos=str.split(",");
					//Si se checkeo el grupo
					if (Current)
						for (var i in permisos)
							tree.setCheck(permisos[i],Current);

					//si se descheckeo el grupo Y
					//confirma q se eliminan definitivamente los permisos para este usuario
					if (!Current && confirm(msg))
					{
						for (var i in permisos)
							tree.setCheck(permisos[i],Current);
					}					
				}
			}

			var treeGrupos=new dhtmlXTreeObject("grupos","100%","100%",0);
			treeGrupos.setImagePath("<? echo $html_root; ?>/imagenes/tree/");
			treeGrupos.enableTreeImages(0);
			treeGrupos.enableCheckBoxes(true);
			treeGrupos.enableTreeLines("disable");
			treeGrupos.setOnCheckHandler(fnOnCheck_treeGrupos);
<? if ($oSelect->selectedIndex!=-1)
		echo "\ntreeGrupos.loadXML('./lista_grp_xml.php?usr_id={$oSelect->options[$oSelect->selectedIndex]->value}');";
?>
			function fix_size() {
				//dependiendo del largo del formulario, seteamos el largo del div del formulario
				var largo=parseInt((parent.document.getElementById('frame2'))?parent.document.getElementById('frame2').clientHeight:document.body.clientHeight)-20;
				//document.getElementById('tabla_contenido').style.height=largo+"px";
			}
/* */
	</script>
</body>
</html>
