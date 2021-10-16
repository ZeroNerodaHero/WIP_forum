<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../css/mainstyle.css">
        <link rel="icon" href="../../res/icon/icon_0.png">
        <title>PeePo</title>
    </head>

    <div class = "PageHeader">
        <?php 
            error_reporting(-1);
            ini_set('display_errors',1); 
            include_once('generateHeader.php');
        ?>
        
    </div>

    <div class = "wrap">
        <div class = "board">
            <?php
                error_reporting(-1);
                ini_set('display_errors',1); 
                
                include_once('generatePage.php');
                generatePage();
            ?>

            <footer>
                <p> 
                    Thats all the content. <br>
                    Have a good day. :))))
                </p>
            </footer>
        </div>

        <div class = "nav">
            <?php
                include_once('generateNav.php');
            ?>
        </div>  

        <div class = "advert">
            <img src="../res/bulletin/bull_0.png" class="advertImg">
        </div>

    </div>
</html>
