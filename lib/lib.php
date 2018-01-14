<?php


require_once(LIB_DIR."/adodb/adodb.inc.php");
require_once(LIB_DIR."/adodb/adodb-pager.inc.php");
require_once(LIB_DIR."/class.phpmailer.php");

// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

define("TIEMPO_INICIO", getmicrotime());
// Chequea la version del sistema operativo en el que se esta
// ejecutando la pagina y define la constante SERVER_OS
if (ereg("Win32",$_SERVER["SERVER_SOFTWARE"]) ||
    ereg("Microsoft",$_SERVER["SERVER_SOFTWARE"]))
	define("SERVER_OS", "windows");
else
	define("SERVER_OS", "linux");

$db = &ADONewConnection($db_type) or die("Error al conectar a la base de datos");
$db->Connect($db_host, $db_user, $db_password, $db_name);
$db->cacheSecs = 3600;
$result=$db->Execute("SET search_path=".join(",",$db_schemas)) or die($db->ErrorMsg());
unset($result);
$db->debug = $db_debug;

// load phpSecureSite
require(LIB_DIR."/phpss/phpss.php");

// libreria para administrar los permisos
require_once LIB_DIR."/gacl.class.php";
$permisos = new gacl(array("items_per_page"=>500,"max_search_return_items"=>500));

/***********************************
 ** Funciones de ambito general
 ***********************************/

function form_login() {
	global $html_root;
	echo "<html><head><script language=javascript>\n";
	echo "if(parent!=null && parent!=self) { parent.location='$html_root/login.php'; }\n";
	echo "else { window.location='$html_root/login.php'; }\n";
	echo "</script></head></html>";
}

function getmicrotime() {
	list($useg, $seg) = explode(" ",microtime());
	return ((float)$useg + (float)$seg);
}
// Funcion que devuelve el tiempo que se demora en generarse la pagina
function tiempo_de_carga () {
	$tiempo_fin = getmicrotime();
	$tiempo = sprintf('%.4f', $tiempo_fin - TIEMPO_INICIO);
	return $tiempo;
}
// Funcion que verifica el estado de la sesión
function Autenticar() {
	$status = phpss_auth();

	switch($status) {
		case PHPSS_AUTH_ALLOW:
			 break;
		case PHPSS_AUTH_NOCOOKIE:
//			   Error("Necesita iniciar sesión para poder ver esta página");
//			 include_once(ROOT_DIR."/login.php");
			 form_login();
			 exit;
			 break;
		case PHPSS_AUTH_INVKEY:
			 phpss_logout();
//			 Error("Usted está usando una sesión no válida");
//			 include_once(ROOT_DIR."/login.php");
			 form_login();
			 exit;
			 break;
		case PHPSS_AUTH_IPACCESS_DENY:
//			 Error("Usted no tiene permitido el acceso desde su dirección IP");
//			 include_once(ROOT_DIR."/login.php");
			 form_login();
			 exit;
			 break;
		case PHPSS_AUTH_ACLDENY:
			 Error("Usted no tiene permiso para ver esta página");
			 exit;
			 break;
		case PHPSS_AUTH_HIJACK:
//			 Error("Su dirección IP es diferente a la que uso para iniciar sesión");
//			 include_once(ROOT_DIR."/login.php");
			 form_login();
			 exit;
			 break;
		case PHPSS_AUTH_TIMEOUT:
//			 if ($parametros["mode"] != "logout") {
//				 Error("Su sesión ha expirado, por favor vuelva a iniciar sesión");
//			 }
//			 include_once(ROOT_DIR."/login.php");
			 form_login();
			 exit;
			 break;
	}

}

function mix_string($string) {
	$split = 4;    // mezclar cada $split caracteres
	$str = str_replace("=","",$string);
	$string = "";
	$str_tmp = explode(":",chunk_split($str,$split,":"));
	for ($i=0;$i<count($str_tmp);$i+=2) {
		 if (strlen($str_tmp[$i+1]) != $split) {
			 $string .= $str_tmp[$i] . $str_tmp[$i+1];
		 }
         else {
               $string .= $str_tmp[$i+1] . $str_tmp[$i];
		 }
    }
	return str_replace(" ","+",$string);
}
function encode_link() {
	$args = func_num_args();
	if ($args == 2) {
		$link = func_get_arg(0);
		$p = func_get_arg(1);
	}
	elseif ($args == 1) {
		$p = func_get_arg(0);
	}
	$str = comprimir_variable($p);
	$string = mix_string($str);
	if(isset($link))
		return $link."?p=".$string;
	else
		return $string;
}
function decode_link($link) {
    $str = mix_string($link);
	$cant = strlen($str)%4;
    if ($cant > 0) $cant = 4 - $cant;
    for ($i=0;$i < $cant;$i++) {
		 $str .= "=";
    }
    return descomprimir_variable($str);
}
/* Funcion para cambiar el tipo de arreglo
   que retorna la consulta a la base de datos
   El paramentro puede ser "a" para que retorne
	un arreglo asociativo con los nombres de las
   columnas como indices, y "n" para que retorne
   un arreglo con los indices de forma de numeros
*/
function db_tipo_res($tipo="d") {
	global $db;
	switch ($tipo) {
	   case "a":   // tipo asociativo
		   $db->SetFetchMode(ADODB_FETCH_ASSOC);
		   break;
	   case "n":   // tipo numerico
		   $db->SetFetchMode(ADODB_FETCH_NUM);
		   break;
	   case "d":
		   $db->SetFetchMode(ADODB_FETCH_BOTH);
		   break;
   }
}

/*
 * Funcion para cambiar un color por otro alternativo
 * cuando los colores son parecidos o no contrastan mucho.
 * los parametros son de la forma: #ffffff
*/
function contraste($fondo, $frente, $reemplazo) {
	$brillo = 125;
   $diferencia = 400;
	$bg = ereg_replace("#","",$fondo);
	$fg = ereg_replace("#","",$frente);
	$bg_r = hexdec(substr($bg,0,2));
	$bg_g = hexdec(substr($bg,2,2));
	$bg_b = hexdec(substr($bg,4,2));
	$fg_r = hexdec(substr($fg,0,2));
	$fg_g = hexdec(substr($fg,2,2));
	$fg_b = hexdec(substr($fg,4,2));
	$bri_bg = (($bg_r * 299) + ($bg_g * 587) + ($bg_b * 114)) / 1000;
	$bri_fg = (($fg_r * 299) + ($fg_g * 587) + ($fg_b * 114)) / 1000;
	$dif = max(($fg_r - $bg_r),($bg_r - $bg_r)) + max(($fg_g - $bg_g),($bg_g - $fg_g)) + max(($fg_b - $bg_b),($bg_b - $fg_b));
	if(intval($bri_bg - $bri_fg) > $brillo or $dif > $diferencia) {
   	return $frente;
   }
   else {
   	return $reemplazo;
   }
}
/*
 * @return array
 * @param sql string
 * @param orden array
 * @param filtro array
 * @param link_pagina string
 * @param where_extra string (opcional)
 * @desc Esta funcion genera el formulario de busqueda y divide el resultado
         de una consulta sql por paginas
         Ejemplo:
		 // variables que contienen los datos actuales de la busqueda
         $page = $_GET["page"] or $page = 0;                                                                //pagina actual
				 $filter = $_POST["filter"] or $filter = $_GET["filter"];                //campo por el que se esta filtrando
				 $keyword = $_POST["keyword"] or $keyword = $_GET["keyword"];        //palabra clave

                 $orden = array(                                        //campos que voy a mostar
                        "default" => "2",                                //campo por defecto
                        "1" => "IdProv",
                        "2" => "Proveedor"
                 );

                 $filtro = array(
						"Proveedor"                => "Proveedor",                //elementos en donde se van a hacer las busquedas
                        "Contacto"                => "Contacto",                //el formato del aarreglo es:
                        "Mail"                        => "Mail"                        //$filtro=array("nombre de la columna en la base de datos" => "nombre a mostrar en el formulario");
                 );
                 //sentencia sql que sin ninguna condicion
				 $sql_tmp = "SELECT IdProv,Proveedor,Contacto,Mail,Teléfono,Comentarios FROM bancos.proveedores";
				 //prefijo para los links de paginas siguiente y anterior
                 $link_tmp = "<a id=ma href='bancos.php?mode=$mode&cmd=$cmd";
                 //condiciones extras de la consulta
				 $where_tmp = "";

				 list($sql,$total_Prov,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp);

*/
function form_busqueda($sql,$orden,$filtro,$link_pagina,$where_extra="",$contar=0,$sumas="",$ignorar="",$seleccion="") {

		global $bgcolor2,$page,$filter,$keyword,$sort,$up;
		global $itemspp,$parametros,$mostrar_form_busqueda;
		if ($_GET['page'])
			$page=($_GET['page'] > 0)?$_GET['page']-1:0;//controlo que no pongan valores negativos

		if ($up == "") {
			$up = $orden["default_up"];
		}
		if ($up == "") {
			$up = "1";
		}
		if ($up == "0") {
//				$up = $parametros["up"];
				$direction="DESC";
				$up2 = "1";
		}
		else {
				$up = "1";
				$direction = "ASC";
				$up2 = "0";
		}
		if ($sort == "") $sort = "default";
		if ($sort == "default") { $sort = $orden["default"]; }
		if ($orden[$sort] == "") { $sort = $orden["default"]; }
		if ($filtro[$filter] == "") { $filter = "all"; }
		//$tmp=es_numero($keyword);
		if (!isset($mostrar_form_busqueda) || $mostrar_form_busqueda !== false) {
			echo "<input type=hidden name=form_busqueda value=1>";
			echo "<b>Buscar:&nbsp;</b><input type='text' name='keyword' value='$keyword' size=20 maxlength=150>\n";
			echo "<b>&nbsp;en:&nbsp;</b><select name='filter'>&nbsp;\n";
			echo "<option value='all'";
			if (!$filter or $filtro[$filter] == "") echo " selected";
			echo ">Todos los campos\n";
			while (list($key, $val) = each($filtro)) {
					echo "<option value='$key'";
					if ($filter == "$key") echo " selected";
					echo ">$val\n";
			}
			echo "</select>\n";
		}
		//print_r($ignore);


		if ($keyword) {

				$where = "\nWHERE ";
				if ($filter == "all" or !$filter) {
						$where_arr = array();
						if (is_array($ignorar)) $where .= "((";
						else $where .= "(";
						reset($filtro);
						while (list($key, $val) = each($filtro)) {
							    if (is_array($ignorar) && !in_array($key,$ignorar))
							     $where_arr[] = "$key ILIKE '%$keyword%'";
							    if (!is_array($ignorar)) $where_arr[] = "$key ILIKE '%$keyword%'";

						}

						$where .= implode(" OR ", $where_arr);
						$where .= ")";

						if (is_array($seleccion)){
						while (list($key, $val) = each($seleccion)) {
						$where .= " OR ($val)";
						}
						$where .= ")";
						}
				}
				else {if (!is_array($ignorar)) $where .= "$filter ILIKE '%$keyword%'";
					  elseif (is_array($ignorar) && !in_array($filter,$ignorar))
						$where .= "$filter ILIKE '%$keyword%'";
						else $where .= " (".$seleccion[$filter].")";
				}
		}

		$sql .= " $where";
		if ($where_extra != "") {
				if ($where != "")
				{
					 //si no tiene un group by al principio
					 if (!eregi("^group by.*|^ group by.*",$where_extra))
						 $sql .= "\nAND";

				}
				else
				{
					 //si no tiene un group by al principio
					 if (!eregi("^group by.*|^ group by.*",$where_extra))
						 $sql .= "\nWHERE";

				}
				$sql .= " $where_extra";
		}
        //echo $sumas." AAAAAAAAAAAAAAAA<br>";
		if ("$contar"=="buscar") {
			$tipo_res = db_tipo_res("a");
//			$result = sql($sql,"CONTAR") or reportar_error($sql,__FILE__,__LINE__);
			$result = sql($sql,"CONTAR") or fin_pagina();
			$tipo_res = db_tipo_res();
			$total = $result->RecordCount();

			//Sumas de campos de montos caso en que usa la consulta general
			$res_sumas='';

			if (	$sumas!='' &&
					substr_count($sql,$sumas["campo"])>0 &&//si el campo esta definido
					is_array($sumas["mask"])//mascara para configurar el resultado
					) {
						$count_mask = count($sumas["mask"]);//tamaño de la mascara
						if ($count_mask==0) {//caso en que suma solo cantidades
							$acum=0;
							for($i=0;$i<$total;$i++){//for
								$acum+=$result->fields[$sumas["campo"]];
								$result->MoveNext();
							}	//fin de for
							$res_sumas ="$acum";
						}//fin de caso suma cantidades solam.
						elseif(substr_count($sql,$sumas["moneda"])>0) {//otro caso //si la moneda esta definida
							$sql_moneda="Select simbolo,id_moneda from moneda";
							$res_moneda=sql($sql_moneda,"Imposible obtener el listado de moneda") or fin_pagina();
							for($i;$i<$res_moneda->RecordCount();$i++){
								$moneda[$res_moneda->fields["id_moneda"]]=$res_moneda->fields["simbolo"];
								$res_moneda->MoveNext();
							}
								//print_r($moneda);
							for($i=0;$i<$count_mask;$i++) {//preparando el acumulador
								$acum[$i]=0;
							}//fin del for

							for($i=0;$i<$total;$i++){//for
								$pos = array_search($moneda[$result->fields[$sumas["moneda"]]],$sumas["mask"]);
								if (is_int($pos))
									$acum[$pos]+=$result->fields[$sumas["campo"]];
								$result->MoveNext();
							}	//fin de for
							$res_sumas = "";
							for($i=0;$i<$count_mask;$i++) { //preparando el resultado
								$res_sumas.=$sumas["mask"][$i].formato_money($acum[$i])." ";
							}//fin del for

						}//fin otro caso

					}
		}
		elseif($contar)
		{
//		$sql_cont = eregi_replace("^SELECT(.*)FROM", "SELECT COUNT(*) AS total FROM", $sql);
//		$sql_cont = eregi_replace("GROUP BY .*", "", $sql_cont);
			$tipo_res = db_tipo_res("n");
//		$result = $db->Execute($sql_cont) or die($db->ErrorMsg());
			$result = sql($contar,"CONTAR") or fin_pagina();
//		$total = $result->fields[0];
			$tipo_res = db_tipo_res();
			$total = $result->fields[0];


			if (is_string($sumas) && $sumas!="") {
				$tipo_res = db_tipo_res("n");
				$result = sql($sumas,"SUMAS") or fin_pagina();
				$tipo_res = db_tipo_res();
				$res_sumas="";
				for ($i=0;$i<$result->RecordCount();$i++){
					$res_sumas.=$result->fields[0]." ".formato_money($result->fields[1])." ";
					$result->MoveNext();
				}

			}
		}
		else {
			$total = 0;
			$res_sumas="";
		}

// $total=99;
		if ($sort != "" && isset($orden[$sort])) {
		    $sql .= "\nORDER BY ".$orden[$sort]." $direction";
		}

		$sql .= "\nLIMIT $itemspp OFFSET ".($page * $itemspp);

		$page_n = $page + 1;
		$page_p = $page - 1;
		$link_pagina_p = "";
		$link_pagina_n = "";
		if (!is_array($link_pagina)) $link_pagina = array();
//		$link_pagina["sort"] = $sort;
//		$link_pagina["up"] = $up;
//		$link_pagina["keyword"] = $keyword;
//		$link_pagina["filter"] = $filter;
		if ($page > 0) {
			$link_pagina["page"] = $page_p;
			$link_pagina_p = "<a title='Página anterior' href='".encode_link($_SERVER["SCRIPT_NAME"],$link_pagina)."'><<</a>";
		}
		$sum=0;
		if (($total % $itemspp)>0) $sum=1;

		$last_page=(intval($total/$itemspp)+$sum);
		$link_pagina_num = "&nbsp;&nbsp;Página&nbsp;<input type='text' value=".($page+1)." name='page' size=2 style='text-align:right;border:none' onkeypress=\" if ((show_alert=(window.event.keyCode==13)) && parseInt(this.value)>0 && parseInt(this.value)<= $last_page ) {location.href='".encode_link($_SERVER["SCRIPT_NAME"],$link_pagina). "&page='+parseInt(this.value);return false;} else if (show_alert) {alert('Por favor ingrese un número válido'); return false;} \" />&nbsp;de&nbsp;$last_page&nbsp;&nbsp;";
		if ($total > $page_n*$itemspp) {
			$link_pagina["page"] = $page_n;
			$link_pagina_n = "<a title='Página siguiente' href='".encode_link($_SERVER["SCRIPT_NAME"],$link_pagina)."'>>></a>";
		}
		if ($total > 0 and $total > $itemspp) {
			$link_pagina_ret = $link_pagina_p.$link_pagina_num.$link_pagina_n;
		}
		else {
			$link_pagina_ret = "";
		}

		return array($sql,$total,$link_pagina_ret,$up2,$res_sumas);
}

function Error($msg,$num="") {
	global $error;
	echo "<center><font size=4 color=#FF0000>Error $num: $msg</font><br></center>\n";
	$error = 1;
}

function link_calendario($control_pos) {
	global $html_root;
	return "<img src=$html_root/imagenes/cal.gif border=0 align=middle style='cursor:hand;' alt='Haga click aqui para\nseleccionar la fecha'  onClick=\"javascript:popUpCalendar(this, $control_pos, 'dd/mm/yyyy');\">";
}

function Aviso($msg) {
	echo "<br><center><font size=4><b>$msg</b></font></center><br>\n";
}

/**
 * @return string
 * @param fecha_db string
 * @desc Convierte una fecha de la forma AAAA-MM-DD
 *       a la forma DD/MM/AAAA
 */
function Fecha($fecha_db) {
		$m = substr($fecha_db,5,2);
		$d = substr($fecha_db,8,2);
		$a = substr($fecha_db,0,4);
		if (is_numeric($d) && is_numeric($m) && is_numeric($a)) {
				return "$d/$m/$a";
		}
		else {
				return "";
		}
}
//funcion que devuelve la diferencia en dias entre dos fechas
//hay que pasar las fechas a la funcion en la forma dd/mm/aaaa
function restaFechas($dFecIni, $dFecFin)
{
    $dFecIni = str_replace("/","",$dFecIni);
    $dFecFin = str_replace("/","",$dFecFin);

    ereg( "([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecIni, $aFecIni);
    ereg( "([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecFin, $aFecFin);

    $date1 = mktime(0,0,0,$aFecIni[2], $aFecIni[1], $aFecIni[3]);
    $date2 = mktime(0,0,0,$aFecFin[2], $aFecFin[1], $aFecFin[3]);

    return round(($date2 - $date1) / (60 * 60 * 24));
}

function hora_ok($hora) {
    if ($hora) {
         $hora_arr = explode(":", $hora);
         if ( (is_numeric($hora_arr[0])) && ($hora_arr[0]>=0 && $hora_arr[0]<=23))
             $hora_apertura = $hora_arr[0];
         else
             return 0;
         if ( (is_numeric($hora_arr[1]))  && ($hora_arr[1]>=0 && $hora_arr[1]<=59) )
            $hora_apertura .= ":".$hora_arr[1];
        else
            return 0;
        if ( (is_numeric($hora_arr[2]))  && ($hora_arr[2]>=0 && $hora_arr[2]<=59))
            $hora_apertura .= ":".$hora_arr[2];
        else
           return 0;
    }

return $hora_apertura;

}


function Hora($hora_db) {
	if (ereg("([0-9]{2}:[0-9]{2}:[0-9]{2})",$hora_db,$hora))
		return $hora[0];
	else
		return "00:00:00";
}



/**
 * @return string
 * @param fecha string
 * @desc Convierte una fecha de la forma DD/MM/AAAA
 *       a la forma AAAA-MM-DD
 */

//funcion defectuosa
//cuidado
function Fecha_db($fecha) {
		if (strstr($fecha,"/"))
			list($d,$m,$a) = explode("/",$fecha);
		elseif (strstr($fecha,"-"))
			list($d,$m,$a) = explode("-",$fecha);
		else
			return "";
		return "$a-$m-$d";
}




/**
 * @return 1 o 0
 * @param fecha date
 * @desc Devuelve 1 si es fecha y 0 si no lo es.
 */
function FechaOk($fecha) {
	if (ereg("-",$fecha))
		list($dia,$mes,$anio)=split("-", $fecha);
	elseif (ereg("/",$fecha))
		list($dia,$mes,$anio)=split("/", $fecha);
	else
		return 0;
	return checkdate($mes,$dia,$anio);
}

/**
 * @return date
 * @param fecha date
 * @desc Convierte una fecha del formato dd-mm-aaaa al
 *       formato aaaa-mm-dd que usa la base de datos.
 */
function ConvFecha($fecha) {
	list($dia,$mes,$anio)=split("-", $fecha);
	return "$anio-$mes-$dia";
}

/**
 * @return int
 * @param fecha date
 * @desc Compara la fecha $fecha con la fecha actual.
 *       Retorna:
 *               0 si $fecha es mayor de 7 dias.
 *               1 si $fecha esta entre 0 y 7 dias.
 *               2 si $fecha es anterior a la fecha actual.
 */
function check_fecha($fecha) {
	$fecha2=strtotime($fecha);
	$num1=($fecha2-intval(time()))/60/60/24;
//    $res=0;
	if ($num1 > 7) {
	   $res=0;
    } elseif ($num1>=0 and $num1<=7) {
       $res=1;
    } else {
	   $res=2;
    }
	return($res);
}
// Manejo de div flotantes
/**
 * @Nombre inicio_barra
 * @param nombre String
 * @param titulo String
 * @param contenido String
 * @param color String
 * @param top integer
 * @param left integer
 * @param height integer
 * @param width integer
 * @param ocultar integer 0 o 1
 * @desc Inserta un div flotante
 *		 Si el top y left no son insertado,
 *		 El div flotante estara en la posicion
 *		 inferior central.
 **/
function inicio_barra($nombre,$titulo,$contenido,$height,$width,$top=null,$left=null,$color="#B7C7D0",$ocultar=1) {
	$he=$height-18;
	echo "<style type='text/css'>
		<!--
		#$nombre	{position: absolute;overflow: hidden; width: $width; height: $height;
			border: 2 outset black; margin: 5px;}
		#title		{background: #006699;padding: 0px; margin: 0px;}
		#inner		{background: $color;border: 2 inset white;overflow: auto; margin: 0px;width: 100%; height: $he;}
		-->
	</style>\n";


	echo "<div id='$nombre'>\n";
	echo "<div class='handle' handlefor='$nombre' id='title'>\n";
	echo "<table width=100% cellspacing=0 cellpadding=0 border=0>\n";
	echo "<tr>\n";
	echo "<td align=center width=90%>\n";
	echo "<font size=2 color='#cdcdcd'><b>$titulo</b></font>\n";
	echo "</td>\n";
	echo "<td align=right width=30%>\n";
	if ($ocultar==1) {
		echo "<img style='cursor: hand;' src='../../imagenes/dropdown2.gif' onClick='ocultar(this,\"$nombre\");'>\n";
		echo "<img style='cursor: hand;' src='../../imagenes/salir.gif' onClick='mini(this,\"$nombre\");'>\n";
	}
	echo "</td></tr></table></div>\n";
	echo "<div id='inner'";
	if ($color) echo " bgcolor=$color";
	echo ">\n";
	echo $contenido;
	echo "</div></div>\n";
	echo "<script>\n";
	//echo "$nombre.style.width=$width;\n";
	//echo "$nombre.style.height=$height;\n";
	if ($top==""){
		echo "$nombre.style.top=(document.body.clientHeight-$height)-5;\n";
		echo "$nombre.top=(document.body.clientHeight)-((document.body.clientHeight-$height)-5);\n";
	}
	else {
		echo "$nombre.style.top=$top;\n";
		echo "$nombre.top=(document.body.clientHeight-$top);\n";
	}
	if ($left=="")
		echo "$nombre.style.left=((document.body.clientWidth/2)-($width/2));\n";
	else
		echo "$nombre.style.left=$left;\n";
	//echo "alert($nombre.style.top);\n";
	echo "</script>\n";
}
// Fin de div flotantes
function html_out($outstr){
  $string=$outstr;
  if ($string <> "") {
	$string=ereg_replace("\"","&#34;",$string);
	$string=ereg_replace("'","&#39;",$string);
	$string=ereg_replace(">","&#62;",$string);
	$string=ereg_replace("<","&#60;",$string);
	$string=ereg_replace("\n","<br>",$string);
  }
  return $string;
}

// the same specialy for hidden form fields and select field option values (uev -> UrlEncodedValues)
//function uev_out($outstr){return ereg_replace("'","&#39;",htmlspecialchars(urlencode($outstr)));}


function atrib_tr($bgcolor_out_int='#E2E9F0'){
  global $bgcolor_over, $text_color_over, $text_color_out ;
  return "bgcolor=$bgcolor_out_int onmouseover=\"this.style.backgroundColor = '$bgcolor_over'; this.style.color = '$text_color_over'\" onmouseout=\"this.style.backgroundColor = '$bgcolor_out_int'; this.style.color = '$text_color_out'\"; style='cursor: hand; height:35px;'";
         }
function atrib_tr1($bgcolor_out_int='#F3F781'){
  global $bgcolor_over, $text_color_over, $text_color_out ;
  return "bgcolor=$bgcolor_out_int onmouseover=\"this.style.backgroundColor = '$bgcolor_over'; this.style.color = '$text_color_over'\" onmouseout=\"this.style.backgroundColor = '$bgcolor_out_int'; this.style.color = '$text_color_out'\"; style='cursor: hand; height:35px;'";
         }
         
function atrib_tr2($bgcolor_out_int='#F78181'){
  global $bgcolor_over, $text_color_over, $text_color_out ;
  return "bgcolor=$bgcolor_out_int onmouseover=\"this.style.backgroundColor = '$bgcolor_over'; this.style.color = '$text_color_over'\" onmouseout=\"this.style.backgroundColor = '$bgcolor_out_int'; this.style.color = '$text_color_out'\"; style='cursor: hand; height:35px;'";
         }
		 
function atrib_tr3($bgcolor_out_int='D7D5FA'){
  global $bgcolor_over, $text_color_over, $text_color_out ;
  return "bgcolor=$bgcolor_out_int onmouseover=\"this.style.backgroundColor = '$bgcolor_over'; this.style.color = '$text_color_over'\" onmouseout=\"this.style.backgroundColor = '$bgcolor_out_int'; this.style.color = '$text_color_out'\"; style='cursor: hand; height:35px;'";
         }
		 
function atrib_tr4($bgcolor_out_int='F5A1F1'){
  global $bgcolor_over, $text_color_over, $text_color_out ;
  return "bgcolor=$bgcolor_out_int onmouseover=\"this.style.backgroundColor = '$bgcolor_over'; this.style.color = '$text_color_over'\" onmouseout=\"this.style.backgroundColor = '$bgcolor_out_int'; this.style.color = '$text_color_out'\"; style='cursor: hand; height:35px;'";
         }
		 
function atrib_tr5($bgcolor_out_int='FC5D4F'){
  global $bgcolor_over, $text_color_over, $text_color_out ;
  return "bgcolor=$bgcolor_out_int onmouseover=\"this.style.backgroundColor = '$bgcolor_over'; this.style.color = '$text_color_over'\" onmouseout=\"this.style.backgroundColor = '$bgcolor_out_int'; this.style.color = '$text_color_out'\"; style='cursor: hand; height:35px;'";
         }
         
function atrib_tr6($bgcolor_out_int='#81BEF7'){
  global $bgcolor_over, $text_color_over, $text_color_out ;
  return "bgcolor=$bgcolor_out_int onmouseover=\"this.style.backgroundColor = '$bgcolor_over'; this.style.color = '$text_color_over'\" onmouseout=\"this.style.backgroundColor = '$bgcolor_out_int'; this.style.color = '$text_color_out'\"; style='cursor: hand; height:35px;'";
         }
         
function atrib_tr7($bgcolor_out_int='#F5D0A9'){
  global $bgcolor_over, $text_color_over, $text_color_out ;
  return "bgcolor=$bgcolor_out_int onmouseover=\"this.style.backgroundColor = '$bgcolor_over'; this.style.color = '$text_color_over'\" onmouseout=\"this.style.backgroundColor = '$bgcolor_out_int'; this.style.color = '$text_color_out'\"; style='cursor: hand; height:35px;'";
         }
		 
function atrib_tr8($bgcolor_out_int='#00FF00'){
  global $bgcolor_over, $text_color_over, $text_color_out ;
  return "bgcolor=$bgcolor_out_int onmouseover=\"this.style.backgroundColor = '$bgcolor_over'; this.style.color = '$text_color_over'\" onmouseout=\"this.style.backgroundColor = '$bgcolor_out_int'; this.style.color = '$text_color_out'\"; style='cursor: hand; height:35px;'";
         }
function tr_tag ($dblclick,$extra="",$bgcolor_out_int='#B7C7D0') {
  global $atrib_tr, $bgcolor_out, $cnr, $bgcolor1, $bgcolor2;
  if (($cnr/2) == round($cnr/2)) { $color = "$bgcolor1"; $cnr++;}
  else { $color = "$bgcolor2"; $cnr++; }
  if (!(strpos($dblclick,"target" )===false))
  {

	$t1=substr($dblclick,strpos($dblclick,"=")+1);
	$target=substr($t1,0,strpos($t1,";")).".";
	//; separa el target de la URL
	$dblclick=substr($dblclick,strpos($dblclick,";")+1);
  }
  $tr_hover_on = atrib_tr($bgcolor_out_int)." onClick=\"$target"."location.href ='$dblclick'\"";
  echo "<tr $tr_hover_on $extra>\n";
}


function formato_money($num) {
	return number_format($num, 2, ',', '.');
}

function es_numero(&$num) {
	if (strstr($num,",")) {
		$num = ereg_replace("\.","",$num);
		$num = ereg_replace(",",".",$num);
	}
	return is_numeric($num);
}

function cargar_feriados() {
	global $_ses_feriados;
	$ret = "";
	foreach ($_ses_feriados as $fecha => $descripciones) {
		list($anio,$mes,$dia) = split("-",$fecha);
		foreach ($descripciones as $descripcion) {
			$ret .= "addHoliday($dia,$mes,$anio,'$descripcion');\n";
		}
	}
	return $ret;
}

function cargar_calendario() {
	global $html_root;	
	echo "<script language='javascript' src='$html_root/lib/popcalendar.js'></script>\n";
}

function mkdirs($strPath, $mode = "0700") {
//	global $server_os;
	if (SERVER_OS == "windows") {
		$strPath = ereg_replace("/","\\",$strPath);
	}
	if (is_dir($strPath)) return true;
	$pStrPath = dirname($strPath);
	if (!mkdirs($pStrPath, $mode)) return false;
	return mkdir($strPath);
}

function verificar_permisos() {
	global $html_root,$bgcolor3,$ouser,$parametros;
	if (ereg("/modulos",$_SERVER["SCRIPT_NAME"])) {
		$tmp = explode("/modulos/",$_SERVER["SCRIPT_NAME"]);
		list($modulo,$pagina) = explode("/",$tmp[1],2);
		$pagina=ereg_replace("\.php","",$pagina);
		$padre = $modulo;
//		echo "<br>tmp:".print_r($tmp);
//		echo "<br>padre:$padre ------- pagina:$pagina<br>";
//		echo "parametros:";print_r($parametros);echo("<br>");
		$i=0;
		while( $i < $ouser->permisos->length) {
			$keyname=$ouser->permisos[$i]->name;
			//si es un item con parametros
			if (ereg("(.*)(\?)(.*)",$keyname,$amenu))
			{
				//si NO es la pagina a checkear permisos
				if ($pagina != $amenu[1])
					$amenu[0]="";
				else
				{
					$menu=$amenu[1].".php";
					$extra2=split("&",$amenu[3]);
					foreach ($extra2 as $key => $value)
					{
						$tmp=split("=",$value,2);
						//si NO vienen los parametros requeridos del item
						if($_GET[$tmp[0]] != $tmp[1])
						{	
							$amenu[0]=""; //para que no checkee y no entre en el 1er if de abajo
							break; 
						}
					}
					unset($extra2);
				}
		 }

//			si es una pagina comun || una con parametros
			if ($keyname == $pagina || $keyname==$amenu[0]) 				
					break;
					
			$i++;
		}

		//si es una pagina comun -> controla con $pagina
		//sino es una pagina con parametros -> controlar con $amenu[0]
//		echo "permisos_check($padre,$pagina) && permisos_check($padre,{$amenu[0]}) {$_SERVER["SCRIPT_NAME"]}<br>";
		if (!permisos_check($padre,$pagina) && !permisos_check($padre,$amenu[0]))
		{
			echo "<html><head><link rel=stylesheet type='text/css' href='$html_root/lib/estilos.css'>\n";
			echo "</head><body bgcolor=\"$bgcolor3\">\n";
            echo "<!-- Debug:\npagina=".$pagina."\npadre=".$padre."\n-->\n";
			echo "<table width='50%' height='100%' border=2 align=center cellpadding=5 cellspacing=5 bordercolor=$bgcolor3>";
			echo "<tr><td height='50%'>&nbsp;</td></tr>";
			echo "<tr><td align=center bordercolor=#FF0000 bgcolor=#FFFFFF>";
			echo "<table border=0 width='100%'>";
			echo "<tr><td width=15% align=center valign=middle>";
			echo "<img src=$html_root/imagenes/error.gif alt='ERROR' border=0>";
			echo "</td><td width=85% align=center valign=middle>";
			echo "<font size=5 color=#000000 face='Verdana, Arial, Helvetica, sans-serif'><b>";
			echo "USTED NO TIENE PERMISO PARA VER LA PAGINA SOLICITADA</b></font>";
			echo "</td></tr></table>";
			echo "</td></tr>";
			echo "<tr><td height='50%'>&nbsp;</td></tr>";
			echo "</table></body></html>\n";
			exit;
		}
	}
}

function cortar($text, $maxChars = 30, $splitter = '...') {
	$theReturn = $text;
	$lastSpace = false;

	// only do the rest if we're over the character limit
	if (strlen($text) > $maxChars)
	{
		$theReturn = substr($text, 0, $maxChars - 1);
		// add closing punctuation back in if found
		if (in_array(substr($text, $maxChars - 1, 1), array(' ', '.', '!', '?')))
		{
			$theReturn .= substr($text, $maxChars, 1);
		}
		else
		{
			// make room for splitter string and look for truncated words
			$theReturn = substr($theReturn, 0, $maxChars - strlen($splitter));
			$lastSpace = strrpos($theReturn, ' ');
			// Remove truncated words and trailing spaces
			if ($lastSpace !== false)
			{
				$theReturn = substr($theReturn, 0, $lastSpace);
			}
			// Remove trailing commas (add more array elements as desired)
			if (in_array(substr($theReturn, -1, 1), array(',')))
			{
				$theReturn = substr($theReturn, 0, -1);
			}
			// append the splitter string
			$theReturn .= $splitter;
		}
	}
	// all done!
	return $theReturn;
}

function cortar2($text, $maxChars = 30, $splitter = '...', $last = 0) {
	$theReturn = $text;

	// only do the rest if we're over the character limit
	if (strlen($text) > $maxChars)
	{
		if ($last)
			$theReturn = $splitter.substr($text, -$maxChars, $maxChars - 1);
		else
			$theReturn = substr($text, 0, $maxChars - 1).$splitter;
	}
	// all done!
	return $theReturn;
}

function sql($sql, $error = -1) {
	global $db,$contador_consultas,$debug_datos;
	$msg = "";
	$result = null;
	if (count($sql) > 1 or is_array($sql)) {
		$db->StartTrans();
		foreach ($sql as $indice => $sql_str) {
			$debug_datos_temp["sql"] = $sql_str;
			if ($db->Execute($sql_str) === false) {
				$msg .= "(Consulta ".($indice + 1)."): ".$db->ErrorMsg()."<br>";
				$debug_datos_temp["error"] = $db->ErrorMsg();
                //  echo $db->ErrorMsg();
				//sql_error($error,$sql_str,$db->ErrorMsg());
			}
			else {
				$debug_datos_temp["affected"] = $db->Affected_Rows();
//				$debug_datos_temp["count"] = $result->RecordCount();
			}
			$debug_datos[] = $debug_datos_temp;
			$contador_consultas++;
		}
		$db->CompleteTrans();
	}
	else {
		$result = $db->Execute($sql);
		$debug_datos_temp["sql"] = $sql;
		if (!$result) {
			$msg .= $db->ErrorMsg()."<br>";
			$debug_datos_temp["error"] = $db->ErrorMsg();
			//echo $db->ErrorMsg();
			//sql_error($error,$sql,$db->ErrorMsg());
		}
		else {
			//$debug_datos_temp["affected"] = $db->Affected_Rows();
			$debug_datos_temp["count"] = $result->RecordCount();
		}
		$debug_datos[] = $debug_datos_temp;
		$contador_consultas++;
	}
	if ($msg) {
		if ($error != -1) {
			echo "</form></center></table><br><font color=#ff0000 size=3><b>ERROR $error: No se pudo ejecutar la consulta en la base de datos.</font><br>Descripción:<br>$msg</b>";
		}
		return false;
	}
	if ($result)
		return $result;
	else
		return true;
}

function sql_error($error,$sql_error,$db_msg) {
	global $_ses_user,$db;
	$error = addslashes($error);
	$sql_error = encode_link($sql_error);
	$db_msg = encode_link($db_msg);
	$sql = "INSERT INTO errores_sql (codigo_error, sql, msg_error, fecha, usuario) ";
	$sql .= "VALUES ('$error', '$sql_error', '$db_msg', '".date("Y-m-d H:i:s")."', ";
	$sql .= "'".$_ses_user["name"]."')";
	$result = $db->Execute($sql);
}


//toma una letra y un string como parametros y devuelve
//el numero de ocurrencias de es letra en ese string
function str_count_letra($letra,$string) {
 $largo=strlen($string);
 $counter=0;
 for($i=0;$i<$largo;$i++)
 {
  if($string[$i]==$letra)
   $counter++;
 }
 return $counter;

}



/**********************************************************************
FUNCION QUE ORDENA UN ARREGLO BIDIMENSIONAL POR EL CAMPO $campo
DE LA SEGUNDA DIMENSON DEL ARREGLO
@bi_array    El arreglo a ordenar
$campo       El campo de la segunda dimension del arreglo por el cual
			 se ordenara el mismo
$tipo_campo  Este parametro se pone con la palabra string,
			 si el $campo es de tipo string
**********************************************************************/
function qsort_second_dimension($bi_array,$campo,$tipo_campo=0)
{
	$i=0;
 $tam=sizeof($bi_array);
 while($i<$tam)
 {$j=$i+1;
  if($tipo_campo=="string")
   $i_item=$bi_array[$i][$campo];
  else
   $i_item=intval($bi_array[$i][$campo]);
  while($j<$tam)
   {
   	if($tipo_campo=="string")
   	{ $j_item=$bi_array[$j][$campo];
   	  if(strcmp($i_item,$j_item)>0)
      {$temp=$bi_array[$i];
       $bi_array[$i]=$bi_array[$j];
       $bi_array[$j]=$temp;
       $j=$tam;
       $i--;
      }
      else
       $j++;
   	}
   	else
   	{
   	  $j_item=intval($bi_array[$j][$campo]);
   	  if($i_item>$j_item)
      {$temp=$bi_array[$i];
       $bi_array[$i]=$bi_array[$j];
       $bi_array[$j]=$temp;
       $j=$tam;
        $i--;
      }
      else
       $j++;
   	}

   }//de while($j<$tam)
   $i++;
 }//de while($i<$tam)
 return $bi_array;
}//de function qsort_second_dimension($bi_array,$campo,$string=0)



/*********************************************************************************
function insertar_string($cadena,$str, $limite)
Proposito:
          Inserta en $cadena, el string $str cada $limite caracteres.

variables utilizadas:
          - $longitud = contador para la longitud de $cadena
          - $tok = division en palabras de $cadena.
          - $palabra = variable utilizada para armar nuevamente $cadena
          - $string = cadena retornada por la funcion es $cadena con $str insertado $limite
          veces.

Logica:
         La funcion recorre $cadena separando a dicha cadena en palabras con la ayuda
         de la funcion strtok().
         Si la longitud de las palabras procesadas hasta el momento supera a $limite entonces
         se concatena al final de dicha palabra $str y se resetea el contador de longitud.
         antes de procesar la proxima palabra se concatena en $string las palabras procesadas
         hasta el momento.

NOTA: funcion implementada para utilizarse en el modulo licitaciones, en pagina
      funciones.php.
**********************************************************************************/
function insertar_string($cadena,$str, $limite){
$longitud=0;
    $tok = strtok ($cadena," ");
    while ($tok) {
        $longitud+=strlen($tok);
        $palabra=$tok;
        $tok = strtok (" ");
        if($longitud>$limite) {$palabra.=$str;$longitud=0;}
        $string.=" ".$palabra;
    }
    return $string;
}
//final de insertar_string


/********************************************************************************
 Funcion que ajusta el texto pasado como parametro en $texto, agregando 'enters'
 donde corresponda para que cada linea de $texto no supere la cantidad de maxima
 de caracteres que se especifican en el parametro $max_long.
*********************************************************************************/
function ajustar_lineas_texto($texto,$max_long)
{
 //tomamos la longitud de la cadena
 $long_texto=strlen($texto);
 $texto_resultado="";
 $contador=0;
 for($i=0;$i<$long_texto;$i++)
 {
  if($texto[$i]=="\r" && $texto[$i+1]=="\n")
  {
   $contador=0;
  }
  else if($contador==$max_long)
  {
   $texto_resultado.="\n";
   $contador=0;
  }
  else
  {
   $contador++;
  }
  $texto_resultado.=$texto[$i];

 }

 return $texto_resultado;
}

function variables_form_busqueda($prefijo,$extra=array()) {
	global $parametros;
	global $page,$keyword,$up,$filter,$sort,$cmd,$cmd1;
	global ${"_ses_".$prefijo};

	if ($_POST["form_busqueda"]) {
		$page = "0";
		$keyword = $_POST["keyword"];

	}
	else {
		if ((string)$_GET["page"] != "")
			$page = $_GET["page"] - 1;
		elseif ((string)$parametros["page"] != "")
			$page = (string)$parametros["page"];
		elseif ((string)${"_ses_".$prefijo}["page"] != "")
			$page = (string)${"_ses_".$prefijo}["page"];
		else
			$page = "0";
	}

	if (!isset($keyword)) {
		if ((string)$parametros["keyword"] != "")
			$keyword = (string)$parametros["keyword"];
		elseif ((string)${"_ses_".$prefijo}["keyword"] != "")
			$keyword = (string)${"_ses_".$prefijo}["keyword"];
		else
			$keyword = "";
	}


	if ((string)$_POST["up"] != "")
		$up = (string)$_POST["up"];
	elseif ((string)$parametros["up"] != "")
		$up = (string)$parametros["up"];
	elseif ((string)${"_ses_".$prefijo}["up"] != "")
		$up = (string)${"_ses_".$prefijo}["up"];
	else
		$up = "";

	if ((string)$_POST["filter"] != "")
		$filter = (string)$_POST["filter"];
	elseif ((string)$parametros["filter"] != "")
		$filter = (string)$parametros["filter"];
	elseif ((string)${"_ses_".$prefijo}["filter"] != "")
		$filter = (string)${"_ses_".$prefijo}["filter"];
	else
		$filter = "";

	if ((string)$_POST["sort"] != "")
		$sort = (string)$_POST["sort"];
	elseif ((string)$parametros["sort"] != "")
		$sort = (string)$parametros["sort"];
	elseif ((string)${"_ses_".$prefijo}["sort"] != "")
		$sort = (string)${"_ses_".$prefijo}["sort"];
	else
		$sort = "default";

	if ((string)$_POST["cmd"] != "")
		$cmd = (string)$_POST["cmd"];
	elseif ((string)$parametros["cmd"] != "")
		$cmd = (string)$parametros["cmd"];
	else
		$cmd = "";

	if ((string)$_POST["cmd1"] != "")
		$cmd1 = (string)$_POST["cmd1"];
	elseif ((string)$parametros["cmd1"] != "")
		$cmd1 = (string)$parametros["cmd1"];
	else
		$cmd1 = "";

	if ((string)$cmd != "") {
		if ((string)$cmd != (string)${"_ses_".$prefijo}["cmd"]) {


			$up = "";
			$page = "0";
			$filter = "";
			$keyword = "";
			$sort = "default";
			if (is_array($extra) and count($extra) > 0) {
				foreach ($extra as $key => $val) {
					global $$key;
					$$key = $val;
				}
			}
			//$flag_vaciar=1;
			$extra = array();
		}
	}
	else $cmd = (string)${"_ses_".$prefijo}["cmd"];

		//if (!$flag_vaciar && is_array($extra) and count($extra) > 0) {
		if (is_array($extra) and count($extra) > 0) {
		foreach ($extra as $key => $val) {
			if ((string)$_POST[$key] != "")
				$extra[$key] = (string)$_POST[$key];
			elseif ((string)$parametros[$key] != "")
				$extra[$key] = (string)$parametros[$key];
			elseif ((string)${"_ses_".$prefijo}[$key] != "")
				$extra[$key] = (string)${"_ses_".$prefijo}[$key];
			global $$key;
			$$key = $extra[$key];
		}
	}

	$variables = array("cmd"=>$cmd,"cmd1"=>$cmd1,"page"=>$page,"keyword"=>$keyword,"filter"=>$filter,"sort"=>$sort,"up"=>$up);
	$variables = array_merge($variables, $extra);
	if (serialize($variables) != serialize(${"_ses_".$prefijo})) {
		phpss_svars_set("_ses_".$prefijo, $variables);
	}

}

function compara_fechas($fecha1, $fecha2) {
	if ($fecha1) {
		$fecha1 = strtotime($fecha1);
	}
	else {
		$fecha1 = 0;
	}
	if ($fecha2) {
		$fecha2 = strtotime($fecha2);
	}
	else {
		$fecha2 = 0;
	}
    if ($fecha1 > $fecha2) return 1;
    elseif ($fecha1 == $fecha2) return 0;
    else return -1; //fecha2 > fecha1
}

/********************************************
Autor: MAC
-funcion que devuelve true si la fecha pasada
es feriado, y false si no lo es
*********************************************/
function feriado($dia_feriado) {
global $_ses_feriados;

$dia_fer=split("/",$dia_feriado);

$feriado=0;
$dia=intval($dia_fer[0]);
$mes=intval($dia_fer[1]);
$anio=intval($dia_fer[2]);

if (is_array($_ses_feriados[$anio."-".$mes."-".$dia])) {
	$feriado = count($_ses_feriados[$anio."-".$mes."-".$dia]);
}
else {
	$feriado = 0;
}
return $feriado;
}



/****************************************************************
Autor: MAC
-funcion que devuelve la cantidad de dias habiles que faltan
desde la $fecha1 de la $fecha2.

-El formato de las fechas debe ser d/m/Y
*****************************************************************/
function diferencia_dias_habiles($fecha1,$fecha2)
{
 $dif_dias=0;
 $fecha_aux=$fecha1;

 while(compara_fechas(fecha_db($fecha_aux),fecha_db($fecha2))==-1) //mientras la fecha2 sea mayor que la 1
 {
  $fecha_split=split("/",$fecha_aux);
  $fecha_dia=date("w",mktime(0,0,0,$fecha_split[1],$fecha_split[0],$fecha_split[2]));

  //si es dia habil, incrementamos la diferencia
  if($fecha_dia!=0 && !feriado($fecha_aux) &&  $fecha_dia!=6)

   $dif_dias++;
  //incrementamos en un dia la fecha
  $fecha_aux=date("d/m/Y",mktime(12,0,0,$fecha_split[1],$fecha_split[0]+1,$fecha_split[2]));

 }
 return $dif_dias;
}

/****************************************************************
Autor: Cestila
-funcion que devuelve la cantidad de dias que faltan
desde la $fecha1 de la $fecha2.

-El formato de las fechas debe ser d/m/Y
/*****************************************************************/

function diferencia_dias($fecha1,$fecha2,$h=0)

{
 $dif_dias=0;
 $fecha_aux=$fecha1;
 $fecha_hasta=$fecha2;
 if ($h) {
         $hora=date("H");
         $minutos=date("i");
         $segundos=date("s");
        while(compara_fechas($fecha_aux,$fecha_hasta)==-1) //mientras la fecha2 sea mayor que la 1
         {
          $fecha_split=split("/",fecha($fecha_aux));
          $dif_dias++;
          $fecha_aux=date("Y-m-d H:i:s",mktime($hora,$minutos,$segundos,$fecha_split[1],$fecha_split[0]+1,$fecha_split[2]));
         }

} //del if
else
   {
   $fecha_hasta=fecha_db($fecha_hasta);
   while(compara_fechas(fecha_db($fecha_aux),$fecha_hasta)==-1) //mientras la fecha2 sea mayor que la 1
    {
     $fecha_split=split("/",$fecha_aux);
     $dif_dias++;
     $fecha_aux=date("d/m/Y",mktime(12,0,0,$fecha_split[1],$fecha_split[0]+1,$fecha_split[2]));
    }
   }

 return $dif_dias;

}//de la funcion dia habiles


///adaptacion de la funcion mariela en php
//funcion que  me convierte de numero a letra copia de la funcion de mariela
function Centenas($VCentena) {
$Numeros[0] = "cero";
$Numeros[1] = "uno";
$Numeros[2] = "dos";
$Numeros[3] = "tres";
$Numeros[4] = "cuatro";
$Numeros[5] = "cinco";
$Numeros[6] = "seis";
$Numeros[7] = "siete";
$Numeros[8] = "ocho";
$Numeros[9] = "nueve";
$Numeros[10] = "diez";
$Numeros[11] = "once";
$Numeros[12] = "doce";
$Numeros[13] = "trece";
$Numeros[14] = "catorce";
$Numeros[15] = "quince";
$Numeros[20] = "veinte";
$Numeros[30] = "treinta";
$Numeros[40] = "cuarenta";
$Numeros[50] = "cincuenta";
$Numeros[60] = "sesenta";
$Numeros[70] = "setenta";
$Numeros[80] = "ochenta";
$Numeros[90] = "noventa";
$Numeros[100] = "ciento";
$Numeros[101] = "quinientos";
$Numeros[102] = "setecientos";
$Numeros[103] = "novecientos";
If ($VCentena == 1) { return $Numeros[100]; }
Else If ($VCentena == 5) { return $Numeros[101];}
Else If ($VCentena == 7 ) {return ( $Numeros[102]); }
Else If ($VCentena == 9) {return ($Numeros[103]);}
Else {return $Numeros[$VCentena];}

}
function Unidades($VUnidad) {
$Numeros[0] = "cero";
$Numeros[1] = "uno";
$Numeros[2] = "dos";
$Numeros[3] = "tres";
$Numeros[4] = "cuatro";
$Numeros[5] = "cinco";
$Numeros[6] = "seis";
$Numeros[7] = "siete";
$Numeros[8] = "ocho";
$Numeros[9] = "nueve";
$Numeros[10] = "diez";
$Numeros[11] = "once";
$Numeros[12] = "doce";
$Numeros[13] = "trece";
$Numeros[14] = "catorce";
$Numeros[15] = "quince";
$Numeros[20] = "veinte";
$Numeros[30] = "treinta";
$Numeros[40] = "cuarenta";
$Numeros[50] = "cincuenta";
$Numeros[60] = "sesenta";
$Numeros[70] = "setenta";
$Numeros[80] = "ochenta";
$Numeros[90] = "noventa";
$Numeros[100] = "ciento";
$Numeros[101] = "quinientos";
$Numeros[102] = "setecientos";
$Numeros[103] = "novecientos";
$tempo=$Numeros[$VUnidad];
return $tempo;
}

function Decenas($VDecena) {
$Numeros[0] = "cero";
$Numeros[1] = "uno";
$Numeros[2] = "dos";
$Numeros[3] = "tres";
$Numeros[4] = "cuatro";
$Numeros[5] = "cinco";
$Numeros[6] = "seis";
$Numeros[7] = "siete";
$Numeros[8] = "ocho";
$Numeros[9] = "nueve";
$Numeros[10] = "diez";
$Numeros[11] = "once";
$Numeros[12] = "doce";
$Numeros[13] = "trece";
$Numeros[14] = "catorce";
$Numeros[15] = "quince";
$Numeros[20] = "veinte";
$Numeros[30] = "treinta";
$Numeros[40] = "cuarenta";
$Numeros[50] = "cincuenta";
$Numeros[60] = "sesenta";
$Numeros[70] = "setenta";
$Numeros[80] = "ochenta";
$Numeros[90] = "noventa";
$Numeros[100] = "ciento";
$Numeros[101] = "quinientos";
$Numeros[102] = "setecientos";
$Numeros[103] = "novecientos";
$tempo = ($Numeros[$VDecena]);
return $tempo;
}





function NumerosALetras($Numero){


list($Numero, $Decimales) = split("[,.]",$Numero);

$Numero = intval($Numero);
$Decimales = intval($Decimales);
$letras = "";

while ($Numero != 0){

// '*---> Validación si se pasa de 100 millones

If ($Numero >= 1000000000) {
$letras = "Error en Conversión a Letras";
$Numero = 0;
$Decimales = 0;
}

// '*---> Centenas de Millón
If (($Numero < 1000000000) And ($Numero >= 100000000)){
If ((Intval($Numero / 100000000) == 1) And (($Numero - (Intval($Numero / 100000000) * 100000000)) < 1000000)){
$letras .= (string) "cien millones ";
}
Else {
$letras = $letras & Centenas(Intval($Numero / 100000000));
If ((Intval($Numero / 100000000) <> 1) And (Intval($Numero / 100000000) <> 5) And (Intval($Numero / 100000000) <> 7) And (Intval($Numero / 100000000) <> 9)) {
$letras .= (string) "cientos ";
}
Else {
$letras .= (string) " ";
}
}
$Numero = $Numero - (Intval($Numero / 100000000) * 100000000);
}

// '*---> Decenas de Millón
If (($Numero < 100000000) And ($Numero >= 10000000)) {
If (Intval($Numero / 1000000) < 16) {
$tempo = Decenas(Intval($Numero / 1000000));
$letras .= (string) $tempo;
$letras .= (string) " millones ";
$Numero = $Numero - (Intval($Numero / 1000000) * 1000000);
}
Else {
$letras = $letras & Decenas(Intval($Numero / 10000000) * 10);
$Numero = $Numero - (Intval($Numero / 10000000) * 10000000);
If ($Numero > 1000000) {
$letras .= $letras & " y ";
}
}
}

// '*---> Unidades de Millón
If (($Numero < 10000000) And ($Numero >= 1000000)) {
$tempo=(Intval($Numero / 1000000));
If ($tempo == 1) {
$letras .= (string) " un millón ";
}
Else {
$tempo= Unidades(Intval($Numero / 1000000));
$letras .= (string) $tempo;
$letras .= (string) " millones ";
}
$Numero = $Numero - (Intval($Numero / 1000000) * 1000000);
}

// '*---> Centenas de Millar
If (($Numero < 1000000) And ($Numero >= 100000)) {
$tempo=(Intval($Numero / 100000));
$tempo2=($Numero - ($tempo * 100000));
If (($tempo == 1) And ($tempo2 < 1000)) {
$letras .= (string) "cien mil ";
}
Else {
$tempo=Centenas(Intval($Numero / 100000));
$letras .= (string) $tempo;
$tempo=(Intval($Numero / 100000));
If (($tempo <> 1) And ($tempo <> 5) And ($tempo <> 7) And ($tempo <> 9)) {
$letras .= (string) "cientos ";
}
Else {
$letras .= (string) " ";
}
}
$Numero = $Numero - (Intval($Numero / 100000) * 100000);
}

// '*---> Decenas de Millar
If (($Numero < 100000) And ($Numero >= 10000)) {
$tempo= (Intval($Numero / 1000));
If ($tempo < 16) {
$tempo = Decenas(Intval($Numero / 1000));
$letras .= (string) $tempo;
$letras .= (string) " mil ";
$Numero = $Numero - (Intval($Numero / 1000) * 1000);
}
Else {
$tempo = Decenas(Intval($Numero / 10000) * 10);
$letras .= (string) $tempo;
$Numero = $Numero - (Intval(($Numero / 10000)) * 10000);
If ($Numero > 1000) {
	$rest = substr($letras, -6);
    if ($rest!='veinte'){
	    $resto = substr($letras, -4);
 	    if ($resto!='diez')
           $letras .=(string) " y ";
    }
   if($rest=='veinte') {
      $letras= substr($letras,0, -1);
  	  $letras.='i';
    }
    if ($resto=='diez') {
    	$letras=substr($letras,0, -1);
        $letras.= 'ci';
    }

}
Else {
$letras .= (string) " mil ";

}
}
}


// '*---> Unidades de Millar
If (($Numero < 10000) And ($Numero >= 1000)) {
$tempo=(Intval($Numero / 1000));
If ($tempo == 1) {
$letras .= (string) "un";
}
Else {
$tempo = Unidades(Intval($Numero / 1000));
$letras .= (string) $tempo;
}
$letras .= (string) " mil ";
$Numero = $Numero - (Intval($Numero / 1000) * 1000);
}

// '*---> Centenas
If (($Numero < 1000) And ($Numero > 99)) {
If ((Intval($Numero / 100) == 1) And (($Numero - (Intval($Numero / 100) * 100)) < 1)) {
//$letras = $letras & "cien ";
$letras.="cien";
}
Else {
$temp=(Intval($Numero / 100));
$l2=Centenas($temp);
$letras .= (string) $l2;
If ((Intval($Numero / 100) <> 1) And (Intval($Numero / 100) <> 5) And (Intval($Numero / 100) <> 7) And (Intval($Numero / 100) <> 9)) {
$letras .= "cientos ";
}
Else {
$letras .= (string) " ";
}
}

$Numero = $Numero - (Intval($Numero / 100) * 100);

}

// '*---> Decenas
If (($Numero < 100) And ($Numero > 9) ) {
If ($Numero < 16 ) {
$tempo = Decenas(Intval($Numero));
$letras .= $tempo;
$Numero = $Numero - Intval($Numero);
}
Else {
$tempo= Decenas(Intval(($Numero / 10)) * 10);
$letras .= (string) $tempo;
$Numero = $Numero - (Intval(($Numero / 10)) * 10);
If ($Numero > 0.99) {

	$rest = substr($letras, -6);
   	if ($rest!='veinte'){
	    $resto = substr($letras, -4);
 	    if ($resto!='diez')
           $letras .=(string) " y ";
    }

   if($rest=='veinte') {
   	  $resto="";
      $letras= substr($letras,0, -1);
  	  $letras.='i';
  	}
    if ($resto=='diez') {
       $letras=substr($letras,0, -1);
 	   $letras.= 'ci';
    }

}
}
}

// '*---> Unidades
If (($Numero < 10) And ($Numero > 0.99)) {
$tempo=Unidades(Intval($Numero));
$letras .= (string) $tempo;

$Numero = $Numero - Intval($Numero);
}


// '*---> Decimales
If ($Decimales > 0) {
	If (($letras <> "Error en Conversión a Letras") And (strlen(Trim($letras)) > 0)) {
		$letras .= (string) " con ".$Decimales."/100";
	}
}
Else {
	If (($letras <> "Error en Conversión a Letras") And (strlen(Trim($letras)) > 0)) {
		$letras .= (string) " ";
	}
}
return $letras;
}
}

function generar_barra_nav($campos_barra) {
	global $cmd,$total_registros,$bgcolor3,$html_root;
	$barra = "";
	$width = floor(100/count($campos_barra));
	foreach ($campos_barra as $clave => $valor) {
         //print_r($valor["extra"]);
		if ($valor["sql_contar"]) {
			$result = sql($valor["sql_contar"]) or die;
			$total_registros[$valor["cmd"]] = $result->fields[0];
			$valor["descripcion"] .= " (".$total_registros[$valor["cmd"]].")";
		}
		if ($cmd == $valor["cmd"]) {
			if (BROWSER_OK) {
	            $barra .= '<a class="btn btn-primary active" role="button" href="'.encode_link($_SERVER["PHP_SELF"],is_array($valor["extra"])?array_merge($valor["extra"],array("cmd" => $valor["cmd"])):array("cmd" => $valor["cmd"])).'">'.$valor["descripcion"].'</a>';
			}
			else {
	            $barra .= "<td width='$width%'>";
				$barra .= "<a class='btn btn-primary btn-block btn-sm active' href='".encode_link($_SERVER["PHP_SELF"],is_array($valor["extra"])?array_merge($valor["extra"],array("cmd" => $valor["cmd"])):array("cmd" => $valor["cmd"]))."'>";
				$barra .= $valor["descripcion"];
	            $barra.="</a></td>";
        	}
		}
		else {
			if (BROWSER_OK) {
            	$barra .= '<a class="btn btn-primary" role="button" href="'.encode_link($_SERVER["PHP_SELF"],is_array($valor["extra"])?array_merge($valor["extra"],array("cmd" => $valor["cmd"])):array("cmd" => $valor["cmd"])).'">'.$valor["descripcion"].'</a>';
			}
			else {
				$barra .= "<td width='$width%'>";
				$barra .= "<a class='btn btn-primary btn-block btn-sm' href='".encode_link($_SERVER["PHP_SELF"],is_array($valor["extra"])?array_merge($valor["extra"],array("cmd" => $valor["cmd"])):array("cmd" => $valor["cmd"]))."'>";
				$barra .= $valor["descripcion"];
				$barra.="</a></td>";
			}
		}
	}
	if (BROWSER_OK) {
		echo '
		<div class="btn-group btn-group-justified" role="group">
	      	'.$barra.'
		</div>
		';
	}
	else {
		echo "<table width='95%' align='center'><tr><td><table class='table'>\n";
		echo "<tr>${barra}</tr></table></td></tr></table>\n";
	}
}


function firma_mail(){
	$confiden="\n\nNOTA DE CONFIDENCIALIDAD\n";
	$confiden.="Este mensaje (y sus anexos) es confidencial generado automaticamente, esta dirigido exclusivamente a ";
	$confiden.="las personas direccionadas en el mail, puede contener información de ";
	$confiden.="propiedad exclusiva de Ministerio de Salud de la Provincia de San Luis y/o amparada por el secreto profesional.\n";
	$confiden.="El acceso no autorizado, uso, reproducción, o divulgación esta prohibido.\n";
	return $confiden;
}

function encabezado_mail(){
	$confiden="SISTEMA NOTIFICACIONES\n\n";	
	return $confiden;
}

//funcion para enviar los mails
function enviar_mail($para,$paracc,$parabcc,$asunto,$contenido,$adjunto,$path,$htmlflag=1){
 $mail = new PHPMailer();	
 $mail->Mailer = "smtp"; 
 $mail->Host = "smtp.sanluis.gov.ar"; //servidor de mi trabajo
 $mail->SMTPAuth = true; 
 $mail->Username = "plannacer"; 
 $mail->Password = "host2010"; 
 $mail->Timeout=50; 
 $mail->From = "plannacer@sanluis.gov.ar";
 $mail->FromName = "Ministerio de Salud";
 $mail->AddAddress($para,$para);
 $mail->Subject = "Notificacion Automatica - ".$asunto;
 $mail->Body = encabezado_mail().$contenido.firma_mail();
 if (!$htmlflag)$mail->IsHTML(true);
 if ($paracc!='')$mail->AddCC($paracc);
 if ($parabcc!='')$mail->AddBCC($parabcc);
 if ($adjunto!='')$mail->AddAttachment($path."/".$adjunto,$adjunto);
 return $mail->Send();
}


function FileUpload($TempFile, $FileSize, $FileName, $FileType, $MaxSize, $Path, $ErrorFunction, $ExtsOk, $ForceFilename, $OverwriteOk,$comprimir=1,$mostrar_carteles=1) {
	global $ID,$id_archivo;
	//global $ID,$_ses_user_name,$id_archivo;
	$retorno["error"] = 0;
	if (strlen($ForceFilename)) { $FileName = $ForceFilename; }
	//$err=`mkdir -p '$Path'`;
	mkdirs (enable_path($Path));

	if (!function_exists($ErrorFunction)) {
		if (!function_exists('DoFileUploadDefErrorHandle')) {
			function DoFileUploadDefErrorHandle($ErrorNumber, $ErrorText) {
				echo "<tr><td colspan=2 align=center><font color=red><b>Error $ErrorNumber: $ErrorText</b></font><br><br></td></tr>";
			}
		}
		$ErrorFunction = 'DoFileUploadDefErrorHandle';
	}
        if($mostrar_carteles)
	{echo "<tr><td>Nombre:</td><td>$FileName</td></tr>\n";
	 echo "<tr><td>Tamaño:</td><td>$FileSize</td></tr>\n";
	 echo "<tr><td>Tipo MIME:</td><td>$FileType</td></tr>\n";
	}
	if($TempFile == 'none' || $TempFile == '') {
		$ErrorTxt = "No se especificó el nombre del archivo<br>";
		$ErrorTxt .= "o el archivo excede el máximo de tamaño de:<br>";
		$ErrorTxt .= ($MaxSize / 1024)." Kb.";
		$retorno["error"] = 1;
		$ErrorFunction($retorno["error"], $ErrorTxt);
		return $retorno;
	}

	if(!is_uploaded_file($TempFile)) {
		$ErrorTxt = "File Upload Attack, Filename: \"$FileName\"";
		$retorno["error"] = 2;
		$ErrorFunction($retorno["error"], $ErrorTxt);
		return $retorno;
	}

	if($FileSize == 0) {
		$ErrorTxt = 'El archivo que ha intentado subir, está vacio!';
		$retorno["error"] = 3;
		$ErrorFunction($retorno["error"], $ErrorTxt);
		return $retorno;
	}

/*
	$TheExt = GetExt($FileName);

	foreach ($ExtsOk as $CurNum => $CurText) {
		if ($TheExt == $CurText) { $FileExtOk = 1; }
	}

	if($FileExtOk != 1) {
		$ErrorTxt = 'You attempted to upload a file with a disallowed extention!';
		$ErrNo = 4;
		$ErrorFunction($ErrNo, $ErrorTxt);
		return $ErrNo;
	}
*/
	if($FileSize > $MaxSize) {
		$ErrorTxt = 'El archivo que ha intentado subir excede el máximo de ' . ($MaxSize / 1024) . 'kb.';
		$retorno["error"] = 5;
		$ErrorFunction($retorno["error"], $ErrorTxt);
		return $retorno;
	}

	$FileNameFull = enable_path($Path."/".$FileName);
	$FileNameFullComp = substr($FileNameFull,0,strlen($FileNameFull) - strpos(strrev($FileNameFull),".") - 1).".zip";

	clearstatcache();
	if((file_exists($FileNameFull) || file_exists($FileNameFullComp)) && !strlen($OverwriteOk)) {
		$ErrorTxt = 'El archivo que ha intentado subir ya existe. Por favor especifique un nombre distinto.';
		$retorno["error"] = 6;
		$ErrorFunction($retorno["error"], $ErrorTxt);
		return $retorno;
	}

	move_uploaded_file ($TempFile, $FileNameFull) or die("error al mover el temporal <br> $TempFile <br> hasta <br> $FileNameFull");

	if ($comprimir) {
		$ext = strtolower(GetExt($FileNameFull));
		if ($ext != "zip") {
			$FileNameOld = $FileNameFull;
			$FileNameFull = $FileNameFullComp;
	//			$err = `/bin/pkzip -add -dir=none "$FileNameFull" "$FileNameOld"`;
			if (SERVER_OS == "linux") {
				$err = `/usr/bin/zip -j -9 -q "$FileNameFull" "$FileNameOld"`;
			} elseif (SERVER_OS == "windows"){
				$paso = ROOT_DIR."\\lib\\zip";
				$err = shell_exec("$paso\\zip.exe -j -9 -q  \"$FileNameFull\" \"$FileNameOld\"");

			} else {
				die("Error en compresión.");
			}
			//echo "<br> $TempFile <br> $FileNameFull<br> $FileNameOld<br>";
			unlink($FileNameOld);

			if ($err) {
				$ErrorTxt = "No se pudo comprimir el archivo $FileName";
				$retorno["error"] = 8;
				$ErrorFunction($retorno["error"], $ErrorTxt);
				return $retorno;
			}
		}

		$FileSizeComp=filesize($FileNameFull);
		if($mostrar_carteles)
		 echo "<tr><td>Tamaño comprimido:</td><td>$FileSizeComp</td></tr>\n";
	}
	chmod ($FileNameFull, 0600);

	if (SERVER_OS == "linux") {
		$FileNameComp = substr($FileNameFull,strrpos($FileNameFull,"/") + 1);
	} elseif (SERVER_OS == "windows"){
		$FileNameComp = substr($FileNameFull,strrpos($FileNameFull,"\\") + 1);
	} else {
		die("Error en conocer el sistema operativo.");
	}

	$retorno["filenamecomp"] = $FileNameComp;
	$retorno["filesizecomp"] = $FileSizeComp;


	if($mostrar_carteles)
	 echo "<tr><td colspan=2 align=center><b>Archivo subido correctamente!</b><br><br></td></tr>\n";


	return $retorno;
}

function GetExt($Filename) {
	$RetVal = explode ( '.', $Filename);
	return $RetVal[count($RetVal)-1];
}


/***********************************************************************
FileDownload sirve para bajar archivos, ya sea comprimidos o no

@Comp Sirve para indicar que se quiere bajar el archivo sin descomprimir
************************************************************************/
function FileDownload($Comp, $FileName, $FileNameFull, $FileType, $FileSize, $zipguardado = 1){
     //si $zipguardado es 1 significa que el archivo esta almacenado en servidor como zip

	if ($zipguardado){
		if (($Comp) or (substr($FileName,strrpos($FileName,".")) == ".zip"))
		{
			if (file_exists($FileNameFull))
			{
				Mostrar_Header($FileName,$FileType,$FileSize);
				readfile($FileNameFull);
				exit();
			}
			else
			{
				Mostrar_Error("Se produjo un error al intentar abrir el archivo comprimido");
			}
		}
		else {
			$FileNameFull = substr($FileNameFull,0,strrpos($FileNameFull,"."));

			if(SERVER_OS == "linux")
			{
				$fp = popen("/usr/bin/unzip -p \"$FileNameFull\" 2> /dev/null","r");
			}
			elseif (SERVER_OS == "windows")
			{
			   	$fp = popen(enable_path(LIB_DIR)."\\zip\\unzip.exe -p \"$FileNameFull\"","rb");
		    }

			if (!$fp)
			{
				Mostrar_Error("Se produjo un error al intentar descomprimir el archivo");
			}
			else
			{
				//echo "NAME $FileName - TYPE $FileSize - SIZE $FileSize";
				Mostrar_Header($FileName,$FileType,$FileSize);
				fpassthru($fp);
				pclose($fp);
				exit();
			}
		}
	}
	else //guardado sin comprimir
	{
		if (file_exists($FileNameFull))
		    {
				Mostrar_Header($FileName,$FileType,$FileSize);
				readfile($FileNameFull);
			}
			else
			{
				Mostrar_Error("Se produjo un error al intentar abrir el archivo comprimido");
			}
	}
}

function fin_pagina($debug=true,$mostrar_tiempo=true,$mostrar_consultas=true) {
	global $_ses_user,$debug_datos,$parametros;
	if ($debug and $_ses_user["debug"] == "on") {
		echo "<pre>\$debug_datos=";
		print_r($debug_datos);
		echo "</pre>";
		echo "<pre>\$parametros=";
		print_r($parametros);
		echo "</pre>";
		echo "<pre>\$_GET=";
		print_r($_GET);
		echo "</pre>";
		echo "<pre>\$_POST=";
		print_r($_POST);
		echo "</pre>";
	}
	if ($mostrar_tiempo) {
		echo "Página generada en ".tiempo_de_carga()." segundos.<br>";
	}
	if ($mostrar_consultas) {
		echo "Se utilizaron ".(count($debug_datos))." consulta/s SQL.<br>";
	}
	die("</body></html>\n");
}

function permisos_check($modulo, $item) {
	global $_ses_user;
	global $ouser;
//	if ($item=='ord_compra')
//	echo "modulo:$modulo pagina:$item<br>";	
//	print_r($_ses_user);
//	die;
//	si existe el permiso, y esta permitido y (es un permiso sin directorio o tiene directorio y es igual al modulo requerido)
//	if ($_ses_user["permisos"][$item] && $_ses_user["permisos"][$item]['allow'] && ($_ses_user["permisos"][$item]['dir']=="" || $_ses_user["permisos"][$item]['dir']==$modulo))
	if ($ouser->permisos[$item])// && ($ouser->permisos[$item]->dir=="" || $ouser->permisos[$item]->dir==$modulo))
		return true;
	else
		return false;
}

/*function permisos_actualizar() {
	global $_ses_user;
	$_ses_user["permisos"] = permisos_cargar($_ses_user["login"]);
	phpss_svars_set("_ses_user", $_ses_user);
}*/

function nombre_archivo($nombre) {
	$nombre = ereg_replace("[()]","",$nombre);
	$nombre = ereg_replace("[^A-Za-z0-9,.+-]","_",$nombre);
//	$nombre = ereg_replace("['`\"/\()<>]","",$nombre);
	return $nombre;
}

/**************************************************************************
Funcion que genera codigo de barra
/**************************************************************************/

require(LIB_DIR."/barcode/barcode.php");
require(LIB_DIR."/barcode/c128aobject.php");

//Esta funcion es exclusiva para Orden de Produccion, ya que no muestra el codigo de barra por el navegador
//Ver funcion mas abajo

function generar_codigo_barra($barcode='0123456789',$output='png',$width='460',$height='120',$xres='2',$font='5',$border='off',$drawtext='off',$stretchtext=' ',$negative='off',$redimweight='',$redimheight='')
{
global $html_root;
//Genración del Código de Barras
if (isset($barcode) && strlen($barcode)>0) {
  $style  = BCS_ALIGN_CENTER;
  $style |= ($output  == "png" ) ? BCS_IMAGE_PNG  : 0;
  $style |= ($output  == "jpeg") ? BCS_IMAGE_JPEG : 0;
  $style |= ($border  == "on"  ) ? BCS_BORDER 	  : 0;
  $style |= ($drawtext== "on"  ) ? BCS_DRAW_TEXT  : 0;
  $style |= ($stretchtext== "on" ) ? BCS_STRETCH_TEXT  : 0;
  $style |= ($negative== "on"  ) ? BCS_REVERSE_COLOR  : 0;

  $obj = new C128AObject(250, 120, $style, $barcode);

  if ($obj) {
   $obj->DrawObject($xres);

   ob_start();
	imagepng($obj->mImg);
   $buffer = ob_get_contents();
   ob_end_clean();
   return $buffer;
  }

}

}//fin funcion generar_codigo_barra

//Esta es la funcion que genera el codigo de barra para que sea mostrado por el navegador

function codigo_barra($barcode='0123456789',$output='png',$width='460',$height='120',$xres='2',$font='5',$border='off',$drawtext='off',$stretchtext=' ',$negative='off',$redimweight='',$redimheight='')
{
global $html_root;
//Genración del Código de Barras
if (isset($barcode) && strlen($barcode)>0) {
  $style  = BCS_ALIGN_CENTER;
  $style |= ($output  == "png" ) ? BCS_IMAGE_PNG  : 0;
  $style |= ($output  == "jpeg") ? BCS_IMAGE_JPEG : 0;
  $style |= ($border  == "on"  ) ? BCS_BORDER 	  : 0;
  $style |= ($drawtext== "on"  ) ? BCS_DRAW_TEXT  : 0;
  $style |= ($stretchtext== "on" ) ? BCS_STRETCH_TEXT  : 0;
  $style |= ($negative== "on"  ) ? BCS_REVERSE_COLOR  : 0;

  $obj = new C128AObject(250, 120, $style, $barcode);

  if ($redimweight!='')
   $estilo="style='width:$redimweight";
  if ($redimheight!='')
  {if ($redimweight!='')
   $estilo.=",height:$redimheight'";
   else
   $estilo="style='height:$redimheight'";
  }
  if (($redimweight!='') && ($redimheight==''))
   $estilo.='\'';

  if ($obj) {
     if ($obj->DrawObject($xres)) {
         echo "<table align='center'><tr><td><img src='$html_root/lib/barcode/image.php?code=".$barcode."&style=".$style."&type=".$type."&width=".$width."&height=".$height."&xres=".$xres."&font=".$font."' $estilo></td></tr></table>";
     } else echo "<table align='center'><tr><td><font color='#FF0000'>".($obj->GetError())."</font></td></tr></table>";
  }

}

}//fin funcion generar_codigo_barra

/**************************************************************************
Fin Funciones que genera codigo de barra
/**************************************************************************/




//funcion para corregir el path segun el sistema operativo
function enable_path($paso){
	if (($paso != "") && ((str_count_letra('/',$paso) > 0) || (str_count_letra('\\',$paso) > 0))) {
		if (SERVER_OS == "linux") {
			$ret = str_replace("\\","/",$paso);
		} elseif (SERVER_OS == "windows") {
			$ret = str_replace("/","\\",$paso);
		}
	} else $ret = $paso;

	return $ret;
}

function comprimir_variable($var) {
	$ret = "";
	if ($var != "") {
		$var = serialize($var);
		if ($var != "") {
			$gz = @gzcompress($var);
			if ($gz != "") {
				$ret = base64_encode($gz);
			}
		}
	}
	return $ret;
return base64_encode(gzcompress(serialize($var)));
}
function descomprimir_variable($var) {
	$ret = "";
	if ($var != "") {
		$var = base64_decode($var);
		if ($var != "") {
			$gz = @gzuncompress($var);
			if ($gz != "") {
				$ret = unserialize($gz);
			}
		}
	}
	return $ret;
}

function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
	global $_ses_user,$_ultimo_error,$html_root;
	$mostrar = 0;
	switch ($errno) {
		case E_USER_WARNING:
			$tipo_error = "USER_WARNING";
			$mostrar = 0;
			break;
		case E_USER_NOTICE:
			$tipo_error = "USER_NOTICE";
			$mostrar = 1;
			break;
		case E_WARNING:
			$tipo_error = "WARNING";
			$mostrar = 2;
			break;
		case E_NOTICE:
			$tipo_error = "NOTICE";
			$mostrar = 0;
			break;
		case E_CORE_WARNING:
			$tipo_error = "CORE_WARNING";
			$mostrar = 2;
			break;
		case E_COMPILE_WARNING:
			$tipo_error = "COMPILE_WARNING";
			$mostrar = 2;
			break;
		case E_USER_ERROR:
			$tipo_error = "USER_ERROR";
			$mostrar = 0;
			break;
		case E_ERROR:
			$tipo_error = "ERROR";
			$mostrar = 2;
			break;
		case E_PARSE:
			$tipo_error = "PARSE";
			$mostrar = 2;
			break;
		case E_CORE_ERROR:
			$tipo_error = "CORE_ERROR";
			$mostrar = 2;
			break;
		case E_COMPILE_ERROR:
			$tipo_error = "COMPILE_ERROR";
			$mostrar = 2;
			break;
		case 2048:
			$mostrar = 0;
	}
	if ($mostrar == 2) {
		$_ultimo_error[] = $errstr;
	}
	$msg_error = "<table width='50%' height='100%' border=0 align=center cellpadding=0 cellspacing=0>";
	$msg_error .= "<tr><td height='50%'>&nbsp;</td></tr>";
	$msg_error .= "<tr><td align=center>";
	$msg_error .= "<table border=2 width='100%' bordercolor='#FF0000' bgcolor='#FFFFFF' cellpadding=0 cellspacing=0>";
	if ($mostrar == 1) {
		if  ($_SERVER["HTTP_HOST"]=="localhost") {
			$msg_error .= "<tr><td width=15% align=center valign=middle style='border-right:0'>";
			$msg_error .= "<img src=$html_root/imagenes/error.gif alt='ERROR' border=0>";
			$msg_error .= "</td><td width=85% align=center valign=middle style='border-left:0'>";
			$msg_error .= "<font size=2 color=#000000 face='Verdana, Arial, Helvetica, sans-serif'><b>";
			$msg_error .= "SE HA PRODUCIDO UN ERROR EN EL SISTEMA<br>";
			$msg_error .= "El error fue notificado a los programadores y sera solucionado a la brevedad<br>";
			$msg_error .= "</b></font>";
			$msg_error .= "</td></tr>";
		}
		else {
			$msg_error .= "TIPO:$tipo_error<br>";
			$a = explode("\t\n\t",$errstr);
			if (substr($a[0],0,2) == "a:") {
				$a[0] = unserialize($a[0]);
			}
				echo "DESCRIPCION:<pre>";
				if (is_array($a[0])) {
					print_r($a[0]);
				}
				else {
					echo $a[0];
				}
				echo "</pre><br>";
				echo "ARCHIVO:".$a[1]."<br>";
				echo "LINEA:".$a[2]."<br>";
			if (count($_ultimo_error) > 0) {
				echo "ERRORES:<pre>";
				print_r($_ultimo_error);
				echo "</pre>";
				$_ultimo_error = array();
			}
			echo "USUARIO:".$_ses_user["name"]."<br>";
		}
		$msg_error .= "</table></td></tr>";
		$msg_error .= "<tr><td height='50%' align='center'>";
		/*$link_volver = "";
		if ($_SERVER["REQUEST_URI"] != "") {
			$link_volver .= $_SERVER["REQUEST_URI"];
		}
		elseif ($_SERVER["HTTP_REFERER"] != "") {
			$link_volver .= $_SERVER["HTTP_REFERER"];
		}
		if ($link_volver == "") {
			$msg_error .= "&nbsp;";
		}
		else {*/
			//$msg_error .= "<input type=button value='Volver' onClick=\"document.location='$link_volver';\" style='width:100px;height:30px;'>";
			$msg_error .= "<input type=button value='Volver' onClick=\"history.back();\" style='width:100px;height:30px;'>";
		//}
		$msg_error .= "</td></tr>";
		$msg_error .= "</table>\n";
		echo $msg_error;
		//phpinfo();
	}
}

function reportar_error($descripcion,$archivo,$linea) {
	if (is_array($descripcion)) {
		$descripcion = serialize($descripcion);
	}
	trigger_error($descripcion."\t\n\t".$archivo."\t\n\t".$linea);
	//fin_pagina();
	exit();
}


///////////////////////////////PARA ELIMINAR ELEMENTOS REPETIDOS EN UN ARRAY////////////////////////////////////
///////////////////////////////BROGGI///////////////////////////////////////////////////////////////////////////
//si retorna_en = 1 la salida es es un arreglo
//si retorna_en = 0 la salida es es un string
function elimina_repetidos($entrada,$retorna_en=1)
{$copia=array();
 $tamaño=count($entrada);
 $indice=0;
 $indice_copia=0;
 while ($indice<$tamaño)
       {$auja=$entrada[$indice];
        $entrada[$indice]="";
        if (in_array($auja,$entrada))
           {
           }
        else {$copia[$indice_copia]=$auja;
              $indice_copia++;
             }
        $indice++;
       }
 if ($retorna_en==1) return $copia;
 else {$tamaño=count($copia);
       $indice=0;
       $string=$copia[$indice];
       $indice++;
       while ($indice<$tamaño)
             {$string.=",".$copia[$indice];
              $indice++;
             }
       return $string;
      }
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function isIPIn($ip,$net,$mask) {
   $lnet=ip2long($net);
   $lip=ip2long($ip);
   $binnet=str_pad( decbin($lnet),32,"0",STR_PAD_LEFT );
   $firstpart=substr($binnet,0,$mask);
   $binip=str_pad( decbin($lip),32,"0",STR_PAD_LEFT );
   $firstip=substr($binip,0,$mask);
   return(strcmp($firstpart,$firstip)==0);
}

function getIP() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
       $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    elseif (isset($_SERVER['HTTP_VIA'])) {
       $ip = $_SERVER['HTTP_VIA'];
    }
    elseif (isset($_SERVER['REMOTE_ADDR'])) {
       $ip = $_SERVER['REMOTE_ADDR'];
    }
    else {
       $ip = "unknown";
    }
	return $ip;
}

/**
 * Carga en las variables de sesion los datos del usuario
 *
 * @param string $username login de usuario
 */
function cargar_user($username)
{
	global $ouser;
	$ouser=new user($username);

	$user = array(
		"id"		=> $ouser->id_usuario,
		"login"		=> $ouser->login,
		"name"		=> $ouser->nombre." ".$ouser->apellido,
		"mail"		=> $ouser->mail,
		"home"		=> $ouser->pagina_inicio,
		"res_width" => $_POST["resolucion_ancho"],
		"res_height"=>$_POST["resolucion_largo"],
		"acceso1"	=> $ouser->accesos[0],
		"acceso2"	=> $ouser->accesos[1],
		"acceso3"	=> $ouser->accesos[2],
		"acceso4"	=> $ouser->accesos[3],
    "acceso5"	=> $ouser->accesos[4],
    "acceso6"	=> $ouser->accesos[5],
    "acceso7"   => $ouser->accesos[6],
    "acceso8"   => $ouser->accesos[7],
    "acceso9"   => $ouser->accesos[8],
    "acceso10"   => $ouser->accesos[9],
    "acceso11"   => $ouser->accesos[10],
    "acceso12"   => $ouser->accesos[11],
		"debug"		=> "off"
//		"permisos"	=> permisos_cargar($ouser)
	);

	phpss_svars_set("_ses_user", $user);
}


/**
 * Guarda los permisos de un usuario en la tabla permisos_actualizar
 *
 * @param id de usuario o arreglo con id de usuarios
 */
function actualizar_permisos_bd($id) {
if (!is_array($id)) {
$array_usuario[0]=$id;
}
else $array_usuario=$id;

foreach($array_usuario as $key => $id_usuario) {
$sql_login="select login from sistema.usuarios where id_usuario=$id_usuario";
$res_login=sql($sql_login) or fin_pagina();
$login=$res_login->fields['login'];

$usuario=new user($login);

$arbol=new ArbolOfPermisos("root");	
$arbol->createMenu($usuario);
ob_start();
$arbol->saveXMLMenu();
$menu_new=ob_get_contents();
$menu_guardar=comprimir_variable($menu_new);
ob_clean();
//guardar permisos
$query="select data from permisos.permisos_sesion
		where id_usuario=$id_usuario";
$result=sql($query,"<br>Error <br>") or fin_pagina();

if ($result->RecordCount()>0) {
    $query="update permisos.permisos_sesion set data='$menu_guardar'
            where id_usuario=$id_usuario";
}
else {
	$query="insert into permisos.permisos_sesion (id_usuario,data) values($id_usuario,'$menu_guardar')";
}

sql($query,"<br>Error al insertar/actualizar los permisos actualizados para el usuario<br>") or fin_pagina();
}
}

//obtener los id de usuarios que tienen permiso
// al permiso que se esta borrando y a sus hijos

function obtener_id_usuarios($id) {
	$sql="select uname from permisos.permisos where id_permiso=$id";
	$res=sql($sql) or die();
	$nombre=$res->fields['uname'];
	ob_start();
    
	$arbol=new ArbolOfPermisos($nombre);
    $arbol->createTree();
    $arbol->saveXML();
    ob_end_clean(); 
    $hijos=array();
    $cant_hijos=$arbol->childcount();

    for ($i=0;$i<$cant_hijos;$i++){
      $hijos[$i]=$arbol->getChild($i)->get_id();
    }
    $hijos[$cant_hijos]=$id; //agrego el id del item seleccinado
   
    $sql="select distinct id_usuario
		  from 
		  (select distinct id_usuario from permisos.permisos_usuarios where id_permiso in (".join(",",$hijos).")
		  union
		  select distinct id_usuario from permisos.grupos_usuarios 
		  join permisos.permisos_grupos using (id_grupo)
		  where id_permiso in (".join(",",$hijos).")) as total";
    $res=sql($sql,"$sql") or fin_pagina();
    
    $usuarios=array();
    $i=0;
    while(!$res->EOF) {
       $usuarios[$i++]=$res->fields['id_usuario'];
       $res->MoveNext();	
    }
  
    return $usuarios;
    
}

/*******************************************
 ** Autenticar el usuario
 *******************************************/

//set_error_handler('errorHandler');
require_once(MOD_DIR."/permisos/permisos.class.php");
if (isset($_POST["loginform"])) {
	// Verificar que el ip sea valido
	$myip = getIP();
	$ip_permitida = false;
	foreach ( $ip_permitidas as $k=>$v ) {
		list($net,$mask)=split("/",$k);
		if (isIPIn($myip,$net,$mask)) {
			$ip_permitida = true;
		}
	}
	
	if (!$ip_permitida) {
		$acceso_remoto = false;
		$sql = "select login from usuarios where acceso_remoto=1";
		$result = sql($sql) or die("No se pudo verificar el usuario");
		while (!$result->EOF) {
			if ($result->fields['login'] == $_POST['username'])
				$acceso_remoto = true;
			$result->MoveNext();
		}
	}

	$status = phpss_login($_POST['username'], $_POST['password']);
	// check if the user is allowed access
	if ($status <= 0) {
	// check the error code
		switch ($status) {
			case PHPSS_LOGIN_AUTHFAIL:
				Error("Su nombre de usuario o contraseña son incorrectos");
				break;
			case PHPSS_LOGIN_IPACCESS_DENY:
				Error("No se permite iniciar sesión desde su dirección IP");
				break;
			case PHPSS_LOGIN_BRUTEFORCE_LOCK_ACCOUNT:
				Error("Esta cuenta ha sido bloqueada debido a varios intentos fallidos de inicio de sesión");
				break;
			case PHPSS_LOGIN_BRUTEFORCE_LOCK_SRCIP:
				Error("No se puede iniciar sesión desde su dirección IP porque hubieron muchos intentos fallidos de inicio de sesión");
				break;
			default:
				Error("Valor de retorno desconocido cuando se intentaba autenticar el usuario");
		}

	   include_once(ROOT_DIR."/login.php");
	   exit;
	}
	

	//Carga las variables de sesion para el usuario
	cargar_user($_POST["username"]);
    $sql = "SELECT dia,mes,anio,descripcion FROM feriados";

	$result = sql($sql) or fin_pagina();
	while (!$result->EOF) {
		$feriados[$result->fields["anio"]."-".$result->fields["mes"]."-".$result->fields["dia"]][] = $result->fields["descripcion"];
		$result->MoveNext();
	}
	phpss_svars_set("_ses_feriados", $feriados);

	unset($myip,$ip_permitida,$k,$v,$net,$mask,$para,$asunto,$texto);
	unset($user, $feriados, $sql, $result);
	header("Location: $html_root/index.php");
}


/*******************************************
 ** Variables Utiles
 *******************************************/

// Tamaño máximo de los archivos a subir
$max_file_size = get_cfg_var("upload_max_filesize");  // Por defecto deberia se 5 MB

// Para usar con los resultados boolean de la base de datos
$sino=array(
	"0" => "No",
	"f" => "No",
	"false" => "No",
	"NO" => "No",
	"n" => "No",
	"N" => "No",
	"1" => "Sí",
	"t" => "Sí",
	"true" => "Sí",
	"SI" => "Sí",
	"s" => "Sí",
	"S" => "Sí"
);
// Para el formato de fecha
$dia_semana = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
$meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

// El tipo de resultado debe ser n para que funcione la
// libreria phpss
db_tipo_res("d");

Autenticar();

$GLOBALS["parametros"] = decode_link($_GET["p"]);

if ($_POST['cambiar_usr'])
{
	$usr_login=$_ses_user['login'];
	cargar_user($_POST['new_login']);
	$_ses_user['original_usr']=$usr_login;
	phpss_svars_set("_ses_user", $_ses_user);
}
elseif ($parametros['restaurar_usr'])
{
	cargar_user($_ses_user['original_usr']);
//	phpss_svars_set("_ses_user", $_ses_user);
}
elseif (!$ouser)
	$ouser=new user($_ses_user['login']);
	
//if (is_array($_ses_user)) {
	//$_ses_user_login = $_ses_user["login"];
	//$_ses_user_name = $_ses_user["name"];
//	$_ses_user_mail = $_ses_user["mail"];
	//$_ses_user_home = $_ses_user["home"];
//	$_ses_user_acceso1 = $_ses_user["acceso1"];
//	$_ses_user_acceso2 = $_ses_user["acceso2"];
//	$_ses_user_acceso3 = $_ses_user["acceso3"];
//	$_ses_user_acceso4 = $_ses_user["acceso4"];
//    $_ses_user_acceso5 = $_ses_user["acceso5"];
//    $_ses_user_acceso6 = $_ses_user["acceso6"];
//    $_ses_user_acceso7 = $_ses_user["acceso7"];
//    $_ses_user_acceso8 = $_ses_user["acceso8"];
//    $_ses_user_acceso9 = $_ses_user["acceso9"];
//    $_ses_user_acceso10 = $_ses_user["acceso10"];
//    $_ses_user_acceso11 = $_ses_user["acceso11"];
//    $_ses_user_acceso12 = $_ses_user["acceso12"]; 
//}

//$GLOBALS["parametros"] = decode_link($_GET["p"]);

verificar_permisos();

define("lib_included","1");
require("fns.gacz.php");

//if ($_SERVER['SCRIPT_NAME']=='/permisos/modulos/admin/usuarios_perfil.php') {
//  echo "_ses_cambiar_perfil_usuario=".$_ses_cambiar_perfil_usuario;
//  echo "<br>parametros['cmd']=".$parametros['cmd'];
//  echo "<br>_POST[cmd]=".$_POST["cmd"];
//  echo "<br>_POST['cambiar_pagina_inicio']=". $_POST['cambiar_pagina_inicio'];
//  echo "<br>_ses_cambiar_acceso=". $_ses_cambiar_acceso;
//  echo "<br>_ses_pagina_inicio=". $_ses_pagina_inicio;
//}


if ($_ses_cambiar_perfil_usuario==1 && !($parametros['cmd']=="cambiar_acceso" || $parametros['cmd']=="actualizar_item" ||
      $_POST["cmd"] == "cambiar_acceso" || $_POST['cambiar_pagina_inicio'] || $_ses_pagina_inicio || $_ses_cambiar_acceso || 
      $_POST["guardar_perfil_uid_usuario >= 117suario"]))
    phpss_svars_set("_ses_cambiar_perfil_usuario", "");
   
if ($_ses_pagina_inicio) {
phpss_svars_set("_ses_pagina_inicio", "");

//phpss_svars_set("_ses_cambiar_perfil_usuario", 1); //para que no actualize la ruta cuando cambia la pagina
$link=encode_link($html_root."/modulos/admin/usuarios_perfil.php",array("pagina_item"=>$_SERVER["REQUEST_URI"],"cmd"=>"actualizar_item"));
header("Location:$link");
exit;
}

if ($_ses_cambiar_acceso) {
phpss_svars_set("_ses_cambiar_acceso", "");
//phpss_svars_set("_ses_cambiar_perfil_usuario", 1); //para que no actualize la ruta cuando cambia la pagina
$link=encode_link($html_root."/modulos/admin/usuarios_perfil.php",array("pagina_item"=>$_SERVER['REQUEST_URI'],"cmd"=>"cambiar_acceso"));
header("Location:$link");
exit;
}

function Mostrar_Header($FileName,$FileType,$FileSize) {
	Header("Cache-Control: post-check=0,pre-check=0");
	Header("Content-Type: $FileType");
	Header("Content-Transfer-Encoding: binary"); 
	Header("Content-Connection: close"); 
	Header("Content-Disposition: attachment; filename=\"$FileName\"");
	Header("Content-Description: $FileName");
	Header("Content-Length: $FileSize");
}

function validar_clave($clave,&$error_clave){
   if(strlen($clave) < 6){
      $error_clave = "La clave debe tener al menos 6 caracteres";
      return false;
   }
   if(strlen($clave) > 16){
      $error_clave = "La clave no puede tener más de 16 caracteres";
      return false;
   }
   if (!preg_match('`[a-z]`',$clave)){
      $error_clave = "La clave debe tener al menos una letra minúscula";
      return false;
   }
   if (!preg_match('`[A-Z]`',$clave)){
      $error_clave = "La clave debe tener al menos una letra mayúscula";
      return false;
   }
   if (!preg_match('`[0-9]`',$clave)){
      $error_clave = "La clave debe tener al menos un caracter numérico";
      return false;
   }
   $error_clave = "";
   return true;
} 

function es_letra($clave,&$error_clave){
if (!preg_match('`[A-Z]`',$clave)){
      return false;
   }
 else return true;
}

function es_cuie($cuie){
	$primera_letra=substr($cuie,0,1);
	$num_cuie=substr($cuie,1,5);
	$boolean_numero=es_numero($num_cuie);
	$boolean_letra=es_letra($primera_letra,$error_txt);
	if ($boolean_numero&&$boolean_letra)
		return true;
	else 
		return false;
	
}

function enviar_mail_html($para,$asunto,$contenido,$adjunto,$path,$adj=1){ 
 return enviar_mail($para,null,null,$asunto,$contenido,$adjunto,$path,'0');
}

function edad($edad){
list($anio,$mes,$dia) = explode("-",$edad);
$anio_dif = date("Y") - $anio;
$mes_dif = date("m") - $mes;
$dia_dif = date("d") - $dia;
if ($dia_dif < 0 || $mes_dif < 0)
$anio_dif--;
return $anio_dif;
}

function GetCountDaysBetweenTwoDates($DateFrom,$DateTo){
$HoursInDay = 24;
$MinutesInHour = 60;
$SecondsInMinutes = 60;
$SecondsInDay = (($SecondsInMinutes*$MinutesInHour)*$HoursInDay );
return intval(abs(strtotime($DateFrom) - strtotime($DateTo))/$SecondsInDay);
}

function bisiesto($anio_actual){
	    $bisiesto=false;
	    //probamos si el mes de febrero del año actual tiene 29 días
	      if (checkdate(2,29,$anio_actual))
	      {
	        $bisiesto=true;
	    }
	    return $bisiesto;
}

function dia_mes_anio($fecha_desde,$fecha_hasta){
	// separamos en partes las fechas
	$array_nacimiento = explode ( "-", $fecha_desde );
	$array_actual = explode ( "-", $fecha_hasta );
	
	$anos =  $array_actual[0] - $array_nacimiento[0]; // calculamos años
	$meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses
	$dias =  $array_actual[2] - $array_nacimiento[2]; // calculamos días
	
	//ajuste de posible negativo en $días
	if ($dias < 0)
	{
	    --$meses;
	
	    //ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual
	    switch ($array_actual[1]) {
	           case 1:     $dias_mes_anterior=31; break;
	           case 2:     $dias_mes_anterior=31; break;
	           case 3: 
	                if (bisiesto($array_actual[0]))
	                {
	                    $dias_mes_anterior=29; break;
	                } else {
	                    $dias_mes_anterior=28; break;
	                }
	           case 4:     $dias_mes_anterior=31; break;
	           case 5:     $dias_mes_anterior=30; break;
	           case 6:     $dias_mes_anterior=31; break;
	           case 7:     $dias_mes_anterior=30; break;
	           case 8:     $dias_mes_anterior=31; break;
	           case 9:     $dias_mes_anterior=31; break;
	           case 10:     $dias_mes_anterior=30; break;
	           case 11:     $dias_mes_anterior=31; break;
	           case 12:     $dias_mes_anterior=30; break;
	    }
	
	    $dias=$dias + $dias_mes_anterior;
	}
	
	//ajuste de posible negativo en $meses
	if ($meses < 0)
	{
	    --$anos;
	    $meses=$meses + 12;
	}
	return 	array ("anios"=>$anos, "meses"=> $meses, "dias"=> $dias);
}

function icono_sort($indice) {
	global $sort, $up;
	$ret = '';
	if (is_numeric($indice) && is_numeric($sort) && ($up == 0 || $up == 1)) {
		if ($sort == $indice) {
			if ($up == 0) {
				$ret = '<span class="glyphicon glyphicon-chevron-up"></span>';	
			}
			else {
				$ret = '<span class="glyphicon glyphicon-chevron-down"></span>';
			}
		} 
	}
	return $ret;
}
?>
