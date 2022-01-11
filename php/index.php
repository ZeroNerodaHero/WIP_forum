<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../css/mainstyle.css">
        <link rel="icon" href="../res/icon/icon_0.png">
        <script type="text/javascript" src="../css/jscrap.js"></script>
        <script type="text/javascript" src="../css/cookieSetting.js"></script>

        <?php
            include_once('genPhp/generatePageHeader.php');
	?>

    </head>

    <div id = "PageHeader">
        <?php 
	/*
            error_reporting(-1);
            ini_set('display_errors',1); 
	*/
            error_reporting(-1);
            ini_set('display_errors',1); 
        include_once('genPhp/generateHeader.php');
        ?>
        
    </div>
    <div id = functionButtonCont>
        <a href="javascript:buttonUp()" class=funcButton>&uarr;</a>
        <br><br>
        <a href="javascript:buttonDown()" class=funcButton>&darr;</a> <br>
    </div> 
    <div class = "wrap">
	<div id="leftEncap">
	    <div id=boardHeader>
	        <?php
	        include_once("genPhp/generateBoardHeader.php");
		?>
	    </div>
            <div id = "board">
            	<?php
                include_once('genPhp/generatePage.php');
                generatePage();
            	?>
	    </div>

            <footer>
                <p> 
                    Thats all the content. <br>
                    Have a good day. :))))
                </p>
            </footer>
        </div>

	<div id="rightEncap">
            <div class = "nav">
	        <div id=navHeader>
		    <span id=navHeaderTit>Navigation</span> 
		    <span id=navHeaderCol>
		    <a href="javascript:toggleNav()" id=navCollapseText class="chevron"> </a>
		    </span> 

	        </div>
	        <div id=navContainer>
                    <?php include_once('genPhp/generateNav.php'); ?>
		    <script>toggleNav(0);</script>
		</div>
            </div>  

            <div class = "advert">
	        <?php include_once('genPhp/generateAdvert.php'); ?>
            </div>
        </div>

    </div>
</html>
