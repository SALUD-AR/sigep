<?
require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['guardar_editar']=="Guardar"){
   $db->StartTrans();
      
   $fecha_alta=Fecha_db($fecha_alta);   
   $fecha_parto=Fecha_db($fecha_parto);  
   $fecha_turno=Fecha_db($fecha_turno);  
    
   if ($llena_epi=='on')$llena_epi='SI';
   else $llena_epi='NO';
   if ($carnet_parenteral=='on')$carnet_parenteral='SI';
   else $carnet_parenteral='NO';
   if ($pes_neonatal=='on')$pes_neonatal='SI';
   else $pes_neonatal='NO';
   if ($vac_hep_b=='on')$vac_hep_b='SI';
   else $vac_hep_b='NO';
   if ($vac_bcg=='on')$vac_bcg='SI';
   else $vac_bcg='NO';
   if ($gamma_anti_rh=='on')$gamma_anti_rh='SI';
   else $gamma_anti_rh='NO';
    if ($control_postparto=='on') $control_postparto='SI';
    ELSE  $control_postparto='NO';
   
   $query="update alta.alta set
             	cuie='$cuie',
             	cuie_at_hab='$cuie_at_hab',
  				fecha_alta='$fecha_alta',
  				fecha_parto='$fecha_parto',
  				nom_madre='$nom_madre',
  				doc_madre='$doc_madre',
  				nom_bebe='$nom_bebe',
  				domicilio='$domicilio',
  				rep_obstetricia='$rep_obstetricia',
  				rep_neo='$rep_neo',
  				rep_enf='$rep_enf',
  				llena_epi='$llena_epi',
  				carnet_parenteral='$carnet_parenteral',
  				peso_nacer='$peso_nacer',
  				riesgo_social='$riesgo_social',
  				sifilis='$sifilis',
  				hiv='$hiv',
  				hep_b='$hep_b',
  				chagas='$chagas',
  				toxo='$toxo',
  				pes_neonatal='$pes_neonatal',
  				vac_hep_b='$vac_hep_b',
  				vac_bcg='$vac_bcg',
  				grupo_factor_mama='$grupo_factor_mama',
  				grupo_factor_bebe='$grupo_factor_bebe',
  				gamma_anti_rh='$gamma_anti_rh',
  				observaciones='$observaciones',
  				pueri='$pueri',
  				alarma_bebe='$alarma_bebe',
  				alarma_madre='$alarma_madre',
  				lac_materna='$lac_materna',
  				salud_repro='$salud_repro',
  				cuidados_puerpe='$cuidados_puerpe', 
  				fecha_turno='$fecha_turno',
  				hora_turno='$hora_turno',
  				control_postparto='$control_postparto'
             
             where id_alta=$id_planilla";

   sql($query, "Error al insertar/actualizar el muleto") or fin_pagina();
  
    $db->CompleteTrans();    
    $accion="Los datos se actualizaron";    
}

if ($_POST['guardar']=="Guardar Planilla"){   
   $db->StartTrans();         
    
   $q="select nextval('alta.alta_id_alta_seq') as id_planilla";
    $id_planilla=sql($q) or fin_pagina();
    $id_planilla=$id_planilla->fields['id_planilla'];
   
   $fecha_alta=Fecha_db($fecha_alta);   
   $fecha_parto=Fecha_db($fecha_parto);  
   $fecha_turno=Fecha_db($fecha_turno);  
    
   if ($llena_epi=='on')$llena_epi='SI';
   else $llena_epi='NO';
   if ($carnet_parenteral=='on')$carnet_parenteral='SI';
   else $carnet_parenteral='NO';
   if ($pes_neonatal=='on')$pes_neonatal='SI';
   else $pes_neonatal='NO';
   if ($vac_hep_b=='on')$vac_hep_b='SI';
   else $vac_hep_b='NO';
   if ($vac_bcg=='on')$vac_bcg='SI';
   else $vac_bcg='NO';
   if ($gamma_anti_rh=='on')$gamma_anti_rh='SI';
   else $gamma_anti_rh='NO';
   if ($control_postparto!='SI') $control_postparto='NO';      
    $query="insert into alta.alta
               (id_alta,
  				cuie,
  				cuie_at_hab,
  				fecha_alta,
  				fecha_parto,
  				nom_madre,
  				doc_madre,
  				nom_bebe,
  				domicilio,
  				rep_obstetricia,
  				rep_neo,
  				rep_enf,
  				llena_epi,
  				carnet_parenteral,
  				peso_nacer,
  				riesgo_social,
  				sifilis,
  				hiv,
  				hep_b,
  				chagas,
  				toxo,
  				pes_neonatal,
  				vac_hep_b,
  				vac_bcg,
  				grupo_factor_mama,
  				grupo_factor_bebe,
  				gamma_anti_rh,
  				observaciones,
  				pueri,
  				alarma_bebe,
  				alarma_madre,
  				lac_materna,
  				salud_repro,
  				cuidados_puerpe,
  				cuie_ef_der,
  				fecha_turno,
  				hora_turno,
  				control_postparto
  				)
             values
              ('$id_planilla',
  				'$cuie',
  				'$cuie_at_hab',
  				'$fecha_alta',
  				'$fecha_parto',
  				'$nom_madre',
  				'$doc_madre',
  				'$nom_bebe',
  				'$domicilio',
  				'$rep_obstetricia',
  				'$rep_neo',
  				'$rep_enf',
  				'$llena_epi',
  				'$carnet_parenteral',
  				'$peso_nacer',
  				'$riesgo_social',
  				'$sifilis',
  				'$hiv',
  				'$hep_b',
  				'$chagas',
  				'$toxo',
  				'$pes_neonatal',
  				'$vac_hep_b',
  				'$vac_bcg',
  				'$grupo_factor_mama',
  				'$grupo_factor_bebe',
  				'$gamma_anti_rh',
  				'$observaciones',
  				'$pueri',
  				'$alarma_bebe',
  				'$alarma_madre',
  				'$lac_materna',
  				'$salud_repro',
  				'$cuidados_puerpe',
  				'$cuie_ef_der',
  				'$fecha_turno',
  				'$hora_turno',
  				'$control_postparto')";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
    
    $accion="Se guardo la Alta";  
    
//MAIL    
date_default_timezone_set('Europe/Madrid');
setlocale(LC_TIME, 'spanish');
$dia_hoy=strftime("%A %d de %B de %Y");
	
$ret .= "<table width='65%'  bgcolor='$color1' align='center' style='border: 2px solid #000000; font-size=14px;'>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='center'>\n";
$ret .= "<b>NOTIFICACION ALTA CONJUNTA</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1' align='right'>\n";
$ret .= "<td align='rigth'>\n";
$ret .= "<b>Plan Nacer, $dia_hoy</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1' align='left'>\n";
$ret .= "<td align='left'>\n";
$sql_nombre_efector_alta= "select * from nacer.efe_conv where cuie='$cuie'";
$res_nombre_efector_alta=sql ($sql_nombre_efector_alta) or die;
$nombre_efector_alta=$res_nombre_efector_alta->fields['nombre'];
$ret .= "<b>Efector que dio el ALTA CONJUNTA: $nombre_efector_alta. CUIE: $cuie. 
Fecha de Parto: $fecha_parto. Fecha de Alta: $fecha_alta. RIESGO SOCIAL: $riesgo_social.</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<b>Por medio de la presente le notifico que se registro un alta conjunta en el hospital de referencia,
por este medio le comunico que $nom_madre cuyo DNI es $doc_madre tiene un turno en el CAPS a su cargo 
el dia $fecha_turno a las $hora_turno Horas. </b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<b>Asimismo informo a Usted que dicha Alta CONJUNTA se encuentra registrada en el sistema de Plan Nacer en el menu
Alta Conjunta > Alta Conjunta  (puede buscar el registro a travez del DNI)</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "</table><br>\n";
    
$ret .= "<table width=95% align=center style='font-size=10px'>\n";
$ret .= "<tr>\n";
$ret .= "<td align=center>\n";
$ret .= "<b> NOTIFICACIONES\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "</table>\n";	
$ret .= "<table width='65%'  bgcolor='$color1' align='center' style='border: 2px solid #000000; font-size=14px;'>\n";
$ret .= "<tr bgcolor='$color1' align='left'>\n";
$ret .= "<td align='rigth'>\n";
$ret .= "<b>Queda Notificado Equipo de Plan Nacer a través del mail oficial.</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1' align='left'>\n";
$ret .= "<td align='left'>\n";
$sql_nombre_efector_alta= "select * from nacer.efe_conv where cuie='$cuie_ef_der'";
$res_nombre_efector_alta=sql ($sql_nombre_efector_alta) or die;
$nombre_efector_alta=$res_nombre_efector_alta->fields['nombre'];
$ret .= "<b>Queda Notificado el Efector: $nombre_efector_alta. CUIE: $cuie_ef_der. A través de los mail declarados.</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$sql= "select * from nacer.mail_efe_conv where cuie='$cuie_ef_der'";
$res_mail=sql($sql,"no se puede ejecutar");
$res_mail->movefirst();
while (!$res_mail->EOF) { 
	$para=$res_mail->fields['mail'];
	$ret .= "<tr bgcolor='$color1' align='left'>\n";
	$ret .= "<td align='left'>\n";
	$ret .= "<b>Mail: $para.</b>\n";
	$ret .= "</td>\n";
	$ret .= "</tr>\n";
	$res_mail->movenext();
}
$ret .= "</table>\n";

echo $ret;
	
	$res_mail->movefirst();
	while (!$res_mail->EOF) { 
	$para=$res_mail->fields['mail'];
	enviar_mail_html($para,'Notificacion ALTA CONJUNTA',$ret,0,0,0);
	enviar_mail_html($para,'Notificacion ALTA CONJUNTA',$ret,0,0,0);
	$res_mail->movenext();
	}
	enviar_mail_html('plan.nacersl@gmail.com','Notificacion ALTA CONJUNTA',$ret,0,0,0);	
	enviar_mail_html('fernandonu@gmail.com','Notificacion ALTA CONJUNTA',$ret,0,0,0);
	 
    $db->CompleteTrans();    
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

if ($_POST['borrar']=="Borrar"){
	$query="delete from alta.alta
			where id_alta=$id_planilla";
	sql($query, "Error al insertar la Planilla") or fin_pagina();
	$accion="Se elimino el Alta $id_planilla"; 	
}

if ($id_planilla) {
$query="SELECT 
  *
FROM
  alta.alta 
  where id_alta=$id_planilla";

$res_factura=sql($query, "Error al traer el Comprobantes") or fin_pagina();

$cuie=$res_factura->fields['cuie'];
$cuie_at_hab=$res_factura->fields['cuie_at_hab'];
$fecha_alta=$res_factura->fields['fecha_alta'];
$fecha_parto=$res_factura->fields['fecha_parto'];
$nom_madre=$res_factura->fields['nom_madre'];
$doc_madre=$res_factura->fields['doc_madre'];
$nom_bebe=$res_factura->fields['nom_bebe'];
$domicilio=$res_factura->fields['domicilio'];
$rep_obstetricia=$res_factura->fields['rep_obstetricia'];
$rep_neo=$res_factura->fields['rep_neo'];
$rep_enf=$res_factura->fields['rep_enf'];
$llena_epi=$res_factura->fields['llena_epi'];
$carnet_parenteral=$res_factura->fields['carnet_parenteral'];
$peso_nacer=number_format($res_factura->fields['peso_nacer'],3,'.','');
$riesgo_social=$res_factura->fields['riesgo_social'];
$sifilis=$res_factura->fields['sifilis'];
$hiv=$res_factura->fields['hiv'];
$hep_b=$res_factura->fields['hep_b'];
$chagas=$res_factura->fields['chagas'];
$toxo=$res_factura->fields['toxo'];
$pes_neonatal=$res_factura->fields['pes_neonatal'];
$vac_hep_b=$res_factura->fields['vac_hep_b'];
$vac_bcg=$res_factura->fields['vac_bcg'];
$grupo_factor_mama=$res_factura->fields['grupo_factor_mama'];
$grupo_factor_bebe=$res_factura->fields['grupo_factor_bebe'];
$gamma_anti_rh=$res_factura->fields['gamma_anti_rh'];
$observaciones=$res_factura->fields['observaciones'];   
$pueri=$res_factura->fields['pueri'];   
$alarma_bebe=$res_factura->fields['alarma_bebe'];   
$alarma_madre=$res_factura->fields['alarma_madre'];   
$lac_materna=$res_factura->fields['lac_materna'];   
$salud_repro=$res_factura->fields['salud_repro'];   
$cuidados_puerpe=$res_factura->fields['cuidados_puerpe'];   
$cuie_ef_der=$res_factura->fields['cuie_ef_der'];   
$fecha_turno=$res_factura->fields['fecha_turno'];   
$hora_turno=$res_factura->fields['hora_turno'];  
$control_postparto=trim($res_factura->fields['control_postparto']);
}
echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{ 
 if(document.all.cuie.value=="-1"){
  alert('Debe Seleccionar un Efector');
  return false;
 } 
 if(document.all.cuie_at_hab.value=="-1"){
  alert('Debe Seleccionar un Efector Atencion Habitual');
  return false;
 } 
 if(document.all.fecha_alta.value==""){
  alert('Debe Ingresar una Fecha de alta');
  return false;
 }
 if(document.all.fecha_parto.value==""){
  alert('Debe Ingresar una Fecha de parto');
  return false;
 }
 if(document.all.nom_madre.value==""){
  alert('Debe Ingresar un nombre de madre');
  return false;
 }
 if(document.all.doc_madre.value==""){
  alert('Debe Ingresar un documento de madre');
  return false;
 }
 if(document.all.nom_bebe.value==""){
  alert('Debe Ingresar un nombre de bebe');
  return false;
 }
 if(document.all.domicilio.value==""){
  alert('Debe Ingresar un domicilio');
  return false;
 }
 if(document.all.rep_obstetricia.value==""){
  alert('Debe Ingresar un Responsable de Obstetricia');
  return false;
 }
 if(document.all.rep_neo.value==""){
  alert('Debe Ingresar un Responsable de Neo');
  return false;
 }
 if(document.all.rep_enf.value==""){
  alert('Debe Ingresar un Responsable de Enfermeria');
  return false;
 }
 if(document.all.peso_nacer.value==""){
  alert('Debe Ingresar un Peso al nacer');
  return false;
 }
 if(document.all.riesgo_social.value=="-1"){
  alert('Debe Seleccionar un Riesgo Social');
  return false;
 } 
 if(document.all.sifilis.value=="-1"){
  alert('Debe Seleccionar sifilis');
  return false;
 } 
 if(document.all.hiv.value=="-1"){
  alert('Debe Seleccionar hiv');
  return false;
 } 
 if(document.all.hep_b.value=="-1"){
  alert('Debe Seleccionar hep_b');
  return false;
 } 
 if(document.all.chagas.value=="-1"){
  alert('Debe Seleccionar chagas');
  return false;
 } 
 if(document.all.toxo.value=="-1"){
  alert('Debe Seleccionar toxo');
  return false;
 } 
 if(document.all.grupo_factor_mama.value=="-1"){
  alert('Debe Seleccionar grupo_factor_mama');
  return false;
 } 
 if(document.all.grupo_factor_bebe.value=="-1"){
  alert('Debe Seleccionar grupo_factor_bebe');
  return false;
 } 
 if(document.all.pueri.value=="-1"){
  alert('Debe Seleccionar puericultura');
  return false;
 }
 if(document.all.alarma_bebe.value=="-1"){
  alert('Debe Seleccionar Alarma Bebe');
  return false;
 } 
 if(document.all.alarma_madre.value=="-1"){
  alert('Debe Seleccionar alarma_madre');
  return false;
 } 
 if(document.all.lac_materna.value=="-1"){
  alert('Debe Seleccionar Lactancia Materna');
  return false;
 } 
 if(document.all.salud_repro.value=="-1"){
  alert('Debe Seleccionar Salud Reproductiva');
  return false;
 } 
 if(document.all.cuidados_puerpe.value=="-1"){
  alert('Debe Seleccionar cuidados puerperio');
  return false;
 }
 if(document.all.cuie_ef_der.value=="-1"){
  alert('Debe Seleccionar un Efector Derivado');
  return false;
 } 
 if(document.all.fecha_turno.value==""){
  alert('Debe Ingresar una Fecha de Turno');
  return false;
 } 
  if(document.all.hora_turno.value==""){
  alert('Debe Ingresar una Hora de Turno');
  return false;
 } 
}//de function control_nuevos()

function editar_campos()
{	
	document.all.cuie.disabled=false;
	document.all.cuie_at_hab.disabled=false;
	document.all.nom_madre.readOnly=false;
	document.all.doc_madre.readOnly=false;
	document.all.nom_bebe.readOnly=false;
	document.all.domicilio.readOnly=false;
	document.all.rep_obstetricia.readOnly=false;
	document.all.rep_neo.readOnly=false;
	document.all.rep_enf.readOnly=false;
	document.all.peso_nacer.readOnly=false;
	document.all.riesgo_social.disabled=false;
	document.all.sifilis.disabled=false;
	document.all.hiv.disabled=false;
	document.all.hep_b.disabled=false;
	document.all.chagas.disabled=false;
	document.all.toxo.disabled=false;
	document.all.grupo_factor_mama.disabled=false;
	document.all.grupo_factor_bebe.disabled=false;
	document.all.gamma_anti_rh.disabled=false;
  	document.all.observaciones.readOnly=false;
  	document.all.pueri.disabled=false;
  	document.all.alarma_bebe.disabled=false;
  	document.all.alarma_madre.disabled=false;
  	document.all.lac_materna.disabled=false;
  	document.all.salud_repro.disabled=false;
  	document.all.cuidados_puerpe.disabled=false;
   	document.all.control_postparto.disabled=false
   	
	document.all.cancelar_editar.disabled=false;
	document.all.guardar_editar.disabled=false;
	document.all.editar.disabled=true;
 	return true;
}//de function control_nuevos()

/**********************************************************/
//funciones para busqueda abreviada utilizando teclas en la lista que muestra los clientes.
var digitos=10; //cantidad de digitos buscados
var puntero=0;
var buffer=new Array(digitos); //declaración del array Buffer
var cadena="";

function buscar_combo(obj)
{
   var letra = String.fromCharCode(event.keyCode)
   if(puntero >= digitos)
   {
       cadena="";
       puntero=0;
   }   
   //sino busco la cadena tipeada dentro del combo...
   else
   {
       buffer[puntero]=letra;
       //guardo en la posicion puntero la letra tipeada
       cadena=cadena+buffer[puntero]; //armo una cadena con los datos que van ingresando al array
       puntero++;

       //barro todas las opciones que contiene el combo y las comparo la cadena...
       //en el indice cero la opcion no es valida
       for (var opcombo=1;opcombo < obj.length;opcombo++){
          if(obj[opcombo].text.substr(0,puntero).toLowerCase()==cadena.toLowerCase()){
          obj.selectedIndex=opcombo;break;
          }
       }
    }//del else de if (event.keyCode == 13)
   event.returnValue = false; //invalida la acción de pulsado de tecla para evitar busqueda del primer caracter
}//de function buscar_op_submit(obj)
</script>

<form name='form1' action='alta_admin.php' method='POST'>
<input type="hidden" value="<?=$id_planilla?>" name="id_planilla">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<?
    	if (!$id_planilla) {
    	?>  
    	<font size=+1><b>Nuevo Dato</b></font>   
    	<? }
        else {
        ?>
        <font size=+1><b>Dato</b></font>   
        <? } ?>
       
    </td>
 </tr>
 <tr><td>
  <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b>Datos Basico</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         <tr>	           
           <td align="center" colspan="2">
            <b> Número del Dato: <font size="+1" color="Red"><?=($id_planilla)? $id_planilla : "Nuevo Dato"?></font> </b>
           </td>
         </tr>
         <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
      		          
         <tr>
         	<td align="right">
				<b>Efector que da el Alta:</b>
			</td>
			<td align="left">			 	
			 <select name=cuie Style="width=257px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				<?if ($id_planilla) echo "disabled"?> >
			 <option value=-1>Seleccione</option>
			 <?
			 $sql= "select * from nacer.efe_conv 
			 		inner join alta.caps_hacen_partos using (cuie)
			 		order by nombre";
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$cuiel=$res_efectores->fields['cuie'];
			    $nombre_efector=$res_efectores->fields['nombre'];
			    
			    ?>
				<option value='<?=$cuiel?>' <?if ($cuie==$cuiel) echo "selected"?> ><?=$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>
			</td>
         </tr>
         
         <tr>
         	<td align="right">
				<b>Efector de Atencion Habitual:</b>
			</td>
			<td align="left">			 	
			 <select name=cuie_at_hab Style="width=257px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				<?if ($id_planilla) echo "disabled"?> >
			 <option value=-1>Seleccione</option>
			 <?
			 $sql= "select * from nacer.efe_conv order by nombre";
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$cuiel=$res_efectores->fields['cuie'];
			    $nombre_efector=$res_efectores->fields['nombre'];
			    
			    ?>
				<option value='<?=$cuiel?>' <?if ($cuie_at_hab==$cuiel) echo "selected"?> ><?=$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>
			</td>
         </tr>
         
         <tr>
			<td align="right">
				<b>Fecha Alta:</b>
			</td>
		    <td align="left">		    	
		    	 <input type=text id=fecha_alta name=fecha_alta value='<?=fecha($fecha_alta);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_alta");?>					    	 
		    </td>		    
		</tr>
		
		<tr>
			<td align="right">
				<b>Fecha Parto:</b>
			</td>
		    <td align="left">		    	
		    	 <input type=text id=fecha_parto name=fecha_parto value='<?=fecha($fecha_parto);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_parto");?>					    	 
		    </td>		    
		</tr>
         
         <tr>
         	<td align="right">
         	  <b>Documento Madre:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$doc_madre?>" name="doc_madre" <? if ($id_planilla) echo "readonly"?>><font color="Red">Sin Puntos</font>
            </td>
         </tr>
         
         <tr>
         	<td align="right">
         	  <b>Nombre Madre:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$nom_madre?>" name="nom_madre" <? if ($id_planilla) echo "readonly"?>>
            </td>
         </tr> 
         
         <tr>
         	<td align="right">
         	  <b>Nombre Bebe:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$nom_bebe?>" name="nom_bebe" <? if ($id_planilla) echo "readonly"?>>
            </td>
         </tr>
         
         <tr>
         	<td align="right">
         	  <b>Domicilio Familiar:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$domicilio?>" name="domicilio" <? if ($id_planilla) echo "readonly"?>>
            </td>
         </tr> 
                  
         <tr>
		      <td id=mo colspan="2">
		       <b> Brindan el Alta</b>
		      </td>
     	 </tr>        
        
     	<tr>
         	<td align="right">
         	  <b>Representante de Obstetricia:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$rep_obstetricia?>" name="rep_obstetricia" <? if ($id_planilla) echo "readonly"?>>
            </td>
         </tr>
         
         <tr>
         	<td align="right">
         	  <b>Representante de Neonatologia:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$rep_neo?>" name="rep_neo" <? if ($id_planilla) echo "readonly"?>>
            </td>
         </tr>
         
         <tr>
         	<td align="right">
         	  <b>Representante de Enfermeria:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$rep_enf?>" name="rep_enf" <? if ($id_planilla) echo "readonly"?>>
            </td>
         </tr>
         
         <tr>
		      <td id=mo colspan="2">
		       <b> Datos Generales - Tilde lo Realizado</b>
		      </td>
     	 </tr>
     	 
     	 <tr>
         	<td align="right">
         	  <b>Llenado de la Epicrisis del Recien Nacido:</b>
         	</td>         	
            <td align="left">		 
              <input type="checkbox" name="llena_epi" <?=($llena_epi=="SI")?"checked":""?>>
            </td>
         </tr> 
         
         <tr>
         	<td align="right">
         	  <b>Carnet Perinatal o Libreta Sanitaria Completa:</b>
         	</td>         	
            <td align="left">		 
              <input type="checkbox" name="carnet_parenteral" <?=($carnet_parenteral=="SI")?"checked":""?>>
            </td>
         </tr> 
     	 
         <tr>
         	<td align="right">
         	  <b>Peso al Nacer:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="20" value="<?=$peso_nacer?>" name="peso_nacer" <? if ($id_planilla) echo "readonly"?>><font color="Red">En Gramos.</font>
            </td>
        </tr>  
         
         <tr>
         	<td align="right">
				<b>Riesgo Social:</b>
			</td>
			<td align="left">			 	
			 <select name=riesgo_social Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value='sin_riesgo' <?if ($riesgo_social=='sin_riesgo') echo "selected"?> Style="background-color: #01DF74;">Sin Riesgo</option>
			  <option value='riesgo_social' <?if ($riesgo_social=='riesgo_social') echo "selected"?> Style="background-color: #F7FE2E;">Riesgo Social</option>
			  <option value='alarma_social' <?if ($riesgo_social=='alarma_social') echo "selected"?> Style="background-color: #F78181;">Alarma Social</option>			  
			 </select>
			</td>
         </tr>
         
         <tr>
         	<td align="right">
				<b>Sifilis:</b>
			</td>
			<td align="left">			 	
			 <select name=sifilis Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value='extraccion_realizada' <?if ($sifilis=='extraccion_realizada') echo "selected"?>>Extraccion Realizada</option>
			  <option value='resultado_entregado' <?if ($sifilis=='resultado_entregado') echo "selected"?>>Resultado Entregado</option>
			  <option value='resultado_pendiente' <?if ($sifilis=='resultado_pendiente') echo "selected"?>>Resultado Pendiente</option>			 
			 </select>
			</td>
         </tr>
         
         <tr>
         	<td align="right">
				<b>HIV:</b>
			</td>
			<td align="left">			 	
			 <select name=hiv Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value='extraccion_realizada' <?if ($hiv=='extraccion_realizada') echo "selected"?>>Extraccion Realizada</option>
			  <option value='resultado_entregado' <?if ($hiv=='resultado_entregado') echo "selected"?>>Resultado Entregado</option>
			  <option value='resultado_pendiente' <?if ($hiv=='resultado_pendiente') echo "selected"?>>Resultado Pendiente</option>			 
			 </select>
			</td>
         </tr>
         
         <tr>
         	<td align="right">
				<b>Hepatitis B:</b>
			</td>
			<td align="left">			 	
			 <select name=hep_b Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value='extraccion_realizada' <?if ($hep_b=='extraccion_realizada') echo "selected"?>>Extraccion Realizada</option>
			  <option value='resultado_entregado' <?if ($hep_b=='resultado_entregado') echo "selected"?>>Resultado Entregado</option>
			  <option value='resultado_pendiente' <?if ($hep_b=='resultado_pendiente') echo "selected"?>>Resultado Pendiente</option>			 
			 </select>
			</td>
         </tr>
         
         <tr>
         	<td align="right">
				<b>Chagas</b>
			</td>
			<td align="left">			 	
			 <select name=chagas Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value='extraccion_realizada' <?if ($chagas=='extraccion_realizada') echo "selected"?>>Extraccion Realizada</option>
			  <option value='resultado_entregado' <?if ($chagas=='resultado_entregado') echo "selected"?>>Resultado Entregado</option>
			  <option value='resultado_pendiente' <?if ($chagas=='resultado_pendiente') echo "selected"?>>Resultado Pendiente</option>			 
			 </select>
			</td>
         </tr>
         
         <tr>
         	<td align="right">
				<b>Toxoplasmosis</b>
			</td>
			<td align="left">			 	
			 <select name=toxo Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value='extraccion_realizada' <?if ($toxo=='extraccion_realizada') echo "selected"?>>Extraccion Realizada</option>
			  <option value='resultado_entregado' <?if ($toxo=='resultado_entregado') echo "selected"?>>Resultado Entregado</option>
			  <option value='resultado_pendiente' <?if ($toxo=='resultado_pendiente') echo "selected"?>>Resultado Pendiente</option>			 
			 </select>
			</td>
         </tr>
         
         <tr>
         	<td align="right">
         	  <b>Pesquisa Neo de enf. metabolicas congenitas:</b>
         	</td>         	
            <td align="left">		 
              <input type="checkbox" name="pes_neonatal" <?=($pes_neonatal=="SI")?"checked":""?>>
            </td>
         </tr> 
         
         <tr>
		      <td id=mo colspan="2">
		       <b> Vacunas</b>
		      </td>
     	 </tr>
           
     	 <tr>
         	<td align="right">
         	  <b>Anti Hepatitis B:</b>
         	</td>         	
            <td align="left">		 
              <input type="checkbox" name="vac_hep_b" <?=($vac_hep_b=="SI")?"checked":""?>>
            </td>
         </tr>    
         
         <tr>
         	<td align="right">
         	  <b>BCG:</b>
         	</td>         	
            <td align="left">		 
              <input type="checkbox" name="vac_bcg" <?=($vac_bcg=="SI")?"checked":""?>>
            </td>
         </tr>   
		         
         <tr>
		      <td id=mo colspan="2">
		       <b> Pautas y Encuestas</b>
		      </td>
     	 </tr>
     	 
     	 <tr>
         	<td align="right">
				<b>Grupo y Factor de la Madre</b>
			</td>
			<td align="left">			 	
			 <select name=grupo_factor_mama Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value='AB+' <?if ($grupo_factor_mama=='AB+') echo "selected"?>>AB+</option>
			  <option value='AB-' <?if ($grupo_factor_mama=='AB-') echo "selected"?>>AB-</option>
			  <option value='A+' <?if ($grupo_factor_mama=='A+') echo "selected"?>>A+</option>
			  <option value='A-' <?if ($grupo_factor_mama=='A-') echo "selected"?>>A-</option>
			  <option value='B+' <?if ($grupo_factor_mama=='B+') echo "selected"?>>B+</option>
			  <option value='B-' <?if ($grupo_factor_mama=='B-') echo "selected"?>>B-</option>
			  <option value='O+' <?if ($grupo_factor_mama=='O+') echo "selected"?>>O+</option>
			  <option value='O-' <?if ($grupo_factor_mama=='O-') echo "selected"?>>O-</option>
			 </select>
			</td>
         </tr>
         
         <tr>
         	<td align="right">
				<b>Grupo y Factor del Bebe</b>
			</td>
			<td align="left">			 	
			 <select name=grupo_factor_bebe Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value='AB+' <?if ($grupo_factor_bebe=='AB+') echo "selected"?>>AB+</option>
			  <option value='AB-' <?if ($grupo_factor_bebe=='AB-') echo "selected"?>>AB-</option>
			  <option value='A+' <?if ($grupo_factor_bebe=='A+') echo "selected"?>>A+</option>
			  <option value='A-' <?if ($grupo_factor_bebe=='A-') echo "selected"?>>A-</option>
			  <option value='B+' <?if ($grupo_factor_bebe=='B+') echo "selected"?>>B+</option>
			  <option value='B-' <?if ($grupo_factor_bebe=='B-') echo "selected"?>>B-</option>
			  <option value='O+' <?if ($grupo_factor_bebe=='O+') echo "selected"?>>O+</option>
			  <option value='O-' <?if ($grupo_factor_bebe=='O-') echo "selected"?>>O-</option>
			 </select>
			</td>
         </tr>
         
         <tr>
         	<td align="right">
         	  <b>Gamaglobulina (en caso de madres RH-):</b>
         	</td>         	
            <td align="left">		 
              <input type="checkbox" name="gamma_anti_rh" <?=($gamma_anti_rh=="SI")?"checked":""?>>
            </td>
         </tr>
         
         <tr>
         	<td align="right">
				<b>Recibe Pautas de Puericultura:</b>
			</td>
			<td align="left">			 	
			 <select name=pueri Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value='Oral' <?if ($pueri=='Oral') echo "selected"?>>Oral</option>
			  <option value='Escrita' <?if ($pueri=='Escrita') echo "selected"?>>Escrita</option>			  
			 </select>
			</td>
         </tr> 
         
         <tr>
         	<td align="right">
				<b>Recibe Pautas de Alarma para el Bebe:</b>
			</td>
			<td align="left">			 	
			 <select name=alarma_bebe Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value='Oral' <?if ($alarma_bebe=='Oral') echo "selected"?>>Oral</option>
			  <option value='Escrita' <?if ($alarma_bebe=='Escrita') echo "selected"?>>Escrita</option>			  
			 </select>
			</td>
         </tr> 
         
         <tr>
         	<td align="right">
				<b>Recibe Pautas de Alarma para la Madre:</b>
			</td>
			<td align="left">			 	
			 <select name=alarma_madre Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value='Oral' <?if ($alarma_madre=='Oral') echo "selected"?>>Oral</option>
			  <option value='Escrita' <?if ($alarma_madre=='Escrita') echo "selected"?>>Escrita</option>			  
			 </select>
			</td>
         </tr> 
         
         <tr>
         	<td align="right">
				<b>Recibe Conserjería de Lactancia Materna:</b>
			</td>
			<td align="left">			 	
			 <select name=lac_materna Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value='Oral' <?if ($lac_materna=='Oral') echo "selected"?>>Oral</option>
			  <option value='Escrita' <?if ($lac_materna=='Escrita') echo "selected"?>>Escrita</option>			  
			 </select>
			</td>
         </tr> 
         
         <tr>
         	<td align="right">
				<b>Recibe Conserjería Sobre Salud Reproductiva:</b>
			</td>
			<td align="left">			 	
			 <select name=salud_repro Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value='Oral' <?if ($salud_repro=='Oral') echo "selected"?>>Oral</option>
			  <option value='Escrita' <?if ($salud_repro=='Escrita') echo "selected"?>>Escrita</option>			  
			 </select>
			</td>
         </tr> 
         
         <tr>
         	<td align="right">
				<b>Recibe Pautas de Cuidado de Puerperio:</b>
			</td>
			<td align="left">			 	
			 <select name=cuidados_puerpe Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value='Oral' <?if ($cuidados_puerpe=='Oral') echo "selected"?>>Oral</option>
			  <option value='Escrita' <?if ($cuidados_puerpe=='Escrita') echo "selected"?>>Escrita</option>			  
			 </select>
			</td>
         </tr> 
          <tr>
         	<td align="right">
         	  <b>Recibio al menos 3 controles Post-Parto:</b>
         	</td>         	
            <td align="left">		 
              <input type="checkbox" name="control_postparto" <?=($control_postparto=='SI')?"checked":""?>><font color="Red"> Refiere a si se controlo y registro en HC al menos 3 controles durante las primeras dos horas posteriores al parto, con registrode temperatura, pulso, TA, involucion uterina y caracteristicas de loquios</font>
            </td>
         </tr>
         <tr>
		      <td id=mo colspan="2">
		       <b> Controles de Salud</b>
		      </td>
     	 </tr> 
     	  <tr>
         	<td align="right">
				<b>Efector de Derivado:</b>
			</td>
			<td align="left">			 	
			 <select name=cuie_ef_der Style="width=257px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				<?if ($id_planilla) echo "disabled"?> >
			 <option value=-1>Seleccione</option>
			 <?
			 $sql= "select * from nacer.efe_conv order by nombre";
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$cuiel=$res_efectores->fields['cuie'];
			    $nombre_efector=$res_efectores->fields['nombre'];
			    ?>
				<option value='<?=$cuiel?>' <?if ($cuie_ef_der==$cuiel) echo "selected"?> ><?=$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>
			</td>
         </tr>
         <tr>
			<td align="right">
				<b>Fecha Turno:</b>
			</td>
		    <td align="left">	
		    	 <input type=text id=fecha_turno name=fecha_turno value='<?=fecha($fecha_turno);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_turno");?>					    	 
		    </td>		    
		</tr>
		<tr>
         	<td align="right">
         	  <b>Hora Turno:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$hora_turno?>" name="hora_turno" <? if ($id_planilla) echo "readonly"?>>
            </td>
         </tr>
		
     	 
     	 <tr>
		      <td id=mo colspan="2">
		       <b> Comentarios</b>
		      </td>
     	 </tr>
     	 
         <tr>
         	<td align="right">
         	  <b>Comentarios:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='4' name='observaciones' <? if ($id_planilla) echo "readonly"?>><?=$observaciones;?></textarea>
            </td>
         </tr>              
        </table>
      </td>      
     </tr> 
   

   <?if (!($id_planilla)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Planilla</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Planilla' onclick="return control_nuevos()"
         title="Guardar datos de la Planilla">
       </td>
      </tr>
     
     <?}?>
     
 </table>           
<br>
<?if ($id_planilla){?>
<table class="bordes" align="center" width="100%">
		 <tr align="center" id="sub_tabla">
		 	<td>	
		 		Editar DATO
		 	</td>
		 </tr>
		 
		 <tr>
		    <td align="center">
		      <input type=button name="editar" value="Editar" onclick="editar_campos()" title="Edita Campos" style="width=130px"> &nbsp;&nbsp;
		      <input type="submit" name="guardar_editar" value="Guardar" title="Guarda Muleto" disabled style="width=130px" onclick="return control_nuevos()">&nbsp;&nbsp;
		      <input type="button" name="cancelar_editar" value="Cancelar" title="Cancela Edicion de Muletos" disabled style="width=130px" onclick="document.location.reload()">		      
		      <?if (permisos_check("inicio","permiso_borrar")) $permiso="";
			  else $permiso="disabled";?>
		      <input type="submit" name="borrar" value="Borrar" style="width=130px" <?=$permiso?>>
		    </td>
		 </tr> 
	 </table>	
	 <br>
	 <?}?>
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='alta_listado.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
    
 </table></td></tr>
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>
