<?php
    echo "<div id=recLinkCont>";
    if($boardPageName == "news"){
        //make a global recent?
    } else if(!empty($_GET["TID"])){
        //is there a way so i only need 1 call to the database?
        $recentLnkCnt = 10;
        foreach($boardThreads as $row){
            $recentRed = "?page=$boardPageName&TID=".$row["threadId"];
            $displayTime = "[".date("m/d-H:i",strtotime($row["time"])) . "] ";
            $displayTxt = $displayTime.$row["title"];
            echo "<div class=recentLnk><a href='$recentRed'>"
                .$displayTxt . "</a></div>";

            if($recentLnkCnt > 0) $recentLnkCnt--;
            else break;
        }
    }
    echo "</div>";
?>
