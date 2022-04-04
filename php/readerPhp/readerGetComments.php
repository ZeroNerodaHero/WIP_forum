<?php
    header('Content-Type:text/xml');
    include_once("../../adminPower/login.php");
    $connTest = mysqli_connect($servername,$user,$pass,"testDB") 
        or die("can't connect to mySql");

    $source = "imgRenderTest";
    $que = "SELECT * FROM ".$source;

    $res = $connTest->query($que);
    echo "<ALL>";
    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            echo "<encap>";
            echo "<postId>".$row["postId"]."</postId>";
            echo "<userID>".$row["userID"]."</userID>";
            echo "<sx>".$row["sx"]."</sx>";
            echo "<sy>".$row["sy"]."</sy>";
            echo "<ex>".$row["ex"]."</ex>";
            echo "<ey>".$row["ey"]."</ey>";
            echo "<comment>".$row["comment"]."</comment>";
            echo "<time>".$row["time"]."</time>";
            echo "</encap>";
        }
    }
    echo "</ALL>";
 
?>
