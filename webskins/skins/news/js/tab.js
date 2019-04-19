// JavaScript Document
var current_id="tatab1";
function ShowItem(id){
	var prev_obj = document.getElementById("item_" + current_id);
	var obj = document.getElementById("item_" + id);
	if(prev_obj){
		prev_obj.style.display = "none";
	}
	document.getElementById("tab_" + current_id).className = "hnews_tab_unselected";
	if(obj){
		obj.style.display = "block";
		current_id = id;
	}
	document.getElementById("tab_" + id).className = "hnews_tab_selected";
}
var current_iddn="dntab1";
function ShowItemDN(id){
	var prev_obj = document.getElementById("item_" + current_iddn);
	var obj = document.getElementById("item_" + id);
	if(prev_obj){
		prev_obj.style.display = "none";
	}
	document.getElementById("tab_" + current_iddn).className = "hnews_tab_unselected";
	if(obj){
		obj.style.display = "block";
		current_iddn = id;
	}
	document.getElementById("tab_" + id).className = "hnews_tab_selected";
}