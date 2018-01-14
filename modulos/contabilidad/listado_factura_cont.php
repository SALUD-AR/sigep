<?php

require_once("../../config.php");

variables_form_busqueda("listado_fact");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

if ($_POST['generar_excel']=="Generar Excel"){
	$periodo=$_POST['periodo'];
	$link=encode_link("listado_factura_cont_excel.php",array("periodo"=>$periodo));?>
	<script>
	window.open('<?=$link?>')
	</script>	
<?}

$cmd="C";

$orden = array(
        "default" => "1",
        "default_up" => "0",
        "1" => "id_factura",
        "2" => "cuie",
        "3" => "nombre",
        "4" => "fecha_factura",
        "5" => "fecha_carga",
        "6" => "periodo",
        "7" => "observaciones"
       );
$filtro = array(
		"to_char(factura.id_factura,'999999')" => "Nro. Factura",
		"cuie" => "CUIE",
        "periodo" => "Periodo",                
        "nombre" => "Nombre del Efector",                
       );

$sql_tmp="SELECT 
  facturacion.factura.cuie,
  facturacion.factura.fecha_carga,
  facturacion.factura.fecha_factura,
  facturacion.factura.periodo,
  facturacion.factura.estado,
  facturacion.factura.observaciones,
  facturacion.factura.id_factura,
  facturacion.factura.online,
  facturacion.factura.alta_comp,
  facturacion.factura.nro_exp_ext,
  facturacion.factura.fecha_exp_ext,
  facturacion.factura.periodo_contable,
  facturacion.factura.monto_prefactura,
  nacer.efe_conv.nombre
FROM
  facturacion.factura
  LEFT JOIN nacer.efe_conv using (cuie)";


if ($cmd=="C")
    $where_tmp=" (factura.estado='C')";

echo $html_header;
?>
<form name=form1 action="listado_factura_cont.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>  
	    &nbsp;&nbsp;
	    
	    <b>Periodo para DDJJ:</b>
			
			        			
			 <select name=periodo Style="width=100px">&nbsp;
			  <?
			  $sql1 = "select * from facturacion.periodo order by periodo";
			  $result1=sql($sql1,"No se puede traer el periodo");
			  while (!$result1->EOF) {?>
			  			  
			  <option value=<?=$result1->fields['periodo']?>><?=$result1->fields['periodo']?></option>
			  <?
			  $result1->movenext();
			  }
			  ?>			  
			  </select>
	    	  <input type="submit" value="Generar Excel" name="generar_excel">	          
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
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_factura_cont.php",array("sort"=>"1","up"=>$up))?>'>Nro Factura</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_factura_cont.php",array("sort"=>"2","up"=>$up))?>'>CUIE</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_factura_cont.php",array("sort"=>"3","up"=>$up))?>'>Efector</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_factura_cont.php",array("sort"=>"4","up"=>$up))?>'>Fecha Factura</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_factura_cont.php",array("sort"=>"6","up"=>$up))?>'>Periodo</a></td>    
    <td align=right id=mo>Nro Exp Ext</td> 
    <td align=right id=mo>Fecha Exp Ext</td>
    <td align=right id=mo>Periodo Contable</td>   
    <td align=right id=mo>Monto Prefactura</td>    
    <td align=right id=mo>Carga EXP</td>
  </tr>
 <?
   while (!$result->EOF) {
   
   if ($result->fields['online']=='SI'){
			$color='#00FF00'; 
			$title='FACTURA GENERADA FUERA DE PLAN NACER';
	}
	else {
			$color=''; 
			$title='';
	}
	if ($result->fields['alta_comp']=='SI'){
			$color_1='#A9BCF5'; 
			$title_1='FACTURA ALTA COMPLEJIDAD';
		}
	else {
			$color_1=''; 
			$title_1='';
	}
		
   	$ref = encode_link("carga_exp.php",array("id_factura"=>$result->fields['id_factura'],"nro_exp_ext"=>$result->fields['nro_exp_ext'],"fecha_exp_ext"=>$result->fields['fecha_exp_ext'],"periodo_contable"=>$result->fields['periodo_contable']));?>  
    <tr <?=atrib_tr()?>>      
     <td bgcolor="<?=$color?>" title="<?=$title?>"><?=$result->fields['id_factura']?></td>
     <td bgcolor="<?=$color?>" title="<?=$title?>"><?=$result->fields['cuie']?></td>
     <td bgcolor="<?=$color_1?>"title="<?=$title_1?>"><?=$result->fields['nombre']?></td>
     <td ><?=fecha($result->fields['fecha_factura'])?></td>     
     <td ><?=$result->fields['periodo']?></td>      
     <td ><?=$result->fields['nro_exp_ext']?></td>   
     <td ><?=fecha($result->fields['fecha_exp_ext'])?></td> 
     <td ><?=$result->fields['periodo_contable']?></td>    
     <td align="center"><?=number_format($result->fields['monto_prefactura'],2,',','.');?></td>
     <td align="center">
       		<?echo "<a target='_blank' href='".$ref."' title='Carga Expediente'>
					<IMG src='$html_root/imagenes/adelante_dis.gif' height='20' width='20' border='0'>
					</a>";?>
     </td>    
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>

<br>
	<table align='center' border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='80%' cellspacing=0 cellpadding=0>
       <tr>
        <td width=30 bgcolor='#00FF00' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Factura Generada Fuera de Plan Nacer (on-line)</td>
       </tr>       
      </table>
     </td>
    </table>

</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
