// Ver: 3.61
String.prototype.number=new Function("return isNaN(atoi(this)) ? 0 : atoi(this)");
String.prototype.bool=new Function("return this+''=='false'||this+''=='0' ? false : true");
Number.prototype.number=new Function("return this");
Number.prototype.bool=new Function("return this==0");
Boolean.prototype.number=new Function("return this ? 0 : 1");
Boolean.prototype.bool=new Function("return this");
ud_='undefined';d_=document;w_=window;px_='px';pt_='pt';b_=d_.body;ab_='absolute';stt_='static';atoi=parseInt;
var nOP=0,nOP5=0,nIE=0,nIE4=0,nIE5=0,nNN=0,nNN4=0,nNN6=0,nMac=0,nIEM=0,nIEW=0,nSTMENU=0;var NS4=0;var nVer=0.0;
detectNav();

// user global param
var MaxMenuNumber=10;
var ReportError=1;
var ReportErrorInIEMac=1;
var ClickItemHideAll=0;
var HideSelect=1;
var HideObject=1;
var HideIFrame=0;
var HideInput=1;

if(nNN6)	HideSelect=0;
if(!nOP5)	HideInput=0;
if((nIEW&&nVer>=5.5)||(nNN6&&nVer>=7.0))	HideIFrame=0;
var st_ht="";
var st_gcount=0;
var st_rl_id=null;
var st_cl_w,st_cl_h;
var st_cumei,st_cumbi,st_cuiti;
var st_rei=/STM([^_]*)_([0-9]*)__([0-9]*)___/;
var st_reb=/STM([^_]*)_([0-9]*)__/;
var st_menus=[];
var st_resHandle="d_.location.reload();";
var st_buf=[];
var st_loaded=0;
var st_scrollid=null;
if((nIEM&&ReportErrorInIEMac)||((nIEW||nNN4)&&ReportError))	w_.onerror=errHandler;
if(nSTMENU)		w_.onload=st_onload;

if(typeof(st_jsloaded)=='undefined'){
if(nSTMENU&&!nNN4)
{
	for(i=0;i<MaxMenuNumber;i++)
	{
		if(nIEW&&nVer>=5.0&&d_.body)
			d_.body.insertAdjacentHTML("AfterBegin","<FONT ID=st_global"+i+"></FONT>");
		else
			d_.write("<FONT ID=st_global"+i+"></FONT>");
	}
}
if(nSTMENU&&!nNN4)	d_.write("<STYLE>\n.st_tbcss{border:none;padding:0px;margin:0px;}\n.st_tdcss{border:none;padding:0px;margin:0px;}\n.st_divcss{border:none;padding:0px;margin:0px;}\n.st_ftcss{border:none;padding:0px;margin:0px;}\n</STYLE>");
st_jsloaded=1;}

st_state=["OU","OV"];

st_fl_id=["Box in","Box out","Circle in","Circle out","Wipe up","Wipe down","Wipe right","Wipe left","Vertical blinds","Horizontal blinds","Checkerboard across","Checkerboard down","Random dissolve","Split vertical in","Split vertical out","Split horizontal in","Split horizontal out","Strips left down","Strips left up","Strips right down","Strips right up","Random bars horizontal","Random bars vertical","Random filter","Fade",
	"Wheel","Slide","Slide push","Spread","Pixelate","Stretch right","Stretch horizontally","Cross in","Cross out","Plus in","Plus out","Star in","Star out","Diamond in","Diamond out","Checkerboard up","Checkerboard left","Blinds up","Blinds left","Wipe clock","Wipe wedge","Wipe radial","Spiral","Zigzag"];
st_fl_string=
[
	"Iris(irisStyle=SQUARE,motion=in)","Iris(irisStyle=SQUARE,motion=out)","Iris(irisStyle=CIRCLE,motion=in)","Iris(irisStyle=CIRCLE,motion=out)",
	"Wipe(GradientSize=1.0,wipeStyle=1,motion=reverse)","Wipe(GradientSize=1.0,wipeStyle=1,motion=forward)","Wipe(GradientSize=1.0,wipeStyle=0,motion=forward)","Wipe(GradientSize=1.0,wipeStyle=0,motion=reverse)",
	"Blinds(bands=8,direction=RIGHT)","Blinds(bands=8,direction=DOWN)",
	"Checkerboard(squaresX=16,squaresY=16,direction=right)","Checkerboard(squaresX=12,squaresY=12,direction=down)","RandomDissolve()",
	"Barn(orientation=vertical,motion=in)","Barn(orientation=vertical,motion=out)","Barn(orientation=horizontal,motion=in)","Barn(orientation=horizontal,motion=out)",
	"Strips(Motion=leftdown)","Strips(Motion=leftup)","Strips(Motion=rightdown)","Strips(Motion=rightup)",
	"RandomBars(orientation=horizontal)","RandomBars(orientation=vertical)","","Fade(overlap=.5)",
	"Wheel(spokes=16)","Slide(slideStyle=hide,bands=15)","Slide(slideStyle=swap,bands=15)","Inset()","Pixelate(MaxSquare=15)",
	"Stretch(stretchStyle=hide)","Stretch(stretchStyle=spin)",
	"Iris(irisStyle=cross,motion=in)","Iris(irisStyle=cross,motion=out)","Iris(irisStyle=plus,motion=in)","Iris(irisStyle=plus,motion=out)","Iris(irisStyle=star,motion=in)","Iris(irisStyle=star,motion=out)","Iris(irisStyle=diamond,motion=in)","Iris(irisStyle=diamond,motion=out)",
	"Checkerboard(squaresX=16,squaresY=16,direction=up)","Checkerboard(squaresX=16,squaresY=16,direction=left)","Blinds(bands=8,direction=up)","Blinds(bands=8,direction=left)",
	"RadialWipe(wipeStyle=clock)","RadialWipe(wipeStyle=wedge)","RadialWipe(wipeStyle=radial)","Spiral(GridSizeX=16,GridSizeY=16)","Zigzag(GridSizeX=16,GridSizeY=16)"
];

st_fl=[];	for(i=st_fl_id.length-1;i>=0;i--)	eval("st_fl['"+st_fl_id[i]+"']=i;");

function beginSTM()
{
	var argu=arguments;
	var argt=[["nam",""],["type","relative"],["pos_l","0"],["pos_t","0"],["flt","none"],["click_sh","false"],["click_hd","true"],["ver","300"],["hddelay","1000"],["shdelay_h","0"],["shdelay_v","250"],["web_path",null],["blank_src","blank.gif"],["",""]];
	for(i=argt.length-2;i>=0;i--) eval("var "+argt[i][0]+"=(i<argu.length)?argu[i]:argt[i][1];");
	nam=nam.replace(/_/g,"rep");
	while(typeof(st_menus[nam])!=ud_)nam+='a';
	st_cumei=nam;st_cumbi=0;st_cuiti=0;
	
	st_menus[st_cumei]=
	{
		bodys:		[],
		mei:		st_cumei,
		hdid:		null,

		block:		"STM"+st_cumei+"_",
		nam:		nam,
		type:		type,
		pos:		type,
		pos_l:		pos_l.number(),
		pos_t:		pos_t.number(),
		flt:		flt,
		click_sh:	click_sh.bool(),
		click_hd:	click_hd.bool(),
		ver:		ver.number(),
		hddelay:	hddelay.number(),
		shdelay_h:	shdelay_h.number(),
		shdelay_v:	shdelay_v.number(),
		web_path:	web_path,

		blank:		new Image(),		
		clicked:	false,

		hideall:	hideall
	};
	var tmobj=st_menus[st_cumei];
	if(web_path==null)	tmobj.web_path='';
	if(tmobj.web_path!=''&&tmobj.web_path.substr(tmobj.web_path.length-1)!='/')	tmobj.web_path+='/';
	tmobj.blank.src=((web_path==null&&typeof(st_path)!=ud_) ? st_path : tmobj.web_path)+blank_src;

	switch(tmobj.type)
	{
	case "absolute":
		tmobj.type="custom";
		break;
	case "custom":
	case "float":
		tmobj.pos=ab_;
		break;
	case "relative":
		if(!tmobj.pos_l&&!tmobj.pos_t)
		{
			tmobj.pos=stt_;
			tmobj.type=stt_;
		}
		break;
	case "static":
	default:
		tmobj.type=stt_;
		tmobj.pos=stt_;
		tmobj.pos_l=0;
		tmobj.pos_t=0;
		break;
	}
}

function beginSTMB()
{
	var argu=arguments;
	var argt=[
	["offset","auto"],["offset_l","0"],["offset_t","0"],["arrange","vertically"],
	["arrow",""],["arrow_w","-1"],["arrow_h","-1"],
	["spacing","3"],["padding","0"],
	["bg_cl","#ffffff"],["bg_image",""],["bg_rep","repeat"],
	["bd_cl","#000000"],["bd_sz","0"],["bd_st","none"],
	["trans","0"],["spec","Normal"],["spec_sp","50"],
	["lw_max","16"],["lh_max","16"],["rw_max","16"],["rh_max","16"],
	["bg_pos_x","0%"],["bg_pos_y","0%"],
	["ds_sz","0"],["ds_color","gray"],
	["hdsp","false"],["bd_cl_t",""],["bd_cl_r",""],["bd_cl_b",""],
	["ds_st","none"],
	["",""]];
	for(i=argt.length-2;i>=0;i--)
	  eval("var "+argt[i][0]+"=(i<argu.length)?argu[i]:argt[i][1];");
	switch(bg_rep){
		case 'tile':
		case 'tiled':
			{bg_rep='repeat';}
			break;
		case 'free':
			bg_rep='no-repeat';
			break;
		case 'tiled by x':
			bg_rep='repeat-x';
			break;
		case 'tiled by y':
			bg_rep='repeat-y';
			break;
		default:
			break;
	}

	var oldmbi=st_cumbi;var olditi=st_cuiti;st_cumbi=st_menus[st_cumei].bodys.length;st_cuiti=0;
	var menu=st_menus[st_cumei];

	menu.bodys[st_cumbi]=
	{
		items:		[],

		mei:		st_cumei,
		mbi:		st_cumbi,
		block:		"STM"+st_cumei+"_"+st_cumbi+"__",
		par:		(st_cumbi ? [st_cumei,oldmbi,olditi] : null),
		getpar:		getparit,
		getme:		getme,
		getlayer:	getlayerMB,
		tmid:		null,
		curiti:		-1,
		isshow:		(st_cumbi==0&&menu.type!="custom"),
		isstatic:	!st_cumbi&&menu.type=='static',
		isvisible:	!st_cumbi&&menu.type!='custom',
		isclick:	!st_cumbi&&menu.click_sh,
		exec_ed:	false,

		arrange:	arrange,
		offset:		offset,
		offset_l:	offset_l.number(),
		offset_t:	offset_t.number(),
		arrow:		getsrc(arrow,menu),
		arrow_w:	arrow_w.number(),
		arrow_h:	arrow_h.number(),
		spacing:	spacing.number(),
		padding:	padding.number(),
		bg_cl:		bg_cl,
		bg_image:	getsrc(bg_image,menu),
		bg_rep:		bg_rep,
		bg_pos_x:	bg_pos_x,
		bg_pos_y:	bg_pos_y,
		bd_st:		bd_st,
		bd_sz:		bd_sz.number(),
		bd_cl:		bd_cl,
		opacity:	100-trans.number(),
		spec:		spec,
		spec_sp:	spec_sp.number(),
		fl_type:	-1,
		lw_max:		lw_max.number(),
		lh_max:		lh_max.number(),
		rw_max:		rw_max.number(),
		rh_max:		rh_max.number(),
		ds_st:		ds_st,
		ds_sz:		ds_st!='none' ? ds_sz.number() : 0,
		ds_color:	ds_color,
		hdsp:		hdsp.bool(),

		getrect:	getrect,
		getxy:		getxy,
		moveto:		moveto,
		adjust:		adjust,
		gettx_h:	getMBTextH,
		gettx_e:	getMBTextE,
		show:		show,
		hide:		hide,
		showpop:	showpop,
		hidepop:	hidepop,
		getCSS:		getMBCSS,
		getFCSS:	getMBFCSS
	};
	var tmobj=menu.bodys[st_cumbi];
	if(st_cumbi)	tmobj.getpar().sub=[st_cumei,st_cumbi];
	tmobj.z_index=	!st_cumbi ? 1000 : tmobj.getpar().getpar().z_index+10;
	if(tmobj.offset=="auto")
	{
		if(st_cumbi)
			tmobj.offset=tmobj.getpar().getpar().arrange=="vertically" ? "right" : "down";
		else
			tmobj.offset= "down";
	}
	if(tmobj.bd_st=="none")
		tmobj.bd_sz=0;
	if(nSTMENU&&!nNN4&&bd_cl_t!="")
		tmobj.bd_cl=(bd_cl_t+" "+bd_cl_r+" "+bd_cl_b+" "+bd_cl);
	bufimg(tmobj.bg_image);
	tmobj.background=getbg(tmobj.bg_cl,tmobj.bg_image,tmobj.bg_rep);
	if(tmobj.mbi&&!tmobj.getpar().getpar().bufed)
	{
		bufimg(tmobj.getpar().getpar().arrow);
		tmobj.getpar().getpar().bufed=true;
	}
	if(nIEW&&nVer<5.0&&nVer>=4.0&&tmobj.isstatic)
	{
		tmobj.spec_init=normal_init;
		tmobj.spec_sh=normal_sh;
		tmobj.spec_hd=normal_hd;
	}
	else if(nIEW&&typeof(st_fl[spec])!=ud_&&(nVer>=5.5||(nVer<5.5&&st_fl[spec]<=23)))
	{
		tmobj.spec_init=filter_init;
		tmobj.spec_sh=filter_sh;
		tmobj.spec_hd=filter_hd;
	}
	else if(nIEW&&spec=="Fade")
	{
		tmobj.spec_init=fade_init;
		tmobj.spec_sh=fade_sh;
		tmobj.spec_hd=fade_hd;
	}
	else
	{
		tmobj.spec_init=normal_init;
		tmobj.spec_sh=normal_sh;
		tmobj.spec_hd=normal_hd;
	}
	tmobj.spec_init();
}

function appendSTMI()
{
	var argu=arguments;
	var argt=[
	["isimage","false"],["text",""],["align","left"],["valign","middle"],
	["image_ou",""],["image_ov",""],["image_w","-1"],["image_h","-1"],["image_b","0"],
	["type","normal"],["bgc_ou","#ffffff"],["bgc_ov","#ffffff"],
	["sep_img",""],["sep_size","1"],["sep_w","-1"],["sep_h","-1"],
	["icon_ou",""],["icon_ov",""],["icon_w","-1"],["icon_h","-1"],["icon_b","0"],
	["tip",""],["url",""],["target","_self"],
	["f_fm_ou","Arial"],["f_sz_ou","9"],["f_cl_ou","#000000"],["f_wg_ou","normal"],["f_st_ou","normal"],["f_de_ou","none"],
	["f_fm_ov","Arial"],["f_sz_ov","9"],["f_cl_ov","#000000"],["f_wg_ov","normal"],["f_st_ov","normal"],["f_de_ov","underline"],
	["bd_sz","0"],["bd_st","none"],["bd_cl_r_ou","#000000"],["bd_cl_l_ou","#000000"],["bd_cl_r_ov","#000000"],["bd_cl_l_ov","#000000"],
	["bd_cl_t_ou",""],["bd_cl_b_ou",""],["bd_cl_t_ov",""],["bd_cl_b_ov",""],
	["st_text",""],["bg_img_ou",""],["bg_img_ov",""],["bg_rep_ou","repeat"],["bg_rep_ov","repeat"],
	["",""]];
	for(i=argt.length-2;i>=0;i--) eval("var "+argt[i][0]+"=(i<argu.length)?argu[i]:argt[i][1];");
	switch(bg_rep_ou){case 'tile':case 'tiled':bg_rep_ou='repeat';break;case 'free':bg_rep_ou='no-repeat';break;case 'tiled by x':bg_rep_ou='repeat-x';break;case 'tiled by y':bg_rep_ou='repeat-y';break;default:break;}
	switch(bg_rep_ov){case 'tile':case 'tiled':bg_rep_ov='repeat';break;case 'free':bg_rep_ov='no-repeat';break;case 'tiled by x':bg_rep_ov='repeat-x';break;case 'tiled by y':bg_rep_ov='repeat-y';break;default:break;}

	st_cuiti=st_menus[st_cumei].bodys[st_cumbi].items.length;
	var menu=st_menus[st_cumei];
	var body=menu.bodys[st_cumbi];
	body.items[st_cuiti]=
	{
		mei:		st_cumei,
		mbi:		st_cumbi,
		iti:		st_cuiti,
		block:		"STM"+st_cumei+"_"+st_cumbi+"__"+st_cuiti+"___",
		par:		[st_cumei,st_cumbi],
		getpar:		getparmb,
		sub:		null,
		getsub:		getsubmenu,
		getme:		getme,
		getlayer:	getlayerIT,
		get_st_lay:	get_st_lay,
		txblock:	"STM"+st_cumei+"_"+st_cumbi+"__"+st_cuiti+"___"+"TX",
		tmid:		null,

		isimage:	isimage.bool(),
		text:		text,
		align:		align,
		valign:		valign,
		image:		[getsrc(image_ou,menu),getsrc(image_ov,menu)],

		image_w:	image_w.number(),
		image_h:	image_h.number(),
		image_b:	image_b.number(),
		type:		type,
		bg_cl:		[bgc_ou,bgc_ov],
		sep_img:	getsrc(sep_img,menu),
		sep_size:	sep_size.number(),
		sep_w:		sep_w.number(),
		sep_h:		sep_h.number(),
		icon:		[getsrc(icon_ou,menu),getsrc(icon_ov,menu)],
		icon_w:		icon_w.number(),
		icon_h:		icon_h.number(),
		icon_b:		icon_b.number(),
		tip:		tip,
		url:		url,
		target:		target,
		f_fm:		[f_fm_ou.replace(/'/g,''),f_fm_ov.replace(/'/g,'')],
		f_sz:		[f_sz_ou.number(),f_sz_ov.number()],
		f_cl:		[f_cl_ou,f_cl_ov],
		f_wg:		[f_wg_ou,f_wg_ov],
		f_st:		[f_st_ou,f_st_ov],
		f_de:		[f_de_ou,f_de_ov],
	
		bd_st:		bd_st,
		bd_sz:		bd_sz.number(),
		bd_cl_r:	[bd_cl_r_ou,bd_cl_r_ov],
		bd_cl_l:	[bd_cl_l_ou,bd_cl_l_ov],
		bd_cl_t:	[bd_cl_t_ou,bd_cl_t_ov],
		bd_cl_b:	[bd_cl_b_ou,bd_cl_b_ov],

		st_text:	st_text,
		bg_img:		[getsrc(bg_img_ou,menu),getsrc(bg_img_ov,menu)],
		bg_rep:		[bg_rep_ou,bg_rep_ov],

		getrect:	getrect,
		gettx:		getMIText,
		showpop:	shitpop,
		hidepop:	hditpop,
		getCSS:		getMICSS,
		getTXCSS:	getMITXCSS
	};

	var tmobj=st_menus[st_cumei].bodys[st_cumbi].items[st_cuiti];
	if(tmobj.bd_st=="none"||!tmobj.bd_sz)
	{
		tmobj.bd_sz=0;	tmobj.bd_st="none";
	}
	if(nOP)
	{
		if(tmobj.bd_st=="ridge")	tmobj.bd_st="outset";
		if(tmobj.bd_st=="groove")	tmobj.bd_st="inset";
	}
	if(tmobj.bd_st=="inset")
	{
		var tmclr=tmobj.bd_cl_l;	tmobj.bd_cl_l=tmobj.bd_cl_r;	tmobj.bd_cl_r=tmclr;	tmobj.bd_st="outset";
	}
	if(bd_cl_t_ou=="")
	{
		if("none_solid_double_dashed_dotted".indexOf(tmobj.bd_st)>=0)
			tmobj.bd_cl_r=tmobj.bd_cl_l;
		if(tmobj.bd_st=="outset")
			tmobj.bd_st="solid";
		tmobj.bd_cl_t=tmobj.bd_cl_l;
		tmobj.bd_cl_b=tmobj.bd_cl_r;
	}
	tmobj.bd_cl=[];
	for(i=0;i<2;i++)
		tmobj.bd_cl[i]=tmobj.bd_cl_t[i]+" "+tmobj.bd_cl_r[i]+" "+tmobj.bd_cl_b[i]+" "+tmobj.bd_cl_l[i];
	if(tmobj.type=="sepline")
		bufimg(tmobj.sep_img);
	else
	{
		for(i=0;i<2;i++)
		{
			bufimg(tmobj.icon[i]);
			if(tmobj.isimage)
				bufimg(tmobj.image[i]);
			bufimg(tmobj.bg_img[i]);
		}
	}
	tmobj.background=[getbg(tmobj.bg_cl[0],tmobj.type=='sepline' ? '' : tmobj.bg_img[0],tmobj.bg_rep[0]),getbg(tmobj.bg_cl[1],tmobj.bg_img[1],tmobj.bg_rep[1])];
}

function endSTMB()
{
	tmobj=st_menus[st_cumei].bodys[st_cumbi].getpar();
	if(tmobj)
	{
		st_cumei=tmobj.mei;
		st_cumbi=tmobj.mbi;
		st_cuiti=tmobj.iti;
	}
}

function endSTM()
{
	var menu=st_menus[st_cumei];
	var menuHTML="";
	var max_l=nSTMENU ? menu.bodys.length : 1;
	for(mbi=0;mbi<max_l;mbi++)
	{
		var body=menu.bodys[mbi];
		var bodyHTML=body.gettx_h();
		bodyHTML+=(body.arrange=="vertically" ? "" : (nNN4||!nSTMENU ? "<TR HEIGHT=100%>" : "<TR ID="+body.block+"TR>"));
		for(iti=0;iti<body.items.length;iti++)
		{
			var item=body.items[iti];
			var itemHTML="";
			itemHTML+=(body.arrange=="vertically" ? (nNN4||!nSTMENU ? "<TR HEIGHT=100%>" : "<TR ID="+item.block+"TR>") : "");
			itemHTML+=item.gettx();
			itemHTML+=(body.arrange=="vertically" ? "</TR>" : "");
			bodyHTML+=itemHTML;
		}
		bodyHTML+=(body.arrange=="vertically" ? "" : "</TR>");
		bodyHTML+=body.gettx_e();
		if(body.isstatic||nNN4||!nSTMENU)
			menuHTML+=bodyHTML;
		else
			st_ht+=bodyHTML;
	}
	if(menuHTML!='')
		d_.write(menuHTML);
	if(nSTMENU&&!nIEM)
	{
		if(st_ht!='')
		{
			if(nNN6)
				getob('st_global'+st_gcount).innerHTML=st_ht;
			else if(nIE&&nVer>=5.0)
				getob('st_global'+st_gcount).insertAdjacentHTML("BeforeEnd",st_ht);
			else
				getob('st_global'+st_gcount).document.write(st_ht);
			st_gcount++;
			st_ht='';
		}
		if(!nOP&&!nNN4)
			prefix(st_cumei);
	}
}

function getMBTextH()
{
	var s="";
	if(nNN4||!nSTMENU)
	{
		if(nNN4)
		{
			s+=!this.isstatic ? "<LAYER" : "<ILAYER";
			if(this.mbi==0&&this.getme().pos==ab_)
				s+=(" LEFT="+this.getme().pos_l+" TOP="+this.getme().pos_t);
			s+=" VISIBILITY="+(this.isvisible ? "show" : "hide");
			s+=" ID="+this.block;
			s+=" Z-INDEX="+this.z_index;
			s+="><LAYER ID="+this.block+"IN>";
		}
		s+="<TABLE BORDER=0 CELLPADDING="+this.bd_sz+" CELLSPACING=0";
		if(this.bd_sz)
			s+=" BGCOLOR="+this.bd_cl;
		s+="><TR><TD>";
		s+="<TABLE BORDER=0 CELLSPACING=0 CELLPADDING="+this.spacing;
		if(this.bg_image!="")
			s+=" BACKGROUND=\""+this.bg_image+"\"";
		if(this.bg_cl!="transparent")
			s+=" BGCOLOR="+this.bg_cl;
		s+=" ID="+this.block;
		s+=">";
	}
	else
	{
		var stdiv="position:"+(this.mbi ? ab_ : this.getme().pos)+";";
		if(this.mbi==0)
		{
			stdiv+=("float:"+this.getme().flt+";");
			stdiv+=("left:"+this.getme().pos_l+"px;");
			stdiv+=("top:"+this.getme().pos_t+"px;");
		}
		
		stdiv+="z-index:"+this.z_index+";";
		stdiv+="visibility:hidden;";
		
		s+=nIE||(nOP&&nVer>=6.0) ? "<TABLE class=st_tbcss CELLPADDING=0 CELLSPACING=0" : "<DIV class=st_divcss";
		s+=" ID="+this.block;
		s+=" STYLE='";
		if(nIEM)
			s+="width:1px;";
		else if(nIE)
			s+="width:0px;";
		s+=this.getFCSS();
		s+=stdiv;
		s+="'>";
		if(nIE||(nOP&&nVer>=6.0))
			s+="<TR ID="+this.block+"TTR><TD class=st_tdcss ID="+this.block+"TTD>";
		s+="<TABLE class=st_tbcss CELLSPACING=0 CELLPADDING=0";
		s+=" ID="+this.block+"TB";
		s+=" STYLE='";
		s+=this.getCSS();
		if(!nOP)
			s+="margin:"+this.ds_sz+"px;";
		s+="'>";
	}
	return s;
}

function getMBTextE()
{
	if(!nSTMENU)
		return "</TABLE></TD></TR></TABLE>";
	else if(nNN4)
		return this.isstatic ? "</TABLE></TD></TR></TABLE></LAYER></ILAYER>" : "</TABLE></TD></TR></TABLE></LAYER></LAYER>";
	else if(nIE||(nOP&&nVer>=6.0))
		return "</TABLE></TD></TR></TABLE>";
	else
		return "</TABLE></DIV>";
}

function getMIText()
{
	var s="";
	if(nNN4||!nSTMENU)
	{
		var max_i=nNN4 ? 2 : 1;
		s+="<TD WIDTH=1 NOWRAP><FONT STYLE='font-size:1pt;'>";
		if(nNN4)
			s+="<ILAYER ID="+this.block+"><LAYER ID="+this.block+"IN>";
		for(i=0;i<max_i;i++)
		{
			if(this.type=="sepline"&&i)
				break;
			if(nNN4)
				s+="<LAYER ID="+this.block+st_state[i]+" Z-INDEX=10 VISIBILITY="+(i ? "HIDE" : "SHOW")+">";
			s+="<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING="+(this.type=="sepline" ? 0 : this.bd_sz);
			if(!nNN4)
				s+=" HEIGHT=100%";
			if(this.bd_sz)
				s+=" BGCOLOR="+this.bd_cl_l[i];
			s+="><TR><TD WIDTH=100%>";
			s+="<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING="+(this.type=="sepline" ? 0 : this.getpar().padding);
			if(!nNN4)
				s+=" HEIGHT=100%";
			if(this.bg_cl[i]!="transparent")
				s+=" BGCOLOR="+this.bg_cl[i];
			s+=" TITLE="+addquo(this.type!="sepline" ? this.tip : "");
			s+="><TR>";

			if(this.type=="sepline")
			{
				s+="<TD NOWRAP VALIGN=TOP"+
					" HEIGHT="+(this.getpar().arrange=="vertically" ? this.sep_size : "100%")+
					" WIDTH="+(this.getpar().arrange=="vertically" ? "100%" : this.sep_size)+
					" STYLE='font-size:0pt;'"+
					">";
				s+=createIMG(this.sep_img,this.block+"LINE",this.sep_w,this.sep_h,0);
				s+="</TD>";
			}
			else
			{
				if(this.getpar().lw_max&&(this.getpar().arrange=="vertically"||this.icon_w))
				{
					s+="<TD ALIGN=CENTER VALIGN=MIDDLE";
					s+=getwdstr(this);
					s+=">";
					s+=createIMG(this.icon[i],this.block+"ICON",this.icon_w,this.icon_h,this.icon_b);
					s+="</TD>";
				}

				s+="<TD WIDTH=100% NOWRAP ALIGN="+this.align+" VALIGN="+this.valign+">";
				if(nNN4)
					s+="<FONT FACE='"+this.f_fm[i]+"' STYLE=\"";
				else
				{
					s+="<A HREF="+addquo(this.url=="" ? "javascript:;" : this.url); 
					s+=" TARGET="+this.target;
					s+=" STYLE=\"font-family:"+this.f_fm[0]+";";
				}
				s+="font-size:"+this.f_sz[i]+"pt;";
				s+="color:"+this.f_cl[i]+";";
				s+="font-weight:"+this.f_wg[i]+";";
				s+="font-style:"+this.f_st[i]+";";
				s+="text-decoration:"+this.f_de[i]+";";
				s+="\">";
				if(this.isimage)
					s+=createIMG(this.image[i],this.block+"IMG",this.image_w,this.image_h,this.image_b);
				else
				{
					s+="<IMG SRC=\""+this.getme().blank.src+"\" WIDTH=1 HEIGHT=1 BORDER=0 ALIGN=ABSMIDDLE>";
					s+=this.text;
				}
				s+=(nNN4 ? "</FONT>" : "</A>");
				s+="</TD>";

				if(this.getpar().arrow_w)
				{
					s+="<TD NOWRAP ALIGN=CENTER VALIGN=MIDDLE>";
					s+=createIMG((this.getsub() ? this.getpar().arrow : this.getme().blank.src),this.block+"ARROW",this.getpar().arrow_w,this.getpar().arrow_h,0);
					s+="</TD>";
				}
			}

			s+="</TR></TABLE>";
			s+="</TD></TR></TABLE>";
			s+="</LAYER>";
			
			if(nNN4&&this.type!="sepline")
			{
				s+="<LAYER ID="+this.block+st_state[i]+"M";
				s+=" Z-INDEX=20";
				s+=">";
				s+="</LAYER>";
			}
		}

		s+="</LAYER></ILAYER></FONT>";
		s+="</TD>";
	}
	else
	{
		s+="<TD class=st_tdcss NOWRAP VALIGN="+(nIE ? "MIDDLE" : "TOP");
		s+=" STYLE='"
			s+="padding:"+this.getpar().spacing+"px;";
		s+="'";
		s+=" ID="+this.getpar().block+this.iti;
		if(nIEW)
			s+=" HEIGHT=100%";
		s+=">";
		if(!nOP&&!nIE)
		{
			s+="<DIV class=st_divcss ID="+this.block;
			s+=" STYLE=\""+this.getCSS();
			s+="\"";
			s+=">";
		}
		s+="<TABLE class=st_tbcss CELLSPACING=0 CELLPADDING=0";
		if(!nOP)
			s+=" HEIGHT=100%";
		if(nIE)
			s+=" VALIGN=MIDDLE";

		s+=" STYLE=\"";
		if(nOP||nIE)
			s+=this.getCSS();
		s+="\"";
		if(this.getpar().arrange=="vertically"||nIEM)
			s+=" WIDTH=100%";
		s+=" ID="+(nOP||nIE ? this.block : (this.block+"TB"));
		s+=" TITLE="+addquo(this.type!="sepline" ? this.tip : "");
		s+="><TR ID="+this.block+"TR>";

		if(this.type=="sepline")
		{
			s+="<TD class=st_tdcss  NOWRAP VALIGN=TOP"+
				" ID="+this.block+"MTD"+
				" HEIGHT="+(this.getpar().arrange=="vertically" ? this.sep_size : "100%")+
				" WIDTH="+(this.getpar().arrange=="vertically" ? "100%" : this.sep_size)+
				">";
			s+=createIMG(this.sep_img,this.block+"LINE",this.sep_w,this.sep_h,0);
			s+="</TD>";
		}
		else
		{
			if(this.getpar().lw_max&&(this.getpar().arrange=="vertically"||this.icon_w))
			{
				s+="<TD class=st_tdcss NOWRAP ALIGN=CENTER VALIGN=MIDDLE HEIGHT=100%";
				s+=" STYLE=\"padding:"+this.getpar().padding+"px\"";
				s+=" ID="+this.block+"LTD";
				s+=getwdstr(this);
				s+=">";
				s+=createIMG(this.icon[0],this.block+"ICON",this.icon_w,this.icon_h,this.icon_b);
				s+="</TD>";
			}
			else if(this.getpar().arrange=="vertically")
			{
				s+="<TD class=st_tdcss";
				s+=" STYLE=\"padding:"+this.getpar().padding+"px\"";
				s+=" ID="+this.block+"LLTD WIDTH=3><IMG SRC=\""+this.getme().blank.src+"\" WIDTH=1 ID="+this.block+"LLTDI></TD>";
			}

			s+="<TD class=st_tdcss NOWRAP HEIGHT=100% STYLE=\"color:"+this.f_cl[0]+";";
			s+="padding:"+this.getpar().padding+"px;";
			s+="\"";
			s+=" ID="+this.block+"MTD";
			s+=" ALIGN="+this.align;
			s+=" VALIGN="+this.valign+">";
			s+="<FONT class=st_ftcss ID="+this.txblock+" STYLE=\""+this.getTXCSS()+"\">";
			if(this.isimage)
				s+=createIMG(this.image[0],this.block+"IMG",this.image_w,this.image_h,this.image_b);
			else
				s+=this.text;
			s+="</FONT>";
			s+="</TD>";

			if(this.getpar().arrow_w)
			{
				s+="<TD class=st_tdcss NOWRAP";
				s+=" STYLE=\"padding:"+this.getpar().padding+"px\"";
				s+=" ID="+this.block+"RTD";
				s+=" WIDTH="+(this.getpar().arrow_w+2);
				s+=" ALIGN=CENTER VALIGN=MIDDLE HEIGHT=100%>";
				s+=createIMG((this.getsub() ? this.getpar().arrow : this.getme().blank.src),this.block+"ARROW",this.getpar().arrow_w,this.getpar().arrow_h,0);
				s+="</TD>";
			}
			else if(this.getpar().arrange=="vertically")
			{
				s+="<TD class=st_tdcss";
				s+=" STYLE=\"padding:"+this.getpar().padding+"px\"";
				s+=" ID="+this.block+"RRTD WIDTH=3><IMG SRC=\""+this.getme().blank.src+"\" WIDTH=1 ID="+this.block+"RRTDI></TD>";
			}
		}
		
		s+="</TR></TABLE>";
		if(!nOP&&!nIE)
			s+="</DIV>";
		s+="</TD>";
	}
	return s;
}

function getMBCSS()
{
	var s="";
	s+="border-style:"+this.bd_st+";";
	s+="border-width:"+this.bd_sz+"px;";
	s+="border-color:"+this.bd_cl+";";
	if(nIE)
		s+="background:"+this.background+";";
	else
	{
		s+="background-color:"+(this.bg_cl)+";";
		if(this.bg_image!="")
		{
			s+="background-image:url("+this.bg_image+");";
			s+="background-repeat:"+this.bg_rep+";";
		}
	}
	return s;
}

function getMBFCSS()
{
	var s="";
	if(nIEW&&(nVer>=5.0||!this.isstatic))
	{
		s+="filter:";
		if(typeof(this.spec_string)!=ud_)
			s+=this.spec_string;
		s+=" Alpha(opacity="+this.opacity+") ";
		if(this.ds_sz!=0)
		{
			if(nVer>=5.5)
				s+="progid:DXImageTransform.Microsoft.";
			if(this.ds_st=="simple")
				s+="dropshadow(color="+this.ds_color+",offx="+this.ds_sz+",offy="+this.ds_sz+",positive=1)";
			else
				s+="Shadow(color="+this.ds_color+",direction=135,strength="+this.ds_sz+")";
		}
		s+=";";
	}
	return s;
}

function getMICSS()
{
	var s="";
	if(this.type!="sepline")
	{
		s+="border-style:"+this.bd_st+";";
		s+="border-width:"+this.bd_sz+"px;";
		s+="border-color:"+this.bd_cl[0]+";";
		
		if(!nIE&&this.bg_img[0]!="")
		{
			s+="background-image:url("+this.bg_img[0]+");";
			s+="background-repeat:"+this.bg_rep[0]+";";
		}
	}
	if(nIE)
		s+="background:"+this.background[0]+";";
	else
		s+="background-color:"+this.bg_cl[0]+";";
	s+="cursor:"+getcursor(this)+";";
	return s;
}

function getMITXCSS()
{
	var s="";
	s+="cursor:"+getcursor(this)+";";
	s+="font-family:"+this.f_fm[0]+";";
	s+="font-size:"+this.f_sz[0]+"pt;";
	s+="font-weight:"+this.f_wg[0]+";";
	s+="font-style:"+this.f_st[0]+";";
	s+="text-decoration:"+this.f_de[0]+";";
	return s;
}

function doitov()
{
	var e=nIE ? event : arguments[0];
	var obj=this;
	var id=this.id;
	st_rei.exec(id);
	mei=RegExp.$1;mbi=atoi(RegExp.$2);iti=atoi(RegExp.$3);
	var it=st_menus[mei].bodys[mbi].items[iti];
	if(nIEW)
	{
		if(!it.getpar().isshow||(e.fromElement&&obj.contains(e.fromElement)))
			return;
	}
	else
	{
		if(!it.getpar().isshow||(!nNN&&(e.fromElement&&e.fromElement.id&&e.fromElement.id.indexOf(it.block)>=0)))
			return ;
	}
	if(nNN4)
		it.getlayer().document.layers[0].captureEvents(Event.MOUSEDOWN);

	if(it.getme().hdid)
	{
		clearTimeout(it.getme().hdid);
		it.getme().hdid=null;
	}

	var curiti=it.getpar().curiti;
	var curit=null;
	if(curiti>=0)
		curit=it.getpar().items[curiti];

	if(!it.getpar().isclick||it.getme().clicked)
	{
		if(it.getpar().curiti!=it.iti)
		{
			if(it.getpar().curiti>=0)
			{
				it.getpar().items[it.getpar().curiti].hidepop();
				it.getpar().curiti=-1;
			}
			it.showpop();
			it.getpar().curiti=it.iti;
		}
		else
		{
			if(it.getsub()&&!it.getsub().isshow)
			{
				shitst(it,1);
				it.getsub().showpop();
			}
		}
	}
	if(it.st_text!="")
		w_.status=it.st_text;
}

function doitou()
{
	var e=nIE ? event : arguments[0];
	var obj=this;
	var id=this.id;
	st_rei.exec(id);
	mei=RegExp.$1;mbi=atoi(RegExp.$2);iti=atoi(RegExp.$3);
	var it=st_menus[mei].bodys[mbi].items[iti];

	if(nIEW)
	{
		if(!it.getpar().isshow||e.toElement&&obj.contains(e.toElement))
			return;
	}
	else
	{
		if(!it.getpar().isshow||(!nNN&&(e.toElement&&e.toElement.id&&e.toElement.id.indexOf(it.block)>=0)))
			return ;
	}
	
	if(nNN4)
		it.getlayer().document.layers[0].releaseEvents(Event.MOUSEDOWN);
	
	if(!it.getsub()||!it.getsub().isshow)
	{
		shitst(it,0);
		it.getpar().curiti=-1;
	}
	else if(it.getsub()&&it.getsub().isshow&&!it.getsub().exec_ed)
		it.hidepop();
	w_.status="";
}

function doitmd()
{
	var e=nIE ? event : arguments[0];
	if(e.button&&e.button>=2)
		return;
	var obj=this;
	var id=this.id;
	st_rei.exec(id);
	mei=RegExp.$1;mbi=atoi(RegExp.$2);iti=atoi(RegExp.$3);
	var it=st_menus[mei].bodys[mbi].items[iti];

	if(it.getpar().isclick)
	{
		it.getme().clicked=!it.getme().clicked;
		if(it.getme().clicked)
		{
			it.showpop();
			it.getpar().curiti=it.iti;
		}
		else
		{
			it.hidepop();
			it.getpar().curiti=-1;
		}
	}
	if(!(it.getpar().isclick&&it.getsub()))
	{
		if(it.url!="")
		{
			if(ClickItemHideAll)	it.getme().hideall();
			var _preurl="javascript:";
			if(it.url.toLowerCase().indexOf(_preurl)==0)
				eval(it.url.substring(_preurl.length,it.url.length));
			else
				w_.open(it.url,it.target);
		}
	}
}

function getrect()
{
	if(nNN4)
	{
		var obj=this.getlayer();
		return [obj.pageX,obj.pageY,obj.clip.width,obj.clip.height];
	}
	else
	{
		var l,t,w,h;
		var obj=this.getlayer();
		if(nOP)
		{
			w=atoi(obj.style.pixelWidth);
			h=atoi(obj.style.pixelHeight);
		}
		else
		{
			w=atoi(obj.offsetWidth);
			h=atoi(obj.offsetHeight);
			if(!nOP&&!nIEM&&typeof(this.iti)==ud_)
				h-=this.ds_sz*2;
		}
		l=0;t=0;
		if(nIEW)
		{
			while(obj)
			{
				switch(obj.tagName)
				{
					case 'DIV':
					case 'SPAN':
						if(obj.style.position!=ab_||(obj.id&&obj.id.indexOf('STM')>=0))
						{
							l+=atoi(obj.offsetLeft);
							t+=atoi(obj.offsetTop);
						}
						break;
					default:
						l+=atoi(obj.offsetLeft);
						t+=atoi(obj.offsetTop);
						break;
				}
				if((obj.tagName=='DIV'||obj.tagName=='SPAN')&&obj.style.position==ab_)
					break;
				obj=obj.offsetParent;
			}
		}
		else if(nNN6)
		{
			while(obj)
			{
				if((obj.id&&obj.id.indexOf("STM")>=0)||(!obj.style.position||obj.style.position!=ab_))
				{
					l+=atoi(obj.offsetLeft);
					t+=atoi(obj.offsetTop);
				}
				obj=obj.offsetParent;
			}
			if(nVer==6.0&&typeof(this.iti)!=ud_)
			{
				l-=this.bd_sz;
				t-=this.bd_sz;
			}
			else if(nVer>=7.0&&typeof(this.iti)!=ud_)
			{
				l+=this.getpar().bd_sz;
				t+=this.getpar().bd_sz;
			}
		}
		else if(nOP)
		{
			while(obj)
			{
				l+=atoi(obj.offsetLeft);
				t+=atoi(obj.offsetTop);
				obj=obj.offsetParent;
			}
		}
		else if(nIEM)
		{
			while(obj)
			{
				l+=atoi(obj.offsetLeft);
				t+=atoi(obj.offsetTop);
				if((obj.tagName=="SPAN"||obj.tagName=="DIV")&&(!obj.id||obj.id.indexOf("STM")<0)&&obj.style.position==ab_)
				{
					t+=atoi(d_.body.topMargin);
					break;
				}
				obj=obj.offsetParent;
			}
			l+=atoi(d_.body.leftMargin);
			l-=this.bd_sz;
			t-=this.bd_sz;
		}
		return [l,t,w,h];
	}
}

function getxy()
{
	var x=this.offset_l;
	var y=this.offset_t;
	var subrc=this.getrect();
	this.rc=subrc;
	if(this.mbi==0)
	{
		if(this.getme().type=="custom")
			return [this.getme().pos_l,this.getme().pos_t];
		else if(this.getme().type=="float")
			return [getcl()+this.getme().pos_l,getct()+this.getme().pos_t];
		else
			return [subrc[0],subrc[1]];
	}
	var itrc=this.getpar().getrect();
	var bdrc=this.getpar().getpar().getrect();
	switch(this.offset)
	{
		case "left":
			x+=itrc[0]-subrc[2];
			y+=itrc[1];
			break;
		case "up":
			x+=itrc[0];
			y+=itrc[1]-subrc[3];
			if(nIEM)
				y+=this.ds_sz;
			break;
		case "right":
			x+=itrc[0]+itrc[2];
			y+=itrc[1];
			break;
		case "down":
			x+=itrc[0];
			y+=itrc[1]+itrc[3];
			break;
		case "auto":
		default:
			break;
	}
	if(!nOP&&!nNN4)
	{
		x-=this.ds_sz;
		y-=this.ds_sz;
	}
	return this.adjust([x,y]);
}

function adjust(xy)
{
/*	var rc=this.getrect();*/
	var tx=xy[0];
	var ty=xy[1];
/*	var c_l=getcl();
	var c_t=getct();
	var c_r=c_l+getcw();
	var c_b=c_t+getch();
	if(tx+rc[2]>c_r)
		tx=c_r-rc[2];
	tx=tx>c_l ? tx : c_l;
	if(ty+rc[3]>c_b)
		ty=c_b-rc[3];
	ty=ty>c_t ? ty : c_t;*/
	return [tx,ty];
}

function ckPage()
{
	var st_or_w=st_cl_w;
	var st_or_h=st_cl_h;
	var st_or_l=st_cl_l;
	var st_or_t=st_cl_t;
	st_cl_w=getcw();
	st_cl_h=getch();
	st_cl_l=getcl();
	st_cl_t=getct();
	if((nOP||nNN4)&&(st_cl_w-st_or_w||st_cl_h-st_or_h))
		eval(st_resHandle);
	else if(st_cl_l-st_or_l||st_cl_t-st_or_t)
		setTimeout("scrollmenu();",500);
}

function shitst(it,nst)
{
	if(nNN4)
	{
		var st_lay=it.get_st_lay();
		for(i=0;i<2;i++)
		{
			st_lay[i*2+1].resizeTo(st_lay[i*2].clip.width,st_lay[i*2].clip.height);
			st_lay[i*2].visibility=(i==nst ? "show" : "hide");
			st_lay[i*2+1].visibility=(i==nst ? "show" : "hide");
		}
	}
	else
	{
		objs=it.getlayer().style;
		if(nIE)
			objs.background=it.background[nst];
		else
		{
			if(nOP)
				objs.background=it.bg_cl[nst];
			else
				objs.backgroundColor=it.bg_cl[nst];
			if(it.bg_img[nst]!="")
			{
				objs.backgroundImage="url("+it.bg_img[nst]+")";
				objs.backgroundRepeat=it.bg_rep[nst];
			}
		}
		if(it.bd_cl[0]!=it.bd_cl[1])
			objs.borderColor=it.bd_cl[nst];
		getob(it.block+'MTD').style.color=it.f_cl[nst];
		tmp=getob(it.txblock).style;
		if(it.f_fm[0]!=it.f_fm[1])
			tmp.fontFamily=it.f_fm[nst];
		tmp.fontSize=it.f_sz[nst]+"pt";
		tmp.fontWeight=it.f_wg[nst];
		tmp.fontStyle=it.f_st[nst];
		tmp.textDecoration=it.f_de[nst];
		
		if(it.icon[0]!=it.icon[1])
		{
			tmp=getob(it.block+"ICON");
			if(tmp)
				tmp.src=it.icon[nst];
		}
		if(it.isimage&&it.image[0]!=it.image[1])
		{
			tmp=getob(it.block+"IMG");
			if(tmp)
				tmp.src=it.image[nst];
		}
	}
}

function dombov()
{
	var e=nIE ? event : arguments[0];
	var obj=this;
	var id=this.id;
	st_reb.exec(id);
	mei=RegExp.$1;mbi=atoi(RegExp.$2);
	var mb=st_menus[mei].bodys[mbi];

	if(nIEW)
	{
		if(!mb.isshow||(e.fromElement&&obj.contains(e.fromElement)))
			return;
	}
	else
	{
		if(!mb.isshow||(!nNN&&(e.fromElement&&e.fromElement.id&&e.fromElement.id.indexOf(mb.block)>=0)))
			return ;
	}
	if(mb.getme().hdid)
	{
		clearTimeout(mb.getme().hdid);
		mb.getme().hdid=null;
	}
}

function dombou()
{
	var e=nIE ? event : arguments[0];
	var obj=this;
	var id=this.id;
	st_reb.exec(id);
	mei=RegExp.$1;mbi=atoi(RegExp.$2);
	var mb=st_menus[mei].bodys[mbi];

	if(nIEW)
	{
		if(!mb.isshow||(e.toElement&&obj.contains(e.toElement)))
			return;
	}
	else
	{
		if(!mb.isshow||(!nNN&&(e.toElement&&e.toElement.id&&e.toElement.id.indexOf(mb.block)>=0)))
			return ;
	}

	if(mb.getme().hdid)
	{
		clearTimeout(mb.getme().hdid);
		mb.getme().hdid=null;
	}
	mb.getme().hdid=setTimeout("st_menus['"+mb.mei+"'].hideall();",mb.getme().hddelay);
}

function showpop()
{
	this.show();
}

function shitpop()
{
	if(this.getsub())
	{
		if(!this.getsub().isshow)
			this.getsub().showpop();
	}
	shitst(this,1);
}

function hditpop()
{
	if(this.getsub()&&this.getsub().isshow)
		this.getsub().hidepop();
	shitst(this,0);
}

function hidepop()
{
	if(this.curiti>=0)
	{
		var tmp=this.items[this.curiti].getsub();
		if(tmp&&tmp.isshow)
			tmp.hidepop();
		shitst(this.items[this.curiti],0);
		this.curiti=-1;
	}
	this.hide();
}

function hideall()
{
	this.clicked=false;
	var mb=this.bodys[0];
	if(mb.isshow)
	{
		if(mb.curiti>=0)
		{
			mb.items[mb.curiti].hidepop();
			mb.curiti=-1;
		}
		if(this.type=="custom")
			mb.hide();
	}
	this.hdid=null;
}

function setupEvent(menu)
{
	var ly;
	for(mbi=0;mbi<menu.bodys.length;mbi++)
	{
		var body=menu.bodys[mbi];
		ly=nNN4 ? body.getlayer().document.layers[0] : body.getlayer();
		
		ly.onmouseover=dombov;
		ly.onmouseout=dombou;
		for(iti=0;iti<body.items.length;iti++)
		{
			var item=body.items[iti];
			if(item.type!="sepline")
			{
				ly=nNN4 ? item.getlayer().document.layers[0] : item.getlayer();
				ly.onmouseover=doitov;
				ly.onmouseout=doitou;
				ly.onmousedown=doitmd;
			}
		}
	}
}

function bufimg(sr)
{
	if(sr!="")
	{
		st_buf[st_buf.length]=new Image();
		st_buf[st_buf.length-1].src=sr;
	}
}

function normal_init()
{
}

function normal_sh()
{
	this.moveto(this.getxy());
	ck_win_els(-1,this);
	sh(this);
}

function normal_hd()
{
	hd(this);
	ck_win_els(+1,this);
}

function fade_init()
{
	this.current=0;
	this.step=atoi(this.opacity*this.spec_sp/200);
	if(this.step<=0)
		this.step++;
}

function fade_sh()
{
	if(this.exec_ed)
	{
		this.current+=this.step;
		if(this.current>this.opacity)
			this.current=this.opacity;
	}
	this.getlayer().filters["Alpha"].opacity=this.current;
	if(!this.exec_ed)
	{
		this.moveto(this.getxy());
		ck_win_els(-1,this);
		sh(this);
	}
	if(this.current!=this.opacity)
		this.tmid=setTimeout(get_sdstr(this,true),100);
}

function fade_hd()
{
	if(this.exec_ed)
	{
		this.current-=this.step;
		if(this.current<0||!this.hdsp)
			this.current=0;
	}
	this.getlayer().filters["Alpha"].opacity=this.current;
	if(!this.current)
	{
		hd(this);
		ck_win_els(+1,this);
	}
	else
		this.tmid=setTimeout(get_sdstr(this,false),100);
}

function filter_init()
{
	this.fl_type=st_fl[this.spec];
	if(this.fl_type==23)
		this.fl_type=atoi(23*Math.random());
	this.duration=10/this.spec_sp;
	if(nVer<5.5)
		this.spec_string=" revealTrans(Transition="+this.fl_type+",Duration="+this.duration+")";
	else
	{
		this.spec_string=" progid:DXImageTransform.Microsoft."+st_fl_string[this.fl_type];
		this.spec_string=this.spec_string.replace(')',',Duration='+this.duration+')');
	}
}

function filter_sh()
{
	var fl_obj=this.getlayer().filters[0];
	fl_obj.stop();
	fl_obj.apply();
	this.moveto(this.getxy());
	ck_win_els(-1,this);
	sh(this);
	fl_obj.play();
}

function filter_hd()
{
	var fl_obj=this.getlayer().filters[0];
	fl_obj.stop();
	if(this.hdsp)	fl_obj.apply();
	hd(this);
	ck_win_els(+1,this);
	if(this.hdsp)	fl_obj.play();
}

function showFloatMenuAt(mei,x,y)
{
	if(nSTMENU)
	{
		var menu=st_menus[mei.replace(/_/g,"rep")];
		if(menu&&menu.type=="custom"&&menu.bodys.length&&!menu.bodys[0].isshow)
		{
			//movetoex(menu,[x,y]);
			movetoex(menu,[0,0]);
			menu.bodys[0].show();
		}
	}
}

function movetoex(menu,xy)
{
	menu.pos_l=xy[0];
	menu.pos_t=xy[1];
}

function getcursor(it)
{
	if(nNN6)
		return "default";
	return it.type!="sepline"&&((it.mbi==0&&it.getme().click_sh&&it.getsub())||it.url!="") ? "hand" : "default";
}

function getwdstr(obj)
{
	if(obj.getpar().arrange=="vertically")
	{
		if(obj.getpar().lw_max>0)
			return " WIDTH="+obj.getpar().lw_max;
		else
			return "";
	}
	else
	{
		if(obj.icon_w>0)
			return " WIDTH="+obj.icon_w;
		else
			return "";
	}
}

function detectNav()
{
	var naVer=navigator.appVersion;
	var naAgn=navigator.userAgent;
	nMac=naVer.indexOf("Mac")>=0;
	nOP=naAgn.indexOf("Opera")>=0;
	if(nOP)
	{
		nVer=parseFloat(naAgn.substring(naAgn.indexOf("Opera ")+6,naAgn.length));
		nOP5=nVer>=5.12&&!nMac;
	}
	else
	{
		nIE=d_.all ? 1 : 0;
		if(nIE)
		{
			nIE4=(eval(naVer.substring(0,1)>=4));
			nVer=parseFloat(naAgn.substring(naAgn.indexOf("MSIE ")+5,naAgn.length));
			nIE5=nVer>=5.0&&nVer<5.5;
			nIEM=nIE4&&nMac;
			nIEW=nIE4&&!nMac;
		}
		else
		{
			nNN4=navigator.appName.toLowerCase()=="netscape"&&naVer.substring(0,1)=="4" ? 1 : 0;
			if(!nNN4)
			{
				nNN6=(d_.getElementsByTagName("*") && naAgn.indexOf("Gecko")!=-1);
				if(nNN6)
				{
					nVer=atoi(navigator.productSub);
					if(naAgn.indexOf("Netscape")>=0)
						nVer=(nVer<20010726 ? 6.0 : (nVer<20020512 ? 6.2 : 7.0));
					else
						nVer=(nVer<20010109 ? 6.0 : (nVer<20010400 ? 6.1 : (nVer<20011221 ? 6.2 : 7.0)));
				}
			}
			else
				nVer=parseFloat(naVer);
			nNN=nNN4||nNN6;
		}
	}
	nSTMENU=nOP5||nIE4||nNN;
}

function st_onload()
{
	if(nIEM||nOP5||nNN4)
	{
		if(st_ht!='')
			d_.body.insertAdjacentHTML('BeforeEnd',st_ht);
		for(i in st_menus)
			prefix(i);
	}
	st_loaded=1;
	if(!nNN4)
	{
		for(i in st_menus)
		{
			menu=st_menus[i];
			curit=null;
			for(body=menu.bodys[0];body&&body.isshow&&body.exec_ed;body=(curit&&curit.getsub() ? curit.getsub() : null))
			{
				ck_win_els(-1,body);
				curit=body.curiti>=0 ? body.items[body.curiti] : null;
			}
		}
	}
}

function errHandler(sMsg,sUrl,sLine)
{
	if(sMsg.substr(0,16)!="Access is denied"&&sMsg!="Permission denied")
		alert("Java Script Error\n"+"\nDescription:"+sMsg+"\nSource:"+sUrl+"\nLine:"+sLine);
	return true;
}

function getparit()
{
	return !this.par ? null : st_menus[this.par[0]].bodys[this.par[1]].items[this.par[2]];
}

function getparmb()
{
	return !this.par ? null : st_menus[this.par[0]].bodys[this.par[1]];
}

function getsubmenu()
{
	return !this.sub ? null : st_menus[this.sub[0]].bodys[this.sub[1]];
}

function getme()
{
	return st_menus[this.mei];
}

function getsrc(sr,me)
{
	if(sr=='')
		return '';
	_sr=sr.toLowerCase();
	if(_sr.indexOf('http://')==0||(_sr.indexOf(':')==1&&_sr.charCodeAt(0)>96&&_sr.charCodeAt(0)<123)||_sr.indexOf('ftp://')==0||_sr.indexOf('/')==0||_sr.indexOf('gopher')==0)
		return sr;
	else
		return me.web_path+sr;
}

function getcl()
{
	var ret;
	if(nNN||nOP)
		ret=w_.pageXOffset;
	else
		ret=d_.body.scrollLeft;
	return atoi(ret);
}

function getct()
{
	var ret;
	if(nNN||nOP)
		ret=w_.pageYOffset;
	else
		ret=d_.body.scrollTop;
	return atoi(ret);
}

function getcw()
{
	var ret;
	if(nNN||nOP)
		ret=w_.innerWidth;
	else if(nIEW&&d_.compatMode=="CSS1Compat")
		ret=d_.documentElement.clientWidth;
	else
		ret=d_.body.clientWidth;
	return atoi(ret);
}

function getch()
{
	if(nNN||nOP)
		ret=w_.innerHeight;
	else if(nIEW&&d_.compatMode=="CSS1Compat")
		ret=d_.documentElement.clientHeight;
	else
		ret=d_.body.clientHeight;
	return atoi(ret);
}

function sh(mb)
{
	var ly=mb.getlayer();
	if(nNN4)
		ly.visibility='show';
	else
		ly.style.visibility='visible';
}

function hd(mb)
{
	var ly=mb.getlayer();
	if(nNN4)
		ly.visibility='hide';
	else
	{
		if(nIE5&&!nMac)	ly.filters['Alpha'].opacity=0;
		ly.style.visibility='hidden';
		if(nIE5&&!nMac)	ly.filters['Alpha'].opacity=mb.opacity;
	}
}

function get_sdstr(mb,issh)
{
	return	"var _mb=st_menus['"+mb.mei+"'].bodys["+mb.mbi+"];_mb.tmid=null;_mb.spec_"+(issh? "sh" : "hd")+"();_mb.exec_ed=true;"
}

function getly(id,doc)
{
	if(doc.layers[id])
		return doc.layers[id];
	for(i=doc.layers.length;i>=0;i--)
	{
		var ly=getly(id,doc.layers[i].document);
		if(ly)
			return ly;
	}
	return null;
}

function getlayerMB()
{
	return getob(this.block);
}

function getlayerIT()
{
	if(nNN4)
		return this.getpar().getlayer().document.layers[0].document.layers[this.block];
	else
		return getob(this.block);
}

function get_st_lay()
{
	if(this.type=='sepline')
		return null;
	var st_arr=[];
	var doc=this.getlayer().document.layers[0].document;
	for(i=0;i<2;i++)
	{
		st_arr[st_arr.length]=doc.layers[this.block+st_state[i]];
		st_arr[st_arr.length]=doc.layers[this.block+st_state[i]+'M'];
	}
	return st_arr;
}

function addquo(n)
{
	return "\""+n+"\"";
}

function getob(id)
{
	if(nNN6)
		return d_.getElementById(id);
	else if(nNN4)
		return getly(id,document);
	else
		return d_.all[id];
}

function moveto(xy)
{
	if(xy&&(this.mbi||this.getme().pos==ab_))
	{
		tp_=this.getlayer();
		if(nNN4)
			tp_.moveToAbsolute(xy[0],xy[1]);
		else if(nOP)
		{
			tp_=tp_.style;
			tp_.pixelLeft=xy[0];
			tp_.pixelTop=xy[1];
		}
		else
		{
			tp_=tp_.style;
			tp_.left=xy[0]+'px';
			tp_.top=xy[1]+'px';
		}
		this.rc=[xy[0],xy[1],this.rc[2],this.rc[3]];
	}
}

function createIMG(src,id,width,height,border)
{
	var s='<IMG SRC=';
	s+=addquo(src);
	if(id!='')
		s+=' ID='+id;
	if(width&&height)
	{
		if(width>0)
			s+=' WIDTH='+width;
		if(height>0)
			s+=' HEIGHT='+height;
	}
	s+=' BORDER='+border+'>';
	return s;
}

function show()
{
	var delay=this.mbi&&this.getpar().getpar().arrange=="vertically" ? this.getme().shdelay_v : this.getme().shdelay_h;
	this.exec_ed=false;
	this.getxy();
	if(this.tmid)
		clearTimeout(this.tmid);
	if(delay>0)
		this.tmid=setTimeout(get_sdstr(this,true),delay);
	this.isshow=true;
	if(delay<=0)
		eval(get_sdstr(this,true));
}

function hide()
{
	if(this.isshow&&!this.exec_ed)
		ck_win_els(-1,this);
	this.exec_ed=false;
	if(this.tmid)
		clearTimeout(this.tmid);
	this.isshow=false;
	eval(get_sdstr(this,false));
}

function fixmenu(menu)
{
	for(mbi=0;mbi<menu.bodys.length;mbi++)
	{
		var body=menu.bodys[mbi];
		if(nOP&&nVer<6.0)
			body.getlayer().style.pixelWidth=atoi(getob(body.block+"TB").style.pixelWidth);
		if(nIEW&&nIE5)
			body.getlayer().style.width=body.getlayer().offsetWidth;
		else if(nIEM||!nIE)
		{
			if(nNN6&&nVer>=6.2)
				body.getlayer().style.MozOpacity=body.opacity+"%";
			if(body.arrange!="vertically")
			{
				var iti=0;
				var fixit=getob(body.block+iti);
				var h=atoi(nOP ? fixit.style.pixelHeight : fixit.offsetHeight);
				if(h)
				{
					for(iti=0;iti<body.items.length;iti++)
					{
						item=body.items[iti];
						lys=item.getlayer().style;
						tm_h=h-2*body.spacing;
						if(nOP)
							lys.pixelHeight=tm_h;
						else if(item.type=="sepline"||nIE)
							lys.height=tm_h+px_;
						else
							lys.height=tm_h-2*item.bd_sz+px_;
	
						if(nIEM)
						{
							var fh=h-2*body.spacing;
							lltd=getob(item.block+"LLTD");
							ltd=getob(item.block+"LTD");
							rtd=getob(item.block+"RTD");
							rrtd=getob(item.block+"RRTD");
							if(lltd)
								lltd.style.height=fh+px_;
							if(ltd)
								ltd.style.height=fh+px_;
							getob(item.block+"MTD").style.height=fh+px_;
							if(rtd)
								rtd.style.height=fh+px_;
							if(rrtd)
								rrtd.style.height=fh+px_;
						}
					}
				}
			}
			else if(nOP)
			{
				for(iti=0;iti<body.items.length;iti++)
				{
					var item=body.items[iti];
					if(item.type!="sepline")
					{
						var fixit=getob(body.block+iti);
						var it=item.getlayer();
						var h=atoi(it.style.pixelHeight);
						var w=atoi(fixit.style.pixelWidth);
						if(h)
							it.style.pixelHeight=h;
						if(w)	
							it.style.pixelWidth=w-2*body.spacing;
					}
				}
			}
		}
	}
	if(menu.type!="custom")
		menu.bodys[0].show();
}

function prefix(mei)
{
	var menu=st_menus[mei];
	var lit=menu.bodys[menu.bodys.length-1].items[menu.bodys[menu.bodys.length-1].items.length-1];
	while(!(lit.getlayer()&&typeof(lit.getlayer())!=ud_));
	setupEvent(menu);
	if(!nNN4)
		fixmenu(menu);
	if(nIEM)
		w_.onscroll=new Function("if(st_scrollid){clearTimeout(st_scrollid);}st_scrollid=setTimeout('scrollmenu();',500);");
	else if(!st_rl_id)
	{
		st_cl_w=getcw();
		st_cl_h=getch();
		st_cl_l=getcl();
		st_cl_t=getct();
		st_rl_id=setInterval("ckPage();",50);
	}
}

function scrollmenu()
{
	for(i in st_menus)
	{
		var menu=st_menus[i];
		if(menu&&menu.type=="float")
		{
			menu.hideall();
			_b=menu.bodys[0];
			ck_win_els(+1,_b);
			_b.moveto([getcl()+menu.pos_l,getct()+menu.pos_t]);
			ck_win_els(-1,_b);
		}
	}
}

function getbg(bg_cl,bg_img,bg_rep)
{
	s=bg_cl;
	if(bg_img!='')
		s+=" url("+bg_img+") "+bg_rep;
	return s;
}

function ck_win_els(change,obj)
{
	if(!st_loaded||nNN4||nOP)	return;
	if(obj.isstatic)		return;
	if(HideSelect)	win_ele_vis("SELECT", change, obj);
	if(HideObject)	win_ele_vis("OBJECT", change, obj);
	if(HideIFrame)	win_ele_vis("IFRAME", change, obj);
	if(HideInput)
	{
		win_ele_vis("INPUT", change, obj);
		win_ele_vis("TEXTAREA", change, obj);
	}
}

function win_ele_vis(tagName, change, obj)
{
	var els = nNN6 ? d_.getElementsByTagName(tagName) : d_.all.tags(tagName)
	var i
	for (i=0; i < els.length; i++)
	{
		var el = els.item(i)
		var flag;
		for(flag=0,tmobj=el.offsetParent;tmobj;tmobj=tmobj.offsetParent)
			if(tmobj.id&&tmobj.id.indexOf("STM")>=0)
				flag=1;
		if(flag)
			continue;
		else if(elements_overlap(el,obj))
		{
			if (el.visLevel)
				el.visLevel += change
			else
				el.visLevel = change
			if (el.visLevel == -1)
			{
				if(typeof(el.visSave)==ud_)
					el.visSave = el.style.visibility
				el.style.visibility = "hidden"
			}
			else if (el.visLevel == 0)
			{
				el.style.visibility = el.visSave;
			}
		}
	}
}

function elements_overlap(el,obj)
{
	var left = 0
	var top = 0
	var width = el.offsetWidth
	var height = el.offsetHeight
	if(width)
		el._width=width;
	else
		width=el._width;
	if(height)
		el._height=height;
	else
		height=el._height;
	
	while (el)
	{
		left += el.offsetLeft
		top += el.offsetTop
		el = el.offsetParent
	}
	return ((left < obj.rc[2]+obj.rc[0]) && (left + width > obj.rc[0]) && (top < obj.rc[3]+obj.rc[1]) && (top + height > obj.rc[1]))
}