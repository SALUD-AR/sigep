<?


require_once("../../config.php");

echo $html_header;

//retorna el indice del campo fecha 'dd/mm/yyyy'
function retorna_indice($array,$campo) {
$cant=count($array);
for($i=0;$i<$cant;$i++) {
   if ($array[$i]['fecha']==$campo) return $i;
}
}

//formato de fecha dd/mm/año
function dia_anterior($fecha){
$fecha_aux=$fecha;
$dia_anterior=0;

while(!$dia_anterior) {
  $fecha_total=split("/",$fecha_aux);
  $dfecha=date("d/m/Y",mktime(0,0,0,$fecha_total[1],$fecha_total[0]-1,$fecha_total[2]));
  $fecha_aux=date("d/m/Y/w",mktime(0,0,0,$fecha_total[1],$fecha_total[0]-1,$fecha_total[2]));
  $fecha_test=split("/",$fecha_aux);
  if($fecha_test[3]!=0 || $fecha_test[3]!=6)
      $dia_anterior=1;
}
$fecha_retornar=split("/",$fecha_aux);
$a=date("d/m/Y",mktime(0,0,0,$fecha_retornar[1],$fecha_retornar[0],$fecha_retornar[2]));
return $a;
}


/* calcula el proximo dia
argumento $fecha es dd/mm/aaaa */
function dia_posterior($fecha){
$fecha_aux=$fecha;
$feriado=0;
  $fecha_total=split("/",$fecha_aux);
  $dfecha=date("d/m/Y",mktime(0,0,0,$fecha_total[1],$fecha_total[0]+1,$fecha_total[2]));
  $fecha_aux=date("d/m/Y/w",mktime(0,0,0,$fecha_total[1],$fecha_total[0]+1,$fecha_total[2]));
  $fecha_test=split("/",$fecha_aux);

$fecha_retornar=split("/",$fecha_aux);
$a=date("d/m/Y",mktime(0,0,0,$fecha_retornar[1],$fecha_retornar[0],$fecha_retornar[2]));
return $a;
}



if ($_POST['guardar'] || $_POST['resumen']) {
//guardar el dolar de la fecha del dia

  $usuario=$_ses_user['name'];
  $fecha_actual=date("Y-m-d");
  $fechas_mostradas=descomprimir_variable($_POST['fechas_mostradas']);
  $value=$_POST['ind'];
  if ($ind !=-1) {
   $dolar=$_POST["dolar_".$value];
   if ($dolar=='null' || $dolar=="") {
    Error ("Ingrese un valor valido para el dolar");
   }
  if (!$error) {
   $sql="select valor_dolar from dolar_comparacion where fecha='$fecha_actual'";
   $res_fec=sql($sql) or fin_pagina();
    $sql="";
      if ($res_fec->RecordCount() > 0) {
      	$sql[]="update dolar_comparacion set valor_dolar=".$dolar." ,usuario='$usuario' ,fecha_cambio= '$fecha_actual'
                 where fecha='$fecha_actual'";

      } else {
          $sql[]="insert into dolar_comparacion (fecha,valor_dolar,usuario,fecha_cambio)
         values('$fecha_actual',".$dolar.",'$usuario','$fecha_actual')";
        }

	  if($dolar>0 && $dolar!="")
	  {$sql[]="update dolar_general set valor=$dolar";
	   sql($sql) or fin_pagina();
	  }
  }
  }

if ($_POST['resumen'] && !$error ) { //envia resumen de cheques
$chk=PostvartoArray('dia_'); //crea un arreglo con los checkbox chequeados
$control=0;
if ($chk) {

  	$contenido= "       RESUMEN DE CHEQUES DEBITADOS \n \n";
foreach($chk as $key => $value) {
  	$fecha=fecha_db($fechas_mostradas[$value]['fecha']);

  	$sql_dolar=" select valor_dolar from dolar_comparacion where fecha='$fecha'";
  	$res_dolar=sql($sql_dolar,"al seleccionar valor dolar") or fin_pagina();
  	$sql="select númeroch,fechaemich,fechavtoch,importech,id_pago,id_moneda,razon_social,
		  ordenes_pagos.valor_dolar,ordenes_pagos.monto,razon_social,nro_orden
		  from cheques
	      join ordenes_pagos using (númeroch,idbanco)
		  join proveedor on proveedor.id_proveedor=cheques.idprov
		  join pago_orden using (id_pago)
		  join orden_de_compra using (nro_orden)
		  WHERE FechaDébCh='$fecha' and id_moneda=2";
  	$res=sql($sql) or fin_pagina();

    $ch_ant="";
  	if ($res->RecordCount() > 0) {
  	$control=1;
  	while (!$res->EOF) {
  	  if ($ch_ant != $res->fields['númeroch'] ) {
  	  $contenido.=$contenido1;
  	  $contenido1="";
  	  $contenido.= "Número Cheque: ".$res->fields['númeroch'];
  	  $contenido.= ", Monto: U\$S ".formato_money($res->fields['monto']);
  	  $contenido.= ", Proveedor ".$res->fields['razon_social']."\n";
  	  $contenido.= " Emitido el día:  ".fecha($res->fields['fechaemich']);
  	  $contenido.= ", Con Vencimiento:  ".fecha($res->fields['fechavtoch'])."\n";
  	  $contenido.= "Empleado en el pago de la Orden Número:  ".$res->fields['nro_orden'];

  	  $contenido1.= ", Con Valor Dolar ".formato_money($res->fields['valor_dolar']);
  	  $contenido1.= ", Debitado el día ".fecha($fecha);
  	  $contenido1.= " con valor de Dólar  ".formato_money($res_dolar->fields['valor_dolar'])."\n";


  	  $diff=($res->fields['monto'] * $res->fields['valor_dolar']) * (1- ($res_dolar->fields['valor_dolar']/$res->fields['valor_dolar']) );

  	  if ($diff < 0)
  	    $contenido1.=" \nSALDO DEUDOR  $ ". formato_money(abs($diff));
  	  elseif ($diff > 0 )
  	   $contenido1.="\nSALDO A FAVOR  $ ". formato_money($diff);
  	  else $contenido1.= "\n SIN SALDO";

  	   $contenido1.="\n ----------------------------------------------------------------------------------------------\n\n";

  	 }
  	 else {
  	     $contenido.=",".$res->fields['nro_orden'];

  	 }
  	 $ch_ant = $res->fields['númeroch'];
  	 $res->MoveNext();

  	}
  	}
   }
$contenido.=$contenido1;

if ($control==0) {
$contenido= 'No hay cheques debitados asociados a ordenes de compra en la/s fecha/s seleccionada/s.';
}
$para=$_ses_user['mail'];
$asunto=" RESUMEN CHEQUES DEBITADOS ";

enviar_mail($para,$asunto,$contenido,'','','',0);
}


}  //FIN POST RESUMEN

}  //fin POST GUARDAR Y RESUMEN


$fecha_actual=date("d/m/Y");
$anio_inicial=1999;
$anio_final=2020;
$mes=$_POST['meses'] or $mes=date("n");
$anio=$_POST['anios'] or $anio=date("Y");
$numero_dias=date("t",mktime(0,0,0,$mes,1,$anio));  //numero de dias del mes seleccionado


$comienzo_mes= date('w', mktime(0,0,0,$mes,1,$anio));  //primer dia del mes "0" (domingo) a "6" (sábado)
$fin_mes=date('w', mktime(0,0,0,$mes,$numero_dias,$anio));  //ultimo dia del mes "0" (domingo) a "6" (sábado)

if ($fin_mes==0) $dias_posteriores=$fin_mes;
 else
   $dias_posteriores=5-$fin_mes;

$dias_semana=array();
$dias_semana[1]='Lunes';
$dias_semana[2]='Martes';
$dias_semana[3]='Miercoles';
$dias_semana[4]='Jueves';
$dias_semana[5]='Viernes';

if ($mes < 10 ) $m='0'.$mes;
  else $m=$mes;

$fecha='01/'.$m.'/'.$anio;

$j=$comienzo_mes;//numero de dia en el que comienza el mes "0" (domingo) a "6" (sábado)
   $i=1;
   if ($j==6) { //es sabado
    	$j=1;
    	$i=3;
    	$restar=2;
    	$fecha_inicio='03/'.$m.'/'.$anio;
    } elseif ($j==0) { //es domingo
         $j++;
    	 $i=2;
    	 $fecha_inicio='02/'.$m.'/'.$anio;
    	 $restar=1;
    }
    else {
    	 $fecha_inicio='01/'.$m.'/'.$anio;
    	 $restar=0;
    }

    if ($mes < 10 ) $m='0'.$mes;
      else $m=$mes;

   $dias_anteriores=0;
   $fecha_ant="";
   for($ind=$j-1;$ind >= 1;$ind--) {
      $fecha_ant=dia_anterior($fecha);
      $fecha=$fecha_ant;
      $dias_anteriores++;
   }
  if ($fecha_ant !="") $fecha_inicio=$fecha_ant;

  $array_fechas=array();

  for ($ind=0; $ind <25;$ind++) {
  	$fec=split("/",$fecha);
    $num=date('w', mktime(0,0,0,$fec[1],$fec[0],$fec[2]));  //dia  "0" (domingo) a "6" (sábado)
    while ($num==0 || $num==6) {
        $fecha=dia_posterior($fecha);
        $fec=split("/",$fecha);
        $num=date('w', mktime(0,0,0,$fec[1],$fec[0],$fec[2]));  //dia  "0" (domingo) a "6" (sábado)

    }
     $array_fechas[$ind]['fecha']=$fecha;
     $array_fechas[$ind]['num']=$dias_semana[$num];
     $array_fechas[$ind]['dolar']="";
     $array_fechas[$ind]['comentario']="";
     $fecha=dia_posterior($fecha);
  }

   $cant=count($array_fechas);
   $list='(';
   for($i=0;$i<$cant;$i++){
      $list.="'".fecha_db($array_fechas["$i"]['fecha'])."'".',';
      }
   $list=substr_replace($list,')',(strrpos($list,',')));

   $sql="select valor_dolar,fecha,comentario from dolar_comparacion where fecha in $list";
   $res=sql($sql) or fin_pagina ();
   while (!$res->EOF) {
   $i=retorna_indice($array_fechas,fecha($res->fields['fecha']));

   $array_fechas[$i]['dolar']=$res->fields['valor_dolar'];
   $array_fechas[$i]['comentario']=$res->fields['comentario'];
   $res->MoveNext();
   }



?>

<script>
function activar(nombre,valor) //activa valor del select objeto
{
var objetoc=eval('window.document.form1.'+nombre); //obtengo objeto select a cambiar
for (var i=0;i<objetoc.length;i++)
{
if (objetoc.options[i].value==valor)
  objetoc.options[i].selected=true;
}
}

function mes_anterior() {
mes_sel=document.all.meses.options[document.all.meses.selectedIndex].value
anio_sel=document.all.anios.options[document.all.anios.selectedIndex].value

if (mes_sel==1){
	 mes_sel=12;
	 anio_sel--;
	 activar('anios',anio_sel);
}
else mes_sel--;
if ((anio_sel == <?=$anio_inicial-1?>) && (mes_sel==12)) {
  alert ('Datos desde Enero de <?=$anio_inicial?>');
  return 0
} else
  activar('meses',mes_sel);
return 1
}


function mes_posterior() {
mes_sel=document.all.meses.options[document.all.meses.selectedIndex].value
anio_sel=document.all.anios.options[document.all.anios.selectedIndex].value

if (mes_sel==12) {
	 mes_sel=1;
	 anio_sel++;
	 activar('anios',anio_sel);
}
else {
	mes_sel++;
}


if ((anio_sel == <?=$anio_final+1?>) && (mes_sel==1)) {
  alert ('Datos hasta Diciembre de <?=$anio_final?> ');
  return 0
} else
  activar('meses',mes_sel);

return 1;
}

function control_chk() {
var i,ctrl=0;
for (i=0;i<25;i++) {
c=eval("document.all.dia_"+i);
   if  (c.checked) return true;
}

alert ("DEBE SELECCIONAR AL MENOS UNA FECHA ");
return false;

}



</script>

<form name='form1' action="resumen_dolar.php" method="post">
<br>
<table align=center bgcolor="White" cellpadding="3">
 <tr>
  <td>
   <table><tr>
          <td><img src='<?="$html_root/imagenes/left2.gif" ?>' border="0" title="" onClick='mes_anterior();document.all.form1.submit();'> </td>
          <td> MES <select name=meses onKeypress='buscar_op(this);' onblur='borrar_buffer()' onclick='borrar_buffer()'>
	   	 	  <option value=1 <?if (1==$mes) echo 'selected'?>> Enero </option>
	   	 	  <option value=2 <?if (2==$mes) echo 'selected'?>> Febrero </option>
	   	 	  <option value=3 <?if (3==$mes) echo 'selected'?>> Marzo </option>
	   	 	  <option value=4 <?if (4==$mes) echo 'selected'?>> Abril</option>
	   	 	  <option value=5 <?if (5==$mes) echo 'selected'?>> Mayo </option>
	   	 	  <option value=6 <?if (6==$mes) echo 'selected'?>> Junio </option>
	   	 	  <option value=7 <?if (7==$mes) echo 'selected'?>> Julio </option>
	   	 	  <option value=8 <?if (8==$mes) echo 'selected'?>> Agosto </option>
	   	 	  <option value=9 <?if (9==$mes) echo 'selected'?> > Septiembre </option>
	   	 	  <option value=10 <?if (10==$mes) echo 'selected'?>> Octubre </option>
	   	 	  <option value=11 <?if (11==$mes) echo 'selected'?>> Noviembre </option>
	   	 	  <option value=12 <?if (12==$mes) echo 'selected'?>> Diciembre </option>

	   	 	 </select>
        </td>

    <td> AÑO <select name=anios onKeypress='buscar_op(this);' onblur='borrar_buffer()' onclick='borrar_buffer()'>
	   <? for ($i=$anio_inicial;$i<=$anio_final;$i++) { ?>
	        <option value=<?=$i?> <?if ($i==$anio) echo 'selected'?>> <?=$i ?> </option>
	   <? } ?>
	   	 </select>
   </td>
 <td> <img src='<?="$html_root/imagenes/right2.gif" ?>' border="0" title="" onClick='mes_posterior();document.all.form1.submit();'> </td>
 </tr>
</table>
</td>
 <td> <input type='submit' name='actualizar' value='Actualizar' ></td>
 </tr>
</table>
<br>


<?
$i=0;
$ind=-1;
?>
<input type="hidden" name="cambiar" value='-1'>
<input type="hidden" name="fecha_act" value=''>

<table align='center' border=1>
<tr>
  <td>
     <table >
        <tr id=mo>
           <td>&nbsp;</td>
           <td> DIA </td>
           <td> VALOR </td>
           <td>&nbsp;</td>
        </tr>
        <?
        for ($j=0;$j<5;$j++) {
        	$comp_fec=compara_fechas(fecha_db($fecha_actual),fecha_db($array_fechas[$i]['fecha']));?>
        <tr <?if ($comp_fec==0) echo "bgcolor='#009999'";elseif ($i<$dias_anteriores || (25-$i <= $dias_posteriores)) echo "bgcolor='#FFCCCC'"; else echo "bgcolor='$bgcolor_out'" ?>>
          <td title="enviar resumen">
              <input type="checkbox"  class="estilos_check"   name='dia_<?=$i?>' value='<?=$i?>' <? if ($comp_fec ==0) { echo 'checked'; $c=0;$ind=$i;} else $c=1;?> onclick=' <? if ($array_fechas[$i]['dolar']==null || $array_fechas[$i]['dolar']=='') {?> alert ("Debe asignar valor al dolar para obtener un resumen");this.checked=false; <?}?>'>
          </td>
          <td> <?=$array_fechas[$i]['num']." ".substr($array_fechas[$i]['fecha'],0,5)?></td>
          <td> <input type="text" name='dolar_<?=$i?>' value='<?=number_format($array_fechas[$i]['dolar'],'2','.','')?>' size=4 ></td>
          <td> <? if ($c==1) {
          	 $com=$array_fechas[$i]['comentario'];
          	 $link1=encode_link('cambiar_dolar.php',array("fecha"=>$array_fechas[$i]['fecha'],"dolar"=>$array_fechas[$i]['dolar']));
             $onclick="ventana=window.open(\"$link1\",\"\",\"left=120,top=80,width=450,height=200,resizable=1,status=1\")";
            	 echo "<input type='button'  title='$com' name='cambiar_<?=$i?>' value='c' onclick='$onclick' >";
             } else echo "&nbsp;" ?>
          </td>
        </tr>
        <?
          $i++;
         }
        ?>
     </table>
  </td>
  <td>
     <table>
        <tr id=mo>
          <td>&nbsp;</td>
          <td> DIA </td>
          <td> VALOR </td>
          <td>&nbsp;</td>
        </tr>
         <?
        for ($j=0;$j<5;$j++) {
        	$comp_fec=compara_fechas(fecha_db($fecha_actual),fecha_db($array_fechas[$i]['fecha']));	 ?>
        <tr <?if ($comp_fec==0) echo "bgcolor='#009999'";elseif ($i<$dias_anteriores || (25-$i <= $dias_posteriores)) echo "bgcolor='#FFCCCC'"; else echo "bgcolor='$bgcolor_out'" ?>>
          <td title="enviar resumen">
              <input type="checkbox" name='dia_<?=$i?>' class="estilos_check" value='<?=$i?>' <? if ( $comp_fec==0) { echo 'checked'; $c=0;$ind=$i;} else $c=1?> onclick='<? if ($array_fechas[$i]['dolar']==null || $array_fechas[$i]['dolar']=='') { ?> alert ("Debe asignar valor al dolar para obtener un resumen");this.checked=false; <? }?>'>
         </td>
          <td> <?=$array_fechas[$i]['num']." ".substr($array_fechas[$i]['fecha'],0,5)?></td>
          <td> <input type="text" name='dolar_<?=$i?>' value='<?=number_format($array_fechas[$i]['dolar'],'2','.','')?>' size=4 ></td>
           <td> <? if ($c==1) {
           	 $com=$array_fechas[$i]['comentario'];
          	 $link1=encode_link('cambiar_dolar.php',array("fecha"=>$array_fechas[$i]['fecha'],"dolar"=>$array_fechas[$i]['dolar']));
             $onclick="ventana=window.open(\"$link1\",\"\",\"left=120,top=80,width=450,height=200,resizable=1,status=1\")";
            	 echo "<input type='button'  title='$com' name='cambiar_<?=$i?>' value='c' onclick='$onclick' >";
             } else echo "&nbsp;" ?>
              </td>
        </tr>
        <?
          $i++;
         }
        ?>

     </table>
  </td>
  <td>
     <table>
        <tr id=mo>
          <td>&nbsp;</td>
          <td> DIA </td>
          <td> VALOR </td>
          <td>&nbsp;</td>
        </tr>
         <?
        for ($j=0;$j<5;$j++) {
        	$comp_fec=compara_fechas(fecha_db($fecha_actual),fecha_db($array_fechas[$i]['fecha']))
        	?>
        <tr <?if ($comp_fec==0) echo "bgcolor='#009999'";elseif ($i<$dias_anteriores || (25-$i <= $dias_posteriores)) echo "bgcolor='#FFCCCC'"; else echo "bgcolor='$bgcolor_out'" ?>>
          <td title="enviar resumen" >
             <input type="checkbox" class="estilos_check" name='dia_<?=$i?>' value='<?=$i?>' <? if ( $comp_fec==0) {echo 'checked'; $c=0; $ind=$i;} else $c=1;?> onclick='<? if ($array_fechas[$i]['dolar']==null || $array_fechas[$i]['dolar']=='') {?> alert ("Debe asignar valor al dolar para obtener un resumen");this.checked=false; <? }?>'>
           </td>
          <td> <?=$array_fechas[$i]['num']." ".substr($array_fechas[$i]['fecha'],0,5)?></td>
          <td> <input type="text" name='dolar_<?=$i?>' value='<?=number_format($array_fechas[$i]['dolar'],'2','.','')?>' size=4 ></td>
           <td> <? if ($c==1) {
           	 $com=$array_fechas[$i]['comentario'];
          	 $link1=encode_link('cambiar_dolar.php',array("fecha"=>$array_fechas[$i]['fecha'],"dolar"=>$array_fechas[$i]['dolar']));
             $onclick="ventana=window.open(\"$link1\",\"\",\"left=120,top=80,width=450,height=200,resizable=1,status=1\")";
            	 echo "<input type='button'  title='$com' name='cambiar_<?=$i?>' value='c' onclick='$onclick' >";
             } else echo "&nbsp;" ?>
           </td>
        </tr>
        <?
          $i++;
         }
        ?>
     </table>
  </td>
  <td>
     <table>
        <tr id=mo>
          <td>&nbsp;</td>
          <td> DIA </td>
          <td> VALOR </td>
          <td>&nbsp;</td>
        </tr>
         <?
        for ($j=0;$j<5;$j++) {
        	$comp_fec=compara_fechas(fecha_db($fecha_actual),fecha_db($array_fechas[$i]['fecha']));?>
        <tr <?if ($comp_fec==0) echo "bgcolor='#009999'";elseif ($i<$dias_anteriores || (25-$i <= $dias_posteriores)) echo "bgcolor='#FFCCCC'"; else echo "bgcolor='$bgcolor_out'" ?>>
          <td title="enviar resumen">
              <input type="checkbox"  class="estilos_check"  name='dia_<?=$i?>' value='<?=$i?>' <? if ($comp_fec==0)  {echo 'checked'; $c=0;$ind=$i;} else $c=1?>  onclick='<? if ($array_fechas[$i]['dolar']==null || $array_fechas[$i]['dolar']=='' ) {?> alert ("Debe asignar valor al dolar para obtener un resumen");this.checked=false; <? }?>'>
           </td>
          <td> <?=$array_fechas[$i]['num']." ".substr($array_fechas[$i]['fecha'],0,5)?></td>
          <td> <input type="text" name='dolar_<?=$i?>' value='<?=number_format($array_fechas[$i]['dolar'],'2','.','')?>' size=4 ></td>
           <td> <? if ($c==1) {
           	 $com=$array_fechas[$i]['comentario'];
          	 $link1=encode_link('cambiar_dolar.php',array("fecha"=>$array_fechas[$i]['fecha'],"dolar"=>$array_fechas[$i]['dolar']));
             $onclick="ventana=window.open(\"$link1\",\"\",\"left=120,top=80,width=450,height=200,resizable=1,status=1\")";
            	 echo "<input type='button' name='cambiar_<?=$i?>' title='$com' value='c' onclick='$onclick' >";
             } else echo "&nbsp;" ?>
            </td>
        </tr>
        <?
          $i++;
         }
        ?>
     </table>
  </td>
  <td>
     <table>
        <tr id=mo>
          <td>&nbsp;</td>
          <td> DIA </td>
          <td> VALOR </td>
          <td>&nbsp;</td>
        </tr>
         <?
        for ($j=0;$j<5;$j++) {
        	$comp_fec=compara_fechas(fecha_db($fecha_actual),fecha_db($array_fechas[$i]['fecha'])); ?>
        <tr <?if ($comp_fec==0) echo "bgcolor='#009999'";elseif ($i<$dias_anteriores || (25-$i <= $dias_posteriores)) echo "bgcolor='#FFCCCC'"; else echo "bgcolor='$bgcolor_out'" ?>>
          <td title="enviar resumen">
                <input type="checkbox"  class="estilos_check" name='dia_<?=$i?>' value='<?=$i?>' <? if ( $comp_fec==0) { echo 'checked';$c=0; $ind=$i;} else $c=1;?>  onclick='<? if ($array_fechas[$i]['dolar']==null || $array_fechas[$i]['dolar']=='') { ?> alert ("Debe asignar valor al dolar para obtener un resumen");this.checked=false; <? } ?>'>
          </td>
          <td> <?=$array_fechas[$i]['num']." ".substr($array_fechas[$i]['fecha'],0,5)?></td>
          <td> <input type="text" name='dolar_<?=$i?>' value='<?=number_format($array_fechas[$i]['dolar'],'2','.','')?>' size=4 ></td>
          <td> <? if ($c==1) {
          	 $com=$array_fechas[$i]['comentario'];
          	 $link1=encode_link('cambiar_dolar.php',array("fecha"=>$array_fechas[$i]['fecha'],"dolar"=>$array_fechas[$i]['dolar']));
             $onclick="ventana=window.open(\"$link1\",\"\",\"left=120,top=80,width=450,height=200,resizable=1,status=1\")";
            	 echo "<input type='button' title='$com' name='cambiar_<?=$i?>' value='c' onclick='$onclick' >";
             } else echo "&nbsp;" ?>
          </td>
        </tr>
        <?
          $i++;
         }
        ?>
     </table>
  </td>

</tr>
</table>

<?$fechas_most=comprimir_variable($array_fechas); ?>

<input type="hidden" name='fechas_mostradas' value='<?=$fechas_most?>'>
<input type="hidden" name='fecha_inicio' value='<?=$fecha_inicio?>'>
<input type="hidden" name='ind' value='<?=$ind?>'>
<br>

<table align="center">
  <tr>
   <? if ($ind !=-1) { //fecha del dia aprarece chequeda ?>
     <td> <input type="submit" name="guardar"  title='guardar el valor del dolar <?=$fecha_actual?>' value='Guardar' onclick='if ((isNaN(document.all.dolar_<?=$ind?>.value)) ||  (document.all.dolar_<?=$ind?>.value=="") ){ alert ("ingrese un valor valido para el dolar");return false;}else return true'> </td>
     <td> <input type="submit" name="resumen" value='Enviar Resumen' onclick='if ( ((isNaN(document.all.dolar_<?=$ind?>.value)) ||  (document.all.dolar_<?=$ind?>.value=="") )){ alert ("ingrese un valor valido para el dolar");return false;} else if (control_chk()) return true; else return false;'> </td>
   <? }
      else  { // no esta en el listado fecha del dia?>
     <td> <input type="submit" name="resumen" value='Enviar Resumen' onclick='if (control_chk()) return true; else return false;'> </td>
      <? } ?>
  </tr>
</table>



</form>