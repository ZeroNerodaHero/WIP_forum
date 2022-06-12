/*
 * note this file is never used anywhere. i thought js has a import/export file thing
 * only keeping it here bc i might want to combine a few files up
 *
 * place trying to replace 
 *  emoteAdd 
 *  reader reload
 */
/*
Parameters:
Required:
    text - string of what will be displayed
Optional
    duration - duration of the display(ms)
    boxShadowColor - use rgba
*/
//------------------------------------------
export function createNonContentMsg(text,duration=2000,boxShadowColor="rgba(0,0,0,0)"){
    var errorBox= document.createElement("div");
    errorBox.className= "noncontentMsg";
    errorBox.innerHTML = text;
                        
    setTimeout(function(){
        errorBox.remove();
    }, duration);

    errorBox.style.boxShadow = errorBox.style.webkitBoxShadow = 
        "0px 0px 17px 4px "+boxShadowColor;

    return errorBox;
}

/*
 *
 * maybe use resize observer?
 */
