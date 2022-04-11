<?php
    function generatePage(){
        global $allBoards,$boardPageName;
        $curPage = "news";
        if(!empty($_GET["page"])){
            $curPage = $_GET["page"];
        }

	$pageNo = 0;
	if(!empty($_GET["pgN"])) $pageNo = $_GET["pgN"];
        if($curPage == "news"){
	    echo "<script>showFuncButtons(0);</script>";
            generateNews($pageNo);            
        } else if($curPage == "blog"){
			
        } else {
            //just to verify if this is fine
            $hasBoard = 0;
            foreach($allBoards as $row){
                if($row["boardName"] == $boardPageName){
                    $hasBoard = 1;
                    break;
                }
            }
            if($hasBoard){
                if(!empty($_GET["TID"])){
                    $TID = $_GET["TID"];
                    generateThread($curPage,$TID);
                    createNewComment($curPage,$TID);
                } else{
                    generateBoard($curPage);
                    createNewThread($curPage);
                }
            } else{
                echoError("BOARD DOESN'T EXIST");
            }
        }
    }

    function generateNews($newsNo){
        global $conn;

        $BEGIN = $newsNo*10;
        $END = $newsNo*10+10;
        $que = "SELECT * FROM frontNews ORDER BY id DESC LIMIT $BEGIN,$END";
        //echo "'$que'<br>";
         
        $res = $conn->query($que); 
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                postNews($row["news_title"],$row["post_time"],
                    $row["news_content"]);
            }
        } else{
            echoError("no news or something went wrong lmao");
        }
    }

    function postNews($title,$datePost,$content){
        echo "<div class=newsPost_title>" . $title;
        echo "<span class=newsPost_time>" . $datePost . "</span>";
        echo "</div><br>";

        echo $content . "<br><br><hr>";
    }

    function generateBoard($board){
        include_once("acclaimGenerator/listAcclaim_func.php");
        global $boardThreads,$pgN,$threadsPerPage;
	//post pinned threads
        for($i = $pgN * $threadsPerPage, $j = 0; $j < $threadsPerPage && 
         $i < count($boardThreads); $i++, $j++){
            $row = $boardThreads[$i]; 
            //toss post acclaim here
            postThreads($board,$row["title"],$row["time"],$row["threadId"],
                $row["postCnt"],$row["acclaim"],($row["tags"]=="pin" ? "pin" : ""));
        } 
    }
        
    function postThreads($board,$title,$time,$TID,$postCnt,$acclaim,$classTag=""){
        $newLink = $_SERVER["REQUEST_URI"] . "&TID=".$TID;
        echo "<div id=p_$TID class=threadEncap onclick='threadRedirect(\"$newLink\",$TID)' >";
        echo "<span class=threadInfo>" . $time . " :::: TID: " .$TID. " | 
            (<span class=postCnter id=cnt_$TID> ".$postCnt." </span>) 
            <span id=pDiff_$TID></span>
            <a href='$newLink'> >>> </a></span>";
		echo "<div class='thread_title $classTag'>" . $title . "</div>";

        echo "<div id=acclaimCont_$TID class=acclaimCont>";
        genAcclaim($acclaim);
        echo "<span id=acclaimTID_$TID class=acclaimDisplay 
                onclick=loadAcclaim('$board',$TID)>+</span>";
        echo "</div>";

        echo "<hr></div>";
    }

    function generateThread($board,$TID){
        global $threadContent;
        $errorThread = 0;

        foreach($threadContent as $row){
            postComment($row["content"],$row["ip"],$row["time"],$row["postId"],$board,$TID);
            $errorThread = 1;
        } 
        if(!($errorThread)){
            echoError("something went wrong lmao");
        }
    }
        
    function postComment($content,$UID,$time,$PID,$board,$TID){
        if(empty($UID)) $UID = 0;
        $boardHash = ord($board[0]);
        $UID = (($UID%1000000)^($TID^($TID<<16))^$boardHash);
        $id_sel = "p".$UID."_".$PID;
        echo "<div class=postEncap id=pd".$PID.">";
        echo "<style> #".$id_sel." { background-color:#".$UID."; } </style>";
        echo "<span class=UID id='$id_sel'>" . $UID . "</span>";
        echo "<span class=threadInfo>" . $time . " :::: PID: " .
            "<a href='javascript:quotePost($PID)'>".  sprintf("%'.07d\n",$PID) . "</a><br>";
        echo "</span>";
        echo "<p class=postContent>" . $content . "</p>";
        echo "<hr>";
        echo "</div>";
    }

    function createNewThread($board){
        //$redirect = $_SERVER['REQUEST_URI'];
        //$redirect = 'postSuccess.php?page=' . $board;
        $redirect = 'postSuccess/index.php?page=' . $board;
        echo 
        '<br><div class=newPostBox>
        <form action=' . $redirect. ' method="post">
            Title: <input type="text" name="title" class="tit" size="65"> 
            <input type="submit" value="Post">.
            Message: <br> ' .
            showTextArea() .
            '<div id=captchaCont><div id=pcaptcha class="g-recaptcha" 
                data-sitekey="6Ld7YKAeAAAAAJRQRJyy3TX5uGz3O4BwQDOOgGw_">
            </div></div><br>
        </form></div>'; 
    }
    function createNewComment($board,$TID){
        //$redirect = 'postSuccess.php?page='.$board."&TID=".$TID;
        $redirect = 'postSuccess/index.php?page='.$board."&TID=".$TID;
        echo 
        '<br><div class=newPostBox>
        <form action=' . $redirect. ' method="post">
            Message: <input type="submit" value="Post"> <br> '.
            showTextArea() .
            '<div id=captchaCont><div id=pcaptcha class="g-recaptcha" 
                data-sitekey="6Ld7YKAeAAAAAJRQRJyy3TX5uGz3O4BwQDOOgGw_">
            </div></div>'.
            '</form></div>'; 
    }

    function showTextArea(){
        return '
            <textarea id="textArea" name="content" rows="6" cols="30" ></textarea> <br>
            ADD: 
            <button onclick="addImg()" type="button"> IMG </button>
            <button onclick="addLink()" type="button"> LNK </button>
	    <button onclick="addYTB()" type="button"> YTB </button>
	    <button onclick="addVIDEO()" type="button"> VIDEO </button>
        ';
    }

    function protecIP($ip){
        //use some sort of encryption
        //right now random stuff
        return ($ip % 100000);
    }
    function echoError($msg){
        echo "<div class=errorContent> $msg <br>";
        echoImg('/res/random/final.gif','/php',"errorImgContent");
        echo "</div>";
    }
?>

<script>
    function addImg(){
        document.getElementById("textArea").value += "[IMG]()";
    }
    function addLink(){
        document.getElementById("textArea").value += "[LNK]()";
    }
    function addYTB(){
        document.getElementById("textArea").value += "[YTB]()";
    }
    function addVIDEO(){
        document.getElementById("textArea").value += "[VIDEO]()";
    }
</script>