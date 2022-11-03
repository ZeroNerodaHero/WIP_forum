function addImg(){
    document.getElementById("textArea").value += "[IMG]()";
}
function addLink(){
    document.getElementById("textArea").value += "[LNK]()";
}
function addYTB(){
    document.getElementById("textArea").value += "[YTB]()";
}
function addVIDEO(){
    document.getElementById("textArea").value += "[VIDEO]()";
}

function quotePost(val){
    var toBeInsert = document.createElement("div");
    toBeInsert.innerHTML ="##"+ val+" ";
    document.getElementById("newTextArea").appendChild(toBeInsert);
    document.getElementById("newTextArea").append(document.createElement("br"));
    buttonDown();
}

function jumpPost(val){
    var ele = document.getElementById(val);
    var top = ele.offsetTop;
    window.scrollTo(0,top-35);
    
    var oldColor= ele.style.backgroundColor;
    ele.style.backgroundColor = "rgba(238,130,238,0.5)";
    ele.style.boxShadow =       "0px -7px 2px 0px rgba(238,130,238,0.5)";
    ele.style.webkitBoxShadow = "0px -7px 2px 0px rgba(238,130,238,0.5)";

    setTimeout(function() {
        ele.style.backgroundColor = oldColor;
        ele.style.boxShadow = "";
        ele.style.webkitBoxShadow = "";
    },500);
}

function threadRedirect(redirect,TID){
    var acclaim_ele = document.getElementById("acclaimTID_"+TID);
    //console.log(acclaim_ele.matches(":hover"));
    if(!acclaim_ele || (acclaim_ele && !acclaim_ele.matches(":hover"))){
        window.location = redirect;
    }
}

function threadHover(TID){
    var acclaim_ele = document.getElementById("acclaimTID_"+TID);
    //console.log(acclaim_ele.matches(":hover"));
    acclaim_ele.style.display = "inline";
}
function threadUnHover(TID){
    var acclaim_ele = document.getElementById("acclaimTID_"+TID);
    //console.log(acclaim_ele.matches(":hover"));
    if(acclaim_ele.innerHTML == "+"){
        acclaim_ele.style.display = "none";
    } else{
        document.addEventListener('click',function(event){
            acclaim_ele.style.display = "none";
            document.removeEventListener('click',arguments.callee);
        });
    }
}

function headerRedirect(page){
    var header_ele = document.getElementById("PageHeader");
    header_ele.addEventListener("click", function() 
        { window.location = "?page="+page; });
    header_ele.addEventListener("mouseenter", function() 
        { header_ele.style.backgroundColor="#ea82df"; });
    header_ele.addEventListener("mouseleave", function() 
        { header_ele.style.backgroundColor="";});
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

function animateWatcherLnk(ele,diff){
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
    var cc = document.getElementsByClassName("watchingLnk");

    for(var i = 0; i < cc.length; i++){
        var overflowPx = checkOverflow(cc[i]);
        if(overflowPx > 0){
            animateWatcherLnk(cc[i],overflowPx);
        }
    }  
}

function closeRecent(){
    var ele = document.getElementById("watchThread");
    ele.style.display = "none";
}

function expandImg(){
    var allImg = document.getElementsByTagName("img");

    for(let it of allImg){
        if(it.className != '') continue;
        it.addEventListener("click",function(){
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

function loadAcclaim(board,TID){
    var xhttp = new XMLHttpRequest();
    var ele = document.getElementById("acclaimTID_"+TID);

    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200 && ele.innerHTML == "+"){
        //if(this.readyState == 4 && this.status == 200){
            ele.innerHTML = this.responseText;
            console.log("fine")
            document.addEventListener('click',function(event){
                if(!ele.matches(":hover")){
                    ele.innerHTML = "+"; 
                    document.removeEventListener('click',arguments.callee);
                    ele.style.backgroundColor="";
                }
            });
        }
    }

    xhttp.open("GET","acclaimGenerator/loadAcclaim.php?board="+board+"&TID="+TID,true);
    xhttp.send();
            console.log("WTF")
}

function expandEmote(ele){
    var childImg = document.createElement("img");
    childImg.id= "activeEmoteExpanded";
    childImg.src= ele.src;

    ele.parentElement.appendChild(childImg);

    ele.style.boxShadow="0px 6px 0px -4px #000000";
    ele.style.webkitBoxShadow="0px 6px 0px -4px #000000";
}
function deflateEmote(ele){
    //assumes child exists
    ele.parentElement.removeChild(ele.parentElement.lastChild);
    ele.style.boxShadow="";
    ele.style.webkitBoxShadow="";
}
function addEmote(ele,board,TID,opt){
    //update the current points
    //update the current emotes
    var xhttp = new XMLHttpRequest();
    var ele = document.getElementById("acclaimCont_"+TID);

    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            //usr doesn't have enough points
            if(this.responseText == "0"){
                var errorBox= document.createElement("div");
                errorBox.className= "noncontentMsg";
                errorBox.innerHTML = "ERROR: YOU DO NOT HAVE ENOUGH POINTS. "+
                                    "NEED AT LEAST 100 FOR A EMOTE. START SLAVING BOI";
                document.getElementById("boardHeader").appendChild(errorBox);
                setTimeout(function(){
                    errorBox.remove();
                }, 2000);
                return;
            }

            //usr has enough points
            var eleChildren = ele.childNodes;
            for(let it of eleChildren){
                if(it.className=="acclaimList"){
                    it.outerHTML= this.responseText;
                    if(this.responseText.length != 0){
                        it.style.backgroundColor="#ff6ade";
                        it.style.marginRight="10px";
                    }
                    break;
                }
            }
        }
    };
    xhttp.open("GET","acclaimGenerator/updateAcclaim.php?board="
                +board+"&TID="+TID+"&opt="+opt,false);
    xhttp.send();

    var eleScore = document.getElementById("usrPointCount");

    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            eleScore.innerHTML = this.responseText;
        }
    };
    xhttp.open("GET","acclaimGenerator/updateUsrScore.php",true);
    xhttp.send();
}

function deleteOldLocalStorage(board){ 
    window.localStorage.removeItem(board+"_lastThread");
}

//initially used to generate a random number trhoug a seed
//bc js doesn't have it. not used
function genMod(num){
    var ret = 0;
    var shift = (num)%7+1;
    for(var i = 0; i < num; i++){
        ret = (ret<<num)|num;
    }
    return ret;
}
