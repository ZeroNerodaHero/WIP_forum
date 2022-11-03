<?php
    include_once("../../adminPowerV2/login.php");

    echo "{";
    $first = 1;
    $que = "SELECT * FROM emotes";
    $res = $conn->query($que);
    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            if($first){
                $first = 0;
            } else{
                echo ",";
            }
            $emotePATH = $row["filePATH"];
            $emoteShortHand =$row["shortHand"];
            echo '"'.$emoteShortHand.'":"'.$emotePATH.'"';
        }
    }
    echo "}";
?>
