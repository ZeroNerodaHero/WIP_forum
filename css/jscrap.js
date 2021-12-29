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

function threadRedirect(redirect){
	window.location = redirect;
}

function headerRedirect(page){
	document.getElementById("PageHeader").addEventListener("click",
		function()
		{
			window.location = "?page="+page;
		});
}

function buttonUp(){
    var ele = document.getElementById("board");
	var ele2 = document.getElementById("boardHeader").offsetHeight;
    var top = ele.offsetTop;
    window.scrollTo(0,top-ele2-10);
}
function buttonDown(){
    var ele = document.getElementById("board");
    var bot = ele.offsetHeight;
    window.scrollTo(0,bot-10);
}

function showFuncButtons(val){
    var ele = document.getElementById("functionButtonCont");
	//what is the difference between inline and block?
	if(val){
		ele.style.display = "inline";	
	} else{
		ele.style.display = "none";	
	}
}
