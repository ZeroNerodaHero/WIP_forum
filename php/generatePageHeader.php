<?php
    include_once("../adminPower/login.php");
	$descript = "FUNCEL.XYZ is an anonymous text-board. ";
	$title = "FUNCEL.XYZ";

	if(!empty($_GET["page"]) && $_GET["page"] != "news"){
		$page = $_GET["page"];
		$que = "SELECT * FROM boards WHERE boardName=".$page;
		$res = $conn->query($que); 

		if($res->num_rows > 0){
			while($row = $res->fetch_assoc()){
				$descript .= $row["descript"];
			}
		}
		$title = "[".$page."]";

		if(!empty($_GET["TID"])){
			$que = "SELECT title FROM ".$page."Threads
					WHERE threadId=".$_GET["TID"];
			$res = $connBoards->query($que); 

			if($res->num_rows > 0){
				while($row = $res->fetch_assoc()){
					$title .= " ".$row["title"];
				}
			}	
		}
	} else{
		$descript .= "Welcome.";
	}

	echo "<title>$title : FUNCEL.XYZ</title>";
	echo "<meta name='description' content='$descript'>";
?>

