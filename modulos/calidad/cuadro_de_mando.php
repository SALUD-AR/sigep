<?php

require_once("../../config.php");

variables_form_busqueda("cuadro_de_mando");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

if ($cmd == "")  $cmd="VERDADERO";

$orden = array(
        "default" => "2",
        "1" => "cuie",
        "2" => "nombre",
             
       );
$filtro = array(
		"cuie" => "CUIE",
        "nombre" => "Nombre",
        "periodo" => "Periodo",          
       );
$datos_barra = array(
     array(
        "descripcion"=> "Convenio",
        "cmd"        => "VERDADERO"
     ),
     array(
        "descripcion"=> "Sin Convenio",
        "cmd"        => "FALSO"
     ),
     array(
        "descripcion"=> "Todos",
        "cmd"        => "TODOS"
     )
);

generar_barra_nav($datos_barra);

$sql_tmp="SELECT 
  nacer.efe_conv.id_efe_conv,
  nacer.efe_conv.nombre,  
  nacer.efe_conv.cuie
FROM
  nacer.efe_conv";


if ($cmd=="VERDADERO")
    $where_tmp=" (efe_conv.com_gestion='VERDADERO')";
    

if ($cmd=="FALSO")
    $where_tmp=" (efe_conv.com_gestion='FALSO')";

echo $html_header;
?>
<form name=form1 action="cuadro_de_mando.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	     &nbsp;&nbsp;
	    <? $link=encode_link("cuadro_de_mando_excel.php",array("cmd"=>$cmd));?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=11 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("cuadro_de_mando.php",array("sort"=>"1","up"=>$up))?>'>CUIE</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("cuadro_de_mando.php",array("sort"=>"2","up"=>$up))?>'>Nombre</a></td>
    <td align=right id=mo>Tot. Ins. ACT.</td>    
    <td align=right id=mo>Tot. Ins. INAC.</td>    
    <td align=right id=mo>Tot. Ins.</td>    
    <td align=right id=mo>Cant. Facturacion</td>    
    <td align=right id=mo>Cant. Trazadora EMB</td>    
    <td align=right id=mo>Cant. Trazadora NIÑOS</td>    
    <td align=right id=mo>Cant. Trazadora PARTOS</td>    
    <td align=right id=mo>Cant. Trazadora TOTAL</td>    
    <td align=right id=mo>Aplicacion de Fondos</td>    
  </tr>
 <?
   while (!$result->EOF) {?>
    
    <tr <?=atrib_tr()?>>        
     <td><?=$result->fields['cuie']?></td>
     <td><?=$result->fields['nombre']?></td>
     <?$cuie=$result->fields['cuie'];
     $sql = "SELECT count (smiafiliados.id_smiafiliados)as r1 from nacer.smiafiliados 
     WHERE cuieefectorasignado='$cuie' and activo='S'";
     $r1=sql($sql,"error R1");     
     ?>
     <td><?=$r1->fields['r1']?></td>
     <?$cuie=$result->fields['cuie'];
     $sql = "SELECT count (smiafiliados.id_smiafiliados)as r1 from nacer.smiafiliados 
     WHERE cuieefectorasignado='$cuie' and activo='N'";
     $r1=sql($sql,"error R1");     
     ?>
     <td><?=$r1->fields['r1']?></td>
     <?$cuie=$result->fields['cuie'];
     $sql = "SELECT count (smiafiliados.id_smiafiliados)as r1 from nacer.smiafiliados 
     WHERE cuieefectorasignado='$cuie'";
     $r1=sql($sql,"error R1");     
     ?>
     <td><?=$r1->fields['r1']?></td>
     <?$cuie=$result->fields['cuie'];
     $sql = "SELECT count (id_factura)as r1 from facturacion.factura 
     WHERE cuie='$cuie'";
     $r1=sql($sql,"error R1");     
     ?>
     <td><?=$r1->fields['r1']?></td>
     <?$cuie=$result->fields['cuie'];
     $sql = "SELECT count (id_emb)as r1 from trazadoras.embarazadas 
     WHERE cuie='$cuie'";
     $t1=sql($sql,"error R1");     
     ?>
     <td><?=$t1->fields['r1']?></td>
     <?$cuie=$result->fields['cuie'];
     $sql = "SELECT count (id_nino)as r1 from trazadoras.nino 
     WHERE cuie='$cuie'";
     $t2=sql($sql,"error R1");     
     ?>
     <td><?=$t2->fields['r1']?></td>
     <?$cuie=$result->fields['cuie'];
     $sql = "SELECT count (id_par)as r1 from trazadoras.partos 
     WHERE cuie='$cuie'";
     $t3=sql($sql,"error R1");     
     ?>
     <td><?=$t3->fields['r1']?></td>
     <?$total_tra=$t1->fields['r1']+$t2->fields['r1']+$t3->fields['r1']?>
     <td><?=$total_tra?></td>
     
   	<?$cuie=$result->fields['cuie'];
     $sql = "select sum (monto_egreso) as total from contabilidad.egreso
		where cuie='$cuie'";
     $total_egreso=sql($sql,"error R1");     
     ?>
     <td><?=number_format($total_egreso->fields['total'],2,',','.')?></td>
          
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>