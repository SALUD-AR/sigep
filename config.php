<?

/*******************************************
 ** Constantes para usar en los include/require
 ** (Directorios relativos al sistema)
 *******************************************/

define("ROOT_DIR", dirname(__FILE__));			// Directorio raiz
define("LIB_DIR", ROOT_DIR."/lib");				// Librerias del sistema
define("MOD_DIR", ROOT_DIR."/modulos");			// Modulos del sistema
define("UPLOADS_DIR", ROOT_DIR."/uploads");		// Directorio para uploads
/*******************************************
 ** Headers para que el explorador no guarde
 ** las páginas en cache.
 *******************************************/

header("Cache-control: no-cache");
header("Expires: ".gmdate("D, d M Y H:i:s")." GMT");

require_once LIB_DIR."/Browser.php";
$browser = new Browser();
if( $browser->isBrowser("Chrome") ||
	($browser->isBrowser("Internet Explorer") && $browser->getVersion() >= 11)
  )
{
  define("BROWSER_OK", true);
}
else
{
  define("BROWSER_OK", false);
}


/*******************************************
 ** Colores del sistema.
 *******************************************/

$bgcolor1 = "#BCD4E6";		// Primer color de fondo
$bgcolor2 = "#D5D5D5";		// Segundo color de fondo
$bgcolor3 = "#B7CEC4";		// Tercer color de fondo
$bgcolor  = "#BCD4E6";


$bgcolor_out  = "#F2EFFB"; // Color de fondo (onmouseout)
$bgcolor_over = "#B9CCF4"; // Color de fondo (onmouseover)
$text_color_out  = "#000000";
$text_color_over = "#000000";




// atributo de los tr de los listados
$atrib_tr="bgcolor=$bgcolor_out onmouseover=\"this.style.backgroundColor = '$bgcolor_over'; this.style.color = '$text_color_over'\" onmouseout=\"this.style.backgroundColor = '$bgcolor_out'; this.style.color = '$text_color_out'\"";

/*******************************************
 ** Cantidad de items a mostrar por página.
 *******************************************/

$itemspp = 50;

/*******************************************
 ** Configuración de la base de datos.
 *******************************************/

$db_type = 'postgres8';				// Tipo de base de datos.
$db_host = 'localhost';		// Host para desarrollo.
$db_user = 'projekt';				// Usuario.
$db_password = 'propcp';			// Contraseña.
$db_name = 'nacer';


// IPs permitidas para conectarse al gestion, si el ip no esta aca, se envia un mail
$ip_permitidas = array(
	"127.0.0.1/32" => "Localhost",
);

$ADODB_CACHE_DIR = LIB_DIR."/adodb/cache";		// Directorio para cache de consultas
// Arreglo que contiene los nombres de los esquemas en la
// base de datos para poder acceder a las tablas sin tener
// que usar en nombre del esquema.
$db_schemas = array(	
	"general",
	"mensajes",
	"permisos",
	"sistema",			
	"facturacion",	
	"calidad",	
    "nacer",
    "planillas",
	"personal",
	"contabilidad",
	"uad",	
	"tareas_divisionsoft"

);
$db_debug = FALSE;					// Debugger de las consultas.

/*******************************************
 ** Limite de tiempo de inactividad para la
 ** expiración de la sesión (en minutos).
 *******************************************/

 $session_timeout = 90;

/*******************************************
 ** Variable $html_root que contiene la ruta
 ** a la raíz de la página.
 ** (Ruta relativa al URL de la página)
 *******************************************/
//if (ereg("(/modulos)|(/lib)|(/index.php)|(/menu.php)|(/menu_xml.php)|(/aviso.php)",$_SERVER["SCRIPT_NAME"],$tmp)) {
//	$tmp=explode($tmp[1].$tmp[2].$tmp[3].$tmp[5].$tmp[6],$_SERVER["SCRIPT_NAME"]);
//	$html_root = $tmp[0];
//}
//unset($tmp);

if (ereg("(/modulos)|(/lib)|(/index.php)|(/menu_para_ayuda.php)|(/menu_xml.php)|(/aviso.php)",$_SERVER["SCRIPT_NAME"],$tmp)) {
	$tmp=explode($tmp[1].$tmp[2].$tmp[3].$tmp[4].$tmp[5].$tmp[6],$_SERVER["SCRIPT_NAME"]);
	$html_root = $tmp[0];
}
unset($tmp);



/*******************************************
 ** Variable $html_footer contiene el
 ** pie de la página.
 *******************************************/

$html_footer = "
  </body>
</html>
";


/*******************************************
 ** Libreria principal del sistema.
 *******************************************/


require LIB_DIR."/lib.php";

/*******************************************
 ** Variable $html_header contiene el
 ** encabezamiento de la página.
 *******************************************/
 
if ($_ses_cambiar_perfil_usuario == 1) 
   $actualizar_menu_perfil_usuario='false';
else $actualizar_menu_perfil_usuario='true';

$html_header = "
<html>
  <head>
    <!--<meta http-equiv='X-UA-Compatible' content='IE=edge'>-->
	<link rel='icon' href='".((($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER['SERVER_NAME'])."$html_root/favicon.ico'>
	<link REL='SHORTCUT ICON' HREF='".((($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER['SERVER_NAME'])."$html_root/favicon.ico'>
	<link rel='stylesheet' type='text/css' href='$html_root/lib/bootstrap-3.3.1/css/bootstrap.min.css'>
	<link rel='stylesheet' type='text/css' href='$html_root/lib/bootstrap-3.3.1/css/bootstrap-theme.min.css'>
  <link rel='stylesheet' type='text/css' href='$html_root/lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css'>
	<link rel='stylesheet' type='text/css' href='$html_root/lib/bootstrap-dialog/bootstrap-dialog.min.css'>
	<link rel='stylesheet' type='text/css' href='$html_root/lib/fullcalendar/fullcalendar.min.css'>
	<link rel='stylesheet' type='text/css' href='$html_root/lib/jquery/jquery-ui.min.css'>
	<link rel='stylesheet' type='text/css' href='$html_root/lib/estilos.css'>
	<link rel='stylesheet' type='text/css' href='$html_root/lib/estilos_bootstrap.css'>
    <script type='text/javascript' src='$html_root/lib/jquery/jquery-1.11.1.min.js'></script>
    <script type='text/javascript' src='$html_root/lib/jquery/jquery-ui.min.js'></script>
    <script type='text/javascript' src='$html_root/lib/moment/moment.es.min.js'></script>
    <script type='text/javascript' src='$html_root/lib/bootstrap-3.3.1/js/bootstrap.min.js'></script>
    <script type='text/javascript' src='$html_root/lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js'></script>
    <script type='text/javascript' src='$html_root/lib/bootstrap-dialog/bootstrap-dialog.min.js'></script>
    <script type='text/javascript' src='$html_root/lib/fullcalendar/fullcalendar.es.min.js'></script>
    <script type='text/javascript' src='$html_root/lib/notifyjs/notify-combined.min.js'></script>
    <script type='text/javascript' src='$html_root/lib/funciones.js'></script>
    <script type='text/javascript'>
		var winW=window.screen.Width;
		var valor=(winW*25)/100;
		var nombre1;
		var titulo1;
		function insertar() {
			ventana.document.all.titulo.innerText=titulo1;
			ventana.frames.frame1.location=nombre1;
		}
		function abrir_ventana(nombre,titulo) {
			var winH=window.screen.availHeight;
			nombre1=nombre;
			titulo1=titulo;
			if ((typeof(ventana) == 'undefined') || ventana.closed) {
				ventana=window.open('$html_root/modulos/ayuda/titulos.htm','ventana_ayuda','width=' + valor + ',height=' + (winH)+ ', left=' + (winW - valor ) +'  ,top=0, scrollbars=0 ');
				window.top.resizeBy(-valor,0);
			}
			else { ventana.focus(); }
			setTimeout('insertar()',400);
		}
		function check_fix_size() {
			if (typeof(fix_size) == 'function') fix_size();
			    if(parent && parent.oPath && $actualizar_menu_perfil_usuario) parent.oPath.updateLink();//actualiza el link del menu actual
			   
		}
		$( document ).ready(function() {
  			$('html').on('click', function () {
				parent.$('#frame2').trigger('click');
			});
			check_fix_size();
			$(document).ajaxSend(function(event, request, settings) {
  				$('#loading-indicator').show();
			});

			$(document).ajaxComplete(function(event, request, settings) {
				$('#loading-indicator').hide();
			});
		});
	</script>
  </head>
 <body onresize='check_fix_size();'>
 <div class='col-md-12 text-center'><img src='/imagenes/cargando.gif' id='loading-indicator' style='display:none' /></div>
 ";

?>