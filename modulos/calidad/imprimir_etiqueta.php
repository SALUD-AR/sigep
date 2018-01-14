<?
/*AUTOR: MAC

$Author: marco_canderle $
$Revision: 1.1 $
$Date: 2004/02/28 15:21:39 $
*/

include("../../config.php");
?>
<html>
<head>
<title>Imprimir</title>
</head>
<body onload="window.print(); window.close();">
PRODUCTO NO CONFORME: <?=$parametros["id"]?><br>
Fecha: <?=date("d/m/Y",mktime())?> - Usuario: <?=$_ses_user['name']?>
</body>
</html>