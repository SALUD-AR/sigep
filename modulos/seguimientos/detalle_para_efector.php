<?

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

$cuie=$_ses_user['login'];
$sql_cuie="select * from nacer.efe_conv where cuie='$cuie'";
$res_cuie= sql($sql_cuie, "Error al traer el Efector") or fin_pagina();
$id_efe_conv=$res_cuie->fields['id_efe_conv'];

function extrae_anio($fecha) {
        list($d,$m,$a) = explode("/",$fecha);
        //$a=$a+2000;
        return $a;
		}

if ($_POST['muestra']=="Muestra"){	
	
	$fecha_desde=fecha_db($_POST['fecha_desde']);
	$fecha_hasta=fecha_db($_POST['fecha_hasta']);
	
	/*$anio=extrae_anio($_POST['fecha_desde']);
	$fecha_desde_anual="$anio"."-01-01";
	$fecha_hasta_anual="$anio"."-12-31";*/
	  		
			
	//CEB
$sql_ceb="select grupo, count (grupo) as cantidad from (
select afidni,grupopoblacional as grupo from (
select distinct cuie,id_smiafiliados from (
select * from (
select id_comprobante from facturacion.prestacion 
where (id_nomenclador=1752 and diagnostico='A97') or
(id_nomenclador=1753 and diagnostico='A97') or
(id_nomenclador=1725 and diagnostico='A97') or
(id_nomenclador=1661 and diagnostico='A97') or
(id_nomenclador=1654 and diagnostico='W78') or
(id_nomenclador=1751 and diagnostico='W78') or
(id_nomenclador=1768 and diagnostico='A97') or
(id_nomenclador=2044 and diagnostico='A97') or
(id_nomenclador=1814 and diagnostico='A97') or
(id_nomenclador=1668 and (diagnostico='T79' or diagnostico='T82')) or
(id_nomenclador=1694 and (diagnostico='T79' or diagnostico='T82')) or
(id_nomenclador=1669 and diagnostico='T83') or
(id_nomenclador=1696 and diagnostico='T83') or
(id_nomenclador=2012 and diagnostico='R96') or
(id_nomenclador=1704 and diagnostico='R96') or
(id_nomenclador=2048 and diagnostico='B80') or
(id_nomenclador=2045 and diagnostico='B80') or
(id_nomenclador=1701 and (diagnostico='P20' or diagnostico='P23' or diagnostico='P24')) or
(id_nomenclador=1710 and (diagnostico='P20' or diagnostico='P23' or diagnostico='P24')) or
(id_nomenclador=2062 and diagnostico='P98') or
(id_nomenclador=1709 and diagnostico='B72') or
(id_nomenclador=1706 and diagnostico='B72') or
(id_nomenclador=1687 and diagnostico='B73') or
(id_nomenclador=1698 and diagnostico='B73') or
(id_nomenclador=1815 and diagnostico='A98') or
(id_nomenclador=1816 and diagnostico='A98') or
(id_nomenclador=1817 and diagnostico='A98') or
(id_nomenclador=2041 and diagnostico='A98') or
(id_nomenclador=1818 and diagnostico='A98') or
(id_nomenclador=1819 and diagnostico='A98') or
(id_nomenclador=1673 and diagnostico='A98') or
(id_nomenclador=1703 and diagnostico='A98') or
(id_nomenclador=1672 and diagnostico='A98') or
(id_nomenclador=2008 and diagnostico='A97') or
(id_nomenclador=2058 and diagnostico='A97') or
(id_nomenclador=2022 and (diagnostico='P18' or diagnostico='W78')) or
(id_nomenclador=1760 and (diagnostico='A98' or diagnostico='X86' or diagnostico='X75')) or
(id_nomenclador=1770 and diagnostico='A98') or
(id_nomenclador=1654 and diagnostico='W78') or
(id_nomenclador=1751 and diagnostico='W78') 
) as prestaciones
inner join facturacion.comprobante using (id_comprobante)

) as comprobantes
where cuie ='$cuie' and fecha_comprobante between '2013-12-31' and '2014-12-31'

) as afiliados 
inner join nacer.smiafiliados using (id_smiafiliados)

) as grupopoblacional 
group by grupo
order by grupo";

$sql_res_ceb=sql($sql_ceb,"No se pudo calcular el ceb del efector");
$sql_res_ceb->movefirst();
while (!$sql_res_ceb->EOF) {
$grupo=$sql_res_ceb->fields['grupo'];
switch ($grupo) {
case 'A' : $ceb_a=($sql_res_ceb->fields['cantidad'])?$sql_res_ceb->fields['cantidad']:0;break;
case 'B' : $ceb_b=($sql_res_ceb->fields['cantidad'])?$sql_res_ceb->fields['cantidad']:0;break;
case 'C' : $ceb_c=($sql_res_ceb->fields['cantidad'])?$sql_res_ceb->fields['cantidad']:0;break;
case 'D' : $ceb_d=($sql_res_ceb->fields['cantidad'])?$sql_res_ceb->fields['cantidad']:0;break;
default : break;
	}
$sql_res_ceb->movenext();
}
	
			
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
			
			
			//embarazadas
    		
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
		    
		    //ninio menores de 1 anio
		    $sql_ninio="select count (id_trz4) as total from trazadorassps.trazadora_4 
						where 
							cuie = '$cuie' and 
							(fecha_control - fecha_nac >= 0 and fecha_control - fecha_nac < 365) and
							(fecha_control between '$fecha_desde' and '$fecha_hasta')";
		    $res_sql_ninio_trzsps= sql($sql_ninio) or die;
		    
		    $sql_ninio="select count (num_doc) as total from trazadoras.nino_new 
						where 
							cuie = '$cuie' and 
							(fecha_control - fecha_nac >= 0 and fecha_control - fecha_nac < 365) and
							(fecha_control between '$fecha_desde' and '$fecha_hasta')";
		    $res_sql_ninio= sql($sql_ninio) or die;
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
		    $ninios_new_pers_1=$res_sql_ninio_pers->fields['total'];
		    
		    //calculo de los controles de niños entre 1 y 9 años
			$cons_1_a_9="select count (*) as total from trazadoras.nino_new 
						where 
							cuie = '$cuie' and 
							(fecha_control - fecha_nac >= 366 and fecha_control - fecha_nac < 3285) and
							(fecha_control between '2013-12-31' and '2014-12-31')";
			$res_cons_1_a_9=sql($cons_1_a_9) or die;
			$ninios_1_a_9=$res_cons_1_a_9->fields['total'];

			//consulta no utilizada-REVISAR

			$sql_ninio="select count (num_doc) as total from trazadoras.nino_new 
						where 
							cuie = '$cuie' and 
							(fecha_control - fecha_nac >= 366 and fecha_control - fecha_nac < 730) and
							(fecha_control between '2013-12-31' and '2014-12-31')";
		    $res_sql_ninio= sql($sql_ninio) or die;
		    $ninios_new_2=$res_sql_ninio->fields['total'];
			//--------------------------------
		    
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
		    
		    //consulta no utilizada-REVISAR
			$sql_ninio="select count (num_doc) as total from trazadoras.nino_new 
						where 
							cuie = '$cuie' and 
							(fecha_control - fecha_nac >= 731 and fecha_control - fecha_nac < 2190) and
							(fecha_control between '2013-12-31' and '2014-12-31')";
		    $res_sql_ninio= sql($sql_ninio) or die;
		    $ninios_new_3=$res_sql_ninio->fields['total'];
			//-------------------------------
		    
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
		    
			//adolescentes
			$sql_adol="select count (num_doc) as total from trazadoras.nino_new 
						where 
							cuie = '$cuie' and 
							(fecha_control - fecha_nac >= 3651 and fecha_control - fecha_nac < 7299) and
							(fecha_control between '2013-12-31' and '2014-12-31')";
		    $res_sql_adol= sql($sql_adol) or die;
		    
		    $sql_adol="select count (id_smiafiliados) as total from trazadorassps.trazadora_10
						where 
							cuie = '$cuie' and 
							(fecha_control - fecha_nac >= 3651 and fecha_control - fecha_nac < 7299) and
							(fecha_control between '2013-12-31' and '2014-12-31')";
		    $res_sql_adol_trz10= sql($sql_adol) or die;
		    
		    $adol_new_3=$res_sql_adol->fields['total']+$res_sql_adol_trz10->fields['total'];
		    
		    $sql_adol_pers="select count (num_doc) as total
								from (
									select distinct num_doc 
										from trazadoras.nino_new 
										where 
											cuie = '$cuie' and 
											(fecha_control - fecha_nac >= 3651 and fecha_control - fecha_nac < 7299) and
											(fecha_control between '2013-12-31' and '2014-12-31'))as cons1";
		    $res_sql_adol_pers= sql($sql_adol_pers) or die;
			
			$sql_adol_pers="select count (id_smiafiliados) as total
								from (
									select distinct id_smiafiliados,id_beneficiarios
										from trazadorassps.trazadora_10
										where 
											cuie = '$cuie' and 
											(fecha_control - fecha_nac >= 3651 and fecha_control - fecha_nac < 7299) and
											(fecha_control between '2013-12-31' and '2014-12-31'))as cons1";
		    $res_sql_adol_pers_trz10= sql($sql_adol_pers) or die;
			
		    $adol_new_pers_3=$res_sql_adol_pers->fields['total']+$res_sql_adol_pers_trz10->fields['total'];
			// fin de adolescentes	 
		    
		    //cuidado sexual
		    
			$sql_cuidado_sexual="select count (*) as total
			from fichero.fichero 
			left join nacer.smiafiliados using (id_smiafiliados)
			left join leche.beneficiarios using (id_beneficiarios)
			where cuie = '$cuie' and fecha_control between '2013-12-31' and '2014-12-31' and salud_rep = 'SI'";
			$result_ssr1=sql($sql_cuidado_sexual) or fin_pagina();
			$cuidado_sexual=$result_ssr1->fields['total'];
		 
			//dia e hip
			$sql_dia="select count(*) as cantidad
			from fichero.fichero 
			left join nacer.smiafiliados using (id_smiafiliados)
			left join leche.beneficiarios using (id_beneficiarios)
			where cuie = '$cuie' and fecha_control between '2013-12-31' and '2014-12-31' and diabetico = 'SI'";
			$result_dia=sql($sql_dia) or fin_pagina();


			$sql_dia2="select count (*) as cantidad from trazadoras.clasificacion_remediar2
			where cuie = '$cuie' and fecha_control between '2013-12-31' and '2014-12-31' and diabetico = 'SI'";
			$result_dia2=sql($sql_dia2) or fin_pagina();
			
			$dia=$result_dia->fields['cantidad'] + $result_dia2->fields['cantidad'];
				
			/*$sql_hip="select count (id_fichero) as total 
										from fichero.fichero 
										where cuie = '$cuie' and fecha_control between '2013-12-31' and '2014-12-31' and hipertenso = 'SI'";*/
										
			$sql_hip="select count (*) as total
			from fichero.fichero 
			left join nacer.smiafiliados using (id_smiafiliados)
			left join leche.beneficiarios using (id_beneficiarios)
			where cuie = '$cuie' and fecha_control between '2013-12-31' and '2014-12-31' and hipertenso = 'SI'";
				$res_hip= sql($sql_hip) or die;
				$hip=$res_hip->fields['total'];
			
			$sql_hip="select count (num_doc) as  total from trazadoras.clasificacion_remediar2 
			where cuie='$cuie' and fecha_control between '2013-12-31' and '2014-12-31' and hipertenso='SI'";
			$res_hip=sql($sql_hip) or die;
			$hip=$hip+$res_hip->fields['total'];

			//vacunas
			$sql_vac="select id_vac_apli,nom_vacum,sum (cantidad) as cant from (
						SELECT  nacer.efe_conv.cuie,
							nacer.efe_conv.nombre as nom_efector,
							trazadoras.vac_apli.id_vac_apli,
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
				trazadoras.vac_apli.id_vac_apli,
				trazadoras.vac_apli.nombre,
				trazadoras.dosis_apli.nombre) as tabla
				group by id_vac_apli,nom_vacum";
			$res_vacunas=sql($sql_vac) or die;
			while (!$res_vacunas->EOF){
			switch ($res_vacunas->fields['id_vac_apli']){
				case 2 : $efe_hep_b=$res_vacunas->fields['cant'];break;
				case 17 : $efe_neumococo=$res_vacunas->fields['cant'];break;
				case 3: $efe_pentavalente=$res_vacunas->fields['cant'];break;
				case 4 : $efe_cuadruple=$res_vacunas->fields['cant'];break;
				case 5 : $efe_sabin=$res_vacunas->fields['cant'];break;
				case 6 : $efe_triple_viral=$res_vacunas->fields['cant'];break;
				case 18 : $efe_gripe_1=$res_vacunas->fields['cant'];break;
				case 19 : $efe_gripe_2=$res_vacunas->fields['cant'];break;
				case 7 : $efe_hep_a=$res_vacunas->fields['cant'];break;
				case 8 : $efe_triple_bacteriana_celular=$res_vacunas->fields['cant'];break;
				case 9: $efe_triple_bacteriana_acelular=$res_vacunas->fields['cant'];break;
				case 10 : $efe_doble_bacteriana=$res_vacunas->fields['cant'];break;
				case 14: $efe_hpv=$res_vacunas->fields['cant'];break;
				case 11 : $efe_doble_viral=$res_vacunas->fields['cant'];break;
				case 13 : $efe_fiebre_amarilla=$res_vacunas->fields['cant'];break;
				default: break;
				}
			$res_vacunas->MoveNext();
			}	
				
				
//llenar con las consultas
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

//devolucion de metas anuales
$query_meta="select *  from nacer.metas where cuie='$cuie'";
$res_query_meta=sql($query_meta, "Error al traer el Efector") or fin_pagina();
$pap_sitam=$res_query_meta->fields['pap_sitam'];
$cant_embarazadas=$res_query_meta->fields['cant_embarazadas'];
$captacion_temprana=$res_query_meta->fields['captacion_temprana'];
$promedio_controles_x_emb=$res_query_meta->fields['promedio_controles_x_emb'];
$mujeres_edad_fertil=$res_query_meta->fields['mujeres_edad_fertil'];
$cns_menor_1_año=$res_query_meta->fields['cns_menor_1_anio'];
$cns_entre_1_y_9=$res_query_meta->fields['cns_entre_1_y_9'];
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
/*$query_meta="select *  from nacer.metasrrhh where cuie='$cuie'";
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
$ceb_veinteasesentaycuatrorrhh=$res_query_meta->fields['ceb_veinteasesentaycuatro'];*/

$sql_sitam="select cantidad from nacer.sitam where cuie='$cuie'";
$res_sitam=sql($sql_sitam,"Error al traer los datos del sitam") or fin_pagina();
$paps=$res_sitam->fields['cantidad'];
  

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

<form name='form1' action='detalle_para_efector.php' method='POST'>
<input type="hidden" value="<?=$id_efe_conv?>" name="id_efe_conv">
<input type="hidden" value="<?=$cuie?>" name="cuie">

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
	    
	    &nbsp;&nbsp;&nbsp;	    
        <!--<?if ($_POST['muestra']){
         	
        $link=encode_link("efec_cumplimiento_pdf.php",array("id_efe_conv"=>$id_efe_conv,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta,"fecha_desde_anual"=>$fecha_desde_anual,"fecha_hasta_anual"=>$fecha_hasta_anual));?>
        <img src="../../imagenes/pdf_logo.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">
        <?}?>-->
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
		 	
</table>

<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
		 <tr align="center" id="sub_tabla">
		 	<td colspan=10>	
		 	<font size=3 >Detalle sobre cumplimientos de metas <BR> </font>
		 	</td>
		 </tr>

		<tr align="center" id="sub_tabla">
		 	<td colspan=10>	
		 	<font size=4 color="red" ><b>Nota Importante: las metas anuales estan fijadas con periodo desde 2013-12-31 al 2014-12-31 <BR></b> </font>
		 	</td>
		 </tr>
		    <tr>
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<?$porcentaje=($ceb_a/$ceb_ceroacinco)*100;
					  $porcentaje=number_format ($porcentaje,2,',','.')?>
					Cobertura Efectiva Basica entre 0 y 5 años: <b><?=($ceb_a)?$ceb_a:0?> / </b> <font size=2 color= red> <b>Meta Anual: <?=$ceb_ceroacinco?> </b></font>
					<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td>   
			   	<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<?$porcentaje=($ceb_b/$ceb_seisanueve)*100;
					  $porcentaje=number_format ($porcentaje,2,',','.')?>					
					Cobertura Efectiva Basica entre 6 y 9 años: <b><?=($ceb_b)?$ceb_b:0?> / </b> <font size=2 color= red> <b>Meta Anual: <?=$ceb_seisanueve?> </b></font>
					<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td>
			</tr> 
			
			<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$ceb_a,"metarrhh_s"=>$ceb_ceroacinco,"tamaño"=>"small","nombre"=>"ceb_ceroacinco","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$ceb_b,"metarrhh_s"=>$ceb_seisanueve,"tamaño"=>"small","nombre"=>"ceb_seisanueve","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			</tr>
			
			<tr>
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<?$porcentaje=($ceb_c/$ceb_diezadiecinueve)*100;
					  $porcentaje=number_format ($porcentaje,2,',','.')?>
					Cobertura Efectiva Basica entre 10 y 19 años: <b><?=($ceb_c)?$ceb_c:0?> / </b> <font size=2 color= red> <b>Meta Anual: <?=$ceb_diezadiecinueve?> </b></font>
					<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				  </td>   
			   	<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<?$porcentaje=($ceb_d/$ceb_veinteasesentaycuatro)*100;
					  $porcentaje=number_format ($porcentaje,2,',','.')?>
					Cobertura Efectiva Basica entre 20 y 64 años: <b><?=($ceb_d)?$ceb_d:0?> / </b> <font size=2 color= red> <b>Meta Anual: <?=$ceb_veinteasesentaycuatro?> </b></font>
					<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td>
			</tr> 
			
			<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$ceb_c,"metarrhh_s"=>$ceb_diezadiecinueve,"tamaño"=>"small","nombre"=>"ceb_diezadiecinueve","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$ceb_d,"metarrhh_s"=>$ceb_veinteasesentaycuatro,"tamaño"=>"small","nombre"=>"ceb_veinteasesentaycuatro","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			</tr>
			
		<tr>
		<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			<?$porcentaje=($ninios_new_1/$cns_menor_1_año)*100;
			$porcentaje=number_format ($porcentaje,2,',','.')?>
			Total de Controles de Ninos menor de 1 año segun periodo (por fecha de control): <b><?=($ninios_new_1)?$ninios_new_1:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$cns_menor_1_año?>  / </b></font>--> <font size=2 color= red> <b>Meta Semestral x RRHH: <?=$cns_menor_1_año?> </b></font>
			<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
		</td>   
		<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			<? //$ninios_total=$ninios_new_2+$ninios_new_3;
			$porcentaje=($ninios_1_a_9/$cns_entre_1_y_9)*100;
			$porcentaje=number_format ($porcentaje,2,',','.')?>
			Total de Controles de Niños de 1 a 9 años segun periodo (por fecha de control): <b><?=($ninios_1_a_9)?$ninios_1_a_9:0?> / </b> <!--<font size=2 color= red> <b>Meta anual: <?=$cns_entre_1_y_9?> / </b></font>--> <font size=2 color= red> <b>Meta Anual x RRHH: <?=$cns_entre_1_y_9?> </b></font>
			<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
		</td>	   	
		</tr> 
		<tr>
			
		<td align="center" border=1 bordercolor=#2C1701>
		<? $link_s=encode_link("metas_grafico.php",array("dato"=>$ninios_new_1,"metarrhh_s"=>$cns_menor_1_año,"tamaño"=>"small","nombre"=>"cns_menor_1_año","cuie"=>$cuie));
		echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
		</td>
		<td align="center" border=1 bordercolor=#2C1701>
		<? $link_s=encode_link("metas_grafico.php",array("dato"=>$ninios_1_a_9,"metarrhh_s"=>$cns_entre_1_y_9,"tamaño"=>"small","nombre"=>"ninios_total","cuie"=>$cuie));
		echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
		</td>
		</tr>		
			
		<tr>
		<?$ref = encode_link("detalle_factura.php",array("cuie"=>$cuie,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
					$onclick_elegir="location.href='$ref' target='_blank'";
					$onclick_elegir="window.open('$ref' , '_blank');";?>
		<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_elegir?>" <?=atrib_tr7()?>>
			<?$porcentaje=($cant_fact/5)*100;
			$porcentaje=number_format ($porcentaje,2,',','.')?>
			Meta de presentacion de facturacion: <b><?=($cant_fact)?$cant_fact:0?> / </b> <font size=2 color= red> <b> Meta Semestral: 5 </b></font>
			<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
		</td> 
		<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
		<?$porcentaje=($embarazadas_12_pers/$captacion_temprana)*100;
		$porcentaje=number_format ($porcentaje,2,',','.')?>
		Total de Embarazadas antes de las 12 semanas: <b><?=($embarazadas_12_pers)?$embarazadas_12_pers:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$captacion_temprana?> / </b></font>--> <font size=2 color= red> <b>Meta Semestral : <?=$captacion_temprana?> </b></font>
		<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
		</td>    	
		     
		</tr>
		<tr>
		<td align="center" border=1 bordercolor=#2C1701>
		<? $link_s=encode_link("metas_grafico.php",array("dato"=>$cant_fact,"metarrhh_s"=>5,"tamaño"=>"small","nombre"=>"cantidad_facturas","cuie"=>$cuie));
		echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
		</td>
		<td align="center" border=1 bordercolor=#2C1701>
		<? $link_s=encode_link("metas_grafico.php",array("dato"=>$embarazadas_12_pers,"metarrhh_s"=>$captacion_temprana,"tamaño"=>"small","nombre"=>"embarazadas_12_pers","cuie"=>$cuie));
		echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
		</td>
		</tr>
		
		<tr>
			<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			<?$porcentaje=($embarazadas/$promedio_controles_x_emb)*100;
			$porcentaje=number_format ($porcentaje,2,',','.')?>
			Total de Controles de Embarazo segun periodo (por fecha de control): <b><?=($embarazadas)?$embarazadas:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$promedio_controles_x_emb?> / </b></font> --><font size=2 color= red> <b>Meta Semestral: <?=$promedio_controles_x_emb?> </b></font>
			<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
			</td>	
		 	<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
			<?$porcentaje=($adol_new_pers_3/$adolecentes)*100;
			$porcentaje=number_format ($porcentaje,2,',','.')?>
			Total de Adolescentes de 10 a 19 años segun periodo (por fecha de control): <b><?=($adol_new_pers_3)?$adol_new_pers_3:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$adolecentes?> / </b></font>--> <font size=2 color= red> <b>Meta Anual x RRHH: <?=$adolecentes?> </b></font>
			<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
			</td> 
			</tr>
		 	<tr>
			
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$embarazadas,"metarrhh_s"=>$promedio_controles_x_emb,"tamaño"=>"small","nombre"=>"embarazadas","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$adol_new_pers_3,"metarrhh_s"=>$adolecentes,"tamaño"=>"small","nombre"=>"adol_new_pers_3","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			</tr>
		 	
			<tr>
						
		 	<?$ref = encode_link("detalle_ssr.php",array("cuie"=>$cuie,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
					$onclick_elegir="location.href='$ref' target='_blank'";
					$onclick_elegir="window.open('$ref' , '_blank');";?>
				<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_elegir?>" <?=atrib_tr7()?>>
				<?$porcentaje=($cuidado_sexual/$mujeres_edad_fertil)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Inscriptos que Marca Cuidado Sexual y Reproductivo: <b><?=($cuidado_sexual)?$cuidado_sexual:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$mujeres_edad_fertil?> / </b></font>--> <font size=2 color= red> <b>Meta Anual x RRHH: <?=$mujeres_edad_fertil?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td>            
            <?$ref_diab = encode_link("detalle_diab.php",array("cuie"=>$cuie,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
					$onclick_elegir="location.href='$ref_diab' target='_blank'";
					$onclick_elegir="window.open('$ref_diab' , '_blank');";?>
				<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_elegir?>" <?=atrib_tr7()?>>
				<?$porcentaje=($dia/$enfermedades_cronicas_DBT)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Controles que Marca Diabetico: <b><?=($dia)?$dia:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$enfermedades_cronicas_DBT?> / </b></font>--> <font size=2 color= red> <b>Meta Anual x RRHH: <?=$enfermedades_cronicas_DBT?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td> 
			</tr>
            
			<tr>
			
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$cuidado_sexual,"metarrhh_s"=>$mujeres_edad_fertil,"tamaño"=>"small","nombre"=>"cuidado_sexual","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$dia,"metarrhh_s"=>$enfermedades_cronicas_DBT,"tamaño"=>"small","nombre"=>"dia","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			</tr>
			
			<tr>
				
				<?$ref_hip = encode_link("detalle_hip.php",array("cuie"=>$cuie,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
					$onclick_elegir="location.href='$ref_hip' target='_blank'";
					$onclick_elegir="window.open('$ref_hip' , '_blank');";?>
				<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_elegir?>" <?=atrib_tr7()?>>
					<?$porcentaje=($hip/$enfermedades_cronicas_HTA)*100;
					$porcentaje=number_format ($porcentaje,2,',','.')?>
					Total de Controles que Marca Hipertenso: <b><?=($hip)?$hip:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$enfermedades_cronicas_HTA?> / </b></font> --><font size=2 color= red> <b>Meta Anual x RRHH: <?=$enfermedades_cronicas_HTA?> </b></font>
					<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td>
				<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<?$porcentaje=($efe_doble_viral/$vacuna_doble_viral)*100;
					$porcentaje=number_format ($porcentaje,2,',','.')?>
					Total de Vacunas Doble Viral: <b><?=($efe_doble_viral)?$efe_doble_viral:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_doble_viral?> / </b></font>--> <font size=2 color= red> <b>Meta Semestral: <?=$vacuna_doble_viral?> </b></font>
					<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td>
					
            </tr>
            <tr>
			
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$hip,"metarrhh_s"=>$enfermedades_cronicas_HTA,"tamaño"=>"small","nombre"=>"hip","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$efe_doble_viral,"metarrhh_s"=>$vacuna_doble_viral,"tamaño"=>"small","nombre"=>"vacuna_doble_viral","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			</tr> 
			 
			 <!-- graficos para vacunas -->
			 
			<tr>
				<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<?$porcentaje=($efe_hep_b/$vacuna_hep_b)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas Hepatitis B: <b><?=($efe_hep_b)?$efe_hep_b:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_hep_b?> / </b></font>--> <font size=2 color= red> <b>Meta Semestral: <?=$vacuna_hep_b?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td> 
				<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<?$porcentaje=($efe_neumococo/$vacuna_neumococo)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas Neumococo: <b><?=($efe_neumococo)?$efe_neumococo:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_neumococo?> / </b></font>--> <font size=2 color= red> <b>Meta Semestral: <?=$vacuna_neumococo?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td>            
            </tr>
			<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$efe_hep_b,"metarrhh_s"=>$vacuna_hep_b,"tamaño"=>"small","nombre"=>"vacuna_hep_b","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$efe_neumococo,"metarrhh_s"=>$vacuna_neumococo,"tamaño"=>"small","nombre"=>"vacuna_neumococo","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			</tr>
			<tr>
				<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<?$porcentaje=($efe_pentavalente/$vacuna_pentavalente)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas Pentavalentes: <b><?=($efe_pentavalente)?$efe_pentavalente:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_pentavalente?> / </b></font> --><font size=2 color= red> <b>Meta Semestral: <?=$vacuna_pentavalente?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td> 
				<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<?$porcentaje=($efe_cuadruple/$vacuna_cuadruple)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas cuadruples: <b><?=($efe_cuadruple)?$efe_cuadruple:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_cuadruple?> / </b></font>--> <font size=2 color= red> <b>Meta Semestral: <?=$vacuna_cuadruple?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td>            
            </tr>
			<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$efe_pentavalente,"metarrhh_s"=>$vacuna_pentavalente,"tamaño"=>"small","nombre"=>"vacuna_pentavalente","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$efe_cuadruple,"metarrhh_s"=>$vacuna_cuadruple,"tamaño"=>"small","nombre"=>"vacuna_cuadruple","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			</tr>
			<tr>
				<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<?$porcentaje=($efe_sabin/$vacuna_sabin)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas Sabin: <b><?=($efe_sabin)?$efe_sabin:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_sabin?> / </b></font>--> <font size=2 color= red> <b>Meta Semestral: <?=$vacuna_sabin?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td> 
				<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<?$porcentaje=($efe_triple_viral/$vacuna_triple_viral)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas Triple Viral: <b><?=($efe_triple_viral)?$efe_triple_viral:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_triple_viral?> / </b></font>--> <font size=2 color= red> <b>Meta Semestral: <?=$vacuna_triple_viral?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td>            
            </tr>
			<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$efe_sabin,"metarrhh_s"=>$vacuna_sabin,"tamaño"=>"small","nombre"=>"vacuna_sabin","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$efe_triple_viral,"metarrhh_s"=>$vacuna_triple_viral,"tamaño"=>"small","nombre"=>"vacuna_triple_viral","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			</tr>
			<tr>
				<? $efe_gripe=$efe_gripe_1+$efe_gripe_2?>
				<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<?$porcentaje=($efe_gripe/$vacuna_gripe)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas Anti-Gripales: <b><?=($efe_gripe)?$efe_gripe:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_gripe?> / </b></font>--> <font size=2 color= red> <b>Meta Semestral: <?=$vacuna_gripe?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td> 
				<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<?$porcentaje=($efe_hep_a/$vacuna_hep_a)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas Hepatitis A: <b><?=($efe_hep_a)?$efe_hep_a:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_hep_a?> / </b></font> --><font size=2 color= red> <b>Meta Semestral: <?=$vacuna_hep_a?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td>            
            </tr>
			<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<?
			$link_s=encode_link("metas_grafico.php",array("dato"=>$efe_gripe,"meta"=>$vacuna_gripe,"metarrhh"=>$vacuna_griperrhh,"metarrhh_s"=>$vacuna_gripe,"tamaño"=>"small","nombre"=>"vacuna_gripe","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$efe_hep_a,"meta"=>$vacuna_hep_a,"metarrhh"=>$vacuna_hep_arrhh,"metarrhh_s"=>$vacuna_hep_a,"tamaño"=>"small","nombre"=>"vacuna_hep_a","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			</tr>
			<tr>
				<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<?$porcentaje=($efe_triple_bacteriana_celular/$vacuna_triple_bacteriana_celular)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas Tri.Bac.Celular: <b><?=($efe_triple_bacteriana_celular)?$efe_triple_bacteriana_celular:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_triple_bacteriana_celular?> / </b></font>--> <font size=2 color= red> <b>Meta Semestral: <?=$vacuna_triple_bacteriana_celular?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td> 
				<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<?$porcentaje=($efe_triple_bacteriana_acelular/$vacuna_triple_bacteriana_acelular)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas Tri.Bac.Acelular: <b><?=($efe_triple_bacteriana_acelular)?$efe_triple_bacteriana_acelular:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_triple_bacteriana_acelular?> / </b></font>--> <font size=2 color= red> <b>Meta Semestral: <?=$vacuna_triple_bacteriana_acelular?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td>            
            </tr>
			<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$efe_triple_bacteriana_celular,"metarrhh_s"=>$vacuna_triple_bacteriana_celular,"tamaño"=>"small","nombre"=>"vacuna_triple_bacteriana_celular","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$efe_triple_bacteriana_acelular,"metarrhh_s"=>$vacuna_triple_bacteriana_acelular,"tamaño"=>"small","nombre"=>"vacuna_triple_bacteriana_acelular","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			</tr>
			<tr>
				<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<?$porcentaje=($efe_doble_bacteriana/$vacuna_doble_bacteriana)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas Doble Bacteriana: <b><?=($efe_doble_bacteriana)?$efe_doble_bacteriana:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_doble_bacteriana?> / </b></font>--> <font size=2 color= red> <b>Meta Semestral: <?=$vacuna_doble_bacteriana?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td> 
				<td align="center" border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<?$porcentaje=($efe_hpv/$vacuna_vph)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas VPH: <b><?=($efe_hpv)?$efe_hpv:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_vph?> / </b></font> --><font size=2 color= red> <b>Meta Semestral: <?=$vacuna_vph?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td>            
            </tr>
			<tr>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$efe_doble_bacteriana,"metarrhh_s"=>$vacuna_doble_bacteriana,"tamaño"=>"small","nombre"=>"vacuna_doble_bacteriana","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			<td align="center" border=1 bordercolor=#2C1701>
			<? $link_s=encode_link("metas_grafico.php",array("dato"=>$efe_hpv,"metarrhh_s"=>$vacuna_vph,"tamaño"=>"small","nombre"=>"vacuna_vph","cuie"=>$cuie));
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
			</td>
			</tr>	
			 
</table>





<?}?>
<BR>
 <tr><td><table width=90% align="center" class="bordes">
  <tr align="center">
   <td>
    <!-- <input type=button name="volver" value="Volver" onclick="document.location='seguimiento.php'"title="Volver al Listado" style="width=150px">     
   -->
   </td>
  </tr>
 </table></td></tr>
 
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>
