/*
I use an integer to set up the values in the settings

{
setting: 32bit int
imgLnk:
color:
opacity:
font-size:
}

use ot^(1<<setting number)
find setting number below

xxxx xxxx xx...
0123 4567 89...

0: collapseNav?
1: collapseRecent
//wtf is 2?
3: bkg is img or color
4: mainboards collapsed?
5: shitboards collapsed?
6: starred or recent (see starThread.js)
    0: is star
    1: is recent
*/

function toggleNavList(type,toggleOrNot){
    var position = -1;
    if(type == "main") position = 4;
    if(type == "shit") position = 5;
    if(position == -1) return;

    var fullSetting = getSettings("settings");
    fullSetting.setting = (fullSetting.setting^(toggleOrNot<<position));
    var ot = fullSetting.setting& (1<<position);
    setCookie("settings", JSON.stringify(fullSetting));
      
    var eleText=document.getElementById(type+"NavToggle");
    var eleFlex=document.getElementById(type+"flexList");
    if(!ot){
        eleText.text="Close";
        eleFlex.style.display="flex";
    } else{
        eleText.text="Open";
        eleFlex.style.display="none";
    }
}

function navListClick(type){
    toggleNavList(type,1);
}

function toggleNav(toggleOrNot=1){
    togglePage(0,"navContainer","navCollapseText",toggleOrNot);
}
function toggleRecent(toggleOrNot=1){
    togglePage(1,"watchThreadContainer",
        "watchThreadCollapseText",toggleOrNot);
}
function getSettings(){
    var settingCookieStr = getCookie("settings");
    var fullSetting = {"setting":4,"imgLnk":"","color":"","opacity":"","font-size":"1em"};

    if(settingCookieStr != "" && settingCookieStr[0] == "{")
        fullSetting = JSON.parse(settingCookieStr);
    return fullSetting;
}
function togglePage(position,docCont,docColText,toggleOrNot){
    var fullSetting = getSettings("settings");
    fullSetting.setting = (fullSetting.setting^(toggleOrNot<<position));

    var ot = fullSetting.setting& (1<<position);
    setCookie("settings", JSON.stringify(fullSetting));

    var ele = document.getElementById(docColText);
    if(ot){
        ele.classList.add("bottom");
        ele.classList.remove("top");
    }else{
        ele.classList.remove("bottom");
        ele.classList.add("top");
    }

    var ele = document.getElementById(docCont);
    if(ot){
        ele.style.display="none";
    }else{
        ele.style.display="block";
    }
}

function setCookie(key,val){
    const d = new Date();
    d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
    let expires = "expires="+d.toUTCString();
    document.cookie = key + "=" + val + ";" + expires + ";path=/php";
}

function getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for(let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
	}
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
	}
    }
    return '';
}


/* COOKIE STUFF
 */
//0 - img
//1 - color
function chngBkgType(opt){
    var fullSetting = getSettings("settings");
    if(opt) fullSetting.setting |= (1<<2);
    else fullSetting.setting &= (~(1<<2));
    //sets bit according to the opt

    setCookie("settings", JSON.stringify(fullSetting));
    regenerateBody();
}

function imgLnkButton(){
    var eleButton = document.getElementById("imgLnkSubmit");
    var eleText = document.getElementById("bkgImgLnk");
    if(eleText.value == "") return;

    var fullSetting = getSettings("settings");
    fullSetting.imgLnk = eleText.value;
    setCookie("settings", JSON.stringify(fullSetting));
    regenerateBody();
}
function colorButton(){
    var eleButton = document.getElementById("colorSubmit");
    var eleText = document.getElementById("bkgColor");
    if(eleText.value == "") return;

    var fullSetting = getSettings("settings");
    fullSetting.color = eleText.value;
    setCookie("settings", JSON.stringify(fullSetting));
    regenerateBody();
}

function regenerateBody(){
    var fullSetting = getSettings("settings");

    //maybe should place some stuff here but nah
    //might not work well after the user is changing both links but...
    document.getElementById("bkgImgLnk").value = fullSetting.imgLnk;
    document.getElementById("bkgColor").value = fullSetting.color;

    //don't understand how to use remove property. but to switch
    //back to background color set the url of the img to ""
    var ele = document.getElementsByTagName("body")[0];
    if((fullSetting.setting & (1<<2))){
        document.getElementById("bkgType_color").checked=true;
        ele.style.backgroundImage = "";
        ele.style.backgroundColor = fullSetting.color;
    } else {
        document.getElementById("bkgType_img").checked=true;
        ele.style.backgroundImage = "url("+fullSetting.imgLnk+")";
    }
}

function generatePageStyle(){
    toggleNav(0);
    toggleRecent(0);
    toggleNavList('shit',0);
    toggleNavList('main',0);
    regenerateBody();
}

