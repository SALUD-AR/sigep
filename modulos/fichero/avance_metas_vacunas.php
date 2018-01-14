<?php
/*
$Author: gaby $
$Revision: 1.0 $
$Date: 2012/10/20 15:22:40 $
*/
require_once("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['guardar_meta']=="Guardar Meta Anual"){
			$sql_tmp="SELECT meta
				FROM
					fichero.meta_vacuna
				where (ano='$anio')and (cuie='$cuie') and (id_vac_apli='$id_vac_apli') and (id_dosis_apli='$id_dosis_apli')";
			$res_meta=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
			if ($res_meta->recordcount()==0){
				$sql_tmp="insert into
					fichero.meta_vacuna
					(ano,cuie,id_dosis_apli,id_vac_apli,meta) values
					('$anio','$cuie','$id_dosis_apli','$id_vac_apli','$meta')";
				sql($sql_tmp,"<br>Error al insetar<br>") or fin_pagina();
			}
			else{
				$sql_tmp="update
					fichero.meta_vacuna SET
					meta=$meta
					where (ano='$anio')and (cuie='$cuie') and (id_vac_apli='$id_vac_apli') and (id_dosis_apli='$id_dosis_apli')";
				sql($sql_tmp,"<br>Error al insetar<br>") or fin_pagina();
			}
}


if ($_POST['muestra']=="Muestra"){
			$fecha_desde=$anio."-01-01";
			$fecha_hasta=$anio."-01-31";
			$sql_tmp="SELECT  
				count(trazadoras.vacunas.id_vacunas)as cantidad
				FROM
					trazadoras.vacunas
				where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta')and (cuie='$cuie') 
				and (trazadoras.vacunas.eliminada=0) and (id_vac_apli='$id_vac_apli') and (id_dosis_apli='$id_dosis_apli')";
			$mes_1=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
			
			$fecha_desde=$anio."-02-01";
			$fecha_hasta=$anio."-02-28";
			$sql_tmp="SELECT  
				count(trazadoras.vacunas.id_vacunas)as cantidad
				FROM
					trazadoras.vacunas
				where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta')and (cuie='$cuie') 
				and (trazadoras.vacunas.eliminada=0) and (id_vac_apli='$id_vac_apli') and (id_dosis_apli='$id_dosis_apli')";
			$mes_2=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
			
			$fecha_desde=$anio."-03-01";
			$fecha_hasta=$anio."-03-31";
			$sql_tmp="SELECT  
				count(trazadoras.vacunas.id_vacunas)as cantidad
				FROM
					trazadoras.vacunas
				where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta')and (cuie='$cuie') 
				and (trazadoras.vacunas.eliminada=0) and (id_vac_apli='$id_vac_apli') and (id_dosis_apli='$id_dosis_apli')";
			$mes_3=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
			
			$fecha_desde=$anio."-04-01";
			$fecha_hasta=$anio."-04-30";
			$sql_tmp="SELECT  
				count(trazadoras.vacunas.id_vacunas)as cantidad
				FROM
					trazadoras.vacunas
				where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta')and (cuie='$cuie') 
				and (trazadoras.vacunas.eliminada=0) and (id_vac_apli='$id_vac_apli') and (id_dosis_apli='$id_dosis_apli')";
			$mes_4=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
			
			$fecha_desde=$anio."-05-01";
			$fecha_hasta=$anio."-05-31";
			$sql_tmp="SELECT  
				count(trazadoras.vacunas.id_vacunas)as cantidad
				FROM
					trazadoras.vacunas
				where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta')and (cuie='$cuie') 
				and (trazadoras.vacunas.eliminada=0) and (id_vac_apli='$id_vac_apli') and (id_dosis_apli='$id_dosis_apli')";
			$mes_5=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
			
			$fecha_desde=$anio."-06-01";
			$fecha_hasta=$anio."-06-30";
			$sql_tmp="SELECT  
				count(trazadoras.vacunas.id_vacunas)as cantidad
				FROM
					trazadoras.vacunas
				where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta')and (cuie='$cuie') 
				and (trazadoras.vacunas.eliminada=0) and (id_vac_apli='$id_vac_apli') and (id_dosis_apli='$id_dosis_apli')";
			$mes_6=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
			
			$fecha_desde=$anio."-07-01";
			$fecha_hasta=$anio."-07-31";
			$sql_tmp="SELECT  
				count(trazadoras.vacunas.id_vacunas)as cantidad
				FROM
					trazadoras.vacunas
				where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta')and (cuie='$cuie') 
				and (trazadoras.vacunas.eliminada=0) and (id_vac_apli='$id_vac_apli') and (id_dosis_apli='$id_dosis_apli')";
			$mes_7=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
			
			$fecha_desde=$anio."-08-01";
			$fecha_hasta=$anio."-08-31";
			$sql_tmp="SELECT  
				count(trazadoras.vacunas.id_vacunas)as cantidad
				FROM
					trazadoras.vacunas
				where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta')and (cuie='$cuie') 
				and (trazadoras.vacunas.eliminada=0) and (id_vac_apli='$id_vac_apli') and (id_dosis_apli='$id_dosis_apli')";
			$mes_8=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
			
			$fecha_desde=$anio."-09-01";
			$fecha_hasta=$anio."-09-30";
			$sql_tmp="SELECT  
				count(trazadoras.vacunas.id_vacunas)as cantidad
				FROM
					trazadoras.vacunas
				where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta')and (cuie='$cuie') 
				and (trazadoras.vacunas.eliminada=0) and (id_vac_apli='$id_vac_apli') and (id_dosis_apli='$id_dosis_apli')";
			$mes_9=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
			
			$fecha_desde=$anio."-10-01";
			$fecha_hasta=$anio."-10-31";
			$sql_tmp="SELECT  
				count(trazadoras.vacunas.id_vacunas)as cantidad
				FROM
					trazadoras.vacunas
				where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta')and (cuie='$cuie') 
				and (trazadoras.vacunas.eliminada=0) and (id_vac_apli='$id_vac_apli') and (id_dosis_apli='$id_dosis_apli')";
			$mes_10=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
			
			$fecha_desde=$anio."-11-01";
			$fecha_hasta=$anio."-11-30";
			$sql_tmp="SELECT  
				count(trazadoras.vacunas.id_vacunas)as cantidad
				FROM
					trazadoras.vacunas
				where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta')and (cuie='$cuie') 
				and (trazadoras.vacunas.eliminada=0) and (id_vac_apli='$id_vac_apli') and (id_dosis_apli='$id_dosis_apli')";
			$mes_11=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
			
			$fecha_desde=$anio."-12-01";
			$fecha_hasta=$anio."-12-31";
			$sql_tmp="SELECT  
				count(trazadoras.vacunas.id_vacunas)as cantidad
				FROM
					trazadoras.vacunas
				where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta')and (cuie='$cuie') 
				and (trazadoras.vacunas.eliminada=0) and (id_vac_apli='$id_vac_apli') and (id_dosis_apli='$id_dosis_apli')";
			$mes_12=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
}
	
echo $html_header;?>

<script>
function control_muestra()
{ 
return true;
}

function control_meta(){ 
	if(document.all.meta.value==""){
		alert('Debe ingresar una meta Numerica');
		document.all.meta.focus();
		return false;
	}
return true;
}
</script>
<form name=form1 action="avance_metas_vacunas.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
	    	  <b>
	    	  Año: 
			  <select name=anio Style="width=100px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();">
			  <option value='2012'>2012</option>
			  <option value='2013'>2013</option>
			  <option value='2014'>2014</option>
			  </select>
				
				
			  Efector: 
			 <select name=cuie Style="width=257px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();">
			 <?$user_login1=substr($_ses_user['login'],0,6);
								  if (es_cuie($_ses_user['login'])){
									$sql1= "select cuie, nombre, com_gestion from nacer.efe_conv where cuie='$user_login1' order by nombre";
								   }									
								  else{
									$usuario1=$_ses_user['id'];
									$sql1= "select nacer.efe_conv.nombre, nacer.efe_conv.cuie, com_gestion 
											from nacer.efe_conv 
											join sistema.usu_efec on (nacer.efe_conv.cuie = sistema.usu_efec.cuie) 
											join sistema.usuarios on (sistema.usu_efec.id_usuario = sistema.usuarios.id_usuario) 
											where sistema.usuarios.id_usuario = '$usuario1'
										 order by nombre";
								   }			 			   
								 $res_efectores=sql($sql1) or fin_pagina();
							 
							 while (!$res_efectores->EOF){ 
								$com_gestion=$res_efectores->fields['com_gestion'];
								$cuie1=$res_efectores->fields['cuie'];
								$nombre_efector=$res_efectores->fields['nombre'];
								if($com_gestion=='FALSO')$color_style='#F78181'; else $color_style='';
								?>
								<option value='<?=$cuie1;?>' Style="background-color: <?=$color_style;?>" <?if ($cuie1==$cuie)echo "selected"?>><?=$cuie1." - ".$nombre_efector?></option>
								<?
								$res_efectores->movenext();
								}?>
			</select>
			
			Vacuna Aplicada:
			 <select name=id_vac_apli Style="width=180px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				>
			 <?
			 $sql= "select * from trazadoras.vac_apli order by nombre";
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$cuiel=$res_efectores->fields['id_vac_apli'];
			    $nombre_efector=$res_efectores->fields['nombre'];			    
			    ?>
				<option value='<?=$cuiel?>' ><?=$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>
			
			Dosis Aplicada:
			 <select name=id_dosis_apli Style="width=50px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				>
			 <?
			 $sql= "select * from trazadoras.dosis_apli order by nombre";
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$cuiel=$res_efectores->fields['id_dosis_apli'];
			    $nombre_efector=$res_efectores->fields['nombre'];			    
			    ?>
				<option value='<?=$cuiel?>' ><?=$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>			
			<input type="submit" name="muestra" value='Muestra' onclick="return control_muestra()" >
			<br> 
			<?if ($_POST['cargar_meta']=="Cargar Meta Anual"){
				$sql_tmp="SELECT meta
					FROM
						fichero.meta_vacuna
					where (ano='$anio')and (cuie='$cuie') and (id_vac_apli='$id_vac_apli') and (id_dosis_apli='$id_dosis_apli')";
				$res_meta=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
				$meta=$res_meta->fields['meta']?>
				Meta Anua: <input type="text" size=6 value="<?=$meta?>" name="meta">
				<input type="submit" name="guardar_meta" value='Guardar Meta Anual' onclick="return control_meta()" >
			<?}else{?>			
				<input type="submit" name="cargar_meta" value='Cargar Meta Anual'>	
			<?}?>
		</b>     
	  </td>
     </tr>
</table>

<?if ($_POST['muestra']){?>

<table border=0 width=90% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
   <tr>
  	<td align=right id=mo>&nbsp</td>
  	<td align=right id=mo>ENERO</td>
    <td align=right id=mo>FEBRERO</td>
    <td align=right id=mo>MARZO</td>
    <td align=right id=mo>ABRIL</td>
    <td align=right id=mo>MAYO</td>
    <td align=right id=mo>JUNIO</td>
    <td align=right id=mo>JULIO</td>
    <td align=right id=mo>AGOSTO</td>
    <td align=right id=mo>SEPTIEMBRE</td>
    <td align=right id=mo>OCTUBRE</td>
    <td align=right id=mo>NOVIEMBRE</td>
    <td align=right id=mo>DICIEMBRE</td>
  </tr>
  <tr <?=atrib_tr()?> >  
	<td>TOTAL MENSUAL</td>  
    <td><?=$mes_1->fields['cantidad']?></td>     
    <td><?=$mes_2->fields['cantidad']?></td>     
    <td><?=$mes_3->fields['cantidad']?></td>     
    <td><?=$mes_4->fields['cantidad']?></td>     
    <td><?=$mes_5->fields['cantidad']?></td>     
    <td><?=$mes_6->fields['cantidad']?></td>     
    <td><?=$mes_7->fields['cantidad']?></td>     
    <td><?=$mes_8->fields['cantidad']?></td>     
    <td><?=$mes_9->fields['cantidad']?></td>     
    <td><?=$mes_10->fields['cantidad']?></td>     
    <td><?=$mes_11->fields['cantidad']?></td>     
    <td><?=$mes_12->fields['cantidad']?></td>         
  </tr>
  
  <tr <?=atrib_tr()?> >  
	<td>TOTAL ACUMULADO</td>  
    <td><?$m1=$mes_1->fields['cantidad']; 
	echo $m1?></td>     
    <td><?$m2=$mes_1->fields['cantidad']+$mes_2->fields['cantidad'];
	echo $m2?></td>     
    <td><?$m3=$mes_1->fields['cantidad']+$mes_2->fields['cantidad']+$mes_3->fields['cantidad'];
	echo $m3?></td>     
    <td><?$m4=$mes_1->fields['cantidad']+$mes_2->fields['cantidad']+$mes_3->fields['cantidad']+$mes_4->fields['cantidad'];
	 echo $m4?></td>     
    <td><?$m5=$mes_1->fields['cantidad']+$mes_2->fields['cantidad']+$mes_3->fields['cantidad']+$mes_4->fields['cantidad']+$mes_5->fields['cantidad'];
	 echo $m5?></td>     
    <td><?$m6=$mes_1->fields['cantidad']+$mes_2->fields['cantidad']+$mes_3->fields['cantidad']+$mes_4->fields['cantidad']+$mes_5->fields['cantidad']+$mes_6->fields['cantidad'];
	 echo $m6?></td>     
    <td><?$m7=$mes_1->fields['cantidad']+$mes_2->fields['cantidad']+$mes_3->fields['cantidad']+$mes_4->fields['cantidad']+$mes_5->fields['cantidad']+$mes_6->fields['cantidad']+$mes_7->fields['cantidad'];
	 echo $m7?></td>     
    <td><?$m8=$mes_1->fields['cantidad']+$mes_2->fields['cantidad']+$mes_3->fields['cantidad']+$mes_4->fields['cantidad']+$mes_5->fields['cantidad']+$mes_6->fields['cantidad']+$mes_7->fields['cantidad']+$mes_8->fields['cantidad'];
	 echo $m8?></td>     
    <td><?$m9=$mes_1->fields['cantidad']+$mes_2->fields['cantidad']+$mes_3->fields['cantidad']+$mes_4->fields['cantidad']+$mes_5->fields['cantidad']+$mes_6->fields['cantidad']+$mes_7->fields['cantidad']+$mes_8->fields['cantidad']+$mes_9->fields['cantidad'];
	 echo $m9?></td>     
    <td><?$m10=$mes_1->fields['cantidad']+$mes_2->fields['cantidad']+$mes_3->fields['cantidad']+$mes_4->fields['cantidad']+$mes_5->fields['cantidad']+$mes_6->fields['cantidad']+$mes_7->fields['cantidad']+$mes_8->fields['cantidad']+$mes_9->fields['cantidad']+$mes_10->fields['cantidad'];
	 echo $m10?></td>     
    <td><?$m11=$mes_1->fields['cantidad']+$mes_2->fields['cantidad']+$mes_3->fields['cantidad']+$mes_4->fields['cantidad']+$mes_5->fields['cantidad']+$mes_6->fields['cantidad']+$mes_7->fields['cantidad']+$mes_8->fields['cantidad']+$mes_9->fields['cantidad']+$mes_10->fields['cantidad']+$mes_11->fields['cantidad'];
	 echo $m11?></td>     
    <td><?$m12=$mes_1->fields['cantidad']+$mes_2->fields['cantidad']+$mes_3->fields['cantidad']+$mes_4->fields['cantidad']+$mes_5->fields['cantidad']+$mes_6->fields['cantidad']+$mes_7->fields['cantidad']+$mes_8->fields['cantidad']+$mes_9->fields['cantidad']+$mes_10->fields['cantidad']+$mes_11->fields['cantidad']+$mes_12->fields['cantidad'];
	 echo $m12?></td>        
  </tr>
  <?	    $periodos_array_ant[0]=$m1;
		    $periodos_array_ant[1]=$m2;
		    $periodos_array_ant[2]=$m3;
		    $periodos_array_ant[3]=$m4;
		    $periodos_array_ant[4]=$m5;
		    $periodos_array_ant[5]=$m6;
		    $periodos_array_ant[6]=$m7;
		    $periodos_array_ant[7]=$m8;
		    $periodos_array_ant[8]=$m9;
		    $periodos_array_ant[9]=$m10;
		    $periodos_array_ant[10]=$m11;
		    $periodos_array_ant[11]=$m12;
			
			$periodos_array[0]=$mes_1->fields['cantidad'];
			$periodos_array[1]=$mes_2->fields['cantidad'];
			$periodos_array[2]=$mes_3->fields['cantidad'];
			$periodos_array[3]=$mes_4->fields['cantidad'];
			$periodos_array[4]=$mes_5->fields['cantidad'];
			$periodos_array[5]=$mes_6->fields['cantidad'];
			$periodos_array[6]=$mes_7->fields['cantidad'];
			$periodos_array[7]=$mes_8->fields['cantidad'];
			$periodos_array[8]=$mes_9->fields['cantidad'];
			$periodos_array[9]=$mes_10->fields['cantidad'];
			$periodos_array[10]=$mes_11->fields['cantidad'];
			$periodos_array[11]=$mes_12->fields['cantidad'];
			
			$periodos_label_array[0]="ENERO";
			$periodos_label_array[1]="FEBRERO";
			$periodos_label_array[2]="MARZO";
			$periodos_label_array[3]="ABRIL";
			$periodos_label_array[4]="MAYO";
			$periodos_label_array[5]="JUNIO";
			$periodos_label_array[6]="JULIO";
			$periodos_label_array[7]="AGOSTO";
			$periodos_label_array[8]="SEPTIEMBRE";
			$periodos_label_array[9]="OCTUBRE";
			$periodos_label_array[10]="NOVIEMBRE";
			$periodos_label_array[11]="DICIEMBRE";
			
			$sql_tmp="SELECT meta
				FROM
					fichero.meta_vacuna
				where (ano='$anio')and (cuie='$cuie') and (id_vac_apli='$id_vac_apli') and (id_dosis_apli='$id_dosis_apli')";
			$res_meta=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
			if ($res_meta->recordcount()>0){
				$meta=$res_meta->fields['meta'];
				$meta_mensual=$meta/12;
				for($i=0;$i<12;$i++){
					$meta_acu[$i]=$meta_acu[$i-1]+$meta_mensual;
					$meta_men[$i]=$meta_mensual;
					$meta_porcent[$i]=($periodos_array_ant[$i]*100)/$meta;
				}
			}
	?>
	<tr <?=atrib_tr()?> >  
		<td>META MENSUAL</td>  
		<?for($i=0;$i<12;$i++){
			echo "<td>".number_format($meta_men[$i],0,',','')."</td>";
		}?>         
	</tr>
	<tr <?=atrib_tr()?> >  
		<td>META ACUMULADA</td>  
		<?for($i=0;$i<12;$i++){
			echo "<td>".number_format($meta_acu[$i],0,',','')."</td>";
		}?>         
	</tr>
	
	<tr <?=atrib_tr()?> >  
		<td>%ACUMULADO</td>  
		<?for($i=0;$i<12;$i++){
			echo "<td>".number_format($meta_porcent[$i],2,',','')."</td>";
		}?>         
	</tr>	
</table>
<br>
<table border=1 width="90%" align="center" cellpadding="3" cellspacing='0' bgcolor=<?=$bgcolor3?>>
<div align="center">
	<tr>
    	<td align="center" colspan="2" class=bordes id="ma">
			<?$link_s=encode_link("avance_metas_grp_small_1.php",array("periodos_array_ant"=>$periodos_array_ant,"periodos_label_array"=>$periodos_label_array,"periodos_array"=>$periodos_array,"meta_men"=>$meta_men,"meta_acu"=>$meta_acu,"meta_porcent"=>$meta_porcent));
			$link_l=encode_link("avance_metas_grp_large_1.php",array("periodos_array_ant"=>$periodos_array_ant,"periodos_label_array"=>$periodos_label_array,"periodos_array"=>$periodos_array));
			//ACA IMPRIME EL GRAFICO EN LA PAGINA ACTUAL REDIRECCIONA EL LINK CON AL GRAFICO CHICO Y LO IMPRIME
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?>
		</td>    	
    </tr>
</div>
</table> <!--finalizo la tabla que contiene el grafico-->	
<?}?>	
</td>
</table>

</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
