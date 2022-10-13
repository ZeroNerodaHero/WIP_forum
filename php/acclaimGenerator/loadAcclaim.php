<?php
    include_once("../../adminPowerV2/login.php");

    $usrId = getUsrID();
    $board = $_GET['board'];
    $TID = $_GET['TID'];

    loadUsrAcclaim($usrId,$board,$TID);

    function loadUsrAcclaim($usrId,$board,$TID){
        global $conn;
        $totalPoints = 0;
        $que = "SELECT totalPoints FROM usrList WHERE usrId=$usrId";
        $res = $conn->query($que);
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $totalPoints = $row["totalPoints"];
            }
        }
        echo "Points: <span id=usrPointCount>".$totalPoints . "</span> | ";

        $que = "SELECT * FROM emotes";
        $res = $conn->query($que);
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $emotePATH = $row["filePATH"];
                $id=$row["id"];
                echo "<img src='../../res/emotes/$emotePATH' class=threadEmote id=threadEmote_$id
                      onmouseover=expandEmote(this) onmouseout=deflateEmote(this)
                      onclick=addEmote(this,'$board',$TID,$id)>";
            }
        }
    }
?>
