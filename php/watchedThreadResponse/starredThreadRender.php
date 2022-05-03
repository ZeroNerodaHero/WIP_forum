<?php
    include_once("../../adminPower/login.php");

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

        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $tid = $row["threadId"];
                $title = $row["title"];
                $lastView = $decoded_json[$board][$tid][0];
                $newView = $decoded_json[$board][$tid][1];
                $diff = $newView - $lastView;
                $newColor = ($diff > 0) ? "#30ff30":"red";

                $exitStr .= "<div class=watchingLnk>($lastView)".
                            "<span style='color:$newColor'>[$diff]</span>".
                            "<a href='?page=$board&TID=$tid' >".
                            "$title</a></div>";
            }
        } 
    }
    echo $exitStr;
?>
