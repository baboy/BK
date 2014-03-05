var BAttribute=function(key,value){
	this.key=key;
	this.value=value;
};

var BHashMap=function(){
	this.A=new Array();
};
BHashMap.prototype.set=function(key,value){
	var e=new BAttribute(key,value);
	this.A[this.A.length]=e;
};
BHashMap.prototype.remove=function(key){
	var len=this.A.length;
	for (var i=0;i<len ;i++ ){
		var e=this.A[i];
		if(e.key==key){
			var a=this.A[i];
			var A1=this.A.splice(i+1,len-1);
			this.A.length=i;
			this.A=this.A.concat(A1);
			return a;
		}
	}
	return null;
};
BHashMap.prototype.get=function(key){
	var len=this.A.length;
	for (var i=0;i<len ;i++ ){
		var e=this.A[i];
		if(e.key==key){
			return e.value;
		}
	}
	return null;
};
BHashMap.prototype.size=function(){
	return this.A.length;
}
BHashMap.prototype.getAttribute=function(index){
	if(index>=this.A.length)return null;
	return this.A[index];
}
//获取鼠标位置
var BMouse={
	getLocation:function(event){
		var event= event||window.event;
		if(event.pageX){
			return {x:event.pageX,y:event.pageY};
		}
		else{
			return {x:event.clientX+
							(document.documentElement.scrollLeft?document.documentElement.scrollLeft:document.body.scrollLeft),
					   y:event.clientY+
							(document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop)
					};
		}
	}
};
// 获取对象的位置
 (function(){
	var BMouse  = window.BMouse = function(evt){
		return new BMouse.fn.init(evt);
	}
	BMouse.fn = BMouse.prototype={
		init:function(evt){
		   if(evt){
				return BMouse().get(evt);
		   }
		   else{
				return BMouse.fn;
		   }
		},
		evt:null,
		get:function(evt){
			this.evt = evt;
			return this;
		},
		getLocation:function(){
			if(this.evt.pageX){
				return {x:this.evt.pageX,y:this.evt.pageY};
			}
			else{
				return {x:this.evt.clientX+
								(document.documentElement.scrollLeft?document.documentElement.scrollLeft:document.body.scrollLeft),
						   y:this.evt.clientY+
								(document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop)
						};
			}
		}
	}
var BElement = window.BElement = function(selector){
    return new BElement.fn.init(selector);
};
BElement.ZIndex  = 1;
BElement.getZIndex = function(){
    return BElement.ZIndex++;
}
BElement.fn = BElement.prototype = {
	init:function(selector){
       if(selector){
            return BElement().find(selector);
       }
       else{
            return BElement.fn;
       }
	},
    selector:null,
    find:function(selector){
        if ( typeof selector === "string" ) {
            selector = document.getElementById(selector);
        }
        this.selector = selector;
        return this;
    },
	getLocationY: function(){
		var obj = this.selector;
		var y = obj.offsetTop;
		while(obj = obj.offsetParent){
			y += obj.offsetTop;
		}
		return y;
	},
	getLocationX :function(){
		var obj = this.selector;
		var x = obj.offsetLeft;
		while(obj = obj.offsetParent){
			x += obj.offsetLeft;
		}
		return x;
	},
	getLocation : function(){
		var obj = this.selector;
		return{x:this.getLocationX(),y:this.getLocationY()}
	},
    setLocation:function(loc){
        this.selector.style.position = "absolute";
        this.selector.style.zIndex = BElement.getZIndex();
        this.selector.style.left = loc.x+"px";
        this.selector.style.top = loc.y+"px";
    }
    ,
	addEvent:function(event,func){
		if(this.selector.attachEvent){
			  this.selector.attachEvent("on"+event,func);
		}
		else{
			this.selector.addEventListener(event,func,false);

		}
	},
	_PopMenu:null,
	popMenu:function(e,p,w){
		if(this._PopMenu==null)
			this._PopMenu=new PopMenu(null,{css:"POPMENU"});
		this._PopMenu.pop(this.selector,e,p,w);

	},
	dropMenu:function(e,p,w){
		if(this._PopMenu==null)
			this._PopMenu=new PopMenu(null,{css:"POPMENU"});
		this._PopMenu.drop(this.selector,e,p,w);
	},
	css:function(k,v){
		try{
			this.selector.style[k]=v;
		}
		catch(e){
			alert(e+"\n"+k)
		}
	}
}
BElement.fn.each = function(arr,func){
	for (i in arr ){func(i,arr[i])	}
}
BElement.fn.each(
	["Width","Height"],
	function(i,name){
		var type = name.toLowerCase();
		BElement.fn[type]=function (size) {
			return this.selector == window ?
			document.compatMode == "CSS1Compat" && document.documentElement[ "client" + name ] ||
			document.body[ "client" + name ] :
			this.selector == document ?
				Math.max(
					document.documentElement["client" + name],
					document.body["scroll" + name], document.documentElement["scroll" + name],
					document.body["offset" + name], document.documentElement["offset" + name]
				) :

				size === undefined ?
					(this.selector ? this.selector["offset" + name] : null) :
					this.css( type, typeof size === "string" ? size : size + "px" );
		}
	}
)
	/*******BString**********/
  var    BString = window.BString = function(s){
		return new BString.fn.init(s);
	};
	BString.fn = BString.prototype = {
		init:function(s){
		   if(s!=null && s!=undefined){
				return BString().get(s);
		   }
		   else{
				return BString.fn;
		   }
		},
		str:null,
		length:0,
		get:function(s){
			this.str=s.toString();
			this.length = this.len();
			return this;
		},
		cut:function(len){
			var s=this.str;
			var n=s.length,j=0;
			for (var i=0; i<n&&j<len;i++ ){
				if(s.charCodeAt(i)>255)j += 2;
				else j += 1;
			}
			if(j>len)--i;
			return s.substr(0,i);
		},
		len:function(){
			var s=this.str;
			var n=s.length,len=0;
			for (var i=0; i<n;i++ ){
				if(s.charCodeAt(i)>255)len += 2;
				else len += 1;
			}
			return len;
		},
		trim:function(){
			return this.str.replace(/(^\s*)|(\s*$)/g, "");
		},
		toJson:function(){
			var ret=null;
			try{
				ret = eval("("+this.str+")");
			}
			catch(e){
				ret=null;
			}
			return ret;
		},
		isEmail:function(){
			if (!this.str || !this.trim()) {
				return false;
			}
			var re = /^[a-zA-Z0-9\._-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/i;
			if (re.test(this.trim()))
				return true;
			else
				return false;
		},
		getEmailDomain:function(){
			if(!this.isEmail())
				return null;
			var re = /^[a-zA-Z0-9\._-]+@([a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+)$/i;
			var a=re.exec(this.str);
			return a.length>=2?a[1]:null;
		},
		getEmailSp:function(){
			if(!this.isEmail())
				return null;
			var re = /^[a-zA-Z0-9\._-]+@([a-zA-Z0-9_-]+)(\.[a-zA-Z0-9_-]+)+$/i;
			var a=re.exec(this.str);
			return a.length>=2?a[1]:null;
		}
	}
	var BNumber=window.BNumber=function(n){return new BNumber.fn.init(n);}
	BNumber.fn = BNumber.prototype = {
		init:function(n){
		   if(n!=null){
				return BNumber().get(n);
		   }
		   else{
				return BNumber.fn;
		   }
		},
		number:null,
		get:function(n){
			if(!isNaN(n))this.number=parseInt(n);
			return this;
		},
		toSize:function(){
			var n=this.number;
			var z="",x=0,y=0;
			var g=1024*1024*1024,m=1024*1024,k=1024;
			var a=1;
			if(n>=g){
				a=g;
				z=" GB";
			}
			else if(n>=m){
				a=m;
				z=" MB"
			}
			else if(n>=k){
				z=" KB";
				a=k
			}
			else{
				z=" B";
			}
			x=Math.floor(n/a);
			y=(n%a)/a;
			y=Math.floor(y*100);
			y=(y==0?"":("."+y));
			return x+y+z;
		}
	}
	var BObject = window.BObject = function(o){
		return new BObject.fn.init(o);
	}
	BObject.fn=BObject.prototype={
		init:function(o){
			if(o!=null){
				return BObject().get(o);
			}
			else{
				return BObject.fn;
			}
		},
		obj:null,
		get:function(o){
			this.obj=o;
			return this;
		},
		clone:function(t){
			function _(o){
				if (!o) {
					return o;
				};
				var obj=null;
                var n=o.length;
                if(n==undefined){
                    obj=new Object();
                    for ( i in o){
                        if(typeof(o[i])=="object") obj[i] = _(o[i]);
                        else obj[i]=o[i];
                    }
                }
                else{
                    obj=new Array();
                    for (var i=0;i<n ;i++ ){
                         if(typeof(o[i])=="object")obj[i] = _(o[i]);
                        else  obj[i]=o[i];
                    }
                }
				return obj;
			}
			return _(this.obj);
		},
		getObjectInArray:function(k,v){
			if(!(this.obj instanceof Array))
				return null;

			function _(o){
				if(!o)return null;
				if(o[k]==v)
					return o;
				if(o[k]!=undefined)
					return null;

				for(j in o){
					if(!o[j])continue;
					if(typeof(o[j])=="object"){
						var r = _(o[j]);
						if(r!=null && r!=undefined)
							return r;
					}//end if

				}// end for
				return null;
			}//end _
			var n = this.obj.length;
			for (var i=0;i<n ;i++ ){
				if(_(this.obj[i])!=null)
					return this.obj[i];
			}
			return null;
		},
        extend : function( obj ){
            var newObj = {};
            for ( k in obj ){
                newObj[k] = obj[k];
            }
            for ( k in this.obj ){
                newObj[k] = this.obj[k];
            }
            return newObj;
        }
	}

	var Alert  = window.Alert=function(s,t){
		var a=new BAlert();
		a.open();
		a.setTitle(t?t:"提示");
		a.setContent(s);
	}

})();

var ulog = function(s){
    document.getElementById("ID_AREA3").innerHTML += s;
}
var BDrag=function(opt){//鼠标按下
    var _drag = function(op){
        var event = op.event,_anchor = op.anchor,_target = op.target;
        var flag=true;// if press the mouse left key
        var _mouse_ori=BMouse(event).getLocation(),_location=BElement(_target).getLocation();
        var _offsetX=_mouse_ori.x-_location.x;//location of mouse to the object
        var _offsetY=_mouse_ori.y-_location.y;//
        var move=function(){//鼠标移动
            if(!flag)return false;
            event=arguments[0]||window.event;
            var _mouse=BMouse(event).getLocation();
            var x=_mouse.x-_offsetX;
            var y=_mouse.y-_offsetY;
            _target.style.position="absolute";
            _target.style.left=x+"px";
            _target.style.top=y+"px";
        }
        var up=function(){//鼠标弹起
            flag=false;
            try{
                _anchor.releaseCapture();
            }
            catch(e){
                document.removeEventListener("mousemove",move,true);
			    document.removeEventListener("mouseup",up,true);
                window.releaseEvents(Event.MOUSEMOVE|Event.MOUSEUP);
            }
            if(typeof(op.afterMouseUp)=="function"){
                op.afterMouseUp({loc:BElement(_target).getLocation(),anchor:_anchor,target:_target});
            }
        }
        try{
            _anchor.setCapture();
            _anchor.onmousemove=move;
            _anchor.onmouseup=up;
        }
        catch(e){
            document.addEventListener("mousemove",move,true);
            document.addEventListener("mouseup",up,true);
            window.captureEvents(Event.MOUSEMOVE|Event.MOUSEUP);
        }
    }//end _drag
    if(opt.hasAddMouseDownEvent==true){
        _drag(opt);
    }
    else{
        opt.anchor.onmousedown = function(){
            var evt = window.event;
            if(arguments.length==1)
                evt = arguments[0];
            if(typeof(opt.beforeMouseDown)=="function"){
                var ret = opt.beforeMouseDown(
                                        {
                                            event:evt,
                                            anchor:opt.anchor,
                                            target:opt.target,
                                            loc:BElement(this).getLocation()
                                        }
                                );
                //判断是否被拦截
                if(ret==false)
                    return;
                //是否要更换参数
                if(typeof(ret)=="object"){
                    try{
                        if(ret.anchor) {
                            var loc = BElement(this).getLocation();
                            opt.anchor = ret.anchor;
                            BElement(opt.anchor).setLocation(loc);
                            //ulog("ulog:"+loc.x+","+loc.y);
                        }
                        if(ret.target) {
                            var loc = BElement(this).getLocation();
                            opt.target = ret.target;
                            BElement(opt.target).setLocation(loc);
                            //ulog("ulog:"+loc.x+","+loc.y);
                        }
                    }catch(e){
                    }
                }//end if

            }
            try{evt.preventDefault();}
            catch(e){}
            opt.event = evt;
            _drag(opt);
        }
    }

}

var $B=new Object();
$B.create = function(type){
	if(type=="window")return new BWindow();
	if(type=="mwindow")return new BMWindow();
	if(type=="dialog")return new Dialog();
}
$B.prompt = function(title,func,params){
	return new BPrompt(title,func,params);
}
$B.pageSize=function(){	return{width:document.body.scrollWidth,height:document.body.scrollHeight};}
$B.addEvent=function(element,event,func){
    if(element.attachEvent){
          element.attachEvent("on"+event,func);
    }
    else{
        element.addEventListener(event,func,false);
    }
}

/******************string*******************/
$B.str=new Object();
$B.str.copy=function(s){
	 if (window.clipboardData){
		 window.clipboardData.setData("Text", s);
		 return true;
	 }
	 else if (window.netscape){
		try{netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');}
		catch(e){
			alert("被浏览器拒绝！\n1.请在浏览器地址栏输入'about:config'并回车\n2.将'signed.applets.codebase_principal_support'设置为'true'");
			return false;
		}
	    var clip = Components.classes['@mozilla.org/widget/clipboard;1']
					 .createInstance(Components.interfaces.nsIClipboard);
	    if (!clip) return false;
	    var trans = Components.classes['@mozilla.org/widget/transferable;1']
					  .createInstance(Components.interfaces.nsITransferable);
	    if (!trans) return false;
	    trans.addDataFlavor('text/unicode');
	    var str = new Object();
	    var len = new Object();
	    var str = Components.classes["@mozilla.org/supports-string;1"]
					.createInstance(Components.interfaces.nsISupportsString);
	    var copytext=s;
	    str.data=copytext;
	    trans.setTransferData("text/unicode",str,copytext.length*2);
	    var clipid=Components.interfaces.nsIClipboard;
	    if (!clip) return false;
	    clip.setData(trans,null,clipid.kGlobalClipboard);
	    return true;
	 }
	 return false;
}

Location.prototype.param = function(){
	var q = window.location.search;
	if (!q) {
		return null;
	};
	q = q.substring(1);
	var a = q.split("&");
	var p = {};
	for(var i = 0,n = a.length; i<n; i++){
		var s = a[i];
		var item = s.split("=");
		var k = item[0];
		var v = item.length>1?item[1]:null;
		p[k] = v;
	}
	return p;
}
String.prototype.json = function(){
	var json = null;
	try{
		json = eval("("+this+")");
	}catch(e){
		console.log(e);
		json = null;
	}
	return json;
};
function _s(s){
	return (s==null || s==undefined)?"":s;
}
var HashEventManager = function(){
	this.events = {};
};
HashEventManager.getInstance = function(){
	if (!window.hashEventManager) {
		window.hashEventManager = new HashEventManager();
		window.onhashchange = function(){
			var h = window.location.hash;
			h = h.substring(1);
			HashEventManager.getInstance().fire(h);
		};
	};
	return window.hashEventManager;
};
HashEventManager.prototype = {
	addEventListener:function(hash,func){
		this.events[hash] = func;
	},
	fire:function(hash){
		if (!hash) {
			hash = "_default_";
		};
		var p = hash.split("/");
		var k = p[0];
		p.splice(0,1);
		console.log(this.events);
		var func = this.events[k];
		if (!func) {
			k = "_default_";
			func = this.events[k];
		};
		if (func) {
			return func.apply(null,p);
		};
		console.log("fire:"+k+","+func);
		/**
		/edit/{module}/sid
		
		**/
	}
};

var Validator = function (){}
Validator.getInstance = function(){
	if(!Validator._instance_){
		Validator._instance_ = new Validator();
	}
	return Validator._instance_;
}
Validator.prototype = {
	checkForm:function(){
		return true;
	}
}
/*****************/
//用来保存全局数据（对象，函数）
//如果是ajax轮循的话，最好也加入到全局中，可以多个地方调用
var Global={
	data:{},
	get:function(k){
		var o=this.data[k];
		if(o==undefined)
			return null;

		var v= o["data"];
		if(typeof(o["filter"])=="function")v=o["filter"](v);
		v = (v==undefined?null:v);
		return v;
	},
	set:function(k, v,f){
		this.data[k] = {};
		this.data[k]["data"]=v;
		this.data[k]['filter']=f;
	}
};
