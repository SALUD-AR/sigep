<?php

require_once("../../config.php");

$a = array(
        "f_desde" => $f_desde,
		"f_hasta" => $f_hasta,
       );
if($_POST['buscar']){
	if($_POST['f_desde']=='' && $_POST['f_hasta']==''){
	$a = array(
       );
	}
}
variables_form_busqueda("resumen_ficha_rr",$a);
$orden = array(
        "default" => "1",
        "1" => "upper(t5.nombre)||' '||upper(t5.apellido),t1.fecha_carga",
       );

$filtro = array(
        "upper(t5.nombre)||' '||upper(t5.apellido)" => "Usuario",
       );
	   
	if($f_desde!='' && $f_hasta!=''){
	  	$where_tmp="t1.fecha_carga between '".Fecha_db($f_desde)."' and '".Fecha_db($f_hasta)."' ";
	  }	  


$sql_tmp="select t1.fecha_carga,case when t5.nombre||' '||t5.apellido <>'' then t5.nombre||' '||t5.apellido  else t1.usuario_carga end as usuario_carga,t1.ctotal
from (select cast(remediar_x_beneficiario.fecha_carga as char(10)) as fecha_carga,upper(usuario_carga) as usuario_carga,count(clavebeneficiario) as ctotal
			from uad.remediar_x_beneficiario
			group by cast(remediar_x_beneficiario.fecha_carga as char(10)),upper(usuario_carga)) as t1
LEFT JOIN
		(select upper(apellido)  as apellido,upper(nombre) as nombre,cast(id_usuario as text) as id_usuario
			from sistema.usuarios) as t5 on t1.usuario_carga=t5.id_usuario";
			$r='';
			
  $sqla= "select *
            from permisos.grupos_usuarios a
            inner join permisos.grupos b on a.id_grupo=b.id_grupo
            where upper(uname) not like '%TODOS%' and upper(uname) not like '%PROG%' and upper(uname) not like '%ADMIN%' and id_usuario=".$_ses_user['id'];
             $val_permisa=sql($sqla, "Error permiso") or fin_pagina();
              if ($val_permisa->RecordCount()>0){  
			  		$sql=$sql_tmp." where t1.usuario_carga='".$_ses_user['id']."'"; 
					$r='readonly';
				} 
						  
	$queryCategoria="SELECT upper(grupos.uname)as uname
			FROM permisos.grupos
			left join permisos.grupos_usuarios on grupos.id_grupo=grupos_usuarios.id_grupo 
  	where upper(uname) like '%ADMIN %' and id_usuario=".$_ses_user['id']."
	group by upper(grupos.uname)";
	$resultado=sql($queryCategoria, "Error al traer el Comprobantes") or fin_pagina();
	if ($resultado->recordcount()>0) {
	$uname=$resultado->fields['uname'];
	$uname_w= substr($uname,6,strlen($uname));
	if(trim($where_tmp)!=''){$where_tmp .= " and ";}
	$where_tmp .= " t1.usuario_carga in (select cast(id_usuario as text)
														FROM permisos.grupos
														left join permisos.grupos_usuarios on grupos.id_grupo=grupos_usuarios.id_grupo  
															where upper(uname)='$uname_w' or upper(uname)='$uname')";
	}													
	
echo $html_header;
?>
<script>
//Validar Fechas
function esFechaValida(fecha){
    if (fecha != undefined && fecha.value != "" ){
        if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)){
            alert("formato de fecha no válido (dd/mm/aaaa)");
            return false;
        }
        var dia  =  parseInt(fecha.value.substring(0,2),10);
        var mes  =  parseInt(fecha.value.substring(3,5),10);
        var anio =  parseInt(fecha.value.substring(6),10);
 
    switch(mes){
        case 1:
        case 3:
        case 5:
        case 7:
        case 8:
        case 10:
        case 12:
            numDias=31;
            break;
        case 4: case 6: case 9: case 11:
            numDias=30;
            break;
        case 2:
            if (comprobarSiBisisesto(anio)){ numDias=29 }else{ numDias=28};
            break;
        default:
            alert("Fecha introducida errónea");
            return false;
    }
 
        if (dia>numDias || dia==0){
            alert("Fecha introducida errónea");
            return false;
        }
        return true;
    }
}
 
function comprobarSiBisisesto(anio){
if ( ( anio % 100 != 0) && ((anio % 4 == 0) || (anio % 400 == 0))) {
    return true;
    }
else {
    return false;
    }
}

var patron = new Array(2,2,4)
var patron2 = new Array(5,16)
function mascara(d,sep,pat,nums){
if(d.valant != d.value){
val = d.value
largo = val.length
val = val.split(sep)
val2 = ''
for(r=0;r<val.length;r++){
val2 += val[r]
}
if(nums){
for(z=0;z<val2.length;z++){
if(isNaN(val2.charAt(z))){
letra = new RegExp(val2.charAt(z),"g")
val2 = val2.replace(letra,"")
}
}
}
val = ''
val3 = new Array()
for(s=0; s<pat.length; s++){
val3[s] = val2.substring(0,pat[s])
val2 = val2.substr(pat[s])
}
for(q=0;q<val3.length; q++){
if(q ==0){
val = val3[q]

}
else{
if(val3[q] != ""){
val += sep + val3[q]
}
}
}
d.value = val
d.valant = val
}
}
</script>
<form name=form1 action="resumen_ficha_rr" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
      	
		<? if($r==''){ list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,strtoupper($where_tmp),"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>		    
	     &nbsp;&nbsp;
	    <? $link=encode_link("resumen_operador_excel.php",array("sql"=>substr($sql,0,strpos($sql,'LIMIT')))); ?>
        <!--<img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">    -->
	  </td>
     </tr>
	 <tr>
	 <td align=center>
	 <b>Fecha Desde:</b><input type="text" size="10" maxlength="10" name="f_desde" onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);" value="<?=$f_desde?>" <?=$r?>/><?=link_calendario('f_desde');?> <b>; Hasta: </b><input type="text" size="10" maxlength="10" name="f_hasta" onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);" value="<?=$f_hasta?>" <?=$r?>/><?=link_calendario('f_hasta');}?>
	 </td>
	 </tr>
</table>

<?$result = sql($sql) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=6 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  <tr >
  	<td align=right id=mo>Fecha de Carga</td>
    <td align=right id=mo>Usuario</td>
    <td align=right id=mo>Total Fichas R+R</td>
  </tr>
 <? 
   while (!$result->EOF) {
       $usuario_carga=$result->fields['usuario_carga'];
       $sq2="select upper(nombre||' '||apellido) as nomus
            from sistema.usuarios
            where upper(nombre||' '||apellido)  like '$usuario_carga%'";
       $resulta = sql($sq2) or die;
       if($resulta->recordcount()>0){
       $usuario_carga=$resulta->fields['nomus'];
       }
       ?>
    <tr <?=atrib_tr()?> >   
	 <td><?=$result->fields['fecha_carga']?></td>
     <td><?=$usuario_carga?></td>
     <td><?=$result->fields['ctotal']?></td>
    </tr>
	<?$result->MoveNext();
   }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>