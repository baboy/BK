var HashEventManager = function(){
	this.events = {};
};
HashEventManager.getInstance = function(){
	if (!window.hashEventManager) {
		window.hashEventManager = new HashEventManager();
		window.onhashchange = function(){
			var h = window.location.hash;
			h = h.substring(1);
			HashEventManager.getInstance().fire(h);
		};
	};
	return window.hashEventManager;
};
HashEventManager.prototype = {
	addEventListener:function(hash,func){
		this.events[hash] = func;
	},
	fire:function(hash){
		if (!hash) {
			hash = "_default_";
		};
		var p = hash.split("/");
		var k = p[0];
		p.splice(0,1);
		console.log(this.events);
		var func = this.events[k];
		if (!func) {
			k = "_default_";
			func = this.events[k];
		};
		if (func) {
			return func.apply(null,p);
		};
		console.log("fire:"+k+","+func);
		/**
		/edit/{module}/sid
		
		**/
	}
};
$(document).ready(function(){
	HashEventManager.getInstance().addEventListener("edit",function(sid){
		BWinMgr.getInstance().openOverlay("/xman/edit/?sid="+sid,{unique:true,"name":"edit"});
	});
	var h = window.location.hash;
	h = h.substring(1);
	HashEventManager.getInstance().fire(h);
});
