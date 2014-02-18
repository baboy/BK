//处理file 数据显示
var FileMgr = function(dirDiv, fileDiv, uploadDiv){
	this.dirs = null;
	this.files = null;
	this.dirTree = null;
	this.param = {};
	this.dirDiv = dirDiv;
	this.fileDiv = fileDiv;
	this.uploadMgr = new UploadMgr();
	this.uploadMgr.container = uploadDiv;
	this.init();
	this.uploadMgr.delegate = this.uploadDelegate();
};
FileMgr.prototype = {
	init:function(){

	},
	query:function(){

	},
	//刷新显示文件夹列表
	lsDir:function(){
		var handler = this;
		$.getJSON("/xman/file/dirs/query/", {"node":"dir"},function(ret){
			console.log(ret);
			var response = new HttpResponse(ret);
			if (response.isSuccess()) {
				var dirs = response.data;
				var op = {
						selctedColor:'white',
						selectedBg:'#3a84d5',
						//folderClosed:'/common/images/tree/icon_folder.gif',
						//folderOpen:'/common/images/tree/icon_folder.gif',
						imgDir:"/static/images/tree/",
						//itemClass:"ITEM_CLASS",
						itemClass:"btree-item",
						lineClass:"TREE_LINECLASS",
						tagKey:"filecount",
                        nameLength:50,
						dirLine:true, // 
						clickHandler:function(id){
							handler.ls(id);
						}
					};
				console.log(dirs);
				var tree=new BTree(dirs,op);
				tree.setFieldName({"name":"title","parentid":"pid","id":"id"});
				//tree.setAttrs({"name":"name","parentid":"parent",id:"name",href:"action"});
				tree.setTop({"title":"Top","id":0});
				tree.build(handler.dirDiv);
				handler.dirTree = tree;
				tree.clickById(0);
			};
		});
	},
	mkdir:function(name){
		var fid = this.dirTree.getSelectedItemId();
		var handler = this;
		var param = {"title":name};
		param.pid = fid;
		console.log("mkdir dir");
		console.log(param);
		$.post("/xman/file/add/", param,function(ret){
			console.log(ret);
			var response = new HttpResponse(ret.json());
			if (response.isSuccess()) {
				console.log(response.data);
				handler.dirTree.add(response.data);
			};
		});
	},
	//刷新显示文件列表 文件夹id
	ls:function(pid){
		if(!pid && this.dirTree){
			pid = this.dirTree.getSelectedItemId();
		}
		var handler = this;
		$.getJSON("/xman/file/query/json/", {"pid":pid}, function(ret){
			//console.log(ret);
			var response = new HttpResponse(ret);
			if (response.isSuccess()) {
				var html = "";
				var list = response.data;
				handler.files = list;
				for (var i = 0, n = list.length; i < n; i++){
					var f = list[i];
					html += "<tr>";
					html += "<td><input type='checkbox' role='select-all'/></td>";
					html += "<td><span role='file-name' fid='"+f["id"]+"'>"+f["title"]+"</span></td>";
					html += "<td><span>"+_s(f["ext"])+"</span></td>";
					html += "<td><span></span></td>";
					html += "<td><span>"+_s(f["create_time"])+"</span></td>";
					html += "</tr>";
				}
				handler.fileDiv.innerHTML = html;
				console.log(response.data);
			};
		});
	},
	sort:function(field, asc){
		this.param["sortby"] = field;
		this.param["sort"] = asc; 
	},
	upload:function(f){
		this.uploadMgr.upload(f);
	},
	add:function(param){
		var handler = this;
		var fid = this.dirTree.getSelectedItemId();
		param.pid = fid;
		$.post("/xman/file/add/", param, function(ret){
			console.log(ret);
			var response = new HttpResponse(ret.json());
			if (response.isSuccess()) {
				handler.ls();
			};
		});

	},
	uploadDelegate:function(){
		var handler = this;
		return {
			uploadFinished:function(service, response){
				console.log("+++ upload finished++");
				console.log(service);
				console.log(response);

				var param = response.data;
				param["node"] = "file";
				param.title = service.file.name
				param.size = service.file.size;
				handler.add(param);
				if (service.div) {
					service.div.parentNode.removeChild(service.div);
				};
			}
		};
	}
};
//处理各种用户操作
var FileOperator = function(eventContainer,contentContainer){

	this.eventContainer = eventContainer;
	this.contentContainer = contentContainer;
	this.mgr = new FileMgr();
	this.init();
};
FileOperator.getInstance = function(){
	if(!window.fileOperator){
		window.fileOperator = new FileOperator(document.body, document.getElementById("content") );
	}
	return window.fileOperator;
};
FileOperator.prototype = {
	init:function(){
		var handler = this;
		this.eventContainer.onclick = function(evt){
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
            	return true;
            switch(role){
            	case "menu-mkfile":{
            		handler.mkfileMenu();
            		break;
            	}
            	case "menu-mkdir":{
            		handler.mkdirMenu();
            		break;
            	}
            	case "menu-del":{
            		var p = window.location.param();
            		var pid = p._pid_;
            		parent.BWinMgr.getInstance().close(pid);
            		break;
            	}
            	case "menu-back":{
            		handler.ls();
            		break;
            	}
            	case "file-name":{
            		var fid = obj.getAttribute("fid");
            		if (!fid) {
            			return;
            		};
            		var files = handler.mgr.files;
            		for(var i in files){
            			var file = files[i];
            			if (file["id"] == fid ) {
		            		var p = window.location.param();
		            		if (p && p._callback_) {
			            		var cback = p._callback_;
			            		func = eval( "("+cback+")" );
			            		func(file);
		            		};
            				BWinMgr.getInstance().close();
            				return;
            			};
            		}
            	}
            };
		};
	},
	showUpload:function(flag){
		var menu1 = $("menu").find("[role=mkfile]").parent();
		var menu2 = $("menu").find("[role=back]").parent();
		if (flag) {
			menu1.hide();
			menu2.show();
			$(this.uploadDiv).slideDown();
		}else{
			menu1.show();
			menu2.hide();
			$(this.uploadDiv).slideUp();
		}
	},
	mkfileMenu:function(){
		console.log("mkfile");
		this.showUpload(true);
	},
	mkdirMenu:function(){
		var handler = this;
		console.log("mkdir");
		//this.showUpload(true);
		var tr = document.createElement("tr");
		var td1 = document.createElement("td");
		var td2 = document.createElement("td");
		td2.setAttribute("colspan",4);
		var label = document.createElement("label");
		label.innerHTML = "目录";
		var input = document.createElement("input");
		input.setAttribute("role","mkdir");
		input.setAttribute("type","text");
		td2.appendChild(label);
		td2.appendChild(input);
		tr.appendChild(td1);
		tr.appendChild(td2);
		if (this.contentDiv.firstChild) {
			$(tr).insertBefore(this.contentDiv.firstChild);
		}else{
			this.contentDiv.appendChild(tr);
		}
		input.onblur = function(){
			$(tr).remove();
		}
		input.onkeydown = function(e){
			var keyCode = e.which ? e.which : e.keyCode;
			if (keyCode == 13) {
				var name = this.value;
			console.log($(this));
				handler.mkdir(name,this);
			};
		}
		input.focus();
	},
	mkdir:function(name,e){
		console.log("mkdir "+name);
		if (e) {
			console.log(e);
			e.onblur = null;
			$(e).parent().html("<span>正在创建目录:"+name+"</span>");
		};
		this.mgr.mkdir(name);
	},
	ls:function(){
		this.showUpload(false);

	},
	setParam:function(param){
		this.param = param;
		this.uploadDiv = document.getElementById(param["upload_id"]);
		this.contentDiv = document.getElementById(param["content_id"]);
		this.dirDiv = document.getElementById(param["dir_id"]);
		this.mgr.dirDiv = this.dirDiv;
		this.mgr.fileDiv = this.contentDiv;
		this.mgr.uploadMgr.container = this.uploadDiv;
		this.mgr.lsDir();
		this.mgr.ls();
	}
};
$(document.body).ready(function(){
	FileOperator.getInstance().setParam({"dir_id":"dir","upload_id":"upload","content_id":"list-content"});
	$(window).resize(function() {
	    var W = $("#container").width();
	    var H = $("#container").height();
	    var mainWidth = W;
	    var mainHeight = H - $("header").height();
	    var menuWidth = $("#dir").width();
	    console.log(menuWidth);
	    var contentWidth = (mainWidth-menuWidth);
	    $("#main").css("height", mainHeight);
	    $("#content-wrapper").css("width",contentWidth);
	 });
	$(window).resize();
});
function selectFile(evt){
	evt = evt ? evt: window.event;
	var obj = evt.srcElement ? evt.srcElement:evt.target;
	var file = obj.files[0];
	FileOperator.getInstance().mgr.upload(file);
}