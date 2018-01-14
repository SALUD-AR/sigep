<?php

require_once("../../config.php");


		$r=''; $where_tmp=""; $where_tmp2="";
  $sqla= "select *
            from permisos.grupos_usuarios a
            inner join permisos.grupos b on a.id_grupo=b.id_grupo
            where upper(uname) not like '%TODOS%' and upper(uname) not like '%PROG%' and upper(uname) not like '%ADMIN%' and id_usuario=".$_ses_user['id'];
             $val_permisa=sql($sqla, "Error permiso") or fin_pagina();
              if ($val_permisa->RecordCount()>0){  
			  		$where_tmp.="where t1.id_usuario='".$_ses_user['id']."'"; 
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
	$where_tmp.= "where t1.id_usuario in (select cast(id_usuario as text)
														FROM permisos.grupos
														left join permisos.grupos_usuarios on grupos.id_grupo=grupos_usuarios.id_grupo  
															where upper(uname)='$uname_w' or upper(uname)='$uname')";
		$ad='a';
	}	
	

if($_POST['buscar'] || $r!=''){
	if($where_tmp==''){$where_tmp.="where ";}else{ $where_tmp.=" and"; }
	if($_POST['f_desde']!='' && $_POST['f_hasta']!=''){
		$where_tmp.=" t2.fecha_carga between '".Fecha_db($_POST['f_desde'])."' and '".Fecha_db($_POST['f_hasta'])."' and";
	}
	if($_POST['quegrupo']!='-1' && $_POST['quegrupo']!=''){
	  	$where_tmp.= " t1.id_usuario in (select cast(id_usuario as text)
														FROM permisos.grupos
														left join permisos.grupos_usuarios on grupos.id_grupo=grupos_usuarios.id_grupo  
															where upper(uname)=upper('".$_POST['quegrupo']."')) and";
	}
	if($_POST['qbusk']!=''){
		$where_tmp.=" upper(t1.nombre||' '||t1.apellido) like upper('%".$_POST['qbusk']."%')";
	}
	if(substr($where_tmp,strlen(rtrim($where_tmp))-3,strlen(rtrim($where_tmp)))=='and'){
		$where_tmp=substr(rtrim($where_tmp),0,strlen(rtrim($where_tmp))-3);
	}
	if($where_tmp=='where '){$where_tmp="";}




	// echo $where_tmp;

		
							
$sql_tmp="DELETE FROM remediar.resumen_operador WHERE creador=".$_ses_user['id'].";

		insert into remediar.resumen_operador(fecha_carga,usuario_carga,fr_c_id_mus,creador)
		SELECT t2.fecha_carga,case when t1.nombre||' '||t1.apellido <>'' then t1.nombre||' '||t1.apellido  else t2.usuario_carga end as usuario_carga
		,t2.fr_c_id_mus,".$_ses_user['id']."
		FROM
		(select upper(apellido)  as apellido,upper(nombre) as nombre,cast(id_usuario as text) as id_usuario
			from sistema.usuarios) as t1
		LEFT JOIN	
		(select cast(beneficiarios.fecha_carga as char(10)) as fecha_carga,upper(beneficiarios.usuario_carga) as usuario_carga
		,count(beneficiarios.id_beneficiarios) as fr_c_id_mus
				from uad.beneficiarios
				where EXISTS (select clavebeneficiario 
												from uad.remediar_x_beneficiario 
												where remediar_x_beneficiario.clavebeneficiario=beneficiarios.clave_beneficiario 
														and remediar_x_beneficiario.usuario_carga=beneficiarios.usuario_carga)
				group by cast(beneficiarios.fecha_carga as char(10)),upper(beneficiarios.usuario_carga)) as t2 on t1.id_usuario=t2.usuario_carga
		 $where_tmp;
		
		
		
		insert into remediar.resumen_operador(fecha_carga,usuario_carga,fr_s_id_mus,creador)
		SELECT t2.fecha_carga,case when t1.nombre||' '||t1.apellido <>'' then t1.nombre||' '||t1.apellido  else t2.usuario_carga end as usuario_carga
		,t2.fr_s_id_mus,".$_ses_user['id']."
		FROM
		(select upper(apellido)  as apellido,upper(nombre) as nombre,cast(id_usuario as text) as id_usuario
			from sistema.usuarios) as t1
		LEFT JOIN	
		(select cast(remediar_x_beneficiario.fecha_carga as char(10)) as fecha_carga,upper(remediar_x_beneficiario.usuario_carga) as usuario_carga
		,count(remediar_x_beneficiario.nroformulario) as fr_s_id_mus
					from uad.remediar_x_beneficiario
					where EXISTS (select clave_beneficiario 
													from uad.beneficiarios 
													where beneficiarios.clave_beneficiario=remediar_x_beneficiario.clavebeneficiario 
															and beneficiarios.usuario_carga<>remediar_x_beneficiario.usuario_carga)
				group by cast(remediar_x_beneficiario.fecha_carga as char(10)),upper(remediar_x_beneficiario.usuario_carga)) as t2 on t1.id_usuario=t2.usuario_carga
		$where_tmp;
		
		
			
			
		insert into remediar.resumen_operador(fecha_carga,usuario_carga,id_s_fr,creador)
		SELECT t2.fecha_carga,case when t1.nombre||' '||t1.apellido <>'' then t1.nombre||' '||t1.apellido  else t2.usuario_carga end as usuario_carga
		,t2.id_s_fr,".$_ses_user['id']."
		FROM
		(select upper(apellido)  as apellido,upper(nombre) as nombre,cast(id_usuario as text) as id_usuario
			from sistema.usuarios) as t1
		LEFT JOIN	
		(select cast(beneficiarios.fecha_carga as char(10)) as fecha_carga,upper(beneficiarios.usuario_carga) as usuario_carga
		,count(beneficiarios.id_beneficiarios) as id_s_fr
					from uad.beneficiarios
					where beneficiarios.clave_beneficiario not in (select clavebeneficiario from uad.remediar_x_beneficiario)
					group by cast(beneficiarios.fecha_carga as char(10)),upper(beneficiarios.usuario_carga)) as t2 on t1.id_usuario=t2.usuario_carga
		$where_tmp;
	
		
		
		
		
		insert into remediar.resumen_operador(fecha_carga,usuario_carga,t_no_env,creador)
		SELECT t2.fecha_carga,case when t1.nombre||' '||t1.apellido <>'' then t1.nombre||' '||t1.apellido  else t2.usuario_carga end as usuario_carga
		,t2.t_no_env,".$_ses_user['id']."
		FROM
		(select upper(apellido)  as apellido,upper(nombre) as nombre,cast(id_usuario as text) as id_usuario
			from sistema.usuarios) as t1
		LEFT JOIN	
		(select cast(beneficiarios.fecha_verificado as char(10)) as fecha_carga,upper(beneficiarios.usuario_verificado) as usuario_carga
		,count(beneficiarios.id_beneficiarios) as t_no_env
					from uad.beneficiarios
					where upper(beneficiarios.estado_envio)='N'
					group by cast(beneficiarios.fecha_verificado as char(10)),upper(beneficiarios.usuario_verificado))  as t2 on t1.id_usuario=t2.usuario_carga
		 $where_tmp;
	";
		sql($sql_tmp) or die; 	
		
		//echo $sql_tmp;
	
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
<form name=form1 action="resumen_operador" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
      	
		<? 	$sql="SELECT t1.fecha_carga,t1.usuario_carga,case when t2.fr_c_id_mus is null then 0 else t2.fr_c_id_mus end fr_c_id_mus
		,case when t3.fr_s_id_mus is null then 0 else t3.fr_s_id_mus end fr_s_id_mus
		,case when t4.id_s_fr is null then 0 else t4.id_s_fr end id_s_fr
		,case when t5.t_no_env is null then 0 else t5.t_no_env end t_no_env
					FROM (
							select fecha_carga,usuario_carga
							from remediar.resumen_operador
							where creador=".$_ses_user['id']."
							group by fecha_carga,usuario_carga
						  ) as t1
					LEFT JOIN (
								select fecha_carga,usuario_carga,fr_c_id_mus
								from remediar.resumen_operador
								where creador=".$_ses_user['id']." and fr_c_id_mus>0
							 ) as t2 on t1.fecha_carga=t2.fecha_carga and t1.usuario_carga=t2.usuario_carga
					LEFT JOIN (
								select fecha_carga,usuario_carga,fr_s_id_mus
								from remediar.resumen_operador
								where creador=".$_ses_user['id']." and fr_s_id_mus>0
							  ) as t3 on t1.fecha_carga=t3.fecha_carga and t1.usuario_carga=t3.usuario_carga
					LEFT JOIN (
								select fecha_carga,usuario_carga,id_s_fr
								from remediar.resumen_operador
								where creador=".$_ses_user['id']." and id_s_fr>0
							  ) as t4 on t1.fecha_carga=t4.fecha_carga and t1.usuario_carga=t4.usuario_carga
					LEFT JOIN (
								select fecha_carga,usuario_carga,t_no_env
								from remediar.resumen_operador
								where creador=".$_ses_user['id']." and t_no_env>0
							  ) as t5 on t1.fecha_carga=t5.fecha_carga and t1.usuario_carga=t5.usuario_carga
					where not (t2.fr_c_id_mus is null and t3.fr_s_id_mus is null and t4.id_s_fr is null and t5.t_no_env is null)
					group by t1.fecha_carga,t1.usuario_carga,fr_c_id_mus,fr_s_id_mus,id_s_fr,t_no_env	
					order by t1.usuario_carga,t1.fecha_carga
					";
			if($r==''){ ?>
			<input type="text" name="qbusk" size="15" maxlength="15" value="<?=$_POST['qbusk']?>"/>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>		    
	     &nbsp;&nbsp;
	    <? $link=encode_link("resumen_operador_excel.php",array("sql"=>$sql)); ?>
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
    <td align=right id=mo>Factores C/Identificacion del mismo usuario</td>
    <td align=right id=mo>Factores S/Identificacion del mismo usuario</td>
    <td align=right id=mo>Identificacion S/Factores</td>
    <td align=right id=mo>Fichas a no enviado por el usuario</td>
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
     <td><?=$result->fields['fr_c_id_mus']?></td>
     <td><?=$result->fields['fr_s_id_mus']?></td>
     <td><?=$result->fields['id_s_fr']?></td>
     <td><?=$result->fields['t_no_env']?></td>
    </tr>
	<?$result->MoveNext();
   }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>