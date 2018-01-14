<?php

require_once("../../config.php");

variables_form_busqueda("resumen_general");
if ($cmd == "")  $cmd="activos";
$orden = array(
        "default" => "1",        
        "1" => "nombreefector",        
       );
$filtro = array(		
        "nombreefector" => "Efector",                
       );

 $datos_barra = array(
     array(
        "descripcion"=> "Activos",
        "cmd"        => "activos"
     ),
     array(
        "descripcion"=> "Inactivos",
        "cmd"        => "inactivos"
     ),
     array(
        "descripcion"=> "Todos",
        "cmd"        => "todos"
     )
);
generar_barra_nav($datos_barra);
$sql_tmp="SELECT 
  smiefectores.nombreefector,
  count(smiafiliados.id_smiafiliados) AS cb
FROM
  facturacion.smiefectores
  left join nacer.smiafiliados on (cuieefectorasignado=cuie)";

if ($cmd=="activos")
    $where_tmp=" (smiafiliados.activo='S')";
    

if ($cmd=="inactivos")
    $where_tmp=" (smiafiliados.activo='N')"; 
    
if ($cmd=="todos")
    $where_tmp=" smiafiliados.id_smiafiliados LIKE '%%' "; 
    
$where_tmp.=" 
  GROUP BY  
  smiefectores.nombreefector";
echo $html_header;
?>
<form name=form1 action="resumen_general" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
      	
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>		    
	     &nbsp;&nbsp;
	    <? $link=encode_link("resumen_general_excel.php",array("sql"=>$sql));?>
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
  <tr >
    <td align=right id=mo>Efector</td>
    <td align=right id=mo>Total</td>    
  </tr>
 <?
   while (!$result->EOF) {?>  	
  
    <tr <?=atrib_tr()?> >     
     <td><?=$result->fields['nombreefector']?></td>     
     <td><?=$result->fields['cb']?></td>          
    </tr>
	<?$result->MoveNext();
   }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>