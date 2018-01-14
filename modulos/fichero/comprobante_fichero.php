<?
require_once ("../../config.php");
include_once("../facturacion/funciones.php");

//require ('funcion.php'); 

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

cargar_calendario();
$usuario1=$_ses_user['id'];

function bisiesto_local($anio_actual){ 
    $bisiesto=false; 
    //probamos si el mes de febrero del año actual tiene 29 días 
      if (checkdate(2,29,$anio_actual)) 
      { 
        $bisiesto=true; 
    } 
    return $bisiesto; 
} 
function calculo_dias($fecha_hast,$fecha_eq){ // calculamos la diferencia de dias en entero 
		//defino fecha 1
		$anio1 = substr($fecha_hast,6,9);
		$mes1 = substr($fecha_hast,3,-5);
		$dia1 = substr($fecha_hast,0,2);
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
		$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);
		//obtengo el valor absoulto de los días (quito el posible signo negativo)
		$dias_diferencia = abs($dias_diferencia);
		$meses_trans=$dias_diferencia/30;
		//quito los decimales a los días de diferencia
		//$dias_diferencia = floor($dias_diferencia); 
		$meses_trans = floor($meses_trans); 
		 return ($meses_trans); 
}

function edad_con_meses($fecha_de_nacimiento){ 
	$fecha_actual = date ("Y-m-d"); 

	// separamos en partes las fechas 
	$array_nacimiento = explode ( "-", $fecha_de_nacimiento ); 
	$array_actual = explode ( "-", $fecha_actual ); 

	$anos =  $array_actual[0] - $array_nacimiento[0]; // calculamos años 
	$meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses 
	$dias =  $array_actual[2] - $array_nacimiento[2]; // calculamos días 

	//ajuste de posible negativo en $días 
	if ($dias < 0) 
	{ 
		--$meses; 

		//ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual 
		switch ($array_actual[1]) { 
			   case 1:     $dias_mes_anterior=31; break; 
			   case 2:     $dias_mes_anterior=31; break; 
			   case 3:  
					if (bisiesto_local($array_actual[0])) 
					{ 
						$dias_mes_anterior=29; break; 
					} else { 
						$dias_mes_anterior=28; break; 
					} 
			   case 4:     $dias_mes_anterior=31; break; 
			   case 5:     $dias_mes_anterior=30; break; 
			   case 6:     $dias_mes_anterior=31; break; 
			   case 7:     $dias_mes_anterior=30; break; 
			   case 8:     $dias_mes_anterior=31; break; 
			   case 9:     $dias_mes_anterior=31; break; 
			   case 10:     $dias_mes_anterior=30; break; 
			   case 11:     $dias_mes_anterior=31; break; 
			   case 12:     $dias_mes_anterior=30; break; 
		} 

		$dias=$dias + $dias_mes_anterior; 
	} 

	//ajuste de posible negativo en $meses 
	if ($meses < 0) 
	{ 
		--$anos; 
		$meses=$meses + 12; 
	} 
	$edad_con_meses_result= array("anos"=>$anos,"meses"=>$meses,"dias"=>$dias);
	return  $edad_con_meses_result;
}
if($anular=="anular"){
	 $db->StartTrans();

	$query="update fichero.fichero set
             anular='SI'
             where id_fichero=$id_fichero";

    sql($query, "Error al Anular la Ficha de Atencion") or fin_pagina();
    $accion="Se Anulado la Ficha de Atencion";   
    		if ($entidad_alta=='nu'){
				$id_beneficiarios=$id; 
		    	$id_smiafiliados=0;
		    	$update_f="update fichero.fichero set fecha_pcontrol_flag='1' 
		    	where id_fichero=(select max(id_fichero) from fichero.fichero  where id_beneficiarios='$id'  AND (anular='' or anular IS NULL))";
		    }//carga de prestacion a paciente NO PLAN NACER
		    if ($entidad_alta=='na'){
				$id_beneficiarios=0; 
		    	$id_smiafiliados=$id;
		    	$update_f="update fichero.fichero set fecha_pcontrol_flag='1' where id_fichero=(select max(id_fichero) from fichero.fichero  where id_smiafiliados='$id'  AND (anular='' or anular IS NULL))";
		    }//carga de prestacion a paciente PLAN NACER
		    sql($update_f, "No se puede actualizar los registros") or fin_pagina();
		     
     /*cargo los log*/ 
		    $usuario=$_ses_user['name'];
		     $fecha_carga=date("Y-m-d H:i:s");
			$log="insert into fichero.log_fichero 
				   (id_fichero, fecha, tipo, descripcion, usuario) 
			values ($id_fichero, '$fecha_carga','Anular Ficha de Atencion','Nro. fichero $id_fichero', '$usuario')";
			sql($log) or fin_pagina();
	 
    $db->CompleteTrans();   
}
function suma_fechas($fecha,$ndias){
      if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha))
      	list($dia,$mes,$anio)=split("/", $fecha);
      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))
        list($dia,$mes,$anio)=split("-",$fecha);
      $nueva = mktime(0,0,0, $mes,$dia,$anio) + $ndias * 24 * 60 * 60;
      $nuevafecha=date("d-m-Y",$nueva);
      return ($nuevafecha);  
} 
if ($_POST['percentilo']=="Sugerir Percentilos"){
	// transformo la talla en cm
	$t_aux=$talla *100;
	$e_mes=calculo_dias($fecha_comprobante,$afifechanac);
				 $db->StartTrans();
		if(($fecha_comprobante!='' || $peso!='') && $e_mes <= 72) { // percentillo edad-peso
				
				//calculemos percentilo de peso-edad
				 $query="SELECT
						fichero.perc_edadpeso.edadpeso,
						fichero.perc_edadpeso.tres,
						fichero.perc_edadpeso.diez,
						fichero.perc_edadpeso.noventa,
						fichero.perc_edadpeso.noventaysiete
						FROM
						fichero.perc_edadpeso
						where fichero.perc_edadpeso.edadpeso=$e_mes";
			
			  $res_query=  sql($query, "Error Generar Percentilo de Peso-Edad") or fin_pagina();
				$tres=$res_query->fields['tres'];
				$diez=$res_query->fields['diez'];
				$noventa=$res_query->fields['noventa'];
				$noventaysiete=$res_query->fields['noventaysiete'];
				
				if($peso < $tres) $percen_peso_edad=1;
				elseif($peso <= $diez) $percen_peso_edad=2;
				elseif($peso <= $noventa) $percen_peso_edad=3;
				elseif($peso <= $noventaysiete) $percen_peso_edad=4;
				else $percen_peso_edad=5;
		}else $percen_peso_edad='';
		if(($fecha_comprobante!='' || $talla!='' )&& $e_mes <= 72 ) {//calculemos percentilo de talla-edad
				 $query1="SELECT
							fichero.perc_edadtalla.edadtalla,
							fichero.perc_edadtalla.tres,
							fichero.perc_edadtalla.noventaysiete
							FROM
							fichero.perc_edadtalla
						where fichero.perc_edadtalla.edadtalla=$e_mes";
			
			  $res_query1=  sql($query1, "Error Generar Percentilo de Talla-Edad") or fin_pagina();
				$tres1=$res_query1->fields['tres'];
				$noventaysiete1=$res_query1->fields['noventaysiete'];
				$tallaAux=$talla*100;
				if($tallaAux <= $tres1) $percen_talla_edad=1;
				elseif($tallaAux <= $noventaysiete1) $percen_talla_edad=2;  
				else $percen_talla_edad=3;
		}else $percen_talla_edad='';		
		if(($peso!='' || $talla!='')&& $talla <= 1.20) { // percentillo peso-talla 0.5-0.5
			// la tabla de talla va de .00 a .50 por ello redondeo los valores de 0 a 50 va 50 de 51 a 99 va 0
			//IMPORTANTE TENER ENCUENTA; La talla en la tabla figura en cm
			  $tallaAux=$talla*100;
			$talla2= explode(".",$tallaAux);
			if($talla2[1]>50 || $talla2[1]=='') 
				$talla2[1]=0;
			else $talla2[1]=5;
			if ($talla2[1]==0) { 
				$talla3=$talla2[0];}// armo la talla para generar el valor
			else{ $talla3=$talla2[0].'.'.$talla2[1];}// armo la talla para generar el valor
			
				 $query2="SELECT
							fichero.perc_tallapeso.tallapeso,
							fichero.perc_tallapeso.tres,
							fichero.perc_tallapeso.diez,
							fichero.perc_tallapeso.noventa,
							fichero.perc_tallapeso.noventaysiete
							FROM
							fichero.perc_tallapeso
						where fichero.perc_tallapeso.tallapeso= $talla3";
			
			  $res_query2=  sql($query2, "Error Generar Percentilo de Peso-Talla") or fin_pagina();
				$tres2=$res_query2->fields['tres'];
				$diez2=$res_query2->fields['diez'];
				$noventa2=$res_query2->fields['noventa'];
				$noventaysiete2=$res_query2->fields['noventaysiete'];
				
				if($peso <= $tres2) $percen_peso_talla=1;
				elseif($peso <= $diez2) $percen_peso_talla=2;
				elseif($peso <= $noventa2) $percen_peso_talla=3;
				elseif($peso <= $noventaysiete2) $percen_peso_talla=4;
				else $percen_peso_talla=5;
		}else $percen_peso_talla='';
		if(($perim_cefalico!='' || $fecha_comprobante!='')&& $e_mes <= 60) {//calculemos percentilo de perimetrocefarico-edad(en mes)
				 $query3="SELECT
							fichero.perc_edadpcef.edadpcef,
							fichero.perc_edadpcef.tres,
							fichero.perc_edadpcef.noventaysiete
							FROM
							fichero.perc_edadpcef
							WHERE fichero.perc_edadpcef.edadpcef=$e_mes";
			
			  $res_query3=  sql($query3, "Error Generar Percentilo de Talla-Edad") or fin_pagina();
				$tres3=$res_query3->fields['tres'];
				$noventaysiete3=$res_query3->fields['noventaysiete'];
				
				if($perim_cefalico < $tres3) $percen_perim_cefali_edad=1;
				elseif($perim_cefalico <= $noventaysiete3) $percen_perim_cefali_edad=2;
				else $percen_perim_cefali_edad=3;
		}else $percen_perim_cefali_edad='';		
		if(($imc!='' || $fecha_comprobante!='')&& $e_mes <= 72) {//calculemos percentilo de imc edad
				 $query4="SELECT
							fichero.perc_imc.edadimc,
							fichero.perc_imc.tres,
							fichero.perc_imc.diez,
							fichero.perc_imc.ochentaycinco,
							fichero.perc_imc.noventaysiete
							FROM
							fichero.perc_imc
							WHERE fichero.perc_imc.edadimc=$e_mes";
			
			 	$res_query4=  sql($query4, "Error Generar Percentilo de Peso-Edad") or fin_pagina();
				$tres4=$res_query4->fields['tres'];
				$diez4=$res_query4->fields['diez'];
				$ochentaycinco4=$res_query4->fields['ochentaycinco'];
				$noventaysiete4=$res_query4->fields['noventaysiete'];
				
				if($peso < $tres4) $percen_imc_edad=1;
				elseif($peso <= $diez4) $percen_imc_edad=2;
				elseif($peso <= $ochentaycinco4) $percen_imc_edad=3;
				elseif($peso <= $noventaysiete4) $percen_imc_edad=4;
				else $percen_imc_edad=5;
		}	else $percen_imc_edad='';	
				

	$db->CompleteTrans();   
		
} 

if ($_POST['guardar']=="Guardar"){
	 $db->StartTrans();
			//if($ta=='')$ta=0;
			$tension_arterial_M=$_POST['tension_arterial_M'];
			$tension_arterial_m=$_POST['tension_arterial_m'];
			$maxima=str_pad($tension_arterial_M,3,"0",STR_PAD_LEFT);
			$minima=str_pad($tension_arterial_m,3,"0",STR_PAD_LEFT);
			$tension_arterial="$maxima"."/"."$minima";
			
			
			$fecha_carga=date("Y-m-d H:i:s");
			$cuie=$_POST['cuie'];
			$nom_medico=$_POST['nom_medico'];
			$fecha_comprobante=$_POST['fecha_comprobante'];
			$comentario=$_POST['comentario'];
			$fecha_comprobante=Fecha_db($fecha_comprobante);
			if ($entidad_alta=='nu'){
				$id_beneficiarios=$id; 
		    	$id_smiafiliados=0;
		    	$update_f="update fichero.fichero set fecha_pcontrol_flag='0' where id_beneficiarios='$id' ";
		    }//carga de prestacion a paciente NO PLAN NACER
		    if ($entidad_alta=='na'){
				$id_beneficiarios=0; 
		    	$id_smiafiliados=$id;
		    	$update_f="update fichero.fichero set fecha_pcontrol_flag='0' where id_smiafiliados='$id'";
		    }//carga de prestacion a paciente PLAN NACER
		    sql($update_f, "No se puede actualizar los registros") or fin_pagina();
		 
			$q="select nextval('fichero.fichero_id_fichero_seq') as id_fichero";
		    $id_fichero=sql($q) or fin_pagina();
		    $id_fichero=$id_fichero->fields['id_fichero'];	
		        
		    $periodo= str_replace("-","/",substr($fecha_comprobante,0,7));
		    $fecha_control=$fecha_comprobante; //la fecha de control = fecha del comprobante.
		    
		    if($fecha_pcontrol=='')$fecha_pcontrol='1000-01-01';
		    else $fecha_pcontrol=Fecha_db($fecha_pcontrol);
		    
			if($tunner=='')$tunner=0;	
		    if(($edad >19)){
		    	if($peso=='')$peso=0;
		    	if($talla=='')$talla=0;
		    	if($imc=='')$imc=0;
		    	//if($ta=='')$ta=0;	    	
		    }
		    if($tasa_materna=='' or $tasa_materna==-1){
		    	$tasa_materna=0;
		    }	

		   
		    if($peso_embarazada=='')$peso_embarazada=0;
		    if($altura_uterina=='')$altura_uterina=0;
		    if($imc_uterina=='')$imc_uterina=0;
		    if($semana_gestacional=='')$semana_gestacional=0;
			if($perim_cefalico=='')$perim_cefalico=0;
			if($diabetico=='')$diabetico='NO';
			if($hipertenso=='')$hipertenso='NO';
			
			if($embarazo=='embarazo'){
				 $embarazo='SI';
				 $fpp=Fecha_db($fpp);
		    	 $fum=Fecha_db($fum);
		    	 $f_diagnostico=Fecha_db($f_diagnostico);
			}
		    else {
		    	 $embarazo='NO';
		    	 $fpp='1000-01-01'; 
				 $fum='1000-01-01'; 
				 $f_diagnostico='1000-01-01';	 
		    }
		    $query="insert into fichero.fichero
			             (id_fichero,  cuie, nom_medico, fecha_control, comentario, periodo, peso, talla, 
			             imc ,ta, tunner, c_vacuna,  ex_clinico_gral, ex_trauma, ex_cardio, ex_odontologico, ex_ecg, hemograma, vsg, 
			             glucemia, uremia, ca_total, orina_cto, chagas ,obs_laboratorio, ergometria, obs_adolesc, id_smiafiliados, id_beneficiarios, 
			             conclusion,tasa_materna,salud_rep,metodo_anti,fecha_pcontrol,fpp, fum, f_diagnostico, peso_embarazada,altura_uterina,imc_uterina, semana_gestacional, 
			             rx_torax,rx_col_vertebral,otros,rx_observaciones,otros_obs,fecha_pcontrol_flag,percen_peso_edad,percen_talla_edad,perim_cefalico,percen_perim_cefali_edad,percen_peso_talla,percen_imc_edad,diabetico,hipertenso,embarazo,publico,ag_visual, obs_ecg)
		             values
		             ($id_fichero, '$cuie', '$nom_medico', '$fecha_control', '$comentario', '$periodo', '$peso', 
			             '$talla', '$imc', '$tension_arterial', '$tunner', '$c_vacuna',  '$ex_clinico_gral', '$ex_trauma', '$ex_cardio', '$ex_odontologico', '$ex_ecg', '$hemograma', 
			             '$vsg', '$glucemia', '$uremia', '$ca_total', '$orina_cto', '$chagas', '$obs_laboratorio', '$ergometria', '$obs_adolesc', '$id_smiafiliados', '$id_beneficiarios', 
			             '$conclusion','$tasa_materna','$salud_rep','$metodo_anti' ,'$fecha_pcontrol', '$fpp', '$fum', '$f_diagnostico', '$peso_embarazada', '$altura_uterina','$imc_uterina','$semana_gestacional',
			             '$rx_torax','$rx_col_vertebral','$otros','$rx_observaciones','$otros_obs','1','$percen_peso_edad','$percen_talla_edad','$perim_cefalico','$percen_perim_cefali_edad','$percen_peso_talla', '$percen_imc_edad','$diabetico','$hipertenso','$embarazo','$publico', '$ag_visual', '$obs_ecg')";	
		    sql($query, "Error al insertar el comprobante") or fin_pagina();	    
		    $accion="Registro Grabado.";	    
		    
		    /*cargo los log*/ 
		    $usuario=$_ses_user['name'];
			$log="insert into fichero.log_fichero 
				   (id_fichero, fecha, tipo, descripcion, usuario) 
			values ($id_fichero, '$fecha_carga','Nuevo Registro','Nro. fichero $id_fichero', '$usuario')";
			sql($log) or fin_pagina();		 
		    $db->CompleteTrans(); 

		//alta en el sistema de facturacion ----------------------------------------------------------------------
		$q_2="select * from nacer.smiafiliados where id_smiafiliados='$id_smiafiliados'";
		$res_2=sql($q_2,"no puedo ejecutar consulta");
		$activo=trim($res_2->fields['activo']);
		$clavebeneficiario=$res_2->fields['clavebeneficiario'];

		
		if (($activo=='S')&&($embarazo=='NO') && ($salud_rep!='SI')){//facturo a los niños
			$db->StartTrans();
			//comprobante
			$q="select nextval('comprobante_id_comprobante_seq') as id_comprobante";
		    $id_comprobante=sql($q) or fin_pagina();
		    $id_comprobante=$id_comprobante->fields['id_comprobante'];	
		    
		    $periodo= str_replace("-","/",substr($fecha_control,0,7));
		    		    
		    $query="insert into facturacion.comprobante
		             (id_comprobante, cuie, nombre_medico, fecha_comprobante, clavebeneficiario, id_smiafiliados, fecha_carga,periodo,comentario,id_servicio,activo)
		             values
		             ($id_comprobante,'$cuie','$nom_medico','$fecha_control','$clavebeneficiario', $id_smiafiliados,'$fecha_carga','$periodo','Desde fichero','1','$activo')";	
		    sql($query, "Error al insertar el comprobante") or fin_pagina();	    
		    $usuario=$_ses_user['name'];
			$log="insert into facturacion.log_comprobante 
				   (id_comprobante, fecha, tipo, descripcion, usuario) 
			values ($id_comprobante, '$fecha_carga','Nuevo Comprobante','Nro. Comprobante $id_comprobante', '$usuario')";
			sql($log) or fin_pagina();	
			
			//prestaciones 
			//tengo que sacar el el id_nomenclador_detalles
			$q="select * from nacer.efe_conv 
				left join facturacion.nomenclador_detalle using (id_nomenclador_detalle)
				where cuie='$cuie'";
		    $res_efector=sql($q,"Error en traer el precio del nomenclador") or fin_pagina();
			$id_nomenclador_detalle=$res_efector->fields['id_nomenclador_detalle'];
			
			//sacar codigo segun prestacion
			if (date("Y-m-d")-fecha_db($afifechanac) <= '1') {
				$codigo= "NPE 32";
			} elseif (date("Y-m-d")-fecha_db($afifechanac) > '1') {
			    $codigo= "NPE 33";
			}
			
			switch ($fecha_comprobante) {
				case $fecha_comprobante<'2012-11-01': $id_nomenclador_detalle=4; break;
				case $fecha_comprobante<'2014-01-01': $id_nomenclador_detalle=9; break;
				default: $id_nomenclador_detalle=10; break;
			}
			//tengo que sacar el id_nomenclado
			$q="select * from facturacion.nomenclador
				where id_nomenclador_detalle='$id_nomenclador_detalle' and codigo='$codigo'";
		    $res_nom=sql($q,"Error en traer el id_nomenclador") or fin_pagina();
			$nomenclador=$res_nom->fields['id_nomenclador'];
			
			//---------------------------------------------------------------------------------
							
				$dias_de_vida=GetCountDaysBetweenTwoDates(fecha_db($afifechanac), $fecha_comprobante);
				if (($dias_de_vida>=0)&&($dias_de_vida<=28)) $grupo_etareo='Neonato';
				if (($dias_de_vida>28)&&($dias_de_vida<=2190)) $grupo_etareo='Cero a Cinco Años';
				if (($dias_de_vida>2190)&&($dias_de_vida<=3650)) $grupo_etareo='Seis a Nueve Años';
				if (($dias_de_vida>3650)&&($dias_de_vida<=7300)) $grupo_etareo='Adolecente';
				if (($dias_de_vida>7300)&&($dias_de_vida<=23725)) $grupo_etareo='Adulto';	
			
			if (($id_nomenclador_detalle > 8)&&($fecha_comprobante>='2012-11-01')){//saco los codigos para la nueva fase
					//sacar codigo segun prestacion
				if (($grupo_etareo=='Neonato')or ($grupo_etareo=='Cero a Cinco Años')){
					if ($dias_de_vida <= '365') {
						$codigo= "C001";
						$desc="Pediátrica en menores de 1 año";
					} elseif ($dias_de_vida > '365') {
						$codigo= "C001";
						$desc="Pediátrica de 1 a 6 años";
					}
				}
				if ($grupo_etareo=='Seis a Nueve Años'){					
						$codigo= "C001";
						$desc="Control en Niños de 6 a 9 años";					
				}
				if ($grupo_etareo=='Adolecente'){					
						$codigo= "C001";
						$desc="Examen Periódico de Salud del adolescente";					
				}
				if ($grupo_etareo=='Adulto'){					
						$codigo= "C001";
						$desc="Examen periódico de salud mujer";					
				}			
				
				//tengo que sacar el id_nomenclado
				$q="select * from facturacion.nomenclador
					where id_nomenclador_detalle='$id_nomenclador_detalle' and codigo='$codigo' and descripcion='$desc' ";
				$res_nom=sql($q,"Error en traer el id_nomenclador") or fin_pagina();
				$nomenclador=$res_nom->fields['id_nomenclador'];
			}//-------------------------------------------------------------------------------------
			
			//tengo que sacar el id_anexo
			$q="select * from facturacion.anexo
				where id_nomenclador_detalle='$id_nomenclador_detalle' and id_nomenclador='$nomenclador'";
		    $res_nom=sql($q,"Error en traer el id_anexo") or fin_pagina();
			$anexo=$res_nom->fields['id_anexo'];
			
			if ($anexo==''){
				$q="select * from facturacion.anexo
					where prueba='No Corresponde' and id_nomenclador_detalle='$id_nomenclador_detalle'";
			    $res_nom=sql($q,"Error en traer el id_anexo") or fin_pagina();
				$anexo=$res_nom->fields['id_anexo'];
			}
			
			//saco id_prestacion
			$q="select nextval('facturacion.prestacion_id_prestacion_seq') as id_prestacion";
		    $id_prestacion=sql($q) or fin_pagina();
		    $id_prestacion=$id_prestacion->fields['id_prestacion'];
		
		    //traigo el precio de la prestacion del nomencladorpara guardarla en la 
		    //tabla de prestacion por que si se cambia el precio en el nomenclador
		    //cambia el precio de todas las prestaciones y las facturas
		    $q="select precio from facturacion.nomenclador where id_nomenclador=$nomenclador";
		    $precio_prestacion=sql($q,"Error en traer el precio del nomenclador") or fin_pagina();
		    $precio_prestacion=$precio_prestacion->fields['precio'];
		    $precio_prestacion=$precio_prestacion;
		    
		    
			if (valida_prestacion_nuevo_nomenclador($id_comprobante,$nomenclador)){
				$query="insert into facturacion.prestacion
						 (id_prestacion,id_comprobante, id_nomenclador,cantidad,precio_prestacion,id_anexo,peso,tension_arterial,diagnostico,estado_envio)
						 values
						 ($id_prestacion,$id_comprobante,$nomenclador,'1',$precio_prestacion,$anexo,'0','00/00','A97','n')";
			
				sql($query, "Error al insertar la prestacion") or fin_pagina();
				
				/*cargo los log*/ 
				$usuario=$_ses_user['name'];
				$log="insert into facturacion.log_prestacion
					   (id_prestacion, fecha, tipo, descripcion, usuario) 
				values ($id_prestacion, '$fecha_carga','Nueva PRESTACION','Nro. prestacion $id_prestacion', '$usuario')";
				sql($log) or fin_pagina();
				$accion.=" Se Genero el Comprobante Nro  $id_comprobante.";
			} else $accion.=" Supero tasa de Uso";
		}
		if ($embarazo=='NO'){ //cargo trazadoras de niño-------------------------------------------------------------------------------------------------
			$fecha_carga=date("Y-m-d H:m:s");
			$usuario=$_ses_user['name'];
			$db->StartTrans();         
		    
			$q="select nextval('trazadoras.nino_new_id_nino_new_seq') as id_planilla";
		    $id_planilla=sql($q) or fin_pagina();
		    $id_planilla=$id_planilla->fields['id_planilla'];
		   
			$tension_arterial_M=$_POST['tension_arterial_M'];
			$tension_arterial_m=$_POST['tension_arterial_m'];
			$maxima=str_pad($tension_arterial_M,3,"0",STR_PAD_LEFT);
			$minima=str_pad($tension_arterial_m,3,"0",STR_PAD_LEFT);
			$tension_arterial="$maxima"."/"."$minima";
		   
		   $fecha_nac=fecha_db($afifechanac);
		   $triple_viral="1980-01-01";  
		      
		   ($talla!=0)?$imc=($peso/($talla*$talla)):$imc=0;
		    $talla=$talla*100; //paso talla en metro a centimetro
			$num_doc=$afidni;
			$apellido=$afiapellido;
			$nombre=$afinombre;
			(date("Y-m-d")-fecha_db($afifechanac) <= '1')?$nino_edad=0:$nino_edad=1;
			
		   $query="insert into trazadoras.nino_new
		             (id_nino_new,cuie,clave,clase_doc,tipo_doc,num_doc,apellido,nombre,fecha_nac,fecha_control,peso,talla,
		  				percen_peso_edad,percen_talla_edad,perim_cefalico,percen_perim_cefali_edad,imc,percen_imc_edad,percen_peso_talla,
		  				triple_viral,nino_edad,observaciones,fecha_carga,usuario,ta)
		             values
		             ('$id_planilla','$cuie','','R','DNI','$num_doc','$apellido','$nombre','$fecha_nac',
		             	'$fecha_control','$peso','$talla','$percen_peso_edad','$percen_talla_edad','$perim_cefalico',
		             	'$percen_perim_cefali_edad','$imc','$percen_imc_edad','$percen_peso_talla','$triple_viral',
		             	'$nino_edad','Desde el Fichero','$fecha_carga','$usuario','$tension_arterial')";

		    sql($query, "Error al insertar la Planilla") or fin_pagina();		    
		    $db->CompleteTrans(); 
			$accion.=" Grabo TRZ.";
		}
		if (($activo=='S')&&($embarazo=='SI')){//facturo embarazadas -------------------------------------------------------------
			$db->StartTrans();
			//comprobante
			$q="select nextval('comprobante_id_comprobante_seq') as id_comprobante";
		    $id_comprobante=sql($q) or fin_pagina();
		    $id_comprobante=$id_comprobante->fields['id_comprobante'];	
		    
		    $periodo= str_replace("-","/",substr($fecha_control,0,7));
		    		    
		    $query="insert into facturacion.comprobante
		             (id_comprobante, cuie, nombre_medico, fecha_comprobante, clavebeneficiario, id_smiafiliados, fecha_carga,periodo,comentario,id_servicio,activo)
		             values
		             ($id_comprobante,'$cuie','$nom_medico','$fecha_control','$clavebeneficiario', $id_smiafiliados,'$fecha_carga','$periodo','Desde fichero','1','$activo')";	
		    sql($query, "Error al insertar el comprobante") or fin_pagina();	    
		    $usuario=$_ses_user['name'];
			$log="insert into facturacion.log_comprobante 
				   (id_comprobante, fecha, tipo, descripcion, usuario) 
			values ($id_comprobante, '$fecha_carga','Nuevo Comprobante','Nro. Comprobante $id_comprobante', '$usuario')";
			sql($log) or fin_pagina();	
			
			//prestaciones 
			//tengo que sacar el el id_nomenclador_detalles
			$q="select * from nacer.efe_conv 
				left join facturacion.nomenclador_detalle using (id_nomenclador_detalle)
				where cuie='$cuie'";
		    $res_efector=sql($q,"Error en traer el precio del nomenclador") or fin_pagina();
			$id_nomenclador_detalle=$res_efector->fields['id_nomenclador_detalle'];
			
			
			//verifico si hay mem 01
			$q1="SELECT comprobante.id_comprobante
				FROM
				facturacion.comprobante
				INNER JOIN facturacion.prestacion ON facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante
				INNER JOIN facturacion.nomenclador ON facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador
				WHERE
				facturacion.comprobante.id_smiafiliados = '$id_smiafiliados' AND
				facturacion.nomenclador.codigo = 'MEM 01' AND
				facturacion.comprobante.fecha_comprobante::DATE BETWEEN CURRENT_DATE-360 AND CURRENT_DATE";
			$res_mem=sql($q1,"Error al buscar mem01") or fin_pagina();
			
			//sacar codigo segun prestacion
			if ($res_mem->recordcount()==0)$codigo= "MEM 01";
			else $codigo= "MEM 02";
			
			//tengo que sacar el id_nomenclado
			$q="select * from facturacion.nomenclador
				where id_nomenclador_detalle='$id_nomenclador_detalle' and codigo='$codigo'";
		    $res_nom=sql($q,"Error en traer el id_nomenclador") or fin_pagina();
			$nomenclador=$res_nom->fields['id_nomenclador'];
			
			//---------------------------------------------------------------------------------
			if ($id_nomenclador_detalle > 8){//saco los codigos para la nueva fase
				//verifico si hay mem 01
				$q1="SELECT comprobante.id_comprobante
					FROM
					facturacion.comprobante
					INNER JOIN facturacion.prestacion ON facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante
					INNER JOIN facturacion.nomenclador ON facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador
					WHERE
					facturacion.comprobante.id_smiafiliados = '$id_smiafiliados' AND
					facturacion.nomenclador.codigo = 'C005' AND
					facturacion.nomenclador.descripcion = 'Control prenatal de 1ra.vez' AND
					facturacion.comprobante.fecha_comprobante::DATE BETWEEN CURRENT_DATE-360 AND CURRENT_DATE";
				$res_mem=sql($q1,"Error al buscar") or fin_pagina();
		
				//sacar codigo segun prestacion
				//if ($res_mem->recordcount()==0){
				if ($fecha_control==$f_diagnostico){
					$codigo= "C005";
					$desc="Control prenatal de 1ra.vez";
				}
				else{
					 $codigo= "C006";
					 $desc="Ulterior de control prenatal";
				 }
				
				//tengo que sacar el id_nomenclado
				$q="select * from facturacion.nomenclador
					where id_nomenclador_detalle='$id_nomenclador_detalle' and codigo='$codigo' and descripcion='$desc'";
				$res_nom=sql($q,"Error en traer el id_nomenclador") or fin_pagina();
				$nomenclador=$res_nom->fields['id_nomenclador'];
			}//------------------------------------------
			
			//tengo que sacar el id_anexo
			$q="select * from facturacion.anexo
				where id_nomenclador_detalle='$id_nomenclador_detalle' and id_nomenclador='$nomenclador'";
		    $res_nom=sql($q,"Error en traer el id_anexo") or fin_pagina();
			$anexo=$res_nom->fields['id_anexo'];
			
			if ($anexo==''){
				$q="select * from facturacion.anexo
					where prueba='No Corresponde' and id_nomenclador_detalle='$id_nomenclador_detalle'";
			    $res_nom=sql($q,"Error en traer el id_anexo") or fin_pagina();
				$anexo=$res_nom->fields['id_anexo'];
			}
			
			//saco id_prestacion
			$q="select nextval('facturacion.prestacion_id_prestacion_seq') as id_prestacion";
		    $id_prestacion=sql($q) or fin_pagina();
		    $id_prestacion=$id_prestacion->fields['id_prestacion'];
		
		    //traigo el precio de la prestacion del nomencladorpara guardarla en la 
		    //tabla de prestacion por que si se cambia el precio en el nomenclador
		    //cambia el precio de todas las prestaciones y las facturas
		    $q="select precio from facturacion.nomenclador where id_nomenclador=$nomenclador";
		    $precio_prestacion=sql($q,"Error en traer el precio del nomenclador") or fin_pagina();
		    $precio_prestacion=$precio_prestacion->fields['precio'];
		    $precio_prestacion=$precio_prestacion;
		    
		    if (valida_prestacion_nuevo_nomenclador($id_comprobante,$nomenclador)){
			$query="insert into facturacion.prestacion
		             (id_prestacion,id_comprobante, id_nomenclador,cantidad,precio_prestacion,id_anexo,peso,tension_arterial,diagnostico,estado_envio)
		             values
		             ($id_prestacion,$id_comprobante,$nomenclador,'1',$precio_prestacion,$anexo,'0','00/00','W78','n')";
		
		    sql($query, "Error al insertar la prestacion") or fin_pagina();
		    
		    /*cargo los log*/ 
		    $usuario=$_ses_user['name'];
			$log="insert into facturacion.log_prestacion
				   (id_prestacion, fecha, tipo, descripcion, usuario) 
			values ($id_prestacion, '$fecha_carga','Nueva PRESTACION','Nro. prestacion $id_prestacion', '$usuario')";
			sql($log) or fin_pagina();
			$accion.=" Se Genero el Comprobante Nro  $id_comprobante.";
			} else $accion.=" Supero tasa de Uso";
		}
		if ($embarazo=='SI'){//cargo trazadoras embarazada-------------------------------------------------------------------------------------------------
			$fecha_carga=date("Y-m-d H:m:s");
			$usuario=$_ses_user['name'];
			$db->StartTrans();         
			    
			$q="select nextval('trazadoras.embarazadas_id_emb_seq') as id_planilla";
			$id_planilla=sql($q) or fin_pagina();
			$id_planilla=$id_planilla->fields['id_planilla'];
			   
			if ($semana_gestacional=='')$sem_gestacion='0'; else $sem_gestacion=$semana_gestacional;
			if ($fum=='')$fum='NULL';else $fum=$fum;
			if ($fpp=='')$fpp='NULL';else $fpp=$fpp;
			
			//$tension_arterial_M=$_POST['tension_arterial_M'];
			//$tension_arterial_m=$_POST['tension_arterial_m'];
			$maxima=str_pad($tension_arterial_M,3,"0",STR_PAD_LEFT);
			$minima=str_pad($tension_arterial_m,3,"0",STR_PAD_LEFT);
			$tension_arterial="$maxima"."/"."$minima";
			
			$fpcp=$fecha_control;			
			$num_doc=$afidni;
			$apellido=$afiapellido;
			$nombre=$afinombre;
			      
			    $query="insert into trazadoras.embarazadas
			             (id_emb,cuie,clave,tipo_doc,num_doc,apellido,nombre,fecha_control,
			             sem_gestacion,fum,fpp,fpcp,observaciones,fecha_carga,usuario,vdrl,antitetanica,ta)
			             values
			             ('$id_planilla','$cuie','','DNI','$num_doc','$apellido',
			             '$nombre','$fecha_control','$sem_gestacion','$fum',
			             '$fpp','$fpcp','Desde el Fichero','$fecha_carga','$usuario','','','$tension_arterial')";

			sql($query, "Error al insertar la Planilla") or fin_pagina();
			$db->CompleteTrans(); 
			$accion.=" Grabo TRZ.";
		}
		
	$db->CompleteTrans();			
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

if ($entidad_alta=='nu'){//carga de prestacion a paciente NO PLAN NACER
	$sql="select * from leche.beneficiarios
	where id_beneficiarios=$id";
    $res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();
    
    $afiapellido=trim($res_comprobante->fields['apellido']);
	$afinombre=trim($res_comprobante->fields['nombre']);
	$afidni=trim($res_comprobante->fields['documento']);
	$nombre=$res_comprobante->fields['domicilio'];
	$afifechanac=$res_comprobante->fields['fecha_nac'];
	$sexo=$res_comprobante->fields['sexo'];
}

if ($entidad_alta=='na'){//carga de prestacion a paciente PLAN NACER
	$sql="select * from nacer.smiafiliados
	 left join nacer.efe_conv on (cuieefectorasignado=cuie)
	 where id_smiafiliados=$id";
    $res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();
    
    $afiapellido=trim($res_comprobante->fields['afiapellido']);
	$afinombre=trim($res_comprobante->fields['afinombre']);
	$afidni=$res_comprobante->fields['afidni'];
	$nombre=$res_comprobante->fields['nombre'];
	$afifechanac=$res_comprobante->fields['afifechanac'];
	$sexo=$res_comprobante->fields['afisexo'];
}

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos(){
 if(document.all.cuie.value==-1){
  alert('Debe Seleccionar un EFECTOR');
  document.all.cuie.focus();
  return false;
 }
 // Fecha de Inscripcion mayor a 01/08/2004.
 //genero la fehca del dia actual para compararla conla fecha de control
 	var fechaActual = new Date();
    dia = fechaActual.getDate();
    mes = fechaActual.getMonth() +1;
    anno = fechaActual.getFullYear();
    if (dia <10) dia = "0" + dia;
    if (mes <10) mes = "0" + mes;  
 
    fechaHoy = dia + "/" + mes + "/" + anno;
	
	/*if (document.all.fecha_comprobante.value > fechaHoy){
			alert ("La fecha de control no puede ser mayor a la fecha de hoy");
			document.all.fecha_comprobante.focus();
			return false;
			} 
	if (document.all.fecha_pcontrol.value <= document.all.fecha_comprobante.value){
			alert ("La fecha del proximo control no puede ser menor o igual a la fecha Control");
			document.all.fecha_pcontrol.focus();
			return false;
			} */
	<?if (GetCountDaysBetweenTwoDates($afifechanac,date("Y-m-d"))<=365){?>
	 if(document.all.perim_cefalico.value==""){
		alert('Debe ingresar el Perimetro Cefalico');
		document.all.perim_cefalico.focus();
		return false;
	}
	if(document.all.percen_perim_cefali_edad.value=="-1"){
		alert('Debe ingresar el Percentilo Perimetro Cefalico');
		document.all.percen_perim_cefali_edad.focus();
		return false;
	}
	<?}?>
	
	<?if (GetCountDaysBetweenTwoDates($afifechanac,date("Y-m-d"))<=3280){?>
	 if(document.all.percen_peso_edad.value=="-1"){
		alert('Debe ingresar el Percentilo Peso Edad');
		document.all.percen_peso_edad.focus();
		return false;
	}
	if(document.all.percen_talla_edad.value=="-1"){
		alert('Debe ingresar el Percentilo Talla Edad');
		document.all.percen_talla_edad.focus();
		return false;
	}
	if(document.all.percen_peso_talla.value=="-1"){
		alert('Debe ingresar el Percentilo Peso Talla');
		document.all.percen_peso_talla.focus();
		return false;
	}
	<?}?>
	
	
	<?if (GetCountDaysBetweenTwoDates($afifechanac,date("Y-m-d"))>3290){?>
	
	if(document.all.tension_arterial_M.value==""){
	 alert("Debe completar el campo Tension Arterial MAXIMA");
	 document.all.tension_arterial_M.focus();
	 return false;
	 }else{
 		var tension_arterial_M=document.all.tension_arterial_M.value;
		if(isNaN(tension_arterial_M)){
			alert('El dato de la tension Arterial MAXIMA debe ser un Numero Entero');
			document.all.tension_arterial_M.focus();
			return false;
		}
	}
	
	if(document.all.tension_arterial_m.value==""){
	 alert("Debe completar el campo de Tension Arterial MINIMA");
	 document.all.tension_arterial_m.focus();
	 return false;
	 }else{
 		var tension_arterial_m=document.all.tension_arterial_m.value;
		if(isNaN(tension_arterial_m)){
			alert('El dato de la tension Arterial MINIMA debe ser un Numero Entero');
			document.all.tension_arterial_m.focus();
			return false;
		}
	}
	<?}?>
	 if (document.all.peso.value==""){
		alert('Debe ingresar el Peso');
		document.all.peso.focus();
		return false;
	
	 } else if (document.all.peso.value >190){
	 	alert('El peso de la persona va desde 10 a 190 Kg');
	 	document.all.peso.focus();
	  return false;
	}
	 
	 if(document.all.talla.value==""){
		alert('Debe ingresar la talla');
	  	document.all.talla.focus();
		return false;
	 }else 	if(document.all.talla.value >= 2.50  ){
	 	alert('la talla no puede superar los 2.50 metros');
	 	document.all.talla.focus();
		return false;
	 }
	 
	 if (document.all.embarazo.checked==true){
		if(document.all.f_diagnostico.value==""){
			alert('Debe ingresar la fecha de diagnostico');
			document.all.f_diagnostico.focus();
			return false;
		}
		if(document.all.fum.value==""){
			alert('Debe ingresar la FUM');
			document.all.fum.focus();
			return false;
		}
		
		
		if(diff_fecha()){
			alert('La fecha de FUM debe ser anterior a la fecha de Diagnostico');
			document.all.fum.focus();
			return false;
		}	
		
		if(document.all.fpp.value==""){
			alert('Debe ingresar la FPP');
			document.all.fpp.focus();
			return false;
		}
		
		if(document.all.semana_gestacional.value==""){
			alert('Debe ingresar las semanas gestacional');
			document.all.semana_gestacional.focus();
			return false;
		}

		if(document.all.semana_gestacional.value>45){
			alert('La cantidad de Semanas Gestacionales Exede un Embarazo Normal, Revise Fecha de Diagnostico y FUM');
			document.all.f_diagnostico.focus();
			return false;
		}
		if(document.all.tension_arterial_M.value==""){
		alert("Debe completar el campo Tension Arterial MAXIMA");
		document.all.tension_arterial_M.focus();
		return false;
		}else{
 		var tension_arterial_M=document.all.tension_arterial_M.value;
		if(isNaN(tension_arterial_M)){
			alert('El dato de la tension Arterial MAXIMA debe ser un Numero Entero');
			document.all.tension_arterial_M.focus();
			return false;
		}
	}
	
	if(document.all.tension_arterial_m.value==""){
	 alert("Debe completar el campo de Tension Arterial MINIMA");
	 document.all.tension_arterial_m.focus();
	 return false;
	 }else{
 		var tension_arterial_m=document.all.tension_arterial_m.value;
		if(isNaN(tension_arterial_m)){
			alert('El dato de la tension Arterial MINIMA debe ser un Numero Entero');
			document.all.tension_arterial_m.focus();
			return false;
		}
	}
	 }

 var peso=document.all.peso.value;
 var talla=document.all.talla.value;
 if (confirm('Esta Seguro que Desea el Registro?')){
	document.all.peso.value = peso.replace(',','.');
	document.all.talla.value = talla.replace(',','.');
	return true;
 }
 else return false;	
}//de function control_nuevos()

var img_ext='<?=$img_ext='../../imagenes/rigth2.gif' ?>';//imagen extendido
var img_cont='<?=$img_cont='../../imagenes/down2.gif' ?>';//imagen contraido
function muestra_tabla(obj_tabla,nro){
 oimg=eval("document.all.imagen_"+nro);//objeto tipo IMG
 if (obj_tabla.style.display=='none'){
 	obj_tabla.style.display='inline';
    oimg.show=0;
    oimg.src=img_ext;
 }
 else{
 	obj_tabla.style.display='none';
    oimg.show=1;
	oimg.src=img_cont;
 }
}
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


function calculo_fpp_sem(){
	
	if (document.all.f_diagnostico.value!="" && document.all.fum.value!="") {
		var f_c=document.all.fecha_comprobante.value;
		var f_f=document.all.fum.value;
		
		var array_c = f_c.split("/");
		var array_f = f_f.split("/");
		
		var ch_c=array_c[2] + "/" + array_c[1] + "/" +array_c[0];
		var ch_f=array_f[2] + "/" + array_f[1] + "/" +array_f[0];
				
		var fecha_control= new Date (ch_c);
		/*var fecha_control_i = new Date ();
		fecha_control=fecha_control_i.getDate() + "/" + (fecha_control_i.getMonth() +1) + "/" + fecha_control_i.getFullYear();*/
		
		var fum=new Date (ch_f);
		var fpp= new Date (ch_f);
		var dias= 10;
		var dias_fpp = 282;
						
	var tiempo=fecha_control.getTime();
	
	
	 
    //Calculamos los milisegundos sobre la fecha que hay que sumar o restar...
    milisegundos=parseInt(dias*24*60*60*1000);
	
    //Modificamos la fecha actual
    total=fecha_control.setTime(tiempo+milisegundos);
	
	
	var fin = fecha_control.getTime() - fum.getTime();
	var dias = Math.floor(fin / (1000 * 60 * 60 * 24))
	var semanas = Math.floor(dias/7);
	
	document.all.semana_gestacional.readonly = true;
	document.all.semana_gestacional.value=semanas;
	document.all.semana_gestacional.readonly = false;
	
	var tiempo_fpp=fpp.getTime();
	miii= parseInt(dias_fpp*24*60*60*1000);
	total_1=fpp.setTime(tiempo_fpp+miii);
	
	var anio=fpp.getFullYear();
	var mes= ("0" + (fpp.getMonth()+1)).slice (-2);
	var dia= ("0" + fpp.getDate()).slice (-2);
	var fecha_fpp= dia + "/" + mes + "/" + anio;
	
	document.all.fpp.readonly = true;
	document.all.fpp.value=fecha_fpp;
	document.all.fpp.readonly = false;
	}
     	 
}


function diff_fecha(){
	
	if (document.all.f_diagnostico.value!="" && document.all.fum.value!="") {
		var f_c=document.all.f_diagnostico.value;
		var f_f=document.all.fum.value;
		
		var array_c = f_c.split("/");
		var array_f = f_f.split("/");
		
		var ch_c=array_c[2] + "/" + array_c[1] + "/" +array_c[0];
		var ch_f=array_f[2] + "/" + array_f[1] + "/" +array_f[0];
				
		var f_diagnostico_1= new Date (ch_c);
		var fum_1=new Date (ch_f);
		
		if (f_diagnostico_1>fum_1) { return false }
		else {return true}
	}
else {alert ('los valores de fecha de diagnostico y fum estan vacios, revisar');
return false;}	
}		

function calculo_imc(){
	var t=document.all.talla.value;
	var p=document.all.peso.value;
	var i=0;
	var peso=document.all.peso.value;
	var talla=document.all.talla.value;
	document.all.peso.value = peso.replace(',','.');
	document.all.talla.value = talla.replace(',','.');
	
   if(t!=0) i=(p/(t * t));
  	 var original=i;
	 var result=Math.round(i*100)/100 ;
	  document.all.imc.readonly = true;
	  document.all.imc.value=result; 
	  document.all.imc.readonly = false;	 
}
function calculo_imc_ult(){
	var t=document.all.altura_uterina.value;
	var p=document.all.peso_embarazada.value;
	var i=0;
	var peso=document.all.peso.value;
	var talla=document.all.talla.value;
	document.all.peso.value = peso.replace(',','.');
	document.all.talla.value = talla.replace(',','.');
	
   if(t!=0) i=(p/(t * t));
  	 var original=i;
	 var result=Math.round(i*100)/100 ;
	  document.all.imc_uterina.focus();
	  document.all.imc_uterina.value=result; 
}

function habilita_hemg(){
	if(document.all.hemograma_selec.checked == true){   
	    document.all.hemograma[0].disabled = false;
	    document.all.hemograma[1].disabled = false;
	    document.all.hemograma[2].disabled = false;
	}else{ 
		   document.all.hemograma[0].checked = false;
		   document.all.hemograma[1].checked = false;
		   document.all.hemograma[2].checked = false;

	    document.all.hemograma[0].disabled = true;
	    document.all.hemograma[1].disabled = true;
	    document.all.hemograma[2].disabled = true;
	
	}
}
function habilita_vsg(){
if(document.all.vsg_selec.checked == true){   
    document.all.vsg[0].disabled = false;
    document.all.vsg[1].disabled = false;
    document.all.vsg[2].disabled = false;
}else{ 
	document.all.vsg[0].checked = false;
	document.all.vsg[1].checked = false;
	document.all.vsg[2].checked = false;
    document.all.vsg[0].disabled = true;
    document.all.vsg[1].disabled = true;
    document.all.vsg[2].disabled = true;

}
    }
function habilita_glucemia(){
	if(document.all.glucemia_selec.checked == true){   
	    document.all.glucemia[0].disabled = false;
	    document.all.glucemia[1].disabled = false;
	    document.all.glucemia[2].disabled = false;
	}else{ 
		document.all.glucemia[0].checked = false;
		document.all.glucemia[1].checked = false;
		document.all.glucemia[2].checked = false;
	    document.all.glucemia[0].disabled = true;
	    document.all.glucemia[1].disabled = true;
	    document.all.glucemia[2].disabled = true;
	
	}
    }
function habilita_uremia(){
	if(document.all.uremia_selec.checked == true){   
	    document.all.uremia[0].disabled = false;
	    document.all.uremia[1].disabled = false;
	    document.all.uremia[2].disabled = false;
	}else{ 
		document.all.uremia[0].checked = false;
		document.all.uremia[1].checked = false;
		document.all.uremia[2].checked = false;
	    document.all.uremia[0].disabled = true;
	    document.all.uremia[1].disabled = true;
	    document.all.uremia[2].disabled = true;
	
	}
}

function habilita_ca(){
	if(document.all.ca_total_selec.checked == true){   
	    document.all.ca_total[0].disabled = false;
	    document.all.ca_total[1].disabled = false;
	    document.all.ca_total[2].disabled = false;
	}else{ 
		document.all.ca_total[0].checked = false;
		document.all.ca_total[1].checked = false;
		document.all.ca_total[2].checked = false;
	    document.all.ca_total[0].disabled = true;
	    document.all.ca_total[1].disabled = true;
	    document.all.ca_total[2].disabled = true;
	
	}
}

function habilita_orina(){
if(document.all.orina_cto_selec.checked == true){   
    document.all.orina_cto[0].disabled = false;
    document.all.orina_cto[1].disabled = false;
    document.all.orina_cto[2].disabled = false;
}else{ 
	document.all.orina_cto[0].checked = false;
	document.all.orina_cto[1].checked = false;
	document.all.orina_cto[2].checked = false;
    document.all.orina_cto[0].disabled = true;
    document.all.orina_cto[1].disabled = true;
    document.all.orina_cto[2].disabled = true;
	}
}   
function habilita_chagas(){
if(document.all.chagas_selec.checked == true){   
    document.all.chagas[0].disabled = false;
    document.all.chagas[1].disabled = false;
    document.all.chagas[2].disabled = false;
}else{ 
	document.all.hemograma[0].checked = false;
	document.all.chagas[1].checked = false;
	document.all.chagas[2].checked = false;
    document.all.chagas[0].disabled = true;
    document.all.chagas[1].disabled = true;
    document.all.chagas[2].disabled = true;
	}
} 



</script>

<form name='form1' action='comprobante_fichero.php' method='POST'>
<input type="hidden" value="<?=$usuario1?>" name="usuario1">
<input type="hidden" name="entidad_alta" value="<?=$entidad_alta?>">
<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="afifechanac" value="<?=$afifechanac?>">
<input type="hidden" name="afiapellido" value="<?=$afiapellido?>">
<input type="hidden" name="afinombre" value="<?=$afinombre?>">
<input type="hidden" name="afidni" value="<?=$afidni?>">
<?/*
   //En el <head> indicamos al objeto xajax se encargue de generar el javascript necesario
   $xajax->printJavascript("xajax/");
   */
echo "<center><b><font size='+2' color='red'>$accion</font></b></center>";
echo "<center><b><font size='+2' color='blue'>$accion1</font></b></center>";

/*******Traemos y mostramos el Log **********/
if ($entidad_alta=='nu'){//carga de prestacion a paciente NO PLAN NACER
$q="SELECT 
	  *
	FROM
      fichero.log_fichero
    LEFT JOIN fichero.fichero using (id_fichero)           
	where fichero.id_beneficiarios=$id
	order by id_log_fichero";
$log=$db->Execute($q) or die ($db->ErrorMsg()."<br>$q");
}

if ($entidad_alta=='na'){//carga de prestacion a paciente PLAN NACER
$q="SELECT 
	  *
	FROM
      fichero.log_fichero
    LEFT JOIN fichero.fichero using (id_fichero)           
	where fichero.id_smiafiliados=$id 
	order by id_log_fichero";
$log=$db->Execute($q) or die ($db->ErrorMsg()."<br>$q");
}

?>
<div align="right">
	<input name="mostrar_ocultar_log" type="checkbox" value="1" onclick="if(!this.checked)
																	  document.all.tabla_logs.style.display='none'
																	 else 
																	  document.all.tabla_logs.style.display='block'
																	  "> Mostrar Logs
</div>	
<!-- tabla de Log de la OC -->
<div style="display:'none';width:98%;overflow:auto;<? if ($log->RowCount() > 3) echo 'height:60;' ?> " id="tabla_logs" >
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor=#cccccc>
<?while (!$log->EOF){?>
	<tr>
	      <td height="20" nowrap>Fecha <?=fecha($log->fields['fecha']). " " .Hora($log->fields['fecha']);?> </td>
	      <td nowrap > Usuario : <?=$log->fields['usuario']; ?> </td>
	      <td nowrap > Tipo : <?=$log->fields['tipo']; ?> </td>
	      <td nowrap > descipcion : <?=$log->fields['descripcion']; ?> </td>	      
	</tr>
	<?$log->MoveNext();
}?>
</table>
</div>
<hr>
<?/*******************  FIN  LOG  ****************************/?>
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <font size=+1><b>Beneficiario</b></font>    
    </td>
 </tr>
 <tr><td>
  <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripción del Beneficiario</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         <tr>
         	<td align="right">
         	  <b>Apellido:
         	</td>         	
            <td align='left'>
              <input type='text' name='afiapellido' value='<?=$afiapellido;?>' size=40 align='right' readonly></b>
             </td>          
             <td align="right">
              <b> Nombre:
         	</td>   
            <td  colspan="2">
             <input type='text' name='afinombre' value='<?=$afinombre;?>' size=40 align='right' readonly></b>
           </td>
          </tr>
          <tr>
           <td align="right">
         	  <b> Documento:
         	</td> 
           <td align='left'>
             <input type='text' name='afidni' value='<?=$afidni;?>' size=40 align='right' readonly></b>
           </td>          
           <td align="right">
         	  <b> Fecha de Nacimiento:
         	</td> 
           <td align='left'>
             <input type='text' name='afifechanac' value='<?=fecha($afifechanac);?>' size=40 align='right' readonly></b>
           </td>
          </tr>
          
          <tr>
           <td align="right" title="Edad a la Fecha actual">
         	 <b> Edad a la Fecha Actual:
           </td> 
           <td align='left'>
			 <?$edad_con_meses=edad_con_meses($afifechanac);
			 $anio_edad=$edad_con_meses["anos"];
			 $meses_edad=$edad_con_meses["meses"];
			 $dias_edad=$edad_con_meses["dias"];
			 ?>
         	 <input type='text' name='edad' value='<?echo $anio_edad." Año/s, ".$meses_edad." Mes/es y ".$dias_edad." dia/s"?>' size=40 align='right' readonly></b>
           </td>
           <td align="right">
         	  <b> Efector Asignado:
         	</td> 
           <td align='left'>
             <input type='text' name='nombreefecto' value='<?=$nombre;?>' size=40 align='right' readonly></b>
           </td>
          </tr>
          
        </table>
      </td>      
     </tr>
   </table>  
   
	 <table class="bordes" align="center" width="90%">
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		Registrar Atencion		 		
		 	</td>
		 </tr>
		 <tr><td class="bordes" align="center"><table>
			 <tr>
				 <td>
					 <tr><td align="center"><table>
						<tr>
						    <td align="right">
						    	<b>Lugar:</b>
						    </td>
						    <td align="left">		          			
					 			 <select name=cuie Style="width=257px" 
					        		onKeypress="buscar_combo(this);"
									onblur="borrar_buffer();"
									onchange="borrar_buffer();" >
									<?$user_login1=substr($_ses_user['login'],0,6);
									  if (es_cuie($_ses_user['login'])){
										$sql1= "select cuie, nombre, com_gestion from nacer.efe_conv where cuie='$user_login1' order by nombre";
									   }									
									  else{
										$usuario1=$_ses_user['id'];
										$sql1= "select nacer.efe_conv.nombre, nacer.efe_conv.cuie, com_gestion 
												from nacer.efe_conv 
												join sistema.usu_efec on (nacer.efe_conv.cuie = sistema.usu_efec.cuie) 
												join sistema.usuarios on (sistema.usu_efec.id_usuario = sistema.usuarios.id_usuario) 
												where sistema.usuarios.id_usuario = '$usuario1'
											 order by nombre";
									   }			 			   
									 $res_efectores=sql($sql1) or fin_pagina();
								 
								 while (!$res_efectores->EOF){ 
									$com_gestion=$res_efectores->fields['com_gestion'];
									$cuie1=$res_efectores->fields['cuie'];
									$nombre_efector=$res_efectores->fields['nombre'];
									if($com_gestion=='FALSO')$color_style='#F78181'; else $color_style='';
									?>
									<option value='<?=$cuie1;?>' Style="background-color: <?=$color_style;?>" <?if ($cuie1==$cuie)echo "selected"?>><?=$cuie1." - ".$nombre_efector?></option>
									<?
									$res_efectores->movenext();
									}?>
								</select>				 			
						    </td>					
						 	<td align="right">
						    	<b>Nombre Medico:</b>
						    </td>
						    <td align="left">
						    	 <input type="text" value="<?if ($_POST['percentilo']=="Sugerir Percentilos")echo $nom_medico;else echo""; ?>" name="nom_medico" Style="width=300px">
						    </td>		    
					    </tr>
					</table></td></tr>
					<tr><td align="center"><table>
						 <tr>
						 	<td align="right">
						    	<b>Fecha de Control:</b>
						    </td>
						    <td align="left">					    	
						    	<?if ($_POST['percentilo']!="Sugerir Percentilos")$fecha_comprobante=date("d/m/Y");?>
						    	 <input type=text id=fecha_comprobante name=fecha_comprobante value='<?=$fecha_comprobante;?>' size=12 readonly>
						    	 <?=link_calendario("fecha_comprobante");?>					    	 
						    </td>					 	
	            			<td align="right">
						    	<b>Fecha proximo Control:</b>
						    </td>
						    <td align="left">					    	
						    	<?if ($_POST['percentilo']!="Sugerir Percentilos")$fecha_pcontrol=date("d/m/Y"); ?>
						    	 <input type=text id=fecha_pcontrol name=fecha_pcontrol value='<?=$fecha_pcontrol;?>' size=12 readonly>
						    	 <?=link_calendario("fecha_pcontrol");?>					    	 
						    </td>	
							<td align="right">
	         	  				<b>Comentario:</b>
	         				</td>         	
	            			<td align='left'>
	              				<textarea cols='40' rows='-2' name='comentario' ><?if ($_POST['percentilo']=="Sugerir Percentilos")echo $comentario;else echo""; ?></textarea>
	            			</td>
						</tr>
					</table></td></tr> 
					<tr><td align="center"class="bordes">
		<!--			<table>
						<tr>
						  	<td>		      
						    	<input type="submit" name="guardar" value="Guardar" title="Guardar" Style="width=250px;height=30px;background:#CEF6CE" onclick="return control_nuevos()">
						   	</td>
						</tr> 
					</table></td></tr>  -->
					
				
         	</td></tr>
		</table></td></tr>	 
	</table>
		
		<table class="bordes" align="center" width="90%">
				 <tr align="center" id="sub_tabla">
				 	<td colspan="2">	
				 		Informacion del Control		 		
				 	</td>
				 </tr>
 
				 <tr><td align="center" class="bordes"><table width="100%" >
						 <? $edad=date("Y-m-d")-$afifechanac; ?>
						 
						 <tr><td align="center"><table width="100%">
						 <tr>
							<td align="Center">
								<font color="Red">En los datos Numericos el separador de Decimales es "."</font>
							</td>
						</tr>
						</table></td></tr>				
						 
						 <tr><td align="center"><table width="100%">					
								 	<tr>
								 		<td align="right">
									    	<b>Peso:</b>
									    </td>
										<td>
											<input type="text" value="<?if ($_POST['percentilo']=="Sugerir Percentilos")echo $peso; else echo "";?>" name="peso" Style="width=50px">en Kg.
										</td>
										<td align="right">
									    	<b>Talla:</b>
									    </td>
										<td>
											<input type="text" value="<?if ($_POST['percentilo']=="Sugerir Percentilos")echo $talla; else echo "";?>" name="talla" Style="width=50px" onchange="calculo_imc()">en Mts.
										</td>
										<td align="right">
									    	<b>IMC:</b>
									    </td>
										<td>
											<input type="text" value="<?if ($_POST['percentilo']=="Sugerir Percentilos")echo $imc; else echo "";?>" name="imc" Style="width=50px" >
										</td>
										
										<?if (GetCountDaysBetweenTwoDates($afifechanac,date("Y-m-d"))>3290){?>
										<td align="right">
											<b> Tensión Arterial	<font color="Red">		MAXIMA:</b></font>
										</td>
										<td>
											<input type="text" value='<?=$tension_arterial_M;?>' name="tension_arterial_M" size=5>
											<b><font color="Red"> / MINIMA</font></b> <input type='text' name='tension_arterial_m' value='<?=$tension_arterial_m;?>' size=5 align='right'></b>
										<td>	<font align='right' color="Red">Tanto para Maxima y Minima los valores son Numeros Enteros</font></td>
										</td>										
										<?}
										
										if (GetCountDaysBetweenTwoDates($afifechanac,date("Y-m-d"))<=365){?>
										<td align="right">
								         	  <b>Perim. Cefalico: </b>
								        </td>         	
								        <td align='left'>
								              <input type="text" size=15 value="<?=$perim_cefalico?>" name="perim_cefalico" >en Cent.
								        </td>
								        <?}?>		
									</tr>
						</table></td></tr>
		
			<tr><td ><table width="85%">	   
			<tr>
					<?if (GetCountDaysBetweenTwoDates($afifechanac,date("Y-m-d"))<=3280){?>
					<td align="right">
						<b>Percentilo Peso/Edad:</b>
					</td>
					<td align="left">			 	
						 <select name=percen_peso_edad Style="width=170px">
						  <option value=-1>Seleccione</option>
						  <option value=1 <?if ($percen_peso_edad=='1') echo "selected"?>> <3 </option>
						  <option value=2 <?if ($percen_peso_edad=='2') echo "selected"?>> 3-10 </option>
						  <option value=3 <?if ($percen_peso_edad=='3') echo "selected"?>> >10-90 </option>
						  <option value=4 <?if ($percen_peso_edad=='4') echo "selected"?>> >90-97 </option>
						  <option value=5 <?if ($percen_peso_edad=='5') echo "selected"?>> >97 </option>
						 </select>
					</td>
					<td align="right">
		         	  	<b>Percentilo Talla/Edad:</b>
		         	</td>         	
		            <td align="left">			 	
						 <select name=percen_talla_edad Style="width=170px">
						  <option value=-1>Seleccione</option>
						  <option value=1 <?if ($percen_talla_edad=='1') echo "selected"?>>-3</option>
						  <option value=2 <?if ($percen_talla_edad=='2') echo "selected"?>>3-97</option>
						  <option value=3 <?if ($percen_talla_edad=='3') echo "selected"?>>+97</option>
						 </select>
					</td>					
					<?}
					
					if (GetCountDaysBetweenTwoDates($afifechanac,date("Y-m-d"))<=365){?>
		         	<td align="right">
						<b>Per. Perim. Cefalico/Edad: </b>
					</td>         	
					<td align="left">			 	
									 <select name=percen_perim_cefali_edad Style="width=170px">
									  <option value=-1>Seleccione</option>
									  <option value=1 <?if ($percen_perim_cefali_edad=='1')echo "selected"?>>-3</option>
									  <option value=2 <?if ($percen_perim_cefali_edad=='2')echo "selected"?>>3-97</option>
									  <option value=3 <?if ($percen_perim_cefali_edad=='3')echo "selected"?>>+97</option>
									 </select>
					</td>	
					<?}?>								
			</tr>
			<tr>
			         	<?if (GetCountDaysBetweenTwoDates($afifechanac,date("Y-m-d"))<=3280){?>
			         	<td align="right">
			         	  <b>Percentilo IMC/Edad: </b>
			         	</td>         	
			            <td align="left">			 	
						 <select name=percen_imc_edad Style="width=170px">
						  <option value=-1>Seleccione</option>
						  <option value=1 <?if ($percen_imc_edad=='1') echo "selected"?>> <3 </option>
						  <option value=2 <?if ($percen_imc_edad=='2') echo "selected"?>> 3-10 </option>
						  <option value=3 <?if ($percen_imc_edad=='3') echo "selected"?>> >10-85 </option>
						  <option value=4 <?if ($percen_imc_edad=='4') echo "selected"?>> >85-97 </option>
						  <option value=5 <?if ($percen_imc_edad=='5') echo "selected"?>> >97 </option>
						  <option value='' <?if ($percen_imc_edad=='') echo "selected"?>>Dato Sin Ingresar</option>			  
						 </select>
						</td>
			         	<td align="right">
			         	  <b>Percentilo Peso/Talla: </b>
			         	</td>         	
			            <td align="left">			 	
						 <select name=percen_peso_talla Style="width=170px">
						  <option value=-1>Seleccione</option>
						  <option value=1 <?if ($percen_peso_talla=='1') echo "selected"?>> <3 </option>
						  <option value=2 <?if ($percen_peso_talla=='2') echo "selected"?>> 3-10 </option>
						  <option value=3 <?if ($percen_peso_talla=='3') echo "selected"?>> >10-85 </option>
						  <option value=4 <?if ($percen_peso_talla=='4') echo "selected"?>> >85-97 </option>
						  <option value=5 <?if ($percen_peso_talla=='5') echo "selected"?>> >97 </option>
						  <option value='' <?if ($percen_peso_talla=='') echo "selected"?>>Dato Sin Ingresar</option>
						 </select>
						</td>
						<?}?>
			</tr>
			<? if($edad < 7){ ?>
				<tr align="center">		                                
					<td align="center"  colspan="6"><input type="submit" name="percentilo" value="Sugerir Percentilos" title="Visualizar Percentilos" Style="width=150px;height=27px;" ></td>	
				</tr>
			<?} ?>
			</table></td></tr>

						<tr><td align="center"><table width="100%">					
								 	<tr><!--
								 		<td align="right">
									    	<b>Diabetico:</b>
									    </td>
										<td>
											<input type="radio" name="diabetico" value="NO" checked>NO
											<input type="radio" name="diabetico" value="SI">SI
										</td>
										<td align="right">
									    	<b>Hipertenso:</b>
									    </td>
										<td>
											<input type="radio" name="hipertenso" value="NO" checked>NO
											<input type="radio" name="hipertenso" value="SI">SI
										</td>-->
										<td align="right">
									    	<b>Público:</b>
									    </td>
										<td>
											<input type="radio" name="publico" value="NO">NO
											<input type="radio" name="publico" value="SI" checked>SI
										</td>										
									</tr>
						</table></td></tr>
						
	</table></td></tr>
	
	<?If($sexo='F' and $edad >=11 ){?>	

		<tr><td><table class="bordes" align="center" width="90%">
			<tr align="left" >	    	
					<td >					    	
						<b>Bajo programa Salud Sexual y Reproductivo:</b> <input type="checkbox" name="salud_rep" value="SI" onclick="muestra_tabla(document.all.prueba_vida3,2);">	 
					</td>
					
					
					<td colspan=8><table id="prueba_vida3"  width="100%" style="display:none;">	 	
						<td >	
					 		<b>Metodo Anticonceptivo:</b>	 		
					 	   <select name=metodo_anti Style="width=257px">
									 <option value=-1>Seleccione </option>
										  <option value="ACO-Orales" >ACO-Orales</option>
										  <option value="ACI-Inyectables" >ACI-Inyectables</option> 
										  <option value="ACOLAC-Orales de Lac" >ACOLAC-Orales de Lac</option> 
										  <option value="AHE-Orales de Emerg" >AHE-Orales de Emerg</option> 
										  <option value="DIU" >DIU</option> 
										  <option value="PRESERVATIVO" >Preservativo</option> 
										  <option value="PRESERVATIVO" >Naturales u Otros</option> 
									</select>
						</td>
					</table></td>
				 </tr>
	
		</table></td></tr>

		<tr><td><table class="bordes" align="center" width="90%">
				 <tr align="center" id="sub_tabla">
				 	<td colspan="2">	
				 		Informacion Adicional de Embarazo	 		
				 	</td>
				 </tr>
				 <? if ($entidad_alta=='na'){//carga de prestacion a paciente PLAN NACER
					  $q_emb= "SELECT DISTINCT id_fichero,id_smiafiliados, 
										fpp, fum, f_diagnostico
										FROM fichero.fichero
										where fichero.fichero.id_smiafiliados=$id and (fichero.fichero.fpp >= CURRENT_DATE)  
										ORDER BY id_fichero DESC";
					    $res_emb=sql($q_emb, "Error al verificar embarazo existente") or fin_pagina();
					    if($res_emb->RecordCount!=EOF){
						    $fpp=$res_emb->fields["fpp"];
							$fum=$res_emb->fields["fum"];
							$f_diagnostico=$res_emb->fields["f_diagnostico"];
						}
				 }else{//PARA EL 'nu'
				 
					    $q_emb= "SELECT DISTINCT id_fichero,id_smiafiliados, 
										fpp, fum, f_diagnostico
					    				FROM fichero.fichero
										where fichero.fichero.id_beneficiarios=$id and (fichero.fichero.fpp >= CURRENT_DATE)  
										ORDER BY id_fichero DESC";
					    $res_emb=sql($q_emb, "Error al verificar embarazo existente") or fin_pagina();
					    if($res_emb->RecordCount!=EOF){
						    $fpp=$res_emb->fields["fpp"];
							$fum=$res_emb->fields["fum"];
							$f_diagnostico=$res_emb->fields["f_diagnostico"];
						}
		
				 }
				?>
				 
				<tr><td class="bordes" align="center"><table width="100%" >	
								<td>	
							 		&nbsp		 		
							 	</td>
							  	<td >					    	
									<input type="checkbox" name="embarazo" value="embarazo" onclick="muestra_tabla(document.all.prueba_vida2,2);"><b>Embarazo</b> 	 
							    </td>
								<tr><td colspan=8><table id="prueba_vida2" border="1" width="100%" style="display:none;border:thin groove">
								 	<td>	
								 		<b>Fecha Diagnostico:</b>		 		
								 	</td>
								  	<td >					    	
								    	 <input type=text id=f_diagnostico name=f_diagnostico value='<?=fecha($f_diagnostico);?>' size=15 readonly>
								    	 <?=link_calendario("f_diagnostico");?>					    	 
								    </td>
								    <td >	
								 		<b>FUM:</b>		 		
								 	</td>
								  	<td >					    	
								    	 <input type=text id=fum name=fum value='<?=fecha($fum);?>' size=15 onblur="if (document.all.fum.value!='') calculo_fpp_sem()" readonly>
								    	 <?=link_calendario("fum");?>										 
								    </td>
								    <td >	
								 		<b>FPP:</b>		 		
								 	</td>
								  	<td >					    	
								    	<!--<input type=text id=fpp name=fpp value='' size=15 readonly> -->
										 <input type=text id=fpp name=fpp value='<?=fecha($fpp);?>' size=15 onfocus="if (document.all.fum.value!='') calculo_fpp_sem()" readonly>
								    	 <?=link_calendario("fpp");?>					    	 
								    </td>
								    <td >	
								 		<b>Semana de Gestacion:</b>		 		
								 	</td>
								  	<td >					    	
								    	 <input type=text id=semana_gestacional name=semana_gestacional value='' size=5 >			    	 
								    </td>
								</table></td></tr>
					</table></td></tr>					
		</table></td></tr>
		<?}?>
			
	<tr><td><table width="90%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.prueba_vida1,2);" >
	  </td>
	  <td align="center">
	   <b>Datos Adicionales</b>
	  </td>
	</tr>
	</table></td></tr>
	<tr><td><table id="prueba_vida1" border="1" width="100%" style="display:none;border:thin groove">
					
			<tr><td align="center"><table>	   
			<tr>
							<td align="right">
								 <b>Carnet de Vacunacion:</b>
							</td>
							<td>
								Completo <input type=radio name='c_vacuna' value='completo'<? if ($id_planilla) echo "readonly"?> checked >
								Incompleto <input type=radio name='c_vacuna' value='incompleto'<? if ($id_planilla) echo "readonly"?>>
							</td>
							<td align="right">
								<b>Agudeza Visual:</b>
							</td>
							<td>
								<input type="text" value="" name="ag_visual" Style="width=50px">
							</td>							
							<td align="right">
							   	<b>TUNNER:</b>
							</td>
							<td>
								<input type="text" value="" name="tunner" Style="width=50px">
							</td>
			</tr>
			</table></td></tr>
		
	<? if($edad <=1 ){?>
		<tr><td align="center"><table>	      
					
						 	<tr>	
								<td align="left">
							    	<b>Tasa Materna:</b>
							    </td>
								<td align="left">			 	
									 <select name=tasa_materna Style="width=100px" >
									  <option value=-1 selected>Seleccione</option>
										  <option value=SI>SI</option>
										  <option value=NO>NO</option>			  		  
									 </select>
								</td>							
							</tr>
		</table></td></tr>
	<?}?>
		
		<tr><td ><table>
						 	<tr>
								<td align="left">
							    	<b>Examen Clinico General:</b>
							    </td>
								<td>
									<textarea cols='40' rows='-2' value="" name='ex_clinico_gral'></textarea>
								</td>								
							
								<td align="left">
							    	<b>Examen Traumatologico:</b>
							    </td>
								<td >
									<textarea cols='40' rows='-2' value="" name='ex_trauma'></textarea>
								</td>								
							</tr>
							<tr>								
								<td align="left">
							    	<b>Examen Cardiologico:</b>
							    </td>
								<td >
									<textarea cols='40' rows='-2' value="" name='ex_cardio'></textarea>
								</td>
						
						
						 								
								<td align="right">
							    	<b>Examen Odontologico:</b>
							    </td>
								<td align="left">
									<textarea cols='40' rows='-2' value="" name='ex_odontologico'></textarea>
								</td>
							</tr>
		</table></td></tr>	
					
					<? 
					// --------------------------------------------- mayores de 12 ----------------------------------------
		if($edad >=12 ){?>
					<tr><td align="left"><table><tr>								
								<td align="left">
											 <b>ECG:</b>
									</td>
									<td>
											<textarea cols='40' rows='-2' value="" name='ex_ecg'></textarea>
									</td>
														
									<td align="left">
									<b>Observacion ECG:</b>  
								</td>
								<td >	
									<textarea cols='40' rows='-2' value="" name='obs_ecg'></textarea>
								</td>
					
					</tr></table></td></tr>
					
					<tr><td><table align="center" width="50%" border="1">
												<b>Laboratorio:</b>
													<tr><td>		
															    <tr>
																	<td>
																		<input type="checkbox" name="hemograma_selec" onClick="habilita_hemg();"> Hemograma 
																		<td>	
																			<input type="radio" name="hemograma" value="N" disabled> N
																			</td>
																		<td><input type="radio" name="hemograma" value="A" disabled> A
																		</td>
																		<td><input type="radio" name="hemograma" value="No Realizado" disabled> No Realizado
																		</td>
																	</td>
																</tr>
																<tr>
																	<td>
																		<input type="checkbox" name="vsg_selec" onClick="habilita_vsg();"> VSG 
																		<td>	
																			<input type="radio" name="vsg" value="N" disabled> N
																			</td>
																		<td><input type="radio" name="vsg" value="A" disabled> A
																		</td>
																		<td><input type="radio" name="vsg" value="No Realizado" disabled> No Realizado
																		</td>
																	</td>
																</tr>
																<tr>
																	<td>
																		<input type="checkbox" name="glucemia_selec" onClick="habilita_glucemia();"> Glucemia 
																		<td>	
																			<input type="radio" name="glucemia" value="N"  disabled> N
																		</td>
																		<td>	
																			<input type="radio" name="glucemia" value="A" disabled> A
																		</td>
																		<td>
																			<input type="radio" name="glucemia" value="No Realizado" disabled> No Realizado
																		</td>
																		
																	</td>
																</tr>
																<tr>
																	<td>
																		<input type="checkbox" name="uremia_selec" onClick="habilita_uremia();"> Uremia 
																		<td>	
																			<input type="radio" name="uremia" value="N" disabled> N 
																			</td>
																		<td><input type="radio" name="uremia" value="A" disabled> A
																		</td>
																		<td><input type="radio" name="uremia" value="No Realizado" disabled> No Realizado
																		</td>
																	</td>
																</tr>
																<tr>
																	<td>
																		<input type="checkbox" name="ca_total_selec" onClick="habilita_ca();"> Col. Total 
																		<td>	
																			<input type="radio" name="ca_total" value="N" disabled> N
																			</td>
																		<td> <input type="radio" name="ca_total" value="A" disabled> A 
																		</td>
																		<td><input type="radio" name="ca_total" value="No Realizado" disabled> No Realizado
																		</td>
																	</td>
																</tr>
																<tr>
																	<td>
																		<input type="checkbox" name="orina_cto_selec" onClick="habilita_orina();"> Orina 
																		<td>	
																			<input type="radio" name="orina_cto" value="N" disabled> N
																			</td>
																		<td><input type="radio" name="orina_cto" value="A" disabled> A 
																		</td>
																		<td><input type="radio" name="orina_cto" value="No Realizado" disabled> No Realizado
																		</td>
																	</td>
																</tr>
																<tr>
																	<td>
																		<input type="checkbox" name="chagas_selec" onClick="habilita_chagas();"> Chagas 
																		<td>	
																			<input type="radio" name="chagas" value="N" disabled> N
																			</td>
																		<td><input type="radio" name="chagas" value="A" disabled> A
																		</td>
																		<td><input type="radio" name="chagas" value="No Realizado" disabled> No Realizado
																		</td>
																	</td>
																</tr>
													</td></tr>
														
							</table></td></tr>
							
							<tr><td align="left"><table>
									<tr>
										<td>
											<b>Observacion Laboratorio:</b>  
										</td>
										<td>	
											<textarea cols='40' rows='-2' value="" name='obs_laboratorio'></textarea>
										</td>
							</tr></table></tr>
							
						<tr><td ><table width="95%">
									<tr>
										<td align="right">
											<b>RX de Torax:</b>  
										</td>
										<td align="left">
											<input type="checkbox" value='SI' name="rx_torax"> 
										<td>
										<td align="right">
											<b>RX de Columna Vertebral:</b>  
										</td>
										<td align="left">
											<input type="checkbox" value='SI' name="rx_col_vertebral">  
										<td>	
										<td align="right">
											<b>Observaciones:</b>  
										</td>
										<td align="left">	
											<textarea cols='40' rows='-2' value="" name='rx_observaciones'></textarea>
										</td>
									</tr>
						</table></td></tr>	
						
						<tr><td align="left"><table width="80%">
									<tr>
										<td align="right">
											<b>Otros:</b>  
										</td>
										<td align="left">	
											<textarea cols='40' rows='-2' value="" name='otros'></textarea>
										</td>
										<td align="right">
											<b>Observaciones:</b>  
										</td>
										<td align="left">	
											<textarea cols='40' rows='-2' value="" name='otros_obs'></textarea>
										</td>
									</tr>
						</table></td></tr>
									
				<?} // ---------------------------------------------Mayor de 16 años---------------------------------------- 
				if ($edad >=16){?>					
						<tr><td align="left"><table width="80%">
								<tr>	
									<td align="right">
								    	<b>Ergometria:</b>
								    </td>
									<td align="left">
										<textarea cols='40' rows='-2' value="" name='ergometria'></textarea>
									</td>								
									
									<td align="right">
								    	<b>Observaciones:</b>
								    </td>
									<td align="left">
										<textarea cols='40' rows='-2' value="" name='obs_adolesc'></textarea>
									</td>								
								</tr>
							</table></td></tr>
				<?}?>	
							
			<?if($edad > 6 ){?>
			<tr><td><table class="bordes" align="center" width="80%">
					 <tr align="center" id="sub_tabla">
					 	<td colspan="2">	
					 		Conclusion de Ficha Medica Intercolegial 		 		
					 	</td>
					 </tr>
					<tr><td align="center" colspan="2">
								<B><font size="+1">
								<input type="radio" name="conclusion" value="Apto" checked>Apto
								&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="conclusion" value="Parcial">Apto Parcial
								&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="conclusion" value="No Apto">No Apto
								</font></B>
					</td></tr>
			</table></td></tr>	
			<?}?>
			
	</table></td></tr>	
		 
<?//tabla de comprobantes
if ($entidad_alta=='nu'){//carga de prestacion a paciente NO PLAN NACER
	$query="SELECT nacer.efe_conv.nombre,
				fichero.fichero.nom_medico,
				fichero.fichero.fecha_control,
				fichero.fichero.periodo, *
			FROM
	fichero.fichero
	INNER JOIN nacer.efe_conv ON fichero.fichero.cuie = nacer.efe_conv.cuie
	where id_beneficiarios='$id' AND (anular='' or anular IS NULL)
	order by fichero.id_fichero DESC";
}elseif ($entidad_alta=='na'){//carga de prestacion a paciente PLAN NACER
			$query="SELECT nacer.efe_conv.nombre,
			nacer.efe_conv.cuie,
				fichero.fichero.nom_medico,
				fichero.fichero.fecha_control,
				fichero.fichero.periodo,*
			FROM
			fichero.fichero
			INNER JOIN nacer.efe_conv ON fichero.fichero.cuie = nacer.efe_conv.cuie
			where id_smiafiliados='$id' and (anular='' or anular IS NULL)
			order by fichero.id_fichero DESC";
		}
$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();?>

<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
	  </td>
	  <td align="center">
	   <b>Prestaciones</b>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida" border="1" width="100%" style="display:none;border:thin groove">
	<?
	
	if ($res_comprobante->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen Prestaciones</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">	
	 	    <td width=1%>&nbsp;</td>
	 		<td width="30%">Efector</td>
	 		<td width="30%">Medico</td>
	 		<td width="30%">Comentario</td>
	 		<td width="10%">Fecha Prestación</td>	 		
	 		<td width="10%">Periodo</td>
	 		<td width="10%">Fecha Proximo Control</td>	
	 		<td width="10%">Asistio Proximo Control</td>
	 		<td >Anular</td>	 	
	 	</tr>
	 	<?//1=ultimo control cargado 0= controles anterioeres
	 	$res_comprobante->movefirst();
	 	while (!$res_comprobante->EOF) {
	 		
	 		$ref =encode_link("fichero_muestra.php",array("id_fichero"=>$res_comprobante->fields['id_fichero'],"id"=>$id,"entidad_alta"=>$entidad_alta,"pagina_viene"=>"fichero_muestra.php"));
           
            $id_tabla="tabla_".$res_comprobante->fields['id_fichero'];	
	 		$onclick_check=" javascript:(this.checked)?Mostrar('$id_tabla'):Ocultar('$id_tabla')";?>
	 		<tr <?=atrib_tr()?>>
	 			<td>
	              <input type=checkbox name=check_prestacion value="" onclick="<?=$onclick_check?>" class="estilos_check">
	            </td>	
		 		<td align="center" onclick="window.open('<?=$ref?>','','toolbar=0,location=0,directories=0,status=0, menubar=0,scrollbars=1');"><?=$res_comprobante->fields['cuie'].' - '.$res_comprobante->fields['nombre']?></td>
		 		<td align="center" onclick="window.open('<?=$ref?>','','toolbar=0,location=0,directories=0,status=0, menubar=0,scrollbars=1');"><?if ($res_comprobante->fields['nom_medico']!="") echo $res_comprobante->fields['nom_medico']; else echo "&nbsp"?></td>
		 		<td align="center" onclick="window.open('<?=$ref?>','','toolbar=0,location=0,directories=0,status=0, menubar=0,scrollbars=1');"><?if ($res_comprobante->fields['comentario']!="") echo $res_comprobante->fields['comentario']; else echo "&nbsp"?></td>
		 		<td align="center" onclick="window.open('<?=$ref?>','','toolbar=0,location=0,directories=0,status=0, menubar=0,scrollbars=1');"><?=fecha($res_comprobante->fields['fecha_control'])?></td>		 		
		 		<td align="center" onclick="window.open('<?=$ref?>','','toolbar=0,location=0,directories=0,status=0, menubar=0,scrollbars=1');"><?=$res_comprobante->fields['periodo']?></td>	
		 		<td align="center" onclick="window.open('<?=$ref?>','','toolbar=0,location=0,directories=0,status=0, menubar=0,scrollbars=1');"><?=fecha($res_comprobante->fields['fecha_pcontrol'])?></td>		 
		 		<td align="center" onclick="<?//=$onclick_elegir?>"><?if ($res_comprobante->fields['fecha_pcontrol_flag']!="0") echo "NO"; else echo "SI"?></td>			 		
		 		<?$ref1 = encode_link("comprobante_fichero.php",array("id_fichero"=>$res_comprobante->fields['id_fichero'], "anular"=>"anular", "entidad_alta"=>$entidad_alta,"id"=>$id ));
            		$onclick_anular="if (confirm('Esta Seguro que Desea ANULAR Comprobante $id_comprobante_aux?')) location.href='$ref1'
            						else return false;	";
					$onclick_no_anular="alert('Ud. no tiene permiso para anular el comprobante')";?>
		 		<?if ($res_comprobante->fields['cuie']==$_ses_user['login']
					  or $_ses_user['login']=='miguel'
					  or $_ses_user['login']=='mario'
					  or $_ses_user['login']=='glhuiller'
					  or $_ses_user['login']=='gmonica'
					  or $_ses_user['login']=='dquevedo') {?>
				<td align="center" onclick="<?=$onclick_anular?>"><img src='../../imagenes/sin_desc.gif' style='cursor:pointer;'></td>	
		 		<?} else {?>
				<td align="center" onclick="<?=$onclick_no_anular?>"><img src='../../imagenes/candado1.gif' style='cursor:pointer;'></td>
				<?}?>
				</tr>	
		 	<tr>
	          <td colspan=9>					  
	                  <div id=<?=$id_tabla?> style='display:none'>
	                  <table width=100% align=center class=bordes>
	                  			    <tr id=ma>		                               
		                               <td>Peso</td>
		                               <td>Talla</td>
		                               <td>IMC</td>
		                               <td>TA</td>
		                               <td>Perc. Peso/edad</td>	  
		                               <td>Perc. talla/edad</td>
		                               <td>Perc. IMC/edad</td>	  
		                               <td>Perc. Peso/Talla</td>	   
		                               <? if($edad <=1 ){?> 
		                               <td>Perimet.Cefarico</td>	  
		                               <td>Perc.Perimet.Cefarico/edad</td>	   
		                               <? }?>                         
		                            </tr>
		                         <tr>
			                            <td align="center" class="bordes"><?if ($res_comprobante->fields['peso']=="") echo "&nbsp"; else echo $res_comprobante->fields["peso"]?></td>			                                 
			                            <td align="center" class="bordes"><?if ($res_comprobante->fields['talla']=="") echo "&nbsp"; else echo $res_comprobante->fields["talla"]?></td>
			                            <td align="center" class="bordes"><?if ($res_comprobante->fields['imc']=="") echo "&nbsp"; else echo$res_comprobante->fields["imc"]?></td>
			                            <td align="center" class="bordes"><?if ($res_comprobante->fields['ta']=="") echo "&nbsp";else echo  $res_comprobante->fields["ta"]?></td>
			                            <td align="center" class="bordes"><?if($res_comprobante->fields['percen_peso_edad']=="1")echo "<3"; elseif ($res_comprobante->fields['percen_peso_edad']=="2")echo "3-10";  elseif ($res_comprobante->fields['percen_peso_edad']=="3")echo ">10-90 ";  elseif ($res_comprobante->fields['percen_peso_edad']=="4")echo ">90-97 ";  elseif ($res_comprobante->fields['percen_peso_edad']=="5")echo ">97";else echo"Dato Sin Ingresar";?></td>
			                    		<td align="center" class="bordes"><?if ($res_comprobante->fields['percen_talla_edad']=='1') echo "-3"; elseif ($res_comprobante->fields['percen_talla_edad']=='2') echo "3-97"; elseif ($res_comprobante->fields['percen_talla_edad']=='3') echo "+97";  else echo "Dato Sin Ingresar";?></td>	
			                    		 <td align="center" class="bordes"><?if ($res_comprobante->fields['percen_imc_edad']=='1') echo "<3"; elseif ($res_comprobante->fields['percen_imc_edad']=='2') echo "3-10"; elseif ($res_comprobante->fields['percen_imc_edad']=='3') echo " >10-85"; elseif ($res_comprobante->fields['percen_imc_edad']=='4') echo ">85-97";elseif ($res_comprobante->fields['percen_imc_edad']=='5') echo " >97"; else echo "Dato Sin Ingresar";?></td>
			                    		<td align="center" class="bordes"><?if ($res_comprobante->fields['percen_peso_talla']=='1') echo "<3"; elseif ($res_comprobante->fields['percen_peso_talla']=='2') echo "3-10"; elseif ($res_comprobante->fields['percen_peso_talla']=='3') echo ">10-85"; elseif ($res_comprobante->fields['percen_peso_talla']=='4') echo ">85-97"; elseif ($res_comprobante->fields['percen_peso_talla']=='5') echo " >97"; else  echo "Dato Sin Ingresar"?></td>			                                 
			                           <? if($edad <=1 ){?> 
		                               	<td align="center" class="bordes"><?if ($res_comprobante->fields['perim_cefalico']=="") echo "&nbsp"; echo number_format($res_comprobante->fields["perim_cefalico"],2,',',0)?></td>
			                    		<td align="center" class="bordes"><?if ($res_comprobante->fields['percen_perim_cefali_edad']=='1') echo "-3"; elseif ($res_comprobante->fields['percen_perim_cefali_edad']=='2') echo "3-97"; elseif ($res_comprobante->fields['percen_perim_cefali_edad']=='3') echo "+97"; else echo "Dato Sin Ingresar";?></td>		   
		                               <? }?>         
			                          
			                      </tr>                            	                            
	               </table>
	               </div>
	
	         </td>
	      </tr>  	
	 		<?$res_comprobante->movenext();
	 	}
	 }?>
</table></td></tr>
					<tr><td align="center"class="bordes">
					<table>
						<tr>
						  	<td>		      
						    	<input type="submit" name="guardar" value="Guardar" title="Guardar" Style="width=250px;height=30px;background:#CEF6CE" onclick="return control_nuevos()">
						   	</td>
						</tr> 
					</table></td></tr> 
					
				
         	</td></tr> 
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
    <td> 	
   	 	<input type=button name="volver" value="Volver" onclick="document.location='../entrega_leche/listado_beneficiarios_leche.php'"title="Volver al Listado" style="width=150px">
    </td>
  </tr>
 </table></td></tr>
 
</td></tr></table>
</table>

</form>
<?=fin_pagina();// aca termino ?>
