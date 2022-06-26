<?php
    include_once("../../adminPower/login.php");

    $source = $_GET["board"]."_".$_GET["TID"]."_comments";
    $que = "SELECT * FROM ".$source;

    $res = $connBoards->query($que);
    $hasStuff = false;

    echo '{ "data":[';
    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            $UID = getUsrSafeHash($row["userID"],$_GET["TID"],ord($_GET["board"][0]));
            $fTime = $row["time"];
            if($hasStuff) echo ",";
            $commentStr = nl2br($row["comment"]);
            $responseStr = nl2br($row["responseStr"]);

            $responseStr = substr($responseStr,9,strlen($responseStr)-9-2);

            echo "[";
            echo $row["postId"].",".$UID.',"'.timeRegFormat($fTime).'",';
            echo "[".$row["sx"].",".$row["sy"].",".$row["ex"].",".$row["ey"]."],";
            echo '"'.$commentStr.'",['.$responseStr.'],'.$row["responseCnt"];
            echo "]";
            $hasStuff = true;
        }
    }
    echo ']}';
?>
