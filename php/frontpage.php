<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../css/mainstyle.css">
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
            <p>
            This is where the advert is at
            </p>
        </div>

    </div>
</html>
