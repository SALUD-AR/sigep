/*
Copyright Scand LLC http://www.scbr.com
This version of Software is free for using in non-commercial applications. 
For commercial use please contact info@scbr.com to obtain license
*/ 

 
 dhtmlXTreeObject.prototype.setOnDragIn=function(func){
 if(typeof(func)=="function")this._onDrInFunc=func;else this._onDrInFunc=eval(func);
};

 
dhtmlXTreeObject.prototype._createDragNode=function(htmlObject){
 if(!this.dADTempOff)return null;

 dhtmlObject=htmlObject.parentObject;
 if(this.lastSelected)this._clearMove(this.lastSelected);
 var dragSpan=document.createElement('div');
 dragSpan.innerHTML="<span><img width='14px' height='14px' src='"+this.imPath+"red.gif'></span>"+dhtmlObject.label;
 dragSpan.style.position="absolute";
 dragSpan.className="dragSpanDiv";
 return dragSpan;
}

 
 dhtmlXTreeObject.prototype._extSetMove=function(htmlObject,x,y){
 if(this.dragger.dragNode)
 this.dragger.dragNode.childNodes[0].childNodes[0].src=this.imPath+"green.gif";
 this._setMoveA(htmlObject,x,y);
};
 dhtmlXTreeObject.prototype._extClearMove=function(htmlObject,x,y){
 if(this.dragger.dragNode)
 this.dragger.dragNode.childNodes[0].childNodes[0].src=this.imPath+"red.gif";
 this._clearMoveA(htmlObject,x,y);
};

 dhtmlXTreeObject.prototype._clearMoveA=dhtmlXTreeObject.prototype._clearMove;
 dhtmlXTreeObject.prototype._clearMove=dhtmlXTreeObject.prototype._extClearMove;

 dhtmlXTreeObject.prototype._setMoveA=dhtmlXTreeObject.prototype._setMove;
 dhtmlXTreeObject.prototype._setMove=dhtmlXTreeObject.prototype._extSetMove;




