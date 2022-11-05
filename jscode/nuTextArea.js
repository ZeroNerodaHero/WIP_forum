class trieNode{
    constructor(nodeFinish = false){
        this.nodeFinish = nodeFinish;
        this.letters= new Map([]);
    }
}

var emoteIsExpanded = false;
var isTextboxChanged = false;
var emoteGenStorage = null;

var emoteTrie = null;
var emoteSearchResult = new Set;

function generateTextArea(){
    var newTextAreaEle = document.createElement("div");
    newTextAreaEle.id = "nuTextEditor";
    newTextAreaEle.innerHTML="<div id=textButtonButtonCont><div id=textButtonCont><span class=textButton onclick=addIMG()> ADD IMG </span><span class=textButton onclick=addLNK()> ADD LNK </span> <span class=textButton onclick=addYTB()> ADD YTB </span> <span class=textButton onclick=addVIDEO()> ADD VIDEO </span> <span class=textButton onclick=showTextEmote() id=emoteExpandButton>EMOTES &#9654;</span></div> </div> <div id=newTextAreaCont> <div contenteditable='true' id=newTextArea></div> </div> <div id=submitCont> <div id=submitButtonCont> <div id=submitButton onclick=submitValue()>Submit </button> </div> </div>";
    newTextAreaEle.addEventListener('keyup',function(event){
        replaceView();
    });
    newTextAreaEle.addEventListener('click',function(event){
    });
    return newTextAreaEle;
}

function addIMG(){
    appendType("[IMG]");
}
function addLNK(){
    appendType("[LNK]",0);
}
function addYTB(){
    appendType("[YTB]");
}
function addVIDEO(){
    appendType("[VIDEO]");
}
function submitValue(){
    var newTextAreaEle = document.getElementById("newTextArea");
    var titEle = document.getElementById("tit");
    if(titEle) titEle.focus();
    newTextAreaEle.focus(); 

    var textVal=extractString(newTextAreaEle);
    textVal = standardizeText(textVal);
    var hiddenTextInput = document.getElementById("hiddenTextInput");
    hiddenTextInput.value = textVal;
    var postForm = document.getElementById("pageForm");
    postForm.submit();
}
function extractString(node){
    if(!node) return "";
    if(node.nodeType == 3){
        return node.textContent;
    }
    if(node.nodeType == 1){
        if(node.nodeName == "IMG"){
            return "\n["+((node.className == "inTextEmote") ? 
                    "EMOTE]("+node.emoteName:"IMG]("+node.src)+")\n";
        }
        if(node.nodeName == "A"){
            if(node.className == "videoLink") return "";
            return "[LNK]("+node.href+")";
        }
        if(node.nodeName == "IFRAME"){
            return "\n[YTB]("+node.src+")\n";
        }
        if(node.nodeName == "VIDEO"){
            return "\n[VIDEO]("+node.currentSrc+")\n";
        }
    }

    var nodeChildren = node.childNodes;
    var retStr="";
    if(nodeChildren.length){
        if(node.nodeName == "DIV") retStr += "\n";
        for(var childNode of nodeChildren){
            retStr += extractString(childNode);
        }
        if(node.nodeName == "DIV") retStr += "\n";
    }
    return retStr;
}
function standardizeText(inputStr){
    if(inputStr=="") return "";
    var i = 0;
    //used to remove inital " " and newlines
    while(i < inputStr.length && (inputStr[i] == " " || inputStr[i] == "\n")){
        i++;
    }
    var outputStr = ""+inputStr[i];
    var outputOffset = 1;
    i++;
    for(; i < inputStr.length; i++){
        if((inputStr[i] == "\n" && outputStr[outputOffset-1] == "\n") ||
           (inputStr[i] == " " && outputStr[outputOffset-1] == "\n") ||
            (inputStr[i] == " " && outputStr[outputOffset-1] == " ")){
                continue;
        }
        outputStr+=inputStr[i];
        outputOffset++;
    }
    return outputStr;
}

function appendType(text,lineBreak=1){
    var newTextArea = document.getElementById("newTextArea");
    newTextArea.focus();

    var srcLnk=document.createElement("span");
    srcLnk.className = "insertLinkArea";
    srcLnk.innerHTML = "*insert link*";
    srcLnk.addEventListener("click",function(){
        this.parentElement.removeChild(this);
    });
    
    var eleType = (lineBreak ? "div" : "span");
    var insertText=document.createElement(eleType);
    insertText.append(" "+text+"(");
    insertText.appendChild(srcLnk);
    insertText.append(") ");

    newTextArea.appendChild(insertText);
}

function replaceView(){
    var node = document.getElementById("newTextArea");
    var oriText = newText = node.innerHTML;

    for(var i = 0; i < oriText.length; i++){
        //if opt-> 0 nothing
        // 1 finding selection type
        // done
        var textLength = oriText.length;
        var start = i,opt = 0;
        var optType = "",optLink="";
        while(i < textLength && opt != 3){
            if(opt ==0 && oriText[i] == "["){
                opt=1;
                start=i;
            }
            else if(opt == 1){
                if(oriText[i] == "]"){
                    i++;
                    if(i < textLength && oriText[i] == "("){
                        opt=2;
                    } else{
                        opt=0;
                        optType = optLink="";
                    }
                }
                else optType += oriText[i];
            } else if(opt == 2){
                if(oriText[i] == ")"){
                    opt = 3;
                } else{
                    optLink += oriText[i];
                }
            }
            i++;
        }
        if(opt == 3){
            if(optLink != "" && optLink[0] != "<"){
                var toInsert = "";
                if(optType == "IMG"){
                    toInsert = "<img src='"+optLink+"'"+
                                " class=newTextArea_img><br>";
                }
                else if(optType == "LNK"){
                    toInsert = "<a href='"+optLink+"'>"+optLink+"</a>";
                }
                else if(optType == "YTB"){
                    var youtubeID="";
                    var j = optLink.length-1;
                    while(j >= 0 && optLink[j] != "/"){
                        var tmpId= "";
                        while(j >= 0 && optLink[j] != "/" && optLink !="&" && optLink[j] != "="){
                            tmpId= optLink[j]+tmpId;
                            j--;
                        }
                        if(optLink[j] == "/"){
                            youtubeID = tmpId;
                            break;
                        } else if(optLink[j] == "="){
                            j--;
                            var GEType="";
                            while(j >= 0 && optLink[j] != "&" && optLink[j] != "?"){
                                GEType = optLink[j]+GEType;
                                j--;
                            }
                            if(GEType == "v"){
                                youtubeID = tmpId;
                                break;
                            } else{
                                j--;
                            }
                        }
                    }
                    toInsert = "<div><iframe width='50%' src='"+
                        "https://www.youtube.com/embed/"+youtubeID+"'"+
                        "</iframe></div>";
                }
                else if(optType == "VIDEO"){
                    var videoType="";
                    var j = optLink.length-1;
                    while(optLink[j] != "."){
                        videoType = optLink[j]+videoType;
                        j--;
                    }
                    if(videoType == "webm" || videoType=="mp4" || videoType=="ogg"){
                        toInsert = "<video width='50%' controls>"+
                            "<source src='"+optLink+"' type='video/"+videoType+
                            "'>You Can't Play Video on this Browser</video><br>"+
                            "<div><a class=videoLink href='"+optLink+"'>"+
                            optLink+"</a></div>"+
                            "<br>";
                    }
                }

                if(toInsert != ""){
                    var keepLeft = oriText.substr(0,start);
                    var keepRight = oriText.substr(i);
                    node.innerHTML = keepLeft+toInsert+keepRight;
                }
            }
        }
    }
}

function nuTextAreaDefaults(){
    var newTextAreaEle = document.getElementById("newTextArea");
    if(newTextAreaEle == null) return;

    newTextAreaEle.innerHTML = "Please Use the Buttons or correct format to insert images, links..."+
        "If you do it right, it will automatically transform to the selected item"+
        "<br><img src='../res/emotes/bumoass.gif' style='text-align: center;width: 100px;display:inline;'>"+
        "<span>(click to remove bumo's(bury fumo) ass)</span>";
    newTextAreaEle.style.color="#00000096";
    newTextAreaEle.addEventListener("focus",function(){
        newTextAreaEle.innerText = "";
        newTextAreaEle.style.color="#000000";
        newTextAreaEle.removeEventListener("focus",arguments.callee,false);
        isTextboxChanged = true;
    });

    var titEle = document.getElementById("tit");
    if(titEle){
        titEle.value="Title";
        titEle.style.color="#00000096";
        titEle.addEventListener("focus",function(){
            titEle.value= "";
            titEle.style.color="#000000";
            titEle.removeEventListener("focus",arguments.callee,false);
        });
    }
}
function showTextEmote(){
    var emoteBoxParent = document.getElementById("emoteExpandButton");
    if(!emoteIsExpanded){
        emoteBoxParent.innerHTML= "EMOTE &#9668;";

        if(emoteGenStorage== null){
            emoteGenStorage= document.createElement("div");
            emoteGenStorage.id = "emoteBox";

            var topBar = document.createElement("div");
            topBar.id = "topBar";
            topBar.innerHTML = "<div id=searchBar>"+
                "<span id=searchBarTit>Search:</span>"+
                "<input id=searchBarInput oninput=searchEmotes(this)>"
                +"</div>";
            emoteGenStorage.appendChild(topBar);
            var horiRet = document.createElement("hr");
            horiRet.id = "emoteHR";
            emoteGenStorage.appendChild(horiRet);

            var loadEmoteCont = document.createElement("div");
            loadEmoteCont.id = "loadEmoteCont";
            loadEmoteCont.innerHTML = emoteAjax();
            emoteGenStorage.appendChild(loadEmoteCont);

            emoteBoxParent.parentElement.appendChild(emoteGenStorage);
        } else{
            emoteGenStorage.style.display = "block";
        }

    } else {
        collapseEmoteBox(emoteBoxParent);
    }
    emoteIsExpanded ^= 1;
}
function searchEmotes(textInput){
    emoteSearcher(textInput.value.toLowerCase());
    /*
    for(var emote of emoteSearchResult){
        console.log(emote);
    }
    console.log("---");
    */
    var ele= document.getElementById("loadEmoteCont");
    var eleChildren = null;
    if(ele != null) eleChildren = ele.children;

    for(var cNode of eleChildren){
        var str = cNode.id.substring(6).toLowerCase();
        if(emoteSearchResult.has(str)){
            cNode.style.display = "inline";
        } else{
            cNode.style.display = "none";
        }
    }
}
function collapseEmoteBox(emoteParentText){
    if(emoteIsExpanded && emoteGenStorage != null){
        emoteGenStorage.style.display = "none";
        emoteParentText.innerHTML= "EMOTE &#9658;";
    }
    return emoteGenStorage;
}
function emoteAjax(){
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        emoteTrie = new trieNode;

        var emoteJSONStorage = JSON.parse(this.responseText);
        var emoteKeys = [];
        var returnStr = "";
        for(let key in emoteJSONStorage){
            let url= "../res/emotes/"+emoteJSONStorage[key];
            
            emoteJSONStorage[key] = url;
            emoteKeys.push(key);
            returnStr+="<a href='javascript:addEmoteToText(\""
                        +key+"\",\""+url+"\")'"+
                        " id=emote_"+key+">"+
                        "<img src='"+url+"' class=addEmote_icon "+
                        "onmouseover='expandEmote(this)' "+
                        "onmouseout='deflateEmote(this)'></a>";
        }
        document.getElementById("loadEmoteCont").innerHTML=returnStr;
        trieCreator(emoteKeys,emoteJSONStorage);
    }
    xhttp.open("GET", "acclaimGenerator/loadEmotes.php", true);
    xhttp.send();
    return "LOADING...";
}

function addEmoteToText(key,url){
    var newTextArea = document.getElementById("newTextArea");
    newTextArea.focus();

    var emote_icon = document.createElement("img");
    emote_icon.src = url;
    emote_icon.className = "inTextEmote";
    emote_icon.emoteName = key;

    newTextArea.appendChild(emote_icon);
}
function trieCreator(wordList,JSONobj){
    for(var tmp_word of wordList){
        var word = tmp_word.toLowerCase();
        var curNode = emoteTrie;
        for(var i = 0; i < word.length; i++){
            var letterChar = word[i];
            var nodeRef = curNode.letters.get(letterChar);
            if(nodeRef != null){
                curNode = nodeRef;
            } else{
                curNode.letters.set(letterChar, new trieNode);
                curNode = curNode.letters.get(letterChar);
            }
        }
        curNode.nodeFinish=true;
    }
}
//this is bad code bc of global(search result) but this 
//might be the most efficient i can think of
//at 12am. such is lyfe
function emoteSearcher(prefix){
    emoteSearchResult.clear();
    var nodeRef = emoteTrie;
    for(var i = 0; i < prefix.length; i++){
        nodeRef = nodeRef.letters.get(prefix[i]);
        if(nodeRef == null) return [];
    }
    return iterateNode(nodeRef,prefix);
}
function iterateNode(node,retStr){
    if(node == null) return;
    if(node.nodeFinish == true){
        emoteSearchResult.add(retStr);
        return;
    }

    for(const [key,value] of node.letters){
        iterateNode(value,retStr+key);
    }
}
