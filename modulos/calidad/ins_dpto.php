<?php

require_once("../../config.php");

variables_form_busqueda("ins_dpto");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

$orden = array(
        "default" => "1",
        "1" => "nombre"
        
             
       );
$filtro = array(
		"nombre" => "nombre"
                
       );

$sql_tmp="SELECT 
  *
FROM
  nacer.dpto";

echo $html_header;
?>
<form name=form1 action="ins_dpto.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	     &nbsp;&nbsp;
	    <? $link=encode_link("ins_dpto_excel.php",array());?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=11 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("ins_dpto.php",array("sort"=>"1","up"=>$up))?>'>NOMBRE</a></td>    
    <td align=right id=mo>Inscriptos ACTIVOS</td>    
    <td align=right id=mo>Inscriptos INACTIVOS</td>    
    <td align=right id=mo>Total Inscriptos</td>   
  </tr>
 <?
   while (!$result->EOF) {
   	$ref = encode_link("ins_dpto_admin.php",array("id_dpto"=>$result->fields['id_dpto']));
    	$onclick_elegir="location.href='$ref'";?>
    
    <tr <?=atrib_tr()?>>             
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>
     
     <?$codigo=$result->fields['codigo'];
     $sql = "SELECT count (smiafiliados.id_smiafiliados)as r1 
			from nacer.smiafiliados 
			left join nacer.efe_conv ON (nacer.efe_conv.cuie = nacer.smiafiliados.cuieefectorasignado)
     		WHERE departamento='$codigo' and activo='S'";
     $r1=sql($sql,"error R1");     
     ?>
     <td onclick="<?=$onclick_elegir?>"><?=$r1->fields['r1']?></td>
     
     <?
     $sql = "SELECT count (smiafiliados.id_smiafiliados)as r1 
			from nacer.smiafiliados 
			left join nacer.efe_conv ON (nacer.efe_conv.cuie = nacer.smiafiliados.cuieefectorasignado)
     		WHERE departamento='$codigo' and activo='N'";
     $r2=sql($sql,"error R1");     
     ?>
     <td onclick="<?=$onclick_elegir?>"><?=$r2->fields['r1']?></td>
     
     <td onclick="<?=$onclick_elegir?>"><?=$r1->fields['r1']+$r2->fields['r1']?></td>
     
    </tr>
	<?$result->MoveNext();
    }?>
    


<?$sql="select dpto.nombre,grupopoblacional,ceb,count (ceb) as total
			from nacer.smiafiliados 
			left join nacer.efe_conv ON (smiafiliados.cuieefectorasignado=efe_conv.cuie)
			left join nacer.dpto ON (efe_conv.departamento=dpto.codigo)
			where activo='S'
			group by
			dpto.nombre,efe_conv.departamento,ceb,grupopoblacional
			order by 
			dpto.nombre,grupopoblacional,ceb";
$result_ceb=sql($sql,"no se puede ejecutar la consulta");?>

<table width="60%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
		 <tr align="center" id="sub_tabla">
		 	<td colspan=8>	
		 		Detalle sobre Cobertura Efectiva
		 	</td>
		 </tr>	
		 <tr>
			<td align=right id=mo><a id=mo>Departamento</a></td> 
			<td align=right id=mo><a id=mo>Grupo Poblacional</a></td> 
			<td align=right id=mo><a id=mo>Cobertura</a></td>  
			<td align=right id=mo><a id=mo>Cantidad</a></td>
		</tr>
		<?while (!$result_ceb->EOF) {?>
			<tr <?=atrib_tr1()?>>        
			 <td align=left><?=$result_ceb->fields['nombre']?></td>
			 <td align=left><?=$result_ceb->fields['grupopoblacional']?></td>
			 <td align=left><?=$result_ceb->fields['ceb']?></td>
			 <td align=left><?=$result_ceb->fields['total']?></td>		      
			 </tr>
			<?$result_ceb->MoveNext();
		} ?> 		 

</table>
</table>

</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
