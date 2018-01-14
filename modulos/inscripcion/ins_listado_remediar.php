<?php
require_once("../../config.php");

variables_form_busqueda("ins_listado_old");
cargar_calendario();

$usuario1=$_ses_user['id'];
$user_name=$_ses_user['name'];

if ($_POST['generarnino']=='Generar'){
        $fechaemp=Fecha_db($_POST['fechaemp']);
		$fechakrga=Fecha_db($_POST['fechakrga']);
		$link=encode_link("ins_listado_remediar_xls.php",array("fechaemp"=>$fechaemp, "fechakrga"=>$fechakrga));?>
	<script>
	window.open('<?=$link?>')
	</script>	
<?}

$orden = array(
        "default" => "1",
        "1" => "beneficiarios.numero_doc",		     
       );
$filtro = array(		
		"numero_doc" => "Numero de Documento",		
		"apellido_benef" => "Apellido",
		);
       
if ($cmd == "")  $cmd="n";

/*$datos_barra = array(
     array(
        "descripcion"=> "No Enviados",
        "cmd"        => "n"
     ),    
     array(
        "descripcion"=> "Enviados",
        "cmd"        => "e"
     ),
     array(
        "descripcion"=> "Todos",
        "cmd"        => "todos"
     )
);

generar_barra_nav($datos_barra);*/

$sql_tmp="SELECT 
			beneficiarios.id_beneficiarios, 
			remediar_x_beneficiario.id_r_x_b,
			beneficiarios.clave_beneficiario,
			beneficiarios.numero_doc,
			beneficiarios.apellido_benef, 
			beneficiarios.nombre_benef, 
			beneficiarios.fecha_nacimiento_benef, 
			beneficiarios.sexo, 
			beneficiarios.estado_envio, 
			remediar_x_beneficiario.nroformulario,
			remediar_x_beneficiario.fechaempadronamiento,
			remediar_x_beneficiario.fecha_carga,
			remediar_x_beneficiario.enviado,
      formulario.puntaje_final,          
      formulario.hta2,        
      formulario.colesterol4					
			FROM uad.remediar_x_beneficiario
			inner join uad.beneficiarios ON (remediar_x_beneficiario.clavebeneficiario=beneficiarios.clave_beneficiario)
      inner join remediar.formulario ON (remediar_x_beneficiario.nroformulario=formulario.nroformulario)";

if ($cmd=="n")
    $where_tmp=" (remediar_x_beneficiario.enviado='n' and tipo_ficha='1') "; // Muestro los no enviados

if ($cmd=="e")
    $where_tmp=" (remediar_x_beneficiario.enviado='e' and tipo_ficha='1')"; // Muestro todos los enviados incluso los borrados
    
    
if ($cmd=="todos")
    $where_tmp=" ( tipo_ficha='1')"; //Muestro todo enviado, no enviado y borrados en ambos casos
    

echo $html_header;?>
<script>
function control_muestra()
{ 
 if(document.all.fechaemp.value==""){
  alert('Debe Ingresar una Fecha DESDE');
  return false;
 } 
 if(document.all.fechakrga.value==""){
  alert('Debe Ingresar una Fecha HASTA');
  return false;
 } 

 alert('Se genera un Excel Completo. Toma como Filtro "Fecha de Empadronamiento" "Fecha de Clasificacion" y "Fecha de Seguimiento"');

return true;
}
</script>
<form name=form1 action="ins_listado_remediar.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
		&nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;<input type='button' name="Auditoriaemp" onclick="window.open('../remediar/auditoriaemp.php','AuditoriaEmpadronamiento','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');" value="Auditoria de Empadronamiento"/>
	    &nbsp;&nbsp;<input type='button' name="auditoria" onclick="window.open('../remediar/auditoria.php','Auditoria','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');" value="Auditoria"/>
	</td>	
	</tr>
	<tr>
	<td align=center>       
	   &nbsp;&nbsp;<b>Fecha desde: </b>
		<input type=text id="fechaemp" name="fechaemp" size=11 maxlength="11" onchange="esFechaValida(this);"> <?=link_calendario('fechaemp');?>
		&nbsp;&nbsp;<b>Fecha hasta: </b>
		<input type=text id="fechakrga" name="fechakrga"  size=11 maxlength="11" onchange="esFechaValida(this);"> <?=link_calendario('fechakrga');?>		
		<? if(1==1){
				$permiso_genera_archivo_remediar="";
			}
			else {
				$permiso_genera_archivo_remediar="disabled";
			}?>
	    &nbsp;&nbsp;<input type=submit name="generarnino" value='Generar' <?=$permiso_genera_archivo_remediar?> onclick="return control_muestra();">
	  </td>
     </tr>     
    <tr>
</table>

<?$result = sql($sql) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
  	<td align=right id=mo>Clave Beneficiario</td>    	    
    <td align=right id=mo><a id=mo href='<?=encode_link("ins_listado_remediar.php",array("sort"=>"1","up"=>$up))?>'>Documento</a></td>      	    
    <td align=right id=mo>Apellido</a></td>      	    
    <td align=right id=mo>Nombre</a></td>
    <td align=right id=mo>F NAC</td>
    <td align=right id=mo>Formulario</td>
    <td align=right id=mo>F Emp R+R</td>
    <td align=right id=mo>F Carga R+R</td>
    <td align=right id=mo>Score</td>
    <td align="right" id="mo">Clasif.</td>     	    
    <td align="right" id="mo">Seg.</td>     	    

  </tr>
 <?
   while (!$result->EOF) {
	$estado_envio=$result->fields['estado_envio'];
	$clave_beneficiario=$result->fields['clave_beneficiario'];
	$sexo=$result->fields['sexo'];
	$fecha_nac=$result->fields['fecha_nacimiento_benef'];
   	$ref = encode_link("../remediar/remediar_admin.php",array("estado_envio"=>$estado_envio,"clave_beneficiario"=>$clave_beneficiario,"sexo"=>$sexo,"fecha_nac"=>$fecha_nac,"vremediar"=>"s","pagina_viene_1"=>"ins_listado_remediar.php"));  	
    $onclick_elegir="location.href='$ref'";?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['clave_beneficiario']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['numero_doc']?></td>        
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['apellido_benef']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre_benef']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=fecha($result->fields['fecha_nacimiento_benef'])?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nroformulario']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=fecha($result->fields['fechaempadronamiento'])?></td>
     <td onclick="<?=$onclick_elegir?>"><?=fecha($result->fields['fecha_carga'])?></td>

     <?if ($result->fields['puntaje_final']==0 and $result->fields['hta2']=='-1' and $result->fields['colesterol4']=='-1') $puntaje_final="";
     else $puntaje_final=$result->fields['puntaje_final']?>
     <td onclick="<?=$onclick_elegir?>"><?=$puntaje_final?></td>

     <td align="center">
       	 <?$ref = encode_link("../trazadoras/remediar_carga.php",array("clave_beneficiario"=>$result->fields['clave_beneficiario'],"pagina"=>'ins_listado_remediar.php'));
       	 
       	   echo "<a href='#' title='Seguimiento' onclick=window.open('".$ref."','Clasificacion','menubar=1,resizable=1,scrollbars=1,width=1000,height=750')><IMG src='$html_root/imagenes/flech.png' height='20' width='20' border='0'></a>";?>
     </td>   
     <td align="center">
       	 <?$ref = encode_link("../trazadoras/seguimiento_admin.php",array("clave_beneficiario"=>$result->fields['clave_beneficiario'],"pagina"=>'ins_listado_remediar.php'));
       	 
       	   echo "<a href='#' title='Seguimiento' onclick=window.open('".$ref."','Seguimiento','menubar=1,resizable=1,scrollbars=1,width=900,height=700')><IMG src='$html_root/imagenes/flech.png' height='20' width='20' border='0'></a>";?>
 
     </td> 
   </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
