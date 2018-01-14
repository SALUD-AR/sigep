<?php
/*
Author: seba
Date: 2015/04/15 16:24:00 $
*/
require_once("../../config.php");

variables_form_busqueda("listado_fact");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

function string_fecha($periodo){
	
	$long= strlen ($periodo);
	$anio= substr ($periodo,0,4);
	$anio_int = (int)$anio;
	
	$mes= substr ($periodo,5,$long);
	$mes_int=(int)$mes;
	$dia="01";
	$fecha="$anio_int-$mes-$dia";
	return $fecha;

}

if ($cmd == "")  $cmd="2015";

$orden = array(
        "default" => "1",
        "1" => "periodo_actual",
        );
$filtro = array(
		"to_char(factura.id_factura,'999999')" => "Nro. Factura",
		"factura.cuie" => "CUIE",
        );

$datos_barra = array(
     array(
        "descripcion"=> "2013",
        "cmd"        => "2013"
     ),
     array(
        "descripcion"=> "2014",
        "cmd"        => "2014"
     ),
     array(
        "descripcion"=> "2015",
        "cmd"        => "2015"
     )
);

generar_barra_nav($datos_barra);

$sql_tmp="SELECT facturacion.factura.cuie,
	facturacion.factura.fecha_carga,
  facturacion.factura.fecha_factura,
  facturacion.factura.periodo,
  facturacion.factura.periodo_actual,
  facturacion.factura.estado,
  facturacion.factura.observaciones,
  facturacion.factura.id_factura,
  facturacion.factura.online,
  facturacion.factura.nro_exp_ext,
  facturacion.factura.fecha_exp_ext,
  facturacion.factura.periodo_contable,
  facturacion.factura.monto_prefactura,
  nacer.efe_conv.nombre

   from facturacion.factura 

   inner join nacer.efe_conv on factura.cuie=efe_conv.cuie";

   
  
$where_tmp="facturacion.factura.estado_exp=0 and facturacion.factura.estado='A' and facturacion.factura.periodo_actual like '%$cmd%'";

echo $html_header;
?>
<form name=form1 action="listado_factura_nuevo.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>  
	    &nbsp;&nbsp;
	     <? //$link=encode_link("informe_facturacion_nuevo_excel.php",array("cmd"=>$cmd));?>
    <!--    <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">-->
	          
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=100%>
      <tr id="ma">
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  
<table border=1 width=100% cellpadding=10  align=center>
  <tr>
    <td align=right id="mo">Nro Factura</a></td>      	
    <td align=right id="mo">CUIE</a></td>      	
    <td align=right id="mo">Efector</a></td>
    <td align=right id="mo">Periodo de Realizacion de la Prestacion</a></td>  
    <td align=right id="mo">Monto Factura</td>
	
	<td align=right id="mo">Cantidad de Notificacion</td>
	<td align=right id="mo">Dias de Mora segun periodo prest.</td>
	<td align=right id="mo">Notificar via Mail</td>
	<td align=right id="mo">Notificar via telefonica</td>
	</tr>
 <?
   $fecha_hoy=date("Y-m-d");
   $fecha_hoy2=fecha($fecha_hoy);
   
   
   while (!$result->EOF) {
   	$dias_mora=restaFechas (Fecha($result->fields['fecha']),$fecha_hoy2);
   	$ref1 = encode_link("notificacion_det.php",array("id_factura"=>$result->fields['id_factura'],"dias_mora"=>$dias_mora));
    $onclick_elegir="location.href='$ref1'";
   	
   	
   	$dias_mora_period=restaFechas (Fecha(string_fecha($result->fields['periodo_actual'])),$fecha_hoy2);
	$diff_dias=restaFechas(Fecha(string_fecha($result->fields['periodo_actual'])),Fecha($result->fields['fecha']));
	$diff_dias=$diff_dias-29;
	
   	$plazo=string_fecha($result->fields['periodo_actual']);
    $plazo_30=date("Y-m-d", strtotime ("$plazo +30 days"));
	$plazo_120=date("Y-m-d", strtotime ("$plazo +120 days"));
    
    /*if ($fecha_hoy<=$plazo_30) $tr=atrib_tr();
	if ($fecha_hoy>$plazo_30 && $fecha_hoy<=$plazo_150) $tr=atrib_tr1();
	if ($fecha_hoy>$plazo_150) $tr=atrib_tr2();*/
	
	//revision 16/04/2012 - dias para contabilizar las moras de presentacion
	
$dias_mora_period=$dias_mora_period-29;	   
if ($dias_mora_period<=30) $tr=atrib_tr();
if ($dias_mora_period>30 && $dias_mora_period<=60) $tr=atrib_tr1();
if ($dias_mora_period>60) $tr=atrib_tr2();

	//hasta aqui revision 16/04/2012 - dias para contabilizar las moras de presentacion
	if ($dias_mora_period>=120){
			$color='#FF0000'; 
			$title='FACTURA CON MAS DE 120 PARA PAGO';
		}
		else {
			$color=''; 
			$title='';
		}
   	//$ref = encode_link("carga_exp.php",array("id_factura"=>$result->fields['id_factura']));?>  
    <tr <?=$tr?>>      
     <td bgcolor="<?=$color?>" title="<?=$title?>"><?=$result->fields['id_factura']?></td>
     
	 <?$id_factura=$result->fields['id_factura'];
	 
	 $query_t="SELECT sum 
			(facturacion.prestacion.cantidad*facturacion.prestacion.precio_prestacion) as total
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
			  where factura.id_factura=$id_factura";
		$total=sql($query_t,"NO puedo calcular el total");
		$query_t1="SELECT sum 
			(nomenclador.prestaciones_n_op.precio) as total1
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN nomenclador.prestaciones_n_op using (id_comprobante)
			  where factura.id_factura=$id_factura";
		$total1=sql($query_t1,"NO puedo calcular el total");
		$total=$total->fields['total']+$total1->fields['total1'];?>
	 
	 <td onclick="<?=$onclick_elegir?>" ><?=$result->fields['cuie']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>
     <td onclick="<?=$onclick_elegir?>" align="center"><?=$result->fields['periodo_actual']?></td>	
     
     <td onclick="<?=$onclick_elegir?>" align="center"><?=number_format($total,2,',','.');?></td>
     
	 <?php $consulta="select id_factura, count (id_factura) as cantidad from facturacion.notificacion where id_factura='$id_factura' group by (id_factura)";
	 						$res_consulta=sql ($consulta,"Error al Ejecutar la Consulta")  or fin_pagina();
	 						$cantidad=$res_consulta->fields['cantidad'];
	 						if (!($cantidad)) $cantidad=0;?>
	 <td onclick="<?=$onclick_elegir?>" align="center"><?=$cantidad;?></td>
	  
	  <td onclick="<?=$onclick_elegir?>" align="center" bgcolor="<?=$color?>" title="<?=$title?>"><?=$dias_mora_period;?></td>
	  <td align="center">
       	 <?$link=encode_link("factura_notif.php", array("id_factura"=>$result->fields['id_factura']));	
		   echo "<a target='_blank' href='".$link."' title='Notificar al Efector via Mail'><IMG src='$html_root/imagenes/restaurar_usr.gif' height='20' width='20' border='0'></a>";?>
       </td>
     <td align="center">
       	 <?$link=encode_link("fact_notif.php", array("id_factura"=>$result->fields['id_factura']));	
		   echo "<a target='_blank' href='".$link."' title='Notificar al Efector via telefonica'><IMG src='$html_root/imagenes/telefono.jpg' height='20' width='20' border='0'></a>";?>
       </td>
     </td> 
     
    </tr>
	<?$result->MoveNext();
    }?>
    
	</table>
</table>

<br>
	<table align='center' border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='80%' cellspacing=0 cellpadding=0>
       <tr>
        <td width=30 bgcolor='#CFE8DD' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Factura con menos de 30 dias</td>
		<td width=30 bgcolor='#F3F781' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Factura entre 30 y 60 dias despues del cierre de factura</td>
		<td width=30 bgcolor='#F78181' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Factura con mas de 60 dias</td>
       </tr>       
      </table>
     </td>
    </table>

</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
