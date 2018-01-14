<?php
/*
Author: JEM

modificada por
$Author: JEM $
$Revision: 1.0 $
$Date: 2011/07/18 13:41:45 $
*/
require_once("../../config.php");

variables_form_busqueda("usr_efectores_listado");

$orden = array(
        "default" => "1",
        "1" => "login",
		"2" => "apellido"
       );
$filtro = array(
		"login" => "login",
		"apellido" => "apellido"  
       );
$sql_tmp="select * from sistema.usuarios";

echo $html_header;
?>
<form name=form1 action="usr_efectores_listado.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_pais,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	  </td>
     </tr>
</table>

<?$result = sql($sql,"No se ejecuto en la consulta principal") or die;?>

<table border=0 width=50% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=12 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_pais?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  <tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("usr_efecotres_listado.php",array("sort"=>"1","up"=>$up))?>' >Login</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("usr_efecotres_listado.php",array("sort"=>"2","up"=>$up))?>' >Nombre y Apellido</a></td>      	
     </tr>
  <?
   while (!$result->EOF) {
   		$ref = encode_link("usr_efectores_admin.php",array("id_usuario"=>$result->fields['id_usuario'],"pagina"=>"usr_efectores_listado"));
    	$onclick_elegir="location.href='$ref'";
   	?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['login']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['apellido'].', '.$result->fields['nombre']?></td>
    </tr>    
	<?$result->MoveNext();
    }?>
  	
</table>
</form>
</body>
</html>

<?php echo fin_pagina(); ?>
