<?

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

function extrae_anio($fecha) {
        list($d,$m,$a) = explode("/",$fecha);
        //$a=$a+2000;
        return $a;
		}


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
	$sql_cuie="select * from nacer.efe_conv where cuie='$cuie'";
	$res_cuie= sql($sql_cuie, "Error al traer el Efector") or fin_pagina();
	$id_efe_conv=$res_cuie->fields['id_efe_conv'];
	}

if ($_POST['muestra']=="Muestra"){	
	
	$fecha_desde=fecha_db($_POST['fecha_desde']);
	$fecha_hasta=fecha_db($_POST['fecha_hasta']);
	
	/*$anio=extrae_anio($_POST['fecha_desde']);
	$fecha_desde_anual="$anio"."-01-01";
	$fecha_hasta_anual="$anio"."-12-31";*/
	  		
			
	//CEB
$sql_ceb="SELECT grupo,count (*) as cantidad from (
SELECT distinct ON (cuie,afidni,afinombre,afiapellido,afifechanac)
		cuie,afidni,afinombre,afiapellido,afifechanac,grupo,fecha_comprobante,codigo,descripcion from (
		select facturacion.prestacion.*,
		facturacion.comprobante.id_smiafiliados,
		facturacion.comprobante.cuie,
		facturacion.comprobante.fecha_comprobante,
		nacer.smiafiliados.afidni,
		nacer.smiafiliados.afinombre,
		nacer.smiafiliados.afiapellido,
		nacer.smiafiliados.afifechanac,
		nacer.smiafiliados.grupopoblacional as grupo,
		facturacion.nomenclador.codigo,
		facturacion.nomenclador.descripcion
		from facturacion.prestacion
		inner join facturacion.nomenclador on prestacion.id_nomenclador=nomenclador.id_nomenclador
		inner join facturacion.comprobante on prestacion.id_comprobante=comprobante.id_comprobante
		inner join nacer.smiafiliados on comprobante.id_smiafiliados=smiafiliados.id_smiafiliados
		where facturacion.nomenclador.ceb='s' and facturacion.comprobante.cuie='$cuie' and 
		facturacion.comprobante.fecha_comprobante between '2014-12-31' and '2015-12-31'
		order by nacer.smiafiliados.afidni
	) as ccc 
) as ttt
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
			$sql_facturacion="SELECT count (*) as cantidad from (
					select distinct on (facturacion.factura.periodo_actual)
	expediente.expediente.id_expediente,
	expediente.expediente.nro_exp,
	expediente.expediente.id_factura,
	expediente.expediente.monto,
	expediente.expediente.fecha_ing,
	nacer.efe_conv.cuie,
	nacer.efe_conv.nombre as efector,
	facturacion.factura.periodo_actual as periodo_fact,
	facturacion.factura.fecha_factura

from expediente.expediente 
inner join nacer.efe_conv on nacer.efe_conv.id_efe_conv=expediente.expediente.id_efe_conv
inner join facturacion.factura on expediente.expediente.id_factura=facturacion.factura.id_factura

where fecha_ing between '$fecha_desde' and '$fecha_hasta' and nacer.efe_conv.cuie='$cuie'
					) as cantidad";
			$res_sql_fact=sql($sql_facturacion) or die;
			$cant_fact=$res_sql_fact->fields['cantidad'];
			
			
		//embarazadas
    		
		 $sql_embarazadas="SELECT count(*)  as total from (SELECT distinct nacer.smiafiliados.afidni,
		nacer.smiafiliados.afinombre,
		nacer.smiafiliados.afiapellido,
		trazadorassps.trazadora_2.edad_gestacional,
		trazadorassps.trazadora_2.fecha_control,
		trazadorassps.trazadora_2.tension_arterial
		from trazadorassps.trazadora_2 
		inner join nacer.smiafiliados on trazadorassps.trazadora_2.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
		where cuie = '$cuie' and (fecha_control between '$fecha_desde' and '$fecha_hasta')

		union --trazadorassps.trazadora_2 con beneficiarios en leche.beneficiarios
		
		select distinct leche.beneficiarios.documento,
		leche.beneficiarios.nombre,
		leche.beneficiarios.apellido,
		trazadorassps.trazadora_2.edad_gestacional,
		trazadorassps.trazadora_2.fecha_control,
		trazadorassps.trazadora_2.tension_arterial
		from trazadorassps.trazadora_2 
		inner join leche.beneficiarios on trazadorassps.trazadora_2.id_beneficiarios=leche.beneficiarios.id_beneficiarios
		where cuie = '$cuie' and (trazadora_2.fecha_control between '$fecha_desde' and '$fecha_hasta')

		union

		SELECT distinct (num_doc::numeric (10,0))::text, nombre,apellido,sem_gestacion,fecha_control,ta
		from trazadoras.embarazadas where cuie = '$cuie' and 
		fecha_control between '$fecha_desde' and '$fecha_hasta') as ccc";

		$res_sql_emb= sql($sql_embarazadas) or die;
		
		    
		    $sql_embarazadas_pers = "SELECT count (num_doc)as total 
										from (select distinct num_doc 
												from trazadoras.embarazadas 
												where 
												cuie = '$cuie' and 
												fecha_control between '$fecha_desde' and '$fecha_hasta' )as cons1";
			$res_sql_emb_pers= sql($sql_embarazadas_pers) or die;
		    $embarazadas_pers=$res_sql_emb_pers->fields['total'];

		    //sumo ambas consultas
		    $embarazadas=$res_sql_emb->fields['total']+$embarazadas_pers;
		    
		    
		    //semana 20
		   /*$sql_embarazadas_20="SELECT count (num_doc) as total from trazadoras.embarazadas where cuie = '$cuie' and 
		    fecha_control between '$fecha_desde' and '$fecha_hasta' and fecha_control <= fpp-140";

		    $res_sql_emb_20= sql($sql_embarazadas_20) or die;
		    $embarazadas_20=$res_sql_emb_20->fields['total'];
		    
		    $sql_embarazadas_20_pers = "SELECT count (num_doc)as total 
										from (select distinct num_doc 
												from trazadoras.embarazadas 
												where 
												cuie = '$cuie' and 
												fecha_control between '$fecha_desde' and '$fecha_hasta' and
												fecha_control <= fpp-140 ) as cons1";
			$res_sql_emb_20_pers= sql($sql_embarazadas_20_pers) or die;
		    $embarazadas_20_pers=$res_sql_emb_20_pers->fields['total'];*/
		    
		//antes de la semana 12
		$sql_embarazadas_12_pers = "SELECT count (*) as total 
							from (SELECT distinct on (nacer.smiafiliados.afidni)
		nacer.smiafiliados.afidni,
		nacer.smiafiliados.afinombre,
		nacer.smiafiliados.afiapellido,
		trazadorassps.trazadora_1.fum,
		trazadorassps.trazadora_1.fpp,
		trazadorassps.trazadora_1.edad_gestacional,
		trazadorassps.trazadora_1.fecha_control_prenatal
		from trazadorassps.trazadora_1 
		inner join nacer.smiafiliados on trazadorassps.trazadora_1.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
		where cuie = '$cuie' and 
		--fecha_control_prenatal <= fpp-196 
		trazadorassps.trazadora_1.edad_gestacional<=12 and 
		(fecha_control_prenatal between '$fecha_desde' and '$fecha_hasta')

		union --trazadorassps.trazadora_1 con beneficiarios en leche.beneficiarios
		
		select distinct on (leche.beneficiarios.documento)
		leche.beneficiarios.documento,
		leche.beneficiarios.nombre,
		leche.beneficiarios.apellido,
		trazadorassps.trazadora_1.fum,
		trazadorassps.trazadora_1.fpp,
		trazadorassps.trazadora_1.edad_gestacional,		
		trazadorassps.trazadora_1.fecha_control_prenatal
		from trazadorassps.trazadora_1 
		inner join leche.beneficiarios on trazadorassps.trazadora_1.id_beneficiarios=leche.beneficiarios.id_beneficiarios
		where cuie = '$cuie' and 
		--fecha_control_prenatal <= fpp-196 
		trazadorassps.trazadora_1.edad_gestacional<=12 and 
		(trazadora_1.fecha_control_prenatal between '$fecha_desde' and '$fecha_hasta')

		union

		select distinct on (num_doc) 
							(num_doc::numeric(10,0))::text as dni,nombre,apellido,fum,fpp,sem_gestacion,fecha_control
							from trazadoras.embarazadas 
							where 
							cuie = '$cuie' and 
							fecha_control between '$fecha_desde' and '$fecha_hasta' and
							--fecha_control <= fpp-196
							trazadoras.embarazadas.sem_gestacion<=12
							) as cons1";

		$res_sql_emb_12_pers= sql($sql_embarazadas_12_pers) or die;
		$embarazadas_12_pers=$res_sql_emb_12_pers->fields['total'];
		    
		//ninio menores de 1 anio
		$sql_ninio="SELECT count (*) as total from (
		SELECT distinct nacer.smiafiliados.afidni,
		nacer.smiafiliados.afinombre,
		nacer.smiafiliados.afiapellido,
		nacer.smiafiliados.afifechanac,
		trazadorassps.trazadora_4.fecha_control,
		trazadorassps.trazadora_4.peso,
  		trazadorassps.trazadora_4.talla,
  		trazadorassps.trazadora_4.perimetro_cefalico,
  		trazadorassps.trazadora_4.percentilo_peso_edad,
  		trazadorassps.trazadora_4.percentilo_talla_edad,
  		trazadorassps.trazadora_4.percentilo_perim_cefalico_edad,
  		trazadorassps.trazadora_4.percentilo_peso_talla
		from trazadorassps.trazadora_4 
		inner join nacer.smiafiliados on trazadorassps.trazadora_4.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
		where cuie = '$cuie' and 
		(fecha_control - fecha_nac >= 0 and fecha_control - fecha_nac < 365) and
		(fecha_control between '$fecha_desde' and '$fecha_hasta')

		union --trazadorassps.trazadora_4 con beneficiarios en leche.beneficiarios
		
		select distinct leche.beneficiarios.documento,
		leche.beneficiarios.nombre,
		leche.beneficiarios.apellido,
		leche.beneficiarios.fecha_nac,
		trazadorassps.trazadora_4.fecha_control,
		trazadorassps.trazadora_4.peso,
  		trazadorassps.trazadora_4.talla,
  		trazadorassps.trazadora_4.perimetro_cefalico,
  		trazadorassps.trazadora_4.percentilo_peso_edad,
  		trazadorassps.trazadora_4.percentilo_talla_edad,
  		trazadorassps.trazadora_4.percentilo_perim_cefalico_edad,
  		trazadorassps.trazadora_4.percentilo_peso_talla
		from trazadorassps.trazadora_4 
		inner join leche.beneficiarios on trazadorassps.trazadora_4.id_beneficiarios=leche.beneficiarios.id_beneficiarios
		where cuie = '$cuie' and 
		(trazadora_4.fecha_control - trazadora_4.fecha_nac >= 0 and trazadora_4.fecha_control - trazadora_4.fecha_nac < 365) and
		(trazadora_4.fecha_control between '$fecha_desde' and '$fecha_hasta')
		
		union --beneficiarios en trazadoras.nino_new

		SELECT distinct (num_doc::numeric(10,0))::text as dni,nombre,apellido,fecha_nac,fecha_control,
		peso,talla,perim_cefalico,percen_peso_edad,percen_talla_edad,percen_perim_cefali_edad,percen_peso_talla
		from trazadoras.nino_new 
		where 
		cuie = '$cuie' and 
		(fecha_control - fecha_nac >= 0 and fecha_control - fecha_nac < 365) and (fecha_control between '$fecha_desde' and '$fecha_hasta')
		) as ccc";

		$res_sql_ninio_trzsps= sql($sql_ninio) or die;
		$ninios_new_1=$res_sql_ninio_trzsps->fields['total'];
		    
		/*$sql_ninio_pers="SELECT count (num_doc) as total
								from (
									select distinct num_doc 
										from trazadoras.nino_new 
										where 
											cuie = '$cuie' and 
											(fecha_control - fecha_nac >= 0 and fecha_control - fecha_nac < 365) and
											(fecha_control between '$fecha_desde' and '$fecha_hasta'))as cons1";

		$res_sql_ninio_pers= sql($sql_ninio_pers) or die;
		$ninios_new_pers_1=$res_sql_ninio_pers->fields['total'];*/
		    
		    

		//calculo de los controles de niños entre 1 y 9 años
		$cons_1_a_9="SELECT count(*) as total from (
		SELECT distinct nacer.smiafiliados.afidni,
		nacer.smiafiliados.afinombre,
		nacer.smiafiliados.afiapellido,
		nacer.smiafiliados.afifechanac,
		trazadorassps.trazadora_7.fecha_control
		from trazadorassps.trazadora_7 
		inner join nacer.smiafiliados on trazadorassps.trazadora_7.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
		where cuie = '$cuie' and 
		(fecha_control - fecha_nac >= 366 and fecha_control - fecha_nac < 3285) and
		(fecha_control between '2014-12-31' and '2015-12-31')

		union --trazadorassps.trazadora_7 con beneficiarios en leche.beneficiarios
		
		select distinct leche.beneficiarios.documento,
		leche.beneficiarios.nombre,
		leche.beneficiarios.apellido,
		leche.beneficiarios.fecha_nac,
		trazadorassps.trazadora_7.fecha_control
		from trazadorassps.trazadora_7 
		inner join leche.beneficiarios on trazadorassps.trazadora_7.id_beneficiarios=leche.beneficiarios.id_beneficiarios
		where cuie = '$cuie' and 
		(trazadora_7.fecha_control - trazadora_7.fecha_nac >= 366 and trazadora_7.fecha_control - trazadora_7.fecha_nac < 3285) and
		(trazadora_7.fecha_control between '2014-12-31' and '2015-12-31')
		
		union --beneficiarios en trazadoras.nino_new

		SELECT distinct (num_doc::numeric(10,0))::text as dni,nombre,apellido,fecha_nac,fecha_control from trazadoras.nino_new 
		where 
		cuie = '$cuie' and 
		(fecha_control - fecha_nac >= 366 and fecha_control - fecha_nac < 3285) and (fecha_control between '2014-12-31' and '2015-12-31')
		) as ccc";

		$res_cons_1_a_9=sql($cons_1_a_9) or die;
		$ninios_1_a_9=$res_cons_1_a_9->fields['total'];

				    
		    $sql_ninio_pers="SELECT count (num_doc) as total
								from (
									select distinct num_doc 
									from trazadoras.nino_new 
									where 
									cuie = '$cuie' and 
									(fecha_control - fecha_nac >= 366 and fecha_control - fecha_nac < 730) and
									(fecha_control between '$fecha_desde' and '$fecha_hasta'))as cons1";
		    $res_sql_ninio_pers= sql($sql_ninio_pers) or die;
		    $ninios_new_pers_2=$res_sql_ninio_pers->fields['total'];
		    
		    		    
		    $sql_ninio_pers="SELECT count (num_doc) as total
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
		$sql_adol="SELECT count(*) as total from (
						SELECT distinct num_doc,fecha_control from trazadoras.nino_new 
						where 
						cuie = '$cuie' and 
						(fecha_control - fecha_nac >= 3651 and fecha_control - fecha_nac < 7299) and
						(fecha_control between '2014-12-31' and '2015-12-31')
						) as ccc";

		    $res_sql_adol= sql($sql_adol) or die;
		    
		    $sql_adol="SELECT count (*) as total from (
						select distinct nacer.smiafiliados.afidni,
		nacer.smiafiliados.afinombre,
		nacer.smiafiliados.afiapellido,
		trazadorassps.trazadora_10.fecha_control,
		trazadorassps.trazadora_10.talla,
		trazadorassps.trazadora_10.peso,
		trazadorassps.trazadora_10.tension_arterial
		from trazadorassps.trazadora_10
		inner join nacer.smiafiliados on trazadorassps.trazadora_10.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
		where cuie = '$cuie' and 
		(fecha_control - fecha_nac >= 3651 and fecha_control - fecha_nac < 7299) and
		(fecha_control between '2014-12-31' and '2015-12-31')
		union 
		select distinct leche.beneficiarios.documento,
			leche.beneficiarios.nombre,
			leche.beneficiarios.apellido,
			trazadorassps.trazadora_10.fecha_control,
			trazadorassps.trazadora_10.talla,
			trazadorassps.trazadora_10.peso,
			trazadorassps.trazadora_10.tension_arterial
			from trazadorassps.trazadora_10 
			inner join leche.beneficiarios on trazadorassps.trazadora_10.id_beneficiarios=leche.beneficiarios.id_beneficiarios
			where cuie = '$cuie' and 
			(trazadora_10.fecha_control - trazadora_10.fecha_nac >= 3651 and trazadora_10.fecha_control - trazadora_10.fecha_nac < 7299) and
			(trazadora_10.fecha_control between '2014-12-31' and '2015-12-31')
		    	) as ccc";

		    $res_sql_adol_trz10= sql($sql_adol) or die;
		    
		   $adol_new_pers_3=$res_sql_adol->fields['total']+$res_sql_adol_trz10->fields['total'];
		    
		    // fin de adolescentes	 
		    
		    //cuidado sexual
		    
			$sql_cuidado_sexual="SELECT Count (*) as total from 
				(SELECT distinct on (nacer.smiafiliados.afidni,fichero.fichero.fecha_control)
				nacer.smiafiliados.afidni,
				nacer.smiafiliados.afinombre,
				nacer.smiafiliados.afiapellido,
				nacer.smiafiliados.afifechanac,
				fichero.fichero.fecha_control,
				fichero.fichero.peso,
				fichero.fichero.talla,
				fichero.fichero.ta
				from fichero.fichero
				inner join nacer.smiafiliados on fichero.fichero.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
				where cuie = '$cuie' and 
				(fecha_control between '2014-12-31' and '2015-12-31' and fichero.fichero.salud_rep = 'SI')
				union 

				select distinct on (leche.beneficiarios.documento,fichero.fichero.fecha_control)
				leche.beneficiarios.documento,
				leche.beneficiarios.nombre,
				leche.beneficiarios.apellido,
				leche.beneficiarios.fecha_nac,
				fichero.fichero.fecha_control,
				fichero.fichero.peso,
				fichero.fichero.talla,
				fichero.fichero.ta
				from fichero.fichero 
				inner join leche.beneficiarios on fichero.fichero.id_beneficiarios=leche.beneficiarios.id_beneficiarios
				where cuie = '$cuie' and			
				(fecha_control between '2014-12-31' and '2015-12-31') and fichero.salud_rep = 'SI') as a";
			
			$result_ssr1=sql($sql_cuidado_sexual) or fin_pagina();
			$cuidado_sexual=$result_ssr1->fields['total'];
		 
	//dia e hip
			$sql_dia="SELECT count(*) as total from (
						SELECT distinct on (afidni,fecha_control)
          afidni,afinombre,afiapellido,afifechanac,fecha_control,estado from (
          SELECT distinct on (nacer.smiafiliados.afidni,fichero.fichero.fecha_control)
          nacer.smiafiliados.afidni,
          nacer.smiafiliados.afinombre,
          nacer.smiafiliados.afiapellido,
          nacer.smiafiliados.afifechanac,
          fichero.fichero.fecha_control,
          'desde fichero (nacer)' as estado
          from fichero.fichero
          inner join nacer.smiafiliados on fichero.fichero.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
          where cuie='$cuie' and fichero.fecha_control between '2014-12-31' and '2015-12-31' and diabetico='SI'
union
          select distinct on (leche.beneficiarios.documento,fichero.fichero.fecha_control)
          leche.beneficiarios.documento,
          leche.beneficiarios.nombre,
          leche.beneficiarios.apellido,
          leche.beneficiarios.fecha_nac,
          fichero.fichero.fecha_control,
          'desde fichero (emp.rapido)' as estado
          from fichero.fichero
          inner join leche.beneficiarios on fichero.fichero.id_beneficiarios=leche.beneficiarios.id_beneficiarios
          where cuie='$cuie' and 
          fichero.fecha_control between '2014-12-31' and '2015-12-31' and diabetico='SI'
union

    SELECT distinct on (clasificacion_remediar2.num_doc,clasificacion_remediar2.fecha_control)
    clasificacion_remediar2.num_doc,
    clasificacion_remediar2.nombre,
    clasificacion_remediar2.apellido,
    clasificacion_remediar2.fecha_nac,
    clasificacion_remediar2.fecha_control,
    'desde clasificacion' as estado
    from trazadoras.clasificacion_remediar2
    where cuie = '$cuie' and fecha_control between '2014-12-31' and '2015-12-31' and diabetico = 'SI'

union
--consulta desde seguimientos
	
	select distinct on (nacer.smiafiliados.afidni,trazadoras.seguimiento_remediar.fecha_comprobante)
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afifechanac,
	trazadoras.seguimiento_remediar.fecha_comprobante as fecha_control,
	'desde seguimiento' as estado
	--trazadoras.clasificacion_remediar2.diabetico,
	--trazadoras.clasificacion_remediar2.hipertenso
	from trazadoras.seguimiento_remediar
	inner join nacer.smiafiliados on nacer.smiafiliados.clavebeneficiario=trim (' ' from trazadoras.seguimiento_remediar.clave_beneficiario)
	inner join trazadoras.clasificacion_remediar2 on trazadoras.clasificacion_remediar2.num_doc=nacer.smiafiliados.afidni
	where trazadoras.seguimiento_remediar.efector='$cuie' and (trazadoras.clasificacion_remediar2.diabetico is not null or trazadoras.clasificacion_remediar2.hipertenso is not null)
	and trazadoras.seguimiento_remediar.fecha_comprobante between '2014-12-31' and '2015-12-31'
	and trazadoras.clasificacion_remediar2.diabetico='SI'

    ) as ccc order by 1,5 ) as cantidad";

	$result_dia=sql($sql_dia) or fin_pagina();


	$dia=$result_dia->fields['total'] ;
				
			
										
	$sql_hip="SELECT count(*) as total from (
				SELECT distinct on (afidni,fecha_control)
          afidni,afinombre,afiapellido,afifechanac,fecha_control,estado from (
          SELECT distinct on (nacer.smiafiliados.afidni,fichero.fichero.fecha_control)
          nacer.smiafiliados.afidni,
          nacer.smiafiliados.afinombre,
          nacer.smiafiliados.afiapellido,
          nacer.smiafiliados.afifechanac,
          fichero.fichero.fecha_control,
          'desde fichero (nacer)' as estado
          from fichero.fichero
          inner join nacer.smiafiliados on fichero.fichero.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
          where cuie='$cuie' and fichero.fecha_control between '2014-12-31' and '2015-12-31' and hipertenso='SI'
union
          select distinct on (leche.beneficiarios.documento,fichero.fichero.fecha_control)
          leche.beneficiarios.documento,
          leche.beneficiarios.nombre,
          leche.beneficiarios.apellido,
          leche.beneficiarios.fecha_nac,
          fichero.fichero.fecha_control,
          'desde fichero (emp.rapido)' as estado
          from fichero.fichero
          inner join leche.beneficiarios on fichero.fichero.id_beneficiarios=leche.beneficiarios.id_beneficiarios
          where cuie='$cuie' and 
          fichero.fecha_control between '2014-12-31' and '2015-12-31' and hipertenso='SI'
union

    SELECT distinct on (clasificacion_remediar2.num_doc,clasificacion_remediar2.fecha_control)
    clasificacion_remediar2.num_doc,
    clasificacion_remediar2.nombre,
    clasificacion_remediar2.apellido,
    clasificacion_remediar2.fecha_nac,
    clasificacion_remediar2.fecha_control,
    'desde clasificacion' as estado
    from trazadoras.clasificacion_remediar2
    where cuie = '$cuie' and fecha_control between '2014-12-31' and '2015-12-31' and hipertenso = 'SI'

union
--consulta desde seguimientos
	
	select distinct on (nacer.smiafiliados.afidni,trazadoras.seguimiento_remediar.fecha_comprobante)
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afifechanac,
	trazadoras.seguimiento_remediar.fecha_comprobante as fecha_control,
	'desde seguimiento' as estado
	--trazadoras.clasificacion_remediar2.diabetico,
	--trazadoras.clasificacion_remediar2.hipertenso
	from trazadoras.seguimiento_remediar
	inner join nacer.smiafiliados on nacer.smiafiliados.clavebeneficiario=trim (' ' from trazadoras.seguimiento_remediar.clave_beneficiario)
	inner join trazadoras.clasificacion_remediar2 on trazadoras.clasificacion_remediar2.num_doc=nacer.smiafiliados.afidni
	where trazadoras.seguimiento_remediar.efector='$cuie' and (trazadoras.clasificacion_remediar2.hipertenso is not null or trazadoras.clasificacion_remediar2.hipertenso is not null)
	and trazadoras.seguimiento_remediar.fecha_comprobante between '2014-12-31' and '2015-12-31'
	and trazadoras.clasificacion_remediar2.hipertenso='SI'

    ) as ccc order by 1,5
				) as cantidad";
				
	$res_hip= sql($sql_hip) or die;
	$hip=$res_hip->fields['total'];
			
	//vacunas consulta vieja aun sirve revisar
			/*$sql_vac="SELECT id_vac_apli,nom_vacum,sum (cantidad) as cant from (
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
				group by id_vac_apli,nom_vacum";*/

			$sql_vac="SELECT id_vac_apli,count (*) as cant from (
select distinct on (nacer.smiafiliados.afidni,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afifechanac
from trazadoras.vacunas 
inner join nacer.smiafiliados on vacunas.id_smiafiliados=smiafiliados.id_smiafiliados
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)

union

select distinct on (leche.beneficiarios.documento,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac)
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	leche.beneficiarios.documento,
	leche.beneficiarios.nombre,
	leche.beneficiarios.apellido,
	leche.beneficiarios.fecha_nac	
from trazadoras.vacunas 
inner join leche.beneficiarios on vacunas.id_beneficiarios=beneficiarios.id_beneficiarios
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)
) as ccc group by id_vac_apli";
			
			$res_vacunas=sql($sql_vac) or die;
			while (!$res_vacunas->EOF){
			switch ($res_vacunas->fields['id_vac_apli']){
				case 2 : $efe_hep_b=$res_vacunas->fields['cant'];break;
				case 17 : $efe_neumococo_1=$res_vacunas->fields['cant'];break;
				case 16 : $efe_neumococo_2=$res_vacunas->fields['cant'];break;
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
		$efe_neumococo=$efe_neumococo_1+$efe_neumococo_2;	
				
				
//llenar con las consultas
}

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



$sql_sitam="select cantidad from nacer.sitam where cuie='$cuie'";
$res_sitam=sql($sql_sitam,"Error al traer los datos del sitam") or fin_pagina();
$paps=$res_sitam->fields['cantidad'];
  



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

<form name='form1' action='detalle_cumplimiento.php' method='POST'>
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
        <?if ($_POST['muestra']){
         	
        $link=encode_link("efec_cumplimiento_pdf.php",array("id_efe_conv"=>$id_efe_conv,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));?> 
        <img src="../../imagenes/pdf_logo.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')"> 
        <?}?>
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
		 	<font size=4 color="red" ><b>Nota Importante: las metas anuales estan fijadas con periodo desde 2014-12-31 al 2015-12-31 <BR></b> </font>
		 	<font size=4 color="red" ><b>Las mismas son evaluadas al 50% de la meta anual para el cumplimiento del primer semestre <BR></b> </font>
		 	</td>
		 </tr>
		    <tr>
				<?$ref_ceb_a = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"ceb_a","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_ceb_a="window.open('$ref_ceb_a' , '_blank');";?>
				<td align="center"  border=1 bordercolor=#2C1701 onclick="<?=$onclick_ceb_a?>" <?=atrib_tr7()?>>
					<?$porcentaje=($ceb_a/$ceb_ceroacinco)*100;
					  $porcentaje=number_format ($porcentaje,2,',','.')?>
					Cobertura Efectiva Basica entre 0 y 5 años: <b><?=($ceb_a)?$ceb_a:0?> / </b> <font size=2 color= red> <b>Meta Anual: <?=$ceb_ceroacinco?> </b></font>
					<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				
				</td>   
			   	<?$ref_ceb_b = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"ceb_b","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_ceb_b="window.open('$ref_ceb_b' , '_blank');";?>
			   	<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_ceb_b?>" <?=atrib_tr7()?>>
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
				<?$ref_ceb_c = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"ceb_c","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_ceb_c="window.open('$ref_ceb_c' , '_blank');";?>
			   	<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_ceb_c?>" <?=atrib_tr7()?>>

					<?$porcentaje=($ceb_c/$ceb_diezadiecinueve)*100;
					  $porcentaje=number_format ($porcentaje,2,',','.')?>
					Cobertura Efectiva Basica entre 10 y 19 años: <b><?=($ceb_c)?$ceb_c:0?> / </b> <font size=2 color= red> <b>Meta Anual: <?=$ceb_diezadiecinueve?> </b></font>
					<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				  </td> 

			   	<?$ref_ceb_d = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"ceb_d","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_ceb_d="window.open('$ref_ceb_d' , '_blank');";?>
			   	
			   	<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_ceb_d?>" <?=atrib_tr7()?>>
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
		<?$ref_cns_1 = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"controles de ninos menor de 1","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
		$onclick_cns_1="window.open('$ref_cns_1' , '_blank');";?>

		<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_cns_1?>" <?=atrib_tr7()?>>
			<?$porcentaje=($ninios_new_1/$cns_menor_1_año)*100;
			$porcentaje=number_format ($porcentaje,2,',','.')?>
			Total de Controles de Ninos menor de 1 año segun periodo (por fecha de control): <b><?=($ninios_new_1)?$ninios_new_1:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$cns_menor_1_año?>  / </b></font>--> <font size=2 color= red> <b>Meta Semestral x RRHH: <?=$cns_menor_1_año?> </b></font>
			<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
		</td>   
		
		<?$ref_cns_1_9 = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"controles_1_a_9","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
		$onclick_cns_1_9="window.open('$ref_cns_1_9' , '_blank');";?>
		
		<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_cns_1_9?>" <?=atrib_tr7()?>>
		<? $porcentaje=($ninios_1_a_9/$cns_entre_1_y_9)*100;
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
		<?$ref_fact = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"facturacion","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
		$onclick_fact="window.open('$ref_fact' , '_blank');";?>
		
		<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_fact?>" <?=atrib_tr7()?>>
			<?$porcentaje=($cant_fact/5)*100;
			$porcentaje=number_format ($porcentaje,2,',','.')?>
			Meta de presentacion de facturacion: <b><?=($cant_fact)?$cant_fact:0?> / </b> <font size=2 color= red> <b> Meta Semestral: 5 </b></font>
			<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
		</td> 
		
		<?$ref_emb_12 = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"embar_antes_sem_12","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
		$onclick_emb_12="window.open('$ref_emb_12' , '_blank');";?>
		
		<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_emb_12?>" <?=atrib_tr7()?>>
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
		<?$ref_cont_emb = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"total_controles_embar","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
		$onclick_cont_emb="window.open('$ref_cont_emb' , '_blank');";?>
		
		<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_cont_emb?>" <?=atrib_tr7()?>>
			<?$porcentaje=($embarazadas/$promedio_controles_x_emb)*100;
			$porcentaje=number_format ($porcentaje,2,',','.')?>
			Total de Controles de Embarazo segun periodo (por fecha de control): <b><?=($embarazadas)?$embarazadas:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$promedio_controles_x_emb?> / </b></font> --><font size=2 color= red> <b>Meta Semestral: <?=$promedio_controles_x_emb?> </b></font>
			<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
			</td>	
		 	

		<?$ref_adol = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"adolescentes","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
		$onclick_adol="window.open('$ref_adol' , '_blank');";?>
		
		<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_adol?>" <?=atrib_tr7()?>>
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
						
		 	<?$ref_ssr = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"cuidado_sexual","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
			$onclick_ssr="window.open('$ref_ssr' , '_blank');";?>
		
			<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_ssr?>" <?=atrib_tr7()?>>
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
				
				<?$ref_vacunas = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"doble_viral","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_vacunas="window.open('$ref_vacunas' , '_blank');";?>
				<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_vacunas?>" <?=atrib_tr7()?>>
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
				<?$ref_vacunas = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"hep_b","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_vacunas="window.open('$ref_vacunas' , '_blank');";?>
				<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_vacunas?>" <?=atrib_tr7()?>>
				<?$porcentaje=($efe_hep_b/$vacuna_hep_b)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas Hepatitis B: <b><?=($efe_hep_b)?$efe_hep_b:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_hep_b?> / </b></font>--> <font size=2 color= red> <b>Meta Semestral: <?=$vacuna_hep_b?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td> 
				
				<?$ref_vacunas = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"neumococo","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_vacunas="window.open('$ref_vacunas' , '_blank');";?>
				<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_vacunas?>" <?=atrib_tr7()?>>
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
				<?$ref_vacunas = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"pentavalente","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_vacunas="window.open('$ref_vacunas' , '_blank');";?>
				<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_vacunas?>" <?=atrib_tr7()?>>
				<?$porcentaje=($efe_pentavalente/$vacuna_pentavalente)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas Pentavalentes: <b><?=($efe_pentavalente)?$efe_pentavalente:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_pentavalente?> / </b></font> --><font size=2 color= red> <b>Meta Semestral: <?=$vacuna_pentavalente?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td> 
				
				<?$ref_vacunas = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"cuadruple","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_vacunas="window.open('$ref_vacunas' , '_blank');";?>
				<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_vacunas?>" <?=atrib_tr7()?>>
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
				<?$ref_vacunas = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"sabin","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_vacunas="window.open('$ref_vacunas' , '_blank');";?>
				<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_vacunas?>" <?=atrib_tr7()?>>
				<?$porcentaje=($efe_sabin/$vacuna_sabin)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas Sabin: <b><?=($efe_sabin)?$efe_sabin:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_sabin?> / </b></font>--> <font size=2 color= red> <b>Meta Semestral: <?=$vacuna_sabin?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td> 
				
				<?$ref_vacunas = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"triple_viral","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_vacunas="window.open('$ref_vacunas' , '_blank');";?>
				<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_vacunas?>" <?=atrib_tr7()?>>
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
				<?$efe_gripe=$efe_gripe_1+$efe_gripe_2;
				$ref_vacunas = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"gripe","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_vacunas="window.open('$ref_vacunas' , '_blank');";?>
				<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_vacunas?>" <?=atrib_tr7()?>>
				<?$porcentaje=($efe_gripe/$vacuna_gripe)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas Anti-Gripales: <b><?=($efe_gripe)?$efe_gripe:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_gripe?> / </b></font>--> <font size=2 color= red> <b>Meta Semestral: <?=$vacuna_gripe?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td> 
				
				<?$ref_vacunas = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"hep_a","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_vacunas="window.open('$ref_vacunas' , '_blank');";?>
				<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_vacunas?>" <?=atrib_tr7()?>>
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
				<?$ref_vacunas = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"trip_celular","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_vacunas="window.open('$ref_vacunas' , '_blank');";?>
				<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_vacunas?>" <?=atrib_tr7()?>>
				<?$porcentaje=($efe_triple_bacteriana_celular/$vacuna_triple_bacteriana_celular)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas Tri.Bac.Celular: <b><?=($efe_triple_bacteriana_celular)?$efe_triple_bacteriana_celular:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_triple_bacteriana_celular?> / </b></font>--> <font size=2 color= red> <b>Meta Semestral: <?=$vacuna_triple_bacteriana_celular?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td> 
				
				<?$ref_vacunas = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"trip_acelular","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_vacunas="window.open('$ref_vacunas' , '_blank');";?>
				<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_vacunas?>" <?=atrib_tr7()?>>
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
				<?$ref_vacunas = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"doble_bacteriana","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_vacunas="window.open('$ref_vacunas' , '_blank');";?>
				<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_vacunas?>" <?=atrib_tr7()?>>
				<?$porcentaje=($efe_doble_bacteriana/$vacuna_doble_bacteriana)*100;
				$porcentaje=number_format ($porcentaje,2,',','.')?>
				Total de Vacunas Doble Bacteriana: <b><?=($efe_doble_bacteriana)?$efe_doble_bacteriana:0?> / </b> <!--<font size=2 color= red> <b>Meta Anual: <?=$vacuna_doble_bacteriana?> / </b></font>--> <font size=2 color= red> <b>Meta Semestral: <?=$vacuna_doble_bacteriana?> </b></font>
				<font size=2 color=green><b>(<?=$porcentaje?> %) </b></font>
				</td> 
				
				<?$ref_vacunas = encode_link("datos_detalle_cumplimiento.php",array("cuie"=>$cuie,"solicita_datos"=>"vph","fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
				$onclick_vacunas="window.open('$ref_vacunas' , '_blank');";?>
				<td align="center" border=1 bordercolor=#2C1701 onclick="<?=$onclick_vacunas?>" <?=atrib_tr7()?>>
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
  <?
   if (!es_cuie($user)){ ?>
		<td>
     	<input type=button name="volver" value="Volver" onclick="document.location='seguimiento.php'"title="Volver al Listado" style="width=150px">     
   		</td>
   <?} 
   else {?>

   		<td>
     	<input type=button name="volver" value="Volver" onclick="document.location='efectores_detalle.php'"title="Volver al Listado" style="width=150px">     
   		</td>
  <?}?>
  </tr>
 </table></td></tr>
 
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>
