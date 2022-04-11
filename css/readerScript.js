var imgWidth = 0, imgHeight = 0, imgLeft = 0, imgTop= 0;
var minSize = 4;
var itCount = 0;

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
function imgRenderInit(board,threadId){
    //for resizes
    window.addEventListener('resize', function(){
        if(imgWidth != imgLayer.offsetWidth || imgHeight !=imgLayer.offsetHeight ){
            console.log("resize "+imgLayer.offsetWidth + " " +imgLayer.offsetHeight );
            console.log("resize "+(++itCount));
            imgRenderInit(board,threadId);

        }
    });


    var imgLayer = document.getElementById("imgContLayer");
    imgWidth = imgLayer.offsetWidth, 
    imgHeight = imgLayer.offsetHeight,
    imgLeft = (imgLayer.offsetLeft-imgLayer.style.marginLeft),
    imgTop= (imgLayer.offsetTop-imgLayer.style.marginTop);

    const allComments = [];

    var usrCanvas= document.getElementById("upperCanvas");
    var usrCtx = usrCanvas.getContext("2d");
    var botCanvas = document.getElementById("lowerCanvas");
    var botCtx= botCanvas.getContext("2d");
    var highlightCanvas = document.getElementById("highlightCanvas");
    var highlightCtx= highlightCanvas.getContext("2d");
    usrCanvas.style.left = botCanvas.style.left = highlightCanvas.style.left = imgLeft+"px";
    usrCanvas.style.top= botCanvas.style.top= highlightCanvas.style.top = imgTop+"px";
    usrCanvas.width = botCanvas.width = highlightCanvas.width = imgWidth;
    usrCanvas.height = botCanvas.height = highlightCanvas.height = imgHeight;

    var usrCommentEle= document.getElementById("usrCommentCont");
    genComments(botCtx,allComments,board,threadId);

    var isDown = false;
    var sx=0,sy=0,ex=0,ey=0;

    //add command to tell initial mousedown position
    //on mouse up see difference
    //create rectangle
    usrCanvas.addEventListener("mousedown",function(){
        isDown = true;
        sx = event.offsetX;
        sy = event.offsetY;
        showComments(event.offsetX,event.offsetY,allComments);
    });
    usrCanvas.addEventListener("mousemove",function(){
        if(isDown){
            ex = event.offsetX;
            ey = event.offsetY;
            drawRect(usrCtx,sx,sy,event.offsetX,event.offsetY);
        } else{
            highlightRect(highlightCtx,event.offsetX,event.offsetY,allComments);
        }
    });
    usrCanvas.addEventListener("mouseup",function(){
        ex = event.offsetX;
        ey = event.offsetY;
        
        if(isDown){
            if(Math.abs((ex-sx)*(ey-sy)) <= minSize){
                //console.log(Math.abs((event.offsetX-sx)*(event.offsetY-sy)));
                //console.log(event.offsetX+' '+sx+' '+event.offsetY+' '+sy);
                //console.log(sx+','+sy+"-->"+ex+","+ey);
                usrCommentEle.style.display="none";
            } else{
                usrCommentEle.style.display="block";
            }

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
}

/*
 * draws a rect given
 * start x,y
 * end x,y
 * max x,y
 * no position have to be set, function auto 
 */
function drawRect(usrCtx,sx,sy,ex,ey,runclear=1,color="#123456a5",debug=0){
    if(runclear){
        clearCanvas(usrCtx);
    }
    usrCtx.strokeStyle = 'black';
    usrCtx.lineWidth   = 2;
    usrCtx.fillStyle=color;
//debug
if(debug){
    usrCtx.fillRect(sx-2,sy-2,4,4);
    usrCtx.fillRect(ex-2,ey-2,4,4);
}


    usrCtx.fillRect(sx,sy,ex-sx,ey-sy);
    usrCtx.strokeRect(sx,sy,ex-sx,ey-sy);
}

function clearCanvas(ctx){
    ctx.clearRect(0,0,imgWidth, imgHeight);
}

function genComments(ctx,rectAr,cBoard,cThread){
    rectAr.length=0;
    const xhttp = new XMLHttpRequest();
    console.log("READER GET " + cBoard + "_"+cThread);
    console.log("readerPhp/readerGetComments.php?board="+cBoard+"&TID="+cThread);
    xhttp.open("GET", "readerPhp/readerGetComments.php?board="+cBoard+"&TID="+cThread,false);
    xhttp.send();

    var xmlDoc=xhttp.responseXML.getElementsByTagName("encap");
    for(var i = 0; i < xmlDoc.length; i++){
        var cSx = parseFloat(xmlDoc[i].getElementsByTagName("sx")[0].childNodes[0].nodeValue);
        cSx*= imgWidth;
        var cSy = parseFloat(xmlDoc[i].getElementsByTagName("sy")[0].childNodes[0].nodeValue);
        cSy*= imgHeight;
        var cEx = parseFloat(xmlDoc[i].getElementsByTagName("ex")[0].childNodes[0].nodeValue);
        cEx*= imgWidth;
        var cEy = parseFloat(xmlDoc[i].getElementsByTagName("ey")[0].childNodes[0].nodeValue);
        cEy*= imgHeight;
        rectAr.push(new commentRect(
            xmlDoc[i].getElementsByTagName("postId")[0].childNodes[0].nodeValue,
            xmlDoc[i].getElementsByTagName("userID")[0].childNodes[0].nodeValue,
            cSx,cSy,cEx,cEy,
            xmlDoc[i].getElementsByTagName("comment")[0].childNodes[0].nodeValue,
            xmlDoc[i].getElementsByTagName("time")[0].childNodes[0].nodeValue)
        );
    }

    for(var cObj of rectAr){
        drawRect(ctx,cObj.sx,cObj.sy,cObj.ex,cObj.ey,0);
    }
}

function showComments(pos_x,pos_y,rectAr){
    var eleComments = document.getElementById("otherCommentCont");
    deleteAllChildren(eleComments);
    for(var cObj of rectAr){
        if(cObj.isInRectangle(pos_x,pos_y)){
            var tmp = document.createElement("div");
            tmp.className = "imgComment";
            tmp.innerHTML = cObj.strComment;
            eleComments.appendChild(tmp);
        }
    }
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
            drawRect(ctx,cObj.sx,cObj.sy,cObj.ex,cObj.ey,0);
        }
    }
}

function postComment(sx,sy,ex,ey,cBoard,cThread){
    console.log("POST "+cBoard + " " + cThread);
    var commentSec = document.getElementById("usrCommentText");
    var comment= commentSec.value;
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        //can do update or do a reload page here
        //document.getElementById("usrCommentText").value= this.responseText;
        console.log("COMMENT: "+this.responseText);
        commentSec.value = "";
    }
    sx = sx/imgWidth; sy = sy/imgHeight;
    ex = ex/imgWidth; ey = ey/imgHeight;
    xhttp.open("POST", "readerPhp/readerAddCommentAjax.php",false);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("board="+cBoard+"&TID="+cThread+
                "&sx="+sx+"&sy="+sy+"&ex="+ex+"&ey="+ey+"&comment="+comment);
}
