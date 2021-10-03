<?php
    include_once("../adminPower/login.php");
    $pagetoGo = "frontpage.php?page=";
    echo "<a href= \"".$pagetoGo."news\">News</a> &#183 ";
    //echo "<a href= \"".$pagetoGo."blog\">Blog</a>";
    echo "<br>";

    echo "Main Boards:<br>";
    $que = "SELECT boardName FROM boards WHERE typeOfBoard=\"main\"";
    getBoard($que);
    /* -----------------------------------------------------------*/
    echo "Shit Boards:<br>";
    $que = "SELECT boardName FROM boards WHERE typeOfBoard=\"shit\"";
    getBoard($que);

    function getBoard($boardquery){
        global $conn,$pagetoGo;
        $res = $conn->query($boardquery);
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $bname = $row["boardName"];
                echo "<a href= \"".$pagetoGo. $bname."\">/". $bname. "/</a> &#183 ";
            }
            echo "<br>";
        }
    }
?>
