var BUFFER_SIZE = 1024*500;
var UploadService = function(file){
	this.file = file;
	this.apiServer = "/storage";
	this.uploadedSize = 0;
	this.delegate = null;
	this.uploadUri = this.getUploadUri();
	//重试时间间隔
	this.retryTimes = 0;
	this.blockSize = BUFFER_SIZE;
};
UploadService.prototype = {
	getUploadUri:function(){
		//上传临时文件名字 ukey 为校验用户登录令牌
		var comp = this.file.name.split(".");
		var ext = comp[comp.length-1];
		var fn = (this.file.lastModifiedDate.getTime()+"").substr(0,10)+"."+this.file.size+"."+this.file.name;
		var x = typeof(jQuery)=='function' ? jQuery : window;
		fn = typeof(x.md5) == "function" ? ( x.md5(fn)+"."+ext ) : fn.replace(/[^\.a-zA-Z0-9_-]/g, "_");
		return this.apiServer+"/"+fn+"?ukey=6yPHwKOPrUuGKCHRauMBzQNv&content-length="+this.file.size;
	},
	// 文件切片
	slice:function(start,len){
	    var blob = null;
	    var end = start+len;
		if(end > this.file.size)
			end = this.file.size;
		if (start >= end)
			return null;
		if (this.file.webkitSlice) {
		  	blob = this.file.webkitSlice(start, end);
		} else if (this.file.mozSlice) {
		  	blob = this.file.mozSlice(start, end);
		}else{
			blob = this.file.slice(start, end);
		}
		return blob;
	},
	/*
	* @param start
	* @param length
	* 接受可变参数 file 指针位置，以及读取长度
	*/
	upload:function(){
		//检查参数
		var start = arguments.length > 0 ? arguments[0]:0;
		var len = arguments.length > 1 ? arguments[1] : (arguments.length==1 ? this.blockSize : 0);
	    var blob = this.slice(start, len);
	    // start http request
		var handler = this;

		var postCallback = function(response){
			console.log(response);
			var status = response ? parseInt(response["status"]) : 0 ;
			status = isNaN(status) ? 0 : status;
			if (status != 1) {
				//返回错误 随着重试测试增多，重试间隔时间越来越长，最长10s
				if (handler.shouldRetry()) {
					handler.retryTimes ++;
					setTimeout( function(){ handler.upload(); }, Math.min(handler.retryTimes, 10) );
				};
				return;
			};

			var offset = response.data["offset"];
			console.log(offset);
			handler.setUploadedSize(offset);
			if(offset >= handler.file.size){
				handler.finish(response);	
			}else{
				handler.upload(offset);
			}
		}
	    var post = function(param){
			console.log(param);

			var xmlhttp = window.XMLHttpRequest ? ( new XMLHttpRequest() ) : (new ActiveXObject("Microsoft.XMLHTTP"));
			xmlhttp.open("POST", handler.uploadUri,true);
			xmlhttp.setRequestHeader("Content-Type", "application/octet-stream");
			if (xmlhttp.overrideMimeType) {
				xmlhttp.overrideMimeType("application/octet-stream"); 
			}
			xmlhttp.onreadystatechange=function(){
				if (xmlhttp.readyState==4 && xmlhttp.status==200){
					var response = xmlhttp.responseText;
					console.log(response);
					try{
						response = eval("("+response+")");
					}catch(e){
						response = null;
					}
					postCallback(response);
				}else  if (xmlhttp.readyState==4) {
					handler.handleError(xmlhttp.responseText);
				};
			};
			xmlhttp.send(param);
	    };
		post(blob);
	},
	stop:function(){

	},
	// delegate
	setUploadedSize:function(uploadedSize){
		this.uploadedSize = uploadedSize;
		if(this.delegate && typeof(this.delegate.setProgress) == "function" ){
			var p = uploadedSize*1.0/this.file.size;
			this.delegate.setProgress(this, p);
		}
		if( this.delegate && typeof(this.delegate.setUploadedSize) == "function" ){
			this.delegate.setUploadedSize(this, uploadedSize, this.file.size);
		}
	},
	finish:function( response  ){
		if( this.delegate && typeof(this.delegate.uploadFinished) == "function" ){
			this.delegate.uploadFinished(this, response);
		}
	},
	handleError:function(response){
		if( this.delegate && typeof(this.delegate.handleError) == "function" ){
			this.delegate.handleError(this, response);
		}
	},
	shouldRetry:function(){
		if (this.delegate && typeof(this.delegate.shouldRetry) == "function" ) {
			return this.delegate.shouldRetry(this, this.retryTimes);
		};
		return true;
	}
};
var UploadMgr = function(container_id){
	this.container = document.getElementById(container_id);
	this.services = [];
	this.init();
}
UploadMgr.prototype = {
	init:function(){

	},
	upload:function(f){
		var handler = this;
		var div = document.createElement("div");
		div.className = "upload-item";
		var label = document.createElement("label");
		label.innerHTML = f.name;
		var pb = document.createElement("div");
		pb.className = "progressbar";
		var pbi = document.createElement("div");
		pbi.className = "progressbar-inner";
		pb.appendChild(pbi);
		var span = document.createElement("span");
		span.innerHTML = "0%";

		var eSpan = document.createElement("span");
		eSpan.className = "error";


		div.appendChild(label);
		div.appendChild(pb);
		div.appendChild(span);
		div.appendChild(eSpan);
		this.container.appendChild(div);

		var uploadService = new UploadService(f);
		uploadService.upload();
		uploadService.delegate = {
			//监控上传进度
			setProgress:function(service, progress){
				var percent = (parseInt(progress*10000)/100) + "%";
				progress = progress < 0.01 ? 0.01 : progress;
				var w = (progress*100) + "%";
				service.progressbar.style.width = w;
				service.pSpan.innerHTML = percent;
				if (handler.delegate && typeof(handler.delegate.setProgress)=="function") {
					handler.delegate.setProgress(service, progress);
				};
			
			},
			// 监控上传大小
			setUploadedSize:function(service, uploadedSize,totalSize){
				//document.getElementById("size").innerHTML = (uploadedSize+"/"+totalSize);
				if (handler.delegate && typeof(handler.delegate.setUploadedSize)=="function") {
					handler.delegate.setUploadedSize(service, uploadedSize, totalSize);
				};
			},
			//上传完成
			uploadFinished:function(service, response){
				console.log("uploadFinished...");
				console.log(response);
				if (handler.delegate && typeof(handler.delegate.uploadFinished)=="function") {
					handler.delegate.uploadFinished(service, response);
				};

			},
			//出错处理
			handleError:function(service, response){
				console.log("handleError...");
				console.log(response);
				service.eSpan.innerHTML = response.msg?response.msg:"上传失败";
				if (handler.delegate && typeof(handler.delegate.handleError)=="function") {
					handler.delegate.handleError(service, response);
				};
			},
			// 是否允许重试
			shouldRetry:function(service, retryTimes){
				console.log("retry:"+retryTimes);
				if (handler.delegate && typeof(handler.delegate.shouldRetry)=="function") {
					handler.delegate.shouldRetry(service, retryTimes);
				};
				return true;
			}
		};
		uploadService.div = div;
		uploadService.progressbar = pbi;
		uploadService.label = label;
		uploadService.pSpan = span;
		uploadService.eSpan = eSpan;
		this.services.push({"service":uploadService, "div":div, "progressbar":pbi, "label":label, "pSpan":span,"eSpan":eSpan});

		
	}
};
