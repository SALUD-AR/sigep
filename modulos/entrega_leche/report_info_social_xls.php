<?php
/*
$Author: gaby $
$Revision: 1.0 $
$Date: 2012/09/21 10:52:40 $
*/
require_once ("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

$sql_tmp=substr($sql,0,-18);
 $sql_tmp;
$result=sql($sql_tmp) or fin_pagina();
$hoy=date("d-m-Y");
excel_header("report_info_social_".$hoy.".xls");
//excel_header("report_info_social.xls");
?>
<form name=form1 method=post action="report_info_social_xls.php">
 <br>
  <table align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr >
	  
    <td align=right bgcolor=#C0C0FF>Estado</a></td> 
    <td align=right bgcolor=#C0C0FF>Efector Solicitante</a></td> 
    <td align=right bgcolor=#C0C0FF>Apellido y Nombre</a></td>    
    <td align=right bgcolor=#C0C0FF>D.N.I.</a></td>	
    <td align=right bgcolor=#C0C0FF>Fecha I.S.</a></td>
    <td align=right bgcolor=#C0C0FF>Realiza I.S.</a></td>
    <td align=right bgcolor=#C0C0FF>Responsable Conclusion I.S.</a></td>
    <td align=right bgcolor=#C0C0FF>Fecha Conclusion</a></td>

 </tr>
 <? 
 while (!$result->EOF) {
 ?>
   <tr>			 
     <td align=center ><?if($result->fields['autorizado']=='PEND')echo "PENDIENTE"; elseif(trim($result->fields['autorizado'])=='SI') echo "AUTORIZADO"; elseif(trim($result->fields['autorizado'])=='NO') echo "RECHAZADO"?></td>
	 <td align=center ><?=$result->fields['efector'].' - '.$result->fields['cu'] ?></td>
     <td align=right ><?=$result->fields['a'].' '.$result->fields['n']?></td>
     <td align=right ><?=$result->fields['d'];?></td>
     <td align=right ><?=Fecha($result->fields['fecha_inf'])?></td>     
     <td align=center ><?=$result->fields['resp_infor']?></td>
     <td align=right ><?if($result->fields['id_user_aut']=='' or $result->fields['id_user_aut']==0) echo " &nbsp"; else echo $result->fields['nom_user'].' '.$result->fields['ape_user'];?></td>     
     <td align=right ><?if($result->fields['id_user_aut']=='' or $result->fields['id_user_aut']==0) echo " &nbsp"; else echo Fecha($result->fields['fecha_aut'])?></td>   
   </tr>	
<?$result->movenext();
    }?>

 </table>
 </br>
 </form>
