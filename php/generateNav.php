<?php
    include_once("../adminPower/login.php");
	$cPage = "news";
	if(!empty($_GET["page"])) $cPage = $_GET["page"];

    $pagetoGo = "?page=";

	if($cPage == "news"){
    	echo "<div class=NewsNavLink id=navActLink>[News]</div>";
	} else{
    	echo " <div class=NewsNavLink>
			<a href= \"".$pagetoGo. "news\" class=navLink>[News]</a></div>";
	}
    echo "<div class=navCategory>Main Boards:</div>";
    getBoard("main",$cPage);
    /* -----------------------------------------------------------*/

    echo "<div class=navCategory>Shit Boards:</div>";
    getBoard("shit",$cPage);
    /* -----------------------------------------------------------*/

    function getBoard($board,$cPage){
    	echo "<div class=navFlexCont>";
    	$boardquery = "SELECT boardName FROM boards WHERE typeOfBoard=\"$board\"";

        global $conn,$pagetoGo;
        $res = $conn->query($boardquery);
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $bname = $row["boardName"];
				if($bname == $cPage){
                	echo "<div class=navLinkCont id=navActLink>[" . $bname.  "]</div>";
					continue;
				}
                echo " <div class=navLinkCont>
					<a href= \"".$pagetoGo. $bname."\" class=navLink> [" . $bname.  "]
					</a></div>";
            }
        }
    	echo "</div>";
    }
?>
