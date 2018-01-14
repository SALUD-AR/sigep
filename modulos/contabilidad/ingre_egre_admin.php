<?
require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if(($_POST['notificar']=="Notificar Via Mail")or ($notificar_mail=="True")){
  $color1="#5090C0";
  $color2="#D5D5D5";
  $ret = "";
  
if ($_POST['notificar']=="Notificar Via Mail"){
 $sql= "SELECT 
	ingreso.id_ingreso,
  facturacion.factura.id_factura,
  facturacion.factura.cuie,
  facturacion.factura.fecha_carga,
  facturacion.factura.fecha_factura,
  facturacion.factura.periodo,
  facturacion.factura.estado,
  facturacion.factura.observaciones,
  facturacion.factura.id_factura,
  facturacion.factura.online,
  facturacion.factura.nro_exp_ext,
  facturacion.factura.fecha_exp_ext,
  facturacion.factura.periodo_contable,
  facturacion.factura.monto_prefactura,
  efe_conv.nombre
FROM
  facturacion.factura  
left join nacer.efe_conv using (cuie)
  left join contabilidad.ingreso on factura.id_factura=ingreso.numero_factura 
  
WHERE  (factura.estado='C') and (factura.cuie='$cuie') and (facturacion.factura.nro_exp_ext is not null) and (ingreso.id_ingreso is not null) 
ORDER BY ingreso.id_ingreso DESC";
}
if ($notificar_mail=="True"){
 $sql= "SELECT 
	ingreso.id_ingreso,
  facturacion.factura.id_factura,
  facturacion.factura.cuie,
  facturacion.factura.fecha_carga,
  facturacion.factura.fecha_factura,
  facturacion.factura.periodo,
  facturacion.factura.estado,
  facturacion.factura.observaciones,
  facturacion.factura.id_factura,
  facturacion.factura.online,
  facturacion.factura.nro_exp_ext,
  facturacion.factura.fecha_exp_ext,
  facturacion.factura.periodo_contable,
  facturacion.factura.monto_prefactura,
  efe_conv.nombre
FROM
  facturacion.factura  
left join nacer.efe_conv using (cuie)
  left join contabilidad.ingreso on factura.id_factura=ingreso.numero_factura 
  
WHERE  (factura.estado='C') and (factura.cuie='$cuie') and (facturacion.factura.nro_exp_ext is not null) 
       and (ingreso.id_ingreso is not null) and (factura.id_factura='$id_factura_notificar')
ORDER BY ingreso.id_ingreso DESC";
}
  
$res_factura=sql($sql,"no se puede ejecutar");
$res_factura->movefirst();

$nombre_efector=$res_factura->fields['nombre'];
$nro_factura=$res_factura->fields['id_factura'];

//aqui estaba el codigo de la insercion en el sistema de expediente

$exp_externo=$res_factura->fields['nro_exp_ext'];
$monto_prefactura=number_format($res_factura->fields['monto_prefactura'],2,',','.');
$periodo_contable=$res_factura->fields['periodo_contable'];
$id_factura=$res_factura->fields['id_factura'];

$sql= "SELECT * FROM nacer.efe_conv where cuie = '$cuie'";  
$res_efector=sql($sql,"no se puede ejecutar");
$referente=$res_efector->fields['referente'];

$query_t="SELECT sum 
			(facturacion.prestacion.cantidad*facturacion.prestacion.precio_prestacion) as total
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
			  where factura.id_factura=$id_factura";
		$total=sql($query_t,"NO puedo calcular el total");
		$query_t1="SELECT sum 
			(nomenclador.prestaciones_n_op.precio) as total1
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN nomenclador.prestaciones_n_op using (id_comprobante)
			  where factura.id_factura=$id_factura";
		$total1=sql($query_t1,"NO puedo calcular el total");
		$monto_factura=$total->fields['total']+$total1->fields['total1'];
		$monto_factura=number_format($monto_factura,2,',','.');

			$query=" SELECT sum(cantidad*monto) as total FROM
  			facturacion.debito  			
  			where id_factura='$id_factura'";
			$result_t_debitado=$db->Execute($query) or die($db->ErrorMsg());
			$debito=number_format($result_t_debitado->fields['total'],2,',','.');

			$query=" SELECT sum(cantidad*monto) as total FROM
  			facturacion.credito  			  
  			where id_factura='$id_factura'";
			$result_t_acreditado=$db->Execute($query) or die($db->ErrorMsg());
			$credito=number_format($result_t_acreditado->fields['total'],2,',','.');


date_default_timezone_set('Europe/Madrid');
setlocale(LC_TIME, 'spanish');
$dia_hoy=strftime("%A %d de %B de %Y");
	
$ret .= "<table width='65%'  bgcolor='$color1' align='center' style='border: 2px solid #000000; font-size=14px;'>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='center'>\n";
$ret .= "<b>FORMULARIO I</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1' align='right'>\n";
$ret .= "<td align='rigth'>\n";
$ret .= "<b>Plan Nacer, $dia_hoy</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1' align='left'>\n";
$ret .= "<td align='left'>\n";
$ret .= "<b>Efector: $nombre_efector. CUIE: $cuie. Número de Factura: $id_factura</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1' align='left'>\n";
$ret .= "<td align='left'>\n";
$ret .= "<b>Referente: $referente.</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<b>Por medio de la presente le notifico que se encuentra a disposición del prestador que usted representa
la suma de $ $monto_factura. Monto neto a liquidar que surge luego de haberse detectado en auditoría conceptos
erróneos en la facturación presentada. Por Consiguiente se debito la suma de $ $debito  y se acredito $ $credito.
transferida por el EPCSS en relacion a la cuasi-factura del mes de $periodo_contable, de $ $monto_prefactura. </b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<b>Asimismo informo a Usted que dicha transferencia se realizo a través del Expediente N°: $exp_externo</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "</table><br>\n";

$sql="select ingre-egre as total, ingre,egre,deve,egre_comp from
		(select sum (monto_deposito)as ingre from contabilidad.ingreso
		where cuie='$cuie') as ingreso,
		(select sum (monto_egreso)as egre from contabilidad.egreso
		where cuie='$cuie') as egreso,
		(select sum (monto_factura)as deve from contabilidad.ingreso
		where cuie='$cuie') as devengado,
		(select sum (monto_egre_comp)as egre_comp from contabilidad.egreso
		where cuie='$cuie') as egre_comp";
$res_saldo=sql($sql,"no puede calcular el saldo");
$total_depositado=number_format($res_saldo->fields['ingre'],2,',','.');
$total_egre_comp=number_format($res_saldo->fields['egre_comp'],2,',','.');

$sql_1="select sum (monto_egre_comp)as egre_incentivo
		from contabilidad.egreso
		where cuie='$cuie' and id_inciso=1";
$res_incentivo=sql($sql_1,"no puede calcular el saldo");
$total_incentivo=number_format($res_incentivo->fields['egre_incentivo'],2,',','.');

$saldo_real=number_format($saldo_real,2,',','.');
$ret .= "<table width=95% align=center style='font-size=10px'>\n";
$ret .= "<tr>\n";
$ret .= "<td align=center>\n";
$ret .= "<b>INFORMACION ANEXA\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "</table>\n";
$ret .= "<table width='65%'  bgcolor='$color1' align='center' style='border: 2px solid #000000; font-size=14px;'>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<b>Por medio de la presente le informo que: </b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<b>Su Saldo Acumulado es de: $ $total_depositado. </b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<b>Su Saldo Comprometido es de: $ $total_egre_comp. De los cuales $ $total_incentivo corresponde a incentivo.</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<b>Su Saldo Real es de: $ $saldo_real. </b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<b>Queda a su disposicion retirar en nuestras oficinas el informe de DEBITO / CREDITO en el horario de 8hs. a 18hs.</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1'>\n";
$ret .= "<td align='justify'>\n";
$ret .= "<font color=white><b>Se ruega contestar este mail al mail Oficial del Plan Nacer, para ser tenido en cuenta como acuse de recibo.</b></font>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "</table><br>\n";


$ret .= "<table width=95% align=center style='font-size=10px'>\n";
$ret .= "<tr>\n";
$ret .= "<td align=center>\n";
$ret .= "<b> NOTIFICACIONES\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "</table>\n";	
$ret .= "<table width='65%'  bgcolor='$color1' align='center' style='border: 2px solid #000000; font-size=14px;'>\n";
$ret .= "<tr bgcolor='$color1' align='left'>\n";
$ret .= "<td align='rigth'>\n";
$ret .= "<b>Queda Notificado Equipo de Plan Nacer a través del mail oficial.</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$ret .= "<tr bgcolor='$color1' align='left'>\n";
$ret .= "<td align='left'>\n";
$ret .= "<b>Queda Notificado el Efector: $nombre_efector. CUIE: $cuie. A través de los mail declarados.</b>\n";
$ret .= "</td>\n";
$ret .= "</tr>\n";
$sql= "select * from nacer.mail_efe_conv where cuie='$cuie'";
$res_mail=sql($sql,"no se puede ejecutar");
$res_mail->movefirst();
while (!$res_mail->EOF) { 
	$para=$res_mail->fields['mail'];
	$ret .= "<tr bgcolor='$color1' align='left'>\n";
	$ret .= "<td align='left'>\n";
	$ret .= "<b>Mail: $para.</b>\n";
	$ret .= "</td>\n";
	$ret .= "</tr>\n";
	$res_mail->movenext();
}
$ret .= "</table>\n";

echo $ret;
	
	$res_mail->movefirst();
	while (!$res_mail->EOF) { 
	$para=$res_mail->fields['mail'];
	enviar_mail_html($para,'Notificacion de Fondos',$ret,0,0,0);
	enviar_mail_html($para,'Notificacion de Fondos',$ret,0,0,0);
	$res_mail->movenext();
	}
	enviar_mail_html('plan.nacersl@gmail.com','Notificacion de Fondos',$ret,0,0,0);	
	enviar_mail_html('caropellegrini@hotmail.com','Notificacion de Fondos',$ret,0,0,0);

	$ref = encode_link("notificacion_excel.php",array("cuie"=>$cuie,"id_factura"=>$nro_factura,"saldo_real"=>$saldo_real));?>
	<script>
	window.open('<?=$ref?>')
	</script>
<?}



if($marcar1=="True"){
	 $db->StartTrans();
	$query="delete from contabilidad.ingreso
             where id_ingreso=$id_ingreso";
    sql($query, "Error al eliminar 1") or fin_pagina();
	
	$query="select id_egreso from contabilidad.incentivo
             where id_ingreso=$id_ingreso";
    $res=sql($query, "Error al eliminar 2") or fin_pagina();
	$id_egre_temp=$res->fields['id_egreso'];
	
	if ($id_egre_temp !=''){
		$query="delete from contabilidad.egreso
	             where id_egreso=$id_egre_temp";
	    sql($query, "Error al eliminar 3") or fin_pagina();
	}
	$query="delete from contabilidad.incentivo
             where id_ingreso=$id_ingreso";
    sql($query, "Error al eliminar 4") or fin_pagina();
	
    if ($id_egre_temp !='') $accion="Se elimino el Ingreso Numero: $id_ingreso. Se Elimino el Incentivo Vinculado en la Tabla Egreso y la Tabla Incentivo. NO SE ELIMINO LOS REGISTROS EN EL SISTEMA DE EXPEDIENTE"; 
	else $accion="Se elimino el Ingreso Numero: $id_ingreso. NO SE ELIMINO LOS REGISTROS EN EL SISTEMA DE EXPEDIENTE"; 
    $db->CompleteTrans();   
}

if($marcar2=="True"){
	 $db->StartTrans();
	$query="delete from contabilidad.egreso
             where id_egreso=$id_egreso";
    sql($query, "Error al eliminar") or fin_pagina();
    
    $query="delete from contabilidad.incentivo
             where id_egreso=$id_egreso";
    sql($query, "Error al eliminar") or fin_pagina();

    $accion="Se elimino el Egreso Numero: $id_egreso."; 
    $db->CompleteTrans();   
}

if ($_POST['guardar']=="Guardar Ingreso"){
	
	$cuie=$_POST['cuie'];
	$fecha_prefactura=Fecha_db($_POST['fecha_prefactura']);
	$comentario=$_POST['comentario'];
	$usuario=$_ses_user['name'];	
	$fecha=date("Y-m-d");	
	$numero_factura=$_POST['numero_factura'];	
	$id_servicio=$_POST['servicio'];
	$expediente_externo=$_POST['expediente_externo'];
	$fecha_exp_ext=$_POST['fecha_exp_ext'];
	$fecha_exp_ext=Fecha_db($fecha_exp_ext);
	
	$query_dupli="select * 
					from contabilidad.ingreso 
					where cuie='$cuie' and numero_factura in ('$numero_factura')";
	$res_dupli = sql($query_dupli,"no se puede ejecurar duplicado");
	
	if ($res_dupli->recordCount()==0){
		
		$query_t="SELECT sum 
			(facturacion.prestacion.cantidad*facturacion.prestacion.precio_prestacion) as total
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
			  where factura.id_factura=$numero_factura";
		$total=sql($query_t,"NO puedo calcular el total");
		$query_t1="SELECT sum 
			(nomenclador.prestaciones_n_op.precio) as total1
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN nomenclador.prestaciones_n_op using (id_comprobante)
			  where factura.id_factura=$numero_factura";
		$total1=sql($query_t1,"NO puedo calcular el total");
		$monto_factura=$total->fields['total']+$total1->fields['total1'];
		
		($monto_factura=='')?$monto_factura=0:$monto_factura=$monto_factura;
		
		$query_t="SELECT monto_prefactura, fecha_factura
			FROM
			  facturacion.factura			  
			  where factura.id_factura=$numero_factura";
		$total=sql($query_t,"NO puedo calcular el total");
		$monto_prefactura=$total->fields['monto_prefactura'];
		
		if ($monto_prefactura==''){
			$monto_prefactura=$monto_factura;
			$query="update facturacion.factura set 
	    			monto_prefactura='$monto_prefactura'   			
	    			where id_factura='$numero_factura'";
	    	sql($query, "Error al actualizar factura") or fin_pagina();
			
		}
		$fecha_factura=$total->fields['fecha_factura'];
	
		
	    $db->StartTrans();
		$q="select nextval('contabilidad.ingreso_id_ingreso_seq') as id_comprobante";
	    $id_comprobante=sql($q) or fin_pagina();
	    $id_comprobante=$id_comprobante->fields['id_comprobante'];	
	    $query="insert into contabilidad.ingreso
	             (id_ingreso,cuie,monto_prefactura,fecha_prefactura,monto_factura,fecha_factura,comentario,usuario,fecha,numero_factura,id_servicio)
	             values
	             ($id_comprobante,'$cuie','$monto_prefactura','$fecha_prefactura','$monto_factura','$fecha_factura','$comentario','$usuario','$fecha','$numero_factura','$id_servicio')";	
	    sql($query, "Error al insertar el comprobante") or fin_pagina();

	   	    
	     $query="select periodo_actual from facturacion.factura where id_factura='$numero_factura'";
	     $res=sql($query,'error al traer factura');
	     $periodo_actual=$res->fields['periodo_actual'];
	     	    
	    
	     
	     $query="update facturacion.factura set 
	    			nro_exp_ext='$expediente_externo',
	    			periodo_contable='$periodo_actual',
	    			fecha_exp_ext='$fecha_exp_ext'    			
	    			where id_factura='$numero_factura'";
	    sql($query, "Error al actualizar factura") or fin_pagina();
	    
	    $accion="Se guardo el Ingreso Numero: $id_comprobante";
	    
	    //codigo para la insercion en el sistema de expediente
	    
	    
$string_coment="Expediente Pago, en Archivo";
$estado="C";
$sql_update="update expediente.expediente set control=5, comentario1='$string_coment',estado='$estado' where id_factura='$numero_factura'";
sql($sql_update) or die;

$q_trans="select nextval('expediente.transaccion_id_transac_seq') as id_transac";
$id_transac=sql($q_trans) or fin_pagina();
$id_transac=$id_transac->fields['id_transac'];

$sql_temp="select * from expediente.expediente where id_factura='$numero_factura'";
$result_temp=sql($sql_temp) or die;
$id_expediente=$result_temp->fields['id_expediente'];
$id_area=1;
$fecha_mov = date("m/d/Y");

$sql_debito="SELECT * FROM facturacion.debito WHERE id_factura='$numero_factura'";
$result_debito = sql($sql_debito) or die;
$debito=0;
while (!$result_debito -> EOF) {
$debito=$debito+($result_debito->fields['monto'] * $result_debito->fields['cantidad']) ;
$result_debito->MoveNext();
};
if (!$debito) $debito=0;

$sql_credito="SELECT * FROM facturacion.credito WHERE id_factura='$numero_factura'";
$result_credito = sql($sql_credito) or die;
$credito=0;
while (!$result_credito -> EOF) {
$credito=$credito+($result_credito->fields['monto'] * $result_credito->fields['cantidad']);
$result_credito->MoveNext();
};
if (!$credito) $credito=0;

$usuario=$_ses_user['name'];

$sql_monto= "SELECT monto_prefactura FROM facturacion.factura 
			 	where id_factura=$numero_factura";
			  $res_monto=sql($sql_monto) or fin_pagina();
			  $monto=$res_monto->fields['monto_prefactura'];

$monto_pago=$monto-$debito+$credito;

$sql_cons_tranf="select num_tranf from expediente.transaccion where id_factura='$numero_factura' and id_area=1 and num_tranf is not null";
$result_cons_tranf=sql($sql_cons_tranf)or die;
$num_tranf=$result_cons_tranf->fields['num_tranf'];

$sql_insert_tranf="insert into expediente.transaccion
               (id_transac,
  				id_expediente,
               	id_area,
  				fecha_mov,
  				estado,
  				comentario,
  				debito,
  				num_tranf,
  				fecha_inf,
  				total_pagar,
  				credito,	
  				id_factura,
  				usuario  				
  				)
             values
              ('$id_transac',
  				'$id_expediente',
  				'$id_area',
  				'$fecha_mov',
  				'$estado',
  				'$string_coment',
  				'$debito',
  				'$num_tranf',
  				'$fecha_mov',
  				'$monto_pago',
  				'$credito',
  				'$numero_factura',
  				'$usuario'  				
  				)";

sql($sql_insert_tranf, "Error al insertar el Expediente") or fin_pagina();



//fin de codigo para la insercion en el sistema de expediente
	    
	    
	    $db->CompleteTrans();
	}
	else $accion="ERROR!!: No se puede Guardar, ya se genero un ingreso con la FACTURA: $numero_factura";
	        
}//de if ($_POST['guardar']=="Guardar Ingreso")

if ($_POST['guardar']=="Guardar Egreso"){
	
	$cuie=$_POST['cuie'];
	$monto_egreso=$_POST['monto_egreso'];
	$fecha_egreso=Fecha_db($_POST['fecha_egreso']);
	$monto_egre_comp=$_POST['monto_egre_comp'];
	$fecha_egre_comp=Fecha_db($_POST['fecha_egre_comp']);
	$comentario=$_POST['comentario1'];
	$usuario=$_ses_user['name'];	
	$fecha=date("Y-m-d");	
	$numero_factura=$_POST['numero_factura'];
	$id_servicio1=$_POST['servicio1'];
	$id_inciso=$_POST['ins_nombre'];
		
	    $db->StartTrans();
		$q="select nextval('contabilidad.egreso_id_egreso_seq') as id_comprobante";
	    $id_comprobante=sql($q) or fin_pagina();
	    $id_comprobante=$id_comprobante->fields['id_comprobante'];	
	    $query="insert into contabilidad.egreso
	             (id_egreso,cuie,monto_egreso,fecha_egreso,comentario,usuario,fecha,id_servicio,id_inciso,monto_egre_comp,fecha_egre_comp)
	             values
	             ($id_comprobante,'$cuie','$monto_egreso','$fecha_egreso','$comentario','$usuario','$fecha','$id_servicio1','$id_inciso','$monto_egre_comp','$fecha_egre_comp')";	
	    sql($query, "Error al insertar el comprobante") or fin_pagina();	    
	    
	    //ingreso a la tabla de incentivos
	    
	    if ($id_inciso==1) { 
            
	    	$sql_ingreso="select id_ingreso from contabilidad.ingreso where numero_factura='$numero_factura'";
	    	$rs_sql_ingreso=sql($sql_ingreso) or die;
	    	$id_ingreso=$rs_sql_ingreso->fields['id_ingreso'];
	    	$id_i="select nextval('contabilidad.id_incentivo_seq') as id_incentivo";
	    	$rs_id_incentivo=sql($id_i) or die;
	    	$id_incentivo=$rs_id_incentivo->fields['id_incentivo'];
	    	
	    	//insercion de incentivo
	    	
	    	$query_fact="select * from facturacion.factura where id_factura='$numero_factura'";
	    	$query_exp="select * from expediente.transaccion where id_factura='$numero_factura'and estado='C'";
	    	$rs_query_fact= sql ($query_fact) or die;
	    	$rs_query_exp= sql ($query_exp) or die;
	    	$fecha_factura=$rs_query_exp->fields['fecha_mov'];
	    	$monto_prefactura=$rs_query_fact->fields['monto_prefactura'];
	    	
	    	$query_incentivo="insert into contabilidad.incentivo (id_incentivo,cuie,id_egreso,id_factura,fecha_prefactura,monto_factura,monto_incentivo,cumple,id_ingreso)
	    	values
	    	('$id_incentivo','$cuie',$id_comprobante,'$numero_factura','$fecha_egre_comp','$monto_prefactura','$monto_egre_comp',2,'$id_ingreso')";
	    	$rs_query_incetivo=sql($query_incentivo) or die;
	    	//echo ($query_incentivo);
	    	
	    }
	    
	    //fin ingreso de datos a la tabla de incentivos
	    
	    
	    
	    $accion="Se guardo el Ingreso Numero: $id_comprobante";
	    $db->CompleteTrans();   
	        
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

$sql="select * from nacer.efe_conv
	 where cuie='$cuie'";
$res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();

$nombre=$res_comprobante->fields['nombre'];
$domicilio=$res_comprobante->fields['domicilio'];
$ciudad=$res_comprobante->fields['cuidad'];

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos_ingresos()
{
  if(document.all.servicio.value=="-1"){
  alert('Debe Seleccionar un Servicio');
  return false;
  }
 if(document.all.numero_factura.value=="-1"){
  alert('Debe Vincular una Factura')
  return false;
 }
  if(document.all.expediente_externo.value==""){
  alert('Debe Ingresar un Expediente Externo');
  return false;
 }
 if(document.all.fecha_exp_ext.value==""){
  alert('Debe Ingresar una Fecha de Expediente Externo');
  return false;
 }
 if (confirm('Esta Seguro que Desea Agregar Ingreso?'))return true;
 else return false;	
}


 function control_nuevos_egresos()
{
 if(document.all.servicio1.value=="-1"){
  alert('Debe Seleccionar un Servicio');
  return false;
  }
  
  if(document.all.ins_nombre.value=="-1"){
  alert('Debe Seleccionar un Inciso');
  return false;
  }
	
 if(document.all.monto_egre_comp.value==""){
  alert('Debe Ingresar un monto egreso COMPROMETIDO');
  return false;
 }
  if(document.all.monto_egreso.value==""){
  alert('Debe Ingresar un monto egreso (0 si no hay monto)');
  return false;
 } 
 if (confirm('Esta Seguro que Desea Agregar Egreso?'))return true;
 else return false;	
}//de function control_nuevos()

var img_ext='<?=$img_ext='../../imagenes/rigth2.gif' ?>';//imagen extendido
var img_cont='<?=$img_cont='../../imagenes/down2.gif' ?>';//imagen contraido
function muestra_tabla(obj_tabla,nro){
 oimg=eval("document.all.imagen_"+nro);//objeto tipo IMG
 if (obj_tabla.style.display=='none'){
 	obj_tabla.style.display='inline';
    oimg.show=0;
    oimg.src=img_ext;
 }
 else{
 	obj_tabla.style.display='none';
    oimg.show=1;
	oimg.src=img_cont;
 }
}
function destrabar_ingreso(){
	document.all.monto_egre_comp.readOnly=false;
	document.all.monto_egre_comp.focus();
}
</script>

<form name='form1' action='ingre_egre_admin.php' method='POST' enctype='multipart/form-data'>
<input type="hidden" value="<?=$id_factura?>" name="id_factura">
<input type="hidden" value="<?=$ins_nombre?>" name="ins_nombre">

<?echo "<center><b><font size='+2' color='red'>$accion</font></b></center>";?>
<input type="hidden" name="cuie" value="<?=$cuie?>">
<?
$sql="select monto_egreso from contabilidad.egreso
		where cuie='$cuie'";
$res_egreso=sql($sql,"no puede calcular el saldo");

if ($res_egreso->recordCount()==0){
	$sql="select ingre as total, ingre,egre,deve,egre_comp from
		(select sum (monto_deposito)as ingre from contabilidad.ingreso
		where cuie='$cuie') as ingreso,
		(select sum (monto_egreso)as egre from contabilidad.egreso
		where cuie='$cuie') as egreso,
		(select sum (monto_factura)as deve from contabilidad.ingreso
		where cuie='$cuie') as devengado,
		(select sum (monto_egre_comp)as egre_comp from contabilidad.egreso
		where cuie='$cuie') as egre_comp";

}
else{
$sql="select ingre-egre as total, ingre,egre,deve,egre_comp from
		(select sum (monto_deposito)as ingre from contabilidad.ingreso
		where cuie='$cuie') as ingreso,
		(select sum (monto_egreso)as egre from contabilidad.egreso
		where cuie='$cuie') as egreso,
		(select sum (monto_factura)as deve from contabilidad.ingreso
		where cuie='$cuie') as devengado,
		(select sum (monto_egre_comp)as egre_comp from contabilidad.egreso
		where cuie='$cuie') as egre_comp";
}
$res_saldo=sql($sql,"no puede calcular el saldo")

?>
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <font size=+1><b>Ingreso / Egreso</b></font>    
    </td>
 </tr>
 <tr><td>
  <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripción del Efector</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         <tr>	           
           <td align="center" colspan="2">
            <b> CUIE: <font size="+1" color="Red"><?=$cuie?></font> </b>
           </td>
         </tr>
         <tr>
         	<td align="right">
         	  <b>Nombre:
         	</td>         	
            <td align='left'>
              <input type='text' name='nombre' value='<?=$nombre;?>' size=60 align='right' readonly></b>
            </td>
         </tr>
         <tr>
            <td align="right">
         	  <b> Domicilio:
         	</td>   
           <td  >
             <input type='text' name='domicilio' value='<?=$domicilio;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          <tr>
           <td align="right">
         	  <b> Ciudad:
         	</td> 
           <td >
             <input type='text' name='ciudad' value='<?=$ciudad;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          <tr>
			<td align="right"><b>Saldo:</b></td>
			<td align="left">		          			
				<b><font size="+1" color="Blue"><?=number_format($res_saldo->fields['total'],2,',','.')?></font></b>
			</td>
		  </tr>  
          <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr> 
         <tr>	           
           <td align="center" colspan="2">
           	<?$ref = encode_link("detalle_servicio.php",array("cuie"=>$cuie, "nombre"=>$nombre));
	   		  $onclick_elegir="window.open('$ref')";?>
             <input type="button" name="detalle_servicio" value="Detalle por Servicio" onclick="(<?=$onclick_elegir?>)" style="width=250px">
             <input type="submit" name="notificar" value="Notificar Via Mail" onclick="return confirm('Se Notificara por Mail Movimiento Bancarios del CAPS. ¿Esta Seguro?');" style="width=250px">
           </td>
         </tr>          
        </table>
      </td>      
     </tr>
   </table>     
	 <table class="bordes" align="center" width="70%">
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		Nuevo Ingreso
		 	</td>
		 </tr>
		 <tr><td class="bordes"><table>
			 <tr>
				 <td>
					 <tr>
					    <td align="right">
					    	<b>Número de Factura:</b>
					    </td>
					     <td align="left">		          			
				 			<select name=numero_factura Style="width=550px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer(); document.forms[0].submit();"
           					> 
	<?//codigo para la resttriccion - ANALIZAR!!!!!!!!!?>		     			
			     			<? if (!($numero_factura)) {
			                 ?><option value=-1 selected>Seleccione</option>
			                 <? $sql="select * from (
										select * from expediente.expediente left join facturacion.factura using (id_factura) where control=4 and factura.cuie='$cuie'
										order by id_factura DESC) as tabla left join facturacion.smiefectores using (cuie)";
                                                   
								
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$id_factura=$res_efectores->fields['id_factura'];
			                 	$nombreefector=$res_efectores->fields['nombreefector'];
			                 	$periodo_actual=$res_efectores->fields['periodo_actual'];
			                 	$periodo=$res_efectores->fields['periodo'];
			                 	$monto_prefactura=number_format($res_efectores->fields['monto_prefactura'],2,',','.');
			                 	$fecha_factura=fecha($res_efectores->fields['fecha_factura']);
			                 ?>
			                   <option value=<?=$id_factura;?><? if ($numero_factura==$id_factura) echo "selected"?>><?="N°:".$id_factura." - Periodo Prestaciones:".$periodo_actual." - Monto Cuasi:".$monto_prefactura?></option>
			                               
			                 <?
			                 $res_efectores->movenext();
			                 }
			     			}
			                 else {  $sql= "select * from facturacion.factura
									left join facturacion.smiefectores using (cuie)
									where id_factura=$numero_factura and factura.cuie='$cuie'";
			                 		
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$id_factura=$res_efectores->fields['id_factura'];
			                 	$nombreefector=$res_efectores->fields['nombreefector'];
			                 	$periodo_actual=$res_efectores->fields['periodo_actual'];
			                 	$periodo=$res_efectores->fields['periodo'];
			                 	$monto_prefactura=number_format($res_efectores->fields['monto_prefactura'],2,',','.');
			                 	$fecha_factura=fecha($res_efectores->fields['fecha_factura']);
			                 ?>
			                   <option value=<?=$id_factura;?>><?="N°:".$id_factura." - Periodo Prestaciones:".$periodo_actual." - Monto Cuasi:".$monto_prefactura?></option>
			                               
			                 <?
			                 $res_efectores->movenext();
			                 }
			                 }
			     			 ?>
			      			
			      			
			      			</select><font size="2" color="Red"></font>
			      		</td>
					    
					 </tr>
					 
					 <tr>
					    <td align="right">
					    	<b>Servicio:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=servicio Style="width=450px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer();"
           					>
			     			<option value=-1>Seleccione</option>
			                 <? if ($cuie=='D05035') {
			                 	$sql= "select * from facturacion.servicio where id_servicio=42 or id_servicio=1";
			                 	$res_efectores=sql($sql) or fin_pagina();
			                 	while (!$res_efectores->EOF){
								$id_servicio=$res_efectores->fields['id_servicio'];
			                 	$descripcion=$res_efectores->fields['descripcion'];?>
			                 	<option <?=($res_efectores->fields['descripcion']=="No Corresponde")?"selected":""?> value=<?=$id_servicio;?>><?=$descripcion?></option>
								<?
								$res_efectores->movenext();
								}
							 }
			                 else {
			     			     			
			                 $sql= "select * from facturacion.servicio order by descripcion";
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$id_servicio=$res_efectores->fields['id_servicio'];
			                 	$descripcion=$res_efectores->fields['descripcion'];
			                 ?>
			                   <option <?=($res_efectores->fields['descripcion']=="No Corresponde")?"selected":""?> value=<?=$id_servicio;?>><?=$descripcion?></option>
			                 <?
			                 $res_efectores->movenext();
			                 	}
			                 }?>
			      			</select>
					    </td>
					 </tr>
					 
					 <tr>
					 	<td align="right">
					    	<b>Fecha de la Prefactura:</b>
					    </td>
					    <td align="left">
					    						    	
					    	<?$fecha_prefactura=date("d/m/Y");?>
					    	 <input type=text id=fecha_prefactura name=fecha_prefactura value='<?=$fecha_prefactura;?>' size=15 readonly>
					    	 <?=link_calendario("fecha_prefactura");?>					    	 
					    </td>		    
					 </tr>
					
					 
					 
					 <tr>
					    <td align="right">
					    	 <b>Expediente Externo:</b>
					    </td>
                         
					    <td align="left"		          			
				 			onchange="borrar_buffer(); document.forms[0].submit()" >
				 			<?					   
					     if ($numero_factura) {
				 		 $sql_exp_ext="select num_tranf from expediente.transaccion where id_factura='$numero_factura' and num_tranf is not null";
					     $result_exp_ext=sql($sql_exp_ext) or die;
					     $num_tranf=$result_exp_ext->fields ['num_tranf'];} ?>
				 			<input type="text" name="expediente_externo" value='<?=$num_tranf;?>' size=18 align="right" readonly>
					    </td>
					 </tr>

					 <tr>
						<td align="right"><b>Fecha de Expediente:</b></td>
						<td align="left"
						onchange="borrar_buffer(); document.forms[0].submit()" >
				 			<?					   
					     if ($numero_factura) {
				 		 $sql_exp_ext="select fecha_mov from expediente.transaccion where id_factura=$numero_factura and num_tranf is not null";
					     $result_exp_ext=sql($sql_exp_ext) or die;
					     $fecha_exp_ext=$result_exp_ext->fields ['fecha_mov'];} ?>
							<input type=text id=fecha_exp_ext name=fecha_exp_ext value='<?=fecha($fecha_exp_ext);?>' size=18 readonly>
				        	 					    	 
						</td>		    
					</tr>
					 
					 <tr>
         				<td align="right">
         	  				<b>Comentario:</b>
         				</td>         	
            			<td align='left'>
              				<textarea cols='70' rows='3' name='comentario' ></textarea>
            			</td>
         			</tr>         			 					 
				  </td>
			 </tr>
		 </table></td></tr>	 
		 <tr>
		  	<td align="center" colspan="2" class="bordes">		      
		    	<input type="submit" name="guardar" value="Guardar Ingreso" title="Guardar Ingreso" Style="width=300px" onclick="return control_nuevos_ingresos()">
		    </td>
		 </tr> 
	 </table>	
 </td></tr>
 
<?//tabla de comprobantes
$query="SELECT 
  *
FROM
  contabilidad.ingreso  
  left join facturacion.factura on ingreso.numero_factura=factura.id_factura 
  left join facturacion.servicio using (id_servicio)
  where ingreso.cuie='$cuie' 
  order by id_ingreso DESC";
$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Ingresos" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
	  </td>
	  <td align="center">
	   <b>Ingresos</b>&nbsp; (Total Depositado: <?=number_format($res_saldo->fields['ingre'],2,',','.')?>
	   				  &nbsp; Total Devengado: <?=number_format($res_saldo->fields['deve'],2,',','.')?>)
	   				  <?$total_depositado=$res_saldo->fields['ingre'] //lo uso en ecuacion mas adelante?>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida" border="1" width="100%" style="display:none;border:thin groove">
	<?if ($res_comprobante->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen Ingresos para este Efector</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">		 	    
	 		<td width="5%">ID</td>
	 		<td width="15%">Numero Factura</td>
	 		<td width="15%">Monto Pre Factura</td>
	 		<td width="15%">Fecha Pre Factura</td>
	 		<td width="15%">Monto Factura</td>
	 		<td width="15%">Fecha Factura</td>
	 		<td width="15%">Monto Deposito</td>
	 		<td width="15%">Fecha Deposito</td>
	 		<td width="15%">Fecha Notificacion</td>
	 		<td width="15%">Comentario</td>
	 		<td width="10%">Usuario</td>
	 		<td width="10%">Fecha</td>
	 		<td width="10%">Servicio</td>
	 		<td width="10%">Editar Serv.</td>
	 		<td width="10%">Notificar</td>
	 		<!-- <td width="10%">Borrar</td> -->
	 	</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 	while (!$res_comprobante->EOF) {
	 		 $ref = encode_link("carga_deposito.php",array("id_ingreso"=>$res_comprobante->fields['id_ingreso'],"pagina"=>"ingre_egre_admin.php","cuie"=>$cuie,"numero_factura"=>$res_comprobante->fields['numero_factura']));             
             $onclick_elegir="location.href='$ref'"; 
             
             if (permisos_check('inicio','contabilidad_exepciones')){	
	             $ref1 = encode_link("ingre_egre_admin.php",array("id_ingreso"=>$res_comprobante->fields['id_ingreso'],"marcar1"=>"True","cuie"=>$cuie));
	             $id_ingreso=$res_comprobante->fields['id_ingreso'];
	             $onclick_eliminar="if (confirm('Esta Seguro que Desea Eliminar Ingreso $id_ingreso ?')) location.href='$ref1'
	            						else return false;	";
	 		}
             else $onclick_eliminar="alert ('Debe Tener Permisos Especiales para poder Eliminar.')";
			 
			 $saldo_real=$total_depositado-$res_saldo->fields['egre']-($res_saldo->fields['egre_comp']-$res_saldo->fields['egre']);
			 $ref_notificar = encode_link("ingre_egre_admin.php",array("id_factura_notificar"=>$res_comprobante->fields['numero_factura'],"notificar_mail"=>"True","cuie"=>$cuie,"saldo_real"=>$saldo_real));
	 		 $ref_editar = encode_link("detalle_ingreso_admin.php",array("numero_factura"=>$res_comprobante->fields['numero_factura'],"cuie"=>$cuie,"saldo_real"=>$saldo_real));
			 $id_factura_notificar=$res_comprobante->fields['numero_factura'];
			 $onclick_notificar="if (confirm('Esta Seguro que Desea Notificar la Factura $id_factura_notificar?')) location.href='$ref_notificar'
	            						else return false;	";
			 $onclick_editar="if (confirm('Esta Seguro que Desea Editar los datos de la Factura $id_factura_notificar?')) location.href='$ref_editar'
	            						else return false;	";
			?>
	 		<tr <?=atrib_tr()?>>	 			
		 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['id_ingreso']?></td>
		 		<td onclick="<?=$onclick_elegir?>" align="center"><b><?=number_format($res_comprobante->fields['numero_factura'],0,'','.')?></b></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=number_format($res_comprobante->fields['monto_prefactura'],2,',','.')?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['fecha_prefactura'])?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=number_format($res_comprobante->fields['monto_factura'],2,',','.')?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['fecha_factura'])?></td>
				<td onclick="<?=$onclick_elegir?>"><?=number_format($res_comprobante->fields['monto_deposito'],2,',','.')?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['fecha_deposito'])?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['fecha_notificacion'])?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['comentario']?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['usuario']?></td>		 		
		 		<td onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['fecha'])?></td>		 		
		 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['descripcion']?></td>		 		
		 		<!--<td onclick="<?=$onclick_eliminar?>" align="center"><img src='../../imagenes/salir.gif' style='cursor:hand;'></td>	-->	 		
		 		<td onclick="<?=$onclick_editar?>" align="center"><img src='../../imagenes/editar1.png' style='cursor:hand;'></td>
		 		<td onclick="<?=$onclick_notificar?>" align="center"><img src='../../imagenes/iconnote_resize.gif' style='cursor:hand;'></td>	 		
		 	</tr>	
		 	
	 		<?$res_comprobante->movenext();
	 	}
	 }?>
</table></td></tr>

<tr><td>
<table class="bordes" align="center" width="70%">
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		Nuevo Egreso
		 	</td>
		 </tr>
		 <tr><td class="bordes"><table>
			 <tr>
				 <td>				 
					 <tr>
					    <td align="right">
					    	<b>Rubro:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=ins_nombre Style="width=450px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer();document.forms[0].submit();"
           					>
			     			<? if ((!($ins_nombre)) or $ins_nombre==-1) {?>
			     				<option value=-1>Seleccione</option>
			                 <?
			                 $sql= "select * from contabilidad.inciso order by id_inciso";
			                 $res_efectores=sql($sql) or fin_pagina();
							 $sql_flag="select * from nacer.efe_conv where cuie='$cuie'";
							 $rs_sql_flag=sql ($sql_flag) or die;
							 $flag_incentivo_efector=$rs_sql_flag->fields['incentivo'];
			                 while (!$res_efectores->EOF){ 
			                 	$id_servicio=$res_efectores->fields['id_inciso'];
			                 	$descripcion=$res_efectores->fields['ins_nombre'];
			                 ?>
			                   <option value='<?=$id_servicio;?>'<?php if ($flag_incentivo_efector=='n'&& $id_servicio=='1')echo "disabled"?>><?=$descripcion?></option>
			                 <?
			                 $res_efectores->movenext();
			                 }
			                 }
			                 else {
			                 	$sql_inc= "select * from contabilidad.inciso where id_inciso='$ins_nombre'";
			                 	$rs_sql_inc=sql($sql_inc) or die;
			                 	$descripcion=$rs_sql_inc->fields['ins_nombre'];
			                    ?>
			                   <option value='<?=$ins_nombre;?>'><?=$descripcion?></option>	
			                <? }	                 
			                 ?>
			      			
			      			
			      			</select>
					    </td>
					 </tr>
					 
					 <tr>
					    <td align="right">
					    	<b>Servicio:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=servicio1 Style="width=450px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer();"
           					>
			     			<option value=-1>Seleccione</option>
			                 <? if ($cuie=='D05035') {
			                 	$sql= "select * from facturacion.servicio where id_servicio=42 or id_servicio=1";
			                 	$res_efectores=sql($sql) or fin_pagina();
								while (!$res_efectores->EOF){
			                 	$id_servicio=$res_efectores->fields['id_servicio'];
			                 	$descripcion=$res_efectores->fields['descripcion'];
								?>
								<option <?=($res_efectores->fields['descripcion']=="No Corresponde")?"selected":""?> value=<?=$id_servicio;?>><?=$descripcion?></option>
								<?
								$res_efectores->movenext();
								}
							}
			                 else {
			     			     			
			                 $sql= "select * from facturacion.servicio order by descripcion";
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$id_servicio=$res_efectores->fields['id_servicio'];
			                 	$descripcion=$res_efectores->fields['descripcion'];
			                 ?>
			                   <option <?=($res_efectores->fields['descripcion']=="No Corresponde")?"selected":""?> value=<?=$id_servicio;?>><?=$descripcion?></option>
			                 <?
			                 $res_efectores->movenext();
			                 	}
			                 }?>
			      			</select>
					    </td>
					 </tr>
					 
					 <tr>
					    <td align="right">
					    	<b>Monto del Egreso Comprometido:</b>
					    </td>
					    <td align="left">
					    <!--<onchange="borrar_buffer(); document.forms[0].submit()" >	-->	          			
				 			<?php 
				 			if ($ins_nombre==1) {
				 				$sql_inciso="select monto_prefactura from facturacion.factura where id_factura='$numero_factura'";
				 				$result_sql_inciso=sql ($sql_inciso) or die;
				 				//$monto_30=($result_sql_inciso->fields['monto_prefactura']*30)/100;
				 			?>
				 			
				 			<input type=text id=monto_egre_comp name=monto_egre_comp value='<?=($result_sql_inciso->fields['monto_prefactura']*30)/100;?>' size=30 align=right readonly>

					    <?}
					    else {?>
					    <input type="text" name="monto_egre_comp" value="" size=30 align="right"> <?}?>
					    
					    <input type=button name=destraba_ingreso value='d' onclick='destrabar_ingreso()'>

					    </td>
					    
					 </tr>
					 <tr>
					 	<td align="right">
					    	<b>Fecha del egreso Comprometido:</b>
					    </td>
					    <td align="left">
					    						    	
					    	<?$fecha_egre_comp=date("d/m/Y");?>
					    	 <input type=text id=fecha_egre_comp name=fecha_egre_comp value='<?=$fecha_egre_comp;?>' size=15 readonly>
					    	 <?=link_calendario("fecha_egre_comp");?>					    	 
					    </td>		    
					 </tr>
					 
					 
					 <tr>
					    <td align="right">
					    	<b>Monto del Egreso:</b>
					    </td>
					    <td align="left">		          			
				 			<input type="text" name="monto_egreso" value="" size=30 align="right">
					    </td>
					 </tr>
					 <tr>
					 	<td align="right">
					    	<b>Fecha del egreso:</b>
					    </td>
					    <td align="left">
					    						    	
					    	<?$fecha_egreso=date("d/m/Y");?>
					    	 <input type=text id=fecha_egreso name=fecha_egreso value='<?=$fecha_egreso;?>' size=15 readonly>
					    	 <?=link_calendario("fecha_egreso");?>					    	 
					    </td>		    
					 </tr>
					 		 
					 <tr>
         				<td align="right">
         	  				<b>Comentario:</b>
         				</td>         	
            			<td align='left'>
              				<textarea cols='70' rows='3' name='comentario1' ></textarea>
            			</td>
         			</tr>   					 
				  </td>
			 </tr>
		 </table></td></tr>	 
		 <tr>
		  	<td align="center" colspan="2" class="bordes">		      
		    	<input type="submit" name="guardar" value="Guardar Egreso" title="Guardar Ingreso" Style="width=300px" onclick="return control_nuevos_egresos()">
		    </td>
		 </tr> 
	 </table>	
 </td></tr>
 
<?//tabla de comprobantes
$query="SELECT 
  *
FROM
  contabilidad.egreso  
  left join facturacion.servicio using (id_servicio) 
  left join contabilidad.inciso using (id_inciso) 
  where cuie='$cuie' and monto_egre_comp <> 0
  order by id_egreso DESC";
$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar egresos" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida1,2);" >
	  </td>
	  <td align="center">
	   <b>Egresos</b>&nbsp; (Total de Egresos:<?=number_format($res_saldo->fields['egre'],2,',','.')?> <!--Comprometido:--><?//number_format($res_saldo->fields['egre_comp'],2,',','.')?>
	   Comprometido NO Pagado: <?=number_format($res_saldo->fields['egre_comp']-$res_saldo->fields['egre'],2,',','.')?>) // <font color=#F781F3>Saldo Real= <?=number_format($total_depositado-$res_saldo->fields['egre']-($res_saldo->fields['egre_comp']-$res_saldo->fields['egre']),2,',','.')?></font></b>
	   <?$saldo_real=$total_depositado-$res_saldo->fields['egre']-($res_saldo->fields['egre_comp']-$res_saldo->fields['egre'])?>
	   <input type="hidden" value="<?=$saldo_real?>" name="saldo_real">
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida1" border="1" width="100%" style="display:none;border:thin groove">
	<?if ($res_comprobante->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen Egresos para este Efector</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">		 	    
	 		<td width="5%">ID</td>
	 		<td width="15%">Rubro</td>
	 		<td width="15%">Monto Egre COMPROMETIDO</td>	 		
	 		<td width="15%">Fecha Egre COMPROMETIDO</td>
	 		<td width="15%">Monto Egre</td>	 		
	 		<td width="15%">Fecha Egre</td>
	 		<td width="15%">Comentario</td>
	 		<td width="15%">Fecha Deposito</td>
	 		<td width="10%">Usuario</td>
	 		<td width="10%">Fecha</td>
	 		<td width="10%">Servicio</td>
	 		<td width="10%">Editar serv.</td>
	 		<td width="10%">Modificado</td>
	 		<td width="10%">Fecha modif</td>
	 		<!--<td width="10%">Borrar</td>-->
	 	</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 	while (!$res_comprobante->EOF) {
	 		$ref = encode_link("modifica_egreso.php",array("id_egreso"=>$res_comprobante->fields['id_egreso'],"pagina"=>"ingre_egre_admin.php","cuie"=>$cuie,"monto_egreso"=>$res_comprobante->fields['monto_egreso'],"monto_egreso_comp"=>$res_comprobante->fields['monto_egre_comp']));             
            $onclick_elegir="location.href='$ref'";
            
            $ref_e = encode_link("detalle_egreso_admin.php",array("id_egreso"=>$res_comprobante->fields['id_egreso'],"cuie"=>$cuie));             
            $onclick_editar_e="location.href='$ref_e'";
	 		
            if (permisos_check('inicio','contabilidad_exepciones')){
		 		$ref1 = encode_link("ingre_egre_admin.php",array("id_egreso"=>$res_comprobante->fields['id_egreso'],"marcar2"=>"True","cuie"=>$cuie));
	            $id_egreso=$res_comprobante->fields['id_egreso'];
	            $onclick_eliminar="if (confirm('Esta Seguro que Desea Eliminar Egreso $id_egreso ?')) location.href='$ref1'
	            						else return false;	"; 		
	 		}	else $onclick_elegir1="alert ('Debe Tener Permisos Especiales para poder eliminar.')";
	 		
	            ?>
	        <? if ($res_comprobante->fields['id_inciso']==1) $tr=atrib_tr1();
	           else $tr=atrib_tr()
	        
	        ?>    
	           
	 		<tr <?=$tr?>>	 			
		 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['id_egreso']?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['ins_nombre']?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=number_format($res_comprobante->fields['monto_egre_comp'],2,',','.')?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['fecha_egre_comp'])?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=number_format($res_comprobante->fields['monto_egreso'],2,',','.')?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['fecha_egreso'])?></td>		 		
		 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['comentario']?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['fecha_deposito'])?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['usuario']?></td>	
		 		<td onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['fecha'])?></td>		 
		 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['descripcion']?></td>
		 		<td onclick="<?=$onclick_editar_e?>" align="center"><img src='../../imagenes/editar1.png' style='cursor:hand;'></td>	
		 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['usuario_mod']?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['fecha_mod'])?></td>	 
		 		<!--<td onclick="<?=$onclick_eliminar?>" align="center"><img src='../../imagenes/salir.gif' style='cursor:hand;'></td>-->	 					 		
		 	</tr>	
		 	
	 		<?$res_comprobante->movenext();
	 	}
	 }?>
</table></td></tr>


 
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='ing_egre_listado.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
</table>

</form>
<?=fin_pagina();// aca termino ?>
