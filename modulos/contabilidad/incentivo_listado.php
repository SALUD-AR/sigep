<?php

require_once("../../config.php");

variables_form_busqueda("ing_egre_listado");

if ($_POST['mostrar']=="Mostrar"){
	$anio=$_POST['anio'];
	
}


$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);
if ($anio==NULL) $anio=date("Y");
$fecha_desde_1="$anio-01-01";
$fecha_hasta_1="$anio-06-30";
$fecha_desde_2="$anio-07-01";
$fecha_hasta_2="$anio-12-31";
//echo $fecha_desde_1;

$orden = array(
        "default" => "1",
        "1" => "cuie",
        "2" => "nombre",
        "3" => "domicilio",
        "4" => "cuidad",         
       );
$filtro = array(
		"cuie" => "CUIE",
        "nombre" => "Nombre",                
       );


/*$sql_tmp="SELECT 
  nacer.efe_conv.id_efe_conv,
  nacer.efe_conv.nombre,
  nacer.efe_conv.domicilio,
  nacer.efe_conv.departamento,
  nacer.efe_conv.localidad,
  nacer.efe_conv.cod_pos,
  nacer.efe_conv.cuidad,
  nacer.efe_conv.cuie
FROM
  nacer.efe_conv";*/
       
$sql_tmp="select nacer.efe_conv.nombre,
  nacer.efe_conv.cuidad,
  nacer.efe_conv.cuie from nacer.efe_conv right join (
  select distinct cuie from contabilidad.incentivo) as tabla using (cuie)
  order by nombre";



echo $html_header;
?>
<form name=form1 action="incentivo_listado.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?//list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;
	    <!--<input type=submit name="buscar" value='Buscar'>-->
	    &nbsp;&nbsp;	
	     Año: <input type="text" name="anio" value='<?echo $anio?>' maxlength="4" size="12">
	    <input type="submit" name="mostrar" value='Mostrar'>    
	  </td>
     </tr>
</table>

<?$result = sql($sql_tmp) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=9 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <?$total_muletos=$result->recordcount()?>      
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("incentivo_listado.php",array("sort"=>"1","up"=>$up))?>'>CUIE</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("incentivo_listado.php",array("sort"=>"2","up"=>$up))?>'>Nombre</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("incentivo_listado.php",array("sort"=>"4","up"=>$up))?>'>Cuidad</a></td>        
    <td align=right id=mo title="Total de incentivo acumulado en el primer semestre del año">Incentivo 1ªSemestre</td> 
    <td align=right id=mo title="Porcentaje parcial sobre el incentivo ">% pago parcial</td>
    <td align=right id=mo title="Total sobre porcentaje de incentivos">Pago Parcial</td>	
    <td align=right id=mo title="Total de incentivo acumulado en el segundo semestre del año">Incentivo 2ªSemestre</td> 
	<td align=right id=mo title="Porcentaje parcial sobre el incentivo ">% pago parcial</td>
    <td align=right id=mo title="Total sobre porcentaje de incentivos">Pago Parcial</td>	
    <!--<td align=right id=mo title="Total de incentivo en todo el año">Total</td> -->
       
  </tr>
 <?
   while (!$result->EOF) {
  	$ref_1 = encode_link("incentivo_detalle.php",array("cuie"=>$result->fields['cuie'],"fecha_desde"=>$fecha_desde_1,"fecha_hasta"=>$fecha_hasta_1,"semestre"=>$semetre=1,"anio"=>$anio));
    $onclick_elegir_1="location.href='$ref_1'";
	$total=$total_parcial=$total_2=$total_parcial_2=0;

    	$cuie=$result->fields['cuie'];
 		$sql_1="select * from contabilidad.incentivo where cuie = '$cuie' and fecha_prefactura between '$fecha_desde_1' and '$fecha_hasta_1'";
		$res_incentivo_1=sql($sql_1,"no puede calcular el total del incentivo");
		
		while (!$res_incentivo_1->EOF) {
						$total=$total+$res_incentivo_1->fields['monto_incentivo'];
						$total_parcial=$total_parcial+$res_incentivo_1->fields['parcial'];
						$res_incentivo_1->MoveNext();	
						};
		
		$res_incentivo_1->MoveFirst();
		
		$cumple=$res_incentivo_1->fields['cumple'];
		switch  ($cumple) {
			case 3 :{$porcentaje=$res_incentivo_1->fields['porcentaje'].' %';
					/*while (!$res_incentivo_1->EOF) {
						$total=$total+$res_incentivo_1->fields['monto_incentivo'];
						$total_parcial=$total_parcial+$res_incentivo_1->fields['parcial'];
						$res_incentivo_1->MoveNext();	
						};*/
					$color_fondo_1='#00FF00';
					break;
					};
			case 1 :{/*while (!$res_incentivo_1->EOF) {
						$total=$total+$res_incentivo_1->fields['monto_incentivo'];
						$res_incentivo_1->MoveNext();	
						}; 
					$total_parcial=$total;*/
					$porcentaje="100 %"; 
					$color_fondo_1='D7D5FA';
					break;
					};
			case 0 : {$porcentaje="0 % (rechazado)";$color_fondo_1='#F78181';break;};
			case 2 : {$porcentaje="pendiente";$color_fondo_1='#F3F781';break;};
			default : break;
			};
		
			
		
		
	$ref_2 = encode_link("incentivo_detalle.php",array("cuie"=>$result->fields['cuie'],"fecha_desde"=>$fecha_desde_2,"fecha_hasta"=>$fecha_hasta_2,"semestre"=>$semetre=2,"anio"=>$anio));
    $onclick_elegir_2="location.href='$ref_2'";	
		
		
		$sql_2="select * from contabilidad.incentivo where cuie = '$cuie' and fecha_prefactura between '$fecha_desde_2' and '$fecha_hasta_2'";
  		$res_incentivo_2=sql($sql_2,"no puede calcular el total del incentivo");
		
		while (!$res_incentivo_2->EOF) {
						$total_2=$total_2+$res_incentivo_2->fields['monto_incentivo'];
						$total_parcial_2=$total_parcial_2+$res_incentivo_2->fields['parcial'];
						$res_incentivo_2->MoveNext();	
						};
		
		$res_incentivo_2->MoveFirst();
		
		$cumple_2=$res_incentivo_2->fields['cumple'];
		switch  ($cumple_2) {
			case 3 :{$porcentaje_2=$res_incentivo_2->fields['porcentaje'].' %';
					/*while (!$res_incentivo_2->EOF) {
						$total_2=$total_2+$res_incentivo_2->fields['monto_incentivo'];
						$total_parcial_2=$total_parcial_2+$res_incentivo_2->fields['parcial'];
						$res_incentivo_2->MoveNext();	
						};*/
					$color_fondo_2='#00FF00';
					break;
					};
			case 1 :{/*while (!$res_incentivo_2->EOF) {
						$total_2=$total_2+$res_incentivo_2->fields['monto_incentivo'];
						$res_incentivo_2->MoveNext();	
						}; 
					$total_parcial_2=$total_2;*/
					$porcentaje_2="100 %"; 
					$color_fondo_2='D7D5FA';
					break;
					};
			case 0 : {$porcentaje_2="0 % (rechazado)";$color_fondo_2='#F78181';break;};
			case 2 : {$porcentaje_2="pendiente";$color_fondo_2='#F3F781';break;};
			default : break;
			};
			
					
  		
  		
	?>
    
	<? //if ($total<>0){ ?>	
		
    <tr <?=atrib_tr()?>>        
     <td bgcolor='<?=$color_fondo?>'><?=$result->fields['cuie']?></td>
     <td bgcolor='<?=$color_fondo?>'><?=$result->fields['nombre']?></td>
     <td bgcolor='<?=$color_fondo?>'><?=$result->fields['cuidad']?></td> 
     <td onclick="<?=$onclick_elegir_1?>"bgcolor='<?=$color_fondo_1?>'><?=number_format($total,2,',','.')?></td> 
	 <td onclick="<?=$onclick_elegir_1?>"bgcolor='<?=$color_fondo_1?>'><?=$porcentaje?></td>	 
	 <td onclick="<?=$onclick_elegir_1?>"bgcolor='<?=$color_fondo_1?>'><?=$cumple==3 ? number_format($total_parcial,2,',','.'):(($cumple==0 or $cumple==2) ? number_format(0,2,',','.') : number_format($total,2,',','.'))?></td>
     <td onclick="<?=$onclick_elegir_2?>"bgcolor='<?=$color_fondo_2?>'><?=number_format($total_2,2,',','.')?></td> 
	 <td onclick="<?=$onclick_elegir_2?>"bgcolor='<?=$color_fondo_2?>'><?=$porcentaje_2?></td> 
	 <td onclick="<?=$onclick_elegir_2?>"bgcolor='<?=$color_fondo_2?>'><?=$cumple_2==3 ? number_format($total_parcial_2,2,',','.'):(($cumple_2==0 or $cumple_2==2) ? number_format(0,2,',','.') : number_format($total_2,2,',','.'))?></td> 	
     <!-- <td bgcolor='<?=$color_fondo?>'><?=number_format($total,2,',','.')?></td>        -->  
     
      <?//}?>        
    </tr>
	<?$result->MoveNext();
    
	$sql_total_0="select sum(monto_incentivo) as total from contabilidad.incentivo where cumple='0' and extract (year from fecha_prefactura)='$anio'";
  	$sql_total_1="select sum(monto_incentivo) as total from contabilidad.incentivo where cumple='1' and extract (year from fecha_prefactura)='$anio'";
  	$sql_total_2="select sum(monto_incentivo) as total from contabilidad.incentivo where cumple='2' and extract (year from fecha_prefactura)='$anio'";
	$sql_total_3="select sum(parcial) as total from contabilidad.incentivo where cumple='3' and extract (year from fecha_prefactura)='$anio'";
  	$res_0=sql($sql_total_0) or die;
  	$total_0=$res_0->fields['total'];
  	$res_1=sql($sql_total_1) or die;
  	$total_1=$res_1->fields['total'];
  	$res_2=sql($sql_total_2) or die;
  	$total_2=$res_2->fields['total'];
	$res_3=sql($sql_total_3) or die;
  	$total_3=$res_3->fields['total'];
	
	
	
	
	}?>
    
</table>
<br>
	<table align='center' border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='80%' cellspacing=0 cellpadding=0>
     <tr>
      <td colspan=10 bordercolor='#FFFFFF'><b>Colores de Referencia para el Listado:</b></td>
     <tr>
     <td width=30% bordercolor='#FFFFFF'>
      <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
       <tr>
        <td width=30 bgcolor='#F3F781' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Incentivos Pendientes de Confirmacion ------- TOTAL: $ <?=number_format($total_2,2,',','.')?></td>
       </tr>
       <tr>
       	<td>
       	 &nbsp;
       	</td>
       </tr>
       <tr>        
        <td width=30 bgcolor='D7D5FA' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Incentivos Aceptados ------- TOTAL: $ <?=number_format($total_1,2,',','.')?></td>
       </tr> 
       <tr>
       	<td>
       	 &nbsp;
       	</td>
       </tr>
       <tr>        
        <td width=30 bgcolor='#00FF00' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Incentivos Aceptados Parcialmente------- TOTAL: $ <?=number_format($total_3,2,',','.')?></td>
       </tr> 
       <tr>
       	<td>
       	 &nbsp;
       	</td>
       </tr>
	   <tr>        
        <td width=30 bgcolor='#F78181' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Incentivos Rechazados por falta de cumplimiento de metas ------- TOTAL: $ <?=number_format($total_0,2,',','.')?></td>
       </tr>       
      </table>
     </td>
    </table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
