<?php
    include_once("login.php");

    $cboard = $_GET["board"];
    $TID = $_GET["TID"];
    $redirect = "deleteStuff.php?board=".$cboard;
    $editTable = $cboard."_".$TID;

    if(empty($_POST["pword"]) || $_POST["pword"] != $mod_ppassword){
        echo "LMAO TRY AGAIN U CAN'T DELETE STUFF, but plz stop";
    }
    else if(!empty($_GET["PID"])){
        //delete Post only
        $que = "DELETE FROM ".$editTable." WHERE postId=".$_GET["PID"];
        myQuery($connBoards,$que);

        //check if empty
        $que = "SELECT * FROM ".$editTable." LIMIT 1";
        $res = $connBoards->query($que);
        if(empty($res) || $res->num_rows==0){
            deleteTbl($connBoards,$TID,$cboard,$editTable);
        } else{
            $redirect .= "&TID=".$_GET["TID"];
        }
    } else if(!empty($_GET["TID"])){
        //delete whole thread
        deleteTbl($connBoards,$TID,$cboard,$editTable);
    }

    function deleteTbl($connBoards,$TID,$cboard,$editTable){
        $que = "DROP TABLE ".$editTable;
        myQuery($connBoards,$que);
        $que = "DELETE FROM ".$cboard."Threads WHERE threadId=".$TID;
        myQuery($connBoards,$que);
    }

    echo "redirecting to <a href='$redirect'> ... </a>";
    echo '
        <script>
        setTimeout(function(){
        location="'.$redirect.'";
        }, 5000);
        </script> ';

?>
