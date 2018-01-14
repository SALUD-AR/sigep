<?php
echo "<script language='javascript'>
var winW=window.screen.Width;
var valor=(winW*25)/100;
var nombre1;
var titulo1;
function insertar(){
 ventana.document.all.titulo.innerText=titulo1;
 ventana.frames.frame1.location=nombre1;
 }

function abrir_ventana(nombre,titulo){
var winH=window.screen.availHeight;
nombre1=nombre;
titulo1=titulo;
if ((typeof(ventana) == 'undefined') || ventana.closed) {
ventana=window.open('$html_root/modulos/ayuda/TITULOS.htm','ventana_ayuda','width=' + valor + ',height=' + (winH)+ ', left=' + (winW - valor ) +'  ,top=0, scrollbars=0 ');
window.top.resizeBy(-valor,0);
}
else {  ventana.focus();
     }

setTimeout('insertar()',400);
}
</script>";
?>