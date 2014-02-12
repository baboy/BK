var MapList = function(){
	 this._list = new Array();
 }
MapList.prototype={
	get:function(k){
		var l = this._list;
		var n=l.length;
		for (var i=0;i<n ;i++ ){
			var e=l[i];
			if(e.key==k){
				return e.value;
			}
		}
		return null;
	},
	add:function(k,v){
		this._list[this._list.length] = new BAttribute(k,v);
	},
	remove:function(k){
		var l = this._list;
		var n=l.length;
		for (var i=0;i<n ;i++ ){
			var e=l[i];
			if(e.key==k){
				var l1=l.splice(i+1,n-1);
				l.length=i;
				l=l.concat(l1);
				this._list = l;
				return e.value;
			}
		}
		return null;
	},
	length:function(){return this._list.length;},
	getByIndex:function(i){
		if(i<0)i=this._list.length+i;
		if(i>=this._list.length||i<0)
			return null;
		else
			return this._list[i].value;
	}
}
var BWinMgr=function(){
	this._pid=1000;
	//所有当前活动窗口
	this._mapList=new MapList();
	this._winLib = {};
};
BWinMgr.getInstance = function(){
	if (!BWinMgr._instance) {
		BWinMgr._instance = new BWinMgr();
	};
	return BWinMgr._instance;
};
BWinMgr.prototype={
	remove:function(k){
		this._mapList.remove(k);
	},
	getByPname:function(k){
		var n=this._mapList.length();
		for (var i=0;i<n ;i++ ){
			var w=this._mapList.getByIndex(i);
			if(w.params.pname==k)
				return w;
		}
		return null;
	},
	exists:function(k){
		return this.getByPname(k)!=null;
	},
	//绑定窗口和状态栏
	bind : function(win, param){
		var handler = this;	
		var pid = this._pid++;
		win.pid = pid;
		//设置最小化事件
		win.setMinimizeEvent(function(){
			});
		//设置最大化事件
		win.setMaximizeEvent(function(){
			});
		//设置退出事件
		win.setExitEvent(function(){
			handler.remove(pid);	
		});
		//设置置顶事件
		win.setFocusEvent(function(){
			});
		//设置失焦事件
		this._mapList.add(pid,{"pid":pid,"win":win, "params":param});
		win.open();
	},
	init:function(){	
		var handler = this;
        BElement(document).addEvent("mousedown",function(){
        	//handler.setTopWindowFocus(false)
        });

	},
	//安装窗口
	setup:function(p){
		for(k in p){
			if(this._winLib[k] && this._winLib[k].type=="sys")continue;
			this._winLib[k] =p[k];
		}// end for
	},
	openWindow:function(p){
		if (typeof(p)=="string") {
			p = this._winLib[k];
		};
		if(!p)
			return false;
		//是否为单实例窗口
		if(p.unique){
			var v=this.getByPname(p.pname);
			if(v){
				this.setTopWindowFocus(false);
				v.win.setVisible(true);
				v.win.toFront(true);
				v.status.on();
				this._mapList.moveToLast(v.pid);
				return false;		
			}
		}
		//内置窗口
		var t = p.title?p.title:"未命名窗口";
		var win = new Bindow(p.width,p.height);
		win.init();
		win.setIcon(p.icon);
		win.setTitle(t);
		win.setSize(p.width,p.height);
		win.addContent(p.content);
		win.setMinSize(p.minwidth,p.minheight);
		win.open();
		this.bind(win,{pname:p.pname,unique:p.unique});
		if(p.fullscreen){
			win.maximize();
		}
		return win;
	},
	openOverlay:function(url, p){
		if (!p) {
			p = {};
		};
		var pid = this._pid++;
		var overlay = new Overlay(document.body);
		p._pid_ = pid;
		this._mapList.add(pid,{"pid":pid,"win":overlay, "params":p});
		overlay.open(url, p);
	},
	close:function(pid){
		if (!pid) {
			var p = window.location.param();
    		if (p && p._pid_) {
				parent.BWinMgr.getInstance().close(p._pid_);
    		};
			return;
		};
		var obj = this._mapList.remove(pid);
		var win = obj.win;
		win.close();
	}
}

var Overlay = function(container){
	this.container = container;
	this.pannel = document.getElementById("overlay");
	if (!this.pannel) {
		var pannel = document.createElement("div");
		pannel.setAttribute("id","overlay");
		this.pannel = pannel;
	};
};
Overlay.prototype = {
	getUrl:function(link, p){
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
		if (p) {
			for(var k in p){
				link += "&"+k+"="+p[k];
			}
		};
		link += hash;
		return link;
	},
	open:function(link, p){
		var url = this.getUrl(link, p);
		console.log(url);
		var iframe = document.createElement("iframe");
		iframe.src = url;
		this.pannel.appendChild(iframe);
		this.pannel.style.display = 'block';
		if (!this.pannel.parentNode) {
			this.container.appendChild(this.pannel);
		};
	},
	close:function(){
		this.pannel.parentNode.removeChild(this.pannel);
	}
};