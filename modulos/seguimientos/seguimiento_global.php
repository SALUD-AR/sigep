<?php

require_once("../../config.php");

variables_form_busqueda("seguimiento_global");
cargar_calendario();

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);


/*if ($_POST['muestra']=="Muestra"){	
	
	
}*/
//$user_login1=substr($_ses_user['login'],0,6);

  	 	
echo $html_header;
?>
<form name=form1 action="seguimiento_global.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<b>	
			
		Desde: <input type=text id=fecha_desde name=fecha_desde value='<?=$fecha_desde?>' size=15 readonly>
		<?=link_calendario("fecha_desde");?>
		
		Hasta: <input type=text id=fecha_hasta name=fecha_hasta value='<?=$fecha_hasta?>' size=15 readonly>
		<?=link_calendario("fecha_hasta");?> 
		
		   
	    
	    &nbsp;&nbsp;&nbsp;
		<input type="submit" name="muestra" value='Muestra' >
	    </b>
	    </td>
       
     </tr>
</table>

<table border=1 width=150% cellspacing=5 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=15 align=left id="ma">
     <table width=150%>
      <tr id="ma">
       <!-- <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td> -->
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
    <td align=right id="mo"><a id="mo">CUIE</a></td>      	
    <td align=right id="mo"><a id="mo">Nombre</a></td>
    <td align=right id="mo"><a id="mo">Cuidad</a></td>        
    <td align=right colspan=2 id="mo"><a id="mo">CEB entre 0 y 5 a&ntilde;os </a></td>  
	<td align=right colspan=2 id="mo"><a id="mo">CEB entre 6 y 9 a&ntilde;os </a></td>
	<td align=right colspan=2 id="mo"><a id="mo">CEB entre 10 y 19 a&ntilde;os </a></td>
	<td align=right colspan=2 id="mo"><a id="mo">CEB entre 20 y 64 a&ntilde;os </a></td>	
    <td align=right colspan=2 id="mo"><a id="mo">Total de Controles de Ni&ntilde;os menor de 1 a&ntilde;o </a></td>
    <td align=right colspan=2 id="mo"><a id="mo">Total de Controles de Ni&ntilde;os de 1 a 9 a&ntilde;os</a></td>
	<td align=right colspan=2 id="mo"><a id="mo">Meta de presentacion de facturacion</a></td>	
	<td align=right colspan=2 id="mo"><a id="mo">Total de Embarazadas antes de las 12 semanas </a></td>	
    <td align=right colspan=2 id="mo"><a id="mo">Total de Controles de Embarazo segun periodo </a></td>
    <td align=right colspan=2 id="mo"><a id="mo">Total de Adolescentes de 10 a 19 a&ntilde;os </a></td>
	<td align=right colspan=2 id="mo"><a id="mo">Total de Inscriptos que Marca Cuidado Sexual y Reproductivo </a></td>        
    <td align=right colspan=2 id="mo"><a id="mo">Total de Controles que Marca Diabetico</a></td>
    <td align=right colspan=2 id="mo"><a id="mo">Total de Controles que Marca Hipertenso</a></td>  
    <td align=right colspan=2 id="mo"><a id="mo">Total de Vacunas Doble Viral</a></td>
	<td align=right colspan=2 id="mo"><a id="mo">Total de Vacunas Hepatitis B</a></td>        
    <td align=right colspan=2 id="mo"><a id="mo">Total de Vacunas Neumococo</a></td>
    <td align=right colspan=2 id="mo"><a id="mo">Total de Vacunas Pentavalentes</a></td>
	<td align=right colspan=2 id="mo"><a id="mo">Total de Vacunas cuadruples </a></td>        
    <td align=right colspan=2 id="mo"><a id="mo">Total de Vacunas Sabin </a></td>
    <td align=right colspan=2 id="mo"><a id="mo">Total de Vacunas Triple Viral</a></td>  
    <td align=right colspan=2 id="mo"><a id="mo">Total de Vacunas Anti-Gripales</a></td>        
    <td align=right colspan=2 id="mo"><a id="mo">Total de Vacunas Hepatitis A</a></td>
    <td align=right colspan=2 id="mo"><a id="mo">Total de Vacunas Tri.Bac.Celular</a></td>
	<td align=right colspan=2 id="mo"><a id="mo">Total de Vacunas Tri.Bac.Acelular</a></td>        
    <td align=right colspan=2 id="mo"><a id="mo">Total de Vacunas Doble Bacteriana</a></td>
    <td align=right colspan=2 id="mo"><a id="mo">Total de Vacunas VPH</a></td>  
    </tr>
	
	<tr>
    <td></td>      	
    <td></td>
    <td></td>        
    <td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>	
	<td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>
	<td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>
	<td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>
    <td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>
    <td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>
	<td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>	
	<td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>	
	<td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>
    <td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>
	<td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>        
    <td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>
    <td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>  
    <td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>
	<td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>       
    <td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>
    <td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>
	<td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>       
    <td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>
    <td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>  
    <td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>       
    <td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>
    <td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>
	<td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>        
    <td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td>
    <td align=right id="mo"><a id="mo">Dato</a></td>
	<td align=right id="mo"><a id="mo">Meta</a></td> 
    </tr>
 <? if ($_POST['muestra']=="Muestra"){
   
   
	$sql="select id_efe_conv,cuie,nombre,cuidad from nacer.efe_conv where conv_sumar='t'";
	$result = sql($sql) or die;
	
	$fecha_desde=fecha_db($_POST['fecha_desde']);
	$fecha_hasta=fecha_db($_POST['fecha_hasta']);
   
	
   
   while (!$result->EOF) {
  	$ref = encode_link("detalle_cumplimiento.php",array("id_efe_conv"=>$result->fields['id_efe_conv'],"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
    $onclick_elegir="location.href='$ref'";
    $cuie=$result->fields['cuie'];?>
    <?
	
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
		facturacion.comprobante.fecha_comprobante between '$fecha_desde' and '$fecha_hasta'
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
		(fecha_control between '$fecha_desde' and '$fecha_hasta')

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
		(trazadora_7.fecha_control between '$fecha_desde' and '$fecha_hasta')
		
		union --beneficiarios en trazadoras.nino_new

		SELECT distinct (num_doc::numeric(10,0))::text as dni,nombre,apellido,fecha_nac,fecha_control from trazadoras.nino_new 
		where 
		cuie = '$cuie' and 
		(fecha_control - fecha_nac >= 366 and fecha_control - fecha_nac < 3285) and (fecha_control between '$fecha_desde' and '$fecha_hasta')
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
						(fecha_control between '$fecha_desde' and '$fecha_hasta')
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
		(fecha_control between '$fecha_desde' and '$fecha_hasta')
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
			(trazadora_10.fecha_control between '$fecha_desde' and '$fecha_hasta')
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
				(fecha_control between '$fecha_desde' and '$fecha_hasta' and fichero.fichero.salud_rep = 'SI')
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
				(fecha_control between '$fecha_desde' and '$fecha_hasta') and fichero.salud_rep = 'SI') as a";
			
			$result_ssr1=sql($sql_cuidado_sexual) or fin_pagina();
			$cuidado_sexual=$result_ssr1->fields['total'];
		 
		    //dia e hip
		   $sql_dia="SELECT count(*) as total from (
						SELECT distinct on (afidni,fecha_control)
          afidni,afinombre,afiapellido,afifechanac,fecha_control,estado from (
          SELECT distinct nacer.smiafiliados.afidni,
          nacer.smiafiliados.afinombre,
          nacer.smiafiliados.afiapellido,
          nacer.smiafiliados.afifechanac,
          fichero.fichero.fecha_control,
          'na' as estado
          from fichero.fichero
          inner join nacer.smiafiliados on fichero.fichero.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
          where cuie='$cuie' and fichero.fecha_control between '$fecha_desde' and '$fecha_hasta' and diabetico='SI'
union
          select distinct leche.beneficiarios.documento,
          leche.beneficiarios.nombre,
          leche.beneficiarios.apellido,
          leche.beneficiarios.fecha_nac,
          fichero.fichero.fecha_control,
          'nu' as estado
          from fichero.fichero
          inner join leche.beneficiarios on fichero.fichero.id_beneficiarios=leche.beneficiarios.id_beneficiarios
          where cuie='$cuie' and 
          fichero.fecha_control between '$fecha_desde' and '$fecha_hasta' and diabetico='SI'
union

    SELECT distinct clasificacion_remediar2.num_doc,
    clasificacion_remediar2.nombre,
    clasificacion_remediar2.apellido,
    clasificacion_remediar2.fecha_nac,
    clasificacion_remediar2.fecha_control,
    'desde clasificacion' as estado
    from trazadoras.clasificacion_remediar2
    where cuie = '$cuie' and fecha_control between '$fecha_desde' and '$fecha_hasta' and diabetico = 'SI') as ccc	) as cantidad";
			
			$result_dia=sql($sql_dia) or fin_pagina();


			$dia=$result_dia->fields['total'] ;
				
			
										
			$sql_hip="SELECT count(*) as total from (
				SELECT distinct on (afidni,fecha_control)
          afidni,afinombre,afiapellido,afifechanac,fecha_control,estado from (
SELECT distinct nacer.smiafiliados.afidni,
          nacer.smiafiliados.afinombre,
          nacer.smiafiliados.afiapellido,
          nacer.smiafiliados.afifechanac,
          fichero.fichero.fecha_control,
          'na' as estado
          from fichero.fichero
          inner join nacer.smiafiliados on fichero.fichero.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
          where cuie='$cuie' and fichero.fecha_control between '$fecha_desde' and '$fecha_hasta' and hipertenso='SI'
union
          select distinct leche.beneficiarios.documento,
          leche.beneficiarios.nombre,
          leche.beneficiarios.apellido,
          leche.beneficiarios.fecha_nac,
          fichero.fichero.fecha_control,
          'nu' as estado
          from fichero.fichero
          inner join leche.beneficiarios on fichero.fichero.id_beneficiarios=leche.beneficiarios.id_beneficiarios
          where cuie='$cuie' and 
          fichero.fecha_control between '$fecha_desde' and '$fecha_hasta' and hipertenso='SI'
union

    SELECT distinct clasificacion_remediar2.num_doc,
    clasificacion_remediar2.nombre,
    clasificacion_remediar2.apellido,
    clasificacion_remediar2.fecha_nac,
    clasificacion_remediar2.fecha_control,
    'desde clasificacion' as estado
    from trazadoras.clasificacion_remediar2
    where cuie = '$cuie' and fecha_control between '$fecha_desde' and '$fecha_hasta' and hipertenso = 'SI') as ccc
				) as cantidad";
				
			$res_hip= sql($sql_hip) or die;
			$hip=$res_hip->fields['total'];

	//vacunas
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
		
			
	/*CONSULTAS PARA LAS METAS*/
			
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
	?>
	
	
			<tr <?=atrib_tr()?>>        
				<td align=center onclick="<?=$onclick_elegir?>"><?=$result->fields['cuie']?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=$result->fields['cuidad']?></td>       
				<td align=center onclick="<?=$onclick_elegir?>"><?=($ceb_a)?$ceb_a:0?></td> 
				<td align=center onclick="<?=$onclick_elegir?>"><?=($ceb_ceroacinco)?$ceb_ceroacinco:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($ceb_b)?$ceb_b:0?></td>
			<td align=center onclick="<?=$onclick_elegir?>"><?=($ceb_seisanueve)?$ceb_seisanueve:0?></td>	
				<td align=center onclick="<?=$onclick_elegir?>"><?=($ceb_c)?$ceb_c:0?></td> 
				<td align=center onclick="<?=$onclick_elegir?>"><?=($ceb_diezadiecinueve)?$ceb_diezadiecinueve:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($ceb_d)?$ceb_d:0?></td> 
			<td align=center onclick="<?=$onclick_elegir?>"><?=($ceb_veinteasesentaycuatro)?$ceb_veinteasesentaycuatro:0?></td>	
				<td align=center onclick="<?=$onclick_elegir?>"><?=($ninios_new_1)?$ninios_new_1:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($cns_menor_1_año)?$cns_menor_1_año:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($ninios_1_a_9)?$ninios_1_a_9:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($cns_entre_1_y_9)?$cns_entre_1_y_9:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($cant_fact)?$cant_fact:0?></td>
			<td align=center onclick="<?=$onclick_elegir?>">5</td>	
				<td align=center onclick="<?=$onclick_elegir?>"><?=($embarazadas_12_pers)?$embarazadas_12_pers:0?></td>
			<td align=center onclick="<?=$onclick_elegir?>"><?=($captacion_temprana)?$captacion_temprana:0?></td>	
				<td align=center onclick="<?=$onclick_elegir?>"><?=($embarazadas)?$embarazadas:0?></td> 
				<td align=center onclick="<?=$onclick_elegir?>"><?=($promedio_controles_x_emb)?$promedio_controles_x_emb:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($adol_new_pers_3)?$adol_new_pers_3:0?></td> 
			<td align=center onclick="<?=$onclick_elegir?>"><?=($adolecentes)?$adolecentes:0?></td>	
				<td align=center onclick="<?=$onclick_elegir?>"><?=($cuidado_sexual)?$cuidado_sexual:0?></td> 
				<td align=center onclick="<?=$onclick_elegir?>"><?=($mujeres_edad_fertil)?$mujeres_edad_fertil:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($dia)?$dia:0?></td>
			<td align=center onclick="<?=$onclick_elegir?>"><?=($enfermedades_cronicas_DBT)?$enfermedades_cronicas_DBT:0?></td>	
				<td align=center onclick="<?=$onclick_elegir?>"><?=($hip)?$hip:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($enfermedades_cronicas_HTA)?$enfermedades_cronicas_HTA:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($efe_doble_viral)?$efe_doble_viral:0?></td>	
				<td align=center onclick="<?=$onclick_elegir?>"><?=($vacuna_doble_viral)?$vacuna_doble_viral:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($efe_hep_b)?$efe_hep_b:0?></td> 
			<td align=center onclick="<?=$onclick_elegir?>"><?=($vacuna_hep_b)?$vacuna_hep_b:0?></td>	
				<td align=center onclick="<?=$onclick_elegir?>"><?=($efe_neumococo)?$efe_neumococo:0?></td> 
				<td align=center onclick="<?=$onclick_elegir?>"><?=($vacuna_neumococo)?$vacuna_neumococo:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($efe_pentavalente)?$efe_pentavalente:0?></td>
			<td align=center onclick="<?=$onclick_elegir?>"><?=($vacuna_pentavalente)?$vacuna_pentavalente:0?></td>	
				<td align=center onclick="<?=$onclick_elegir?>"><?=($efe_cuadruple)?$efe_cuadruple:0?></td> 
				<td align=center onclick="<?=$onclick_elegir?>"><?=($vacuna_cuadruple)?$vacuna_cuadruple:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($efe_sabin)?$efe_sabin:0?></td> 
			<td align=center onclick="<?=$onclick_elegir?>"><?=($vacuna_sabin)?$vacuna_sabin:0?></td>	
				<td align=center onclick="<?=$onclick_elegir?>"><?=($efe_triple_viral)?$efe_triple_viral:0?></td> 
				<td align=center onclick="<?=$onclick_elegir?>"><?=($vacuna_triple_viral)?></td>
				<? $efe_gripe=$efe_gripe_1+$efe_gripe_2;?>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($efe_gripe)?$efe_gripe:0?></td> 
			<td align=center onclick="<?=$onclick_elegir?>"><?=($vacuna_gripe)?></td>	
				<td align=center onclick="<?=$onclick_elegir?>"><?=($efe_hep_a)?$efe_hep_a:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($vacuna_hep_a)?$vacuna_hep_a:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($efe_triple_bacteriana_celular)?$efe_triple_bacteriana_celular:0?></td> 
				<td align=center onclick="<?=$onclick_elegir?>"><?=($vacuna_triple_bacteriana_celular)?$vacuna_triple_bacteriana_celular:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($efe_triple_bacteriana_acelular)?$efe_triple_bacteriana_acelular:0?></td>  
				<td align=center onclick="<?=$onclick_elegir?>"><?=($vacuna_triple_bacteriana_acelular)?$vacuna_triple_bacteriana_acelular:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($efe_doble_bacteriana)?$efe_doble_bacteriana:0?></td> 
				<td align=center onclick="<?=$onclick_elegir?>"><?=($vacuna_doble_bacteriana)?$vacuna_doble_bacteriana:0?></td>
				<td align=center onclick="<?=$onclick_elegir?>"><?=($efe_hpv)?$efe_hpv:0?></td> 
			<td align=center onclick="<?=$onclick_elegir?>"><?=($vacuna_vph)?$vacuna_vph:0?></td>	
    </tr>
	<?$result->MoveNext(); 
		}    
    }?>    
    <tr>
  	<td colspan=15 align=left id=ma>
     <table width=100%>
      
    </table>
   </td>
  </tr>
 </table>  
   <tr>
  	<td colspan=15 align=left id=ma>
     <table width=100%>
      	
    </table>
   </td>
  </tr>
  
  <table>
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='seguimiento.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
  </table>

</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>