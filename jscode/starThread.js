/* json format:
 * board_threadId: lastTotalPost, currentTotalPost
 *
 */
function getStarThreads(){
    var tmp = window.localStorage.getItem("starThreads");
    if(tmp == null) return "{}";
    return tmp;
}

function setStarJson(nstr){
    window.localStorage.setItem("starThreads",nstr);
}

function setStarThread(board,TID,pCnt){
    var starCol = JSON.parse(getStarThreads());
    
    if(!starCol.hasOwnProperty(board)){
        starCol[board] = {}; 
    }
    starCol[board][TID.toString()] = [pCnt,pCnt];
    setStarJson(JSON.stringify(starCol));

    var ele = document.getElementById("threadStarButton")
    if(ele != null){
        //this is for the post success page that does the shit
        ele.text= "\u2605";
        ele.href="javascript:unStarThread('"+board+"',"+TID+","+pCnt+")";
        starReload(0);
    }
}

function unStarThread(board,TID,pCnt){
    var starCol = JSON.parse(getStarThreads());

    if(starCol.hasOwnProperty(board) && starCol[board].hasOwnProperty(TID)){
        delete starCol[board][TID];
        if(Object.keys(starCol[board]).length==0) delete starCol[board];
    }
    setStarJson(JSON.stringify(starCol));

    var ele = document.getElementById("threadStarButton")
    ele.text= "\u2606";
    ele.href="javascript:setStarThread('"+board+"',"+TID+","+pCnt+")";
    starReload(0);
}

//here is the problem, i need to do a json update and a json render
//u cannot do this in one go
function starGetter(board,TID,pCnt){
    var starStr = getStarThreads();
    if(starStr == "{}") return;


    //really bad way to tell if board exists, but we can use it to update
    //no cost i guess
//console.time('global');
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "../php/watchedThreadResponse/starredThreadResponse.php",false);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("json="+starStr);
    setStarJson(xhttp.responseText);
//console.timeEnd('global');
}

function staringCurrent(board,TID){
    var starCol = JSON.parse(getStarThreads());
    return starCol.hasOwnProperty(board) && starCol[board].hasOwnProperty(TID);
}

function starReload(settingChange){
    //essentially stargetter updates the json value to correct thing
    //and xhttp makes the correct reference links
    starGetter();

    //generate the bar stuff
    var starStr = getStarThreads();
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "../php/watchedThreadResponse/starredThreadRender.php",false);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("json="+starStr);
    document.getElementById("recLinkCont").innerHTML= xhttp.responseText;
    recentAnimateAll();

    if(settingChange){
        var fullSetting = getSettings("settings");
        fullSetting.setting = (fullSetting.setting&(~(1<<6)));
        var ot = fullSetting.setting&(1<<6);
        setCookie("settings", JSON.stringify(fullSetting));
    }
}

function starMaster(board,TID,pCnt){
    var starStr = getStarThreads();
    var starCol = JSON.parse(starStr);

    var ele = document.getElementById("threadStarButton");
    //extra json parse? remove or no
    if(ele){
        if(staringCurrent(board,TID)){
            ele.text= "\u2605";
            ele.href="javascript:unStarThread('"+board+"',"+TID+","+pCnt+")";
            starCol[board][TID][0] = pCnt;
        } else{
            ele.text= "\u2606";
            ele.href="javascript:setStarThread('"+board+"',"+TID+","+pCnt+")";
        }
    }
    starStr = JSON.stringify(starCol);
    setStarJson(starStr);

    //we can update this first because it really doesn't matter
    //0 is being set here
    //1 is set in starReload
    starReload(0);
}

function recentReload(board){
    var xhttp = new XMLHttpRequest();
    xhttp.open("GET", "../php/watchedThreadResponse/recentThreadResponse.php?board="
                +board,false);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
    document.getElementById("recLinkCont").innerHTML= xhttp.responseText;

    var fullSetting = getSettings("settings");
    fullSetting.setting = (fullSetting.setting|(1<<6));
    var ot = fullSetting.setting&(1<<6);
    setCookie("settings", JSON.stringify(fullSetting));
    recentAnimateAll();
}

function watchMaster(board,TID,pCnt){
    var fullSetting = getSettings("settings");
    var ot = fullSetting.setting&(1<<6);
    if(TID == -1){
        if(getStarThreads() == "{}"){
            document.getElementById("watchThread").style.display = "none";
        } else{
            starMaster(board,TID,pCnt);
        }
    } else{
        if(!ot){
            starMaster(board,TID,pCnt);
        } else{
            recentReload(board);
        }
    }
}

function clearStar(board){
    var starCol = JSON.parse(getStarThreads());
    if(starCol.hasOwnProperty(board)){
        delete starCol[board];
        setStarJson(JSON.stringify(starCol));
    }
    starReload(0);
}
