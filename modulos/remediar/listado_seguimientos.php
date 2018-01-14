<?php
require_once("../../config.php");

Header('Content-Type: text/html; charset=LATIN1');

variables_form_busqueda("ins_listado_old");
$usuario1=$_ses_user['id'];
$user_name=$_ses_user['name'];
$orden = array(
        "default" => "1",
        "1" => "beneficiarios.numero_doc",
       );
$filtro = array(		
		"numero_doc" => "Numero de Documento",		
		"apellido_benef" => "Apellido",
		);
       
$sql_tmp="select seguimiento_remediar.*, apellido_benef, nombre_benef, numero_doc,fecha_nacimiento_benef
			from trazadoras.seguimiento_remediar
			inner join uad.beneficiarios ON (seguimiento_remediar.clave_beneficiario= beneficiarios.clave_beneficiario)";

echo $html_header;?>

<form name=form1 action="listado_seguimientos.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>	 
      <? $link=encode_link("listado_seguimiento_excel.php",array());?>
      Excel Completo<img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">

      <? $link=encode_link("listado_seguimiento_excel1.php",array());?>
      Excel Filtrado<img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">

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
    <td align=right id=mo>F NAC</td>
  </tr>
 <?
   while (!$result->EOF) {
   	//$ref = encode_link("ins_admin_old.php",array("id_planilla"=>$result->fields['id_beneficiarios'],"pagina_viene_1"=>"ins_listado_todos_remediar.php"));   	
   // $onclick_elegir="location.href='$ref'";?>
  
    <tr <?=atrib_tr()?>>     
     <td  onclick="<?//=$onclick_elegir?>"><?=$result->fields['clave_beneficiario']?></td>
     <td  onclick="<?//=$onclick_elegir?>"><?=$result->fields['numero_doc']?></td>        
     <td  onclick="<?//=$onclick_elegir?>"><?=$result->fields['apellido_benef']?></td>     
     <td  onclick="<?//=$onclick_elegir?>"><?=$result->fields['nombre_benef']?></td>     
     <td  onclick="<?//=$onclick_elegir?>"><?=fecha($result->fields['fecha_nacimiento_benef'])?></td>    
   </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
