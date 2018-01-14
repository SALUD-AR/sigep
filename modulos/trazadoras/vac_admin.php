<?
/*
Author: Ferni

modificada por
$Author: Gaby $
$Revision: 1.42 $
$Date: 2011/02/24 15:50:00 $
*/

require_once ("../../config.php");

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

if ($_POST['guardar']=="Guardar"){   
   $db->StartTrans();         
     $fecha_vac=Fecha_db($fecha_vac);
     $fecha_vac_prox=Fecha_db($fecha_vac_prox);
     $fecha_carga=date("Y-m-d H:i:s");     
	 
	 if ($entidad_alta=='nu'){		
		$id_beneficiarios=$id; $id_smiafiliados=0;
		$query_01= "select * from
						trazadoras.vacunas
						where id_beneficiarios='$id_beneficiarios' and fecha_vac='$fecha_vac' and id_vac_apli='$id_vac_apli' and id_dosis_apli='$id_dosis_apli' and eliminada='0'";
		$update_f="update fichero.fichero set fecha_pcontrol_flag='0' where id_beneficiarios='$id'";
	}//carga de prestacion a paciente NO PLAN NACER
	 
	 if ($entidad_alta=='na'){
		$id_beneficiarios=0; $id_smiafiliados=$id;
		$query_01= "select * from
						trazadoras.vacunas
						where id_smiafiliados='$id_smiafiliados' and fecha_vac='$fecha_vac' and id_vac_apli='$id_vac_apli' and id_dosis_apli='$id_dosis_apli' and eliminada='0'";
		$update_f="update fichero.fichero set fecha_pcontrol_flag='0' where id_smiafiliados='$id'";
	}//carga de prestacion a paciente PLAN NACER
 	 	 
	 $resultado=sql($query_01, "ERROR AL GENERAR CONSULTA 01") or fin_pagina();
	 sql($update_f, "No se puede actualizar los registros") or fin_pagina();	 

	 if($resultado->RecordCount()==0){

	 	$q="select nextval('trazadoras.vacunas_id_vacunas_seq') as id_planilla";
	    $id_planilla=sql($q) or fin_pagina();
	    $id_planilla=$id_planilla->fields['id_planilla'];
	       
	    $query="insert into trazadoras.vacunas
	             (id_vacunas,id_vac_apli,id_dosis_apli,fecha_vac,nom_resp,comentario,cuie, id_beneficiarios, id_smiafiliados, eliminada, email,lote,fecha_vac_prox,fecha_pcontrol_flag,estado_envio)
	             values
	             ('$id_planilla','$id_vac_apli','$id_dosis_apli','$fecha_vac','$nom_resp','$comentario','$cuie', '$id_beneficiarios', '$id_smiafiliados',0,'$email','$lote','$fecha_vac_prox','1','n')";
	
	    sql($query, "Error al insertar la Planilla") or fin_pagina();
	    
	    $accion="Registro Grabado";  
	     /*cargo los log*/ 
	    $q_1="select nextval('trazadoras.log_vacunas_id_log_vacunas_seq') as id_log";
	    $id_log=sql($q_1) or fin_pagina();
	    $id_log=$id_log->fields['id_log'];
	    
		    $usuario=$_ses_user['name'];
			$log="insert into trazadoras.log_vacunas 
				   (id_log_vacunas,id_vacunas, fecha, tipo, descripcion, usuario) 
			values ($id_log, '$id_planilla','$fecha_carga','Nuevo Registro','Nro. Registro $id_planilla', '$usuario')";
			sql($log) or fin_pagina();
			
		//alta en el sistema de facturacion
		$q_2="select * from nacer.smiafiliados where id_smiafiliados='$id_smiafiliados'";
		$res_2=sql($q_2,"no puedo ejecutar consulta");
		$activo=trim($res_2->fields['activo']);
		$clavebeneficiario=$res_2->fields['clavebeneficiario'];

		if (($activo=='S')&&( 
						(($id_vac_apli == '6')&&($id_dosis_apli=='1')) or 
						(($id_vac_apli == '6')&&($id_dosis_apli=='2')) or
						($id_vac_apli == '5') or 
						($id_vac_apli == '1') or
						($id_vac_apli == '3') or
						($id_vac_apli == '4') or
						($id_vac_apli == '7') or
						($id_vac_apli == '15') or
						($id_vac_apli == '2') or
						($id_vac_apli == '10') or
						($id_vac_apli == '11') or
						($id_vac_apli == '18') or
						($id_vac_apli == '19') or
						($id_vac_apli == '14') or
						($id_vac_apli == '8') or
						($id_vac_apli == '9') or
						($id_vac_apli == '16') or
						($id_vac_apli == '17') 
						)){
			$db->StartTrans();
			//comprobante
			$q="select nextval('comprobante_id_comprobante_seq') as id_comprobante";
		    $id_comprobante=sql($q) or fin_pagina();
		    $id_comprobante=$id_comprobante->fields['id_comprobante'];	
		    
		    $periodo= str_replace("-","/",substr($fecha_vac,0,7));
		    		    
		    $query="insert into facturacion.comprobante
		             (id_comprobante, cuie, nombre_medico, fecha_comprobante, clavebeneficiario, id_smiafiliados, fecha_carga,periodo,comentario,id_servicio,activo)
		             values
		             ($id_comprobante,'$cuie','$nom_resp','$fecha_vac','$clavebeneficiario', $id_smiafiliados,'$fecha_carga','$periodo','Desde Modulo Vacunacion','1','$activo')";	
		    sql($query, "Error al insertar el comprobante") or fin_pagina();	    
		    $usuario=$_ses_user['name'];
			$log="insert into facturacion.log_comprobante 
				   (id_comprobante, fecha, tipo, descripcion, usuario) 
			values ($id_comprobante, '$fecha_carga','Nuevo Comprobante','Nro. Comprobante $id_comprobante', '$usuario')";
			sql($log) or fin_pagina();	
			
			//prestaciones -----------------------------------------------------
			//tengo que sacar el el id_nomenclador_detalles
			$q="select * from nacer.efe_conv 
				left join facturacion.nomenclador_detalle using (id_nomenclador_detalle)
				where cuie='$cuie'";
		    $res_efector=sql($q,"Error en traer el precio del nomenclador") or fin_pagina();
			$id_nomenclador_detalle=$res_efector->fields['id_nomenclador_detalle'];
			
			//sacar codigo segun prestacion
			/*if ($id_vac_apli == '6') {
				$codigo= "NPE 41";
			} elseif ($id_vac_apli == '5') {
			    $codigo= "NPE 42";
			} elseif ($id_vac_apli == '3') {
			    $codigo= "NPE 42";
			} elseif ($id_vac_apli == '4') {
			    $codigo= "NPE 42";
			} elseif ($id_vac_apli == '7') {
			    $codigo= "NPE 42";
			} elseif ($id_vac_apli == '15') {
			    $codigo= "NPE 42";
			} elseif ($id_vac_apli == '12') {
			    $codigo= "NPE 42";
			} elseif ($id_vac_apli == '18') {
			    $codigo= "NPE 42";
			} elseif ($id_vac_apli == '19') {
			    $codigo= "NPE 42";
			} elseif ($id_vac_apli == '8') {
			    $codigo= "NPE 42";
			} */
			
			//if (($id_nomenclador_detalle > 8)&&($fecha_vac>='2012-11-01')){
				if (($id_vac_apli == '6')&&($id_dosis_apli=='1')) {
					$codigo= "V001";
					$descripcion="Dosis aplicada de vacuna triple viral en niños menores de 6 años";
				}elseif (($id_vac_apli == '6')&&($id_dosis_apli!='1')){
					$codigo= "V001";
					$descripcion="Dosis aplicada de vacuna triple viral en niños menores de 6 años";
				} elseif ($id_vac_apli == '1') {
					$codigo= "V009";
					$descripcion="Dosis aplicada de inmunización de recién nacido (BCG antes del alta y Hepatitis B en primeras 12 hs de vida)";
				} elseif ($id_vac_apli == '5') {
					$codigo= "V002";
					$descripcion="Dosis aplicada de Sabín en niños de 2, 4, 6 y 18 meses y 6 años o actualización de esquema";
				} elseif ($id_vac_apli == '3') {
					$codigo= "V003";
					$descripcion="Dosis aplicada de inmunización Pentavalente en niños de 2, 4, y 6 meses o actualización de esquema";
				} elseif ($id_vac_apli == '4') {
					$codigo= "V004";
					$descripcion="Dosis aplicada de inmunización Cuádruple en niños de 18 meses o actualización de esquema";
				} elseif ($id_vac_apli == '7') {
					$codigo= "V005";
					$descripcion="Dosis aplicada de inmunización para Hepatitis A en niños de 12 meses o actualización de esquema";
				} elseif ($id_vac_apli == '15') {
					$codigo= "V008";
					$descripcion="Dosis aplicada de dTap triple acelular (refuerzo a los 11 años)";
				} elseif ($id_vac_apli == '2') {
					$codigo= "V009";
					$descripcion="Dosis aplicada de inmunización anti hepatitis B (Actualización esquema)";
				} elseif ($id_vac_apli == '10') {
					$codigo= "V010";
					$descripcion="Dosis aplicada de Doble adultos  >16 años";				
				} elseif ($id_vac_apli == '11') {
					$codigo= "V011";
					$descripcion="Dosis aplicada de Doble viral (rubéola + sarampión)";
				} elseif ($id_vac_apli == '18') {
					$codigo= "V013";
					$descripcion="Dosis aplicada de vacuna antigripal en niños de 6 a 24 meses o en niños mayores con factores de riesgo";
				} elseif ($id_vac_apli == '19') {
					$codigo= "V013"; 
					$descripcion="Dosis aplicada de Vacuna Antigripal en personas con factores de riesgo";
				} elseif ($id_vac_apli == '14') {
					$codigo= "V014"; 
					$descripcion="Dosis aplicada de Vacuna contra VPH (Virus Papiloma Humano) en niñas de 11 años ";
				} elseif (($id_vac_apli == '8')or($id_vac_apli == '9')) { //"Triple Bacteriana Acelular"
					$codigo= "V008"; 
					$descripcion="Dosis aplicada de dTap Triple Acelular (Actualización esquema en niños mayores 7 años)";
				} elseif ($id_vac_apli == '16') { //"Neumo 23"
					$codigo= "V015"; 
					$descripcion="Dosis aplicada de vacuna neumococo conjugada";
				} elseif ($id_vac_apli == '17') { //"Neumo 13"
					$codigo= "V015"; 
					$descripcion="Dosis aplicada de vacuna neumococo conjugada";
				}
			echo $id_vac_apli;
			//}
			
			/*if (($fecha_vac<'2012-11-01')){
				$id_nomenclador_detalle=4;
			}*/
			
			//tengo que sacar el id_nomenclado
			$q="select * from facturacion.nomenclador
				where id_nomenclador_detalle='$id_nomenclador_detalle' and codigo='$codigo' and descripcion LIKE '%$descripcion%'";
		    $res_nom=sql($q,"Error en traer el id_nomenclador") or fin_pagina();
			$nomenclador=$res_nom->fields['id_nomenclador'];
			
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
		    
		    $query="insert into facturacion.prestacion
		             (id_prestacion,id_comprobante, id_nomenclador,cantidad,precio_prestacion,id_anexo,peso,tension_arterial,diagnostico,estado_envio)
		             values
		             ($id_prestacion,$id_comprobante,$nomenclador,'1',$precio_prestacion,$anexo,'0','00/00','A98','n')";
		
		    sql($query, "Error al insertar la prestacion") or fin_pagina();
		    
		    $accion.=" - Se Genero un comprobante por ser Beneficiario de Plan Nacer";
			
		    /*cargo los log*/ 
		    $usuario=$_ses_user['name'];
			$log="insert into facturacion.log_prestacion
				   (id_prestacion, fecha, tipo, descripcion, usuario) 
			values ($id_prestacion, '$fecha_carga','Nueva PRESTACION','Nro. prestacion $id_prestacion', '$usuario')";
			sql($log) or fin_pagina();
			
			
		    $db->CompleteTrans();		    
		}
		if ((($id_vac_apli == '5')||($id_vac_apli == '4'))and(GetCountDaysBetweenTwoDates(fecha_db($fecha_nac),date("Y-m-d"))<744)){//cargo trz VIII
			$fecha_nac_db=fecha_db($fecha_nac);
			$q="select nextval('trazadorassps.seq_id_trz8') as id_planilla";
			$id_planilla=sql($q) or fin_pagina();
			$id_planilla=$id_planilla->fields['id_planilla'];
	       
			$query="insert into trazadorassps.trazadora_8
	             (id_trz8,cuie,fecha_nac,fecha_vacuna_cuad_bacteriana,fecha_vacuna_antipoliomelitica,fecha_carga,usuario,
	             comentario,id_smiafiliados,id_beneficiarios)
	             values
	             ('$id_planilla','$cuie','$fecha_nac_db','$fecha_vac','$fecha_vac','$fecha_carga','$usuario',
	             'Desde Fichero de Vacunas. $comentario','$id_smiafiliados','$id_beneficiarios')";
			sql($query, "Error al insertar la Planilla") or fin_pagina();	    
			$accion.="Se guardo TRZ 8"; 
		}
		if ((($id_vac_apli == '5')||($id_vac_apli == '6'))and(GetCountDaysBetweenTwoDates(fecha_db($fecha_nac),date("Y-m-d"))>1825)){//cargo trz IV
			$fecha_nac_db=fecha_db($fecha_nac);
			$q="select nextval('trazadorassps.seq_id_trz9') as id_planilla";
			$id_planilla=sql($q) or fin_pagina();
			$id_planilla=$id_planilla->fields['id_planilla'];
	       
			$query="insert into trazadorassps.trazadora_9
	             (id_trz9,cuie,fecha_nac,fecha_vacuna_trip_bacteriana,fecha_vacuna_trip_viral,fecha_vacuna_antipoliomelitica,
	             fecha_carga,usuario,comentario,id_smiafiliados,id_beneficiarios)
	             values
	             ('$id_planilla','$cuie','$fecha_nac_db','$fecha_vac','$fecha_vac','$fecha_vac',
	             '$fecha_carga','$usuario','Desde Fichero de Vacunas. $comentario','$id_smiafiliados','$id_beneficiarios')";
			sql($query, "Error al insertar la Planilla") or fin_pagina();	    
			$accion.="Se guardo TRZ 9";
		}			
	 }else{
	 	 $accion="Esta vacuna ya ha sido cargada";  
	 }
 
	 
    $db->CompleteTrans();    
    //valida si esta captado
    $q="select * from nacer.smiafiliados where afidni='$num_doc'";
    $res_captado=sql($q) or fin_pagina();
    if ($res_captado->RecordCount()==0)
    {
    	$accion2="La Persona NO esta Captada por el Plan Nacer";
    }
    else
    {
    	$accion2="";
    }
    
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

if ($borra_vacuna=="borra_vacuna"){
	$fecha_carga=date("Y-m-d H:i:s");
	
	$query="update trazadoras.vacunas set
			eliminada=1 
			where id_vacunas=$id_planilla";
	sql($query, "Error al Borrar Registro") or fin_pagina();
	$accion="Se Elimino el registro $id_vacunas de vacunas"; 	
	
	 /*cargo los log*/ 
	 
	    $q_2="select nextval('trazadoras.log_vacunas_id_log_vacunas_seq') as id_log2";
	    $id_log2=sql($q_2) or fin_pagina();
	    $id_log2=$id_log2->fields['id_log2'];
	    
		    $usuario=$_ses_user['name'];
			$log2="insert into trazadoras.log_vacunas 
				   (id_log_vacunas, id_vacunas, fecha, tipo, descripcion, usuario) 
			values ($id_log2, '$id_planilla', '$fecha_carga','Elimino Registro','Nro. Registro $id_planilla', '$usuario')";
			sql($log2) or fin_pagina();
}

if ($id_planilla) {
	$query="SELECT 
			  *
			FROM
			  trazadoras.vacunas  
			  where id_vacunas=$id_planilla";
	
	$res_factura=sql($query, "Error al traer el Comprobantes") or fin_pagina();
	
	$cuie=$res_factura->fields['cuie'];
	$id_vac_apli=$res_factura->fields['id_vac_apli'];
	$id_dosis_apli=$res_factura->fields['id_dosis_apli'];
	$fecha_vac=$res_factura->fields['fecha_vac'];
	$nom_resp=$res_factura->fields['nom_resp'];
	$comentario=$res_factura->fields['comentario'];
}

if ($entidad_alta=='nu'){//carga de prestacion a paciente NO PLAN NACER
	$sql="select * from leche.beneficiarios
	where id_beneficiarios=$id";
    $res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();
    
    $apellido=$res_comprobante->fields['apellido'];
	$nombre=$res_comprobante->fields['nombre'];
	$dni=$res_comprobante->fields['documento'];
	$fecha_nac=$res_comprobante->fields['fecha_nac'];

} 
if ($entidad_alta=='na'){//carga de prestacion a paciente PLAN NACER
	$sql="select * from nacer.smiafiliados
		 left join nacer.efe_conv on (cuieefectorasignado=cuie)
		 where id_smiafiliados=$id";
    $res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();
 
    $apellido=trim($res_comprobante->fields['afiapellido']);
	$nombre=trim($res_comprobante->fields['afinombre']);
	$dni=$res_comprobante->fields['afidni'];
	$fecha_nac=$res_comprobante->fields['afifechanac'];
}
echo $html_header;
?>
<script>
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
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
	 if(document.all.cuie.value=="-1"){
	  alert('Debe Seleccionar un Efector');
	  return false;
	 } 
	 if(document.all.fecha_vac.value==""){
	  alert('Debe Ingresar una Fecha de Vacunacion');
	  return false;
	 } 
	 if(document.all.fecha_vac_prox.value==""){
	  alert('Debe Ingresar una Fecha de Proxima Vacuna');
	  return false;
	 } 
	 if(document.all.id_vac_apli.value=="-1"){
	  alert('Debe Seleccionar una Vacuna Aplicada');
	  return false; 
	 } 
	 
	 if(document.all.id_dosis_apli.value=="-1"){
	  alert('Debe Seleccionar una dosis Aplicada');
	  return false; 
	 } 
	 if(document.all.lote.value==""){
	  alert('Debe Ingresar un lote');
	  return false; 
	 } 
	 /*if (document.all.fecha_vac_prox.value <= document.all.fecha_vac.value){
			alert ("La fecha del proximo control no puede ser menor o igual a la fecha Control");
			return false;
	} */
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
</script>

<form name='form1' action='vac_admin.php' method='POST'>
<input type="hidden" value="<?=$usuario1?>" name="usuario1">
<input type="hidden" value="<?=$id_planilla?>" name="id_planilla">
<input type="hidden" value="<?=$id?>" name="id">
<input type="hidden" value="<?=$entidad_alta?>" name="entidad_alta"><?/*
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
      trazadoras.log_vacunas
    LEFT JOIN trazadoras.vacunas using (id_vacunas)           
	where trazadoras.vacunas.id_beneficiarios='$id'
	order by id_log_vacunas";
$log=sql($q);
}

if ($entidad_alta=='na'){//carga de prestacion a paciente PLAN NACER
$q="SELECT 
	  *
	FROM
     trazadoras.log_vacunas
    LEFT JOIN trazadoras.vacunas using (id_vacunas)           
	where trazadoras.vacunas.id_smiafiliados='$id '
	order by id_log_vacunas";
$log=sql($q);
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
        <font size=+1><b>Dato</b></font>   
        <? } ?>
       
    </td>
 </tr>
 <tr><td>
  <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripcion del Beneficario </b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         <tr>	           
           <td align="center" colspan="2">
            <b> Número del Dato: <font size="+1" color="Red"><?=($id_planilla)? $id_planilla : "Nuevo Dato"?></font> </b>
           </td>
         </tr>    
         <tr>
         	<td align="right">
         	  <b>Apellido:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$apellido?>" name="apellido" <? if ($id_planilla) echo "readonly"?>>
            </td>
       
         	<td align="right">
         	  <b>Nombre:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$nombre?>" name="nombre" <? if ($id_planilla) echo "readonly"?>>
            </td>
         </tr> 
		<tr>
         	<td align="right">
         	  <b>Documento:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="20" value="<?=$dni?>" name="dni" <? if ($id_planilla) echo "readonly"?>>
            </td>
         
			<td align="right">
				<b>Fecha de Nacimiento:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_nac name=fecha_nac value='<?=fecha($fecha_nac);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_nac");?>					    	 
		    </td>	
		    <td align="right" title="Edad a la Fecha actual">
         	  <b> Edad a la Fecha Actual:
           </td> 
           <td align='left'>
			 <?$edad_con_meses=edad_con_meses($fecha_nac);
			 $anio_edad=$edad_con_meses["anos"];
			 $meses_edad=$edad_con_meses["meses"];
			 $dias_edad=$edad_con_meses["dias"];
			 ?>
         	 <input type='text' name='edad' value='<?echo $anio_edad." Año/s, ".$meses_edad." Mes/es y ".$dias_edad." dia/s"?>' size=30 align='right' readonly></b>
			</td>   
		    	    
		</tr>
	<?// -------------tablas dobes para armar los datos de la vacuna-----------------?>	
		
	<table width=100% align="center" class="bordes">
     <tr align="center" id="sub_tabla">
      <td colspan="2">
       <b> Informacion de Vacunacion </b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         <tr>
         	<td align="right">
				<b>Efector:</b>
			</td>
			<td align="left">			 	
			 <select name=cuie Style="width=257px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				>
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
								$cuie=$res_efectores->fields['cuie'];
								$nombre_efector=$res_efectores->fields['nombre'];
								if($com_gestion=='FALSO')$color_style='#F78181'; else $color_style='';
								?>
								<option value='<?=$cuie;?>' Style="background-color: <?=$color_style;?>"><?=$cuie." - ".$nombre_efector?></option>
								<?
								$res_efectores->movenext();
								}?>
			</select>
			</td>
			<td align="right">
				<b>Fecha:</b>
			
		    	<?$fecha_vac=date("d/m/Y");?>
		    	 <input type=text id=fecha_vac name=fecha_vac value='<?=$fecha_vac;?>' size=15  readonly> 
		    	 <?=link_calendario("fecha_vac");?>
		    </td>
		    </td>
			<td align="right">
				<b>Fecha Proxima Vacuna:</b>
		
		    	<?$fecha_vac_prox=date("d/m/Y");?>
		    	 <input type=text id=fecha_vac_prox name=fecha_vac_prox value='<?=$fecha_vac_prox;?>' size=15  readonly> 
		    	 <?=link_calendario("fecha_vac_prox");?>
		    </td>
		    		    
		</tr>       
         <tr>
         	<td align="right">
				<b>Vacuna Aplicada:</b>
			</td>
			<td align="left">			 	
			 <select name=id_vac_apli Style="width=257px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				>
			 <option value=-1>Seleccione</option>
			 <?
			 $sql= "select * from trazadoras.vac_apli where id_vac_apli <> '15' order by nombre";
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$cuiel=$res_efectores->fields['id_vac_apli'];
			    $nombre_efector=$res_efectores->fields['nombre'];			    
			    ?>
				<option value='<?=$cuiel?>' ><?=$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>
			</td>
        
         	<td align="right">
				<b>Dosis Aplicada:</b>
			</td>
			<td align="left">			 	
			 <select name=id_dosis_apli Style="width=265px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				>
			 <option value=-1>Seleccione</option>
			 <?
			 $sql= "select * from trazadoras.dosis_apli order by nombre";
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$cuiel=$res_efectores->fields['id_dosis_apli'];
			    $nombre_efector=$res_efectores->fields['nombre'];			    
			    ?>
				<option value='<?=$cuiel?>' ><?=$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>
			</td>
         </tr>   	
		  <tr>
         	<td align="right">
         	  <b>Lote:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$lote?>" name="lote" >
            </td>
			<td align="right">
         	  <b>Nombre del Responsable:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$nom_resp?>" name="nom_resp" >
            </td>
         	
         </tr> 
		<tr>
         	<td align="right">
         	  <b>Email:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$email?>" name="email" >
            </td>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='2' name='comentario' ></textarea>
            </td>
         </tr>  		 
        </table>
      </td>      
     </tr> 
     <?// -------------tablas dobes para armar los datos de la vacuna-----------------?>	  
    </tr></td></table>    
  </table>
<?// -------------tablas dobes para armar los datos de la vacuna-----------------?>	  

	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Registro</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar' Style="width=300px;height=30px" onclick="return control_nuevos()"
         title="Guardar registro de vacunacion">
       </td>
      </tr>
     
           
<br>

	
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='../entrega_leche/listado_beneficiarios_leche.php'"title="Volver al Listado" style="width=150px">
   </td>
  </tr>
 </table></td></tr>
 </table>
 <?// --------------------tablas de muestra de vacunas dadas y facturadas 
if ($entidad_alta=='nu'){//carga de prestacion a paciente NO PLAN NACER
	$query="SELECT *,
				nacer.efe_conv.nombre as nom_efector,
				trazadoras.vacunas.fecha_vac as f_vacuna,
				trazadoras.vacunas.fecha_vac_prox,
				trazadoras.vacunas.lote,
				trazadoras.vac_apli.nombre as nom_vacum,
				trazadoras.dosis_apli.nombre as dosis,
				trazadoras.vacunas.fecha_pcontrol_flag
			FROM
			trazadoras.vacunas
			INNER JOIN nacer.efe_conv ON trazadoras.vacunas.cuie = nacer.efe_conv.cuie
			INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
			INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
			INNER JOIN leche.beneficiarios on trazadoras.vacunas.id_beneficiarios= leche.beneficiarios.id_beneficiarios
			where leche.beneficiarios.id_beneficiarios='$id'  and eliminada < '1' 
			ORDER BY trazadoras.vacunas.fecha_vac DESC";
}elseif ($entidad_alta=='na'){//carga de prestacion a paciente PLAN NACER
			$query="SELECT *,
					nacer.efe_conv.nombre as nom_efector,
					trazadoras.vacunas.fecha_vac as f_vacuna,
					trazadoras.vacunas.fecha_vac_prox,
					trazadoras.vacunas.lote,
					trazadoras.vac_apli.nombre as nom_vacum,
					trazadoras.dosis_apli.nombre as dosis,
					trazadoras.vacunas.fecha_pcontrol_flag
					FROM
					trazadoras.vacunas
					INNER JOIN nacer.efe_conv ON trazadoras.vacunas.cuie = nacer.efe_conv.cuie
					INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
					INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
					INNER JOIN nacer.smiafiliados on trazadoras.vacunas.id_smiafiliados= nacer.smiafiliados.id_smiafiliados
					where trazadoras.vacunas.id_smiafiliados='$id' and (trazadoras.vacunas.eliminada = '0')
					ORDER BY trazadoras.vacunas.fecha_vac DESC";
		}
$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();?>

<tr><td><table width="85%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
	  </td>
	  <td align="center">
	   <b>Prestaciones</b>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida" border="1" width="85%" style="display:none;border:thin groove"align="center">
	<?
	if ($_POST['guardar']=="Guardar"){?>
		<script>
			muestra_tabla(document.all.prueba_vida,2);
		</script>
	<?}
	
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
	 		<td >Efector</td>	 		
	 		<td >Fecha</td>	 		
	 		<td >Fecha Prox Vacuna</td>	 		
	 		<td >Nombre de Vacuna</td>
	 		<td >Dosis</td>
	 		<td >Lote</td>
			<td >Nombre del Responsable</td>
			<td >Mail</td>			
			<td >Prox. Control</td>			
	 		<?if (permisos_check('inicio','perm_borra_vacuna')){?>
	 		<td width=1%>Borrar</td>
	 		<?}?>
	 	</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 	while (!$res_comprobante->EOF) {
			$ref = encode_link("esavi.php",array("id_vacunas"=>$res_comprobante->fields['id_vacunas'],"pagina"=>"vac_admin.php"));             
            $onclick_elegir="location.href='$ref'";
            
            $id_vacunas=$res_comprobante->fields['id_vacunas'];
            $sql = "select * from trazadoras.esavi where id_vacunas='$id_vacunas'";
			$result_vac=sql($sql,"no se puede ejecutar");
			if ($result_vac->recordcount()>0)$color="#F7819F";
			else $color="";?>
	 		<tr <?=atrib_tr()?>>
		 		<td align="center" bgcolor='<?=$color?>' onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['cuie'].' - '.$res_comprobante->fields['nom_efector']?></td>
		 		<td align="center" bgcolor='<?=$color?>' onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['f_vacuna'])?></td>		 		
		 		<td align="center" bgcolor='<?=$color?>' onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['fecha_vac_prox'])?></td>		 		
		 		<td align="center" bgcolor='<?=$color?>' onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['nom_vacum']?></td>		
		 		<td align="center" bgcolor='<?=$color?>' onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['dosis']?></td>	
		 		<td align="center" bgcolor='<?=$color?>' onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['lote']?></td>	
				<td align="center" bgcolor='<?=$color?>' onclick="<?=$onclick_elegir?>"><?if ($res_comprobante->fields['nom_resp']!="") echo $res_comprobante->fields['nom_resp']; else echo "&nbsp"?></td>
		 		<td align="center" bgcolor='<?=$color?>' onclick="<?=$onclick_elegir?>"><?if ($res_comprobante->fields['email']!="") echo $res_comprobante->fields['email']; else echo "&nbsp"?></td>	
		 		<td align="center" onclick="<?//=$onclick_elegir?>"><?if ($res_comprobante->fields['fecha_pcontrol_flag']!="0") echo "NO"; else echo "SI"?></td>			
		 		 <?if (permisos_check('inicio','perm_borra_vacuna')){		 		 	
					$ref=encode_link("vac_admin.php",array("id_planilla"=>$res_comprobante->fields['id_vacunas'],"borra_vacuna"=>"borra_vacuna","id"=>$id,"entidad_alta"=>$entidad_alta)); 
					$onclick_provincia="if (confirm('Seguro que desea eliminar el registro de vacunación?')) location.href='$ref'"; ?>
					<td align="center"><img src='../../imagenes/salir.gif' style='cursor:hand;' onclick="<?=$onclick_provincia?>"></td>
		 		<?}?>		 		 		
		 	</tr>	
		 	
	 		<?$res_comprobante->movenext();
	 	}
	 }?>
</table></td></tr>
 <? // --------------------tabla principal color--------------?>
  </table>  
 </form>
 
 <?=fin_pagina();// aca termino ?>
