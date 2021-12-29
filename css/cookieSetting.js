/*
I use an integer to set up the values in the settings

use ot^(1<<setting number)
find setting number below

xxxx xxxx xx...
0123 4567 89...

0: collapseNav?
*/

function toggleNav(toggleOrNot=1){
	var ot = 1;
	if(document.cookie != ""){
		ot = getCookie("settings");
	}
	ot = (ot^(toggleOrNot<<0))&1;
	setCookie("settings", ot);
	togglePage("navContainer",ot)

	var ele = document.getElementById("navCollapseText");
	if(ot){
		ele.classList.add("bottom");
		ele.classList.remove("top");
	}else{
		ele.classList.remove("bottom");
		ele.classList.add("top");
	}
}

function togglePage(docId,sig){
	var ele = document.getElementById(docId);
	if(sig){
		ele.style.display="none";
	}else{
		ele.style.display="block";
	}
}

function setCookie(key,val){
	const d = new Date();
  	d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
    let expires = "expires="+d.toUTCString();
	document.cookie = key + "=" + val + ";" + expires + ";path=/";
	console.log(document.cookie);
}

function getCookie(cname) {
	let name = cname + "=";
	let ca = document.cookie.split(';');
	for(let i = 0; i < ca.length; i++) {
		let c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
}
