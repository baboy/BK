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

            	}
            	case "post":{
            		handler.post();
            	}
            	case "close":{
            		handler.close();
            	}
            };
		}
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
	post:function(){
		var fields = {
			"id":"id",
			"title":"title",
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

	},
	query:function(){

	},
	close:function(){
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
	EditHandler.getInstance("container");
});