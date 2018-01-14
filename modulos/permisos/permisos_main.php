<?
set_time_limit(0);
require_once "../../config.php";

echo $html_header;
$cmd=$parametros['cmd'];
$ses_cmd=phpss_svars_get("_ses_permisos_cmd");
if ($cmd=="" && $ses_cmd=="")
	$cmd="p";
elseif ($cmd!="")
	phpss_svars_set("_ses_permisos_cmd",$cmd);   
elseif ($ses_cmd!="")
	$cmd=$ses_cmd;
      

if (!file_exists("permisos.xml")) {
        $arbol=new ArbolOfPermisos("root");		
		$arbol->createTree();
		ob_start();
		$arbol->saveXML();
		file_put_contents("permisos.xml",ob_get_contents());
		ob_end_clean();
		$arbol=null;
}	


$datos_barra = array(
                    array(
                        "descripcion"    => "Permisos",
                        "cmd"            => "p"
                        ),                   
                    array(
                        "descripcion"    => "Permisos-Usuarios",
                        "cmd"            => "pu"
                        ),
                    array(
                        "descripcion"    => "Permisos-Grupos",
                        "cmd"            => "pg"
                        )                  
	);
generar_barra_nav($datos_barra);
$html_root=((($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER['SERVER_NAME']).$html_root;
?>
<div id="div_formulario">
<? 
	switch ($cmd) {
		case 'p': include("./permisos.php");break;
		case 'pu': include("./permisos-usr.php"); break;
		case 'pg': include("./permisos-grp.php"); break;
	}
?>
</div>
</body>
</html>