<?php

//variables globales
$resultado; //para el resultado de los queries que se ejecuten
$filas_encontradas;//
$serialp;
$serialu;
$parte_serial;
$primer_ser;
$letra;
$pserial;
$userial;

//FUNCIONES DE CODIFICACION Y DECODIFICACION DE ATRIBUTOS DE LA BD

//dado el codigo de accesorio, devuelve el nombre correspondiente o
//el string "Codigo desconocido" si hay error.
function decod_accesorio($codigo)
{switch ($codigo)
 {case(0):return "Teclado";break;
  case(1):return "Mouse";break;
  case(2):return "Parlantes";break;
  case(3):return "Microfono";break;
  default:return "Codigo desconocido";
 }
}

//dado el nombre del accesorio, devuelve el codigo correspondiente
//o -1 en caso de desconocer el modelo.
function encod_accesorio($acc)
{switch ($acc)
 {case("Teclado"):return 0;break;
  case("Mouse"):return 1;break;
  case("Parlantes"):return 2;break;
  case("Microfono"):return 3;break;
  default: return -1;
 }
}

//dado el codigo de componente, devuelve el nombre correspondiente o
//el string "Codigo desconocido" si hay error.
function decod_componente($codigo)
{ switch ($codigo)
 {case(0):return "Tarjeta Madre";break;
  case(1):return "Microprocesador";break;
  case(2):return "Memoria";break;
  case(3):return "Disco Rigido";break;
  case(4):return "Floppy";break;
  case(5):return "Lectora CD";break;
  case(6):return "Grabadora CD";break;
  case(7):return "Lectora DVD";break;
  case(8):return "Modem";break;
  case(9):return "Placa de Red";break;
  case(10):return "Placa de Sonido";break;
  case(11):return "Placa de Video";break;
  case(12):return "Plataforma";break;
  default:return "Codigo desconocido";
 }
}

//dado el nombre del componente, devuelve el codigo correspondiente
//o -1 en caso de desconocer el modelo.
function encod_componente($compo)
{switch ($compo)
 {case("Tarjeta Madre"):return 0;break;
  case("Microprocesador"):return 1;break;
  case("Memoria"):return 2;break;
  case("Disco Rigido"):return 3;break;
  case("Floppy"):return 4;break;
  case("Lectora CD"):return 5;break;
  case("Grabadora CD"):return 6;break;
  case("Lectora DVD"):return 7;break;
  case("Modem"):return 8;break;
  case("Placa de Red"):return 9;break;
  case("Placa de Sonido"):return 10;break;
  case("Placa de Video"):return 11;break;
  case("Plataforma"):return 12;break;
  default:return -1;
 }
}

//dado el codigo de tipo garantia de la orden de produccion,
//devuelve el nombre correspondiente o
//el string "Codigo desconocido" si hay error.
function decod_garantia($codigo)
{switch ($codigo)
 {case(0):return "Partes";break;
  case(1):return "Laboratorio";break;
  case(2):return "Domicilio";break;
  default:return "Codigo desconocido";
 }
}


//dado el nombre del tipo de garantia de la orden de produccion,
//devuelve el codigo correspondiente
//o -1 en caso de desconocer el modelo.
function encod_garantia($acc)
{switch ($acc)
 {case("Partes"):return 0;break;
  case("Laboratorio"):return 1;break;
  case("Domicilio"):return 2;break;
  default: return -1;
 }
}

//dado el codigo del estado de la orden de produccion,
//devuelve el nombre correspondiente o
//el string "Codigo desconocido" si hay error.
function decod_estado($codigo)
{switch ($codigo)
 {case(0):return "Pendiente";break;
  case(1):return "En Proceso";break;
  case(2):return "Terminada";break;
  default:return "Codigo desconocido";
 }
}


//dado el nombre del Estado de la orden de produccion,
//devuelve el codigo correspondiente
//o -1 en caso de desconocer el modelo.
function encod_estado($estado)
{switch ($estado)
 {case("Pendiente"):return 0;break;
  case("En Proceso"):return 1;break;
  case("Terminada"):return 2;break;
  default: return -1;
 }
}

function gen_serial($ens,$fecha,$modelo,$producto)
//calculamos ensamblador
{global $db;
if($fecha!=""){
global $resultado,$serialp,$serialu,$parte_serial,$primer_ser,$letra,$pserial,$userial;
 switch ($ens)
   {case "PLAN NACER":{$serialu=$serialp='C';break;}
    case "DYAR":{$serialu=$serialp='Y';break;}
    case "DIGITAL STORES":{$serialu=$serialp='S';break;}
    case "COSTANOR":{$serialu=$serialp='Z';break;}
    case "GLOBAL EXPRESS":{$serialu=$serialp='G';break;}
    case "PC HALL":{$serialu=$serialp='P';break;}
   }// fin switch
 list($d,$m,$a)=explode("/",$fecha);
  switch ($a) //calculamos año
  {case 2003:$serialu.='Q';$serialp.='Q';break;
   case 2004:$serialu.='R';$serialp.='R';break;
   case 2005:$serialu.='S';$serialp.='S';break;
   case 2006:$serialu.='T';$serialp.='T';break;
   case 2007:$serialu.='U';$serialp.='U';break;
   case 2008:$serialu.='V';$serialp.='V';break;
  } //fin switch
  $serialp.=$d; //calculamos dia
  $serialu.=$d;
  switch ($m)  //calculamos el mes
  {case '01':$serialu.='CE';$serialp.='CE';break;
   case '02':$serialu.='CF';$serialp.='CF';break;
   case '03':$serialu.='CM';$serialp.='CM';break;
   case '04':$serialu.='AA';$serialp.='AA';break;
   case '05':$serialu.='AM';$serialp.='AM';break;
   case '06':$serialu.='AJ';$serialp.='AJ';break;
   case '07':$serialu.='JJ';$serialp.='JJ';break;
   case '08':$serialu.='JA';$serialp.='JA';break;
   case '09':$serialu.='JS';$serialp.='JS';break;
   case '10':$serialu.='OO';$serialp.='OO';break;
   case '11':$serialu.='ON';$serialp.='ON';break;
   case '12':$serialu.='OD';$serialp.='OD';break;
  }// fin switch
if (($producto!="Computadoras CDR")&&($producto!="Server"))
{$serialu.='OM';$serialp.='OM';}
else
{switch($modelo)
  {case "ENTERPRISE":$serialu.='EN';$serialp.='EN';break;
   case "MATRIX":$serialu.='MA';$serialp.='MA';break;
   case "SERVER":$serialu.='SE';$serialp.='SE';break;
   default:$serialu.='OM';$serialp.='OM';break;
  }// fin switch
}
 $query="Select * from serial";
 $temp = $db->Execute($query) or die($db->ErrorMsg());
 /* While ($temp['lock']==1) //espero que lock se desbloqueada
 {$query="select * from serial";
  ejecutar_query($query);
  $temp=$resultado[0]; //uso variable temporaria
 }
 $query="update serial set lock='1' where nro=".$temp['nro'].";";
 ejecutar_query($query);
*/
 $primer=($temp->fields['nro']+1)%1000; //obtengo la primer maquina
 $pserial=$primer-1;
 if ($primer==000)
  $serialp.=chr(ord($temp->fields['letra'])+1);
 else
  $serialp.=$temp->fields['letra'];
 $parte_serial=$serialp; //obtengo primer parte de serial
 $primer_ser=$primer;    //obtengo el primer numero de serial
 $ultimo=($primer+$_POST['cant']-1)%1000; //obtengo la primer maquina
 if ($_POST['cant']+$temp->fields['nro']>1000) //actualizamos la letra
  $temp->fields['letra']=chr(ord($temp->fields['letra'])+1);
 $userial=$ultimo;
 $serialu.=$temp->fields['letra'];
 $letra=$temp->fields['letra'];
 if ($primer<100) //concateno valor con los 0 que pueden llegar a faltar
  $serialp.='0';
 if ($primer<10)
  $serialp.='0';
 if ($ultimo<100) //concateno valor con los 0 que pueden llegar a faltar
  $serialu.='0';
 if ($ultimo<10)
  $serialu.='0';
 $serialp.=$primer;
 $serialu.=$ultimo;
 }//if fecha!=""
}//fin funcion gen_serial

//funcion temporal para que personal de entrada de datos genere nros seriales a partir de
//el primer serial que ingresen ellos modificacion de gen_serial

function gen_serial2($ens,$fecha,$modelo, $ing_prim, $letra,$verificacion,$producto){
//calculamos ensamblador
global $resultado,$serialp,$serialu,$parte_serial,$primer_ser,$letra,$pserial,$userial;
if ($verificacion=="on")
{$s='';
 $s='ND';
 $s.=$_POST['nrord'];
 $s.='S';
 $serialp=$s;
 $serialp.=1;
 $serialu=$s;
 $serialu.=$_POST['cant'];
 $primer_ser=1;
 $parte_serial=$s;
}
else
{switch ($ens)
   {case "PLAN NACER":{$serialu=$serialp='C';break;}
    case "DYAR":{$serialu=$serialp='Y';break;}
    case "DIGITAL STORES":{$serialu=$serialp='S';break;}
    case "COSTANOR":{$serialu=$serialp='Z';break;}
    case "GLOBAL EXPRESS":{$serialu=$serialp='G';break;}
    case "PC HALL":{$serialu=$serialp='P';break;}
   }// fin switch
 list($d,$m,$a)=explode("/",$fecha);
  switch ($a) //calculamos año
  {case 2003:$serialu.='Q';$serialp.='Q';break;
   case 2004:$serialu.='R';$serialp.='R';break;
   case 2005:$serialu.='S';$serialp.='S';break;
   case 2006:$serialu.='T';$serialp.='T';break;
   case 2007:$serialu.='U';$serialp.='U';break;
   case 2008:$serialu.='V';$serialp.='V';break;
  } //fin switch
  $serialp.=$d; //calculamos dia
  $serialu.=$d;
  switch ($m)  //calculamos el mes
  {case '01':$serialu.='CE';$serialp.='CE';break;
   case '02':$serialu.='CF';$serialp.='CF';break;
   case '03':$serialu.='CM';$serialp.='CM';break;
   case '04':$serialu.='AA';$serialp.='AA';break;
   case '05':$serialu.='AM';$serialp.='AM';break;
   case '06':$serialu.='AJ';$serialp.='AJ';break;
   case '07':$serialu.='JJ';$serialp.='JJ';break;
   case '08':$serialu.='JA';$serialp.='JA';break;
   case '09':$serialu.='JS';$serialp.='JS';break;
   case '10':$serialu.='OO';$serialp.='OO';break;
   case '11':$serialu.='ON';$serialp.='ON';break;
   case '12':$serialu.='OD';$serialp.='OD';break;
  }// fin switch
if (($producto!="Computadoras CDR")&&($producto!="Server"))
{$serialu.='OM';$serialp.='OM';}
else
{switch($modelo)
  {case "ENTERPRISE":$serialu.='EN';$serialp.='EN';break;
   case "MATRIX":$serialu.='MA';$serialp.='MA';break;
   case "SERVER":$serialu.='SE';$serialp.='SE';break;
   default:$serialu.='OM';$serialp.='OM';break;
  }// fin switch
}
 $primer=$ing_prim;
 $serialp.=$letra;
 $parte_serial=$serialp; //obtengo primer parte de serial
 $primer_ser=$primer;    //obtengo el primer numero de serial
 $ultimo=($primer+$_POST['cant']-1)%1000; //obtengo la primer maquina
 //if ($_POST['cant']+$temp['nro']>1000) //actualizamos la letra
 // $temp['letra']=chr(ord($temp['letra'])+1);
 $userial=$ultimo;
 $serialu.=$letra;
 //$letra=$temp['letra'];
 if ($primer<100) //concateno valor con los 0 que pueden llegar a faltar
  $serialp.='0';
 if ($primer<10)
  $serialp.='0';
 if ($ultimo<100) //concateno valor con los 0 que pueden llegar a faltar
  $serialu.='0';
 if ($ultimo<10)
  $serialu.='0';
 $serialp.=$primer;
 $serialu.=$ultimo;
}
}//fin funcion gen_serial

?>