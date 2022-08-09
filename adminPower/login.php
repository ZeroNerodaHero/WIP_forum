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
            $que = "INSERT INTO usrList(usrId,lastPost) 
                    VALUES($usrId,'0000-00-00 00:00:00')";
            myQuery($conn,$que);
            setcookie("usrId",$usrId,time()+(86400 * 365 * 5),"/php");
            return $usrId;
        } 
        //i initially threw away that first return $usrId uptop
        //but PHP believes someshit about not being able to find the cookie
        //dont know why but above wroks
        return $_COOKIE["usrId"];
    } 

    function checkBan($usr_IP,$print=false){
        global $conn;
        $curTime = date("m/d/Y H:i",time());
        
        //dev
        $que = "SELECT * FROM ipBans WHERE ip = '$usr_IP'";
        $res = $conn->query($que); 

        $echoStr = "";
        $ret = false;

        if(!empty($res) && $res->num_rows != 0){
            while($row = $res->fetch_assoc()){
                $echoStr .= "<br>START: ".$curTime. " <br> EXPIRES: " . $row["expire"] . "<br>";
                $echoStr .= "Reason for ban: " . $row["reason"] . "<br>";
                if($curTime > $row['expire']){
                    $echoStr .= "BAN HAS EXPIRED AND BEEN LIFTED<br>";
                    $que = "DELETE FROM ipBans WHERE ip='$usr_IP'";
                    myQuery($conn,$que);
                } else{
                    $ret = true;
                }
            }
        } 
        if($print) echo $echoStr;
        return $ret;
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

    //why did i combine the two?
    function updateUsrScore($usr_ID,$cnt){
        global $conn;

        $que = "UPDATE usrList SET totalPoints=totalPoints+'$cnt'
                WHERE usrId=".$usr_ID;
        myQuery($conn,$que);
    }
    function updateUsrTime($usr_ID){
        global $conn;
        $que = "UPDATE usrList SET lastPost=CURRENT_TIMESTAMP
                WHERE usrId=".$usr_ID;
        myQuery($conn,$que);
    }
    function generalUsrUpdate(){
        $usr_ID = getUsrID();

        updateUsrScore($usr_ID,10);
        updateUsrTime($usr_ID);
    }

    function usrCanPost($usr_ID){
        global $conn;
        $que = "SELECT lastPost,totalPoints FROM usrList WHERE usrId=".$usr_ID;

        $res = $conn->query($que);
        $lastPostTime = NULL;
        $totalPoints=0;
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $lastPostTime = $row["lastPost"]; 
                $totalPoints = $row["totalPoints"];
            }
        }
        if($lastPostTime == NULL) return NULL;
        //$newTime = max((750-$totalPoints)/10,0);
        $newTime = 0;
        $lastTimeObj = date_create($lastPostTime);
        $lastTimeObj->add(new DateInterval("PT".$newTime."S"));
        $curDate = date_create();

        if($lastTimeObj <= $curDate) return NULL;
        $tmp = date_diff($lastTimeObj,$curDate);
        return $tmp->format("%i minutes and %s seconds");
    }

    function echoImg($src,$toGo,$imgClass=''){
        echo "<a href='$toGo'>
            <img src='$src' class='$imgClass'> </a>";
    }

    //use after post gets updated. not before. retard
    //also serves as a time updater
    function updatePostCnt($board,$TID){
        global $connBoards;
        $que = "SELECT postCnt FROM $board"."Threads WHERE threadId=".$TID;
        $res = $connBoards->query($que);
        $newPostCnt = -1;
        if($res->num_rows > 0){ while($row = $res->fetch_assoc()){
            $newPostCnt = $row["postCnt"];
        }}
        $newPostCnt++;

        $que = "UPDATE $board"."Threads
                SET postCnt = $newPostCnt, time=CURRENT_TIMESTAMP
                WHERE threadId = $TID";
        myQuery($connBoards,$que);
    }

    function getUsrSafeHash($UID,$TID,$boardHash){
        return (($UID%1000000)^($TID^($TID<<16))^$boardHash);
    }
    function timeRegFormat($time){
        $phpdate = strtotime( $time);
        $mysqldate = date( 'n/d/y-H:i', $phpdate );
        return $mysqldate;
    }
?>
