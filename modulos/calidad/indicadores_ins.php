<?php
require_once("../../config.php");

echo $html_header;
variables_form_busqueda("indicadores_ins");

if (!$cmd) {
	$cmd='20'.date(y);
	$_ses_indicadores["cmd"] = $cmd;
	phpss_svars_set("_ses_indicadores_ins", $cmd);
}
//$cmd='20'.date(y);
$datos_barra = array(					
					/*array(
						"descripcion"	=> "Año 2007",
						"cmd"			=> "2007"
						),*/
					/*array(
						"descripcion"	=> "Año 2008",
						"cmd"			=> "2008"
						),*/
					array(
						"descripcion"	=> "Año 2009",
						"cmd"			=> "2009"
						),
					array(
						"descripcion"	=> "Año 2010",
						"cmd"			=> "2010"
						),
					array(
						"descripcion"	=> "Año 2011",
						"cmd"			=> "2011"
						),
					array(
						"descripcion"	=> "Año 2012",
						"cmd"			=> "2012"
						),
					array(
						"descripcion"	=> "Año 2013",
						"cmd"			=> "2013"
						),	
				   );
echo "<br>";

?>
<form action="indicadores_ins.php" method="post" name="form_indicadores">
<? 
generar_barra_nav($datos_barra);

//genero esta consulta para saber los indicadores que hay luego genero en base a eso
$sql1="SELECT *
	   FROM calidad.desc_indicador_ins 
	   Order By desc_indicador_ins.id_desc_indicador_ins ASC";
$result= sql($sql1) or fin_pagina();
?>

<br>
<table border=1 width="100%" align="center" cellpadding="3" cellspacing='0' bgcolor=<?=$bgcolor3?>>
<tr id="ma">
 <!--<td><font size="1"> DESEABLE </font></td>-->
 <td><font size="2"> INDICADORES </font></td>
 <td> <font size="1">ENERO </font></td>
 <td> <font size="1">FEBRERO</font></td>
 <td> <font size="1">MARZO</font></td>
 <td> <font size="1">ABRIL</font></td>
 <td> <font size="1">MAYO</font></td>
 <td> <font size="1">JUNIO </font></td>
 <td> <font size="1">JULIO </font></td>
 <td> <font size="1">AGOSTO </font></td>
 <td> <font size="1">SEPTIEMBRE </font></td>
 <td> <font size="1">OCTUBRE </font></td>
 <td> <font size="1">NOVIEMBRE </font></td>
 <td> <font size="1">DICIEMBRE </font></td>
</tr>

<?
while (!$result->EOF){ //itera hasta que se genere el ultimo indocador
$id=$result->fields['id_desc_indicador_ins'];//lo usa en la consulta de abajo

//consulta que recupera los indicadores que tenga de acuerdo al Año y al tipo de indicador
$sql="SELECT id_desc_indicador_ins, mes, anio, valor 
      FROM calidad.indicadores_ins 
	  WHERE (indicadores_ins.id_desc_indicador_ins=$id) AND (indicadores_ins.anio=$cmd)
	  Order By indicadores_ins.mes ASC";
$sat_cliente= sql($sql) or fin_pagina();
/*
*el while itera tantos indicadores existan
*dentro de while verifica el mes que pertenece el indicador y lo pone.
*luego si el mes de indicador corresponde al correcto lo imprime y avanza un lugar sino no avanza (se podria imprimir un 0)
*se asegura de avanzar 12 veces por la cantidad de meses
*/
?>
 
 <tr align="center"> <!--cargo una fila en la grilla-->
 <? 
 	if ( $result->fields['descripcion'] != ""){//imprime el indicador si viene distinto de vacio
 		?>
 		<!--<td align="left">
 			<b><?//=number_format($result->fields['valor_deseable'],2,',','.')?></b>
 		</td> 	-->
 		<td align="left"><b> 		
 		<a target="_blank" id=graf href='<?=encode_link("indicadores_grafico_int.php",array("anio"=>$cmd,"id_indi"=>$result->fields['id_desc_indicador_ins'],"tamAño"=>"large"))?>'><?=$result->fields['descripcion']?>
 		</a></b></td>
 		<? 		
 	}
 	//else echo "<td>Vacio</td>";
 
 
 	if (($sat_cliente->fields['valor']<$result->fields['valor_deseable'])&& ($sat_cliente->fields['valor']!=''))
	 	$color_indi='#FF6666';
 	else $color_indi='';
 
 ?>
 <td bgcolor=<?=$color_indi?>>
 <?
 if ($sat_cliente->fields['mes'] == 1 ) {
 			echo number_format($sat_cliente->fields['valor'],0,',','.');
		 	$sat_cliente->MoveNext();
 	}
 else echo "&nbsp;";
 ?>
 </td>
 
 <?
  if (($sat_cliente->fields['valor']<$result->fields['valor_deseable'])&& ($sat_cliente->fields['valor']!=''))
	 	$color_indi='#FF6666';
  else $color_indi='';
 ?> 
 <td bgcolor=<?=$color_indi?>>
 <?
 if ($sat_cliente->fields['mes'] == 2 ) {
 			echo number_format($sat_cliente->fields['valor'],0,',','.');
		 	$sat_cliente->MoveNext();
 	}
 else echo "&nbsp;";
 ?>
 </td>
 
 <?
  if (($sat_cliente->fields['valor']<$result->fields['valor_deseable'])&& ($sat_cliente->fields['valor']!=''))
	 	$color_indi='#FF6666';
  else $color_indi='';
 ?> 
 <td bgcolor=<?=$color_indi?>>
 <?
 if ($sat_cliente->fields['mes'] == 3 ) {
 			echo number_format($sat_cliente->fields['valor'],0,',','.');
		 	$sat_cliente->MoveNext();
 	}
 else echo "&nbsp;";
 ?>
 </td>
 
 <?
  if (($sat_cliente->fields['valor']<$result->fields['valor_deseable'])&& ($sat_cliente->fields['valor']!=''))
	 	$color_indi='#FF6666';
  else $color_indi='';
 ?> 
 <td bgcolor=<?=$color_indi?>>
 <?
 if ($sat_cliente->fields['mes'] == 4 ) {
 			echo number_format($sat_cliente->fields['valor'],0,',','.');
		 	$sat_cliente->MoveNext();
 	}
 else echo "&nbsp;";
 ?>
 </td>
 
 <?
  if (($sat_cliente->fields['valor']<$result->fields['valor_deseable'])&& ($sat_cliente->fields['valor']!=''))
	 	$color_indi='#FF6666';
  else $color_indi='';
 ?> 
 <td bgcolor=<?=$color_indi?>>
 <?
 if ($sat_cliente->fields['mes'] == 5 ) {
 			echo number_format($sat_cliente->fields['valor'],0,',','.');
		 	$sat_cliente->MoveNext();
 	}
 else echo "&nbsp;";
 ?>
 </td>
 
 <?
  if (($sat_cliente->fields['valor']<$result->fields['valor_deseable'])&& ($sat_cliente->fields['valor']!=''))
	 	$color_indi='#FF6666';
  else $color_indi='';
 ?> 
 <td bgcolor=<?=$color_indi?>>
 <?
 if ($sat_cliente->fields['mes'] == 6 ) {
 			echo number_format($sat_cliente->fields['valor'],0,',','.');
		 	$sat_cliente->MoveNext();
 	}
 else echo "&nbsp;";
 ?>
 </td>
 
 <?
  if (($sat_cliente->fields['valor']<$result->fields['valor_deseable'])&& ($sat_cliente->fields['valor']!=''))
	 	$color_indi='#FF6666';
  else $color_indi='';
 ?> 
 <td bgcolor=<?=$color_indi?>>
 <?
 if ($sat_cliente->fields['mes'] == 7 ) {
 			echo number_format($sat_cliente->fields['valor'],0,',','.');
		 	$sat_cliente->MoveNext();
 	}
 else echo "&nbsp;";
 ?>
 </td>
 
 <?
  if (($sat_cliente->fields['valor']<$result->fields['valor_deseable'])&& ($sat_cliente->fields['valor']!=''))
	 	$color_indi='#FF6666';
  else $color_indi='';
 ?> 
 <td bgcolor=<?=$color_indi?>>
 <?
 if ($sat_cliente->fields['mes'] == 8 ) {
 			echo number_format($sat_cliente->fields['valor'],0,',','.');
		 	$sat_cliente->MoveNext();
 	}
 else echo "&nbsp;";
 ?>
 </td>
 
 <?
  if (($sat_cliente->fields['valor']<$result->fields['valor_deseable'])&& ($sat_cliente->fields['valor']!=''))
	 	$color_indi='#FF6666';
  else $color_indi='';
 ?> 
 <td bgcolor=<?=$color_indi?>>
 <?
 if ($sat_cliente->fields['mes'] == 9 ) {
 			echo number_format($sat_cliente->fields['valor'],0,',','.');
		 	$sat_cliente->MoveNext();
 	}
 else echo "&nbsp;";
 ?>
 </td>
 
 <?
  if (($sat_cliente->fields['valor']<$result->fields['valor_deseable'])&& ($sat_cliente->fields['valor']!=''))
	 	$color_indi='#FF6666';
  else $color_indi='';
 ?> 
 <td bgcolor=<?=$color_indi?>>
 <?
 if ($sat_cliente->fields['mes'] == 10 ) {
 			echo number_format($sat_cliente->fields['valor'],0,',','.');
		 	$sat_cliente->MoveNext();
 	}
 else echo "&nbsp;";
 ?>
 </td>
 
 <?
  if (($sat_cliente->fields['valor']<$result->fields['valor_deseable'])&& ($sat_cliente->fields['valor']!=''))
	 	$color_indi='#FF6666';
  else $color_indi='';
 ?> 
 <td bgcolor=<?=$color_indi?>>
 <?
 if ($sat_cliente->fields['mes'] == 11 ) {
 			echo number_format($sat_cliente->fields['valor'],0,',','.');
		 	$sat_cliente->MoveNext();
 	}
 else echo "&nbsp;";
 ?>
 </td>
 
 <?
  if (($sat_cliente->fields['valor']<$result->fields['valor_deseable'])&& ($sat_cliente->fields['valor']!=''))
	 	$color_indi='#FF6666';
  else $color_indi='';
 ?> 
 <td bgcolor=<?=$color_indi?>>
 <?
 if ($sat_cliente->fields['mes'] == 12 ) {
 			echo number_format($sat_cliente->fields['valor'],0,',','.');
		 	$sat_cliente->MoveNext();
 	}
 else echo "&nbsp;";
 ?>
 </td>
 
 </tr>
<?
$result->MoveNext();//para que me imprima por el siguiente indicador
}//del while
?>
</table> <!--termina la grilla que almacena los indicadores-->
<?if ($_ses_user['login']=="sebastian")  {?>
<table width="100%" align="center" cellpadding="3" cellspacing='0'>
<tr align="center">	
	<td align="center">
		<input type="button" name="Carga Valores" value="Carga Valores" onclick="var entrar=confirm('CUIDADO!! Usted esta por ingresar a un Area de configuracion de sistema de indicadores esto solo debe hacerse con autorizacion del directorio y el gerente de calidad.'); if ( entrar ) window.open ('indicadores_guardar_ins.php');">
	</td>
</tr>
</table>
<?}?>
</table>
<br>
<!-- Genero otra tabla para contener los graficos-->
<table border=1 width="95%" align="center" cellpadding="3" cellspacing='0' bgcolor=<?=$bgcolor3?>>
<div align="center">
<?
//en result tengo todos los indicadores
$result->MoveFirst();//lo muevo al principio ya que el while anterior lo llevo a final
while (!$result->EOF){//itera hasta el ultimo indicador
	
	//realizo consuta para verificar que tenga datos para poder graficar
	$id_descripcion_indicador_ins = $result->fields['id_desc_indicador_ins'];
	$sql="SELECT id_indicadores_ins 
			FROM calidad.indicadores_ins
			where anio=$cmd and id_desc_indicador_ins = $id_descripcion_indicador_ins";
	$result_verifica = sql($sql) or fin_pagina();
	
	//verifico que tenga valor el inficador para poder generar el grafico
	if (!$result_verifica->EOF){
	//genera dos link a la pagina donde se genera el grafico (uno chico y otro grande)
	$link_s=encode_link("indicadores_grafico.php",array("anio"=>$cmd,"id_indi"=>$result->fields['id_desc_indicador_ins'],"tamaño"=>"small"));
	$link_l=encode_link("indicadores_grafico_int.php",array("anio"=>$cmd,"id_indi"=>$result->fields['id_desc_indicador_ins'],"tamaño"=>"large"));
	//ACA IMPRIME EL GRAFICO EN LA PAGINA ACTUAL REDIRECCIONA EL LINK CON AL GRAFICO CHICO Y LO IMPRIME
	echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";
	}//del if que me dice si tengo datos para graficar
	//avanzo result 
	$result->MoveNext();
}//del while
?>
</div>
</table> <!--finalizo la tabla que contiene el grafico-->
<br>
</form>

<?fin_pagina();?>