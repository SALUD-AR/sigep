// funciones de modificacion de los tag select, tambien para su escritura

var o = null;
var isNN = (navigator.appName.indexOf("Netscape")!=-1);
var form_enviado = 0; // Variable para controlar si un formulario ha sido enviado

function activar(nombre,valor) //activa valor del select objeto
{
var objetoc=eval('window.document.form.'+nombre); //obtengo objeto select a cambiar
for (var i=0;i<objetoc.length;i++)
{
if (objetoc.options[i].text==valor)
objetoc.options[i].selected=true;
}
}

function activar_prov()
 {var objeto=eval('window.document.form.proveedor_mother');
  activar('proveedor_video',objeto.options[objeto.selectedIndex].text);
  activar('proveedor_sonido',objeto.options[objeto.selectedIndex].text);
  activar('proveedor_red',objeto.options[objeto.selectedIndex].text);
  activar('proveedor_modem',objeto.options[objeto.selectedIndex].text);
  activar('proveedor_micro',objeto.options[objeto.selectedIndex].text);
  activar('proveedor_mem',objeto.options[objeto.selectedIndex].text);
  activar('proveedor_graba',objeto.options[objeto.selectedIndex].text);
  activar('proveedor_dvd',objeto.options[objeto.selectedIndex].text);
  activar('proveedor_cd',objeto.options[objeto.selectedIndex].text);
  activar('proveedor_hdd',objeto.options[objeto.selectedIndex].text);
 }

function activar_gar()
{var objeto=eval('window.document.form.garantia_mother'); 
 activar('garantia_video',objeto.options[objeto.selectedIndex].text);
 activar('garantia_sonido',objeto.options[objeto.selectedIndex].text);
 activar('garantia_red',objeto.options[objeto.selectedIndex].text);
 activar('garantia_modem',objeto.options[objeto.selectedIndex].text);
 activar('garantia_micro',objeto.options[objeto.selectedIndex].text);
 activar('garantia_mem',objeto.options[objeto.selectedIndex].text);
 activar('garantia_graba',objeto.options[objeto.selectedIndex].text);
 activar('garantia_dvd',objeto.options[objeto.selectedIndex].text);
 activar('garantia_cd',objeto.options[objeto.selectedIndex].text);
 activar('garantia_hdd',objeto.options[objeto.selectedIndex].text);
}



function beginEditing(menu){
//finish();

if(menu[menu.selectedIndex].id == "editable"){
o = new Object();
o.editOption = menu[menu.selectedIndex];
o.editOption.old = o.editOption.text;
o.editOption.text = "_";
menu.blur();
window.focus();
document.onkeypress = keyPressHandler;
document.onkeydown = keyDownHandler;
} // fin if
function keyDownHandler(e){
var keyCode = (isNN)?e.which:event.keyCode;
return (keyCode!=8 || keyPressHandler(e));
} //fin function keydownhandler
function keyPressHandler(e){
var option = o.editOption;
var keyCode = (isNN)?e.which:event.keyCode;
if(keyCode==8 || keyCode==37)
option.text = option.text.substring(0,option.text.length-2)+"_";
else if(keyCode==13){
finish();
}// fin else if
else if(keyCode!=0)
option.text = option.text.substring(0,option.text.length-1) + String.fromCharCode(keyCode) + "_";
return false;
}// fin keypresshandler
function finish(){
if(o!=null){
option = o.editOption;
if(option.text.length > 1)
option.text = option.text.substring(0,option.text.length-1);
else
option.text = option.old;
document.onkeypress = null;
document.onkeydown = null;
o = null;
}// fin if
} //fin function finish
} //fin function begineditingthis 

function getvalue()
{
//alert("hi");
//editable =this ;
alert(this.options[this.selectedIndex].text);
//document.form.elements[editable].options[document.form.elements[editable].selectedIndex].text);
document.form.txtoption.value = this.options[this.selectedIndex].text;
//document.form.this.options[document.form.this.selectedIndex].text;
}

//MORE_ROWS BY GACZ
//aumenta dinamicamente el numero de lineas de un @textarea al tipear enter
//tiene un maximo @maxrows (0 sin limite)
//llamar en el evento onkeypress
function more_rows(textarea,maxrows)
{
if (typeof(maxrows)=='undefined' || isNaN(maxrows))
	maxrows=0;

if (event.keyCode == 13 ) 
{
	if (maxrows==0 || textarea.rows < maxrows)
	textarea.rows++;
}

}

//funciones para busqueda abrebiada utilizando teclas en la lista que muestra los clientes.
var digitos=15 //cantidad de digitos buscados
var puntero=0
var buffer=new Array(digitos) //declaración del array Buffer
var cadena=""

function buscar_op(obj){
   var letra = String.fromCharCode(event.keyCode)
   if(puntero >= digitos){
       cadena="";
       puntero=0;
    }
   //si se presiona la tecla ENTER, borro el array de teclas presionadas y salto a otro objeto...
   if (event.keyCode == 13){
       borrar_buffer();
      // if(objfoco!=0) objfoco.focus(); //evita foco a otro objeto si objfoco=0
    }
   //sino busco la cadena tipeada dentro del combo...
   else{
       buffer[puntero]=letra;
       //guardo en la posicion puntero la letra tipeada
       cadena=cadena+buffer[puntero]; //armo una cadena con los datos que van ingresando al array
       puntero++;

       //barro todas las opciones que contiene el combo y las comparo la cadena...
       for (var opcombo=0;opcombo < obj.length;opcombo++){
          if(obj[opcombo].text.substr(0,puntero).toLowerCase()==cadena.toLowerCase()){
          obj.selectedIndex=opcombo;break;
          }
       }
    }
   event.returnValue = false; //invalida la acción de pulsado de tecla para evitar busqueda del primer caracter
}

function borrar_buffer(){
   //inicializa la cadena buscada
    cadena="";
    puntero=0;
}

function submit_form(boton){
	var b=eval('boton.form.'+boton.name);
	alert(b.value);
	
//	if (form_enviado == 0) {
//		if (boton) {
			boton.disabled=true;
//		}
		//boton.form.submit();
//		form_enviado = 1;
//	}
}

function Mostrar(obj) {
	eval("document.all." + obj + ".style.display='block'");
}

function Ocultar(obj) {
	eval("document.all." + obj + ".style.display='none'");
}

/**********************************************************
Esta funcion controla que el objeto sea un número valido.
Sino lo es, envia un alerta y vacia el campo.
-Retorna 1 si se encontro error y 0 sino.
***********************************************************/
function control_numero(objeto,nombre_campo)
{var fallo=0;
 var valor=objeto.value;
 if(isNaN(valor))
 {fallo=1;
  alert('El valor ingresado en el campo "'+nombre_campo+'" debe ser un número válido.');
  objeto.value='';
  return 1;
 } 
 return 0;
}	

/******************************************
Da formato de dinero al valor
(REQUIERE QUE SE INCLUYA LA LIBRERIA 
NumberFormat150.js en el lib,
para poder funcionar)
*******************************************/

function formato_money(valor){

var numero=new  NumberFormat();

 numero.setNumber(parseFloat(valor));
 numero.setInputDecimal(numero.PERIOD);
 numero.setCurrency(false);
 numero.setSeparators(true, numero.PERIOD)
 return numero.toFormatted();
}


/******************************************
Da formato de Base de Datos(BD) al valor 
(REQUIERE QUE SE INCLUYA LA LIBRERIA 
NumberFormat150.js en el lib,
para poder funcionar)
*******************************************/

function formato_BD(valor){

var numero=new  NumberFormat();

 numero.setNumber(parseFloat(valor));
 numero.setCommas(false);
 numero.setCurrency(false);
 return numero.toFormatted();

}

function buscar_elemento(valor,arreglo)
{var i;
 for(i=0;((i<arreglo.length)&&(valor!=arreglo[i]));i++);
 if (i==arreglo.length)
  return false;
 else
  return true;

}

/*****************************************
Funcion filtar_teclas permite ingresar por teclado
solo lo que se le pasa en el parametro (goods), esta
necesita de la función getkey
******************************************/
function getkey(e)
{
if (window.event)
   return window.event.keyCode;
else if (e)
   return e.which;
else
   return null;
}

function filtrar_teclas(e, goods, invert)
{
var key, keychar;
key = getkey(e);
if (key == null) return false;

// get character
keychar = String.fromCharCode(key);
keychar = keychar.toLowerCase();
goods = goods.toLowerCase();

// check goodkeys
//si invert==true checkea que las teclas que se pasaron no aparezcan
//de lo contrario solo deja imprimir las teclas que se pasaron
if (arguments.length==3 && invert)
{
	if (goods.indexOf(keychar) == -1)
		return true;
}
else if (goods.indexOf(keychar) != -1)
	return true;

// control keys
if ( key==null || key==0 || key==8 || key==9 || key==13 || key==27 )
   return true;

// else return false
return false;


}

//compara fecha (fechaHoy)hoy con fecha ingresada (fecha_ing_eg)
//si fechaHoy >= que la fecha_ing_eg entonces devuelve true 
//nombre es el nombre del input que contiene la fecha en formato dd/mm/yyyy
function es_mayor(nombre) {

//en estas líneas creamos las fechas 
miFechaActual = new Date(); 
var fecha_ing_eg=eval("document.all." + nombre + ".value");  //dd/mm/yyyy
var fechas=fecha_ing_eg.split("/");
var miFechaPasada = new Date(fechas[2],parseInt(fechas[1])-1,fechas[0]); 

//extraemos el día mes y año 
dia = parseInt(miFechaActual.getDate()); 
mes = parseInt(parseInt(miFechaActual.getMonth()) + 1); 
anio = parseInt(miFechaActual.getFullYear()); 

dia_nuevo = parseInt(miFechaPasada.getDate()); 
mes_nuevo = parseInt(parseInt(miFechaPasada.getMonth()) + 1); 
anio_nuevo = parseInt(miFechaPasada.getFullYear()); 

if (anio_nuevo < anio) {
    return false;
}
if ((anio_nuevo==anio) && (mes_nuevo < mes)) {
   return false;
}
if ((mes_nuevo==mes) && dia_nuevo < dia) {
  return false;
}
return true;
}


//TRIM_QUOTES() BY GACZ
//elimina comillas dobles y simples del string texto
//retorna el nuevo string sin comillas
function trim_quotes(texto)
{
	while (texto.match(/'|"/))
		texto=texto.replace(/'|"/,'');
	
	return texto;
}

//fnNoQuotes() BY GACZ
//sirve para evitar que se ingresen comillas en un objeto del tipo input o textarea	
//@obj es la referencia al objeto en cuestion
function fnNoQuotes(obj)
{
	obj.onpaste=function () {event.returnValue=false; obj.value=trim_quotes(window.clipboardData.getData('Text'));};
	obj.onkeypress=function () {return filtrar_teclas(event,"'\"",true);};
}
	
	
//funcion que se le pasa dos checked
//el primero es el checked que sirve para elegir los check (segundo parametro)}
//estilo como funcionan los mail
function seleccionar_todos(elegir,check){
var valor;

if (typeof(check)!='undefined'){
             if(elegir.checked==true){
                      valor=true;
                      }
                      else{
                      valor=false;
                       }

             //le coloco el valor que le correspone a todos


            if (typeof(check.length)!='undefined')
                {
                for(i=0;i<check.length;i++){
                        check[i].checked=valor;
                        }//del for
                }
                else
                 {
                 check.checked=valor;
                 }
 }//del primer if
}//de la funcion

//funciones para el menu contextual


var menuskin = "skin1"; // skin0, or skin1
var display_url = 0; // Show URLs in status bar?
function showmenuie5() {


if (event.ctrlKey==0) {

var rightedge = document.body.clientWidth-event.clientX;
var bottomedge = document.body.clientHeight-event.clientY;
if (rightedge < ie5menu.offsetWidth)
ie5menu.style.left = document.body.scrollLeft + event.clientX - ie5menu.offsetWidth;
else
ie5menu.style.left = document.body.scrollLeft + event.clientX;
if (bottomedge < ie5menu.offsetHeight)
ie5menu.style.top = document.body.scrollTop + event.clientY - ie5menu.offsetHeight;
else
ie5menu.style.top = document.body.scrollTop + event.clientY;
ie5menu.style.visibility = "visible";
return false;
}
else {
ie5menu.style.visibility = "hidden";
}
}

function hidemenuie5() {
ie5menu.style.visibility = "hidden";
}

function highlightie5() {
if (event.srcElement.className == "menuitems") {
event.srcElement.style.backgroundColor = "highlight";
event.srcElement.style.color = "white";
if (display_url)
window.status = event.srcElement.url;
  }
}

function lowlightie5() {
if (event.srcElement.className == "menuitems") {
event.srcElement.style.backgroundColor = "";
event.srcElement.style.color = "black";
window.status = "";
  }
}

function jumptoie5() {
if (event.srcElement.className == "menuitems") {
if (event.srcElement.getAttribute("target") != null)
window.open(event.srcElement.url, event.srcElement.getAttribute("target"));
else
window.location = event.srcElement.url;
  }
}
//fin de las funciones del menu

// Simulador de Blink por colores
function titilar(){
	if (document.all) {
		var blink = document.all.tags("BLINK");
		if (blink.length==0) return;
		for (var i=0; i < blink.length; i++) {
			if (blink[i].tipo=="blink") 
				blink[i].style.visibility = blink[i].style.visibility == "" ? "hidden" : "";
			else {
				if (blink[i].tipo=="color" || !blink[i].tipo)
					blink[i].style.color = blink[i].style.color=="" ? "red" : "";
			}
		}
		setTimeout("titilar()",500);
	}
}

function ImprimirDivs(arreglo_nombres,header) {
	var win=window.open("about:blank", "Imprimir", "left=0, top=0, toolbar=no, scrollbars=yes");
	var strHtml;
	if (typeof(header)!='undefined') {
		strHtml = header;
	}
	else {
		strHtml="<html><head></head><body>";
	}
	for (var i=0; i<arreglo_nombres.length; i++) {
		var el=document.getElementById(arreglo_nombres[i]);
		strHtml += el.innerHTML;
	}
	strHtml += "</body></html>";
	win.document.write(strHtml);
	win.document.close();
	win.print();
	win.close();
}

function redondear_numero(valor) {
	var numberField = parseFloat(valor); // Field where the number appears
	var rlength = 2; // The number of decimal places to round to
	var newnumber = Math.round(numberField*Math.pow(10,rlength))/Math.pow(10,rlength);
	return newnumber;
}

/**
 * Alert window
 * 
 * @param {type} message
 * @param {type} callback
 * @returns {undefined}
 */
BootstrapDialog.alert = function(message, callback) {
    new BootstrapDialog({
        title: '<b>Informaci&oacute;n</b>',
        message: message,
        data: {
            'callback': callback
        },
        closable: false,
        buttons: [{
                label: 'Aceptar',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(true);
                    dialog.close();
                }
            }]
    }).open();
};

/**
 * Confirm window
 * 
 * @param {type} message
 * @param {type} callback
 * @returns {undefined}
 */
BootstrapDialog.confirm = function(message, callback) {
    new BootstrapDialog({
        title: '<b>Confirmaci&oacute;n</b>',
        message: message,
        closable: false,
        data: {
            'callback': callback
        },
        buttons: [{
                label: 'Cancelar',
                action: function(dialog) {
                    typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);
                    dialog.close();
                }
            }, {
                label: 'Aceptar',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(true);
                    dialog.close();
                }
            }]
    }).open();
};

function isDNI(value) {
  var er = /^[0-9]+$/;
  return er.test(value);
}

/*
    -9007199254740990 to 9007199254740990
*/

function isInt(n) {
    return +n === n && !(n % 1);
}

/*
    -128 to 127
*/

function isInt8(n) {
    return +n === n && !(n % 1) && n < 0x80 && n >= -0x80;
}

/*
    -32768 to 32767
*/

function isInt16(n) {
    return +n === n && !(n % 1) && n < 0x8000 && n >= -0x8000;
}

/*
    -2147483648 to 2147483647
*/

function isInt32(n) {
    return +n === n && !(n % 1) && n < 0x80000000 && n >= -0x80000000;
}

/*
    0 to 9007199254740990
*/

function isUint(n) {
    return +n === n && !(n % 1) && n >= 0;
}

/*
    0 to 255
*/

function isUint8(n) {
    return +n === n && !(n % 1) && n < 0x100 && n >= 0;
}

/*
    0 to 65535
*/

function isUint16(n) {
    return +n === n && !(n % 1) && n < 0x10000 && n >= 0;
}

/*
    0 to 4294967295
*/

function isUint32(n) {
    return +n === n && !(n % 1) && n < 0x100000000 && n >= 0;
}

/*
    Any number including Infinity and -Infinity but not NaN
*/

function isFloat(n) {
    return +n === n;
}

/*
    Any number from -3.4028234e+38 to 3.4028234e+38 (Single-precision floating-point format)
*/

function isFloat32(n) {
    return +n === n && Math.abs(n) <= 3.4028234e+38;
}

/*
    Any number excluding Infinity and -Infinity and NaN (Number.MAX_VALUE = 1.7976931348623157e+308)
*/

function isFloat64(n) {
    return +n === n && Math.abs(n) <= 1.7976931348623157e+308;
}

function adjustModalMaxHeightAndPosition(){
  $('.modal').each(function(){
    if($(this).hasClass('in') === false){
      $(this).show();
    }
    var contentHeight = $(window).height() - 60;
    var headerHeight = $(this).find('.modal-header').outerHeight() || 2;
    var footerHeight = $(this).find('.modal-footer').outerHeight() || 2;
    // console.log('c='+contentHeight+' h='+headerHeight+' f='+footerHeight);
    $(this).find('.modal-content').css({
      'max-height': function () {
        return contentHeight;
      }
    });

    $(this).find('.modal-body').css({
      'max-height': function () {
        return contentHeight - (headerHeight + footerHeight);
      }
    });

    $(this).find('.modal-dialog').addClass('modal-dialog-center').css({
      'margin-top': function () {
        return -($(this).outerHeight() / 2);
      },
      'margin-left': function () {
        return -($(this).outerWidth() / 2);
      }
    });
    if($(this).hasClass('in') === false){
      $(this).hide();
    }
  });
}

function renderModal(selector, html, options) {
  var parent = "body",
      $this = $(parent).find(selector);

  options = options || {};
  options.width = options.width || 'auto';

  if ($this.length == 0) {
    var selectorArr = selector.split("#");
    var $wrapper = $('<div class="modal fade" id="' + selectorArr[selectorArr.length-1] + '"></div>').append(html);
    $this = $wrapper.appendTo(parent);
    adjustModalMaxHeightAndPosition();
    $this.modal();
  } else {
    adjustModalMaxHeightAndPosition();
    $this.html(html).modal();
  }
}

function modal_top(selector) {
  window.top.renderModal(selector, $(selector).html());
}

$(document).ready(function() {
  if ($(window).height() >= 320){
    $(window).resize(adjustModalMaxHeightAndPosition).trigger("resize");
  }
});
