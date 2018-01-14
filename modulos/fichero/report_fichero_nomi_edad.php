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

$rangoedad_desde=substr($rangoedad,0,2);
$rangoedad_hasta=$rangoedad_desde+1;

if ($_POST['generar_excel']=="Generar Excel"){
	$periodo=$_POST['periodo'];
	$link=encode_link("report_fichero_nomi_edad_excel.php",array("rangoedad_desde"=>$rangoedad_desde,"rangoedad_hasta"=>$rangoedad_hasta,"fecha_desde"=>$_POST['fecha_desde'],"fecha_hasta"=>$_POST['fecha_hasta'],"cuie"=>$_POST['cuie']));
	?>
	<script>
	window.open('<?=$link?>')
	</script>	
<?}
if ($_POST['muestra']=="Muestra"){
	$cuie=$_POST['cuie'];
	$fecha_desde=Fecha_db($_POST['fecha_desde']);
	$fecha_hasta=Fecha_db($_POST['fecha_hasta']);
		if($cuie!='Todos'){
			$sql_tmp="SELECT							
							nacer.smiafiliados.*,((fichero.fichero.fecha_control - nacer.smiafiliados.afifechanac)/365) as edad_smiafil,
							leche.beneficiarios.*,((fichero.fichero.fecha_control - leche.beneficiarios.fecha_nac)/365) as edad_benef,
							fichero.fichero.*,
							nacer.efe_conv.nombre as nom_efe
							FROM
							fichero.fichero 
							LEFT OUTER JOIN nacer.smiafiliados ON fichero.fichero.id_smiafiliados = nacer.smiafiliados.id_smiafiliados
							LEFT OUTER JOIN leche.beneficiarios ON leche.beneficiarios.id_beneficiarios = fichero.fichero.id_beneficiarios
							INNER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = fichero.fichero.cuie
						where 
							CASE WHEN leche.beneficiarios.fecha_nac IS NULL THEN
								(fichero.fichero.fecha_control BETWEEN '$fecha_desde' and '$fecha_hasta') and (nacer.efe_conv.cuie='$cuie')
								and ((fichero.fecha_control - nacer.smiafiliados.afifechanac)/365)>=$rangoedad_desde and ((fichero.fecha_control - nacer.smiafiliados.afifechanac)/365)<$rangoedad_hasta
							ELSE
								(fichero.fichero.fecha_control BETWEEN '$fecha_desde' and '$fecha_hasta') and (nacer.efe_conv.cuie='$cuie')
								and ((fichero.fecha_control - leche.beneficiarios.fecha_nac)/365)>=$rangoedad_desde and ((fichero.fecha_control - leche.beneficiarios.fecha_nac)/365)<$rangoedad_hasta
							END
						ORDER BY fecha_control";
}else {
				$sql_tmp="SELECT							
							nacer.smiafiliados.*,((fichero.fichero.fecha_control - nacer.smiafiliados.afifechanac)/365) as edad_smiafil,
							leche.beneficiarios.*,((fichero.fichero.fecha_control - leche.beneficiarios.fecha_nac)/365) as edad_benef,
							fichero.fichero.*,
							nacer.efe_conv.nombre as nom_efe
							FROM
							fichero.fichero 
							LEFT OUTER JOIN nacer.smiafiliados ON fichero.fichero.id_smiafiliados = nacer.smiafiliados.id_smiafiliados
							LEFT OUTER JOIN leche.beneficiarios ON leche.beneficiarios.id_beneficiarios = fichero.fichero.id_beneficiarios
							INNER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = fichero.fichero.cuie
						where 
							CASE WHEN leche.beneficiarios.fecha_nac IS NULL THEN
								(fichero.fichero.fecha_control BETWEEN '$fecha_desde' and '$fecha_hasta') 
								and ((fichero.fecha_control  - nacer.smiafiliados.afifechanac)/365)>=$rangoedad_desde and ((fichero.fecha_control  - nacer.smiafiliados.afifechanac)/365)<$rangoedad_hasta
							ELSE
								(fichero.fichero.fecha_control BETWEEN '$fecha_desde' and '$fecha_hasta') 
								and ((fichero.fecha_control  - leche.beneficiarios.fecha_nac)/365)>=$rangoedad_desde and ((fichero.fecha_control  - leche.beneficiarios.fecha_nac)/365)<$rangoedad_hasta
							END
						ORDER BY fecha_control";
							
			}
			
			$res_comprobante=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();


}
echo $html_header;?>

<script>
function control_muestra()
{ 
 if(document.all.fecha_desde.value==""){
  alert('Debe Ingresar una Fecha DESDE');
  return false;
 } 
 if(document.all.fecha_hasta.value==""){
  alert('Debe Ingresar una Fecha HASTA');
  return false;
 } 
return true;
}
</script>
<form name=form1 action="report_fichero_nomi_edad.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<b><?if ($fecha_desde=='')$fecha_desde=date("Y-m-d",time()-(30*24*60*60));
			  if ($fecha_hasta=='')$fecha_hasta=date("Y-m-d");?>
			  Desde: <input type=text id=fecha_desde name=fecha_desde value='<?=fecha($fecha_desde)?>' size=15 readonly>
				<?=link_calendario("fecha_desde");?>
		
			  Hasta: <input type=text id=fecha_hasta name=fecha_hasta value='<?=fecha($fecha_hasta)?>' size=15 readonly>
				<?=link_calendario("fecha_hasta");?> 
				
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
			  <option value='Todos' <?if ("Todos"==$cuie)echo "selected"?>>Todos</option>
			</select>
			
			Edad:
			<select name=rangoedad Style="width=57px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();">
			<option value='00-01' <?if ("00-01"==$rangoedad)echo "selected"?>>00-01</option>
			<option value='01-02' <?if ("01-02"==$rangoedad)echo "selected"?>>01-02</option>
			<option value='02-03' <?if ("02-03"==$rangoedad)echo "selected"?>>02-03</option>
			<option value='03-04' <?if ("03-04"==$rangoedad)echo "selected"?>>03-04</option>
			<option value='04-05' <?if ("04-05"==$rangoedad)echo "selected"?>>04-05</option>
			<option value='05-06' <?if ("05-06"==$rangoedad)echo "selected"?>>05-06</option>
			<option value='06-07' <?if ("06-07"==$rangoedad)echo "selected"?>>06-07</option>
			<option value='07-08' <?if ("07-08"==$rangoedad)echo "selected"?>>07-08</option>
			<option value='08-09' <?if ("08-09"==$rangoedad)echo "selected"?>>08-09</option>
			<option value='09-10' <?if ("09-10"==$rangoedad)echo "selected"?>>09-10</option>
			<option value='10-11' <?if ("10-11"==$rangoedad)echo "selected"?>>10-11</option>
			<option value='11-12' <?if ("11-12"==$rangoedad)echo "selected"?>>11-12</option>
			<option value='12-13' <?if ("12-13"==$rangoedad)echo "selected"?>>12-13</option>
			<option value='13-14' <?if ("13-14"==$rangoedad)echo "selected"?>>13-14</option>
			<option value='14-15' <?if ("14-15"==$rangoedad)echo "selected"?>>14-15</option>
			<option value='15-16' <?if ("15-16"==$rangoedad)echo "selected"?>>15-16</option>
			<option value='16-17' <?if ("16-17"==$rangoedad)echo "selected"?>>16-17</option>
			<option value='17-18' <?if ("17-18"==$rangoedad)echo "selected"?>>17-18</option>
			<option value='18-19' <?if ("18-19"==$rangoedad)echo "selected"?>>18-19</option>
			<option value='19-20' <?if ("19-20"==$rangoedad)echo "selected"?>>19-20</option>
			<option value='20-21' <?if ("20-21"==$rangoedad)echo "selected"?>>20-21</option>
			<option value='21-22' <?if ("21-22"==$rangoedad)echo "selected"?>>21-22</option>
			<option value='22-23' <?if ("22-23"==$rangoedad)echo "selected"?>>22-23</option>
			<option value='23-24' <?if ("23-24"==$rangoedad)echo "selected"?>>23-24</option>
			<option value='24-25' <?if ("24-25"==$rangoedad)echo "selected"?>>24-25</option>
			<option value='25-26' <?if ("25-26"==$rangoedad)echo "selected"?>>25-26</option>
			<option value='26-27' <?if ("26-27"==$rangoedad)echo "selected"?>>26-27</option>
			<option value='27-28' <?if ("27-28"==$rangoedad)echo "selected"?>>27-28</option>
			<option value='28-29' <?if ("28-29"==$rangoedad)echo "selected"?>>28-29</option>
			<option value='29-30' <?if ("29-30"==$rangoedad)echo "selected"?>>29-30</option>
			<option value='30-31' <?if ("30-31"==$rangoedad)echo "selected"?>>30-31</option>
			<option value='31-32' <?if ("31-32"==$rangoedad)echo "selected"?>>31-32</option>
			<option value='32-33' <?if ("32-33"==$rangoedad)echo "selected"?>>32-33</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>33-34</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>34-35</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>35-36</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>36-37</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>37-38</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>38-39</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>39-40</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>40-41</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>41-42</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>42-43</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>43-44</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>44-45</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>45-46</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>46-47</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>47-48</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>48-49</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>49-50</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>50-51</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>51-52</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>52-53</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>53-54</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>54-55</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>55-56</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>56-57</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>57-58</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>58-59</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>59-60</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>60-61</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>61-62</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>62-63</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>63-64</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>64-65</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>65-66</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>66-67</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>67-68</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>68-69</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>69-70</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>70-71</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>71-72</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>72-73</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>73-74</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>74-75</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>75-76</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>76-77</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>77-78</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>78-79</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>79-80</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>80-81</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>81-82</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>82-83</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>83-84</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>84-85</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>85-86</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>86-87</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>87-88</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>88-89</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>89-90</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>90-91</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>91-92</option>
			<option value='33-34' <?if ("33-34"==$rangoedad)echo "selected"?>>92-93</option>
			</select>
			
			<input type="submit" name="muestra" value='Muestra' onclick="return control_muestra()" >
			<input type="submit" value="Generar Excel" name="generar_excel">
    	  </b>     
	  </td>
     </tr>
</table>

<?if ($_POST['muestra']){?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$res_comprobante->recordCount()?> Filtro: <?=$_POST['periodo'];?></td>       
      </tr>
    </table>
   </td>
  </tr>
    <td align=right id=mo>Efector</td>
    <td align=right id=mo>DNI</td>
    <td align=right id=mo>Nombre</td>
    <td align=right id=mo>Apellido</td>
    <td align=right id=mo>Fecha Nac</td>
    <td align=right id=mo>Edad</td>
    <td align=right id=mo>Domicilio</td>
    <td align=right id=mo>Fecha Control</td>
    <td align=right id=mo>Peso</td>
    <td align=right id=mo>Talla</td>
    <td align=right id=mo>IMC</td>
    <td align=right id=mo>Per. Cef.</td>
    <td align=right id=mo>Perc Peso/Edad</td>
    <td align=right id=mo>Perc Talla/Edad</td>
    <td align=right id=mo>Perc Perim. Cefalico/Edad</td>
    <td align=right id=mo>Perc IMC/Edad</td>
    <td align=right id=mo>Perc Peso/Talla</td>
    
  </tr>
 <? $t=0;
 	$s=0;
   while (!$res_comprobante->EOF) {?>  	
  
    <tr <?=atrib_tr()?> >     
	     <td ><?=$res_comprobante->fields['nom_efe']?></td>    
	     <td ><?=($res_comprobante->fields['afidni']!='')?$res_comprobante->fields['afidni']:$res_comprobante->fields['documento'];?></td>    
	     <td ><?=($res_comprobante->fields['afinombre']!='')?$res_comprobante->fields['afinombre']:$res_comprobante->fields['nombre'];?></td>    
	     <td ><?=($res_comprobante->fields['afiapellido']!='')?$res_comprobante->fields['afiapellido']:$res_comprobante->fields['apellido'];?></td>  
	     <td ><?=($res_comprobante->fields['afifechanac']!='')?fecha($res_comprobante->fields['afifechanac']):fecha($res_comprobante->fields['fecha_nac']);?></td>  
	     <td ><?=($res_comprobante->fields['edad_smiafil']!='')?$res_comprobante->fields['edad_smiafil']:$res_comprobante->fields['edad_benef'];?></td>  
	     <td ><?=($res_comprobante->fields['afidomlocalidad']!='')?$res_comprobante->fields['afidomlocalidad']:$res_comprobante->fields['domicilio'];?></td>  
	     <td ><?=fecha($res_comprobante->fields['fecha_control'])?></td>  
	     <td ><?=number_format($res_comprobante->fields["peso"],2,',',0)?></td>    
	     <td ><?=number_format($res_comprobante->fields["talla"],2,',',0)?></td>    
	     <td ><?=number_format($res_comprobante->fields["imc"],2,',',0)?></td>    
		 <td ><?if ($res_comprobante->fields['perim_cefalico']=="") echo "&nbsp"; echo number_format($res_comprobante->fields["perim_cefalico"],2,',',0)?></td>
		 <td ><?if($res_comprobante->fields['percen_peso_edad']=="1")echo "<3"; elseif ($res_comprobante->fields['percen_peso_edad']=="2")echo "3-10";  elseif ($res_comprobante->fields['percen_peso_edad']=="3")echo ">10-90 ";  elseif ($res_comprobante->fields['percen_peso_edad']=="4")echo ">90-97 ";  elseif ($res_comprobante->fields['percen_peso_edad']=="5")echo ">97";else echo"Dato Sin Ingresar";?></td>
	     <td ><?if ($res_comprobante->fields['percen_talla_edad']=='1') echo "-3"; elseif ($res_comprobante->fields['percen_talla_edad']=='2') echo "3-97"; elseif ($res_comprobante->fields['percen_talla_edad']=='3') echo "+97";  else echo "Dato Sin Ingresar";?></td>	
	  	 <td ><?if ($res_comprobante->fields['percen_perim_cefali_edad']=='1') echo "-3"; elseif ($res_comprobante->fields['percen_perim_cefali_edad']=='2') echo "3-97"; elseif ($res_comprobante->fields['percen_perim_cefali_edad']=='3') echo "+97"; else echo "Dato Sin Ingresar";?></td>		   
   	     <td ><?if ($res_comprobante->fields['percen_imc_edad']=='1') echo "<3"; elseif ($res_comprobante->fields['percen_imc_edad']=='2') echo "3-10"; elseif ($res_comprobante->fields['percen_imc_edad']=='3') echo " >10-85"; elseif ($res_comprobante->fields['percen_imc_edad']=='4') echo ">85-97";elseif ($res_comprobante->fields['percen_imc_edad']=='5') echo " >97"; else echo "Dato Sin Ingresar";?></td>
	     <td ><?if ($res_comprobante->fields['percen_peso_talla']=='1') echo "<3"; elseif ($res_comprobante->fields['percen_peso_talla']=='2') echo "3-10"; elseif ($res_comprobante->fields['percen_peso_talla']=='3') echo ">10-85"; elseif ($res_comprobante->fields['percen_peso_talla']=='4') echo ">85-97"; elseif ($res_comprobante->fields['percen_peso_talla']=='5') echo " >97"; else  echo "Dato Sin Ingresar"?></td>			                                 
			                           
    </tr>
	<?$res_comprobante->MoveNext();
   }?>
    
    
</table>
<?}?>
<br>
	
</td>
</table>

</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
