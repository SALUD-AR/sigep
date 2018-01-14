<?php
require_once("../../config.php");

Header('Content-Type: text/html; charset=LATIN1');

variables_form_busqueda("ins_listado_old");
$usuario1=$_ses_user['id'];
$user_name=$_ses_user['name'];
$orden = array(
        "default" => "1",
        "1" => "beneficiarios.numero_doc",
		"3" => "efe_conv.nombre",       
       );
$filtro = array(		
		"numero_doc" => "N�mero de Documento",		
		"apellido_benef" => "Apellido",
		"efe_conv.nombre" => "Efector",
		);
       
$sql_tmp="SELECT 
			beneficiarios.id_beneficiarios, 
			beneficiarios.clave_beneficiario, 
			beneficiarios.apellido_benef, 
			beneficiarios.nombre_benef, 
			beneficiarios.fecha_nacimiento_benef, 
			beneficiarios.numero_doc, 
			efe_conv.nombre 
			FROM 
			uad.beneficiarios
			left join nacer.efe_conv on beneficiarios.cuie_ea=efe_conv.cuie";



echo $html_header;?>

<form name=form1 action="ins_listado_todos_remediar.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>	    
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
  	<td align=right id=mo>Clave Beneficiario</td>    	    
    <td align=right id=mo><a id=mo href='<?=encode_link("ins_listado_old.php",array("sort"=>"1","up"=>$up))?>'>Documento</a></td>      	    
    <td align=right id=mo>Apellido</a></td>      	    
    <td align=right id=mo>Nombre</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("ins_listado_old.php",array("sort"=>"3","up"=>$up))?>'>Efector</a></td>
    <td align=right id=mo>F NAC</td>
  </tr>
 <?
   while (!$result->EOF) {
   	$ref = encode_link("ins_admin_old.php",array("id_planilla"=>$result->fields['id_beneficiarios'],"pagina_viene_1"=>"ins_listado_todos_remediar.php"));   	
    $onclick_elegir="location.href='$ref'";?>
  
    <tr <?=atrib_tr()?>>     
     <td  onclick="<?=$onclick_elegir?>"><?=$result->fields['clave_beneficiario']?></td>
     <td  onclick="<?=$onclick_elegir?>"><?=$result->fields['numero_doc']?></td>        
     <td  onclick="<?=$onclick_elegir?>"><?=$result->fields['apellido_benef']?></td>     
     <td  onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre_benef']?></td>     
     <td  onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>
     <td  onclick="<?=$onclick_elegir?>"><?=fecha($result->fields['fecha_nacimiento_benef'])?></td>     
   </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
