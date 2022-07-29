<?php
    function generatePage(){
        global $allBoards,$boardPageName,$threadType;
        $curPage = $boardPageName;

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
                    if($threadType == 1){
                        include_once("genPhp/generateImgRender.php");
                        genImgThread($curPage,$TID); 
                    } else{
                        generateThread($curPage,$TID);
                        createNewComment($curPage,$TID);
                    }
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
        echo "<div class=newsPostCont>";
        echo "<div class=newsPost_title>" . $title;
        echo "<span class=newsPost_time>" . $datePost . "</span>";
        echo "</div>";

        echo "<div class=newPost_main>".$content."</div><hr></div>";
    }

    function generateBoard($board){
        include_once("acclaimGenerator/listAcclaim_func.php");
        global $boardThreads,$pgN,$threadsPerPage;

        for($i = $pgN * $threadsPerPage, $j = 0; $j < $threadsPerPage && 
         $i < count($boardThreads); $i++, $j++){
            $row = $boardThreads[$i]; 
            //toss post acclaim here
            postThreads($board,$row["title"],$row["time"],$row["threadId"],
                $row["postCnt"],$row["acclaim"],($row["tags"]=="pin" ? "pin" : ""));
        } 
    }
        
    function postThreads($board,$title,$time,$TID,$postCnt,$acclaim,$classTag=""){
        $time = timeRegFormat($time);
        $newLink = $_SERVER["REQUEST_URI"] . "&TID=".$TID;
        echo "<div id=p_$TID class=threadEncap onclick='threadRedirect(\"$newLink\",$TID)' 
                onmouseover=threadHover($TID) onmouseout=threadUnHover($TID) >";
        echo "<span class=threadInfo>" . $time . " :::: TID:" .$TID. " | 
            (<span class=postCnter id=cnt_$TID>".$postCnt."</span>)".
            "<span id=pDiff_$TID></span>
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
        $time = timeRegFormat($time);
        if(empty($UID)) $UID = 0;
        $boardHash = ord($board[0]);
        //$UID = (($UID%1000000)^($TID^($TID<<16))^$boardHash);
        $UID = getUsrSafeHash($UID,$TID,$boardHash); 
        $id_sel = "p".$UID."_".$PID;
        echo "<div class=postEncap id=pd".$PID.">";
        echo "<style> #".$id_sel." { background-color:#".$UID."; } </style>";
        echo "<span class=UID id='$id_sel'>" . $UID . "</span>";
        echo "<span class=threadInfo>" . $time . " :::: PID:" .
            "<a href='javascript:quotePost($PID)'>".  sprintf("%'.07d\n",$PID) . "</a><br>";
        echo "</span>";
        echo "<div class=postContent>" . $content . "</div>";
        echo "<hr>";
        echo "</div>";
    }

    function createNewThread($board){
        //$redirect = $_SERVER['REQUEST_URI'];
        //$redirect = 'postSuccess.php?page=' . $board;
        $redirect = 'postSuccess/index.php?page=' . $board;
        echo 
        '<br><div class=newPostBox>
        <form action=' . $redirect. ' method="post" id=pageForm>
            <input name="title" id="tit" value=Title onclick="updateTitle()"
            onfocusout="updateTitle()"><br>'.
            showTextArea() .
            'Anote Thread: <input type="checkbox" name="anote" value="yes">
            <div id=captchaCont><div id=pcaptcha class="g-recaptcha" 
                data-sitekey="6Ld7YKAeAAAAAJRQRJyy3TX5uGz3O4BwQDOOgGw_">
            </div></div><br>
        </form></div>'; 
    }
    function createNewComment($board,$TID){
        //$redirect = 'postSuccess.php?page='.$board."&TID=".$TID;
        $redirect = 'postSuccess/index.php?page='.$board."&TID=".$TID;
        echo 
        '<br><div class=newPostBox>
        <form action=' . $redirect. ' method="post" id=pageForm>'.
            showTextArea() .
            '<div id=captchaCont><div id=pcaptcha class="g-recaptcha" 
                data-sitekey="6Ld7YKAeAAAAAJRQRJyy3TX5uGz3O4BwQDOOgGw_">
            </div></div>'.
            '</form></div>'; 
    }

    function showTextArea(){
        return '
            <textarea name="content" id=hiddenTextInput></textarea>
            <script>document.getElementById("pageForm").appendChild(generateTextArea())</script>
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
