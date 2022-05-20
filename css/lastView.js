/*
 * returns a string
 */
function getAllLVJson(){
    var tmp = window.localStorage.getItem("LastViewRecorder");
    if(tmp == null) return "{}";
    return tmp;
}
/*
 * returns a json object
 */
function getBoardJson(){
    var tmp = getAllLVJson();
    var jObj = JSON.parse(tmp);
    return jObj;
}

function setBoardJson(obj){
    window.localStorage.setItem("LastViewRecorder",JSON.stringify(obj));
}

function iterateCnter(board){
    var recorder = getBoardJson();

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
        console.log(typeof(TID)+" "+typeof(TID.toString()));
        if(recorder[board] && recorder[board][TID.toString()]){
            oPostCnt = recorder[board][TID.toString()][0]; 
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
    }
}

function updateLastThread(board,TID,pCnt){
    var boardData = getBoardJson();
    console.log(boardData);
    console.log(typeof(boardData));

    if(!boardData.hasOwnProperty(board)){
        boardData[board] = {};
    }
    console.log(boardData);
    console.log(board+" "+TID+" "+pCnt);
    boardData[board][TID.toString()] = [pCnt];
    console.log("LST "+boardData);
    setBoardJson(boardData);
}

//exampleJSON
/*  [
 *      {'TID': 14,'postCnt': 10 , 'isWatched': false}
 *      {'TID': 12,'postCnt': 10 , 'isWatched': false}
 *  ]
 *  { "example":[[10,4],[9,6]]}
 *  translate: boardName example has thread 10 with a last view of 4
 *  and has a thread 9 with a last view of 4
 */
