<?php
    include_once("login.php");

    $cboard = $_GET["board"];
    $redirect = "deleteStuff.php?board=".$cboard;
    $editTable = $cboard."_".$_GET["TID"];

    if(empty($_POST["pword"]) || $_POST["pword"] != $mod_ppassword){
        echo "LMAO TRY AGAIN U CAN'T DELETE STUFF, but plz stop";
    }
    else if(!empty($_GET["PID"])){
        $redirect .= "&TID=".$_GET["TID"];
        //delete Post only
        $que = "DELETE FROM ".$editTable." WHERE postId=".$_GET["PID"];
        myQuery($connBoards,$que);
    } else if(!empty($_GET["TID"])){
        //delete whole thread
        $que = "DROP TABLE ".$editTable;
        myQuery($connBoards,$que);
        $que = "DELETE FROM ".$cboard."Threads WHERE threadId=".$_GET["TID"];
        echo $que . " <br>";
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
