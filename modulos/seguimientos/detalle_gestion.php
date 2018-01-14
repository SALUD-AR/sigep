<?

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

function extrae_anio($fecha) {
        list($d,$m,$a) = explode("/",$fecha);
        //$a=$a+2000;
        return $a;
		}


$user=$_ses_user['login'];

if ($id_efe_conv) {
	$query ="SELECT 
	efe_conv.*,dpto.nombre as dpto_nombre
	FROM
	nacer.efe_conv 
	left join nacer.dpto on dpto.codigo=efe_conv.departamento   
	where id_efe_conv=$id_efe_conv";
	
	$res_factura=sql($query, "Error al traer el Efector") or fin_pagina();
	
	$cuie=$res_factura->fields['cuie'];
	$nombre=$res_factura->fields['nombre'];
	$domicilio=$res_factura->fields['domicilio'];
	$departamento=$res_factura->fields['dpto_nombre'];
	$localidad=$res_factura->fields['localidad'];
	$cod_pos=$res_factura->fields['cod_pos'];
	$cuidad=$res_factura->fields['cuidad'];
	$referente=$res_factura->fields['referente'];
	$tel=$res_factura->fields['tel'];
	}
else {

	$cuie=$_ses_user['login'];
	$sql_cuie="select * from nacer.efe_conv where cuie='$cuie'";
	$res_cuie= sql($sql_cuie, "Error al traer el Efector") or fin_pagina();
	$id_efe_conv=$res_cuie->fields['id_efe_conv'];
	}

if ($_POST['muestra']=="Muestra"){	
	
	$fecha_desde=fecha_db($_POST['fecha_desde']);
	$fecha_hasta=fecha_db($_POST['fecha_hasta']);
	
	$fecha_hoy=Date("d/m/Y");
	$anio_corr=extrae_anio($fecha_hoy);
	$fecha_desde_sem_1="$anio_corr"."-01-01";
	$fecha_hasta_sem_1="$anio_corr"."-06-30";
	
	$fecha_desde_sem_2="$anio_corr"."-07-01";
	$fecha_hasta_sem_2="$anio_corr"."-12-31";

//suatoria de los expedientes comprometidos

$sql_comp_sum="SELECT sum(monto_egre_comp) as total from (
SELECT 
  id_egreso,monto_egre_comp,fecha_egre_comp,comentario
FROM
  contabilidad.egreso  
  left join facturacion.servicio using (id_servicio) 
  left join contabilidad.inciso using (id_inciso) 
  where cuie='$cuie' and monto_egre_comp <> 0 and monto_egreso = 0 --and fecha_egre_comp between '$fecha_desde' and '$fecha_hasta'
  order by id_egreso DESC
  ) as sumas";
$sql_res_comp_sum=sql($sql_comp_sum,"No se puede traer los datos para la suma de los expedientes comprometidos");//
	
	
//contabilidad, saldos,ingresos,egresos,comprometidos
$sql="SELECT ingre-egre as total, ingre,egre,deve,egre_comp from
		(select sum (monto_deposito)as ingre from contabilidad.ingreso
		where cuie='$cuie') as ingreso,
		(select sum (monto_egreso)as egre from contabilidad.egreso
		where cuie='$cuie') as egreso,
		(select sum (monto_factura)as deve from contabilidad.ingreso
		where cuie='$cuie') as devengado,
		(select sum (monto_egre_comp)as egre_comp from contabilidad.egreso
		where cuie='$cuie') as egre_comp";

$res_saldo=sql($sql,"no puede calcular el saldo");
$total_depositado=number_format($res_saldo->fields['ingre'],2,',','.');
//$total_egre_comp=number_format($res_saldo->fields['egre_comp'],2,',','.');
$total_egre_comp=number_format($sql_res_comp_sum->fields['total'],2,',','.');
$total=number_format($res_saldo->fields['total'],2,',','.');
$ingreso=number_format($res_saldo->fields['ingre'],2,',','.');
$egreso=number_format($res_saldo->fields['egre'],2,',','.');
//$saldo_real=$total_depositado-$egreso-($total_egre_comp-$egreso);
$saldo_real = $res_saldo->fields['total']-$sql_res_comp_sum->fields['total'];
$saldo_real = number_format($saldo_real,2,',','.');
$saldo_p=$total_egre_comp-$egreso;
$saldo_p=number_format($saldo_p,2,',','.');

//$uso_f=($egreso/$ingreso)*100;
$uso_f=($res_saldo->fields['egre']/$res_saldo->fields['ingre'])*100;
$uso_de_fondos=number_format($uso_f,2,',','.');
$saldo_i=(100-$uso_f);
$saldo_inmovilizado=number_format($saldo_i,2,',','.');

//end contabilidad, saldos,ingresos,egresos,comprometidos

//facturas pagadas
$sql_fac="SELECT id_factura,fecha_ing,periodo,monto from expediente.expediente where estado='C' 
and id_efe_conv=(select id_efe_conv from nacer.efe_conv where cuie='$cuie' limit 1) order by fecha_ing";
$sql_fact=sql($sql_fac,"No se Puede abrir la base de datos de facturas");
//end facturas pagadas

//detalles incentivos
$no_cumle="SELECT id_factura,monto_factura,monto_incentivo from contabilidad.incentivo where cuie='$cuie' and cumple='0'";

$cumple="SELECT id_factura,monto_factura,monto_incentivo from contabilidad.incentivo where cuie='$cuie' and cumple='1'";

$pendientes="SELECT id_factura,monto_factura,monto_incentivo from contabilidad.incentivo where cuie='$cuie' and cumple='2'";

$parciales="SELECT id_factura,monto_factura,monto_incentivo from contabilidad.incentivo where cuie='$cuie' and cumple='3'";

$sql_incetivos_totales="SELECT cumple,sum(monto_factura),sum(monto_incentivo) from contabilidad.incentivo where cuie='$cuie'
group by cumple";

//end detalles incentivos

//codigos facturados por periodos
$sql_codigos="SELECT * from (
select id_nomenclador,count(id_nomenclador) as cantidad from (
select * from facturacion.comprobante where cuie='$cuie'  and fecha_comprobante between '$fecha_desde' and '$fecha_hasta') as comprobantes
inner join facturacion.prestacion using (id_comprobante) 
group by (id_nomenclador)
) as codigos_nomenclador
inner join (select id_nomenclador,codigo,grupo,subgrupo,descripcion from facturacion.nomenclador) as nomenclador using (id_nomenclador)
order by codigo";
$sql_cod=sql($sql_codigos,"No se puede abrir la base de datos de Codigos Facturados");


//end codigos facturados

//codigos de prestaciones NO facturados por el centro

$sql_prestaciones="SELECT codigo,grupo,subgrupo,descripcion from (
select * from (
select id_nomenclador from facturacion.nomenclador where id_nomenclador_detalle=11 order by id_nomenclador
) as nomenclador
except 

select id_nomenclador from (
select * from facturacion.comprobante where cuie='$cuie'  and fecha_comprobante between '$fecha_desde' and '$fecha_hasta') as comprobantes
inner join facturacion.prestacion using (id_comprobante) 
group by (id_nomenclador) order by id_nomenclador
) as codigos_no_facturados
inner join facturacion.nomenclador using (id_nomenclador) order by grupo";

$sql_pres=sql($sql_prestaciones,"No se pudo abrir la base de datos de prestaciones no facturadas por el centro");

//end codigos de prestaciones ...

$sql_1="SELECT sum (monto_egre_comp)as egre_incentivo
		from contabilidad.egreso
		where cuie='$cuie' and id_inciso=1";
$res_incentivo=sql($sql_1,"no puede calcular el saldo");
$total_incentivo=number_format($res_incentivo->fields['egre_incentivo'],2,',','.');	
				
//incentivos
$sql_inc="SELECT monto_egreso from (
select * from contabilidad.egreso where id_servicio=1 and id_inciso=1 
and comentario ilike '%Suma de Incentivo correspondiente en semestre%' and cuie='$cuie' ) as t1

where fecha_egreso = (select max (fecha_egreso) from (

select * from contabilidad.egreso where id_servicio=1 and id_inciso=1 
and comentario ilike '%Suma de Incentivo correspondiente en semestre%' and cuie='$cuie' ) as t2)";

$sql_res_inc=sql($sql_inc,"No se Puede calcular los montos de Incentivos");

$sql_acum_1="select sum(monto_incentivo) as total from contabilidad.incentivo where cuie='$cuie' and cumple='2' and fecha_prefactura between 
				'$fecha_desde_sem_1' and '$fecha_hasta_sem_1'";
$sql_res_acum_1=sql($sql_acum_1,"No se puede calcular el acumulado de incentivo");

$sql_acum_2="select sum(monto_incentivo) as total from contabilidad.incentivo where cuie='$cuie' and cumple='2' and fecha_prefactura between 
				'$fecha_desde_sem_2' and '$fecha_hasta_sem_2'";
$sql_res_acum_2=sql($sql_acum_2,"No se puede calcular el acumulado de incentivo");



//expedientes comprometidos

$sql_comp_sum="SELECT sum(monto_egre_comp) as total from (
SELECT 
  id_egreso,monto_egre_comp,fecha_egre_comp,comentario
FROM
  contabilidad.egreso  
  left join facturacion.servicio using (id_servicio) 
  left join contabilidad.inciso using (id_inciso) 
  where cuie='$cuie' and monto_egre_comp <> 0 and monto_egreso = 0 --and fecha_egre_comp between '$fecha_desde' and '$fecha_hasta'
  order by id_egreso DESC
  ) as sumas";
$sql_res_comp_sum=sql($sql_comp_sum,"No se puede traer los datos para la suma de los expedientes comprometidos");

$sql_comp="SELECT 
  id_egreso,monto_egre_comp,fecha_egre_comp,comentario
FROM
  contabilidad.egreso  
  left join facturacion.servicio using (id_servicio) 
  left join contabilidad.inciso using (id_inciso) 
  where cuie='$cuie' and monto_egre_comp <> 0 and monto_egreso = 0 --and fecha_egre_comp between '$fecha_desde' and '$fecha_hasta'
  order by id_egreso DESC";
$sql_res_comp=sql($sql_comp,"No se puede traer los datos de los expedientes comprometidos");

//Consultas redundantes - para poder medir la ceb nesecito los datos desde facturacion.
//Beneficiarios inscriptos por el efector
$sql_insc="SELECT afiapellido,afinombre,afidni,afifechanac,activo,cuieefectorasignado,cuielugaratencionhabitual,fechainscripcion,ceb,cuie_ceb,GrupoPoblacional
from nacer.smiafiliados where cuieefectorasignado='$cuie' and fechainscripcion between '$fecha_desde' and '$fecha_hasta'";

//afiliados con ceb por el centro
$sql_ceb="SELECT afiapellido,afinombre,afidni,afifechanac,activo,cuieefectorasignado,cuielugaratencionhabitual,fechainscripcion,ceb,cuie_ceb,GrupoPoblacional
from nacer.smiafiliados where cuie_ceb='$cuie' and fechainscripcion between '$fecha_desde' and '$fecha_hasta' and ceb='S'";
//end consultas redundantes

//consulta para sacar el ceb
$sql_ceb="SELECT grupo,count (*) as cantidad from (
SELECT distinct ON (cuie,afidni,afinombre,afiapellido,afifechanac)
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
		facturacion.comprobante.fecha_comprobante between '$fecha_desde' and '$fecha_hasta'
		order by nacer.smiafiliados.afidni
	) as ccc 
) as ttt
group by grupo
order by grupo";

$sql_res_ceb=sql($sql_ceb,"No se pudo calcular el ceb del efector");

$sql_res_ceb->movefirst();
while (!$sql_res_ceb->EOF) {
$grupo=$sql_res_ceb->fields['grupo'];
switch ($grupo) {
case 'A' : $ceb_a=($sql_res_ceb->fields['cantidad'])?$sql_res_ceb->fields['cantidad']:0;break;//de 0 a 5
case 'B' : $ceb_b=($sql_res_ceb->fields['cantidad'])?$sql_res_ceb->fields['cantidad']:0;break;//de 6 a 9
case 'C' : $ceb_c=($sql_res_ceb->fields['cantidad'])?$sql_res_ceb->fields['cantidad']:0;break;//adolescentes
case 'D' : $ceb_d=($sql_res_ceb->fields['cantidad'])?$sql_res_ceb->fields['cantidad']:0;break;//mujeres
default : break;
	}
$sql_res_ceb->movenext();
}

//metas CEB del efector
$sql_metas="select * from nacer.metas where cuie='$cuie'";
$sql_res_metas=sql($sql_metas,"No se pudo traer los datos de metas del efector");

//metas segun RRHH
$sql_metasrrhh="select * from nacer.metasrrhh where cuie='$cuie'";
$sql_res_metasrrhh=sql($sql_metasrrhh,"No se pudo traer los datos de metas del efector");



}

if ($id_efe_conv) {
$query="SELECT 
  efe_conv.*,dpto.nombre as dpto_nombre
FROM
  nacer.efe_conv 
  left join nacer.dpto on dpto.codigo=efe_conv.departamento   
  where id_efe_conv=$id_efe_conv";

$res_factura=sql($query, "Error al traer el Efector") or fin_pagina();

$cuie=$res_factura->fields['cuie'];
$nombre=$res_factura->fields['nombre'];
$domicilio=$res_factura->fields['domicilio'];
$departamento=$res_factura->fields['dpto_nombre'];
$localidad=$res_factura->fields['localidad'];
$cod_pos=$res_factura->fields['cod_pos'];
$cuidad=$res_factura->fields['cuidad'];
$referente=$res_factura->fields['referente'];
$tel=$res_factura->fields['tel'];

}

echo $html_header;
?>
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
 if(document.all.fecha_hasta.value<document.all.fecha_desde.value){
  alert('La Fecha HASTA debe ser MAYOR 0 IGUAL a la Fecha DESDE');
  return false;
 }
 if(document.all.fecha_desde.value.indexOf("-")!=-1){
	  alert('Debe ingresar un fecha en el campo DESDE');
	  return false;
	 }
if(document.all.fecha_hasta.value.indexOf("-")!=-1){
	  alert('Debe ingresar una fecha en el campo HASTA');
	  return false;
	 }
return true;
}

var img_ext='<?=$img_ext='f_right1.png' ?>';//imagen extendido
var img_cont='<?=$img_cont='f_down1.png' ?>';//imagen contraido

function muestra_tabla(obj_tabla,nro){
	 oimg=eval("document.all.imagen_"+nro);//objeto tipo IMG
	 if (obj_tabla.style.display=='none'){
	 	obj_tabla.style.display='inline';
	    oimg.show=0;
	    oimg.src=img_ext;
	 }
	 else{
	 	obj_tabla.style.display='none';
	    oimg.show=1;
		oimg.src=img_cont;
	 }
	}
</script>

<form name='form1' action='detalle_gestion.php' method='POST'>
<input type="hidden" value="<?=$id_efe_conv?>" name="id_efe_conv">
<input type="hidden" value="<?=$cuie?>" name="cuie">

<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<b>	
		<?if ($fecha_desde=='') $fecha_desde=DATE ('d/m/Y');
		if ($fecha_hasta=='') $fecha_hasta=DATE ('d/m/Y');?>		
		Desde: <input type=text id=fecha_desde name=fecha_desde value='<?=$fecha_desde?>' size=15 readonly>
		<?=link_calendario("fecha_desde");?>
		
		Hasta: <input type=text id=fecha_hasta name=fecha_hasta value='<?=$fecha_hasta?>' size=15 readonly>
		<?=link_calendario("fecha_hasta");?> 
		
		   
	    
	    &nbsp;&nbsp;&nbsp;
	    <input type="submit" name="muestra" value='Muestra' onclick="return control_muestra()" >
	    </b>
	    
	    &nbsp;&nbsp;&nbsp;	    
        <?if ($_POST['muestra']){
         	
        $link=encode_link("efec_cumplimiento_pdf.php",array("id_efe_conv"=>$id_efe_conv,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));?>
        <!--<img src="../../imagenes/pdf_logo.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">-->
        <?}?>
	  </td>
       
     </tr>
     
    
     
</table>
<table width="98%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<font size=+1><b>Efector: <?echo $cuie.". Desde: ".fecha($fecha_desde)." Hasta: ".fecha($fecha_hasta)?> </b></font>        
    </td>
 </tr>
 <tr><td>
  <table width=100% align="center" class="bordes">
     <tr>
      <td id=mo colspan="5">
       <b> Descripcion del Efector</b>
      </td>
     </tr>
     <tr>
       <td>
        <table align="center">
                
         <td align="right">
				<b>Nombre:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$nombre?>" name="nombre" readonly>
            </td>
         </tr>
         
         <tr>	           
           
         <tr>
         <td align="right">
				<b>Domicilio:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$domicilio?>" name="domicilio" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Departamento:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$departamento?>" name="departamento" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Localidad:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$localidad?>" name="localidad" readonly>
            </td>
         </tr>
        </table>
      </td>      
      <td>
        <table align="center">        
         <tr>
         <td align="right">
				<b>Codigo Postal:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$cod_pos?>" name="cod_pos" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Cuidad:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$cuidad?>" name="cuidad" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Referente:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$referente?>" name="referente" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Telefono:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$tel?>" name="tel" readonly>
            </td>
         </tr>          
        </table>
      </td>  
       
     </tr> 
           
 </table>           

<?if ($_POST['muestra']){?>
<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
		<tr align="center" id="sub_tabla">
		 	
</table>
<table>
	<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
		 <tr align="center" id="sub_tabla">
		 	<td colspan=10>	
		 	<font size=4 >Evaluacion de Gestion <BR> </font>
		 	</td>
		 </tr>
		    <tr>
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=3 color= red><b>Uso de Fondos</b> </font>
			     </td>   
				  
				 <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Ingreso en Pesos: </br><b><?=($ingreso)?$ingreso:0?> </b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Egreso en Pesos: </br><b><?=($egreso)?$egreso:0?></b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Uso de Fondos (%): </br><b><?=($uso_de_fondos)?$uso_de_fondos:0?></b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Saldo Inmovilizado (%): </br><b><?=($saldo_inmovilizado)?$saldo_inmovilizado:0?></b></font>
			      </td>
			</tr>
		</table>
		<br/>
		<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
			
			<tr>
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=3 color= red> <b>Dinero Disponible  </b></font>
			      </td>   
				  
				 <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Saldo en Pesos (Ingreso - Egreso): <br/><b><?=($total)?$total:0?> </b> </font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Total Expedientes Comprometidos: <br/><b><?=($total_egre_comp)?$total_egre_comp:0?> </b> </font>
			      </td>
				  
				  <?if ($saldo_real<0) $atrib=atrib_tr5();
					else $atrib=atrib_tr8();?>
				  <td align="center"  border=1 bordercolor=#2C1701 <?=$atrib?>>
					<font size=2>Saldo Real: <br/><b><?=($saldo_real)?$saldo_real:0?> </b> </font>
			      </td>
			</tr>
			</table>
			
			<br/>
			<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
			<tr>	  
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=3 color= red><b>Pago de Incentivos</b> </font>
			     </td>   
				  
				 <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Ultimo Pago: </br><b><?=($sql_res_inc->fields['monto_egreso'])?number_format($sql_res_inc->fields['monto_egreso'],2,',','.'):0?> </b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Acumulado 1° Semeste (Sujeto a cumpl. de Metas): </br><b><?=($sql_res_acum_1->fields['total'])?number_format($sql_res_acum_1->fields['total'],2,',','.'):0?> </b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Acumulado 2° Semeste (Sujeto a cumpl. de Metas): </br><b><?=($sql_res_acum_2->fields['total'])?number_format($sql_res_acum_2->fields['total'],2,',','.'):0?></b></font>
			      </td>
			</tr>
			</table>
			<!--
			<br/>
			<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
			<tr>
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=3 color= red><b>Poblacion Objetivo</b> </font>
			     </td>   
				  
				 <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Niños de 0 a 5 Año: </br><b><?=$sql_res_metas->fields['ceb_ceroacinco']?></b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Niños de 6 a 9 años: </br><b><?=$sql_res_metas->fields['ceb_seisanueve']?></b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Adolescentes (10 a 19 años): </br><b><?=$sql_res_metas->fields['ceb_diezadiecinueve']?></b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Mujeres (20 a 64 años): </br><b><?=$sql_res_metas->fields['ceb_veinteasesentaycuatro']?></b></font>
			      </td>
			</tr>
			</table>
			
			<br/>
			<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
			<tr>
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=3 color= red><b>Poblacion Objetivo segun RRHH</b> </font>
			     </td>   
				  
				 <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Niños de 0 a 5 Año: </br><b><?=$sql_res_metasrrhh->fields['ceb_ceroacinco']?></b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Niños de 6 a 9 años: </br><b><?=$sql_res_metasrrhh->fields['ceb_seisanueve']?></b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Adolescentes (10 a 19 años): </br><b><?=$sql_res_metasrrhh->fields['ceb_diezadiecinueve']?></b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Mujeres (20 a 64 años): </br><b><?=$sql_res_metasrrhh->fields['ceb_veinteasesentaycuatro']?></b></font>
			      </td>
			</tr>
			</table>
			-->
			<br/>
			<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
			<tr>
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=3 color= red><b>Poblacion Inscripta al Programa </b> </font>
			     </td>   
				 	  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Poblacion Objetivo</br></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Poblacio Objetivo x RRHH</br></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>CEB</br></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2>Porcentaje</br></font>
			      </td>
			</tr>
			
			<tr>
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=2 color= red><b>Ni&ntilde;os de 0 a 5 A&ntilde;o</b> </font>
			     </td>
				   
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=2><b><?=$sql_res_metas->fields['ceb_ceroacinco']?$sql_res_metas->fields['ceb_ceroacinco']:0?> </b></font>
			    </td>
				
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=2><b><?=($sql_res_metasrrhh->fields['ceb_ceroacinco'])?$sql_res_metasrrhh->fields['ceb_ceroacinco']:0?> </b></font>
			    </td>
				
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=2><b><?=$ceb_a?> </b></font>
			    </td>
				  
				   <td align="center"  border=1 <?if ($sql_res_metas->fields['ceb_ceroacinco']) {
				   $met1=($ceb_a*100)/$sql_res_metas->fields['ceb_ceroacinco'];}
				   else $met1=0?>bordercolor=#2C1701 <?=atrib_tr7()?>>
				   
					<font size=2><b><?=number_format($met1,2,',','.');?>% </b></font>
			      </td>
				  </tr>
			<tr>
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=2 color= red><b>Ni&ntilde;os de 6 a 9 A&ntilde;o</b> </font>
			     </td>
				 <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=2><b><?=($sql_res_metas->fields['ceb_seisanueve'])?$sql_res_metas->fields['ceb_seisanueve']:0?> </b></font>
			     </td>
				 
				 <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=2><b><?=($sql_res_metasrrhh->fields['ceb_seisanueve'])?$sql_res_metasrrhh->fields['ceb_seisanueve']:0?> </b></font>
			     </td>
				 
				 <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=2><b><?=$ceb_b?> </b></font>
			     </td>			 
				 
				 <?if ($sql_res_metas->fields['ceb_seisanueve']) {$met1=($ceb_b*100)/$sql_res_metas->fields['ceb_seisanueve'];}
				 else $met1=0?>
				 <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=number_format($met1,2,',','.')?>% </b></font>
			     </td>
				 </tr>
			<tr>
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=2 color= red><b>Adolescentes (10 a 19 a&ntilde;os)</b> </font>
			     </td>
				  
				   <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=($sql_res_metas->fields['ceb_diezadiecinueve'])?$sql_res_metas->fields['ceb_diezadiecinueve']:0?> </b></font>
			        </td>
					
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=($sql_res_metasrrhh->fields['ceb_diezadiecinueve'])?$sql_res_metasrrhh->fields['ceb_diezadiecinueve']:0?> </b></font>
			        </td>
					
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=$ceb_c?> </b></font>
			        </td>
					
				  <?if ($sql_res_metas->fields['ceb_diezadiecinueve']) {$met1=($ceb_c*100)/$sql_res_metas->fields['ceb_diezadiecinueve'];}
				  else $met1=0?>
				   <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=number_format($met1,2,',','.')?>% </b></font>
			      </td>
				  </tr>
			<tr>
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=2 color= red><b>Mujeres (20 a 64 a&ntilde;os)</b> </font>
			     </td>
				 			  
				   <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=($sql_res_metas->fields['ceb_veinteasesentaycuatro'])?$sql_res_metas->fields['ceb_veinteasesentaycuatro']:0?> </b></font>
					</td>
					
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=($sql_res_metasrrhh->fields['ceb_veinteasesentaycuatro'])?$sql_res_metasrrhh->fields['ceb_veinteasesentaycuatro']:0?> </b></font>
					</td>
					
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=$ceb_d?> </b></font>
					</td>
					
			      <?if ($sql_res_metas->fields['ceb_veinteasesentaycuatro']) {
				  $met1=($ceb_d*100)/$sql_res_metas->fields['ceb_veinteasesentaycuatro'];}
				  else $met1=0?>
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=number_format($met1,2,',','.')?>% </b></font>
			      </td>
				  </tr>
	</table>
	
	<br/>
	<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
			<tr align="center" id="sub_tabla">
		 	<td colspan=10>	
		 	<img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.facturas,2);" >
			<font size=3 color= red> <b>Facturas pagas en el Periodo Fijado </b></font>
		 	</td>
				
	<tr><td><table id="facturas" width=90% align="center" class="bordes" style="display:none;border:thin groove">
			
			<tr>
				 <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2> <b>N° de Factura </b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2> <b>Fecha de Ingreso </b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2> <b>Periodo </b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2> <b>Saldo </b></font>
			      </td>
			</tr>	
			<tr>
				<? $sql_fact->MoveFirst();				
					while (!$sql_fact->EOF)
					{?>
					<tr>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>><font size=2> <?=$sql_fact->fields['id_factura']?></font></td>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>><font size=2> <?=$sql_fact->fields['fecha_ing']?></font></td>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>><font size=2> <?=$periodo=$sql_fact->fields['periodo']?></font></td>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>><font size=2> <?=$sql_fact->fields['monto']?></font></td>
					</tr>
					<?$sql_fact->MoveNext();
					}?>
			</tr>  
			<tr>
			</td></tr></table>
	</table>	  
				  
	<br/>
	<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
	<tr align="center" id="sub_tabla">
	<td colspan=10>
	<img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.codigos,2);" >
	<font size=3 color= red> <b>Codigos Facturados por Periodo Fijado</b></font>
	</td>
			
	<tr><td><table id="codigos" width=90% align="center" class="bordes" style="display:none;border:thin groove">
				 <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2> <b>Codigos Facturados </b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2> <b>Grupo </b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2> <b>Subgrupo </b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2> <b>Descripcion </b></font>
			      </td>
				  
				 <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=2> <b>Cantidad </b></font>
			     </td>
			</tr>
			<tr>
				<? $sql_cod->MoveFirst();
					while (!$sql_cod->EOF)
					{?>
					<tr>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr5()?>><font size=2> <?=$sql_cod->fields['codigo']?></font></td>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr5()?>><font size=2> <?=$sql_cod->fields['grupo']?></font></td>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr5()?>><font size=2> <?=$sql_cod->fields['subgrupo']?></font></td>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr5()?>><font size=2> <?=$sql_cod->fields['descripcion']?></font></td>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr5()?>><font size=2> <?=$sql_cod->fields['cantidad']?></font></td>
					</tr>
					<?$sql_cod->MoveNext();
					}?>
			</tr>
		</td></tr></table>
		</table>
		
	<br/>
	<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
			<tr align="center" id="sub_tabla">
		 	<td colspan=10>	
			<img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.prestaciones,2);" >
		 	<font size=3 color= red> <b>Codigos NO Facturados</b></font>
		 	</td>
			
			<tr><td><table id="prestaciones" width=90% align="center" class="bordes" style="display:none;border:thin groove">
				 <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2> <b>Codigos</b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2> <b>Grupo </b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2> <b>Subgrupo </b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2> <b>Descripcion </b></font>
			      </td>
				 </tr>
			<tr>
				<? $sql_pres->MoveFirst();
					while (!$sql_pres->EOF)
					{?>
					<tr>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr4()?>><font size=2> <?=$sql_pres->fields['codigo']?></font></td>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr4()?>><font size=2> <?=$sql_pres->fields['grupo']?></font></td>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr4()?>><font size=2> <?=$sql_pres->fields['subgrupo']?></font></td>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr4()?>><font size=2> <?=$sql_pres->fields['descripcion']?></font></td>
					</tr>
					<?$sql_pres->MoveNext();
					}?>
			</tr>
			</td></tr></table>
		</table>
		
	<br/>
	<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
			<tr align="center" id="sub_tabla">
		 	<td colspan=10>	
			<img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.expedientes,2);" >
		 	<font size=3 color= red> <b>Expedientes Comprometidos</b></font>
		 	</td>
			
			<tr><td><table id="expedientes" width=90% align="center" class="bordes" style="display:none;border:thin groove">
				 <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2> <b>N° Expediente</b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2> <b>Monto</b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2> <b>Fecha</b></font>
			      </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2> <b>Descripcion</b></font>
			      </td>
				  </tr>
			<tr>
				<? $sql_res_comp->MoveFirst();
					while (!$sql_res_comp->EOF)
					{?>
					<tr>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>><font size=2> <?=$sql_res_comp->fields['id_egreso']?></font></td>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>><font size=2> <b><?=number_format($sql_res_comp->fields['monto_egre_comp'],2,',','.')?></b></font></td>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>><font size=2> <?=$sql_res_comp->fields['fecha_egre_comp']?></font></td>
					<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>><font size=1> <?=$sql_res_comp->fields['comentario']?></font></td>
					</tr>
					<?$sql_res_comp->MoveNext();
					}?>
			</tr>
			</td></tr></table>
		</table>
		
<br/>
	<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
			<tr align="center" id="sub_tabla">
		 	<td colspan=10>	
		 	<img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.priorizado,2);" >
			<font size=3 color= red> <b>Informacion Priorizada </b></font>
		 	</td>
				
	<tr><td><table id="priorizado" width=90% align="center" class="bordes" style="display:none;border:thin groove">
			
			<tr>
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=2> <b>Concepto</b></font>
			    </td>
				  
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=2> <b>Numerador</b></font>
			    </td>

				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=2> <b>Denominador</b></font>
				</td>

				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
				<font size=2> <b>Valor</b></font>
			    </td>				
			</tr>
			
			
			<tr>
				<td align="left"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?> title="El indicador mide el porcentaje de beneficiarios a cargo del efector con Cobertura Efectiva Básica (CEB)">
				<font size=2> <b>1.1 - TASA DE COBERTURA EFECTIVA B&Aacute;SICA </b></font>
			    </td>
				<?
				$numerador=$ceb_a+$ceb_b+$ceb_c+$ceb_d;
					
				$sql_ex="SELECT count(*) as cantidad from 
					(SELECT distinct id_smiafiliados 
					from nacer.smiafiliados 
					where  cuieefectorasignado='$cuie' or cuielugaratencionhabitual='$cuie'
					) as cantidad";
				$res_sql_ex=sql($sql_ex,"No se puede calcular el denominador de CEB");
				$denominador=($res_sql_ex->fields['cantidad'])?$res_sql_ex->fields['cantidad']:0;


				//$denominador=$sql_res_metas->fields['ceb_ceroacinco']+$sql_res_metas->fields['ceb_seisanueve']+$sql_res_metas->fields['ceb_diezadiecinueve']+$sql_res_metas->fields['ceb_veinteasesentaycuatro'];
				if ($denominador)
				  {$ceb_total=($numerador/$denominador)*100;}
				  else $ceb_total=0?> 
				
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=$numerador?> </b></font>
			     </td>
				 
				 <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=$denominador?> </b></font>
			     </td>				
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=number_format($ceb_total,2,',','.')?> %</b></font>
			     </td>
			</tr>

			<tr>
				<td align="left"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?> title="El indicador mide el porcentaje de niños de 6 a 9 años de edad beneficiarios a cargo del efector con Cobertura Efectiva Básica (CEB)">
				<font size=2> <b>1.2 - TASA DE COBERTURA EFECTIVA B&Aacute;SICA EN NI&Ntilde;OS DE 6-9 A&Ntilde;OS </b></font>
			      </td>
				<?
				$numerador=$ceb_b;
				
				$sql_ex="SELECT count(*) as cantidad from  (
					select distinct id_smiafiliados 
					from nacer.smiafiliados 
					where (cuieefectorasignado='$cuie' or cuielugaratencionhabitual='$cuie' 
						or cuie_ceb='$cuie') and grupopoblacional='B'
					) as cantidad";
				$res_sql_ex=sql($sql_ex,"No se puede calcular el denominador de la tasa de cobertura");
				$denominador=($res_sql_ex->fields['cantidad'])?$res_sql_ex->fields['cantidad']:0;

				if ($denominador) {$ceb_seis_a_nueve=($numerador/$denominador)*100;
				  } else $ceb_seis_a_nueve=0;?>  
				
				<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=$numerador?> </b></font>
			     </td>
				 
				 <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=$denominador?> </b></font>
			     </td>
				  
				  <td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=number_format($ceb_seis_a_nueve,2,',','.')?>% </b></font>
			     </td>
			</tr>
			
			<tr>
				<td align="left"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?> title="El indicador mide el cumplimiento de las actividades extramuro esperadas por cada efector">
				<font size=2> <b>1.3 - NIVEL ACTIVIDADES EXTRAMURO </b></font>
			      </td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b>-----</b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b>-----</b></font>
			</td>
				
			
			
			<?$sql_ex="SELECT count (*) as cantidad from (
						select id_comprobante from facturacion.prestacion 
						where id_nomenclador=1671 or id_nomenclador=2195 or id_nomenclador=1338 or id_nomenclador=1337) as t1
						left join facturacion.comprobante using (id_comprobante)
						where cuie='$cuie'";
			$res_sql_ex=sql($sql_ex,"No se puede calcula Nivel actividad extramuro");?>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=($res_sql_ex->fields['cantidad'])?$res_sql_ex->fields['cantidad']:0?></b></font>
			</td>
			</tr>
			<tr>
				<td align="left"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?> title="El indicador mide el cumplimiento de las búsquedas activas de adolescentes para valoración integral y de embarazadas adolescentes por agente sanitario y/o personal de salud esperadas por cada efector">
				<font size=2> <b>1.4 - NIVEL DE BUSQUEDA ACTIVA DEL ADOLESCENTE </b></font>
			      </td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b>-----</b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b>-----</b></font>
			</td>
			
			<?$sql_ex="SELECT count (*) as cantidad from (
						select id_comprobante from facturacion.prestacion 
						where id_nomenclador=1988 or id_nomenclador=2081 or id_nomenclador=1257 or
						id_nomenclador=1989 or id_nomenclador=2080 or id_nomenclador=1260) as t1
						left join facturacion.comprobante using (id_comprobante)
						where cuie='$cuie'";
			$res_sql_ex=sql($sql_ex,"No se puede calculas busqueda activa en adolescentes");?>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=($res_sql_ex->fields['cantidad'])?$res_sql_ex->fields['cantidad']:0?></b></font>
			</td>
			
			</tr>
			
			<br/>
			<tr>
				<td align="left"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?> title="El indicador mide el nivel de notificación de embarazos de alto riesgo a cargo del efector">
				<font size=2> <b>2.1 - TASA DE DETECCION DE FACTORES DE RIESGO EN EMBARAZO </b></font>
			      </td>
			
			<?$sql_ex="SELECT count (*) as cantidad from (
						select distinct id_smiafiliados from facturacion.comprobante 
						inner join facturacion.prestacion on comprobante.id_comprobante=prestacion.id_comprobante
						where (prestacion.id_nomenclador=1658 or prestacion.id_nomenclador=2418 or prestacion.id_nomenclador=1647)
						and comprobante.cuie='$cuie'
						) as cantidad";
			$res_sql_ex=sql($sql_ex,"No se puede calcular factores de riesgo en embarazadas");
			$numerador=$res_sql_ex->fields['cantidad'];
			
			$sql_emb="SELECT count (*) as cantidad from (
						select distinct afidni from nacer.smiafiliados 
						where (cuieefectorasignado='$cuie' or cuielugaratencionhabitual='$cuie' or cuie_ceb='$cuie')
							and afitipocategoria=1
						) as cantidad";
			$sql_res_emb=sql($sql_emb,"no se pueden traer el denominador de embarazadas");
			
			
			$denominador=$sql_res_emb->fields['cantidad'];
			if ($denominador) {$res_emb=($numerador/$denominador)*100;}
			else $res_emb=0;
			?>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b><?=$numerador?></b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b><?=$denominador?></b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b><?=number_format($res_emb,2,',','.')?></b></font>
			</td>
			
			</tr>
			
			<tr>
				<td align="left"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?> title="El indicador mide el nivel de Búsqueda activa de embarazadas en el primer trimestre por agente sanitario  y/o personal de salud">
				<font size=2> <b>2.2 - NIVEL DE B&Uacute;SQUEDA ACTIVA DE EMBARAZADAS </b></font>
			      </td>
			<?$sql_ex="SELECT count (*) as cantidad from (
						select distinct comprobante.id_smiafiliados
						from facturacion.comprobante
						inner join facturacion.prestacion on comprobante.id_comprobante=prestacion.id_comprobante
						where (prestacion.id_nomenclador=1983 or prestacion.id_nomenclador=2076 or prestacion.id_nomenclador=1261)
						and comprobante.cuie='$cuie'
						) as cantidad";
			$res_sql_ex=sql($sql_ex,"No se puede calcular busqueda activa en embarazadas");?>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b>-----</b></font>
			</td>
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b>-----</b></font>
			</td>
						
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b><?=($res_sql_ex->fields['cantidad'])?$res_sql_ex->fields['cantidad']:0?></b></font>
			</td>
			
			</tr>
			
			<tr>
				<td align="left"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?> title="El indicador mide el nivel de embarazadas de alto riesgo a cargo del efector que son trasladadas en forma oportuna a un mayor nivel de complejidad para realización de parto/cesárea">
				<font size=2> <b>2.3 - TASA DE TRASLADO INTRA-&Uacute;TERO </b></font>
			      </td>
			<!-- NO FIGURAN LAS PRACTICAS EN EL NOMENCLADOR-->
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b>-----</b></font>
			</td>
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b>-----</b></font>
			</td>
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b>0</b></font>
			</td>
			
			</tr>
		
			<tr>
				<td align="left"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?> title="El indicador mide el nivel de Referencia oportuna por embarazo de alto riesgo a cargo del efector">
				<font size=2> <b>2.4 - NIVEL DE REFERENCIA OPORTUNA </b></font>
			      </td>
			
			<? $sql_ex="SELECT count (*) as cantidad from (
						select distinct comprobante.id_smiafiliados
						from facturacion.comprobante
						inner join facturacion.prestacion on comprobante.id_comprobante=prestacion.id_comprobante
						where prestacion.id_nomenclador=2419 
						and comprobante.cuie='$cuie'
						) as cantidad";
				$res_sql_ex=sql($sql_ex,"No se encuntran los datos para refer.Oportuna");
				$dato=$res_sql_ex->fields['cantidad'];
				?>

			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b>-----</b></font>
			</td>
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b>-----</b></font>
			</td>
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b><?=$dato?></b></font>
			</td>
			
			</tr>
			
			<tr>
				<td align="left"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?> title="El indicador mide la cobertura del seguimiento de salud de los adolescentes beneficiarios a cargo del efector (trazadora X post-auditoría)">
				<font size=2> <b>3.1 - SEGUIMIENTO DE SALUD DEL ADOLESCENTE </b></font>
			      </td>
			<?/*$sql_ex="SELECT count (*) as cantidad from nacer.smiafiliados where cuieefectorasignado='$cuie' 
						and (fechainscripcion::date-afifechanac) between 3650 and 6935";*/

			
			//Adolescentes desde trazadoras.nino_new
			$sql_ex="SELECT count(*) as cantidad from (
			SELECT distinct on (nacer.smiafiliados.afiapellido, 
		nacer.smiafiliados.afinombre,ccc.num_doc)

	nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	--nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	ccc.cuie,
	(ccc.num_doc::numeric(30,0))::text as afidni,
	ccc.fecha_control,
	trim (both '0000' from ccc.peso::text) as peso ,
	round (ccc.talla) as talla,
	ccc.percen_peso_edad,
	ccc.percen_talla_edad,
	ccc.percen_perim_cefali_edad,
	ccc.percen_peso_talla,
	ccc.ta
	from nacer.smiafiliados
	inner join (select *,(trazadoras.nino_new.num_doc::numeric(30,0))::text as afidni from trazadoras.nino_new) as ccc
	on nacer.smiafiliados.afidni=ccc.afidni
	--filtro para adolescentes entre 10 y 19
	where  (nacer.smiafiliados.fechainscripcion between '$fecha_desde' and '$fecha_hasta') and 
	((nacer.smiafiliados.fechainscripcion::date-nacer.smiafiliados.afifechanac) between 3650 and 6935) 
	and nacer.smiafiliados.cuie_ceb='$cuie') as cantidad";


	$res_sql_ex=sql($sql_ex,"No se puede calcular seguimiento del adolescente");
	$numerador=$res_sql_ex->fields['cantidad'];

	//adolescentes desde trazadorassps.trazadora_10
	$sql_ex="SELECT count(*) as cantidad from (
		SELECT distinct on (nacer.smiafiliados.afiapellido, 
		nacer.smiafiliados.afinombre,nacer.smiafiliados.afidni)

	nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	trazadorassps.trazadora_10.cuie, 
	trazadorassps.trazadora_10.fecha_nac, 
	trazadorassps.trazadora_10.fecha_control, 
	trazadorassps.trazadora_10.peso, 
	case when trazadorassps.trazadora_10.talla<=2 then round (trazadorassps.trazadora_10.talla*100) 
	else round(trazadorassps.trazadora_10.talla) end as talla,
	trazadorassps.trazadora_10.cuie
	from nacer.smiafiliados
	inner join trazadorassps.trazadora_10 on nacer.smiafiliados.id_smiafiliados=trazadorassps.trazadora_10.id_smiafiliados
	--filtro para adolescentes entre 10 y 19
	where  (nacer.smiafiliados.fechainscripcion between '$fecha_desde' and '$fecha_hasta') and 
	((nacer.smiafiliados.fechainscripcion::date-nacer.smiafiliados.afifechanac) between 3650 and 6935) 
	and nacer.smiafiliados.cuie_ceb='$cuie') as cantidad";


	$res_sql_ex=sql($sql_ex,"No se puede calcular seguimiento del adolescente");
	$numerador+=$res_sql_ex->fields['cantidad'];

	//Adolescentes desde fichero.fichero
	$sql_ex="SELECT count (*) as cantidad from (
	SELECT distinct on (nacer.smiafiliados.afiapellido,nacer.smiafiliados.afinombre,nacer.smiafiliados.afidni)

	nacer.smiafiliados.clavebeneficiario, 
	nacer.smiafiliados.afiapellido, 
	nacer.smiafiliados.afinombre, 
	nacer.smiafiliados.afitipodoc, 
	nacer.smiafiliados.aficlasedoc::character(1), 
	nacer.smiafiliados.afidni, 
	nacer.smiafiliados.afisexo::character(1),
	nacer.smiafiliados.afifechanac,
	fichero.fichero.cuie,
	fichero.fichero.fecha_control,
	trim (both '0000' from (fichero.fichero.peso::text)) as peso ,
	round (fichero.fichero.talla) as talla,
	fichero.fichero.percen_peso_talla
	from nacer.smiafiliados
	inner join fichero.fichero on nacer.smiafiliados.id_smiafiliados=fichero.fichero.id_smiafiliados
	--filtro para adolescentes entre 10 y 19
	where  (nacer.smiafiliados.fechainscripcion between '$fecha_desde' and '$fecha_hasta') and 
	((nacer.smiafiliados.fechainscripcion::date-nacer.smiafiliados.afifechanac) between 3650 and 6935) 
	and nacer.smiafiliados.cuie_ceb='$cuie') as cantidad";


	$res_sql_ex=sql($sql_ex,"No se puede calcular seguimiento del adolescente");
	$numerador+=$res_sql_ex->fields['cantidad'];

	/*$sql_ex="SELECT count (*) as cantidad from nacer.smiafiliados where cuie_ceb='$cuie' 
						and fechainscripcion between '$fecha_desde' and '$fecha_hasta'
						and (fechainscripcion::date-afifechanac) between 3650 and 6935";
	$res_sql_ex=sql($sql_ex,"No se puede calcular DEenominador de seguimiento del adolescente");
	$denominador=$res_sql_ex->fields['cantidad'];*/

	//$denominador=$sql_res_metas->fields['ceb_diezadiecinueve'];
	$denominador=$ceb_c;
	if ($denominador) {$res_adol=($numerador/$denominador)*100;}
	else $res_adol=0;?>
			
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=$numerador?></b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=$denominador?></b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=number_format($res_adol,2,',','.')?></b></font>
			</td>
			
			</tr>
			
			<tr>
				<td align="left"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?> title="El indicador mide la cobertura de la promoción de derechos y cuidados en salud sexual y reproductiva de los beneficiarios a cargo del efector (trazadora XI post-auditoría)">
				<font size=2> <b>3.2 - PROMOCION DE DERECHOS Y CUIDADOS EN SALUD SEXUAL Y REPRODUCTIVA </b></font>
			      </td>
			
			<?/*$sql_ex="SELECT count (*) as cantidad from nacer.smiafiliados where cuieefectorasignado='$cuie' 
						and (((fechainscripcion::date-afifechanac) between 3650 and 6935)
						or (afisexo='F' and (fechainscripcion::date-afifechanac) between 7300 and 23360))";*/

			$sql_ex="SELECT count (*) as cantidad from (
										select * from 
						(select distinct id_smiafiliados from nacer.smiafiliados
					where --(nacer.smiafiliados.fechainscripcion between '2014-01-01' and '2014-12-31') and 
								--filtro adolescentes entre 10 y 19 años y mujeres de 20 a 64 años
					(((nacer.smiafiliados.".'"FechaUtimaPrestacion"'."-nacer.smiafiliados.afifechanac) between 3650 and 6935) or
					--filtro mujeres entre 20 y 24 años
					(((nacer.smiafiliados.".'"FechaUtimaPrestacion"'."-nacer.smiafiliados.afifechanac) between 7300 and 8760) and 
										 nacer.smiafiliados.afisexo='F'))
										 and (cuie_ceb='$cuie' or cuieefectorasignado='$cuie' or cuielugaratencionhabitual='$cuie')) as smiafiliados
											
								inner join (
									
								SELECT distinct comprobante.id_smiafiliados from facturacion.prestacion
								inner join facturacion.comprobante on comprobante.id_comprobante=prestacion.id_comprobante
								where (prestacion.id_nomenclador=2040 or prestacion.id_nomenclador=1821 or prestacion.id_nomenclador=2469 or prestacion.id_nomenclador=2470 --TA T007
										or prestacion.id_nomenclador=2011 or prestacion.id_nomenclador=2004 or prestacion.id_nomenclador=2471 or prestacion.id_nomenclador=2472 --TA T008
										or prestacion.id_nomenclador=1667 or prestacion.id_nomenclador=1980 or prestacion.id_nomenclador=2458 --TA T001
										or prestacion.id_nomenclador=2020 or prestacion.id_nomenclador=2046 or prestacion.id_nomenclador=2477 or prestacion.id_nomenclador=2479 or prestacion.id_nomenclador=2478 --TA T011
										or prestacion.id_nomenclador=2021 or prestacion.id_nomenclador=2043 or prestacion.id_nomenclador=2482 or prestacion.id_nomenclador=2483 --TA T013
										or prestacion.id_nomenclador=2006 or prestacion.id_nomenclador=2484) --TA T014
										and comprobante.cuie='$cuie'
								) as prestaciones on smiafiliados.id_smiafiliados=prestaciones.id_smiafiliados
								) as cantidad";

	$res_sql_ex=sql($sql_ex,"No se puede calcular numerador cuidado sexual y reproductivo");
	$numerador=$res_sql_ex->fields['cantidad'];
			
	$sql_ex="SELECT count (*) as cantidad from (
		select distinct id_smiafiliados from nacer.smiafiliados
		where --(nacer.smiafiliados.fechainscripcion between '$fecha_desde' and '$fecha_hasta') and 
		--filtro adolescentes entre 10 y 19 años y mujeres de 20 a 24 años
		(((nacer.smiafiliados.".'"FechaUtimaPrestacion"'."-nacer.smiafiliados.afifechanac) between 3650 and 6935) or
		--filtro mujeres entre 20 y 24 años
		(((nacer.smiafiliados.".'"FechaUtimaPrestacion"'."-nacer.smiafiliados.afifechanac) between 7300 and 8760) and 
		 nacer.smiafiliados.afisexo='F'))
		 and (cuie_ceb='$cuie' or cuieefectorasignado='$cuie' or cuielugaratencionhabitual='$cuie')
		) as cantidad";

		$res_sql_ex=sql($sql_ex,"No se puede calcular denominador cuidado sexual y reproductivo");
		$denominador=$res_sql_ex->fields['cantidad'];
		//$denominador=$sql_res_metas->fields['ceb_diezadiecinueve']+$sql_res_metas->fields['ceb_veinteasesentaycuatro'];
		
			if ($denominador) {$res_ssr=($numerador/$denominador)*100;}
			else $res_ssr=0;
			?>
						
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=$numerador?></b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=$denominador?></b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=number_format ($res_ssr,2,',','.')?></b></font>
			</td>
			
			</tr>
			
			<tr>
				<td align="left"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?> title="El indicador mide la tasa de cobertura del control odontologico en adolescentes beneficiarios a cargo del efector ">
				<font size=2> <b>3.3 - TASA DE COBERTURA DE CONTROL ODONTOL&Oacute;GICO </b></font>
			      </td>
			<?$sql_ex="SELECT count (*) as cantidad from (
						select distinct comprobante.id_smiafiliados
					from facturacion.prestacion 
					inner join facturacion.comprobante on comprobante.id_comprobante=prestacion.id_comprobante
					where (prestacion.id_nomenclador=2044 or prestacion.id_nomenclador=2163) and comprobante.cuie='$cuie'
					and comprobante.fecha_comprobante between '$fecha_desde' and '$fecha_hasta'
				) as cantidad";

			$res_sql_ex=sql($sql_ex,"No se puede calcular cobertura de control odontologico");
			$numerador=$res_sql_ex->fields['cantidad'];
			
			/*$sql_ex="SELECT * from nacer.smiafiliados where cuieefectorasignado='$cuie' 
					and ((fechainscripcion::date-afifechanac) between 3650 and 6650)";

			$res_sql_ex=sql($sql_ex,"No se puede calcular la cantidad de inscripto de 11 años");*/
			
			//$denominador=$sql_res_metas->fields['ceb_diezadiecinueve'];
			$denominador=$ceb_c;
			if($denominador) {$res_odon=($numerador/$denominador)*100;}
			else $res_odon=0;
			
			?>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=$numerador?></b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=$denominador?></b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=number_format($res_odon,2,',','.')?></b></font>
			</td>
			
			</tr>
			
			<tr>
				<td align="left"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?> title="El indicador mide la tasa de cobertura de inmunizaciones de VPH con 3 dosis aplicadas a adolescentes beneficiarias de 11 años a cargo del efector">
				<font size=2> <b>3.4 - TASA DE COBERTURA DE INMUNIZACIONES DE VPH </b></font>
			      </td>
			
			<?$sql_ex="SELECT count (*) as cantidad from (
				select * from (
SELECT id_smiafiliados from nacer.smiafiliados where cuieefectorasignado='$cuie' 
					and ((fechainscripcion::date-afifechanac) between 4015 and 4380)) as smiafiliados
inner join (
	select comprobante.id_comprobante,
		comprobante.id_smiafiliados 
		from facturacion.prestacion 

		inner join facturacion.comprobante on comprobante.id_comprobante=prestacion.id_comprobante
		where (prestacion.id_nomenclador=1819 or prestacion.id_nomenclador=2260) and comprobante.cuie='$cuie'
	) as prestaciones on smiafiliados.id_smiafiliados=prestaciones.id_smiafiliados
) as cantidad";

			$res_sql_ex=sql($sql_ex,"No se puede calcular cobertura de inmunizaciones de vph");
			$numerador=($res_sql_ex->fields['cantidad'])?$res_sql_ex->fields['cantidad']:0;
			//hay que analizar la consulta del numerador
			
			$sql_ex="SELECT count(*) as cantidad from (
			SELECT * from nacer.smiafiliados where cuieefectorasignado='$cuie' 
					and ((fechainscripcion::date-afifechanac) between 4015 and 4380)) as cantidad";

			$res_sql_ex=sql($sql_ex,"No se puede calcular la cantidad de inscripto de 11 años");
			
			$denominador=($res_sql_ex->fields['cantidad'])?$res_sql_ex->fields['cantidad']:0;
			
			if ($denominador) {$res_vph=($numerador/$denominador)*100;}
			else $res_vph=0;
			?>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=$numerador?></b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=$denominador?></b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr7()?>>
					<font size=2><b><?=number_format($res_vph,2,',','.')?></b></font>
			</td>
			
			</tr>
			
			<tr>
				<td align="left"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?> title="El indicador mide la tasa de cobertura de mamografías en mujeres beneficiarias a cargo del efector en su zona de influencia">
				<font size=2> <b>4.1 - TASA DE COBERTURA DE MAMOGRAF&Iacute;AS</b></font>
			      </td>
			
			<?$sql_ex="SELECT count (*) as cantidad from (
						select distinct comprobante.id_smiafiliados
						from facturacion.comprobante
						inner join facturacion.prestacion on comprobante.id_comprobante=prestacion.id_comprobante
						where (prestacion.id_nomenclador=1345 or prestacion.id_nomenclador=1770 or prestacion.id_nomenclador=2209)
						and comprobante.cuie='$cuie'
						) as cantidad";

			$res_sql_ex=sql($sql_ex,"No se puede calcular mamografias");
			$numerador=$res_sql_ex->fields['cantidad'];
			
			$sql_ex1="SELECT count (*) as cantidad from (
					select distinct id_smiafiliados from nacer.smiafiliados 
					where (cuieefectorasignado='$cuie' or cuielugaratencionhabitual='$cuie') and 
					afisexo='F' and (fechainscripcion::date-afifechanac) between 17885 and 23360
					) as cantidad";

			$res_sql_ex1=sql($sql_ex1,"No se puede calcular cantidad de mujeres entre 49 y 64");
			
			$denominador=($res_sql_ex1->fields['cantidad'])?$res_sql_ex1->fields['cantidad']:0;
			
			if ($denominador<>0) {$result_ex=$res_sql_ex->fields['cantidad']/$denominador;}
			else $result_ex=0;
			?>
			
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b><?=$numerador?></b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b><?=$denominador?></b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b><?=$result_ex?>%</b></font>
			</td>
			</tr>
			<tr>
				<td align="left"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?> title="El indicador mide la tasa de cobertura de lecturas de PAP en mujeres beneficiarias a cargo del efector en su zona de influencia">
				<font size=2> <b>4.2 - TASA DE COBERTURA DE TAMIZAJE C&Aacute;NCER C&Eacute;RVICOUTERINO </b></font>
			      </td>
			<?$sql_ex="SELECT count (*) as cantidad from (
					(select distinct id_smiafiliados from nacer.smiafiliados 
					where (cuieefectorasignado='$cuie' or cuielugaratencionhabitual='$cuie') and 
					afisexo='F' and (fechainscripcion::date-afifechanac) between 17885 and 23360
					) as smiafiliados

					inner join (
						select distinct comprobante.id_smiafiliados
						from facturacion.comprobante
						inner join facturacion.prestacion on comprobante.id_comprobante=prestacion.id_comprobante
						where (prestacion.id_nomenclador=1247 or prestacion.id_nomenclador=1248 or prestacion.id_nomenclador=1985
						or prestacion.id_nomenclador=2070 or prestacion.id_nomenclador=1760 or prestacion.id_nomenclador=2069)
						and (prestacion.diagnostico='A98' or prestacion.diagnostico='X86' or prestacion.diagnostico='X75')
						and comprobante.cuie='$cuie' and (prestacion.fecha_prestacion between '$fecha_hasta'::date -730 and '$fecha_desde')
					) as prestaciones on smiafiliados.id_smiafiliados=prestaciones.id_smiafiliados
				) as cantidad";

			$res_sql_ex=sql($sql_ex,"No se puede calcular numerador del Tamizaje");
			$numerador=$res_sql_ex->fields['cantidad'];
			
			$sql_ex1="SELECT count (*) as cantidad from (
					select distinct id_smiafiliados from nacer.smiafiliados 
					where (cuieefectorasignado='$cuie' or cuielugaratencionhabitual='$cuie') and 
					afisexo='F' and (fechainscripcion::date-afifechanac) between 17885 and 23360
					) as cantidad";

			$res_sql_ex1=sql($sql_ex1,"No se puede calcular cantidad de mujeres entre 49 y 64");
			
			$denominador=($res_sql_ex1->fields['cantidad'])?$res_sql_ex1->fields['cantidad']:0;
			
			if ($denominador<>0) {$result_ex=($numerador/$denominador)*100;}
			else $result_ex=0;
			?>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b><?=$numerador?></b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b><?=$denominador?></b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b><?=number_format($result_ex,2,',','.')?></b></font>
			</td>
			</tr>
			
			<tr>
				<td align="left"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?> title="El indicador mide la tasa de cobertura de lecturas de biopsias en mujeres beneficiarias a cargo del efector en su zona de influencia">
				<font size=2> <b>4.3 - TASA DE LECTURA POR BIOPSIA </b></font>
			      </td>
			
			<?$sql_ex="SELECT count (*) as cantidad from (
						select distinct comprobante.id_smiafiliados
						from facturacion.comprobante
						inner join facturacion.prestacion on comprobante.id_comprobante=prestacion.id_comprobante
						where (prestacion.id_nomenclador=1992 or prestacion.id_nomenclador=1249 or prestacion.id_nomenclador=1250
						or prestacion.id_nomenclador=1993 or prestacion.id_nomenclador=2071 or prestacion.id_nomenclador=2072)
						and comprobante.cuie='$cuie' 
						) as cantidad";

			$res_sql_ex=sql($sql_ex,"No se puede calcular laboratorio");
			$numerador=$res_sql_ex->fields['cantidad'];
			
			$sql_ex="SELECT count (*) as cantidad from (
			select distinct id_smiafiliados from trazadorassps.trazadora_12 where cuie = '$cuie' and id_smiafiliados<>0
			) as cantidad";
			
			$res_sql_ex=sql($sql_ex,"No se pueden traer los datos de trazadora 12");
			$denominador=$res_sql_ex->fields['cantidad'];
			
			if ($denominador<>0) {$result_ex=($numerador/$denominador)*100;}
			else $result_ex=0;
			?>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b><?=$numerador?></b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b><?=$denominador?></b></font>
			</td>
			
			<td align="center"  border=1 bordercolor=#2C1701 <?=atrib_tr6()?>>
					<font size=2><b><?=number_format($result_ex,2,',','.')?></b></font>
			</td>
			</tr>
			
			<tr>
			</td></tr></table>
	</table>
<?}?>
<BR>
 <tr><td><table width=90% align="center" class="bordes">
  <tr align="center">
  <?
   if (!es_cuie($user)){ ?>
		<td>
     	<input type=button name="volver" value="Volver" onclick="document.location='seguimiento.php'"title="Volver al Listado" style="width=150px">     
   		</td>
   <?} 
   else {?>

   		<td>
     	<input type=button name="volver" value="Volver" onclick="document.location='efectores_detalle.php'"title="Volver al Listado" style="width=150px">     
   		</td>
  <?}?>

  </tr>
 </table></td></tr>
 
  </table>
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>
