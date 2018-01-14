<?
require_once ("../../config.php");
include_once('lib_inscripcion.php');

Header('Content-Type: text/html; charset=LATIN1');

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

($_POST['fecha_nac']=='')?$fecha_nac=date("d/m/Y"):$fecha_nac=$_POST['fecha_nac'];
($_POST['fecha_diagnostico_embarazo']=='')?$fecha_diagnostico_embarazo=date("d/m/Y"):$fecha_diagnostico_embarazo=$_POST['fecha_diagnostico_embarazo'];
($_POST['fecha_probable_parto']=='')?$fecha_probable_parto=date("d/m/Y"):$fecha_probable_parto=$_POST['fecha_probable_parto'];
($_POST['fecha_efectiva_parto']=='')?$fecha_efectiva_parto=date("d/m/Y"):$fecha_efectiva_parto=$_POST['fecha_efectiva_parto'];
($_POST['fecha_inscripcion']=='')?$fecha_inscripcion=date("d/m/Y"):$fecha_inscripcion=$_POST['fecha_inscripcion'];
$usuario1=$_ses_user['id'];
if($id_planilla){
	$queryCategoria="SELECT beneficiarios.*, efe_conv.nombre, efe_conv.cuie
			FROM uad.beneficiarios
			left join nacer.efe_conv on beneficiarios.cuie_ea=efe_conv.cuie 
  	where id_beneficiarios=$id_planilla";

	$resultado=sql($queryCategoria, "Error al traer el Comprobantes") or fin_pagina();
	$id_categoria=$resultado->fields['id_categoria'];
	$semanas_embarazo=$resultado->fields['semanas_embarazo'];
	$pais_nac=$resultado->fields['pais_nac'];
	$provincia_nac=$resultado->fields['provincia_nac'];
   	$localidad_proc=$resultado->fields['localidad_nac'];
   	$departamento=$resultado->fields['departamento'];
   	$localidad=$resultado->fields['localidad'];
   	$municipio=$resultado->fields['municipio'];
   	$barrio=$resultado->fields['barrio'];
   	$indigena= $resultado->fields['indigena'];
   	$id_tribu= $resultado->fields['id_tribu'];
   	$id_lengua= $resultado->fields['id_lengua'];
   	$responsable=$resultado->fields['responsable'];
   	$menor_convive_con_adulto=$resultado->fields['menor_convive_con_adulto'];
   	$tipo_doc_madre=$resultado->fields['tipo_doc_madre'];
   	$nro_doc_madre=$resultado->fields['nro_doc_madre'];
   	$apellido_madre=$resultado->fields['apellido_madre'];
   	$nombre_madre=$resultado->fields['nombre_madre'];
   	$sexo=$resultado->fields['sexo'];
   	$clave_beneficiario=$resultado->fields['clave_beneficiario'];
   	$clase_doc=$resultado->fields['clase_documento_benef'];
   	$trans=$resultado->fields['tipo_transaccion'];
   	$estado_envio=$resultado->fields['estado_envio'];
   	
   	if ($trans == 'B'){
   		$trans="Borrado";
   	}
   	
}

if(($id_categoria=='-1')or($id_categoria=='7')or($id_categoria=='8')){
	$embarazada= none; 
	$datos_resp= none;
}
if ($id_categoria=='1'){
	$embarazada=inline;
	$datos_resp=none;
	$puerpera=none;
	if(! $id_planilla){
		$semanas_embarazo=$_POST['semanas_embarazo'];
	}
}else {
	$embarazada=none;
	$datos_resp=none;
	$puerpera=none;
}

if ($id_categoria == '2'){
	$embarazada=none;
	$datos_resp=none;
	$puerpera=inline;
	}

if(($id_categoria=='3')||($id_categoria=='4')){ 
	$datos_resp=inline;
	$embarazada=none;
	$puerpera=none;
}

	// Muestra Cambio de Domicilio al momento de hacer una modificacion solamente.
if ($tipo_transaccion != 'M'){
	$cdomi1=none;
} // FIN

if ($_POST['b']=="b"){
	$cdomi1=inline;
}
// Transferir a los beneficiarios de la vieja ficha de inscripcion a la nueva.
if ($_POST['transferir']=="Transferir"){
     $fecha_carga=date("Y-m-d H:m:s");
     
	 $db->StartTrans();
	 if (($id_categoria == '1') || ($id_categoria == '2')) {
	 $query = "update uad.beneficiarios set tipo_ficha='2', activo='1', estado_envio='n', id_categoria='6', tipo_transaccion='M', fecha_carga='$fecha_carga' where id_beneficiarios=".$id_planilla;
	 sql($query, "Error al transferir el muleto") or fin_pagina();
	 }
 	  if (($id_categoria == '3') || ($id_categoria == '4')) {
	 $query = "update uad.beneficiarios set tipo_ficha='2', activo='1', estado_envio='n', id_categoria='5', tipo_transaccion='M', fecha_carga='$fecha_carga' where id_beneficiarios=".$id_planilla;
	 sql($query, "Error al transferir el muleto") or fin_pagina();
	 }  
	 $db->CompleteTrans();    
   	 $accion="Los datos se transfirieron correctamente";
} // FIN

// Update Beneficiarios
if ($_POST['guardar_editar']=="Guardar"){
		
   $db->StartTrans();
  
   
   $fecha_carga=date("Y-m-d H:m:s");
   $usuario=$_ses_user['login'];
   /*$usuario = substr($usuario,0,9);*/
   
   	$fecha_nac=Fecha_db($fecha_nac);
   	$fecha_diagnostico_embarazo=Fecha_db($fecha_diagnostico_embarazo);
   	$semanas_embarazo=$_POST['semanas_embarazo'];
   	//////////////
   	$clave_beneficiario=$_POST['clave_beneficiario'];
   	$sexo=$_POST['sexo'];
   	$pais_nac=$_POST['pais_nac'];
   	$localidad_proc=$_POST['localidad_proc'];
    $provincia_nac=$_POST['provincia_nac'];
    $indigena=$_POST['indigena'];
    $id_tribu=$_POST['id_tribu'];
    $id_lengua= $_POST['id_lengua'];
    $departamento=$_POST['departamento'];
   	$localidad=$_POST['localidad'];
   	$municipio=$_POST['municipio'];
   	$barrio=$_POST['barrio'];
   	$id_categoria=$_POST['id_categoria'];
	$responsable=$_POST['responsable'];
	$menor_convive_con_adulto=$_POST['menor_convive_con_adulto'];
	$tipo_doc_madre=$_POST['tipo_doc_madre'];
	$nro_doc_madre=$_POST['nro_doc_madre'];
	$apellido_madre=$_POST['apellido_madre'];
	$nombre_madre=$_POST['nombre_madre'];
	$clase_doc=$_POST['clase_doc'];
	
	$fecha_probable_parto=Fecha_db($fecha_probable_parto);
	$fecha_efectiva_parto=Fecha_db($fecha_efectiva_parto);
	$fecha_inscripcion=Fecha_db($fecha_inscripcion);
 
	
  if($responsable =='MADRE'){
   			$query = "update uad.beneficiarios set 
             cuie_ea='$cuie', nombre_benef=upper('$nombre'),
             apellido_benef=upper('$apellido'),
             numero_doc='$num_doc',clase_documento_benef=upper('$clase_doc'),fecha_nacimiento_benef='$fecha_nac',
             indigena=upper('$indigena'),id_tribu=$id_tribu,id_lengua=$id_lengua,
             id_categoria=$id_categoria,
             calle=upper('$calle'),numero_calle='$numero_calle',piso='$piso',dpto=upper('$dpto'),manzana='$manzana',entre_calle_1=upper('$entre_calle_1'),
             entre_calle_2=upper('$entre_calle_2'), departamento=upper('$departamenton'), localidad=upper('$localidadn'), municipio=upper('$municipion'), 
             barrio=upper('$barrion'),telefono='$telefono',cod_pos='$cod_posn',observaciones=upper('$observaciones'),fecha_inscripcion='$fecha_inscripcion',
             provincia_nac=upper('$provincia_nacn'),localidad_nac=upper('$localidad_procn'),pais_nac=upper('$pais_nacn'),
             tipo_ficha='1',responsable=upper('$responsable'), menor_convive_con_adulto=upper('$menor_convive_con_adulto'), 
             nombre_madre=upper('$nombre_madre'),fecha_carga='$fecha_carga', usuario_carga=upper('$usuario'),
             apellido_madre=upper('$apellido_madre'), nro_doc_madre='$nro_doc_madre', 
             tipo_doc_madre=upper('$tipo_doc_madre'),nombre_padre='',apellido_padre='', 
             nro_doc_padre='',tipo_doc_padre='',nombre_tutor='', apellido_tutor='', 
             nro_doc_tutor='',tipo_doc_tutor='', tipo_transaccion='M', estado_envio='n',activo='1',sexo=upper('$sexo')
                       
             where id_beneficiarios=".$id_planilla;
   }elseif($responsable =='PADRE'){
   		$query = "update uad.beneficiarios set 
             cuie_ea='$cuie', nombre_benef=upper('$nombre'),
             apellido_benef=upper('$apellido'),
             numero_doc='$num_doc',clase_documento_benef=upper('$clase_doc'),fecha_nacimiento_benef='$fecha_nac',
             indigena=upper('$indigena'),id_tribu=$id_tribu,id_lengua=$id_lengua,
             id_categoria=$id_categoria,
             calle=upper('$calle'),numero_calle='$numero_calle',piso='$piso',dpto=upper('$dpto'),manzana='$manzana',entre_calle_1=upper('$entre_calle_1'),
             entre_calle_2=upper('$entre_calle_2'), departamento=upper('$departamenton'), localidad=upper('$localidadn'), municipio=upper('$municipion'), 
             barrio=upper('$barrion'),telefono='$telefono',cod_pos='$cod_posn',observaciones=upper('$observaciones'),fecha_inscripcion='$fecha_inscripcion',
             provincia_nac=upper('$provincia_nacn'),localidad_nac=upper('$localidad_procn'),pais_nac=upper('$pais_nacn'),
             tipo_ficha='1',responsable=upper('$responsable'), menor_convive_con_adulto=upper('$menor_convive_con_adulto'), 
             nombre_padre=upper('$nombre_madre'),fecha_carga='$fecha_carga', usuario_carga=upper('$usuario'),
             apellido_padre=upper('$apellido_madre'), nro_doc_padre='$nro_doc_madre', 
             tipo_doc_padre=upper('$tipo_doc_madre'),nombre_madre='', 
             apellido_madre='', nro_doc_madre='',tipo_doc_madre='',nombre_tutor='', 
             apellido_tutor='', nro_doc_tutor='',tipo_doc_tutor='', tipo_transaccion='M', estado_envio='n',activo='1',sexo=upper('$sexo')                        
              
         where id_beneficiarios=".$id_planilla;
  		 }elseif($responsable =='TUTOR') {
   			$query = "update uad.beneficiarios set 
             cuie_ea='$cuie', nombre_benef=upper('$nombre'),
             apellido_benef=upper('$apellido'),
             numero_doc='$num_doc',clase_documento_benef=upper('$clase_doc'),fecha_nacimiento_benef='$fecha_nac',
             indigena=upper('$indigena'),id_tribu=$id_tribu,id_lengua=$id_lengua,
             id_categoria=$id_categoria,
             calle=upper('$calle'),numero_calle='$numero_calle',piso='$piso',dpto=upper('$dpto'),manzana='$manzana',entre_calle_1=upper('$entre_calle_1'),
             entre_calle_2=upper('$entre_calle_2'), departamento=upper('$departamenton'), localidad=upper('$localidadn'), municipio=upper('$municipion'), 
             barrio=upper('$barrion'),telefono='$telefono',cod_pos='$cod_posn',observaciones=upper('$observaciones'),fecha_inscripcion='$fecha_inscripcion',
             provincia_nac=upper('$provincia_nacn'),localidad_nac=upper('$localidad_procn'),pais_nac=upper('$pais_nacn'),
             tipo_ficha='1',responsable=upper('$responsable'), menor_convive_con_adulto=upper('$menor_convive_con_adulto'), 
             nombre_tutor=upper('$nombre_madre'),fecha_carga='$fecha_carga', usuario_carga=upper('$usuario'),
             apellido_tutor=upper('$apellido_madre'), nro_doc_tutor='$nro_doc_madre', 
             tipo_doc_tutor=upper('$tipo_doc_madre'),nombre_madre='', 
             apellido_madre='', nro_doc_madre='',tipo_doc_madre='',nombre_padre='',
             apellido_padre='', nro_doc_padre='',tipo_doc_padre='', tipo_transaccion='M', estado_envio='n',activo='1',sexo=upper('$sexo')                              
                       
             where id_beneficiarios=".$id_planilla;
  		 }
  	
	if (($id_categoria=='1') && ($estado_envio== 'n'))  {
   			$query = "update uad.beneficiarios set 
             cuie_ea='$cuie', nombre_benef=upper('$nombre'),
             apellido_benef=upper('$apellido'),
             numero_doc='$num_doc',clase_documento_benef=upper('$clase_doc'),fecha_nacimiento_benef='$fecha_nac',
             indigena=upper('$indigena'),id_tribu=$id_tribu,id_lengua=$id_lengua,id_categoria=$id_categoria,
			 fecha_diagnostico_embarazo='$fecha_diagnostico_embarazo',semanas_embarazo='$semanas_embarazo',fecha_probable_parto='$fecha_probable_parto',
			 calle=upper('$calle'),numero_calle='$numero_calle',piso='$piso',dpto=upper('$dpto'),manzana='$manzana',
			 entre_calle_1=upper('$entre_calle_1'),entre_calle_2=upper('$entre_calle_2'), departamento=upper('$departamenton'), 
			 localidad=upper('$localidadn'), municipio=upper('$municipion'), barrio=upper('$barrion'),
			 telefono='$telefono',cod_pos='$cod_posn',observaciones=upper('$observaciones'),fecha_carga='$fecha_carga', usuario_carga=upper('$usuario'),
			 fecha_inscripcion='$fecha_inscripcion', provincia_nac=upper('$provincia_nacn'), localidad_nac=upper('$localidad_procn'), pais_nac=upper('$pais_nacn'), 
			 tipo_ficha='1', tipo_transaccion='A', estado_envio='n',activo='1',sexo=upper('$sexo')
                       
             where id_beneficiarios=".$id_planilla;
  		 }elseif (($id_categoria=='2') && ($estado_envio== 'n')) {
   			$query = "update uad.beneficiarios set 
             cuie_ea='$cuie', nombre_benef=upper('$nombre'),
             apellido_benef=upper('$apellido'),
             numero_doc='$num_doc',clase_documento_benef=upper('$clase_doc'),fecha_nacimiento_benef='$fecha_nac',
             indigena=upper('$indigena'),id_tribu=$id_tribu,id_lengua=$id_lengua,id_categoria=$id_categoria,
			 fecha_efectiva_parto='$fecha_efectiva_parto',
             calle=upper('$calle'),numero_calle='$numero_calle',piso='$piso',dpto=upper('$dpto'),manzana='$manzana',
			 entre_calle_1=upper('$entre_calle_1'),entre_calle_2=upper('$entre_calle_2'), departamento=upper('$departamenton'), 
			 localidad=upper('$localidadn'), municipio=upper('$municipion'), barrio=upper('$barrion'),
			 telefono='$telefono',cod_pos='$cod_posn',observaciones=upper('$observaciones'),fecha_carga='$fecha_carga', usuario_carga=upper('$usuario'),
			 fecha_inscripcion='$fecha_inscripcion', provincia_nac=upper('$provincia_nacn'), localidad_nac=upper('$localidad_procn'), pais_nac=upper('$pais_nacn'), 
			 tipo_ficha='1', tipo_transaccion='A', estado_envio='n',activo='1',sexo=upper('$sexo')
                       
             where id_beneficiarios=".$id_planilla;
  		 }		
  		    
if(($responsable =='MADRE') && ($estado_envio== 'e')){
   			$query = "update uad.beneficiarios set 
             cuie_ea='$cuie', nombre_benef=upper('$nombre'),
             apellido_benef=upper('$apellido'),
             numero_doc='$num_doc',clase_documento_benef=upper('$clase_doc'),fecha_nacimiento_benef='$fecha_nac',
             indigena=upper('$indigena'),id_tribu=$id_tribu,id_lengua=$id_lengua,
             id_categoria=$id_categoria,
             calle=upper('$calle'),numero_calle='$numero_calle',piso='$piso',dpto=upper('$dpto'),manzana='$manzana',entre_calle_1=upper('$entre_calle_1'),
             entre_calle_2=upper('$entre_calle_2'), departamento=upper('$departamenton'), localidad=upper('$localidadn'), municipio=upper('$municipion'), 
             barrio=upper('$barrion'),telefono='$telefono',cod_pos='$cod_posn',observaciones=upper('$observaciones'),fecha_inscripcion='$fecha_inscripcion',
             provincia_nac=upper('$provincia_nacn'),localidad_nac=upper('$localidad_procn'),pais_nac=upper('$pais_nacn'),
             tipo_ficha='1',responsable=upper('$responsable'), menor_convive_con_adulto=upper('$menor_convive_con_adulto'), 
             nombre_madre=upper('$nombre_madre'),fecha_carga='$fecha_carga', usuario_carga=upper('$usuario'),
             apellido_madre=upper('$apellido_madre'), nro_doc_madre='$nro_doc_madre', 
             tipo_doc_madre=upper('$tipo_doc_madre'),nombre_padre='',apellido_padre='', 
             nro_doc_padre='',tipo_doc_padre='',nombre_tutor='', apellido_tutor='', 
             nro_doc_tutor='',tipo_doc_tutor='', tipo_transaccion='M', estado_envio='n',activo='1',sexo=upper('$sexo')
                       
             where id_beneficiarios=".$id_planilla;
   }elseif(($responsable =='PADRE') && ($estado_envio== 'e')){
   		$query = "update uad.beneficiarios set 
             cuie_ea='$cuie', nombre_benef=upper('$nombre'),
             apellido_benef=upper('$apellido'),
             numero_doc='$num_doc',clase_documento_benef=upper('$clase_doc'),fecha_nacimiento_benef='$fecha_nac',
             indigena=upper('$indigena'),id_tribu=$id_tribu,id_lengua=$id_lengua,
             id_categoria=$id_categoria,
             calle=upper('$calle'),numero_calle='$numero_calle',piso='$piso',dpto=upper('$dpto'),manzana='$manzana',entre_calle_1=upper('$entre_calle_1'),
             entre_calle_2=upper('$entre_calle_2'), departamento=upper('$departamenton'), localidad=upper('$localidadn'), municipio=upper('$municipion'), 
             barrio=upper('$barrion'),telefono='$telefono',cod_pos='$cod_posn',observaciones=upper('$observaciones'),fecha_inscripcion='$fecha_inscripcion',
             provincia_nac=upper('$provincia_nacn'),localidad_nac=upper('$localidad_procn'),pais_nac=upper('$pais_nacn'),
             tipo_ficha='1',responsable=upper('$responsable'), menor_convive_con_adulto=upper('$menor_convive_con_adulto'), 
             nombre_padre=upper('$nombre_madre'),fecha_carga='$fecha_carga', usuario_carga=upper('$usuario'),
             apellido_padre=upper('$apellido_madre'), nro_doc_padre='$nro_doc_madre', 
             tipo_doc_padre=upper('$tipo_doc_madre'),nombre_madre='', 
             apellido_madre='', nro_doc_madre='',tipo_doc_madre='',nombre_tutor='', 
             apellido_tutor='', nro_doc_tutor='',tipo_doc_tutor='', tipo_transaccion='M', estado_envio='n',activo='1',sexo=upper('$sexo')                        
              
         where id_beneficiarios=".$id_planilla;
  		 }elseif(($responsable =='TUTOR') && ($estado_envio== 'e')) {
   			$query = "update uad.beneficiarios set 
             cuie_ea='$cuie', nombre_benef=upper('$nombre'),
             apellido_benef=upper('$apellido'),
             numero_doc='$num_doc',clase_documento_benef=upper('$clase_doc'),fecha_nacimiento_benef='$fecha_nac',
             indigena=upper('$indigena'),id_tribu=$id_tribu,id_lengua=$id_lengua,
             id_categoria=$id_categoria,
             calle=upper('$calle'),numero_calle='$numero_calle',piso='$piso',dpto=upper('$dpto'),manzana='$manzana',entre_calle_1=upper('$entre_calle_1'),
             entre_calle_2=upper('$entre_calle_2'), departamento=upper('$departamenton'), localidad=upper('$localidadn'), municipio=upper('$municipion'), 
             barrio=upper('$barrion'),telefono='$telefono',cod_pos='$cod_posn',observaciones=upper('$observaciones'),fecha_inscripcion='$fecha_inscripcion',
             provincia_nac=upper('$provincia_nacn'),localidad_nac=upper('$localidad_procn'),pais_nac=upper('$pais_nacn'),
             tipo_ficha='1',responsable=upper('$responsable'), menor_convive_con_adulto=upper('$menor_convive_con_adulto'), 
             nombre_tutor=upper('$nombre_madre'),fecha_carga='$fecha_carga', usuario_carga=upper('$usuario'),
             apellido_tutor=upper('$apellido_madre'), nro_doc_tutor='$nro_doc_madre', 
             tipo_doc_tutor=upper('$tipo_doc_madre'),nombre_madre='', 
             apellido_madre='', nro_doc_madre='',tipo_doc_madre='',nombre_padre='', 
             apellido_padre='', nro_doc_padre='',tipo_doc_padre='', tipo_transaccion='M', estado_envio='n',activo='1',sexo=upper('$sexo')                              
                       
             where id_beneficiarios=".$id_planilla;
  		 }
  		
		if (($id_categoria=='1')&& ($estado_envio=='e'))  {
   			$query = "update uad.beneficiarios set 
             cuie_ea='$cuie', nombre_benef=upper('$nombre'),
             apellido_benef=upper('$apellido'),
             numero_doc='$num_doc',clase_documento_benef=upper('$clase_doc'),fecha_nacimiento_benef='$fecha_nac',
             indigena=upper('$indigena'),id_tribu=$id_tribu,id_lengua=$id_lengua,id_categoria=$id_categoria,
			 fecha_diagnostico_embarazo='$fecha_diagnostico_embarazo',semanas_embarazo='$semanas_embarazo',fecha_probable_parto='$fecha_probable_parto',
			 fecha_efectiva_parto='1899-12-30',
			 calle=upper('$calle'),numero_calle='$numero_calle',piso='$piso',dpto=upper('$dpto'),manzana='$manzana',
			 entre_calle_1=upper('$entre_calle_1'),entre_calle_2=upper('$entre_calle_2'), departamento=upper('$departamenton'), 
			 localidad=upper('$localidadn'), municipio=upper('$municipion'), barrio=upper('$barrion'),
			 telefono='$telefono',cod_pos='$cod_posn',observaciones=upper('$observaciones'),fecha_carga='$fecha_carga', usuario_carga=upper('$usuario'),
			 fecha_inscripcion='$fecha_inscripcion', provincia_nac=upper('$provincia_nacn'), localidad_nac=upper('$localidad_procn'), pais_nac=upper('$pais_nacn'), 
			 tipo_ficha='1', tipo_transaccion='M', estado_envio='n',activo='1',sexo=upper('$sexo')
                       
             where id_beneficiarios=".$id_planilla;
  		 }elseif (($id_categoria=='2')&& ($estado_envio=='e')) {
   			$query = "update uad.beneficiarios set 
             cuie_ea='$cuie', nombre_benef=upper('$nombre'),
             apellido_benef=upper('$apellido'),
             numero_doc='$num_doc',clase_documento_benef=upper('$clase_doc'),fecha_nacimiento_benef='$fecha_nac',
             indigena=upper('$indigena'),id_tribu=$id_tribu,id_lengua=$id_lengua,id_categoria=$id_categoria,
             fecha_diagnostico_embarazo='1899-12-30',semanas_embarazo='0',fecha_probable_parto='1899-12-30',
			 fecha_efectiva_parto='$fecha_efectiva_parto',
             calle=upper('$calle'),numero_calle='$numero_calle',piso='$piso',dpto=upper('$dpto'),manzana='$manzana',
			 entre_calle_1=upper('$entre_calle_1'),entre_calle_2=upper('$entre_calle_2'), departamento=upper('$departamenton'), 
			 localidad=upper('$localidadn'), municipio=upper('$municipion'), barrio=upper('$barrion'),
			 telefono='$telefono',cod_pos='$cod_posn',observaciones=upper('$observaciones'),fecha_carga='$fecha_carga', usuario_carga=upper('$usuario'),
			 fecha_inscripcion='$fecha_inscripcion', provincia_nac=upper('$provincia_nacn'), localidad_nac=upper('$localidad_procn'), pais_nac=upper('$pais_nacn'), 
			 tipo_ficha='1', tipo_transaccion='M', estado_envio='n',activo='1',sexo=upper('$sexo')
                       
             where id_beneficiarios=".$id_planilla;  		 
  		 }
  		 if ((($id_categoria=='7')||($id_categoria=='8'))&& ($estado_envio=='e'))  {
   			$query = "update uad.beneficiarios set 
             cuie_ea='$cuie', nombre_benef=upper('$nombre'),
             apellido_benef=upper('$apellido'),
             numero_doc='$num_doc',clase_documento_benef=upper('$clase_doc'),fecha_nacimiento_benef='$fecha_nac',
             indigena=upper('$indigena'),id_tribu=$id_tribu,id_lengua=$id_lengua,id_categoria=$id_categoria,
			 calle=upper('$calle'),numero_calle='$numero_calle',piso='$piso',dpto=upper('$dpto'),manzana='$manzana',
			 entre_calle_1=upper('$entre_calle_1'),entre_calle_2=upper('$entre_calle_2'), departamento=upper('$departamenton'), 
			 localidad=upper('$localidadn'), municipio=upper('$municipion'), barrio=upper('$barrion'),
			 telefono='$telefono',cod_pos='$cod_posn',observaciones=upper('$observaciones'),fecha_carga='$fecha_carga', usuario_carga=upper('$usuario'),
			 fecha_inscripcion='$fecha_inscripcion', provincia_nac=upper('$provincia_nacn'), localidad_nac=upper('$localidad_procn'), pais_nac=upper('$pais_nacn'), 
			 tipo_ficha='1', tipo_transaccion='M', estado_envio='n',activo='1',sexo=upper('$sexo')
                       
             where id_beneficiarios=".$id_planilla;
  		 }elseif ((($id_categoria=='7')||($id_categoria=='8'))&& ($estado_envio=='n')) {
   			$query = "update uad.beneficiarios set 
             cuie_ea='$cuie', nombre_benef=upper('$nombre'),
             apellido_benef=upper('$apellido'),
             numero_doc='$num_doc',clase_documento_benef=upper('$clase_doc'),fecha_nacimiento_benef='$fecha_nac',
             indigena=upper('$indigena'),id_tribu=$id_tribu,id_lengua=$id_lengua,id_categoria=$id_categoria,
             calle=upper('$calle'),numero_calle='$numero_calle',piso='$piso',dpto=upper('$dpto'),manzana='$manzana',
			 entre_calle_1=upper('$entre_calle_1'),entre_calle_2=upper('$entre_calle_2'), departamento=upper('$departamenton'), 
			 localidad=upper('$localidadn'), municipio=upper('$municipion'), barrio=upper('$barrion'),
			 telefono='$telefono',cod_pos='$cod_posn',observaciones=upper('$observaciones'),fecha_carga='$fecha_carga', usuario_carga=upper('$usuario'),
			 fecha_inscripcion='$fecha_inscripcion', provincia_nac=upper('$provincia_nacn'), localidad_nac=upper('$localidad_procn'), pais_nac=upper('$pais_nacn'), 
			 tipo_ficha='1', tipo_transaccion='A', estado_envio='n',activo='1',sexo=upper('$sexo')
    
             where id_beneficiarios=".$id_planilla;  		 
  		 }
  		 
   sql($query, "Error al insertar/actualizar el muleto") or fin_pagina();   
	 
   $db->CompleteTrans();    
   $accion="Los datos se actualizaron";
   $cambiodom = 'N';
		
} //FIN Update

//Insert Beneficiarios
if ($_POST['guardar']=="Guardar Planilla"){
	$fecha_nac1=fecha_db($fecha_nac);
$sql1="select * from uad.beneficiarios	  
	 	where numero_doc='$num_doc' and fecha_nacimiento_benef = '$fecha_nac1'";
		$res_extra1=sql($sql1, "Error al verificar la existencia del beneficiario") or fin_pagina();
		/*if (($res_extra1->recordcount()>0) && ($res_extra1->fields['clase_documento_benef'] == 'P'))*/
		
		if ($res_extra1->RecordCount()!=EOF)
		{
			$accion="El Beneficiario ya esta Empadronado";
			
			$tipo_transaccion='M';
			$id_planilla=$res_extra1->fields['id_beneficiarios'];       
		    $clave_beneficiario=$res_extra1->fields['clave_beneficiario'];
			$apellido=$res_extra1->fields['apellido_benef'];
		 	$nombre=$res_extra1->fields['nombre_benef'];
		 	$clase_doc=$res_extra1->fields['clase_documento_benef'];
		 	$tipo_doc=$res_extra1->fields['tipo_documento'];
		 	$sexo=$res_extra1->fields['sexo'];
		 	$fecha_nac=Fecha($res_extra1->fields['fecha_nacimiento_benef']);
		 	$pais_nac=$res_extra1->fields['pais_nac'];
		 	$provincia_nac=$res_extra1->fields['provincia_nac'];
		 	$localidad_proc=$res_extra1->fields['localidad_nac'];
		 	$indigena= $res_extra1->fields['indigena'];
		 	$id_tribu= $res_extra1->fields['id_tribu'];
		 	$id_lengua= $res_extra1->fields['id_lengua'];
		 	$id_categoria=$res_extra1->fields['id_categoria'];
			
		 	if(($id_categoria=='3')||($id_categoria=='4')){ 
			$datos_resp=inline;
			$embarazada=none;
			$responsable=$res_extra1->fields['responsable'];
				if ($responsable=='MADRE'){
					$menor_convive_con_adulto=$res_extra1->fields['menor_convive_con_adulto'];
		   			$tipo_doc_madre=$res_extra1->fields['tipo_doc_madre'];
		   			$nro_doc_madre=$res_extra1->fields['nro_doc_madre'];
		   			$apellido_madre=$res_extra1->fields['apellido_madre'];
		   			$nombre_madre=$res_extra1->fields['nombre_madre'];
				}elseif ($responsable=='PADRE'){
					$menor_convive_con_adulto=$res_extra1->fields['menor_convive_con_adulto'];
		   			$tipo_doc_madre=$res_extra1->fields['tipo_doc_padre'];
		   			$nro_doc_madre=$res_extra1->fields['nro_doc_padre'];
		   			$apellido_madre=$res_extra1->fields['apellido_padre'];
		   			$nombre_madre=$res_extra1->fields['nombre_padre'];	
				}elseif ($responsable=='TUTOR'){
					$menor_convive_con_adulto=$res_extra1->fields['menor_convive_con_adulto'];
		   			$tipo_doc_madre=$res_extra1->fields['tipo_doc_tutor'];
		   			$nro_doc_madre=$res_extra1->fields['nro_doc_tutor'];
		   			$apellido_madre=$res_extra1->fields['apellido_tutor'];
		   			$nombre_madre=$res_extra1->fields['nombre_tutor'];	
				}
		 	}
		 	$fecha_inscripcion=Fecha($res_extra1->fields['fecha_inscripcion']);
		 	$cuie=$res_extra1->fields['cuie_ea'];
		 	$calle=$res_extra1->fields['calle'];
		 	$numero_calle=$res_extra1->fields['numero_calle'];
			$piso=$res_extra1->fields['piso'];
			$dpto=$res_extra1->fields['dpto'];
			$manzana=$res_extra1->fields['manzana'];
			$entre_calle_1=$res_extra1->fields['entre_calle_1'];
			$entre_calle_2=$res_extra1->fields['entre_calle_2'];	
			$telefono=$res_extra1->fields['telefono'];
			$departamento=$res_extra1->fields['departamento'];
		   	$localidad=$res_extra1->fields['localidad'];
		   	$municipio=$res_extra1->fields['municipio'];
		   	$barrio=$res_extra1->fields['barrio'];
			$cod_pos=$res_extra1->fields['cod_pos'];
			$observaciones=$res_extra1->fields['observaciones'];   	
				if ($id_categoria=='1'){
				$embarazada=inline;
				$datos_resp=none;
				$puerpera=none;
				$fecha_diagnostico_emabrazo=Fecha($res_extra1->fields['fecha_diagnostico_embarazo']);
				$semanas_embarazo=$res_extra1->fields['semanas_embarazo'];
				$fecha_probable_parto=Fecha($res_extra1->fields['fecha_probable_parto']);
				}   	
					if ($id_categoria=='2'){
					$embarazada=none;
					$datos_resp=none;
					$puerpera=inline;
					$fecha_efectiva_parto=Fecha($res_extra1->fields['fecha_efectiva_parto']);
					}
		}//	fin de if ($res_extra1->RecordCount()!=EOF)
				elseif (($res_extra1->recordcount()== 0) && ($clase_doc=='A') || ($clase_doc=='P') || ($res_extra1->recordcount()> 0)&& ($clase_doc=='A')) {
				
				
			
		   $fecha_carga= date("Y-m-d");
		   $usuario=$_ses_user['login'];
		   
		   
		    
		    $fecha_nac=Fecha_db($fecha_nac);
		   	$fecha_diagnostico_embarazo=Fecha_db($fecha_diagnostico_embarazo);
		 
		   	$fecha_probable_parto=Fecha_db($fecha_probable_parto);
			$fecha_efectiva_parto=Fecha_db($fecha_efectiva_parto);
			$fecha_inscripcion=Fecha_db($fecha_inscripcion);
			
		   $db->StartTrans();      
		
		   $sql_parametros="select * from uad.parametros ";
		   $result_parametros=sql($sql_parametros) or fin_pagina();
		   $codigo_provincia=$result_parametros->fields['codigo_provincia'];
		   $codigo_ci=$result_parametros->fields['codigo_ci'];   
		   $codigo_uad=$result_parametros->fields['codigo_uad'];   
		    
		   $q="select nextval('uad.beneficiarios_id_beneficiarios_seq') as id_planilla";
		   $id_planilla=sql($q) or fin_pagina();
		
		   $id_planilla=$id_planilla->fields['id_planilla'];
		   
		   $id_planilla_clave= str_pad($id_planilla, 6, '0', STR_PAD_LEFT);
		    
		   $clave_beneficiario=$codigo_provincia.$codigo_uad.$codigo_ci.$id_planilla_clave;
			 
		    /*$usuario = substr($usuario,0,9);*/
		        
		    $responsable=$_POST['responsable'];
		    
		   $sql="Select puco.documento from puco.puco where puco.documento = '$num_doc'";
		   $sql="Select puco.documento from puco.puco where puco.documento = '1234567891'";
		   $res_extra=sql($sql, "Error al traer el beneficiario") or fin_pagina();
		   
   if (($res_extra->recordcount()>0) && ($responsable=='MADRE')){
   $query="insert into uad.beneficiarios
             (id_beneficiarios,estado_envio,clave_beneficiario,tipo_transaccion,apellido_benef,nombre_benef,clase_documento_benef,
             tipo_documento,numero_doc,id_categoria,sexo,fecha_nacimiento_benef,provincia_nac,localidad_nac,pais_nac,
             indigena,id_tribu,id_lengua,fecha_diagnostico_embarazo,semanas_embarazo,
             fecha_probable_parto,fecha_efectiva_parto,
             cuie_ea,cuie_ah,menor_convive_con_adulto,tipo_doc_madre,
             nro_doc_madre,apellido_madre,nombre_madre,calle,numero_calle,
             piso,dpto,manzana,entre_calle_1,entre_calle_2,telefono,departamento,localidad,municipio,barrio,cod_pos,observaciones,
			 fecha_inscripcion,fecha_carga,usuario_carga,activo,tipo_ficha,responsable)
             values
             ($id_planilla,'n','$clave_beneficiario',upper('$tipo_transaccion'),upper('$apellido'),upper('$nombre'),upper('$clase_doc'),
             upper('$tipo_doc'),'$num_doc','$id_categoria',upper('$sexo'),'$fecha_nac',upper('$provincia_nacn'),upper('$localidad_procn'),upper('$pais_nacn'),
             upper('$indigena'),$id_tribu,$id_lengua,'1899-12-30',null,
             '1899-12-30','1899-12-30',
             upper('$cuie'),upper('$cuie'),upper('$menor_convive_con_adulto'),
             upper('$tipo_doc_madre'),'$nro_doc_madre',upper('$apellido_madre'),upper('$nombre_madre'),
             upper('$calle'),'$numero_calle','$piso',upper('$dpto'), '$manzana',upper('$entre_calle_1'),
             upper('$entre_calle_2'),'$telefono',upper('$departamenton'),upper('$localidadn'),upper('$municipion'),upper('$barrion'),
             '$cod_posn',upper('$observaciones'), '$fecha_inscripcion','$fecha_carga',upper('$usuario'),'1','1', upper('$responsable'))";


    	sql($query, "Error al insertar la Planilla") or fin_pagina();
    
    	$accion="Se guardo la Planilla - El inscripto esta en el PUCO";

    	$db->CompleteTrans();
    	   
   }elseif (($res_extra->recordcount()== 0) && ($responsable=='MADRE'))  {
   		$query="insert into uad.beneficiarios
             (id_beneficiarios,estado_envio,clave_beneficiario,tipo_transaccion,apellido_benef,nombre_benef,clase_documento_benef,
             tipo_documento,numero_doc,id_categoria,sexo,fecha_nacimiento_benef,provincia_nac,localidad_nac,pais_nac,
             indigena,id_tribu,id_lengua,fecha_diagnostico_embarazo,semanas_embarazo,
             fecha_probable_parto,fecha_efectiva_parto,
             cuie_ea,cuie_ah,menor_convive_con_adulto,tipo_doc_madre,
             nro_doc_madre,apellido_madre,nombre_madre,calle,numero_calle,
             piso,dpto,manzana,entre_calle_1,entre_calle_2,telefono,departamento,localidad,municipio,barrio,cod_pos,observaciones,
			 fecha_inscripcion,fecha_carga,usuario_carga,activo,tipo_ficha,responsable)
             values
             ($id_planilla,'n','$clave_beneficiario',upper('$tipo_transaccion'),upper('$apellido'),upper('$nombre'),upper('$clase_doc'),
             upper('$tipo_doc'),'$num_doc','$id_categoria',upper('$sexo'),'$fecha_nac',upper('$provincia_nacn'),upper('$localidad_procn'),upper('$pais_nacn'),
             upper('$indigena'),$id_tribu,$id_lengua,'1899-12-30',null,
             '1899-12-30','1899-12-30',
             upper('$cuie'),upper('$cuie'),upper('$menor_convive_con_adulto'),
             upper('$tipo_doc_madre'),'$nro_doc_madre',upper('$apellido_madre'),upper('$nombre_madre'),
             upper('$calle'),'$numero_calle','$piso',upper('$dpto'), '$manzana',upper('$entre_calle_1'),
             upper('$entre_calle_2'),'$telefono',upper('$departamenton'),upper('$localidadn'),upper('$municipion'),upper('$barrion'),
             '$cod_posn',upper('$observaciones'), '$fecha_inscripcion','$fecha_carga',upper('$usuario'),'1','1', upper('$responsable'))";


    	sql($query, "Error al insertar la Planilla") or fin_pagina();
    
    	$accion="Se guardo la Planilla";       
	 
    	$db->CompleteTrans();
   }
   if (($res_extra->recordcount()>0) && ($responsable=='PADRE')) {
   			
   				$query="insert into uad.beneficiarios
             (id_beneficiarios,estado_envio,clave_beneficiario,tipo_transaccion,apellido_benef,nombre_benef,clase_documento_benef,
             tipo_documento,numero_doc,id_categoria,sexo,fecha_nacimiento_benef,provincia_nac,localidad_nac,pais_nac,
             indigena,id_tribu,id_lengua,fecha_diagnostico_embarazo,semanas_embarazo,
             fecha_probable_parto,fecha_efectiva_parto,
             cuie_ea,cuie_ah,menor_convive_con_adulto,tipo_doc_padre,
             nro_doc_padre,apellido_padre,nombre_padre,calle,numero_calle,
             piso,dpto,manzana,entre_calle_1,entre_calle_2,telefono,departamento,localidad,municipio,barrio,cod_pos,observaciones,
			 fecha_inscripcion,fecha_carga,usuario_carga,activo,tipo_ficha,responsable)
             values
             ($id_planilla,'n','$clave_beneficiario',upper('$tipo_transaccion'),upper('$apellido'),upper('$nombre'),upper('$clase_doc'),
             upper('$tipo_doc'),'$num_doc','$id_categoria',upper('$sexo'),'$fecha_nac',upper('$provincia_nacn'),upper('$localidad_procn'),upper('$pais_nacn'),
             upper('$indigena'),$id_tribu,$id_lengua,'1899-12-30',null,
             '1899-12-30','1899-12-30',
             upper('$cuie'),upper('$cuie'),upper('$menor_convive_con_adulto'),
             upper('$tipo_doc_madre'),'$nro_doc_madre',upper('$apellido_madre'),upper('$nombre_madre'),
             upper('$calle'),'$numero_calle','$piso',upper('$dpto'), '$manzana',upper('$entre_calle_1'),
             upper('$entre_calle_2'),'$telefono',upper('$departamenton'),upper('$localidadn'),upper('$municipion'),upper('$barrion'),
             '$cod_posn',upper('$observaciones'), '$fecha_inscripcion','$fecha_carga',upper('$usuario'),'1','1', upper('$responsable'))";


    	sql($query, "Error al insertar la Planilla") or fin_pagina();
    
    	$accion="Se guardo la Planilla - El inscripto esta en el PUCO";       
	 
    	$db->CompleteTrans();			
   
   		}elseif (($res_extra->recordcount()== 0) && ($responsable=='PADRE')) {
   		$query="insert into uad.beneficiarios
             (id_beneficiarios,estado_envio,clave_beneficiario,tipo_transaccion,apellido_benef,nombre_benef,clase_documento_benef,
             tipo_documento,numero_doc,id_categoria,sexo,fecha_nacimiento_benef,provincia_nac,localidad_nac,pais_nac,
             indigena,id_tribu,id_lengua,fecha_diagnostico_embarazo,semanas_embarazo,
             fecha_probable_parto,fecha_efectiva_parto,
             cuie_ea,cuie_ah,menor_convive_con_adulto,tipo_doc_padre,
             nro_doc_padre,apellido_padre,nombre_padre,calle,numero_calle,
             piso,dpto,manzana,entre_calle_1,entre_calle_2,telefono,departamento,localidad,municipio,barrio,cod_pos,observaciones,
			 fecha_inscripcion,fecha_carga,usuario_carga,activo,tipo_ficha,responsable)
             values
             ($id_planilla,'n','$clave_beneficiario',upper('$tipo_transaccion'),upper('$apellido'),upper('$nombre'),upper('$clase_doc'),
             upper('$tipo_doc'),'$num_doc','$id_categoria',upper('$sexo'),'$fecha_nac',upper('$provincia_nacn'),upper('$localidad_procn'),upper('$pais_nacn'),
             upper('$indigena'),$id_tribu,$id_lengua,'1899-12-30',null,
             '1899-12-30','1899-12-30',
             upper('$cuie'),upper('$cuie'),upper('$menor_convive_con_adulto'),
             upper('$tipo_doc_madre'),'$nro_doc_madre',upper('$apellido_madre'),upper('$nombre_madre'),
             upper('$calle'),'$numero_calle','$piso',upper('$dpto'), '$manzana',upper('$entre_calle_1'),
             upper('$entre_calle_2'),'$telefono',upper('$departamenton'),upper('$localidadn'),upper('$municipion'),upper('$barrion'),
             '$cod_posn',upper('$observaciones'), '$fecha_inscripcion','$fecha_carga',upper('$usuario'),'1','1', upper('$responsable'))";


    	sql($query, "Error al insertar la Planilla") or fin_pagina();
    
    	$accion="Se guardo la Planilla";       
	 
    	$db->CompleteTrans();
    }
if (($res_extra->recordcount()>0) && ($responsable=='TUTOR')) {
   	$query="insert into uad.beneficiarios
             (id_beneficiarios,estado_envio,clave_beneficiario,tipo_transaccion,apellido_benef,nombre_benef,clase_documento_benef,
             tipo_documento,numero_doc,id_categoria,sexo,fecha_nacimiento_benef,provincia_nac,localidad_nac,pais_nac,
             indigena,id_tribu,id_lengua,fecha_diagnostico_embarazo,semanas_embarazo,
             fecha_probable_parto,fecha_efectiva_parto,
             cuie_ea,cuie_ah,menor_convive_con_adulto,tipo_doc_tutor,
             nro_doc_tutor,apellido_tutor,nombre_tutor,calle,numero_calle,
             piso,dpto,manzana,entre_calle_1,entre_calle_2,telefono,departamento,localidad,municipio,barrio,cod_pos,observaciones,
	     fecha_inscripcion,fecha_carga,usuario_carga,activo,tipo_ficha,responsable)
             values
             ($id_planilla,'n','$clave_beneficiario',upper('$tipo_transaccion'),upper('$apellido'),upper('$nombre'),upper('$clase_doc'),
             upper('$tipo_doc'),'$num_doc','$id_categoria',upper('$sexo'),'$fecha_nac',upper('$provincia_nacn'),upper('$localidad_procn'),upper('$pais_nacn'),
             upper('$indigena'),$id_tribu,$id_lengua,'1899-12-30',null,
             '1899-12-30','1899-12-30',
             upper('$cuie'),upper('$cuie'),upper('$menor_convive_con_adulto'),
             upper('$tipo_doc_madre'),'$nro_doc_madre',upper('$apellido_madre'),upper('$nombre_madre'),
             upper('$calle'),'$numero_calle','$piso',upper('$dpto'), '$manzana',upper('$entre_calle_1'),
             upper('$entre_calle_2'),'$telefono',upper('$departamenton'),upper('$localidadn'),upper('$municipion'),upper('$barrion'),
             '$cod_posn',upper('$observaciones'), '$fecha_inscripcion','$fecha_carga',upper('$usuario'),'1','1', upper('$responsable'))";


    	sql($query, "Error al insertar la Planilla") or fin_pagina();
    
    	$accion="Se guardo la Planilla - El inscripto esta en el PUCO";       
	 
    	$db->CompleteTrans();
   } elseif (($res_extra->recordcount()== 0) && ($responsable=='TUTOR')) {
   	$query="insert into uad.beneficiarios
             (id_beneficiarios,estado_envio,clave_beneficiario,tipo_transaccion,apellido_benef,nombre_benef,clase_documento_benef,
             tipo_documento,numero_doc,id_categoria,sexo,fecha_nacimiento_benef,provincia_nac,localidad_nac,pais_nac,
             indigena,id_tribu,id_lengua,fecha_diagnostico_embarazo,semanas_embarazo,
             fecha_probable_parto,fecha_efectiva_parto,
             cuie_ea,cuie_ah,menor_convive_con_adulto,tipo_doc_tutor,
             nro_doc_tutor,apellido_tutor,nombre_tutor,calle,numero_calle,
             piso,dpto,manzana,entre_calle_1,entre_calle_2,telefono,departamento,localidad,municipio,barrio,cod_pos,observaciones,
	     fecha_inscripcion,fecha_carga,usuario_carga,activo,tipo_ficha,responsable)
             values
             ($id_planilla,'n','$clave_beneficiario',upper('$tipo_transaccion'),upper('$apellido'),upper('$nombre'),upper('$clase_doc'),
             upper('$tipo_doc'),'$num_doc','$id_categoria',upper('$sexo'),'$fecha_nac',upper('$provincia_nacn'),upper('$localidad_procn'),upper('$pais_nacn'),
             upper('$indigena'),$id_tribu,$id_lengua,'1899-12-30',null,
             '1899-12-30','1899-12-30',
             upper('$cuie'),upper('$cuie'),upper('$menor_convive_con_adulto'),
             upper('$tipo_doc_madre'),'$nro_doc_madre',upper('$apellido_madre'),upper('$nombre_madre'),
             upper('$calle'),'$numero_calle','$piso',upper('$dpto'), '$manzana',upper('$entre_calle_1'),
             upper('$entre_calle_2'),'$telefono',upper('$departamenton'),upper('$localidadn'),upper('$municipion'),upper('$barrion'),
             '$cod_posn',upper('$observaciones'), '$fecha_inscripcion','$fecha_carga',upper('$usuario'),'1','1', upper('$responsable'))";


    	sql($query, "Error al insertar la Planilla") or fin_pagina();
    
    	$accion="Se guardo la Planilla";       
	 
    	$db->CompleteTrans();
   } 
   if (($res_extra->recordcount()>0) && ($id_categoria == '1')){
      	$query="insert into uad.beneficiarios
             (id_beneficiarios,estado_envio,clave_beneficiario,tipo_transaccion,apellido_benef,nombre_benef,clase_documento_benef,
             tipo_documento,numero_doc,id_categoria,sexo,fecha_nacimiento_benef,provincia_nac,localidad_nac,pais_nac,
             indigena,id_tribu,id_lengua,fecha_diagnostico_embarazo,semanas_embarazo,
             fecha_probable_parto,fecha_efectiva_parto,cuie_ea,cuie_ah,calle,numero_calle,
             piso,dpto,manzana,entre_calle_1,entre_calle_2,telefono,departamento,localidad,municipio,barrio,cod_pos,observaciones,
			 fecha_inscripcion,fecha_carga,usuario_carga,activo,tipo_ficha)
             values
             ($id_planilla,'n','$clave_beneficiario',upper('$tipo_transaccion'),upper('$apellido'),upper('$nombre'),upper('$clase_doc'),
             upper('$tipo_doc'),'$num_doc','$id_categoria',upper('$sexo'),'$fecha_nac',upper('$provincia_nacn'),upper('$localidad_procn'),upper('$pais_nacn'),
             upper('$indigena'),$id_tribu,$id_lengua,'$fecha_diagnostico_embarazo','$semanas_embarazo',
             '$fecha_probable_parto','1899-12-30',upper('$cuie'),upper('$cuie'),upper('$calle'),'$numero_calle',
             '$piso',upper('$dpto'), '$manzana',upper('$entre_calle_1'),upper('$entre_calle_2'),'$telefono',upper('$departamenton'),
             upper('$localidadn'),upper('$municipion'),upper('$barrion'),'$cod_posn',upper('$observaciones'), '$fecha_inscripcion',
             '$fecha_carga',upper('$usuario'),'1','1')";
			
    	sql($query, "Error al insertar la Planilla") or fin_pagina();
    
    	$accion="Se guardo la Planilla - El inscripto esta en el PUCO";       
	 
    	$db->CompleteTrans(); 
    }   elseif (($res_extra->recordcount()== 0) && ($id_categoria == '1')) {
    	$query="insert into uad.beneficiarios
             (id_beneficiarios,estado_envio,clave_beneficiario,tipo_transaccion,apellido_benef,nombre_benef,clase_documento_benef,
             tipo_documento,numero_doc,id_categoria,sexo,fecha_nacimiento_benef,provincia_nac,localidad_nac,pais_nac,
             indigena,id_tribu,id_lengua,fecha_diagnostico_embarazo,semanas_embarazo,
             fecha_probable_parto,fecha_efectiva_parto,cuie_ea,cuie_ah,calle,numero_calle,
             piso,dpto,manzana,entre_calle_1,entre_calle_2,telefono,departamento,localidad,municipio,barrio,cod_pos,observaciones,
			 fecha_inscripcion,fecha_carga,usuario_carga,activo,tipo_ficha)
             values
             ($id_planilla,'n','$clave_beneficiario',upper('$tipo_transaccion'),upper('$apellido'),upper('$nombre'),upper('$clase_doc'),
             upper('$tipo_doc'),'$num_doc','$id_categoria',upper('$sexo'),'$fecha_nac',upper('$provincia_nacn'),upper('$localidad_procn'),upper('$pais_nacn'),
             upper('$indigena'),$id_tribu,$id_lengua,'$fecha_diagnostico_embarazo','$semanas_embarazo',
             '$fecha_probable_parto','1899-12-30',upper('$cuie'),upper('$cuie'),upper('$calle'),'$numero_calle',
             '$piso',upper('$dpto'), '$manzana',upper('$entre_calle_1'),upper('$entre_calle_2'),'$telefono',upper('$departamenton'),
             upper('$localidadn'),upper('$municipion'),upper('$barrion'),'$cod_posn',upper('$observaciones'), '$fecha_inscripcion',
             '$fecha_carga',upper('$usuario'),'1','1')";
			
    	sql($query, "Error al insertar la Planilla") or fin_pagina();
    
    	$accion="Se guardo la Planilla";       
	 
    	$db->CompleteTrans(); 
    }
   if (($res_extra->recordcount()>0) && ($id_categoria == '2')) {
        	$query="insert into uad.beneficiarios
             (id_beneficiarios,estado_envio,clave_beneficiario,tipo_transaccion,apellido_benef,nombre_benef,clase_documento_benef,
             tipo_documento,numero_doc,id_categoria,sexo,fecha_nacimiento_benef,provincia_nac,localidad_nac,pais_nac,
             indigena,id_tribu,id_lengua,fecha_diagnostico_embarazo,semanas_embarazo,
             fecha_probable_parto,fecha_efectiva_parto,cuie_ea,cuie_ah,calle,numero_calle,
             piso,dpto,manzana,entre_calle_1,entre_calle_2,telefono,departamento,localidad,municipio,barrio,cod_pos,observaciones,
			 fecha_inscripcion,fecha_carga,usuario_carga,activo,tipo_ficha)
             values
             ($id_planilla,'n','$clave_beneficiario',upper('$tipo_transaccion'),upper('$apellido'),upper('$nombre'),upper('$clase_doc'),
             upper('$tipo_doc'),'$num_doc','$id_categoria',upper('$sexo'),'$fecha_nac',upper('$provincia_nacn'),upper('$localidad_procn'),upper('$pais_nacn'),
             upper('$indigena'),$id_tribu,$id_lengua,'1899-12-30',null,
             '1899-12-30','$fecha_efectiva_parto',upper('$cuie'),upper('$cuie'),upper('$calle'),'$numero_calle',
             '$piso',upper('$dpto'), '$manzana',upper('$entre_calle_1'),upper('$entre_calle_2'),'$telefono',upper('$departamenton'),
             upper('$localidadn'),upper('$municipion'),upper('$barrion'),'$cod_posn',upper('$observaciones'), '$fecha_inscripcion',
             '$fecha_carga',upper('$usuario'),'1','1')";
			
    	sql($query, "Error al insertar la Planilla") or fin_pagina();
    
    	$accion="Se guardo la Planilla - El inscripto esta en el PUCO";       
	 
    	$db->CompleteTrans(); 
    }elseif (($res_extra->recordcount()== 0) && ($id_categoria == '2')) {
    	
    	$query="insert into uad.beneficiarios
             (id_beneficiarios,estado_envio,clave_beneficiario,tipo_transaccion,apellido_benef,nombre_benef,clase_documento_benef,
             tipo_documento,numero_doc,id_categoria,sexo,fecha_nacimiento_benef,provincia_nac,localidad_nac,pais_nac,
             indigena,id_tribu,id_lengua,fecha_diagnostico_embarazo,semanas_embarazo,
             fecha_probable_parto,fecha_efectiva_parto,cuie_ea,cuie_ah,calle,numero_calle,
             piso,dpto,manzana,entre_calle_1,entre_calle_2,telefono,departamento,localidad,municipio,barrio,cod_pos,observaciones,
			 fecha_inscripcion,fecha_carga,usuario_carga,activo,tipo_ficha)
             values
             ($id_planilla,'n','$clave_beneficiario',upper('$tipo_transaccion'),upper('$apellido'),upper('$nombre'),upper('$clase_doc'),
             upper('$tipo_doc'),'$num_doc','$id_categoria',upper('$sexo'),'$fecha_nac',upper('$provincia_nacn'),upper('$localidad_procn'),upper('$pais_nacn'),
             upper('$indigena'),$id_tribu,$id_lengua,'1899-12-30',null,
             '1899-12-30','$fecha_efectiva_parto',upper('$cuie'),upper('$cuie'),upper('$calle'),'$numero_calle',
             '$piso',upper('$dpto'), '$manzana',upper('$entre_calle_1'),upper('$entre_calle_2'),'$telefono',upper('$departamenton'),
             upper('$localidadn'),upper('$municipion'),upper('$barrion'),'$cod_posn',upper('$observaciones'), '$fecha_inscripcion',
             '$fecha_carga',upper('$usuario'),'1','1')";
			
    	sql($query, "Error al insertar la Planilla") or fin_pagina();
    
    	$accion="Se guardo la Planilla";       
	 
    	$db->CompleteTrans(); 
    	
    }
    if (($res_extra->recordcount()>0) && (($id_categoria == '7')||($id_categoria == '8'))) {
        	$query="insert into uad.beneficiarios
             (id_beneficiarios,estado_envio,clave_beneficiario,tipo_transaccion,apellido_benef,nombre_benef,clase_documento_benef,
             tipo_documento,numero_doc,id_categoria,sexo,fecha_nacimiento_benef,provincia_nac,localidad_nac,pais_nac,
             indigena,id_tribu,id_lengua,fecha_diagnostico_embarazo,semanas_embarazo,
             fecha_probable_parto,fecha_efectiva_parto,cuie_ea,cuie_ah,calle,numero_calle,
             piso,dpto,manzana,entre_calle_1,entre_calle_2,telefono,departamento,localidad,municipio,barrio,cod_pos,observaciones,
			 fecha_inscripcion,fecha_carga,usuario_carga,activo,tipo_ficha)
             values
             ($id_planilla,'n','$clave_beneficiario',upper('$tipo_transaccion'),upper('$apellido'),upper('$nombre'),upper('$clase_doc'),
             upper('$tipo_doc'),'$num_doc','$id_categoria',upper('$sexo'),'$fecha_nac',upper('$provincia_nacn'),upper('$localidad_procn'),upper('$pais_nacn'),
             upper('$indigena'),$id_tribu,$id_lengua,'1899-12-30',null,
             '1899-12-30','1899-12-30',upper('$cuie'),upper('$cuie'),upper('$calle'),'$numero_calle',
             '$piso',upper('$dpto'), '$manzana',upper('$entre_calle_1'),upper('$entre_calle_2'),'$telefono',upper('$departamenton'),
             upper('$localidadn'),upper('$municipion'),upper('$barrion'),'$cod_posn',upper('$observaciones'), '$fecha_inscripcion',
             '$fecha_carga',upper('$usuario'),'1','1')";
			
    	sql($query, "Error al insertar la Planilla") or fin_pagina();
    
    	$accion="Se guardo la Planilla - El inscripto esta en el PUCO";       
	 
    	$db->CompleteTrans(); 
    }elseif (($res_extra->recordcount()== 0) && (($id_categoria == '7')||($id_categoria == '8'))) {
    	
    	$query="insert into uad.beneficiarios
             (id_beneficiarios,estado_envio,clave_beneficiario,tipo_transaccion,apellido_benef,nombre_benef,clase_documento_benef,
             tipo_documento,numero_doc,id_categoria,sexo,fecha_nacimiento_benef,provincia_nac,localidad_nac,pais_nac,
             indigena,id_tribu,id_lengua,fecha_diagnostico_embarazo,semanas_embarazo,
             fecha_probable_parto,fecha_efectiva_parto,cuie_ea,cuie_ah,calle,numero_calle,
             piso,dpto,manzana,entre_calle_1,entre_calle_2,telefono,departamento,localidad,municipio,barrio,cod_pos,observaciones,
			 fecha_inscripcion,fecha_carga,usuario_carga,activo,tipo_ficha)
             values
             ($id_planilla,'n','$clave_beneficiario',upper('$tipo_transaccion'),upper('$apellido'),upper('$nombre'),upper('$clase_doc'),
             upper('$tipo_doc'),'$num_doc','$id_categoria',upper('$sexo'),'$fecha_nac',upper('$provincia_nacn'),upper('$localidad_procn'),upper('$pais_nacn'),
             upper('$indigena'),$id_tribu,$id_lengua,'1899-12-30',null,
             '1899-12-30','1899-12-30',upper('$cuie'),upper('$cuie'),upper('$calle'),'$numero_calle',
             '$piso',upper('$dpto'), '$manzana',upper('$entre_calle_1'),upper('$entre_calle_2'),'$telefono',upper('$departamenton'),
             upper('$localidadn'),upper('$municipion'),upper('$barrion'),'$cod_posn',upper('$observaciones'), '$fecha_inscripcion',
             '$fecha_carga',upper('$usuario'),'1','1')";
			
    	sql($query, "Error al insertar la Planilla") or fin_pagina();
    
    	$accion="Se guardo la Planilla";       
	 
    	$db->CompleteTrans(); 
    	
    }
   
		}

  //INSERTO EN REMEDIAR REDES
   //inserto en tablas de uad.remediar_x_beneficiario remediar.formulario

            $sql="select nextval('uad.remediar_x_beneficiario_Id_r_x_b_seq') as id";
            $res_rxb=sql($sql) or fin_pagina();
            $Id_r_x_b=$res_rxb->fields['id'];

            $query1="insert into uad.remediar_x_beneficiario 
                        (id_r_x_b,nroformulario,fechaempadronamiento,clavebeneficiario,fecha_carga,usuario_carga,emp_rapido)
                        values
                        ($Id_r_x_b,$clave_beneficiario,'$fecha_inscripcion','$clave_beneficiario','$fecha_carga',upper('$usuario'),'i')";
            sql($query1, "Error al insertar Remediar1") or fin_pagina();


            $q="select nextval('remediar.formulario_Id_formulario_seq') as id";
            $id_Idformulario=sql($q) or fin_pagina();
            $Id_formulario=$id_Idformulario->fields['id'];
                
            $usuario_nombre=$_ses_user['name'];
            $query2="insert into remediar.formulario 
                        (id_formulario,nroformulario,factores_riesgo,hta2,hta3,colesterol4,colesterol5,dmt26,dmt27,ecv8,tabaco9,puntaje_final,apellidoagente,nombreagente,centro_inscriptor,dni_agente,os,cual_os,usuario,fecha_carga,form_rapido)
                        values
                        ($Id_formulario,$clave_beneficiario,-1,-1,-1,-1,-1,-1,-1,-1,-1,0,'','',upper('$cuie'),'','NINGUNA','','$usuario','$fecha_carga','i')";

            sql($query2, "Error al insertar Remediar2") or fin_pagina();
  //FIN DE INSERTO EN REDES   
       
} //FIN Insert Beneficiarios

// Borrado de Beneficiarios
if ($_POST['borrar']=="Borrar"){
	
	if ($tipo_transaccion == 'B'){
	$query="UPDATE uad.beneficiarios  SET activo='0', tipo_transaccion= 'B', estado_envio='n'  WHERE (id_beneficiarios= $id_planilla)";
	sql($query, "Error al insertar la Planilla") or fin_pagina();
	   
	$accion="Se elimino la planilla $id_planilla";
	}
	
} //FIN Borrado Beneficiarios

//Busqueda de Beneficiarios
if ($_POST['b']=="b"){
	
		$sql1="select * from uad.beneficiarios	  
	 	where numero_doc='$num_doc'";
		$res_extra1=sql($sql1, "Error al traer el beneficiario") or fin_pagina();
		if ($res_extra1->recordcount()>0){
			$accion="El Beneficiario ya esta Empadronado";
		if ($res_extra1->recordcount()>1) $accion.=" -- HAY ".$res_extra1->recordcount()." INSCRIPTOS CON EL MISMO NUMERO POR FAVOR VERIFIQUE";
	$tipo_transaccion='M';
	$id_planilla=$res_extra1->fields['id_beneficiarios'];       
    $clave_beneficiario=$res_extra1->fields['clave_beneficiario'];
	$apellido=$res_extra1->fields['apellido_benef'];
 	$nombre=$res_extra1->fields['nombre_benef'];
 	$clase_doc=$res_extra1->fields['clase_documento_benef'];
 	$tipo_doc=$res_extra1->fields['tipo_documento'];
 	$sexo=$res_extra1->fields['sexo'];
 	$fecha_nac=Fecha($res_extra1->fields['fecha_nacimiento_benef']);
 	$pais_nac=$res_extra1->fields['pais_nac'];
 	$provincia_nac=$res_extra1->fields['provincia_nac'];
 	$localidad_proc=$res_extra1->fields['localidad_nac'];
 	$indigena= $res_extra1->fields['indigena'];
 	$id_tribu= $res_extra1->fields['id_tribu'];
 	$id_lengua= $res_extra1->fields['id_lengua'];
 	$id_categoria=$res_extra1->fields['id_categoria'];
	
 	if(($id_categoria=='3')||($id_categoria=='4')){ 
	$datos_resp=inline;
	$embarazada=none;
	$responsable=$res_extra1->fields['responsable'];
		if ($responsable=='MADRE'){
			$menor_convive_con_adulto=$res_extra1->fields['menor_convive_con_adulto'];
   			$tipo_doc_madre=$res_extra1->fields['tipo_doc_madre'];
   			$nro_doc_madre=$res_extra1->fields['nro_doc_madre'];
   			$apellido_madre=$res_extra1->fields['apellido_madre'];
   			$nombre_madre=$res_extra1->fields['nombre_madre'];
		}elseif ($responsable=='PADRE'){
			$menor_convive_con_adulto=$res_extra1->fields['menor_convive_con_adulto'];
   			$tipo_doc_madre=$res_extra1->fields['tipo_doc_padre'];
   			$nro_doc_madre=$res_extra1->fields['nro_doc_padre'];
   			$apellido_madre=$res_extra1->fields['apellido_padre'];
   			$nombre_madre=$res_extra1->fields['nombre_padre'];	
		}elseif ($responsable=='TUTOR'){
			$menor_convive_con_adulto=$res_extra1->fields['menor_convive_con_adulto'];
   			$tipo_doc_madre=$res_extra1->fields['tipo_doc_tutor'];
   			$nro_doc_madre=$res_extra1->fields['nro_doc_tutor'];
   			$apellido_madre=$res_extra1->fields['apellido_tutor'];
   			$nombre_madre=$res_extra1->fields['nombre_tutor'];	
		}
 	}
 	$fecha_inscripcion=Fecha($res_extra1->fields['fecha_inscripcion']);
 	$cuie=$res_extra1->fields['cuie_ea'];
 	$calle=$res_extra1->fields['calle'];
 	$numero_calle=$res_extra1->fields['numero_calle'];
	$piso=$res_extra1->fields['piso'];
	$dpto=$res_extra1->fields['dpto'];
	$manzana=$res_extra1->fields['manzana'];
	$entre_calle_1=$res_extra1->fields['entre_calle_1'];
	$entre_calle_2=$res_extra1->fields['entre_calle_2'];	
	$telefono=$res_extra1->fields['telefono'];
	$departamento=$res_extra1->fields['departamento'];
   	$localidad=$res_extra1->fields['localidad'];
   	$municipio=$res_extra1->fields['municipio'];
   	$barrio=$res_extra1->fields['barrio'];
	$cod_pos=$res_extra1->fields['cod_pos'];
	$observaciones=$res_extra1->fields['observaciones'];   	
		if ($id_categoria=='1'){
		$embarazada=inline;
		$datos_resp=none;
		$puerpera=none;
		$fecha_diagnostico_emabrazo=Fecha($res_extra1->fields['fecha_diagnostico_embarazo']);
		$semanas_embarazo=$res_extra1->fields['semanas_embarazo'];
		$fecha_probable_parto=Fecha($res_extra1->fields['fecha_probable_parto']);
		}   	
			if ($id_categoria=='2'){
			$embarazada=none;
			$datos_resp=none;
			$puerpera=inline;
			$fecha_efectiva_parto=Fecha($res_extra1->fields['fecha_efectiva_parto']);
			}
		}else {
			$accion2="Beneficiario no Encontrado";
		}
}//FIN Busqueda Beneficiario por DNI



if ($id_planilla) {

$query="SELECT beneficiarios.*, efe_conv.nombre, efe_conv.cuie
			FROM uad.beneficiarios
			left join nacer.efe_conv on beneficiarios.cuie_ea=efe_conv.cuie 
  where id_beneficiarios=$id_planilla";

$res_factura=sql($query, "Error al traer el Comprobantes") or fin_pagina();

$es_padre=$res_factura->fields['apellido_padre'];
$es_madre=$res_factura->fields['apellido_madre'];
$es_tutor=$res_factura->fields['apellido_tutor'];

if($es_padre != null){
	$responsable="PADRE";
	$tipo_doc_madre=$res_factura->fields['tipo_doc_padre'];
    $nro_doc_madre=$res_factura->fields['nro_doc_padre'];
    $apellido_madre=$res_factura->fields['apellido_padre']; 
    $nombre_madre=$res_factura->fields['nombre_padre'];
    $menor_convive_con_adulto=$res_factura->fields['menor_convive_con_adulto'];
    
	}
	elseif ($es_madre != null){
		$responsable="MADRE";
		$tipo_doc_madre=$res_factura->fields['tipo_doc_madre'];
    	$nro_doc_madre=$res_factura->fields['nro_doc_madre'];
    	$apellido_madre=$res_factura->fields['apellido_madre']; 
    	$nombre_madre=$res_factura->fields['nombre_madre'];
    	$menor_convive_con_adulto=$res_factura->fields['menor_convive_con_adulto'];
	}
	elseif ($es_tutor != null) {
		$responsable="TUTOR";
		$tipo_doc_madre=$res_factura->fields['tipo_doc_tutor'];
    	$nro_doc_madre=$res_factura->fields['nro_doc_tutor'];
    	$apellido_madre=$res_factura->fields['apellido_tutor']; 
    	$nombre_madre=$res_factura->fields['nombre_tutor'];
   	 	$menor_convive_con_adulto=$res_factura->fields['menor_convive_con_adulto'];
	}



$num_doc=$res_factura->fields['numero_doc']; 
$apellido= $res_factura->fields['apellido_benef'];
$nombre=$res_factura->fields['nombre_benef'];
$fecha_nac=fecha($res_factura->fields['fecha_nacimiento_benef']);


$fecha_diagnostico_embarazo=fecha($res_factura->fields['fecha_diagnostico_embarazo']);


$fecha_probable_parto=fecha($res_factura->fields['fecha_probable_parto']);


$fecha_efectiva_parto=fecha($res_factura->fields['fecha_efectiva_parto']);
$calle=$res_factura->fields['calle'];
$numero_calle=$res_factura->fields['numero_calle'];

$piso=$res_factura->fields['piso'];
$dpto=$res_factura->fields['dpto'];
$manzana=$res_factura->fields['manzana'];
$entre_calle_1=$res_factura->fields['entre_calle_1'];
$entre_calle_2=$res_factura->fields['entre_calle_2'];
$telefono=$res_factura->fields['telefono'];
$cod_pos=$res_factura->fields['cod_pos'];
$fecha_inscripcion=fecha($res_factura->fields['fecha_inscripcion']);
$observaciones=$res_factura->fields['observaciones'];
$cuie=$res_factura->fields['cuie'];
$pais_nac=$res_factura->fields['pais_nac'];
$provincia_nac=$res_factura->fields['provincia_nac'];
$localidad_proc=$res_factura->fields['localidad_nac'];
$departamento=$res_factura->fields['departamento'];
$id_categoria=$res_factura->fields['id_categoria'];
$indigena=$res_factura->fields['indigena'];
$id_tribu=$res_factura->fields['id_tribu'];
$id_lengua=$res_factura->fields['id_lengua'];
$responsable=$res_factura->fields['responsable'];
$clase_doc=$res_factura->fields['clase_documento_benef'];

}//FIN

// Query que muestra la informacion guardada del Beneficiario del Pais de Nacimiento
if (($id_planilla != '') && ($cambiodom != 'S')){
	$strConsulta = "select pais_nac from uad.beneficiarios where id_beneficiarios = $id_planilla ";
	$result = @pg_exec($strConsulta); 
	$fila= pg_fetch_array($result);
	$pais_nac.='<option value="'.$fila["pais_nac"].'">'.$fila["pais_nac"].'</option>';
	$pais_nacn=$fila["pais_nac"];
	}// FIN	
	elseif (($id_planilla == '') || ($cambiodom == 'S')){ // Query para traer los paises para luego ser utilizado con AJAX para que no refresque la pagina.
	$strConsulta = "select id_pais, nombre from uad.pais order by nombre";
	$result = @pg_exec($strConsulta); 
	$pais_nac = '<option value="0"> Seleccione Pa&iacute;s </option>';
	$opciones6 = '<option value="0"> Seleccione Provincia </option>';
	$opciones7 = '<option value="0"> Seleccione Localidad </option>';
	
	while( $fila = pg_fetch_array($result) )
	{
		
		$pais_nac.='<option value="'.$fila["id_pais"].'">'.$fila["nombre"].'</option>';
		
	} // FIN WHILE	
	
} // FIN ELSEIF

// Query que muestra la informacion guardada del Beneficiario de la Provincia de Nacimiento
if (($id_planilla != '') && ($cambiodom != 'S')){
	$strConsulta = "select provincia_nac from uad.beneficiarios where id_beneficiarios = $id_planilla";
	$result = @pg_exec($strConsulta); 
	$fila= pg_fetch_array($result);
	$opciones6.='<option value="'.$fila["provincia_nac"].'">'.$fila["provincia_nac"].'</option>';
	$provincia_nacn=$fila["provincia_nac"];
}// FIN

// Query que muestra la informacion guardada del Beneficiario de la Localidad de Nacimiento
if (($id_planilla != '') && ($cambiodom != 'S')){
	$strConsulta = "select localidad_nac from uad.beneficiarios where id_beneficiarios = $id_planilla";
	$result = @pg_exec($strConsulta); 
	$fila= pg_fetch_array($result);
	$opciones7.='<option value="'.$fila["localidad_nac"].'">'.$fila["localidad_nac"].'</option>';
	$localidad_procn=$fila["localidad_nac"];
}// FIN

// Query que muestra la informacion guardada del Beneficiario del Departamento donde vive
if (($id_planilla != '') && ($cambiodom != 'S')){
	$strConsulta = "select departamento from uad.beneficiarios where id_beneficiarios = $id_planilla";
	$result = @pg_exec($strConsulta); 
	$fila= pg_fetch_array($result);
	$departamento.='<option value="'.$fila["departamento"].'">'.$fila["departamento"].'</option>';
	$departamenton=$fila["departamento"];
	}// FIN	
	elseif (($id_planilla == '') || ($cambiodom ==  'S')){// Query para traer los departamentos para luego ser utilizado con AJAX para que no refresque la pagina.
 	$strConsulta = "select id_departamento, nombre from uad.departamentos where id_provincia = '12' order by nombre ";
	$result = @pg_exec($strConsulta); 
	$departamento = '<option value="0"> Seleccione Departamento </option>';
	$opciones2 = '<option value="0"> Seleccione Localidad </option>';
	$opciones3 = '<option value="0"> Seleccione Municipio </option>';
	$opciones4 = '<option value="0"> Seleccione Barrio </option>';
	$opciones5 = '<option value="0"> Codigo Postal  </option>';	
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
cargar_calendario();

$directorio_base=trim(substr(ROOT_DIR, strrpos(ROOT_DIR,chr(92))+1, strlen(ROOT_DIR)));
?>
<script>
//Script para el manejo de combobox de Pais de Nacimiento - Provincia de Nacimiento y Localidad de Nacimiento
$(document).ready(function(){
	$("#pais_nac").change(function(){
		$.ajax({
			url:"procesa.php",
			type: "POST",
			data:"id_pais="+$("#pais_nac").val(),
			success: function(opciones){
				$("#provincia_nac").html(opciones);
						
			}
		})
	});
});
$(document).ready(function(){
	$("#provincia_nac").change(function(){
		$.ajax({
			url:"procesa.php",
			type: "POST",
			data:"id_provincia="+$("#provincia_nac").val(),
			success: function(opciones){
				$("#localidad_nac").html(opciones);
						
			}
		})
	});
}); //FIN

//Script para el manejo de combobox de Departamento - Localidad - Municipio y Barrio
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
	document.all.pais_nacn.value =  pais_nac;
}// FIN

//Guarda el nombre de la Provincia de Nacimiento
function showprovincia_nac(){
	var provincia_nac = document.getElementById('provincia_nac')[document.getElementById('provincia_nac').selectedIndex].innerHTML;
	document.all.provincia_nacn.value =  provincia_nac;
}// FIN

//Guarda el nombre de la Localidad de Nacimiento
function showlocalidad_nac(){
	var localidad_nac = document.getElementById('localidad_nac')[document.getElementById('localidad_nac').selectedIndex].innerHTML;
	document.all.localidad_procn.value =  localidad_nac;
}// FIN

//Guarda el nombre del Departamento
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

// Validar Fechas
function esFechaValida(fecha){
    if (fecha != undefined && fecha.value != "" ){
        if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)){
            alert("formato de fecha no v�lido (dd/mm/aaaa)");
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
            alert("Fecha introducida err�nea");
            return false;
    }
 
        if (dia>numDias || dia==0){
            alert("Fecha introducida err�nea");
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

// Funcion para verificar que el DNI no tenga espacios en blanco
function CheckUserName(ele) { 
	if (/\s/.test(ele.value)) { 
		alert("No se permiten espacios en blanco");
		document.all.num_doc.focus(); } 
	} 

//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 if(document.all.num_doc.value==""){
	 alert("Debe completar el campo numero de documento");
	 document.all.num_doc.focus();
	 return false;
	 }else{
 		var num_doc=document.all.num_doc.value;
		if(isNaN(num_doc)){
			alert('El dato ingresado en numero de documento debe ser entero y no contener espacios');
			document.all.num_doc.focus();
			return false;
	 	}
	 }

	
 if(document.all.apellido.value==""){
	 alert("Debe completar el campo apellido");
	 document.all.apellido.focus();
	 return false;
 }else{
	 var charpos = document.all.apellido.value.search("/[^A-Za-z\s]/"); 
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
		 var charpos = document.all.nombre.value.search("/[^A-Za-z\s]/"); 
		   if( charpos >= 0) 
		    { 
		     alert( "El campo Nombre solo permite letras "); 
		     document.all.nombre.focus();
		     return false;
		    }
		 }		
	
 if(document.all.sexo.value=="-1"){
			alert("Debe completar el campo sexo");
			document.all.sexo.focus();
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

 if(document.all.id_categoria.value=='-1'){
		  alert('Debe Ingresar una Categoria');
		  return false;
		 }
 
 if(document.all.cuie.value=="-1"){
		  alert('Debe Seleccionar un Efector');
		  document.all.cuie.focus();
		  return false;
		 } 
 
 if ((document.all.id_categoria.value=='3')||(document.all.id_categoria.value=='4')){
			if(document.all.responsable.value==""){
				alert ("Debe completar el campo Datos del responsable");
				document.all.responsable.focus();
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
			var anio_mayor_nivel=document.all.anio_mayor_nivel.value;
			 var anio_mayor_nivel_madre=document.all.anio_mayor_nivel_madre.value;
			 if(isNaN(anio_mayor_nivel) || isNaN(anio_mayor_nivel_madre) ){
				alert('El dato ingresado en años mayor nivel debe ser entero');
			 return false;
			 }
			if(document.all.apellido_madre.value==""){
				alert("Debe completar el campo apellido del responsable");
				document.all.apellido_madre.focus();
				 return false;
			 }else{
				 var charpos = document.all.apellido_madre.value.search("[^A-Za-z/\s/]"); 
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
				 var charpos = document.all.nombre_madre.value.search("[^A-Za-z/\s/]"); 
				   if( charpos >= 0) 
				    { 
				     alert( "El campo Nombre del responsable solo permite letras "); 
				     document.all.nombre_madre.focus();
				     return false;
				    }
				 }	
				
			if(document.all.alfabeta_madre.value=="-1"){
			alert("Debe completar el campo alfabeto del responsable");
			 return false;
		 	}
		}
	 
 var docu=document.all.clase_doc.value;
		if(docu!='P'){
			var num1=document.all.nro_doc_madre.value;
			var num2=document.all.num_doc.value;
			if (num1 != num2){
				alert("Los numeros de documento deben coincidir");
				document.all.num_doc.focus();
				return false;
			}
		}
	

  if(document.all.fecha_nac.value==""){
		alert("Debe completar el campo fecha de nacimiento");
		 return false;
		 }
}
//de function control_nuevos()

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

function cambiar_patalla(){	
	
	//si no hay nada seleccionado en categoria no mostrar nada
	if (document.all.id_categoria.value=='-1'){
		document.all.cat_emb.style.display='none';
		document.all.cat_nino.style.display='none';
		document.all.cat_puerp.style.display='none';
	}	
	if (document.all.id_categoria.value=='8'){
		document.all.cat_emb.style.display='none';
		document.all.cat_nino.style.display='none';
		document.all.cat_puerp.style.display='none';
	}
	if (document.all.id_categoria.value=='7'){
		document.all.cat_emb.style.display='none';
		document.all.cat_nino.style.display='none';
		document.all.cat_puerp.style.display='none';
	}
	
	//si es masculino y recien nacio es una inscripcion con datos de padre madre o tutor
	if ((document.all.sexo.value=='M')&&(document.all.id_categoria.value=='3')){
		document.all.cat_emb.style.display='none';
		document.all.cat_nino.style.display='inline';
		document.all.cat_puerp.style.display='none';
	}
	
	//si es masculino y menor de 6 a�os es una inscripcion con datos de padre madre o tutor
	if ((document.all.sexo.value=='M')&&(document.all.id_categoria.value=='4')){
		document.all.cat_emb.style.display='none';
		document.all.cat_nino.style.display='inline';
		document.all.cat_puerp.style.display='none';
	}

	//femenino embarazada
	if(((document.all.sexo.value=='f')||(document.all.sexo.value=='F'))&&(document.all.id_categoria.value=='1')){
		document.all.cat_emb.style.display='inline';
		document.all.cat_nino.style.display='none';
		document.all.cat_puerp.style.display='none';
		}

	//femenino puerpera menor de 45 d�as
	if(((document.all.sexo.value=='f')||(document.all.sexo.value=='F'))&&(document.all.id_categoria.value=='2')){
		document.all.cat_emb.style.display='none';
		document.all.cat_nino.style.display='none';
		document.all.cat_puerp.style.display='inline';
		
		}
		
	//si es menor de 6 a�os y femenino una inscripcion con los datos de madre padro o tutor
	if(((document.all.sexo.value=='f')||(document.all.sexo.value=='F'))&&(document.all.id_categoria.value=='4' || document.all.id_categoria.value=='3')){
		document.all.cat_emb.style.display='none';
		document.all.cat_nino.style.display='inline';
		document.all.cat_puerp.style.display='none';
		
		}
	

}// Calcula la FPP 
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
}

function recalcF1(){
 with (document.form1){
  fecha_probable_parto.value = addToDate(fecha_diagnostico_embarazo.value, 280 - (semanas_embarazo.value *7));
 }
}

function pulsar(e) {
	  tecla = (document.all) ? e.keyCode :e.which;
	  return (tecla!=13);
	} 

</script>

<form name='form1' action='ins_admin_old.php' accept-charset="latin1" method='POST'>
<input type="hidden" value="<?=$id_planilla?>" name="id_planilla">
<input type="hidden" value="<?=$usuario1?>" name="usuario1">
<input type="hidden" value="<?=$estado_envio?>" name="estado_envio">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<?echo "<center><b><font size='+1' color='Blue'>$accion2</font></b></center>";?>
<table width="97%" cellspacing=0 border="1" bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
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
            <b> N&uacute;mero de Formulario: <font size="+1" color="Blue"><?=($id_planilla)? $clave_beneficiario : "Nuevo"?></font> </b> <? if ($trans == 'Borrado'){?> <b><font size="+1" color="Blue"><?=($id_planilla)? $trans : $trans?></font></b><?}?>
       
           </td>
         </tr>
         
         <tr>	           
           <td align="right" colspan="4">
             <b><font size="0" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
         
         <tr>
         	<td align="right" width="20%">
         	  <b>N&uacute;mero de Documento:</b>
         	</td>         	
            <td align='left' width="30%">
              <input type="text" size="30" value="<?=$num_doc?>" name="num_doc" onblur="CheckUserName(this);" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
              <input type="submit" size="3" value="b" name="b"> <br><font color="Red">Sin Puntos</font>
            </td>
            
            <td align="right" width="20%">
				<b>Tipo de Transacci&oacute;n:</b>
			</td>
			<td align="left" width="30%">			 	
			 <select name=tipo_transaccion Style="width=200px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();document.forms[0].submit()" 
				<?php if ($trans == 'Borrado')echo "disabled"?>
				>
			 <option value='A' <?if ($tipo_transaccion=='A') echo "selected"?>>Inscripci&oacute;n</option>
			 <option value='M'<?if ($tipo_transaccion=='M') echo "selected"?>>Modificaci&oacute;n</option>
			 <option value='B'<?if ($tipo_transaccion=='B') echo "selected"?>>Baja</option>
			 		 
			</select>
			</td>            
         </tr>
         
         <tr>
         	<td align="right">
         	  <b>Apellido:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="30" value="<?=$apellido?>" name="apellido" onkeypress="return pulsar(event)" onblur="this.value=this.value.toUpperCase();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>
           	<td align="right">
         	  <b>Nombre:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="30" value="<?=$nombre?>" name="nombre" onkeypress="return pulsar(event)" onblur="this.value=this.value.toUpperCase();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>
         </tr> 
         
		<tr>
            <td align="right">
				<b>Clase de Documento:</b>
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
			  <option value=LC <?if ($tipo_doc=='LC') echo "selected"?>>Libreta C&iacute;vica</option>
			  <option value=PA <?if ($tipo_doc=='PA') echo "selected"?>>Pasaporte Argentino</option>
			  <option value=CM <?if ($tipo_doc=='CM') echo "selected"?>>Certificado Migratorio</option>
			 </select>
			</td>
         </tr>

         <tr>	           
           <td align="center" colspan="4" id="ma">
            <b> Datos de Nacimiento </b>
           </td>
         </tr>
         
         <tr>
         	<td align="right">
				<b>Sexo:</b>
			</td>
			<td align="left">			 	
			<select name=sexo Style="width=200px" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> >
			  <option value=F <?if ($sexo=='F') echo "selected"?>>Femenino</option>
			  <option value=M <?if ($sexo=='M') echo "selected"?>>Masculino</option>
			  </select>
			  
			 
			</td> 
         	<td align="right">
				<b>Fecha de Nacimiento:</b>
			</td>
		    <td align="left">
		    	<input type=text name=fecha_nac id=fecha_nac onblur="esFechaValida(this);" value='<?=$fecha_nac;?>' size=15 onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
		    	<?=link_calendario('fecha_nac');?>   
		    </td>		    
		</tr>   

		<tr>
			<td align="right" >
				<b>Extranjero/Pa&iacute;s:</b> <input type="hidden" name="pais_nacn" value="<?=$pais_nacn?>">
			</td>
			<td align="left" >
			<select id="pais_nac" name="pais_nac" onchange="showpais_nac();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>><?php echo $pais_nac;?></select>			 	
		   	</td> 
    
            <td align="right">
				<b>Provincia:</b> <input type="hidden" name="provincia_nacn" value="<?=$provincia_nacn?>">
			</td>
			<td align="left">	
			<select id="provincia_nac" name="provincia_nac" onchange="showprovincia_nac();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>><?php echo $opciones6;?></select>
			</td> 
         	
         </tr> 
         
         <tr>
            <td align="right">
				<b>Localidad:</b> <input type="hidden" name="localidad_procn" value="<?=$localidad_procn?>">
				
			</td>
			<td align="left">			 	
			<select id="localidad_nac" name="localidad_nac" onchange="showlocalidad_nac();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>><?php echo $opciones7;?></select>
			
			</td>
			<td align="right">
         	   <b>Originario:</b>
         	   	
         	</td>         	
            <td align='left'>
				<input type="radio" name="indigena" value="N"  <?php if(($indigena == "N") or ($indigena==""))echo "checked" ;?> onclick="document.all.id_tribu.value='0';document.all.id_lengua.value='0';" > NO
				<input type="radio" name="indigena" value="S" <?php if($indigena == "S") echo "checked" ;?> onclick="document.all.id_tribu.disabled=false;document.all.id_lengua.disabled=false;"> SI
            </td>
					
         </tr> 
         
         <tr>
         	<td align="right">
         	  <b>Pueblo Ind&iacute;gena:</b>
         	</td>         	
            <td align='left'>
              <select name=id_tribu Style="width=200px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				<?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
			 <option value='-1'>Seleccione</option>
			 <?
			 $sql= "select * from uad.tribus order by nombre";
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
			 $sql= "select * from uad.lenguas order by nombre";
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
            <b> Categor&iacute;a </b>
           </td>        
         </tr>
         
         <tr  align="center">
         	<td align="right" width="20%" colspan="2">
				<b>Categor&iacute;a del Beneficiario:</b>
			</td>
			<td align="left" width="30%" colspan="2">			 	
			 <select name=id_categoria Style="width=200px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer(); cambiar_patalla();" 
				<?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
			 <?
			 $sql= "select * from uad.categorias where tipo_ficha='1' order by id_categoria ";
			 $res_efectores=sql($sql) or fin_pagina();?>
			 
			 <option value='-1' <?if ($id_categoria=='-1') echo "selected"?>>Seleccione</option>
			 <?while (!$res_efectores->EOF){ 
			 	$id_categorial=$res_efectores->fields['id_categoria'];
			 	$tipo_ficha=$res_efectores->fields['tipo_ficha'];
			    $categoria=$res_efectores->fields['categoria'];?>
				<option value='<?=$id_categorial?>'<?if ($id_categoria==$id_categorial) echo "selected";?>><?echo $categoria;?></option>
			    <?$res_efectores->movenext();
			    }?>
			</select>
			</td>            
         </tr> 
         
         
         
         <tr><td colspan="4"><table id="cat_nino" class="bordes" width="100%" style="display:<?=$datos_resp ?>;border:thin groove;">
         
         <tr>         
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
			  <option value='' <?if ($responsable=='') echo "selected"?>>RESPONSABLE</option>
			  <option value=MADRE <?if ($responsable=='MADRE') echo "selected"?>>MADRE</option>
			  <option value=PADRE <?if ($responsable=='PADRE') echo "selected"?>>PADRE</option>
			  <option value=TUTOR <?if ($responsable=='TUTOR') echo "selected"?>>TUTOR</option>
			  </select>			 	
			
			
			<td align="right">
				<b>Menor Vive con Adulto:</b>
			</td>
			<td align="left" >			 	
			 <select name=menor_convive_con_adulto Style="width=200px" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> >
			  <option value=S <?if ($menor_convive_con_adulto=='S') echo "selected"?>>SI</option>
			  <option value=N <?if ($menor_convive_con_adulto=='N') echo "selected"?>>NO</option>
			  </select>
			</td> 
		</tr>
         
          <tr>
          	<td align="right">
				<b>Tipo de Documento:</b>
			</td>
			<td align="left">			 	
			 <select name=tipo_doc_madre Style="width=200px" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> >
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
         	  <b>Apellido:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="30" value="<?=$apellido_madre?>" name="apellido_madre" onkeypress="return pulsar(event)" onblur="this.value=this.value.toUpperCase();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>
           	<td align="right">
         	  <b>Nombre:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="30" value="<?=$nombre_madre?>" name="nombre_madre" onkeypress="return pulsar(event)" onblur="this.value=this.value.toUpperCase();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>
         </tr> 
         <tr>	           
            
         </table>
         
         </td></tr>
         
         <tr><td colspan="4"><table id="cat_emb" class="bordes" width="100%" style="display:<?= $embarazada ?>;border:thin groove">
         
         <tr>	           
           <td align="center" colspan="4" id="ma">
            <b> Datos de Embarazo </b>
           </td>        
         </tr>
         
          <tr>
         	<td align="right">
				<b>Fecha de Diag. de Embarazo:</b>
			</td>
		    <td align="left">	       
		    	 <input type=text name=fecha_diagnostico_embarazo id=fecha_diagnostico_embarazo onblur="esFechaValida(this);" value='<?=$fecha_diagnostico_embarazo;?>' onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> size=15>
		    	 <?=link_calendario("fecha_diagnostico_embarazo");?>					    	 
		    </td>
		    <td align="right">
         	   <b>Semana de Embarazo:</b>         	   	
         	</td>         	
            <td align='left'>
            
            	<input type="text" name="semanas_embarazo"  value=<?=$semanas_embarazo;?> onblur="recalcF1()" onkeypress="return pulsar(event)"  size="30"   <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>		    
		</tr>   
		
		<tr>
         	<td align="right">
				<b>Fecha Probable de Parto:</b>
			</td>
		    <td align="left">
		    	
		    	 <input type=text name=fecha_probable_parto id=fecha_probable_parto onblur="esFechaValida(this);" value='<?=$fecha_probable_parto;?>' size=15 onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
		    	 <?=link_calendario("fecha_probable_parto");?>				    	 
		    </td>
		    </tr>   
		       
         </table>
          <tr><td colspan="4"><table id="cat_puerp" class="bordes" width="100%" style="display:<?= $puerpera ?>;border:thin groove">
         
         <tr>	           
           <td align="center" colspan="4" id="ma">
            <b> Puerpera menor de 45 d&iacute;as </b>
           </td>        
         </tr>
		    <tr>
		    <td align="right">
         	   <b>Fecha Efectiva del Parto:</b>         	   	
         	</td>         	
            <td align='left'>
            	<? $fecha_comprobante=date("d/m/Y");?>
		    	<input type=text id=fecha_efectiva_parto name=fecha_efectiva_parto onblur="esFechaValida(this);" value='<?=$fecha_efectiva_parto;?>' size=15 onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
		    	<?=link_calendario("fecha_efectiva_parto");?>
            </td>		    
		
          </tr>   
		       
         </table>
         <tr>	           
           <td align="center" colspan="4" id="ma">
            <b> Fecha de Inscripci&oacute;n </b>
           </td>
         </tr>

         <tr>
         	<td align="right" colspan="2">
				<b>Fecha de Inscripci&oacute;n:</b>
			</td>
		    <td align="left" colspan="2">
		    	 <input type=text name=fecha_inscripcion id=fecha_inscripcion onblur="esFechaValida(this);" value='<?=$fecha_inscripcion;?>' size=15 onkeypress="return pulsar(event)" readonly>
				 <?php if (!$id_planilla) {?>
				 <?=link_calendario("fecha_inscripcion");}?>					    	 
		    </td>		    	    
		</tr>
         
         <tr>	           
           <td align="center" colspan="4" id="ma">
            <b> Efector Habitual </b>
           </td>
         </tr>
         
         <tr>
         	<td align="right" width="20%" colspan="2">
				<b>Efector Habitual:</b>
			</td>
			<td align="left" width="30%" colspan="2">			 	
			 <select name=cuie Style="width=300px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				 <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?> >
			 <option value=-1>Seleccione</option>
			 <?
			 $sql= "select * from nacer.efe_conv order by nombre";
			/* $sql= " select nacer.efe_conv.nombreefector, nacer.efe_conv.cuie from nacer.efe_conv join sistema.usu_efec on (nacer.efe_conv.cuie = sistema.usu_efec.cuie) 
			        join sistema.usuarios on (sistema.usu_efec.id_usuario = sistema.usuarios.id_usuario) 
			        where sistema.usuarios.id_usuario = '$usuario1' order by nombreefector";*/
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
            <b> Datos del Domicilio </b>
           </td>
         </tr>
        <tr>
        <td colspan="4" align="center" id="cdomi" style="display:<?=$cdomi1?>">
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
              <input type="text" size="30" value="<?=$calle?>" name="calle" onkeypress="return pulsar(event)" onblur="this.value=this.value.toUpperCase();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>
           	<td align="right">
         	  <b>Numero Calle:</b><input type="text" size="5" value="<?=$numero_calle?>" name="numero_calle" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
         	</td>         	
            <td align='left'>
			  <b>Piso:</b><input type="text" size="5" value="<?=$piso?>" name="piso" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>         	
            </td>
         </tr>  
         
         <tr>
         	<td align="right">
         	  <b>Dpto / Casa:</b>
         	  <input type="text" size="10" value="<?=$dpto?>" name="dpto" onkeypress="return pulsar(event)" onblur="this.value=this.value.toUpperCase();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
         	</td>         	
            <td align='left'>
			  <b>Mz:</b>
         	  <input type="text" size="10" value="<?=$manzana?>" name="manzana" onkeypress="return pulsar(event)" onblur="this.value=this.value.toUpperCase();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>         	
            </td>
         	<td align="right">
         	  <b>Entre Calle:</b><input type="text" size="10" value="<?=$entre_calle_1?>" name="entre_calle_1" onkeypress="return pulsar(event)" onblur="this.value=this.value.toUpperCase();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
         	</td>         	
            <td align='left'>
			  <b>Entre Calle:</b>
         	  <input type="text" size="10" value="<?=$entre_calle_2?>" name="entre_calle_2" onkeypress="return pulsar(event)" onblur="this.value=this.value.toUpperCase();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>         	
            </td>         	
         </tr>  
         
         <tr>
         	<td align="right">
         	  <b>Telefono:</b>
         	</td>         	
            <td align='left'>
         	  <input type="text" size="30" value="<?=$telefono?>" name="telefono" onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>         	
            </td>
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
           <td align="center" colspan="4" id="ma">
            <b> Observaciones </b>
           </td>        
         </tr>
         
         <tr align="center">
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left' colspan="3">
              <textarea cols='80' rows='4' name='observaciones' onblur="this.value=this.value.toUpperCase();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>> <?=$observaciones;?> </textarea>
            </td>
         </tr>   
        
        <?if ($id_planilla){?>
          <tr>            
            <td align="center" colspan="4">
            <br/>
            <div class="row">
            <div class="col-md-12">
            <div class="panel panel-default" id="panel_ficha_consumo">
              <div class="panel-heading">
                <b><a data-toggle="collapse" data-target="#ficha_consumo" href="#panel_ficha_consumo" class="collapsed">
                  Ficha de consumo
                </a></b>
              </div>
              <div id="ficha_consumo" class="panel-collapse collapse">
                <div class="panel-body text-center">
                  <table class="table table-condensed table-hover">
                    <thead>
                      <tr>
                        <th width="18%" class="small">M&eacute;dico</th>
                        <th width="18%" class="small">Especialidad</th>
                        <th width="30%" class="small">Diagn&oacute;stico</th>
                        <th width="24%" class="small">Evoluci&oacute;n</th>
                        <th width="10%" class="small">Fecha</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "SELECT 
                                  nacer.cie10.dec10 AS diagnostico_cie10,
                                  nacer.diagnosticos.evolucion,
                                  nacer.diagnosticos.fecha_atencion,
                                  nacer.especialidades.nom_titulo AS especialidad,
                                  nacer.medicos.apellido AS medico_apellido,
                                  nacer.medicos.nombre AS medico_nombre,
                                  nacer.cepsap_items.descripcion AS diagnostico_cepsap
                                FROM
                                  nacer.diagnosticos
                                  LEFT OUTER JOIN nacer.cie10 ON (nacer.diagnosticos.id_cie10 = nacer.cie10.id10)
                                  LEFT OUTER JOIN nacer.agendas_eventos ON (nacer.diagnosticos.id_turno = nacer.agendas_eventos.id)
                                  LEFT OUTER JOIN nacer.agendas ON (nacer.agendas_eventos.id_agenda = nacer.agendas.id)
                                  LEFT OUTER JOIN nacer.especialidades_medicos ON (nacer.agendas.id_especialidad_medico = nacer.especialidades_medicos.id)
                                  LEFT OUTER JOIN nacer.especialidades ON (nacer.especialidades_medicos.id_especialidad = nacer.especialidades.id_especialidad)
                                  LEFT OUTER JOIN nacer.medicos ON (nacer.especialidades_medicos.id_medico = nacer.medicos.id_medico)
                                  LEFT OUTER JOIN nacer.cepsap_items ON (nacer.diagnosticos.id_cepsap = nacer.cepsap_items.id)
                                WHERE
                                  nacer.agendas_eventos.id_paciente = $id_planilla
                                ORDER BY
                                  nacer.diagnosticos.id_diagnostico DESC";
                       $res_ficha = sql($query, "al traer los datos de la Ficha de Consumo") or fin_pagina();
                      if ($res_ficha->recordCount() > 0) {
                        while (!$res_ficha->EOF) {
                          echo '<tr>';
                          echo '<td>', $res_ficha->fields['medico_apellido'], ' ', $res_ficha->fields['medico_nombre'], '</td>';
                          echo '<td>', $res_ficha->fields['especialidad'], '</td>';
                          echo '<td>';
                          if (!empty($res_ficha->fields['diagnostico_cie10'])) {
                            echo 'CIE10: ', $res_ficha->fields['diagnostico_cie10'], '<br/>';
                          }
                          if (!empty($res_ficha->fields['diagnostico_cepsap'])) {
                            echo 'CEPSAP: ', $res_ficha->fields['diagnostico_cepsap'], '<br/>';
                          }
                          echo '</td>';
                          echo '<td>', $res_ficha->fields['evolucion'], '</td>';
                          echo '<td class="text-center">', Fecha($res_ficha->fields['fecha_atencion']), '</td>';
                          echo '<tr>';
                          $res_ficha->MoveNext();
                        }
                      }
                      else {
                        echo '<td colspan="5" align="center" class="danger"><strong>No hay datos</strong></td>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            </div>

            </td>
          </tr>  
          <?}?> 
                    
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
<?if  ($clave_beneficiario != '') {?>
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
		      <input type="button" name="cancelar_editar" value="Cancelar" title="Cancelar Edicion" disabled style="width=130px" onclick="document.location.reload()">		      
		      <input type=button name="carga_remediar" value="Remediar+Redes" onclick="window.open('<?=encode_link("../remediar/remediar_admin.php",array("estado_envio"=>$estado_envio,"clave_beneficiario"=>$clave_beneficiario,"sexo"=>$sexo,"fecha_nac"=>$fecha_nac,"vremediar"=>'s',"pagina_viene_1"=>"ins_admin_old.php"))?>','Remediar','dependent:yes,width=1000,height=700,top=1,left=60,scrollbars=yes');" title="Carga Remediar + Redes" <?if ($tipo_transaccion == "B") echo "disabled"?>>&nbsp;
		    </td>
		 </tr> 
	 </table>	
	 <br>
	 <?}?>
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
   	  <?if ($pagina_viene_1){?>
     <input type=button name="volver" value="Volver" onclick="document.location='<?=$pagina_viene_1?>'"title="Volver al Listado" style="width=150px">     
 	<?}ELSE{?>
  	<input type=button name="volver" value="Volver" onclick="document.location='ins_listado_old.php'"title="Volver al Listado" style="width=150px">     
 	<?}?>  
   </td>
  </tr>
 
 </table></td></tr>
 
 
 </table>
</form>
 
<?=fin_pagina();// aca termino ?>
