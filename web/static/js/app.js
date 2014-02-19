var AppHandler = function(obj){
	this.container = obj;
	this.init();
}
AppHandler.prototype = {
	init:function(){
		$(document).ready(function(){
			$.ajaxPrefilter(function (options){options.global = true;});
			$(this).ajaxStart(function(){
				$("#notice-board").show().html("正在载入...");
			}).ajaxStop(function(){
				$("#notice-board").hide().html("请求已完成");
			}).ajaxError(function(){
				$("#notice-board").hide().html("请求失败");
			});

		});
		var handler = this;
		this.container.onclick = function(evt){
			evt = evt ? evt: window.event;
            var obj = evt.srcElement ? evt.srcElement:evt.target;
            var role = obj.getAttribute("role");
            if(!role)
            	return false;
            switch(role){
            	//点击创建按钮
            	case "add-product":{
            		var form = null;
            		while(!form){
            			if (obj.tagName.toLowerCase() == "form") {
            				form = obj;
            				break;
            			};
            			if (obj.parentNode) {
            				obj = obj.parentNode;
            			}else{
            				break;
            			}
            		}
            		return form?handler.addProduct(form):false;
            		break;
            	}
            	case "add-build":{
            		var form = null;
            		while(!form){
            			if (obj.tagName.toLowerCase() == "form") {
            				form = obj;
            				break;
            			};
            			if (obj.parentNode) {
            				obj = obj.parentNode;
            			}else{
            				break;
            			}
            		}
            		return form?handler.addBuild(form):false;
            		break;
            	}
            	//点击标题 查看详情
            	case "view-detail":{
            		return handler.viewDetail($pid);
            		break;
            	}
            	//点击更新
            	case "update":{
            		return handler.update($pid);
            		break;
            	}
            	//点击Builds
            	case "view-builds":{
            		var appid = obj.getAttribute("appid");
            		//return handler.viewBuilds(appid);
            		break;
            	}
            	case "toggle":{
            		var toggleId = obj.getAttribute("toggle");
            		if (toggleId) {
            			var div = document.getElementById(toggleId);
            			if (div) {
            				var display = div.style.display == "none" ? "table-row" : "none";
            				div.style.display = display;
            			};
            		};

            		break;
            	}
            };
		}
	},
	showNotice:function(msg){
		console.log(msg);
		setTimeout(function(){$("#notice-board").show().fadeIn(3000).html(msg);},1);
	},
	showProducts:function(){
		var handler = this;
		$.getJSON("/app/products/view/", function(data){
			var status = new Status(data);
			if(status.isSuccess()){
				handler.parse(status.data);
			}
		});
	},
	toggleAddForm:function(){
		$(this.container).find(".row-input[role=add]").toggle();
	},
	parse:function(data){

		this.container.innerHTML = data;
	},
	addProduct:function(form){
		var pass = Validator.getInstance().checkForm(form);
		var handler = this;
		if (pass) {
			$(form).ajaxForm({"dataType":"json"}).ajaxSubmit(function(data){
				if(typeof(data)=="string"){
					data = data.json();
				}
				var status = new Status(data);
				if(!status.isSuccess()){
					handler.showNotice(status.msg);
				}
				console.log(data);
				return false;
			});
		};
		return false;
	},

	addBuild:function(form){
		var pass = Validator.getInstance().checkForm(form);
		var handler = this;
		if (pass) {
			$(form).ajaxForm({"dataType":"json"}).ajaxSubmit(function(data){
				if(typeof(data)=="string"){
					data = data.json();
				}
				var status = new Status(data);
				if(!status.isSuccess()){
					handler.showNotice(status.msg);
				}else{
					handler.queryBuilds(status.data.appid);
				}
				console.log(data);
				return false;
			});
		};
		return false;
	},
	viewDetail:function(){

	},
	update:function(){

	},
	queryBuilds:function(pid){
		var handler = this;
		$.getJSON("/app/builds/view/",{"id":pid}, function(data){
			var status = new Status(data);
			if(status.isSuccess()){
				console.log(status.data);
				handler.parse(status.data);
			}
		});
	},
	viewBuilds:function(appid){
		var buildDiv = document.createElement("div");
		var app = new AppHandler(buildDiv);
		this.container.nav.push(buildDiv);
		buildDiv.setNavBarVisible(false);
		//buildDiv.setTitle("App Builds For [<strong>"+product.name+"</strong>]  package:[<strong>"+product.package+"</strong>]");
		app.queryBuilds(appid);
	},
	uploadAppPackage:function(f){
		var uploadMgr = new UploadMgr("display-download_url");
		uploadMgr.delegate = {
			uploadFinished:function(service, response){
				console.log(service);
				console.log(response);
				var param = response.data;
				var file_input = document.getElementById("download_url");
				setReferenceValue(file_input, param.url);
			}
		};
		uploadMgr.upload(f);
	}
}

function uploadAppPackage(evt){
	evt = evt ? evt: window.event;
	var obj = evt.srcElement ? evt.srcElement:evt.target;
	var file = obj.files[0];
	appHandler.uploadAppPackage(file);
	var role = obj.getAttribute("role");

}

$(document).ready(function(){
	var addBtn = document.createElement("button");
	addBtn.innerHTML = "Add";
	var appDiv = document.createElement("div");
	var nav = new NavigationHandler("app-content");
	nav.push(appDiv);
	appDiv.setTitle("App Management");
	appDiv.setNavBarVisible(false);
	appDiv.setRightItem(addBtn);

	window.appHandler = new AppHandler(appDiv);
	appHandler.showProducts();
	addBtn.onclick = function(){
		appHandler.toggleAddForm();
	}

	HashEventManager.getInstance().addEventListener("builds",function(appid){
		appHandler.viewBuilds(appid);
	});
	var h = window.location.hash;
	h = h.substring(1);
	HashEventManager.getInstance().fire(h);
});