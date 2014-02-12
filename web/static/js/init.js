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