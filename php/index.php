<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../css/mainstyle.css">
        <link rel="stylesheet" href="../css/reuseStyle.css">
        <link rel="stylesheet" href="../css/readerStyle.css">
        <link rel="icon" href="../res/icon/icon_0.png">
        <script type="text/javascript" src="../jscode/jscrap.js"></script>
        <script type="text/javascript" src="../jscode/cookieSetting.js"></script>
        <script type="text/javascript" src="../jscode/lastView.js"></script>
        <script type="text/javascript" src="../jscode/readerScript.js"></script>
        <script type="text/javascript" src="../jscode/starThread.js"></script>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>

        <?php
            include_once('genPhp/generatePageHeader.php');
	?>
    </head>

    <?php
        if($newUsr){
            include_once('genPhp/generateNewUserPrompt.php');
        }
    ?>
    <span id=topRowCont>
        <details class=topRowButton title="Settings">
            <summary>&#9881</summary>
            <div>
            Settings <br>
            Background Type: <br>
                <input type=radio id=bkgType_img name=bkgType value=Image onclick=chngBkgType(0)>
                    <label for=bkgType_img>Image</label>
                <input type=radio id=bkgType_color name=bkgType value=Color onclick=chngBkgType(1)>
                    <label for=bkgType_Color>Color</label>
                <br>
            Image Link: 
                <input type=submit class=topRowSubmit id=imgLnkSubmit 
                    value=x onclick=imgLnkButton()><br>
                <input type=text id=bkgImgLnk name=bkgImgLnk><br>
            Background Color: 
                <input type=submit class=topRowSubmit id=colorSubmit 
                    value=x onclick=colorButton()><br>
                <input type=text id=bkgColor name=bkgColor><br>
            Board Opacity: (WIP)<br>
                <input type=range id=board_opacity class=topRowSlider 
                    name=board_opacity min=0 max=100> <br> 
            Font Size(in rem): (WIP)<br>
                <input type=range id=fontSize class=topRowSlider
                    name=font_size min=0 max=4><br>
            </div>
        </details>
        <script>
        </script>
    </span>
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
		</div>
            </div>  

            <div class=sideBarElement id=watchThread>
	        <div class=sideBarHeader id=watchThreadHeader>
                    <span class=sideBarTitle id=watchThreadTitle>
                        Thread Watcher:
                    </span> 
		    <span class=sideBarCol id=watchThreadCol>
                        <a href="javascript:toggleRecent()" 
                        id=watchThreadCollapseText class="chevron"> </a>
		    </span> 
	        </div>
	        <div class=sideBarContainer id=watchThreadContainer>
                    <div id=watchingTypeSelector>
                        <?php
                        echo "<a href='javascript:starReload(1)'".
                            " class=watchSwitcher>Starred</a>";
                        if($typeOfPage== 1){
                        echo " | <a href='javascript:recentReload(\"$boardPageName\")'".
                            " class=watchSwitcher>Recent</a>";
                        }
                        ?>
                    </div>
                    <div id=recLinkCont>
                    <?php
                        include_once('genPhp/generateRecent.php'); 
                        //this is not a great way but yea
                    ?>
                    </div>

		</div>
            </div>  

            <div class = "advert">
	        <?php include_once('genPhp/generateAdvert.php'); ?>
            </div>
            <div id="miscLnk">
                <a class=contactGen href="miscPg/advertisment.html">
                    Advertisment
                </a>
            </div>
        </div>
    </div>

    <!--for variables and stuff -->
    <?php
        $pStr = "<script> 
                  var totalContent=".$threadContentSize.";";
        if($typeOfPage == 1){
            //thread view
            $pStr .= "watchMaster('$boardPageName',$threadID,totalContent,1);
                      updateLastThread('$boardPageName',$threadID,totalContent);";
        } else {
            //board
            $pStr .= "watchMaster('$boardPageName',$threadID,totalContent,0);
                      iterateCnter('$boardPageName');";
        }
        $pStr .= "recentAnimateAll();</script>";
        echo $pStr;

        if($hasOldLast){
            echo "<script>"; 
            foreach($allBoards as $it){
                $bName = $it["boardName"];
                echo "deleteOldLocalStorage('$bName');";
            }
                echo "deleteOldLocalStorage('news');";
            echo "</script>"; 
        } 
    ?>
    <script>
        generatePageStyle();
        expandImg();
    </script>
</html>
