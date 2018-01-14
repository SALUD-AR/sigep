<?php
/*
Author: seba
$Revision: 1.0 $
$Date: 2015/04/30 10:52:40 $
*/
require_once("../../config.php");

variables_form_busqueda("listado_formula_inicio");

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
        "descripcion"=> "Entregado",
        "cmd"        => "4"
     ),
     array(
        "descripcion"=> "Todos",
        "cmd"        => "todos"
     )       
);

generar_barra_nav($datos_barra);
 
$sql_tmp="SELECT * from (
      SELECT
      nacer.smiafiliados.afiapellido as a,
      nacer.smiafiliados.afinombre as n,
      nacer.smiafiliados.afidni as d,
      leche.formula_inicio.id_formula,
      leche.formula_inicio.formula_solicitada,
      leche.formula_inicio.causas_neonatales,
      leche.formula_inicio.causas_maternas,
      leche.formula_inicio.otras_causas,
      leche.formula_inicio.fecha_solicitud,
      leche.formula_inicio.cantidad,
      leche.formula_inicio.medico,
      leche.formula_inicio.id_smiafiliados as id,
      nacer.efe_conv.cuie as cu,
      nacer.efe_conv.nombre AS efector,
      leche.formula_inicio.estado,
      leche.formula_inicio.id_user_aut,
      leche.formula_inicio.fecha_autor,
      leche.formula_inicio.fecha_entrega,
      sistema.usuarios.nombre as nom_user,
      sistema.usuarios.apellido as ape_user
      FROM
      leche.formula_inicio
      inner JOIN nacer.smiafiliados ON nacer.smiafiliados.id_smiafiliados = leche.formula_inicio.id_smiafiliados
      LEFT OUTER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = leche.formula_inicio.cuie
      LEFT OUTER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = leche.formula_inicio.id_user_aut
    
      union 

      SELECT
      leche.beneficiarios.apellido as a,
      leche.beneficiarios.nombre as n,
      leche.beneficiarios.documento as d,
      leche.formula_inicio.id_formula,
      leche.formula_inicio.formula_solicitada,
      leche.formula_inicio.causas_neonatales,
      leche.formula_inicio.causas_maternas,
      leche.formula_inicio.otras_causas,
      leche.formula_inicio.fecha_solicitud,
      leche.formula_inicio.cantidad,
      leche.formula_inicio.medico,
      leche.formula_inicio.id_beneficiario as id,
      nacer.efe_conv.cuie as cu,
      nacer.efe_conv.nombre AS efector,
      leche.formula_inicio.estado,
      leche.formula_inicio.id_user_aut,
      leche.formula_inicio.fecha_autor,
      leche.formula_inicio.fecha_entrega,
      sistema.usuarios.nombre as nom_user,
      sistema.usuarios.apellido as ape_user
      FROM
      leche.formula_inicio
      inner JOIN leche.beneficiarios ON leche.beneficiarios.id_beneficiarios = leche.formula_inicio.id_beneficiario
      LEFT OUTER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = leche.formula_inicio.cuie
      LEFT OUTER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = leche.formula_inicio.id_user_aut) as qu";

if ($cmd==1)
    $where_tmp=" (qu.estado='p')";
if ($cmd==2)
    $where_tmp=" (qu.estado='a')";  
if ($cmd==3)
    $where_tmp=" (qu.estado='n')";
if ($cmd==4)
    $where_tmp=" (qu.estado='e')";
   
echo $html_header;
?>
<form name=form1 action="listado_formula_inicio.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_infosoc,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    </td>
	    <td align=center>
	    &nbsp;&nbsp;<? $link=encode_link("report_info_form_inicio_xls.php",array("sql"=>$sql));?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">
	  </td>
     </tr>
</table>

<?$result = sql($sql,"No se ejecuto en la consulta principal") or die;?>

<table border=1 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=13 align=left id="ma">
     <table width=100%>
      <tr id="ma">
       <td width=30% align=left><b>Total:</b> <?=$total_infosoc?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  <tr>

    <td align=right id="mo"><a id="mo" href='<?=encode_link("infosocial_listado.php",array("sort"=>"1","up"=>$up))?>'>Estado</a></td> 
    <td align=right id="mo"><a id="mo" href='<?=encode_link("infosocial_listado.php",array("sort"=>"2","up"=>$up))?>'>Efector Solicitante</a></td> 
    <td align=right id="mo"><a id="mo" href='<?=encode_link("infosocial_listado.php",array("sort"=>"7","up"=>$up))?>'>Apellido y Nombre</a></td>    
    <td align=right id="mo"><a id="mo" href='<?=encode_link("infosocial_listado.php",array("sort"=>"8","up"=>$up))?>'>D.N.I.</a></td>	
    <td align=right id="mo"><a id="mo" href='<?=encode_link("infosocial_listado.php",array("sort"=>"3","up"=>$up))?>'>Fecha de Solicitud</a></td>
    <td align=right id="mo">Formula Solicita</a></td>
    <td align=right id="mo">Causas Neonatales</a></td>
    <td align=right id="mo">Causas Maternas</a></td>
    <td align=right id="mo">Otras Causas</a></td>
    <td align=right id="mo">Medico Solicitante</a></td>
    <td align=right id="mo"><a id="mo" href='<?=encode_link("infosocial_listado.php",array("sort"=>"5","up"=>$up))?>'>Responsable Concludasion I.S.</a></td>
    <td align=right id="mo"><a id="mo" href='<?=encode_link("infosocial_listado.php",array("sort"=>"6","up"=>$up))?>'>Fecha Conclusion</a></td>
    <td align=right id="mo">Fecha de Entrega</a></td>
 </tr>
  <?
   while (!$result->EOF) {
	if($cmd==1 || $cmd==2 || $cmd==3 || $cmd==4){
   		$ref = encode_link("aut_infosocial_form_inicio.php",array("cmd"=>$cmd,"pagina"=>"aut_infosocial_form_inicio"));
    	$onclick_elegir="location.href='$ref'";
   	 }
   	?>
  
    <tr <?=atrib_tr()?>>     
     <td align=center onclick="<?=$onclick_elegir?>"><?if($result->fields['estado']=='p')echo "PENDIENTE"; elseif(trim($result->fields['estado'])=='a') echo "AUTORIZADO"; 
     elseif(trim($result->fields['estado'])=='n') echo "RECHAZADO";elseif(trim($result->fields['estado'])=='e') echo "ENTREGADO"?></td>
	 <td align=center onclick="<?=$onclick_elegir?>"><?=$result->fields['efector'].' - '.$result->fields['cu'] ?></td>
     <td align="center" onclick="<?=$onclick_elegir?>"><?=$result->fields['a'].' '.$result->fields['n']?></td>
     <td align="center" onclick="<?=$onclick_elegir?>"><?=$result->fields['d'];?></td>
     <td align="center" onclick="<?=$onclick_elegir?>"><?=Fecha($result->fields['fecha_solicitud'])?></td>
     <td align="center" onclick="<?=$onclick_elegir?>"><?=$result->fields['formula_solicitada']?></td>
     <td align="center" onclick="<?=$onclick_elegir?>"><?=($result->fields['causas_neonatales']!="-1")?$result->fields['causas_neonatales']:""?></td> 
     <td align="center" onclick="<?=$onclick_elegir?>"><?=($result->fields['causas_maternas']!="-1")?$result->fields['causas_maternas']:""?></td>
     <td align="center" onclick="<?=$onclick_elegir?>"><?=($result->fields['otras_causas']!="-1")?$result->fields['otras_causas']:""?></td>
     <td align="center" onclick="<?=$onclick_elegir?>"><?=$result->fields['medico']?></td>    
     <td align="center" onclick="<?=$onclick_elegir?>"><?if($result->fields['id_user_aut']=='' or $result->fields['id_user_aut']==0) echo " &nbsp"; else echo $result->fields['nom_user'].' '.$result->fields['ape_user'];?></td>     
     <td align="center" onclick="<?=$onclick_elegir?>"><?if($result->fields['id_user_aut']=='' or $result->fields['id_user_aut']==0) echo " &nbsp"; else echo Fecha($result->fields['fecha_autor'])?></td>   
     <td align="center" onclick="<?=$onclick_elegir?>"><?=Fecha($result->fields['fecha_entrega'])?></td>
    </tr>    
	<?$result->MoveNext();
    }?>
  	
</table>
</form>
</body>
</html>

<?echo fin_pagina();// aca termino ?>