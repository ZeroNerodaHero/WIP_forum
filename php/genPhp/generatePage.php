<?php
    function generatePage(){
        global $conn;
        $curPage = "news";
        if(!empty($_GET["page"])){
            $curPage = $_GET["page"];
        }

        if($curPage == "news"){
	    echo "<script>showFuncButtons(0);</script>";

	    $newsNo = 0;
	    if(!empty($_GET["newsNo"])) $newsNo = $_GET["newsNo"];
            generateNews($newsNo);            
        } else if($curPage == "blog"){
			
        } else {
            $que = "SELECT * FROM boards WHERE boardName = '$curPage'";
            $res = $conn->query($que);
            if($res->num_rows > 0){
                if(!empty($_GET["TID"])){
                    $TID = $_GET["TID"];
                    generateThread($curPage,$TID);
                    createNewComment($curPage,$TID);
                } else{
                    generateBoard($curPage);
                    createNewThread($curPage);
                }
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
            echo "no news or something went wrong lmao <br>";
        }
    }

    function postNews($title,$datePost,$content){
        echo "<div class=newsPost_title>" . $title;
        echo "<span class=newsPost_time>" . $datePost . "</span>";
        echo "</div><br>";

        echo $content . "<br><br><hr>";
    }

    function generateBoard($board){
        global $connBoards;
		//post pinned threads
        $que = "SELECT * FROM ".$board."Threads 
				WHERE tags='pin' ORDER BY threadId ASC";
        $res = $connBoards->query($que); 
        if(!empty($res) && $res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                postThreads($row["title"],$row["time"],$row["threadId"],"pin");
            }
        }

		//post normal threads
        $que = "SELECT * FROM ".$board."Threads ORDER BY time DESC";

        $res = $connBoards->query($que); 
        if(!empty($res) && $res->num_rows > 0){
            while($row = $res->fetch_assoc()){
				if($row["tags"]=="pin") continue;
                postThreads($row["title"],$row["time"],$row["threadId"]);
            }
        } else{
            echo "no threads or something went wrong lmao <br>";
        }
    }
        
    function postThreads($title,$time,$TID,$classTag=""){
        $newLink = $_SERVER["REQUEST_URI"] . "&TID=".$TID;
        echo "<div onclick='threadRedirect(\"$newLink\")'>";
        echo "<span class=threadInfo>" . $time . " :::: TID: " .
            $TID . " | <a href='$newLink'> >>> </a></span>";
		echo "<div class='thread_title $classTag'>" . $title . "</div>";
        echo "<br><hr></div>";
    }

    function generateThread($board,$TID){
        global $connBoards;
        $table = $board."\_" . $TID;
        $que = "SHOW TABLES LIKE '$table'";
        //echo $que . "<br>";

        $res = $connBoards->query($que);
        if(empty($res) || $res->num_rows < 1){
            echo "ERROR: thread didn't show or doesn't exist";
            return NULL;
        }

        $que = "SELECT * FROM ".$board."_".$TID;
        //echo $que . "<br>";
        $res = $connBoards->query($que);
        if(!empty($res) && $res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                postComment($row["content"],$row["ip"],$row["time"],$row["postId"],$board,$TID);
            }
        } else{
            echo "something went wrong lmao <br>";
        }
    }
        
    function postComment($content,$UID,$time,$PID,$board,$TID){
        if(empty($UID)) $UID = 0;
        $UID = (($UID%1000000)^($TID^($TID<<16)));
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
            <input type="submit" value="Post"> <br>
            Message: <br> ' .
            showTextArea() .
            '<br>
        </form></div>'; 
    }
    function createNewComment($board,$TID){
        //$redirect = 'postSuccess.php?page='.$board."&TID=".$TID;
        $redirect = 'postSuccess/index.php?page='.$board."&TID=".$TID;
        echo 
        '<br><div class=newPostBox>
        <form action=' . $redirect. ' method="post">
            Message: <input type="submit" value="Post"> <br> ' .
            showTextArea() .
        '</form></div>'; 
    }

    function showTextArea(){
        return '
            <textarea id="textArea" name="content" rows="6" cols="30" ></textarea> <br>
            <button onclick="addImg()" type="button"> ADD IMG </button>
            <button onclick="addLink()" type="button"> ADD LNK </button>
			<button onclick="addYTB()" type="button"> ADD YTB </button>
        ';
    }

    function protecIP($ip){
        //use some sort of encryption
        //right now random stuff
        return ($ip % 100000);
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
</script>
