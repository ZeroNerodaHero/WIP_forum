//typeCode:
//0 - login page
function updateBody(typeCode){
    var adminBody = document.getElementById('adminBody');


    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200){
            adminBody.innerHTML = this.responseText; 
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

    console.log(typeCode);
    var selectedEle = document.getElementById("selectedBody");
    if(typeCode == 1) postingNews(selectedEle);
    else if(typeCode == 2) createBoard(selectedEle);
    else if(typeCode == 3) renderDelete(typeCode);
    else if(typeCode == 4) listBans(typeCode,selectedEle);
    else if(typeCode == 5) listBadWords(typeCode,selectedEle);
    else if(typeCode == 6) advertManager(typeCode,selectedEle);
    else if(typeCode == 7) updateMysql(selectedEle);
}
function expandAdminContent(){
    var adminEle = document.getElementById("adminContent");
    adminEle.style.gridTemplateColumns="20% 80%";
}

function postingNews(ele) {
console.log("news"); 
    ele.innerHTML = 
        '<h1>Ur Posting News</h1>'+
        '<form action="updateNews.php" method="post">'+
            'Title: <input type="text" name="title" class="tit"> <br>'+
            'Message: <br>'+
                '<textarea name="content" rows="6" cols="100" ></textarea>'+
            '<br>'+
            'Password: <input type="text" name="password">'+
            '<input type="submit" value="Post">'+
        '</form>';
}

/*
 * end of posting news js code
*/

function createBoard(ele) {
console.log("boards"); 
    ele.innerHTML = 
        '<h1>Create Board</h1><ul><li> Don\'t add / to the thing. No need. </li>'+
        '<li> Use the pinned post as a way to post rules and stuff. yea </li></ul><br>'+

        '<form action="updateBoard.php" method="post">'+
        'Board Name: <input type="text" name="boardName" class="tit"><br>'+
        'Description: <br>'+
        '<textarea name="descript" rows="2" cols="100" ></textarea><br>'+
        'Post Title: <input type="text" name="pinTit" class="tit"> <br>'+
        'Pinned Post: <br>'+
        '<textarea name="pinned" rows="6" cols="100" ></textarea><br>'+
        'Password: <input type="text" name="password">'+
        '<input type="submit" value="Post"></form><br>';
}
/*
 * creat stuff
*/
function renderDelete(typeCode,board=null,threadId=null,isAnote=0) {
console.log("dlete stuff"); 
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
/*
/*
 * Deletes stuff
 */
function listBans(ele) {
console.log("list bans"); 
}
function listBadWords(ele) {
console.log("badowrds"); 
}
function advertManager(ele) {
console.log("Adverts"); 
}
function updateMysql(ele) {
console.log("updateMysql"); 
}


