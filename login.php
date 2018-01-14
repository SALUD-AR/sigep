<?
if (ereg("/login.php",$_SERVER["SCRIPT_NAME"])) {
	$tmp=explode("/login.php",$_SERVER["SCRIPT_NAME"]);
	$html_root = $tmp[0];
}

require_once "lib/Browser.php";
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

?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Sistema de Gestión Programa Sumar</title>
<style type="text/css">
<!--
.Estilo1 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #045279;
}
.Estilo2 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.Estilo4 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000000;
	font-weight: bold;
}
body {
	background-image: url(imagenes/fondo3.jpg);
	background-color: #A2A2A2;
}
.Estilo7 {font-size: 12px; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; color: #FFFFFF; }
.Estilo8 {
	font-size: 16px;
	font-weight: bold;
	font-family: Arial, Helvetica, sans-serif;
	color: #FFFFFF;
}
.Estilo10 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 14px;
	font-weight: bold;
	color: #045279;
}
a:link {
	text-decoration: none;
	color: #333333;
}
a:visited {
	text-decoration: none;
	color: #333333;
}
a:hover {
	text-decoration: none;
	color: #FF3300;
}
a:active {
	text-decoration: none;
	color: #333333;
}
-->
</style>
<link rel="icon" href="<? echo ((($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER['HTTP_HOST']).$html_root; ?>/favicon.ico">
<link REL='SHORTCUT ICON' HREF='<? echo ((($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER['HTTP_HOST']).$html_root; ?>/favicon.ico'>

<link type='text/css' href='<? echo $html_root; ?>/lib/estilos.css' REL='stylesheet'>
</head>

<body style="overflow:hidden;" onLoad="javascript: document.frm.username.focus();" topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
<?if (!BROWSER_OK){?>
  <script type="text/javascript">window.open ("https://www.google.com/chrome/browser/desktop/index.html","_blank")</script>
  <script type="text/javascript">alert ("NAVEGADOR NO COMPATIBLE. Recomendamos Instalar Google Chrome.")</script>
<?}?>
<form action='index.php' method='post' name='frm'>
<input type="hidden" name="resolucion_ancho" value="">
<input type="hidden" name="resolucion_largo" value="">
<div align="center">
<br>
<table width="586" border="0" align="center" cellspacing="10">
  <tr>
    <td><img src="imagenes/sigep2.jpg" alt="SISTEMA DE GESTION DEL PROGRAMA SUMAR" width="797" height="195"></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0">
        <tr>
          <td width="62%" align="center"><table width="100" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td nowrap><table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td nowrap><table width="100%" border="0" cellpadding="5" cellspacing="0" bgcolor="#FFFFFF">
                    <tr>
                      <td nowrap><span class="Estilo1">Accesos directos:</span></td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                  <td><img src="imagenes/somb.png" width="100%" height="11"></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td><table width="357" border="0" cellspacing="5">
                  <tr>
                    <td width="26" valign="middle" nowrap><img src="imagenes/flech.png" alt="&gt;" width="26" height="26" border="0"></td>
                    <td width="312" valign="middle" nowrap=""><span class="Estilo4"><a href="http://www.salud.sanluis.gov.ar/">Sitio web Ministerio de Salud</a></span></td>
                  </tr>
                  <tr>
                    <td valign="middle" nowrap><img src="imagenes/flech.png" alt="&gt;" width="26" height="26" border="0"></td>
                    <td valign="middle" nowrap="" class="Estilo2"><a href="http://agenciasanluis.com/"><strong>Sitio web Agencia de Noticias</strong></a></td>
                  </tr>
                  <tr>
                    <td valign="middle" nowrap><img src="imagenes/flech.png" alt="&gt;" width="26" height="26" border="0"></td>
                    <td valign="middle" nowrap="" class="Estilo2"><a href="http://www.capacitacionsumar.msal.gov.ar/"><strong>Plataforma de capacitacion</strong></a></td>
                  </tr>
                  
              </table></td>
            </tr>
          </table></td>
          <td width="38%" align="center"><p>&nbsp;</p>
            <table width="100" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="1" align="right"><img src="imagenes/caja1.png" width="18" height="174"></td>
              <td background="imagenes/caja2.png"><form method="POST">
                <table width="100" border="0" cellspacing="7">
                  <tr>
                    <td colspan="2"><div align="center" class="Estilo8">Ingreso al Sistema</div></td>
                    </tr>
                  <tr>
                    <td><div align="right" class="Estilo7">Usuario:</div></td>
                    <td><span style="text-align: right">
                      <INPUT name=username AUTOCOMPLETE="off" style="border-style: solid; border-width: 1px" size="18" tabindex="1">
                    </span></td>
                  </tr>
                  <tr>
                    <td><div align="right" class="Estilo7">Contrase&ntilde;a:</div></td>
                    <td><span style="text-align: right">
                      <INPUT type=password name=password AUTOCOMPLETE="off" style="border-style: solid; border-width: 1px" size="18" tabindex="2">
                    </span></td>
                  </tr>
                  <tr>
                    <td colspan="2" align="center"><label>
                      <div align="center"><span style="text-align: right">
                          <INPUT name="loginform" type="submit" class="Estilo10"value="Ingresar">
                        </span></div>
                    </label></td>
                  </tr>
                </table>
                </form>                </td>
              <td width="1" align="left"><img src="imagenes/caja3.png" width="18" height="174"></td>
            </tr>
          </table>
</td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#006A9E"><table width="100" border="0" align="center" cellpadding="0" cellspacing="10">
          <tr>
            <td nowrap><span class="Estilo7">2014 <strong>&copy;</strong> Copyright MINISTERIO DE SALUD - PROVINCIA DE SAN LUIS</span></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><img src="imagenes/somb.png" width="100%" height="11"></td>
      </tr>
    </table>
      </td>
  </tr>
</table>
</div>
<script>
//guardamos la resolucion de la pantalla del usuario en los hiddens para despues recuperarlas
//y guardarlas en las variable de sesion $_ses_user
document.all.resolucion_ancho.value=screen.width;
document.all.resolucion_largo.value=screen.height;

</script>
</body>
</form>
</html>
