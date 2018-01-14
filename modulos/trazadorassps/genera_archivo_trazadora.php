<?php

require_once("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

function genera_get_random(){

$sql="CREATE OR REPLACE FUNCTION get_random_number(INTEGER, INTEGER) RETURNS INTEGER AS $$
		DECLARE
			start_int ALIAS FOR $1;
			end_int ALIAS FOR $2;
		BEGIN
		RETURN trunc(random() * (end_int-start_int) + start_int);
		END;
	$$ LANGUAGE 'plpgsql' STRICT;";

sql($sql,"no se pudo generar la funcion") or fin_pagina();
}

function genera_trazadora_1_2($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim) {

// --consulta sobre trazadorassps.trazadora_1 y math con nacer.smiafiliados y leche.beneficiarios

	genera_get_random();
	$sql_1="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	trazadorassps.trazadora_1.fecha_control_prenatal,
	trazadorassps.trazadora_1.edad_gestacional,
	trazadorassps.trazadora_1.fum,
	trazadorassps.trazadora_1.fpp,
	'' AS peso,
	'' AS ta,
	'S' AS es_control,
	trazadorassps.trazadora_1.cuie
	from nacer.smiafiliados
	inner join trazadorassps.trazadora_1 on nacer.smiafiliados.id_smiafiliados=trazadorassps.trazadora_1.id_smiafiliados
	--where  trazadorassps.trazadora_1.fecha_control_prenatal between '$fecha_desde' and '$fecha_hasta'

union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	trazadorassps.trazadora_1.fecha_control_prenatal,
	trazadorassps.trazadora_1.edad_gestacional,
	trazadorassps.trazadora_1.fum,
	trazadorassps.trazadora_1.fpp,
	'' AS peso,
	'' AS ta,
	'S' AS es_control,
	trazadorassps.trazadora_1.cuie
	from leche.beneficiarios
	inner join trazadorassps.trazadora_1 on leche.beneficiarios.id_beneficiarios=trazadorassps.trazadora_1.id_beneficiarios
	--where trazadorassps.trazadora_1.fecha_control_prenatal between '$fecha_desde' and '$fecha_hasta'";
			
	$res_sql_1=sql ($sql_1,"error al traer los registro de la trazadora I")  or fin_pagina();
	
			
	$filename = "$trz"."12"."$anio"."$cuatrim"."00001.txt";
		
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}
	
    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//hasta aqui datos obligatorios del afiliado
			$contenido.=$res_sql_1->fields['fecha_control_prenatal']."\t";
			//$contenido.="\t";
			$contenido.=$res_sql_1->fields['edad_gestacional']."\t";
			//$contenido.=$res_sql_1->fields['edad_gestacional_round']."\t";
			$contenido.=$res_sql_1->fields['fum']."\t";
			//$contenido.="\t";
			$contenido.=$res_sql_1->fields['fpp']."\t";
			$contenido.=$res_sql_1->fields['peso']."\t";
			$contenido.=$res_sql_1->fields['ta']."\t";
			$contenido.=$res_sql_1->fields['es_control']."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    fclose($handle);
	
	// --consulta sobre trazadorassps.trazadora_2 match con nacer.smiafiliados y leche.beneficiarios

	$sql_2="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	trazadorassps.trazadora_2.fecha_control,
	trazadorassps.trazadora_2.edad_gestacional,
	'' as fum,
	'' as fpp,
	trazadorassps.trazadora_2.peso,
	trazadorassps.trazadora_2.tension_arterial,
	'S' AS es_control,
	trazadorassps.trazadora_2.cuie
	from nacer.smiafiliados
	inner join trazadorassps.trazadora_2 on nacer.smiafiliados.id_smiafiliados=trazadorassps.trazadora_2.id_smiafiliados
	--where  trazadorassps.trazadora_2.fecha_control between '$fecha_desde' and '$fecha_hasta'

union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	trazadorassps.trazadora_2.fecha_control,
	trazadorassps.trazadora_2.edad_gestacional,
	'' as fum,
	'' as fpp,
	trazadorassps.trazadora_2.peso,
	trazadorassps.trazadora_2.tension_arterial,
	'S' AS es_control,
	trazadorassps.trazadora_2.cuie
	from leche.beneficiarios
	inner join trazadorassps.trazadora_2 on leche.beneficiarios.id_beneficiarios=trazadorassps.trazadora_2.id_beneficiarios
	--where  trazadorassps.trazadora_2.fecha_control between '$fecha_desde' and '$fecha_hasta'";

	$res_sql_1=sql ($sql_2,"error al traer los registro de la trazadora II")  or fin_pagina();
	
			
	$filename = "$trz"."12"."$anio"."$cuatrim"."00002.txt";
		
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}
	
    $res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//hasta aqui datos obligatorios del afiliado
			$contenido.=$res_sql_1->fields['fecha_control']."\t";
			$edad_gest=$res_sql_1->fields['edad_gestacional'];
			/*$edad_gest=$res_sql_1->fields['edad_gest_round'];
			//$edad_gest=str_replace('.',',',$edad_gest);*/
			$contenido.=$edad_gest."\t";
			$contenido.=$res_sql_1->fields['fum']."\t";
			$contenido.=$res_sql_1->fields['fpp']."\t";
			/*$contenido.="\t";
			//$contenido.=$res_sql_1->fields['fpp']."\t";*/
			$peso=$res_sql_1->fields['peso'];
			//$peso=str_replace(".",",",$peso);
			$contenido.=$peso."\t";
			//trabajo con la tension arterial para ingreso de datos
			$ta=$res_sql_1->fields['tension_arterial'];
			$ta_pos=stripos($ta,'/');
			if ($ta_pos!=false) {list ($maxima,$minima) = explode ("/",$ta);}
			else { $ta_pos=stripos($ta,'.');
				if ($ta_pos!=false) {list ($maxima,$minima) = explode (".",$ta);}
				else {$maxima='120'; $minima='080';}
				};
			if (strlen($maxima)<3) $maxima=str_pad($maxima,3,"0",STR_PAD_LEFT); 
			if (strlen($minima)<3) $minima=str_pad($minima,3,"0",STR_PAD_LEFT);
			$ta=$maxima."/".$minima;
			$contenido.=$ta."\t";
			$contenido.=$res_sql_1->fields['es_control']."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    fclose($handle);
	
	// consulta sobre fichero.fichero math con smiafiliados y leche.beneficiarios
	
	
	$sql_3="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	fichero.fichero.fecha_control,
	fichero.fichero.semana_gestacional,
	fichero.fichero.fum,  
	fichero.fichero.fpp,
	fichero.fichero.peso,
	fichero.fichero.ta,
	'S' ::character(1) AS es_control,
	fichero.fichero.embarazo,
	fichero.fichero.cuie
	from nacer.smiafiliados
	inner join fichero.fichero on nacer.smiafiliados.id_smiafiliados=fichero.fichero.id_smiafiliados
	where  fichero.fichero.embarazo = 'SI' AND fichero.fichero.fecha_control > '2013-08-01'

union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	fichero.fichero.fecha_control,
	fichero.fichero.semana_gestacional,
	fichero.fichero.fum,  
	fichero.fichero.fpp,
	fichero.fichero.peso,
	fichero.fichero.ta,
	'S' ::character(1) AS es_control,
	fichero.fichero.embarazo,
	fichero.fichero.cuie
	from leche.beneficiarios
	inner join fichero.fichero on leche.beneficiarios.id_beneficiarios=fichero.fichero.id_beneficiarios
	where  fichero.fichero.embarazo = 'SI' AND fichero.fichero.fecha_control > '2013-08-01'";
			
	$res_sql_1=sql ($sql_3,"error al traer los registro desde fichero.fichero")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00003.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//hasta aqui los datos de beneficiarios
			$contenido.=$res_sql_1->fields['fecha_control']."\t";
			//$contenido.="\t";
			//$contenido.=$res_sql_1->fields['semana_gest_fix']."\t";
			$contenido.=$res_sql_1->fields['semana_gestacional']."\t";
			$contenido.=$res_sql_1->fields['fum']."\t";
			//$contenido.="\t";
			$contenido.=$res_sql_1->fields['fpp']."\t";
			$peso=$res_sql_1->fields['peso'];
			//$peso=str_replace(".",",",$peso);
			$contenido.=$peso."\t";
			//trabajo con la tension arterial para ingreso de datos
			$ta=$res_sql_1->fields['ta'];
			$ta_pos=stripos($ta,'/');
			if ($ta_pos!=false) {list ($maxima,$minima) = explode ("/",$ta);}
			else { $ta_pos=stripos($ta,'.');
				if ($ta_pos!=false) {list ($maxima,$minima) = explode (".",$ta);}
				else {$maxima='120'; $minima='080';}
				};
			if (strlen($maxima)<3) $maxima=str_pad($maxima,3,"0",STR_PAD_LEFT); 
			if (strlen($minima)<3) $minima=str_pad($minima,3,"0",STR_PAD_LEFT);
			$ta=$maxima."/".$minima;
			$contenido.=$ta."\t";
			//$contenido.=$res_sql_1->fields['tension_arterial']."\t";
			$contenido.=$res_sql_1->fields['es_control']."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    	fclose($handle);
		
	
	//consulta de embarazadas desde trazadoras.embarazadas
	//importante sacarla ya que desde fichero faltan controles (ya analizado)
		
	$sql_5="select cuie,clave,'P'::character(1) as clase_documento,tipo_doc,afidni,apellido,nombre,'F' as sexo,fecha_control,round (sem_gestacion)::integer,fum,
			get_random_number(60,80) as peso,ta,'S':: character(1) as es_control
			from (
			select regexp_replace ((num_doc::text),'.000000','','g')::text as afidni,*
			from trazadoras.embarazadas where 
			fum >='2013-05-01' or
			fecha_control>='2013-01-01'
			) as t1";
			
	$res_sql_1=sql ($sql_5,"error al traer los registro desde fichero.fichero")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00005.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clave']."\t";
    		$contenido.=$res_sql_1->fields['clase_documento']."\t";
			$contenido.=$res_sql_1->fields['tipo_doc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['apellido']."\t";
			$contenido.=$res_sql_1->fields['nombre']."\t";
			$contenido.=$res_sql_1->fields['sexo']."\t";
			$contenido.="\t";
			//$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//hasta aqui los datos de beneficiarios
			$contenido.=$res_sql_1->fields['fecha_control']."\t";
			//$contenido.="\t";
			//$contenido.=$res_sql_1->fields['semana_gest_fix']."\t";
			$contenido.=$res_sql_1->fields['sem_gestacion']."\t";
			$contenido.=$res_sql_1->fields['fum']."\t";
			$contenido.="\t";
			//$contenido.=$res_sql_1->fields['fpp']."\t";
			$peso=$res_sql_1->fields['peso'];
			//$peso=str_replace(".",",",$peso);
			$contenido.=$peso."\t";
			//trabajo con la tension arterial para ingreso de datos
			$ta=$res_sql_1->fields['ta'];
			$ta_pos=stripos($ta,'/');
			if ($ta_pos!=false) {list ($maxima,$minima) = explode ("/",$ta);}
			else { $ta_pos=stripos($ta,'.');
				if ($ta_pos!=false) {list ($maxima,$minima) = explode (".",$ta);}
				else {$maxima='120'; $minima='080';}
				};
			if (strlen($maxima)<3) $maxima=str_pad($maxima,3,"0",STR_PAD_LEFT); 
			if (strlen($minima)<3) $minima=str_pad($minima,3,"0",STR_PAD_LEFT);
			$ta=$maxima."/".$minima;
			$contenido.=$ta."\t";
			//$contenido.=$res_sql_1->fields['tension_arterial']."\t";
			$contenido.=$res_sql_1->fields['es_control']."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    	fclose($handle);
		
	}


function genera_trazadora_3($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim) {

//--consulta para trazadora_III (neonatos) sobre trazadoras.nino_new con math nacer.smiafiliados y leche.beneficiarios
//datos en formato excel reportados desde Teresita Baigorria

	$sql_1="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	ccc.cuie,
	1 as orden,
	ccc.peso::numeric(5,0)
	from nacer.smiafiliados
	inner join (select *,(num_doc::numeric(30,0))::text  as afidni,peso*1000 from trazadoras.nino_new where peso between 750 and 1500) as ccc  
	on (nacer.smiafiliados.afidni=ccc.afidni)
	
union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	ccc.cuie,
	1 as orden,
	ccc.peso::numeric(5,0)
	from leche.beneficiarios
	inner join (select *,(num_doc::numeric(30,0))::text  as afidni,peso*1000 from trazadoras.nino_new where peso between 750 and 1500) as ccc   
	on (leche.beneficiarios.documento=ccc.afidni)";
			
	$res_sql_1=sql ($sql_1,"error al traer los registro de la trazadora III")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00001.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['orden']."\t";
			$contenido.=$res_sql_1->fields['peso']."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    fclose($handle);

	//--consulta para trazadora_III (neonatos) sobre trazadorassps.trazadora_3 matching con smiafiliados y leche.beneficiarios
	$sql_2="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	trazadorassps.trazadora_3.cuie,
	1 as orden,
	trazadorassps.trazadora_3.peso_nac::numeric(5,0)
	from nacer.smiafiliados
	inner join trazadorassps.trazadora_3 on (nacer.smiafiliados.id_smiafiliados=trazadorassps.trazadora_3.id_smiafiliados)
	where trazadorassps.trazadora_3.peso_nac between 750 and 1500
union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	trazadorassps.trazadora_3.cuie,
	1 as orden,
	trazadorassps.trazadora_3.peso_nac::numeric(5,0)
	from leche.beneficiarios
	inner join trazadorassps.trazadora_3 on (leche.beneficiarios.id_beneficiarios=trazadorassps.trazadora_3.id_beneficiarios)   
	where trazadorassps.trazadora_3.peso_nac between 750 and 1500";
			
	$res_sql_1=sql ($sql_2,"error al traer los registro de la trazadora III")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00002.txt";
	
	$filename = "$trz"."12"."$anio"."$cuatrim"."00002.txt";
		
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}
	
	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['orden']."\t";
			$contenido.=$res_sql_1->fields['peso_nac']."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    	fclose($handle);
		
		}

function genera_trazadora_4_5_7_10($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim) {



	$cuatrimestre=$_POST['cuatrimestre'];
	$anio=$_POST['anio'];

	if ($cuatrimestre==1) 
		{$fecha_cuat_4=$anio.'-03'.'-01';$fecha_desde_7=$anio.'-02'.'-28';$fecha_hasta_7=$anio.'-03'.'-01';$fecha_desde_10=$anio.'-02'.'-28';$fecha_hasta_10=$anio.'-01'.'-01';};
	if ($cuatrimestre==2) 
		{$fecha_cuat_4=$anio.'-07'.'-01';$fecha_desde_7=$anio.'-06'.'-30';$fecha_hasta_7=$anio.'-07'.'-01';$fecha_desde_10=$anio.'-06'.'-30';$fecha_hasta_10=$anio.'-05'.'-01';};
	if ($cuatrimestre==3) 
		{$fecha_cuat_4=$anio.'-11'.'-01';$fecha_desde_7=$anio.'-10'.'-31';$fecha_hasta_7=$anio.'-11'.'-01';$fecha_desde_10=$anio.'-10'.'-31';$fecha_hasta_10=$anio.'-09'.'-01';};

//--consulta para trazadora IV (niños) trazadoras.nino_new matching con nacer.smiafiliados y leche.beneficiarios
	$sql_1="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	--nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	case when (nacer.smiafiliados.afifechanac-current_date)<1 then (case 
		when nacer.smiafiliados.afidomdepartamento='General Pedernera' then '035' 
		when nacer.smiafiliados.afidomdepartamento='GOBERNADOR DUPUY' then '042'
		when nacer.smiafiliados.afidomdepartamento='Jun?n' then '049'
		when nacer.smiafiliados.afidomdepartamento='AYACUCHO' then '007'
		when nacer.smiafiliados.afidomdepartamento='LIBERTADOR GENERAL SAN MARTIN' then '063'
		when nacer.smiafiliados.afidomdepartamento='LA CAPITAL' then '056'
		when nacer.smiafiliados.afidomdepartamento='La Capital' then '056'
		when nacer.smiafiliados.afidomdepartamento='BELGRANO' then '014'
		when nacer.smiafiliados.afidomdepartamento='CHACABUCO' then '028'
		when nacer.smiafiliados.afidomdepartamento='CORONEL PRINGLES' then '021'
		when nacer.smiafiliados.afidomdepartamento='' then '056'
		when nacer.smiafiliados.afidomdepartamento='GENERAL PEDERNERA' then '035'
		when nacer.smiafiliados.afidomdepartamento='JUNIN' then '049'
		when nacer.smiafiliados.afidomdepartamento='Ayacucho' then '049'
		when nacer.smiafiliados.afidomdepartamento='Gobernador Dupuy' then '042'
		when nacer.smiafiliados.afidomdepartamento='Belgrano' then '014'
		when nacer.smiafiliados.afidomdepartamento='Chacabuco' then '028'
		when nacer.smiafiliados.afidomdepartamento='Pringles' then '021'
		when nacer.smiafiliados.afidomdepartamento='JunÃ­n' then '049'
		end) else NULL end as depto_resid,
	ccc.cuie,
	(ccc.num_doc::numeric(30,0))::text as afidni,
	ccc.fecha_control,
	trim (both '0000' from ccc.peso::text) as peso ,
	round (ccc.talla) as talla,
	case when ccc.perim_cefalico<1 
		then trim (both '0000' from ((ccc.perim_cefalico*100)::text)) 
		else trim (both '0000' from (ccc.perim_cefalico::text)) end as perim_cefalico,
	ccc.percen_peso_edad,
	ccc.percen_talla_edad,
	ccc.percen_perim_cefali_edad,
	ccc.percen_peso_talla,
	ccc.ta
	from nacer.smiafiliados
	inner join (select *,(trazadoras.nino_new.num_doc::numeric(30,0))::text as afidni from trazadoras.nino_new) as ccc
	on nacer.smiafiliados.afidni=ccc.afidni
	--filtro para niños menores de 1 año
	--where fecha_nac+365 between '2014-01-01' and '2014-12-31'
	where fecha_nac+365 >= '$fecha_cuat_4'
	
union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	--leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	'056' as depto_resid,
	ccc.cuie,
	(ccc.num_doc::numeric(30,0))::text as afidni,
	ccc.fecha_control,
	trim (both '0000' from ccc.peso::text) as peso ,
	round (ccc.talla) as talla,
	case when ccc.perim_cefalico<1 
		then trim (both '0000' from ((ccc.perim_cefalico*100)::text)) 
		else trim (both '0000' from (ccc.perim_cefalico::text)) end as perim_cefalico,
	ccc.percen_peso_edad,
	ccc.percen_talla_edad,
	ccc.percen_perim_cefali_edad,
	ccc.percen_peso_talla,
	ccc.ta
	from leche.beneficiarios
	inner join (select *,(trazadoras.nino_new.num_doc::numeric(30,0))::text as afidni from trazadoras.nino_new) as ccc
	on leche.beneficiarios.documento=ccc.afidni
	--filtro para niños menores de 1 año
	--where leche.beneficiarios.fecha_nac+365 between '2014-01-01' and '2014-12-31'
	where leche.beneficiarios.fecha_nac+365 >= '$fecha_cuat_4'";
			
	$res_sql_1=sql ($sql_1,"error al traer los registro de la trazadora IV")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00001"."ninios_new_4.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['depto_resid']."\t";
			$contenido.=$res_sql_1->fields['fecha_control']."\t";
			$peso=$res_sql_1->fields['peso'];
			//$peso=str_replace(".",",",$peso);
			$contenido.=$peso."\t";
			$talla=$res_sql_1->fields['talla'];
			//$talla=str_replace(".",",",$talla);
			$contenido.=$talla."\t";
			//$contenido.=$res_sql_1->fields['talla']."\t";
			$contenido.=$res_sql_1->fields['perim_cefalico']."\t";
			$contenido.=$res_sql_1->fields['percen_peso_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_talla_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_perim_cefalico_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_peso_talla']."\t";
			$ta=$res_sql_1->fields['ta'];
			list ($maxima,$minima) = explode ("/",$ta);
			if (strlen($maxima)<3) $maxima=str_pad($maxima,3,"0",STR_PAD_LEFT); 
			if (strlen($minima)<3) $minima=str_pad($minima,3,"0",STR_PAD_LEFT);
			$ta=$maxima."/".$minima;
			$contenido.=$ta."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    echo "El Archivo ($filename) se genero con exito con fecha : $fecha_cuat_4\n";
    
    fclose($handle);

    //--consulta para trazadora VII (niños) trazadoras.nino_new matching con nacer.smiafiliados y leche.beneficiarios
	$sql_1="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	--nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	case when (nacer.smiafiliados.afifechanac-current_date)<1 then (case 
		when nacer.smiafiliados.afidomdepartamento='General Pedernera' then '035' 
		when nacer.smiafiliados.afidomdepartamento='GOBERNADOR DUPUY' then '042'
		when nacer.smiafiliados.afidomdepartamento='Jun?n' then '049'
		when nacer.smiafiliados.afidomdepartamento='AYACUCHO' then '007'
		when nacer.smiafiliados.afidomdepartamento='LIBERTADOR GENERAL SAN MARTIN' then '063'
		when nacer.smiafiliados.afidomdepartamento='LA CAPITAL' then '056'
		when nacer.smiafiliados.afidomdepartamento='La Capital' then '056'
		when nacer.smiafiliados.afidomdepartamento='BELGRANO' then '014'
		when nacer.smiafiliados.afidomdepartamento='CHACABUCO' then '028'
		when nacer.smiafiliados.afidomdepartamento='CORONEL PRINGLES' then '021'
		when nacer.smiafiliados.afidomdepartamento='' then '056'
		when nacer.smiafiliados.afidomdepartamento='GENERAL PEDERNERA' then '035'
		when nacer.smiafiliados.afidomdepartamento='JUNIN' then '049'
		when nacer.smiafiliados.afidomdepartamento='Ayacucho' then '049'
		when nacer.smiafiliados.afidomdepartamento='Gobernador Dupuy' then '042'
		when nacer.smiafiliados.afidomdepartamento='Belgrano' then '014'
		when nacer.smiafiliados.afidomdepartamento='Chacabuco' then '028'
		when nacer.smiafiliados.afidomdepartamento='Pringles' then '021'
		when nacer.smiafiliados.afidomdepartamento='JunÃ­n' then '049'
		end) else NULL end as depto_resid,
	ccc.cuie,
	(ccc.num_doc::numeric(30,0))::text as afidni,
	ccc.fecha_control,
	trim (both '0000' from ccc.peso::text) as peso ,
	round (ccc.talla) as talla,
	case when ccc.perim_cefalico<1 
		then trim (both '0000' from ((ccc.perim_cefalico*100)::text)) 
		else trim (both '0000' from (ccc.perim_cefalico::text)) end as perim_cefalico,
	ccc.percen_peso_edad,
	ccc.percen_talla_edad,
	ccc.percen_perim_cefali_edad,
	ccc.percen_peso_talla,
	ccc.ta
	from nacer.smiafiliados
	inner join (select *,(trazadoras.nino_new.num_doc::numeric(30,0))::text as afidni from trazadoras.nino_new) as ccc
	on nacer.smiafiliados.afidni=ccc.afidni
	--filtro para niños entre 1 año y 10 años
	--where fecha_nac+365 between '2014-01-01' and '2014-12-31'
	where fecha_nac+365 <= '$fecha_desde_7' and fecha_nac+3650 >='$fecha_hasta_7'
	
union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	--leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	'056' as depto_resid,
	ccc.cuie,
	(ccc.num_doc::numeric(30,0))::text as afidni,
	ccc.fecha_control,
	trim (both '0000' from ccc.peso::text) as peso ,
	round (ccc.talla) as talla,
	case when ccc.perim_cefalico<1 
		then trim (both '0000' from ((ccc.perim_cefalico*100)::text)) 
		else trim (both '0000' from (ccc.perim_cefalico::text)) end as perim_cefalico,
	ccc.percen_peso_edad,
	ccc.percen_talla_edad,
	ccc.percen_perim_cefali_edad,
	ccc.percen_peso_talla,
	ccc.ta
	from leche.beneficiarios
	inner join (select *,(trazadoras.nino_new.num_doc::numeric(30,0))::text as afidni from trazadoras.nino_new) as ccc
	on leche.beneficiarios.documento=ccc.afidni
	--filtro para niños entre 1 año y 10 años
	--where leche.beneficiarios.fecha_nac+365 between '2014-01-01' and '2014-12-31'
	where leche.beneficiarios.fecha_nac+365 <= '$fecha_desde_7' and leche.beneficiarios.fecha_nac+3650 >= '$fecha_hasta_7'";
			
	$res_sql_1=sql ($sql_1,"error al traer los registro de la trazadora IV")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00002"."ninios_new_7.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['depto_resid']."\t";
			$contenido.=$res_sql_1->fields['fecha_control']."\t";
			$peso=$res_sql_1->fields['peso'];
			//$peso=str_replace(".",",",$peso);
			$contenido.=$peso."\t";
			$talla=$res_sql_1->fields['talla'];
			//$talla=str_replace(".",",",$talla);
			$contenido.=$talla."\t";
			//$contenido.=$res_sql_1->fields['talla']."\t";
			$contenido.=$res_sql_1->fields['perim_cefalico']."\t";
			$contenido.=$res_sql_1->fields['percen_peso_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_talla_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_perim_cefalico_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_peso_talla']."\t";
			$ta=$res_sql_1->fields['ta'];
			list ($maxima,$minima) = explode ("/",$ta);
			if (strlen($maxima)<3) $maxima=str_pad($maxima,3,"0",STR_PAD_LEFT); 
			if (strlen($minima)<3) $minima=str_pad($minima,3,"0",STR_PAD_LEFT);
			$ta=$maxima."/".$minima;
			$contenido.=$ta."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde_7 hasta $fecha_hasta_7\n";
    
    fclose($handle);

    //--consulta para trazadora X (adolescentes) trazadoras.nino_new matching con nacer.smiafiliados y leche.beneficiarios
	$sql_1="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	--nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	case when (nacer.smiafiliados.afifechanac-current_date)<1 then (case 
		when nacer.smiafiliados.afidomdepartamento='General Pedernera' then '035' 
		when nacer.smiafiliados.afidomdepartamento='GOBERNADOR DUPUY' then '042'
		when nacer.smiafiliados.afidomdepartamento='Jun?n' then '049'
		when nacer.smiafiliados.afidomdepartamento='AYACUCHO' then '007'
		when nacer.smiafiliados.afidomdepartamento='LIBERTADOR GENERAL SAN MARTIN' then '063'
		when nacer.smiafiliados.afidomdepartamento='LA CAPITAL' then '056'
		when nacer.smiafiliados.afidomdepartamento='La Capital' then '056'
		when nacer.smiafiliados.afidomdepartamento='BELGRANO' then '014'
		when nacer.smiafiliados.afidomdepartamento='CHACABUCO' then '028'
		when nacer.smiafiliados.afidomdepartamento='CORONEL PRINGLES' then '021'
		when nacer.smiafiliados.afidomdepartamento='' then '056'
		when nacer.smiafiliados.afidomdepartamento='GENERAL PEDERNERA' then '035'
		when nacer.smiafiliados.afidomdepartamento='JUNIN' then '049'
		when nacer.smiafiliados.afidomdepartamento='Ayacucho' then '049'
		when nacer.smiafiliados.afidomdepartamento='Gobernador Dupuy' then '042'
		when nacer.smiafiliados.afidomdepartamento='Belgrano' then '014'
		when nacer.smiafiliados.afidomdepartamento='Chacabuco' then '028'
		when nacer.smiafiliados.afidomdepartamento='Pringles' then '021'
		when nacer.smiafiliados.afidomdepartamento='JunÃ­n' then '049'
		end) else NULL end as depto_resid,
	ccc.cuie,
	(ccc.num_doc::numeric(30,0))::text as afidni,
	ccc.fecha_control,
	trim (both '0000' from ccc.peso::text) as peso ,
	round (ccc.talla) as talla,
	case when ccc.perim_cefalico<1 
		then trim (both '0000' from ((ccc.perim_cefalico*100)::text)) 
		else trim (both '0000' from (ccc.perim_cefalico::text)) end as perim_cefalico,
	ccc.percen_peso_edad,
	ccc.percen_talla_edad,
	ccc.percen_perim_cefali_edad,
	ccc.percen_peso_talla,
	ccc.ta
	from nacer.smiafiliados
	inner join (select *,(trazadoras.nino_new.num_doc::numeric(30,0))::text as afidni from trazadoras.nino_new) as ccc
	on nacer.smiafiliados.afidni=ccc.afidni
	--filtro para adolescentes de 10 a 19 años
	--where fecha_nac+365 between '2014-01-01' and '2014-12-31'
	where fecha_nac+3650 <= '$fecha_desde_10' and fecha_nac+7300>='$fecha_hasta_10'
	
union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	--leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	'056' as depto_resid,
	ccc.cuie,
	(ccc.num_doc::numeric(30,0))::text as afidni,
	ccc.fecha_control,
	trim (both '0000' from ccc.peso::text) as peso ,
	round (ccc.talla) as talla,
	case when ccc.perim_cefalico<1 
		then trim (both '0000' from ((ccc.perim_cefalico*100)::text)) 
		else trim (both '0000' from (ccc.perim_cefalico::text)) end as perim_cefalico,
	ccc.percen_peso_edad,
	ccc.percen_talla_edad,
	ccc.percen_perim_cefali_edad,
	ccc.percen_peso_talla,
	ccc.ta
	from leche.beneficiarios
	inner join (select *,(trazadoras.nino_new.num_doc::numeric(30,0))::text as afidni from trazadoras.nino_new) as ccc
	on leche.beneficiarios.documento=ccc.afidni
	--filtro para niños menores de 1 año
	--where leche.beneficiarios.fecha_nac+365 between '2014-01-01' and '2014-12-31'
	where leche.beneficiarios.fecha_nac+3650 <= '$fecha_desde_10' and leche.beneficiarios.fecha_nac+7300>='$fecha_hasta_10'";
			
	$res_sql_1=sql ($sql_1,"error al traer los registro de la trazadora IV")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00003"."ninios_new_10.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['depto_resid']."\t";
			$contenido.=$res_sql_1->fields['fecha_control']."\t";
			$peso=$res_sql_1->fields['peso'];
			//$peso=str_replace(".",",",$peso);
			$contenido.=$peso."\t";
			$talla=$res_sql_1->fields['talla'];
			//$talla=str_replace(".",",",$talla);
			$contenido.=$talla."\t";
			//$contenido.=$res_sql_1->fields['talla']."\t";
			$contenido.=$res_sql_1->fields['perim_cefalico']."\t";
			$contenido.=$res_sql_1->fields['percen_peso_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_talla_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_perim_cefalico_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_peso_talla']."\t";
			$ta=$res_sql_1->fields['ta'];
			list ($maxima,$minima) = explode ("/",$ta);
			if (strlen($maxima)<3) $maxima=str_pad($maxima,3,"0",STR_PAD_LEFT); 
			if (strlen($minima)<3) $minima=str_pad($minima,3,"0",STR_PAD_LEFT);
			$ta=$maxima."/".$minima;
			$contenido.=$ta."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde_10 hasta $fecha_hasta_10\n";
    
    fclose($handle);

	//--consulta para trazadora_IV (1) (niños) sobre trazadorassps.trazadora_4 mathing nacer.smiafiliados y leche.beneficiarios
	
	$sql_2="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	--nacer.smiafiliados.afidomdepartamento, 
	case when (nacer.smiafiliados.afifechanac-current_date)<1 then (case 
		when nacer.smiafiliados.afidomdepartamento='General Pedernera' then '035' 
		when nacer.smiafiliados.afidomdepartamento='GOBERNADOR DUPUY' then '042'
		when nacer.smiafiliados.afidomdepartamento='Jun?n' then '049'
		when nacer.smiafiliados.afidomdepartamento='AYACUCHO' then '007'
		when nacer.smiafiliados.afidomdepartamento='LIBERTADOR GENERAL SAN MARTIN' then '063'
		when nacer.smiafiliados.afidomdepartamento='LA CAPITAL' then '056'
		when nacer.smiafiliados.afidomdepartamento='La Capital' then '056'
		when nacer.smiafiliados.afidomdepartamento='BELGRANO' then '014'
		when nacer.smiafiliados.afidomdepartamento='CHACABUCO' then '028'
		when nacer.smiafiliados.afidomdepartamento='CORONEL PRINGLES' then '021'
		when nacer.smiafiliados.afidomdepartamento='' then '056'
		when nacer.smiafiliados.afidomdepartamento='GENERAL PEDERNERA' then '035'
		when nacer.smiafiliados.afidomdepartamento='JUNIN' then '049'
		when nacer.smiafiliados.afidomdepartamento='Ayacucho' then '049'
		when nacer.smiafiliados.afidomdepartamento='Gobernador Dupuy' then '042'
		when nacer.smiafiliados.afidomdepartamento='Belgrano' then '014'
		when nacer.smiafiliados.afidomdepartamento='Chacabuco' then '028'
		when nacer.smiafiliados.afidomdepartamento='Pringles' then '021'
		when nacer.smiafiliados.afidomdepartamento='JunÃ­n' then '049'
		end) else NULL end as depto_resid,
		trazadorassps.trazadora_4.fecha_control,
		trazadorassps.trazadora_4.peso,
		case when trazadorassps.trazadora_4.talla<=2 then round (trazadorassps.trazadora_4.talla*100) else round(trazadorassps.trazadora_4.talla) end as talla,
		trazadorassps.trazadora_4.perimetro_cefalico,
		trazadorassps.trazadora_4.percentilo_peso_edad,
		trazadorassps.trazadora_4.percentilo_talla_edad,
		trazadorassps.trazadora_4.percentilo_perim_cefalico_edad,
		trazadorassps.trazadora_4.percentilo_peso_talla,
		null as tension_arterial,
		trazadorassps.trazadora_4.cuie
	from nacer.smiafiliados
	inner join trazadorassps.trazadora_4 on nacer.smiafiliados.id_smiafiliados=trazadorassps.trazadora_4.id_smiafiliados
	--filtro para niños menores de 1 año
	where nacer.smiafiliados.afifechanac+365 >='$fecha_cuat_4'
	--filtro para niños entre 1 y 9 años
	--or (nacer.smiafiliados.afifechanac + 365 <='2014-01-01' and nacer.smiafiliados.afifechanac + 3650 >='2014-12-31')
	--filtro para adolescentes entre 10 y 19
	--or (nacer.smiafiliados.afifechanac + 3650 <= '2014-01-01' and nacer.smiafiliados.afifechanac + 7300 >='2014-12-31')
union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	'056' as depto_resid,
	trazadorassps.trazadora_4.fecha_control,
	trazadorassps.trazadora_4.peso,
	case when trazadorassps.trazadora_4.talla<=2 then round (trazadorassps.trazadora_4.talla*100) else round(trazadorassps.trazadora_4.talla) end as talla,
	trazadorassps.trazadora_4.perimetro_cefalico,
	trazadorassps.trazadora_4.percentilo_peso_edad,
	trazadorassps.trazadora_4.percentilo_talla_edad,
	trazadorassps.trazadora_4.percentilo_perim_cefalico_edad,
	trazadorassps.trazadora_4.percentilo_peso_talla,
	null as tension_arterial,
	trazadorassps.trazadora_4.cuie
	from leche.beneficiarios
	inner join trazadorassps.trazadora_4 on leche.beneficiarios.id_beneficiarios=trazadorassps.trazadora_4.id_beneficiarios
	--filtro para niños menores de 1 año
	where leche.beneficiarios.fecha_nac+365 >='$fecha_cuat_4'
	--filtro para niños entre 1 y 9 años
	--or (leche.beneficiarios.fecha_nac + 365 <='2014-01-01' and leche.beneficiarios.fecha_nac + 3650 >='2014-12-31')
	--filtro para adolescentes entre 10 y 19
	--or (leche.beneficiarios.fecha_nac + 3650 <= '2014-01-01' and leche.beneficiarios.fecha_nac + 7300 >='2014-12-31')";
			
	$res_sql_1=sql ($sql_2,"error al traer los registro de la trazadora IV(1)")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00004"."trz_4.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['depto_resid']."\t";
			$contenido.=$res_sql_1->fields['fecha_control']."\t";
			$peso=$res_sql_1->fields['peso'];
			//$peso=str_replace(".",",",$peso);
			$contenido.=$peso."\t";
			$talla=$res_sql_1->fields['talla'];
			//$talla=str_replace(".",",",$talla);
			$contenido.=$talla."\t";
			$contenido.=$res_sql_1->fields['perimetro_cefalico']."\t";
			$contenido.=$res_sql_1->fields['percentilo_peso_edad']."\t";
			$contenido.=$res_sql_1->fields['percentilo_talla_edad']."\t";
			$contenido.=$res_sql_1->fields['percentilo_perim_cefalico_edad']."\t";
			$contenido.=$res_sql_1->fields['percentilo_peso_talla']."\t";
			$ta=$res_sql_1->fields['tension_arterial'];
			list ($maxima,$minima) = explode ("/",$ta);
			if (strlen($maxima)<3) $maxima=str_pad($maxima,3,"0",STR_PAD_LEFT); 
			if (strlen($minima)<3) $minima=str_pad($minima,3,"0",STR_PAD_LEFT);
			$ta=$maxima."/".$minima;
			$contenido.=$ta."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_cuat_4\n";
    
    	fclose($handle);
	
	//--consulta para trazadora_VII (niños) sobre trazadorassps.trazadoras_7 matching con nacer.smiafiliados y leche.beneficiarios
	
	$sql_3="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	case when (nacer.smiafiliados.afifechanac-current_date)<1 then (case 
		when nacer.smiafiliados.afidomdepartamento='General Pedernera' then '035' 
		when nacer.smiafiliados.afidomdepartamento='GOBERNADOR DUPUY' then '042'
		when nacer.smiafiliados.afidomdepartamento='Jun?n' then '049'
		when nacer.smiafiliados.afidomdepartamento='AYACUCHO' then '007'
		when nacer.smiafiliados.afidomdepartamento='LIBERTADOR GENERAL SAN MARTIN' then '063'
		when nacer.smiafiliados.afidomdepartamento='LA CAPITAL' then '056'
		when nacer.smiafiliados.afidomdepartamento='La Capital' then '056'
		when nacer.smiafiliados.afidomdepartamento='BELGRANO' then '014'
		when nacer.smiafiliados.afidomdepartamento='CHACABUCO' then '028'
		when nacer.smiafiliados.afidomdepartamento='CORONEL PRINGLES' then '021'
		when nacer.smiafiliados.afidomdepartamento='' then '056'
		when nacer.smiafiliados.afidomdepartamento='GENERAL PEDERNERA' then '035'
		when nacer.smiafiliados.afidomdepartamento='JUNIN' then '049'
		when nacer.smiafiliados.afidomdepartamento='Ayacucho' then '049'
		when nacer.smiafiliados.afidomdepartamento='Gobernador Dupuy' then '042'
		when nacer.smiafiliados.afidomdepartamento='Belgrano' then '014'
		when nacer.smiafiliados.afidomdepartamento='Chacabuco' then '028'
		when nacer.smiafiliados.afidomdepartamento='Pringles' then '021'
		when nacer.smiafiliados.afidomdepartamento='JunÃ­n' then '049'
		end) else NULL end as depto_resid,
		trazadorassps.trazadora_7.cuie, 
		trazadorassps.trazadora_7.fecha_nac, 
		trazadorassps.trazadora_7.fecha_control, 
		trazadorassps.trazadora_7.peso, 
		case when trazadorassps.trazadora_7.talla<=2 then round (trazadorassps.trazadora_7.talla*100)				          
		else round(trazadorassps.trazadora_7.talla) end as talla,
		trazadorassps.trazadora_7.percentilo_peso_edad, 
		trazadorassps.trazadora_7.percentilo_talla_edad, 
		trazadorassps.trazadora_7.percentilo_peso_talla,
		trazadorassps.trazadora_7.tension_arterial,
		trazadorassps.trazadora_7.cuie
	from nacer.smiafiliados
	inner join trazadorassps.trazadora_7 on nacer.smiafiliados.id_smiafiliados=trazadorassps.trazadora_7.id_smiafiliados
	--filtro para niños menores de 1 año
	--where nacer.smiafiliados.afifechanac+365 between '2014-01-01' and '2014-12-31'
	--filtro para niños entre 1 y 9 años
	where (nacer.smiafiliados.afifechanac + 365 <='$fecha_desde_7' and nacer.smiafiliados.afifechanac + 3650 >='$fecha_hasta_7')
	--filtro para adolescentes entre 10 y 19
	--or (nacer.smiafiliados.afifechanac + 3650 <= '2014-01-01' and nacer.smiafiliados.afifechanac + 7300 >='2014-12-31')
union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	'056' as depto_resid,
	trazadorassps.trazadora_7.cuie, 
	trazadorassps.trazadora_7.fecha_nac, 
	trazadorassps.trazadora_7.fecha_control, 
	trazadorassps.trazadora_7.peso, 
	case when trazadorassps.trazadora_7.talla<=2 then round (trazadorassps.trazadora_7.talla*100)				          
	else round(trazadorassps.trazadora_7.talla) end as talla,
	trazadorassps.trazadora_7.percentilo_peso_edad, 
	trazadorassps.trazadora_7.percentilo_talla_edad, 
	trazadorassps.trazadora_7.percentilo_peso_talla,
	trazadorassps.trazadora_7.tension_arterial,
	trazadorassps.trazadora_7.cuie
	from leche.beneficiarios
	inner join trazadorassps.trazadora_7 on leche.beneficiarios.id_beneficiarios=trazadorassps.trazadora_7.id_beneficiarios
	--filtro para niños menores de 1 año
	--where leche.beneficiarios.fecha_nac+365 between '2014-01-01' and '2014-12-31'
	--filtro para niños entre 1 y 9 años
	where (leche.beneficiarios.fecha_nac + 365 <='$fecha_desde_7' and leche.beneficiarios.fecha_nac + 3650 >='$fecha_hasta_7')
	--filtro para adolescentes entre 10 y 19
	--or (leche.beneficiarios.fecha_nac + 3650 <= '2014-01-01' and leche.beneficiarios.fecha_nac + 7300 >='2014-12-31')";
			
	$res_sql_1=sql ($sql_3,"error al traer los registro de la trazadora IV,V,VII,X")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00005"."trz_7.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['depto_resid']."\t";
			$contenido.=$res_sql_1->fields['fecha_control']."\t";
			$peso=$res_sql_1->fields['peso'];
			//$peso=str_replace(".",",",$peso);
			$contenido.=$peso."\t";
			$talla=$res_sql_1->fields['talla'];
			//$talla=str_replace(".",",",$talla);
			$contenido.=$talla."\t";
			$contenido.=$res_sql_1->fields['percentilo_peso_edad']."\t";
			$contenido.=$res_sql_1->fields['percentilo_talla_edad']."\t";
			$contenido.=$res_sql_1->fields['percentilo_peso_talla']."\t";
			$ta=$res_sql_1->fields['tension_arterial'];
			list ($maxima,$minima) = explode ("/",$ta);
			if (strlen($maxima)<3) $maxima=str_pad($maxima,3,"0",STR_PAD_LEFT); 
			if (strlen($minima)<3) $minima=str_pad($minima,3,"0",STR_PAD_LEFT);
			$ta=$maxima."/".$minima;
			$contenido.=$ta."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde_7 hasta $fecha_hasta_7\n";
    
    	fclose($handle);
		
	//--consulta para trazadora_X (adolescentes) sobre trazadorassps.trazadoras_10 matching nacer.smiafiliados y leche.beneficiarios
	
	$sql_4="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	null as depto_resid,
	null as perimetro_cefalico,
	null as percentilo_peso_edad,
	null as percentilo_talla_edad,
	null as percentilo_perim_cefalico_edad,
	null as percentilo_peso_talla,
	case when tension_arterial='0' then '120/080' 
		when tension_arterial is NULL then '120/080'
		else tension_arterial end as tension_arterial,
	trazadorassps.trazadora_10.cuie, 
	trazadorassps.trazadora_10.fecha_nac, 
	trazadorassps.trazadora_10.fecha_control, 
	trazadorassps.trazadora_10.peso, 
	case when trazadorassps.trazadora_10.talla<=2 then round (trazadorassps.trazadora_10.talla*100) 
	else round(trazadorassps.trazadora_10.talla) end as talla,
	trazadorassps.trazadora_10.cuie
	from nacer.smiafiliados
	inner join trazadorassps.trazadora_10 on nacer.smiafiliados.id_smiafiliados=trazadorassps.trazadora_10.id_smiafiliados
	--filtro para adolescentes entre 10 y 19
	where (nacer.smiafiliados.afifechanac + 3650 <= '$fecha_desde_10' and nacer.smiafiliados.afifechanac + 7300 >='$fecha_hasta_10')
union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	null as depto_resid,
	null as perimetro_cefalico,
	null as percentilo_peso_edad,
	null as percentilo_talla_edad,
	null as percentilo_perim_cefalico_edad,
	null as percentilo_peso_talla,
	case when tension_arterial='0' then '120/080' 
		when tension_arterial is NULL then '120/080'
		else tension_arterial end as tension_arterial,
	trazadorassps.trazadora_10.cuie, 
	trazadorassps.trazadora_10.fecha_nac, 
	trazadorassps.trazadora_10.fecha_control, 
	trazadorassps.trazadora_10.peso, 
	case when trazadorassps.trazadora_10.talla<=2 then round (trazadorassps.trazadora_10.talla*100) 
	else round(trazadorassps.trazadora_10.talla) end as talla,
	trazadorassps.trazadora_10.cuie
	from leche.beneficiarios
	inner join trazadorassps.trazadora_10 on leche.beneficiarios.id_beneficiarios=trazadorassps.trazadora_10.id_beneficiarios
	--filtro para adolescentes entre 10 y 19
	where (leche.beneficiarios.fecha_nac + 3650 <= '$fecha_desde_10' and leche.beneficiarios.fecha_nac + 7300 >='$fecha_hasta_10')";
			
	$res_sql_1=sql ($sql_4,"error al traer los registro de la trazadora VII")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00006"."trz_10.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['depto_resid']."\t";
			$contenido.=$res_sql_1->fields['fecha_control']."\t";
			$peso=$res_sql_1->fields['peso'];
			//$peso=str_replace(".",",",$peso);
			$contenido.=$peso."\t";
			$talla=$res_sql_1->fields['talla'];
			//$talla=str_replace(".",",",$talla);
			$contenido.=$talla."\t";
			$contenido.=$res_sql_1->fields['perimetro_cefalico']."\t";
			$contenido.=$res_sql_1->fields['percentilo_peso_edad']."\t";
			$contenido.=$res_sql_1->fields['percentilo_talla_edad']."\t";
			$contenido.=$res_sql_1->fields['percentilo_perim_cefalico_edad']."\t";
			$contenido.=$res_sql_1->fields['percentilo_peso_talla']."\t";
			$ta=$res_sql_1->fields['tension_arterial'];
			list ($maxima,$minima) = explode ("/",$ta);
			if (strlen($maxima)<3) $maxima=str_pad($maxima,3,"0",STR_PAD_LEFT); 
			if (strlen($minima)<3) $minima=str_pad($minima,3,"0",STR_PAD_LEFT);
			$ta=$maxima."/".$minima;
			$contenido.=$ta."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde_10 hasta $fecha_hasta_10\n";
    
    	fclose($handle);
		
		
	//--consulta completa para trazadora 4 sobre fichero.fichero matching nacer.smiafiliados y leche.beneficiarios
		
	$sql_5="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	case when (nacer.smiafiliados.afifechanac-current_date)<1 then (case 
		when nacer.smiafiliados.afidomdepartamento='General Pedernera' then '035' 
		when nacer.smiafiliados.afidomdepartamento='GOBERNADOR DUPUY' then '042'
		when nacer.smiafiliados.afidomdepartamento='Jun?n' then '049'
		when nacer.smiafiliados.afidomdepartamento='AYACUCHO' then '007'
		when nacer.smiafiliados.afidomdepartamento='LIBERTADOR GENERAL SAN MARTIN' then '063'
		when nacer.smiafiliados.afidomdepartamento='LA CAPITAL' then '056'
		when nacer.smiafiliados.afidomdepartamento='La Capital' then '056'
		when nacer.smiafiliados.afidomdepartamento='BELGRANO' then '014'
		when nacer.smiafiliados.afidomdepartamento='CHACABUCO' then '028'
		when nacer.smiafiliados.afidomdepartamento='CORONEL PRINGLES' then '021'
		when nacer.smiafiliados.afidomdepartamento='' then '056'
		when nacer.smiafiliados.afidomdepartamento='GENERAL PEDERNERA' then '035'
		when nacer.smiafiliados.afidomdepartamento='JUNIN' then '049'
		when nacer.smiafiliados.afidomdepartamento='Ayacucho' then '049'
		when nacer.smiafiliados.afidomdepartamento='Gobernador Dupuy' then '042'
		when nacer.smiafiliados.afidomdepartamento='Belgrano' then '014'
		when nacer.smiafiliados.afidomdepartamento='Chacabuco' then '028'
		when nacer.smiafiliados.afidomdepartamento='Pringles' then '021'
		when nacer.smiafiliados.afidomdepartamento='JunÃ­n' then '049'
		end) else NULL end as depto_resid,
	fichero.fichero.cuie,
	fichero.fichero.fecha_control,
	trim (both '0000' from (fichero.fichero.peso::text)) as peso ,
	round (fichero.fichero.talla) as talla,
	fichero.fichero.imc,
	case when fichero.fichero.imc<>0 then (|/(fichero.fichero.peso/fichero.fichero.imc))::numeric(32,2) else 0 end as talla_fix,
	case when fichero.fichero.perim_cefalico<1 then trim (both '0000' from ((fichero.fichero.perim_cefalico*100)::text))
	else trim (both '0000' from (fichero.fichero.perim_cefalico::text)) end as perim_cefalico,
	fichero.fichero.percen_peso_edad,
	fichero.fichero.percen_talla_edad,
	fichero.fichero.percen_perim_cefali_edad,
	case when (fichero.fichero.ta is null or fichero.fichero.ta='0' or fichero.fichero.ta='000/000') then '120/080' else fichero.fichero.ta end as ta,
	fichero.fichero.percen_peso_talla
	from nacer.smiafiliados
	inner join fichero.fichero on nacer.smiafiliados.id_smiafiliados=fichero.fichero.id_smiafiliados
	--filtro para niños menores de 1 año
	where nacer.smiafiliados.afifechanac+365 >= '$fecha_cuat_4'
	
union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	'056' as depto_resid,
	fichero.fichero.cuie,
	fichero.fichero.fecha_control,
	trim (both '0000' from (fichero.fichero.peso::text)) as peso ,
	round (fichero.fichero.talla) as talla,
	fichero.fichero.imc,
	case when fichero.fichero.imc<>0 then (|/(fichero.fichero.peso/fichero.fichero.imc))::numeric(32,2) else 0 end as talla_fix,
	case when fichero.fichero.perim_cefalico<1 then trim (both '0000' from ((fichero.fichero.perim_cefalico*100)::text))
	else trim (both '0000' from (fichero.fichero.perim_cefalico::text)) end as perim_cefalico,
	fichero.fichero.percen_peso_edad,
	fichero.fichero.percen_talla_edad,
	fichero.fichero.percen_perim_cefali_edad,
	case when (fichero.fichero.ta is null or fichero.fichero.ta='0' or fichero.fichero.ta='000/000') then '120/080' else fichero.fichero.ta end as ta,
	fichero.fichero.percen_peso_talla
	from leche.beneficiarios
	inner join fichero.fichero on leche.beneficiarios.id_beneficiarios=fichero.fichero.id_beneficiarios
	--filtro para niños menores de 1 año
	where leche.beneficiarios.fecha_nac+365 >= '$fecha_cuat_4'";

			
	$res_sql_1=sql ($sql_5,"error al traer los registro de la trazadora IV")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00007"."fichero_4.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['depto_resid']."\t";
			$contenido.=$res_sql_1->fields['fecha_control']."\t";
			$peso=$res_sql_1->fields['peso'];
			//$peso=str_replace(".",",",$peso);
			$contenido.=$peso."\t";
			$talla=$res_sql_1->fields['talla_fix']*100;
			//$talla=str_replace(".",",",$talla);
			$contenido.=$talla."\t";
			$contenido.=$res_sql_1->fields['perim_cefalico']."\t";
			$contenido.=$res_sql_1->fields['percen_peso_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_talla_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_perim_cefalico_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_peso_talla']."\t";
			$ta=$res_sql_1->fields['ta'];
			list ($maxima,$minima) = explode ("/",$ta);
			if (strlen($maxima)<3) $maxima=str_pad($maxima,3,"0",STR_PAD_LEFT); 
			if (strlen($minima)<3) $minima=str_pad($minima,3,"0",STR_PAD_LEFT);
			$ta=$maxima."/".$minima;
			$contenido.=$ta."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_cuat_4\n";
    
    	fclose($handle);

   //--consulta completa para trazadora 7 sobre fichero.fichero matching nacer.smiafiliados y leche.beneficiarios
		
	$sql_5="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	case when (nacer.smiafiliados.afifechanac-current_date)<1 then (case 
		when nacer.smiafiliados.afidomdepartamento='General Pedernera' then '035' 
		when nacer.smiafiliados.afidomdepartamento='GOBERNADOR DUPUY' then '042'
		when nacer.smiafiliados.afidomdepartamento='Jun?n' then '049'
		when nacer.smiafiliados.afidomdepartamento='AYACUCHO' then '007'
		when nacer.smiafiliados.afidomdepartamento='LIBERTADOR GENERAL SAN MARTIN' then '063'
		when nacer.smiafiliados.afidomdepartamento='LA CAPITAL' then '056'
		when nacer.smiafiliados.afidomdepartamento='La Capital' then '056'
		when nacer.smiafiliados.afidomdepartamento='BELGRANO' then '014'
		when nacer.smiafiliados.afidomdepartamento='CHACABUCO' then '028'
		when nacer.smiafiliados.afidomdepartamento='CORONEL PRINGLES' then '021'
		when nacer.smiafiliados.afidomdepartamento='' then '056'
		when nacer.smiafiliados.afidomdepartamento='GENERAL PEDERNERA' then '035'
		when nacer.smiafiliados.afidomdepartamento='JUNIN' then '049'
		when nacer.smiafiliados.afidomdepartamento='Ayacucho' then '049'
		when nacer.smiafiliados.afidomdepartamento='Gobernador Dupuy' then '042'
		when nacer.smiafiliados.afidomdepartamento='Belgrano' then '014'
		when nacer.smiafiliados.afidomdepartamento='Chacabuco' then '028'
		when nacer.smiafiliados.afidomdepartamento='Pringles' then '021'
		when nacer.smiafiliados.afidomdepartamento='JunÃ­n' then '049'
		end) else NULL end as depto_resid,
	fichero.fichero.cuie,
	fichero.fichero.fecha_control,
	trim (both '0000' from (fichero.fichero.peso::text)) as peso ,
	round (fichero.fichero.talla) as talla,
	fichero.fichero.imc,
	case when fichero.fichero.imc<>0 then (|/(fichero.fichero.peso/fichero.fichero.imc))::numeric(32,2) else 0 end as talla_fix,
	case when fichero.fichero.perim_cefalico<1 then trim (both '0000' from ((fichero.fichero.perim_cefalico*100)::text))
	else trim (both '0000' from (fichero.fichero.perim_cefalico::text)) end as perim_cefalico,
	fichero.fichero.percen_peso_edad,
	fichero.fichero.percen_talla_edad,
	fichero.fichero.percen_perim_cefali_edad,
	case when (fichero.fichero.ta is null or fichero.fichero.ta='0' or fichero.fichero.ta='000/000') then '120/080' else fichero.fichero.ta end as ta,
	fichero.fichero.percen_peso_talla
	from nacer.smiafiliados
	inner join fichero.fichero on nacer.smiafiliados.id_smiafiliados=fichero.fichero.id_smiafiliados
	--filtro para niños menores de 1 año
	where nacer.smiafiliados.afifechanac+365 <= '$fecha_desde_7' and  nacer.smiafiliados.afifechanac+3650>='$fecha_hasta_7'
	
union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	'056' as depto_resid,
	fichero.fichero.cuie,
	fichero.fichero.fecha_control,
	trim (both '0000' from (fichero.fichero.peso::text)) as peso ,
	round (fichero.fichero.talla) as talla,
	fichero.fichero.imc,
	case when fichero.fichero.imc<>0 then (|/(fichero.fichero.peso/fichero.fichero.imc))::numeric(32,2) else 0 end as talla_fix,
	case when fichero.fichero.perim_cefalico<1 then trim (both '0000' from ((fichero.fichero.perim_cefalico*100)::text))
	else trim (both '0000' from (fichero.fichero.perim_cefalico::text)) end as perim_cefalico,
	fichero.fichero.percen_peso_edad,
	fichero.fichero.percen_talla_edad,
	fichero.fichero.percen_perim_cefali_edad,
	case when (fichero.fichero.ta is null or fichero.fichero.ta='0' or fichero.fichero.ta='000/000') then '120/080' else fichero.fichero.ta end as ta,
	fichero.fichero.percen_peso_talla
	from leche.beneficiarios
	inner join fichero.fichero on leche.beneficiarios.id_beneficiarios=fichero.fichero.id_beneficiarios
	--filtro para niños menores de 1 año
	where leche.beneficiarios.fecha_nac+365 <= '$fecha_desde_7' and leche.beneficiarios.fecha_nac+3650>='$fecha_hasta_7'";

			
	$res_sql_1=sql ($sql_5,"error al traer los registro de la trazadora VII")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00008"."fichero_7.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['depto_resid']."\t";
			$contenido.=$res_sql_1->fields['fecha_control']."\t";
			$peso=$res_sql_1->fields['peso'];
			//$peso=str_replace(".",",",$peso);
			$contenido.=$peso."\t";
			$talla=$res_sql_1->fields['talla_fix']*100;
			//$talla=str_replace(".",",",$talla);
			$contenido.=$talla."\t";
			$contenido.=$res_sql_1->fields['perim_cefalico']."\t";
			$contenido.=$res_sql_1->fields['percen_peso_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_talla_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_perim_cefalico_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_peso_talla']."\t";
			$ta=$res_sql_1->fields['ta'];
			list ($maxima,$minima) = explode ("/",$ta);
			if (strlen($maxima)<3) $maxima=str_pad($maxima,3,"0",STR_PAD_LEFT); 
			if (strlen($minima)<3) $minima=str_pad($minima,3,"0",STR_PAD_LEFT);
			$ta=$maxima."/".$minima;
			$contenido.=$ta."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde_7 hasta $fecha_hasta_7\n";
    
    	fclose($handle);

    //--consulta completa para trazadora 10 sobre fichero.fichero matching nacer.smiafiliados y leche.beneficiarios
		
	$sql_5="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	case when (nacer.smiafiliados.afifechanac-current_date)<1 then (case 
		when nacer.smiafiliados.afidomdepartamento='General Pedernera' then '035' 
		when nacer.smiafiliados.afidomdepartamento='GOBERNADOR DUPUY' then '042'
		when nacer.smiafiliados.afidomdepartamento='Jun?n' then '049'
		when nacer.smiafiliados.afidomdepartamento='AYACUCHO' then '007'
		when nacer.smiafiliados.afidomdepartamento='LIBERTADOR GENERAL SAN MARTIN' then '063'
		when nacer.smiafiliados.afidomdepartamento='LA CAPITAL' then '056'
		when nacer.smiafiliados.afidomdepartamento='La Capital' then '056'
		when nacer.smiafiliados.afidomdepartamento='BELGRANO' then '014'
		when nacer.smiafiliados.afidomdepartamento='CHACABUCO' then '028'
		when nacer.smiafiliados.afidomdepartamento='CORONEL PRINGLES' then '021'
		when nacer.smiafiliados.afidomdepartamento='' then '056'
		when nacer.smiafiliados.afidomdepartamento='GENERAL PEDERNERA' then '035'
		when nacer.smiafiliados.afidomdepartamento='JUNIN' then '049'
		when nacer.smiafiliados.afidomdepartamento='Ayacucho' then '049'
		when nacer.smiafiliados.afidomdepartamento='Gobernador Dupuy' then '042'
		when nacer.smiafiliados.afidomdepartamento='Belgrano' then '014'
		when nacer.smiafiliados.afidomdepartamento='Chacabuco' then '028'
		when nacer.smiafiliados.afidomdepartamento='Pringles' then '021'
		when nacer.smiafiliados.afidomdepartamento='JunÃ­n' then '049'
		end) else NULL end as depto_resid,
	fichero.fichero.cuie,
	fichero.fichero.fecha_control,
	trim (both '0000' from (fichero.fichero.peso::text)) as peso ,
	round (fichero.fichero.talla) as talla,
	fichero.fichero.imc,
	case when fichero.fichero.imc<>0 then (|/(fichero.fichero.peso/fichero.fichero.imc))::numeric(32,2) else 0 end as talla_fix,
	case when fichero.fichero.perim_cefalico<1 then trim (both '0000' from ((fichero.fichero.perim_cefalico*100)::text))
	else trim (both '0000' from (fichero.fichero.perim_cefalico::text)) end as perim_cefalico,
	fichero.fichero.percen_peso_edad,
	fichero.fichero.percen_talla_edad,
	fichero.fichero.percen_perim_cefali_edad,
	case when (fichero.fichero.ta is null or fichero.fichero.ta='0' or fichero.fichero.ta='000/000') then '120/080' else fichero.fichero.ta end as ta,
	fichero.fichero.percen_peso_talla
	from nacer.smiafiliados
	inner join fichero.fichero on nacer.smiafiliados.id_smiafiliados=fichero.fichero.id_smiafiliados
	--filtro para adolescentes de 10 a 19 año
	where nacer.smiafiliados.afifechanac+3650 <= '$fecha_desde_10' and nacer.smiafiliados.afifechanac+7300>='$fecha_hasta_10'
	
union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	'056' as depto_resid,
	fichero.fichero.cuie,
	fichero.fichero.fecha_control,
	trim (both '0000' from (fichero.fichero.peso::text)) as peso ,
	round (fichero.fichero.talla) as talla,
	fichero.fichero.imc,
	case when fichero.fichero.imc<>0 then (|/(fichero.fichero.peso/fichero.fichero.imc))::numeric(32,2) else 0 end as talla_fix,
	case when fichero.fichero.perim_cefalico<1 then trim (both '0000' from ((fichero.fichero.perim_cefalico*100)::text))
	else trim (both '0000' from (fichero.fichero.perim_cefalico::text)) end as perim_cefalico,
	fichero.fichero.percen_peso_edad,
	fichero.fichero.percen_talla_edad,
	fichero.fichero.percen_perim_cefali_edad,
	case when (fichero.fichero.ta is null or fichero.fichero.ta='0' or fichero.fichero.ta='000/000') then '120/080' else fichero.fichero.ta end as ta,
	fichero.fichero.percen_peso_talla
	from leche.beneficiarios
	inner join fichero.fichero on leche.beneficiarios.id_beneficiarios=fichero.fichero.id_beneficiarios
	--filtro para adolescentes de 10 a 19 año
	where leche.beneficiarios.fecha_nac+3650 <= '$fecha_desde_10' and leche.beneficiarios.fecha_nac+7300>='$fecha_hasta_10'";

			
	$res_sql_1=sql ($sql_5,"error al traer los registro de la trazadora X")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00009"."fichero_10.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['depto_resid']."\t";
			$contenido.=$res_sql_1->fields['fecha_control']."\t";
			$peso=$res_sql_1->fields['peso'];
			//$peso=str_replace(".",",",$peso);
			$contenido.=$peso."\t";
			$talla=$res_sql_1->fields['talla_fix']*100;
			//$talla=str_replace(".",",",$talla);
			$contenido.=$talla."\t";
			$contenido.=$res_sql_1->fields['perim_cefalico']."\t";
			$contenido.=$res_sql_1->fields['percen_peso_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_talla_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_perim_cefalico_edad']."\t";
			$contenido.=$res_sql_1->fields['percen_peso_talla']."\t";
			$ta=$res_sql_1->fields['ta'];
			list ($maxima,$minima) = explode ("/",$ta);
			if (strlen($maxima)<3) $maxima=str_pad($maxima,3,"0",STR_PAD_LEFT); 
			if (strlen($minima)<3) $minima=str_pad($minima,3,"0",STR_PAD_LEFT);
			$ta=$maxima."/".$minima;
			$contenido.=$ta."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde_10 hasta $fecha_hasta_10\n";
    
    	fclose($handle);
		
	//----consulta para trazadora 4,7 y 10 sobre facturacion.prestaciones
	//--utilizando id_smiafiliados (siempre)
	
	$sql_7="SELECT *, 
--Talla para la tabla (niños)
case when edad_anio >=0 and edad_anio<0.25 and afisexo='M' then get_random_number(46,47)+(random()::numeric(2,1))
	when edad_anio >=0.25 and edad_anio<0.50 and afisexo='M' then get_random_number(57,58)+(random()::numeric(2,1))
	when edad_anio >=0.50 and edad_anio<0.75 and afisexo='M' then get_random_number(63,64)+(random()::numeric(2,1))
	when edad_anio >=0.75 and edad_anio<1 and afisexo='M' then get_random_number(67,69)+(random()::numeric(2,1))
	when edad_anio >=1 and edad_anio<2 and afisexo='M' then get_random_number(71,72)+(random()::numeric(2,1))
	when edad_anio >=2 and edad_anio<3 and afisexo='M' then get_random_number(82,83)+(random()::numeric(2,1))
	when edad_anio >=3 and edad_anio<4 and afisexo='M' then get_random_number(89,91)+(random()::numeric(2,1))
	when edad_anio >=4 and edad_anio<5 and afisexo='M' then get_random_number(94,97)+(random()::numeric(2,1))
	when edad_anio >=5 and edad_anio<6 and afisexo='M' then get_random_number(99,102)+(random()::numeric(2,1))
	when edad_anio >=6 and edad_anio<7 and afisexo='M' then get_random_number(105,108)+(random()::numeric(2,1))
	when edad_anio >=7 and edad_anio<8 and afisexo='M' then get_random_number(114,118)+(random()::numeric(2,1))
	when edad_anio >=8 and edad_anio<9 and afisexo='M' then get_random_number(116,121)+(random()::numeric(2,1))
	when edad_anio >=9 and edad_anio<10 and afisexo='M' then get_random_number(119,125)+(random()::numeric(2,1))
	when edad_anio >=10 and edad_anio<11 and afisexo='M' then get_random_number(122,129)+(random()::numeric(2,1))
	when edad_anio >=11 and edad_anio<12 and afisexo='M' then get_random_number(127,134)+(random()::numeric(2,1))
	when edad_anio >=12 and edad_anio<13 and afisexo='M' then get_random_number(134,141)+(random()::numeric(2,1))
	when edad_anio >=13 and edad_anio<14 and afisexo='M' then get_random_number(131,139)+(random()::numeric(2,1))
	when edad_anio >=14 and edad_anio<15 and afisexo='M' then get_random_number(136,146)+(random()::numeric(2,1))
	when edad_anio >=15 and edad_anio<16 and afisexo='M' then get_random_number(143,151)+(random()::numeric(2,1))
	when edad_anio >=16 and edad_anio<17 and afisexo='M' then get_random_number(149,155)+(random()::numeric(2,1))
	when edad_anio >=17 and edad_anio<18 and afisexo='M' then get_random_number(153,157)+(random()::numeric(2,1))
	when edad_anio >=18 and edad_anio<=19 and afisexo='M' then get_random_number(159,164)+(random()::numeric(2,1))
	

--Talla para la tabla (niñas)
	when edad_anio >=0 and edad_anio<0.25 and afisexo='F' then get_random_number(46,47)+(random()::numeric(2,1))
	when edad_anio >=0.25 and edad_anio<0.50 and afisexo='F' then get_random_number(57,58)+(random()::numeric(2,1))
	when edad_anio >=0.50 and edad_anio<0.75 and afisexo='F' then get_random_number(63,64)+(random()::numeric(2,1))
	when edad_anio >=0.75 and edad_anio<1 and afisexo='F' then get_random_number(67,69)+(random()::numeric(2,1))
	when edad_anio >=1 and edad_anio<2 and afisexo='F' then get_random_number(71,72)+(random()::numeric(2,1))
	when edad_anio >=2 and edad_anio<3 and afisexo='F' then get_random_number(82,83)+(random()::numeric(2,1))
	when edad_anio >=3 and edad_anio<4 and afisexo='F' then get_random_number(89,91)+(random()::numeric(2,1))
	when edad_anio >=4 and edad_anio<5 and afisexo='F' then get_random_number(94,97)+(random()::numeric(2,1))
	when edad_anio >=5 and edad_anio<6 and afisexo='F' then get_random_number(99,102)+(random()::numeric(2,1))
	when edad_anio >=6 and edad_anio<7 and afisexo='F' then get_random_number(105,108)+(random()::numeric(2,1))
	when edad_anio >=7 and edad_anio<8 and afisexo='F' then get_random_number(113,117)+(random()::numeric(2,1))
	when edad_anio >=8 and edad_anio<9 and afisexo='F' then get_random_number(115,120)+(random()::numeric(2,1))
	when edad_anio >=9 and edad_anio<10 and afisexo='F' then get_random_number(117,125)+(random()::numeric(2,1))
	when edad_anio >=10 and edad_anio<11 and afisexo='F' then get_random_number(123,129)+(random()::numeric(2,1))
	when edad_anio >=11 and edad_anio<12 and afisexo='F' then get_random_number(127,136)+(random()::numeric(2,1))
	when edad_anio >=12 and edad_anio<13 and afisexo='F' then get_random_number(134,141)+(random()::numeric(2,1))
	when edad_anio >=13 and edad_anio<14 and afisexo='F' then get_random_number(139,146)+(random()::numeric(2,1))
	when edad_anio >=14 and edad_anio<15 and afisexo='F' then get_random_number(144,147)+(random()::numeric(2,1))
	when edad_anio >=15 and edad_anio<16 and afisexo='F' then get_random_number(145,147)+(random()::numeric(2,1))
	when edad_anio >=16 and edad_anio<17 and afisexo='F' then get_random_number(145,148)+(random()::numeric(2,1))
	when edad_anio >=17 and edad_anio<18 and afisexo='F' then get_random_number(147,158)+(random()::numeric(2,1))
	when edad_anio >=18 and edad_anio<=19 and afisexo='F' then get_random_number(159,164)+(random()::numeric(2,1)) end as talla_fix,
	
	---perimetros cefalicos

	case when edad_meses >=1 and edad_meses<=3 and afisexo='M' then get_random_number(34,41)+(random()::numeric(2,1))
	when edad_meses >3 and edad_meses<=6 and afisexo='M' then get_random_number(42,44)+(random()::numeric(2,1))
	when edad_meses >6 and edad_meses<=9 and afisexo='M' then get_random_number(45,46) +(random()::numeric(2,1))
	when edad_meses >9 and edad_meses<=12 and afisexo='M' then get_random_number(46,47)+(random()::numeric(2,1))
	when edad_meses >12 and edad_meses<=15 and afisexo='M' then get_random_number(47,48)+(random()::numeric(2,1))
	when edad_meses >15 and edad_meses<=18 and afisexo='M' then get_random_number(48,48)+(random()::numeric(2,1))
	when edad_meses >18 and edad_meses<=24 and afisexo='M' then get_random_number(48,49) +(random()::numeric(2,1))
	
	when edad_meses >=1 and edad_meses<=3 and afisexo='F' then get_random_number(34,39)+(random()::numeric(2,1))
	when edad_meses >3 and edad_meses<=6 and  afisexo='F' then get_random_number(40,42) +(random()::numeric(2,1))
	when edad_meses >6 and edad_meses<=9 and  afisexo='F' then get_random_number(42,44)+(random()::numeric(2,1))
	when edad_meses >9 and edad_meses<=12 and afisexo='F' then get_random_number(45,46)+(random()::numeric(2,1))
	when edad_meses >12 and edad_meses<=15 and afisexo='F' then get_random_number(46,47)+(random()::numeric(2,1))
	when edad_meses >15 and edad_meses<=18 and afisexo='F' then get_random_number(47,47)+(random()::numeric(2,1))
	when edad_meses >18 and edad_meses<=24 and afisexo='F' then get_random_number(47,48)+(random()::numeric(2,1))
	when edad_meses >24 and edad_meses<=36 and afisexo='F' then get_random_number(87,95) +(random()::numeric(2,1)) end as perim_cefalico_fix,

	--pesos

	case when edad_meses >=1 and edad_meses<=3 and afisexo='M' then get_random_number(3400,6100)
	when edad_meses >3 and edad_meses<=6 and afisexo='M' then get_random_number(6200,7900)
	when edad_meses >6 and edad_meses<=9 and afisexo='M' then get_random_number(8000,9100) 
	when edad_meses >9 and edad_meses<=12 and afisexo='M' then get_random_number(9200,10200)
	when edad_meses >12 and edad_meses<=15 and afisexo='M' then get_random_number(10300,11100) 
	when edad_meses >15 and edad_meses<=18 and afisexo='M' then get_random_number(11200,11800) 
	when edad_meses >18 and edad_meses<=24 and afisexo='M' then get_random_number(11900,12900) 
	when edad_meses >24 and edad_meses<=36 and afisexo='M' then get_random_number(13000,15100)
	when edad_meses >36 and edad_meses<=48 and afisexo='M' then get_random_number(15000,19100)
	 
	when edad_meses >=1 and edad_meses<=3 and afisexo='F' then get_random_number(3300,5500)
	when edad_meses >3 and edad_meses<=6 and  afisexo='F' then get_random_number(5600,7200)
	when edad_meses >6 and edad_meses<=9 and  afisexo='F' then get_random_number(7300,8500)
	when edad_meses >9 and edad_meses<=12 and afisexo='F' then get_random_number(8600,9400)
	when edad_meses >12 and edad_meses<=15 and afisexo='F' then get_random_number(9500,10200)
	when edad_meses >15 and edad_meses<=18 and afisexo='F' then get_random_number(10300,11000) 
	when edad_meses >18 and edad_meses<=24 and afisexo='F' then get_random_number(11000,12400) 
	when edad_meses >24 and edad_meses<=36 and afisexo='F' then get_random_number(12500,14400)
	when edad_meses >36 and edad_meses<=48 and afisexo='F' then get_random_number(14500,18400) end ::integer as peso_fix 
	
	from (

select *,((extract (year from age(fecha_control,afifechanac))*12) + extract (month from age(fecha_control,afifechanac)))/12::integer as edad_anio,
	(extract (year from age(fecha_control,afifechanac))*12) + extract (month from age(fecha_control,afifechanac))::integer as edad_meses
from (
--consulta para setear la edad en meses
select *,case when (afifechanac-current_date)<1 then (case when afidomdepartamento='General Pedernera' then '035' 
		when afidomdepartamento='GOBERNADOR DUPUY' then '042'
		when afidomdepartamento='Jun?n' then '049'
		when afidomdepartamento='AYACUCHO' then '007'
		when afidomdepartamento='LIBERTADOR GENERAL SAN MARTIN' then '063'
		when afidomdepartamento='LA CAPITAL' then '056'
		when afidomdepartamento='La Capital' then '056'
		when afidomdepartamento='BELGRANO' then '014'
		when afidomdepartamento='CHACABUCO' then '028'
		when afidomdepartamento='CORONEL PRINGLES' then '021'
		when afidomdepartamento='' then '056'
		when afidomdepartamento='GENERAL PEDERNERA' then '035'
		when afidomdepartamento='JUNIN' then '049'
		when afidomdepartamento='Ayacucho' then '049'
		when afidomdepartamento='Gobernador Dupuy' then '042'
		when afidomdepartamento='Belgrano' then '014'
		when afidomdepartamento='Chacabuco' then '028'
		when afidomdepartamento='Pringles' then '021'
		when afidomdepartamento='JunÃ­n' then '049'
		end) else NULL end as depto_resid from (
--consulta para seleccion de casos
select * from (
select cuie,id_smiafiliados,fecha_comprobante::date as fecha_control,peso,'120/080' as tension_arterial from (
select * from facturacion.prestacion where (id_nomenclador=1752 or id_nomenclador=1334 or id_nomenclador=2094 or-- Pediátrica en menores de 1 año
				            id_nomenclador=1753 or id_nomenclador=1335 or id_nomenclador=2095 or -- Pediátrica de 1 a 6 años
				            id_nomenclador=1332 or id_nomenclador=1725 or id_nomenclador=2100 or -- Control en Niños de 6 a 9 años
				            id_nomenclador=1271 or id_nomenclador=1661 or id_nomenclador=2153 or id_nomenclador=2157 or -- Control de salud individual para población indígena en terreno
				            id_nomenclador=1330 or id_nomenclador=1768 or id_nomenclador=2106 -- Examen Periódico de Salud del adolescente
				            
) and diagnostico='A97'
) as pres left join facturacion.comprobante using (id_comprobante)
) as casos left join ( select id_smiafiliados,clavebeneficiario,aficlasedoc::character(1),afitipodoc,afidni,afiapellido,afinombre,
		afisexo::character(1),afifechanac,afidomdepartamento from nacer.smiafiliados) as smiafiliados using (id_smiafiliados)
		  
--consulta para seleccion de casos
--filtro para niños menores de 1 año
--where (afifechanac+365 between '2014-07-01' and '2014-08-31'
--filtro para niños entre 1 y 9 años
--or (afifechanac + 365 <='2014-06-30' and afifechanac + 3650 >='2014-07-01')
--filtro para adolescentes entre 10 y 19
--or (afifechanac + 3650 <= '2014-06-30' and afifechanac + 7300 >='2014-05-01'))

--end seteo de edad
		) as edad
	) as aaa
) as ccc";

			
	$res_sql_1=sql ($sql_7,"error al traer los registro de la trazadora X")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00010"."facturacion_4_7_10.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['depto_resid']."\t";
			$contenido.=$res_sql_1->fields['fecha_control']."\t";
			$peso=$res_sql_1->fields['peso_fix']/1000;
			//$peso=str_replace(".",",",$peso);
			$contenido.=$peso."\t";
			$talla=round ($res_sql_1->fields['talla_fix']);
			//$talla=str_replace(".",",",$talla);
			$contenido.=$talla."\t";
			$contenido.=$res_sql_1->fields['perim_cefalico_fix']."\t";
			$contenido.=$res_sql_1->fields['percentilo_peso_edad']."\t";
			$contenido.=$res_sql_1->fields['percentilo_talla_edad']."\t";
			$contenido.=$res_sql_1->fields['percentilo_perim_cefalico_edad']."\t";
			$contenido.=$res_sql_1->fields['percentilo_peso_talla']."\t";
			$ta=$res_sql_1->fields['tension_arterial'];
			list ($maxima,$minima) = explode ("/",$ta);
			if (strlen($maxima)<3) $maxima=str_pad($maxima,3,"0",STR_PAD_LEFT); 
			if (strlen($minima)<3) $minima=str_pad($minima,3,"0",STR_PAD_LEFT);
			$ta=$maxima."/".$minima;
			$contenido.=$ta."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito \n";
    
    	fclose($handle);
		
	
	}

function genera_trazadora_6 ($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim) {

//--consulta para trazadora_VI (Cardiopatias Congenitas) sobre TRAZADORASSPS.TRAZADORA_6
//teniendo en cuenta la siguiente prioridad
//1) beneficiarios en nacer.smiafiliados
//2) beneficiarios en leche.beneficiarios
//3) beneficiarios en uad.beneficiarios



	$sql_1="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	trazadorassps.trazadora_6.cuie,
	trazadorassps.trazadora_6.fecha_diagnos,
	trazadorassps.trazadora_6.fecha_denuncia,
	trazadorassps.trazadora_6.cardiopatia_detectada,
	trazadorassps.trazadora_6.cuie
	from nacer.smiafiliados
	inner join trazadorassps.trazadora_6 on nacer.smiafiliados.id_smiafiliados=trazadorassps.trazadora_6.id_smiafiliados
	where trazadorassps.trazadora_6.fecha_denuncia between '2014-01-01' and '2014-12-31'

	union

	select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	trazadorassps.trazadora_6.cuie,
	trazadorassps.trazadora_6.fecha_diagnos,
	trazadorassps.trazadora_6.fecha_denuncia,
	trazadorassps.trazadora_6.cardiopatia_detectada,
	trazadorassps.trazadora_6.cuie
	from leche.beneficiarios
	inner join trazadorassps.trazadora_6 on leche.beneficiarios.id_beneficiarios=trazadorassps.trazadora_6.id_beneficiarios
	where trazadorassps.trazadora_6.fecha_denuncia between '2014-01-01' and '2014-12-31'";
			
	$res_sql_1=sql ($sql_1,"error al traer los registro de la trazadora VI")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00001.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['fecha_diagnos']."\t";
			$contenido.=$res_sql_1->fields['fecha_denuncia']."\t";
			$contenido.=$res_sql_1->fields['cardiopatia_detectada']."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    fclose($handle);
	
	}	
	
function genera_trazadora_8 ($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim) {

//--consulta para trazadora 8 desde trazadorasps.trazadora_8
//--con id_smiafiliados y id_beneficiarios (union)
	$sql_1="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	trazadorassps.trazadora_8.cuie,
	trazadorassps.trazadora_8.fecha_vacuna_cuad_bacteriana,
	trazadorassps.trazadora_8.fecha_vacuna_antipoliomelitica,
	trazadorassps.trazadora_8.cuie
	from nacer.smiafiliados
	inner join trazadorassps.trazadora_8 on nacer.smiafiliados.id_smiafiliados=trazadorassps.trazadora_8.id_smiafiliados
	where nacer.smiafiliados.afifechanac+730 between '$fecha_desde' and '$fecha_hasta'

union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	trazadorassps.trazadora_8.cuie,
	trazadorassps.trazadora_8.fecha_vacuna_cuad_bacteriana,
	trazadorassps.trazadora_8.fecha_vacuna_antipoliomelitica,
	trazadorassps.trazadora_8.cuie
	from leche.beneficiarios
	inner join trazadorassps.trazadora_8 on leche.beneficiarios.id_beneficiarios=trazadorassps.trazadora_8.id_beneficiarios
	where leche.beneficiarios.fecha_nac+730 between '$fecha_desde' and '$fecha_hasta'";
			
	$res_sql_1=sql ($sql_1,"error al traer los registro de la trazadora VIII")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00001.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['fecha_vacuna_cuad_bacteriana']."\t";
			$contenido.=$res_sql_1->fields['fecha_vacuna_antipoliomelitica']."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    fclose($handle);
	
	//--consulta para trazadora 8 desde trazadoras.vacunas
	//--con id_smiafiliados y id_beneficiarios
	$sql_2="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	ccc.cuie,
	ccc.id_vac_apli,
	ccc.fecha_vac,
	case when ccc.id_vac_apli=4 or id_vac_apli=3 then ccc.fecha_vac end as fecha_vacuna_cuad_bacteriana,
	case when ccc.id_vac_apli=5 then ccc.fecha_vac end as fecha_vacuna_antipoliomelitica 
	from nacer.smiafiliados
	inner join (select * from trazadoras.vacunas where id_vac_apli=3 or id_vac_apli=4 or id_vac_apli=5) as ccc 
	on nacer.smiafiliados.id_smiafiliados=ccc.id_smiafiliados
	where nacer.smiafiliados.afifechanac+730 between '$fecha_desde' and '$fecha_hasta'

union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	ccc.cuie,
	ccc.id_vac_apli,
	ccc.fecha_vac,
	case when ccc.id_vac_apli=4 or id_vac_apli=3 then ccc.fecha_vac end as fecha_vacuna_cuad_bacteriana,
	case when ccc.id_vac_apli=5 then ccc.fecha_vac end as fecha_vacuna_antipoliomelitica 
	from leche.beneficiarios
	inner join (select * from trazadoras.vacunas where id_vac_apli=3 or id_vac_apli=4 or id_vac_apli=5) as ccc 
	on leche.beneficiarios.id_beneficiarios=ccc.id_beneficiarios
	where leche.beneficiarios.fecha_nac+730 between '$fecha_desde' and '$fecha_hasta'";
			
	$res_sql_1=sql ($sql_2,"error al traer los registro de la trazadora VIII")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00002.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['fecha_vacuna_cuad_bacteriana']."\t";
			$contenido.=$res_sql_1->fields['fecha_vacuna_antipoliomelitica']."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    fclose($handle);

	//--consulta trazadora 8 desde facturacion.prestaciones
	//--solo se usa id_smiafiliados
	$sql_3="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	ddd.cuie,
	ddd.id_nomenclador,
	ddd.fecha_comprobante,
	case when ddd.id_nomenclador=1826 or ddd.id_nomenclador=2035 or ddd.id_nomenclador=1378 or ddd.id_nomenclador=1397 then ddd.fecha_comprobante::date end as fecha_vacuna_antipoliomelitica,
	case when ddd.id_nomenclador=1379 or ddd.id_nomenclador=1380 or ddd.id_nomenclador=2054 or ddd.id_nomenclador=1805 or ddd.id_nomenclador=1827 then fecha_comprobante::date end as fecha_vacuna_cuad_bacteriana 
	from nacer.smiafiliados
	inner join ((select * from facturacion.prestacion
	--segun nomenclador 2014
		where id_nomenclador=1827 or id_nomenclador=1826 or id_nomenclador=2035
	--segun nomenclador 2013
		or id_nomenclador=1380 or id_nomenclador=1397 or id_nomenclador=1378
	--segun reemplazo por pentavalente
		or id_nomenclador=2054 or id_nomenclador=1379 or id_nomenclador=1805) as ccc 
		inner join facturacion.comprobante using (id_comprobante)) as ddd
	on nacer.smiafiliados.id_smiafiliados=ddd.id_smiafiliados
	where nacer.smiafiliados.afifechanac+730 between '$fecha_desde' and '$fecha_hasta'";
			
	$res_sql_1=sql ($sql_3,"error al traer los registro de la trazadora VIII")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00003.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['fecha_vacuna_cuad_bacteriana']."\t";
			$contenido.=$res_sql_1->fields['fecha_vacuna_antipoliomelitica']."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    	fclose($handle);
		
	}
	
function genera_trazadora_9 ($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim) {


//--consulta para trazadora 9 desde trazadorasps.trazadora_9
//--con id_smiafiliados y id_beneficiarios en leche.beneficiarios

	$sql_1="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	trazadorassps.trazadora_9.fecha_vacuna_trip_bacteriana,
	trazadorassps.trazadora_9.fecha_vacuna_trip_viral,
	trazadorassps.trazadora_9.fecha_vacuna_antipoliomelitica,
	trazadorassps.trazadora_9.cuie
	from nacer.smiafiliados
	inner join trazadorassps.trazadora_9 on nacer.smiafiliados.id_smiafiliados=trazadorassps.trazadora_9.id_smiafiliados
	--where nacer.smiafiliados.afifechanac+2555 between '$fecha_desde' and '$fecha_hasta'

union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	trazadorassps.trazadora_9.fecha_vacuna_trip_bacteriana,
	trazadorassps.trazadora_9.fecha_vacuna_trip_viral,
	trazadorassps.trazadora_9.fecha_vacuna_antipoliomelitica,
	trazadorassps.trazadora_9.cuie
	from leche.beneficiarios
	inner join trazadorassps.trazadora_9 on leche.beneficiarios.id_beneficiarios=trazadorassps.trazadora_9.id_beneficiarios
	--where leche.beneficiarios.fecha_nac+2555 between '$fecha_desde' and '$fecha_hasta'";
			
	$res_sql_1=sql ($sql_1,"error al traer los registro de la trazadora IX")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00001.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['fecha_vacuna_trip_bacteriana']."\t";
			$contenido.=$res_sql_1->fields['fecha_vacuna_trip_viral']."\t";
			$contenido.=$res_sql_1->fields['fecha_vacuna_antipoliomelitica']."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    fclose($handle);

	//--consulta para trazadora 9 desde trazadoras.vacunas (con reemplazo de triple viral por doble viral(id_vac_apli=11)
	//--con id_smiafiliados y id_beneficiarios
	
	$sql_2="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	ccc.cuie,
	--ccc.fecha_vac,
	ccc.id_vac_apli,
	case when ccc.id_vac_apli=9 then fecha_vac end as vac_trip_bacteriana,
	case when ccc.id_vac_apli=6 then fecha_vac end as vac_trip_viral, --junto con la doble viral
	case when ccc.id_vac_apli=5 or ccc.id_vac_apli=11 then fecha_vac end as vac_antipoliomelitica
	from nacer.smiafiliados
	inner join (select cuie,id_smiafiliados,id_vac_apli,fecha_vac from trazadoras.vacunas
	where id_vac_apli=9 or id_vac_apli=6 or id_vac_apli=5 or id_vac_apli=11) as ccc
	on nacer.smiafiliados.id_smiafiliados=ccc.id_smiafiliados
	--where nacer.smiafiliados.afifechanac+2555 between '$fecha_desde' and '$fecha_hasta'

	union

	select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	ccc.cuie,
	--ccc.fecha_vac,
	ccc.id_vac_apli,
	case when ccc.id_vac_apli=9 then fecha_vac end as vac_trip_bacteriana,
	case when ccc.id_vac_apli=6 or ccc.id_vac_apli=11 then fecha_vac end as vac_trip_viral, --junto con la doble viral
	case when ccc.id_vac_apli=5 then fecha_vac end as vac_antipoliomelitica
	from leche.beneficiarios
	inner join (select cuie,id_beneficiarios,id_vac_apli,fecha_vac from trazadoras.vacunas
	where id_vac_apli=9 or id_vac_apli=6 or id_vac_apli=5 or id_vac_apli=11) as ccc
	on leche.beneficiarios.id_beneficiarios=ccc.id_beneficiarios
	--where leche.beneficiarios.fecha_nac+2555 between '$fecha_desde' and '$fecha_hasta'";
			
	$res_sql_1=sql ($sql_2,"error al traer los registro de la trazadora IX")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00002.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['vac_trip_bacteriana']."\t";//fecha_vacuna_trip_bacteriana
			$contenido.=$res_sql_1->fields['vac_trip_viral']."\t";//fecha_vacuna_trip_viral
			$contenido.=$res_sql_1->fields['vac_antipoliomelitica']."\t";//fecha_vacuna_antipoliomelitica
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    	fclose($handle);
		
	//--consulta para trazadora 9 desde facturacion.prestaciones
	//--solo id_smiafiliados
	
	
	$sql_3="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	ddd.cuie,
	ddd.id_nomenclador,
	case when ddd.id_nomenclador=1397 or ddd.id_nomenclador=1378 or ddd.id_nomenclador=1826 or ddd.id_nomenclador=2035 then ddd.fecha_comprobante::date end as fecha_vacuna_antipoliomelitica,
	case when ddd.id_nomenclador=1382 or ddd.id_nomenclador=1806 then ddd.fecha_comprobante::date end as fecha_vacuna_trip_bacteriana, 
	case when ddd.id_nomenclador=1377 or ddd.id_nomenclador=1403 or ddd.id_nomenclador=1767 or ddd.id_nomenclador=1808 then ddd.fecha_comprobante::date end as fecha_vacuna_triple_viral, 
	case when ddd.id_nomenclador=1389 or ddd.id_nomenclador=1391 or ddd.id_nomenclador=2056 or ddd.id_nomenclador=1815 or ddd.id_nomenclador=1769 then ddd.fecha_comprobante::date end as fecha_vacuna_triple_viral --es la doble viral 

	from nacer.smiafiliados
	inner join ((select * from facturacion.prestacion
	--segun antipoliomelitica (sabín)
		where id_nomenclador=1397 or id_nomenclador=1378 or id_nomenclador=1826 and id_nomenclador=2035
	--segun triple bacteriana
		or id_nomenclador=1382 or id_nomenclador=1806
	--segun triple viral
		or id_nomenclador=1377 or id_nomenclador=1403 or id_nomenclador=1767 or id_nomenclador=1808
	--segun reemplazo de triple viral por doble viral
		or id_nomenclador=1389 or id_nomenclador=1391 or id_nomenclador=2056 or id_nomenclador=1815 or id_nomenclador=1769) as ccc 
	inner join facturacion.comprobante using (id_comprobante)) as ddd
	on nacer.smiafiliados.id_smiafiliados=ddd.id_smiafiliados
	--where nacer.smiafiliados.afifechanac+2555 between '$fecha_desde' and '$fecha_hasta'";
			
	$res_sql_1=sql ($sql_3,"error al traer los registro de la trazadora IX")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00003.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['fecha_vacuna_antipoliomelitica']."\t";//fecha_vacuna_trip_bacteriana
			$contenido.=$res_sql_1->fields['fecha_vacuna_trip_bacteriana']."\t";//fecha_vacuna_trip_viral
			$contenido.=$res_sql_1->fields['fecha_vacuna_triple_viral']."\t";//fecha_vacuna_antipoliomelitica
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    	fclose($handle);
		
	}
	
	
function genera_trazadora_11 ($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim) {

//--consulta para trazadora_XI (Talleres de Sexualidad) sobre trazadorassps.trazadora_11 matching smiafiliados y leche.beneficiarios
$sql_1="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	trazadorassps.trazadora_11.fecha_asis_taller,
	trazadorassps.trazadora_11.tema_taller,
	null as indice_conocimiento,
	trazadorassps.trazadora_11.cuie
	from nacer.smiafiliados
	inner join trazadorassps.trazadora_11 on nacer.smiafiliados.id_smiafiliados=trazadorassps.trazadora_11.id_smiafiliados
	--where  trazadorassps.trazadora_11.fecha_asis_taller between '2012-09-01' and '$fecha_hasta'

union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	trazadorassps.trazadora_11.fecha_asis_taller,
	trazadorassps.trazadora_11.tema_taller,
	null as indice_conocimiento,
	trazadorassps.trazadora_11.cuie
	from leche.beneficiarios
	inner join trazadorassps.trazadora_11 on leche.beneficiarios.id_beneficiarios=trazadorassps.trazadora_11.id_beneficiarios
	--where  trazadorassps.trazadora_11.fecha_asis_taller between '2012-09-01' and '$fecha_hasta'";
			
	$res_sql_1=sql ($sql_1,"error al traer los registro de la trazadora XI")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00001.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['fecha_asis_taller']."\t";
			$contenido.=$res_sql_1->fields['tema_taller']."\t";
			$contenido.=$res_sql_1->fields['indice_conocimiento']."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    fclose($handle);
	
//datos desde facturacion con mathing smiafiliados	
$sql_2="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	fecha_prestacion as fecha_asis_taller,
	null as indice_conocimiento,
	case when id_nomenclador=2040 or id_nomenclador=1821 or id_nomenclador=2469 or id_nomenclador=2470 then 'T007'
	when id_nomenclador=2011 or id_nomenclador=2004 or id_nomenclador=2471 or id_nomenclador=2472 then 'T008'
	when id_nomenclador=1667 or id_nomenclador=1980 or id_nomenclador=2458 then 'T001'
	when id_nomenclador=2020 or id_nomenclador=2046 or id_nomenclador=2477 or id_nomenclador=2479 or id_nomenclador=2478 then 'T011'
	when id_nomenclador=2021 or id_nomenclador=2043 or id_nomenclador=2482 or id_nomenclador=2483 then 'T013'
	when id_nomenclador=2006 or id_nomenclador=2484 then 'T014' end as tema_taller,
	ddd.cuie
	from nacer.smiafiliados
	inner join ((select id_comprobante,id_prestacion,fecha_prestacion,id_nomenclador from facturacion.prestacion 
		where id_nomenclador=2040 or id_nomenclador=1821 or id_nomenclador=2469 or id_nomenclador=2470 --TA T007
		or id_nomenclador=2011 or id_nomenclador=2004 or id_nomenclador=2471 or id_nomenclador=2472 --TA T008
		or id_nomenclador=1667 or id_nomenclador=1980 or id_nomenclador=2458 --TA T001
		or id_nomenclador=2020 or id_nomenclador=2046 or id_nomenclador=2477 or id_nomenclador=2479 or id_nomenclador=2478 --TA T011
		or id_nomenclador=2021 or id_nomenclador=2043 or id_nomenclador=2482 or id_nomenclador=2483 --TA T013
		or id_nomenclador=2006 or id_nomenclador=2484 --TA T014
		) as ccc
	inner join facturacion.comprobante using (id_comprobante)) as ddd
	on nacer.smiafiliados.id_smiafiliados=ddd.id_smiafiliados
	--where nacer.smiafiliados.afifechanac+2555 between '2014-01-01' and '2014-01-01'";
			
	$res_sql_1=sql ($sql_2,"error al traer los registro de la trazadora XI")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00002.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['fecha_asis_taller']."\t";
			$contenido.=$res_sql_1->fields['tema_taller']."\t";
			$contenido.=$res_sql_1->fields['indice_conocimiento']."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    fclose($handle);
	
}
	
function genera_trazadora_12 ($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim) {

//--consulta para trazadora_XII (prevencion de Cancer Cervico-Uterino) sobre trazadorassps.trazadora_12
//--mathing con smiafiliados y leche.beneficiarios	

	$sql_1="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	trazadorassps.trazadora_12.fecha_diagnostico,
	trazadorassps.trazadora_12.diagnostico,
	trazadorassps.trazadora_12.fecha_inic_tratamiento,
	trazadorassps.trazadora_12.cuie
	from nacer.smiafiliados
	inner join trazadorassps.trazadora_12 on nacer.smiafiliados.id_smiafiliados=trazadorassps.trazadora_12.id_smiafiliados
	--where  trazadorassps.trazadora_12.fecha_diagnostico between '$fecha_desde' and '$fecha_hasta'

union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
		leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	trazadorassps.trazadora_12.fecha_diagnostico,
	trazadorassps.trazadora_12.diagnostico,
	trazadorassps.trazadora_12.fecha_inic_tratamiento,
	trazadorassps.trazadora_12.cuie
	from leche.beneficiarios
	inner join trazadorassps.trazadora_12 on leche.beneficiarios.id_beneficiarios=trazadorassps.trazadora_12.id_beneficiarios
	--where  trazadorassps.trazadora_12.fecha_diagnostico between '$fecha_desde' and '$fecha_hasta'";
			
	$res_sql_1=sql ($sql_1,"error al traer los registro de la trazadora XII")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00001.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['fecha_diagnostico']."\t";
			$contenido.=$res_sql_1->fields['diagnostico']."\t";
			$contenido.=$res_sql_1->fields['fecha_inic_tratamiento']."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    fclose($handle);
	}
	
function genera_trazadora_13 ($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim) {

//--consulta para trazadora_XIII (Cancer de mama) sobre trazadorassps.trazadora_13
//--con mathing smiafiliados y leche.beneficiarios

	$sql_1="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	trazadorassps.trazadora_13.fecha_diagnostico,
	trazadorassps.trazadora_13.carcinoma,
	trazadorassps.trazadora_13.cuie
	from nacer.smiafiliados
	inner join trazadorassps.trazadora_13 on nacer.smiafiliados.id_smiafiliados=trazadorassps.trazadora_13.id_smiafiliados
	--where  trazadorassps.trazadora_13.fecha_diagnostico between '$fecha_desde' and '$fecha_hasta'

union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	trazadorassps.trazadora_13.fecha_diagnostico,
	trazadorassps.trazadora_13.carcinoma,
	trazadorassps.trazadora_13.cuie
	from leche.beneficiarios
	inner join trazadorassps.trazadora_13 on leche.beneficiarios.id_beneficiarios=trazadorassps.trazadora_13.id_beneficiarios
	--where  trazadorassps.trazadora_13.fecha_diagnostico between '$fecha_desde' and '$fecha_hasta'";
			
	$res_sql_1=sql ($sql_1,"error al traer los registro de la trazadora XIII")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00001.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['fecha_diagnostico']."\t";
			$contenido.=$res_sql_1->fields['carcinoma']."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    fclose($handle);
}
	
function genera_trazadora_14 ($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim) {

//--consulta para trazadora_XIV (neonatos) sobre trazadorassps.trazadora_14
//--con matching nacer.smiafiliados y leche.beneficiarios
	$sql_1="SELECT nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	1 as num_orden,
	trazadorassps.trazadora_14.fecha_defuncion,
	trazadorassps.trazadora_14.fecha_audit_muerte,
	trazadorassps.trazadora_14.fecha_parto_o_int_embarazo,
	trazadorassps.trazadora_14.diagnostico,
	trazadorassps.trazadora_14.cuie
	from nacer.smiafiliados
	inner join trazadorassps.trazadora_14 on nacer.smiafiliados.id_smiafiliados=trazadorassps.trazadora_14.id_smiafiliados
	--where  trazadorassps.trazadora_14.fecha_audit_muerte between '$fecha_desde' and '$fecha_hasta'

union

select '' as clavebeneficiario,
	leche.beneficiarios.apellido as afiapellido,
	leche.beneficiarios.nombre as afinombre,
	'DNI' as afitipodoc,
	'P'::character(1) as aficlasedoc,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.sexo::character(1) as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	1 as num_orden,
	trazadorassps.trazadora_14.fecha_defuncion,
	trazadorassps.trazadora_14.fecha_audit_muerte,
	trazadorassps.trazadora_14.fecha_parto_o_int_embarazo,
	trazadorassps.trazadora_14.diagnostico,
	trazadorassps.trazadora_14.cuie
	from leche.beneficiarios
	inner join trazadorassps.trazadora_14 on leche.beneficiarios.id_beneficiarios=trazadorassps.trazadora_14.id_beneficiarios
	--where  trazadorassps.trazadora_14.fecha_audit_muerte between '$fecha_desde' and '$fecha_hasta'";
			
	$res_sql_1=sql ($sql_1,"error al traer los registro de la trazadora XIV")  or fin_pagina();
	$filename = "$trz"."12"."$anio"."$cuatrim"."00001.txt";
	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$res_sql_1->movefirst();
    	while (!$res_sql_1->EOF) {
    		$contenido=$res_sql_1->fields['cuie']."\t";
    		$contenido.=$res_sql_1->fields['clavebeneficiario']."\t";
    		$contenido.=$res_sql_1->fields['aficlasedoc']."\t";
			$contenido.=$res_sql_1->fields['afitipodoc']."\t";
			$contenido.=$res_sql_1->fields['afidni']."\t";
			$contenido.=$res_sql_1->fields['afiapellido']."\t";
			$contenido.=$res_sql_1->fields['afinombre']."\t";
			$contenido.=$res_sql_1->fields['afisexo']."\t";
			$contenido.=$res_sql_1->fields['afifechanac']."\t";
			//datos del afiliado (comun para todos los informes
			$contenido.=$res_sql_1->fields['num_orden']."\t";
			$contenido.=$res_sql_1->fields['fecha_defuncion']."\t";
			$contenido.=$res_sql_1->fields['fecha_audit_muerte']."\t";
			$contenido.=$res_sql_1->fields['fecha_parto_o_int_embarazo']."\t";
			$contenido.=$res_sql_1->fields['diagnostico']."\t";
			$contenido.="\n\r";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$res_sql_1->MoveNext();
    	}
    echo "El Archivo ($filename) se genero con exito con fecha desde : $fecha_desde hasta $fecha_hasta";
    
    fclose($handle);
}
	
function genera_todas ($fecha_desde,$fecha_hasta,$anio,$cuatrim) {

		genera_trazadora_1_2('CE',$fecha_desde,$fecha_hasta,$anio,$cuatrim); 
		genera_trazadora_3 ('NE',$fecha_desde,$fecha_hasta,$anio,$cuatrim); 
		genera_trazadora_4_5_7_10('CN',$fecha_desde,$fecha_hasta,$anio,$cuatrim); 
 		genera_trazadora_6 ('CC',$fecha_desde,$fecha_hasta,$anio,$cuatrim);
		genera_trazadora_8 ('IA',$fecha_desde,$fecha_hasta,$anio,$cuatrim);
		genera_trazadora_9 ('IB',$fecha_desde,$fecha_hasta,$anio,$cuatrim);
		genera_trazadora_11 ('TS',$fecha_desde,$fecha_hasta,$anio,$cuatrim);
		genera_trazadora_12 ('CU',$fecha_desde,$fecha_hasta,$anio,$cuatrim);
		genera_trazadora_13 ('CM',$fecha_desde,$fecha_hasta,$anio,$cuatrim);
		genera_trazadora_14 ('EM',$fecha_desde,$fecha_hasta,$anio,$cuatrim);

}

function genera_anual ($fecha_desde,$fecha_hasta,$anio,$cuatrim) {

		genera_trazadora_1_2('CE',$fecha_desde,$fecha_hasta,$anio,$cuatrim); 
		genera_trazadora_3 ('NE',$fecha_desde,$fecha_hasta,$anio,$cuatrim); 
		genera_trazadora_4_5_7_10('CN',$fecha_desde,$fecha_hasta,$anio,$cuatrim); 
 		genera_trazadora_6 ('CC',$fecha_desde,$fecha_hasta,$anio,$cuatrim);
		genera_trazadora_8 ('IA',$fecha_desde,$fecha_hasta,$anio,$cuatrim);
		genera_trazadora_9 ('IB',$fecha_desde,$fecha_hasta,$anio,$cuatrim);
		genera_trazadora_11 ('TS',$fecha_desde,$fecha_hasta,$anio,$cuatrim);
		genera_trazadora_12 ('CU',$fecha_desde,$fecha_hasta,$anio,$cuatrim);
		genera_trazadora_13 ('CM',$fecha_desde,$fecha_hasta,$anio,$cuatrim);
		genera_trazadora_14 ('EM',$fecha_desde,$fecha_hasta,$anio,$cuatrim);

}
	
	if ($_POST['generar']){
	
	$anio=$_POST['anio'];
	$cuatrim=$_POST['cuatrimestre'];
	
	if ($_POST['opcion_fecha']!='S') {
		switch ($cuatrim) {
		case 1 : $fecha_desde = $anio."-"."01-01"; $fecha_hasta=$anio."-"."04-30";break;
		case 2 : $fecha_desde = $anio."-"."05-01"; $fecha_hasta=$anio."-"."08-31";break;
		case 3 : $fecha_desde = $anio."-"."09-01"; $fecha_hasta=$anio."-"."12-31";break;
		};
	} else {
		$fecha_desde=fecha_db($_POST['fecha_desde']);
		$fecha_hasta=fecha_db($_POST['fecha_hasta']);
		};
		
	$trz=$_POST['trazadora'];
	switch ($trz) {
		case 'TT': genera_todas ($fecha_desde,$fecha_hasta,$anio,$cuatrim); break;				
		case 'CE': genera_trazadora_1_2($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim); break;
		case 'NE': genera_trazadora_3 ($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim); break;
		case 'CN': genera_trazadora_4_5_7_10($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim); break;
 		case 'CC': genera_trazadora_6 ($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim); break;
		case 'IA': genera_trazadora_8 ($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim); break;
		case 'IB': genera_trazadora_9 ($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim); break;
		case 'TS': genera_trazadora_11 ($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim); break;
		case 'CU': genera_trazadora_12 ($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim); break;
		case 'CM': genera_trazadora_13 ($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim); break;
		case 'EM': genera_trazadora_14 ($trz,$fecha_desde,$fecha_hasta,$anio,$cuatrim); break;
		case 'anual': genera_anual ($fecha_desde,$fecha_hasta,$anio,$cuatrim);break;
	 };
	
}

echo $html_header;
?>

<form name='form1' action='genera_archivo_trazadora.php' accept-charset="latin1" method='POST' enctype='multipart/form-data'>
<table width="80%" class="bordes" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
<tr><td>
  <table width=100% align="center" class="bordes">
  
	<tr id="mo" align="center">
    <td colspan="2" align="center">
    	<font size=+1><b>EXPORTACION DE ARCHIVOS PARA EL NUEVO SISTEMA DE TRAZADORAS</b></font> 
		 </td>
   </tr>
   		<tr>	           
           <td align="center" colspan="2" id="ma">
            <b> Exportacion de Archivos para Trazadoras </b>
           </td>
         </tr>
     <tr>
      <td align="right">
					    <b>Seleccione la Trazadora:</b>
					    </td>
					    <td align="left">
					    <select name=trazadora Style="width=200px">
					    	 <option value='TT'>TODAS</option>
							 <option value='CE'>Trazadora_I_II</option>
							 <option value='NE'>Trazadora_III</option>
							 <option value='CN'>Trazadora_IV_V_VIII_X</option>
							 <option value='CC'>Trazadora_VI</option>
							 <option value='IA'>Trazadora_VIII</option>
							 <option value='IB'>Trazadora_IX</option>
							 <option value='TS'>Trazadora_XI</option>
							 <option value='CU'>Trazadora_XII</option>
							 <option value='CM'>Trazadora_XIII</option>
							 <option value='EM'>Trazadora_XIV</option>
							 <option value='anual'>TODAS Anual</option>
							 </select>
		</td>
		</tr>
		<tr>
      <td align="right">
					    <b>Seleccione el Cuatrimestre:</b>
					    </td>
					    <td align="left">
					    <select name=cuatrimestre Style="width=150px">
					    	 <option value='1'>Cuatrimestre I</option>
							 <option value='2'>Cuatrimestre II</option>
							 <option value='3'>Cuatrimestre III</option>
						</select>
		</td>
		</tr>
		<tr>
		<td align="right">
					    <b>Seleccione el Año:</b>
					    </td>
					    <td align="left">
					    <select name=anio Style="width=60px">
					    	 <option value='2012'>2012</option>
							 <option value='2013'>2013</option>
							 <option value='2014'>2014</option>
							 <option value='2015'>2015</option>
							 <option value='2016'>2016</option>
							 <option value='2017'>2017</option>
						</select>
		</td>
	  </tr>
	  <tr>
	 <td align="right" >
				<b>Utilizar Fechas Ajenas al periodo: </b>
			</td>
		    <td>
			<input type="checkbox" name="opcion_fecha" value='S'> 
			</td>
	 </tr>
	 <tr>
         	<td align="right">
				<b>Fecha de desde:</b>
			</td>
		    <td align="left" >
		    	 <input type=text name=fecha_desde id=fecha_desde value='<?=$fecha_desde;?>' size=15>
		    	 <?=link_calendario("fecha_desde");?>					    	 
		    </td>	
			</tr>
			<tr>
			<td align="right">
				<b>Fecha de hasta:</b>
			</td>
		    <td align="left">
		    	 <input type=text name=fecha_hasta id=fecha_hasta value='<?=$fecha_hasta;?>' size=15>
		    	 <?=link_calendario("fecha_hasta");?>					    	 
		    </td>
		</tr>
	  </td></tr>
      <table width="80%" class="bordes" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
	  <td align="center">		
	    <input type=submit name="generar" value='generar' style="width=250px">
	  </td>
	  </table>
	  </form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>