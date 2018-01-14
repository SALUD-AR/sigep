<?php
require_once("../../config.php");

variables_form_busqueda("alta_listado");

$orden = array(
        "default" => "1",
        "1" => "doc_madre",       
        "2" => "nom_madre",       
        "3" => "fecha_alta",        
        "4" => "fecha_parto",
        "5" => "efe_conv.nombre"
       );
$filtro = array(		
		"efe_conv.nombre" => "Nombre Efector",		
		"doc_madre" => "Documento",					
		"nom_madre" => "Nombre",		
       );
$sql_tmp="SELECT * FROM alta.alta
			left join nacer.efe_conv using (CUIE)";

echo $html_header;

?>
<form name=form1 action="alta_listado.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;<? $link=encode_link("alta_listado_excel.php",array());?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">
	  
	    &nbsp;&nbsp;<input type='button' name="nueva_emb" value='Nuevo Dato' onclick="document.location='alta_admin.php'">
	    
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
    <td align=right id=mo><a id=mo href='<?=encode_link("alta_listado.php",array("sort"=>"1","up"=>$up))?>'>Documento</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("alta_listado.php",array("sort"=>"2","up"=>$up))?>'>Nombre</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("alta_listado.php",array("sort"=>"3","up"=>$up))?>'>Fecha Alta</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("alta_listado.php",array("sort"=>"4","up"=>$up))?>'>Fecha Parto</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("alta_listado.php",array("sort"=>"5","up"=>$up))?>'>Efector</a></td>      	        
  </tr>
 <?
   while (!$result->EOF) {
   	$ref = encode_link("alta_admin.php",array("id_planilla"=>$result->fields['id_alta']));
    $onclick_elegir="location.href='$ref'";?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['doc_madre']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nom_madre']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=Fecha($result->fields['fecha_alta'])?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=Fecha($result->fields['fecha_parto'])?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>          
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>