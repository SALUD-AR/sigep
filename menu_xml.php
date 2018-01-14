<?

require("config.php");
require_once(ROOT_DIR."/modulos/permisos/permisos.class.php");

Header("Content-type:text/xml");

$usuario=new user($_ses_user['login']);
$id=$usuario->get_id_usuario();

$query="select data from permisos.permisos_sesion
		where id_usuario=$id";
$result=sql($query,"<br>Error <br>") or fin_pagina();

if ($result->RecordCount()) {
    $ff=descomprimir_variable($result->fields["data"]);
    echo $ff;
}
else {
$arbol=new ArbolOfPermisos("root");	
$arbol->createMenu($usuario);
ob_start();
$arbol->saveXMLMenu();
$menu_new=ob_get_contents();
//file_put_contents("menunew.xml",$menu_new);
$menu_guardar=comprimir_variable($menu_new);

//guardar permisos

$query="insert into permisos.permisos_sesion (id_usuario,data) values($id,'$menu_guardar')";
sql($query,"<br>Error al insertar/actualizar los permisos actualizados para el usuario<br>") or fin_pagina();
}
?>