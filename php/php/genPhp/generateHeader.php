<?php
    $pageTitle = (empty($_GET["page"])) ? "news" : $_GET["page"];
    if($pageTitle == "news"){
        echo "<div class=headTitle>WELCOME TO FUNCEL.XYZ</div>";
        echo "<div class=headText>News</div>";
    } else{
        if(!empty($_GET["TID"])){
            echo "<div class=headTitle>".$threadTitle." </div>";
            echo "<script type='text/javascript'>headerRedirect('$pageTitle')</script>";
            $pageTitle = '<a href="?page='.$pageTitle.'">'.$pageTitle.'</a>';
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

