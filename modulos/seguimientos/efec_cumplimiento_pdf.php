<?php
/*
Author: sebastian lohaiza

modificada por
$Author: seba $
$Revision: 1.40 $
$Date: 2014/08/20 12:25:40 $
*/
require_once("../../config.php");
require (dirname(__FILE__).'/html2pdf/html2pdf.class.php');

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);


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

			
	//facturacion
			$sql_facturacion="select count (*) as cantidad from (
					select * from (
					select id_factura,id_expediente,nro_exp,fecha_ing,monto,periodo_fact,
					case when extract (month from fecha_ing)=1 then (extract (year from fecha_ing)-1)||'/'||'12'
					else extract (year from fecha_ing)||'/'||regexp_replace(to_char((extract (month from fecha_ing))-1,'00'),' ','','g') end ::text as periodo_ingreso  from (

					select * from (
					select id_efe_conv from nacer.efe_conv where cuie='$cuie') as cuie_efector
					left join (select * from expediente.expediente where fecha_ing between '$fecha_desde' and '$fecha_hasta') as facturas_periodo using (id_efe_conv)
 
					) as efector
					left join (select periodo_actual as periodo_fact,id_factura from facturacion.factura) as too1 using (id_factura)
					) as factura_efector where periodo_fact=periodo_ingreso and (extract (day from fecha_ing) between 1 and 12)
					) as cantidad";
			$res_sql_fact=sql($sql_facturacion) or die;
			$cant_fact=$res_sql_fact->fields['cantidad'];
			
			
//extraccion de metas
$query_meta="select *  from nacer.metas where cuie='$cuie'";
$res_query_meta=sql($query_meta, "Error al traer el Efector") or fin_pagina();
$pap_sitam=$res_query_meta->fields['pap_sitam'];
$cant_embarazadas=$res_query_meta->fields['cant_embarazadas'];
$captacion_temprana=$res_query_meta->fields['captacion_temprana'];
$promedio_controles_x_emb=$res_query_meta->fields['promedio_controles_x_emb'];
$mujeres_edad_fertil=$res_query_meta->fields['mujeres_edad_fertil'];
$cns_menor_1_año=$res_query_meta->fields['cns_menor_1_anio'];
$cns_entre_1_y_6=$res_query_meta->fields['cns_entre_1_y_6'];
$adolecentes=$res_query_meta->fields['adolecentes'];
$enfermedades_cronicas_HTA=$res_query_meta->fields['hta'];
$enfermedades_cronicas_DBT=$res_query_meta->fields['dbt'];
$vacuna_hep_b=$res_query_meta->fields['hep_b'];
$vacuna_neumococo=$res_query_meta->fields['neumococo'];
$vacuna_pentavalente=$res_query_meta->fields['pentavalente'];
$vacuna_cuadruple=$res_query_meta->fields['cuadruple'];
$vacuna_sabin=$res_query_meta->fields['sabin'];
$vacuna_triple_viral=$res_query_meta->fields['triple_viral'];
$vacuna_gripe=$res_query_meta->fields['gripe'];
$vacuna_hep_a=$res_query_meta->fields['hep_a'];
$vacuna_triple_bacteriana_celular=$res_query_meta->fields['triple_bacteriana_celular'];
$vacuna_triple_bacteriana_acelular=$res_query_meta->fields['triple_bacteriana_acelular'];
$vacuna_doble_bacteriana=$res_query_meta->fields['doble_bacteriana'];
$vacuna_vph=$res_query_meta->fields['vph'];
$vacuna_doble_viral=$res_query_meta->fields['doble_viral'];
$vacuna_fiebre_amarilla=$res_query_meta->fields['fiebre_amarilla'];

$ceb_ceroacinco=$res_query_meta->fields['ceb_ceroacinco'];
$ceb_seisanueve=$res_query_meta->fields['ceb_seisanueve'];
$ceb_diezadiecinueve=$res_query_meta->fields['ceb_diezadiecinueve'];
$ceb_veinteasesentaycuatro=$res_query_meta->fields['ceb_veinteasesentaycuatro'];

//devolucion de metas por recursos humanos
$query_meta="select *  from nacer.metasrrhh where cuie='$cuie'";
$res_query_meta=sql($query_meta, "Error al traer el Efector") or fin_pagina();
$pap_sitamrrhh=$res_query_meta->fields['pap_sitam'];
$cant_embarazadasrrhh=$res_query_meta->fields['cant_embarazadas'];
$captacion_tempranarrhh=$res_query_meta->fields['captacion_temprana'];
$promedio_controles_x_embrrhh=$res_query_meta->fields['promedio_controles_x_emb'];
$mujeres_edad_fertilrrhh=$res_query_meta->fields['mujeres_edad_fertil'];
$cns_menor_1_añorrhh=$res_query_meta->fields['cns_menor_1_anio'];
$cns_entre_1_y_6rrhh=$res_query_meta->fields['cns_entre_1_y_6'];
$adolecentesrrhh=$res_query_meta->fields['adolecentes'];
$enfermedades_cronicas_HTArrhh=$res_query_meta->fields['hta'];
$enfermedades_cronicas_DBTrrhh=$res_query_meta->fields['dbt'];
$vacuna_hep_brrhh=$res_query_meta->fields['hep_b'];
$vacuna_neumococorrhh=$res_query_meta->fields['neumococo'];
$vacuna_pentavalenterrhh=$res_query_meta->fields['pentavalente'];
$vacuna_cuadruplerrhh=$res_query_meta->fields['cuadruple'];
$vacuna_sabinrrhh=$res_query_meta->fields['sabin'];
$vacuna_triple_viralrrhh=$res_query_meta->fields['triple_viral'];
$vacuna_griperrhh=$res_query_meta->fields['gripe'];
$vacuna_hep_arrhh=$res_query_meta->fields['hep_a'];
$vacuna_triple_bacteriana_celularrrhh=$res_query_meta->fields['triple_bacteriana_celular'];
$vacuna_triple_bacteriana_acelularrrhh=$res_query_meta->fields['triple_bacteriana_acelular'];
$vacuna_doble_bacterianarrhh=$res_query_meta->fields['doble_bacteriana'];
$vacuna_vphrrhh=$res_query_meta->fields['vph'];
$vacuna_doble_viralrrhh=$res_query_meta->fields['doble_viral'];
$vacuna_fiebre_amarillarrhh=$res_query_meta->fields['fiebre_amarilla'];
$ceb_ceroacincorrhh=$res_query_meta->fields['ceb_ceroacinco'];
$ceb_seisanueverrhh=$res_query_meta->fields['ceb_seisanueve'];
$ceb_diezadiecinueverrhh=$res_query_meta->fields['ceb_diezadiecinueve'];
$ceb_veinteasesentaycuatrorrhh=$res_query_meta->fields['ceb_veinteasesentaycuatro'];

$sql_sitam="select cantidad from nacer.sitam where cuie='$cuie'";
$res_sitam=sql($sql_sitam,"Error al traer los datos del sitam") or fin_pagina();
$paps=$res_sitam->fields['cantidad'];
  

}	    

ob_start(); 
?> 
<page format=A4 backtop="5mm" backbottom="5mm" backleft="2mm" backright="2mm"  style="font-size: 8pt">

<!-- <page_header> --> 
<table class="page_header" width=210 height=297 cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor="#CFE8DD" class="bordes">
	<!-- width=210 height=297 -->
	<tr>
   <td align="center" border=1 bordercolor=#E0E0E0 bgcolor="#F5BCA9">
    	<?$hoy = date("d/m/Y H:i"); ?>
    	<font size=+2><b>Fecha y Hora de Corte del Informe: <?echo $hoy?> </b></font>        
    </td>
    </tr>
 	</table>

<table class="page_header" width=210 height=297 cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor="#CFE8DD" class="bordes">
	<!-- width=210 height=297 -->
	<tr>
   <td align="center" border=1 bordercolor=#E0E0E0 bgcolor="#CFE8DD">
    	<font size=+2><b>Efector: <?echo $cuie.". Periodo Desde: ".fecha($fecha_desde)." Hasta: ".fecha($fecha_hasta)?> </b></font>        
    </td>
    </tr>
 	</table>
	<!-- </page_header> -->

<table border=2 width=210 height=297 align="center" class="bordes" >
     <tr>
      <td >
       <b>Descripcion del Efector</b>
      </td>
      </tr>
       <tr>
       <td>
      
        <table align="center">
         <tr>
         <td align="right">
				<b>Nombre:</b>
			</td>
			<td align="left" border=1 bordercolor=#E0E0E0 bgcolor="#CFE8DD">		 
             <?echo $nombre?>"
            </td>
           <td align="right">
				<b>Codigo Postal:</b>
			</td>
			<td align="left" border=1 bordercolor=#E0E0E0 bgcolor="#CFE8DD">		 	 
              <?echo $cod_pos?>
            </td>
           </tr>
            <tr>
         <td align="right">
				<b>Domicilio:</b>
			</td>
			<td align="left" bordercolor=#E0E0E0 bgcolor="#CFE8DD">	 
              <?echo $domicilio?>
            </td>
         <td align="right">
				<b>Cuidad:</b>
			</td>
			<td align="left" bordercolor=#E0E0E0 bgcolor="#CFE8DD">	 
              <?echo $cuidad?>
            </td>
         </tr> 
          <tr>
         <td align="right">
				<b>Departamento:</b>
			</td>
			<td align="left" bordercolor=#E0E0E0 bgcolor="#CFE8DD">			 
              <?echo $departamento?>
            </td>
         <td align="right">
				<b>Referente:</b>
			</td>
			<td align="left" bordercolor=#E0E0E0 bgcolor="#CFE8DD">
              <?echo $referente?>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Localidad:</b>
			</td>
			<td align="left" bordercolor=#E0E0E0 bgcolor="#CFE8DD">			 
             <?echo $localidad?>
            </td>
          <td align="right">
				<b>Telefono:</b>
			</td>
			<td align="left" bordercolor=#E0E0E0 bgcolor="#CFE8DD">		 
              <?echo $tel?>
            </td>
         </tr>
            </table>
            
         </td>
         </tr>
  
  </table>
<?//consultas auxiliares




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
		    
		   
		    
		    //sql ninios_new
		    
		    $sql_ninio_pers="select count (num_doc) as total
								from (
									select distinct num_doc 
										from trazadoras.nino_new 
										where 
											cuie = '$cuie' and 
											(fecha_control - fecha_nac >= 0 and fecha_control - fecha_nac < 365) and
											(fecha_control between '$fecha_desde' and '$fecha_hasta'))as cons1";
		    $res_sql_ninio_pers= sql($sql_ninio_pers) or die;
		    $ninios_new_pers_1=$res_sql_ninio_pers->fields['total'];
		    
		    
		    
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
		    
		    //vacunas
		    
		    $sql_vac="select nom_vacum,sum (cantidad) as cant from (
						SELECT  nacer.efe_conv.cuie,
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
					where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta')and (nacer.efe_conv.cuie='$cuie') and (trazadoras.vacunas.eliminada=0)
				GROUP BY
				nacer.efe_conv.cuie,
				nacer.efe_conv.nombre ,
				trazadoras.vac_apli.nombre,
				trazadoras.dosis_apli.nombre) as tabla
				group by nom_vacum";
			$res_vacunas=sql($sql_vac) or die;
			while (!$res_vacunas->EOF){
			switch ($res_vacunas->fields['nom_vacum']){
				case "Hepatitis B" : $efe_hep_b=$res_vacunas->fields['cant'];break;
				case "Neumo 13" : $efe_neumococo=$res_vacunas->fields['cant'];break;
				case "Pentavalente ": $efe_pentavalente=$res_vacunas->fields['cant'];break;
				case "Cuadruple" : $efe_cuadruple=$res_vacunas->fields['cant'];break;
				case "Sabin" : $efe_sabin=$res_vacunas->fields['cant'];break;
				case "Triple Viral" : $efe_triple_viral=$res_vacunas->fields['cant'];break;
				case "Antigripal 0.25 para niños" : $efe_gripe_1=$res_vacunas->fields['cant'];break;
				case "Antogripal 0.5 Adulto o Agripal Adulto" : $efe_gripe_2=$res_vacunas->fields['cant'];break;
				case "Hepatitis A" : $efe_hep_a=$res_vacunas->fields['cant'];break;
				case "Triple Bacteriana Celular" : $efe_triple_bacteriana_celular=$res_vacunas->fields['cant'];break;
				case "Triple Bacteriana Acelular": $efe_triple_bacteriana_acelular=$res_vacunas->fields['cant'];break;
				case "Doble Bacteriana" : $efe_doble_bacteriana=$res_vacunas->fields['cant'];break;
				case "HPV" : $efe_hpv=$res_vacunas->fields['cant'];break;
				case "Doble Viral" : $efe_doble_viral=$res_vacunas->fields['cant'];break;
				case "Fiebre Hemorrágica Argentina" : $efe_fiebre_amarilla=$res_vacunas->fields['cant'];break;
				default: break;
				}
			$res_vacunas->MoveNext();
			}	//fin vacunas
		    
//fin consultas auxiliares?>  
<table><tr><td>&nbsp;</td></tr></table>
<table><tr><td>&nbsp;</td></tr></table>

<table border=2 width=210 height=297 align="center" class="bordes" >		 
		 <tr align="center" id="sub_tabla">
		 	<td colspan=10>	
		 	<font size=3 >Detalle sobre cumplimientos de metas <BR> </font>
		 	</td>
		 </tr>

		 
		<tr>
		<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
		Cobertura Efectiva Basica entre 0 y 5 años: <b><?=$ceb_a?> / </b> <font size=2 color= red> <b>meta x RRHH: <?=$ceb_ceroacincorrhh?> </b></font>
		</td>   
		<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
		Cobertura Efectiva Basica entre 6 y 9 años: <b><?=$ceb_b?> / </b> <font size=2 color= red> <b>meta x RRHH: <?=$ceb_seisanueverrhh?> </b></font>
		</td>
		</tr>
			
		<tr>
		<td align="center" border=1 bordercolor=#2C1701>
		<? echo "<a target='_blank'><img src='ceb_ceroacinco.png'  border=0 align=top></a>\n";?>
		</td>
		<td align="center" border=1 bordercolor=#2C1701>
		<?echo "<a target='_blank'><img src='ceb_seisanueve.png'  border=0 align=top></a>\n";?>
		</td>
		</tr>	
		<tr>
		<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
		Cobertura Efectiva Basica entre 10 y 19 años: <b><?=$ceb_c?> / </b> <font size=2 color= red> <b>meta x RRHH: <?=$ceb_diezadiecinueverrhh?> </b></font>
		</td>   
		<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
		Cobertura Efectiva Basica entre 20 y 64 años: <b><?=$ceb_d?> / </b> <font size=2 color= red> <b>meta x RRHH: <?=$ceb_veinteasesentaycuatrorrhh?> </b></font>
		</td>
		</tr> 	
			
		<tr>
		<td align="center" border=1 bordercolor=#2C1701>
		<? echo "<a target='_blank'><img src='ceb_diezadiecinueve.png'  border=0 align=top></a>\n";?>
		</td>
		<td align="center" border=1 bordercolor=#2C1701>
		<?echo "<a target='_blank'><img src='ceb_veinteasesentaycuatro.png'  border=0 align=top></a>\n";?>
		</td>
		</tr>

		<tr>
		<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					Total de Controles de Niños menor de 1 año segun periodo (por fecha de control): <b><?=($ninios_new_1)?$ninios_new_1:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$cns_menor_1_año?>  / </b></font>--> <font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($cns_menor_1_añorrhh/2)?> </b></font>
		</td>   
		<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<? $ninios_total=$ninios_new_2+$ninios_new_3?>
					Total de Controles de Niños de 1 a 9 años segun periodo (por fecha de control): <b><?=($ninios_total)?$ninios_total:0?> / </b> <!--<font size=2 color= red> <b>Meta anual: <?=$cns_entre_1_y_6?> / </b></font>--> <font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($cns_entre_1_y_6rrhh/2)?> </b></font>
		</td>	   	
		</tr> 
		
		<tr>
		<td align="center" border=1 bordercolor=#2C1701>
		<? echo "<a target='_blank'><img src='cns_menor_1_año.png'  border=0 align=top></a>\n";?>
		</td>
		<td align="center" border=1 bordercolor=#2C1701>
		<?echo "<a target='_blank'><img src='ninios_total.png'  border=0 align=top></a>\n";?>
		</td>
		</tr>
		
		</table>
		</page>
		
		<page format=A4 backtop="5mm" backbottom="5mm" backleft="2mm" backright="2mm"  style="font-size: 8pt">
		  	<table border=2 width=210 height=297 align="center" class="bordes" >		 
			<tr align="center" id="sub_tabla">
		 		<td colspan=10>	
		 		<font size=3 >Detalle sobre cumplimientos de metas <BR> </font>
		 		</td>
			 </tr>
		
		
		<tr>
		<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_elegir?>" <?=atrib_tr7()?>>
		Meta de presentacion de facturacion: <b><?=($cant_fact)?$cant_fact:0?> / </b> <font size=2 color= red> <b> Meta Semestral: 5 </b></font>
		</td> 
		<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
		Total de Embarazadas antes de las 12 semanas: <b><?=($embarazadas_12_pers)?$embarazadas_12_pers:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$captacion_temprana?> / </b></font>--> <font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($captacion_tempranarrhh/2)?> </b></font>
		</td>
		</tr>
		     
		<tr>
		<td align="center" border=1 bordercolor=#2C1701>
		<?echo "<a href='$link_l' target='_blank'><img src='cantidad_facturas.png'  border=0 align=top></a>\n";?>
		</td>
		<td align="center" border=1 bordercolor=#2C1701>
		<?echo "<a href='$link_l' target='_blank'><img src='embarazadas_12_pers.png'  border=0 align=top></a>\n";?>
		</td>
		</tr>
		

		<tr>
		<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
		Total de Controles de Embarazo segun periodo (por fecha de control): <b><?=($embarazadas)?$embarazadas:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$promedio_controles_x_emb?> / </b></font> --><font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($promedio_controles_x_embrrhh/2)?> </b></font>
		</td>	
		<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
		Total de Adolescentes de 10 a 19 años segun periodo (por fecha de control): <b><?=($adol_new_pers_3)?$adol_new_pers_3:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$adolecentes?> / </b></font>--> <font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($adolecentesrrhh/2)?> </b></font>
		</td>  	
		</tr>
		
		<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='embarazadas.png'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='adol_new_pers_3.png'  border=0 align=top></a>\n";?>
			</td>
		</tr>
		 	
		<tr>
			<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_elegir?>" <?=atrib_tr7()?>>
			Total de Inscriptos que Marca Cuidado Sexual y Reproductivo: <b><?=($cuidado_sexual)?$cuidado_sexual:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$mujeres_edad_fertil?> / </b></font>--> <font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($mujeres_edad_fertilrrhh/2)?> </b></font>
			</td>            
            <td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_elegir?>" <?=atrib_tr7()?>>
			Total de Controles que Marca Diabetico: <b><?=($dia)?$dia:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$enfermedades_cronicas_DBT?> / </b></font>--> <font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($enfermedades_cronicas_DBTrrhh/2)?> </b></font>
			</td>          
            </tr>
            
			<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='cuidado_sexual.png'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='dia.png'  border=0 align=top></a>\n";?>
			</td>
			</tr>
           
            <tr>
			<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_elegir?>" <?=atrib_tr7()?>>
			Total de Controles que Marca Hipertenso: <b><?=($hip)?$hip:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$enfermedades_cronicas_HTA?> / </b></font> --><font size=2 color= red> <b>Meta x RRHH Semestral: <?=round ($enfermedades_cronicas_HTArrhh/2)?> </b></font>
			</td>
			<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			Total de Vacunas Doble Viral: <b><?=($efe_doble_viral)?$efe_doble_viral:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_doble_viral?> / </b></font>--> <font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($vacuna_doble_viralrrhh/2)?> </b></font>
			</td>           
            </tr>
            
			<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='hip.png'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='vacuna_doble_viral.png'  border=0 align=top></a>\n";?>
			</td>
			</tr> 
			
			
		</table>
		</page>
		
		<page format=A4 backtop="5mm" backbottom="5mm" backleft="2mm" backright="2mm"  style="font-size: 8pt">
		  	<table border=2 width=210 height=297 align="center" class="bordes" >		 
			<tr align="center" id="sub_tabla">
		 		<td colspan=10>	
		 		<font size=3 >Detalle sobre cumplimientos de metas <BR> </font>
		 		</td>
			 </tr>
			
			<tr>
			<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			Total de Vacunas Hepatitis B: <b><?=($efe_hep_b)?$efe_hep_b:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_hep_b?> / </b></font>--> <font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($vacuna_hep_brrhh/2)?> </b></font>
			</td> 
			<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			Total de Vacunas Neumococo: <b><?=($efe_neumococo)?$efe_neumococo:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_neumococo?> / </b></font>--> <font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($vacuna_neumococorrhh/2)?> </b></font>
			</td>               
            </tr>
			
			<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='vacuna_hep_b.png'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='vacuna_neumococo.png'  border=0 align=top></a>\n";?>
			</td>
			</tr>
				
				
			<tr>
			<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			Total de Vacunas Pentavalentes: <b><?=($efe_pentavalente)?$efe_pentavalente:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_pentavalente?> / </b></font> --><font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($vacuna_pentavalenterrhh/2)?> </b></font>
			</td> 
			<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			Total de Vacunas cuadruples: <b><?=($efe_cuadruple)?$efe_cuadruple:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_cuadruple?> / </b></font>--> <font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($vacuna_cuadruplerrhh/2)?> </b></font>
			</td>             
            </tr>
			
			<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='vacuna_pentavalente.png'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='vacuna_cuadruple.png'  border=0 align=top></a>\n";?>
			</td>
			</tr>
			
			<tr>
			<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			Total de Vacunas Sabin: <b><?=($efe_sabin)?$efe_sabin:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_sabin?> / </b></font>--> <font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($vacuna_sabinrrhh/2)?> </b></font>
			</td> 
			<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			Total de Vacunas Triple Viral: <b><?=($efe_triple_viral)?$efe_triple_viral:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_triple_viral?> / </b></font>--> <font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($vacuna_triple_viralrrhh/2)?> </b></font>
			</td>             
            </tr>
			
			<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='vacuna_sabin.png'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='vacuna_triple_viral.png'  border=0 align=top></a>\n";?>
			</td>
			</tr>
			
			<tr>
			<? $efe_gripe=$efe_gripe_1+$efe_gripe_2?>
			<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			Total de Vacunas Anti-Gripales: <b><?=($efe_gripe)?$efe_gripe:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_gripe?> / </b></font>--> <font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($vacuna_griperrhh/2)?> </b></font>
			</td> 
			<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			Total de Vacunas Hepatitis A: <b><?=($efe_hep_a)?$efe_hep_a:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_hep_a?> / </b></font> --><font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($vacuna_hep_arrhh/2)?> </b></font>
			</td>            
            </tr>
			
			<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='vacuna_gripe.png'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='vacuna_hep_a.png'  border=0 align=top></a>\n";?>
			</td>
			</tr>
			
			</table>
			</page>
			
			<page format=A4 backtop="5mm" backbottom="5mm" backleft="2mm" backright="2mm"  style="font-size: 8pt">
			<table border=2 width=210 height=297 align="center" class="bordes" >		 
				 <tr align="center" id="sub_tabla">
		 		<td colspan=10>	
		 		<font size=3 >Detalle sobre cumplimientos de metas <BR> </font>
		 		</td>
			 </tr>
			
			<tr>
			<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			Total de Vacunas Tri.Bac.Celular: <b><?=($efe_triple_bacteriana_celular)?$efe_triple_bacteriana_celular:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_triple_bacteriana_celular?> / </b></font>--> <font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($vacuna_triple_bacteriana_celularrrhh/2)?> </b></font>
			</td> 
			<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			Total de Vacunas Tri.Bac.Acelular: <b><?=($efe_triple_bacteriana_acelular)?$efe_triple_bacteriana_acelular:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_triple_bacteriana_acelular?> / </b></font>--> <font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($vacuna_triple_bacteriana_acelularrrhh/2)?> </b></font>
			</td>            
            </tr>
			
			<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='vacuna_triple_bacteriana_celular.png'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='vacuna_triple_bacteriana_acelular.png'  border=0 align=top></a>\n";?>
			</td>
			</tr>
			
			<tr>
			<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			Total de Vacunas Doble Bacteriana: <b><?=($efe_doble_bacteriana)?$efe_doble_bacteriana:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_doble_bacteriana?> / </b></font>--> <font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($vacuna_doble_bacterianarrhh/2)?> </b></font>
			</td> 
			<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			Total de Vacunas VPH: <b><?=($efe_hpv)?$efe_hpv:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_vph?> / </b></font> --><font size=2 color= red> <b>Meta x RRHH Semestral: <?=round($vacuna_vphrrhh/2)?> </b></font>
			</td>            
            </tr>
			
			<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='vacuna_doble_bacteriana.png'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<?echo "<a href='$link_l' target='_blank'><img src='vacuna_vph.png'  border=0 align=top></a>\n";?>
			</td>
			</tr>
			
</table>

</page>
<?$content = ob_get_clean(); 
   $html2pdf = new HTML2PDF('P','A4','es',array(mL, mT, mR, mB));
   $html2pdf->WriteHTML($content);
   $file="cumplimiento_".$cuie."_".$fecha_desde."_".$fecha_hasta;
   $html2pdf->Output("$file.pdf",'D');?> 
   		    
