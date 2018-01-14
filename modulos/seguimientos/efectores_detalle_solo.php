<?

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['muestra']=="Muestra"){	
	
	$fecha_desde=fecha_db($_POST['fecha_desde']);
	$fecha_hasta=fecha_db($_POST['fecha_hasta']);
	
					    
//detalle de facturas ingresadas en una determinada fecha	
	    
		    $sql_facturado_det="select factura.id_factura, factura.fecha_factura, factura.periodo, factura.periodo_actual,  factura.periodo_contable, factura.observaciones
								from facturacion.factura 
								where cuie = '$cuie' and factura.fecha_factura between '$fecha_desde' and '$fecha_hasta' and estado='C' ORDER BY id_factura DESC";
		    $res_sql_facturado_det= sql($sql_facturado_det) or die;
		       
		    $sql_comprobantes="select nomenclador.codigo, nomenclador.descripcion, nomenclador_detalle.descripcion as descripcion_nomenclador , con1.cantidad, nomenclador.precio, (con1.cantidad*nomenclador.precio) as total
								from(
									select nomenclador.id_nomenclador, count (nomenclador.id_nomenclador) as cantidad
																	from facturacion.factura
																	inner join facturacion.comprobante using (id_factura)
																	inner join facturacion.prestacion using (id_comprobante)
																	inner join facturacion.nomenclador using (id_nomenclador)							
																	where factura.cuie = '$cuie' and factura.fecha_factura between '$fecha_desde' and '$fecha_hasta' and estado='C'

									group by (nomenclador.id_nomenclador)
									) as con1
								inner join facturacion.nomenclador using (id_nomenclador)
								inner join facturacion.nomenclador_detalle using (id_nomenclador_detalle)
								order by nomenclador.codigo";
		   $res_comprobantes=sql ($sql_comprobantes) or die;		
}

if ($id_efe_conv) {
$query="SELECT 
  efe_conv.*,dpto.nombre as dpto_nombre
FROM
  nacer.efe_conv 
  left join nacer.dpto on dpto.codigo=efe_conv.departamento   
  where id_efe_conv=$id_efe_conv";

$res_factura=sql($query, "Error al traer el Efector") or fin_pagina();

$cuie=$res_factura->fields['cuie'];
$nombre=$res_factura->fields['nombre'];
$domicilio=$res_factura->fields['domicilio'];
$departamento=$res_factura->fields['dpto_nombre'];
$localidad=$res_factura->fields['localidad'];
$cod_pos=$res_factura->fields['cod_pos'];
$cuidad=$res_factura->fields['cuidad'];
$referente=$res_factura->fields['referente'];
$tel=$res_factura->fields['tel'];

}
echo $html_header;
?>
<script>
function control_muestra()
{ 
 if(document.all.fecha_desde.value==""){
  alert('Debe Ingresar una Fecha DESDE');
  return false;
 } 
 if(document.all.fecha_hasta.value==""){
  alert('Debe Ingresar una Fecha HASTA');
  return false;
 } 
 if(document.all.fecha_hasta.value<document.all.fecha_desde.value){
  alert('La Fecha HASTA debe ser MAYOR 0 IGUAL a la Fecha DESDE');
  return false;
 }
return true;
}
</script>

<form name='form1' action='efectores_detalle_solo.php' method='POST'>
<input type="hidden" value="<?=$id_efe_conv?>" name="id_efe_conv">
<input type="hidden" value="<?=$cuie?>" name="cuie">
<input type="hidden" value="<?=$selec_fecha?>" name="selec_fecha">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<b>	
		<?if ($fecha_desde=='') $fecha_desde=DATE ('d/m/Y');
		if ($fecha_hasta=='') $fecha_hasta=DATE ('d/m/Y');?>		
		Desde: <input type=text id=fecha_desde name=fecha_desde value='<?=$fecha_desde?>' size=15 readonly>
		<?=link_calendario("fecha_desde");?>
		
		Hasta: <input type=text id=fecha_hasta name=fecha_hasta value='<?=$fecha_hasta?>' size=15 readonly>
		<?=link_calendario("fecha_hasta");?> 
		
		&nbsp;&nbsp;&nbsp;
	    <input type="submit" name="muestra" value='Muestra' onclick="return control_muestra()" >
	    </b>	    
	  </td>       
     </tr>
     
     <tr>
		 <td align="center">
			 <b></b><font color="red" size="-1">* Este modulo lista Facturas CERRADAS)</font></b>
		 </td>
     </tr>
     
</table>
<table width="98%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<font size=+1><b>Efector: <?echo $cuie.". Desde: ".fecha($fecha_desde)." Hasta: ".fecha($fecha_hasta)?> </b></font>        
    </td>
 </tr>
 <tr><td>
  <table width=100% align="center" class="bordes">
     <tr>
      <td id=mo colspan="5">
       <b> Descripci�n del Efector</b>
      </td>
     </tr>
     <tr>
       <td>
        <table align="center">
                
         <td align="right">
				<b>Nombre:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$nombre?>" name="nombre" readonly>
            </td>
         </tr>
         
         <tr>	           
           
         <tr>
         <td align="right">
				<b>Domicilio:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$domicilio?>" name="domicilio" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Departamento:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$departamento?>" name="departamento" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Localidad:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$localidad?>" name="localidad" readonly>
            </td>
         </tr>
        </table>
      </td>      
      <td>
        <table align="center">        
         <tr>
         <td align="right">
				<b>Codigo Postal:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$cod_pos?>" name="cod_pos" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Cuidad:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$cuidad?>" name="cuidad" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Referente:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$referente?>" name="referente" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Telefono:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$tel?>" name="tel" readonly>
            </td>
         </tr>          
        </table>
      </td>  
       
     </tr> 
           
 </table>           

<?if ($_POST['muestra']){?>
<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
		 <tr align="center" id="sub_tabla">
		 	<td colspan=10>	
		 		Detalle sobre Facturas Cerradas en el periodo
		 	</td>
		 </tr>
		 
		 <tr>
			<td align=right id=mo><a id=mo>Numero de Factura</a></td>  
			<td align=right id=mo><a id=mo>Fecha Factura</a></td>
			<td align=right id=mo><a id=mo>Periodo Factura</a></td>
			<td align=right id=mo><a id=mo>Periodo Prestacion</a></td>
			<td align=right id=mo><a id=mo>Periodo Contable</a></td>
			<td align=right id=mo><a id=mo>Observaciones</a></td>
		</tr>
 <?
  while (!$res_sql_facturado_det->EOF) {?>
	<tr> 
     <td align=left><?=$res_sql_facturado_det->fields['id_factura'];?></td>
     <td align=left><?=fecha($res_sql_facturado_det->fields['fecha_factura']);?></td>
     <td align=left><?=$res_sql_facturado_det->fields['periodo'];?></td>
     <td align=left><?=$res_sql_facturado_det->fields['periodo_actual'];?></td>
     <td align=left><?=$res_sql_facturado_det->fields['periodo_contable'];?></td>
     <td align=left><?=$res_sql_facturado_det->fields['observaciones'];?></td>
    </tr> 
	<?$res_sql_facturado_det->MoveNext();
    }?>   
</table>



<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
		 <tr align="center" id="sub_tabla">
		 	<td colspan=7>	
		 		Detalle sobre Prestaciones de Facturas LIQUDADAS (pagas) en el periodo <BR>(Criterio de Filtro: <?=$criterio_filtro;?>)
		 	</td>
		 </tr>
		 
		 <tr>
			<td align=right id=mo><a id=mo>Codigo</a></td> 
			<td align=right id=mo><a id=mo>Descripcion</a></td>  
			<td align=right id=mo><a id=mo>Nomenclador Usado</a></td>       	
			<td align=right id=mo><a id=mo>Cantidad</a></td>
			<td align=right id=mo><a id=mo>Precio</a></td> 
			<td align=right id=mo><a id=mo>Total</a></td>
		</tr>
 <?
  if  ($res_comprobantes) {
  while (!$res_comprobantes->EOF) {
  	?>
    <tr <?=atrib_tr1()?>>        
     <td align=left><?=$res_comprobantes->fields['codigo']?></td>
     <td align=left><?=$res_comprobantes->fields['descripcion']?></td>
     <td align=left><?=$res_comprobantes->fields['descripcion_nomenclador']?></td>
     <td align=right><?=$res_comprobantes->fields['cantidad']?></td>
     <td align=right><?=number_format($res_comprobantes->fields['precio'],2,',','.')?></td>  
     <td align=right><?=number_format($res_comprobantes->fields['total'],2,',','.')?></td>      
     </tr>
	<?$res_comprobantes->MoveNext();
    }    
  }?>   
</table>
<?}?>
<BR>
 <tr><td><table width=90% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='seguimiento.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>
