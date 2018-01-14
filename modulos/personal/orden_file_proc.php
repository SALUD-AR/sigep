<?
/*
Autor:  CarlitoX
Creado: viernes 06/08/04
MODIFICADA POR
$Author: gabriel $
$Revision: 1.2 $
$Date: 2005/09/06 13:55:46 $
*/
$id_capacitacion=$parametros['id_capacitacion'];
$user=$parametros['user'];
if ($_POST['baceptar']){
    $acceso="Todos";
    $comentario="Archivo de capacitación";
    $fecha=date("Y/m/d");
    $files_total=count($_FILES['archivo']["name"]);
    $error_vector=array();
    for ($i=0; $i < $files_total ; $i++ ){
    	$filename=$_FILES["archivo"]["name"][$i];
    	$tamanio=$_FILES['archivo']["size"][$i];
	    if (!$filename) $error_msg="Debe seleccionar un archivo";
	    elseif ($_FILES["archivo"]["error"][$i]) $error_msg="El archivo '$filename' es muy grande ";
	    if (!$error_msg){
	    	if (subir_archivo($_FILES["archivo"]["tmp_name"][$i],UPLOADS_DIR."/archivos/$filename",$error_msg)===true){
	         $sql="select nextval('subir_archivos_id_seq') as idfile ";
	         $res=sql($sql) or $db->errormsg()."<br>";
	         $idfile=$res->fields['idfile'];
	         $q="INSERT INTO personal.archivos_capacitaciones 
	              (id_capacitacion, nombre,comentario,creadopor,fecha,size,acceso) Values
	              ($id_capacitacion,'$filename','$comentario','".$user."','$fecha','$tamanio','$acceso')";
	         if (!sql($q)) $error_msg="No se pudo insertar el archivo ".$db->errormsg()."<br>$q ";	
	         else $ok_msg="El archivo '$filename' se subió con éxito";
	         echo "<script>window.opener.location.reload();</script>";
	    	}
	    }
	     $error_vector[]=$error_msg;
	     $error_msg="";
	     $ok_vector[]=$ok_msg;
	     $ok_msg="";
    }
}
?>