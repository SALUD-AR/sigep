<?php

require_once("../../config.php");

variables_form_busqueda("resumen_prestaciones");

$orden = array(
        "default" => "1",
        "1" => "nomenclador.id_nomenclador",        
       );
$filtro = array(		
        "factura.periodo" => "Periodo",                
       );

$sql_tmp="SELECT 
  sum(facturacion.prestacion.cantidad) AS cantidad,
  facturacion.nomenclador.codigo,
  facturacion.nomenclador.descripcion,
  facturacion.prestacion.precio_prestacion,
  facturacion.prestacion.precio_prestacion*sum(facturacion.prestacion.cantidad) as precio_total
FROM
  facturacion.nomenclador
  INNER JOIN facturacion.prestacion ON (facturacion.nomenclador.id_nomenclador = facturacion.prestacion.id_nomenclador)
  INNER JOIN facturacion.comprobante ON (facturacion.prestacion.id_comprobante = facturacion.comprobante.id_comprobante)
  INNER JOIN facturacion.factura ON (facturacion.comprobante.id_factura = facturacion.factura.id_factura) ";
$where_tmp=" (estado='C')
  GROUP BY
  facturacion.nomenclador.id_nomenclador,
  facturacion.nomenclador.codigo,
  facturacion.nomenclador.descripcion,
  facturacion.prestacion.precio_prestacion";
echo $html_header;
?>
<form name=form1 action="resumen_prestaciones.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		 &nbsp;&nbsp; Ej: 2011/06
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>		    
	     &nbsp;&nbsp;
	    <? $link=encode_link("resumen_prestaciones_excel.php",array("sql"=>$sql));?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">    
	  </td>
     </tr>
</table>

<?
$sql=ereg_replace('ILIKE','=',$sql);
$sql=ereg_replace('%','',$sql);
$result = sql($sql) or die;?>

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
    <td align=right id=mo>Codigo</td>
    <td align=right id=mo>Descripcion</td>
    <td align=right id=mo>Cantidad</td>
    <td align=right id=mo>P Unitario</td>
    <td align=right id=mo>P Total</td>
  </tr>
 <? $t=0;
 	$s=0;
   while (!$result->EOF) {?>  	
  
    <tr <?=atrib_tr()?> >     
     <td><?=$result->fields['codigo']?></td>     
     <td><?=$result->fields['descripcion']?></td>     
     <td><?=$result->fields['cantidad']?></td>     
     <td><?=number_format($result->fields['precio_prestacion'],2,',','.')?></td>     
     <td><?=number_format($result->fields['precio_total'],2,',','.')?></td>   
     <?$t=$t+$result->fields['precio_total'];
     	$s=$s+$result->fields['cantidad']
     ?>  
    </tr>
	<?$result->MoveNext();
   }?>
    <tr>    	  
     	<td>Monto Total</td> 
     	<td><b><?=number_format($t,2,',','.')?></b></td> 
    </tr>
    <tr>    	
     	<td>Total Prestaciones</td> 
     	<td><b><?=number_format($s,0,',','.')?></b></td> 
    </tr> 
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
