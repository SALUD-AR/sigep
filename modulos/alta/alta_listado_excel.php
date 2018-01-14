<?php

require_once ("../../config.php");

$sql="SELECT * FROM alta.alta
			left join nacer.efe_conv using (CUIE)";


$result=sql($sql) or fin_pagina();

excel_header("alta_listado.xls");

?>
<form name=form1 method=post action="alta_listado_excel.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total: </b><?=$result->RecordCount();?> 
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>   	
    <td>Efector de Alta</td>      	    
    <td>Efector Habitual</td>      	    
    <td>Fecha Alta</td>      	    
    <td>Fecha Parto</td>      	    
    <td>Nombre Madre</td>      	    
    <td>Docuememto Madre</td>      	    
    <td>Nombre Bebe</td>      	    
    <td>Domicilio</td>      	    
    <td>Reponsable Obstetricia</td>      	    
    <td>Reponsable Neonatologia</td>      	    
    <td>Reponsable Enfermeria</td>      	    
    <td>Llena Epicrisis</td>      	    
    <td>Carnet Parenteral</td>      	    
    <td>Peso al Nacer</td>      	    
    <td>Riesgo Social</td>      	    
    <td>Sifilis</td>      	    
    <td>HIV</td>      	    
    <td>Hep B</td>      	    
    <td>Chagas</td>      	    
    <td>Toxoplasmosis</td>      	    
    <td>Pesquisa Neonatal</td>      	    
    <td>Vacuna Hep B</td>      	    
    <td>Vacuna BCG</td>      	    
    <td>Grupo y Factor de la Madre</td>      	    
    <td>Grupo y Factor del Bebe</td>      	    
    <td>Gamma Anti RH</td>      	    
    <td>Observaciones</td>
    <td>Puericultura</td>
    <td>Alarma Bebe</td>
    <td>Alarma Madre</td>
    <td>Lactancia Materna</td>
    <td>Salud Reproductiva</td>
    <td>Cuidados de Puerperio</td>  
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr> 
     <td ><?=$result->fields['nombre']?></td>         
     <td ><?=$result->fields['cuie_at_hab']?></td>         
     <td ><?=Fecha($result->fields['fecha_alta'])?></td>     
     <td ><?=Fecha($result->fields['fecha_parto'])?></td>     
     <td ><?=$result->fields['nom_madre']?></td>    
     <td ><?=$result->fields['doc_madre']?></td>    
     <td ><?=$result->fields['nom_bebe']?></td>    
     <td ><?=$result->fields['domicilio']?></td>    
     <td ><?=$result->fields['rep_obstetricia']?></td>    
     <td ><?=$result->fields['rep_neo']?></td>    
     <td ><?=$result->fields['rep_enf']?></td>    
     <td ><?=$result->fields['llena_epi']?></td>    
     <td ><?=$result->fields['carnet_parenteral']?></td>    
     <td ><?=$result->fields['peso_nacer']?></td>    
     <td ><?=$result->fields['riesgo_social']?></td>    
     <td ><?=$result->fields['sifilis']?></td>    
     <td ><?=$result->fields['hiv']?></td>    
     <td ><?=$result->fields['hep_b']?></td>    
     <td ><?=$result->fields['chagas']?></td>    
     <td ><?=$result->fields['toxo']?></td>    
     <td ><?=$result->fields['pes_neonatal']?></td>    
     <td ><?=$result->fields['vac_hep_b']?></td>    
     <td ><?=$result->fields['vac_bcg']?></td>    
     <td ><?=$result->fields['grupo_factor_mama']?></td>    
     <td ><?=$result->fields['grupo_factor_bebe']?></td>    
     <td ><?=$result->fields['gamma_anti_rh']?></td>    
     <td ><?=$result->fields['observaciones']?></td>
     <td ><?=$result->fields['pueri']?></td>
     <td ><?=$result->fields['alarma_bebe']?></td>
     <td ><?=$result->fields['alarma_madre']?></td>
     <td ><?=$result->fields['lac_materna']?></td>
     <td ><?=$result->fields['salud_repro']?></td>
     <td ><?=$result->fields['cuidados_puerpe']?></td>       
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>