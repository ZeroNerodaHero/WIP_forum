<?php
    $pointsForEmote = 100;
    include_once("../../adminPowerV2/login.php");
    include_once("listAcclaim_func.php");
    $usrId = getUsrID();
    $aboard = $_GET['board'];
    $aTID = $_GET['TID'];
    $opt = $_GET['opt'];
    $acclaimStr = "";
    $oldTime = 0;
    $totalPoints = 0;

    $emoteQUE = "SELECT * FROM emotes";
    $emoteRES = $conn->query($emoteQUE);
    $emoteList = array("NULL");
    if($emoteRES->num_rows > 0){
        while($row = $emoteRES->fetch_assoc()){
            $emoteList[] = $row["filePATH"];
        }
    }

    $que = "SELECT totalPoints FROM usrList WHERE usrId=$usrId";
    $res = $conn->query($que);
    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            $totalPoints = $row["totalPoints"];
        }
    } 
    $pointsForEmote = 100;
    if($totalPoints > $pointsForEmote){
        //get old string
        //update part of the string
        //replace
        //update usr score
        //done
         
        $que = "SELECT acclaim,time FROM ".$aboard."Threads
                WHERE threadId = $aTID";
        $res = $connBoards->query($que);
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $acclaimStr = $row["acclaim"];
                $oldTime = $row["time"];
            }
        } 
        $length = strlen($acclaimStr);

        $i = 0;
        while($i < $length){
            $key=0;$value = 0;
            while($i < $length && $acclaimStr[$i] != ":"){
                $key = $key*10 + (int)$acclaimStr[$i];
                $i++;
            } $i++;
            while($i < $length && $acclaimStr[$i] != ","){
                $value = $value*10 + (int)$acclaimStr[$i];
                $i++;
            } $i++;
            if($key == $opt){
                $acclaimStr = str_replace($key.":".$value.",",
                                          $key.":".($value+1).",",
                                          $acclaimStr);
                //if i don't use key++, what happens is that it renders it twice;
                $i++;
                break; 
            }
        } 
        if($i == $length){
            $acclaimStr .= $opt.":1,";
        }

        if($acclaimStr != ""){
            $que = "UPDATE ".$aboard."Threads 
                    SET acclaim ='$acclaimStr',
                        time='$oldTime'
                    WHERE threadId=$aTID";
            myQuery($connBoards,$que);

            $que = "UPDATE usrList 
                    SET totalPoints=".($totalPoints-100)."
                    WHERE usrId=$usrId";
            myQuery($conn,$que);

            genAcclaim($acclaimStr,$emoteList);
        }
    } else{
        echo "0";
    }
?>
