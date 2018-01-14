///////////////////////////////////////////////////////////////////////
//     This script was designed by Erik Arvidsson for WebFX          //
//                                                                   //
//     For more info and examples see: http://webfx.eae.net          //
//     or send mail to erik@eae.net                                  //
//                                                                   //
//     Feel free to use this code as lomg as this disclaimer is      //
//     intact.                                                       //
///////////////////////////////////////////////////////////////////////

var checkZIndex = true;

var dragobject = null;
var tx;
var ty;

var ie5 = document.all != null && document.getElementsByTagName != null;

function getReal(el) {
	tmp = el;
	while ((tmp != null) && (tmp.tagName != "BODY")) {
		if ((tmp.className == "moveme") || (tmp.className == "handle")){
			el = tmp;
			return el;
		}
		tmp = tmp.parentElement;
	}
	return el;
}


function moveme_onmousedown() {
	el = getReal(window.event.srcElement)
	
	if (el.className == "moveme" || el.className == "handle") {
		if (el.className == "handle") {
			tmp = el.getAttribute("handlefor");
			if (tmp == null) {
				dragobject = null;
				return;
			}
			else
				dragobject = eval(tmp);
		}
		else 
			dragobject = el;
		
		if (checkZIndex) makeOnTop(dragobject);
		
		ty = window.event.clientY - getTopPos(dragobject);
		tx = window.event.clientX - getLeftPos(dragobject);
		window.event.returnValue = false;
		window.event.cancelBubble = true;
	}
	else {
		dragobject = null;
	}
}

function moveme_onmouseup() {
	el = getReal(window.event.srcElement);
	
	if (el.className == "moveme" || el.className == "handle") {
		temp = el.getAttribute("handlefor");
		tmp=eval(temp);
		tmp.top=(document.body.clientHeight-window.event.clientY)+14;
		//alert(tmp.top);
	}
	if(dragobject) {
		dragobject = null;
	}
}

function moveme_onmousemove() {
	if (dragobject) {
		if (window.event.clientX >= 0 && window.event.clientY >= 0) {
			dragobject.style.left = window.event.clientX - tx;
			dragobject.style.top = window.event.clientY - ty;
		}
		window.event.returnValue = false;
		window.event.cancelBubble = true;
	}
}

function getLeftPos(el) {
	if (ie5) {
		if (el.currentStyle.left == "auto")
			return 0;
		else {
			return parseInt(el.currentStyle.left);
		}	
	}
	else {
		return el.style.pixelLeft;
	}
}

function getTopPos(el) {
	if (ie5) {
		if (el.currentStyle.top == "auto")
			return 0;
		else {
			return parseInt(el.currentStyle.top);
		}
	}
	else {
		return el.style.pixelTop;
	}
}

function makeOnTop(el) {
	var daiz;
	var max = 0;
	var da = document.all;
	
	for (var i=0; i<da.length; i++) {
		daiz = da[i].style.zIndex;
		if (daiz != "" && daiz > max)
			max = daiz;
	}
	
	el.style.zIndex = max + 1;
}

function scroll() {
	var tmp;
	tmp = document.getElementsByTagName("div");
	for (var i=0; i<tmp.length;i++) {
		if (tmp[i].className == "moveme" || tmp[i].className == "handle") {
			//alert (i);
			//alert (tmp[i-1].top);
			tmp[i-1].style.top = (document.body.clientHeight-parseInt(tmp[i-1].top))+document.body.scrollTop;
		}
	}
}

if (document.all) { //This only works in IE4 or better
	document.onmousedown = moveme_onmousedown;
	document.onmouseup = moveme_onmouseup;
	document.onmousemove = moveme_onmousemove;
	window.onscroll = scroll;
}

function ocultar(boton,div) {
	var tmp=document.getElementById(div);
	if (boton.src.indexOf("dropdown2.gif")!=-1) {
		boton.src="../../imagenes/drop2.gif";
		tmp.h=tmp.style.height;
		tmp.style.height=25;
	}
	else {
		tmp.style.height=tmp.h;
		boton.src="../../imagenes/dropdown2.gif";
	}
}

function max(div) {
	var tmp=document.getElementById(div);
	//alert(tmp.style.top);
	tmp.style.height=tmp.hh;
	tmp.style.width=tmp.w;
	tmp.style.left=tmp.l;
	tmp.style.top=(document.body.clientHeight-tmp.tmptop)+document.body.scrollTop;
	tmp.innerHTML=tmp.contenido;
	tmp.style.filter='';
	tmp.top=tmp.tmptop;
}

function mini(boton,div) {
	var tmp=document.getElementById(div);
	tmp.hh=tmp.style.height;
	tmp.w=tmp.style.width;
	tmp.l=tmp.style.left;
	tmp.t=tmp.style.top;
	tmp.style.height=40;
	tmp.style.width=40;
	
	tmp.contenido=tmp.innerHTML;
	var str="<div style='position: absolute;cursor: hand;top: 3;left: 3; width: 35;heigth 35;margin:0;'>";
	str+="<img src='../../imagenes/menu.gif' onClick='max(\""+div+"\");'></div>";
	tmp.innerHTML+=str;
	tmp.style.background='white';
	tmp.style.filter='Alpha(Opacity=80)';
	tmp.style.top=(document.body.clientHeight-50)+document.body.scrollTop;
	tmp.style.left=document.body.clientWidth-50;
	tmp.tmptop=parseInt(tmp.top);
	tmp.top=document.body.clientHeight-(document.body.clientHeight-50);
}

document.write("<style>");
document.write(".moveme		{cursor: move;}");
document.write(".handle		{cursor: move;}");
document.write("</style>");