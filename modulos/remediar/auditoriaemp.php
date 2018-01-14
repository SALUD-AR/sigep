<?php

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

$sql_tmp="select * from (
				select nombre, apellido,login,usuario_carga,count(login) as cantidad
							from uad.remediar_x_beneficiario 
							left join sistema.usuarios ON (cast(usuario_carga as integer)=id_usuario)
				group by nombre, apellido,login,usuario_carga) as a
				where cantidad <> 0";
$result=sql($sql_tmp) or fin_pagina();

$sql_cuidado_sexual="select count(*) from (
				select nombre, apellido,login,count(login) as cantidad
							from uad.remediar_x_beneficiario 
							left join sistema.usuarios ON (cast(usuario_carga as integer)=id_usuario)
				group by nombre, apellido,login) as a
				where cantidad <> 0";
$res_cuidado_sexual= sql($sql_cuidado_sexual) or die;
$cuidado_sexual=$res_cuidado_sexual->fields['total'];

echo $html_header;
?>
<form name=form1 method=post action="auditoriaemp.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total: <?=$result->RecordCount();?> </b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id=mo>Login</td>      	
    <td align=right id=mo>Nombre</td>      	
    <td align=right id=mo>Apellido</td>      	
    <td align=right id=mo>Cantidad Cargado</td>    
  </tr>
  <?   
  while (!$result->EOF) {
	$ref = encode_link("auditoriaemp_detalle.php",array("usuario_carga"=>$result->fields['usuario_carga']));
    $onclick_elegir="location.href='$ref'";?>  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['login']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['apellido']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['cantidad']?></td>
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>
