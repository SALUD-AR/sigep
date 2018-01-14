<?php
require_once("../../config.php");
include("funciones.php");
$hora=$_POST['hora'];
if($tipo_m && (strlen($hora)==5)) $hora.=':00'; //por ahora
list($h,$m,$s)= explode(":",$hora);
if(!(is_numeric($h) && is_numeric($m) && is_numeric($s))) $hora='00:00:00';
$f_venc=fecha_db($_POST['venc']).' '.$hora;
$mensaje=$_POST['nota'];
$finicio=date("Y-m-d H:i:s");
$tipo_m=$_POST['tipo_m'];//me fijo si es nuevo mensaje o se redirige
$sql="select nombre from usuarios where login='".$_ses_user['login']."';";
$result=$db->Execute($sql) or die($db->ErrorMsg());
$user_n=$result->fields['nombre'];
$user=$_ses_user['login'];
//Consulto en BD para almacenar el login del destinatario en vez del nombre
$para=$_POST['para'];
if ($para!="Todos")
{$ssql_tit="select login from usuarios where login='$para'";
 $result1=$db->Execute($ssql_tit) or die($db->ErrorMsg());
 $para=$result1->fields['login'];
}

if($tipo_m) $tipo2='MTP';//insertamos nuevo mensaje
else {$tipo2='MRU'; //mensaje redirigido
     //el nro de orden viene desde redirigir.php como campo oculto
	$id_mensaje=$_POST['id_m'];
	$nro_ord=$_POST['nro_ord'];
	//variable que tiene el mensaje antes de ser modificado
	$anterior=$_POST['anterior'];
	//busco titulo	
	$ssql_tit="select titulo from tipo_de_mensaje where tipo1='MCP' and tipo2='MRU'";
	$result1=$db->Execute($ssql_tit) or die($db->ErrorMsg());
	$tit=$result1->fields['titulo'].' '.$user_n;
}//else
//busco titulo 
$ssql_tit="select titulo from tipo_de_mensaje where tipo1='MCP' and tipo2='".$tipo2."'";
db_tipo_res('a');
$result1=$db->Execute($ssql_tit) or die($db->ErrorMsg());
$tit=$result1->fields['titulo'].' '.$user_n;
if($para=='Todos'){
	 $ssq_todos="select login from usuarios where nombre!='root';";
	 db_tipo_res('a');
     $result_todos=$db->Execute($ssq_todos) or die($db->ErrorMsg());
	 while(!$result_todos->EOF){
     	$para=$result_todos->fields['login'];
     	$ssql_ins="insert into mensajes (tipo1,tipo2,numero,usuario_origen,comentario,";
        $ssql_ins.=" usuario_destino,fecha_entrega,fecha_vencimiento,nro_orden,recibido,terminado,desestimado,";
        $ssql_ins.="titulo) values ('MCP','$tipo2',1,'$user','$mensaje','$para', '$finicio', '$f_venc',1,false,false,false,'$tit')";
        db_tipo_res('a');
        $result=$db->Execute($ssql_ins) or die($db->ErrorMsg());
        $result_todos->MoveNext();
     }//while	 
  }//if para todos	
 else{
   $ssql_ins="insert into mensajes (tipo1,tipo2,numero,usuario_origen,comentario,";
   $ssql_ins.=" usuario_destino,fecha_entrega,fecha_vencimiento,nro_orden,recibido,terminado,desestimado,";
   $ssql_ins.="titulo) values ('MCP','$tipo2',1,'$user','$mensaje','$para', '$finicio', '$f_venc',1,false,false,false,'$tit')";
   db_tipo_res('a');
   $result=$db->Execute($ssql_ins) or die($db->ErrorMsg());
  }//else
header('location: ./mensajes.php');
?>