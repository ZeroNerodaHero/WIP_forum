<?php
    if($boardPageName == "news"){
        //make a global recent?
    } else if(!empty($_GET["TID"])){
        //is there a way so i only need 1 call to the database?
        $recentLnkCnt = 10;

        //this is very messy calling structure. I don't like it 
        //but lets let it go fro now

        foreach($boardThreads as $row){
            $recentRed = "?page=$boardPageName&TID=".$row["threadId"];
            $displayTime = "[".date("m/d-H:i",strtotime($row["time"])) . "] ";
            $displayTxt = $displayTime.$row["title"];
            echo "<div class=watchingLnk><a href='$recentRed'>"
                .$displayTxt . "</a></div>";

            if($recentLnkCnt > 0) $recentLnkCnt--;
            else break;
        }
    }
?>
