<?php

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

switch ($solicita_datos) {
	case 'ceb_a': {

		$sql_ceb="SELECT distinct ON (cuie,afidni,afinombre,afiapellido,afifechanac)
		cuie,afidni,afinombre,afiapellido,afifechanac,grupo,fecha_comprobante,codigo,descripcion from (
		select facturacion.prestacion.*,
		facturacion.comprobante.id_smiafiliados,
		facturacion.comprobante.cuie,
		facturacion.comprobante.fecha_comprobante,
		nacer.smiafiliados.afidni,
		nacer.smiafiliados.afinombre,
		nacer.smiafiliados.afiapellido,
		nacer.smiafiliados.afifechanac,
		nacer.smiafiliados.grupopoblacional as grupo,
		facturacion.nomenclador.codigo,
		facturacion.nomenclador.descripcion
		from facturacion.prestacion
		inner join facturacion.nomenclador on prestacion.id_nomenclador=nomenclador.id_nomenclador
		inner join facturacion.comprobante on prestacion.id_comprobante=comprobante.id_comprobante
		inner join nacer.smiafiliados on comprobante.id_smiafiliados=smiafiliados.id_smiafiliados
		where facturacion.nomenclador.ceb='s' and facturacion.comprobante.cuie='$cuie' and 
		facturacion.comprobante.fecha_comprobante between '2014-12-31' and '2015-12-31'
		order by nacer.smiafiliados.afidni
	) as ccc where grupo='A'";

$res_ceb=sql($sql_ceb) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$res_ceb->RecordCount();?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="98%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=center width="10%" id="mo">DNI</td>      	
    <td align=right width="25%" id="mo">Nombre</td>   	
    <td align=right width="25%" id="mo">Apellido</td>      	
    <td align=right width="10%"id="mo">Fecha Nacimiento</td>      	
    <td align=right width="10%" id="mo">Fecha Control</td>      	
    <td align=right id="mo">Codigo</td>
    <td align=right width="35%"id="mo">Descripcion</td>
  </tr>
  <?   
  while (!$res_ceb->EOF) {?>
     <tr>     
     <td align=center><?=$res_ceb->fields['afidni']?></td>
	 <td><?=$res_ceb->fields['afinombre']?></td>
	 <td><?=$res_ceb->fields['afiapellido']?></td>
	 <td align=center><?=Fecha($res_ceb->fields['afifechanac'])?></td>
	 <td align=center><?=Fecha($res_ceb->fields['fecha_comprobante'])?></td>
	 <td align=center><?=$res_ceb->fields['codigo']?></td>
	 <td><?=$res_ceb->fields['descripcion']?></td>
    </tr>	
	<?$res_ceb->MoveNext();
    }?>
	 </table>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

case 'ceb_b': {

		$sql_ceb="SELECT distinct ON (cuie,afidni,afinombre,afiapellido,afifechanac)
		cuie,afidni,afinombre,afiapellido,afifechanac,grupo,fecha_comprobante,codigo,descripcion from (
		select facturacion.prestacion.*,
		facturacion.comprobante.id_smiafiliados,
		facturacion.comprobante.cuie,
		facturacion.comprobante.fecha_comprobante,
		nacer.smiafiliados.afidni,
		nacer.smiafiliados.afinombre,
		nacer.smiafiliados.afiapellido,
		nacer.smiafiliados.afifechanac,
		nacer.smiafiliados.grupopoblacional as grupo,
		facturacion.nomenclador.codigo,
		facturacion.nomenclador.descripcion
		from facturacion.prestacion
		inner join facturacion.nomenclador on prestacion.id_nomenclador=nomenclador.id_nomenclador
		inner join facturacion.comprobante on prestacion.id_comprobante=comprobante.id_comprobante
		inner join nacer.smiafiliados on comprobante.id_smiafiliados=smiafiliados.id_smiafiliados
		where facturacion.nomenclador.ceb='s' and facturacion.comprobante.cuie='$cuie' and 
		facturacion.comprobante.fecha_comprobante between '2014-12-31' and '2015-12-31'
		order by nacer.smiafiliados.afidni
	) as ccc where grupo='B'";

$res_ceb=sql($sql_ceb) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$res_ceb->RecordCount();?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=center width="10%" id="mo">DNI</td>      	
    <td align=right width="25%" id="mo">Nombre</td>   	
    <td align=right width="25%" id="mo">Apellido</td>      	
    <td align=right width="10%"id="mo">Fecha Nacimiento</td>       	
    <td align=right id="mo">Fecha Control</td>      	
    <td align=right id="mo">Codigo</td>
    <td align=right width="35%"id="mo">Descripcion</td>
  </tr>
  <?   
  while (!$res_ceb->EOF) {?>
     <tr>     
     <td><?=$res_ceb->fields['afidni']?></td>
	 <td><?=$res_ceb->fields['afinombre']?></td>
	 <td><?=$res_ceb->fields['afiapellido']?></td>
	 <td><?=$res_ceb->fields['afifechanac']?></td>
	 <td><?=Fecha($res_ceb->fields['fecha_comprobante'])?></td>
	 <td><?=$res_ceb->fields['codigo']?></td>
	 <td><?=$res_ceb->fields['descripcion']?></td>
    </tr>	
	<?$res_ceb->MoveNext();
    }?>
	 </table>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

case 'ceb_c': {

		$sql_ceb="SELECT distinct ON (cuie,afidni,afinombre,afiapellido,afifechanac)
		cuie,afidni,afinombre,afiapellido,afifechanac,grupo,fecha_comprobante,codigo,descripcion from (
		select facturacion.prestacion.*,
		facturacion.comprobante.id_smiafiliados,
		facturacion.comprobante.cuie,
		facturacion.comprobante.fecha_comprobante,
		nacer.smiafiliados.afidni,
		nacer.smiafiliados.afinombre,
		nacer.smiafiliados.afiapellido,
		nacer.smiafiliados.afifechanac,
		nacer.smiafiliados.grupopoblacional as grupo,
		facturacion.nomenclador.codigo,
		facturacion.nomenclador.descripcion
		from facturacion.prestacion
		inner join facturacion.nomenclador on prestacion.id_nomenclador=nomenclador.id_nomenclador
		inner join facturacion.comprobante on prestacion.id_comprobante=comprobante.id_comprobante
		inner join nacer.smiafiliados on comprobante.id_smiafiliados=smiafiliados.id_smiafiliados
		where facturacion.nomenclador.ceb='s' and facturacion.comprobante.cuie='$cuie' and 
		facturacion.comprobante.fecha_comprobante between '2014-12-31' and '2015-12-31'
		order by nacer.smiafiliados.afidni
	) as ccc where grupo='C'";

$res_ceb=sql($sql_ceb) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$res_ceb->RecordCount();?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=center width="10%" id="mo">DNI</td>      	
    <td align=right width="20%" id="mo">Nombre</td>   	
    <td align=right width="20%" id="mo">Apellido</td>      	
    <td align=center width="15%" id="mo">Fecha Nacimiento</td>       	
    <td align=center width="15%" id="mo">Fecha Control</td>      	
    <td align=center id="mo">Codigo</td>
    <td align=right width="40%"id="mo">Descripcion</td>
  </tr>
  <?   
  while (!$res_ceb->EOF) {?>
     <tr>     
     <td align="center"><?=$res_ceb->fields['afidni']?></td>
	 <td><?=$res_ceb->fields['afinombre']?></td>
	 <td><?=$res_ceb->fields['afiapellido']?></td>
	 <td align="center"><?=Fecha($res_ceb->fields['afifechanac'])?></td>
	 <td align="center"><?=Fecha($res_ceb->fields['fecha_comprobante'])?></td>
	 <td align="center"><?=$res_ceb->fields['codigo']?></td>
	 <td><?=$res_ceb->fields['descripcion']?></td>
    </tr>	
	<?$res_ceb->MoveNext();
    }?>
	 </table>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

case 'ceb_d': {

		$sql_ceb="SELECT distinct ON (cuie,afidni,afinombre,afiapellido,afifechanac)
		cuie,afidni,afinombre,afiapellido,afifechanac,grupo,fecha_comprobante,codigo,descripcion from (
		select facturacion.prestacion.*,
		facturacion.comprobante.id_smiafiliados,
		facturacion.comprobante.cuie,
		facturacion.comprobante.fecha_comprobante,
		nacer.smiafiliados.afidni,
		nacer.smiafiliados.afinombre,
		nacer.smiafiliados.afiapellido,
		nacer.smiafiliados.afifechanac,
		nacer.smiafiliados.grupopoblacional as grupo,
		facturacion.nomenclador.codigo,
		facturacion.nomenclador.descripcion
		from facturacion.prestacion
		inner join facturacion.nomenclador on prestacion.id_nomenclador=nomenclador.id_nomenclador
		inner join facturacion.comprobante on prestacion.id_comprobante=comprobante.id_comprobante
		inner join nacer.smiafiliados on comprobante.id_smiafiliados=smiafiliados.id_smiafiliados
		where facturacion.nomenclador.ceb='s' and facturacion.comprobante.cuie='$cuie' and 
		facturacion.comprobante.fecha_comprobante between '2014-12-31' and '2015-12-31'
		order by nacer.smiafiliados.afidni
	) as ccc where grupo='D'";

$res_ceb=sql($sql_ceb) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$res_ceb->RecordCount();?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=center width="10%" id="mo">DNI</td>      	
    <td align=right width="25%" id="mo">Nombre</td>   	
    <td align=right width="25%" id="mo">Apellido</td>      	
    <td align=right width="10%" id="mo">Fecha Nacimiento</td>      	
    <td align=right width="10%" id="mo">Fecha Control</td>      	
    <td align=right id="mo">Codigo</td>
    <td align=right width="35%"id="mo">Descripcion</td>
  </tr>
  <?   
  while (!$res_ceb->EOF) {?>
     <tr>     
     <td align="center"><?=$res_ceb->fields['afidni']?></td>
	 <td><?=$res_ceb->fields['afinombre']?></td>
	 <td><?=$res_ceb->fields['afiapellido']?></td>
	 <td align="center"><?=$res_ceb->fields['afifechanac']?></td>
	 <td align="center"><?=Fecha($res_ceb->fields['fecha_comprobante'])?></td>
	 <td align="center"><?=$res_ceb->fields['codigo']?></td>
	 <td><?=$res_ceb->fields['descripcion']?></td>
    </tr>	
	<?$res_ceb->MoveNext();
    }?>
	 </table>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

case 'facturacion': {

		$sql="SELECT distinct on (facturacion.factura.periodo_actual)
  expediente.expediente.id_expediente,
  expediente.expediente.nro_exp,
  expediente.expediente.id_factura,
  expediente.expediente.monto,
  expediente.expediente.fecha_ing,
  nacer.efe_conv.cuie,
  nacer.efe_conv.nombre as efector,
  facturacion.factura.periodo_actual as periodo_fact,
  facturacion.factura.fecha_factura

from expediente.expediente 
inner join nacer.efe_conv on nacer.efe_conv.id_efe_conv=expediente.expediente.id_efe_conv
inner join facturacion.factura on expediente.expediente.id_factura=facturacion.factura.id_factura

where fecha_ing between '$fecha_desde' and '$fecha_hasta' and nacer.efe_conv.cuie='$cuie'";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$res_sql->RecordCount();?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="6"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id="mo">ID Factura</td>      	
    <td align=right id="mo">ID Expediente</td>   	
    <td align=right id="mo">Nro.Expediente</td>      	
    <td align=right id="mo">Fecha Ingreso</td>      	
    <td align=right id="mo">Monto</td>      	
    <td align=right id="mo">Periodo</td>
    </tr>
  <?   
  while (!$res_sql->EOF) {?>
     <tr>     
     <td align="center"><?=$res_sql->fields['id_factura']?></td>
	 <td align="center"><?=$res_sql->fields['id_expediente']?></td>
	 <td><?=$res_sql->fields['nro_exp']?></td>
	 <td align="center"><?=$res_sql->fields['fecha_ing']?></td>
	 <td align="center">$<?=$res_sql->fields['monto']?></td>
	 <td align="center"><?=$res_sql->fields['periodo_fact']?></td>
	 </tr>	
	<?$res_sql->MoveNext();
    }?>
	 </table>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

 case 'controles de ninos menor de 1': {
 		$sql="SELECT distinct nacer.smiafiliados.afidni,
		nacer.smiafiliados.afinombre,
		nacer.smiafiliados.afiapellido,
		nacer.smiafiliados.afifechanac,
		trazadorassps.trazadora_4.fecha_control,
		trazadorassps.trazadora_4.peso,
  		trazadorassps.trazadora_4.talla,
  		trazadorassps.trazadora_4.perimetro_cefalico,
  		trazadorassps.trazadora_4.percentilo_peso_edad,
  		trazadorassps.trazadora_4.percentilo_talla_edad,
  		trazadorassps.trazadora_4.percentilo_perim_cefalico_edad,
  		trazadorassps.trazadora_4.percentilo_peso_talla
		from trazadorassps.trazadora_4 
		inner join nacer.smiafiliados on trazadorassps.trazadora_4.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
		where cuie = '$cuie' and 
		(fecha_control - fecha_nac >= 0 and fecha_control - fecha_nac < 365) and
		(fecha_control between '$fecha_desde' and '$fecha_hasta')

		union --trazadorassps.trazadora_4 con beneficiarios en leche.beneficiarios
		
		select distinct leche.beneficiarios.documento,
		leche.beneficiarios.nombre,
		leche.beneficiarios.apellido,
		leche.beneficiarios.fecha_nac,
		trazadorassps.trazadora_4.fecha_control,
		trazadorassps.trazadora_4.peso,
  		trazadorassps.trazadora_4.talla,
  		trazadorassps.trazadora_4.perimetro_cefalico,
  		trazadorassps.trazadora_4.percentilo_peso_edad,
  		trazadorassps.trazadora_4.percentilo_talla_edad,
  		trazadorassps.trazadora_4.percentilo_perim_cefalico_edad,
  		trazadorassps.trazadora_4.percentilo_peso_talla
		from trazadorassps.trazadora_4 
		inner join leche.beneficiarios on trazadorassps.trazadora_4.id_beneficiarios=leche.beneficiarios.id_beneficiarios
		where cuie = '$cuie' and 
		(trazadora_4.fecha_control - trazadora_4.fecha_nac >= 0 and trazadora_4.fecha_control - trazadora_4.fecha_nac < 365) and
		(trazadora_4.fecha_control between '$fecha_desde' and '$fecha_hasta')
		
		union --beneficiarios en trazadoras.nino_new

		SELECT distinct (num_doc::numeric(10,0))::text as dni,nombre,apellido,fecha_nac,fecha_control,
		peso,talla,perim_cefalico,percen_peso_edad,percen_talla_edad,percen_perim_cefali_edad,percen_peso_talla
		from trazadoras.nino_new 
		where 
		cuie = '$cuie' and 
		(fecha_control - fecha_nac >= 0 and fecha_control - fecha_nac < 365) and (fecha_control between '$fecha_desde' and '$fecha_hasta')";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$res_sql->RecordCount();?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="12"> 
  <tr bgcolor=#C0C0FF>
    <td align=center width="10%" id="mo">DNI</td>      	
    <td align=right width="25%" id="mo">Nombre</td>   	
    <td align=right width="25%" id="mo">Apellido</td>      	
    <td align=right width="10%"id="mo">Fecha Nacimiento</td>       	
    <td align=right id="mo">Fecha Control</td> 
    <td align=right id="mo">Peso</td> 
    <td align=right id="mo">Talla</td> 
    <td align=right id="mo">Perimetro Cefalico</td> 
    <td align=right id="mo">Prcent.Peso/Edad</td>  
    <td align=right id="mo">Prcent.Talla/Edad</td>	
    <td align=right id="mo">Prcent.Perm.Cefal./Edad</td>
    <td align=right id="mo">Prcent.Peso/Talla</td>
    </tr>
  <?   
  while (!$res_sql->EOF) {?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=Fecha($res_sql->fields['afifechanac'])?></td>
	 <td><?=Fecha($res_sql->fields['fecha_control'])?></td>
	 <td align="center"><?=$res_sql->fields['peso']?></td>
	 <td align="center"><?=$res_sql->fields['talla']?></td>
	 <td><?=$res_sql->fields['perimetro_cefalico']?></td>
	 <td align="center"><?=$res_sql->fields['percentilo_peso_edad']?></td>
	 <td align="center"><?=$res_sql->fields['percentilo_talla_edad']?></td>
	 <td align="center"><?=$res_sql->fields['percentilo_perim_cefalico_edad']?></td>
	 <td align="center"><?=$res_sql->fields['percentilo_peso_talla']?></td>
	 </tr>	
	<?$res_sql->MoveNext();
    }?>
	 </table>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case
	
case 'controles_1_a_9': {

		$sql="SELECT distinct nacer.smiafiliados.afidni,
		nacer.smiafiliados.afinombre,
		nacer.smiafiliados.afiapellido,
		nacer.smiafiliados.afifechanac,
		trazadorassps.trazadora_7.fecha_control,
		trazadorassps.trazadora_7.peso,
		trazadorassps.trazadora_7.talla,
		trazadorassps.trazadora_7.percentilo_peso_edad,
		trazadorassps.trazadora_7.percentilo_talla_edad,
		trazadorassps.trazadora_7.percentilo_peso_talla,
		trazadorassps.trazadora_7.tension_arterial::text
		from trazadorassps.trazadora_7 
		inner join nacer.smiafiliados on trazadorassps.trazadora_7.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
		where cuie = '$cuie' and 
		(fecha_control - fecha_nac >= 366 and fecha_control - fecha_nac < 3285) and
		(fecha_control between '2014-12-31' and '2015-12-31')

		union --trazadorassps.trazadora_7 con beneficiarios en leche.beneficiarios
		
		select distinct leche.beneficiarios.documento,
		leche.beneficiarios.nombre,
		leche.beneficiarios.apellido,
		leche.beneficiarios.fecha_nac,
		trazadorassps.trazadora_7.fecha_control,
		trazadorassps.trazadora_7.peso,
		trazadorassps.trazadora_7.talla,
		trazadorassps.trazadora_7.percentilo_peso_edad,
		trazadorassps.trazadora_7.percentilo_talla_edad,
		trazadorassps.trazadora_7.percentilo_peso_talla,
		trazadorassps.trazadora_7.tension_arterial::text
		from trazadorassps.trazadora_7 
		inner join leche.beneficiarios on trazadorassps.trazadora_7.id_beneficiarios=leche.beneficiarios.id_beneficiarios
		where cuie = '$cuie' and 
		(trazadora_7.fecha_control - trazadora_7.fecha_nac >= 366 and trazadora_7.fecha_control - trazadora_7.fecha_nac < 3285) and
		(trazadora_7.fecha_control between '2014-12-31' and '2015-12-31')
		
		union --beneficiarios en trazadoras.nino_new

		SELECT distinct (num_doc::numeric(10,0))::text as dni,nombre,apellido,fecha_nac,fecha_control,
		peso,talla,percen_peso_edad,percen_talla_edad,percen_peso_talla,ta
		from trazadoras.nino_new 
		where 
		cuie = '$cuie' and 
		(fecha_control - fecha_nac >= 366 and fecha_control - fecha_nac < 3285) and (fecha_control between '2014-12-31' and '2015-12-31')";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$res_sql->RecordCount();?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="11"> 
  <tr bgcolor=#C0C0FF>
    <td align=center width="10%" id="mo">DNI</td>      	
    <td align=right width="25%" id="mo">Nombre</td>   	
    <td align=right width="25%" id="mo">Apellido</td>      	
    <td align=right width="10%"id="mo">Fecha Nacimiento</td>       	
    <td align=right id="mo">Fecha Control</td> 
    <td align=right width="25%" id="mo">Peso</td>  
    <td align=right width="25%" id="mo">Talla</td>
    <td align=right width="25%" id="mo">Prcent.Peso/Edad</td>  
    <td align=right width="25%" id="mo">Prcent.Talla/Edad</td> 
    <td align=right width="25%" id="mo">Prcent.Peso/Talla</td> 
    <td align=right width="25%" id="mo">Tension Arterial</td> 	
     </tr>
  <?   
  while (!$res_sql->EOF) {?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['afifechanac']?></td>
	 <td><?=$res_sql->fields['fecha_control']?></td>
	 <td align="center"><?=$res_sql->fields['peso']?></td>
	 <td align="center"><?=$res_sql->fields['talla']?></td>
	 <td align="center"><?=$res_sql->fields['percentilo_peso_edad']?></td>
	 <td align="center"><?=$res_sql->fields['percentilo_talla_edad']?></td>
	 <td align="center"><?=$res_sql->fields['percen_peso_talla']?></td>
	 <td><?=$res_sql->fields['tension_arterial']?></td>
	 </tr>	
	<?$res_sql->MoveNext();
    }?>
	 </table>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

 case 'embar_antes_sem_12': {

		$sql="SELECT distinct on (nacer.smiafiliados.afidni)
    nacer.smiafiliados.afidni,
    nacer.smiafiliados.afinombre,
    nacer.smiafiliados.afiapellido,
    trazadorassps.trazadora_1.fum,
    trazadorassps.trazadora_1.fpp,
    trazadorassps.trazadora_1.edad_gestacional,
    trazadorassps.trazadora_1.fecha_control_prenatal
    from trazadorassps.trazadora_1 
    inner join nacer.smiafiliados on trazadorassps.trazadora_1.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
    where cuie = '$cuie' and 
    --fecha_control_prenatal <= fpp-196 
    trazadorassps.trazadora_1.edad_gestacional<=12 and 
    (fecha_control_prenatal between '$fecha_desde' and '$fecha_hasta')

    union --trazadorassps.trazadora_1 con beneficiarios en leche.beneficiarios
    
    select distinct on (leche.beneficiarios.documento)
    leche.beneficiarios.documento,
    leche.beneficiarios.nombre,
    leche.beneficiarios.apellido,
    trazadorassps.trazadora_1.fum,
    trazadorassps.trazadora_1.fpp,
    trazadorassps.trazadora_1.edad_gestacional,   
    trazadorassps.trazadora_1.fecha_control_prenatal
    from trazadorassps.trazadora_1 
    inner join leche.beneficiarios on trazadorassps.trazadora_1.id_beneficiarios=leche.beneficiarios.id_beneficiarios
    where cuie = '$cuie' and 
    --fecha_control_prenatal <= fpp-196 
    trazadorassps.trazadora_1.edad_gestacional<=12 and 
    (trazadora_1.fecha_control_prenatal between '$fecha_desde' and '$fecha_hasta')

    union

    select distinct on (num_doc)
              (num_doc::numeric(10,0))::text as dni,nombre,apellido,fum,fpp,sem_gestacion,fecha_control
              from trazadoras.embarazadas 
              where 
              cuie = '$cuie' and 
              fecha_control between '$fecha_desde' and '$fecha_hasta' and
              --fecha_control <= fpp-196
              trazadoras.embarazadas.sem_gestacion<=12";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$res_sql->RecordCount();?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id="mo">DNI</td>      	
    <td align=right id="mo">Nombre</td>   	
    <td align=right id="mo">Apellido</td>      	
    <td align=right id="mo">FUM</td>      	
    <td align=right id="mo">FPP</td>      	
    <td align=right id="mo">Edad Gestacional</td>
    <td align=right id="mo">Fecha Control</td>
  </tr>
  <?   
  while (!$res_sql->EOF) {?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['fum']?></td>
	 <td><?=$res_sql->fields['fpp']?></td>
	 <td><?=$res_sql->fields['edad_gestacional']?></td>
	 <td><?=$res_sql->fields['fecha_control_prenatal']?></td>
    </tr>	
	<?$res_sql->MoveNext();
    }?>
	 </table>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

case 'total_controles_embar': {

		$sql="SELECT distinct nacer.smiafiliados.afidni,
		nacer.smiafiliados.afinombre,
		nacer.smiafiliados.afiapellido,
		trazadorassps.trazadora_2.edad_gestacional,
		trazadorassps.trazadora_2.fecha_control,
		trazadorassps.trazadora_2.tension_arterial
		from trazadorassps.trazadora_2 
		inner join nacer.smiafiliados on trazadorassps.trazadora_2.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
		where cuie = '$cuie' and (fecha_control between '$fecha_desde' and '$fecha_hasta')

		union --trazadorassps.trazadora_2 con beneficiarios en leche.beneficiarios
		
		select distinct leche.beneficiarios.documento,
		leche.beneficiarios.nombre,
		leche.beneficiarios.apellido,
		trazadorassps.trazadora_2.edad_gestacional,
		trazadorassps.trazadora_2.fecha_control,
		trazadorassps.trazadora_2.tension_arterial
		from trazadorassps.trazadora_2 
		inner join leche.beneficiarios on trazadorassps.trazadora_2.id_beneficiarios=leche.beneficiarios.id_beneficiarios
		where cuie = '$cuie' and (trazadora_2.fecha_control between '$fecha_desde' and '$fecha_hasta')

		union

		SELECT distinct (num_doc::numeric (10,0))::text, nombre,apellido,sem_gestacion,fecha_control,ta
		from trazadoras.embarazadas where cuie = '$cuie' and 
		fecha_control between '$fecha_desde' and '$fecha_hasta'";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$res_sql->RecordCount();?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="6"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id="mo">DNI</td>      	
    <td align=right id="mo">Nombre</td>   	
    <td align=right id="mo">Apellido</td>      	
    <td align=right id="mo">Edad Gestacional</td>      	
    <td align=right id="mo">Fecha Control</td>      	
    <td align=right id="mo">Tension Arterial</td>
    </tr>
  <?   
  while (!$res_sql->EOF) {?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['edad_gestacional']?></td>
	 <td><?=$res_sql->fields['fecha_control']?></td>
	 <td><?=$res_sql->fields['tension_arterial']?></td>
	 </tr>	
	<?$res_sql->MoveNext();
    }?>
	 </table>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case


 case 'adolescentes': {

		$sql="SELECT distinct nacer.smiafiliados.afidni,
		nacer.smiafiliados.afinombre,
		nacer.smiafiliados.afiapellido,
		nacer.smiafiliados.afifechanac,
		trazadorassps.trazadora_10.fecha_control,
		trazadorassps.trazadora_10.talla,
		trazadorassps.trazadora_10.peso,
		trazadorassps.trazadora_10.tension_arterial
		from trazadorassps.trazadora_10
		inner join nacer.smiafiliados on trazadorassps.trazadora_10.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
		where cuie = '$cuie' and 
		(fecha_control - fecha_nac >= 3651 and fecha_control - fecha_nac < 7299) and
		(fecha_control between '2014-12-31' and '2015-12-31')
		union 
		select distinct leche.beneficiarios.documento,
			leche.beneficiarios.nombre,
			leche.beneficiarios.apellido,
			leche.beneficiarios.fecha_nac,
			trazadorassps.trazadora_10.fecha_control,
			trazadorassps.trazadora_10.talla,
			trazadorassps.trazadora_10.peso,
			trazadorassps.trazadora_10.tension_arterial
			from trazadorassps.trazadora_10 
			inner join leche.beneficiarios on trazadorassps.trazadora_10.id_beneficiarios=leche.beneficiarios.id_beneficiarios
			where cuie = '$cuie' and 
			(trazadora_10.fecha_control - trazadora_10.fecha_nac >= 3651 and trazadora_10.fecha_control - trazadora_10.fecha_nac < 7299) and
			(trazadora_10.fecha_control between '2014-12-31' and '2015-12-31')

		union

		SELECT distinct (num_doc::numeric(10,0))::text,nombre,apellido,fecha_nac,fecha_control,talla,peso,ta  from trazadoras.nino_new 
						where 
						cuie = '$cuie' and 
						(fecha_control - fecha_nac >= 3651 and fecha_control - fecha_nac < 7299) and
						(fecha_control between '2014-12-31' and '2015-12-31')";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$res_sql->RecordCount();?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="8"> 
  <tr bgcolor=#C0C0FF>
    <td align=center width="10%" id="mo">DNI</td>      	
    <td align=right width="25%" id="mo">Nombre</td>   	
    <td align=right width="25%" id="mo">Apellido</td>      	
    <td align=right width="10%"id="mo">Fecha Nacimiento</td>       	
    <td align=right id="mo">Fecha Control</td>      	
    <td align=right id="mo">Talla</td>
    <td align=right id="mo">Peso</td>
    <td align=right id="mo">Tension Arterial</td>
  </tr>
  <?   
  while (!$res_sql->EOF) {?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['afifechanac']?></td>
	 <td><?=$res_sql->fields['fecha_control']?></td>
	 <td align="center"><?=$res_sql->fields['talla']?></td>
	 <td align="center"><?=$res_sql->fields['peso']?></td>
	 <td><?=$res_sql->fields['tension_arterial']?></td>
    </tr>	
	<?$res_sql->MoveNext();
    }?>
	 </table>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case


case 'cuidado_sexual': {

		$sql="SELECT distinct on (nacer.smiafiliados.afidni,fichero.fichero.fecha_control)
        nacer.smiafiliados.afidni,
				nacer.smiafiliados.afinombre,
				nacer.smiafiliados.afiapellido,
				nacer.smiafiliados.afifechanac,
				fichero.fichero.fecha_control,
				fichero.fichero.peso,
				fichero.fichero.talla,
				fichero.fichero.ta
				from fichero.fichero
				inner join nacer.smiafiliados on fichero.fichero.id_smiafiliados=nacer.smiafiliados.id_smiafiliados
				where cuie = '$cuie' and 
				(fecha_control between '2014-12-31' and '2015-12-31' and fichero.fichero.salud_rep = 'SI')
				union 

				select distinct on (leche.beneficiarios.documento,fichero.fichero.fecha_control)
				leche.beneficiarios.documento,
        leche.beneficiarios.nombre,
				leche.beneficiarios.apellido,
				leche.beneficiarios.fecha_nac,
				fichero.fichero.fecha_control,
				fichero.fichero.peso,
				fichero.fichero.talla,
				fichero.fichero.ta
				from fichero.fichero 
				inner join leche.beneficiarios on fichero.fichero.id_beneficiarios=leche.beneficiarios.id_beneficiarios
				where cuie = '$cuie' and			
				(fecha_control between '2014-12-31' and '2015-12-31') and fichero.salud_rep = 'SI'";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$res_sql->RecordCount();?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="8"> 
  <tr bgcolor=#C0C0FF>
    <td align=center width="10%" id="mo">DNI</td>      	
    <td align=right width="25%" id="mo">Nombre</td>   	
    <td align=right width="25%" id="mo">Apellido</td>      	
    <td align=right width="10%"id="mo">Fecha Nacimiento</td>       	
    <td align=right id="mo">Fecha Control</td>      	
    <td align=right id="mo">Talla</td>
    <td align=right id="mo">Peso</td>
    <td align=right id="mo">Tensio Arterial</td>
  </tr>
  <?   
  while (!$res_sql->EOF) {?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['afifechanac']?></td>
	 <td><?=$res_sql->fields['fecha_control']?></td>
	 <td align="center"><?=$res_sql->fields['peso']?></td>
	 <td align="center"><?=$res_sql->fields['talla']?></td>
	 <td><?=$res_sql->fields['ta']?></td>
    </tr>	
	<?$res_sql->MoveNext();
    }?>
	 </table>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

//////////////////////////vacunas////////////////////////////////////////////


 case 'doble_viral': {

		$sql="SELECT distinct on (nacer.smiafiliados.afidni,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afifechanac
from trazadoras.vacunas 
inner join nacer.smiafiliados on vacunas.id_smiafiliados=smiafiliados.id_smiafiliados
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)

union

select distinct on (leche.beneficiarios.documento,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac)
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	leche.beneficiarios.documento,
	leche.beneficiarios.nombre,
	leche.beneficiarios.apellido,
	leche.beneficiarios.fecha_nac	
from trazadoras.vacunas 
inner join leche.beneficiarios on vacunas.id_beneficiarios=beneficiarios.id_beneficiarios
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
 
 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id="mo">DNI</td>      	
    <td align=right id="mo">Nombre</td>   	
    <td align=right id="mo">Apellido</td>      	
    <td align=right id="mo">Fecha Nacimiento</td>      	
    <td align=right id="mo">Fecha Vacuna</td>      	
    <td align=right id="mo">Nombre</td>
    <td align=right id="mo">Dosis</td>
  </tr>
  <? $count=0;  
  while (!$res_sql->EOF) {

  		if ($res_sql->fields['id_vac_apli']==11) { $count++;?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['afifechanac']?></td>
	 <td><?=$res_sql->fields['fecha_vac']?></td>
	 <td><?=$res_sql->fields['nom_vacum']?></td>
	 <td align="center"><?=$res_sql->fields['dosis']?></td>
    </tr>	
	<?}
		$res_sql->MoveNext();
    
   }?>
	</table>
	<br>
	<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$count;?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table>
 <br>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

 case 'hep_b': {

		$sql="SELECT distinct on (nacer.smiafiliados.afidni,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afifechanac
from trazadoras.vacunas 
inner join nacer.smiafiliados on vacunas.id_smiafiliados=smiafiliados.id_smiafiliados
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)

union

select distinct on (leche.beneficiarios.documento,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	leche.beneficiarios.documento,
	leche.beneficiarios.nombre,
	leche.beneficiarios.apellido,
	leche.beneficiarios.fecha_nac	
from trazadoras.vacunas 
inner join leche.beneficiarios on vacunas.id_beneficiarios=beneficiarios.id_beneficiarios
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id="mo">DNI</td>      	
    <td align=right id="mo">Nombre</td>   	
    <td align=right id="mo">Apellido</td>      	
    <td align=right id="mo">Fecha Nacimiento</td>      	
    <td align=right id="mo">Fecha Vacuna</td>      	
    <td align=right id="mo">Nombre</td>
    <td align=right id="mo">Dosis</td>
  </tr>
  <?  $count=0; 
  while (!$res_sql->EOF) {

  		if ($res_sql->fields['id_vac_apli']==2) { $count++;?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['afifechanac']?></td>
	 <td><?=$res_sql->fields['fecha_vac']?></td>
	 <td><?=$res_sql->fields['nom_vacum']?></td>
	 <td align="center"><?=$res_sql->fields['dosis']?></td>
    </tr>	
	<?}
		$res_sql->MoveNext();
    
   }?>
	 </table>
	<br>
	<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$count;?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table>
 <br>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case


 case 'neumococo': {

		$sql="SELECT distinct on (nacer.smiafiliados.afidni,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afifechanac
from trazadoras.vacunas 
inner join nacer.smiafiliados on vacunas.id_smiafiliados=smiafiliados.id_smiafiliados
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)

union

select distinct on (leche.beneficiarios.documento,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	leche.beneficiarios.documento,
	leche.beneficiarios.nombre,
	leche.beneficiarios.apellido,
	leche.beneficiarios.fecha_nac	
from trazadoras.vacunas 
inner join leche.beneficiarios on vacunas.id_beneficiarios=beneficiarios.id_beneficiarios
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
 
 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id="mo">DNI</td>      	
    <td align=right id="mo">Nombre</td>   	
    <td align=right id="mo">Apellido</td>      	
    <td align=right id="mo">Fecha Nacimiento</td>      	
    <td align=right id="mo">Fecha Vacuna</td>      	
    <td align=right id="mo">Nombre</td>
    <td align=right id="mo">Dosis</td>
  </tr>
  <? $count=0;  
  while (!$res_sql->EOF) {

  		if ($res_sql->fields['id_vac_apli']==16 || $res_sql->fields['id_vac_apli']==17) { $count++;?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['afifechanac']?></td>
	 <td><?=$res_sql->fields['fecha_vac']?></td>
	 <td><?=$res_sql->fields['nom_vacum']?></td>
	 <td align="center"><?=$res_sql->fields['dosis']?></td>
    </tr>	
	<?}
		$res_sql->MoveNext();
    
   }?>
	 </table>
	<br>
	<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$count;?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table>
 <br>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

 case 'pentavalente': {

		$sql="SELECT distinct on (nacer.smiafiliados.afidni,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afifechanac
from trazadoras.vacunas 
inner join nacer.smiafiliados on vacunas.id_smiafiliados=smiafiliados.id_smiafiliados
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)

union

select distinct on (leche.beneficiarios.documento,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	leche.beneficiarios.documento,
	leche.beneficiarios.nombre,
	leche.beneficiarios.apellido,
	leche.beneficiarios.fecha_nac	
from trazadoras.vacunas 
inner join leche.beneficiarios on vacunas.id_beneficiarios=beneficiarios.id_beneficiarios
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">

 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id="mo">DNI</td>      	
    <td align=right id="mo">Nombre</td>   	
    <td align=right id="mo">Apellido</td>      	
    <td align=right id="mo">Fecha Nacimiento</td>      	
	<td align=right id="mo">Fecha Vacuna</td>      	
    <td align=right id="mo">Nombre</td>
    <td align=right id="mo">Dosis</td>
  </tr>
  <? $count=0;
  while (!$res_sql->EOF) {

  		if ($res_sql->fields['id_vac_apli']==3) { $count++;?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['afifechanac']?></td>
	 <td><?=$res_sql->fields['fecha_vac']?></td>
	 <td><?=$res_sql->fields['nom_vacum']?></td>
	 <td align="center"><?=$res_sql->fields['dosis']?></td>
    </tr>	
	<?}
		$res_sql->MoveNext();
    
   }?>
	 </table>
	<br>
	<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$count;?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table>
 <br>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

 case 'cuadruple': {

		$sql="SELECT distinct on (nacer.smiafiliados.afidni,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afifechanac
from trazadoras.vacunas 
inner join nacer.smiafiliados on vacunas.id_smiafiliados=smiafiliados.id_smiafiliados
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)

union

select distinct on (leche.beneficiarios.documento,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	leche.beneficiarios.documento,
	leche.beneficiarios.nombre,
	leche.beneficiarios.apellido,
	leche.beneficiarios.fecha_nac	
from trazadoras.vacunas 
inner join leche.beneficiarios on vacunas.id_beneficiarios=beneficiarios.id_beneficiarios
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
 
 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id="mo">DNI</td>      	
    <td align=right id="mo">Nombre</td>   	
    <td align=right id="mo">Apellido</td>      	
    <td align=right id="mo">Fecha Nacimiento</td>      	
	<td align=right id="mo">Fecha Vacuna</td>      	
    <td align=right id="mo">Nombre</td>
    <td align=right id="mo">Dosis</td>
  </tr>
  <? $count=0;  
  while (!$res_sql->EOF) {

  		if ($res_sql->fields['id_vac_apli']==4) { $count++;?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['afifechanac']?></td>
	 <td><?=$res_sql->fields['fecha_vac']?></td>
	 <td><?=$res_sql->fields['nom_vacum']?></td>
	 <td align="center"><?=$res_sql->fields['dosis']?></td>
    </tr>	
	<?}
		$res_sql->MoveNext();
    
   }?>
	 </table>
	<br>
|<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$count;?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table>
 <br>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

 case 'sabin': {

		$sql="SELECT distinct on (nacer.smiafiliados.afidni,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afifechanac
from trazadoras.vacunas 
inner join nacer.smiafiliados on vacunas.id_smiafiliados=smiafiliados.id_smiafiliados
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)

union

select distinct on (leche.beneficiarios.documento,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	leche.beneficiarios.documento,
	leche.beneficiarios.nombre,
	leche.beneficiarios.apellido,
	leche.beneficiarios.fecha_nac	
from trazadoras.vacunas 
inner join leche.beneficiarios on vacunas.id_beneficiarios=beneficiarios.id_beneficiarios
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
 
 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id="mo">DNI</td>      	
    <td align=right id="mo">Nombre</td>   	
    <td align=right id="mo">Apellido</td>      	
    <td align=right id="mo">Fecha Nacimiento</td>      	
    <td align=right id="mo">Fecha Vacuna</td>      	
    <td align=right id="mo">Nombre</td>
    <td align=right id="mo">Dosis</td>
  </tr>
  <? $count=0;  
  while (!$res_sql->EOF) {

  		if ($res_sql->fields['id_vac_apli']==5) { $count++;?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['afifechanac']?></td>
	 <td><?=$res_sql->fields['fecha_vac']?></td>
	 <td><?=$res_sql->fields['nom_vacum']?></td>
	 <td align="center"><?=$res_sql->fields['dosis']?></td>
    </tr>	
	<?}
		$res_sql->MoveNext();
    
   }?>
	 </table>
<br>
	<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$count;?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table>
 <br>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

 case 'triple_viral': {

		$sql="SELECT distinct on (nacer.smiafiliados.afidni,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afifechanac
from trazadoras.vacunas 
inner join nacer.smiafiliados on vacunas.id_smiafiliados=smiafiliados.id_smiafiliados
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)

union

select distinct on (leche.beneficiarios.documento,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	leche.beneficiarios.documento,
	leche.beneficiarios.nombre,
	leche.beneficiarios.apellido,
	leche.beneficiarios.fecha_nac	
from trazadoras.vacunas 
inner join leche.beneficiarios on vacunas.id_beneficiarios=beneficiarios.id_beneficiarios
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">

 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id="mo">DNI</td>      	
    <td align=right id="mo">Nombre</td>   	
    <td align=right id="mo">Apellido</td>      	
    <td align=right id="mo">Fecha Nacimiento</td>      	
    <td align=right id="mo">Fecha Vacuna</td>      	
    <td align=right id="mo">Nombre</td>
    <td align=right id="mo">Dosis</td>
  </tr>
  <? $count=0;  
  while (!$res_sql->EOF) {

  		if ($res_sql->fields['id_vac_apli']==6) { $count++;?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['afifechanac']?></td>
	 <td><?=$res_sql->fields['fecha_vac']?></td>
	 <td><?=$res_sql->fields['nom_vacum']?></td>
	 <td align="center"><?=$res_sql->fields['dosis']?></td>
    </tr>	
	<?}
		$res_sql->MoveNext();
    
   }?>
	 </table>
<br>
	<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$count;?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table>
 <br>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

 case 'gripe': {

		$sql="SELECT distinct on (nacer.smiafiliados.afidni,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afifechanac
from trazadoras.vacunas 
inner join nacer.smiafiliados on vacunas.id_smiafiliados=smiafiliados.id_smiafiliados
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)

union

select distinct on (leche.beneficiarios.documento,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	leche.beneficiarios.documento,
	leche.beneficiarios.nombre,
	leche.beneficiarios.apellido,
	leche.beneficiarios.fecha_nac	
from trazadoras.vacunas 
inner join leche.beneficiarios on vacunas.id_beneficiarios=beneficiarios.id_beneficiarios
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">

 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id="mo">DNI</td>      	
    <td align=right id="mo">Nombre</td>   	
    <td align=right id="mo">Apellido</td>      	
    <td align=right id="mo">Fecha Nacimiento</td>      	
    <td align=right id="mo">Fecha Vacuna</td>      	
    <td align=right id="mo">Nombre</td>
    <td align=right id="mo">Dosis</td>
  </tr>
  <? $count=0;  
  while (!$res_sql->EOF) {

  		if ($res_sql->fields['id_vac_apli']==18 || $res_sql->fields['id_vac_apli']==19) { $count++;?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['afifechanac']?></td>
	 <td><?=$res_sql->fields['fecha_vac']?></td>
	 <td><?=$res_sql->fields['nom_vacum']?></td>
	 <td align="center"><?=$res_sql->fields['dosis']?></td>
    </tr>	
	<?}
		$res_sql->MoveNext();
    
   }?>
	 </table>
	<br>
	<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$count;?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table>
 <br>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

 case 'hep_a': {

		$sql="SELECT distinct on (nacer.smiafiliados.afidni,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afifechanac
from trazadoras.vacunas 
inner join nacer.smiafiliados on vacunas.id_smiafiliados=smiafiliados.id_smiafiliados
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)

union

select distinct on (leche.beneficiarios.documento,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	leche.beneficiarios.documento,
	leche.beneficiarios.nombre,
	leche.beneficiarios.apellido,
	leche.beneficiarios.fecha_nac	
from trazadoras.vacunas 
inner join leche.beneficiarios on vacunas.id_beneficiarios=beneficiarios.id_beneficiarios
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">

 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id="mo">DNI</td>      	
    <td align=right id="mo">Nombre</td>   	
    <td align=right id="mo">Apellido</td>      	
    <td align=right id="mo">Fecha Nacimiento</td>      	
    <td align=right id="mo">Fecha Vacuna</td>      	
    <td align=right id="mo">Nombre</td>
    <td align=right id="mo">Dosis</td>
  </tr>
  <? $count=0;  
  while (!$res_sql->EOF) {

  		if ($res_sql->fields['id_vac_apli']==7) { $count++;?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['afifechanac']?></td>
	 <td><?=$res_sql->fields['fecha_vac']?></td>
	 <td><?=$res_sql->fields['nom_vacum']?></td>
	 <td align="center"><?=$res_sql->fields['dosis']?></td>
    </tr>	
	<?}
		$res_sql->MoveNext();
    
   }?>
	 </table>
	<br>
	<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$count;?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table>
 <br>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

 case 'trip_celular': {

		$sql="SELECT distinct on (nacer.smiafiliados.afidni,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afifechanac
from trazadoras.vacunas 
inner join nacer.smiafiliados on vacunas.id_smiafiliados=smiafiliados.id_smiafiliados
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)

union

select distinct on (leche.beneficiarios.documento,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	leche.beneficiarios.documento,
	leche.beneficiarios.nombre,
	leche.beneficiarios.apellido,
	leche.beneficiarios.fecha_nac	
from trazadoras.vacunas 
inner join leche.beneficiarios on vacunas.id_beneficiarios=beneficiarios.id_beneficiarios
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
 
 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id="mo">DNI</td>      	
    <td align=right id="mo">Nombre</td>   	
    <td align=right id="mo">Apellido</td>      	
    <td align=right id="mo">Fecha Nacimiento</td>      	
    <td align=right id="mo">Fecha Vacuna</td>      	
    <td align=right id="mo">Nombre</td>
    <td align=right id="mo">Dosis</td>
  </tr>
  <? $count=0;  
  while (!$res_sql->EOF) {

  		if ($res_sql->fields['id_vac_apli']==8) { $count++;?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['afifechanac']?></td>
	 <td><?=$res_sql->fields['fecha_vac']?></td>
	 <td><?=$res_sql->fields['nom_vacum']?></td>
	 <td align="center"><?=$res_sql->fields['dosis']?></td>
    </tr>	
	<?}
		$res_sql->MoveNext();
    
   }?>
	 </table>
	<br>
	<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$count;?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table>
 <br>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

 case 'trip_acelular': {

		$sql="SELECT distinct on (nacer.smiafiliados.afidni,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afifechanac
from trazadoras.vacunas 
inner join nacer.smiafiliados on vacunas.id_smiafiliados=smiafiliados.id_smiafiliados
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)

union

select distinct on (leche.beneficiarios.documento,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	leche.beneficiarios.documento,
	leche.beneficiarios.nombre,
	leche.beneficiarios.apellido,
	leche.beneficiarios.fecha_nac	
from trazadoras.vacunas 
inner join leche.beneficiarios on vacunas.id_beneficiarios=beneficiarios.id_beneficiarios
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">
 
 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id="mo">DNI</td>      	
    <td align=right id="mo">Nombre</td>   	
    <td align=right id="mo">Apellido</td>      	
    <td align=right id="mo">Fecha Nacimiento</td>      	
    <td align=right id="mo">Fecha Vacuna</td>      	
    <td align=right id="mo">Nombre</td>
    <td align=right id="mo">Dosis</td>
  </tr>
  <? $count=0; 
  while (!$res_sql->EOF) {

  		if ($res_sql->fields['id_vac_apli']==9) { $count++;?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['afifechanac']?></td>
	 <td><?=$res_sql->fields['fecha_vac']?></td>
	 <td><?=$res_sql->fields['nom_vacum']?></td>
	 <td align="center"><?=$res_sql->fields['dosis']?></td>
    </tr>	
	<?}
		$res_sql->MoveNext();
    
   }?>
	 </table>
	<br>
	<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$count;?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table>
 <br>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

 case 'doble_bacteriana': {

		$sql="SELECT distinct on (nacer.smiafiliados.afidni,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afifechanac
from trazadoras.vacunas 
inner join nacer.smiafiliados on vacunas.id_smiafiliados=smiafiliados.id_smiafiliados
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)

union

select distinct on (leche.beneficiarios.documento,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	leche.beneficiarios.documento,
	leche.beneficiarios.nombre,
	leche.beneficiarios.apellido,
	leche.beneficiarios.fecha_nac	
from trazadoras.vacunas 
inner join leche.beneficiarios on vacunas.id_beneficiarios=beneficiarios.id_beneficiarios
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">

 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id="mo">DNI</td>      	
    <td align=right id="mo">Nombre</td>   	
    <td align=right id="mo">Apellido</td>      	
    <td align=right id="mo">Fecha Nacimiento</td>      	
    <td align=right id="mo">Fecha Vacuna</td>      	
    <td align=right id="mo">Nombre</td>
    <td align=right id="mo">Dosis</td>
  </tr>
  <? $count=0;
  while (!$res_sql->EOF) {

  		if ($res_sql->fields['id_vac_apli']==10) { $count++;?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['afifechanac']?></td>
	 <td><?=$res_sql->fields['fecha_vac']?></td>
	 <td><?=$res_sql->fields['nom_vacum']?></td>
	 <td align="center"><?=$res_sql->fields['dosis']?></td>
    </tr>	
	<?}
		$res_sql->MoveNext();
    
   }?>
	 </table>
	<br>
	<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$count;?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table>
 <br>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case

 
 case 'vph': {

		$sql="SELECT distinct on (nacer.smiafiliados.afidni,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	nacer.smiafiliados.afidni,
	nacer.smiafiliados.afinombre,
	nacer.smiafiliados.afiapellido,
	nacer.smiafiliados.afifechanac
from trazadoras.vacunas 
inner join nacer.smiafiliados on vacunas.id_smiafiliados=smiafiliados.id_smiafiliados
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)

union

select distinct on (leche.beneficiarios.documento,trazadoras.vacunas.id_vac_apli,trazadoras.vacunas.fecha_vac) 
	trazadoras.vacunas.id_vac_apli,
	trazadoras.vacunas.fecha_vac,
	trazadoras.vac_apli.nombre as nom_vacum,
	trazadoras.dosis_apli.nombre as dosis,
	leche.beneficiarios.documento,
	leche.beneficiarios.nombre,
	leche.beneficiarios.apellido,
	leche.beneficiarios.fecha_nac	
from trazadoras.vacunas 
inner join leche.beneficiarios on vacunas.id_beneficiarios=beneficiarios.id_beneficiarios
INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and cuie='$cuie' and (trazadoras.vacunas.eliminada=0)";

$res_sql=sql($sql) or die();

echo $html_header;
?>
<form name=form1 method=post action="datos_detalle_cumplimiento.php">

 <br>
 <table width="95%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="7"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id="mo">DNI</td>      	
    <td align=right id="mo">Nombre</td>   	
    <td align=right id="mo">Apellido</td>      	
    <td align=right id="mo">Fecha Nacimiento</td>      	
    <td align=right id="mo">Fecha Vacuna</td>      	
    <td align=right id="mo">Nombre</td>
    <td align=right id="mo">Dosis</td>
  </tr>
  <? $count=0; 
  while (!$res_sql->EOF) {

  		if ($res_sql->fields['id_vac_apli']==14) { $count++;?>
     <tr>     
     <td><?=$res_sql->fields['afidni']?></td>
	 <td><?=$res_sql->fields['afinombre']?></td>
	 <td><?=$res_sql->fields['afiapellido']?></td>
	 <td><?=$res_sql->fields['afifechanac']?></td>
	 <td><?=$res_sql->fields['fecha_vac']?></td>
	 <td><?=$res_sql->fields['nom_vacum']?></td>
	 <td align="center"><?=$res_sql->fields['dosis']?></td>
    </tr>	
	<?}
		$res_sql->MoveNext();
    
   }?>
	 </table>
<br>
	<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Inscriptos: <?=$count;?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table>
 <br>
 </form>
 <?=fin_pagina();// aca termino ?>
 <?break;}//del case
}//del swith?>