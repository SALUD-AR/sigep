<?php
require_once("../../config.php");
     $periodo=substr($fechaemp,0,4).substr($fechaemp,5,2);  
	 $fecha_actual=date("Y-m-d");
$sql_tmp2="SELECT distinct beneficiarios.id_beneficiarios,beneficiarios.estado_envio,beneficiarios.clave_beneficiario,beneficiarios.tipo_transaccion,beneficiarios.apellido_benef
,beneficiarios.nombre_benef,beneficiarios.clase_documento_benef,beneficiarios.tipo_documento,beneficiarios.numero_doc,beneficiarios.id_categoria,beneficiarios.sexo
,beneficiarios.fecha_nacimiento_benef,beneficiarios.provincia_nac,beneficiarios.localidad_nac,beneficiarios.pais_nac,beneficiarios.indigena,beneficiarios.id_tribu
,beneficiarios.id_lengua,beneficiarios.alfabeta,beneficiarios.estudios,beneficiarios.anio_mayor_nivel,beneficiarios.tipo_doc_madre,beneficiarios.nro_doc_madre
,beneficiarios.apellido_madre,beneficiarios.nombre_madre,beneficiarios.alfabeta_madre,beneficiarios.estudios_madre,beneficiarios.anio_mayor_nivel_madre
,beneficiarios.tipo_doc_padre,beneficiarios.nro_doc_padre,beneficiarios.apellido_padre,beneficiarios.nombre_padre,beneficiarios.alfabeta_padre
,beneficiarios.estudios_padre,beneficiarios.anio_mayor_nivel_padre,beneficiarios.tipo_doc_tutor,beneficiarios.nro_doc_tutor,beneficiarios.apellido_tutor
,beneficiarios.nombre_tutor,beneficiarios.alfabeta_tutor,beneficiarios.estudios_tutor,beneficiarios.anio_mayor_nivel_tutor,beneficiarios.fecha_diagnostico_embarazo
,beneficiarios.semanas_embarazo,beneficiarios.fecha_probable_parto,beneficiarios.fecha_efectiva_parto,beneficiarios.cuie_ea,beneficiarios.cuie_ah
,beneficiarios.menor_convive_con_adulto,case when trim(beneficiarios.calle)='' then 'S/D' else beneficiarios.calle end as calle
,case trim(beneficiarios.numero_calle) when '' then 'S/D' when 'S/D' then 'verob' when 'S/N' then 'verob' else beneficiarios.numero_calle end as numero_calle
,beneficiarios.piso,beneficiarios.dpto,beneficiarios.manzana
,beneficiarios.entre_calle_1,beneficiarios.entre_calle_2,beneficiarios.telefono 
,case when trim(beneficiarios.departamento)='' then 'S/D' else beneficiarios.departamento end as departamento
,case when trim(beneficiarios.localidad)='' then 'S/D' else beneficiarios.localidad end as localidad
,case when trim(beneficiarios.municipio)=''then 'S/D' else beneficiarios.municipio end as municipio
,case when trim(beneficiarios.cod_pos)='' then 'S/D' else beneficiarios.cod_pos end as cod_pos
,case when trim(beneficiarios.barrio)='' then 'S/D' else beneficiarios.barrio end as barrio
,beneficiarios.observaciones,beneficiarios.fecha_inscripcion,beneficiarios.fecha_carga,beneficiarios.usuario_carga
,beneficiarios.activo,beneficiarios.score_riesgo,beneficiarios.mail,beneficiarios.celular,beneficiarios.otrotel,beneficiarios.estadoest,beneficiarios.fum
,beneficiarios.obsgenerales,beneficiarios.estadoest_madre,beneficiarios.tipo_ficha,beneficiarios.responsable,beneficiarios.discv,beneficiarios.disca
,beneficiarios.discmo,beneficiarios.discme,beneficiarios.otradisc,beneficiarios.estadoest_padre,beneficiarios.estadoest_tutor,beneficiarios.menor_embarazada
,beneficiarios.apellido_benef_otro,beneficiarios.nombre_benef_otro,beneficiarios.fecha_verificado,beneficiarios.usuario_verificado,beneficiarios.apellidoagente
,beneficiarios.nombreagente,beneficiarios.centro_inscriptor,beneficiarios.dni_agente,smiefectores.nombreefector,relacioncodigos.codremediar
,MAX(remediar_x_beneficiario.fechaempadronamiento)fechaempadronamiento
			FROM uad.beneficiarios
			left join facturacion.smiefectores on beneficiarios.cuie_ea=smiefectores.cuie
			inner join general.relacioncodigos on beneficiarios.cuie_ea=relacioncodigos.cuie
			left join uad.remediar_x_beneficiario on remediar_x_beneficiario.clavebeneficiario=beneficiarios.clave_beneficiario ";

//$sql_tmp2.=" WHERE beneficiarios.estado_envio in ('e','n') ";
//$sql_tmp2.="  length(beneficiarios.numero_doc) in (7,8) ";
$sql_tmp2.=" 			WHERE beneficiarios.id_beneficiarios not in (select id_beneficiarios from remediar.listado_enviados) ";
//$sql_tmp2.=" 			AND NOT EXISTS(SELECT tipo_doc, documento	FROM puco.puco WHERE tipo_doc = tipo_documento AND documento = CAST(numero_doc AS INT)) ";
$sql_tmp2.=" 			AND remediar_x_beneficiario.fechaempadronamiento <= '$fechaemp' ";
$sql_tmp2.=" 			AND beneficiarios.fecha_carga <= '$fechakrga' ";
// es reemplazado por linea de abajo...$sql_tmp2.=" 			AND remediar_x_beneficiario.fechaempadronamiento - DATE(beneficiarios.fecha_nacimiento_benef)>= 2190 ";
//reemp. por linea de abajo - ped. x Bettina - 16/01 Pedro $sql_tmp2.=" 			AND '$fecha_actual' - DATE(beneficiarios.fecha_nacimiento_benef)>= 2190 ";
$sql_tmp2.=" 			AND '$fechaemp' - DATE(beneficiarios.fecha_nacimiento_benef)>= 2190 ";
$sql_tmp2.=" 			AND relacioncodigos.codremediar IS NOT NULL ";
// muy importante, codremediar<>'' significa que solo algunos efectores tienen remediar
$sql_tmp2.=" 			AND trim(relacioncodigos.codremediar) <>''";
$sql_tmp2.=" 			AND remediar_x_beneficiario.fechaempadronamiento NOTNULL ";
// se acrodo sacarlo...$sql_tmp2.=" 			AND CAST(beneficiarios.numero_doc AS INT) BETWEEN 999999 AND 100000000 ";
$sql_tmp2.=" 			AND tipo_ficha in ('1','3') ";
$sql_tmp2.=" 			AND beneficiarios.fallecido ='n' ";
//agregado filtro por estado de envio - 16/12 Pedro - pedido por Bettina
$sql_tmp2.=" 			AND beneficiarios.estado_envio ='n' "; 
//$sql_tmp2.=" 			AND beneficiarios.calle ='n' AND beneficiarios.numero_calle ='n' AND beneficiarios.calle ='n' AND beneficiarios.calle ='n' ";
							
$sql_tmp2.=" GROUP BY 
beneficiarios.id_beneficiarios,beneficiarios.estado_envio,beneficiarios.clave_beneficiario,beneficiarios.tipo_transaccion,beneficiarios.apellido_benef
,beneficiarios.nombre_benef,beneficiarios.clase_documento_benef,beneficiarios.tipo_documento,beneficiarios.numero_doc,beneficiarios.id_categoria,beneficiarios.sexo
,beneficiarios.fecha_nacimiento_benef,beneficiarios.provincia_nac,beneficiarios.localidad_nac,beneficiarios.pais_nac,beneficiarios.indigena,beneficiarios.id_tribu
,beneficiarios.id_lengua,beneficiarios.alfabeta,beneficiarios.estudios,beneficiarios.anio_mayor_nivel,beneficiarios.tipo_doc_madre,beneficiarios.nro_doc_madre
,beneficiarios.apellido_madre,beneficiarios.nombre_madre,beneficiarios.alfabeta_madre,beneficiarios.estudios_madre,beneficiarios.anio_mayor_nivel_madre
,beneficiarios.tipo_doc_padre,beneficiarios.nro_doc_padre,beneficiarios.apellido_padre,beneficiarios.nombre_padre,beneficiarios.alfabeta_padre
,beneficiarios.estudios_padre,beneficiarios.anio_mayor_nivel_padre,beneficiarios.tipo_doc_tutor,beneficiarios.nro_doc_tutor,beneficiarios.apellido_tutor
,beneficiarios.nombre_tutor,beneficiarios.alfabeta_tutor,beneficiarios.estudios_tutor,beneficiarios.anio_mayor_nivel_tutor,beneficiarios.fecha_diagnostico_embarazo
,beneficiarios.semanas_embarazo,beneficiarios.fecha_probable_parto,beneficiarios.fecha_efectiva_parto,beneficiarios.cuie_ea,beneficiarios.cuie_ah
,beneficiarios.menor_convive_con_adulto,case when trim(beneficiarios.calle)='' then 'S/D' else beneficiarios.calle end 
,case trim(beneficiarios.numero_calle) when '' then 'S/D' when 'S/D' then 'verob' when 'S/N' then 'verob' else beneficiarios.numero_calle end
,beneficiarios.piso,beneficiarios.dpto,beneficiarios.manzana
,beneficiarios.entre_calle_1,beneficiarios.entre_calle_2,beneficiarios.telefono 
,case when trim(beneficiarios.departamento)=''then 'S/D' else beneficiarios.departamento end
,case when trim(beneficiarios.localidad)='' then 'S/D' else beneficiarios.localidad end 
,case when trim(beneficiarios.municipio)=''then 'S/D' else beneficiarios.municipio end 
,case when trim(beneficiarios.cod_pos)='' then 'S/D' else beneficiarios.cod_pos end 
,case when trim(beneficiarios.barrio)='' then 'S/D' else beneficiarios.barrio end 
,beneficiarios.observaciones,beneficiarios.fecha_inscripcion,beneficiarios.fecha_carga,beneficiarios.usuario_carga
,beneficiarios.activo,beneficiarios.score_riesgo,beneficiarios.mail,beneficiarios.celular,beneficiarios.otrotel,beneficiarios.estadoest,beneficiarios.fum
,beneficiarios.obsgenerales,beneficiarios.estadoest_madre,beneficiarios.tipo_ficha,beneficiarios.responsable,beneficiarios.discv,beneficiarios.disca
,beneficiarios.discmo,beneficiarios.discme,beneficiarios.otradisc,beneficiarios.estadoest_padre,beneficiarios.estadoest_tutor,beneficiarios.menor_embarazada
,beneficiarios.apellido_benef_otro,beneficiarios.nombre_benef_otro,beneficiarios.fecha_verificado,beneficiarios.usuario_verificado,beneficiarios.apellidoagente
,beneficiarios.nombreagente,beneficiarios.centro_inscriptor,beneficiarios.dni_agente,smiefectores.nombreefector,relacioncodigos.codremediar";
		
		//echo  '?br>'.$sql_tmp2.'<br>' ;
						
$result12=sql($sql_tmp2) or die;


    	$result12->movefirst();
    	$user2 = $result12->fields['usuario_carga'];
    	$id_user2 = $result12->fields['usuario_carga'];
    	if (!$result12->EOF) {
    	  	
    	$resultP2=sql("select * from uad.parametros") or die;
   		$resultP2->movefirst();
   		$cod_uad2 = $resultP2->fields['codigo_uad'];
  		$cod_prov2 = $resultP2->fields['codigo_provincia'];
  		
  		
  		//$resultU2=sql("select id_usuario from sistema.usuarios where substr(usuarios.nombre,0,10)='$user2'");
  		//$id_user2 = $resultU2->fields['id_usuario'];
  		

/////HEADER
    		$contenido2.='"H"';
    		$contenido2.=';';
			$contenido2.=$fecha_actual;
			//$contenido2.=';';
			//$contenido2.=$result12->fields['id_localidad'];
			$contenido2.=';';
			//$contenido2.=$cod_uad; //$id_user; //10
			$contenido2.=$id_user2;
			$contenido2.=';';

    		if (!$resultP2->EOF) {

    		$contenido2.=$cod_prov2;	//--2	Dos Primeras Letras? O el Id?
    		$contenido2.=';';
	  		$contenido2.=$cod_uad2; //UAD	//3	Ejemplo?
	  		$contenido2.=';';
    		$cod_ci2 = $resultP2->fields['codigo_ci'];
			$contenido2.=$cod_ci2;
			$contenido2.=';';
    		

			//genero nombre de archivo
			$filename_remediar= 'BR'.$cod_prov2.$periodo.'.txt';

			//creo y abro el archivo
    		if (!$handle2 = fopen("../remediar/".$filename_remediar, "w")) { //'a'
        	 echo "<br>No se Puede abrir ($filename_remediar)";
         	exit;
    		}else {
    			ftruncate($handle2,filesize("../remediar/".$filename_remediar));
     		}
			// fin gen archivo, sigo con la cadenas
			

    		}
    		
    		//$result1AE=sql("select max(id_archivos_enviados) from uad.archivos_enviados") or die;
    		//$result1AE->movefirst();
    		//if (!$resultAE->EOF) {
			//$contenido2.=$result1AE->fields['id_archivos_enviados'];  //secuencia
			//$contenido2.=';';
    		//}
/*VersionAplicativo	10	Agregado en versi n 2. Si no viene nada, asumimos que es la versi n anterior. La versi n del aplicativo indica si vienen o no vienen la info de campos modificados.
En la versi n 2.0, este campo vendr  con el texto  2.0 
*/			$contenido2.=$periodo;
    		/*$contenido2.=';';
			$contenido2.="4.1";*/
			$contenido2.=';';
			$contenido2.="
";

			$where2='';
			$where2_2='';
    	while (!$result12->EOF) {
			$where2.=',';
			$where2_2.=',';
/////////DATOS
			$contenido2.='"D"';
			$contenido2.=';';
			$id_beneficiario2 = $result12->fields['clave_beneficiario'];
			/*$where.=$id_beneficiario;*/
			$where2.=$result12->fields['id_beneficiarios'];
			$where2_2.=$result12->fields['clave_beneficiario'];
			
    		if (strlen($id_beneficiario2) < 16) {$id_beneficiario2 = str_repeat("0",16-strlen($id_beneficiario2)).$id_beneficiario2;}
    	
			$contenido2.=$id_beneficiario2;//$id_beneficiario2;
			$contenido2.=';';
			$contenido2.='"'.$result12->fields['apellido_benef'].' '.$result12->fields['apellido_benef_otro'].'"';	//30	Uad.Beneficiarios.apellido
			$contenido2.=';';
			$contenido2.='"'.$result12->fields['nombre_benef'].' '.$result12->fields['nombre_benef_otro'].'"';	//30	Uad.Beneficiarios.nombre
			$contenido2.=';';
			$contenido2.='"'.$result12->fields['tipo_documento'].'"';	//5	Sigla (DNI, CUIL, etc)
			$contenido2.=';';
			$contenido2.='"'.$result12->fields['clase_documento_benef'].'"';	//1	Propio o Ajeno? Si es ajeno, seria el dni de quien hace el tramite?
			$contenido2.=';';
			$contenido2.=$result12->fields['numero_doc'];	//12	
			$contenido2.=';';
			$contenido2.='"'.$result12->fields['sexo'].'"';	//1	M / F
			$contenido2.=';';
			$id_categoria2 = $result12->fields['id_categoria'];
			$contenido2.='';//$id_categoria2;	//1	Valores de 1 a 4
			$contenido2.=';';
			$contenido2.=$result12->fields['fecha_nacimiento_benef'];	//10	AAAA-MM-DD (A o, Mes, D a)
			$contenido2.=';';
			$indigena2 = $result12->fields['indigena'];
			$contenido2.='';//$indigena2 ;	//1	S/N
			$contenido2.=';';
			$id2 = $result12->fields['id_lengua'];
			if (is_numeric($id) == 0) { $id = 0;}
			$contenido2.='';//$id2;	//5	N mero de identificaci n de lengua
			$contenido2.=';';
    		$id2 = $result12->fields['id_tribu'];
			if (is_numeric($id2) == 0) { $id2 = 0;}
			//$tribu = str_replace(null,0,$result12->fields['id_tribu']);
			$contenido2.='';//$id2;	//5	N mero de tribu
			$contenido2.=';';
			$contenido2.='""';//$result12->fields['tipo_doc_madre'];	//5	
			$contenido2.=';';
			$contenido2.='';//$result12->fields['nro_doc_madre'];	//12	
			$contenido2.=';';
			$contenido2.='""';//$result12->fields['apellido_madre'];	//30	
			$contenido2.=';';
			$contenido2.='""';//$result12->fields['nombre_madre'];	//30	
			$contenido2.=';';
			$contenido2.='""';//$result12->fields['tipo_doc_padre'];	//5	
			$contenido2.=';';
			$contenido2.='';//$result12->fields['nro_doc_padre'];	//12	
			$contenido2.=';';
			$contenido2.='""';//$result12->fields['apellido_padre'];	//30	
			$contenido2.=';';
			$contenido2.='""';//$result12->fields['nombre_padre'];	//30	
			$contenido2.=';';
			$contenido2.='""';//$result12->fields['tipo_doc_tutor'];	//5	
			$contenido2.=';';
			$contenido2.='';//$result12->fields['nro_doc_tutor'];	//12	
			$contenido2.=';';
			$contenido2.='""';//$result12->fields['apellido_tutor'];	//30	
			$contenido2.=';';
			$contenido2.='""';//$result12->fields['nombre_tutor'];	//30	
			$contenido2.=';';
			$contenido2.=substr($result12->fields['fechaempadronamiento'],0,10);	//10	
			$contenido2.=';';
			//cambio formato de fecha
			$fecha_carga2=substr($result12->fields['fecha_carga'],0,10);
			$fechaParaInsertar2= '1899-12-30';
			/*$fechaExplode = explode("/", $fecha_carga);
			$fechaParaInsertar = date("Y-m-d", mktime(0,0,0,$fechaExplode[1], $fechaExplode[0], $fechaExplode[2]));*/
			// inserto nueva fecha
			$contenido2.='';//$fechaParaInsertar2;	
			$contenido2.=';';
			
			
				
			
	
			
			$fecha_d_emb2 = $result12->fields['fecha_diagnostico_embarazo'];
			$contenido2.='';//$fecha_d_emb2;	//10	
			$contenido2.=';';
			
			$sem_emb2 = $result12->fields['semanas_embarazo']; 	//3
			$contenido2.='';//$sem_emb2;	//3	
			$contenido2.=';';
				
			$fecha_pr_parto2=$result12->fields['fecha_probable_parto'];
			$contenido2.='';//$fecha_pr_parto2;
			$contenido2.=';';
				
			$fecha_ef_parto2=$result12->fields['fecha_efectiva_parto'];
			$contenido2.= '';//$fecha_ef_parto2;	//10	Fecha del parto o de la interrupci n del embarazo
			$contenido2.=';';
			
			if ($result12->fields['activo'] == 1) {$activo2 = 'S';} else {$activo2 = 'N';}
			$contenido2.='';//$activo2;	//1	Si/No   Campo para el borrado logico
			$contenido2.=';';
			if($result12->fields['calle']==''){
				$calle='S/D';
			}else{ $calle=$result12->fields['calle']; } 
			$contenido2.='"'.$calle.'"';	//40	
			$contenido2.=';';
			$contenido2.='"'.$result12->fields['numero_calle'].'"';	//5	
			$contenido2.=';';
			$contenido2.='"'.$result12->fields['manzana'].'"';	//5	
			$contenido2.=';';
			$contenido2.=$result12->fields['piso'];	//5	
			$contenido2.=';';
			$contenido2.='"'.$result12->fields['dpto'].'"';	//5	
			$contenido2.=';';
			$contenido2.='""';//$result12->fields['entre_calle_1'];	//40	
			$contenido2.=';';
			$contenido2.='""';//$result12->fields['entre_calle_2'];	//40	
			$contenido2.=';';
			$contenido2.='"'.str_replace('-1','',$result12->fields['barrio']).'"';	//40	
			$contenido2.=';';
			$contenido2.='"'.str_replace('-1','',$result12->fields['municipio']).'"';	//40	
			$contenido2.=';';
			$contenido2.='"'.str_replace('-1','',$result12->fields['departamento']).'"';	//40	
			$contenido2.=';';
			$contenido2.='"'.str_replace('-1','',$result12->fields['localidad']).'"';	//40	
			$contenido2.=';';
			$contenido2.=$result12->fields['cod_pos']; //DomCodigoPostal	
			$contenido2.=';';
			$contenido2.=$cod_prov2;//$result12->fields['provincia_nac'];
			$contenido2.=';';
			$contenido2.='"'.$result12->fields['telefono'].'"';	//20	
			$contenido2.=';';
			
			$contenido2.='""';//$result12->fields['cuie_ea']; //LugarAtencionHabitual	80	Efector
			$contenido2.=';';
			//$id_nov += 1;
			
			$contenido2.= '';//$id_nov2; //id_novedad=id_beneficiario
			$contenido2.=';';
			$contenido2.='""';//$result12->fields['tipo_transaccion']; // TipoNovedad
			$contenido2.=';'; 
			$contenido2.='';//substr($result12->fields['fecha_carga'],0,10); //FechaNovedad	10	Fecha en la que se produjo la novedad. Fundamentalmente se utilizar  para la fecha de baja.
			$contenido2.=';'; 
			$contenido2.='""';//$cod_prov2;//CodigoProvinciaAltaDatos	2	
			$contenido2.=';'; 
			$contenido2.='""';//$cod_uad2; //CodigoUADAltaDatos	3
			$contenido2.=';'; 	
			$contenido2.='""';//$cod_ci2; //CodigoCIAltaDatos	5
			$contenido2.=';'; 
			$contenido2.=substr($result12->fields['fecha_carga'],0,10); //FechaCarga
			$contenido2.=';';
			$contenido2.='';//$id_user2; //UsuarioCarga - NO VA !!! QUITARR TODO lo referente
			$contenido2.=';';
			$contenido2.='';//$cod_uad; // checkSum
			$contenido2.=';';
			$contenido2.='';//$result12->fields['cuie_ea']; //Efector
			$contenido2.=';';
			$contenido2.='"'.$result12->fields['codremediar'].'"'; //Efector
			$contenido2.=';';
			$contenido2.=$result12->fields['score_riesgo']; //score_riesgo
			$contenido2.=';';
			$contenido2.='""';
			$contenido2.=';';
			$contenido2.='';
			$contenido2.=';';
			$contenido2.='""';
			$contenido2.=';';
			$contenido2.='';
			$contenido2.=';';
			$contenido2.='""';
			$contenido2.=';';
			$contenido2.='';
			$contenido2.=';';
			$contenido2.='""';
			$contenido2.=';';
			$contenido2.='';
			//for($i=1; $i<70; $i++){  //ClaveBinaria	70	Indica con una m scara de ceros y unos, cu les campos fueron modificados.
			//	$contenido2.="1";
			//}
			$contenido2.=';';
			$contenido2.="
";	
	   		$result12->MoveNext();
    	}
    	
////// TRAILER
    	$contenido2.='"T"';
    	$contenido2.=';';
    	$cantidad_registros2=$result12->numRows();
		$contenido2.=$cantidad_registros2; // CantidadRegistros	6	Cantidad de registros que vinieron
		$contenido2.=';';
		$contenido2.="
";
		
		if ($result12->EOF) {
		if (fwrite($handle2, $contenido2) === FALSE) {
        		echo "<br>No se Puede escribir  ($filename_remediar)";
        		exit;
    		}
		else {	
			$where2=substr($where2,1,strlen($where2));
			$where2_2=substr($where2_2,1,strlen($where2_2));
			echo "<br>El Archivo ($filename_remediar) se genero con exito";
			$fecha_generacion=date("Y-m-d H:m:s");
			$partes=explode(',',$where2) ;
			$partes2=explode(',',$where2_2) ;
			$i=0; $cuenta=0;
			while ($i< strlen($where2)){ 
				if ($where2[$i]==","){ $cuenta++;}
				$i++; 
			} 
			$j=0; 
			while ($j<= $cuenta){ 
			$consulta2_2= "update uad.remediar_x_beneficiario set enviado='s'  where clavebeneficiario='$partes2[$j]'";
			sql($consulta2_2, "Error al insertar en archivos enviados") or fin_pagina(); 											
			$consulta2= "insert into remediar.listado_enviados(fecha_generacion,usuario,nombre_archivo_enviado,id_beneficiarios,puco) values('$fecha_generacion','$user2','$filename_remediar',$partes[$j],'$periodo_puco')";
			sql($consulta2, "Error al insertar en archivos enviados") or fin_pagina(); 
			
				$j++;
			}
	
			}
		}
		else {echo "<br>No hay registros para generar";}
	
    	fclose($handle2);
    	}
		else {echo "<br>No hay registros para generar";}
//var_dump($contenido2);