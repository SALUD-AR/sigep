<?php

function coprobar_obligatorios(){
	$apellido=$_POST['apellido'];
	$nombre=$_POST['nombre'];
$tipo_documento=$_POST['tipo_doc'];
$clase_doc=$_POST['clase_doc'];
$tipo_doc=$_POST['tipo_doc'];
$sexo=$_POST['sexo'];
$fecha_nac=$_POST['fecha_nac'];
$pais_nac=$_POST['pais_nac'];
$provincia_nac=$_POST['provincia_nac'];
$localidad_nac=$_POST['localidad_proc'];
$indigena=$_POST['indigena'];
$tipo_doc_madre=$_POST['tipo_doc_madre'];
$nro_doc_madre=$_POST['nro_doc_madre'];
$apellido_madre=$_POST['apellido_madre'];
$nombre_madre=$_POST['nombre_madre'];
$alfabeta_madre=$_POST['alfabeta_madre'];

//si es mayor de 18 años
$fecha_diagnostico_embarazo=$_POST['fecha_diagnostico_embarazo'];
$fecha_probable_parto=$_POST['fecha_probable_parto'];
$fecha_efectiva_parto=$_POST['fecha_efectiva_parto'];

$cuie=$_POST['cuie'];
$calle=$_POST['calle'];
$nro_calle=$_POST['nro_calle'];
$departamento=$_POST['departamento'];
$localidad=$_POST['localidad'];
$fecha_inscripcion=$_POST['fecha_inscripcion'];




}

	if ($fecha_probable_parto!="")$$fecha_probable_parto=Fecha_db($fecha_probable_parto);
   else $fecha_probable_parto="1980-01-01";  
     $fecha_efectiva_parto=$fecha_probable_parto;
     $fecha_inscripcion = Fecha_db($fecha_inscripcion);
 
     if ($fecha_nac!="")$fecha_nac=Fecha_db($fecha_nac);
  	 else $fecha_nac="1980-01-01";  
    
     
     if ($fecha_efectiva_parto!="")$$fecha_efectiva_parto=Fecha_db($fecha_efectiva_parto);
   	else $fecha_efectiva_parto="1980-01-01";  
   
   if ($fecha_diganostico_embarazo!="")$$fecha_diagnostico_embarazo=Fecha_db($fecha_diagnostico_embarazo);
   else $fecha_diagnostico_embarazo="1980-01-01";  
   if ($fecha_carga =="") $fecha_carga= "2010-11-18 00:00:00";
   if ($fecha_inscripcion =="") $fecha_inscripcion= "2010-11-18 00:00:00";
   if ($semanas_embarazo=="") $semanas_embarazo = 0;
   if($score_riesgo=="")$score_riesgo = 0;
  
     //if ($anio_mayor_nivel == "") $anio_mayor_nivel = 0;
    // if ($anio_mayor_nivel_madre == "") $anio_mayor_nivel_madre = 0;
     $usuario = substr($usuario,0,9);


?>