<?php
    include_once("/var/www/html/adminPower/login.php");
    $descript = "FUNCEL.XYZ is an anonymous text-board. ";
    $tabTitle = "[news]";

    $threadTitle = "ERROR: THREAD DOESN'T EXIST";

    if(!empty($_GET["page"]) && $_GET["page"] != "news"){
	$page = $_GET["page"];
        $que = "SELECT * FROM boards WHERE boardName='".$page."'";
	$res = $conn->query($que); 

	if($res->num_rows > 0){
	    while($row = $res->fetch_assoc()){
	        $descript .= $row["descript"];
	    }
	}
	$tabTitle = "[".$page."]";

	if(!empty($_GET["TID"])){
	    $que = "SELECT title FROM ".$page."Threads
	            WHERE threadId=".$_GET["TID"];
	    $res = $connBoards->query($que); 

	    if($res->num_rows > 0){
	        while($row = $res->fetch_assoc()){
                    $threadTitle = $row["title"];
	            $tabTitle .= " ".$row["title"];
		}
	    }	
        } else {
            $tabTitle .= " Threads";
	}
    } else{
        $descript .= "Welcome.";
    }

    echo "<title>$tabTitle : FUNCEL.XYZ</title>";
    echo "<meta name='description' content='$descript'>";
?>

