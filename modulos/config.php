<?php
/*
$Author: fernando $
$Revision: 1.97 $
$Date: 2006/12/19 21:33:07 $
*/

/**********************************
 ** Modulo admin                 **
 **********************************/
// Configuracion
$item[]=Array(
		"nombre" => "config",
		"modulo" => "admin",
		"descripcion" => "Herramientas",
		"posicion" => "3",
		"tipo" => "sub"
);
// Cambiar Contraseña
$item[]=Array(
		"nombre" => "usuarios_contra",
		"modulo" => "admin",
		"descripcion" => "Cambiar Contraseña",
		"padre"        => "config",
		"posicion" => "1",
);
// Colores
$item[]=Array(
		"nombre" => "config_color",
		"modulo" => "admin",
		"descripcion" => "Colores",
		"padre"        => "config",
		"posicion" => "2",
);
// Perfil de Usuario
$item[]=Array(
		"nombre" => "usuarios_perfil",
		"modulo" => "admin",
		"descripcion" => "Perfil de Usuario",
		"padre"        => "config",
		"posicion" => "4",
);
// configuracion del sistema
$item[]=Array(
		"nombre" => "sistema",
		"modulo" => "admin",
		"descripcion" => "Sistema",
		"padre" => "config",
		"posicion" => "5",
		"tipo" => "sub"
);
$item[]=Array (
		"nombre" => "usuarios_view",
		"modulo" => "admin",
		"descripcion" => "Usuarios",
		"padre" => "sistema",
		"posicion" => "1"
);
$item[]=Array (
		"nombre" => "usuarios_conectados",
		"modulo" => "admin",
		"descripcion" => "Usuarios conectados",
		"padre" => "sistema",
		"posicion" => "2"
);
$item[]=Array (
		"nombre" => "usuarios_nuevo",
		"modulo" => "admin",
		"descripcion" => "Nuevo Usuario",
		"padre" => "sistema",
		"posicion" => "2"
);
$item[]=Array (
		"nombre" => "feriados",
		"modulo" => "admin",
		"descripcion" => "Feriados",
		"padre" => "config",
		"posicion" => "3"
);

/**********************************
 ** Modulo bancos                **
 **********************************/

// Bancos
$item[]=Array (
		"nombre" => "bancos",
		"modulo" => "bancos",
		"descripcion" => "Bancos",
		"padre" => "administracion",
		"posicion" => "1",
		"tipo" => "sub"
);

// venta de facturas
$item[]=array(
		"nombre" => "facturas_venta",
		"modulo" => "bancos",
		"descripcion" => "Venta de facturas",
		"padre" => "bancos",
		"posicion" => "5"
);

//--------------------- Balances ------------------
$item[]=array(
		"nombre" => "bancos_balances",
		"modulo" => "bancos",
		"descripcion" => "Balances",
		"padre" => "bancos",
		"posicion" => "1",
		"tipo" => "sub"
);
// asiento remuneraciones
$item[]=array(
		"nombre" => "balances/asiento_remuneracion",
		"modulo" => "bancos",
		"descripcion" => "Asiento de Remuneraciones",
		"padre" => "bancos_balances",
		"posicion" => "1"
);
// asiento ventas
$item[]=array(
		"nombre" => "balances/asiento_ventas",
		"modulo" => "bancos",
		"descripcion" => "Asiento de Ventas",
		"padre" => "bancos_balances",
		"posicion" => "1"
);
// retenciones
$item[]=array(
		"nombre" => "balances/retenciones",
		"modulo" => "bancos",
		"descripcion" => "Asiento de Retenciones",
		"padre" => "bancos_balances",
		"posicion" => "1"
);
// retenciones
$item[]=array(
		"nombre" => "balances/asiento_compras",
		"modulo" => "bancos",
		"descripcion" => "Asiento de Compras",
		"padre" => "bancos_balances",
		"posicion" => "1"
);
// asiento de bancos
$item[]=array(
		"nombre" => "balances/asiento_bancos",
		"modulo" => "bancos",
		"descripcion" => "Asiento de Bancos",
		"padre" => "bancos_balances",
		"posicion" => "1"
);

// balance
$item[]=array(
		"nombre" => "balances/balance",
		"modulo" => "bancos",
		"descripcion" => "Balance",
		"padre" => "bancos_balances",
		"posicion" => "1"
);

// -------------------- Movimiento ----------------
$item[]=array(
		"nombre" => "bancos_movi",
		"modulo" => "bancos",
		"descripcion" => "Movimientos",
		"padre" => "bancos",
		"posicion" => "1",
		"tipo" => "sub"
);
// cheques debitados
$item[]=array(
		"nombre" => "bancos_movi_chdeb",
		"modulo" => "bancos",
		"descripcion" => "Cheques Debitados",
		"padre" => "bancos_movi",
		"posicion" => "1"
);
// Cheques Pendientes
$item[]=array(
		"nombre" => "bancos_movi_chpen",
		"modulo" => "bancos",
		"descripcion" => "Cheques Pendientes",
		"padre" => "bancos_movi",
		"posicion" => "2"
);
// Debito
$item[]=array(
		"nombre" => "bancos_movi_debitos",
		"modulo" => "bancos",
		"descripcion" => "Débitos",
		"padre" => "bancos_movi",
		"posicion" => "3"
);
$item[]=array(
		"nombre" => "cheques_para_confirmar",
		"modulo" => "bancos",
		"descripcion" => "Cheques Para Confirmar",
		"padre" => "bancos_movi",
		"posicion" => "3"
);
// Cheques entre fechas
/*$item[]=array(
		"nombre" => "bancos_movi_chfecha",
		"descripcion" => "Cheques entre fechas",
		"padre" => "bancos_movi",
		"posicion" => "4"
);*/
// Depositos Acreditos
$item[]=array(
		"nombre" => "bancos_movi_depacr",
		"modulo" => "bancos",
		"descripcion" => "Depositos acreditados",
		"padre" => "bancos_movi",
		"posicion" => "5"
);
// Depositos Pendientes
$item[]=array(
		"nombre" => "bancos_movi_deppen",
		"modulo" => "bancos",
		"descripcion" => "Depositos Pendientes",
		"padre" => "bancos_movi",
		"posicion" => "6"
);
// Tarjetas Pendientes
$item[]=array(
		"nombre" => "bancos_movi_taracr",
		"modulo" => "bancos",
		"descripcion" => "Tarjetas Acreditadas",
		"padre" => "bancos_movi",
		"posicion" => "7"
);
// Tarjetas Pendientes
$item[]=array(
		"nombre" => "bancos_movi_tarpen",
		"modulo" => "bancos",
		"descripcion" => "Tarjetas Pendientes",
		"padre" => "bancos_movi",
		"posicion" => "8"
);
// Saldos
$item[]=array(
		"nombre" => "bancos_movi_saldos",
		"modulo" => "bancos",
		"descripcion" => "Saldos",
		"padre" => "bancos_movi",
		"posicion" => "9"
);
// ---------------- fin Movimientos ---------------

// ---------------- Valores de Terceros -----------
$item[]=array(
		"nombre" => "bancos_val",
		"modulo" => "bancos",
		"descripcion" => "Valores de Terceros",
		"padre" => "bancos",
		"posicion" => "2",
		"tipo" => "sub"
);

//Cheques de Terceros
$item[]=array(
		"nombre" => "bancos_valores_chter",
		"modulo" => "bancos",
		"descripcion" => "Cheques de Terceros",
		"padre" => "bancos_val",
		"posicion" => "1"
);
//Ingresos de cheques
$item[]=array(
		"nombre" => "bancos_valores_ingch",
		"modulo" => "bancos",
		"descripcion" => "Ingreso Cheque",
		"padre" => "bancos_val",
		"posicion" => "2"
);
// ------------------ fin Valores Terceros -----------

// ------------------ Ingresos -----------------------
$item[]=array(
		"nombre" => "bancos_ing",
		"modulo" => "bancos",
		"descripcion" => "Ingresos",
		"padre" => "bancos",
		"posicion" => "3",
		"tipo" => "sub"
);
// Cheques
$item[]=array(
		"nombre" => "bancos_ing_ch",
		"modulo" => "bancos",
		"descripcion" => "Cheques",
		"padre" => "bancos_ing",
		"posicion" => "1"
);
// Depositos
$item[]=array(
		"nombre" => "bancos_ing_dep",
		"modulo" => "bancos",
		"descripcion" => "Depósitos",
		"padre" => "bancos_ing",
		"posicion" => "2"
);
// Debitos
$item[]=array(
		"nombre" => "bancos_ing_deb",
		"modulo" => "bancos",
		"descripcion" => "Débitos",
		"padre" => "bancos_ing",
		"posicion" => "3"
);
// Tarjetas
$item[]=array(
		"nombre" => "bancos_ing_tar",
		"modulo" => "bancos",
		"descripcion" => "Tarjetas",
		"padre" => "bancos_ing",
		"posicion" => "4"
);

//transferencia
$item[]=array(
		"nombre" => "bancos_listado_transferencias",
		"modulo" => "bancos",
		"descripcion" => "Transferencias",
		"padre" => "bancos_ing",
		"posicion" => "5"
);
// ------------------------ fin Ingresos -------------

// ------------------------ Mantenimiento ------------
$item[]=array(
		"nombre" => "bancos_mant",
		"modulo" => "bancos",
		"descripcion" => "Mantenimiento",
		"padre" => "bancos",
		"posicion" => "4",
		"tipo" => "sub"
);
// Bancos
$item[]=array(
		"nombre" => "bancos_mant_ban",
		"modulo" => "bancos",
		"descripcion" => "Bancos",
		"padre" => "bancos_mant",
		"posicion" => "1"
);
// Depositos
$item[]=array(
		"nombre" => "bancos_mant_dep",
		"modulo" => "bancos",
		"descripcion" => "Depósitos",
		"padre" => "bancos_mant",
		"posicion" => "2"
);
// Debitos
$item[]=array(
		"nombre" => "bancos_mant_deb",
		"modulo" => "bancos",
		"descripcion" => "Débitos",
		"padre" => "bancos_mant",
		"posicion" => "3"
);
// Tarjetas
$item[]=array(
		"nombre" => "bancos_mant_tar",
		"modulo" => "bancos",
		"descripcion" => "Tarjetas",
		"padre" => "bancos_mant",
		"posicion" => "4"
);
// Proveedores
/*$item[]=array(
		"nombre" => "bancos_mant_prov",
		"descripcion" => "Proveedores",
		"padre" => "bancos_mant",
		"posicion" => "5"
);*/
// ----------------------- Fin Mantenimiento -----------
// ----------------------- Informes --------------------
$item[]=array(
		"nombre" => "bancos_informes",
		"modulo" => "bancos",
		"descripcion" => "Informes",
		"padre" => "bancos",
		"posicion" => "5"
);
// ----------------------- Fin Informes ----------------
// ----------------------- Chequeras -------------------

// ----------------------- Control de Ingresos --------------------
$item[]=array(
		"nombre" => "control_cheques",
		"modulo" => "bancos",
		"descripcion" => "Control de Ingresos",
		"padre" => "bancos",
		"posicion" => "7"
);
// ----------------------- Fin Control de Ingresos ----------------

// ----------------------- Chequeras -------------------
$item[]=array(
	    "nombre" => "chequeras",
		"modulo" => "bancos",
		"descripcion" => "Chequeras",
		"padre" => "bancos",
		"posicion" => "6",
		"tipo" => "sub"
);
//ABM chequeras
$item[]=array(
		"nombre" => "bancos_chequeras",
		"modulo" => "bancos",
		"descripcion" => "Chequeras",
		"padre" => "chequeras",
		"posicion" => "1"
);
//ABM chequeras
$item[]=array(
		"nombre" => "bancos_listado_chequeras",
		"modulo" => "bancos",
		"descripcion" => "Listado Chequeras",
		"padre" => "chequeras",
		"posicion" => "2"
);

// ----------------------- Cheques Diferidos -------------------
$item[]=array(
	    "nombre" => "Cheques Diferidos",
		"modulo" => "bancos",
		"descripcion" => "Cheques Diferidos",
		"padre" => "bancos",
		"posicion" => "8",
		"tipo" => "sub"
);
//Ingreso manual de cheques
$item[]=array(
		"nombre" => "alta_cheque_dif",
		"modulo" => "bancos",
		"descripcion" => "Ingreso Cheque Diferido",
		"padre" => "Cheques Diferidos",
		"posicion" => "1"
);

//Cheques diferidos no depositados
$item[]=array(
		"nombre" => "ver_chequesdif_pend",
		"modulo" => "bancos",
		"descripcion" => "Cheques Diferidos Pendientes",
		"padre" => "Cheques Diferidos",
		"posicion" => "2"
);

//Cheques diferidos finalizados
$item[]=array(
		"nombre" => "ver_chequesdif_fin",
		"modulo" => "bancos",
		"descripcion" => "Cheques Diferidos Finalizados",
		"padre" => "Cheques Diferidos",
		"posicion" => "3"
);

// ----------------------- Fin Chequeras ---------------


/**********************************
 ** Modulo inicio                **
 **********************************/

 $item[]=Array (
		"nombre" => "inicio",
		"modulo" => "inicio",
		"descripcion" => "Inicio",
		"ayuda" => "Página principal",
		"posicion" => "1",
		"padre" => "default",
		"link" => "index.php",
		"tipo" => "sub"
);

/**********************************
 ** Modulo internet              **
 **********************************/

$item[]=Array (
		"nombre" => "internet",
		"modulo" => "internet",
		"padre" => "administracion",
		"descripcion" => "Internet",
		"posicion" => "11",
		"tipo" => "sub"
);
$item[]=Array (
		"nombre" => "internet_view",
		"modulo" => "internet",
		"padre" => "internet",
		"descripcion" => "Ver usuarios",
		"ayuda" => "Ver lista de usuarios",
		"posicion" => "1"
);
$item[]=Array (
		"nombre" => "internet_add",
		"modulo" => "internet",
		"padre" => "internet",
		"descripcion" => "Agregar usuario",
		"ayuda" => "Agregar un nuevo usuario a la base de datos",
		"posicion" => "2"
);


/**********************************
 ** Modulo licitaciones          **
 **********************************/

$item[]=Array (
		"nombre" => "licitaciones",
		"modulo" => "licitaciones",
		"descripcion" => "Licitaciones",
		"padre" => "administracion",
		"posicion" => "2",
		"tipo" => "sub"
);

// Configuraciones
$item[]=array(
        "nombre" => "configuraciones",
        "modulo" => "licitaciones",
        "descripcion" => "Configuraciones",
        "padre" => "licitaciones",
        "posicion" => "1",
        "tipo" => "sub"
);

 // Configuracion
$item[]=Array (
        "nombre" => "licitaciones_config",
        "modulo" => "licitaciones",
        "descripcion" => "Configuración",
        "padre" => "configuraciones",
        "posicion" => "1"
);

$item[]=Array (
        "nombre" => "listado_competidores",
        "modulo" => "licitaciones",
        "descripcion" => "Competidores",
        "padre" => "configuraciones",
        "posicion" => "2"
);

$item[]=Array (
        "nombre" => "listado_comp_cert",
        "modulo" => "licitaciones",
        "descripcion" => "C.F.C",
        "padre" => "configuraciones",
        "posicion" => "2"
);

$item[]=Array (
        "nombre" => "prioridad_detalle",
        "modulo" => "licitaciones",
        "descripcion" => "Prioridad de detalle",
        "padre" => "configuraciones",
        "posicion" => "3"
);

/*$item[]=Array (
        "nombre" => "nueva_entidad",
        "modulo" => "licitaciones",
        "descripcion" => "Entidades",
        "padre" => "configuraciones",
        "posicion" => "4"
);*/

$item[]=Array (
        "nombre" => "ETAPS",
        "modulo" => "licitaciones",
        "descripcion" => "ETAPS",
        "padre" => "configuraciones",
        "posicion" => "5"
);

$item[]=Array (
        "nombre" => "desc_folletos",
        "modulo" => "licitaciones",
        "descripcion" => "Cargar Folletos",
        "padre" => "configuraciones",
        "posicion" => "6"
);
$item[]=Array (
        "nombre" => "desc_productos",
        "modulo" => "licitaciones",
        "descripcion" => "Cargar Descripcion",
        "padre" => "configuraciones",
        "posicion" => "7"
);

$item[]=Array (

        "nombre" => "resumen_dolar",
        "modulo" => "general",
        "descripcion" => "Valor Dolar",
        "padre" => "configuraciones",
        "posicion" => "8"
);
$item[]=Array (

        "nombre" => "licitaciones_config_botones",
        "modulo" => "licitaciones",
        "descripcion" => "Preferencias Usuarios",
        "padre" => "configuraciones",
        "posicion" => "9"
);

//-----------------
//ver licitaciones
$item[]=Array (
		"nombre" => "licitaciones_view",
		"modulo" => "licitaciones",
		"descripcion" => "Ver Licitaciones",
		"padre" => "licitaciones",
		"posicion" => "18"
);
// Nueva
$item[]=Array (
		"nombre" => "licitaciones_new",
		"modulo" => "licitaciones",
		"descripcion" => "Nueva Licitación",
		"padre" => "licitaciones",
		"posicion" => "12"
);
// Conbranzas
$item[]=Array (
		"nombre" => "lic_cobranzas",
		"modulo" => "licitaciones",
		"descripcion" => "Seguimiento de cobros",
		"padre" => "licitaciones",
		"posicion" => "17"
);
// Estadisticas
$item[]=Array (
		"nombre" => "licitaciones_stats",
		"modulo" => "licitaciones",
		"descripcion" => "Estadísticas",
		"padre" => "licitaciones",
		"posicion" => "8"
);

$item[]=Array (
		"nombre" => "lic_cargar_res",
		"modulo" => "licitaciones",
		"descripcion" => "Cargar Resultados",
		"ayuda" => "Carga los resultados para la licitacion indicada",
		"padre" => "licitaciones",
		"posicion" => "4"
);


$item[]=Array (
		"nombre" => "lic_ver_res",
		"modulo" => "licitaciones",
		"descripcion" => "Resultados Licitaciones",
		"padre" => "licitaciones",
		"posicion" => "16"
);



$item[]=Array (
		"nombre" => "licitaciones_papelera",
		"modulo" => "licitaciones",
		"descripcion" => "Papelera",
		"padre" => "licitaciones",
		"posicion" => "13"
);


/*
$item[]=Array (
		"nombre" => "contactos",
		"modulo" => "contactos_generales",
		"descripcion" => "Contactos Generales",
		"padre" => "licitaciones",
		"posicion" => "13"
);
*/
$item[]=Array (
		"nombre" => "lic_gestiones",
		"modulo" => "licitaciones",
		"descripcion" => "Gestiones Generales",
		"padre" => "licitaciones",
		"posicion" => "10"
);

$item[]=Array (
		"nombre" => "lic_garantia_oferta",
		"modulo" => "licitaciones",
		"descripcion" => "Licitaciones OC",
		"padre" => "licitaciones",
		"posicion" => "11"
);
$item[]=Array (
		"nombre" => "lic_prod_ofer",
		"modulo" => "licitaciones",
		"descripcion" => "Productos Ofertados",
		"padre" => "licitaciones",
		"posicion" => "15"
);

$item[]=Array (
		"nombre" => "agregar_firmante",
		"modulo" => "licitaciones",
		"descripcion" => "Cargar Firmantes",
		"padre" => "configuraciones",
		"posicion" => "19"
);

// licitaciones - documentacion
$item[]=Array (
		"nombre" => "doc_lista",
		"modulo" => "licitaciones",
		"descripcion" => "Documentación",
		"padre" => "licitaciones",
		"posicion" => "19"
);

$item[]=Array (
		"nombre" => "garantias_contrato",
		"modulo" => "licitaciones",
		"descripcion" => "Garantías de contrato",
		"padre" => "licitaciones",
		"posicion" => "19"
);

/**********************************
 ** Modulo mensajes              **
 **********************************/


$item[]=Array (
		"nombre" => "administracion",
		"modulo" => "mensajes",
		"descripcion" => "Administración",
		"posicion" => "1",
		"tipo" => "sub"
);
$item[]=Array (
		"nombre" => "mensajes",
		"modulo" => "mensajes",
		"descripcion" => "Mensajes",
		"padre" => "administracion",
		"posicion" => "12"
);


/**********************************
 ** Modulo ordprod               **
 **********************************/

// Ordenes de Produccion
$item[]=Array (
		"nombre" => "ordenes",
		"modulo" => "ordprod",
		"descripcion" => "Produccion",
		"padre" => "administracion",
		"posicion" => "4",
		"tipo" => "sub"
);
// auditoría de calidad
/*$item[]=Array (
		"nombre" => "seguimiento_produccion_bsas_audit",
		"modulo" => "ordprod",
		"descripcion" => "Auditoría de calidad de producción Bs. As.",
		"padre" => "ordenes",
		"posicion" => "1"
);*/
// Ver Ordenes
$item[]=Array (
		"nombre" => "ordenes_ver",
		"modulo" => "ordprod",
		"descripcion" => "Ordenes de Producción",
		"padre" => "ordenes",
		"posicion" => "1"
);
$item[]=Array (
		"nombre" => "ordenes_nueva",
		"modulo" => "ordprod",
		"descripcion" => "Nueva Orden de Producción",
		"padre" => "ordenes",
		"posicion" => "3"
);

// Ver Ordenes Gestion 2 y no soy las ordenes anteriores del gestion 1
$item[]=Array (
		"nombre" => "ordenes_ver_gestion_2",
		"modulo" => "ordprod",
		"descripcion" => "Ordenes de Producción Gestion 2",
		"padre" => "ordenes",
		"posicion" => "1"
);

/*
$item[]=Array (
		"nombre" => "ordenes_ant",
		"modulo" => "ordprod",
		"descripcion" => "Ordenes Antiguas",
		"padre" => "ordenes",
		"posicion" => "4"
);*/
$item[]=Array (
		"nombre" => "ordenes_ver_old",
		"modulo" => "ordprod",
		"descripcion" => "Ordenes Viejas",
		"padre" => "ordenes",
		"posicion" => "5"
);
$item[]=Array (
		"nombre" => "altas_ensambladores",
		"modulo" => "ordprod",
		"descripcion" => "Nuevo Ensamblador",
		"padre" => "ordenes",
		"posicion" => "6"
);

$item[]=Array (
		"nombre" => "ver_seguimiento_ordenes",
		"modulo" => "ordprod",
		"descripcion" => "Seguimiento de Producción",
		"padre" => "ordenes",
		"posicion" => "7"
);


$item[]=Array (
		"nombre" => "generar_codigos_barra",
		"modulo" => "ordprod",
		"descripcion" => "Generar Códigos de Barra",
		"padre" => "ordenes",
		"posicion" => "8"
);

//transportes
/*$item[]=Array (
		"nombre" => "transporte",
		"modulo" => "ordprod",
		"descripcion" => "Transportes",
		"padre" => "ordenes",
		"posicion" => "1"
);*/

//Ordenes de quemado, ahora prueba de vida
$item[]=Array (
		"nombre" => "listado_ordenes",
		"modulo" => "ordquem",
		"descripcion" => "Prueba de vida",
		"padre" => "ordenes",
		"posicion" => "9"
);
// Drivers y Maquinas
$item[]=Array (
		"nombre" => "ordenes_drivers",
		"modulo" => "ordprod",
		"descripcion" => "Drivers",
		"padre" => "ordenes",
		"posicion" => "10",
		"tipo" => "sub"
);

// Drivers
$item[]=Array (
		"nombre" => "listar_drivers",
		"modulo" => "ordprod",
		"descripcion" => "Listar Drivers",
		"padre" => "ordenes_drivers",
		"posicion" => "1"
);
$item[]=Array (
		"nombre" => "nuevo_drivers",
		"modulo" => "ordprod",
		"descripcion" => "Nuevo Drivers",
		"padre" => "ordenes_drivers",
		"posicion" => "2"
);
// Maquinas
$item[]=Array (
		"nombre" => "listar_maquina",
		"modulo" => "ordprod",
		"descripcion" => "Listar Maquinas",
		"padre" => "ordenes_drivers",
		"posicion" => "3"
);

$item[]=Array (
		"nombre" => "ver_prorrogas",
		"modulo" => "ordprod",
		"descripcion" => "Prorrogas",
		"padre" => "ordenes",
		"posicion" => "11"
);

$item[]=Array (
		"nombre" => "seguimiento_produccion_bsas",
		"modulo" => "ordprod",
		"descripcion" => "Producción Bs. As.",
		"padre" => "ordenes",
		"posicion" => "13"
);

$item[]=Array (
		"nombre" => "productos_compuestos",
		"modulo" => "ordprod",
        "descripcion" => "Productos Compuestos",
		"padre" => "ordenes",
		"posicion" => "12"
);
$item[]=Array (
		"nombre" => "listado_componentes_daniados",
		"modulo" => "ordprod",
        "descripcion" => "Componentes Dañados",
		"padre" => "ordenes",
		"posicion" => "12"
);
/*$item[]=Array (
		"nombre" => "nueva_maquina",
		"modulo" => "ordprod",
		"descripcion" => "Nueva Maquina",
		"padre" => "ordenes_drivers",
		"posicion" => "4"
);*/
/*
$item[]=Array (
		"nombre" => "linea_produccion_bs_as",
		"modulo" => "ordprod",
     "descripcion" => "Línea de producción Bs. As.",
		"padre" => "ordenes",
		"posicion" => "12"
);
*/
/**********************************
 ** Modulo permisos              **
 **********************************/
$item[]=Array (
		"nombre" => "acl_list",
		"modulo" => "permisos",
		"descripcion" => "Permisos",
		"padre" => "sistema",
		"posicion" => "5"
);
//IMPORTANTE: COMENTAR ESTE NODO PARA GENERAR CUANDO SE GENERAN LOS PERMISOS PARA PAGINAS DE MENU
$item[]=Array (
		"nombre" => "permisos_main",
		"modulo" => "permisos",
		"descripcion" => "Permisos(nuevo)",
		"padre" => "sistema",
		"posicion" => "5"
);
$item[]=Array (
		"nombre" => "borrar_cache",
		"modulo" => "admin",
		"descripcion" => "Borrar Cache",
		"ayuda" => "Borrar la cache de permisos\nUsar cuando se cambia algun permiso.",
		"padre" => "sistema",
		"link" => encode_link($html_root."/index.php",array("mode" => "borrar_cache")),
		"posicion" => "6"
);
$item[]=Array (
		"nombre" => "actualizar_permisos",
		"modulo" => "permisos",
		"descripcion" => "Actualizar Permisos Usuario",
		"padre" => "sistema",
		"posicion" => "5"
);
/*
$item[]=Array (
		"nombre" => "cambiar_usuario",
		"modulo" => "permisos",
		"descripcion" => "Cambiar de Usuario",
		"ayuda" => "Convertirse en otro usuario",
		"padre" => "sistema",
		"posicion" => "6"
);
*/
/**********************************
 ** Modulo productos             **
 **********************************/


$item[]=Array (
		"nombre" => "seg_maq_listar",
		"modulo" => "productos",
		"descripcion" => "Computadoras CDR",
		"tipo" => "item",
		"padre" => "productos",
		"posicion" => "1"
);

$item[]=Array (
		"nombre" => "proveedores",
		"modulo" => "productos",
		"descripcion" => "Proveedores",
		"tipo" => "item",
		"padre" => "productos",
		"posicion" => "2"
);

$item[]=Array (
		"nombre" => "carga_prov",
		"modulo" => "productos",
		"descripcion" => "Nuevo Proveedor",
		"tipo" => "item",
		"padre" => "productos",
		"posicion" => "3"
);
/*
$item[]=Array (
		"nombre" => "productos1",
		"modulo" => "general",
		"descripcion" => "Productos",
		"tipo" => "item",
		"padre" => "productos",
		"posicion" => "4"
);*/

$item[]=Array (
		"nombre" => "listado_productos_especificos",
		"modulo" => "productos",
		"descripcion" => "Productos Específicos",
		"tipo" => "item",
		"padre" => "productos",
		"posicion" => "4"
);

$item[]=Array (
		"nombre" => "productos",
		"modulo" => "productos",
		"descripcion" => "Productos",
		"tipo" => "sub",
		"padre" => "administracion",
		"posicion" => "5"
);


$item[]=Array (
		"nombre" => "listado_productos",
		"modulo" => "productos",
		"descripcion" => "Productos Generales",
		"tipo" => "item",
		"padre" => "productos",
		"posicion" => "6"
);

$item[]=Array (
		"nombre" => "productos_codigob",
		"modulo" => "productos",
		"descripcion" => "Códigos de Barra",
		"tipo" => "item",
		"padre" => "productos",
		"posicion" => "7"
);

$item[]=Array (
		"nombre" => "unificar_productos",
		"modulo" => "productos",
		"descripcion" => "Unificar Productos",
		"tipo" => "item",
		"padre" => "productos",
		"posicion" => "8"
);

//Modulo de Mercaderia en Transito
/*$item[]=Array (
		"nombre" => "menu_merc_trans",
		"modulo" => "merc_trans",
		"descripcion" => "Mercadería en Tránsito",
		"tipo" => "sub",
		"padre" => "productos",
		"posicion" => "8"
);

$item[]=Array (
		"nombre" => "listado_merc_trans",
		"modulo" => "merc_trans",
		"descripcion" => "En Curso",
		"tipo" => "item",
		"padre" => "menu_merc_trans",
		"posicion" => "1"
);

$item[]=Array (
		"nombre" => "historial_merc_trans",
		"modulo" => "merc_trans",
		"descripcion" => "Historial",
		"tipo" => "item",
		"padre" => "menu_merc_trans",
		"posicion" => "2"
);
*/


$item[]=Array (
		"nombre" => "compatibilidades",
		"modulo" => "productos",
		"descripcion" => "Compatibilidades",
		"tipo" => "item",
		"padre" => "productos",
		"posicion" => "7"
);

$item[]=Array (
		"nombre" => "garantias_listado",
		"modulo" => "productos",
		"descripcion" => "Garantias",
		"tipo" => "item",
		"padre" => "productos",
		"posicion" => "8"
);

/****************
Menu de Stock
****************/
$item[]=Array (
		"nombre" => "stock",
		"modulo" => "stock",
		"descripcion" => "Stock",
		"tipo" => "sub",
		"padre" => "productos",
		"posicion" => "1"
);
/*
Distintos sub menues
*/
$item[]=Array (
		"nombre" => "stock_buenos_aires",
		"modulo" => "stock",
		"descripcion" => "Stock Buenos Aires",
		"tipo" => "item",
		"padre" => "stock",
		"posicion" => "0"
);

$item[]=Array (
		"nombre" => "stock_san_luis",
		"modulo" => "stock",
		"descripcion" => "Stock San Luis",
		"tipo" => "item",
		"padre" => "stock",
		"posicion" => "1"
);

$item[]=Array (
		"nombre" => "stock_st_ba",
		"modulo" => "stock",
		"descripcion" => "Stock Servicio Técnico Bs. As.",
		"tipo" => "item",
		"padre" => "stock",
		"posicion" => "4"
);

$item[]=Array (
		"nombre" => "stock_sl_incorriente",
		"modulo" => "stock",
		"descripcion" => "Stock San Luis - Incorriente",
		"tipo" => "item",
		"padre" => "stock",
		"posicion" => "4"
);
$item[]=Array (
		"nombre" => "stock_produccion",
		"modulo" => "stock",
		"descripcion" => "Stock en Producción",
		"tipo" => "item",
		"padre" => "stock",
		"posicion" => "4"
);

$item[]=Array (
		"nombre" => "listar_rma",
		"modulo" => "stock",
		"descripcion" => "RMA",
		"tipo" => "item",
		"padre" => "stock",
		"posicion" => "7"
);

$item[]=Array (
		"nombre" => "stock_coradir",
		"modulo" => "stock",
		"descripcion" => "Stock Coradir",
		"tipo" => "item",
		"padre" => "stock",
		"posicion" => "4"
);


$item[]=Array (
		"nombre" => "stock_produccion_san_luis",
		"modulo" => "stock",
		"descripcion" => "Stock Producción San Luis",
		"tipo" => "item",
		"padre" => "stock",
		"posicion" => "4"
);

$item[]=Array (
		"nombre" => "submenu_movimiento",
		"modulo" => "mov_material",
		"descripcion" => "Movimiento de Material",
		"tipo" => "sub",
		"padre" => "stock",
		"posicion" => "0"
	);

$item[]=Array (
		"nombre" => "listado_mov_material",
		"modulo" => "mov_material",
		"descripcion" => "Listado Movimientos",
		"tipo" => "item",
		"padre" => "submenu_movimiento",
		"posicion" => "1"
);

$item[]=Array (
		"nombre" => "detalle_movimiento",
		"modulo" => "mov_material",
		"descripcion" => "Nuevo Movimiento",
		"tipo" => "item",
		"padre" => "submenu_movimiento",
		"posicion" => "1"
);
$item[]=Array (
		"nombre" => "listar_rma_san_luis",
		"modulo" => "stock",
		"descripcion" => "RMA San Luis",
		"tipo" => "item",
		"padre" => "stock",
		"posicion" => "7"
);
/*****************
Submenu en Productos:
Reclamo de Partes
******************/
/*
$item[]=Array (
		"nombre" => "reclamo_partes",
		"modulo" => "reclamo_partes",
		"descripcion" => "Reclamo de Partes",
		"tipo" => "sub",
		"padre" => "productos",
		"posicion" => "9"
);


$item[]=Array (
		"nombre" => "seguimiento_reclamo_partes",
		"modulo" => "reclamo_partes",
		"descripcion" => "Seguimiento",
		"tipo" => "item",
		"padre" => "reclamo_partes",
		"posicion" => "0"
);

$item[]=Array (
		"nombre" => "detalle_reclamo_partes",
		"modulo" => "reclamo_partes",
		"descripcion" => "Nuevo Reclamo de Partes",
		"tipo" => "item",
		"padre" => "reclamo_partes",
		"posicion" => "1"
);
*/
/************************************************************************
**	 								SUBMENU COMPRAS                  			  **
************************************************************************/
$item[]=Array (
		"nombre"=>"menucompras",
		"descripcion" => "Compras/Ventas",
		"tipo" => "sub",
		"padre" => "administracion",
		"posicion" => "3"
);
/************************************************************************
**	 								Retenciones y Percepciones                 			   **
************************************************************************/
$item[]=Array (
		"nombre"=>"retenciones_percepciones.ui",
    "modulo"=>"ret_per",
		"descripcion" => "Retenciones/Percepciones",
		"tipo" => "item",
		"padre" => "menucompras",
		"posicion" => "5"
);


/************************************************************************
**	 								Factoring                  			  **
************************************************************************/
$item[]=Array (
		"nombre"=>"listado_factoring",
        "modulo"=>"factoring",
		"descripcion" => "Factoring",
		"tipo" => "item",
		"padre" => "menucompras",
		"posicion" => "5"
);



//******************************************************
//Menu de Cash Flow
//*******************************************************
$item[]=Array (
		"nombre" => "flujo_dinero", //nombre del archivo .php
		"modulo" => "flujo_dinero",
		"descripcion" => "Cash Flow", //lo que ve el usuario
		"tipo" => "item", //tipo de item opciones-> (item,sub)
		"padre" => "menucompras", //item tipo "sub" del que depende
		"posicion" => "4" //posicion dentro de los hijos del padre
);


//-------------------------------------
// Modulo facturacion
//-------------------------------------
$item[]=Array (
		"nombre" => "facturas",
		"modulo" => "facturas",
		"descripcion" => "Facturación",
		"tipo" => "sub",
		"padre" => "menucompras",
		"posicion" => "0"
);

$item[]=Array (
		"nombre" => "factura_listar",
		"modulo" => "facturas",
		"padre" => "facturas",
		"descripcion" => "Listar Facturas",
		"ayuda" => "Ver lista de facturas",
		"posicion" => "1"
);
$item[]=Array (
		"nombre" => "factura_nueva",
		"modulo" => "facturas",
		"padre" => "facturas",
		"descripcion" => "Nueva Factura",
		"ayuda" => "Crear una nueva factura",
		"posicion" => "0"
);

$item[]=Array (
		"nombre" => "compras_consolidadas", //nombre del archivo .php
		"modulo" => "ord_compra",
		"descripcion" => "Compras Consolidadas", //lo que ve el usuario
		"tipo" => "item", //tipo de item opciones-> (item,sub)
		"padre" => "menucompras", //item tipo "sub" del que depende
		"posicion" => "4" //posicion dentro de los hijos del padre
);


//******************************************
// Modulo Compras
//******************************************
$item[]=Array (
		"nombre"=>"compra",
		"modulo" => "ord_compra",
		"descripcion" => "Compras",
		"tipo" => "sub",
		"padre" => "menucompras",
		"posicion" => "1"
);
/********************************************
Ordenes de Pago
*********************************************/
$item[]=Array (
		"nombre"=>"menupagos",
		"modulo" => "ord_pago",
		"descripcion" => "Pagos",
		"tipo" => "sub",
		"padre" => "menucompras",
		"posicion" => "1"
);
//Listado
$item[]=Array (
		"nombre"=>"ord_pago_listar",
		"modulo" => "ord_pago",
		"descripcion" => "Listar Pagos",
		"tipo" => "item",
		"padre" => "menupagos",
		"posicion" => "1"
);
//Nueva orden de pago

$item[]=Array (
		"nombre" => "ord_compra_asociar?modo=oc_pagos",
		"modulo" => "ord_compra",
		"descripcion" => "Nueva Orden de Pago",
		"padre" => "menupagos",
		"posicion" => "0"
);

/********************
 Ordenes de Compra
*********************/

$item[]=Array (
		"nombre"=>"menu_ordenes_compra",
		"modulo" => "ord_compra",
		"descripcion" => "Ordenes de Compra",
		"tipo" => "sub",
		"padre" => "compra",
		"posicion" => "1"
);

$item[]=Array (
		"nombre" => "ord_compra_asociar?modo=oc_compras",
		"modulo" => "ord_compra",
		"descripcion" => "Nueva Orden",
		"padre" => "menu_ordenes_compra",
		"posicion" => "0"
);

$item[]=Array (
		"nombre" => "ord_compra_listar",
		"modulo" => "ord_compra",
		"descripcion" => "Listar Ordenes",
		"padre" => "menu_ordenes_compra",
		"posicion" => "1"
);

$item[]=Array (
		"nombre"=>"listado_posad",
		"modulo" => "ord_compra",
		"descripcion" => "Administración de Posad",
		"padre" => "menu_ordenes_compra",
		"posicion" => "2"
);

$item[]=Array (
		"nombre" => "prodcomp",
		"modulo" => "ord_compra",
		"descripcion" => "Productos Comprados",
		"padre" => "compra",
		"posicion" => "2"
);

$item[]=Array (
		"nombre" => "recepciones_rapidas",
		"modulo" => "ord_compra",
		"descripcion" => "Recepciones Rápidas",
		"padre" => "menu_ordenes_compra",
		"posicion" => "0"
);

/********************
 Compras Serv Tec
*********************/
/*
$item[]=Array (
		"nombre"=>"menu_ordenes_compra_serv_tec",
		"modulo" => "ord_compra",
		"descripcion" => "Ordenes de Servicio Técnico",
		"tipo" => "sub",
		"padre" => "compra",
		"posicion" => "1"
);

$item[]=Array (
		"nombre" => "caso_admin?backto=../ord_compra/ord_compra.php&pag=asociar&coradir_bs_as=no&modo=oc_serv_tec",
		"modulo" => "casos",
		"descripcion" => "Nueva Orden",
		"padre" => "menu_ordenes_compra_serv_tec",
		"posicion" => "0"
);

$item[]=Array (
		"nombre" => "listado_oc_serv_tec",
		"modulo" => "ord_compra",
		"descripcion" => "Listar Ordenes",
		"padre" => "menu_ordenes_compra_serv_tec",
		"posicion" => "1"
);*/

/********************
 Compras Internacionales
*********************/
/*
$item[]=Array (
		"nombre"=>"menu_ordenes_internacionales",
		"modulo" => "ord_compra",
		"descripcion" => "Ordenes de Importación",
		"tipo" => "sub",
		"padre" => "compra",
		"posicion" => "1"
);

$item[]=Array (
		"nombre" => "ord_compra?modo=oc_internacional",
		"modulo" => "ord_compra",
		"descripcion" => "Nueva Orden",
		"padre" => "menu_ordenes_internacionales",
		"posicion" => "0"
);

$item[]=Array (
		"nombre" => "listado_oc_internacionales",
		"modulo" => "ord_compra",
		"descripcion" => "Listar Ordenes",
		"padre" => "menu_ordenes_internacionales",
		"posicion" => "1"
);
*/
/********************
 Ordenes de Pago
*********************/
/*$item[]=Array (
		"nombre"=>"menu_ordenes_pagos",
		"modulo" => "ord_compra",
		"descripcion" => "Ordenes de Pago",
		"tipo" => "sub",
		"padre" => "compra",
		"posicion" => "1"
);

$item[]=Array (
		"nombre" => "ord_compra?modo=oc_pagos",
		"modulo" => "ord_compra",
		"descripcion" => "Nueva Orden",
		"padre" => "menu_ordenes_pagos",
		"posicion" => "0"
);

$item[]=Array (
		"nombre" => "listado_oc_pagos",
		"modulo" => "ord_compra",
		"descripcion" => "Listar Ordenes",
		"padre" => "menu_ordenes_pagos",
		"posicion" => "1"
);
*/
/*$item[]=Array (
		"nombre" => "ord_compra_lite",
		"modulo" => "ord_compra",
		"descripcion" => "OC_Lite",
		"padre" => "compra",
		"posicion" => "1"
);*/


//-------------------------------------
// Modulo remitos
//-------------------------------------
$item[]=Array (
		"nombre" => "remitos",
		"modulo" => "remitos",
		"descripcion" => "Remitos",
		"tipo" => "sub",
		"padre" => "menucompras",
		"posicion" => "2"
);
$item[]=Array (
		"nombre" => "remito_nuevo", //nombre del archivo .php
		"modulo" => "remitos",
		"descripcion" => "Nuevo Remito", //lo que ve el usuario
		"tipo" => "item", //tipo de item opciones-> (item,sub)
		"padre" => "remitos", //item tipo "sub" del que depende
		"posicion" => "0" //posicion dentro de los hijos del padre
);

$item[]=Array (
		"nombre" => "remito_listar", //nombre del archivo .php
		"modulo" => "remitos",
		"descripcion" => "Listar remitos", //lo que ve el usuario
		"tipo" => "item", //tipo de item opciones-> (item,sub)
		"padre" => "remitos", //item tipo "sub" del que depende
		"posicion" => "1" //posicion dentro de los hijos del padre

);

$item[]=Array (
		"nombre" => "remito_int_listar", //nombre del archivo .php
		"modulo" => "remito_interno",
		"descripcion" => "Listar Remitos Internos", //lo que ve el usuario
		"tipo" => "item", //tipo de item opciones-> (item,sub)
		"padre" => "remitos", //item tipo "sub" del que depende
		"posicion" => "2" //posicion dentro de los hijos del padre

);

$item[]=Array (
		"nombre" => "adjuntos_remitos_listado",
		"modulo" => "remitos",
		"descripcion" => "Adjuntos de remitos",
		"tipo" => "item",
		"padre" => "remitos",
		"posicion" => "2"
);


//--------------------------------------------------------------
//  modulo cuentas de proveedores
//----------------------------------------------------------------
$item[]=Array (
		"nombre" => "lista_cuentas", //nombre del archivo .php
		"modulo" => "cuenta_prov",
		"descripcion" => "Cuentas de proveedores", //lo que ve el usuario
		"tipo" => "item", //tipo de item opciones-> (item,sub)
		"padre" => "menucompras", //item tipo "sub" del que depende
		"posicion" => "5" //posicion dentro de los hijos del padre
);


//--------------------------------------------------------------
//  modulo facturas de proveedores
//----------------------------------------------------------------
$item[]=Array (
		"nombre" => "fact_prov_listar", //nombre del archivo .php
		"modulo" => "factura_proveedores",
		"descripcion" => "Facturas de proveedores", //lo que ve el usuario
		"tipo" => "item", //tipo de item opciones-> (item,sub)
		"padre" => "menucompras", //item tipo "sub" del que depende
		"posicion" => "3" //posicion dentro de los hijos del padre
);
$item[]=Array (
		"nombre" => "libro_iva", //nombre del archivo .php
		"modulo" => "facturas", //nombre del directorio
		"descripcion" => "Libro de IVA", //lo que ve el usuario
		"tipo" => "item", //tipo de item opciones-> (item,sub)
		"padre" => "menucompras", //item tipo "sub" del que depende
		"posicion" => "4" //posicion dentro de los hijos del padre
);
$item[]=Array (
		"nombre" => "archivo", //nombre del archivo .php
		"modulo" => "facturas", //nombre del directorio
		"descripcion" => "Archivo sin parametros", //lo que ve el usuario
		"tipo" => "item", //tipo de item opciones-> (item,sub)
		"padre" => "menucompras", //item tipo "sub" del que depende
		"posicion" => "6" //posicion dentro de los hijos del padre
);

//--------------------------------------------------------------
//  modulo de notas de credito
//----------------------------------------------------------------
        $item[]=Array (
        "nombre" => "nota_credito_menu", //nombre del archivo .php
        "modulo" => "ord_compra",
        "descripcion" => "Notas de Crédito", //lo que ve el usuario
        "tipo" => "sub", //tipo de item opciones-> (item,sub)
        "padre" => "menucompras", //item tipo "sub" del que depende
        "posicion" => "4" //posicion dentro de los hijos del padre
);

        $item[]=Array (
        "nombre" => "nota_credito", //nombre del archivo .php
        "modulo" => "ord_compra",
        "descripcion" => "Nueva Nota de Crédito", //lo que ve el usuario
        "tipo" => "item", //tipo de item opciones-> (item,sub)
        "padre" => "nota_credito_menu", //item tipo "sub" del que depende
        "posicion" => "1" //posicion dentro de los hijos del padre
);

  $item[]=Array (
        "nombre" => "nota_credito_listar", //nombre del archivo .php
        "modulo" => "ord_compra",
        "descripcion" => "Ver Notas de Crédito", //lo que ve el usuario
        "tipo" => "item", //tipo de item opciones-> (item,sub)
        "padre" => "nota_credito_menu", //item tipo "sub" del que depende
        "posicion" => "2" //posicion dentro de los hijos del padre
);


/*************************************
 Modulo Pedidos de Material
**************************************/
$item[]=Array (
        "nombre" => "listado_mov_material?pedido_material=1", //nombre del archivo .php
        "modulo" => "mov_material",
        "descripcion" => "Pedidos de Material", //lo que ve el usuario
        "tipo" => "item", //tipo de item opciones-> (item,sub)
        "padre" => "menucompras", //item tipo "sub" del que depende
        "posicion" => "4" //posicion dentro de los hijos del padre
);


/************************************************************************
**	 													                  			  **
************************************************************************/

// MODULO Archivos
$item[]=Array (
        "nombre"=>"archivos",
        "modulo" => "archivos",
        "descripcion" => "Archivos",
        "tipo" => "sub",
        "padre" => "administracion",
        "posicion" => "10"
);
$item[]=Array (
        "nombre" => "admin",
        "modulo" => "archivos",
        "descripcion" => "Lista de Archivos",
        "padre" => "archivos",
        "posicion" => "0"
);
$item[]=Array (
        "nombre" => "archivos_nuevo",
        "modulo" => "archivos",
        "descripcion" => "Subir Archivos",
        "padre" => "archivos",
        "posicion" => "1"
);
//-------------------------------------------------
//-------------------------------------------------
// MODULO Archivos
$item[]=Array (
        "nombre"=>"tareas",
        "modulo" => "tareas",
        "descripcion" => "Tareas",
        "tipo" => "sub",
        "padre" => "administracion",
        "posicion" => "9"
);
$item[]=Array (
        "nombre" => "listado_tareas",
        "modulo" => "tareas",
        "descripcion" => "Tareas",
        "padre" => "tareas",
        "posicion" => "0"
);



$item[]=Array (
        "nombre" => "ds_listado",
        "modulo" => "tareas",
        "descripcion" => "Division Software",
        "padre" => "tareas",
        "posicion" => "2"
);

$item[]=Array (
        "nombre" => "lista_proyectos",
        "modulo" => "tareas",
        "descripcion" => "Proyectos",
        "padre" => "tareas",
        "posicion" => "2"
);
//-------------------------------------------------

/**********************************
 ** Modulo stock                 **
 **********************************/
/*
$item[]=Array (
		"nombre" => "stock",
		"modulo" => "stock",
		"descripcion" => "Stock",
		"tipo" => "item",
		"padre" => "productos",
		"posicion" => "8"
);
*/


//*************  Modulo CLIENTES ****************

$item[]=Array (
		"nombre"=>"clientes",
		"modulo" => "modulo_clientes",
		"descripcion" => "Entidades/Clientes",
		"tipo" => "sub",
		"padre" => "administracion",
		"posicion" => "8"
);


$item[]=Array (
		"nombre" => "nuevo_cliente",
		"modulo" => "modulo_clientes",
		"descripcion" => "Agregar/Modificar",
		"padre" => "clientes",
		"posicion" => "1"
);
$item[]=Array (
		"nombre" => "nuevo_cliente?es_pymes=1",
		"modulo" => "modulo_clientes",
		"descripcion" => "PYMES",
		"padre" => "clientes",
		"posicion" => "1"
);

$item[]=Array (
		"nombre" => "viejos_clientes",
		"modulo" => "modulo_clientes",
		"descripcion" => "Consultar Clientes Viejos",
		"padre" => "clientes",
		"posicion" => "2"
);
$item[]=Array (
		"nombre" => "org",
		"modulo" => "modulo_clientes",
		"descripcion" => "Organismos",
		"padre" => "clientes",
		"posicion" => "3"
);

$item[]=Array (
		"nombre" => "clientes_referencia",
		"modulo" => "modulo_clientes",
		"descripcion" => "Clientes de referencia",
		"padre" => "clientes",
		"posicion" => "3"
);

$item[]=Array (
		"nombre" => "llamadas_listado",
		"modulo" => "modulo_clientes",
		"descripcion" => "Registro de Llamadas",
		"padre" => "clientes",
		"posicion" => "3"
);
/*
$item[]=Array (
		"nombre" => "lic_calif_lista",
		"modulo" => "modulo_clientes",
		"descripcion" => "Satisfaccion Clientes",
		"padre" => "clientes",
		"posicion" => "2"
);*/


//*************  Modulo Caja ****************

$item[]=Array (
        "nombre"=>"caja",
        "modulo" => "caja",
        "descripcion" => "Caja",
        "tipo" => "sub",
        "padre" => "administracion",
        "posicion" => "9"
);

/*****Modulo BsAS************/
$item[]=Array (
        "nombre"=>"cajabsas",
        "modulo" => "caja",
        "descripcion" => "Caja Buenos Aires",
        "tipo" => "sub",
        "padre" => "caja",
        "posicion" => "1"
);
/***************************************
  **                Caja de Seguridad                      **
  ***************************************/
 $item[]=Array (
        "nombre"=>"caja_seguridad",
        "modulo" => "caja",
        "descripcion" => "Cajas de Seguridad",
        //"tipo" => "sub",
        "padre" => "caja",
        "posicion" => "1"
);

/*
$item[]=Array (
        "nombre" => "egresos_bsas",
        "modulo" => "caja",
        "descripcion" => "Ingresos/Egresos",
        "padre" => "cajabsas",
        "posicion" => "1"
);
$item[]=Array (
        "nombre" => "caja_bsas",
        "modulo" => "caja",
        "descripcion" => "Caja Diaria",
        "padre" => "cajabsas",
        "posicion" => "2"
);
$item[]=Array (
        "nombre" => "listado_bsas",
        "modulo" => "caja",
        "descripcion" => "Ver listados",
        "padre" => "cajabsas",
        "posicion" => "3"
);
*/
$item[]=Array (
        "nombre" => "listado?distrito=2",
        "modulo" => "caja",
        "descripcion" => "Ver listados",// (nuevo)
        "padre" => "cajabsas",
        "posicion" => "1"
);
$item[]=Array (
        "nombre" => "ingresos_egresos?distrito=2",
        "modulo" => "caja",
        "descripcion" => "Ingresos/Egresos",// (nuevo)
        "padre" => "cajabsas",
        "posicion" => "2"
);
$item[]=Array (
        "nombre" => "caja_diaria?distrito=2",
        "modulo" => "caja",
        "descripcion" => "Caja Diaria",// (nuevo)
        "padre" => "cajabsas",
        "posicion" => "3"
);

$item[]=Array (
        "nombre" => "listado_anticipo",
        "modulo" => "caja",
        "descripcion" => "Listar Anticipos a rendir",// (nuevo)
        "padre" => "cajabsas",
        "posicion" => "4"
);

/*****Modulo San Luis************/
$item[]=Array (
        "nombre"=>"cajasl",
        "modulo" => "caja",
        "descripcion" => "Caja San Luis",
        "tipo" => "sub",
        "padre" => "caja",
        "posicion" => "2"
);

/*
$item[]=Array (
        "nombre" => "ingresos_sl",
        "modulo" => "caja",
        "descripcion" => "Ingresos/Egresos",
        "padre" => "cajasl",
        "posicion" => "1"
);
$item[]=Array (
        "nombre" => "caja_sl",
        "modulo" => "caja",
        "descripcion" => "Caja Diaria",
        "padre" => "cajasl",
        "posicion" => "2"
);
$item[]=Array (
        "nombre" => "listado_sl",
        "modulo" => "caja",
        "descripcion" => "Ver listados",
        "padre" => "cajasl",
        "posicion" => "3"
);
*/
$item[]=Array (
        "nombre" => "ingresos_egresos?distrito=1",
        "modulo" => "caja",
        "descripcion" => "Ingresos/Egresos",// (nuevo)
        "padre" => "cajasl",
        "posicion" => "1"
);

$item[]=Array (
        "nombre" => "caja_diaria?distrito=1",
        "modulo" => "caja",
        "descripcion" => "Caja Diaria",// (nuevo)
        "padre" => "cajasl",
        "posicion" => "2"
);

$item[]=Array (
        "nombre" => "listado?distrito=1",
        "modulo" => "caja",
        "descripcion" => "Ver listados",// (nuevo)
        "padre" => "cajasl",
        "posicion" => "3"
);

/********************************************
 *************  Modulo Casos ****************
 ********************************************/

$item[]=Array (
        "nombre"=>"casos",
        "modulo" => "casos",
        "descripcion" => "Servicio Técnico",
        "tipo" => "sub",
        "padre" => "administracion",
        "posicion" => "10"
);
$item[]=Array (
        "nombre" => "caso_admin?coradir_bs_as=no",
        "modulo" => "casos",
        "descripcion" => "Administración de Casos",
        "padre" => "casos",
        "posicion" => "1"
);
$item[]=Array (
        "nombre" => "caso_admin?coradir_bs_as=si",
        "modulo" => "casos",
        "descripcion" => "Administración de Casos Coradir Bs. As.",
        "padre" => "casos",
        "posicion" => "1"
);
$item[]=Array (
        "nombre" => "listado_visitas",
        "modulo" => "casos",
        "descripcion" => "Listado de Visitas",
        "padre" => "casos",
        "posicion" => "1"
);

$item[]=Array (
        "nombre" => "reporte_tecnico",
        "modulo" => "casos",
        "descripcion" => "Reportes de Problemas Técnicos",
        "padre" => "casos",
        "posicion" => "2"
);
$item[]=Array (
        "nombre" => "caso_ate",
        "modulo" => "casos",
        "descripcion" => "C.A.S.",
        "padre" => "casos",
        "posicion" => "4"
);
$item[]=Array (
        "nombre" => "caso_nuevo",
        "modulo" => "casos",
        "descripcion" => "Nuevo Caso",
        "padre" => "casos",
        "posicion" => "3"
);
$item[]=Array (
        "nombre" => "caso_est",
        "modulo" => "casos",
        "descripcion" => "Estados del Usuario",
        "padre" => "casos",
        "posicion" => "5"
);
$item[]=Array (
        "nombre" => "dependencias",
        "modulo" => "casos",
        "descripcion" => "Dependencias",
        "padre" => "casos",
        "posicion" => "6"
);

$item[]=Array (
        "nombre" => "permisos_tecnicos",
        "modulo" => "casos",
        "descripcion" => "Tecnicos Simples",
        "padre" => "casos",
        "posicion" => "7"
);
$item[]=Array (
        "nombre" => "caso_estadisticas",
        "modulo" => "casos",
        "descripcion" => "Estadisticas",
        "padre" => "casos",
        "posicion" => "8"
);
$item[]=Array (
        "nombre" => "nuevo_tecnico_visita",
        "modulo" => "casos",
        "descripcion" => "Nuevo Técnico Visitas",
        "padre" => "casos",
        "posicion" => "8"
);

$item[]=Array (
        "nombre" => "gerencia_st",
        "modulo" => "casos",
        "descripcion" => "Gerencia de S.T.",
        "padre" => "casos",
        "posicion" => "8"
);

/*
$item[]=Array (
		"nombre" => "muletos",
		"modulo" => "casos",
		"descripcion" => "Muletos",
		"padre" => "casos",
		"posicion" => "9",
		"tipo" => "sub"
);

$item[]=Array (
		"nombre" => "muletos_admin",
		"modulo" => "casos",
		"descripcion" => "Administrar Muletos",
		"padre" => "muletos",
		"posicion" => "1"
);
*/
$item[]=Array (
		"nombre" => "muletos_listado",
		"modulo" => "casos",
		"descripcion" => "Monitores RMA",
		"padre" => "casos",
		"posicion" => "9"
);

// menu viejo !

/********************************************
 *************  Modulo Casos ****************
 ********************************************/
/*
$item[]=Array (
        "nombre"=>"casos",
        "modulo" => "casos",
        "descripcion" => "C.A.S.",
        "tipo" => "sub",
        "padre" => "administracion",
        "posicion" => "10"
);
$item[]=Array (
        "nombre" => "caso_admin",
        "modulo" => "casos",
        "descripcion" => "Administración de Casos",
        "padre" => "casos",
        "posicion" => "1"
);
$item[]=Array (
        "nombre" => "reporte_tecnico",
        "modulo" => "casos",
        "descripcion" => "Reportes de Problemas Técnicos",
        "padre" => "casos",
        "posicion" => "2"
);
$item[]=Array (
        "nombre" => "caso_ate",
        "modulo" => "casos",
        "descripcion" => "Atendido por",
        "padre" => "casos",
        "posicion" => "4"
);
$item[]=Array (
        "nombre" => "caso_nuevo",
        "modulo" => "casos",
        "descripcion" => "Nuevo Caso",
        "padre" => "casos",
        "posicion" => "3"
);
$item[]=Array (
        "nombre" => "caso_est",
        "modulo" => "casos",
        "descripcion" => "Estados del Usuario",
        "padre" => "casos",
        "posicion" => "5"
);
$item[]=Array (
        "nombre" => "dependencias",
        "modulo" => "casos",
        "descripcion" => "Dependencias",
        "padre" => "casos",
        "posicion" => "6"
);

$item[]=Array (
        "nombre" => "permisos_tecnicos",
        "modulo" => "casos",
        "descripcion" => "Tecnicos Simples",
        "padre" => "casos",
        "posicion" => "7"
);
$item[]=Array (
        "nombre" => "caso_estadisticas",
        "modulo" => "casos",
        "descripcion" => "Estadisticas",
        "padre" => "casos",
        "posicion" => "8"
);
*/
/********************************************
 *************  Modulo Maquinas ****************
 ********************************************/

/*$item[]=Array (
        "nombre"=>"maquinas",
        "modulo" => "maquinas",
        "descripcion" => "Administrar Drivers",
        "tipo" => "sub",
        "padre" => "administracion",
        "posicion" => "11"
);
$item[]=Array (
        "nombre" => "drivers_admin",
        "modulo" => "maquinas",
        "descripcion" => "Administración de Drivers",
        "padre" => "maquinas",
        "posicion" => "1"
);
$item[]=Array (
        "nombre" => "drivers_nuevo",
        "modulo" => "maquinas",
        "descripcion" => "Nuevos Drivers",
        "padre" => "maquinas",
        "posicion" => "2"
);
$item[]=Array (
        "nombre" => "maquinas_admin",
        "modulo" => "maquinas",
        "descripcion" => "Administración de Maquinas",
        "padre" => "maquinas",
        "posicion" => "3"
);
$item[]=Array (
        "nombre" => "maquinas_nuevo",
        "modulo" => "maquinas",
        "descripcion" => "Nuevas Maquinas",
        "padre" => "maquinas",
        "posicion" => "4"
);*/

/**********************************
 ** Modulo Personal              **
 **********************************/

$item[]=Array (
		"nombre" => "personal",
		"modulo" => "personal",
		"descripcion" => "Personal",
		"padre" => "administracion",
		"posicion" => "10",
		"tipo" => "sub"
);
$item[]=Array (
		"nombre" => "listado_legajos",
		"modulo" => "personal",
		"descripcion" => "Listado de legajos",
		"padre" => "personal",
		"posicion" => "1"
);
$item[]=Array (
		"nombre" => "listado_evaluaciones",
		"modulo" => "personal",
		"descripcion" => "Listado de evaluaciones",
		"padre" => "personal",
		"posicion" => "2"
);
$item[]=Array (
		"nombre" => "evaluadores",
		"modulo" => "personal",
		"descripcion" => "Evaluadores",
		"padre" => "personal",
		"posicion" => "3"
);
$item[]=Array (
		"nombre" => "listado_liq_sueldo",
		"modulo" => "liquidacion_sueldos",
		"descripcion" => "Listado de Recibos de Sueldos",
		"padre" => "personal",
		"posicion" => "4"
);
$item[]=Array (
		"nombre" => "adelanto_cuenta",
		"modulo" => "personal",
		"descripcion" => "Adelantos",
		"padre" => "personal",
		"posicion" => "5"
);
$item[]=Array (
		"nombre" => "listado_pagar_sueldo",
		"modulo" => "personal",
		"descripcion" => "Pago de Sueldos",
		"padre" => "personal",
		"posicion" => "6"
);
$item[]=Array (
		"nombre" => "control_presentismo",
		"modulo" => "personal",
		"descripcion" => "Control de presentismo",
		"padre" => "personal",
		"posicion" => "7"
);
$item[]=Array (
		"nombre" => "capacitaciones",
		"modulo" => "personal",
		"descripcion" => "Capacitaciones",
		"padre" => "personal",
		"posicion" => "7"
);
$item[]=Array (
		"nombre" => "capacitados",
		"modulo" => "personal",
		"descripcion" => "Calificación de personal capacitado",
		"padre" => "personal",
		"posicion" => "7"
);
$item[]=Array (
		"nombre" => "directorio",
		"modulo" => "personal",
		"descripcion" => "Directorio",
		"padre" => "personal",
		"posicion" => "7"
);
/*$item[]=Array (
		"nombre" => "listado_pagar_sueldo",
		"modulo" => "personal",
		"descripcion" => "Pago de Sueldos 2",
		"padre" => "personal",
		"posicion" => "7"
);*/
/**********************************
 ** Modulo Calidad              **
 **********************************/

$item[]=Array (
		"nombre" => "calidad",
		"modulo" => "calidad",
		"descripcion" => "Calidad",
		"padre" => "administracion",
		"posicion" => "12",
		"tipo" => "sub"
);

$item[]=Array (
		"nombre" => "noconformes",
		"modulo" => "calidad",
		"descripcion" => "Productos No Conformes",
		"padre" => "calidad",
		"posicion" => "1"
);

$item[]=Array (
		"nombre" => "listado_pac_pap",
		"modulo" => "calidad",
		"descripcion" => "P.A.C./P.A.P.",
		"padre" => "calidad",
		"posicion" => "2"
);

$item[]=Array (
		"nombre" => "lic_calif_lista",
		"modulo" => "modulo_clientes",
		"descripcion" => "Satisfaccion Clientes",
		"padre" => "calidad",
		"posicion" => "3"
);
$item[]=Array (
		"nombre" => "clasif_prove",
		"modulo" => "productos",
		"descripcion" => "Calificación Proveedor",
		"padre" => "calidad",
		"posicion" => "4"
);
$item[]=Array (
		"nombre" => "listado_manual?distrito=bs_as",
		"modulo" => "calidad",
		"descripcion" => "Manuales Bs. As.",
		"padre" => "calidad",
		"posicion" => "5"
);
$item[]=Array (
		"nombre" => "listado_manual?distrito=san_luis",
		"modulo" => "calidad",
		"descripcion" => "Manuales San Luis",
		"padre" => "calidad",
		"posicion" => "6"
);

$item[]=Array (
		"nombre" => "listar_quejas",
		"modulo" => "calidad",
		"descripcion" => "Quejas/Consultas",
		"padre" => "calidad",
		"posicion" => "6"
);

$item[]=Array (
		"nombre" => "indicadores",
		"modulo" => "calidad",
		"descripcion" => "Indicadores",
		"padre" => "calidad",
		"posicion" => "7"
);

$item[]=Array (
		"nombre" => "eventos_incidentes",
		"modulo" => "calidad",
		"descripcion" => "Eventos/Incidentes",
		"padre" => "calidad",
		"posicion" => "8"
);

$item[]=Array (
		"nombre" => "calidad_auditorias",
		"modulo" => "calidad",
		"descripcion" => "Auditoría de Calidad",
		"padre" => "calidad",
		"posicion" => "8"
);

/**********************************
 **     Modulo Presupuestos      **
 **********************************/

$item[]=Array (
		"nombre"=>"presupuestos_menu",
		"descripcion" => "Presupuestos",
		"tipo" => "sub",
		"padre" => "administracion",
		"posicion" => "16"
);

$item[]=Array (
		"nombre"=>"presupuestos_view",
		"descripcion" => "Ver Presupuestos",
		"modulo" => "presupuestos",
		"padre" => "presupuestos_menu",
		"posicion" => "1"
);

 /*
 $item[]=Array (
		"nombre"=>"presupuestos_new",
		"descripcion" => "Nuevo Presupuesto",
		"modulo" => "presupuestos",
		"padre" => "presupuestos_menu",
		"posicion" => "1"
);
  */

$item[]=Array (
		"nombre"=>"presupuestos_new?es_pyme=0",
		"descripcion" => "Nuevo Presupuesto",
		"modulo" => "presupuestos",
		"padre" => "presupuestos_menu",
		"posicion" => "1"
);

 $item[]=Array (
		"nombre"=>"presupuestos_new?es_pyme=1",
		"descripcion" => "Nuevo Presupuesto Pyme",
		"modulo" => "presupuestos",
		"padre" => "presupuestos_menu",
		"posicion" => "1"
);

/**********************************
 **     Modulo Muestras       **
 **********************************/

$item[]=Array (
		"nombre"=>"menu_muestras",
		"descripcion" => "Muestras",
		"tipo" => "sub",
		"padre" => "administracion",
		"posicion" => "17"
);

$item[]=Array (
		"nombre"=>"seguimiento_muestras",
		"descripcion" => "Listado Muestras",
		"modulo" => "muestras",
		"padre" => "menu_muestras",
		"posicion" => "1"
);
/*$item[]=Array (
		"nombre"=>"detalle_muestras",
		"descripcion" => "Nueva Muestra",
		"modulo" => "muestras",
		"padre" => "menu_muestras",
		"posicion" => "2"
);*/


/**********************************
 **     Modulo PCPower       **
 **********************************/

$item[]=Array (
		"nombre"=>"pcpower",
		"descripcion" => "PcPower",
		"tipo" => "sub",
		"padre" => "administracion",
		"posicion" => "15"
);

$item[]=Array (
		"nombre"=>"pcpower_presupuestos_menu",
		"descripcion" => "Presupuestos",
		"tipo" => "sub",
		"padre" => "pcpower",
		"posicion" => "1"
);

$item[]=Array (
		"nombre"=>"presupuestos/pcpower_presupuestos_view",
		"descripcion" => "Ver Presupuestos",
		"modulo" => "pcpower",
		"padre" => "pcpower_presupuestos_menu",
		"posicion" => "1"
);

$item[]=Array (
		"nombre"=>"presupuestos/pcpower_presupuestos_new",
		"descripcion" => "Nuevo Presupuesto",
		"modulo" => "pcpower",
		"padre" => "pcpower_presupuestos_menu",
		"posicion" => "2"
);

//clientes
$item[]=Array (
		"nombre"=>"pcpower_clientes_menu",
		"descripcion" => "Clientes",
		"tipo" => "sub",
		"padre" => "pcpower",
		"posicion" => "1"
);

$item[]=Array (
		"nombre"=>"Entidades/pcpower_nuevo_cliente",
		"descripcion" => "Clientes",
		"modulo" => "pcpower",
		"padre" => "pcpower_clientes_menu",
		"posicion" => "1"
);

$item[]=Array (
		"nombre"=>"Entidades/pcpower_org",
		"descripcion" => "Organismos",
		"modulo" => "pcpower",
		"padre" => "pcpower_clientes_menu",
		"posicion" => "2"
);
/********************************************
             Modulo Comidas
********************************************/
$item[]=Array (
		"nombre" => "comidas",
		"modulo" => "comidas",
		"descripcion" => "Comidas",
		"padre" => "administracion",
		"posicion" => "18",
		"tipo" => "sub"
);
$item[]=Array (
		"nombre"=>"comidas_pedidos",
		"descripcion" => "Pedido de Comidas",
		"modulo" => "comidas",
		"padre" => "comidas",
		"posicion" => "2"
);
$item[]=Array (
		"nombre"=>"comidas_agregar",
		"descripcion" => "Administración de Comidas",
		"modulo" => "comidas",
		"padre" => "comidas",
		"posicion" => "1"
);

/********************************************
             Modulo Logística
********************************************/
$item[]=Array (
		"nombre" => "logistica",
		"modulo" => "admin",
		"descripcion" => "Logística",
		"padre" => "administracion",
		"posicion" => "18",
		"tipo" => "sub"
);
$item[]=Array (
		"nombre"=>"transporte_listado",
		"descripcion" => "Transportes",
		"modulo" => "logistica",
		"padre" => "logistica",
		"posicion" => "2"
);
$item[]=Array (
		"nombre" => "entregas",
		"modulo" => "ordprod",
		"descripcion" => "Entregas",
		"padre" => "logistica",
		"posicion" => "8"
);

$item[]=Array (
		"nombre" => "listado_envios",
		"modulo" => "ordprod",
		"descripcion" => "Listado Envios",
		"padre" => "logistica",
		"posicion" => "1"
);

/********************************************
             Modulo Contabilidad
********************************************/
$item[]=Array (
		"nombre" => "menu_contabilidad",
		"modulo" => "admin",
		"descripcion" => "Contabilidad",
		"padre" => "administracion",
		"posicion" => "18",
		"tipo" => "sub"
);
$item[]=Array (
		"nombre"=>"listado_imputaciones",
		"descripcion" => "Imputaciones",
		"modulo" => "contabilidad",
		"padre" => "menu_contabilidad",
		"posicion" => "2"
);

$item[]=Array (
		"nombre" => "inventario.html",
		"modulo" => "stock",
		"descripcion" => "Bienes de Uso",
		"tipo" => "item",
		"padre" => "menu_contabilidad",
		"posicion" => "8"
);

$item[]=Array (
		"nombre" => "cuentas_lista",
		"modulo" => "contabilidad",
		"descripcion" => "Cuentas",
		"tipo" => "item",
		"padre" => "menu_contabilidad",
		"posicion" => "8"
);

$item[]=Array (
		"nombre" => "items_imputados",
		"modulo" => "contabilidad",
		"descripcion" => "Items imputados",
		"tipo" => "item",
		"padre" => "menu_contabilidad",
		"posicion" => "8"
);
$item[]=Array (
		"nombre" => "saldold_listado",
		"modulo" => "contabilidad",
		"descripcion" => "Saldo LD",
		"tipo" => "item",
		"padre" => "menu_contabilidad",
		"posicion" => "8"
);

?>