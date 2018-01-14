<?php

require_once "../../config.php";
require_once("../permisos/permisos.class.php");

echo $html_header;

?>
<SCRIPT>
function long_firma(firma1,firma2,firma3)
{
 if(firma1.length>30)
 {alert('La longitud del primer campo de firma no puede superar los 30 caracteres.');
  return false;
 }
 if(firma2.length>30)
 {alert('La longitud del segundo campo de firma no puede superar los 30 caracteres.');
  return false;
 }
 if(firma3.length>30)
 {alert('La longitud del tercer campo de firma no puede superar los 30 caracteres.');
  return false;
 }
 return true;
}
</SCRIPT>
<?

if ($_POST['cambiar_pagina_inicio']){	
	$nombre_item = $_POST["nombre_item"];
	phpss_svars_set("_ses_nombre_item",$nombre_item);
	phpss_svars_set("_ses_pagina_inicio",1);
	phpss_svars_set("_ses_cambiar_perfil_usuario", 1); //para que no actualice la ruta cuando cambia la pagina
	phpss_svars_set("_ses_recargar", 1); 
	echo $html_header;
	echo "
		<table width=50% height=100% border=2 align=center cellpadding=20 cellspacing=0 bordercolor=$bgcolor3>
		  <tr><td height=50%>&nbsp;</td></tr>
		  <tr>
			<td align=center bordercolor=#FF0000 bgcolor=#FFFFFF>
				<b><font size=3>Seleccione la nueva página<br>de inicio en el menú...</font></b>
			</td>
		  </tr>
		  <tr><td height=50%>&nbsp;</td></tr>
		</table>
		</body>
		</html>\n";
	exit;
}

if ($_POST["cmd"] == "cambiar_acceso") {
	$nombre_item = $_POST["nombre_item"];
	phpss_svars_set("_ses_nombre_item",$nombre_item);
	phpss_svars_set("_ses_cambiar_acceso",1);
	phpss_svars_set("_ses_cambiar_perfil_usuario", 1); //para que no actualice la ruta cuando cambia la pagina
	phpss_svars_set("_ses_recargar", 1); 
	echo $html_header;
	echo "
		<table width=50% height=100% border=2 align=center cellpadding=20 cellspacing=0 bordercolor=$bgcolor3>
		  <tr><td height='50%'>&nbsp;</td></tr>
		  <tr>
			<td align=center bordercolor=#FF0000 bgcolor=#FFFFFF>
				<b><font size=3>Seleccione la nueva página<br>de acceso directo en el menú...</font></b>
			</td>
		  </tr>
		  <tr><td height='50%'>&nbsp;</td></tr>
		</table>
		</body>
		</html>\n";
	exit;
}

if ($_POST["guardar_perfil_usuario"]) {
	$apellido = $_POST["apellido"] or Error("Debe ingresar el apellido");
	$nombre = $_POST["nombre"] or Error("Debe ingresar el nombre");
	$direccion = $_POST["direccion"];
	$telefono = $_POST["telefono"];
	$celular = $_POST["celular"];
	$mail = $_POST["mail"];
	$firma1 = $_POST["firma1"];
	$firma2 = $_POST["firma2"];
	$firma3 = $_POST["firma3"];
	$pagina_inicio = $_POST["pagina_inicio"];
	$acceso1=$_POST["acceso1"];
	$acceso2=$_POST["acceso2"];
	$acceso3=$_POST["acceso3"];
	$acceso4=$_POST["acceso4"];
    $acceso5=$_POST["acceso5"];
    $acceso6=$_POST["acceso6"];
    $acceso7=$_POST["acceso7"];
    $acceso8=$_POST["acceso8"];
    $acceso9=$_POST["acceso9"];
    $acceso10=$_POST["acceso10"];
    $acceso11=$_POST["acceso11"];
    $acceso12=$_POST["acceso12"];
	if (!strstr($mail,"@") && $mail) {
		Error("El mail esta mal ingresado.");
	}
	if (!$error) {
		  $sql = "UPDATE usuarios SET ";
		  $sql .= "apellido='$apellido',
				   nombre='$nombre',
				   direccion='$direccion',
				   telefono='$telefono',
				   celular='$celular',
				   mail='$mail',
				   pagina_inicio='$pagina_inicio',
		           firma1='$firma1',
		           firma2='$firma2',
		           firma3='$firma3',
		           acceso1='$acceso1',
		           acceso2='$acceso2',
		           acceso3='$acceso3',
                   acceso4='$acceso4',
                   acceso5='$acceso5',
                   acceso6='$acceso6',
                   acceso7='$acceso7',
                   acceso8='$acceso8',
                   acceso9='$acceso9',
		           acceso10='$acceso10',
                   acceso11='$acceso11',
                   acceso12='$acceso12'";
		  $sql .= " WHERE login='".$_ses_user['login']."'";
		sql($sql) or fin_pagina();
//		include "../../lib/gacl_api.class.php";
//		$gacl_api = new gacl_api();
//		$id=$gacl_api->get_object_id("usuarios",$_ses_user_login,"aco");
//		if ($gacl_api->edit_object($id,'usuarios',$nombre ." ". $apellido,$_ses_user_login,1,0,'aco')) {
//			aviso ("Los datos se modificaron con éxito.");
//		}
		phpss_svars_set("_ses_nombre_item","");
		phpss_svars_set("_ses_pagina_inicio", "");
	    phpss_svars_set("_ses_cambiar_acceso", "");
	           
	    if ($_ses_recargar==1) {
	  	   echo "<html><head><script language=javascript>\n";
	   	   echo "alert('Los datos se modificaron con éxito.');\n";
	       echo "if(parent!=null && parent!=self) { parent.location='$html_root/index.php'; }\n";
	       echo "else { window.location='$html_root/index.php'; }\n";
	       echo "</script></head></html>";
	       phpss_svars_set("_ses_recargar", "");
        }	
	    else {
	     aviso ("Los datos se modificaron con éxito.");
	    }  
	    
	}
}

$usuario=new user($_ses_user['login']);

$pagina_inicio =$_ses_user["home"];
$acceso1=$_ses_user["acceso1"];
$acceso2=$_ses_user["acceso2"];
$acceso3=$_ses_user["acceso3"];
$acceso4=$_ses_user["acceso4"];
$acceso5=$_ses_user["acceso5"];
$acceso6=$_ses_user["acceso6"];
$acceso7=$_ses_user["acceso7"];
$acceso8=$_ses_user["acceso8"];
$acceso9=$_ses_user["acceso9"];
$acceso10=$_ses_user["acceso10"];
$acceso11=$_ses_user["acceso11"];
$acceso12=$_ses_user["acceso12"];

if ($parametros['cmd']=='actualizar_item' || $parametros['cmd']=='cambiar_acceso') {
	$div=ereg_replace("^$html_root","",$parametros['pagina_item']);
	$div=explode("/",$div,4);  
	
    if ($parametros['cmd']=='actualizar_item')
	   $pagina_inicio=ereg_replace("\.php","",$div[3]);
	elseif ($parametros['cmd']=='cambiar_acceso')  {    
	    $pagina_item=ereg_replace("\.php","",$div[3]); 
	    
	    $nombre_item=$_ses_nombre_item;
	    phpss_svars_set("_ses_nombre_item", "");
	    $$nombre_item = $pagina_item;
	}
	$_ses_user["home"] = $pagina_inicio;
	
	$_ses_user["acceso1"] = $acceso1;
	$_ses_user["acceso2"] = $acceso2;
	$_ses_user["acceso3"] = $acceso3;
	$_ses_user["acceso4"] = $acceso4;
    $_ses_user["acceso5"] = $acceso5;
    $_ses_user["acceso6"] = $acceso6;
    $_ses_user["acceso7"] = $acceso7;
    $_ses_user["acceso8"] = $acceso8;
    $_ses_user["acceso9"] = $acceso9;
    $_ses_user["acceso10"] = $acceso10;
    $_ses_user["acceso11"] = $acceso11;
    $_ses_user["acceso12"] = $acceso12;
      
	phpss_svars_set("_ses_user", $_ses_user);
	$pagina_inicio =$_ses_user["home"];
	$acceso1=$_ses_user["acceso1"];
	$acceso2=$_ses_user["acceso2"];
	$acceso3=$_ses_user["acceso3"];
	$acceso4=$_ses_user["acceso4"];
    $acceso5=$_ses_user["acceso5"];
    $acceso6=$_ses_user["acceso6"];
    $acceso7=$_ses_user["acceso7"];
    $acceso8=$_ses_user["acceso8"];
    $acceso9=$_ses_user["acceso9"];
    $acceso10=$_ses_user["acceso10"];
    $acceso11=$_ses_user["acceso11"];
    $acceso12=$_ses_user["acceso12"];
	
   
    Aviso("Haga click en Guardar para guardar los cambios");
    
    phpss_svars_set("_ses_cambiar_perfil_usuario", 1); //para que no actualize la ruta cuando cambia la pagina    
   
}

?>
<form action='usuarios_perfil.php' method='POST' name=frm id=frm>
<input type="hidden"  name="cmd" value="">
<input type="hidden"  name="nombre_item" value="">

<table width='100%' align='center' border="0" cellpadding="2" cellspacing="3">

 <tr>
  <td style="border-style: none; border-width: medium">
   <table width=100% border=0>
	<tr>
		 <td width="50%"> <div align="right">
					<font face="Trebuchet MS" size="2+">Usuario: </font> </div></td>
	     <td width="50%"><font face="Trebuchet MS" size="2+">
	  <? echo $usuario->login;?>
	   </font> </td>
	</tr>
   </table>
  </td>
 </tr>
 <tr>
  <td align=center style="border-style: none; border-width: medium">
	<p class=menutitulo style='margin-bottom: 0;'><b>Datos Personales</b></p>
  </td>
 </tr>
 <tr>
  <td style="border-style: none; border-width: medium">
   <table width=60% align="center" border=0>
	<tr>
				  <td width=40%> <div align="right"><font face="Trebuchet MS" size="2"><b><font color=red>*</font></b> Apellido:
					</font> </div></td>
	 <td width=60%>
	  <input type='text' name=apellido value='<?=$usuario->apellido;?>' size=30>
	 </td>
	</tr>
	<tr>
				  <td> <div align="right"><font face="Trebuchet MS" size="2"><b><font color=red>*</font></b> Nombre:
					</font> </div></td>
	 <td>
	  <input type='text' name=nombre value='<?=$usuario->nombre;?>' size=30>
	 </td>
	</tr>
	<tr>
				  <td> <div align="right"><font face="Trebuchet MS" size="2">Dirección:
					</font> </div></td>
	 <td>
	  <input type='text' name=direccion value='<?=$usuario->direccion;?>' size=30>
	 </td>
	</tr>
	<tr>
				  <td> <div align="right"><font face="Trebuchet MS" size="2">Teléfono:
					</font> </div></td>
	 <td>
	  <input type='text' name=telefono value='<?=$usuario->telefono;?>' size=30>
	 </td>
	</tr>
	<tr>
				  <td> <div align="right"><font face="Trebuchet MS" size="2">Celular:
					</font> </div></td>
	 <td>
	  <input type='text' name=celular value='<?=$usuario->celular;?>' size=30>
	 </td>
	</tr>
	<tr>
				  <td> <div align="right"><font face="Trebuchet MS" size="2">E-M@il:
					</font> </div></td>
	 <td>
	  <input type='text' name=mail value='<?=$usuario->mail;?>' size=30>
	 </td>
	</tr>
</table>
 	<table width="43%" align="center">
	  <tr>
	  <td width="15%">&nbsp;</td>
	  <td align="center" width="85%" bgcolor="<?=$bgcolor1?>"><font color="white"><b>Firma</b></font></td>
      </tr>
      <tr>
       <td></td> 
       <td bgcolor="<?=$bgcolor3?>" align="center">
	    <input type="text" name="firma1" value="<?=$usuario->firma1;?>" size=30><br>
	    <input type="text" name="firma2" value="<?=$usuario->firma2;?>" size=30><br>
	    <input type="text" name="firma3" value="<?=$usuario->firma3;?>" size=30>
	   </td>
	  </tr>
	 </table> 

   <table width=60% align="center" border=0>
     <tr> <td> 
<div align="right">
        <img src='<?php echo "$html_root/imagenes/ayuda.gif" ?>' border="0" alt="ayuda" onClick="abrir_ventana('<?php echo "$html_root/modulos/ayuda/admin/pag_inicio.htm" ?>', 'PERFIL DE USUARIO')" >
    </div>           

          </td></tr>
	 <tr>
				  <td> <div align="right"><font face="Trebuchet MS" size="2">Página de Inicio:
					</font> </div></td>
	 <td>
	 <table border=0 cellspacing=0 cellpadding=0><tr>
	 <td>
	 <input type='hidden' name=pagina_inicio value='<? echo $pagina_inicio; ?>'>
	
	 <b><?=$usuario->permisos->getpathByName($pagina_inicio).$usuario->permisos->getdescByName($pagina_inicio); ?></b>
	  &nbsp;&nbsp;
	 </td>
	  <td>
	   <input type='submit' name=cambiar_pagina_inicio value='Cambiar...' onclick="document.all.cmd.value='';document.all.nombre_item.value='pagina_inicio'">
	  </td>
	 
	 </tr>
	 </table>
	 </td>
	</tr>
	 
	 <input type='hidden' name=acceso1 value='<? echo $acceso1; ?>'>
     <input type='hidden' name=acceso2 value='<? echo $acceso2; ?>'>
     <input type='hidden' name=acceso3 value='<? echo $acceso3; ?>'>
     <input type='hidden' name=acceso4 value='<? echo $acceso4; ?>'>
     <input type='hidden' name=acceso5 value='<? echo $acceso5; ?>'>
     <input type='hidden' name=acceso6 value='<? echo $acceso6; ?>'>
     <input type='hidden' name=acceso7 value='<? echo $acceso7; ?>'>
     <input type='hidden' name=acceso8 value='<? echo $acceso8; ?>'>
     <input type='hidden' name=acceso9 value='<? echo $acceso9; ?>'>
     <input type='hidden' name=acceso10 value='<? echo $acceso10; ?>'>
     <input type='hidden' name=acceso11 value='<? echo $acceso11; ?>'>
     <input type='hidden' name=acceso12 value='<? echo $acceso12; ?>'>

   <table align="center" border='1' cellpadding='3' cellspacing='0'>
	<tr id=mo>
		<td align='center' colspan='6'><b>Accesos Directos</b></td>
	</tr>
    <tr bgcolor=<?=$bgcolor_out?>>
		<td align='center' valign='top' width=90>
			<img align='center' src='<?echo $html_root;?>/imagenes/1.jpg' border='0'>
			<br><b><?=$usuario->permisos->getdescByName($acceso1);?></b>
		</td>
		<td align='center' valign='top' width=90>
			<img align='center' src='<?echo $html_root;?>/imagenes/2.jpg' border='0'>
			<br><b><?=$usuario->permisos->getdescByName($acceso2);?></b>
		</td>
		<td align='center' valign='top' width=90>
			<img align='center' src='<?echo $html_root;?>/imagenes/3.jpg' border='0'>
			<br><b><?=$usuario->permisos->getdescByName($acceso3);?></b>
		</td>
		<td align='center' valign='top' width=90>
			<img align='center' src='<?echo $html_root;?>/imagenes/4.jpg' border='0'>
			<br><b><?=$usuario->permisos->getdescByName($acceso4);?></b>
		</td>
                <td align='center' valign='top' width=90>
			<img align='center' src='<?echo $html_root;?>/imagenes/5.jpg' border='0'>
			<br><b><?=$usuario->permisos->getdescByName($acceso5);?></b>
		</td>
                <td align='center' valign='top' width=90>
			<img align='center' src='<?echo $html_root;?>/imagenes/6.jpg' border='0'>
			<br><b><?=$usuario->permisos->getdescByName($acceso6);?></b>
		</td>
        </tr>

        <tr bgcolor="<?=$bgcolor3?>">
            <td align="center"><input name="cambiar_acceso1" type="submit" value="<?if ($acceso1!='')echo 'Cambiar'; else echo'Agregar';?>" onclick="document.all.cmd.value='cambiar_acceso';document.all.nombre_item.value='acceso1'"></td>
            <td align="center"><input name="cambiar_acceso2" type="submit" value="<?if ($acceso2!='')echo 'Cambiar'; else echo'Agregar';?>" onclick="document.all.cmd.value='cambiar_acceso';document.all.nombre_item.value='acceso2'"></td>
            <td align="center"><input name="cambiar_acceso3" type="submit" value="<?if ($acceso3!='')echo 'Cambiar'; else echo'Agregar';?>" onclick="document.all.cmd.value='cambiar_acceso';document.all.nombre_item.value='acceso3'"></td>
            <td align="center"><input name="cambiar_acceso4" type="submit" value="<?if ($acceso4!='')echo 'Cambiar'; else echo'Agregar';?>" onclick="document.all.cmd.value='cambiar_acceso';document.all.nombre_item.value='acceso4'"></td>
            <td align="center"><input name="cambiar_acceso5" type="submit" value="<?if ($acceso5!='')echo 'Cambiar'; else echo'Agregar';?>" onclick="document.all.cmd.value='cambiar_acceso';document.all.nombre_item.value='acceso5'"></td>
            <td align="center"><input name="cambiar_acceso6" type="submit" value="<?if ($acceso6!='')echo 'Cambiar'; else echo'Agregar';?>" onclick="document.all.cmd.value='cambiar_acceso';document.all.nombre_item.value='acceso6'"></td>
  		</tr>

        <tr bgcolor=<?=$bgcolor_out?>>    
          <td align='center' valign='top' width=90 >
            <img align='center' src='<?echo $html_root;?>/imagenes/7.jpg' border='0'>
            <br><b><?=$usuario->permisos->getdescByName($acceso7);?></b>
        </td>
                <td align='center' valign='top' width=90>
            <img align='center' src='<?echo $html_root;?>/imagenes/8.jpg' border='0'>
            <br><b><?=$usuario->permisos->getdescByName($acceso8);?></b>
        </td>
                <td align='center' valign='top' width=90>
            <img align='center' src='<?echo $html_root;?>/imagenes/9.jpg' border='0'>
            <br><b><?=$usuario->permisos->getdescByName($acceso9);?></b>
        </td>
                <td align='center' valign='top' width=90>
            <img align='center' src='<?echo $html_root;?>/imagenes/10.jpg' border='0'>
            <br><b><?=$usuario->permisos->getdescByName($acceso10);?></b>
        </td>
                <td align='center' valign='top' width=90>
            <img align='center' src='<?echo $html_root;?>/imagenes/11.jpg' border='0'>
            <br><b><?=$usuario->permisos->getdescByName($acceso11);?></b>
        </td>
                <td align='center' valign='top' width=90 >
            <img align='center' src='<?echo $html_root;?>/imagenes/12.jpg' border='0'>
            <br><b><?=$usuario->permisos->getdescByName($acceso12);?></b>
        </td>
    </tr>
  <tr bgcolor="<?=$bgcolor3?>">
    <td align="center"><input name="cambiar_acceso7" type="submit" value="<?if ($acceso7!='')echo 'Cambiar'; else echo'Agregar';?>" onclick="document.all.cmd.value='cambiar_acceso';document.all.nombre_item.value='acceso7'"></td>
    <td align="center"><input name="cambiar_acceso8" type="submit" value="<?if ($acceso8!='')echo 'Cambiar'; else echo'Agregar';?>" onclick="document.all.cmd.value='cambiar_acceso';document.all.nombre_item.value='acceso8'"></td>
    <td align="center"><input name="cambiar_acceso9" type="submit" value="<?if ($acceso9!='')echo 'Cambiar'; else echo'Agregar';?>" onclick="document.all.cmd.value='cambiar_acceso';document.all.nombre_item.value='acceso9'"></td>
    <td align="center"><input name="cambiar_acceso10" type="submit" value="<?if ($acceso10!='')echo 'Cambiar'; else echo'Agregar';?>" onclick="document.all.cmd.value='cambiar_acceso';document.all.nombre_item.value='acceso10'"></td>
    <td align="center"><input name="cambiar_acceso11" type="submit" value="<?if ($acceso11!='')echo 'Cambiar'; else echo'Agregar';?>" onclick="document.all.cmd.value='cambiar_acceso';document.all.nombre_item.value='acceso11'"></td>
    <td align="center"><input name="cambiar_acceso12" type="submit" value="<?if ($acceso12!='')echo 'Cambiar'; else echo'Agregar';?>" onclick="document.all.cmd.value='cambiar_acceso';document.all.nombre_item.value='acceso12'"></td>
  </tr>
 </table>
	</tr>
	<tr>
	 <td colspan=2 align=center><br>
       <input type='submit' name='guardar_perfil_usuario' value='Guardar &gt;&gt;' onclick="return long_firma(document.all.firma1.value,document.all.firma2.value,document.all.firma3.value)">	
	 </td>
	</tr>
   </table>
  </td>
 </tr>
</table>
</form>
<?
fin_pagina();
?>