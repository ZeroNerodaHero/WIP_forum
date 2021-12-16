function quotePost(val){
    console.log("post is " + val);
    document.getElementById("textArea").value += "##"+ val+"\n";
}

function jumpPost(val){
    var ele = document.getElementById(val);
    var top = ele.offsetTop;
    window.scrollTo(0,top-10);
    
    var oldColor= ele.style.backgroundColor;
    ele.style.backgroundColor = "#606060";

    setTimeout(function() {
        ele.style.backgroundColor = oldColor;
    },500);
}

function toggleNav(){
	var ot = 0;
	if(document.cookie == ""){
		setCookie("orient",ot);
	} else{
		ot = getCookie("orient");
	}
	setCookie("orient", (ot ==0 ? 1 : 0));
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

function threadRedirect(redirect){
	window.location = redirect;
}

function headerRedirect(page){
	document.getElementById("PageHeader").addEventListener("click",
		function()
		{
			window.location = "frontpage.php?page="+page;
		});
}
