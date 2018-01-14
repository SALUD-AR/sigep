<?php

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

$sql_tmp="SELECT distinct on (afidni,fecha_control)
          afidni,afinombre,afiapellido,afifechanac,fecha_control,estado from (
          SELECT distinct on (nacer.smiafiliados.afidni,fichero.fichero.fecha_control)
          nacer.smiafiliados.afidni,
          nacer.smiafiliados.afinombre,
          nacer.smiafiliados.afiapellido,
          nacer.smiafiliados.afifechanac,
          fichero.fichero.fecha_control,
          'desde fichero (nacer)' as estado
          from fichero.fichero
          inner join nacer.smiafiliados on fichero.fichero.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
          where cuie='$cuie' and fichero.fecha_control between '2014-12-31' and '2015-12-31' and diabetico='SI'
union
          select distinct on (leche.beneficiarios.documento,fichero.fichero.fecha_control)
          leche.beneficiarios.documento,
          leche.beneficiarios.nombre,
          leche.beneficiarios.apellido,
          leche.beneficiarios.fecha_nac,
          fichero.fichero.fecha_control,
          'desde fichero (emp.rapido)' as estado
          from fichero.fichero
          inner join leche.beneficiarios on fichero.fichero.id_beneficiarios=leche.beneficiarios.id_beneficiarios
          where cuie='$cuie' and 
          fichero.fecha_control between '2014-12-31' and '2015-12-31' and diabetico='SI'
union

    SELECT distinct on (clasificacion_remediar2.num_doc,clasificacion_remediar2.fecha_control)
    clasificacion_remediar2.num_doc,
    clasificacion_remediar2.nombre,
    clasificacion_remediar2.apellido,
    clasificacion_remediar2.fecha_nac,
    clasificacion_remediar2.fecha_control,
    'desde clasificacion' as estado
    from trazadoras.clasificacion_remediar2
    where cuie = '$cuie' and fecha_control between '2014-12-31' and '2015-12-31' and diabetico = 'SI'

union
--consulta desde seguimientos
  
  select distinct on (nacer.smiafiliados.afidni,trazadoras.seguimiento_remediar.fecha_comprobante)
  nacer.smiafiliados.afidni,
  nacer.smiafiliados.afinombre,
  nacer.smiafiliados.afiapellido,
  nacer.smiafiliados.afifechanac,
  trazadoras.seguimiento_remediar.fecha_comprobante as fecha_control,
  'desde seguimiento' as estado
  --trazadoras.clasificacion_remediar2.diabetico,
  --trazadoras.clasificacion_remediar2.hipertenso
  from trazadoras.seguimiento_remediar
  inner join nacer.smiafiliados on nacer.smiafiliados.clavebeneficiario=trim (' ' from trazadoras.seguimiento_remediar.clave_beneficiario)
  inner join trazadoras.clasificacion_remediar2 on trazadoras.clasificacion_remediar2.num_doc=nacer.smiafiliados.afidni
  where trazadoras.seguimiento_remediar.efector='$cuie' and (trazadoras.clasificacion_remediar2.diabetico is not null or trazadoras.clasificacion_remediar2.hipertenso is not null)
  and trazadoras.seguimiento_remediar.fecha_comprobante between '2014-12-31' and '2015-12-31'
  and trazadoras.clasificacion_remediar2.diabetico='SI'

    ) as ccc order by 1,5 ";
$result=sql($sql_tmp) or fin_pagina();

echo $html_header;
?>
<form name=form1 method=post action="detalle_ceb.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$result->RecordCount();?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align="right" id="mo">Inscripcion</td>        
    <td align="right" id="mo">DNI</td>        
    <td align="right" id="mo">Apellido</td>       
    <td align="right" id="mo">Nombre</td>  
    <td align="right" id="mo">Fecha Nacimiento</td>     
    <td align="right" id="mo">Fecha Control</td>
  </tr>
  <?   
  while (!$result->EOF) {
  switch ($result->fields['estado']) {
      case 'na':
        {?>  
      <tr>     
      <td>Inscripto SUMAR</td>
      <td><?=$result->fields['afidni']?></td>
      <td><?=$result->fields['afiapellido']?></td>
      <td><?=$result->fields['afinombre']?></td>
      <td><?=fecha($result->fields['afifechanac'])?></td> 
      <td><?=fecha($result->fields['fecha_control'])?></td> 
    </tr>
  <? break;}
       
     case 'nu':
      {?>
      <tr>     
     <td>Inscripto por Fichero</td>
     <td><?=$result->fields['afidni']?></td>
     <td><?=$result->fields['afiapellido']?></td>
     <td><?=$result->fields['afinombre']?></td>
     <td><?=fecha($result->fields['afifechanac'])?></td> 
     <td><?=fecha($result->fields['fecha_control'])?></td> 
    </tr>
  <? break;}   

      default: {?>
        <tr>     
        <td>Desde Seguimientos</td>
        <td><?=$result->fields['afidni']?></td>
        <td><?=$result->fields['afiapellido']?></td>
        <td><?=$result->fields['afinombre']?></td>
        <td><?=fecha($result->fields['afifechanac'])?></td> 
        <td><?=fecha($result->fields['fecha_control'])?></td> 
    </tr>
     <? break; }

    }//del swith 
	$result->MoveNext();
  }?>
 </table>
 </form>
 <?=fin_pagina();// aca termino ?>