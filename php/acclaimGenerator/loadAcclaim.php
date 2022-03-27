<?php
    include_once("../../adminPower/login.php");

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
        echo "<span id=usrPointCount> Points: ".$totalPoints . "</span> | ";
        $totalEmotes = 3;
        for($i = 0; $i < $totalEmotes; $i++){
            echo "<img src='../../res/emotes/emote_$i.png' class=threadEmote id=threadEmote_$i
                  onmouseover=expandEmote(this) onmouseout=deflateEmote(this)
                  onclick=addEmote(this,'$board',$TID,$i)>";
        }
    }
?>
