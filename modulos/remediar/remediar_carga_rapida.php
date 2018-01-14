<?
require_once ("../../config.php");
include_once("../facturacion/funciones.php");

extract($_POST, EXTR_SKIP);
if ($parametros)
extract($parametros, EXTR_OVERWRITE);

cargar_calendario();

$sql = "select 	clave_beneficiario,
				tipo_documento,
				clase_documento_benef,
				numero_doc,
				apellido_benef,
				nombre_benef,
				fecha_nacimiento_benef,
				sexo,telefono,
				provincia_nac,
				departamento,
				municipio,
				localidad,
				calle,
				barrio,
				numero_calle,
				manzana,
				piso,
				dpto
				from uad.beneficiarios	  
				where beneficiarios.clave_beneficiario='$clave_beneficiario' ";
$res_extra = sql($sql, "Error al traer el beneficiario") or fin_pagina();
    
        $clave = $res_extra->fields['clave_beneficiario'];
        $tipo_doc = $res_extra->fields['tipo_documento'];
        $clase_doc = $res_extra->fields['clase_documento_benef'];
        $num_doc = number_format($res_extra->fields['numero_doc'], 0, '.', '');
        $apellido = $res_extra->fields['apellido_benef'] . ' ' . $res_extra->fields['apellido_benef_otro'];
        $nombre = $res_extra->fields['nombre_benef'] . ' ' . $res_extra->fields['nombre_benef_otro'];
        $fecha_nac = fecha($res_extra->fields['fecha_nacimiento_benef']);
        $sexo = $res_extra->fields['sexo'];
        $telefono = $res_extra->fields['telefono'];
        $provincia_nac = $res_extra->fields['provincia_nac'];
        $departamento = $res_extra->fields['departamento'];
        $municipio = $res_extra->fields['municipio'];
        $localidad = $res_extra->fields['localidad'];
        $calle = $res_extra->fields['calle'];
        $barrio = $res_extra->fields['barrio'];
        $numero_calle = $res_extra->fields['numero_calle'];
        $manzana = $res_extra->fields['manzana'];
        $piso = $res_extra->fields['piso'];
        $dpto = $res_extra->fields['dpto'];
        
        //verifica si tiene cargada clasificacion
        $query = "SELECT  id_clasificacion,nro_clasificacion
					FROM trazadoras.clasificacion_remediar2  
					where clave_beneficiario='$clave'";
        $res_factura = sql($query, "Error al traer el Comprobantes") or fin_pagina();
        if ($res_factura->recordcount() > 0) {
            $id_planilla = $res_factura->fields['id_clasificacion'];
            $nro_clasificacion = $res_factura->fields['nro_clasificacion']; //echo rtrim(substr($accion,3,9));
            if (rtrim(substr($accion2, 3, 6)) != "guardo" && rtrim(substr($accion2, 3, 9)) != "actualizo" && $accion != 'Imposible guardar el medico.' && $accion != 'Falta el medico.') {
                $accion = 'Beneficiario ya posee Clasificacion con Nro. ' . $nro_clasificacion;
            }
        }    
 
/* * ******
 * metodo de guardar
 * ******* */

if ($_POST['guardar'] == "Guardar Planilla") {
	
	if ($nro_clasificacion=='') $nro_clasificacion=$clave_beneficiario;
    $fecha_carga = date("Y-m-d H:m:s");
    $usuario = $_ses_user['id'];
    $db->StartTrans();

    $fecha_nac = Fecha_db($fecha_nac);
    $fecha_clasificacion = Fecha_db($fecha_clasificacion);
      
    if ($dmt == '') {
        $dmt = 0;
    }
    
    
    if ($dmt2 == 'on') {
        $dmt2 = 1;
    } else {
        $dmt2 = 0;
    }
    if ($insu_renal == 'on') {
        $insu_renal = 1;
    } else {
        $insu_renal = 0;
    }    
    if ($dmt_menor == 'on') {
        $dmt_menor = 1;
    } else {
        $dmt_menor = 0;
    }
    if ($hta_menor == 'on') {
        $hta_menor = 1;
    } else {
        $hta_menor = 0;
    }
    if ($hta == 'on') {
        $hta = 1;
    } else {
        $hta = 0;
    }
    if ($tabaquismo == 'on') {
        $tabaquismo = 1;
    } else {
        $tabaquismo = 0;
    }
    if ($meno_prema == 'on') {
        $meno_prema = 1;
    } else {
        $meno_prema = 0;
    }
    if ($antihiper == 'on') {
        $antihiper = 1;
    } else {
        $antihiper = 0;
    }
    if ($obesi == 'on') {
        $obesi = 1;
    } else {
        $obesi = 0;
    }
    if ($acv_prema == 'on') {
        $acv_prema = 1;
    } else {
        $acv_prema = 0;
    }
    if ($trigli == 'on') {
        $trigli = 1;
    } else {
        $trigli = 0;
    }
    if ($hdl_col == 'on') {
        $hdl_col = 1;
    } else {
        $hdl_col = 0;
    }
    if ($hiperglu == 'on') {
        $hiperglu = 1;
    } else {
        $hiperglu = 0;
    }
    if ($microalbu == 'on') {
        $microalbu = 1;
    } else {
        $microalbu = 0;
    }            

    $ta_sist = str_replace(',', '.', $ta_sist);
    if ($ta_sist == '') {
        $ta_sist = 0;
    }
    $ta_diast = str_replace(',', '.', $ta_diast);
    if ($ta_diast == '') {
        $ta_diast = 0;
    }
    $col_tot = str_replace(',', '.', $col_tot);
    if ($col_tot == '') {
        $col_tot = 0;
    }
    
    if (!$id_planilla) {
        $queryx = "SELECT  clave_beneficiario,num_doc,apellido,nombre
				FROM trazadoras.clasificacion_remediar2  
			  where nro_clasificacion='$nro_clasificacion'";
        $res_comp_num_comp = sql($queryx, "Error al traer el Comprobantes") or fin_pagina();
        if ($res_comp_num_comp->recordcount() == 0) {

            //inserto en tablas de uad.remediar_x_beneficiario remediar.formulario

            $sql="select nextval('uad.remediar_x_beneficiario_Id_r_x_b_seq') as id";
            $res_rxb=sql($sql) or fin_pagina();
            $Id_r_x_b=$res_rxb->fields['id'];

            $query1="insert into uad.remediar_x_beneficiario 
                        (id_r_x_b,nroformulario,fechaempadronamiento,clavebeneficiario,fecha_carga,usuario_carga,emp_rapido)
                        values
                        ($Id_r_x_b,$clave,'$fecha_clasificacion','$clave','$fecha_carga',upper('$usuario'),'s')";
            sql($query1, "Error al insertar Remediar1") or fin_pagina();


            $q="select nextval('remediar.formulario_Id_formulario_seq') as id";
            $id_Idformulario=sql($q) or fin_pagina();
            $Id_formulario=$id_Idformulario->fields['id'];
                
            $usuario_nombre=$_ses_user['name'];
            $query2="insert into remediar.formulario 
                        (id_formulario,nroformulario,factores_riesgo,hta2,hta3,colesterol4,colesterol5,dmt26,dmt27,ecv8,tabaco9,puntaje_final,apellidoagente,nombreagente,centro_inscriptor,dni_agente,os,cual_os,usuario,fecha_carga,form_rapido)
                        values
                        ($Id_formulario,$clave,0,0,0,0,0,0,0,0,0,0,upper('$usuario_nombre'),upper('$usuario_nombre'),upper('$cuie'),'dni','NINGUNA','','$usuario_nombre','$fecha_carga','s')";

            sql($query2, "Error al insertar Remediar2") or fin_pagina();

            //inserto en clasificacion
            if ($fecha_prox_seguimiento=='')$fecha_prox_seguimiento='1980-01-01';
			else $fecha_prox_seguimiento=fecha_db($fecha_prox_seguimiento);
            $query = "insert into trazadoras.clasificacion_remediar2 
				(id_clasificacion,nro_clasificacion,cuie,clave_beneficiario,tipo_doc,num_doc,apellido,nombre,
				fecha_nac,fecha_carga,usuario,fecha_control,id_medico,dmt,acv,vas_per,car_isq,col310,col_ldl,ct_hdl,pres_art
				,dmt2,insu_renal,dmt_menor,hta_menor,hta,tabaquismo,ta_sist,ta_diast,col_tot,menopausia,antihiper,
				obesi,acv_prema,trigli,hdl_col,hiperglu,microalbu,rcvg,fecha_prox_seguimiento,diabetico,hipertenso,clasif_rapida)
            values 
				(nextval('trazadoras.clasificacion_remediar_id_clasificacion_seq'),'$nro_clasificacion','$cuie','$clave','$tipo_doc','$num_doc','$apellido',
				'$nombre','$fecha_nac','$fecha_carga','$usuario','$fecha_clasificacion','0','$dmt','$acv','$vas_per','$car_isq','$col310','$col_ldl','$ct_hdl','$pres_art','$dmt2',
				'$insu_renal','$dmt_menor','$hta_menor','$hta','$tabaquismo','$ta_sist','$ta_diast','$col_tot','$menopausia','$antihiper','$obesi','$acv_prema',
				'$trigli','$hdl_col','$hiperglu','$microalbu','$riesgo_global','$fecha_prox_seguimiento','$diabetico','$hipertenso','s') RETURNING id_clasificacion";
			$res_extras = sql($query, "Error al insertar la Planilla") or fin_pagina();
			$id_planilla = $res_extras->fields['id_clasificacion'];
            
		
			$accion = '';
            $accion2 = "Se guardo la Planilla Clasificacion Nro: " . $nro_clasificacion. ".";
            
        }
        if ($res_comp_num_comp->recordcount() > 0) {
            $accion2 = '';
            $accion = 'El Nro. de Clasificacion ' . $nro_clasificacion . ' ya existe para ' . $res_comp_num_comp->fields['apellido'] . ' ' . $res_comp_num_comp->fields['nombre'] . ', ' . $res_comp_num_comp->fields['num_doc'] . '.';
        }
    } elseif ($id_planilla) {
    	if ($fecha_prox_seguimiento=='')$fecha_prox_seguimiento='NULL';
		else $fecha_prox_seguimiento=fecha_db($fecha_prox_seguimiento);
        $query = "Update trazadoras.clasificacion_remediar2 set nro_clasificacion='$nro_clasificacion',id_medico='0',dmt='$dmt',acv='$acv',vas_per='$vas_per',car_isq='$car_isq',col310='$col310',col_ldl='$col_ldl',ct_hdl='$ct_hdl',pres_art='$pres_art'
        ,dmt2='$dmt2',insu_renal='$insu_renal',dmt_menor='$dmt_menor',hta_menor='$hta_menor',hta='$hta',tabaquismo='$tabaquismo',ta_sist='$ta_sist',ta_diast='$ta_diast',col_tot='$col_tot',menopausia='$menopausia',antihiper='$antihiper'
        ,obesi='$obesi',acv_prema='$acv_prema',trigli='$trigli',hdl_col='$hdl_col',hiperglu='$hiperglu',microalbu='$microalbu',fecha_control='$fecha_clasificacion',rcvg='$riesgo_global',fecha_prox_seguimiento='$fecha_prox_seguimiento', 
		diabetico='$diabetico', hipertenso='$hipertenso', clasif_rapida='s', cuie='$cuie' where id_clasificacion=$id_planilla";    
        
        $res_extras = sql($query, "Error al insertar la Planilla") or fin_pagina();
        $accion = '';
        $accion2 = "Se actualizo la Planilla";
    }

    $db->CompleteTrans();
	
	//graba registro en fichero
	if($ta=='')$ta=0;
	$fecha_carga=date("Y-m-d H:i:s");
	$nom_medico='Remediar mas Redes';
	$fecha_comprobante=$_POST['fecha_clasificacion'];
	$fecha_comprobante=Fecha_db($fecha_comprobante);
	$comentario='Desde Remediar mas Redes';
	$periodo= str_replace("-","/",substr($fecha_comprobante,0,7));
	$fecha_control=$fecha_comprobante; //la fecha de control = fecha del comprobante.
		    
	if($fecha_prox_seguimiento=='')$fecha_pcontrol='1000-01-01';
	else $fecha_pcontrol=$fecha_prox_seguimiento;
		    
	if($tunner=='')$tunner=0;	
	if($peso=='')$peso=0;
	if($talla=='')$talla=0;
	if($imc=='')$imc=0;
	if($ta=='')$ta=0;	    	
   	$tasa_materna=0;
    if($peso_embarazada=='')$peso_embarazada=0;
    if($altura_uterina=='')$altura_uterina=0;
    if($imc_uterina=='')$imc_uterina=0;
    if($semana_gestacional=='')$semana_gestacional=0;
	if($perim_cefalico=='')$perim_cefalico=0;
	$embarazo='NO';
	$fpp='1000-01-01'; 
	$fum='1000-01-01'; 
	$f_diagnostico='1000-01-01';	

	$query="select id_smiafiliados,clavebeneficiario from nacer.smiafiliados where clavebeneficiario='$nro_clasificacion'";
	$res=sql($query, "No se puede traer el id_smiafiliados") or fin_pagina();
	$id=$res->fields['id_smiafiliados'];
	if ($id!=''){
		$id_beneficiarios=0; 
		$id_smiafiliados=$id;
		$update_f="update fichero.fichero set fecha_pcontrol_flag='0' where id_smiafiliados='$id'";
		sql($update_f, "No se puede actualizar los registros") or fin_pagina();	
		
		$q="select nextval('fichero.fichero_id_fichero_seq') as id_fichero";
			    $id_fichero=sql($q) or fin_pagina();
			    $id_fichero=$id_fichero->fields['id_fichero'];
		$query="insert into fichero.fichero
				             (id_fichero,  cuie, nom_medico, fecha_control, comentario, periodo, peso, talla, 
				             imc ,ta, tunner, c_vacuna,  ex_clinico_gral, ex_trauma, ex_cardio, ex_odontologico, ex_ecg, hemograma, vsg, 
				             glucemia, uremia, ca_total, orina_cto, chagas ,obs_laboratorio, ergometria, obs_adolesc, id_smiafiliados, id_beneficiarios, 
				             conclusion,tasa_materna,salud_rep,metodo_anti,fecha_pcontrol,fpp, fum, f_diagnostico, peso_embarazada,altura_uterina,imc_uterina, semana_gestacional, 
				             rx_torax,rx_col_vertebral,otros,rx_observaciones,otros_obs,fecha_pcontrol_flag,percen_peso_edad,percen_talla_edad,perim_cefalico,percen_perim_cefali_edad,percen_peso_talla,percen_imc_edad,diabetico,hipertenso,embarazo,publico,ag_visual, obs_ecg)
			             values
			             ($id_fichero, '$cuie', '$nom_medico', '$fecha_control', '$comentario', '$periodo', '$peso', 
				             '$talla', '$imc', '$ta', '$tunner', '$c_vacuna',  '$ex_clinico_gral', '$ex_trauma', '$ex_cardio', '$ex_odontologico', '$ex_ecg', '$hemograma', 
				             '$vsg', '$glucemia', '$uremia', '$ca_total', '$orina_cto', '$chagas', '$obs_laboratorio', '$ergometria', '$obs_adolesc', '$id_smiafiliados', '$id_beneficiarios', 
				             '$conclusion','$tasa_materna','$salud_rep','$metodo_anti' ,'$fecha_pcontrol', '$fpp', '$fum', '$f_diagnostico', '$peso_embarazada', '$altura_uterina','$imc_uterina','$semana_gestacional',
				             '$rx_torax','$rx_col_vertebral','$otros','$rx_observaciones','$otros_obs','1','$percen_peso_edad','$percen_talla_edad','$perim_cefalico','$percen_perim_cefali_edad','$percen_peso_talla', '$percen_imc_edad','$diabetico','$hipertenso','$embarazo','$publico', '$ag_visual', '$obs_ecg')";	
		sql($query, "Error al insertar el comprobante") or fin_pagina();	    
		$accion_fichero="Registro Grabado en Fichero.";
	}

    /* if ($pagina=="prestacion_admin.php") echo "<script>window.close()</script>";   */
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

/* * ******
 * metodo de borrar
 * ******* */

if ($_POST['borrar'] == "Borrar") {
    $query = "delete from trazadoras.clasificacion_remediar2
			where id_clasificacion=$id_planilla";
    sql($query, "Error al insertar la Planilla") or fin_pagina();
    $accion = "Se elimino la planilla $id_planilla de Ni�os";
}

/* * ******
 * metodo de buscar
 * ******* */

if ($_POST['buscar_clasificacion'] == "b") {
    if ($_POST['nro_clasificacion'] != '') {
        $query = "SELECT id_clasificacion,nro_clasificacion
				FROM trazadoras.clasificacion_remediar2  
			  where nro_clasificacion='$nro_clasificacion' and clave_beneficiario='$clave'";
        $res_factura = sql($query, "Error al traer el Comprobantes") or fin_pagina();
        if ($res_factura->recordcount() > 0) {
            $id_planilla = $res_factura->fields['id_clasificacion'];
            $nro_clasificacion = $res_factura->fields['nro_clasificacion'];
            $accion = 'Clasificacion con Nro.' . $nro_clasificacion . ' encontrada.';
        }
        if ($res_factura->recordcount() == 0) {
            $accion2 = 'No se encuentra Clasificacion con Nro.' . $nro_clasificacion . ' para este beneficiario';
            //$id_planilla=0;                               
            $fecha_carga = '';
            $acv = '';
            $vas_per = '';
            $car_isq = '';
            $col310 = '';
            $col_ldl = '';
            $ct_hdl = '';
            $pres_art = '';
            $dmt2 = '';
            $insu_renal = '';
            $dmt_menor = '';
            $hta_menor = '';

            $hta = '';
            $tabaquismo = '';
            $ta_sist = '';
            $ta_diast = '';
            $col_tot = '';
            $meno_prema = '';
            $antihiper = '';
            $obesi = '';
            $acv_prema = '';
            $trigli = '';
            $hdl_col = '';
            $hiperglu = '';
            $microalbu = '';
            $riesgo_global = '';

            $apellido_medico = '';
            $nombre_medico = '';
            $matricula_medico = '';
            //$id_smiafiliados=0;
        }
    } else {
        echo "<SCRIPT Language='Javascript'> alert('Debe Cargar el Nro. de Clasificacion'); </SCRIPT>";
        
    }
}


if ($id_planilla) { //echo 'aaa';
    $query = "SELECT  *
	FROM
  trazadoras.clasificacion_remediar2  a
  left join planillas.medicos b on a.id_medico=b.id_medico
  where id_clasificacion=$id_planilla";
// VER AQU� TAMBI�N
    $res_factura = sql($query, "Error al traer el Comprobantes") or fin_pagina();

    $cuie = $res_factura->fields['cuie'];
    $clave = $res_factura->fields['clave_beneficiario'];
    $tipo_doc = $res_factura->fields['tipo_doc'];
    $num_doc = number_format($res_factura->fields['num_doc'], 0, '.', '');
    $apellido = $res_factura->fields['apellido'];
    $nombre = $res_factura->fields['nombre'];
    $fecha_nac = fecha($res_factura->fields['fecha_nac']);
    $fecha_carga = fecha($res_factura->fields['fecha_carga']);

    $acv = $res_factura->fields['acv'];
    $vas_per = $res_factura->fields['vas_per'];
    $car_isq = $res_factura->fields['car_isq'];
    $col310 = $res_factura->fields['col310'];
    $col_ldl = $res_factura->fields['col_ldl'];
    $ct_hdl = $res_factura->fields['ct_hdl'];
    $pres_art = $res_factura->fields['pres_art'];
    $dmt2 = $res_factura->fields['dmt2'];
    $insu_renal = $res_factura->fields['insu_renal'];
    $dmt_menor = $res_factura->fields['dmt_menor'];
    $hta_menor = $res_factura->fields['hta_menor'];
    $dmt=$res_factura->fields['dmt'];

    $hta = $res_factura->fields['hta'];
    $tabaquismo = $res_factura->fields['tabaquismo'];
    $ta_sist = $res_factura->fields['ta_sist'];
    $ta_diast = $res_factura->fields['ta_diast'];
    $col_tot = $res_factura->fields['col_tot'];
    $menopausia = $res_factura->fields['menopausia'];
    $antihiper = $res_factura->fields['antihiper'];
    $obesi = $res_factura->fields['obesi'];
    $acv_prema = $res_factura->fields['acv_prema'];
    $trigli = $res_factura->fields['trigli'];
    $hdl_col = $res_factura->fields['hdl_col'];
    $hiperglu = $res_factura->fields['hiperglu'];
    $microalbu = $res_factura->fields['microalbu'];
    $riesgo_global = $res_factura->fields['rcvg'];
    $fecha_clasificacion=$res_factura->fields['fecha_control'];

    $apellido_medico = $res_factura->fields['apellido_medico'];
    $nombre_medico = $res_factura->fields['nombre_medico'];
    $dni_medico = $res_factura->fields['dni_medico'];
	$fecha_prox_seguimiento = $res_factura->fields['fecha_prox_seguimiento'];
	$diabetico = trim($res_factura->fields['diabetico']);
	$hipertenso = trim($res_factura->fields['hipertenso']);
    $fechaempadronamiento=$res_factura->fields['fecha_control'];
}

echo $html_header;
?>
<script>
    //controlan que ingresen todos los datos necesarios par el muleto
    function control_nuevos()
    {

         if(document.all.fecha_clasificacion.value==""){
			alert("Debe completar la Fecha de la Clasificacion");
			document.all.fecha_clasificacion.focus();
			return false;
		 }
		 
        if(document.all.riesgo_global[0].checked==false && document.all.riesgo_global[1].checked==false && document.all.riesgo_global[2].checked==false && document.all.riesgo_global[3].checked==false){
            alert("Debe seleccionar al menos una opcion en RCVG");
            //document.all.nro_clasificacion.focus();
            return false;
        }
        
        function mayor_menor($dato,$mayor,$menor,$mensaje){
            kamikaze=false;
            if (variable == false){
                if ($mayor!=="vacio"){
                    if ($dato.value > $mayor){
                        kamikaze=true;
                    }
                }
                if ($mayor!=="vacio"){
                    if ($dato.value < $menor){
                        kamikaze=true;
                    }
                }
                if (kamikaze==true){
                    alert($mensaje+'. De lo contrario comun&acuote;quese a Plan Nacer');
                    $dato.focus();
                    variable=true;
                    return variable;
                }
            }
        }			
        // Funcion Convertir fecha
        function f_fecha(fechaentrada,fechasalida) {
            var elem = fechaentrada.split('/');
            var dia = elem[0];
            var mes = elem[1]-1;
            var anio = elem[2];
            fechasalida.setFullYear(eval(anio),eval(mes),eval(dia));
            return fechasalida;
        }
        // Convierto fechas para poder compararlas despu�s
        var vfecha_control=new Date();
        vfecha_control = f_fecha(document.all.fecha_control.value,vfecha_control);	

        variable = false;
        $error = false;

        function verif_vacio($dato,$vacio,$mensaje){
            if (($dato.value==$vacio)&&(variable==false)){
                alert('Debe ingresar'+$mensaje);
                $dato.focus();	
                variable = true;
                return variable;
            }
        }
        function cambio_cero($dato){		 
            if ($dato==""){
                $dato="0";
            }
        }
        verif_vacio(document.all.fecha_control,""," Fecha de Control");

        if (variable==false){
            ////////---------------- HASTA 1 A�O ---------------------------/////////////////////////
            if(document.all.nino_edad.value==0){
                mayor_menor(document.all.peso,15,2.5,"El peso debe encontrarse entre 2.5 y 15 Kg en ni&ntilde;os menores de un a&ntilde;o");
                mayor_menor(document.all.talla,100,30,"La talla debe encontrarse entre 30 y 100 cm");

            }//CIERRE NI�O EDAD = 0
            /////////-------------- DE 1 A 6 A�OS-------------------////////////////////////////////
            if (document.all.nino_edad.value=="1"){
                mayor_menor(document.all.peso,50,5,"El peso debe encontrarse entre 5 y 50 kg para ni&ntilde;os de entre 1 a 5 a&ntilde;os");	
                mayor_menor(document.all.talla,160,40,"La talla del ni&ntilde;o debe encontrarse entre 40 y 160 cm para ni&ntilde;os de entre 1 a 5 a&ntilde;os");	

            } //CIERRO NI�OS DE 1 A 5 A�OS
        } // CIERRA variable == false

        // ************* TRIPLE VIRAL MAYOR A FECHA NACIMIENTO *****************
        if (variable==true){
            return false;
        }	 
    }//de function control_nuevos()


    /**********************************************************/
    //funciones para busqueda abreviada utilizando teclas en la lista que muestra los clientes.
    var digitos=10; //cantidad de digitos buscados
    var puntero=0;
    var buffer=new Array(digitos); //declaraci�n del array Buffer
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
        event.returnValue = false; //invalida la acci�n de pulsado de tecla para evitar busqueda del primer caracter
    }//de function buscar_op_submit(obj)
    /**********************************************************/
    //Validar Fechas
    function esFechaValida(fecha){
        if (fecha != undefined && fecha.value != "" ){
            if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)){
                alert("formato de fecha no valido (dd/mm/aaaa)");
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
                                alert("Fecha introducida err&oacute;nea");
                                return false;
                        }
 
                        if (dia>numDias || dia==0){
                            alert("Fecha introducida err&oacute;nea");
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
                /**********************************************************/
                var patron = new Array(2,2,4)
                var patron2 = new Array(5,16)
                function mascara(d,sep,pat,nums){
                    if(d.valant != d.value){
                        val = d.value
                        largo = val.length
                        val = val.split(sep)
                        val2 = ''
                        for(r=0;r<val.length;r++){
                            val2 += val[r]
                        }
                        if(nums){
                            for(z=0;z<val2.length;z++){
                                if(isNaN(val2.charAt(z))){
                                    letra = new RegExp(val2.charAt(z),"g")
                                    val2 = val2.replace(letra,"")
                                }
                            }
                        }
                        val = ''
                        val3 = new Array()
                        for(s=0; s<pat.length; s++){
                            val3[s] = val2.substring(0,pat[s])
                            val2 = val2.substr(pat[s])
                        }
                        for(q=0;q<val3.length; q++){
                            if(q ==0){
                                val = val3[q]

                            }
                            else{
                                if(val3[q] != ""){
                                    val += sep + val3[q]
                                }
                            }
                        }
                        d.value = val
                        d.valant = val
                    }
                }

</script>
<style type="text/css">
    <!--
    .Estilo1 {
        font-size: large;
        color: #FF6633;
    }
    -->
</style>

<form name='form1' action='remediar_carga_rapida.php' method='POST'>
    <input type="hidden" value="<?= $id_planilla ?>" name="id_planilla">
    <input type="hidden" value="<?= $pagina ?>" name="pagina">
    <input type="hidden" value="<?= $id_smiafiliados ?>" name="id_smiafiliados">
    <input type="hidden" value="<?= $pagina_viene ?>" name="pagina_viene">
    <input type="hidden" value="<?= $tema ?>" name="tema">
    <input type="hidden" value="<?= $id_nomenclador_detalle ?>" name="id_nomenclador_detalle">
    <input type="hidden" value="<?= $fecha_comprobante ?>" name="fecha_comprobante">
    <input type="hidden" value="<?= $clave_beneficiario ?>" name="clave_beneficiario">
    <input type="hidden" value="<?= $fecha_nacimiento ?>" name="fecha_nacimiento">
    <input type="hidden" value="<?= $sexo_codigo ?>" name="sexo_codigo">
    <input type="hidden" value="<?= $edad ?>" name="edad">
    <input type="hidden" value="<?= $prestacion ?>" name="prestacion">
    <input type="hidden" value="<?= $tema ?>" name="tema">
    <input type="hidden" value="<?= $patologia ?>" name="patologia">
    <input type="hidden" value="<?= $profesional ?>" name="profesional">
    <input type="hidden" value="<?= $id_comprobante ?>" name="id_comprobante">
<? echo "<center><b><font size='+1' color='Blue'>$accion2</font></b></center>"; ?>
<? echo "<center><b><font size='+1' color='red'>$accion $accion_fichero</font></b></center>"; ?>   
    
    <table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?= $bgcolor_out ?>' class="bordes">
        <tr>
        <tr id="mo">
            <td align="center" colspan="4" >
                <b> N&uacute;mero de Clasificacion </b><input type="text" maxlength="10" name="nro_clasificacion" value="<?= $nro_clasificacion ?>" disabled> 
        </tr>
        <tr id="mo">
            <td>
                <?
                if (!$id_planilla) {
                    ?>  
                    <font size=+1><b>Nuevo Dato</b></font>   
                <?
                } else {
                    ?>
                    <font size=+1><b>Dato</b></font>   
                <? } ?>
            </td>
        </tr>

        <!--********************
        Comienzo del formulario
        ************************-->  

        <tr >
            <td>
                <table width=95% align="center" class="bordes">
                    <tr>
                        <td id=mo colspan="2">
                            <b> Descripci&oacute;n de la PLANILLA</b>
                        </td>
                    </tr>
                </table>



                <table width=95% align="center" class="bordes" >
                    <tr>	           
                        <td align="center" colspan="2">
                            <b> N&uacute;mero del Dato: <font size="+1" color="Red"><?= ($id_planilla) ? $id_planilla : "Nuevo Dato" ?></font> </b>           <label></label>
                        </td>
                    </tr>
                    <tr>	           
                        <td align="center" colspan="2">
                            <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>           
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-left:10px">
                            <b>Clave Beneficiario:</b><?= $clave ?><input type="hidden" value="<?= $clave ?>" name="clave" />
                        </td>
                    </tr> 
                    <tr >
                        <td style="padding-left:10px">
                            <b>Efector de Clasificacion:</b> <?
                            $sql = "select * from facturacion.smiefectores where cuie='$cuie'";
                            $res_efectores = sql($sql) or fin_pagina();
                            if ($res_efectores->fields['cuie']!='') echo $res_efectores->fields['cuie'] . '-' . $res_efectores->fields['nombreefector'];
                            else echo 'Esperando Clasificacion';?>
                        </td>
                        <td style="padding-left:10px">

                            <b>Fecha de Clasificacion: </b><? if ($fechaempadronamiento!='') echo fecha($fechaempadronamiento); else echo 'Esperando Clasificacion';?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-left:10px">
                            <b>Apellido:</b><?= $apellido ?><input type="hidden" value="<?= $apellido ?>" name="apellido" />
                            &nbsp;&nbsp;
                            <b>Nombre:</b><?= $nombre ?><input type="hidden" value="<?= $nombre ?>" name="nombre" />
                            &nbsp;&nbsp;
                            <b>Clase de Doc.:</b><? if ($clase_doc == 'P')
                                echo "Propio";
                            if ($clase_doc == 'A')
                                echo "Ajeno" ?><input type="hidden" value="<?= $clase_doc ?>" name="clase_doc" />
                            &nbsp;&nbsp;
                            <b>Tipo de Doc.:</b> <?= $tipo_doc ?><input type="hidden" value="<?= $tipo_doc ?>" name="tipo_doc" />


                        </td>
                    </tr> 
                    <tr>
                        <td colspan="2" style="padding-left:10px">

                            <b>Nro. de Doc.:</b> <?= $num_doc ?><input type="hidden" value="<?= $num_doc ?>" name="num_doc" />
                            &nbsp;&nbsp;
                            <b>Fecha de Nacimiento:</b>	<?= $fecha_nac ?><input type="hidden" value="<?= $fecha_nac ?>" name="fecha_nac" />
                            &nbsp;&nbsp;
                            <b>Edad-A&ntilde;os (a la fecha clasificacion): </b><?if ($fechaempadronamiento!='') echo substr($fechaempadronamiento, 0, 4) - substr($fecha_nac, 6, 10); 
                                                                                    else echo 'Esperando Clasificacion'?>

                            <b>Sexo:</b> <? if ($sexo == 'F')
                                echo "Femenino";
                            if ($sexo == 'M')
                                echo "Masculino"; ?>

                        </td>                  
                    </tr>     
                    <tr>
                        <td colspan="2" style="padding-left:10px">

                            <b>Telefono:</b>	<?= $telefono ?>
                            &nbsp;&nbsp;
                            <b>Provincia:</b><?= $provincia_nac ?>
                            &nbsp;&nbsp;
                            <b>Departamento:</b> <?= $departamento ?>

                        </td>                  
                    </tr>  
                    <tr>
                        <td colspan="2" style="padding-left:10px">					  
                            <b> Municipio: </b><?= $municipio ?>
                            &nbsp;&nbsp;
                            <b> Localidad: </b><?= $localidad ?>
                            &nbsp;&nbsp;
                            <b> Calle-Ruta: </b><?= $calle ?>
                            &nbsp;&nbsp;
                            <b> Nro. de Puerta: </b><?= $numero_calle ?>

                        </td>                  
                    </tr> 
                    <tr>
                        <td colspan="2" style="padding-left:10px">
                            <b> Barrio: </b><?= $barrio ?>
                            &nbsp;&nbsp;
                            <b> Mza.: </b><?= $manzana ?>
                            &nbsp;&nbsp;
                            <b> Piso: </b><?= $piso ?>
                            &nbsp;&nbsp;
                            <b> Casa-Depto: </b><?= $dpto ?>	
                        </td>                  
                    </tr>      
                </table>   
            </td>
        </tr>       


        </td>
        </tr>

		
		
        <!--********************
        Comienzo del 1body del formulario
        ************************-->  
        <td>
            <table width=95% align="center" class="bordes" style="margin-top:5px">
                <table width=95% align="center" class="bordes" >
                    <tr>
                        <td id=mo colspan="2">
                            <b> Factores de Derivacion</b>
                        </td>
                    </tr>
                    <td><table class="bordes">
                            <tr>
                                <td colspan="2" style="padding-top:5px;padding-left:10px">
                                    <b>Usted Tuvo un Infacto, ACV o problema cardiaco:</b>
                                </td>
                                <td colspan="2" style="padding-top:5px;padding-left:10px">
                                    <input type="radio" value="0" name="acv"  title="ACV" <?php if (strtoupper($acv) == 0)echo "checked"; ?>/> NO
                                    <input type="radio" value="1" name="acv"  title="ACV" <?php if (strtoupper($acv) == 1)echo "checked"; ?>/> SI
                                    <input type="radio" value="2" name="acv"  title="ACV" <?php if (strtoupper($acv) == 2)echo "checked"; ?>/> NS/NC
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top:5px;padding-left:10px">
                                    <b>Usted es Hipertenso:</b>
                                </td>
                                <td colspan="2" style="padding-top:5px;padding-left:10px">
                                    <input type="radio" value="0" name="vas_per"  title="Hipertenso" <?php if (strtoupper($vas_per) == 0)echo "checked"; ?>/> NO
                                    <input type="radio" value="1" name="vas_per"  title="Hipertenso" <?php if (strtoupper($vas_per) == 1)echo "checked"; ?>/> SI
                                    <input type="radio" value="2" name="vas_per"  title="Hipertenso" <?php if (strtoupper($vas_per) == 2)echo "checked"; ?>/> NS/NC
                                </td>
                                <td colspan="2" style="padding-top:5px;padding-left:10px">
                                    <b>Toma Medicacion:</b>
                                </td>
                                <td colspan="2" style="padding-top:5px;padding-left:10px">
                                    <input type="radio" value="0" name="car_isq"  title="Medicacion" <?php if (strtoupper($car_isq) == 0)echo "checked"; ?>/> NO
                                    <input type="radio" value="1" name="car_isq"  title="Medicacion" <?php if (strtoupper($car_isq) == 1)echo "checked"; ?>/> SI
                                    <input type="radio" value="2" name="car_isq"  title="Medicacion" <?php if (strtoupper($car_isq) == 2)echo "checked"; ?>/> NS/NC
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top:5px;padding-left:10px">
                                    <b>Familiar con Diabetes:</b>
                                </td>
                                <td colspan="2" style="padding-top:5px;padding-left:10px">
                                    <input type="radio" value="0" name="col310"  title="Diabetes" <?php if (strtoupper($col310) == 0)echo "checked"; ?>/> NO
                                    <input type="radio" value="1" name="col310"  title="Diabetes" <?php if (strtoupper($col310) == 1)echo "checked"; ?>/> SI
                                    <input type="radio" value="2" name="col310"  title="Diabetes" <?php if (strtoupper($col310) == 2)echo "checked"; ?>/> NS/NC
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top:5px;padding-left:10px">
                                    <b>Glucemia en Embarazo:</b>
                                </td>
                                <td colspan="2" style="padding-top:5px;padding-left:10px">
                                    <input type="radio" value="0" name="col_ldl"  title="Glucemia en Embarazo" <?php if (strtoupper($col_ldl) == 0)echo "checked"; ?>/> NO
                                    <input type="radio" value="1" name="col_ldl"  title="Glucemia en Embarazo" <?php if (strtoupper($col_ldl) == 1)echo "checked"; ?>/> SI
                                    <input type="radio" value="2" name="col_ldl"  title="Glucemia en Embarazo" <?php if (strtoupper($col_ldl) == 2)echo "checked"; ?>/> NS/NC
                                </td>
                                <td colspan="2" style="padding-top:5px;padding-left:10px">
                                    <b>Hijo Mayor a 4 kg:</b>
                                </td>
                                <td colspan="2" style="padding-top:5px;padding-left:10px">
                                    <input type="radio" value="0" name="ct_hdl"  title="Mayor a 4 kg" <?php if (strtoupper($ct_hdl) == 0)echo "checked"; ?>/> NO
                                    <input type="radio" value="1" name="ct_hdl"  title="Mayor a 4 kg" <?php if (strtoupper($ct_hdl) == 1)echo "checked"; ?>/> SI
                                    <input type="radio" value="2" name="ct_hdl"  title="Mayor a 4 kg" <?php if (strtoupper($ct_hdl) == 2)echo "checked"; ?>/> NS/NC
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top:5px;padding-left:10px">
                                    <b>Perimetro Abdominal en cm.:</b>
                                </td>
                                <td colspan="2" style="padding-top:5px;padding-left:10px">
                                    <input type="text" value="<?=$pres_art ?>" name="pres_art" size="3" style="font-size:9px;" maxlength="3" title="Perimetro Abdominal"/>
                                </td>
                            </tr>
                    </table></td>
                </table>

                <!--********************
                    Comienzo del 2body del formulario
                    ************************-->  

                <tr>
                    <td>                       
                        <table width=95% align="center" class="bordes" style="margin-top:5px">
                            <tr>
                                <td colspan="2">
                            <tr>
                                <td align="center" id='mo' colspan="2"><b>Clasificaci&oacute;n con utilizaci&oacute;n de tabla de predicci&oacute;n RCVG</b></td>
                            </tr>
                            <tr>
                                <td align="left" style="padding-left:10px">
                                    <div style="display:inline;">
                                        <b title="Sin DMT">Sin DMT</b> 
                                        <input type="radio" value="0" name="dmt" <?php if (strtoupper($dmt) == "0")
                                echo "checked"; ?> title="Sin DMT"  <?= $desabil ?>/>
                                        &nbsp;
                                        <b title="DMT 1">DMT1</b> 
                                        <input type="radio" value="1" name="dmt" <?php if (strtoupper($dmt) == "1")
                                echo "checked"; ?> title="DMT 1"  <?= $desabil ?>/>
                                        &nbsp;
                                        <b title="DMT 2">DMT2</b>
                                        <input style="margin-right:5px" type="radio" value="2" name="dmt" <?php if (strtoupper($dmt) == "2")
                                echo "checked"; ?>  title="DMT 2" <?= $desabil ?>/>
                                        <b title="Hipertension Arterial">HTA</b>
                                        <input type="checkbox" name="hta" <?php if (strtoupper($hta) == 1)
                                echo "checked"; ?>  title="Hipertension Arterial"  <?= $desabil ?>/>
                                        <b title="Control de la Presion Arterial Sistolica">TA Sist</b>
                                        <input type="text" value="<?= $ta_sist ?>" name="ta_sist" size="3" style="font-size:9px;" maxlength="3" title="Control de la Presion Arterial Sistolica" <?= $desabil ?>/>
                                        &nbsp;
                                        <b title="Control de la Presion Arterial Diastolica">TA Diast</b>
                                        <input type="text" value="<?= $ta_diast ?>" name="ta_diast" size="3" style="font-size:9px;" maxlength="3" title="Control de la Presion Arterial Diastolica"<?= $desabil ?>/>
                                        &nbsp;
                                        <b>Tabaquismo</b>
                                        <input type="checkbox" name="tabaquismo"<?php if (strtoupper($tabaquismo) == 1)
                                echo "checked"; ?>  <?= $desabil ?>/>
                                        <input type="hidden" name="col_tot" value="0" id="col_tot">
                                    </div>
							
							</table></td></tr>
                </tr>

                <tr>
            <td><table width=95% align="center" class="bordes" style="margin-top:5px">
                    <tr><td><div style="background-color:#A2A2A2"><b>Riesgo Cardiovascular Global</b>                                
                                <input style="margin-left:10px" type="radio" value="bajo" name="riesgo_global" <?php if (strtoupper($riesgo_global) == "BAJO")                                    
                                echo "checked"; ?> title="Riesgo Bajo"  <?= $desabil ?>/> Bajo < 10%
                                <input style="margin-left:10px" type="radio" value="mode" name="riesgo_global" <?php if (strtoupper($riesgo_global) == "MODE")
                                echo "checked"; ?> title="Riesgo Moderado"  <?= $desabil ?>/> Moderado 10% a < 20% 
                                <input style="margin-left:10px" type="radio" value="alto" name="riesgo_global" <?php if (strtoupper($riesgo_global) == "ALTO")
                                echo "checked"; ?> title="Riesgo Alto"  <?= $desabil ?>/> Alto 20% a < 30% 
                                <input style="margin-left:10px" type="radio" value="malto" name="riesgo_global" <?php if (strtoupper($riesgo_global) == "MALTO")
                                echo "checked"; ?> title="Riesgo Muy Alto"  <?= $desabil ?>/> Muy Alto > 30% 
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:5px;padding-left:15px" align="left"><? if($fecha_clasificacion=='') $fecha_clasificacion = date("d/m/Y"); ?>
                            <b>Fecha de clasificaci&oacute;n: </b><input type='text' id='fecha_clasificacion' name='fecha_clasificacion' value='<?= Fecha($fecha_clasificacion); ?>' size=15 onKeyUp="mascara(this,'/',patron,true);" onblur="esFechaValida(this);" <?= $desabil ?>/>
                            <?=link_calendario("fecha_clasificacion"); ?>
                                                       
                            <? if($fecha_prox_seguimiento=='') $fecha_prox_seguimiento = ''; ?>
                            <!--<b>Fecha de Proximo Seguimiento: </b><input type='text' id='fecha_prox_seguimiento' name='fecha_prox_seguimiento' value='<?= Fecha($fecha_prox_seguimiento); ?>' size=15 onKeyUp="mascara(this,'/',patron,true);" onblur="esFechaValida(this);" <?= $desabil ?>/>-->
                            <?//=link_calendario("fecha_prox_seguimiento"); ?>
                            <input type="hidden" name="fecha_prox_seguimiento" value="<?= Fecha($fecha_prox_seguimiento); ?>" id="fecha_prox_seguimiento">
                        </td>
                            
                   </tr>
                </table></td>
        </tr>

        <tr>
        <td><table width=95% align="center" class="bordes" style="margin-top:5px">
        <tr>               
           <td align="center" id="mo" colspan="2">
            <b> Efector Clasificacion </b>
           </td>
         </tr>
         
         <tr>
            <td align="right" width="20%" >
                <b>Efector:</b>
            </td>
            <td align="left" width="30%" colspan="2">               
             <select name=cuie Style="width=300px" 
                onKeypress="buscar_combo(this);"
                onblur="borrar_buffer();"
                onchange="borrar_buffer();" 
                 
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
        </td>
        </tr>


        <tr>
        <td>
        <table width=95% align="center" class="bordes" style="margin-top:5px">
            <tr align="center" id="sub_tabla">
                <td>    
                    <b>Guarda Planilla</b>
                </td>
            </tr>


            <tr align="center">
                
                <td>
                    <input type='submit' name='guardar' value='Guardar Planilla' onclick="return control_nuevos()" style='width:200px; height:35px'
                           title="Guardar datos de la Planilla" <?= $desabil ?>/>
                </td>
            </tr>
            
             <tr><td colspan="2"><table width=100% align="center" class="bordes">
                <tr align="center">
                 <td >
                <input type=button name="cerrar" value="Cerrar" onclick="window.close()">     
                </td>
                 </tr>
        </table>
        </td>
        </tr>   
  
</table> 


</table>
</td>
</tr>
        <br>

        </form>

            <?
if(($accion2=="Se actualizo la Planilla")||($accion2=="Se guardo la Planilla Clasificacion Nro. " . $nro_clasificacion ." y la Prestacion: " . $id_prestacion)){
    sleep(2);
    echo('<script>window.close();</script>');
    } ?> 
<?= fin_pagina(); // aca termino ?>





       
