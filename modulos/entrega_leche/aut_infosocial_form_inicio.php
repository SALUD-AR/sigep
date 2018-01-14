<?
/*
Author: seba
$Revision: 1.43 $
$Date: 2015/05/04 13:53:00 $
*/

require_once ("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

variables_form_busqueda("infosocial_listado");

$orden = array(
        "default" => "5",
        "1" => "cu",
        "3" => "d",
        "5" => "a",
        "6" => "n",  
        "7" => "efector" 
        );
$filtro = array(
		"cu" => "CUIE",
        "a"=> "Apellido",
        "d" => "D.N.I.",
        "n"=> "Nombre",
        "efector"=> "Efector"
        
       );


if ($_POST['autorizar']=="Autorizar") {
	
	$error = 0;
	$seleccionados = $_POST["chk_producto"] or Error("No se seleccionó ninguna solicitud para relacionar");
	
  if (!$error) {
		$sql_array = array();
		
			$fecha_aut=date('Y-m-d');
			$user=$_ses_user['login'];
			$usuario_con="SELECT * from sistema.usuarios where login='$user'";
			$resusu = sql($usuario_con,"Error al verificar usuario $user") or fin_pagina();
			$user_id=$resusu->fields['id_usuario'];
			foreach ($seleccionados as $id_informe) {
			$sql_array[] = "update  leche.formula_inicio set
						        estado='a',
						        fecha_autor='$fecha_aut',
						        id_user_aut='$user_id'
						        where id_formula=$id_informe";
		}
		$result = sql($sql_array) or fin_pagina();
		Aviso("El/los informe/s Social/es han sido Autorizados");
	}
}

if ($_POST['rechazar']=="Rechazar") {
	
	$error = 0;
	$seleccionados = $_POST["chk_producto"] or Error("No se seleccionó ninguna solicitud para relacionar");
	
  if (!$error) {
		$sql_array = array();
		
			$fecha_aut=date('Y-m-d');
			$user=$_ses_user['login'];
			$usuario_con="SELECT * from sistema.usuarios where login='$user'";
			$resusu = sql($usuario_con,"Error al verificar usuario $user") or fin_pagina();
			$user_id=$resusu->fields['id_usuario'];
			
		foreach ($seleccionados as $id_informe) {
				$sql_array[] = "update  leche.formula_inicio set
						        estado='n',
						        fecha_autor='$fecha_aut',
						        id_user_aut='$user_id'
						        where id_formula=$id_informe";
		}
		$result = sql($sql_array) or fin_pagina();
		Aviso("El/los informe/s Social/es han sido Rechado");
	}
}
if ($_POST['pendiente']=="Pendiente") {
	
	$error = 0;
	$seleccionados = $_POST["chk_producto"] or Error("No se seleccionó ninguna solicitud para relacionar");
	
  if (!$error) {
		$sql_array = array();
			$usuario_con="SELECT * from sistema.usuarios where login='$user'";
			$resusu = sql($usuario_con,"Error al verificar usuario $user") or fin_pagina();
			$user_id=$resusu->fields['id_usuario'];
			
		foreach ($seleccionados as $id_informe) {
				$sql_array[] = "update  leche.formula_inicio set
						        estado='p'
						        where id_formula=$id_informe";
		}
		$result = sql($sql_array) or fin_pagina();
		Aviso("El/los informe/s Social/es se modificaron a PENDIENTES para su evaluacion");
	}
}

if ($_POST['entrega']=="Entregado") {
	
	$error = 0;
	$seleccionados = $_POST["chk_producto"] or Error("No se seleccionó ninguna solicitud para relacionar");
	
  if (!$error) {
		$sql_array = array();
		
			$fecha_entrega=date('Y-m-d');
			$user=$_ses_user['login'];
			$usuario_con="SELECT * from sistema.usuarios where login='$user'";
			$resusu = sql($usuario_con,"Error al verificar usuario $user") or fin_pagina();
			$user_id=$resusu->fields['id_usuario'];
			foreach ($seleccionados as $id_informe) {
			$sql_array[] = "update  leche.formula_inicio set
						        estado='e',
						        fecha_entrega='$fecha_entrega',
						        id_user_aut='$user_id'
						        where id_formula=$id_informe";
		}
		$result = sql($sql_array) or fin_pagina();
		Aviso("El/los informe/s Social/es de Formula de Inicio han sido Entregadas");
	}
}

$sql_tmp="SELECT * from (
      SELECT
      nacer.smiafiliados.afiapellido as a,
      nacer.smiafiliados.afinombre as n,
      nacer.smiafiliados.afidni as d,
      leche.formula_inicio.id_formula,
      leche.formula_inicio.formula_solicitada,
      leche.formula_inicio.causas_neonatales,
      leche.formula_inicio.causas_maternas,
      leche.formula_inicio.otras_causas,
      leche.formula_inicio.fecha_solicitud,
      leche.formula_inicio.cantidad,
      leche.formula_inicio.medico,
      leche.formula_inicio.id_smiafiliados as id,
      nacer.efe_conv.cuie as cu,
      nacer.efe_conv.nombre AS efector,
      leche.formula_inicio.estado,
      leche.formula_inicio.id_user_aut,
      leche.formula_inicio.fecha_autor,
      leche.formula_inicio.fecha_entrega,
      sistema.usuarios.nombre as nom_user,
      sistema.usuarios.apellido as ape_user
      FROM
      leche.formula_inicio
      inner JOIN nacer.smiafiliados ON nacer.smiafiliados.id_smiafiliados = leche.formula_inicio.id_smiafiliados
      LEFT OUTER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = leche.formula_inicio.cuie
      LEFT OUTER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = leche.formula_inicio.id_user_aut
    
      union 

      SELECT
      leche.beneficiarios.apellido as a,
      leche.beneficiarios.nombre as n,
      leche.beneficiarios.documento as d,
      leche.formula_inicio.id_formula,
      leche.formula_inicio.formula_solicitada,
      leche.formula_inicio.causas_neonatales,
      leche.formula_inicio.causas_maternas,
      leche.formula_inicio.otras_causas,
      leche.formula_inicio.fecha_solicitud,
      leche.formula_inicio.cantidad,
      leche.formula_inicio.medico,
      leche.formula_inicio.id_beneficiario as id,
      nacer.efe_conv.cuie as cu,
      nacer.efe_conv.nombre AS efector,
      leche.formula_inicio.estado,
      leche.formula_inicio.id_user_aut,
      leche.formula_inicio.fecha_autor,
      leche.formula_inicio.fecha_entrega,
      sistema.usuarios.nombre as nom_user,
      sistema.usuarios.apellido as ape_user
      FROM
      leche.formula_inicio
      inner JOIN leche.beneficiarios ON leche.beneficiarios.id_beneficiarios = leche.formula_inicio.id_beneficiario
      LEFT OUTER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = leche.formula_inicio.cuie
      LEFT OUTER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = leche.formula_inicio.id_user_aut) as qu";

if ($cmd==1)
    $where_tmp=" (qu.estado='p')";
if ($cmd==2)
   $where_tmp=" (qu.estado='a')";  
if ($cmd==3)
    $where_tmp=" (qu.estado='n')";
if ($cmd==4)
    $where_tmp=" (qu.estado='e')";

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto

function seleccionar(chkbox){
	for (var i=0;i < document.forms["form1"].elements.length;i++)
	{
		var elemento = document.forms[0].elements[i];
		if (elemento.type == "checkbox")
		{
		elemento.checked = chkbox.checked
		}
	}
} 
</script>

<form name='form1' action='aut_infosocial_form_inicio.php' method='POST'>
<input type="hidden" value="<?=$cmd?>" name="cmd">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_infosoc,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    </td>
     </tr>
</table>
<?$result = sql($sql,"No se ejecuto en la consulta principal") or die;?>
<table width="98%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <font size=+1><b>Informe Social <?=$accion1?></b></font>    
    </td>
 </tr>
			
<table width="98%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">	 
	</td></tr> 
	<?//tabla de comprobantes		
			
			if ($result->RecordCount()==0){?>
				 <tr>
				  <td align="center">
				   <font size="3" color="Red"><b>No existe documentacion para esta solicitud</b></font>
				  </td>
				</tr>
			<?}else{?>
				 	<tr id="sub_tabla">	
				 		<td align="right" id="mo"> <input type=checkbox name="seleccionar_todos" value="1" onclick="seleccionar(this)"> </td>
				 		<td align="right" id="mo">Estado</td> 
				 	    <td align="right" id="mo">Apellido y Nombre</td>
				 		<td align="right" id="mo">D.N.I.</td>
				 		<td align="right" id="mo">Efector Solicitante</td>
				 		<td align="right" id="mo">Fecha de I.S.</td>
				 		<td align="right" id="mo">Informe Social</td>
				 		<td align="right" id="mo">Responsable de I.S.</td>
				 		<td align="right" id="mo">Archivos</td>
				 	</tr>
				 	<?while (!$result->EOF){
						   	$id_informe=$result->fields['id_formula'];
						   ?>
				 		<tr <?=atrib_tr()?> >				 
				 			<td align="center"> <input type=checkbox name="chk_producto[]" value="<?=$id_informe?>"> </td>		
				 			<td ><?if($result->fields['estado']=='p')echo "PENDIENTE"; elseif(trim($result->fields['estado'])=='a') echo "AUTORIZADO"; elseif(trim($result->fields['estado'])=='n') echo "RECHAZADO"?></td>		
					 		<td ><?=$result->fields['a'].' '.$result->fields['n'];?></td>
					 		<td ><?=$result->fields['d']?></td>
					 		<td ><?=$result->fields['efector'].' - '.$result->fields['cu']?></td>
					 		<td align="center"><?=fecha($result->fields['fecha_solicitud'])?></td>
					 		<td ><?=$result->fields['informe']?></td>
					 		<td ><?=$result->fields['resp_infor']?></td>
					 		<?   //lista de archivos subidos
					 			$sql1="SELECT *
										FROM leche.formula_inicio
										WHERE
										leche.formula_inicio.id_formula = $id_informe
										--ORDER BY leche.archivo_fichero.id_ar_fic DESC";										
									   $result_items1=sql($sql1) or fin_pagina();
				                  ?>
				                  <?while (!$result_items1->EOF){ ?>

										<? if (is_file(MOD_DIR."/entrega_leche/ar_info_social/".$result_items1->fields["nombre"]))?>
										<td ><a href='<?=encode_link("comprobante_admin_leche.php",array ("file" =>$result_items1->fields["nombre"],"size" =>$result_items1->fields["size"],"cmd" => "download", 'id'=>$id,'entidad_alta'=>$entidad_alta))?>'>
										<?=$result_items1->fields["nombre"]?></a> </td >
					          
						             <?$result_items1->movenext();
					          	}//del while ?> 	               
					 	</tr>	
					 <?$result->movenext();
				 	}// fin while
				} //fin del else?>	 	
	</td></tr>
 </table>
 
 <table width="98%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 
  <tr align="center">
    <td> 		                               
	<input type=submit name="autorizar" value="Autorizar" <?if ($cmd==2 or $cmd==3) echo "disabled"?> onclick="return confirm ('Autorizar Informe Social?')" title="Esta Seguro que desea Atorizar Informe Social?" style="width=150px">     
	<input type=submit name="rechazar" value="Rechazar" <?if ($cmd==2 or $cmd==3) echo "disabled"?> onclick="return confirm ('Rechazar Informe Social?')" title="Desvincular" style="width=150px">     		
	<input type=submit name="pendiente" value="Pendiente" <?if ($cmd==2) echo "disabled"?> onclick="return confirm ('Colocar en Pendiente?')" title="Colocar en estado Pendiente para su revision" style="width=150px">  
	<input type=submit name="entrega" value="Entregado" <?if ($cmd==1 or $cmd==3) echo "disabled"?> onclick="return confirm ('Registrar Entrega de Leche')" title="Registrar la fecha de entrega de leche" style="width=150px">   			   	   
 </td>
  </tr>
 
 </table>
 
 <table width="98%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 
  <tr align="center">
    <td> 	
		<input type=button name="volver" value="Volver" onclick="document.location='listado_formula_inicio.php'"title="Volver al Listado" style="width=150px">     
    </td>
  </tr>
 
 </table>
  
 </form>
 
 <?=fin_pagina();// aca termino ?>