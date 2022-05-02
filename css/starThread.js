/* json format:
 * board_threadId: lastTotalPost, currentTotalPost
 *
 */
function getWatchThreads(){
    var tmp = window.localStorage.getItem("watchThreads");
    if(tmp == null) return "{}";
    return tmp;
}

function setWatchJson(nstr){
    window.localStorage.setItem("watchThreads",nstr);
}

function setWatchThread(board,TID,pCnt){
    var watchCol = JSON.parse(getWatchThreads());
    
    if(!watchCol.hasOwnProperty(board)){
        watchCol[board] = {}; 
    }
    watchCol[board][TID.toString()] = [pCnt,pCnt];
    setWatchJson(JSON.stringify(watchCol));

    var ele = document.getElementById("threadStarButton")
    ele.text= "\u2605";
    ele.href="javascript:unWatchThread('"+board+"',"+TID+","+pCnt+")";
}

function unWatchThread(board,TID,pCnt){
    var watchCol = JSON.parse(getWatchThreads());

    if(watchCol.hasOwnProperty(board) && watchCol[board].hasOwnProperty(TID)){
        delete watchCol[board][TID];
    }
    setWatchJson(JSON.stringify(watchCol));

    var ele = document.getElementById("threadStarButton")
    ele.text= "\u2606";
    ele.href="javascript:setWatchThread('"+board+"',"+TID+","+pCnt+")";
}

function watchGetter(board,TID,pCnt){
    var watchStr = getWatchThreads();
    var xhttp = new XMLHttpRequest();

    //really bad way to tell if board exists, but we can use it to update
    //no cost i guess
//console.time('global');
    xhttp.open("POST", "../php/watchedThreadResponse/watchedThreadResponse.php",false);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("json="+watchStr);
    setWatchJson(xhttp.responseText);
//console.timeEnd('global');
}

function watchingCurrent(board,TID){
    var watchCol = JSON.parse(getWatchThreads());
    return watchCol.hasOwnProperty(board) && watchCol[board].hasOwnProperty(TID);
}

function watchMaster(board,TID,pCnt){
    watchGetter();

    var ele = document.getElementById("threadStarButton")
    if(watchingCurrent(board,TID)){
        ele.href="javascript:unWatchThread('"+board+"',"+TID+","+pCnt+")";
    } else{
        ele.href="javascript:setWatchThread('"+board+"',"+TID+","+pCnt+")";
    }
    //generate the bar stuff
}
