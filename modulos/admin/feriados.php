<?
include("../../config.php");
echo $html_header;

$nro=$_POST["hideme"];
$anio=$_POST["anio"] or $anio=date('Y');

if ($_POST['modificar']) {
	$db->StartTrans();	
    $gdia=$_POST["Mdia"];
	$gmes=$_POST["Mmes"];
    $gdesc=$_POST["Mdesc"];
    $nro=$_POST["hideme"];
	$query="UPDATE feriados SET dia='$gdia', mes='$gmes', descripcion='".$gdesc."' WHERE id_fecha=$nro";
    $rs = sql($query,"$query") or fin_pagina();
    $db->CompleteTrans();
}
                    
if ($_POST['nuevo_feriado']) {
	$db->StartTrans();	
    $gdia=$_POST["Mdia"];
    $gmes=$_POST["Mmes"];
    $gdesc=$_POST["Mdesc"];
    $sql_ctrl="select id_fecha from feriados where dia=$gdia and mes=$gmes and
               anio=$anio";
    $res_ctrl=sql($sql_ctrl,"$sql_ctrl") or fin_pagina();
    if ($res_ctrl->RecordCount() == 0) {
       $query="INSERT INTO feriados (dia, mes, descripcion,anio) values ('$gdia','$gmes','$gdesc',$anio);"; 
       $rs = sql($query,"$query") or fin_pagina();
    }
    else Error('EL FERIADO YA ESTA REGISTRADO');
    $db->CompleteTrans();
} 

if ($_POST['borrar_feriado']) {
  	 $db->StartTrans();	
     $nro=$_POST["hideme"];
     $query="DELETE FROM feriados WHERE id_fecha=$nro";
	 $rs = sql($query,"$query") or fin_pagina();
	 $db->CompleteTrans();
} 
	                    
if ($_POST['borrar_por_anio']) {
	 $db->StartTrans();	
     $query="DELETE FROM feriados WHERE anio=$anio"; 
     sql($query,"$query") or fin_pagina();
     $db->CompleteTrans();
}


function cambiar ($m) {
switch ($m){
case 1: {return "Enero";
         break;
         }
case 2: {return "Febrero";
         break;
         }
case 3: {return "Marzo";
         break;
         }
case 4: {return "Abril";
         break;
         } 
case 5: {return "Mayo";
         break;
         } 
case 6: {return "Junio";
         break;
         }
case 7: {return "Julio";
         break;
         } 
case 8: {return "Agosto";
         break;
         } 
case 9: {return "Septiembre";
         break;
         }                                                                    
case 10: {return "Octubre";
         break;
         }
case 11: {return "Noviembre";
         break;
         }    
case 12: {return "Diciembre";
         break;
         }     
default: return $m;         	
}

}

//devuelve el dia de la semana que le correponde d/m/a
// dom=0,lun=1...
function calcula_numero_dia_semana($dia,$mes,$ano){ 
    $nrodiasemana = date('w', mktime(0,0,0,$mes,$dia,$ano));
    return $nrodiasemana;
} 

function ultimoDia($mes,$ano){ 
    $ultimo_dia=28; 
    while (checkdate($mes,$ultimo_dia + 1,$ano)){ 
       $ultimo_dia++; 
    } 
    return $ultimo_dia; 
} 


?>

<? cargar_calendario(); ?>
<script language="javascript">

function mostrar(id){
document.form1.hideme.value = id;
obj_dia=eval("document.all.dia_" + id);
obj_mes=eval("document.all.mes_" + id);
obj_desc=eval("document.all.desc_" + id);
document.form1.Mdia.options[obj_dia.value].selected=true;
document.form1.Mmes.options[obj_mes.value].selected=true;
document.form1.Mdesc.value=obj_desc.value;

}

function control(){

if (((document.all.Mmes.options[document.all.Mmes.selectedIndex].value==4) 
     ||(document.all.Mmes.options[document.all.Mmes.selectedIndex].value==6) 
     ||(document.all.Mmes.options[document.all.Mmes.selectedIndex].value==9)
     || (document.all.Mmes.options[document.all.Mmes.selectedIndex].value==11)) 
     && (document.all.Mdia.options[document.all.Mdia.selectedIndex].value==31)){
	alert ("El mes seleccionado tiene solo 30 dias");
	return false;
	}
	
if  ((document.all.Mmes.options[document.all.Mmes.selectedIndex].value==2) 
     && (document.all.Mdia.options[document.all.Mdia.selectedIndex].value > document.all.form1.febrero.value)){
     alert ("Febrero solo tiene " + document.all.form1.febrero.value + " dias" );
	 return false;
   }
return true;	 
}

</script>

<div align="center">
<form name="form1" method="post" action="feriados.php">
<?
$ini=1998;
$fin=2010;

$ssql="SELECT id_fecha,dia,mes,descripcion FROM feriados where anio=$anio ORDER BY mes,dia";
$rs = sql($ssql) or fin_pagina();

?>
<br>
  <table width="655" border="0">
  <tr>
    <td align="center" colspan=2>
    <b>Año: </b>
    <select name="anio" onchange="document.all.form1.submit();">
       <?for($i=$ini;$i<=$fin;$i++) {?>
          <option value='<?=$i?>' <?if ($anio==$i) echo 'selected'?>> <?=$i?></option>
        <?}?>
    </select>
     &nbsp;&nbsp;&nbsp;
     <?if (permisos_check("inicio","permiso_borrar_feriados")) { ?>
      <input type="submit" name="borrar_por_anio" value="Borrar por año"> 
     <? }?>
    </td>
  </tr>
    <tr> 
      <td height="199" width="321"> 
        <table width="98%" border="1" >
          <tr bgcolor="#5090C0"> 
            <td width="17%">MODIF </td>
            <td width="11%">DIA </td>
            <td width="13%">MES </td>
            <td width="57%">DESCRIPCION </td>
          </tr>
          <?php

// buscar filas sucesivas en el resultado
  while(!$rs->EOF) {
        $dia= $rs->fields["dia"];
        $mes= $rs->fields["mes"];
        $mes1=cambiar ($mes);
        $descripcion= $rs->fields["descripcion"];
		$id= $rs->fields["id_fecha"];
		?>
          <input type="hidden" name="<?php echo "dia_".$id?>"  value="<?php echo $dia?>">
          <input type="hidden" name="<?php echo "mes_".$id?>"  value="<?php echo $mes?>">
          <input type="hidden" name="<?php echo "desc_".$id?>" value="<?php echo $descripcion?>">
          <input type="hidden" name="<?php echo "id_".$id?>" value="<?php echo $id?>">
          <tr> 
            <td width="19%"> 
              <div align="center">
                <input type="radio" name="fecha" class="estilos_check" value="<?php echo $id ?>" onclick="mostrar(this.value)">
              </div>
            </td>
            <td width="11%"> 
              <?php  echo $dia ?>
            </td>
            <td width="13%"> 
              <?php  echo $mes1 ?>
            </td>
            <td width="57%"> 
              <?php  echo $descripcion ?>
            </td>
          </tr>
          <?php
  $rs->MoveNext();
      }
  ?>
  </table>
      </td>
      <td width="291" valign="top"> 
        <table width="93%" border="1">
		 <tr bgcolor="#5090C0"> <td>
              <div align="center">DATOS SELECCIONADOS </div>
            </td> </tr>
          <tr> 
            <td valign="top" height="32" width="308">DIA 
              <select name="Mdia">
			  <option value="0">Seleccione dia</option>
			   <option value="1">1 </option>
			   <option value="2">2 </option>
			   <option value="3">3 </option>
			   <option value="4">4 </option>
			   <option value="5">5 </option>
			   <option value="6">6 </option>
			   <option value="7">7 </option>
			   <option value="8">8 </option>
			   <option value="9">9 </option>
			   <option value="10">10 </option>
			   <option value="11">11 </option>
			   <option value="12">12 </option>
			   <option value="13">13 </option>
			   <option value="14">14 </option>
			   <option value="15">15 </option>
			   <option value="16">16 </option>
			   <option value="17">17 </option>
			   <option value="18">18 </option>
			   <option value="19">19 </option>
			   <option value="20">20 </option>
			   <option value="21">21 </option>
			   <option value="22">22 </option>
			   <option value="23">23 </option>
			   <option value="24">24 </option>
			   <option value="25">25 </option>
			   <option value="26">26 </option>
			   <option value="27">27 </option>
			   <option value="28">28 </option>
			   <option value="29">29 </option>
			   <option value="30">30 </option>
			   <option value="31">31 </option>
              </select>
			  
          </tr>
          <tr> 
            <td height="32" valign="top">MES 
              
			  <select name="Mmes">
			  <option value="0">Seleccione mes</option>
			  <option value="1">Enero</option>
			   <option value="2">Febrero</option>
			   <option value="3">Marzo</option>
			   <option value="4">Abril</option>
			   <option value="5">Mayo</option>
			   <option value="6">Junio</option>
			   <option value="7">Julio</option>
			   <option value="8">Agosto</option>
			   <option value="9">Septiembre</option>
			   <option value="10">Octubre</option>
			   <option value="11">Noviembre</option>
			   <option value="12">Diciembre</option>
              </select>
            </td>
          </tr>
          <tr> 
            <td height="51" valign="top">DESCRIPCION 
              <input type="text" name="Mdesc" value="<?php echo $_POST["desc_".$nro] ?>">
            </td>
          </tr>
          <tr> 
            <td valign="top" height="37" align="center"> 
              <input type="submit" name="modificar" value="Modificar">
              <input type="submit" name="nuevo_feriado" value="Nuevo feriado" onclick='return control();'>
              <input type="submit" name="borrar_feriado" value="Borrar feriado">
            </td>
            </tr>
        </table>
      </td>
      </tr>
  </table>


<a href='http://www.mininterior.gov.ar/servicios/feriados.asp' target='_new'><b>Ministerio del Interior</b></a>
<br><br>
<input type="hidden" name="hideme" value="0">
<input type="hidden" name="febrero" value="<?=ultimoDia(2,$anio)?>">
<input type="text" name="f_entrega" value=<?php echo $_POST['f_entrega']; ?>><?php echo link_calendario("f_entrega"); ?>
 
</form>
</div>
</body>
</html>
