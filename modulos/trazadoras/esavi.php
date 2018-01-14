<?php

require_once("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if($_POST["aceptar"]=="Aceptar"){
   $db->StartTrans();
   
    $sql = "select * from trazadoras.esavi where id_vacunas='$id_vacunas'";
	$result_esavi=sql($sql,"no se puede ejecutar");
	if ($result_esavi->recordcount()==0){
		$q="select nextval('trazadoras.esavi_id_esavi_seq') as id_planilla";
		$id_planilla=sql($q) or fin_pagina();
		$id_planilla=$id_planilla->fields['id_planilla'];
			   
		$query="insert into trazadoras.esavi
					 (id_esavi,id_vacunas,observaciones)
					 values
					 ('$id_planilla','$id_vacunas','$observaciones')";
		
		sql($query, "Error al insertar la Planilla") or fin_pagina();
	}  
	else{
		$query="UPDATE trazadoras.esavi
					 SET
					 observaciones=$observaciones
					 where id_vacunas='$id_vacunas'";
		
		sql($query, "Error al insertar la Planilla") or fin_pagina();
	}
   $db->CompleteTrans();
   
   $ref = encode_link("../entrega_leche/listado_beneficiarios_leche.php",array());                      
   echo "<script>   			
   			location.href='$ref';
   		</script>";    	
 }

$sql = "select * from trazadoras.vacunas where id_vacunas='$id_vacunas'";
$result=sql($sql,"no se puede ejecutar");

$sql = "select * from trazadoras.esavi where id_vacunas='$id_vacunas'";
$result_esavi=sql($sql,"no se puede ejecutar");
$observaciones=$result_esavi->fields['observaciones'];

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 if(document.all.observaciones.value==""){
  alert('Debe Ingresar una Observacion');
  return false;
 }
 
 if (confirm('Esta Seguro que Desea Agregar Dato?'))return true;
 else return false;	
 

}
</script>
<form name=form1 action="esavi.php" method=POST>
<input type="hidden" name="id_vacunas" value="<?=$id_vacunas?>">

<table cellspacing=2 cellpadding=2 width=40% align=center class=bordes>
<br>
    <tr id="mo">
    	<td align="center" colspan="2" class=bordes>
    		<b><font size="+1">Carga Esavi</font></b>
    	</td>    	
    </tr>
    
    <tr>
		<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='2' name='observaciones'><?=$observaciones?></textarea>
            </td>
	</tr>
    
    <tr>
    	<td align="center" colspan="2" class=bordes id="mo">
    		<input type="submit" name="aceptar" value="Aceptar" onclick="return control_nuevos()" Style="width=200px">
    		&nbsp;&nbsp;&nbsp;
    		<input type="button" name="cerrar" value="Volver" onclick="document.location='../entrega_leche/listado_beneficiarios_leche.php'" Style="width=200px">
    	</td>    	
    </tr>    
</table>
<br>
<br>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
