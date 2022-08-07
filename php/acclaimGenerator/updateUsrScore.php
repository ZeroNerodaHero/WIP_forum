<?php
    include_once("../../adminPowerV2/login.php");

    $usrId = getUsrID();
    $totalPoints = 0;
    $que = "SELECT totalPoints FROM usrList WHERE usrId=$usrId";
    $res = $conn->query($que);
    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            $totalPoints = $row["totalPoints"];
        }
    }
    echo $totalPoints;
?>
