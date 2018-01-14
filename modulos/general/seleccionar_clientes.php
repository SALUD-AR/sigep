<?

require_once("../../config.php");

extract($_POST,EXTR_SKIP);

function tabla_filtros_nombres() {

 $abc=array("a","b","c","d","e","f","g","h","i",
            "j","k","l","m","n","ñ","o","p","q",
            "r","s","t","u","v","w","x","y","z");
$cantidad=count($abc);

echo "<table  align='center' width='80%' height='80%' id='mo'>";
echo "<input type=hidden name='filtro' value='".$_POST['filtro']."'";
    echo "<tr>";
    for($i=0;$i<$cantidad;$i++){
        $letra=$abc[$i];
       switch ($i) {
                     case 9:
                     case 18:
                     case 27:echo "</tr><tr>";
                          break;
                   default:
                  } //del switch

echo "<td style='cursor:hand' onclick=\"document.all.filtro.value='$letra'; document.form1.submit();\">$letra</td>";
      }//del for
   echo "</tr>";
   echo "<tr>";
    echo "<td colspan='9' style='cursor:hand' onclick=\"document.all.filtro.value='Todas'; document.form1.submit();\"> Todos";
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td colspan='9' style='cursor:hand' onclick=\"document.all.filtro.value=''; document.form1.submit();\"> Los mas Usados";
    echo "</td>";
   echo "</tr>";
   echo "</table>";
}  //de la funcion


if ($parametros)
 extract($parametros,EXTR_OVERWRITE);
 
if ($_POST['guardar']) {
 $db->StartTrans();
 $id_entidad=$_POST['select_cliente'];
 $nombre=$_POST['nbrecl'];
 $direccion=$_POST['direccion'];
 $telefono=$_POST['telefono'];
 $mail=$_POST['email'];
 $cuit=$_POST['cuit'];
 $iib=$_POST['iib'];
 $select_condicion_iva=$_POST['condicioniva'];
 $select_tasa_iva=$_POST['iva'];
 
 $query="update licitaciones.entidad set nombre='$nombre',direccion='$direccion',
         telefono='$telefono',mail='$mail',cuit='$cuit',id_condicion=$select_condicion_iva,
         iib='$iib',id_iva=$select_tasa_iva
         where id_entidad=$id_entidad";
 sql($query) or fin_pagina();
 
 if ($db->CompleteTrans())
	$informar="<center><b>La entidad \"$nombre\" fue actualizada con éxito</b></center>";
 else
	$informar="<center><b>La entidad \"$nombre\" no se pudo actualizar</b></center>";
}


if($filtro!="") {
	if ($filtro=='Todas') 
        $q="select entidad.*,
               condicion_iva.nombre as condicioniva,tasa_iva.porcentaje as iva
               from licitaciones.entidad
               left join general.tasa_iva using (id_iva)
               left join general.condicion_iva using (id_condicion) 
            order by entidad.nombre";
    else   
           $q="select entidad.*,
               condicion_iva.nombre as condicioniva,tasa_iva.porcentaje as iva
               from licitaciones.entidad
               left join general.tasa_iva using (id_iva)
               left join general.condicion_iva using (id_condicion) 
               where entidad.nombre ilike '$filtro%' and activo_entidad=1 order by entidad.nombre";

   $clientes=sql($q,"No se pudo realizar la consulta que trae los clientes") or fin_pagina();   	
  }
else{
$id_usuario=$_ses_user['id'];	
$sql="select entidad.*,
      condicion_iva.nombre as condicioniva,tasa_iva.porcentaje as iva 
	  from licitaciones.usuarios_clientes 
	  left join licitaciones.entidad using(id_entidad)
	  left join general.tasa_iva using (id_iva)
	  left join general.condicion_iva using (id_condicion) 
	  where id_usuario=$id_usuario order by peso_uso desc limit 10";
$mas_usados=sql($sql,"No pudo recuperar los clientes mas usados") or fin_pagina();  

}  
?>
<head>
<title>Clientes</title>
<link rel=stylesheet type='text/css' href='../../lib/estilos.css'>
<?=$html_header?>

<script>
function control_datos() {
	
	if (document.all.condicioniva.value==0) {
	    alert ('Debe seleccionar Condicion Iva')
        return false;
	}
	if (document.all.iva.value==0) {
	    alert ('Debe seleccionar Iva')
        return false;
	}
return true;
}
</script>
</head>

<?=$informar?>
<form name="form1" method="post" action="" >
  <table width="100%" border="0" cellspacing="1" cellpadding="1">
    <tr> 
      <td height="27" colspan="2" align="center"><b>CLIENTES</b></td>
    </tr>    
	<tr>
	<td colspan="2"><? tabla_filtros_nombres();?></td>
	</tr>
    <tr> 
      <td height="168" colspan="2" align="center" nowrap> 
        <center> 
        
         <select name="select_cliente" size="10" style="width:576"  ondblclick="<?=$onclickaceptar?>" 
onchange="if (this.selectedIndex!=-1 && aceptar.disabled) 
	      aceptar.disabled=0;
	      if (document.all.guardar)
	      guardar.disabled=0;
	      document.all.nbrecl.disabled=0;
	      email.disabled=0;
	      direccion.disabled=0;
	      telefono.disabled=0;
	      cuit.disabled=0;
	      iib.disabled=0;
	      condicioniva.disabled=0;
	      iva.disabled=0;
          direccion.value=eval('document.all.direccion_'+ this[this.selectedIndex].value +'.value');
          telefono.value=eval('document.all.telefono_'+ this[this.selectedIndex].value +'.value');
          email.value=eval('document.all.email_'+ this[this.selectedIndex].value +'.value');
          nbrecl.value=eval('document.all.nbrecl_'+ this[this.selectedIndex].value +'.value');
          cuit.value=eval('document.all.cuit_'+ this[this.selectedIndex].value +'.value');
          iib.value=eval('document.all.iib_'+ this[this.selectedIndex].value +'.value');
          ind=eval('document.all.condicioniva_'+ this[this.selectedIndex].value +'.value');
          document.all.condicioniva.options[ind].selected=true;
          ind1=eval('document.all.iva_'+ this[this.selectedIndex].value +'.value');
          document.all.iva.options[ind1].selected=true;"
onKeypress="if (this.selectedIndex!=-1 && aceptar.disabled) 
	aceptar.disabled=0;
	if (document.all.guardar)
	guardar.disabled=0;
	document.all.nbrecl.disabled=0;
	email.disabled=0;
	direccion.disabled=0;
	telefono.disabled=0;
	cuit.disabled=0;
	iib.disabled=0;
	condicioniva.disabled=0;
	iva.disabled=0;
 direccion.value=eval('document.all.direccion_'+ this[this.selectedIndex].value +'.value');
 telefono.value=eval('document.all.telefono_'+ this[this.selectedIndex].value +'.value');
 email.value=eval('document.all.email_'+ this[this.selectedIndex].value +'.value');
 nbrecl.value=eval('document.all.nbrecl_'+ this[this.selectedIndex].value +'.value');
 cuit.value=eval('document.all.cuit_'+ this[this.selectedIndex].value +'.value');
 iib.value=eval('document.all.iib_'+ this[this.selectedIndex].value +'.value');
 ind=eval('document.all.condicioniva_'+ this[this.selectedIndex].value +'.value');
 document.all.condicioniva.options[ind].selected=true;
 ind1=eval('document.all.iva_'+ this[this.selectedIndex].value +'.value');
 document.all.iva.options[ind1].selected=true;
 if(event.keyCode==13){<?
                         echo $onclickaceptar
                        ?>}
buscar_op(this);
if (this.selectedIndex!=-1 && aceptar.disabled) 
	aceptar.disabled=0;
	if (document.all.guardar)
	      guardar.disabled=0;
	document.all.nbrecl.disabled=0;
	email.disabled=0;
	direccion.disabled=0;
	telefono.disabled=0;
	cuit.disabled=0;
	iib.disabled=0;
	condicioniva.disabled=0;
	iva.disabled=0;
 direccion.value=eval('document.all.direccion_'+ this[this.selectedIndex].value +'.value');
 telefono.value=eval('document.all.telefono_'+ this[this.selectedIndex].value +'.value');
 email.value=eval('document.all.email_'+ this[this.selectedIndex].value +'.value');
 nbrecl.value=eval('document.all.nbrecl_'+ this[this.selectedIndex].value +'.value');
 cuit.value=eval('document.all.cuit_'+ this[this.selectedIndex].value +'.value');
 iib.value=eval('document.all.iib_'+ this[this.selectedIndex].value +'.value');
 
 ind=eval('document.all.condicioniva_'+ this[this.selectedIndex].value +'.value');
 document.all.condicioniva.options[ind].selected=true;
 ind1=eval('document.all.iva_'+ this[this.selectedIndex].value +'.value');
 document.all.iva.options[ind1].selected=true;
" 
onblur="borrar_buffer()" 
onclick="borrar_buffer()"  
>            <?
if ($filtro!="")
{ 
while (!$clientes->EOF)
{
?>
            <option value="<?=$clientes->fields['id_entidad'] ?>" <?if ($_POST['select_cliente']== $clientes->fields['id_entidad']) echo 'selected'?>> 
            <?=$clientes->fields['nombre'] ?>
            </option>
            <?
	$clientes->MoveNext();
}
}
else {
	 while (!$mas_usados->EOF)
{
?>
            <option value="<?=$mas_usados->fields['id_entidad'] ?>" <?if ($_POST['select_cliente']== $mas_usados->fields['id_entidad']) echo 'selected'?> > 
            <?=$mas_usados->fields['nombre'] ?>
            </option>
            <?
	$mas_usados->MoveNext();
}
}
?>
          </select>
        </center>
        <bR>
        <b>Si los datos del cliente no son correctos, seleccionelo de la lista,
        <br> modifique los datos y presione el botón Guardar
        <br><br>
        <b>Para elegir el cliente, seleccionelo de la lista,<br> presione el botón "Cargar Cliente"</b><br><br><br>
        </td>
     </tr>
     </table>

     
    
<table align="center" cellpadding="2" cellspacing="2" width="60%" class="bordessininferior">
<tr id=mo>
   <td colspan="2" align="center"> DATOS DEL CLIENTE</td>
</tr>
</table>
<table align="center" cellpadding="2" cellspacing="2" id="sub_tabla" width="60%" class="bordessinsuperior">
   <tr>
      <td colspan="3" align="center"><strong>Nombre del Cliente:</strong> 
            <input name="nbrecl" type="text" size="50" value="<?=$_POST['nbrecl']?>" disabled > 
      </td>
   </tr>
   <tr>
      <td width="72%" height="91" colspan="3" align="center">
         <strong>Dirección</strong>&nbsp;&nbsp; &nbsp;&nbsp;
         <input name="chk_direccion" type="checkbox" id="chk_direccion" value="1" checked>
          dirección de entrega<br> 
         <textarea name="direccion" cols="45" id="direccion" disabled><?=$_POST['direccion']?></textarea>
      </td>
   </tr>
   <tr> 
      <td> <b>Telefono</b> <input name="telefono" value=" <?=$_POST['telefono']?>" type="text" id="telefono" disabled > </td>
      <td> <b>Email</b><br> <input name="email" type="text" value=" <?=$_POST['email']?>" disabled > </td>
      <td><b>C.U.I.T.</b> <input name="cuit" type="text" value="<?=$_POST['cuit']?>" disabled > </td>
   </tr>
   <tr>
      <td><b>I.I.B.<bR>  </b> <input name="iib" value="<?=$_POST['iib']?>"  type="text" disabled > </td>
      <td ><b>Condición I.V.A.</b> 
        <?    $query="SELECT nombre,id_condicion from condicion_iva";
              $resultado = sql($query) or fin_pagina();
              $filas_encontradas=$resultado->RecordCount();
              ?>
              <select name='condicioniva' disabled>
               <option value=0>Seleccione</option>
              <?
              while (!$resultado->EOF) {
                  ?>
                  <option value='<?=$resultado->fields['id_condicion']?>' <?if($_POST['condicioniva']==$resultado->fields['id_condicion']) echo 'selected' ?>>
                     <?=$resultado->fields['nombre']?></option>         
                 <? $resultado->MoveNext();
              }
              ?>
              </select>
      </td>
      <td><b>I.V.A.</b> <br>
       <?
              $query="SELECT porcentaje,id_iva from tasa_iva";
              $resultado = sql($query) or fin_pagina();
              $filas_encontradas=$resultado->RecordCount();
              ?>
              <select name='iva' disabled>
               <option value=0>Seleccione</option>
              <?
              while (!$resultado->EOF) {
                  ?>
                  <option value='<?=$resultado->fields['id_iva']?>' <?if($_POST['iva']==$resultado->fields['id_iva']) echo 'selected' ?>>
                     <?=$resultado->fields['porcentaje']?></option>         
                 <? $resultado->MoveNext();
              }
              ?>
              </select>
      
      
      </td>
   </tr>
   </tr>   
      <td colspan="3" height="20" align="center">
        <br>
        
        <input name="aceptar" type="button" value="Cargar Cliente" onclick="<?=$onclickaceptar?>"> 
        <?
        if (permisos_check("inicio","permiso_editar_cliente")) {?>
	     &nbsp;&nbsp; <input type="submit" name="guardar" value="Guardar" onclick="return control_datos();" disabled >
        <?}?>
        &nbsp;&nbsp; <input name="cancelar" type="button" value="Cerrar" onclick="location.href=<?=$onclicksalir?>">
        </td>
    </tr>
</table>
<?
if ($filtro!="")
{ 
$clientes->MoveFirst();
while (!$clientes->EOF)
{
?>
  <input type="hidden" name="direccion_<?=$clientes->fields['id_entidad'] ?>" value="<?=$clientes->fields['direccion'] ?>" > 
  <input type="hidden" name="telefono_<?=$clientes->fields['id_entidad'] ?>" value="<?=$clientes->fields['telefono'] ?>" > 
  <input type="hidden" name="email_<?=$clientes->fields['id_entidad'] ?>" value="<?=$clientes->fields['mail'] ?>" >
  <input type="hidden" name="nbrecl_<?=$clientes->fields['id_entidad'] ?>" value="<?=$clientes->fields['nombre'] ?>" >  
  <input type="hidden" name="cuit_<?=$clientes->fields['id_entidad'] ?>" value="<?=$clientes->fields['cuit'] ?>" >  
  <input type="hidden" name="iib_<?=$clientes->fields['id_entidad'] ?>" value="<?=$clientes->fields['iib'] ?>" >  
  <input type="hidden" name="condicioniva_<?=$clientes->fields['id_entidad'] ?>" value="<?if ($clientes->fields['id_condicion']) echo $clientes->fields['id_condicion']; else echo '0' ?>" >  
  <input type="hidden" name="iva_<?=$clientes->fields['id_entidad'] ?>" value="<?if ($clientes->fields['id_iva']) echo $clientes->fields['id_iva']; else echo '0' ?>" >  
  
<?
	$clientes->MoveNext();
}
}
else{

$mas_usados->MoveFirst();
while (!$mas_usados->EOF)
{
?>
  <input type="hidden" name="direccion_<?=$mas_usados->fields['id_entidad'] ?>" value="<?=$mas_usados->fields['direccion'] ?>" > 
  <input type="hidden" name="telefono_<?=$mas_usados->fields['id_entidad'] ?>" value="<?=$mas_usados->fields['telefono'] ?>" > 
  <input type="hidden" name="email_<?=$mas_usados->fields['id_entidad'] ?>" value="<?=$mas_usados->fields['mail'] ?>" >
  <input type="hidden" name="nbrecl_<?=$mas_usados->fields['id_entidad'] ?>" value="<?=$mas_usados->fields['nombre'] ?>" >  
  <input type="hidden" name="cuit_<?=$mas_usados->fields['id_entidad'] ?>" value="<?=$mas_usados->fields['cuit'] ?>" >  
  <input type="hidden" name="iib_<?=$mas_usados->fields['id_entidad'] ?>" value="<?=$mas_usados->fields['iib'] ?>" >  
  <input type="hidden" name="condicioniva_<?=$mas_usados->fields['id_entidad'] ?>" value="<? if ($mas_usados->fields['id_condicion']) echo $mas_usados->fields['id_condicion']; else echo '0' ?>" >  
  <input type="hidden" name="iva_<?=$mas_usados->fields['id_entidad'] ?>" value="<?if ($mas_usados->fields['id_iva']) echo $mas_usados->fields['id_iva']; else echo '0';?>" >
<?
	$mas_usados->MoveNext();
}
}
echo fin_pagina();
?>
  
