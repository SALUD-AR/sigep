<?
/*
Author: sebastian 

$Revision: 1.3 $
$Date: 204/08/20  $
*/
require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($selec_fecha=='factura.fecha_factura') $criterio_filtro="Fecha de la Factura de acuerdo a Facturacion->Factura";
if ($selec_fecha=='expediente.fecha_ing') $criterio_filtro="Fecha Ingreso al Sistema de Expediente";
if ($selec_fecha=='ingreso.fecha') $criterio_filtro="Fecha de Pago de Acuerdo a Contabilidad->Ingreso";
$user=$_ses_user['login'];

if ($id_efe_conv) {
	$query ="SELECT 
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
else {

	$cuie=$_ses_user['login'];
	$sql_cuie="SELECT 
			efe_conv.*,dpto.nombre as dpto_nombre
			FROM
			nacer.efe_conv 
			left join nacer.dpto on dpto.codigo=efe_conv.departamento   
			where cuie='$cuie'";
	$res_cuie= sql($sql_cuie, "Error al traer el Efector") or fin_pagina();
	$id_efe_conv=$res_cuie->fields['id_efe_conv'];
	$nombre=$res_cuie->fields['nombre'];
	$domicilio=$res_cuie->fields['domicilio'];
	$departamento=$res_cuie->fields['dpto_nombre'];
	$localidad=$res_cuie->fields['localidad'];
	$cod_pos=$res_cuie->fields['cod_pos'];
	$cuidad=$res_cuie->fields['cuidad'];
	$referente=$res_cuie->fields['referente'];
	$tel=$res_cuie->fields['tel'];
	}	


if ($_POST['muestra']=="Muestra"){	
	
	$fecha_desde=fecha_db($_POST['fecha_desde']);
	$fecha_hasta=fecha_db($_POST['fecha_hasta']);
	
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
		      
		    
		    $sql_embarazadas="select count (num_doc) as total from trazadoras.embarazadas where cuie = '$cuie' and fecha_control between '$fecha_desde' and '$fecha_hasta'";
		    $res_sql_emb= sql($sql_embarazadas) or die;
		    $embarazadas=$res_sql_emb->fields['total'];
		    
		    $sql_embarazadas_pers = "select count (num_doc)as total 
										from (select distinct num_doc 
												from trazadoras.embarazadas 
												where 
												cuie = '$cuie' and 
												fecha_control between '$fecha_desde' and '$fecha_hasta' )as cons1";
			$res_sql_emb_pers= sql($sql_embarazadas_pers) or die;
		    $embarazadas_pers=$res_sql_emb_pers->fields['total'];
		    
		    //semana 20
		    $sql_embarazadas_20="select count (num_doc) as total from trazadoras.embarazadas where cuie = '$cuie' and fecha_control between '$fecha_desde' and '$fecha_hasta' and fecha_control <= fpp-140";
		    $res_sql_emb_20= sql($sql_embarazadas_20) or die;
		    $embarazadas_20=$res_sql_emb_20->fields['total'];
		    
		    $sql_embarazadas_20_pers = "select count (num_doc)as total 
										from (select distinct num_doc 
												from trazadoras.embarazadas 
												where 
												cuie = '$cuie' and 
												fecha_control between '$fecha_desde' and '$fecha_hasta' and
												fecha_control <= fpp-140 ) as cons1";
			$res_sql_emb_20_pers= sql($sql_embarazadas_20_pers) or die;
		    $embarazadas_20_pers=$res_sql_emb_20_pers->fields['total'];
		    
		    //antes de la semana 12
			$sql_embarazadas_12="select count (num_doc) as total from trazadoras.embarazadas where cuie = '$cuie' and fecha_control between '$fecha_desde' and '$fecha_hasta' and fecha_control <= fpp-196";
		    $res_sql_emb_12= sql($sql_embarazadas_12) or die;
		    $embarazadas_12=$res_sql_emb_12->fields['total'];
		    
		    $sql_embarazadas_12_pers = "select count (num_doc)as total 
										from (select distinct num_doc 
												from trazadoras.embarazadas 
												where 
												cuie = '$cuie' and 
												fecha_control between '$fecha_desde' and '$fecha_hasta' and
												fecha_control <= fpp-196 ) as cons1";
			$res_sql_emb_12_pers= sql($sql_embarazadas_12_pers) or die;
		    $embarazadas_12_pers=$res_sql_emb_12_pers->fields['total'];
		 
		    //trazadoras
		    $sql_partos="select count (num_doc) as total from trazadoras.partos where cuie = '$cuie' and fecha_parto between '$fecha_desde' and '$fecha_hasta'";
		    $res_sql_partos= sql($sql_partos) or die;
		    $partos=$res_sql_partos->fields['total'];
		    
		    	    
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
			
			$sql_vac="select count (num_doc) as total from trazadoras.nino_new 
						where cuie = '$cuie' and triple_viral between '$fecha_desde' and '$fecha_hasta'";
		    $res_sql_vac= sql($sql_vac) or die;
		    $ninios_vac_trz=$res_sql_vac->fields['total'];
		    
			$result2="SELECT 
					  count (facturacion.comprobante.cuie) as total_1					  
					FROM
					  facturacion.comprobante
					  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
					  INNER JOIN facturacion.nomenclador ON (facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador)
					WHERE
					  (facturacion.nomenclador.codigo = 'NPE 41' OR facturacion.nomenclador.codigo = 'V001') AND (facturacion.comprobante.cuie = '$cuie') AND
					  (facturacion.comprobante.fecha_comprobante between '$fecha_desde' and '$fecha_hasta')";
			$res_sql_vac_fac= sql($result2) or die;
		    $ninios_vac_trz_fac=$res_sql_vac_fac->fields['total_1'];
		    
//llenar con las consultas
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
 if(document.all.fecha_desde.value.indexOf("-")!=-1){
	  alert('Debe ingresar un fecha en el campo DESDE');
	  return false;
	 }
 if(document.all.fecha_hasta.value.indexOf("-")!=-1){
	  alert('Debe ingresar una fecha en el campo HASTA');
	  return false;
	 }
return true;
}
</script>

<form name='form1' action='efectores_detalle.php' method='POST'>
<input type="hidden" value="<?=$id_efe_conv?>" name="id_efe_conv">
<input type="hidden" value="<?=$cuie?>" name="cuie">
<input type="hidden" value="<?=$selec_fecha?>" name="selec_fecha">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<b>	
		Desde: <input type=text id=fecha_desde name=fecha_desde value='<?=$fecha_desde?>' size=15 readonly>
		<?=link_calendario("fecha_desde");?>
		
		Hasta: <input type=text id=fecha_hasta name=fecha_hasta value='<?=$fecha_hasta?>' size=15 readonly>
		<?=link_calendario("fecha_hasta");?> 
		
		&nbsp;&nbsp;&nbsp;
		<select name='selec_fecha'>
		  <option value="expediente.fecha_ing" <?if ($selec_fecha=='expediente.fecha_ing')echo 'selected';?>>Fecha Ingreso Sistema Expediente</option>
		  <option value="factura.fecha_factura" <?if ($selec_fecha=='factura.fecha_factura')echo 'selected';?>>Fecha Factura</option>
		  <option value="ingreso.fecha" <?if ($selec_fecha=='ingreso.fecha')echo 'selected';?>>Fecha Ingreso (Pago)</option>		  
		</select>    
	    
	    &nbsp;&nbsp;&nbsp;
	    <input type="submit" name="muestra" value='Muestra' onclick="return control_muestra()" >
	    </b>
	    
	    &nbsp;&nbsp;&nbsp;	    
        <? if ($_POST['muestra']){
        $link=encode_link("efec_detalle_pdf.php",array("id_efe_conv"=>$id_efe_conv,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta,"selec_fecha"=>$selec_fecha));?>
        <img src="../../imagenes/pdf_logo.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">
        <?}?>
	  </td>
       
     </tr>
     
     <tr>
		 <td align="center">
			 <b></b><font color="red" size="-1">* Este modulo lista Facturas Ingresadas al Sistema de Expediente y Pagadas (independientemente del criterio de busqueda)</font></b>
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
       <b> Descripcion del Efector</b>
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
<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
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
<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
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

<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
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

<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
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

<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
		 <tr align="center" id="sub_tabla" >
		 	<td colspan=6>	
		 		Detalle sobre Trazadoras
		 	</td>
		 </tr>
		 
		    <tr>
				<td align="center" colspan=2>
					Total de Partos segun periodo (por fecha de Parto): <b><?=$partos?></b>
				</td>
            </tr>
            
            <tr>
				<td align="left">
					Total de Controles de Embarazo segun periodo (por fecha de control): <b><?=$embarazadas?></b>
				</td>
				<td align="left">
					Total de Embarazadas segun periodo (por fecha de control): <b><?=$embarazadas_pers?></b>
				</td>
            </tr>
			<?
			$sql_embarazadas="select count (id_trz1) as total 
								from trazadorassps.trazadora_1 
								where cuie = '$cuie' and fecha_control_prenatal between '$fecha_desde' and '$fecha_hasta'";
		    $res_sql_emb= sql($sql_embarazadas) or die;
		    $embarazadas_fact=$res_sql_emb->fields['total'];
		    
		    $sql_embarazadas_pers = "select count (id_trz1)as total 
										from (select distinct id_trz1 
								from trazadorassps.trazadora_1 
								where cuie = '$cuie' and fecha_control_prenatal between '$fecha_desde' and '$fecha_hasta')as cons1";
			$res_sql_emb_pers= sql($sql_embarazadas_pers) or die;
		    $embarazadas_pers_fact=$res_sql_emb_pers->fields['total'];			
			?>
			<tr>
				<td align="left">
					Total de Controles de Embarazo segun periodo (por fecha de control en Facturacion) : <b><?=$embarazadas_fact?></b>
				</td>
				<td align="left">
					Total de Embarazadas segun periodo (por fecha de control en Facturacion): <b><?=$embarazadas_pers_fact?></b>
				</td>
            </tr>
            
            <tr>
			 <?$ref = encode_link("detalle_emb20.php",array("cuie"=>$cuie,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
					$onclick_elegir="location.href='$ref' target='_blank'";
					$onclick_elegir="window.open('$ref' , '_blank');";?>
					
				<td align="left" onclick="<?=$onclick_elegir?>" <?=atrib_tr1()?>>
					Total de Controles de Embarazo antes de las 20 semanas: <b><?=$embarazadas_20?></b>
				</td>
				<td align="left">
					Total de Embarazadas antes de las 20 semanas: <b><?=$embarazadas_20_pers?></b>
				</td>
			</tr> 
			
			<tr>
				<td align="left">
					Total de Controles de Embarazo antes de las 12 semanas: <b><?=$embarazadas_12?></b>
				</td>
				<td align="left">
					Total de Embarazadas antes de las 12 semanas: <b><?=$embarazadas_12_pers?></b>
				</td>
			</tr> 
			<?
			$sql_emb_15="select count (num_doc)as total
					from (select  distinct num_doc, afifechanac
							from trazadoras.embarazadas
							inner join nacer.smiafiliados ON (to_char(embarazadas.num_doc, 'FM99999999MI')=smiafiliados.afidni)
							where 
							cuie = '$cuie' and 
							fecha_control between '$fecha_desde' and '$fecha_hasta' 
							and fpp-afifechanac<5475 --menores de 15 aÃ±os
							) as cons1";
			$res_sql_emb_15= sql($sql_emb_15) or die;
		    $embarazadas_menos_15=$res_sql_emb_15->fields['total'];
			?>

			<tr>
				<td align="left">
					Total de Embarazadas Menores de 15 años: <b><?=$embarazadas_menos_15?></b>
				</td>
				<td align="left">
					&nbsp;
				</td>
			</tr> 
			<?
			$sql_embarazadas="select count (id_fichero) as total 
								from fichero.fichero 
								where cuie = '$cuie' and 
										fecha_control=f_diagnostico and
										fecha_control between '$fecha_desde' and '$fecha_hasta'";
		    $res_sql_emb= sql($sql_embarazadas) or die;
		    $embarazadas_pri=$res_sql_emb->fields['total'];
		    
		    $sql_embarazadas_20="select count (id_fichero) as total 
								from fichero.fichero 
								where cuie = '$cuie' and 
										fecha_control=f_diagnostico and
										fecha_control between '$fecha_desde' and '$fecha_hasta' and 
										fecha_control <= fpp-140";
		    $res_sql_emb_20= sql($sql_embarazadas_20) or die;
		    $embarazadas_20_pri=$res_sql_emb_20->fields['total'];
			
			$sql_embarazadas_12="select count (id_fichero) as total 
								from fichero.fichero 
								where cuie = '$cuie' and 
										fecha_control=f_diagnostico and
										fecha_control between '$fecha_desde' and '$fecha_hasta' and 
										fecha_control <= fpp-196";
		    $res_sql_emb_12= sql($sql_embarazadas_12) or die;
		    $embarazadas_12_pri=$res_sql_emb_12->fields['total'];
			?>
			
			<tr>
				<td align="left">
					Total de Primeros Controles de Embarazo (fecha de control = Fecha Primer Control Prenatal): <b><?=$embarazadas_pri?></b>
				</td>
				<td align="left">
					Total de Primeros Controles de Embarazo antes de la Semana 20: <b><?=$embarazadas_20_pri?></b>
				</td>
			</tr>
			
			<tr>
				<td align="left">
					Total de Primeros Controles de Embarazo antes de la Semana 12: <b><?=$embarazadas_12_pri?></b>
				</td>
				<td align="left">
					Total de Primeros Controles de Embarazo despues de la Semana 20: <b><?=$embarazadas_pri-$embarazadas_20_pri?></b>
				</td>
			</tr>
			<?		    
		    $sql_ninio="select count (num_doc) as total from trazadoras.nino_new 
						where 
							cuie = '$cuie' and 
							(fecha_control - fecha_nac >= 0 and fecha_control - fecha_nac < 365) and
							(fecha_control between '$fecha_desde' and '$fecha_hasta')";
		    $res_sql_ninio= sql($sql_ninio) or die;
		    $ninios_new_1=$res_sql_ninio->fields['total'];
		    
		    
		   $sql_ninio="select count (id_trz4) as total from trazadorassps.trazadora_4 
						where 
							cuie = '$cuie' and 
							(fecha_control - fecha_nac >= 0 and fecha_control - fecha_nac < 365) and
							(fecha_control between '$fecha_desde' and '$fecha_hasta')";
		    $res_sql_ninio_trzsps= sql($sql_ninio) or die;
		    
			$ninios_new_1=$res_sql_ninio->fields['total']+$res_sql_ninio_trzsps->fields['total'];
			
			$sql_ninio_pers="select count (num_doc) as total
								from (
									select distinct num_doc 
										from trazadoras.nino_new 
										where 
											cuie = '$cuie' and 
											(fecha_control - fecha_nac >= 0 and fecha_control - fecha_nac < 365) and
											(fecha_control between '$fecha_desde' and '$fecha_hasta'))as cons1";
		    $res_sql_ninio_pers= sql($sql_ninio_pers) or die;
			
			$sql_ninio_pers="select count (id_smiafiliados) as total
								from (
									select distinct id_smiafiliados from trazadorassps.trazadora_4 
									where 
										cuie = '$cuie' and 
										(fecha_control - fecha_nac >= 0 and fecha_control - fecha_nac < 365) and
										(fecha_control between '$fecha_desde' and '$fecha_hasta'))as cons1";
		    $res_sql_ninio_pers_trzsps= sql($sql_ninio_pers) or die;
						
		    $ninios_new_pers_1=$res_sql_ninio_pers->fields['total']+$res_sql_ninio_pers_trzsps->fields['total'];
		    
		    $sql_ninio="select count (num_doc) as total from trazadoras.nino_new 
						where 
							cuie = '$cuie' and 
							(fecha_control - fecha_nac >= 366 and fecha_control - fecha_nac < 730) and
							(fecha_control between '$fecha_desde' and '$fecha_hasta')";
		    $res_sql_ninio= sql($sql_ninio) or die;
		    $ninios_new_2=$res_sql_ninio->fields['total'];
		    
		    $sql_ninio_pers="select count (num_doc) as total
								from (
									select distinct num_doc 
										from trazadoras.nino_new 
										where 
											cuie = '$cuie' and 
											(fecha_control - fecha_nac >= 366 and fecha_control - fecha_nac < 730) and
											(fecha_control between '$fecha_desde' and '$fecha_hasta'))as cons1";
		    $res_sql_ninio_pers= sql($sql_ninio_pers) or die;
		    $ninios_new_pers_2=$res_sql_ninio_pers->fields['total'];
		    
		    $sql_ninio="select count (num_doc) as total from trazadoras.nino_new 
						where 
							cuie = '$cuie' and 
							(fecha_control - fecha_nac >= 731 and fecha_control - fecha_nac < 2190) and
							(fecha_control between '$fecha_desde' and '$fecha_hasta')";
		    $res_sql_ninio= sql($sql_ninio) or die;
		    $ninios_new_3=$res_sql_ninio->fields['total'];
		    
		    $sql_ninio_pers="select count (num_doc) as total
								from (
									select distinct num_doc 
										from trazadoras.nino_new 
										where 
											cuie = '$cuie' and 
											(fecha_control - fecha_nac >= 731 and fecha_control - fecha_nac < 2190) and
											(fecha_control between '$fecha_desde' and '$fecha_hasta'))as cons1";
		    $res_sql_ninio_pers= sql($sql_ninio_pers) or die;
		    $ninios_new_pers_3=$res_sql_ninio_pers->fields['total'];
		    
			//adelescentes!!!!!
			$sql_adol="select count (num_doc) as total from trazadoras.nino_new 
						where 
							cuie = '$cuie' and 
							(fecha_control - fecha_nac >= 3651 and fecha_control - fecha_nac < 7299) and
							(fecha_control between '$fecha_desde' and '$fecha_hasta')";
		    $res_sql_adol= sql($sql_adol) or die;
			
			$sql_adol="select count (id_smiafiliados) as total from trazadorassps.trazadora_10
						where 
							cuie = '$cuie' and 
							(fecha_control - fecha_nac >= 3651 and fecha_control - fecha_nac < 7299) and
							(fecha_control between '$fecha_desde' and '$fecha_hasta')";
		    $res_sql_adol_trz10= sql($sql_adol) or die;
			
		    $adol_new_3=$res_sql_adol->fields['total']+$res_sql_adol_trz10->fields['total'];
		    
		    $sql_adol_pers="select count (num_doc) as total
								from (
									select distinct num_doc 
										from trazadoras.nino_new 
										where 
											cuie = '$cuie' and 
											(fecha_control - fecha_nac >= 3651 and fecha_control - fecha_nac < 7299) and
											(fecha_control between '$fecha_desde' and '$fecha_hasta'))as cons1";
		    $res_sql_adol_pers= sql($sql_adol_pers) or die;
			
			$sql_adol_pers="select count (id_smiafiliados) as total
								from (
									select distinct id_smiafiliados
										from trazadorassps.trazadora_10
										where 
											cuie = '$cuie' and 
											(fecha_control - fecha_nac >= 3651 and fecha_control - fecha_nac < 7299) and
											(fecha_control between '$fecha_desde' and '$fecha_hasta'))as cons1";
		    $res_sql_adol_pers_trz10= sql($sql_adol_pers) or die;
			
		    $adol_new_pers_3=$res_sql_adol_pers->fields['total']+$res_sql_adol_pers_trz10->fields['total'];
			// fin de adolescentes!!

			
			//MUJERES!!!!!
			$sql_muj="select count (num_doc) as total from trazadoras.nino_new 
						where 
							cuie = '$cuie' and 
							(fecha_control - fecha_nac >= 7299 and fecha_control - fecha_nac < 23725) and
							(fecha_control between '$fecha_desde' and '$fecha_hasta')";
		    $res_sql_muj= sql($sql_muj) or die;
		    $muj_new_3=$res_sql_muj->fields['total'];
		    
		    $sql_muj_pers="select count (num_doc) as total
								from (
									select distinct num_doc 
										from trazadoras.nino_new 
										where 
											cuie = '$cuie' and 
											(fecha_control - fecha_nac >= 7299 and fecha_control - fecha_nac < 23725) and
											(fecha_control between '$fecha_desde' and '$fecha_hasta'))as cons1";
		    $res_sql_muj_pers= sql($sql_muj_pers) or die;
		    $muj_new_pers_3=$res_sql_muj_pers->fields['total'];
		    
		    // fin de MUJERES!!
		    $sql_ninio="select count (num_doc) as total from trazadoras.nino_new 
						where 
							cuie = '$cuie' and 
							(fecha_control - fecha_nac >= 0 and fecha_control - fecha_nac < 2190) and
							(fecha_control between '$fecha_desde' and '$fecha_hasta')";
		    $res_sql_ninio= sql($sql_ninio) or die;
		    $ninios_new_4=$res_sql_ninio->fields['total'];
		    
		    $sql_ninio_pers="select count (num_doc) as total
								from (
									select distinct num_doc 
										from trazadoras.nino_new 
										where 
											cuie = '$cuie' and 
											(fecha_control - fecha_nac >= 0 and fecha_control - fecha_nac < 2190) and
											(fecha_control between '$fecha_desde' and '$fecha_hasta'))as cons1";
		    $res_sql_ninio_pers= sql($sql_ninio_pers) or die;
		    $ninios_new_pers_4=$res_sql_ninio_pers->fields['total'];
		    
			?>            
           <tr align="center" id="sub_tabla" >
				<td colspan=6>	
					Controles de Nino por Rango de Edad (Solo Trazadoras con las Nuevas Curvas OMS)
				</td>
			</tr>
			
			<tr>
				<td align="left">
					Total de Controles de Ninos menor de 1 año segun periodo (por fecha de control): <b><?=$ninios_new_1?></b>
				</td> 
				<td align="left">
					Total de Niños menor de 1 año segun periodo (por fecha de control): <b><?=$ninios_new_pers_1?></b>
				</td>            
            </tr>
            
            <tr>
				<td align="left">
					Total de Controles de Niños de 1 a 2 años segun periodo (por fecha de control): <b><?=$ninios_new_2?></b>
				</td> 
				<td align="left">
					Total de Niños menor de 1 a 2 años segun periodo (por fecha de control): <b><?=$ninios_new_pers_2?></b>
				</td>            
            </tr>
             <tr>
				<td align="left">
					Total de Controles de Niños de 2 a 6 años segun periodo (por fecha de control): <b><?=$ninios_new_3?></b>
				</td> 
				<td align="left">
					Total de Niños menor de 2 a 6 años segun periodo (por fecha de control): <b><?=$ninios_new_pers_3?></b>
				</td>            
            </tr>
            
            <tr>
				<td align="left">
					Total de Controles de Niños menor de 6 años segun periodo (por fecha de control): <b><?=$ninios_new_1+$ninios_new_2+$ninios_new_3?></b>
				</td> 
				<td align="left">
					Total de Niños menor de 6 años segun periodo (por fecha de control): <b><?=$ninios_new_pers_1+$ninios_new_pers_2+$ninios_new_pers_3?></b>
				</td>            
            </tr>
			<?	$sql_cuidado_sexual="select Count (*) as total from 
									(select distinct id_smiafiliados
									from fichero.fichero 
									where cuie = '$cuie' and fecha_control between '$fecha_desde' and '$fecha_hasta' and salud_rep = 'SI') as a";
				$res_cuidado_sexual= sql($sql_cuidado_sexual) or die;
				$cuidado_sexual=$res_cuidado_sexual->fields['total'];?>
			<tr>
				<td align="left">
					Total de Dosis de Vacunas Triple Viral FACTURADO: <b><?=$ninios_vac_trz+$ninios_vac_trz_fac?></b>
				</td> 
				<?$ref = encode_link("detalle_ssr.php",array("cuie"=>$cuie,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
					$onclick_elegir="location.href='$ref' target='_blank'";
					$onclick_elegir="window.open('$ref' , '_blank');";?>
				<td align="left" onclick="<?=$onclick_elegir?>" <?=atrib_tr1()?>>
					Total de Inscriptos que Marca Cuidado Sexual y Reproductivo: <b><?=$cuidado_sexual?></b>
				</td>            
            </tr>
            
            <?$sql="select sum (cantidad) as total FROM 
					(SELECT  nacer.efe_conv.cuie, 
													nacer.efe_conv.nombre as nom_efector,
													trazadoras.vac_apli.nombre as nom_vacum,
													trazadoras.dosis_apli.nombre as dosis,
													count(trazadoras.vac_apli.nombre)as cantidad
												FROM
													trazadoras.vacunas
													INNER JOIN nacer.efe_conv ON trazadoras.vacunas.cuie = nacer.efe_conv.cuie
													INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
													INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
													LEFT OUTER JOIN leche.beneficiarios on trazadoras.vacunas.id_beneficiarios= leche.beneficiarios.id_beneficiarios
													LEFT OUTER JOIN nacer.smiafiliados on trazadoras.vacunas.id_smiafiliados= nacer.smiafiliados.id_smiafiliados
												where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta')and (nacer.efe_conv.cuie='$cuie') 
													and (trazadoras.vacunas.eliminada=0) and (vac_apli.nombre='Triple Viral')
												GROUP BY
													nacer.efe_conv.cuie,
													nacer.efe_conv.nombre ,
													trazadoras.vac_apli.nombre,
													trazadoras.dosis_apli.nombre
													) AS cons";
			$ninios_vac_fichero=sql($sql,"");
			$ninios_vac_fichero=$ninios_vac_fichero->fields['total']
            ?>
            <tr>
				<td align="left">
					Total de Dosis de Vacunas Triple Viral CARGADAS EN FICHERO: <b><?=$ninios_vac_fichero?></b>
				</td> 
				<td align="left">
				</td>            
            </tr>
            
            <?	$sql_dia="select count (id_fichero) as total 
										from fichero.fichero 
										where cuie = '$cuie' and fecha_control between '$fecha_desde' and '$fecha_hasta' and diabetico = 'SI'";
				$res_dia= sql($sql_dia) or die;
				$dia=$res_dia->fields['total'];
				
				$sql_hip="select count (id_fichero) as total 
										from fichero.fichero 
										where cuie = '$cuie' and fecha_control between '$fecha_desde' and '$fecha_hasta' and hipertenso = 'SI'";
				$res_hip= sql($sql_hip) or die;
				$hip=$res_hip->fields['total'];?>
			<tr>
			    <?$ref = encode_link("detalle_diab.php",array("cuie"=>$cuie,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
					$onclick_elegir="location.href='$ref' target='_blank'";
					$onclick_elegir="window.open('$ref' , '_blank');";?>
				<td align="left" onclick="<?=$onclick_elegir?>" <?=atrib_tr1()?>>
					Total de Controles que Marca Diabetico: <b><?=$dia?></b>
				</td> 
				<?$ref = encode_link("detalle_hip.php",array("cuie"=>$cuie,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
					$onclick_elegir="location.href='$ref' target='_blank'";
					$onclick_elegir="window.open('$ref' , '_blank');";?>
				<td align="left" onclick="<?=$onclick_elegir?>" <?=atrib_tr1()?>>
					Total de Controles que Marca Hipertenso: <b><?=$hip?></b>
				</td>            
            </tr> 
            
            <tr>
				<td align="left">
					Total de Controles de Adolescentes de 10 a 19 años segun periodo (por fecha de control): <b><?=$adol_new_3?></b>
				</td> 
				<td align="left">
					Total de Adolescentes de 10 a 19 años segun periodo (por fecha de control): <b><?=$adol_new_pers_3?></b>
				</td>            
            </tr>
			
			<tr>
				<td align="left">
					Total de Controles de Mujeres de 20 a 64 años segun periodo (por fecha de control): <b><?=$muj_new_3?></b>
				</td> 
				<td align="left">
					Total de Mujeres de 20 a 64 años segun periodo (por fecha de control): <b><?=$muj_new_pers_3?></b>
				</td>            
            </tr>
                      
</table>

<?$sql="select grupopoblacional,ceb,count (ceb) as total from nacer.smiafiliados 
where cuieefectorasignado='$cuie' and activo='S'
group by
ceb,grupopoblacional
order by grupopoblacional,ceb";
$result_ceb=sql($sql,"no se puede ejecutar la consulta");?>

<table width="60%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
		 <tr align="center" id="sub_tabla">
		 	<td colspan=8>	
		 		Detalle sobre Cobertura Efectiva
		 	</td>
		 </tr>	
		 <tr>
			<td align=right id=mo><a id=mo>Grupo Poblacional</a></td> 
			<td align=right id=mo><a id=mo>Cobertura</a></td>  
			<td align=right id=mo><a id=mo>Cantidad</a></td>
		</tr>
		<?while (!$result_ceb->EOF) {
			$ref = encode_link("detalle_ceb.php",array("cuie"=>$cuie,"ceb"=>$result_ceb->fields['ceb'],"grupo_poblacional"=>$result_ceb->fields['grupopoblacional']));
			$onclick_elegir="location.href='$ref' target='_blank'";
			$onclick_elegir="window.open('$ref' , '_blank');";?>			
			<tr <?=atrib_tr1()?>>        
			 <td align=left onclick="<?=$onclick_elegir?>"><?=$result_ceb->fields['grupopoblacional']?></td>
			 <td align=left onclick="<?=$onclick_elegir?>"><?=$result_ceb->fields['ceb']?></td>
			 <td align=left onclick="<?=$onclick_elegir?>"><?=$result_ceb->fields['total']?></td>		      
			 </tr>
			<?$result_ceb->MoveNext();
		} ?> 		 
</table>
<?}?>
<BR>
 <tr><td><table width=90% align="center" class="bordes">
  <tr align="center">
    <td>
     <?
     $ref_det = encode_link("detalle_cumplimiento.php",array("id_efe_conv"=>$id_efe_conv,"fecha_desde"=>fecha($fecha_desde),"fecha_hasta"=>fecha($fecha_hasta)));
     $onclick_elegir_det="location.href='$ref_det'";?>
     <input type=button style="background-color:'#F5D0A9'" name="cumplimiento" value="Evaluacion de Cumplimientos" onclick="<?=$onclick_elegir_det?>" title="Evaluacion de Cumplimientos" style="width=200px">     
   </td>
   
   <td>
     <?
     $ref_det_1 = encode_link("detalle_gestion.php",array("id_efe_conv"=>$id_efe_conv,"fecha_desde"=>fecha($fecha_desde),"fecha_hasta"=>fecha($fecha_hasta)));
     $onclick_elegir_det_1="location.href='$ref_det_1'";?>
     <input type=button style="background-color:'#2FF815'" name="gestion" value="Detalle de Gestion" onclick="<?=$onclick_elegir_det_1?>" title="Detalle de Gestion" style="width=200px">     
   </td>
   <?
   if (!es_cuie($user)){ ?>
		<td>
     	<input type=button name="volver" value="Volver" onclick="document.location='seguimiento.php'"title="Volver al Listado" style="width=150px">     
   		</td>
   <?} ?>

   
  
  </tr>
 </table></td></tr>
 
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>
