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
<font size=2><b>Ingrese Nombre, codigo o palabra clave que identifique al efector y presiones enter</b> </font>&nbsp; 
<input type="text" style="font-size:9" name="efectores" size="20" id="efectores" maxlength="40" onkeyup="iSubmitEnter(event, document.form1)">
</FORM>
<script>
document.getElementById('efectores').focus();
</script>
<?
if ($_GET['efectores'] || $_GET['efectores']=='0'){ 
	$nefectores= ($_GET['efectores']);
	$efectores= 'N'.$_GET['efectores'];
	$sql=  "select a.cuie,a.nombre,a.localidad,a.departamento
			from nacer.efe_conv a 			       
			WHERE  upper(a.nombre) like upper('%$nefectores%') or upper(a.cuie) = upper('$efectores') 
			or upper(a.cuie) like upper('%$nefectores%')
			order by a.nombre ";
	$res_efectores=sql($sql) or fin_pagina();?>
	
					<script>
						document.getElementById('efectores').value='<?=$nefectores?>';
					</script>
	<table border=1 cellspacing=0 cellpadding=0 height=10% align="center" width=97%>
	<caption>&nbsp;<h2><U>Resultados de la Busqueda</U></h2></caption>
					 <tr>
					  <td align="center">&nbsp;<h5>Codigo SISA</h5></td>
					  <td align="center">&nbsp;<h5>Codigo Plan Nacer</h5></td>
					  <td align="center">&nbsp;<h5>Codigo Remediar</h5></td>
					  <td align="center">&nbsp;<h5>Nombre Efector</h5></td>
					  <td align="center">&nbsp;<h5>Departamento</h5></td>
					  <td align="center">&nbsp;<h5>Localidad</h5></td>
					 </tr>

					<?while (!$res_efectores->EOF){?>
						<tr>
							<td>&nbsp;<font size=2><?=$res_efectores->fields['codigosisa']?></font>&nbsp;</td>
							<td><a href="#" onclick="opener.document.forms.form1.cuie.value='<?=$res_efectores->fields['cuie']?>'; window.close();" style="text-decoration:none;" ><font size=2><?=$res_efectores->fields['cuie']?></font></a></td>
							<td>&nbsp;<font size=2><?=$res_efectores->fields['codremediar']?></font>&nbsp;</td>
							<td>&nbsp;<font size=2><? echo $res_efectores->fields['nombre'];?></font>&nbsp;</td>
							<td>&nbsp;<font size=2><? echo '('.$res_efectores->fields['departamento'].')'; ?></font>&nbsp;</td>
							<td>&nbsp;<font size=2><? echo '('.$res_efectores->fields['localidad'].')'; ?></font>&nbsp;</td>
						</tr>
					<?$res_efectores->movenext();
					}?> 
	</table>
				 
	<BR> <a href="javascript:close()" ><font size=2>CERRAR CONSULTAR</font></a>
<?}
echo fin_pagina();// aca termino?>
