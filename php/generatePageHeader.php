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
	} else{
		$descript .= "Welcome.";
	}

	echo "<title>$title</title>";
	echo "<meta name='description' content='$descript'>";
?>

