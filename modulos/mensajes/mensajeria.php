<?php
require_once("../../config.php");
require_once("funciones.php");
switch ($_POST['boton1'])
{case "Enviar Nuevo Mensaje":{require_once("./nuevo_mens.php");
                              break;
                             }
 case "Reenviar Mensaje": {require_once("./redirige.php");
                            break;
                           }
 default:{
?>
<html>
<head>
<title>Mensajeria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script>
// funciones que iluminan las filas de la tabla
function sobre(src,color_entrada) {
    src.style.backgroundColor=color_entrada;src.style.cursor="hand";
}
function bajo(src,color_default) {
    src.style.backgroundColor=color_default;src.style.cursor="default";
}

function comprueba()
{var i;
if (document.form.cantr.value==0)
{alert("Debe seleccionar un mensaje");
 return false;
}
 if (document.form.cantr.value==1)
 {if(document.form.radio.checked)
  return true;
 }
 for(i=0;i<document.form.radio.length;i++)
 {if(document.form.radio[i].checked)
  return true;
 }//for
alert("Debe seleccionar un mensaje");
return false;
}

function borrar()
{var valor;
 if (!comprueba())
 return false;
 else
 {valor=prompt('Dime el motivo por el cual desestimas este mensaje','');
  if ((valor==null) || (valor==""))
   return false;
  else
  {window.document.form.mensaje.value=valor;
   return true;
  }
 }
}
function controlnoc(){
  alert("Hay ordenes de compra que están por vencer.");
}
function controlvenchoy(){
  alert("Hay ordenes de compra vencidas.");
}
</script>
</head>
<body bgcolor="#E0E0E0">
<?php 
//este codigo es el que controla si ordenes de compra por vencer o mensajes que venecen hoy
if(($_POST['boton1']!='Mensajeria')&&($_ses_mensajes_primera_v != 1)){
phpss_svars_set("_ses_mensajes_primera_v",1);
// $fecha_actual=date("Y-m-d H:i:s");
// $hora_actual=date("H:i:s");
$fecha_actual=date("Y-n-j");
$hora_actual=date("G:i");
// $fecha_a=substr($fecha_actual,0,10);
// $hora_a=substr($fecha_actual,11,16);
//echo 'fecha: '.$fecha_actual;
//echo 'hora: '.$hora_actual.'||||||||';
list($aa,$ma,$da) = explode("-",$fecha_actual); 
list($ha,$mia) = explode(":",$hora_actual);                
$sql="select tipo1, tipo2, fecha_vencimiento from  mensajes where terminado='f'";
db_tipo_res('a');
$result2=$db->Execute($sql) or die($db->ErrorMsg());
$noc=0;
$vench=0;
while (!$result2->EOF)
  {
     $fecha_v=substr($result2->fields['fecha_vencimiento'],0,10);
     $hora_v=substr($result2->fields['fecha_vencimiento'],11,16);
	 list($a,$m,$d) = explode("-",$fecha_v);
     list($hv,$miv,$sv)= explode(":",$hora_v);
//echo '|||actual:dia'.$da.'hora:'.$ha.'min:'.$mia.'vencim:dia'.$d.'hora'.$hv.'min:'.$miv;
     if(($result2->fields['tipo1']=='LIC')&&($result2->fields['tipo2']=='NOC'))
       { 
       	for($i=0;$i<=7;$i++)
        {
     	$fecha=date( "Y-m-d", mktime(0,0,0,$ma,$da+$i,$aa));
     	if($fecha_v==$fecha) $noc=1;
        if (($aa>$a)||(($aa==$a)&&($ma>$m))||(($aa==$a)&&($ma==$m)&&($da-$d>1))) $vench=1;
        elseif(($aa==$a)&&($ma==$m)&&($da==$d)&&(($ha>$hv)||(($ha==$hv)&&($mia>$miv)))) $vench=1;
        
        }//for
     	
     }//if
   $result2->MoveNext();   
  }//while
 if($noc){
 ?>
 <script language='JavaScript'>
  controlnoc();
 </script>
 <? 
 }//if noc

 if($vench){
 	?>
   <script language='JavaScript'>
     controlvenchoy();
   </script>
 <? }//vence hoy
 }//if	
 ?>
<form name="form" method="post" action="mensajeria.php">
<center>
    <table width="90%" border="0">
      <tr bgcolor="#c0c6c9">
        <td> <left><font color="#006699" size="2" face="Arial, helvetica, sans-serif"><b>&nbsp&nbspMensajeria:    (Hora: <?PHP echo $juan1=date("H");?>:<?php echo $juan2=date("i");?>)</b></font></left></td>
    </tr>
  </table>
    <table width="704" border="0">
      <tr> 
        <td width="101" height="21"> 
          <div align="right">De:</div>
        </td>
        <td width="138" valign="top"> 
          <select name="u_origen" style="font-family: helvetica, sans-serif; background-color: #F5F5F5; font-size: 8pt; border-style: solid; border-width: 0;">
            <?php if (isset($_POST['u_origen'])) echo'<option selected>'.$_POST['u_origen'].'</option>';?>
		     <option value=''></option>
           <?php
				$ssql1="select nombre from usuarios where nombre!='root';";
				db_tipo_res('a');
                $result1=$db->Execute($ssql1) or die($db->ErrorMsg());
				while(!$result1->EOF){
			 
				?>
            <option> 
            <?php echo $result1->fields['nombre'];?>
            </option>
			 
            <?php 
			$result1->MoveNext();
		}//while?>
          </select>
        </td>
        <td width="109" > 
          <div align="right">Hacia:</div>
        </td>
        <td width="130"> 
          <select name="u_destino" style="font-family: helvetica, sans-serif; background-color: #F5F5F5; font-size: 8pt; border-style: solid; border-width: 0;">
		  <?php if (isset($_POST['u_destino'])) echo'<option selected>'.$_POST['u_destino'].'</option>';?>
		   <option value=''></option>
            <?php
				$ssql1="select nombre from usuarios where nombre!='root';";
				db_tipo_res('a');
                $result1=$db->Execute($ssql1) or die($db->ErrorMsg());
				while(!$result1->EOF){
			 ?>
            <option> 
            <?php echo $result1->fields['nombre'];?>
            </option>
            <?php
            $result1->MoveNext();
            }//while
           ?>
          </select>
        </td>
        <td valign="top" width="206">&nbsp; </td>
      </tr>
      <tr> 
        <td height="35"> 
          <div align="right"><left>Buscar en:</left></div>
        </td>
        <td > <left> 
          <select name="en" style="font-family: helvetica, sans-serif; background-color: #F5F5F5; font-size: 8pt; border-style: solid; border-width: 0;">
		    <? if (isset($_POST['en'])){
		     switch($_POST['en']){
			  case "id_mensaje": $var='Id mensaje';break;
              case "nro_orden": $var='N&uacute;mero de orden';break;
              case "comentario": $var='Mensaje';break;
              case "titulo": $var='Título de mensaje';break;
              case "fecha_entrega": $var='Fecha de entrega';break;
              case "fecha_recibo": $var='Fecha de recibo';break;
              case "fecha_vencimiento":$var='Fecha de vencimiento';break;
			  case "fecha_terminado":$var='Fecha de terminado';break;
              case "estado_final":$var='Estado Final';break;
              case "usuario_origen":$var='Usuario Origen';break;
              case "usuario_destino":$var='Usuario Destino';break;
              case "usuario_finaliza": $var='Usuario Finaliza';break;
			  case "terminado": $var='Terminado';break;
			  case "desestimado": $var='Desestimado';break;
			  case "recibido": $var='Recibido';break;
              case "borrado": $var='Borrado';break;
              case "redirigido": $var='Redirigido';break;
              case "Todos los campos":$var ='';break;
			 }
			 echo'<option seleted value="'.$_POST['en'].'">'.$var.'</option>';
		  }
		  
		  ?>
		
		     <option value=""></option>
            <option value="id_mensaje">Id mensaje</option>
            <option value="nro_orden">N&uacute;mero de orden</option>
            <option value="comentario">Mensaje</option>
            <option value="titulo">T&iacute;tulo de mensaje</option>
            <option value="fecha_entrega">Fecha de entrega</option>
            <option value="fecha_recibo">Fecha de recibo</option>
            <option value="fecha_vencimiento">Fecha de vencimiento</option>
            <option value="fecha_terminado">Fecha de terminado</option>
            <option value="estado_final">Estado Final</option>
            <option value="usuario_origen">Usuario Origen</option>
            <option value="usuario_destino">Usuario Destino</option>
            <option value="usuario_finaliza">Usuario Finaliza</option>
			<option value="recibido">Recibido</option>
			<option value="desestimado">Desestimado</option>
			<option value="terminado">Terminado</option>
			<option value="borrado">Borrado</option>
            <option value="redirigido">Redirigido</option>
            <option value="">Todos los campos</option>
          </select>
          </left></td>
        <td > 
          <div align="right">Palabra:</div>
        </td>
        <td >
          <input style="background-color: #F5F5F5; font-size: 8pt; border-style: solid; border-width: 1;" type="text" name="palabra" value="<?php if (isset($_POST['palabra'])) echo $_POST['palabra']; else ''; ?>">
          </td>
        <td> 
          &nbsp;&nbsp;&nbsp;<input type="submit" style="border-style: outset;	border-width: 1px; border-color: #000000; background-color: #F5F5F5;
				color: #000000;font-size=8pt;text-align: center;cursor:hand;" name="boton1" value="Buscar">
        </td>
        </tr>
    </table>
    
    <table border="0" cellspacing="2" cellpadding="0" width="90%">
      <tr bgcolor="#006699"> 
        <td width="26" height="19" valign="top">&nbsp;</td>
        <td width="129" valign="top"> 
          <center>
            <a style="text-decoration:none" href=<?php echo "mensajeria.php?est=0"; ?>><font size="2" family="helvetica, sans-serif" color="#c0c6c9"><b>Fecha entrega</b></font></a> 
          </center>
        </td>
        <td width="396" valign="top"> 
          <center>
            <a style="text-decoration:none" href=<?php echo "mensajeria.php?est=1"; ?>><font size="2" family="helvetica, sans-serif" color="#c0c6c9"><b>Mensaje</b></font></a> 
          </center>
        </td>
        <td width="137" valign="top"> 
          <a style="text-decoration:none" href=<?php echo "mensajeria.php?est=2"; ?>><div align="center"><font size="2" family="helvetica, sans-serif" color="#c0c6c9"><b>Vencimiento</b></font></div></a>
        </td>
      </tr>
    </table>
		  
		  <div style="position:relative; width:100%; height:48%; overflow:auto;">
          
      <table border="0" cellspacing="2" cellpadding="0" width="90%">
        <?php
		   switch($est){
			  case 0: $orden=" order by fecha_entrega";break;
			  case 1: $orden=" order by comentario";break;
			  case 2: $orden=" order by fecha_vencimiento";break;
			  default:$orden=" order by fecha_recibo";break;
			}
            
	       $sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes".$orden; 			
		   if($_POST['boton1']=='Buscar'){
			$en=$_POST['en']; 
			$palabra=$_POST['palabra'];
			$u_dest=$_POST['u_destino'];
			$u_orig=$_POST['u_origen'];
            if($en=='Todos los campos')$en='';
			
			if(($en=='')&&($u_orig=='')&&($u_dest=='')&&($palabra==''))
			$sql="select desestimado, terminado, recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes".$orden; 			

			elseif(($en=='')&&($palabra=='')&&($u_dest==''))
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_origen='$u_orig'".$orden;

			elseif(($en=='')&&($palabra=='')&&($u_orig==''))
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_destino='$u_dest'".$orden;

			elseif(($en=='')&&($u_orig=='')&&($u_dest=='')){
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where";
			$sql.=" tipo2 like '%".$palabra."%' or tipo1 like '%".$palabra."%' or usuario_destino like '%".$palabra."%' or titulo like '%".$palabra."%' or fecha_entrega like '%".$palabra."%' ";
			$sql.="or fecha_vencimiento like '%".$palabra."%' or comentario like '%".$palabra."%' or id_mensaje like '%".$palabra."%' or usuario_finaliza like '%".$palabra."%' or usuario_origen like '%".$palabra."%' or ";
			$sql.="fecha_recibo like '%".$palabra."%' or fecha_terminado like '%".$palabra."%' or nro_orden like '%".$palabra."%' or numero like '%".$palabra."%' or estado_final like '%".$palabra."%'".$orden;
			}
			elseif(($palabra=='')&&($u_orig=='')&&($u_dest=='')&&(($en=='terminado')||($en=='desestimado')||($en=='recibido')))
			  $sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where ".$en."  ='1'".$orden;
            
			elseif(($palabra=='')&&($u_orig=='')&&($u_dest=='')&&(($en=='borrado')||($en=='redirigido')))
			  $sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where desestimado='t' and comentario  like '%".$en."%'".$orden;
            
			elseif(($palabra=='')&&($u_orig=='')&&($u_dest==''))
              $sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where ".$en."  =NULL".$orden;
			
			elseif(($en=='')&&($palabra==''))
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_origen='$u_orig' and usuario_destino='$u_dest'".$orden; 			

			elseif(($u_orig=='')&&($u_dest=='')&&(($en=='terminado')||($en=='desestimado')||($en=='recibido'))){
			 $sql= "select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where ".$en." ='1' and("; 			
			$sql.=" tipo2 like '%".$palabra."%' or tipo1 like '%".$palabra."%' or titulo like '%".$palabra."%' or fecha_entrega like '%".$palabra."%' ";
			$sql.="or fecha_vencimiento like '%".$palabra."%' or comentario like '%".$palabra."%' or id_mensaje like '%".$palabra."%' or usuario_finaliza like '%".$palabra."%' or usuario_origen like '%".$palabra."%' or ";
			$sql.="fecha_recibo like '%".$palabra."%' or fecha_terminado like '%".$palabra."%' or nro_orden like '%".$palabra."%' or numero like '%".$palabra."%' or estado_final like '%".$palabra."%')".$orden; 			
			}  			
			
			elseif(($u_orig=='')&&($u_dest=='')&&(($en=='borrado')||($en=='redirigido'))){
			$sql= "select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where desestimado='t' and comentario like '%".$en."%' and(";
			$sql.=" tipo2 like '%".$palabra."%' or tipo1 like '%".$palabra."%' or titulo like '%".$palabra."%' or fecha_entrega like '%".$palabra."%' ";
			$sql.="or fecha_vencimiento like '%".$palabra."%' or comentario like '%".$palabra."%' or id_mensaje like '%".$palabra."%' or usuario_finaliza like '%".$palabra."%' or usuario_origen like '%".$palabra."%' or ";
			$sql.="fecha_recibo like '%".$palabra."%' or fecha_terminado like '%".$palabra."%' or nro_orden like '%".$palabra."%' or numero like '%".$palabra."%' or estado_final like '%".$palabra."%')".$orden; 			
			}  			
			elseif (($u_orig=='')&&($u_dest=='')) $sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where ".$en." like '%".$palabra."%'".$orden; 			
			
			elseif(($u_orig=='')&&($en=='')){
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_destino='$u_dest' and (";
			$sql.=" tipo2 like '%".$palabra."%' or tipo1 like '%".$palabra."%' or titulo like '%".$palabra."%' or fecha_entrega like '%".$palabra."%' ";
			$sql.="or fecha_vencimiento like '%".$palabra."%' or comentario like '%".$palabra."%' or id_mensaje like '%".$palabra."%' or usuario_finaliza like '%".$palabra."%' or usuario_origen like '%".$palabra."%' or ";
			$sql.="fecha_recibo like '%".$palabra."%' or fecha_terminado like '%".$palabra."%' or nro_orden like '%".$palabra."%' or numero like '%".$palabra."%' or estado_final like '%".$palabra."%')".$orden;
				
 			}	
			elseif(($u_orig=='')&&($palabra=='')&&(($en=='borrado')||($en=='redirigido')))
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_destino='$u_dest' and desestimado='t' and comentario like '%".$en."%'".$orden; 			
			
			elseif(($u_orig=='')&&($palabra=='')&&(($en=='desestimado')||($en=='terminado')||($en=='recibido')))
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_destino='$u_dest' and ".$en."='1'".$orden; 			
			
			elseif(($u_orig=='')&&($palabra==''))
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_destino='$u_dest' and ".$en."=NULL".$orden; 			

			elseif(($u_dest=='')&&($en=='')){
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_origen='$u_orig' and (";
			$sql.=" tipo2 like '%".$palabra."%' or tipo1 like '%".$palabra."%' or usuario_destino like '%".$palabra."%' or titulo like '%".$palabra."%' or fecha_entrega like '%".$palabra."%' ";
			$sql.="or fecha_vencimiento like '%".$palabra."%' or comentario like '%".$palabra."%' or id_mensaje like '%".$palabra."%' or usuario_finaliza like '%".$palabra."%' or ";
			$sql.="fecha_recibo like '%".$palabra."%' or fecha_terminado like '%".$palabra."%' or nro_orden like '%".$palabra."%' or numero like '%".$palabra."%' or estado_final like '%".$palabra."%')".$orden;
			} 			

			elseif(($u_dest=='')&&($palabra=='')&&(($en=='borrado')||($en=='redirigido')))
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_origen='$u_orig' and desestimado='t' and comentario like '%".$en."%'".$orden; 			
			
			elseif(($u_dest=='')&&($palabra=='')&&(($en=='desestimado')||($en=='terminado')||($en=='recibido')))
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_origen='$u_orig' and ".$en."='1'".$orden; 			
			
			elseif(($u_dest=='')&&($palabra==''))
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_origen='$u_orig' and ".$en."=NULL".$orden; 			

			elseif (($u_dest=='')&&(($en=='borrado')||($en=='redirigido'))){
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_origen='$u_orig' and desestimado='t' and comentario like '%".$en."%' and("; 			
			$sql.=" tipo2 like '%".$palabra."%' or tipo1 like '%".$palabra."%' or usuario_destino like '%".$palabra."%' or titulo like '%".$palabra."%' or fecha_entrega like '%".$palabra."%' ";
			$sql.="or fecha_vencimiento like '%".$palabra."%' or comentario like '%".$palabra."%' or id_mensaje like '%".$palabra."%' or usuario_finaliza like '%".$palabra."%' or ";
			$sql.="fecha_recibo like '%".$palabra."%' or fecha_terminado like '%".$palabra."%' or nro_orden like '%".$palabra."%' or numero like '%".$palabra."%' or estado_final like '%".$palabra."%')".$orden;
			}
			elseif (($u_dest=='')&&(($en=='desestimado')||($en=='terminado')||($en=='recibido'))){
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_origen='$u_orig' and ".$en."='1' and ("; 			
			$sql.=" tipo2 like '%".$palabra."%' or tipo1 like '%".$palabra."%' or usuario_destino like '%".$palabra."%' or titulo like '%".$palabra."%' or fecha_entrega like '%".$palabra."%' ";
			$sql.="or fecha_vencimiento like '%".$palabra."%' or comentario like '%".$palabra."%' or id_mensaje like '%".$palabra."%' or usuario_finaliza like '%".$palabra."%' or ";
			$sql.="fecha_recibo like '%".$palabra."%' or fecha_terminado like '%".$palabra."%' or nro_orden like '%".$palabra."%' or numero like '%".$palabra."%' or estado_final like '%".$palabra."%')".$orden;
			} 			

			elseif ($u_dest=='')
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_origen='$u_orig' and ".$en." like '%".$palabra."%'".$orden; 			
			
			elseif (($u_orig=='')&&(($en=='borrado')||($en=='redirigido'))){
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_destino='$u_dest' and desestimado='t' and comentario like '%".$en."%' and (";
			$sql.=" tipo2 like '%".$palabra."%' or tipo1 like '%".$palabra."%' or usuario_destino like '%".$palabra."%' or titulo like '%".$palabra."%' or fecha_entrega like '%".$palabra."%' ";
			$sql.="or fecha_vencimiento like '%".$palabra."%' or comentario like '%".$palabra."%' or id_mensaje like '%".$palabra."%' or usuario_finaliza like '%".$palabra."%' or ";
			$sql.="fecha_recibo like '%".$palabra."%' or fecha_terminado like '%".$palabra."%' or nro_orden like '%".$palabra."%' or numero like '%".$palabra."%' or estado_final like '%".$palabra."%')".$orden;
			} 			
			
			elseif (($u_orig=='')&&(($en=='desestimado')||($en=='terminado')||($en=='recibido'))){
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_destino='$u_dest' and ".$en."='1' and ("; 			
			$sql.=" tipo2 like '%".$palabra."%' or tipo1 like '%".$palabra."%' or usuario_destino like '%".$palabra."%' or titulo like '%".$palabra."%' or fecha_entrega like '%".$palabra."%' ";
			$sql.="or fecha_vencimiento like '%".$palabra."%' or comentario like '%".$palabra."%' or id_mensaje like '%".$palabra."%' or usuario_finaliza like '%".$palabra."%' or ";
			$sql.="fecha_recibo like '%".$palabra."%' or fecha_terminado like '%".$palabra."%' or nro_orden like '%".$palabra."%' or numero like '%".$palabra."%' or estado_final like '%".$palabra."%')".$orden;
			} 			

			elseif ($u_orig=='')
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_destino='$u_dest' and ".$en." like '%".$palabra."%'".$orden; 			

			elseif ($en==''){
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_destino='$u_dest' and usuario_origen='$u_orig' and ("; 			
			$sql.=" tipo2 like '%".$palabra."%' or tipo1 like '%".$palabra."%' or titulo like '%".$palabra."%' or fecha_entrega like '%".$palabra."%' ";
			$sql.="or fecha_vencimiento like '%".$palabra."%' or comentario like '%".$palabra."%' or id_mensaje like '%".$palabra."%' or usuario_finaliza like '%".$palabra."%' or ";
			$sql.="fecha_recibo like '%".$palabra."%' or fecha_terminado like '%".$palabra."%' or nro_orden like '%".$palabra."%' or numero like '%".$palabra."%' or estado_final like '%".$palabra."%')".$orden;
		    }
			elseif (($palabra=='')&&(($en=='borrado')||($en=='redirigido')))
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_destino='$u_dest' and usuario_origen='$u_orig' and desestimado='t' and comentario like '%".$en."%'".$orden; 			

			elseif ($palabra=='')
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_destino='$u_dest' and usuario_origen='$u_orig' and ".$en."=NULL".$orden; 			

			elseif (($en=='desestimado')||($en=='terminado')||($en=='recibido')){
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_destino='$u_dest' and usuario_origen='$u_orig' and ".$en."='1' and("; 
			$sql.=" tipo2 like '%".$palabra."%' or tipo1 like '%".$palabra."%' or titulo like '%".$palabra."%' or fecha_entrega like '%".$palabra."%' ";
			$sql.="or fecha_vencimiento like '%".$palabra."%' or comentario like '%".$palabra."%' or id_mensaje like '%".$palabra."%' or usuario_finaliza like '%".$palabra."%' or usuario_origen like '%".$palabra."%' or ";
			$sql.="fecha_recibo like '%".$palabra."%' or fecha_terminado like '%".$palabra."%' or nro_orden like '%".$palabra."%' or numero like '%".$palabra."%' or estado_final like '%".$palabra."%')".$orden;
			}
			elseif (($en=='borrado')||($en=='redirigido')){
			$sql="select desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_destino='$u_dest' and usuario_origen='$u_orig' and desestimado='t' and comentario like '%".$en."%' and("; 
			$sql.=" tipo2 like '%".$palabra."%' or tipo1 like '%".$palabra."%' or titulo like '%".$palabra."%' or fecha_entrega like '%".$palabra."%' ";
			$sql.="or fecha_vencimiento like '%".$palabra."%' or comentario like '%".$palabra."%' or id_mensaje like '%".$palabra."%' or usuario_finaliza like '%".$palabra."%' or usuario_origen like '%".$palabra."%' or ";
			$sql.="fecha_recibo like '%".$palabra."%' or fecha_terminado like '%".$palabra."%' or nro_orden like '%".$palabra."%' or numero like '%".$palabra."%' or estado_final like '%".$palabra."%')".$orden;
			}				
 			else 
			$sql="select tipo1,tipo2,desestimado, terminado,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento from mensajes where usuario_destino='$u_dest' and usuario_origen='$u_orig' and ".$en." like '%".$palabra."%'".$orden; 
			
	       }//if
	       
		
	    //echo"<br>".$sql;
	    db_tipo_res('a');
        $result2=$db->Execute($sql) or die($db->ErrorMsg());
		$i=0;
		   while (!$result2->EOF){
		   
		     $flag=0; //me dice si debo poner la fuente de color azul o no	               
           	
	   			if ($i==0)
	   				{$color=$bgcolor2;
	    			 $color2=$bgcolor1;
	    			 $i=1;
	   				}
	   			else
	   			{$color=$bgcolor1;
	    		 $color2=$bgcolor2;
	    		 $i=0;
	   			}
	   			$cantidad+=$result2->RecordCount();
	   			 if ($result2->fields['desestimado']=='t') $color1="FF0000";//rojo
	   			 elseif ($result2->fields['terminado']=='t') $color1="FFFF00"; //amarillo
                 else $color1='';
                 
                 //$fecha_actual=date("Y/m/d H:m:s");
				 $fecha_actual=date("Y/m/d");
				 $hora_actual=date("H:i");
                 //$fecha_a=substr($fecha_actual,0,10);
                 //$hora_a=substr($fecha_actual,10,16);
		         list($aa,$ma,$da) = explode("/",$fecha_actual);
		         list($ha,$mia)= explode(":",$hora_actual);
           
                 $fecha_v=substr($result2->fields['fecha_vencimiento'],0,10);
                 $hora_v=substr($result2->fields['fecha_vencimiento'],11,16);
	   		     
				 list($a,$m,$d) = explode("-",$fecha_v);
                 list($hv,$miv,$sv)= explode(":",$hora_v);
				 if (($aa>$a)||(($aa==$a)&&($ma>$m))||(($aa==$a)&&($ma==$m)&&($da-$d>=1)))
                 $colorfv="FF0000";//rojo
                 elseif(($aa==$a)&&($ma==$m)&&($da==$d)&&(($ha>$hv)||(($ha==$hv)&&($mia>$miv)))) $colorfv="FF0000";  //rojo  
			     else $colorfv=$color2; 
             
				?>
        <a href="ver_mens.php?id_mensaje=<? echo $result2->fields['id_mensaje'];?>&donde=1"> 
        <tr bgcolor="<?php echo $color; ?>"  title="<?php echo $result2->fields['titulo']; ?>" onMouseOver="sobre(this,'#FFFFFF');" onMouseOut="bajo(this,'<? echo $color;?>' );"> 
          <td width="24" height="18" valign="top" bgcolor="<?php echo $color1;?>" > 
            <input type="radio" name="radio" value="<?php echo $result2->fields['id_mensaje'] ?>">
          </td>
          <td width="131" valign="top" > 
            <center>
              <font size=2 color="<?php echo $color2; ?>"> 
              <?php 
              $fecha1=fecha(substr($result2->fields['fecha_entrega'],0,10));
			  $tiempo1=substr($result2->fields['fecha_entrega'],10,18);
			  echo $fecha1.$tiempo1;
			  ?>
              </font> 
            </center>
          </td>
          <td width="396" valign="top"> <font size=2 color="<?php echo $color2; ?>"> 
            <center>
              <?php echo $result2->fields['comentario'];?>
            </center>
            </font> </td>
          <td width="137" valign="top" ><font size=2 color="<?php echo $colorfv; ?>"> 
            <center>
              <?php 
                    $fecha=substr($result2->fields['fecha_vencimiento'],0,10);
					list($a,$m,$d)=explode('-',$fecha);
					$fecha=$d.'/'.$m.'/'.$a;
					$tiempo=substr($result2->fields['fecha_vencimiento'],10,18);
					echo $fecha.$tiempo; ?>
            </center>
            </font></td>
        </tr>
        </a> 
        <input type="hidden" name="comentario[<?php echo $result2->fields['id_mensaje']; ?>]" value="<?php echo $result2->fields['comentario']; ?>">
        
        <?php 
        $result2->MoveNext();
		}//while ?>
      </table>
          
    </div>
   <hr size="10">
   
    <table border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='367' align="center" height="21">
      <tr> 
        <td width=19 valign="top" height=23 bgcolor='#FFFF00' bordercolor='#000000'>&nbsp;</td>
        <td width=158 valign="top" bordercolor='#FFFFFF'><font face="Arial, Helvetica, sans-serif" size="2">Mensaje 
          Terminado</font></td>
        <td width=19 valign="top" bgcolor='#FF7878' bordercolor='#000000'>&nbsp;</td>
        <td width="160" valign="top" bordercolor='#FFFFFF'><font size="2" face="Arial, Helvetica, sans-serif">Mensaje 
          Desestimado</font></td>
      </tr>
    </table>
    <br>
<input type="hidden" name="cantr" value="<?PHP echo $cantidad; ?>">
<input type="hidden" name="mensaje" value="">
    &nbsp&nbsp&nbsp
<input type="submit" name="boton1" value="Enviar Nuevo Mensaje">
&nbsp&nbsp&nbsp<input type="submit" name="boton1" value="Reenviar Mensaje" onClick="return comprueba();">
    
</center>
</form>

</body>
</html>
<?php
 }
}// fin switch
?>