<?php
require_once("../../config.php");
$mens=$_POST['mensaje'];
$id=$_POST['radio'];
$tipo1=$_POST['tipo1']; 
$usuario=$user_firstname;
$usuario.=' ';
$usuario.=$user_name;
$fecha=date("Y-m-j H:i:s");
if($tipo1=="MCP") 
    $sql="update mensajes set terminado='true',fecha_terminado='".$fecha."',comentario='".$comentario[$id]."|||| FUE BORRADO justificativo: ".$mens."' where id_mensaje=".$id;
else
	$sql="update mensajes set desestimado='true',fecha_terminado='".$fecha."',comentario='".$comentario[$id]."|||| FUE BORRADO justificativo: ".$mens."' where id_mensaje=".$id;


$db->Execute($sql) or die($db->ErrorMsg());
header("location: ./mensajes.php");
?>