<?php
    include_once("../../adminPowerV2/login.php");

    $json = $_POST["json"];
    //true here to make it associative array
    $decoded_json = json_decode($json, true);
    $exitStr = "";
    foreach($decoded_json as $board => $watchedThreads) {
        $isFirst = true;
        $que = "SELECT threadId,title FROM ".$board."Threads WHERE threadId=";
        foreach($watchedThreads as $threadId => $datas){
            $que .= ($isFirst ? "":" or threadId="). $threadId;
            $isFirst = false;
        }

        $res = $connBoards->query($que);
        $exitStr .= "<div class=starTitle>[$board]</div>".
                    "<span class=clearStar>".
                    "<a href='javascript:clearStar(\"$board\")'>Clear</a></span>";
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $tid = $row["threadId"];
                $title = $row["title"];
                $lastView = $decoded_json[$board][$tid][0];
                $newView = $decoded_json[$board][$tid][1];
                $diff = $newView - $lastView;
                $newColor = ($diff > 0) ? "#30ff30":"#7f0000";
                $exitStr .= "<div class=watchingLnk>".
                            "<a href='?page=$board&TID=$tid'
                              style='color:$newColor' title='+$diff comments'>".
                            "($lastView)[$diff]$title</a></div>";
            }
        } 
    }
    if($exitStr == ""){
        $exitStr = "<div class=noStarThreads>Starring a thread is a feature 
                    that allows you to keep track of the threads</div>";
    }
    echo $exitStr;
?>
