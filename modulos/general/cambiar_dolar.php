<?

require_once("../../config.php");

echo $html_header;

//presiona el boton c para cambiar valor del dolar distinto de la fecha actual
if ($_POST['guardar'])  {
 $fecha=fecha_db($_POST['fecha_act']);
 $usuario=$_ses_user['name'];
 $fecha_actual=date("Y-m-d");
 
 $dolar=$_POST["text_dolar"];
 $comentario=$_POST['comentario'];
 if ($dolar=='null' || $dolar=="" ) {
   Error ("Ingrese un valor valido para el dolar");
 }
 
 
 if (!$error) {
 $sql="select valor_dolar from dolar_comparacion where fecha='$fecha'";
 $res_fec=sql($sql) or fin_pagina();
 if ($res_fec -> RecordCount() > 0){
     $sql="update dolar_comparacion set valor_dolar=".$dolar." ,usuario='$usuario' ,fecha_cambio= '$fecha_actual',comentario='$comentario'
             where fecha='$fecha'";
 } else {
    $sql="insert into dolar_comparacion (fecha,valor_dolar,usuario,fecha_cambio,comentario)
         values('$fecha',$dolar,'$usuario','$fecha_actual','$comentario')";
 }
 
 sql($sql) or fin_pagina();
 }
 ?>
<script>
 window.opener.document.form1.submit();
 window.close();
</script> 
<?
 
}

?>

<form action='cambiar_dolar.php' method='POST'>
<? $fecha=$parametros['fecha'] or $_POST['fecha'];
   $dolar=$parametros['dolar'] or $_POST['dolar'];
   $comentario=$_POST['comentario'];
   ?> 
<input type='hidden' name='fecha_act' value=<?=$fecha?> >
<input type='hidden' name='dolar' value=<?=$dolar?> >
<table align="center" cellpadding="2">
  <tr id=mo>
    <td colspan=2 align="center"> CAMBIAR VALOR DOLAR </td>
  </tr>
 <tr bgcolor=<?=$bgcolor_out?>>
    <td colspan="2"> <b>Fecha </b> <?=$fecha ?>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Valor </b> <?=number_format($dolar,"2",".","");?>
    </td>
 </tr>
 <tr bgcolor=<?=$bgcolor_out?>>
   <td colspan="2" align="center"> <b>Ingrese valor del dolar </b>
       &nbsp;&nbsp; <input type="text" name="text_dolar" value="">
   </td>
 </tr>
 <tr bgcolor=<?=$bgcolor_out?>>
    <td align="right"><b> Comentarios:<b> 	</td>	
     <td>   <textarea name="comentario" cols="40" rows="3"><?=$comentario?></textarea> </td>
 </tr>
</table>

<br>
<table align=center>
<tr>
   <td><input type='submit' name='guardar' value='Guardar' onclick="if ( (isNaN(document.all.text_dolar.value)) || (document.all.text_dolar.value=='')) {alert ('ingrese numero valido para el dolar');return false;} else return true;"></td>
   <td><input type='button' name='cerrar' value='Cerrar' onclick="window.close();"></td>
<tr>
</table>