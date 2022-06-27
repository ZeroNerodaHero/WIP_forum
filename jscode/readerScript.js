var global_lineWidth = 2;
var imgWidth = 0, imgHeight = 0, imgLeft = 0, imgTop= 0;
var minSize = 4, maxSize = 900000;
var maxX=-1,maxY=-1,minX=-1,minY=-1;
var itCount = 0;
var colorSelect="red",colorRead="#f1897349";
//used for quick access. no checks given whether or not
//this is valid
var threadID=-1;
var boardName=null;
const allComments = [];

//system matically generate all img.
//post in succession
class commentRect{
    constructor(pid,uid,time,pSx,pSy,pEx,pEy,strComment,jsonResponse,responseCnt){
        if(pSx > pEx){
            [pSx,pEx]=[pEx,pSx];
        }
        if(pSy > pEy){
            [pSy,pEy]=[pEy,pSy];
        }

        this.pid =pid;
        this.uid =uid;

        //proportional
        this.pSx=pSx;
        this.pSy=pSy;
        this.pEx=pEx;
        this.pEy=pEy;

        //calculauted
        this.sx=Math.floor(pSx*imgWidth);
        this.sy=Math.floor(pSy*imgHeight);
        this.ex=Math.floor(pEx*imgWidth);
        this.ey=Math.floor(pEy*imgHeight);

        this.strComment = strComment;
        this.time = time;
        this.jsonResponse = jsonResponse;
        this.responseCnt= responseCnt;
    }
    isInRectangle(x,y){
        return (this.sx <= x && this.ex >= x && 
                this.sy <= y && this.ey >= y);
    }
    updateNewDimension(){
        this.sx=Math.floor(this.pSx*imgWidth);
        this.sy=Math.floor(this.pSy*imgHeight);
        this.ex=Math.floor(this.pEx*imgWidth);
        this.ey=Math.floor(this.pEy*imgHeight);
    }

    print(){
        console.log(this.sx + ","+this.sy+" -> "+this.ex +","+this.ey);
    }
}
function imgRenderSizeUpdate(imgLayer,usrCanvas,botCanvas,highlightCanvas){
    imgWidth = imgLayer.offsetWidth, 
    imgHeight = imgLayer.offsetHeight,
    imgLeft = (imgLayer.offsetLeft-imgLayer.style.marginLeft),
    imgTop= (imgLayer.offsetTop-imgLayer.style.marginTop);

    usrCanvas.style.left = botCanvas.style.left = highlightCanvas.style.left = imgLeft+"px";
    usrCanvas.style.top= botCanvas.style.top= highlightCanvas.style.top = imgTop+"px";
    usrCanvas.width = botCanvas.width = highlightCanvas.width = imgWidth;
    usrCanvas.height = botCanvas.height = highlightCanvas.height = imgHeight;
}

function imgRenderInit(board,threadId){
    boardName=board;
    threadID = threadId;
    
    //for resizes
    var otherCommentEle = document.getElementById("otherCommentCont");
    var allCommentEle = document.getElementById("imgCommentCont");
    var nheight = window.innerHeight * 0.75;
    allCommentEle.style.height=nheight+"px";
    otherCommentEle.style.height=(nheight-100)+"px";

    var usrCommentEle= document.getElementById("usrCommentCont");

    var imgLayer = document.getElementById("imgContLayer");
    var usrCanvas= document.getElementById("upperCanvas");
    var usrCtx = usrCanvas.getContext("2d");
    var botCanvas = document.getElementById("lowerCanvas");
    var botCtx= botCanvas.getContext("2d");
    var highlightCanvas = document.getElementById("highlightCanvas");
    var highlightCtx= highlightCanvas.getContext("2d");

    window.addEventListener('resize', function(){
        if(imgWidth != imgLayer.offsetWidth || imgHeight !=imgLayer.offsetHeight ){
            //console.log("resize "+imgLayer.offsetWidth + " " +imgLayer.offsetHeight );
            imgRenderSizeUpdate(imgLayer,usrCanvas,botCanvas,highlightCanvas);
            redrawComments(botCtx);
        }
    });
    imgRenderSizeUpdate(imgLayer,usrCanvas,botCanvas,highlightCanvas);
    genComments(botCtx,allComments,board,threadId);

    var isDown = false;
    var sx=0,sy=0,ex=0,ey=0;

    //choose whether it is mouse(0) or touch(1)
    var deviceType = 0; 
    const eventType = ["mousedown","mousemove","mouseup"];
    if("ontouchstart" in document.documentElement &&
      navigator.userAgent.match(/Mobi/)){
        deviceType = 1; 
        eventType[0] = "touchstart";
        eventType[1] = "touchmove";
        eventType[2] = "touchend";
        
    }


    //add command to tell initial mousedown position
    //on mouse up see difference
    //create rectangle
    //how this code is written: if(mouse or touch) do mouse or touch action
    //it might seem like replicated code, however i have to add a few extra
    //parameters for mobile
    ////down/start
    usrCanvas.addEventListener(eventType[0],function(){
        var bound = event.target.getBoundingClientRect();
        isDown = true;
        sx = (!deviceType) ? event.offsetX : (event.touches[0].clientX-bound.left);
        sy = (!deviceType) ? event.offsetY : (event.touches[0].clientY-bound.top);
        if(deviceType) event.preventDefault();
//console.log(bound.left+' '+bound.top);
//console.log(sx+' '+sy);
//console.log(event);
        maxX=sx,maxY=sy,minX=sx,minY=sy;

        //clear whole canvas
        clearCanvas(usrCtx);
        showComments(sx,sy,allComments);
    });
    ////move
    usrCanvas.addEventListener(eventType[1],function(){
        var bound = event.target.getBoundingClientRect();
        moveX = (!deviceType) ? event.offsetX : (event.changedTouches[0].clientX-bound.left);
        moveY = (!deviceType) ? event.offsetY : (event.changedTouches[0].clientY-bound.top);

        if(isDown){
//console.log(bound.left+' '+bound.top);
//console.log("rect->"+sx+','+sy+" to "+ex+","+ey);
            ex = moveX;
            ey = moveY;
            maxX=Math.max(maxX,ex),maxY=Math.max(maxY,ey);
            minX=Math.min(minX,ex),minY=Math.min(minY,ey);
            //console.log(minX+","+minY+":::"+maxX+","+maxY);

            //clear from min to max
            clearCanvas(usrCtx,minX,minY,maxX,maxY,0);
            drawRect(usrCtx,sx,sy,ex,ey);
        } else{
            highlightRect(highlightCtx,moveX,moveY,allComments);
        }
    });
    usrCanvas.addEventListener(eventType[2],function(){
        var bound = event.target.getBoundingClientRect();
        ex = (!deviceType) ? event.offsetX : (event.changedTouches[0].clientX-bound.left);
        ey = (!deviceType) ? event.offsetY : (event.changedTouches[0].clientY-bound.top);
        
        if(isDown){
            maxX=Math.max(maxX,ex),maxY=Math.max(maxY,ey);
            minX=Math.min(minX,ex),minY=Math.min(minY,ey);

            var highSize = Math.abs((ex-sx)*(ey-sy));
            if(highSize > minSize && highSize < maxSize){
                usrCommentEle.style.display="block";
            } else{
                usrCommentEle.style.display="none";
            }

            clearCanvas(usrCtx,minX,minY,maxX,maxY,0);
            drawRect(usrCtx,sx,sy,ex,ey);
            isDown = false;
        }
    });

    var usrCommentButton= document.getElementById("usrCommentSubmit");
    usrCommentButton.addEventListener("click",function(){
        clearCanvas(usrCtx);
        postComment(sx,sy,ex,ey,board,threadId);
        clearCanvas(botCtx);
        genComments(botCtx,allComments,board,threadId);
        usrCommentEle.style.display="none";
    });
    //every 100ms check is size has changed
    //if after 3 secs-> delete inteval
    var timeCheck = 0;
    var checkResize = setInterval(function(){
        if(imgWidth != imgLayer.offsetWidth || imgHeight !=imgLayer.offsetHeight ){
            //console.log("reloaded well");
            imgRenderSizeUpdate(imgLayer,usrCanvas,botCanvas,highlightCanvas);
            redrawComments(botCtx,allComments,board,threadId);
            timeCheck = 0;
        } else{
            if(timeCheck >= 3000) {
                clearInterval(checkResize);
                //console.log("no timer");
            } else{
                timeCheck+=100;
            }
        }
    },100);
}

/*
 * draws a rect given
 * start x,y
 * end x,y
 * max x,y
 * no position have to be set, function auto 
 */
function drawRect(usrCtx,sx,sy,ex,ey,color="#123456a5",debug=0){
    usrCtx.strokeStyle = 'black';
    usrCtx.lineWidth   = global_lineWidth;
    usrCtx.fillStyle=color;
//debug
if(debug){
    usrCtx.fillRect(sx-2,sy-2,4,4);
    usrCtx.fillRect(ex-2,ey-2,4,4);
}


    usrCtx.fillRect(sx,sy,ex-sx,ey-sy);
    usrCtx.strokeRect(sx,sy,ex-sx,ey-sy);
}

function clearCanvas(ctx,sx=0,sy=0,ex=imgWidth,ey=imgHeight,isCanvas=true){
    if(!isCanvas){
        sx=Math.max(0,sx-global_lineWidth);
        sy=Math.max(0,sy-global_lineWidth);
        ex=Math.min(ex+global_lineWidth, imgWidth);
        ey=Math.min(ey+global_lineWidth, imgHeight);
    }
    ctx.clearRect(sx,sy,(ex-sx),(ey-sy));
}

function genComments(ctx,rectAr,cBoard,cThread){
    rectAr.length=0;
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var jsonVal = this.responseText;
            var jsonObj = JSON.parse(jsonVal);
            var mainData = jsonObj["data"];

            for(var i = 0; i<mainData.length;i++){
                var subData = mainData[i];
                /*
                var bSx = Math.floor(subData[3][0]*imgWidth);
                var bSy = Math.floor(subData[3][1]*imgHeight);
                var bEx = Math.floor(subData[3][2]*imgWidth);
                var bEy = Math.floor(subData[3][3]*imgHeight);
                */
                rectAr.push(new commentRect(
                    subData[0],subData[1],subData[2],
                    subData[3][0],subData[3][1],subData[3][2],subData[3][3],
                    subData[4],
                    subData[5],subData[6],
                ));
            }
        }
        for(var cObj of rectAr){
            drawRect(ctx,cObj.sx,cObj.sy,cObj.ex,cObj.ey,colorRead);
        }
    }
    xhttp.open("GET", "readerPhp/readerGetComments.php?board="+cBoard+"&TID="+cThread);
    xhttp.send();

}
function redrawComments(ctx){
    //i need to update the bSxs
    for(var cObj of allComments){
        cObj.updateNewDimension();
        drawRect(ctx,cObj.sx,cObj.sy,cObj.ex,cObj.ey,colorRead);
    }
}

function showComments(pos_x,pos_y,rectAr){
    var eleComments = document.getElementById("otherCommentCont");
    deleteAllChildren(eleComments);
    for(var cObj of rectAr){
        if(cObj.isInRectangle(pos_x,pos_y)){
            showCommentText(eleComments,cObj.strComment,cObj);
        }
    }
}
function showCommentText(eleComments,strComment,commentObj=null,color=""){
    var pId = -1,uId=-1,cTime=-1;
    var type = 0;
    
    if(commentObj != null){
        pId = commentObj.pid,uId=commentObj.uid,cTime=commentObj.time;
        type = 1;
    }

    var tmp = document.createElement("div");
    tmp.className = "imgComment";
    tmp.id= "iC_"+pId;

    var innerTXT= "<p class=contentCommentBox>"+strComment+"</p>";
    if(type==1){
        innerTXT = genericImgComment(uId,cTime,strComment,pId,commentObj.responseCnt);
    } else{
        innerTXT="<div class=readerCommentHead><u>SERVER_RESPONSE</u></div>"+innerTXT;
        tmp.style.backgroundColor="red";
    }
    var commentMainBody = document.createElement("div");
    commentMainBody.className = "commentCont";
    commentMainBody.id="iC_"+pId+"_commentBody";
    commentMainBody.innerHTML = innerTXT;

    if(type!=0){
        commentMainBody.addEventListener('click', function(){
            //theoretically only 1 should exist if there is no expansion
            if(document.getElementById("iC_"+pId).childElementCount == 1){
                refreshResponseComments(pId);
            }
        });
    }
    tmp.appendChild(commentMainBody);

    if(color!="")tmp.style.backgroundColor=color;
    if(type != 0) eleComments.appendChild(tmp);
    else{
        eleComments.insertBefore(tmp,eleComments.firstChild);
    }
}

function genericImgComment(uId,cTime,strComment,pId=-1,responseCnt=-1){
    var responseStr = (responseCnt == -1 ? "":
                    "<div id=p_"+pId+"_responseCnt class=commentResponseCnt>"+
                    "Comment(s): "+responseCnt+"</div>");
    var innerTXT= "<div class=contentCommentBox><br>"+strComment+"</div>";
    innerTXT = "<div class=commentInfoCont>"+
            "<span class=idCommentBox>"+uId+"&nbsp&nbsp&nbsp&nbsp</span>"+
            "<span class=timeCommentBox>"+cTime+"</span>"+"</div>"+
            innerTXT + responseStr;
    return innerTXT;
}

function refreshResponseComments(pId){
    var commentEle = document.getElementById("iC_"+pId);
    var commentExpanded = document.getElementById("commentExpanded");
    if(commentExpanded != null){
        commentExpanded.remove();
    }
console.log("uhhh update");

    commentExpanded = expandResponseComments(pId);
    commentEle.appendChild(commentExpanded);
    var commentResponseTextarea = createTextArea(pId);
    commentExpanded.appendChild(commentResponseTextarea);
}

function expandResponseComments(pId){
    var eleExpandedComment = document.createElement("div");
    eleExpandedComment.id = "commentExpanded";

    var eleResponseCont = document.createElement("div");
    eleResponseCont.id = "responseCommentCont";

//console.log(allComments);
    var responseOBJ = null; 
    var responseCnt = -1;
    for(var cObj of allComments){
//console.log(cObj.pid + " " +pId);
        if(cObj.pid == pId){
            responseOBJ = cObj.jsonResponse;
            responseCnt = cObj.responseCnt;
            break;
        }
    }

    if(responseOBJ == null) return eleExpandedComment;
//console.log(responseOBJ);
    for(var i = 0; i<responseOBJ.length;i++){
        var responseItem = responseOBJ[i];
        //console.log(responseOBJ["data"][i]);

        var eleResponse = document.createElement("div");
        eleResponse.className = "commentResponse";
        eleResponse.innerHTML = genericImgComment(responseItem[1],
                                                  responseItem[2],
                                                  responseItem[3]);
        eleResponseCont.appendChild(eleResponse);
    }
    eleExpandedComment.insertBefore(eleResponseCont,eleExpandedComment.firstChild);

    return eleExpandedComment;
}

function createTextArea(pId){
    var eleResponseBox = document.createElement("div");
    eleResponseBox.className = "newCommentCont";
    eleResponseBox.id = "newCommentResponse";
    var textBox= "<div class=usrCommentTitle>Comment</div>"+
                 "<div class=usrTextConstraint><textarea class=usrCommentTextArea "+
                 "id=usrResponseTextArea rows=3></textarea><br></div>";
    eleResponseBox.innerHTML = textBox;

    var eleButtonContainer = document.createElement("div");
    eleButtonContainer.className = "usrCommentSubmitCont";

    var eleButton = document.createElement("button");
    eleButton.innerText="Post";
    eleButton.type="submit";
    eleButton.id = "usrResponseSubmit";
    eleButton.addEventListener("click",function(){
        var responseText = document.getElementById("usrResponseTextArea");
        postResponseComment(pId,responseText.value);
    });

    //temporary solution. using eleButton does not work for
    //some reason
    eleButtonContainer.appendChild(eleButton);

    eleResponseBox.appendChild(eleButtonContainer);
    return eleResponseBox;
}

function postResponseComment(pId,responseComment){
    //can i use one xhttp instead of so many?
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
//console.log("NEW POST RESPONSE\n\n"+this.responseText);
            var jsonObj = JSON.parse(this.responseText);
            if(jsonObj["returnCode"] == 1){
                //have to do a linear search. cannot do a refernece
                for(var cObj of allComments){
                    if(pId == cObj.pid){
                        cObj.responseCnt = jsonObj["responseCnt"];
                        cObj.jsonResponse = jsonObj["data"];
                        break;
                    }
                }
                //need to update the things
                refreshResponseComments(pId);

                var responseCntEle = document.getElementById("p_"+pId+"_responseCnt");
                if(responseCntEle != null){
                    responseCntEle.innerText = "Comment(s): "+jsonObj["responseCnt"];
                }
            }
            var eleComments = document.getElementById("otherCommentCont");
            showCommentText(eleComments,jsonObj["responseStr"]);
        }
    }
    xhttp.open("POST", "readerPhp/readerCommentResponse.php");
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("board="+boardName+"&tid="+threadID+"&pId="+pId+
               "&responseComment="+responseComment);
}

function deleteAllChildren(ele){
    while(ele.firstChild){
        ele.removeChild(ele.firstChild);
    }
}
function highlightRect(ctx,pos_x,pos_y,rectAr){
    var canvasHighlight= document.getElementById("highlightCanvas");
    clearCanvas(ctx);
    for(var cObj of rectAr){
        if(cObj.isInRectangle(pos_x,pos_y)){
            drawRect(ctx,cObj.sx,cObj.sy,cObj.ex,cObj.ey,colorRead);
        }
    }
}

function postComment(sx,sy,ex,ey,cBoard,cThread){
    //console.log("POST "+cBoard + " " + cThread);
    var commentSec = document.getElementById("usrCommentText");
    var comment= commentSec.value;
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200 && this.responseText != "EMPTY"){
            //can do update or do a reload page here
            //document.getElementById("usrCommentText").value= this.responseText;
            //console.log("COMMENT: "+this.responseText);
            commentSec.value = "";

            var eleComments = document.getElementById("otherCommentCont");
            showCommentText(eleComments,this.responseText);
        }
    }
    sx = sx/imgWidth; sy = sy/imgHeight;
    ex = ex/imgWidth; ey = ey/imgHeight;
    xhttp.open("POST", "readerPhp/readerAddCommentAjax.php",false);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("board="+cBoard+"&TID="+cThread+
                "&sx="+sx+"&sy="+sy+"&ex="+ex+"&ey="+ey+"&comment="+comment);

}
function toggleAnote(){
    var lowerEle = document.getElementById("lowerCanvas");
    var highlightEle = document.getElementById("highlightCanvas");

    if(lowerEle.style.display=="none"){
        lowerEle.style.display = "block";
        highlightEle.style.display = "block";
    } else{
        lowerEle.style.display = "none";
        highlightEle.style.display = "none";
    }
}

function captchaPopUp(){
    //saved for later
    //what i am thinking if it gets bad is to use an invisible captcha
    //system so instead of not allowing the bot to post, we let it post
    //but it will talk a long time. and we will track the ip of the bot
    //and remember it so that cookieclearing doesn't work
}

function getCommentWpid(pid){
    for(var cObj of allComments){
        if(pId == cObj.pid){
            return cObj;
        }
    }
}
