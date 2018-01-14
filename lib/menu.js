

///////////////////////////////////////////////////////////////////////
//     This menu was designed by Erik Arvidsson for WebFX            //
//                                                                   //
//     For more info and examples see: http://webfx.eae.net          //
//     or send mail to erik@eae.net                                  //
//                                                                   //
//     Feel free to use this code as lomg as this disclaimer is      //
//     intact.                                                       //
///////////////////////////////////////////////////////////////////////

var activeMenu = null;
var activeSub = null;
var tempEl;
var t;
var hideWindowedControls = true;
var timeout_cerrar;
var timeout_tiempo = 750;	// Tiempo en milisegundos para cerrar el menu

var ie5 = (document.getElementsByTagName != null);

////////////////////////////////////////////////////////
//If you wan't different colors than default overload these functions...
function menuItemHighlight(el) {
		el.style.background = "highlight";
        el.style.color = "highlighttext";
}

function menuItemNormal(el) {
		el.style.background = "";
		el.style.color = "";
}

function raiseButton(el) {
		el.style.borderTop ="1 solid buttonhighlight";
		el.style.borderLeft ="1 solid buttonhighlight";
		el.style.borderBottom ="1 solid buttonshadow";
		el.style.borderRight ="1 solid buttonshadow";
		el.style.padding ="1";
		el.style.paddingLeft = "7";
		el.style.paddingRight = "7";
}

function normalButton(el) {
//        el.style.border = "1 solid buttonface";
		el.style.border = "0";
		el.style.padding ="1";
		el.style.paddingLeft = "7";
		el.style.paddingRight = "7";
}

function pressedButton(el) {
		el.style.borderTop ="1 solid buttonshadow";
		el.style.paddingTop = "2";
		el.style.borderLeft ="1 solid buttonshadow";
		el.style.paddingLeft = "8";
		el.style.borderBottom ="1 solid buttonhighlight";
		el.style.paddingBottom= "0";
		el.style.borderRight = "1 solid buttonhighlight";
		el.style.paddingRight = "6";
}
//...untill here
////////////////////////////////////////////////////////


function cleanUpMenuBar() {
		for (i=0; i <menu.rows.length; i++) {
                for (j=0; j <menu.rows(i).cells.length; j++) {
						if (menu.rows(i).cells(j).className == "root") {
                                normalButton(menu.rows(i).cells(j));
                        }
                }
        }
        showWindowedObjects(true);
			 parent.document.all.frame2.style.zIndex = 2;

}

function getMenuItem(el) {
		temp = el;
		while ((temp!=null) && (temp.tagName!="TABLE") && (temp.id!="ItemActual") && (temp.id!="menubar") && (temp.id!="menu") && (temp.id!="handle")) {
				if ((temp.tagName=="TR") || (temp.className=="root"))
						el = temp;
				temp = temp.parentElement;
		}
		return el;
}

function getSub(el) {
        temp = el;
		while ((temp!=null) && (temp.className != "sub")) {
                if (temp.tagName=="TABLE")
                        el = temp;
                temp = temp.parentElement;
        }
        return el;
}

function menuClick() {
		if (event.srcElement == null)
			return;
		var el=getMenuItem(event.srcElement);
		if ((el.className != "disabled") && (el.id != "menubar")){
				if (el.className == "root") {
					if (activeMenu) {
								raiseButton(el);
								showWindowedObjects(true);
						}
						else
								pressedButton(el);
						if (el.href)
							window.open(el.href,"_top");
						else
							toggleMenu(el);
				}
				else if (el.href) {
						cleanUpMenuBar();
						if (activeMenu)
								toggleMenu(activeMenu.parentElement);
						if (el.target)
								window.open(el.href, el.target);
						else if (document.all.tags("BASE").item(0) != null)
								window.open(el.href, document.all.tags("BASE").item(0).target);
						else
								window.open(el.href,"_top");
				}
		}
		window.event.cancelBubble = true;
}

////////////////////////////////////////////////////////
// Used to hide the menu when clicked elsewhere
function Restore() {
		if (activeMenu) {
			toggleMenu(activeMenu.parentElement);
			cleanUpMenuBar();
		}
		parent.document.all.frame2.style.zIndex = 2;
}

document.onclick=Restore;
////////////////////////////////////////////////////////

function menuOver() {
		if (event.toElement == null) {
			return;
		}
		if (event.fromElement == null) {
			pressedButton(getMenuItem(event.toElement));
//			toggleMenu(getMenuItem(event.toElement));
			return;
		}
		if (event.fromElement == event.toElement) {
			return;
		}
		clearTimeout(timeout_cerrar);
		var fromEl = getMenuItem(event.fromElement);
		var toEl = getMenuItem(event.toElement);
		if (fromEl == toEl)
				return;
		if ((toEl.className != "disabled") && (toEl.id != "menubar")){
				if (toEl.title)
						window.status = toEl.title;
				if (toEl.className == "root") {
						if (activeMenu) {
								if (toEl.menu != activeMenu) {
										cleanUpMenuBar();
										pressedButton(toEl);
										toggleMenu(toEl);
								}
						}
						else {
										raiseButton(toEl);
										pressedButton(toEl);
										//toggleMenu(toEl);
						}
				}
				else {
						if ((fromEl != toEl) && (toEl.tagName != "TABLE")){
								cleanup(toEl.parentElement.parentElement, false);
								menuItemHighlight(toEl);
								toEl.parentElement.parentElement.activeItem = toEl;
								if (toEl.className == "sub")
										showSubMenu(toEl,true);
						}
						//else
							//Restore();

				}
		}
		//else
			//Restore();
}



function menuOut() {
		if (event.fromElement == null) {
			return;
		}
		if (event.toElement == null) {
			normalButton(getMenuItem(event.fromElement));
			return;
		}
		if (event.fromElement == event.toElement) {
			return;
		}
		if (!((event.toElement.id) || (event.toElement.className))) {
			clearTimeout(timeout_cerrar);
			timeout_cerrar = setTimeout("Restore()",timeout_tiempo);
		}
/*		msg = "from:\n";
		msg += "class="+event.fromElement.className+"\n";
		msg += "id="+event.fromElement.id+"\n";
		msg += "to:\n";
		msg += "class="+event.toElement.className+"\n";
		msg += "id="+event.toElement.id+"\n";
		alert(msg);
*/
		var fromEl = getMenuItem(event.fromElement);
		var toEl = getMenuItem(event.toElement);
		if (fromEl == toEl) {
			return;
		}
		window.status = "";
		if (fromEl.className == "root"){
				if (activeMenu) {
						if (fromEl.menu != activeMenu)
								normalButton(fromEl);
				}
				else
						normalButton(fromEl);
		}
		else {
				if  ((fromEl.className != "disabled") && (fromEl.id != "menubar")){
						if ((fromEl.className == "sub") && (getSub(toEl) == fromEl.subMenu) || (fromEl.subMenu == toEl.parentElement.parentElement))
								return;
						else if ((fromEl.className == "sub")){
								cleanup(fromEl.subMenu, true);
								menuItemNormal(fromEl);
						}
						else if ((fromEl != toEl) && (fromEl.tagName != "TABLE"))
								menuItemNormal(fromEl);
				}
		}
}



function toggleMenu(el) {
		if (el.menu == null)
				el.menu = getChildren(el);
		if (el.menu == activeMenu) {
				if (activeSub)
						menuItemNormal(activeSub.parentElement.parentElement);
				cleanup(el.menu,true);
				activeMenu = null;
				activeSub = null;
//                showWindowedObjects(true);
		}
		else {
				if (activeMenu) {
						cleanup(activeMenu,true);
						hideMenu(activeMenu);
				}

				activeMenu = el.menu;

				if (typeof(activeMenu) != 'undefined') {
					var tPos = topPos(el.menu) + menu.offsetHeight;

					if ((document.body.offsetHeight - tPos) >= el.menu.offsetHeight) {
                        el.menu.style.pixelTop = (ie5) ? el.offsetHeight + 1
                                                       : menu.offsetHeight - el.offsetTop - 2;
                        dir = 2;
                    }
                    else {
                        el.menu.style.pixelTop = (ie5) ? el.offsetTop - el.menu.offsetHeight - 1
                                                       : el.offsetTop - el.menu.offsetHeight + 2;
                        dir = 8;
                    }

                    el.menu.style.pixelLeft = (ie5) ? el.offsetLeft - 2 : el.offsetLeft;
                    show(el.menu, dir);
                    showWindowedObjects(false);
					parent.document.all.frame2.style.zIndex = 0;
				}
		}
}

function showSubMenu(el,show) {
        var dir = 2;
        temp = el;
        list = el.children.tags("TD");
        el = list[list.length-1];
        if (el.menu == null)
                el.menu = getChildren(el);
        temp.subMenu = el.menu;
        if ((el.menu != activeMenu) && (show)) {
                activeSub = el.menu;

                if (typeof(activeSub) != 'undefined') {
                    var lPos = leftPos(el.menu);
                    if ((document.body.offsetWidth - lPos)  >= el.menu.offsetWidth) {
                        el.menu.style.left = (ie5) ? el.parentNode.offsetWidth
                                                   : el.offsetParent.offsetWidth;
                        dir = 6;
                    }
                    else {
                        el.menu.style.left = - el.menu.offsetWidth + 3;
                        dir = 4;
                    }

                    var tPos = (ie5) ? topPos(el.menu) + el.offsetTop
                                     : topPos(el.menu) + el.offsetParent.offsetTop;// + el.menu.offsetTop;

                    if ((document.body.offsetHeight - tPos) >= el.menu.offsetHeight)
                        el.menu.style.top =  (ie5) ? el.offsetTop
                                                   : el.offsetParent.offsetTop;
                    else
                        el.menu.style.top =  (ie5) ? el.offsetTop + el.offsetHeight - el.menu.offsetHeight
                                                   : el.offsetParent.offsetTop + el.offsetParent.offsetHeight - el.menu.offsetHeight + 2;
                    showSub(el.menu, dir);
                }
        }
        else {
                show(el.menu ,dir);
                activeSub = null;
        }
}


////////////////////////////////////////////////////////
//The following two functions are needed to calculate the position
function topPos(el) {
        var temp = el;
        var y = 0;
        if (typeof(temp) == 'undefined')
            return y;
        while (temp.id!="menu") {
                temp = temp.offsetParent;
                y += temp.offsetTop;
        }
        return y;
}

function leftPos(el) {
        var temp = el;
        var x = 0;
        if (typeof(temp) == 'undefined')
            return x;
        while (temp.id!="menu") {
                temp = temp.offsetParent;
                x += temp.offsetLeft;
        }
        return x + el.offsetParent.offsetWidth;
}
////////////////////////////////////////////////////////


function show(el, dir) {
        if (typeof(fade) == "function")
                fade(el, true);
        else if (typeof(swipe) == "function") {
                tempElSwipe = el;
                tempDirSwipe = dir;
                el.style.visibility = "visible";
                el.style.visibility = "hidden";
                window.setTimeout("tempSwipe()", 0);
//                swipe(el, dir);
        }
        else
                el.style.visibility = "visible";
}

var tempElSwipe, tempDirSwipe;

function tempSwipe() {
		swipe(tempElSwipe, tempDirSwipe);
}

function showSub(el ,dir) {
        show(el, dir);
}

function cleanup(menu,hide) {
    if (typeof(menu) != 'undefined') {
		if (menu.activeItem) { //If you've been here before
				if ((menu.activeItem.className == "sub") && (menu.activeItem.subMenu)){ //The active item has  a submenu
						cleanup(menu.activeItem.subMenu, true);  //Clean up the subs as well
                }
				menuItemNormal(menu.activeItem);
        }
        if (hide) {
                hideMenu(menu);
        }
    }
}

function hideMenu(el) {
        if (typeof(fade) == "function") {
                fade(el, false);
//                window.setTimeout(fadeTimer);
        }
        else if (typeof(swipe) == "function") {
                hideSwipe(el);
        }
        else
                el.style.visibility = "hidden";
}

function getChildren(el) {
        var tList = el.children.tags("TABLE");
        return tList[0];
}

/////////////////////////////////////////////////////////////////////////////
// The rest is just for the moving/docking
var dragObject = null;
var dragObjectPos = "top";
var tx;
var ty;

/////////////////////////////////////////////////////////////////////////////
// Fixing sizes and positions
//window.onload=fixSize;
//window.onresize=fixSize;

function fixSize() {
//        return true;
        if (dragObjectPos == "top") {
                outerDiv.style.top = menu.offsetHeight;
                outerDiv.style.height = document.body.clientHeight - menu.offsetHeight;
        }
        else if( dragObjectPos == "bottom") {
                outerDiv.style.top = 0;
                outerDiv.style.height = document.body.clientHeight - menu.offsetHeight;
                menu.style.top = document.body.clientHeight - menu.offsetHeight;
        }
        else {
                outerDiv.style.top = 0;
                outerDiv.style.height=document.body.clientHeight;
        }
}
/////////////////////////////////////////////////////////////////////////////


function document.onmousedown() {
        if(window.event.srcElement.id == "handle") {
                dragObject = document.all[window.event.srcElement.getAttribute("for")];
				Restore();        //Hide the menus while moving
                ty = (window.event.clientY - dragObject.style.pixelTop);
                window.event.returnValue = false;
                window.event.cancelBubble = true;
        }
        else {
                dragObject = null;
        }
}

function document.onmouseup() {
        if(dragObject) {
                dragObject = null;
        }
}

function document.onmousemove() {
        if(dragObject) {
                if(window.event.clientX >= 0) {
                        if((window.event.clientY - ty) <= 15) {
                                dragObject.style.border = "0 solid buttonface";
                                dragObject.style.width = "100%";
                                dragObject.style.top = 0;
                                dragObject.style.left = 0;
                                dragObjectPos = "top";
                                fixSize();
                        }
                        else if ((window.event.clientY - ty) >= document.body.clientHeight - menu.offsetHeight - 15) {
                                dragObject.style.border = "0 solid buttonface";
                                dragObject.style.width = "100%";
                                dragObject.style.top = document.body.clientHeight - menu.offsetHeight;
                                dragObject.style.left = 0;
                                dragObjectPos="bottom";
                                fixSize();
                        }
                        else {
                                dragObject.style.width = "10px";
                                dragObject.style.left = window.event.clientX;
                                dragObject.style.top = window.event.clientY - ty;
                                dragObject.style.border = "2px outset white";
                                dragObjectPos = "float";
                                fixSize();
                        }
                }
                else {
                        dragObject.style.border = "";
                        dragObject.style.left = "0";
                        dragObject.style.top = "0";
                }
                window.event.returnValue = false;
                window.event.cancelBubble = true;
        }
}

//This function si used for hiding windowed controls because they interfere with the menus
function showWindowedObjects(show) {
		if (hideWindowedControls && (typeof(parent.frames.frame2.document) != "unknown")) {
//                var windowedObjectTags = new Array("SELECT", "IFRAME", "OBJECT", "APPLET","EMBED");
				var windowedObjectTags = new Array("SELECT");
				var windowedObjects = new Array();
				var j=0;

				for (var i=0; i<windowedObjectTags.length; i++) {
						var tmpTags = parent.frames.frame2.document.all.tags(windowedObjectTags[i]);

						if (tmpTags.length > 0) {
								for (var k=0; k<tmpTags.length; k++) {
										windowedObjects[j++] = tmpTags[k];
								}
						}
				}

				for (var i=0; i<windowedObjects.length; i++) {
						if (!show) {
								windowedObjects[i].visBackup = (windowedObjects[i].style.visibility == null) ? "visible" : windowedObjects[i].style.visibility;
						}
						windowedObjects[i].style.visibility = (show) ? windowedObjects[i].visBackup : "hidden";
				}
		}
}