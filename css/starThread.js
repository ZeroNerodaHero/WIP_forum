/* json format:
 * board_threadId: lastTotalPost, currentTotalPost
 *
 */
function getWatchThreads(){
    var tmp = window.localStorage.getItem("watchThreads");
    if(tmp == null) return "[]";
    return tmp;
}

function setWatchJson(nstr){
    window.localStorage.setItem("watchThreads",nstr);
}

function setWatchThreads(board,TID,pCnt){
    var strData = getWatchThreads();
    var cmpTmp = strData;
    var selector = board+'_'+TID;

    var nstr= '{"board":"'+board+'","tid":'+TID+',"lastTotalPost":'+
                pCnt+',"currentTotalPost":'+pCnt+"}";

    if(strData == "[]"){
        strData="["+nstr +']';
    } else{
        if(strData.includes(nstr)) return;

        var rep = '{"board":"'+board+'","tid":"'+TID+
                '","lastTotalPost":[0-9]{0,9},"currentTotalPost":[0-9]{0,9}}';
        strData = strData.replace(new RegExp(rep),nstr);
        if(strData == cmpTmp){
            strData = strData.replace("]",","+nstr+"]");
        }
    }
    setWatchJson(strData);
}

function unWatchThread(board,TID){
    var strData = getWatchThreads();
    var cmpTmp = strData;
    var selector = board+'_'+TID;

    //{0,1} is this right?
    var rep = '{"board":"'+board+'","tid":"'+TID+
            '","lastTotalPost":[0-9]{0,9},"currentTotalPost":[0-9]{0,9}}';
    strData = strData.replace(new RegExp(rep),"");
    setWatchJson(strData);
}

function watchMaster(board,TID,pCnt){
    var watchCol = JSON.parse(getWatchThreads());
    var xhttp = new XMLHttpRequest();


    //really bad way to tell if board exists, but we can use it to update
    //no cost i guess
    console.time('global');
    for(var i = 0; i < watchCol.length; i++){
        var it = watchCol[i];

        var board=it.board;
        var tid=it.tid;
        var lastTP=it.lastTotalPost;
        var currentTP=it.currentTotalPost;

        if(TID == tid){
            console.log("isWatched");
            document.getElementById("threadStarButton").text= "\u2605";
        }
        console.time('Function #1');
        xhttp.open("GET", "../php/watchedThreadResponse/watchedThreadResponse.php?"
                    +"board="+board+"&TID="+tid, false);
        xhttp.send();
        console.log(xhttp.responseText);
        console.timeEnd('Function #1');
    }
        console.timeEnd('global');
}
