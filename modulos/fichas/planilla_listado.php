<?php

require_once("../../config.php");

variables_form_busqueda("planilla_listado");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

if ($cmd == "")  $cmd="A";

$orden = array(
        "default" => "11",
        "default_up" => "0",
        "1" => "periodo",
        "2" => "fecha_hora",
        "3" => "cant_nino",
        "4" => "cant_embarazada",
        "5" => "nombreefector",
        "6" => "usuario",
        "7" => "descripcion_agente",
        "8" => "descripcion_entrega",
        "9" => "descripcion_recibe",
        "10" => "tipo",
        "11" => "id_planillas",
       );
$filtro = array(
		"tipo" => "Tipo",
		"periodo" => "Periodo",
		"descripcion_agente" => "Agente",
        "descripcion_entrega" => "Entrega",
        "descripcion_recibe" => "Recibe",
        "nombreefector" => "Efector",
       );
$sql_tmp="SELECT 
  planillas.planillas.id_planillas,
  planillas.planillas.periodo,
  planillas.planillas.fecha_hora,
  planillas.planillas.cant_nino,
  planillas.planillas.cant_embarazada,
  planillas.planillas.motivo,
  planillas.planillas.usuario,
  planillas.planillas.tipo,
  planillas.agente_inscriptor.descripcion_agente,
  planillas.entrega.descripcion_entrega,
  planillas.recibe.descripcion_recibe,
  nacer.efe_conv.nombre
FROM
  planillas.agente_inscriptor
  INNER JOIN planillas.planillas ON (planillas.agente_inscriptor.id_agente_inscriptor = planillas.planillas.id_agente_inscriptor)
  INNER JOIN planillas.entrega ON (planillas.planillas.id_entrega = planillas.entrega.id_entrega)
  INNER JOIN planillas.recibe ON (planillas.planillas.id_recibe = planillas.recibe.id_recibe)
  INNER JOIN nacer.efe_conv ON (planillas.planillas.id_efector = nacer.efe_conv.cuie)";


echo $html_header;
?>
<form name=form1 action="planilla_listado.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;<input type='button' name="nueva_planilla" value='Nueva Planilla' onclick="document.location='planilla_admin.php'">
	    &nbsp;&nbsp;
	    <? $link=encode_link("planilla_listado_excel.php",array());?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">
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
    <td align=right id=mo><a id=mo href='<?=encode_link("planilla_listado.php",array("sort"=>"11","up"=>$up))?>'>ID</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("planilla_listado.php",array("sort"=>"1","up"=>$up))?>'>Periodo</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("planilla_listado.php",array("sort"=>"2","up"=>$up))?>'>Fecha</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("planilla_listado.php",array("sort"=>"3","up"=>$up))?>'>Niño</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("planilla_listado.php",array("sort"=>"4","up"=>$up))?>'>Embarazada</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("planilla_listado.php",array("sort"=>"5","up"=>$up))?>'>Efector</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("planilla_listado.php",array("sort"=>"6","up"=>$up))?>'>Usuario</a></td>    
    <td align=right id=mo><a id=mo href='<?=encode_link("planilla_listado.php",array("sort"=>"7","up"=>$up))?>'>Agente</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("planilla_listado.php",array("sort"=>"8","up"=>$up))?>'>Entrega</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("planilla_listado.php",array("sort"=>"9","up"=>$up))?>'>Recibe</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("planilla_listado.php",array("sort"=>"10","up"=>$up))?>'>Tipo</a></td>    
  </tr>
 <?
   while (!$result->EOF) {
   	$ref = encode_link("planilla_admin.php",array("id_planilla"=>$result->fields['id_planillas']));
    $onclick_elegir="location.href='$ref'";?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['id_planillas']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['periodo']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=fecha($result->fields['fecha_hora'])?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['cant_nino']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['cant_embarazada']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td> 
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['usuario']?></td>      
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['descripcion_agente']?></td>      
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['descripcion_entrega']?></td>      
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['descripcion_recibe']?></td>      
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['tipo']?></td>      
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>