//typeCode:
//0 - login page
function updateBody(typeCode){
    var adminBody = document.getElementById('adminBody');
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200){
            adminBody.innerHTML = this.responseText; 
            updateLog();
        }
    }

    if(typeCode == 0){
        var textEle = document.getElementById("passwd");
        xhttp.open("POST", "backgroundAdmin.php");
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("typeCode="+typeCode+"&passwd="+passwd.value);
    } 

    adminBody.innerHTML = 'Loading...';
    console.log('currently on loginPage');
}

function showSelected(typeCode){
    expandAdminContent();

    var selectedEle = document.getElementById("selectedBody");
    if(typeCode == 1) postingNews(selectedEle);
    else if(typeCode == 2) createBoard(selectedEle);
    else if(typeCode == 3) renderDelete(typeCode);
    else if(typeCode == 4) generatePage(typeCode,selectedEle);
    else if(typeCode == 5) generatePage(typeCode,selectedEle);
    else if(typeCode == 6) generatePage(typeCode,selectedEle);
    else if(typeCode == 7) updateMysql(selectedEle);
    updateLog();
}
function expandAdminContent(){
    var adminEle = document.getElementById("adminContent");
    adminEle.style.gridTemplateColumns="20% 80%";
}

function postingNews(ele) {
    ele.innerHTML = 
        '<h1>Ur Posting News</h1>'+
        'Title: <input id="newsTitle" class="tit"> <br>'+
        'Message: <br>'+
            '<textarea id="newsContent" rows="6" cols="100" ></textarea>'+
        '<br>'+
        '<button onclick=postNews()>Post News</button>';
}

function postNews(){
    var titleEle = document.getElementById("newsTitle");
    var contentEle= document.getElementById("newsContent");
    var title = titleEle.value;
    var content= contentEle.value;

    content = encodeURIComponent(content);

    var postData = "typeCode=1&title="+title+"&content="+content;
    connectWServer(postData,function(){
        titleEle.value = contentEle.value = "";
    });
}

/*
 * end of posting news js code
*/

function createBoard(ele) {
    ele.innerHTML = 
        '<h1>Create Board</h1><ul><li> Don\'t add / to the thing. No need. </li>'+
        '<li> Use the pinned post as a way to post rules and stuff. yea </li></ul><br>'+

        'Board Name: <input id="boardName" class="tit"><br>'+
        'Description: <br>'+
        '<textarea id="descript" rows="2" cols="100" ></textarea><br>'+
        'Post Title: <input id="pinTit" class="tit"> <br>'+
        'Pinned Post: <br>'+
        '<textarea id="pinned" rows="6" cols="100" ></textarea><br>'+
        '<button onclick=addBoard()>Create Board</button>';
}
function addBoard(){
    var boardNameEle = document.getElementById("boardName");
    var descriptEle = document.getElementById("descript");
    var titleEle = document.getElementById("pinTit");
    var pinnedContentEle = document.getElementById("pinned");

    var boardName = boardNameEle.value;
    var descript = descriptEle.value;
    var title = titleEle.value;
    var pinnedContent = pinnedContentEle.value;

    var postData = "typeCode=2&board="+boardName+"&descript="+descript+
                "&title="+title+"&pinnedContent="+pinnedContent;
    connectWServer(postData,function(){
        boardNameEle.value = descriptEle.value = "";
        titleEle.value = pinnedContentEle.value = ""; 
    });
}
/*
 * creat stuff
*/
function renderDelete(typeCode,board=null,threadId=null,isAnote=0) {
    var ele = document.getElementById("selectedBody");
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200){
            //console.log(this.responseText);
            ele.innerHTML = this.responseText; 
        }
    }
    var POSTdata = "typeCode="+typeCode+"&pageType=";
    if(board == null && threadId == null) POSTdata += '0';
    else if(board != null && threadId == null) POSTdata += '1&board='+board;
    else if(threadId != null && threadId != null) 
        POSTdata += '2&board='+board+'&threadId='+threadId+"&isAnote="+isAnote;
//console.log(POSTdata);

    xhttp.open("POST", "backgroundAdmin.php");
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(POSTdata);

    ele.innerText = "Loading";
}
function deleteStuff(board,threadId,isAnote=null,postId=null,responsePID=null){
    var POSTdata = "typeCode=8&board="+board+"&tId="+threadId;
    //deleting response
    if(isAnote == true && postId != null && responsePID != null)
        POSTdata += "&opt=4&isAnote=1&pId="+postId+"&rpId="+responsePID;
    //deleting post from anote thread
    else if(isAnote == true && postId != null)
        POSTdata += "&opt=3&isAnote=1&pId="+postId;
    //deleting post from normal thread
    else if(postId != null)
        POSTdata += "&opt=2&pId="+postId;
    else if(isAnote == true)
        POSTdata += "&opt=1&isAnote=1";
    else
        POSTdata += "&opt=0";

//console.log(POSTdata);
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200){
//console.log(this.responseText);
            //if null then just null
            if(postId != null){
                renderDelete(3,board,threadId,isAnote);
            } else{
                renderDelete(3,board);
            }
        }
    }
    xhttp.open("POST", "backgroundAdmin.php");
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(POSTdata);
}
function uhOhBan(board,tId,pId,isAnote=0,rId=null){
    var selector = "banReason_"+pId;
    if(rId != null) selector += "_"+rId;
    var reasonEle = document.getElementById(selector);
    var reason = reasonEle.value;

    var postData = "typeCode=9&reason="+reason+
                    "&board="+board+"&tId="+tId+"&pId="+pId+"&isAnote="+isAnote;
    if(rId != null) postData += "&rId="+rId;
    
    connectWServer(postData,function(){
        reasonEle.value = "";
    },1);
}
function unBanUsr(usrId){
    updatePage(4,"typeCode=10&usrId="+usrId);
}
/*
/*
 * Deletes stuff
 */
function generatePage(typeCode,ele) {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200){
            ele.innerHTML = this.responseText;
        }
    }
    xhttp.open("POST", "backgroundAdmin.php");
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("typeCode="+typeCode);
}
function updatePage(typeCode,postData){
    connectWServer(postData,function(){
        showSelected(typeCode);
    });
}
function connectWServer(postData,runFunc=null,setEle=null,debug=0){
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200){
            //if(runFunc != null) runFunc();
            if(runFunc != null) runFunc(this.responseText);
            if(setEle != null) setEle = this.responseText;
            if(debug) console.log(this.responseText);
        }
    }
    xhttp.open("POST", "backgroundAdmin.php");
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(postData);
}
/*
 * banned words
 */
function deleteWord(word){
    updatePage(5,"typeCode=11&word="+word);
}
function addWord(){
    var word = document.getElementById("newBannedWord").value;
    if(word != "") updatePage(5,"typeCode=12&word="+word);
}
//////////////////////////////////
function updateAdvert(id,opt){
    var postData = "typeCode=13&"; 
    var newValue="";
    var oldValue="";
    if(opt== 0){
        newValue = document.getElementById("chngImgLnk_"+id).value;
        oldValue = document.getElementById("adlinkImg_"+id).innerText;
    } else if(opt== 1){
        newValue = document.getElementById("chngSiteLnk_"+id).value;
        oldValue = document.getElementById("adlinkSite_"+id).innerText;
    } else if(opt==2){
        newValue = document.getElementById("chngMaxPoint_"+id).value;
        oldValue = document.getElementById("maxPoint_"+id).innerText;
    }
    if(newValue != ""){
        newValue= encodeURIComponent(newValue);
        oldValue= encodeURIComponent(oldValue);
        postData += "id="+id+"&opt="+opt+"&value="+newValue+"&oldValue="+oldValue;
        if(postData != null) updatePage(6,postData);
    }
}
//////////////////////////////////
function updateMysql(ele) {
console.log("updateMysql"); 
}
//////////////////////////////////
function updateLog() {
    var logEle = document.getElementById("adminLog");
    //in all honesty i don't even know why passing a parameter works like
    //this. other functions don't have a parameter, will this cause a problem?
    var logVal = connectWServer("typeCode=99",function(logStr){
        logEle.innerHTML = "<pre>"+logStr+"</pre>";
        logEle.scrollTop = logEle.scrollHeight;
    });
}
/////////////////////////////////
function addAdvert(){
    var imgLnk = document.getElementById("imageLnk");
    var siteLnk= document.getElementById("siteLnk");
    var points = document.getElementById("points");
    var boardsLimited= document.getElementById("boardsLimited");

    if(imgLnk && siteLnk && points && boardsLimited &&
       imgLnk.value!="" && siteLnk.value!="" && points.value!=""){
        var postData = "typeCode=14&imgLnk="+encodeURIComponent(imgLnk.value)+
                        "&siteLnk="+encodeURIComponent(siteLnk.value)+
                        "&points="+points.value+"&boardsLimited="+boardsLimited.value;
        if(postData != null) updatePage(6,postData);
        console.log(postData);
    }

}
