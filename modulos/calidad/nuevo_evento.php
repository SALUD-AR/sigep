<?

require_once("../../config.php");

//esto es para cuando lo creo
if ($_POST['guardar']=="Guardar")
{$fecha=date("Y-m-d H:i:s");
  $db->StartTrans();
    $sql = "select nextval('evento_id_evento_seq') as id_evento";
    $id_eve = sql($sql,"Erro al traer el id_evento") or fin_pagina();
    $sql = "insert into evento (id_evento,id_tipo_evento,area,suseso,estado,medida)";
    $sql .= " values (".$id_eve->fields['id_evento'].",".$_POST['tipos'].",'".$_POST['area']."','".$_POST['suseso']."',1,'".$_POST['medida']."')";
    $resultconsulta = sql($sql,"Error al insertar el nuevo evento/incidente") or fin_pagina(); 
    $sql = "insert into log_evento (id_evento,usuario,fecha,comentario)";
    $sql .= " values (".$id_eve->fields['id_evento'].",'$_ses_user[name]','$fecha','Creado')";
    $resultconsulta = sql($sql,"Error al insertar el log") or fin_pagina(); 
  $db->CompleteTrans();
  header("location:eventos_incidentes.php");
  
}
//************************************************************************************************************

//para cuando es editar
if ($_POST['editar']=="Guardar")
{$fecha=date("Y-m-d H:i:s");
  $db->StartTrans();    
    $sql = "update evento set id_tipo_evento=".$_POST['tipos'].",area='".$_POST['area']."',suseso='".$_POST['suseso']."',medida='".$_POST['medida']."',estado=1 where id_evento=".$_POST['id_evento'];    
    $resultconsulta = sql($sql,"Error al insertar el nuevo evento/incidente") or fin_pagina(); 
    $sql = "insert into log_evento (id_evento,usuario,fecha,comentario)";
    $sql .= " values (".$_POST['id_evento'].",'$_ses_user[name]','$fecha','Modificado')";
    $resultconsulta = sql($sql,"Error al insertar el log") or fin_pagina(); 
  $db->CompleteTrans();
  header("location:eventos_incidentes.php");
}
//***********************************************************************************************************


//para cuando lo paso a terminado
if ($_POST['terminar']=="Terminar")
{$fecha=date("Y-m-d H:i:s");
  $db->StartTrans();    
    $sql = "update evento set id_tipo_evento=".$_POST['tipos'].",area='".$_POST['area']."',suseso='".$_POST['suseso']."',medida='".$_POST['medida']."',estado=2 where id_evento=".$_POST['id_evento'];    
    $resultconsulta = sql($sql,"Error al insertar el nuevo evento/incidente") or fin_pagina(); 
    $sql = "insert into log_evento (id_evento,usuario,fecha,comentario)";
    $sql .= " values (".$_POST['id_evento'].",'$_ses_user[name]','$fecha','Terminado')";
    $resultconsulta = sql($sql,"Error al insertar el log") or fin_pagina(); 
  $db->CompleteTrans();
  header("location:eventos_incidentes.php");
}
//***********************************************************************************************************

if ($parametros['id_evento'])
   {$sql = "select * from evento where id_evento=".$parametros['id_evento'];
    $resul_sql = sql($sql,"Error al traer los datos del evento $sql") or fin_pagina();
    $area=$resul_sql->fields['area'];
    $tipo=$resul_sql->fields['id_tipo_evento'];
    $suseso=$resul_sql->fields['suseso'];
    $medida=$resul_sql->fields['medida'];
   	
   }	

?>


<?=$html_header?>

</head>

 <form name="nuevo_evento" method="POST" action="nuevo_evento.php">
 <br>
 <table align="center" width="100%">
  <tr>
   <td align="center">
    <font size="3"><b><u>Eventos/Incidentes</u></b></font>
   </td>
  </tr>
 </table>
 <br>
 <? if ($parametros['id_evento']!=-1)
 {$sql = "select * from log_evento where id_evento=".$parametros['id_evento'];
  $resul_log = sql($sql,"Error a traer los log"); 	
 ?>	
  <table class="bordes" align="center" width="80%">
   <tr id="mo">
    <td align="center">
     <b>Fecha</b>
    </td>
    <td align="center">
     <b>Usuario</b>
    </td>
    <td align="center">
     <b>Acción</b>
    </td>
   </tr>
   <?
    while (!$resul_log->EOF)
    {
   ?>
    <tr id="ma">
     <td align="center">
      <?=fecha($resul_log->fields['fecha'])?>
     </td>
     <td align="center">
      <?=$resul_log->fields['usuario']?>
     </td>
     <td align="center">
      <?=$resul_log->fields['comentario']?>
     </td>
    </tr>
   <?
    $resul_log->MoveNext();
    }
    ?>
  </table>
  <?
  }
  ?>
  <br>
  <table align="center" width="60%" class="bordes" cellpadding="1">
   <tr>
    <td>
     <b><font size="2">Area: </font></b>
    </td>
    <td>
     <input <?if ($parametros['cmd']=="terminadas") echo "readonly"?>  name="area" type="text" size="25" value="<?=$area?>"> 
    </td>    
   </tr>
   <tr>
    <td>
     <b><font size="2"> Tipo:</font></b>     
    </td>
    <td>
     <select name="tipos" <?if ($parametros['cmd']=="terminadas") echo "disabled"?> >
      <?$sql = "select * from tipo_evento";
        $resul_eventos=sql($sql,"Error no se pudieron consultar los eventos ene l select de eventos");
        $selected="";
        while (!$resul_eventos->EOF)
        {if ($resul_eventos->fields['id_tipo_evento']==$tipo) $selected="selected";
      ?>     
      <option <?=$selected?> value="<?=$resul_eventos->fields['id_tipo_evento']?>"><?=$resul_eventos->fields['tipo_evento']?></option>     
      <?
        $selected="";
        $resul_eventos->MoveNext();
        }
      ?>
     </select>     
     </td>
   </tr>
   <tr>
    <td colspan="2">
     <b><font size="2"> Suceso:</font></b>
    </td>
   </tr>
   <tr>
    <td colspan="2">
     <textarea <?if ($parametros['cmd']=="terminadas") echo "readonly"?> name="suseso" cols="100" rows="10"><?=$suseso?></textarea>
    </td>
   </tr> 
   <tr>
    <td colspan="2">
     <b><font size="2"> Medida Tomada:</font></b>
    </td>
   </tr>
   <tr>
    <td colspan="2">
     <textarea <?if ($parametros['cmd']=="terminadas") echo "readonly"?> name="medida" cols="100" rows="10"><?=$medida?></textarea>
    </td>
   </tr> 
  </table>
  <br>
<? if ($parametros['cmd']=="pendientes") 
{
	?> 
  <table align="center" width="50%" border="1">
   <tr>
    <? if ($parametros['id_evento']==-1) {
    ?> 	
    <td align="center">
     <input type="submit" name="guardar" value="Guardar">
    </td>
    <?}
    else {
    ?>
    <td align="center">
     <input type="submit" name="editar" value="Guardar">
    </td>
    <td align="center">
     <input type="submit" name="terminar" value="Terminar">
    </td>
    <td align="center">
     <input type="button" name="volver" value="Volver" onclick="document.location='eventos_incidentes.php'">
    </td>
    <?
    }
    ?>
   </tr>
   <input type="hidden" name="id_evento" value="<?=$parametros['id_evento']?>">
  </table>
  <?
  }
 ?>
 </form>
 <?=fin_pagina();?>

