<?php
require_once("../../config.php");

$id_contacto_licitacion=$parametros["id_contacto_licitacion"];

global $db;

$sql="select * from contactos_licitacion where id_contactos_licitacion=$id_contacto_licitacion";
$resultado=$db->execute($sql) or die($db->ErrorMsg()."<br>"."Error: ".$sql);
?>
<HTML>
<HEAD>
<meta Name="generator" content="PHPEd Version 3.2 (Build 3220 )   ">
<title>Información del Contacto</title>
<meta Name="author" content="">
<?php echo "<link rel=stylesheet type='text/css' href='$html_root/lib/estilos.css'>"; ?>
<link rel="SHORTCUT ICON"  href="/path-to-ico-file/logo.ico">
</HEAD>
<BODY bgcolor="<?echo $bgcolor2; ?>">
<table  width="70%" align="center" border="1" cellspacing="1" cellpadding="2" bordercolor="#000000" >
<tr id="mo">
<td colspan='2' align="center" >
<b>
Información Del contacto
</td>
</tr>
<tr height='10%'>
  <td width="25%" id="ma2"  >
  <b>
  Nombre:
  </td>
  <td align="left" bgcolor="<? echo $bgcolor3;?>">
  &nbsp;
  <b>
  <?
  echo $resultado->fields["nombre"];
  ?>
  </td>
</tr>
<tr height='10%'>
  <td  id="ma2" >
  <b>
  Teléfono:
  </td>
  <td align="left" bgcolor="<? echo $bgcolor3;?>">
  &nbsp;
  <b>
  <?
  echo $resultado->fields["tel"];
  ?>
  </td>
</tr>
<tr height='10%'>
  <td  id="ma2">
  <b>
  Dirección:
  </td>
  <td align="left" bgcolor="<? echo $bgcolor3;?>">
  &nbsp;
  <b>
  <?
  echo $resultado->fields["direccion"];
  ?>
  </td>
</tr>
<tr height='10%'>
  <td  id="ma2">
  <b>
   Provincia:
   </td>
  <td align="left" bgcolor="<? echo $bgcolor3;?>">
  &nbsp;
  <b>
  <?
  echo $resultado->fields["provincia"];
  ?>
  </td>
</tr>
<tr height='10%'>
  <td  id="ma2">
  <b>
  Localidad:
  </td>
  <td align="left" bgcolor="<? echo $bgcolor3;?>">
  &nbsp;
  <b>
  <?
  echo $resultado->fields["localidad"];
  ?>
  </td>
</tr>
<tr height='10%'>
  <td id="ma2">
  <b>
  C.P.:
  </td>
  <td align="left" bgcolor="<? echo $bgcolor3;?>">
  &nbsp;
  <b>
  <?
  echo $resultado->fields["cod_postal"];
  ?>
  </td>
</tr>
<tr height='10%'>
  <td id="ma2">
  <b>
  Mail:
  </td>
  <td align="left" bgcolor="<? echo $bgcolor3;?>">
  &nbsp;
  <b>
  <?
  echo $resultado->fields["mail"];
  ?>
  </td>
</tr>
<tr height='10%'>
  <td id="ma2">
  <b>
  Fax:
  </td>
  <td align="left" bgcolor="<? echo $bgcolor3;?>">
  &nbsp;
  <b>
  <?
  echo $resultado->fields["fax"];
  ?>
  </td>
</tr>
<tr height='10%'>
  <td id="ma2">
  <b>
  ICQ:
  </td>
  <td align="left" bgcolor="<? echo $bgcolor3;?>">
   &nbsp;
   <b>
  <?
  echo $resultado->fields["icq"];
  ?>
  </td>
</tr>
<tr height='10%'>
  <td id="ma2" colspan='2' >
  <b>
  Observaciones:
  </td>
</tr>
<tr>
  <td colspan='2' align="left" bgcolor="<? echo $bgcolor3;?>">
  &nbsp;
  <b>
  <?
  echo $resultado->fields["observaciones"];
  ?>
  </td>
</tr>
<tr>
  <td colspan='2' align="center">
  <input type="button" name="boton" value="Cerrar"  onclick="window.close()">
  </td>
</tr>
</table>
</BODY>
</HTML>