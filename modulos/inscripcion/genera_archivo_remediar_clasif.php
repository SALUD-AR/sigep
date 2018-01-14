<?php

require_once("../../config.php");
$periodo = substr($fechaemp, 0, 4);
$elperiodo = intval(substr($fechaemp, 5, 2));
if ($elperiodo >= 9) {
    $elperiodo = '12';
} elseif ($elperiodo >= 5) {
    $elperiodo = '08';
} else {
    $elperiodo = '04';
}
$periodo.=$elperiodo;
$fecha_actual = date("Y-m-d");

//Vieja consulta para generar el C. En el futuro obsoleta

$sql_tmp1 = "SELECT distinct beneficiarios.estado_envio,beneficiarios.clave_beneficiario,beneficiarios.apellido_benef
,beneficiarios.nombre_benef,beneficiarios.clase_documento_benef,beneficiarios.tipo_documento,beneficiarios.numero_doc,beneficiarios.sexo
,beneficiarios.fecha_nacimiento_benef,beneficiarios.provincia_nac,beneficiarios.localidad_nac,beneficiarios.pais_nac
,beneficiarios.cuie_ea,beneficiarios.cuie_ah,beneficiarios.calle
,beneficiarios.numero_calle
,beneficiarios.piso,beneficiarios.dpto,beneficiarios.manzana
,beneficiarios.entre_calle_1,beneficiarios.entre_calle_2,beneficiarios.telefono
,beneficiarios.departamento
,beneficiarios.localidad
,beneficiarios.municipio
,beneficiarios.cod_pos
,beneficiarios.barrio
,beneficiarios.activo,beneficiarios.score_riesgo,beneficiarios.mail,beneficiarios.celular,beneficiarios.otrotel
,beneficiarios.tipo_ficha,beneficiarios.responsable,beneficiarios.discv,beneficiarios.disca,beneficiarios.discmo,beneficiarios.discme,beneficiarios.otradisc
,beneficiarios.apellido_benef_otro,beneficiarios.nombre_benef_otro,beneficiarios.fecha_verificado,beneficiarios.usuario_verificado,beneficiarios.apellidoagente
,beneficiarios.nombreagente,beneficiarios.centro_inscriptor,beneficiarios.dni_agente,smiefectores.nombreefectores,relacioncodigos.codremediar


,clasificacion_remediar.id_clasificacion,clasificacion_remediar.nro_clasificacion,clasificacion_remediar.fecha_control ,clasificacion_remediar.fecha_carga
,case clasificacion_remediar.dbt when '0' then '' end as dbt,clasificacion_remediar.usuario
,case clasificacion_remediar.hta when '1' then 'S' else 'N' end as hta
,case clasificacion_remediar.tabaquismo when '1' then 'S' else 'N' end as tabaquismo
,clasificacion_remediar.dislipemia
,case clasificacion_remediar.obesidad when '1' then 'S' else 'N' end as obesidad
,clasificacion_remediar.rcvg,clasificacion_remediar.ta_sist,clasificacion_remediar.ta_diast,clasificacion_remediar.col_tot,clasificacion_remediar.hdl
,clasificacion_remediar.ldl,clasificacion_remediar.tagss,clasificacion_remediar.gluc,clasificacion_remediar.hba1,clasificacion_remediar.enalapril_mg
,clasificacion_remediar.furosemida_mg,clasificacion_remediar.glibenclam_mg,clasificacion_remediar.simvastat_mg,clasificacion_remediar.atenolol_mg
,clasificacion_remediar.hidroclorot_mg,clasificacion_remediar.metformina_mg,clasificacion_remediar.ass_mg
,clasificacion_remediar.insulina
,clasificacion_remediar.otras_drogas,clasificacion_remediar.otras_drogas_mg,clasificacion_remediar.otras_drogas2,clasificacion_remediar.otras_drogas2_mg

			FROM uad.beneficiarios
			left join facturacion.smiefectores on beneficiarios.cuie_ea=smiefectores.cuie
			inner join general.relacioncodigos on beneficiarios.cuie_ea=relacioncodigos.cuie
			left join uad.remediar_x_beneficiario on remediar_x_beneficiario.clavebeneficiario=beneficiarios.clave_beneficiario
			inner join trazadoras.clasificacion_remediar on remediar_x_beneficiario.clavebeneficiario=clasificacion_remediar.clave";

$sql_tmp1.=" WHERE beneficiarios.id_beneficiarios in (select id_beneficiarios from remediar.listado_enviados)";

//$sql_tmp1.=" AND length(beneficiarios.numero_doc) in (7,8)";
//$sql_tmp1.=" 			AND NOT EXISTS(SELECT tipo_doc, documento FROM puco.puco WHERE tipo_doc = tipo_documento AND documento = CAST(numero_doc AS INT)) ";
$sql_tmp1.=" 			AND clasificacion_remediar.fecha_control <= '$fechaemp' ";
$sql_tmp1.=" 			AND clasificacion_remediar.fecha_carga <= '$fechakrga' ";

$sql_tmp1.=" 			AND '$fecha_actual' - DATE(beneficiarios.fecha_nacimiento_benef)>= 2190 ";
$sql_tmp1.=" 			AND relacioncodigos.codremediar IS NOT NULL ";
$sql_tmp1.=" 			AND trim(relacioncodigos.codremediar) <>''";
$sql_tmp1.=" 			AND clasificacion_remediar.fecha_control NOTNULL ";
$sql_tmp1.=" 			AND clasificacion_remediar.enviado ilike '%n%'";
$sql_tmp1.=" 			AND beneficiarios.fallecido ='n' ";
// ya no se toma en cuenta la diferencia de fecha entre el control y el empadronamiento
//$sql_tmp1.=" 			AND DATE(clasificacion_remediar.fecha_control) - DATE(remediar_x_beneficiario.fechaempadronamiento) <= 240";
//a partir de enero 2012 se consideran todos los niveles de rcvg (Valeria - R+R)
//$sql_tmp2.=" 			AND upper(clasificacion_remediar.rcvg) in ('MODE','ALTO')";
/**/
  $sql_tmp1.=" 			AND clasificacion_remediar.ta_sist > 0";
  $sql_tmp1.=" 			AND clasificacion_remediar.ta_diast > 0";
  $sql_tmp1.=" 			AND clasificacion_remediar.col_tot > 0"; 
//$sql_tmp2.=" 			AND clasificacion_remediar.gluc > 0";		

$sql_tmp1.=" GROUP BY beneficiarios.estado_envio,beneficiarios.clave_beneficiario,beneficiarios.apellido_benef
,beneficiarios.nombre_benef,beneficiarios.clase_documento_benef,beneficiarios.tipo_documento,beneficiarios.numero_doc,beneficiarios.sexo
,beneficiarios.fecha_nacimiento_benef,beneficiarios.provincia_nac,beneficiarios.localidad_nac,beneficiarios.pais_nac
,beneficiarios.cuie_ea,beneficiarios.cuie_ah,beneficiarios.calle
,beneficiarios.numero_calle
,beneficiarios.piso,beneficiarios.dpto,beneficiarios.manzana
,beneficiarios.entre_calle_1,beneficiarios.entre_calle_2,beneficiarios.telefono
,beneficiarios.departamento
,beneficiarios.localidad
,beneficiarios.municipio
,beneficiarios.cod_pos
,beneficiarios.barrio
,beneficiarios.activo,beneficiarios.score_riesgo,beneficiarios.mail,beneficiarios.celular,beneficiarios.otrotel
,beneficiarios.tipo_ficha,beneficiarios.responsable,beneficiarios.discv,beneficiarios.disca,beneficiarios.discmo,beneficiarios.discme,beneficiarios.otradisc
,beneficiarios.apellido_benef_otro,beneficiarios.nombre_benef_otro,beneficiarios.fecha_verificado,beneficiarios.usuario_verificado,beneficiarios.apellidoagente
,beneficiarios.nombreagente,beneficiarios.centro_inscriptor,beneficiarios.dni_agente,smiefectores.nombreefectores,relacioncodigos.codremediar


,clasificacion_remediar.id_clasificacion,clasificacion_remediar.nro_clasificacion,clasificacion_remediar.fecha_control ,clasificacion_remediar.fecha_carga
,clasificacion_remediar.dbt,clasificacion_remediar.usuario
,clasificacion_remediar.hta
,clasificacion_remediar.tabaquismo
,clasificacion_remediar.dislipemia,clasificacion_remediar.obesidad
,clasificacion_remediar.rcvg
,clasificacion_remediar.ta_sist,clasificacion_remediar.ta_diast,clasificacion_remediar.col_tot,clasificacion_remediar.hdl
,clasificacion_remediar.ldl,clasificacion_remediar.tagss,clasificacion_remediar.gluc,clasificacion_remediar.hba1,clasificacion_remediar.enalapril_mg
,clasificacion_remediar.furosemida_mg,clasificacion_remediar.glibenclam_mg,clasificacion_remediar.simvastat_mg,clasificacion_remediar.atenolol_mg
,clasificacion_remediar.hidroclorot_mg,clasificacion_remediar.metformina_mg,clasificacion_remediar.ass_mg
,clasificacion_remediar.insulina
,clasificacion_remediar.otras_drogas,clasificacion_remediar.otras_drogas_mg,clasificacion_remediar.otras_drogas2,clasificacion_remediar.otras_drogas2_mg";

//echo  '<br>'.$sql_tmp2.'<br>' ;

$result12 = sql($sql_tmp1) or die;

$result12->movefirst();
$user2 = $result12->fields['usuario'];
$id_user2 = $result12->fields['usuario'];
if (!$result12->EOF) {
    $head = true;
    $resultP2 = sql("select * from uad.parametros") or die;
    $resultP2->movefirst();
    $cod_uad2 = $resultP2->fields['codigo_uad'];
    $cod_prov2 = $resultP2->fields['codigo_provincia'];

    /*     * ******
      /HEADER
     */
    $contenido2.='"H"';
    $contenido2.=';';
    $contenido2.='';
    $contenido2.=$fecha_actual;     // 10                                                       HEADER N�2
    //$contenido2.=';';
    //$contenido2.=$result12->fields['id_localidad'];
    $contenido2.=';';
    //$contenido2.=$cod_uad;
    $contenido2.=$id_user2;             //10                                                    HEADER N�3
    $contenido2.=';';

    if (!$resultP2->EOF) {
        $contenido2.=$cod_prov2; //  2	Dos Primeras Letras? O el Id?  -- el id parece          HEADER N�4
        $contenido2.=';';
        $contenido2.=$periodo;  //  6                                                           HEADER N�5


        $contenido2.=';';
        $contenido2.='
';

        //genero nombre de archivo
        $filename_remediar = 'C' . $cod_prov2 . $periodo . '.txt';
    }// fin gen archivo, sigo con la cadenas

    $where2 = '';
    //$where2_2='';

    while (!$result12->EOF) {
        $where2.=',';
        //$where2_2.=',';
        //
        //DATOS
        $contenido2.='"D"'; //Datos n1
        $contenido2.=';';

        $where2.=$result12->fields['id_clasificacion'];
        $contenido2.='"' . $result12->fields['tipo_documento'] . '"'; //3	Sigla (DNI, CUIL, etc)  DATOS N2
        $contenido2.=';';
        $contenido2.=$result12->fields['numero_doc']; //12  DATOS N3
        $contenido2.=';';
        $contenido2.='"' . $result12->fields['sexo'] . '"'; //1	M / F                                   DATOS N4
        $contenido2.=';';
        $contenido2.=$result12->fields['fecha_nacimiento_benef']; //10	AAAA-MM-DD (A o, Mes, D a)      DATOS N5
        $contenido2.=';';
        $contenido2.=substr($result12->fields['fecha_control'], 0, 10); // fecha clasificacion          DATOS N6
        $contenido2.=';';
        $contenido2.='"' . $result12->fields['calle'] . '"'; //40                                                           DATOS N7
        $contenido2.=';';
        $contenido2.='"' . $result12->fields['numero_calle'] . '"'; //5                                 DATOS N8
        $contenido2.=';';
        $contenido2.='"' . $result12->fields['manzana'] . '"'; //5                                      DATOS N9
        $contenido2.=';';
        $contenido2.=$result12->fields['piso']; //5                                                     DATOS N10
        $contenido2.=';';
        $contenido2.='"' . $result12->fields['dpto'] . '"'; //5                                         DATOS N11
        $contenido2.=';';
        $contenido2.='"' . $result12->fields['entre_calle_1'] . '"'; //40                               DATOS N12
        $contenido2.=';';
        $contenido2.='"' . $result12->fields['entre_calle_2'] . '"'; //40                               DATOS N13
        $contenido2.=';';
        $contenido2.='"' . str_replace('-1', '', $result12->fields['barrio']) . '"'; //40               DATOS N14
        $contenido2.=';';
        $contenido2.='"' . str_replace('-1', '', $result12->fields['municipio']) . '"'; //40            DATOS N15
        $contenido2.=';';
        $contenido2.='"' . str_replace('-1', '', $result12->fields['departamento']) . '"'; //40         DATOS N16
        $contenido2.=';';
        $contenido2.='"' . str_replace('-1', '', $result12->fields['localidad']) . '"'; //40            DATOS N17
        $contenido2.=';';
        $contenido2.=$result12->fields['cod_pos']; // 8 DomCodigoPostal                                 DATOS N18
        $contenido2.=';';
        $contenido2.=$cod_prov2; // 2 $result12->fields['provincia_nac'];                               DATOS N19
        $contenido2.=';';
        $contenido2.='"' . $result12->fields['telefono'] . '"'; //20                                    DATOS N20
        $contenido2.=';';
        $contenido2.=substr($result12->fields['fecha_carga'], 0, 10); // 10 FechaCarga                  DATOS N21
        $contenido2.=';';
        $contenido2.='"' . $result12->fields['codremediar'] . '"'; // 15 Efector                        DATOS N22
        $contenido2.=';';

        $contenido2.='"N"';    //  clasif1    1  DATOS N23
        $contenido2.=';';
        $contenido2.='"N"';    //  clasif2   1   DATOS N24
        $contenido2.=';';
        $contenido2.='"N"';    //  clasif3    1   DATOS N25
        $contenido2.=';';
        $contenido2.='"N"';    //  clasif4    1   DATOS N26
        $contenido2.=';';
        $contenido2.='"N"';    //  clasif5    1   DATOS N27
        $contenido2.=';';
        $contenido2.='"N"';    //  clasif6    1   DATOS N28
        $contenido2.=';';
        $contenido2.='"N"';    //  clasif7    1   DATOS N29
        $contenido2.=';';
        $contenido2.='"N"';    //  clasif8    1   DATOS N30
        $contenido2.=';';
        $contenido2.='"N"';    //  clasif9    1   DATOS N31
        $contenido2.=';';
        $contenido2.='"N"';    //  clasif10    1   DATOS N32
        $contenido2.=';';
        $contenido2.='"N"';    //  clasif11    1   DATOS N33
        $contenido2.=';';
        $contenido2.='"' . $result12->fields['dmt'] . '"';   // 1       DMT    DATOS N34
        $contenido2.=';';
        $contenido2.='"' . $result12->fields['hta'] . '"';  //  1       HTA    DATOS N35
        $contenido2.=';';
        $contenido2.='"' . $result12->fields['tabaquismo']. '"';          // 3       DATOS N36
        $contenido2.=';';
        $contenido2.=$result12->fields['ta_sist'];          // 3       ta_sist
        $contenido2.=';';
        $contenido2.=$result12->fields['ta_diast'];         // 3      ta_diast
        $contenido2.=';';
        $contenido2.=$result12->fields['col_tot'];          // 3       col_tot
        $contenido2.=';';
        $contenido2.='"N"';    //  1   factor1
        $contenido2.=';';
        $contenido2.='"N"';    //  1   factor2
        $contenido2.=';';
        $contenido2.='"' . $result12->fields['obesidad'] . '"'; //  1   factor3
        $contenido2.=';';
        $contenido2.='"N"';    //  1   factor4
        $contenido2.=';';
        $contenido2.='"N"';    //  1   factor5
        $contenido2.=';';
        $contenido2.='"N"';    //  1   factor6
        $contenido2.=';';
        $contenido2.='"N"';    //  1   factor7
        $contenido2.=';';
        $contenido2.='"N"';    //  1   factor8
        $contenido2.=';';

        //TODO toma el score riesgo de uad.beneficiarios que tiene como valores muy diferentes (3,0,14,9,1,2,7 hasta 15)

        $contenido2.='"' . $result12->fields['rcvg'] . '"'; //  rcvg
        $contenido2.=';';

        $contenido2.="
";
        $result12->MoveNext();
    }

    $cantidad_registros2 = $result12->numRows();
}
//Nueva consulta para generar el C. En el futuro solamente esta queda    

$sql_tmp2 = "SELECT distinct beneficiarios.estado_envio,beneficiarios.clave_beneficiario,beneficiarios.apellido_benef
,beneficiarios.nombre_benef,beneficiarios.clase_documento_benef,beneficiarios.tipo_documento,beneficiarios.numero_doc,beneficiarios.sexo
,beneficiarios.fecha_nacimiento_benef,beneficiarios.provincia_nac,beneficiarios.localidad_nac,beneficiarios.pais_nac
,beneficiarios.cuie_ea,beneficiarios.cuie_ah,beneficiarios.calle
,beneficiarios.numero_calle
,beneficiarios.piso,beneficiarios.dpto,beneficiarios.manzana
,beneficiarios.entre_calle_1,beneficiarios.entre_calle_2,beneficiarios.telefono
,beneficiarios.departamento
,beneficiarios.localidad
,beneficiarios.municipio
,beneficiarios.cod_pos
,beneficiarios.barrio
,beneficiarios.activo,beneficiarios.score_riesgo,beneficiarios.mail,beneficiarios.celular,beneficiarios.otrotel
,beneficiarios.tipo_ficha,beneficiarios.responsable,beneficiarios.discv,beneficiarios.disca,beneficiarios.discmo,beneficiarios.discme,beneficiarios.otradisc
,beneficiarios.apellido_benef_otro,beneficiarios.nombre_benef_otro,beneficiarios.fecha_verificado,beneficiarios.usuario_verificado,beneficiarios.apellidoagente
,beneficiarios.nombreagente,beneficiarios.centro_inscriptor,beneficiarios.dni_agente,smiefectores.nombreefector,relacioncodigos.codremediar


,clasificacion_remediar2.id_clasificacion,clasificacion_remediar2.nro_clasificacion,clasificacion_remediar2.fecha_control ,clasificacion_remediar2.fecha_carga
,case clasificacion_remediar2.dmt when '0' then '' end as dmt

,clasificacion_remediar2.usuario
,case clasificacion_remediar2.hta when '1' then 'S' else 'N' end as hta
,case clasificacion_remediar2.tabaquismo when '1' then 'S' else 'N' end as tabaquismo
,clasificacion_remediar2.rcvg

,clasificacion_remediar2.ta_sist
,clasificacion_remediar2.ta_diast
,clasificacion_remediar2.col_tot
,case clasificacion_remediar2.acv when '1' then 'S' else 'N' end as acv
,case clasificacion_remediar2.vas_per when '1' then 'S' else 'N' end as vas_per
,case clasificacion_remediar2.car_isq when '1' then 'S' else 'N' end as car_isq
,case clasificacion_remediar2.col310 when '1' then 'S' else 'N' end as col310
,case clasificacion_remediar2.col_ldl when '1' then 'S' else 'N' end as col_ldl
,case clasificacion_remediar2.ct_hdl when '1' then 'S' else 'N' end as ct_hdl
,case clasificacion_remediar2.pres_art when '1' then 'S' else 'N' end as pres_art
,case clasificacion_remediar2.dmt2 when '1' then 'S' else 'N' end as dmt2
,case clasificacion_remediar2.insu_renal when '1' then 'S' else 'N' end as insu_renal
,case clasificacion_remediar2.dmt_menor when '1' then 'S' else 'N' end as dmt_menor
,case clasificacion_remediar2.hta_menor when '1' then 'S' else 'N' end as hta_menor



,case clasificacion_remediar2.menopausia when '1' then 'S' else 'N' end as menopausia
,case clasificacion_remediar2.antihiper when '1' then 'S' else 'N' end as antihiper
,case clasificacion_remediar2.obesi when '1' then 'S' else 'N' end as obesi
,case clasificacion_remediar2.acv_prema when '1' then 'S' else 'N' end as acv_prema
,case clasificacion_remediar2.trigli when '1' then 'S' else 'N' end as trigli
,case clasificacion_remediar2.hdl_col when '1' then 'S' else 'N' end as hdl_col
,case clasificacion_remediar2.hiperglu when '1' then 'S' else 'N' end as hiperglu
,case clasificacion_remediar2.microalbu when '1' then 'S' else 'N' end as microalbu

			FROM uad.beneficiarios
			left join facturacion.smiefectores on beneficiarios.cuie_ea=smiefectores.cuie
			inner join general.relacioncodigos on beneficiarios.cuie_ea=relacioncodigos.cuie
			left join uad.remediar_x_beneficiario on remediar_x_beneficiario.clavebeneficiario=beneficiarios.clave_beneficiario
			inner join trazadoras.clasificacion_remediar2 on remediar_x_beneficiario.clavebeneficiario=clasificacion_remediar2.clave_beneficiario";

$sql_tmp2.=" WHERE beneficiarios.id_beneficiarios in (select id_beneficiarios from remediar.listado_enviados)";
//saacar
//$sql_tmp2.=" AND length(beneficiarios.numero_doc) in (7,8)";
//$sql_tmp2.=" 			AND NOT EXISTS(SELECT tipo_doc, documento FROM puco.puco WHERE tipo_doc = tipo_documento AND documento = CAST(numero_doc AS INT)) ";
$sql_tmp2.=" 			AND clasificacion_remediar2.fecha_control <= '$fechaemp' ";
$sql_tmp2.=" 			AND clasificacion_remediar2.fecha_carga <= '$fechakrga' ";

$sql_tmp2.=" 			AND '$fecha_actual' - DATE(beneficiarios.fecha_nacimiento_benef)>= 2190 ";
$sql_tmp2.=" 			AND relacioncodigos.codremediar IS NOT NULL ";
$sql_tmp2.=" 			AND trim(relacioncodigos.codremediar) <>''";
$sql_tmp2.=" 			AND clasificacion_remediar2.fecha_control NOTNULL ";
$sql_tmp2.=" 			AND clasificacion_remediar2.enviado ilike '%n%'";
$sql_tmp2.=" 			AND beneficiarios.fallecido ='n' ";

// ya no se toma en cuenta la diferencia de fecha entre el control y el empadronamiento
//$sql_tmp2.=" 			AND DATE(clasificacion_remediar2.fecha_control) - DATE(remediar_x_beneficiario.fechaempadronamiento) <= 240";
//a partir de enero 2012 se consideran todos los niveles de rcvg (Valeria - R+R)
/* $sql_tmp2.=" 			AND upper(clasificacion_remediar2.rcvg) in ('MODE','ALTO')";*/	
  $sql_tmp2.=" 			AND clasificacion_remediar2.ta_sist > 0";
  $sql_tmp2.=" 			AND clasificacion_remediar2.ta_diast > 0";
  $sql_tmp2.=" 			AND clasificacion_remediar2.col_tot > 0"; 
//$sql_tmp2.=" 			AND clasificacion_remediar.gluc > 0";		

$sql_tmp2.=" GROUP BY beneficiarios.estado_envio,beneficiarios.clave_beneficiario,beneficiarios.apellido_benef
,beneficiarios.nombre_benef,beneficiarios.clase_documento_benef,beneficiarios.tipo_documento,beneficiarios.numero_doc,beneficiarios.sexo
,beneficiarios.fecha_nacimiento_benef,beneficiarios.provincia_nac,beneficiarios.localidad_nac,beneficiarios.pais_nac
,beneficiarios.cuie_ea,beneficiarios.cuie_ah,beneficiarios.calle
,beneficiarios.numero_calle
,beneficiarios.piso,beneficiarios.dpto,beneficiarios.manzana
,beneficiarios.entre_calle_1,beneficiarios.entre_calle_2,beneficiarios.telefono
,beneficiarios.departamento
,beneficiarios.localidad 
,beneficiarios.municipio
,beneficiarios.cod_pos
,beneficiarios.barrio
,beneficiarios.activo,beneficiarios.score_riesgo,beneficiarios.mail,beneficiarios.celular,beneficiarios.otrotel
,beneficiarios.tipo_ficha,beneficiarios.responsable,beneficiarios.discv,beneficiarios.disca,beneficiarios.discmo,beneficiarios.discme,beneficiarios.otradisc
,beneficiarios.apellido_benef_otro,beneficiarios.nombre_benef_otro,beneficiarios.fecha_verificado,beneficiarios.usuario_verificado,beneficiarios.apellidoagente
,beneficiarios.nombreagente,beneficiarios.centro_inscriptor,beneficiarios.dni_agente,smiefectores.nombreefectores,relacioncodigos.codremediar


,clasificacion_remediar2.id_clasificacion,clasificacion_remediar2.nro_clasificacion,clasificacion_remediar2.fecha_control ,clasificacion_remediar2.fecha_carga
,clasificacion_remediar2.dmt,clasificacion_remediar2.usuario
,clasificacion_remediar2.hta,clasificacion_remediar2.tabaquismo
,clasificacion_remediar2.obesi
,clasificacion_remediar2.rcvg,clasificacion_remediar2.ta_sist,clasificacion_remediar2.ta_diast,clasificacion_remediar2.col_tot,clasificacion_remediar2.acv
,clasificacion_remediar2.vas_per,clasificacion_remediar2.car_isq,clasificacion_remediar2.col310,clasificacion_remediar2.col_ldl,clasificacion_remediar2.ct_hdl
,clasificacion_remediar2.pres_art,clasificacion_remediar2.dmt2,clasificacion_remediar2.insu_renal,clasificacion_remediar2.dmt_menor
,clasificacion_remediar2.hta_menor,clasificacion_remediar2.menopausia,clasificacion_remediar2.antihiper
,clasificacion_remediar2.acv_prema,clasificacion_remediar2.trigli,clasificacion_remediar2.hdl_col,clasificacion_remediar2.hiperglu,clasificacion_remediar2.microalbu";

//echo  '<br>'.$sql_tmp2.'<br>' ;

$result13 = sql($sql_tmp2) or die;

$result13->movefirst();
if (!$result13->EOF) {
    $resultP2 = sql("select * from uad.parametros") or die;
    $resultP2->movefirst();
    $cod_uad2 = $resultP2->fields['codigo_uad'];
    $cod_prov2 = $resultP2->fields['codigo_provincia'];

    if (!$head) {
        /*         * ******
          /HEADER
         */
        $contenido2.='"H"';
        $contenido2.=';';
        $contenido2.='';
        $contenido2.=$fecha_actual;     // 10                                                       HEADER N�2
        //$contenido2.=';';
        //$contenido2.=$result12->fields['id_localidad'];
        $contenido2.=';';
        //$contenido2.=$cod_uad;
        $id_user2 = $result13->fields['usuario'];
        $contenido2.=$id_user2;             //10                                                    HEADER N�3
        $contenido2.=';';

        $head = true;
        $contenido2.=$cod_prov2; //  2	Dos Primeras Letras? O el Id?  -- el id parece          HEADER N�4
        $contenido2.=';';
        $contenido2.=$periodo;  //  6                                                           HEADER N�5


        $contenido2.=';';
        $contenido2.='
';
        //genero nombre de archivo
        $filename_remediar = 'C' . $cod_prov2 . $periodo . '.txt';
    }

    $where3 = '';
    //$where2_2='';

    while (!$result13->EOF) {
        $where3.=',';
        //$where2_2.=',';
        //
        //DATOS
        $contenido2.='"D"';
        $contenido2.=';';

        $where3.=$result13->fields['id_clasificacion'];
        $contenido2.='"' . $result13->fields['tipo_documento'] . '"'; //3	Sigla (DNI, CUIL, etc)  DATOS N�2
        $contenido2.=';';
        $contenido2.=$result13->fields['numero_doc']; //12  DATOS N�3
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['sexo'] . '"'; //1	M / F                                   DATOS N�4
        $contenido2.=';';
        $contenido2.=$result13->fields['fecha_nacimiento_benef']; //10	AAAA-MM-DD (A o, Mes, D a)      DATOS N�5
        $contenido2.=';';
        $contenido2.=substr($result13->fields['fecha_control'], 0, 10); // fecha clasificacion          DATOS N�6
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['calle'] . '"'; //40                                                           DATOS N�7
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['numero_calle'] . '"'; //5                                 DATOS N�8
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['manzana'] . '"'; //5                                      DATOS N�9
        $contenido2.=';';
        $contenido2.=$result13->fields['piso']; //5                                                     DATOS N�10
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['dpto'] . '"'; //5                                         DATOS N�11
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['entre_calle_1'] . '"'; //40                               DATOS N�12
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['entre_calle_2'] . '"'; //40                               DATOS N�13
        $contenido2.=';';

        $contenido2.='"' . $result13->fields['barrio'] . '"'; //40               DATOS N�14
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['municipio'] . '"'; //40            DATOS N�15
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['departamento'] . '"'; //40         DATOS N�16
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['localidad'] . '"'; //40            DATOS N�17
        $contenido2.=';';
        $contenido2.=$result13->fields['cod_pos']; // 8 DomCodigoPostal                                 DATOS N�18
        $contenido2.=';';
        $contenido2.=$cod_prov2; // 2 $result12->fields['provincia_nac'];                               DATOS N�19
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['telefono'] . '"'; //20                                    DATOS N�20
        $contenido2.=';';
        $contenido2.=substr($result13->fields['fecha_carga'], 0, 10); // 10 FechaCarga                  DATOS N�21
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['codremediar'] . '"'; // 15 Efector                        DATOS N�22
        $contenido2.=';';



        $contenido2.='"' . $result13->fields['acv'] . '"';    //  clasif1    1
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['vas_per'] . '"';    //  clasif2   1
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['car_isq'] . '"';    //  clasif3    1
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['col310'] . '"';    //  clasif4    1
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['col_ldl'] . '"';    //  clasif5    1
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['ct_hdl'] . '"';    //  clasif6    1
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['pres_art'] . '"';    //  clasif7    1
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['dmt2'] . '"';    //  clasif8    1
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['insu_renal'] . '"';    //  clasif9    1
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['dmt_menor'] . '"';    //  clasif10    1
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['hta_menor'] . '"';    //  clasif11    1
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['dmt'] . '"';   // 1       DMT
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['hta'] . '"';  //  1       HTA
        $contenido2.=';';
        $contenido2.='"' .$result13->fields['tabaquismo']. '"';          // 3       DATOS N36
        $contenido2.=';';
        $contenido2.=$result13->fields['ta_sist'];          // 3       ta_sist
        $contenido2.=';';
        $contenido2.=$result13->fields['ta_diast'];         // 3      ta_diast
        $contenido2.=';';
        $contenido2.=$result13->fields['col_tot'];          // 3       col_tot
        $contenido2.=';';
        $contenido2.='"' . trim($result13->fields['menopausia']) . '"';   //  1   factor1
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['antihiper'] . '"';    //  1   factor2
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['obesi'] . '"'; //  1   factor3
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['acv_prema'] . '"';    //  1   factor4
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['trigli'] . '"';    //  1   factor5
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['hdl_col'] . '"';   //  1   factor6
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['hiperglu'] . '"';   //  1   factor7
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['microalbu'] . '"';    //  1   factor8
        $contenido2.=';';
        $contenido2.='"' . $result13->fields['rcvg'] . '"'; //  rcvg
        $contenido2.=';';

        $contenido2.='
';
        $result13->MoveNext();
    }
}

if ($head) {

    // TRAILER
    $contenido2.='"T"';
    $contenido2.=';';
    $cantidad_registros2 = $result12->numRows();
    $cantidad_registros2 += $result13->numRows();
    $contenido2.=$cantidad_registros2; // CantidadRegistros	6	Cantidad de registros que vinieron
    $contenido2.=';';
    //creo y abro el archivo
    if (!$handle2 = fopen("./archivos/" . $filename_remediar, "w")) { //'a'
        echo "<br>No se Puede abrir ($filename_remediar)";
        exit;
    } else {
        ftruncate($handle2, filesize("./archivos/" . $filename_remediar));
    }

    if (fwrite($handle2, $contenido2) == FALSE) {
        echo "<br>No se Puede escribir  ($filename_remediar)";
        exit;
    } else {
        $where2 = substr($where2, 1, strlen($where2));
        $where3 = substr($where3, 1, strlen($where3));
        echo "<br>El Archivo ($filename_remediar) se genero con exito ";
        echo "<a href='./archivos/$filename_remediar'>Ver</a>";
        $fecha_generacion = date("Y-m-d H:m:s");
        $partes = explode(',', $where2);
        $partes2 = explode(',', $where3);
        $i = 0;
        $cuenta = 0;
        while ($i < strlen($where2)) {
            if ($where2[$i] == ",") {
                $cuenta++;
            }
            $i++;
        }
        $i = 0;
        $cuenta2 = 0;
        while ($i < strlen($where3)) {
            if ($where3[$i] == ",") {
                $cuenta2++;
            }
            $i++;
        }
        $j = 0;
        while ($j <= $cuenta) {
            if ($partes[$j] != '') {
                $consulta2 = "update trazadoras.clasificacion_remediar set enviado='s', fecha_envio='$fecha_generacion' where id_clasificacion=$partes[$j]";
                sql($consulta2, "Error al insertar en archivos enviados") or fin_pagina();
                //}
            }
            $j++;
        }
        $j = 0;
        while ($j <= $cuenta2) {
            if ($partes2[$j] != '') {
                $consulta2 = "update trazadoras.clasificacion_remediar2 set enviado='s', fecha_envio='$fecha_generacion' where id_clasificacion=$partes2[$j]";
                sql($consulta2, "Error al insertar en archivos enviados") or fin_pagina();
            }
            $j++;
        }
    }
    fclose($handle2);
} else {
    echo "<br>No hay registros para generar";
}
//var_dump($contenido2);
?>