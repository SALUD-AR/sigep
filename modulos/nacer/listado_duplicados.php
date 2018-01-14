<?php

require_once("../../config.php");

variables_form_busqueda("listado_duplicados");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

if ($cmd == "")  $cmd="activos";

$orden = array(
        "default" => "1",
        "1" => "manrodocumento",
        "2" => "afifechanac",
        "3" => "afiapellido",
        "4" => "afinombre",
        "5" => "afidni",        
        "6" => "fechainscripcion",         
       );
$filtro = array(
		"afidni" => "DNI",
        "afiapellido" => "Apellido",
        "afinombre" => "Nombre",          
       );

$sql_tmp="SELECT * FROM nacer.smiafiliados";
$where_tmp=" (activo, manrodocumento, afifechanac)
IN(
SELECT activo, manrodocumento, afifechanac FROM nacer.smiafiliados
GROUP BY activo, manrodocumento, afifechanac
HAVING count(*)>1) and activo='S' and manrodocumento<>''";
    
echo $html_header;
?>
<form name=form1 action="listado_duplicados.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	     &nbsp;&nbsp;
	    <? $link=encode_link("listado_duplicado_excel.php",array());?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=9 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_duplicados.php",array("sort"=>"1","up"=>$up))?>'>Doc Madre</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_duplicados.php",array("sort"=>"2","up"=>$up))?>'>Nombre Madre</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_duplicados.php",array("sort"=>"2","up"=>$up))?>'>Fecha Nac Niño</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_duplicados.php",array("sort"=>"3","up"=>$up))?>'>Apellido</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_duplicados.php",array("sort"=>"4","up"=>$up))?>'>Nombre</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_duplicados.php",array("sort"=>"5","up"=>$up))?>'>DNI</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_duplicados.php",array("sort"=>"6","up"=>$up))?>'>F Ins</a></td>    
  </tr>
 <?
   while (!$result->EOF) {?>
  
    <tr <?=atrib_tr()?>>        
     <td><?=$result->fields['manrodocumento']?></td>
     <td><?=$result->fields['maapellido'].", ".$result->fields['manombre']?></td> 
     <td><?=fecha($result->fields['afifechanac'])?></td>
     <td><?=$result->fields['afiapellido']?></td>     
     <td><?=$result->fields['afinombre']?></td>     
     <td><?=$result->fields['afidni']?></td> 
     <td><?=fecha($result->fields['fechainscripcion'])?></td>  
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>