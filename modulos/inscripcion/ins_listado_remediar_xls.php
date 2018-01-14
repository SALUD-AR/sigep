<?php
/*
$Author: gaby $
$Revision: 1.0 $
$Date: 2010/10/20 15:22:40 $
*/
require_once ("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
	$mes_anio=date('Ym');
excel_header($mes_anio.".xls");
//echo $html_header;
?>
<form name=form1 method=post action="ins_listado_remediar_xls.php">
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
	    <td align=right id=mo>ID_TIPODOC</a></td>   
	   	<td align=right id=mo>NRODOC</a></td>     	
	    <td align=right id=mo>APELLIDO</a></td>   
	    <td align=right id=mo>NOMBRE</a></td>
	    <td align=right id=mo>SEXO</a></td> 
	    <td align=right id=mo>FECHA_NACIMIENTO</a></td>   
	    <td align=right id=mo>ID_PAIS</a></td> 
	    <td align=right id=mo>ID_NACIONALIDAD</a></td> 	    
	    <td align=right id=mo>INDOCUMENTADO</a></td> 
	    <td align=right id=mo>ID_PROVINCIA_DOMICILIO</a></td> 		
	    <td align=right id=mo>ID_LOCALIDAD_DOMICILIO</a></td>
	     <td align=right id=mo>CP_DOMICILIO</a></td>
	     <td align=right id=mo>CALLE</a></td>
	     <td align=right id=mo>MANZANA</a></td>
	     <td align=right id=mo>BARRIO</a></td>
	     <td align=right id=mo>CALLE_NRO</a></td>
	     <td align=right id=mo>CALLE_PISO</a></td>
	     <td align=right id=mo>CALLE_DPTO</a></td>
	     <td align=right id=mo>TIENE_TELEFONO</a></td>
	     <td align=right id=mo>ID_TIPO_TE1</a></td>
	     <td align=right id=mo>TE1</a></td>
	     <td align=right id=mo>EMAIL</a></td>
	     <td align=right id=mo>ID_EFECTOR_EMPADRON</a></td>
	     <td align=right id=mo>FECHA_EMPADRON</a></td>
	     <td align=right id=mo>ID_FACTOR_RIESGO1</a></td>
	     <td align=right id=mo>ID_FACTOR_RIESGO2</a></td>
	     <td align=right id=mo>ID_FACTOR_RIESGO3</a></td>
	     <td align=right id=mo>ID_FACTOR_RIESGO4</a></td>
	     <td align=right id=mo>ID_FACTOR_RIESGO5</a></td>
	     <td align=right id=mo>ID_FACTOR_RIESGO6</a></td>
	     <td align=right id=mo>ID_FACTOR_RIESGO7</a></td>
	     <td align=right id=mo>ID_FACTOR_RIESGO8</a></td>
	     <td align=right id=mo>ID_FACTOR_RIESGO9</a></td>
	     <td align=right id=mo>SUMATORIA</a></td>
	     <td align=right id=mo>COMENTARIO</a></td>
	     <td align=right id=mo>ID_EFECTOR_CLASIFICACION</a></td>
	     <td align=right id=mo>FECHA_CLASIFICACION</a></td>
	     <td align=right id=mo>ID_RCVG_CLASIFICACION</a></td>
	     <td align=right id=mo>ID_PROFESIONAL_CLASIF</a></td>
	     <td align=right id=mo>CLASIF_ACV</a></td> 
	     <td align=right id=mo>CLASIF_VP</a></td>
	     <td align=right id=mo>CLASIF_CI</a></td>
	     <td align=right id=mo>CLASIF_CT_310</a></td>
	     <td align=right id=mo>CLASIF_CLDL</a></td>
	     <td align=right id=mo>CLASIF_CT_HDL</a></td>
	     <td align=right id=mo>CLASIF_PAPE</a></td>
	     <td align=right id=mo>CLASIF_DMT</a></td>
	     <td align=right id=mo>CLASIF_IR</a></td>
	     <td align=right id=mo>CLASIF_39DMT</a></td>
	     <td align=right id=mo>CLASIF_39HTA</a></td>
	     <td align=right id=mo>CLASIF_DMT1</a></td>
	     <td align=right id=mo>CLASIF_DMT2</a></td>
	     <td align=right id=mo>CLASIF_HTA</a></td>
	     <td align=right id=mo>CLASIF_TAS</a></td>
	     <td align=right id=mo>CLASIF_TAD</a></td>
	     <td align=right id=mo>CLASIF_TABAQ</a></td>
	     <td align=right id=mo>CLASIF_CT</a></td>
	     <td align=right id=mo>CLASIF_MP35</a></td>
	     <td align=right id=mo>CLASIF_PTA</a></td>
	     <td align=right id=mo>CLASIF_OBES</a></td>
	     <td align=right id=mo>CLASIF_ACV_PREM</a></td>
	     <td align=right id=mo>CLASIF_TAGS</a></td>
	     <td align=right id=mo>CLASIF_HDL</a></td>
	     <td align=right id=mo>CLASIF_HIPERGLUC</a></td>
	     <td align=right id=mo>CLASIF_MICROALBUM</a></td>	
	     <td align=right id=mo>BAJO_PROGRAMA (ver siisa)</a></td>	
	     <td align=right id=mo>DIABETICO</a></td>	
	     <td align=right id=mo>HIPERTENSO</a></td>
	     <td align=right id=mo>ID_EFECTOR_SEG</a></td>
	     <td align=right id=mo>FECHA_SEGUIMIENTO</a></td>
	     <td align=right id=mo>ID_RCVG_SEGUIMIENTO</a></td>
	     <td align=right id=mo>ID_PROFESIONAL_SEGUI</a></td>
	     <td align=right id=mo>SEGUI_DMT2</a></td>
	     <td align=right id=mo>SEGUI_HTA</a></td>
	     <td align=right id=mo>SEGUI_TAS</a></td>
	     <td align=right id=mo>SEGUI_TAD</a></td> 
	     <td align=right id=mo>SEGUI_TABAQ</a></td>
	     <td align=right id=mo>SEGUI_CT</a></td>
	     <td align=right id=mo>SEGUI_GLUC</a></td>
	     <td align=right id=mo>SEGUI_PESO</a></td>
	     <td align=right id=mo>SEGUI_TALLA</a></td>
	     <td align=right id=mo>SEGUI_IMC</a></td>
	     <td align=right id=mo>SEGUI_HBA1C</a></td>
	     <td align=right id=mo>SEGUI_ECG</a></td>
	     <td align=right id=mo>SEGUI_FO</a></td>
	     <td align=right id=mo>SEGUI_EXAMEN_PIE</a></td>
	     <td align=right id=mo>SEGUI_MICROALBUM</a></td>
	     <td align=right id=mo>SEGUI_HDL</a></td> 
	     <td align=right id=mo>SEGUI_LDL</a></td>
	     <td align=right id=mo>SEGUI_TAGS</a></td>
	     <td align=right id=mo>SEGUI_CREAT</a></td>
	     <td align=right id=mo>ID_ESPECIALIDAD1</a></td>
	     <td align=right id=mo>ID_ESPECIALIDAD2</a></td>
	     <td align=right id=mo>ID_ESPECIALIDAD3</a></td>
	     <td align=right id=mo>ID_ESPECIALIDAD4</a></td>
<?//campos extras**************************	     ?>
	     <td align=right id=mo>ID_CMDB_PERSONA</a></td>
	     <td align=right id=mo>ID_CMDB_DOMICILIO</a></td>
	     <td align=right id=mo>ID_CMDB_CS</a></td>
	     <td align=right id=mo>ID_TIPODOC_MADRE</a></td>
	     <td align=right id=mo>NRODOC_MADRE</a></td>
	     <td align=right id=mo>ID_PROVINCIA</a></td>
	     <td align=right id=mo>ID_LOCALIDAD</a></td>
	     <td align=right id=mo>CUIL</a></td>
	     <td align=right id=mo>ID_CMDB_ETNIA</a></td>
	     <td align=right id=mo>ID_CMDB_GRUPO</a></td>
	     <td align=right id=mo>ID_CMDB_FACTOR</a></td>
	     <td align=right id=mo>SE_DECLARA_PUEBLO_INDIGENA</a></td>
	     <td align=right id=mo>ID_TIPO_TE2</a></td>
	     <td align=right id=mo>TE2</a></td>
	     <td align=right id=mo>ID_TIPO_TE3</a></td>
	     <td align=right id=mo>TE3</a></td>
	     <td align=right id=mo>ID_TIPO_TE4</a></td>
	     <td align=right id=mo>TE4</a></td>
	     <td align=right id=mo>ID_REDES_PADRON</a></td>
	     <td align=right id=mo>ID_REDES_CLASIFICACION</a></td>
	     <td align=right id=mo>ID_REDES_SEGUIMIENTO</a></td>
	     <td align=right id=mo>Usuario Carga (no siisa)</a></td>
	<?
//*****************************Primer consula y iteracion
$query_uno="SELECT *, remediar_x_beneficiario.usuario_carga as usuario_carga_remediar, uad.beneficiarios.localidad as loc, uad.beneficiarios.localidad_nac as loc_nac
						FROM
						uad.remediar_x_beneficiario
						LEFT JOIN uad.beneficiarios ON uad.remediar_x_beneficiario.clavebeneficiario = uad.beneficiarios.clave_beneficiario
						LEFT JOIN remediar.formulario ON uad.remediar_x_beneficiario.nroformulario = remediar.formulario.nroformulario
						LEFT JOIN nacer.efe_conv ON remediar.formulario.centro_inscriptor = nacer.efe_conv.cuie

						
						where uad.remediar_x_beneficiario.fechaempadronamiento BETWEEN  '$fechaemp' AND '$fechakrga'";
$resultuno=sql($query_uno)or fin_pagina();
?>   

 </tr>
 <? while (!$resultuno->EOF) {?>  
  <tr>
  	<td  align=right ><?if($resultuno->fields['tipo_documento']=='DNI')echo 1;elseif($resultuno->fields['tipo_documento']=='LC')echo 2;
    		elseif($resultuno->fields['tipo_documento']=='LE')echo 3;elseif($resultuno->fields['tipo_documento']=='CI') echo 4;
    		else echo 5;?></td> 
    		<?//datos de la tabla beneficiarios   
    		 
    		?>
  	<td  align=right ><?php 
  						if (strlen($resultuno->fields['numero_doc'])<=8) 
  							echo str_repeat('0',8-strlen($resultuno->fields['numero_doc'])).$resultuno->fields['numero_doc'];
  						else echo $resultuno->fields['numero_doc']
  						?>
  	</td>    
    <td  align=right ><?=$resultuno->fields['apellido_benef'];?></td>
    <td  align=right ><?=$resultuno->fields['nombre_benef']?></td>    
    <td  align=right ><?=$resultuno->fields['sexo']?></td> 
    <td  align=right ><?=fecha($resultuno->fields['fecha_nacimiento_benef'])?></td> 
    <td  align=right ><?if ($resultuno->fields['pais_nac']!='') echo $resultuno->fields['pais_nac'];
								else echo "ARGENTINA";?></td>    
    <td  align=right ><?=""?></td> 
    <td  align=right ><?=""?></td>     
    <td  align=right >19</td>
 	<td align=right><?if ($resultuno->fields['loc']!='') echo $resultuno->fields['loc'];
						else {
								if ($resultuno->fields['loc_nac']!='') echo $resultuno->fields['loc_nac'];
								else echo "SAN LUIS";
							}?></td>
	<td align=right><?=$resultuno->fields['cod_pos']?></td>
	<td align=right><?=$resultuno->fields['calle']?></td>
	<td align=right><?=$resultuno->fields['manzana']?></td>
	<td align=right><?=str_replace('B? ', '', $resultuno->fields['barrio'] );?></td>
	<td align=right><?=$resultuno->fields['numero_calle']?></td>
	<td align=right ><?=$resultuno->fields['piso']?></td>
	<td align=right ><?=$resultuno->fields['dpto']?></td>
	<td align=right ><?if($resultuno->fields['telefono']=="" || $resultuno->fields['telefono']==EOF) echo "NO"; else echo "SI"?></td>
	<td align=right ><?if($resultuno->fields['telefono']!="") echo 1; elseif($resultuno->fields['celular']!="") echo 2;?></td>
	<td align=right><?=$resultuno->fields['celular']?></td>
	<td align=right ><?=$resultuno->fields['mail']?></td>
	<?//datos de la tabla formulario    ?>
	<td align=right style="mso-number-format:'@';"><?=(string)$resultuno->fields['cod_siisa']?></td>
	<td align=right ><?=fecha($resultuno->fields['fechaempadronamiento'])?></td>
	<td align=right><?if($resultuno->fields['factores_riesgo']=="" || $resultuno->fields['factores_riesgo']==EOF || $resultuno->fields['factores_riesgo']==0) echo "";else echo $resultuno->fields['factores_riesgo'];//factores_riesgo1?></td>
	<td align=right ><?=($resultuno->fields['hta2']==1)?"5":"6";//factores_riesgo2?></td>
	<?if ($resultuno->fields['hta3']==3) $hta3_muestra="7";
		else if ($resultuno->fields['hta3']==4) $hta3_muestra="8";
		else $hta3_muestra="9";
	?>	
	<td align=right ><?=$hta3_muestra;//factores_riesgo3?></td>
	
	<td align=right ><?if($resultuno->fields['colesterol4']=="" || $resultuno->fields['colesterol4']==EOF || $resultuno->fields['colesterol4']==0) echo "";else echo($resultuno->fields['colesterol4']==1)?"10":"11";//factores_riesgo4?></td>
	<td align=right ><?if($resultuno->fields['colesterol5']=="" || $resultuno->fields['colesterol5']==EOF || $resultuno->fields['colesterol5']==0) echo "";else echo($resultuno->fields['colesterol5']==3)?"12":"13";//factores_riesgo5 ?></td>
	<td align=right><?if($resultuno->fields['dmt26']=="" || $resultuno->fields['dmt26']==EOF || $resultuno->fields['dmt26']==0) echo "";elseif ($resultuno->fields['dmt26']==1)echo 14;else echo 15;//factores_riesgo6?></td>
	<td align=right><?if($resultuno->fields['dmt27']=="" || $resultuno->fields['dmt27']==EOF || $resultuno->fields['dmt27']==0) echo "";else echo($resultuno->fields['dmt27']==3)?"16":"17";//factores_riesgo7?></td>
	
	
	<td align=right><?if($resultuno->fields['ecv8']=="" || $resultuno->fields['ecv8']==EOF || $resultuno->fields['ecv8']==0) echo "";elseif ($resultuno->fields['ecv8']==1) echo 18;//8
		else if ($resultuno->fields['ecv8']==2) echo 19;
		else echo 20;
	?></td>
	
	<td align=right><?if(trim($resultuno->fields['tabaco9'])==1)echo 21;elseif(trim($resultuno->fields['tabaco9'])==2) echo 22;else echo"";//factores_riesgo2?></td>
	<td align=right ><?=$resultuno->fields['puntaje_final'];//sumatoria?></td>
	<?//datos de la tabla trazadoras    ?>
	     <td align=right><?//COMENTARIO?></td>
	     <td align=right><?//ID_EFECTOR_CLASIFICACION?></td>
	     <td align=right><?//FECHA_CLASIFICACION?></td>
	     <td align=right><?//ID_RCVG_CLASIFICACION?></td>
	     <td align=right ><?//ID_PROFESIONAL_CLASIF?></td>
	     <td align=right ><?//CLASIF_ACV?></td> 
	     <td align=right><?//CLASIF_VP?></td>
	     <td align=right ><?//CLASIF_CI?></td>
	     <td align=right ><?//CLASIF_CT_31?></td>
	     <td align=right ><?//CLASIF_CLDL?></td>
	     <td align=right ><?//CLASIF_CT_HDL?></td>
	     <td align=right ><?//CLASIF_PAPE?></td>
	     <td align=right ><?//CLASIF_DMT?></td>
	     <td align=right ><?//CLASIF_IR?></td>
	     <td align=right ><?//CLASIF_39DMT?></td>
	     <td align=right ><?//CLASIF_39HTA?></td>
	     <td align=right ><?//CLASIF_DMT1?></td>
	     <td align=right ><?//CLASIF_DMT2?></td>
	     <td align=right ><?//CLASIF_HTA?></td>
	     <td align=right><?//CLASIF_TAS?></td>
	     <td align=right ><?//CLASIF_TAD?></td>
	     <td align=right ><?//CLASIF_TABAQ?></td>
	     <td align=right ><?//CLASIF_CT?></td>
	     <td align=right ><?//CLASIF_MP35?></td>
	     <td align=right><?//CLASIF_PTA?></td>
	     <td align=right ><?//CLASIF_OBES?></td>
	     <td align=right ><?//CLASIF_ACV_PREM?></td>
	     <td align=right ><?//CLASIF_TAGS?></td>
	     <td align=right ><?//CLASIF_HDL?></td>
	     <td align=right ><?//CLASIF_HIPERGLUC?></td>
	    <td align=right ><?//CLASIF_MICROALBUM?></td>	
	     <td align=right ><?//ID_EFECTOR_SEG?></td>
	     <td align=right ><?//FECHA_SEGUIMIENTO?></td>
	     <td align=right ><?//ID_RCVG_SEGUIMIENTO?></td>
	     <td align=right ><?//ID_PROFESIONAL_SEGUI?></td>
	     <td align=right ><?//SEGUI_DMT2?></td>
	     <td align=right ><?//SEGUI_HTA?></td>
	     <td align=right ><?//SEGUI_TAS?></td>
	     <td align=right ><?//SEGUI_TAD?></td> 
	     <td align=right ><?//SEGUI_TABAQ?></td>
	     <td align=right ><?//SEGUI_CT?></td>
	     <td align=right ><?//SEGUI_GLUC?></td>
	     <td align=right ><?//SEGUI_PESO?></td>
	     <td align=right ><?//SEGUI_TALLA?></td>
	     <td align=right ><?//SEGUI_IMC?></td>
	     <td align=right ><?//SEGUI_HBA1C?></td>
	     <td align=right ><?//SEGUI_ECG?></td>
	     <td align=right ><?//SEGUI_FO?></td>
	     <td align=right ><?//SEGUI_EXAMEN_PIE?></td>
	     <td align=right ><?//SEGUI_MICROALBUM?></td>
	     <td align=right ><?//SEGUI_HDL?></td> 
	     <td align=right ><?//SEGUI_LDL?></td>
	     <td align=right ><?//SEGUI_TAGS?></td>
	     <td align=right ><?//SEGUI_CREAT?></td>
	     <td align=right ><?//ID_ESPECIALIDAD1?></td>
	     <td align=right ><?//ID_ESPECIALIDAD2?></td>
	     <td align=right ><?//ID_ESPECIALIDAD3?></td>
	     <td align=right ><?//ID_ESPECIALIDAD4?></td>
<?//campos extras**************************	     ?>
	     <td align=right ><?//ID_CMDB_PERSONA?></td>
	     <td align=right ><?//ID_CMDB_DOMICILIO?></td>
	     <td align=right ><?//ID_CMDB_CS?></td>
	     <td align=right ><?if($resultuno->fields['tipo_doc_madre']=='DNI')echo 1;elseif($resultuno->fields['tipo_doc_madre']=='LC')echo 2;
    		elseif($resultuno->fields['tipo_doc_madre']=='LE')echo 3;elseif($resultuno->fields['tipo_doc_madre']=='CI') echo 4;
    		elseif($resultuno->fields['tipo_doc_madre']=='DE')echo 5; elseif($resultuno->fields['tipo_doc_madre']=='DNIF')echo 6;
    		elseif($resultuno->fields['tipo_doc_madre']=='DNIM')echo 7; elseif($resultuno->fields['tipo_doc_madre']=='IND')echo 9;
    		elseif($resultuno->fields['tipo_doc_madre']=='CM')echo 8; elseif($resultuno->fields['tipo_doc_madre']=='' || $resultuno->fields['tipo_documento']==EOF)echo ""?></td> 
    		<?//datos de la tabla beneficiarios    ?>
  		<td  align=right ><?if($resultuno->fields['tipo_doc_madre']!='' || $resultuno->fields['tipo_doc_madre']!=EOF)echo str_repeat('0',8-strlen($resultuno->fields['nro_doc_madre'])).$resultuno->fields['nro_doc_madre'];//NRODOC_MADRE?></td>
	     <td align=right ><?=$resultuno->fields['provincia_nac'];//ID_PROVINCIA?></td>
	     <td align=right ><?=$resultuno->fields['localidad_nac'];//ID_LOCALIDAD?></td>
	     <td align=right ><?echo "";//CUIL?></td>
	     <td align=right ><?//ID_CMDB_ETNIA?></td>
	     <td align=right ><?//ID_CMDB_GRUPO?></td>
	     <td align=right ><?//ID_CMDB_FACTOR?></td>
	     <td align=right ><?if($resultuno->fields['indigena']=='S')echo "SI";else echo"NO"//SE_DECLARA_PUEBLO_INDIGENA?></td>
	     <td align=right><?//ID_TIPO_TE2?></td>
	     <td align=right><?//TE2?></td>
	     <td align=right><?//ID_TIPO_TE3?></td>
	     <td align=right><?//TE3?></td>
	     <td align=right ><?//ID_TIPO_TE4?></td>
	     <td align=right ><?//TE4?></td>
	     <td align=right ><?//ID_REDES_PADRON?></td>
	     <td align=right ><?//ID_REDES_CLASIFICACION?></td>
	     <td align=right ><?//ID_REDES_SEGUIMIENTO?></td>   
	     <td align=right ><?//ID_REDES_SEGUIMIENTO?></td>   
	     <td align=right ><?//ID_REDES_SEGUIMIENTO?></td>   
	     <td align=right ><?//ID_REDES_SEGUIMIENTO?></td>   
		 
		 <?$id_usuario=$resultuno->fields['usuario_carga_remediar'];
		 $sql="select * from sistema.usuarios where login='$id_usuario'";
		 $res_usu=sql($sql)or fin_pagina();
		 ?>
		 <td align=right ><?=$res_usu->fields['apellido'].", ". $res_usu->fields['nombre']." (".$res_usu->fields['login'].")";?></td>

     </tr>
    <?
	$resultuno->MoveNext();
    }
    
    	//****************segunda consulta******************************//
    		$query_dos="SELECT *
						FROM
						trazadoras.clasificacion_remediar2
						LEFT JOIN uad.beneficiarios ON uad.beneficiarios.clave_beneficiario = trazadoras.clasificacion_remediar2.clave_beneficiario
						LEFT JOIN nacer.efe_conv ON nacer.efe_conv.cuie = trazadoras.clasificacion_remediar2.cuie
						WHERE
						trazadoras.clasificacion_remediar2.fecha_control BETWEEN '$fechaemp' AND '$fechakrga'";
    		$result2=sql($query_dos)or fin_pagina();
    	while (!$result2->EOF) {?>
    		  <tr>
				  	<td  align=right ><?if($result2->fields['tipo_doc']=='DNI')echo 1;elseif($result2->fields['tipo_doc']=='LC')echo 2;
				    		elseif($result2->fields['tipo_doc']=='LE')echo 3;elseif($result2->fields['tipo_doc']=='CI') echo 4;
				    		else echo 5;?></td> 
				    		<?//datos de la tabla beneficiarios    ?>
				  	<td  align=right ><?php 
				  						if (strlen($result2->fields['numero_doc'])<=8)
				  							echo str_repeat('0',8-strlen($result2->fields['numero_doc'])).$result2->fields['numero_doc'];
				  						else echo $result2->fields['numero_doc']
				  						?>
				  	</td>    
				    <td  align=right ><?//apellido?></td>
				    <td  align=right ><?//nombre?></td>    
				    <td  align=right ><?//sexo?></td> 
				    <td  align=right ><?//fecha($resultuno->fields['fecha_nacimiento_benef'])?></td> 
				    <td  align=right ><?//$resultuno->fields['pais_nac']?></td>    
				    <td  align=right ><?//=""?></td> 
				    <td  align=right ><?//=""?></td>     
				    <td  align=right ><?//=19?></td>
				 	<td align=right><?//=$resultuno->fields['localidad']?></td>
					<td align=right><?//=$resultuno->fields['cod_pos']?></td>
					<td align=right><?//=$resultuno->fields['calle'].$resultuno->fields['manzana']?></td>
					<td align=right><?//=$resultuno->fields['numero_calle']?></td>
					<td align=right ><?//=$resultuno->fields['piso']?></td>
					<td align=right ><?//=$resultuno->fields['dpto']?></td>
					<td align=right ><?//if($resultuno->fields['telefono']=="" || $resultuno->fields['telefono']==EOF) echo "NO"; else echo "SI"?></td>
					<td align=right ><?//if($resultuno->fields['telefono']!="") echo 1; elseif($resultuno->fields['celular']!="") echo 2;?></td>
					<td align=right><?//=$resultuno->fields['celular']?></td>
					<td align=right ><?//=$resultuno->fields['mail']?></td>
					<?//datos de la tabla formulario    ?>
					<td align=right ><?//=$resultuno->fields['centro_inscriptor']?></td>
					<td align=right ><?//=fecha($resultuno->fields['fecha_carga'])?></td>
					<td align=right><?//=$resultuno->fields['factores_riesgo']?></td>
					<td align=right ><?//=$resultuno->fields['hta2']?></td>
					<td align=right ><?//=$resultuno->fields['hta3']?></td>
					<td align=right ><?//=$resultuno->fields['colesterol4']?></td>
					<td align=right ><?//=$resultuno->fields['colesterol5']?></td>
					<td align=right><?//=$resultuno->fields['dmt26']?></td>
					<td align=right><?//=$resultuno->fields['dmt27']?></td>
					<td align=right><?//=$resultuno->fields['ecv8']?></td>
					<td align=right><?//=$resultuno->fields['tabaco9']?></td>
					<td align=right ><?//=$resultuno->fields['puntaje_final'];//sumatoria?></td>
					<?//datos de la tabla trazadoras    ?>
				     <td align=right><?//COMENTARIO?></td>
				     <td align=right><?//COMENTARIO?></td>
				     <td align=right><?//COMENTARIO?></td>
				     <td align=right style="mso-number-format:'@';"><?=(string)$result2->fields['cod_siisa'];//ID_EFECTOR_CLASIFICACION?></td>
				     <td align=right><?=fecha($result2->fields['fecha_control']);//FECHA_CLASIFICACION?></td>
				     <td align=right><?if($result2->fields['rcvg']=="bajo")echo 1; elseif ($result2->fields['rcvg']=="mode") echo 2; 
				     	elseif ($result2->fields['rcvg']=="alto") echo 3;elseif ($result2->fields['rcvg']=="malto") echo 4;//ID_RCVG_CLASIFICACION?></td>
				     <td align=right ><?//ID_PROFESIONAL_CLASIF?></td>
				     <td align=right ><?if($result2->fields['acv']==1)echo "SI";else echo "NO";//CLASIF_ACV?></td> 
				     <td align=right><?if($result2->fields['vas_per']==1)echo "SI";else echo "NO"; //CLASIF_VP?></td>
				     <td align=right ><?if($result2->fields['car_isq']==1)echo "SI";else echo "NO";//CLASIF_CI?></td>
				     <td align=right ><?if($result2->fields['col310']==1)echo "SI";else echo "NO";//CLASIF_CT_31?></td>
				     <td align=right ><?if($result2->fields['col_ldl']==1)echo "SI";else echo "NO";//CLASIF_CLDL?></td>
				     <td align=right ><?if($result2->fields['ct_hdl']==1)echo "SI";else echo "NO";//CLASIF_CT_HDL?></td>
				     <td align=right ><?if($result2->fields['pres_art']==1)echo "SI";else echo "NO";//CLASIF_PAPE?></td>
				     <td align=right ><?if($result2->fields['dmt2']==1)echo "SI";else echo "NO";//CLASIF_DMT?></td>
				     <td align=right ><?if($result2->fields['insu_renal']==1)echo "SI";else echo "NO";//CLASIF_IR?></td>
				     <td align=right ><?if($result2->fields['dmt_menor']==1)echo "SI";else echo "NO";//CLASIF_39DMT?></td>
				     <td align=right ><?if($result2->fields['hta_menor']==1)echo "SI";else echo "NO";//CLASIF_39HTA?></td>
				     <td align=right ><?if($result2->fields['dmt']==1)echo "SI";else echo "NO";//CLASIF_DMT1?></td>
				     <td align=right ><?if($result2->fields['dmt']==2)echo "SI";else echo "NO";//CLASIF_DMT2?></td>
				     <td align=right ><?if($result2->fields['hta']==1)echo "SI";else echo "NO";//CLASIF_HTA?></td>
				     <td align=right><?if($result2->fields['ta_sist']==0)echo "";else echo $result2->fields['ta_sist'];//CLASIF_TAS?></td>
				     <td align=right ><?if($result2->fields['ta_diast']==0)echo "";else echo $result2->fields['ta_diast'];//CLASIF_TAD?></td>
				     <td align=right ><?if($result2->fields['tabaquismo']==1)echo "SI";else echo "NO";//CLASIF_TABAQ?></td>
				     <td align=right ><?if($result2->fields['col_tot']==0)echo "";else echo $result2->fields['col_tot'];//CLASIF_CT?></td>
				     <td align=right ><?if($result2->fields['menopausia']==1)echo "SI";else echo "NO";//CLASIF_MP35 menopausia prematura?></td>
				     <td align=right><?if($result2->fields['antihiper']==1)echo "SI";else echo "NO";//CLASIF_PTA?></td>
				     <td align=right ><?if($result2->fields['obesi']==1)echo "SI";else echo "NO";//CLASIF_OBES?></td>
				     <td align=right ><?if($result2->fields['acv_prema']==1)echo "SI";else echo "NO";//CLASIF_ACV_PREM?></td>
				     <td align=right ><?if($result2->fields['trigli']==1)echo "SI";else echo "NO";//CLASIF_TAGS?></td>
				     <td align=right ><?if($result2->fields['hdl_col']==1)echo "SI";else echo "NO";//CLASIF_HDL?></td>
				     <td align=right ><?if($result2->fields['hiperglu']==1)echo "SI";else echo "NO";//CLASIF_HIPERGLUC?></td>
				     <td align=right ><?if($result2->fields['microalbu']==1)echo "SI";else echo "NO";//CLASIF_MICROALBUM?></td>
				     <td align=right ><?if($result2->fields['bajo_prog']==1)echo "SI";else echo "NO";//BAJO_PROGRAMA?></td>	
				     <td align=right ><?=$result2->fields['diabetico'];?></td>		
				     <td align=right ><?=$result2->fields['hipertenso'];?></td>	
				     <? //datos tabla seguimientos*****************************?>		     
				     <td align=right ><?//ID_EFECTOR_SEG?></td>
				     <td align=right ><?//FECHA_SEGUIMIENTO?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//ID_RCVG_SEGUIMIENTO?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//ID_PROFESIONAL_SEGUI?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_DMT2?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_HTA?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_TAS?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_TAD?></td> 
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_TABAQ?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_CT?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_GLUC?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_PESO?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_TALLA?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_IMC?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_HBA1C?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_ECG?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_FO?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_EXAMEN_PIE?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_MICROALBUM?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_HDL?></td> 
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_LDL?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_TAGS?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//SEGUI_CREAT?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//ID_ESPECIALIDAD1?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//ID_ESPECIALIDAD2?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//ID_ESPECIALIDAD3?></td>
				     <td align=right ><?//=$result2->fields['puntaje_final'];//ID_ESPECIALIDAD4?></td>
			<?//campos extras**************************	     ?>
				     <td align=right ><?//ID_CMDB_PERSONA?></td>
				     <td align=right ><?//ID_CMDB_DOMICILIO?></td>
				     <td align=right ><?//ID_CMDB_CS?></td>
				     <td align=right ><?//TOPO DOC_MADRE?></td> 
			    		<?//datos de la tabla beneficiarios    ?>
			  		<td  align=right ><?//NRODOC_MADRE?></td>
				     <td align=right ><?//ID_PROVINCIA?></td>
				     <td align=right ><?//ID_LOCALIDAD?></td>
				     <td align=right ><?//CUIL?></td>
				     <td align=right ><?//ID_CMDB_ETNIA?></td>
				     <td align=right ><?//ID_CMDB_GRUPO?></td>
				     <td align=right ><?//ID_CMDB_FACTOR?></td>
				     <td align=right ><?//SE_DECLARA_PUEBLO_INDIGENA?></td>
				     <td align=right><?//ID_TIPO_TE2?></td>
				     <td align=right><?//TE2?></td>
				     <td align=right><?//ID_TIPO_TE3?></td>
				     <td align=right><?//TE3?></td>
				     <td align=right ><?//ID_TIPO_TE4?></td>
				     <td align=right ><?//TE4?></td>
				     <td align=right ><?//ID_REDES_PADRON?></td>
				     <td align=right ><?//ID_REDES_CLASIFICACION?></td>
				     <td align=right ><?//ID_REDES_SEGUIMIENTO?></td>   
			     </tr>
    	<?	$result2->MoveNext();
    	}
    	//****************tercer consulta****************************
    	$query_tres="SELECT *
						FROM
						trazadoras.seguimiento_remediar
						LEFT JOIN uad.beneficiarios ON uad.beneficiarios.clave_beneficiario = trazadoras.seguimiento_remediar.clave_beneficiario
						LEFT JOIN nacer.efe_conv ON trazadoras.seguimiento_remediar.efector = nacer.efe_conv.cuie
						WHERE
						trazadoras.seguimiento_remediar.fecha_comprobante BETWEEN '$fechaemp' AND '$fechakrga'";
    		$result3=sql($query_tres)or fin_pagina();
    	while (!$result3->EOF) {?>
    	  <tr>
				  	<td  align=right ><?if($result3->fields['tipo_documento']=='DNI')echo 1;elseif($result3->fields['tipo_documento']=='LC')echo 2;
				    		elseif($result3->fields['tipo_documento']=='LE')echo 3;elseif($result3->fields['tipo_documento']=='CI') echo 4;
				    		else echo 5;?></td> 
				    		<?//datos de la tabla beneficiarios    ?>
				  	<td  align=right ><?php 
				  						if (strlen($result3->fields['numero_doc'])<=8)
				  							echo str_repeat('0',8-strlen($result3->fields['numero_doc'])).$result3->fields['numero_doc'];
				  						else echo $result3->fields['numero_doc']
				  						?>
				  	</td>    
				    <td  align=right ><?//apellido?></td>
				    <td  align=right ><?//nombre?></td>    
				    <td  align=right ><?//sexo?></td> 
				    <td  align=right ><?//fecha($resultuno->fields['fecha_nacimiento_benef'])?></td> 
				    <td  align=right ><?//$resultuno->fields['pais_nac']?></td>    
				    <td  align=right ><?//=""?></td> 
				    <td  align=right ><?//=""?></td>     
				    <td  align=right ><?//=19?></td>
				 	<td align=right><?//=$resultuno->fields['localidad']?></td>
					<td align=right><?//=$resultuno->fields['cod_pos']?></td>
					<td align=right><?//=$resultuno->fields['calle'].$resultuno->fields['manzana']?></td>
					<td align=right><?//=$resultuno->fields['numero_calle']?></td>
					<td align=right ><?//=$resultuno->fields['piso']?></td>
					<td align=right ><?//=$resultuno->fields['dpto']?></td>
					<td align=right ><?//if($resultuno->fields['telefono']=="" || $resultuno->fields['telefono']==EOF) echo "NO"; else echo "SI"?></td>
					<td align=right ><?//if($resultuno->fields['telefono']!="") echo 1; elseif($resultuno->fields['celular']!="") echo 2;?></td>
					<td align=right><?//=$resultuno->fields['celular']?></td>
					<td align=right ><?//=$resultuno->fields['mail']?></td>
					<?//datos de la tabla formulario    ?>
					<td align=right ><?//=$resultuno->fields['centro_inscriptor']?></td>
					<td align=right ><?//=fecha($resultuno->fields['fecha_carga'])?></td>
					<td align=right><?//=$resultuno->fields['factores_riesgo']?></td>
					<td align=right ><?//=$resultuno->fields['hta2']?></td>
					<td align=right ><?//=$resultuno->fields['hta3']?></td>
					<td align=right ><?//=$resultuno->fields['colesterol4']?></td>
					<td align=right ><?//=$resultuno->fields['colesterol5']?></td>
					<td align=right><?//=$resultuno->fields['dmt26']?></td>
					<td align=right><?//=$resultuno->fields['dmt27']?></td>
					<td align=right><?//=$resultuno->fields['ecv8']?></td>
					<td align=right><?//=$resultuno->fields['tabaco9']?></td>
					<td align=right ><?//=$resultuno->fields['puntaje_final'];//sumatoria?></td>
					<?//datos de la tabla trazadoras    ?>
				     <td align=right><?//=$resultuno->fields['puntaje_final'];//COMENTARIO?></td>
				     <td align=right><?//=$resultuno->fields['puntaje_final'];//ID_EFECTOR_CLASIFICACION?></td>
				     <td align=right><?//=$resultuno->fields['puntaje_final'];//FECHA_CLASIFICACION?></td>
				     <td align=right><?//=$resultuno->fields['puntaje_final'];//ID_RCVG_CLASIFICACION?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//ID_PROFESIONAL_CLASIF?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_ACV?></td> 
				     <td align=right><?//=$resultuno->fields['puntaje_final'];//CLASIF_VP?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_CI?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_CT_31?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_CLDL?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_CT_HDL?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_PAPE?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_DMT?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_IR?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_39DMT?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_39HTA?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_DMT1?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_DMT2?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_HTA?></td>
				     <td align=right><?//=$resultuno->fields['puntaje_final'];//CLASIF_TAS?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_TAD?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_TABAQ?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_CT?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_MP35?></td>
				     <td align=right><?//=$resultuno->fields['puntaje_final'];//CLASIF_PTA?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_OBES?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_ACV_PREM?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_TAGS?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_HDL?></td>
				     <td align=right ><?//=$resultuno->fields['puntaje_final'];//CLASIF_HIPERGLUC?></td>
				     <td align=right ><?//CLASIF_MICROALBUM?></td>	
				     <td align=right ><?//CLASIF_MICROALBUM?></td>	
				     <td align=right ><?//CLASIF_MICROALBUM?></td>	
				     <td align=right ><?//CLASIF_MICROALBUM?></td>	
				     <td align=right ><?//CLASIF_MICROALBUM?></td>	
				     <td align=right ><?//CLASIF_MICROALBUM?></td>	
				   <?//datos de la tabla seguimiento   ?>  
				     <td align=right style="mso-number-format:'@';"><?=(string)$result3->fields['cod_siisa'];//ID_EFECTOR_SEG?></td>
				     <td align=right ><?=fecha($result3->fields['fecha_comprobante']);//FECHA_SEGUIMIENTO?></td>
				     <td align=right ><?//ID_RCVG_SEGUIMIENTO?></td>
				     <td align=right ><?//ID_PROFESIONAL_SEGUI?></td>
				     <td align=right ><?if($result3->fields['dtm2']==1)echo "SI";else echo "NO";//SEGUI_DMT2?></td>
				     <td align=right ><?if($result3->fields['hta']==1)echo "SI";else echo "NO";//SEGUI_HTA?></td>
				     <td align=right ><?=$result3->fields['ta_sist'];//SEGUI_TAS?></td>
				     <td align=right ><?=$result3->fields['ta_diast'];//SEGUI_TAD?></td> 
				     <td align=right ><?if($result3->fields['tabaquismo']==1)echo "SI";else echo "NO";//SEGUI_TABAQ?></td>
				     <td align=right ><?=$result3->fields['col_tot'];//SEGUI_CT?></td>
				     <td align=right ><?=$result3->fields['gluc'];//SEGUI_GLUC?></td>
				     <td align=right ><?=$result3->fields['peso'];//SEGUI_PESO?></td>
				     <td align=right ><?=$result3->fields['talla'];//SEGUI_TALLA?></td>
				     <td align=right ><?=$result3->fields['imc'];//SEGUI_IMC?></td>
				     <td align=right ><?=$result3->fields['hba1c'];//SEGUI_HBA1C?></td>
				     <td align=right ><?=$result3->fields['ecg'];//SEGUI_ECG?></td>
				     <td align=right ><?=$result3->fields['fondodeojo'];//SEGUI_FO?></td>
				     <td align=right ><?=$result3->fields['examendepie'];//SEGUI_EXAMEN_PIE?></td>
				     <td align=right ><?=$result3->fields['microalbuminuria'];//SEGUI_MICROALBUM?></td>
				     <td align=right ><?=$result3->fields['hdl'];//SEGUI_HDL?></td> 
				     <td align=right ><?=$result3->fields['ldl'];//SEGUI_LDL?></td>
				     <td align=right ><?=$result3->fields['tags'];//SEGUI_TAGS?></td>
				     <td align=right ><?=$result3->fields['creatininemia'];//SEGUI_CREAT?></td>
				     <td align=right ><?=$result3->fields['esp1'];//ID_ESPECIALIDAD1?></td>
				     <td align=right ><?=$result3->fields['esp2'];//ID_ESPECIALIDAD2?></td>
				     <td align=right ><?=$result3->fields['esp3'];//ID_ESPECIALIDAD3?></td>
				     <td align=right ><?=$result3->fields['esp4'];//ID_ESPECIALIDAD4?></td>
				 <?//campos extras**************************	     ?>
				     <td align=right ><?//ID_CMDB_PERSONA?></td>
				     <td align=right ><?//ID_CMDB_DOMICILIO?></td>
				     <td align=right ><?//ID_CMDB_CS?></td>
				     <td align=right ><?//TOPO DOC_MADRE?></td> 
			    <?//datos de la tabla beneficiarios    ?>
			  	     <td  align=right ><?//NRODOC_MADRE?></td>
				     <td align=right ><?//ID_PROVINCIA?></td>
				     <td align=right ><?//ID_LOCALIDAD?></td>
				     <td align=right ><?//CUIL?></td>
				     <td align=right ><?//ID_CMDB_ETNIA?></td>
				     <td align=right ><?//ID_CMDB_GRUPO?></td>
				     <td align=right ><?//ID_CMDB_FACTOR?></td>
				     <td align=right ><?//SE_DECLARA_PUEBLO_INDIGENA?></td>
				     <td align=right><?//ID_TIPO_TE2?></td>
				     <td align=right><?//TE2?></td>
				     <td align=right><?//ID_TIPO_TE3?></td>
				     <td align=right><?//TE3?></td>
				     <td align=right ><?//ID_TIPO_TE4?></td>
				     <td align=right ><?//TE4?></td>
				     <td align=right ><?//ID_REDES_PADRON?></td>
				     <td align=right ><?//ID_REDES_CLASIFICACION?></td>
				     <td align=right ><?//ID_REDES_SEGUIMIENTO?></td>  
				    </tr>
    		
    	<?	$result3->MoveNext();
    	}
    			
    ?> 
    </table>
 </form>
