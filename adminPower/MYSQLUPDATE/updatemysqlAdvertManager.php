<?php 
    include_once("../login.php");
    $que = "DESCRIBE advertManager";
    $res = $conn->query($que);
    if($res == FALSE){
        $que = "CREATE TABLE advertManager(
                username varchar(200) UNIQUE,
                password varchar(200),
                email varchar(200) UNIQUE,
                verified boolean NOT NULL DEFAULT FALSE,
                userId bigInt(20) UNIQUE,
                credits int NOT NULL DEFAULT 0,
                transactionHistory mediumText NOT NULL DEFAULT '',
                messages tinyText NOT NULL DEFAULT '',
                lastSessionId int,
                lastTime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
        echo $que;
        myQuery($conn,$que);
    }
?>


