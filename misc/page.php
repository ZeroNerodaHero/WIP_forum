<?php
    include_once("../adminPower/login.php");


    function generatePage(){
        if(empty($_GET["page"])){
            echo "GET BACK IN LINE";
            return NULL;
        }
        global $conn;
        //echo "UR ON " . $_GET["page"] . "<br>";
        //echo "IP IS " . sprintf("%u",ip2long(getIP())) . "<br>";
        $curPage = $_GET["page"];
        if($curPage == "news"){
            generateNews();            
        } else if($curPage == "blog"){

        } else {
            $que = "SELECT * FROM boards WHERE boardName = '$curPage'";
            $res = $conn->query($que);
            if($res->num_rows > 0){
                if(!empty($_GET["TID"])){
                    $TID = $_GET["TID"];
                    generateThread($curPage,$TID);
                } else{
                    generateBoard($curPage);
                    createNewThread($curPage);
                }
            } 
        }
    }

    function generateNews(){
        global $conn;

        $BEGIN = 0;
        $END = 10;
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
        $que = "SELECT * FROM ".$board."Threads ORDER BY time ASC";
//echo $que."<br>";

        $res = $connBoards->query($que); 
        if(!empty($res) && $res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                postThreads($row["title"],$row["time"],$row["threadId"]);
            }
        } else{
            echo "no threads or something went wrong lmao <br>";
        }
    }
        
    function postThreads($title,$time,$TID){
        $newLink = $_SERVER["REQUEST_URI"] . "&TID=".$TID;
        echo "<div class=thread_title>" . $title;
        echo "<span class=threadInfo>" . $time . " :::: TID: " .
            $TID . " | ";
        echo "<a href='$newLink'> > </a>";
        echo "</span></div><br><hr>";
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

        $que = "SELECT postId,time,content FROM ".$board."_".$TID;
        //echo $que . "<br>";
        $res = $connBoards->query($que);
        if(!empty($res) && $res->num_rows > 0){
            while($row = $res->fetch_assoc()){

                postComment($row["content"],$row["time"],$row["postId"]);
            }
        } else{
            echo "something went wrong lmao <br>";
        }
    }
        
    function postComment($content,$time,$PID){
        echo "<span class=threadInfo>" . $time . " :::: PID: " .
            $PID . "<br>";
        echo "</span>";
        echo "<p>" . $content . "</p>";
        echo "<hr>";
    }

    function createNewThread($board){
        global $connBoards;
        $postTitle = addslashes($_POST["title"]); 
        $postContent = addslashes($_POST["content"]); 
        $threadId=NULL;

        if(!empty($postTitle) && !empty($postContent)){
            $que = "INSERT INTO ". $board. "Threads (title)
                VALUES('$postTitle')";
myQuery($connBoards,$que);
 
            $threadId = $connBoards->insert_id;
            $newTable = $board . "_" . $threadId;

            //create post table and post
            $que = "CREATE TABLE " . $newTable . "(
                        postId int NOT NULL AUTO_INCREMENT,
                        time TIMESTAMP,
                        content varchar(2000),
                        ip int,
                        PRIMARY KEY(postId)
                    )";
myQuery($connBoards,$que);

            $usrIP = ip2long(getusrIP());
            $que = "INSERT INTO ". $newTable . "( content,ip) 
                    VALUES( '$postContent',";
            if(!empty($usrIP)) $que .= $usrIP.")";
            else $que .= "NULL)";

myQuery($connBoards,$que);
        }
        //$redirect = $_SERVER['REQUEST_URI'];
        $redirect = 'postSuccess.php?page=' . $_GET['page'];
        echo 
        "<form action=" . $redirect. " method=\"post\">
            Title: <input type=\"text\" name=\"title\" class=\"tit\" size=\"65\"> 
            <input type=\"submit\" value=\"Post\"> <br>
            Message: <br>
                <textarea name=\"content\" rows=\"6\" cols=\"100\" ></textarea>
            <br>
        </form> "; 
    }
?>
