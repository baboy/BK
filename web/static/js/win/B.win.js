/*******************************************
********************************************
*****   Notice      ************************
***** @author: baboy
***** @msn: baboyzyh@msn.com
***** @QQ:20331244
***** @site: http://www.uiabc.cn
*****
***** 写于一深夜 2007年毕业之际 @_@
*****
***** API
*****
*****
*****        |------------------------------------------------------------|
*****        |  class: Bindow                                             |
*****        |------------------------------------------------------------|
*****        | width : windows' width                                     |
*****        | height: window's height                                    |
*****        | _frame: winow's frame                                      |
*****        | _title: window' title                                      |
*****        | _exit : window's exit button                               |
*****        | _panel: window's client area                               |
*****        | _status: window's status bar                               |
*****        | _enter:                                                    |
*****        | _cancel:                                                   |
*****        | _buttonBar                                                 |
*****        |                                                            |
*****        |------------------------------------------------------------|
*****        | init():initialize a window                                 |
*****        | open(): display the window after it is initialized         |
*****        | setTitle(string): set the window's title                   |
*****        | setContent(obj):set the window client area's content       |
*****        | set(para1,para2):                                          |
*****        | setExitEvent(func) set closeBtn's event                    |
*****        | setEnterEvent(func) set EnterBtn's event                   |
*****        | setCancelEvent(func) set cancelBtn's event                 |
*****        | setCenter():                                               |
*****        | setStatus(msg):                                            |
*****        | setSize(width,height)                                      |
*****        | setWidth(width)                                            |
*****        | setHeight(height)                                          |
*****        | dispose()                                                  |
*****        |------------------------------------------------------------|
*****/
var Bindow = function(w,h){
	this._width = w?w:300;
	this._height = h;
	this._frame = null;
	this._title = null;
	this._content = null;
	this._status = null;
	this._buttonBar = null;
	this._enter = null;
	this._cancel = null;
	this._yes=null;
	this._no=null;
	this._focusData=null;
	this._blurDate = null;
	this._isVisible=false;
	this._isFullScreen=false;
	this._point=null;
	this._toolbar=null;
	this._resizeCallback = new Array();
	this._minwidth=300;
	this._minheight=200;
	this._resize=null;
	this._oriSize={width:this._width,height:this._height};
	this._oriLoc=null;
}
Bindow._zIndex=900;
Bindow.prototype.init=function(){
	this.create();
}
Bindow.prototype.create = function(){
	var handler = this;
	var f = document.createElement("div");
	f.className="BINDOW";
	f.style.zIndex = (Bindow._zIndex++);
	var grid = document.createElement("div");
	var grid2 = grid.cloneNode(true);
	var grid3 = grid.cloneNode(true);
	var grid4 = grid.cloneNode(true);
    var grid5 = grid.cloneNode(true);
	var grid6 = grid.cloneNode(true);
	var grid7 = grid.cloneNode(true);
	var grid8 = grid.cloneNode(true);
	var grid9 = grid.cloneNode(true);
	grid.className = "BINDOW-TOPLEFT";
    grid2.className="BINDOW-TITLEBAR";
	grid3.className = "BINDOW-TOPRIGHT";
	grid4.className = "BINDOW-LEFT";
    grid5.className = "BINDOW-PANEL";
	grid6.className = "BINDOW-RIGHT";
	grid7.className = "BINDOW-BOTTOMLEFT";
	grid8.className = "BINDOW-BOTTOM";
	grid9.className = "BINDOW-BOTTOMRIGHT";
	f.appendChild(grid);
    f.appendChild(grid3);
	f.appendChild(grid2);//titlebar
	f.appendChild(grid4);
    f.appendChild(grid5);//panel
	f.appendChild(grid6);
	f.appendChild(grid7);
	f.appendChild(grid8);
	f.appendChild(grid9);

	//设置改变client区域大小时的回调函数
	var resize = new Resizable(f,grid9);
	resize.setMinSize(this._minwidth,this._minheight);
	resize.setCallback(function(){handler.callResize();});
	resize.enable();
	this._resize = resize;

	var icon = document.createElement("img");
	icon.className = "BINDOW-ICON";
	icon.style.display = "none";

	var title  = document.createElement("div");
	title.className = "BINDOW-TITLE";
    BDrag({anchor:title,target:f,beforeMouseDown:function(){return !handler.isFullScreen}});
    /*
	title.onmousedown=function(){
		var event=arguments[0]||window.event;
		if(handler.isFullScreen)return false;
		BDrag(event,this,f);
	}
    */
	var minimize = document.createElement("span");
	var maximize = minimize.cloneNode(true);
	var exit = minimize.cloneNode(true);
	minimize.className = "BINDOW-BTN-MINIMIZE";
	//minimize.src = "common/images/win1/minimize.gif";
	maximize.className = "BINDOW-BTN-MAXIMIZE";
	maximize.src = "common/images/win1/maximize.gif";
	exit.className = "BINDOW-BTN-EXIT";
	exit.src = "common/images/win1/exit.gif";

	grid2.appendChild(icon);
	grid2.appendChild(title);
	grid2.appendChild(minimize);
	grid2.appendChild(maximize);
	grid2.appendChild(exit);


	var content = document.createElement("div");
	var toolbar = content.cloneNode(true);
	toolbar.className = "BINDOW-TOOLBAR";
	toolbar.style.display="none";
	content.className = "BINDOW-PANEL-CONTENT";
	grid5.appendChild(toolbar);
	grid5.appendChild(content);

	this._frame = f;
	this._content = content;
	this._toolbar = toolbar;
	this._title = title;
	this. _minimize = minimize;
	this._maximize = maximize;
	this._exit = exit;
	this._icon = icon;
	this._frame.style.display = "none";
	document.body.appendChild(this._frame);
	this.setSize(this._width,this._height);
	this.setCenter();
	this.setExitEvent();
	this.setMinimizeEvent();
	this.setFocusEvent();
	BElement(window).addEvent("resize",function(){if(handler.isFullScreen)handler.maximize();});
}
//设置图标
Bindow.prototype.setIcon=function(src){
	if(src){
		this._icon.style.display = "block";
		this._icon.src = src;
	}else{
		this._icon.style.display = "none";
	}
}
Bindow.prototype.setCenter = function(){
		var w = BElement(this._frame).width();
		var h = BElement(this._frame).height();

		if(!w)w = parseInt(this._width);
		if(!h)h = parseInt(this._height);
		if(isNaN(w)){w = 300;}
		if(isNaN(h)){h = 250;}
		var l = parseInt((BElement(window).width()-w)/2);
		var t = parseInt((BElement(window).height()-h)/3);
		this._frame.style.position = "absolute";
		this._frame.style.left = l+"px";
		this._frame.style.top = t+"px";
}
// 关闭窗口
Bindow.prototype.close=Bindow.prototype.exit = function(){
	if(this._frame)document.body.removeChild(this._frame);
}
//隐藏窗口
Bindow.prototype.setVisible =function(t){
		if(t)this._frame.style.display="block";
		else this._frame.style.display="none";
		this._isVisible = t;
}
//显示窗口

//显示窗口
Bindow.prototype.open = function(){
	if(this._frame==null)this.init();
	if(this._frame.style.display == "none"){
		this.setCenter();
		this.setVisible(true);
	};
	this.callResize();
}
//设置标题
Bindow.prototype.setTitle = function(t){
	if(!t) return false;
	t = BString(t).trim();
	this._title.innerHTML = BString(t).length>40?(BString(t).cut(37)+"..."):t;
	this._title.title=t;
}
//设置客户区内容
Bindow.prototype.setContent = function(content){
	if(!content) return false;
	this._content.innerHTML = "";
	//如果传入的是dom对象
	if(typeof(content) == "object"){
		this._content.appendChild(content);
	}
	else{//如果传入的是字符串或其它简单类型的数据
		this._content.innerHTML = content;
	}
}
Bindow.prototype.setLink = function(link,p){

	var i = link.indexOf("#");
	var hash = "";
	if (i>0) {
		hash = link.substring(i);
		link = link.substring(0,i);
	}
	i = link.indexOf("?");
	if (i<0) {
		link = link+'?';
	}
	link += "&_pid_="+this.pid;
	if (p) {
		for(var k in p){
			link += "&"+k+"="+p[k];
		}
	};
	link += hash;
	this._content.innerHTML = "<iframe src='"+link+"'></iframe>";
}
//添加内容到客户区
Bindow.prototype.addContent = function(content){
	if(!content) return false;
	//如果传入的是dom对象
	if(typeof(content) == "object"){
		this._content.appendChild(content);
	}
	else{//如果传入的是字符串或其它简单类型的数据
		var div = document.createElement("div");
		div.innerHTML = content;
		this._content.appendChild(div);
	}
}
//快速设置窗口信息
Bindow.prototype.set = function(title,content){
	this.setTitle(title);
	this.setContent(content);
}
//设置窗口位置
Bindow.prototype.setLocation = function(x,y){
	this._frame.style.left = x+"px";
	this._frame.style.top = y+"px";
}
//设置状态栏信息
Bindow.prototype.setStatus = function(msg){
	this._status.innerHTML = msg;
}
//设置窗口宽度
Bindow.prototype.setWidth = function(w){
	if(w)this._frame.style.width = w+"px";
}
//设置窗口高度
Bindow.prototype.setHeight = function(h){
	if(h)this._frame.style.height = h+"px";
}
//设置窗口大小
Bindow.prototype.setSize = function(w,h){
	this.setWidth(w);
	this.setHeight(h);
}
//退出时间
Bindow.prototype.setExitEvent = function(func,param){
	var handle = this;
	this._exit.onclick = function(){
		//防止冒泡
		try{window.event.cancelBubble=true;}catch(e){arguments[0].stopPropagation();}
		if(typeof(func)=="function")if(func(param)===false)return false;
		handle.close();
	}
}
Bindow.prototype.maximize=function(){
	this.setLocation(0,0);
	this.setSize(BElement(window).width(),BElement(window).height());
	this.isFullScreen = true;
	this.callResize();
}
Bindow.prototype.recover=function(){
	this.setLocation(this._point.x,this._point.y);
	this.setSize(this._width,this._height);
	this.isFullScreen = false;
	this.callResize();
}
//最小化时间
Bindow.prototype.setMinimizeEvent = function(f,p){
	var handle = this;
	this._minimize.onclick = function(){
		//放置冒泡
		try{window.event.cancelBubble=true;}catch(e){arguments[0].stopPropagation();}
		if(typeof(f)=="function")if(f(p)===false) return false;
		handle.setVisible(false);
	}
}
//最大化事件
Bindow.prototype.setMaximizeEvent = function(f,p){
	var handler = this;
	this._maximize.onclick=this._title.ondblclick=function(){
		if(typeof(f)=="function")if(f(p)===false) return false;
		var t=handler._frame.style;
		if(handler.isFullScreen){
			handler.recover();
		}
		else{
			handler._width = BElement(handler._frame).width();
			handler._height = BElement(handler._frame).height();
			handler._point={x:BElement(handler._frame).getLocationX(),y:BElement(handler._frame).getLocationY()};
			handler.maximize();
		}
	}
}
//焦点事件
Bindow.prototype.setFocusEvent=function(f,p){
	var handler = this;
	this._frame.onmousedown=function(){
		try{window.event.cancelBubble=true;}catch(e){arguments[0].stopPropagation();}
		if(typeof(f)=="function")if(f(p)===false) return false;
		handler.focus();
	}
}
//失焦事件
Bindow.prototype.setBlurEvent=function(f,p){
	this._blurData = {func:f,param:p};
}
Bindow.prototype.toFront=function(){
	this._frame.style.zIndex = Bindow._zIndex++;
}
Bindow.prototype.focus=function(){
	this.toFront();
}
Bindow.prototype.blur=function(){
	if(this._blurData)this._blurData.func(this._blurData.param);
}

Bindow.prototype.isVisible=function(){
	return this._isVisible;
}
Bindow.prototype.setToolbarVisible=function(t){
	if(t){
		this._toolbar.style.display="block";
		this._content.style.top = "26px";
	}
	else{
		this._toolbar.style.display="none";
		this._content.style.top = 0;
	}
}
Bindow.prototype.addTool=function(o){
	if(typeof(o)=="object"){
		o.className= o.className+" BINDOW-TOOLBAR-ITEM";
		this._toolbar.appendChild(o);
	}
}
Bindow.prototype.setResizeCallback=function(f){
	this._resizeCallback[this._resizeCallback.length]=f;
}
//获取客户区大小
Bindow.prototype.getClientSize=function(){
	return {width:BElement(this._content).width(),height:BElement(this._content).height()};
}
Bindow.prototype.getClient=function(){
	return this._content;
}
//改变窗体大小时的回调函数
Bindow.prototype.callResize=function(){
	var n=this._resizeCallback.length;
	for (var i=0;i<n ;i++ ){
		this._resizeCallback[i]();
	}
}

Bindow.prototype.setMinSize=function(w,h){
	this._minwidth=w;
	this._minheight=h;
	if(this._resize)this._resize.setMinSize(w,h);
}

var BAlert = function(w,h){
	this._width = w?w:300;
	this._height = h;
	this._frame = null;
	this._title = null;
	this._content = null;
	this._status = null;
	this._buttonBar = null;
	this._enter = null;
	this._cancel = null;
	this._yes=null;
	this._no=null;
	this._focusData=null;
	this._blurDate = null;
	this._isVisible=false;
	this._isFullScreen=false;
	this._point=null;
	this._toolbar=null;
	this._resizeCallback = new Array();
	this._minwidth=300;
	this._minheight=200;
	this._resize=null;
	this._oriSize={width:this._width,height:this._height};
	this._oriLoc=null;
	this._shadow=null;
	this. _minimize=null;
	this. _maximize=null;
}
BAlert.prototype = new Bindow();
BAlert.prototype.init=function(){
	var d= document.createElement("div");
	d.className="BINDOW-SHADOW";
	d.style.zIndex=((Bindow._zIndex++)*100);
	document.body.appendChild(d);
	this._shadow = d;
	this.create();
	this._frame.style.zIndex = ((Bindow._zIndex++)*100);
	this._content.style.background="#EEE";
	this. _minimize.parentNode.removeChild(this. _minimize);
	this. _maximize.parentNode.removeChild(this. _maximize);
	this._content.style.padding="5px";
}
BAlert.prototype.open=function(){
	if(!this._frame){this.init();}
	if(this._frame.style.display == "none"){
		this.setCenter();
		this.setVisible(true);
		this._shadow.style.display="block";
	};
}
BAlert.prototype.close = BAlert.prototype.exit=function(){
	if(this._frame)document.body.removeChild(this._frame);
	if(this._shadow)document.body.removeChild(this._shadow);
}
//焦点事件
BAlert.prototype.setFocusEvent=function(f,p){
	return false;
}
