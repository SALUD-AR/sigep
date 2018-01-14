<?

require_once("../../config.php");
require_once("./permisos.class.php");

$menu = $parametros["menu"]; 
$mode = $parametros["mode"];

//print_r($parametros);
function menuitem ($datos) {
/*	echo "<tr class='sub' title='".$datos["ayuda"]."'";
	echo ">\n";
	echo "<td class='icon'>";
	if ($datos["icono"]) echo "<img src='$html_root/imagenes/".$datos["icono"]."'>";
	else echo "&nbsp;";
	echo "</td>\n";
	echo "<td nowrap id=ItemNormal";
	echo ">".$datos['descripcion']."</td>\n";
	echo "<td id=SubMenu>\n";*/
	echo "<item id='".utf8_encode($datos['nombre'])."' text='".utf8_encode($datos['descripcion'])."'>\n";
//	echo "<userdata name='name'>".utf8_encode($datos['nombre'])."</userdata>\n";
}
function generaritem($items) {
	global $item_menu,$mainmenu,$menu_sub,$item_default,$file;
	while (list($key,$item)=each($items)) {
		if (!$item["padre"]) $item["padre"]="inicio";
		if($item["tipo"] == "sub") {
			if ($menu_sub[$item["nombre"]]) {
				Error("No se pudo cargar el item ".$item["nombre"]." (".$item["descripcion"].") porque ya existe en el menu");
				continue;
			}
		}
		else {
			if ($mainmenu[$item["nombre"]]) {
				Error("No se pudo cargar el item ".$item["nombre"]." (".$item["descripcion"].") porque ya existe en el menu");
				continue;
			}
		}
//		$item["modulo"] = $file;
		while(list($key,$val) = each($item))
			$item_menu[$key] = $val;
		if($item["tipo"] == "sub")
			$menu_sub[$item["nombre"]]=$item_menu;
		else
			$mainmenu[$item["nombre"]]=$item_menu;
		$item_menu = $item_default;
	}
}

function filtrarpadre($padre,$menubar=0) {
		global $mainmenu,$menu;
		$pos_temp=Array();
		reset($mainmenu);
		while (list($key,$valor)=each($mainmenu)) {
//			if (($valor["padre"] == $padre) && ($valor["nombre"] != $mainmenu[$menu]["padre"] && $valor["nombre"] != $mainmenu[$menu]["nombre"]))
//			if (($valor["padre"] == $padre) && ($padre != "inicio"))
			if ($valor["padre"] == $padre && !$valor["en_menu"])
//			if ($valor["padre"] == $padre)
				$pos_temp[$valor["nombre"]]=$valor["descripcion"];
		}
		asort($pos_temp);
		if (count($pos_temp)>0) {
/*				if ($padre!="inicio") {
						if ($menubar) echo "<span class='more'>4</span>\n";
						else echo "&nbsp;";
						echo "<table cellspacing='0' class='menu' style='visibility: visible;'>\n";
				}
*/			reset($pos_temp);
				while (list($key,$valor)=each($pos_temp)) 
				{
						menuitem($mainmenu[$key]);
						filtrarpadre($key,1);
				}
//				echo "</table></td>";
				echo "</item>\n";
//				if ($menubar) echo "</tr>\n";
		}
		else {
//			echo "&nbsp;</td>\n";
				echo "</item>\n";
		}
}
function generar_inicio() {
	global $mainmenu,$mode;
//	if ($mode == "seleccionar_inicio")
//		$params = array("mode" => "inicio_seleccionado", "inicio_seleccionado" => $mainmenu[$key]["descripcion"],"menu" => $mainmenu[$key]["nombre"],"modulo" => $mainmenu[$key]["modulo"]);
//	else
//		$params = array("menu" => $mainmenu[$key]["nombre"],"modulo" => $mainmenu[$key]["modulo"]);
//	echo "<td class='root' title='Página principal' href='index.php'>Inicio</td>\n";

	while (list($key,$valor)=each($mainmenu)) {
		if ($valor["padre"] == "inicio" && !$valor["en_menu"]) {
			$pos_temp[$valor["nombre"]]=$valor["descripcion"];
		}
	}
	asort($pos_temp);
	while (list($key,$valor)=each($pos_temp)) {
		echo "<item id='".utf8_encode($mainmenu[$key]['nombre'])."' text='".utf8_encode($mainmenu[$key]['descripcion'])."'>\n ";
//		echo "<userdata name='name'>".utf8_encode($mainmenu[$key]['nombre'])."</userdata>\n";
		filtrarpadre($key);
	}
}
$item_default = array(
		"nombre"        => "",
		"link"          => "",
		"tipo"			=> "item",
		"modulo"        => "",
		"icono"         => "",
		"ayuda"         => "",
		"posicion"      => 0,
		"padre"         => "inicio"
);
$mainmenu=array();
$menu_sub=array();
		$file_include = MOD_DIR."/config.php";
		if (file_exists($file_include)) { // exite el archivo config.php
			$item=array();
			include($file_include);

			generaritem($item);
		}
		else {
			die("ERROR: No se encontro el archivo de configuracion del menu");
		}

$mm=$mainmenu;
while (list($key,$valor)=each($mm)) {
	if (1)//(permisos_check($valor["padre"],$key)) 
	{
		$nombre = $valor["nombre"];
		while ($mainmenu[$nombre]["padre"] != "default") {
			$nombre = $mainmenu[$nombre]["padre"];
			if (!isset($mainmenu[$nombre])) {
				$mainmenu[$nombre] = $menu_sub[$nombre];
			}
		}
		if ($mainmenu[$key]["tipo"] == "item") {
			if ($valor["link"] == "") {
				if ($mode == "seleccionar_item")
					$params = array("mode" => "item_seleccionado", "item_seleccionado" => $mainmenu[$key]["nombre"],"menu" => $mainmenu[$key]["nombre"]);
				else
					$params = array("menu" => $mainmenu[$key]["nombre"]);
				$mainmenu[$key]["link"] = encode_link("index.php",$params);
			}
//			else echo "link: ".$valor["link"];
		}
		if ($key == "inicio") {
			if ($mode == "seleccionar_item")
				$mainmenu[$key]["link"] = encode_link($html_root."/index.php",array("mode" => "item_seleccionado", "item_seleccionado" => "inicio"));
			else
				$mainmenu[$key]["link"] = $html_root."/index.php";
		}
	}
	else {
		unset($mainmenu[$key]);
	}
}
//Se incluye un arrreglo de archivos llamado $archivos
include("arreglo_archivos.php");
include("arreglo_botones.php");

//Crea un arbol de permisos a partir de un arbol XML q solo contiene nodos <item>
function getTree(DOMNode &$domNode,ArbolOfPermisos $arbol)
{
	global $archivos;
	for ($itemNode= $domNode->firstChild; $itemNode != NULL; $itemNode = $itemNode->nextSibling,$i++) 
	{
		if ($itemNode->nodeType==XML_TEXT_NODE)			continue;
		$tipo=$itemNode->hasChildNodes() && $itemNode->childNodes->length >1 ?Modulo:PaginaMenu;
/*		print_r($itemNode);
		echo "<br>uname=". $uname=utf8_decode($itemNode->attributes->getNamedItem('id')->value);
		echo "<br>desc=".$desc=utf8_decode($itemNode->attributes->getNamedItem('text')->value);
*/		
		$uname=utf8_decode($itemNode->attributes->getNamedItem('id')->value);
		$desc=utf8_decode($itemNode->attributes->getNamedItem('text')->value);
		
		$p=new ArbolOfPermisos($uname,$desc,$tipo);
//		$p->dir=$itemNode->hasChildNodes();
		//si es una pagina, busco su ubicacion dentro de modulos
		if ($tipo==PaginaMenu)		
		{
			//Busco la ruta donde se encuentra el archivo dentro de modulos
			foreach ($archivos as $key => $value)
			{
				//en caso de que sea una pagina con parametros
				if (ereg("(.*)\?.*",$uname,$tmp))
					$uname=$tmp[1];
				if (ereg("(.*)/($uname\.php$)",$value,$arr))
				{
					$p->dir=$arr[1];
//					unset($archivos[$key]);//lo elimino para achicar el arreglo
					break;
				}
			}			
		}
		$arbol->appendChild($p);
		getTree($itemNode,$p);//recupero el arbol de p
	}
}
/**
 * Genera los permisos de tipo pagina menu
 *
 * @param string $xmlstr es el xml q representa el arbol del menu para generar los permisos
 */
function paginasMenu($xmlstr="")
{
	global $db;	
	$db->startTrans();
	$xml=new DOMDocument();
	$xml->preserveWhiteSpace = false;
	if($xmlstr!="")
		$xml->loadXML($xmlstr);
	else 
		$xml->load("menu.xml");
	$root=$xml->firstChild;
	$arbol=new ArbolOfPermisos("root");
	$arbol->createTree();
	getTree($root,$arbol);
	$arbol->saveDB();
	Header("Content-type:text/plain");
	$arbol->saveXML();
	$db->completeTrans();

}

function paginasFuera()
{
		global $archivos;
		global $db;		
		$q ="SELECT a.value as sectionvalue,b.value,b.name ";
		$q.="FROM permisos.aro_sections AS a ";
		$q.="JOIN permisos.aro AS b ON a.value=b.section_value ";
		$q.="WHERE b.value IS NOT NULL AND a.value='inicio' ";
		$q.="ORDER BY a.value, b.value";
		$r=sql($q) or die($q);
		
		$db->startTrans();
		while (!$r->EOF) 
		{
			$uname=$r->fields['value'];
			$desc=$r->fields['name'];
			//en caso de que sea una pagina con parametros
			if (ereg("(.*)\?.*",$uname,$tmp))
					$uname2=$tmp[1];
			else 
				$uname2=$uname;
			//Busco la ruta donde se encuentra el archivo dentro de modulos
			foreach ($archivos as $key => $value)
			{
					//si esta en $archivos es un paginaFueramenu
					if (ereg("(.*)/($uname2\.php$)",$value,$arr))
					{
						$p=new ArbolOfPermisos($uname,$desc,PaginaFuera);
						$p->dir=$arr[1];
						$padre=new ArbolOfPermisos($arr[1]);
						$padre->appendChild($p);
						$padre->saveDB();
						break;
					}
			}			
			$r->movenext();
		}
		$db->completeTrans();

}

function botones()
{
	$q ="SELECT a.value as sectionvalue,b.value,b.name ";
	$q.="FROM permisos.aro_sections AS a ";
	$q.="JOIN permisos.aro AS b ON a.value=b.section_value ";
	$q.="WHERE b.value IS NOT NULL AND a.value='inicio' ";
	$q.="ORDER BY a.value, b.value";
	$r=sql($q) or die($q);
	global $botones;
	global $db;
	$db->startTrans();
	$pfuera=new ArbolOfPermisos("BOTONES_fuera","BOTONES_fuera",Modulo);

	while (!$r->EOF) 
	{
		$uname=$r->fields['value'];
		$desc=$r->fields['name'];
		//si existe en el listado de permisos en uso
		if(($file=$botones[$uname])!="") 
		{
			//busco la parte final de la ruta o sea el nombre de archivo
			if (ereg("(.*)/([a-zA-Z\-_0-9 ]*)$",$file,$arr))
			{
//				die($arr[2]);
				$file=$arr[2];
				$p=new ArbolOfPermisos($file,"",PaginaFuera);
				if ($p->dir=="")
					$p->dir=$arr[1];
				$p->appendChild(new ArbolOfPermisos($uname,$desc,Permiso));
				$p->saveDB();
			}
			else //Los agrego todos en un mismo Modulo si no encontre el padre en el arbol
				$pfuera->appendChild(new ArbolOfPermisos($uname,$desc,Permiso));
		}
		$r->movenext();
	}
	$pfuera->saveDB(0);
	$db->completeTrans();
}

function permisos_cargarN($usuario) 
{
		global $permisos;
		$sql = "SELECT a.value,b.value FROM aro_sections AS a ";
		$sql .= "LEFT JOIN permisos.aro AS b ON a.value=b.section_value ";
		$sql .= "WHERE b.value IS NOT NULL ORDER BY a.value, b.value";
	
		$result = sql($sql) or fin_pagina();
		while (!$result->EOF) {
			list($aro_section_value,$aro_value) = $result->fields;
			$acl_result = $permisos->acl_query("usuarios", $usuario, $aro_section_value, $aro_value);
			$access = &$acl_result['allow'];
			if ($access) {
//				$permitidos[$aro_section_value][$aro_value] = 1;
				$permitidos[] = $aro_value;
			}
			$result->MoveNext();
		}
		return $permitidos;
}
function permisos_usuarios($page=0)
{
	global $db;
$page*=10;
//Recupero todos los usuarios activos del sistema	
$sql_tmp = "SELECT usuarios.id_usuario,usuarios.login,";
//$sql_tmp .= "usuarios.id_usuario||' '||usuarios.nombre ||' '|| usuarios.apellido||' ('||usuarios.login||')' as nombre,";
$sql_tmp .= "usuarios.nombre ||' '|| usuarios.apellido||' ('||usuarios.login||')' as nombre,";
$sql_tmp .= "usuarios.comentarios ";
$sql_tmp .= "FROM usuarios ";
$sql_tmp .= "join phpss_account on phpss_account.username=usuarios.login ";
$sql_tmp .= "where active='true' ";
$sql_tmp .= "order by login ";
$sql_tmp .= "limit 10 offset $page ";

$r=sql($sql_tmp) or fin_pagina();
	$db->startTrans();

	echo "Pagina: ".($page/10)."<br>";
	echo "cantidad total: ".$r->recordcount()."<Br>";
	while (!$r->EOF) 
	{
		$skip=0;
		switch ($r->fields['login']) {
			case 'gonzalo':
			case 'nazabal':
			case 'mariela':
			case 'marcos':
			case 'fernando':
			case 'ferni':
			case 'quique':
			case 'cestila':
					$skip=1;
			break;
		
			default:
				break;
		}
		echo "<hr>";
		echo "user_login=".$r->fields['login']." user_id=".$r->fields['id_usuario']."<br>";
		if ($skip)
		{
			echo "SKIP=$skip<br>";
			$r->movenext();
			continue;			
		}
		$arr=permisos_cargarN($r->fields['login']);
		if(is_array($arr))
		{	
			$arr="'".implode("','",$arr)."'";
			//AQUI INSERTAR CODIGO PARA INSERTAR LOS PERMISOS
			
			$q ="insert into permisos_usuarios (id_permiso,id_usuario) ";
			$q.="select id_permiso,".$r->fields['id_usuario']." ";
			$q.="from permisos ";
			$q.="where permisos.uname in ($arr) ";
			sql($q) or die($q);
//			echo "$q<br>";
		}
		//print_r($arr);
		$r->movenext();
	}
	$db->completeTrans();		
}

?>
