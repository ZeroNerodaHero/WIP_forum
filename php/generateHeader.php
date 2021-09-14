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
        $TID=$_GET["TID"];
        if(!empty($TID)){
            echo "<div class=headTitle>'$pageTitle'</div>";
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
