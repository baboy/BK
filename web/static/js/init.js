var AdminHandler = function(obj){
	this.container = obj;
	this.init();
}
AdminHandler.prototype = {
	init:function(){}
}
$(document).ready(function(){
	$.ajaxPrefilter(
		function (options){
			options.global = true;
		}
	);
	$(this).ajaxStart(function(){
		$("#notice-board").show().html("正在载入...");
	}).ajaxStop(function(){
		$("#notice-board").hide().html("请求已完成");
	}).ajaxError(function(){
		$("#notice-board").hide().html("请求失败");
	});
});
var Status = function(json){
	this.json = json;
	this.status = -1;
	this.msg = "请求失败";
	this.data = null;
	if (json && typeof(json)=="object") {
		this.msg = json["msg"];
		this.status = json["status"];
		this.data = json["data"];
	};
}
Status.prototype = {
	isSuccess:function(){
		return parseInt(this.status)==1;
	}
}

var HttpResponse = function(json){
	this.json = json;
	this.status = -1;
	this.msg = "请求失败";
	this.data = null;
	if (json && typeof(json)=="object") {
		this.msg = json["msg"];
		this.status = json["status"];
		this.data = json["data"];
	};
}
HttpResponse.prototype = {
	isSuccess:function(){
		return parseInt(this.status)==1;
	}
}


var setReferenceValue = function(){
	var obj = null;
	var val = null;
	if (arguments.length>0) {
		obj = arguments[0];
	}
	if (arguments.length>1) {
		val = arguments[1];
	}
	if (typeof(obj) == "string") {
		obj = document.getElementById(obj);
	};
	if (!obj) 
		return;
	var _get = function(o){
		if (!o) 
			return null;
		var tagName = o.tagName.toLowerCase();
		if (tagName == "input") {
			return o.value;
		}else if(tagName == "img"){
			return o.getAttribute("src");
		}else{
			return o.innerHTML;
		}
		return null;
	};
	var _set = function(o, v){
		if (!o) 
			return;
		var tagName = o.tagName.toLowerCase();
		if (tagName == "input") {
			o.value = v;
		}else if(tagName == "img"){
			o.setAttribute("src",v);
		}else{
			o.innerHTML = v;
		}
	};
	var input = document.getElementById(obj.getAttribute("input"));
	var display = document.getElementById(obj.getAttribute("display"));
	//no reference
	if (arguments.length==1 && !display) {
		return;
	};
	if (val === null && display) {
		val = _get(display);
	};
	_set(obj, val);
	_set(input, val);
	_set(display,val);
}

//lib
var NavigationHandler = function(container_id){
	this.container = document.getElementById(container_id);
	this.objects = [];
}
NavigationHandler.prototype = {
	addPushView:function(obj){

	},
	push:function(obj){
		var w = $(this.container).width();
		var h = $(this.container).height();
		var pushView = new NavigationPushView(this,obj);
		pushView.container.style.width = w+"px";
		if (this.objects.length>0) {
			this.objects[this.objects.length-1].container.style.display="none";
		};
		this.objects.push( pushView );
		this.container.appendChild(pushView.container);
	},
	pop:function(){
		var lastObj = this.objects[this.objects.length-1];
		lastObj.container.parentNode.removeChild(lastObj.container);
		this.objects.length --;
		lastObj = this.objects[this.objects.length-1];
		lastObj.container.style.display="";
	}
}

var NavigationPushView = function(nav, obj, width, height){
	this.nav = nav;
	this.obj = obj;
	this.container = document.createElement("div");
	this.container.className = "nh-push-view";
	this.container.setAttribute("role","push-view");
	this.titleContainer = null;
	this.contentContainer = null;
	this.init();
	this.createFrame();
}
NavigationPushView.prototype = {
	init:function(){
		var handler = this;
		this.container.onclick = function(evt){
			evt = evt ? evt: window.event;
            var obj = evt.srcElement ? evt.srcElement:evt.target;
            var role = obj.getAttribute("role");
            if(!role)
            	return false;
            switch(role){
            	case "push-back":{
            		handler.nav.pop();
            		break;
            	}
            }

		}
	},
	createFrame:function(){
		var handler = this;
		var top = document.createElement("div");
		top.className = "nh-top";

		var backBtn = document.createElement("div");
		backBtn.className = "nh-back";
		backBtn.innerHTML = " <返回 ";
		backBtn.onclick = function(){
			handler.nav.pop();
		};

		var titleContainer = document.createElement("div");
		titleContainer.className = "nh-title";

		var rightItem = document.createElement("div");
		rightItem.className = "nh-title-item-right";
		if (this.nav.objects.length>0) {
			top.appendChild(backBtn);
		};
		top.appendChild(rightItem);
		top.appendChild(titleContainer);

		this.titleContainer = titleContainer;
		this.rightItem = rightItem;

		var content = document.createElement("div");
		content.className = "nh-content";

		content.appendChild(this.obj);
		this.container.appendChild(top);
		this.container.appendChild(content);
		this.obj.setTitle = function(title){
			handler.setTitle(title);
		};
		this.obj.setRightItem = function(obj){
			handler.setRightItem(obj);
		};
		this.obj.push = function(obj){
			this.nav.push(obj);
		};
		this.obj.nav = this.nav;

	},
	setTitle:function(title){
		this.titleContainer.innerHTML = title;
	},
	setRightItem:function(obj){
		this.rightItem.appendChild(obj);
	}
};