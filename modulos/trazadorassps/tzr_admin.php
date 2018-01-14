<?
/*
Author: seba

modificada por
$Author: seba $
$Revision: 1.42 $
$Date: 2013/02/01 16:49:00 $
update segun nuevas modificaciones sobre el reporte de trazadoras Octubre/2013
*/

require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();


$fecha_hoy=date("Y-m-d");

function bisiesto_local($anio_actual){ 
    $bisiesto=false; 
    //probamos si el mes de febrero del año actual tiene 29 días 
      if (checkdate(2,29,$anio_actual)) 
      { 
        $bisiesto=true; 
    } 
    return $bisiesto; 
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

if ($_POST['guardar']=="Guardar Planilla Trazadora 1"){
   
   $fecha_carga=date("Y-m-d");
   $usuario=$_ses_user['name'];
   $db->StartTrans();   
   $fecha_ctrl_prenatal=Fecha_db($_POST['fecha_control_prenatal']);
   $fum=Fecha_db($_POST['fum']);
   $fpp=Fecha_db($_POST['fpp']);   
   $edad_gestacional=$_POST['edad_gestacional_trz1']; 
   $comentario_trz1=$_POST['observaciones_trz1'];    
    
   $q="select nextval('trazadorassps.seq_id_trz1') as id_planilla";
   $id_planilla=sql($q) or fin_pagina();
   $id_planilla=$id_planilla->fields['id_planilla'];
   
   if ($entidad_alta=='na'){ //smiafiliados
    $id_beneficarios=0;
   	$query="insert into trazadorassps.trazadora_1	
             (id_trz1,cuie,id_smiafiliados,fecha_control_prenatal,fum,fpp,edad_gestacional
             ,fecha_carga,usuario,comentario,id_beneficiarios)
             values
             ('$id_planilla','$cuie','$id','$fecha_ctrl_prenatal','$fum','$fpp',
             '$edad_gestacional','$fecha_carga','$usuario','$comentario_trz1',$id_beneficarios)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
    }
if ($entidad_alta=='nu'){ //leche.beneficarios
    $id_smiafiliados=0;
	$query="insert into trazadorassps.trazadora_1	
             (id_trz1,cuie,id_beneficiarios,fecha_control_prenatal,fum,fpp,edad_gestacional
             ,fecha_carga,usuario,comentario,id_smiafiliados)
             values
             ('$id_planilla','$cuie','$id','$fecha_ctrl_prenatal','$fum','$fpp',
             '$edad_gestacional','$fecha_carga','$usuario','$comentario_trz1',$id_smiafiliados)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
	}
    $accion="Se guardo la Planilla";
    $mensaje="ATENCIÓN TEMPRANA DE EMBARAZO";    
	 
    $db->CompleteTrans();
   
    if ($pagina=="tzr_admin.php") echo "<script>window.close()</script>";  
           
}//de if ($_POST['guardar']=="Guardar Planilla Trazadora 1")

if ($_POST['guardar']=="Guardar Planilla Trazadora 2"){
   
   $fecha_carga=date("Y-m-d");
   $usuario=$_ses_user['name'];
   $db->StartTrans();   
   $fecha_control=Fecha_db($_POST['fecha_control']); 
   $edad_gestacional=$_POST['edad_gestacional']; 
   $comentario_trz2=$_POST['observaciones_trz2']; 
   $peso=$_POST['peso'];
   
   $tension_arterial_M=$_POST['tension_arterial_M'];
   $tension_arterial_m=$_POST['tension_arterial_m'];
   $maxima=str_pad($tension_arterial_M,3,"0",STR_PAD_LEFT);
   $minima=str_pad($tension_arterial_m,3,"0",STR_PAD_LEFT);
   $tension_arterial="$maxima"."/"."$minima";
      
    
   $q="select nextval('trazadorassps.seq_id_trz2') as id_planilla";
   $id_planilla=sql($q) or fin_pagina();
   $id_planilla=$id_planilla->fields['id_planilla'];
 
   if ($entidad_alta=='na'){ //smiafiliados
       $id_beneficarios=0;
   	   $query="insert into trazadorassps.trazadora_2	
             (id_trz2,cuie,id_smiafiliados,fecha_control,edad_gestacional
             ,peso,tension_arterial,fecha_carga,usuario,comentario,id_beneficiarios)
             values
             ('$id_planilla','$cuie','$id','$fecha_control',
             '$edad_gestacional','$peso','$tension_arterial','$fecha_carga','$usuario','$comentario_trz2',$id_beneficarios)";

   	 sql($query, "Error al insertar la Planilla") or fin_pagina();
     }
	if ($entidad_alta=='nu'){ //leche.beneficarios
    	$id_smiafiliados=0;
		$query="insert into trazadorassps.trazadora_2	
             (id_trz2,cuie,id_beneficiarios,fecha_control,edad_gestacional
             ,peso,tension_arterial,fecha_carga,usuario,comentario,id_smiafiliados)
             values
             ('$id_planilla','$cuie','$id','$fecha_control',
             '$edad_gestacional','$peso','$tension_arterial','$fecha_carga','$usuario','$comentario_trz2',$id_smiafiliados)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
	}
    $accion="Se guardo la Planilla";    
	$mensaje="SEGUIMIENTO DE EMBARAZO"; 
    $db->CompleteTrans();
   
     
   if ($pagina=="tzr_admin.php") echo "<script>window.close()</script>";  
           
}//de if ($_POST['guardar']=="Guardar Planilla Trazadora 2")

if ($_POST['guardar']=="Guardar Planilla Trazadora 3"){
   
   $fecha_carga=date("Y-m-d");
   $usuario=$_ses_user['name'];
   $db->StartTrans();   
   $fecha_nac_trz3=Fecha_db($_POST['fecha_nac']);
   $peso_nac_trz3=$_POST['peso_nac_trz3'];
   $sobrevida_trz3=($_POST['sobrevida_trz3'])? 'S':'N';
   $comentario_trz3=$_POST['observaciones_trz3']; 
       
    
   $q="select nextval('trazadorassps.seq_id_trz3') as id_planilla";
   $id_planilla=sql($q) or fin_pagina();
   $id_planilla=$id_planilla->fields['id_planilla'];
   
    //codigo de insercion  
 	if ($entidad_alta=='na'){ //smiafiliados
    	$id_beneficarios=0;
   		$query="insert into trazadorassps.trazadora_3	
             (id_trz3,cuie,id_smiafiliados,fecha_nac,peso_nac,sobrevida
             ,fecha_carga,usuario,comentario,id_beneficiarios)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz3',
             '$peso_nac_trz3','$sobrevida_trz3','$fecha_carga','$usuario','$comentario_trz3',$id_beneficarios)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
     }
if ($entidad_alta=='nu'){ //leche.beneficarios
    $id_smiafiliados=0;
	$query="insert into trazadorassps.trazadora_3	
             (id_trz3,cuie,id_beneficiarios,fecha_nac,peso_nac,sobrevida
             ,fecha_carga,usuario,comentario,id_smiafiliados)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz3',
             '$peso_nac_trz3','$sobrevida_trz3','$fecha_carga','$usuario','$comentario_trz3',$id_smiafiliados)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
	}
    $accion="Se guardo la Planilla";    
	$mensaje="TRAZADORA III: EFECTIVIDAD DEL CUIDADO NEONATAL"; 
    $db->CompleteTrans();
   
   //fin de codigo de insercion
   if ($pagina=="tzr_admin.php") echo "<script>window.close()</script>";  
           
}//de if ($_POST['guardar']=="Guardar Planilla Trazadora 3")


if ($_POST['guardar']=="Guardar Planilla Trazadora 4"){
   
   $fecha_carga=date("Y-m-d");
   $usuario=$_ses_user['name'];
   $db->StartTrans();   
   $fecha_ctrl_trz4=Fecha_db($_POST['fecha_control_trz4']); 
   $peso_trz4=$_POST['peso_trz4'];
   $talla_trz4=$_POST ['talla_trz4'];
   $perimetro_cefalico_trz4=$_POST['perimetro_cefalico_trz4'];
   $percentilo_peso_edad_trz4=$_POST['percentilo_peso_edad_trz4'];
   $percentilo_talla_edad_trz4=$_POST['percentilo_talla_edad_trz4'];
   $percentilo_perim_cefalico_edad_trz4=$_POST['percentilo_per_cefalico_edad_trz4'];
   $percentilo_peso_talla_trz4=$_POST['percentilo_peso_talla_trz4'];
   $comentario_trz4=$_POST['observaciones_trz4'];
   $fecha_nac_trz4=Fecha_db($_POST ['fecha_nac']);     
    
   $q="select nextval('trazadorassps.seq_id_trz4') as id_planilla";
   $id_planilla=sql($q) or fin_pagina();
   $id_planilla=$id_planilla->fields['id_planilla'];
   
    //codigo de insercion  
 if ($entidad_alta=='na'){ //smiafiliados
    $id_beneficarios=0;
   	$query="insert into trazadorassps.trazadora_4	
             (id_trz4,cuie,id_smiafiliados,fecha_nac,
  				fecha_control ,
  				peso ,
  				talla,
      			perimetro_cefalico,
      			percentilo_peso_edad,
  				percentilo_talla_edad,
  				percentilo_perim_cefalico_edad,
  				percentilo_peso_talla,
  				fecha_carga,
  				usuario,
  				comentario,id_beneficiarios)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz4','$fecha_ctrl_trz4',
             '$peso_trz4','$talla_trz4','$perimetro_cefalico_trz4',
             '$percentilo_peso_edad_trz4',
             '$percentilo_talla_edad_trz4','$percentilo_perim_cefalico_edad_trz4',
             '$percentilo_peso_talla_trz4','$fecha_carga','$usuario','$comentario_trz4',$id_beneficarios)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
    }
if ($entidad_alta=='nu'){ //leche.beneficarios
    $id_smiafiliados=0;
	$query="insert into trazadorassps.trazadora_4	
             (id_trz4,cuie,id_beneficiarios,fecha_nac,
  				fecha_control ,
  				peso ,
  				talla,
      			perimetro_cefalico,
      			percentilo_peso_edad,
  				percentilo_talla_edad,
  				percentilo_perim_cefalico_edad,
  				percentilo_peso_talla,
  				fecha_carga,
  				usuario,
  				comentario,id_smiafiliados)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz4','$fecha_ctrl_trz4',
             '$peso_trz4','$talla_trz4','$perimetro_cefalico_trz4',
             '$percentilo_peso_edad_trz4',
             '$percentilo_talla_edad_trz4','$percentilo_perim_cefalico_edad_trz4',
             '$percentilo_peso_talla_trz4','$fecha_carga','$usuario','$comentario_trz4',$id_smiafiliados)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
	}
    $accion="Se guardo la Planilla";    
	$mensaje="TRAZADORA IV: SEGUIMIENTO DE SALUD DEL NIÑO MENOR DE 1 AÑO";  
    $db->CompleteTrans();
  
   if ($pagina=="tzr_admin.php") echo "<script>window.close()</script>";  
           
}//de if ($_POST['guardar']=="Guardar Planilla Trazadora 4")

if ($_POST['guardar']=="Guardar Planilla Trazadora 6"){
   
   $fecha_carga=date("Y-m-d");
   $usuario=$_ses_user['name'];
   $db->StartTrans();   
   $comentario_trz6=$_POST['observaciones_trz6'];  
   $fecha_diagnostico_trz6=Fecha_db($_POST['fecha_diagnostico_trz6']);
   $fecha_denuncia_trz6=Fecha_db($_POST['fecha_denuncia_trz6']);
   $cardiopatia_detectada=$_POST['cardiopatia_detectada_trz6'];
   $fecha_nac_trz6=Fecha_db($fecha_nac);
   
    
   $q="select nextval('trazadorassps.seq_id_trz6') as id_planilla";
   $id_planilla=sql($q) or fin_pagina();
   $id_planilla=$id_planilla->fields['id_planilla'];
   
   
 if ($entidad_alta=='na'){ //smiafiliados
    $id_beneficarios=0;
   	$query="insert into trazadorassps.trazadora_6	
             (id_trz6,cuie,id_smiafiliados,fecha_nac,fecha_diagnos,fecha_denuncia
             ,cardiopatia_detectada,fecha_carga,usuario,comentario,id_beneficiarios)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz6','$fecha_diagnostico_trz6','$fecha_denuncia_trz6',
             '$cardiopatia_detectada','$fecha_carga','$usuario','$comentario_trz6',$id_beneficarios)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
    }
if ($entidad_alta=='nu'){ //leche.beneficarios
    $id_smiafiliados=0;
	$query="insert into trazadorassps.trazadora_6	
             (id_trz6,cuie,id_beneficiarios,fecha_nac,fecha_diagnos,fecha_denuncia
             ,cardiopatia_detectada,fecha_carga,usuario,comentario,id_smiafiliados)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz6','$fecha_diagnostico_trz6','$fecha_denuncia_trz6',
             '$cardiopatia_detectada','$fecha_carga','$usuario','$comentario_trz6',$id_smiafiliados)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
	}
    $accion="Se guardo la Planilla";    
	$mensaje="TRAZADORA VI: CAPACIDAD DE DETECCIÓN DE CASOS DE CARDIOPATÍA CONGÉNITA EN EL MENOR DE 1 AÑO"; 
    $db->CompleteTrans();
   
   //fin de codigo de insercion
   if ($pagina=="tzr_admin.php") echo "<script>window.close()</script>";  
           
}//de if ($_POST['guardar']=="Guardar Planilla Trazadora 6")

if ($_POST['guardar']=="Guardar Planilla Trazadora 7"){
   
   $fecha_carga=date("Y-m-d");
   $usuario=$_ses_user['name'];
   $db->StartTrans();  
   $fecha_control_trz7=Fecha_db($_POST['fecha_control_trz7']); 
   $peso_trz7=$_POST['peso_trz7'];
   $talla_trz7=$_POST['talla_trz7'];
   
   $tension_arterial_M=$_POST['tension_arterial_trz7_M'];
   $tension_arterial_m=$_POST['tension_arterial_trz7__m'];
   $maxima=str_pad($tension_arterial_M,3,"0",STR_PAD_LEFT);
   $minima=str_pad($tension_arterial_m,3,"0",STR_PAD_LEFT);
   $tension_arterial="$maxima"."/"."$minima";
   $percentilo_peso_edad_trz7=$_POST['percentilo_peso_edad_trz7'];
   $percentilo_talla_edad_trz7=$_POST['percentilo_talla_edad_trz7'];
   $percentilo_peso_talla_trz7=$_POST['percentilo_peso_talla_trz7'];
   $comentario_trz7=$_POST['observaciones_trz7'];
   $fecha_nac_trz7=Fecha_db($fecha_nac);
       
    
   $q="select nextval('trazadorassps.seq_id_trz7') as id_planilla";
   $id_planilla=sql($q) or fin_pagina();
   $id_planilla=$id_planilla->fields['id_planilla'];
   
    //codigo de insercion  
 if ($entidad_alta=='na'){ //smiafiliados
    $id_beneficarios=0;
   	$query="insert into trazadorassps.trazadora_7	
             (id_trz7,cuie,id_smiafiliados,fecha_nac,fecha_control,peso,
  			talla ,tension_arterial,percentilo_peso_edad , percentilo_talla_edad ,percentilo_peso_talla
             ,fecha_carga,usuario,comentario,id_beneficiarios)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz7','$fecha_control_trz7','$peso_trz7','$talla_trz7',
              '$tension_arterial', '$percentilo_peso_edad_trz7','$percentilo_talla_edad_trz7',
               '$percentilo_peso_talla_trz7',
               '$fecha_carga','$usuario','$comentario_trz7',$id_beneficarios)";
    

    sql($query, "Error al insertar la Planilla") or fin_pagina();
    }
if ($entidad_alta=='nu'){ //leche.beneficarios
    $id_smiafiliados=0;
	$query="insert into trazadorassps.trazadora_7	
             (id_trz7,cuie,id_beneficiarios,fecha_nac,fecha_control,peso,
  			talla ,tension_arterial,percentilo_peso_edad , percentilo_talla_edad ,percentilo_peso_talla
             ,fecha_carga,usuario,comentario,id_smiafiliados)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz7','$fecha_control_trz7','$peso_trz7','$talla_trz7'
              '$tension_arterial', '$percentilo_peso_edad_trz7','$percentilo_talla_edad_trz7',
               '$percentilo_peso_talla_trz7',
               '$fecha_carga','$usuario','$comentario_trz7',$id_smiafiliados)";
    

    sql($query, "Error al insertar la Planilla") or fin_pagina();
	}
    $accion="Se guardo la Planilla";    
	$mensaje="TRAZADORA VII: SEGUIMIENTO DE SALUD DEL NIÑO DE 1 A 9 AÑOS"; 
    $db->CompleteTrans();
   
   if ($pagina=="tzr_admin.php") echo "<script>window.close()</script>";  
           
}//de if ($_POST['guardar']=="Guardar Planilla Trazadora 7")

if ($_POST['guardar']=="Guardar Planilla Trazadora 8"){
   
   $fecha_carga=date("Y-m-d");
   $usuario=$_ses_user['name'];
   $db->StartTrans();  
   $fecha_vac_cua_bact_trz8=Fecha_db ($_POST['fecha_vac_cua_bact_trz8']);
   $fecha_vac_antipolio_trz8=Fecha_db ($_POST['fecha_vac_antipolio_trz8']);
   $comentario_trz8=$_POST['observaciones_trz8']; 
   $fecha_nac_trz8=Fecha_db($fecha_nac);       
    
   $q="select nextval('trazadorassps.seq_id_trz8') as id_planilla";
   $id_planilla=sql($q) or fin_pagina();
   $id_planilla=$id_planilla->fields['id_planilla'];
   
 if ($entidad_alta=='na'){ //smiafiliados
    $id_beneficarios=0;
   	$query="insert into trazadorassps.trazadora_8	
             (id_trz8,cuie,id_smiafiliados,fecha_nac,fecha_vacuna_cuad_bacteriana,
  			  fecha_vacuna_antipoliomelitica,
              fecha_carga,usuario,comentario,id_beneficiarios)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz8',
             '$fecha_vac_cua_bact_trz8','$fecha_vac_antipolio_trz8',
             '$fecha_carga','$usuario','$comentario_trz8',$id_beneficarios)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
    }
if ($entidad_alta=='nu'){ //leche.beneficarios
    $id_smiafiliados=0;
	$query="insert into trazadorassps.trazadora_8	
             (id_trz8,cuie,id_beneficiarios,fecha_nac,fecha_vacuna_cuad_bacteriana,
  			  fecha_vacuna_antipoliomelitica,
              fecha_carga,usuario,comentario,id_smiafiliados)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz8',
             '$fecha_vac_cua_bact_trz8','$fecha_vac_antipolio_trz8',
             '$fecha_carga','$usuario','$comentario_trz8',$id_smiafiliados)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
	}
    $accion="Se guardo la Planilla";    
	$mensaje="TRAZADORA VIII: COBERTURA DE INMUNIZACIONES A LOS 24 MESES"; 
    $db->CompleteTrans();
   
   //fin de codigo de insercion
   if ($pagina=="tzr_admin.php") echo "<script>window.close()</script>";  
           
}//de if ($_POST['guardar']=="Guardar Planilla Trazadora 8")

if ($_POST['guardar']=="Guardar Planilla Trazadora 9"){
   
   $fecha_carga=date("Y-m-d");
   $usuario=$_ses_user['name'];
   $db->StartTrans();  
   $fecha_vac_tri_bac_trz9=Fecha_db($_POST['fecha_vac_tri_bac_trz9']);
   $fecha_vac_tri_vir_trz9=Fecha_db($_POST['fecha_vac_tri_vir_trz9']);
   $fecha_vac_antipolio_trz9=Fecha_db($_POST['fecha_vac_antipolio_trz9']);
   $comentario_trz9= $_POST['observaciones_trz9'];
   $fecha_nac_trz9=Fecha_db($fecha_nac);
        
    
   $q="select nextval('trazadorassps.seq_id_trz9') as id_planilla";
   $id_planilla=sql($q) or fin_pagina();
   $id_planilla=$id_planilla->fields['id_planilla'];
   
    //codigo de insercion  
 if ($entidad_alta=='na'){ //smiafiliados
    $id_beneficarios=0;
   	$query="insert into trazadorassps.trazadora_9	
             (id_trz9,cuie,id_smiafiliados,fecha_nac,
  				fecha_vacuna_trip_bacteriana,
  				fecha_vacuna_trip_viral,
  				fecha_vacuna_antipoliomelitica,
             	fecha_carga,usuario,comentario,id_beneficiarios)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz9',
              '$fecha_vac_tri_bac_trz9','$fecha_vac_tri_vir_trz9','$fecha_vac_antipolio_trz9',
             '$fecha_carga','$usuario','$comentario_trz9',$id_beneficarios)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
    }
if ($entidad_alta=='nu'){ //leche.beneficarios
    $id_smiafiliados=0;
	$query="insert into trazadorassps.trazadora_9	
             (id_trz9,cuie,id_beneficiarios,fecha_nac,
  				fecha_vacuna_trip_bacteriana,
  				fecha_vacuna_trip_viral,
  				fecha_vacuna_antipoliomelitica,
             	fecha_carga,usuario,comentario,id_smiafiliados)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz9',
              '$fecha_vac_tri_bac_trz9','$fecha_vac_tri_vir_trz9','$fecha_vac_antipolio_trz9',
             '$fecha_carga','$usuario','$comentario_trz9',$id_smiafiliados)";
    sql($query, "Error al insertar la Planilla") or fin_pagina();
	}
    $accion="Se guardo la Planilla";    
	$mensaje="TRAZADORA IX: COBERTURA DE INMUNIZACIONES A LOS 7 AÑOS"; 
    $db->CompleteTrans();
   
   //fin de codigo de insercion
   if ($pagina=="tzr_admin.php") echo "<script>window.close()</script>";  
           
}//de if ($_POST['guardar']=="Guardar Planilla Trazadora 9")


if ($_POST['guardar']=="Guardar Planilla Trazadora 10"){
   
   $fecha_carga=date("Y-m-d");
   $usuario=$_ses_user['name'];
   $db->StartTrans();   
   $fecha_control_trz10=Fecha_db($_POST['fecha_control_trz10']);
   $talla_trz10=$_POST['talla_trz10'];
   $peso_trz10=$_POST['peso_trz10'];
   
   $tension_arterial_M=$_POST['tension_arterial_trz10_M'];
   $tension_arterial_m=$_POST['tension_arterial_trz10_m'];
   $maxima=str_pad($tension_arterial_M,3,"0",STR_PAD_LEFT);
   $minima=str_pad($tension_arterial_m,3,"0",STR_PAD_LEFT);
   $tension_arterial_trz10="$maxima"."/"."$minima";
   
   $comentario_trz10=$_POST['observaciones_trz10'];
   $fecha_nac_trz10=Fecha_db($fecha_nac);
        
    
   $q="select nextval('trazadorassps.seq_id_trz10') as id_planilla";
   $id_planilla=sql($q) or fin_pagina();
   $id_planilla=$id_planilla->fields['id_planilla'];
   
  if ($entidad_alta=='na'){ //smiafiliados
    $id_beneficarios=0;
   	$query="insert into trazadorassps.trazadora_10	
             (id_trz10,cuie,id_smiafiliados,fecha_nac,
  			  fecha_control,talla,peso,tension_arterial
             ,fecha_carga,usuario,comentario,id_beneficiarios)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz10','$fecha_control_trz10',
              '$talla_trz10','$peso_trz10','$tension_arterial_trz10',
             '$fecha_carga','$usuario','$comentario_trz10',$id_beneficarios)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
     }
if ($entidad_alta=='nu'){ //leche.beneficarios
    $id_smiafiliados=0;
	$query="insert into trazadorassps.trazadora_10	
             (id_trz10,cuie,id_beneficiarios,fecha_nac,
  			  fecha_control,talla,peso,tension_arterial
             ,fecha_carga,usuario,comentario,id_smiafiliados)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz10','$fecha_control_trz10',
              '$talla_trz10','$peso_trz10','$tension_arterial_trz10',
             '$fecha_carga','$usuario','$comentario_trz10',$id_smiafiliados)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
	}
    $accion="Se guardo la Planilla";    
	$mensaje="TRAZADORA X: SEGUIMIENTO DE SALUD DEL ADOLESCENTE DE 10 A 19 AÑOS"; 
    $db->CompleteTrans();
   
   if ($pagina=="tzr_admin.php") echo "<script>window.close()</script>";  
           
}//de if ($_POST['guardar']=="Guardar Planilla Trazadora 10")

if ($_POST['guardar']=="Guardar Planilla Trazadora 11"){
   
   $fecha_carga=date("Y-m-d");
   $usuario=$_ses_user['name'];
   $db->StartTrans();   
   
   $fecha_nac_trz11=Fecha_db($fecha_nac);
   $fecha_taller=Fecha_db($_POST['fecha_taller']);
   $tema_taller=$_POST['tema_taller'];
   $comentario_trz11=$_POST['observaciones_trz11'];
        
    
   $q="select nextval('trazadorassps.seq_id_trz11') as id_planilla";
   $id_planilla=sql($q) or fin_pagina();
   $id_planilla=$id_planilla->fields['id_planilla'];
   
  if ($entidad_alta=='na'){ //smiafiliados
    $id_beneficarios=0;
   	$query="insert into trazadorassps.trazadora_11	
             (id_trz11,cuie,id_smiafiliados,fecha_nac,
  			  fecha_asis_taller,tema_taller
             ,fecha_carga,usuario,comentario,id_beneficiarios)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz11',
             '$fecha_taller','$tema_taller',	
             '$fecha_carga','$usuario','$comentario_trz11',$id_beneficarios)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
    }
if ($entidad_alta=='nu'){ //leche.beneficarios
    $id_smiafiliados=0;
	$query="insert into trazadorassps.trazadora_11	
             (id_trz11,cuie,id_beneficiarios,fecha_nac,
  			  fecha_asis_taller,tema_taller
             ,fecha_carga,usuario,comentario,id_smiafiliados)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz11',
             '$fecha_taller','$tema_taller',	
             '$fecha_carga','$usuario','$comentario_trz11',$id_smiafiliados)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
	}
    $accion="Se guardo la Planilla";    
	$mensaje="TRAZADORA XI: PROMOCION DEL DERECHO AL CUIDADO SEXUAL Y REPRODUCTIVO ENTRE LOS 14 Y LOS 25 AÑOS"; 
    $db->CompleteTrans();
   
   if ($pagina=="tzr_admin.php") echo "<script>window.close()</script>";  
           
}//de if ($_POST['guardar']=="Guardar Planilla Trazadora 11")

if ($_POST['guardar']=="Guardar Planilla Trazadora 12"){
   
   $fecha_carga=date("Y-m-d");
   $usuario=$_ses_user['name'];
   $db->StartTrans();  
   $fecha_diagnostico_trz12=Fecha_db($_POST['fecha_diagnostico_trz12']);
   $diag=$_POST['diag'];
   $fecha_inic_tratamiento=($_POST['fecha_inic_tratamiento']=='')?'1900-01-01':Fecha_db($_POST['fecha_inic_tratamiento']);
      
   $fecha_nac_trz12=Fecha_db($fecha_nac);
   $comentario_trz12=$_POST['observaciones_trz12'];     
    
   $q="select nextval('trazadorassps.seq_id_trz12') as id_planilla";
   $id_planilla=sql($q) or fin_pagina();
   $id_planilla=$id_planilla->fields['id_planilla'];
   
    //codigo de insercion  
 if ($entidad_alta=='na'){ //smiafiliados
    $id_beneficarios=0;
    $query="insert into trazadorassps.trazadora_12	
             (id_trz12,cuie,id_smiafiliados,fecha_nac,
  			  fecha_diagnostico,diagnostico, fecha_inic_tratamiento, 
             fecha_carga,usuario,comentario,id_beneficiarios)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz12',
              '$fecha_diagnostico_trz12','$diag','$fecha_inic_tratamiento',
             '$fecha_carga','$usuario','$comentario_trz12',$id_beneficarios)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
    }
if ($entidad_alta=='nu'){ //leche.beneficarios
    $id_smiafiliados=0;
	 $query="insert into trazadorassps.trazadora_12	
             (id_trz12,cuie,id_beneficiarios,fecha_nac,
  			 fecha_diagnostico,diagnostico, fecha_inic_tratamiento 
             fecha_carga,usuario,comentario,id_smiafiliados)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz12',
              '$fecha_diagnostico_trz12','$diag','$fecha_inic_tratamiento',
             '$fecha_carga','$usuario','$comentario_trz12',$id_smiafiliados)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
	}
    $accion="Se guardo la Planilla";    
	$mensaje="TRAZADORA XII: PREVENCION DEL CANCER CERVICO-UTERINO";  
    $db->CompleteTrans();
   
   //fin de codigo de insercion
   if ($pagina=="tzr_admin.php") echo "<script>window.close()</script>";  
           
}//de if ($_POST['guardar']=="Guardar Planilla Trazadora 12")

if ($_POST['guardar']=="Guardar Planilla Trazadora 13"){
   
   $fecha_carga=date("Y-m-d");
   $usuario=$_ses_user['name'];
   $db->StartTrans();   
   $fecha_diagnos_trz13=Fecha_db($_POST['fecha_diagnostico_tzr13']);
   $fecha_inic_tratamiento_trz13=Fecha_db($_POST['fecha_inic_tratamiento_trz13']);
   $estadio=(!$_POST['estadio'])?"":$_POST['estadio'];
   $carcinoma=$_POST['carcinoma'];
   $tamanio=(!$_POST['tamanio_trz13'])? "":$_POST['tamanio_trz13'];
   $ganglios_linfaticos=(!$_POST['ganglios'])? "":$_POST['ganglios'];
   $metastasis=(!$_POST['metastasis'])?"":$_POST['metastasis'];
   
   
   $fecha_nac_trz13=Fecha_db($fecha_nac);
   $comentario_trz13=$_POST['observaciones_trz13'];
        
    
   $q="select nextval('trazadorassps.seq_id_trz13') as id_planilla";
   $id_planilla=sql($q) or fin_pagina();
   $id_planilla=$id_planilla->fields['id_planilla'];
   
    //codigo de insercion  
 if ($entidad_alta=='na'){ //smiafiliados
    $id_beneficarios=0;
   	$query="insert into trazadorassps.trazadora_13	
             (id_trz13,cuie,id_smiafiliados,fecha_nac,
  			   fecha_diagnostico,fecha_inic_tratamiento ,estadio,carcinoma,tamanio,ganglios_linfaticos,metastasis,
             fecha_carga,usuario,comentario,id_beneficiarios)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz13',
              '$fecha_diagnos_trz13','$fecha_inic_tratamiento_trz13','$estadio','$carcinoma','$tamanio','$ganglios_linfaticos','$metastasis',
             '$fecha_carga','$usuario','$comentario_trz13',$id_beneficarios)";
    
   	sql($query, "Error al insertar la Planilla") or fin_pagina();
    }
if ($entidad_alta=='nu'){ //leche.beneficarios
    $id_smiafiliados=0;
	$query="insert into trazadorassps.trazadora_13	
             (id_trz13,cuie,id_beneficiarios,fecha_nac,
  			   fecha_diagnostico,fecha_inic_tratamiento ,estadio,carcinoma,tamanio,ganglios_linfaticos,metastasis,
             fecha_carga,usuario,comentario,id_smiafiliados)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz13',
              '$fecha_diagnos_trz13','$fecha_inic_tratamiento_trz13','$estadio','$carcinoma','$tamanio','$ganglios_linfaticos','$metastasis',
             '$fecha_carga','$usuario','$comentario_trz13',$id_smiafiliados)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
	}
    $accion="Se guardo la Planilla";    
	$mensaje="TRAZADORA XIII: DIAGNOSTICO DEL CANCER DE MAMA"; 
    $db->CompleteTrans();
   
   //fin de codigo de insercion
   if ($pagina=="tzr_admin.php") echo "<script>window.close()</script>";  
           
}//de if ($_POST['guardar']=="Guardar Planilla Trazadora 13")

if ($_POST['guardar']=="Guardar Planilla Trazadora 14"){
   
   $fecha_carga=date("Y-m-d");
   $usuario=$_ses_user['name'];
   $db->StartTrans();   
   $fecha_def=Fecha_db($_POST['fecha_def']);
   $fecha_audit_muerte=Fecha_db($_POST['fecha_audit_muerte']);
   $fecha_parto_o_int_embarazo=Fecha_db($_POST['fecha_parto_o_int_embarazo']);
   $comentario_trz14=$_POST['observaciones_trz14'];
   
    $fecha_nac_trz14=Fecha_db($fecha_nac);    
    
   $q="select nextval('trazadorassps.seq_id_trz14') as id_planilla";
   $id_planilla=sql($q) or fin_pagina();
   $id_planilla=$id_planilla->fields['id_planilla'];
   
    //codigo de insercion  
 if ($entidad_alta=='na'){ //smiafiliados
    $id_beneficarios=0;
   	$query="insert into trazadorassps.trazadora_14	
             (id_trz14,cuie,id_smiafiliados,fecha_nac,
  			 fecha_defuncion, fecha_audit_muerte,fecha_parto_o_int_embarazo,
             fecha_carga,usuario,comentario,id_beneficiarios)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz14',
              '$fecha_def','$fecha_audit_muerte','$fecha_parto_o_int_embarazo',
             '$fecha_carga','$usuario','$comentario_trz14',$id_beneficarios)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
    }
if ($entidad_alta=='nu'){ //leche.beneficarios
    $id_smiafiliados=0;
	$query="insert into trazadorassps.trazadora_14	
             (id_trz14,cuie,id_beneficiarios,fecha_nac,
  			  fecha_defuncion ,fecha_audit_muerte,fecha_parto_o_int_embarazo,
              fecha_carga,usuario,comentario,id_smiafiliados)
             values
             ('$id_planilla','$cuie','$id','$fecha_nac_trz14',
              '$fecha_def','$fecha_audit_muerte','$fecha_parto_o_int_embarazo',
             '$fecha_carga','$usuario','$comentario_trz14',$id_smiafiliados)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
	}
    $accion="Se guardo la Planilla";    
	$mensaje="TRAZADORA XIV: EVALUACION DEL PROCESO DE ATENCION DE MUERTES INFANTILES Y MATERNAS";  
    $db->CompleteTrans();
   
   //fin de codigo de insercion
   if ($pagina=="tzr_admin.php") echo "<script>window.close()</script>";  
           
}//de if ($_POST['guardar']=="Guardar Planilla Trazadora 14")

/*if ($_POST['borrar']=="Borrar"){
	$query="delete from trazadoras.partos
			where id_par=$id_planilla";
	sql($query, "Error al insertar la Planilla") or fin_pagina();
	$accion="Se elimino la planilla $id_planilla de Partos"; 	
}*/

//if ($pagina=='tzr_admin.php'){
if ($id_planilla){	
	if ($entidad_alta=='nu'){//carga de prestacion a paciente NO PLAN NACER
	$sql="select * from leche.beneficiarios
	where id_beneficiarios=$id";
    $res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();
    
    $apellido=trim($res_comprobante->fields['apellido']);
	$nombre=trim($res_comprobante->fields['nombre']);
	$num_doc_1=trim($res_comprobante->fields['documento']);
	$num_doc=number_format($num_doc_1,0,'.','');
	$localidad=$res_comprobante->fields['domicilio'];
	$fecha_nac=$res_comprobante->fields['fecha_nac'];
	$sexo=$res_comprobante->fields['sexo'];
	}

	if ($entidad_alta=='na'){//carga de prestacion a paciente PLAN NACER
	$sql="select * from nacer.smiafiliados
	 left join nacer.efe_conv on (cuieefectorasignado=cuie)
	 where id_smiafiliados=$id";
    $res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();
    
    $apellido=trim($res_comprobante->fields['afiapellido']);
	$nombre=trim($res_comprobante->fields['afinombre']);
	$num_doc=number_format($res_comprobante->fields['afidni'],0,'.','');
	$localidad=$res_comprobante->fields['afidomlocalidad'];
	$fecha_nac=$res_comprobante->fields['afifechanac'];
	$sexo=$res_comprobante->fields['afisexo'];
	}
	
		
}

/*if ($id_planilla) {
$query="SELECT 
  *
FROM
  trazadoras.partos  
  where id_par=$id_planilla";

$res_factura=sql($query, "Error al traer el Comprobantes") or fin_pagina();

$cuie=$res_factura->fields['cuie'];
$clave=$res_factura->fields['clave'];
$tipo_doc=$res_factura->fields['tipo_doc'];
$num_doc=number_format($res_factura->fields['num_doc'],0,'.','');
$apellido=$res_factura->fields['apellido'];
$nombre=$res_factura->fields['nombre'];
$fecha_parto=$res_factura->fields['fecha_parto'];
$apgar=number_format($res_factura->fields['apgar'],0,'','');
$peso=number_format($res_factura->fields['peso'],3,'.','');
$vdrl=$res_factura->fields['vdrl'];
$antitetanica=$res_factura->fields['antitetanica'];
$fecha_conserjeria=$res_factura->fields['fecha_conserjeria'];
$observaciones=$res_factura->fields['observaciones'];
$fecha_carga=$res_factura->fields['fecha_carga'];
$usuario=$res_factura->fields['usuario'];
}*/

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios para la trazadora
function control_trazadora1()
{ 
  
  if(document.all.fecha_control_prenatal.value==""){
  alert('Debe Seleccionar la Fecha del Control Prenatal');
  return false;
 }
 
  if(document.all.fum.value==""){
  alert('Debe Seleccionar la Fecha de la Ultima Mestruacion (FUM)');
  return false;
 }
 
  if(document.all.fpp.value==""){
  alert('Debe Seleccionar la Fecha Probable de Parto (FPP)');
  return false;
 }
 if(document.all.edad_gestacional_trz1.value==""){
	  alert('Debe Seleccionar la Edad Gestacional');
	  return false;
	 }
 return control_nuevos();
}//cotrol de trazadora 1


function control_trazadora2()
{ 
	if(document.all.fecha_control.value==""){
		  alert('Debe Ingresar la Fecha de Control');
		  return false;
		 }
	 
	 if(document.all.edad_gestacional.value==""){
		  alert('Debe Ingresar la Edad Gestacional');
		  return false;
		 }else{
 		var edad_gestacional=document.all.edad_gestacional.value;
		if(isNaN(edad_gestacional)){
			alert('El dato de la Edad Gestacional debe ser un Numero Entero');
			document.all.edad_gestacional.focus();
			return false;
		}
	}
	
	if (document.all.peso.value==""){ 
		alert('Debe ingresar el Peso');
		document.all.peso.focus();
		return false;
		}
	
	if (isNaN(document.all.peso.value)) {
		alert('El dato del Peso debe ser un Numero Real');
		document.all.peso.focus();
		return false;
		}
	
	if (document.all.peso.value>300) {
		alert ('El Peso debe ser menor de 300 Kg');
		document.all.peso.focus();
		return false;
		}
		
	if (document.all.peso.value<0) {
		alert ('El Peso debe ser mayor de 0 Kg');
		document.all.peso.focus();
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
	 
	 
 return control_nuevos();
}//cotrol de trazadora 2

function control_trazadora3()
{ 
	 
	 if (document.all.peso_nac_trz3.value==""){ 
		alert('Debe ingresar el Peso');
		document.all.peso_nac_trz3.focus();
		return false;
		}
	
	if (isNaN(document.all.peso_nac_trz3.value)) {
		alert('El dato del Peso debe ser un Numero Real');
		document.all.peso_nac_trz3.focus();
		return false;
		}
	
	if (document.all.peso_nac_trz3.value>1500) {
		alert ('El Peso debe ser menor de 1500 g');
		document.all.peso_nac_trz3.focus();
		return false;
		}
		
	if (document.all.peso_nac_trz3.value<750) {
		alert ('El Peso debe ser mayor de 750 g');
		document.all.peso_nac_trz3.focus();
		return false;
		}
	 
 return control_nuevos();
}//cotrol de trazadora 3

function control_trazadora4()
{ 
	if(document.all.fecha_control_trz4.value==""){
		  alert('Debe Ingresar Fecha de Control');
		  return false;
		 }
	
	if (document.all.peso_trz4.value==""){ 
		alert('Debe ingresar el Peso');
		document.all.peso_trz4.focus();
		return false;
		}
	
	if (isNaN(document.all.peso_trz4.value)) {
		alert('El dato del Peso debe ser un Numero Real');
		document.all.peso_trz4.focus();
		return false;
		}
	
	if (document.all.peso_trz4.value>190) {
		alert ('El Peso debe ser menor de 190 Kg');
		document.all.peso_nac_trz3.focus();
		return false;
		}
		
	if (document.all.peso_trz4.value<0) {
		alert ('El Peso debe ser mayor de 0 Kg');
		document.all.peso_nac_trz3.focus();
		return false;
		}
	
	
	if(document.all.talla_trz4.value==""){
		  alert('Debe Ingresar la Talla');
		  return false;
		 }
	 if(document.all.perimetro_cefalico_trz4.value==""){
		  alert('Debe Ingresar el Perimetro Cefalico');
		  return false;
		 }
	  /*if(document.all.percentilo_peso_edad_trz4.value==""){
		  alert('Debe Ingresar el Percentilo Peso - Edad');
		  return false;
		 }
	 if(document.all.percentilo_talla_edad_trz4.value==""){
		  alert('Debe Ingresar el Percentilo Talla - Edad');
		  return false;
		 }
	 if(document.all.percentilo_per_cefalico_edad_trz4.value==""){
		  alert('Debe Ingresar el Percentilo del Perimetro Cefalico - Edad');
		  return false;
		 }
	 if(document.all.percentilo_peso_talla_trz4.value==""){
		  alert('Debe Ingresar el Percentilo del Peso - Talla');
		  return false;
		 }*/
 return control_nuevos();
}//cotrol de trazadora 4

function control_trazadora6()
{ 
	if(document.all.cardiopatia_detectada_trz6.value==""){
			  alert('Debe Ingresar la Cardiopatia Detectada');
			  return false;
		 }

	 if(document.all.cardiopatia_detectada_trz6.value<1 || document.all.cardiopatia_detectada_trz6.value>162){
		  alert('El Valor de la Cardiopatia Detectada debe ser mayor de 1 y menor de 162');
		  return false;
	 }

	if(document.all.fecha_diagnostico_trz6.value==""){
		  alert('Debe Ingresar la Fecha del Diagnostico');
		  return false;
		 }

	if(document.all.fecha_denuncia_trz6.value==""){
		  alert('Debe Ingresar la Fecha de Denuncia');
		  return false;
		 }
	 
 return control_nuevos();
}//cotrol de trazadora 6

function control_trazadora7()
{ 
	if(document.all.fecha_control_trz7.value==""){
		  alert('Debe Ingresar la Fecha del Control');
		  return false;
		 }
	
	if (document.all.peso_trz7.value==""){ 
		alert('Debe ingresar el Peso');
		document.all.peso_trz7.focus();
		return false;
		}
	
	if (isNaN(document.all.peso_trz7.value)) {
		alert('El dato del Peso debe ser un Numero Real');
		document.all.peso_trz7.focus();
		return false;
		}
		
	if(document.all.talla_trz7.value==""){
		  alert('Debe Ingresar el Talla al Nacer');
		  return false;
		 }
	
	if(document.all.tension_arterial_trz7_M.value==""){
	 alert("Debe completar el campo Tension Arterial MAXIMA");
	 document.all.tension_arterial_trz7_M.focus();
	 return false;
	 }else{
 		var tension_arterial_trz7_M=document.all.tension_arterial_trz7_M.value;
		if(isNaN(tension_arterial_trz7_M)){
			alert('El dato de la tension Arterial MAXIMA debe ser un Numero Entero');
			document.all.tension_arterial_trz7_M.focus();
			return false;
		}
	}
	
	if(document.all.tension_arterial_trz7_m.value==""){
	 alert("Debe completar el campo de Tension Arterial MINIMA");
	 document.all.tension_arterial_trz7_m.focus();
	 return false;
	 }else{
 		var tension_arterial_trz7_m=document.all.tension_arterial_trz7_m.value;
		if(isNaN(tension_arterial_trz7_m)){
			alert('El dato de la tension Arterial MINIMA debe ser un Numero Entero');
			document.all.tension_arterial_trz7_m.focus();
			return false;
		}
	}	 
		 
	 /*if(document.all.percentilo_peso_edad_trz7.value==""){
		  alert('Debe Ingresar el Percentilo Peso - Edad');
		  return false;
		 }
	 if(document.all.percentilo_talla_edad_trz7.value==""){
		  alert('Debe Ingresar el Percentilo Talla - Edad');
		  return false;
		 }
	 if(document.all.percentilo_per_cefalico_edad_trz7.value==""){
		  alert('Debe Ingresar el Percentilo Perimetro Cefalico - Edad');
		  return false;
		 }
	 if(document.all.percentilo_peso_talla_trz7.value==""){
		  alert('Debe Ingresar el Percentilo Perimetro Peso - Talla');
		  return false;
		 }*/
	 
 return control_nuevos();
}//cotrol de trazadora 7

function control_trazadora8()
{ 
	if(document.all.fecha_vac_cua_bact_trz8.value==""){
		  alert('Debe Ingresar Fecha de vacunación Cuádruple Bacteriana');
		  return false;
		 }
	if(document.all.fecha_vac_antipolio_trz8.value==""){
		  alert('Debe Ingresar Fecha de vacunación Antipoliomielítica');
		  return false;
		 }
		  
 return control_nuevos();
}//cotrol de trazadora 8

function control_trazadora9()
{ 
	 if(document.all.fecha_vac_tri_bac_trz9.value==""){
		  alert('Debe Ingresar Fecha de aplicación de vacuna triple bacteriana');
		  return false;
		 }
	 if(document.all.fecha_vac_tri_vir_trz9.value==""){
		  alert('Debe Ingresar Fecha de aplicación de vacuna triple viral');
		  return false;
		 }
	 if(document.all.fecha_vac_antipolio_trz9.value==""){
		  alert('Debe Ingresar Fecha de aplicación de vacuna antipoliomielítica');
		  return false;
		 }
	 
 return control_nuevos();
}//cotrol de trazadora 9


function control_trazadora10()
{ 
	 if(document.all.fecha_control_trz10.value==""){
		  alert('Debe Ingresar Fecha de Control');
		  return false;
		 }
	 if(document.all.talla_trz10.value==""){
		  alert('Debe Ingresar la Talla');
		  return false;
		 }
	 if (document.all.peso_trz10.value==""){ 
		alert('Debe ingresar el Peso');
		document.all.peso_trz10.focus();
		return false;
		}
	
	if (isNaN(document.all.peso_trz10.value)) {
		alert('El dato del Peso debe ser un Numero Real');
		document.all.peso_trz10.focus();
		return false;
		}
	
		
	if(document.all.tension_arterial_trz10_M.value==""){
	 alert("Debe completar el campo Tension Arterial MAXIMA");
	 document.all.tension_arterial_trz10_M.focus();
	 return false;
	 }else{
 		var tension_arterial_trz10_M=document.all.tension_arterial_trz10_M.value;
		if(isNaN(tension_arterial_trz10_M)){
			alert('El dato de la tension Arterial MAXIMA debe ser un Numero Entero');
			document.all.tension_arterial_trz10_M.focus();
			return false;
		}
	}
	
	if(document.all.tension_arterial_trz10_m.value==""){
	 alert("Debe completar el campo de Tension Arterial MINIMA");
	 document.all.tension_arterial_trz10_m.focus();
	 return false;
	 }else{
 		var tension_arterial_trz10_m=document.all.tension_arterial_trz10_m.value;
		if(isNaN(tension_arterial_trz10_m)){
			alert('El dato de la tension Arterial MINIMA debe ser un Numero Entero');
			document.all.tension_arterial_trz10_m.focus();
			return false;
		}
	}	 
		
	 
 return control_nuevos();
}//cotrol de trazadora 10

function control_trazadora11()
{ 
	 if(document.all.fecha_taller.value==""){
		  alert('Debe Ingresar Fecha de Asistencia al Taller');
		  return false;
		 }
	 if(document.all.tema_taller.value==-1){
		  alert('Debe Ingresar el Tema del Taller');
		  return false;
		 }
		 
 return control_nuevos();
}//cotrol de trazadora 11

function control_trazadora12()
{ 
	 if(document.all.fecha_diagnostico_trz12.value==""){
		  alert('Debe Ingresar Fecha de Diagnostico Histologico');
		  return false;
		 }
	 if(document.all.diag.value==-1){
		  alert('Debe Ingresar el Diagnostico');
		  return false;
		 }

 
 return control_nuevos();
}//cotrol de trazadora 12

function control_trazadora13()
{ 
	 if(document.all.fecha_diagnostico_tzr13.value==""){
		  alert('Debe Ingresar Fecha de Diagnostico');
		  return false;
		 }

	 /*if(document.all.estadio.value==""){
		  alert('Debe Ingresar Estadio');
		  return false;
		 }*/
	 if(document.all.carcinoma.value==-1){
		  alert('Debe Ingresar el Carcinoma');
		  return false;
		 }

	 /*if(document.all.tamanio_trz13.value==""){
		  alert('Debe Ingresar el Tamaño');
		  return false;
		 }
	 if(document.all.ganglios.value==""){
		  alert('Debe Ingresar Ganglios Linfaticos');
		  return false;
		 }
	 if(document.all.metastasis.value==""){
		  alert('Debe Ingresar Metastasis');
		  return false;
		 }*/
	 if(document.all.fecha_inic_tratamiento_trz13.value==""){
			document.all.fecha_inic_tratamiento_trz13.value="01-01-1980";
			  return true;
			 }
	 
 return control_nuevos();
}//cotrol de trazadora 13

function control_trazadora14()
{ 
	 if(document.all.fecha_def.value==""){
		  alert('Debe Ingresar la Fecha de Defuncion');
		  return false;
		 }
	 if(document.all.fecha_audit_muerte.value==""){
		  alert('Debe Ingresar la Fecha de la Auditoria de Muerte');
		  return false;
		 }
	 if(document.all.fecha_parto_o_int_embarazo.value==""){
		  alert('Debe Ingresar la Fecha de Parto o Interrupción del Embarazo');
		  return false;
		 }
	  
 return control_nuevos();
}//cotrol de trazadora 14

function control_nuevos()
{ 
 if(document.all.cuie.value=="-1"){
  alert('Debe Seleccionar un Efector');
  return false;
 } 
 if(document.all.tipo_doc.value=="-1"){
  alert('Debe Seleccionar un Tipo de Documento');
  return false; 
 }   
 if(document.all.num_doc.value==""){
  alert('Debe Ingresar un Documento');
  return false;
 }
 if(document.all.apellido.value==""){
  alert('Debe Ingresar un apellido');
  return false;
 }
 if(document.all.nombre.value==""){
  alert('Debe Ingresar un nombre');
  return false;
 } 
  
}//de function control_nuevos()

function editar_campos()
{	
	document.all.cuie.disabled=false;	
	document.all.tipo_doc.disabled=false;
	document.all.num_doc.readOnly=false;
	document.all.apellido.readOnly=false;
	document.all.nombre.readOnly=false;
	document.all.apgar.readOnly=false;	
	document.all.peso.readOnly=false;
	document.all.vdrl.disabled=false;
	document.all.antitetanica.disabled=false;	
	document.all.observaciones.readOnly=false;
	
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
</script>

<form name='form1' action='tzr_admin.php' method='POST'>
<input type="hidden" value="<?=$id_planilla?>" name="id_planilla">
<input type="hidden" value="<?=$pagina?>" name="pagina">
<input type="hidden" value="<?=$entidad_alta?>" name="entidad_alta">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<?
    	if (!$id_planilla) {
    	?>  
    	<font size=+1><b>Nuevo Dato</b></font>   
    	<? }
        else {
        ?>
        <font size=+1><b><? echo $mensaje?></b></font>   
        <? } ?>
       
    </td>
 </tr>
 <tr><td>
 
  <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
      <b>Datos del Efector</b> 
      </td>
     </tr>
     <tr>
       <td>
        <table>
                
         <tr>
         	<td align="center">
				<b>Cuie:</b>
			</td>
			<td align="center">			 	
			 <select name=cuie Style="width=257px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				<?if ($id_planilla) echo "disabled"?>>
			 <option value=-1>Seleccione</option>
			 <?
			 $sql= "select * from nacer.efe_conv order by nombre";
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
       </table> 
 </table>  
 
   <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Datos del Beneficiario</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         
        <tr>
         	<td align="right">
         	  <b>Clave Beneficiario:</b>
         	</td>         	
            <td align='left'>
              <input type='text' name='id' value='<?=$id;?>' size=40 align='right' readonly></b>
            </td>
         </tr> 

          <tr>
         	<td align="right">
         	  <b>Número de Documento:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$dni?>" name="dni" <? if ($id_planilla) echo "readonly"?>><font color="Red">Sin Puntos</font>
            </td>
         </tr> 
         
         <tr>
         	<td align="right">
         	  <b>Apellido:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$apellido?>" name="apellido" <? if ($id_planilla) echo "readonly"?>>
            </td>
         </tr> 
         
         <tr>
         	<td align="right">
         	  <b>Nombre:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$nombre?>" name="nombre" <? if ($id_planilla) echo "readonly"?>>
            </td>
         </tr>          
          <tr>
         <td align="right">
				<b>Fecha de Nacimiento:</b>
			</td>
			<td align='left'>
              <input type='text' name='fecha_nac' value='<?=fecha($fecha_nac);?>' size=40 align='right' readonly></b>
            
			    
			    <?$dias_de_vida=GetCountDaysBetweenTwoDates($fecha_nac, $fecha_hoy);
				if (($dias_de_vida>=0)&&($dias_de_vida<=28)) $grupo_etareo='Neonato';
				if (($dias_de_vida>28)&&($dias_de_vida<=365)) $grupo_etareo='Menor de Un Año';
				if (($dias_de_vida>365)&&($dias_de_vida<=730)) $grupo_etareo='24 Meses';
				
				//if (($dias_de_vida>365)&&($dias_de_vida<=2190)) $grupo_etareo='Uno a Cinco Años';
				if (($dias_de_vida>365)&&($dias_de_vida<=3650)) {
					if (($dias_de_vida>2555)&&($dias_de_vida<=2920)) $grupo_etareo='Siete Años';	
					else $grupo_etareo='Uno a Nueve Años';
				}
				if (($dias_de_vida>3650)&&($dias_de_vida<=7300)) $grupo_etareo='Adolecente';
				if (($dias_de_vida>7300)&&($dias_de_vida<=23725)) $grupo_etareo='Adulto';	
				?>
            
            </td>
         </tr>         
		  <tr>
		  <td align="right">
				<b>Edad:</b>
			</td>
			<?$edad=edad_con_meses($fecha_nac);
			  $edad_anio=$edad["anos"];
			?>
			<td align='left'>
              <input type='text' name='edad' value='<?=$edad_anio;?>' size=40 align='right' readonly></b>
            </td>
         </tr>
		 <tr>
         <td align="right">
				<b>Localidad:</b>
			</td>
			<td align='left'>
              <input type='text' name='localidad' value='<?=$localidad;?>' size=40 align='right' readonly></b>
            </td>
         </tr>
         <tr>
         <td align="right">
				<b>Sexo:</b>
			</td>
			<td align='left'>
              <input type='text' name='sexo' value='<?=$sexo;?>' size=40 align='right' readonly></b>
            </td>
         </tr>
          <tr>
         <td align="right">
				<b>Grupo Etareo:</b>
			</td>
			<td align='left'>
              <input type='text' name='sexo' value='<?=$grupo_etareo;?>' size=40 align='right' readonly></b>
            </td>
         </tr>
        
    </table>   
 </table>
 
 <?if (($grupo_etareo=='Adolecente' || $grupo_etareo=='Adulto') && trim($sexo)=='F'){?>
 <tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.trazadora1,2);" >
	  </td>
	  <td align="center">
	   <b>Datos de Trazadora I : ATENCIÓN TEMPRANA DE EMBARAZO</b>
	  </td>
	</tr>
</table></td></tr>
  <tr><td><table id="trazadora1" width=90% align="center" class="bordes" style="display:none;border:thin groove">
	     <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
	    
	    <tr>
   		<tr>
			<td align="right">
				<b>Fecha de Control Prenatal:</b>
			</td>
		    <td align="left">
		    	<?$fecha_ctrl_prenatal=date("d/m/Y");?>
		    	 <input type=text id=fecha_control_prenatal name=fecha_control_prenatal
		    	  value='<?=$fecha_ctrl_prenatal;?>' size=15>
		    	 <?=link_calendario("fecha_control_prenatal");?>					    	 
		    </td>		    
		</tr>
		<tr>
			<td align="right">
				<b>Fecha de ultima Mestruacion (FUM) :</b>
			</td>
		    <td align="left">
		    	 <input type=text id=fum name=fum value='<?=$fum;?>' size=15 >
		    	 <?=link_calendario("fum");?>					    	 
		    </td>		    
		</tr>
		<tr>
			<td align="right">
				<b>Fecha Probable de Parto (FPP):</b>
			</td>
		    <td align="left">
		    	 <input type=text id=fpp name=fpp value='<?=$fpp;?>' size=15 >
		    	 <?=link_calendario("fpp");?>					    	 
		    </td>		    
		</tr>
		<tr>
           <td align="right">
         	  <b> Edad gestacional al momento de la prestación:
         	</td> 
           <td >
             <input type='text' name='edad_gestacional_trz1' value='<?=$edad_gestacional_trz1;?>' size=40 align='right'></b>
           </td>
          </tr>		
		 <tr>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='4' name='observaciones_trz1' <? if ($id_planilla) echo "readonly"?>><?=$observaciones_trz1;?></textarea>
            </td>
         </tr>  
      
  
   <?if (!($id_planilla)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Planilla Trazadora I : ATENCIÓN TEMPRANA DE EMBARAZO</b>
  		</td>
  	</tr>  
      
      
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Planilla Trazadora 1' onclick="return control_trazadora1()"
         title="Guardar datos de la Planilla para la Trazadora I">
       </td>
      </tr>
     
     <?}?>
    
</table></td></tr>
 <?}?>    

  <?if (($grupo_etareo=='Adolecente'  || $grupo_etareo == 'Adulto') && trim($sexo)=='F'){?>
 <tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.trazadora2,2);" >
	  </td>
	  <td align="center">
	   <b>Datos de Trazadora II : SEGUIMIENTO DE EMBARAZO</b>
	  </td>
	</tr>
	
</table></td></tr>
  <tr><td><table id="trazadora2" width=90% align="center" class="bordes" style="display:none;border:thin groove">
	    
	     <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
	    
	    <tr>
			<td align="right">
				<b>Fecha del control :</b>
			</td>
		    <td align="left">
		    	<?$fecha_control=date("d/m/Y");?>
		    	 <input type=text id=fecha_control name=fecha_control value='<?=$fecha_control;?>' size=15 >
		    	 <?=link_calendario("fecha_control");?>					    	 
		    </td>		    
		</tr>
		
		<tr>
           <td align="right">
         	  <b> Edad gestacional:
         	</td> 
           <td >
             <input type='text' name='edad_gestacional' value='<?=$edad_gestacional;?>' size=60 align='right'></b>
           </td>
          </tr>			
		 <tr>
         <tr>
           <td align="right">
         	  <b> Peso:
         	</td> 
           <td >
             <input type='text' name='peso' value='<?=$peso;?>' size=60 align='right'></b><font color="Red">Kg (Kilogramos)</font>
           </td>
          </tr>	
          <tr>
           <td align="right">
         	  <b> Tensión Arterial	<font color="Red">		MAXIMA:</b></font>
         	</td> 
           <td >
             <input type='number' name='tension_arterial_M' value='<?=$tension_arterial_M;?>' size=10 align='right'>	<b><font color="Red">/ MINIMA</font></b> <input type='number' name='tension_arterial_m' value='<?=$tension_arterial_m;?>' size=10 align='right'></b>
			<font color="Red">Los Valores son Numeros Enteros</font>
		   </td>
          </tr>	
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='4' name='observaciones_trz2' <? if ($id_planilla) echo "readonly"?>><?=$observaciones_trz2;?></textarea>
            </td>
         </tr>

   <?if (!($id_planilla)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Planilla Trazadora II : SEGUIMIENTO DE EMBARAZO</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Planilla Trazadora 2' onclick="return control_trazadora2()"
         title="Guardar datos de la Planilla de Trazadora II">
       </td>
      </tr>
     
     <?}?>
   </td>
 </tr>
         	                            
 </table></td></tr>
 <?}?> 
 
  <?if (1){?> 
 <tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.trazadora3,2);" >
	  </td>
	  <td align="center">
	   <b>Datos de Trazadora III : EFECTIVIDAD DEL CUIDADO NEONATAL</b>
	  </td>
	</tr>
</table></td></tr>
  <tr><td><table id="trazadora3" width=90% align="center" class="bordes" style="display:none;border:thin groove">
	     <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
	    
	    <tr>
			<td align="right">
				<b>Fecha Nacimiento:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_nac name=fecha_nac value='<?=fecha($fecha_nac);?>' size=15>
		    	 <?=link_calendario("fecha_parto");?>					    	 
		    </td>		    
		</tr>
		<tr>
           <td align="right">
         	  <b> Peso:
         	</td> 
           <td >
             <input type='text' name='peso_nac_trz3' value='<?=$peso_nac_trz3;?>' size=60 align='right'></b><font color="Red">g (Gramos)</font>
           </td>
          </tr>	
          <tr>
           <td align="right">
         	  <b> Sobrevida a los 28 días:
         	</td> 
           <td >
             <input type='checkbox' name='sobrevida_trz3' value='1' align='right'></b>
           </td>
          </tr>
		 <tr>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='4' name='observaciones_trz3' <? if ($id_planilla) echo "readonly"?>><?=$observaciones_trz3;?></textarea>
            </td>
         </tr>   
      

   <?if (!($id_planilla)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Planilla Trazadora III : EFECTIVIDAD DEL CUIDADO NEONATAL</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Planilla Trazadora 3' onclick="return control_trazadora3()"
         title="Guardar datos de la Planilla">
       </td>
      </tr>
     
     <?}?>
   </td>
</tr> 

</table></td></tr>
<?}?> 

 <?if ($grupo_etareo=='Neonato' || $grupo_etareo == 'Menor de Un Año'){?>   
  <tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.trazadora4,2);" >
	  </td>
	  <td align="center">
	   <b>Datos de Trazadora IV : SEGUIMIENTO DE SALUD DEL NIÑO MENOR DE 1 AÑO</b>
	  </td>
	</tr>
</table></td></tr>

<tr><td><table id="trazadora4" width=90% align="center" class="bordes" style="display:none;border:thin groove">
	    
	     <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
         
	    <tr>
			<td align="right">
				<b>Fecha de nacimiento:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_nac name=fecha_nac value='<?=fecha($fecha_nac);?>' size=15>
		    	 <?=link_calendario("fecha_nac");?>					    	 
		    </td>		    
		</tr>
		<tr>
			<td align="right">
				<b>Fecha del Control:</b>
			</td>
		    <td align="left">
		    	<?$fecha_control_trz4=date("d/m/Y");?>
		    	 <input type=text id=fecha_control_trz4 name=fecha_control_trz4 value='<?=$fecha_control_trz4;?>' size=15>
		    	 <?=link_calendario("fecha_control_trz4");?>					    	 
		    </td>		    
		</tr>		
		<tr>
           <td align="right">
         	  <b> Peso:
         	</td> 
           <td >
             <input type='text' name='peso_trz4' value='<?=$peso_trz4;?>' size=60 align='right'></b><font color="Red">Kg (Kilogramos)</font>
           </td>
          </tr>	
          <tr>
           <td align="right">
         	  <b> Talla:
         	</td> 
           <td >
             <input type='text' name='talla_trz4' value='<?=$talla_trz4;?>' size=60 align='right'></b><font color="Red">Cm (Centimetros)</font>
           </td>
          </tr>	
          <tr>
           <td align="right">
         	  <b> Perímetro cefálico:
         	</td> 
           <td >
             <input type='text' name='perimetro_cefalico_trz4' value='<?=$perimetro_cefalico_trz4;?>' size=60 align='right'></b><font color="Red">Cm (Centimetros)</font>
           </td>
          </tr>	
          <tr>
           <td align="right">
         	  <b> Percentilo de Peso/Edad:
         	</td> 
                   
           <td align="left">			 	
						 <select name=percentilo_peso_edad_trz4 Style="width=170px" <?if ($id_planilla) echo "disabled"?>>
						  <option value=-1>Seleccione</option>
						  <option value=1 <?if ($percentilo_peso_edad_trz4=='1') echo "selected"?>> <3 </option>
						  <option value=2 <?if ($percentilo_peso_edad_trz4=='2') echo "selected"?>> 3-10 </option>
						  <option value=3 <?if ($percentilo_peso_edad_trz4=='3') echo "selected"?>> >10-90 </option>
						  <option value=4 <?if ($percentilo_peso_edad_trz4=='4') echo "selected"?>> >90-97 </option>
						  <option value=5 <?if ($percentilo_peso_edad_trz4=='5') echo "selected"?>> >97 </option>
						  <option value='' <?if ($percentilo_peso_edad_trz4=='') echo "selected"?>>Dato Sin Ingresar</option>			  
						 </select><font color="Red">No Obligatorio</font>
					</td>
           
           </tr>	
          <tr>
           <td align="right">
         	  <b> Percentilo Talla/Edad:
         	</td> 
            <td align="left">			 	
						 <select name=percentilo_talla_edad_trz4 Style="width=170px" <?if ($id_planilla) echo "disabled"?>>
						  <option value=-1>Seleccione</option>
						  <option value=1 <?if ($percentilo_talla_edad_trz4=='1') echo "selected"?>>-3</option>
						  <option value=2 <?if ($percentilo_talla_edad_trz4=='2') echo "selected"?>>3-97</option>
						  <option value=3 <?if ($percentilo_talla_edad_trz4=='3') echo "selected"?>>+97</option>
						  <option value='' <?if ($percentilo_talla_edad_trz4=='') echo "selected"?>>Dato Sin Ingresar</option>			  
						 </select><font color="Red">No Obligatorio</font>
					</td>
          </tr>	
          <tr>
           <td align="right">
         	  <b> Percentilo Perímetro Cefálico/Edad:
         	</td> 
            <td align="left">			 	
									 <select name=percentilo_per_cefalico_edad_trz4 Style="width=170px" <?if ($id_planilla) echo "disabled"?>>
									  <option value=-1>Seleccione</option>
									  <option value=1 <?if ($percentilo_per_cefalico_edad_trz4=='1') echo "selected"?>>-3</option>
									  <option value=2 <?if ($percentilo_per_cefalico_edad_trz4=='2') echo "selected"?>>3-97</option>
									  <option value=3 <?if ($percentilo_per_cefalico_edad_trz4=='3') echo "selected"?>>+97</option>
									  <option value='' <?if ($percentilo_per_cefalico_edad_trz4=='') echo "selected"?>>Dato Sin Ingresar</option>			  
									 </select><font color="Red">No Obligatorio</font>
					</td>
          </tr>	
          <tr>
           <td align="right">
         	  <b> Percentilo Peso/Talla:
         	</td> 
            <td align="left">			 	
						 <select name=percentilo_peso_talla_trz4 Style="width=170px" <?if ($id_planilla) echo "disabled"?>>
						  <option value=-1>Seleccione</option>
						  <option value=1 <?if ($percentilo_peso_talla_trz4=='1') echo "selected"?>> <3 </option>
						  <option value=2 <?if ($percentilo_peso_talla_trz4=='2') echo "selected"?>> 3-10 </option>
						  <option value=3 <?if ($percentilo_peso_talla_trz4=='3') echo "selected"?>> >10-85 </option>
						  <option value=4 <?if ($percentilo_peso_talla_trz4=='4') echo "selected"?>> >85-97 </option>
						  <option value=5 <?if ($percentilo_peso_talla_trz4=='5') echo "selected"?>> >97 </option>
						  <option value='' <?if ($percentilo_peso_talla_trz4=='') echo "selected"?>>Dato Sin Ingresar</option>			  
						 </select><font color="Red">No Obligatorio</font>
						</td>
          </tr>	
		 <tr>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='4' name='observaciones_trz4' <? if ($id_planilla) echo "readonly"?>><?=$observaciones_trz4;?></textarea>
            </td>
         </tr>   
      

   <?if (!($id_planilla)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Planilla Trazadora IV : SEGUIMIENTO DE SALUD DEL NIÑO MENOR DE 1 AÑO</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Planilla Trazadora 4' onclick="return control_trazadora4()"
         title="Guardar datos de la Planilla">
       </td>
      </tr>
     
     <?}?>
   </td>
  </tr> 
  
  </table></td></tr> 
  <?}?> 
  
   <?if ($grupo_etareo=='Neonato' || $grupo_etareo == 'Menor de Un Año'){?>  
  <tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.trazadora6,2);" >
	  </td>
	  <td align="center">
	   <b>Datos de Trazadora VI : CAPACIDAD DE DETECCIÓN DE CASOS DE CARDIOPATÍA CONGÉNITA EN EL MENOR DE 1 AÑO</b>
	  </td>
	</tr>
</table></td></tr>
  <tr><td><table id="trazadora6" width=90% align="center" class="bordes" style="display:none;border:thin groove">
	    
	     <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
	    
	    <tr>
			<td align="right">
				<b>Fecha de nacimiento:</b>
			</td>
		    <td align="left">
		    	
		    	 <input type=text id=fecha_nac name=fecha_nac value='<?=fecha($fecha_nac);?>' size=15>
		    	 <?=link_calendario("fecha_nac");?>					    	 
		    </td>		    
		</tr>
		<tr>
           <td align="right">
				<b>Fecha de Diagnostico:</b>
			</td>
		    <td align="left">
		    	<input type=text id=fecha_diagnostico_trz6 name=fecha_diagnostico_trz6 value='<?=fecha($fecha_diagnostico_trz6);?>' size=15>
		    	 <?=link_calendario("fecha_diagnostico_trz6");?>					    	 
		    </td>		    
		</tr>
		<tr>
			<td align="right">
				<b>Fecha de Denuncia:</b>
			</td>
		    <td align="left">
		    	 <input type=text id=fecha_denuncia_trz6 name=fecha_denuncia_trz6 value='<?=fecha($fecha_denuncia_trz6);?>' size=15>
		    	 <?=link_calendario("fecha_denuncia_trz6");?>					    	 
		    </td>		    
		</tr>
		<tr>
           <td align="right">
         	  <b> Cardiopatía detectada:
         	</td> 
           <td >
             <input type='text' name='cardiopatia_detectada_trz6' value='<?=$cardiopatia_detectada_trz6;?>' size=60 align='right'></b><font color="Red">Valor mayor de uno(1) y menor de 162</font>
           </td>
          </tr>			
		 <tr>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='4' name='observaciones_trz6' <? if ($id_planilla) echo "readonly"?>><?=$observaciones_trz6;?></textarea>
            </td>
         </tr>   
      

   <?if (!($id_planilla)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Planilla Trazadora VI : CAPACIDAD DE DETECCIÓN DE CASOS DE CARDIOPATÍA CONGÉNITA EN EL MENOR DE 1 AÑO</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Planilla Trazadora 6' onclick="return control_trazadora6()"
         title="Guardar datos de la Planilla">
       </td>
      </tr>
     
     <?}?>
   </td>
</tr>  


  </table></td></tr>
  <?}?>
 
  <?if ($grupo_etareo=='Uno a Nueve Años'){?>  
  <tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.trazadora7,2);" >
	  </td>
	  <td align="center">
	   <b>Datos de Trazadora VII : SEGUIMIENTO DE SALUD DEL NIÑO DE 1 A 9 AÑOS</b>
	  </td>
	</tr>
</table></td></tr>
  <tr><td><table id="trazadora7" width=90% align="center" class="bordes" style="display:none;border:thin groove">
	     <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
	    
	    <tr>
			<td align="right">
				<b>Fecha de nacimiento:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_nac name=fecha_nac value='<?=fecha($fecha_nac);?>' size=15>
		    	 <?=link_calendario("fecha_nac");?>					    	 
		    </td>		    
		</tr>
		<tr>
			<td align="right">
				<b>Fecha del Control:</b>
			</td>
		    <td align="left">
		    	<?$fecha_control_trz7=date("d/m/Y");?>
		    	 <input type=text id=fecha_control_trz7 name=fecha_control_trz7 value='<?=$fecha_control_trz7;?>' size=15>
		    	 <?=link_calendario("fecha_control_trz7");?>					    	 
		    </td>		    
		</tr>		
		 <tr>
           <td align="right">
         	  <b> Peso:
         	</td> 
           <td >
             <input type='text' name='peso_trz7' value='<?=$peso_trz7;?>' size=60 align='right'></b><font color="Red">Kg (Kilogramos)</font>
           </td>
          </tr>	
          <tr>
           <td align="right">
         	  <b> Talla:
         	</td> 
           <td >
             <input type='text' name='talla_trz7' value='<?=$talla_trz7;?>' size=60 align='right'></b><font color="Red">Cm (centimetro)</font>
           </td>
          </tr>	
          <tr>
           <td align="right">
         	  <b> Tensión Arterial	<font color="Red">		MAXIMA:</b></font>
         	</td> 
           <td >
             <input type='text' name='tension_arterial_trz7_M' value='<?=$tension_arterial_trz7_M;?>' size=10 align='right'>	<b><font color="Red">/ MINIMA</font></b> <input type='text' name='tension_arterial_trz7_m' value='<?=$tension_arterial_trz7_m;?>' size=10 align='right'></b>
			<font color="Red">Los Valores son Numeros Enteros</font>
		   </td>
          </tr>
          <tr>
           <td align="right">
         	  <b> Percentilo de Peso/Edad:
         	</td> 
            <td align="left">			 	
						 <select name=percentilo_peso_edad_trz7 Style="width=170px" <?if ($id_planilla) echo "disabled"?>>
						  <option value=-1>Seleccione</option>
						  <option value=1 <?if ($percentilo_peso_edad_trz7=='1') echo "selected"?>> <3 </option>
						  <option value=2 <?if ($percentilo_peso_edad_trz7=='2') echo "selected"?>> 3-10 </option>
						  <option value=3 <?if ($percentilo_peso_edad_trz7=='3') echo "selected"?>> >10-90 </option>
						  <option value=4 <?if ($percentilo_peso_edad_trz7=='4') echo "selected"?>> >90-97 </option>
						  <option value=5 <?if ($percentilo_peso_edad_trz7=='5') echo "selected"?>> >97 </option>
						  <option value='' <?if ($percentilo_peso_edad_trz7=='') echo "selected"?>>Dato Sin Ingresar</option>			  
						 </select><font color="Red">No Obligatorio</font>
					</td>
          </tr>	
          <tr>
           <td align="right">
         	  <b> Percentilo Talla/Edad:
         	</td> 
            <td align="left">			 	
						 <select name=percentilo_talla_edad_trz7 Style="width=170px" <?if ($id_planilla) echo "disabled"?>>
						  <option value=-1>Seleccione</option>
						  <option value=1 <?if ($percentilo_talla_edad_trz7=='1') echo "selected"?>>-3</option>
						  <option value=2 <?if ($percentilo_talla_edad_trz7=='2') echo "selected"?>>3-97</option>
						  <option value=3 <?if ($percentilo_talla_edad_trz7=='3') echo "selected"?>>+97</option>
						  <option value='' <?if ($percentilo_talla_edad_trz7=='') echo "selected"?>>Dato Sin Ingresar</option>			  
						 </select><font color="Red">No Obligatorio</font>
					</td>
          </tr>	
          <tr>
           <td align="right">
         	  <b> Percentilo Peso/Talla:
         	</td> 
            <td align="left">			 	
						 <select name=percentilo_peso_talla_trz7 Style="width=170px" <?if ($id_planilla) echo "disabled"?>>
						  <option value=-1>Seleccione</option>
						  <option value=1 <?if ($percentilo_peso_talla_trz7=='1') echo "selected"?>> <3 </option>
						  <option value=2 <?if ($percentilo_peso_talla_trz7=='2') echo "selected"?>> 3-10 </option>
						  <option value=3 <?if ($percentilo_peso_talla_trz7=='3') echo "selected"?>> >10-85 </option>
						  <option value=4 <?if ($percentilo_peso_talla_trz7=='4') echo "selected"?>> >85-97 </option>
						  <option value=5 <?if ($percentilo_peso_talla_trz7=='5') echo "selected"?>> >97 </option>
						  <option value='' <?if ($percentilo_peso_talla_trz7=='') echo "selected"?>>Dato Sin Ingresar</option>			  
						 </select><font color="Red">No Obligatorio</font>
						</td>
          </tr>
		 <tr>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='4' name='observaciones_trz7' <? if ($id_planilla) echo "readonly"?>><?=$observaciones_trz7;?></textarea>
            </td>
         </tr> 
      

   <?if (!($id_planilla)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Planilla Trazadora VII : SEGUIMIENTO DE SALUD DEL NIÑO DE 1 A 9 AÑOS</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Planilla Trazadora 7' onclick="return control_trazadora7()"
         title="Guardar datos de la Planilla">
       </td>
      </tr>
     
     <?}?>
   </td>
</tr> 
</table></td></tr>
<?}?>

 <?if ($grupo_etareo=='Neonato' || $grupo_etareo=='Menor de Un Año' || $grupo_etareo=='24 Meses'){?>  
  <tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.trazadora8,2);" >
	  </td>
	  <td align="center">
	   <b>Datos de Trazadora VIII : COBERTURA DE INMUNIZACIONES A LOS 24 MESES</b>
	  </td>
	</tr>
</table></td></tr>
  <tr><td><table id="trazadora8" width=90% align="center" class="bordes" style="display:none;border:thin groove">
	     <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
	    
	    <tr>
			<td align="right">
				<b>Fecha de nacimiento:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_nac name=fecha_nac value='<?=fecha($fecha_nac);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_nac");?>					    	 
		    </td>		    
		</tr>
		<tr>
			<td align="right">
				<b>Fecha de vacunación Cuádruple Bacteriana:</b>
			</td>
		    <td align="left">
		    	<?$fecha_vac_cua_bact_trz8=date("d/m/Y");?>
		    	 <input type=text id=fecha_vac_cua_bact_trz8 name=fecha_vac_cua_bact_trz8 value='<?=$fecha_vac_cua_bact_trz8;?>' size=15>
		    	 <?=link_calendario("fecha_vac_cua_bact_trz8");?>					    	 
		    </td>		    
		</tr>
		<tr>
			<td align="right">
				<b>Fecha de vacunación Antipoliomielítica:</b>
			</td>
		    <td align="left">
		    	<?$fecha_vac_antipolio_trz8=date("d/m/Y");?>
		    	 <input type=text id=fecha_vac_antipolio_trz8 name=fecha_vac_antipolio_trz8 value='<?=$fecha_vac_antipolio_trz8;?>' size=15>
		    	 <?=link_calendario("fecha_vac_antipolio_trz8");?>					    	 
		    </td>		    
		</tr>		
		 <tr>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='4' name='observaciones_trz8' <? if ($id_planilla) echo "readonly"?>><?=$observaciones_trz8;?></textarea>
            </td>
         </tr> 
      

   <?if (!($id_planilla)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Planilla Trazadora VIII : COBERTURA DE INMUNIZACIONES A LOS 24 MESES</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Planilla Trazadora 8' onclick="return control_trazadora8()"
         title="Guardar datos de la Planilla">
       </td>
      </tr>
     
     <?}?>
   </td>
</tr> 
</table></td></tr>
<?}?>

 <?if ($grupo_etareo=='Siete Años'){?>  
  <tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.trazadora9,2);" >
	  </td>
	  <td align="center">
	   <b>Datos de Trazadora IX : COBERTURA DE INMUNIZACIONES A LOS 7 AÑOS</b>
	  </td>
	</tr>
</table></td></tr>
  <tr><td><table id="trazadora9" width=90% align="center" class="bordes" style="display:none;border:thin groove">
	    
	     <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
	    
	    <tr>
			<td align="right">
				<b>Fecha de nacimiento:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_nac name=fecha_nac value='<?=fecha($fecha_nac);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_nac");?>					    	 
		    </td>		    
		</tr>
		<tr>
			<td align="right">
				<b>Fecha de aplicación de vacuna triple bacteriana:</b>
			</td>
		    <td align="left">
		    	<?$fecha_vac_tri_bac_trz9=date("d/m/Y");?>
		    	 <input type=text id=fecha_vac_tri_bac_trz9 name=fecha_vac_tri_bac_trz9 value='<?=$fecha_vac_tri_bac_trz9;?>' size=15>
		    	 <?=link_calendario("fecha_vac_tri_bac_trz9");?>					    	 
		    </td>		    
		</tr>
		<tr>
			<td align="right">
				<b>Fecha de aplicación de vacuna triple viral:</b>
			</td>
		    <td align="left">
		    	<?$fecha_vac_tri_vir_trz9=date("d/m/Y");?>
		    	 <input type=text id=fecha_vac_tri_vir_trz9 name=fecha_vac_tri_vir_trz9 value='<?=$fecha_vac_tri_vir_trz9;?>' size=15>
		    	 <?=link_calendario("fecha_vac_tri_vir_trz9");?>					    	 
		    </td>		    
		</tr>
		<tr>
			<td align="right">
				<b>Fecha de aplicación de vacuna antipoliomielítica:</b>
			</td>
		    <td align="left">
		    	<?$fecha_vac_antipolio_trz9=date("d/m/Y");?>
		    	 <input type=text id=fecha_vac_antipolio_trz9 name=fecha_vac_antipolio_trz9 value='<?=$fecha_vac_antipolio_trz9;?>' size=15>
		    	 <?=link_calendario("fecha_vac_antipolio_trz9");?>					    	 
		    </td>		    
		</tr>
				
		 <tr>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='4' name='observaciones_trz9' <? if ($id_planilla) echo "readonly"?>><?=$observaciones_trz9;?></textarea>
            </td>
         </tr> 
      

   <?if (!($id_planilla)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Planilla Trazadora IX : COBERTURA DE INMUNIZACIONES A LOS 7 AÑOS</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Planilla Trazadora 9' onclick="return control_trazadora9()"
         title="Guardar datos de la Planilla">
       </td>
      </tr>
     
     <?}?>
   </td>
</tr> 
</table></td></tr>
<?}?>

  <?if ($grupo_etareo=='Adolecente'){?> 
  <tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.trazadora10,2);" >
	  </td>
	  <td align="center">
	   <b>Datos de Trazadora X : SEGUIMIENTO DE SALUD DEL ADOLESCENTE DE 10 A 19 AÑOS</b>
	  </td>
	</tr>
</table></td></tr>


  <tr><td><table id="trazadora10" width=90% align="center" class="bordes" style="display:none;border:thin groove">
	    <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
	   
	   <tr>
			<td align="right">
				<b>Fecha de nacimiento:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_nac name=fecha_nac value='<?=fecha($fecha_nac);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_nac");?>					    	 
		    </td>		    
		</tr>
		<tr>
			<td align="right">
				<b>Fecha del control:</b>
			</td>
		    <td align="left">
		    	<?$fecha_control_trz10=date("d/m/Y");?>
		    	 <input type=text id=fecha_control_trz10 name=fecha_control_trz10 value='<?=$fecha_control_trz10;?>' size=15>
		    	 <?=link_calendario("fecha_control_trz10");?>					    	 
		    </td>		    
		</tr>		
		 <tr>
           <td align="right">
         	  <b> Talla:
         	</td> 
           <td >
             <input type='text' name='talla_trz10' value='<?=$talla_trz10;?>' size=60 align='right'></b><font color="Red">Cm (Centimetros)</font>
           </td>
          </tr>	
          <tr>
           <td align="right">
         	  <b> Peso:
         	</td> 
           <td >
             <input type='text' name='peso_trz10' value='<?=$peso_trz10;?>' size=60 align='right'></b><font color="Red">Kg (Kilogramos)</font>
           </td>
          </tr>	
		 <tr>
           <td align="right">
         	  <b> Tensión Arterial	<font color="Red">		MAXIMA:</b></font>
         	</td> 
           <td >
             <input type='text' name='tension_arterial_trz10_M' value='<?=$tension_arterial_trz10_M;?>' size=10 align='right'>	<b><font color="Red">/ MINIMA</font></b> <input type='text' name='tension_arterial_trz10_m' value='<?=$tension_arterial_trz10_m;?>' size=10 align='right'></b>
			<font color="Red">Los Valores son Numeros Enteros</font>
		   </td>
          </tr>
		<tr>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='4' name='observaciones_trz10' <? if ($id_planilla) echo "readonly"?>><?=$observaciones_trz10;?></textarea>
            </td>
         </tr> 
      

   <?if (!($id_planilla)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Planilla Trazadora X : SEGUIMIENTO DE SALUD DEL ADOLESCENTE DE 10 A 19 AÑOS</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Planilla Trazadora 10' onclick="return control_trazadora10()"
         title="Guardar datos de la Planilla">
       </td>
      </tr>
     
     <?}?>
   </td>
</tr> 
</table></td></tr>
 
 <?}?>     	                            

<?if ($grupo_etareo=='Adolecente' || $grupo_etareo == 'Adulto'){?>
 <tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.trazadora11,2);" >
	  </td>
	  <td align="center">
	   <b>Datos de Trazadora XI : PROMOCION DEL DERECHO AL CUIDADO SEXUAL Y REPRODUCTIVO ENTRE LOS 14 Y LOS 25 AÑOS</b>
	  </td>
	</tr>
</table></td></tr>
  <tr><td><table id="trazadora11" width=90% align="center" class="bordes" style="display:none;border:thin groove">
	     <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
	    
	    <tr>
   		<tr>
			<td align="right">
				<b>Fecha de Nacimiento:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_nac name=fecha_nac
		    	  value='<?=fecha($fecha_nac);?>' size=15>
		    	 <?=link_calendario("fecha_nac");?>					    	 
		    </td>		    
		</tr>
		<tr>
   		<tr>
			<td align="right">
				<b>Fecha de Asistencia al Taller:</b>
			</td>
		    <td align="left">
		    	<?$fecha_taller=date("d/m/Y");?>
		    	 <input type=text id=fecha_taller name=fecha_taller
		    	  value='<?=$fecha_taller;?>' size=15>
		    	 <?=link_calendario("fecha_taller");?>					    	 
		    </td>		    
		</tr>	
		 <tr>
           <td align="right">
         	  <b> Tema del Taller:</b>
         	</td> 
           <td align="left">			 	
			 <select name=tema_taller Style="width=600px">
        		<option value=-1>Seleccione</option>
        		<option value="T001">T001 - Encuentro para Promocion de Salud Sexual y Reproductiva, Conductas Saludables, Habitos de Higiene</option>
        		<option value="T007">T007 - Taller de Prevencion de VIH e ITS</option>
        		<option value="T008">T008 - Taller de Prevencion de Violencia de Genero</option>
        		<option value="T013">T013 - Taller de Promocion de Salud Sexual y Reproductiva</option>
        		<option value="T014">T014 - Taller de Salud Sexual, Confidencialidad, Genero y Derecho, en Sala de Espera</option>			 
			</select>
			</td>
          </tr>
		 
		 <tr>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='4' name='observaciones_trz11' <? if ($id_planilla) echo "readonly"?>><?=$observaciones_trz_11;?></textarea>
            </td>
         </tr>  
      
  
   <?if (!($id_planilla)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Planilla Trazadora XI : PROMOCION DEL DERECHO AL CUIDADO SEXUAL Y REPRODUCTIVO ENTRE LOS 14 Y LOS 25 AÑOS</b>
  		</td>
  	</tr>  
      
      
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Planilla Trazadora 11' onclick="return control_trazadora11()"
         title="Guardar datos de la Planilla para la Trazadora XI">
       </td>
      </tr>
     
     <?}?>
    
</table></td></tr>
 <?}?> 
 
 <?if (($grupo_etareo=='Adolecente' || $grupo_etareo == 'Adulto') && (trim($sexo)=='F')){?>
 <tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.trazadora12,2);" >
	  </td>
	  <td align="center">
	   <b>Datos de Trazadora XII : PREVENCION DEL CANCER CERVICO-UTERINO</b>
	  </td>
	</tr>
</table></td></tr>
  <tr><td><table id="trazadora12" width=90% align="center" class="bordes" style="display:none;border:thin groove">
	     <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
	    
	    <tr>
   		<tr>
			<td align="right">
				<b>Fecha de Nacimiento:</b>
			</td>
		    <td align="left">
		    	
		    	 <input type=text id=fecha_nac name=fecha_nac
		    	  value='<?=fecha($fecha_nac);?>' size=15>
		    	 <?=link_calendario("fecha_nac");?>					    	 
		    </td>		    
		</tr>
		<tr>
   		<tr>
			<td align="right">
				<b>Fecha de Diagnostico:</b>
			</td>
		    <td align="left">
		    	<input type=text id=fecha_diagnostico_trz12 name=fecha_diagnostico_trz12 
		    	 value='<?=fecha($fecha_diagnostico_trz12);?>' size=15>
		    	 <?=link_calendario("fecha_diagnostico_trz12");?>					    	 
		    </td>		    
		</tr>	
		 <tr>
           <td align="right">
         	  <b> Diagnostico:</b>
         	</td> 
           <td align="left">			 	
			 <select name=diag Style="width=160px">
        		<option value=-1>Seleccione</option>
        		<option value=1>H-SIL</option>
        		<option value=2>CIN 2</option>
        		<option value=3>CIN 3</option>
        		<option value=4>Carcinoma in situ</option>
        		<option value=5>Cancer Cervico-uterino</option>			 
			</select>
			</td>
          </tr>
		  <tr>
			<td align="right">
				<b>Fecha de Inicio de Tratamiento:</b>
			</td>
		    <td align="left">
		    	
		    	 <input type=text id=fecha_inic_tratamiento name=fecha_inic_tratamiento
		    	  value='<?=$fecha_inic_tratamiento;?>' size=15>
		    	 <?=link_calendario("fecha_inic_tratamiento");?>					    	 
		    <font color="Red">No Obligatorio</font>	</td>    
		</tr>
		  <tr>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='4' name='observaciones_trz12' <? if ($id_planilla) echo "readonly"?>><?=$observaciones_trz12;?></textarea>
            </td>
         </tr>  
      
  
   <?if (!($id_planilla)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Planilla Trazadora XII : PREVENCION DEL CANCER CERVICO-UTERINO</b>
  		</td>
  	</tr>  
      
      
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Planilla Trazadora 12' onclick="return control_trazadora12()"
         title="Guardar datos de la Planilla para la Trazadora XII">
       </td>
      </tr>
     
     <?}?>
    
</table></td></tr>
 <?}?>          

 <?if (($grupo_etareo=='Adolecente' || $grupo_etareo == 'Adulto') && (trim($sexo)=='F')){?>
 <tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.trazadora13,2);" >
	  </td>
	  <td align="center">
	   <b>Datos de Trazadora XIII : DIAGNOSTICO DEL CANCER DE MAMA</b>
	  </td>
	</tr>
</table></td></tr>
  <tr><td><table id="trazadora13" width=90% align="center" class="bordes" style="display:none;border:thin groove">
	     <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
	    
	    <tr>
   		<tr>
			<td align="right">
				<b>Fecha de Nacimiento:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_nac name=fecha_nac
		    	  value='<?=fecha($fecha_nac);?>' size=15>
		    	 <?=link_calendario("fecha_nac");?>					    	 
		    </td>		    
		</tr>
		<tr>
			<td align="right">
				<b>Fecha de Diagnostico Histologico:</b>
			</td>
		    <td align="left">
		    	<?$fecha_diagnostico_tzr13=date("d/m/Y");?>
		    	 <input type=text id=fecha_diagnostico_tzr13 name=fecha_diagnostico_tzr13
		    	  value='<?=$fecha_diagnostico_tzr13;?>' size=15>
		    	 <?=link_calendario("fecha_diagnostico_tzr13");?>					    	 
		    </td>		    
		</tr>	
		  <tr>
			<td align="right">
				<b>Fecha de Inicio de Tratamiento:</b>
			</td>
		    <td align="left">
		    	<?$fecha_inic_tratamiento_trz13=date("d/m/Y");?>
		    	 <input type=text id=fecha_inic_tratamiento_trz13 name=fecha_inic_tratamiento_trz13
		    	  value='<?=$fecha_inic_tratamiento_trz13;?>' size=15>
		    	 <?=link_calendario("fecha_inic_tratamiento_trz13");?>	<font color="Red">No Obligatorio</font>				    	 
		    </td>		    
		</tr>
		  <tr>
           <td align="right">
         	  <b> Carcinona:
         	</td> 
           <td align="left">			 	
			 <select name=carcinoma Style="width=160px">
        		<option value=-1>Seleccione</option>
        		<option value="1">Carcinoma in Situ</option>
        		<option value="2">Carcinoma Invasor</option>
        	</select>
			</td>
          </tr>
          <tr>
           <td align="right">
         	  <b> Tamaño:
         	</td> 
           <td align="left">			 	
			 <select name=tamanio_trz13 Style="width=160px">
        		<option value=-1>Seleccione</option>
        		<option value="T0">T0</option>
        		<option value="T1">T1</option>
        		<option value="T2">T2</option>
        		<option value="T3">T3</option>
        		<option value="T4">T4</option>			 
			</select>
			</td>
          </tr>
          <tr>
           <td align="right">
         	  <b> Ganglios Linfaticos:
         	</td> 
           <td align="left">			 	
			 <select name=ganglios Style="width=160px">
        		<option value=-1>Seleccione</option>
        		<option value="N0">N0</option>
        		<option value="N1">N1</option>
        		<option value="N2">N2</option>
        	</select>
			</td>
          </tr>
          <tr>
           <td align="right">
         	  <b> Metastasis:
         	</td> 
           <td align="left">			 	
			 <select name=metastasis Style="width=160px">
        		<option value=-1>Seleccione</option>
        		<option value="M0">M0</option>
        		<option value="M1">M1</option>
        	</select>
			</td>
          </tr>
         <tr>
           <td align="right">
         	  <b> Estadio:
         	</td> 
           <td align="left">			 	
			 <select name=estadio Style="width=160px">
        		<option value=-1>Seleccione</option>
        		<option value="I">I</option>
        		<option value="IIA">IIA</option>
        		<option value="IIB">CIN 3</option>
        		<option value="IIIA">IIB</option>
        		<option value="IIIB">IIIB</option>
        		<option value="IIIC">IIIC</option>
        		<option value="IV">IV</option>			 
			</select>
			</td>
          </tr>
		  <tr>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='4' name='observaciones_trz13' <? if ($id_planilla) echo "readonly"?>><?=$observaciones_trz13;?></textarea>
            </td>
         </tr>  
      
  
   <?if (!($id_planilla)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Planilla Trazadora XIII : DIAGNOSTICO DEL CANCER DE MAMA</b>
  		</td>
  	</tr>  
      
      
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Planilla Trazadora 13' onclick="return control_trazadora13()"
         title="Guardar datos de la Planilla para la Trazadora XIII">
       </td>
      </tr>
     
     <?}?>
    
</table></td></tr>
 <?}?> 
 
  <?if (1){?>
 <tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.trazadora14,2);" >
	  </td>
	  <td align="center">
	   <b>Datos de Trazadora XIV : EVALUACION DEL PROCESO DE ATENCION DE MUERTES INFANTILES Y MATERNAS</b>
	  </td>
	</tr>
</table></td></tr>
  <tr><td><table id="trazadora14" width=90% align="center" class="bordes" style="display:none;border:thin groove">
	     <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
	     <tr>
   		<tr>
			<td align="right">
				<b>Fecha de Nacimiento:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_nac name=fecha_nac
		    	  value='<?=fecha($fecha_nac);?>' size=15>
		    	 <?=link_calendario("fecha_nac");?>					    	 
		    </td>		    
		</tr>
	    <tr>
   		<tr>
			<td align="right">
				<b>Fecha de Defuncion:</b>
			</td>
		    <td align="left">
		    	<?$fecha_def=date("d/m/Y");?>
		    	 <input type=text id=fecha_def name=fecha_def
		    	  value='<?=$fecha_def;?>' size=15>
		    	 <?=link_calendario("fecha_def");?>					    	 
		    </td>		    
		</tr>
		<tr>
			<td align="right">
				<b>Fecha de Auditoria de Muerte:</b>
			</td>
		    <td align="left">
		    	<?$fecha_audit_muerte=date("d/m/Y");?>
		    	 <input type=text id=fecha_audit_muerte name=fecha_audit_muerte
		    	  value='<?=$fecha_audit_muerte;?>' size=15>
		    	 <?=link_calendario("fecha_audit_muerte");?>					    	 
		    </td>		    
		</tr>
		<tr>
			<td align="right">
				<b>Fecha de Parto O Interrupcion del Embarazo:</b>
			</td>
		    <td align="left">
		    	<?$fecha_parto_o_int_embarazo=date("d/m/Y");?>
		    	 <input type=text id=fecha_parto_o_int_embarazo name=fecha_parto_o_int_embarazo
		    	  value='<?=$fecha_parto_o_int_embarazo;?>' size=15>
		    	 <?=link_calendario("fecha_parto_o_int_embarazo");?>					    	 
		    </td>		    
		</tr>	
		   <tr>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='4' name='observaciones_trz14' <? if ($id_planilla) echo "readonly"?>><?=$observaciones_trz14;?></textarea>
            </td>
         </tr>  
      
  
   <?if (!($id_planilla)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Planilla Trazadora XIV : EVALUACION DEL PROCESO DE ATENCION DE MUERTES INFANTILES Y MATERNAS</b>
  		</td>
  	</tr>  
      
      
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Planilla Trazadora 14' onclick="return control_trazadora14()"
         title="Guardar datos de la Planilla para la Trazadora XIV">
       </td>
      </tr>
     
     <?}?>
    
</table></td></tr>
 <?}?> 
<br>                                                           
<?if ($id_planilla){/*?>
<table class="bordes" align="center" width="100%">
		 <tr align="center" id="sub_tabla">
		 	<td>	
		 		Editar DATO
		 	</td>
		 </tr>
		 
		 <tr>
		    <td align="center">
		      <input type=button name="editar" value="Editar" onclick="editar_campos()" title="Edita Campos" style="width=130px"> &nbsp;&nbsp;
		      <input type="submit" name="guardar_editar" value="Guardar" title="Guarda Muleto" disabled style="width=130px" onclick="return control_nuevos()">&nbsp;&nbsp;
		      <input type="button" name="cancelar_editar" value="Cancelar" title="Cancela Edicion de Muletos" disabled style="width=130px" onclick="document.location.reload()">		      
		      <?if (permisos_check("inicio","permiso_borrar")) $permiso="";
			  else $permiso="disabled";?>
		      <input type="submit" name="borrar" value="Borrar" style="width=130px" <?=$permiso?>>
		    </td>
		 </tr> 
	 </table>	
	 <br>
	 <?*/}?>
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='../entrega_leche/listado_beneficiarios_leche.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
  <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
   	<font color="Black" size="3"> <b>En esta pantalla se registran todas las trazadoras en su respectivo formulario</b></font>
   </td>
  </tr>
  </table></td></tr>
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>