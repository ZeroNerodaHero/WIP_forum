<?php
    $cPage = "news";
    if(!empty($_GET["page"])) $cPage = $_GET["page"];


    if($cPage == "news"){
        echo "<div class=NewsNavLink id=navActLink>[News]</div>";
    } else{
    	echo " <div class=NewsNavLink>
            <a href='?page=news' class=navLink>[News]</a></div>";
    }
    echo "<div class=navCategory id=mainNavList> Main Boards: 
        [<a href=javascript:navListClick('main') id=mainNavToggle></a>]</div>";
    getBoard("main",$cPage);
    /* -----------------------------------------------------------*/

    echo "<div class=navCategory id=shitNavList> Shit Boards: 
        [<a href=javascript:navListClick('shit') id=shitNavToggle></a>]</div>";
    getBoard("shit",$cPage);
    /* -----------------------------------------------------------*/

    function getBoard($boardType,$cPage){
        global $allBoards;
    	echo "<div class=navFlexCont id=".$boardType."flexList>";

        $pagetoGo = "?page=";
        foreach($allBoards as $row){
            if($row["typeOfBoard"] != $boardType) continue;
            $bname = $row["boardName"];
	    if($bname == $cPage){
                echo "<div class=navLinkCont id=navActLink>[" . $bname.  "]</div>";
	        continue;
            }
            echo " <div class=navLinkCont>
	        <a href='?page=$bname' class=navLink> [" . $bname.  "]
	        </a></div>";
        }
    	echo "</div>";
    }
?>
