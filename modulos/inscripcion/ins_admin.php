<?
require_once ("../../config.php");
include_once('lib_inscripcion.php');

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();
//Coloca fechas a fecha actual pq los valores estan programados con una fecha de 1890 en formato de base de datos, por ello al habilitar los valores deja la fecha con el valor q corresponde.
($_POST['fecha_nac']=='')?$fecha_nac=date("d/m/Y"):$fecha_nac=$_POST['fecha_nac'];
($_POST['fum']=='')?$fum=date("d/m/Y"):$fum=$_POST['fum'];
($_POST['fecha_diagnostico_embarazo']=='')?$fecha_diagnostico_embarazo=date("d/m/Y"):$fecha_diagnostico_embarazo=$_POST['fecha_diagnostico_embarazo'];
($_POST['fecha_probable_parto']=='')?$fecha_probable_parto=date("d/m/Y"):$fecha_probable_parto=$_POST['fecha_probable_parto'];
($_POST['fecha_efectiva_parto']=='')?$fecha_efectiva_parto=date("d/m/Y"):$fecha_efectiva_parto=$_POST['fecha_efectiva_parto'];
($_POST['fecha_inscripcion']=='')?$fecha_inscripcion=date("d/m/Y"):$fecha_inscripcion=$_POST['fecha_inscripcion'];
$edad=$_POST['edades'];

function calculo_dias($fecha_eq){ // calculamos la diferencia de dias en entero 
		//defino fecha 1
		$anio1 = date('Y');
		$mes1 = date('m');
		$dia1 = date('d');
		//defino fecha 2			
			
		 $dia2 = substr($fecha_eq,0,2);
		 $mes2 = substr($fecha_eq,3,-5);
		 $anio2 = substr($fecha_eq,6,9);
		//calculo timestam de las dos fechas
		$timestamp1 = mktime(0,0,0,$mes1,$dia1,$anio1);
		$timestamp2 = mktime(0,0,0,$mes2,$dia2,$anio2); 
		//resto a una fecha la otra
		$segundos_diferencia = $timestamp1 - $timestamp2;
		//echo $segundos_diferencia;
		
		//convierto segundos en días
		/*$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);
		//obtengo el valor absoulto de los días (quito el posible signo negativo)
		$dias_diferencia = abs($dias_diferencia);
		
		//quito los decimales a los días de diferencia
		$dias_diferencia = floor($dias_diferencia); */
		 return ($dias_diferencia); 
}

// Update de Beneficiarios
if ($_POST['guardar_editar']=="Guardar"){
   $db->StartTrans();
   $fecha_carga=date("Y-m-d H:m:s");
   $usuario=$_ses_user['login'];
   /*$usuario = substr($usuario,0,9);*/
   	$fecha_nac=Fecha_db($fecha_nac);
   	$fum=Fecha_db($fum);
   	$fecha_diagnostico_embarazo=Fecha_db($fecha_diagnostico_embarazo);
   	$semanas_embarazo=$_POST['semanas_embarazo'];
   	$fecha_probable_parto=Fecha_db($fecha_probable_parto);
   	$clave_beneficiario=$_POST['clave_beneficiario'];
   	$alfabeta=$_POST['alfabeta'];
   	$sexo=$_POST['sexo'];
   	$pais_nac=$_POST['pais_nac'];
   	$indigena=$_POST['indigena'];
    $id_tribu=$_POST['id_tribu'];
    $id_lengua= $_POST['id_lengua'];
    $departamento=$_POST['departamento'];
   	$localidad=$_POST['localidad'];
   	$municipio=$_POST['municipio'];
   	$barrio=$_POST['barrio'];
   	$estudios=$_POST['estudios'];
   	$id_categoria=$_POST['id_categoria'];
	$anio_mayor_nivel=$_POST['anio_mayor_nivel'];
	$responsable=$_POST['responsable'];
	$menor_convive_con_adulto=$_POST['menor_convive_con_adulto'];
	$tipo_doc_madre=$_POST['tipo_doc_madre'];
	$nro_doc_madre=$_POST['nro_doc_madre'];
	$apellido_madre=$_POST['apellido_madre'];
	$nombre_madre=$_POST['nombre_madre'];
	$estudios_madre=$_POST['estudios_madre'];
	$anio_mayor_nivel_madre=$_POST['anio_mayor_nivel_madre'];
   	$score_riesgo=$_POST['score_riesgo'];
   	$mail=$_POST['mail'];
	$celular=$_POST['celular'];
	$otrotel=$_POST['otrotel'];
	$estadoest=$_POST['estadoest'];
	$discv=$_POST['discv'];
	$disca=$_POST['disca'];
	$discmo=$_POST['discmo'];
	$discme=$_POST['discme'];
	$otradisc=$_POST['otradisc'];
	$obsgenerales=$_POST['obsgenerales'];
	$estadoest_madre=$_POST['estadoest_madre'];
	$menor_embarazada=$_POST['menor_embarazada'];
	$clase_doc=$_POST['clase_doc'];	
	$fecha_inscripcion=Fecha_db($fecha_inscripcion);

		echo $semanas_embarazo;
    if($menor_embarazada=='N'){
		$fecha_diagnostico_embarazo='1899-12-30';
		$semanas_embarazo=0;
    	$fecha_probable_parto='1899-12-30';
    	$fecha_efectiva_parto='1899-12-30';	
    	$fum='1899-12-30';
    }
    else $fecha_efectiva_parto='1899-12-30';	
     //Responsable Padre, menor no embarazada o menor de 9 años (Insert)
    if($responsable=='PADRE'){

    	$estadoest_madre='';
    	$tipo_doc_padre=$tipo_doc_madre;
    	$nro_doc_padre=$nro_doc_madre;
    	$apellido_padre=$apellido_madre;
    	$nombre_padre=$nombre_madre;
    	$alfabeta_padre=$alfabeta_madre;
    	$estudios_padre=$estudios_madre;
    	$anio_mayor_nivel_padre=$anio_mayor_nivel_madre;
    	$estadoest_padre=$estadoest_madre;
    	$tipo_doc_madre='';
    	$nro_doc_madre=0;
    	$apellido_madre='';
    	$nombre_madre='';
    	$alfabeta_madre='';
    	$estudios_madre='';
    	$anio_mayor_nivel_madre=0;
    	$tipo_doc_tutor='';
    	$nro_doc_tutor=0;
    	$apellido_tutor='';
    	$nombre_tutor='';
    	$alfabeta_tutor='';
    	$estudios_tutor='';
    	$anio_mayor_nivel_tutor=0;
    	$estadoest_tutor='';
    	
    }elseif ($responsable== 'TUTOR' ){
    	
    	$tipo_doc_tutor=$tipo_doc_madre;
    	$nro_doc_tutor=$nro_doc_madre;
    	$apellido_tutor=$apellido_madre;
    	$nombre_tutor=$nombre_madre;
    	$alfabeta_tutor=$alfabeta_madre;
    	$estudios_tutor=$estudios_madre;
    	$anio_mayor_nivel_tutor=$anio_mayor_nivel_madre;
    	$estadoest_tutor=$estadoest_madre;
    	$tipo_doc_madre='';
    	$nro_doc_madre=0;
    	$apellido_madre='';
    	$nombre_madre='';
    	$alfabeta_madre='';
    	$estudios_madre='';
    	$anio_mayor_nivel_madre=0;
    	$estadoest_madre='';
    	$tipo_doc_padre='';
    	$nro_doc_padre=0;
    	$apellido_padre='';
    	$nombre_padre='';
    	$alfabeta_padre='';
    	$estudios_padre='';
    	$anio_mayor_nivel_padre=0;
    	$estadoest_padre='';

    } elseif ($responsable!= 'MADRE'){
    	 
    	 $tipo_doc_madre='';
    	$nro_doc_madre=0;
    	$apellido_madre='';
    	$nombre_madre='';
    	$alfabeta_madre='';
    	$estudios_madre='';
    	$anio_mayor_nivel_madre=0;
    	$estadoest_madre='';
    	$tipo_doc_padre='';
    	$nro_doc_padre=0;
    	$apellido_padre='';
    	$nombre_padre='';
    	$alfabeta_padre='';
    	$estudios_padre='';
    	$anio_mayor_nivel_padre=0;
    	$estadoest_padre='';
    	$tipo_doc_tutor='';
    	$nro_doc_tutor=0;
    	$apellido_tutor='';
    	$nombre_tutor='';
    	$alfabeta_tutor='';
    	$estudios_tutor='';
    	$anio_mayor_nivel_tutor=0;
    	$estadoest_tutor='';
    }else {
    	
    	 	$tipo_doc_padre='';
    	$nro_doc_padre=0;
    	$apellido_padre='';
    	$nombre_padre='';
    	$alfabeta_padre='';
    	$estudios_padre='';
    	$anio_mayor_nivel_padre=0;
    	$estadoest_padre='';
    	$tipo_doc_tutor='';
    	$nro_doc_tutor=0;
    	$apellido_tutor='';
    	$nombre_tutor='';
    	$alfabeta_tutor='';
    	$estudios_tutor='';
    	$anio_mayor_nivel_tutor=0;
    	$estadoest_tutor='';
    }
	
	//en caso de no ser embarazada colocamos a todos los campos valores por default
$query1="SELECT tipo_transaccion,estado_envio
			FROM uad.beneficiarios
			left join nacer.efe_conv on beneficiarios.cuie_ea=efe_conv.cuie 
  	where id_beneficiarios=$id_planilla";

	$result1=sql($query1, "Error en consulta 1") or fin_pagina();
	//Datos de la inscripcion
		     $trans_tip=trim($result1->fields['tipo_transaccion']);
		    $estado_env=trim($result1->fields['estado_envio']);
	
	if ($estado_env=='n' and $trans_tip=='A') {
		$tipo_transaccion='A';		
	}else $tipo_transaccion='M';
	
 	$dias=calculo_dias(fecha($result->fields['$fecha_inscripcion']));
   	$query = "update uad.beneficiarios set 
   			estado_envio='n', tipo_transaccion='$tipo_transaccion',
             cuie_ea='$cuie', id_categoria=$id_categoria, tipo_ficha='2', observaciones=upper('$observaciones'), 
             fecha_inscripcion='$fecha_inscripcion',fecha_carga='$fecha_carga', usuario_carga=upper('$usuario'),
             nombre_benef=upper('$nombre'), apellido_benef=upper('$apellido'), numero_doc='$num_doc', fecha_nacimiento_benef='$fecha_nac',
             clase_documento_benef=upper('$clase_doc'), pais_nac=upper('$paisn'),
             alfabeta=upper('$alfabeta'),estudios=upper('$estudios'), anio_mayor_nivel='$anio_mayor_nivel',
             indigena=upper('$indigena'),id_tribu=$id_tribu,id_lengua=$id_lengua,
             calle=upper('$calle'),numero_calle='$numero_calle',piso='$piso',dpto=upper('$dpto'),manzana='$manzana',entre_calle_1=upper('$entre_calle_1'),
             entre_calle_2=upper('$entre_calle_2'), departamento=upper('$departamenton'), localidad=upper('$localidadn'), municipio=upper('$municipion'), 
             barrio=upper('$barrion'),telefono='$telefono',cod_pos='$cod_posn',
              mail=upper('$mail'), celular='$celular',otrotel='$otrotel', estadoest=upper('$estadoest'),
             menor_convive_con_adulto=upper('$menor_convive_con_adulto'),  responsable=upper('$responsable'),
             nombre_madre=upper('$nombre_madre'),anio_mayor_nivel_madre=$anio_mayor_nivel_madre,alfabeta_madre=upper('$alfabeta_madre'), estudios_madre=upper('$estudios_madre'), apellido_madre=upper('$apellido_madre'), nro_doc_madre='$nro_doc_madre', tipo_doc_madre=upper('$tipo_doc_madre'), estadoest_madre=upper('$estadoest_madre'),
             nombre_padre= upper('$nombre_padre'),anio_mayor_nivel_padre=$anio_mayor_nivel_padre,alfabeta_padre=upper('$alfabeta_padre'),estudios_padre=upper('$estudios_padre'), apellido_padre=upper('$apellido_padre'), nro_doc_padre='$nro_doc_padre',tipo_doc_padre=upper('$tipo_doc_padre'), estadoest_padre=upper('$estadoest_padre'),
             nombre_tutor=upper('$nombre_tutor') ,anio_mayor_nivel_tutor=$anio_mayor_nivel_tutor,alfabeta_tutor=upper('$alfabeta_tutor'), estudios_tutor=upper('$estudios_tutor'),apellido_tutor=upper('$apellido_tutor'), nro_doc_tutor='$nro_doc_tutor', tipo_doc_tutor=upper('$tipo_doc_tutor'), estadoest_tutor=upper('$estadoest_tutor'),
             menor_embarazada=upper('$menor_embarazada'),fecha_diagnostico_embarazo='$fecha_diagnostico_embarazo', semanas_embarazo='$semanas_embarazo',fecha_probable_parto='$fecha_probable_parto', 
             score_riesgo='$score_riesgo',fum='$fum',
             discv=upper('$discv'),disca=upper('$disca'),discmo=upper('$discmo'),discme=upper('$discme'),otradisc=upper('$otradisc'), obsgenerales=upper('$obsgenerales')
             where id_beneficiarios=".$id_planilla;
		
  
	sql($query, "Error al Actualizar los datos") or fin_pagina();   
	$db->CompleteTrans();    
   	$accion="Los datos se actualizaron";
    $cambiodom = 'N';		 
} //FIN Update

// Insert de Beneficiarios
if ($_POST['guardar']=="Guardar Planilla"){
		$sql1="select * from uad.beneficiarios	  
	 			where numero_doc='$num_doc'";
		$res_extra1=sql($sql1, "Error al traer el beneficiario") or fin_pagina();
		
		if ($res_extra1->recordcount()>0){
			$accion="El Beneficiario ya esta Empadronado";
			
			$tipo_transaccion='M';
			$id_planilla=$res_extra1->fields['id_beneficiarios'];       
		    $clave_beneficiario=$res_extra1->fields['clave_beneficiario'];
			$apellido=$res_extra1->fields['apellido_benef'];
		 	$nombre=$res_extra1->fields['nombre_benef'];
		 	$tipo_doc=$res_extra1->fields['tipo_documento'];
		 	$clase_doc=$res_extra1->fields['clase_documento_benef'];
		 	$mail=$res_extra1->fields['mail'];
			$celular=$res_extra1->fields['celular'];
			$sexo=$res_extra1->fields['sexo'];
		 	$fecha_nac=Fecha($res_extra1->fields['fecha_nacimiento_benef']);
		 	$pais_nac=$res_extra1->fields['pais_nac'];
		 	$id_categoria=$res_extra1->fields['id_categoria'];
		  	$indigena= $res_extra1->fields['indigena'];
		 	$id_tribu= $res_extra1->fields['id_tribu'];
		 	$id_lengua= $res_extra1->fields['id_lengua'];
		 	$alfabeta=$res_extra1->fields['alfabeta'];
			$estudios=$res_extra1->fields['estudios'];
			$estadoest=$res_extra1->fields['estadoest'];
			$anio_mayor_nivel=$res_extra1->fields['anio_mayor_nivel'];
		 	$calle=$res_extra1->fields['calle'];
		 	$numero_calle=$res_extra1->fields['numero_calle'];
			$piso=$res_extra1->fields['piso'];
			$dpto=$res_extra1->fields['dpto'];
			$manzana=$res_extra1->fields['manzana'];
			$entre_calle_1=$res_extra1->fields['entre_calle_1'];
			$entre_calle_2=$res_extra1->fields['entre_calle_2'];	
			$telefono=$res_extra1->fields['telefono'];
			$otrotel=$res_extra1->fields['otrotel'];
			$departamento=$res_extra1->fields['departamento'];
		   	$localidad=$res_extra1->fields['localidad'];
		   	$municipio=$res_extra1->fields['municipio'];
		   	$barrio=$res_extra1->fields['barrio'];
			$cod_pos=$res_extra1->fields['cod_pos'];
			$observaciones=$res_extra1->fields['observaciones'];
	 		
				if ($responsable=='MADRE'){
		    			$tipo_doc_madre=$res_extra1->fields['tipo_doc_madre'];
	   					$nro_doc_madre=$res_extra1->fields['nro_doc_madre'];
	   					$apellido_madre=$res_extra1->fields['apellido_madre'];
	   					$nombre_madre=$res_extra1->fields['nombre_madre'];
	   					$alfabeta_madre=$res_extra1->fields['alfabeta_madre'];
						$estudios_madre=$res_extra1->fields['estudios_madre'];
	   					$estadoest_madre=$res_extra1->fields['estadoest_madre'];
	   					$anio_mayor_nivel_madre=$res_extra1->fields['anio_mayor_nivel_madre'];
				}elseif ($responsable=='PADRE'){
						$tipo_doc_madre=$res_extra1->fields['tipo_doc_padre'];
	   					$nro_doc_madre=$res_extra1->fields['nro_doc_padre'];
	   					$apellido_madre=$res_extra1->fields['apellido_padre'];
	   					$nombre_madre=$res_extra1->fields['nombre_padre'];
	   					$alfabeta_madre=$res_extra1->fields['alfabeta_padre'];
						$estudios_madre=$res_extra1->fields['estudios_padre'];
	   					$estadoest_madre=$res_extra1->fields['estadoest_padre'];
	   					$anio_mayor_nivel_madre=$res_extra1->fields['anio_mayor_nivel_padre'];	
				}elseif ($responsable=='TUTOR'){
						$tipo_doc_madre=$res_extra1->fields['tipo_doc_tutor'];
			   			$nro_doc_madre=$res_extra1->fields['nro_doc_tutor'];
			   			$apellido_madre=$res_extra1->fields['apellido_tutor'];
			   			$nombre_madre=$res_extra1->fields['nombre_tutor'];	
			   			$alfabeta_madre=$res_extra1->fields['alfabeta_tutor'];
						$estudios_madre=$res_extra1->fields['estudios_tutor'];
			   			$estadoest_madre=$res_extra1->fields['estadoest_tutor'];
			   			$anio_mayor_nivel_madre=$res_extra1->fields['anio_mayor_nivel_tutor'];	
						}
			
			
			// Menor de 9 años, no muestra la información de embarazo y muestra la información del menor_convive_con_adulto	
			if (($id_categoria=='5') && ($sexo=='F')&& ($menor_embarazada =='N')){
			$embarazada=none;
			$mva1=inline;
			$datos_resp=inline;
			$memb=none;
			$menor_convive_con_adulto=$res_extra1->fields['menor_convive_con_adulto'];
			$responsable=$res_extra1->fields['responsable'];
 		} // Menor de 10 años hasta 18 años, pregunta si la menor esta o no embarazada y la información de menor_convive_con_adulto
	 		if (($id_categoria=='5') && ($sexo=='F') && ($menor_embarazada =='N')){ 
			$embarazada=none;
			$mva1=inline;
			$datos_resp=inline;
			$memb=inline;
	 		$menor_convive_con_adulto=$res_extra1->fields['menor_convive_con_adulto'];
			$responsable=$res_extra1->fields['responsable'];
			$menor_embarazada=$res_extra1->fields['menor_embarazada'];
	 		}
				//Si esta embarazada muestra la información de embarazo.
			if ($menor_embarazada=='S'){
				$embarazada=inline;
				$fum=Fecha($res_extra1->fields['fum']);
				$fecha_diagnostico_emabrazo=Fecha($res_extra1->fields['fecha_diagnostico_embarazo']);
				$semanas_embarazo=$res_extra1->fields['semanas_embarazo'];
				$fecha_probable_parto=Fecha($res_extra1->fields['fecha_probable_parto']);		
			} // Si no esta embarazada no la muestra.
				else{
					$embarazada=none;
				}
		
			// Menor de 18 años, masculino muestra solo la información menor_convive_con_adulto
			if(($id_categoria=='5') && ($sexo=='M')) { 
				$mva1=inline;
				$datos_resp=inline;
				$embarazada=none;
				$memb=none;
				$menor_convive_con_adulto=$res_extra1->fields['menor_convive_con_adulto'];
				$responsable=$res_extra1->fields['responsable'];

			}else// Mayor de 18 años Femenino muesta la información de embarazo.
		 		if (($id_categoria=='6') && ($sexo=='F')){
					$embarazada=inline;
					$datos_resp=none;
					$mva1=none;
					$memb=none;
					$fum=Fecha($res_extra1->fields['fum']);
					$fecha_diagnostico_emabrazo=Fecha($res_extra1->fields['fecha_diagnostico_embarazo']);
					$semanas_embarazo=$res_extra1->fields['semanas_embarazo'];
					$fecha_probable_parto=Fecha($res_extra1->fields['fecha_probable_parto']);		
		 		}else // Mayor de 18 años Masuclino no muestra la información de embarazo.
		 		if ($sexo=='M') {
					$embarazada=none;
					$datos_resp=none;
					$mva1=none;
					$memb=none;
				}//FIN
	
			$discv=$res_extra1->fields['discv'];
			$disca=$res_extra1->fields['disca'];
			$discmo=$res_extra1->fields['discmo'];
			$discme=$res_extra1->fields['discme'];
			$otradisc=$res_extra1->fields['otradisc'];
			$fecha_inscripcion=Fecha($res_extra1->fields['fecha_inscripcion']);
		 	$cuie=$res_extra1->fields['cuie_ea'];
		 	$obsgenerales=$res_extra1->fields['obsgenerales'];
	
		}else { 
//-------------------comienza el insert del nuevo beneficiario--------------------
					
	   $fecha_carga= date("Y-m-d");
	   $usuario=$_ses_user['login'];
	 
	    $fecha_nac=Fecha_db($fecha_nac);
	   	$fum=Fecha_db($fum);
	    $fecha_diagnostico_embarazo=Fecha_db($fecha_diagnostico_embarazo);
	 	$fecha_probable_parto=Fecha_db($fecha_probable_parto);
	   	$fecha_inscripcion=Fecha_db($fecha_inscripcion);
		$fecha_efectiva_parto=Fecha_db($fecha_efectiva_parto);
	   $db->StartTrans();      
	
	   $sql_parametros="select * from uad.parametros ";
	   $result_parametros=sql($sql_parametros) or fin_pagina();
	   $codigo_provincia=$result_parametros->fields['codigo_provincia'];
	   $codigo_ci=$result_parametros->fields['codigo_ci'];   
	   $codigo_uad=$result_parametros->fields['codigo_uad'];   
	    
	  /*$usuario = substr($usuario,0,9);*/
    
	 $responsable=$_POST['responsable'];  
	 $sql="Select puco.documento from puco.puco where puco.documento = '$num_doc'";
	 $res_extra=sql($sql, "Error al traer el beneficiario") or fin_pagina();
	 $estado_envio='n';
    
    if($menor_embarazada=='N'){
		$fecha_diagnostico_embarazo='1899-12-30';
		$semanas_embarazo=0;
    	$fecha_probable_parto='1899-12-30';
    	$fecha_efectiva_parto='1899-12-30';	
    	$fum='1899-12-30';
    }
    else $fecha_efectiva_parto='1899-12-30';	
    
     //Responsable Padre, menor no embarazada o menor de 9 años (Insert)
    if($responsable=='PADRE'){
 
    	$estadoest_madre='';
    	$tipo_doc_padre=$tipo_doc_madre;
    	$nro_doc_padre=$nro_doc_madre;
    	$apellido_padre=$apellido_madre;
    	$nombre_padre=$nombre_madre;
    	$alfabeta_padre=$alfabeta_madre;
    	$estudios_padre=$estudios_madre;
    	$anio_mayor_nivel_padre=$anio_mayor_nivel_madre;
    	$estadoest_padre=$estadoest_madre;
    	$tipo_doc_madre='';
    	$nro_doc_madre=0;
    	$apellido_madre='';
    	$nombre_madre='';
    	$alfabeta_madre='';
    	$estudios_madre='';
    	$anio_mayor_nivel_madre=0;
    	$tipo_doc_tutor='';
    	$nro_doc_tutor=0;
    	$apellido_tutor='';
    	$nombre_tutor='';
    	$alfabeta_tutor='';
    	$estudios_tutor='';
    	$anio_mayor_nivel_tutor=0;
    	$estadoest_tutor='';
    	
    }elseif ($responsable== 'TUTOR' ){
    	
    	$tipo_doc_tutor=$tipo_doc_madre;
    	$nro_doc_tutor=$nro_doc_madre;
    	$apellido_tutor=$apellido_madre;
    	$nombre_tutor=$nombre_madre;
    	$alfabeta_tutor=$alfabeta_madre;
    	$estudios_tutor=$estudios_madre;
    	$anio_mayor_nivel_tutor=$anio_mayor_nivel_madre;
    	$estadoest_tutor=$estadoest_madre;
    	$tipo_doc_madre='';
    	$nro_doc_madre=0;
    	$apellido_madre='';
    	$nombre_madre='';
    	$alfabeta_madre='';
    	$estudios_madre='';
    	$anio_mayor_nivel_madre=0;
    	$estadoest_madre='';
    	$tipo_doc_padre='';
    	$nro_doc_padre=0;
    	$apellido_padre='';
    	$nombre_padre='';
    	$alfabeta_padre='';
    	$estudios_padre='';
    	$anio_mayor_nivel_padre=0;
    	$estadoest_padre='';

       } elseif ($responsable!= 'MADRE'){
 
    	$tipo_doc_madre='';
    	$nro_doc_madre=0;
    	$apellido_madre='';
    	$nombre_madre='';
    	$alfabeta_madre='';
    	$estudios_madre='';
    	$anio_mayor_nivel_madre=0;
    	$estadoest_madre='';
    	$tipo_doc_padre='';
    	$nro_doc_padre=0;
    	$apellido_padre='';
    	$nombre_padre='';
    	$alfabeta_padre='';
    	$estudios_padre='';
    	$anio_mayor_nivel_padre=0;
    	$estadoest_padre='';
    	$tipo_doc_tutor='';
    	$nro_doc_tutor=0;
    	$apellido_tutor='';
    	$nombre_tutor='';
    	$alfabeta_tutor='';
    	$estudios_tutor='';
    	$anio_mayor_nivel_tutor=0;
    	$estadoest_tutor='';
    }else {
 
    	$tipo_doc_padre='';
    	$nro_doc_padre=0;
    	$apellido_padre='';
    	$nombre_padre='';
    	$alfabeta_padre='';
    	$estudios_padre='';
    	$anio_mayor_nivel_padre=0;
    	$estadoest_padre='';
    	$tipo_doc_tutor='';
    	$nro_doc_tutor=0;
    	$apellido_tutor='';
    	$nombre_tutor='';
    	$alfabeta_tutor='';
    	$estudios_tutor='';
    	$anio_mayor_nivel_tutor=0;
    	$estadoest_tutor='';
    }
    //(Insert)
     $q="select nextval('uad.beneficiarios_id_beneficiarios_seq') as id_planilla";
	   $id_planilla=sql($q) or fin_pagina();
	   $id_planilla=$id_planilla->fields['id_planilla'];
	   $id_planilla_clave= str_pad($id_planilla, 6, '0', STR_PAD_LEFT);
    $clave_beneficiario=$codigo_provincia.$codigo_uad.$codigo_ci.$id_planilla_clave;
    echo $semanas_embarazo; //$clave_beneficiario;

    //hago el insert para cualquier caso de beneficiario con los datos ya modificados y campos validados anteriormente
	$dias=calculo_dias(fecha($result->fields['$fecha_inscripcion']));
   $query="insert into uad.beneficiarios 
			(id_beneficiarios, estado_envio, clave_beneficiario, tipo_transaccion, apellido_benef, nombre_benef,
			clase_documento_benef, tipo_documento, numero_doc, id_categoria, sexo, fecha_nacimiento_benef,
			provincia_nac, localidad_nac, pais_nac, indigena, id_tribu, id_lengua, alfabeta, estudios, anio_mayor_nivel,
			menor_embarazada, fecha_diagnostico_embarazo, semanas_embarazo, fecha_probable_parto, fecha_efectiva_parto,  fum, score_riesgo,
			cuie_ea, cuie_ah, menor_convive_con_adulto,
			tipo_doc_madre, nro_doc_madre, apellido_madre, nombre_madre, alfabeta_madre, estudios_madre, anio_mayor_nivel_madre, estadoest_madre,
			tipo_doc_padre, nro_doc_padre, apellido_padre, nombre_padre, alfabeta_padre, estudios_padre, anio_mayor_nivel_padre, estadoest_padre,
			tipo_doc_tutor, nro_doc_tutor, apellido_tutor, nombre_tutor, alfabeta_tutor, estudios_tutor, anio_mayor_nivel_tutor, estadoest_tutor,
			calle, numero_calle, piso, dpto, manzana, entre_calle_1, entre_calle_2,
			telefono, departamento, localidad, municipio, barrio, cod_pos, observaciones, fecha_inscripcion, fecha_carga, usuario_carga,
			activo, tipo_ficha, responsable, mail, celular, otrotel, estadoest, discv, disca, discmo, discme, otradisc, obsgenerales)
			values
			('$id_planilla','$estado_envio','$clave_beneficiario',upper('$tipo_transaccion'),upper('$apellido'),upper('$nombre'),
			upper('$clase_doc'), upper('$tipo_doc'),'$num_doc','$id_categoria',upper('$sexo'),'$fecha_nac',
			upper('$provincia_nac'),upper('$localidad_proc'),upper('$paisn'),upper('$indigena'),$id_tribu,$id_lengua,upper('$alfabeta'),upper('$estudios'),'$anio_mayor_nivel',
		 	upper('$menor_embarazada'), '$fecha_diagnostico_embarazo','$semanas_embarazo', '$fecha_probable_parto', '$fecha_efectiva_parto', '$fum', '$score_riesgo',
          	upper('$cuie'),upper('$cuie'),upper('$menor_convive_con_adulto'),
			upper('$tipo_doc_madre'),$nro_doc_madre,upper('$apellido_madre'),upper('$nombre_madre'),upper('$alfabeta_madre'),upper('$estudios_madre'), '$anio_mayor_nivel_madre', '$estadoest_madre',
            upper('$tipo_doc_padre'),$nro_doc_padre,upper('$apellido_padre'),upper('$nombre_padre'),upper('$alfabeta_padre'),upper('$estudios_padre'),'$anio_mayor_nivel_padre','$estadoest_padre',
            upper('$tipo_doc_tutor'), $nro_doc_tutor,upper('$apellido_tutor'),upper('$nombre_tutor'),upper('$alfabeta_tutor'),upper('$estudios_tutor'),'$anio_mayor_nivel_tutor','$estadoest_tutor',
        	upper('$calle'),'$numero_calle','$piso',upper('$dpto'), '$manzana',upper('$entre_calle_1'), upper('$entre_calle_2'),
			'$telefono',upper('$departamenton'),upper('$localidadn'),upper('$municipion'),upper('$barrion'), '$cod_posn',upper('$observaciones'), '$fecha_inscripcion','$fecha_carga',upper('$usuario'),
        	 '1','2', upper('$responsable'),upper('$mail'),'$celular','$otrotel',upper('$estadoest'), upper('$discv'),upper('$disca'),upper('$discmo'), upper('$discme'),upper('$otradisc'),upper('$obsgenerales'))";


			// Busca antes de hacer el insert si el beneficiario esta o no en el PUCO
    		if ($res_extra->recordcount()>0) {
    			sql($query, "Error al insertar la Planilla") or fin_pagina();
    			$accion="Se guardo la Planilla - El inscripto esta en el PUCO";       	
    			}
    		if ($res_extra->recordcount()==0){
    			sql($query, "Error al insertar la Planilla") or fin_pagina();
    			$accion="Se guardo la Planilla";       
	     		$db->CompleteTrans();
    			}

    	}//FIN
    $db->CompleteTrans();
       
}//FIN Insert

// Borrado de Beneficiarios
if ($_POST['borrar']=="Borrar"){
	
	if ($tipo_transaccion == 'B'){
	$query="UPDATE uad.beneficiarios  SET activo='0', tipo_transaccion= 'B', estado_envio='n'  WHERE (id_beneficiarios= $id_planilla)";
	sql($query, "Error al insertar la Planilla") or fin_pagina();
	   
	$accion="Se elimino la planilla $id_planilla";
	}
	
} //FIN Borrado Beneficiarios

// Buscar Beneficiarios por DNI
if ($_POST['b']=="b"){
		$sql1="select * from uad.beneficiarios	  
	 	where numero_doc='$num_doc'";
		$res_extra1=sql($sql1, "Error al traer el beneficiario") or fin_pagina();
		if ($res_extra1->recordcount()>0){
			$accion="El Beneficiario ya esta Empadronado";
		$tipo_transaccion='M';
		$id_planilla=$res_extra1->fields['id_beneficiarios'];       
	    $clave_beneficiario=$res_extra1->fields['clave_beneficiario'];
		$apellido=$res_extra1->fields['apellido_benef'];
	 	$nombre=$res_extra1->fields['nombre_benef'];
	 	$tipo_doc=$res_extra1->fields['tipo_documento'];
	 	$clase_doc=$res_extra1->fields['clase_documento_benef'];
	 	$mail=$res_extra1->fields['mail'];
		$celular=$res_extra1->fields['celular'];
		$sexo=$res_extra1->fields['sexo'];
	 	$fecha_nac=Fecha($res_extra1->fields['fecha_nacimiento_benef']);
	 	$pais_nac=$res_extra1->fields['pais_nac'];
	 	$id_categoria=$res_extra1->fields['id_categoria'];
	  	$indigena= $res_extra1->fields['indigena'];
	 	$id_tribu= $res_extra1->fields['id_tribu'];
	 	$id_lengua= $res_extra1->fields['id_lengua'];
	 	$alfabeta=$res_extra1->fields['alfabeta'];
		$estudios=$res_extra1->fields['estudios'];
		$estadoest=$res_extra1->fields['estadoest'];
		$anio_mayor_nivel=$res_extra1->fields['anio_mayor_nivel'];
	 	$calle=$res_extra1->fields['calle'];
	 	$numero_calle=$res_extra1->fields['numero_calle'];
		$piso=$res_extra1->fields['piso'];
		$dpto=$res_extra1->fields['dpto'];
		$manzana=$res_extra1->fields['manzana'];
		$entre_calle_1=$res_extra1->fields['entre_calle_1'];
		$entre_calle_2=$res_extra1->fields['entre_calle_2'];	
		$telefono=$res_extra1->fields['telefono'];
		$otrotel=$res_extra1->fields['otrotel'];
		$departamento=$res_extra1->fields['departamento'];
	   	$localidad=$res_extra1->fields['localidad'];
	   	$municipio=$res_extra1->fields['municipio'];
	   	$barrio=$res_extra1->fields['barrio'];
		$cod_pos=$res_extra1->fields['cod_pos'];
		$observaciones=$res_extra1->fields['observaciones'];
 		// Menor de 9 años, no muestra la información de embarazo y muestra la información del menor_convive_con_adulto	
		if (($id_categoria=='5') && ($sexo=='F')&& ($menor_embarazada =='N')){
		$embarazada=none;
		$mva1=inline;
		$datos_resp=inline;
		$memb=none;
		$menor_convive_con_adulto=$res_extra1->fields['menor_convive_con_adulto'];
		$responsable=$res_extra1->fields['responsable'];
		if ($responsable=='MADRE'){
	    	$tipo_doc_madre=$res_extra1->fields['tipo_doc_madre'];
   			$nro_doc_madre=$res_extra1->fields['nro_doc_madre'];
   			$apellido_madre=$res_extra1->fields['apellido_madre'];
   			$nombre_madre=$res_extra1->fields['nombre_madre'];
   			$alfabeta_madre=$res_extra1->fields['alfabeta_madre'];
			$estudios_madre=$res_extra1->fields['estudios_madre'];
   			$estadoest_madre=$res_extra1->fields['estadoest_madre'];
   			$anio_mayor_nivel_madre=$res_extra1->fields['anio_mayor_nivel_madre'];
		}elseif ($responsable=='PADRE'){
			$tipo_doc_madre=$res_extra1->fields['tipo_doc_padre'];
   			$nro_doc_madre=$res_extra1->fields['nro_doc_padre'];
   			$apellido_madre=$res_extra1->fields['apellido_padre'];
   			$nombre_madre=$res_extra1->fields['nombre_padre'];
   			$alfabeta_madre=$res_extra1->fields['alfabeta_padre'];
			$estudios_madre=$res_extra1->fields['estudios_padre'];
   			$estadoest_madre=$res_extra1->fields['estadoest_padre'];
   			$anio_mayor_nivel_madre=$res_extra1->fields['anio_mayor_nivel_padre'];	
			}elseif ($responsable=='TUTOR'){
			$tipo_doc_madre=$res_extra1->fields['tipo_doc_tutor'];
   			$nro_doc_madre=$res_extra1->fields['nro_doc_tutor'];
   			$apellido_madre=$res_extra1->fields['apellido_tutor'];
   			$nombre_madre=$res_extra1->fields['nombre_tutor'];	
   			$alfabeta_madre=$res_extra1->fields['alfabeta_tutor'];
			$estudios_madre=$res_extra1->fields['estudios_tutor'];
   			$estadoest_madre=$res_extra1->fields['estadoest_tutor'];
   			$anio_mayor_nivel_madre=$res_extra1->fields['anio_mayor_nivel_tutor'];	
			}
 		} // Menor de 10 años hasta 18 años, pregunta si la menor esta o no embarazada y la información de menor_convive_con_adulto
 		if (($id_categoria=='5') && ($sexo=='F') && ($menor_embarazada =='N')){ 
		$embarazada=none;
		$mva1=inline;
		$datos_resp=inline;
		$memb=inline;
 		$menor_convive_con_adulto=$res_extra1->fields['menor_convive_con_adulto'];
		$responsable=$res_extra1->fields['responsable'];
		$menor_embarazada=$res_extra1->fields['menor_embarazada'];
		if ($responsable=='MADRE'){
	    	$tipo_doc_madre=$res_extra1->fields['tipo_doc_madre'];
   			$nro_doc_madre=$res_extra1->fields['nro_doc_madre'];
   			$apellido_madre=$res_extra1->fields['apellido_madre'];
   			$nombre_madre=$res_extra1->fields['nombre_madre'];
   			$alfabeta_madre=$res_extra1->fields['alfabeta_madre'];
			$estudios_madre=$res_extra1->fields['estudios_madre'];
   			$estadoest_madre=$res_extra1->fields['estadoest_madre'];
   			$anio_mayor_nivel_madre=$res_extra1->fields['anio_mayor_nivel_madre'];
		}elseif ($responsable=='PADRE'){
			$tipo_doc_madre=$res_extra1->fields['tipo_doc_padre'];
   			$nro_doc_madre=$res_extra1->fields['nro_doc_padre'];
   			$apellido_madre=$res_extra1->fields['apellido_padre'];
   			$nombre_madre=$res_extra1->fields['nombre_padre'];	
   			$alfabeta_madre=$res_extra1->fields['alfabeta_padre'];
			$estudios_madre=$res_extra1->fields['estudios_padre'];
   			$estadoest_madre=$res_extra1->fields['estadoest_padre'];
   			$anio_mayor_nivel_madre=$res_extra1->fields['anio_mayor_nivel_padre'];	
			}elseif ($responsable=='TUTOR'){
			$tipo_doc_madre=$res_extra1->fields['tipo_doc_tutor'];
   			$nro_doc_madre=$res_extra1->fields['nro_doc_tutor'];
   			$apellido_madre=$res_extra1->fields['apellido_tutor'];
   			$nombre_madre=$res_extra1->fields['nombre_tutor'];	
   			$alfabeta_madre=$res_extra1->fields['alfabeta_tutor'];
			$estudios_madre=$res_extra1->fields['estudios_tutor'];
   			$estadoest_madre=$res_extra1->fields['estadoest_tutor'];
   			$anio_mayor_nivel_madre=$res_extra1->fields['anio_mayor_nivel_tutor'];	
			}
			//Si esta embarazada muestra la información de embarazo.
			if ($menor_embarazada=='S'){
				$embarazada=inline;
				$fum=Fecha($res_extra1->fields['fum']);
				$fecha_diagnostico_emabrazo=Fecha($res_extra1->fields['fecha_diagnostico_embarazo']);
				$semanas_embarazo=$res_extra1->fields['semanas_embarazo'];
				$fecha_probable_parto=Fecha($res_extra1->fields['fecha_probable_parto']);		
			} // Si no esta embarazada no la muestra.
			else{
				$embarazada=none;
			}
		}// FIN
		// Menor de 18 años, masculino muestra solo la información menor_convive_con_adulto
		if(($id_categoria=='5') && ($sexo=='M')) { 
			$mva1=inline;
			$datos_resp=inline;
			$embarazada=none;
			$memb=none;
			$menor_convive_con_adulto=$res_extra1->fields['menor_convive_con_adulto'];
			$responsable=$res_extra1->fields['responsable'];
				if ($responsable=='MADRE'){
	    			$tipo_doc_madre=$res_extra1->fields['tipo_doc_madre'];
   					$nro_doc_madre=$res_extra1->fields['nro_doc_madre'];
   					$apellido_madre=$res_extra1->fields['apellido_madre'];
   					$nombre_madre=$res_extra1->fields['nombre_madre'];
   					$alfabeta_madre=$res_extra1->fields['alfabeta_madre'];
					$estudios_madre=$res_extra1->fields['estudios_madre'];
   					$estadoest_madre=$res_extra1->fields['estadoest_madre'];
   					$anio_mayor_nivel_madre=$res_extra1->fields['anio_mayor_nivel_madre'];
				}elseif ($responsable=='PADRE'){
					$tipo_doc_madre=$res_extra1->fields['tipo_doc_padre'];
   					$nro_doc_madre=$res_extra1->fields['nro_doc_padre'];
   					$apellido_madre=$res_extra1->fields['apellido_padre'];
   					$nombre_madre=$res_extra1->fields['nombre_padre'];
   					$alfabeta_madre=$res_extra1->fields['alfabeta_padre'];
					$estudios_madre=$res_extra1->fields['estudios_padre'];
   					$estadoest_madre=$res_extra1->fields['estadoest_padre'];
   					$anio_mayor_nivel_madre=$res_extra1->fields['anio_mayor_nivel_padre'];	
				}elseif ($responsable=='TUTOR'){
			$tipo_doc_madre=$res_extra1->fields['tipo_doc_tutor'];
   			$nro_doc_madre=$res_extra1->fields['nro_doc_tutor'];
   			$apellido_madre=$res_extra1->fields['apellido_tutor'];
   			$nombre_madre=$res_extra1->fields['nombre_tutor'];	
   			$alfabeta_madre=$res_extra1->fields['alfabeta_tutor'];
			$estudios_madre=$res_extra1->fields['estudios_tutor'];
   			$estadoest_madre=$res_extra1->fields['estadoest_tutor'];
   			$anio_mayor_nivel_madre=$res_extra1->fields['anio_mayor_nivel_tutor'];	
			}
		}// Mayor de 18 años Femenino muesta la información de embarazo.
 		if (($id_categoria=='6') && ($sexo=='F')){
			$embarazada=inline;
			$datos_resp=none;
			$mva1=none;
			$memb=none;
			$fum=Fecha($res_extra1->fields['fum']);
			$fecha_diagnostico_emabrazo=Fecha($res_extra1->fields['fecha_diagnostico_embarazo']);
			$semanas_embarazo=$res_extra1->fields['semanas_embarazo'];
			$fecha_probable_parto=Fecha($res_extra1->fields['fecha_probable_parto']);		
 		}// Mayor de 18 años Masuclino no muestra la información de embarazo.
 		if (($id_categoria=='6') && ($sexo=='M')) {
			$embarazada=none;
			$datos_resp=none;
			$mva1=none;
			$memb=none;
		}//FIN
	
	$discv=$res_extra1->fields['discv'];
	$disca=$res_extra1->fields['disca'];
	$discmo=$res_extra1->fields['discmo'];
	$discme=$res_extra1->fields['discme'];
	$otradisc=$res_extra1->fields['otradisc'];
	$fecha_inscripcion=Fecha($res_extra1->fields['fecha_inscripcion']);
 	$cuie=$res_extra1->fields['cuie_ea'];
 	$obsgenerales=$res_extra1->fields['obsgenerales'];
	
	}else {
			$accion2="Beneficiario no Encontrado";
		}
}//FIN Busqueda por DNI
if($id_planilla){ ;
	 $queryCategoria="SELECT beneficiarios.*, efe_conv.nombre, efe_conv.cuie
			FROM uad.beneficiarios
			left join nacer.efe_conv on beneficiarios.cuie_ea=efe_conv.cuie 
  	where id_beneficiarios=$id_planilla";

	$resultado=sql($queryCategoria, "Error al traer el Comprobantes") or fin_pagina();
	//Datos de la inscripcion
			$clave_beneficiario=trim($resultado->fields['clave_beneficiario']);
		   	$trans=$resultado->fields['tipo_transaccion'];
			$id_categoria=$resultado->fields['id_categoria'];
			$fecha_inscripcion=fecha($resultado->fields['fecha_inscripcion']);
			$observaciones=trim($resultado->fields['observaciones']);
			$cuie=$resultado->fields['cuie'];
	//datos personales del beneficiario
			$estadoest=$resultado->fields['estadoest'];
		  	$edad=$resultado->fields['edades'];   
			$clase_doc=$resultado->fields['clase_documento_benef']; 
			$sexo=$resultado->fields['sexo'];
			$num_doc=trim($resultado->fields['numero_doc']); 
			$apellido= trim($resultado->fields['apellido_benef']);
			$nombre=trim($resultado->fields['nombre_benef']);
			$fecha_nac=fecha($resultado->fields['fecha_nacimiento_benef']);	
			$pais_nac=$resultado->fields['pais_nac'];
			$mail=$resultado->fields['mail'];
		   	$celular=$resultado->fields['celular'];
		   	$otrotel=$resultado->fields['otrotel'];
	//lugar de domicilio
			$calle=$resultado->fields['calle'];
			$numero_calle=$resultado->fields['numero_calle'];
			$piso=$resultado->fields['piso'];
			$dpto=$resultado->fields['dpto'];
			$manzana=$resultado->fields['manzana'];
			$entre_calle_1=$resultado->fields['entre_calle_1'];
			$entre_calle_2=$resultado->fields['entre_calle_2'];
			$telefono=$resultado->fields['telefono'];
			$departamento=$resultado->fields['departamento'];
		   	$localidad=$resultado->fields['localidad'];
		   	$cod_pos=$resultado->fields['cod_pos'];	 
		   	$municipio=$resultado->fields['municipio'];
		   	$barrio=$resultado->fields['barrio'];
	//estudios del beneficiario  	
		   	$alfabeta=$resultado->fields['alfabeta'];
		   	$estudios=$resultado->fields['estudios'];	
		   	$anio_mayor_nivel=$resultado->fields['anio_mayor_nivel'];	
	//datos si es originario
		   	$indigena= $resultado->fields['indigena'];
		   	$id_tribu= $resultado->fields['id_tribu'];
		   	$id_lengua= $resultado->fields['id_lengua'];
	//datos de convivencia	
		   	$menor_convive_con_adulto=$resultado->fields['menor_convive_con_adulto'];
		// toma el responsable del niño en el caso que conviva con alguien.
		
		   	if($menor_convive_con_adulto=='S'){//verifico si convive con alguien y quien es el responsable
			 	$responsable=$resultado->fields['responsable'];	   
				if($responsable == 'PADRE'){
					$tipo_doc_madre=$resultado->fields['tipo_doc_padre'];
				    $nro_doc_madre=$resultado->fields['nro_doc_padre'];
				    $apellido_madre=$resultado->fields['apellido_padre']; 
				    $nombre_madre=$resultado->fields['nombre_padre'];
				    $alfabeta_madre=$resultado->fields['alfabeta_padre'];
				    $estudios_madre=$resultado->fields['estudios_padre'];
				    $anio_mayor_nivel_madre=$resultado->fields['anio_mayor_nivel_padre'];
				    $estadoest_madre=$resultado->fields['estadoest_padre'];
					}
					elseif ($responsable == 'MADRE'){
						$tipo_doc_madre=$resultado->fields['tipo_doc_madre'];
				    	$nro_doc_madre=$resultado->fields['nro_doc_madre'];
				    	$apellido_madre=$resultado->fields['apellido_madre']; 
				    	$nombre_madre=$resultado->fields['nombre_madre'];
				    	$alfabeta_madre=$resultado->fields['alfabeta_madre'];
				    	$estudios_madre=$resultado->fields['estudios_madre'];
				    	$anio_mayor_nivel_madre=$resultado->fields['anio_mayor_nivel_madre'];
				    	$estadoest_madre=$resultado->fields['estadoest_madre'];
					}elseif ($responsable == 'TUTOR'){
						$tipo_doc_madre=$resultado->fields['tipo_doc_tutor'];
				    	$nro_doc_madre=$resultado->fields['nro_doc_tutor'];
				    	$apellido_madre=$resultado->fields['apellido_tutor']; 
				    	$nombre_madre=$resultado->fields['nombre_tutor'];
				    	$alfabeta_madre=$resultado->fields['alfabeta_tutor'];
				    	$estudios_madre=$resultado->fields['estudios_tutor'];
				    	$anio_mayor_nivel_madre=$resultado->fields['anio_mayor_nivel_tutor'];
				    	$estadoest_madre=$resultado->fields['estadoest_tutor'];
							} 	
	   			}//fin de if($menor_convive_con_adulto=='S'){ 
		   	$menor_embarazada=$resultado->fields['menor_embarazada'];
			if ($menor_embarazada=='S'){
			
				$fum=fecha($resultado->fields['fum']);
				$fecha_diagnostico_embarazo=fecha($resultado->fields['fecha_diagnostico_embarazo']);
				$semanas_embarazo=$resultado->fields['semanas_embarazo'];
				$fecha_probable_parto=fecha($resultado->fields['fecha_probable_parto']);
				$score_riesgo=$resultado->fields['score_riesgo'];
			}
		   	$discv=$resultado->fields['discv'];
		   	$disca=$resultado->fields['disca'];
		   	$discmo=$resultado->fields['discmo'];
		   	$discme=$resultado->fields['discme'];
		   	$otradisc=$resultado->fields['otradisc'];
		   	$obsgenerales=trim($resultado->fields['obsgenerales']);	   			
   	// Marca Borrado al beneficiario.
		   	if ($trans == 'B'){
	   		$trans="Borrado";
	   	}
}


// INICIO Formulario Inicial, no se muestra la información de embarazo, o menor vive con adulto.
if(($id_categoria=='') && ($edad == '')){

	$embarazada= none; 
	$datos_resp= none;
	$mva1= none;
	$memb= none;
	$menor_embarazada=none;
} elseif (($id_categoria == '6')&& ($sexo=='F')){ //Femenino mayor de 19 años, pregunta si esta o no embarazada para mostrar la información de embarazo.
			
			$embarazada=none;
			$datos_resp=none;
			$mva1=none;
			$memb=inline;
			
			if ($menor_embarazada =='S'){
				
				$embarazada=inline;
				$semanas_embarazo=$_POST['semanas_embarazo'];
			}else {
				
				$embarazada=none;	
			}
			
		} elseif (($id_categoria == '5')&& ($sexo=='F')){
				
					$embarazada=none;
					$datos_resp=inline;
					$mva1=inline;
					$memb=inline;
					if ($menor_embarazada =='S'){
					
						$embarazada=inline;
						$semanas_embarazo=$_POST['semanas_embarazo'];
					}else {
						
							$embarazada=none;	
						}
				}elseif (($id_categoria=='5') && ($sexo=='M')) { // Masculino menor de 19 años, muestra la información de menor vive con adulto y no la de embarazo
						
						$mva1=inline;
						$datos_resp=inline;
						$embarazada=none;
						$memb=none;
							
					} elseif (($id_categoria=='6') && ($sexo=='M')) {// Masculino mayor de 19 años, no muesta la información de embarazo ni tampoco la de menor vive con adulto.
						
						$embarazada=none;
						$datos_resp=none;
						$mva1=none;
						$memb=none;
						} // FIN

	// Muestra Cambio de Domicilio al momento de hacer una modificacion solamente.
if ($tipo_transaccion != 'M'){
	$cdomi1=none;
} // FIN

// Query que muestra la informacion guardada del Beneficiario del Pais de Nacimiento
if (($id_planilla != '') && ($cambiodom != 'S')){
	$strConsulta = "select pais_nac from uad.beneficiarios where id_beneficiarios = $id_planilla ";
	$result = @pg_exec($strConsulta); 
	$fila= pg_fetch_array($result);
	$pais_nac.='<option value="'.$fila["pais_nac"].'">'.$fila["pais_nac"].'</option>';
	$paisn=$fila["pais_nac"];
	}// FIN	
	elseif (($id_planilla == '') || ($cambiodom == 'S')) { // Query para traer los paises para luego ser utilizado con AJAX para que no refresque la pagina.
	$strConsulta = "select id_pais, nombre from uad.pais order by nombre";
	$result = @pg_exec($strConsulta); 
	$pais_nac = '<option value="-1"> Seleccione Pais </option>';
		
	while( $fila = pg_fetch_array($result) )
	{
		
		$pais_nac.='<option value="'.$fila["id_pais"].'">'.$fila["nombre"].'</option>';
		
	} // FIN WHILE	
	
} // FIN ELSEIF

// Query que muestra la informacion guardada del Beneficiario del Departamento donde vive
if (($id_planilla != '') && ($cambiodom != 'S')){
	$strConsulta = "select departamento from uad.beneficiarios where id_beneficiarios = $id_planilla";
	$result = @pg_exec($strConsulta); 
	$fila= pg_fetch_array($result);
	$departamento.='<option value="'.$fila["departamento"].'">'.$fila["departamento"].'</option>';
	$departamenton=$fila["departamento"];
	}// FIN	
	elseif (($id_planilla == '') || ($cambiodom ==  'S')){// Query para traer los departamentos para luego ser utilizado con AJAX para que no refresque la pagina.
 	$strConsulta = "select id_departamento, nombre from uad.departamentos order by nombre";
	$result = @pg_exec($strConsulta); 
	$departamento = '<option value="-1"> Seleccione Departamento </option>';
	$opciones2 = '<option value="-1"> Seleccione Localidad </option>';
	$opciones3 = '<option value="-1"> Seleccione Municipio </option>';
	$opciones4 = '<option value="-1"> Seleccione Barrio </option>';
	$opciones5 = '<option value="-1"> Codigo Postal  </option>';	
	while( $fila = pg_fetch_array($result) )
	{
		
		$departamento.='<option value="'.$fila["id_departamento"].'">'.$fila["nombre"].'</option>';
		
	} // FIN WHILE
} //FIN ELSEIF

// Query que muestra la informacion guardada del Beneficiario de la Localidad donde vive
if (($id_planilla != '') && ($cambiodom != 'S')){
	$strConsulta = "select localidad from uad.beneficiarios where id_beneficiarios = $id_planilla";
	$result = @pg_exec($strConsulta); 
	$fila= pg_fetch_array($result);
	$opciones2.='<option value="'.$fila["localidad"].'">'.$fila["localidad"].'</option>';
	$localidadn=$fila["localidad"];
}// FIN

// Query que muestra la informacion guardada del Beneficiario del Municipio donde vive
if (($id_planilla != '') && ($cambiodom != 'S')){
	$strConsulta = "select cod_pos from uad.beneficiarios where id_beneficiarios = $id_planilla";
	$result = @pg_exec($strConsulta); 
	$fila= pg_fetch_array($result);
	$opciones5.='<option value="'.$fila["cod_pos"].'">'.$fila["cod_pos"].'</option>';
	$cod_posn=$fila["cod_pos"];
}// FIN

// Query que muestra la informacion guardada del Beneficiario del Municipio donde vive
if (($id_planilla != '') && ($cambiodom != 'S')){
	$strConsulta = "select municipio from uad.beneficiarios where id_beneficiarios = $id_planilla";
	$result = @pg_exec($strConsulta); 
	$fila= pg_fetch_array($result);
	$opciones3.='<option value="'.$fila["municipio"].'">'.$fila["municipio"].'</option>';
	$municipion=$fila["municipio"];
}// FIN

// Query que muestra la informacion guardada del Beneficiario del Barrio donde vive
if (($id_planilla != '') && ($cambiodom != 'S')){
	$strConsulta = "select barrio from uad.beneficiarios where id_beneficiarios = $id_planilla";
	$result = @pg_exec($strConsulta); 
	$fila= pg_fetch_array($result);
	$opciones4.='<option value="'.$fila["barrio"].'">'.$fila["barrio"].'</option>';
	$barrion=$fila["barrio"];
}// FIN

echo $html_header;

$directorio_base=trim(substr(ROOT_DIR, strrpos(ROOT_DIR,chr(92))+1, strlen(ROOT_DIR)));
?>
<script>
// Script para el manejo de combobox de Departamento - Localidad - Codigo Postal - Municipio y Barrio
$(document).ready(function(){
	$("#departamento").change(function(){
		$.ajax({
			url:"procesa.php",
			type: "POST",
			data:"id_departamento="+$("#departamento").val(),
			success: function(opciones){
				$("#localidad").html(opciones);
						
			}
		})
	});
});
$(document).ready(function(){
	$("#localidad").change(function(){
		$.ajax({
			url:"procesa.php",
			type: "POST",
			data:"id_localidad="+$("#localidad").val(),
			success: function(opciones){
				$("#cod_pos").html(opciones);
				}
		})
	});
});
$(document).ready(function(){
	$("#cod_pos").change(function(){
		$.ajax({
			url:"procesa.php",
			type: "POST",
			data:"id_codpos="+$("#cod_pos").val(),
			success: function(opciones){
				$("#municipio").html(opciones);
				
						
			}
		})
	});
});

$(document).ready(function(){
	$("#municipio").change(function(){
		$.ajax({
			url:"procesa.php",
			type: "POST",
			data:"id_municipio="+$("#municipio").val(),
			success: function(opciones){
				$("#barrio").html(opciones);
				
				
			}
		})
	});
});// FIN

//Guarda el nombre del Pais
function showpais_nac(){
	var pais_nac = document.getElementById('pais_nac')[document.getElementById('pais_nac').selectedIndex].innerHTML;
	document.all.paisn.value =  pais_nac;
}// FIN

// Guarda el nombre del Departamento
function showdepartamento(){
	var departamento = document.getElementById('departamento')[document.getElementById('departamento').selectedIndex].innerHTML;
	document.all.departamenton.value =  departamento;
} // FIN

//Guarda el nombre del Localidad
function showlocalidad(){
	var localidad = document.getElementById('localidad')[document.getElementById('localidad').selectedIndex].innerHTML;
	document.all.localidadn.value =  localidad;
}// FIN

// Guarda el Codigo Postal
function showcodpos(){
	var cod_pos = document.getElementById('cod_pos')[document.getElementById('cod_pos').selectedIndex].innerHTML;
	document.all.cod_posn.value =  cod_pos;
}// FIN

//Guarda el nombre del Municipio
function showmunicipio(){
	var municipio = document.getElementById('municipio')[document.getElementById('municipio').selectedIndex].innerHTML;
	document.all.municipion.value =  municipio;
}// FIN

//Guarda el nombre del Barrio
function showbarrio(){
	var barrio = document.getElementById('barrio')[document.getElementById('barrio').selectedIndex].innerHTML;
	document.all.barrion.value =  barrio;
}// FIN


//Validar Fechas
function esFechaValida(fecha){
    if (fecha != undefined && fecha.value != "" ){
        if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)){
            alert("formato de fecha no válido (dd/mm/aaaa)");
            return false;
        }
        var dia  =  parseInt(fecha.value.substring(0,2),10);
        var mes  =  parseInt(fecha.value.substring(3,5),10);
        var anio =  parseInt(fecha.value.substring(6),10);
 
    switch(mes){
        case 1:
        case 3:
        case 5:
        case 7:
        case 8:
        case 10:
        case 12:
            numDias=31;
            break;
        case 4: case 6: case 9: case 11:
            numDias=30;
            break;
        case 2:
            if (comprobarSiBisisesto(anio)){ numDias=29 }else{ numDias=28};
            break;
        default:
            alert("Fecha introducida errónea");
            return false;
    }
 
        if (dia>numDias || dia==0){
            alert("Fecha introducida errónea");
            return false;
        }
        return true;
    }
}
 
function comprobarSiBisisesto(anio){
if ( ( anio % 100 != 0) && ((anio % 4 == 0) || (anio % 400 == 0))) {
    return true;
    }
else {
    return false;
    }
}


//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{

	if(document.all.apellido.value==""){
		 alert("Debe completar el campo apellido");
		 document.all.apellido.focus();
		 return false;
	 }else{
		 var charpos = document.all.apellido.value.search("[^A-Za-z/ \s/]"); 
		   if( charpos >= 0) 
		    { 
		     alert( "El campo Apellido solo permite letras "); 
		     document.all.apellido.focus();
		     return false;
		    }
		 }	
	 
	 if(document.all.nombre.value==""){
		 alert("Debe completar el campo nombre");
		 document.all.nombre.focus();
		 return false;
		 }else{
			 var charpos = document.all.nombre.value.search("[^A-Za-z/ \s/]"); 
			   if( charpos >= 0) 
			    { 
			     alert( "El campo Nombre solo permite letras "); 
			     document.all.nombre.focus();
			     return false;
			    }
			 }		
		
 if(document.all.num_doc.value==""){
	 alert("Debe completar el campo numero de documento");
	 document.all.num_doc.focus();
	 return false;
	 }else{
 		var num_doc=document.all.num_doc.value;
		if(isNaN(num_doc)){
			alert('El dato ingresado en numero de documento debe ser entero');
			document.all.num_doc.focus();
			return false;
	 	}
	 }
 
  if(document.all.sexo.value==-1){
			alert("Debe completar el campo sexo");
			document.all.sexo.focus();
			 return false;
		 }
 
 if(document.all.pais_nac.value==-1){
		alert("Debe completar el campo pais");
		document.all.pais_nac.focus();
		 return false;
		 }

 if(document.all.calle.value==""){
		alert("Debe completar el campo calle");
		document.all.calle.focus();
		 return false;
		 }

if(document.all.numero_calle.value==""){
		alert("Debe completar el campo numero calle");
		document.all.numero_calle.focus();
		 return false;
		 }

if(document.all.departamento.value==-1){
	alert("Debe completar el campo departamento");
	document.all.departamento.focus();
	 return false;
	 }
if(document.all.localidadn.value==-1){
	alert("Debe completar el campo Localidad");
	document.all.localidadn.focus();
	 return false;
	 }
if(document.all.cod_posn.value==""){
	alert("Debe completar el campo Codigo Postal");
	document.all.cod_posn.focus();
	 return false;
	 }
if(document.all.municipion.value==""){
	alert("Debe completar el campo Municipio");
	document.all.municipion.focus();
	 return false;
	 }
if(document.all.barrion.value==""){
	alert("Debe completar el campo Barrio");
	document.all.barrion.focus();
	 return false;
	 }	 
	 
	 // Validación Menores de 18 años
	if (ed <= 17) {
		if(document.all.responsable.value==-1){
				alert ("Debe completar el campo Datos del responsable");
				document.all.responsable.focus();
				return false;
			}

			if(document.all.tipo_doc_madre.value==-1){
				alert("Debe completar el campo tipo de documento del responsable");
				document.all.apellido_madre.focus();
				 return false;
			 }
			if(document.all.nro_doc_madre.value==""){
				
				alert("Debe completar el campo numero de documento del responsable");
			
				return false;
			 }else{
				 var num_doc_madre=document.all.nro_doc_madre.value;
				 if(isNaN(num_doc_madre)){
					alert('El dato ingresado en numero de documento del responsable debe ser entero');
					document.all.num_doc_madre.focus();
					return false;
				}
			}
			if(document.all.apellido_madre.value==""){
				alert("Debe completar el campo apellido del responsable");
				document.all.apellido_madre.focus();
				 return false;
			 }else{
				 var charpos = document.all.apellido_madre.value.search("[^A-Za-z/ \s/]"); 
				   if( charpos >= 0) 
				    { 
				     alert( "El campo apellido del responsable solo permite letras "); 
				     document.all.apellido_madre.focus();
				     return false;
				    }
				 }	
			if(document.all.nombre_madre.value==""){
				alert("Debe completar el campo nombre del responsable");
				document.all.nombre_madre.focus();
				 return false;
			 }else{
				 var charpos = document.all.nombre_madre.value.search("[^A-Za-z/ \s/]"); 
				   if( charpos >= 0) 
				    { 
				     alert( "El campo Nombre del responsable solo permite letras "); 
				     document.all.nombre_madre.focus();
				     return false;
				    }
				 }
			 if (document.all.clase_doc.value == 'P'){
				 var num1=document.all.nro_doc_madre.value;
				 var num2=document.all.num_doc.value;
				 if (num1 == num2){
					alert ("Los numero de documento no pueden ser iguales");
					document.all.num_doc.focus();
					return false;
					 }
				 }	
	 } // FIN Menores de 18 años
		 
	// Documento Ajeno y Menor de 1 año de Edad.
	 	if((document.all.clase_doc.value =='A') && (ed > 1)){
			var num1=document.all.nro_doc_madre.value;
			var num2=document.all.num_doc.value;
			if (num1 != num2){
				alert("Los numeros de documento deben coincidir");
				document.all.num_doc.focus();
				return false;
			}
		} // FIN
	 	
	// Fecha de Inscripcion mayor a 01/08/2004.
		if (document.all.fecha_inscripcion.value <= '01/08/2004'){
			alert ("La fecha de inscripcion debe ser mayor a 01/08/2004");
			document.all.fecha_inscripcion.focus();
			return false;
			} 	// FIN

		
		
	//Mujer Embarazada
	if(document.all.fecha_diagnostico_embarazo.value==""){
	alert("Debe completar el campo fecha de diagnostico de embarazo");
	 return false;
	 }
	if(document.all.fecha_probable_parto.value==""){
	alert("Debe completar el campo fecha probable de parto");
	 return false;
	 }

	
	if(document.all.cuie.value==-1){
		  alert('Debe Seleccionar un Efector');
		  document.all.cuie.focus();
		  return false;
		 } 

}//de function control_nuevos()

// Funcion para verificar que el DNI no tenga espacios en blanco
function CheckUserName(ele) { 
	if (/\s/.test(ele.value)) { 
		alert("No se permiten espacios en blanco");
		document.all.num_doc.focus(); } 
	} 
	
function editar_campos()
{
	inputs = document.form1.getElementsByTagName('input'); //Arma un arreglo con todos los campos tipo INPUT
	for (i=0; i<inputs.length; i++){
	    inputs[i].readOnly=false;
	}

	document.all.cancelar_editar.disabled=false;
	document.all.guardar_editar.disabled=false;
	document.all.editar.disabled=true;
 	return true;
}//de function control_nuevos()

/**********************************************************/
//funciones para busqueda abreviada utilizando teclas en la lista que muestra los clientes.
var digitos=10; //cantidad de digitos buscados
var puntero=0;
var buffer=new Array(digitos); //declaración del array Buffer
var cadena="";

function buscar_combo(obj)
{
   var letra = String.fromCharCode(event.keyCode)
   if(puntero >= digitos)
   {
       cadena="";
       puntero=0;
   }   
   //sino busco la cadena tipeada dentro del combo...
   else
   {
       buffer[puntero]=letra;
       //guardo en la posicion puntero la letra tipeada
       cadena=cadena+buffer[puntero]; //armo una cadena con los datos que van ingresando al array
       puntero++;

       //barro todas las opciones que contiene el combo y las comparo la cadena...
       //en el indice cero la opcion no es valida
       for (var opcombo=1;opcombo < obj.length;opcombo++){
          if(obj[opcombo].text.substr(0,puntero).toLowerCase()==cadena.toLowerCase()){
          obj.selectedIndex=opcombo;break;
          }
       }
    }//del else de if (event.keyCode == 13)
   event.returnValue = false; //invalida la acción de pulsado de tecla para evitar busqueda del primer caracter
}//de function buscar_op_submit(obj)

// Función para mostra la información de embarazo y de ser menor la información del adulto.
function cambiar_patalla(){

	// Masculino - Menor de 18 años edad no muestra la información de embarazo, muestra la información de menor vive con adulto 
	//y pide la información del adulto aunque el menor no viva con el. 
	if ((document.all.sexo.value == 'M') && (document.all.id_categoria.value == '5')) {
		document.all.cat_emb.style.display='none';
        document.all.cat_nino.style.display='inline';
        document.all.mva.style.display='inline';
        document.all.memb.style.display='none';
        }//fin
	
	// Masculino - Mayor de edad 19 años no muestra la información de embarazo, no muestra la información de menor vive con adulto. 
	if ((document.all.sexo.value == 'M') && (document.all.id_categoria.value == '6')) {
	   document.all.cat_emb.style.display='none';
       document.all.cat_nino.style.display='none';
       document.all.mva.style.display='none';
       document.all.memb.style.display='none';
       } //fin

	// Femenino - Menor de 9 años no muestra la información de embarazo, muestra la información de menor vive con adulto 
	//y pide la información del adulto aunque el menor no viva con el. 
	if ((document.all.sexo.value == 'F') && (document.all.edades.value <= 9)) {
		 document.all.cat_emb.style.display='none';
         document.all.cat_nino.style.display='inline';
         document.all.mva.style.display='inline';
         document.all.memb.style.display='none';
         }

	// Femenino - Mayor de 10 años puede o no estar embarazada, muestra la información de menor vive con adulto
	// pide la información del adulto aunque el menor no viva con el y pregunta si esta o no embarazada.
	if ((document.all.sexo.value == 'F') && (document.all.edades.value >= 10)) {
		document.all.cat_emb.style.display='none';
        document.all.cat_nino.style.display='inline';
        document.all.mva.style.display='inline';
        document.all.memb.style.display='inline';
               // Si esta embarazada muestra la información del embarazo
        if (document.all.menor_embarazada.value=='S'){
        	document.all.cat_emb.style.display='inline';
            } //fin
			//Si no esta embarazada muestra el combo solamente.
        if (document.all.menor_embarazada.value=='N'){
    		document.all.memb.style.display='inline';
               }
		} //fin
		
	// Femenino - Mayor de 19 años puede o no estar embarazada,pregunta si esta o no 
	// embarazada para pedir la información de embarazo, no muestra la información de menor vive con adulto. 
	if ((document.all.sexo.value == 'F') && (document.all.id_categoria.value == '6')) {
	   document.all.cat_emb.style.display='none';
       document.all.cat_nino.style.display='none';
       document.all.mva.style.display='none';
       document.all.memb.style.display='inline';
       // Si esta embarazada muestra la información del embarazo
       if (document.all.menor_embarazada.value=='S'){
       	document.all.cat_emb.style.display='inline';
           } //fin
       		//Si no esta embarazada muestra el combo solamente.
       if (document.all.menor_embarazada.value=='N'){
		document.all.memb.style.display='inline';
           }
    } //fin

} // FIN Cambiar_Patalla()

// Calculo de días para fecha de Nacimiento Mayor a Fecha Actual
function fechaNacAct(){  
    var d1 = $('#fecha_nac').val().split("/");  
    var dat1 = new Date(d1[2], parseFloat(d1[1])-1, parseFloat(d1[0]));  
    var d2 = $('#fecha_inscripcion').val().split("/");  
    var dat2 = new Date(d2[2], parseFloat(d2[1])-1, parseFloat(d2[0]));  
  
    var fin = dat2.getTime() - dat1.getTime();  
    var dias = Math.floor(fin / (1000 * 60 * 60 * 24))    
  
    return dias;  
}  // FIN

function verificaFPP(){  
    var d1 = $('#fecha_probable_parto').val().split("/");  
    var dat1 = new Date(d1[2], parseFloat(d1[1])-1, parseFloat(d1[0]));  
    var d2 = $('#fecha_inscripcion').val().split("/");  
    var dat2 = new Date(d2[2], parseFloat(d2[1])-1, parseFloat(d2[0]));  
  
    var fin = dat2.getTime() - dat1.getTime();  
    var dias = Math.floor(fin / (1000 * 60 * 60 * 24))    
  
    return dias;
    
    
}  // FIN

// Valida que la Fecha Probable de Parto no supere los 45 días después del Parto
function mostrarDias(){
	if (verificaFPP () >= '46'){
		
	alert ("No se puede Inscribir porque supero los 45 días después del Parto");
	document.all.fecha_probable_parto.focus();
	return false;
	}
} // FIN

// Fecha Diagnostico de Embarazo no puede ser superior a la Fecha de Inscripción
function validaFDE(){  
    var d1 = $('#fecha_diagnostico_embarazo').val().split("/");  
    var dat1 = new Date(d1[2], parseFloat(d1[1])-1, parseFloat(d1[0]));  
    var d2 = $('#fecha_inscripcion').val().split("/");  
    var dat2 = new Date(d2[2], parseFloat(d2[1])-1, parseFloat(d2[0]));  
  
    var fin = dat2.getTime() - dat1.getTime();  
    var dias = Math.floor(fin / (1000 * 60 * 60 * 24))    
  
    return dias;  
}  // FIN

// Valida que la Fecha de Diagnostico de Embarazo sea menor a la Fecha de Inscripcion
function mostrarFDE(){
	if ((validaFDE() <= '-1') || (validaFDE() == '0')) {
		
	alert ("La Fecha de Diagnostico de Embarazo tiene que ser menor a la Fecha de Inscripción");
	}
} // FIN


// calcula la edad y da el valor de la categoria
function edad(Fecha){
fecha = new Date(Fecha)
hoy = new Date()
ed = parseInt(((hoy -fecha)/365/24/60/60/1000)+1)

if ((fechaNacAct() <= '-1' ) || (fechaNacAct() == '0'))
	
{
	alert("La Fecha de Nacimiento no puede ser igual o mayor al día de hoy");
	document.all.fecha_nac.focus();
	return false;
}


//si es mayor de 18 años categoria 6
if (ed >= 18) {
		document.getElementById('id_categoria').value = '6';
		document.getElementById("edades").value =  ed ;
		if (document.all.clase_doc.value == 'A')
			alert ("Mayor de 18, el documento debe ser Propio")
			document.all.clase_doc.focus();
				
	}
//si es menor de 17 años categoria 5
	if (ed <= 17) {
		document.getElementById('id_categoria').value = '5';
		document.getElementById("edades").value =  ed;
		if ((ed >= 1) && (document.all.clase_doc.value == 'A')){
			alert ("Niño mayor de 1 año, el documento debe ser Propio")
			document.all.clase_doc.focus();
		}
	}
		
} //FIN calculo de edad y categoría

//Desarma la fecha para calcular la FPP
var aFinMes = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

function finMes(nMes, nAno){
 return aFinMes[nMes - 1] + (((nMes == 2) && (nAno % 4) == 0)? 1: 0);
}

 function padNmb(nStr, nLen, sChr){
  var sRes = String(nStr);
  for (var i = 0; i < nLen - String(nStr).length; i++)
   sRes = sChr + sRes;
  return sRes;
 }

 function makeDateFormat(nDay, nMonth, nYear){
  var sRes;
  sRes = padNmb(nDay, 2, "0") + "/" + padNmb(nMonth, 2, "0") + "/" + padNmb(nYear, 4, "0");
  return sRes;
 }
 
function incDate(sFec0){
 var nDia = parseInt(sFec0.substr(0, 2), 10);
 var nMes = parseInt(sFec0.substr(3, 2), 10);
 var nAno = parseInt(sFec0.substr(6, 4), 10);
 nDia += 1;
 if (nDia > finMes(nMes, nAno)){
  nDia = 1;
  nMes += 1;
  if (nMes == 13){
   nMes = 1;
   nAno += 1;
  }
 }
 return makeDateFormat(nDia, nMes, nAno);
}

function decDate(sFec0){
 var nDia = Number(sFec0.substr(0, 2));
 var nMes = Number(sFec0.substr(3, 2));
 var nAno = Number(sFec0.substr(6, 4));
 nDia -= 1;
 if (nDia == 0){
  nMes -= 1;
  if (nMes == 0){
   nMes = 12;
   nAno -= 1;
  }
  nDia = finMes(nMes, nAno);
 }
 return makeDateFormat(nDia, nMes, nAno);
}

function addToDate(sFec0, sInc){
 var nInc = Math.abs(parseInt(sInc));
 var sRes = sFec0;
 if (parseInt(sInc) >= 0)
  for (var i = 0; i < nInc; i++) sRes = incDate(sRes);
 else
  for (var i = 0; i < nInc; i++) sRes = decDate(sRes);
 return sRes;
} //FIN Fecha para calculo de  FPP

// Calcula la FPP
function recalcF1(){
 with (document.form1){
  fecha_probable_parto.value = addToDate(fecha_diagnostico_embarazo.value, 280 - (semanas_embarazo.value *7));
	
	}
} // FIN FPP

// Funcion para que cuando presionas Enter en los campos no haga nada.
function pulsar(e) {
	  tecla = (document.all) ? e.keyCode :e.which;
	  return (tecla!=13);
} // FIN 

</script>
<form name='form1' action='ins_admin.php' accept-charset="utf-8" method='POST'>

<input type="hidden" value="<?=$id_planilla?>" name="id_planilla">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<?echo "<center><b><font size='+1' color='Blue'>$accion2</font></b></center>";?>
<table width="100%" cellspacing="0" border="1" bordercolor="#E0E0E0" align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
<select name=id_categoria id=id_categoria Style="display:none;" onKeypress="buscar_combo(document);" onblur="borrar_buffer();"
onchange="borrar_buffer(); cambiar_patalla(); document.forms[0].submit();" 
<?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
<?
$sql= "select * from uad.categorias order by id_categoria";
$res_efectores=sql($sql) or fin_pagina();?>
           
<option value='-1' selected>Seleccione</option>
<?while (!$res_efectores->EOF){ 
$id_categorial=$res_efectores->fields['id_categoria'];
$tipo_ficha=$res_efectores->fields['tipo_ficha'];
$categoria=$res_efectores->fields['categoria'];?>
<option value='<?=$id_categorial?>'<?if ($id_categoria==$id_categorial) echo "selected";?> <?echo $tipo_ficha."-".$categoria;?>></option>
<?$res_efectores->movenext();
}?>
</select>
<input type="hidden" value="<?=$usuario1?>" name="usuario1">
 <tr id="mo">
    <td>
    	<?
    	if (!$id_planilla) {
    	?>  
    	<font size=+1><b>Nuevo Formulario</b></font>   
    	<? }
        else {
        ?>
        <font size=+1><b>Formulario</b></font>   
        <? } ?>
       
    </td>
 </tr>
 <tr><td>
  <table width=100% align="center" class="bordes">
      <tr>     
       <td>
        <table class="bordes" align="center">                          
         <tr>	           
           <td align="center" colspan="4" id="ma">
            <b> Número de Formulario: <font size="+1" color="Blue"><?=($id_planilla)? $clave_beneficiario : "Nuevo"?></font> </b> <? if ($trans == 'Borrado'){?> <b><font size="+1" color="Blue"><?=($id_planilla)? $trans : $trans?></font></b><?}?>
            
           </td>
         </tr>
         
         <tr>	           
         <td align="right" width="20%">
				<b>Tipo de Transaccion:</b>
			</td>
			<td align="left" width="30%">			 	
			 <select name=tipo_transaccion Style="width=200px"
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();document.forms[0].submit()" 
				<?php if ($trans == 'Borrado')echo "disabled"?>
				>
			 <option value='A' <?if ($tipo_transaccion=='A') echo "selected"?>>Inscripcion</option>
			 <option value='M'<?if ($tipo_transaccion=='M') echo "selected"?>>Modificacion</option>
			 <option value='B'<?if ($tipo_transaccion=='B') echo "selected"?>>Baja</option>
			 </select>		
			</td>            
            <td align="left" colspan="2">
             <b><font size="0" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>

		</tr>      
		<tr>
		  <tr>
                <td align="right" width="20%">
         	  <b>Número de Documento:</b>
         	</td>         	
            <td align='left' width="30%">
              <input type="text" size="30" value="<?=$num_doc?>" name="num_doc" onblur="CheckUserName(this);" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
              <input type="submit" size="3" value="b" name="b">Seleccione [b] verificar <br><font color="Red">Sin Puntos</font>
            </td>
                </tr>
            <td align="right">
				<b>El Documento es:</b>
			</td>
			<td align="left">			 	
			 <select name=clase_doc Style="width=200px" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
			  <option value=P <?if ($clase_doc=='P') echo "selected"?>>Propio</option>
			  <option value=A <?if ($clase_doc=='A') echo "selected"?>>Ajeno</option>
			</select>
			</td> 
         	<td align="right">
				<b>Tipo de Documento:</b>
			</td>
			<td align="left">			 	
			 <select name=tipo_doc Style="width=200px" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
			  <option value=DNI <?if ($tipo_doc=='DNI') echo "selected"?>>Documento Nacional de Identidad</option>
			  <option value=LE <?if ($tipo_doc=='LE') echo "selected"?>>Libreta de Enrolamiento</option>
			  <option value=LC <?if ($tipo_doc=='LC') echo "selected"?>>Libreta Civica</option>
			  <option value=PA <?if ($tipo_doc=='PA') echo "selected"?>>Pasaporte Argentino</option>
			  <option value=CM <?if ($tipo_doc=='CM') echo "selected"?>>Certificado Migratorio</option>
			 </select>
			</td>
                </tr>
                 
         <tr>
         
         <td align="right">
         	  <b>Apellidos:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="30" value="<?=$apellido?>" name="apellido" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>
           	<td align="right">
         	  <b>Nombres:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="30" value="<?=$nombre?>" name="nombre" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>
         
            
         </tr>
        <tr>
                        <td align="right">
                            <b> Mail: </b>
                        </td>
                        <td align="left">
                            <input type="text" size="35" name="mail" value="<?=$mail?>" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
                        </td>
                        <td align="right">
                            <b>Celular:</b>
                        </td>
                        <td align="left">
                            <input type="text" size="30" name="celular" value="<?=$celular?>" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
                        </td>
         </tr>

         <tr>	           
           <td align="center" colspan="4" id="ma">
            <b> Datos de Nacimiento, Sexo, Origen y Estudios </b>
           </td>
         </tr>
         
         <tr>
         	<td align="right">
				<b>Sexo:</b>
			</td>

			<td align="left">			 	
						<select name=sexo Style="width=200px" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> >
			  <option value='-1'>Seleccione</option>
			  <option value=F <?if ($sexo=='F') echo "selected"?>>Femenino</option>
			  <option value=M <?if ($sexo=='M') echo "selected"?>>Masculino</option>
			  </select>
									
			</td> 

         	<td align="right">
				<b>Fecha de Nacimiento:</b> <input type="hidden" name="edades" id=edades value="<?=$edad?>">
			</td>
		    
			<td align="left">
		    	<input type=text name=fecha_nac id=fecha_nac onchange="esFechaValida(this);" onblur="edad(this.value); cambiar_patalla(); " value='<?=$fecha_nac;?>' size=15 onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
		    	<?=link_calendario('fecha_nac'); echo $edades;?> 
		    	</td>		    	     
		 
		</tr>   

		<tr>
			<td align="right" >
			<b>Extranjero/Pais:</b> <input type="hidden" name="paisn" value="<?=$paisn?>"> 
    		</td>
    		<td align="left">
    		<select id="pais_nac" name="pais_nac" onchange="showpais_nac();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>><?php echo $pais_nac;?></select>
    		</td>
     </tr>
       <tr>	           
           <td align="center" colspan="4" id="ma">
            <b> Originario </b>
           </td>
         </tr>
          <tr>
			<td align="right">
         	   <b>¿Pertenece a algún Pueblo Indígena?</b>
         	   	
         	</td>         	
            <td align='left'>
				<input type="radio" name="indigena" value="N" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if(($indigena == "N") or ($indigena==""))echo "checked" ;?> onclick="document.all.id_tribu.value='0';document.all.id_lengua.value='0';" > NO
				<input type="radio" name="indigena" value="S" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if($indigena == "S") echo "checked" ;?> onclick="document.all.id_tribu.disabled=false;document.all.id_lengua.disabled=false;"> SI
            </td>
             </tr>
          <tr>
         	<td align="right">
         	  <b>Pueblo Indigena:</b>
         	</td>         	
            <td align='left'>
              <select name=id_tribu Style="width=200px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				<?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
			 <option value='-1'>Seleccione</option>
			 <?
			 $sql= "select * from uad.tribus";
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$id=$res_efectores->fields['id_tribu'];
			    $nombre=$res_efectores->fields['nombre'];
			    
			    ?>
				<option value='<?=$id?>' <?if ($id_tribu==$id) echo "selected"?> ><?=$nombre?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>
            </td>
           	<td align="right">
         	  <b>Idioma O Lengua:</b>
         	</td>         	
            <td align='left'>
             <select name=id_lengua Style="width=200px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				<?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
			 <option value='-1'>Seleccione</option>
			 <?
			 $sql= "select * from uad.lenguas";
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$id=$res_efectores->fields['id_lengua'];
			    $nombre=$res_efectores->fields['nombre'];
			    
			    ?>
				<option value='<?=$id?>' <?if ($id_lengua==$id) echo "selected"?> ><?=$nombre?></option>
				
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>
            </td>
         </tr> 
         <tr>	           
           <td align="center" colspan="4" id="ma">
            <b> Estudios </b>
           </td>
         </tr>         
         <tr>
         	<td align="right">
         	   <b>Alfabetizado:</b>
         	   	
         	</td>         	
            <td align='left'>
            	<input type="radio" name="alfabeta" value="S" onclick="document.all.estudios[1].checked=true" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if(($alfabeta == "S") or ($alfabeta==""))echo "checked" ;?>> SI
				<input type="radio" name="alfabeta" value="N" onclick="document.all.estudios[0].checked=false;document.all.estudios[1].checked=false;document.all.estudios[2].checked=false;document.all.anio_mayor_nivel.value='0';" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if($alfabeta == "N") echo "checked" ;?>> NO
            </td>
           <td align="right">
            <b>Estado:</b>
            </td>    
            <td align="left">			 	
			<select name=estadoest Style="width=200px" <?php if (($id_planilla) and ($tipo_transaccion != "M")) echo "disabled"?>>

			  <option value=C <?if ($estadoest=='C') echo "selected"?>>Completo</option>
			  <option value=I <?if ($estadoest=='I') echo "selected"?>>Incompleto</option>
			
			  
			 </select>
			 </td>
            </tr>
             
         <tr>
        <td align="right">
         	   <b>Estudios:</b>         	   	
         	</td>         	
            <td align='left'>
				<input type="radio" name="estudios" value="Inicial" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if(($estudios == "INICIAL") or ($estudios=="Inicial"))echo "checked" ;?>>Inicial
				<input type="radio" name="estudios" value="Primario" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if(($estudios == "PRIMARIO") or ($estudios=="Primario"))echo "checked" ;?>>Primario
				<input type="radio" name="estudios" value="Secundario" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if (($estudios == "SECUNDARIO")or ($estudios=="Secundario"))echo "checked" ;?>>Secundario
				<input type="radio" name="estudios" value="Terciario" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if (($estudios == "TERCIARIO")or ($estudios=="Terciario"))echo "checked" ;?>>Terciario
				<input type="radio" name="estudios" value="Universitario" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if (($estudios == "UNIVERSITARIO")or ($estudios=="Universitario"))echo "checked" ;?>>Universitario
            </td>            
            
             <td align="right">
         	   <b>Años Mayor Nivel:</b>
         	</td>         	
            <td align='left'>
            	<input type="text" size="30" value='<?= $anio_mayor_nivel;?>' name="anio_mayor_nivel" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>
            
          </tr>
                             
         
 		<tr>	           
           <td align="center" colspan="4" id="ma">
            <b> Datos del Domicilio </b>
           </td>
         </tr>
         
         <tr>
         <td colspan="2" align="center" id="mva" style="display:<?=$mva1?>">
			 <b>Menor convive con adulto:</b><select name=menor_convive_con_adulto id=menor_convive_con_adulto Style="width=200px" <?php if (($id_planilla) and ($tipo_transaccion != "M")) echo "disabled"?>>
              <option value='' >Seleccione</option>
			  <option value=S <?if ($menor_convive_con_adulto=='S') echo "selected"?>>SI</option>
			  <option value=N <?if ($menor_convive_con_adulto=='N') echo "selected"?>>NO</option>
			
			  
			 </select>

			</td>
			<td colspan="2" align="center" id="cdomi" style="display:<?=$cdomi1?>" >
			 <b>Cambio de Domicilio:</b> <select name=cambiodom Style="width=200px" onchange="document.forms[0].submit()" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> >
			  <option value='-1'>Seleccione</option>
			  <option value=S <?if ($cambiodom=='S') echo "selected"?>>SI</option>
			  <option value=N <?if ($cambiodom=='N') echo "selected"?>>NO</option>
			  </select>
 			</td>
			</tr>
			
			<tr>
			  <td align="right">
         	  <b>Calle:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="30" value="<?=$calle?>" name="calle" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>
           	<td align="right">
         	  <b>N° de Puerta:</b>
         	  <input type="text" size="15" value="<?=$numero_calle?>" name="numero_calle" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
         	</td>         	
            <td align='left'>
			  <b>Piso:</b>
         	  <input type="text" size="15" value="<?=$piso?>" name="piso" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>         	
            </td>
         </tr>  
         
         <tr>
         	<td align="right">
         	  <b>Depto:</b>
         	  <input type="text" size="10" value="<?=$dpto?>" name="dpto" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
         	</td>         	
            <td align='left'>
			  <b>Mz:</b>
         	  <input type="text" size="10" value="<?=$manzana?>" name="manzana" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>         	
            </td>
         	<td align="right">
         	  <b>Entre Calle:</b>
         	  <input type="text" size="15" value="<?=$entre_calle_1?>" name="entre_calle_1" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
         	</td>         	
            <td align='left'>
			  <b>Entre Calle:</b>
         	  <input type="text" size="15" value="<?=$entre_calle_2?>" name="entre_calle_2" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>         	
            </td>         	
         </tr>  
         
         <tr>
         	<td align="right">
         	  <b>Telefono:</b>
         	</td>         	
            <td align='left'>
         	  <input type="text" size="30" value="<?=$telefono?>" name="telefono" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>         	
            </td>
         	<td align="right">
         	<b>Otro</b>(ej: vecino)         	     	
         	</td>
         	<td align="left">
         	<input type="text" size="30" name="otrotel" value="<?=$otrotel?>" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
         	</td>
         	</tr>
 <!-- Ajax -->
  <tr>
    <td align="right">
    <b>Departamento:</b> <input type="hidden" name="departamenton" value="<?=$departamenton?>"> 
    </td>
    <td align="left">
    <select id="departamento" name="departamento" onchange="showdepartamento();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>><?php echo $departamento;?></select>
    </td>
    <td align="right">
    <b>Localidad:</b><input type="hidden" name="localidadn" value="<?=$localidadn?>">
    </td>
    <td align="left">
    <select id="localidad" name="localidad" onchange="showlocalidad();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>><?php echo $opciones2;?></select>
    </td>
    </tr>
    <tr>
    <td align="right">
         	  <b>Codigo Postal:</b> <input type="hidden" name="cod_posn" value="<?=$cod_posn?>"> 
         	</td>         
         	 <td align='left'>	
           <select id="cod_pos" name="cod_pos" onchange="showcodpos();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>><?php echo $opciones5; ?></select>
               </td>
    <td align="right">
    <b>Municipio:</b><input type="hidden" name="municipion" value="<?=$municipion?>">
    </td>
    <td align="left">
    <select id="municipio" name="municipio" onchange="showmunicipio();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>><?php echo $opciones3; ?></select>
    </td>
    
    </tr>
 
          	<tr>
    <td align="right">
    <b>Barrio:</b><input type="hidden" name="barrion" value="<?=$barrion?>">
    </td>
    <td align="left">
    <select id="barrio" name="barrio" onchange="showbarrio();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>><?php echo $opciones4; ?></select>
    </td>        
         </tr>
<!--  Fin Ajax -->
 		<tr>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left' colspan="3">
              <textarea cols='80' rows='4' name='observaciones' <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>> <?=$observaciones;?> </textarea>
            </td>
         </tr>                          
                  
         <tr><td colspan="4"><table id="cat_nino" class="bordes" width="100%" style="display:<?= $datos_resp ?>;border:thin groove;">
         
         <tr>         
         <? if ($id_categoria!='6'){?>
         <td align="center" colspan="4" id="ma">
            <b> Datos del Responsable </b>
         </td>        
         </tr>
         <tr>
         	<td align="right" >
				<b>Datos de Responsable:</b>
			</td>
			<td align="left" >			 	
			 <select name=responsable Style="width=200px" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> >
			  <option value='-1' <? if ($responsable=='-1') echo "selected"?>>Seleccione</option> 
			  <option value=MADRE <?if ($responsable=='MADRE') echo "selected"?>>Madre</option>
			  <option value=PADRE <?if ($responsable=='PADRE') echo "selected"?>>Padre</option>
			  <option value=TUTOR <?if ($responsable=='TUTOR') echo "selected"?>>Tutor</option>
			 </select>
			</td> 
			
		</tr>
                   <tr>
          	<td align="right">
				<b>Tipo de Documento:</b>
			</td>
			<td align="left">			 	
			 <select name=tipo_doc_madre Style="width=200px" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
			  <option value=DNI <?if ($tipo_doc_madre=='DNI') echo "selected"?>>Documento Nacional de Identidad</option>
			  <option value=LE <?if ($tipo_doc_madre=='LE') echo "selected"?>>Libreta de Enrolamiento</option>
			  <option value=LC <?if ($tipo_doc_madre=='LC') echo "selected"?>>Libreta Civica</option>
			  <option value=PA <?if ($tipo_doc_madre=='PA') echo "selected"?>>Pasaporte Argentino</option>
			  <option value=CM <?if ($tipo_doc_madre=='CM') echo "selected"?>>Certificado Migratorio</option>
			 </select>
			</td>          	
         	<td align="right" width="20%">
         	  <b>Documento:</b>
         	</td>         	
            <td align='left' width="30%">
              <input type="text" size="30" value="<?=$nro_doc_madre?>" name="nro_doc_madre" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>            
         </tr>
                  <tr>
         	<td align="right">
         	  <b>Apellidos:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="30" value="<?=$apellido_madre?>" name="apellido_madre" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>
           	<td align="right">
         	  <b>Nombres:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="30" value="<?=$nombre_madre?>" name="nombre_madre" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>
         </tr> 
                  <tr>	           
           <td align="center" colspan="4" id="ma">
            <b> Alfabetización </b>
           </td>        
         </tr>
                  <tr>
         	<td align="right">
         	   <b>Alfabeta:</b>
         	   	
         	</td>         	
            <td align='left'>
            	<input type="radio" name="alfabeta_madre" value="S" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> onclick="document.all.estudios_madre[1].checked=true" checked> SI
				<input type="radio" name="alfabeta_madre" value="N" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> onclick="document.all.estudios_madre[0].checked=false;document.all.estudios_madre[1].checked=false;document.all.estudios_madre[2].checked=false;document.all.anio_mayor_nivel_madre.value='0';"> NO
            </td>
            <td align="right">
            <b>Estado:</b>
            </td>    
            
         <td align="left">			 	
			<select name=estadoest_madre Style="width=200px" <?php if (($id_planilla) and ($tipo_transaccion != "M")) echo "disabled"?>>

			  <option value=C <?if ($estadoest_madre=='C') echo "selected"?>>Completo</option>
			  <option value=I <?if ($estadoest_madre=='I') echo "selected"?>>Incompleto</option>
			
			  
			 </select>
			 </td>
            
            
         </tr>
           <tr>
            <td align="right">
         	   <b>Estudios:</b>         	   	
         	</td>         	
            <td align='left'>
				<input type="radio" name="estudios_madre" value="Inicial" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if(($estudios_madre == "INICIAL") or ($estudios_madre=="Inicial"))echo "checked" ;?>>Inicial
				<input type="radio" name="estudios_madre" value="Primario" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if(($estudios_madre == "PRIMARIO") or ($estudios_madre=="Primario"))echo "checked" ;?>>Primario
				<input type="radio" name="estudios_madre" value="Secundario" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if (($estudios_madre == "SECUNDARIO")or ($estudios_madre=="Secundario"))echo "checked" ;?>>Secundario
				<input type="radio" name="estudios_madre" value="Terciario" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if (($estudios_madre == "TERCIARIO")or ($estudios_madre=="Terciario"))echo "checked" ;?>>Terciario
				<input type="radio" name="estudios_madre" value="Universitario" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if (($estudios_madre == "UNIVERSITARIO")or ($estudios_madre=="Universitario"))echo "checked" ;?>>Universitario
            </td>            
            <td align="right">
         	   <b>Años Mayor Nivel:</b>         	   	
         	</td>         	
            <td align='left'>
            	<input type="text" size="30" value='<?=$anio_mayor_nivel_madre?>' name="anio_mayor_nivel_madre" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>
         </tr>
         
         
                  <?}?>
         </table>
         
         </td></tr>
        
         <tr id="memb" style="display:<?=$memb?>;">
          
			<td align="center" colspan="4" >
			  <b>Embarazada:</b><select name=menor_embarazada id=menor_embarazada Style="width=200px" onchange="cambiar_patalla();" <?php if (($id_planilla) and ($tipo_transaccion != "M")) echo "disabled"?>>
			   <option value=N <?if ($menor_embarazada=='N') echo "selected"?>>NO</option>
			 	<option value=S <?if ($menor_embarazada=='S') echo "selected"?>>SI</option>
			  
			 </select>
			</td>
			</tr>
		 
         <tr><td colspan="4"><table id="cat_emb" class="bordes" width="100%" style="display:<?= $embarazada ?>;border:thin groove">
         
         <? if ($sexo!='m'){?>
         <tr>	           
           <td align="center" colspan="4" id="ma">
            <b> Datos de Embarazo </b>
           </td>        
         </tr>
           <tr>
         	   <td align="right">
         	   <b>F.U.M.:</b>         	   	
         	</td>         	
            <td align='left'>
            	<?$fecha_comprobante=date("d/m/Y");?>
		    	<input type=text name=fum id=fum size=15 onblur="esFechaValida(this);" value='<?=$fum;?>' onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
		    	<?=link_calendario("fum");?>	
            </td>		    
           <td align="right">
				<b>Fecha de Diag. de Embarazo:</b>
			</td>
		    <td align="left">	       
		    	 <input type=text name=fecha_diagnostico_embarazo id=fecha_diagnostico_embarazo onblur="esFechaValida(this);mostrarFDE()" value='<?=$fecha_diagnostico_embarazo;?>' onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> size=15>
		    	 <?=link_calendario("fecha_diagnostico_embarazo");?>					    	 
		    </td>
		
		</tr>   
		<tr>
		    <td align="right">
         	   <b>Semana de Embarazo:</b>         	   	
         	</td>         	
            <td align='left'>
            
            	<input type="text" name="semanas_embarazo" id=semanas_embarazo  value='<?=$semanas_embarazo;?>' onblur="recalcF1();"  size="30" onkeypress="return pulsar(event)"  <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>		    
		<td align="right">
				<b>Fecha Probable de Parto:</b>
			</td>
			
		    <td align="left">
		    	 <input type=text name=fecha_probable_parto id=fecha_probable_parto onblur="esFechaValida(this);mostrarDias();" value='<?=$fecha_probable_parto;?>' size=15 onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
		    	 <?=link_calendario("fecha_probable_parto");?>					    	 
		    </td>
		    </tr>
		  <tr>
		  <td align="center" colspan="4" id="ma">
            <b> Riesgo Cardiovascular </b>
           </td>       
           </tr>
		 <tr>
           <td align="right">
         	   <b>Score de riesgo:</b>         	   	
         	</td>         	
            <td align='left'>
            	<input type="text" size="10" value='<?=$score_riesgo?>' name="score_riesgo" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>
          </tr>
         <?}?>
         </table>
         
         <tr>	           
           <td align="center" colspan="4" id="ma">
            <b> Discapacidad </b>
           </td>
         </tr>
         <tr>
         <td align="center" colspan="4">
			<input type=checkbox name=discv value='Visual' <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if($discv == "VISUAL") echo "checked" ;?> > Visual
			<input type=checkbox name=disca value='Auditiva' <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if($disca == "AUDITIVA") echo "checked" ;?> > Auditiva
			<input type=checkbox name=discmo value='Motriz' <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if($discmo == "MOTRIZ") echo "checked" ;?> > Motriz
			<input type=checkbox name=discme value='Mental' <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if($discme == "MENTAL") echo "checked" ;?> > Mental
			<input type=checkbox name=otradisc value='Otra Discapacidad' <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> <?php if($otradisc == "OTRA DISCAPACIDAD") echo "checked" ;?> > Otra discapacidad 
         </td>
         </tr>
         <tr>	           
           <td align="center" colspan="4" id="ma">
            <b> Fecha de Inscripcion </b>
           </td>
         </tr>

         <tr>
         	<td align="center" width="20%" colspan="4">
				<b>Fecha de Inscripcion:</b><input type=text onblur="esFechaValida(this);" name=fecha_inscripcion id=fecha_inscripcion value='<?=$fecha_inscripcion;?>' size=15 onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
		    	 <?=link_calendario("fecha_inscripcion");?>					    	 
			</td>
		     
		</tr>
         
         <tr>	           
           <td align="center" colspan="4" id="ma">
            <b> Efector Habitual </b>
           </td>
         </tr>
         
         <tr>
         	<td align="center" width="20%" colspan="4">
				<b>Efector Habitual:</b> <select name=cuie Style="width=300px" 
        	onKeypress="buscar_combo(this);"
			onblur="borrar_buffer();"
			onchange="borrar_buffer();"
        	<?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
			 
			 <?
			  $user_login1=substr($_ses_user['login'],0,6);
			  if (es_cuie($_ses_user['login']))
			  $sql= "select cuie, nombre, com_gestion from nacer.efe_conv where cuie='$user_login1' order by nombre";	
			  else{
			  	echo"<option value=-1>Seleccione</option>";
			  	$sql= "select cuie, nombre, com_gestion from nacer.efe_conv order by nombre";
			 	 }
			 
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$cuiel=$res_efectores->fields['cuie'];
			    $nombre_efector=$res_efectores->fields['nombre'];
			    
			    ?>
				<option value='<?=$cuiel?>' <?if ($cuie==$cuiel) echo "selected"?> ><?=$cuiel." - ".$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>
			</td>
				    
		</tr>
	
         <tr>	           
           <td align="center" colspan="4" id="ma">
            <b> Observaciones Generales </b>
           </td>        
         </tr>
         
         <tr align="center">
         	
            <td align='center' colspan="4">
              <textarea cols='80' rows='4' name='obsgenerales' <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> > <?=$obsgenerales;?>  </textarea>
            </td>
         </tr>   
                    
        </table>
      </td>      
     </tr> 
   

   <?if ((!($id_planilla))and ($clave_beneficiario=='')){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guardar Planilla</b>
  		</td>
  	</tr>  
  	 <tr align="center">
	 	<td>
	 		<b><font size="0" color="Red">Nota: Verifique todos los datos antes de guardar</font> </b>
	 	</td>
	</tr>
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Planilla' onclick="return control_nuevos()"
         title="Guardar datos de la Planilla" >
       </td>
      </tr>
     
     <?}?>
     
 </table>           
<br>
<?if ($clave_beneficiario != ''){?>
<table class="bordes" align="center" width="100%">
		 <tr align="center" id="sub_tabla">
		 	<td>	
		 		Editar DATO   
		 	</td>
		 </tr>
		 <tr align="center">
		 	<td>
		 		<b><font size="0" color="Red">Nota: Verifique todos los datos antes de guardar</font> </b>
		 	</td>
		 </tr>
		 
		 <tr>
		    <td align="center">
	          <input type="submit" name="guardar_editar" value="Guardar" title="Guardar"  style="width=130px" <?php if ($tipo_transaccion != "M") echo "disabled"?> onclick="return control_nuevos()">&nbsp;&nbsp;
		      <input type="button" name="cancelar_editar" value="Cancelar" title="Cancelar Edicion" style="width=130px" <?php if ($tipo_transaccion != "M") echo "disabled"?> onclick="history.back(-1);">		      
		      <?if (permisos_check("inicio","permiso_borrar")) $permiso="";
			  else $permiso="disabled";?>
		  <input type="submit" name="borrar" value="Borrar" style="width=130px" <?=$permiso?> <?php if ($tipo_transaccion != "B") echo "disabled"?>>
		    </td>
		 </tr> 
	 </table>	
	 <br>
	 <?}?>
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='ins_listado.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 
 </table></td></tr>
 
 
 </table>
</form>
 
 <?=fin_pagina();// aca termino ?>
