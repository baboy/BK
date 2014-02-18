var EditHandler = function(obj){
	this.container = obj;
	this.init();
}
EditHandler.getInstance = function(){
	if (!EditHandler.instance) {
		var obj = arguments.length>0?arguments[0]:null;
		if ( typeof(obj) == "string" ) {
			obj = document.getElementById(obj);
		}
		EditHandler.instance = new EditHandler(obj);
	};
	return EditHandler.instance;
}
EditHandler.prototype = {
	init:function(){
		var handler = this;
		this.container.onclick = function(evt){
			evt = evt ? evt: window.event;
            var obj = evt.srcElement ? evt.srcElement:evt.target;
            var role = obj.getAttribute("role");
            if(!role)
            	return false;
            switch(role){
            	//点击创建按钮
            	case "file-browser":{
            		input = document.getElementById(obj.getAttribute("input"));
            		display = document.getElementById(obj.getAttribute("display"));
            		handler.browserMp4(obj, input, display);
            		break;
            	}
            	case "file-browser":{
            		input = document.getElementById(obj.getAttribute("input"));
            		display = document.getElementById(obj.getAttribute("display"));
            		handler.browserMp4(obj, input, display);
            		break;
            	}
            	case "pic-add":{
            		box = document.getElementById(obj.getAttribute("box"));
            		display = document.getElementById(obj.getAttribute("display"));
            		handler.browserImage(obj, box, display);
            		break;

            	}
            	case "post":{
            		handler.post(obj);
            		break;
            	}
            	case "close":{
            		handler.close();
            		break;
            	}
            };
		};
		
	},
	formReady:function(){
		var forms = document.getElementsByTagName("form");
		for(var i =0, n = forms.length; i < n; i++){
			var form = forms[i];
			var inputs = form.getElementsByTagName("input");
			for(var j = 0, n2 = inputs.length; j < n2; j++){
				var input = inputs[j];
				setReferenceValue(input);
			}
		};
	},
	browserMp4:function(obj, input, display){
		console.log(obj,input,display);
		var param = { width:700, height:500 };
		window._select_file_ = function(file){
			console.log(file);
			input.value = file.url;
			display.innerHTML = file.url;
		}
		BWinMgr.getInstance().openWindow(param).setLink("/xman/file/mgr/", {"_callback_":"parent._select_file_"});
	},
	browserImage:function(obj, box, display){
		console.log(obj,box,display);
		var param = { width:700, height:500 };
		window._select_file_ = function(file){
			console.log(file);
			display.src = file.url;
			box.className = "pic-box";

		}
		BWinMgr.getInstance().openWindow(param).setLink("/xman/file/mgr/", {"_callback_":"parent._select_file_"});
	},
	post:function(obj){
		this.formReady();
		var form = null;
		while( obj && !form ){
			var tagName = obj.tagName.toLowerCase();
			if (tagName == "form") {
				form = obj;
				break;
			};
			obj = obj.parentNode;
		};
		if (!form) {
			return;
		};
		var inputs = form.getElementsByTagName("input");
		var param = {};
		for(var i = 0, n = inputs.length; i < n; i++){
			var input = inputs[i];
			var field = input.getAttribute("name");
			param[field] = input.value;
		}
		param["content"] = tinyMCE.activeEditor.getContent({format : 'raw'});
		console.log(param); 

		/*
		var fields = {
			"id":"id",
			"title":"title",
			"actor":"actor",
			"director":"director",
			"thumbnail":"thumbnail",
			"pic":"pic",
			"subtitle":"subtitle",
			"author":"author",
			"tag":"tag",
			"summary":"summary",
			"m3u8":"m3u8",
			"mp4":"mp4"};
		var param = {};
		for(var k in fields){
			var k2 = fields[k];
			var obj = document.getElementById(k2);
			if (!obj) {
				continue;
			};
			var v = "";
			var tagName = obj.tagName.toLowerCase();
			if (tagName == "input") {
				v = obj.value;
			}else if(tagName == "img"){
				v = obj.getAttribute("src");
			}else{
				v = obj.innerHTML;
			}
			param[k] = v;
		}
		param["content"] = tinyMCE.activeEditor.getContent({format : 'raw'});
		console.log(param);
		*/

	},
	load:function(){
		var sid = document.getElementById("sid").value;
		var setPic = function(obj, url){
			console.log(obj,url);

			setReferenceValue(obj, url);
			var box = document.getElementById(obj.getAttribute("box"));
			if (url) {
				box.className = "pic-box";
			}else{
				box.className = "pic-box-empty";
			}
		};
		var updateInfo = function(m){
			var fields = {
			"id":{"field":"sid"},
			"title":{"field":"title"},
			"thumbnail":{"field":"thumbnail","setter":setPic},
			"pic":{"field":"pic","setter":setPic},
			"subtitle":{"field":"subtitle"},
			"author":{"field":"author"},
			"tag":{"field":"tag"},
			"summary":{"field":"summary","setter":setReferenceValue},
			"m3u8":{"field":"sd","setter":setReferenceValue},
			"mp4":{"field":"mp4","setter":setReferenceValue}};
			for(var k in fields){
				var obj = document.getElementById(k);
				if (!obj) {
					continue;
				};
				var conf = fields[k];
				var field = conf["field"];
				var func = conf["setter"];
				var val = m[field];
				if (func) {
					func(obj, val);
					continue;
				};
				var tagName = obj.tagName.toLowerCase();
				if (tagName == "input") {
					obj.value = val;
				}else if(tagName == "img"){
					obj.setAttribute("src",val);
				}else{
					obj.innerHTML = val;
				}

			};
			tinyMCE.activeEditor.setContent(m["content"]);
			console.log(m);
		};
		$.getJSON("/xman/media/detail/",{"sid":sid}, function(ret){
			var res = new HttpResponse(ret);
			console.log(res);
			if (res.isSuccess()) {
				updateInfo(ret.data);
			};
		});
	},
	close:function(){
		parent.location.hash = "#";
		BWinMgr.getInstance().close();
	}
}


$(document.body).ready(function(){
	$(window).resize(function() {
	    var W = $("#container").width();
	    var H = $("#container").height();
	    var mainWidth = W;
	    var mainHeight = H - $("etitle").height();
	    var actWidth = $("#act").width();
	    var infoWidth = $("#info").width();
	    var contentWidth = (mainWidth-actWidth-infoWidth);
	    $("#main").css("height", mainHeight);
	    $("#content-wrapper").css("margin-left",infoWidth);
	    $("#content-wrapper").css("width",contentWidth);
	 });
	$(window).resize();
	EditHandler.getInstance("container").load();
});