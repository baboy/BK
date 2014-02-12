<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<meta name="keywords" content="baboy, cms, php, BK framework, php framework">
		<meta name="description" content="cms adminitrator">
		<meta name="author" content="Baboy">
		<title>AppMan</title>
		<link rel="stylesheet" type="text/css" href="<?=$relatvie_path?>/static/css/main.css">
		<script src="<?=$relatvie_path?>/static/js/jquery.js" charset="UTF-8"></script>
		<script src="<?=$relatvie_path?>/static/js/jquery.form.js" charset="UTF-8"></script>
		<script src="<?=$relatvie_path?>/static/js/init.js?<?=rand()?>" charset="UTF-8"></script>
 		<SCRIPT LANGUAGE="JavaScript" src="<?=$relatvie_path?>/static/js/B.util.js?<?=rand()?>"></SCRIPT>
  		<SCRIPT LANGUAGE="JavaScript" src="<?=$relatvie_path?>/static/js/B.tree-1.01.js"></SCRIPT>
  		<SCRIPT LANGUAGE="JavaScript" src="<?=$relatvie_path?>/static/js/upload.js"></SCRIPT>
		<script src="<?=$relatvie_path?>/static/js/win/B.win.mgr.js?<?=rand()?>" charset="UTF-8"></script>
  		<style>
	  		.btree-item, .btree-item ul{margin:0;padding:0}
			.btree-item a{
				list-style: none;
				padding:2px 3px;
				margin: 0px 0 0 2px;
				white-space: nowrap;
				font-size:12px;
			}
			.btree-item li{border-left1:1px solid red;padding:0;}
			.btree-item ul{border-left1:1px solid blue;margin:0;padding:0;padding-left:18px;}
			.btree-item a{cursor:pointer;}
			.btree-item li{-moz-user-select:none;hutia:expression(this.onselectstart=function(){return(false)});}
  		</style>
		<style>
			.h-menu{
				margin: 0;
				height:32px;
				line-height: 32px;
			}
			.h-menu ul.menu-group {
				float: left;
			}
			.h-menu ul.menu-group li {
				float: left;
				cursor: pointer;

				height:100%;
				padding:0 10px 0;
				background: #ccc; 
				border-left:1px solid #DDD;
				border-right:1px solid #BCBCBC;
			}
			.h-menu ul.menu-group li.left{
				border-left:none;
			}
			a {
				color:blue;
				cursor:pointer;
			}
			table, td{
				 border-spacing: 0;
			}
			iframe{
				border:none;
				width:100%;
				height:100%;
			}
			#container{
				width:100%;
			}
			#main{
				width:100%;
			}
		</style>
		<style>
			.div-input{
				padding: 10px 20px 10px;
			}
			.div-input label{
				display:block;
				float:left;
				width:150px;
				font: 80%;
				color:#555;
			}
			.div-submit{
				padding:10px  0  10px 150px;
			}
			#overlay{
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background: #cccccc;
				display: none;
			}
			#overlay iframe{
				width: 100%;
				height: 100%;
				border: none;

			}
			#content-wrapper{

			}
			.upload-item{
				height:28px;
			}
			.upload-item span{
				line-height: 28px;
				padding:0 10px 0;
			}
			.upload-item label{
				line-height:28px;
				float:left;
				width:100px;
				text-align: right;
				padding-right:10px;
			}
			.upload-item .progressbar{
				line-height: 28px;
				margin-top:12px;
			}
			.progressbar{
				float:left;
				width:200px;
			}
			.progressbar{
				height:4px;
				border:1px solid gray;
				font-size: 0;
			}
			.progressbar-inner{
				height:100%;
				width:0;
				background:#ccc;
			}
		</style>
		<script>
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
							tree.selectedItemById(0);
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
					var handler = this;
					$.post("/xman/file/query/json/", {"pid":pid}, function(ret){
						//console.log(ret);
						var response = new HttpResponse(ret.json());
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
					var fid = this.dirTree.getSelectedItemId();
					param.pid = fid;
					$.post("/xman/file/add/", param, function(ret){
						console.log(ret);
						var response = new HttpResponse(ret.json());
						if (response.isSuccess()) {
							console.log(response.data);
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
					            		var cback = p._callback_;
					            		func = eval( "("+cback+")" );
					            		func(file);
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

		</script>
	</head>
	<body>
		<div id="container">
			<header>
				<div class="notice-board-wrapper"><div id="notice-board">正在加载...</div></div>
				<div class="module-title">File Management</div>
			</header>
			
			<div id="main" style="border:1px solid #CCC;">
				<menu id="dir" class="menu">
				</menu>
				<div class="content-wrapper" id="content-wrapper">
					<menu class="h-menu" style="border-bottom:1px solid #ccc;">
						<ul class="menu-group">
							<li class="title left" role="menu-mkfile" style="position:relative">
								<strong>+File</strong>
								<div style="position:absolute;top:0;left:0;visible:hidden;opacity:0;">
									<input type="file" style="width:50px;height:30px" onchange="selectFile()">
								</div>
							</li>
							<li class="title" role="menu-mkdir">+Dir</li>
							<li class="title" role="menu-mkdir">+Rename</li>
							<li class="title" role="menu-del">+Delete</li>
						</ul>
						<ul class="menu-group" style="display:none">
							<li class="title" role="back">&lt; back</li>
						</ul>
					</menu>
					<div id="upload">
					</div>
					<div>
						<table class="list">
							<thead>
								<tr>
									<th class="cb"><input type="checkbox" role="select-all"/></th>
									<th style="">名称</th>
									<th style="width:60px">类型</th>
									<th style="width:80px">创建者</th>
									<th style="width:100px">日期</th>
								</tr>
							</thead>
							<tbody id="list-content">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

	</body>

</html>