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

//system matically generate all img.
//post in succession
class commentRect{
    constructor(pid,uid,sx,sy,ex,ey,strComment,time=0){
        if(sx > ex){
            sx += ex;
            ex = sx - ex;
            sx -= ex;
        }
        if(sy > ey){
            sy += ey;
            ey = sy - ey;
            sy -= ey;
        }

        this.pid =pid;
        this.uid =uid;
        this.sx=sx;
        this.sy=sy;
        this.ex=ex;
        this.ey=ey;
        this.strComment = strComment;
        this.time = time;
    }
    isInRectangle(x,y){
        return (this.sx <= x && this.ex >= x && 
                this.sy <= y && this.ey >= y);
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

    const allComments = [];
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
            genComments(botCtx,allComments,board,threadId);
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
            genComments(botCtx,allComments,board,threadId);
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
    //console.log("READER GET " + cBoard + "_"+cThread);
    //console.log("readerPhp/readerGetComments.php?board="+cBoard+"&TID="+cThread);
    xhttp.open("GET", "readerPhp/readerGetComments.php?board="+cBoard+"&TID="+cThread,false);
    xhttp.send();

    var xmlDoc=xhttp.responseXML.getElementsByTagName("encap");
    for(var i = 0; i < xmlDoc.length; i++){
        var cSx = parseFloat(xmlDoc[i].getElementsByTagName("sx")[0].childNodes[0].nodeValue);
        cSx = Math.floor(cSx*imgWidth);
        var cSy = parseFloat(xmlDoc[i].getElementsByTagName("sy")[0].childNodes[0].nodeValue);
        cSy = Math.floor(cSy*imgHeight);
        var cEx = parseFloat(xmlDoc[i].getElementsByTagName("ex")[0].childNodes[0].nodeValue);
        cEx = Math.floor(cEx*imgWidth);
        var cEy = parseFloat(xmlDoc[i].getElementsByTagName("ey")[0].childNodes[0].nodeValue);
        cEy = Math.floor(cEy*imgHeight);
        rectAr.push(new commentRect(
            xmlDoc[i].getElementsByTagName("postId")[0].childNodes[0].nodeValue,
            xmlDoc[i].getElementsByTagName("userID")[0].childNodes[0].nodeValue,
            cSx,cSy,cEx,cEy,
            xmlDoc[i].getElementsByTagName("comment")[0].childNodes[0].nodeValue,
            xmlDoc[i].getElementsByTagName("time")[0].childNodes[0].nodeValue)
        );
    }

    for(var cObj of rectAr){
        drawRect(ctx,cObj.sx,cObj.sy,cObj.ex,cObj.ey,colorRead);
    }
}

function showComments(pos_x,pos_y,rectAr){
    var eleComments = document.getElementById("otherCommentCont");
    deleteAllChildren(eleComments);
    for(var cObj of rectAr){
        if(cObj.isInRectangle(pos_x,pos_y)){
            showCommentText(eleComments,cObj.strComment,1,cObj.pid,cObj.uid,cObj.time);
        }
    }
}
function showCommentText(eleComments,strComment,type=0,pId="",uId="",cTime="",color=""){
    var tmp = document.createElement("div");
    tmp.className = "imgComment";
    tmp.id= "iC_"+pId;
    var innerTXT= "<p class=contentCommentBox>"+strComment+"</p>";
    if(type==1){
        innerTXT = genericImgComment(uId,cTime,strComment);
    } else{
        tmp.style.backgroundColor="red";
    }
    tmp.innerHTML = "<div class=commentCont>"+innerTXT+"</div>";

    tmp.addEventListener('click', function(){
        //theoretically only 1 should exist if there is no expansion
        if(document.getElementById("iC_"+pId).childElementCount == 1){
            refreshResponseComments(pId);
        }
    });

    if(color!="")tmp.style.backgroundColor=color;
    eleComments.appendChild(tmp);
}
function genericImgComment(uId,cTime,strComment){
    var innerTXT= "<div class=contentCommentBox><br>"+strComment+"</div>";
    innerTXT = "<div class=commentInfoCont>"+
            "<span class=idCommentBox>"+uId+"&nbsp&nbsp&nbsp&nbsp</span>"+
            "<span class=timeCommentBox>"+cTime+"</span>"+
            "</div>"+innerTXT;
    return innerTXT;
}

function refreshResponseComments(pId){
    var commentEle = document.getElementById("iC_"+pId);
    var commentExpanded = document.getElementById("commentExpanded");
    if(commentExpanded != null){
        commentExpanded.remove();
        console.log("removed");
    }

    commentExpanded = expandResponseComments(pId);
    commentEle.appendChild(commentExpanded);
    var commentResponseTextarea = createTextArea();
    commentExpanded.appendChild(commentResponseTextarea);
}

function expandResponseComments(pId){
    var eleExpandedComment = document.createElement("div");
    eleExpandedComment.id = "commentExpanded";

    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            //jsonify
            /*
            var commentResponse = document.createElement("div");
            commentResponse.className = "commentResponse";
            commentResponse.innerHTML = genericImgComment("user","14","test");
            eleExpandedComment.appendChild(commentResponse);
            */
            eleExpandedComment.innerHTML = "<div class=commentResponse>"+
                                           genericImgComment("user","14","test")+
                                           "</div>"+
                                           eleExpandedComment.innerHTML;
        }
    };
    var selector = boardName+"_"+threadID;
    xhttp.open("GET", "readerPhp/readerGetCommentResponse.php?selector="+selector+"&pId="+pId);
    xhttp.send();
    return eleExpandedComment;
}

function createTextArea(){
    var eleResponseBox = document.createElement("div");
    eleResponseBox.className = "newCommentCont";
    eleResponseBox.id = "newCommentResponse";
    var textBox= "<div class=usrCommentTitle>Comment</div>"+
                 "<div class=usrTextConstraint><textarea class=usrCommentTextArea "+
                 "id=usrCommentResponseText rows=3></textarea><br></div>";
    eleResponseBox.innerHTML = textBox;

    var eleButtonContainer = document.createElement("div");
    eleButtonContainer.className = "usrCommentSubmitCont";
    var eleButton = document.createElement("button");
    eleButton.innerText="Post";

    eleResponseBox.appendChild(eleButton);
    return eleResponseBox;
}

function postResponseComment(pId,responseComment){
    //can i use one xhttp instead of so many?
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            expandResponseComments(pId);
        }
    }
    xhttp.open("POST", "readerPhp/readerGetCommentResponse.php?selector="+selector+"&pId="+pId);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("newResponse="+responseComment);
                
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
