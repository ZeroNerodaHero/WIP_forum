<?php
    include_once("../../adminPower/login.php");
    $connTest = mysqli_connect($servername,$user,$pass,"testDB") 
        or die("can't connect to mySql");

    //use both post and get?
    $usrId = getUsrID();
    $sx = $_POST["sx"]; $sy = $_POST["sy"]; $ex = $_POST["ex"]; $ey = $_POST["ey"];
    $comment = $_POST["comment"];

    //do default checks
    $tableName = "imgRenderTest";
    $que = "INSERT INTO $tableName(userId,sx,sy,ex,ey,comment) 
            VALUES($usrId,$sx,$sy,$ex,$ey,'$comment')";
    echo $que;
    $connTest->query($que);

    //do reload page or just do a semi-update to lower canvas?
    //full reload faster and solve the porblem of concurrent users
    //updating
?>
