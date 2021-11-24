<?php
    echo "<div id=navHeader> Navigation </div>";
    include_once("../adminPower/login.php");
    $pagetoGo = "frontpage.php?page=";
    echo "<a href= \"".$pagetoGo. "news\">
          <div id=NewsNavLink>[ News ]</div> </a>";
    echo "Main Boards:";
    $que = "SELECT boardName FROM boards WHERE typeOfBoard=\"main\"";
    getBoard($que);
    /* -----------------------------------------------------------*/
    echo "Shit Boards:";
    $que = "SELECT boardName FROM boards WHERE typeOfBoard=\"shit\"";
    getBoard($que);

    function getBoard($boardquery){
        global $conn,$pagetoGo;
        $res = $conn->query($boardquery);
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $bname = $row["boardName"];
                echo "<a href= \"".$pagetoGo. $bname."\">
                    <div class=navLink>[ " . $bname.  "]</div> </a>";
            }
        }
    }
?>
