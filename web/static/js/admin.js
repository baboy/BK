
$(document).ready(function(){
	HashEventManager.getInstance().addEventListener("edit",function(sid){
		BWinMgr.getInstance().openOverlay("/xman/edit/?sid="+sid,{unique:true,"name":"edit"});
	});
	var h = window.location.hash;
	h = h.substring(1);
	HashEventManager.getInstance().fire(h);
});
