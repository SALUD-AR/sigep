<?php

require_once("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['generar']){
	$sql_tmp="select * from public.dosep";
	$result1=sql($sql_tmp);
	$filename = 'DOSEP.txt';	

	  	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$result1->movefirst();
    	while (!$result1->EOF) {
    		$contenido="DNI";
    		$contenido.=str_repeat('0',8-strlen($result1->fields['num'])).$result1->fields['num'];
    		$contenido.=$result1->fields['ape']." ";
    		$contenido.=$result1->fields['nom'];
    		$contenido1=$contenido;
    		$contenido.=str_repeat(' ',61-strlen($contenido1));
    		$contenido.=substr($result1->fields['sexo'],0,1);
    		$contenido.="3225515700";
    		$contenido.="    SL";    		
    		if ($result1->fields['plan']=="PLG") $contenido.="T";
    		else $contenido.="A";
    		$contenido.="\n";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$result1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito";
    
    	fclose($handle);
	
}

if ($_POST['generar_n']){
	$sql_tmp="select * from public.dosep";
	$result1=sql($sql_tmp);
	$filename = 'DOSEP_mod.txt';	

	  	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$result1->movefirst();
    	while (!$result1->EOF) {
    		$contenido="DNI"."||";
    		$contenido.=str_repeat('0',8-strlen($result1->fields['num'])).$result1->fields['num'];
    		$contenido.="||";
    		$contenido.=$result1->fields['ape']." ".$result1->fields['nom'].str_repeat(' ',50-strlen($result1->fields['ape']." ".$result1->fields['nom']));
    		$contenido.="||";
    		$contenido.=substr($result1->fields['sexo'],0,1);
    		$contenido.="||";
    		$contenido.="912001";
    		$contenido.="||";
    		$contenido.="    5700";
    		$contenido.="||";
    		$contenido.="12";    		
    		$contenido.="||";
    		if ($result1->fields['plan']=="PLG") $contenido.="T";
    		else $contenido.="A";
    		$contenido.="\r";
    		$contenido.="\n";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$result1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito";
    
    	fclose($handle);
	
}


if ($_POST['nomivac']){
	
	
	$fecha_desde=fecha_db($_POST['fecha_desde']);
	$fecha_hasta=fecha_db($_POST['fecha_hasta']);
	
	$sql_tmp="SELECT *,case when esquemas.ID_VACUNA_ESQUEMA<>'IMPORTACION' then 3::text
	else 'IMPORTACION' end as ID_APLICACION_CONDICION from (

SELECT vacunas.ID,
	vacunas.afitipodoc as ID_TIPODOC,
	vacunas.afidni as NRODOC,
	vacunas.afiapellido as APELLIDO,
	vacunas.afinombre as NOMBRE,
	vacunas.afisexo::character(1) as SEXO,
	to_char (vacunas.afifechanac,'dd/MM/yyyy') as FECHA_NACIMIENTO,
	vacunas.".'"maTipoDocumento"'." as ID_TIPODOC_MADRE,
	vacunas.manrodocumento as NRODOC_MADRE, 
	200 as ID_PAIS,
	200 as ID_NACIONALIDAD,
	19 as ID_PROVINCIA_NACIMIENTO,
	--vacunas.afidomlocalidad as ID_LOCALIDAD_NACIMIENTO,
	'' as ID_LOCALIDAD_NACIMIENTO,
	'NO' as INDOCUMENTADO,
	19 as ID_PROVINCIA_DOMICILIO, 
	vacunas.cod_sissa as ID_LOCALIDAD_DOMICILIO,
	vacunas.codigodepartamento as ID_DEPARTAMENTO_DOMICILIO,
	vacunas.".'"afiDomCP"'. "as CP_DOMICILIO,
	vacunas.".'"afiDomCalle"'. "as CALLE,
	vacunas.".'"afiDomNro"'. "as CALLE_NRO,
	vacunas.".'"afiDomPiso"'. "as CALLE_PISO,
	null as ID_TIPO_NF, 
	'IMPORTACION' as ID_TIPO_VACUNA,
	vacunas.nomivac as ID_VACUNA,
	vacunas.id_dosis_apli as ID_DOSIS,
	vacunas.cod_siisa as ID_ORIGEN,
	to_char (vacunas.fecha_vac,'dd/MM/yyyy') as FECHA_APLICACION,
	'00' as LOTE,
	
	CASE WHEN vacunas.nomivac = 143 and vacunas.edad_meses >=6 and vacunas.edad_meses <=24 then 78::text
		when vacunas.nomivac = 143 and vacunas.edad_meses >=25 and vacunas.edad_meses <=96 then 180::text
		when vacunas.nomivac = 143 and vacunas.edad_meses >=108 and vacunas.edad_meses <=768 then 124 ::text
		when vacunas.nomivac = 143 and vacunas.edad_meses >=780 then 181::text
		when vacunas.nomivac = 127 and vacunas.edad_meses <=1 then 63::text
		when vacunas.nomivac = 127 and vacunas.edad_meses >1 then 109::text
		--else id_esquema end as ID_VACUNA_ESQUEMA,
		ELSE 'IMPORTACION' end as ID_VACUNA_ESQUEMA
	from (

	SELECT * from (
--beneficiarios en smiafiliados
SELECT DISTINCT ON (trazadoras.vacunas.id_smiafiliados,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac)
	trazadoras.vacunas.id_vacunas as ID, 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.id_dosis_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vacunas.cuie,
	trazadoras.vacunas.id_smiafiliados,
	nacer.efe_conv.cod_siisa,
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afitipodoc,
	nacer.smiafiliados.afisexo,
	nacer.smiafiliados.afifechanac,
	nacer.smiafiliados.".'"maTipoDocumento"'.",
	nacer.smiafiliados.manrodocumento,
	nacer.smiafiliados.afidomlocalidad,
	nacer.smiafiliados.".'"afiDomCP"'.",
	nacer.smiafiliados.".'"afiDomCalle"'.",
	nacer.smiafiliados.".'"afiDomNro"'.",
	nacer.smiafiliados.".'"afiDomPiso"'.",
	trazadoras.vac_apli.nomivac as  ID_VACUNA,
	trazadoras.vac_apli.*,
	(extract (year from age (trazadoras.vacunas.fecha_vac,nacer.smiafiliados.afifechanac))*1) + 
	extract (month from age (trazadoras.vacunas.fecha_vac,nacer.smiafiliados.afifechanac)) as edad_meses,

	case when nacer.smiafiliados.afidomlocalidad is null then 'SAN LUIS'
		 when nacer.smiafiliados.afidomlocalidad='' then 'SAN LUIS'
		 when nacer.smiafiliados.afidomlocalidad='San Luis' then 'SAN LUIS'
		 when nacer.smiafiliados.afidomlocalidad='Villa Mercedes' then 'VILLA MERCEDES'
		 when nacer.smiafiliados.afidomlocalidad='Lujn' then 'LUJAN'
		 when nacer.smiafiliados.afidomlocalidad='Santa Rosa del Conlara' then 'SANTA ROSA DEL CONLARA'
		 when nacer.smiafiliados.afidomlocalidad='Las Aguadas' then 'LAS AGUADAS'
		 when nacer.smiafiliados.afidomlocalidad='Buena Esperanza' then 'BUENA ESPERANZA'
		 when nacer.smiafiliados.afidomlocalidad='La Punta' then 'LA PUNTA'
		 when nacer.smiafiliados.afidomlocalidad='Juana Koslay' then 'JUANA KOSLAY'
		 when nacer.smiafiliados.afidomlocalidad='Merlo' then 'MERLO'
		 when nacer.smiafiliados.afidomlocalidad='Villa de Praga' then 'VILLA DE PRAGA'
		 when nacer.smiafiliados.afidomlocalidad='Quines' then 'QUINES'
		 when nacer.smiafiliados.afidomlocalidad='Nogoli' then 'NOGOLI'
		 when nacer.smiafiliados.afidomlocalidad='Lujan' then 'LUJAN'
		 when nacer.smiafiliados.afidomlocalidad='Arizona' then 'ARIZONA'
		 when nacer.smiafiliados.afidomlocalidad='Justo Daract' then 'JUSTO DARACT'
		 when nacer.smiafiliados.afidomlocalidad='El Volc?n' then 'EL VOLCAN'
		 when nacer.smiafiliados.afidomlocalidad='La Toma' then 'LA TOMA'
		 when nacer.smiafiliados.afidomlocalidad='SELECCIONE LOCALIDAD' then 'SAN LUIS'
		 when nacer.smiafiliados.afidomlocalidad='Balde - Capital' then 'BALDE'
		 when nacer.smiafiliados.afidomlocalidad='San Francisco del Monte de Oro' then 'SAN FRANCISO DEL MONTE DE ORO'
		 when nacer.smiafiliados.afidomlocalidad='Beazley' then 'BEAZLEY'
		 when nacer.smiafiliados.afidomlocalidad='La Florida - Pringles' then 'LA FLORIDA'
		 when nacer.smiafiliados.afidomlocalidad='Los Molles - Jun?n' then 'LOS MOLLES'
		 when nacer.smiafiliados.afidomlocalidad='El Volcan' then 'EL VOLCAN'
		 when nacer.smiafiliados.afidomlocalidad='Tilisarao' then 'TILISARAO'
		 when nacer.smiafiliados.afidomlocalidad='San Mart?n - San Mart?n' then 'SAN MARTIN'
		 when nacer.smiafiliados.afidomlocalidad='Las Chacras - San Mart??n' then 'LAS CHACRAS '
		 when nacer.smiafiliados.afidomlocalidad='Carpinter?a' then 'CARPINTERIA'
		 when nacer.smiafiliados.afidomlocalidad='Villa de la Quebrada' then 'VILLA DE LA QUEBRADA'
		 when nacer.smiafiliados.afidomlocalidad='El Trapiche' then 'EL TRAPICHE'
		 when nacer.smiafiliados.afidomlocalidad='TRAPICHE' then 'EL TRAPICHE'     
		 when nacer.smiafiliados.afidomlocalidad='Santa Rosa' then 'SANTA ROSA'
		 when nacer.smiafiliados.afidomlocalidad='Concaran' then 'CONCARAN'
		 when nacer.smiafiliados.afidomlocalidad='El Volc??n' then 'EL VOLCAN'
		 when nacer.smiafiliados.afidomlocalidad='Naschel' then 'NASCHEL'
		 when nacer.smiafiliados.afidomlocalidad='San Pablo' then 'SAN PABLO'
		 when nacer.smiafiliados.afidomlocalidad='Balde' then 'BALDE'
		 when nacer.smiafiliados.afidomlocalidad='Alto Pencoso' then 'ALTO PENCOSO'
		 when nacer.smiafiliados.afidomlocalidad='Bagual' then 'BAGUAL'
		 when nacer.smiafiliados.afidomlocalidad='Piedra Blanca' then 'PIEDRA BLANCA'
		 when nacer.smiafiliados.afidomlocalidad='Potrerillos' then 'POTRERILLO'
		 when nacer.smiafiliados.afidomlocalidad='Villa Reynolds' then 'VILLA REYNOLDS'
		 when nacer.smiafiliados.afidomlocalidad='Las Lagunas - San Mart??n' then 'LAS LAGUNAS'
		 when nacer.smiafiliados.afidomlocalidad='Renca' then 'RENCA'
		 when nacer.smiafiliados.afidomlocalidad='Potrero de los Funes' then 'PROTERO DE LOS FUNES'
		 when nacer.smiafiliados.afidomlocalidad='Protrero de los Funes' then 'PROTERO DE LOS FUNES'
		 when nacer.smiafiliados.afidomlocalidad='Juan Jorba' then 'JUAN JORBA'
		 when nacer.smiafiliados.afidomlocalidad='R?o Grande' then 'RIO GRANDE'
		 when nacer.smiafiliados.afidomlocalidad='Luj??n' then 'LUJAN'
		 when nacer.smiafiliados.afidomlocalidad='Nahuel Mapa' then 'NAUEL MAPA'
		 when nacer.smiafiliados.afidomlocalidad='Anchorena' then 'ANCHORENA'
		 when nacer.smiafiliados.afidomlocalidad='Batavia' then 'BATAVIA'
		 when nacer.smiafiliados.afidomlocalidad='Candelaria' then 'CANDELARIA'
		 when nacer.smiafiliados.afidomlocalidad='San Jose del Morro' then 'SAN JOSE DEL MORRO'
		 when nacer.smiafiliados.afidomlocalidad='Anchorena' then 'ANCHORENA'
		 when nacer.smiafiliados.afidomlocalidad='La Vertiente - San Mart??n' then 'LA VERTIENTE'
		 when nacer.smiafiliados.afidomlocalidad='Concar?n' then 'CONCARAN'
		 when nacer.smiafiliados.afidomlocalidad='Carpinteria' then 'CARPINTERIA'
		 when nacer.smiafiliados.afidomlocalidad='San Mart??n - San Mart??n' then 'SAN MARTIN'
		 when nacer.smiafiliados.afidomlocalidad='San Jeronino' then 'SAN JERONIMO'
		 when nacer.smiafiliados.afidomlocalidad='La Florida' then 'LA FLORIDA'
		 when nacer.smiafiliados.afidomlocalidad='Lafinur' then 'LAFINUR'
		 when nacer.smiafiliados.afidomlocalidad='Union' then 'UNION'
		 when nacer.smiafiliados.afidomlocalidad='El Zapallar' then 'EL ZAPALLAR'
		 when nacer.smiafiliados.afidomlocalidad='San Ger?nimo' then 'SAN JERONIMO'
		 when nacer.smiafiliados.afidomlocalidad='Navia' then 'NAVIA'
		 when nacer.smiafiliados.afidomlocalidad='El Desag??adero' then 'DESAGUADERO'
		 when nacer.smiafiliados.afidomlocalidad='La Calera' then 'LA CALERA'
		 when nacer.smiafiliados.afidomlocalidad='Las Lagunas' then 'LAS LAGUNAS'
		 when nacer.smiafiliados.afidomlocalidad='Los Molles - Jun??n' then 'LOS MOLLES'
		 when nacer.smiafiliados.afidomlocalidad='Lavaisse' then 'LAVAISSE'
		 when nacer.smiafiliados.afidomlocalidad='El Morro' then 'EL MORRO'
		 when nacer.smiafiliados.afidomlocalidad='Los Cajones' then 'LOS CAJONES'
		 when nacer.smiafiliados.afidomlocalidad='Leandro N Alem' then 'LEANDRO N ALEM'
		 when nacer.smiafiliados.afidomlocalidad='Villa del Carmen' then 'VILLA DEL CARMEN'
		 when nacer.smiafiliados.afidomlocalidad='Cortaderas - Chacabuco' then 'CORTADERAS'
		 when nacer.smiafiliados.afidomlocalidad='Las Botijas' then 'LAS BOTIJAS'
		 when nacer.smiafiliados.afidomlocalidad='Fortuna' then 'FORTUNA'
		 when nacer.smiafiliados.afidomlocalidad='Papagayos' then 'PAPAGALLO'
		 when nacer.smiafiliados.afidomlocalidad='Las Palomas' then 'LAS PALOMAS'
		 when nacer.smiafiliados.afidomlocalidad='EL VOLC?N' then 'EL VOLCAN'
		 when nacer.smiafiliados.afidomlocalidad='El Morro' then 'EL MORRO'
		--ANALIZAR FALTA ALGUNAS CORRECCIONES    
	else nacer.smiafiliados.afidomlocalidad end as ID_LOCALIDAD_DOMICILIO
	
from trazadoras.vacunas 

inner join nacer.smiafiliados on trazadoras.vacunas.id_smiafiliados=smiafiliados.id_smiafiliados
inner join trazadoras.vac_apli on trazadoras.vacunas.id_vac_apli=trazadoras.vac_apli.id_vac_apli
inner join nacer.efe_conv on trazadoras.vacunas.cuie=nacer.efe_conv.cuie

where fecha_vac between '$fecha_desde' and '$fecha_hasta' and trazadoras.vacunas.id_smiafiliados is not null and trazadoras.vacunas.id_beneficiarios=0 
and (estado_envio is null or estado_envio='n')

union

--beneficiarios leche.beneficiarios
SELECT distinct on (trazadoras.vacunas.id_smiafiliados,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vacunas as ID,
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.id_dosis_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vacunas.cuie,
	trazadoras.vacunas.id_smiafiliados,
	nacer.efe_conv.cod_siisa,
	leche.beneficiarios.documento as afidni,
	leche.beneficiarios.nombre as afinombre,
	leche.beneficiarios.apellido as afiapellido,
	'DNI' as afitipodoc,
	leche.beneficiarios.sexo as afisexo,
	leche.beneficiarios.fecha_nac as afifechanac,
	'' as ".'"maTipoDocumento"'.",
	'' as manrodocumento,
	'San Luis' as afidomlocalidad,
	'5700' as ".'"afiDomCP"'.",
	leche.beneficiarios.domicilio as ".'"afiDomCalle"'.",
	'' as ".'"afiDomNro"'.",
	'' as ".'"afiDomPiso"'.",
	trazadoras.vac_apli.nomivac as  ID_VACUNA,
	trazadoras.vac_apli.*,
	(extract (year from age (trazadoras.vacunas.fecha_vac,leche.beneficiarios.fecha_nac))*1) + 
	extract (month from age (trazadoras.vacunas.fecha_vac,leche.beneficiarios.fecha_nac)) as edad_meses,
	'SAN LUIS' as ID_LOCALIDAD_DOMICILIO
	
from trazadoras.vacunas 

inner join leche.beneficiarios on trazadoras.vacunas.id_beneficiarios=beneficiarios.id_beneficiarios
inner join trazadoras.vac_apli on trazadoras.vacunas.id_vac_apli=trazadoras.vac_apli.id_vac_apli
inner join nacer.efe_conv on trazadoras.vacunas.cuie=nacer.efe_conv.cuie


where fecha_vac between '$fecha_desde' and '$fecha_hasta' and trazadoras.vacunas.id_beneficiarios is not null and trazadoras.vacunas.id_smiafiliados=0 
and (estado_envio is null or estado_envio='n')
) as beneficiarios

--tabla con codigo sissa por departamentos y localidades
inner join ( select uad.departamentos.codigodepartamento,
	uad.localidades.nombre as id_localidad_domicilio,
	uad.localidades.cod_sissa

	from uad.provincias 
	inner join uad.departamentos on uad.provincias.id_provincia=uad.departamentos.id_provincia
	inner join uad.localidades on uad.departamentos.id_departamento=uad.localidades.id_departamento
	where uad.provincias.id_provincia=12

) as localidades on localidades.id_localidad_domicilio=beneficiarios.ID_LOCALIDAD_DOMICILIO


	) as vacunas
) as esquemas";
	$result1=sql($sql_tmp);
	$filename = 'nomivac.txt';	//cambiar nombre

	  	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}
		//encabezado
		$contenido="id_tipodoc;nrodoc;apellido;nombre;sexo;fecha_nacimiento;id_tipodoc_madre;nrodoc_madre;id_pais;id_nacionalidad;id_provincia_nacimiento;id_localidad_nacimiento;indocumentado;id_provincia_domicilio;id_localidad_domicilio;id_departamento_domicilio;cp_domicilio;calle;calle_nro;calle_piso;id_tipo_nf;id_tipo_vacuna;id_vacuna;id_dosis;id_origen;fecha_aplicacion;lote;id_vacuna_esquema;id_aplicacion_condicion";
		$contenido.="\r";
    	$contenido.="\n";
		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
		
    	$result1->movefirst();
    	while (!$result1->EOF) {
    		
    		$contenido=$result1->fields['id_tipodoc'].";";
			$contenido.=$result1->fields['nrodoc'].";";
			$contenido.=$result1->fields['apellido'].";";
			$contenido.=$result1->fields['nombre'].";";
			$contenido.=$result1->fields['sexo'].";";
			$contenido.=$result1->fields['fecha_nacimiento'].";";
			$contenido.=$result1->fields['id_tipodoc_madre'].";";
			$contenido.=$result1->fields['nrodoc_madre'].";";
			$contenido.=$result1->fields['id_pais'].";";
			$contenido.=$result1->fields['id_nacionalidad'].";";
			$contenido.=$result1->fields['id_provincia_nacimiento'].";";
			$contenido.=$result1->fields['id_localidad_nacimiento'].";";
			$contenido.=$result1->fields['indocumentado'].";";
			$contenido.=$result1->fields['id_provincia_domicilio'].";";
			$contenido.=$result1->fields['id_localidad_domicilio'].";";
			$contenido.=$result1->fields['id_departamento_domicilio'].";";
			$contenido.=$result1->fields['cp_domicilio'].";";
			$contenido.=$result1->fields['calle'].";";
			$contenido.=$result1->fields['calle_nro'].";";
			$contenido.=$result1->fields['calle_piso'].";";
			$contenido.=$result1->fields['id_tipo_nf'].";";
			$contenido.=$result1->fields['id_tipo_vacuna'].";";
			$contenido.=$result1->fields['id_vacuna'].";";
			$contenido.=$result1->fields['id_dosis'].";";
			$contenido.=$result1->fields['id_origen'].";";
			$contenido.=$result1->fields['fecha_aplicacion'].";";
			$contenido.=$result1->fields['lote'].";";
			$contenido.=$result1->fields['id_vacuna_esquema'].";";
			$contenido.=$result1->fields['id_aplicacion_condicion'].";";
			$contenido.="\r";
    		$contenido.="\n";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
			$id_vacunas=$result1->fields['id'];
			$sql_update="update trazadoras.vacunas set estado_envio='e' where id_vacunas='$id_vacunas'";
			$res_sql_update=sql($sql_update,"No se pudieron actualizar los datos trazadoras.vacunas");
    		$result1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito";
    
    	fclose($handle);
	
}

if ($_POST['generarupdate']){
	
	//para generar script de exportacion de datos desde postgres a SQLserver
	//desde el uad.beneficiarios
	
	$sql_tmp="select * from uad.beneficiarios where tipo_transaccion='M' and estado_envio='e'";
	//$sql_tmp="select * from uad.beneficiarios where id_categoria=1 and fecha_diagnostico_embarazo between '2011-01-01' and '2011-12-31'";
	$result1=sql($sql_tmp);
	$filename = 'update.txt';	

	  	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}

    	$result1->movefirst();
    	while (!$result1->EOF) {
    		$contenido="UPDATE SMIAfiliados SET ";
    		$contenido.="afiApellido="."'".$result1->fields['apellido_benef']."'".", ";
    		$contenido.="afiNombre="."'".$result1->fields['nombre_benef']."'".", ";
    		$contenido.="afiTipoDoc="."'".$result1->fields['tipo_documento']."'".", ";
    		$contenido.="afiClaseDoc="."'".$result1->fields['clase_documento_benef']."'".", ";
    		$contenido.="afiDNI="."'".$result1->fields['numero_doc']."'".", ";
    		$contenido.="afiSexo="."'".$result1->fields['sexo']."'".", ";
    		$contenido.="afiProvincia="."'".$result1->fields['provincia_nac']."'".", ";
    		$contenido.="afiLocalidad="."'".$result1->fields['localidad_nac']."'".", ";
    		$contenido.="afiTipoCategoria="."'".$result1->fields['id_categoria']."'".", ";
    		$contenido.="afiFechaNac="."'".Fecha($result1->fields['fecha_nacimiento_benef'])."'".", ";
    		$contenido.="afiDeclaraIndigena="."'".$result1->fields['indigena']."'".", ";
    		$contenido.="afiId_Lengua="."'".$result1->fields['id_lengua']."'".", ";
    		$contenido.="afiId_Tribu="."'".$result1->fields['id_tribu']."'".", ";
    		$contenido.="maTipoDocumento="."'".$result1->fields['tipo_doc_madre']."'".", ";
    		$contenido.="maNroDocumento="."'".$result1->fields['nro_doc_madre']."'".", ";
    		$contenido.="maApellido="."'".$result1->fields['apellido_madre']."'".", ";
    		$contenido.="maNombre="."'".$result1->fields['nombre_madre']."'".", ";
    		$contenido.="paTipoDocumento="."'".$result1->fields['tipo_doc_padre']."'".", ";
    		$contenido.="paNroDocumento="."'".$result1->fields['nro_doc_padre']."'".", ";
    		$contenido.="paApellido="."'".$result1->fields['apellido_padre']."'".", ";
    		$contenido.="paNombre="."'".$result1->fields['nombre_padre']."'".", ";
    		$contenido.="OtroTipoDocumento="."'".$result1->fields['tipo_doc_tutor']."'".", ";
    		$contenido.="OtroNroDocumento="."'".$result1->fields['nro_doc_tutor']."'".", ";
    		$contenido.="OtroApellido="."'".$result1->fields['apellido_tutor']."'".", ";
    		$contenido.="OtroNombre="."'".$result1->fields['nombre_tutor']."'".", ";
    		$contenido.="FechaInscripcion="."'".Fecha($result1->fields['fecha_inscripcion'])."'".", ";
    		$contenido.="FechaDiagnosticoEmbarazo="."'".Fecha($result1->fields['fecha_diagnostico_embarazo'])."'".", ";
    		$contenido.="SemanasEmbarazo="."'".$result1->fields['semanas_embarazo']."'".", ";
    		$contenido.="FechaProbableParto="."'".Fecha($result1->fields['fecha_probable_parto'])."'".", ";
    		$contenido.="FechaEfectivaParto="."'".Fecha($result1->fields['fecha_efectiva_parto'])."'".", ";
    		$contenido.="Activo="."'".$result1->fields['activo']."'".", ";
    		$contenido.="afiDomCalle="."'".$result1->fields['calle']."'".", ";
    		$contenido.="afiDomNro="."'".$result1->fields['numero_calle']."'".", ";
    		$contenido.="afiDomManzana="."'".$result1->fields['manzana']."'".", ";
    		$contenido.="afiDomPiso="."'".$result1->fields['piso']."'".", ";
    		$contenido.="afiDomDepto="."'".$result1->fields['dpto']."'".", ";
    		$contenido.="afiDomEntreCalle1="."'".$result1->fields['entre_calle_1']."'".", ";
    		$contenido.="afiDomEntreCalle2="."'".$result1->fields['entre_calle_2']."'".", ";
    		$contenido.="afiDomBarrioParaje="."'".$result1->fields['barrio']."'".", ";
    		$contenido.="afiDomMunicipio="."'".$result1->fields['municipio']."'".", ";
    		$contenido.="afiDomDepartamento="."'".$result1->fields['departamento']."'".", ";
    		$contenido.="afiDomLocalidad="."'".$result1->fields['localidad']."'".", ";
    		$contenido.="afiDomProvincia="."'12'".", ";
    		$contenido.="afiDomCP="."'".$result1->fields['cod_pos']."'".", ";
    		$contenido.="afiTelefono="."'".$result1->fields['telefono']."'".", ";
    		$contenido.="FechaCarga="."'".Fecha($result1->fields['fecha_carga'])."'".", ";
    		$contenido.="UsuarioCreacion="."'".$result1->fields['usuario_carga']."'".", ";
    		$contenido.="MenorConviveConTutor="."'".$result1->fields['menor_convive_con_adulto']."'".", ";
    		$contenido.="CUIEEfectorAsignado="."'".$result1->fields['cuie_ea']."'".", ";
    		$contenido.="CUIELugarAtencionHabitual="."'".$result1->fields['cuie_ah']."'";
    		
    		$contenido.="WHERE ClaveBeneficiario=".$result1->fields['clave_beneficiario']."; ";
    		
    		$contenido.="\n";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}   		
    		$result1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito";
    
    	fclose($handle);
	
}

if ($_POST['importarc']){
	//...........................para subir el archivo c................
	    $path = MOD_DIR."/nacer/archivos/";
		$name = $_FILES["archivo"]["name"];		
		$temp = $_FILES["archivo"]["tmp_name"];
		$size = $_FILES["archivo"]["size"];
		$type = $_FILES["archivo"]["type"];
		$extensiones = array("txt");
		if ($name) {
			$name = strtolower($name);
			$ext = substr($name,-3);
			if ($ext != "txt") {
				Error("El formato del archivo debe ser TXT");
			}
			$name = "$name";
			$ret = FileUpload($temp,$size,$name,$type,$max_file_size,$path,"",$extensiones,"",1,0);
			if ($ret["error"] != 0) {
				Error("No se pudo subir el archivo");
			}
		}
  
		$filename = MOD_DIR."/nacer/archivos/".$name;	

	  	if (!$handle = fopen($filename, 'r')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}
    	$fecha_mig=date("Y-m-d");
    	$buffer = stream_get_line($handle, 1024, "\n"); 
    	$periodo_c=substr($buffer,-6,-1);//funciona bien no tocar
    	$periodo_c='2'.$periodo_c;
    	$cont=0;
    
    	while (!feof($handle)) {
    		$buffer = stream_get_line($handle, 1024, "\n");
    		$buffer=ereg_replace("'",null,$buffer);
    		$buffer=ereg_replace('"',null,$buffer);
	        $buffer_array=explode(chr(9),$buffer);
	      	list($a,$b,$c,$d,$f,$g,$h,$i,$j,$k,$l)=$buffer_array;
	      	

	        $q="select nextval('nacer.historico_c_id_historico_c_seq') as id_historico_c";
		    $id_historico_c=sql($q) or fin_pagina();
		    $id_historico_c=$id_historico_c->fields['id_historico_c'];    
      
   			$sql_tmp="INSERT INTO nacer.historico_c
        			(id_historico_c,clave_beneficiario,ape_nom,activo,motivo_baja,fecha_migra,periodo,nom_archivo,cod_baja)
        			VALUES
        			('$id_historico_c', '$b','$f','$h','$j','$fecha_mig','$periodo_c','$filename','$i')";
			sql($sql_tmp);        
	        $cont++;
    	}
		 	
    	echo "Se exportaron $cont Registros correspondientes al Periodo $periodo_c";
    	fclose($handle);
	
}    


if ($_POST['importapuco']){
	$filename = 'puco.txt';	

	  	if (!$handle = fopen($filename, 'r')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}
    
    $filename1 = 'C:\\puco_ok.txt';
    	if (!$handle1 = fopen($filename1, 'w+')) {
        	 echo "No se Puede abrir ($filename1)";
         	exit;
    	}
    	
    	while (!feof($handle)) {
        $buffer = fgets($handle, 61);
        $a=substr($buffer,3,8);
        $b=substr($buffer,0,3);
        $c=substr($buffer,15,6);
        $d=substr($buffer,22,40);       
        
       $contenido="";
       $contenido.=trim($b);
       $contenido.=chr(9);
       $contenido.=ereg_replace('[^ A-Za-z0-9_-]','',trim($d));
       $contenido.=chr(9);
       $contenido.=ereg_replace('[^ A-Za-z0-9_-]','',trim($c));
	   $contenido.=chr(9);
	   $contenido.=trim($a);     
       $contenido.="\n";
    		if (fwrite($handle1, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename1)";
        		exit;
    		}
          
       }
		 	
    	echo "Se Genero c:\poco_ok.txt";
    	fclose($handle);
    	fclose($handle1);
	
}

if ($_POST['importapucocompleto']){
    $filename = 'puco.txt'; 

        if (!$handle = fopen($filename, 'r')) {
             echo "No se Puede abrir ($filename)";
            exit;
        }
        
        $sql_tmp="truncate table puco.puco;";
        sql($sql_tmp); 

        $sql_tmp="DROP INDEX puco.doc_i;";
        sql($sql_tmp);

               
       while (!feof($handle)) {
        $buffer = fgets($handle, 61);
        $a=substr($buffer,3,8);
        $b=substr($buffer,0,3);
        $c=substr($buffer,15,6);
        $d=substr($buffer,22,40);       
        
       
        $b=trim($b);
        $d=ereg_replace('[^ A-Za-z0-9_-]','',trim($d));
        $c=ereg_replace('[^ A-Za-z0-9_-]','',trim($c));
        $a=trim($a);  

        $sql_tmp="INSERT INTO puco.puco
                    (tipo_doc,nombre,cod_os,documento)
                    VALUES
                    ('$b', '$d','$c','$a')";
        sql($sql_tmp); 
       }

        $sql_tmp="CREATE INDEX doc_i
                  ON puco.puco
                  USING btree
                  (documento)";
        sql($sql_tmp);

            
        echo "Se importo puco";
        fclose($handle);          
}

if ($_POST['generarsmiafiliados']){
	$sql_tmp="delete from nacer.smiafiliados";
	$result1=sql($sql_tmp);
	echo "Se elimino datos de la tabla smiafiliados <br>";
	$filename = 'smiafiliados.csv';	

	  	if (!$handle = fopen($filename, 'r')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}
		
    	$cont=0;
    	while (!feof($handle)) {
        $buffer = fgets($handle, 8192);
        $buffer=ereg_replace(chr(9),null,$buffer);
        $buffer=ereg_replace("'",null,$buffer);
        $buffer=explode('"',$buffer);
        //print_r($buffer);
        list($a,$b,$c,$d,$f,$g,$h,$i,$j,$k,$l,$m,$n,$o,$p,$q,$r,$s,$t,$u,$v,$w,$x,$y,$z,$ab,$ac,$ad,$ae,$af,$ag,$ah,$ai,
        	 $aj,$ak,$al,$am,$an,$ao,$ap,$aq,$ar,$as,$at,$au,$av,$aw,$ax)=$buffer;
        $b= ereg_replace(',000000',null,$b);
        $w= ereg_replace(',000000',null,$w);
        $y= Fecha_db(ereg_replace(' 12:00 a.m.',null,$y)); if ($y=='') $y='1980-01-01';
        $ah= ereg_replace(',000000',null,$ah); 
        $al= Fecha_db(ereg_replace(' 12:00 a.m.',null,$al));if ($al=='') $al='1980-01-01';
        $an= Fecha_db(ereg_replace(' 12:00 a.m.',null,$an));if ($an=='') $an='1980-01-01';
		$ax= Fecha_db(ereg_replace(' 12:00 a.m.',null,$ax));if ($ax=='') $ax='1980-01-01';		
		$sql_tmp="INSERT INTO nacer.smiafiliados
        			(id_smiafiliados,clavebeneficiario,afiapellido,afinombre,afitipodoc,aficlasedoc,afidni,afisexo,afidomdepartamento,
  						afidomlocalidad,afitipocategoria,afifechanac,activo,cuieefectorasignado,cuielugaratencionhabitual,
  						motivobaja,mensajebaja,fechainscripcion,fechacarga,usuariocarga,manrodocumento,maapellido,manombre,fechadiagnosticoembarazo)
        			VALUES
        			($b,'$d','$g','$i','$k','$m','$o','$q','$s','$u',$w,'$y','$ab','$ad','$af',$ah,'$aj','$al',
        			'$an','$ap','$ar','$at','$av','$ax')";
		sql($sql_tmp);        
        $cont++;
    	}
		 	
    	echo "Se exportaron $cont Registros";
    	fclose($handle);
	
}

if ($_POST['generarsmiafiliadosaux']){
	$sql_tmp="delete from nacer.smiafiliadosaux";
	$result1=sql($sql_tmp);
	echo "Se elimino datos de la tabla smiafiliadosaux <br>";
	$filename = 'smiafiliadosaux.csv';	

	  	if (!$handle = fopen($filename, 'r')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}
		
    	$cont=0;
    	while (!feof($handle)) {
        $buffer = fgets($handle, 8192);
        $buffer=ereg_replace(chr(9),null,$buffer);
        $buffer=ereg_replace("'",null,$buffer);
        $buffer=explode('"',$buffer);
        //print_r($buffer);
        list($a,$b,$c,$d)=$buffer;
        $d= ereg_replace(',000000',null,$d);
        $sql_tmp="INSERT INTO nacer.smiafiliadosaux
        			(clavebeneficiario,id_procesoingresoafiliados)
        			VALUES
        			('$b',$d)";
		sql($sql_tmp);        
        $cont++;
    	}
		 	
    	echo "Se exportaron $cont Registros";
    	fclose($handle);
	
}

if ($_POST['generarsmiefectores']){
	$sql_tmp="delete from facturacion.smiefectores";
	$result1=sql($sql_tmp);
	echo "Se elimino datos de la tabla smiefectores <br>";
	$filename = 'smiefectores.csv';	

	  	if (!$handle = fopen($filename, 'r')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}
		
    	$cont=0;
    	while (!feof($handle)) {
        $buffer = fgets($handle, 8192);
        $buffer=ereg_replace(chr(9),null,$buffer);
        $buffer=ereg_replace("'",null,$buffer);
        $buffer=explode('"',$buffer);
        //print_r($buffer);
        list($a,$b,$c,$d,$f,$g,$h,$i,$j,$k)=$buffer;
        $sql_tmp="INSERT INTO facturacion.smiefectores
        			(cuie,tipoefector,nombreefector,direccion,localidadmunicipiopartido)
        			VALUES
        			('$b','$d','$g','$i','$k')";
		sql($sql_tmp);        
        $cont++;
    	}
		 	
    	echo "Se exportaron $cont Registros";
    	fclose($handle);
	
}

if ($_POST['generarsmiprocesoafiliados']){
	$sql_tmp="delete from nacer.smiprocesoafiliados";
	$result1=sql($sql_tmp);
	echo "Se elimino datos de la tabla smiprocesoafiliados <br>";
	$filename = 'smiprocesoafiliados.csv';	

	  	if (!$handle = fopen($filename, 'r')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}
		
    	$cont=0;
    	while (!feof($handle)) {
        $buffer = fgets($handle, 8192);
        $buffer=ereg_replace(chr(9),null,$buffer);
        $buffer=ereg_replace("'",null,$buffer);
        $buffer=explode('"',$buffer);
        //print_r($buffer);
        list($a,$b,$c,$d,$f,$g)=$buffer;
        $b= ereg_replace(',000000',null,$b);
        
        $sql_tmp="INSERT INTO nacer.smiprocesoafiliados
        			( id_procafiliado,periodo,codigocialtadatos)
        			VALUES
        			($b,'$d','$g')";
		sql($sql_tmp);        
        $cont++;
    	}
		 	
    	echo "Se exportaron $cont Registros";
    	fclose($handle);
	
}

if ($_POST['generaexclusionduplicados']){
	$sql_tmp="select * from uad.beneficiarios left join nacer.smiafiliados on (uad.beneficiarios.numero_doc=nacer.smiafiliados.afidni)
			 where (estado_envio='n' and afidni IS NOT NULL and tipo_transaccion='A'and nacer.smiafiliados.activo='S')";
	$result1=sql($sql_tmp)or die;
	$cont=0;
	while (!$result1->EOF){
			$dni=$result1->fields['numero_doc'];
		    $update="UPDATE uad.beneficiarios SET estado_envio='e' where numero_doc='$dni'";	
		    sql($update) or die;
		    $cont++;
			$result1->MoveNext();
	};			
	echo "Se cambio el estado de $cont Registros Duplicados";
}

if ($_POST['migraciondeestadoactivo']){
	$sql_tmp="select clavebeneficiario,activo from nacer.smiafiliados";
	$result1=sql($sql_tmp)or die;
	$cont=0;
	while (!$result1->EOF){
			$clavebeneficiario=$result1->fields['clavebeneficiario'];
			$activo=$result1->fields['activo'];
			$update="UPDATE uad.beneficiarios SET activo='$activo' where clave_beneficiario='$clavebeneficiario'";	
		    sql($update) or die;
		    $cont++;
			$result1->MoveNext();
	};			
	echo "Se cambio el estado de activo de $cont Registros";
}


//codigo para corregir los beneficiarios con estado de envio que no estan en el A

if ($_POST['corregir']){
	$sql_tmp="select distinct clave_beneficiarios from uad.beneficiarios where
	beneficiarios.clave_beneficiarios not in (select clavebeneficiarios from nacer.smiafiliados)";
	$result1=sql($sql_tmp)or die;
	$cont=0;
	while (!$result1->EOF){
			$clavebeneficiario=$result1->fields['clave_beneficiario'];
			$estado_envio="n";
			$update="UPDATE uad.beneficiarios SET estado_envio='$estado_envio' where clave_beneficiario='$clavebeneficiario'";	
		    sql($update) or die;
		    $cont++;
			$result1->MoveNext();
	};			
	echo "Se cambio el estado de envio de $cont Registros";
}

echo $html_header;
?>
<form name=form1 action="genera_archivo.php" method=POST enctype="multipart/form-data">
<table width="80%" class="bordes" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
<tr><td>
  <table width=100% align="center" class="bordes">
  
	<tr id="mo" align="center">
    <td colspan="3" align="center">
    	<font size=+1><b>Importar Archivo</b></font>      	      
    </td>
   </tr>
   		<tr>	           
           <td align="center" colspan="3" id="ma">
            <b> Obra Social Provincial (DOSEP) </b>
           </td>
         </tr>
     <tr>
      <td align="right">		
	    <input type=submit name="generar" value='Genera Archivo OSP' style="width=250px" disabled>
	  </td>
	  <td align="right">		
	    <input type=submit name="generar_n" value='Genera Archivo OSP Nuevo' style="width=250px">
	  </td>
	  
	  <td align="left">		
	    <font color="Red">Debe tener preparada con los datos correspondiente la tabla "public.dosep".</font>
	  </td>
     </tr> 
     
	 
	 <tr>	           
           <td align="center" colspan="3" id="ma">
            <b> NOMIVAC </b>
           </td>
         </tr>
     <tr>
       <td>
		<b>	
		Desde: <input type=text id=fecha_desde name=fecha_desde value='<?=$fecha_desde?>' size=15 readonly>
		<?=link_calendario("fecha_desde");?>
		</b>
		</td>
		
		<td>
		<b>
		Hasta: <input type=text id=fecha_hasta name=fecha_hasta value='<?=$fecha_hasta?>' size=15 readonly>
		<?=link_calendario("fecha_hasta");?> 
		</b>
		</td>
		   
	    <td>
	    &nbsp;&nbsp;&nbsp;
		<input type="submit" name="nomivac" value='Genera Archivo Nomivac' >
	    </td>
	 </tr>
	 
	 
	 <tr>	           
           <td align="center" colspan="3" id="ma">
            <b> Importacion Archivos Sistema</b>
           </td>
         </tr>
     <tr>
     
     
      <tr>
      <tr><td align="center">		
		<font size=1><b> Importacion del archivo C para su Historial</b></font>
	  </td></tr>
	   <td align="right">		
		<input name="archivo" type="file" style="width=250px" id="archivo">
	  </td>
	  <td align="left">		
	    <input type=submit name="importarc" value='Importar' style="width=100px" disabled>
	  </td>
     </tr>
     </tr> 
         
         
     <tr>
      <td align="right">		
	    <input type=submit name="importapuco" value='Importar PUCO' style="width=250px" disabled>
	  </td>
	   <td align="left">		
	    <font color="Red">Debe copiar archivo puco.txt a la carpeta "sistema\modulos\nacer". Genera Archivo puco_ok.txt para hacer proceso de importacion manual a la tabla puco.puco</font>
	  </td>
     </tr>
     <tr>
      <td align="right">        
        <input type=submit name="importapucocompleto" value='Importar PUCO Completo' style="width=250px" disabled>
      </td>
       <td align="left">        
        <font color="Red">Debe copiar archivo puco.txt a la carpeta "sistema\modulos\nacer". Este proceso hace la importacion directamente en la tabla puco.puco.</font>
      </td>
     </tr>
     <tr>
      <td align="right">		
	    <input type=submit name="generarsmiafiliados" value='Importar Smiafiliados' style="width=250px" disabled>
	  </td>
	   <td align="left">		
	    <font color="Red">Debe copiar archivo smiafiliados.csv a la carpeta "sistema\modulos\nacer"</font>
	  </td>
     </tr>
     <tr>
      <td align="right">		
	    <input type=submit name="generarsmiafiliadosaux" value='Importar Smiafiliadosaux' style="width=250px" disabled>
	  </td>
	 <td align="left">		
	    <font color="Red">Debe copiar archivo smiafiliadosaux.csv a la carpeta "sistema\modulos\nacer"</font>
	  </td>
     </tr>
     <tr>
      <td align="right">		
	    <input type=submit name="generarsmiefectores" value='Importar Smiefectores' style="width=250px" disabled>
	  </td>
	  <td align="left">		
	    <font color="Red">Debe copiar archivo smiefectores.csv a la carpeta "sistema\modulos\nacer"</font>
	  </td>
     </tr>
     <tr>
      <td align="right">	
	    <input type=submit name="generarsmiprocesoafiliados" value='Importar Smiprocesoafiliados' style="width=250px" disabled>
	  </td>
	  <td align="left">		
	    <font color="Red">Debe copiar archivo smiprocesoafiliados.csv a la carpeta "sistema\modulos\nacer"</font>
	  </td>
     </tr>
     <tr>
      <td align="right">	
	    <input type=submit name="generaexclusionduplicados" value='Depuracion Beneficiarios Duplicados' style="width=250px" disabled>
	  </td>
	  <td align="left">		
	    <font color="Red">Se generara una depuracion de los beneficiarios duplicados (uad.beneficiarios)"</font>
	  </td>
	  
	  </tr>
     
      <td align="right">	
	    <input type=submit name="migraciondeestadoactivo" value='Cambia el estado de Activo en uad.beneficiarios' style="width=250px" disabled>
	  </td>
	  <td align="left">		
	    <font color="Red">Se cambia los estado de Activo dentro de la tabla uad.beneficiarios"</font>
	  </td>
     </table>

     
       </td>
     </tr>
</table>

</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>