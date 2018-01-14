<?php

require_once("../../config.php");

variables_form_busqueda("resumen_planillas_ai_ai");

$orden = array(
        "default" => "1",        
        "1" => "descripcion_agente",        
       );
$filtro = array(		
        "planillas.periodo" => "Periodo",                
       );

$sql_tmp="SELECT   
  sum(planillas.cant_nino) AS cn,
  sum(planillas.cant_embarazada) AS ca,
  descripcion_agente
FROM
  planillas.planillas
  INNER JOIN planillas.agente_inscriptor ON (planillas.planillas.id_agente_inscriptor = planillas.agente_inscriptor.id_agente_inscriptor)";
$where_tmp=" tipo='Recepcion' 
  GROUP BY
descripcion_agente";
echo $html_header;
?>
<form name=form1 action="resumen_planillas_ai" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
        (Ej: 2007/12 2008/02)
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>		    
	     &nbsp;&nbsp;
	    <? $link=encode_link("resumen_planillas_excel_ai.php",array("sql"=>$sql));?>
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
    <td align=right id=mo>Agente Inscriptor</td>
    <td align=right id=mo>Cant Niños</td>
    <td align=right id=mo>Cant Embarazadas</td>
  </tr>
 <?
   while (!$result->EOF) {?>  	
  
    <tr <?=atrib_tr()?> >     
     <td><?=$result->fields['descripcion_agente']?></td>     
     <td><?=$result->fields['cn']?></td>     
     <td><?=$result->fields['ca']?></td>     
    </tr>
	<?$result->MoveNext();
   }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>