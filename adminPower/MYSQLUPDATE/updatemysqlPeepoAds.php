<?php 
    include_once("../login.php");
    $que = "DESCRIBE peepoAds";
    $res = $conn->query($que);
    if($res == FALSE){
        $que = "CREATE TABLE peepoAds(
            name varchar(255),
            id int NOT NULL AUTO_INCREMENT,
            uploaderId bigint,
            linkToImg varchar(1024) NOT NULL DEFAULT 'ERROR',
            linkToSite varchar(1024) NOT NULL DEFAULT 'ERROR',
            totalLoads int NOT NULL DEFAULT 0,
            totalClicks int NOT NULL DEFAULT 0,
            lowerText varchar(1024),
            maxPoints int NOT NULL DEFAULT 0,
            etcInfo tinyText,
            uploadTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            lastTime timestamp ON UPDATE CURRENT_TIMESTAMP,
            viewHistory mediumText,
            PRIMARY KEY(id))";
        echo $que;
        myQuery($conn,$que);
    }
?>


