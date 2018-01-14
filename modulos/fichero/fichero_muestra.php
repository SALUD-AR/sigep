<?
require_once ("../../config.php");
//require ('funcion.php'); 

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

cargar_calendario();
$usuario1=$_ses_user['id'];

function suma_fechas($fecha,$ndias){
      if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha))
      	list($dia,$mes,$año)=split("/", $fecha);
      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))
        list($dia,$mes,$año)=split("-",$fecha);
      $nueva = mktime(0,0,0, $mes,$dia,$año) + $ndias * 24 * 60 * 60;
      $nuevafecha=date("d-m-Y",$nueva);
      return ($nuevafecha);  
}

if ($entidad_alta=='nu'){//carga de prestacion a paciente NO PLAN NACER
	$query="SELECT nacer.efe_conv.nombre as nom_efe,
				nacer.efe_conv.cuie as cuiee,
				leche.beneficiarios.apellido as afiapellido,
				leche.beneficiarios.nombre as afinombre,
				leche.beneficiarios.fecha_nac as afifechanac,
				leche.beneficiarios.documento as afidni,
				leche.beneficiarios.sexo as afisexo,
				*
				FROM
				fichero.fichero
				INNER JOIN nacer.efe_conv ON fichero.fichero.cuie = nacer.efe_conv.cuie
				INNER JOIN leche.beneficiarios ON fichero.fichero.id_beneficiarios = leche.beneficiarios.id_beneficiarios
				where fichero.fichero.id_beneficiarios='$id' and fichero.fichero.id_fichero=$id_fichero
				order by fichero.id_fichero DESC";
}elseif ($entidad_alta=='na'){//carga de prestacion a paciente PLAN NACER
			$query="SELECT nacer.efe_conv.nombre as nom_efe,
					nacer.efe_conv.cuie  as cuiee,
					nacer.smiafiliados.afiapellido,
					nacer.smiafiliados.afinombre,
					nacer.smiafiliados.afidni,
					nacer.smiafiliados.afisexo,
					nacer.smiafiliados.afifechanac,
					*
					FROM
					fichero.fichero
					INNER JOIN nacer.efe_conv ON fichero.fichero.cuie = nacer.efe_conv.cuie
					INNER JOIN nacer.smiafiliados ON nacer.smiafiliados.id_smiafiliados = fichero.fichero.id_smiafiliados
					where fichero.fichero.id_smiafiliados='$id' and fichero.fichero.id_fichero=$id_fichero
					order by fichero.id_fichero DESC";
		}

	$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
	
$afiapellido=$res_comprobante->fields["afiapellido"];		
$afinombre=$res_comprobante->fields["afinombre"];
$afidni=$res_comprobante->fields["afidni"];
$afisexo=$res_comprobante->fields["afisexo"];
$afifechanac=$res_comprobante->fields["afifechanac"];	
$id_fichero=$res_comprobante->fields["id_fichero"];		
$id_smiafiliados=$res_comprobante->fields["id_smiafiliados"];
$id_beneficiarios=$res_comprobante->fields["id_beneficiarios"];



$ex_clinico_gral=$res_comprobante->fields["ex_clinico_gral"];
$ex_trauma=$res_comprobante->fields["ex_trauma"];
$ex_cardio=$res_comprobante->fields["ex_cardio"];
$ex_odontologico=$res_comprobante->fields["ex_odontologico"];
$ex_ecg=$res_comprobante->fields["ex_ecg"];
$hemograma=$res_comprobante->fields["hemograma"];
$vsg=$res_comprobante->fields["vsg"];
$glucemia=$res_comprobante->fields["glucemia"];
$uremia=$res_comprobante->fields["uremia"];
$ca_total=$res_comprobante->fields["ca_total"];
$orina_cto=$res_comprobante->fields["orina_cto"];
$chagas=$res_comprobante->fields["chagas"];
$obs_laboratorio=$res_comprobante->fields["obs_laboratorio"];
$ergometria=$res_comprobante->fields["ergometria"];
$obs_adolesc=$res_comprobante->fields["obs_adolesc"];
$conclusion=$res_comprobante->fields["conclusion"];
$tasa_materna=$res_comprobante->fields["tasa_materna"];
$fpp=$res_comprobante->fields["fpp"];
$fum=$res_comprobante->fields["fum"];
$f_diagnostico=$res_comprobante->fields["f_diagnostico"];
$peso_embarazada=$res_comprobante->fields["peso_embarazada"];
$altura_uterina=$res_comprobante->fields["altura_uterina"];
$imc_uterina=$res_comprobante->fields["imc_uterina"];
$semana_gestacional=$res_comprobante->fields["semana_gestacional"];
$rx_torax=$res_comprobante->fields["rx_torax"];
$rx_col_vertebral=$res_comprobante->fields["rx_col_vertebral"];
$otros=$res_comprobante->fields["otros"];
$rx_observaciones=$res_comprobante->fields["rx_observaciones"];
$otros_obs=$res_comprobante->fields["otros_obs"];
$embarazo=$res_comprobante->fields["embarazo"];


	
		
echo $html_header;
?>


<form name='form1' action='fichero_muestra.php' method='POST' >
<input type="hidden" name="entidad_alta" value="<?=$entidad_alta?>">
<input type="hidden" name="id" value="<?=$id?>">

<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <?/*----------primero datos---------------*/?>
	<tr>
	   <td colspan=9>			      
	      <table width=100% align=center class=bordes>
			<tr id=ma >	
		 		<td >Efector</td>
		 		<td >Medico</td>
		 		<td >Comentario</td>
		 		<td >Fecha Prestación</td>	 		
		 		<td >Periodo</td>
			</tr>
			<tr>
		 		<td align="center" class="bordes" ><?=$res_comprobante->fields['cuiee'].' - '.$res_comprobante->fields['nom_efe']?></td>
		 		<td align="center" class="bordes" ><?if ($res_comprobante->fields['nom_medico']!="") echo $res_comprobante->fields['nom_medico']; else echo "&nbsp"?></td>
		 		<td align="center"  class="bordes"><?if ($res_comprobante->fields['comentario']!="") echo $res_comprobante->fields['comentario']; else echo "&nbsp"?></td>
		 		<td align="center"  class="bordes"><?=fecha($res_comprobante->fields['fecha_control'])?></td>		 		
		 		<td align="center"  class="bordes"><?=$res_comprobante->fields['periodo']?></td>		
			</tr> 	
	 	
			<?/*----------primero datos---------------*/?> 	
			<tr>
	          <td colspan=9>					  
	           <div id=<?=$id_tabla?> style='display:none'>
	            <table width=100% align=center class=bordes>
				  	<tr id=ma>		                               
					   <td>Peso</td>
					   <td>Talla</td>
					   <td>IMC</td>
					   <td>TA</td>
					   <td>Perc. Peso/edad</td>	  
					   <td>Perc. talla/edad</td>
					   <td>Perc. IMC/edad</td>	  
					   <td>Perc. Peso/Talla</td>	   
					   <td>Perimet.Cefarico</td>	  
					   <td>Perc.Perimet.Cefarico/edad</td>	                      
				  	</tr>
				  	<tr>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['peso']=="") echo "&nbsp"; else echo $res_comprobante->fields["peso"]?></td>			                                 
						<td align="center" class="bordes"><?if ($res_comprobante->fields['talla']=="") echo "&nbsp"; else echo $res_comprobante->fields["talla"]?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['imc']=="") echo "&nbsp"; else echo$res_comprobante->fields["imc"]?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['ta']=="") echo "&nbsp";else echo  $res_comprobante->fields["ta"]?></td>
						<td align="center" class="bordes"><?if($res_comprobante->fields['percen_peso_edad']=="1")echo "<3"; elseif ($res_comprobante->fields['percen_peso_edad']=="2")echo "3-10";  elseif ($res_comprobante->fields['percen_peso_edad']=="3")echo ">10-90 ";  elseif ($res_comprobante->fields['percen_peso_edad']=="4")echo ">90-97 ";  elseif ($res_comprobante->fields['percen_peso_edad']=="5")echo ">97";else echo"Dato Sin Ingresar";?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['percen_talla_edad']=='1') echo "-3"; elseif ($res_comprobante->fields['percen_talla_edad']=='2') echo "3-97"; elseif ($res_comprobante->fields['percen_talla_edad']=='3') echo "+97";  else echo "Dato Sin Ingresar";?></td>	
						<td align="center" class="bordes"><?if ($res_comprobante->fields['percen_imc_edad']=='1') echo "<3"; elseif ($res_comprobante->fields['percen_imc_edad']=='2') echo "3-10"; elseif ($res_comprobante->fields['percen_imc_edad']=='3') echo " >10-85"; elseif ($res_comprobante->fields['percen_imc_edad']=='4') echo ">85-97";elseif ($res_comprobante->fields['percen_imc_edad']=='5') echo " >97"; else echo "Dato Sin Ingresar";?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['percen_peso_talla']=='1') echo "<3"; elseif ($res_comprobante->fields['percen_peso_talla']=='2') echo "3-10"; elseif ($res_comprobante->fields['percen_peso_talla']=='3') echo ">10-85"; elseif ($res_comprobante->fields['percen_peso_talla']=='4') echo ">85-97"; elseif ($res_comprobante->fields['percen_peso_talla']=='5') echo " >97"; else  echo "Dato Sin Ingresar"?></td>			                                 
					    <td align="center" class="bordes"><?if ($res_comprobante->fields['perim_cefalico']=="") echo "&nbsp"; echo number_format($res_comprobante->fields["perim_cefalico"],2,',',0)?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['percen_perim_cefali_edad']=='1') echo "-3"; elseif ($res_comprobante->fields['percen_perim_cefali_edad']=='2') echo "3-97"; elseif ($res_comprobante->fields['percen_perim_cefali_edad']=='3') echo "+97"; else echo "Dato Sin Ingresar";?></td>		   
					</tr>   
			 	</table>
	          </div>
			</td>
         </tr> 
         <?/*----------segundos datos---------------*/?> 	
    	 <tr>
		  <td colspan=9>					  
	           <div id=<?=$id_tabla?> style='display:none'>
	            <table width=100% align=center class=bordes>
				<tr id=ma >	
			 		<td >Carnet de Vacunacion</td>
			 		<td >TUNNER</td>
			 		<td >Agudeza Visual</td>
			 		<td >Diabetes</td>	 		
			 		<td >Hipertencion</td>
			 		<td >Publico</td>
				</tr>
				<tr>
			 		<td align="center" class="bordes" ><?if ($res_comprobante->fields["c_vacuna"]!="") echo $res_comprobante->fields["c_vacuna"]; else echo "&nbsp"?></td>
			 		<td align="center" class="bordes" ><?if ($res_comprobante->fields['tunner']!="") echo $res_comprobante->fields['tunner']; else echo "&nbsp"?></td>
			 		<td align="center"  class="bordes"><?if ($res_comprobante->fields['ag_visual']!="") echo $res_comprobante->fields['ag_visual']; else echo "&nbsp"?></td>
			 		<td align="center"  class="bordes"><?if ($res_comprobante->fields['diabetico']!="") echo $res_comprobante->fields['diabetico']; else echo "&nbsp"?></td> 		
			 		<td align="center"  class="bordes"><?if ($res_comprobante->fields['hipertenso']!="") echo $res_comprobante->fields['hipertenso']; else echo "&nbsp"?></td>	
			 		<td align="center"  class="bordes"><?if ($res_comprobante->fields['publico']!="") echo $res_comprobante->fields['publico']; else echo "&nbsp"?></td> 		
				</tr>  
				</table>
	          </div>
			</td>
         </tr>    
         <?/*----------tercero datos---------------*/?> 	
			<tr>
	          <td colspan=9>					  
	           <div id=<?=$id_tabla?> style='display:none'>
	            <table width=100% align=center class=bordes>
				  	<tr id=ma>		                               
					   <td>Examen Clinico Gral</td>
					   <td>Examen Cardiologico</td>
					   <td>Examen Odontologico</td>                      
				  	</tr>
				  	<tr>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['ex_clinico_gral']=="") echo "&nbsp"; else echo $res_comprobante->fields["ex_clinico_gral"]?></td>			                                 
						<td align="center" class="bordes"><?if ($res_comprobante->fields['ex_cardio']=="") echo "&nbsp"; else echo $res_comprobante->fields["ex_cardio"]?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['ex_odontologico']=="") echo "&nbsp"; else echo$res_comprobante->fields["ex_odontologico"]?></td>
					</tr>   
			 	</table>
	          </div>
			</td>
         </tr> 
         <?/*----------segundos datos---------------*/?> 
          <?/*----------tercero datos---------------*/?> 	
			<tr>
	          <td colspan=9>					  
	           <div id=<?=$id_tabla?> style='display:none'>
	            <table width=100% align=center class=bordes>
				  	<tr id=ma>		                               
					   <td>Examen Traumatologico</td>
					   <td>ECG</td>
					   <td>Obs. ECG</td>                      
				  	</tr>
				  	<tr>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['ex_trauma']=="") echo "&nbsp"; else echo $res_comprobante->fields["ex_trauma"]?></td>			                                 
						<td align="center" class="bordes"><?if ($res_comprobante->fields['ex_ecg']=="") echo "&nbsp"; else echo $res_comprobante->fields["ex_ecg"]?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['obs_ecg']=="") echo "&nbsp"; else echo$res_comprobante->fields["obs_ecg"]?></td>
					</tr>   
			 	</table>
	          </div>
			</td>
         </tr> 
         <?/*----------segundos datos---------------*/?>   
         <?/*----------tercero datos---------------*/?> 	
			<tr>
	          <td colspan=9>					  
	           <div id=<?=$id_tabla?> style='display:none'>
	            <table width=100% align=center class=bordes>
				  	<tr id=ma>		                               
					   <td>RX de Torax</td>
					   <td>RX de Columna Vertebral</td>
					   <td>Obs. RX</td>                       
				  	</tr>
				  	<tr>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['rx_torax']=="") echo "&nbsp"; else echo $res_comprobante->fields["rx_torax"]?></td>			                                 
						<td align="center" class="bordes"><?if ($res_comprobante->fields['rx_col_vertebral']=="") echo "&nbsp"; else echo $res_comprobante->fields["rx_col_vertebral"]?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['rx_observaciones']=="") echo "&nbsp"; else echo$res_comprobante->fields["rx_observaciones"]?></td>
					</tr>   
					
			 	</table>
	          </div>
			</td>
         </tr> 
         <?/*----------segundos datos---------------*/?>       
         <?/*----------tercero datos---------------*/?> 	
			<tr>
	          <td colspan=9>					  
	           <div id=<?=$id_tabla?> style='display:none'>
	            <table width=100% align=center class=bordes>
				  	<tr id=ma>		                               
					   <td>Otros Estudios</td>
					   <td>Observaciones</td>                
				  	</tr>
				  	<tr>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['otros']=="") echo "&nbsp"; else echo $res_comprobante->fields["otros"]?></td>			                                 
						<td align="center" class="bordes"><?if ($res_comprobante->fields['otros_obs']=="") echo "&nbsp"; else echo $res_comprobante->fields["otros_obs"]?></td>
					</tr>   
			 	</table>
	          </div>
			</td>
         </tr> 
         <?/*----------segundos datos---------------*/?>    
          <?/*----------tercero datos---------------*/?> 	
			<tr>
	          <td colspan=9>					  
	           <div id=<?=$id_tabla?> style='display:none'>
	            <table width=100% align=center class=bordes>
				  	<tr id=ma>		                               
					   <td>Ergometria</td>
					   <td>Obs. Ergometria</td>                
				  	</tr>
				  	<tr>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['ergometria']=="") echo "&nbsp"; else echo $res_comprobante->fields["ergometria"]?></td>			                                 
						<td align="center" class="bordes"><?if ($res_comprobante->fields['obs_adolesc']=="") echo "&nbsp"; else echo $res_comprobante->fields["obs_adolesc"]?></td>
					</tr>   
			 	</table>
	          </div>
			</td>
         </tr> 
         <?/*----------segundos datos---------------*/?>       
         <?/*----------titulo---------------*/?> 	
			<tr>
	          <td colspan=9>					  
	           <div id=<?=$id_tabla?> style='display:none'>
	            <table width=100% align=center class=bordes>
				  	<tr id=ma>		                               
					   <td>Informacion de Embarazo</td>
					  </tr>   
			 	</table>
	          </div>
			</td>
         </tr> 
          <?/*----------tercero datos---------------*/?> 	
			<tr>
	          <td colspan=9>					  
	           <div id=<?=$id_tabla?> style='display:none'>
	            <table width=100% align=center class=bordes>
				  	<tr id=ma>		                               
					   <td>Fecha de Diag.</td>
					   <td>FUM</td>
					   <td>FPP</td>      
					   <td>Semana de Gest.</td>
					   <td>Peso Gest.</td>
					   <td>Altura Uterina</td>     
					   <td>IMC Uterina</td>            
				  	</tr>
				  	<tr>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['f_diagnostico']=="1000-01-01") echo "&nbsp"; else echo fecha($res_comprobante->fields["f_diagnostico"])?></td>			                                 
						<td align="center" class="bordes"><?if ($res_comprobante->fields['fum']=="1000-01-01") echo "&nbsp"; else echo fecha($res_comprobante->fields["fum"])?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['fpp']=="1000-01-01") echo "&nbsp"; else echo fecha($res_comprobante->fields["fpp"])?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['semana_gestacional']=="") echo "&nbsp"; else echo $res_comprobante->fields["semana_gestacional"]?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['peso_embarazada']=="") echo "&nbsp"; else echo $res_comprobante->fields["peso_embarazada"]?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['altura_uterina']=="") echo "&nbsp"; else echo $res_comprobante->fields["altura_uterina"]?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['imc_uterina']=="") echo "&nbsp"; else echo $res_comprobante->fields["imc_uterina"]?></td>
					</tr>   
			 	</table>
	          </div>
			</td>
         </tr> 
         <?/*----------segundos datos---------------*/?>      
          <?/*----------titulo---------------*/?> 	
			<tr>
	          <td colspan=9>					  
	           <div id=<?=$id_tabla?> style='display:none'>
	            <table width=100% align=center class=bordes>
				  	<tr id=ma>		                               
					   <td>Estudios de Laboratorios</td>
					  </tr>   
			 	</table>
	          </div>
			</td>
         </tr> 
          <?/*----------tercero datos---------------*/?> 	
			<tr>
	          <td colspan=9>					  
	           <div id=<?=$id_tabla?> style='display:none'>
	            <table width=100% align=center class=bordes>
				  	<tr id=ma>		                               
					   <td>Hemograma.</td>
					   <td>VSG</td>
					   <td>Glucemia</td>      
					   <td>Uremia</td>
					   <td>Col. Total</td>
					   <td>Orina</td>     
					   <td>Chagas</td>            
				  	</tr>
				  	<tr>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['hemograma']=="") echo "&nbsp"; else echo $res_comprobante->fields["hemograma"]?></td>			                                 
						<td align="center" class="bordes"><?if ($res_comprobante->fields['vsg']=="") echo "&nbsp"; else echo $res_comprobante->fields["vsg"]?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['glucemia']=="") echo "&nbsp"; else echo $res_comprobante->fields["glucemia"]?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['uremia']=="") echo "&nbsp"; else echo $res_comprobante->fields["uremia"]?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['ca_total']=="") echo "&nbsp"; else echo $res_comprobante->fields["ca_total"]?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['orina_cto']=="") echo "&nbsp"; else echo $res_comprobante->fields["orina_cto"]?></td>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['imc_uterina']=="") echo "&nbsp"; else echo $res_comprobante->fields["chagas"]?></td>
					</tr>   
					 <table width=100% align=center class=bordes>
				  	<tr id=ma>		                               
					   <td>Obs. de Laboratorio</td>
					</tr>
				  	<tr>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['obs_laboratorio']=="") echo "&nbsp"; else echo $res_comprobante->fields["obs_laboratorio"]?></td>			                                 
					</tr>  
			 	</table>
	          </div>
			</td>
         </tr> 
         <?/*----------segundos datos---------------*/?> 	
   <?/*----------segundos datos---------------*/?>      
          <?/*----------titulo---------------*/?> 	
			<tr>
	          <td colspan=9>					  
	           <div id=<?=$id_tabla?> style='display:none'>
	            <table width=100% align=center class=bordes>
				  	<tr id=ma>		                               
					   <td>Ficha Medica Intercolegial</td>
					  </tr>   
			 	</table>
	          </div>
			</td>
         </tr> 
          <?/*----------tercero datos---------------*/?> 	
			<tr>
	          <td colspan=9>					  
	           <div id=<?=$id_tabla?> style='display:none'>
	            <table width=100% align=center class=bordes>
				  	<tr id=ma>		                               
					   <td>Conclusion</td>              
				  	</tr>
				  	<tr>
						<td align="center" class="bordes"><?if ($res_comprobante->fields['conclusion']=="") echo "&nbsp"; else echo $res_comprobante->fields["conclusion"]?></td>			                               
					</tr>   
			 	</table>
	          </div>
			</td>
         </tr> 
         <?/*----------segundos datos---------------*/?>      
</table> 
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
    <td> 	
   	 	<input type=button name="cerrar" value="Cerrar"  onclick="window.close()" title="Cerrar Ventana" style="width=150px">
    </td>
  </tr>
 </table></td></tr>
 
</td></tr></table>
</table>

</form>
<?=fin_pagina();// aca termino ?>
