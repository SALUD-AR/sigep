<?
require_once ("../../config.php"); 

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);



if ($_POST['buscar_remediar']=="b"){
if ($_POST['num_form_remediar']!=''){
	 $queryrmediar="SELECT remediar_x_beneficiario.fechaempadronamiento,remediar_x_beneficiario.fecha_carga
				FROM  uad.remediar_x_beneficiario 
							inner join remediar.formulario on formulario.nroformulario=remediar_x_beneficiario.nroformulario
							inner join uad.beneficiarios on remediar_x_beneficiario.clavebeneficiario=beneficiarios.clave_beneficiario
	  where formulario.nroformulario='".$_POST['num_form_remediar']."'
		group by remediar_x_beneficiario.fechaempadronamiento,remediar_x_beneficiario.fecha_carga";
	
	$res_remediar=sql($queryrmediar, "Error al traer el Comprobantes") or fin_pagina();
	if ($res_remediar->RecordCount()>0){
		//$num_form_remediar=$res_remediar->fields['nroformulario'];
		$fechaempadronamiento=fecha($res_remediar->fields['fechaempadronamiento']);
		$fecha_carga=fecha($res_remediar->fields['fecha_carga']);
		}else{  $accion2="No se encuentra formulario"; }
	}else{ echo "<SCRIPT Language='Javascript'> alert('Debe Cargar el Nº de Formulario'); </SCRIPT>";}
}
echo $html_header;
?>

<form name='form1' action='auditoria.php' method='POST'>

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
             <tr id="mo">
                <td align="center" colspan="2" >
                    <b> N&uacute;mero de Formulario Remediar + Redes </b><input type="text" maxlength="16" name="num_form_remediar" value="<?=$num_form_remediar?>" ><input type=submit name="buscar_remediar" value="b" title="b" >
               </td>
             </tr>
             <tr id="ma">
         	<td align="left" >
				<b>Fecha de Empadronamiento:</b><?=$fechaempadronamiento?>
		    </td>
			<td align="left" >
				<b>Fecha de Carga:</b><?=$fecha_carga?>
		    </td>
	  		</tr>
          
        </table>
    
</form>

 <?=fin_pagina();// aca termino ?>
