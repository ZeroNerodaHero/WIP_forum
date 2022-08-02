<?php 
    include_once("../login.php");
    $que = "DESCRIBE 'advertManger'";
    $res = $conn->query($que);
    if($res == FALSE){
        $que = "CREATE TABLE advertManager(
                username varchar(200),
                password varchar(200),
                email varchar(200),
                userId bigInt(20),
                credits long NOT NULL DEFAULT 0,
                transactionHistory mediumText NOT NULL DEFAULT '',
                lastTime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
        echo $que;
        myQuery($conn,$que);
    }
?>


