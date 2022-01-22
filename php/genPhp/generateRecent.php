<?php
    echo "<div id=recLinkCont>";
    $cPage = "news";
    if(!empty($_GET["page"])) $cPage = $_GET["page"];

    if($cPage == "news"){
        //make a global recent?
    } else if(!empty($_GET["TID"])){
        //is there a way so i only need 1 call to the database?
        $que = "SELECT * FROM ".$cPage."Threads ORDER BY time DESC LIMIT 10";
        
        $res = $connBoards->query($que);
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $recentRed = "?page=$page&TID=".$row["threadId"];
                $displayTime = "[".date("m/d-H:i",strtotime($row["time"])) . "] ";
                $displayTxt = $displayTime.$row["title"];
                echo "<div class=recentLnk><a href='$recentRed'>"
                    .$displayTxt . "</a></div>";
            }
        }
    }
    echo "</div>";
?>
