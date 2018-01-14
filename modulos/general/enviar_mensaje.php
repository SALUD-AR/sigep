<?php

include("funciones.php");

/**
 * @return void
 * @param hora_venc hora que vence el mensaje Ej: 18:30
 * @param fecha_venc fecha que vence el mensaje dia/mes/año
 * @param mensaje motivo del mensaje
 * @param tipo1 tipo de mensaje Ej: Licitaciones, entonces LIC (Ver tabla tipo_de_mensaje)
 * @param tipo2 segundo tipo del mensaje Ej: Nueva orden necesita control y aprobacion, entonces EDC
 * @param para destinatario del mensaje
 * @desc permite enviar mensajes entre usuarios (en carpeta general hay un ejemplo)
 */
function enviar_mensaje($hora_venc,$fecha_venc,$mensaje,$tipo1,$tipo2,$para)
{global $db,$_ses_user_login;
$hora_venc.=":00";
$fecha_venc=ereg_replace("/","-",$fecha_venc);
$fecha_venc=ConvFecha($fecha_venc);
list($h,$m,$s)= explode(":",$hora_venc);
if(!(is_numeric($h) && is_numeric($m)))
 $hora='00:00:00';
$fecha_venc=$fecha_venc.' '.$hora_venc;
$finicio=date("Y-m-d H:i:s");
$sql="select nombre from usuarios where login='$_ses_user_login';";
$result=$db->Execute($sql) or die($db->ErrorMsg());
$user=$result->fields[0];
$ssql_tit="select titulo from tipo_de_mensaje where tipo1='$tipo1' and tipo2='$tipo2'";
$result1=$db->Execute($ssql_tit) or die($db->ErrorMsg());
$tit=$result1->fields[0].' '.$user;
$ssq_todos="select nombre from usuarios where nombre='$para';";
$result=$db->Execute($ssq_todos) or die($db->ErrorMsg());
$para=$result->fields[0];
$ssql_ins="insert into mensajes (tipo1,tipo2,numero,usuario_origen,comentario,";
$ssql_ins.=" usuario_destino,fecha_entrega,fecha_vencimiento,nro_orden,recibido,terminado,desestimado,";
$ssql_ins.="titulo) values ('$tipo1','$tipo2',1,'$user','$mensaje','$para','$finicio', '$fecha_venc',1,false,false,false,'$tit')";
echo $ssql_ins;
$db->Execute($ssql_ins) or die($db->ErrorMsg());
}
?>