<?php
require_once("../../config.php");

if ($_POST['guardar_valor_deseable']=='Guardar'){	
	$desc_indicadores_deseable=$_POST['desc_indicadores_deseable'];
	$valor_deseable=$_POST['valor_deseable'];
	$sql="update calidad.desc_indicador_ins set valor_deseable=$valor_deseable where id_desc_indicador_ins=$desc_indicadores_deseable";				
	sql($sql,'NO puede insertar');	
}

if ($_POST['guardar']=='Guardar'){
	$anio=$_POST['anio'];
	$mes=$_POST['mes'];
	$desc_indicadores=$_POST['desc_indicadores'];
	$valor=$_POST['valor'];
	
	$sql="select * from calidad.indicadores_ins 
			where id_desc_indicador_ins = '$desc_indicadores' and mes='$mes' and anio='$anio'";	
	$result = sql($sql,'NO puede ejecutar la consulta de validacion de insercion');
	
	if ($result->EOF){
		$sql="insert into calidad.indicadores_ins (id_desc_indicador_ins,mes,anio,valor) 
				   values ('$desc_indicadores',$mes,'$anio','$valor')";				
	    sql($sql,'NO puede insertar');		
	}
	else{
		$sql="update calidad.indicadores_ins set valor='$valor'
				where (id_desc_indicador_ins='$desc_indicadores' and mes='$mes' and anio='$anio')";				
	    sql($sql,'NO puede insertar');
	}	
}

echo $html_header;
echo "<br>";

?>
<form action="indicadores_guardar_ins.php" method="post" name="form_indicadores_guardar">
<script>
function control_datos()
{
 if(document.all.anio.value=="-1"){
 	alert('Debe Seleccionar un año');
  	return false;
 }
 if(document.all.mes.value=="-1"){
 	alert('Debe Seleccionar un mes');
  	return false;
 }
 if(document.all.desc_indicadores.value=="-1"){
 	alert('Debe Seleccionar un Indicador');
  	return false;
 } 
 
 if(document.all.valor.value==""){
  alert('Debe ingresar un Valor');
  return false;
 }
 
 return true;
}//de function control_nuevos()

function control_datos_deseable(){
	if(document.all.desc_indicadores_deseable.value=="-1"){
 		alert('Debe Seleccionar un Indicador');
  		return false;
 	} 
	if(document.all.valor_deseable.value==""){
  		alert('Debe ingresar un Valor');
  		return false;
 	}
 	return true;	
}

</script>
<br>
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
<tr id="mo">
    <td>
    	Agregar Valor Deseable del Indicador
    </td>
</tr>
<tr align="right">
	<td align="center">
	  <select name=desc_indicadores_deseable>
      <option value=-1>Seleccione</option>
                 <?
                 $sql= "select * from calidad.desc_indicador_ins order by id_desc_indicador_ins";
                 $result_indi=sql($sql) or fin_pagina();
                 while (!$result_indi->EOF){ 
                 	$id_indi=$result_indi->fields['id_desc_indicador_ins'];
                 	$nombre=$result_indi->fields['descripcion'];
                 ?>
                   <option value=<?=$id_indi;?> ><?=$nombre?></option>
                 <?$result_indi->movenext();
                 }?>
      </select>
      
      <input type="text" name="valor_deseable" value="" style="width=60px">
		
	  <INPUT type="submit" name="guardar_valor_deseable" value="Guardar" onclick="return control_datos_deseable()">
	</td>
</tr>
</table>
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
<tr id="mo">
    <td>
    	Agregar Valor del Indicador
    </td>
</tr>
<tr align="right">
	<td align="center">
	
		<select name=anio>
	     <option value=-1>Seleccione</option>	     
	     <option value=2007 selected>2007</option>
	     <option value=2008>2008</option>                 
	     <option value=2009>2009</option>                 
	     <option value=2010>2010</option>                 
	     <option value=2011>2011</option>                 
	     <option value=2012>2012</option>                 
	     <option value=2013>2013</option>                 
	    </select>
	    
	    <select name=mes>
	     <option value=-1>Seleccione</option>
	     <option value=1>Enero</option>
	     <option value=2>Febrero</option>
	     <option value=3>Marzo</option>                 
	     <option value=4>Abril</option>                 
	     <option value=5>Mayo</option>                 
	     <option value=6>Junio</option>                 
	     <option value=7>Julio</option>                 
	     <option value=8>Agosto</option>                 
	     <option value=9>Septiembre</option>                 
	     <option value=10>Octubre</option>                 
	     <option value=11>Noviembre</option>                 
	     <option value=12>Diciembre</option>	     
	    </select>
	      
      <select name=desc_indicadores>
      <option value=-1>Seleccione</option>
                 <?
                 $sql= "select * from calidad.desc_indicador_ins order by id_desc_indicador_ins";
                 $result_indi=sql($sql) or fin_pagina();
                 while (!$result_indi->EOF){ 
                 	$id_indi=$result_indi->fields['id_desc_indicador_ins'];
                 	$nombre=$result_indi->fields['descripcion'];
                 ?>
                   <option value=<?=$id_indi;?> ><?=$nombre?></option>
                 <?$result_indi->movenext();
                 }?>
      </select>
      
      <input type="text" name="valor" value="" style="width=60px">
		
	  <INPUT type="submit" name="guardar" value="Guardar" onclick="return control_datos()">		
	</td>
</tr>
</table>
<br>

</form>
<?fin_pagina();?>