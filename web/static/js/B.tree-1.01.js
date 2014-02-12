var BTree = function(list,op){
    this._oriList = list;
	this._list=BObject(list).clone(true);
	this._options=op;
	this.init();
	this._selectedItemId=null;
	this._result=null;
	this._slector=null;
	this._lis=null;
	this._basicFields = {name:"foldername",id:'id',parentid:'parentid'};
	this._fields={name:"foldername",id:'id',parentid:'parentid',tag:"_tag"};
	this._attrs = {};
	this._top=null;
	this._ul=null;
}
BTree.prototype.setFields = function(p){
    if(typeof(p)!="object")
        return;
    for (k in p){
        this._fields[k] = p[k];
    }
}
BTree.prototype.init=function(){this.setEvent(this._options);}
BTree.prototype.setEvent=function(op){
	this._options=op;
	if(!this._options)this._options={};
	//if(!this._options.clickHandle)this._options.clickHandle = function(){return false;};

	if(!this._options.doubleClickHandle)this._options.doubleClickHandle = function(){return false};
	if(!this._options.contextMenuHandle)this._options.contextMenuHandle = function(){return false};
	if(this._options.expand==null)this._options.expand = true;
	if(!this._options.imgDir)this._options.imgDir="images/tree/";
	if(!this._options.plus)this._options.plus = this._options.imgDir+"plus.gif";
	if(!this._options.minus)this._options.minus = this._options.imgDir+"minus.gif";
	if(!this._options.folderClosed)this._options.folderClosed = this._options.imgDir+"folderClosed.gif";
	if(!this._options.folderOpen)this._options.folderOpen = this._options.imgDir+"folderOpen.gif";
    if(!this._options.selectedColor)this._options.selectedColor="white";
	if(!this._options.selectedBg)this._options.selectedBg="skyblue";
    this._nameLength = this._options.nameLength;
}
BTree.getObjectsByKeyValue=function(objArr,key,value){
	var n = objArr.length;
	var _r = new Array();
	for (var i=0;i<n ;i++ )if(objArr[i][key]==value)_r[_r.length] = objArr[i];
	return _r;
}
BTree.prototype.setNameLength=function(n){
	this._nameLength=n==null?10:n;
}
BTree.prototype.createNode=function(data){
	var handler=this,
		op=this._options,
		list=this._list,
		handler=this;
	var flag=false,
		_src=null,
		_mirror=null,
		_srcId,
		_targetId=null;
	function setEvent(o,param){//设置事件
		o.onclick = function(){
			if(typeof(op.clickHandler)=="function" && op.clickHandler(param)===false)
				return false;
			flag=false;
			var a=BTree.getObjectsByKeyValue(handler._list,handler._fields["id"],handler._selectedItemId);
			var  selectedItem=(a.length>0?a[0]._item:null);
			handler._selectedItemId=param;
			if(selectedItem){
				selectedItem.style.backgroundColor="transparent";
                selectedItem.style.color="";
            }
			o.style.backgroundColor=op.selectedBg;
            o.style.color=op.selectedColor;
			selectedItem=o;
		}

		function down(){flag=true;_src=this,_srcId=param;	return false;}
		function move(){
			if(!flag||!_src)return false;
			if(!_mirror){
				_mirror=document.createElement("span");
				//if(op.itemClass)_mirror.className=op.itemClass;
				_mirror=_src.cloneNode(true);
				_mirror.style.padding="0 0 0 18px";_mirror.style.margin=0;
				_mirror.style.background=_src.style.background;
				_mirror.innerHTML=_src.innerHTML;
				_mirror.style.position="absolute";
				document.body.appendChild(_mirror);
			}
			var evt=arguments[0]||window.event;
			_mirror.style.zIndex="1001";
			_mirror.style.left=(evt.clientX+10)+"px";
			_mirror.style.top=(evt.clientY-3)+"px";
		}
		function over(){
			if(!flag||!_src)return false;
			var r=handler.getRelation(_srcId,param);
			if(r!=0&&r!=5)return false;
			this.style.backgroundColor="skyblue";
            _targetId=param;
		}
		function out(){
			_targetId=null;
			if(!flag||!_src)return false;
			var r=handler.getRelation(_srcId,param);
			if(r!=0&&r!=5)return false;
			if(handler._selectedItemId!=param)this.style.backgroundColor="transparent";
		}
		function up(){
			if(!flag)return false;
			if(_mirror)
                document.body.removeChild(_mirror);
			if(_srcId!=null&&_targetId!=null&&this==o){
				this.style.backgroundColor="transparent";
				handler.moveItem(_srcId,_targetId);
			}
			flag=false;_mirror=null;	_targetId=null;__srcId=null;
		}
        if(op.dragEnable){
            BElement(document).addEvent("onmousemove",move);
            BElement(document).addEvent("onmouseup",up);
        }

		o.onmousedown=function(){
             if(typeof(op.mousedownHandler)=="function")
                op.mousedownHandler.apply(this,[{data:param,event:arguments[0]||window.event}]);
            if(op.dragEnable)
                down.apply(this);
        }
		o.onmouseover = function(){
            if(typeof(op.mouseoverHandler)=="function")
                op.mouseoverHandler.apply(this,[{data:param,event:arguments[0]||window.event}]);
            if(!op.dragHandle)
                over.apply(this);
        }
		o.onmouseout = function(){
            if(typeof(op.mouseoutHandler)=="function")
                op.mouseoutHandler.apply(this,[{data:param,event:arguments[0]||window.event}]);
            if(op.dragEnable)
                out.apply(this);
        }
		o.onmouseup = function(){
            if(typeof(op.mouseupHandler)=="function")
                op.mouseupHandler.apply(this,[{data:param,event:arguments[0]||window.event}]);
            if(op.dragEnable)
                up.apply(this);
        }
	}

	/*核心：生产节点*/
	function createNode(o){
		var li=document.createElement("li");
		li.style.listStyle="none";
		//handler._lis[handler._lis.length]=li;

		//折叠图片
		var img=document.createElement("img");
		img.style.verticalAlign="middle";
		img.style.margin=0;

		//文件夹图标
		var icon=img.cloneNode(true);
		var a=document.createElement("a");
		//加上其它属性
		for (k in handler._fields){
			if(!k || handler._basicFields[k])continue;
			var keyRef = handler._fields[k];
			var v = o[keyRef];
			if(v){
				a.setAttribute(k,v);
			}
			//a[keyRef] = o[keyRef];
		}
		//if(op.itemClass){a.className=op.itemClass;}
		var _name = o[handler._fields.name]//this._nameLength

        if(op.tagKey)
            o[handler._fields._tag] = o[op.tagKey];

		var _tagStr=_tag= o[handler._fields._tag];
		if(_tag==null || _tag=="" || _tag==undefined)
			_tagStr=_tag="";
		if(_tagStr!="")
			_tagStr= "<b>("+_tag+")</b>";

		a.title = (_name+_tag);

        a.id = "BELEMENT_ID_"+o[handler._fields.id];

		a.innerHTML=(_name+_tagStr);
		if(_name.indexOf("<")<0){
			var _nStr=op.nameLength-BString(_tag).len()-3;
			_name=BString(_name+_tag).len()>op.nameLength?
				(BString(_name).cut(_nStr)+"..."):
				_name;
			a.innerHTML=(_name+_tagStr);
		}
		//保存节点
		o._item=a;//方便设置
		o._li=li;
		o._icon = icon;
		setEvent(a,o[handler._fields.id]);
		li.appendChild(img);
		li.appendChild(icon);
		li.appendChild(a);
		var c=BTree.getObjectsByKeyValue(list,handler._fields.parentid,o[handler._fields.id]);
		var n=c.length;
		if(n>0){
			var child=document.createElement("ul");
			for (var i=0;i<n ;i++ ){
				var _c=c[i];
				child.appendChild(createNode(_c));
			}
			li.appendChild(child);
		}
		return li;
	}
	return createNode(data);
}
BTree.prototype.build = function(selector){
	var handler=this,op=this._options,list=this._list,handler=this;
	var n = list.length;
	var ul=document.createElement("ul");
	if(op.itemClass)ul.className=op.itemClass;
	//如果有外来顶级节点
	if(this._top)
		for (var i=0;i<n ;i++ )
			if(list[i][this._fields.id]!=this._top[this._fields.id])
				//如果没有父节点 就把顶级节点设置成它的父节点
				if(BTree.getObjectsByKeyValue(list,this._fields.id,list[i][this._fields.parentid]).length<1 )
					list[i][this._fields.parentid]=this._top[this._fields.id];

	//循环创建节点
	for (var i=0;i<n ;i++ )
		if(BTree.getObjectsByKeyValue(list,this._fields.id,list[i][this._fields.parentid]).length<1)
			ul.appendChild(this.createNode(list[i]));
	this._ul=ul;
	this.setUI();
	if(!selector&&this._selector){
		this._selector.innerHTML="";
		this._selector.appendChild(ul);
	}
	if(typeof(selector)=="string"){
		var sObj=document.getElementById(selector);
		this._selector=sObj;
		this._selector.innerHTML="";
		sObj.appendChild(ul);
	}
	if(typeof(selector)=="object") {
		this._selector=selector;
		this._selector.innerHTML="";
		selector.appendChild(ul);
	}
}

BTree.prototype.setIconById=function(id,iconPath){
	var o=BTree.getObjectsByKeyValue(this._list,"id",id);
	if(o.length==1){
		if(iconPath==null){
			o[0]._item.parentNode.removeChild(o[0]._item.previousSibling);
		}
		else{
			o[0]._item.previousSibling.src=iconPath;
		}
	}
}
//选中某节点
BTree.prototype.selectedItemById=function(id){
	id= id==null?this._selectedItemId:id;
	var o=BTree.getObjectsByKeyValue(this._list,this._fields.id,id);
	if(o.length==1){
		o[0]._item.style.color=this._options.selectedColor;
        o[0]._item.style.background=this._options.selectedBg;
    }
}
//选中某节点
BTree.prototype.cancelSelectedItem=function(){
	var id= this._selectedItemId;
	if(id==null)return;
	var o=BTree.getObjectsByKeyValue(this._list,this._fields.id,id);
	if(o.length==1)
		o[0]._item.style.background="transparent";
}
//根据id获取name
BTree.prototype.getNameById=function(id){
	var o=BTree.getObjectsByKeyValue(this._list,this._fields.id,id);
	if(o.length>1)
		return o[0][this._fields.name];
	return null;
}
//获取节点id
BTree.prototype.getSelectedItemId=function(){return this._selectedItemId}
BTree.prototype.getItemNameById=function(id){
	if(id==null)id=this._selectedItemId;
	var o=BTree.getObjectsByKeyValue(this._list,"id",id);
	if(o.length==1)return o[0][this._fields.name];
	return null;
}
//根据id修改节点名称
BTree.prototype.setItemNameById=function(id,name){
	var o=BTree.getObjectsByKeyValue(this._list,this._fields.id,id);
	if(o.length==1){
		var _name=(name==null?o[0][this._fields.name]:name);
		var _tagStr = _tag=o[0][this._fields._tag];
		if(_tag==null || _tag=="" || _tag==undefined)
			_tagStr=_tag="";
		if(_tagStr!="")
			_tagStr= "<b>("+_tag+")</b>";
		o[0]._item.innerHTML=_name+_tagStr;
		if(_name.indexOf("<")<0){
			var _nStr=this._options.nameLength-BString(_tag).len()-3;
			_name=BString(_name+_tag).len()>this._options.nameLength?
				(BString(_name).cut(_nStr)+"..."):
				_name;
			o[0]._item.innerHTML=(_name+_tagStr);
		}
	}
}
//设置tag
BTree.prototype.setTag=function(id,tag){
	tag=tag?tag:"";
	var o=BTree.getObjectsByKeyValue(this._list,this._fields.id,id);
	if(o.length==1){
		o[0]._tag = tag;
		this.setItemNameById(id);
	}
}
BTree.prototype.setTop=function(p){
	this._top=p;
	if(p)this._list[this._list.length]=p;
}
//设置图标
BTree.prototype.setIcon=function(id,iconPath){
	var o=BTree.getObjectsByKeyValue(this._list,this._fields.id,id);
	if(o.length>0){
		o[0]._icon.src=iconPath;
	}
}
//移动节点
BTree.prototype.moveItem=function(f,parentId){//移动节点 @para f:要移动的节点id 可以是数组，一次移动多个，parentId:目标节点
	var arr=null;
	if(typeof(f)=="object") arr=f;
	else {arr=new Array();arr[0]=f}
	var n=arr.length;
	for(var i=0;i<n;i++){
		var item=BTree.getObjectsByKeyValue(this._list,this._fields.id,arr[i]);
		if(item.length==1){item[0][this._fields.parentid]=parentId;}
	}
	this.build();
	//this.clickById(this.getSelectedItemId());
    this.clickById(parentId);
}
//
BTree.prototype.getRelation=function(id1,id2){//return 0:没关系 1: 自身 2: 前者是后者父亲  3:爷爷，4：孩子，5，孙子
	if(id1==null||id2==null)return 0;
	if(id1==id2)return 1;
	var handler=this;
	function check(_id1,_id2){
		var arr=handler.getIdPath(_id2);
		var n=arr.length;
		for (var i=0;i<n ;i++ )
			if(arr[i]==_id1){
				if((i+2)==n)
                    return 2;
				else
                    return 3;
			};
		return null;
	}
	var flag1=check(id1,id2),flag2=check(id2,id1);
	return flag1!=null?flag1:(flag2!=null?(flag2==2?4:5):0);
}
//删除节点
BTree.prototype.remove=function(f){//移除节点 可以移除多个
	var arr=null;
	if(typeof(f)=="object")arr=f;
	else{arr = new Array();arr[0]=f;}
	var handler=this,list=this._list,n=list.length;

	function rm(id){
		for(var i=0;i<list.length;i++){
			if(list[i][handler._fields.id]==id){
				list[i].del=true;
				var c=BTree.getObjectsByKeyValue(list,handler._fields.parentid,id);
				var _n=c.length;
				for (var j=0;j<_n;j++ )rm(c[j][handler._fields.id]);
			}
		}
	}
	var _n=arr.length;
	for (var k=0;k <_n;k++ )rm(arr[k]);
	for (var i=(n-1);i>=0 ;i-- ){
		if(this._list[i].del){
			var curNode=this._list[i]._item.parentNode;
			var parentNode=curNode.parentNode;
			parentNode.removeChild(curNode);
			this._list=this._list.slice(0,i).concat(this._list.slice(i+1));
		}
	}

	this.setUI();
	//this.build();
	this.selectedItemById();
}
BTree.prototype.clickById=function(id){//模拟点击事件
	if(!id)return false;
	var o=BTree.getObjectsByKeyValue(this._list,this._fields.id,id);
	if(o.length!=1)return false;
	var item=o[0]._item;
	try{
        item.click();
    }
	catch(e){
		var evt = item.ownerDocument.createEvent("MouseEvents");
		evt.initEvent("click", false, true);
		item.dispatchEvent(evt);
	}
}
BTree.prototype.getPath=function(id){//获取节点路径 返回数组
	if(!id)id=this.getSelectedItemId();
	if(!id)return [];
	var handler=this,list=this._list;
	function getPath(id){
		if(!id)return "";
		var o=BTree.getObjectsByKeyValue(list,handler._fields.id,id);
		if(o.length==1)return (o[0][handler._fields.parentid]?(getPath(o[0][handler._fields.parentid])+"|"):"")+o[0][handler._fields.name];
		return "";
	}
	return getPath(id).split("|");
}
BTree.prototype.getIdPath=function(id){//获取节点id路径 返回数组
	if(!id)id=this.getSelectedItemId();
	if(!id)return [];
	var handler=this,list=this._list;
	function getPath(id){
		if(!id)
            return "";
		var o=BTree.getObjectsByKeyValue(list,handler._fields.id,id);
		if(o.length==1)
            return (o[0][handler._fields.parentid]?(getPath(o[0][handler._fields.parentid])+"|"):"")+o[0][handler._fields.id];
		return "";
	}
	return getPath(id).split("|");
}
BTree.prototype.getIdPathFrom=function(id){//获取从id开始的子路径 返回包含数组的数组
	if(!id)
        id=this.getSelectedItemId();
	if(!id)
        return [];
	var handler=this,
        list=this._list,
        path=[];
	function getPath(id){
		if(!id)
            return [];
        //该节点数据
		var o=BTree.getObjectsByKeyValue(list,handler._fields.id,id);
        if(o.length<1)
            return [];
        var _curPath = [];
        var children = handler.getChildren(id);
        if(children && children.length > 0){
            var n_children = children.length;
            for (var i=0;i<n_children ;i++ ){
                var _childPath = getPath(children[i][handler._fields.id]);
                var n_childPath = _childPath.length;
                for (var j=0;j<n_childPath ;j++ ){
                    _curPath.push([id].concat(_childPath[j]));
                }
            }
        }
		return _curPath.length>0?_curPath:[[id]];
	}
    path = getPath(id);
	return path;
}

BTree.prototype.add=function(f){//添加节点
	var arr=null;
	if(f instanceof Array)arr=f;
	else{arr=new Array();arr[0]=f;}
	var n=arr.length;
	for (var i=0;i<n ;i++ )
		//没有重复id 就添加
		if(BTree.getObjectsByKeyValue(this._list,this._fields.id,arr[i][this._fields.id]).length<1){
			var pid = arr[i][this._fields.parentid], parentNode=null;
			if(pid==null || pid==undefined)
				parentNode=null;
			else{
				var p=BTree.getObjectsByKeyValue(this._list,this._fields.id,pid);
				if(p.length>0)
					parentNode=p[0]._li;
			}
			if(parentNode==null)
				if(this._top)
					parentNode = this._top._li;
				else
					parentNode = this._ul.firstChild;
			if(parentNode.childNodes.length>3)
				parentNode=parentNode.lastChild;
			else{
				var ul=document.createElement("ul");
				parentNode.appendChild(ul);
				parentNode = ul;
			}
			parentNode.appendChild(this.createNode(arr[i]));
			this._list[this._list.length]=arr[i];
		}
	this.setUI();
	this.selectedItemById();
}
BTree.prototype.upwards=function(id){//模拟点击父亲节点
	if(!id)id=this.getSelectedItemId();
	var o=BTree.getObjectsByKeyValue(this._list,this._fields.id,id);
	if(o.length<1||!o[0][this._fields.parentid])return;
	this.clickById(o[0][this._fields.parentid]);
}
BTree.prototype.copy=function(selector,op){//复制树
	var tree=new BTree(this._list,op?op:this._options);
	tree.build(selector);
	return tree;
}
BTree.prototype.update=function(list){//更新树
	this._list=list;
	this.build();
	this.clickById(this.getSelectedItemId());
}
BTree.prototype.setUI=function(){//画线
	var op=this._options;
	var handler=this;

	function collspan(s,t,img1,img2,flag){//展开 合拢
		if(flag==null)flag=true;
		s.nextSibling.src=(flag?op.folderOpen:op.folderClosed);
		s.src=op.imgDir+(flag?img1:img2);
		t.style.display=(flag?"":"none");
		s.onclick=function(){
			if(t.style.display=="none"){
				s.src=op.imgDir+img1;
				t.style.display="";
				s.nextSibling.src=op.folderOpen;
			}
			else{
				s.src=op.imgDir+img2;
				t.style.display="none";
				s.nextSibling.src=op.folderClosed;
			}
		}
	}

	var lis=this._list,n=lis.length,op=this._options;
	for (var i=0;i<n ;i++ ){
		var li=lis[i]._li;
		var c1=(!li.parentNode.parentNode||li.parentNode.parentNode.nodeType==11)?0:1;
		c1=(li.parentNode==this._ul)?0:1;
		var c2=li.previousSibling?1:0;
		var c3=li.nextSibling?1:0;
		var c4=li.childNodes.length>3?1:0;
        c4 = c4==1?(li.lastChild.childNodes.length>0?1:0):0;
		var code=c1*1000+c2*100+c3*10+c4;
		var cImg=op.folderClosed;
        if(!op.dirLine){
            switch(code){
                case 0:{//1. 孤家寡人
                    li.removeChild(li.firstChild);
                    li.firstChild.src = cImg;
                    break;
                }
                case 1://2. 只有子节点
                case 11://4. 有后继有孩子
                case 101:case 1001:case 1101://只有前驱和后继
                case 111:case 1111:case 1011://8. 有前驱后继和孩子
                {
                    collspan(li.firstChild,li.lastChild,"minus.gif","plus.gif",op.expand);
                    break;
                }
                case 10://3. 只有后继
                case 100:case 1000:case 1100://5. 只有前驱
                case 110:case 1010:case 1110:
                {
                    li.firstChild.nextSibling.src = cImg;
                    li.firstChild.style.display = "none";
                    li.firstChild.nextSibling.style.marginLeft = "18px";
                    break;
                }
                default:{break;}
            }//end switch
            continue;
        }
        else{
            switch(code){
                case 0:{//1. 孤家寡人
                    li.removeChild(li.firstChild);
                    li.firstChild.src = cImg;
                    break;
                }
                case 1:{//2. 只有子节点
                    collspan(li.firstChild,li.lastChild,"minus1.gif","plus1.gif",op.expand);
                    break;
                }
                case 10:{//3. 只有后继
                    li.firstChild.src=op.imgDir+"line1.gif";
                    li.firstChild.nextSibling.src = cImg;
                    break;
                }
                case 11:{//4. 有后继有孩子
                    collspan(li.firstChild,li.lastChild,"minus2.gif","plus2.gif",op.expand);
                    //li.lastChild.src = cImg;
                    li.lastChild.style.background="url('"+op.imgDir+"line2.gif') repeat-y";
                    break;
                }
                case 100:case 1000:case 1100:{//5. 只有前驱
                    li.firstChild.src=op.imgDir+"line4.gif";
                    li.firstChild.nextSibling.src = cImg;
                    break;
                }
                case 101:case 1001:case 1101:{//6. 只有前驱和孩子
                    collspan(li.firstChild,li.lastChild,"minus4.gif","plus4.gif",op.expand);
                    break;
                }
                case 110:case 1010:case 1110:{//7. 只有前驱和后继
                    li.firstChild.src=op.imgDir+"line3.gif";
                    li.firstChild.nextSibling.src = cImg;
                    break;
                }
                case 111:case 1111:case 1011:{//8. 有前驱后继和孩子
                    collspan(li.firstChild,li.lastChild,"minus3.gif","plus3.gif",op.expand);
                    li.lastChild.style.background="url('"+op.imgDir+"line2.gif') repeat-y";
                    break;
                }
                default:{break;}
            }//end switch
        }
	}//end for
}

BTree.prototype.setProperty=function(id,key,value){
	if(id==null||key==null)return false;
	var list=this._list,n=list.length;
	for(var i=0;i<n;i++){
		var o=list[i];
		if(o["id"]==id)o[key]=value;
	}
}
BTree.prototype.getProperty=function(id,key){
	if(id==null||key==null)return false;
	var list=this._list,n=list.length;
	for(var i=0;i<n;i++){
		var o=list[i];
		if(id==o[ this._fields["id"] ]){return o[key];}
	}
	return null;
}

BTree.prototype.getChildren=function(id){
	return BTree.getObjectsByKeyValue(this._list,this._fields.parentid,id);
}
BTree.prototype.setFieldName=function(o){
	if( !o || typeof(o)!="object")
		return;
	for ( k in o ){
		this._fields[k] = o[k];
	}
}
BTree.prototype.setAttributions=function(o){
	if( !o || typeof(o)!="object")
		return;
	for ( k in o ){
		this._attrs[k] = o[k];
	}
}