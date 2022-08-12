function updateBody(typeCode){
    if(typeCode == 0){
        //cannot set it as 1 in postdata bc
        //empty(0) is false not true
        login();
    }
    else if(typeCode >= 2 && typeCode <= 6){
        var ele = document.getElementById("advertStuff");
        toServer("typeCode="+typeCode,1,ele);
    }
}
function login(){
    var usernameVal = document.getElementById("username").value;
    var passwordVal= document.getElementById("passwd").value;
    var postData = "typeCode=1&username="+usernameVal+"&password="+passwordVal;
    toServer(postData,1,document.getElementById("toReplace"));
}
function setUsrId(id){
    console.log(id);
}

/* responseType -> 0 do ntohing
 * 1 -> set stuff
 * 2 -> execute stuff
 * */
function toServer(postData,responseType=0,stuff=0){
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200){
            if(responseType==1){
                stuff.innerHTML = this.responseText;
            }
            else if(responseType == 2){
                stuff();
            } else if(responseType == 3){
                stuff(JSON.parse(this.responseText));
            }
        }
    }
    xhttp.open("POST", "backgroundAdvert.php");
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(postData);
}

function updatePreviewImg(){
    var imgValEle = document.getElementById("lnkToImg");
    var imgPreviewEle = document.getElementById("addAdvertImgPreview");
    if(imgValEle != null && imgPreviewEle !=null) imgPreviewEle.src=imgValEle.value;
}
function updatePreviewLnk(){
    var lnkValEle = document.getElementById("lnkToSite");
    var lnkPreviewEle = document.getElementById("addAdvertLnkPreview");
    if(lnkValEle != null && lnkPreviewEle !=null) lnkPreviewEle.href=lnkValEle.value;
}
function newAd(){
    var imgValEle = document.getElementById("lnkToImg");
    var lnkValEle = document.getElementById("lnkToSite");
    var creditEle = document.getElementById("addAdCredits");
    var postData = "typeCode=10&lnkToImg="+encodeURIComponent(imgValEle.value)+
            "&lnkToSite="+encodeURIComponent(lnkValEle.value)+
            "&credits="+creditEle.value;
    toServer(postData,3,function(serverResponse){
        imgValEle.value = lnkValEle.value = creditEle.value = "";
        document.getElementById("addAdvertImgPreview").src="../res/emotes/emote_2.png";
        document.getElementById("addAdvertLnkPreview").href="";
        if(serverResponse.code == 0){
            toServer("typeCode=7",1,document.getElementById("toReplace"));
        } else{
            var newEle = document.createElement("div");
            newEle.id="addAdvertErrorMessage";
            newEle.innerText = "OY VEY: YOU DO NOT HAVE ENOUGH CREDITS.";
            document.getElementById("advertStuff").insertBefore(
                newEle,document.getElementById("addAdvertCont"));
        }
    });
}
function deleteAd(id){
    var postData = "typeCode=11&adId="+id;
    toServer(postData,2,function(){
        toServer("typeCode=7",1,document.getElementById("toReplace"));
    });
}
function updatePWordCheck(){
    var oldPWord = document.getElementById("oldPword").value;
    var newPWord = document.getElementById("newPword").value;
    var retypePWord = document.getElementById("retypePword").value;
    if(oldPWord != "" && newPWord != "" && newPWord.length > 6 && newPWord == retypePWord){
        document.getElementById("changePWordButton").disabled=false;
    } else {
        document.getElementById("changePWordButton").disabled=true;
    }
}
function changePWord(){
    var oldPWord = document.getElementById("oldPword").value;
    var newPWord = document.getElementById("newPword").value;
    var retypePWord = document.getElementById("retypePword").value;
    if(oldPWord != "" && newPWord != "" && newPWord.length > 6 && newPWord == retypePWord){
        var postData = "typeCode=20&pword="+oldPWord+"&newPWord="+newPWord;
        toServer(postData,1,document.getElementById("advertStuff"));
    }
}
function addPromo(){
    var promoInput = document.getElementById("promoCodeInput");
    if(promoInput.value != ""){
        var postData = "typeCode=24&promoCode="+promoInput.value;
        toServer(postData,1,document.getElementById("toReplace"));
    }
}
/*-----------------------------------------*/
/*-------SIGN UP---------------------------*/
function checkPasswds(){
    var passwdEle = document.getElementById("passwd");
    var repasswdEle = document.getElementById("repasswd");
    var checkerEle = document.getElementById("goodRetype");
    var submitEle = document.getElementById("registerInput");
    if(passwdEle.value.length > 6 && passwdEle.value == repasswdEle.value){
        checkerEle.style.color="green";
    } else{
        checkerEle.style.color="red";
    }
    checkRegister();
}
function uniqueUserName(){
    var toCheck = "username";
    var value = document.getElementById("username").value;
    if(value.length > 5) checkUnique(toCheck,value);
    else{
        var goodEle = document.getElementById("goodusername");
        setColorEle(goodEle,0);
    }
}
function uniqueEmail(){
    var toCheck = "email";
    var value = document.getElementById("email").value;
    if(checkValidEmail(value)){
        checkUnique(toCheck,value);
    } else{
        var goodEle = document.getElementById("goodemail");
        setColorEle(goodEle,0);
    }
}
function checkUnique(toCheck,value){ 
    var goodEle = document.getElementById("good"+toCheck);

}
/*-----------------------------------------*/
/*-------SIGN UP---------------------------*/
function checkPasswds(){
    var passwdEle = document.getElementById("passwd");
    var repasswdEle = document.getElementById("repasswd");
    var checkerEle = document.getElementById("goodRetype");
    var submitEle = document.getElementById("registerInput");
    if(passwdEle.value.length > 6 && passwdEle.value == repasswdEle.value){
        checkerEle.style.color="green";
    } else{
        checkerEle.style.color="red";
    }
    checkRegister();
}
function uniqueUserName(){
    var toCheck = "username";
    var value = document.getElementById("username").value;
    if(value.length > 5) checkUnique(toCheck,value);
    else{
        var goodEle = document.getElementById("goodusername");
        setColorEle(goodEle,0);
    }
}
function uniqueEmail(){
    var toCheck = "email";
    var value = document.getElementById("email").value;
    if(checkValidEmail(value)){
        checkUnique(toCheck,value);
    } else{
        var goodEle = document.getElementById("goodemail");
        setColorEle(goodEle,0);
    }
}
function checkUnique(toCheck,value){ 
    var goodEle = document.getElementById("good"+toCheck);
    goodEle.style.color="";

    var postData = "isCheck=1&toCheck="+toCheck+"&value="+value;
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200){
            setColorEle(goodEle,(this.responseText != "1"));
            checkRegister();
        }
    }
    xhttp.open("POST", "backgroundSignUp.php");
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(postData);
}
function setColorEle(ele,bval){
    if(bval){
        ele.style.color="green";
    } else{
        ele.style.color="red";
    }

}
function checkRegister(){
    var goodUserName= document.getElementById("goodusername");
    var goodEmail= document.getElementById("goodemail");
    var goodRetype = document.getElementById("goodRetype");
    var submitEle = document.getElementById("registerInput");

    if(goodUserName.style.color == "green" &&
      goodEmail.style.color == "green" && 
      goodRetype.style.color == "green"){
        submitEle.disabled=false;
    } else{
        submitEle.disabled=true;
    }
}
function checkValidEmail(email){
    var hasAt = false, hasDot = false;
    for(var i = 0; i < email.length; i++){
        if(email[i] == '@') hasAt = 1;
        if(email[i] == '.') hasDot = 1;
    }
    return hasAt && hasDot;
}
