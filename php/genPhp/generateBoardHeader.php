<?php
    $reLnk = '/php';
    $navStr = "<a href='$reLnk' class=bheaderLnk>FUNCEL.XYZ</a>";
    //defined elsewhere
    //$pgN = 0;

    if(empty($_GET["TID"]) ){
        if(!empty($_GET["pgN"])) $pgN=$_GET["pgN"];

        //intdiv doesn't work bc servers have different versions
        $maxPg = ($boardPageName == "news" ? $maxNews : 
            (int)((count($boardThreads)+$threadsPerPage-1)/$threadsPerPage));
        $leftN=max($pgN-1,0);
        $rightN=min($pgN+1,$maxPg);

        $redUrl="?page=".$boardPageName."&pgN=";
        echo " <div id=pageNavCont> <span class=pageNavPart> Page: ".
            (($pgN != 0) ? 
	    "<a href='$redUrl"."0' class=pageNavLinkFar><<(First) </a>
	    | <a href='$redUrl".$leftN."' class=pageNavLinkNear>".($leftN+1)." </a>" :
            "<strike class=pageNavLinkFar> <<(First) </strike>" ). 
            "<span id=curPageNav> [".($pgN+1) . "] </span>".
	    "</span> <span class=pageNavPart>" .
	    (($pgN+1 < $maxPg) ? 
	    "<a href='$redUrl".$rightN."' class=pageNavLinkNear>".($rightN+1)." </a>
	    | <a href='$redUrl".($maxPg-1)."' class=pageNavLinkFar> (Last)>> </a>" :
	    "<strike class=pageNavLinkFar> (Last)>> </strike>").
	    "</span> </div> ";
    } 
    if(!empty($_GET["page"]) ){ 
        $pageN = $_GET["page"];
        $reLnk .= "?page=".$pageN;
        $navStr .= " > <a href='$reLnk' class=bheaderLnk>".$pageN."</a>";

        if(!empty($_GET["TID"])){
            $threadN = $_GET["TID"];
	    $reLnk .= "&TID=".$threadN;

	    $navStr .= " > <a href='$reLnk' class=bheaderLnk>".
	            " ThreadNo".$threadN." : ".$threadTitle."</a>";

            //here begins the code for starring a thread or not
            $navStr .= "<a id=threadStarButton 
                        href=\"javascript:setWatchThread('$boardPageName',".
                            "$threadID,$threadContentSize)\">&#9734</a>";
        }
    } 
    echo $navStr." &#x21bb";
?>
