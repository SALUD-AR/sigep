<?

require_once("../../config.php");

$oSelect=new HtmlOptionList("new_login",30,"width:400px");
$sql_tmp = "SELECT usuarios.id_usuario,usuarios.login,";
//$sql_tmp .= "usuarios.id_usuario||' '||usuarios.nombre ||' '|| usuarios.apellido||' ('||usuarios.login||')' as nombre,";
$sql_tmp .= "usuarios.nombre ||' '|| usuarios.apellido||' ('||usuarios.login||')' as nombre,";
$sql_tmp .= "usuarios.comentarios ";
$sql_tmp .= "FROM usuarios ";
$sql_tmp .= "join phpss_account on phpss_account.username=usuarios.login ";
$sql_tmp .= "where active='true' ";
$sql_tmp .= "order by nombre ";

$r=sql($sql_tmp) or fin_pagina();
$oSelect->optionsFromResulset($r,array("value"=>"login","text"=>"nombre"));
if ($oSelect->selectedIndex==-1)	$oSelect->selectedIndex=0;
echo $html_header;

?>
<form target="_top" action="<?=$html_root."/index.php" ?>" method="post">
<table width="100%" border="0" align="center">
<tr>
	<td align="center">
	<? $oSelect->toBrowser(); ?>
	</td>
</tr>
<tr>
	<td align="center"><br><input type="submit" name="cambiar_usr" value="Cambiar al usuario" ></td>
</tr>
</table>
</form>
<?
fin_pagina();
?>