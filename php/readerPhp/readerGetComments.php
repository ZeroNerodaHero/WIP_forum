<?php
    header('Content-Type:text/xml');
    include_once("../../adminPower/login.php");

    $source = $_GET["board"]."_".$_GET["TID"]."_comments";
    $que = "SELECT * FROM ".$source;

    $res = $connBoards->query($que);
    echo "<ALL>";
    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            $UID = getUsrSafeHash($row["userID"],$_GET["TID"],ord($_GET["board"][0]));
            $fTime = $row["time"];
            echo "<encap>";
            echo "<postId>".$row["postId"]."</postId>";
            echo "<userID>".$UID."</userID>";
            echo "<sx>".$row["sx"]."</sx>";
            echo "<sy>".$row["sy"]."</sy>";
            echo "<ex>".$row["ex"]."</ex>";
            echo "<ey>".$row["ey"]."</ey>";
            echo "<comment>".$row["comment"]."</comment>";
            //echo "<time>".$row["time"]."</time>";
            echo "<time>".timeRegFormat($fTime)."</time>";
            echo "</encap>";
        }
    }
    echo "</ALL>";
?>
