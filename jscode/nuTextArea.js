function generateTextArea(){
    var newTextAreaEle = document.createElement("div");
    newTextAreaEle.id = "nuTextEditor";
    newTextAreaEle.innerHTML="<div id=textButtonButtonCont><div id=textButtonCont><span class=textButton onclick=addIMG()> ADD IMG </span><span class=textButton onclick=addLNK()> ADD LNK </span> <span class=textButton onclick=addYTB()> ADD YTB </span> <span class=textButton onclick=addVIDEO()> ADD VIDEO </span> </div> </div> <div id=newTextAreaCont> <div contenteditable='true' id=newTextArea></div> </div> <div id=submitCont> <div id=submitButtonCont> <div id=submitButton onclick=submitValue()>Submit </button> </div> </div>";
    newTextAreaEle.addEventListener('keyup',function(event){
        replaceView();
    });
    newTextAreaEle.addEventListener('click',function(event){
    });
    return newTextAreaEle;
}

function addIMG(){
    appendText("[IMG]");
}
function addLNK(){
    appendText("[LNK]",0);
}
function addYTB(){
    appendText("[YTB]");
}
function addVIDEO(){
    appendText("[VIDEO]");
}
function submitValue(){
    var newTextAreaEle = document.getElementById("newTextArea");
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
            return "\n[IMG]("+node.src+")\n";
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

    var retStr="";
    if(node.nodeName == "DIV") retStr += "\n";
    var nodeChildren = node.childNodes;
    for(var childNode of nodeChildren){
        retStr += extractString(childNode);
    }
    if(node.nodeName == "DIV") retStr += "\n";
    return retStr;
}
function standardizeText(inputStr){
    var i = 0;
    //used to remove inital " " and newlines
    while(inputStr[i] == " " || inputStr[i] == "\n") i++;
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

function appendText(text,lineBreak=1){
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

    var newTextArea = document.getElementById("newTextArea");
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
                    toInsert = "<img src='"+optLink+"'><br>";
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
