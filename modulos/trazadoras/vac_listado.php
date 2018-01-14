<?php
/*
Author: ferni 

modificada por
$Author: ferni $
$Revision: 1.30 $
$Date: 2006/07/20 15:22:40 $
*/
require_once("../../config.php");

variables_form_busqueda("vac_listado");

$orden = array(
        "default" => "1",
        "1" => "efe_conv.nombre",
        "2" => "dni",       
        "3" => "apellido",       
        "4" => "vacunas.nombre",        
        "5" => "fecha_nac",        
        "6" => "fecha_vac"        
       );
$filtro = array(		
		"efe_conv.nombre" => "Nombre Efector",		
		"dni" => "Documento",		
		"apellido" => "Apellido",		
		"vacunas.nombre" => "Nombre",		
       );
$sql_tmp="SELECT efe_conv.nombre as nombre_efe,vacunas.nombre as nom,* FROM trazadoras.vacunas
			left join nacer.efe_conv using (CUIE)";

echo $html_header;

?>
<form name=form1 action="vac_listado.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;<? $link=encode_link("vac_listado_excel.php",array());?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">
	  
	    &nbsp;&nbsp;<input type='button' name="nueva_nino" value='Nuevo Dato' onclick="document.location='vac_admin.php'">
	   
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>
<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("vac_listado.php",array("sort"=>"1","up"=>$up))?>'>Nom Efec</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("vac_listado.php",array("sort"=>"2","up"=>$up))?>'>DNI</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("vac_listado.php",array("sort"=>"3","up"=>$up))?>'>Apellido</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("vac_listado.php",array("sort"=>"4","up"=>$up))?>'>Nombre</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("vac_listado.php",array("sort"=>"5","up"=>$up))?>'>Fecha Nac</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("vac_listado.php",array("sort"=>"6","up"=>$up))?>'>Fecha Vac</a></td>      	    
  </tr>
 <?
   while (!$result->EOF) {
   	$ref = encode_link("vac_admin.php",array("id_planilla"=>$result->fields['id_vacunas']));
    $onclick_elegir="location.href='$ref'";?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre_efe']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['dni']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['apellido']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nom']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=Fecha($result->fields['fecha_nac'])?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=Fecha($result->fields['fecha_vac'])?></td>     
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>