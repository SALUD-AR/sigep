<?php

require_once ("../../config.php");

$cmd=$parametros["cmd"];

$sql="SELECT 
  efe_conv.*,zona_sani.*,dpto.nombre as nombre_dpto
FROM
  nacer.efe_conv
  left join facturacion.nomenclador_detalle using (id_nomenclador_detalle)
  left join nacer.zona_sani using (id_zona_sani)
  left join nacer.dpto on dpto.codigo=efe_conv.departamento";


if ($cmd=="VERDADERO")
    $sql.=" where (efe_conv.conv_sumar='t')";
    

if ($cmd=="FALSO")
    $sql.=" where (efe_conv.conv_sumar='f')";
	
if ($cmd=="REDSALUD")
    $sql.=" where (efe_conv.com_gestion='REDSALUD')";
	
$result=sql($sql) or fin_pagina();

excel_header("Efectores Unificado.xls");

?>
<form name=form1 method=post action="efectores_unif_excel.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total beneficiarios: </b><?=$result->RecordCount();?> 
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right >Cuie</td>      	
    <td align=right >Siisa</td>      	
    <td align=right >Nombre</td>
    <td align=right >Domicilio</td>
    <td align=right >Departamento</td>
    <td align=right >Localidad</td>
    <td align=right >Cod postal</td>    
    <td align=right >Cuidad</td>    
    <td align=right >Referente</td>    
    <td align=right >Tel</td>    
    <td align=right >Mail</td>
    <td align=right >Comp gestion</td>
    <td align=right >Comp gestion firmante</td>
    <td align=right >Referente con Addenda</td>
    <td align=right >DNI Referente con Addenda</td>
    <td align=right >Fecha Comp Gestion</td>
    <td align=right >Fecha Fin Comp Gestion</td>    
    <td align=right >Comp gestion pago indirecto</td>    
    <td align=right >Tercero Admin</td>    
    <td align=right >Tercero Admin Firmante</td>    
    <td align=right >Fecha Tercero Admin</td>    
    <td align=right >Fecha Fin Tercero Admin</td>    
    <td align=right >Zona Sanitaria</td>     
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td><?=$result->fields['cuie']?></td>
     <td><?="cod: " . $result->fields['cod_siisa']?></td>
     <td><?=$result->fields['nombre']?></td>
     <td><?=$result->fields['domicilio']?></td>     
     <td><?=$result->fields['nombre_dpto']?></td>     
     <td><?=$result->fields['localidad']?></td>     
     <td><?=$result->fields['cod_pos']?></td>     
     <td><?=$result->fields['cuidad']?></td>     
     <td><?=$result->fields['referente']?></td> 
     <td><?=$result->fields['tel']?></td>  
     <?$cuie=$result->fields['cuie'];
     $sql="select * from nacer.mail_efe_conv where cuie = '$cuie'";
     $result_mail=sql($sql,'Error');
     $result_mail->movefirst();
     $contenido_mail='';
     while (!$result_mail->EOF) {
     	$contenido_mail.=$result_mail->fields['descripcion'].': '.$result_mail->fields['mail'].' ';
     	$result_mail->MoveNext();
     }
     ?>  
     <td onclick="<?=$onclick_elegir?>"><?=$contenido_mail?></td>
     <td><?=$result->fields['com_gestion']?></td>  
     <td><?=$result->fields['com_gestion_firmante']?></td>  
     <td><?=$result->fields['com_gestion_firmante_actual']?></td>  
     <td><?=$result->fields['dni_firmante_actual']?></td>  
     <td><?=fecha($result->fields['fecha_comp_ges'])?></td>  
     <td><?=fecha($result->fields['fecha_fin_comp_ges'])?></td> 
     <td><?=$result->fields['com_gestion_pago_indirecto']?></td>  
     <td><?=$result->fields['tercero_admin']?></td>  
     <td><?=$result->fields['tercero_admin_firmante']?></td>  
     <td><?=fecha($result->fields['fecha_tercero_admin'])?></td> 
     <td><?=fecha($result->fields['fecha_fin_tercero_admin'])?></td>  
     <td><?=$result->fields['nombre_zona']?></td>  
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>