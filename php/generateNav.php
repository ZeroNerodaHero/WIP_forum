<?php
    include_once("../adminPower/login.php");
	$cPage = "news";
	if(!empty($_GET["page"])) $cPage = $_GET["page"];

    $pagetoGo = "?page=";

	if($cPage == "news"){
    	echo "<div class=NewsNavLink id=navActLink>[News]</div>";
	} else{
    	echo "<a href= \"".$pagetoGo. "news\">
          <div class=NewsNavLink>[News]</div> </a>";
	}
    echo "<div class=navCategory>Main Boards:</div>";
    $que = "SELECT boardName FROM boards WHERE typeOfBoard=\"main\"";
    getBoard($que,$cPage);
    /* -----------------------------------------------------------*/
    echo "<div class=navCategory>Shit Boards:</div>";
    $que = "SELECT boardName FROM boards WHERE typeOfBoard=\"shit\"";
    getBoard($que,$cPage);

    function getBoard($boardquery,$cPage){
        global $conn,$pagetoGo;
        $res = $conn->query($boardquery);
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $bname = $row["boardName"];
				if($bname == $cPage){
                	echo "<div class=navLink id=navActLink>[" . $bname.  "]</div>";
					continue;
				}
                echo "<a href= \"".$pagetoGo. $bname."\">
                    <div class=navLink>[" . $bname.  "]</div> </a>";
            }
        }
    }
?>
