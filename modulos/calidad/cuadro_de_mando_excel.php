<?php

require_once ("../../config.php");

$cmd=$parametros["cmd"];

$sql="SELECT 
  nacer.efe_conv.id_efe_conv,
  nacer.efe_conv.nombre,  
  nacer.efe_conv.cuie
FROM
  nacer.efe_conv";


if ($cmd=="VERDADERO")
    $sql.=" where (efe_conv.com_gestion='VERDADERO') order by nombre";
    

else if ($cmd=="FALSO")
    $sql.=" where (efe_conv.com_gestion='FALSO') order by nombre";

else $sql.=" order by nombre";
    
    
$result=sql($sql) or fin_pagina();

excel_header("Cuadro de Mando.xls");

?>
<form name=form1 method=post action="efectores_unif_excel.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total Efectores: </b><?=$result->RecordCount();?> 
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id=mo>CUIE</td>      	
    <td align=right id=mo>Nombre</td>
    <td align=right id=mo>Tot. Ins. ACT.</td>    
    <td align=right id=mo>Tot. Ins. INAC.</td>    
    <td align=right id=mo>Tot. Ins.</td>    
    <td align=right id=mo>Cant. Facturacion</td>    
    <td align=right id=mo>Cant. Trazadora EMB</td>    
    <td align=right id=mo>Cant. Trazadora NIÑOS</td>    
    <td align=right id=mo>Cant. Trazadora PARTOS</td>    
    <td align=right id=mo>Cant. Trazadora TOTAL</td> 
    <td align=right id=mo>Aplicacion de Fondos</td> 
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td><?=$result->fields['cuie']?></td>
     <td><?=$result->fields['nombre']?></td>
     <?$cuie=$result->fields['cuie'];
     $sql = "SELECT count (smiafiliados.id_smiafiliados)as r1 from nacer.smiafiliados 
     WHERE cuieefectorasignado='$cuie' and activo='S'";
     $r1=sql($sql,"error R1");     
     ?>
     <td><?=$r1->fields['r1']?></td>
     <?$cuie=$result->fields['cuie'];
     $sql = "SELECT count (smiafiliados.id_smiafiliados)as r1 from nacer.smiafiliados 
     WHERE cuieefectorasignado='$cuie' and activo='N'";
     $r1=sql($sql,"error R1");     
     ?>
     <td><?=$r1->fields['r1']?></td>
     <?$cuie=$result->fields['cuie'];
     $sql = "SELECT count (smiafiliados.id_smiafiliados)as r1 from nacer.smiafiliados 
     WHERE cuieefectorasignado='$cuie'";
     $r1=sql($sql,"error R1");     
     ?>
     <td><?=$r1->fields['r1']?></td>
     <?$cuie=$result->fields['cuie'];
     $sql = "SELECT count (id_factura)as r1 from facturacion.factura 
     WHERE cuie='$cuie'";
     $r1=sql($sql,"error R1");     
     ?>
     <td><?=$r1->fields['r1']?></td>
     <?$cuie=$result->fields['cuie'];
     $sql = "SELECT count (id_emb)as r1 from trazadoras.embarazadas 
     WHERE cuie='$cuie'";
     $t1=sql($sql,"error R1");     
     ?>
     <td><?=$t1->fields['r1']?></td>
     <?$cuie=$result->fields['cuie'];
     $sql = "SELECT count (id_nino)as r1 from trazadoras.nino 
     WHERE cuie='$cuie'";
     $t2=sql($sql,"error R1");     
     ?>
     <td><?=$t2->fields['r1']?></td>
     <?$cuie=$result->fields['cuie'];
     $sql = "SELECT count (id_par)as r1 from trazadoras.partos 
     WHERE cuie='$cuie'";
     $t3=sql($sql,"error R1");     
     ?>
     <td><?=$t3->fields['r1']?></td>
     <?$total_tra=$t1->fields['r1']+$t2->fields['r1']+$t3->fields['r1']?>
     <td><?=$total_tra?></td>     
     <?$cuie=$result->fields['cuie'];
     $sql = "select sum (monto_egreso) as total from contabilidad.egreso
		where cuie='$cuie'";
     $total_egreso=sql($sql,"error R1");     
     ?>
     <td><?=number_format($total_egreso->fields['total'],2,',','.')?></td> 
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>