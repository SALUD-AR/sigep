<?php

require_once ("../../config.php");

$cmd=$parametros["cmd"];
$estado=$cmd;
if ($cmd=='todos') $cmd='T';
elseif ($cmd=='activos')$cmd='S';
else $cmd='N';

$sql="SELECT
      DISTINCT
      
      SMIAfiliados.ClaveBeneficiario,
      SMIAfiliados.afiApellido    ,
      SMIAfiliados.afiNombre    ,
      SMIAfiliados.afiTipoDoc ,
      SMIAfiliados.afiDNI       ,
      SMIAfiliados.afiSexo ,
      SMIAfiliados.CUIEEfectorAsignado,
      SMIAfiliados.MotivoBaja,
      SMIAfiliados.MensajeBaja,
      SMIAfiliados.fechainscripcion,
      SMIAfiliados.fechacarga,
      SMIEfectores.TipoEfector,
      SMIEfectores.NombreEfector,
      SMITiposCategorias.Descripcion,
      SMIProcesoAfiliados.Id_ProcAfiliado,
      SMIProcesoAfiliados.Periodo,
      SMIProcesoAfiliados.CodigoCIAltaDatos


FROM nacer.smiafiliados
left join nacer.SMIAfiliadosAux on (SMIAfiliados.clavebeneficiario = SMIAfiliadosAux.clavebeneficiario)
left join nacer.SMIProcesoAfiliados on (SMIProcesoAfiliados.Id_ProcAfiliado = SMIAfiliadosAux.Id_ProcesoIngresoAfiliados)
left join facturacion.SMIEfectores on (SMIAfiliados.CUIEEfectorAsignado = SMIEfectores.CUIE)

left join nacer.smitiposcategorias on (SMIAfiliados.afiTipoCategoria = smitiposcategorias.CodCategoria)";

if ($cmd!='T') $sql.=" WHERE  (smiafiliados.activo='$cmd')";
$sql.=" Order by smiafiliados.afiapellido";
$result=sql($sql) or fin_pagina();

excel_header("beneficiarios_peridod.xls");

?>
<form name=form1 method=post action="listado_beneficiarios_excel_per.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total beneficiarios: </b><?=$result->RecordCount();?> 
       </td>       
      </tr>
      <tr>
      <td align=left>
       <b>Estado: <font size="+1" color="Red"><?=$estado;?> </font></b>
       </td>       
      </tr>
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right >Apellido</td>      	
    <td align=right >Nombre</td>
    <td align=right >DNI</td>
    <td align=right >Tipo Beneficiario</td>
    <td align=right >Nombre Efector</td>         
    <td align=right >Clave Beneficiario</td>
    <td align=right >Periodo</td>
    <td align=right >Activo</td>
    <td align=right >Motivo Baja</td>
    <td align=right >Mensaje Baja</td>
    <td align=right >Fecha Inscripcion</td>
    <td align=right >Fecha de Carga</td>
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td><?=$result->fields['afiapellido']?></td>
     <td><?=$result->fields['afinombre']?></td>
     <td><?=$result->fields['afidni']?></td>     
     <td><?=$result->fields['descripcion']?></td>     
     <td><?=$result->fields['nombreefector']?></td>         
     <td "<?=excel_style("texto")?>"><?=$result->fields['clavebeneficiario']?></td> 
     <td><?=$result->fields['periodo']?></td> 
     <td><?=$result->fields['activo']?></td>  
     <td><?=$result->fields['motivobaja']?></td> 
     <td><?=$result->fields['mensajebaja']?></td>  
     <td><?=$result->fields['fechainscripcion']?></td>  
     <td><?=$result->fields['fechacarga']?></td>  
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>