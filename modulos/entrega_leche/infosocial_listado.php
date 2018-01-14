<?php
/*
Author: gaby 

modificada por
$Author: gaby $
$Revision: 1.0 $
$Date: 2012/09/21 10:52:40 $
*/
require_once("../../config.php");

variables_form_busqueda("infosocial_listado");

$orden = array(
        "default" => "5",
        "1" => "cu",
        "3" => "d",
        "5" => "a",
        "6" => "n",  
        "7" => "efector" 
        );
$filtro = array(
		"cu" => "CUIE",
        "a"=> "Apellido",
        "d" => "D.N.I.",
        "n"=> "Nombre",
        "efector"=> "Efector"
        
       );
  if ($cmd=='')$cmd=1;     
$datos_barra = array(
     array(
        "descripcion"=> "Pendiente",
        "cmd"        => "1"
     ),
     array(
        "descripcion"=> "Autorizado",
        "cmd"        => "2"
     ),
     array(
        "descripcion"=> "Rechazado",
        "cmd"        => "3"
     ),
     array(
        "descripcion"=> "Todos",
        "cmd"        => "todos"
     )       
);

generar_barra_nav($datos_barra);
 
$sql_tmp="select * from (
			SELECT
			nacer.smiafiliados.afiapellido as a,
			nacer.smiafiliados.afinombre as n,
			nacer.smiafiliados.afidni as d,
			leche.info_social.id_informe,
			leche.info_social.informe,
			leche.info_social.fecha_inf,
			leche.info_social.resp_infor,
			leche.info_social.id_smiafiliados as id,
			nacer.efe_conv.cuie as cu,
			nacer.efe_conv.nombre AS efector,
			leche.info_social.autorizado,
			leche.info_social.id_user_aut,
			leche.info_social.fecha_aut,
			sistema.usuarios.nombre as nom_user,
			sistema.usuarios.apellido as ape_user
			FROM
			leche.info_social
			inner JOIN nacer.smiafiliados ON nacer.smiafiliados.id_smiafiliados = leche.info_social.id_smiafiliados
			LEFT OUTER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = leche.info_social.cuie
			LEFT OUTER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = leche.info_social.id_user_aut
		
			union	

			SELECT
			leche.beneficiarios.apellido as a,
			leche.beneficiarios.nombre as n,
			leche.beneficiarios.documento as d,
			leche.info_social.id_informe,
			leche.info_social.informe,
			leche.info_social.fecha_inf,
			leche.info_social.resp_infor,
			leche.info_social.id_beneficiarios as id,
			nacer.efe_conv.cuie as cu,
			nacer.efe_conv.nombre AS efector,
			leche.info_social.autorizado,
			leche.info_social.id_user_aut,
			leche.info_social.fecha_aut,
			sistema.usuarios.nombre as nom_user,
			sistema.usuarios.apellido as ape_user
			FROM
			leche.info_social
			inner JOIN leche.beneficiarios ON leche.beneficiarios.id_beneficiarios = leche.info_social.id_beneficiarios
			LEFT OUTER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = leche.info_social.cuie
			LEFT OUTER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = leche.info_social.id_user_aut) as qu";

if ($cmd==1)
    $where_tmp=" (qu.autorizado='PEND')";
if ($cmd==2)
    $where_tmp=" (qu.autorizado='SI')";  
if ($cmd==3)
    $where_tmp=" (qu.autorizado='NO')";
   
echo $html_header;
?>
<form name=form1 action="infosocial_listado.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_infosoc,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    </td>
	    <td align=center>
	    &nbsp;&nbsp;<? $link=encode_link("report_info_social_xls.php",array("sql"=>$sql));?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">
	  </td>
     </tr>
</table>

<?$result = sql($sql,"No se ejecuto en la consulta principal") or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=12 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_infosoc?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  <tr>

    <td align=right id=mo><a id=mo href='<?=encode_link("infosocial_listado.php",array("sort"=>"1","up"=>$up))?>'>Estado</a></td> 
    <td align=right id=mo><a id=mo href='<?=encode_link("infosocial_listado.php",array("sort"=>"2","up"=>$up))?>'>Efector Solicitante</a></td> 
    <td align=right id=mo><a id=mo href='<?=encode_link("infosocial_listado.php",array("sort"=>"7","up"=>$up))?>'>Apellido y Nombre</a></td>    
    <td align=right id=mo><a id=mo href='<?=encode_link("infosocial_listado.php",array("sort"=>"8","up"=>$up))?>'>D.N.I.</a></td>	
    <td align=right id=mo><a id=mo href='<?=encode_link("infosocial_listado.php",array("sort"=>"3","up"=>$up))?>'>Fecha I.S.</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("infosocial_listado.php",array("sort"=>"4","up"=>$up))?>'>Realiza I.S.</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("infosocial_listado.php",array("sort"=>"5","up"=>$up))?>'>Responsable Conclusion I.S.</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("infosocial_listado.php",array("sort"=>"6","up"=>$up))?>'>Fecha Conclusion</a></td>

 </tr>
  <?
   while (!$result->EOF) {
	if($cmd==1 || $cmd==2 || $cmd==3 ){
   		$ref = encode_link("aut_infosocial.php",array("cmd"=>$cmd,"pagina"=>"aut_infosocial"));
    	$onclick_elegir="location.href='$ref'";
   	 }
   	?>
  
    <tr <?=atrib_tr()?>>     
     <td align=center onclick="<?=$onclick_elegir?>"><?if($result->fields['autorizado']=='PEND')echo "PENDIENTE"; elseif(trim($result->fields['autorizado'])=='SI') echo "AUTORIZADO"; elseif(trim($result->fields['autorizado'])=='NO') echo "RECHAZADO"?></td>
	 <td align=center onclick="<?=$onclick_elegir?>"><?=$result->fields['efector'].' - '.$result->fields['cu'] ?></td>
     <td align=right onclick="<?=$onclick_elegir?>"><?=$result->fields['a'].' '.$result->fields['n']?></td>
     <td align=right onclick="<?=$onclick_elegir?>"><?=$result->fields['d'];?></td>
     <td align=right onclick="<?=$onclick_elegir?>"><?=Fecha($result->fields['fecha_inf'])?></td>     
     <td align=center onclick="<?=$onclick_elegir?>"><?=$result->fields['resp_infor']?></td>
     <td align=right onclick="<?=$onclick_elegir?>"><?if($result->fields['id_user_aut']=='' or $result->fields['id_user_aut']==0) echo " &nbsp"; else echo $result->fields['nom_user'].' '.$result->fields['ape_user'];?></td>     
     <td align=right onclick="<?=$onclick_elegir?>"><?if($result->fields['id_user_aut']=='' or $result->fields['id_user_aut']==0) echo " &nbsp"; else echo Fecha($result->fields['fecha_aut'])?></td>   
    </tr>    
	<?$result->MoveNext();
    }?>
  	
</table>
</form>
</body>
</html>

<?echo fin_pagina();// aca termino ?>