<?
require_once ("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

$sql="select * from nacer.smiafiliados
	 left join nacer.smitiposcategorias on (afitipocategoria=codcategoria)
	 left join nacer.efe_conv on (cuieefectorasignado=cuie)
	 where id_smiafiliados=$id_smiafiliados";
$res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();

$afiapellido=$res_comprobante->fields['afiapellido'];
$afinombre=$res_comprobante->fields['afinombre'];
$afidni=$res_comprobante->fields['afidni'];
$descripcion=$res_comprobante->fields['descripcion'];
$nombre=$res_comprobante->fields['nombre'];
$afifechanac=$res_comprobante->fields['afifechanac'];
$activo=$res_comprobante->fields['activo'];
$afisexo=$res_comprobante->fields['afisexo'];
$cuie=$res_comprobante->fields['cuie'];
$cod_siisa=$res_comprobante->fields['cod_siisa'];
$fechainscripcion=$res_comprobante->fields['fechainscripcion'];
$afitipodoc=$res_comprobante->fields['afitipodoc'];
$afisexo=$res_comprobante->fields['afisexo'];

$afiDomCalle=$res_comprobante->fields['afiDomCalle'];
$afiDomNro=$res_comprobante->fields['afiDomNro'];
$afiDomManzana=$res_comprobante->fields['afiDomManzana'];
$afiDomPiso=$res_comprobante->fields['afiDomPiso'];
$afiDomEntreCalle1=$res_comprobante->fields['afiDomEntreCalle1'];
$afiDomEntreCalle2=$res_comprobante->fields['afiDomEntreCalle2'];
$afiDomBarrioParaje=$res_comprobante->fields['afiDomBarrioParaje'];
$afiDomMunicipio=$res_comprobante->fields['afiDomMunicipio'];
$afiDomProvincia=$res_comprobante->fields['afiDomProvincia'];
$afiDomCP=$res_comprobante->fields['afiDomCP'];
$afiTelefono=$res_comprobante->fields['afiTelefono'];
$afidomdepartamento=$res_comprobante->fields['afidomdepartamento'];
$afidomlocalidad=$res_comprobante->fields['afidomlocalidad'];

echo $html_header;
?>
<form name='form1' action='beneficiarios_vista.php' method='POST'>
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <font size=+1><b>Beneficiario</b></font>    
    </td>
 </tr>
 <tr><td>
  <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripción del Beneficiario</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         
         <tr>
         	<td align="right">
         	  <b>Apellido:
         	</td>         	
            <td align='left'>
              <input type='text' name='afiapellido' value='<?=$afiapellido;?>' size=60 align='right' readonly></b>
            </td>
         </tr>
         <tr>
            <td align="right">
         	  <b> Nombre:
         	</td>   
           <td  colspan="2">
             <input type='text' name='afinombre' value='<?=$afinombre;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  <tr>
           <td align="right">
         	  <b> Tipo Documento:
         	</td> 
           <td colspan="2">
             <input type='text' name='afitipodoc' value='<?=$afitipodoc;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          <tr>
           <td align="right">
         	  <b> Documento:
         	</td> 
           <td colspan="2">
             <input type='text' name='afidni' value='<?=$afidni;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
           
		   <tr>
           <td align="right">
         	  <b> Sexo:
         	</td> 
           <td colspan="2">
             <input type='text' name='sexo' value='<?=$afisexo;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  
		  <tr>
           <td align="right">
         	  <b> Calle:
         	</td> 
           <td colspan="2">
             <input type='text' name='afiDomCalle' value='<?=$afiDomCalle;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  
		  <tr>
           <td align="right">
         	  <b> Numero:
         	</td> 
           <td colspan="2">
             <input type='text' name='afiDomNro' value='<?=$afiDomNro;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  
		  <tr>
           <td align="right">
         	  <b> Manzana:
         	</td> 
           <td colspan="2">
             <input type='text' name='afiDomManzana' value='<?=$afiDomManzana;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  
		  <tr>
           <td align="right">
         	  <b> Piso:
         	</td> 
           <td colspan="2">
             <input type='text' name='afiDomPiso' value='<?=$afiDomPiso;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  
		  <tr>
           <td align="right">
         	  <b> Entre calle 1:
         	</td> 
           <td colspan="2">
             <input type='text' name='afiDomEntreCalle1' value='<?=$afiDomEntreCalle1;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  
		   <tr>
           <td align="right">
         	  <b> Entre calle 2:
         	</td> 
           <td colspan="2">
             <input type='text' name='afiDomEntreCalle2' value='<?=$afiDomEntreCalle2;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  
		   <tr>
           <td align="right">
         	  <b> Barrio/Paraje:
         	</td> 
           <td colspan="2">
             <input type='text' name='afiDomBarrioParaje' value='<?=$afiDomBarrioParaje;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  
		  <tr>
           <td align="right">
         	  <b> Municipio:
         	</td> 
           <td colspan="2">
             <input type='text' name='afiDomMunicipio' value='<?=$afiDomMunicipio;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  
		  <tr>
           <td align="right">
         	  <b> Provincia:
         	</td> 
           <td colspan="2">
             <input type='text' name='afiDomProvincia' value='<?=$afiDomProvincia;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  
		  <tr>
           <td align="right">
         	  <b> CP:
         	</td> 
           <td colspan="2">
             <input type='text' name='afiDomCP' value='<?=$afiDomCP;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  
		  <tr>
           <td align="right">
         	  <b>Telefono:
         	</td> 
           <td colspan="2">
             <input type='text' name='afiTelefono' value='<?=$afiTelefono;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  
		   <tr>
           <td align="right">
         	  <b>Departamento:
         	</td> 
           <td colspan="2">
             <input type='text' name='afidomdepartamento' value='<?=$afidomdepartamento;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  
		  <tr>
           <td align="right">
         	  <b>Localidad:
         	</td> 
           <td colspan="2">
             <input type='text' name='afidomlocalidad' value='<?=$afidomlocalidad;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  
           <tr>
           <td align="right">
         	  <b> Fecha de Nacimiento:
         	</td> 
           <td colspan="2">
             <input type='text' name='afifechanac' value='<?=fecha($afifechanac);?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  
		  <tr>
           <td align="right">
         	  <b> Fecha de Inscripcion:
         	</td> 
           <td colspan="2">
             <input type='text' name='fechainscripcion' value='<?=fecha($fechainscripcion)?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          
          <tr>
           <td align="right">
         	  <b> Efector Asignado:
         	</td> 
           <td colspan="2">
             <input type='text' name='nombreefecto' value='<?=$nombre;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  <tr>
           <td align="right">
         	  <b> Cuie:
         	</td> 
           <td colspan="2">
             <input type='text' name='cuie' value='<?=$cuie;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
		  <tr>
           <td align="right">
         	  <b> Cod Siisa:
         	</td> 
           <td colspan="2">
             <input type='text' name='cod_siisa' value='<?=$cod_siisa;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
        </table>
      </td>      
     </tr>
   </table>     
 
 <!--<tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>   	 
     	<input type=button name="volver" value="Volver" onclick="document.location='beneficiarios_vista.php'"title="Volver al Listado" style="width=150px">     
    </td>
  </tr>
 </table></td></tr>-->
 
</table>


    
</form>
<?=fin_pagina();// aca termino ?>
