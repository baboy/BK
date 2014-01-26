var MovieHandler = function(obj){
	this.container = obj;
	this.init();
}
MovieHandler.prototype = {
	init:function(){
		$(document).ready(function(){
			$.ajaxPrefilter(function (options){options.global = true;});
			$(this).ajaxStart(function(){
				$(top.document.body).find("#notice-board").show().html("正在载入...");
			}).ajaxStop(function(){
				$(top.document.body).find("#notice-board").hide().html("请求已完成");
			}).ajaxError(function(){
				$(top.document.body).find("#notice-board").hide().html("请求失败");
			});

		});
		var handler = this;
		this.container.onclick = function(evt){
			evt = evt ? evt: window.event;
            var obj = evt.srcElement ? evt.srcElement:evt.target;
            var role = null;
            while(!role && obj && obj!=document.body){
            	role = obj.getAttribute("role");
            	if (!role)
            		obj = obj.parentNode;
            }
            console.log(role);
            if(!role)
            	return false;
            var sid = obj.getAttribute("id");
            console.log(sid);
            switch(role){
            	//点击创建按钮
            	case "add-product":{
            		
            		break;
            	}
            	case "add-build":{
            		
            		break;
            	}
            	//点击标题 查看详情
            	case "toggle-detail":{
            		handler.toggleDetail(obj,sid);
            		break;
            	}
            	//点击更新
            	case "update":{
            		break;
            	}
            	//点击Builds
            	case "view-builds":{
            		break;
            	}
            };
		}
	},
	showNotice:function(msg){
		console.log(msg);
		setTimeout(function(){$("#notice-board").show().fadeIn(3000).html(msg);},1);
	},
	showMovieList:function(){
		var handler = this;
		$.getJSON("/xman/movie/query", function(data){
			var status = new Status(data);
			if(status.isSuccess()){
				handler.container.innerHTML = status.data;
			}
		});
	},
	toggleDetail:function(td, sid){
		var tr = td.parentNode;
		if ($(tr).hasClass("selected")) {
			$(tr).removeClass("selected");
		}else{
			$(tr).addClass("selected");
		}
	}
}