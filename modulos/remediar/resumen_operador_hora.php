<?php

require_once("../../config.php");

$result ='';
		$r=''; $where_tmp=""; $where_tmp2="";
  $sqla= "select *
            from permisos.grupos_usuarios a
            inner join permisos.grupos b on a.id_grupo=b.id_grupo
            where upper(uname) not like '%TODOS%' and upper(uname) not like '%PROG%' and upper(uname) not like '%ADMIN%' and id_usuario=".$_ses_user['id'];
             $val_permisa=sql($sqla, "Error permiso") or fin_pagina();
              if ($val_permisa->RecordCount()>0){  
			  		$where_tmp.="where usuarios.id_usuario=".$_ses_user['id']; 
					$r='readonly';
				} 
			$ad='';			  
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
	$where_tmp.= "where usuarios.id_usuario in (select id_usuario 
														FROM permisos.grupos
														left join permisos.grupos_usuarios on grupos.id_grupo=grupos_usuarios.id_grupo  
															where upper(uname)='$uname_w' or upper(uname)='$uname')";
		$ad='a';
	}	
	

if($_POST['buscar'] || $r!=''){
	if($where_tmp==''){$where_tmp.="where ";}else{ $where_tmp.=" and"; }
	if($_POST['f_desde']!='' && $_POST['f_hasta']!=''){
		$where_tmp.=" beneficiarios.fecha_carga between '".Fecha_db($_POST['f_desde'])."' and '".Fecha_db($_POST['f_hasta'])."' and";
	}
	if($_POST['quegrupo']!='-1' && $_POST['quegrupo']!=''){
	  	$where_tmp.= " usuarios.id_usuario in (select id_usuario
														FROM permisos.grupos
														left join permisos.grupos_usuarios on grupos.id_grupo=grupos_usuarios.id_grupo  
															where upper(uname)=upper('".$_POST['quegrupo']."')) and";
	}
	if($_POST['qbusk']!=''){
		$where_tmp.=" upper(usuarios.nombre||' '||usuarios.apellido) like upper('%".$_POST['qbusk']."%')";
	}
	if(substr($where_tmp,strlen(rtrim($where_tmp))-3,strlen(rtrim($where_tmp)))=='and'){
		$where_tmp=substr(rtrim($where_tmp),0,strlen(rtrim($where_tmp))-3);
	}
	if($where_tmp=='where '){$where_tmp="";}




	// echo $where_tmp;

		
/*							
$sql_tmp="select cast(fecha_carga as char(10)) as fecha_cargax,usuarios.nombre||' '||usuarios.apellido ,count(*)
					from uad.beneficiarios 
inner join  sistema.usuarios  on upper(beneficiarios.usuario_carga)=cast(usuarios.id_usuario as text)

where usuario_carga in (select cast(id_usuario as text)
														FROM permisos.grupos
														left join permisos.grupos_usuarios on grupos.id_grupo=grupos_usuarios.id_grupo  
															where upper(uname)='IPEC' or upper(uname)='IPEC')
AND cast(fecha_carga as char(10)) between '2011-09-01' and '2011-09-13'
and (EXTRACT(HOUR FROM  fecha_carga)>12 or (EXTRACT(HOUR FROM  fecha_carga)=12 and EXTRACT(MINUTE FROM  fecha_carga)>=30))
group by  cast(fecha_carga as char(10)) ,usuarios.nombre||' '||usuarios.apellido ";
	*/	
	$sql_tmp="select EXTRACT(HOUR FROM  fecha_carga) as hora,cast(fecha_carga as char(10)) as fecha_cargax,(usuarios.nombre||' '||usuarios.apellido) as nom_ape,count(*) as cant
					from uad.beneficiarios 
inner join  sistema.usuarios  on upper(beneficiarios.usuario_carga)=cast(usuarios.id_usuario as text) ";
	$sql_tmp.=$where_tmp;
	$sql_tmp.=" group by  EXTRACT(HOUR FROM  fecha_carga),cast(fecha_carga as char(10)) ,usuarios.nombre||' '||usuarios.apellido 
				order by usuarios.nombre||' '||usuarios.apellido,cast(fecha_carga as char(10)) ,EXTRACT(HOUR FROM  fecha_carga)";
	//sql($sql_tmp) or die; 	
		
		//echo $sql_tmp;
	$result = sql($sql_tmp) or die;
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
<form name=form1 action="resumen_operador_hora" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
      	
		<? 	if($r==''){ ?>
			<input type="text" name="qbusk" size="15" maxlength="15" value="<?=$_POST['qbusk']?>"/>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>		    
	     &nbsp;&nbsp;
	    <? $link=encode_link("resumen_operador_hora_excel.php",array("sql"=>$sql_tmp)); ?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">    
	  </td>
     </tr>
	 <tr>
	 <td align=center>
	 <b>Fecha Desde:</b><input type="text" size="10" maxlength="10" name="f_desde" onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);" value="<?=$_POST['f_desde']?>" <?=$r?>/><?=link_calendario('f_desde');?> <b>; Hasta: </b><input type="text" size="10" maxlength="10" name="f_hasta" onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);" value="<?=$_POST['f_hasta']?>" <?=$r?>/><?=link_calendario('f_hasta');} if($r=='' && $ad==''){?>&nbsp;&nbsp;&nbsp; <b>Grupo:</b>
	 		<select name="quegrupo">
	 			<option value="-1" <? if($_POST['quegrupo']=='-1'){ echo 'selected';}?>>Todos</option>
				<option value="REMEDIAR" <? if($_POST['quegrupo']=='REMEDIAR'){ echo 'selected';}?>>Remediar</option>
				<option value="IPEC" <? if($_POST['quegrupo']=='IPEC'){ echo 'selected';}?>>Ipec</option>
	 		</select><? }?>
	 </td>
	 </tr>
</table>



<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=6 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  <tr >
  	<td align=right id=mo>Fecha de Carga</td>
    <td align=right id=mo>Hora</td>
    <td align=right id=mo>Usuario</td>
    <td align=right id=mo>Cantidad</td>
  </tr>
 <? //print_r($result);
 if($result!=NULL){ 
	   while (!$result->EOF) {   ?>
		<tr <?=atrib_tr()?> >   
		 <td><?=$result->fields['fecha_cargax']?></td>
		 <td><?=$result->fields['hora']?></td>
		 <td><?=$result->fields['nom_ape']?></td>
		 <td><?=$result->fields['cant']?></td>
		</tr>
		<? $result->MoveNext();
	   }
	} 
	?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>