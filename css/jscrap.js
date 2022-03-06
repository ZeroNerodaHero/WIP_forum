function quotePost(val){
    console.log("post is " + val);
    document.getElementById("textArea").value += "##"+ val+"\n";
}

function jumpPost(val){
    var ele = document.getElementById(val);
    var top = ele.offsetTop;
    window.scrollTo(0,top-10);
    
    var oldColor= ele.style.backgroundColor;
    ele.style.backgroundColor = "rgba(238,130,238,0.2)";
    ele.style.boxShadow = "0px -3px 1px 5px rgba(238,130,238,0.2)";
    ele.style.webkitBoxShadow = "0px -3px 1px 5px rgba(238,130,238,0.2)";
    console.log(oldColor);

    setTimeout(function() {
        ele.style.backgroundColor = oldColor;
        ele.style.boxShadow = "";
        ele.style.webkitBoxShadow = "";
    },500);
}

function threadRedirect(redirect){
    window.location = redirect;
}

function headerRedirect(page){
    document.getElementById("PageHeader").addEventListener("click",
        function() { window.location = "?page="+page; }
    );
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


function checkOverflow(ele){
    if(ele.scrollWidth < ele.clientWidth){
        return 0;
    }
    return ele.scrollWidth - ele.clientWidth;
}

function animateRecent(ele,diff){
    //constant help better look
    diff += diff/5;
    //assume overflow
    var animateTime = 9000;
    var animateFrameTime = 300;
    var fpA = animateTime / animateFrameTime;
    var cpf = diff/fpA;

    var currentSkew = 0;

    //intervaled
    setInterval(function(){
        if(Math.abs(currentSkew) >= diff){
            currentSkew = 0;
        } else{
            currentSkew -= cpf;
        }
        //console.log(currentSkew + " " + ele.style.marginLeft+ ":");
        ele.style.marginLeft = currentSkew+"px";
    },animateFrameTime);
}

function recentAnimateAll(){
    var ele = document.getElementById("recLinkCont");
    var cc = ele.children;

    for(var i = 0; i < cc.length; i++){
        var overflowPx = checkOverflow(cc[i]);
        if(overflowPx > 0){
            animateRecent(cc[i],overflowPx);
        }
    }  
}

function closeRecent(){
    var ele = document.getElementById("recentThread");
    ele.style.display = "none";
}

function expandImg(){
    var allImg = document.getElementsByTagName("img");

    for(let it of allImg){
        it.addEventListener("click",function(){
            console.log(it.style.width);
            it.style.width=(it.style.width != "100%") ?"100%":"100px";}
        );
    }
}

function precentToHex(p){
    var percent = Math.max(0,Math.min(p,100));
    var intVal = Math.round(p/100 * 255);
    var hexval = intVal.toString(16);
    return hexval.padStart(2,'0').toUpperCase();
}
