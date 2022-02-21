<?php

if(0){
error_reporting(-1);
ini_set('display_errors',1);
}

    include_once("commWserver.php");

    $data = "peepo";
    $conn = mysqli_connect($servername,$user,$pass,$data) 
        or die("can't connect to mySql");

    $boardData = "peepoPost";
    $connBoards = mysqli_connect($servername,$user,$pass,$boardData) 
        or die("can't connect to mySql");

    function myQuery($mcon,$que,$msg=""){
        if($mcon->query($que)){
            //echo "successfully added " .$msg;
        } else{
            echo "failed to add " . $que;
            echo "<br>";
        }
    }

    //this techinically is usr id but eh what am i to sya
    //becareful. u can only call this before html bc W3 says 
    //so and im not about to commit a crime
    function getUsrID(){
        global $conn;
        if(!isset($_COOKIE["usrId"])){
            /*
             * beacuse the number is so large i don't think
             * this is needed. no collisions should happen here
            while(1){
                $usrId = rand() << 32 | rand();
                $que = "SELECT * FROM usrList WHERE usrId =".$usrId;
                $res = $conn-query($que);

                if(empty($res) || $res->num_rows == 0) break;
            }
             */
            $usrId = rand() << 32 | rand();
            $que = "INSERT INTO usrList(usrId) VALUES($usrId)";
            myQuery($conn,$que);
            setcookie("usrId",$usrId,time()+(86400 * 365 * 5),"/");
            return $usrId;
        } 
        //i initially threw away that first return $usrId uptop
        //but PHP believes someshit about not being able to find the cookie
        //dont know why but above wroks
        return $_COOKIE["usrId"];
    } 

    function checkBan($usr_IP){
        global $conn;
        $curTime = date("Y-m-d H:i:s",time());
        
        //dev
        $que = "SELECT * FROM ipBans WHERE ip = '$usr_IP'";
        $res = $conn->query($que); 

        if(!empty($res) && $res->num_rows != 0){
            while($row = $res->fetch_assoc()){
                echo $curTime. " ::::: " . $row["expire"] . "<br>";
                echo "Reason for ban: " . $row["reason"] . "<br>";
                if($curTime > $row['expire']){
                    echo "BAN HAS BEEN LIFTED<br>";
                    $que = "DELETE FROM ipBans WHERE ip='$usr_IP'";
                    myQuery($conn,$que);
                    return false;
                } else{
                    echo "banned checkBan<br>";
                    return true;
                }
            }
        } 
        return false;
    }

    function banUsr($usr_ID,$reason,$expire_time){
        global $conn;
        $que = "INSERT INTO ipBans(ip,reason,expire)
                VALUES ('$usr_ID','$reason',
                ADDTIME(CURRENT_TIMESTAMP,'$expire_time'))";
        myQuery($conn,$que); 
        updateUsrScore($usr_ID,-100);
    }

    function isBadWord($word){
        global $conn;

        $que = "SELECT * FROM badWord WHERE word='$word'";
        $res = $conn->query($que);

        return !empty($res) && $res->num_rows != 0; 
    }

    function updateUsrScore($usr_ID,$cnt){
        global $conn;

        $que = "UPDATE usrList SET totalPoints=totalPoints+'$cnt'
                WHERE usrId=".$usr_ID;
        myQuery($conn,$que);
    }

    function updateUsrTime($usr_ID){
        global $conn;
        $que = "UPDATE usrList SET lastPost=CURRENT_TIME
                WHERE usrId=".$usr_ID;
        myQuery($conn,$que);
    }

    function echoImg($src,$toGo,$imgClass=''){
        echo "<a href='$toGo'>
            <img src='$src' class='$imgClass'> </a>";
    }

    //use after post gets updated. not before. retard
    //also serves as a time updater
    function updatePostCnt($board,$TID){
        global $connBoards;
        $que = "SELECT * FROM $board"."_".$TID;
        $res = $connBoards->query($que);

        $newPostCnt = $res->num_rows;
        $que = "UPDATE $board"."Threads
                SET postCnt = $newPostCnt
                WHERE threadId = $TID";
        myQuery($connBoards,$que);
    }
?>
