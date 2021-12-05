function quotePost(val){
    console.log("post is " + val);
    document.getElementById("textArea").value += "##"+ val+"\n";
}

function jumpPost(val){
    var ele = document.getElementById(val);
    var top = ele.offsetTop;
    window.scrollTo(0,top-10);
    
    var oldColor= ele.style.backgroundColor;
    ele.style.backgroundColor = "#606060";

    setTimeout(function() {
        ele.style.backgroundColor = oldColor;
    },500);
}

