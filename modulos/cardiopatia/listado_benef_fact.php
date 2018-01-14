<?php

require_once("../../config.php");

variables_form_busqueda("listado_benef_fact");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);


$orden = array(
        "default" => "1",
        "1" => "apellido",
        "2" => "nombre",
        "3" => "dni",
        "7" => "id_beneficiario",
         );
$filtro = array(
		"to_char(id_beneficiario,'999999')" => "id_beneficiario",
        "apellido" => "Apellido",
        "nombre" => "Nombre",
        "to_char(dni,'99999999')" => "DNI",
                
       );


$sql_tmp="select * from cardiopatia.beneficiario";



echo $html_header;
?>
<form name=form1 action="listado_benef_fact.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;<input type='button' name="nuevo" value='Nuevo Dato' onclick="document.location='beneficiario.php';">
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>

<table border=1 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=12 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
    <td align="center" id=mo><a id=mo href='<?=encode_link("listado_benef_fact.php",array("sort"=>"7","up"=>$up))?>' >Id_Beneficiario</a></td>
    <td align="right" id=mo><a id=mo href='<?=encode_link("listado_benef_fact.php",array("sort"=>"1","up"=>$up))?>' >Apellido</a></td>      	
    <td align="right" id=mo><a id=mo href='<?=encode_link("listado_benef_fact.php",array("sort"=>"2","up"=>$up))?>'>Nombre</a></td>
    <td align="right" id=mo><a id=mo href='<?=encode_link("listado_benef_fact.php",array("sort"=>"3","up"=>$up))?>'>DNI</a></td>
    
    <td align="right" id=mo>F NAC</td>
   
  </tr>
 <?
        $id_pagina=1;
 		  
 
      while (!$result->EOF) {
      $ref = encode_link("factura_cardiop.php",array("id_beneficiario"=>$result->fields['id_beneficiario'],"id_pagina"=>$id_pagina));
      $onclick_elegir="location.href='$ref'";
      ?>
   	 
    <tr <?=atrib_tr()?>>     
     <td align="center" onclick="<?=$onclick_elegir?>"><?=$result->fields['id_beneficiario']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['apellido']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['dni']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=fecha($result->fields['fechanacimiento'])?></td>
    </tr>    
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>