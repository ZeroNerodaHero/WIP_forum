<?php
    include_once("../../adminPowerV2/login.php");

    $json = $_POST["json"];
    //true here to make it associative array
    $decoded_json = json_decode($json, true);

    foreach($decoded_json as $board => $watchedThreads) {
        $isFirst = true;
        $que = "SELECT threadId, postCnt FROM ".$board."Threads WHERE threadId=";
        foreach($watchedThreads as $threadId => $datas){
            $que .= ($isFirst ? "":" or threadId="). $threadId;
            $isFirst = false;
        }
        $res = $connBoards->query($que);

        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $tid = $row["threadId"];
                $pCnt = $row["postCnt"];
                $decoded_json[$board][$tid][1] = intVal($pCnt);
            }
        } 
    }
    echo json_encode($decoded_json);
?>
