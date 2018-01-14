<?

require("config.php");

$menu = $parametros["menu"];
$mode = $parametros["mode"];

function menuitem ($datos) {
	echo "<tr class=sub title='".$datos["ayuda"]."'";
	
	if ($datos["link"]) echo " onclick='if (checkform()) {if (confirm(\"Se ha cambiado el formulario. ¿Esta seguro que quiere continuar?\")) parent.window.location=\"".$datos["link"]."\";} else {parent.window.location=\"".$datos["link"]."\";}'";
	echo ">\n";
	echo "<td class='icon'>";
	if ($datos["icono"]) echo "<img src='$html_root/imagenes/".$datos["icono"]."'>";
	else echo "&nbsp;";
	echo "</td>\n";
	echo "<td nowrap id=ItemNormal";
	echo ">".$datos['descripcion']."</td>\n";
	echo "<td id=SubMenu>\n";
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
			if ($valor["padre"] == $padre && !$valor["en_menu"])
				$pos_temp[$valor["nombre"]]=$valor["descripcion"];
		}
		asort($pos_temp);
		if (count($pos_temp)>0) {
				if ($padre!="inicio") {
						if ($menubar) echo "<span class=more>4</span>\n";
						else echo "&nbsp;";
						echo "<table cellspacing='0' class='menu' style='visibility: hidden;'>\n";
				}
				reset($pos_temp);
				while (list($key,$valor)=each($pos_temp)) {
						menuitem($mainmenu[$key]);
						filtrarpadre($key,1);
				}
				echo "</table></td>";
				if ($menubar) echo "</tr>\n";
		}
		else {
			echo "&nbsp;</td>\n";
		}
}
function selectitem($select) {
		global $mainmenu,$menu;
		if (!is_array($mainmenu[$select])) {
			generar_inicio();
			return;
		}
		if ($mainmenu[$select]["padre"] != "default") {
			$GLOBALS["mainmenu"][$select]["en_menu"] = 1;
			selectitem($mainmenu[$select]["padre"]);
		}
		echo "<td nowrap class=root ";
		if ($mainmenu[$select]["link"])
			echo "href='".$mainmenu[$select]["link"]."' ";
			
		if ($mainmenu[$select]["ayuda"])
			echo "title='".$mainmenu[$select]["ayuda"]."' ";
		if ($select == $menu)
			echo "id=ItemActual";
		echo ">".$mainmenu[$select]["descripcion"]."\n";
		if ($select == "inicio")
			echo "<table cellspacing='0' class='menu' style='visibility: hidden;'>\n";
		filtrarpadre($select);
}
function generar_inicio() {
	global $mainmenu,$mode;

	echo "<td class=root title='Página principal'>Inicio</td>\n";

	while (list($key,$valor)=each($mainmenu)) {
		if ($valor["padre"] == "inicio" && !$valor["en_menu"]) {
			$pos_temp[$valor["nombre"]]=$valor["descripcion"];
		}
	}
	asort($pos_temp);
	while (list($key,$valor)=each($pos_temp)) {
		echo "<td class=root ";
		if ($mainmenu[$key]["ayuda"])
			echo "title='".$mainmenu[$key]["ayuda"]."'";
		echo ">".$mainmenu[$key]["descripcion"]."\n";
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

		//$file_include = MOD_DIR."/config.php";
		$file_include = MOD_DIR."/ayuda_menu/datos_menu.php";
		//echo "file_include".$file_include;
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
	//if (permisos_check($valor["padre"],$key)) {
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
				$mainmenu[$key]["link"] = encode_link("$html_root/modulos/ayuda_menu/ayuda_menu.php",$params);
			}
		}
		if ($key == "inicio") {
			if ($mode == "seleccionar_item")
				$mainmenu[$key]["link"] = encode_link("$html_root/modulos/ayuda_menu/ayuda_menu.php",array("mode" => "item_seleccionado", "item_seleccionado" => "inicio"));
			else
				$mainmenu[$key]["link"] = "$html_root/modulos/ayuda_menu/ayuda_menu.php";
		}
//	}
//	else {
//		unset($mainmenu[$key]);
//	}
}

echo "<html>\n";
echo "<head>\n";
echo "<link type='text/css' href='lib/menu.css' REL='stylesheet'>\n";
echo "<script type='text/javascript' src='lib/swipe.js'></script>\n";
echo "<script type='text/javascript' src='lib/menu.js'></script>\n";
echo "<script type='text/javascript' src='lib/checkform.js'></script>\n";
echo "</head>\n";
echo "<body SCROLL='no' style='background-color: transparent'>\n";

echo "<table id='menu' cellspacing='1' onselectstart='return false' onmouseover='menuOver()' onmouseout='menuOut()' onclick='menuClick()'>\n";
echo "<tr id=menubar>\n";
$pos_temp=Array();

if (count($mainmenu) > 0) {
	reset($mainmenu);
	if (!$menu) {
		generar_inicio();
	}
	else {
		selectitem($menu);
	}
}
else {
	echo "</tr><tr>";
}
echo "<td width=100% class='disabled'>&nbsp;</td>";
echo "<td width=70 align=center class='root' href='".encode_link($html_root."/modulos/ayuda_menu/ayuda_menu.php",array("mode"=>"cerrar"))."'>Cerrar</td>";
echo "</tr></table>\n";

echo "</body></html>\n";
?>
