<?php
    include_once("../../adminPower/login.php"); 

    $limit = (empty($_GET["limit"]) ? 10 : $_GET["limit"]);
    if(!empty($_GET["board"])){
        $board = $_GET["board"];
        $que = "SELECT threadId,time,title FROM ".$board."Threads".
               " ORDER BY time DESC LIMIT ".$limit;
        $res = $connBoards->query($que);

        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $recentRed = "?page=$board&TID=".$row["threadId"];
                $displayTime = "[".timeRegFormat($row["time"]) . "] ";
                $displayTxt = $displayTime.$row["title"];
                echo "<div class=watchingLnk><a href='$recentRed'>"
                    .$displayTxt . "</a></div>";

            }
        }
    }
?>
