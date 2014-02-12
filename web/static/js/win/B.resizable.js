//缩放类
var Resizable = function(obj,anchor){
	this.object = obj;
	this.anchor = anchor;
	this.pos = BElement(obj).getLocation();              //调用外部函数获取对象页面位置
	this._callback=null;
}
Resizable.prototype.enable = function(){
	if(!this.anchor){
		//this.object.style.overflow = "auto";
		//this.object.style.cursor = "pointer";
		var anchor = document.createElement("div");
		anchor.style.width = "3px";
		anchor.style.height = "3px";
		anchor.style.fontSize = 0;
		anchor.style.zIndex = this.object.style.zIndex?(this.object.style.zIndex+1):2;
		//anchor.style.border = "1px solid #333";
		anchor.style.background = "black";
		anchor.style.position = "absolute";
		anchor.title = "按住拖放大小";
		anchor.style.right = 0;
		anchor.style.bottom =0;
		anchor.style.background="black";
		this.object.appendChild(anchor);
		this.anchor = anchor;
	}

	this.addEvent();
}
Resizable.prototype.addEvent = function(){  // 添加鼠标控制
	
	var isMouseDown = false;                     //鼠标按下
	var handler = this;
	var object = this.object;
	this.anchor.onmouseover = function(){
		this.style.cursor = "se-resize";
	}
	this.anchor.onmousedown = function(event){// 鼠标按下                
	   
		isMouseDown = true;
		event = event || window.event;
		var anchor = this;

		var oW = BElement(object).width();                    //拖动区域宽度
		var oH = BElement(object).height();                   //拖动区域高度
		
		

		
		var mousePos = BMouse(event).getLocation();   //鼠标按下时的位置     
		var aX = mousePos.x;                     // 鼠标 x坐标
		var aY = mousePos.y;					 //鼠标 y坐标 
		
		
		// 鼠标移动
		function mouseMove(event){
			if(!isMouseDown)return;

			event = event || window.event; 
			var mousePos = BMouse(event).getLocation();   //鼠标按下时的位置
			var aX1 = mousePos.x;                     
			var aY1 = mousePos.y;    
			
			offsetX = aX1-aX;
			offsetY = aY1-aY;  
			
			var oW1 = oW+offsetX;   //区域现在的宽度
			var oH1 = oH+offsetY;    //区域现在高度
			if(oW1<1){
				oW1 = 1;
			}
			if(oH1<1){
				oH1 = 1;
			}
			var flag=false;
			if(!handler._minwidth || oW1>handler._minwidth)
				object.style.width = oW1 + "px";
			if(!handler._minheight || oH1>handler._minheight)
				object.style.height = oH1+"px";
			if(handler._callback)handler._callback();
			
			if(!(anchor.parentNode==document.body))return false;
			var aL = (BElement(object).getLocation().x + BElement(object).width() - 2)+"px";
			var aT = (BElement(object).getLocation().y + BElement(object).height() - 2)+"px";		
			anchor.style.left = aL;
			anchor.style.top = aT;
			return false;

		}
		function mouseUp(){// 鼠标弹起
			isMouseDown = false;
			
			anchor.style.cursor = "default";
			try{
				this.releaseCapture();
			}
			catch(e){
				window.releaseEvents(Event.MOUSEMOVE|Event.MOUSEUP);
			}
			return false;
		}

	   try{                                                    //获得焦点
			this.setCapture();
			this.onmousemove = mouseMove;
			this.onmouseup = mouseUp;
	   }
		catch(e){
			window.captureEvents(Event.MOUSEMOVE|Event.MOUSEUP);
			document.addEventListener("mouseup",mouseUp,true);
			document.addEventListener("mousemove",mouseMove,true);                    
		}
		return false;
	}	
}
Resizable.prototype.getAnchor=function(){
	return this.anchor;
}
Resizable.prototype.setCallback=function(f){
		this._callback = f;
}
Resizable.prototype.setMinSize=function(w,h){
		this._minwidth=w;
		this._minheight=h;
}