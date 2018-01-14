

//----------------------------------------------------------------------------
//funcion que quita los espacios al principio y al final
function trim(inputString) {
   // Removes leading and trailing spaces from the passed string. Also removes
   // consecutive spaces and replaces it with one space. If something besides
   // a string is passed in (null, custom object, etc.) then return the input.
   if (typeof inputString != "string") { return inputString; }
   var retValue = inputString;
   var ch = retValue.substring(0, 1);
   while (ch == " ") { // Check for spaces at the beginning of the string
      retValue = retValue.substring(1, retValue.length);
      ch = retValue.substring(0, 1);
   }
   ch = retValue.substring(retValue.length-1, retValue.length);
   while (ch == " ") { // Check for spaces at the end of the string
      retValue = retValue.substring(0, retValue.length-1);
      ch = retValue.substring(retValue.length-1, retValue.length);
   }
   while (retValue.indexOf("  ") != -1) { // Note that there are two spaces in the string - look for multiple spaces within the string
      retValue = retValue.substring(0, retValue.indexOf("  ")) + retValue.substring(retValue.indexOf("  ")+1, retValue.length); // Again, there are two spaces in each of the strings
   }
   return retValue; // Return the trimmed string back to the user
} // Ends the "trim" function
//----------------------------------------------------------------------------
//funcion add_option
//añade una opcion a uns combo select
//parametros: 
//@select es el elemento al cual se le añadira una opcion
//@text es el valor que se mostrara
//@value es el valor asociado a text
function add_option(select,value,text)
{
 select.length++;
 select.options[select.length-1].text=text;
 select.options[select.length-1].value=value;
}
//funcion del_option
//elimina una opcion en un combo select
//parametros: 
//@select es el elemento al cual se quitara una opcion
//@index indice de la opcion a remover
function del_option(select,index)
{
 select.options[index] = null;
}

//funcion move_options
//mueve una o mas opciones de un select a otro
//parametros:
//@select1 objeto fuente
//@select2 objeto destino
//@selected booleano que indica si se deben dejar seleccionadas las opciones añadidas
//@return retorna la cantidad de opciones movidas
function move_options(select2,select1,selected)
{
	var total=0;
	if (arguments.length==2)
		selected=false;
	for (var i=0; i < select1.length; )
	{
		if (select1.options[i].selected)
		{
			add_option(select2,select1.options[i].value,select1.options[i].text);
			select2.options[select2.length-1].selected=selected;
			del_option(select1,i);
			total++;
		}
		else
			i++;
	}
	return total;
}

//----------------------------------------------------------------------------

function ylib_Browser()
{
	d=document;
	this.agt=navigator.userAgent.toLowerCase();
	this.major = parseInt(navigator.appVersion);
	this.dom=(d.getElementById)?1:0;
	this.ns=(d.layers);
	this.ns4up=(this.ns && this.major >=4);
	this.ns6=(this.dom&&navigator.appName=="Netscape");
	this.op=(window.opera? 1:0);
	this.ie=(d.all);
	this.ie4=(d.all&&!this.dom)?1:0;
	this.ie4up=(this.ie && this.major >= 4);
	this.ie5=(d.all&&this.dom);
	this.win=((this.agt.indexOf("win")!=-1) || (this.agt.indexOf("16bit")!=-1));
	this.mac=(this.agt.indexOf("mac")!=-1);
};

var oBw = new ylib_Browser();

function ylib_getObj(id,d)
{
	var i,x;  if(!d) d=document; 
	if(!(x=d[id])&&d.all) x=d.all[id]; 
	for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][id];
	for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=ylib_getObj(id,d.layers[i].document);
	if(!x && document.getElementById) x=document.getElementById(id); 
	return x;
};

function ylib_getH(o) { return (oBw.ns)?((o.height)?o.height:o.clip.height):((oBw.op&&typeof o.style.pixelHeight!='undefined')?o.style.pixelHeight:o.offsetHeight); };
function ylib_setH(o,h) { if(o.clip) o.clip.height=h; else if(oBw.op && typeof o.style.pixelHeight != 'undefined') o.style.pixelHeight=h; else o.style.height=h; };
function ylib_getW(o) { return (oBw.ns)?((o.width)?o.width:o.clip.width):((oBw.op&&typeof o.style.pixelWidth!='undefined')?w=o.style.pixelWidth:o.offsetWidth); };
function ylib_setW(o,w) { if(o.clip) o.clip.width=w; else if(oBw.op && typeof o.style.pixelWidth != 'undefined') o.style.pixelWidth=w; else o.style.width=w; };
function ylib_getX(o) { return (oBw.ns)?o.left:((o.style.pixelLeft)?o.style.pixelLeft:o.offsetLeft); };
function ylib_setX(o,x) { if(oBw.ns) o.left=x; else if(typeof o.style.pixelLeft != 'undefined') o.style.pixelLeft=x; else o.style.left=x; };
function ylib_getY(o) { return (oBw.ns)?o.top:((o.style.pixelTop)?o.style.pixelTop:o.offsetTop); };
function ylib_setY(o,y) { if(oBw.ns) o.top=y; else if(typeof o.style.pixelTop != 'undefined') o.style.pixelTop=y; else o.style.top=y; };
function ylib_getPageX(o) { var x=0; if(oBw.ns) x=o.pageX; else { while(eval(o)) { x+=o.offsetLeft; o=o.offsetParent; } } return x; };
function ylib_getPageY(o) { var y=0; if(oBw.ns) y=o.pageY; else { while(eval(o)) { y+=o.offsetTop; o=o.offsetParent; } } return y; };
function ylib_getZ(o) { return (oBw.ns)?o.zIndex:o.style.zIndex; };
function ylib_moveTo(o,x,y) { ylib_setX(o,x);ylib_setY(o,y); };
function ylib_moveBy(o,x,y) { ylib_setX(o,ylib_getPageX(o)+x);ylib_setY(o,ylib_getPageY(o)+y); };
function ylib_setZ(o,z) { if(oBw.ns)o.zIndex=z;else o.style.zIndex=z; };
function ylib_show(o,disp) { (oBw.ns)? '':(!disp)? o.style.display="inline":o.style.display=disp; (oBw.ns)? o.visibility='show':o.style.visibility='visible'; };
function ylib_hide(o,disp) { (oBw.ns)? '':(arguments.length!=2)? o.style.display="none":o.style.display=disp; (oBw.ns)? o.visibility='hide':o.style.visibility='hidden'; };
function ylib_setStyle(o,s,v) { if(oBw.ie5||oBw.dom) eval("o.style."+s+" = '" + v +"'"); };
function ylib_getStyle(o,s) { if(oBw.ie5||oBw.dom) return eval("o.style."+s); };
function ylib_addEvt(o,e,f,c){ if(o.addEventListener)o.addEventListener(e,f,c);else if(o.attachEvent)o.attachEvent("on"+e,f);else eval("o.on"+e+"="+f) };
function ylib_writeHTML(o,h) { if(oBw.ns){var doc=o.document;doc.write(h);doc.close();return false;} if(o.innerHTML)o.innerHTML=h; };

function ylib_insertHTML(o,h,w)
{
	if(oBw.op) return;
	if(o.insertAdjacentHTML)
	{ 
		o.insertAdjacentHTML(w,h);
		return;
	}
	if(oBw.ns)
	{
		ylib_writeHTML(o,h);
		return;
	}
	var r = o.ownerDocument.createRange();
	r.setStartBefore(o);
	var frag = r.createContextualFragment(h);
	ylib_insertObj(o,w,frag);
};

function ylib_insertObj(o,w,node)
{
	switch(w)
	{
		case 'beforeBegin':
			o.parentNode.insertBefore(node,o);
		break;

		case 'afterBegin':
			o.insertBefore(node,o.firstChild);
		break;

		case 'beforeEnd':
			o.appendChild(node);
		break;

		case 'afterEnd':
			if (o.nextSibling) o.parentNode.insertBefore(node,o.nextSibling);
			else o.parentNode.appendChild(node);
		break;
	}
};

//------------------------------------------------------------------------------------------------------
/* Buttons */
var g_oMenu;

function Menu_Click(p_oEvent)
{
	var oEvent = p_oEvent ? p_oEvent : window.event;
	var oSender = p_oEvent ? oEvent.target : oEvent.srcElement;

	if(p_oEvent) oEvent.stopPropagation();
	else oEvent.cancelBubble = true;
	
	this.Sender = oSender;
	this.Event = oEvent;
	
	if(typeof this.ClickHandler != 'undefined') this.ClickHandler();
};

function Menu_MouseOver(p_oEvent)
{
	var oEvent = p_oEvent ? p_oEvent : window.event;
	var oSender = p_oEvent ? oEvent.target : oEvent.srcElement;
	
	if(oSender.tagName == 'LI') oSender.className = 'hover';
	else if(oSender.tagName == 'A') oSender.parentNode.className = 'hover';
	else return false;
};

function Menu_MouseOut(p_oEvent)
{
	var oEvent = p_oEvent ? p_oEvent : window.event;
	var oSender = p_oEvent ? oEvent.target : oEvent.srcElement;
	
	if(oSender.tagName == 'LI') oSender.className = '';
	else if(oSender.tagName == 'A') oSender.parentNode.className = '';
	else return false;	
};

function Button_Click(p_oEvent)
{
	var oEvent = p_oEvent ? p_oEvent : window.event;
	var oSender = p_oEvent ? oEvent.target : oEvent.srcElement;

	if(p_oEvent) oEvent.stopPropagation();
	else oEvent.cancelBubble = true;

	this.Event = oEvent;
	this.Sender = oSender;

	HideMenu();
	this.Menu.Button = this;
	g_oMenu = this.Menu;

	if(typeof this.ClickHandler != 'undefined') 
	 this.ClickHandler();
	else 
	 g_oMenu.Show();
	
	document.onclick = Document_Click;
};

function ButtonMenu(p_sMenuId, p_oClickHandler)
{
	var oMenu = document.getElementById(p_sMenuId);

	if(oMenu)
	{
		if(typeof p_oClickHandler != 'undefined') oMenu.ClickHandler = p_oClickHandler;
			
		oMenu.Show = function () { 
			if(document.all) this.style.width = this.offsetWidth+'px';
			this.style.top = ylib_getPageY(this.Button)+this.Button.offsetHeight+'px';
			this.style.left = ylib_getPageX(this.Button)+'px';
			this.style.visibility = 'visible'; 
		};
			
		oMenu.onclick = Menu_Click;
		
		if(document.all)
		{
			oMenu.onmouseover = Menu_MouseOver;
			oMenu.onmouseout = Menu_MouseOut;
		}

		return oMenu;
	}
	else return false;
};

function Button()
{
	//var oButton = document.getElementById(p_sButtonId);
	var oButton = window.event.srcElement;//el que genero el evento
	if(oButton)
	{
		oButton.onclick = Button_Click;
		return oButton;
	}
	else return false;
};

function MenuButton(p_oButtonClickHandler,p_sMenuId, p_oMenuClickHandler)
{
	var oEvent =  window.event;
	var oSender = oEvent.srcElement;
	var oButton = new Button();
	if(oButton)
	{
		oButton.ClickHandler = p_oButtonClickHandler;	
		oButton.Menu = new ButtonMenu(p_sMenuId, p_oMenuClickHandler);
		oSender.onclick();
	}
};


function HideMenu()
{
	if(typeof g_oMenu != 'undefined' && g_oMenu)
	{
		g_oMenu.style.visibility = 'hidden';
		g_oMenu = null;
		document.onclick = null;
		window.onresize = null;
	}
	else return;
};

function Document_Click()
{
	HideMenu();
};


function Window_Resize()
{
	g_oMenu.style.left = ((ylib_getPageX(g_oMenu.Button)+g_oMenu.Button.offsetWidth)-g_oMenu.Button.Menu.offsetWidth)+'px';
}

function Show()
{
	g_oMenu.style.visibility = 'visible';

	if(!this.Configured)
	{
		if(g_oMenu.offsetHeight > 250)
		{
			g_oMenu.style.width = g_oMenu.offsetWidth+20+"px";
			g_oMenu.className += ' overflow';
		}
		else g_oMenu.style.width = g_oMenu.offsetWidth+"px";

		document.onclick = Document_Click;
		window.onresize = Window_Resize;
		this.Configured = true;
	}

	g_oMenu.style.top = (ylib_getPageY(this) + this.offsetHeight)+'px';
	g_oMenu.style.left = ((ylib_getPageX(this)+this.offsetWidth)-this.Menu.offsetWidth)+'px';
};


//--------------------------------------------------------------------------------------------
//parametros
//@menuId  String con el ID del div que contiene el menu
//@functionName Nombre de la funcion que se ejecutara para cada opcion del menu
function show_menu()
{
	var nArguments = arguments.length;
	
	//si solo se pasa el ID del menu
	//cada LI debe tener su onclick propio
	function __show_menu_OneArgument(menuId)
	{
		MenuButton(Show,menuId,null);
		
	};
	function __show_menu_TwoArguments(menuId,functionName)
	{
		MenuButton(Show,menuId,functionName);
	};
	
	if (nArguments==1)
		return __show_menu_OneArgument(arguments[0]);
	else if (nArguments==2)
		return __show_menu_TwoArguments(arguments[0],arguments[1]);
	else
		return null;
}

//alternar_color:
//cambia el color de fondo de un objeto 
//y lo vuelve al color original en la proxima invocacion
//@obj es el objeto a cambiar el color
function alternar_color(obj,color_nuevo) {
		color_nuevo=color_nuevo.toLowerCase();
		if (typeof(obj.originalBgcolor)=='undefined')
			obj.originalBgcolor=obj.style.backgroundColor;
		if (obj.style.backgroundColor != color_nuevo)
			obj.style.backgroundColor = color_nuevo;
		else
			obj.style.backgroundColor = obj.originalBgcolor;
	}

//-->

//----------------------------------------------------------------------------
//----------------------------------------------------------------------------

