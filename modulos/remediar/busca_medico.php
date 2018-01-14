<?php
 require_once ("../../config.php");
 extract($_POST,EXTR_SKIP);
 if ($parametros) extract($parametros,EXTR_OVERWRITE);
 echo $html_header;
?>
<script>
function iSubmitEnter(oEvento, oFormulario){
     var iAscii;

     if (oEvento.keyCode)
         iAscii = oEvento.keyCode;
     else if (oEvento.which)
         iAscii = oEvento.which;
     else
         return false;

     /*if (iAscii == 13)*/ oFormulario.submit();

     return true;
}
</script>
<FORM METHOD="get" ACTION="" name="form1" id="form1">
<font size=2><b>Ingrese Nombre, Apellido o Nro. Documento del promotor</b> </font>
 &nbsp; <input type="text" style="font-size:9" name="valor_buscar" size="20"   maxlength="40" onkeyup="iSubmitEnter(event, document.form1)">

</FORM>
<script>
document.getElementById('valor_buscar').focus();
</script>
<?
if ($_GET['valor_buscar']){
$vremediar='n';
$valor_buscar= $_GET['valor_buscar'];


$sql=  "select trim(upper(apellido_medico))as apellido_medico,trim(upper(nombre_medico))as nombre_medico,trim(dni_medico)as dni_medico,id_medico
            from planillas.medicos
		WHERE trim(upper(dni_medico)) = upper('$valor_buscar') or trim(upper(nombre_medico)) like upper('%$valor_buscar%')  or trim(upper(dni_medico)) like upper('%$valor_buscar%') or trim(upper(apellido_medico)) like upper('%$valor_buscar%')";
$res_efectores=sql($sql) or fin_pagina();
	 ?>
					<script>
						document.getElementById('valor_buscar').value='<?=$valor_buscar?>';
					</script>
				  <tr>
				  <td class="titulo_consulta">&nbsp;<h2><U>Resultados de la Busqueda</U></h2></td>
				 </tr>
				  <table border=1 cellspacing=0 cellpadding=0 height=10% align="center" width=80%>
				 <tr>
				   <td align="center">&nbsp;<h5>Doc. Medico</h5></td>
				  <td align="center">&nbsp;<h5>Apellido Medico</h5></td>
				 <td align="center">&nbsp;<h5>Nombre Medico</h5></td>
				 </tr>
<?
			while (!$res_efectores->EOF){ if(rtrim($res_efectores->fields['dni_medico'])==''){$dni_medico='S/D';}else{$dni_medico=rtrim($res_efectores->fields['dni_medico']);}?>
					<tr>
					<td>&nbsp;<a href="#" onclick="opener.document.forms.form1.dni_medico.value = '<?=$dni_medico?>'; opener.document.forms.form1.nombre_medico.value = '<?=$res_efectores->fields['nombre_medico']?>'; opener.document.forms.form1.apellido_medico.value = '<?=$res_efectores->fields['apellido_medico']?>';opener.document.forms.form1.id_medico.value = '<?=$res_efectores->fields['id_medico']?>';opener.document.forms.form1.dni_medico.readOnly = true; opener.document.forms.form1.nombre_medico.readOnly = true; opener.document.forms.form1.apellido_medico.readOnly = true; window.close();" style="text-decoration:none;"><font size=2><?=$dni_medico?></font></a></td>
					<td>&nbsp;<font size=2><? echo $res_efectores->fields['apellido_medico']; ?></font>&nbsp;</td>
                    <td>&nbsp;<font size=2><?=$res_efectores->fields['nombre_medico']?></font>&nbsp;</td>
			<? $res_efectores->movenext();
			    }?> </table>
			 
			  <BR> <a href="#" onclick="opener.document.forms.form1.dni_medico.readOnly = false; opener.document.forms.form1.nombre_medico.readOnly = false; opener.document.forms.form1.apellido_medico.readOnly = false;opener.document.forms.form1.id_medico.value = 'new'; window.close();" ><font size=2>Nuevo Medico</font></a>
<? }
echo fin_pagina();// aca termino?>
