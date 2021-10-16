<?php
    include_once("../adminPower/login.php");
    $pageTitle = $_GET["page"];
    if(empty($pageTitle)){
        echo "<div class=headTitle>WELCOME TO PEEPO</div>";
    } else if($pageTitle == "news"){
        echo "<div class=headTitle>WELCOME TO PEEPO</div><br>";
        echo "<div class=headText>News</div>";
    } else if($pageTitle == "blog"){
        echo "<div class=headTitle>WELCOME TO PEEPO</div><br>";
        echo "<div class=headText>Blog</div>";
    } else{
        if(!empty($_GET["TID"])){
            $TID=$_GET["TID"];

            $que = "SELECT title FROM ".$pageTitle."Threads where threadId='$TID'";
            $res = $connBoards->query($que);

            if(!empty($res) && $res->num_rows > 0){
                while($row = $res->fetch_assoc()){
                    echo "<div class=headTitle>".$row["title"]." </div>";
                }
            }

            $pageTitle = '<a href="frontpage.php?page='.$pageTitle.'">'.$pageTitle.'</a>';
            echo "<div>/".$pageTitle."/</div>";
        } else{
            echo "<div class=headTitle>/".$pageTitle."/</div>";
        }
    }
    /*
     * i want to make it look like this
     * shit
     * name of thread
    */
?>
