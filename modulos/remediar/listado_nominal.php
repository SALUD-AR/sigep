<?php

require_once("../../config.php");


if($_POST['buscar']){

	if($_POST['f_desde']!='' && $_POST['f_hasta']!=''){
		$where_tmp.=" and remediar_x_beneficiario.fechaempadronamiento between '".Fecha_db($_POST['f_desde'])."' and '".Fecha_db($_POST['f_hasta'])."' ";
	}
	if($_POST['efector']!='-1' && $_POST['efector']!=''){
	  	$where_tmp.= " and formulario.centro_inscriptor ='".$_POST['efector']."'";
	}

						
$sql="select (apellido_benef||' '||apellido_benef_otro) as ape,(nombre_benef||' '||nombre_benef_otro) as nom,calle,numero_calle,localidad,municipio,barrio
		,formulario.apellidoagente||' '||formulario.nombreagente as promotor,nombreefector
		,remediar_x_beneficiario.fechaempadronamiento,score_riesgo
					from uad.beneficiarios
					inner join uad.remediar_x_beneficiario on remediar_x_beneficiario.clavebeneficiario=beneficiarios.clave_beneficiario 
					inner join remediar.formulario on formulario.nroformulario=remediar_x_beneficiario.nroformulario
					inner join facturacion.smiefectores on smiefectores.cuie=formulario.centro_inscriptor
		 where cast(score_riesgo as int)>= 4  and remediar_x_beneficiario.enviado = 's' $where_tmp
		 
		 group by (apellido_benef||' '||apellido_benef_otro),(nombre_benef||' '||nombre_benef_otro),calle,numero_calle,localidad,municipio,barrio
		,formulario.apellidoagente||' '||formulario.nombreagente,nombreefector
		,remediar_x_beneficiario.fechaempadronamiento,score_riesgo";
	}	
	//	echo $sql;
	
	
echo $html_header;
?>
<script>
//Validar Fechas
function esFechaValida(fecha){
    if (fecha != undefined && fecha.value != "" ){
        if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)){
            alert("formato de fecha no válido (dd/mm/aaaa)");
            return false;
        }
        var dia  =  parseInt(fecha.value.substring(0,2),10);
        var mes  =  parseInt(fecha.value.substring(3,5),10);
        var anio =  parseInt(fecha.value.substring(6),10);
 
    switch(mes){
        case 1:
        case 3:
        case 5:
        case 7:
        case 8:
        case 10:
        case 12:
            numDias=31;
            break;
        case 4: case 6: case 9: case 11:
            numDias=30;
            break;
        case 2:
            if (comprobarSiBisisesto(anio)){ numDias=29 }else{ numDias=28};
            break;
        default:
            alert("Fecha introducida errónea");
            return false;
    }
 
        if (dia>numDias || dia==0){
            alert("Fecha introducida errónea");
            return false;
        }
        return true;
    }
}
 
function comprobarSiBisisesto(anio){
if ( ( anio % 100 != 0) && ((anio % 4 == 0) || (anio % 400 == 0))) {
    return true;
    }
else {
    return false;
    }
}

var patron = new Array(2,2,4)
var patron2 = new Array(5,16)
function mascara(d,sep,pat,nums){
if(d.valant != d.value){
val = d.value
largo = val.length
val = val.split(sep)
val2 = ''
for(r=0;r<val.length;r++){
val2 += val[r]
}
if(nums){
for(z=0;z<val2.length;z++){
if(isNaN(val2.charAt(z))){
letra = new RegExp(val2.charAt(z),"g")
val2 = val2.replace(letra,"")
}
}
}
val = ''
val3 = new Array()
for(s=0; s<pat.length; s++){
val3[s] = val2.substring(0,pat[s])
val2 = val2.substr(pat[s])
}
for(q=0;q<val3.length; q++){
if(q ==0){
val = val3[q]

}
else{
if(val3[q] != ""){
val += sep + val3[q]
}
}
}
d.value = val
d.valant = val
}
}
</script>
<form name=form1 action="listado_nominal" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
      	

			
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>		    
	     &nbsp;&nbsp;
	    <? $link=encode_link("listado_nominal_excel.php",array("sql"=>$sql)); ?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">    
	  </td>
     </tr>
	 <tr>
	 <td align=center>
	 <b>Fecha Desde:</b><input type="text" size="10" maxlength="10" name="f_desde" onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);" value="<?=$_POST['f_desde']?>" <?=$r?>/><?=link_calendario('f_desde');?> <b>; Hasta: </b><input type="text" size="10" maxlength="10" name="f_hasta" onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);" value="<?=$_POST['f_hasta']?>" <?=$r?>/><?=link_calendario('f_hasta'); ?><br /> <b>Efector:</b>
	 		<select name="efector">
	 			<option value="-1" <? if($_POST['quegrupo']=='-1'){ echo 'selected';}?>>Todos</option>
				<?
			 $sql2= "select * from facturacion.smiefectores order by nombreefector";
			 $res_efectores2=sql($sql2) or fin_pagina();
			 while (!$res_efectores2->EOF){
			 	$cuiec=$res_efectores2->fields['cuie'];
			    $nombre_efector=$res_efectores2->fields['nombreefector'];

			    ?>
				<option value='<?=$cuiec?>' <?if ($cuie==$cuiec) echo "selected"?> ><?=$cuiec." - ".$nombre_efector?></option>
			    <?
			    $res_efectores2->movenext();
			    }?>
			</select><button onclick="window.open('../inscripcion/busca_efector.php?qkmpo=efector','Buscar','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');">b</button>
	 </td>
	 </tr>
</table>

<? if($_POST['buscar']){ 
	$result = sql($sql) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr >
  	<td align=right id=mo>Fecha de Carga</td>
    <td align=right id=mo>Apellido</td>
    <td align=right id=mo>Nombre</td>
    <td align=right id=mo>Calle y Nº</td>
    <td align=right id=mo>Localidad</td>
    <td align=right id=mo>Municipio</td>
	<td align=right id=mo>Barrio</td>
	<td align=right id=mo>Efector</td>
	<td align=right id=mo>Promotor</td>
	<td align=right id=mo>Score Riesgo</td>
  </tr>
 <? 
   while (!$result->EOF) {      ?>
    <tr <?=atrib_tr()?> >   
	 <td><?=fecha($result->fields['fechaempadronamiento'])?></td>
     <td><?=$result->fields['ape']?></td>
     <td><?=$result->fields['nom']?></td>
     <td><?=$result->fields['calle'].' '.$result->fields['num_calle']?></td>
     <td><?=$result->fields['localidad']?></td>
     <td><?=$result->fields['municipio']?></td>
	 <td><?=$result->fields['barrio']?></td>
	  <td><?=$result->fields['nombreefector']?></td>
	  <td><?=$result->fields['promotor']?></td>
	  <td><?=$result->fields['score_riesgo']?></td>
    </tr>
	<?$result->MoveNext();
   } 
 } ?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>