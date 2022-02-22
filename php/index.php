<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../css/mainstyle.css">
        <link rel="icon" href="../res/icon/icon_0.png">
        <script type="text/javascript" src="../css/jscrap.js"></script>
        <script type="text/javascript" src="../css/cookieSetting.js"></script>
        <script type="text/javascript" src="../css/lastView.js"></script>

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
            <div class=sideBarElement id="nav">
	        <div class=sideBarHeader id=navHeader>
		    <span class=sideBarTitle id=navHeaderTit>Navigation</span> 
		    <span class=sideBarCol id=navHeaderCol>
		    <a href="javascript:toggleNav()" id=navCollapseText class="chevron"> </a>
		    </span> 
	        </div>
	        <div class=sideBarContainer id=navContainer>
                    <?php include_once('genPhp/generateNav.php'); ?>
		    <script>toggleNav(0);</script>
		</div>
            </div>  

            <div class=sideBarElement id=recentThread>
	        <div class=sideBarHeader id=recentThreadHeader>
                    <span class=sideBarTitle id=recentThreadTitle>
                        Recent Thread
                    </span> 
		    <span class=sideBarCol id=recentThreadCol>
                        <a href="javascript:toggleRecent()" 
                        id=recentThreadCollapseText class="chevron"> </a>
		    </span> 
	        </div>
	        <div class=sideBarContainer id=recentThreadContainer>
                    <?php
                        include_once('genPhp/generateRecent.php'); 
                        //this is not a great way but yea
                        if(!empty($_GET["page"]) && !empty($_GET["TID"]))
                            echo "<script> recentAnimateAll(); </script>";
                        else
                            echo "<script> closeRecent(); </script>";
                    ?>

                    <script>
                        toggleRecent(0);
                    </script>
		</div>
            </div>  

            <div class = "advert">
	        <?php include_once('genPhp/generateAdvert.php'); ?>
            </div>
        </div>
    </div>

    <!--for variables and stuff -->
    <?php
        if($typeOfPage == 1){
            echo "<script>
                var totalContent=".sizeof($threadContent)."
                updateLastThread('$boardPageName',$threadID,totalContent);
                </script>";
        } else {
            echo "<script>iterateCnter('$boardPageName')</script>";
        }
    ?>
    <script>
        expandImg() 
    </script>
</html>
