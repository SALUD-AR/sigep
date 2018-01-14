<?php
/*
Author: sebastian lohaiza

modificada por
$Author: seba $
$Revision: 1.30 $
$Date: 2012/03/01 18:25:40 $
*/
require_once("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

//consultas
$sql="SELECT * FROM nacer.efe_conv where id_efe_conv='$id_efe_conv'";
$result=sql($sql) or fin_pagina();
$cuie=$result->fields['cuie'];
$nombre=$result->fields['nombre'];
$domicilio=$result->fields['domicilio'];
$departamento=$result->fields['dpto_nombre'];
$localidad=$result->fields['localidad'];
$cod_pos=$result->fields['cod_pos'];
$cuidad=$result->fields['cuidad'];
$referente=$result->fields['referente'];
$tel=$result->fields['tel'];

$sql_activos="select count (clavebeneficiario) as total 
							from nacer.smiafiliados 
							where cuieefectorasignado='$cuie' and activo='S' and fechacarga between '$fecha_desde' and '$fecha_hasta'";
		    $res_sql_activos= sql($sql_activos) or die;
		    $activos=$res_sql_activos->fields['total'];
		    
		    $sql_inactivos="select count (clavebeneficiario) as total 
							from nacer.smiafiliados 
							where cuieefectorasignado='$cuie' and activo='N' and fechacarga between '$fecha_desde' and '$fecha_hasta'";
		    $res_sql_inactivos= sql($sql_inactivos) or die;
		    $inactivos=$res_sql_inactivos->fields['total'];
		    
		    $sql_todos="select count (clavebeneficiario) as total 
						from nacer.smiafiliados 
						where cuieefectorasignado='$cuie' and fechacarga between '$fecha_desde' and '$fecha_hasta'";
		    $res_sql_todos= sql($sql_todos) or die;
		    $todos=$res_sql_todos->fields['total'];
		    
//total de facturas ingresadas en una derminada fecha
		    
		   $sql_facturado="select count (id_expediente) as total, sum (monto) as monto 
							from expediente.expediente 
							inner join facturacion.factura using (id_factura)
							left join expediente.estado_expediente ON (expediente.control = estado_expediente.estado)
							left join contabilidad.ingreso ON (ingreso.numero_factura=expediente.id_factura)
							where id_efe_conv = 
								(select id_efe_conv from nacer.efe_conv where cuie = '$cuie')
								and $selec_fecha between '$fecha_desde' and '$fecha_hasta'";
		    $res_sql_facturado= sql($sql_facturado) or die;
		    $facturado_total=$res_sql_facturado->fields['total'];
		    $monto_f=$res_sql_facturado->fields['monto'];
		    
//detalle de facturas ingresadas en una determinada fecha	
	    
		    $sql_facturado_det="select expediente.id_expediente, expediente.nro_exp, expediente.id_factura , expediente.monto,expediente.fecha_ing, factura.fecha_factura, factura.periodo, factura.periodo_actual,  factura.periodo_contable, factura.observaciones, estado_expediente.desc_estado, expediente.control, ingreso.fecha
								from expediente.expediente 
								inner join facturacion.factura using (id_factura)
								left join expediente.estado_expediente ON (expediente.control = estado_expediente.estado)
								left join contabilidad.ingreso ON (ingreso.numero_factura=expediente.id_factura)
								where id_efe_conv = 
									(select id_efe_conv from nacer.efe_conv where cuie = '$cuie')
									and $selec_fecha between '$fecha_desde' and '$fecha_hasta' ORDER BY id_factura DESC";
		    $res_sql_facturado_det= sql($sql_facturado_det) or die;
		       
//total de facturas liquidadas (pagas), el final del circuito de pago es la variable "control = 5"
		    
		    $sql_liquidado="select count (id_expediente) as total, sum (monto) as monto 
							from expediente.expediente 
							inner join facturacion.factura using (id_factura)	
							left join expediente.estado_expediente ON (expediente.control = estado_expediente.estado)
							left join contabilidad.ingreso ON (ingreso.numero_factura=expediente.id_factura)	
							where id_efe_conv = 
								(select id_efe_conv from nacer.efe_conv where cuie = '$cuie')
								 and control=5 and $selec_fecha between '$fecha_desde' and '$fecha_hasta' ";
		    $res_sql_liquidado= sql($sql_liquidado) or die;
		    $liquidado_total=$res_sql_liquidado->fields['total'];
		    $monto_l=$res_sql_liquidado->fields['monto'];

//detalle completo de las facturas pagas en ese periodo, control=5
		    
		    $sql_facturado_liq="select expediente.id_expediente, expediente.nro_exp, expediente.id_factura , expediente.monto,expediente.fecha_ing, factura.fecha_factura, factura.periodo, factura.periodo_actual,  factura.periodo_contable, factura.observaciones, estado_expediente.desc_estado, expediente.control, ingreso.fecha
								from expediente.expediente 
								inner join facturacion.factura using (id_factura)	
								left join expediente.estado_expediente ON (expediente.control = estado_expediente.estado)
								left join contabilidad.ingreso ON (ingreso.numero_factura=expediente.id_factura)							
								where id_efe_conv = 
										(select id_efe_conv from nacer.efe_conv where cuie = '$cuie')
										and control=5 and $selec_fecha between '$fecha_desde' and '$fecha_hasta' ORDER BY id_factura DESC";
		    $res_sql_facturado_liq= sql($sql_facturado_liq) or die;
		      
		    
		    $sql_embarazadas="select count (clave) as total from trazadoras.embarazadas where cuie = '$cuie' and fecha_control between '$fecha_desde' and '$fecha_hasta'";
		    $res_sql_emb= sql($sql_embarazadas) or die;
		    $embarazadas=$res_sql_emb->fields['total'];
		    
		    $sql_embarazadas_20="select count (clave) as total from trazadoras.embarazadas where cuie = '$cuie' and fecha_control between '$fecha_desde' and '$fecha_hasta' and fecha_control <= fpp-140";
		    $res_sql_emb_20= sql($sql_embarazadas_20) or die;
		    $embarazadas_20=$res_sql_emb_20->fields['total'];
		 
		    $sql_partos="select count (clave) as total from trazadoras.partos where cuie = '$cuie' and fecha_parto between '$fecha_desde' and '$fecha_hasta'";
		    $res_sql_partos= sql($sql_partos) or die;
		    $partos=$res_sql_partos->fields['total'];
		    
		    $sql_ninio="select count (id_nino_new) as total from trazadoras.nino_new where cuie = '$cuie' and fecha_control between '$fecha_desde' and '$fecha_hasta'";
		    $res_sql_ninio= sql($sql_ninio) or die;
		    $ninios_new=$res_sql_ninio->fields['total'];
		    
		    $sql_ninio="select count (id_nino) as total from trazadoras.nino where cuie = '$cuie' and fecha_control between '$fecha_desde' and '$fecha_hasta'";
		    $res_sql_ninio= sql($sql_ninio) or die;
		    $ninios=$res_sql_ninio->fields['total']+$ninios_new;
		    
		    $sql_comprobantes="select nomenclador.codigo, nomenclador.descripcion, nomenclador_detalle.descripcion as descripcion_nomenclador , con1.cantidad, nomenclador.precio, (con1.cantidad*nomenclador.precio) as total
								from(
									select nomenclador.id_nomenclador, count (nomenclador.id_nomenclador) as cantidad
																	from expediente.expediente 
																	inner join facturacion.factura using (id_factura)
																	inner join facturacion.comprobante using (id_factura)
																	inner join facturacion.prestacion using (id_comprobante)
																	inner join facturacion.nomenclador using (id_nomenclador)
																	inner join contabilidad.ingreso ON (ingreso.numero_factura=expediente.id_factura)							
																	where id_efe_conv = 
																			(select id_efe_conv from nacer.efe_conv where cuie = '$cuie')
																			and control=5 and $selec_fecha between '$fecha_desde' and '$fecha_hasta' 

									group by (nomenclador.id_nomenclador)
									) as con1
								inner join facturacion.nomenclador using (id_nomenclador)
								inner join facturacion.nomenclador_detalle using (id_nomenclador_detalle)
								order by nomenclador.codigo";
		    $res_comprobantes=sql ($sql_comprobantes) or die;
		    
		    $sql_anexo="select nomenclador.codigo, nomenclador.descripcion,anexo.prueba, nomenclador_detalle.descripcion as descripcion_nomenclador , con1.cantidad
								from(
									select anexo.id_anexo, count (anexo.id_anexo) as cantidad
																	from expediente.expediente 
																	inner join facturacion.factura using (id_factura)
																	inner join facturacion.comprobante using (id_factura)
																	inner join facturacion.prestacion using (id_comprobante)
																	inner join facturacion.anexo using (id_anexo)
																	inner join contabilidad.ingreso ON (ingreso.numero_factura=expediente.id_factura)							
																	where id_efe_conv = 
																			(select id_efe_conv from nacer.efe_conv where cuie = '$cuie')
																			and control=5 and $selec_fecha between '$fecha_desde' and '$fecha_hasta' 

									group by (anexo.id_anexo)
									) as con1
								inner join facturacion.anexo using (id_anexo)
								inner join facturacion.nomenclador_detalle using (id_nomenclador_detalle)
								left join facturacion.nomenclador using (id_nomenclador)

								order by codigo";
		    $res_anexo=sql ($sql_anexo) or die;		    

excel_header("listado de seguimiento.xls");

?>
<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<font size=+1><b>Efector: <?echo $cuie.". Desde: ".fecha($fecha_desde)." Hasta: ".fecha($fecha_hasta)?> </b></font>        
    </td>
 </tr>
 <tr><td>
  <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="5">
       <b> Descripción del Efector</b>
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

<?if ($id_efe_conv){?>
<table width="90%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
		 <tr align="center" id="sub_tabla">
		 	<td colspan=6 title="Utiliza la Tabla nacer.smiafiliados">	
		 		Detalle sobre Inscripcion <BR>(Validados por UEC)
		 	</td>
		 </tr>
		 
		 <tr>	    
		    <td align="left" >
				Total Inscripciones ACTIVOS segun periodo: <b><?=$activos?> </b>			    
            </td>
            <td align="left" >
				Total Inscripciones INACTIVOS segun periodo:<b> <?=$inactivos?></b>			    
            </td>   
            <td align="left">
				Total Inscripciones TOTALES segun periodo: <b><?=$todos?></b>
            </td>         
		 </tr> 
</table>	 
<table width="90%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
		<tr align="center" id="sub_tabla">
		 	<td colspan=6 >	
		 		Detalle sobre Facturacion <BR>(Criterio de Filtro: <?=$criterio_filtro;?>)
		 	</td>
		 </tr>
		
		 <tr>
		    <td align="left">
				Cantidad de Facturas en el periodo: <b><?=$facturado_total?></b>, por un total de: <b>$<?=$monto_f?></b>
            </td>
            <td align="left">
				Cantidad de Facturas LIQUIDADAS (PAGAS) en el periodo: <b><?=$liquidado_total?></b>, por un total de: <b>$<?=$monto_l?></b>
            </td>
		 </tr> 
</table>	 
<table width="90%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
		 <tr align="center" id="sub_tabla">
		 	<td colspan=10>	
		 		Detalle sobre Facturas en el periodo <BR>(Criterio de Filtro: <?=$criterio_filtro;?>)
		 	</td>
		 </tr>
		 
		 <tr>
			<td align=right id=mo><a id=mo>Numero de Expediente</a></td> 
			<td align=right id=mo><a id=mo>Numero de Factura</a></td>  
			<td align=right id=mo><a id=mo>Monto</a></td>  
			<td align=right id=mo><a id=mo>Fecha Factura</a></td>
		    <td align=right id=mo><a id=mo>Fecha Ingr. Exp.</a></td>
			<td align=right id=mo><a id=mo>Fecha Ingreso (pago)</a></td>
			<td align=right id=mo><a id=mo>Periodo Factura</a></td>
			<td align=right id=mo><a id=mo>Periodo Prestacion</a></td>
			<td align=right id=mo><a id=mo>Periodo Contable</a></td>
			<td align=right id=mo><a id=mo>Observaciones</a></td>
			</tr>
 <?
  if  ($res_sql_facturado_det) {
  while (!$res_sql_facturado_det->EOF) {
	 
	 $id_expediente=$res_sql_facturado_det->fields['id_expediente'];
     $sql_trans_exp="select * 
					from expediente.transaccion 
					inner join expediente.ref_expediente ON (transaccion.estado=ref_expediente.ref_exp)
					where id_expediente=$id_expediente
					order by id_transac DESC";
     $trans_exp_res=sql($sql_trans_exp,"error culiau") or die;
     $ref = encode_link("../expediente/historial_det.php",array("id_expediente"=>$id_expediente));
	 ?>
    <tr <?=atrib_tr1()?>>   
     <td align=left title="<?=$res_sql_facturado_det->fields['desc_estado']." - ".$trans_exp_res->fields['desc_ref'];?>" BGCOLOR="<?=($res_sql_facturado_det->fields['control']=='5')?'':'#F79F81';?>" onclick = window.open('<?=$ref?>')>
     	<?=$res_sql_facturado_det->fields['nro_exp'];?></td>
     <td align=left title="<?=$res_sql_facturado_det->fields['desc_estado']." - ".$trans_exp_res->fields['desc_ref'];?>" BGCOLOR="<?=($res_sql_facturado_det->fields['control']=='5')?'':'#F79F81';?>" onclick = window.open('<?=$ref?>')>
		<?=$res_sql_facturado_det->fields['id_factura'];?></td>
     <td align=left><?=$res_sql_facturado_det->fields['monto'];?></td>
     <td align=left><?=fecha($res_sql_facturado_det->fields['fecha_factura']);?></td>
     <td align=left><?=fecha($res_sql_facturado_det->fields['fecha_ing']);?></td>
     <td align=left><?=fecha($res_sql_facturado_det->fields['fecha']);?></td>
     <td align=left><?=$res_sql_facturado_det->fields['periodo'];?></td>
     <td align=left><?=$res_sql_facturado_det->fields['periodo_actual'];?></td>
     <td align=left><?=$res_sql_facturado_det->fields['periodo_contable'];?></td>
     <td align=left><?=$res_sql_facturado_det->fields['observaciones'];?></td>
     
	<?$res_sql_facturado_det->MoveNext();
    }    
  }?>   
</table>

<table width="90%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
		 <tr align="center" id="sub_tabla">
		 	<td colspan=10>	
		 		Detalle sobre Facturas LIQUDADAS (pagas) en el periodo <BR> (Criterio de Filtro: <?=$criterio_filtro;?>)
		 	</td>
		 </tr>
		 
		 <tr>
			<td align=right id=mo><a id=mo>Numero de Expediente</a></td> 
			<td align=right id=mo><a id=mo>Numero de Factura</a></td>  
			<td align=right id=mo><a id=mo>Monto</a></td>       	
			<td align=right id=mo><a id=mo>Fecha Factura</a></td>
		    <td align=right id=mo><a id=mo>Fecha Ingr. Exp.</a></td>
			<td align=right id=mo><a id=mo>Fecha Ingreso (pago)</a></td>
			<td align=right id=mo><a id=mo>Periodo Factura</a></td>
			<td align=right id=mo><a id=mo>Periodo Prestacion</a></td>
			<td align=right id=mo><a id=mo>Periodo Contable</a></td>
			<td align=right id=mo><a id=mo>Observaciones</a></td>
		</tr>
 <?
  if  ($res_sql_facturado_liq) {
  while (!$res_sql_facturado_liq->EOF) {
  	?>
    <tr <?=atrib_tr1()?>>  
    
    <td align=left><?=$res_sql_facturado_liq->fields['nro_exp'];?></td>
     <td align=left><?=$res_sql_facturado_liq->fields['id_factura'];?></td>
     <td align=left><?=$res_sql_facturado_liq->fields['monto'];?></td>
     <td align=left><?=fecha($res_sql_facturado_liq->fields['fecha_factura']);?></td>
     <td align=left><?=fecha($res_sql_facturado_liq->fields['fecha_ing']);?></td>
     <td align=left><?=fecha($res_sql_facturado_liq->fields['fecha']);?></td>
     <td align=left><?=$res_sql_facturado_liq->fields['periodo'];?></td>
     <td align=left><?=$res_sql_facturado_liq->fields['periodo_actual'];?></td>
     <td align=left><?=$res_sql_facturado_liq->fields['periodo_contable'];?></td>
     <td align=left><?=$res_sql_facturado_liq->fields['observaciones'];?></td>      
    </tr>
	<?$res_sql_facturado_liq->MoveNext();
    }    
  }?>   
</table>

<table width="90%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
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

<table width="90%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
		<tr align="center" id="sub_tabla">
		 	<td colspan=8>	
		 		Detalle sobre Anexos de Facturas LIQUDADAS (pagas) en el periodo <BR>(Criterio de Filtro: <?=$criterio_filtro;?>)
		 	</td>
		 </tr>	
		 <tr>
			<td align=right id=mo><a id=mo>Codigo</a></td> 
			<td align=right id=mo><a id=mo>Descripcion</a></td>  
			<td align=right id=mo><a id=mo>Anexo</a></td>  
			<td align=right id=mo><a id=mo>Cantidad</a></td>
			<td align=right id=mo><a id=mo>Nomenclador</a></td>  
		</tr>
		<?
		  if  ($res_anexo) {
		  while (!$res_anexo->EOF) {
			?>
			<tr <?=atrib_tr1()?>>        
			 <td align=left><?=$res_anexo->fields['codigo']?></td>
			 <td align=left><?=$res_anexo->fields['descripcion']?></td>
			 <td align=left><?=$res_anexo->fields['prueba']?></td>
			 <td align=right><?=$res_anexo->fields['cantidad']?></td>
			 <td align=right><?=$res_anexo->fields['descripcion_nomenclador']?></td>		      
			 </tr>
			<?$res_anexo->MoveNext();
			}    
		  }?>   
		 
</table>

<table width="90%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
		 <tr align="center" id="sub_tabla" >
		 	<td colspan=6>	
		 		Detalle sobre Trazadoras
		 	</td>
		 </tr>
		 
		    <tr>
		    <td align="left">
				Total de Partos segun periodo: <b><?=$partos?></b>
            </td>
            <td align="left">
				Total de Niños segun periodo (segun fecha de control): <b><?=$ninios?></b>
            </td>
            
            </tr>
            <td align="left">
				Total de Embarazadas segun periodo (segun fecha de control): <b><?=$embarazadas?></b>
            </td>
            <td align="left">
				Total de Embarazadas antes de las 20 semanas del parto: <b><?=$embarazadas_20?></b>
            </td>
			</tr> 
</table>	 
 </form>
