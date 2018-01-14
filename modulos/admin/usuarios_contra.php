<?php

include "../../config.php";
echo $html_header;

$passv=$_POST["passv"];
$passn=$_POST["passn"];
$passva=$_POST["passva"];
$cmd=$_POST["cmd"];
if ($cmd=="Cambiar <>") {
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
	$sql = "select password from phpss_account where username='".$_ses_user['login']."'";
    $rs = $db->Execute($sql);
    if (!(md5($passv)==$rs->fields["password"]))
          error("La contraseña no es la misma - ".$rs->fields["password"]);
    elseif (!($passn==$passva))
        error ("La validacion no es correcta.");
    elseif (!$passn)
        error ("Debe ingresar una nueva contraseña");
    else {
          $sql="UPDATE phpss_account SET password='".md5($passn)."' WHERE username='".$_ses_user['login']."'";
          if ($db->Execute($sql)) {
              aviso ("Los datos se Modificaron con éxito.");
          }
    }
}
?>
<table width=100% height=100% cellpadding=0 cellspacing=6 bgcolor=<? echo $bgcolor2?> bordercolor="#111111">
<tr>
<td>
<form action='usuarios_contra.php' method='POST'>
<table width=325 align=center cellpadding="3" style="border-width:1; border-collapse: collapse" bordercolor="#000000">
 <tr>
  <td align=center style="border-style: none; border-width: medium">
   <table width=313 border=0>
   <tr>
     <td colspan=2 width="307">
      <p class=titulo style='margin-bottom: 9;margin-left:2; margin-right:2; margin-top:2' align=center>
      <b><font face="Trebuchet MS" color='#559955'>Cambiar mi contraseña</font></b></p>
      <p align="justify" style="margin-left: 2; margin-right: 2; margin-top: 2; margin-bottom: 9">
      <font size="2" face="Trebuchet MS">Se recomienda que use una contraseña de 6 o m&aacute;s caracteres,
                    en lo posible use caracteres alfabéticos y números, gracias.
      </font>
     </td>
    </tr>
    <tr>
     <td width="118">
      <div align="right">
       <font face="Trebuchet MS" size="2">Contraseña actual:</font>
      </div>
     </td>
     <td width="185">
      <input type='password' name=passv size=20>
     </td>
    </tr>
    <tr>
     <td width="118">
      <div align="right">
       <font face="Trebuchet MS" size="2">Contraseña nueva: </font>
      </div>
     </td>
     <td width="185">
      <input type='password' name=passn size=20>
     </td>
    </tr>
    <tr>
                  <td width="118">
                  <p align="right"><font size="2" face="Trebuchet MS">Validar contraseña:</font> </td>
     <td width="185">
      <input type='password' name=passva size=20>
     </td>
    </tr>
    <tr>
     <td colspan=2 align=center width="307">
      <input type='submit' name='cmd' value='Cambiar <>'>
      <hr>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
</form>
</td>
</tr>
</table>