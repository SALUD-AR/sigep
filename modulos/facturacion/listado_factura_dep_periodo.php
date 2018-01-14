<?php
/*
$Author: gaby $
$Revision: 1.0 $
$Date: 2010/10/20 15:22:40 $
*/
require_once("../../config.php");
cargar_calendario();

function ultimoDia($mes,$ano){ 
    if ($mes=='02')$ultimo_dia=28;
	else if ($mes=='04' or $mes=='06'or $mes=='09' or $mes=='11') $ultimo_dia=30;
	else $ultimo_dia=31;
    while (checkdate($mes,$ultimo_dia + 1,$ano)){ 
       $ultimo_dia++; 
    } 
    return $ultimo_dia; 
}

if ($_POST['Filtrar']=="Filtrar"){
	$periodo_actual=$_POST['periodo'];		
	$fecha_desde=ereg_replace('/','-',$periodo_actual).'-01';

	$periodo_hasta=$_POST['periodo_hasta'];
	$anio=substr($periodo_hasta,0,4);
	$mes=substr($periodo_hasta,5,2);
	$fecha_hasta=ereg_replace('/','-',$periodo_hasta).'-'.ultimoDia($mes,$anio);

$sql_tmp=" SELECT			
			Sum(facturacion.prestacion.cantidad) AS cantidad,
			facturacion.nomenclador.codigo,
			facturacion.nomenclador.descripcion,
			facturacion.nomenclador.catas,
			facturacion.nomenclador.priori,
			facturacion.prestacion.precio_prestacion,
			facturacion.prestacion.precio_prestacion*sum(facturacion.prestacion.cantidad) AS precio_total
			FROM
			
				(SELECT * from facturacion.factura
				WHERE id_factura in 
					(SELECT DISTINCT
						factura.id_factura
					FROM facturacion.factura 
					INNER JOIN expediente.expediente ON facturacion.factura.id_factura = expediente.expediente.id_factura
					INNER JOIN expediente.transaccion ON expediente.transaccion.id_expediente = expediente.expediente.id_expediente
					WHERE
						(expediente.transaccion.id_area = 1 AND expediente.transaccion.estado = 'D') AND
						(expediente.transaccion.num_tranf is null) AND
						(expediente.transaccion.fecha_mov BETWEEN '$fecha_desde' AND '$fecha_hasta') 
					))as cons1
			INNER JOIN facturacion.comprobante USING (id_factura)
			INNER JOIN facturacion.prestacion USING (id_comprobante)
			INNER JOIN facturacion.nomenclador USING (id_nomenclador)
			GROUP BY
			facturacion.nomenclador.id_nomenclador,
			facturacion.nomenclador.codigo,
			facturacion.nomenclador.descripcion,
			facturacion.nomenclador.catas,
			facturacion.nomenclador.priori,
			facturacion.prestacion.precio_prestacion
			ORDER BY codigo DESC";			
}
echo $html_header;?>

<form name=form1 action="listado_factura_dep_periodo.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<b>Periodo desde:	        			
			 <select name=periodo Style="width=100px">&nbsp;
			  <?
			  $sql1 = "SELECT
							*
							FROM
							facturacion.periodo
							order by  periodo";
			  $result1=sql($sql1,"No se puede traer el periodo");
			  while (!$result1->EOF) {?>	  
			  <option value=<?=$result1->fields['periodo']?> <?if ($result1->fields['periodo']==$_POST['periodo']) echo "selected"?>><?=$result1->fields['periodo']?></option>
			  <?
			  $result1->movenext();
			  }
			  ?>			  
			  </select>
			  Periodo hasta:
			  <select name=periodo_hasta Style="width=100px">&nbsp;
			  <?
			  $sql1 = "SELECT
							*
							FROM
							facturacion.periodo
							order by  periodo";
			  $result1=sql($sql1,"No se puede traer el periodo");
			  while (!$result1->EOF) {?>	  
			  <option value=<?=$result1->fields['periodo']?> <?if ($result1->fields['periodo']==$_POST['periodo_hasta']) echo "selected"?>><?=$result1->fields['periodo']?></option>
			  <?
			  $result1->movenext();
			  }
			  ?>			  
			  </select>
			  
			  <input type="submit" value="Filtrar" name="Filtrar">		    	 
    	  </b>     
	  </td>
     </tr>
</table>

<?if ($_POST['Filtrar']=="Filtrar") {
$result = sql($sql_tmp) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$result->recordCount()?> Filtro: <?=$_POST['periodo'];?></td>       
      </tr>
    </table>
   </td>
  </tr>
  
<td align=right id=mo>Codigo</td>
    <td align=right id=mo>Descripcion</td>
    <td align=right id=mo>Cantidad</td>
    <td align=right id=mo>P Unitario</td>
    <td align=right id=mo>P Total</td>
  </tr>
 <? $t=0;
 	$s=0;
   while (!$result->EOF) {  	
	if ($result->fields['catas']=='1'){
			$color_catas='#FA58AC'; 
			$title_catas='Prestacion Catastrofica';
	   }else{
			$color_catas=''; 
			$title_catas='Prestacion Normal';
	}
	if ($result->fields['priori']=='1'){
			$color_priori='#00FF00'; 
			$title_priori='Prestacion Priorizada';
	   }else{
			$color_priori=''; 
			$title_priori='Prestacion Normal';
	}
	
	?>
    <tr <?=atrib_tr()?> >     
     <td bgcolor="<?=$color_catas?>" title="<?=$title_catas?>"><?=$result->fields['codigo']?></td>     
     <td bgcolor="<?=$color_priori?>" title="<?=$title_priori?>"><?=$result->fields['descripcion']?></td>     
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

<br>
	

</td>
</table>

<?}?>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
