<?
require_once ("../../config.php"); 

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();
$user_cuie=substr($_ses_user['login'],0,6);

$desabil_guardar='';
($_POST['fechaempadronamiento']=='')?'':$fechaempadronamiento=$_POST['fechaempadronamiento'];
($_POST['num_form_remediar']=='')?'':$num_form_remediar=$_POST['num_form_remediar'];
($_POST['clavebeneficiario']=='')?'':$clave_beneficiario=$_POST['clavebeneficiario'];
/*EDITAR REMEDIAR*/ //echo $accion_remediar;
if ($_POST['guardar_editar']=="Guardar"){	
    $fecha_carga= date("Y-m-d");
    $usuario=$_ses_user['id'];
    $usuario = substr($usuario,0,9);
    $clave_beneficiario=$_POST['clavebeneficiario'];
    $edad=$_POST['edad'];
    $sexo=$_POST['sexo'];
    $fecha_nac=$_POST['fecha_nac'];
    if ($_POST['num_form_remediar']=='') $num_form_remediar=$clave_beneficiario;
    else $num_form_remediar=$_POST['num_form_remediar'];
    $puntaje_final=$_POST['puntaje_final'];
    $fechaempadronamiento=Fecha_db($_POST['fechaempadronamiento']);
    $apellidoagente=$_POST['apellidoagente'];
    $nombreagente=$_POST['nombreagente'];
    $num_doc_agente=$_POST['num_doc_agente'];
    $cuie=$_POST['cuie'];
    $os=$_POST['os'];
    $cual_os=$_POST['cual_os'];
	
	if(rtrim($_POST['factorriesgo'])!=''){ 
		$factorriesgo=$_POST['factorriesgo']; 
		}
	else{ 
		$accion="No se pudo calcular el Factor de Riesgo.";
        echo "<SCRIPT Language='Javascript'>
		 		location.href='".encode_link("remediar_admin.php",array("estado_envio"=>$estado_envio,"clave_beneficiario"=>$clave_beneficiario,"sexo"=>$sexo,"fecha_nac"=>$fecha_nac,"edad"=>$edad,"vremediar"=>'n',"accion"=>$accion))."';
		 </SCRIPT>";
		 }
	$factorriesgo_p=$_POST['puntos_1'];
	if($factorriesgo_p==''){ $factorriesgo_p=0; }
	
    list($hta2,$p1)=explode("_",$_POST['hta2']);
    if($hta2==''){$hta2=0; $p1=0;}
	
    list($hta3,$p2)=explode("_",$_POST['hta3']);
    if($hta3==''){$hta3=0; $p2=0;}
	
    list($colesterol4,$p3)=explode("_",$_POST['colesterol4']);
    if($colesterol4==''){$colesterol4=0; $p3=0;}
	
    list($colesterol5,$p4)=explode("_",$_POST['colesterol5']);
    if($colesterol5==''){$colesterol5=0; $p4=0;}
	
    list($dmt26,$p5)=explode("_",$_POST['dmt26']);
    if($dmt26==''){$dmt26=0; $p5=0;}
	
    list($dmt27,$p6)=explode("_",$_POST['dmt27']);
    if($dmt27==''){$dmt27=0; $p6=0;}
	
    list($tabaco9,$p7)=explode("_",$_POST['tabaco9']);
    if($tabaco9==''){$tabaco9=0; $p7=0;}
	
    list($ecv8,$p8)=explode("_",$_POST['ecv8']);
    if($ecv8==''){$ecv8=0; $p8=0;}
	if($puntaje_final==''){
		$puntaje_final=$factorriesgo_p+$p1+$p2+$p3+$p4+p5+p6+p7+p8;
	}
    $db->StartTrans();

        $q="select id_formulario from remediar.formulario
             where nroformulario=$num_form_remediar and nroformulario in (select nroformulario
                                                                            from uad.remediar_x_beneficiario
                                                                            where clavebeneficiario='$clave_beneficiario')";
        //$id_Idformulario=sql($q) or fin_pagina();
        $val=sql($q, "Error en consulta de validacion") or fin_pagina();
        if ($val->RecordCount()!=0){
            $Id_formulario=$val->fields['id_formulario'];

            $fecha_carga=date("Y-m-d");
			$usuario=$_ses_user['name'];
			
			$query2="update remediar.formulario set 
							factores_riesgo=$factorriesgo,
							hta2=$hta2,hta3=$hta3,
							colesterol4=$colesterol4,colesterol5=$colesterol5,
							dmt26=$dmt26,dmt27=$dmt27,ecv8=$ecv8,tabaco9=$tabaco9,
							puntaje_final=$puntaje_final,
							apellidoagente=upper('$apellidoagente'),nombreagente=upper('$nombreagente'),
							centro_inscriptor=upper('$cuie'),dni_agente=upper('$num_doc_agente'),
							os=upper('$os'),cual_os=upper('$cual_os'),
							usuario='$usuario',
							fecha_carga='$fecha_carga'
            where id_formulario=$Id_formulario";

            sql($query2, "Error al verificar Remediar2") or fin_pagina();
	
		$query3="update uad.beneficiarios set estado_envio='n',score_riesgo=$puntaje_final
                where clave_beneficiario='$clave_beneficiario'";

	    	sql($query3, "Error al insertar la Planilla") or fin_pagina();

            $accion="Se actualizo Remediar";
        }else{  
                $query="select * from uad.remediar_x_beneficiario
		where nroformulario=$num_form_remediar";
	    $val=sql($query, "Error en consulta de validacion") or fin_pagina();

            if ($val->RecordCount()==0){
                $sql="select nextval('uad.remediar_x_beneficiario_Id_r_x_b_seq') as id";
                $res_rxb=sql($sql) or fin_pagina();
                $Id_r_x_b=$res_rxb->fields['id'];

                $query1="insert into uad.remediar_x_beneficiario (id_r_x_b,nroformulario,fechaempadronamiento,clavebeneficiario,fecha_carga,usuario_carga)
                        values($Id_r_x_b,$num_form_remediar,'$fechaempadronamiento','$clave_beneficiario','$fecha_carga',upper('$usuario'))";
                sql($query1, "Error al insertar Remediar1") or fin_pagina();

                $q="select nextval('remediar.formulario_Id_formulario_seq') as id";
                $id_Idformulario=sql($q) or fin_pagina();
                $Id_formulario=$id_Idformulario->fields['id'];
				
				$fecha_carga=date("Y-m-d");
				$usuario=$_ses_user['name'];
				
                $query2="insert into remediar.formulario (id_formulario,nroformulario,factores_riesgo,hta2,hta3,colesterol4,colesterol5,dmt26,dmt27,ecv8,tabaco9,puntaje_final,apellidoagente,nombreagente,centro_inscriptor,dni_agente,os,cual_os,usuario,fecha_carga)
                        values($Id_formulario,$num_form_remediar,$factorriesgo,$hta2,$hta3,$colesterol4,$colesterol5,$dmt26,$dmt27,$ecv8,$tabaco9,$puntaje_final,upper('$apellidoagente'),upper('$nombreagente'),upper('$cuie'),upper('$num_doc_agente'),upper('$os'),upper('$cual_os'),'$usuario','$fecha_carga')";

                sql($query2, "Error al insertar Remediar2") or fin_pagina();
		
			$query3="update uad.beneficiarios set score_riesgo=$puntaje_final
               	 where clave_beneficiario='$clave_beneficiario'";

	   	 	sql($query3, "Error al insertar la Planilla") or fin_pagina();

                $accion="Se guardo Remediar";
                 
                $vremediar="s";
            }else{ $accion="Ya existe para otro beneficiario el n&deg; de formulario Remediar que intenta grabar.";
                   echo "<SCRIPT Language='Javascript'>
		 		location.href='".encode_link("remediar_admin.php",array("estado_envio"=>$estado_envio,"clave_beneficiario"=>$clave_beneficiario,"sexo"=>$sexo,"fecha_nac"=>$fecha_nac,"edad"=>$edad,"vremediar"=>'n',"accion"=>$accion))."';
		 </SCRIPT>";
            }
        }$fechaempadronamiento=Fecha($fechaempadronamiento);
    $db->CompleteTrans();

}
echo "<SCRIPT Language='Javascript'>
				function nuevo_remediar(){
		 		location.href='".encode_link("remediar_admin.php",array("estado_envio"=>'p',"clave_beneficiario"=>$clave_beneficiario,"sexo"=>$sexo,"fecha_nac"=>$fecha_nac,"edad"=>0,"vremediar"=>'n'))."';
				}

		 </SCRIPT>";
//echo $_POST['num_form_remediar'].'***'.$vremediar;
//echo $_POST['fechaempadronamiento'].'**'.$_POST['num_form_remediar'];
if($vremediar=="s" && $_POST['num_form_remediar']==''){ //echo 'aaaa';
/*Remdiar mas redes inicio recupero*/
 $queryrmediar="SELECT remediar_x_beneficiario.fechaempadronamiento,formulario.nroformulario,puntaje_final,hta2,hta3,colesterol4
                ,colesterol5,dmt26,dmt27,tabaco9,ecv8,remediar_x_beneficiario.usuario_carga,formulario.apellidoagente
				,formulario.nombreagente,formulario.centro_inscriptor
                ,formulario.dni_agente,upper(os) as os,cual_os,fecha_nacimiento_benef,estado_envio
			FROM  uad.remediar_x_beneficiario 
                        inner join remediar.formulario on formulario.nroformulario=remediar_x_beneficiario.nroformulario
                        inner join uad.beneficiarios on remediar_x_beneficiario.clavebeneficiario=beneficiarios.clave_beneficiario
  where clavebeneficiario='$clave_beneficiario' and fechaempadronamiento in (select max(fechaempadronamiento) from uad.remediar_x_beneficiario where clavebeneficiario='$clave_beneficiario')
    group by remediar_x_beneficiario.fechaempadronamiento,formulario.nroformulario,puntaje_final,hta2,hta3,colesterol4,colesterol5,dmt26,dmt27,tabaco9,ecv8,remediar_x_beneficiario.usuario_carga
    ,formulario.apellidoagente,formulario.nombreagente,formulario.centro_inscriptor,formulario.dni_agente,os,cual_os,fecha_nacimiento_benef,estado_envio";

$res_remediar=sql($queryrmediar, "Error al traer el Comprobantes") or fin_pagina();
if ($res_remediar->RecordCount()>0){
	$num_form_remediar=$res_remediar->fields['nroformulario'];
	$puntaje_final=$res_remediar->fields['puntaje_final'];
	$hta2=$res_remediar->fields['hta2'];
    $hta3=$res_remediar->fields['hta3'];
    $colesterol4=$res_remediar->fields['colesterol4'];
    $colesterol5=$res_remediar->fields['colesterol5'];
    $dmt26=$res_remediar->fields['dmt26'];
    $dmt27=$res_remediar->fields['dmt27'];
    $tabaco9=$res_remediar->fields['tabaco9'];
    $ecv8=$res_remediar->fields['ecv8'];
    $fechaempadronamiento=fecha($res_remediar->fields['fechaempadronamiento']);
    $fecha_nac=fecha($res_remediar->fields['fecha_nacimiento_benef']);
    $apellidoagente=$res_remediar->fields['apellidoagente'];
    $nombreagente=$res_remediar->fields['nombreagente'];
    $cuie=$res_remediar->fields['centro_inscriptor'];
    $num_doc_agente=$res_remediar->fields['dni_agente'];
    $os=$res_remediar->fields['os'];
    $estado_envio=$res_remediar->fields['estado_envio'];
    $cual_os=$res_remediar->fields['cual_os'];
	}
 /*Remdiar mas redes fin recupero*/
}

if ($_POST['buscar_promotor']=="Buscar"){
    /*MISIONES*/
     //list($hta2,$p)=explode("_",$_POST['hta2']);
    $href=encode_link("busca_promotor.php",array("cual_os"=>$cual_os,"os"=>$os,"cuie"=>$cuie,"puntaje_final"=>$puntaje_final,"hta2"=>$hta2,"hta3"=>$hta3,"colesterol4"=>$colesterol4,"colesterol5"=>$colesterol5,"dmt26"=>$dmt26,"dmt27"=>$dmt27,"tabaco9"=>$tabaco9,"ecv8"=>$ecv8,"fechaempadronamiento"=>$fechaempadronamiento,"num_form_remediar"=>$num_form_remediar,"clavebeneficiario"=>$clavebeneficiario,"fecha_nac"=>$fecha_nac,"sexo"=>$sexo,"vremediar"=>$vremediar,"campo_actual"=>$campo_actual,"pagina"=>'remediar_admin.php',"estado_envio"=>$estado_envio,"pantalla"=>'Remediar'));
    echo "<SCRIPT Language='Javascript'> window.open('$href','Buscar','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');</SCRIPT>";
}

if ($_POST['buscar_remediar']=="b"){
if ($_POST['num_form_remediar']!=''){
	 $queryrmediar="SELECT remediar_x_beneficiario.fechaempadronamiento,formulario.nroformulario,puntaje_final,hta2,hta3,colesterol4
					,colesterol5,dmt26,dmt27,tabaco9,ecv8,remediar_x_beneficiario.usuario_carga,formulario.apellidoagente,formulario.nombreagente,formulario.centro_inscriptor
					,formulario.dni_agente,upper(os) as os,cual_os,fecha_nacimiento_benef,estado_envio,sexo,factores_riesgo
				FROM  uad.remediar_x_beneficiario 
							inner join remediar.formulario on formulario.nroformulario=remediar_x_beneficiario.nroformulario
							inner join uad.beneficiarios on remediar_x_beneficiario.clavebeneficiario=beneficiarios.clave_beneficiario
	  where clavebeneficiario='$clave_beneficiario' and formulario.nroformulario='".$_POST['num_form_remediar']."'
		group by remediar_x_beneficiario.fechaempadronamiento,formulario.nroformulario,puntaje_final,hta2,hta3,colesterol4,colesterol5,dmt26,dmt27,tabaco9,ecv8,remediar_x_beneficiario.usuario_carga
		,formulario.apellidoagente,formulario.nombreagente,formulario.centro_inscriptor,formulario.dni_agente,os,cual_os,fecha_nacimiento_benef,estado_envio,sexo,factores_riesgo";
	
	$res_remediar=sql($queryrmediar, "Error al traer el Comprobantes") or fin_pagina();
	if ($res_remediar->RecordCount()>0){
		$num_form_remediar=$res_remediar->fields['nroformulario'];
		$puntaje_final=$res_remediar->fields['puntaje_final'];
		$hta2=$res_remediar->fields['hta2'];
		$hta3=$res_remediar->fields['hta3'];
		$sexo=$res_remediar->fields['sexo'];
		$id_factorriesgo=$res_remediar->fields['factores_riesgo'];
		$colesterol4=$res_remediar->fields['colesterol4'];
		$colesterol5=$res_remediar->fields['colesterol5'];
		$dmt26=$res_remediar->fields['dmt26'];
		$dmt27=$res_remediar->fields['dmt27'];
		$tabaco9=$res_remediar->fields['tabaco9'];
		$ecv8=$res_remediar->fields['ecv8'];
		$fechaempadronamiento=fecha($res_remediar->fields['fechaempadronamiento']);
		$fecha_nac=fecha($res_remediar->fields['fecha_nacimiento_benef']);
		$apellidoagente=$res_remediar->fields['apellidoagente'];
		$nombreagente=$res_remediar->fields['nombreagente'];
		$cuie=$res_remediar->fields['centro_inscriptor'];
		$num_doc_agente=$res_remediar->fields['dni_agente'];
		$os=$res_remediar->fields['os'];
		$estado_envio=$res_remediar->fields['estado_envio'];
		$cual_os=$res_remediar->fields['cual_os'];
		}else{  $accion2="No se encuentra formulario"; }
	}else{ echo "<SCRIPT Language='Javascript'> alert('Debe Cargar el N� de Formulario'); </SCRIPT>";}
}
echo $html_header;
?>
<script>
 //controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
    if(document.all.num_form_remediar.value==""){
                 //alert("Debe completar el campo numero de formulario");
                // document.all.num_form_remediar.focus();
                // return false;
                 }else{
                        var num_form_remediar=document.all.num_form_remediar.value;
                        if(isNaN(num_form_remediar)){
                                alert('El dato ingresado en numero de formulario debe ser entero');
                                document.all.num_form_remediar.focus();
                                return false;
                        }
                 }
      if(document.all.fechaempadronamiento.value==""){
                alert("Debe completar el campo fecha de empadronamiento");
                return false;
            }
    //if(document.all.num_form_remediar.value!=""){
         var edad=form1.edad.value;
		 
        if(document.all.factorriesgo.value=="" || document.all.factorriesgo.value.replace(/^\s*|\s*$/g,"") ==""){
                alert("No se calculo Factor de Riesgo");
                return false;
            }
           
         
		  /*if(edad>20){
              if(document.all.hta2.value=="-1"){
                alert("Debe completar el campo HTA 2)");
                document.all.hta2.focus();
                 return false;
               }
          }*/
           /*if(document.all.hta3.value=="-1"){
            alert("Debe completar el campo HTA 3)");
            document.all.hta3.focus();
             return false;
           }
           if(edad>20){
               if(document.all.colesterol4.value=="-1"){
                alert("Debe completar el campo COLESTEROL 4)");
                document.all.colesterol4.focus();
                 return false;
               }
           }
           if(document.all.colesterol5.value=="-1"){
            alert("Debe completar el campo COLESTEROL 5)");
            document.all.colesterol5.focus();
             return false;
           }
           if(edad>40){
               if(document.all.dmt26.value=="-1"){
                alert("Debe completar el campo DMT2 6)");
                document.all.dmt26.focus();
                 return false;
               }
           }
           if(document.all.dmt27.value=="-1"){
            alert("Debe completar el campo DMT2 7)");
            document.all.dmt27.focus();
             return false;
           }
           if(document.all.ecv8.value=="-1"){
            alert("Debe completar el campo ECV 8)");
            document.all.ecv8.focus();
             return false;
           }
           if(document.all.tabaco9.value=="-1"){
            alert("Debe completar el campo TABACO 9)");
            document.all.tabaco9.focus();
             return false;
           }*/
           
		   if(document.all.cuie.value=="-1"){
            alert("Debe elegir un centro inscriptor");
            document.all.cuie.focus();
             return false;
           }
		   
	/*var apellidoagente=document.all.apellidoagente.value;
    if(apellidoagente.replace(/^\s+|\s+$/g,"")==""){
	 alert("Debe completar el campo apellido Agente");
	 document.all.apellidoagente.focus();
	 return false;
         }else{
	 var charpos = document.all.apellidoagente.value.search("/[^A-Za-z\s]/");
	   if( charpos >= 0)
	    {
	     alert( "El campo Apellido Agente solo permite letras ");
	     document.all.apellidoagente.focus();
	     return false;
	    }
	 }	
	 		
	var nombreagente=document.all.nombreagente.value;
    if(nombreagente.replace(/^\s+|\s+$/g,"")==""){
	 alert("Debe completar el campo nombre Agente");
	 document.all.nombreagente.focus();
	 return false;
	 }else{
		 var charpos = document.all.nombreagente.value.search("/[^A-Za-z\s]/");
		   if( charpos >= 0)
		    {
		     alert( "El campo Nombre Agente solo permite letras ");
		     document.all.nombreagente.focus();
		     return false;
		    }
		 } */
   // }
}//de function control_nuevos()


/**********************************************************/
//funciones para busqueda abreviada utilizando teclas en la lista que muestra los clientes.
var digitos=10; //cantidad de digitos buscados
var puntero=0;
var buffer=new Array(digitos); //declaraci?n del array Buffer
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
   event.returnValue = false; //invalida la acci?n de pulsado de tecla para evitar busqueda del primer caracter
}//de function buscar_op_submit(obj)

//Validar Fechas
function esFechaValida(fecha){
    if (fecha != undefined && fecha.value != "" ){
        if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)){
            alert("formato de fecha no v�lido (dd/mm/aaaa)");
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
            alert("Fecha introducida err�nea");
            return false;
    }
 
        if (dia>numDias || dia==0){
            alert("Fecha introducida err�nea");
            return false;
        }
        return true;
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

<form name='form1' action='remediar_admin.php' method='POST'>
    <? // echo $clave_beneficiario.'**'.$sexo.'**'.$fecha_nac.'**'.$pagina;
    //echo $_POST['fechaempadronamiento'].'**'.$_POST['num_form_remediar'];
    $edad=substr($fechaempadronamiento,6,10)-substr($fecha_nac,6,10);
	if($edad<0 ){
		$desabil_guardar='disabled';
		if($num_form_remediar && $accion2!="No se encuentra formulario"){
		echo "<SCRIPT Language='Javascript'> alert('La Fecha de Empadronamiento no puede ser anterior a la Fecha de Nacimiento'); </SCRIPT>";
		}
		
	}?>
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<?echo "<center><b><font size='+1' color='Blue'>$accion2</font></b></center>";?>
<table width="97%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
        <font size=+1><b>Formulario</b></font>
    </td>
 </tr>
 <tr><td>
  <table width=100% align="center" class="bordes">
      <tr>
       <td>
        <table class="bordes" align="center">
         <tr>
           <td align="center" colspan="4" id="ma">
               <b> N&uacute;mero de Formulario Nacer: <font size="+1" color="Blue"><?=($clave_beneficiario)? $clave_beneficiario : $clave_beneficiario=$clavebeneficiario?></font> </b>
            <input type="hidden" value="<?=$clave_beneficiario?>" name="clavebeneficiario">
            <input type="hidden" value="<?=$fecha_nac?>" name="fecha_nac">
            <input type="hidden" value="<?=$sexo?>" name="sexo">
            <input type="hidden" value="<?=$vremediar?>" name="vremediar">
            <input type="hidden" value="<?=$campo_actual?>" name="campo_actual">
            <input type="hidden"  value="<?=$edad?>" name="edad">
            <input type="hidden"  value="<?=$pagina_viene_1?>" name="pagina_viene_1">
			<input type="hidden"  value="<?=$estado_envio?>" name="estado_envio">
           </td>
         </tr>

         </td>
       </tr>

         

         <tr>

             <tr id="mo">
                <td align="center" colspan="4" >
                    <b> N&uacute;mero de Formulario Remediar + Redes </b><input type="text" maxlength="16" name="num_form_remediar" value="<?=$num_form_remediar?>" readOnly>
               </td>
             </tr>
             <tr id="ma">
         	<td align="left" colspan="4">
				<b>Fecha de Empadronamiento:</b>
			
		    	 <input type=text name=fechaempadronamiento value='<?=$fechaempadronamiento;?>' size=15 <?php if ($num_form_remediar && $accion2!="No se encuentra formulario")echo "readOnly"; ?> onblur="esFechaValida(this); sumatoria();" onKeyUp="mascara(this,'/',patron,true);">
		    	 <?//if (!$num_form_remediar){ echo link_calendario("fechaempadronamiento");}?>
		    </td>
	  </tr>
          <tr id="mo">
                <td align="center" colspan="4" >
                    <b> Datos Cobertura </b>
               </td>
             </tr>
             <tr id="ma">
         	<td align="center" colspan="4">
				<input type="radio" name="os" value="OBRA SOCIAL" <?php if($os == "OBRA SOCIAL") echo "checked" ;?>> Obra Social &nbsp;&nbsp;&nbsp;
				<input type="radio" name="os" value="MUTUAL" <?php if ($os == "MUTUAL")echo "checked" ;?>> Mutual &nbsp;&nbsp;&nbsp;
				<input type="radio" name="os" value="PREPAGA" <?php if ($os == "PREPAGA")echo "checked" ;?>> Prepaga &nbsp;&nbsp;&nbsp;
                                <input type="radio" name="os" value="NINGUNA" <?php if (($os == "NINGUNA") or ($os==""))echo "checked" ;?>> Ninguna
                                &nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;<b>Cual?</b> <input type=text name=cual_os value="<?=$cual_os?>">
		    </td>
	  </tr>
           <tr id="ma">
           <td align="left" colspan="4">
            <b>Factores de Riesgo</b>
           </td>
         </tr>
         <tr align="center">
         	<td  colspan="4">
         	  <table width="100%" border="1" cellspacing="0" bordercolor="#006699" style="border:thin groove;">
                      <tr >
                          <td style=" padding-left: 40px"><b> 1) Sexo y edad </b></td>
                          <td align="center"><?if($sexo=='F'){ echo 'Femenino';}else{ echo 'Masculino';}?></td>
                          <td align="center"><?  if($id_factorriesgo!=''){ $mas_slq="or (id_factor=$id_factorriesgo)";}  
                            $sql= "select *
				from remediar.factores_riesgo
                                where (substring(sexo,1,1)=upper('$sexo') and $edad between edadini and edadfin) ".$mas_slq;
            $refrescar='document.forms[0].submit()';

			 ?>
			<!--<select name=factorriesgo Style="width=200px"
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer(); "
				>
			 <option value='-1'>Seleccione</option>-->


			 <?
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){
			 	$id_factorriesgo=$res_efectores->fields['id_factor'];
			    echo $descripcion=$res_efectores->fields['descripcion'];
                             $edadini=$res_efectores->fields['edadini'];
                              $edadfin=$res_efectores->fields['edadfin'];
                              $puntos_1=$res_efectores->fields['puntaje'];
                              

			    ?>
                            <input type="hidden"  value="<?=$id_factorriesgo?>" name="factorriesgo">
                            <input type="hidden"  value="<?=$puntos_1?>" name="puntos_1">
				<!--<option value='<?//=$idl?>' <?//if (($descripcion1_remediar==$descripcion)||($edad>=$edadini && $edad<$edadfin )) echo "selected"?> ><?//=$descripcion?></option>-->
			    <?
			    $res_efectores->movenext();
			    }?>
			<!--</select>--></td>
                      </tr>
                  </table>
         	</td>
         </tr>
         <tr id="ma">
           <td align="left" colspan="4">
            <b>HTA</b>
           </td>
         </tr>
         <tr align="center">
         	<td  colspan="4">
         	  <table width="100%" border="1" cellspacing="0" bordercolor="#006699" style="border:thin groove;">
                      <? if($edad>20){ ?>
                      <tr>
                          <td style=" padding-left: 40px" width="72%"><b> 2) En los &uacute;limos 2 a&ntilde;os, &iquest;le tomaron la presi&oacute;n arterial?<br>
                               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(s&oacute;lo para mayores de 20 a&ntilde;os)</b></td>
                          <td align="center"><?
                            $sql= "select *
				from remediar.hta
                                 where cual=2
                                 order by id_hta";
            $refrescar='document.forms[0].submit()';

			 ?>
			<select name=hta2 Style="width:200px"
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer(); sumatoria();"
				onchange="sumatoria();">
			 <option value='-1'>Seleccione</option>


			 <?
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){
			 	$idl=$res_efectores->fields['id_hta'];
			     $descripcion=$res_efectores->fields['descripcion2'];
                               $puntos_2=$res_efectores->fields['puntaje'];
                               list($hta2,$p)=explode("_",$hta2);
			    ?>
				<option value='<?=$idl.'_'.$puntos_2?>' <?if ($hta2==$idl) echo "selected"?> ><?=$descripcion?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select></td>
                      </tr><? }?>
                      <tr>
                          <td style=" padding-left: 40px" width="72%"><b> 3) &iquest;Cuantas veces un m&eacute;dico, una enfermera u otro profesional de la salud<br>
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;le dijo que ten&iacute;a la presi&oacute;n alta?</b></td>
                          <td align="center"><?
                            $sql= "select *
				from remediar.hta
                                 where cual=3";
            $refrescar='document.forms[0].submit()';

			 ?>
			<select name=hta3 Style="width:200px"
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer(); sumatoria();"
				onchange="sumatoria();">
			 <option value='-1'>Seleccione</option>


			 <?
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){
			 	$idl=$res_efectores->fields['id_hta'];
			     $descripcion=$res_efectores->fields['descripcion2'];
                        $puntos_3=$res_efectores->fields['puntaje'];
                        list($hta3,$p)=explode("_",$hta3);
			    ?>
				<option value='<?=$idl.'_'.$puntos_3?>' <?if ($hta3==$idl) echo "selected"?> ><?=$descripcion?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select></td>
                      </tr>
                  </table>
         	</td>
         </tr>
         <tr id="ma">
           <td align="left" colspan="4">
            <b>COLESTEROL</b>
           </td>
         </tr>
         <tr align="center">
         	<td  colspan="4">
         	  <table width="100%" border="1" cellspacing="0" bordercolor="#006699" style="border:thin groove;">
                      <? if($edad>20){ ?>
                      <tr>
                          <td style=" padding-left: 40px" width="72%"><b> 4) En los &uacute;limos 5 a&ntilde;os, &iquest;le midieron el colesterol?<br>
                               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(s&oacute;lo para mayores de 20 a&ntilde;os)</b></td>
                          <td align="center"><?
                            $sql= "select *
				from remediar.colesterol
                                 where cual=4";
            $refrescar='document.forms[0].submit()';

			 ?>
			<select name=colesterol4 Style="width:200px"
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer(); sumatoria();"
				onchange="sumatoria();">
			 <option value='-1'>Seleccione</option>


			 <?
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){
			 	$idl=$res_efectores->fields['id_colesterol'];
			     $descripcion=$res_efectores->fields['descripcion2'];
                                $puntos_4=$res_efectores->fields['puntaje'];
                                list($colesterol4,$p)=explode("_",$colesterol4);
			    ?>
				<option value='<?=$idl.'_'.$puntos_4?>' <?if ($colesterol4==$idl) echo "selected"?> ><?=$descripcion?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select></td>
                      </tr>
                      <? }?>
                      <tr>
                          <td style=" padding-left: 40px" width="72%"><b> 5) &iquest;Alguna vez un m&eacute;dico, una enfermera u otro profesional <br>
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;de la salud le dijo que ten&iacute;a colesterol alto?</b></td>
                          <td align="center"><?
                            $sql= "select *
				from remediar.colesterol
                                 where cual=5";
            $refrescar='document.forms[0].submit()';

			 ?>
			<select name=colesterol5 Style="width:200px"
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer(); sumatoria();"
				onchange="sumatoria();">
			 <option value='-1'>Seleccione</option>


			 <?
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){
			 	$idl=$res_efectores->fields['id_colesterol'];
			     $descripcion=$res_efectores->fields['descripcion2'];
                             $puntos_5=$res_efectores->fields['puntaje'];
                             list($colesterol5,$p)=explode("_",$colesterol5);
			    ?>
				<option value='<?=$idl.'_'.$puntos_5?>' <?if ($colesterol5==$idl) echo "selected"?> ><?=$descripcion?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select></td>
                      </tr>
                  </table>
         	</td>
         </tr>
         <tr id="ma">
           <td align="left" colspan="4">
            <b>DMT2</b>
           </td>
         </tr>
         <tr align="center">
         	<td  colspan="4">
         	  <table width="100%" border="1" cellspacing="0" bordercolor="#006699" style="border:thin groove;">
                      <? if($edad>40){ ?>
                      <tr>
                          <td style=" padding-left: 40px" width="72%"><b> 6) En los &uacute;limos 3 a&ntilde;os, &iquest;le midieron glucemia/az&uacute;car en sangre?<br>
                               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(s&oacute;lo para mayores de 40 a&ntilde;os)</b></td>
                          <td align="center"><?
                            $sql= "select *
				from remediar.dmt2
                                 where cual=6";
            $refrescar='document.forms[0].submit()';

			 ?>
			<select name=dmt26 Style="width:200px"
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer(); sumatoria();"
				onchange="sumatoria();">
			 <option value='-1'>Seleccione</option>


			 <?
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){
			 	$idl=$res_efectores->fields['id_dmt2'];
			     $descripcion=$res_efectores->fields['descripcion2'];
                                   $puntos_6=$res_efectores->fields['puntaje'];
                                   list($dmt26,$p)=explode("_",$dmt26);
			    ?>
				<option value='<?=$idl.'_'.$puntos_6?>' <?if ($dmt26==$idl) echo "selected"?> ><?=$descripcion?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select></td>
                      </tr>
                      <? }?>
                      <tr>
                          <td style=" padding-left: 40px" width="72%"><b> 7) &iquest;Alguna vez un doctor, una enfermera u otro profesional de la salud le<br>
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;dijo que ten&iacute;a diabetes o az&uacute;car alta en la sangre?</b></td>
                          <td align="center"><?
                            $sql= "select *
				from remediar.dmt2
                                 where cual=7";
            $refrescar='document.forms[0].submit()';

			 ?>
			<select name=dmt27 Style="width:200px"
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer(); sumatoria();"
				onchange="sumatoria();">
			 <option value='-1'>Seleccione</option>


			 <?
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){
			 	$idl=$res_efectores->fields['id_dmt2'];
			     $descripcion=$res_efectores->fields['descripcion2'];
                                   $puntos_7=$res_efectores->fields['puntaje'];
                                   list($dmt27,$p)=explode("_",$dmt27);
			    ?>
				<option value='<?=$idl.'_'.$puntos_7?>' <?if ($dmt27==$idl) echo "selected"?> ><?=$descripcion?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select></td>
                      </tr>
                  </table>
         	</td>
         </tr>
         <tr id="ma">
           <td align="left" colspan="4">
            <b>ECV</b>
           </td>
         </tr>
         <tr align="center">
         	<td  colspan="4">
         	  <table width="100%" border="1" cellspacing="0" bordercolor="#006699" style="border:thin groove;">
                      <tr>
                          <td style=" padding-left: 40px" width="72%"><b> 8) &iquest;Ud. o alg&uacute;n familiar directo (padre, madre) tuvo un infarto, ACV<br>
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(ataque cerebral) o problema card&iacute;aco?</b></td>
                          <td align="center"><?
                            $sql= "select *
				from remediar.ecv
                                order by id_ecv";
            $refrescar='document.forms[0].submit()';

			 ?>
			<select name=ecv8 Style="width:200px"
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer(); sumatoria();"
				onchange="sumatoria();">
			 <option value='-1'>Seleccione</option>


			 <?
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){
			 	$idl=$res_efectores->fields['id_ecv'];
			     $descripcion=$res_efectores->fields['descripcion'];
                                $puntos_8=$res_efectores->fields['puntaje'];
                                list($ecv8,$p)=explode("_",$ecv8);
			    ?>
				<option value='<?=$idl.'_'.$puntos_8?>' <?if ($ecv8==$idl) echo "selected"?> ><?=$descripcion?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select></td>
                      </tr>
                  </table>
         	</td>
         </tr>
         <tr id="ma">
           <td align="left" colspan="4">
            <b>TABACO</b>
           </td>
         </tr>
         <tr align="center">
         	<td  colspan="4">
         	  <table width="100%" border="1" cellspacing="0" bordercolor="#006699" style="border:thin groove;">
                      <tr>
                          <td style=" padding-left: 40px" width="72%"><b> 9) &iquest;Ud. fum&oacute; al menos un cigarrillo en los &uacute;ltimos 30 d&iacute;as?</b></td>
                          <td align="center"><?
                            $sql= "select *
				from remediar.tabaco";
            $refrescar='document.forms[0].submit()';

			 ?>
			<select name=tabaco9 Style="width:200px"
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer(); sumatoria();"
				onchange="sumatoria();">
			 <option value='-1'>Seleccione</option>


			 <?
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){
			 	$idl=$res_efectores->fields['id_tabaco'];
			     $descripcion=$res_efectores->fields['descripcion'];
                                    $puntos_9=$res_efectores->fields['puntaje'];
                                    list($tabaco9,$p)=explode("_",$tabaco9);
			    ?>
				<option value='<?=$idl.'_'.$puntos_9?>' <?if ($tabaco9==$idl) echo "selected"?> ><?=$descripcion?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select></td>
                      </tr>
                  </table>
         	</td>
         </tr>
         <tr id="ma">
           <td  colspan="4" >
               <b style=" margin-left: 490px">SUMATORIA &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>

                 <?if ($puntaje_final==0 and $hta2=='-1' and $colesterol4=='-1') $puntaje_final="";?>
                 <input type="text"  value="<?=$puntaje_final?>" name="puntaje_final" readonly size="31">
           </td>
         </tr>
        <tr id="ma">
           <td align="center" colspan="4">
			<button onclick="window.open('busca_promotor.php','Buscar','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');">Buscar</button>&nbsp;<b>Datos del Agente Inscriptor</b>
           </td>
         </tr>
         <tr>
         	<td align="right">
         	  <b>Apellido:</b>
         	</td>
            <td align='left'>
              <input type="text" size="30" value="<?=$apellidoagente?>" name="apellidoagente"  maxlength="50" >
            </td>
           	<td align="right">
         	  <b>Nombre:</b>
         	</td>
            <td align='left'>
              <input type="text" size="30" value="<?=$nombreagente?>" name="nombreagente"  maxlength="50">
            </td>
         </tr>
         <tr>
             <td align="right">
         	  <b>Nro. Doc.:</b>
         	</td>
            <td align='left'>
              <input type="text" size="30" value="<?=$num_doc_agente?>" name="num_doc_agente" maxlength="12">
            </td>
         </tr>
          <tr>
           <td align="center" colspan="4" id="ma">
            <b> Centro Inscriptor </b>
           </td>
         </tr>

         <tr>
         	<td align="right" width="20%" colspan="2">
				<b>Lugar:</b>
			</td>
			<td align="left" width="30%" colspan="2">
			 <select name=cuie Style="width:300px"
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();">
			 <option value=-1>Seleccione</option>
			 <?
			 $sql= "select * from nacer.efe_conv order by nombre";
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){
			 	$cuiec=$res_efectores->fields['cuie'];
			    $nombre_efector=$res_efectores->fields['nombre'];
			    ?>
				<option value='<?=$cuiec?>' <?if ($cuie==$cuiec) echo "selected"?> ><?=$cuiec." - ".$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select><button onclick="window.open('../inscripcion/busca_efector.php','Buscar','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');">b</button>
			</td>

		</tr>
    <?

     echo "<SCRIPT Language='Javascript'> 
             if(form1.num_form_remediar.value=='' && form1.edad.value>0){
                form1.puntaje_final.value=form1.puntos_1.value;
              }
              function sumatoria(){
                  var edad=form1.edad.value; 
                  if(edad<0){
                    document.forms[0].submit();
                  }
                  var p_hta2=0;
                  var p_hta3=0;
                  var p_colesterol4=0;
                  var p_colesterol5=0;
                  var p_dmt26=0;
                  var p_dmt27=0;
                  var p_tabaco9=0;
                  var p_ecv8=0;
                  if(edad>20){
                      var hta2=form1.hta2.value;
                      if(hta2!='-1'){
                        p_hta2=hta2.split('_');
                        p_hta2=p_hta2[1]
                      }
                   }
                  var hta3=form1.hta3.value;
                  if(hta3!='-1'){
                    p_hta3=hta3.split('_');
                    p_hta3=p_hta3[1]
                  }
                  if(edad>20){
                      var colesterol4=form1.colesterol4.value;
                      if(colesterol4!='-1'){
                        p_colesterol4=colesterol4.split('_');
                        p_colesterol4=p_colesterol4[1]
                      }
                   }
                  var colesterol5=form1.colesterol5.value;
                  if(colesterol5!='-1'){
                    p_colesterol5=colesterol5.split('_');
                    p_colesterol5=p_colesterol5[1]
                  }
                  if(edad>40){
                      var dmt26=form1.dmt26.value;
                      if(dmt26!='-1'){
                        p_dmt26=dmt26.split('_');
                        p_dmt26=p_dmt26[1]
                      }
                   }
                  var dmt27=form1.dmt27.value;
                  if(dmt27!='-1'){
                    p_dmt27=dmt27.split('_');
                    p_dmt27=p_dmt27[1]
                  }
                  var ecv8=form1.ecv8.value;
                  if(ecv8!='-1'){
                    p_ecv8=ecv8.split('_');
                    p_ecv8=p_ecv8[1]
                  }
                  var tabaco9=form1.tabaco9.value;
                  if(tabaco9!='-1'){
                    p_tabaco9=tabaco9.split('_');
                    p_tabaco9=p_tabaco9[1]
                  }
                  /*alert(p_colesterol4);*/
                  form1.puntaje_final.value=parseInt(form1.puntos_1.value)+parseInt(p_hta2)+parseInt(p_hta3)+parseInt(p_colesterol4)+parseInt(p_colesterol5)+parseInt(p_dmt26)+parseInt(p_dmt27)+parseInt(p_ecv8)+parseInt(p_tabaco9);
              }
	</SCRIPT>";
?>
        </table>
      </td>
     </tr>

    
	<tr id="mo">
  		<td align=center colspan="2">
  			<b>Guardar Planilla</b>
  		</td>
  	</tr>
  	
  	 <tr align="center">
	 	<td>
	 		<b><font size="0" color="Red">Nota: Verifique todos los datos antes de guardar</font> </b>
	 	</td>
	</tr>
	
    <tr align="center">
       <td>
        <input type='submit' name='guardar_editar' value='Guardar' onclick="return control_nuevos()"title="Guardar datos de la Planilla" <?=$desabil_guardar?>>
       </td>
    </tr>
    
    <?if($pagina_viene_1=="ins_listado_remediar.php"){?>
    	<tr align="center">
	       <td>
	       <input type=button name="volver" value="Volver" onclick="document.location='../inscripcion/ins_listado_remediar.php'"title="Volver al Listado" style="width=150px"> 
	       </td>
    	</tr>    
    <?} ?> 
 </table>
</form>

<script>
var campo_focus=document.all.campo_actual.value;
if(campo_focus==''){
    document.getElementById('campo_actual').value='num_form_remediar';
    campo_focus='num_form_remediar';
}else{
	if(campo_focus=='num_form_remediar'){
		campo_focus='fechaempadronamiento';
		document.getElementById('campo_actual').value='fechaempadronamiento';
	}else{
          campo_focus='os';
		  }
}
document.getElementById(campo_focus).focus();
</script>

<?if(($_POST['guardar_editar']=="Guardar")&&($pagina_viene_1=="ins_admin_old.php")){
    sleep(2);
    echo('<script>window.close();</script>');
    } ?> 

<?=fin_pagina();// aca termino ?>
