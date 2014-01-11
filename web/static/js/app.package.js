function _s(s){
	return s?s:"";
}
var AppHandler = function(obj){
	this.container = obj;
}
AppHandler.prototype = {
	query:function(){
		var handler = this;
		$.getJSON("http://bf.cn/log/app/query", function($data){
			var status = new Status($data);
			if(status.isSuccess()){
				handler.parse(status.data());
			}
		});
	},
	parse:function(data){
		console.log(data);
		var html = "<table><thead><th class='app-name'>Name</th><th class='app-package'>Package</th><th class='app-developer'>Developer</th><th class='app-action'>Action</th></thead><tbody>";
		for (var i = 0, n = data.length; i < n; i++) {
			var item = data[i];
			var bgColor = (i%2==0 ? "#FEFEFE" : "#EFEFEF");
			html += "<tr style='background:"+bgColor+"'><td>"+_s(item["name"])+"</td><td>"+_s(item["package"])+"</td><td>"+_s(item["developer"])+"</td><td><a role='role-view-build' rid='"+item["id"]+"'>Builds</a></td></tr>";
		};
		html += "</tbody></table>";
		this.container.innerHTML = html;
	}
}
var Status = function(json){
	this.json = json;
}
Status.prototype = {
	isSuccess:function(){
		return this.json["status"];
	},
	msg:function(){
		return this.json["msg"];
	},
	data:function(){
		return this.json["data"];
	}
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
		this.objects.push( pushView );
		this.container.appendChild(pushView.container);
	},
	pop:function(){

	}
}


var NavigationPushView = function(nav, obj, width, height){
	this.nav = nav;
	this.obj = obj;
	this.container = document.createElement("div");
	this.titleContainer = null;
	this.contentContainer = null;
	this.init();
}
NavigationPushView.prototype = {
	init:function(){
		var top = document.createElement("div");
		top.className = "nh-top";
		var backBtn = document.createElement("div");
		backBtn.className = "nh-back";
		var titleContainer = document.createElement("div");
		titleContainer.className = "nh-title";
		top.appendChild(backBtn);
		top.appendChild(titleContainer);
		this.titleContainer = titleContainer;
		var content = document.createElement("div");
		content.className = "nh-content";
		content.appendChild(this.obj);
		this.container.appendChild(top);
		this.container.appendChild(content);
		var handler = this;
		this.obj.setTitle = function(t){
			handler.setTitle(t);
		}

	},
	setTitle:function(title){
		this.titleContainer.innerHTML = title;
	}
}