function getBoardJson(board){
    var tmp = window.localStorage.getItem(board+"_lastThread");
    if(tmp == null) return "[]";
    return tmp;
}

function setBoardJson(board,nstr){
    window.localStorage.setItem(board+"_lastThread",nstr);
}

function iterateCnter(board){
    var jsonData = JSON.parse(getBoardJson(board));
    var newjsonData = "[";

    var mData = new Map();
    for(let it of jsonData){
        mData.set(it.TID,Array(it.postCnt,it.isWatched));
    }

    var eles = document.getElementsByClassName("postCnter");

    //update with []
    //for(let it of eles){
    for(var i = 0; i < eles.length; i++){
        var it = eles[i];
        var eleId = it.id;
        var TID = parseInt(eleId.substr(4));
        var nPostCnt = it.textContent;
        var oPostCnt = 0;

        var diff = nPostCnt;
        //calculate the difference or show original amount
        if(mData.has(TID)){
            oPostCnt = mData.get(TID)[0]; 
            diff = nPostCnt-oPostCnt; 
        } 

        var pDiff = document.getElementById("pDiff_"+TID);
        if(diff > 0){
            pDiff.textContent = "[+"+diff+"]";
            pDiff.classList.add("pDiff_pos");
        } else {
            pDiff.textContent = '['+diff+']';
            pDiff.classList.add("pDiff_na");
        }
        
        newjsonData+='{"TID":'+TID+',"postCnt":'+oPostCnt+',"isWatched":false}'
            + ((i == eles.length-1) ? "" : ",");
    }
    newjsonData+=']';
    //console.log(newjsonData);
    setBoardJson(board,newjsonData);
    //window.localStorage.setItem(board+"_lastThread",null);
    //overWrite jsonData
}

function updateLastThread(board,TID,pCnt){
    var strData = getBoardJson(board);
    var cmpTmp = strData;
    if(strData == '[]') return;

    var rep = '"TID":'+TID+',"postCnt":[0-9]{0,9}';
    var nstr= '"TID":'+TID+',"postCnt":'+pCnt;
    strData = strData.replace(new RegExp(rep),nstr);

    if(strData == cmpTmp){
        strData = strData.replace("]",",{"+nstr+"}]");
    }

    setBoardJson(board,strData);
}

//exampleJSON
/*  [
 *      {'TID': 14,'postCnt': 10 , 'isWatched': false}
 *      {'TID': 12,'postCnt': 10 , 'isWatched': false}
 *  ]
 */
