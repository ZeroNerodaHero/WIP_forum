<?php
    include_once("../../adminPowerV2/login.php");
    include_once("../postSuccess/postFunc.php");

    $sx = $_POST["sx"]; $sy = $_POST["sy"]; $ex = $_POST["ex"]; $ey = $_POST["ey"];
    $comment = $_POST["comment"];
    if(!empty($sx) && !empty($sy) && !empty($ex) && !empty($ey) &&
        $sx != $ex && $sy != $ey &&
        !empty($comment) && !empty($_POST["board"]) && !empty($_POST["TID"])){

        $board = $_POST["board"];
        $TID= $_POST["TID"];

        $errorChecker = newPostChecker($comment);
        $comment = str_replace("<","&lt;",$comment);
        $comment= str_replace(">","&gt;",$comment);
        $comment= str_replace("\n","<br>",$comment);
        //$comment= nl2br($comment);
        //echo $comment;
        $comment = addSlashes($comment);


        if($errorChecker != 0){
            echo '{"returnCode":0,"msg":"';
            echo errorDisplay($errorChecker);
            echo '"}';
        } else{
            //use both post and get?
            $usrId = getUsrID();

            //do default checks
            $tableName = $board."_".$TID."_comments";
            $que = "INSERT INTO $tableName(userId,sx,sy,ex,ey,comment) 
                    VALUES($usrId,$sx,$sy,$ex,$ey,'$comment')";
            echo '{"returnCode":1,"msg":"POST SUCCESS"}';
            //echo "$que";
            myQuery($connBoards,$que);
            updatePostCnt($board,$TID);
            generalUsrUpdate();
        }
    } else{
        echo '{"returnCode":0,"msg":"POST FAILED"}';
    }

    //do reload page or just do a semi-update to lower canvas?
    //full reload faster and solve the porblem of concurrent users
    //updating
?>
