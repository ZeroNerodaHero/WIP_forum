<?php
    $page = "news";
    if(!empty($_GET["page"])) $page=$_GET["page"];

    $que = "select * from peepoAds";
    if($page != "news")
        $que .= " where (boardLimited='' || boardLimited='$page')";

    $res = $conn->query($que);
    if(!empty($res) && $res->num_rows > 0){
        $ran = rand() % $res->num_rows;
        //w3schoole only gave me these info kinda scuff
        $que .= " LIMIT 1 OFFSET $ran";
        $res = $conn->query($que);
        while($row = $res->fetch_assoc()){
            echoImg($row["linkToImg"],$row["linkToSite"]);
	    if($row["totalLoads"] > $row["maxPoints"]){
	        deleteAd($row["id"]);
	    } else{
	        $que = "UPDATE peepoAds SET totalLoads=".
	            ($row["totalLoads"]+1)." WHERE id=".$row["id"];
		myQuery($conn,$que);
	    }
	}
    } else{
        echoImg("/var/www/html/res/bulletin/bull_0.png","");
    }
	
    function echoImg($src,$toGo){
        echo "<a href='$toGo'>
            <img src='$src' class='advertImg'> </a>";
    }
    function deleteAd($id){
        global $conn;
        $que = "DELETE FROM peepoAds WHERE id=".$id;
        myQuery($conn,$que);
    }
?>
