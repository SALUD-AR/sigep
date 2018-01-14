<?

require_once("../../config.php");
cargar_calendario();


$id_tarea=$parametros['id_tarea'] or $id_tarea=$_POST['id_tarea'];
$pagina=$parametros['pagina'] or $pagina=$_POST['pagina'];
echo $msg."<br>";
$cons="select nombre,apellido,id_usuario 
       from sistema.usuarios 
       join permisos.phpss_account on phpss_account.username=usuarios.login 
       and phpss_account.active='true' 
       order by nombre,apellido";
$result=sql($cons,"No se pudo recuperar los uusarios activos");
$consu="select nombre,apellido,id_patrocinante,id_usuario from patrocinantes join usuarios using(id_usuario) order by nombre,apellido";
$resulta=sql($consu,"No se pudo recuperar los patrocinantes")or fin_pagina();

echo $html_header;


if($pagina==1)
{
	$buscar="select * from tareas_soft where id_tarea_soft=$id_tarea";
	$re_buscar=sql($buscar,"Nose pudo recuperar los datos de la tarea")or fin_pagina();
	$id_patro=$re_buscar->fields['id_patrocinante'];
	$buscar1="select id_usuario,nombre,apellido from patrocinantes join usuarios using(id_usuario) where id_patrocinante=$id_patro";
	$re_buscar1=sql($buscar1,"No se pudo recuperar los datos del patrocinante")or fin_pagina();
	
	$usuario1=$_ses_user['name'];
	
	$p_usu=$re_buscar1->fields['id_usuario'];
	$npatro=$re_buscar1->fields['nombre'];
	$apatro=$re_buscar1->fields['apellido'];
	$nom_pat="$npatro"." "."$apatro";
	$id_usu=$re_buscar->fields['id_usuario'];
	$buscar2="select nombre,apellido from usuarios where id_usuario=$id_usu";
	$re_buscar2=sql($buscar2,"No se pudo recuperar los datos del encargado")or fin_pagina();
	$nenc=$re_buscar2->fields['nombre'];
	$aenc=$re_buscar2->fields['apellido'];
	$nom_enc="$nenc"." "."$aenc";

	//$id_usu=$re_buscar->fields['id_patrocinante'];
	$asunto=$re_buscar->fields['asunto'];
	$descripcion=$re_buscar->fields['descripcion'];
	$Fecha_Desde=Fecha($re_buscar->fields['vencimiento']);
	$Fecha_Hasta=Fecha($re_buscar->fields['aviso']);
	$peri=$re_buscar->fields['peridiocidad'];
	$tiempos=$re_buscar->fields['tiempo_periodicidad'];
	$tipos=$re_buscar->fields['tipo_tiempo'];
	//$id_prog=$re_buscar->fields['id_prog'];
	
}




if($_POST['boton']=="Finalizar Tarea")
{
	if ($_POST['chequeado']) { //si esta chequeado Periodicidad
 		$peri=1;
 		$perio="SI";
 		$fvenci1=$_POST["fecha_ven"];	
	    $faviso1=$_POST["fecha_aviso"];
 		$dif=diferencia_dias($fvenci1,$faviso1);
 		/*echo"---$dif "; 
 		die();*/		
 		if ($_POST['cm'])
 			{
	 			$m=1;
	 			$tiem=$_POST['mes1'];
	 			
	 			$Fecha_Desde=Fecha_db($_POST['fecha_ven']);
	 			list($anio,$mes,$dia)=explode("-",$Fecha_Desde);
		        $Fecha_Desde=date("Y-m-d",mktime(0,0,0,$mes+$tiem,$dia,$anio));
		        $f_7dias=date("Y-m-d",mktime(0,0,0,$mes,$dia+11,$anio));
		        $Fecha_Hasta=Fecha_db($_POST['fecha_aviso']);
		        list($anio,$mes,$dia)=explode("-",$Fecha_Hasta);
		        $Fecha_Hasta=date("Y-m-d",mktime(0,0,0,$mes,$dia+$dif,$anio));
		        $tiempos=$tiem;
				$tipos=$m;
 			}
 		if ($_POST['cs'])
 			{
	 			$m=2;
	 			$tiem=$_POST['semana1'];
	 			$total_d=$tiem*7;
	 			$Fecha_Desde=Fecha_db($_POST['fecha_ven']);
	 			list($anio,$mes,$dia)=explode("-",$Fecha_Desde);
		        $Fecha_Desde=date("Y-m-d",mktime(0,0,0,$mes,$dia+$total_d,$anio));
		        $f_7dias=date("Y-m-d",mktime(0,0,0,$mes,$dia+11,$anio));
		        $tiempos=$tiem;
				$tipos=$m;
		        $Fecha_Hasta=Fecha_db($_POST['fecha_aviso']);
		        list($anio,$mes,$dia)=explode("-",$Fecha_Hasta);
		        $Fecha_Hasta=date("Y-m-d",mktime(0,0,0,$mes,$dia+$dif,$anio));
 			}
 		if ($_POST['cd'])
 			{
	 			$m=3;
	 			$tiem=$_POST['dia1'];
	 			$Fecha_Desde=Fecha_db($_POST['fecha_ven']);
	 			list($anio,$mes,$dia)=explode("-",$Fecha_Desde);
		        $Fecha_Desde=date("Y-m-d",mktime(0,0,0,$mes,$dia+$tiem,$anio));
		        $f_7dias=date("Y-m-d",mktime(0,0,0,$mes,$dia+11,$anio));
		        $tiempos=$tiem;
				$tipos=$m;
		        $Fecha_Hasta=Fecha_db($_POST['fecha_aviso']);
		        list($anio,$mes,$dia)=explode("-",$Fecha_Hasta);
		        $Fecha_Hasta=date("Y-m-d",mktime(0,0,0,$mes,$dia+$dif,$anio));
		        
		        
 			}
 			
 			$id_tarea=$_POST['id_tarea'];
 			$id_patro=$_POST['patroc'];
 			$id_usu=$_POST['encargado'];
 			$asunto=$_POST['asunto'];
			$descripcion=$_POST['observacion'];
 						
 			
 			$consu1="select id_usuario from patrocinantes where id_patrocinante=$id_patro";
			$resulta1=sql($consu1,"No se pudo recuperar los patrocinantes")or fin_pagina();
			$i_usu=$resulta1->fields['id_usuario'];
			
 			if($i_usu==$id_usu)
 			{
 			$comentario="El Encargado y el Patrocinante no puede ser la misma persona";
 			/*echo("$i_usu -- $id_usu");
			die();	*/
 			}
 			else
 			
 			{
 			
			$upda="update tareas_soft set tarea_activa=0 where id_tarea_soft=$id_tarea";	
			sql($upda,"No se pudo actualizar los datos de las tareas")or fin_pagina();
			$pagina=1;
 			
		    $query3="select nextval('tareas_soft_id_tarea_soft_seq')as id_tarea";
	    	$id_ta=sql($query3,"<br> error al traer el id_transporte<br>") or fin_pagina();
	    	$id_tareas=$id_ta->fields['id_tarea'];
 		
 		if($peri==1)
 		{
	 		$campos="(id_tarea_soft,id_usuario,asunto,aviso,vencimiento,peridiocidad,descripcion,id_patrocinante,tiempo_periodicidad,tipo_tiempo,aviso_7dias)";
	 		$inser="insert into tareas_soft $campos VALUES ". 
	 		"($id_tareas,$id_usu,'$asunto','$Fecha_Hasta','$Fecha_Desde',$peri,'$descripcion',$id_patro,'$tiem',$m,'$f_7dias')";
	 		sql($inser,"No se pudo guardar la nueva tarea")or fin_pagina();
	 		$camp="(id_tarea_soft,id_usuario)";
	 		$ins="insert into tarea_usuario $camp values".
	 		"($id_tareas,$id_usu)";
	 		sql($ins,"No se pudo guardar usuario y tarea")or fin_pagina();
 		}
 		else 
 		{
	 		$campos="(id_tarea_soft,id_usuario,asunto,aviso,vencimiento,peridiocidad,descripcion,id_patrocinante,aviso_7dias)";
	 		$inser="insert into tareas_soft $campos VALUES ". 
	 		"($id_tareas,$id_usu,'$asunto','$Fecha_Hasta','$Fecha_Desde',$peri,'$descripcion',$id_patro,'$f_7dias')";
	 		sql($inser,"No se pudo guardar la nueva tarea")or fin_pagina();	
	 		$camp="(id_tarea_soft,id_usuario)";
	 		$ins="insert into tarea_usuario $camp values".
	 		"($id_tareas,$id_usu)";
	 		sql($ins,"No se pudo guardar usuario y tarea")or fin_pagina();
 		}
 	}
 		$pagina=1;	
 	}
 					
 	
 	
 	else 
 	{
 			
 	
	$id_tarea=$_POST['id_tarea'];
	$upda="update tareas_soft set tarea_activa=0 where id_tarea_soft=$id_tarea";	
	sql($upda,"No se pudo actualizar los datos de las tareas")or fin_pagina();
	$pagina=1;
	}
	$Fecha_Hasta=Fecha($Fecha_Hasta);
 	$Fecha_Desde=Fecha($Fecha_Desde);
 	
 	$accion="La Tarea Se Finalizo";
 	$ref=encode_link("listado_tareas.php",array());?>
	<script>
	window.opener.location.href='<?=$ref?>';
	window.close();
	</script>
<?}


if($_POST['guardar']=="Guardar")
{

 	$tiem="";
 	$m=0;
 	
 	$Fecha_Hasta=Fecha_db($_POST['fecha_aviso']);
 	$Fecha_Desde=Fecha_db($_POST['fecha_ven']);
 	$Fecha_Hasta1=$_POST['fecha_aviso'];
 	$Fecha_Desde1=$_POST['fecha_ven'];
    list($anio,$mes,$dia)=explode("-",$Fecha_Desde);
	$f_7dias=date("Y-m-d",mktime(0,0,0,$mes,$dia+11,$anio)); 
 	if ($_POST['chequeado'])
 	{
 		$peri=1;
 		$perio="SI";
 		if ($_POST['cm'])
 			{
 			$m=1;
 			$tiem=$_POST['mes1'];
 			}
 		if ($_POST['cs'])
 			{
 			$m=2;
 			$tiem=$_POST['semana1'];
 			}
 		if ($_POST['cd'])
 			{
 			$m=3;
 			$tiem=$_POST['dia1'];
 			}		
 	}
 	else 
 	{
 		$peri=2;
 		$perio="NO";	
 	}
 	/*echo"tipo $m ---- tiempo $tiem peri $peri";
    die();*/
 	$tiempos=$tiem;
 	$tipos=$m;
 	$id_patro=$_POST['patroc'];
 	$id_usu=$_POST['encargado'];
 	$asunto=$_POST['asunto'];
	$descripcion=$_POST['observacion'];
 	$id_tarea=$_POST['id_tarea'];
 	
 	$consu1="select id_usuario from patrocinantes where id_patrocinante=$id_patro";
	$resulta1=sql($consu1,"No se pudo recuperar los patrocinantes")or fin_pagina();
	$i_usu=$resulta1->fields['id_usuario'];
			
 	if($i_usu==$id_usu)
 	{
 		$comentario="El Encargado y el Patrocinante no puede ser la misma persona";
 		/*echo("$i_usu -- $id_usu");
		die();	*/
 	}
 	else
 	{
 				
 	if($_POST['pagina']==1)
 	{
 		if($peri==1)
 		{
		 	$upda="update tareas_soft set asunto='$asunto',descripcion='$descripcion',aviso='$Fecha_Hasta',vencimiento='$Fecha_Desde',peridiocidad=$peri, 
		 	id_patrocinante=$id_patro,id_usuario=$id_usu,tiempo_periodicidad='$tiem',tipo_tiempo=$m,aviso_7dias='$f_7dias' where id_tarea_soft=$id_tarea";	
		 	sql($upda,"No se pudo actualizar los datos de las tareas")or fin_pagina();
 		}
 		else 
 		{
	 		$upda="update tareas_soft set asunto='$asunto',descripcion='$descripcion',aviso='$Fecha_Hasta',vencimiento='$Fecha_Desde',peridiocidad=$peri, 
		 	id_patrocinante=$id_patro,id_usuario=$id_usu,aviso_7dias='$f_7dias' where id_tarea_soft=$id_tarea";	
		 	sql($upda,"No se pudo actualizar los datos de las tareas")or fin_pagina();	
 		}
	 	 $pagina=1;
	 	
	 	 $fecha=fecha_db(date("d/m/Y",mktime()));
		 $usuario=$_ses_user['name'];
		
		 $fecdeb=fecha_db(date("Y/m/d"));
		 $sql_prog = "SELECT nombre,id_usuario,apellido,mail FROM usuarios where id_usuario=$id_usu";
		 $result_prog = sql($sql_prog,"no se pudo recuperar el nombre del proveedor") or fin_pagina();
		 $nom_prog=$result_prog->fields['nombre'];
		 $ape_prog=$result_prog->fields['apellido'];
		 $mail_prog=$result_prog->fields['mail'];
		 $a_n="$nom_prog"." "."$ape_prog";
		 //$id_p=$result_prog->fields['id_patrocinante'];
		 $sql_pat = "select nombre,apellido,id_patrocinante,mail from patrocinantes join usuarios using(id_usuario) where id_patrocinante=$id_patro";
		 $result_pat = sql($sql_pat,"no se pudo recuperar los datos del patrocinante") or fin_pagina();
		 $nom_pat=$result_pat->fields['nombre'];
		 $ape_pat=$result_pat->fields['apellido'];
		 $mail=$result_pat->fields['mail'];
		 $nom_ape="$nom_pat"." "."$ape_pat";
		 
		 
		if($usuario=="$a_n")
		 	{
		 	$contenido="Se modificaron algunos datos de la tarea que fue asignada a $a_n  \n\n";
		 	$contenido.="Asunto: $asunto \n\n";
		 	$contenido.="Vencimiento: $Fecha_Desde1  Aviso $Fecha_Hasta1 \n\n";
			$contenido.="Descripcion: $descripcion \n\n";		 
		 	//$contenido.="Periodicidad $perio \n";		 
		 	$asunto="Notificación de modificacion en la tarea asignada";	
		 		
		 	$para="$mail";
		 	//enviar_mail($para,$asunto,$contenido,"","","");
		 	}
		 
		
		 if($usuario=="$nom_ape")
		    {
		    	$contenido="Se modificaron algunos datos de la tarea asignada por $nom_ape \n\n";
		 		$contenido.="Asunto: $asunto \n\n";
		 		$contenido.="Vencimiento: $Fecha_Desde1  Aviso $Fecha_Hasta1 \n\n";
		 		$contenido.="Descripcion: $descripcion \n\n";		 
		 		//$contenido.="Periodicidad $perio \n";		 
				 $asunto="Notificación de modificacion en la tarea asignada";
		    	
		       $para="$mail_prog";
		       //enviar_mail($para,$asunto,$contenido,"","","");      
		    }
	 	
	 	
 	}
 	else 
 	{
 		$query3="select nextval('tareas_soft_id_tarea_soft_seq')as id_tarea";
	    $id_ta=sql($query3,"<br> error al traer el id_transporte<br>") or fin_pagina();
	    $id_tareas=$id_ta->fields['id_tarea'];
 		
 		if($peri==1)
 		{
	 		$campos="(id_tarea_soft,id_usuario,asunto,aviso,vencimiento,peridiocidad,descripcion,id_patrocinante,tiempo_periodicidad,tipo_tiempo,aviso_7dias)";
	 		$inser="insert into tareas_soft $campos VALUES ". 
	 		"($id_tareas,$id_usu,'$asunto','$Fecha_Hasta','$Fecha_Desde',$peri,'$descripcion',$id_patro,'$tiem',$m,'$f_7dias')";
	 		sql($inser,"No se pudo guardar la nueva tarea")or fin_pagina();
	 		$camp="(id_tarea_soft,id_usuario)";
	 		$ins="insert into tarea_usuario $camp values".
	 		"($id_tareas,$id_usu)";
	 		sql($ins,"No se pudo guardar usuario y tarea")or fin_pagina();
 		}
 		else 
 		{
	 		$campos="(id_tarea_soft,id_usuario,asunto,aviso,vencimiento,peridiocidad,descripcion,id_patrocinante,aviso_7dias)";
	 		$inser="insert into tareas_soft $campos VALUES ". 
	 		"($id_tareas,$id_usu,'$asunto','$Fecha_Hasta','$Fecha_Desde',$peri,'$descripcion',$id_patro,'$f_7dias')";
	 		sql($inser,"No se pudo guardar la nueva tarea")or fin_pagina();	
	 		$camp="(id_tarea_soft,id_usuario)";
	 		$ins="insert into tarea_usuario $camp values".
	 		"($id_tareas,$id_usu)";
	 		sql($ins,"No se pudo guardar usuario y tarea")or fin_pagina();
 		}
 		$pagina=2;	
 	}
 	}
 	$Fecha_Hasta=Fecha($Fecha_Hasta);
 	$Fecha_Desde=Fecha($Fecha_Desde);
 	
 	$accion="Tarea Grabada";
 	$ref=encode_link("listado_tareas.php",array());?>
	<script>
	window.opener.location.href='<?=$ref?>';
	window.close();
	</script> 
<?}?>

<script>
	
function control_nuevos()
{
 if (confirm('Esta Seguro que Desea Agregar la Tarea?'))return true;
 else return false;	
}//de function control_nuevos()

function chequear_todos()
{
//alert(document.all.chequeado.value);
if(document.all.chequeado.value==1)
{
	
		var chec=eval("document.all.mes");	
		chec.disabled=true;
	    var chec1=eval("document.all.semana");	
		chec1.disabled=true;
		var chec2=eval("document.all.dia");	
		chec2.disabled=true;
		document.all.mes.value=1;
	    document.all.dia.value=1;
	    document.all.semana.value=1;
	
	    document.all.chequeado.value=0;	
}
else
{
		var chec=eval("document.all.mes");	
		chec.disabled=false;
		var chec1=eval("document.all.semana");	
		chec1.disabled=false;
		var chec2=eval("document.all.dia");	
		chec2.disabled=false;
	    document.all.mes.value=0;
	    document.all.dia.value=0;
	    document.all.semana.value=0;
		document.all.chequeado.value=1;	
   } 
}

function chequear_dia()
{
//alert(document.all.chequeado.value);
if(document.all.dia.value==1)
{
	
		var chec=eval("document.all.mes");	
		chec.disabled=false;
	    var chec1=eval("document.all.semana");	
		chec1.disabled=false;
	    document.all.dia.value=1;
	    document.all.semana.value=0;
	    document.all.mes.value=0;
		
	    document.all.cd.value=0;	
}
else
{
		var chec=eval("document.all.mes");	
		chec.disabled=true;
	    var chec1=eval("document.all.semana");	
		chec1.disabled=true;
		document.all.semana.value=1;
		document.all.mes.value=1;
		document.all.dia.value=0;	
		document.all.cd.value=1;	
	
}
}


function chequear_semana()
{

	if(document.all.semana.value==1)
	{	
	    var chec=eval("document.all.mes");	
		chec.disabled=false;
		var chec2=eval("document.all.dia");	
		chec2.disabled=false;
	    document.all.semana.value=1;	
	    document.all.mes.value=0;	
	    document.all.dia.value=0;	
	    document.all.cs.value=0;	
	}
	else
	{
	
		var chec=eval("document.all.mes");	
		chec.disabled=true;
		var chec2=eval("document.all.dia");	
		chec2.disabled=true;
	    document.all.semana.value=0;
	    document.all.mes.value=1;	
	    document.all.dia.value=1;		
	    document.all.cs.value=1;		
	}
	
}	

function chequear_mes()
{

	if(document.all.mes.value==1)
	{
		var chec1=eval("document.all.semana");	
		chec1.disabled=false;
		var chec2=eval("document.all.dia");	
		chec2.disabled=false;	
	    document.all.mes.value=1;
	    document.all.dia.value=0;
	    document.all.semana.value=0;
	    document.all.cm.value=0;
	}
	else
	{
		
		var chec1=eval("document.all.semana");	
		chec1.disabled=true;
		var chec2=eval("document.all.dia");	
		chec2.disabled=true;
	    document.all.mes.value=0;	
	    document.all.dia.value=1;
	    document.all.semana.value=1;
	    document.all.cm.value=1;	
	}

} 
function comparar()
{
 	var id_u=eval("document.all.encargado");
 	var id_p=eval("document.all.patroc");
 	alert(id_p);
 	alert(id_u);	

}


</script>

<form name="form1" method="POST" action="cargar_tarea.php">
<?echo "<center><b><font size='+2' color='red'>$accion</font></b></center>";?>
<input type='hidden' name='id_tarea' value='<?=$id_tarea?>'>
<input type='hidden' name='cd' <?if($tipos==3){?> value="1"<?}else{?> value="0"<?}?>>
<input type='hidden' name='cm' <?if($tipos==1){?> value="1"<?}else{?> value="0"<?}?>>
<input type='hidden' name='cs' <?if($tipos==2){?> value="1"<?}else{?> value="0"<?}?>>
  <table width="95%" align="center" id="mo">
   <tr>
    <td align="center">
    <font  size="4" color="Red"><b><?=$comentario?></b></font>
 	</td>
 	</tr>
 	<tr>
    <td align="center">
    <font  size="4"><b>Tareas</b></font>
 	</td>
   </tr>
  </table>
  <table width="95%" align="center" class="bordes">
   <tr>
	   <td align="center">
	   <b>Asunto</b>    
	    <input type="text" name="asunto" size="70" value="<?=$asunto?>" ><br><br>
	   </td>
   </tr>
   <tr>
	   <td align="center"> 
	   <?
	    if ($Fecha_Desde=='') $Fecha_Desde=date("d/m/Y");
	 	echo "<b>Vencimiento: </b>";
	 	echo "<input type=text size=10 name=fecha_ven value='$Fecha_Desde' title='Ingrese la fecha de vencimiento'>";
	 	echo link_calendario("fecha_ven");
	 	?>
	 	<!--<input type="button" name="m" value="M">-->
	 	<?
	 	echo "&nbsp;&nbsp;&nbsp;&nbsp;";
	 	?>
	 	<b>Encargado</b>
	 	<select name="encargado">
	 	<?
	 	while (!$result->EOF)
	        {?>
		        <option value="<?=$result->fields['id_usuario']?>"
		        <?if($result->fields['id_usuario']==$id_usu){?>selected<?}?>>
		        <?=$result->fields['nombre']?>&nbsp;<?=$result->fields['apellido']?>
		        </option>
		        <?        
		        $result->MoveNext();
	        } ?>
	    </select>
	 	</td>
 	</tr>
 	<tr>
	    <td align="center"> 
	 	<?
		if ($Fecha_Hasta=='') $Fecha_Hasta=date("d/m/Y");
	 	echo "<b>Aviso: </b>";
	 	echo "<input type=text size=10 name=fecha_aviso value='$Fecha_Hasta' title='Ingrese la fecha de aviso'>";
	 	echo link_calendario("fecha_aviso");//onclick="control()"
	 	?>
	 	<!--<input type="button" name="m" value="M">-->
	 	<?
	 	echo "&nbsp;&nbsp;&nbsp;&nbsp;";
	 	?>
	 	<b>Patrocinante</b>
	 	<select name="patroc">
	 	<?
	 	while(!$resulta->EOF)
	        {?>
		        <option value="<?=$resulta->fields['id_patrocinante']?>"
		        <?if($resulta->fields['id_patrocinante']==$id_patro){?>selected<?}?>>
		        <?=$resulta->fields['nombre']?>&nbsp;<?=$resulta->fields['apellido']?>
		        </option>
		        <?        
		        $resulta->MoveNext();
	        } 
	        ?>
	    </select> &nbsp;&nbsp;	
	 	</td>
 	</tr> 
 	<tr>
	    <td align="center"> 
	 	
	 	<input type="checkbox" name="chequeado"  <?if($peri==1){?>checked value="1"<?}else{?> value="0"<?}?> onclick="chequear_todos()">
	 	<b>Periodicidad</b> &nbsp; &nbsp;
	 	<input type="checkbox" name="dia" value="1" <?if($tipos==3){?>checked value="1"<?}else{?> value="0" disabled<?}?> onclick="chequear_dia()"><b>Dias</b> 
	 	<select name="dia1">
	 	<?
	 	$i=1;
	 	while($i<=31)
	        {?>
		        <option value="<?=$i?>"
		        <?if($i==$tiempos){?>selected<?}?>>
		        <?=$i?>
		        </option>
		        <?$i++;      
	        } 
	        ?>
	    </select> &nbsp;&nbsp;	
	 	<input type="checkbox" name="semana" value="1" <?if($tipos==2){?>checked value="1"<?}else{?> value="0" disabled<?}?> onclick="chequear_semana()"><b>Semana</b> 	
	 	
	 	<select name="semana1">
	 	<?
	 	$i=1;
	 	while ($i<=24)
	        {?>
		        <option value="<?=$i?>"
		        <?if($i==$tiempos){?>selected<?}?>>
		        <?=$i?>
		        </option>
		        <?$i++;       
	        } 
	        ?>
	    </select>&nbsp;&nbsp;
	 	<input type="checkbox" name="mes" value="1" <?if($tipos==1){?>checked value="1"<?}else{?> value="0" disabled<?}?> onclick="chequear_mes()"><b>Meses</b> 	
	 	<select name="mes1">
	 	<?
	 	$i=1;
	 	while ($i<=12)
	        {?>
		        <option value="<?=$i?>"
		        <?if($i==$tiempos){?>selected<?}?>>
		        <?=$i?>
		        </option>
		        <?$i++;       
	        } 
	        ?>
	    </select>
	 	<br><br>
	 	</td>
 	</tr> 
 	<tr>
	    <td align="center"> 
	 	<?
	 	if ($Fecha_Desde=='') $Fecha_Desde=date("d/m/Y");
	 	echo "<b>Proximo Aviso </b>";
	 	echo "<input type=text size=10 name=fecha_avis value='$Fecha_Desde' title='Ingrese la fecha de vencimiento'>";
	 	echo link_calendario("fecha_avis");
	 	?>
	 	</td>
 	</tr>
	<tr>
	    <td align="center"> 
	    <b>Descripcion:</b><br>
	    <textarea name="observacion" cols="90" wrap="VIRTUAL" id="observacion" rows="6"><?echo"$descripcion";?></textarea> 	
	 	<br><br>
	    </td>
 	</tr> 
 	</table> 
	<TABLE align="center" cellspacing="0">
	<tr>
	
	<?
	 
	if($pagina==1)
	{
		
	if(($usuario1==$nom_enc)||($usuario1==$nom_pat))
	 {?>
		<td> <input type="submit" name="guardar" value="Guardar" onclick="return control_nuevos()">
		</TD>
		<td>
		<input type="submit" name="boton" value="Finalizar Tarea">
		</td>
		
		<?}?>
		<input type='hidden' name='pagina' value='1'>
		<td>
		<input type="button"  name="boton_volver" value="Volver"  onclick="window.location='listado_tareas.php'"> 
		</td>
		<?
	}
	else
	{
		?>
		<td> <input type="submit" name="guardar" value="Guardar" onclick="return control_nuevos()">
		</TD>
		<td>
		<input type="submit" name="boton" value="Finalizar Tarea">
		</td>
		<input type='hidden' name='pagina' value='2'>
		<td>
		<?$ref=encode_link("listado_tareas.php",array());?>
		<input type="button"  name="boton_volver" value="Cerrar"  onclick="window.opener.location.href='<?=$ref?>';window.close()"> 
		</td>
		<?
	}
	?>

	</tr>
	</TABLE>	
</from>
</body>
</html>
