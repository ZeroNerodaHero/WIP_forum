<?php
    include_once("../../adminPower/login.php");
    include_once("../postSuccess/postFunc.php");

    $sx = $_POST["sx"]; $sy = $_POST["sy"]; $ex = $_POST["ex"]; $ey = $_POST["ey"];
    $comment = $_POST["comment"];
    if(!empty($sx) && !empty($sy) && !empty($ex) && !empty($ey) &&
        !empty($comment) && !empty($_POST["board"]) && !empty($_POST["TID"])){

        $board = $_POST["board"];
        $TID= $_POST["TID"];

        if(!testString($comment,3000)){
            echo "COMMENT IS TOO LONG";
        } else if(!textVerify($comment)){
            echo "U SAID SOMETHING BAD!!!";
        } else{
            //use both post and get?
            $usrId = getUsrID();

            //do default checks
            $tableName = $board."_".$TID."_comments";
            $que = "INSERT INTO $tableName(userId,sx,sy,ex,ey,comment) 
                    VALUES($usrId,$sx,$sy,$ex,$ey,'$comment')";
            echo "POST SUCCESS";
            //echo "$que";
            myQuery($connBoards,$que);
            updatePostCnt($board,$TID);
            generalUsrUpdate();
        }
    } else{
        echo "EMPTY";
    }

    //do reload page or just do a semi-update to lower canvas?
    //full reload faster and solve the porblem of concurrent users
    //updating
?>
